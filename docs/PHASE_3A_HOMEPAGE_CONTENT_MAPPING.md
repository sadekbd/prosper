# Phase 3A: Homepage Static Content Mapping

Date: 2026-07-04

Scope: inventory and seed mapping only. No application code, Blade views, controllers, routes, models, migrations, admin pages, styles, assets, database records, seeders, or build output were changed.

## Safety Confirmation

- `page_sections` already exists in the local `prosper_media` database.
- No `page_sections` records were inserted during this phase.
- `php artisan prosper:install` was not run and must never be run against an existing database.
- Existing dynamic services, portfolio projects, blog articles, and site settings must remain in their current domain tables.
- Public homepage design, routes, animations, markup, and behavior remain unchanged.

## Files Inspected

- `resources/views/public/home.blade.php`
- `app/Http/Controllers/Public/HomeController.php`
- `resources/views/components/public/header.blade.php`
- `resources/views/components/public/footer.blade.php`
- `resources/views/components/public/seo-meta.blade.php`
- `app/Models/Service.php`
- `app/Models/PortfolioProject.php`
- `app/Models/BlogArticle.php`

## Homepage Section Order

| Sort | Proposed key | Visible purpose | Source | Classification |
| ---: | --- | --- | --- | --- |
| 10 | `hero` | Main homepage hero copy, CTAs, dashboard mockup | `resources/views/public/home.blade.php:27-55`, `:84-143` | Static |
| 20 | `stats` | Hero statistics row | `resources/views/public/home.blade.php:59-73` | Static repeated content |
| 30 | `trust_bar` | Technology/platform trust strip | `resources/views/public/home.blade.php:159-183` | Static repeated content |
| 40 | `difference` | "Why Choose Us" section and three cards | `resources/views/public/home.blade.php:188-256` | Static repeated content |
| 50 | `services_intro` | Services preview heading and CTA | `resources/views/public/home.blade.php:261-331` | Partially dynamic |
| 60 | `portfolio_intro` | Portfolio preview heading and CTA | `resources/views/public/home.blade.php:336-413` | Static cards with unused dynamic dependency |
| 70 | `blog_intro` | Blog preview heading and CTA | `resources/views/public/home.blade.php:418-473` | Static cards with unused dynamic dependency |
| 80 | `primary_cta` | Final CTA and proof points | `resources/views/public/home.blade.php:478-521` | Static repeated content |

No rendered homepage newsletter section exists, so no `newsletter` page section is proposed for `page_key = home`.

## Dynamic Dependencies

`app/Http/Controllers/Public/HomeController.php:13-26` currently passes:

- `$services`: `Service::active()->take(3)->get()`
- `$portfolios`: `PortfolioProject::published()->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc')->take(3)->get()` when `portfolio_projects` exists
- `$articles`: `BlogArticle::with('category')->published()->take(3)->get()` when `blog_articles` exists

Current render behavior:

- Services are rendered from `$services` when records exist, with hard-coded Blade fallback records only when empty.
- Portfolio projects are currently not rendered from `$portfolios`; the Blade always renders `$demoPortfolio`.
- Blog articles are currently not rendered from `$articles`; the Blade always renders `$demoArticles`.

Future homepage work should preserve the existing service module and should use existing portfolio/blog modules rather than copying project or article records into `page_sections`.

## Proposed Page Section Records

All proposed records use:

- `page_key`: `home`
- `status`: `active`

### `hero`

Source: `resources/views/public/home.blade.php:27-55`, `:84-143`

Mapping:

| Field | Value |
| --- | --- |
| `section_key` | `hero` |
| `sort_order` | `10` |
| `eyebrow` | `Be Optimistic` |
| `title` | `Engineering Digital Success with Technical Precision.` |
| `subtitle` | `Professional Google Ads management, conversion tracking, and high-performance web development built to turn visitors into loyal customers.` |
| `body` | `null` |
| `button_label` | `View Our Services` |
| `button_url` | `/services` from `route('services')` |
| `image` | `null` |

Payload shape:

```json
{
  "title_lines": ["Engineering Digital", "Success", "with", "Technical Precision."],
  "secondary_button": {
    "label": "Get a Free Audit",
    "url": "/contact",
    "route": "contact"
  },
  "dashboard": {
    "eyebrow": "Live Dashboard",
    "title": "Q2 Campaign Performance",
    "status": "Live",
    "metrics": [
      {"label": "Conversions", "value": "1,248", "change": "↑ 34%", "color": "text-pm-cyan"},
      {"label": "ROAS", "value": "4.8x", "change": "↑ 12%", "color": "text-pm-gold"},
      {"label": "CTR", "value": "7.2%", "change": "↑ 8%", "color": "text-white"}
    ],
    "chart": {
      "label": "Weekly Conversions",
      "days": ["Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
      "bar_heights": [35, 55, 42, 70, 60, 85, 75]
    },
    "tracking": {
      "label": "Tracking:",
      "items": ["GTM", "GA4", "Meta API", "Server-Side"]
    },
    "floating_badges": [
      {"label": "ROI", "value": "↑ 340%"},
      {"label": "Tracking Active"}
    ]
  }
}
```

### `stats`

Source: `resources/views/public/home.blade.php:59-73`

Mapping:

| Field | Value |
| --- | --- |
| `section_key` | `stats` |
| `sort_order` | `20` |
| `eyebrow` | `null` |
| `title` | `null` |
| `subtitle` | `null` |
| `body` | `null` |
| `button_label` | `null` |
| `button_url` | `null` |
| `image` | `null` |

Payload shape:

```json
{
  "items": [
    {"value": 150, "suffix": "+", "label": "Projects Done"},
    {"value": 98, "suffix": "%", "label": "Client Satisfaction"},
    {"value": 5, "suffix": "x", "label": "Average ROAS"},
    {"value": 3, "suffix": "+", "sep": "yr", "label": "Experience"}
  ]
}
```

### `trust_bar`

Source: `resources/views/public/home.blade.php:159-183`

Mapping:

| Field | Value |
| --- | --- |
| `section_key` | `trust_bar` |
| `sort_order` | `30` |
| `eyebrow` | `null` |
| `title` | `Technologies & Platforms We Master` |
| `subtitle` | `null` |
| `body` | `null` |
| `button_label` | `null` |
| `button_url` | `null` |
| `image` | `null` |

Payload shape:

```json
{
  "items": [
    {"label": "Google Ads", "color": "#4285F4"},
    {"label": "Tag Manager", "color": "#F57C00"},
    {"label": "Meta Pixel", "color": "#1877F2"},
    {"label": "Laravel", "color": "#FF2D20"},
    {"label": "MySQL", "color": "#4479A1"},
    {"label": "Analytics GA4", "color": "#E37400"},
    {"label": "PHP 8", "color": "#777BB4"}
  ]
}
```

### `difference`

Source: `resources/views/public/home.blade.php:188-256`

Mapping:

| Field | Value |
| --- | --- |
| `section_key` | `difference` |
| `sort_order` | `40` |
| `eyebrow` | `Why Choose Us` |
| `title` | `The Prosper Media Difference` |
| `subtitle` | `We combine deep technical expertise with marketing intelligence to deliver results others simply can't match.` |
| `body` | `null` |
| `button_label` | `null` |
| `button_url` | `null` |
| `image` | `null` |

Payload shape:

```json
{
  "cards": [
    {
      "badge": "No. 01",
      "color": "cyan",
      "icon_path": "M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4",
      "title": "Tech-First Marketing",
      "body": "We code the tracking setup that other agencies miss. Every pixel, every event, every conversion — captured with precision using GTM, server-side tracking, and custom API integrations."
    },
    {
      "badge": "No. 02",
      "color": "gold",
      "icon_path": "M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z",
      "title": "ROI Focused",
      "body": "Every click is treated as an investment, not an expense. We obsess over ROAS, CPA, and conversion rates — building campaigns that compound in profitability over time."
    },
    {
      "badge": "No. 03",
      "color": "cyan",
      "icon_path": "M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z",
      "title": "Transparent Data",
      "body": "Clear reporting, measurable results, and honest technical support. You always know exactly what is happening with your campaigns and why it's working."
    }
  ]
}
```

### `services_intro`

Source: `resources/views/public/home.blade.php:261-331`

Mapping:

| Field | Value |
| --- | --- |
| `section_key` | `services_intro` |
| `sort_order` | `50` |
| `eyebrow` | `What We Do` |
| `title` | `Our Core Services` |
| `subtitle` | `Three pillars of technical excellence powering your entire digital growth engine.` |
| `body` | `null` |
| `button_label` | `View All Services` |
| `button_url` | `/services` from `route('services')` |
| `image` | `null` |

Payload shape:

```json
{
  "dynamic_source": {
    "model": "App\\Models\\Service",
    "controller_variable": "services",
    "scope": "active",
    "limit": 3
  },
  "card_link_label": "Learn More",
  "card_icons": [
    "M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z",
    "M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z",
    "M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
  ]
}
```

The actual service cards belong in `services`, not `page_sections`.

### `portfolio_intro`

Source: `resources/views/public/home.blade.php:336-413`

Mapping:

| Field | Value |
| --- | --- |
| `section_key` | `portfolio_intro` |
| `sort_order` | `60` |
| `eyebrow` | `Our Work` |
| `title` | `Recent Projects` |
| `subtitle` | `Real results from real campaigns. See how we engineer digital success.` |
| `body` | `null` |
| `button_label` | `All Projects` |
| `button_url` | `/portfolio` from `route('portfolio')` |
| `image` | `null` |

Payload shape:

```json
{
  "dynamic_source": {
    "model": "App\\Models\\PortfolioProject",
    "controller_variable": "portfolios",
    "scope": "published",
    "limit": 3,
    "current_status": "passed by controller but not rendered by Blade"
  },
  "card_link_label": "View Case Study"
}
```

The current hard-coded demo project cards should be treated as portfolio module fallback/source content, not page section content.

### `blog_intro`

Source: `resources/views/public/home.blade.php:418-473`

Mapping:

| Field | Value |
| --- | --- |
| `section_key` | `blog_intro` |
| `sort_order` | `70` |
| `eyebrow` | `Knowledge Hub` |
| `title` | `Latest Articles` |
| `subtitle` | `Practical insights, tutorials and guides from our technical team.` |
| `body` | `null` |
| `button_label` | `All Articles` |
| `button_url` | `/blog` from `route('blog')` |
| `image` | `null` |

Payload shape:

```json
{
  "dynamic_source": {
    "model": "App\\Models\\BlogArticle",
    "controller_variable": "articles",
    "scope": "published",
    "limit": 3,
    "current_status": "passed by controller but not rendered by Blade"
  },
  "card_link_label": "Read Article"
}
```

The current hard-coded demo article cards should be treated as blog module fallback/source content, not page section content.

### `primary_cta`

Source: `resources/views/public/home.blade.php:478-521`

Mapping:

| Field | Value |
| --- | --- |
| `section_key` | `primary_cta` |
| `sort_order` | `80` |
| `eyebrow` | `Let's Work Together` |
| `title` | `Ready to scale your business?\nBe Optimistic.` |
| `subtitle` | `We've got the data covered. Let's engineer your digital success together with the precision your business deserves.` |
| `body` | `null` |
| `button_label` | `Start Growing Today` |
| `button_url` | `/contact` from `route('contact')` |
| `image` | `null` |

Payload shape:

```json
{
  "title_lines": ["Ready to scale your business?", "Be Optimistic."],
  "secondary_button": {
    "label": "See Our Work",
    "url": "/portfolio",
    "route": "portfolio"
  },
  "proof_points": [
    "No Long-Term Contracts",
    "Free Audit Consultation",
    "ROI-Focused Approach",
    "100% Transparent Reporting"
  ]
}
```

## Static And Fallback Content Not To Copy Into Page Sections

### Existing service module records

Keep these in `services` and `service_features`.

Current homepage fallback source: `resources/views/public/home.blade.php:280-287`

Fallback services:

```json
[
  {
    "title": "Google Ads Mastery",
    "subtitle": "Maximise your ROI with data-driven search advertising.",
    "slug": "google-ads-mastery"
  },
  {
    "title": "Advanced Conversion Tracking",
    "subtitle": "Stop guessing and start measuring every touchpoint.",
    "slug": "advanced-conversion-tracking"
  },
  {
    "title": "Professional Web Development",
    "subtitle": "High-performance websites built for conversion.",
    "slug": "professional-web-development"
  }
]
```

Reason: service cards are domain records and are already rendered dynamically when `$services` contains active records.

### Existing portfolio module records

Keep these in `portfolio_projects`.

Current hard-coded homepage cards: `resources/views/public/home.blade.php:358-364`

```json
[
  {
    "title": "E-Commerce Google Ads Overhaul",
    "category": "google_ads",
    "description": "Full-funnel campaign with audience segmentation, dynamic remarketing, and Smart Bidding strategy.",
    "result": "320% ROAS — 3 Months",
    "tech": ["Google Ads", "GTM", "GA4"]
  },
  {
    "title": "GTM + Meta CAPI Server Tracking",
    "category": "tracking_setup",
    "description": "Server-side Facebook Conversion API setup eliminating browser data loss from iOS 14+ changes.",
    "result": "85% Data Recovery",
    "tech": ["Meta CAPI", "GTM", "Node.js"]
  },
  {
    "title": "Laravel SaaS Landing Page",
    "category": "web_development",
    "description": "High-converting, mobile-first landing page with A/B testing, heatmaps, and speed optimization.",
    "result": "CTR: 4.2% → 11.8%",
    "tech": ["Laravel", "MySQL", "Tailwind"]
  }
]
```

Reason: project cards are portfolio records. The controller already prepares `$portfolios`, but the Blade currently renders hard-coded demo cards instead.

### Existing blog module records

Keep these in `blog_articles` and `blog_categories`.

Current hard-coded homepage cards: `resources/views/public/home.blade.php:434-440`

```json
[
  {
    "category": "Google Ads Tips",
    "title": "How to Set Up Server-Side Conversion Tracking in 2025",
    "excerpt": "A complete guide covering GTM server containers, transport URL setup, and debugging server-side tags for maximum conversion data accuracy.",
    "date": "May 15, 2025"
  },
  {
    "category": "Tracking Guides",
    "title": "Server-Side vs Client-Side Tracking: The Complete Comparison",
    "excerpt": "Understanding the technical difference between client-side pixels and server-side tracking and when each approach maximises your data accuracy.",
    "date": "May 8, 2025"
  },
  {
    "category": "Web Dev Tutorials",
    "title": "Building High-Converting Laravel Landing Pages That Actually Convert",
    "excerpt": "The technical and psychological framework behind landing pages that convert at 3x the industry average using Laravel and modern front-end techniques.",
    "date": "Apr 28, 2025"
  }
]
```

Reason: article cards are blog records. The controller already prepares `$articles`, but the Blade currently renders hard-coded demo cards instead.

### Site settings, header, footer, and SEO metadata

Keep global content in settings or a future global/navigation module, not homepage `page_sections`.

- `resources/views/components/public/header.blade.php:23-33`: logo fallback, `ProsperMedia`, `Be Optimistic`
- `resources/views/components/public/header.blade.php:41-49`: navigation labels and route names
- `resources/views/components/public/header.blade.php:85-92`, `:145-148`: `Get a Free Audit`
- `resources/views/components/public/footer.blade.php:17-33`: logo fallback and footer brand copy
- `resources/views/components/public/footer.blade.php:39-44`: social platform labels/icons around dynamic URLs
- `resources/views/components/public/footer.blade.php:63-103`: footer quick links and service links
- `resources/views/components/public/footer.blade.php:106-185`: contact labels, CTA, copyright, utility links
- `resources/views/components/public/seo-meta.blade.php:35-60`: organization schema name, description, address fallback, and service list
- `resources/views/public/home.blade.php:3-4`: homepage meta title and description

Homepage meta fields are page-level SEO content. They should be handled by a future SEO/page metadata strategy rather than mixed into visible homepage sections.

## Duplicate Source Warnings

- `database/seeders/ServiceSeeder.php` overlaps with service fallback cards in `resources/views/public/home.blade.php:280-287` and service page fallback content.
- `database/seeders/PortfolioSeeder.php` overlaps conceptually with homepage demo project cards, though homepage cards are currently always hard-coded.
- `database/seeders/BlogArticleSeeder.php` overlaps conceptually with homepage demo article cards, though homepage cards are currently always hard-coded.
- `database/seeders/SiteSettingsSeeder.php` overlaps with header/footer brand fallback and SEO/settings fallback content.
- `HomeController` prepares `$portfolios` and `$articles`, but the homepage Blade does not use them yet. Future work should fix the data source mismatch without changing visual design.
- Header/footer navigation and footer service links duplicate route and service-label content. They should not be duplicated into homepage `page_sections`.

## Proposed Future Seed Order

1. Verify existing `services`, `portfolio_projects`, `blog_articles`, and `site_settings` records remain intact.
2. Seed the eight `home` page sections in this order: `hero`, `stats`, `trust_bar`, `difference`, `services_intro`, `portfolio_intro`, `blog_intro`, `primary_cta`.
3. Keep service cards sourced from active `services` records.
4. Keep portfolio cards sourced from published `portfolio_projects` records after the Blade is safely switched from `$demoPortfolio`.
5. Keep blog cards sourced from published `blog_articles` records after the Blade is safely switched from `$demoArticles`.
6. Remove Blade fallback arrays only after equivalent database/module content is present and verified.

## Verification Checklist For Future Implementation

- Compare hero eyebrow, line breaks, body text, and both CTA labels/links against the current homepage.
- Compare all four statistic values, suffixes, and labels.
- Compare trust bar item order, labels, and colors.
- Compare all difference cards, badges, icons, titles, and body copy.
- Confirm services section still shows active service records in the same card design and order.
- Confirm portfolio preview shows published/featured portfolio records without changing cards, badges, result labels, technology tags, or links.
- Confirm blog preview shows published blog records without changing card layout, dates, category labels, excerpts, or links.
- Compare final CTA headline line break, copy, buttons, and proof point order.
- Verify header, footer, SEO schema, and site settings are unchanged unless handled by a separate approved phase.
- Verify no public route URLs change.
- Verify no mobile or desktop spacing, animation, or Tailwind classes change.

## No-Change Confirmation

This phase created only this documentation file:

- `docs/PHASE_3A_HOMEPAGE_CONTENT_MAPPING.md`

No application code, database records, routes, controllers, Blade views, models, migrations, admin pages, styles, packages, build files, or storage files were modified.
