<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($entries as $entry)
    <url>
        <loc>{{ $entry['loc'] }}</loc>
        <lastmod>{{ optional($entry['lastmod'])->toAtomString() ?? now()->toAtomString() }}</lastmod>
        @foreach ($entry['alternates'] as $locale => $url)
        <xhtml:link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}" />
        @endforeach
        @if ($entry['alternates']->has(config('site.default_locale')))
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $entry['alternates'][config('site.default_locale')] }}" />
        @endif
    </url>
@endforeach
</urlset>
