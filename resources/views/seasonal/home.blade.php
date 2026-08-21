@extends('seasonal.layout')
@php use App\Data\SeasonalFoods; @endphp

{{-- "{$monthName} {$count} Foods" reads as a date ("August 18"). Keep the count after the noun. --}}
@section('title', "What's in Season in Korea Right Now — {$count} Foods at Their Peak in {$monthName}")
@section('description', "It is {$monthName} in Korea. {$count} fruits, vegetables, seafood and mountain greens are at their peak right now — what they are, and how Koreans eat them.")
@section('canonical', url('/seasonal'))
@section('ogTitle', "What's in Season in Korea Right Now ({$monthName})")

@section('content')
<section class="hero">
    <div class="wrap">
        <span class="eyebrow">Updated for {{ $monthName }}</span>
        <h1>What's in season in Korea right now</h1>
        <p class="lede">
            It is <strong>{{ $monthName }}</strong> in Korea, and <strong>{{ $count }}</strong> items are at their peak —
            fruit, vegetables, seafood and the mountain greens that only show up for a few weeks a year.
        </p>
        <p class="lede">
            Korean peak seasons are set by the domestic harvest and catch, so they often land a month
            or more away from the northern-hemisphere norm. Strawberries, for instance, are a
            <em>winter</em> fruit here.
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

    <h2>How to use this</h2>
    <p class="lede">
        Buying in season in Korea is mostly a price and flavour decision: at peak the same item is
        cheaper, sweeter or fattier, and stacked at the front of every market. If you are standing in
        a mart wondering whether something is worth buying today, check the month page for
        {{ $monthName }} above — or open the app, which answers the same question offline.
    </p>

    <div class="nav-pair">
        <a href="{{ route('seasonal.month', ['month' => SeasonalFoods::monthSlug($month)]) }}">
            → Full {{ $monthName }} guide
        </a>
    </div>
</section>
@endsection
