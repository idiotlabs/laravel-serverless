<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f4ec">
    <title>@yield('title', "What's in Season in Korea Right Now")</title>
    <meta name="description" content="@yield('description', 'A month-by-month guide to Korean seasonal produce — which fruit, vegetables, seafood and mountain greens are at their peak in Korea right now, and how they are eaten.')">
    @hasSection('canonical')
        <link rel="canonical" href="@yield('canonical')">
    @endif
    <meta property="og:site_name" content="Korean Seasonal Food">
    <meta property="og:title" content="@yield('ogTitle', "What's in Season in Korea Right Now")">
    <meta property="og:description" content="@yield('ogDescription', 'Which Korean fruit, vegetables and seafood are at their peak this month.')">
    <meta property="og:type" content="@yield('ogType', 'website')">
    <meta property="og:url" content="@yield('canonical', url('/seasonal'))">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f7f4ec;
            --surface: #ffffff;
            --border: rgba(31, 26, 18, 0.10);
            --text: #1f1a12;
            --muted: #6d6558;
            --accent: #2f7d4f;
            --accent-soft: rgba(47, 125, 79, 0.10);
            --warm: #c2652a;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #14120e;
                --surface: #1e1b16;
                --border: rgba(255, 255, 255, 0.10);
                --text: #f2ede3;
                --muted: #a49b8b;
                --accent: #6cc38c;
                --accent-soft: rgba(108, 195, 140, 0.14);
                --warm: #e08a4d;
            }
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0; padding: 0;
            background: var(--bg); color: var(--text);
            font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
            line-height: 1.6;
        }
        a { color: inherit; text-decoration: none; }
        a:hover { color: var(--accent); }
        .wrap { max-width: 1000px; margin: 0 auto; padding: 0 24px; }
        .wrap-narrow { max-width: 720px; margin: 0 auto; padding: 0 24px; }
        header.site {
            position: sticky; top: 0; z-index: 10;
            background: color-mix(in srgb, var(--bg) 88%, transparent);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }
        .site-bar {
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; padding: 14px 24px; max-width: 1000px; margin: 0 auto;
        }
        .logo { display: inline-flex; align-items: center; gap: 10px; font-weight: 600; font-size: 14px; }
        .logo-mark {
            display: grid; place-items: center; width: 26px; height: 26px;
            border-radius: 8px; background: var(--accent); color: #fff;
            font-size: 14px; font-weight: 700;
        }
        .btn {
            display: inline-block; padding: 9px 16px; border-radius: 999px;
            font-size: 13px; font-weight: 600; border: 1px solid var(--border);
        }
        .btn-primary { background: var(--accent); color: #fff; border-color: transparent; }
        .btn-primary:hover { color: #fff; opacity: 0.9; }
        h1 { font-size: clamp(28px, 5vw, 42px); line-height: 1.2; margin: 0 0 12px; letter-spacing: -0.02em; }
        h2 { font-size: 20px; margin: 40px 0 14px; letter-spacing: -0.01em; }
        h3 { font-size: 16px; margin: 0 0 6px; }
        .lede { color: var(--muted); font-size: 17px; margin: 0 0 8px; }
        .hero { padding: 48px 0 8px; }
        .eyebrow {
            display: inline-block; font-size: 12px; font-weight: 600; letter-spacing: 0.08em;
            text-transform: uppercase; color: var(--accent);
            background: var(--accent-soft); padding: 5px 10px; border-radius: 999px; margin-bottom: 14px;
        }
        .grid { display: grid; gap: 12px; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); }
        .card {
            display: block; background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; padding: 16px;
        }
        .card:hover { border-color: var(--accent); }
        .card .ko { color: var(--muted); font-size: 13px; }
        .card p { margin: 8px 0 0; font-size: 13px; color: var(--muted); }
        .months { display: flex; flex-wrap: wrap; gap: 6px; margin: 18px 0 0; padding: 0; list-style: none; }
        .months a {
            display: inline-block; padding: 7px 12px; border-radius: 999px;
            border: 1px solid var(--border); font-size: 13px; background: var(--surface);
        }
        .months a.on { background: var(--accent); color: #fff; border-color: transparent; }
        .tag {
            display: inline-block; font-size: 12px; color: var(--muted);
            border: 1px solid var(--border); border-radius: 999px; padding: 3px 9px;
        }
        .tag.now { color: #fff; background: var(--warm); border-color: transparent; }
        .nav-pair { display: flex; justify-content: space-between; gap: 12px; margin: 40px 0 0; font-size: 14px; }
        table.season { width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 8px; }
        table.season th, table.season td { text-align: left; padding: 8px 10px; border-bottom: 1px solid var(--border); }
        table.season th { color: var(--muted); font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        footer.site { border-top: 1px solid var(--border); margin-top: 64px; padding: 28px 0 48px; color: var(--muted); font-size: 13px; }
        footer.site .links { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 12px; }
        main { padding-bottom: 8px; }
    </style>
    @stack('head')
</head>
<body>
    <header class="site">
        <div class="site-bar">
            <a href="{{ route('seasonal.home') }}" class="logo">
                <span class="logo-mark">S</span>
                <span>Korean Seasonal Food</span>
            </a>
            <a href="https://play.google.com/store/apps/details?id=idiotlabs.seasonal"
               target="_blank" rel="noopener" class="btn btn-primary">Get the app</a>
        </div>
    </header>
    <main>@yield('content')</main>
    <footer class="site">
        <div class="wrap">
            <div class="links">
                <a href="{{ route('seasonal.home') }}">In season now</a>
                <a href="{{ route('seasonal.month', ['month' => 'january']) }}">By month</a>
                <a href="https://idiotlabs.kr">idiotLabs</a>
            </div>
            <p style="margin: 0;">
                Peak seasons follow the Korean domestic harvest and catch, so they can differ from
                other countries by a month or more. Greenhouse and imported produce is available
                year-round; this guide describes when each item is at its best in Korea.
            </p>
        </div>
    </footer>
</body>
</html>
