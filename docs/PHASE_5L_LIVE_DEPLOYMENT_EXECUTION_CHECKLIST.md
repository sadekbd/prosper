# Phase 5L: Live Deployment Execution Checklist

Date: 2026-07-06

Scope: prepare the exact live deployment execution plan for the dynamic homepage and homepage admin editor. This phase verified local readiness and local build output, then documented the deployment runbook. No production server was contacted, no deployment package was created, no production command was run, and no database migration or seeder was executed.

## Local Readiness Result

Branch:

```text
feature/dynamic-reusable-portfolio
```

Initial Git status:

```text
## feature/dynamic-reusable-portfolio...origin/feature/dynamic-reusable-portfolio [ahead 1]
```

Recent commits:

```text
8f35d96 Document homepage admin production deployment plan
bbeca86 Document final homepage dirty update retest
2608876 Fix homepage admin repeated field ordering
ccf1f4f Add MySQL-shaped homepage dirty update coverage
62a3d1e Refine homepage admin dirty-only updates
```

Readiness commands run locally:

```powershell
php artisan test
git diff --check
```

Results:

- Full suite passed: 71 tests, 865 assertions.
- `git diff --check`: no whitespace errors.

## Local Build Result

`node_modules` was present, so no install command was run.

Build command:

```powershell
npm run build
```

Result:

- Build completed successfully.
- `public/build/manifest.json` exists.
- Built assets:
  - `public/build/assets/app-CBnYXK8e.css`
  - `public/build/assets/app-DP6xFuAG.js`

Do not commit build assets unless this project's deployment branch intentionally tracks `public/build`.

## Deployment Package Strategy

Package filename convention:

```text
homepage_admin_deploy_YYYYMMDD_HHMMSS.zip
```

Example local package path:

```text
deploy/homepage_admin_deploy_YYYYMMDD_HHMMSS.zip
```

Create the zip only after tests and local build pass, and only when the operator is ready to upload.

### Include

Include:

- `app`
- `artisan`
- `bootstrap`
- `composer.json`
- `composer.lock`
- `config`
- `database/migrations`
- `database/seeders/HomePageSectionSeeder.php`
- `package.json`
- `package-lock.json` if present
- `public`
- `resources`
- `routes`
- `storage/app/public` only if this is part of the established deployment workflow and does not overwrite live uploads
- `vendor` only if live Composer is unavailable and PHP version compatibility is confirmed
- `vite.config.js`

Homepage-specific files included by the package:

- `app/Support/HomepageContent.php`
- `app/Http/Controllers/Public/HomeController.php`
- `app/Http/Controllers/Admin/HomepageController.php`
- `app/Http/Requests/Admin/UpdateHomepageRequest.php`
- `database/migrations/2026_07_04_000001_create_page_sections_table.php`
- `database/seeders/HomePageSectionSeeder.php`
- `resources/views/admin/homepage/edit.blade.php`
- `resources/views/public/home.blade.php`
- `resources/views/components/admin/sidebar.blade.php`
- `routes/admin.php`

### Exclude

Exclude:

- `.env`
- `.git`
- `node_modules`
- `backups`
- `deploy`
- local database dumps
- `storage/logs`
- local cache/temp files
- tests, unless production test execution is intentionally part of the deployment workflow
- local IDE files

### Local PowerShell Packaging Example

Use a staging directory instead of zipping the working tree directly.

```powershell
$stamp = Get-Date -Format "yyyyMMdd_HHmmss"
$release = "deploy\homepage_admin_deploy_$stamp"
$zip = "$release.zip"
New-Item -ItemType Directory -Force -Path $release
```

Copy required directories/files into `$release`, preserving structure. Then create the archive:

```powershell
Compress-Archive -Path "$release\*" -DestinationPath $zip
Get-Item $zip
```

Do not include `.env`, `node_modules`, `backups`, local dumps, or local logs.

No deployment zip was created in Phase 5L.

## Live Server Pre-Deployment Backup Commands

Use the actual project path. Previous context suggested:

```bash
cd /home/sadekbdt/flsa-welfare
```

If the live path differs, use the real path and record it.

Create a timestamp:

```bash
STAMP=$(date +%Y%m%d_%H%M%S)
```

Create backup directories outside the web root if possible:

```bash
mkdir -p ~/deployment_backups/$STAMP
```

Database backup command pattern:

```bash
mysqldump --single-transaction --routines --triggers --databases LIVE_DATABASE_NAME > ~/deployment_backups/$STAMP/live_database_before_homepage_admin_$STAMP.sql
```

Do not print the database password in terminal history or documentation. Prefer a protected MySQL option file or an interactive prompt.

Project file backup:

```bash
tar -czf ~/deployment_backups/$STAMP/project_files_before_homepage_admin_$STAMP.tar.gz .
```

Current built asset backup:

```bash
tar -czf ~/deployment_backups/$STAMP/public_build_before_homepage_admin_$STAMP.tar.gz public/build
```

Verify backups:

```bash
ls -lh ~/deployment_backups/$STAMP
test -s ~/deployment_backups/$STAMP/live_database_before_homepage_admin_$STAMP.sql
test -s ~/deployment_backups/$STAMP/project_files_before_homepage_admin_$STAMP.tar.gz
test -s ~/deployment_backups/$STAMP/public_build_before_homepage_admin_$STAMP.tar.gz
```

Inspect the database backup for expected tables without exposing credentials:

```bash
grep -E "CREATE TABLE.*(admin_users|page_sections|services|portfolio_projects|blog_articles|site_settings|activity_logs)" ~/deployment_backups/$STAMP/live_database_before_homepage_admin_$STAMP.sql
```

Stop if any backup is missing, empty, or cannot be verified.

## Upload And Unzip Plan

Upload the package to a temporary location:

```bash
mkdir -p ~/deployment_uploads
```

Example upload destination:

```text
~/deployment_uploads/homepage_admin_deploy_YYYYMMDD_HHMMSS.zip
```

Unzip into a temporary release directory:

```bash
mkdir -p ~/deployment_releases/homepage_admin_deploy_$STAMP
unzip ~/deployment_uploads/homepage_admin_deploy_YYYYMMDD_HHMMSS.zip -d ~/deployment_releases/homepage_admin_deploy_$STAMP
```

Compare key files before copying:

```bash
ls -lah ~/deployment_releases/homepage_admin_deploy_$STAMP
test -f ~/deployment_releases/homepage_admin_deploy_$STAMP/artisan
test -f ~/deployment_releases/homepage_admin_deploy_$STAMP/routes/admin.php
test -f ~/deployment_releases/homepage_admin_deploy_$STAMP/public/build/manifest.json
```

Recommended copy approach:

```bash
rsync -av \
  --exclude='.env' \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='backups' \
  --exclude='deploy' \
  --exclude='storage/logs' \
  --exclude='storage/app/public' \
  ~/deployment_releases/homepage_admin_deploy_$STAMP/ \
  /home/sadekbdt/flsa-welfare/
```

Preserve:

- `.env`
- `storage`
- `storage/app/public` uploads
- existing file permissions where needed
- live user-uploaded media

Do not overwrite live `.env`.

Do not delete `storage/app/public` uploads.

## Composer And Vendor Strategy

Check Composer:

```bash
composer --version
```

If Composer exists:

```bash
composer install --no-dev --optimize-autoloader
```

Do not run:

```bash
composer update
```

If Composer is not found:

- Use the uploaded local `vendor` directory only if this is the accepted live workflow.
- Confirm local PHP and live PHP are compatible, ideally PHP 8.3 on both.
- Stop if PHP versions or extensions are incompatible.

Check PHP:

```bash
php -v
php -m
```

## Asset Strategy

Because the live server may not run npm build, the preferred path is:

1. Build locally from the approved commit.
2. Upload `public/build`.
3. Verify `public/build/manifest.json` on the live server.

Live verification:

```bash
test -f public/build/manifest.json
ls -lah public/build public/build/assets
```

If the live server can build assets and that workflow is approved:

```bash
npm ci
npm run build
```

Do not run npm commands on live if Node/npm availability or memory limits are uncertain.

## Migration Strategy

Run only after verified backups and file deployment:

```bash
php artisan migrate --force
```

This applies the additive `page_sections` migration if needed.

Never run:

```bash
php artisan prosper:install
php artisan migrate:fresh
php artisan migrate:refresh
php artisan migrate:reset
php artisan migrate:rollback
php artisan db:wipe
```

## Seeder Decision Tree

Before seeding, check the homepage section count:

```bash
php artisan tinker --execute="echo DB::table('page_sections')->where('page_key', 'home')->count();"
```

Decision:

- If count is `0`, run the targeted seeder once:

```bash
php artisan db:seed --class=HomePageSectionSeeder --force
```

- If count is `8`, do not run the seeder unless overwriting production homepage values with seeded defaults is explicitly approved.
- If count is between `1` and `7`, stop and report. Use a safer missing-record repair plan.
- If count is greater than `8`, stop and investigate duplicates or unexpected records.

Never run broad seeding:

```bash
php artisan db:seed --force
```

Reason:

- `HomePageSectionSeeder` uses `updateOrCreate`.
- It creates missing fixed records.
- It also overwrites existing fixed records with seeded defaults.

## Cache Commands

After file deployment and safe database steps:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If production uses queues:

```bash
php artisan queue:restart
```

If PHP-FPM/opcache is active, reload PHP-FPM or restart the web service according to the host's normal procedure.

## Post-Deployment Public Checks

Public checks:

- `GET /` returns 200.
- Hero renders.
- Stats render exactly four items.
- Trust bar renders exactly seven items.
- Difference renders exactly three cards.
- Services preview renders live service records or approved fallback behavior.
- Portfolio preview renders live portfolio records or approved fallback behavior.
- Blog preview renders live blog records or approved fallback behavior.
- Final CTA renders.
- Header and footer remain unchanged.
- Public navigation works.
- Quick mobile check around 390px.
- Quick tablet checks around 768px and 1024px.
- Desktop check around 1440px.

Do not perform a valid production content update during these checks unless separately approved.

## Post-Deployment Admin Checks

Admin checks:

- `/admin/login` loads.
- `super_admin` can access `/admin/homepage`.
- `admin` can access `/admin/homepage`.
- `article_writer` cannot access `/admin/homepage`.
- Homepage sidebar link appears only for allowed roles.
- Edit screen shows all eight tabs:
  - Hero
  - Stats
  - Trust Bar
  - Difference
  - Services
  - Portfolio
  - Blog
  - Final CTA
- Preview Homepage link works.
- There are no add, delete, or reorder controls.
- Invalid chart height `101` is rejected.
- Invalid validation response preserves entered value and does not update content.

Do not submit a valid production content update without explicit approval.

## Database Read-Only Checks

Use read-only checks after migration/seeding decisions.

```sql
SELECT COUNT(*) AS total_page_sections FROM page_sections;

SELECT COUNT(*) AS home_sections
FROM page_sections
WHERE page_key = 'home';

SELECT section_key, status, sort_order
FROM page_sections
WHERE page_key = 'home'
ORDER BY sort_order;

SELECT page_key, section_key, COUNT(*) AS duplicates
FROM page_sections
GROUP BY page_key, section_key
HAVING COUNT(*) > 1;

SELECT COUNT(*) AS services_count FROM services;
SELECT COUNT(*) AS portfolio_projects_count FROM portfolio_projects;
SELECT COUNT(*) AS blog_articles_count FROM blog_articles;
SELECT COUNT(*) AS site_settings_count FROM site_settings;
```

Expected:

- Home sections count is `8`.
- Duplicate query returns no rows.
- Sort orders are `10, 20, 30, 40, 50, 60, 70, 80`.
- All fixed homepage records are `active`.
- Services, portfolio, blog, and settings counts match the pre-deployment backup baseline.

## Rollback Execution Plan

Rollback only if deployment fails and the operator approves.

### Restore Project Files

From the live project path:

```bash
cd /home/sadekbdt/flsa-welfare
tar -xzf ~/deployment_backups/$STAMP/project_files_before_homepage_admin_$STAMP.tar.gz -C /home/sadekbdt/flsa-welfare
```

If the backup contains the project root itself, restore into the parent directory instead. Confirm archive structure before extraction:

```bash
tar -tzf ~/deployment_backups/$STAMP/project_files_before_homepage_admin_$STAMP.tar.gz | head
```

### Restore Assets

```bash
rm -rf public/build
tar -xzf ~/deployment_backups/$STAMP/public_build_before_homepage_admin_$STAMP.tar.gz -C .
```

Use caution with `rm -rf`; verify the path is exactly the live project's `public/build` first.

### Restore Database

Only if database rollback is required and approved:

```bash
mysql LIVE_DATABASE_NAME < ~/deployment_backups/$STAMP/live_database_before_homepage_admin_$STAMP.sql
```

Prefer a full backup restore over ad hoc SQL repair when deployment has already changed schema/content.

Do not use destructive Laravel reset commands.

### Clear Caches After Rollback

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

If desired after rollback:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Stop Conditions

Stop immediately if:

- Git branch is not the approved deployment branch/commit.
- Local tests fail.
- Local build fails.
- Backup creation fails.
- Backup file is empty.
- Live project path is uncertain.
- Composer is missing and no approved `vendor` upload strategy exists.
- Live PHP version is incompatible.
- `page_sections` home count is between `1` and `7`.
- Duplicate `page_key` plus `section_key` rows exist.
- Migration fails.
- Seeder would overwrite existing production edits without approval.
- `public/build/manifest.json` is missing after upload.
- Public homepage returns an error.
- Admin login or `/admin/homepage` returns an error.
- Any service, portfolio, blog, or settings count changes unexpectedly.

## Destructive Commands Prohibited

Do not run:

```bash
php artisan prosper:install
php artisan migrate:fresh
php artisan migrate:refresh
php artisan migrate:reset
php artisan migrate:rollback
php artisan db:wipe
php artisan db:seed --force
composer update
```

Do not perform a production content update without explicit approval.

## Phase 5L Confirmation

No production server was contacted.

No production command was run.

No migration or seeder was run locally or remotely.

No deployment zip was created in this phase.

No application code or tests were modified.

`php artisan prosper:install` was not run.

The deployment execution checklist is ready for operator review.
