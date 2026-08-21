<?php

namespace App\Console\Commands;

use App\Models\HanalMeditation;
use App\Services\Hanal\Gemini;
use App\Services\Hanal\NewsSignal;
use App\Services\Hanal\Pipeline;
use App\Services\Hanal\VerseDb;
use Illuminate\Console\Command;

/**
 * 한알 오늘의 말씀 생성 (매일 밤 스케줄러).
 *
 * 정서 태그 → 파이프라인(추천→조회→생성→가드3중→폴백) → hanal_meditations 저장.
 * 이미 있으면 스킵(멱등). --date=YYYY-MM-DD, --force 지원.
 */
class HanalGenerateDaily extends Command
{
    protected $signature = 'hanal:generate {--date=} {--force} {--days=1}';
    protected $description = '한알 오늘의 말씀을 생성해 저장';

    // Stage 1: 뉴스 신호 전(§6 TODO) 임시 — 정서 태그를 날짜로 로테이션해 매일 다르게.
    // 무거움/상황/밝음/신앙을 고루 섞어 배치.
    private const TAG_ROTATION = [
        ['분주함', '신뢰·맡김'], ['감사'], ['불안', '신뢰·맡김'], ['기다림', '소망'],
        ['지침', '평안'], ['기쁨', '감사'], ['외로움'], ['결정·갈림길', '신뢰·맡김'],
        ['슬픔·상실', '평안'], ['소망'], ['회개·돌이킴', '새 시작'], ['평안'],
        ['두려움', '신뢰·맡김'], ['낙심', '소망'],
    ];

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $tz = new \DateTimeZone('Asia/Seoul'); // Lambda는 UTC → 날짜 off-by-one 방지
        $base = $this->option('date')
            ? new \DateTime($this->option('date'), $tz)
            : new \DateTime('today', $tz);

        $db = new VerseDb();
        $gemini = new Gemini();
        if (! $gemini->hasKey()) {
            $this->error('GEMINI_API_KEY 미설정 — 생성 불가');
            return self::FAILURE;
        }
        $pipe = new Pipeline($gemini, $db);
        $archiveSeed = json_decode(file_get_contents(resource_path('hanal/archive_seed.json')), true);

        // 뉴스→정서 태그 (킬포인트). 실패 시 null → 날짜 로테이션 폴백.
        //
        // ⚠️ 2026-08-20 (S154) — **과거 날짜 백필에는 쓰지 않는다.**
        // `NewsSignal::tagsForToday()` 는 이름 그대로 **오늘** 헤드라인을 읽는다.
        // 백필로 07-28 을 생성하면서 08-20 뉴스의 정서를 입히면 그건 그날의 레마가
        // 아니라 오늘의 레마를 과거에 심는 것이고, `--days=N` 루프에서는 N 일이 전부
        // 같은 태그를 받아 아카이브가 한 주제로 도배된다(「지난 말씀 7일」이 앱 내
        // 유일한 재진입 동기라 직접 타격이다).
        //
        // 반대로 **미래 날짜에는 반드시 써야 한다**: 주 경로인 야간 잡이 «전날 밤에
        // 내일치» 를 만들기 때문에, 「오늘만 뉴스」로 좁히면 킬포인트가 죽는다.
        // 그래서 기준은 today 가 아니라 **today 이상**이다.
        $today = (new \DateTime('today', $tz))->format('Y-m-d');
        // `--days=N` 이 과거에서 시작해 오늘을 넘어설 수 있으므로 **범위의 끝**으로 판단한다.
        // (범위에 today 이상이 하나도 없을 때만 조회를 건너뛴다 = LLM 호출 낭비 방지)
        $rangeEnd = (clone $base)->modify('+' . ($days - 1) . ' day')->format('Y-m-d');
        $usesNews = $rangeEnd >= $today;
        // 최근 7일 사용 태그 — NewsSignal 의 동률 회피용(소프트). S157.
        $recentTags = $usesNews
            ? HanalMeditation::where('date', '<', $base->format('Y-m-d'))
                ->orderBy('date', 'desc')->limit(7)->pluck('theme_tags')
                ->flatten()->filter()->unique()->values()->all()
            : [];
        $newsTags = $usesNews ? (new NewsSignal($gemini))->tagsForToday($recentTags) : null;
        if ($newsTags) {
            $this->line('  뉴스 신호: ' . implode(', ', $newsTags));
        } elseif (! $usesNews) {
            $this->line('  과거 백필 → 뉴스 신호 미사용, 날짜 로테이션 태그');
        }

        $failed = [];
        for ($i = 0; $i < $days; $i++) {
            $date = (clone $base)->modify("+$i day")->format('Y-m-d');
            $existing = HanalMeditation::where('date', $date)->first();
            // 멱등성: 이미 "신선 생성"된 날은 스킵. 단 과거 폴백행은 재시도 허용
            // (폴백=번들 시드 = 앱이 어차피 폴백하는 것과 동일 → 신선 생성으로 덮을 가치 있음).
            if ($existing && $existing->source !== 'archive_fallback' && ! $this->option('force')) {
                $this->line("  $date: 이미 생성됨 (스킵)");
                continue;
            }

            // 날짜별 판정: 같은 실행 안에서도 과거 날은 로테이션, today 이상은 뉴스.
            $tags = ($date >= $today && $newsTags)
                ? $newsTags
                : self::TAG_ROTATION[(int) (new \DateTime($date))->format('z') % count(self::TAG_ROTATION)];

            // 최근 7일 이력 / 최근 30일 사용 ref / 폴백 아카이브(과거 저장분 + 시드)
            $recent = HanalMeditation::where('date', '<', $date)->orderBy('date', 'desc')->limit(30)->get();

            // ⚠️ 2026-08-20 (S154) — **중복 회피는 양방향이어야 한다.**
            // 위 조회는 `date < $date` 라 정방향 생성에는 맞지만 백필에는 눈이 반쪽이다:
            // 08-13 을 백필할 때 이미 존재하는 08-16·18·19·20 을 못 본다. 실제로 첫
            // 백필(08-13)이 시편 46:10 「가만히 머무는 힘」 을 뽑았는데 08-20 이 이미
            // 시편 62:1 「잠잠히 머무는 힘」 이었다 — 「지난 말씀 7일」 창에 둘이 나란히
            // 놓인다. 그래서 **ref 중복 회피와 폴백 아카이브에는 이후 날짜도 포함**한다.
            // 정방향 생성(오늘/내일)에는 이후 날짜가 존재하지 않으므로 동작 불변.
            $later = HanalMeditation::where('date', '>', $date)->orderBy('date', 'asc')->limit(30)->get();
            $neighbors = $recent->concat($later);

            // ⚠️ 2026-08-20 (S154) — 이력에 **title 을 포함**한다. 프롬프트에서 이 배열은
            // "최근 7일 이력 (겹치지 않게)" 로 들어가는데(`Gemini::generate`), 정작 title 이
            // 빠져 있어 **LLM 이 본 적 없는 것을 피할 수는 없었다.** 실측: 08-13 백필이
            // 두 번 연속 08-20 의 「잠잠히 머무는 힘」 과 겹치는 제목을 뽑았다
            // (「가만히 머무는 힘」 → ref 중복 회피 후에도 「잠잠함의 힘」).
            //
            // 그리고 이력도 **이웃 기준**으로 고른다: "겹치지 않게" 는 방향이 없는 요구라
            // 백필이 이후 날짜와 겹치는 것도 똑같이 막아야 한다. 정방향 생성에는 이후
            // 날짜가 없으므로 종전과 동일하게 과거 7일이 된다.
            $nearest = $neighbors
                ->sortBy(fn ($r) => abs((strtotime($r->date) - strtotime($date)) / 86400))
                ->take(7)
                ->sortBy('date');
            $recentHistory = $nearest->map(fn ($r) => [
                'verse_ref' => $r->verse_ref, 'title' => $r->title, 'structure' => $r->structure,
                'opener' => $r->opener, 'closer' => $r->closer, 'core_words' => [],
            ])->values()->all();
            // ref 중복 회피와 폴백 후보는 방향이 없다 → 이웃(과거+이후) 전체를 쓴다.
            // ⚠️ 종전 주석 2건을 오늘(S157) 정정했다. ⓐ "스타일 이력은 과거만" 이라 적었지만
            // 실제로는 아래 $nearest(양방향)를 쓴다 — "겹치지 않게" 는 방향 없는 요구라 그게
            // 맞다. ⓑ "완화가 array_slice($recentRefs, 0, 7) 을 쓴다" 도 S156 에서 $protectedRefs
            // (±7일)로 교체돼 더는 사실이 아니다. 이 레포의 사고 이력이 전부 «문서가 말한 것과
            // 도구가 하는 것이 다름» 이라, 틀린 주석은 없는 주석보다 나쁘다.
            // 근접순 정렬은 유지한다 — 판독·디버깅에서 가까운 이웃을 먼저 보는 편이 낫다.
            $recentRefs = $neighbors
                ->sortBy(fn ($r) => abs((strtotime($r->date) - strtotime($date)) / 86400))
                ->pluck('verse_ref')->values()->all();
            $archive = array_merge(
                $neighbors->map(fn ($r) => array_merge($r->toAppJson(), ['tags' => $r->tags]))->all(),
                $archiveSeed,
            );

            // 「지난 말씀 7일」 창 안의 ref = 완화 재시도에서도 재사용 금지 대상.
            // 행 수가 아니라 **날짜**로 잰다 (Pipeline 의 S156 주석 참조).
            $protectedRefs = $neighbors
                ->filter(fn ($r2) => abs((strtotime($r2->date) - strtotime($date)) / 86400) <= 7)
                ->pluck('verse_ref')->unique()->values()->all();

            $r = $pipe->runDay($tags, $recentHistory, $recentRefs, $archive, $protectedRefs);
            $m = $r['meditation'];
            // 신선 생성만 저장. 폴백/실패는 저장 안 함 → 앱이 자체 번들(날짜해시)로 폴백,
            // 다음 스케줄러 실행에서 신선 생성으로 자연 충전 (멱등성 함정 제거).
            if (! $m || $r['source'] === 'archive_fallback') {
                // ⚠️ 2026-08-20 (S154) — 사유를 generic 문구로 덮지 않는다.
                // 종전엔 폴백이면 무조건 'LLM 실패 → 폴백' 이라고만 적었는데, 실제 사유는
                // `Pipeline::fallback($why)` 가 warnings 에 담아 보낸다("후보 0" / "가드 재실패"
                // / "LLM 실패: <메시지>"). 셋은 처방이 전혀 다르다 — 후보 0 은 dedup 이
                // 후보를 굶긴 것이고, 가드 재실패는 생성 품질이며, 예외는 인프라다.
                // 보고 경계에서 이걸 뭉개면 S151 이 고친 침묵이 한 겹 아래에서 되살아난다.
                $why = $r['guard']['warnings'][0] ?? null;
                $reason = $m
                    ? ($why ?? 'LLM 실패 → 폴백(미저장)')
                    : implode(', ', $r['guard']['failures']);
                $this->warn("  $date: 신선 생성 실패, 미저장 — $reason");
                $failed[] = "$date($reason)";
                continue;
            }

            $verseText = $db->lookup($m['verse_ref']) ?? ($m['verse_text'] ?? '');
            HanalMeditation::updateOrCreate(['date' => $date], [
                'verse_ref' => $m['verse_ref'],
                'verse_text' => $verseText,
                'title' => mb_substr($m['title'], 0, 40),
                'hashtag' => mb_substr(ltrim($m['hashtag'], '# '), 0, 40),
                'context_line' => mb_substr($m['context_line'], 0, 120),
                'body' => $m['body'],
                'prayer' => $m['prayer'],
                'tags' => $m['tags'] ?? $tags,
                'source' => $r['source'],
                'structure' => $m['structure'] ?? null,
                'opener' => $m['opener'] ?? null,
                'closer' => $m['closer'] ?? null,
                'theme_tags' => $tags,
            ]);
            $this->info("  $date: {$r['source']} | {$m['verse_ref']} | {$m['title']} [" . implode(',', $tags) . ']');
            // 성공 경로의 warnings 도 출력한다. 여기서 버리면 "완화해서 겨우 만들었다" 가
            // "정상 생성" 과 구별되지 않아, 구절 풀 고갈이 조용히 진행된다.
            foreach ($r['guard']['warnings'] ?? [] as $w) {
                $this->warn("    ⚠️ $w");
            }
        }

        // 🚨 2026-08-20 수리: 실패해도 SUCCESS 를 반환하고 있었다.
        // 스케줄러는 exit code 만 보므로 27일 중 16일이 미생성인데 로그는 전부 "DONE"
        // 이었다 — 감시 불가능한 상태. 한 날이라도 신선 생성에 실패하면 실패로 보고한다
        // (앱은 번들 폴백으로 계속 동작하므로 유저 영향은 없고, 드러나는 것만 달라진다).
        if ($failed) {
            $this->error('신선 생성 실패: ' . implode(', ', $failed));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}