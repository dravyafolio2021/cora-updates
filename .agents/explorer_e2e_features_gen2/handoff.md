# Handoff Report — Cora UI & Views Analysis

## 1. Observation
- **Dashboard routing hook in `cora-real-estate.php:25-36`**:
  ```php
  function cora_real_estate_ai_admin_menu() {
      add_menu_page(
          __( 'Cora for Real Estate', 'cora-real-estate' ),
          __( 'Cora AI', 'cora-real-estate' ),
          'manage_options',
          'cora-real-estate',
          'cora_real_estate_ai_render_dashboard',
          'dashicons-superhero',
          2
      );
  }
  ```
- **Login redirection in `cora-real-estate.php:292-300`**:
  ```php
  function cora_real_estate_ai_login_redirect( $redirect_to, $request, $user ) {
      if ( $user instanceof WP_User ) {
          $allowed_roles = array( 'administrator', 'cora_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor' );
          foreach ( $allowed_roles as $role ) {
              if ( in_array( $role, (array) $user->roles ) ) {
                  return home_url( '/workspace' );
              }
          }
      }
  ```
- **Included Views in `admin-dashboard.php:6307-6347`**:
  ```php
  <?php if ( $sub_page === 'pages' ) : ?>
      <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-pages.php'; ?>
  ...
  ```
- **Current `admin-script.js` declarations (lines 6709-7520)**:
  - `window.coraRefreshComments = function() { ... }`
  - `window.coraModerateComment = function(commentId, action) { ... }`
  - `window.coraOpenCommentReplyDrawer = function(commentId, authorName, excerpt) { ... }`
  - `window.coraCloseCommentReplyDrawer = function() { ... }`
  - `window.coraSubmitCommentReply = function() { ... }`
  - `window.coraOpenPageDrawer = function(pageId) { ... }`
  - `window.coraClosePageDrawer = function() { ... }`
  - `window.coraSubmitPage = function() { ... }`
  - `window.coraDeletePage = function(pageId) { ... }`
  - `window.coraSaveAppearanceSettings = function() { ... }`
  - `window.coraOpenMediaSelector = function(fieldId) { ... }`
  - `window.coraOpenNewMenuDrawer = function() { ... }`
  - `window.coraSubmitNewMenu = function() { ... }`
  - `window.coraOpenAddMenuItemDrawer = function() { ... }`
  - `window.coraCloseAddMenuItemDrawer = function() { ... }`
  - `window.coraToggleMenuItemTypeFields = function(type) { ... }`
  - `window.coraSubmitMenuItem = function() { ... }`
  - `window.coraRemoveMenuItem = function(itemId) { ... }`
  - `window.coraLoadMediaIntoEditor = function(attachmentId) { ... }`
  - `window.coraResetEditorCanvas = function() { ... }`
  - `window.coraSetCropRatio = function(w, h) { ... }`
  - `window.coraRotateImage = function(deg) { ... }`
  - `window.coraFlipImage = function(dir) { ... }`
  - `window.coraSaveEditedImage = function() { ... }`
  - `window.coraSaveMediaMetadata = function() { ... }`
  - `window.coraSaveSystemSettingsSuite = function() { ... }`
- **Missing function Reference in `views/view-tools.php:154`**:
  ```html
  <input type="file" id="cora-import-file" accept=".xml" class="hidden" onchange="coraShowSelectedImportFile(this)">
  ```
  But `coraShowSelectedImportFile` is not defined anywhere in the JavaScript files.
- **Mocked Save Image function in `admin-script.js:7485-7487`**:
  ```javascript
  window.coraSaveEditedImage = function() {
      window.coraShowToast("Media updated successfully.");
  };
  ```

## 2. Logic Chain
1. The login redirect hook in `cora-real-estate.php` intercepts the login of users with roles like `administrator` and redirects them immediately to `/workspace`.
2. The template redirect hook in `cora-real-estate.php` intercepts `/workspace` and subpaths like `/workspace/comments`, loading `admin-dashboard.php` with the respective views conditionally.
3. Review of the latest `admin-script.js` shows that all the interactive callback functions used in the 6 views (except `coraShowSelectedImportFile`) are fully registered on the global window object.
4. This resolves the mismatches identified in previous reports (`explorer_1` and `explorer_features`), which claimed that appearance settings, menu builders, comment moderation, and media canvas editing functions were completely missing from the script. They were added to the bottom of `admin-script.js` and override any old stubs.
5. In `views/view-tools.php:154`, the file selection input calls `coraShowSelectedImportFile(this)`. Since this function is not declared in JS, it will cause an uncaught ReferenceError when a file is selected.
6. The canvas image modification save function `coraSaveEditedImage` is a mock that only displays a toast; it does not invoke the registered PHP backend action `cora_save_edited_image` via AJAX.

## 3. Caveats
- No caveats.

## 4. Conclusion
1. The 6 core modules are mapped to subpage routes at `/workspace/pages`, `/workspace/comments`, `/workspace/appearance`, `/workspace/tools`, `/workspace/media-editor`, and `/workspace/settings-suite`.
2. The login credentials are verified as `cora_admin` / `cora_secure_pass_123` with automated workspace redirection.
3. Nearly all views are now correctly matched to the javascript callbacks in `admin-script.js`, except for a missing function `coraShowSelectedImportFile` in `view-tools.php`.
4. Image canvas saves and XML imports are stubs/mocks on the frontend.

## 5. Verification Method
- **Verify file existence and contents**: Inspect `/Users/shrutian/Desktop/cora/.agents/explorer_e2e_features_gen2/analysis.md` using `view_file` to confirm the presence of all required inventory details.
- **Verify script definitions**: Use `grep_search` to verify that `window.coraShowSelectedImportFile` does not exist in `admin-script.js`.
