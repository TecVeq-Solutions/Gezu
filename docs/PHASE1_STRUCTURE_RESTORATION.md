# Phase 1 — Standard structure restoration

## Campaign model merge

- **Sources:** `public_html/app/Models/Campaign.php` (428 lines) vs former `app/app/Models/Campaign.php` (395 lines).
- **Diff:** Identical through line 393 (`getPayoutSummaryAttribute`). The longer file appends only:
  - `isOpenForSubmissions()`
  - `isHalfFilled()`
  - `isCompleted()`
  - `getClaimedAmountAttribute()`
  - `getClaimedPercentageAttribute()`
- **Resolution:** Canonical model is `gezu/app/Models/Campaign.php` with **all** of the above methods preserved from `public_html`.

## Seven reconciled files

MD5-verified identical between `public_html/app/...` and `app/...` after recovery:

- `Http/Controllers/Auth/AuthController.php`
- `Http/Controllers/Admin/AdminPanelController.php`
- `Http/Controllers/Clipper/ClipperPanelController.php`
- `Http/Controllers/Streamer/StreamerPanelController.php`
- `Http/Controllers/WebController.php`
- `Services/ClipSubmissionService.php`
- `Services/GoogleAuthService.php`

## Nested project backup

The old nested Laravel tree that lived at `gezu/app/` was moved to:

`../gezu-nested-full-backup-20261106-171913`

## Staging directory

Application layer copy before swap:

`../_gezu_app_layer_stage_20261106-171913`

## Full project backup

`../gezu-phase1-backup-20261106-171245` (~503MB)

## public_html (phase note)

`public_html/` was **not** deleted. Subset under `public_html/app/` duplicates the canonical `app/` for those paths; keep for deployment review. Broader `public_html/` may mirror config/resources for hosting—retire only after deployment mapping.
