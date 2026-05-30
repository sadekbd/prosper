<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

  {{-- Static Pages --}}
  @foreach($staticPages as $page)
  <url>
    <loc>{{ $page['url'] }}</loc>
    <lastmod>{{ $page['lastmod'] }}</lastmod>
    <changefreq>{{ $page['changefreq'] }}</changefreq>
    <priority>{{ $page['priority'] }}</priority>
  </url>
  @endforeach

  {{-- Services --}}
  @foreach($services as $service)
  <url>
    <loc>{{ route('services.show', $service->slug) }}</loc>
    <lastmod>{{ $service->updated_at->toDateString() }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  @endforeach

  {{-- Blog Categories --}}
  @foreach($categories as $cat)
  <url>
    <loc>{{ route('blog', ['category' => $cat->slug]) }}</loc>
    <lastmod>{{ $cat->updated_at->toDateString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
  @endforeach

  {{-- Portfolio Projects --}}
  @foreach($portfolio as $project)
  <url>
    <loc>{{ route('portfolio.show', $project->slug) }}</loc>
    <lastmod>{{ $project->updated_at->toDateString() }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
  </url>
  @endforeach

  {{-- Blog Articles --}}
  @foreach($articles as $article)
  <url>
    <loc>{{ route('blog.show', $article->slug) }}</loc>
    <lastmod>{{ $article->updated_at->toDateString() }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
  </url>
  @endforeach

</urlset>