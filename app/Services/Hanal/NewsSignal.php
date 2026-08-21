<?php

namespace App\Services\Hanal;

/**
 * 뉴스→정서 태그 신호 (PRD §6 킬포인트 — "오늘의 정서를 읽어 말씀을 고른다").
 *
 * 헤드라인 제목만 수집 → LLM이 감정 태그 1~2개로 요약(고정 enum).
 * **뉴스 내용은 묵상에 절대 안 들어간다** — 추상 감정 태그만 파이프라인으로 전달.
 * 실패(네트워크·LLM·검증)하면 null 반환 → 호출측이 날짜 로테이션 폴백.
 */
class NewsSignal
{
    private const TAGS = [
        '불안', '두려움', '지침', '슬픔·상실', '외로움', '낙심', '분주함', '결정·갈림길',
        '갈등·관계', '기다림', '새 시작', '재정·염려', '감사', '기쁨', '소망', '평안',
        '회개·돌이킴', '신뢰·맡김',
    ];
    // 감정층위 이탈 방지 (Guards TOPIC_LEAK과 동일 취지 — 추출 태그는 enum이라 안전하나 이중 방어)
    private const FEEDS = [
        'https://news.google.com/rss?hl=ko&gl=KR&ceid=KR:ko',
    ];

    public function __construct(private Gemini $llm) {}

    /**
     * @param  array<int,string>  $recentTags  최근 7일 사용 태그 — 동률일 때 회피용(소프트).
     * @return array<int,string>|null 감정 태그 1~2개, 실패 시 null
     */
    public function tagsForToday(array $recentTags = []): ?array
    {
        $titles = $this->fetchHeadlines();
        if (count($titles) < 5) {
            return null; // 헤드라인 부족 → 폴백
        }
        try {
            $system = file_get_contents(resource_path('hanal/news_tag_system.txt'));
            $user = "오늘의 헤드라인:\n- " . implode("\n- ", array_slice($titles, 0, 30));
            // ⚠️ 2026-08-20 (S157) — 소프트 다양성 신호. **하드 게이트가 아니다.**
            // 실측 배경: 27일 태그 분포가 불안 15·지침 8 로 붕괴했고, 뉴스로 태그를 뽑은
            // 13일은 **13/13 전부 불안**이었다(로테이션 14일은 2회). 원인은 수요가 아니라
            // 프롬프트였다 — 종전 system 이 "(예: 불안, 지침, 막막함)" 으로 예시를 박아
            // 두었고(그중 '막막함' 은 허용 목록에 없는 값), 한국 뉴스는 대부분의 날
            // 정치 갈등이 지배적이라 그 규칙이 매일 발화하며 예시에 앵커링됐다.
            // ⛔ 하드 금지(7일 내 동일 태그 금지)는 채택하지 않았다 — 반증 스테이지 실측상
            // 14항 로테이션과 충돌해 140일 중 107일(76%)이 규칙 위반이 되고, 강제 차순위가
            // 미검증 태그쌍을 recommender 에 밀어넣어 «반복적인 날» 대신 «콘텐츠 없는 날»
            // 을 만든다. 정확성 우선 + 동률 회피가 여기서 감당할 수 있는 최대치다.
            if ($recentTags) {
                $user .= "\n\n최근 7일 사용 태그(동률이면 회피): " . implode(', ', array_unique($recentTags));
            }
            $schema = ['type' => 'object', 'properties' => ['tags' => ['type' => 'array', 'items' => ['type' => 'string']]], 'required' => ['tags']];
            $out = $this->llm->lightJson($system, $user, $schema); // 경량 모델 재사용
            $tags = array_values(array_filter($out['tags'] ?? [], fn ($t) => in_array($t, self::TAGS, true)));
            $tags = array_slice(array_unique($tags), 0, 2);
            return $tags ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** @return array<int,string> 헤드라인 제목들 */
    private function fetchHeadlines(): array
    {
        $titles = [];
        foreach (self::FEEDS as $url) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 12,
                CURLOPT_USERAGENT => 'hanal/1.0',
            ]);
            $xml = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($xml === false || $code !== 200) {
                continue;
            }
            // <title>...</title> 추출 (첫 title은 피드 제목이라 스킵)
            if (preg_match_all('/<title>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/title>/us', $xml, $m)) {
                foreach (array_slice($m[1], 1) as $t) {
                    $t = trim(html_entity_decode(strip_tags($t), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    if ($t !== '') {
                        $titles[] = $t;
                    }
                }
            }
        }
        return $titles;
    }
}
