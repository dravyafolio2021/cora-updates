# Original User Request

## Initial Request — 2026-07-08T00:38:12Z

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

## Follow-up — 2026-07-08T06:11:48+05:30

Ship v0.1 of the Cora Real Estate Platform as a modular, enterprise-grade WordPress SaaS plugin. The plugin replaces native WordPress admin interfaces with a mobile-first, highly responsive, monochromatic Studio Minimalist UI/UX covering both core WordPress administration and specialized real estate workflows.

Working directory: /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate
Integrity mode: development

PREVIOUS PROGRESS: The prior run made partial progress on M2 (UI Polish) and M3 (AJAX Functionalities). Files already modified include: assets/js/admin-script.js, views/view-settings-suite.php, views/view-tools.php, views/view-pages.php, cora-real-estate.php. Read these files to understand current state before continuing.

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

## 2026-07-08T10:49:49Z

Build an AI-driven, visual canvas-based frontend page builder for WordPress, completely bypassing the standard block editor to deliver fast, mobile-first pages natively integrated with the Cora admin dashboard.

Working directory: /Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-real-estate
Integrity mode: development

## Requirements

### R1. Visual Canvas Integration
Evaluate and integrate a robust open-source visual canvas library (e.g., GrapesJS, or similar) into the Cora admin dashboard. This will serve as the primary frontend page builder, bypassing the native WordPress block editor.

### R2. AI Prompt-to-Layout Engine
Implement an AI-driven engine that allows non-technical users to generate entire page structures and layouts directly within the canvas by submitting a simple text prompt (e.g., "Build a landing page for a luxury villa").

### R3. High-Performance, Mobile-First Rendering
The final published pages must be optimized for speed and mobile responsiveness. The system should generate raw, lightweight HTML/CSS (e.g., utilizing Tailwind CSS or similar utility frameworks) and bypass heavy WordPress theme constraints to ensure maximum frontend performance.

### R4. Cora UI Aesthetic
The builder interface itself must seamlessly blend with the Cora "Studio Minimalist" aesthetic, utilizing right-sliding side drawers for settings and custom monochromatic toasts for alerts.

## Acceptance Criteria

### Functional Integrity
- [ ] A dedicated "Visual Builder" tab exists in the Cora admin dashboard and successfully loads the integrated canvas library.
- [ ] A user can submit a text prompt and the canvas automatically populates with a structured layout based on that prompt.
- [ ] Publishing a design from the canvas generates an accessible frontend URL serving clean HTML/CSS.

### Mobile & Visual UX
- [ ] The published frontend page renders flawlessly without horizontal scrolling or broken containers at a 375px mobile viewport.
- [ ] The builder interface uses 0 instances of browser-native `alert()`, `confirm()`, or `prompt()`; all feedback uses the custom toast system.

### Stability
- [ ] Running `php -l` across all newly created/modified `.php` files reports 0 syntax errors.
- [ ] E2E or programmatic tests confirm that the visual builder loads and a layout can be generated without console errors or fatal server errors.

