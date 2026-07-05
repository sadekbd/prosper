# Phase 4K: Homepage Test Harness Cleanup

Date: 2026-07-06

Scope: test-harness cleanup only. Production application code, Blade views, routes, models, migrations, seeders, admin pages, and local MySQL data were not changed.

## Root Cause

The previous full-suite failure was:

```text
Tests\Feature\ExampleTest::test_the_application_returns_a_successful_response
```

The stock Laravel example test requested `/` while PHPUnit was using SQLite `:memory:`. After the homepage integration, `HomeController` legitimately queries `page_sections`, but the default test did not create the `page_sections` table.

This was a test-environment setup gap, not a production homepage failure.

## Files Updated

- `tests/Feature/ExampleTest.php`
- `docs/PHASE_4K_TEST_HARNESS_CLEANUP.md`

The existing `ExampleTest` was updated in place because it already represented the generic application smoke-test intent. It now performs a meaningful homepage smoke test instead of a bare request with no schema setup.

No focused homepage integration tests were modified.

## Minimum SQLite Schema Setup

The smoke test uses the SQLite `:memory:` connection from `phpunit.xml` and prepares only what `GET /` needs:

- Runs only `database/migrations/2026_07_04_000001_create_page_sections_table.php`.
- Creates a minimal `services` table compatible with `Service::active()->take(3)->get()`.
- Leaves `portfolio_projects` and `blog_articles` absent because `HomeController` already guards those tables with `Schema::hasTable()`.
- Seeds `HomePageSectionSeeder` to exercise the dynamic homepage path.
- Caches minimal empty site settings so the global view composer does not need a `site_settings` table.

## Why `prosper:install` Was Not Used

`php artisan prosper:install` is destructive and creates/resets broad application tables. The smoke test only needed an isolated SQLite schema for the homepage request, so the installer was intentionally not used.

No migrations or seeders were run against local MySQL `prosper_media`.

## Smoke-Test Coverage

The updated smoke test confirms:

- Database driver is SQLite before and after the request.
- `GET /` returns HTTP 200.
- The current homepage renders.
- Seeded dynamic hero content appears:
  - `Engineering Digital`
  - `Technical Precision.`
- Representative homepage content appears:
  - `Our Core Services`
  - `Ready to scale your business?`

It does not duplicate all section-level assertions because those remain covered by the focused homepage integration tests.

## Test Results

Focused replacement test:

```powershell
php artisan test tests\Feature\ExampleTest.php
```

Result:

- Passed, 1 test, 8 assertions.

Full suite:

```powershell
php artisan test
```

Result:

- Passed, 46 tests, 658 assertions.

Diff check:

```powershell
git diff --check
```

Result:

- No whitespace errors.

Warnings:

```text
warning: in the working copy of 'app/Http/Controllers/Public/HomeController.php', CRLF will be replaced by LF the next time Git touches it
warning: in the working copy of 'resources/views/public/home.blade.php', CRLF will be replaced by LF the next time Git touches it
```

These warnings pre-existed this phase and are consistent with the repository `.gitattributes` LF policy documented in Phase 4J.

## Production And Database Confirmation

This phase did not modify:

- Production application code
- `HomeController`
- Blade views
- Routes
- Models
- Migrations
- Seeders
- Admin pages
- Existing domain records
- Local MySQL `prosper_media` data

The test remains isolated to SQLite `:memory:`.

## Remaining Test Limitations

The smoke test intentionally prepares only the minimum homepage schema. It is not a substitute for:

- Full migration coverage.
- Admin CRUD tests.
- End-to-end database lifecycle tests.
- Browser visual regression tests.

The focused homepage tests remain the detailed safety net for section payload validation, fallbacks, URL safety, and live/fallback card behavior.

## Recommendation For Admin CRUD Phase

Before building admin CRUD for `page_sections`, keep the same testing discipline:

- Use SQLite `:memory:` or a disposable test database.
- Avoid `RefreshDatabase` against local MySQL `prosper_media`.
- Add CRUD tests with explicit schema setup.
- Mirror the existing Blade safety constraints in request validation tests.
- Do not use `prosper:install` for test setup.
