# Phase 4B: Homepage Hero Blade Integration

Date: 2026-07-04

Scope: connect only the homepage hero section to `$homeSections->get('hero')`. All other homepage sections remain rendered from their existing Blade content.

## Files Changed

- `resources/views/public/home.blade.php`
- `tests/Feature/HomeHeroBladeIntegrationTest.php`
- `docs/PHASE_4B_HOME_HERO_INTEGRATION.md`

No controller, route, model, migration, seeder, admin, settings, header, footer, SEO, service, portfolio, blog, or database record was changed.

## Hero Fields Made Dynamic

The hero section now reads editable content from:

```php
$hero = $homeSections->get('hero');
```

Dynamic hero values:

- Eyebrow
- Title lines
- Subtitle
- Primary CTA label and URL
- Secondary CTA label and URL
- Dashboard eyebrow
- Dashboard title
- Dashboard status
- Dashboard metrics
- Chart label
- Chart day labels
- Chart bar heights
- Tracking label
- Tracking items
- Floating badges

The existing HTML structure, Tailwind classes, SVG markup, spacing, gradients, responsive behavior, animation classes, dashboard layout, and visual hierarchy were preserved.

## Fallback Strategy

Fallbacks are defined locally in the Blade hero integration block. If `$homeSections` is missing, empty, inactive, or does not contain `hero`, the hero renders the original hard-coded content.

Fallback content was not moved into `HomeController`.

## URL Safety

Known internal hero links prefer named routes:

- Primary CTA: `services`
- Secondary CTA: `contact`

If a URL is used directly, the Blade helper accepts only `/`, `https://`, or `http://` URLs. Unsafe strings such as `javascript:` fall back to the known safe route URL.

## Color-Class Allowlist

Dashboard metric color classes are restricted to:

- `text-pm-cyan`
- `text-pm-gold`
- `text-white`

Invalid metric color values fall back to the original class for that metric.

## Chart-Height Validation

Chart bar heights are accepted only when numeric and between `0` and `100`.

Invalid values fall back per bar to the original heights:

```php
[35, 55, 42, 70, 60, 85, 75]
```

## Test Commands And Results

Commands run:

```powershell
php artisan test tests\Feature\HomeHeroBladeIntegrationTest.php
php artisan test tests\Feature\HomeControllerPageSectionsTest.php
php artisan test tests\Feature\HomePageSectionSeederTest.php
php artisan test tests\Feature\PageSectionSchemaTest.php
git diff --check
```

Results:

- `HomeHeroBladeIntegrationTest`: passed, 3 tests, 45 assertions.
- `HomeControllerPageSectionsTest`: passed, 2 tests, 38 assertions.
- `HomePageSectionSeederTest`: passed, 1 test, 53 assertions.
- `PageSectionSchemaTest`: passed, 2 tests, 24 assertions.
- `git diff --check`: no whitespace errors.

Warnings:

```text
warning: in the working copy of 'app/Http/Controllers/Public/HomeController.php', CRLF will be replaced by LF the next time Git touches it
warning: in the working copy of 'resources/views/public/home.blade.php', CRLF will be replaced by LF the next time Git touches it
```

## Hero-Only Confirmation

Only the hero portion of `resources/views/public/home.blade.php` was changed.

Unchanged homepage sections:

- Stats row content
- Trust bar
- Difference
- Services preview
- Portfolio preview
- Blog preview
- Final CTA

Header, footer, and SEO rendering were not modified.

## Database Confirmation

No migrations or seeders were run against local MySQL in this phase. No database records were inserted, updated, or deleted.

## Manual Visual Comparison Checklist

Before advancing to the next phase, compare the rendered homepage hero against the previous hard-coded hero:

- Eyebrow text and badge styling match.
- Hero line breaks remain: `Engineering Digital`, highlighted `Success` with `with`, then `Technical Precision.`
- Subtitle wraps naturally without layout shift.
- Primary and secondary CTA labels, links, classes, and spacing match.
- Hero stats row remains unchanged.
- Dashboard card position, border, glow, and animation match.
- Dashboard title, status, KPI cards, chart bars, day labels, tracking tags, and floating badges match.
- Desktop-only dashboard behavior remains unchanged.
- Mobile hero layout remains unchanged.

## Next-Phase Recommendation

Integrate the next static homepage section in a similarly narrow pass, one section at a time, with local Blade fallbacks and focused tests before changing any additional rendered content.
