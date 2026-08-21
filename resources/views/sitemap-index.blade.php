<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
{{-- sitemap index — 자식 목록은 SitemapController 가 라우터에서 자동 수집한다. --}}
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($children as $loc)
  <sitemap>
    <loc>{{ $loc }}</loc>
  </sitemap>
@endforeach
</sitemapindex>
