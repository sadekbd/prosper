# Phase 5B: Homepage Normalization Extraction

Date: 2026-07-06

Scope: behavior-preserving extraction of homepage section defaults and normalization from `resources/views/public/home.blade.php` into a reusable support class. No admin CRUD was implemented in this phase.

## Files Created And Changed

Created:

- `app/Support/HomepageContent.php`
- `tests/Unit/HomepageContentTest.php`
- `docs/PHASE_5B_HOMEPAGE_NORMALIZATION_EXTRACTION.md`

Changed:

- `resources/views/public/home.blade.php`

No controller, route, model, migration, seeder, admin view, sidebar, or database record was changed.

## Support Class Responsibility

`App\Support\HomepageContent` now owns the homepage content defaults and safety normalization previously embedded in the Blade view.

It does not query the database, mutate models, or change record ordering. It receives existing `PageSection` instances and existing domain collections from the view and returns normalized arrays for rendering.

## Public Methods

Section methods:

- `hero(?PageSection $section): array`
- `stats(?PageSection $section): array`
- `trustBar(?PageSection $section): array`
- `difference(?PageSection $section): array`
- `servicesIntro(?PageSection $section): array`
- `portfolioIntro(?PageSection $section): array`
- `blogIntro(?PageSection $section): array`
- `primaryCta(?PageSection $section): array`

Domain card helpers:

- `serviceCards($services): array`
- `portfolioCards($portfolios): array`
- `blogCards($articles): array`

Static metadata:

- `SECTION_KEYS`
- `SORT_ORDERS`

Defaults:

- `defaults(): array`

## Default Source Decision

Defaults were copied from the current tested homepage Blade behavior and checked against `HomePageSectionSeeder`.

The extraction intentionally preserves existing content and payload shapes. It does not rewrite visible copy, URLs, title lines, labels, SVG paths, colors, fixed counts, or domain fallback card content.

## Shared Validation Methods

The support class centralizes:

- active-section handling
- payload array extraction
- plain text validation
- safe URL and known-route fallback handling
- SVG path validation
- metric color allowlist
- trust-bar color allowlist
- chart-height validation
- stat suffix validation
- fixed-count normalization
- portfolio technology validation
- blog date formatting
- live/fallback card selection

## Blade Logic Removed

The homepage Blade no longer contains the previous repeated local implementations of:

- `strip_tags` validation checks
- URL safety closures
- route fallback closures
- SVG path safety checks
- metric color allowlist checks
- trust-bar color allowlist checks
- fixed-count list normalization
- chart-height validation
- stat suffix validation
- portfolio technology decoding/filtering
- blog date formatting
- repeated fallback arrays for section content

The Blade now asks `HomepageContent` for normalized arrays such as:

- `$heroContent`
- `$statsContent`
- `$trustBarContent`
- `$differenceContent`
- `$servicesIntroContent`
- `$portfolioIntroContent`
- `$blogIntroContent`
- `$primaryCtaContent`

## Blade Logic Intentionally Retained

The Blade still owns presentation:

- HTML structure
- Tailwind classes
- responsive layout
- section order
- SVG wrapper markup
- button markup
- card markup
- loops over already-normalized arrays
- route links for service card detail routes

The repeated KPI/chart loop shells were left in place to avoid changing public markup.

## Domain Boundaries

Domain records remain outside `page_sections`:

- Services still come from `$services` or the existing service fallback cards.
- Portfolio cards still come from `$portfolios` or the existing portfolio fallback cards.
- Blog cards still come from `$articles` or the existing blog fallback cards.

The support class normalizes display arrays only. It does not copy service, portfolio, or blog records into page-section defaults.

## Query Count Confirmation

`HomeController` was not modified.

The homepage still performs:

- one `page_sections` query
- the existing services query
- the existing guarded portfolio query
- the existing guarded blog query

No Eloquent queries were moved into `HomepageContent`.

## Test Commands And Results

Commands run:

```powershell
php artisan test tests\Unit\HomepageContentTest.php
php artisan test tests\Feature\HomeHeroBladeIntegrationTest.php
php artisan test tests\Feature\HomeStatsBladeIntegrationTest.php
php artisan test tests\Feature\HomeTrustBarBladeIntegrationTest.php
php artisan test tests\Feature\HomeDifferenceBladeIntegrationTest.php
php artisan test tests\Feature\HomeServicesIntroBladeIntegrationTest.php
php artisan test tests\Feature\HomePortfolioIntroBladeIntegrationTest.php
php artisan test tests\Feature\HomeBlogIntroBladeIntegrationTest.php
php artisan test tests\Feature\HomePrimaryCtaBladeIntegrationTest.php
php artisan test tests\Feature\HomeControllerPageSectionsTest.php
php artisan test tests\Feature\HomePageSectionSeederTest.php
php artisan test tests\Feature\PageSectionSchemaTest.php
php artisan test
git diff --check
```

Results:

- `HomepageContentTest`: passed, 8 tests, 47 assertions.
- `HomeHeroBladeIntegrationTest`: passed, 3 tests, 45 assertions.
- `HomeStatsBladeIntegrationTest`: passed, 4 tests, 54 assertions.
- `HomeTrustBarBladeIntegrationTest`: passed, 5 tests, 91 assertions.
- `HomeDifferenceBladeIntegrationTest`: passed, 5 tests, 93 assertions.
- `HomeServicesIntroBladeIntegrationTest`: passed, 5 tests, 40 assertions.
- `HomePortfolioIntroBladeIntegrationTest`: passed, 6 tests, 58 assertions.
- `HomeBlogIntroBladeIntegrationTest`: passed, 6 tests, 59 assertions.
- `HomePrimaryCtaBladeIntegrationTest`: passed, 5 tests, 94 assertions.
- `HomeControllerPageSectionsTest`: passed, 2 tests, 38 assertions.
- `HomePageSectionSeederTest`: passed, 1 test, 53 assertions.
- `PageSectionSchemaTest`: passed, 2 tests, 24 assertions.
- Full suite: passed, 54 tests, 705 assertions.
- `git diff --check`: no whitespace errors.

Warnings:

```text
warning: in the working copy of 'app/Http/Controllers/Public/HomeController.php', CRLF will be replaced by LF the next time Git touches it
warning: in the working copy of 'resources/views/public/home.blade.php', CRLF will be replaced by LF the next time Git touches it
```

These are the existing line-ending warnings and were not addressed in this phase.

## Visual Comparison Result

The rendered homepage Blade was exercised through the focused homepage integration tests after extraction. Those tests verify the representative visible output, section counts, URLs, fallback behavior, unsafe-value rejection, live/fallback domain-card behavior, and route-safe rendering for every dynamic homepage section.

No screenshots were generated. No local asset build was run.

Manual source comparison confirmed:

- HTML structure remains in `home.blade.php`.
- Tailwind class strings remain in the Blade markup.
- Controller-provided variables remain unchanged.
- Section loops still render the same fixed counts.
- Domain card live/fallback behavior is still selected at render time.

## Payload Structures

Payload structures were not changed.

The support class reads the existing shapes:

- `hero.payload.title_lines`
- `hero.payload.secondary_button`
- `hero.payload.dashboard`
- `stats.payload.items`
- `trust_bar.payload.items`
- `difference.payload.cards`
- `services_intro.payload.card_link_label`
- `services_intro.payload.card_icons`
- `portfolio_intro.payload.card_link_label`
- `blog_intro.payload.card_link_label`
- `primary_cta.payload.title_lines`
- `primary_cta.payload.secondary_button`
- `primary_cta.payload.proof_points`

No new payload keys were introduced.

## Database Confirmation

No migrations or seeders were run.

No local MySQL records were inserted, updated, or deleted.

`php artisan prosper:install` was not run.

## Recommended Next Phase

Proceed to admin CRUD implementation in a narrow phase:

1. Add `UpdateHomepageRequest` using the same safety rules now centralized in `HomepageContent`.
2. Add `Admin\HomepageController`.
3. Add `/admin/homepage` edit/update routes behind `admin.auth` and `admin.admin`.
4. Add the admin edit Blade view using the existing dark admin form patterns.
5. Add sidebar integration for `super_admin` and `admin`.
6. Add focused admin CRUD tests before any local database write.
