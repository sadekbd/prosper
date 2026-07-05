<?php

namespace Tests\Feature;

use App\Models\PageSection;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomeHeroBladeIntegrationTest extends TestCase
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

    public function test_homepage_hero_renders_seeded_section_content(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->renderHome();

        $this->assertStringContainsString('Be Optimistic', $html);
        $this->assertStringContainsString('Engineering Digital', $html);
        $this->assertStringContainsString('Technical Precision.', $html);
        $this->assertStringContainsString('Professional Google Ads management, conversion tracking, and high-performance web development built to turn visitors into loyal customers.', $html);
        $this->assertStringContainsString('View Our Services', $html);
        $this->assertStringContainsString('href="http://localhost:8000/services"', $html);
        $this->assertStringContainsString('Get a Free Audit', $html);
        $this->assertStringContainsString('href="http://localhost:8000/contact"', $html);

        $this->assertStringContainsString('Live Dashboard', $html);
        $this->assertStringContainsString('Q2 Campaign Performance', $html);
        $this->assertStringContainsString('Conversions', $html);
        $this->assertStringContainsString('1,248', $html);
        $this->assertStringContainsString('ROAS', $html);
        $this->assertStringContainsString('4.8x', $html);
        $this->assertStringContainsString('CTR', $html);
        $this->assertStringContainsString('7.2%', $html);
        $this->assertStringContainsString('Weekly Conversions', $html);
        $this->assertStringContainsString('GTM', $html);
        $this->assertStringContainsString('GA4', $html);
        $this->assertStringContainsString('Meta API', $html);
        $this->assertStringContainsString('Server-Side', $html);
        $this->assertStringContainsString('ROI', $html);
        $this->assertStringContainsString('Tracking Active', $html);
    }

    public function test_homepage_hero_falls_back_when_hero_section_is_missing_or_inactive(): void
    {
        $this->assertStringContainsString('View Our Services', $this->renderHome());
        $this->assertStringContainsString('Q2 Campaign Performance', $this->renderHome());

        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'hero')
            ->update(['status' => 'inactive']);

        $html = $this->renderHome();

        $this->assertStringContainsString('Be Optimistic', $html);
        $this->assertStringContainsString('Engineering Digital', $html);
        $this->assertStringContainsString('Success', $html);
        $this->assertStringContainsString('Technical Precision.', $html);
        $this->assertStringContainsString('View Our Services', $html);
        $this->assertStringContainsString('Get a Free Audit', $html);
        $this->assertStringContainsString('Live Dashboard', $html);
        $this->assertStringContainsString('Q2 Campaign Performance', $html);
    }

    public function test_homepage_hero_rejects_unsafe_metric_colors_urls_and_chart_heights(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $hero = PageSection::where('page_key', 'home')->where('section_key', 'hero')->firstOrFail();
        $payload = $hero->payload;
        $payload['dashboard']['metrics'][0]['color'] = 'text-red-evil arbitrary-class';
        $payload['dashboard']['chart']['bar_heights'] = [999, -1, 'bad', 70, 60, 85, 75];
        $payload['secondary_button']['route'] = null;
        $payload['secondary_button']['url'] = 'javascript:alert(1)';

        $hero->update([
            'button_url' => 'javascript:alert(1)',
            'payload' => $payload,
        ]);

        $html = $this->renderHome();

        $this->assertStringNotContainsString('text-red-evil', $html);
        $this->assertStringNotContainsString('arbitrary-class', $html);
        $this->assertStringContainsString('font-heading text-pm-cyan', $html);
        $this->assertStringNotContainsString('javascript:alert(1)', $html);
        $this->assertStringContainsString('href="http://localhost:8000/services"', $html);
        $this->assertStringContainsString('href="http://localhost:8000/contact"', $html);
        $this->assertStringNotContainsString('height:999%', $html);
        $this->assertStringNotContainsString('height:-1%', $html);
        $this->assertStringNotContainsString('height:bad%', $html);
        $this->assertStringContainsString('height:35%', $html);
        $this->assertStringContainsString('height:55%', $html);
        $this->assertStringContainsString('height:42%', $html);
    }

    private function renderHome(): string
    {
        return view('public.home', [
            'settings' => $this->settings(),
            'services' => collect(),
            'portfolios' => collect(),
            'articles' => collect(),
            'homeSections' => PageSection::query()
                ->active()
                ->forPage('home')
                ->ordered()
                ->get()
                ->keyBy('section_key'),
        ])->render();
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
