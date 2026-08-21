<?php

namespace App\Http\Controllers;

use App\Models\HanalMeditation;

/**
 * 한알(cc-project-24) 웹 표면.
 *
 * ## 왜 이 페이지가 있는가 (2026-08-21, S158 · D038)
 *
 * `/hanal` 은 404 였다. 앱이 말씀카드 공유 기능을 갖고 있는데 **공유된 링크가 착지할
 * 곳이 없었고**, 스토어 밖에서 이 앱을 설명할 표면도 없었다.
 *
 * ⛔ **이 페이지는 SEO 유입 장치가 아니다.** council(D038) 이 실측으로 확인한 바:
 * 「오늘의 말씀」 head 는 bible.com·대한성서공회·성서유니온이 점유하고, 후보로 삼았던
 * 질문형 키워드는 수요가 사실상 0 이었다(pytrends geo=KR 5년 mean 0.18 · 262주 중 10주).
 * 그리고 idiotlabs.kr 은 89일간 impressions 6건·클릭 0 이다. 이 페이지의 값어치는
 * ① 공유 착지점 ② 색인 aging 의 시작점 ③ 스토어 밖 설명면, 셋뿐이다.
 * **여기서 유입이 나올 거라 기대하지 말 것** — 나오면 그게 D038 예측의 반증이다.
 *
 * ## URL 이 개념을 담지 않는 이유
 *
 * 앱 컨셉(«오늘의 정서에 맞춘 선곡»)은 founder 가 명시한 대로 **가설이고 언제든 피봇될
 * 수 있다**. URL 에 개념을 박으면(`/hanal/news-verse/...`) 피봇할 때 색인 자산을 통째로
 * 잃는데, 색인 축적이 애초에 이 페이지를 지금 만드는 이유다. 그래서 경로는 **산출물
 * 종류**로만 나눈다: `/hanal`, `/hanal/verse/{date}`.
 *
 * ## 카피 제약 (D038 신학 좌석 — 비용 0 이라 반증 스테이지에서도 살아남았다)
 *
 * - **발화자는 편집자다.** «건넨다/골랐다» 어조만. «하나님이 오늘 당신에게 주시는» 같은
 *   계시 어조 금지 — 「말씀 뽑기」는 한국 교계에서 이미 성문화된 비판 대상이고
 *   (1번 논거가 «누가 후보를 고르느냐»), 우리 답은 «LLM 이 골랐다» 라 방어가 어렵다.
 * - **「레마」 를 쓰지 않는다.** 로고스/레마 구분은 활성 이단 논쟁 어휘다.
 * - **「뉴스」 를 말하지 않는다.** 메커니즘을 파는 카피는 지고, 그건 founder 결정이다.
 * - **개인화를 약속하지 않는다.** «지금 내 감정에 딱 맞는» 은 person-level 약속인데 실제
 *   메커니즘은 집단 정서라 안 맞는 날 배신감이 된다. 「오늘 같은 날」 = day-level 만 쓴다.
 */
class HanalController extends Controller
{
    /** 설치 링크 — UTM 필수. 없으면 웹→앱 귀속이 또 blind 가 된다(D001 이 5개월째 못 푼 것). */
    public const PLAY_URL = 'https://play.google.com/store/apps/details?id=idiotlabs.hanal';

    /** 아카이브·사이트맵 노출 상한. 코퍼스가 자라도 페이지가 무한히 늘지 않게. */
    private const ARCHIVE_DAYS = 60;

    public function agreement()
    {
        return view('hanal.agreement');
    }

    /** GET /hanal — 허브. 가장 최근 말씀 + 아카이브 + 설치 링크. */
    public function home()
    {
        return view('hanal.home', [
            'm' => HanalMeditation::where('date', '<=', $this->today())
                ->orderByDesc('date')->first(),
            'archive' => $this->archive(),
            'play' => $this->play('home'),
        ]);
    }

    /**
     * GET /hanal/verse/{date} — 그날의 말씀. 공유 착지점.
     *
     * ⛔ 없는 날은 404 다. 빈 페이지를 200 으로 주면 색인에 빈 껍데기가 쌓이고, 무엇보다
     * «생성 실패» 와 «아직 안 온 날» 이 같은 모양이 된다(부재 ≠ 0).
     */
    public function verse(string $date)
    {
        $m = HanalMeditation::where('date', $date)->first();
        abort_if(! $m, 404);

        return view('hanal.verse', [
            'm' => $m,
            'archive' => $this->archive($date),
            'play' => $this->play('verse'),
            'prev' => HanalMeditation::where('date', '<', $date)->orderByDesc('date')->first(),
            'next' => HanalMeditation::where('date', '>', $date)
                ->where('date', '<=', $this->today())->orderBy('date')->first(),
        ]);
    }

    /** GET /hanal/sitemap.xml */
    public function sitemap()
    {
        return response(view('hanal.sitemap', ['days' => $this->archive()])->render(), 200)
            ->header('Content-Type', 'application/xml');
    }

    private function today(): string
    {
        return now('Asia/Seoul')->toDateString();
    }

    /** 최근 N일. **미래 날짜 제외** — 내일치가 미리 생성돼 있어도 노출하지 않는다. */
    private function archive(?string $except = null)
    {
        return HanalMeditation::where('date', '<=', $this->today())
            ->when($except, fn ($q) => $q->where('date', '!=', $except))
            ->orderByDesc('date')
            ->limit(self::ARCHIVE_DAYS)
            ->get(['date', 'title', 'verse_ref']);
    }

    private function play(string $content): string
    {
        return self::PLAY_URL . '&' . http_build_query([
            'utm_source' => 'idiotlabs.kr',
            'utm_medium' => 'web',
            'utm_campaign' => 'hanal_web',
            'utm_content' => $content,
        ]);
    }
}
