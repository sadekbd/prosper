<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomepageRequest;
use App\Models\ActivityLog;
use App\Models\PageSection;
use App\Support\HomepageContent;
use Illuminate\Support\Facades\DB;

class HomepageController extends Controller
{
    public function edit(HomepageContent $content)
    {
        $homeSections = PageSection::query()
            ->forPage('home')
            ->ordered()
            ->get()
            ->keyBy('section_key');

        return view('admin.homepage.edit', [
            'form' => $this->formData($content, $homeSections),
            'sectionKeys' => HomepageContent::SECTION_KEYS,
            'routeOptions' => $this->routeOptions(),
            'metricColors' => ['text-pm-cyan', 'text-pm-gold', 'text-white'],
            'trustColors' => ['#4285F4', '#F57C00', '#1877F2', '#FF2D20', '#4479A1', '#E37400', '#777BB4'],
        ]);
    }

    public function update(UpdateHomepageRequest $request)
    {
        $sections = $request->homepageData();

        DB::transaction(function () use ($sections) {
            foreach ($sections as $sectionKey => $section) {
                PageSection::updateOrCreate(
                    [
                        'page_key' => 'home',
                        'section_key' => $sectionKey,
                    ],
                    array_merge($section, [
                        'page_key' => 'home',
                        'status' => 'active',
                    ])
                );
            }

            ActivityLog::log(
                'updated_homepage_sections',
                'PageSection',
                null,
                'Updated homepage page sections'
            );
        });

        return redirect()
            ->route('admin.homepage.edit')
            ->with('success', 'Homepage content updated successfully.');
    }

    /**
     * @param \Illuminate\Support\Collection<string, PageSection> $homeSections
     * @return array<string, mixed>
     */
    private function formData(HomepageContent $content, $homeSections): array
    {
        $hero = $content->hero($homeSections->get('hero'));
        $stats = $content->stats($homeSections->get('stats'));
        $trustBar = $content->trustBar($homeSections->get('trust_bar'));
        $difference = $content->difference($homeSections->get('difference'));
        $servicesIntro = $content->servicesIntro($homeSections->get('services_intro'));
        $portfolioIntro = $content->portfolioIntro($homeSections->get('portfolio_intro'));
        $blogIntro = $content->blogIntro($homeSections->get('blog_intro'));
        $primaryCta = $content->primaryCta($homeSections->get('primary_cta'));

        return [
            'hero' => [
                'eyebrow' => $hero['eyebrow'],
                'title_lines' => $hero['title_lines'],
                'subtitle' => $hero['subtitle'],
                'primary_label' => $hero['primary_label'],
                'primary_route' => $this->routeNameForUrl($homeSections->get('hero')?->button_url, 'services'),
                'primary_url' => $homeSections->get('hero')?->button_url ?? '/services',
                'secondary_button' => [
                    'label' => $hero['secondary_label'],
                    'route' => data_get($homeSections->get('hero')?->payload, 'secondary_button.route', 'contact'),
                    'url' => data_get($homeSections->get('hero')?->payload, 'secondary_button.url', '/contact'),
                ],
                'dashboard' => [
                    'eyebrow' => $hero['dashboard']['eyebrow'],
                    'title' => $hero['dashboard']['title'],
                    'status' => $hero['dashboard']['status'],
                    'metrics' => $hero['dashboard']['metrics'],
                    'chart' => [
                        'label' => $hero['dashboard']['chart_label'],
                        'days' => $hero['dashboard']['chart_days'],
                        'bar_heights' => $hero['dashboard']['bar_heights'],
                    ],
                    'tracking' => [
                        'label' => $hero['dashboard']['tracking_label'],
                        'items' => $hero['dashboard']['tracking_items'],
                    ],
                    'floating_badges' => [
                        [
                            'label' => data_get($hero, 'dashboard.top_badge.label', ''),
                            'value' => data_get($hero, 'dashboard.top_badge.value', ''),
                        ],
                        [
                            'label' => data_get($hero, 'dashboard.bottom_badge.label', ''),
                            'value' => data_get($hero, 'dashboard.bottom_badge.value', ''),
                        ],
                    ],
                ],
            ],
            'stats' => $stats,
            'trust_bar' => $trustBar,
            'difference' => [
                'eyebrow' => $difference['eyebrow'],
                'title' => $difference['title'],
                'subtitle' => $difference['subtitle'],
                'cards' => array_map(fn (array $card) => [
                    'badge' => $card['badge'],
                    'color' => $card['color'],
                    'icon_path' => $card['icon'],
                    'title' => $card['title'],
                    'body' => $card['desc'],
                ], $difference['cards']),
            ],
            'services_intro' => [
                'eyebrow' => $servicesIntro['eyebrow'],
                'title' => $servicesIntro['title'],
                'subtitle' => $servicesIntro['subtitle'],
                'cta_label' => $servicesIntro['cta_label'],
                'cta_route' => $this->routeNameForUrl($homeSections->get('services_intro')?->button_url, 'services'),
                'cta_url' => $homeSections->get('services_intro')?->button_url ?? '/services',
                'card_link_label' => $servicesIntro['card_link_label'],
                'card_icons' => $servicesIntro['card_icons'],
            ],
            'portfolio_intro' => [
                'eyebrow' => $portfolioIntro['eyebrow'],
                'title' => $portfolioIntro['title'],
                'subtitle' => $portfolioIntro['subtitle'],
                'cta_label' => $portfolioIntro['cta_label'],
                'cta_route' => $this->routeNameForUrl($homeSections->get('portfolio_intro')?->button_url, 'portfolio'),
                'cta_url' => $homeSections->get('portfolio_intro')?->button_url ?? '/portfolio',
                'card_link_label' => $portfolioIntro['card_link_label'],
            ],
            'blog_intro' => [
                'eyebrow' => $blogIntro['eyebrow'],
                'title' => $blogIntro['title'],
                'subtitle' => $blogIntro['subtitle'],
                'cta_label' => $blogIntro['cta_label'],
                'cta_route' => $this->routeNameForUrl($homeSections->get('blog_intro')?->button_url, 'blog'),
                'cta_url' => $homeSections->get('blog_intro')?->button_url ?? '/blog',
                'card_link_label' => $blogIntro['card_link_label'],
            ],
            'primary_cta' => [
                'eyebrow' => $primaryCta['eyebrow'],
                'title_lines' => $primaryCta['title_lines'],
                'subtitle' => $primaryCta['subtitle'],
                'primary_label' => $primaryCta['primary_label'],
                'primary_route' => $this->routeNameForUrl($homeSections->get('primary_cta')?->button_url, 'contact'),
                'primary_url' => $homeSections->get('primary_cta')?->button_url ?? '/contact',
                'secondary_button' => [
                    'label' => $primaryCta['secondary_label'],
                    'route' => data_get($homeSections->get('primary_cta')?->payload, 'secondary_button.route', 'portfolio'),
                    'url' => data_get($homeSections->get('primary_cta')?->payload, 'secondary_button.url', '/portfolio'),
                ],
                'proof_points' => $primaryCta['proof_points'],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function routeOptions(): array
    {
        return [
            'home' => 'Home',
            'services' => 'Services',
            'portfolio' => 'Portfolio',
            'blog' => 'Blog',
            'contact' => 'Contact',
        ];
    }

    private function routeNameForUrl(?string $url, string $fallback): string
    {
        return match ($url) {
            '/' => 'home',
            '/services' => 'services',
            '/portfolio' => 'portfolio',
            '/blog' => 'blog',
            '/contact' => 'contact',
            default => $fallback,
        };
    }
}
