# Cora Platform — Module Progress & Branch Synchronization Manifest

> **Single Source of Truth for Multi-Branch Parallel Development**
> This file is automatically updated by the `module-sync-monitor` subagent to ensure parallel chats and feature branches remain fully synchronized and never break shared platform APIs or files.

---

## 1. Branch Index & Active Modules

| Module Name | Branch Name | Status | Main Touchpoint Files | Assigned Agent / Chat |
|---|---|---|---|---|
| **Core Platform** | `main` | 🟢 Stable (v4.8.11) | `cora-workspace.php`, `view-settings-suite.php` | Main Orchestrator |
<!-- MODULE_ROWS_START -->
| **Email Management** | `main` | 🟢 Merged to Main | `views/view-emails.php`, `cora-workspace.php` | Email Module Agent |
| **File Manager** | `main` | 🟢 Merged to Main | `views/view-vault.php`, `cora-workspace.php` | Dedicated Vault Agent |
| **Forms & Reviews** | `main` | 🟢 Merged to Main | `views/view-forms.php`, `public-form-view.php`, `views/view-emails.php` | Dedicated Forms Agent |
| **Media Module** | `main` | 🟢 Merged to Main | `views/view-media.php`, `views/view-media-editor.php`, `cora-workspace.php` | Media Module Agent |
| **Studio Module** | `main` | 🟢 Merged to Main | `cora-workspace.php`, Studio Views & Features | Studio Module Agent |
| **Content Module** | `main` | 🟢 Merged to Main | `views/view-content-suite.php` | Content Module Agent |
| **Content Module v2** | `main` | 🟢 Merged to Main | `views/view-content-suite.php`, `admin-dashboard.php` | Content Editor Agent |
| **Lead Management** | `main` | 🟢 Merged to Main | `views/view-leads.php`, `cora-workspace.php`, `admin-dashboard.php` | Lead Suite Agent |
| **Frontend Module** | `main` | 🟢 Merged to Main | `cora-frontend/index.html`, `CORA_PLATFORM_ONBOARDING_ONE_PAGER.md` | Frontend Module Agent |
| **Public Docs Portal** | `main` | 🟢 Merged to Main | `views/view-public-docs*.php`, `includes/docs-engine.php` | Docs Portal Agent |
| **PWA Module** | `main` | 🟢 Merged to Main | `admin-dashboard.php`, `cora-service-worker.js`, `cora-manifest.json` | PWA Module Agent |
| **Finance Module** | `main` | 🟢 Merged to Main | `views/view-financials.php`, `cora-workspace.php` | Finance AI Co-founder Agent |
<!-- MODULE_ROWS_END -->

---

## 2. Shared File Touchpoints & Conflict Guard

> [!IMPORTANT]
> If multiple feature branches modify any of the following shared files simultaneously, coordinators must review parameter signatures and line ranges to prevent merge conflicts:

- `app/public/wp-content/plugins/cora-workspace/cora-workspace.php` (Core AJAX Handlers & Hooks, Universal Auto-Save Engine)
- `app/public/wp-content/plugins/cora-workspace/admin-dashboard.php` (Main Dashboard Controller & Routing)
- `app/public/wp-content/plugins/cora-workspace/views/view-emails.php` (Email Center View & Outbox Composer)
- `app/public/wp-content/plugins/cora-workspace/views/view-vault.php` (Document Vault & Document Studio Wizard)
- `app/public/wp-content/plugins/cora-workspace/views/view-settings-suite.php` (Settings & Backup Views)
- `app/public/wp-content/plugins/cora-workspace/views/view-content-suite.php` (Content AI Suite & Editor)

---

## 3. Branch Activity & Progress Log

### `main` (Production Base)
- **Platform Version**: `4.8.11`
- **Health**: 100% Operational & Clean Slate Base. All branches merged into `main`. Full regression and E2E test suites verified ✅.

<!-- BRANCH_LOGS_START -->
### `feature/finance-ai-cofounder` (Merged Branch)
- **Status**: 🟢 Merged to `main` (Aug 15, 2026) — Workspace-level Tenant Isolation & Clean Empty States (v3.4.45): Replaced legacy mock data fallbacks with genuine tenant-scoped database queries (`agency_id = %d`), added clean empty states for new tenants across all tabs, dynamic JS data bridge for live Chart.js visualizations, and dynamic customizable categories.
- **Main Touchpoint**: `views/view-financials.php`, `cora-workspace.php`, `updates/cora-workspace.zip`.

### `feature/frontend-module` (Merged Branch)
- **Status**: 🟢 Merged to `main` (Aug 14, 2026) — Marketing Frontend Landing Page: High-converting single-section hero with monochromatic design, floating feature pills, cursor parallax dashboard preview, scroll-reveal animations, right-sliding Contact Drawer, custom Toast engine, and responsive mobile layout. Platform onboarding one-pager (`CORA_PLATFORM_ONBOARDING_ONE_PAGER.md`) added for AI agent review.
- **Main Touchpoint**: `cora-frontend/index.html`, `CORA_PLATFORM_ONBOARDING_ONE_PAGER.md`.

### `feature/studio-module` (Active Branch)
- **Status**: 🟡 Active — Studio Features, Studio Booking & Management, Equipment & Crew Integration, and Studio Suite workflows.
- **Main Touchpoint**: `cora-workspace.php`, `views/view-crew-scheduler.php`, Studio plugin assets & views.

### `feature/content-ai-copilot` (Merged Branch)
- **Status**: 🟢 Merged to `main` — Content AI Suite: Complete structural overhaul across all 7 tabs (v3.2.88), custom 32px padding (v3.2.87), consolidated Myra assistant, removed dark mode (v3.2.83), and locked portrait orientation (v3.2.85).
- **Main Touchpoint**: `views/view-content-suite.php`.

### `feature/content-module-v2` (Merged Branch)
- **Status**: 🟢 Merged to `main` — Content Editor v2: Quill WYSIWYG editor with sticky docked toolbar, slash command hints, document outline & metrics panel, mobile quick action bar, and landscape auto-rotate lock (v3.3.0 → v3.4.0).
- **Main Touchpoint**: `views/view-content-suite.php`, `admin-dashboard.php`.

### `feature/email-management` (Merged Branch)
- **Status**: 🟢 Merged to `main` — Enterprise Studio Email Suite: Sub-Tabs, Live Preview Card, Template Studio, Drip Sequences, Outbox Logs Table, Hostinger SMTP Settings, and Diagnostic Drawers.
- **Main Touchpoint**: `views/view-emails.php`, `cora-workspace.php`.

### `feature/lead-management` (Merged Branch)
- **Status**: 🟢 Merged to `main` — Enterprise Lead Management Suite: Drag & drop Kanban funnel, searchable directory table, funnel & revenue analytics, sliding side drawers (deal details, create/edit lead, schedule follow-up), lead activity timeline, direct email outreach, and lead conversion to client.
- **Main Touchpoint**: `views/view-leads.php`, `cora-workspace.php`, `admin-dashboard.php`.

### `feature/document-studio-vault` (Merged Branch)
- **Status**: 🟢 Merged to `main` (commit `edc5d5a4`) — Document Vault Dashboard, 5-Step Guided Document Studio Wizard, E-Sign Audit Registry, Quality Health Check, GST math, reactive KPI cards, watermark preview.
- **Main Touchpoint**: `views/view-vault.php`, `cora-workspace.php`.

### `feature/forms-module` (Merged Branch)
- **Status**: 🟢 Merged to `main` (commit `121ba12f`) — Multi-channel review settings, WhatsApp-first automation rules with Hinglish presets, Notion-styled form inputs.
- **Main Touchpoint**: `views/view-forms.php`, `public-form-view.php`, `views/view-emails.php`.

### `feature/media-module` (Merged Branch)
- **Status**: 🟢 Merged to `main` (commit `e27bc7f3`) — Studio-grade Media Library Dashboard with folder navigation, search & MIME filtering, bulk toolbar, right-sliding detail sheet, folder email sharing, storage meter, dynamic aspect-ratio crop presets (1:1, 4:3, 16:9), transformations, and SEO metadata manager. Left sidebar segment tab controls and media card presets added in v3.4.0.
- **Main Touchpoint**: `views/view-media.php`, `views/view-media-editor.php`, `cora-workspace.php`.

### Public Docs Portal (Merged to Main)
- **Status**: 🟢 Merged — Three-column Notion-like developer documentation portal at `/docs` with AI Playground sidebar, command palette search (Cmd+K), AJAX page routing, and mobile-responsive layout (v3.2.90 → v3.4.0).
- **Main Touchpoint**: `views/view-public-docs*.php`, `includes/docs-engine.php`.

### PWA Module (Merged to Main)
- **Status**: 🟢 Merged — Dynamic PWA onboarding wizard, premium loading splash screen, portrait orientation lock with landscape shield overlay, service worker cache eviction engine, and in-app update banner (v3.2.73 → v3.3.0).
- **Main Touchpoint**: `admin-dashboard.php`, `cora-service-worker.js`, `cora-manifest.json`.
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
| v4.0.0  | Aug 25, 2026 | Major platform release consolidating all workspace modules into a single unified clean-slate main branch. Full multi-tenant isolation, dynamic data bridges, autonomous AI co-founder deck, and verified 100% E2E test passing baseline |
| v3.4.44 | Aug 14, 2026 | Cora Finance complete rebuild as an AI Co-founder: live briefing ('Cora's Take'), 4 snapshot metrics, 'Needs Your Attention' proactive cards, Money In (Receivables & AI follow-ups), Money Out (Recurring cost intelligence), Client Profitability & Project Feasibility simulator, 30/60/90-Day Cash Flow Forecast, GST estimates, Ask Cora AI copilot, and side drawer architecture |
| v3.4.43 | Aug 14, 2026 | Meta WhatsApp Cloud API gateway, 24h cost optimizer, settings tester; marketing landing page shipped |
| v3.4.42 | Aug 14, 2026 | Media mobile storage strip horizontal margin & padding fix |
| v3.4.41 | Aug 14, 2026 | Media storage indicator mobile optimization; in-dev sidebar items hidden |
| v3.4.40 | Aug 14, 2026 | Avatar online status indicator dot anchored bottom-right with SaaS-standard contrast border |
| v3.4.39 | Aug 14, 2026 | Version bump and manifest update |
| v3.4.38 | Aug 14, 2026 | Full workspace storage footprint computation including all media assets, documents, and generated variants |
| v3.4.37 | Aug 14, 2026 | Media layout void spacing fix; storage scope scoped to genuine workspace media |
| v3.4.36 | Aug 14, 2026 | Real-time workspace storage usage analytics in media library header |
| v3.4.35 | Aug 14, 2026 | Disabled state and click guards enforced on all system-locked inputs and permissions matrix rows |
| v3.4.34 | Aug 14, 2026 | Strict single workspace owner policy with transfer ownership workflow; Platform Super Admin badge for Shruti |
| v3.4.28 | Aug 14, 2026 | Mobile documentation responsive layout overhaul, left navigation sliding drawer transition, command palette styling fixes |
| v3.4.0  | Aug 12, 2026 | Sticky docked toolbar, collapsible AI assistant, slash hint, landscape auto-rotate lock, left sidebar segment tabs in media editor |
| v3.3.9  | Aug 11, 2026 | Document outline & metrics functional, atomic deploy backup fix |
| v3.3.7  | Aug 10, 2026 | Mobile quick action bar, horizontal scroll prevention |
| v3.3.3  | Aug 10, 2026 | PWA onboarding wizard, premium loading splash, portrait lock with landscape shield |
| v3.3.1  | Aug 9, 2026  | Calendar day number persistence fix |
| v3.3.0  | Aug 9, 2026  | Platform version bump, landscape shield overlay |
| v3.2.101 | Aug 8, 2026 | AI Playground embedded inline in docs portal |
| v3.2.88 | Aug 7, 2026 | Content Suite complete 7-tab structural overhaul |
| v3.2.83 | Aug 6, 2026 | Dark mode completely removed platform-wide |
