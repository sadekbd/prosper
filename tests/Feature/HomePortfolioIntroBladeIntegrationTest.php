<?php

namespace Tests\Feature;

use App\Models\PageSection;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomePortfolioIntroBladeIntegrationTest extends TestCase
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

    public function test_portfolio_intro_renders_seeded_content_and_live_domain_cards(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->portfolioHtml($this->domainPortfolios(2));

        $this->assertStringContainsString('Our Work', $html);
        $this->assertStringContainsString('Recent Projects', $html);
        $this->assertStringContainsString('Real results from real campaigns. See how we engineer digital success.', $html);
        $this->assertStringContainsString('All Projects', $html);
        $this->assertStringContainsString('href="http://localhost:8000/portfolio"', $html);
        $this->assertSame(2, substr_count($html, 'View Case Study'));

        $this->assertStringContainsString('Live Ads Project', $html);
        $this->assertStringContainsString('Live Tracking Project', $html);
        $this->assertStringContainsString('Google Ads', $html);
        $this->assertStringContainsString('Tracking Setup', $html);
        $this->assertStringContainsString('Live ads short description.', $html);
        $this->assertStringContainsString('Live tracking result', $html);
        $this->assertStringContainsString('Live Tech', $html);
        $this->assertStringContainsString('href="http://localhost:8000/portfolio/live-ads-project"', $html);
        $this->assertStringNotContainsString('E-Commerce Google Ads Overhaul', $html);
    }

    public function test_empty_portfolio_collection_preserves_exact_demo_fallback_cards(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->portfolioHtml(collect());

        $this->assertStringContainsString('E-Commerce Google Ads Overhaul', $html);
        $this->assertStringContainsString('GTM + Meta CAPI Server Tracking', $html);
        $this->assertStringContainsString('Laravel SaaS Landing Page', $html);
        $this->assertStringContainsString('Full-funnel campaign with audience segmentation, dynamic remarketing, and Smart Bidding strategy.', $html);
        $this->assertStringContainsString('85% Data Recovery', $html);
        $this->assertStringContainsString('Laravel', $html);
        $this->assertSame(3, substr_count($html, 'View Case Study'));
        $this->assertSame(3, substr_count($html, 'bg-white/5 border border-white/10 rounded-2xl overflow-hidden'));
    }

    public function test_live_and_fallback_cards_are_not_mixed_and_live_cards_are_limited_to_three(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->portfolioHtml($this->domainPortfolios(4));

        $this->assertStringContainsString('Live Ads Project', $html);
        $this->assertStringContainsString('Live Tracking Project', $html);
        $this->assertStringContainsString('Live Web Project', $html);
        $this->assertStringNotContainsString('Fourth Live Project', $html);
        $this->assertStringNotContainsString('E-Commerce Google Ads Overhaul', $html);
        $this->assertSame(3, substr_count($html, 'bg-white/5 border border-white/10 rounded-2xl overflow-hidden'));
    }

    public function test_page_sections_do_not_control_portfolio_records(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $section = PageSection::where('page_key', 'home')->where('section_key', 'portfolio_intro')->firstOrFail();
        $payload = $section->payload;
        $payload['projects'] = [
            ['title' => 'Page Section Portfolio Project', 'slug' => 'bad'],
        ];
        $section->update(['payload' => $payload]);

        $html = $this->portfolioHtml($this->domainPortfolios(1));

        $this->assertStringContainsString('Live Ads Project', $html);
        $this->assertStringNotContainsString('Page Section Portfolio Project', $html);
        $this->assertStringNotContainsString('E-Commerce Google Ads Overhaul', $html);
    }

    public function test_portfolio_intro_falls_back_when_record_is_missing_inactive_or_malformed(): void
    {
        $this->assertFallbackIntro($this->portfolioHtml($this->domainPortfolios(1)));

        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'portfolio_intro')
            ->update(['status' => 'inactive']);

        $this->assertFallbackIntro($this->portfolioHtml($this->domainPortfolios(1)));

        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'portfolio_intro')
            ->update([
                'eyebrow' => '<strong>Unsafe Work</strong>',
                'title' => '<em>Unsafe Projects</em>',
                'subtitle' => '<script>alert(1)</script>',
                'button_label' => '<span>Unsafe CTA</span>',
                'button_url' => 'javascript:alert(1)',
                'payload' => ['card_link_label' => '<strong>Unsafe Link</strong>'],
            ]);

        $html = $this->portfolioHtml($this->domainPortfolios(1));

        $this->assertFallbackIntro($html);
        $this->assertStringContainsString('View Case Study', $html);
        $this->assertStringContainsString('href="http://localhost:8000/portfolio"', $html);
        $this->assertStringNotContainsString('javascript:alert(1)', $html);
        $this->assertStringNotContainsString('<strong>Unsafe Work</strong>', $html);
        $this->assertStringNotContainsString('<strong>Unsafe Link</strong>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_project_text_is_escaped_and_malformed_technologies_are_rejected(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->portfolioHtml(collect([
            (object) [
                'title' => '<strong>Live HTML Project</strong>',
                'slug' => 'live-html-project',
                'category' => 'google_ads',
                'category_label' => '<em>Unsafe Category</em>',
                'short_description' => '<script>alert(1)</script> Live description.',
                'result_summary' => '<span>Unsafe Result</span>',
                'technologies' => ['Safe Tech', '<script>alert(1)</script>', ['Nested'], 'https://evil.test', 'class=text-red-500'],
            ],
        ]));

        $this->assertStringNotContainsString('<strong>Live HTML Project</strong>', $html);
        $this->assertStringNotContainsString('<em>Unsafe Category</em>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('<span>Unsafe Result</span>', $html);
        $this->assertStringContainsString('&lt;strong&gt;Live HTML Project&lt;/strong&gt;', $html);
        $this->assertStringContainsString('Safe Tech', $html);
        $this->assertStringNotContainsString('https://evil.test', $html);
        $this->assertStringNotContainsString('class=text-red-500', $html);
    }

    private function assertFallbackIntro(string $html): void
    {
        $this->assertStringContainsString('Our Work', $html);
        $this->assertStringContainsString('Recent Projects', $html);
        $this->assertStringContainsString('Real results from real campaigns. See how we engineer digital success.', $html);
        $this->assertStringContainsString('All Projects', $html);
    }

    private function portfolioHtml($portfolios): string
    {
        preg_match('/<section class="section-padding bg-pm-navy relative overflow-hidden">(.*?)<\/section>/s', $this->renderHome($portfolios), $matches);

        return $matches[1] ?? '';
    }

    private function renderHome($portfolios): string
    {
        return view('public.home', [
            'settings' => $this->settings(),
            'services' => collect(),
            'portfolios' => $portfolios,
            'articles' => collect(),
            'homeSections' => PageSection::query()
                ->active()
                ->forPage('home')
                ->ordered()
                ->get()
                ->keyBy('section_key'),
        ])->render();
    }

    private function domainPortfolios(int $count = 3)
    {
        return collect([
            (object) [
                'title' => 'Live Ads Project',
                'slug' => 'live-ads-project',
                'category' => 'google_ads',
                'category_label' => 'Google Ads',
                'short_description' => 'Live ads short description.',
                'result_summary' => 'Live ads result',
                'technologies' => ['Live Tech', 'GTM', 'GA4'],
            ],
            (object) [
                'title' => 'Live Tracking Project',
                'slug' => 'live-tracking-project',
                'category' => 'tracking_setup',
                'category_label' => 'Tracking Setup',
                'short_description' => 'Live tracking short description.',
                'result_summary' => 'Live tracking result',
                'technologies' => ['Meta CAPI', 'Server GTM'],
            ],
            (object) [
                'title' => 'Live Web Project',
                'slug' => 'live-web-project',
                'category' => 'web_development',
                'category_label' => 'Web Development',
                'short_description' => 'Live web short description.',
                'result_summary' => 'Live web result',
                'technologies' => ['Laravel', 'Tailwind'],
            ],
            (object) [
                'title' => 'Fourth Live Project',
                'slug' => 'fourth-live-project',
                'category' => 'automation',
                'category_label' => 'Automation',
                'short_description' => 'Fourth live short description.',
                'result_summary' => 'Fourth live result',
                'technologies' => ['Automation'],
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
