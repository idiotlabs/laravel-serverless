<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/breathflow') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/breathflow/best-free-breathing-app') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/breathflow/how-to-stop-a-panic-attack') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/breathflow/cant-sleep-anxiety') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/breathflow/breathing-exercises-for-anxiety') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    @foreach ($techniques as $t)
    <url>
        <loc>{{ url('/breathflow/' . $t['slug']) }}</loc>
        <changefreq>monthly</changefreq>
        <priority>{{ $t['category'] === 'panic' ? '0.9' : '0.7' }}</priority>
    </url>
    @endforeach
</urlset>
