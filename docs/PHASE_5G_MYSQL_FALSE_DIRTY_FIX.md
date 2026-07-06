# Phase 5G: MySQL False-Dirty Homepage Section Diagnosis

Date: 2026-07-06

Scope: restore the local hero test value, diagnose the Phase 5F false-dirty result against local MySQL records, strengthen dirty-update tests with MySQL-shaped repeated payload fixtures, and verify the suite without performing another local admin save.

## Failed Phase 5F Observation

Phase 5F changed only:

```text
hero.eyebrow: Be Optimistic -> Be Optimistic Test
```

The save also changed `updated_at` for:

- `stats`
- `trust_bar`
- `difference`
- `services_intro`
- `primary_cta`

The latest activity log description after that save was:

```text
Updated homepage page sections: hero, stats, trust_bar, difference, services_intro, primary_cta
```

Unchanged rows:

- `portfolio_intro`
- `blog_intro`

Domain counts remained unchanged.

## Restoration

The test value was restored with one targeted SQL update only:

```sql
UPDATE page_sections
SET eyebrow = 'Be Optimistic'
WHERE page_key = 'home'
  AND section_key = 'hero';
```

The admin editor was not used for restoration.

Verification after restoration:

- Hero eyebrow is `Be Optimistic`.
- Public homepage contains `Be Optimistic`.
- Public homepage does not contain `Be Optimistic Test`.
- `page_sections` total remains 8.
- Home section count remains 8.
- Duplicate home section pairs remain 0.
- Domain counts remained unchanged:
  - Services: 2
  - Portfolio projects: 6
  - Blog articles: 5
  - Site settings: 17

The failed-test activity log was not deleted.

## Diagnostic Method

Diagnostics were read-only after the targeted hero restoration.

The diagnostic compared local MySQL-loaded `PageSection` models against in-memory controller/request transformed attributes for a simulated form payload changing only `hero.eyebrow`.

For each section, it compared:

- current model attributes
- current casted payload
- proposed transformed attributes
- normalized attributes from `HomepageController::persistedAttributes()`
- Eloquent `getDirty()` result on a cloned model

No admin form was submitted after the fix.
No credentials were printed.
Temporary diagnostic scripts were created only in the system temp directory and removed immediately.

## Exact Dirty Attributes Found

Against the current code path, after restoring the hero eyebrow, the read-only MySQL diagnostic produced:

| Section | Dirty keys |
| --- | --- |
| `hero` | `eyebrow` |
| `stats` | none |
| `trust_bar` | none |
| `difference` | none |
| `services_intro` | none |
| `portfolio_intro` | none |
| `blog_intro` | none |
| `primary_cta` | none |

This means the committed dirty-only controller logic correctly preserves unchanged MySQL-loaded rows in memory.

## Nested Payload Differences Observed

The local MySQL rows affected by the failed Phase 5F save showed repeated-list payload representation differences rather than domain-table changes.

Observed affected paths:

- `stats.payload.items`
  - item order differed from seeded order
  - empty `sep` values were represented as `null`
  - numeric values were represented as strings
- `trust_bar.payload.items`
  - item order differed from seeded order
- `difference.payload.cards`
  - card order differed from seeded order
  - associative key order differed inside card objects
- `services_intro.payload.card_icons`
  - icon order differed from seeded order
- `primary_cta.title`
  - title line order differed
- `primary_cta.payload.title_lines`
  - title line order differed
- `primary_cta.payload.secondary_button`
  - associative key order differed

Controls:

- `portfolio_intro.payload.card_link_label` had no repeated list shape and stayed unchanged.
- `blog_intro.payload.card_link_label` had no repeated list shape and stayed unchanged.

## Why SQLite Tests Missed The Local Observation

The existing SQLite dirty-update tests covered seeded payloads and simple equivalent JSON cases, but they did not include the exact repeated-array shapes found in local MySQL after the failed browser-form retest.

The local failure was consistent with a form submission path that changed repeated-list ordering or optional empty representations before the dirty-only code path was verified in the running web process.

The current controller logic, when executed directly against the restored MySQL rows, only marks `hero.eyebrow` dirty for the simulated single-field change.

## Normalization And Comparison Fix

No public payload structure was changed.

The existing Phase 5E controller fix remains the production dirty gate:

- Build the proposed attributes from the validated full form.
- For existing records, compare canonical current payload and canonical proposed payload.
- Preserve the existing payload array if the canonical forms are equivalent.
- Fill the model.
- Let Eloquent `isDirty()` decide whether to save.

The test suite was strengthened so repeated-list MySQL representations are covered before another local browser retest.

## Zero And Empty-Value Safeguards

The canonical comparison does not globally remove meaningful falsey values.

Covered safeguards:

- `null` and empty string are treated as equivalent only for optional empty payload values.
- The string value `"0"` is preserved.
- A meaningful stat change from `150` to `0` remains dirty and is saved.
- Numeric/string equivalence is used for comparison only; it does not hide real content changes.
- Existing payload JSON is preserved when no material change exists.

## Tests Added

Updated:

- `tests/Feature/Admin/HomepageDirtyUpdateTest.php`

Added coverage:

- MySQL-loaded repeated payload representation does not create false dirty updates.
- Changing only hero against that representation updates only hero.
- `stats`, `trust_bar`, `difference`, `services_intro`, and `primary_cta` remain untouched when equivalent.
- Meaningful zero stat values are preserved and saved.
- Numeric/string comparison does not hide real content changes.

## Test Results

Commands run:

```powershell
php artisan test tests\Feature\Admin\HomepageDirtyUpdateTest.php
php artisan test tests\Feature\Admin\HomepageAdminTest.php
php artisan test tests\Feature\Admin\HomepageUpdateValidationTest.php
php artisan test
git diff --check
```

Results:

- `HomepageDirtyUpdateTest`: passed, 8 tests, 67 assertions.
- `HomepageAdminTest`: passed, 4 tests, 41 assertions.
- `HomepageUpdateValidationTest`: passed, 2 tests, 21 assertions.
- Full suite: passed, 68 tests, 834 assertions.
- `git diff --check`: no whitespace errors.

## Local MySQL Safety Confirmation

Local MySQL changes in this phase were limited to the targeted hero restoration:

```text
home.hero eyebrow = Be Optimistic
```

No admin form was submitted after the fix.
No local migrations or seeders were run.
No service, portfolio, blog, settings, user, route, controller, Blade, migration, or seeder records were modified.
`php artisan prosper:install` was not run.

## Current Content Confirmation

Original hero content is restored:

```text
Be Optimistic
```

No test text remains on the public homepage.

The failed Phase 5F activity log remains in place as requested.

## Recommendation

Run another controlled local retest in a fresh Phase 5H after ensuring the local web server is using the latest PHP code.

Recommended retest shape:

1. Restart or reload the local PHP web server/opcache if applicable.
2. Back up `prosper_media`.
3. Change only `hero.eyebrow` through `/admin/homepage`.
4. Confirm only `hero.updated_at` changes.
5. Confirm one activity log description identifies only `hero`.
6. Restore `hero.eyebrow` through the admin editor.
7. Confirm only `hero.updated_at` changes again.
8. Submit a no-op full form and confirm no timestamp or activity-log changes.
