<?php

namespace App\Support;

use App\Models\PageSection;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class HomepageContent
{
    public const SECTION_KEYS = [
        'hero',
        'stats',
        'trust_bar',
        'difference',
        'services_intro',
        'portfolio_intro',
        'blog_intro',
        'primary_cta',
    ];

    public const SORT_ORDERS = [
        'hero' => 10,
        'stats' => 20,
        'trust_bar' => 30,
        'difference' => 40,
        'services_intro' => 50,
        'portfolio_intro' => 60,
        'blog_intro' => 70,
        'primary_cta' => 80,
    ];

    private const METRIC_COLORS = ['text-pm-cyan', 'text-pm-gold', 'text-white'];

    private const TRUST_COLORS = [
        '#4285F4',
        '#F57C00',
        '#1877F2',
        '#FF2D20',
        '#4479A1',
        '#E37400',
        '#777BB4',
    ];

    /**
     * @return array<string, mixed>
     */
    public function hero(?PageSection $section): array
    {
        $section = $this->activeSection($section);
        $payload = $this->payload($section);
        $dashboard = data_get($payload, 'dashboard', []);
        $dashboard = is_array($dashboard) ? $dashboard : [];

        $secondaryButton = data_get($payload, 'secondary_button', []);
        $secondaryButton = is_array($secondaryButton) ? $secondaryButton : [];

        $fallback = $this->heroDefaults();
        $titleLines = data_get($payload, 'title_lines', $fallback['title_lines']);
        $titleLines = is_array($titleLines) && count($titleLines) >= 4
            ? array_values($titleLines)
            : $fallback['title_lines'];

        $rawMetrics = data_get($dashboard, 'metrics', []);
        $rawMetrics = is_array($rawMetrics) ? array_values($rawMetrics) : [];
        $metrics = collect($fallback['metrics'])->map(function (array $metricFallback, int $index) use ($rawMetrics) {
            $metric = isset($rawMetrics[$index]) && is_array($rawMetrics[$index]) ? $rawMetrics[$index] : [];
            $color = $metric['color'] ?? $metricFallback['color'];

            return [
                'label' => $metric['label'] ?? $metricFallback['label'],
                'value' => $metric['value'] ?? $metricFallback['value'],
                'change' => $metric['change'] ?? $metricFallback['change'],
                'color' => $this->metricColor($color, $metricFallback['color']),
            ];
        })->all();

        $chartDays = data_get($dashboard, 'chart.days', $fallback['chart_days']);
        $chartDays = is_array($chartDays) && count($chartDays) === 7 ? array_values($chartDays) : $fallback['chart_days'];

        $rawBarHeights = data_get($dashboard, 'chart.bar_heights', []);
        $rawBarHeights = is_array($rawBarHeights) ? array_values($rawBarHeights) : [];
        $barHeights = collect($fallback['bar_heights'])->map(
            fn ($height, $index) => $this->chartHeight($rawBarHeights[$index] ?? null, $height)
        )->all();

        $trackingItems = data_get($dashboard, 'tracking.items', $fallback['tracking_items']);
        $trackingItems = is_array($trackingItems) ? $trackingItems : $fallback['tracking_items'];

        $floatingBadges = data_get($dashboard, 'floating_badges', $fallback['floating_badges']);
        $floatingBadges = is_array($floatingBadges) ? array_values($floatingBadges) : $fallback['floating_badges'];

        return [
            'eyebrow' => $section->eyebrow ?? $fallback['eyebrow'],
            'title_lines' => $titleLines,
            'subtitle' => $section->subtitle ?? $fallback['subtitle'],
            'primary_label' => $section->button_label ?? $fallback['primary_label'],
            'primary_url' => $this->safeUrl($section->button_url ?? $fallback['primary_url'], 'services', $fallback['primary_url']),
            'secondary_label' => data_get($secondaryButton, 'label', $fallback['secondary_label']),
            'secondary_url' => $this->routeOrSafeUrl(
                data_get($secondaryButton, 'route'),
                data_get($secondaryButton, 'url', $fallback['secondary_url']),
                'contact',
                $fallback['secondary_url']
            ),
            'dashboard' => [
                'eyebrow' => data_get($dashboard, 'eyebrow', $fallback['dashboard_eyebrow']),
                'title' => data_get($dashboard, 'title', $fallback['dashboard_title']),
                'status' => data_get($dashboard, 'status', $fallback['dashboard_status']),
                'metrics' => $metrics,
                'chart_label' => data_get($dashboard, 'chart.label', $fallback['chart_label']),
                'chart_days' => $chartDays,
                'bar_heights' => $barHeights,
                'tracking_label' => data_get($dashboard, 'tracking.label', $fallback['tracking_label']),
                'tracking_items' => $trackingItems,
                'top_badge' => $floatingBadges[0] ?? $fallback['floating_badges'][0],
                'bottom_badge' => $floatingBadges[1] ?? $fallback['floating_badges'][1],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function stats(?PageSection $section): array
    {
        $section = $this->activeSection($section);
        $fallbackItems = $this->statsDefaults()['items'];
        $rawItems = data_get($this->payload($section), 'items', []);
        $rawItems = is_array($rawItems) ? $rawItems : [];

        $items = collect($rawItems)
            ->filter(fn ($item) => is_array($item))
            ->map(fn ($item) => $this->statItem($item))
            ->filter()
            ->take(4)
            ->values();

        return [
            'items' => $items->count() === 4 ? $items->all() : $fallbackItems,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function trustBar(?PageSection $section): array
    {
        $section = $this->activeSection($section);
        $fallback = $this->trustBarDefaults();
        $rawItems = data_get($this->payload($section), 'items', []);
        $rawItems = is_array($rawItems) ? $rawItems : [];

        $items = collect($rawItems)
            ->filter(fn ($item) => is_array($item))
            ->map(fn ($item, $index) => $this->trustBarItem($item, $fallback['items'][$index] ?? null))
            ->filter()
            ->take(7)
            ->values();

        return [
            'title' => $section->title ?? $fallback['title'],
            'items' => $items->count() === 7 ? $items->all() : $fallback['items'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function difference(?PageSection $section): array
    {
        $section = $this->activeSection($section);
        $fallback = $this->differenceDefaults();
        $rawCards = data_get($this->payload($section), 'cards', []);
        $rawCards = is_array($rawCards) ? $rawCards : [];

        $cards = collect($rawCards)
            ->take(3)
            ->filter(fn ($card) => is_array($card))
            ->map(fn ($card, $index) => $this->differenceCard($card, $fallback['cards'][$index] ?? null))
            ->filter()
            ->values();

        return [
            'eyebrow' => $section->eyebrow ?? $fallback['eyebrow'],
            'title' => $section->title ?? $fallback['title'],
            'subtitle' => $section->subtitle ?? $fallback['subtitle'],
            'cards' => $cards->count() === 3 ? $cards->all() : $fallback['cards'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function servicesIntro(?PageSection $section): array
    {
        $section = $this->activeSection($section);
        $fallback = $this->servicesIntroDefaults();
        $payload = $this->payload($section);

        $rawIcons = data_get($payload, 'card_icons', []);
        $rawIcons = is_array($rawIcons) ? array_values($rawIcons) : [];

        $icons = collect($fallback['card_icons'])->map(
            fn ($icon, $index) => $this->safeSvgPath($rawIcons[$index] ?? null, $icon)
        )->all();

        return [
            'eyebrow' => $section->eyebrow ?? $fallback['eyebrow'],
            'title' => $section->title ?? $fallback['title'],
            'subtitle' => $section->subtitle ?? $fallback['subtitle'],
            'cta_label' => $section->button_label ?? $fallback['cta_label'],
            'cta_url' => $this->safeUrl($section->button_url ?? $fallback['cta_url'], 'services', $fallback['cta_url']),
            'card_link_label' => $this->plainText(data_get($payload, 'card_link_label'), $fallback['card_link_label'], 40),
            'card_icons' => $icons,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function portfolioIntro(?PageSection $section): array
    {
        $section = $this->activeSection($section);
        $fallback = $this->portfolioIntroDefaults();
        $payload = $this->payload($section);

        return [
            'eyebrow' => $this->plainText($section->eyebrow ?? null, $fallback['eyebrow'], 80),
            'title' => $this->plainText($section->title ?? null, $fallback['title'], 120),
            'subtitle' => $this->plainText($section->subtitle ?? null, $fallback['subtitle'], 220),
            'cta_label' => $this->plainText($section->button_label ?? null, $fallback['cta_label'], 50),
            'cta_url' => $this->safeUrl($section->button_url ?? null, 'portfolio', $fallback['cta_url']),
            'card_link_label' => $this->plainText($payload['card_link_label'] ?? null, $fallback['card_link_label'], 50),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function portfolioCards($portfolios): array
    {
        $liveProjects = $portfolios instanceof Collection && $portfolios->count() > 0
            ? $portfolios->take(3)->map(function ($project) {
                $slug = $this->scalarText(data_get($project, 'slug'), 120);
                $category = $this->scalarText(data_get($project, 'category'), 80);
                $categoryLabel = $this->scalarText(data_get($project, 'category_label'), 80);

                if ($categoryLabel === '' && $category !== '') {
                    $categoryLabel = ucfirst(str_replace('_', ' ', $category));
                }

                return [
                    'title' => $this->scalarText(data_get($project, 'title'), 140),
                    'category_label' => $categoryLabel,
                    'description' => $this->scalarText(data_get($project, 'short_description') ?? data_get($project, 'description'), 260),
                    'result' => $this->scalarText(data_get($project, 'result_summary') ?? data_get($project, 'result'), 160),
                    'technologies' => $this->technologies(data_get($project, 'technologies')),
                    'url' => $slug !== '' ? route('portfolio.show', $slug) : route('portfolio'),
                ];
            })
            : collect();

        return $liveProjects->count() > 0 ? $liveProjects->all() : $this->portfolioFallbackProjects();
    }

    /**
     * @return array<string, mixed>
     */
    public function blogIntro(?PageSection $section): array
    {
        $section = $this->activeSection($section);
        $fallback = $this->blogIntroDefaults();
        $payload = $this->payload($section);

        return [
            'eyebrow' => $this->plainText($section->eyebrow ?? null, $fallback['eyebrow'], 80),
            'title' => $this->plainText($section->title ?? null, $fallback['title'], 120),
            'subtitle' => $this->plainText($section->subtitle ?? null, $fallback['subtitle'], 220),
            'cta_label' => $this->plainText($section->button_label ?? null, $fallback['cta_label'], 50),
            'cta_url' => $this->safeUrl($section->button_url ?? null, 'blog', $fallback['cta_url']),
            'card_link_label' => $this->plainText($payload['card_link_label'] ?? null, $fallback['card_link_label'], 50),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function blogCards($articles): array
    {
        $liveArticles = $articles instanceof Collection && $articles->count() > 0
            ? $articles->take(3)->map(function ($article) {
                $slug = $this->scalarText(data_get($article, 'slug'), 120);
                $categoryLabel = $this->scalarText(data_get($article, 'category.name'), 80);

                if ($categoryLabel === '') {
                    $categoryLabel = $this->scalarText(data_get($article, 'category_label') ?? data_get($article, 'category'), 80);
                }

                return [
                    'category_label' => $categoryLabel,
                    'title' => $this->scalarText(data_get($article, 'title'), 180),
                    'excerpt' => $this->scalarText(data_get($article, 'excerpt'), 320),
                    'date' => $this->date(data_get($article, 'published_at') ?? data_get($article, 'created_at')),
                    'url' => $slug !== '' ? route('blog.show', $slug) : route('blog'),
                ];
            })
            : collect();

        return $liveArticles->count() > 0 ? $liveArticles->all() : $this->blogFallbackArticles();
    }

    /**
     * @return array<string, mixed>
     */
    public function primaryCta(?PageSection $section): array
    {
        $section = $this->activeSection($section);
        $fallback = $this->primaryCtaDefaults();
        $payload = $this->payload($section);

        $rawTitleLines = data_get($payload, 'title_lines', []);
        $titleLines = is_array($rawTitleLines)
            ? collect($rawTitleLines)
                ->map(fn ($line) => is_string($line) ? trim($line) : null)
                ->filter(fn ($line) => $this->isPlainText($line, 120))
                ->take(2)
                ->values()
                ->all()
            : [];
        $titleLines = count($titleLines) === 2 ? $titleLines : $fallback['title_lines'];

        $rawProofPoints = data_get($payload, 'proof_points', []);
        $proofPoints = is_array($rawProofPoints)
            ? collect($rawProofPoints)
                ->map(fn ($proof) => is_string($proof) ? trim($proof) : null)
                ->filter(fn ($proof) => $this->isPlainText($proof, 100))
                ->take(4)
                ->values()
                ->all()
            : [];
        $proofPoints = count($proofPoints) === 4 ? $proofPoints : $fallback['proof_points'];

        $secondaryButton = data_get($payload, 'secondary_button', []);
        $secondaryButton = is_array($secondaryButton) ? $secondaryButton : [];

        return [
            'eyebrow' => $this->plainText($section->eyebrow ?? null, $fallback['eyebrow'], 80),
            'title_lines' => $titleLines,
            'subtitle' => $this->plainText($section->subtitle ?? null, $fallback['subtitle'], 500),
            'primary_label' => $this->plainText($section->button_label ?? null, $fallback['primary_label'], 50),
            'primary_url' => $this->safeUrl($section->button_url ?? null, 'contact', $fallback['primary_url']),
            'secondary_label' => $this->plainText($secondaryButton['label'] ?? null, $fallback['secondary_label'], 50),
            'secondary_url' => $this->safeUrl($secondaryButton['route'] ?? ($secondaryButton['url'] ?? null), 'portfolio', $fallback['secondary_url']),
            'proof_points' => $proofPoints,
        ];
    }

    /**
     * @return array<int, object>
     */
    public function serviceCards($services): array
    {
        $cards = $services instanceof Collection && $services->count() > 0
            ? $services->take(3)
            : collect([
                (object) ['title' => 'Google Ads Mastery', 'subtitle' => 'Maximise your ROI with data-driven search advertising.', 'slug' => 'google-ads-mastery'],
                (object) ['title' => 'Advanced Conversion Tracking', 'subtitle' => 'Stop guessing and start measuring every touchpoint.', 'slug' => 'advanced-conversion-tracking'],
                (object) ['title' => 'Professional Web Development', 'subtitle' => 'High-performance websites built for conversion.', 'slug' => 'professional-web-development'],
            ]);

        return $cards->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'hero' => $this->heroDefaults(),
            'stats' => $this->statsDefaults(),
            'trust_bar' => $this->trustBarDefaults(),
            'difference' => $this->differenceDefaults(),
            'services_intro' => $this->servicesIntroDefaults(),
            'portfolio_intro' => $this->portfolioIntroDefaults(),
            'portfolio_cards' => $this->portfolioFallbackProjects(),
            'blog_intro' => $this->blogIntroDefaults(),
            'blog_cards' => $this->blogFallbackArticles(),
            'primary_cta' => $this->primaryCtaDefaults(),
        ];
    }

    private function activeSection(?PageSection $section): ?PageSection
    {
        return $section && ($section->status ?? 'active') === 'active' ? $section : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(?PageSection $section): array
    {
        return $section && is_array($section->payload) ? $section->payload : [];
    }

    private function plainText($value, string $fallback, int $maxLength): string
    {
        if (! is_string($value)) {
            return $fallback;
        }

        $value = trim($value);

        return $this->isPlainText($value, $maxLength) ? $value : $fallback;
    }

    private function isPlainText($value, int $maxLength): bool
    {
        return is_string($value)
            && $value !== ''
            && mb_strlen($value) <= $maxLength
            && strip_tags($value) === $value;
    }

    private function safeUrl($value, string $fallbackRoute, string $fallbackUrl): string
    {
        if (is_string($value)) {
            $value = trim($value);

            if (($value === $fallbackRoute || $value === $fallbackUrl) && Route::has($fallbackRoute)) {
                return route($fallbackRoute);
            }

            if ($value === '/' || str_starts_with($value, '/') || str_starts_with($value, 'https://') || str_starts_with($value, 'http://')) {
                return $value;
            }
        }

        return Route::has($fallbackRoute) ? route($fallbackRoute) : $fallbackUrl;
    }

    private function routeOrSafeUrl($routeValue, $urlValue, string $fallbackRoute, string $fallbackUrl): string
    {
        if (is_string($routeValue) && $routeValue === $fallbackRoute && Route::has($fallbackRoute)) {
            return route($fallbackRoute);
        }

        return $this->safeUrl($urlValue, $fallbackRoute, $fallbackUrl);
    }

    private function metricColor($value, string $fallback): string
    {
        return in_array($value, self::METRIC_COLORS, true) ? $value : $fallback;
    }

    private function chartHeight($value, int $fallback): int|float
    {
        return is_numeric($value) && $value >= 0 && $value <= 100 ? $value : $fallback;
    }

    /**
     * @return array<string, string>|null
     */
    private function statItem(array $item): ?array
    {
        $value = $item['value'] ?? null;
        $value = is_numeric($value) || (is_string($value) && preg_match('/^[0-9][0-9,.]{0,11}$/', $value))
            ? (string) $value
            : null;
        $suffix = isset($item['suffix']) ? (string) $item['suffix'] : '';
        $suffix = in_array($suffix, ['+', '%', 'x', ''], true) ? $suffix : null;
        $sep = isset($item['sep']) ? trim((string) $item['sep']) : '';
        $sep = $sep === '' || preg_match('/^[A-Za-z]{1,5}$/', $sep) ? $sep : null;
        $label = isset($item['label']) ? trim((string) $item['label']) : null;
        $label = $this->isPlainText($label, 80) ? $label : null;

        if ($value === null || $suffix === null || $sep === null || $label === null) {
            return null;
        }

        return compact('value', 'suffix', 'sep', 'label');
    }

    /**
     * @return array<string, string>|null
     */
    private function trustBarItem(array $item, ?array $fallback): ?array
    {
        $label = isset($item['label']) ? trim((string) $item['label']) : null;
        $label = $this->isPlainText($label, 60) ? $label : null;
        $color = isset($item['color']) ? strtoupper((string) $item['color']) : null;
        $color = preg_match('/^#[0-9A-F]{6}$/', $color ?? '') && in_array($color, self::TRUST_COLORS, true)
            ? $color
            : ($fallback['color'] ?? null);

        if ($label === null || $color === null) {
            return null;
        }

        return compact('label', 'color');
    }

    /**
     * @return array<string, string>|null
     */
    private function differenceCard(array $card, ?array $fallback): ?array
    {
        $badge = isset($card['badge']) ? trim((string) $card['badge']) : null;
        $badge = $this->isPlainText($badge, 20) ? $badge : null;
        $color = isset($card['color']) ? trim((string) $card['color']) : null;
        $color = in_array($color, ['cyan', 'gold'], true) ? $color : null;
        $icon = $this->safeSvgPath($card['icon_path'] ?? null, $fallback['icon'] ?? null);
        $title = isset($card['title']) ? trim((string) $card['title']) : null;
        $title = $this->isPlainText($title, 100) ? $title : null;
        $desc = isset($card['body']) ? trim((string) $card['body']) : null;
        $desc = $this->isPlainText($desc, 1000) ? $desc : null;

        if ($badge === null || $color === null || $icon === null || $title === null || $desc === null) {
            return null;
        }

        return compact('badge', 'color', 'icon', 'title', 'desc');
    }

    private function safeSvgPath($value, ?string $fallback): ?string
    {
        $icon = isset($value) ? trim((string) $value) : '';
        $isSafe = $icon !== ''
            && preg_match('/^[A-Za-z0-9\s,.\-]+$/', $icon)
            && ! preg_match('/[<>"\';]|javascript|onload|style/i', $icon);

        return $isSafe ? $icon : $fallback;
    }

    private function scalarText($value, int $maxLength = 160): string
    {
        if (! is_scalar($value)) {
            return '';
        }

        $value = trim((string) $value);

        return $value !== '' && mb_strlen($value) <= $maxLength ? $value : '';
    }

    /**
     * @return array<int, string>
     */
    private function technologies($technologies): array
    {
        if (is_string($technologies)) {
            $decoded = json_decode($technologies, true);
            $technologies = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($technologies)) {
            return [];
        }

        return collect($technologies)
            ->filter(fn ($tech) => is_scalar($tech))
            ->map(fn ($tech) => trim((string) $tech))
            ->filter(function ($tech) {
                return $tech !== ''
                    && mb_strlen($tech) <= 40
                    && strip_tags($tech) === $tech
                    && ! preg_match('/https?:\/\/|javascript|data:|vbscript:|class=|style=/i', $tech);
            })
            ->take(3)
            ->values()
            ->all();
    }

    private function date($value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('M j, Y');
        }

        if (is_string($value) && trim($value) !== '') {
            try {
                return Carbon::parse($value)->format('M j, Y');
            } catch (\Throwable $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * @return array<string, mixed>
     */
    private function heroDefaults(): array
    {
        return [
            'eyebrow' => 'Be Optimistic',
            'title_lines' => ['Engineering Digital', 'Success', 'with', 'Technical Precision.'],
            'subtitle' => 'Professional Google Ads management, conversion tracking, and high-performance web development built to turn visitors into loyal customers.',
            'primary_label' => 'View Our Services',
            'primary_url' => '/services',
            'secondary_label' => 'Get a Free Audit',
            'secondary_url' => '/contact',
            'dashboard_eyebrow' => 'Live Dashboard',
            'dashboard_title' => 'Q2 Campaign Performance',
            'dashboard_status' => 'Live',
            'metrics' => [
                ['label' => 'Conversions', 'value' => '1,248', 'change' => '↑ 34%', 'color' => 'text-pm-cyan'],
                ['label' => 'ROAS', 'value' => '4.8x', 'change' => '↑ 12%', 'color' => 'text-pm-gold'],
                ['label' => 'CTR', 'value' => '7.2%', 'change' => '↑ 8%', 'color' => 'text-white'],
            ],
            'chart_label' => 'Weekly Conversions',
            'chart_days' => ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
            'bar_heights' => [35, 55, 42, 70, 60, 85, 75],
            'tracking_label' => 'Tracking:',
            'tracking_items' => ['GTM', 'GA4', 'Meta API', 'Server-Side'],
            'floating_badges' => [
                ['label' => 'ROI', 'value' => '↑ 340%'],
                ['label' => 'Tracking Active'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function statsDefaults(): array
    {
        return [
            'items' => [
                ['value' => '150', 'suffix' => '+', 'sep' => '', 'label' => 'Projects Done'],
                ['value' => '98', 'suffix' => '%', 'sep' => '', 'label' => 'Client Satisfaction'],
                ['value' => '5', 'suffix' => 'x', 'sep' => '', 'label' => 'Average ROAS'],
                ['value' => '3', 'suffix' => '+', 'sep' => 'yr', 'label' => 'Experience'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function trustBarDefaults(): array
    {
        return [
            'title' => 'Technologies & Platforms We Master',
            'items' => [
                ['label' => 'Google Ads', 'color' => '#4285F4'],
                ['label' => 'Tag Manager', 'color' => '#F57C00'],
                ['label' => 'Meta Pixel', 'color' => '#1877F2'],
                ['label' => 'Laravel', 'color' => '#FF2D20'],
                ['label' => 'MySQL', 'color' => '#4479A1'],
                ['label' => 'Analytics GA4', 'color' => '#E37400'],
                ['label' => 'PHP 8', 'color' => '#777BB4'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function differenceDefaults(): array
    {
        return [
            'eyebrow' => 'Why Choose Us',
            'title' => 'The Prosper Media Difference',
            'subtitle' => "We combine deep technical expertise with marketing intelligence to deliver results others simply can't match.",
            'cards' => [
                [
                    'badge' => 'No. 01',
                    'color' => 'cyan',
                    'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                    'title' => 'Tech-First Marketing',
                    'desc' => 'We code the tracking setup that other agencies miss. Every pixel, every event, every conversion — captured with precision using GTM, server-side tracking, and custom API integrations.',
                ],
                [
                    'badge' => 'No. 02',
                    'color' => 'gold',
                    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    'title' => 'ROI Focused',
                    'desc' => 'Every click is treated as an investment, not an expense. We obsess over ROAS, CPA, and conversion rates — building campaigns that compound in profitability over time.',
                ],
                [
                    'badge' => 'No. 03',
                    'color' => 'cyan',
                    'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                    'title' => 'Transparent Data',
                    'desc' => "Clear reporting, measurable results, and honest technical support. You always know exactly what is happening with your campaigns and why it's working.",
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function servicesIntroDefaults(): array
    {
        return [
            'eyebrow' => 'What We Do',
            'title' => 'Our Core Services',
            'subtitle' => 'Three pillars of technical excellence powering your entire digital growth engine.',
            'cta_label' => 'View All Services',
            'cta_url' => '/services',
            'card_link_label' => 'Learn More',
            'card_icons' => [
                'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
                'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function portfolioIntroDefaults(): array
    {
        return [
            'eyebrow' => 'Our Work',
            'title' => 'Recent Projects',
            'subtitle' => 'Real results from real campaigns. See how we engineer digital success.',
            'cta_label' => 'All Projects',
            'cta_url' => '/portfolio',
            'card_link_label' => 'View Case Study',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function portfolioFallbackProjects(): array
    {
        return [
            [
                'title' => 'E-Commerce Google Ads Overhaul',
                'category_label' => 'Google Ads',
                'description' => 'Full-funnel campaign with audience segmentation, dynamic remarketing, and Smart Bidding strategy.',
                'result' => '320% ROAS — 3 Months',
                'technologies' => ['Google Ads', 'GTM', 'GA4'],
                'url' => route('portfolio'),
            ],
            [
                'title' => 'GTM + Meta CAPI Server Tracking',
                'category_label' => 'Tracking Setup',
                'description' => 'Server-side Facebook Conversion API setup eliminating browser data loss from iOS 14+ changes.',
                'result' => '85% Data Recovery',
                'technologies' => ['Meta CAPI', 'GTM', 'Node.js'],
                'url' => route('portfolio'),
            ],
            [
                'title' => 'Laravel SaaS Landing Page',
                'category_label' => 'Web Development',
                'description' => 'High-converting, mobile-first landing page with A/B testing, heatmaps, and speed optimization.',
                'result' => 'CTR: 4.2% → 11.8%',
                'technologies' => ['Laravel', 'MySQL', 'Tailwind'],
                'url' => route('portfolio'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function blogIntroDefaults(): array
    {
        return [
            'eyebrow' => 'Knowledge Hub',
            'title' => 'Latest Articles',
            'subtitle' => 'Practical insights, tutorials and guides from our technical team.',
            'cta_label' => 'All Articles',
            'cta_url' => '/blog',
            'card_link_label' => 'Read Article',
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function blogFallbackArticles(): array
    {
        return [
            [
                'category_label' => 'Google Ads Tips',
                'title' => 'How to Set Up Server-Side Conversion Tracking in 2025',
                'excerpt' => 'A complete guide covering GTM server containers, transport URL setup, and debugging server-side tags for maximum conversion data accuracy.',
                'date' => 'May 15, 2025',
                'url' => route('blog'),
            ],
            [
                'category_label' => 'Tracking Guides',
                'title' => 'Server-Side vs Client-Side Tracking: The Complete Comparison',
                'excerpt' => 'Understanding the technical difference between client-side pixels and server-side tracking and when each approach maximises your data accuracy.',
                'date' => 'May 8, 2025',
                'url' => route('blog'),
            ],
            [
                'category_label' => 'Web Dev Tutorials',
                'title' => 'Building High-Converting Laravel Landing Pages That Actually Convert',
                'excerpt' => 'The technical and psychological framework behind landing pages that convert at 3x the industry average using Laravel and modern front-end techniques.',
                'date' => 'Apr 28, 2025',
                'url' => route('blog'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function primaryCtaDefaults(): array
    {
        return [
            'eyebrow' => "Let's Work Together",
            'title_lines' => ['Ready to scale your business?', 'Be Optimistic.'],
            'subtitle' => "We've got the data covered. Let's engineer your digital success together with the precision your business deserves.",
            'primary_label' => 'Start Growing Today',
            'primary_url' => '/contact',
            'secondary_label' => 'See Our Work',
            'secondary_url' => '/portfolio',
            'proof_points' => [
                'No Long-Term Contracts',
                'Free Audit Consultation',
                'ROI-Focused Approach',
                '100% Transparent Reporting',
            ],
        ];
    }
}
