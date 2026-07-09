# Handoff Report - Feature Inventory & Analysis

## 1. Observation

A systematic review of the 6 view files in `/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/views/` was performed alongside analysis of `/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js` and `/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`.

### View 1: `view-pages.php` (Static Pages & Landing Page Builder)
*   **Core Purpose / Features**: Manage static pages and landing pages for the real estate agency. Provides creation, editing, viewing, and deletion operations. Supports templates (Default, Full Width, Landing Page), hierarchy (parent page assignment), menu order sorting, a rich text editor (Quill), and SEO meta descriptions.
*   **Key HTML Element IDs, Classes, and Inputs**:
    *   **Header Button**: "New Page" button with class `cora-btn-primary` and `onclick="coraOpenPageDrawer()"` (line 49).
    *   **Table Body**: `id="cora-pages-table-body"` (line 105).
    *   **Table Rows**: Clicking triggers `coraOpenPageDrawer(page_id)` (line 131).
    *   **Quick Actions**: Edit button (`onclick="coraOpenPageDrawer(id)"` - line 169), View link (line 170), and Delete button (`onclick="coraDeletePage(id)"` - line 171).
    *   **Drawer Sheet (Editor)**:
        *   Overlay: `id="cora-drawer-page-overlay"` (line 182).
        *   Container: `id="cora-drawer-page"` (line 183).
        *   Header Title: `id="cora-drawer-page-title"` (line 189).
        *   Close Button: class `cora-close-page-drawer` calling `coraClosePageDrawer()` (line 191).
        *   Page ID (hidden): `id="cora-page-id-input"` (line 197).
        *   Page Title Input: `id="cora-page-title-input"` (line 202).
        *   URL Slug Input: `id="cora-page-slug-input"` (line 211).
        *   Status Dropdown: `id="cora-page-status-input"` (line 216).
        *   Parent Page Dropdown: `id="cora-page-parent-input"` (lines 232, 241).
        *   Page Template Dropdown: `id="cora-page-template-input"` (line 249).
        *   Menu Order Input: `id="cora-page-order-input"` (line 260).
        *   Quill Editor Container: `id="cora-page-quill-editor"` (line 270).
        *   SEO Meta Description: `id="cora-page-seo-desc-input"` (line 277).
        *   Cancel Button: `onclick="coraClosePageDrawer()"` (line 284).
        *   Save Button: `onclick="coraSubmitPage()"` (line 285).
*   **Interactive Elements**:
    *   Right-sliding Page Editor Drawer (`id="cora-drawer-page"`) that translates from `translate-x-full`.
    *   Rich text toolbar integration via a Quill instance (`coraPageQuill`).
*   **AJAX Hooks / Actions Triggered**:
    *   `coraOpenPageDrawer(page_id)`: Fetches details from AJAX action `cora_get_page` (line 6869 of `admin-script.js`).
    *   `coraSubmitPage()`: Submits details to AJAX action `cora_save_page` (line 6934 of `admin-script.js`).
    *   `coraDeletePage(page_id)`: Submits request to AJAX action `cora_delete_page` (line 6966 of `admin-script.js`).
*   **Analysis Status**: **Fully Functional**. Frontend interactions correspond exactly to JS handlers, and backend handlers are registered in `cora-real-estate.php` (lines 2910, 2976, and 3001).

---

### View 2: `view-comments.php` (Client Discussions & Lead Notes)
*   **Core Purpose / Features**: Moderation interface for blog comments, client inquiries, and lead follow-up notes. Shows counts and filters by comment status (All, Pending, Approved, Spam, Trash). Provides inline quick actions for status updates and a comment reply workflow.
*   **Key HTML Element IDs, Classes, and Inputs**:
    *   **Refresh Button**: `onclick="coraRefreshComments()"` (line 41).
    *   **Filter Tabs**: Filter links with page and comment status parameters: `href="?page=cora-workspace&sub=comments&comment_status=..."` (lines 50-63).
    *   **Timeline Rows**: Each comment row has `id="cora-comment-<?php echo esc_attr( $comment_id ); ?>"` (line 90).
    *   **Quick Actions**:
        *   Approve: `onclick="coraModerateComment(id, 'approve')"` (line 125).
        *   Unapprove: `onclick="coraModerateComment(id, 'hold')"` (line 130).
        *   Reply: `onclick="coraOpenCommentReplyDrawer(id, author, excerpt)"` (line 136).
        *   Spam: `onclick="coraModerateComment(id, 'spam')"` (line 142).
        *   Trash: `onclick="coraModerateComment(id, 'trash')"` (line 149).
        *   Restore: `onclick="coraModerateComment(id, 'restore')"` (line 154).
        *   Delete Permanently: `onclick="coraDeleteCommentPermanent(id)"` (line 158).
    *   **Reply Drawer**:
        *   Container: `id="cora-drawer-comment-reply"` (line 172).
        *   Parent Comment ID (hidden): `id="cora-reply-parent-id"` (line 184).
        *   Author Preview: `id="cora-reply-author-name"` (line 189).
        *   Excerpt Preview: `id="cora-reply-content-preview"` (line 192).
        *   Reply Textarea: `id="cora-reply-textarea"` (line 198).
        *   Cancel Button: `onclick="coraCloseCommentReplyDrawer()"` (line 204).
        *   Send Button: `id="cora-btn-submit-comment-reply"` calling `onclick="coraSubmitCommentReply()"` (line 205).
*   **Interactive Elements**:
    *   Right-sliding comment reply drawer sheet (`id="cora-drawer-comment-reply"`).
    *   Hover-triggered inline action bars for quick moderation.
*   **AJAX Hooks / Actions Triggered**:
    *   Frontend HTML calls: `coraRefreshComments()`, `coraModerateComment()`, `coraOpenCommentReplyDrawer()`, `coraCloseCommentReplyDrawer()`, `coraDeleteCommentPermanent()`, `coraSubmitCommentReply()`.
*   **Analysis Status**: **CRITICAL MISMATCHES & MISSING BACKEND IMPLEMENTATIONS**.
    1.  **Missing JS Declarations**: `coraRefreshComments`, `coraModerateComment`, `coraOpenCommentReplyDrawer`, `coraCloseCommentReplyDrawer`, and `coraDeleteCommentPermanent` are completely missing from `admin-script.js`.
    2.  **Mismatched JS Declarations**: Instead of the above, `admin-script.js` defines `window.coraOpenCommentReplyModal`, `window.coraUpdateCommentStatus`, and `window.coraFilterComments` (lines 6601-6626).
    3.  **Mismatched Input IDs**: The reply drawer in JS selectors targets elements that don't exist in PHP:
        *   JS expects `#cora-reply-comment-id` but PHP defines `cora-reply-parent-id`.
        *   JS expects `#cora-reply-target-author` but PHP defines `cora-reply-author-name`.
        *   JS expects `#cora-reply-target-excerpt` but PHP defines `cora-reply-content-preview`.
        *   JS expects `#cora-reply-content` but PHP defines `cora-reply-textarea`.
        *   JS expects `#cora-modal-reply-comment` (modal styling) but PHP defines `#cora-drawer-comment-reply` (drawer styling).
    4.  **No Backend AJAX Handlers**: There are no registered action hooks in `cora-real-estate.php` for moderating or deleting comments (e.g. no `wp_ajax_cora_update_comment_status` or `wp_ajax_cora_delete_comment_permanent` is registered).

---

### View 3: `view-appearance.php` (Appearance & Navigation Builder)
*   **Core Purpose / Features**: Customizes active theme identity features (Site Tagline, Agency Logo URL, Favicon URL) and supports configuring the site's navigation menus using a drag-and-drop hierarchy builder.
*   **Key HTML Element IDs, Classes, and Inputs**:
    *   **Header Action**: "Save All Settings" button with `onclick="coraSaveAppearanceSettings()"` (line 35).
    *   **Branding Inputs**:
        *   Site Tagline: `id="cora-brand-tagline"` (line 72).
        *   Agency Logo URL: `id="cora-brand-logo-url"` (line 77) with Browse button calling `coraOpenMediaSelector('cora-brand-logo-url')` (line 78).
        *   Favicon URL: `id="cora-brand-favicon-url"` (line 84) with Browse button calling `coraOpenMediaSelector('cora-brand-favicon-url')` (line 85).
    *   **Navigation Pickers**:
        *   Menu Dropdown: `id="cora-nav-menu-select"` (line 101).
        *   Create Menu Button: `onclick="coraOpenNewMenuDrawer()"` (line 110).
    *   **Menu Items List**:
        *   Container: `id="cora-menu-items-list"` (line 115).
        *   Item Wrapper: has `data-item-id` attribute (line 126).
        *   Drag Handle: class `cursor-move` (line 128).
        *   Remove Item Button: `onclick="coraRemoveMenuItem(item_id)"` (line 138).
        *   Add Menu Link Button: `onclick="coraOpenAddMenuItemDrawer()"` (line 149).
    *   **Add Menu Item Drawer**:
        *   Container: `id="cora-drawer-menu-item"` (line 160).
        *   Close Button: `onclick="coraCloseAddMenuItemDrawer()"` (line 166).
        *   Item Type: `id="cora-menu-item-type"` with `onchange="coraToggleMenuItemTypeFields(this.value)"` (line 174).
        *   Static Page Selection Wrapper: `id="cora-field-menu-page"` (line 180) containing dropdown `id="cora-menu-page-id"` (line 182).
        *   Custom URL Selection Wrapper: `id="cora-field-menu-url"` (line 189) containing input `id="cora-menu-custom-url"` (line 191).
        *   Navigation Label Input: `id="cora-menu-item-label"` (line 196).
        *   Cancel Button: `onclick="coraCloseAddMenuItemDrawer()"` (line 202).
        *   Save Button: `onclick="coraSubmitMenuItem()"` (line 203).
*   **Interactive Elements**:
    *   Drag-and-drop sorting list on `#cora-menu-items-list`.
    *   Media selector popup wrappers for branding URL browse fields.
    *   Right-sliding drawer for adding items (`#cora-drawer-menu-item`).
*   **AJAX Hooks / Actions Triggered**:
    *   Frontend calls: `coraSaveAppearanceSettings()`, `coraOpenMediaSelector()`, `coraOpenNewMenuDrawer()`, `coraRemoveMenuItem()`, `coraOpenAddMenuItemDrawer()`, `coraCloseAddMenuItemDrawer()`, `coraToggleMenuItemTypeFields()`, `coraSubmitMenuItem()`.
*   **Analysis Status**: **CRITICAL FUNCTION MISMATCHES & UNIMPLEMENTED ENDPOINTS**.
    1.  **Missing JS Declarations**: `coraSaveAppearanceSettings`, `coraOpenMediaSelector`, `coraOpenNewMenuDrawer`, `coraRemoveMenuItem`, `coraOpenAddMenuItemDrawer`, `coraCloseAddMenuItemDrawer`, `coraToggleMenuItemTypeFields`, and `coraSubmitMenuItem` are not defined in `admin-script.js`.
    2.  **Mismatched JS**: In `admin-script.js`, there is a stub function named `window.coraSaveMenuStructure` (line 6635) that only triggers a toast, which is completely decoupled from the actual view interactions.
    3.  **No Backend AJAX Handlers**: No backend endpoints exist in `cora-real-estate.php` for menu updates or custom branding save operations (saving brand assets is not registered).

---

### View 4: `view-tools.php` (System Tools & Diagnostics)
*   **Core Purpose / Features**: Provides system checks, server configuration auditing (PHP version, MySQL version, SSL status, Cron & memory limit details), XML WXR imports/exports, and GDPR user privacy logs (exports/erasures).
*   **Key HTML Element IDs, Classes, and Inputs**:
    *   **Diagnostics Action**: "Copy Site Diagnostics" button with `onclick="coraCopySiteDiagnostics()"` (line 33).
    *   **Export WXR Section**:
        *   Selection Radios: `name="cora_export_type"` with values `all`, `posts`, `pages`, `media` (lines 112-125).
        *   Download XML Button: `onclick="coraRunXMLExport()"` (line 130).
    *   **Import WXR Section**:
        *   Import Selector: `id="cora-import-file" type="file" accept=".xml"` with `onchange="coraShowSelectedImportFile(this)"` (line 154).
        *   Browse Button: triggers file selection (line 155).
        *   Display Element: `id="cora-selected-file-display"` (line 158).
        *   Run Button: `onclick="coraRunXMLImport()"` (line 162).
    *   **GDPR Compliance Section**:
        *   Export Email Input: `id="cora-gdpr-export-email"` (line 184).
        *   Export Button: `onclick="coraRunGDPRExport()"` (line 185).
        *   Erase Email Input: `id="cora-gdpr-erase-email"` (line 192).
        *   Erase Button: `onclick="coraRunGDPRErase()"` (line 193).
*   **Interactive Elements**:
    *   Standard input buttons, radios, file pickers.
*   **AJAX Hooks / Actions Triggered**:
    *   `coraCopySiteDiagnostics()` (JS clipboard only, no AJAX).
    *   `coraRunXMLExport()` -> AJAX Action: `cora_export_xml` (line 7003 of `admin-script.js`).
    *   `coraRunXMLImport()` -> JS Toast check only (line 7016).
    *   `coraRunGDPRExport()` -> AJAX Action: `cora_gdpr_export` (line 7026).
    *   `coraRunGDPRErase()` -> AJAX Action: `cora_gdpr_erase` (line 7045).
*   **Analysis Status**: **RUN-TIME REFERENCE ERRORS**.
    *   **Case Mismatch**: The HTML template calls `coraRunGDPRExport()` and `coraRunGDPRErase()`. However, `admin-script.js` registers them as camelCase `coraRunGdprExport` and `coraRunGdprErase` (lines 6663, 6672, and 7020, 7039). This case mismatch causes a Javascript `ReferenceError: coraRunGDPRExport is not defined` when clicked.
    *   **Missing JS**: `coraShowSelectedImportFile` is not defined in `admin-script.js`, causing a crash on file selection.
    *   **Backend Support**: AJAX backend endpoints are correctly registered for the valid actions in `cora-real-estate.php` (lines 3977, 3986, 3995).

---

### View 5: `view-media-editor.php` (Media Library Advanced Editor)
*   **Core Purpose / Features**: Manipulates images directly (applying aspect ratio crops, 90-degree rotations, flipping, scaling custom pixel dimensions) and edits search engine metadata (Title, Alt Text, Caption, Internal description).
*   **Key HTML Element IDs, Classes, and Inputs**:
    *   **Media Selector**: `id="cora-editor-media-select"` with `onchange="coraLoadMediaIntoEditor(this.value)"` (line 52).
    *   **Reset Button**: `onclick="coraResetEditorCanvas()"` (line 63).
    *   **Crop Ratio Presets**:
        *   1:1 Square: `onclick="coraSetCropRatio(1, 1)"` (line 71).
        *   4:3 Standard: `onclick="coraSetCropRatio(4, 3)"` (line 72).
        *   16:9 Widescreen: `onclick="coraSetCropRatio(16, 9)"` (line 73).
        *   Free Crop: `onclick="coraSetCropRatio(null)"` (line 74).
    *   **Transform Tools**:
        *   Rotate Clockwise: `onclick="coraRotateImage(90)"` (line 79).
        *   Rotate Counter-Clockwise: `onclick="coraRotateImage(-90)"` (line 82).
        *   Flip Horizontal: `onclick="coraFlipImage('h')"` (line 85).
    *   **Canvas Containers**:
        *   Container: `id="cora-editor-canvas-container"` (line 92).
        *   Image Element: `id="cora-editor-preview-img"` (line 94).
    *   **Scale Dimensions Inputs**:
        *   Width Input: `id="cora-scale-width"` (line 107).
        *   Height Input: `id="cora-scale-height"` (line 109).
    *   **Apply Transformation Button**: `onclick="coraSaveEditedImage()"` (line 112).
    *   **SEO Meta Fields**:
        *   Attachment ID (hidden): `id="cora-meta-attachment-id"` (line 128).
        *   Title: `id="cora-meta-title"` (line 133).
        *   Alt Text: `id="cora-meta-alt"` (line 141).
        *   Caption: `id="cora-meta-caption"` (line 146).
        *   Description/Notes: `id="cora-meta-description"` (line 151).
        *   Save Metadata Button: `onclick="coraSaveMediaMetadata()"` (line 156).
*   **Interactive Elements**:
    *   Transform preview canvas using dynamic image dimensions and CSS scale/rotation effects.
*   **AJAX Hooks / Actions Triggered**:
    *   Frontend calls: `coraOpenMediaUploader()`, `coraLoadMediaIntoEditor()`, `coraResetEditorCanvas()`, `coraSetCropRatio()`, `coraRotateImage()`, `coraFlipImage()`, `coraSaveEditedImage()`, `coraSaveMediaMetadata()`.
*   **Analysis Status**: **FUNCTION MISMATCHES & UNIMPLEMENTED RASTERIZATION ON BACKEND**.
    1.  **Missing JS Declarations**: `coraOpenMediaUploader`, `coraLoadMediaIntoEditor`, `coraResetEditorCanvas`, `coraSetCropRatio`, `coraRotateImage`, and `coraFlipImage` are not declared in `admin-script.js`.
    2.  **Mismatched JS Declarations**: In `admin-script.js`, there is `window.coraApplyMediaTransform(type)` (which takes parameters like `'rotate-right'`, `'rotate-left'`, `'flip-h'`, `'flip-v'`) and `window.coraOpenMediaEditorModal(id, url, name, dims)`, which do not map to the views.
    3.  **Missing Backend Image Transformer**: While metadata saving is fully wired via AJAX action `cora_save_media_metadata` (lines 7071 in JS and 4024 in PHP), the actual rasterization and image manipulation save (`coraSaveEditedImage`) only triggers a dummy toast on the frontend (`coraSaveEditedImage()` -> `window.coraShowToast("Media updated successfully.")` in JS line 7085). There is no backend PHP handler for cropping, scaling, rotating, or flipping the actual image binary files.

---

### View 6: `view-settings-suite.php` (System Settings Complete Suite)
*   **Core Purpose / Features**: Complete multi-tab configuration suite managing site-wide settings.
    *   **Tab 1 (General)**: Site Title, Tagline, Administration Email, Default registration roles, and membership status.
    *   **Tab 2 (Reading)**: Homepage display modes (latest posts feed vs. static page), page on front, page for posts, search engine indexing (robots visibility toggles).
    *   **Tab 3 (Writing)**: Default taxonomy category, default post formats.
    *   **Tab 4 (Discussion)**: Pingbacks/trackbacks, comments status, manual approval queues, blacklist/disallowed keywords fields.
    *   **Tab 5 (Permalinks)**: URL routing layouts (Plain, Day and Name, Month and Name, Post Name).
    *   **Tab 6 (Privacy)**: Assigns official privacy policy pages.
*   **Key HTML Element IDs, Classes, and Inputs**:
    *   **Tabs Navigation**: Links leading to `?page=cora-workspace&sub=settings-suite&settings_tab=...` (lines 41-55).
    *   **Settings Form**: `id="cora-settings-suite-form"` (line 59) with `onsubmit` handler.
    *   **Form Inputs (depending on Tab)**:
        *   General Tab: `name="blogname"`, `name="blogdescription"`, `name="admin_email"`, `name="default_role"`, `name="users_can_register"` (lines 72-93).
        *   Reading Tab: `name="show_on_front"`, `name="page_on_front"`, `name="page_for_posts"`, `name="blog_public"` (lines 111-142).
        *   Writing Tab: `name="default_category"`, `name="default_post_format"` (lines 162-170).
        *   Discussion Tab: `name="default_pingback_flag"`, `name="default_comment_status"`, `name="comment_moderation"`, `name="moderation_keys"`, `name="disallowed_keys"` (lines 190-209).
        *   Permalinks Tab: `name="permalink_structure"` (plain, day-and-name, month-and-name, post-name) (lines 225-249).
        *   Privacy Tab: `name="wp_page_for_privacy_policy"` (line 269).
    *   **Header & Footer Action Buttons**: "Save All Settings" calling `coraSaveSystemSettingsSuite()` (lines 31, 282).
*   **Interactive Elements**:
    *   Tabbed workspace loading.
    *   Large forms submission handlers.
*   **AJAX Hooks / Actions Triggered**:
    *   `coraSaveSystemSettingsSuite()` -> AJAX Action: `cora_save_system_settings_suite` (JS line 7098).
*   **Analysis Status**: **Fully Functional**. Backend option updating is robustly implemented in PHP (`cora_ajax_save_system_settings_suite` - line 4026 of `cora-real-estate.php`), sanitizing and saving options automatically, including rewrites flush for permalinks.

---

## 2. Logic Chain

The step-by-step reasoning that led to the findings above is as follows:

1.  **Extracting Elements**: Each view file was examined to isolate user-triggered attributes. For instance, the button on line 125 of `view-comments.php` contains the click target:
    `onclick="coraModerateComment(<?php echo esc_js( $comment_id ); ?>, 'approve')"`
    This requires a globally defined JS function `coraModerateComment` to accept two arguments.
2.  **Evaluating JavaScript Definitions**: Looking into `assets/js/admin-script.js`, a search for `coraModerateComment` returned **0 matches**.
3.  **Identifying Alternative Mappings**: Searching `admin-script.js` for related comment hooks revealed:
    `window.coraUpdateCommentStatus = function(id, action)` (line 6618)
    This exposes a clear naming mismatch where the PHP views try to invoke `coraModerateComment` instead of `coraUpdateCommentStatus`.
4.  **Confirming Selector Gaps**: In the reply flow, `view-comments.php` defines a form input with `id="cora-reply-textarea"`. However, the javascript function `coraSubmitCommentReply` (line 6608) targets `$('#cora-reply-content').val()`. This creates a selector mismatch: the text inside the HTML input will never be read, and the submission will fail content validation.
5.  **Validating Case Sensitivity**: In `view-tools.php`, the GDPR export button calls `coraRunGDPRExport()`. In `admin-script.js`, the function is defined as `coraRunGdprExport`. In JavaScript, function names are case-sensitive; calling `coraRunGDPRExport()` will throw a `ReferenceError` immediately at runtime.
6.  **Evaluating Backend Registrations**: In `cora-real-estate.php`, all AJAX actions are registered using `add_action( 'wp_ajax_cora_<action_name>', '<handler_name>' )`. Searching for handlers matching comments, menu builder interactions, or image transforms yielded **0 results**. Therefore, even if the frontend javascript functions were correctly mapped, the AJAX queries targeting those actions would return WP's default admin-ajax `400 Bad Request` or `0` error responses.

---

## 3. Caveats

*   **Tailwind/CSS Dependency**: The visual layouts use extensive tailwind styling (`flex items-center gap-3`, etc.). It is assumed that Tailwind CSS is correctly loaded in the WordPress admin panel.
*   **Third-party Integrations**: The Quill Rich Text editor (`Quill`) is instantiated in `admin-script.js` but depends on an external library script being enqueued in the WP admin context. If Quill is not loaded, pages builder drawer initialization will crash.
*   **Permissions and Nonces**: The AJAX calls rely on security nonces (`coraREData.ajaxNonce` or `coraREData.nonce`). Any testing must ensure nonces are correctly localized in the window scope.

---

## 4. Conclusion

The system view inventory reveals that two files (`view-pages.php` and `view-settings-suite.php`) are fully wired and functional. However, the other four views suffer from critical integration issues:
*   `view-comments.php`: Fully broken due to missing/mismatched JS function declarations, ID mismatches, and no backend PHP handlers.
*   `view-appearance.php`: Menu builder is entirely unimplemented on both the JS and backend PHP sides.
*   `view-tools.php`: Run-time ReferenceError occurs due to case-mismatch in GDPR methods (`GDPR` vs `Gdpr`) and missing files loader (`coraShowSelectedImportFile`).
*   `view-media-editor.php`: Actual image transformation is mocked on the JS side and lacks backend handlers.

### Actionable Remediation Diff Map (JS Recommendations)
To resolve the JavaScript errors, the following modifications are proposed for `assets/js/admin-script.js`:

```javascript
// Fix GDPR casing issue:
window.coraRunGDPRExport = window.coraRunGdprExport;
window.coraRunGDPRErase = window.coraRunGdprErase;

// Map Comment Moderation buttons to active status updater:
window.coraModerateComment = window.coraUpdateCommentStatus;

// Fix Comment Reply modal/drawer compatibility and ID mapping:
window.coraOpenCommentReplyDrawer = function(id, author, excerpt) {
    $('#cora-reply-parent-id').val(id);
    $('#cora-reply-author-name').text(author);
    $('#cora-reply-content-preview').text(excerpt);
    $('#cora-reply-textarea').val('');
    $('#cora-drawer-comment-reply').removeClass('translate-x-full');
};
window.coraCloseCommentReplyDrawer = function() {
    $('#cora-drawer-comment-reply').addClass('translate-x-full');
};
// Update Submit Reply helper to read correct fields:
window.coraSubmitCommentReply = function() {
    const parentId = $('#cora-reply-parent-id').val();
    const content = $('#cora-reply-textarea').val().trim();
    if (!content) {
        window.coraShowToast("Reply content cannot be empty.");
        return;
    }
    // AJAX to backend would go here once registered...
};
```

---

## 5. Verification Method

### Manual Console Verification (Reproduction of ReferenceErrors)
To confirm the ReferenceErrors:
1.  Navigate to the **System Tools & Diagnostics** view.
2.  Open the browser inspection console.
3.  Click the **Export Data** or **Anonymize & Erase** buttons in the GDPR box.
4.  Observe the uncaught exception: `ReferenceError: coraRunGDPRExport is not defined`.

### Inspecting Selector Mismatches
1.  Open `/wp-content/plugins/cora-real-estate/views/view-comments.php` and verify:
    *   Textarea ID is defined as `cora-reply-textarea` (line 198).
2.  Open `/wp-content/plugins/cora-real-estate/assets/js/admin-script.js` and verify:
    *   Line 6609 reads values from `#cora-reply-content`, which does not exist in the view.

### Invalidation Conditions
This analysis is invalidated if comment moderation or menu management handlers are enqueued dynamically from a third-party framework or standard WordPress admin scripts that bypasses this plugin's assets.
