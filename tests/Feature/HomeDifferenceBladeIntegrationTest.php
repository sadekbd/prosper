<?php

namespace Tests\Feature;

use App\Models\PageSection;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomeDifferenceBladeIntegrationTest extends TestCase
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

    public function test_homepage_difference_renders_seeded_heading_and_three_cards(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->differenceHtml();

        $this->assertStringContainsString('Why Choose Us', $html);
        $this->assertStringContainsString('The Prosper Media Difference', $html);
        $this->assertStringContainsString("We combine deep technical expertise with marketing intelligence to deliver results others simply can&#039;t match.", $html);
        $this->assertSame(3, substr_count($html, 'card-hover aos group relative overflow-hidden'));
        $this->assertCardsInOrder($html);
        $this->assertStringContainsString('We code the tracking setup that other agencies miss.', $html);
        $this->assertStringContainsString('captured with precision using GTM, server-side tracking, and custom API integrations.', $html);
        $this->assertStringContainsString('Every click is treated as an investment, not an expense.', $html);
        $this->assertStringContainsString('Clear reporting, measurable results, and honest technical support.', $html);
    }

    public function test_homepage_difference_uses_only_approved_color_class_mapping(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $html = $this->differenceHtml();

        $this->assertStringContainsString('text-pm-cyan/5', $html);
        $this->assertStringContainsString('bg-pm-cyan/10', $html);
        $this->assertStringContainsString('text-pm-cyan', $html);
        $this->assertStringContainsString('text-pm-gold/5', $html);
        $this->assertStringContainsString('bg-pm-gold/10', $html);
        $this->assertStringContainsString('text-pm-gold', $html);
        $this->assertStringNotContainsString('text-red-evil', $html);
        $this->assertStringNotContainsString('arbitrary-class', $html);
    }

    public function test_homepage_difference_falls_back_when_record_is_missing_or_inactive(): void
    {
        $this->assertCardsInOrder($this->differenceHtml());

        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'difference')
            ->update(['status' => 'inactive']);

        $html = $this->differenceHtml();

        $this->assertStringContainsString('Why Choose Us', $html);
        $this->assertStringContainsString('The Prosper Media Difference', $html);
        $this->assertCardsInOrder($html);
        $this->assertSame(3, substr_count($html, 'card-hover aos group relative overflow-hidden'));
    }

    public function test_homepage_difference_falls_back_for_malformed_payload_and_rejects_html(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        PageSection::where('page_key', 'home')
            ->where('section_key', 'difference')
            ->firstOrFail()
            ->update([
                'payload' => [
                    'cards' => [
                        [
                            'badge' => '<strong>No. 01</strong>',
                            'color' => 'cyan',
                            'icon_path' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                            'title' => '<em>Tech-First Marketing</em>',
                            'body' => '<script>alert(1)</script>',
                        ],
                    ],
                ],
            ]);

        $html = $this->differenceHtml();

        $this->assertCardsInOrder($html);
        $this->assertStringNotContainsString('<strong>No. 01</strong>', $html);
        $this->assertStringNotContainsString('&lt;strong&gt;No. 01&lt;/strong&gt;', $html);
        $this->assertStringNotContainsString('<em>Tech-First Marketing</em>', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertSame(3, substr_count($html, 'card-hover aos group relative overflow-hidden'));
    }

    public function test_homepage_difference_limits_extra_cards_and_blocks_unsafe_svg_and_colors(): void
    {
        $this->seed(HomePageSectionSeeder::class);

        $difference = PageSection::where('page_key', 'home')->where('section_key', 'difference')->firstOrFail();
        $payload = $difference->payload;
        $payload['cards'][0]['icon_path'] = 'M0 0 <script>alert(1)</script> onload javascript style';
        $payload['cards'][1]['color'] = 'text-red-evil arbitrary-class';
        $payload['cards'][] = [
            'badge' => 'No. 04',
            'color' => 'cyan',
            'icon_path' => 'M1 1',
            'title' => 'Extra Difference',
            'body' => 'Extra card body',
        ];
        $difference->update(['payload' => $payload]);

        $html = $this->differenceHtml();

        $this->assertSame(3, substr_count($html, 'card-hover aos group relative overflow-hidden'));
        $this->assertStringNotContainsString('Extra Difference', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('onload', $html);
        $this->assertStringNotContainsString('javascript', $html);
        $this->assertStringNotContainsString('style', $html);
        $this->assertStringNotContainsString('text-red-evil', $html);
        $this->assertStringNotContainsString('arbitrary-class', $html);
        $this->assertStringContainsString('d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"', $html);
        $this->assertCardsInOrder($html);
    }

    private function differenceHtml(): string
    {
        preg_match('/<section class="section-padding bg-pm-grey">(.*?)<\/section>/s', $this->renderHome(), $matches);

        return $matches[1] ?? '';
    }

    private function assertCardsInOrder(string $html): void
    {
        $expected = [
            'No. 01',
            'Tech-First Marketing',
            'No. 02',
            'ROI Focused',
            'No. 03',
            'Transparent Data',
        ];
        $lastPosition = -1;

        foreach ($expected as $text) {
            $position = strpos($html, $text);

            $this->assertNotFalse($position, "Missing Difference card text: {$text}");
            $this->assertGreaterThan($lastPosition, $position, "Difference card text out of order: {$text}");

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
