# Test Baseline Investigation

Date: 2026-07-04

Scope: Test baseline investigation only. No packages, Composer files, application code, test files, PHPUnit config, database records, migrations, seeders, build artifacts, or app behavior were modified.

## Critical Safety Warning

Do not run database-refreshing tests against the existing `prosper_media` database.

If future tests use `RefreshDatabase`, `DatabaseMigrations`, `DatabaseTransactions` incorrectly, or any migration reset/fresh behavior while pointed at MySQL `prosper_media`, they can modify or destroy local content. Tests must remain isolated on SQLite in-memory or a disposable test database before any database-refreshing trait is enabled.

Also do not run `php artisan prosper:install` on the existing database. It is destructive.

## Files Inspected

- `composer.json`
- `composer.lock`
- `vendor/composer/installed.json`
- `vendor/composer/installed.php`
- `bootstrap/providers.php`
- `bootstrap/app.php`
- `bootstrap/cache/packages.php`
- `phpunit.xml`
- `tests/TestCase.php`
- `tests/Feature/ExampleTest.php`
- `tests/Unit/ExampleTest.php`
- `vendor/bin`

No `phpunit.xml.dist` file exists.

## Exact Package Versions

From `composer.lock`:

| Package | Locked Version |
| --- | --- |
| `laravel/framework` | `v13.12.0` |
| `nunomaduro/collision` | `v8.9.4` |
| `phpunit/phpunit` | `12.5.28` |
| `mockery/mockery` | `1.6.12` |
| `laravel/pint` | `v1.29.1` |

From `vendor/composer/installed.json`:

| Package | Installed In Current `vendor` |
| --- | --- |
| `laravel/framework` | `v13.12.0` |
| `nunomaduro/collision` | Not installed |
| `phpunit/phpunit` | Not installed |
| `mockery/mockery` | Not installed |
| `laravel/pint` | Not installed |

Direct path checks:

- `vendor/nunomaduro/collision`: missing
- `vendor/phpunit/phpunit`: missing
- `vendor/bin/phpunit.bat`: missing

`vendor/bin` currently contains runtime binaries such as `carbon.bat`, `psysh.bat`, and `var-dump-server.bat`, but no PHPUnit binary.

## Commands Run

### `composer show nunomaduro/collision`

First attempt failed because Composer could not use the PHP temp directory:

```text
PHP temp directory (C:\Users\DELL\AppData\Local\Temp) does not exist or is not writable to Composer.
```

Rerun with approved escalation:

```text
Package "nunomaduro/collision" not found, try using --available (-a) to show all available packages.
```

Conclusion: Collision is present in `composer.lock` but not installed in current `vendor`.

### `composer show phpunit/phpunit`

First attempt failed for the same PHP temp directory issue.

Rerun with approved escalation:

```text
Package "phpunit/phpunit" not found, try using --available (-a) to show all available packages.
```

Conclusion: PHPUnit is present in `composer.lock` but not installed in current `vendor`.

### `php artisan list`

Succeeded.

Important result:

- `test` command is not listed.
- `make:test` is listed.
- `schedule:test` is listed.
- `prosper:install` is listed and must not be run on the existing database.

### `.\vendor\bin\phpunit.bat --version`

Failed:

```text
.\vendor\bin\phpunit.bat : The term '.\vendor\bin\phpunit.bat' is not recognized as the name of a cmdlet, function, script file, or operable program.
```

Conclusion: PHPUnit binary is not installed.

### `.\vendor\bin\phpunit.bat`

Failed with the same missing command/path error.

Conclusion: PHPUnit cannot currently run directly from `vendor/bin`.

## PHPUnit Configuration

`phpunit.xml` exists and configures:

```xml
<env name="APP_ENV" value="testing"/>
<env name="CACHE_STORE" value="array"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="MAIL_MAILER" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="SESSION_DRIVER" value="array"/>
```

Test database environment:

- Intended test database connection: SQLite
- Intended test database storage: in-memory database
- It does not point tests at local MySQL `prosper_media`.

## Existing Tests

`tests/Feature/ExampleTest.php`:

- Extends `Tests\TestCase`.
- Calls `GET /`.
- Asserts HTTP 200.
- `RefreshDatabase` is present only as a commented import:

```php
// use Illuminate\Foundation\Testing\RefreshDatabase;
```

`tests/Unit/ExampleTest.php`:

- Extends `PHPUnit\Framework\TestCase`.
- Asserts `true`.

Current tests do not intentionally touch the real MySQL database. The feature test may bootstrap the Laravel app and execute homepage logic, but under `phpunit.xml` it should use SQLite `:memory:` if PHPUnit is available.

## Package Discovery And Providers

`bootstrap/providers.php` contains only:

```php
App\Providers\AppServiceProvider::class
```

`bootstrap/cache/packages.php` currently lists package providers for:

- `laravel/tinker`
- `nesbot/carbon`
- `nunomaduro/termwind`
- `spatie/laravel-sluggable`

It does not list `nunomaduro/collision`.

`bootstrap/app.php` registers routing, middleware aliases, and exception configuration. It does not manually register a test command provider.

## Root Cause Of Missing `php artisan test`

Root cause is identifiable:

- `composer.json` declares `nunomaduro/collision` and `phpunit/phpunit` under `require-dev`.
- `composer.lock` locks Collision `v8.9.4` and PHPUnit `12.5.28`.
- The current `vendor` installation does not include dev dependencies.
- `vendor/composer/installed.json` contains Laravel framework but not Collision or PHPUnit.
- `vendor/nunomaduro/collision` and `vendor/phpunit/phpunit` are missing.
- `bootstrap/cache/packages.php` does not include Collision's Laravel service provider.
- Therefore Collision is not loaded, and Laravel's Artisan `test` command is not registered.

This is not an application routing/controller issue. It is a dependency installation state issue: the project appears to be running with production-style vendor dependencies, likely from `composer install --no-dev` or equivalent.

## Whether PHPUnit Works Directly

No. PHPUnit does not currently work directly because `vendor/bin/phpunit.bat` is missing and `phpunit/phpunit` is not installed in `vendor`.

The PHPUnit config file exists, and the locked version is available in `composer.lock`, but the executable is absent from the installed vendor tree.

## Collision Status

Collision is:

- Declared in `composer.json` under `require-dev`
- Locked in `composer.lock` as `v8.9.4`
- Not installed in `vendor`
- Not listed in package discovery cache
- Not loaded by Artisan

Collision is not merely disabled; it is missing from the current installed dependency set.

## Safe Recommended Fix

Safe fix for local development only:

1. Ensure PHP temp directory writability is fixed first, because Composer reported `C:\Users\DELL\AppData\Local\Temp` is missing or not writable.
2. Install dev dependencies from the existing lock file without updating versions:

```bash
composer install
```

or, if Composer was previously run with no-dev:

```bash
composer install --dev
```

3. Rebuild package discovery if Composer scripts do not do it automatically:

```bash
php artisan package:discover
```

4. Re-check:

```bash
php artisan test
.\vendor\bin\phpunit.bat --version
.\vendor\bin\phpunit.bat
```

Do not run `composer update` for this fix unless package upgrades are intentionally being reviewed.

## Can Phase 2 Schema Work Proceed Safely?

Phase 2 can proceed safely only if schema changes remain additive and non-destructive, as planned, and no database-refreshing tests are run against the existing `prosper_media` database.

However, automated test verification is currently not available through `php artisan test` or direct PHPUnit. Before relying on tests for Phase 2, dev dependencies should be installed from the lock file and the test command should be revalidated.

Recommended Phase 2 gate:

- Additive migrations only.
- No `prosper:install`.
- No migration reset/refresh/fresh/rollback/wipe.
- No seeders unless explicitly reviewed.
- Fix local dev dependency installation before treating tests as a safety net.

## Commands Intentionally Not Run

- `composer install`
- `composer update`
- `php artisan package:discover`
- `php artisan prosper:install`
- `php artisan migrate`
- `php artisan migrate:fresh`
- `php artisan migrate:refresh`
- `php artisan migrate:reset`
- `php artisan migrate:rollback`
- `php artisan db:seed`
- `php artisan db:wipe`
- `npm run build`
