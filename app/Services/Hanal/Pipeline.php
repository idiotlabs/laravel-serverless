<?php

namespace App\Services\Hanal;

/**
 * 한알 라이브 생성 파이프라인 (Python pipeline.run_day 포팅).
 *
 * 테마 태그 → Step0 구절 추천 → 전문 조회 주입 → 생성 → 가드 3중
 *   → (실패 시 사유 피드백) 재생성 1회 → 아카이브 폴백.
 */
class Pipeline
{
    public function __construct(
        private Gemini $llm,
        private VerseDb $db,
    ) {}

    /**
     * @param  array  $recentHistory  최근 7일 [{verse_ref,structure,opener,closer,core_words}]
     * @param  array  $recentRefs     이웃 30일 사용 ref (양방향)
     * @param  array  $archive        폴백 풀 (검증된 과거 묵상 + 번들 시드)
     * @param  array  $protectedRefs  완화 재시도에서도 **절대 재사용하면 안 되는** ref =
     *                                앱의 「지난 말씀 7일」 창(대상일 ±7일) 안에 있는 ref.
     *                                비우면 완화가 아무 것도 보호하지 않으므로 호출측이 반드시 채운다.
     * @return array{meditation:?array,guard:array,source:string}
     */
    public function runDay(array $themeTags, array $recentHistory, array $recentRefs, array $archive, array $protectedRefs = []): array
    {
        // LLM/네트워크 하드 실패(429·5xx·타임아웃)는 매일 잡을 죽이지 않고 폴백한다.
        try {
            // ⚠️ 2026-08-20 (S157) — 추천기에 **제외 목록을 넘긴다.**
            // 종전에는 태그만 보내고(=이미 쓴 구절을 모른 채 5개를 뽑고) 사후에 걸렀다.
            // 그래서 코퍼스가 커질수록 5개가 전부 기사용이 되어 «후보 0» 이 났고, 오늘
            // 3회 발화했다. 더 나쁜 건 재시도였다 — **완전히 동일한 프롬프트**를 다시
            // 보내고 있어서 사실상 무의미했다. 이제 1차에 제외 목록 + 12개를 요청하고,
            // 2차는 1차가 실제로 준 refs 까지 추가로 제외해 **다른 질문**을 던진다.
            // 호출 수는 종전과 같다(최대 2회).
            $refs = $this->llm->recommendRefs($themeTags, $recentRefs, 12);
            $candidates = $this->db->resolveCandidates($refs, $recentRefs);
            if (! $candidates) {
                $refs2 = $this->llm->recommendRefs($themeTags, array_merge($recentRefs, $refs), 12);
                $candidates = $this->db->resolveCandidates($refs2, $recentRefs);
                $refs = array_merge($refs, $refs2);
            }
            // ⚠️ 2026-08-20 (S154) — 후보 0 이면 포기하지 않고 **중복 창을 좁혀 한 번 더** 시도한다.
            //
            // 왜: `recentRefs` 전체로 걸러 0 이 되는 상황은 "이 태그의 구절을 최근에 다
            // 썼다" 는 뜻이지 "쓸 구절이 없다" 는 뜻이 아니다. 실측(08-01·08-15, 태그
            // 「불안+신뢰·맡김」)에서 이 경로가 두 날을 통째로 미생성으로 만들었다.
            // 코퍼스가 커질수록 태그별 구절 풀이 먼저 마르므로 **정방향 일일 생성에서도
            // 언젠가 같은 자리에서 멈춘다** — 백필이 그 시점을 앞당겨 드러냈을 뿐이다.
            //
            // 완화는 "중복 회피 포기" 가 아니다: 가장 가까운 7개만 피한다(= 지난 말씀
            // 7일 창과 같은 폭). 그 창 밖의 재사용은 아카이브에서 눈에 띄지 않는다.
            // ⛔ 완화가 조용히 일어나면 안 된다 → warnings 에 남겨 판독에서 보이게 한다.
            // ⚠️ 2026-08-20 (S156) — 완화 창을 «가장 가까운 7 **행**» 에서 «±7 **일**» 로 고쳤다.
            //
            // 종전 구현은 `array_slice($recentRefs, 0, 7)` 였고 나는 그걸 "아카이브 창과 같은
            // 폭" 이라고 정당화했다. **틀렸다** — 코퍼스가 촘촘해지면 7 행은 ±3~4 일이라
            // 7 일 창보다 **좁아진다**. 실측으로 걸렸다: 완화가 걸린 08-01 이 시편 56:3 을
            // 뽑았는데 08-06 이 이미 같은 구절이었다(**5 일 간격 = 아카이브 창 안**).
            // 행 수와 날짜 수는 코퍼스 밀도에 따라 갈라지므로, 창을 지키려면 날짜로 재야 한다.
            $relaxed = false;
            if (! $candidates) {
                $relaxedCands = $this->db->resolveCandidates($refs, $protectedRefs);

                // ⚠️ 부분겹침도 막는다. `resolveCandidates` 는 ref **문자열 완전일치**로
                // 거르므로 「잠언 3:5」와 「잠언 3:5-6」을 서로 다른 구절로 본다. 실측으로
                // 걸렸다: ±7일 수리 직후 08-01 이 「잠언 3:5-6」을 뽑았는데 07-28(4일 차,
                // **창 안**)이 이미 「잠언 3:5」였다. 유저에게 두 카드는 사실상 같은 구절이다.
                // 보호 창 안에서는 **같은 책·장**을 통째로 막는다(창 밖 재사용은 계속 허용).
                $blockedChapters = [];
                foreach ($protectedRefs as $pr) {
                    $blockedChapters[self::chapterKey($pr)] = true;
                }
                $relaxedCands = array_values(array_filter(
                    $relaxedCands,
                    fn ($c) => ! isset($blockedChapters[self::chapterKey($c['ref'] ?? '')]),
                ));

                $candidates = $relaxedCands;
                $relaxed = (bool) $candidates;
            }
            if (! $candidates) {
                return $this->fallback($archive, $recentRefs, $themeTags, '후보 0 (완화 후에도)');
            }

            $feedback = [];
            foreach (['generated', 'regenerated'] as $attempt) {
                $med = $this->llm->generate($candidates, $recentHistory, $themeTags, $feedback);
                $ctx = $this->verseContext($med['verse_ref'] ?? '');
                $guard = Guards::runAll($med, $candidates, $this->db, $recentHistory, $ctx, $this->llm);
                if ($guard['passed']) {
                    if ($relaxed) {
                        $guard['warnings'][] = '중복 창 완화(이웃 30 → ±7일) 후 생성 — 태그별 구절 풀 고갈 신호';
                    }
                    return ['meditation' => $med, 'guard' => $guard, 'source' => $attempt];
                }
                $feedback = $guard['failures'];
            }
            return $this->fallback($archive, $recentRefs, $themeTags, '가드 재실패');
        } catch (\Throwable $e) {
            return $this->fallback($archive, $recentRefs, $themeTags, 'LLM 실패: ' . $e->getMessage());
        }
    }

    /** "잠언 3:5-6" -> "잠언 3" — 절 범위 표기가 달라도 같은 장이면 같은 키. */
    private static function chapterKey(string $ref): string
    {
        $ref = trim($ref);
        $pos = mb_strrpos($ref, ':');

        return $pos === false ? $ref : mb_substr($ref, 0, $pos);
    }

    /** 가드 ③ 에 넘길 문맥. ±2절 포함 — S157 이전에는 해당 절 하나만 넘기고 있었다. */
    private function verseContext(string $ref): string
    {
        return "$ref (> 표시가 인용 구절):\n" . $this->db->contextAround($ref, 2);
    }

    private function fallback(array $archive, array $recentRefs, array $tags, string $why): array
    {
        $recent = array_flip($recentRefs);
        foreach ($archive as $item) {
            if (isset($recent[$item['verse_ref'] ?? ''])) {
                continue;
            }
            $itemTags = $item['tags'] ?? [];
            if (! $tags || array_intersect($itemTags, $tags)) {
                return ['meditation' => $item, 'guard' => ['passed' => true, 'failures' => [], 'warnings' => ["아카이브 폴백 ($why)"]], 'source' => 'archive_fallback'];
            }
        }
        return ['meditation' => null, 'guard' => ['passed' => false, 'failures' => ["폴백 실패 ($why)"], 'warnings' => []], 'source' => 'archive_fallback'];
    }
}