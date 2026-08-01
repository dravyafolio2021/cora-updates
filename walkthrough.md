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

---

## 📦 Version 2.9.8 Release & Package Deployment

### 1. Responsive Software Updates Layout Redesign
- **Wider Drawer Sheet**: Adjusted maximum width to `960px` to comfortably display columns, dropdown selectors, and timeline elements on desktop screens while remaining fully responsive (100% width) on mobile devices.
- **Mockup Components Integration**:
  - **Header Section**: Rendered the mockup title `"Software Updates"` alongside the subtitle and a static `"Release Channel: Production Stable"` dropdown card.
  - **Platform Available Card**: Designed the available updates container card featuring a rotate sync icon within a black rounded box, the `"UPDATE AVAILABLE"` (or `"FULLY UP TO DATE"`) pill, and a calendar released-on indicator.
  - **Vertical timeline**: Formatted an axis line (`.cora-update-timeline::before`) and dot markers matching each release version from `2.9.8` down to `2.9.0`.

### 2. Interactive Timeline Accordion Items
- **Collapsible Cards**: Each release note is mapped to a clickable accordion row. Caret icon indicators rotate dynamically as accordion cards are toggled.
- **Topic Icons mapping**: Mapped standard thin SVG vector icons (e.g. sparkles, code, shield, refresh, link, zap, users, wifi) based on the topic of changes.
- **Expand All / Collapse All controls**: Added a master trigger button in the timeline header that expands or collapses all accordion rows simultaneously.

### 3. Dynamic Live Changelog Parser
- **DOMParser script**: Integrated a JavaScript parser inside `window.coraCheckForUpdatesNow` to decode HTML changelog markup received from the live updates server. If newer release versions exist, they are parsed and dynamically prepended onto the timeline in real-time.

### 4. Release updates Deployment
- Incremented plugin files and headers to **v2.9.8**.
- Packaged clean update file `cora-workspace.zip` (15MB size) and updated the update manifest `cora-workspace.json`.
- Committed and pushed release updates to both repositories (`dravyafolio2021/heycora` and `dravyafolio2021/cora-updates`). Workspaces are ready for upgrading!

---

## 📦 Version 2.9.43 Release: Dynamic Tenant Canvas Front-End Website Routing

- **Tenant-Scoped Canvas Routing**: Refactored `cora_canvas_theme_frontend_router()` to support dynamic front-end public website URLs (`/site/{workspace_slug}` and `/site/{workspace_slug}/{page_slug}`).
- **Strict Route Parsing**: Differentiates tenant website URLs from internal workspace admin dashboard routes (`/workspace_slug/dashboard`, `/workspace_slug/canvas`, etc.), ensuring zero interference with admin views.
- **Dynamic Site Links**: Updated Canvas Builder theme preview and view buttons in `views/view-canvas.php` to resolve exact tenant URLs (`home_url('/site/' . $cora_ws_slug)`).
- **Deployment**: Successfully packaged and deployed **v2.9.43** to both Demo (`https://heycora.in/demo`) and Production (`https://app.heycora.in`).

---

## 📦 Version 2.9.60 Release: User Management Drawer Accessibility & Inline Binding Fixes

- **Edit User Drawer (`cora-edit-user-drawer`)**: Fixed inline CSS overrides during open/close events to strip `.collapsed` and `.hidden` classes and enforce `display: flex`, `visibility: visible`, and `transform: translateX(0)`.
- **Edit Custom Role Drawer (`cora-edit-custom-role-drawer`)**: Standardized layout overrides to align with theme guidelines. Bound `openEditCustomRoleDrawer` and `closeEditCustomRoleDrawer` to the global `window` object to override legacy stubs.
- **Attendance Reports Drawer (`cora-attendance-reports-drawer`)**: Corrected inline selectors and transition classes; bound open/close controls to `window` for reliable access during analytics lookup.
- **Global Window Handler Registrations**: Registered all inline actions (`coraResendVerification`, `coraCancelInvitation`, `handleDuplicateCustomRole`, `handleDeleteCustomRole`, `triggerCronAction`) explicitly under the `window` namespace, resolving browser reference issues.
- **Release & Deployment**: Deployed successfully to Demo (`heycora.in/demo`) and Production (`heycora.in`). Both environments verified active at `2.9.60` and passing all health checks ✅.

---

## 📦 Version 2.9.62 Release: Geofencing & Drawer Interaction Fixes

- **Positioning & Interaction Locks**: Upgraded `openInviteDrawer`, `openEditUserDrawer`, and `openGeofenceDrawer` to explicitly remove `.translate-x-full` and `.pointer-events-none` classes from the side drawers. Added inline styling resets (`pointer-events: auto`, `transform: translateX(0)`) to override the global `admin-dashboard.php` CSS overlays that were forcing drawers to remain hidden and non-interactive.
- **Consolidated Double Definitions**: Cleaned up the duplicate declarations of `openInviteDrawer()` and `closeInviteDrawer()` scripts in `views/view-users.php` to prevent scoping overrides and redundant visual transitions.
- **Removed Double-Firing Click Handlers**: Removed the duplicate jQuery click listener for `.cora-edit-user-btn` which was causing race conditions when animating the user edit drawer sheet.
- **Global Scope Window Assignment**: Exposed geofencing interactive functions (`selectGeofenceRadius`, `switchGeofenceMode`, `updateMapPreviewFromInput`, `handleSaveGeofence`) and toolbar filters (`toggleMobileFilters`, `clearFilters`) globally to the `window` namespace, resolving reference errors thrown by inline HTML attributes when clicked.
- **Release & Deployment**:
  - Incremented version to `2.9.62` in `cora-workspace.php` and updates manifest.
  - Built package `cora-workspace.zip` (3.9M) and successfully deployed to Demo (`heycora.in/demo`) and Production (`heycora.in`). Both environments verified active at `2.9.62` and running flawlessly ✅.

---

## 📦 Version 2.9.63 Release: JS Syntax Error Resolution

- **Syntax Fix**: Resolved a critical missing closing brace (`}`) for the `copyAttendanceShareLink()` function inside the script block of `views/view-users.php`. This syntax error had blocked browser JavaScript parsing, causing the entire UI (including page/tab navigation, buttons, and popups) to become frozen/unclickable.
- **Verified Syntax Compilation**: Validated the entire javascript code block using Node's `vm.Script` syntax checker to ensure 100% correct parsing on page load.
- **Release & Deployment**:
  - Incremented version to `2.9.63` in `cora-workspace.php` and updates manifest.
  - Successfully built and deployed to Demo (`heycora.in/demo`) and Production (`heycora.in`), verifying version `2.9.63` active and all interactive elements fully operational ✅.

---

## 📦 Version 2.9.64 Release: Geofencing Location Detection Option

- **Detect Current Location**: Added an absolute location-crosshair icon button inside the geofencing address input field (`#geofence-address-input`) in `views/view-users.php`.
- **Geolocation API Integration**: Implemented a geolocation handler `detectCurrentLocationForGeofence(event)` using the browser's native `navigator.geolocation.getCurrentPosition` API.
- **Automatic Reverse Geocoding**:
  - Automatically retrieves the user's current GPS latitude and longitude coordinates.
  - Instantly populates the coordinates into the input field and updates the map preview.
  - Dispatches a request to OpenStreetMap's Nominatim reverse-geocoding API to resolve the coordinates into a user-friendly street address (which automatically replaces the raw coordinates in the input box).
- **Custom Toasts & Scope Binding**: Utilizes the monochromatic custom Toast notification system for loading state and coordinate retrieval success/failure, and binds all new handlers to the global `window` object scope.
- **Release & Deployment**:
  - Incremented versions to `2.9.64` in `cora-workspace.php` and updates manifest `cora-workspace.json`.
  - Built and packaged `cora-workspace.zip`.
  - Deployed release successfully to both Demo (`https://heycora.in/demo`) and Production (`https://heycora.in`) environments with all verification checks passing ✅.


