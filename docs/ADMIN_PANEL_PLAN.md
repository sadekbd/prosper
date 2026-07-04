# Admin Panel Plan

Use the existing admin layout, sidebar, topbar, dark theme, form styling, pagination, flash messages, and middleware. Do not redesign admin screens.

## Existing Admin Modules To Reuse
- Settings: `admin/settings` for global settings, logo, favicon, contact details, social links, default SEO.
- Services: `admin/services` for active/inactive services, ordered service features, SEO fields.
- Portfolio: `admin/portfolio` for project/case-study CRUD.
- Blog categories/articles: `admin/blog/categories`, `admin/blog/articles`.
- Contact messages: `admin/messages`.
- Newsletter: `admin/newsletter`.
- Users: `admin/users`.

## Admin Modules Needed

### Page Sections
- Routes under `admin/page-sections` or grouped by page such as `admin/pages/{page}/sections`.
- Manage fixed sections for home, about, contact, services index, portfolio index, blog index.
- Fields: eyebrow, title, subtitle, body, button label, button URL, image, JSON/card repeater payload, active/inactive, sort order.
- Must preserve current frontend markup by passing section data into existing Blade structures.
- Use dedicated Form Requests per page/section type to validate payload shape.

### Navigation Links
- Manage header and footer link labels/order while preserving existing route names.
- Fields: area, label, route name or URL, target, status, sort order.
- Restrict unsafe external URLs and require either `route_name` or `url`.

### Portfolio Categories
- CRUD for portfolio category name, slug, color, description, status, sort order.
- Seed current categories: Google Ads, Tracking Setup, Web Development, Landing Page, Automation.
- Integrate portfolio project create/edit forms by loading active categories.
- Preserve existing project `category` values during compatibility phase.

### Testimonials
- CRUD for testimonial cards if/when public sections need testimonials.
- Fields: name, role, company, quote, rating, image, featured, status, sort order.

### Media References
- Optional later module or internal service for tracking uploaded files.
- Minimum admin behavior: warn/prevent deletion when a file is referenced by settings, articles, projects, sections, or pages.

## Existing Modules That Need Completion
- Settings should validate values by type and restrict image size/type.
- Portfolio project forms should eventually support gallery image management, not only `featured_image`.
- Blog article forms should expose Open Graph title/description/image if those columns remain intended for use.
- Contact service-interest options should be generated from active services where possible while preserving legacy submitted enum values.
- Privacy policy should be editable through an existing or new Pages module using the current `pages` table.

## Authorization
- Keep all admin routes behind `admin.auth`.
- Settings, navigation, page sections, portfolio categories, and testimonials should use `admin.super` or `admin.admin` depending on ownership decision.
- Blog writer access should remain limited to writer-owned articles.
- Prefer policies or dedicated middleware checks for new modules; keep role behavior consistent with current admin design.

## Validation And Transactions
- Add dedicated Form Request classes for each new module.
- Use database transactions for multi-row changes: page sections with repeater items, service features, media replacement, portfolio gallery updates.
- Use non-destructive saves first; media deletion should happen only after successful DB commit and only when unreferenced.

## UI Integration Points
- Add sidebar links only after each module is implemented.
- Use existing cards/tables/forms and Tailwind classes.
- Keep empty states and pagination consistent with current admin views.
- Avoid changing public design while replacing hard-coded values with controller-provided data.
