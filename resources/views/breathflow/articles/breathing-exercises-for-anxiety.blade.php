@extends('breathflow.layout')

@section('title', 'Breathing Exercises for Anxiety: A Practical Guide')
@section('description', 'How controlled breathing calms anxiety, the four techniques that work best (box, coherent, extended exhale, physiological sigh), and how to build a daily habit. Free, no subscription.')
@section('ogTitle', 'Breathing Exercises for Anxiety That Actually Work')
@section('ogDescription', 'The science of why breathing calms anxiety and the four techniques worth knowing.')
@section('ogType', 'article')
@section('canonical', url('/breathflow/breathing-exercises-for-anxiety'))

@php
    use App\Data\BreathflowTechniques;
    $appUrl = 'https://play.google.com/store/apps/details?id=idiotlabs.breathflow&referrer=utm_source%3Didiotlabs.kr%26utm_medium%3Dreferral%26utm_campaign%3Dbreathing_exercises_anxiety';
    $links = array_values(array_filter(array_map(fn($s) => BreathflowTechniques::bySlug($s),
        ['box-breathing', 'coherent-breathing', 'extended-exhale', 'physiological-sigh'])));
    $faq = [
        ['q' => 'What is the best breathing exercise for anxiety?',
         'a' => 'There is no single best one, but the most reliable share one feature: a slow, long exhale. Box breathing (4-4-4-4) is great for everyday tension, coherent breathing (about 5 breaths per minute) is best for steady daily practice, and the physiological sigh is fastest in an acute spike. Pick the one you will actually do.'],
        ['q' => 'How often should I do breathing exercises for anxiety?',
         'a' => 'A few minutes once or twice a day builds the most benefit over time, because regular practice gradually lowers your baseline stress. You can also use a technique in the moment whenever anxiety rises. Consistency matters more than duration.'],
        ['q' => 'Can breathing exercises replace anxiety medication?',
         'a' => 'No. Breathing exercises are a helpful self-care tool and are well supported by research, but they are not a substitute for medication or therapy. If anxiety is affecting your life, talk to a doctor or mental-health professional and treat breathing as a complement to their advice.'],
        ['q' => 'Do breathing exercises for anxiety actually work?',
         'a' => 'Yes — slow breathing has measurable effects: it stimulates the vagus nerve, lowers heart rate, and shifts the body into the parasympathetic "rest and digest" state. The effect is strongest when the exhale is longer than the inhale and when you practice regularly.'],
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
    <p class="hero-eyebrow">Guide · Anxiety</p>
    <h1>Breathing exercises for anxiety</h1>
    <p class="lead muted">Controlled breathing is one of the few anxiety tools that works in seconds and costs nothing. This guide explains why it works, the four techniques worth knowing, and how to turn them into a daily habit.</p>

    <h2>Why breathing calms anxiety</h2>
    <p class="muted">Anxiety puts your body into fight-or-flight: faster heart, shallow chest breathing, tense muscles. You cannot consciously slow your heart, but you can slow your breath — and the breath is the remote control for the rest. A long, slow exhale stimulates the vagus nerve and switches on the parasympathetic "rest and digest" system, lowering heart rate within a few breaths.</p>

    <h2>Four techniques that work</h2>
    <ul class="use-cases">
        <li><span class="dot"></span><span><strong>Box breathing (4-4-4-4)</strong> — inhale, hold, exhale, hold, each for 4 seconds. Steady and grounding; used by Navy SEALs under pressure.</span></li>
        <li><span class="dot"></span><span><strong>Coherent breathing</strong> — about 5 breaths per minute (in 6, out 6). The best pattern for a daily practice that lowers baseline anxiety.</span></li>
        <li><span class="dot"></span><span><strong>Extended exhale</strong> — keep the exhale clearly longer than the inhale. The simplest way to calm down fast.</span></li>
        <li><span class="dot"></span><span><strong>Physiological sigh</strong> — double inhale, long exhale. Fastest relief in an acute spike.</span></li>
    </ul>

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

    <h2>How to build the habit</h2>
    <p class="muted">The biggest benefit comes from regular practice, not perfect technique. Anchor a few minutes to something you already do — after waking, before bed, or during a work break. A short daily course makes it stick: BreathFlow's free 7-Day Calm course guides one short session a day so the habit forms without willpower.</p>

    <div class="cta-box">
        <h2>Practice them, guided and free</h2>
        <p class="muted">BreathFlow includes all four techniques above plus a one-tap panic SOS and a free 7-Day Calm course — no subscription, no account, fully offline. Haptic feedback guides each breath so you can close your eyes.</p>
        <a href="{{ $appUrl }}" target="_blank" rel="noopener" class="btn btn-primary">Get BreathFlow free on Google Play</a>
    </div>

    <h2>When to get help</h2>
    <p class="muted">Breathing exercises are a complement to professional care, not a replacement. If anxiety is affecting your daily life, talk to a doctor or mental-health professional. If you are in crisis, contact your local emergency services.</p>

    <h2>Frequently asked questions</h2>
    @foreach ($faq as $f)
        <h3 style="margin-bottom: 4px;">{{ $f['q'] }}</h3>
        <p class="muted" style="margin-top: 0;">{{ $f['a'] }}</p>
    @endforeach

    <h2>Related guides</h2>
    <ul class="use-cases">
        <li><span class="dot"></span><a href="{{ route('breathflow.article.panic') }}">How to stop a panic attack</a></li>
        <li><span class="dot"></span><a href="{{ route('breathflow.article.sleep') }}">Can't sleep because of anxiety?</a></li>
        <li><span class="dot"></span><a href="{{ route('breathflow.guide') }}">The best free breathing app</a></li>
    </ul>
</article>
@endsection
