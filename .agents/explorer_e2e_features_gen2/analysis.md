# Cora Real Estate Platform UI & Views Analysis Report

## Executive Summary
This report provides a comprehensive analysis of the Cora Real Estate Platform plugin's UI views, interactive elements, AJAX endpoints, and JavaScript/CSS assets for the 6 core replacement modules. It reconciles previous explorer findings with the current state of the codebase, cataloging verified functionality, resolved mismatches, and remaining development gaps.

---

## 1. Credentials & Admin Dashboard Access Flow

### A. Credentials
*   **Username**: `cora_admin`
*   **Password**: `cora_secure_pass_123`

### B. Access and Navigation Flow
1.  **Login**: Access the standard WordPress login page at `/wp-login.php` and submit the credentials.
2.  **Redirect Hook**: The plugin registers a login redirect hook (`cora_real_estate_ai_login_redirect` in `cora-real-estate.php:292`). Upon successful login, any user with authorized roles (including `administrator`) is automatically redirected to `/workspace` (which defaults to the primary dashboard view `/workspace/dashboard`).
3.  **WP-Admin Menu Item**: In the WordPress Admin Area sidebar, a high-priority top-level menu page labeled **"Cora AI"** (using slug `cora-real-estate` and dashicon `dashicons-superhero`) is added. Clicking this menu item executes `cora_real_estate_ai_render_dashboard()`, which redirects the browser to `/workspace`.
4.  **Route Interception**: The plugin intercepts requests to `/workspace` using the `template_redirect` hook, performs user authentication and role-based feature checks (administrators are granted full privileges for all 22 workspace features), and includes the custom standalone Notion/Shopify-style template `admin-dashboard.php`.

---

## 2. Platform Architecture & Subpage Routing
The workspace views are rendered conditionally inside `admin-dashboard.php` based on the URL path slug. The 6 core modules correspond to the following subpage URL paths:

| Subpage / View | Subpage URL Path | Underlying View File Path |
| :--- | :--- | :--- |
| **Pages** | `/workspace/pages` | `views/view-pages.php` |
| **Comments** | `/workspace/comments` | `views/view-comments.php` |
| **Appearance** | `/workspace/appearance` | `views/view-appearance.php` |
| **Tools** | `/workspace/tools` | `views/view-tools.php` |
| **Media-Editor** | `/workspace/media-editor` | `views/view-media-editor.php` |
| **Settings-Suite**| `/workspace/settings-suite`| `views/view-settings-suite.php` |

---

## 3. Inventory of Core Modules

### 1. Pages (`views/view-pages.php`)
*   **Role**: Static page management and landing page editor.
*   **UI Controls & Form Fields**:
    *   **New Page Button**: `cora-btn-primary` class, triggers `onclick="coraOpenPageDrawer()"`
    *   **Table Body**: Selector `#cora-pages-table-body`
    *   **Quick Actions (Row click or buttons)**: Edit button (`onclick="coraOpenPageDrawer(page_id)"`), View link (`href` is post permalink), and Delete button (`onclick="coraDeletePage(page_id)"`).
    *   **Page Editor Drawer**: Container ID `#cora-drawer-page`, Overlay ID `#cora-drawer-page-overlay`
    *   **Drawer Form Fields**:
        *   Page ID (hidden): `id="cora-page-id-input"`
        *   Page Title Input: `id="cora-page-title-input"`
        *   URL Slug Input: `id="cora-page-slug-input"`
        *   Status Dropdown: `id="cora-page-status-input"` (Options: `publish`, `draft`, `private`)
        *   Parent Page Dropdown: `id="cora-page-parent-input"` (nested inside `#cora-page-parent-wrapper`)
        *   Page Template Dropdown: `id="cora-page-template-input"` (Options: `default`, `full-width`, `landing-page`)
        *   Menu Order Input: `id="cora-page-order-input"`
        *   Quill Rich Editor Container: `id="cora-page-quill-editor"` (instantiated in JS via Quill)
        *   SEO Meta Description: `id="cora-page-seo-desc-input"`
        *   Save Page Button: triggers `onclick="coraSubmitPage()"`
        *   Cancel Button: triggers `onclick="coraClosePageDrawer()"`
*   **AJAX Calls**:
    *   `cora_get_page`: Fetches page content and metadata.
    *   `cora_save_page`: Saves/updates page options.
    *   `cora_delete_page`: Deletes page.

### 2. Comments (`views/view-comments.php`)
*   **Role**: Client discussions, blog comments, and lead inquiry moderation interface.
*   **UI Controls & Form Fields**:
    *   **Refresh Feed Button**: `cora-btn-secondary` class, triggers `onclick="coraRefreshComments()"`
    *   **Status Filter Tabs**: Links filtering by state (`all`, `hold`, `approve`, `spam`, `trash`) using parameters `?page=cora-workspace&sub=comments&comment_status=...`
    *   **Timeline Comments Feed**: Comment elements have ID `#cora-comment-[comment_id]`
    *   **Inline Moderation Actions**:
        *   Approve: `onclick="coraModerateComment(comment_id, 'approve')"`
        *   Unapprove: `onclick="coraModerateComment(comment_id, 'hold')"`
        *   Reply: `onclick="coraOpenCommentReplyDrawer(comment_id, author_name, excerpt)"`
        *   Spam: `onclick="coraModerateComment(comment_id, 'spam')"`
        *   Trash: `onclick="coraModerateComment(comment_id, 'trash')"`
        *   Restore: `onclick="coraModerateComment(comment_id, 'restore')"`
        *   Delete Permanently: `onclick="coraDeleteCommentPermanent(comment_id)"`
    *   **Reply Drawer**: Container ID `#cora-drawer-comment-reply`
    *   **Drawer Form Fields**:
        *   Parent ID (hidden): `id="cora-reply-parent-id"`
        *   Author Display Element: `id="cora-reply-author-name"`
        *   Excerpt Display Element: `id="cora-reply-content-preview"`
        *   Reply Textarea: `id="cora-reply-textarea"`
        *   Send Reply Button: `id="cora-btn-submit-comment-reply"` calling `onclick="coraSubmitCommentReply()"`
        *   Cancel Button: triggers `onclick="coraCloseCommentReplyDrawer()"`
*   **AJAX Calls**:
    *   `cora_moderate_comment`: Approves, holds, spams, trashes, or restores a comment status.
    *   `cora_delete_comment_permanent`: Deletes comment from database.
    *   `cora_submit_comment_reply`: Posts new reply comment.

### 3. Appearance (`views/view-appearance.php`)
*   **Role**: Theme identity management and site menu builder.
*   **UI Controls & Form Fields**:
    *   **Save All Settings Button**: triggers `onclick="coraSaveAppearanceSettings()"`
    *   **Branding Assets Fields**:
        *   Site Tagline Input: `id="cora-brand-tagline"`
        *   Agency Logo URL Input: `id="cora-brand-logo-url"` with Browse button calling `coraOpenMediaSelector('cora-brand-logo-url')`
        *   Favicon URL Input: `id="cora-brand-favicon-url"` with Browse button calling `coraOpenMediaSelector('cora-brand-favicon-url')`
    *   **Menu Builder Section**:
        *   Select Menu Dropdown: `id="cora-nav-menu-select"` (triggers `onchange` reload with `menu_id` query param)
        *   Create Menu Button: triggers `onclick="coraOpenNewMenuDrawer()"` (draws `#cora-drawer-new-menu` dynamically in JS containing `#cora-new-menu-name` and "Create Menu" button calling `coraSubmitNewMenu()`)
        *   Menu Items Drag-and-Drop List: ID `#cora-menu-items-list` (items have `data-item-id` attribute)
        *   Remove Item Button: `onclick="coraRemoveMenuItem(item_id)"`
        *   Add Menu Link Button: triggers `onclick="coraOpenAddMenuItemDrawer()"`
    *   **Add Menu Item Drawer**: Container ID `#cora-drawer-menu-item`
    *   **Drawer Form Fields**:
        *   Item Type Dropdown: `id="cora-menu-item-type"` with `onchange="coraToggleMenuItemTypeFields(this.value)"` (Options: `page`, `custom`)
        *   Static Page Select Container: `id="cora-field-menu-page"` containing dropdown `id="cora-menu-page-id"`
        *   Custom URL Container: `id="cora-field-menu-url"` (hidden by default) containing input `id="cora-menu-custom-url"`
        *   Navigation Label Input: `id="cora-menu-item-label"`
        *   Add to Menu Button: triggers `onclick="coraSubmitMenuItem()"`
        *   Cancel Button: triggers `onclick="coraCloseAddMenuItemDrawer()"`
*   **AJAX Calls**:
    *   `cora_save_appearance_settings`: Saves brand identity options.
    *   `cora_create_nav_menu`: Registers a new navigation menu.
    *   `cora_add_menu_item`: Adds a link (page or external URL) to a menu.
    *   `cora_delete_menu_item`: Removes item from menu.

### 4. Tools (`views/view-tools.php`)
*   **Role**: Diagnostics, XML WXR backup migration, and GDPR compliance.
*   **UI Controls & Form Fields**:
    *   **Copy Diagnostics Button**: triggers `onclick="coraCopySiteDiagnostics()"`
    *   **Export Content Selector**: Radio inputs `name="cora_export_type"` (Values: `all`, `posts`, `pages`, `media`) and download button triggering `onclick="coraRunXMLExport()"`
    *   **Import Content Selector**: Hidden file input `id="cora-import-file" type="file" accept=".xml"` triggered by Browse button, selected file displays in `#cora-selected-file-display` (supposedly using onchange handler), and Run button triggering `onclick="coraRunXMLImport()"`
    *   **GDPR Export Input**: Email input `id="cora-gdpr-export-email"` and button triggering `onclick="coraRunGDPRExport()"`
    *   **GDPR Erase Input**: Email input `id="cora-gdpr-erase-email"` and button triggering `onclick="coraRunGDPRErase()"`
*   **AJAX Calls**:
    *   `cora_export_xml`: Generates export file.
    *   `cora_gdpr_export`: Packages personal data associated with the email.
    *   `cora_gdpr_erase`: Trashes/anonymizes client personal details.

### 5. Media-Editor (`views/view-media-editor.php`)
*   **Role**: Advanced image editing suite and SEO metadata optimization.
*   **UI Controls & Form Fields**:
    *   **Upload Button**: triggers `onclick="coraOpenMediaUploader()"` (uses `wp.media` frame selector)
    *   **Select Image Dropdown**: `id="cora-editor-media-select"` with `onchange="coraLoadMediaIntoEditor(this.value)"`
    *   **Reset Canvas Button**: triggers `onclick="coraResetEditorCanvas()"`
    *   **Crop Presets**: Buttons triggering `coraSetCropRatio(w, h)` (Values: `1, 1`, `4, 3`, `16, 9`, `null` for Free Crop)
    *   **Transform Toolbar**:
        *   Rotate Clockwise: triggers `onclick="coraRotateImage(90)"`
        *   Rotate Counter-Clockwise: triggers `onclick="coraRotateImage(-90)"`
        *   Flip Horizontal: triggers `onclick="coraFlipImage('h')"`
    *   **Canvas Elements**: Container ID `#cora-editor-canvas-container` and Image ID `#cora-editor-preview-img`
    *   **Scale Inputs**: Width ID `#cora-scale-width` and Height ID `#cora-scale-height`
    *   **Apply Transformation Button**: triggers `onclick="coraSaveEditedImage()"`
    *   **SEO Metadata Fields**:
        *   Attachment ID (hidden): `id="cora-meta-attachment-id"`
        *   Title: `id="cora-meta-title"`
        *   Alternative Text: `id="cora-meta-alt"`
        *   Caption: `id="cora-meta-caption"`
        *   Description Textarea: `id="cora-meta-description"`
        *   Update SEO Metadata Button: triggers `onclick="coraSaveMediaMetadata()"`
*   **AJAX Calls**:
    *   `cora_get_attachment_metadata`: Loads metadata into the SEO form when a media dropdown item is selected.
    *   `cora_save_media_metadata`: Saves updated title, alt, caption, and description.
    *   `cora_save_edited_image` (Registered in PHP, but *not* invoked by JS).

### 6. Settings-Suite (`views/view-settings-suite.php`)
*   **Role**: Global WordPress & Platform settings suite.
*   **UI Controls & Form Fields**:
    *   **Save System Settings Button (Top/Bottom)**: triggers `coraSaveSystemSettingsSuite()` (via form submit or click)
    *   **Tab Navigation Links**: links matching `?page=cora-workspace&sub=settings-suite&settings_tab=...` (General, Reading, Writing, Discussion, Permalinks, Privacy)
    *   **Form**: ID `#cora-settings-suite-form`
    *   **Tab Inputs**:
        *   *General Tab*: Site Title (`name="blogname"`), Tagline (`name="blogdescription"`), Admin Email (`name="admin_email"`), Default Role (`name="default_role"`), Membership Checkbox (`name="users_can_register"`)
        *   *Reading Tab*: Homepage Mode (`name="show_on_front"`), Homepage Page Select (`name="page_on_front"`), Posts Page Select (`name="page_for_posts"`), Crawlers Checkbox (`name="blog_public"`)
        *   *Writing Tab*: Category Select (`name="default_category"`), Post Format Select (`name="default_post_format"`)
        *   *Discussion Tab*: Pingbacks Checkbox (`name="default_pingback_flag"`), Comments Checkbox (`name="default_comment_status"`), Approval Checkbox (`name="comment_moderation"`), Moderation Queue Words (`name="moderation_keys"`), Disallowed Keys (`name="disallowed_keys"`)
        *   *Permalinks Tab*: Permalink Structure radio inputs (`name="permalink_structure"`)
        *   *Privacy Tab*: Privacy Page Select (`name="wp_page_for_privacy_policy"`)
*   **AJAX Calls**:
    *   `cora_save_system_settings_suite`: Serializes settings form and updates WP options in database, flushing rewrites if permalinks are changed.

---

## 4. Synthesis of Findings & Environment Audit

### A. Consensus
*   **Visual Aesthetics**: Follows a Notion/Shopify monochromatic styling. Light and dark classes are available.
*   **No Browser Defaults**: The system does not use standard alert, confirm, or prompt overlays. Instead, notifications are dispatched to `window.coraShowToast()`, and confirmations are handled via a custom overlay modal (`#cora-confirm-modal`).
*   **Right-Sliding Drawers**: Add and Edit forms use right-sliding drawers that slide out via css transitions (toggling the `.translate-x-full` class) instead of overlay popup modals.
*   **Router Integrity**: The backend routes `/workspace` and its subpaths are mapped properly, enforcing login and roles-based capability parameters.

### B. Resolved Conflicts & Progress
*   *Conflict*: Previous analysis reports claimed extensive Javascript function mismatches, stating that interactive actions for Comments, Appearance, GDPR, and Media Editor were completely missing from `admin-script.js`.
*   *Verification*: Direct inspection of `assets/js/admin-script.js` confirms that **these functions have since been added** at the bottom of the file (lines 6709 to 7520). Because they are registered globally on the window object after the older stubs, JavaScript overrides the stubs. Thus, functions like `coraModerateComment`, `coraOpenCommentReplyDrawer`, `coraSaveAppearanceSettings`, `coraOpenNewMenuDrawer`, and others are now **fully matched and declared** on the frontend.

### C. Remaining Development Gaps & Bugs
Despite the functions being declared, the following issues remain in the current code:
1.  **Missing JS function**: `coraShowSelectedImportFile` is referenced in the tools view onchange hook:
    `onchange="coraShowSelectedImportFile(this)"`
    However, this function is **not declared anywhere** in `admin-script.js` or `admin-dashboard.php`. Selecting an import XML file will trigger a `ReferenceError`.
2.  **Mocked Transformations (No AJAX invocation)**: In `view-media-editor.php`, clicking "Apply & Save Image Transformation" calls `coraSaveEditedImage()`. In `admin-script.js:7485`, this function is defined as:
    ```javascript
    window.coraSaveEditedImage = function() {
        window.coraShowToast("Media updated successfully.");
    };
    ```
    It only displays a toast and **does not send the canvas rotation, flip, crop, or scaling parameters to the server**. Although a backend handler `cora_ajax_save_edited_image` is fully implemented in PHP (handling rotation, flip, crop, and resize operations), the JS script never fires it.
3.  **Mocked XML Import**: Symmetrically, `coraRunXMLImport()` only triggers a toast stating it is ready, but does not execute any AJAX upload/import actions.
