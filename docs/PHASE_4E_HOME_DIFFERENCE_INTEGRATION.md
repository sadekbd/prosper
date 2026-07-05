# Phase 4E: Homepage Difference Blade Integration

Date: 2026-07-04

Scope: connect only the homepage Difference section to `$homeSections->get('difference')`. Hero, stats, and trust bar remain database-driven from prior phases, and all later homepage sections remain unchanged.

## Files Changed

- `resources/views/public/home.blade.php`
- `tests/Feature/HomeDifferenceBladeIntegrationTest.php`
- `docs/PHASE_4E_HOME_DIFFERENCE_INTEGRATION.md`

No controller, route, model, migration, seeder, admin, settings, header, footer, SEO, service, portfolio, blog, final CTA, or database record was changed.

## Dynamic Heading Fields

The Difference heading now reads from:

```php
$differenceSection = $homeSections->get('difference');
```

Dynamic heading fields:

- Eyebrow
- Title
- Subtitle

## Dynamic Card Fields

Cards are read from:

```php
$differenceSection?->payload['cards'];
```

Dynamic card fields:

- Badge
- Color key
- Icon path
- Title
- Body

The existing card loop, grid structure, SVG element, Tailwind classes, spacing, hover behavior, and responsive breakpoints were preserved.

## Three-Card Fallback Strategy

The original three cards are defined once in the Blade Difference integration block. If the record is missing, inactive, malformed, or the first three payload positions do not produce three valid cards, the complete fallback set is rendered.

The section always renders exactly three cards in this order:

- `No. 01` / `Tech-First Marketing`
- `No. 02` / `ROI Focused`
- `No. 03` / `Transparent Data`

## Color Mapping

Approved color keys:

- `cyan`
- `gold`

The Blade maps those keys only through the existing fixed conditional Tailwind classes:

- Cyan paths use the existing `text-pm-cyan`, `text-pm-cyan/5`, and `bg-pm-cyan/10` branches.
- Gold paths use the existing `text-pm-gold`, `text-pm-gold/5`, and `bg-pm-gold/10` branches.

Raw class names from payload are never rendered.

## SVG Path Validation

Icon paths are accepted only when they contain normal SVG path characters:

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

Invalid icon paths fall back to the original icon path for the same card position.

## Escaping And Payload Validation

Each card is validated before rendering:

- `badge`: plain text, non-empty, max 20 characters.
- `color`: `cyan` or `gold`.
- `icon_path`: safe SVG path string.
- `title`: plain text, non-empty, max 100 characters.
- `body`: plain text, non-empty, max 1000 characters.

Badge, title, and body are rendered with escaped Blade syntax. Payload HTML, scripts, URLs, inline styles, event handlers, and arbitrary class names are not accepted.

## Count And Order Stability

Only the first three payload card positions are considered.

- More than three valid cards: extra cards are ignored.
- Fewer than three valid cards in the first three positions: the full fallback set is rendered.
- Malformed data cannot change the grid or rendered card count.

## Test Commands And Results

Commands run:

```powershell
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

Hero behavior remains covered by `HomeHeroBladeIntegrationTest` and passed after this phase.

Stats behavior remains covered by `HomeStatsBladeIntegrationTest` and passed after this phase.

Trust bar behavior remains covered by `HomeTrustBarBladeIntegrationTest` and passed after this phase.

Unchanged later homepage sections:

- Services preview
- Portfolio preview
- Blog preview
- Final CTA

Header, footer, and SEO rendering were not modified.

## Database Confirmation

No migrations or seeders were run against local MySQL in this phase. No database records were inserted, updated, or deleted.

## Manual Visual Comparison Checklist

Before advancing to the next phase, compare the Difference section against the previous render:

- Eyebrow reads `Why Choose Us`.
- Title reads `The Prosper Media Difference`.
- Subtitle text and line wrapping remain visually consistent.
- Three cards render in the same order.
- Card badges, titles, and body copy match.
- Cyan/gold card accents match the previous colors.
- SVG icons match the previous shapes.
- Watermark numbers, icon boxes, badges, card spacing, hover behavior, and responsive grid remain unchanged.
- No fourth card appears if payload contains extra entries.

## Next-Phase Recommendation

Continue with the next homepage section in another narrow Blade-only pass, preserving local fallbacks, fixed class mappings, payload validation, and focused tests before connecting additional sections.
