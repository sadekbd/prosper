# Phase 5C: Homepage Admin CRUD Implementation

Date: 2026-07-06

Scope: implement a fixed admin editor for the eight homepage `page_sections` records with isolated SQLite tests. No local MySQL homepage records were updated.

## Files Created And Changed

Created:

- `app/Http/Requests/Admin/UpdateHomepageRequest.php`
- `app/Http/Controllers/Admin/HomepageController.php`
- `resources/views/admin/homepage/edit.blade.php`
- `tests/Feature/Admin/HomepageAdminTest.php`
- `tests/Feature/Admin/HomepageUpdateValidationTest.php`
- `docs/PHASE_5C_HOMEPAGE_ADMIN_CRUD_IMPLEMENTATION.md`

Changed:

- `routes/admin.php`
- `resources/views/components/admin/sidebar.blade.php`

No public homepage rendering, `HomepageContent` payload structures, `PageSection` migration, `HomePageSectionSeeder`, service, portfolio, blog, settings, header, footer, or SEO code was changed in this phase.

## Routes And Middleware

Added under the existing authenticated admin group:

```php
GET /admin/homepage
name: admin.homepage.edit
middleware: admin.auth, admin.admin

PUT /admin/homepage
name: admin.homepage.update
middleware: admin.auth, admin.admin
```

Controller:

```php
App\Http\Controllers\Admin\HomepageController
```

`php artisan route:list` confirms:

- `admin/homepage` `GET|HEAD` -> `admin.homepage.edit`
- `admin/homepage` `PUT` -> `admin.homepage.update`

## Authorization Behavior

Access is controlled by existing middleware:

- guest: redirected to `admin.login`
- `super_admin`: allowed
- `admin`: allowed
- `article_writer`: forbidden

The Form Request returns `true` for `authorize()` and relies on route middleware, matching the project convention.

## Request Validation Architecture

Created one full-form request:

```php
App\Http\Requests\Admin\UpdateHomepageRequest
```

It validates all eight fixed section roots:

- `hero`
- `stats`
- `trust_bar`
- `difference`
- `services_intro`
- `portfolio_intro`
- `blog_intro`
- `primary_cta`

Validation rejects:

- `page_key`
- `section_key`
- `sort_order`
- `status`
- raw HTML
- scripts
- event handlers
- unsafe URL schemes
- unsafe SVG path content
- arbitrary metric classes
- invalid color keys
- invalid trust-bar colors
- invalid stat suffixes
- incorrect fixed item counts
- unknown top-level section fields where practical
- submitted domain card data such as portfolio projects inside the page-section form

Valid punctuation and current content characters such as arrows, apostrophes, and em dashes are accepted.

## Controller Transaction Behavior

`HomepageController@edit`:

- loads `home` page sections with one query
- orders by existing `ordered()` scope
- keys by `section_key`
- uses `HomepageContent` to populate normalized defaults for missing records
- does not create records during `GET`
- renders `admin.homepage.edit`

`HomepageController@update`:

- uses only validated request data
- transforms fields into existing `page_sections` columns and payload shapes
- writes only the eight fixed records
- forces `page_key = home`
- forces `status = active`
- preserves fixed sort orders 10 through 80
- uses `PageSection::updateOrCreate()` by `page_key` and `section_key`
- wraps all eight writes and activity logging in one `DB::transaction()`
- redirects to `admin.homepage.edit`
- flashes `Homepage content updated successfully.`

An isolated test uses a SQLite trigger to force one section write failure and confirms the transaction rolls back earlier section updates.

## Field To Payload Transformation

Top-level columns:

- `eyebrow`
- `title`
- `subtitle`
- `body`
- `button_label`
- `button_url`
- `image`
- `payload`
- `status`
- `sort_order`

Payload shapes remain unchanged:

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

Known route selections store homepage-compatible paths in top-level `button_url` fields:

- `home` -> `/`
- `services` -> `/services`
- `portfolio` -> `/portfolio`
- `blog` -> `/blog`
- `contact` -> `/contact`

Payload button objects keep `route` only when a known route is selected and keep `url` as the safe stored path or direct safe URL.

## Admin UI Structure

Created:

```text
resources/views/admin/homepage/edit.blade.php
```

The view reuses the existing admin layout, dark cards, form control classes, flash-message layout, validation error display, and Alpine.js.

UI structure:

- one `PUT` form
- eight tabs:
  - Hero
  - Stats
  - Trust Bar
  - Difference
  - Services
  - Portfolio
  - Blog
  - Final CTA
- fixed repeated rows
- no add/remove controls
- no delete controls
- no section reordering
- no raw JSON textarea
- no `page_key`, `section_key`, `status`, or `sort_order` fields
- Preview Homepage link
- sticky Save Homepage action area
- tab error indicators
- `old()` support after validation failure
- field-level `@error` output
- help text for fixed counts, SVG path fields, title-line styling, and domain data boundaries

## Sidebar Behavior

Updated:

```text
resources/views/components/admin/sidebar.blade.php
```

Added `Homepage` under the `Content` group:

- route: `admin.homepage.edit`
- visible to `super_admin` and `admin`
- hidden from `article_writer`
- uses existing sidebar active-state logic and styling

## Activity Logging

Successful updates create one activity log:

```php
ActivityLog::log(
    'updated_homepage_sections',
    'PageSection',
    null,
    'Updated homepage page sections'
);
```

The activity log write is inside the same transaction as the eight section updates.

## Isolated Test Setup

Created focused tests:

- `tests/Feature/Admin/HomepageAdminTest.php`
- `tests/Feature/Admin/HomepageUpdateValidationTest.php`

The tests use SQLite `:memory:` and create only the required tables:

- `page_sections`
- `admin_users`
- `activity_logs`
- minimal `services`
- minimal `portfolio_projects`
- minimal `blog_articles`

The tests do not connect to local MySQL and do not run `prosper:install`.

## Test Results

Targeted commands run:

```powershell
php artisan test tests\Feature\Admin\HomepageAdminTest.php
php artisan test tests\Feature\Admin\HomepageUpdateValidationTest.php
```

Results:

- `HomepageAdminTest`: passed, 4 tests, 41 assertions.
- `HomepageUpdateValidationTest`: passed, 2 tests, 21 assertions.

Full suite:

```powershell
php artisan test
```

Result:

- passed, 60 tests, 767 assertions.

Other checks:

```powershell
php artisan route:list
git diff --check
```

Results:

- route list includes `admin.homepage.edit` and `admin.homepage.update`.
- `git diff --check`: no whitespace errors.

Warnings:

```text
warning: in the working copy of 'app/Http/Controllers/Public/HomeController.php', CRLF will be replaced by LF the next time Git touches it
warning: in the working copy of 'resources/views/components/admin/sidebar.blade.php', CRLF will be replaced by LF the next time Git touches it
warning: in the working copy of 'resources/views/public/home.blade.php', CRLF will be replaced by LF the next time Git touches it
warning: in the working copy of 'routes/admin.php', CRLF will be replaced by LF the next time Git touches it
```

Line-ending policy was not changed in this phase.

## Local MySQL Confirmation

No admin form was submitted against local MySQL.

No migrations or seeders were run against local MySQL.

No local MySQL `page_sections`, services, portfolio, blog, or settings records were inserted, updated, or deleted.

`php artisan prosper:install` was not run.

## Known Limitations

- The admin editor is implemented and covered by feature tests, but no live browser save was performed in this phase because local MySQL writes were explicitly out of scope.
- The tabbed editor is intentionally fixed-count. It does not support adding arbitrary sections or cards.
- Direct safe URLs are allowed by validation, but known internal route selects are the recommended editor path.
- The editor does not include a reset-to-seeded-default button yet.

## Next Deployment And Review Phase

Recommended next phase:

1. Back up local MySQL.
2. Inspect the admin editor in browser with a `super_admin` or `admin` account.
3. Submit a controlled local admin update only after approval.
4. Verify the eight `home` `page_sections` records changed as expected.
5. Verify the public homepage reflects saved data without changing service, portfolio, or blog records.
6. Run the full suite again after the approved local database exercise.
