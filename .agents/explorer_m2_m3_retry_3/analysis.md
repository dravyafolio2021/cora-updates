# Cora Real Estate CRM - M2 & M3 Code Audit & Analysis Report

This analysis outlines the client-side facades, server-side facades, capability checks, parameter validations, and layout violations in Cora Real Estate Platform v0.1.

---

## 1. GDPR AJAX Validation & Frontend Facade

### A. Observations & Code Location
1. **Backend Handling**: In `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php` (lines 3979–3997):
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
   If the `email` field is missing or invalid, `sanitize_email` returns an empty string `""`. The function then immediately calls `wp_send_json_success`, which outputs:
   `{"success":true,"data":{"message":"GDPR personal data export request generated for ."}}`.
   This is a validation failure because the backend processes empty or invalid strings as successful requests.
2. **Frontend Facade**: In `app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js` (lines 7406–7456):
   - In `window.coraRunGDPRExport` and `window.coraRunGDPRErase`, if the request fails (`.fail()`) or returns `success: false` from the server, the code still invokes `window.coraShowToast` with a simulated success message: `"GDPR personal data export request generated for [email]."` or `"GDPR personal data erasure request processed for [email]."`.
   - If `coraREData.ajaxNonce` is missing, it skips the network entirely and displays a fake success toast.

### B. Proposed Fixes
1. **Backend Validation**: Modify both `cora_ajax_gdpr_export` and `cora_ajax_gdpr_erase` to perform a check using `is_email()` and `empty()`. If it fails, return a JSON error response.
2. **Frontend Handling**: Check the actual `res.success` field in the jQuery success callback, and show an error toast if `success` is false. In the `.fail()` callback, show an error toast. Do not display simulated success toasts when the nonce is missing.

---

## 2. User Popover Layout Violations

### A. Observations & Code Location
1. **File**: `app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php` (lines 2614–2687).
2. **The Issue**: The global layout rules require the Admin User Popover to contain:
   - A workspace status connection indicator (currently placed outside the popover card in the sidebar footer).
   - An active AI model selector (already present).
   - Quota metrics (completely absent).
3. **Dark Theme Continuity**: The CSS overrides for the popover in dark theme target `bg-[#fafaf9]` explicitly (line 1798).

### B. Proposed Fixes
1. **Workspace Connection Indicator**: Add the status indicator directly to the header of the popover `#cora-profile-popover` styled with the Notion-like green light.
2. **Quota Metrics**: Insert a monochromatic quota card matching the Notion theme. Use the class `bg-[#fafaf9]` to automatically support dark theme conversion. Include a zinc progress bar representing storage or query usage (e.g., 42%).

---

## 3. Advanced Media Editor AJAX Integration

### A. Observations & Code Location
1. **Frontend Mockup**: In `app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js` (lines 7485–7487):
   ```javascript
   window.coraSaveEditedImage = function() {
       window.coraShowToast("Media updated successfully.");
   };
   ```
   This does not call the backend at all, acting as a complete client-side facade.
2. **Backend implementation**: The plugin already defines a complete `cora_ajax_save_edited_image` AJAX handler at line 4297 in `cora-real-estate.php` which utilizes `wp_get_image_editor()`, performs `rotate`, `flip`, `crop`, and `resize`, and updates attachment metadata.

### B. Proposed Fixes
1. **Interactive Integration**: Rewrite `window.coraSaveEditedImage` in `admin-script.js` to gather:
   - `attachment_id` (from `#cora-meta-attachment-id`)
   - `rotate` (from preview image `data('rotate')`)
   - `flip` (from preview image `data('scalex')` or `data('scaley')` matching `'h'` or `'v'`)
   - `width`/`height` (from `#cora-scale-width` and `#cora-scale-height`)
2. **Ajax Submission**: Make a real `$.post` call using the existing backend action `cora_save_edited_image`.
3. **Response Handling**: If the response is successful, reload the image preview in the DOM by appending a cache-busting timestamp query string to the image URL (e.g., `url + '?ver=' + Date.now()`). If the request fails, show an error toast. Do not display a mock success toast on request failure.

---

## 4. Media Metadata AJAX Handling

### A. Observations & Code Location
1. **File**: `app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js` (lines 7478–7482):
   ```javascript
   }, function(res) {
       window.coraShowToast("Media updated successfully.");
   }).fail(function() {
       window.coraShowToast("Media updated successfully.");
   });
   ```
   Both the success callback and the fail callback display `"Media updated successfully."` without checking `res.success` or handling HTTP/server errors.

### B. Proposed Fixes
1. **Response Checking**: Validate `res.success === true` and use `res.data.message` if available.
2. **Error Recovery**: In the `.fail()` handler, display a meaningful error toast like `"Failed to save media metadata."`.

---

## 5. System Settings Suite AJAX Handling

### A. Observations & Code Location
1. **File**: `app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js` (lines 7490–7519):
   - If the nonce or form is missing, the frontend mocks success with `window.coraShowToast("Global system settings updated successfully.");`.
   - On completion of the POST request, the success callback and `.fail()` callback both output `"Global system settings updated successfully."`.

### B. Proposed Fixes
1. **Remove Mock Success on Missing Nonce**: Do not show a success toast if the form is missing or the nonce is unavailable.
2. **Check Response Status**: Check `res.success` in the success callback.
3. **Handle Server Failures**: Display a failure toast in the `.fail()` callback.
