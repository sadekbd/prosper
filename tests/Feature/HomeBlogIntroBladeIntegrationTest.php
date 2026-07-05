<?php

namespace Tests\Feature;

use App\Models\PageSection;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomeBlogIntroBladeIntegrationTest extends TestCase
{
    private Migration $migration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        $this->migration = require database_path('migrations/2026_07_04_000001_create_page_sections_table.php');

        Schema::dropIfExists('page_sections');
        $this->migration->up();

        Cache::put('site_settings_all', $this->settings(), 3600);
    }

    protected function tearDown(): void
    {
        $this->migration->down();

        parent::tearDown();
    }

    public function test_blog_intro_renders_seeded_content_and_live_domain_articles(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->blogHtml($this->domainArticles(2));

        $this->assertStringContainsString('Knowledge Hub', $html);
        $this->assertStringContainsString('Latest Articles', $html);
        $this->assertStringContainsString('Practical insights, tutorials and guides from our technical team.', $html);
        $this->assertStringContainsString('All Articles', $html);
        $this->assertStringContainsString('href="http://localhost:8000/blog"', $html);
        $this->assertSame(2, substr_count($html, 'Read Article'));

        $this->assertStringContainsString('Live Tracking Article', $html);
        $this->assertStringContainsString('Live Ads Article', $html);
        $this->assertStringContainsString('Tracking Guides', $html);
        $this->assertStringContainsString('Google Ads Tips', $html);
        $this->assertStringContainsString('Live tracking excerpt.', $html);
        $this->assertStringContainsString('Jul 1, 2026', $html);
        $this->assertStringContainsString('Jul 2, 2026', $html);
        $this->assertStringContainsString('href="http://localhost:8000/blog/live-tracking-article"', $html);
        $this->assertStringNotContainsString('How to Set Up Server-Side Conversion Tracking in 2025', $html);
        $this->assertStringNotContainsString('12 min read', $html);
    }

    public function test_empty_article_collection_preserves_exact_demo_fallback_articles(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->blogHtml(collect());

        $this->assertStringContainsString('How to Set Up Server-Side Conversion Tracking in 2025', $html);
        $this->assertStringContainsString('Server-Side vs Client-Side Tracking: The Complete Comparison', $html);
        $this->assertStringContainsString('Building High-Converting Laravel Landing Pages That Actually Convert', $html);
        $this->assertStringContainsString('A complete guide covering GTM server containers, transport URL setup, and debugging server-side tags for maximum conversion data accuracy.', $html);
        $this->assertStringContainsString('May 15, 2025', $html);
        $this->assertStringContainsString('Apr 28, 2025', $html);
        $this->assertSame(3, substr_count($html, 'Read Article'));
        $this->assertSame(3, substr_count($html, '<article class="card-hover aos group">'));
    }

    public function test_live_and_fallback_articles_are_not_mixed_and_live_articles_are_limited_to_three(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->blogHtml($this->domainArticles(4));

        $this->assertStringContainsString('Live Tracking Article', $html);
        $this->assertStringContainsString('Live Ads Article', $html);
        $this->assertStringContainsString('Live Web Article', $html);
        $this->assertStringNotContainsString('Fourth Live Article', $html);
        $this->assertStringNotContainsString('How to Set Up Server-Side Conversion Tracking in 2025', $html);
        $this->assertSame(3, substr_count($html, '<article class="card-hover aos group">'));
    }

    public function test_page_sections_do_not_control_blog_article_records(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $section = PageSection::where('page_key', 'home')->where('section_key', 'blog_intro')->firstOrFail();
        $payload = $section->payload;
        $payload['articles'] = [
            ['title' => 'Page Section Blog Article', 'slug' => 'bad'],
        ];
        $section->update(['payload' => $payload]);

        $html = $this->blogHtml($this->domainArticles(1));

        $this->assertStringContainsString('Live Tracking Article', $html);
        $this->assertStringNotContainsString('Page Section Blog Article', $html);
        $this->assertStringNotContainsString('How to Set Up Server-Side Conversion Tracking in 2025', $html);
    }

    public function test_blog_intro_falls_back_when_record_is_missing_inactive_or_malformed(): void
    {
        $this->assertFallbackIntro($this->blogHtml($this->domainArticles(1)));

        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'blog_intro')
            ->update(['status' => 'inactive']);

        $this->assertFallbackIntro($this->blogHtml($this->domainArticles(1)));

        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'blog_intro')
            ->update([
                'eyebrow' => '<strong>Unsafe Hub</strong>',
                'title' => '<em>Unsafe Articles</em>',
                'subtitle' => '<script>alert(1)</script>',
                'button_label' => '<span>Unsafe CTA</span>',
                'button_url' => 'javascript:alert(1)',
                'payload' => ['card_link_label' => '<strong>Unsafe Read</strong>'],
            ]);

        $html = $this->blogHtml($this->domainArticles(1));

        $this->assertFallbackIntro($html);
        $this->assertStringContainsString('Read Article', $html);
        $this->assertStringContainsString('href="http://localhost:8000/blog"', $html);
        $this->assertStringNotContainsString('javascript:alert(1)', $html);
        $this->assertStringNotContainsString('<strong>Unsafe Hub</strong>', $html);
        $this->assertStringNotContainsString('<strong>Unsafe Read</strong>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_article_text_is_escaped_and_unsafe_values_do_not_render_raw_html(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->blogHtml(collect([
            (object) [
                'title' => '<strong>Live HTML Article</strong>',
                'slug' => 'live-html-article',
                'category' => (object) ['name' => '<em>Unsafe Category</em>'],
                'excerpt' => '<script>alert(1)</script> Live excerpt.',
                'published_at' => Carbon::create(2026, 7, 3),
                'read_time' => 6,
            ],
        ]));

        $this->assertStringNotContainsString('<strong>Live HTML Article</strong>', $html);
        $this->assertStringNotContainsString('<em>Unsafe Category</em>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;strong&gt;Live HTML Article&lt;/strong&gt;', $html);
        $this->assertStringContainsString('&lt;em&gt;Unsafe Category&lt;/em&gt;', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt; Live excerpt.', $html);
        $this->assertStringContainsString('Jul 3, 2026', $html);
        $this->assertStringNotContainsString('6 min read', $html);
    }

    private function assertFallbackIntro(string $html): void
    {
        $this->assertStringContainsString('Knowledge Hub', $html);
        $this->assertStringContainsString('Latest Articles', $html);
        $this->assertStringContainsString('Practical insights, tutorials and guides from our technical team.', $html);
        $this->assertStringContainsString('All Articles', $html);
    }

    private function blogHtml($articles): string
    {
        preg_match_all('/<section class="section-padding bg-pm-grey">(.*?)<\/section>/s', $this->renderHome($articles), $matches);

        foreach ($matches[1] ?? [] as $sectionHtml) {
            if (str_contains($sectionHtml, 'Latest Articles') || str_contains($sectionHtml, 'Knowledge Hub')) {
                return $sectionHtml;
            }
        }

        return '';
    }

    private function renderHome($articles): string
    {
        return view('public.home', [
            'settings' => $this->settings(),
            'services' => collect(),
            'portfolios' => collect(),
            'articles' => $articles,
            'homeSections' => PageSection::query()
                ->active()
                ->forPage('home')
                ->ordered()
                ->get()
                ->keyBy('section_key'),
        ])->render();
    }

    private function domainArticles(int $count = 3)
    {
        return collect([
            (object) [
                'title' => 'Live Tracking Article',
                'slug' => 'live-tracking-article',
                'category' => (object) ['name' => 'Tracking Guides'],
                'excerpt' => 'Live tracking excerpt.',
                'published_at' => Carbon::create(2026, 7, 1),
                'read_time' => 12,
            ],
            (object) [
                'title' => 'Live Ads Article',
                'slug' => 'live-ads-article',
                'category' => (object) ['name' => 'Google Ads Tips'],
                'excerpt' => 'Live ads excerpt.',
                'published_at' => Carbon::create(2026, 7, 2),
                'read_time' => 9,
            ],
            (object) [
                'title' => 'Live Web Article',
                'slug' => 'live-web-article',
                'category' => (object) ['name' => 'Web Dev Tutorials'],
                'excerpt' => 'Live web excerpt.',
                'published_at' => Carbon::create(2026, 7, 3),
                'read_time' => 7,
            ],
            (object) [
                'title' => 'Fourth Live Article',
                'slug' => 'fourth-live-article',
                'category' => (object) ['name' => 'Case Studies'],
                'excerpt' => 'Fourth live excerpt.',
                'published_at' => Carbon::create(2026, 7, 4),
                'read_time' => 5,
            ],
        ])->take($count);
    }

    /**
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        return [
            'default_meta_title' => null,
            'default_meta_description' => null,
            'og_image' => null,
            'favicon' => null,
            'site_logo' => null,
            'site_name' => null,
            'google_analytics_id' => null,
            'facebook_url' => null,
            'linkedin_url' => null,
            'github_url' => null,
            'fiverr_url' => null,
            'business_email' => null,
            'whatsapp_number' => null,
            'business_address' => null,
        ];
    }
}
