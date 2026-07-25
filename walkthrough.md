# Walkthrough - Custom Roles Feature Expansion

The Custom Roles feature in **Cora Workspace** has been expanded across frontend (`views/view-users.php`) and backend (`cora-workspace.php`).

## 1. Enhanced Custom Role Definition Form (`views/view-users.php`)
Located in `#tab-custom-roles`:
- **Base Role Template Selector**: Allows selecting pre-configured templates (Branch Manager, Photographer, Editor, Viewer) to auto-fill access levels and feature permission checkboxes.
- **Operational Access Level**: Selectable options for Read-Only (`read_only`), Standard Contributor (`contributor`), and Manager/Admin (`manager`).
- **Max Shoot/Booking Quota**: Numerical monthly cap input field.
- **Categorized Feature Permission Toggles**: Checkbox toggles for module permissions (CRM & Leads, Showings & Bookings, Financials, Media & Vault, Equipment, AI Suite, Attendance).

## 2. Active Custom Roles Table & Edit Drawer Sheet
- **Active Custom Roles Table**:
  - Role Name column
  - Identifier & Base Template column
  - Access Level badge & Monthly Quota column
  - Actions column featuring:
    - `cora-edit-custom-role-btn` ("Edit Permissions") button
    - `cora-duplicate-custom-role-btn` ("Duplicate Role") button
    - Delete button with two-step confirmation & user assignment safety check
- **Edit Custom Role Side Drawer Sheet**:
  - Right-sliding drawer `<aside id="cora-edit-custom-role-drawer">` matching Cora workspace drawer design.
  - Allows editing role display name, access level, shoot quota, and permissions matrix.
  - `#save-custom-role-btn` triggers `cora_ajax_save_custom_role_permissions` via AJAX.

## 3. Backend AJAX Handlers (`cora-workspace.php`)
- `cora_ajax_add_custom_role()` (aliased to `cora_create_custom_role`):
  - Sanitizes and processes `role_name`, `base_template`, `access_level`, `max_quota`, and `permissions`.
  - Creates the WordPress role (`add_role()`).
  - Stores metadata in `cora_custom_roles` option and permission mapping in `cora_role_permissions` option.
- `cora_ajax_save_custom_role_permissions()`:
  - Updates existing custom role metadata in `cora_custom_roles` and permissions in `cora_role_permissions`.
  - Updates WordPress role display name if changed.
- `cora_ajax_duplicate_custom_role()`:
  - Clones an existing role's definition, metadata, and permissions matrix into a new unique role key (`Copy of [Role Name]`).
- `cora_ajax_delete_custom_role()`:
  - Enforces assignment safety check by checking if any users are currently assigned to the role (`get_users(array('role' => $role_key))`). If assigned, returns an error toast preventing deletion.

## 4. Verification & Testing
- Tested on local environment `cora.local`.
- Confirmed single-active drawer rule (`window.coraCloseAllDrawers()`).
- Direct feedback routed through monochromatic toasts (`window.coraShowToast`).

## 5. Sub-Tab URL Query Parameter Persistence (`views/view-users.php`)
- **Sub-Tab Click Handler**: Updates URL query string dynamically using `history.replaceState(null, '', newUrl)` with `?tab=tab-slug` (e.g. `?tab=active-members`, `?tab=pending-invitations`, `?tab=permissions-matrix`, `?tab=attendance-logs`, `?tab=custom-roles`).
- **DOM Ready State Restoration**: Reads `tab` parameter on page load (`new URLSearchParams(window.location.search)`). If `activeTab` exists, finds the matching `.cora-sub-tab` and triggers `.trigger('click')`, restoring the active sub-tab view without resetting to default upon browser refresh.

## 6. Attendance Logs Layout & Geofence CTA Integration (`views/view-users.php` & `assets/js/admin-script.js`)
- **Window Attachment for Drawer Handlers**: Immediately attaches `window.openGeofenceDrawer = openGeofenceDrawer` and `window.closeGeofenceDrawer = closeGeofenceDrawer` at the top of the `<script>` block in `views/view-users.php` to prevent `undefined` runtime errors on CTA clicks.
- **Removed Duplicate Log Punch Card**: Completely removed the redundant left "Log Punch" card widget from `#tab-attendance-logs`. Primary punch functionality is maintained exclusively in the top workspace header widget.
- **Integrated Office Geofencing Stat Card**: Transformed Card 3 in the top stat row into an interactive **Office Geofence & Location Control Card**:
  - Upper label: `OFFICE GEOFENCING` with real-time status pill (`#stat-geofence-status`, e.g. `500m Enforced` or `Not Configured`).
  - Middle text: Location address/name preview (`#cora-geofence-current-address`).
  - Action trigger: Interactive button `<button type="button" onclick="openGeofenceDrawer()">Map & Settings</button>` (and clicking anywhere on the card invokes `openGeofenceDrawer()`).
## 7. Custom Roles Sub-Tab Redesign (`views/view-users.php` & `assets/js/admin-script.js`)
- **Full-Width Custom Roles Overview**:
  - Removed the heavy inline creation form from the left column of `#tab-custom-roles`.
  - Replaced main view with a spacious, Notion/Shopify-style full-width overview.
  - Header bar with title, subtitle, and primary CTA button: `<button type="button" onclick="openCreateCustomRoleDrawer()">+ Create Custom Role</button>`.
- **Preset Role Quick-Clones / Template Cards Row**:
  - Quick-start template cards for **Branch Manager**, **Photographer**, **Editor**, and **Viewer**.
  - Clicking any template card invokes `openCreateCustomRoleDrawer(templateKey)`, automatically selecting the template and setting default permissions & access levels.
- **Active Custom Roles Table**:
  - Displays Role Name + System Identifier (`cora_role_key`).
  - Displays Feature Permission Tags (e.g. `CRM`, `Showings`, `Media`, `Attendance`, `Financials`, `Equipment`, `AI Suite` as sleek neutral badges).
  - Displays Access Level (`Manager`, `Contributor`, `Read-Only`) & Monthly Quota badge.
  - Compact action buttons: `Edit Permissions` (drawer), `Duplicate` (icon button), and `Delete` (icon button).
- **Dedicated "Create Custom Role" Side Drawer (`#cora-create-custom-role-drawer`)**:
  - Right-sliding drawer `<aside id="cora-create-custom-role-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-[520px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">`.
  - Contains inputs for Role Display Name (`#custom-role-name`), Base Role Template Selector (`#custom-role-base-template`), Operational Access Level (`#custom-role-access-level`), Max Shoot/Booking Quota (`#custom-role-max-quota`), Feature Permissions Matrix checkboxes (`.custom-role-perm-cb`), and Submit button (`#create-role-submit-btn`).
- **JS Drawer Controllers & Handlers**:
  - Exposed `window.openCreateCustomRoleDrawer` and `window.closeCreateCustomRoleDrawer` in both `views/view-users.php` and `assets/js/admin-script.js`.
  - Enforced single-active drawer policy (`window.coraCloseAllDrawers()`).
## 8. Attendance Logs Controls, Filtering, Reports & Share Expansion (`views/view-users.php`, `cora-workspace.php`, `assets/js/admin-script.js`)
- **Enhanced Attendance Filter Toolbar (`views/view-users.php`)**:
  - Located in `#tab-attendance-logs`:
  - **Employee Picker Dropdown** (`#attendance-filter-user`): Filter by "All Team Members" or specific workspace users from `$users`.
  - **Period Direction Dropdown** (`#attendance-filter-period`): Filter by "All Time", "Today", "Yesterday", "This Week", "This Month", or "Custom Date Range".
  - **Custom Date Range Inputs** (`#attendance-date-start`, `#attendance-date-end`): Dynamically revealed when "Custom Date Range" is selected.
  - **Event Type Dropdown** (`#attendance-filter-event`): Filter by "All Event Types", "Punch In", or "Punch Out".
  - **Primary CTA Buttons**:
    - `<button type="button" onclick="openAttendanceReportsDrawer()">Automated Reports & Share</button>`
    - `<button type="button" onclick="exportAttendanceCSV()">Export CSV</button>`
- **Automated Attendance Report & Share Side Drawer (`#cora-attendance-reports-drawer`)**:
  - Right-sliding side drawer sheet `<aside id="cora-attendance-reports-drawer" class="collapsed fixed top-0 right-0 z-50 h-full w-[500px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">`.
  - **Header**: Title "Automated Attendance Reports & Share" + close trigger `closeAttendanceReportsDrawer()`.
  - **Report Scope Controls**: Time Horizon (Daily Summary, Weekly Report, Monthly Payroll Sheet, Custom Range) and Employee Target (Entire Workspace vs Specific Employee).
  - **Export & Sharing Action Blocks**:
    - **Download CSV Export** (`exportAttendanceCSV()`)
    - **Printable / PDF Summary** (`generatePrintableAttendanceReport()`)
    - **Send Email Digest** (`sendAttendanceEmailDigest()`)
    - **Copy Secure Share Link** (`copyAttendanceShareLink()`)
- **Backend Filter & Report Endpoints (`cora-workspace.php`)**:
  - `cora_ajax_get_attendance_logs()` (bound to `wp_ajax_cora_get_attendance_logs` & `wp_ajax_cora_fetch_attendance`): processes `user_id`, `period`, `start_date`, `end_date`, and `event_type` to dynamically filter stored timestamp logs.
  - `cora_ajax_generate_attendance_report()` (bound to `wp_ajax_cora_generate_attendance_report`): supports sending HTML email digests (`wp_mail()`), printable report summary data, and generating secure token share links.
- **JS Handlers & Single-Drawer Protection (`assets/js/admin-script.js` & `views/view-users.php`)**:
  - Exposed `window.openAttendanceReportsDrawer` and `window.closeAttendanceReportsDrawer`.
  - Enforced single-active drawer protection via `window.coraCloseAllDrawers()`.
  - Wired live event listeners on filter dropdowns to execute `fetchAttendanceLogs()` without page refresh.

## 9. Redesigned Permissions Matrix Sub-Tab (`views/view-users.php` & `cora-workspace.php`)
- **Permissions Matrix Header Controls & Toolbar (`views/view-users.php`)**:
  - Top header controls bar inside `#tab-permissions-matrix`.
  - **Quick Search Input** (`#matrix-role-search`): Real-time role row filtering as text is typed.
  - **Action Buttons**:
    - `Reset Defaults` (`#matrix-reset-defaults-btn`): Resets role matrix permissions to standard default configurations.
    - `Grant All (Selected Role)` (`#matrix-grant-all-btn`): Grants all module feature permissions for the currently selected/active role row.
    - `Save Matrix` (`#matrix-save-btn`): Explicitly triggers AJAX matrix saving with live sync status badge (`● Live Sync Active`).
- **Ultra-Modern Matrix Table Design (`views/view-users.php`)**:
  - **Category Grouping Badges**: Header row categorized into **CORE NAVIGATION** (Dashboard, Showings CRM / Shoots, Feature Hub / Portfolio / Client Leads), **OPERATIONAL** (Team & Roles, Equipment / Camera Gear), and **ADMINISTRATIVE** (Financials, Settings) with clean neutral badges.
  - **Sticky Left Column for Role Title**: Pinned role title column with smooth horizontal scrolling.
  - **Role Title + Access Level Badges**: Displays Role Name alongside Access Level Badge (`Super Admin`, `Manager`, `Contributor`, `Read-Only`, `Custom`).
  - **Row Selection & Subtle Hover**: Interactive row click selection (`.selected-matrix-role`) and smooth hover styling (`hover:bg-zinc-50/70 dark:hover:bg-zinc-850/50`).
  - **Custom Styled Checkboxes**: Custom monochromatic checkboxes (`accent-zinc-950 dark:accent-zinc-100 rounded cursor-pointer`).
  - **Super Admin Global System Lock**: Displays a clean "Global System Lock" badge for Super Admin / Owner rows with disabled checked indicators.
- **Backend AJAX Matrix Handler (`cora-workspace.php`)**:
  - `cora_ajax_save_permissions_matrix()` (bound to `cora_ajax_save_permissions_matrix`, `cora_save_permissions_matrix`, and `cora_save_role_permissions`): Sanitizes and saves the matrix mapping into WordPress `cora_role_permissions` option, ensuring system administrators always maintain complete access, and returns monochromatic success toasts (`window.coraShowToast`).

## 10. Global Side Drawer Overlay & Canvas Layout Stability (`admin-dashboard.php`, `assets/js/admin-script.js`, `views/view-users.php`)
- **Global Drawer Backdrop Element (`admin-dashboard.php`)**:
  - Placed right before closing `</body>` tag: `<div id="cora-drawer-backdrop" onclick="window.coraCloseAllDrawers()" class="hidden fixed inset-0 bg-black/30 dark:bg-black/60 z-[9990] backdrop-blur-[1.5px] transition-opacity duration-200 cursor-pointer"></div>`.
- **CSS Width & Position Isolation (`admin-dashboard.php`)**:
  - Updated `aside[id$="-drawer"]` CSS rules: `position: fixed !important; top: 0 !important; right: 0 !important; z-index: 9999 !important; height: 100vh !important; width: 500px !important; max-width: 90vw !important; transition: transform 300ms cubic-bezier(0.16, 1, 0.3, 1), visibility 300ms ease-in-out !important;`.
  - Added shadow on open: `aside[id$="-drawer"]:not(.collapsed) { box-shadow: -10px 0 30px rgba(0,0,0,0.15) !important; }`.
  - Protected main workspace container width: `main, .cora-main-content, #cora-workspace-container { width: 100% !important; max-width: 100% !important; flex: 1 1 auto !important; }`.
- **Global Drawer Controllers & Shortcuts (`assets/js/admin-script.js` & `views/view-users.php`)**:
  - Updated `window.coraCloseAllDrawers()` to collapse drawers (`addClass('collapsed')`), hide backdrop (`addClass('hidden')`), and remove body state (`removeClass('cora-drawer-open')`).
  - Updated all drawer openers (`openInviteDrawer`, `openEditUserDrawer`, `openGeofenceDrawer`, `openCreateCustomRoleDrawer`, `openEditCustomRoleDrawer`, `openAttendanceReportsDrawer`, `coraToggleAddShowingDrawer`) to call `window.coraCloseAllDrawers()`, un-collapse target drawer, and show backdrop (`$('#cora-drawer-backdrop').removeClass('hidden')`).
  - Added global Escape key listener (`$(document).on('keydown', function(e) { if (e.key === 'Escape' || e.keyCode === 27) window.coraCloseAllDrawers(); })`).
- **Responsive Card Grids (`views/view-users.php`)**:
  - Refined card grid rows to `grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4` to prevent text/stat clutter across screen sizes and drawer states.


