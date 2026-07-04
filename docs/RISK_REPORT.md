# Risk Report

## Duplicate Data Sources
- Services exist in `services`/`service_features`, seeders, `services.blade.php` fallback arrays, `home.blade.php` fallback objects, and footer hard-coded links.
- Portfolio projects exist in `portfolio_projects`, seeders, `portfolio.blade.php` fallback arrays, and `home.blade.php` fallback arrays.
- Blog articles/categories exist in database/seeders, but blog and home views still contain fallback article cards.
- Settings are database-backed, but brand, slogan, footer labels, SEO text, and config fallbacks are still hard-coded in multiple places.
- Privacy policy is seeded into `pages` but public route still renders `resources/views/public/privacy-policy.blade.php`.

## SEO Risks
- Page-level meta sections are hard-coded per Blade file, while default settings also contain SEO fields.
- Blog detail supports Open Graph fields in the model/table, but admin forms do not expose all OG fields.
- Portfolio detail lacks full OG columns, relying mostly on title/description.
- `seo-meta.blade.php` contains hard-coded organization description, fallback address, and service list.
- `public/robots.txt` uses `https://yourdomain.com/sitemap.xml`, which is wrong for `https://prospermedia.online`.
- Sitemap static page priorities/changefreq are hard-coded and may drift from editable pages.

## Route And Slug Compatibility Risks
- Existing public URLs must remain unchanged: `/services/{slug}`, `/portfolio/{slug}`, `/blog/{slug}`, `/privacy-policy`.
- Portfolio category filters currently depend on hard-coded string keys; moving categories to a table must preserve existing query values.
- Contact `service_interest` is an enum-like field with hard-coded validation values; replacing it with dynamic services can break existing submissions unless legacy values are preserved.
- Generated slugs currently do not update on edit in services/blog/portfolio, which helps preserve URLs but may surprise admins.

## Database And Migration Risks
- Domain tables are created by `prosper:install`, not standard migrations. Deployment docs currently mention `migrate` and `db:seed`, which will not create custom domain tables by themselves.
- `prosper:install` drops custom tables before recreating them; it is destructive and unsafe for production content.
- Adding category foreign keys directly to portfolio projects could be risky until existing string categories are reconciled.
- Current default tests use in-memory SQLite and do not include custom Prosper tables.

## Storage And Media Risks
- Updating a setting image deletes the previous file immediately.
- Updating/deleting blog or portfolio featured images deletes files immediately.
- There is no reference tracking, so shared/reused media could be deleted while still displayed elsewhere.
- Portfolio gallery fields exist and display publicly, but admin UI currently does not manage gallery images.
- Upload validation uses `image` and size limits, but does not define explicit MIME allowlists or custom safe filename handling.

## Security Risks
- Public contact form uses Form Request validation and CSRF but no explicit route throttle/spam honeypot.
- Newsletter form uses inline validation and CSRF but no explicit route throttle/spam protection.
- Blog and portfolio rich content render with `{!! !!}`. This requires trusted/sanitized HTML; current admin code does not visibly apply HTMLPurifier despite package availability.
- Several admin CRUD controllers use inline validation rather than dedicated Form Requests.
- No policies were found; authorization is mostly middleware plus controller helper checks.
- Admin signup is publicly available under `/admin/signup` with writer limit logic; this should be reviewed for production.

## Query And Count Risks
- Blog category counts use status `published` but do not require `published_at` to be non-null, while `BlogArticle::published()` does require `published_at`; this can create visible count/list inconsistencies.
- Popular posts query filters `status = published` but does not require `published_at`, also creating possible inconsistency with main article listing.
- Portfolio public index loads all published projects with `get()`; pagination may be needed if the portfolio grows.
- Services index loads all active services; likely fine now but should keep sort/order behavior.

## Admin UX Risks
- Portfolio categories are duplicated in public view, model accessor, controller constants, admin validation, and admin form options.
- Settings form allows image uploads but lacks per-setting validation rules in the model/table.
- Gallery images and Open Graph images are present in schema/view logic but not fully manageable in admin.
- Deleting services/categories/projects/articles is hard delete in many places; soft deletes are not enabled.

## Build And Deployment Risks
- `README.md` is still default Laravel documentation and does not describe the Prosper-specific install flow.
- `DEPLOYMENT.md` instructs `php artisan db:seed --force` but not the destructive nature of `prosper:install`.
- `npm run build` was intentionally not run during audit.
- Production credentials and `.env` were not inspected or modified.

## Highest-Priority Safe Fixes Later
- Introduce non-destructive migrations for missing content tables.
- Seed hard-coded content exactly before switching views.
- Make portfolio categories database-backed while preserving current string slugs.
- Fix blog count queries to match `BlogArticle::published()` semantics.
- Add contact/newsletter throttling and spam protection.
- Add media reference safeguards before expanding image management.
