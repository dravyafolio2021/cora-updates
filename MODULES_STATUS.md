# Cora Platform — Module Progress & Branch Synchronization Manifest

> **Single Source of Truth for Multi-Branch Parallel Development**
> This file is maintained to ensure parallel agent execution and feature branches remain fully synchronized and never break shared platform APIs or files.

---

## 1. Branch Index & Active Modules

| Module Name | Branch Name | Status | Main Touchpoint Files | Assigned Agent / Chat |
|---|---|---|---|---|
| **Core Platform** | `main` | 🟢 Stable (v4.9.374) | `cora-workspace.php`, `admin-dashboard.php` | Main Orchestrator |
<!-- MODULE_ROWS_START -->
| **Universal Edge-Docked Entry Inspector & Timezone Engine** | `main` | 🟢 Complete & Active (v4.9.374) | `views/view-forms.php`, `assets/css/admin-style.css`, `cora-workspace.php` | Forms & Telemetry Agent |
| **Dynamic Affiliate Referral Tracking & Checkout Discounts** | `main` | 🟢 Complete & Active (v4.9.374) | `includes/affiliate-referral-engine.php`, `views/view-affiliate-referrals.php`, `cora-frontend/app/api/referral/track/route.ts`, `cora-frontend/lib/referral.ts` | Growth & Affiliate Agent |
| **Forms Cards Overhaul & Universal 5th Tab All Entries** | `main` | 🟢 Complete & Active (v4.9.372) | `views/view-forms.php`, `cora-workspace.php` | Forms UX & Performance Agent |
| **Pricing SaaS Hierarchy & Resource Specifications** | `feat/marketing-site-reform-2026-10-06` | 🟢 Complete & Active (Oct 2026) | `cora-frontend/app/pricing/page.tsx`, `cora-frontend/components/pricing/*` | Growth & Marketing Frontend Agent |
| **Partner Coaching Accelerator Funnel (VNX Digital)** | `feat/marketing-site-reform-2026-10-06` | 🟢 Complete & Active (Oct 2026) | `cora-frontend/app/vnxdigital/page.tsx`, `cora-frontend/app/vanshrajput/page.tsx` | Growth & Partnership Agent |
| **Unified Global Resources Hero Component** | `feat/marketing-site-reform-2026-10-06` | 🟢 Complete & Active (Oct 2026) | `cora-frontend/components/resources/GlobalResourcesHero.tsx` | Design System Agent |
| **Marketing Site Reform & Tools/Templates Ecosystem** | `feat/marketing-site-reform-2026-10-06` | 🟢 Complete & Active (Oct 2026) | `cora-frontend/app/*`, `cora-frontend/components/hero/Hero.tsx`, `cora-frontend/components/layout/Navbar.tsx` | Growth & Marketing Frontend Agent |
| **Sticky AI SDR Bar & Objection Handling Engine** | `feat/marketing-site-reform-2026-10-06` | 🟢 Complete & Active (Oct 2026) | `cora-frontend/components/hero/HeroAIInput.tsx`, `cora-frontend/app/api/ai-preview/route.ts` | Growth AI & Sales Agent |
| **Multi-Format Template & Asset Library (16+ Schemas)** | `feat/marketing-site-reform-2026-10-06` | 🟢 Complete & Active (Oct 2026) | `cora-frontend/app/templates/*`, `cora-frontend/lib/templates-data.ts` | Templates & Theme Ecosystem Agent |
| **AI Run Quotas & Fair Usage Pacing** | `main` | 🟢 Complete & Active (v4.9.364) | `cora-workspace.php`, `views/view-settings-suite.php`, `admin-dashboard.php` | AI Architecture & Super Admin Agent |
| **Endorsely Affiliate Tracking Suite** | `main` | 🟢 Complete & Active (v4.9.364) | `cora-frontend/app/layout.tsx`, `cora-frontend/components/pricing/*` | Growth & Checkout Agent |
| **Live Static Site & Tailwind Parity Engine** | `main` | 🟢 Complete & Active (v4.9.363) | `views/view-canvas.php`, `cora-workspace.php` | Canvas Visual Engine Agent |
| **Unified 'Edit with AI' Workflow** | `main` | 🟢 Complete & Active (v4.9.362) | `views/view-canvas.php`, `cora-workspace.php` | Canvas Visual Engine Agent |
| **Forms Embedding Suite & Interoperability** | `main` | 🟢 Complete & Active (v4.9.361) | `views/view-canvas.php`, `views/view-forms.php`, `cora-workspace.php` | Forms & Canvas Agent |
| **Streamlined Live Code Editor Header** | `main` | 🟢 Complete & Active (v4.9.360) | `views/view-canvas.php` | Canvas UX Agent |
| **Landing Page Template Markup & Resilience Shield** | `main` | 🟢 Complete & Active (v4.9.358 - v4.9.359) | `views/view-canvas.php`, `cora-workspace.php` | Canvas Visual Engine Agent |
| **ChatGPT Action Discovery & 28 Tools Ceiling** | `main` | 🟢 Complete & Active (v4.9.355 - v4.9.357) | `cora-workspace.php`, `includes/mcp/class-cora-universal-mcp-server.php`, `includes/mcp/class-cora-mcp-tool-registry.php` | AI Architecture Agent |
| **Interactive OAuth 2.1 Consent Panel** | `main` | 🟢 Complete & Active (v4.9.354) | `includes/mcp/class-cora-oauth-server.php`, `scripts/test_mcp_full_access.php` | Identity & Security Agent |
| **Canvas Drawers Standard & Overflow Locking** | `main` | 🟢 Complete & Active (v4.9.352 - v4.9.353) | `views/view-canvas.php`, `cora-workspace.php` | Canvas Visual Engine Agent |
| **AI Tools MCP Gateway & Live Action Playground** | `main` | 🟢 Complete & Active (v4.9.351) | `views/view-canvas.php`, `cora-workspace.php` | AI Architecture Agent |
| **Zero-Copy MCP File Management & Page Publishing** | `main` | 🟢 Complete & Active (v4.9.349) | `includes/mcp/class-cora-mcp-tool-registry.php`, `cora-workspace.php` | Canvas Integration Agent |
| **Add Theme Wizard 2x2 Grid & Production Previews** | `main` | 🟢 Complete & Active (v4.9.344 - v4.9.345) | `views/view-canvas.php`, `assets/img/*` | Canvas Visual Engine Agent |
| **Super Admin Action Restoration & Portaling** | `main` | 🟢 Complete & Active (v4.9.343) | `views/view-super-admin.php`, `cora-workspace.php` | Super Admin Agent |
| **Universal PWA Install Suite & Admin Popover** | `feat/admin-pwa-install-trigger` | 🟢 Complete & Active (v4.9.342) | `admin-dashboard.php`, `views/view-settings-suite.php` | Mobile & PWA Agent |
| **Super Admin PWA Badging & Icon Suite** | `feat/workspace-v4.9.340` | 🟢 Complete & Active (v4.9.341) | `assets/pwa/*`, `scripts/generate_admin_pwa_icons.php`, `admin-dashboard.php` | Mobile & PWA Agent |
| **Admin Quota Rate-Limit Bypass & Session Timeout** | `main` | 🟢 Complete & Active (v4.9.339) | `cora-workspace.php` | Super Admin Agent |
| **Super Admin PWA Badging & Role Manifest** | `feat/workspace-v4.9.338` | 🟢 Complete & Active (v4.9.338) | `admin-dashboard.php`, `cora-workspace.php`, `scripts/generate_admin_pwa_icons.php` | Mobile & PWA Agent |
| **Security Path Containment & 304 Caching** | `feat/workspace-v4.9.338` | 🟢 Complete & Active (v4.9.337) | `cora-workspace.php`, `includes/class-cora-nextjs-integration.php` | Security Architecture Agent |
| **Canvas AI Starter Prompts & Form Interception** | `feat/workspace-v4.9.338` | 🟢 Complete & Active (v4.9.334 - v4.9.336) | `views/view-canvas.php`, `cora-workspace.php` | Canvas Visual Engine Agent |
| **Official ChatGPT Plugin & Remote MCP 2.0** | `feat/workspace-v4.9.338` | 🟢 Complete & Active (v4.9.334) | `cora-chatgpt-plugin/*`, `includes/mcp/*` | AI Architecture Agent |
| **Automated Static Site Compiler & Exporter** | `feat/workspace-v4.9.338` | 🟢 Complete & Active (v4.9.317 - v4.9.331) | `includes/class-cora-nextjs-integration.php`, `cora-workspace.php` | Canvas Integration Agent |
| **Canvas Next.js Live Preview & Dev Proxy** | `feat/workspace-v4.9.316` | 🟢 Complete & Active (v4.9.316) | `views/view-canvas.php`, `includes/class-cora-nextjs-integration.php`, `cora-workspace.php` | Canvas Visual Engine Agent |
| **Inventory & Field Van Sales Engine** | `feat/sales-items-registry-and-print`, `feat/inventory-and-van-sales` | 🟢 Complete & Active (v4.9.314 - v4.9.315) | `includes/class-cora-inventory-engine.php`, `views/view-inventory-management.php`, `cora-workspace.php` | Supply Chain & Inventory Agent |
| **Canvas Git Integration & ISR Revalidation** | `feat/canvas-theme-integrations` | 🟢 Complete & Active (v4.9.308 - v4.9.313) | `includes/class-cora-nextjs-integration.php`, `views/view-canvas.php`, `cora-workspace.php` | Canvas Integration Agent |
| **Workspace Core & Live Theme Customizer** | `feat/cora-workspace-core` | 🟢 Complete & Active (v4.9.300 - v4.9.307) | `views/view-canvas.php`, `views/view-financials.php`, `views/view-vault.php`, `admin-dashboard.php` | Workspace Core Agent |
| **CRM & Leads High-Velocity Pipeline** | `feat/workspace-next`, `feat/cora-workspace-core` | 🟢 Complete & Active (v4.9.300 - v4.9.302) | `views/view-leads.php`, `cora-workspace.php`, `assets/js/admin-script.js` | CRM Architecture Agent |
| **Universal Remote MCP & OAuth 2.1 PKCE** | `feat/cora-guides-v2-digital-book` | 🟢 Complete & Active (v4.9.210 - v4.9.230) | `includes/mcp/*`, `views/view-mcp.php`, `cora-workspace.php` | AI Architecture Agent |
| **Workspace Next & Identity Verification** | `feat/workspace-next` | 🟢 Complete & Active (v4.9.268 - v4.9.273) | `views/view-settings-suite.php`, `views/verify.php`, `cora-workspace.php` | Authentication & Identity Agent |
| **Finance & GST Invoicing Suite** | `feat/cora-workspace-core` | 🟢 Complete & Active (v4.9.303 - v4.9.304) | `views/view-financials.php`, `views/view-vault.php`, `cora-workspace.php` | Finance AI Co-founder Agent |
| **Media Manager & Asset Hub** | `main` | 🟢 Complete & Active (v4.9.295 - v4.9.298) | `views/view-media.php`, `cora-workspace.php`, `assets/css/admin-style.css` | Media Module Agent |
| **PWA & Web Push Notification Subsystem** | `main` | 🟢 Complete & Active (v4.9.262 - v4.9.267) | `cora-service-worker.js`, `admin-dashboard.php`, `views/view-super-admin.php` | Mobile Resilience Agent |
| **AI Co-Founder & Continuous RAG** | `main` | 🟢 Complete & Active (v4.9.274 - v4.9.276) | `admin-dashboard.php`, `cora-workspace.php`, `assets/js/admin-script.js` | AI Orchestrator Agent |
| **Super Admin Multi-Tenancy Console** | `main` | 🟢 Complete & Active (v4.9.235 - v4.9.267) | `views/view-super-admin.php`, `admin-dashboard.php`, `cora-workspace.php` | Super Admin Agent |
| **Security Hardening & Central Authorization** | `security/audit-remediation` | 🟢 Complete & Active (v4.9.209) | `includes/class-cora-authorization.php`, `includes/class-cora-ssrf-filter.php`, `cora-workspace.php` | Security Architecture Agent |
| **Email Verification & Route Interception** | `main` | 🟢 Complete & Active (v4.9.208) | `views/verify.php`, `cora-workspace.php`, `views/login.php` | Authentication & Identity Agent |
| **Mobile GPU Acceleration & Touch Latency** | `main` | 🟢 Complete & Active (v4.9.207) | `assets/css/admin-style.css`, `admin-dashboard.php` | Mobile Performance Agent |
| **Affiliates & Referrals Gamified Hub** | `main` | 🟢 Complete & Active (v4.9.200 - v4.9.206) | `views/view-affiliate-referrals.php`, `includes/affiliate-referral-engine.php` | Growth & Affiliate Agent |
| **Canvas Themes Revamp & Speed Suite** | `main` | 🟢 Complete & Active (v4.9.199) | `views/view-canvas.php`, `cora-workspace.php` | Canvas Visual Engine Agent |
| **Email Communications High-Density Suite** | `main` | 🟢 Complete & Active (v4.9.199) | `views/view-emails.php`, `cora-workspace.php` | Email Module Agent |
| **System Settings Suite AI Controller** | `main` | 🟢 Complete & Active (v4.9.198) | `views/view-settings-suite.php`, `cora-workspace.php` | Settings Suite Agent |
| **AI Tools MCP Gateway & Voice Mode** | `main` | 🟢 Complete & Active (v4.9.193 - v4.9.197) | `views/view-mcp.php`, `views/view-rag.php`, `cora-workspace.php` | AI Architecture Agent |
| **Media Management AI Copilot & Dock** | `main` | 🟢 Complete & Active (v4.9.191 - v4.9.192) | `views/view-media.php`, `cora-workspace.php` | Media Module Agent |
| **User Management & People Ops Copilot** | `main` | 🟢 Complete & Active (v4.9.190) | `views/view-users.php`, `cora-workspace.php` | RBAC & Governance Agent |
| **App Modules (Feature Hub)** | `main` | 🟢 Complete & Active (v4.9.200) | `views/view-feature-hub.php`, `cora-workspace.php` | Feature Hub Agent |
| **Media Proofing & Telemetry** | `main` | 🟢 Complete & Active (v4.9.188) | `views/view-media.php`, `share-media.php`, `cora-workspace.php` | Media Module Agent |
| **Users & Role Governance** | `main` | 🟢 Complete & Active (v4.9.186) | `views/view-users.php`, `cora-workspace.php` | RBAC & Governance Agent |
| **Field Ops & Geolocation Tracker**| `main` | 🟢 Complete & Active (v4.9.182) | `assets/js/cora-field-ops-tracker.js`, `views/view-users.php` | Field Ops Agent |
| **System Settings & Notifications**| `main` | 🟢 Complete & Active (v4.9.179) | `views/view-settings-suite.php`, `cora-workspace.php` | Settings Suite Agent |
| **Forms & Reviews 2.0 (Form Architect)** | `main` | 🟢 Complete & Active (v4.9.177) | `views/view-forms.php`, `includes/workspace-header.php` | Forms UX & Performance Agent |
| **AI Co-Founder & Voice AI Overhaul**| `main` | 🟢 Complete & Active (v4.9.162) | `admin-dashboard.php`, `cora-workspace.php`, `admin-script.js` | AI Orchestrator Agent |
| **Enterprise AI Safety Guardrails** | `main` | 🟢 Complete & Active (v4.9.139) | `cora-workspace.php`, `admin-script.js` | AI Security & Governance Agent |
| **Content AI Suite** | `main` | 🟢 Complete & Active (v4.9.159) | `views/view-content-suite.php`, `cora-workspace.php` | Content Module Agent |
| **Client Task Manager (CRM Tasks)**| `main` | 🟢 Complete & Active (v4.9.150) | `admin-dashboard.php`, `views/view-client-task-manager.php` | Task Management Agent |
| **Dashboard & Nav Customizer** | `main` | 🟢 Complete & Active (v4.9.147) | `admin-dashboard.php`, `cora-workspace.php` | Telemetry & UX Customizer Agent |
| **Client Management Suite** | `main` | 🟢 Complete & Active (v4.9.124) | `views/view-clients.php`, `views/view-financials.php` | CRM Client Suite Agent |
| **Public White-Labeled Client Portal**| `main` | 🟢 Complete & Active (v4.9.122) | `public-client-portal.php` | Client Portal Agent |
| **CRM & Client Revenue Suite** | `feature/crm-module-suite` | 🟢 Merged to Main (v4.9.124) | `views/view-leads.php`, `views/view-financials.php`, `cora-workspace.php` | CRM Architecture Agent |
| **CRM & Lead Pipeline System** | `feature/crm-pipeline-next` | 🟢 Merged to Main (v4.9.118) | `views/view-leads.php`, `cora-workspace.php` | CRM Pipeline Agent |
| **Sidebar & Nav Architecture** | `main` | 🟢 Complete & Active (v4.9.178) | `admin-dashboard.php`, `cora-workspace.php` | Navigation UX Agent |
| **Professional Services Vertical**| `feature/industry-professional-services`| 🟢 Complete & Active (v4.9.106) | `cora-workspace.php`, `views/*` | Industry Architecture Agent |
| **Agency Partner Ecosystem** | `feature/agency-partner-ecosystem` | 🟡 Active In-Progress | `cora-frontend/app/*`, `views/*`, `cora-workspace.php` | Agency Ecosystem Agent |
| **Executive 24h PDF Reports** | `feature/workspace-development-2026-09-12` | 🟢 Complete & Active (v4.9.103) | `cora-workspace.php`, `views/view-inventory-management.php` | Executive Reporting Agent |
| **Field Driver Chrome Stripping**| `feature/workspace-development-2026-09-12` | 🟢 Complete & Active (v4.9.102) | `admin-dashboard.php`, `admin-script.js`, `admin-style.css` | Security & Terminal Agent |
| **Stationery & Plant Inventory** | `feature/workspace-development-2026-09-12` | 🟢 Complete & Active (v4.9.103) | `includes/class-cora-inventory-engine.php`, `views/view-inventory-management.php` | Supply Chain & Inventory Agent |
| **Field Sales Driver Isolation** | `feature/workspace-development-2026-09-12` | 🟢 Complete & Active (v4.9.103) | `views/view-inventory-management.php`, `views/setup-account.php`, `admin-dashboard.php` | RBAC & Security Agent |
| **Super Admin Console (v4.9.59)** | `main` | 🟢 Merged to Main | `views/view-super-admin.php`, `admin-dashboard.php` | Super Admin Agent |
| **Universal Website Migrator** | `main` | 🟢 Merged to Main | `includes/class-cora-html-website-migrator.php`, `views/view-canvas.php` | Canvas Migration Agent |
| **Dynamic AI Co-Founder** | `main` | 🟢 Merged to Main | `admin-dashboard.php`, `cora-workspace.php` | AI Co-Founder Agent |
| **Multimodal Team Migration**| `main` | 🟢 Merged to Main | `views/view-users.php`, `cora-workspace.php` | Team Onboarding Agent |
| **Voice AI Discussion** | `main` | 🟢 Merged to Main | `admin-dashboard.php`, `cora-workspace.php` | Voice AI Engine Agent |
| **Canvas Dual Builder** | `main` | 🟢 Merged to Main | `views/view-canvas.php`, `view-canvas-render.php` | Canvas Visual Engine Agent |
| **Mobile & PWA Engine** | `main` | 🟢 Merged to Main | `admin-dashboard.php`, `cora-service-worker.js` | Mobile Resilience Agent |
| **Document Vault** | `main` | 🟢 Merged to Main | `views/view-vault.php`, `cora-workspace.php` | Dedicated Vault Agent |
| **Finance AI Co-founder**| `main` | 🟢 Merged to Main | `views/view-financials.php`, `cora-workspace.php` | Finance AI Co-founder Agent |
| **Email Suite & Hostinger** | `main` | 🟢 Merged to Main | `views/view-emails.php`, `cora-workspace.php` | Email Module Agent |
| **Public Docs Portal** | `main` | 🟢 Merged to Main | `views/view-public-docs*.php`, `includes/docs-engine.php` | Docs Portal Agent |
<!-- MODULE_ROWS_END -->

---

## 2. Shared File Touchpoints & Conflict Guard

> [!IMPORTANT]
> If multiple feature branches modify any of the following shared files simultaneously, coordinators must review parameter signatures and line ranges to prevent merge conflicts:

- `app/public/wp-content/plugins/cora-workspace/cora-workspace.php` (Core AJAX Handlers, Micro-Cache, Schema, Hooks, AI Safety & Policy Guardrails, Storage Details, Central Route Interception)
- `app/public/wp-content/plugins/cora-workspace/includes/class-cora-authorization.php` (Central Tenant Context Assertion, Object-Level Policy Gatekeeper across Tasks, Forms, Vault, Canvas, Media)
- `app/public/wp-content/plugins/cora-workspace/includes/class-cora-ssrf-filter.php` (Outbound HTTP Request SSRF Filter guarding RFC1918 private subnets, loopback, and cloud metadata)
- `app/public/wp-content/plugins/cora-workspace/includes/class-cora-nextjs-integration.php` (Next.js Git Connector, Route Scanner, Headless REST APIs & ISR Revalidation)
- `app/public/wp-content/plugins/cora-workspace/includes/class-cora-inventory-engine.php` (Extended Dual-Tier Supply Chain, 3-Step Van Dispatch Wizard, Weight Allocation @ ₹401.25/kg, Spot Billing, Sales Register, Manifest Print & CSV)
- `app/public/wp-content/plugins/cora-workspace/includes/mcp/class-cora-universal-mcp-server.php`, `class-cora-oauth-server.php`, `class-cora-mcp-tool-registry.php` (Universal Remote MCP Server & OAuth 2.1 Engine RFC 7591 / RFC 9728)
- `app/public/wp-content/plugins/cora-workspace/views/view-canvas.php` (Canvas Themes Single-Stream Library, Next.js Git Connector, Live Dev Server Preview, Level 2 Customizer)
- `app/public/wp-content/plugins/cora-workspace/views/view-inventory-management.php` (Plant & Field Van POS, 3-Step Dispatch Wizard, Spot Invoices, Sales Register, Manifest Print)
- `app/public/wp-content/plugins/cora-workspace/views/view-leads.php` (Full-Height Edge-to-Edge Leads Drawer, Draggable Resizer, Accordion Groups, Team Assignee Sanitization)
- `app/public/wp-content/plugins/cora-workspace/views/view-financials.php` (Financials Ledger, GST Invoicing Suite, Document Vault Sync)
- `app/public/wp-content/plugins/cora-workspace/views/view-vault.php` (Document Vault, GST Invoicing, Contract Immutability & 500KB Payload Cap)
- `app/public/wp-content/plugins/cora-workspace/views/view-settings-suite.php` (System Settings Suite, Administration Email Verification, 100 Daily AI Credits Quota)
- `app/public/wp-content/plugins/cora-workspace/views/view-super-admin.php` (11-Tab Super Admin Suite, Danger Zone Workspace Deletion Modal, Web Push Telemetry)
- `app/public/wp-content/plugins/cora-workspace/views/verify.php` (Early Route Interception for Email Verification & Instant Auto-Login)
- `app/public/wp-content/plugins/cora-workspace/views/view-mcp.php` (AI Tools MCP Developer Gateway, Live Voice Assistant Stage, Sticky Chat Input Dock, Model Selector Popover)
- `app/public/wp-content/plugins/cora-workspace/views/view-affiliate-referrals.php` (Affiliates & Referrals Gamified Hub, 5 Sticky Sub-Tabs, Tier Progress Meter, Leaderboard, Earnings Simulator)
- `app/public/wp-content/plugins/cora-workspace/views/view-emails.php` (High-Density Email Suite, 2x2 Mobile KPI Cards, Dynamic CRM Contacts Sync)
- `app/public/wp-content/plugins/cora-workspace/admin-dashboard.php` (Main Dashboard Controller, Canvas Quick-Actions, Mobile Island, Voice & AI Copilot UI, Centered Analytics)
- `app/public/wp-content/plugins/cora-workspace/includes/workspace-header.php` (Sticky Sub-Navigation Tabs Controller & Filter Toolbar)
- `app/public/wp-content/plugins/cora-workspace/share-media.php` (Public Shared Media Proofing Portal, Route Interception, Telemetry Tracking)
- `app/public/wp-content/plugins/cora-workspace/views/view-media.php` (Media Proofing Hub, Centered Folder Modals, Curated Color Tag Presets, Floating Action Dock)
- `app/public/wp-content/plugins/cora-workspace/assets/js/cora-field-ops-tracker.js` (Field Ops Telemetry Engine, Login Auto-Start & Beacon Flush Logout)
- `app/public/wp-content/plugins/cora-workspace/views/view-forms.php` (Form AI Architect, 15 Field Types, Full-Height Live Preview, Flush Mobile Sticky Tabs)
- `app/public/wp-content/plugins/cora-workspace/views/view-content-suite.php` (Content AI Suite, Bulk Actions Toolbar & Opportunities Grid)
- `app/public/wp-content/plugins/cora-workspace/views/view-feature-hub.php` (Directory, 6 Foundation Modules Lock, Rule 13 Tonal Fills)
- `app/public/wp-content/plugins/cora-workspace/public-client-portal.php` (Public White-Labeled Client Portal Engine)
- `app/public/wp-content/plugins/cora-workspace/includes/affiliate-referral-engine.php` (Affiliate Attribution, Dual-Rewards & Payouts Engine)
- `app/public/wp-content/plugins/cora-workspace/views/view-clients.php` (4-Subtab Client Management Suite)
- `app/public/wp-content/plugins/cora-workspace/views/view-client-task-manager.php` (Client Task Manager Board & Resizable Floating Drawer)
- `app/public/wp-content/plugins/cora-workspace/views/view-users.php` (People Ops AI Copilot, Multimodal Team Migration, Custom Role Drawer, Equal AI Token Budgeting)
- `app/public/wp-content/plugins/cora-workspace/cora-service-worker.js` (Web Push Delivery, VAPID Key Sync, High-Speed Cache Strategy)

---

## 3. Branch Activity & Progress Log

### `main` (Production Base)
- **Platform Version**: `4.9.316`
- **Health**: 100% Operational & Clean Slate Base (HEAD commit `a54aeceeb` on `feat/workspace-v4.9.316`). Canvas Live Next.js preview & proxy router, offline dev server detection, preview cookie isolation, 3-step Van Dispatch Wizard, weight allocation, sales register, manifest printing, Universal Remote MCP, and OAuth 2.1 PKCE server verified ✅.

<!-- BRANCH_LOGS_START -->
### `feat/workspace-v4.9.316` (Active Working Base)
- **Status**: 🟢 Active Working Base (v4.9.316, HEAD commit `a54aeceeb`) — Canvas Live Next.js Build Preview, Dev Server Proxy Router & Preview Cookie Isolation:
  - Embedded high-density, dark-themed Next.js App Preview Mockup card in Level 1 with real-time route chips and quick actions (`feat(canvas): add Next.js app preview card in level 1 and fast offline dev server detection`).
  - Implemented fast offline dev server detection via non-blocking sub-150ms probe ping to `http://localhost:3000` (`mode: 'no-cors'`). If offline, displays dedicated Terminal Instruction Card overlay (`npm run dev`).
  - Connected live Next.js build preview & proxy router, eliminating random fallback screens (`feat(canvas): connect live Next.js build preview & proxy router, eliminate random welcome fallback`).
  - Isolated preview cookie (`cora_canvas_preview`) strictly to explicit preview query parameters (`?preview_canvas=...` or `?preview=1`), purging preview state during standard site browsing to prevent leaking across sessions or unwanted theme activation.
- **Main Touchpoint**: `views/view-canvas.php`, `includes/class-cora-nextjs-integration.php`, `cora-workspace.php`.

### `feat/sales-items-registry-and-print` & `feat/inventory-and-van-sales` (Merged to Main)
- **Status**: 🟢 Complete & Merged (v4.9.314 - v4.9.315) — Stationery & Van Sales Dual Engine, Weight Allocation & Itemized Manifests:
  - Engineered structured 3-Step Van Dispatch Wizard (`1. Driver & Van Assignment` -> `2. Cargo & Weight Allocation` -> `3. Review & Dispatch Manifest`) computing gross, tare, and net weights with real-time stock decrements.
  - Implemented standardized weight allocation and spot billing pricing at ₹401.25/kg with browser-native GST tax invoice auto-print, QR codes, HSN breakdowns, and reverse-charge disclaimers.
  - Enforced consecutive 4-digit numeric bill numbers (`#1001`, `#1002`, etc.) with date-only formatting (`d M Y`) and customer/firm name positioned ahead of date.
  - Built comprehensive Sales Register ledger table with itemized breakdown, printable ledger, and 1-click UTF-8 itemized CSV export.
  - Added van stock print layout and available dispatch manifests with smart cargo fallback, delivering physical signed handover manifests for field drivers.
  - Aligned inventory reports and exports to exact mockup specifications with a 4-column layout (`SKU/Product`, `Category/Type`, `Available Stock`, `Valuation/Rate`) and 5-column dispatch report (`Manifest #`, `Driver / Van`, `Firm / Route`, `Weight Allocated`, `Status / Date`).
- **Main Touchpoint**: `includes/class-cora-inventory-engine.php`, `views/view-inventory-management.php`, `cora-workspace.php`.

### `feat/cora-workspace-core` (Merged to Main)
- **Status**: 🟢 Complete & Merged (v4.9.300 - v4.9.307) — Workspace Core, Canvas Live Theme Customizer & Finance Vault Sync:
  - Resolved pen icon routing in Canvas themes to directly open the Level 2 live theme customizer editor (`views/view-canvas.php`), preserving URL edit state and instant return navigation.
  - Added Canvas hover quick-actions directly in the sidebar navigation menu, enabling 1-click access to the live theme customizer and new-tab live site view.
  - Normalized sidebar navigation layout, removing extra vertical spacing below Canvas and aligning nav group items.
  - Unified bottom sidebar notification bell with canonical header notification drawer.
  - Hardened Finance & GST Invoicing Suite with multi-step creator validation, line items serialization, and automated Document Vault sync generating immutable PDF records with SHA-256 cryptographic hashes.
  - Re-engineered Media Manager dialogs into edge-to-edge side drawers and centered pop-up modals with curated color tag presets and custom color pickers.
- **Main Touchpoint**: `views/view-canvas.php`, `views/view-financials.php`, `views/view-vault.php`, `views/view-media.php`, `admin-dashboard.php`, `cora-workspace.php`.

### `feat/cora-guides-v2-digital-book` (Merged to Main)
- **Status**: 🟢 Complete & Merged (v4.9.210 - v4.9.230) — Universal Remote MCP Server & OAuth 2.1 PKCE Architecture:
  - Implemented Cora Universal Remote MCP Server (`includes/mcp/class-cora-universal-mcp-server.php`) exposing Model Context Protocol (v2026-07-28 / v2024-11-05) over HTTP JSON-RPC 2.0 and Server-Sent Events (SSE) streaming (`/wp-json/cora-mcp/v1/sse`, `/wp-json/cora-mcp/v1/messages`).
  - Built RFC 6749 / RFC 7636 OAuth 2.1 PKCE Authorization Server (`class-cora-oauth-server.php`) with cryptographic `S256` code challenges, authorization endpoint routing, pretty endpoint discovery, and explicit content-type consent screens.
  - Added RFC 7591 Dynamic Client Registration for Google Gemini, Cursor, ChatGPT, and Claude.
  - Implemented RFC 9728 Protected Resource Metadata (`/.well-known/oauth-protected-resource`) and `WWW-Authenticate` challenge headers.
  - Added universal token extraction for ChatGPT/Codex and JSON-RPC request hydration for Claude Web and ChatGPT.
  - Integrated 1-click effortless AI prompts and copyable connection setup in AI Tools MCP view (`views/view-mcp.php`) with interactive OAuth 2.1 connection test runner.
  - Focused assistant matrix on ChatGPT, Claude, and Gemini with official vector SVG logos and live connected status badges.
- **Main Touchpoint**: `includes/mcp/*`, `views/view-mcp.php`, `cora-workspace.php`.

### `feat/workspace-next` (Merged to Main)
- **Status**: 🟢 Complete & Merged (v4.9.268 - v4.9.273) — Administration Email Verification & CRM Lead Drawer Architecture:
  - Implemented full Administration Email verification workflow in Settings Suite: existing verified email stays locked with active verification badge, while new email receives a 32-byte cryptographic token confirmation link; added full-width email input, resilient AJAX error handling, authenticated mail transport via `heycora@claraverse.in`, and cross-subdomain auth guards.
  - Overhauled CRM Leads detail inspection into full-height edge-to-edge side drawers auto-portaled to `document.body` with transparent backdrop (eradicating dark trapped overlays), interactive left-edge drag resize with localStorage persistence, collapsible accordion groups, and strict team assignee roster sanitization with bi-directional Kanban sync.
  - Enforced 100 daily AI credits quota, zero-overlay confirmation modals, read-only OAuth bridge card, and dynamic workspace roles in account defaults.
- **Main Touchpoint**: `views/view-settings-suite.php`, `views/view-leads.php`, `views/verify.php`, `cora-workspace.php`.
### `security/audit-remediation` (Active Working Base)
- **Status**: 🟢 Complete & Active (v4.9.209) — Platform Security Hardening & Central Authorization Gatekeeper:
  - **SEC-001 & SEC-006**: Cryptographic Token Hardening with 32-byte CSPRNG (`random_bytes(32)`), SHA-256 hash-at-rest verification, single-use invalidation, and strict expiration checks across email verification and invitation links.
  - **SEC-002**: Centralized Tenant Authorization Engine (`class-cora-authorization.php`) with strict agency context assertions (`cora_assert_agency_context`) and object-level policy checks across Tasks, Forms, Vault documents, Canvas themes, and Media attachments.
  - **SEC-003**: Isolated backup directory placed securely outside web-accessible `ABSPATH`.
  - **SEC-004**: Patched Next.js (16.3.3) and Sharp (0.35.4) dependencies, achieving 0 npm audit vulnerabilities in `cora-frontend`.
  - **SEC-005**: WhatsApp Cloud API webhook signature verification using HMAC-SHA256 (`hash_hmac('sha256', $raw_payload, $app_secret)`) against `X-Hub-Signature-256`.
  - **SEC-007**: Mandatory CSRF nonce enforcement across all AI action execution endpoints.
  - **SEC-008**: Eradicated stored and DOM XSS vulnerabilities in repeatable form builders and mock checkout workflows.
  - **SEC-009**: Comprehensive SSRF Protection Filter (`class-cora-ssrf-filter.php`) blocking RFC1918 private subnets, loopback, link-local, and cloud metadata (169.254.169.254) addresses.
  - **SEC-010**: Plugin updater download host allowlisting preventing remote code execution.
  - **SEC-011**: Document Vault 500KB payload caps and SHA-256 contract immutability enforcement.
  - **SEC-012**: Scoped WordPress custom capabilities preventing tenant admin roles from gaining global site-admin or plugin installation privileges.
  - **SEC-013**: Client gallery password verification using `wp_check_password` and HMAC-SHA256 HttpOnly session cookies.
  - **SEC-014**: Exact hostname origin validation on Next.js frontend API proxy preventing unauthorized internal data disclosure.
  - **SEC-015**: Hardened HTTP security headers (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`) and Service Worker query exclusion preventing token leakage in caches.
  - **SEC-017**: Model Context Protocol (MCP) Bearer token authorization and tenant scoping.
  - **SEC-018**: Structured security audit logging with automated credential redaction.
- **Main Touchpoint**: `includes/class-cora-authorization.php`, `includes/class-cora-ssrf-filter.php`, `cora-workspace.php`, `views/verify.php`.

### `feature/email-verification-route-interception` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.208) — Immediate Route Interception for Email Verification & Auto-Login: Early route interception in `cora_workspace_handle_workspace_route()` resolving `/workspace/verify` prior to template redirection; supports pre-fetched link safety, automatic multi-meta verification flags (`cora_email_verified` and `cora_workspace_email_verified`), instant session initialization via `wp_set_auth_cookie()`, and dedicated fallback verification view in `views/verify.php`.
- **Main Touchpoint**: `cora-workspace.php`, `views/verify.php`.

### `feature/mobile-gpu-acceleration` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.207) — Mobile GPU Acceleration & Instant-Tap Optimization: Integrated hardware acceleration (`transform: translateZ(0); will-change: transform;`) across mobile bottom sheets, drawers, and floating islands; eliminated 300ms mobile touch delay via `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` across all interactive controls.
- **Main Touchpoint**: `assets/css/admin-style.css`, `admin-dashboard.php`.

### `feature/affiliates-gamified-hub` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.200 - v4.9.206) — Affiliates & Referrals Gamified Hub & Universal Foundation Placement:
  - Added Affiliates & Referrals (`affiliates`) as a permanent locked foundation module across all 6 industry module classes.
  - Overhauled partner dashboard into a gamified command center with tier progression meter (Bronze → Silver → Gold → Diamond) and active streak multiplier badge (`⚡ 3-Streak Active (+5% Bonus)`).
  - Built interactive partner leaderboard with Top 3 Podium cards (🥇, 🥈, 🥉) and 'Your Studio Ranking' highlight card.
  - Structured into 5 sticky edge-to-edge sub-navigation tabs (`Overview & Meter`, `Leaderboard & Ranks`, `Earnings Simulator`, `Referrals & Logs`, `Payouts & Banking`).
  - Overhauled reward structure: +100 Free AI Runes per verified signup, 20% Monthly Recurring Commission, 30% Annual Recurring Commission, and guaranteed milestone cash bonuses (10 Referrals → ₹1k, 50 Referrals → ₹5k, 100 Referrals → ₹10k).
  - Streamlined partner onboarding screener with 3 punchy revenue pillars and progressive disclosure accordions.
  - Purged neon styling and harsh outlines to 100% monochromatic Notion/Shopify standard.
- **Main Touchpoint**: `views/view-affiliate-referrals.php`, `includes/affiliate-referral-engine.php`, `cora-workspace.php`, `modules/*/class-*-module.php`.

### `feature/canvas-themes-email-revamp` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.199) — Canvas Themes UI/UX Revamp & High-Density Email Suite:
  - Merged Live Theme and Draft Themes into a single-stream Theme Library (`#tab-canvas-overview`) with 1-click publishing and quota tracking.
  - High-density Speed & Core Web Vitals diagnostic suite with 4-card metric strip (CWV score, LCP speed, Themes library quota, published pages) and 1-click `Optimize Now →` recommendation banner.
  - Edge-to-edge frosted sticky navigation bar with zero side gaps.
  - Viewable-only mobile navigation mode suppressing heavy builder/migration engines.
  - High-density 2x2 mobile / 1x4 desktop email KPI cards, industry email templates, and cross-module CRM contact sync (`wp_cora_clients`, `wp_cora_leads`).
- **Main Touchpoint**: `views/view-canvas.php`, `views/view-emails.php`, `cora-workspace.php`.

### `feature/system-settings-ai-controller` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.198) — Full AI Agent Control for System Settings Suite:
  - Empowered Cora AI as Principal Systems Architect with real-time RAG context across all 12 system settings modules.
  - Executable action tags for updating settings (`update_settings`), switching tabs (`switch_settings_tab`), triggering backups (`trigger_backup`), clearing caches (`clear_system_cache`), checking platform updates (`check_platform_updates`), and streaming activity logs (`view_activity_logs`).
  - Direct live DOM form input synchronization and Rule 3 privacy sanitization across settings.
- **Main Touchpoint**: `views/view-settings-suite.php`, `cora-workspace.php`.

### `feature/ai-tools-mcp-voice` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.193 - v4.9.197) — AI Tools MCP Gateway, Voice Assistant & RAG Normalization:
  - High-density personalized AI executive co-founder platform with dual mode switcher (Text Chat & Live Voice Mode).
  - Immersive live voice stage featuring central animated pulsing sphere orb (`.cora-voice-sphere`), soundwave waveform bars, Web Speech API recognition, ElevenLabs neural voice synthesis, and real-time live transcript stream.
  - Platform-unified workspace header and sticky sub-navigation tabs (`AI Assistant & Voice`, `MCP Developer Gateway`, `Living Memory RAG`).
  - Notion/Linear-grade Model Selector Popover (`[ ◆ Gemini 3.5 Flash | Real-time ▾ ]`) across Google Gemini, Anthropic Claude, OpenAI, and Groq Ultra-Fast.
  - Desktop sticky chat input dock (`position: sticky; bottom: 0;`) in full-height flex layout (`calc(100vh - 195px)`).
  - Zero-outline monochromatic surface styling on RAG domain vectors complying strictly with Rule 13.
- **Main Touchpoint**: `views/view-mcp.php`, `views/view-rag.php`, `cora-workspace.php`.

### `feature/media-management-ai-copilot` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.191 - v4.9.192) — Media Management AI Copilot & Floating Mobile Action Dock:
  - Trained Cora AI as executive Chief Creative Director & Media Asset Architect with real-time workspace storage telemetry and folder collections taxonomy.
  - Direct AI execution tags: `upload_media`, `create_folder`, `share_media`, `filter_media`, `inspect_media_telemetry`, `view_storage_breakdown`.
  - Floating monochromatic action dock (`#cm-mobile-bottom-bar`) positioned cleanly above the mobile island nav (`bottom: calc(76px + env(safe-area-inset-bottom))`) with 160px bottom scroll padding.
- **Main Touchpoint**: `views/view-media.php`, `cora-workspace.php`, `assets/css/admin-style.css`.

### `feature/user-management-ai-copilot` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.190) — User & People Ops AI Copilot:
  - Trained Cora AI as executive People Ops & Access Architect with real-time live workspace team intelligence.
  - Direct AI execution tags: `invite_member`, `edit_member_role`, `open_team_migration`, `open_permissions_matrix`, `filter_members`, `resend_invite`, `check_ai_quota`.
- **Main Touchpoint**: `views/view-users.php`, `cora-workspace.php`, `admin-dashboard.php`.

### `feature/core-foundation-modules-lock` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.189) — Core Foundation Modules Locking & Universal Feature Hub Governance: Universally locked foundation modules across all 6 industry modules (`class-custom-module.php`, `class-manufacturing-inventory-module.php`, `class-marketing-agency-module.php`, `class-studio-module.php`, `class-professional-services-module.php`, `class-re-module.php`). In `cora-workspace.php`, `cora_get_custom_enabled_features()` automatically merges foundation features into tenant capabilities regardless of toggle states. In `view-feature-hub.php`, locked foundation cards display an immutable `Foundation` badge with lock vector SVG and disabled toggle switch; card click handlers and batch actions (`select-all`, `deselect-all`, `reset-defaults`, `discard`) preserve foundation features.
- **Main Touchpoint**: `cora-workspace.php`, `views/view-feature-hub.php`, `modules/*/class-*-module.php`.

### `feature/media-proofing-telemetry` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.187 - v4.9.188) — Public Media Proofing Route Interception & Telemetry Suite: Public shared media route interception (`share-media.php`, `/workspace/shared-media/{token}`, `/workspace/share-media/{token}`, `/share-media.php?cora_share={token}`) delivering a 100% white-labeled monochromatic proofing view compliant with Claude aesthetic (`#FBFaf7`), token resolution, expiry verification, and password protection; comprehensive telemetry engine logging impression views (`cora_track_media_share_impression`) and asset downloads (`cora_track_media_share_download`); in `view-media.php`, rendered real-time KPI cards (Total Views, Unique Views, Total Downloads, Unique Downloads), interactive filter chips (`All`, `Downloads`, `Views`), and detailed audit trail with actor name, timestamp, and IP.
- **Main Touchpoint**: `share-media.php`, `views/view-media.php`, `cora-workspace.php`.

### `feature/custom-roles-module-scoping` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.184 - v4.9.186) — Custom Roles Dynamic Scoping & Scroll Freeze Resolution: Standardized custom role builder drawer to render real, human-readable module names instead of raw technical slugs; dynamically filtered capability checkboxes strictly to active modules enabled for the workspace; resolved mobile and desktop touch/scroll lock freeze on role drawers; added custom role deletion modal with cascading safety re-assignment; responsive mobile role cards with zero overflow.
- **Main Touchpoint**: `views/view-users.php`, `cora-workspace.php`, `assets/js/admin-script.js`, `assets/css/admin-style.css`.

### `feature/workspace-storage-breakdown` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.183) — Comprehensive Workspace Storage Footprint & Breakdown: Overhauled `cora_get_workspace_storage_details()` to calculate full multi-dimensional digital footprint including Media attachments (`wp_posts` + disk storage), Document Vault files, AI Chats/Memory/Knowledge footprint (`wp_cora_rag_knowledge`, cached options `cora_ai_*`, `cora_rag_*`, usermeta), and User Activity/Telemetry/Attendance footprint; visual breakdown in `view-media.php` with category badges and MB/GB formatting.
- **Main Touchpoint**: `cora-workspace.php`, `views/view-media.php`.

### `feature/field-ops-login-logout-telemetry` (Merged to Main)
- **Status**: 🟢 Merged to `main` (v4.9.182) — Field Ops Telemetry Lifecycle & Beacon Flush: Automatically initializes GPS employee tracking on login session detection (`cora-field-ops-tracker.js`, `admin-dashboard.php`); background heartbeat positioning pings; auto-stops tracking on user logout with `navigator.sendBeacon` payload flush on `beforeunload` / `pagehide` to capture clean punch-out timestamps and pending coordinates.
- **Main Touchpoint**: `assets/js/cora-field-ops-tracker.js`, `admin-dashboard.php`, `cora-workspace.php`.
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
| **v4.9.374** | Oct 2026 | Universal Edge-Docked Entry Inspector with zero-backdrop clarity, Client Timezone Engine with localized relative and absolute timestamps, Dynamic Affiliate Referral Tracking (`POST /wp-json/cora/v1/referrals/track`), multi-cookie referral attribution (`cora-frontend/lib/referral.ts`), automated checkout coupon discount deductions (10%–20% off plans in Razorpay checkout and pricing), and unified global resources hero across all resource directories |
| **v4.9.373** | Oct 2026 | Submissions data sanitization, base64 digital signature preview rendering, table-fixed explicit layout preventing horizontal blowouts, and REST deletion endpoint (`DELETE /wp-json/cora/v1/forms/submissions/{id}`) with tenant isolation |
| **v4.9.372** | Oct 2026 | Forms Cards Overhaul & Universal 5th Tab "All Entries" Registry (`#all-entries`): Expanded form card padding (`p-5 sm:p-6`), fully clickable form cards navigating to editor, unlocked mobile form editing across all screen widths, live interactive form preview modal with device switchers, metric summary strip, Notion-style filter toolbar, and dynamic multi-form CSV export |
| **v4.9.370** | Oct 2026 | Pricing Page Overhaul: Webflow/Framer style hierarchy pills, information tooltips across all cards, "Free Site Migration" floating badge on Scale plan, explicit bandwidth & storage specifications (15–50GB storage, 100–500GB bandwidth), pure monochrome switcher pills, and active-voice copy |
| **v4.9.368** | Oct 2026 | Partner & Coaching Accelerator Portal (`/vnxdigital`, `/vanshrajput`): Co-branded hero, creator social proof, and locked 3-day trial to Full Coaching Accelerator |
| **v4.9.366** | Oct 2026 | Unified Global Resources Hero Section (`GlobalResourcesHero.tsx`) across Templates, Tools, Guides, Blog, Newsletter, and Articles with consistent search and filter chips |
| **Marketing Reform** | Oct 2026 | Repositioned homepage hero headline to 'Build websites. Run your business, with an AI co-founder.', dual Google / Free Templates CTAs, sticky bottom AI SDR bar with automated objection handling and dynamic conversion coupons (MIGRATEFREE, CORA10), overhauled navbar to 'Tools & Templates', deployed 16+ multi-format templates and schemas at /templates (Native Pages, AI Prompts, Elementor Kits, Lovable Projects), updated autonomous AI workforce agent portraits, and overhauled executive confidence 6-feature grid with active-voice copy |
| **v4.9.364** | Oct 2026 | Standardized AI run quotas (15K Pro / 25K Scale monthly), added weekly soft-limit throttling (3,750 Pro / 6,250 Scale runs/week), Super Admin rate-limit bypass, integrated Endorsely affiliate tracking suite (`app.endorsely.com/assets/affiliate-tracking.js`) across frontend layouts and checkout sheets with dynamic conversion pinging |
| **v4.9.363** | Oct 2026 | Guaranteed live static site styling parity, automated Tailwind CSS CDN injection, and CSS scope containment across Canvas previews |
| **v4.9.362** | Oct 2026 | Combined fragmented editor edit actions into unified 'Edit with AI' toolbar button in Canvas Live Code Editor with instant diff previewing |
| **v4.9.361** | Oct 2026 | Cora AI editor interoperability and workspace forms embedding suite with `[cora-form id="..."]` shortcode and iframe generator for static sites |
| **v4.9.360** | Oct 2026 | Streamlined Live Code Editor header by removing redundant portrait and viewport switchers for clean, distraction-free editing |
| **v4.9.359** | Oct 2026 | Completed landing page template markup and universal live preview resilience shield across all 6 niche templates |
| **v4.9.358** | Oct 2026 | Live HTML preview resilience shield: auto-injection of Tailwind CSS CDN, error boundary encapsulation, postMessage bridge, and zero blank sections guarantee |
| **v4.9.357** | Oct 2026 | ChatGPT action discovery resolved with 28 curated tools ceiling, OpenAPI 3.0.1 schema hardening, empty property sanitization, and session discovery |
| **v4.9.356** | Oct 2026 | ChatGPT MCP server plugin handshake support, session tool discovery, and unified REST handler |
| **v4.9.355** | Oct 2026 | Upgraded OpenAPI to 3.0.1, curated 28 core actions ceiling for ChatGPT Custom GPT compliance, and purged emojis from action descriptions |
| **v4.9.354** | Oct 2026 | Universal read/write access and interactive OAuth 2.1 consent panel with master toggle, granular category permissions, and quick presets (`all`, `readonly`, `website`) |
| **v4.9.353** | Oct 2026 | Resolved JavaScript syntax parsing errors and restored canvas button click handlers across all editor toolbars |
| **v4.9.352** | Oct 2026 | Standardized canvas side drawer sheets (`.cora-drawer`), eliminated popup scroll jumps, locked body overflow (`body.cora-canvas-drawer-open`) |
| **v4.9.351** | Oct 2026 | Synchronized global MCP token and endpoint architecture (`/wp-json/cora-mcp/v1/openapi.json`), unified AI tools MCP gateway and live action playground directly into Canvas Template Editor |
| **v4.9.349** | Oct 2026 | OpenAI Developer Plugin deployment and zero-copy native MCP file management across all domains (`cora_canvas_update_page_html`, `cora_canvas_get_page_html`, `cora_canvas_create_theme`) |
| **v4.9.345** | Oct 2026 | High-fidelity realistic screenshot preview assets (`assets/img/tmpl_*`), edge-to-edge sticky bottom bar, zero emojis in Add Theme Wizard |
| **v4.9.344** | Oct 2026 | Add Theme Wizard UI Overhaul: active-voice 2x2 grid, sticky bottom bar, marketing link, template search filter, and 6 production templates (Agency, Consulting, E-Commerce, Photography, Real Estate, SaaS) |
| **v4.9.343** | Oct 2026 | Restored Super Admin workspace action buttons, direct launch routing (`/workspace/dashboard?agency_id={id}&industry={type}`), and modal portaling to `document.body` |
| **v4.9.342** | Oct 2026 | Universal in-app PWA install suite, settings trigger card in System Settings Suite, and sticky sidebar admin popover trigger |
| **v4.9.341** | Oct 2026 | Official Cora Super Admin PWA app icon suite with dynamic ADMIN badging (512x512, 192x192, Apple touch icons, favicons) |
| **v4.9.339** | Oct 2026 | Eliminated artificial 6-hour session limit and bypassed rate limits for admin users (`manage_options`) |
| **v4.9.338** | Oct 2026 | Super Admin dynamic PWA badging release packaging and version synchronization |
| **v4.9.337** | Oct 2026 | Security path containment with strict `realpath` checks, ZipArchive extraction traversal sanitization, and conditional HTTP 304 ETag caching |
| **v4.9.334 - v4.9.336** | Oct 2026 | Canvas AI Starter Prompts Grid (5 production template prompts: Agency, SaaS, Consulting, Photography, Custom) and native lead form ingestion bridge into `wp_cora_leads` |
| **v4.9.333** | Oct 2026 | Unbreakable 10-min website launch with instant HTML static mode and universal lead CRM bridge |
| **v4.9.317 - v4.9.331** | Oct 2026 | Automated Static Site Compiler & Exporter Pipeline (Next.js, Expo React Native Web, Vite) with CloudLinux Inode recovery, low-memory limits (`NODE_OPTIONS="--max-old-space-size=768"`), and asset stream router |
| **v4.9.316** | Oct 2026 | Canvas live Next.js build preview & proxy router, fast offline dev server detection (`http://localhost:3000`), preview cookie isolation (`cora_canvas_preview`) scoped to explicit query parameters, Level 2 live theme customizer routing, and theme activation scoping |
| **v4.9.315** | Oct 2026 | Exact 4-column plant inventory layout (`SKU/Product`, `Category/Type`, `Available Stock`, `Valuation/Rate`) and 5-column dispatch report reordering (`Manifest #`, `Driver / Van`, `Firm / Route`, `Weight Allocated`, `Status / Date`), itemized CSV export, printable van stock manifests, smart cargo fallback, sold item weights and unit counts |
| **v4.9.314** | Oct 2026 | 3-Step Van Dispatch Wizard (`Driver & Van` -> `Cargo & Weight` -> `Review & Dispatch`), standardized weight-based allocation (@ ₹401.25/kg), 4-digit numeric bill numbers (`#1001`), spot billing ledger, sales register with itemized breakdown, and browser-native GST tax invoice auto-print |
| **v4.9.313** | Oct 2026 | Scanned routes ingestion pipeline and Next.js theme badge in Canvas theme library |
| **v4.9.312** | Oct 2026 | Dual GitHub authorization headers fallback (`token <pat>` & `Bearer <pat>`) for private repository PAT authentication |
| **v4.9.311** | Oct 2026 | Dynamic GitHub branch discovery, private repository PAT access, route scanner hang elimination |
| **v4.9.310** | Oct 2026 | Next.js Git Connector Wizard routing directly from Canvas "Add Theme" button |
| **v4.9.309** | Oct 2026 | Unified notification architecture synchronizing sidebar bottom bell with canonical header notification drawer |
| **v4.9.308** | Oct 2026 | Headless Next.js integration engine (`class-cora-nextjs-integration.php`), App Router and Pages Router scanner, headless REST APIs, on-demand ISR revalidation webhooks (`/api/revalidate`) |
| **v4.9.307** | Oct 2026 | Canvas pen icon routing to Level 2 live theme customizer editor with URL edit state persistence |
| **v4.9.306** | Oct 2026 | Sidebar layout normalization, removal of extra vertical spacing below Canvas, nav group alignment |
| **v4.9.305** | Oct 2026 | Canvas hover quick-actions in sidebar for instant live theme customizer and new-tab live site view |
| **v4.9.304** | Oct 2026 | GST invoice publishing stabilization, default input guards, automated Document Vault sync with SHA-256 hash |
| **v4.9.303** | Oct 2026 | GST invoice multi-step creator validation, line items serialization, and vault linking |
| **v4.9.302** | Oct 2026 | CRM team assignee roster sanitization and bi-directional lead drawer state synchronization |
| **v4.9.301** | Oct 2026 | Leads side drawer left-edge draggable resize with localStorage memory, snap button removal, collapsible accordion groups |
| **v4.9.300** | Oct 2026 | Edge-to-edge full-height side drawers, auto-portaling to `document.body`, transparent backdrop overlay, lead data sanitization |
| **v4.9.298** | Sep 2026 | Curated color tag presets and custom color picker for Media Manager folders |
| **v4.9.296** | Sep 2026 | Centered pop-up modals for Media folder creation and settings with transparent backdrop |
| **v4.9.295** | Sep 2026 | Redesigned Media Manager dialogs into edge-to-edge side drawers with zero dark overlay |
| **v4.9.290** | Sep 2026 | AI Team Migration converted to edge-to-edge side drawer, eliminate dark overlays, robust open/close state machine |
| **v4.9.285** | Sep 2026 | Autonomous AI content writing, editing, state management, and rich card previews |
| **v4.9.280** | Sep 2026 | Dynamic task notification alerts and SVG search iconography upgrade |
| **v4.9.279** | Sep 2026 | Streamlined admin profile popover with live AI Connected status badge and zero viewport clipping |
| **v4.9.276** | Sep 2026 | AI real-time SPA navigation router executing direct client navigation and actions from user prompts |
| **v4.9.274** | Sep 2026 | Continuous RAG living memory self-learning, ultra-concise 1-2 line responses, 1-click CTA action pills |
| **v4.9.269 - v4.9.273** | Sep 2026 | Administration email verification workflow with dynamic badges (`Verified` / `Pending Verification`), tokenized confirmation links, authenticated mail delivery (`heycora@claraverse.in`), and robust AJAX handling |
| **v4.9.266 - v4.9.267** | Sep 2026 | Web Push notification subsystem overhaul, service worker push delivery, dynamic VAPID key sync, Super Admin device subscription telemetry |
| **v4.9.263** | Sep 2026 | Enforced 100 daily AI credits quota, zero-overlay confirmation modals, read-only OAuth bridge card, dynamic roles in account defaults |
| **v4.9.262** | Sep 2026 | PWA custom app icon branding and 1-click broadcast push updates |
| **v4.9.235 - v4.9.239** | Sep 2026 | Super Admin Danger Zone and workspace deletion confirmation modal (zero dark overlay, quoted IDs, option-store cascade removal) |
| **v4.9.232** | Sep 2026 | Unified single-domain routing architecture, clean URLs without workspace slug, archive ref preservation |
| **v4.9.229 - v4.9.230** | Sep 2026 | RFC 7591 Dynamic Client Registration for Google Gemini and connected MCP clients |
| **v4.9.228** | Sep 2026 | ChatGPT actions discovery with underscore tool schema, resilient CRM ingestion, official vector AI logo suite |
| **v4.9.220 - v4.9.224** | Sep 2026 | Universal token extraction, JSON-RPC request hydration for Claude Web and ChatGPT, pretty OAuth endpoint discovery, consent screen redirect |
| **v4.9.210 - v4.9.219** | Sep 2026 | Cora Universal Remote MCP Server (`/wp-json/cora-mcp/v1/sse`), OAuth 2.1 PKCE server, RFC 9728 Protected Resource Metadata, `WWW-Authenticate` headers, 1-click AI prompts for ChatGPT, Claude, and Gemini |
| **v4.9.209** | Sep 2026 | Complete platform security hardening (SEC-001 - SEC-023), Central Authorization Gatekeeper (`class-cora-authorization.php`), Outbound HTTP SSRF Filter (`class-cora-ssrf-filter.php`), WhatsApp HMAC-SHA256 signature verification, 32-byte cryptographic token entropy, Next.js 16.3.3 & Sharp 0.35.4 patches |
| **v4.9.208** | Sep 2026 | Immediate route interception for email verification & magic auto-login (`/workspace/verify` via `views/verify.php`), pre-fetched link safety, and instant auth session cookie hydration |
| **v4.9.207** | Sep 2026 | Mobile GPU hardware acceleration (`transform: translateZ(0); will-change: transform;`) and universal instant-tap responsiveness (`touch-action: manipulation; -webkit-tap-highlight-color: transparent;`) |
| **v4.9.206** | Sep 2026 | Actionable & streamlined partner onboarding screener with 3 revenue pillars (30% Annual / 20% Monthly, milestone bonuses, 100 Free AI Runes) and progressive disclosure accordions |
| **v4.9.205** | Sep 2026 | Desktop agenda daily tasks integration, dashboard clutter reduction, monochromatic admin popover card, and sub-tab click interactivity fix |
| **v4.9.204** | Sep 2026 | Edge-to-edge sticky sub-navigation tabs, zero horizontal scrolling responsive card feeds across Leaderboard/Referrals/Payouts, and monochromatic UI polish |
| **v4.9.203** | Sep 2026 | Affiliate onboarding screener & reward model overhaul (+100 Free AI Runes, 20% Monthly / 30% Annual recurring commission, ₹1k/₹5k/₹10k milestone cash bonus roadmap) |
| **v4.9.202** | Sep 2026 | Gamified Partner Dashboard & Earnings Meter (Bronze → Silver → Gold → Diamond), streak multiplier badge (`⚡ 3-Streak Active (+5% Bonus)`), 5 sticky edge-to-edge sub-tabs, and Top 3 Podium leaderboard |
| **v4.9.201** | Sep 2026 | End-to-end functional sign in & registration flow, robust email verification link system with 24-hour expiration, automated workspace provisioning, and resilient resend verification |
| **v4.9.200** | Sep 2026 | Permanently locked Affiliates & Referrals (`affiliates`) into Workspace Foundation across all 6 industry module classes with alphanumeric sidebar badge support |
| **v4.9.199** | Sep 2026 | Canvas Themes UI/UX Revamp (unified Theme Library `#tab-canvas-overview`, 4-metric Speed/CWV diagnostic strip, edge-to-edge frosted sticky tabs, viewable-only mobile mode) & High-Density Email module |
| **v4.9.198** | Sep 2026 | Full AI Agent Management & Synchronized Control in System Settings Suite (`update_settings`, `switch_settings_tab`, `trigger_backup`, `clear_system_cache`), live DOM input sync, Rule 3 privacy sanitization |
| **v4.9.197** | Sep 2026 | Desktop sticky chat input dock in AI MCP view (`views/view-mcp.php`, `calc(100vh - 195px)`, `position: sticky; bottom: 0;`) |
| **v4.9.196** | Sep 2026 | Zero-outline monochromatic design compliance in Living Memory RAG domain vectors and border normalization |
| **v4.9.195** | Sep 2026 | Platform-unified workspace header in AI Tools MCP, Linear-grade Model Selector Popover (`[ ◆ Gemini 3.5 Flash | Real-time ▾ ]`), responsive control ribbon, and drawer pointer lock protection |
| **v4.9.194** | Sep 2026 | Unified sticky sub-navigation tabs in AI Tools MCP, mobile ergonomics and layout unification for Living Memory RAG |
| **v4.9.193** | Sep 2026 | Personalized AI Assistant & Live Voice Mode in AI Tools MCP (`views/view-mcp.php`) with pulsing sphere orb (`.cora-voice-sphere`), soundwave bars, ElevenLabs synthesis, and personalized prompt matrix |
| **v4.9.192** | Sep 2026 | Mobile floating action dock (`#cm-mobile-bottom-bar`) positioned cleanly above island nav in Media Library with 160px bottom scroll padding |
| **v4.9.191** | Sep 2026 | Action-oriented AI Copilot for Media Management & Library (`upload_media`, `create_folder`, `share_media`, `filter_media`, `inspect_media_telemetry`, `view_storage_breakdown`) |
| **v4.9.190** | Sep 2026 | Action-oriented AI Copilot for User & Crew Management (`invite_member`, `edit_member_role`, `open_team_migration`, `open_permissions_matrix`, `filter_members`, `resend_invite`, `check_ai_quota`) |
| **v4.9.189** | Sep 2026 | Lock core foundation modules (`blogs`, `forms`, `team-roles`, `media`, `vault` + `dashboard`) across all industry modules and Feature Hub UI with immutable badge and toggle lock |
| **v4.9.188** | Sep 2026 | Comprehensive media proofing telemetry with Total/Unique views and downloads tracking, filter chips, and access log audit trail (`view-media.php`, `cora-workspace.php`) |
| **v4.9.187** | Sep 2026 | Public shared media route interception (`share-media.php`, `/workspace/shared-media/{token}`) delivering monochromatic proofing view compliant with Claude aesthetic |
| **v4.9.186** | Sep 2026 | Standardize custom role builder to use real module names and strictly filter permissions to active workspace modules |
| **v4.9.185** | Sep 2026 | Resolve mobile and desktop touch/scroll lock freeze on custom role drawers |
| **v4.9.184** | Sep 2026 | Custom role deletion confirmation modal, dynamic workspace capabilities, and zero-overflow mobile cards (`view-users.php`) |
| **v4.9.183** | Sep 2026 | Include AI chats, vectors, and user activity logs in multi-dimensional workspace storage calculation and UI breakdown (`cora-workspace.php`, `view-media.php`) |
| **v4.9.182** | Sep 2026 | Auto-start employee GPS tracking on login session detection and auto-stop with `navigator.sendBeacon` flush on logout (`cora-field-ops-tracker.js`) |
| **v4.9.181** | Sep 2026 | Prevent text truncation of staff name, date, time, and exact GPS coordinates on mobile attendance cards (`view-users.php`) |
| **v4.9.180** | Sep 2026 | Divide workspace monthly AI token credits equally among active team members by default, dynamic industry roles, and declutter user edit drawer |
| **v4.9.179** | Sep 2026 | Persist notification channel toggles and trigger matrix across reloads (`view-settings-suite.php`), and make settings sub-tabs sticky with `pan-x` touch swipe |
| **v4.9.178** | Sep 2026 | Universal role permissions fallback and defensive group shield (`cora_filter_sidebar_nav_by_role`) preventing empty sidebar menus |

---

*Cora Platform Release Manifest v4.9.374 — Architecture & Engineering Team.*
