<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class HomePageSectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->sections() as $section) {
            PageSection::updateOrCreate(
                [
                    'page_key' => 'home',
                    'section_key' => $section['section_key'],
                ],
                array_merge($section, [
                    'page_key' => 'home',
                    'status' => 'active',
                ])
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function sections(): array
    {
        return [
            [
                'section_key' => 'hero',
                'eyebrow' => 'Be Optimistic',
                'title' => 'Engineering Digital Success with Technical Precision.',
                'subtitle' => 'Professional Google Ads management, conversion tracking, and high-performance web development built to turn visitors into loyal customers.',
                'body' => null,
                'button_label' => 'View Our Services',
                'button_url' => '/services',
                'image' => null,
                'payload' => [
                    'title_lines' => ['Engineering Digital', 'Success', 'with', 'Technical Precision.'],
                    'secondary_button' => [
                        'label' => 'Get a Free Audit',
                        'url' => '/contact',
                        'route' => 'contact',
                    ],
                    'dashboard' => [
                        'eyebrow' => 'Live Dashboard',
                        'title' => 'Q2 Campaign Performance',
                        'status' => 'Live',
                        'metrics' => [
                            ['label' => 'Conversions', 'value' => '1,248', 'change' => 'â†‘ 34%', 'color' => 'text-pm-cyan'],
                            ['label' => 'ROAS', 'value' => '4.8x', 'change' => 'â†‘ 12%', 'color' => 'text-pm-gold'],
                            ['label' => 'CTR', 'value' => '7.2%', 'change' => 'â†‘ 8%', 'color' => 'text-white'],
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
                            ['label' => 'ROI', 'value' => 'â†‘ 340%'],
                            ['label' => 'Tracking Active'],
                        ],
                    ],
                ],
                'sort_order' => 10,
            ],
            [
                'section_key' => 'stats',
                'eyebrow' => null,
                'title' => null,
                'subtitle' => null,
                'body' => null,
                'button_label' => null,
                'button_url' => null,
                'image' => null,
                'payload' => [
                    'items' => [
                        ['value' => 150, 'suffix' => '+', 'label' => 'Projects Done'],
                        ['value' => 98, 'suffix' => '%', 'label' => 'Client Satisfaction'],
                        ['value' => 5, 'suffix' => 'x', 'label' => 'Average ROAS'],
                        ['value' => 3, 'suffix' => '+', 'sep' => 'yr', 'label' => 'Experience'],
                    ],
                ],
                'sort_order' => 20,
            ],
            [
                'section_key' => 'trust_bar',
                'eyebrow' => null,
                'title' => 'Technologies & Platforms We Master',
                'subtitle' => null,
                'body' => null,
                'button_label' => null,
                'button_url' => null,
                'image' => null,
                'payload' => [
                    'items' => [
                        ['label' => 'Google Ads', 'color' => '#4285F4'],
                        ['label' => 'Tag Manager', 'color' => '#F57C00'],
                        ['label' => 'Meta Pixel', 'color' => '#1877F2'],
                        ['label' => 'Laravel', 'color' => '#FF2D20'],
                        ['label' => 'MySQL', 'color' => '#4479A1'],
                        ['label' => 'Analytics GA4', 'color' => '#E37400'],
                        ['label' => 'PHP 8', 'color' => '#777BB4'],
                    ],
                ],
                'sort_order' => 30,
            ],
            [
                'section_key' => 'difference',
                'eyebrow' => 'Why Choose Us',
                'title' => 'The Prosper Media Difference',
                'subtitle' => "We combine deep technical expertise with marketing intelligence to deliver results others simply can't match.",
                'body' => null,
                'button_label' => null,
                'button_url' => null,
                'image' => null,
                'payload' => [
                    'cards' => [
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
                    ],
                ],
                'sort_order' => 40,
            ],
            [
                'section_key' => 'services_intro',
                'eyebrow' => 'What We Do',
                'title' => 'Our Core Services',
                'subtitle' => 'Three pillars of technical excellence powering your entire digital growth engine.',
                'body' => null,
                'button_label' => 'View All Services',
                'button_url' => '/services',
                'image' => null,
                'payload' => [
                    'card_link_label' => 'Learn More',
                    'card_icons' => [
                        'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                        'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
                        'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                    ],
                ],
                'sort_order' => 50,
            ],
            [
                'section_key' => 'portfolio_intro',
                'eyebrow' => 'Our Work',
                'title' => 'Recent Projects',
                'subtitle' => 'Real results from real campaigns. See how we engineer digital success.',
                'body' => null,
                'button_label' => 'All Projects',
                'button_url' => '/portfolio',
                'image' => null,
                'payload' => [
                    'card_link_label' => 'View Case Study',
                ],
                'sort_order' => 60,
            ],
            [
                'section_key' => 'blog_intro',
                'eyebrow' => 'Knowledge Hub',
                'title' => 'Latest Articles',
                'subtitle' => 'Practical insights, tutorials and guides from our technical team.',
                'body' => null,
                'button_label' => 'All Articles',
                'button_url' => '/blog',
                'image' => null,
                'payload' => [
                    'card_link_label' => 'Read Article',
                ],
                'sort_order' => 70,
            ],
            [
                'section_key' => 'primary_cta',
                'eyebrow' => "Let's Work Together",
                'title' => "Ready to scale your business?\nBe Optimistic.",
                'subtitle' => "We've got the data covered. Let's engineer your digital success together with the precision your business deserves.",
                'body' => null,
                'button_label' => 'Start Growing Today',
                'button_url' => '/contact',
                'image' => null,
                'payload' => [
                    'title_lines' => ['Ready to scale your business?', 'Be Optimistic.'],
                    'secondary_button' => [
                        'label' => 'See Our Work',
                        'url' => '/portfolio',
                        'route' => 'portfolio',
                    ],
                    'proof_points' => [
                        'No Long-Term Contracts',
                        'Free Audit Consultation',
                        'ROI-Focused Approach',
                        '100% Transparent Reporting',
                    ],
                ],
                'sort_order' => 80,
            ],
        ];
    }
}
