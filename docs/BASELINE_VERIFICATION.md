# Baseline Verification

Date: 2026-07-04

Scope: Phase 0 baseline only. No application code, database records, migrations, seeders, build artifacts, routes, controllers, models, Blade views, styles, or packages were modified.

## Critical Warning

Do not run `php artisan prosper:install` against an existing database.

The audit confirmed `prosper:install` is destructive: it drops and recreates Prosper Media domain tables such as `admin_users`, `site_settings`, `services`, `portfolio_projects`, `blog_articles`, `contact_messages`, and related tables. It must not be used on the current working database or any production/staging database that contains existing content.

## Git State

Command:

```bash
git status --short --branch --untracked-files=all
```

Result:

```text
## feature/dynamic-reusable-portfolio...origin/feature/dynamic-reusable-portfolio [ahead 1]
```

Current branch: `feature/dynamic-reusable-portfolio`

Working tree state: no modified or untracked files were reported before creating this Phase 0 baseline document. Branch is ahead of `origin/feature/dynamic-reusable-portfolio` by 1 commit.

## Framework And Package Stack

Commands:

```bash
php artisan about
php -v
node -v
npm -v
composer --version
```

Summary:

- Application name: `Prosper Media`
- Laravel version: `13.12.0`
- PHP version: `8.3.30`
- Composer version: `2.9.5`
- Node version: `v24.14.1`
- npm version: `11.11.0`
- Environment: `local`
- Debug mode: enabled
- App URL: `localhost:8000`
- Database driver: `mysql`
- Cache driver: `file`
- Queue driver: `sync`
- Session driver: `file`
- Mail driver: `smtp`
- Vite input files: `resources/css/app.css`, `resources/js/app.js`

Composer stack:

- `laravel/framework ^13.8`
- `laravel/tinker ^3.0`
- `spatie/laravel-sluggable ^4.0`
- `intervention/image ^4.1`
- `ezyang/htmlpurifier ^4.19`
- Dev tools include PHPUnit `^12.5.12`, Laravel Pint, Pail, Pao, Collision, Faker, Mockery.

Frontend stack:

- Blade templates
- Tailwind CSS `^3.4.17`
- Vite `^8.0.0`
- Laravel Vite Plugin `^3.1`
- Alpine.js `^3.15.12`
- `@tailwindcss/forms`, `@tailwindcss/typography`, `autoprefixer`, `postcss`, `concurrently`

No React or Inertia package or page structure was found in the baseline audit.

## Public Route Baseline

Command:

```bash
php artisan route:list
```

Public routes recorded:

| Method | URI | Name | Action |
| --- | --- | --- | --- |
| GET/HEAD | `/` | `home` | `Public\HomeController@index` |
| GET/HEAD | `/about` | `about` | `Public\AboutController@index` |
| GET/HEAD | `/services` | `services` | `Public\ServicesController@index` |
| GET/HEAD | `/services/{slug}` | `services.show` | `Public\ServicesController@show` |
| GET/HEAD | `/portfolio` | `portfolio` | `Public\PortfolioController@index` |
| GET/HEAD | `/portfolio/{slug}` | `portfolio.show` | `Public\PortfolioController@show` |
| GET/HEAD | `/blog` | `blog` | `Public\BlogController@index` |
| GET/HEAD | `/blog/category/{slug}` | `blog.category` | `Public\BlogController@category` |
| GET/HEAD | `/blog/{slug}` | `blog.show` | `Public\BlogController@show` |
| GET/HEAD | `/contact` | `contact` | `Public\ContactController@index` |
| POST | `/contact` | `contact.store` | `Public\ContactController@store` |
| POST | `/newsletter/subscribe` | `newsletter.subscribe` | `Public\NewsletterController@subscribe` |
| GET/HEAD | `/privacy-policy` | `privacy-policy` | `routes/web.php:40` |
| GET/HEAD | `/sitemap.xml` | `sitemap` | `Public\SitemapController@index` |
| GET/HEAD | `/up` | none | Laravel health route |

Laravel also registers local storage routes:

- `GET/HEAD storage/{path}` named `storage.local`
- `PUT storage/{path}` named `storage.local.upload`

## Admin Route Baseline

Admin prefix: `/admin`

Admin auth routes:

| Method | URI | Name | Action |
| --- | --- | --- | --- |
| GET/POST | `/admin/login` | `admin.login`, `admin.login.post` | `Admin\Auth\LoginController` |
| GET/POST | `/admin/signup` | `admin.signup`, `admin.signup.post` | `Admin\Auth\SignupController` |
| GET/POST | `/admin/forgot-password` | `admin.forgot`, `admin.forgot.post` | `Admin\Auth\ForgotPasswordController` |
| GET/POST | `/admin/reset-password` | `admin.reset.form`, `admin.reset.post` | `Admin\Auth\ForgotPasswordController` |
| POST | `/admin/logout` | `admin.logout` | `Admin\Auth\LoginController@logout` |

Admin module routes:

| Module | Route Group | Actions |
| --- | --- | --- |
| Dashboard | `/admin/dashboard` | index |
| Blog articles | `/admin/blog/articles` | index, create, store, edit, update, publish, reject, destroy |
| Blog categories | `/admin/blog/categories` | index, store, edit, update, destroy |
| Services | `/admin/services` | index, create, store, edit, update, destroy |
| Portfolio | `/admin/portfolio` | index, create, store, edit, update, destroy |
| Contact messages | `/admin/messages` | index, show, update status, destroy |
| Newsletter | `/admin/newsletter` | index, destroy |
| Users | `/admin/users` | index, update status, update role, destroy |
| Settings | `/admin/settings` | index, update |

Route count reported by Artisan: 64 routes.

## Database Connection Status

Command:

```bash
php artisan db:show
```

Result summary:

- Connection succeeded.
- Driver/server: MySQL `8.4.3`
- Configured connection: `mysql`
- Current database: `prosper_media`
- Host: `127.0.0.1`
- Port: `3306`
- Open connections: `1`
- Total tables visible to the connection: `107`
- Total visible size: `12.78 MB`

The connection can see multiple schemas because the local MySQL user has broad visibility. The current `prosper_media` schema includes both Laravel default tables and Prosper domain tables:

- Laravel/default tables: `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations`
- Prosper domain tables: `activity_logs`, `admin_password_resets`, `admin_users`, `blog_articles`, `blog_categories`, `contact_messages`, `newsletter_subscribers`, `pages`, `portfolio_projects`, `service_features`, `services`, `site_settings`

## Migration Status

Command:

```bash
php artisan migrate:status
```

Result:

```text
0001_01_01_000000_create_users_table  [1] Ran
0001_01_01_000001_create_cache_table  [1] Ran
0001_01_01_000002_create_jobs_table   [1] Ran
```

Current migration file structure:

- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/migrations/0001_01_01_000001_create_cache_table.php`
- `database/migrations/0001_01_01_000002_create_jobs_table.php`

Important baseline note: only the three Laravel default migration files are present. Prosper Media domain tables exist in the database but are not represented by normal migration files; they are created by the destructive `prosper:install` command and must not be recreated.

## Test Results

Command:

```bash
php artisan test
```

Result:

```text
ERROR  Command "test" is not defined. Did you mean one of these?
  make:test
  schedule:test
```

Baseline status: failed because this Laravel installation does not currently register the `test` Artisan command, even though `composer.json` contains a `test` script that calls `@php artisan test`.

No substitute test command was run during Phase 0.

Existing test files:

- `tests/Feature/ExampleTest.php`
- `tests/Unit/ExampleTest.php`

## Storage Symlink Status

`php artisan about` reported:

```text
C:\laragon\www\prosper-media\public\storage  LINKED
```

Direct filesystem inspection:

```text
FullName   : C:\laragon\www\prosper-media\public\storage
LinkType   : Junction
Target     : C:\laragon\www\prosper-media\storage\app\public
Attributes : Directory, Archive, ReparsePoint
```

Baseline status: public storage is linked via a Windows junction.

## Existing Upload Directory Summary

Inspected path:

```text
storage/app/public
```

Found:

- `storage/app/public/.gitignore`
- `storage/app/public/uploads`
- `storage/app/public/uploads/settings`

Uploaded media files found:

| Directory | Files | Total Size |
| --- | ---: | ---: |
| `storage/app/public/uploads/settings` | 2 PNG files | 903,421 bytes |

No `uploads/blog` or `uploads/portfolio` directories were present during this baseline inspection.

Settings upload files observed:

- `h7ZzPnyAt2kCBn57v25tjBr7ajGy0u32J15SzjTQ.png`
- `mmLZfgS9wm0cRATRJC9xnB1a9GunJIbv1zvI4WXp.png`

## Command Failures And Warnings

### `php artisan about`

First attempt failed inside the read-only sandbox because Laravel/Symfony attempted to write log/temp process output:

```text
The stream or file "storage/logs/laravel.log" could not be opened in append mode: Permission denied
A temporary file could not be opened to write the process output
```

The command was rerun with approved escalation and succeeded. This was a sandbox permission issue, not an application runtime failure.

### `php artisan test`

Failed because the `test` command is not defined in this Artisan installation.

### `composer --version`

Composer version was reported successfully, but Composer also warned:

```text
PHP temp directory (C:\Users\DELL\AppData\Local\Temp) does not exist or is not writable to Composer.
```

This may affect package operations or tooling that needs PHP temp files. No package install/update command was run.

## Issues To Resolve Before Phase 2

- Decide how to handle the missing `php artisan test` command before adding migration/content modules. The current `composer test` script also depends on this missing command.
- Add or document a safe non-destructive migration path for Prosper domain tables. Do not rely on `prosper:install` for an existing database.
- Consider updating deployment documentation so it clearly warns that `prosper:install` is destructive.
- Resolve PHP temp directory writability if future Composer/tooling work requires temp files.
- Ensure future migrations are additive only and do not drop, rename, recreate, reset, refresh, rollback, or destructively modify existing tables/columns.
- Preserve the current `public/storage` junction and existing settings upload files during all future work.

## Commands Intentionally Not Run

- `php artisan prosper:install`
- `php artisan db:seed`
- `php artisan migrate:fresh`
- `php artisan migrate:refresh`
- `php artisan migrate:reset`
- `php artisan migrate:rollback`
- `npm run build`
- Any package install/update command
