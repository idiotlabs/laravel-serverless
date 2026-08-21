<?php

namespace App\Services\Hanal;

/**
 * 개역한글 전문 조회 테이블 — 한알 라이브 생성의 유일한 성경 본문 원천.
 * (Python verse_db.py 포팅 — 동작 동일성 검증됨)
 *
 * LLM은 장절(ref)만 추천하고, 인용 텍스트는 여기서 조회해 주입한다.
 * 데이터: resources/hanal/krv_han.json (31,068절).
 */
class VerseDb
{
    private array $verses;

    public function __construct(?string $path = null)
    {
        $path ??= resource_path('hanal/krv_han.json');
        $d = json_decode(file_get_contents($path), true);
        $this->verses = $d['verses'];
    }

    public function count(): int
    {
        return count($this->verses);
    }

    /** 장절 ref → 원문. 범위("시편 46:10-11")면 절들을 개행으로 이어 반환. 미존재/생략절 → null. */
    public function lookup(string $ref): ?string
    {
        $ref = trim($ref);
        if (isset($this->verses[$ref])) {
            return $this->verses[$ref];
        }
        if (preg_match('/^(.+?)\s+(\d+):(\d+)-(\d+)$/u', $ref, $m)) {
            [, $book, $chap, $v1, $v2] = $m;
            $parts = [];
            for ($v = (int) $v1; $v <= (int) $v2; $v++) {
                $k = "$book $chap:$v";
                if (! isset($this->verses[$k])) {
                    return null;
                }
                $parts[] = $this->verses[$k];
            }
            return $parts ? implode("\n", $parts) : null;
        }
        return null;
    }

    /**
     * Step0 추천 refs → 원문 확보된 후보만. 미존재 폐기, 최근 사용 제외, 중복 제거(입력순).
     * @return array<int,array{ref:string,text:string}>
     */
    public function resolveCandidates(array $refs, array $recentRefs = []): array
    {
        $recent = array_flip($recentRefs);
        $seen = [];
        $out = [];
        foreach ($refs as $ref) {
            $ref = trim($ref);
            if (isset($seen[$ref]) || isset($recent[$ref])) {
                continue;
            }
            $text = $this->lookup($ref);
            if ($text === null) {
                continue;
            }
            $seen[$ref] = true;
            $out[] = ['ref' => $ref, 'text' => $text];
        }
        return $out;
    }

    /**
     * ref 주변 ±$span 절을 붙인 문맥 문자열. 가드 ③ 전용.
     *
     * ⚠️ 2026-08-20 (S157) 신설. 가드 ③ 프롬프트는 payload 를 `[구절 원문+문맥]` 이라
     * 부르는데 실제로 넘어가던 건 **해당 절 하나뿐**이었다(Pipeline::verseContext).
     * 즉 판정 기준 ④「문맥 모순」을 문맥 없이 판정시키고 있었다. LLM 호출은 그대로이므로
     * 쿼터 증분 0. 없는 절은 조용히 건너뛴다(경계·생략절).
     */
    public function contextAround(string $ref, int $span = 2): string
    {
        $ref = trim($ref);
        if (! preg_match('/^(.+?)\s+(\d+):(\d+)(?:-(\d+))?$/u', $ref, $m)) {
            return $this->lookup($ref) ?? '';
        }
        [, $book, $chap, $v1] = $m;
        $v2 = $m[4] ?? $v1;
        $from = max(1, (int) $v1 - $span);
        $to = (int) $v2 + $span;
        $parts = [];
        for ($v = $from; $v <= $to; $v++) {
            $t = $this->verses["$book $chap:$v"] ?? null;
            if ($t === null) {
                continue;
            }
            $mark = ($v >= (int) $v1 && $v <= (int) $v2) ? '>' : ' ';
            $parts[] = "$mark $book $chap:$v $t";
        }
        return $parts ? implode("\n", $parts) : ($this->lookup($ref) ?? '');
    }

    /** 묵상 본문의 따옴표 인용이 DB 원문의 부분 문자열인지 (가드 ①). */
    public function containsFragment(string $ref, string $fragment): bool
    {
        $original = $this->lookup($ref);
        if ($original === null) {
            return false;
        }
        return str_contains(self::norm($original), self::norm($fragment));
    }

    public static function norm(string $s): string
    {
        return trim(preg_replace('/\s+/u', ' ', $s));
    }
}