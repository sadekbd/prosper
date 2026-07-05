<?php

namespace Tests\Feature;

use App\Models\PageSection;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomeServicesIntroBladeIntegrationTest extends TestCase
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

    public function test_services_intro_renders_seeded_section_content_and_domain_service_cards(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->servicesHtml($this->domainServices());

        $this->assertStringContainsString('What We Do', $html);
        $this->assertStringContainsString('Our Core Services', $html);
        $this->assertStringContainsString('Three pillars of technical excellence powering your entire digital growth engine.', $html);
        $this->assertStringContainsString('View All Services', $html);
        $this->assertStringContainsString('href="http://localhost:8000/services"', $html);
        $this->assertSame(3, substr_count($html, 'Learn More'));

        $this->assertStringContainsString('Domain Ads Service', $html);
        $this->assertStringContainsString('Domain Tracking Service', $html);
        $this->assertStringContainsString('Domain Web Service', $html);
        $this->assertStringContainsString('href="http://localhost:8000/services/domain-ads"', $html);
        $this->assertStringNotContainsString('Page Section Service Title', $html);
    }

    public function test_services_intro_falls_back_when_record_is_missing_or_inactive(): void
    {
        $this->assertFallbackSectionContent($this->servicesHtml($this->domainServices()));

        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'services_intro')
            ->update(['status' => 'inactive']);

        $this->assertFallbackSectionContent($this->servicesHtml($this->domainServices()));
    }

    public function test_services_intro_rejects_unsafe_cta_url_html_label_and_svg_path(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $section = PageSection::where('page_key', 'home')->where('section_key', 'services_intro')->firstOrFail();
        $payload = $section->payload;
        $payload['card_link_label'] = '<strong>Unsafe Learn</strong>';
        $payload['card_icons'][0] = 'M0 0 <script>alert(1)</script> onload javascript style';
        $section->update([
            'button_label' => '<em>Unsafe CTA</em>',
            'button_url' => 'javascript:alert(1)',
            'payload' => $payload,
        ]);

        $html = $this->servicesHtml($this->domainServices());

        $this->assertStringContainsString('href="http://localhost:8000/services"', $html);
        $this->assertStringNotContainsString('javascript:alert(1)', $html);
        $this->assertStringNotContainsString('<strong>Unsafe Learn</strong>', $html);
        $this->assertStringContainsString('Learn More', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('onload', $html);
        $this->assertStringNotContainsString('javascript', $html);
        $this->assertStringNotContainsString('style', $html);
        $this->assertStringContainsString('d="M9 19v-6a2', $html);
    }

    public function test_empty_services_collection_preserves_existing_fallback_service_cards(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->servicesHtml(collect());

        $this->assertStringContainsString('Google Ads Mastery', $html);
        $this->assertStringContainsString('Advanced Conversion Tracking', $html);
        $this->assertStringContainsString('Professional Web Development', $html);
        $this->assertSame(3, substr_count($html, 'Learn More'));
    }

    public function test_page_sections_do_not_control_service_count_or_order(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $section = PageSection::where('page_key', 'home')->where('section_key', 'services_intro')->firstOrFail();
        $payload = $section->payload;
        $payload['services'] = [
            ['title' => 'Page Section Service Title', 'slug' => 'bad'],
        ];
        $payload['card_icons'][] = 'M1 1';
        $section->update(['payload' => $payload]);

        $html = $this->servicesHtml($this->domainServices(4));

        $this->assertStringContainsString('Domain Ads Service', $html);
        $this->assertStringContainsString('Domain Tracking Service', $html);
        $this->assertStringContainsString('Domain Web Service', $html);
        $this->assertStringNotContainsString('Fourth Domain Service', $html);
        $this->assertStringNotContainsString('Page Section Service Title', $html);
        $this->assertSame(3, substr_count($html, 'group relative bg-pm-grey rounded-2xl'));
    }

    private function assertFallbackSectionContent(string $html): void
    {
        $this->assertStringContainsString('What We Do', $html);
        $this->assertStringContainsString('Our Core Services', $html);
        $this->assertStringContainsString('Three pillars of technical excellence powering your entire digital growth engine.', $html);
        $this->assertStringContainsString('View All Services', $html);
        $this->assertStringContainsString('Learn More', $html);
    }

    private function servicesHtml($services): string
    {
        preg_match('/<section class="section-padding bg-white">(.*?)<\/section>/s', $this->renderHome($services), $matches);

        return $matches[1] ?? '';
    }

    private function renderHome($services): string
    {
        return view('public.home', [
            'settings' => $this->settings(),
            'services' => $services,
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

    private function domainServices(int $count = 3)
    {
        return collect([
            (object) ['title' => 'Domain Ads Service', 'subtitle' => 'Domain ads subtitle.', 'slug' => 'domain-ads'],
            (object) ['title' => 'Domain Tracking Service', 'subtitle' => 'Domain tracking subtitle.', 'slug' => 'domain-tracking'],
            (object) ['title' => 'Domain Web Service', 'subtitle' => 'Domain web subtitle.', 'slug' => 'domain-web'],
            (object) ['title' => 'Fourth Domain Service', 'subtitle' => 'Fourth subtitle.', 'slug' => 'domain-fourth'],
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
