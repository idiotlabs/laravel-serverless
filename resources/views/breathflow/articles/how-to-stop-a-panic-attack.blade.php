@extends('breathflow.layout')

@section('title', 'How to Stop a Panic Attack: A Step-by-Step Breathing Guide')
@section('description', 'A calm, practical guide to stopping a panic attack with breathing. What is happening in your body, the fastest technique to slow it down, and how to ride it out — free, no app required.')
@section('ogTitle', 'How to Stop a Panic Attack — Step by Step')
@section('ogDescription', 'The fastest breathing technique to calm a panic attack, and how to do it when you cannot think straight.')
@section('ogType', 'article')
@section('canonical', url('/breathflow/how-to-stop-a-panic-attack'))

@php
    use App\Data\BreathflowTechniques;
    $appUrl = 'https://play.google.com/store/apps/details?id=idiotlabs.breathflow&referrer=utm_source%3Didiotlabs.kr%26utm_medium%3Dreferral%26utm_campaign%3Dhow_to_stop_panic_attack';
    $links = array_values(array_filter(array_map(fn($s) => BreathflowTechniques::bySlug($s),
        ['panic-sos-breathing', 'physiological-sigh', 'extended-exhale'])));
    $faq = [
        ['q' => 'What is the fastest way to stop a panic attack?',
         'a' => 'Make your exhale longer than your inhale. A long, slow exhale activates the parasympathetic nervous system and tells your body the danger is over. The single fastest tool is the physiological sigh: two short inhales through the nose, then one long, slow exhale through the mouth. Repeat for one to three minutes.'],
        ['q' => 'How long does a panic attack last?',
         'a' => 'Most panic attacks peak within about 10 minutes and pass within 20 to 30 minutes, even if you do nothing. Slow breathing can shorten the peak and make it far less frightening. Knowing it will end on its own is itself calming.'],
        ['q' => 'Can breathing really stop a panic attack?',
         'a' => 'Breathing will not always stop an attack instantly, but it is the most reliable way to reduce its intensity and shorten it. Panic drives fast, shallow breathing (hyperventilation), which makes the physical symptoms worse. Slowing the breath breaks that loop.'],
        ['q' => 'How do I stop a panic attack at night?',
         'a' => 'The same long-exhale breathing works at night. Keep the lights low, sit up slightly, and use a guided pace so you do not have to count. A haptic-guided app like BreathFlow can vibrate the rhythm so you can keep your eyes closed in the dark.'],
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
    <p class="hero-eyebrow">Guide · Panic &amp; anxiety</p>
    <h1>How to stop a panic attack</h1>
    <p class="lead muted">A panic attack feels like an emergency, but it is not dangerous and it will pass. The fastest thing you can do right now is change how you are breathing. Here is exactly how — no app required.</p>

    <h2>What is happening in your body</h2>
    <p class="muted">A panic attack is your fight-or-flight system firing when there is no real threat. Your heart races, your chest tightens, and you start breathing fast and shallow. That over-breathing (hyperventilation) drops your carbon dioxide and creates the dizziness, tingling, and "I can't breathe" feeling — which makes the panic worse. The way out is to slow the breath, especially the exhale.</p>

    <h2>The fastest technique: the physiological sigh</h2>
    <p class="muted">Researchers at Stanford found this is the quickest way to calm the nervous system in real time.</p>
    <ol class="steps">
        <li><span class="num">1</span><span>Inhale through your nose. Then, without exhaling, take a second short sip of air on top to fully inflate your lungs.</span></li>
        <li><span class="num">2</span><span>Exhale slowly and completely through your mouth, longer than the inhale.</span></li>
        <li><span class="num">3</span><span>Repeat for one to three minutes. The first few breaths do the most work.</span></li>
    </ol>

    <h2>Step by step, when you can't think</h2>
    <ul class="use-cases">
        <li><span class="dot"></span><span><strong>Name it.</strong> "This is a panic attack. It will pass. It is not dangerous."</span></li>
        <li><span class="dot"></span><span><strong>Lengthen the exhale.</strong> Breathe out for longer than you breathe in — even 4 in, 6 out.</span></li>
        <li><span class="dot"></span><span><strong>Drop your shoulders</strong> and unclench your jaw and hands.</span></li>
        <li><span class="dot"></span><span><strong>Ground yourself.</strong> Name five things you can see, four you can hear, three you can touch.</span></li>
        <li><span class="dot"></span><span><strong>Keep going</strong> until the wave passes. It always does.</span></li>
    </ul>

    <h2>Try these guided</h2>
    <p class="muted">If counting is hard mid-panic, let a technique guide the timing for you.</p>
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

    <div class="cta-box">
        <h2>One-tap Panic SOS</h2>
        <p class="muted">BreathFlow's Panic SOS starts at your fast breathing speed (2s/2s) and slows you down over five minutes — no menus, no counting. Haptic mode guides you by vibration so you can close your eyes. Free, offline, no subscription.</p>
        <a href="{{ $appUrl }}" target="_blank" rel="noopener" class="btn btn-primary">Get BreathFlow free on Google Play</a>
    </div>

    <h2>When to get help</h2>
    <p class="muted">If panic attacks are frequent, stopping you from doing everyday things, or you are ever unsure whether it is panic or a medical emergency, talk to a doctor or mental-health professional. If you are in crisis, contact your local emergency services. Breathing is a tool for self-care, not a replacement for treatment.</p>

    <h2>Frequently asked questions</h2>
    @foreach ($faq as $f)
        <h3 style="margin-bottom: 4px;">{{ $f['q'] }}</h3>
        <p class="muted" style="margin-top: 0;">{{ $f['a'] }}</p>
    @endforeach

    <h2>Related guides</h2>
    <ul class="use-cases">
        <li><span class="dot"></span><a href="{{ route('breathflow.article.sleep') }}">Can't sleep because of anxiety?</a></li>
        <li><span class="dot"></span><a href="{{ route('breathflow.article.anxiety') }}">Breathing exercises for anxiety</a></li>
        <li><span class="dot"></span><a href="{{ route('breathflow.guide') }}">The best free breathing app</a></li>
    </ul>
</article>
@endsection
