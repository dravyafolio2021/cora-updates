# Cora Platform — Module Progress & Branch Synchronization Manifest

> **Single Source of Truth for Multi-Branch Parallel Development**
> This file is maintained to ensure parallel agent execution and feature branches remain fully synchronized and never break shared platform APIs or files.

---

## 1. Branch Index & Active Modules

| Module Name | Branch Name | Status | Main Touchpoint Files | Assigned Agent / Chat |
|---|---|---|---|---|
| **Core Platform** | `main` | 🟢 Stable (v4.9.32) | `cora-workspace.php`, `admin-dashboard.php` | Main Orchestrator |
<!-- MODULE_ROWS_START -->
| **Voice AI Discussion** | `main` | 🟢 Merged to Main | `admin-dashboard.php`, `cora-workspace.php` | Voice AI Engine Agent |
| **Canvas Dual Builder** | `main` | 🟢 Merged to Main | `views/view-canvas.php`, `view-canvas-render.php` | Canvas Visual Engine Agent |
| **Forms & Reviews 2.0** | `main` | 🟢 Merged to Main | `views/view-forms.php`, `cora-workspace.php` | Forms 2.0 Agent |
| **App Modules (Feature Hub)**| `main` | 🟢 Merged to Main | `views/view-feature-hub.php`, `cora-workspace.php` | Feature Hub Agent |
| **Mobile & PWA Engine** | `main` | 🟢 Merged to Main | `admin-dashboard.php`, `cora-service-worker.js` | Mobile Resilience Agent |
| **Content AI Suite** | `main` | 🟢 Merged to Main | `views/view-content-suite.php` | Content Module Agent |
| **Lead Management (CRM)**| `main` | 🟢 Merged to Main | `views/view-leads.php`, `cora-workspace.php` | Lead Suite Agent |
| **Document Vault** | `main` | 🟢 Merged to Main | `views/view-vault.php`, `cora-workspace.php` | Dedicated Vault Agent |
| **Media Proofing** | `main` | 🟢 Merged to Main | `views/view-media.php`, `views/view-media-editor.php`| Media Module Agent |
| **Finance AI Co-founder**| `main` | 🟢 Merged to Main | `views/view-financials.php`, `cora-workspace.php` | Finance AI Co-founder Agent |
| **Email Suite** | `main` | 🟢 Merged to Main | `views/view-emails.php`, `cora-workspace.php` | Email Module Agent |
| **Public Docs Portal** | `main` | 🟢 Merged to Main | `views/view-public-docs*.php`, `includes/docs-engine.php` | Docs Portal Agent |
<!-- MODULE_ROWS_END -->

---

## 2. Shared File Touchpoints & Conflict Guard

> [!IMPORTANT]
> If multiple feature branches modify any of the following shared files simultaneously, coordinators must review parameter signatures and line ranges to prevent merge conflicts:

- `app/public/wp-content/plugins/cora-workspace/cora-workspace.php` (Core AJAX Handlers, Micro-Cache, Schema & Hooks)
- `app/public/wp-content/plugins/cora-workspace/admin-dashboard.php` (Main Dashboard Controller, Mobile Island & Voice AI UI)
- `app/public/wp-content/plugins/cora-workspace/views/view-canvas.php` (Dual-Engine Theme Builder & Visual HTML Editor)
- `app/public/wp-content/plugins/cora-workspace/views/view-forms.php` (Forms & Reviews 2.0 Engine & Settings Suite)
- `app/public/wp-content/plugins/cora-workspace/views/view-feature-hub.php` (Feature Hub & Module Management)
- `app/public/wp-content/plugins/cora-workspace/views/view-vault.php` (Document Vault & GST Invoicing)
- `app/public/wp-content/plugins/cora-workspace/views/view-content-suite.php` (Content AI Suite & Editor)

---

## 3. Branch Activity & Progress Log

### `main` (Production Base)
- **Platform Version**: `4.9.32`
- **Health**: 100% Operational & Clean Slate Base. All branches merged into `main`. Full regression and E2E test suites verified ✅.

<!-- BRANCH_LOGS_START -->
### `feature/canvas-visual-html` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.31 - v4.9.32) — Dual-Engine Canvas Architecture: In-browser Visual HTML Editor inside isolated sandboxed iframe, real-time inline text editing (`contenteditable`), media asset inventory scanner (`renderHtmlInventoryList`), 1-click image replacement popover, clean HTML extraction (`getCleanIframeHtml`), AI element rewrites, Core Web Vitals & SEO optimization, and Theme Wizard dual-card selection.
- **Main Touchpoint**: `views/view-canvas.php`, `views/view-canvas-render.php`, `cora-workspace.php`.

### `feature/feature-hub` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.28 - v4.9.30) — Dynamic Module Enablement: Workspace-level module management (`view-feature-hub.php`), explicit save workflow with unsaved changes bar, batch toggle controls, state persistence to `cora_agency_modules_{agency_id}`, and scoped CSS namespacing.
- **Main Touchpoint**: `views/view-feature-hub.php`, `cora-workspace.php`.

### `feature/mobile-resilience` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.24 - v4.9.27) — Mobile Adaptive Floating Island & Touch Hardening: Consolidated bottom floating island navigation, strict prohibition of mobile side drawers, bottom slide-up sheets with drag handle and spring easing, click interception shield, skeleton dismissal engine, and 0ms tap latency.
- **Main Touchpoint**: `admin-dashboard.php`, `cora-service-worker.js`.

### `feature/voice-ai-engine` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.0 - v4.9.13) — Continuous Hands-Free AI Voice Copilot: Real-time discussion engine with auto-endpointing, dynamic speech synthesis, live chat feed, 4 curated voice personalities (Myra, Aarav, Vikram, Kavya), 9 regional dialects with transliteration sync, and situational awareness RAG.
- **Main Touchpoint**: `admin-dashboard.php`, `cora-workspace.php`.

### `feature/forms-module-2.0` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.3 - v4.9.23) — Forms & Reviews 2.0: Audit and hardening of 26 form widgets, AI Conversion Doctor with funnel drop-off analytics, multi-channel notification routing (WhatsApp Cloud API, SMTP, Webhooks), automation flows tab, and embed runtime modal.
- **Main Touchpoint**: `views/view-forms.php`, `cora-workspace.php`.

### `feature/multi-industry-engine` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.0) — Multi-Industry Architecture & WP Lockdown: Added Marketing Agency vertical alongside Photography Studio and Real Estate. Virtual clean URLs (`/workspace/*`) and complete `/wp-admin/` backend lockdown.
- **Main Touchpoint**: `cora-workspace.php`, `admin-dashboard.php`.
<!-- BRANCH_LOGS_END -->

---

## 4. Multi-Agent Development Protocol

1. **Before Starting Work on a New Module**:
   - Check `MODULES_STATUS.md` to see if your target shared files are currently being modified by another active branch.
   - Run `git checkout main` → `git pull` → `git checkout -b feature/<module-name>`.

2. **During Feature Development**:
   - Keep commits granular with prefix: `feat(<module>): ...` or `fix(<module>): ...`.
   - Update your branch status in this file before pushing or requesting merge.

3. **Before Merging to Main**:
   - Run `git diff main..feature/<module-name> --stat` to ensure only module-scoped files were touched.

---

## 5. Version History (Recent)

| Version | Date | Key Changes |
| :--- | :--- | :--- |
| **v4.9.32** | Sep 2026 | Dual-Engine Canvas Page Builder (Elementor + Visual HTML Lovable), inline contenteditable, clean HTML export, asset inventory scanner, AI element rewriting, and automated Core Web Vitals optimization |
| **v4.9.30** | Sep 2026 | App Modules & Feature Hub with explicit save workflow, batch toggle controls, and tenant module registry |
| **v4.9.27** | Sep 2026 | Mobile click interception shield, skeleton dismissal engine, and 0ms touch latency removal |
| **v4.9.23** | Sep 2026 | Forms & Reviews 2.0 with AI Conversion Doctor, funnel analytics, WhatsApp Cloud API, and 26 hardened widgets |
| **v4.9.13** | Sep 2026 | Continuous Hands-Free AI Voice Discussion Engine with 4 personalities, auto-endpointing, and 9 regional dialects |
| **v4.9.0**  | Sep 2026 | Multi-Industry Engine expansion (Marketing Agency vertical), WordPress backend lockdown, and virtual URL masking |
| **v4.8.11** | Aug 2026 | Performance optimizations, static micro-cache layer, and dynamic PWA manifest sync |
| **v4.0.0**  | Aug 2026 | Major platform release consolidating all workspace modules into a single unified clean-slate main branch |
| **v3.4.44** | Aug 2026 | Cora Finance AI Co-founder with 4 snapshot metrics, cash flow forecast, and GST invoicing |
| **v3.4.34** | Aug 2026 | Strict single workspace owner policy with transfer ownership workflow; Platform Super Admin badge for Studio Admin |

---

*Cora Platform Release Manifest v4.9.32 — Architecture & Engineering Team.*
