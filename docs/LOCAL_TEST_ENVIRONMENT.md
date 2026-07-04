# Local Test Environment Restore

Date: 2026-07-04

Scope: local test environment restore only. No application code, migrations, database records, seeders, Composer manifests, lock file, npm build output, or test files were modified. Composer-managed `vendor` files were restored from the existing `composer.lock`.

## Temporary Directory

Commands run:

```powershell
php -i | findstr /I "upload_tmp_dir sys_temp_dir"
php -r "echo sys_get_temp_dir(), PHP_EOL;"
```

Output:

```text
sys_temp_dir => no value => no value
upload_tmp_dir => no value => no value
C:\Users\DELL\AppData\Local\Temp
```

Directory check:

- `C:\Users\DELL\AppData\Local\Temp` exists.
- Inside the read-only sandbox, a write test failed with access denied.
- Outside the sandbox, the same write test succeeded.

Resolution:

- No new temp directory was created.
- Composer was run with approved escalation so it could use the normal PHP temp directory.

## Composer Install

Command executed:

```powershell
composer install
```

Purpose:

- Restore dependencies strictly from the existing `composer.lock`.
- Include dev dependencies.
- Do not update package versions.

Composer result summary:

```text
Installing dependencies from lock file (including require-dev)
Package operations: 33 installs, 0 updates, 0 removals
Generating optimized autoload files
@php artisan package:discover --ansi
nunomaduro/collision .. DONE
```

Composer package discovery ran normally and discovered:

- `laravel/pail`
- `laravel/pao`
- `laravel/tinker`
- `nesbot/carbon`
- `nunomaduro/collision`
- `nunomaduro/termwind`
- `spatie/laravel-sluggable`

## Lock File Status

Pre-install `composer.lock` SHA256:

```text
7DEEA055CA532BE9172C4B41FC0F7B3DACD57CAC3015823B8EA082C7C5303740
```

Post-install `composer.lock` SHA256:

```text
7DEEA055CA532BE9172C4B41FC0F7B3DACD57CAC3015823B8EA082C7C5303740
```

Result: `composer.lock` did not change.

`composer.json` was not modified.

## Installed Package Versions

Command:

```powershell
composer show nunomaduro/collision
```

Installed Collision:

```text
nunomaduro/collision v8.9.4
path: C:\laragon\www\prosper-media\vendor\nunomaduro\collision
```

Command:

```powershell
composer show phpunit/phpunit
```

Installed PHPUnit:

```text
phpunit/phpunit 12.5.28
path: C:\laragon\www\prosper-media\vendor\phpunit\phpunit
```

## Artisan Test Command

Command:

```powershell
php artisan list
```

Result:

- `test` command is now available.
- `pail` is now available.
- `prosper:install` is still present and must not be run against the existing database.

Command:

```powershell
php artisan test --version
```

Output:

```text
Laravel Framework 13.12.0
```

## PHPUnit Binary

Command:

```powershell
.\vendor\bin\phpunit.bat --version
```

Output:

```text
PHPUnit 12.5.28 by Sebastian Bergmann and contributors.
```

`.\vendor\bin\phpunit.bat` now exists.

## Test Database Configuration

Before running the full suite, `phpunit.xml` was re-confirmed:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

Test database:

- Connection: SQLite
- Database: in-memory
- It does not use the local MySQL `prosper_media` database.

Safety warning:

- Do not run database-refreshing tests against `prosper_media`.
- Keep test database isolation in place before adding `RefreshDatabase`, `DatabaseMigrations`, or similar traits.

## Full Test Results

Command:

```powershell
php artisan test
```

Result:

```text
failed
tests: 2
passed: 1
failed: 1
assertions: 2
```

Passing test:

- `Tests\Unit\ExampleTest::test_that_true_is_true`

Failing test:

- `Tests\Feature\ExampleTest::test_the_application_returns_a_successful_response`

Failure summary:

```text
Expected response status code [200] but received 500.
SQLSTATE[HY000]: General error: 1 no such table: services
Connection: sqlite, Database: :memory:
SQL: select * from "services" where "status" = active order by "sort_order" asc limit 3
```

Likely cause:

- The feature test boots the app and requests `/`.
- `HomeController@index` queries the `services` table directly.
- `phpunit.xml` correctly points tests to SQLite `:memory:`.
- The current migration set only contains Laravel default migrations; Prosper Media domain tables such as `services` are not represented by normal migration files.
- Therefore the isolated in-memory test database has no `services` table.

This is a test-environment/schema-coverage issue, not evidence that the local MySQL site is broken.

## Phase 2A Readiness

Local test tooling is restored:

- Dev dependencies are installed from the lock file.
- Collision is installed and discovered.
- `php artisan test` is available.
- Direct PHPUnit is available.
- `composer.lock` is unchanged.

Phase 2A schema work can proceed only with the previously agreed safety rules:

- Additive migrations only.
- No `prosper:install`.
- No `migrate:fresh`, `migrate:refresh`, `migrate:reset`, `migrate:rollback`, `db:wipe`, or seeders unless separately reviewed and approved.
- Do not rely on the current test suite as fully green yet.

Before treating tests as a safety net, the project needs a non-destructive test schema strategy for Prosper domain tables, such as additive migrations for domain tables or targeted test setup that creates required tables in SQLite without touching MySQL.

## Commands Intentionally Not Run

- `composer update`
- `php artisan prosper:install`
- `php artisan migrate`
- `php artisan migrate:fresh`
- `php artisan migrate:refresh`
- `php artisan migrate:reset`
- `php artisan migrate:rollback`
- `php artisan db:seed`
- `php artisan db:wipe`
- `npm run build`
