# Safe Migration Plan

This is a review-first plan. No implementation should start until these audit docs are approved.

## Phase 0: Baseline Verification
- Capture current public route list and admin route list.
- Run current tests before changes.
- Take screenshots of public pages and representative admin screens.
- Confirm storage symlink and current uploaded files.
- Confirm production database backup procedure.

## Phase 1: Audit Only
- Completed by these docs.
- No migrations, seeders, records, routes, controllers, models, Blade files, assets, or build output changed.

## Phase 2: Add Non-Destructive Schema
- Add missing tables only: `page_sections`, `navigation_links`, `portfolio_categories`, `testimonials`, optional `media_assets`.
- Add nullable/backward-compatible columns only where needed.
- Do not rename/drop existing tables or columns.
- Do not convert enums or foreign keys destructively.

## Phase 3: Seed Current Static Content
- Create seeders that copy current hard-coded visible content exactly into new tables.
- Seed homepage hero, stats, trust bar, difference cards, fallback cards, CTAs.
- Seed about page story, mission cards, skill bars, CTA.
- Seed contact page intro, response notes, contact stats, and service-interest labels.
- Seed navigation/footer labels and links.
- Seed portfolio categories from current hard-coded keys/labels/colors.
- Seed privacy policy/page records from existing visible content.

## Phase 4: Read Dynamic Data Without Visual Changes
- Update controllers to pass new database content to existing Blade views.
- Keep existing markup and Tailwind classes intact.
- Remove Blade fallbacks only after equivalent database seed records are present.
- Eager-load relationships to avoid N+1 queries.
- Keep URLs and slugs unchanged.

## Phase 5: Admin CRUD One Module At A Time
- Implement and review one module at a time:
  1. Portfolio categories.
  2. Navigation/footer links.
  3. Page sections for homepage.
  4. Page sections for about/contact/index pages.
  5. Testimonials.
  6. Pages/privacy policy.
  7. Media reference safeguards.
- After each module, provide changed files, database changes, routes, tests, manual verification, rollback steps, and remaining risks.

## Phase 6: Replace Duplicate Sources
- Remove or stop using Blade fallback arrays once database content is seeded and verified.
- Keep existing seeders updated as canonical defaults for reusable installs.
- Replace hard-coded portfolio categories in controllers/forms/accessors with database-backed category records.
- Replace service-interest enum behavior carefully; preserve existing stored values.

## Phase 7: Security And Reliability
- Add rate limiting/spam protection to public contact and newsletter forms.
- Add Form Requests for admin CRUD currently using inline validation.
- Sanitize trusted rich HTML for blog/project/page content.
- Restrict uploaded file MIME types/sizes and generate safe filenames.
- Prevent deletion of referenced media.
- Add soft deletes where appropriate.

## Phase 8: Tests
- Add feature tests for public rendering of database-backed homepage/about/contact/portfolio/blog content.
- Add admin CRUD tests for new modules.
- Add tests for contact/newsletter validation and throttling.
- Add tests for route/slug compatibility.
- Add tests for portfolio category filter/count behavior.

## Rollback Strategy
- Every schema change must be additive and nullable where possible.
- New views/controllers should tolerate missing records only during deployment, but production content should be seeded before switching.
- If a module misbehaves, revert controller/view usage to the previous hard-coded source while leaving new tables unused.
- Do not delete legacy columns or static content until after production verification.

## Commands To Avoid During Audit
- No `npm run build`.
- No `php artisan db:seed`.
- No destructive migration/reset commands.
- No production `.env` or credential edits.
