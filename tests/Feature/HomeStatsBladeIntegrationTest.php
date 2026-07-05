<?php

namespace Tests\Feature;

use App\Models\PageSection;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomeStatsBladeIntegrationTest extends TestCase
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

    public function test_homepage_stats_render_seeded_values_in_order(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $statsHtml = $this->statsHtml();

        $this->assertStatsInOrder($statsHtml);
        $this->assertStringContainsString('150+', $statsHtml);
        $this->assertStringContainsString('98%', $statsHtml);
        $this->assertStringContainsString('5x', $statsHtml);
        $this->assertStringContainsString('3+ yr', $statsHtml);
        $this->assertStringContainsString('Projects Done', $statsHtml);
        $this->assertStringContainsString('Client Satisfaction', $statsHtml);
        $this->assertStringContainsString('Average ROAS', $statsHtml);
        $this->assertStringContainsString('Experience', $statsHtml);
    }

    public function test_homepage_stats_fall_back_when_stats_record_is_missing_or_inactive(): void
    {
        $this->assertStatsInOrder($this->statsHtml());

        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'stats')
            ->update(['status' => 'inactive']);

        $statsHtml = $this->statsHtml();

        $this->assertStatsInOrder($statsHtml);
        $this->assertStringContainsString('3+ yr', $statsHtml);
    }

    public function test_homepage_stats_fall_back_for_malformed_payload_and_do_not_render_html(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'stats')
            ->firstOrFail()
            ->update([
                'payload' => [
                    'items' => [
                        ['value' => '<script>alert(1)</script>', 'suffix' => '+', 'label' => '<strong>Projects Done</strong>'],
                        ['value' => 98, 'suffix' => 'danger', 'label' => 'Client Satisfaction'],
                        ['value' => 5, 'suffix' => 'x', 'label' => 'Average ROAS'],
                    ],
                ],
            ]);

        $statsHtml = $this->statsHtml();

        $this->assertStatsInOrder($statsHtml);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $statsHtml);
        $this->assertStringNotContainsString('<strong>Projects Done</strong>', $statsHtml);
        $this->assertStringContainsString('Projects Done', $statsHtml);
    }

    public function test_homepage_stats_ignore_more_than_four_valid_items(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $stats = PageSection::where('page_key', 'home')->where('section_key', 'stats')->firstOrFail();
        $payload = $stats->payload;
        $payload['items'][] = ['value' => 999, 'suffix' => '+', 'label' => 'Extra Stat'];
        $stats->update(['payload' => $payload]);

        $statsHtml = $this->statsHtml();

        $this->assertSame(4, substr_count($statsHtml, 'data-counter='));
        $this->assertStringNotContainsString('Extra Stat', $statsHtml);
        $this->assertStatsInOrder($statsHtml);
    }

    private function statsHtml(): string
    {
        return $this->renderHome();
    }

    private function assertStatsInOrder(string $statsHtml): void
    {
        $expected = ['Projects Done', 'Client Satisfaction', 'Average ROAS', 'Experience'];
        $lastPosition = -1;

        foreach ($expected as $label) {
            $position = strpos($statsHtml, $label);

            $this->assertNotFalse($position, "Missing stat label: {$label}");
            $this->assertGreaterThan($lastPosition, $position, "Stat label out of order: {$label}");

            $lastPosition = $position;
        }
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
