# Cora Real Estate Platform v0.1 - Plugin Codebase Analysis (M2 & M3)

This report details the findings from the investigation of the Cora Real Estate Platform v0.1 plugin codebase, covering script mismatches, AJAX handlers, mobile responsiveness, native dialogue overlays, right-sliding drawers, and visual styling constraints.

---

## 1. Naming/Callback Mismatches & Missing Stubs in `assets/js/admin-script.js`

A detailed cross-reference of the templates inside the `views/` directory and the JavaScript logic in `assets/js/admin-script.js` revealed significant mismatches and missing stubs in the **Appearance**, **Comments**, and **Media-Editor** modules.

### A. Appearance Module
*   **Template File**: `views/view-appearance.php`
*   **JS Section**: `assets/js/admin-script.js` (lines 6629–6647)
*   **Mismatches / Missing Callbacks**:
    *   `onclick="coraSaveAppearanceSettings()"` (called on line 35 of `view-appearance.php`): **Missing from JS**. JS defines `coraSaveMenuStructure` (line 6635), `coraSaveWidgetSettings` (line 6639), and `coraSaveCustomCSS` (line 6643), but not a unified saving function.
    *   `onclick="coraOpenMediaSelector('...')"` (called on lines 78, 85 of `view-appearance.php`): **Missing from JS**. There is no media selector popup logic defined for logo or favicon URLs.
    *   `onclick="coraOpenNewMenuDrawer()"` (called on line 110 of `view-appearance.php`): **Missing from JS**.
    *   `onclick="coraRemoveMenuItem(...)"` (called on line 138 of `view-appearance.php`): **Missing from JS**.
    *   `onclick="coraOpenAddMenuItemDrawer()"` (called on line 149 of `view-appearance.php`): **Missing from JS**.
    *   `onclick="coraCloseAddMenuItemDrawer()"` (called on lines 166, 202 of `view-appearance.php`): **Missing from JS**.
    *   `onchange="coraToggleMenuItemTypeFields(...)"` (called on line 174 of `view-appearance.php`): **Missing from JS**.
    *   `onclick="coraSubmitMenuItem()"` (called on line 203 of `view-appearance.php`): **Missing from JS**.
*   **Impact**: The Appearance layout rendering and Menu Builder drawer components are completely static, and any click actions on the page result in console JavaScript errors.

### B. Comments Module
*   **Template File**: `views/view-comments.php`
*   **JS Section**: `assets/js/admin-script.js` (lines 6582–6627)
*   **Mismatches / Missing Callbacks**:
    *   `onclick="coraRefreshComments()"` (called on line 41 of `view-comments.php`): **Missing from JS**.
    *   `onclick="coraModerateComment(...)"` (called on lines 125, 130, 142, 149, 154 of `view-comments.php`): **Missing from JS**. In JS, the status update function is named `window.coraUpdateCommentStatus(id, action)` (line 6618).
    *   `onclick="coraOpenCommentReplyDrawer(...)"` (called on line 136 of `view-comments.php`): **Missing from JS**. JS contains `window.coraOpenCommentReplyModal(id, author, excerpt)` (line 6601), but it targets modal selectors (`#cora-modal-reply-comment`) rather than the drawer element IDs in the template.
    *   `onclick="coraDeleteCommentPermanent(...)"` (called on line 158 of `view-comments.php`): **Missing from JS**.
    *   `onclick="coraCloseCommentReplyDrawer()"` (called on lines 178, 204 of `view-comments.php`): **Missing from JS**.
    *   `onclick="coraSubmitCommentReply()"` (called on line 205 of `view-comments.php`): Exists in JS (line 6608) but targets input field `#cora-reply-content` instead of `#cora-reply-textarea` used in the template drawer.
*   **Impact**: Comment moderation actions (Approve, Unapprove, Spam, Trash, Delete, Reply) cannot be executed because of naming mismatches and mismatched element selectors.

### C. Media-Editor Module
*   **Template File**: `views/view-media-editor.php`
*   **JS Section**: `assets/js/admin-script.js` (lines 6685–6721)
*   **Mismatches / Missing Callbacks**:
    *   `onclick="coraOpenMediaUploader()"` (called on line 37 of `view-media-editor.php`): **Missing from JS**.
    *   `onchange="coraLoadMediaIntoEditor(...)"` (called on line 52 of `view-media-editor.php`): **Missing from JS**.
    *   `onclick="coraResetEditorCanvas()"` (called on line 63 of `view-media-editor.php`): **Missing from JS**.
    *   `onclick="coraSetCropRatio(...)"` (called on lines 71–74 of `view-media-editor.php`): **Missing from JS**.
    *   `onclick="coraRotateImage(...)"` (called on lines 79, 82 of `view-media-editor.php`): **Missing from JS**.
    *   `onclick="coraFlipImage(...)"` (called on line 85 of `view-media-editor.php`): **Missing from JS**.
    *   `onclick="coraSaveEditedImage()"` (called on line 112 of `view-media-editor.php`): Exists in JS (line 7085) but is defined at the very end of the script as a mock toast fallback:
        ```javascript
        window.coraSaveEditedImage = function() {
            window.coraShowToast("Media updated successfully.");
        };
        ```
    *   `onclick="coraSaveMediaMetadata()"` (called on line 156 of `view-media-editor.php`): Exists in JS (line 7058) at the end of the script and maps correctly to the AJAX hook.
*   **Impact**: While metadata saving works, all core visual transformations (cropping, rotating, flipping, resetting, loading media) are completely unimplemented in JavaScript.

---

## 2. AJAX Form Handlers Inspection

We identified and analyzed all registered AJAX actions (`wp_ajax_cora_*`) in the core plugin file `cora-real-estate.php` and mapped them to the front-end AJAX requests in `assets/js/admin-script.js`.

### A. List of Registered Hook Mappings

| AJAX Action Hook (`cora-real-estate.php`) | JS Trigger / Page Request | Status / Alignment |
| :--- | :--- | :--- |
| `wp_ajax_cora_re_resend_verification` | `cora_re_resend_verification` | Matches |
| `wp_ajax_cora_save_role_permissions` | `cora_save_role_permissions` | Matches |
| `wp_ajax_cora_create_team_user` | `cora_create_team_user` | Matches |
| `wp_ajax_cora_re_save_listing` | `cora_re_save_listing` | Matches (PHP callback `cora_ajax_save_equipment`) |
| `wp_ajax_cora_assign_equipment` | `cora_assign_equipment` | Matches |
| `wp_ajax_cora_re_save_showing_assignments` | `cora_re_save_showing_assignments` | Matches (PHP callback `cora_ajax_save_crew_assignments`) |
| `wp_ajax_cora_delete_team_user` | `cora_delete_team_user` | Matches |
| `wp_ajax_cora_update_team_user` | `cora_update_team_user` | Matches |
| `wp_ajax_cora_re_delete_listing` | `cora_re_delete_listing` | Matches (PHP callback `cora_ajax_delete_equipment`) |
| `wp_ajax_cora_re_save_document` | `cora_re_save_document` | Matches |
| `wp_ajax_cora_share_document` | `cora_share_document` | Matches |
| `wp_ajax_cora_sync_google_doc` | `cora_sync_google_doc` | Matches |
| `wp_ajax_cora_save_portfolio` | `cora_save_portfolio` | Matches |
| `wp_ajax_cora_delete_portfolio` | `cora_delete_portfolio` | Matches |
| `wp_ajax_cora_toggle_portfolio_like` | `cora_toggle_portfolio_like` | Matches (Triggered in `public-gallery-view.php`) |
| `wp_ajax_cora_re_submit_lead` | `cora_re_submit_lead` | Matches |
| `wp_ajax_cora_update_lead_status` | `cora_update_lead_status` | Matches |
| `wp_ajax_cora_re_delete_lead` | `cora_re_delete_lead` | Matches |
| `wp_ajax_cora_re_convert_lead_to_client` | `cora_re_convert_lead_to_client` | Matches |
| `wp_ajax_cora_delete_client` | `cora_delete_client` | Matches |
| `wp_ajax_cora_save_booking` | `cora_save_booking` | Matches |
| `wp_ajax_cora_update_booking_status` | `cora_update_booking_status` | Matches |
| `wp_ajax_cora_update_lead_email_status` | `cora_update_lead_email_status` | Matches |
| `wp_ajax_cora_save_transaction` | `cora_save_transaction` | Matches |
| `wp_ajax_cora_delete_transaction` | `cora_delete_transaction` | Matches |
| `wp_ajax_cora_send_document_email` | `cora_send_document_email` | Matches |
| `wp_ajax_cora_get_article` | `cora_get_article` | Matches |
| `wp_ajax_cora_save_article` | `cora_save_article` | Matches |
| `wp_ajax_cora_get_page` | `cora_get_page` | Matches |
| `wp_ajax_cora_save_page` | `cora_save_page` | Matches |
| `wp_ajax_cora_delete_page` | `cora_delete_page` | Matches |
| `wp_ajax_cora_analyze_seo` | `cora_analyze_seo` | Matches |
| `wp_ajax_cora_get_media` | `cora_get_media` | Matches |
| `wp_ajax_cora_create_media_folder` | `cora_create_media_folder` | Matches |
| `wp_ajax_cora_upload_media` | `cora_upload_media` | Matches |
| `wp_ajax_cora_assign_media_folder` | `cora_assign_media_folder` | Matches |
| `wp_ajax_cora_gbp_save_profile` | *None* | **Unused/Dead action hook** |
| `wp_ajax_cora_gbp_save_review_reply` | *None* | **Unused/Dead action hook** |
| `wp_ajax_cora_gbp_save_post` | *None* | **Unused/Dead action hook** |
| `wp_ajax_cora_gbp_disconnect` | `cora_gbp_disconnect` | Matches |
| `wp_ajax_cora_gbp_save_api_credentials` | `cora_gbp_save_api_credentials` | Matches |
| `wp_ajax_cora_gbp_search_places` | `cora_gbp_search_places` | Matches |
| `wp_ajax_cora_gbp_connect_place` | `cora_gbp_connect_place` | Matches |
| `wp_ajax_cora_gbp_get_oauth_url` | `cora_gbp_get_oauth_url` | Matches |
| `wp_ajax_cora_gbp_fetch_accounts` | `cora_gbp_fetch_accounts` | Matches |
| `wp_ajax_cora_gbp_fetch_locations` | `cora_gbp_fetch_locations` | Matches |
| `wp_ajax_cora_gbp_select_location` | `cora_gbp_select_location` | Matches |
| `wp_ajax_cora_gbp_fetch_reviews` | `cora_gbp_fetch_reviews` | Matches |
| `wp_ajax_cora_gbp_reply_review` | `cora_gbp_reply_review` | Matches |
| `wp_ajax_cora_gbp_create_post` | `cora_gbp_create_post` | Matches |
| `wp_ajax_cora_save_attendance` | `cora_save_attendance` | Matches |
| `wp_ajax_cora_save_client_tasks` | `cora_save_client_tasks` | Matches |
| `wp_ajax_cora_save_media_metadata` | `cora_save_media_metadata` | Matches |
| `wp_ajax_cora_save_system_settings_suite` | `cora_save_system_settings_suite` | Matches |
| `wp_ajax_cora_fetch_attendance` | `cora_fetch_attendance` | Matches |
| `wp_ajax_cora_fetch_client_tasks` | `cora_fetch_client_tasks` | Matches |
| `wp_ajax_cora_export_xml` | `cora_export_xml` | Matches |
| `wp_ajax_cora_gdpr_export` | `cora_gdpr_export` | Matches |
| `wp_ajax_cora_gdpr_erase` | `cora_gdpr_erase` | Matches |
| `wp_ajax_cora_re_save_ai_keys` | `cora_re_save_ai_keys` | Matches |
| `wp_ajax_cora_ai_chat` | `cora_ai_chat` | Matches |

### B. Missing Handlers and Discrepancies
1.  **Comments Module**:
    *   There are **no comment AJAX hooks registered** (e.g., no `wp_ajax_cora_moderate_comment`, etc.).
    *   JS function `window.coraUpdateCommentStatus` handles comment approvals purely on the front-end by using local reloads via `setTimeout` and mocked success toasts.
2.  **Appearance Module**:
    *   There are **no appearance AJAX hooks registered**.
    *   Menu settings and navigation updates are completely mock-based in JS.
3.  **Google Business Profile (GBP) Dead Code**:
    *   `cora_gbp_save_profile`, `cora_gbp_save_review_reply`, and `cora_gbp_save_post` hooks are registered in `cora-real-estate.php` but have no corresponding front-end actions in `admin-script.js`.
4.  **GDPR Form Logic Discrepancy**:
    *   `view-tools.php` calls `coraRunGDPRExport()` and `coraRunGDPRErase()`.
    *   In `admin-script.js`, there are **duplicate definitions** of these handlers with different casings:
        *   Lines 6663, 6672: `coraRunGdprExport()` and `coraRunGdprErase()` (Mixed case: Gdpr). These are dead code and do not execute AJAX requests.
        *   Lines 7020, 7039: `coraRunGDPRExport()` and `coraRunGDPRErase()` (Uppercase: GDPR). These actually perform AJAX calls.
        *   *Bug*: The uppercase versions (`coraRunGDPRExport`/`coraRunGDPRErase`) do not extract values from the text inputs `#cora-gdpr-export-email` or `#cora-gdpr-erase-email`. They post empty requests to the server, verifying only permissions/nonces.

---

## 3. Responsive CSS Layout & Viewport Evaluation (375px/430px Mobile vs. Desktop)

Below is an assessment of mobile responsiveness using utility classes (Tailwind CSS) across all 6 views:

1.  **Appearance**:
    *   *Grid*: Excellent layout shifting (`grid-cols-1 lg:grid-cols-3` with `lg:col-span-2`).
    *   *Bug*: In the navigation menu list (`cora-menu-items-list`), the URLs rendered inside `<span>` tags do not have truncation or overflow wrappers. A long permalink URL can overflow the grid item boundary on a 375px/430px mobile screen.
2.  **Comments**:
    *   *Horizontal Scroll*: The filters tab bar uses `overflow-x-auto`, allowing smooth sliding on mobile.
    *   *Feed Layout*: Uses `flex-col sm:flex-row`, stacking content nicely on 375px viewports. Highly responsive.
3.  **Media-Editor**:
    *   *Toolbar*: Uses `flex-wrap` which correctly wraps the crop aspect ratio buttons on smaller screens.
    *   *Canvas*: Image containment (`max-h-[420px] max-w-full object-contain`) fits properly within 375px/430px bounds.
4.  **Pages**:
    *   *Viewport Break / Layout Bug (Header)*: The page header (`cora-page-header`) is defined as `flex items-center justify-between` on line 33. Unlike other modules, it lacks a responsive fallback (`flex-col sm:flex-row`). On 375px/430px screens, the text and "New Page" button collide, forcing layout breaks.
    *   *Viewport Break / Layout Bug (Table)*: The static pages table (line 94) is rendered directly inside a container without an overflow wrapper. It has 6 wide columns (Title, Permalink, Parent, Template, Status, Actions) and will overflow, breaking the entire viewport layout and forcing a horizontal scroll on the base page.
5.  **Tools**:
    *   *Metrics/System Grid*: Uses `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4`, adapting layout cleanly.
    *   *Bug*: GDPR forms use `flex gap-2` on mobile (lines 183, 191). The long button text ("Anonymize & Erase" / "Export Data") squashes the email input text fields down to a few pixels on a 375px viewport.
6.  **Settings-Suite**:
    *   *Tab Navigation*: Horizontally scrollable via `overflow-x-auto` (line 39).
    *   *Form Layouts*: Uses responsive sm grids.
    *   *Bug*: The SEO permalink previews (lines 223–256) display full-width URL strings inside `<code class="font-mono text-[11px]">` blocks. If the site URL is long, these blocks do not wrap or truncate, creating a viewport break on mobile.

---

## 4. Evaluation of Native Browser Dialog Overlays

We searched the codebase for browser-native dialog modals:
*   `alert()`: **None found**.
*   `confirm()`: **None found**.
*   `prompt()`: **None found** (one comment in `admin-script.js` references overriding Quill's defaults to avoid it).

All system user alerts, successes, warnings, and error messages are properly routed to the monochromatic toast notification framework:
```javascript
window.coraShowToast("...");
```
This is fully compliant with the global dialogue guidelines.

---

## 5. Right-Sliding Drawers & Notion/Shopify Monochromatic Styling

### A. Right-Sliding Drawers
*   Drawers are structured using Tailwind's layout properties:
    *   `fixed inset-y-0 right-0 z-[99999] w-full sm:w-[480px] transition-transform duration-300 translate-x-full`
*   In `admin-script.js`, drawers (such as task drawer and pages drawer) are shown/hidden by calling:
    *   `removeClass('translate-x-full')` (Slide in)
    *   `addClass('translate-x-full')` (Slide out)
*   **Issues**:
    1.  The drawers for the **Appearance** and **Comments** modules are defined in HTML but cannot slide out because their opening/closing trigger functions are missing in JS.
    2.  For strict Tailwind reliability and styling standards, the JS should toggle both classes: `removeClass('translate-x-full').addClass('translate-x-0')` to slide in, and `addClass('translate-x-full').removeClass('translate-x-0')` to slide out. Currently, `translate-x-0` is not explicitly added.

### B. Notion/Shopify Monochromatic Theme Compliance
*   **Color Palette**: Strictly adheres to neutral gray and monochromatic tones (`zinc-900`, `zinc-500`, `zinc-200`, `bg-white`, `border-zinc-200`).
*   **Accents**: Limited to minimal indicators (`emerald-700` for active/SSL states, `amber-700` for pending/unapproved states, and `red-700` for delete/spam states).
*   **Typography**: Uses standard sans-serif system fonts.
*   **Vector Icons**: Icons are SVGs utilizing thin-lined vector paths (`stroke-width: 1.8` or `2.2`), which matches the global styling criteria. No colorful gradients or emojis are utilized.
