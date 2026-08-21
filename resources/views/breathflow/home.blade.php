@extends('breathflow.layout')

@section('title', 'BreathFlow — Panic Attack SOS & 12 Breathing Techniques')
@section('description', 'Free guided breathing exercises for panic attacks, anxiety, sleep, and focus. 12 science-backed techniques plus a one-tap SOS that meets you at your panic speed.')
@section('canonical', url('/breathflow'))

@php
    use App\Data\BreathflowTechniques;
    $labels = [
        'panic' => 'Emergency',
        'stress' => 'Stress relief',
        'sleep' => 'Sleep',
        'focus' => 'Focus',
        'energy' => 'Energy',
    ];
@endphp

@section('content')
<section class="wrap">
    <p class="hero-eyebrow">BreathFlow</p>
    <h1>Breathing exercises designed for the moment you <em style="font-style: normal; color: var(--accent);">can't think</em> — not for when you're already calm.</h1>
    <p class="lead muted">One-tap Panic SOS that starts at your panic speed and slows you down. Plus 12 science-backed techniques for sleep, focus, and energy. Free, no subscription.</p>
    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
        <a href="https://play.google.com/store/apps/details?id=idiotlabs.breathflow"
           target="_blank" rel="noopener" class="btn btn-primary">Get BreathFlow on Google Play</a>
        <a href="{{ route('breathflow.show', 'panic-sos-breathing') }}" class="btn btn-outline">Try Panic SOS first →</a>
    </div>
</section>

<section class="wrap" id="techniques">
    <h2>All 13 breathing techniques</h2>
    @foreach (['panic', 'stress', 'sleep', 'focus', 'energy'] as $cat)
        @if (!empty($byCategory[$cat]))
            <p class="subhead">{{ $labels[$cat] }}</p>
            <div class="grid">
                @foreach ($byCategory[$cat] as $t)
                    <a href="{{ route('breathflow.show', $t['slug']) }}" class="card">
                        <span class="badge">{{ ucfirst($cat) }}</span>
                        <div class="card-name">{{ $t['name'] }}</div>
                        <div class="card-tag">{{ $t['tagline'] }}</div>
                        <div class="pattern">{{ BreathflowTechniques::patternToString($t['pattern']) }}</div>
                    </a>
                @endforeach
            </div>
        @endif
    @endforeach
</section>

<section class="wrap">
    <h2>What makes BreathFlow different</h2>
    <div class="pillars">
        <div class="card">
            <h3>Built for panic, not for calm people</h3>
            <p class="muted" style="margin: 0;">Most breathing apps assume you can already breathe slowly. Panic SOS meets you at your real breathing speed (2s/2s) and decelerates over five minutes.</p>
        </div>
        <div class="card">
            <h3>Haptic-first</h3>
            <p class="muted" style="margin: 0;">Long-press to turn off the screen and let vibration guide you. For dark rooms, sensory overload, or moments you can't look at a screen.</p>
        </div>
        <div class="card">
            <h3>No subscription. Ever.</h3>
            <p class="muted" style="margin: 0;">All 12 techniques and the Panic SOS are free forever. No paywalls on what should be available the second you need it.</p>
        </div>
    </div>
</section>
@endsection
