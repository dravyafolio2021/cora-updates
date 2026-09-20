# Cora Platform — Module Progress & Branch Synchronization Manifest

> **Single Source of Truth for Multi-Branch Parallel Development**
> This file is maintained to ensure parallel agent execution and feature branches remain fully synchronized and never break shared platform APIs or files.

---

## 1. Branch Index & Active Modules

| Module Name | Branch Name | Status | Main Touchpoint Files | Assigned Agent / Chat |
|---|---|---|---|---|
| **Core Platform** | `main` | 🟢 Stable (v4.9.137) | `cora-workspace.php`, `admin-dashboard.php` | Main Orchestrator |
<!-- MODULE_ROWS_START -->
| **Client Management Suite** | `main` | 🟢 Complete & Active (v4.9.124) | `views/view-clients.php`, `views/view-financials.php` | CRM Client Suite Agent |
| **Client Task Manager (CRM Tasks)**| `main` | 🟢 Complete & Active (v4.9.124) | `views/view-client-task-manager.php`, `views/partials/partial-clients-kanban-tasks.php` | Task Management Agent |
| **Public White-Labeled Client Portal**| `main` | 🟢 Complete & Active (v4.9.122) | `public-client-portal.php` | Client Portal Agent |
| **Affiliate & Referral System** | `main` | 🟢 Complete & Active (v4.9.121) | `includes/affiliate-referral-engine.php`, `views/view-affiliate-referrals.php` | Growth & Affiliate Agent |
| **AI Co-Founder & Voice AI Overhaul**| `main` | 🟢 Complete & Active (v4.9.137) | `admin-dashboard.php`, `cora-workspace.php` | AI Orchestrator Agent |
| **CRM & Client Revenue Suite** | `feature/crm-module-suite` | 🟢 Merged to Main (v4.9.124) | `views/view-leads.php`, `views/view-financials.php`, `cora-workspace.php` | CRM Architecture Agent |
| **CRM & Lead Pipeline System** | `feature/crm-pipeline-next` | 🟢 Merged to Main (v4.9.118) | `views/view-leads.php`, `cora-workspace.php` | CRM Pipeline Agent |
| **Users & Role Governance** | `feature/industry-professional-services` | 🟢 Complete & Active (v4.9.108) | `views/view-users.php`, `cora-workspace.php` | RBAC & Governance Agent |
| **Sidebar & Nav Architecture** | `main` | 🟢 Complete & Active (v4.9.113) | `admin-dashboard.php`, `cora-workspace.php` | Navigation UX Agent |
| **App Modules (Feature Hub)** | `main` | 🟢 Complete & Active (v4.9.106) | `views/view-feature-hub.php`, `cora-workspace.php` | Feature Hub Agent |
| **Professional Services Vertical**| `feature/industry-professional-services`| 🟢 Complete & Active (v4.9.106) | `cora-workspace.php`, `views/*` | Industry Architecture Agent |
| **Agency Partner Ecosystem** | `feature/agency-partner-ecosystem` | 🟡 Active In-Progress | `cora-frontend/app/*`, `views/*`, `cora-workspace.php` | Agency Ecosystem Agent |
| **Executive 24h PDF Reports** | `feature/workspace-development-2026-09-12` | 🟢 Complete & Active (v4.9.103) | `cora-workspace.php`, `views/view-inventory-management.php` | Executive Reporting Agent |
| **Field Driver Chrome Stripping**| `feature/workspace-development-2026-09-12` | 🟢 Complete & Active (v4.9.102) | `admin-dashboard.php`, `admin-script.js`, `admin-style.css` | Security & Terminal Agent |
| **Stationery & Plant Inventory** | `feature/workspace-development-2026-09-12` | 🟢 Complete & Active (v4.9.103) | `includes/class-cora-inventory-engine.php`, `views/view-inventory-management.php` | Supply Chain & Inventory Agent |
| **Field Sales Driver Isolation** | `feature/workspace-development-2026-09-12` | 🟢 Complete & Active (v4.9.103) | `views/view-inventory-management.php`, `views/setup-account.php`, `admin-dashboard.php` | RBAC & Security Agent |
| **Dashboard & Nav Customizer** | `feature/workspace-development-2026-09-12` | 🟢 Complete & Active (v4.9.103) | `admin-dashboard.php`, `admin-script.js`, `cora-workspace.php` | Telemetry & UX Customizer Agent |
| **Super Admin Console (v4.9.59)** | `main` | 🟢 Merged to Main | `views/view-super-admin.php`, `admin-dashboard.php` | Super Admin Agent |
| **Field Ops & Geolocation Tracker**| `main` | 🟢 Merged to Main | `assets/js/cora-field-ops-tracker.js`, `views/view-users.php` | Field Ops Agent |
| **Universal Website Migrator** | `main` | 🟢 Merged to Main | `includes/class-cora-html-website-migrator.php`, `views/view-canvas.php` | Canvas Migration Agent |
| **Dynamic AI Co-Founder** | `main` | 🟢 Merged to Main | `admin-dashboard.php`, `cora-workspace.php` | AI Co-Founder Agent |
| **Multimodal Team Migration**| `main` | 🟢 Merged to Main | `views/view-users.php`, `cora-workspace.php` | Team Onboarding Agent |
| **Voice AI Discussion** | `main` | 🟢 Merged to Main | `admin-dashboard.php`, `cora-workspace.php` | Voice AI Engine Agent |
| **Canvas Dual Builder** | `main` | 🟢 Merged to Main | `views/view-canvas.php`, `view-canvas-render.php` | Canvas Visual Engine Agent |
| **Forms & Reviews 2.0** | `main` | 🟢 Merged to Main | `views/view-forms.php`, `cora-workspace.php` | Forms 2.0 Agent |
| **Mobile & PWA Engine** | `main` | 🟢 Merged to Main | `admin-dashboard.php`, `cora-service-worker.js` | Mobile Resilience Agent |
| **Content AI Suite** | `main` | 🟢 Merged to Main | `views/view-content-suite.php` | Content Module Agent |
| **Document Vault** | `main` | 🟢 Merged to Main | `views/view-vault.php`, `cora-workspace.php` | Dedicated Vault Agent |
| **Media Proofing** | `main` | 🟢 Merged to Main | `views/view-media.php`, `views/view-media-editor.php`| Media Module Agent |
| **Finance AI Co-founder**| `main` | 🟢 Merged to Main | `views/view-financials.php`, `cora-workspace.php` | Finance AI Co-founder Agent |
| **Email Suite & Hostinger** | `main` | 🟢 Merged to Main | `views/view-emails.php`, `cora-workspace.php` | Email Module Agent |
| **Public Docs Portal** | `main` | 🟢 Merged to Main | `views/view-public-docs*.php`, `includes/docs-engine.php` | Docs Portal Agent |
<!-- MODULE_ROWS_END -->

---

## 2. Shared File Touchpoints & Conflict Guard

> [!IMPORTANT]
> If multiple feature branches modify any of the following shared files simultaneously, coordinators must review parameter signatures and line ranges to prevent merge conflicts:

- `app/public/wp-content/plugins/cora-workspace/cora-workspace.php` (Core AJAX Handlers, Micro-Cache, Schema & Hooks)
- `app/public/wp-content/plugins/cora-workspace/admin-dashboard.php` (Main Dashboard Controller, Mobile Island, Voice & AI Copilot UI)
- `app/public/wp-content/plugins/cora-workspace/public-client-portal.php` (Public White-Labeled Client Portal Engine)
- `app/public/wp-content/plugins/cora-workspace/includes/affiliate-referral-engine.php` (Affiliate Attribution, Dual-Rewards & Payouts Engine)
- `app/public/wp-content/plugins/cora-workspace/views/view-affiliate-referrals.php` (Affiliate Dashboard, Screener & Earnings Calculator)
- `app/public/wp-content/plugins/cora-workspace/views/view-clients.php` (4-Subtab Client Management Suite)
- `app/public/wp-content/plugins/cora-workspace/views/view-client-task-manager.php` (Client Task Manager Board & Resizable Floating Drawer)
- `app/public/wp-content/plugins/cora-workspace/views/partials/partial-clients-kanban-tasks.php` (Client Tasks Kanban Partial)
- `app/public/wp-content/plugins/cora-workspace/views/view-super-admin.php` (11-Tab Super Admin Suite & MRR Telemetry)
- `app/public/wp-content/plugins/cora-workspace/views/view-users.php` (Multimodal Team Migration & Field Ops Live Tracker)
- `app/public/wp-content/plugins/cora-workspace/views/view-canvas.php` (Dual-Engine Theme Builder, Migrator & Visual Editor)
- `app/public/wp-content/plugins/cora-workspace/views/view-forms.php` (Forms & Reviews 2.0 Engine & Settings Suite)
- `app/public/wp-content/plugins/cora-workspace/views/view-feature-hub.php` (Feature Hub & Module Management)
- `app/public/wp-content/plugins/cora-workspace/views/view-vault.php` (Document Vault & GST Invoicing)
- `app/public/wp-content/plugins/cora-workspace/views/view-content-suite.php` (Content AI Suite & Editor)

---

## 3. Branch Activity & Progress Log

### `main` (Production Base)
- **Platform Version**: `4.9.137`
- **Health**: 100% Operational & Clean Slate Base. Full regression and automated CRM/Inventory/E2E test suites verified ✅.

<!-- BRANCH_LOGS_START -->
### `feature/ai-drawer-voice-overhaul` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.125 - v4.9.137) — Dynamic AI Co-Founder & Voice AI Architecture Overhaul: 1-click `+ New Chat` action control in drawer header, action-oriented chat UI with generative cards and right-aligned user speech bubbles, footer mic button wired as direct Voice Mode switch, integrated Voice Settings tab, auto-suppressed mobile keyboard on voice triggers, compact 1-row in-drawer telemetry bar / 2-column pacing cards, monthly parity pacing, tier-based AI quota system with accordion expansion and high-z-index quota modal, strict 3px height constraint on progress bars, and universal background page scroll lock SOP.
- **Main Touchpoint**: `admin-dashboard.php`, `cora-workspace.php`.

### `feature/crm-module-suite` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.119 - v4.9.124) — CRM & Enterprise Client Revenue Suite: 4-subtab Client Management Suite (`views/view-clients.php`), Client Task Manager with high-performance Kanban boards, real-time AJAX persistence, right-click context command menu, and resizable floating Task Details Drawer (`views/view-client-task-manager.php`, `views/partials/partial-clients-kanban-tasks.php`) featuring left-edge drag handle, 48px topbar anchor, rounded left arc (`rounded-l-2xl`), subtask checklist with progress bar, 4 dedicated drawer tabs (`Checklist`, `Scope`, `Assets`, `Activity`), and multi-industry dictionaries; automated sync between client contracts and financial overview ledger; Form Campaign Lead Intake Engine with anti-pollution routing; and 100% white-labeled mobile-first Public Client Portal (`public-client-portal.php`) with Anthropic Claude aesthetic (`#FBFaf7`).
- **Main Touchpoint**: `views/view-clients.php`, `views/view-client-task-manager.php`, `views/partials/partial-clients-kanban-tasks.php`, `public-client-portal.php`, `views/view-financials.php`, `cora-workspace.php`.

### `feature/affiliates-referral-system` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.115 - v4.9.121) — End-to-End Affiliate & Referral Engine: Dual-reward architecture (+100 AI credits on free signup, 40% recurring commission on paid subscriptions), 3-step partner enrollment screener flow before unlocking dashboard, geolocation-based annual-only pricing & commission matrix across 6 tiers, dedicated schema (`wp_cora_referral_links`, `wp_cora_referrals`, `wp_cora_affiliate_payouts`), 30-day attribution cookie, ₹1,000 minimum withdrawal via UPI / Bank Transfer, and official SVG vector share marks (WhatsApp, LinkedIn, X, QR code).
- **Main Touchpoint**: `includes/affiliate-referral-engine.php`, `views/view-affiliate-referrals.php`, `cora-workspace.php`.

### `feature/crm-pipeline-next` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.109 - v4.9.118) — CRM Lead Management & Sales Pipeline Overhaul: Ultra-compact 3-level lead cards with single-row quick outreach footer (1-tap WhatsApp, phone, email, stage progression menu), in-column micro-search & context sorting (Deal Value, Recency, Alphabetical), customizable pastel column tints (`bg-sky-50`, `bg-amber-50`, `bg-purple-50`, `bg-emerald-50`) with live counter sync, unified independent multi-filter popover with active filter count badges, customizable decision-oriented top KPI scorecards (Total Pipeline Value, Won Revenue, Conversion Velocity, Active Leads), Dynamic Forms 2.0 to Leads Kanban bridge, AI Sales Call Synthesizer, and polished Lead Detail Drawer.
- **Main Touchpoint**: `views/view-leads.php`, `cora-workspace.php`.

### `feature/industry-professional-services` (Merged Branch)
- **Status**: 🟢 Complete & Active (v4.9.104 - v4.9.108) — Agency Team Governance & Dynamic Role Engine: Dynamic custom role creator, tenant-scoped permission matrix (`tab-roles`, `tab-permissions`) with sticky columns, desktop & mobile tab customization drawer (`tab-customizer`) with drag-and-drop reordering and visibility toggles, permanent team member deletion lifecycle, modern atomic mobile member/invite cards, high-contrast initials-based SVG avatars replacing gravatars, strict tenant branch isolation, and full Professional Services vertical (`professional_services`) roadmap.
- **Main Touchpoint**: `views/view-users.php`, `views/view-feature-hub.php`, `cora-workspace.php`.

### `feature/workspace-development-2026-09-12` (Active Feature Branch)
- **Status**: 🟢 Complete & Active (v4.9.60 - v4.9.103) — Single Consolidated 24-Hour Executive PDF Report & Anti-Spam Notification Engine (disarmed repetitive micro-event emails, routed all micro-events to in-app bell & PWA push alerts, consolidated master operational briefing delivered strictly once per 24h per owner, printable PDF report with digital verification seals, recipient deduplication and rate limit protection), Universal Driver Mode Real-Time Chrome Stripping (purged role simulation preview banner, stripped global search, bell, avatar, desktop sidebar, floating island, and mobile navigation drawer for drivers with `.cora-driver-mode-active`, 100% full-width dedicated Van POS terminal), Stationery Manufacturing & Plant Inventory Engine (Central Plant Command Center, 3-Step SKU Studio Drawer with live margin telemetry, Bulk CSV & Starter Kits, Consignment Van Dispatch with city route chips and Top 5 fast-selling auto-suggestions, Branded emails via Hostinger SMTP with 1x1 zero-cache tracking pixel and live open badges, Safe Restock Rollback on invoice/consignment deletion, Executive 24h Supply Recon & Loss Prevention Audit Engine, Mobile Van Sales POS terminal with live stock-on-wheels, spot sales billing, Gemini Vision receipt OCR, Day-End Return Settlement, Dedicated Field Sales Driver role `cora_field_vendor` with server-side terminal isolation and driver AI grounding, Dynamic Dashboard Analytics & Mobile Nav Customizer across 14 metrics and 16 platform modules, Universal Mobile & Desktop Body Scroll Lock System).
- **Main Touchpoint**: `includes/class-cora-inventory-engine.php`, `views/view-inventory-management.php`, `views/setup-account.php`, `admin-dashboard.php`, `admin-script.js`, `admin-style.css`, `cora-workspace.php`.

### `feature/super-admin-overhaul-mrr` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.59) — Super Admin mobile navigation overhaul, container isolation fixes, MRR & ARR telemetry suite, AI Master Token Pool, tenant capability matrix, emergency command center, forensics audit log stream, and global scroll bottom clipping fixes with flex spacers.
- **Main Touchpoint**: `views/view-super-admin.php`, `admin-dashboard.php`, `cora-workspace.php`.

### `feature/field-ops-geolocation-maps` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.58) — Field Ops & Geolocation Live Tracking Engine with stop/rest detection, dwell time telemetry, route replay, Touch Pan mode toggle, free high-definition multi-layer maps (Esri Satellite HD, Esri Streets, OpenStreetMap, CartoDB Dark), and strict Single Workspace Owner policy.
- **Main Touchpoint**: `assets/js/cora-field-ops-tracker.js`, `views/view-users.php`, `cora-workspace.php`.

### `feature/attendance-pwa-push-migration` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.57) — Migrated morning and evening team attendance reminders from email to interactive PWA push notifications and in-app alerts with 1-click check-in/out deep links.
- **Main Touchpoint**: `cora-workspace.php`, `cora-service-worker.js`.

### `feature/canvas-website-migrator` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.56) — Universal Website (HTML/CSS/JS) multi-page crawler and migrator engine with asset isolation, DOM sanitization, draft theme generation, and direct Visual HTML Editor synchronization.
- **Main Touchpoint**: `includes/class-cora-html-website-migrator.php`, `views/view-canvas.php`, `cora-workspace.php`.

### `feature/platform-tour-numeric-guard` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.56) — Restrict phone inputs to numeric digits with international dial code parsing across all forms, lead drawers, team manager, and client profiles. Deployed interactive monochromatic Platform Onboarding Tour System (`window.coraStartPlatformTour`) with pulsing DOM beacons, progress counter, and user meta state persistence.
- **Main Touchpoint**: `admin-dashboard.php`, `admin-script.js`, `admin-style.css`, `cora-workspace.php`.

### `feature/hostinger-smtp-relay` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.55) — Enforced default active Hostinger SMTP relay configuration out of the box for instantaneous transactional email delivery (lead notifications, OTPs, e-sign links).
- **Main Touchpoint**: `cora-workspace.php`.

### `feature/super-admin-isolation` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.54) — Super Admin Sidebar Menu Isolation: Dedicated Super Admin tools (Platform Analytics, Workspace Provisioning, Master AI Token Pool, Tenant Health) isolated from tenant workspace menus. Universal sign out engine invalidating session cookies and routing immediately to `/workspace/login`.
- **Main Touchpoint**: `admin-dashboard.php`, `cora-workspace.php`, `view-settings-suite.php`.

### `feature/security-url-masking` (Merged Branch)
- **Status**: 🟢 Merged to `main` (v4.9.52) — Security URL Masking: Masks `/wp-content/` to `/assets/` and `/wp-includes/` to `/core/` with native symlinks and `.htaccess` rewrite rules to hide WordPress internals.
- **Main Touchpoint**: `.htaccess`, `admin-dashboard.php`, `cora-workspace.php`, `manifest.json`.
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
| **v4.9.137** | Sep 2026 | Add 1-click + New Chat action control to AI drawer header, reset conversation state without page reload (`admin-dashboard.php`) |
| **v4.9.136** | Sep 2026 | Action-oriented chat UI with generative cards, structured markdown formatting, and right-aligned user speech bubbles |
| **v4.9.135** | Sep 2026 | Remove distracting outline and border from in-drawer tab selector for clean seamless visual integration |
| **v4.9.134** | Sep 2026 | Enforce strict 3px height on AI quota progress bar to eliminate vertical oval ballooning and maintain clean horizontal bar geometry |
| **v4.9.133** | Sep 2026 | Universal AI drawer background page scroll lock SOP (`coraLockScroll` / `coraUnlockScroll`) eliminating background viewport jitter |
| **v4.9.132** | Sep 2026 | Footer mic button wired as direct Voice Mode switch, auto-focus prevention, and shortened input placeholders |
| **v4.9.131** | Sep 2026 | In-drawer telemetry redesigned into compact 1-row block and resolved 0% percentage rounding glitch |
| **v4.9.130** | Sep 2026 | Redesign in-drawer telemetry to compact 2-column pacing cards and integrated voice settings tab |
| **v4.9.129** | Sep 2026 | Hide bottom input in Voice Mode and prevent mobile keyboard popup on voice mic triggers |
| **v4.9.128** | Sep 2026 | Refine in-drawer quota telemetry to monthly parity, minimal directional cards, and simplified model branding |
| **v4.9.127** | Sep 2026 | Implement in-drawer AI quota accordion expansion and airtight mobile scroll lock SOP |
| **v4.9.126** | Sep 2026 | Tier-based AI quota system, high-z-index quota modal, and universal drawer scroll lock SOP |
| **v4.9.125** | Sep 2026 | AI Co-Founder quick action presets converted to compact horizontal scroll rail |
| **v4.9.124** | Sep 2026 | Reduce side padding on Live Metrics container and cards |
| **v4.9.123** | Sep 2026 | Live metrics telemetry card hover effect, alignment, and currency vector icons |
| **v4.9.122** | Sep 2026 | Mobile warm cream background extended seamlessly to bottom of mobile screen with zero color seams |
| **v4.9.121** | Sep 2026 | Redesign PWA update notification into sleek top-right docked pill with dismiss action |
| **v4.9.120** | Sep 2026 | Add 3-step partner enrollment screener flow before unlocking affiliate dashboard |
| **v4.9.119** | Sep 2026 | End-to-end Affiliate & Referral System: dual-reward engine (+100 AI credits on free signup, 40% commission on paid plans), 6-plan pricing & commission matrix, geolocation-based annual pricing, and official vector share marks (WhatsApp, LinkedIn, X, QR code) |
| **v4.9.118-CRM** | Sep 2026 | Client Management Suite (4 subtabs) & Client Task Manager with Kanban pipeline, right-click command menu, and resizable floating Task Details Drawer with left arc, subtask checklist & multi-industry dictionaries |
| **v4.9.118** | Sep 2026 | Polish high-density Kanban lead card layout with ultra-compact single-row action footer (1-tap WhatsApp, phone, email, stage progression context menu), live column lead counter & deal value synchronization, and release package updates |
| **v4.9.117** | Sep 2026 | Simplify filter dropdown into a clean, independent multi-select popover with dynamic live filter badges, enforce 2 core CRM tabs (Pipeline & Analytics), and optimize real-time card filtering |
| **v4.9.116** | Sep 2026 | Unify toolbar multi-filters, introduce in-column micro-search & context sorting (Deal Value, Recency, Alphabetical), and apply customizable subtle pastel column tints (`bg-sky-50`, `bg-amber-50`, `bg-purple-50`, `bg-emerald-50`) across Kanban stages |
| **v4.9.115** | Sep 2026 | Decision-oriented analytics & customizable top KPI scorecards (Total Pipeline Value, Won Revenue MTD/QTD, Conversion Velocity, Active Opportunities), compact scorecard styling, and KPI selector drawer |
| **v4.9.114** | Sep 2026 | Streamline leads action bar, modernize customize columns drawer into global design system with drag-and-drop column management |
| **v4.9.113** | Sep 2026 | Reorganize Finance into CRM sidebar group and Forms into Workspace Foundation group across all industry modules for unified revenue and pipeline tracking |
| **v4.9.112** | Sep 2026 | Relocate Interactive Calendar into CRM navigation group across all industry modules for seamless shoot, showing, and appointment scheduling |
| **v4.9.111** | Sep 2026 | Standardize leads page header with global workspace header, interactive AI brand stack, and responsive filter trigger |
| **v4.9.110** | Sep 2026 | Establish CRM as an independent first-class sidebar navigation group across all industry modules |
| **v4.9.109** | Sep 2026 | Dynamic Forms to Leads Kanban bridge (instant inbound lead generation), AI Sales Call Synthesizer extracting pain points, deal size, sentiment, and next actions, and AI morning briefing integration |
| **v4.9.108** | Sep 2026 | Hardened mobile drawer sheet lifecycle, optimized mobile team card layout, and responsive touch gestures |
| **v4.9.107** | Sep 2026 | Permanent team member deletion with cascade unassignment and zero orphaned records, modernize active team members mobile card UI, and replace mystery gravatars with high-contrast initials-based SVG avatars |
| **v4.9.106** | Sep 2026 | Dynamic role creation engine, tenant-scoped permission matrix (`tab-roles`, `tab-permissions`), strict workspace owner gatekeeping, and scope workspace locations strictly by tenant |
| **v4.9.105** | Sep 2026 | Desktop & mobile tab customization drawer (`tab-customizer`) with drag-and-drop reordering, and sticky column permissions matrix |
| **v4.9.104** | Sep 2026 | App Modules & Feature Hub comprehensive 14-module foundation matrix across 5 categories with category filter bar, reactive toggles, and navigation route decoupling |
| **v4.9.103** | Sep 2026 | Single consolidated 24-Hour Executive PDF Report delivered strictly once per 24 hours per owner; disarmed repetitive micro-event notification emails (SEO ranking alerts, attendance briefs, check-in pings, 0-task briefings) and routed 100% of micro-events to in-app Bell & PWA Web Push alerts; multi-agency deduplication and rate limit protections |
| **v4.9.102** | Sep 2026 | Purged role simulation preview banner (`#cora-role-preview-banner`) from driver views via server-side checks and guarded JS; minimal topbar with direct sign-out and complete island suppression for field drivers |
| **v4.9.101** | Sep 2026 | Universal driver mode real-time chrome stripping with `.cora-driver-mode-active` body class; dynamically hides global topbar search, notifications bell, profile avatar/popovers, and mobile island navigation; 100% full-screen POS terminal |
| **v4.9.100** | Sep 2026 | Field Sales Driver total terminal isolation; excluded desktop sidebar, top header search (`⌘K`), notifications, profile popovers, and mobile navigation drawers; driver-safe controller actions for Gemini Vision OCR bill scanning and shop visit GPS recording |
| **v4.9.99** | Sep 2026 | Complete Field Driver view isolation, plant inventory removal, mobile island navigation scoped strictly to Home and AI Sparkle, and strictly grounded Driver AI Copilot |
| **v4.9.98** | Sep 2026 | Dedicated Field Sales Driver role (`cora_field_vendor`), 1-step activation & 1-tap Google Sign-Up, server-side route guarding and perspective locking |
| **v4.9.97** | Sep 2026 | Branded van consignment dispatch emails via Hostinger SMTP, zero-cache 1x1 tracking pixel, real-time open status badges (🟢 Opened, 🟡 Sent, ⚪ Pending), WhatsApp brief & direct invite share suite |
| **v4.9.96** | Sep 2026 | Click unlocking & ReferenceError fix during IIFE initialization, bulletproof event delegation across all 84 inventory controller methods |
| **v4.9.93** | Sep 2026 | 3-card Dispatch Mobile Drawer with city chips, Top 5 fast-selling auto-suggestions, multi-product catalog picker modal, interactive quantity steppers, safe-area elevation |
| **v4.9.92** | Sep 2026 | Mobile navigation island customizer expanded to all 16 platform modules with real-time search, zero-match empty state, and title-cased preview pills |
| **v4.9.88** | Sep 2026 | 2x2 mobile KPI scorecard grid (70% vertical scroll savings), purposeful subtle state accents, 3-column mobile SKU cards |
| **v4.9.87** | Sep 2026 | Executive 24h Supply Recon & Daily Audit Engine, multi-route settlement, top SKU rankings, AI diagnostics, standalone print-ready PDF, WhatsApp share studio |
| **v4.9.86** | Sep 2026 | Editable & deletable spot billing invoices and consignments with automated safe stock restoration back to active van or central plant stock |
| **v4.9.81** | Sep 2026 | Unified full-height Studio Drawer architecture across all inventory modals positioned dynamically below topbar |
| **v4.9.79** | Sep 2026 | Smart toast placement & dynamic CTA collision avoidance, elevating notifications above open Studio Drawers and primary buttons |
| **v4.9.75** | Sep 2026 | 3-step numbered circular stepper, active step scoped styles, enterprise margin telemetry cards (Factory Margin, Retailer Spread, GST Liability, Net Base Price) |
| **v4.9.72** | Sep 2026 | Add/Edit Product 3-step bottom drawer (`Identity & Media` -> `Pricing & Margins` -> `Stock & Logistics`), drag-and-drop media upload, monochromatic delete confirmation |
| **v4.9.63** | Sep 2026 | Dynamic Dashboard Analytics & Mobile Navigation Customizer (14 KPI cards, 3 middle island slots, AJAX persistence) |
| **v4.9.61** | Sep 2026 | Dedicated staging environment app icon with 'STG' ribbon, PWA manifest sync (`CORA Staging`) via `cora_is_staging_env()` |
| **v4.9.60** | Sep 2026 | Stationery Manufacturing & Mobile Van Sales Inventory Engine initial release, single point of control plant command center, mobile field vendor mode, standardized `Inventory & Leads` sidebar grouping |
| **v4.9.59** | Sep 2026 | Super Admin mobile navigation overhaul, container isolation fixes, MRR telemetry suite, global scroll bottom clipping fix with flex spacers |
| **v4.9.58** | Sep 2026 | Field Ops & Geolocation Live Tracking with HD multi-layer maps (Esri Satellite, Esri Streets, OSM, CartoDB Dark), stop/rest detection, route replay engine, Touch Pan mode, and strict Single Workspace Owner policy |
| **v4.9.57** | Sep 2026 | Migrated morning/evening attendance reminders from email to interactive PWA push notifications and in-app alerts |
| **v4.9.56** | Sep 2026 | Universal Website (HTML/CSS/JS) multi-page crawler & migrator engine, numeric phone input validation, interactive Platform Onboarding Tour |
| **v4.9.55** | Sep 2026 | Enforced default active Hostinger SMTP relay configuration for instantaneous transactional email delivery |
| **v4.9.54** | Sep 2026 | Super Admin Sidebar Menu Isolation, administrative analytics dashboard, and universal sign-out handler |
| **v4.9.52** | Sep 2026 | Security URL masking: `/wp-content/` masked to `/assets/` and `/wp-includes/` masked to `/core/` via symlinks and rewrites |
| **v4.9.51** | Sep 2026 | Media Proofing folder header breadcrumb file count sync, mobile folder header polish, auto-chat toast silencing |
| **v4.9.50** | Sep 2026 | Replaced legacy `javascript:void(0)` with clean semantic RESTful URLs across all navigation links |
| **v4.9.49** | Sep 2026 | Public Canvas theme routing isolation preventing subpage collision with workspace dashboard views |
| **v4.9.48** | Sep 2026 | Overhauled real-time speech transcription & duplex TTS audio engine with barge-in interruption detection |
| **v4.9.46** | Sep 2026 | Enforced strict tenant-scoped RAG vector lookups and absolute multi-tenant database isolation for workspace AI |
| **v4.9.45** | Sep 2026 | Real-time live streaming speech transcription, natural Indian voice synthesis, and full-height voice canvas UI |
| **v4.9.44** | Sep 2026 | Multimodal Team Migration Hub with Vision OCR roster parsing, page-aware AI copilot, 24h memory auto-rotation, and AI Quota Hub |
| **v4.9.43** | Sep 2026 | Action-oriented AI Co-Founder with bidirectional continuous self-learning RAG loop and multi-module action cards |
| **v4.9.42** | Sep 2026 | Unified Dynamic AI Co-Founder panel with dual text & voice mode, clean SVG icons, and quota progress showcase |
| **v4.9.38** | Sep 2026 | Visual HTML Canvas code-split editor with live DOM synchronization and URL edit state persistence (`?page_id=...&edit_mode=visual`) |
| **v4.9.32** | Sep 2026 | Dual-Engine Canvas Page Builder (Elementor + Visual HTML Lovable), inline contenteditable, clean HTML export, asset inventory scanner |
| **v4.9.30** | Sep 2026 | App Modules & Feature Hub with explicit save workflow, batch toggle controls, and tenant module registry |
| **v4.9.27** | Sep 2026 | Mobile click interception shield, skeleton dismissal engine, and 0ms touch latency removal |
| **v4.9.23** | Sep 2026 | Forms & Reviews 2.0 with AI Conversion Doctor, funnel analytics, WhatsApp Cloud API, and 26 hardened widgets |
| **v4.9.13** | Sep 2026 | Continuous Hands-Free AI Voice Discussion Engine with 4 personalities, auto-endpointing, and 9 regional dialects |
| **v4.9.0**  | Sep 2026 | Multi-Industry Engine expansion (Marketing Agency vertical), WordPress backend lockdown, and virtual URL masking |
| **v4.0.0**  | Aug 2026 | Major platform consolidation release uniting all workspace modules into a unified clean-slate main branch |

---

*Cora Platform Release Manifest v4.9.137 — Architecture & Engineering Team.*
