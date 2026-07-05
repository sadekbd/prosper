# Phase 4H: Homepage Blog Intro And Dynamic Blog Article Cards Integration

Date: 2026-07-04

Scope: connect only the homepage Blog preview section shell to `$homeSections->get('blog_intro')` and use the existing `$articles` domain collection for live cards. Final CTA, header, footer, SEO, and previous homepage sections remain unchanged.

## Files Changed

- `resources/views/public/home.blade.php`
- `tests/Feature/HomeBlogIntroBladeIntegrationTest.php`
- `docs/PHASE_4H_HOME_BLOG_INTEGRATION.md`

No controller, route, model, migration, seeder, admin, blog backend, final CTA, header, footer, SEO, or database record was changed.

## Blog Intro Fields Made Dynamic

The Blog preview section now reads from:

```php
$blogIntroSection = $homeSections->get('blog_intro');
```

Dynamic intro fields:

- Eyebrow
- Title
- Subtitle
- Section CTA label
- Section CTA URL
- Card link label

## Existing BlogArticle Source Preserved

Blog cards now use the existing `$articles` collection passed by `HomeController`.

Article records remain owned by the Blog module. The page section payload does not provide article titles, slugs, categories, excerpts, dates, read time, images, status, ordering, or publish state.

## Previous Hard-Coded-Only Behavior

Before this phase, the homepage always rendered the local `$demoArticles` cards and ignored the `$articles` collection already passed by the controller.

## New Live-Record And Fallback Behavior

- If `$articles` contains records, the homepage renders up to the first three in the existing controller order.
- If `$articles` is missing or empty, the homepage renders the existing three demo fallback articles.
- Live articles and fallback articles are not mixed.
- Page-section payload cannot control article count or ordering.

## Exact Fallback Articles Preserved

The fallback articles remain:

- `How to Set Up Server-Side Conversion Tracking in 2025`
- `Server-Side vs Client-Side Tracking: The Complete Comparison`
- `Building High-Converting Laravel Landing Pages That Actually Convert`

Their existing categories, excerpts, dates, order, link behavior, placeholder image, and card markup were preserved.

## Field Compatibility Mapping

Live blog cards map these existing domain fields into the unchanged homepage card markup:

- `title`
- `slug`
- `category.name`, falling back to `category_label` or `category`
- `excerpt`
- `published_at`, falling back to `created_at` only if needed

Card URLs use `route('blog.show', $slug)` when a live slug exists, otherwise `route('blog')`.

The current homepage card design does not display read time or featured images, so those fields remain in the Blog domain and are not added to this homepage card in Phase 4H.

## Category Handling

The integration prefers the existing article `category` relationship and its `name` field. If a controlled object lacks that relation, the local display fallback can use a plain `category_label` or `category` value.

No blog category tables, category storage, filtering, or admin logic were changed.

## Date Formatting

Live article dates prefer `published_at` and render as:

```text
M j, Y
```

This matches the existing homepage fallback style, such as `May 15, 2025`.

Invalid or missing dates render as an empty string to preserve layout without inventing dates.

## Excerpt Handling

Live cards use the existing article `excerpt` field only.

No summary is generated from raw article content in this phase. Excerpts render through escaped Blade output and retain the existing `line-clamp-3` layout.

## URL Safety

The section CTA prefers `route('blog')` for the seeded `/blog` value.

Direct section CTA URLs are accepted only when they are `/`, relative paths beginning with `/`, or `http://` / `https://` URLs. Unsafe schemes such as `javascript:`, `data:`, and `vbscript:` fall back to `route('blog')`.

Live article cards use the existing `blog.show` route and domain slugs.

## Card-Count Behavior

- More than three live articles: only the first three render.
- One or two live articles: only those live records render.
- Empty live collection: all three fallback demo articles render.
- `page_sections` payload cannot add, remove, or reorder blog cards.

## Test Commands And Results

Commands run:

```powershell
php artisan test tests\Feature\HomeBlogIntroBladeIntegrationTest.php
php artisan test tests\Feature\HomePortfolioIntroBladeIntegrationTest.php
php artisan test tests\Feature\HomeServicesIntroBladeIntegrationTest.php
php artisan test tests\Feature\HomeDifferenceBladeIntegrationTest.php
php artisan test tests\Feature\HomeTrustBarBladeIntegrationTest.php
php artisan test tests\Feature\HomeStatsBladeIntegrationTest.php
php artisan test tests\Feature\HomeHeroBladeIntegrationTest.php
php artisan test tests\Feature\HomeControllerPageSectionsTest.php
php artisan test tests\Feature\HomePageSectionSeederTest.php
php artisan test tests\Feature\PageSectionSchemaTest.php
git diff --check
```

Results:

- `HomeBlogIntroBladeIntegrationTest`: passed, 6 tests, 59 assertions.
- `HomePortfolioIntroBladeIntegrationTest`: passed, 6 tests, 58 assertions.
- `HomeServicesIntroBladeIntegrationTest`: passed, 5 tests, 40 assertions.
- `HomeDifferenceBladeIntegrationTest`: passed, 5 tests, 93 assertions.
- `HomeTrustBarBladeIntegrationTest`: passed, 5 tests, 91 assertions.
- `HomeStatsBladeIntegrationTest`: passed, 4 tests, 54 assertions.
- `HomeHeroBladeIntegrationTest`: passed, 3 tests, 45 assertions.
- `HomeControllerPageSectionsTest`: passed, 2 tests, 38 assertions.
- `HomePageSectionSeederTest`: passed, 1 test, 53 assertions.
- `PageSectionSchemaTest`: passed, 2 tests, 24 assertions.
- `git diff --check`: no whitespace errors.

Warnings:

```text
warning: in the working copy of 'app/Http/Controllers/Public/HomeController.php', CRLF will be replaced by LF the next time Git touches it
warning: in the working copy of 'resources/views/public/home.blade.php', CRLF will be replaced by LF the next time Git touches it
```

## Behavior Confirmation

The prior homepage sections remain covered by their focused tests and passed after this phase:

- Hero
- Stats
- Trust bar
- Difference
- Services intro
- Portfolio preview

Unchanged later homepage sections:

- Final CTA

Header, footer, and SEO rendering were not modified.

## Database Confirmation

No migrations or seeders were run against local MySQL in this phase. No database records were inserted, updated, or deleted.

## Manual Visual Comparison Checklist

Before advancing, compare the Blog preview section against the previous render:

- Eyebrow reads `Knowledge Hub`.
- Title reads `Latest Articles`.
- Subtitle text and wrapping remain consistent.
- Section CTA reads `All Articles` and links to Blog.
- Live blog articles render when provided.
- Empty articles still render the three existing demo fallback articles.
- Card link label reads `Read Article`.
- Category label, date placement, placeholder icon, excerpt clamp, spacing, hover behavior, and responsive grid remain unchanged.
- One or two live records do not mix with fallback cards.
- More than three live records do not render extra cards.

## Next-Phase Recommendation

Continue with the Final CTA section in another narrow Blade-only pass, preserving local fallbacks, payload validation, URL safety, and focused tests.
