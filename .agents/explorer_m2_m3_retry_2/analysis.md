# Technical Analysis Report: Milestone M2 & M3

## 1. Executive Summary
This report analyzes client-side and server-side facades, security capability bypasses, GDPR validation vulnerabilities, and layout violations in the Cora Real Estate Platform v0.1 plugin. A concrete step-by-step remediation strategy is presented to transition the codebase from mockup stubs to secure, fully functional AJAX-driven workflows.

---

## 2. Client-Side Facades Analysis
We identified multiple instances of mockup code on the client-side (`assets/js/admin-script.js`) that simulate server communication using `setTimeout` delays and hardcoded success toasts, even when AJAX requests fail.

### A. Saving Image Transformations (`coraSaveEditedImage`)
* **File & Lines**: `assets/js/admin-script.js:7485-7487`
* **Current Code**:
  ```javascript
  window.coraSaveEditedImage = function() {
      window.coraShowToast("Media updated successfully.");
  };
  ```
* **Issues**:
  - The function is completely stubbed out. It does not perform any AJAX request to the server.
  - The media editor UI (`views/view-media-editor.php`) allows rotation, flipping, aspect ratio selection, and dimension scaling, but none of these inputs are transmitted to the server when clicking "Apply & Save Image Transformation".

### B. Mockup Success Toasts in AJAX Failure Handlers (`.fail()`)
Several event handlers capture failures in AJAX execution but display successful feedback to the user:
1. **Save Media Metadata (`coraSaveMediaMetadata`)**
   - *File & Lines*: `assets/js/admin-script.js:7458-7483`
   - *Issue*: Displays a success toast (`Media updated successfully.`) in both the successful callback and the `.fail()` callback, completely masking network or server errors.
2. **Save Settings Suite (`coraSaveSystemSettingsSuite`)**
   - *File & Lines*: `assets/js/admin-script.js:7489-7519`
   - *Issue*: Displays a success toast (`Global system settings updated successfully.`) in both success and `.fail()` callbacks.
3. **GDPR Personal Data Export (`coraRunGDPRExport`)**
   - *File & Lines*: `assets/js/admin-script.js:7406-7429`
   - *Issue*: Displays success toasts even on server-side validation error or AJAX request failure.
4. **GDPR Personal Data Erasure (`coraRunGDPRErase`)**
   - *File & Lines*: `assets/js/admin-script.js:7431-7456`
   - *Issue*: Similar to GDPR export, the `.fail()` callback hides any errors and shows `GDPR personal data erasure request processed...`.

### C. Tools & Diagnostics Mockups (`setTimeout` Facades)
* **File & Lines**: `assets/js/admin-script.js:6651-6662`
* **Current Code**:
  ```javascript
  window.coraRunDiagnostics = function() {
      window.coraShowToast("Executing server health check and Redis memory inspection...");
      setTimeout(() => window.coraShowToast("Diagnostics completed: All systems nominal (100% Health Score)."), 1500);
  };
  window.coraTriggerExport = function() {
      window.coraShowToast("Generating WXR export archive (Posts, Pages, Media)...");
      setTimeout(() => window.coraShowToast("Export archive generated and download started!"), 1500);
  };
  window.coraTriggerImport = function() {
      window.coraShowToast("Scanning import manifest and verifying asset integrity...");
      setTimeout(() => window.coraShowToast("Import simulation complete: 0 errors detected."), 1500);
  };
  ```
* **Issues**:
  - Entirely mock implementations using `setTimeout`.
  - The server-side already registers a real XML export function `cora_ajax_export_xml` (`cora-real-estate.php:3977`), but the client bypasses it completely.

---

## 3. Server-Side Facades & Capability Bypasses Analysis

### A. GDPR Input Validation Failures
* **File & Lines**: `cora-real-estate.php:3979-3997`
* **Current Code**:
  ```php
  function cora_ajax_gdpr_export() {
      check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
      if ( ! current_user_can( 'manage_privacy_options' ) && ! current_user_can( 'manage_options' ) ) {
          wp_send_json_error( array( 'message' => 'Unauthorized' ) );
      }
      $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
      wp_send_json_success( array( 'message' => 'GDPR personal data export request generated for ' . $email . '.' ) );
  }
  ```
* **Issues**:
  - The endpoints do not validate the presence or structural format of the `email` parameter.
  - If `email` is absent or completely invalid, `sanitize_email()` sanitizes it to an empty string (`""`), and the handler issues a successful JSON output indicating action completed for `.` (which resolves to `GDPR personal data export request generated for .`).

### B. Security Capability Check Bypasses
Several critical endpoints register handlers and verify AJAX nonces but completely bypass permission checks, allowing unauthorized logged-in users to perform admin-level activities:

1. **`cora_ajax_save_article` (lines 2808-2876)**
   - *Impact*: Any logged-in user can update or create WordPress posts.
   - *Fix*: Check for `edit_posts` or `manage_options`.
2. **`cora_ajax_get_article` (lines 2776-2803)**
   - *Impact*: Any logged-in user can read post metadata/content.
   - *Fix*: Check for `edit_posts` or `manage_options`.
3. **`cora_ajax_get_page` (lines 2881-2910)**
   - *Impact*: Any logged-in user can fetch page metadata.
   - *Fix*: Check for `edit_pages` or `manage_options`.
4. **`cora_ajax_delete_page` (lines 2981-3001)**
   - *Impact*: Any logged-in user can delete static pages.
   - *Fix*: Check for `delete_pages` or `manage_options`.
5. **`cora_ajax_analyze_seo` (lines 3006-3031)**
   - *Impact*: Unauthorized endpoint access.
   - *Fix*: Check for `edit_posts` or `manage_options`.
6. **`cora_ajax_get_media` (lines 3036-3092)**
   - *Impact*: Unauthorized file directory listing.
   - *Fix*: Check for `upload_files` or `manage_options`.
7. **`cora_ajax_create_media_folder` (lines 3097-3114)**
   - *Impact*: Unauthorized folder taxonomy insertion.
   - *Fix*: Check for `upload_files` or `manage_options`.
8. **`cora_ajax_upload_media` (lines 3119-3166)**
   - *Impact*: Unauthorized media file upload/attachment creation.
   - *Fix*: Check for `upload_files` or `manage_options`.
9. **`cora_ajax_assign_media_folder` (lines 3171-3185)**
   - *Impact*: Unauthorized category tagging on files.
   - *Fix*: Check for `upload_files` or `manage_options`.

---

## 4. User Popover Violations Analysis
* **File & Lines**: `admin-dashboard.php:2614-2687`
* **Current Layout**:
  - The sticky administrator popover card `#cora-profile-popover` sits at the bottom of the sidebar.
  - It contains UID, active role label, AI Model selection selector, quick actions, settings links, theme toggle, and Sign Out.
* **Violations**:
  - The **workspace status connection indicator** (green status dot) is located in the outer sidebar footer (`.cora-sidebar-footer`), violating the global requirement to have it inside the popover menu.
  - The **quota metrics** (such as AI usage quotas or storage usage limits) are completely absent.

---

## 5. Proposed Remediation Strategy

### Step 1: Connect Frontend Image Editor to WordPress Image Editor API
Update `window.coraSaveEditedImage` in `admin-script.js` to collect image transform parameters and perform an AJAX call to the existing backend `cora_save_edited_image` endpoint.

* **Centering Crop Logic**: Because the UI only provides aspect ratio presets (e.g. 16:9, 4:3) and no draggable crop bounding box, the client must calculate a centered crop rectangle relative to the image's original dimensions:
  ```javascript
  const imgEl = $('#cora-editor-preview-img')[0];
  const naturalWidth = imgEl.naturalWidth;
  const naturalHeight = imgEl.naturalHeight;
  const cropW = img.data('crop-w');
  const cropH = img.data('crop-h');
  
  if (cropW && cropH) {
      const imageRatio = naturalWidth / naturalHeight;
      const targetRatio = cropW / cropH;
      let finalCropW = naturalWidth;
      let finalCropH = naturalHeight;
      
      if (imageRatio > targetRatio) {
          finalCropW = Math.round(naturalHeight * targetRatio);
      } else {
          finalCropH = Math.round(naturalWidth / targetRatio);
      }
      const cropX = Math.round((naturalWidth - finalCropW) / 2);
      const cropY = Math.round((naturalHeight - finalCropH) / 2);
      
      data.crop_x = cropX;
      data.crop_y = cropY;
      data.crop_w = finalCropW;
      data.crop_h = finalCropH;
  }
  ```

### Step 2: Implement Clean AJAX Error Handling on the Client
Update all key JS functions in `admin-script.js` to:
1. Verify `res.success` in the success callback. If `false`, display the custom error message from `res.data.message` via `window.coraShowToast`.
2. Cleanly capture connection failures in `.fail()` and display a generic connection error toast rather than triggering a mock success message.

### Step 3: Implement Email Parameter Validation in GDPR Endpoints
Inject verification conditions inside `cora_ajax_gdpr_export()` and `cora_ajax_gdpr_erase()` in `cora-real-estate.php`:
```php
$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
if ( empty( $email ) || ! is_email( $email ) ) {
    wp_send_json_error( array( 'message' => 'Invalid or missing email address.' ) );
}
```

### Step 4: Inject Proper Capability Checks in Backend AJAX Endpoints
For all identified endpoints lacking authorization checks, insert:
```php
if ( ! current_user_can( 'CAPABILITY_NAME' ) && ! current_user_can( 'manage_options' ) ) {
    wp_send_json_error( array( 'message' => 'Unauthorized' ) );
}
```
*Cap mappings:*
- Articles & SEO: `edit_posts`
- Pages: `edit_pages` (and `delete_pages` for deletion)
- Media & Folders: `upload_files`

### Step 5: Refactor User profile popover Layout
Modify `#cora-profile-popover` inside `admin-dashboard.php` to include:
1. Replicated/moved workspace connection status indicator (green dot status dot + text).
2. Clean Notion-style progress bars for quota tracking (AI generations: e.g. 84/100; Vault storage: e.g. 4.2MB/100MB).
