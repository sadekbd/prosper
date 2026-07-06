# Phase 5K: Production Deployment Preparation And Rollback Plan

Date: 2026-07-06

Scope: production deployment planning only for the dynamic homepage `page_sections` work and homepage admin editor. No production deployment, production connection, migration, seeding, backup creation, package install, build, or database write was performed in this phase.

## Deployment Scope

Included features:

- Dynamic homepage rendering from eight fixed `home` `page_sections` records.
- Homepage normalization layer in `App\Support\HomepageContent`.
- Homepage admin editor at `/admin/homepage`.
- Dirty-only homepage admin saves with activity logging only when material changes occur.
- Sidebar link for `super_admin` and `admin`.
- Public homepage domain-card behavior for services, portfolio, and blog remains sourced from their existing domain tables.

Fixed homepage section keys:

- `hero`
- `stats`
- `trust_bar`
- `difference`
- `services_intro`
- `portfolio_intro`
- `blog_intro`
- `primary_cta`

Not included:

- No service, portfolio, blog, settings, header, footer, SEO, route-name, or domain-table redesign.
- No generic page builder.
- No production content update is approved by this plan.

## Files And Features Reviewed

Reviewed deployment-relevant files:

- `composer.json`
- `package.json`
- `vite.config.js`
- `DEPLOYMENT.md`
- `routes/admin.php`
- `database/migrations/2026_07_04_000001_create_page_sections_table.php`
- `database/seeders/HomePageSectionSeeder.php`
- `app/Support/HomepageContent.php`
- `app/Http/Controllers/Public/HomeController.php`
- `app/Http/Controllers/Admin/HomepageController.php`
- `app/Http/Requests/Admin/UpdateHomepageRequest.php`
- `resources/views/admin/homepage/edit.blade.php`
- `resources/views/public/home.blade.php`
- `resources/views/components/admin/sidebar.blade.php`
- `.env.example`
- Prior Phase 2 through Phase 5 deployment, backup, seed, and regression documents

## Production Requirements

Runtime requirements from project files:

- PHP `^8.3` per `composer.json`.
- Laravel `^13.8`.
- MySQL is expected for production content.
- Composer 2.x if dependencies are installed on the server.
- Node/npm only if assets are built on the server.
- Vite inputs:
  - `resources/css/app.css`
  - `resources/js/app.js`
- Alpine.js is bundled through npm dependencies.

This feature set requires:

- PHP file upload.
- Database migration for `page_sections` if not already present.
- Targeted homepage seeding for the eight fixed records if they are not already present.
- Laravel cache clearing and recaching.
- Vite asset upload or server build only if the deployed asset manifest/build is stale or missing.

This feature set does not require:

- `composer update`.
- Any destructive migration command.
- `php artisan prosper:install`.
- Broad `php artisan db:seed`.
- Production admin form smoke update unless explicitly approved.

## Pre-Deployment Backup Checklist

Before any production deployment command, create and verify:

- Full production database backup.
- Current deployed project file backup or release snapshot.
- Current `public/build` asset backup if assets are replaced.
- Current `.env` backup stored securely outside the web root.

Database backup verification:

- Backup file exists.
- Backup file size is greater than zero.
- Backup can be opened/read by the deployment operator.
- Backup contains these tables when present:
  - `admin_users`
  - `page_sections`
  - `services`
  - `portfolio_projects`
  - `blog_articles`
  - `site_settings`
  - `activity_logs`

Suggested filename convention:

```text
prosper_media_before_homepage_admin_deploy_YYYYMMDD_HHMMSS.sql
```

Suggested storage:

- One copy on the server outside the web root.
- One downloaded/off-server copy.

Do not proceed if the backup cannot be verified.

## Migration Strategy

The production server needs the additive `page_sections` migration:

```bash
php artisan migrate --force
```

Migration behavior:

- Creates `page_sections`.
- Adds a unique key on `page_key` plus `section_key`.
- Does not touch service, portfolio, blog, settings, or admin tables.

Never run these commands for this deployment:

```bash
php artisan migrate:fresh
php artisan migrate:refresh
php artisan migrate:reset
php artisan migrate:rollback
php artisan db:wipe
php artisan prosper:install
```

If migration fails, stop and inspect the error. Do not attempt a reset or reinstall.

## Seeder Strategy

The live server needs eight fixed `home` records in `page_sections`.

The targeted seeder is:

```bash
php artisan db:seed --class=HomePageSectionSeeder --force
```

Important behavior:

- `HomePageSectionSeeder` uses `PageSection::updateOrCreate()`.
- It creates missing fixed records.
- It also updates existing fixed records back to the seeded values.
- It does not seed service, portfolio, blog, or settings records.

Recommended first deployment strategy:

- If production has no `home` `page_sections` records yet, run the targeted seeder once after `php artisan migrate --force`.
- Verify exactly eight `home` records afterward.

Recommended future deployment strategy:

- Do not rerun the seeder after editors have changed production homepage content unless overwriting those eight records back to seed defaults is intentional and approved.
- If production already contains edited `home` `page_sections`, prefer a reviewed manual diff or a purpose-built non-overwriting repair script for missing records only.

Do not run broad seeding:

```bash
php artisan db:seed --force
```

Broad seeding may affect unrelated project data depending on configured seeders.

## File Upload And Build Strategy

Recommended Git flow:

1. Commit and push the reviewed branch.
2. Merge to the deployment branch only after approval.
3. Deploy from a tagged commit or known commit hash.
4. Record the deployed commit hash in the release notes.

Upload/include:

- Application code under `app/`.
- Routes under `routes/`.
- Blade views under `resources/views/`.
- CSS/JS sources if the server builds assets.
- `database/migrations/2026_07_04_000001_create_page_sections_table.php`.
- `database/seeders/HomePageSectionSeeder.php`.
- `composer.json` and `composer.lock`.
- `package.json`, lock file, and Vite config only if building assets on the server.
- New/changed `public/build` assets if building locally.

Do not upload:

- `.env`.
- `.git`.
- `node_modules`.
- `backups`.
- Local `storage/logs`.
- Local cache files under `bootstrap/cache`.
- Local database dumps.
- Tests unless the production deployment workflow intentionally includes them.

Composer options:

- If Composer is available on the server, use:

```bash
composer install --no-dev --optimize-autoloader
```

- Do not run `composer update` during deployment.
- If Composer is unavailable, upload a compatible `vendor` directory only if that is the established server workflow and the local PHP/platform versions match production.

Asset options:

- If Node/npm is available on the server:

```bash
npm ci
npm run build
```

- If Node/npm is not available on the server, build locally from the approved commit and upload `public/build`.
- Do not upload `node_modules`.

Because this phase did not change app CSS or JS source directly, a fresh build may not be necessary if the current production `public/build` already matches the deployed Blade/layout requirements. Still verify the Vite manifest and admin/public assets after upload.

## Live Command Checklist

Use the real project path in place of the placeholder:

```bash
cd /path/to/prosper-media
```

Optional maintenance mode:

```bash
php artisan down --message="Maintenance in progress. Please check back shortly."
```

Install PHP dependencies if Composer is available:

```bash
composer install --no-dev --optimize-autoloader
```

Build assets on server only if that is the chosen workflow:

```bash
npm ci
npm run build
```

Run only the safe migration:

```bash
php artisan migrate --force
```

Run the targeted homepage seeder only when approved by the seeder strategy:

```bash
php artisan db:seed --class=HomePageSectionSeeder --force
```

Clear and rebuild Laravel caches:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If production uses queues/workers:

```bash
php artisan queue:restart
```

If the storage symlink is missing:

```bash
php artisan storage:link
```

Bring the site back:

```bash
php artisan up
```

If any command fails, stop and diagnose before continuing.

## Post-Deployment Public Verification

Public site checks:

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
- Public navigation links work.
- Mobile spot check at approximately 390px.
- Tablet spot check at approximately 768px and 1024px.
- Desktop spot check at approximately 1440px.

Do not perform a production content update during this verification unless separately approved.

## Post-Deployment Admin Verification

Admin checks:

- `/admin/login` works.
- `super_admin` can access `/admin/homepage`.
- `admin` can access `/admin/homepage`.
- `article_writer` cannot access `/admin/homepage`.
- Sidebar `Homepage` link is visible only to `super_admin` and `admin`.
- Edit screen loads all eight tabs:
  - Hero
  - Stats
  - Trust Bar
  - Difference
  - Services
  - Portfolio
  - Blog
  - Final CTA
- Existing production values load.
- Preview Homepage link works.
- Save Homepage button is visible.
- There are no delete, add, or reorder controls for fixed homepage sections.
- Validation rejects an invalid chart height such as `101`.
- Validation preserves entered invalid values without saving them.

Do not submit a valid production content update until the site owner explicitly approves it.

## Database Verification Checks

Use read-only checks first.

Suggested SQL:

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

Expected homepage result:

- `home_sections = 8`.
- Duplicate query returns no rows.
- Fixed sort orders:
  - `hero`: 10
  - `stats`: 20
  - `trust_bar`: 30
  - `difference`: 40
  - `services_intro`: 50
  - `portfolio_intro`: 60
  - `blog_intro`: 70
  - `primary_cta`: 80
- All eight fixed records are `active`.
- Service, portfolio, blog, and settings counts match the pre-deployment baseline.

## Optional Production Smoke Update

This is optional and approval-required.

Recommended only after public/admin read-only verification passes:

1. Record all eight homepage `updated_at` values and payload checksums.
2. Change only `hero.eyebrow` to a temporary safe value.
3. Submit once through `/admin/homepage`.
4. Confirm only `hero.updated_at` changes.
5. Confirm activity log says only `hero`.
6. Confirm public homepage shows the temporary value.
7. Restore the original value immediately.
8. Confirm only `hero.updated_at` changes again.
9. Confirm no temporary text remains.
10. Submit a no-op save only if approved and confirm no rows/logs change.

Do not perform this smoke update without owner approval and a verified backup.

## Rollback Plan

### Code Rollback

Preferred:

- Restore the previous release directory or deployment artifact.
- Or check out the previous known-good commit/tag.
- Clear caches:

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

If cache rebuilding is part of the release process:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Rollback

Preferred:

- Restore the verified pre-deployment database backup.

Limited alternative:

- If `page_sections` did not exist before deployment, no live edits were made, and rollback is approved, remove only `page_sections` through a reviewed table-specific rollback or SQL plan.

Do not use:

```bash
php artisan migrate:fresh
php artisan migrate:refresh
php artisan migrate:reset
php artisan db:wipe
php artisan prosper:install
```

Do not drop `page_sections` if production editors have already created live homepage content that must be preserved.

### Asset Rollback

- Restore the previous `public/build` directory or previous asset release.
- Clear view/cache if Blade or manifest references changed.

### Emergency Cache Commands

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

If route cache causes issues, leave route cache disabled until the route issue is identified.

## Risk List

Deployment risks:

- Live server may not have Composer.
- Live server may not have Node/npm.
- PHP version may be below the required `^8.3`.
- MySQL/MariaDB JSON behavior may differ from local MySQL.
- Route/config/view caches may contain stale code or environment values.
- Opcache/PHP-FPM may need a restart after upload.
- File permissions may block cache writes or uploads.
- Vite manifest may be missing if assets are not built/uploaded correctly.
- `HomePageSectionSeeder` can overwrite edited homepage content if rerun after launch.
- Broad `php artisan db:seed --force` may affect unrelated data.
- Admin role data may differ on production, especially `article_writer` accounts.
- Existing live database may be missing `portfolio_projects` or `blog_articles`; the public controller guards those tables, but the service query expects the existing services domain table.
- `prosper:install` is destructive and must never be used as a deployment repair command.
- Existing line-ending warnings should be handled separately with a reviewed `.gitattributes` policy, not during deployment.
- Maintenance mode custom render view may not exist; use plain `php artisan down` if needed.

## Final Readiness Statement

The dynamic homepage and homepage admin editor are ready for deployment preparation based on:

- Focused homepage integration tests.
- Homepage admin CRUD tests.
- Dirty-only save tests.
- Real local MySQL/browser retest in Phase 5J.
- Full suite result from Phase 5J: 71 tests, 865 assertions.

Deployment should proceed only after:

- A verified production database backup exists.
- A verified current file/release backup exists.
- Migration/seeder strategy is confirmed for the current production database state.
- Build/upload strategy is confirmed for the production server.
- An eligible `super_admin` or `admin` account is available for verification.
- The deployment operator agrees not to run destructive commands.

No production deployment was performed in Phase 5K.

No production database was accessed or modified.

No migrations, seeders, Composer commands, npm commands, builds, or backups were run in this phase.

`php artisan prosper:install` was not run.
