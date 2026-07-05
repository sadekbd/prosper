# Phase 4F: Homepage Services Intro Blade Integration

Date: 2026-07-04

Scope: connect only the homepage Services preview section shell to `$homeSections->get('services_intro')`. Existing service cards remain sourced from the services domain data or the current Blade fallback records.

## Files Changed

- `resources/views/public/home.blade.php`
- `tests/Feature/HomeServicesIntroBladeIntegrationTest.php`
- `docs/PHASE_4F_HOME_SERVICES_INTRO_INTEGRATION.md`

No controller, route, model, migration, seeder, admin, service backend, portfolio, blog, final CTA, header, footer, SEO, or database record was changed.

## Services Intro Fields Made Dynamic

The Services preview section now reads from:

```php
$servicesIntroSection = $homeSections->get('services_intro');
```

Dynamic section fields:

- Eyebrow
- Title
- Subtitle
- Section CTA label
- Section CTA URL
- Card link label
- Presentation icon paths by card position

## Existing Service Domain Source Preserved

Service cards still come from the existing `$services` collection:

```php
$coreServices = isset($services) && $services->count() > 0
    ? $services->take(3)
    : collect([...existing fallback services...]);
```

The integration does not copy service titles, subtitles, descriptions, slugs, features, statuses, or ordering into `page_sections`.

The existing fallback service records remain in Blade:

- `Google Ads Mastery`
- `Advanced Conversion Tracking`
- `Professional Web Development`

## Section Fallback Strategy

If `services_intro` is missing, inactive, or malformed, the section falls back to:

- Eyebrow: `What We Do`
- Title: `Our Core Services`
- Subtitle: `Three pillars of technical excellence powering your entire digital growth engine.`
- CTA label: `View All Services`
- CTA route: `services`
- Card link label: `Learn More`
- Current live presentation icon paths

The current rendered output is preserved.

## Card-Link Label Validation

The card link label must be:

- Plain text
- Non-empty
- Maximum 40 characters

Invalid labels fall back to `Learn More`. Output uses escaped Blade syntax.

## CTA URL Safety

The section CTA prefers the known internal `services` route.

Direct URLs are accepted only when they begin with:

- `/`
- `http://`
- `https://`

Unsafe schemes such as `javascript:`, `data:`, and `vbscript:` fall back to `route('services')`.

## SVG Icon Validation

Presentation icons are accepted only as SVG path data containing:

- Letters
- Digits
- Spaces
- Commas
- Periods
- Minus signs

Rejected icon content includes:

- `<`
- `>`
- Quotes
- Semicolons
- `javascript`
- `onload`
- `style`

Invalid icon paths fall back to the original icon for the same card position. Raw SVG markup from the database is never rendered.

## Test Commands And Results

Commands run:

```powershell
php artisan test tests\Feature\HomeServicesIntroBladeIntegrationTest.php
php artisan test tests\Feature\HomeDifferenceBladeIntegrationTest.php
php artisan test tests\Feature\HomeTrustBarBladeIntegrationTest.php
php artisan test tests\Feature\HomeStatsBladeIntegrationTest.php
php artisan test tests\Feature\HomeHeroBladeIntegrationTest.php
php artisan test tests\Feature\HomeControllerPageSectionsTest.php
php artisan test tests\Feature\HomePageSectionSeederTest.php
php artisan test tests\Feature\PageSectionSchemaTest.php
git diff --check
```

Results:

- `HomeServicesIntroBladeIntegrationTest`: passed, 5 tests, 40 assertions.
- `HomeDifferenceBladeIntegrationTest`: passed, 5 tests, 93 assertions.
- `HomeTrustBarBladeIntegrationTest`: passed, 5 tests, 91 assertions.
- `HomeStatsBladeIntegrationTest`: passed, 4 tests, 54 assertions.
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

## Behavior Confirmation

Hero, stats, trust bar, and Difference behavior remain covered by their focused tests and passed after this phase.

Unchanged later homepage sections:

- Portfolio preview
- Blog preview
- Final CTA

Header, footer, and SEO rendering were not modified.

## Database Confirmation

No migrations or seeders were run against local MySQL in this phase. No database records were inserted, updated, or deleted.

## Manual Visual Comparison Checklist

Before advancing to the next phase, compare the Services preview section against the previous render:

- Eyebrow reads `What We Do`.
- Title reads `Our Core Services`.
- Subtitle text and wrapping remain consistent.
- Section CTA reads `View All Services` and links to Services.
- Service cards still render from active service records when provided.
- Empty services still render the three existing fallback service cards.
- Card link label reads `Learn More`.
- Icon wrappers, SVGs, card spacing, hover behavior, and responsive grid remain unchanged.
- Page-section payload cannot add or reorder service cards.

## Next-Phase Recommendation

Continue with the next homepage section in another narrow Blade-only pass, preserving domain data boundaries, local fallbacks, payload validation, and focused tests.
