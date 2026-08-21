@extends('breathflow.layout')

@section('title', "Can't Sleep From Anxiety? Breathing Exercises That Help")
@section('description', 'Anxiety keeping you awake? Learn why worry spikes at night and the breathing exercises — including 4-7-8 — that slow a racing mind so you can fall asleep. Free, no subscription.')
@section('ogTitle', "Can't Sleep From Anxiety? Try These Breathing Exercises")
@section('ogDescription', 'Why anxiety gets worse at night, and the breathing techniques that help you fall asleep.')
@section('ogType', 'article')
@section('canonical', url('/breathflow/cant-sleep-anxiety'))

@php
    use App\Data\BreathflowTechniques;
    $appUrl = 'https://play.google.com/store/apps/details?id=idiotlabs.breathflow&referrer=utm_source%3Didiotlabs.kr%26utm_medium%3Dreferral%26utm_campaign%3Dcant_sleep_anxiety';
    $links = array_values(array_filter(array_map(fn($s) => BreathflowTechniques::bySlug($s),
        ['4-7-8-breathing', '2-1-relaxing', 'sleep-wave-breathing'])));
    $faq = [
        ['q' => 'Why does anxiety get worse at night?',
         'a' => 'At night the distractions of the day fall away, so worries get louder. Lying still with a racing mind also keeps your nervous system in a mild fight-or-flight state, which blocks the wind-down your body needs to fall asleep. Slow breathing is one of the few tools that can switch that state off on demand.'],
        ['q' => 'What breathing technique helps with sleep?',
         'a' => 'The 4-7-8 technique is the best known: inhale for 4 seconds, hold for 7, exhale slowly for 8. The long hold and longer exhale slow your heart rate and shift you into "rest and digest" mode. A simple 2-to-1 pattern (exhale twice as long as you inhale) works just as well if 4-7-8 feels like too much.'],
        ['q' => 'How does 4-7-8 breathing help you sleep?',
         'a' => 'The extended exhale and breath hold raise carbon dioxide slightly and stimulate the vagus nerve, which lowers heart rate and blood pressure. That physical calm makes it much easier for a busy mind to let go and drift off.'],
        ['q' => 'Is there a free app for sleep breathing?',
         'a' => 'Yes. Most sleep apps charge a subscription, but BreathFlow includes 4-7-8, sleep wave, and other wind-down techniques for free, fully offline. Haptic mode guides the rhythm by vibration so you can keep your eyes closed and the screen dark.'],
    ];
    $faqLd = ['@context' => 'https://schema.org', '@type' => 'FAQPage',
        'mainEntity' => array_map(fn($f) => ['@type' => 'Question', 'name' => $f['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]], $faq)];
@endphp

@push('head')
    <script type="application/ld+json">{!! json_encode($faqLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<article class="wrap-narrow" style="padding-top: 48px; padding-bottom: 24px;">
    <a href="{{ route('breathflow.home') }}" class="crumb">← BreathFlow</a>
    <p class="hero-eyebrow">Guide · Sleep &amp; anxiety</p>
    <h1>Can't sleep because of anxiety?</h1>
    <p class="lead muted">A racing mind at 2am is one of the most common forms of anxiety. The good news: you can slow it down with your breath. Here is why anxiety spikes at night and the exercises that help you fall asleep.</p>

    <h2>Why anxiety gets worse at night</h2>
    <p class="muted">During the day, work and people keep your mind occupied. At night, those distractions disappear and unresolved worries surface. Lying awake and frustrated keeps your body in a low-grade stress state — the opposite of what sleep needs. Breathing slowly is a direct switch out of that state, no thinking required.</p>

    <h2>4-7-8 breathing for sleep</h2>
    <p class="muted">The most popular wind-down technique, often called a "natural tranquilizer."</p>
    <ol class="steps">
        <li><span class="num">1</span><span>Inhale quietly through your nose for 4 seconds.</span></li>
        <li><span class="num">2</span><span>Hold your breath for 7 seconds.</span></li>
        <li><span class="num">3</span><span>Exhale slowly through your mouth for 8 seconds.</span></li>
        <li><span class="num">4</span><span>Repeat 4 to 8 cycles. If the 7-second hold feels long, shorten everything but keep the exhale longest.</span></li>
    </ol>

    <h2>If 4-7-8 is too much</h2>
    <p class="muted">A gentler option is 2-to-1 breathing: simply make your exhale about twice as long as your inhale (for example, in for 4, out for 8). It is easier to sustain and just as calming. Sleep wave breathing slows the pattern gradually as you go, mimicking how your breath naturally settles as you fall asleep.</p>

    <div class="grid">
        @foreach ($links as $t)
            <a href="{{ route('breathflow.show', $t['slug']) }}" class="card">
                <span class="badge">{{ ucfirst($t['category']) }}</span>
                <div class="card-name">{{ $t['name'] }}</div>
                <div class="card-tag">{{ $t['tagline'] }}</div>
                <div class="pattern">{{ BreathflowTechniques::patternToString($t['pattern']) }}</div>
            </a>
        @endforeach
    </div>

    <h2>Small habits that help</h2>
    <ul class="use-cases">
        <li><span class="dot"></span><span>Put the phone down at least 30 minutes before bed — screen light delays sleep.</span></li>
        <li><span class="dot"></span><span>Keep the room cool and dark.</span></li>
        <li><span class="dot"></span><span>If you are awake more than 20 minutes, do a few rounds of 4-7-8 rather than watching the clock.</span></li>
    </ul>

    <div class="cta-box">
        <h2>Wind down, guided in the dark</h2>
        <p class="muted">BreathFlow includes 4-7-8, sleep wave, and 2:1 relaxing breathing — all free, all offline. Haptic mode vibrates the rhythm so you can keep your eyes closed and the screen off. No subscription, no account.</p>
        <a href="{{ $appUrl }}" target="_blank" rel="noopener" class="btn btn-primary">Get BreathFlow free on Google Play</a>
    </div>

    <h2>When to get help</h2>
    <p class="muted">Occasional anxious nights are normal. If anxiety or insomnia is frequent and affecting your days, talk to a doctor or mental-health professional. Breathing is a self-care tool, not a replacement for treatment.</p>

    <h2>Frequently asked questions</h2>
    @foreach ($faq as $f)
        <h3 style="margin-bottom: 4px;">{{ $f['q'] }}</h3>
        <p class="muted" style="margin-top: 0;">{{ $f['a'] }}</p>
    @endforeach

    <h2>Related guides</h2>
    <ul class="use-cases">
        <li><span class="dot"></span><a href="{{ route('breathflow.article.panic') }}">How to stop a panic attack</a></li>
        <li><span class="dot"></span><a href="{{ route('breathflow.article.anxiety') }}">Breathing exercises for anxiety</a></li>
        <li><span class="dot"></span><a href="{{ route('breathflow.guide') }}">The best free breathing app</a></li>
    </ul>
</article>
@endsection
