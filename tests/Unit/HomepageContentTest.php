<?php

namespace Tests\Unit;

use App\Models\PageSection;
use App\Support\HomepageContent;
use Illuminate\Support\Collection;
use Tests\TestCase;

class HomepageContentTest extends TestCase
{
    private HomepageContent $content;

    protected function setUp(): void
    {
        parent::setUp();

        $this->content = new HomepageContent();
    }

    public function test_defaults_for_all_homepage_sections_are_available(): void
    {
        $defaults = $this->content->defaults();

        $this->assertSame(HomepageContent::SECTION_KEYS, [
            'hero',
            'stats',
            'trust_bar',
            'difference',
            'services_intro',
            'portfolio_intro',
            'blog_intro',
            'primary_cta',
        ]);
        $this->assertSame(10, HomepageContent::SORT_ORDERS['hero']);
        $this->assertSame(80, HomepageContent::SORT_ORDERS['primary_cta']);

        $this->assertSame('Be Optimistic', $defaults['hero']['eyebrow']);
        $this->assertSame(['Engineering Digital', 'Success', 'with', 'Technical Precision.'], $defaults['hero']['title_lines']);
        $this->assertSame('View Our Services', $defaults['hero']['primary_label']);
        $this->assertSame('Technologies & Platforms We Master', $defaults['trust_bar']['title']);
        $this->assertSame('Why Choose Us', $defaults['difference']['eyebrow']);
        $this->assertSame('What We Do', $defaults['services_intro']['eyebrow']);
        $this->assertSame('Our Work', $defaults['portfolio_intro']['eyebrow']);
        $this->assertSame('Knowledge Hub', $defaults['blog_intro']['eyebrow']);
        $this->assertSame("Let's Work Together", $defaults['primary_cta']['eyebrow']);
    }

    public function test_valid_seeded_like_payloads_normalize_without_content_changes(): void
    {
        $hero = $this->content->hero($this->section('hero', [
            'eyebrow' => 'Be Optimistic',
            'subtitle' => 'Professional Google Ads management, conversion tracking, and high-performance web development built to turn visitors into loyal customers.',
            'button_label' => 'View Our Services',
            'button_url' => '/services',
            'payload' => [
                'title_lines' => ['Engineering Digital', 'Success', 'with', 'Technical Precision.'],
                'secondary_button' => ['label' => 'Get a Free Audit', 'url' => '/contact', 'route' => 'contact'],
                'dashboard' => [
                    'eyebrow' => 'Live Dashboard',
                    'title' => 'Q2 Campaign Performance',
                    'status' => 'Live',
                    'metrics' => [
                        ['label' => 'Conversions', 'value' => '1,248', 'change' => '↑ 34%', 'color' => 'text-pm-cyan'],
                        ['label' => 'ROAS', 'value' => '4.8x', 'change' => '↑ 12%', 'color' => 'text-pm-gold'],
                        ['label' => 'CTR', 'value' => '7.2%', 'change' => '↑ 8%', 'color' => 'text-white'],
                    ],
                    'chart' => [
                        'label' => 'Weekly Conversions',
                        'days' => ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
                        'bar_heights' => [35, 55, 42, 70, 60, 85, 75],
                    ],
                    'tracking' => [
                        'label' => 'Tracking:',
                        'items' => ['GTM', 'GA4', 'Meta API', 'Server-Side'],
                    ],
                    'floating_badges' => [
                        ['label' => 'ROI', 'value' => '↑ 340%'],
                        ['label' => 'Tracking Active'],
                    ],
                ],
            ],
        ]));

        $this->assertSame('Engineering Digital', $hero['title_lines'][0]);
        $this->assertSame('↑ 34%', $hero['dashboard']['metrics'][0]['change']);
        $this->assertSame('↑ 340%', $hero['dashboard']['top_badge']['value']);
        $this->assertSame([35, 55, 42, 70, 60, 85, 75], $hero['dashboard']['bar_heights']);

        $cta = $this->content->primaryCta($this->section('primary_cta', [
            'eyebrow' => "Let's Work Together",
            'subtitle' => "We've got the data covered. Let's engineer your digital success together with the precision your business deserves.",
            'button_label' => 'Start Growing Today',
            'button_url' => '/contact',
            'payload' => [
                'title_lines' => ['Ready to scale your business?', 'Be Optimistic.'],
                'secondary_button' => ['label' => 'See Our Work', 'route' => 'portfolio', 'url' => '/portfolio'],
                'proof_points' => ['No Long-Term Contracts', 'Free Audit Consultation', 'ROI-Focused Approach', '100% Transparent Reporting'],
            ],
        ]));

        $this->assertSame(['Ready to scale your business?', 'Be Optimistic.'], $cta['title_lines']);
        $this->assertSame('100% Transparent Reporting', $cta['proof_points'][3]);
    }

    public function test_missing_and_inactive_sections_return_exact_fallback_shapes(): void
    {
        $inactiveHero = $this->section('hero', [
            'status' => 'inactive',
            'eyebrow' => 'Hidden Hero',
            'payload' => ['title_lines' => ['Hidden', 'Hero', 'Should', 'Not Render']],
        ]);

        $this->assertSame('Be Optimistic', $this->content->hero(null)['eyebrow']);
        $this->assertSame('Be Optimistic', $this->content->hero($inactiveHero)['eyebrow']);
        $this->assertCount(4, $this->content->stats(null)['items']);
        $this->assertCount(7, $this->content->trustBar(null)['items']);
        $this->assertCount(3, $this->content->difference(null)['cards']);
        $this->assertCount(4, $this->content->primaryCta(null)['proof_points']);
    }

    public function test_unsafe_urls_metric_classes_and_chart_heights_fall_back(): void
    {
        $hero = $this->content->hero($this->section('hero', [
            'button_url' => 'javascript:alert(1)',
            'payload' => [
                'secondary_button' => ['url' => 'data:text/html,bad'],
                'dashboard' => [
                    'metrics' => [
                        ['label' => 'Conversions', 'value' => '1,248', 'change' => '↑ 34%', 'color' => 'text-red-evil arbitrary-class'],
                    ],
                    'chart' => [
                        'bar_heights' => [999, -1, 'bad', 70, 60, 85, 75],
                    ],
                ],
            ],
        ]));

        $this->assertSame(route('services'), $hero['primary_url']);
        $this->assertSame(route('contact'), $hero['secondary_url']);
        $this->assertSame('text-pm-cyan', $hero['dashboard']['metrics'][0]['color']);
        $this->assertSame([35, 55, 42, 70, 60, 85, 75], $hero['dashboard']['bar_heights']);
    }

    public function test_fixed_counts_and_allowlists_are_enforced(): void
    {
        $stats = $this->content->stats($this->section('stats', [
            'payload' => [
                'items' => [
                    ['value' => 1, 'suffix' => '+', 'label' => 'One'],
                    ['value' => 2, 'suffix' => '%', 'label' => 'Two'],
                    ['value' => 3, 'suffix' => 'bad', 'label' => 'Three'],
                    ['value' => 4, 'suffix' => 'x', 'label' => 'Four'],
                ],
            ],
        ]));
        $this->assertSame('Projects Done', $stats['items'][0]['label']);
        $this->assertCount(4, $stats['items']);

        $trust = $this->content->trustBar($this->section('trust_bar', [
            'title' => 'Platforms',
            'payload' => [
                'items' => array_merge(
                    array_fill(0, 7, ['label' => 'Safe', 'color' => '#000000']),
                    [['label' => 'Extra', 'color' => '#4285F4']]
                ),
            ],
        ]));
        $this->assertCount(7, $trust['items']);
        $this->assertSame('#4285F4', $trust['items'][0]['color']);

        $cta = $this->content->primaryCta($this->section('primary_cta', [
            'payload' => [
                'title_lines' => ['One', 'Two'],
                'proof_points' => ['One', 'Two', 'Three', 'Four', 'Five'],
            ],
        ]));
        $this->assertSame(['One', 'Two', 'Three', 'Four'], $cta['proof_points']);
    }

    public function test_unsafe_svg_paths_and_difference_colors_fall_back(): void
    {
        $difference = $this->content->difference($this->section('difference', [
            'eyebrow' => 'Why Choose Us',
            'title' => 'The Prosper Media Difference',
            'subtitle' => "We combine deep technical expertise with marketing intelligence to deliver results others simply can't match.",
            'payload' => [
                'cards' => [
                    [
                        'badge' => 'No. 01',
                        'color' => 'purple arbitrary-class',
                        'icon_path' => '<script>alert(1)</script>',
                        'title' => 'Unsafe',
                        'body' => 'Unsafe body',
                    ],
                    [
                        'badge' => 'No. 02',
                        'color' => 'gold',
                        'icon_path' => 'M9 19v-6a2 2 0 00-2-2H5a2',
                        'title' => 'ROI Focused',
                        'body' => 'Every click is treated as an investment.',
                    ],
                    [
                        'badge' => 'No. 03',
                        'color' => 'cyan',
                        'icon_path' => 'M15 12a3 3 0 11-6 0',
                        'title' => 'Transparent Data',
                        'body' => 'Clear reporting.',
                    ],
                ],
            ],
        ]));

        $this->assertSame('Tech-First Marketing', $difference['cards'][0]['title']);
        $this->assertSame('cyan', $difference['cards'][0]['color']);
        $this->assertStringNotContainsString('<script>', $difference['cards'][0]['icon']);
    }

    public function test_html_like_content_is_rejected_from_strict_page_section_fields(): void
    {
        $portfolio = $this->content->portfolioIntro($this->section('portfolio_intro', [
            'eyebrow' => '<strong>Unsafe</strong>',
            'title' => '<script>alert(1)</script>',
            'subtitle' => 'Safe subtitle',
            'button_label' => '<em>Unsafe</em>',
            'button_url' => 'javascript:alert(1)',
            'payload' => [
                'card_link_label' => '<span>Unsafe</span>',
            ],
        ]));

        $this->assertSame('Our Work', $portfolio['eyebrow']);
        $this->assertSame('Recent Projects', $portfolio['title']);
        $this->assertSame('All Projects', $portfolio['cta_label']);
        $this->assertSame(route('portfolio'), $portfolio['cta_url']);
        $this->assertSame('View Case Study', $portfolio['card_link_label']);
    }

    public function test_domain_records_are_not_embedded_in_page_section_defaults(): void
    {
        $defaults = $this->content->defaults();

        $this->assertArrayNotHasKey('cards', $defaults['services_intro']);
        $this->assertArrayNotHasKey('projects', $defaults['portfolio_intro']);
        $this->assertArrayNotHasKey('articles', $defaults['blog_intro']);

        $this->assertSame('Live Service', $this->content->serviceCards(collect([
            (object) ['title' => 'Live Service', 'subtitle' => 'Live subtitle', 'slug' => 'live-service'],
        ]))[0]->title);
        $this->assertSame('E-Commerce Google Ads Overhaul', $this->content->portfolioCards(collect())[0]['title']);
        $this->assertSame('How to Set Up Server-Side Conversion Tracking in 2025', $this->content->blogCards(collect())[0]['title']);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function section(string $key, array $attributes = []): PageSection
    {
        return new PageSection(array_merge([
            'page_key' => 'home',
            'section_key' => $key,
            'status' => 'active',
            'sort_order' => HomepageContent::SORT_ORDERS[$key] ?? 10,
            'payload' => [],
        ], $attributes));
    }
}
