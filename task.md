# Task: Cora Forms Cards Overhaul & Universal "All Entries" 5th Tab

## 1. Objectives & Deliverables
- **Form Card Padding & Interaction Architecture**:
  - [x] Fix distorted card padding: Expanded card internal padding to `p-5 sm:p-6` with dedicated `p-3.5` internal container for campaign/standard details and `px-3 py-1.5` tag chips, preventing any text from touching borders or corners.
  - [x] Make entire form card clickable: Clicking anywhere on `.form-card` or the form title navigates directly to `#edit/${form.id}` without interfering with action buttons.
  - [x] Make action buttons reliable across all screen sizes:
    - Removed `hidden lg:flex` / `hidden sm:flex` restrictions on `.btn-edit-form`.
    - Removed `< 1024px` blockage in `handleRouting()` and `loadFormIntoEditor()` that was kicking users back to `#list`.
    - Fixed `ReferenceError: coraREData is not defined` in `.btn-view-live`.
  - [x] Built in-app interactive Form Live Preview Modal (`#cora-form-preview-modal`): Allows viewing any form in real time with desktop and mobile viewport toggles and a 1-click external tab launcher.

- **Universal 5th Top Tab: "All Entries" (`#all-entries`)**:
  - [x] Added 5th tab to `$forms_header_args['tabs']` with dynamic submission count badge (`#tab-all-entries-badge`).
  - [x] Built `#forms-all-entries-tab-content` DOM view:
    - Metric summary strip (Total Submissions, Active Forms with Entries, Completion Rate, Latest Activity).
    - Notion-style filter toolbar: Form source dropdown selector, status dropdown (Completed / Partial), live search input, 1-click CSV export, and refresh button.
    - Notion-style data table (Desktop table with columns `#`, `Form`, `Submitter / Data`, `Status`, `Submitted At`, `IP Address`, `Actions` + Mobile responsive card list).
  - [x] Enhanced Entry Inspector (`openEntryInspector`): Made universal across both single-form submissions drawer and the All Entries registry with slide sheet animation (slide-up on mobile, slide-over on desktop) and blurred monochromatic backdrop.
  - [x] Added REST deletion for submissions (`DELETE /wp-json/cora/v1/forms/submissions/{id}`) with custom `coraConfirmAction` monochromatic modal.
  - [x] Export to CSV: Exports all filtered submissions across all forms with dynamic field columns from `submitted_data`.

## 2. Granular Subtask Decomposition & Execution
- **Subtask 1: Backend REST Enhancements (`cora-workspace.php`)** [x]
  - Enhanced `cora_rest_get_all_form_submissions` to JOIN `cora_forms` (`SELECT s.*, f.title as form_title, f.form_key, f.settings as form_settings`).
  - Added `DELETE /wp-json/cora/v1/forms/submissions/(?P<id>\d+)` endpoint with tenancy permission checks.
- **Subtask 2: Header Tabs & 5th Tab Registration** [x]
  - Registered `all-entries` tab in `$forms_header_args['tabs']` with dynamic submission badge count.
- **Subtask 3: DOM Containers & Universal Inspector** [x]
  - Added `#forms-all-entries-tab-content` markup.
  - Made `#cora-entry-inspector` universal and mobile slide-up compliant (Rule 1 & Rule 12).
  - Added `#cora-form-preview-modal` with responsive device toggles.
- **Subtask 4: Client-Side Routing & Submissions Engine (`view-forms.php`)** [x]
  - Wired `#all-entries` and `#entries` in `handleRouting()`.
  - Implemented `loadAllWorkspaceSubmissions()`, `renderAllEntriesTable()`, `getSubmitterSummary()`, and `exportAllEntriesCsv()`.
  - Fixed form card delegated click handlers and unlocked editor across all viewport widths.
- **Subtask 5: Entries Drawer & Scrolling Overhaul** [x]
  - Fixed horizontal table blowout: Added `table-fixed` and set explicit column widths (`w-14`, `w-48 md:w-56`, `w-auto`, `w-24`, `w-36`, `w-28`, `w-24`).
  - Sanitized `getSubmitterSummary()`: Base64 digital signatures and long data blobs are detected and summarized compactly (`[Uploaded File / Signature]`), preventing massive width stretching.
  - Overhauled `#cora-entry-inspector`:
    - Desktop: Slides smoothly from the right (`top-0 right-0 bottom-0 w-[480px] max-w-[90vw] border-l translate-x-0`).
    - Mobile: Slides up from the bottom (`inset-x-0 bottom-0 h-[85vh] max-h-[85vh] rounded-t-3xl border-t translate-y-0`).
    - Added submitter quick overview strip (Name, Email/Phone).
    - Added inline signature/image preview `<img src="...">` inside cards instead of dumping 50,000-character base64 text strings.
    - Added sticky footer with "Copy JSON", "Delete", and "Done" actions.
  - Fixed function name collision: Renamed drawer submissions table function to `renderDrawerSubmissionsTable()`, resolving collision with builder view table.
  - Removed backdrop overlay from Entry Inspector (`openEntryInspector`): Background table remains 100% visible, clear, and unblurred. Added outside click detection to dismiss inspector seamlessly.
  - Bumped version to `4.9.372`.
