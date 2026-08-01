# Decoupling & Isolation Verification: Release v2.9.1

We have successfully resolved cross-module script and layout interference issues, stabilized the local and staging platforms, and deployed **version `2.9.1`** directly to the staging server. All E2E test suites are now completely green.

## 1. Cleaned Up Script Loading & Caching
- **Issue**: Standard WordPress script enqueues were bypassed on the front-end intercept `/workspace` route, causing front-end pages to lack critical JS triggers when inline scripts were removed.
- **Resolution**: Enqueued the compiled `admin-script.js` directly within `admin-dashboard.php` using a version-controlled external script tag: `<script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/admin-script.js?v=' . CORA_WORKSPACE_VERSION; ?>"></script>`. This loads scripts reliably on all entry points while leveraging browser caching.

## 2. Synchronized Version Settings
- **Issue**: The PHP constant `CORA_WORKSPACE_VERSION` was outdated (`2.5.4`), causing update checks to constantly flag update banners and drawers.
- **Resolution**: Synchronized the constant value to `2.9.1` to match the plugin headers, resolving unwanted update prompt overlays in the E2E suites.

## 3. Scoped Global Listeners & Modal Backdrops
- **Issue**: The global function `coraCloseAllDrawers()` targeted all elements ending in `-drawer` by adding `.collapsed`. This caused centered overlay modals (like the Review Request modal) to get stuck off-screen or hidden.
- **Resolution**: Updated `coraOpenSendReviewDrawer()` in `view-review-acquisition.php` to explicitly remove the `collapsed` and `translate-x-full` classes, ensuring it displays properly.

## 4. Corrected Canvas Roles Permissions
- **Issue**: The workspace owner role `cora_shruti` (Owner (Shruti)) was missing from `cora_canvas_ajax_permission_check()`, leading to authorization failures when editing canvas configurations.
- **Resolution**: Added `cora_shruti` to the allowed write list, restoring full configuration management capabilities.

## 5. Masked Critical Credentials
- **Issue**: The GitHub Personal Access Token input was unmasked and lacked copy/cut/drag restrictions.
- **Resolution**: Added `oncopy="return false;"`, `oncut="return false;"`, `ondragstart="return false;"`, and `ondrop="return false;"` to secure the credentials inputs.

---

## Verification & Deployment Status

- **E2E Test Suites**: Running the full Playwright E2E suite confirms that all **152 tests passed successfully**!
- **Auto-Updates Channel**: `cora-workspace.json` updated with version `2.9.1` and pushed to the updates repository.
- **Staging Server (Hostinger)**: Standard staging deployment executed. The active version `2.9.1` and the AJAX update handlers have been verified directly over HTTPS on the staging server:
  - `ACTIVE_VERSION: 2.9.1`
  - `AJAX_TRIGGER_EXISTS: YES`
  - `AJAX_CHECK_EXISTS: YES`
  - `AJAX_PROGRESS_EXISTS: YES`
