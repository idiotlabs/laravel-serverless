@extends('streakflow.layout')

@section('title', 'StreakFlow — Free Habit Tracker & Streak Counter (No Subscription)')
@section('description', 'StreakFlow is a free habit tracker and streak counter. Build daily streaks, recover a missed day instead of losing everything, and keep your chain going — free, offline, with a home-screen widget.')
@section('canonical', url('/streakflow'))

@php
    $appUrl = 'https://play.google.com/store/apps/details?id=idiotlabs.streakflow&referrer=utm_source%3Didiotlabs.kr%26utm_medium%3Dreferral%26utm_campaign%3Dstreakflow_home';
@endphp

@section('content')
<section>
    <div class="wrap-narrow">
        <p class="hero-eyebrow">Free habit tracker · No subscription</p>
        <h1>Build streaks. Don't lose them.</h1>
        <p class="tagline">StreakFlow is a simple, free streak counter and habit tracker. Mark a habit each day, watch the chain grow — and recover a missed day instead of resetting to zero.</p>
        <a href="{{ $appUrl }}" target="_blank" rel="noopener" class="btn btn-primary">Get StreakFlow free on Google Play</a>
    </div>
</section>

<section style="padding-top: 0;">
    <div class="wrap-narrow">
        <div class="pillars">
            <div class="card">
                <div class="card-name">Streak recovery</div>
                <p class="muted" style="margin: 0;">Miss a day? Recover it instead of losing months of progress. One off day should not undo the habit you are building.</p>
            </div>
            <div class="card">
                <div class="card-name">Home-screen widget</div>
                <p class="muted" style="margin: 0;">See your streak without opening the app, so you never forget to check in before the day ends.</p>
            </div>
            <div class="card">
                <div class="card-name">Free &amp; offline</div>
                <p class="muted" style="margin: 0;">Unlimited habits, no subscription, no account. Your data stays on your device.</p>
            </div>
        </div>

        <div class="cta-box">
            <h2>Want the longer version?</h2>
            <p class="muted">Read our honest guide to free habit trackers — what "free" should mean, the "don't break the chain" method, and how the apps compare.</p>
            <a href="{{ route('streakflow.guide') }}" class="btn btn-outline">The best free habit tracker in 2026</a>
        </div>
    </div>
</section>
@endsection
