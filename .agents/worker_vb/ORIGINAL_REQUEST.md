## 2026-07-08T10:54:31Z

You are teamwork_preview_worker (conv ID visual_builder_implementation).
Your task is to implement the Visual Canvas Page Builder frontend feature for the Cora Real Estate Platform.

Please perform the following steps:

1. Update `/Users/shrutian/Desktop/cora/PROJECT.md` at project root:
   - Add new milestones:
     - M6 | Visual_Canvas_Integration | Evaluate and integrate GrapesJS in the Cora dashboard. | M5 | COMPLETED
     - M7 | AI_Layout_Engine | Implement server-side AJAX generation endpoint utilizing configured AI providers or fallback. | M6 | COMPLETED
     - M8 | Mobile_First_Frontend | Save designs in WordPress page meta and intercept template redirect to serve raw HTML/CSS. | M7 | COMPLETED
     - M9 | Cora_UI_Aesthetic | Implement settings drawers and monochrome visual styling for GrapesJS matching Cora UI rules. | M8 | COMPLETED
     - M10 | E2E_Verification | Build E2E test scripts, verify responsiveness on mobile, and audit code integrity. | M9 | COMPLETED

2. Modify `cora-real-estate.php`:
   - Add `'visual-builder'` to the administrator features array `$allowed_features` on line 163.
   - Hook to `template_redirect` with priority 5: check if `is_page()` and `get_post_meta( get_the_ID(), '_cora_is_visual_builder', true ) === '1'`. Clean the output buffer, output the custom HTML structure serving the GrapesJS HTML and CSS, injection of Tailwind CDN (`<script src="https://cdn.tailwindcss.com"></script>`), viewport meta for responsiveness, and `exit;`.
   - Register AJAX handlers for:
     - `wp_ajax_cora_save_builder_page` (saves title, slug, status, html, css to a standard page post type, setting `_cora_is_visual_builder` to `'1'` and HTML/CSS to meta).
     - `wp_ajax_cora_get_builder_page` (retrieves the saved page HTML, CSS, title, slug, status).
     - `wp_ajax_cora_generate_layout` (takes a text prompt and returns a JSON payload containing generated Tailwind HTML and custom CSS. Integrate with the configured OpenAI/Gemini providers via options `cora_re_ai_openai_key` and `cora_re_ai_gemini_key`. If keys are missing, return a beautiful responsive villa landing page HTML mockup as a fallback, ensuring stability and testability).

3. Modify `assets/js/admin-script.js`:
   - Add `'visual-builder'` to the two administrator capabilities lists around lines 99 and 2369.

4. Modify `admin-dashboard.php`:
   - Add the "Visual Builder" navigation menu item under the "WordPress Core" group, with `data-target="visual-builder"` (placed right below Pages).
   - Add the visual builder section wrapper around line 6274 (below Pages section):
     ```php
     <?php if ( $sub_page === 'visual-builder' ) : ?>
     <section id="cora-page-visual-builder" class="cora-page-section cora-active space-y-6">
         <?php include CORA_REAL_ESTATE_AI_PATH . 'views/view-visual-builder.php'; ?>
     </section>
     <?php endif; ?>
     ```

5. Create `views/view-visual-builder.php`:
   - Load GrapesJS CSS CDN (`https://unpkg.com/grapesjs/dist/css/grapes.min.css`) and JS CDN (`https://unpkg.com/grapesjs/dist/grapes.min.js`).
   - Create a top header bar with page selection dropdown, AI prompt text input, "Generate Page" button, "Settings" button, and "Publish" button.
   - Build a layout: a left sidebar for GrapesJS custom blocks (Section, Text, Image, Button, Columns) and a main canvas area for the GrapesJS editor.
   - Include a right-sliding settings drawer styled strictly to the Cora monochromatic style. The settings drawer must edit Page Title, URL Slug, Page Status (Published / Draft / Private).
   - Write custom styling overrides in a `<style>` tag to style all GrapesJS panels, buttons, blocks, and elements to be strictly monochromatic (slate/zinc grays, whites, and blacks, no colorful default styles).
   - Write JS in this view to:
     - Initialize GrapesJS editor.
     - Handle page selection change: if page is selected, call AJAX to get its data and set it into the editor via `editor.setComponents(data.html)` and `editor.setStyle(data.css)`. If "New Page" is selected, clear editor components.
     - Handle AJAX call for AI layout generation: send prompt to server, show a clean monochromatic loading indicator, and set editor content on success. Use `window.coraShowToast` for errors/success.
     - Handle "Publish" save AJAX: extract editor HTML (`editor.getHtml()`) and CSS (`editor.getCss()`), page settings from inputs/drawers, send to server, show a success toast with the frontend link, and update page select dropdown list.
     - Check if `page_id` query param exists in `window.location.search` and auto-select it.

6. Add quick-action access in `views/view-pages.php`:
   - If a page has `_cora_is_visual_builder` meta set to `'1'`, change the "Edit" button action to redirect to `/workspace/visual-builder?page_id={page_id}` or let it trigger loading of the Visual Builder tab.

7. Write Playwright E2E tests:
   - Create a new E2E test file `/Users/shrutian/Desktop/cora/tests/e2e/visual-builder.spec.ts` (or add to `new-features-empirical.spec.ts`).
   - Login, go to `/workspace/visual-builder`.
   - Submit an AI generation text prompt, verify it populates GrapesJS canvas.
   - Open settings drawer, fill title and slug, click Publish. Verify success toast.
   - Navigate to the published page's frontend URL, assert that it renders cleanly at 375px mobile viewport without horizontal scrolling, and check for any console errors.

8. Run verification commands:
   - Run PHP syntax checks: `find . -name "*.php" ! -path "*/node_modules/*" ! -path "*/vendor/*" -exec php -l {} \;` (verify 0 errors).
   - Run Playwright E2E tests: `npx playwright test` (confirm all pass).
