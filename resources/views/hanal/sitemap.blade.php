<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://idiotlabs.kr/hanal</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
@foreach ($days as $d)
  <url>
    <loc>https://idiotlabs.kr/hanal/verse/{{ $d->date->toDateString() }}</loc>
    <lastmod>{{ $d->date->toDateString() }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
@endforeach
</urlset>
