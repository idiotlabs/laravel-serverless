@extends('seasonal.layout')
@php
    use App\Data\SeasonalFoods;
    $monthNames = array_map(fn ($m) => SeasonalFoods::MONTHS[$m], $item['months']);
    $monthList = implode(', ', $monthNames);
@endphp

@section('title', "Is {$item['name']} in Season in Korea? — Peak: {$monthList}")
@section('description', "{$item['name']} ({$item['korean']}) is at its peak in Korea in {$monthList}. What it is, when to buy it, and how Koreans eat it.")
@section('canonical', url('/seasonal/food/' . $item['slug']))
@section('ogType', 'article')

@section('content')
<section class="hero">
    <div class="wrap-narrow">
        <span class="eyebrow">{{ SeasonalFoods::CATEGORIES[$item['category']] }}</span>
        <h1>{{ $item['name'] }} <span style="color:var(--muted);font-weight:400;font-size:0.5em;">{{ $item['korean'] }}</span></h1>
        <p style="margin:0 0 14px;">
            @if ($inSeasonNow)
                <span class="tag now">In season now &middot; {{ $currentMonthName }}</span>
            @else
                <span class="tag">Out of peak season in {{ $currentMonthName }}</span>
            @endif
        </p>
        <p class="lede">{{ $item['description'] }}</p>
    </div>
</section>

<section class="wrap-narrow">
    <h2>When it is in season</h2>
    <p class="lede">
        {{ $item['name'] }} peaks in Korea in <strong>{{ $monthList }}</strong>.
    </p>
    <ul class="months">
        @foreach (SeasonalFoods::MONTHS as $n => $name)
            <li>
                <a class="{{ in_array($n, $item['months'], true) ? 'on' : '' }}"
                   href="{{ route('seasonal.month', ['month' => SeasonalFoods::monthSlug($n)]) }}">{{ substr($name, 0, 3) }}</a>
            </li>
        @endforeach
    </ul>

    @if ($related !== [])
        <h2>In season at the same time</h2>
        <div class="grid">
            @foreach ($related as $r)
                <a class="card" href="{{ route('seasonal.food', ['slug' => $r['slug']]) }}">
                    <h3>{{ $r['name'] }}</h3>
                    <div class="ko">{{ $r['korean'] }}</div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="nav-pair">
        <a href="{{ route('seasonal.home') }}">← What's in season now</a>
        <a href="{{ route('seasonal.month', ['month' => SeasonalFoods::monthSlug($item['months'][0])]) }}">
            {{ SeasonalFoods::MONTHS[$item['months'][0]] }} guide →
        </a>
    </div>
</section>
@endsection
