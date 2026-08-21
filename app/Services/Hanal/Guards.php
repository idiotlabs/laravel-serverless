<?php

namespace App\Services\Hanal;

/**
 * 자동 가드 3중 (Python guards.py 포팅 — 규칙 동일).
 * ① 원문 주입 검증 ② 규칙 린트(정규식) ③ 2차 LLM 신학 검증.
 *
 * 안전 핵심 코드 — GuardsTest(포팅된 케이스)로 Python과 동일 동작을 보장한다.
 */
class Guards
{
    // ── 가드 ② 정규식 (모두 /u) ──
    private const HONORIFIC = '/여러분|성도님|당신/u';
    private const POLITICS = '/대통령|국회|여당|야당|정당|선거|장관|보수|진보|좌파|우파|시위|탄핵/u';
    private const TOPIC_LEAK = '/뉴스|물가|금리|수능|취업난|부동산|사고|참사|재난|경제|전쟁|폭염|한파|사건/u';
    private const GOD_HEDGE = '/(하나님|그분|주님|여호와)[^.。\n]{0,40}(계실 (거예요|거에요|지도)|있을 거예요)/u';
    private const CERTAIN_PROMISE = '/될 것입니다/u';
    private const TIME_BOUND = '/오늘 밤|잠들기 전|하루 끝|하루의 끝|저녁에|아침에 일어나/u';
    private const DIDACTIC = [
        '/[은는이가] 아니라 .+입니다/u',
        "/['‘][^'’]+['’](이|)라는 것은 .+입니다/u",
        '/[은는] .+에서 시작됩니다/u',
    ];
    private const LEN_MIN = 200;
    private const LEN_MAX = 360;
    private const STRUCTURES = ['negation', 'scene', 'figure', 'word'];
    private const CLOSERS = ['action', 'permission', 'lingering', 'still'];
    private const COOLDOWN = ['붙들다', '붙잡다', '자리', '시선', '선물', '무게', '초대'];

    private static function bodyText(array $m): string
    {
        return implode(' ', $m['body'] ?? []);
    }

    /** 가드 ① 원문 주입 검증 */
    public static function injection(array $m, array $candidates, VerseDb $db): array
    {
        $failures = [];
        $warnings = [];
        $ref = $m['verse_ref'] ?? '';
        $candRefs = array_column($candidates, 'ref');
        if (! in_array($ref, $candRefs, true)) {
            return ['passed' => false, 'failures' => ["verse_ref '$ref' 후보 목록에 없음"], 'warnings' => []];
        }
        if ($db->lookup($ref) === null) {
            return ['passed' => false, 'failures' => ["verse_ref '$ref' 전문 테이블 미존재"], 'warnings' => []];
        }
        // ⚠️ 2026-08-20 (S157) — 문자 클래스에 **큰따옴표를 추가**했다.
        // 종전 `['‘]…['’]` 은 27일 라이브에서 인용 12건 중 **6건만** 검사하고 있었고,
        // 못 보던 6건이 하필 **긴 통인용**(마태복음 11:28 전절·이사야 43:1·시편 42:5·
        // 잠언 3:5-6 두 절 걸침)이었다 — 오인용이 나면 가장 치명적인 자리다.
        // 확장 후 12/12 검사, 불일치 **0건**(재측정 2회 일치).
        //
        // ⛔ **failures 로 승격하지 않고 warnings 를 유지한다.** 반증 스테이지 지적:
        // 0/12 는 rule of three 로 95% 상한 **~22%** 이지 0 이 아니고, 새로 검사하게 될
        // 6건은 지금껏 **한 번도 검사된 적이 없는 모집단**이다. 그리고 실패 비용이
        // "재생성 1회" 가 아니다 — 2연속 실패면 fallback→미저장→**204**, 그날이 통째로
        // 사라진다(앱은 조용히 번들 시드를 보여준다). 표본이 쌓인 뒤 승격을 재검토한다.
        if (preg_match_all("/['‘\"“]([^'’\"”]{2,})['’\"”]/u", self::bodyText($m), $mm)) {
            foreach ($mm[1] as $frag) {
                $core = preg_replace('/(으라|라|하라|하시니라)$/u', '', $frag);
                if (! $db->containsFragment($ref, $frag) && ! $db->containsFragment($ref, $core)) {
                    $warnings[] = "인용 '$frag' 원문 부분문자열 아님 (어미 변형 가능 — 검수 대상)";
                }
            }
        }
        return ['passed' => empty($failures), 'failures' => $failures, 'warnings' => $warnings];
    }

    /** 가드 ② 규칙 린트 */
    public static function lint(array $m, array $recentHistory = []): array
    {
        $failures = [];
        $warnings = [];
        $body = self::bodyText($m);
        $full = ($m['title'] ?? '') . ' ' . $body;

        if (preg_match(self::HONORIFIC, $full)) {
            $failures[] = '호칭 사용 (여러분/성도님/당신)';
        }
        if (preg_match(self::POLITICS, $full)) {
            $failures[] = '정치·시사 어휘 감지';
        }
        $ctx = $m['context_line'] ?? '';
        if (preg_match(self::POLITICS, $ctx) || preg_match(self::TOPIC_LEAK, $ctx)) {
            $failures[] = "context_line 뉴스/사건 언급 (감정층위만 허용): '$ctx'";
        }
        $prayer = $m['prayer'] ?? '';
        if (trim($prayer) === '') {
            $failures[] = 'prayer 없음 (닫는 기도 필수)';
        } else {
            if (preg_match(self::POLITICS, $prayer) || preg_match(self::TOPIC_LEAK, $prayer)) {
                $failures[] = 'prayer 뉴스/정치 언급';
            }
            if (! preg_match('/하나님|주님|아버지|여호와/u', $prayer)) {
                $warnings[] = 'prayer에 하나님 호칭 없음';
            }
        }
        if (preg_match(self::GOD_HEDGE, $body)) {
            $failures[] = '하나님 주어에 hedge 어미 (§여지의 경계 위반)';
        }
        if (preg_match(self::CERTAIN_PROMISE, $body)) {
            $failures[] = "단정형 약속 '될 것입니다'";
        }
        if (preg_match(self::TIME_BOUND, $body)) {
            $failures[] = '시간대 전제 마무리 (아침 알림 전제 위반)';
        }
        foreach (self::DIDACTIC as $pat) {
            if (preg_match($pat, $body)) {
                $warnings[] = "해설 자세 문형 의심: $pat";
                break;
            }
        }

        $n = mb_strlen($body);
        if ($n < self::LEN_MIN || $n > self::LEN_MAX) {
            $failures[] = "분량 {$n}자 (허용 " . self::LEN_MIN . '~' . self::LEN_MAX . ')';
        }

        $st = $m['structure'] ?? null;
        $cl = $m['closer'] ?? null;
        if (! in_array($st, self::STRUCTURES, true)) {
            $warnings[] = "structure '$st' 미정의";
        }
        if (! in_array($cl, self::CLOSERS, true)) {
            $warnings[] = "closer '$cl' 미정의";
        }

        $last2 = array_slice($recentHistory, -2);
        if ($st && self::anyEquals($last2, 'structure', $st)) {
            $failures[] = "structure '$st' 최근 2일 내 중복";
        }
        if ($cl && self::anyEquals($last2, 'closer', $cl)) {
            $warnings[] = "closer '$cl' 최근 2일 내 중복";
        }
        $last3 = array_slice($recentHistory, -3);
        $negCount = 0;
        foreach ($last3 as $h) {
            if (($h['structure'] ?? null) === 'negation') {
                $negCount++;
            }
        }
        if ($st === 'negation' && $negCount >= 2) {
            $failures[] = 'negation 구조 최근 3일 편중 (30% 상한 취지)';
        }

        // 어휘 쿨다운 (경고만)
        $recentWords = [];
        foreach (array_slice($recentHistory, -7) as $h) {
            foreach ($h['core_words'] ?? [] as $w) {
                $recentWords[$w] = true;
            }
        }
        $hit = [];
        foreach ($m['core_words'] ?? [] as $w) {
            if (isset($recentWords[$w]) && in_array($w, self::COOLDOWN, true)) {
                $hit[] = $w;
            }
        }
        if ($hit) {
            $warnings[] = '어휘 쿨다운 중복: ' . implode(',', $hit);
        }

        return ['passed' => empty($failures), 'failures' => $failures, 'warnings' => $warnings];
    }

    private static function anyEquals(array $rows, string $key, $val): bool
    {
        foreach ($rows as $h) {
            if (($h[$key] ?? null) === $val) {
                return true;
            }
        }
        return false;
    }

    /** 가드 ③ 2차 LLM 신학 검증 */
    public static function theology(array $m, string $verseContext, ?Gemini $llm): array
    {
        if ($llm === null) {
            return ['passed' => true, 'failures' => [], 'warnings' => ['가드③ skip (LLM 미주입)']];
        }
        $user = "[구절 원문+문맥]\n{$verseContext}\n\n[묵상]\n제목: " . ($m['title'] ?? '') . "\n" . self::bodyText($m);
        $verdict = $llm->judge($llm->guard3System(), $user);
        if (! empty($verdict['pass'])) {
            return ['passed' => true, 'failures' => [], 'warnings' => []];
        }
        $failures = array_map(fn ($r) => "신학검증: $r", $verdict['reasons'] ?? []);
        return ['passed' => false, 'failures' => $failures, 'warnings' => []];
    }

    /** 3중 종합 — 하나라도 fail이면 전체 fail. */
    public static function runAll(array $m, array $candidates, VerseDb $db, array $recentHistory = [], string $verseContext = '', ?Gemini $llm = null): array
    {
        $failures = [];
        $warnings = [];
        foreach ([self::injection($m, $candidates, $db), self::lint($m, $recentHistory)] as $g) {
            $failures = array_merge($failures, $g['failures']);
            $warnings = array_merge($warnings, $g['warnings']);
        }

        // ⚠️ 2026-08-20 (S157) — 가드 ③ 은 **앞 두 가드가 통과했을 때만** 호출한다.
        // 종전에는 배열 리터럴이 셋을 전부 즉시 평가해, injection/lint 가 이미 실패한
        // 폐기 예정 후보에도 LLM 판정 호출이 나갔다. 무료 티어 일일 쿼터를 오늘 실제로
        // 소진(429)했고 생성 1회가 이미 최대 4~5 호출을 쓰므로, 이 단락 하나가 하루
        // 최대 2 호출을 아낀다 — 판정기를 삭제하는 것보다 나은 429 대응이다.
        // (가드 ③ 은 중복이 아니다: 판정 기준 6개 중 정죄·값싼 위로·문맥 모순·교리 논란
        //  4개는 lint 정규식에 대응물이 아예 없다.)
        if (empty($failures)) {
            $g3 = self::theology($m, $verseContext, $llm);
            $failures = array_merge($failures, $g3['failures']);
            $warnings = array_merge($warnings, $g3['warnings']);
        } else {
            $warnings[] = '가드③ skip (앞 가드 실패로 단락 — 쿼터 절약)';
        }

        return ['passed' => empty($failures), 'failures' => $failures, 'warnings' => $warnings];
    }
}