# Handoff Report — 2026-07-08T00:48:33+05:30

This soft handoff report summarizes the investigation of the Cora Real Estate Platform v0.1 plugin codebase for Milestones M2 and M3.

---

## 1. Observation

Direct code observations from the plugin directory `/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate`:

1.  **Appearance Module**:
    *   File `views/view-appearance.php` calls:
        *   `onclick="coraSaveAppearanceSettings()"` (line 35)
        *   `onclick="coraOpenMediaSelector('cora-brand-logo-url')"` (line 78)
        *   `onclick="coraOpenNewMenuDrawer()"` (line 110)
        *   `onclick="coraRemoveMenuItem(...)"` (line 138)
        *   `onclick="coraOpenAddMenuItemDrawer()"` (line 149)
        *   `onclick="coraCloseAddMenuItemDrawer()"` (line 166)
        *   `onchange="coraToggleMenuItemTypeFields(this.value)"` (line 174)
        *   `onclick="coraSubmitMenuItem()"` (line 203)
    *   File `assets/js/admin-script.js` defines only these Appearance hooks:
        *   `window.coraActivateTheme = function(id)` (line 6631)
        *   `window.coraSaveMenuStructure = function()` (line 6635)
        *   `window.coraSaveWidgetSettings = function()` (line 6639)
        *   `window.coraSaveCustomCSS = function()` (line 6643)
    *   *Result*: None of the trigger functions called in the PHP view exist in JS.

2.  **Comments Module**:
    *   File `views/view-comments.php` calls:
        *   `onclick="coraRefreshComments()"` (line 41)
        *   `onclick="coraModerateComment(..., '...')"` (line 125)
        *   `onclick="coraOpenCommentReplyDrawer(..., '...', '...')"` (line 136)
        *   `onclick="coraDeleteCommentPermanent(...)"` (line 158)
        *   `onclick="coraCloseCommentReplyDrawer()"` (line 178)
    *   File `assets/js/admin-script.js` defines:
        *   `window.coraToggleSelectAllComments = function(el)` (line 6584)
        *   `window.coraBulkActionComments = function()` (line 6587)
        *   `window.coraOpenCommentReplyModal = function(id, author, excerpt)` (line 6601)
        *   `window.coraSubmitCommentReply = function()` (line 6608)
        *   `window.coraUpdateCommentStatus = function(id, action)` (line 6618)
        *   `window.coraFilterComments = function(status, btn)` (line 6622)
    *   *Result*: Calls to `coraRefreshComments`, `coraModerateComment`, `coraOpenCommentReplyDrawer`, `coraDeleteCommentPermanent`, and `coraCloseCommentReplyDrawer` are missing or mismatched in name in JS.

3.  **Media-Editor Module**:
    *   File `views/view-media-editor.php` calls:
        *   `onclick="coraOpenMediaUploader()"` (line 37)
        *   `onchange="coraLoadMediaIntoEditor(this.value)"` (line 52)
        *   `onclick="coraResetEditorCanvas()"` (line 63)
        *   `onclick="coraSetCropRatio(..., ...)"` (line 71)
        *   `onclick="coraRotateImage(...)"` (line 79)
        *   `onclick="coraFlipImage('h')"` (line 85)
        *   `onclick="coraSaveEditedImage()"` (line 112)
    *   File `assets/js/admin-script.js` defines:
        *   `window.coraOpenMediaEditorModal = function(id, url, name, dims)` (line 6685)
        *   `window.coraApplyMediaTransform = function(type)` (line 6694)
        *   `window.coraSaveMediaEdits = function()` (line 6713)
        *   `window.coraSaveMediaMetadata = function()` (line 7058)
        *   `window.coraSaveEditedImage = function()` (line 7085)
    *   *Result*: JS callbacks triggered in the view are missing/unimplemented, except `coraSaveEditedImage` (mock-only) and `coraSaveMediaMetadata` (actual AJAX call).

4.  **AJAX Handlers**:
    *   `cora-real-estate.php` registers 60 AJAX hooks.
    *   Hooks `cora_gbp_save_profile`, `cora_gbp_save_review_reply`, and `cora_gbp_save_post` are registered in PHP but are never referenced or triggered in JS.
    *   `admin-script.js` has duplicate casing declarations for GDPR compliance:
        *   `coraRunGdprExport` / `coraRunGdprErase` (lines 6663, 6672) - mixed case, dead code.
        *   `coraRunGDPRExport` / `coraRunGDPRErase` (lines 7020, 7039) - uppercase, active.
        *   *Observation*: The active uppercase handlers do not read input values from `#cora-gdpr-export-email` or `#cora-gdpr-erase-email` and submit empty payloads.
    *   There are **no comment-related or appearance-related AJAX hooks** registered in `cora-real-estate.php`.

5.  **Responsive Layouts**:
    *   `view-pages.php` contains:
        *   `<div class="cora-page-header flex items-center justify-between">` on line 33 (fails to wrap to column on mobile).
        *   `<table class="w-full text-left border-collapse">` on line 94 without any enclosing `overflow-x-auto` horizontal scroll block.
    *   `view-tools.php` GDPR inputs (lines 183, 191) squeeze the input elements to tiny sizes on mobile.
    *   `view-settings-suite.php` SEO permalinks code blocks (lines 223–256) lack truncation and break viewports on 375px/430px when domain names are long.

6.  **Dialogue Overlays & Styles**:
    *   No occurrences of `alert()`, `confirm()`, or `prompt()` are used. All messaging is piped through `window.coraShowToast`.
    *   Neutral monochromatic grey/black styling is strictly followed, and SVGs use thin vector paths.
    *   Task/page drawers use `translate-x-full` for translations, but do not explicitly add `translate-x-0` upon entry.

---

## 2. Logic Chain

1.  **Script Callback Failures**: Since PHP view templates render `<button onclick="functionName()">` with specific function names (e.g., `coraSaveAppearanceSettings`), and these names are completely absent in the globally enqueued `admin-script.js` file, clicking those buttons throws a `ReferenceError` at runtime and stalls user interactions.
2.  **Mocked Modules**: Since there are no AJAX handlers enqueued for comments or appearance in `cora-real-estate.php`, these modules are fundamentally designed as mocks in the current v0.1 plugin state.
3.  **Mobile Viewport Breakers**: Standard HTML tables require horizontal scroll containers (like a div with `overflow-x-auto`) to be responsive on 375px/430px viewports. Because the table in `view-pages.php` spans 6 columns and lacks this, it will force a horizontal scroll of the entire admin dashboard viewport.
4.  **GDPR Form Logic Failure**: In `admin-script.js`, because the uppercase `coraRunGDPRExport` and `coraRunGDPRErase` do not read the inputs `#cora-gdpr-export-email` and `#cora-gdpr-erase-email` (unlike the dead mixed-case functions), the AJAX requests they submit contain no email identifier data, rendering the compliance suite non-functional.

---

## 3. Caveats

*   Only checked files inside `cora-real-estate` plugin directory.
*   Assumed Tailwind CSS library itself is loaded correctly (loaded from CDN link `https://cdn.tailwindcss.com` inside `admin-dashboard.php` which is outside the scope of our module templates).
*   No functional testing was conducted on active local installations due to read-only constraints, but call mappings were verified directly by static analysis of JS and PHP sources.

---

## 4. Conclusion

*   The v0.1 plugin contains substantial JS callback mismatches and missing stubs that prevent the **Appearance**, **Comments**, and **Media-Editor** modules from being operational.
*   The **Pages** module has critical responsiveness issues on mobile screen viewports (375px/430px) due to a non-responsive header and an table element that overflows the screen.
*   The **GDPR suite** in the Tools module has dual-casing duplication and submits blank AJAX requests without reading the input parameters.
*   The platform is fully compliant with the global guidelines regarding native dialogues (none used) and Notion/Shopify styling.

---

## 5. Verification Method

To verify these findings manually:
1.  **Inspect Callback Mismatches**: Match the `onclick` callbacks in `views/view-appearance.php`, `views/view-comments.php`, and `views/view-media-editor.php` with declarations in `assets/js/admin-script.js`.
2.  **Verify GDPR Casing Bug**: Open `assets/js/admin-script.js` and locate lines 6663, 6672 (`coraRunGdpr*`) and lines 7020, 7039 (`coraRunGDPR*`). Check that the latter ones contain no input selectors.
3.  **Evaluate Mobile Responsiveness**: Look at lines 33 and 93–94 of `views/view-pages.php` to confirm the lack of horizontal wrapper classes and screen width breakpoints.

---

## 6. Remaining Work

1.  **Resolve JavaScript Callbacks Mismatches**:
    *   Align functions in `admin-script.js` to match the exact names called in `view-appearance.php` and `view-comments.php`.
    *   Implement stubs or correct the mapping in `admin-script.js` for `coraOpenMediaUploader`, `coraLoadMediaIntoEditor`, `coraResetEditorCanvas`, `coraSetCropRatio`, `coraRotateImage`, and `coraFlipImage` for the Media-Editor.
2.  **Implement Comments & Appearance Backend AJAX Handlers**:
    *   Define hooks in `cora-real-estate.php` for comments moderation/replies and menu savings.
    *   Update the front-end JS functions to submit actual AJAX requests to the new hooks rather than reloading the page statically.
3.  **Fix GDPR AJAX Parameters**:
    *   Merge duplicate GDPR functions in `admin-script.js`.
    *   Update the AJAX call to read and submit the email parameter.
4.  **Fix Mobile Responsiveness Viewport Bugs**:
    *   Wrap the Pages table in a `div` with class `overflow-x-auto`.
    *   Change the Pages header classes to `flex flex-col sm:flex-row gap-4 justify-between sm:items-center`.
    *   Add responsive layouts for GDPR forms to prevent email input squashing on mobile.
    *   Add text truncation/wrap to SEO Permalinks URL codes in Settings-Suite.
