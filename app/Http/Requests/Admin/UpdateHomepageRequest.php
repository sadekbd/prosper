<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateHomepageRequest extends FormRequest
{
    private const ROUTES = ['home', 'services', 'portfolio', 'blog', 'contact'];
    private const METRIC_COLORS = ['text-pm-cyan', 'text-pm-gold', 'text-white'];
    private const TRUST_COLORS = ['#4285F4', '#F57C00', '#1877F2', '#FF2D20', '#4479A1', '#E37400', '#777BB4'];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page_key' => ['prohibited'],
            'section_key' => ['prohibited'],
            'sort_order' => ['prohibited'],
            'status' => ['prohibited'],

            'hero' => ['required', 'array'],
            'hero.eyebrow' => ['required', 'string', 'max:80'],
            'hero.title_lines' => ['required', 'array', 'size:4'],
            'hero.title_lines.*' => ['required', 'string', 'max:120'],
            'hero.subtitle' => ['required', 'string', 'max:500'],
            'hero.primary_label' => ['required', 'string', 'max:50'],
            'hero.primary_route' => ['nullable', 'string', 'in:' . implode(',', self::ROUTES)],
            'hero.primary_url' => ['nullable', 'string', 'max:255'],
            'hero.secondary_button' => ['required', 'array'],
            'hero.secondary_button.label' => ['required', 'string', 'max:50'],
            'hero.secondary_button.route' => ['nullable', 'string', 'in:' . implode(',', self::ROUTES)],
            'hero.secondary_button.url' => ['nullable', 'string', 'max:255'],
            'hero.dashboard' => ['required', 'array'],
            'hero.dashboard.eyebrow' => ['required', 'string', 'max:80'],
            'hero.dashboard.title' => ['required', 'string', 'max:120'],
            'hero.dashboard.status' => ['required', 'string', 'max:40'],
            'hero.dashboard.metrics' => ['required', 'array', 'size:3'],
            'hero.dashboard.metrics.*.label' => ['required', 'string', 'max:60'],
            'hero.dashboard.metrics.*.value' => ['required', 'string', 'max:40'],
            'hero.dashboard.metrics.*.change' => ['required', 'string', 'max:40'],
            'hero.dashboard.metrics.*.color' => ['required', 'string', 'in:' . implode(',', self::METRIC_COLORS)],
            'hero.dashboard.chart' => ['required', 'array'],
            'hero.dashboard.chart.label' => ['required', 'string', 'max:80'],
            'hero.dashboard.chart.days' => ['required', 'array', 'size:7'],
            'hero.dashboard.chart.days.*' => ['required', 'string', 'max:10'],
            'hero.dashboard.chart.bar_heights' => ['required', 'array', 'size:7'],
            'hero.dashboard.chart.bar_heights.*' => ['required', 'integer', 'min:0', 'max:100'],
            'hero.dashboard.tracking' => ['required', 'array'],
            'hero.dashboard.tracking.label' => ['required', 'string', 'max:40'],
            'hero.dashboard.tracking.items' => ['required', 'array', 'size:4'],
            'hero.dashboard.tracking.items.*' => ['required', 'string', 'max:40'],
            'hero.dashboard.floating_badges' => ['required', 'array', 'size:2'],
            'hero.dashboard.floating_badges.*.label' => ['required', 'string', 'max:40'],
            'hero.dashboard.floating_badges.*.value' => ['nullable', 'string', 'max:40'],

            'stats' => ['required', 'array'],
            'stats.items' => ['required', 'array', 'size:4'],
            'stats.items.*.value' => ['required', 'string', 'max:20'],
            'stats.items.*.suffix' => ['nullable', 'string', 'in:+,%,x,'],
            'stats.items.*.sep' => ['nullable', 'string', 'max:5'],
            'stats.items.*.label' => ['required', 'string', 'max:80'],

            'trust_bar' => ['required', 'array'],
            'trust_bar.title' => ['required', 'string', 'max:120'],
            'trust_bar.items' => ['required', 'array', 'size:7'],
            'trust_bar.items.*.label' => ['required', 'string', 'max:60'],
            'trust_bar.items.*.color' => ['required', 'string', 'in:' . implode(',', self::TRUST_COLORS)],

            'difference' => ['required', 'array'],
            'difference.eyebrow' => ['required', 'string', 'max:80'],
            'difference.title' => ['required', 'string', 'max:120'],
            'difference.subtitle' => ['required', 'string', 'max:500'],
            'difference.cards' => ['required', 'array', 'size:3'],
            'difference.cards.*.badge' => ['required', 'string', 'max:20'],
            'difference.cards.*.color' => ['required', 'string', 'in:cyan,gold'],
            'difference.cards.*.icon_path' => ['required', 'string', 'max:1000'],
            'difference.cards.*.title' => ['required', 'string', 'max:100'],
            'difference.cards.*.body' => ['required', 'string', 'max:1000'],

            'services_intro' => ['required', 'array'],
            'services_intro.eyebrow' => ['required', 'string', 'max:80'],
            'services_intro.title' => ['required', 'string', 'max:120'],
            'services_intro.subtitle' => ['required', 'string', 'max:500'],
            'services_intro.cta_label' => ['required', 'string', 'max:50'],
            'services_intro.cta_route' => ['nullable', 'string', 'in:' . implode(',', self::ROUTES)],
            'services_intro.cta_url' => ['nullable', 'string', 'max:255'],
            'services_intro.card_link_label' => ['required', 'string', 'max:40'],
            'services_intro.card_icons' => ['required', 'array', 'size:3'],
            'services_intro.card_icons.*' => ['required', 'string', 'max:1000'],

            'portfolio_intro' => ['required', 'array'],
            'portfolio_intro.eyebrow' => ['required', 'string', 'max:80'],
            'portfolio_intro.title' => ['required', 'string', 'max:120'],
            'portfolio_intro.subtitle' => ['required', 'string', 'max:500'],
            'portfolio_intro.cta_label' => ['required', 'string', 'max:50'],
            'portfolio_intro.cta_route' => ['nullable', 'string', 'in:' . implode(',', self::ROUTES)],
            'portfolio_intro.cta_url' => ['nullable', 'string', 'max:255'],
            'portfolio_intro.card_link_label' => ['required', 'string', 'max:50'],

            'blog_intro' => ['required', 'array'],
            'blog_intro.eyebrow' => ['required', 'string', 'max:80'],
            'blog_intro.title' => ['required', 'string', 'max:120'],
            'blog_intro.subtitle' => ['required', 'string', 'max:500'],
            'blog_intro.cta_label' => ['required', 'string', 'max:50'],
            'blog_intro.cta_route' => ['nullable', 'string', 'in:' . implode(',', self::ROUTES)],
            'blog_intro.cta_url' => ['nullable', 'string', 'max:255'],
            'blog_intro.card_link_label' => ['required', 'string', 'max:50'],

            'primary_cta' => ['required', 'array'],
            'primary_cta.eyebrow' => ['required', 'string', 'max:80'],
            'primary_cta.title_lines' => ['required', 'array', 'size:2'],
            'primary_cta.title_lines.*' => ['required', 'string', 'max:120'],
            'primary_cta.subtitle' => ['required', 'string', 'max:500'],
            'primary_cta.primary_label' => ['required', 'string', 'max:50'],
            'primary_cta.primary_route' => ['nullable', 'string', 'in:' . implode(',', self::ROUTES)],
            'primary_cta.primary_url' => ['nullable', 'string', 'max:255'],
            'primary_cta.secondary_button' => ['required', 'array'],
            'primary_cta.secondary_button.label' => ['required', 'string', 'max:50'],
            'primary_cta.secondary_button.route' => ['nullable', 'string', 'in:' . implode(',', self::ROUTES)],
            'primary_cta.secondary_button.url' => ['nullable', 'string', 'max:255'],
            'primary_cta.proof_points' => ['required', 'array', 'size:4'],
            'primary_cta.proof_points.*' => ['required', 'string', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->rejectUnknownKeys($validator);
            $this->validatePlainText($validator);
            $this->validateUrls($validator);
            $this->validateSvgPaths($validator);
            $this->validateStats($validator);
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function homepageData(): array
    {
        $validated = $this->validated();

        return [
            'hero' => [
                'section_key' => 'hero',
                'eyebrow' => $validated['hero']['eyebrow'],
                'title' => implode(' ', $this->orderedList($validated['hero']['title_lines'])),
                'subtitle' => $validated['hero']['subtitle'],
                'body' => null,
                'button_label' => $validated['hero']['primary_label'],
                'button_url' => $this->urlValue($validated['hero']['primary_route'] ?? null, $validated['hero']['primary_url'] ?? null, 'services'),
                'image' => null,
                'payload' => [
                    'title_lines' => $this->orderedList($validated['hero']['title_lines']),
                    'secondary_button' => [
                        'label' => $validated['hero']['secondary_button']['label'],
                        'url' => $this->urlValue($validated['hero']['secondary_button']['route'] ?? null, $validated['hero']['secondary_button']['url'] ?? null, 'contact'),
                        'route' => $this->payloadRouteValue($validated['hero']['secondary_button']['route'] ?? null),
                    ],
                    'dashboard' => [
                        'eyebrow' => $validated['hero']['dashboard']['eyebrow'],
                        'title' => $validated['hero']['dashboard']['title'],
                        'status' => $validated['hero']['dashboard']['status'],
                        'metrics' => $this->orderedList($validated['hero']['dashboard']['metrics']),
                        'chart' => [
                            'label' => $validated['hero']['dashboard']['chart']['label'],
                            'days' => $this->orderedList($validated['hero']['dashboard']['chart']['days']),
                            'bar_heights' => array_map('intval', $this->orderedList($validated['hero']['dashboard']['chart']['bar_heights'])),
                        ],
                        'tracking' => [
                            'label' => $validated['hero']['dashboard']['tracking']['label'],
                            'items' => $this->orderedList($validated['hero']['dashboard']['tracking']['items']),
                        ],
                        'floating_badges' => array_values(array_map(function (array $badge): array {
                            return array_filter([
                                'label' => $badge['label'],
                                'value' => $badge['value'] ?? null,
                            ], fn ($value) => $value !== null && $value !== '');
                        }, $this->orderedList($validated['hero']['dashboard']['floating_badges']))),
                    ],
                ],
                'sort_order' => 10,
            ],
            'stats' => [
                'section_key' => 'stats',
                'eyebrow' => null,
                'title' => null,
                'subtitle' => null,
                'body' => null,
                'button_label' => null,
                'button_url' => null,
                'image' => null,
                'payload' => ['items' => $this->orderedList($validated['stats']['items'])],
                'sort_order' => 20,
            ],
            'trust_bar' => [
                'section_key' => 'trust_bar',
                'eyebrow' => null,
                'title' => $validated['trust_bar']['title'],
                'subtitle' => null,
                'body' => null,
                'button_label' => null,
                'button_url' => null,
                'image' => null,
                'payload' => ['items' => $this->orderedList($validated['trust_bar']['items'])],
                'sort_order' => 30,
            ],
            'difference' => [
                'section_key' => 'difference',
                'eyebrow' => $validated['difference']['eyebrow'],
                'title' => $validated['difference']['title'],
                'subtitle' => $validated['difference']['subtitle'],
                'body' => null,
                'button_label' => null,
                'button_url' => null,
                'image' => null,
                'payload' => ['cards' => $this->orderedList($validated['difference']['cards'])],
                'sort_order' => 40,
            ],
            'services_intro' => [
                'section_key' => 'services_intro',
                'eyebrow' => $validated['services_intro']['eyebrow'],
                'title' => $validated['services_intro']['title'],
                'subtitle' => $validated['services_intro']['subtitle'],
                'body' => null,
                'button_label' => $validated['services_intro']['cta_label'],
                'button_url' => $this->urlValue($validated['services_intro']['cta_route'] ?? null, $validated['services_intro']['cta_url'] ?? null, 'services'),
                'image' => null,
                'payload' => [
                    'card_link_label' => $validated['services_intro']['card_link_label'],
                    'card_icons' => $this->orderedList($validated['services_intro']['card_icons']),
                ],
                'sort_order' => 50,
            ],
            'portfolio_intro' => [
                'section_key' => 'portfolio_intro',
                'eyebrow' => $validated['portfolio_intro']['eyebrow'],
                'title' => $validated['portfolio_intro']['title'],
                'subtitle' => $validated['portfolio_intro']['subtitle'],
                'body' => null,
                'button_label' => $validated['portfolio_intro']['cta_label'],
                'button_url' => $this->urlValue($validated['portfolio_intro']['cta_route'] ?? null, $validated['portfolio_intro']['cta_url'] ?? null, 'portfolio'),
                'image' => null,
                'payload' => ['card_link_label' => $validated['portfolio_intro']['card_link_label']],
                'sort_order' => 60,
            ],
            'blog_intro' => [
                'section_key' => 'blog_intro',
                'eyebrow' => $validated['blog_intro']['eyebrow'],
                'title' => $validated['blog_intro']['title'],
                'subtitle' => $validated['blog_intro']['subtitle'],
                'body' => null,
                'button_label' => $validated['blog_intro']['cta_label'],
                'button_url' => $this->urlValue($validated['blog_intro']['cta_route'] ?? null, $validated['blog_intro']['cta_url'] ?? null, 'blog'),
                'image' => null,
                'payload' => ['card_link_label' => $validated['blog_intro']['card_link_label']],
                'sort_order' => 70,
            ],
            'primary_cta' => [
                'section_key' => 'primary_cta',
                'eyebrow' => $validated['primary_cta']['eyebrow'],
                'title' => implode("\n", $this->orderedList($validated['primary_cta']['title_lines'])),
                'subtitle' => $validated['primary_cta']['subtitle'],
                'body' => null,
                'button_label' => $validated['primary_cta']['primary_label'],
                'button_url' => $this->urlValue($validated['primary_cta']['primary_route'] ?? null, $validated['primary_cta']['primary_url'] ?? null, 'contact'),
                'image' => null,
                'payload' => [
                    'title_lines' => $this->orderedList($validated['primary_cta']['title_lines']),
                    'secondary_button' => [
                        'label' => $validated['primary_cta']['secondary_button']['label'],
                        'url' => $this->urlValue($validated['primary_cta']['secondary_button']['route'] ?? null, $validated['primary_cta']['secondary_button']['url'] ?? null, 'portfolio'),
                        'route' => $this->payloadRouteValue($validated['primary_cta']['secondary_button']['route'] ?? null),
                    ],
                    'proof_points' => $this->orderedList($validated['primary_cta']['proof_points']),
                ],
                'sort_order' => 80,
            ],
        ];
    }

    private function rejectUnknownKeys(Validator $validator): void
    {
        $allowed = [
            'hero' => ['eyebrow', 'title_lines', 'subtitle', 'primary_label', 'primary_route', 'primary_url', 'secondary_button', 'dashboard'],
            'stats' => ['items'],
            'trust_bar' => ['title', 'items'],
            'difference' => ['eyebrow', 'title', 'subtitle', 'cards'],
            'services_intro' => ['eyebrow', 'title', 'subtitle', 'cta_label', 'cta_route', 'cta_url', 'card_link_label', 'card_icons'],
            'portfolio_intro' => ['eyebrow', 'title', 'subtitle', 'cta_label', 'cta_route', 'cta_url', 'card_link_label'],
            'blog_intro' => ['eyebrow', 'title', 'subtitle', 'cta_label', 'cta_route', 'cta_url', 'card_link_label'],
            'primary_cta' => ['eyebrow', 'title_lines', 'subtitle', 'primary_label', 'primary_route', 'primary_url', 'secondary_button', 'proof_points'],
        ];

        foreach ($allowed as $section => $keys) {
            $value = $this->input($section);
            if (is_array($value) && array_diff(array_keys($value), $keys) !== []) {
                $validator->errors()->add($section, 'Unknown homepage fields are not allowed.');
            }
        }
    }

    private function validatePlainText(Validator $validator): void
    {
        foreach ($this->all() as $section => $value) {
            if (! is_array($value)) {
                continue;
            }

            $this->walkScalars($value, $section, function (string $path, mixed $scalar) use ($validator) {
                if (! is_string($scalar)) {
                    return;
                }

                if (strip_tags($scalar) !== $scalar || preg_match('/javascript|vbscript|data:text|on[a-z]+\s*=|<|>/i', $scalar)) {
                    $validator->errors()->add($path, 'HTML, scripts, and event handlers are not allowed.');
                }
            });
        }
    }

    private function validateUrls(Validator $validator): void
    {
        foreach ([
            'hero.primary' => ['hero.primary_route', 'hero.primary_url'],
            'hero.secondary_button' => ['hero.secondary_button.route', 'hero.secondary_button.url'],
            'services_intro.cta' => ['services_intro.cta_route', 'services_intro.cta_url'],
            'portfolio_intro.cta' => ['portfolio_intro.cta_route', 'portfolio_intro.cta_url'],
            'blog_intro.cta' => ['blog_intro.cta_route', 'blog_intro.cta_url'],
            'primary_cta.primary' => ['primary_cta.primary_route', 'primary_cta.primary_url'],
            'primary_cta.secondary_button' => ['primary_cta.secondary_button.route', 'primary_cta.secondary_button.url'],
        ] as $label => [$routePath, $urlPath]) {
            $route = data_get($this->all(), $routePath);
            $url = data_get($this->all(), $urlPath);

            if (($route === null || $route === '') && ($url === null || $url === '')) {
                $validator->errors()->add($label, 'A route or URL is required.');
                continue;
            }

            if ($url !== null && $url !== '' && ! $this->isSafeUrl($url)) {
                $validator->errors()->add($urlPath, 'Unsafe URL schemes are not allowed.');
            }
        }
    }

    private function validateSvgPaths(Validator $validator): void
    {
        foreach ([
            'difference.cards' => $this->input('difference.cards', []),
            'services_intro.card_icons' => $this->input('services_intro.card_icons', []),
        ] as $prefix => $items) {
            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $index => $item) {
                $path = $prefix === 'difference.cards' ? ($item['icon_path'] ?? null) : $item;
                if (! is_string($path) || ! $this->isSafeSvgPath($path)) {
                    $validator->errors()->add("{$prefix}.{$index}", 'Only safe SVG path data is allowed.');
                }
            }
        }
    }

    private function validateStats(Validator $validator): void
    {
        foreach ($this->input('stats.items', []) as $index => $item) {
            $value = $item['value'] ?? null;
            $sep = $item['sep'] ?? '';

            if (! is_string($value) || ! preg_match('/^[0-9][0-9,.]{0,18}$/', $value)) {
                $validator->errors()->add("stats.items.{$index}.value", 'Stat values must be numeric or numeric-like text.');
            }

            if ($sep !== null && $sep !== '' && (! is_string($sep) || ! preg_match('/^[A-Za-z]{1,5}$/', $sep))) {
                $validator->errors()->add("stats.items.{$index}.sep", 'The stat separator must be short alphabetic text.');
            }
        }
    }

    private function walkScalars(array $value, string $prefix, callable $callback): void
    {
        foreach ($value as $key => $item) {
            $path = "{$prefix}.{$key}";
            if (is_array($item)) {
                $this->walkScalars($item, $path, $callback);
                continue;
            }

            $callback($path, $item);
        }
    }

    private function isSafeUrl(mixed $url): bool
    {
        if (! is_string($url)) {
            return false;
        }

        $url = trim($url);

        return $url === '/'
            || str_starts_with($url, '/')
            || str_starts_with($url, 'http://')
            || str_starts_with($url, 'https://');
    }

    private function isSafeSvgPath(string $path): bool
    {
        $path = trim($path);

        return $path !== ''
            && preg_match('/^[A-Za-z0-9\s,.\-]+$/', $path)
            && ! preg_match('/[<>"\';]|javascript|onload|style/i', $path);
    }

    private function payloadRouteValue(?string $route): ?string
    {
        return in_array($route, self::ROUTES, true) ? $route : null;
    }

    private function urlValue(?string $route, ?string $url, string $fallbackRoute): string
    {
        if (in_array($route, self::ROUTES, true)) {
            return $this->routePath($route);
        }

        return is_string($url) && $this->isSafeUrl($url) ? trim($url) : $this->routePath($fallbackRoute);
    }

    private function routePath(string $route): string
    {
        return match ($route) {
            'home' => '/',
            'portfolio' => '/portfolio',
            'blog' => '/blog',
            'contact' => '/contact',
            default => '/services',
        };
    }

    /**
     * @param array<int|string, mixed> $items
     * @return array<int, mixed>
     */
    private function orderedList(array $items): array
    {
        ksort($items, SORT_NUMERIC);

        return array_values($items);
    }
}
