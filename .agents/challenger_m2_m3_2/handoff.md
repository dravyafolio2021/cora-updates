# Handoff Report - Milestone M2/M3 Review

## 1. Observation

During my empirical review of the Cora Real Estate Platform v0.1 plugin, I observed the following issues across the source files:

### A. Missing Email Parameter Validation in GDPR AJAX Endpoints
* **File Path**: `/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`
* **Lines**: 3979–3997
* **Verbatim Code**:
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
* **Result**: Running our automated test suite script `/Users/shrutian/Desktop/cora/tests/run_ajax_tests.php` with missing email param (`$email = ''`) returned success:
```json
{"success":true,"data":{"message":"GDPR personal data export request generated for ."}}
```

### B. Fake Success Toasts for GDPR in JS
* **File Path**: `/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js`
* **Lines**: 7408–7428
* **Verbatim Code**:
```javascript
window.coraRunGDPRExport = function() {
    const email = $('#cora-gdpr-export-email').val().trim();
    if (!email) {
        window.coraShowToast("Please enter a valid email address.");
        return;
    }
    if (!coraREData.ajaxNonce) {
        window.coraShowToast("GDPR personal data export request generated for " + email + ".");
        return;
    }
    $.post(coraREData.ajaxUrl, {
        action: 'cora_gdpr_export',
        nonce: coraREData.ajaxNonce,
        email: email
    }, function(res) {
        if (res && res.success) {
            window.coraShowToast(res.data.message || "GDPR personal data export request generated for " + email + ".");
        } else {
            window.coraShowToast("GDPR personal data export request generated for " + email + ".");
        }
    }).fail(function() {
        window.coraShowToast("GDPR personal data export request generated for " + email + ".");
    });
};
```
* **Result**: On nonce mismatch or network error, the JS client still issues a success toast saying the export was successfully generated/processed.

### C. Critical Authorization Bypasses in Content Management AJAX Endpoints
* **File Path**: `/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`
* **Article Saving** (Lines 2808–2833): Lacks capability checks completely:
```php
function cora_ajax_save_article() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    ...
```
* **Page Saving** (Lines 2915–2924): Empty check block allowing authorization bypass:
```php
function cora_ajax_save_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    if ( ! current_user_can( 'edit_pages' ) && ! current_user_can( 'manage_options' ) ) {
        $user = wp_get_current_user();
        if ( empty( $user->roles ) || ! in_array( 'administrator', (array) $user->roles ) ) {
            // Allow if admin or edit_pages capability
        }
    }
```
* **Page Deletion** (Lines 2981–2988): Lacks capability checks completely:
```php
function cora_ajax_delete_page() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );

    $page_id = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
```

### D. Conformance Violations (Missing Quota Metrics)
* **File Path**: `/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php`
* **Lines**: 2614–2687 (`#cora-profile-popover`)
* **Result**: Quota metrics are missing from the administrator profile popover menu widget, violating the global UI rule *("Include a workspace status connection indicator, an active AI model selector, and quota metrics directly in the popover menu.")*.

---

## 2. Logic Chain

1. **Email Sanitize to Empty**: Under `cora_ajax_gdpr_export`, input is sanitized using `sanitize_email()`. If the input is empty or invalid (e.g. `'not-an-email'`), it returns `''`. Because the function has no `empty( $email )` check, it returns a success JSON string with an empty email (e.g. `"... generated for ."`).
2. **Deceptive Toast Feedback**: In the JS AJAX failure or missing nonce branches for GDPR actions, the success message is hardcoded into the fallback `coraShowToast` calls. Thus, the client interface deceptively reports success to the user when the request was either not sent or rejected by the server.
3. **Privilege Escalation**:
   - `cora_ajax_save_page` checks permissions but does not terminate execution or return an error if they are missing (the conditional body is empty). Therefore, execution continues and saves/updates pages.
   - `cora_ajax_save_article` and `cora_ajax_delete_page` do not check user roles/capabilities, meaning any logged-in user with a valid nonce (e.g., subscriber) can perform these actions.
4. **Missing Quota Widget**: The global rules require quota metrics to be located directly inside the popover menu card. Reviewing `#cora-profile-popover` shows only UID, Upgrade button, active AI model selector, Account Settings, Tour, and Logout. Quota metrics are not present.

---

## 3. Caveats

- We could not test actual GBP API connections or Google Drive network integrations since we are operating in a local development environment under CODE_ONLY network restrictions. However, all local WP AJAX handlers and capability validations were fully verified.

---

## 4. Conclusion

**Verdict**: **FAIL**

While the UI responsiveness (375px/430px mobile drawer toggles, standard grid adjustments) is correctly implemented using Tailwind classes, the Milestone M2 (UI Polish) and M3 (AJAX Functionality) fail due to critical authorization bypasses, lack of input parameter validations, deceptive frontend feedback stubs, and non-compliance with the admin popover quota metrics specification.

---

## 5. Verification Method

To independently verify these findings, run the custom PHP-CLI AJAX test suite against the Local WordPress environment:

```bash
"/Users/shrutian/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php" /Users/shrutian/Desktop/cora/tests/run_ajax_tests.php
```

Ensure the test output matches the failures logged in the summary table (GDPR empty/invalid emails succeeding, capability checks succeeding or missing).
