<?php

namespace Tests\Feature;

use App\Models\PageSection;
use Database\Seeders\HomePageSectionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomePageSectionSeederTest extends TestCase
{
    private Migration $migration;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        $this->migration = require database_path('migrations/2026_07_04_000001_create_page_sections_table.php');

        Schema::dropIfExists('page_sections');
        $this->migration->up();
    }

    protected function tearDown(): void
    {
        $this->migration->down();

        parent::tearDown();
    }

    public function test_home_page_section_seeder_creates_expected_idempotent_records(): void
    {
        $this->seed(HomePageSectionSeeder::class);
        $this->seed(HomePageSectionSeeder::class);

        $sections = PageSection::forPage('home')->ordered()->get()->keyBy('section_key');

        $expectedSortOrders = [
            'hero' => 10,
            'stats' => 20,
            'trust_bar' => 30,
            'difference' => 40,
            'services_intro' => 50,
            'portfolio_intro' => 60,
            'blog_intro' => 70,
            'primary_cta' => 80,
        ];

        $this->assertSame(8, PageSection::where('page_key', 'home')->count());
        $this->assertSame(array_keys($expectedSortOrders), $sections->keys()->all());

        foreach ($expectedSortOrders as $sectionKey => $sortOrder) {
            $section = $sections->get($sectionKey);

            $this->assertSame($sortOrder, $section->sort_order);
            $this->assertSame('active', $section->status);
            $this->assertIsArray($section->payload);
        }

        $hero = $sections->get('hero');
        $this->assertSame('Be Optimistic', $hero->eyebrow);
        $this->assertSame('Engineering Digital Success with Technical Precision.', $hero->title);
        $this->assertSame('View Our Services', $hero->button_label);
        $this->assertSame('/services', $hero->button_url);
        $this->assertSame(['Engineering Digital', 'Success', 'with', 'Technical Precision.'], $hero->payload['title_lines']);
        $this->assertSame('Get a Free Audit', $hero->payload['secondary_button']['label']);
        $this->assertSame('/contact', $hero->payload['secondary_button']['url']);

        $this->assertSame([
            ['value' => 150, 'suffix' => '+', 'label' => 'Projects Done'],
            ['value' => 98, 'suffix' => '%', 'label' => 'Client Satisfaction'],
            ['value' => 5, 'suffix' => 'x', 'label' => 'Average ROAS'],
            ['value' => 3, 'suffix' => '+', 'sep' => 'yr', 'label' => 'Experience'],
        ], $sections->get('stats')->payload['items']);

        $trustItems = $sections->get('trust_bar')->payload['items'];
        $this->assertCount(7, $trustItems);
        $this->assertSame(['Google Ads', 'Tag Manager', 'Meta Pixel', 'Laravel', 'MySQL', 'Analytics GA4', 'PHP 8'], array_column($trustItems, 'label'));

        $this->assertSame([
            [
                'badge' => 'No. 01',
                'color' => 'cyan',
                'icon_path' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                'title' => 'Tech-First Marketing',
                'body' => 'We code the tracking setup that other agencies miss. Every pixel, every event, every conversion â€” captured with precision using GTM, server-side tracking, and custom API integrations.',
            ],
            [
                'badge' => 'No. 02',
                'color' => 'gold',
                'icon_path' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'title' => 'ROI Focused',
                'body' => 'Every click is treated as an investment, not an expense. We obsess over ROAS, CPA, and conversion rates â€” building campaigns that compound in profitability over time.',
            ],
            [
                'badge' => 'No. 03',
                'color' => 'cyan',
                'icon_path' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                'title' => 'Transparent Data',
                'body' => "Clear reporting, measurable results, and honest technical support. You always know exactly what is happening with your campaigns and why it's working.",
            ],
        ], $sections->get('difference')->payload['cards']);

        $primaryCta = $sections->get('primary_cta');
        $this->assertSame('Start Growing Today', $primaryCta->button_label);
        $this->assertSame('/contact', $primaryCta->button_url);
        $this->assertSame('See Our Work', $primaryCta->payload['secondary_button']['label']);
        $this->assertSame('/portfolio', $primaryCta->payload['secondary_button']['url']);
        $this->assertSame([
            'No Long-Term Contracts',
            'Free Audit Consultation',
            'ROI-Focused Approach',
            '100% Transparent Reporting',
        ], $primaryCta->payload['proof_points']);

        $payloadJson = $sections->pluck('payload')->toJson();
        foreach ([
            'Google Ads Mastery',
            'E-Commerce Google Ads Overhaul',
            'GTM + Meta CAPI Server Tracking',
            'Laravel SaaS Landing Page',
            'How to Set Up Server-Side Conversion Tracking in 2025',
            'Server-Side vs Client-Side Tracking: The Complete Comparison',
            'Building High-Converting Laravel Landing Pages That Actually Convert',
            'App\\\\Models\\\\Service',
            'controller_variable',
            'current_status',
        ] as $excludedContent) {
            $this->assertStringNotContainsString($excludedContent, $payloadJson);
        }

        $duplicateGroups = DB::table('page_sections')
            ->select('page_key', 'section_key', DB::raw('COUNT(*) as aggregate_count'))
            ->groupBy('page_key', 'section_key')
            ->having('aggregate_count', '>', 1)
            ->count();

        $this->assertSame(0, $duplicateGroups);
    }
}
