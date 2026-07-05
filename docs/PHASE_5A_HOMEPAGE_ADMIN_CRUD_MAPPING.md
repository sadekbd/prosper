# Phase 5A: Homepage Page Sections Admin CRUD Mapping

Date: 2026-07-06

Scope: audit the existing admin architecture and map a safe admin editing workflow for the eight fixed homepage `page_sections` records. No CRUD implementation was created in this phase.

## Files Reviewed

- `routes/admin.php`
- `resources/views/layouts/admin.blade.php`
- `resources/views/components/admin/sidebar.blade.php`
- `resources/views/components/admin/topbar.blade.php`
- `app/Http/Controllers/Admin/*`
- `resources/views/admin/*`
- `app/Http/Middleware/AdminAuth.php`
- `app/Http/Middleware/AdminOrAbove.php`
- `app/Http/Middleware/SuperAdminOnly.php`
- `app/Models/AdminUser.php`
- `app/Models/ActivityLog.php`
- `app/Models/PageSection.php`
- `app/Http/Controllers/Public/HomeController.php`
- `resources/views/public/home.blade.php`
- `database/seeders/HomePageSectionSeeder.php`
- Existing focused homepage tests

## Existing Admin Architecture Findings

Admin routes are grouped under `Route::prefix('admin')->name('admin.')`.

The authenticated admin area uses `admin.auth`, then narrower role middleware:

- `admin.admin`: `super_admin` and `admin`
- `admin.super`: `super_admin` only
- Blog article routes are available to all authenticated admin users, with controller-level writer restrictions.

Existing route names are concise module names such as:

- `admin.services`
- `admin.services.update`
- `admin.portfolio`
- `admin.settings`
- `admin.settings.update`

Controller namespace convention is `App\Http\Controllers\Admin`.

View convention is `resources/views/admin/{module}`. Single-screen settings use `resources/views/admin/settings/index.blade.php`.

The admin layout provides:

- Dark shell with sidebar and topbar.
- `@yield('title')` and `@yield('page-title')` style topbar content.
- Global success and error flash messages in the layout.
- Alpine.js support through existing app assets.
- `@stack('scripts')` for page-specific scripts.

Common form patterns:

- Dark cards: `bg-[#1a2540] border border-white/5 rounded-2xl p-6`
- Inputs: `bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm`
- Help/error text: small gray or red text below fields.
- Validation errors are rendered with `@error(...)`.
- Repeatable fields already exist in service create/edit forms through Alpine `x-data` and `x-for`.

Delete/update confirmation patterns are simple native `onsubmit="return confirm(...)"` confirmations. Homepage sections should not expose delete actions.

Activity logging is used by services, blog articles, users, and some portfolio actions through `ActivityLog::log(...)`. Settings currently save without an activity log.

Pagination is used on list screens, not relevant to a fixed homepage edit screen.

## Recommended URL And Route Structure

Recommended URL:

```text
/admin/homepage
```

Recommended route names:

```php
admin.homepage.edit
admin.homepage.update
```

Recommended routes:

```php
Route::middleware('admin.admin')->prefix('homepage')->group(function () {
    Route::get('/', [HomepageController::class, 'edit'])->name('homepage.edit');
    Route::put('/', [HomepageController::class, 'update'])->name('homepage.update');
});
```

Rationale:

- It follows the existing single-screen settings pattern.
- It is editor-friendly and does not expose table implementation names.
- `/admin/page-sections/home` is too database-centric.
- `/admin/pages/home` implies a generic page builder that does not exist.
- `/admin/content/homepage` adds a new route grouping convention not currently used.

## Controller Plan

Recommended controller:

```php
App\Http\Controllers\Admin\HomepageController
```

Methods:

- `edit()`
- `update(UpdateHomepageRequest $request)`

`edit()` should:

- Load `PageSection` records with one query:
  - `forPage('home')`
  - `ordered()`
  - `get()`
  - `keyBy('section_key')`
- Merge loaded records with a fixed homepage section schema/default map.
- Render one edit screen.
- Never create records during read-only edit rendering.

`update()` should:

- Use one validated nested request.
- Transform validated form data into the existing eight `page_sections` shapes.
- Wrap all eight writes in one `DB::transaction()`.
- Use `PageSection::updateOrCreate()` with:
  - `page_key`
  - `section_key`
- Preserve fixed `status = active`.
- Preserve fixed `sort_order` values 10 through 80.
- Redirect to `admin.homepage.edit`.
- Flash success through the existing layout.

Missing-record handling:

- Missing fixed records should be recreated on update through `updateOrCreate`.
- Missing records should not block the edit page; the form should display safe defaults.

Activity logging:

- Recommended because this changes public content:

```php
ActivityLog::log(
    'updated_homepage_sections',
    'PageSection',
    null,
    'Updated homepage page sections'
);
```

If the implementation tests do not create `activity_logs`, tests should either create a minimal activity log schema or assert through the app path that includes it. Do not remove logging just to simplify tests.

## Authorization Plan

Recommended role access:

- `super_admin`: allowed
- `admin`: allowed
- `article_writer`: denied

Recommended middleware:

```php
admin.auth
admin.admin
```

This matches Services, Portfolio, Categories, Messages, and Newsletter. A new policy system is not needed for this fixed admin screen.

## Form Structure

Use one homepage edit screen with fixed sections. Editors should not add, delete, or reorder sections.

Recommended field display:

- Fixed section tabs or accordion panels.
- Fixed item counts for stats, trust bar, difference cards, hero chart days, hero chart bars, and final CTA proof points.
- No add/remove buttons for fixed homepage items.
- Inputs should keep domain records separate from page section content.

### Hero Fields

| Label | Column / Payload Path | Input | Rule | Max | Required | Reorder | Hidden |
| --- | --- | --- | --- | ---: | --- | --- | --- |
| Eyebrow | `eyebrow` | text | plain text | 80 | yes | no | no |
| Title line 1 | `payload.title_lines.0` | text | plain text | 120 | yes | no | no |
| Title line 2 | `payload.title_lines.1` | text | plain text | 120 | yes | no | no |
| Title line 3 | `payload.title_lines.2` | text | plain text | 120 | yes | no | no |
| Title line 4 | `payload.title_lines.3` | text | plain text | 120 | yes | no | no |
| Full title | `title` | generated hidden | joined/generated text | 255 | yes | no | yes |
| Subtitle | `subtitle` | textarea | plain text | 500 | yes | no | no |
| Primary CTA label | `button_label` | text | plain text | 50 | yes | no | no |
| Primary CTA route | derived from `button_url` | select | known route | 40 | yes | no | no |
| Primary CTA URL | `button_url` | hidden/generated or text | safe URL | 255 | yes | no | hide if route select |
| Secondary CTA label | `payload.secondary_button.label` | text | plain text | 50 | yes | no | no |
| Secondary CTA route | `payload.secondary_button.route` | select | known route | 40 | yes | no | no |
| Secondary CTA URL | `payload.secondary_button.url` | hidden/generated or text | safe URL | 255 | yes | no | hide if route select |
| Dashboard eyebrow | `payload.dashboard.eyebrow` | text | plain text | 80 | yes | no | no |
| Dashboard title | `payload.dashboard.title` | text | plain text | 120 | yes | no | no |
| Dashboard status | `payload.dashboard.status` | text | plain text | 40 | yes | no | no |
| Metric label | `payload.dashboard.metrics.*.label` | text | plain text | 60 | yes | fixed 3 | no |
| Metric value | `payload.dashboard.metrics.*.value` | text | plain text metric | 40 | yes | fixed 3 | no |
| Metric change | `payload.dashboard.metrics.*.change` | text | plain text metric | 40 | yes | fixed 3 | no |
| Metric color | `payload.dashboard.metrics.*.color` | select | allowlist | 20 | yes | fixed 3 | no |
| Chart label | `payload.dashboard.chart.label` | text | plain text | 80 | yes | no | no |
| Chart day labels | `payload.dashboard.chart.days.*` | text | plain text | 10 | yes | fixed 7 | no |
| Chart heights | `payload.dashboard.chart.bar_heights.*` | number | integer 0-100 | n/a | yes | fixed 7 | no |
| Tracking label | `payload.dashboard.tracking.label` | text | plain text | 40 | yes | no | no |
| Tracking item | `payload.dashboard.tracking.items.*` | text | plain text | 40 | yes | fixed current count | no |
| Floating badge label | `payload.dashboard.floating_badges.*.label` | text | plain text | 40 | yes | fixed 2 | no |
| Floating badge value | `payload.dashboard.floating_badges.*.value` | text | plain text | 40 | optional | fixed 2 | no |

Help text: "Hero title line breaks affect the current highlighted layout. Keep four lines unless design is updated."

### Stats Fields

Exactly four fixed rows.

| Label | Path | Input | Rule | Max | Required | Reorder |
| --- | --- | --- | --- | ---: | --- | --- |
| Value | `payload.items.*.value` | text | numeric or numeric-like safe metric | 20 | yes | no |
| Suffix | `payload.items.*.suffix` | select | one of `+`, `%`, `x`, empty | 2 | optional | no |
| Separator | `payload.items.*.sep` | text/select | empty or short alpha text | 5 | optional | no |
| Label | `payload.items.*.label` | text | plain text | 80 | yes | no |

Help text: "Stats always render four items. Counts and order are fixed for layout stability."

### Trust Bar Fields

Exactly seven fixed rows.

| Label | Path | Input | Rule | Max | Required | Reorder |
| --- | --- | --- | --- | ---: | --- | --- |
| Section title | `title` | text | plain text | 120 | yes | no |
| Item label | `payload.items.*.label` | text | plain text | 60 | yes | no |
| Dot color | `payload.items.*.color` | select | approved hex color | 7 | yes | no |

Approved colors:

- `#4285F4`
- `#F57C00`
- `#1877F2`
- `#FF2D20`
- `#4479A1`
- `#E37400`
- `#777BB4`

### Difference Fields

Exactly three fixed cards.

| Label | Column / Path | Input | Rule | Max | Required | Reorder |
| --- | --- | --- | --- | ---: | --- | --- |
| Eyebrow | `eyebrow` | text | plain text | 80 | yes | no |
| Title | `title` | text | plain text | 120 | yes | no |
| Subtitle | `subtitle` | textarea | plain text | 500 | yes | no |
| Card badge | `payload.cards.*.badge` | text | plain text | 20 | yes | no |
| Card color | `payload.cards.*.color` | select | `cyan` or `gold` | 10 | yes | no |
| Icon path | `payload.cards.*.icon_path` | textarea | safe SVG path data | 1000 | yes | no |
| Card title | `payload.cards.*.title` | text | plain text | 100 | yes | no |
| Card body | `payload.cards.*.body` | textarea | plain text | 1000 | yes | no |

Help text: "Icon path accepts SVG path data only, not full SVG markup."

### Services Intro Fields

| Label | Column / Path | Input | Rule | Max | Required | Reorder |
| --- | --- | --- | --- | ---: | --- | --- |
| Eyebrow | `eyebrow` | text | plain text | 80 | yes | no |
| Title | `title` | text | plain text | 120 | yes | no |
| Subtitle | `subtitle` | textarea | plain text | 500 | yes | no |
| CTA label | `button_label` | text | plain text | 50 | yes | no |
| CTA route | derived from `button_url` | select | known route | 40 | yes | no |
| CTA URL | `button_url` | hidden/generated or text | safe URL | 255 | yes | no |
| Card link label | `payload.card_link_label` | text | plain text | 40 | yes | no |
| Icon path 1-3 | `payload.card_icons.*` | textarea | safe SVG path data | 1000 | yes | fixed 3 |

Hidden from editors:

- Service titles
- Service descriptions
- Service features
- Service slugs
- Service ordering
- Service count

### Portfolio Intro Fields

| Label | Column / Path | Input | Rule | Max | Required | Reorder |
| --- | --- | --- | --- | ---: | --- | --- |
| Eyebrow | `eyebrow` | text | plain text | 80 | yes | no |
| Title | `title` | text | plain text | 120 | yes | no |
| Subtitle | `subtitle` | textarea | plain text | 500 | yes | no |
| CTA label | `button_label` | text | plain text | 50 | yes | no |
| CTA route | derived from `button_url` | select | known route | 40 | yes | no |
| CTA URL | `button_url` | hidden/generated or text | safe URL | 255 | yes | no |
| Card link label | `payload.card_link_label` | text | plain text | 50 | yes | no |

Hidden from editors on this screen:

- Portfolio project titles
- Slugs
- Categories
- Technologies
- Result fields
- Featured/published state
- Query limit and ordering

### Blog Intro Fields

| Label | Column / Path | Input | Rule | Max | Required | Reorder |
| --- | --- | --- | --- | ---: | --- | --- |
| Eyebrow | `eyebrow` | text | plain text | 80 | yes | no |
| Title | `title` | text | plain text | 120 | yes | no |
| Subtitle | `subtitle` | textarea | plain text | 500 | yes | no |
| CTA label | `button_label` | text | plain text | 50 | yes | no |
| CTA route | derived from `button_url` | select | known route | 40 | yes | no |
| CTA URL | `button_url` | hidden/generated or text | safe URL | 255 | yes | no |
| Card link label | `payload.card_link_label` | text | plain text | 50 | yes | no |

Hidden from editors on this screen:

- Blog article titles
- Slugs
- Categories
- Excerpts
- Dates
- Read time
- Images
- Publish state
- Query limit and ordering

### Primary CTA Fields

| Label | Column / Path | Input | Rule | Max | Required | Reorder |
| --- | --- | --- | --- | ---: | --- | --- |
| Eyebrow | `eyebrow` | text | plain text | 80 | yes | no |
| Title line 1 | `payload.title_lines.0` | text | plain text | 120 | yes | no |
| Title line 2 | `payload.title_lines.1` | text | plain text | 120 | yes | no |
| Full title | `title` | generated hidden | joined/generated text | 255 | yes | no |
| Subtitle | `subtitle` | textarea | plain text | 500 | yes | no |
| Primary label | `button_label` | text | plain text | 50 | yes | no |
| Primary route | derived from `button_url` | select | known route | 40 | yes | no |
| Primary URL | `button_url` | hidden/generated or text | safe URL | 255 | yes | no |
| Secondary label | `payload.secondary_button.label` | text | plain text | 50 | yes | no |
| Secondary route | `payload.secondary_button.route` | select | known route | 40 | yes | no |
| Secondary URL | `payload.secondary_button.url` | hidden/generated or text | safe URL | 255 | yes | no |
| Proof point | `payload.proof_points.*` | text | plain text | 100 | yes | fixed 4 |

Help text: "The second title line is visually highlighted on the public homepage."

## Editor Safety Boundaries

Editors must not control:

- `page_key`
- `section_key`
- `sort_order`
- Raw `status`
- Arbitrary section count
- Arbitrary card/item count
- Arbitrary Tailwind classes
- Raw HTML
- Scripts
- Event handlers
- Inline styles
- Model class names
- Controller variable names
- Service records
- Portfolio records
- Blog records
- Query limits
- Query ordering
- Unsafe URL schemes
- Full SVG markup

Use select controls for:

- Hero metric colors: `text-pm-cyan`, `text-pm-gold`, `text-white`
- Difference colors: `cyan`, `gold`
- Trust bar colors: seven approved hex values
- Stat suffixes: `+`, `%`, `x`, empty
- Known internal routes: `services`, `contact`, `portfolio`, `blog`

## Validation Architecture

Recommended request class:

```php
App\Http\Requests\Admin\UpdateHomepageRequest
```

Recommended approach:

- One full-form request with nested arrays.
- One controller update endpoint.
- Private helper methods inside the request for section rule groups.
- Custom `after()` validation for exact counts, plain-text checks, safe URLs, and safe SVG path checks where Laravel's string rules are not expressive enough.

Why not one request per section:

- The homepage has eight fixed records and one public page.
- A single transaction should update the complete page configuration.
- One request prevents partial section drift and keeps route count low.

Validation must mirror the public Blade safety rules:

- Plain text only.
- Reject HTML and scripts.
- Fixed array counts.
- Safe URL or known route values only.
- Safe SVG path data only.
- Chart heights are integers from 0 to 100.
- Color and suffix allowlists.
- No raw class names except the existing metric color allowlist.

Suggested reusable validation helpers:

- `plainText($value, int $max)`
- `safeUrlOrRoute($value, array $allowedRoutes)`
- `safeSvgPath($value)`
- `exactListCount($value, int $count)`
- `rejectHtmlLikeContent($value)`

## Field To Database Mapping

All writes are fixed to:

```php
'page_key' => 'home',
'status' => 'active',
```

Fixed section keys and sort orders:

| Section key | Sort order |
| --- | ---: |
| `hero` | 10 |
| `stats` | 20 |
| `trust_bar` | 30 |
| `difference` | 40 |
| `services_intro` | 50 |
| `portfolio_intro` | 60 |
| `blog_intro` | 70 |
| `primary_cta` | 80 |

Use these columns only:

- `eyebrow`
- `title`
- `subtitle`
- `body`
- `button_label`
- `button_url`
- `image`
- `payload`
- `status`
- `sort_order`

Keep `body` and `image` null unless a future phase explicitly connects them.

## Proposed Payload Shapes

### Hero

```php
[
    'title_lines' => [
        'Engineering Digital',
        'Success',
        'with',
        'Technical Precision.',
    ],
    'secondary_button' => [
        'label' => 'Get a Free Audit',
        'url' => '/contact',
        'route' => 'contact',
    ],
    'dashboard' => [
        'eyebrow' => 'Live Dashboard',
        'title' => 'Q2 Campaign Performance',
        'status' => 'Live',
        'metrics' => [
            ['label' => 'Conversions', 'value' => '1,248', 'change' => 'up 34%', 'color' => 'text-pm-cyan'],
            ['label' => 'ROAS', 'value' => '4.8x', 'change' => 'up 12%', 'color' => 'text-pm-gold'],
            ['label' => 'CTR', 'value' => '7.2%', 'change' => 'up 8%', 'color' => 'text-white'],
        ],
        'chart' => [
            'label' => 'Weekly Conversions',
            'days' => ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
            'bar_heights' => [35, 55, 42, 70, 60, 85, 75],
        ],
        'tracking' => [
            'label' => 'Tracking:',
            'items' => ['GTM', 'GA4', 'Meta API', 'Server-Side'],
        ],
        'floating_badges' => [
            ['label' => 'ROI', 'value' => 'up 340%'],
            ['label' => 'Tracking Active'],
        ],
    ],
]
```

Note: production should preserve the current stored visible symbols from the seeded record. The examples above describe shape; implementation should not rewrite visible content.

### Stats

```php
[
    'items' => [
        ['value' => 150, 'suffix' => '+', 'sep' => '', 'label' => 'Projects Done'],
        ['value' => 98, 'suffix' => '%', 'sep' => '', 'label' => 'Client Satisfaction'],
        ['value' => 5, 'suffix' => 'x', 'sep' => '', 'label' => 'Average ROAS'],
        ['value' => 3, 'suffix' => '+', 'sep' => 'yr', 'label' => 'Experience'],
    ],
]
```

### Trust Bar

```php
[
    'items' => [
        ['label' => 'Google Ads', 'color' => '#4285F4'],
        ['label' => 'Tag Manager', 'color' => '#F57C00'],
        ['label' => 'Meta Pixel', 'color' => '#1877F2'],
        ['label' => 'Laravel', 'color' => '#FF2D20'],
        ['label' => 'MySQL', 'color' => '#4479A1'],
        ['label' => 'Analytics GA4', 'color' => '#E37400'],
        ['label' => 'PHP 8', 'color' => '#777BB4'],
    ],
]
```

### Difference

```php
[
    'cards' => [
        [
            'badge' => 'No. 01',
            'color' => 'cyan',
            'icon_path' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
            'title' => 'Tech-First Marketing',
            'body' => 'We code the tracking setup that other agencies miss...',
        ],
        [
            'badge' => 'No. 02',
            'color' => 'gold',
            'icon_path' => 'M9 19v-6a2 2 0 00-2-2H5a2...',
            'title' => 'ROI Focused',
            'body' => 'Every click is treated as an investment...',
        ],
        [
            'badge' => 'No. 03',
            'color' => 'cyan',
            'icon_path' => 'M15 12a3 3 0 11-6 0...',
            'title' => 'Transparent Data',
            'body' => 'Clear reporting, measurable results...',
        ],
    ],
]
```

### Services Intro

```php
[
    'card_link_label' => 'Learn More',
    'card_icons' => [
        'M9 19v-6a2 2 0 00-2-2H5a2...',
        'M9 3v2m6-2v2M9 19v2m6-2v2...',
        'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
    ],
]
```

### Portfolio Intro

```php
[
    'card_link_label' => 'View Case Study',
]
```

### Blog Intro

```php
[
    'card_link_label' => 'Read Article',
]
```

### Primary CTA

```php
[
    'title_lines' => [
        'Ready to scale your business?',
        'Be Optimistic.',
    ],
    'secondary_button' => [
        'label' => 'See Our Work',
        'url' => '/portfolio',
        'route' => 'portfolio',
    ],
    'proof_points' => [
        'No Long-Term Contracts',
        'Free Audit Consultation',
        'ROI-Focused Approach',
        '100% Transparent Reporting',
    ],
]
```

## UI Recommendation

Recommended layout:

- One admin page.
- Tabbed interface using Alpine.js.
- One tab for each fixed section:
  - Hero
  - Stats
  - Trust Bar
  - Difference
  - Services
  - Portfolio
  - Blog
  - Final CTA
- Dark cards inside each tab.
- Fixed numbered rows for repeated data.
- Save-all button in a sticky action bar.
- Preview link to `route('home')`.
- No delete action.

Save behavior:

- Use one form and one `PUT` request.
- Save all eight sections inside one transaction.
- On validation failure, return to the tab containing the first error.

Unsaved-change warning:

- Recommended with Alpine dirty tracking, but it can be a second implementation step if needed.

Reset-to-seeded-values:

- Do not include in the first CRUD implementation.
- If added later, make it per section, require confirmation, and use the same fixed defaults. It must not touch service, portfolio, or blog domain records.

## Sidebar Integration Plan

Recommended sidebar item:

- Group: `Content`
- Label: `Homepage`
- Route: `admin.homepage.edit`
- Access: `admin`
- Placement: after `Portfolio` or before `Blog Articles`; after `Portfolio` keeps page-level content near content modules.
- Icon: reuse an existing simple document/home SVG path style.

Article writers should not see the link.

The existing sidebar active logic should work because the route prefix segment will be `homepage`.

## Transaction And Activity Log Plan

Use one transaction around all eight `updateOrCreate` calls:

```php
DB::transaction(function () use ($sections) {
    foreach ($sections as $sectionKey => $attributes) {
        PageSection::updateOrCreate(
            ['page_key' => 'home', 'section_key' => $sectionKey],
            $attributes
        );
    }

    ActivityLog::log(
        'updated_homepage_sections',
        'PageSection',
        null,
        'Updated homepage page sections'
    );
});
```

If any section transformation or write fails, no partial homepage content should be saved.

## Testing Plan

Use SQLite `:memory:` or a disposable test database.

Recommended tests:

- `super_admin` can access edit screen.
- `admin` can access edit screen.
- `article_writer` receives 403.
- Guest redirects to admin login.
- Edit screen renders all eight sections.
- Existing values load from seeded `page_sections`.
- Successful update writes all eight records.
- Update preserves fixed `page_key`, `section_key`, `sort_order`, and `status`.
- Nested validation failures show errors.
- Unsafe URL schemes are rejected.
- Unsafe SVG paths are rejected.
- Invalid metric colors are rejected.
- Invalid trust colors are rejected.
- Invalid difference color keys are rejected.
- Invalid stat suffixes are rejected.
- Fixed item counts are enforced.
- Transaction rolls back on failure.
- Missing records are recreated on update.
- Public homepage reflects saved section content.
- Service records are not modified.
- Portfolio records are not modified.
- Blog records are not modified.
- Domain card counts/order remain controlled by domain modules, not `page_sections`.
- Activity log is created on successful save.

## Maintainability Risks

The public homepage Blade currently contains repeated local validation and normalization logic for each section. That was appropriate for narrow phased integration, but admin CRUD will need to mirror the same rules.

Risk:

- Validation rules could drift between the admin request and public Blade fallback logic.
- URL, SVG, color, and count validation could be duplicated in multiple places.
- Future sections may become harder to test if normalization remains embedded in Blade.

Recommendation:

- Before or during CRUD implementation, extract shared homepage section defaults and normalization into a small server-side presenter or value object layer.
- Keep the first extraction behavior-preserving and covered by the existing focused homepage tests.
- Do not change payload structures during the extraction unless a verified defect is found.

Suggested implementation order:

1. Create a fixed homepage section schema/default map.
2. Extract safe normalization helpers used by both public rendering and admin form population.
3. Add `UpdateHomepageRequest`.
4. Add `Admin\HomepageController`.
5. Add admin edit view with tabs and fixed fields.
6. Add routes under `admin.auth` plus `admin.admin`.
7. Add sidebar link for admin and super admin only.
8. Add focused admin feature tests.
9. Run full homepage and admin regression tests.

## Recommended Implementation Sequence

Phase 5B:

- Extract reusable homepage section defaults and normalization helpers without changing rendered output.
- Keep all existing public homepage tests green.

Phase 5C:

- Add admin routes, controller, request, and edit view.
- Update sidebar.
- Add admin CRUD tests.

Phase 5D:

- Browser review of the admin editor.
- Verify update behavior against local database only after backup and approval.

## Confirmation

No application code was changed.

No admin routes, controllers, Form Requests, Blade views, sidebar links, models, migrations, seeders, or public homepage files were modified.

No database records were inserted, updated, or deleted.

`php artisan prosper:install` was not run.
