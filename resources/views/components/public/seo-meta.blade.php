{{--
  Usage in any page:
  @section('meta_title', $article->meta_title ?? $article->title . ' — Prosper Media')
  @section('meta_description', $article->meta_description ?? $article->excerpt)

  This component adds structured data (JSON-LD) for better SEO.
--}}

@if (isset($type) && $type === 'article' && isset($article))
    @php
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title ?? '',
            'description' => $article->excerpt ?? '',
            'author' => [
                '@type' => 'Person',
                'name' => $article->author->full_name ?? 'Prosper Media',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Prosper Media',
                'url' => url('/'),
            ],
            'datePublished' => optional($article->published_at)->toISOString(),
            'dateModified' => optional($article->updated_at)->toISOString(),
            'url' => url()->current(),
        ];
    @endphp

    <script type="application/ld+json">
{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endif

@if (!isset($type) || $type === 'organization')
    @php
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => 'Prosper Media',
            'description' => 'Tech-first digital marketing agency specialising in Google Ads, conversion tracking and web development.',
            'url' => url('/'),
            'email' => $settings['business_email'] ?? 'hello@prospermedia.com',
            'telephone' => $settings['business_mobile'] ?? '',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $settings['business_address'] ?? 'Dhaka, Bangladesh',
            ],
            'sameAs' => [
                $settings['facebook_url'] ?? '',
                $settings['linkedin_url'] ?? '',
                $settings['github_url'] ?? '',
            ],
            'serviceType' => [
                'Google Ads Management',
                'Conversion Tracking',
                'Web Development',
            ],
        ];
    @endphp

    <script type="application/ld+json">
{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endif