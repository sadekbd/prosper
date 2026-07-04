# Database Plan

Principles:
- Reuse existing tables first.
- Add only non-destructive migrations.
- Preserve existing slugs, URLs, visible content, sort order, statuses, and media paths.
- Seed or migrate current hard-coded copy into database records before views stop using Blade fallback content.

## Reuse Existing Tables
- `site_settings`: continue using for global site name, slogan, logo, favicon, contact details, social URLs, default SEO, analytics IDs, default Open Graph image.
- `services` and `service_features`: continue using for service pages, homepage service previews, footer service links, and contact service-interest options where compatible.
- `portfolio_projects`: continue using for case studies, featured projects, technologies, gallery images, active/draft status, sort order, SEO.
- `blog_categories` and `blog_articles`: continue using for posts, categories, tags, authors, status, publish date, read time, images, SEO.
- `contact_messages`: continue using for contact leads.
- `newsletter_subscribers`: continue using for newsletter signups.
- `pages`: should be used for static pages such as privacy policy and can be extended for page-level metadata.

## Missing Tables

### `page_sections`
Purpose: editable content blocks for fixed public pages while preserving Blade markup/design.

Suggested columns:
- `id`
- `page_key` string indexed, examples: `home`, `about`, `contact`, `services_index`, `portfolio_index`, `blog_index`
- `section_key` string indexed, examples: `hero`, `stats`, `difference`, `trust_bar`, `cta`
- `title` nullable string
- `subtitle` nullable string
- `eyebrow` nullable string
- `body` nullable longText
- `button_label` nullable string
- `button_url` nullable string
- `image` nullable string
- `payload` nullable json for cards, metrics, proof points, dashboard labels, skill bars, trust items
- `status` enum/string: `active`, `inactive`
- `sort_order` unsigned integer
- timestamps

### `navigation_links`
Purpose: editable header/footer labels and links without changing route URLs.

Suggested columns:
- `id`
- `area` string indexed: `header`, `mobile_header`, `footer_primary`, `footer_utility`
- `label` string
- `route_name` nullable string
- `url` nullable string
- `target` nullable string default `_self`
- `status` enum/string: `active`, `inactive`
- `sort_order` unsigned integer
- timestamps

### `portfolio_categories`
Purpose: remove hard-coded portfolio category enums/labels/colors from Blade, controllers, admin forms, and model accessors.

Suggested columns:
- `id`
- `name` string
- `slug` string unique, preserving current keys: `google_ads`, `tracking_setup`, `web_development`, `landing_page`, `automation`
- `color` string nullable
- `description` nullable string/text
- `status` enum/string: `active`, `inactive`
- `sort_order` unsigned integer
- timestamps

Compatibility phase:
- Keep existing `portfolio_projects.category` string column.
- Add lookup by slug first; defer any foreign key conversion until after production data is reconciled.

### `testimonials`
Purpose: target module requested but no current table/model/admin was found.

Suggested columns:
- `id`
- `name` string
- `role` nullable string
- `company` nullable string
- `quote` text
- `rating` nullable unsigned tiny integer
- `image` nullable string
- `status` enum/string: `active`, `inactive`
- `is_featured` boolean default false
- `sort_order` unsigned integer
- timestamps
- optional soft deletes

### `media_assets` Or Media Reference Tracking
Purpose: avoid deleting uploaded media still referenced by another setting, article, project, section, or page.

Suggested columns:
- `id`
- `disk` string default `public`
- `path` string unique
- `mime_type` nullable string
- `size` nullable integer
- `original_name` nullable string
- `uploaded_by` nullable foreign key to `admin_users`
- timestamps

This can be introduced after the first content modules if direct storage paths are kept during initial conversion.

## Existing Tables That Need Non-Destructive Columns

### `pages`
Add if missing:
- `template` nullable string
- `status` already exists
- `sort_order` unsigned integer default 0
- `published_at` nullable timestamp
- `og_description` nullable string

### `site_settings`
Optional additions:
- `sort_order` unsigned integer default 0
- `validation_rules` nullable json
- `help_text` nullable string
- `is_public` boolean default true

### `portfolio_projects`
Optional additions:
- `portfolio_category_id` nullable foreign key to `portfolio_categories`
- `deleted_at` nullable timestamp for soft deletes
- `og_title`, `og_description`, `og_image` to match blog SEO capabilities

### `blog_articles`
Optional additions:
- `deleted_at` nullable timestamp for soft deletes
- `is_featured` boolean default false if featured blog placement is needed.

### `services`
Optional additions:
- `is_featured` boolean default false
- `deleted_at` nullable timestamp for soft deletes

### `contact_messages`
Optional additions:
- `spam_score` nullable decimal/integer
- `source_page` nullable string
- `deleted_at` nullable timestamp for soft deletes

## Content Migration/Seed Sources
- Homepage sections: migrate from `resources/views/public/home.blade.php`.
- About sections: migrate from `resources/views/public/about.blade.php`.
- Contact page intro/stats/options: migrate from `resources/views/public/contact.blade.php`, service options mapped to active `services` when possible.
- Portfolio categories: migrate current keys/labels/colors from `portfolio.blade.php`, `PortfolioProject` accessor, controller constants, and admin forms.
- Navigation/footer: migrate current route labels and footer utility links.
- CTAs/newsletter copy: migrate from each public Blade view into `page_sections`.
- Privacy policy: route should eventually use the seeded `pages` record without changing `/privacy-policy`.
