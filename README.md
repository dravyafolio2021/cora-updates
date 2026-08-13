# Cora Platform

> Multi-tenant SaaS workspace platform for Indian agencies — Photography Studios & Real Estate Brokerages.

---

## Overview

Cora is a full-stack WordPress-based SaaS platform that provides white-labeled, multi-tenant workspace dashboards for Indian service agencies. Each workspace is an isolated business environment with CRM, content management, financial tools, team scheduling, and AI-powered assistants.

**Current Version**: `v3.4.0`  
**Stack**: WordPress 6.x, PHP 8.2, Vanilla CSS/Tailwind, JavaScript (ES6+), Quill.js, Elementor  
**AI Providers**: Google Gemini 3.5 Flash, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o

---

## Architecture

```
cora/
├── app/public/wp-content/plugins/
│   ├── cora-workspace/          # Core platform plugin (v3.4.0)
│   │   ├── admin-dashboard.php  # Main dashboard controller & routing
│   │   ├── cora-workspace.php   # Core AJAX handlers, hooks, DB functions
│   │   ├── includes/            # Backend engines (docs, RAG, MCP)
│   │   ├── views/               # 40+ modular PHP view files
│   │   └── assets/              # JS, CSS, images
│   ├── cora-real-estate/        # Real estate industry extension
│   ├── cora-studio-ai/          # Photography studio extension
│   └── cora-frontend/           # Marketing frontend module
├── docs/                        # Technical documentation
├── tests/                       # Playwright E2E test suites
├── scripts/                     # Build, deploy, and provisioning scripts
├── updates/                     # Release artifacts (.zip + .json manifests)
└── .agents/                     # AI agent configuration & rules
```

---

## Core Modules

| Module | View File | Description |
| :--- | :--- | :--- |
| **Dashboard** | `admin-dashboard.php` | Main workspace landing with bento grid KPIs |
| **Content AI Suite** | `view-content-suite.php` | 7-tab content lifecycle engine with Myra AI copilot |
| **Lead Management** | `view-leads.php` | Drag-and-drop Kanban CRM pipeline |
| **Media Library** | `view-media.php` | Studio-grade media manager with crop presets |
| **Email Suite** | `view-emails.php` | Transactional email composer with SMTP integration |
| **Document Vault** | `view-vault.php` | GST-compliant invoicing with E-Sign workflows |
| **Canvas Builder** | `view-canvas.php` | White-labeled Elementor theme builder |
| **Crew Scheduler** | `view-crew-scheduler.php` | Timeline-based crew assignment system |
| **Forms & Reviews** | `view-forms.php` | Multi-channel review collection |
| **Financial Dashboard** | `view-financials.php` | Revenue tracking and payment monitoring |
| **Developer Docs** | `view-public-docs.php` | Public 3-column Notion-like documentation portal |
| **AI RAG** | `view-rag.php` | Per-tenant knowledge base |
| **MCP Gateway** | `view-mcp.php` | JSON-RPC WebSocket connection portal |

---

## Local Development

### Prerequisites
- [Local by Flywheel](https://localwp.com/) or equivalent WordPress local environment
- PHP 8.2+, Node.js 18+
- Playwright for E2E testing

### Setup
```bash
# Clone the repository
git clone https://github.com/dravyafolio2021/heycora.git cora
cd cora

# Provision test accounts
php scripts/setup_local_accounts.php
```

### Test Credentials

| Role | Username | Password | URL |
| :--- | :--- | :--- | :--- |
| Super Admin | `cora_admin` | `cora_secure_pass_123` | `http://cora.local/workspace/dashboard` |
| Real Estate Owner | `re_owner` | `cora_secure_pass_123` | `http://cora.local/workspace/dashboard?industry=real_estate` |
| Studio Owner | `studio_owner` | `cora_secure_pass_123` | `http://cora.local/workspace/dashboard?industry=photography_studio` |

### Build & Deploy
```bash
# Build release package
./scripts/build.sh

# Deploy to staging
./scripts/deploy.sh
```

### Testing
```bash
# Install Playwright browsers
npx playwright install

# Run full E2E suite
npx playwright test

# Run specific tier
npx playwright test tests/e2e/tier4-workload-flows.spec.ts
```

---

## Documentation

| Document | Path | Description |
| :--- | :--- | :--- |
| **Platform Architecture** | [`docs/cora-platform-documentation.md`](docs/cora-platform-documentation.md) | Complete technical specification (v3.4.0) |
| **Canvas Module** | [`docs/canvas-frontend-module.md`](docs/canvas-frontend-module.md) | Theme builder & Elementor integration |
| **Module Status** | [`MODULES_STATUS.md`](MODULES_STATUS.md) | Branch synchronization manifest |
| **Local Credentials** | [`LOCAL_CREDENTIALS.md`](LOCAL_CREDENTIALS.md) | Test environment access details |
| **Agent Rules** | [`.agents/AGENTS.md`](.agents/AGENTS.md) | AI development agent configuration |

---

## Design System

Cora enforces a **Notion/Shopify monochromatic visual standard**:
- **Palette**: 11-step zinc neutral ramp (`zinc-50` → `zinc-950`), pure white and black
- **Typography**: Inter (UI), Outfit (display), JetBrains Mono (code)
- **Icons**: Thin-lined vector SVGs (`stroke-width: 1.8-2.2`)
- **No dark mode**: Pure light mode enforced platform-wide (since v3.2.83)
- **No browser modals**: All feedback via custom toast system (`window.coraShowToast()`)

---

## Branch Strategy

```
main                    ← Production-ready, all merges go here
├── feature/studio-module     ← Active: Studio management features
├── feature/frontend-module   ← Active: Marketing frontend & Lovable
└── docs/platform-docs-update ← Documentation updates
```

See [`MODULES_STATUS.md`](MODULES_STATUS.md) for complete branch index and conflict guard rules.

---

## License

Proprietary. All rights reserved.
