<?php

use App\Services\YoutubeService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(fn() => (new YoutubeService())->getTrendList())
    ->name('youtube-trend-list')
    ->everyThreeHours()
    ->onOneServer();

// 한알 오늘의 말씀 — 전날 밤에 내일치를 미리 생성(새벽기도 사용자가 항상 받도록),
// 새벽에 오늘치 안전망 재실행(밤 생성 실패 시). 전부 멱등(존재 시 스킵).
//
// ⚠️ 2026-08-20 수리 — 왜 Schedule::command 가 아니라 Schedule::call 인가
//
// 실측: 출시(07-25) 이후 27일 중 **16일이 미생성**이었다(앱은 그 날들에 번들 폴백을
// 보여줬다 = 이 앱의 킬포인트인 "오늘 나에게 온 말씀"이 대부분의 날에 없었다).
// CloudWatch 상으로는 매일 정시에 `Running ['artisan' hanal:generate] 4~6초 DONE`
// 이었다 — 즉 **성공처럼 보였다**. 두 겹의 침묵이 겹친 결과다:
//   ① 커맨드가 생성 실패 시에도 exit 0 을 반환했다(아래 커맨드에서 함께 수리).
//   ② Schedule::command 는 출력을 /dev/null 로 버려 사유가 남지 않았다.
//
// 같은 파일 안에 대조군이 있었다: `youtube-trend-list` 는 Schedule::call(in-process)
// 이고 정상 동작한다. 한알만 Schedule::command(서브프로세스)였고, 서브프로세스는
// 4~6초 만에 빈손으로 끝났다. 반면 **같은 커맨드를 Lambda 에서 직접 호출하면
// 성공한다**(08-18·08-19·08-20 셋 다 수동 호출 1회로 생성됨 = 전환적 실패가 아니라
// 경로 차이). 그래서 동작이 확인된 경로로 옮기고, 출력을 로그에 남긴다.
//
// 남은 불확실성: 서브프로세스가 왜 조용히 죽는지는 **아직 모른다**. 이 수리는 그
// 원인을 규명한 게 아니라 동작하는 경로로 우회하면서 **다음 실패를 볼 수 있게** 만든다.
$hanalGenerate = function (?string $date = null) {
    $args = $date ? ['--date' => $date] : [];
    // ⚠️ 반드시 `stderr` 채널이어야 한다. 기본 채널은 `stack`→`single`(파일)이고
    // Lambda 에서 그 파일은 /tmp/storage/logs 에 쓰여 **CloudWatch 에 안 보인다** —
    // 그러면 이 수리 자체가 이 사고와 똑같은 침묵이 된다(2026-08-20 배포 직전 적발).
    $log = Log::channel('stderr');
    try {
        $code = Artisan::call('hanal:generate', $args);
        $out = trim(Artisan::output());
        // 성공/실패 모두 남긴다 — "조용한 성공"이 이 사고의 원인이었다.
        $log->info('[hanal:generate] exit=' . $code . ' ' . $out);
    } catch (\Throwable $e) {
        $log->error('[hanal:generate] 예외: ' . $e->getMessage());
    }
};

Schedule::call(fn () => $hanalGenerate('tomorrow'))
    ->name('hanal-generate-tomorrow')
    ->timezone('Asia/Seoul')->dailyAt('23:00')->onOneServer();
Schedule::call(fn () => $hanalGenerate())
    ->name('hanal-generate-today')
    ->timezone('Asia/Seoul')->dailyAt('04:30')->onOneServer();
// 안전망 1회 추가 — 위 둘이 모두 실패해도 아침 사용자가 신선 콘텐츠를 받도록.
// 멱등이라 이미 생성된 날엔 즉시 스킵한다(비용 0).
Schedule::call(fn () => $hanalGenerate())
    ->name('hanal-generate-morning-safety')
    ->timezone('Asia/Seoul')->dailyAt('07:30')->onOneServer();
