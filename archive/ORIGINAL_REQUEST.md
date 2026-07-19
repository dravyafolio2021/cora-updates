# Original User Request

## Initial Request — 2026-07-08T01:07:52+05:30

Ship v0.1 of the Cora Real Estate Platform as a modular, enterprise-grade WordPress SaaS plugin. The plugin replaces native WordPress admin interfaces with a mobile-first, highly responsive, monochromatic Studio Minimalist UI/UX covering both core WordPress administration and specialized real estate workflows.

Working directory: /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate
Integrity mode: development

## Requirements

### R1. Full-Suite Mobile Responsiveness & UI Polish
All WordPress replacement modules (`pages`, `comments`, `appearance`, `tools`, `media-editor`, `settings-suite`) and core Cora Real Estate modules must render seamlessly on mobile devices (375px and 430px viewport widths) and desktop. The UI must strictly follow the Studio Minimalist monochromatic aesthetic, using right-sliding side drawers for modals/forms, custom monochromatic toasts for feedback, and clean SVG iconography without any browser-native overlays (`alert()`, `confirm()`, `prompt()`).

### R2. End-to-End Functional Verification & Error Handling
All interactive workflows—including AJAX form submissions, right-sliding drawer controls, comment moderation actions, image canvas manipulation, and data export/erasure utilities—must execute smoothly without PHP syntax errors, JavaScript console errors, or unhandled server exceptions.

### R3. Production Plugin Packaging & Readiness
The complete plugin codebase must be cleanly structured, audited, and packaged as an installable WordPress plugin archive ready for agency SaaS onboarding.

## Acceptance Criteria

### Mobile & Visual UX
- [ ] No horizontal scrolling or broken flex/grid containers occur at mobile viewport widths (375px and 430px) across all plugin modules.
- [ ] 0 instances of browser-native `alert()`, `confirm()`, or `prompt()` exist across the codebase; all user feedback is routed through `window.coraShowToast()`.
- [ ] All modals and editing forms open as right-sliding side drawers (`translate-x-0` vs `translate-x-full`) preserving screen layout.

### Functional Integrity
- [ ] Running `php -l` across all `.php` files in the plugin directory reports 0 syntax errors.
- [ ] AJAX action handlers in `cora-real-estate.php` and front-end helper stubs in `admin-script.js` execute their respective operations without throwing runtime errors or failing silently.
- [ ] Agent-as-judge inspection verifies that all 6 WordPress core replacement modules open, display their respective metrics/tables, and trigger interactive drawers/toasts cleanly.

### Packaging
- [ ] All unnecessary temporary files, test artifacts, or debug logs are excluded from the core plugin structure.
- [ ] A production-ready plugin package (`cora-real-estate-v0.1.zip` or finalized directory structure) is generated in the workspace root without missing dependencies or broken asset references.

## Follow-up — 2026-07-08T08:01:56+05:30

Ship v0.1 of the Cora Real Estate Platform as a modular, enterprise-grade WordPress SaaS plugin. The plugin replaces native WordPress admin interfaces with a mobile-first, highly responsive, monochromatic Studio Minimalist UI/UX covering both core WordPress administration and specialized real estate workflows.

Working directory: /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate
Integrity mode: development

PREVIOUS PROGRESS:
- M2 (UI Polish), M3 (AJAX functionalities), M4 (Packaging) are complete.
- The zip package `cora-real-estate-v0.1.zip` has been generated in the plugin directory.
- 73/73 tests are passing.
- Focus: Complete Milestone M5 (E2E pass validation, hardening, finalizing the package, and verifying that the plugin loads and runs correctly in the local WordPress environment).

Please inspect current status, run the E2E validation suite to verify the code, perform final hardening, and output a confirmation of readiness.

## Follow-up — 2026-07-08T08:30:20+05:30

Hi, the user just modified `cora-real-estate.php` to fix a broken include: changing `public-portfolio-view.php` to `public-gallery-view.php` (which is the actual file name present in the folder). Please ensure the zip package `cora-real-estate-v0.1.zip` is regenerated to include this fix before concluding Milestone M5.

## Follow-up — 2026-07-08T15:21:26+05:30

Add advanced real estate listing management, 3rd-party portal syncing, and a comprehensive lead capture/management workflow to the Cora Real Estate Platform, prioritizing a mobile-first, non-technical user experience.

Working directory: /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate
Integrity mode: development

## Requirements

### R1. Comprehensive Lead Capture Pipeline
Build a native lead capture and management workflow. This must include a custom-built WordPress form module (do NOT use Contact Form 7, WPForms, etc.), a webhook ingestion endpoint for external platforms (e.g., Ads Manager, 99acres), and a manual entry interface for agents.

### R2. 3rd-Party Listing Sync
Implement a listing management system that allows a non-technical user to sync properties with 3rd-party portals simply by adding a profile or listing link. The agent team is free to decide the best technical approach (API, scraping, or hybrid) provided it does not cause technical or legal conflicts.

### R3. AI-Driven SEO Optimization
Integrate AI/SEO features designed to make listings rank higher in search results. This should automatically generate or optimize listing titles, descriptions, and meta tags based on the property details provided.

### R4. Mobile-First "Studio Minimalist" UI
All new modules (Lead Management, Listing Sync, AI SEO) must strictly adhere to the existing mobile-first "Studio Minimalist" aesthetic. Use right-sliding drawers for forms, custom monochromatic toasts for feedback, and ensure flawless rendering on mobile viewports.

## Acceptance Criteria

### Functional Integrity
- [ ] A custom frontend form can be embedded and successfully captures a lead into the system.
- [ ] A dedicated webhook endpoint exists and successfully processes a simulated JSON payload from an external ad platform.
- [ ] Entering a dummy 3rd-party profile link triggers the listing sync process without unhandled server errors.
- [ ] Saving a listing automatically triggers the AI/SEO optimization logic to populate meta titles and descriptions.

### Mobile & Visual UX
- [ ] No horizontal scrolling or broken containers occur at mobile viewport widths (375px and 430px) across all new modules.
- [ ] 0 instances of browser-native `alert()`, `confirm()`, or `prompt()` exist in the new codebase; all feedback uses the custom toast system.

### Stability
- [ ] Running `php -l` across all newly created/modified `.php` files reports 0 syntax errors.
- [ ] E2E or programmatic tests confirm that the lead pipeline and listing sync execute without fatal errors.
