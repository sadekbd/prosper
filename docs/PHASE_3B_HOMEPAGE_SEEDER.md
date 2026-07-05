# Phase 3B: Homepage Page Sections Seeder

Date: 2026-07-04

Scope: create seed data only for the eight documented homepage `page_sections` records. The homepage is not connected to `page_sections` in this phase.

## Files Created

- `database/seeders/HomePageSectionSeeder.php`
- `tests/Feature/HomePageSectionSeederTest.php`
- `docs/PHASE_3B_HOMEPAGE_SEEDER.md`

## Exact Records

The seeder writes only records with `page_key = home`:

| Sort | Section key | Status |
| ---: | --- | --- |
| 10 | `hero` | `active` |
| 20 | `stats` | `active` |
| 30 | `trust_bar` | `active` |
| 40 | `difference` | `active` |
| 50 | `services_intro` | `active` |
| 60 | `portfolio_intro` | `active` |
| 70 | `blog_intro` | `active` |
| 80 | `primary_cta` | `active` |

Visible content, labels, URLs, colors, icon paths, and section order were copied from `docs/PHASE_3A_HOMEPAGE_CONTENT_MAPPING.md` and re-checked against `resources/views/public/home.blade.php`.

## Idempotency

`HomePageSectionSeeder` uses `PageSection::updateOrCreate()` with this identifying key pair:

- `page_key`
- `section_key`

The `page_sections` migration also has a unique index on `page_key` and `section_key`, so repeated runs update the same eight records instead of creating duplicates.

## Payload Design

The payloads keep only content and presentation metadata needed to reproduce the current homepage sections:

- Hero title lines, secondary CTA, dashboard text, dashboard metrics, chart labels, tracking tags, and floating badge text.
- Stats repeated values and labels.
- Trust-bar item labels and colors.
- Difference card badges, colors, icon paths, titles, and body copy.
- Service intro card link label and the three existing presentation icon paths.
- Portfolio intro card link label.
- Blog intro card link label.
- Final CTA title lines, secondary CTA, and proof points.

The Phase 3A document proposed `dynamic_source` metadata for services, portfolio, and blog intros. Phase 3B deliberately excludes that implementation metadata because public editors do not need model class names, controller variable names, scopes, limits, or audit notes inside production payload JSON.

## Deliberately Excluded

The seeder does not copy these domain card records into `page_sections`:

- Service cards such as `Google Ads Mastery`, `Advanced Conversion Tracking`, and `Professional Web Development`.
- Portfolio cards such as `E-Commerce Google Ads Overhaul`, `GTM + Meta CAPI Server Tracking`, and `Laravel SaaS Landing Page`.
- Blog article cards such as `How to Set Up Server-Side Conversion Tracking in 2025`, `Server-Side vs Client-Side Tracking: The Complete Comparison`, and `Building High-Converting Laravel Landing Pages That Actually Convert`.

Those records remain owned by the existing services, portfolio, and blog modules.

## Targeted Test

Created `tests/Feature/HomePageSectionSeederTest.php`.

The test:

- Forces SQLite `:memory:`.
- Runs only `2026_07_04_000001_create_page_sections_table.php`.
- Runs `HomePageSectionSeeder` twice.
- Confirms exactly eight `home` records exist.
- Confirms every expected section key exists in the expected order.
- Confirms all sort orders, active statuses, and array payloads.
- Confirms representative exact values for hero, stats, trust bar, difference cards, and final CTA.
- Confirms service, portfolio, and blog domain card records were not copied into `page_sections`.
- Confirms no duplicate `page_key` plus `section_key` records exist.

Test command:

```powershell
php artisan test tests\Feature\HomePageSectionSeederTest.php
```

Diff whitespace command:

```powershell
git diff --check
```

## Local Deployment Command For Next Phase

Do not run this in Phase 3B. Proposed command for a later approved local deployment phase:

```powershell
php artisan db:seed --class=HomePageSectionSeeder
```

Do not run `php artisan prosper:install`.

## Rollback And Content Removal

To remove only these seeded homepage section records in a future approved database phase:

```sql
DELETE FROM page_sections
WHERE page_key = 'home'
  AND section_key IN (
    'hero',
    'stats',
    'trust_bar',
    'difference',
    'services_intro',
    'portfolio_intro',
    'blog_intro',
    'primary_cta'
  );
```

Because the seeder is idempotent, re-running the approved seed command later will recreate or update the same eight records.

## No-Change Confirmation

This phase did not change:

- `DatabaseSeeder.php`
- `HomeController`
- Blade views
- Routes
- Admin UI
- Existing services, portfolio, blog, settings, header, footer, or SEO code
- Existing domain records

The seeder was not registered globally and was not run against local MySQL.
