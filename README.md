# Cora Platform

> Multi-tenant SaaS workspace engine for Indian service agencies — Photography Studios, Real Estate Brokerages, Marketing Agencies, Stationery Manufacturing Plants & Professional Services.

---

## Overview

Cora is a full-stack, enterprise-grade WordPress-based SaaS platform that provides white-labeled, multi-tenant workspace dashboards. Each workspace operates as an isolated business environment equipped with Lead CRM & Pipeline Kanban, Content AI Suite with Bulk Operations, Financial Intelligence, Team Governance & Dynamic Custom Roles, Active-Only Equal AI Token Allocation, Field Ops & Geolocation Live Tracking with Beacon-backed telemetry, Universal Foundation Modules Locking, Media Proofing Portal with route interception and client telemetry, Multi-Dimensional Workspace Storage Footprint, Form AI Architect with 15 field types and live preview, Stationery Manufacturing & Field Van Sales POS, Single Consolidated 24-Hour Executive PDF Reporting, Visual Website Canvas, Universal Website Migrator, Continuous Hands-Free Voice AI, Multimodal Team Migration, Enterprise AI Safety Guardrails, and per-tenant module customization.

* **Current Version**: `v4.9.189`
* **Supported Verticals**: Photography Studio (`photography_studio`), Real Estate Brokerage (`real_estate`), Marketing Agency (`marketing_agency`), Stationery Manufacturing & Van Sales (`stationery_inventory` / `manufacturing`), Professional Services & Consulting Agency (`professional_services`), Custom Workspace (`custom-workspace`)
* **Tech Stack**: WordPress 6.x (Locked Down Backend), PHP 8.2+, Tailwind CSS (Monochromatic Zinc Ramp), JavaScript (ES6+), Leaflet.js, Next.js, Quill.js, Elementor, Sandboxed Visual HTML Engine
* **AI Providers**: Google Gemini 3.5 Flash / Pro Multimodal, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o

---

## Architecture

```
cora/
├── app/public/wp-content/plugins/
│   ├── cora-workspace/          # Core platform plugin (v4.9.189)
│   │   ├── admin-dashboard.php  # Main dashboard controller, navigation & dynamic routing
│   │   ├── cora-workspace.php   # Core AJAX handlers, hooks, DB schema, micro-cache, RAG & AI Safety
│   │   ├── share-media.php      # Public media proofing portal with route interception & telemetry
│   │   ├── public-client-portal.php # Public white-labeled client portal with Claude aesthetic
│   │   ├── includes/            # Backend engines (affiliates, inventory, docs, RAG, MCP, PWA, tour, migrator)
│   │   ├── modules/             # Modular industry domain engines & feature definitions
│   │   ├── views/               # 50+ modular PHP view files (Clients, Forms, Media, Tasks, Users, etc.)
│   │   └── assets/              # JS (Field Ops Tracker, Voice AI, UI), CSS, dynamic versioned icons
│   ├── cora-real-estate/        # Real estate industry extension
│   ├── cora-studio-ai/          # Photography studio extension
│   └── cora-frontend/           # Marketing frontend module
├── docs/                        # Master technical documentation suite
├── tests/                       # Playwright E2E test suites (Tier 1-4)
├── scripts/                     # Build, deploy, and account provisioning scripts
├── updates/                     # Release artifacts (.zip + .json manifests)
└── .agents/                     # AI agent configuration & workspace rules
```

---

## Core Modules & Capabilities

| Module | Primary View | Description |
| :--- | :--- | :--- |
| **Workspace Dashboard** | `admin-dashboard.php` | Adaptive workspace landing with centered 60% max-width KPI scorecards (guaranteed 4 cards: 2x2 mobile, 1x4 desktop), moving purple gradient AI pulse trigger, dynamic user display name resolution, and Interactive Platform Tour |
| **CRM & Lead Pipeline** | `view-leads.php` | Kanban pipeline with ultra-compact 3-level cards, 1-tap outreach footer (WhatsApp/Phone/Email), in-column search & sort, pastel column tints, unified multi-filter popovers, decision analytics, and AI call synthesizer |
| **Client Management Suite** | `view-clients.php` | 4-subtab client management hub with Overview/Directory, Active Projects & Deals, Client Tasks (Kanban), and automated Financials ledger sync |
| **Client Task Manager (CRM Tasks)** | `view-client-task-manager.php` | High-performance Kanban boards with real-time AJAX persistence, right-click command menu, resizable floating Task Details Drawer with rounded left arc, and past-time scheduling validation with auto-computed upcoming slots |
| **Public Client Portal** | `public-client-portal.php` | 100% white-labeled mobile-first portal with Anthropic Claude aesthetic (`#FBFaf7`), milestones, deliverables proofing vault, UPI/card checkout, and official vector payment marks |
| **Affiliate & Referral System** | `view-affiliate-referrals.php` | End-to-end referral engine with dual-rewards (+100 AI credits on free signup, 40% commission on paid plans), 3-step partner enrollment screener, 6-plan pricing & commission matrix, and UPI/Bank payout requests |
| **Users & Role Governance** | `view-users.php` | Dynamic custom role builder with real module titles, permissions strictly filtered to active workspace modules, active-only equal AI token allocation, desktop & mobile tab customizer, initials avatars, and safe role deletion |
| **App Modules (Feature Hub)**| `view-feature-hub.php`| Streamlined 24-module directory with Universal Foundation Modules Lock (`blogs`, `forms`, `team-roles`, `media`, `vault` + `dashboard`) across all industry modules, immutable lock badges, single-column horizontal mobile cards, unified search & control bar, and Rule 13 zero-outline styling |
| **Interactive Calendar** | `view-calendar.php` | Unified scheduling for bookings, showings, and milestones situated in the independent CRM sidebar group |
| **Financial AI Co-founder**| `view-financials.php` | Multi-tenant cash ledger, 30-day runway projections, deal feasibility simulator, and real-time reconciliation metrics for client settlements |
| **Dashboard & Nav Customizer** | `admin-dashboard.php` | Personalize 14 KPI telemetry scorecards with dynamic industry filtering, Rule 13 zero-outline tonal cards, and customize the 3 middle mobile island slots across all 16 platform modules |
| **Stationery & Van Inventory** | `view-inventory-management.php` | Dual-mode plant command center and mobile field van terminal with spot billing, GST math, multimodal OCR, and 24h recon |
| **Executive 24h PDF Reports** | `view-inventory-management.php` | Single consolidated 24-Hour Executive PDF Report delivered strictly once per 24 hours; pure in-app/push alerts for micro-events |
| **Field Driver Isolated POS** | `view-inventory-management.php` | 100% full-width dedicated Van POS terminal with total chrome stripping (`.cora-driver-mode-active`) and grounded driver AI |
| **Cora AI Assistant (Dynamic Co-Founder)** | `admin-dashboard.php` | Standardized "CORA AI" assistant with modern sparkle vector icon, hamburger chat history drawer (`#cora-ai-history-drawer`), 1-2 line concise conversational SOP, generative action cards, mobile rich text cards with 1-click blog draft generator, balanced brace action tag parsing (`cora_ai_extract_balanced_json`), and tier-based quota telemetry |
| **Enterprise AI Safety Guardrails** | `cora-workspace.php` | Enterprise policy enforcement engine (`wp_cora_security_incidents`) scanning for 6 violation categories (weapons, violence, nudity, religious defamation, self-harm, jailbreaks) with automated dual-escalation to Platform Super Admins and Workspace Owners, plus RBAC action execution validation |
| **Voice AI Discussion & Mode Switch** | `admin-dashboard.php` | Real-time continuous duplex voice engine with footer mic Voice Mode switch, integrated Voice Settings tab, auto-suppressed mobile keyboard, and 9 regional Indian dialects |
| **Multimodal Team Migration**| `view-users.php` | AI-powered roster OCR ingestion (PDF/PNG/JPG), automatic role mapping, and 1-click batch team provisioning |
| **Field Ops & Live Tracking**| `views/view-users.php`, `cora-field-ops-tracker.js` | Live GPS tracking with auto-start on login session detection, heartbeat pings, auto-stop with `navigator.sendBeacon` flush on logout, stop/rest detection, velocity telemetry, route replay, and free HD multi-layer maps |
| **Content AI Suite** | `view-content-suite.php`| 7-dashboard content lifecycle engine with bulk actions engine (floating selection toolbar for bulk status, category, export, delete), fixed-width dropdowns, responsive 3-column mobile Opportunities grid, sticky flush sub-tabs, and 1-click blog draft generator |
| **Dual-Engine Canvas** | `view-canvas.php` | Dual website builder: Elementor White-Label + In-Browser Visual HTML Editor with URL edit state persistence |
| **Universal Website Migrator**| `view-canvas.php` | 1-click multi-page crawler scraping external HTML/CSS/JS sites into editable draft themes |
| **Forms & Reviews 2.0 (Form AI Architect)** | `view-forms.php` | Full-featured Form AI Architect with complete CRUD lifecycle, 15 field types, multi-step wizards, live full-height preview modal, top control panel isolation, 3-metric stage funnel analytics, and dual-mode responsive data cards (table on desktop, activity cards on mobile) |
| **Media Proofing & Telemetry** | `view-media.php`, `share-media.php` | Studio-grade asset management with crop presets, public shared media route interception (`/workspace/shared-media/{token}`) with Claude cream aesthetic, Total/Unique views and download telemetry tracking, filter chips, and access log audit trail |
| **Multi-Dimensional Storage Footprint** | `view-media.php`, `cora-workspace.php` | Dynamic workspace storage calculator encompassing media files, vault contracts, AI chat history/vectors, and user activity/attendance telemetry with visual UI category breakdown |
| **System Settings Suite** | `view-settings-suite.php` | Multi-tab settings suite with persistent notification channel toggles (Email, WhatsApp, Push) and trigger matrix across reloads, sticky sub-tabs with `pan-x` smooth touch swipe |
| **Sidebar & Nav Architecture** | `admin-dashboard.php`, `cora-workspace.php` | Defensive navigation shield with universal role permissions fallback (`cora_filter_sidebar_nav_by_role`) preventing empty menu groups |
| **Document Vault** | `view-vault.php` | GST-compliant invoicing (CGST/SGST/IGST, SAC 9983) with SHA-256 legal e-sign audit registry |
| **Crew & Team Scheduler** | `view-crew-scheduler.php`| Timeline-based shift scheduling, crew allocation, and dispatch management |
| **Equipment & Listings** | `view-equipment.php` | Camera gear custody tracking / Geocoded real estate inventory |
| **Super Admin Console** | `view-super-admin.php` | 11-tab administrative control center for MRR telemetry, tenant health, global AI tokens & emergency controls |
| **Developer Docs Portal** | `view-public-docs.php` | 3-column public documentation portal with AI Playground and Command Palette (`/docs`) |

---

## Design System & UX Standards

1. **Pure Light Mode**: Zero dark mode for instant splash rendering and strict visual continuity (`#ffffff` / `zinc-50` through `zinc-950`).
2. **Monochromatic Neutral Palette**: Notion/Shopify-inspired zinc color ramp with color accents strictly bound to functional states (🟢 Active, 🟡 Pending, 🔴 Critical).
3. **Rule 13: Zero-Outline Tonal Surface Selection Architecture**: Heavy bounding box outlines, high-contrast dark border strokes (`border-zinc-900`, `border-black`, `ring-2`), and harsh perimeter borders are strictly prohibited. Active, selected, and focused items MUST use soft monochromatic tonal fills (`bg-zinc-100/90 dark:bg-zinc-800/80`) with subtle, uniform structural borders (`border-zinc-200/80`).
4. **Sticky Sub-Navigation Tabs SOP (~36px Sleek Height)**: Sub-navigation bars (Forms, Content Suite, Settings, Users) adhere to a compact ~36px height with smooth transitions on scroll. On mobile viewports, tabs are flush (`left: 0`, `px-0`, zero top margin) to eliminate offset clipping under the topbar.
5. **Mobile Sheet & Drawer SOP**: Zero mobile side drawers. All action sheets, creators, and filters open as **bottom slide-up sheets** (`translate-y-full` to `translate-y-0`) with drag handles and spring easing.
6. **Top-Down Floating Toasts (Mobile) / Dynamic Offset (Desktop)**: Monochromatic notification feedback (`window.coraShowToast`) floating top-center on mobile and elevated above Studio Drawers on desktop.
7. **Universal Body Scroll Lock SOP**: `window.coraLockScroll()` & `window.coraUnlockScroll()` with scrollable drawer container opt-in (`.cora-drawer-scrollable`) eliminating background page jitter across all modals, drawers, and the AI panel.
8. **Native Fluid Touch Scroll & Zero Synthetic Reloads**: Native mobile scrolling preserved across all viewports; synthetic pull-to-refresh JavaScript engines are strictly prohibited to prevent false reloads and jitter during scroll.
9. **0ms Touch Latency**: `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` applied across all interactive controls.
10. **Desktop Centered KPI Scorecards (Max 60% Width)**: Main dashboard metrics are constrained to a maximum 60% container width and centered horizontally on desktop viewports, guaranteeing a 1x4 row on desktop and 2x2 grid on mobile viewports.
11. **Security URL Masking**: Direct rewrites and symlinks masking `wp-content` to `/assets/` and `wp-includes` to `/core/` to shield internal platform architecture.
12. **Semantic URL Navigation**: Clean RESTful navigation paths across all dashboard views (`/workspace/{subpage}`) replacing legacy JavaScript links.
13. **Dedicated Independent CRM Navigation Group**: CRM elevated to a first-class independent group housing Leads Pipeline, Clients, Tasks, Calendar, and Finance across all industry verticals.
14. **Strict Single Owner Policy**: One workspace owner per tenant with guarded role assignability.
15. **Strict Role & Terminal Scoping**: Dedicated Field Driver role (`cora_field_vendor`) with server-side route redirection, DOM container isolation, complete chrome stripping, and terminal-locked AI copilot.
16. **Anti-Spam & Single 24-Hour Executive Delivery**: Disarms repetitive micro-event notification emails; all transient events route to In-App Bell & Web Push, consolidating executive business summaries into a single printable PDF delivered strictly once every 24 hours.
17. **Sleek Docked PWA Update Notification**: Redesigned update pill notification docked top-right with dismiss action, seamless cache purging, and version synchronization.
18. **Resizable Floating Task Drawer**: Drag-to-resize left handle with persistence, anchored below 48px top navbar with rounded left arc (`rounded-l-2xl`), ambient shadow, and zero background backdrop overlay.
19. **Seamless Mobile Warm Cream Background**: Mobile viewport background extended seamlessly with warm cream tone to eliminate bottom-edge color seams on mobile devices.
20. **Official Brand Vector Marks**: Strict enforcement of official vector SVGs for payments (WhatsApp, Google Pay, PhonePe, Paytm) and social sharing (WhatsApp, LinkedIn, X, QR code), completely eliminating emojis and raster images.
21. **Full-Width Sticky Sub-Tabs Bar & `pan-x` Horizontal Touch Swipe**: Sub-navigation tabs span the full width of workspace content seamlessly without floating gaps, pin directly below the topbar on scroll, and support smooth horizontal touch scrolling with outline removal.
22. **Universal Foundation Modules Architecture**: The 5 core operational modules (`blogs`, `forms`, `team-roles`, `media`, `vault` + `dashboard`) are universally locked and permanently merged across all industry modules to protect core tenant infrastructure.
23. **Multi-Dimensional Workspace Storage Footprint**: Storage telemetry accounts for media uploads, vault PDFs, AI vector embeddings and transcripts, and activity/GPS audit logs with UI category breakdowns.
24. **Beacon-Backed Field Telemetry Lifecycle**: Field GPS tracking automatically starts upon verified user login and cleanly flushes pending coordinates via `navigator.sendBeacon` upon logout.
25. **Active-Only Equal AI Token Allocation**: Workspace monthly AI token credits are divided equally among active team members by default, strictly ignoring pending invitations or deactivated users.

---

## Local Development & Test Environment

### Prerequisites
* [Local by Flywheel](https://localwp.com/) or WordPress 6.x on PHP 8.2+
* Node.js 18+ and Playwright for E2E validation

### Quick Start
```bash
# Clone the repository
git clone https://github.com/dravyafolio2021/heycora.git cora
cd cora

# Provision local testing accounts
/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php scripts/setup_local_accounts.php
```

### Pre-Configured Test Accounts (`http://cora.local`)

| Workspace Archetype | Username / Email | Password | Direct URL |
| :--- | :--- | :--- | :--- |
| 🏡 **Real Estate Brokerage** | `re_owner`<br>`owner.realestate@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=real_estate](http://cora.local/workspace/dashboard?industry=real_estate) |
| 📸 **Photography Studio** | `studio_owner`<br>`owner.studio@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=photography_studio](http://cora.local/workspace/dashboard?industry=photography_studio) |
| 📈 **Marketing Agency** | `marketing_owner`<br>`owner.marketing@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=marketing_agency](http://cora.local/workspace/dashboard?industry=marketing_agency) |
| 🏭 **Stationery Manufacturing** | `stationery_owner`<br>`owner.stationery@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=stationery_inventory](http://cora.local/workspace/dashboard?industry=stationery_inventory) |
| 💼 **Professional Services Agency** | `prof_owner`<br>`owner.profservices@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=professional_services](http://cora.local/workspace/dashboard?industry=professional_services) |
| 🚚 **Field Sales Van Driver** | `driver_rohan`<br>`driver.rohan@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=stationery_inventory&subpage=plant_inventory&mode=vendor](http://cora.local/workspace/dashboard?industry=stationery_inventory&subpage=plant_inventory&mode=vendor) |
| 👑 **Platform Super Admin** | `cora_admin`<br>`admin@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard](http://cora.local/workspace/dashboard) |

---

## Testing & Quality Assurance

```bash
# Install Playwright test dependencies
npx playwright install

# Run full E2E test suite
npx playwright test
```

---

## Documentation Index

* [Master Technical Platform Documentation](docs/cora-platform-documentation.md)
* [Canvas & Frontend Module Documentation](docs/canvas-frontend-module.md)
* [Developer Feature & Optimization Guide](docs/DEVELOPER_FEATURE_GUIDE.md)
* [Local Test Credentials Directory](LOCAL_CREDENTIALS.md)
* [Platform Onboarding One-Pager](CORA_PLATFORM_ONBOARDING_ONE_PAGER.md)
* [Modules Status & Release Manifest](MODULES_STATUS.md)

---

*Cora Platform v4.9.189 — Architecture & Development Team.*
