# Custom Roles Expansion - Cora Workspace

- [x] **Enhanced Custom Role Definition Form (`views/view-users.php`)**
  - [x] Base Role Template Selector (Branch Manager, Photographer, Editor, Viewer).
  - [x] Feature Permission Checkboxes / Blocks (CRM & Leads, Showings & Bookings, Financials, Media & Vault, Equipment, AI Suite, Attendance).
  - [x] Operational Access Level (Read-Only, Standard Contributor, Manager/Admin).
  - [x] Max Shoot/Booking Quota (Monthly numerical cap).

- [x] **Active Custom Roles Actions & Edit Drawer**
  - [x] Active Custom Roles table with Edit Permissions, Duplicate Role, and Delete actions.
  - [x] Right-sliding side drawer sheet `<aside id="cora-edit-custom-role-drawer">`.
  - [x] Form in edit drawer allowing updates to role label, access level, quota limits, and permissions matrix.
  - [x] Save button (`#save-custom-role-btn`) submitting to `cora_ajax_save_custom_role_permissions`.

- [x] **Backend AJAX Handlers (`cora-workspace.php`)**
  - [x] `cora_ajax_add_custom_role()` processing `permissions`, `base_template`, `access_level`, `max_quota`.
  - [x] `cora_ajax_save_custom_role_permissions()` updating `cora_custom_roles` and `cora_role_permissions` options.
  - [x] `cora_ajax_duplicate_custom_role()` duplicating role definition and permissions matrix.
  - [x] `cora_ajax_delete_custom_role()` with user assignment safety check.

- [x] **UI/UX & Guidelines**
  - [x] Monochromatic styling & clean vector icons.
  - [x] Toast notifications (`window.coraShowToast`).
  - [x] Single-active drawer enforcement (`window.coraCloseAllDrawers()`).

- [x] **URL Query Parameter Persistence for Sub-Tabs (`views/view-users.php`)**
  - [x] Update sub-tab click handler to push/replace URL query string with `?tab=tab-slug` via `history.replaceState(null, '', newUrl)`.
  - [x] DOM ready listener reads `tab` parameter and triggers `.trigger('click')` on matching `.cora-sub-tab` to restore active tab state on refresh.

- [x] **Office Geofence CTA & Top Stat Row Integration (`views/view-users.php` & `assets/js/admin-script.js`)**
  - [x] Task 5: Update views/view-users.php (Tenant isolation and key mappings for geofence settings retrieval).
  - [x] Task 6: Update cora-workspace.php (Tenant-scoped updates for geofencing AJAX and save handlers).
  - [x] Attach `window.openGeofenceDrawer = openGeofenceDrawer` and `window.closeGeofenceDrawer = closeGeofenceDrawer` at top of script tag in `views/view-users.php`.
  - [x] 3rd stat card in `#tab-attendance-logs` displays `OFFICE GEOFENCING`, status badge (`500m Enforced` or `Not Configured`), current address subtitle, and CTA button with `onclick="openGeofenceDrawer()"`.
  - [x] Stat card container clickable via `onclick="openGeofenceDrawer()"`.
  - [x] Redundant Log Punch card removed and Punch Log History table set to 100% full width.

- [x] **Notion/Shopify Custom Roles Sub-Tab Redesign (`views/view-users.php` & `assets/js/admin-script.js`)**
  - [x] Removed inline form from left column in `#tab-custom-roles`.
  - [x] Implemented full-width Custom Roles Overview header bar with title, subtitle, and primary CTA button (`onclick="openCreateCustomRoleDrawer()"`).
  - [x] Built Preset Role Quick-Clones / Template Cards row for Branch Manager, Photographer, Editor, and Viewer.
  - [x] Designed Active Custom Roles table displaying Role Name, System Identifier (`cora_role_key`), Feature Permission Tags as sleek neutral badges, Access Level, Monthly Quota, and compact Action Buttons (Edit Permissions, Duplicate, Delete).
  - [x] Created dedicated Create Custom Role Side Drawer sheet (`#cora-create-custom-role-drawer`) containing Role Name, Base Template Selector, Access Level, Quota Input, Feature Matrix Checkboxes, and Submit Button.
  - [x] Exposed `openCreateCustomRoleDrawer()` and `closeCreateCustomRoleDrawer()` in `views/view-users.php` and `assets/js/admin-script.js` enforcing single-active drawer rule via `window.coraCloseAllDrawers()`.- [x] **Attendance Logs Controls, Filtering, Reports & Share Expansion (`views/view-users.php`, `cora-workspace.php`, `assets/js/admin-script.js`)**
  - [x] Enhanced Attendance Filter Toolbar in `#tab-attendance-logs`: `#attendance-filter-user` (Employee Picker), `#attendance-filter-period` (Period Direction), `#attendance-date-start` & `#attendance-date-end` (Custom Date Range), `#attendance-filter-event` (Event Type), Automated Reports & Share CTA button, Export CSV CTA button.
  - [x] Automated Attendance Report & Share Side Drawer (`#cora-attendance-reports-drawer`): Right-sliding side drawer sheet with header, report scope controls (Time Horizon & Employee Target), and action blocks (Download CSV Export, Printable / PDF Summary, Send Email Digest, Copy Secure Share Link).
  - [x] Backend Filter & Report Endpoints (`cora-workspace.php`): Updated `cora_ajax_get_attendance_logs()` processing `user_id`, `period`, `start_date`, `end_date`, `event_type` and timestamp filtering. Created `cora_ajax_generate_attendance_report()` endpoint supporting email digests, printable summary payload, and secure share link tokens.
  - [x] JS Handlers & Single-Drawer Protection: Exposed `window.openAttendanceReportsDrawer` and `window.closeAttendanceReportsDrawer`, enforced `window.coraCloseAllDrawers()`, and wired filter dropdown event listeners for live non-reloading data updates.

- [x] **Redesigned Permissions Matrix Sub-Tab (`views/view-users.php` & `cora-workspace.php`)**
  - [x] Permissions Matrix Toolbar & Controls Bar inside `#tab-permissions-matrix`: Quick Search input (`#matrix-role-search`), `Reset Defaults` button, `Grant All (Selected Role)` button, `Save Matrix` button with live sync status badge (`● Live Sync Active`).
  - [x] Category Grouping Badges in Table Header: `CORE NAVIGATION` (Dashboard, Showings CRM / Shoots, Feature Hub / Portfolio / Client Leads), `OPERATIONAL` (Team & Roles, Equipment / Camera Gear), `ADMINISTRATIVE` (Financials, Settings).
  - [x] Sticky Left Column for **Role Title**: Pinned role title column with smooth horizontal scrolling and role row selection logic.
  - [x] Access Level Badges: Rendered Role Name + Access Level Badge (`Super Admin`, `Manager`, `Contributor`, `Read-Only`, `Custom`).
  - [x] Custom Styled Checkboxes & Row Hover Effects: Monochromatic checkboxes (`accent-zinc-950 dark:accent-zinc-100 rounded cursor-pointer`) and subtle row hover states (`hover:bg-zinc-50/70 dark:hover:bg-zinc-850/50`).
  - [x] Global System Lock for Super Admin: Clean "Global System Lock" badge with disabled check indicators.
  - [x] Backend AJAX Handler (`cora-workspace.php`): Updated `cora_ajax_save_permissions_matrix()` handling `matrix`, `role_key`, and `permissions` payloads cleanly, updating `cora_role_permissions`, and returning monochromatic toasts (`window.coraShowToast`).

- [x] **Global Side Drawer Overlay & Canvas Layout Stability (`admin-dashboard.php`, `assets/js/admin-script.js`, `views/view-users.php`)**
  - [x] Implement language synchronization on page load in `admin-dashboard.php`
  - [x] Mask credentials fields in `views/view-settings-suite.php` and add block/copy restrictions
  - [x] Global Drawer Backdrop (`#cora-drawer-backdrop`) added right before closing `</body>` tag in `admin-dashboard.php` with backdrop blur, dark/light mode overlays, and `onclick="window.coraCloseAllDrawers()"`.
  - [x] CSS Drawer Styles & Canvas Width Isolation (`admin-dashboard.php`): Enforced `width: 500px !important`, `max-width: 90vw !important`, `top: 0`, `right: 0`, `z-index: 9999`, smooth `cubic-bezier(0.16, 1, 0.3, 1)` opening transitions, `box-shadow: -10px 0 30px rgba(0,0,0,0.15)` when open, and locked main workspace container width to 100% (`main, .cora-main-content, #cora-workspace-container { width: 100% !important; flex: 1 1 auto !important; }`).
  - [x] Global Drawer Controllers (`assets/js/admin-script.js`): Updated `window.coraCloseAllDrawers()` to hide `#cora-drawer-backdrop` and remove `cora-drawer-open` from `body`. Updated all drawer openers (`openInviteDrawer`, `openEditUserDrawer`, `openGeofenceDrawer`, `openCreateCustomRoleDrawer`, `openEditCustomRoleDrawer`, `openAttendanceReportsDrawer`, `coraToggleAddShowingDrawer`) to invoke `window.coraCloseAllDrawers()`, remove `.collapsed`, and show `#cora-drawer-backdrop`.
  - [x] Global Keyboard Shortcut: Added `Escape` key event listener (`e.key === 'Escape' || e.keyCode === 27`) to invoke `window.coraCloseAllDrawers()`.

- [x] **Financial Backend AJAX Handlers & Financial Intelligence Endpoints (`cora-workspace.php`)**
  - [x] `cora_ajax_get_financial_data`: Registered actions `wp_ajax_cora_ajax_get_financial_data`, `wp_ajax_cora_get_financial_data`, `wp_ajax_cora_fetch_financials`. Filters `cora_financial_ledger`, `cora_invoices`, `cora_payouts` by period, start_date, end_date, industry_scope, and status. Calculates `total_inflow`, `total_outflow`, `net_profit`, `margin_pct`, `pending_dues`, and `chart` data.
  - [x] `cora_ajax_add_ledger_entry`: Registered actions `wp_ajax_cora_ajax_add_ledger_entry`, `wp_ajax_cora_add_ledger_entry`. Validates `cora_ajax_nonce` and appends entry with id (uniqid), timestamp, user_id, user_name to `cora_financial_ledger` option.
  - [x] `cora_ajax_create_invoice`: Registered actions `wp_ajax_cora_ajax_create_invoice`, `wp_ajax_cora_create_invoice`. Validates nonce, computes deposit_amount and due_balance, generates unique invoice # (`INV-` + date + rand) and share token, and saves to `cora_invoices` option.
  - [x] `cora_ajax_process_payout`: Registered actions `wp_ajax_cora_ajax_process_payout`, `wp_ajax_cora_process_payout`. Calculates net payout after split % and tax %, saves to `cora_payouts` option, and logs an outflow entry in `cora_financial_ledger`.
  - [x] `cora_ajax_generate_financial_pdf_report`: Registered actions `wp_ajax_cora_ajax_generate_financial_pdf_report`, `wp_ajax_cora_generate_financial_pdf_report`. Returns formatted financial statement payload for browser printing and CSV export.

- [x] **Leads Persistence & Reseed**
  - [x] Unconditionally set `cora_migration_v2_complete` at the end of custom table migrations.
  - [x] Deactivate old legacy `cora-real-estate` plugin to prevent duplicate functions.
  - [x] Clear `wp_cora_leads` and seed exactly 3 leads for each of the 5 statuses.
  - [x] Verify persistence across refreshes.
  - [x] All Playwright E2E tests pass.

- [x] **Leads CRM Refactoring & Team Assignment (v2.5.3 Release)**
  - [x] **Notion-Inspired Minimalist CRM Styling**: Replaced icon badges with clean inline gray tags, monochromatic table formatting, and subtle borders.
  - [x] **Team Member Assignment Filter**: Toolbar dropdown and card avatars displaying assigned user, with list filter persistence.
  - [x] **One-Tap Lead Status Transitions**: Trigger status cycles directly from Kanban boards and main drawer sheets.
  - [x] **SLA Status Header Card**: Integrated visual SLA countdown status card at the top of detail drawers.
  - [x] **Activity Timeline Logging**: Auto-generate activity logs for user assignments and status movements.
  - [x] **Plugin Packaging & Updates Dispatch**: Bumped version to `2.5.3` and successfully released zip to sub-repository.

- [x] **Forms Tenant-Scoped Security & Data Isolation (v2.9.100 Release)**
  - [x] Resolve subsite AJAX REST 302 redirects by bypassing handled workspace routing checks for REST and AJAX endpoints.
  - [x] Implement secure dynamic user-agency mapping inside `cora_rest_save_form` and `cora_rest_get_forms` instead of hardcoding default fallback ID `1`.
  - [x] Run E2E test suites to verify forms creation, draft status, and list rendering compile and execute cleanly.
  - [x] Query remote demo database to migrate and assign existing form records (like `frm_58795621`) to `agency_id = 4` to match active workspace `/test-1/` owner.
  - [x] Build, package, and deploy plugin version `2.9.100` to Demo and Main Production servers.
  - [x] Run remote Playwright validation to verify forms count and cards list load successfully in the live user dashboard (`TOTAL FORMS: 5`).

- [x] **E2E Test Suite Failures Resolution**
  - [x] Fix Audit Logs CSV Export E2E Failure in `views/view-audit-panel.php`.
  - [x] Fix Visibility Test Switcher E2E Failure in `tests/e2e/test-visibility.spec.ts`.
  - [x] Execute E2E verification tests locally to verify success.
