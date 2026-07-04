# Dynamic Modules Already Present

## Stack And Root
- Project root: `C:\laragon\www\prosper-media`.
- Backend: Laravel `^13.8`, PHP `^8.3`, Blade templates, Laravel session auth, Eloquent models.
- Frontend: Blade + Tailwind CSS `^3.4.17`, Vite `^8.0.0`, Alpine.js `^3.15.12`. No React/Inertia pages were found.
- Key package support: `spatie/laravel-sluggable`, `intervention/image`, `ezyang/htmlpurifier`.

## Public Routes
- `/` -> `App\Http\Controllers\Public\HomeController@index`, `resources/views/public/home.blade.php`.
- `/about` -> `AboutController@index`, `resources/views/public/about.blade.php`.
- `/services` and `/services/{slug}` -> `ServicesController`, dynamic service records.
- `/portfolio` and `/portfolio/{slug}` -> `PortfolioController`, dynamic project records.
- `/blog`, `/blog/category/{slug}`, `/blog/{slug}` -> `BlogController`, dynamic category/article records.
- `/contact` GET/POST -> `ContactController`, contact form persistence.
- `/newsletter/subscribe` POST -> `NewsletterController`, newsletter persistence.
- `/privacy-policy` -> static Blade view.
- `/sitemap.xml` -> `SitemapController`, mixed static page list plus dynamic services/projects/articles/categories.

## Admin Routes
- Admin prefix: `/admin`, route names under `admin.*`, loaded from `routes/admin.php`.
- Guest admin auth: login, signup, forgot password, reset password.
- Authenticated admin modules: dashboard, blog articles, blog categories, services, portfolio, messages, newsletter, users, settings.
- Middleware aliases are registered in `bootstrap/app.php`: `admin.auth`, `admin.guest`, `admin.admin`, `admin.super`, `admin.writer`.

## Database Structure
Normal migration files currently include only Laravel defaults:
- `users`, `password_reset_tokens`, `sessions`.
- `cache`, `cache_locks`.
- `jobs`, `job_batches`, `failed_jobs`.

Prosper Media domain tables are created by `app/Console/Commands/InstallProsperMedia.php`:
- `admin_users`, `admin_password_resets`, `site_settings`, `services`, `service_features`.
- `portfolio_projects`, `blog_categories`, `blog_articles`.
- `contact_messages`, `newsletter_subscribers`, `pages`, `activity_logs`.

## Existing Models
- `SiteSetting`: cached global key/value settings.
- `Service`, `ServiceFeature`: service listings and ordered features.
- `PortfolioProject`: portfolio/case-study records, category enum, JSON technologies/gallery, featured flag.
- `BlogCategory`, `BlogArticle`: blog taxonomy, articles, tags, publish status, author relation.
- `ContactMessage`: public contact leads.
- `NewsletterSubscriber`: public newsletter signups.
- `AdminUser`: custom admin guard user with role/status helpers.
- `ActivityLog`: admin activity tracking helper.
- `Page`: generic page table, currently only seeded for privacy policy but not used by public route.

## Already Database-Driven Website Areas
- Global settings are loaded for every view in `AppServiceProvider` via `SiteSetting::getAllSettings()`.
- Header/footer can display uploaded `site_logo`; fallback brand text remains static in `resources/views/components/public/header.blade.php` and `footer.blade.php`.
- Contact information and social links are partially dynamic through `site_settings` in contact/footer/SEO components.
- Services are dynamic in `ServicesController`, `Service` model, `service_features` relation, admin CRUD, and service detail pages.
- Homepage service cards use dynamic services when records exist; fallback/static content still exists in Blade.
- Portfolio projects/case studies are dynamic through `PortfolioProject`, public portfolio routes, admin portfolio CRUD, featured status, status, sort order, technologies, featured image, project URL, and gallery display fields.
- Blog categories and posts are dynamic through `BlogCategory`, `BlogArticle`, public blog routes, category filtering, search, popular posts, related posts, admin article/category CRUD, publish workflow, tags, views, read time, SEO fields, and featured images.
- Contact leads are stored in `contact_messages` and managed in admin messages.
- Newsletter signups are stored in `newsletter_subscribers` and managed in admin newsletter.
- Sitemap includes dynamic services, portfolio projects, blog posts, and blog categories.
- Admin dashboard uses dynamic counts from blog, portfolio, contact messages, services, users, and subscribers.

## Admin Panel Already Present
- Layout and components: `resources/views/layouts/admin.blade.php`, `components/admin/sidebar.blade.php`, `components/admin/topbar.blade.php`.
- Auth views: `resources/views/admin/auth/*.blade.php`.
- CRUD/list views: services, portfolio, blog articles, blog categories, messages, newsletter, users, settings.
- Roles: `super_admin`, `admin`, `article_writer`.

## Image Upload And Storage Already Present
- Settings images upload to `storage/app/public/uploads/settings`.
- Blog featured images upload to `storage/app/public/uploads/blog`.
- Portfolio featured images upload to `storage/app/public/uploads/portfolio`.
- Existing uploads use Laravel `store(..., 'public')` and display through `Storage::url(...)`.

## SEO Already Present
- Page-level Blade sections define meta title/description on public pages.
- Detail pages use record metadata for services, portfolio projects, and blog articles.
- Blog articles include Open Graph fields in the model/table, but admin article forms currently expose only meta title/description, tags, and read time.
- `resources/views/components/public/seo-meta.blade.php` builds schema data and uses settings for some organization fields.

## Validation, Auth, And Security Already Present
- Contact form uses `app/Http/Requests/Public/ContactFormRequest.php`.
- Admin login/signup use dedicated Form Requests.
- Admin CRUD modules mostly use inline `$request->validate(...)`.
- CSRF tokens are present in Blade forms.
- Admin login has `RateLimiter` protection.
- Security headers middleware is applied to web responses.

## Tests And Build
- Tests are currently Laravel examples only: `tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`.
- Build config: `vite.config.js`, `tailwind.config.js`, `postcss.config.js`, `package.json`.
- Deployment guide exists in `DEPLOYMENT.md`.
