# Phase 4G: Homepage Portfolio Intro And Dynamic Portfolio Cards Integration

Date: 2026-07-04

Scope: connect only the homepage Portfolio preview section shell to `$homeSections->get('portfolio_intro')` and use the existing `$portfolios` domain collection for live cards. Blog, final CTA, header, footer, SEO, and previous homepage sections remain unchanged.

## Files Changed

- `resources/views/public/home.blade.php`
- `tests/Feature/HomePortfolioIntroBladeIntegrationTest.php`
- `docs/PHASE_4G_HOME_PORTFOLIO_INTEGRATION.md`

No controller, route, model, migration, seeder, admin, portfolio backend, blog, final CTA, header, footer, SEO, or database record was changed.

## Portfolio Intro Fields Made Dynamic

The Portfolio preview section now reads from:

```php
$portfolioIntroSection = $homeSections->get('portfolio_intro');
```

Dynamic intro fields:

- Eyebrow
- Title
- Subtitle
- Section CTA label
- Section CTA URL
- Card link label

## Existing PortfolioProject Source Preserved

Portfolio cards now use the existing `$portfolios` collection passed by `HomeController`.

Project records remain owned by the Portfolio module. The page section payload does not provide project titles, slugs, categories, descriptions, results, technologies, featured status, ordering, or publish status.

## Previous Hard-Coded-Only Behavior

Before this phase, the homepage always rendered the local `$demoPortfolio` cards and ignored the `$portfolios` collection already passed by the controller.

## New Live-Record And Fallback Behavior

- If `$portfolios` contains records, the homepage renders up to the first three in the existing controller order.
- If `$portfolios` is missing or empty, the homepage renders the existing three demo fallback cards.
- Live cards and fallback cards are not mixed.
- Page-section payload cannot control project count or ordering.

## Exact Fallback Projects Preserved

The fallback projects remain:

- `E-Commerce Google Ads Overhaul`
- `GTM + Meta CAPI Server Tracking`
- `Laravel SaaS Landing Page`

Their existing categories, descriptions, result text, technologies, order, link behavior, and card markup were preserved.

## Field Compatibility Mapping

Live portfolio cards map these existing domain fields into the unchanged homepage card markup:

- `title`
- `slug`
- `category_label`, falling back to a display label from `category`
- `short_description`, falling back to `description` for controlled test objects
- `result_summary`, falling back to `result` for controlled test objects
- `technologies`

Card URLs use `route('portfolio.show', $slug)` when a live slug exists, otherwise `route('portfolio')`.

## Category Handling

The integration prefers the existing `PortfolioProject::category_label` accessor. If a controlled object does not expose that accessor, the local display fallback formats the existing `category` value.

No category tables, category storage, filtering, or admin logic were changed.

## Technology Validation

Technologies continue to come only from the portfolio record.

The homepage accepts arrays or safely decoded JSON strings, renders only scalar plain-text labels, limits visible homepage tags to three, and rejects nested structures, HTML, scripts, URLs, class fragments, and style fragments.

All technology output is escaped.

## URL Safety

The section CTA prefers `route('portfolio')` for the seeded `/portfolio` value.

Direct section CTA URLs are accepted only when they are `/`, relative paths beginning with `/`, or `http://` / `https://` URLs. Unsafe schemes such as `javascript:`, `data:`, and `vbscript:` fall back to `route('portfolio')`.

Live project cards use the existing `portfolio.show` route and domain slugs.

## Card-Count Behavior

- More than three live portfolio records: only the first three render.
- One or two live portfolio records: only those live records render.
- Empty live collection: all three fallback demo projects render.
- `page_sections` payload cannot add, remove, or reorder portfolio cards.

## Test Commands And Results

Commands run:

```powershell
php artisan test tests\Feature\HomePortfolioIntroBladeIntegrationTest.php
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

- `HomePortfolioIntroBladeIntegrationTest`: passed, 6 tests, 58 assertions.
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

The prior homepage sections remain covered by their focused tests and passed after this phase:

- Hero
- Stats
- Trust bar
- Difference
- Services intro

Unchanged later homepage sections:

- Blog preview
- Final CTA

Header, footer, and SEO rendering were not modified.

## Database Confirmation

No migrations or seeders were run against local MySQL in this phase. No database records were inserted, updated, or deleted.

## Manual Visual Comparison Checklist

Before advancing, compare the Portfolio preview section against the previous render:

- Eyebrow reads `Our Work`.
- Title reads `Recent Projects`.
- Subtitle text and wrapping remain consistent.
- Section CTA reads `All Projects` and links to Portfolio.
- Live portfolio records render when provided.
- Empty portfolios still render the three existing demo fallback cards.
- Card link label reads `View Case Study`.
- Category badge, result badge, technology tags, placeholder icon, spacing, hover behavior, and responsive grid remain unchanged.
- One or two live records do not mix with fallback cards.
- More than three live records do not render extra cards.

## Next-Phase Recommendation

Continue with the Blog preview section in another narrow Blade-only pass, preserving the existing blog domain data source, local fallbacks, payload validation, and focused tests.
