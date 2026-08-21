<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

/**
 * 도메인 전체 sitemap index — `GET /sitemap.xml`.
 *
 * ## 왜 (2026-08-21, S158 · founder 지시)
 *
 * 종전에는 앱마다 sitemap 이 따로 있었고(`/seasonal/sitemap.xml` 등) `robots.txt` 에
 * 하나씩 손으로 나열했다. 그래서 **앱을 하나 추가할 때마다 GSC 에 다시 제출**해야 했고,
 * 실제로 누락이 생겼다 — `/streakflow/sitemap.xml` 은 200 으로 살아 있었는데
 * `robots.txt` 에 없었다(오늘 이 작업 중 발견).
 *
 * sitemap index 는 «sitemap 들의 sitemap» 이다. 이걸 한 번만 제출해 두면 크롤러가
 * 자식 sitemap 을 알아서 따라간다 → **앞으로 앱을 추가해도 재제출이 필요 없다.**
 *
 * ## 자식 목록을 손으로 관리하지 않는 이유
 *
 * 목록을 배열로 박아두면 **그 배열이 곧 새로운 누락 지점**이 된다(방금 robots.txt 가
 * 그랬듯). 그래서 라우터에 등록된 **`*.sitemap` 이름의 GET 라우트를 자동 수집**한다.
 * 새 앱이 `->name('foo.sitemap')` 관례만 지키면 이 파일은 영원히 손댈 일이 없다.
 *
 * ⚠️ **관례 의존이라는 한계는 명시해 둔다**: 이름을 다르게 단 sitemap 라우트는 조용히
 * 빠진다. 그래서 아래 `children()` 이 **0개를 반환하면 500 으로 죽는다** — 빈 index 를
 * 200 으로 주면 «자식이 없다» 와 «수집에 실패했다» 가 같은 모양이 되고, 그건 이 레포가
 * 반복해서 데인 «부재 ≠ 0» 이다. 크롤러에게도 조용한 빈 index 가 최악이다.
 *
 * ⛔ `lastmod` 는 넣지 않는다. 자식 sitemap 의 실제 갱신 시각을 여기서 알 방법이 없고,
 * 지어낸 lastmod 는 없는 것보다 나쁘다(크롤러가 그걸 믿는다).
 */
class SitemapController extends Controller
{
    /**
     * 정규 도메인 고정.
     *
     * ⛔ `url()` 을 쓰지 않는다 — 그건 **요청 호스트에서 파생**되는데, 이 앱은 API Gateway
     * 뒤에 있어서 프록시 헤더 처리에 따라 `http://` 나 실행 URL(`*.execute-api.*`)이 나올 수
     * 있다. sitemap index 의 자식 loc 이 다른 호스트면 크롤러가 그걸 그대로 따라가고,
     * 무엇보다 **틀렸다는 사실이 조용하다**(200 으로 잘 나가니까). 다른 앱 sitemap 뷰도
     * 전부 이 도메인을 하드코딩하고 있어 관례도 일치한다.
     */
    private const BASE = 'https://idiotlabs.kr';

    public function index()
    {
        $children = $this->children();

        abort_if($children === [], 500, 'sitemap index: 자식 sitemap 수집 실패');

        $xml = view('sitemap-index', ['children' => $children])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * 라우터에서 `*.sitemap` GET 라우트를 수집해 절대 URL 로 반환한다.
     *
     * @return array<int,string>
     */
    private function children(): array
    {
        $urls = [];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (! $name || ! str_ends_with($name, '.sitemap')) {
                continue;
            }
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }
            $urls[] = self::BASE . '/' . ltrim($route->uri(), '/');
        }

        sort($urls);

        return array_values(array_unique($urls));
    }
}
