@extends('breathflow.layout')

@section('title', $t['seo_title'])
@section('description', $t['seo_description'])
@section('ogTitle', $t['seo_title'])
@section('ogDescription', $t['seo_description'])
@section('ogType', 'article')
@section('canonical', url('/breathflow/' . $t['slug']))

@php
    use App\Data\BreathflowTechniques;
    $categoryLabels = [
        'panic' => 'Emergency · Panic SOS',
        'stress' => 'Stress relief',
        'sleep' => 'Sleep',
        'focus' => 'Focus',
        'energy' => 'Energy',
    ];
    $p = $t['pattern'];
    $cycleSeconds = ($p['inhale'] + $p['hold_in'] + $p['exhale'] + $p['hold_out']) * $t['cycles'];
    $totalMinutes = max(1, (int) ceil($cycleSeconds / 60));
    $howTo = [
        '@context' => 'https://schema.org',
        '@type' => 'HowTo',
        'name' => $t['name'],
        'description' => $t['intro'],
        'totalTime' => 'PT' . $totalMinutes . 'M',
        'step' => array_map(fn($i, $s) => [
            '@type' => 'HowToStep',
            'position' => $i + 1,
            'text' => $s,
        ], array_keys($t['steps']), $t['steps']),
    ];
@endphp

@push('head')
    <script type="application/ld+json">{!! json_encode($howTo, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<article class="wrap-narrow" style="padding-top: 48px; padding-bottom: 24px;">
    <a href="{{ route('breathflow.home') }}" class="crumb">← All techniques</a>
    <p class="hero-eyebrow">{{ $categoryLabels[$t['category']] }}</p>
    <h1>{{ $t['name'] }}</h1>
    <p class="tagline">{{ $t['tagline'] }}</p>

    <div class="pattern-box">
        <span class="muted">Pattern</span>
        <span>{{ BreathflowTechniques::patternToString($t['pattern']) }}</span>
        <span class="muted">·</span>
        <span>{{ $t['cycles'] }} cycles</span>
    </div>

    <p class="lead" style="font-size: 18px;">{{ $t['intro'] }}</p>

    <h2>Why it works</h2>
    <p class="muted">{{ $t['science'] }}</p>

    <h2>How to do it</h2>
    <ol class="steps">
        @foreach ($t['steps'] as $i => $step)
            <li>
                <span class="num">{{ $i + 1 }}</span>
                <span>{{ $step }}</span>
            </li>
        @endforeach
    </ol>

    <h2>When to use {{ strtolower($t['name']) }}</h2>
    <ul class="use-cases">
        @foreach ($t['use_cases'] as $u)
            <li><span class="dot"></span><span>{{ $u }}</span></li>
        @endforeach
    </ul>

    <div class="cta-box">
        <h2>Try it guided in BreathFlow</h2>
        <p class="muted">Haptic-guided. No subscription. Available now on Google Play.</p>
        <a href="https://play.google.com/store/apps/details?id=idiotlabs.breathflow"
           target="_blank" rel="noopener" class="btn btn-primary">Get BreathFlow free</a>
    </div>

    @if (!empty($related))
        <h2>Related techniques</h2>
        <div class="grid">
            @foreach ($related as $r)
                <a href="{{ route('breathflow.show', $r['slug']) }}" class="card">
                    <span class="badge">{{ ucfirst($r['category']) }}</span>
                    <div class="card-name">{{ $r['name'] }}</div>
                    <div class="card-tag">{{ $r['tagline'] }}</div>
                    <div class="pattern">{{ BreathflowTechniques::patternToString($r['pattern']) }}</div>
                </a>
            @endforeach
        </div>
    @endif
</article>
@endsection
