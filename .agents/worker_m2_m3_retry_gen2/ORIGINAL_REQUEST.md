## 2026-07-08T01:00:56Z
You are a worker. Your working directory is /Users/shrutian/Desktop/cora/.agents/worker_m2_m3_retry_gen2.
Your task is to apply the patch `/Users/shrutian/Desktop/cora/.agents/explorer_m2_m3_retry_3/proposed_changes.patch` to the files in the codebase, or implement the changes manually if the patch does not apply cleanly.

Files to modify:
1. `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php`
2. `app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php`
3. `app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js`

Requirements:
- Make sure that the GDPR AJAX endpoints validate that the email is non-empty and is a valid email, returning JSON errors on failure.
- Ensure that the client-side mock/facade success toasts in GDPR, Media Metadata, and System Settings Suite JS functions are replaced with proper response validation and meaningful error display in `.fail()` callbacks.
- Integrate the existing backend image editor AJAX action into `window.coraSaveEditedImage` using canvas/transformation data (rotate, flip, width, height) collected from the DOM.
- Redesign the profile popover in `admin-dashboard.php` to place the green workspace connection status indicator inside the header and append a Notion-style monochromatic quota card showing storage usage metrics.
- Ensure all custom styling, toasts, and popovers strictly adhere to the global user rules.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Verification:
Run the test command:
`php ajax-challenger-test.php`
Ensure that all tests pass.

Write your handoff report to `/Users/shrutian/Desktop/cora/.agents/worker_m2_m3_retry_gen2/handoff.md` with:
1. Steps taken to apply/implement the changes.
2. The output of running `php ajax-challenger-test.php`.
3. Verification details showing no lint or syntax errors (run `php -l` on modified PHP files).

When done, send a message back to the parent indicating completion.
