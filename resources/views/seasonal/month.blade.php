@extends('seasonal.layout')
@php use App\Data\SeasonalFoods; @endphp

@section('title', "{$monthName} Seasonal Food in Korea — {$count} Fruits, Vegetables & Seafood")
@section('description', "What is in season in Korea in {$monthName}: {$count} fruits, vegetables, seafood and mountain greens at their peak, with what each one is and how it is eaten.")
@section('canonical', url('/seasonal/' . SeasonalFoods::monthSlug($month)))
@section('ogType', 'article')

@section('content')
<section class="hero">
    <div class="wrap">
        <span class="eyebrow">{{ $isCurrent ? 'In season now' : 'Month guide' }}</span>
        <h1>Korean seasonal food in {{ $monthName }}</h1>
        <p class="lede">
            <strong>{{ $count }}</strong> items are at their peak in Korea in {{ $monthName }}.
            @if ($isCurrent)
                That is this month — everything below should be in the markets right now.
            @endif
        </p>
        <ul class="months">
            @foreach (SeasonalFoods::MONTHS as $n => $name)
                <li>
                    <a class="{{ $n === $month ? 'on' : '' }}"
                       href="{{ route('seasonal.month', ['month' => SeasonalFoods::monthSlug($n)]) }}">{{ substr($name, 0, 3) }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</section>

<section class="wrap">
    @include('seasonal._list', ['grouped' => $grouped])

    <h2>At a glance</h2>
    <table class="season">
        <thead><tr><th>Item</th><th>Korean</th><th>Category</th><th>Peak months</th></tr></thead>
        <tbody>
        @foreach ($grouped as $cat => $items)
            @foreach ($items as $i)
                <tr>
                    <td><a href="{{ route('seasonal.food', ['slug' => $i['slug']]) }}">{{ $i['name'] }}</a></td>
                    <td>{{ $i['korean'] }}</td>
                    <td>{{ SeasonalFoods::CATEGORIES[$cat] }}</td>
                    <td>{{ implode(', ', array_map(fn ($m) => substr(SeasonalFoods::MONTHS[$m], 0, 3), $i['months'])) }}</td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>

    <div class="nav-pair">
        <a href="{{ route('seasonal.month', ['month' => $prev['slug']]) }}">← {{ $prev['name'] }}</a>
        <a href="{{ route('seasonal.home') }}">In season now</a>
        <a href="{{ route('seasonal.month', ['month' => $next['slug']]) }}">{{ $next['name'] }} →</a>
    </div>
</section>
@endsection
