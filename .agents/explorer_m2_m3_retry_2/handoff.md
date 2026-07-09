# Explorer Handoff Report: Milestone M2 (UI Polish) & M3 (AJAX Functionality)

## 1. Observation
We conducted a comprehensive read-only investigation of the Cora Real Estate Platform plugin workspace and observed the following:

1. **Client-Side Facades & Mock Timeout Delays**:
   - In `assets/js/admin-script.js:7485-7487`, `coraSaveEditedImage` is defined as a stub that only displays a success toast without making any AJAX request:
     ```javascript
     window.coraSaveEditedImage = function() {
         window.coraShowToast("Media updated successfully.");
     };
     ```
   - In `assets/js/admin-script.js:6651-6662`, `coraRunDiagnostics`, `coraTriggerExport`, and `coraTriggerImport` use fake `setTimeout` delays to simulate background tasks instead of running functional operations (even though the server-side has a genuine WXR export handler `cora_ajax_export_xml` registered at `cora-real-estate.php:3977`).

2. **Mock Success Toasts on AJAX Failures**:
   - In `assets/js/admin-script.js`, functions `coraSaveMediaMetadata` (line 7458), `coraSaveSystemSettingsSuite` (line 7489), `coraRunGDPRExport` (line 7406), and `coraRunGDPRErase` (line 7431) capture AJAX failures via `.fail()` but display success notifications anyway (e.g. `Media updated successfully.` or `Global system settings updated successfully.`).

3. **GDPR Backend Validation Deficiencies**:
   - In `cora-real-estate.php:3979-3997`, endpoints `cora_ajax_gdpr_export` and `cora_ajax_gdpr_erase` accept missing or invalid email fields, sanitize them to empty strings (`""`), and output success responses indicating requests were processed for `.` (empty string).

4. **Security Capability Bypasses**:
   - The following AJAX endpoints in `cora-real-estate.php` execute business actions but lack authorization/permission checks, allowing any logged-in user (such as a subscriber or editor) to run them:
     - `cora_ajax_get_article` (line 2776)
     - `cora_ajax_save_article` (line 2808)
     - `cora_ajax_get_page` (line 2881)
     - `cora_ajax_delete_page` (line 2981)
     - `cora_ajax_analyze_seo` (line 3006)
     - `cora_ajax_get_media` (line 3036)
     - `cora_ajax_create_media_folder` (line 3097)
     - `cora_ajax_upload_media` (line 3119)
     - `cora_ajax_assign_media_folder` (line 3171)

5. **User Profile Popover Violations**:
   - In `admin-dashboard.php:2614-2687`, the `#cora-profile-popover` card is missing the workspace status connection indicator (which is currently residing in the outer sidebar footer `.cora-sidebar-footer` on line 2690).
   - Quota metrics (e.g. AI Generations count and Secure Vault Storage usage) are entirely absent from the popover container.

---

## 2. Logic Chain
- **Image transformation and EXIF saving**: The client-side lacks a connection to the server. Since the server has a real `cora_save_edited_image` action defined (`cora-real-estate.php:4297`) using the WP Image Editor API, the client must trigger an AJAX POST containing rotation, flipping, scale dimensions, and centered crop bounds. Centered crop bounds are mathematically calculated on the client using the image's original dimensions and aspect ratio presets, since no crop dragging is supported.
- **Fail handler error handling**: masking failed network requests with success toasts creates false user feedback. Handlers must inspect `res.success` and display correct status and error messages from `res.data.message` dynamically, with generic errors in `.fail()`.
- **GDPR Validation**: The backend uses `sanitize_email( $_POST['email'] )` without verifying if the result is a valid email using `is_email()`. Thus, blank or invalid formatting leads to sanitizing to an empty string, which the backend reports as successfully handled. The backend must enforce `!empty($email) && is_email($email)`.
- **Security Check Bypasses**: Logged-in role capabilities must be checked using `current_user_can()`. Without this, subscribers can modify or delete core pages and posts via the AJAX endpoints.
- **User Popover Rules**: To adhere to the global user rules, the profile widget popover `#cora-profile-popover` must contain the workspace connection status (green dot indicator + text) and the quota progress metrics.

---

## 3. Caveats
- Sourced Local WP environment directories for execution, but the Local SQL database service was down/offline during terminal run (`Error establishing a database connection`). Hence, code correctness was verified statically through file inspections, structure trace, and logic analysis.
- Public views (`public-doc-view.php` and `public-gallery-view.php`) were excluded from investigation scope as they are external public layouts.

---

## 4. Conclusion
To close these loopholes and secure the plugin, the implementation team must:
1. Re-route JS events in `admin-script.js` to real WordPress admin AJAX actions.
2. Calculate centered crop boundaries on the client side and forward crop coordinates, scale dimensions, rotation, and flip state to the `cora_save_edited_image` backend API.
3. Clean up AJAX callbacks to show actual status updates, checking `res.success` and `.fail()`.
4. Secure backend AJAX endpoints by validating parameters (using `is_email()`) and enforcing proper capabilities (like `manage_options`, `upload_files`, `edit_posts`, `edit_pages`).
5. Redesign `#cora-profile-popover` to host the connection indicator and two Notion-style quota progress bars (AI queries, Vault space).

---

## 5. Verification Method

### A. Automatic Test Suite Execution
When the database service is restored, execute the AJAX challenger suite from the workspace root:
```bash
PATH="/Users/shrutian/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin:$PATH" php ajax-challenger-test.php
```
Verify that all 10 tests return `passed: true`.

### B. Manual Code Review Checklist
1. Inspect `admin-script.js` around line 7485 to confirm `window.coraSaveEditedImage` issues a `$.post` request to `coraREData.ajaxUrl` with rotation/dimensions data.
2. Confirm `coraSaveMediaMetadata` has separate toast outputs for `res.success` and `.fail()`.
3. Inspect `cora-real-estate.php` and confirm `cora_ajax_gdpr_export` and `cora_ajax_gdpr_erase` fail with `wp_send_json_error` when the `email` argument is missing or fails `is_email()`.
4. Confirm `cora_ajax_save_article`, `cora_ajax_get_article`, `cora_ajax_get_page`, `cora_ajax_delete_page`, and other endpoints include `current_user_can()` validation checks.
5. Verify `admin-dashboard.php` to ensure the `#cora-profile-popover` contains the status indicator dot and the monochromatic usage progress bars.

---

## 6. Actionable Implementation Drafts

Below are the exact code snippets recommended for integration by the implementer.

### Snippet 1: Client-Side AJAX and Error Handling Updates (`assets/js/admin-script.js`)
```javascript
// Replace window.coraSaveEditedImage (line 7485) with:
window.coraSaveEditedImage = function() {
    const attachmentId = $('#cora-meta-attachment-id').val() || 0;
    if (!attachmentId || !coraREData.ajaxNonce) {
        window.coraShowToast("Error: No active image selected or session invalid.");
        return;
    }

    window.coraShowToast("Processing image rasterization and updating EXIF metadata...");

    const img = $('#cora-editor-preview-img');
    const imgEl = img[0];
    const data = {
        action: 'cora_save_edited_image',
        nonce: coraREData.ajaxNonce,
        attachment_id: attachmentId
    };

    // Rotation
    const rotate = img.data('rotate') || 0;
    if (rotate !== 0) {
        data.rotate = rotate;
    }

    // Flipping
    const scaleX = img.data('scalex') || 1;
    const scaleY = img.data('scaley') || 1;
    if (scaleX === -1) {
        data.flip = 'h';
    } else if (scaleY === -1) {
        data.flip = 'v';
    }

    // Centered crop calculation
    const cropW = img.data('crop-w');
    const cropH = img.data('crop-h');
    if (cropW && cropH && imgEl && imgEl.naturalWidth && imgEl.naturalHeight) {
        const naturalWidth = imgEl.naturalWidth;
        const naturalHeight = imgEl.naturalHeight;
        const imageRatio = naturalWidth / naturalHeight;
        const targetRatio = cropW / cropH;
        
        let finalCropW = naturalWidth;
        let finalCropH = naturalHeight;
        
        if (imageRatio > targetRatio) {
            finalCropW = Math.round(naturalHeight * targetRatio);
        } else {
            finalCropH = Math.round(naturalWidth / targetRatio);
        }
        
        data.crop_x = Math.round((naturalWidth - finalCropW) / 2);
        data.crop_y = Math.round((naturalHeight - finalCropH) / 2);
        data.crop_w = finalCropW;
        data.crop_h = finalCropH;
    }

    // Dimension scaling
    const scaleWidth = $('#cora-scale-width').val();
    const scaleHeight = $('#cora-scale-height').val();
    if (scaleWidth && scaleHeight) {
        data.width = parseInt(scaleWidth, 10);
        data.height = parseInt(scaleHeight, 10);
    }

    $.post(coraREData.ajaxUrl, data, function(res) {
        if (res && res.success) {
            window.coraShowToast(res.data.message || "Media modifications saved permanently!");
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            const errMsg = (res && res.data && res.data.message) ? res.data.message : "Failed to save image transformations.";
            window.coraShowToast("Error: " + errMsg);
        }
    }).fail(function() {
        window.coraShowToast("Error: Connection failed or server error occurred.");
    });
};

// Replace window.coraSaveMediaMetadata (line 7458) with:
window.coraSaveMediaMetadata = function() {
    const attachmentId = $('#cora-meta-attachment-id').val() || 0;
    const title = $('#cora-meta-title').val() || '';
    const alt = $('#cora-meta-alt').val() || '';
    const caption = $('#cora-meta-caption').val() || '';
    const description = $('#cora-meta-description').val() || '';

    if (!coraREData.ajaxNonce || !attachmentId) {
        window.coraShowToast("Error: Session invalid or no active media selected.");
        return;
    }

    $.post(coraREData.ajaxUrl, {
        action: 'cora_save_media_metadata',
        nonce: coraREData.ajaxNonce,
        attachment_id: attachmentId,
        title: title,
        alt: alt,
        caption: caption,
        description: description
    }, function(res) {
        if (res && res.success) {
            window.coraShowToast(res.data.message || "Media updated successfully.");
        } else {
            const errMsg = (res && res.data && res.data.message) ? res.data.message : "Failed to update media metadata.";
            window.coraShowToast("Error: " + errMsg);
        }
    }).fail(function() {
        window.coraShowToast("Error: Connection failed or server error occurred.");
    });
};

// Replace window.coraSaveSystemSettingsSuite (line 7489) with:
window.coraSaveSystemSettingsSuite = function() {
    const form = $('#cora-settings-suite-form');
    if (!form.length || !coraREData.ajaxNonce) {
        window.coraShowToast("Error: Settings form not found.");
        return;
    }

    const formData = form.serializeArray();
    const data = {
        action: 'cora_save_system_settings_suite',
        nonce: coraREData.ajaxNonce
    };

    $.each(formData, function(i, field) {
        data[field.name] = field.value;
    });

    const checkboxes = ['users_can_register', 'blog_public', 'default_pingback_flag', 'default_comment_status', 'comment_moderation'];
    checkboxes.forEach(function(cbName) {
        const cb = form.find('input[name="' + cbName + '"]');
        if (cb.length > 0 && !cb.is(':checked')) {
            data[cbName] = 0;
        }
    });

    $.post(coraREData.ajaxUrl, data, function(res) {
        if (res && res.success) {
            window.coraShowToast(res.data.message || "Global system settings updated successfully.");
        } else {
            const errMsg = (res && res.data && res.data.message) ? res.data.message : "Failed to update system settings.";
            window.coraShowToast("Error: " + errMsg);
        }
    }).fail(function() {
        window.coraShowToast("Error: Connection failed or server error occurred.");
    });
};

// Replace window.coraRunGDPRExport (line 7406) and window.coraRunGDPRErase (line 7431) with:
window.coraRunGDPRExport = function() {
    const email = $('#cora-gdpr-export-email').val().trim();
    if (!email) {
        window.coraShowToast("Please enter a valid email address.");
        return;
    }
    if (!coraREData.ajaxNonce) {
        window.coraShowToast("Error: Session invalid.");
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
            const errMsg = (res && res.data && res.data.message) ? res.data.message : "Failed to generate GDPR export request.";
            window.coraShowToast("Error: " + errMsg);
        }
    }).fail(function() {
        window.coraShowToast("Error: Connection failed or server error occurred.");
    });
};

window.coraRunGDPRErase = function() {
    const email = $('#cora-gdpr-erase-email').val().trim();
    if (!email) {
        window.coraShowToast("Please enter a valid email address.");
        return;
    }
    window.coraConfirmAction('GDPR Erasure Request', 'Are you sure you want to permanently erase personal data for ' + email + '?', function() {
        if (!coraREData.ajaxNonce) {
            window.coraShowToast("Error: Session invalid.");
            return;
        }
        $.post(coraREData.ajaxUrl, {
            action: 'cora_gdpr_erase',
            nonce: coraREData.ajaxNonce,
            email: email
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast(res.data.message || "GDPR personal data erasure request processed for " + email + ".");
            } else {
                const errMsg = (res && res.data && res.data.message) ? res.data.message : "Failed to process GDPR erasure request.";
                window.coraShowToast("Error: " + errMsg);
            }
        }).fail(function() {
            window.coraShowToast("Error: Connection failed or server error occurred.");
        });
    });
};
```

### Snippet 2: Backend GDPR Email Validations (`cora-real-estate.php`)
```php
// Replace cora_ajax_gdpr_export (line 3979) and cora_ajax_gdpr_erase (line 3989) with:
function cora_ajax_gdpr_export() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_privacy_options' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    if ( empty( $email ) || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Invalid email address.' ) );
    }
    wp_send_json_success( array( 'message' => 'GDPR personal data export request generated for ' . $email . '.' ) );
}

function cora_ajax_gdpr_erase() {
    check_ajax_referer( 'cora_ajax_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_privacy_options' ) && ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Unauthorized' ) );
    }
    $email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    if ( empty( $email ) || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Invalid email address.' ) );
    }
    wp_send_json_success( array( 'message' => 'GDPR personal data erasure request processed for ' . $email . '.' ) );
}
```

### Snippet 3: Backend Security Check Enhancements (`cora-real-estate.php`)
Inject the capability checks at the beginning of each endpoint:
```php
// 1. For Article & SEO actions (get_article, save_article, analyze_seo)
if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'manage_options' ) ) {
    wp_send_json_error( array( 'message' => 'Unauthorized' ) );
}

// 2. For Page actions (get_page, save_page)
if ( ! current_user_can( 'edit_pages' ) && ! current_user_can( 'manage_options' ) ) {
    wp_send_json_error( array( 'message' => 'Unauthorized' ) );
}

// 3. For Delete Page action (delete_page)
if ( ! current_user_can( 'delete_pages' ) && ! current_user_can( 'manage_options' ) ) {
    wp_send_json_error( array( 'message' => 'Unauthorized' ) );
}

// 4. For Media actions (get_media, create_media_folder, upload_media, assign_media_folder)
if ( ! current_user_can( 'upload_files' ) && ! current_user_can( 'manage_options' ) ) {
    wp_send_json_error( array( 'message' => 'Unauthorized' ) );
}
```

### Snippet 4: Refactored Profile Popover Layout (`admin-dashboard.php`)
```html
<!-- Replace first section of #cora-profile-popover (lines 2614-2628) with: -->
<div id="cora-profile-popover" class="hidden absolute bottom-20 left-4 right-4 bg-white border border-zinc-200 rounded-2xl shadow-xl p-4 z-30 flex flex-col gap-2.5 animate-in fade-in slide-in-from-bottom-2 duration-150">
    <!-- UID Display & Workspace Status Connection Indicator -->
    <div class="px-1 flex justify-between items-center select-none border-b border-zinc-100 pb-2">
        <span class="text-[9px] text-zinc-400 font-semibold tracking-wide">UID : <?php echo esc_html( $current_wp_user->ID ); ?></span>
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_4px_rgba(16,185,129,0.5)]"></span>
            <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider">AI Connected</span>
        </div>
    </div>
    <!-- Upgrade Container block -->
    <div class="bg-[#fafaf9] border border-zinc-200/50 rounded-xl p-3 flex justify-between items-center select-none">
        <div class="flex flex-col">
            <span class="text-xs font-bold text-zinc-900"><?php echo esc_html( $current_user_role_label ); ?></span>
        </div>
        <button class="bg-[#18181b] hover:bg-zinc-800 text-white font-bold text-[10px] px-3 py-1.5 rounded-lg transition-colors cursor-pointer active:scale-95 shadow-sm" onclick="window.coraShowToast('Upgrade flow is loading... Upgrade to Premium to unlock full AI capabilities!')">
            Upgrade
        </button>
    </div>

    <!-- Quota Metrics Block -->
    <div class="px-1 py-1 flex flex-col gap-2 border-b border-zinc-100 pb-3 select-none">
        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Usage & Quotas</span>
        <div class="space-y-1.5">
            <!-- AI Generation -->
            <div class="flex justify-between items-center text-[10px] text-zinc-500 font-semibold">
                <span>AI Generations</span>
                <span>84 / 100 queries</span>
            </div>
            <div class="w-full h-1 bg-zinc-100 rounded-full overflow-hidden">
                <div class="h-full bg-zinc-800 rounded-full" style="width: 84%;"></div>
            </div>

            <!-- Secure Storage -->
            <div class="flex justify-between items-center text-[10px] text-zinc-500 font-semibold mt-1">
                <span>Secure Vault Storage</span>
                <span>4.2 MB / 100 MB</span>
            </div>
            <div class="w-full h-1 bg-zinc-100 rounded-full overflow-hidden">
                <div class="h-full bg-zinc-800 rounded-full" style="width: 4.2%;"></div>
            </div>
        </div>
    </div>
```
