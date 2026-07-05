<?php

namespace Tests\Feature;

use App\Models\PageSection;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomeTrustBarBladeIntegrationTest extends TestCase
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

    public function test_homepage_trust_bar_renders_seeded_title_items_and_colors(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $trustBarHtml = $this->trustBarHtml();

        $this->assertStringContainsString('Technologies &amp; Platforms We Master', $trustBarHtml);
        $this->assertTrustLabelsInOrder($trustBarHtml);

        foreach ($this->expectedColors() as $color) {
            $this->assertStringContainsString("background-color: {$color}", $trustBarHtml);
        }

        $this->assertSame(7, substr_count($trustBarHtml, 'group cursor-default'));
    }

    public function test_homepage_trust_bar_falls_back_when_record_is_missing_or_inactive(): void
    {
        $this->assertTrustLabelsInOrder($this->trustBarHtml());

        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'trust_bar')
            ->update(['status' => 'inactive']);

        $trustBarHtml = $this->trustBarHtml();

        $this->assertStringContainsString('Technologies &amp; Platforms We Master', $trustBarHtml);
        $this->assertTrustLabelsInOrder($trustBarHtml);
        $this->assertSame(7, substr_count($trustBarHtml, 'group cursor-default'));
    }

    public function test_homepage_trust_bar_falls_back_for_malformed_payload_and_does_not_render_label_html(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'trust_bar')
            ->firstOrFail()
            ->update([
                'payload' => [
                    'items' => [
                        ['label' => '<strong>Google Ads</strong>', 'color' => '#4285F4'],
                        ['label' => 'Tag Manager', 'color' => '#F57C00'],
                        ['label' => 'Meta Pixel', 'color' => '#1877F2'],
                    ],
                ],
            ]);

        $trustBarHtml = $this->trustBarHtml();

        $this->assertTrustLabelsInOrder($trustBarHtml);
        $this->assertStringNotContainsString('<strong>Google Ads</strong>', $trustBarHtml);
        $this->assertStringNotContainsString('&lt;strong&gt;Google Ads&lt;/strong&gt;', $trustBarHtml);
        $this->assertSame(7, substr_count($trustBarHtml, 'group cursor-default'));
    }

    public function test_homepage_trust_bar_ignores_more_than_seven_items_and_blocks_invalid_colors(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $trustBar = PageSection::where('page_key', 'home')->where('section_key', 'trust_bar')->firstOrFail();
        $payload = $trustBar->payload;
        $payload['items'][0]['color'] = 'background-image:url(javascript:alert(1))';
        $payload['items'][] = ['label' => 'Extra Platform', 'color' => '#4285F4'];
        $trustBar->update(['payload' => $payload]);

        $trustBarHtml = $this->trustBarHtml();

        $this->assertSame(7, substr_count($trustBarHtml, 'group cursor-default'));
        $this->assertStringNotContainsString('Extra Platform', $trustBarHtml);
        $this->assertStringNotContainsString('background-image', $trustBarHtml);
        $this->assertStringNotContainsString('javascript:alert(1)', $trustBarHtml);
        $this->assertStringContainsString('background-color: #4285F4', $trustBarHtml);
        $this->assertTrustLabelsInOrder($trustBarHtml);
    }

    public function test_homepage_trust_bar_keeps_single_current_sequence_structure(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $trustBarHtml = $this->trustBarHtml();

        $this->assertSame(1, substr_count($trustBarHtml, 'flex flex-wrap items-center justify-center gap-6 md:gap-12'));
        $this->assertSame(7, substr_count($trustBarHtml, 'w-2.5 h-2.5 rounded-full'));
    }

    private function trustBarHtml(): string
    {
        preg_match('/<section class="bg-white border-y border-gray-100 py-12">(.*?)<\/section>/s', $this->renderHome(), $matches);

        return $matches[1] ?? '';
    }

    private function assertTrustLabelsInOrder(string $trustBarHtml): void
    {
        $lastPosition = -1;

        foreach ($this->expectedLabels() as $label) {
            $position = strpos($trustBarHtml, $label);

            $this->assertNotFalse($position, "Missing trust bar label: {$label}");
            $this->assertGreaterThan($lastPosition, $position, "Trust bar label out of order: {$label}");

            $lastPosition = $position;
        }
    }

    /**
     * @return array<int, string>
     */
    private function expectedLabels(): array
    {
        return [
            'Google Ads',
            'Tag Manager',
            'Meta Pixel',
            'Laravel',
            'MySQL',
            'Analytics GA4',
            'PHP 8',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function expectedColors(): array
    {
        return [
            '#4285F4',
            '#F57C00',
            '#1877F2',
            '#FF2D20',
            '#4479A1',
            '#E37400',
            '#777BB4',
        ];
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
