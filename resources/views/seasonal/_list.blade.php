{{-- Shared category-grouped listing used by both the "now" page and each month page. --}}
@php use App\Data\SeasonalFoods; @endphp

@forelse ($grouped as $cat => $items)
    <h2 id="{{ $cat }}">{{ SeasonalFoods::CATEGORIES[$cat] }} <span style="color:var(--muted);font-weight:400;">({{ count($items) }})</span></h2>
    <div class="grid">
        @foreach ($items as $i)
            <a class="card" href="{{ route('seasonal.food', ['slug' => $i['slug']]) }}">
                <h3>{{ $i['name'] }}</h3>
                <div class="ko">{{ $i['korean'] }}</div>
                <p>{{ \Illuminate\Support\Str::limit($i['description'], 96) }}</p>
            </a>
        @endforeach
    </div>
@empty
    <p class="lede">No items recorded for this month yet.</p>
@endforelse
