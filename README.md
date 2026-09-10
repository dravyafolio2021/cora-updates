# Cora Platform

> Multi-tenant SaaS workspace engine for Indian service agencies — Photography Studios, Real Estate Brokerages & Marketing Agencies.

---

## Overview

Cora is a full-stack, enterprise-grade WordPress-based SaaS platform that provides white-labeled, multi-tenant workspace dashboards. Each workspace operates as an isolated business environment equipped with Lead CRM, Content AI, Financial Intelligence, Team Scheduling, Forms & Reviews 2.0, Visual Website Canvas, Continuous Hands-Free Voice AI, and per-tenant module customization.

* **Current Version**: `v4.9.32`
* **Supported Verticals**: Photography Studio (`photography_studio`), Real Estate Brokerage (`real_estate`), Marketing Agency (`marketing_agency`)
* **Tech Stack**: WordPress 6.x (Locked Down Backend), PHP 8.2+, Tailwind CSS (Monochromatic Zinc Ramp), JavaScript (ES6+), Next.js, Quill.js, Elementor, Sandboxed Visual HTML Engine
* **AI Providers**: Google Gemini 3.5 Flash, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o

---

## Architecture

```
cora/
├── app/public/wp-content/plugins/
│   ├── cora-workspace/          # Core platform plugin (v4.9.32)
│   │   ├── admin-dashboard.php  # Main dashboard controller & routing
│   │   ├── cora-workspace.php   # Core AJAX handlers, hooks, DB schema, micro-cache
│   │   ├── includes/            # Backend engines (docs, RAG, MCP, PWA)
│   │   ├── views/               # 50+ modular PHP view files
│   │   └── assets/              # JS, CSS, dynamic versioned icons
│   ├── cora-real-estate/        # Real estate industry extension
│   ├── cora-studio-ai/          # Photography studio extension
│   └── cora-frontend/           # Marketing frontend module
├── docs/                        # Technical documentation suite
├── tests/                       # Playwright E2E test suites (Tier 1-4)
├── scripts/                     # Build, deploy, and account provisioning scripts
├── updates/                     # Release artifacts (.zip + .json manifests)
└── .agents/                     # AI agent configuration & workspace rules
```

---

## Core Modules & Capabilities

| Module | Primary View | Description |
| :--- | :--- | :--- |
| **Workspace Dashboard** | `admin-dashboard.php` | Adaptive workspace landing with bento grid KPIs and mobile floating island |
| **Voice AI Discussion** | `admin-dashboard.php` | Real-time continuous hands-free voice engine with 4 personalities & 9 dialects |
| **Content AI Suite** | `view-content-suite.php`| 7-dashboard content lifecycle engine with Myra AI copilot and Quill editor |
| **Lead Management (CRM)**| `view-leads.php` | Kanban pipeline with automated multi-channel follow-ups |
| **Dual-Engine Canvas** | `view-canvas.php` | Dual website builder: Elementor White-Label + Visual HTML Editor |
| **Forms & Reviews 2.0** | `view-forms.php` | 26 hardened form widgets, AI Conversion Doctor, WhatsApp/SMTP triggers |
| **App Modules (Feature Hub)**| `view-feature-hub.php`| Tenant module customizer with explicit save workflow and batch toggles |
| **Media Proofing Manager** | `view-media.php` | Studio-grade asset management with crop presets (1:1, 4:3, 16:9) |
| **Document Vault** | `view-vault.php` | GST-compliant invoicing with SHA-256 e-sign legal audit registry |
| **Finance AI Co-founder**| `view-financials.php` | Multi-tenant cash ledger, 30-day projections, deal feasibility simulator |
| **Crew & Team Scheduler** | `view-crew-scheduler.php`| Timeline-based shift scheduling and dispatch management |
| **Equipment & Listings** | `view-equipment.php` | Camera custody tracking / Geocoded real estate inventory |
| **Unified Inbox** | `view-inbox.php` | Consolidated customer messaging bridging WhatsApp and SMTP email |
| **Developer Docs Portal** | `view-public-docs.php` | 3-column public documentation portal with AI Playground (`/docs`) |

---

## Design System & UX Standards

1. **Pure Light Mode**: Complete removal of dark mode for zero-lag splash rendering and strict visual continuity (`#ffffff` / `zinc-50` through `zinc-950`).
2. **Monochromatic Neutral Palette**: Notion/Shopify-inspired zinc color ramp with color accents strictly bound to functional states (🟢 Active, 🟡 Pending, 🔴 Critical).
3. **Mobile Sheet & Drawer SOP**: Zero mobile side drawers. All action sheets, creators, and filters open as **bottom slide-up sheets** (`translate-y-full` to `translate-y-0`) with drag handles and spring easing.
4. **Top-Down Floating Toasts**: Monochromatic notification feedback (`window.coraShowToast`) floating top-center on mobile to eliminate visual collisions.
5. **0ms Touch Latency**: `touch-action: manipulation;` applied across all interactive controls.

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
php scripts/setup_local_accounts.php
```

### Pre-Configured Test Accounts (`http://cora.local`)

| Workspace Archetype | Username / Email | Password | Direct URL |
| :--- | :--- | :--- | :--- |
| 🏡 **Real Estate Brokerage** | `re_owner`<br>`owner.realestate@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=real_estate](http://cora.local/workspace/dashboard?industry=real_estate) |
| 📸 **Photography Studio** | `studio_owner`<br>`owner.studio@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=photography_studio](http://cora.local/workspace/dashboard?industry=photography_studio) |
| 📈 **Marketing Agency** | `marketing_owner`<br>`owner.marketing@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=marketing_agency](http://cora.local/workspace/dashboard?industry=marketing_agency) |
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

*Cora Platform v4.9.32 — Architecture & Development Team.*
