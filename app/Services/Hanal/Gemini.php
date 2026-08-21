<?php

namespace App\Services\Hanal;

/**
 * Gemini LLM 클라이언트 (무료 티어). Python llm_gemini.py 포팅.
 *
 * 키는 GEMINI_API_KEY 환경변수 (Lambda env — 코드/앱에 미탑재).
 * 재시도/백오프 포함. REST 직접 호출(curl) — SDK 의존 없음.
 */
class Gemini
{
    private string $key;
    private string $genModel;
    private string $lightModel;
    private string $genSystem;
    private string $step0System;
    private string $guard3System;

    public function __construct()
    {
        // config 경유 — config:cache 시에도 안전 (env() 직접 호출은 캐시되면 null).
        // 로컬 CLI 검증(Laravel 부트 없이)에서는 config() 미정의라 env로 폴백.
        $cfg = function_exists('config') ? config('services.hanal') : null;
        $this->key = (string) ($cfg['gemini_key'] ?? getenv('GEMINI_API_KEY') ?: '');
        $this->genModel = (string) ($cfg['gen_model'] ?? getenv('HANAL_GEMINI_GEN_MODEL') ?: 'gemini-3.5-flash');
        $this->lightModel = (string) ($cfg['light_model'] ?? getenv('HANAL_GEMINI_LIGHT_MODEL') ?: 'gemini-3.1-flash-lite');
        $this->genSystem = file_get_contents(resource_path('hanal/gen_system.txt'));
        $this->step0System = file_get_contents(resource_path('hanal/step0_system.txt'));
        $this->guard3System = file_get_contents(resource_path('hanal/guard3_system.txt'));
    }

    public function hasKey(): bool
    {
        return $this->key !== '';
    }

    public function guard3System(): string
    {
        return $this->guard3System;
    }

    private function cleanSchema(array $schema): array
    {
        unset($schema['additionalProperties']);
        if (isset($schema['properties'])) {
            foreach ($schema['properties'] as $k => $v) {
                if (is_array($v)) {
                    $schema['properties'][$k] = $this->cleanSchema($v);
                }
            }
        }
        if (isset($schema['items']) && is_array($schema['items'])) {
            $schema['items'] = $this->cleanSchema($schema['items']);
        }
        return $schema;
    }

    /** JSON 스키마 강제 호출 + 재시도(503/429/5xx 지수 백오프 최대 4회). */
    private function jsonCall(string $model, string $system, string $user, array $schema): array
    {
        $payload = [
            'systemInstruction' => ['parts' => [['text' => $system]]],
            'contents' => [['role' => 'user', 'parts' => [['text' => $user]]]],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'responseSchema' => $this->cleanSchema($schema),
            ],
        ];
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->key}";
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $lastErr = '';
        for ($attempt = 0; $attempt < 4; $attempt++) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
            ]);
            $resp = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);

            if ($resp !== false && $code === 200) {
                $j = json_decode($resp, true);
                $text = $j['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $out = json_decode($text, true);
                if (is_array($out)) {
                    return $out;
                }
                $lastErr = 'JSON 파싱 실패';
            } else {
                $lastErr = "HTTP $code $err";
            }
            if ($attempt < 3 && ($resp === false || in_array($code, [429, 500, 502, 503, 504], true))) {
                usleep((2 ** $attempt) * 1_000_000); // 1,2,4초
                continue;
            }
            break;
        }
        throw new \RuntimeException("Gemini 호출 실패: $lastErr");
    }

    /**
     * @param  array<int,string>  $exclude  이미 사용한 ref — 프롬프트에 실어 보낸다.
     * @param  int  $n  요청 개수. 5는 dedup 후 여유가 없어 «후보 0» 을 만들었다(S157).
     * @return array<int,string>
     */
    public function recommendRefs(array $tags, array $exclude = [], int $n = 12): array
    {
        $schema = ['type' => 'object', 'properties' => ['refs' => ['type' => 'array', 'items' => ['type' => 'string']]], 'required' => ['refs']];
        $user = "다음 정서에 어울리는 구절 {$n}개: " . implode(', ', $tags);
        if ($exclude) {
            // 장 단위까지 제외 — 「잠언 3:5」와 「잠언 3:5-6」이 사실상 같은 구절이라
            // ref 문자열만 제외하면 같은 장을 되받는다(S156 에서 실측).
            $chapters = array_values(array_unique(array_map(
                fn ($r) => ($p = mb_strrpos(trim($r), ':')) === false ? trim($r) : mb_substr(trim($r), 0, $p),
                $exclude,
            )));
            $user .= "\n\n이미 사용해 제외할 장(같은 장의 다른 절도 제외): " . implode(', ', array_slice($chapters, 0, 60));
            $user .= "\n제외 후 {$n}개를 못 채우면 채울 수 있는 만큼만 반환하세요. 제외 목록을 어기지 마세요.";
        }
        return $this->jsonCall($this->lightModel, $this->step0System, $user, $schema)['refs'] ?? [];
    }

    /** @param array<int,string> $feedback 직전 가드 실패 사유 */
    public function generate(array $candidates, array $recentHistory, array $themeTags, array $feedback = []): array
    {
        $fb = '';
        if ($feedback) {
            $fb = "\n\n[직전 시도가 아래 규칙을 어겼습니다. 이 점만 반드시 고쳐 다시 쓰세요]\n- " . implode("\n- ", $feedback);
        }
        $recent7 = array_slice($recentHistory, -7);
        $user = "오늘의 정서 태그: " . implode(', ', $themeTags) . "\n\n"
            . "구절 후보 (이 중에서만 선택, 원문 그대로):\n" . json_encode($candidates, JSON_UNESCAPED_UNICODE) . "\n\n"
            . "최근 7일 이력 (겹치지 않게):\n" . json_encode($recent7, JSON_UNESCAPED_UNICODE)
            . $fb . "\n\n위 조건으로 오늘의 묵상 1개를 작성하세요.";
        return $this->jsonCall($this->genModel, $this->genSystem, $user, self::meditationSchema());
    }

    /** 경량 모델로 임의 JSON 스키마 호출 (뉴스 태그 추출 등). */
    public function lightJson(string $system, string $user, array $schema): array
    {
        return $this->jsonCall($this->lightModel, $system, $user, $schema);
    }

    /** @return array{pass:bool,reasons:array} */
    public function judge(string $system, string $user): array
    {
        $schema = ['type' => 'object', 'properties' => [
            'pass' => ['type' => 'boolean'],
            'reasons' => ['type' => 'array', 'items' => ['type' => 'string']],
        ], 'required' => ['pass', 'reasons']];
        return $this->jsonCall($this->lightModel, $system, $user, $schema);
    }

    public static function meditationSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'verse_ref' => ['type' => 'string'],
                'title' => ['type' => 'string'],
                'hashtag' => ['type' => 'string'],
                'context_line' => ['type' => 'string'],
                'body' => ['type' => 'array', 'items' => ['type' => 'string'], 'minItems' => 3, 'maxItems' => 4],
                'prayer' => ['type' => 'string'],
                'structure' => ['type' => 'string', 'enum' => ['negation', 'scene', 'figure', 'word']],
                'opener' => ['type' => 'string', 'enum' => ['question', 'empathy', 'scene']],
                'closer' => ['type' => 'string', 'enum' => ['action', 'permission', 'lingering', 'still']],
                'core_words' => ['type' => 'array', 'items' => ['type' => 'string']],
            ],
            'required' => ['verse_ref', 'title', 'hashtag', 'context_line', 'body', 'prayer', 'structure', 'opener', 'closer', 'core_words'],
        ];
    }
}