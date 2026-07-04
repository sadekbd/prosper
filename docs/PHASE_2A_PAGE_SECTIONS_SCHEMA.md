# Phase 2A: Page Sections Schema

Date: 2026-07-04

Scope: schema and model only for the first reusable dynamic-content module. Public design, routes, controllers, Blade views, admin screens, existing data, and existing Prosper domain tables were not changed.

## Files Created

- `database/migrations/2026_07_04_000001_create_page_sections_table.php`
- `app/Models/PageSection.php`
- `tests/Feature/PageSectionSchemaTest.php`
- `docs/PHASE_2A_PAGE_SECTIONS_SCHEMA.md`

## Design Confirmation

The unique `(page_key, section_key)` constraint was added intentionally.

Reason: this first module targets fixed editable page sections, such as `home.hero`, `home.cta`, `about.story`, or `contact.hero`. Each fixed section key should have one canonical record per page. Repeated content inside a section, such as stats, cards, proof points, or tool lists, belongs in the section `payload` JSON until a more specific child-table need appears.

## Exact Schema

Table: `page_sections`

Columns:

| Column | Type | Nullable | Default |
| --- | --- | --- | --- |
| `id` | big integer primary key | no | auto-increment |
| `page_key` | string | no | none |
| `section_key` | string | no | none |
| `eyebrow` | string | yes | null |
| `title` | string | yes | null |
| `subtitle` | string | yes | null |
| `body` | longText | yes | null |
| `button_label` | string | yes | null |
| `button_url` | string | yes | null |
| `image` | string | yes | null |
| `payload` | json | yes | null |
| `status` | string | no | `active` |
| `sort_order` | unsigned integer | no | `0` |
| `created_at` | timestamp | yes | Laravel default |
| `updated_at` | timestamp | yes | Laravel default |

## Indexes And Constraints

Indexes:

- `page_key`
- `section_key`
- `status`
- `sort_order`

Unique constraint:

- `unique(page_key, section_key)`

This means `home + hero` can exist once, while `about + hero` can exist separately.

## Model

Model: `App\Models\PageSection`

Fillable fields:

- `page_key`
- `section_key`
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

Casts:

- `payload` => `array`
- `sort_order` => `integer`

Scopes:

- `active()` filters `status = active`
- `forPage(string $pageKey)` filters by `page_key`
- `ordered()` sorts by `sort_order`, then `id`

No extra business logic was added.

## Test Strategy

Test file: `tests/Feature/PageSectionSchemaTest.php`

The test:

- Uses the existing `phpunit.xml` SQLite in-memory test database.
- Does not request `/` or any public route.
- Does not depend on legacy Prosper tables such as `services`, `portfolio_projects`, `site_settings`, or `blog_articles`.
- Does not invoke `prosper:install`.
- Requires and runs only the new page sections migration in the isolated SQLite test environment.
- Confirms the table and required columns exist.
- Inserts `PageSection` records through the model.
- Confirms `payload` is returned as an array.
- Confirms `sort_order` is cast to an integer.
- Confirms `active`, `forPage`, and `ordered` scopes work.
- Confirms duplicate `page_key + section_key` pairs throw a database exception.

Re-confirmed test DB config:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Test Commands And Results

Targeted test command:

```powershell
php artisan test tests\Feature\PageSectionSchemaTest.php
```

Result:

```text
passed
tests: 2
passed: 2
assertions: 24
```

Whitespace check:

```powershell
git diff --check
```

Result: passed with no output.

Full test suite was not run during this phase. The known unrelated legacy failure remains: `Tests\Feature\ExampleTest` requests `/` and fails on SQLite `:memory:` because the legacy `services` table is not represented by migrations. That failure was not changed or masked.

## Compatibility Notes

MySQL:

- `json` maps to MySQL JSON.
- Composite unique index on `page_key` and `section_key` is supported.
- String index lengths use Laravel defaults and are compatible with current Laravel/MySQL defaults.

SQLite:

- `json` is stored compatibly for Laravel casting.
- Composite unique index is enforced.
- The targeted test confirms the migration/model behavior under SQLite `:memory:`.

## Deployment Instructions

For a reviewed deployment only:

```powershell
php artisan migrate
```

This migration is additive and creates only a new table. It does not modify or delete existing tables or data.

Do not run:

- `php artisan prosper:install`
- `php artisan migrate:fresh`
- `php artisan migrate:refresh`
- `php artisan migrate:reset`
- `php artisan migrate:rollback` unless intentionally rolling back this migration
- `php artisan db:wipe`
- seeders

## Rollback Instructions

If this migration has been deployed and must be reverted, the migration `down()` method drops only:

```text
page_sections
```

Rollback should be used only in a controlled deployment rollback plan. It should not be run casually against an environment containing content in `page_sections`.

## Confirmations

- No existing table was modified.
- No existing table was dropped, renamed, recreated, or truncated.
- No database records were modified.
- No route was modified.
- No controller was modified.
- No Blade view was modified.
- No admin page/sidebar was modified.
- No service, portfolio, blog, contact, newsletter, or settings code was modified.
- No package was installed or updated during Phase 2A.
- `composer.json` and `composer.lock` were not modified.
- `npm run build` was not run.
- Public design and application behavior remain unchanged.
