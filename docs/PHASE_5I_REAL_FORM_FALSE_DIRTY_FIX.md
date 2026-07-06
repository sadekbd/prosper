# Phase 5I: Real Form False-Dirty Fix

Date: 2026-07-06

Scope: diagnose the Phase 5H false-dirty save, fix request transformation for real form-shaped repeated arrays, strengthen isolated tests, and verify without performing another local MySQL admin save.

## Phase 5H Failure Summary

Phase 5H used a fresh `php artisan serve` process and ran:

```powershell
php artisan optimize:clear
```

The controlled save changed only:

```text
hero.eyebrow: Be Optimistic -> Be Optimistic Test
```

But the save also changed `updated_at` on:

- `stats`
- `trust_bar`
- `difference`
- `services_intro`
- `primary_cta`

The activity log description was:

```text
Updated homepage page sections: hero, stats, trust_bar, difference, services_intro, primary_cta
```

Unchanged controls:

- `portfolio_intro`
- `blog_intro`

This ruled out the stale web-process hypothesis.

## Restored Content Confirmation

Before code changes in Phase 5I, the local state was verified read-only:

- Hero eyebrow: `Be Optimistic`
- Public homepage contains `Be Optimistic`
- Public homepage does not contain `Be Optimistic Test`
- `page_sections` total: 8
- Home section count: 8
- Duplicate section pairs: 0
- Services: 2
- Portfolio projects: 6
- Blog articles: 5
- Site settings: 17

No MySQL update was performed in Phase 5I.

## Root Cause

The dirty-only comparison was working for simulated payloads, but the actual submitted form body could arrive with numeric-indexed repeated arrays inserted out of index order.

The request transformation used `array_values()` on repeated arrays:

```php
array_values($validated['stats']['items'])
```

`array_values()` preserves insertion order. It does not sort by numeric key.

So if request data arrived as keys `1, 2, 0, 3`, the transformed payload became a real reordered list. That made unchanged repeated sections materially dirty.

## Exact Dirty Keys And Paths

The false-dirty sections map to repeated numeric arrays:

| Section | Dirty top-level key | Nested cause |
| --- | --- | --- |
| `stats` | `payload` | `payload.items` order changed |
| `trust_bar` | `payload` | `payload.items` order changed |
| `difference` | `payload` | `payload.cards` order changed |
| `services_intro` | `payload` | `payload.card_icons` order changed |
| `primary_cta` | `title`, `payload` | `payload.title_lines` order changed, which also regenerated `title` in changed order |

Controls:

- `portfolio_intro` did not change because it has no repeated list payload in the admin form.
- `blog_intro` did not change because it has no repeated list payload in the admin form.

Hero intentionally changed:

- `hero.eyebrow`

## Code Fix

Changed:

- `app/Http/Requests/Admin/UpdateHomepageRequest.php`

Fix location:

- request transformation, not public rendering
- no admin route/sidebar changes
- no public payload contract changes

Added an `orderedList()` helper:

```php
private function orderedList(array $items): array
{
    ksort($items, SORT_NUMERIC);

    return array_values($items);
}
```

The request now sorts numeric-indexed repeated arrays before reindexing and persisting them.

Applied to:

- `hero.title_lines`
- `hero.dashboard.metrics`
- `hero.dashboard.chart.days`
- `hero.dashboard.chart.bar_heights`
- `hero.dashboard.tracking.items`
- `hero.dashboard.floating_badges`
- `stats.items`
- `trust_bar.items`
- `difference.cards`
- `services_intro.card_icons`
- `primary_cta.title_lines`
- `primary_cta.proof_points`

This preserves intended editor order by numeric field index, regardless of request insertion order.

## Safety Boundaries

The fix does not:

- Change public homepage rendering.
- Change payload structures.
- Change section keys or sort orders.
- Change migrations or seeders.
- Change admin routes or sidebar.
- Modify services, portfolio, blog, settings, users, header, footer, or SEO.
- Hide meaningful repeated-field content changes.

Meaningful repeated-field changes still save normally.

## Tests Added

Updated:

- `tests/Feature/Admin/HomepageDirtyUpdateTest.php`

Added coverage:

- Out-of-order form payload changing only hero updates only hero.
- Out-of-order form payload no-op touches no records and creates no activity log.
- Out-of-order form payload with a real `primary_cta.proof_points.2` change updates only `primary_cta`.

Existing coverage retained:

- Single-section dirty save.
- No-op save.
- Multiple-section dirty save.
- Missing section recreation.
- Transaction rollback.
- MySQL-shaped repeated payload representation.
- Meaningful zero values.
- Numeric/string equivalence without hiding real changes.

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

- `HomepageDirtyUpdateTest`: passed, 11 tests, 98 assertions.
- `HomepageAdminTest`: passed, 4 tests, 41 assertions.
- `HomepageUpdateValidationTest`: passed, 2 tests, 21 assertions.
- Full suite: passed, 71 tests, 865 assertions.
- `git diff --check`: no whitespace errors.

## MySQL Safety Confirmation

No local MySQL admin save was performed after the fix.

Only read-only MySQL checks were run in Phase 5I.

Confirmed after the fix:

- Hero eyebrow remains `Be Optimistic`.
- Public homepage contains `Be Optimistic`.
- Public homepage does not contain `Be Optimistic Test`.
- `page_sections` total remains 8.
- Home section count remains 8.
- Duplicate pairs remain 0.

The failed Phase 5H activity log remains preserved.

## Recommendation

Proceed with a fresh Phase 5J controlled local retest:

1. Confirm clean Git state.
2. Restart or refresh the local PHP web process.
3. Back up local `prosper_media`.
4. Submit the hero-only admin change once.
5. Confirm only `hero.updated_at` changes and latest activity description is:

```text
Updated homepage page sections: hero
```

6. Restore through admin.
7. Confirm only hero changes again.
8. Submit a no-op save and confirm no timestamps or activity logs change.
