## 2026-07-08T00:54:13Z
You are the Implementation Worker for Milestones M2 (UI Polish) and M3 (AJAX Functionality) of Cora Real Estate Platform v0.1.
Your working directory is /Users/shrutian/Desktop/cora/.agents/worker_m2_m3.
Create your BRIEFING.md and progress.md in your working directory and maintain them.

Objectives:
1. Edit /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php:
   - Register the new AJAX endpoints in cora-real-estate.php and implement their backend handlers:
     - wp_ajax_cora_save_appearance_settings -> cora_ajax_save_appearance_settings()
     - wp_ajax_cora_add_menu_item -> cora_ajax_add_menu_item()
     - wp_ajax_cora_delete_menu_item -> cora_ajax_delete_menu_item()
     - wp_ajax_cora_create_nav_menu -> cora_ajax_create_nav_menu()
     - wp_ajax_cora_moderate_comment -> cora_ajax_moderate_comment()
     - wp_ajax_cora_delete_comment_permanent -> cora_ajax_delete_comment_permanent()
     - wp_ajax_cora_submit_comment_reply -> cora_ajax_submit_comment_reply()
     - wp_ajax_cora_get_attachment_metadata -> cora_ajax_get_attachment_metadata()
     - wp_ajax_cora_save_edited_image -> cora_ajax_save_edited_image()
   - Ensure the new endpoints check referer using 'cora_ajax_nonce', check capabilities, perform actions, and return success/error JSON.
   - Update cora_ajax_gdpr_export and cora_ajax_gdpr_erase to read the email parameter and return a response message mentioning the email address.
   - Run syntax check (php -l) on cora-real-estate.php.

2. Edit /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js:
   - Clean up duplicate GDPR functions (coraRunGdpr* and coraRunGDPR*). Keep only one clean, non-duplicated implementation for export and erase, ensuring they validate the email, read it from the input, and pass it in the AJAX call.
   - Align/define the missing/mismatched JS trigger functions called in the PHP views:
     - Appearance module: coraSaveAppearanceSettings, coraOpenMediaSelector, coraOpenNewMenuDrawer, coraOpenAddMenuItemDrawer, coraCloseAddMenuItemDrawer, coraToggleMenuItemTypeFields, coraSubmitMenuItem, coraRemoveMenuItem.
     - Comments module: coraRefreshComments, coraModerateComment, coraOpenCommentReplyDrawer, coraCloseCommentReplyDrawer, coraDeleteCommentPermanent, coraSubmitCommentReply.
     - Media-Editor module: coraOpenMediaUploader, coraLoadMediaIntoEditor, coraResetEditorCanvas, coraSetCropRatio, coraRotateImage, coraFlipImage, coraSaveEditedImage.
   - Make sure all drawer animations explicitly use translate-x-full and translate-x-0 toggle classes.
   - Ensure NO native browser dialogue overlays (alert, confirm, prompt) are present.

3. Edit views for mobile responsiveness:
   - /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/views/view-pages.php:
     - Change page header structure to flex-col sm:flex-row sm:items-center justify-between gap-4.
     - Wrap table element in overflow-x-auto.
   - /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/views/view-tools.php:
     - Make GDPR email input and buttons stack vertically on mobile (use flex-col sm:flex-row gap-2 pt-1), and make input elements full width on mobile (w-full sm:flex-1).
   - /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate/views/view-settings-suite.php:
     - Make permalinks labels stack vertically on mobile (use flex-col sm:flex-row sm:items-center gap-2) and apply truncation/break-all classes to home_url code blocks.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

When completed, verify syntax using php -l, document results in your handoff report (/Users/shrutian/Desktop/cora/.agents/worker_m2_m3/handoff.md), and notify the Implementation Track Orchestrator (Conversation ID: 4dfea731-c42b-4364-b908-99d008613ce3).
