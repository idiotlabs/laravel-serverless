<?php

namespace App\Http\Controllers;

use App\Data\SeasonalFoods;

class SeasonalController extends Controller
{
    /**
     * "What's in season in Korea right now" — the month is resolved in KST, not
     * server time, because the whole value of the page is that the answer is
     * current for Korea.
     */
    public function home()
    {
        $month = SeasonalFoods::currentMonth();

        return view('seasonal.home', [
            'month' => $month,
            'monthName' => SeasonalFoods::MONTHS[$month],
            'grouped' => SeasonalFoods::forMonthByCategory($month),
            'count' => count(SeasonalFoods::forMonth($month)),
        ]);
    }

    /** /seasonal/august — one page per month, the query shape people actually type. */
    public function month(string $month)
    {
        $n = SeasonalFoods::monthFromSlug($month);
        if ($n === null) {
            abort(404);
        }

        $prev = $n === 1 ? 12 : $n - 1;
        $next = $n === 12 ? 1 : $n + 1;

        return view('seasonal.month', [
            'month' => $n,
            'monthName' => SeasonalFoods::MONTHS[$n],
            'grouped' => SeasonalFoods::forMonthByCategory($n),
            'count' => count(SeasonalFoods::forMonth($n)),
            'isCurrent' => $n === SeasonalFoods::currentMonth(),
            'prev' => ['n' => $prev, 'name' => SeasonalFoods::MONTHS[$prev], 'slug' => SeasonalFoods::monthSlug($prev)],
            'next' => ['n' => $next, 'name' => SeasonalFoods::MONTHS[$next], 'slug' => SeasonalFoods::monthSlug($next)],
        ]);
    }

    /** /seasonal/food/peach */
    public function food(string $slug)
    {
        $item = SeasonalFoods::bySlug($slug);
        if ($item === null) {
            abort(404);
        }

        return view('seasonal.food', [
            'item' => $item,
            'related' => SeasonalFoods::related($slug),
            'inSeasonNow' => in_array(SeasonalFoods::currentMonth(), $item['months'], true),
            'currentMonthName' => SeasonalFoods::MONTHS[SeasonalFoods::currentMonth()],
        ]);
    }

    public function sitemap()
    {
        $xml = view('seasonal.sitemap', [
            'foods' => SeasonalFoods::all(),
            'months' => SeasonalFoods::MONTHS,
        ])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
