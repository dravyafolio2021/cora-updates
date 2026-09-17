# Cora Platform

> Multi-tenant SaaS workspace engine for Indian service agencies — Photography Studios, Real Estate Brokerages, Marketing Agencies & Stationery Manufacturing Plants.

---

## Overview

Cora is a full-stack, enterprise-grade WordPress-based SaaS platform that provides white-labeled, multi-tenant workspace dashboards. Each workspace operates as an isolated business environment equipped with Lead CRM, Content AI, Financial Intelligence, Team Scheduling, Field Ops & Geolocation Live Tracking, Stationery Manufacturing & Field Van Sales POS, Single Consolidated 24-Hour Executive PDF Reporting, Forms & Reviews 2.0, Visual Website Canvas, Universal Website Migrator, Continuous Hands-Free Voice AI, Multimodal Team Migration, and per-tenant module customization.

* **Current Version**: `v4.9.103`
* **Supported Verticals**: Photography Studio (`photography_studio`), Real Estate Brokerage (`real_estate`), Marketing Agency (`marketing_agency`), Stationery Manufacturing & Van Sales (`stationery_inventory` / `manufacturing`), Professional Services & Consulting Agency (`professional_services`)
* **Tech Stack**: WordPress 6.x (Locked Down Backend), PHP 8.2+, Tailwind CSS (Monochromatic Zinc Ramp), JavaScript (ES6+), Leaflet.js, Next.js, Quill.js, Elementor, Sandboxed Visual HTML Engine
* **AI Providers**: Google Gemini 3.5 Flash / Pro Multimodal, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o

---

## Architecture

```
cora/
├── app/public/wp-content/plugins/
│   ├── cora-workspace/          # Core platform plugin (v4.9.103)
│   │   ├── admin-dashboard.php  # Main dashboard controller & dynamic routing
│   │   ├── cora-workspace.php   # Core AJAX handlers, hooks, DB schema, micro-cache, RAG
│   │   ├── includes/            # Backend engines (inventory, docs, RAG, MCP, PWA, tour, website migrator)
│   │   ├── modules/             # Modular industry domain engines & feature definitions
│   │   ├── views/               # 50+ modular PHP view files
│   │   └── assets/              # JS (Field Ops, Voice AI, UI), CSS, dynamic versioned icons
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
| **Workspace Dashboard** | `admin-dashboard.php` | Adaptive workspace landing with bento grid KPIs, mobile floating island, and Interactive Platform Tour |
| **Dashboard & Nav Customizer** | `admin-dashboard.php` | Personalize 14 KPI telemetry scorecards and customize the 3 middle mobile island slots across all 16 platform modules |
| **Stationery & Van Inventory** | `view-inventory-management.php` | Dual-mode plant command center and mobile field van terminal with spot billing, GST math, multimodal OCR, and 24h recon |
| **Executive 24h PDF Reports** | `view-inventory-management.php` | Single consolidated 24-Hour Executive PDF Report delivered strictly once per 24 hours; pure in-app/push alerts for micro-events |
| **Field Driver Isolated POS** | `view-inventory-management.php` | 100% full-width dedicated Van POS terminal with total chrome stripping (`.cora-driver-mode-active`) and grounded driver AI |
| **Dynamic AI Co-Founder** | `admin-dashboard.php` | Unified dual-mode copilot (Chat & Voice) with bidirectional self-learning RAG loop and action cards |
| **Voice AI Discussion** | `admin-dashboard.php` | Real-time continuous duplex voice engine with streaming transcription, natural Indian voices & soundwave UI |
| **Multimodal Team Migration**| `view-users.php` | AI-powered roster OCR ingestion (PDF/PNG/JPG), automatic role mapping, and 1-click batch team provisioning |
| **Field Ops & Live Tracking**| `view-users.php` | Live GPS tracking, stop/rest detection, velocity telemetry, route replay, and free HD multi-layer maps |
| **Content AI Suite** | `view-content-suite.php`| 7-dashboard content lifecycle engine with Myra AI copilot, SEO visibility tracker, and Quill editor |
| **Lead Management (CRM)**| `view-leads.php` | Kanban pipeline with numeric phone validation, automated WhatsApp Cloud API & SMTP follow-ups |
| **Dual-Engine Canvas** | `view-canvas.php` | Dual website builder: Elementor White-Label + In-Browser Visual HTML Editor with URL edit state persistence |
| **Universal Website Migrator**| `view-canvas.php` | 1-click multi-page crawler scraping external HTML/CSS/JS sites into editable draft themes |
| **Forms & Reviews 2.0** | `view-forms.php` | 26 hardened form widgets, AI Conversion Doctor, WhatsApp/SMTP triggers, and embed generator |
| **App Modules (Feature Hub)**| `view-feature-hub.php`| Tenant module customizer with 22 structured P0/P1/P2 agency modules, explicit save workflow, and batch toggles |
| **Media Proofing Manager** | `view-media.php` | Studio-grade asset management with crop presets (1:1, 4:3, 16:9) and synced folder headers |
| **Document Vault** | `view-vault.php` | GST-compliant invoicing (CGST/SGST/IGST, SAC 9983) with SHA-256 legal e-sign audit registry |
| **Finance AI Co-founder**| `view-financials.php` | Multi-tenant cash ledger, 30-day runway projections, and deal feasibility simulator |
| **Crew & Team Scheduler** | `view-crew-scheduler.php`| Timeline-based shift scheduling, crew allocation, and dispatch management |
| **Equipment & Listings** | `view-equipment.php` | Camera gear custody tracking / Geocoded real estate inventory |
| **Super Admin Console** | `view-super-admin.php` | 11-tab administrative control center for MRR telemetry, tenant health, global AI tokens & emergency controls |
| **Developer Docs Portal** | `view-public-docs.php` | 3-column public documentation portal with AI Playground and Command Palette (`/docs`) |

---

## Design System & UX Standards

1. **Pure Light Mode**: Zero dark mode for instant splash rendering and strict visual continuity (`#ffffff` / `zinc-50` through `zinc-950`).
2. **Monochromatic Neutral Palette**: Notion/Shopify-inspired zinc color ramp with color accents strictly bound to functional states (🟢 Active, 🟡 Pending, 🔴 Critical).
3. **Mobile Sheet & Drawer SOP**: Zero mobile side drawers. All action sheets, creators, and filters open as **bottom slide-up sheets** (`translate-y-full` to `translate-y-0`) with drag handles and spring easing.
4. **Top-Down Floating Toasts (Mobile) / Dynamic Offset (Desktop)**: Monochromatic notification feedback (`window.coraShowToast`) floating top-center on mobile and elevated above Studio Drawers on desktop.
5. **Universal Body Scroll Lock**: `window.coraLockScroll()` & `window.coraUnlockScroll()` with scrollable drawer container opt-in (`.cora-drawer-scrollable`) eliminating background page jitter.
6. **0ms Touch Latency**: `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` applied across all interactive controls.
7. **Security URL Masking**: Direct rewrites and symlinks masking `wp-content` to `/assets/` and `wp-includes` to `/core/` to shield internal platform architecture.
8. **Semantic URL Navigation**: Clean RESTful navigation paths across all dashboard views (`/workspace/{subpage}`) replacing legacy JavaScript links.
9. **Strict Single Owner Policy**: One workspace owner per tenant with guarded role assignability.
10. **Strict Role & Terminal Scoping**: Dedicated Field Driver role (`cora_field_vendor`) with server-side route redirection, DOM container isolation, complete chrome stripping, and terminal-locked AI copilot.
11. **Anti-Spam & Single 24-Hour Executive Delivery**: Disarms repetitive micro-event notification emails; all transient events route to In-App Bell & Web Push, consolidating executive business summaries into a single printable PDF delivered strictly once every 24 hours.

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

*Cora Platform v4.9.103 — Architecture & Development Team.*
