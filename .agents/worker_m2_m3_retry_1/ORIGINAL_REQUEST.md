## 2026-07-07T20:09:54Z
You are the Implementation Worker for Milestones M2 (UI Polish) and M3 (AJAX Functionality) of Cora Real Estate Platform v0.1.
Your working directory is /Users/shrutian/Desktop/cora/.agents/worker_m2_m3_retry_1.
Create your BRIEFING.md and progress.md in your working directory and maintain them.

Objectives:
1. Read the handoff report and proposed_changes.patch from the Explorer 3 directory (/Users/shrutian/Desktop/cora/.agents/explorer_m2_m3_retry_3/).
2. Apply all changes from proposed_changes.patch to:
   - /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php
   - /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php
   - /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js
   If direct patching fails, manually apply the exact changes described in the patch and the handoff.

3. Fix the Security Capability Check Bypasses in cora-real-estate.php:
   - Identify the 14 (or more) AJAX handlers in cora-real-estate.php that are missing user capability checks (you can run /Users/shrutian/Desktop/cora/.agents/auditor_m2_m3/audit_cora.py to scan and identify them).
   - Add appropriate capability checks to each of them:
     - Use current_user_can('manage_options') for general administrative functions (e.g., saving settings, deleting pages/articles, uploading media, GBP operations).
     - Or use current_user_can('edit_pages') / current_user_can('edit_posts') / current_user_can('upload_files') if more appropriate.
     - If the check fails, return wp_send_json_error( array( 'message' => 'Unauthorized' ) ).
     - Make sure that ALL backend AJAX handlers in cora-real-estate.php have BOTH check_ajax_referer('cora_ajax_nonce', 'nonce') AND a proper capability check.

4. Verify GDPR Validation on backend and frontend:
   - Validate input emails in cora_ajax_gdpr_export and cora_ajax_gdpr_erase using empty() and is_email(), returning JSON error on failure.
   - Ensure frontend JS handles .fail() and res.success correctly without mock-toast assumptions on errors.

5. Enforce User Popover layout constraints in admin-dashboard.php:
   - Put the green status indicator saying "Connected" inside the popover card header.
   - Include a storage and AI credit metrics block inside the popover card with style bg-[#fafaf9] (which converts cleanly to dark mode).

6. Verify the changes:
   - Run/verify the code using php if available, or write a custom test/verification run if you can.
   - Run the integration tests: php ajax-challenger-test.php. Verify that all tests return success/passed. If php is not in the default path, look for it in local PHP environment paths or run the PHP file using the local server context if possible.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

When completed, document the results in your handoff report (/Users/shrutian/Desktop/cora/.agents/worker_m2_m3_retry_1/handoff.md), and notify the parent conversation ID: 2d3cb2be-fa12-4dbd-a134-929233389d60.
