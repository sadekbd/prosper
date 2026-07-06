# Phase 5E: Homepage Dirty-Only Updates

Date: 2026-07-06

Scope: refine the admin homepage save path so only materially changed `page_sections` records are written, while preserving one full form, one update endpoint, full validation, one transaction, fixed records, existing payload structures, and activity logging.

## Root Cause

Phase 5D showed that changing only the hero eyebrow also updated `updated_at` on several unchanged homepage section rows.

The root cause was the update action calling `PageSection::updateOrCreate()` for all eight fixed sections on every valid submit, followed by unconditional activity logging.

Some full-form values can also differ from seeded JSON in PHP representation while preserving the same public content:

- Stats values may be stored as numbers but submitted as strings.
- Empty optional fields such as `sep` may be omitted in stored payloads but submitted as empty strings.
- Optional badge values may be omitted in stored payloads but represented as empty form values.
- Associative JSON key order can differ without changing meaning.

Those differences can make Eloquent consider an unchanged casted JSON payload dirty.

## Files Changed

- `app/Http/Controllers/Admin/HomepageController.php`
- `tests/Feature/Admin/HomepageDirtyUpdateTest.php`
- `docs/PHASE_5E_HOMEPAGE_DIRTY_UPDATE.md`

No public homepage Blade, `HomepageContent` payload structures, migrations, seeders, routes, sidebar, service, portfolio, blog, settings, header, footer, or SEO code was changed.

## Attribute Normalization Strategy

The controller still receives the existing validated section attributes from `UpdateHomepageRequest::homepageData()`.

Before filling an existing `PageSection`, the controller now normalizes proposed payloads for dirty comparison against the current casted payload:

- Cast scalar JSON values to comparable strings for equivalence checks.
- Ignore empty optional `null` and empty-string payload values when comparing.
- Sort associative keys for stable comparison.
- Preserve list order for fixed repeated items.
- Treat omitted empty stat separators and submitted empty stat separators as equivalent.

If the canonical current payload and canonical proposed payload are equivalent, the controller keeps the existing payload array on the model. This avoids rewriting JSON solely because of harmless form representation differences.

The persisted payload shape is not changed when a section is materially edited.

## Dirty Detection Strategy

The update path now uses:

```php
$pageSection = PageSection::firstOrNew([
    'page_key' => 'home',
    'section_key' => $sectionKey,
]);

$pageSection->fill($attributes);

if (! $pageSection->exists || $pageSection->isDirty()) {
    $pageSection->save();
}
```

Eloquent remains the final dirty-detection gate after model casts and fillable attributes are applied.

## Missing Records

Missing fixed records are still recreated through `firstOrNew()` plus `save()`.

Recreated records preserve:

- `page_key = home`
- fixed `section_key`
- `status = active`
- fixed sort orders from 10 through 80
- existing payload structures

Unchanged existing records are not touched while a missing record is recreated.

## No-Op Saves

If a valid full-form submit matches the currently persisted homepage content:

- No `page_sections` rows are saved.
- No `updated_at` values change.
- No activity log is created.
- The user is redirected back to the homepage editor.
- The existing success flash channel displays:

```text
Homepage content is already up to date.
```

The admin layout currently renders `success` and `error` flash messages, so the no-op message uses the existing success slot rather than adding layout scope.

## Activity Logging

Activity logging now happens only when at least one fixed homepage section is created or materially changed.

Changed saves create one activity log for the whole form submission, not one log per section.

The description includes the changed section keys:

```text
Updated homepage page sections: hero, primary_cta
```

No-op saves create no activity log.

## Transaction Behavior

All comparisons, dirty writes, missing-record creation, and activity logging remain inside one `DB::transaction()`.

If one changed section write fails:

- Earlier changed section writes roll back.
- Later changed section writes do not persist.
- No activity log persists.
- Unchanged records remain untouched.

## Targeted Test Results

Commands run:

```powershell
php artisan test tests\Feature\Admin\HomepageDirtyUpdateTest.php
php artisan test tests\Feature\Admin\HomepageAdminTest.php
php artisan test tests\Feature\Admin\HomepageUpdateValidationTest.php
```

Results:

- `HomepageDirtyUpdateTest`: passed, 6 tests, 51 assertions.
- `HomepageAdminTest`: passed, 4 tests, 41 assertions.
- `HomepageUpdateValidationTest`: passed, 2 tests, 21 assertions.

Dirty-update coverage includes:

- Single-section change updates only that section and logs once.
- No-op save touches no rows and creates no log.
- Multiple-section change updates only those sections and logs once.
- Missing section recreation does not touch unchanged existing rows.
- Transaction rollback prevents partial writes and logs.
- Equivalent JSON shapes do not create false dirty updates.

## Full Suite And Route Results

Full suite:

```powershell
php artisan test
```

Result:

```text
66 tests passed, 818 assertions
```

Route check:

```powershell
php artisan route:list --name=admin.homepage
```

Confirmed routes:

- `GET|HEAD admin/homepage` named `admin.homepage.edit`
- `PUT admin/homepage` named `admin.homepage.update`

Whitespace check:

```powershell
git diff --check
```

Result: no whitespace errors.

## Local MySQL Confirmation

This phase was verified through isolated SQLite tests only.

No admin form was submitted against local MySQL.
No local `prosper_media` records were inserted, updated, or deleted.
No migrations or seeders were run.
`php artisan prosper:install` was not run.

## Recommended Controlled Local Retest

Repeat the Phase 5D local browser/database review after this change:

1. Back up local `prosper_media`.
2. Change only the hero eyebrow through `/admin/homepage`.
3. Confirm only the hero row's `updated_at` changes.
4. Confirm one activity log is created.
5. Restore the original hero eyebrow through the editor.
6. Confirm only the hero row changes again.
7. Submit an unchanged full form and confirm no rows and no activity log change.

## Remaining Deployment Considerations

- The dirty-only save path is covered by isolated tests, but should still receive one local browser retest before deployment approval.
- The editor remains a full-form save, so validation still covers all eight sections on every submit.
- Audit precision is improved at the row timestamp and activity-log level.
- Public homepage rendering and payload structures were not changed.
