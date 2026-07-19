# Project: Cora Real Estate Platform v0.1

## Architecture
Cora Real Estate Platform v0.1 is a WordPress SaaS plugin designed with a Notion/Shopify minimalist monochromatic UI/UX. The plugin overrides native WordPress admin views and implements specialized real estate workspaces.
- **Main Entry Point**: `cora-real-estate.php` routes request paths, registers AJAX endpoints, and enqueues scripts.
- **Dashboard Renderer**: `admin-dashboard.php` acts as the main shell for the workspace layout and enqueues core/custom sub-page templates.
- **Core Replacement Modules**: Located in `views/view-*.php` (Pages, Comments, Appearance, Tools, Media-Editor, Settings-Suite).
- **Assets**: 
  - `assets/js/admin-script.js`: Interactive stubs, AJAX form handlers, right-sliding drawers, and canvas manipulation.
  - `assets/css/admin-style.css`: Theme styles, transitions, layout overrides.

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|---|---|---|---|
| M1 | E2E_Test_Infra | Setup E2E testing framework, design & generate Tier 1-4 tests (Feature, Boundary, Pairwise, Workload) and publish `TEST_READY.md`. | None | COMPLETED |
| M2 | UI_Polish | Ensure 375px/430px mobile responsiveness, monochromatic theme, right-sliding drawers, and coraShowToast in the 6 replacement modules. | None | COMPLETED |
| M3 | AJAX_Functionality | Fully implement and verify AJAX handlers in `cora-real-estate.php` and JS stubs in `admin-script.js`. | M2 | COMPLETED |
| M4 | Packaging | Run PHP syntax checks, clean debug/test files, and compile final plugin zip package `cora-real-estate-v0.1.zip`. | M3 | COMPLETED |
| M5 | E2E_Pass_And_Hardening | Execute E2E tests, resolve failures, and execute white-box adversarial testing (Tier 5) with Challengers. | M1, M4 | COMPLETED |
| M6 | Visual_Canvas_Integration | Evaluate and integrate GrapesJS in the Cora dashboard. | M5 | COMPLETED |
| M7 | AI_Layout_Engine | Implement server-side AJAX generation endpoint utilizing configured AI providers or fallback. | M6 | COMPLETED |
| M8 | Mobile_First_Frontend | Save designs in WordPress page meta and intercept template redirect to serve raw HTML/CSS. | M7 | COMPLETED |
| M9 | Cora_UI_Aesthetic | Implement settings drawers and monochrome visual styling for GrapesJS matching Cora UI rules. | M8 | COMPLETED |
| M10 | E2E_Verification | Build E2E test scripts, verify responsiveness on mobile, and audit code integrity. | M9 | COMPLETED |

## Interface Contracts
### Front-End AJAX Client ↔ WordPress Admin AJAX Server
AJAX calls from `admin-script.js` to `cora-real-estate.php`:
- Action hook suffix: `wp_ajax_cora_*`
- Content-Type: `application/x-www-form-urlencoded` or JSON payload.
- Response Structure:
  ```json
  {
    "success": true,
    "data": { ... }
  }
  ```
  or
  ```json
  {
    "success": false,
    "data": { "message": "Error description" }
  }
  ```

## Code Layout
- `cora-real-estate.php` - Plugin bootstrapping and AJAX action dispatching
- `admin-dashboard.php` - Workspace layout container
- `views/` - Module-specific template views (appearance, comments, media-editor, pages, settings-suite, tools)
- `assets/js/admin-script.js` - JS logic, event handlers, and stubs
- `assets/css/admin-style.css` - Theme styles
