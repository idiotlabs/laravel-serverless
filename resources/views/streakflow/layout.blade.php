<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0d0b0a">
    <title>@yield('title', 'StreakFlow — Free Habit Tracker & Streak Counter (No Subscription)')</title>
    <meta name="description" content="@yield('description', 'A free habit tracker and streak counter with no subscription. Build daily streaks, recover a missed day instead of losing everything, and keep your chain going. Free, offline, with a home-screen widget.')">
    @hasSection('canonical')
        <link rel="canonical" href="@yield('canonical')">
    @endif
    <meta property="og:site_name" content="StreakFlow">
    <meta property="og:title" content="@yield('ogTitle', 'StreakFlow — Free Habit Tracker & Streak Counter')">
    <meta property="og:description" content="@yield('ogDescription', 'Build streaks, do not lose them. A free, no-subscription habit tracker with streak recovery and a home-screen widget.')">
    <meta property="og:type" content="@yield('ogType', 'website')">
    <meta property="og:url" content="@yield('canonical', url('/streakflow'))">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0d0b0a;
            --surface: #211a16;
            --border: rgba(255, 255, 255, 0.08);
            --text: #fbf4ee;
            --muted: #b39d8f;
            --accent: #ff7a3c;
            --accent-soft: rgba(255, 122, 60, 0.18);
            --calm: #ffb066;
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
            line-height: 1.6;
        }
        a { color: inherit; text-decoration: none; }
        a:hover { color: var(--accent); }
        .wrap { max-width: 1080px; margin: 0 auto; padding: 0 24px; }
        .wrap-narrow { max-width: 720px; margin: 0 auto; padding: 0 24px; }
        header.site {
            position: sticky; top: 0; z-index: 10;
            background: rgba(13, 11, 10, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }
        .site-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 24px; max-width: 1080px; margin: 0 auto;
        }
        .logo {
            display: inline-flex; align-items: center; gap: 10px;
            font-weight: 600; font-size: 14px; letter-spacing: 0.02em;
        }
        .logo-mark {
            display: grid; place-items: center;
            width: 32px; height: 32px; border-radius: 999px;
            background: var(--accent); color: var(--bg);
            font-weight: 700; font-size: 14px;
        }
        .btn {
            display: inline-block; padding: 12px 22px;
            border-radius: 999px; font-weight: 600; font-size: 14px;
            transition: opacity 0.15s, transform 0.15s, background 0.15s;
        }
        .btn:hover { opacity: 0.9; color: inherit; }
        .btn-primary {
            background: var(--accent); color: var(--bg);
        }
        .btn-outline {
            border: 1px solid rgba(255, 255, 255, 0.18); color: var(--text);
        }
        .btn-outline:hover { background: rgba(255, 255, 255, 0.05); color: var(--text); }
        h1, h2, h3 { margin: 0 0 16px; font-weight: 600; letter-spacing: -0.01em; }
        h1 { font-size: clamp(36px, 5vw, 52px); line-height: 1.1; }
        h2 { font-size: clamp(24px, 3vw, 32px); margin-top: 48px; }
        h3 { font-size: 18px; }
        p { color: var(--text); margin: 0 0 16px; }
        .muted { color: var(--muted); }
        section { padding: 56px 0; }
        .hero-eyebrow {
            font-size: 12px; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.2em; color: var(--accent); margin-bottom: 16px;
        }
        .grid {
            display: grid; gap: 16px;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
        .card {
            display: block; padding: 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            transition: transform 0.15s, border-color 0.15s;
        }
        .card:hover {
            transform: translateY(-2px);
            border-color: var(--accent);
            color: var(--text);
        }
        .badge {
            display: inline-block;
            background: var(--accent-soft);
            color: var(--accent);
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 12px;
        }
        .card-name { font-size: 17px; font-weight: 600; margin-bottom: 4px; }
        .card-tag { font-size: 14px; color: var(--muted); margin-bottom: 12px; }
        .subhead {
            font-size: 12px;
            font-weight: 500;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin: 32px 0 16px;
        }
        .pillars {
            display: grid; gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            margin-top: 24px;
        }
        .pillars .card { transition: none; cursor: default; }
        .pillars .card:hover { transform: none; border-color: var(--border); }
        .crumb {
            display: inline-block;
            margin-bottom: 32px;
            font-size: 14px;
            color: var(--muted);
        }
        .crumb:hover { color: var(--text); }
        ol.steps { list-style: none; margin: 0 0 32px; padding: 0; }
        ol.steps li {
            display: flex; gap: 16px; margin-bottom: 16px; align-items: flex-start;
        }
        ol.steps .num {
            display: grid; place-items: center;
            flex: 0 0 28px; width: 28px; height: 28px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 700; font-size: 13px;
        }
        ul.use-cases { list-style: none; margin: 0 0 32px; padding: 0; color: var(--muted); }
        ul.use-cases li {
            display: flex; gap: 12px; margin-bottom: 8px; align-items: flex-start;
        }
        ul.use-cases .dot {
            flex: 0 0 6px; width: 6px; height: 6px; margin-top: 10px;
            background: var(--accent);
            border-radius: 999px;
        }
        table.compare {
            width: 100%; border-collapse: collapse; margin: 24px 0 32px;
            font-size: 14px;
        }
        table.compare th, table.compare td {
            text-align: left; padding: 12px 14px;
            border-bottom: 1px solid var(--border);
        }
        table.compare th { color: var(--muted); font-weight: 600; }
        table.compare td:first-child { color: var(--text); font-weight: 500; }
        .cta-box {
            text-align: center;
            padding: 40px;
            background: var(--surface);
            border-radius: 24px;
            margin: 48px 0;
        }
        .cta-box h2 { margin-top: 0; }
        footer.site {
            border-top: 1px solid var(--border);
            margin-top: 96px;
            padding: 48px 0 64px;
            color: var(--muted);
            font-size: 14px;
        }
        footer.site .links {
            display: flex; flex-wrap: wrap; gap: 24px; margin-bottom: 16px;
        }
        footer.site a:hover { color: var(--text); }
        .lead { font-size: 18px; color: var(--text); margin-bottom: 32px; }
        .tagline { font-size: 20px; color: var(--muted); margin-bottom: 24px; }
        @media (max-width: 640px) {
            section { padding: 40px 0; }
            h2 { margin-top: 32px; }
            .cta-box { padding: 28px; }
        }
    </style>
    @stack('head')
</head>
<body>
    <header class="site">
        <div class="site-bar">
            <a href="{{ route('streakflow.home') }}" class="logo">
                <span class="logo-mark">S</span>
                <span>StreakFlow</span>
            </a>
            <a href="https://play.google.com/store/apps/details?id=idiotlabs.streakflow"
               target="_blank" rel="noopener" class="btn btn-primary">Get the app</a>
        </div>
    </header>
    <main>@yield('content')</main>
    <footer class="site">
        <div class="wrap">
            <div class="links">
                <a href="{{ route('streakflow.home') }}">Home</a>
                <a href="{{ route('streakflow.guide') }}">Best free habit tracker</a>
                <a href="https://idiotlabs.kr">idiotLabs</a>
            </div>
            <p style="margin: 0;">
                StreakFlow is a habit-tracking tool for personal use. Build streaks for the
                habits you choose; results depend on your own consistency.
            </p>
        </div>
    </footer>
</body>
</html>
