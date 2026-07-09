# Execution Plan: Visual Canvas Page Builder

This document outlines the step-by-step plan for implementing the visual canvas-based frontend page builder.

## Plan Summary
We will integrate GrapesJS into the Cora admin dashboard, implement an AI layout generator endpoint that connects to configured AI providers, store generated designs in WordPress page meta, and intercept frontend rendering to serve clean, lightweight HTML/CSS bypassing standard theme constraints.

---

## Steps

### Step 1: Evaluating and Integrating Visual Canvas (R1, R4)
- **Objective**: Load GrapesJS on a dedicated tab in the Cora admin dashboard and initialize it cleanly.
- **Actions**:
  1. Add `'visual-builder'` to the allowed features list in `cora-real-estate.php` so it passes workspace role/authentication checks.
  2. Add the "Visual Builder" navigation menu item under the "WordPress Core" group in `admin-dashboard.php`.
  3. Include a template section for `'visual-builder'` in the subpage section of `admin-dashboard.php` that includes `views/view-visual-builder.php`.
  4. Create `views/view-visual-builder.php` containing:
     - External CSS: `https://unpkg.com/grapesjs/dist/css/grapes.min.css`
     - External JS: `https://unpkg.com/grapesjs/dist/grapes.min.js`
     - A container `div id="cora-gjs-editor"` to render GrapesJS.
     - Styles to customize GrapesJS UI elements to be monochromatic (Notion/Shopify minimalist aesthetic).
     - UI components for selecting a page to edit (dropdown or select menu), entering an AI layout prompt, and buttons for publishing.
     - Right-sliding side drawer for page settings (Page Title, Slug, Status).

### Step 2: Implement AI Prompt-to-Layout Engine (R2, R3)
- **Objective**: Create the server-side AJAX endpoint that contacts the AI API to generate page layout HTML/CSS and injects it into GrapesJS.
- **Actions**:
  1. Register AJAX actions `wp_ajax_cora_generate_layout` in `cora-real-estate.php`.
  2. Implement `cora_ajax_generate_layout()`:
     - Check permissions and nonce.
     - Retreive the configured AI model/API keys (using same pattern as `cora_ajax_ai_chat`).
     - Prompt the model with a system prompt instructing it to output structured HTML and CSS representing the requested landing page.
     - The HTML must use clean, responsive components utilizing Tailwind CSS utility classes.
     - The response should be returned in JSON format: `{ "html": "...", "css": "..." }`. If API keys are missing, return a clean mockup fallback layout to ensure the system remains testable and robust without failing.
  3. Update `assets/js/admin-script.js` to handle:
     - Triggering the AI generation AJAX call when the "Generate Page" button is clicked.
     - Displaying a loading state.
     - Injecting the returned HTML/CSS into the GrapesJS editor using `editor.setComponents(html)` and `editor.setStyle(css)`.
     - Using `window.coraShowToast` for feedback.

### Step 3: Implement High-Performance Mobile-First Frontend Rendering & Saving (R3, R4)
- **Objective**: Save GrapesJS canvas markup into WordPress pages and render it on the frontend by bypassing theme constraints.
- **Actions**:
  1. Register AJAX action `wp_ajax_cora_save_builder_page` in `cora-real-estate.php`.
  2. Implement `cora_ajax_save_builder_page()`:
     - Accept page title, slug, status, GrapesJS HTML, and GrapesJS CSS.
     - Create or update the WordPress post (type `page`).
     - Set post meta `_cora_is_visual_builder` to `'1'`.
     - Set post meta `_cora_visual_builder_html` and `_cora_visual_builder_css`.
     - Return the published page's frontend URL.
  3. Intercept template rendering:
     - Hook into `template_redirect` in `cora-real-estate.php`.
     - If the queried page is a visual builder page (i.e. `_cora_is_visual_builder` is `'1'`), clean the buffer and output the custom HTML template directly:
       ```php
       echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
       echo '<style>' . esc_html( $css ) . '</style>';
       echo '<script src="https://cdn.tailwindcss.com"></script>';
       echo '</head><body>' . $html . '</body></html>';
       exit;
       ```
  4. In JS, wire up the Save / Publish button to call this AJAX save handler and display a success toast with the frontend URL link.

### Step 4: Verification and E2E Tests (R2, R3, R4)
- **Objective**: Verify that visual builder works, renders mobile-responsively, and has no console or PHP errors.
- **Actions**:
  1. Write E2E test file `tests/e2e/visual-builder.spec.ts` that:
     - Logins as administrator.
     - Navigates to `/workspace/visual-builder`.
     - Submits a prompt to generate a page structure.
     - Confirms GrapesJS canvas is loaded and has content.
     - Fills page details (Page Title, Slug) and clicks Publish.
     - Visually inspects or checks that the page exists and the frontend URL is correct.
     - Navigates to the frontend URL.
     - Asserts that it loads cleanly on a mobile viewport (375px) without console errors or horizontal scrolling.
  2. Run PHP linting (`php -l`) on all new/modified files.
