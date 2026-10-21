# Cora Platform — Module Progress & Branch Synchronization Manifest

> **Single Source of Truth for Multi-Branch Parallel Development**
> This file is maintained to ensure parallel agent execution and feature branches remain fully synchronized and never break shared platform APIs or files.

---

## 1. Branch Index & Active Modules

| Module Name | Branch Name | Status | Main Touchpoint Files | Assigned Agent / Chat |
|---|---|---|---|---|
| **Core Platform** | `main` | 🟢 Stable (v4.9.420) | `cora-workspace.php`, `admin-dashboard.php` | Main Orchestrator |
<!-- MODULE_ROWS_START -->
| **Workspace-Native Next.js Static Export & Smart Asset Router** | `main` | 🟢 100% Production Ready (v4.9.420) | `cora-workspace.php`, `includes/class-cora-nextjs-integration.php`, `views/view-canvas.php` | Next.js Architecture Agent |
| **Canvas Performance & Decision-Driven Telemetry Intelligence Hub** | `main` | 🟢 100% Production Ready (v4.9.419) | `includes/class-cora-performance-telemetry.php`, `views/view-canvas.php` | Telemetry & Performance Agent |
| **Cora Business Email & Outreach Campaigns Suite v2** | `main` | 🟢 100% Production Ready (v4.9.419) | `includes/class-cora-email-campaigns-engine.php`, `views/view-emails.php` | Email & Outreach Systems Agent |
| **Affiliate & Referral Partner Hub Redesign (HeroUI & ShadCN Anatomy)** | `main` | 🟢 100% Production Ready (v4.9.419) | `views/view-affiliate-referrals.php` | Growth & Frontend Design Agent |
| **ChatGPT Action Discovery & 30-Action OpenAPI Ceiling Engine** | `main` | 🟢 100% Production Ready (v4.9.417) | `cora-workspace.php`, `includes/mcp/*` | AI & Protocol Architecture Agent |
| **Canvas Global Pages, HTML 3D Deployments & Next.js Static Builds** | `main` | 🟢 100% Production Ready (v4.9.415) | `cora-workspace.php`, `includes/class-cora-nextjs-integration.php` | Canvas Architecture Agent |
| **CLA / ChatGPT Autonomous Workspace Operator** | `main` | 🟢 100% Production Ready (v4.9.412) | `includes/mcp/class-cora-oauth-server.php`, `includes/mcp/class-cora-mcp-tool-registry.php`, `scripts/test_cla_chatgpt_mcp_e2e.php` | Autonomous AI & Systems Operator Agent |
| **Dynamic Multi-Channel Template Engine** | `main` | 🟢 100% Production Ready (v4.9.412) | `includes/class-cora-template-engine.php`, `views/view-canvas.php` | Canvas Architecture Agent |
| **Universal Official Vector AI Brand Logos Engine** | `main` | 🟢 100% Production Ready (v4.9.412) | `cora-frontend/components/features/OfficialBrandLogos.tsx`, `includes/workspace-header.php`, `views/view-mcp.php` | Design System & Brand Agent |
| **Top 3 AI Market Leader Connectors & Minimal OAuth** | `main` | 🟢 100% Production Ready (v4.9.407) | `includes/mcp/class-cora-oauth-server.php`, `includes/partials/workspace-developer-gateway-drawer.php` | AI & Integration Architecture Agent |
| **1-Click Gemini MCP Connector & Autonomous Campaigns** | `main` | 🟢 100% Production Ready (v4.9.406) | `includes/class-cora-autonomous-campaigns.php`, `scripts/test_autonomous_campaigns_e2e.php` | Content & Autonomous Marketing Agent |
| **WhatsApp Cloud API Zero-Cost CSW Queue & Inbound Bot Engine** | `main` | 🟢 100% Production Ready (v4.9.401) | `includes/class-cora-whatsapp-gateway.php`, `cora-workspace.php`, `scripts/test_whatsapp_csw_automation.php` | WhatsApp & Messaging Agent |
| **Super Admin Integrations & WhatsApp Telemetry Hub** | `main` | 🟢 100% Production Ready (v4.9.397) | `views/view-super-admin.php`, `cora-workspace.php` | Super Admin & Telemetry Agent |
| **Workspace Content REST API & Canonical 9-Tool MCP Suite** | `main` | 🟢 100% Production Ready (v4.9.391) | `includes/class-cora-workspace-content-rest-controller.php`, `includes/mcp/class-cora-mcp-tool-registry.php`, `scripts/test_content_suite_e2e.php` | Content & API Architecture Agent |
| **Workspace Developer API & MCP Gateway Docked Drawer** | `main` | 🟢 100% Production Ready (v4.9.391) | `includes/partials/workspace-developer-gateway-drawer.php`, `includes/workspace-header.php` | Developer Experience & UI Agent |
| **Google Business Profile & Google Maps AI Autopilot** | `main` | 🟢 100% Production Ready (v4.9.389) | `views/view-google-profile.php`, `cora-workspace.php`, `assets/js/admin-script.js`, `tests/e2e/test-gbp-management.spec.ts` | Local SEO & Integrations Agent |
| **Plugins in App Module & Open API / Dynamic MCP Architecture** | `main` | 🟢 100% Production Ready (v4.9.387) | `includes/class-cora-plugins-skills-engine.php`, `views/view-feature-hub.php`, `includes/mcp/class-cora-mcp-tool-registry.php`, `admin-dashboard.php`, `cora-workspace.php` | AI & Extensibility Architect |
| **Enterprise Client Onboarding Suite & Portal Role Isolation** | `main` | 🟢 100% Production Ready (v4.9.387) | `views/view-clients.php`, `public-client-portal.php`, `cora-workspace.php`, `scripts/verify_client_onboarding_features.php` | Client Operations & Security Agent |
| **Interactive Workspace Scheduling Calendar & Google Meet** | `main` | 🟢 100% Production Ready (v4.9.387) | `views/view-calendar.php`, `cora-workspace.php`, `admin-dashboard.php` | Scheduling & Integrations Agent |
| **Collapsible Sidebar Navigation Groups & Memory State** | `main` | 🟢 Complete & Active (v4.9.386) | `admin-dashboard.php`, `assets/css/admin-style.css`, `assets/js/admin-script.js` | UI & Navigation Agent |
| **Mobile Sticky Sub-Tabs, Zero-Gap Anchoring & 28px Search Target** | `main` | 🟢 Complete & Active (v4.9.385) | `admin-dashboard.php`, `includes/workspace-header.php`, `assets/css/admin-style.css` | Mobile & Navigation Agent |
| **Global Sticky Workspace Header & Mobile Expandable Search** | `main` | 🟢 Complete & Active (v4.9.384) | `admin-dashboard.php`, `includes/workspace-header.php`, `assets/css/admin-style.css`, `assets/js/admin-script.js` | Mobile & Navigation Agent |
| **Sub-Tabs Extra Actions & Level 01 Segmented Switcher** | `main` | 🟢 Complete & Active (v4.9.383) | `includes/workspace-header.php`, `views/view-projects.php` | Navigation & UI Agent |
| **Shopify-Inspired Canvas Maximize & Instant Sidebar Sync** | `main` | 🟢 Complete & Active (v4.9.382) | `views/view-projects.php`, `admin-dashboard.php` | Canvas Visual Engine Agent |
| **Shopify-Inspired Docked Drawer Architecture** | `main` | 🟢 Complete & Active (v4.9.382) | `views/view-projects.php`, `admin-dashboard.php` | Workspace Core & UI Agent |
| **Agency Projects & Portfolio Case Studies Module** | `main` | 🟢 100% Production Ready (v4.9.384) | `modules/projects/*`, `views/view-projects.php`, `cora-frontend/app/projects/*` | Portfolio & Case Studies Agent |
| **Public Micro-Tools Growth Engine** | `main` | 🟢 100% Production Ready (v4.9.381) | `cora-frontend/app/tools/*`, `cora-frontend/lib/tools-canonical-registry.ts` | Growth & Micro-Tools Agent |
| **Canvas Themes GitHub CI/CD & Rollback Engine** | `main` | 🟢 100% Production Ready (v4.9.381) | `includes/class-cora-github-integration.php`, `views/view-canvas.php` | Canvas Integration & DevOps Agent |
| **Canonical Industry Directory & Slug Migration** | `main` | 🟢 Complete & Active (v4.9.380) | `cora-frontend/app/industries/*`, `cora-frontend/lib/industry-data.ts`, `cora-frontend/components/layout/Navbar.tsx` | Industry Hub & Navigation Agent |
| **Autonomous AI Workforce & Infinite Skills Marquee** | `main` | 🟢 Complete & Active (v4.9.379) | `cora-frontend/app/ai-agent/page.tsx`, `components/ai-agent/InfiniteSkillsSection.tsx`, `scripts/deploy_frontend.py` | AI Workforce & Deployment Agent |
| **Features Interactive Catalog & Lenis Scroll Isolation** | `main` | 🟢 Complete & Active (v4.9.378) | `cora-frontend/app/features/page.tsx`, `components/features/FeaturesSidebar.tsx` | Features & Performance Agent |
| **Enterprise Brand Registry & SVG Download Kit** | `main` | 🟢 Complete & Active (v4.9.377) | `cora-frontend/app/brand/page.tsx`, `components/brand/*` | Brand & Design System Agent |
| **Agency Story Canvas & Chronological Journey Ribbon** | `main` | 🟢 Complete & Active (v4.9.376) | `cora-frontend/app/about/page.tsx`, `components/about/ScrollDrivenTimeline.tsx` | Storytelling & UX Agent |
| **Contact Multi-Field Validation & Feedback Suite** | `main` | 🟢 Complete & Active (v4.9.375) | `cora-frontend/app/contact/page.tsx` | Forms & Client Validation Agent |
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
| **Client Management Suite** | `main` | 🟢 Complete & Active (v4.9.387) | `views/view-clients.php`, `views/view-financials.php`, `scripts/verify_client_onboarding_features.php` | CRM Client Suite Agent |
| **Public White-Labeled Client Portal**| `main` | 🟢 Complete & Active (v4.9.387) | `public-client-portal.php`, `cora-workspace.php` | Client Portal Agent |
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
- `app/public/wp-content/plugins/cora-workspace/includes/class-cora-plugins-skills-engine.php` (Dynamic Plugins, Modules & AI Skills Runtime Engine, Isolated Custom DB Schemas, REST Endpoints, API Keys, Dynamic MCP 2.0 Integration)
- `app/public/wp-content/plugins/cora-workspace/views/view-calendar.php` (Interactive Workspace Scheduling Calendar, Month/Week/Day Views, Google Calendar Two-Way Sync, Google Meet 3-4-3 Format Standardization)
- `app/public/wp-content/plugins/cora-workspace/views/view-google-profile.php` (Google Business Profile & Google Maps AI Autopilot Hub, Multi-Branch Locations, Live Sync, AI Review Replies, 7-Day Hours, Maps Post Publisher, Local SEO 3-Pack Rank Tracker)
- `app/public/wp-content/plugins/cora-workspace/includes/class-cora-whatsapp-gateway.php` (WhatsApp Cloud API Gateway v22.0, Zero-Cost 24h CSW Queue, Inbound Bot Router, 3/hr Rate Limiter, Executive PDF Report Delivery)
- `app/public/wp-content/plugins/cora-workspace/includes/class-cora-workspace-content-rest-controller.php` (Workspace Content REST API, 11-Point Algorithmic SEO & GEO Audit Engine, Full CRUD, Batch Publishing, Keyword Opportunity Engine)
- `app/public/wp-content/plugins/cora-workspace/includes/partials/workspace-developer-gateway-drawer.php` (Workspace Developer API & MCP Gateway Docked Drawer, Rule 15 SOP Compliant, API Key Generation, Claude/Cursor Config, Live Sandbox)

---

## 3. Branch Activity & Progress Log

### `main` (Production Base)
- **Platform Version**: `4.9.419`
- **Health**: 100% Operational & Clean Slate Base (HEAD commit on `main`). Canvas Performance & Decision-Driven Telemetry Intelligence Hub (`class-cora-performance-telemetry.php`, 6 decision KPIs, RUM Core Web Vitals, GSC striking distance), Cora Business Email & Outreach Campaigns Suite v2 (`class-cora-email-campaigns-engine.php`, 1-20 inboxes, round-robin rotation, Spintax, 8 sub-tabs), Affiliate & Referral Partner Hub Redesign (HeroUI & ShadCN Anatomy), ChatGPT Action Discovery & 30-Action OpenAPI Ceiling Engine (`?limit=28`), CLA / ChatGPT Autonomous Workspace Operator Integration (`scripts/test_cla_chatgpt_mcp_e2e.php`, RFC 8414 userinfo, dynamic plugin manifest, 246 tool catalog, 9 universal handlers), Dynamic Multi-Channel Template Engine (`includes/class-cora-template-engine.php`), Universal Official AI Brand Logos Engine (`OfficialBrandLogos.tsx`), Top 3 Market Leader Connectors & Minimal High-Trust OAuth, 1-Click Gemini MCP Connector & Autonomous Campaigns, and WhatsApp Zero-Cost CSW Queue Engine verified ✅.

<!-- BRANCH_LOGS_START -->
### `feat/canvas-performance-telemetry-hub` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.419) — Canvas Performance & Decision Telemetry Command Center:
  - Engineered 100% self-hosted, cookieless telemetry engine in `includes/class-cora-performance-telemetry.php` logging to `wp_cora_web_analytics`.
  - Passive frontend beacon (`<1KB`) with `navigator.sendBeacon` and passive RUM Core Web Vitals observers (LCP, CLS).
  - Dynamic Executive Decision Directive Banner recommending highest-impact growth and performance fixes.
  - 6-Card Decision KPI Matrix: Live Traffic Pulse (5m), Unique Visitors, Dwell Time & Bounce, Closed-Loop Form Conversion, Real User Core Web Vitals, Platform Latency & Incidents.
  - 4 Segmented Sub-Views: Traffic & Acquisition, Top Pages, Speed & PageSpeed Diagnostics, Search & SEO striking distance keywords.
  - Multi-Tab Settings Drawer (`openPerformanceAnalyticsSettingsDrawer`) for PageSpeed API, GSC Service Account, Native Telemetry, and Uptime Webhooks.
- **Main Touchpoint**: `includes/class-cora-performance-telemetry.php`, `views/view-canvas.php`, `cora-workspace.php`.

### `feat/business-email-campaigns-suite-v2` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.419) — Cora Business Email & Outreach Suite v2:
  - Multi-Account Business Email Architecture supporting 1 to 20 inboxes across 4 tiers (Starter, Growth, Pro, Enterprise) for Google Workspace and Hostinger Webmail.
  - Automated round-robin sender rotation across connected active inbox pool.
  - Automated 14-day gradual warmup ramp and deliverability health monitor (SPF, DKIM, DMARC).
  - Spintax variation engine (`parse_spintax`) for dynamic personalization.
  - Multi-step cold outreach drip cadences with automated reply detection & stop triggers.
  - Marketing mass broadcast segmenter and scheduler.
  - 8 Modern Edge-to-Edge Sub-Navigation Tabs in `views/view-emails.php`.
- **Main Touchpoint**: `includes/class-cora-email-campaigns-engine.php`, `views/view-emails.php`, `cora-workspace.php`.

### `feat/affiliate-referrals-heroui-shadcn` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.419) — Affiliate Partner Hub Redesign (HeroUI & ShadCN Anatomy):
  - Complete UI overhaul of `views/view-affiliate-referrals.php` adopting HeroUI & ShadCN component architecture.
  - Modular HeroUI Card (Header, Body, Footer), Elevated Segmented Pill Tabs with micro-counters, Smooth Progress Track with milestones, Bordered Snippet with copy feedback, Squircle Avatar with font-mono, Status Chips & Badges, Interactive Deal Multiplier Slider.
  - 5 Dedicated Views: `overview`, `leaderboard`, `calculator`, `referrals`, `payouts`.
  - Strict Rule 4 and Rule 13 compliance: zero heavy outlines, soft tonal surface fills (`bg-zinc-100 dark:bg-zinc-800`), and pure light mode priority.
- **Main Touchpoint**: `views/view-affiliate-referrals.php`.

### `feat/chatgpt-action-discovery-30-ceiling` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.417) — ChatGPT Action Discovery & 30-Action OpenAPI Ceiling:
  - Curated 28-tool OpenAPI schema (`?limit=28`) strictly enforcing OpenAI's 30-action ceiling to eliminate Custom GPT schema import failures, while exposing full 246 tools unconstrained via `?all=1` and direct MCP.
  - Parameter schemas sanitized with zero emojis and non-empty property objects.
- **Main Touchpoint**: `cora-workspace.php`, `includes/mcp/class-cora-mcp-tool-registry.php`.
### `feat/cla-mcp-autonomous-operator` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.412) — CLA / ChatGPT Autonomous Workspace Operator Integration:
  - Resolved ChatGPT `"not_installed"` issue via RFC 8414 Dynamic Userinfo handshake implementation (`/oauth/userinfo` and `/wp-json/cora-oauth/v1/userinfo`).
  - Dynamic AI Plugin Manifest (`ai-plugin.json`) supporting both `'cla'` and `'cora'` identity namespaces.
  - Full MCP Catalog Expansion: Auto-generates 246 tool aliases across canonical, `cla_*`, and `cora_*` namespaces without restrictive truncation.
  - Implemented 9 Universal Core Operator Tool Handlers: `list_workspaces`, `get_workspace`, `get_authenticated_account`, `upload_media`, `list_media`, `get_media`, `delete_media`, `get_analytics`, `get_financial_summary`.
  - Enforced strict multi-tenant database isolation across tasks, media vault, and CRM leads.
  - Validated 100% (12/12) automated end-to-end operational verification steps (`scripts/test_cla_chatgpt_mcp_e2e.php`).
- **Main Touchpoint**: `includes/mcp/class-cora-oauth-server.php`, `includes/mcp/class-cora-mcp-tool-registry.php`, `includes/mcp/class-cora-universal-mcp-server.php`, `scripts/test_cla_chatgpt_mcp_e2e.php`.

### `feat/dynamic-multi-channel-template-engine` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.412) — Dynamic Multi-Channel Template Engine:
  - Engineered Shopify-style dynamic presentation layer in `includes/class-cora-template-engine.php` decoupling content entities (Articles, Pages, Policies, Products, Collections) from themes.
  - Self-healing database table `wp_cora_canvas_templates` for storing archetype definitions, slot layouts, and localized rendering configurations.
  - Zero Content Loss Guarantee: Allows agency owners to hot-swap website themes without losing published case studies, forms, or editorial articles.
  - Media Selector Modal Integration (`partials/workspace-media-selector-modal.php`) and Content Approval Drawer (`views/partials/content-approval-drawer.php`).
- **Main Touchpoint**: `includes/class-cora-template-engine.php`, `views/view-canvas.php`, `includes/partials/workspace-media-selector-modal.php`.

### `feat/universal-official-ai-brand-logos` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.412) — Universal Official AI Brand Logos Engine:
  - Standardized authentic official vector brand logos and official colors for OpenAI/ChatGPT (`#10A37F`), Anthropic Claude (`#D97706`), Google Gemini (official 4-point sparkle gradient `#4E82EE`→`#9B72CB`→`#D96570`), Perplexity (`#20808D`), and YouTube (`#FF0000`).
  - Replaced all outdated monochromatic and placeholder SVGs across Frontend (`OfficialBrandLogos.tsx`, `CapabilityVisualCard.tsx`, `ProductShowcase.tsx`) and Workspace (`workspace-header.php`, `view-mcp.php`, `view-canvas.php`, `view-settings-suite.php`, `admin-dashboard.php`).
- **Main Touchpoint**: `cora-frontend/components/features/OfficialBrandLogos.tsx`, `includes/workspace-header.php`, `views/view-mcp.php`.

### `feat/mcp-top-3-connectors-minimal-oauth` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.407) — Top 3 AI Connectors & Minimal High-Trust Consent:
  - Pre-engineered 1-click connectors for Google Gemini, Anthropic Claude, and OpenAI ChatGPT.
  - Minimal High-Trust OAuth Consent Screen (`class-cora-oauth-server.php`): Streamlined visual hierarchy eliminating security friction, real-time scope counter, 1-click presets ("Full Access Operator", "Read-Only Auditor", "Website Only").
  - Live telemetry handshakes and heartbeat verification monitoring active AI assistant connections.
- **Main Touchpoint**: `includes/mcp/class-cora-oauth-server.php`, `includes/partials/workspace-developer-gateway-drawer.php`.

### `feat/gemini-mcp-autonomous-campaigns` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.406) — 1-Click Gemini Connector & Autonomous Campaigns Engine:
  - Implemented 1-click seamless Gemini MCP connector using OAuth `client_credentials` grant for instantaneous server-to-server token exchange.
  - Autonomous Content Campaigns Engine (`includes/class-cora-autonomous-campaigns.php`): Manages scheduled cross-channel topic campaigns (daily, 3x/week, weekly, bi-weekly), high-intent keyword opportunity matching, 11-point algorithmic SEO & GEO quality gating (min SEO: 80, min GEO: 85), and self-improving content optimization.
  - Automated test runner `scripts/test_autonomous_campaigns_e2e.php` verifying campaign lifecycle and AI generation.
- **Main Touchpoint**: `includes/class-cora-autonomous-campaigns.php`, `scripts/test_autonomous_campaigns_e2e.php`.

### `feat/whatsapp-cloud-api-csw-engine` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.398 - v4.9.401) — WhatsApp Cloud API Zero-Cost CSW Queue, Inbound Bot Engine, Rate Limiting & Executive PDF Report Delivery:
  - Implemented 24-Hour Customer Service Window (CSW) lifecycle management inside `includes/class-cora-whatsapp-gateway.php` utilizing Meta Graph API v22.0.
  - Zero-Cost Smart Queueing: When outside the 24-hour window, outgoing notifications are automatically queued instead of triggering paid utility templates, preserving $0.00 billed Meta tier charges.
  - Inbound Bot Engine: Interactive webhook router listening for incoming user messages (`[1] Leads`, `[2] Financials`, `[3] Staff Attendance`, `[4] Daily Briefing`, `[5] Gateway Health`, `[Menu]`) automatically refreshing the 24-hour CSW session window and immediately flushing pending queued updates.
  - Automated 3/hr Rolling Rate Limiter (`can_send_automated_notification`, `record_automated_dispatch`) preventing alert fatigue.
  - Smart Lead Batching: Groups high-velocity inbound form leads into scheduled digests.
  - 1-Click Cryptographic Tokenized Executive PDF Report Delivery (`cora_get_24h_report_url`) delivering secure financial and operational daily reports via WhatsApp document messages without raw long links.
  - Official Registered Business Phone Number (`+91 70153 17965`) synchronization, WhatsApp Business Profile DP verification, and deep-link activation URLs.
  - Automated test suite `scripts/test_whatsapp_csw_automation.php` covering profile verification, CSW window calculations, outbound session messages, zero-cost queueing, inbound bot commands, and cron maintenance.
- **Main Touchpoint**: `includes/class-cora-whatsapp-gateway.php`, `cora-workspace.php`, `scripts/test_whatsapp_csw_automation.php`.

### `feat/super-admin-whatsapp-telemetry` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.397) — Super Admin Integrations & WhatsApp Telemetry Hub:
  - Built dedicated `#tab-super-integrations` within `views/view-super-admin.php` offering complete governance over Meta Cloud API connectivity.
  - Real-Time 24-Hour CSW Window Monitor: Live visual countdown timers showing time remaining per active workspace recipient with color-coded status badges.
  - Zero Billed Charges Verification: Real-time cost meter tracking $0.00 Meta free tier service conversations and alert counts.
  - Verified Meta Graph API connectivity indicators (WABA ID `1735159218190730`, Phone Number ID `613919938474268`, Verified Profile badge, and High Quality Green tier rating).
  - Manual Simulation Test Suite: Integrated controls for manual test push triggers, 24h session renewal simulations, sync with Meta Graph API (`syncMetaGraphApi`), and delivery webhook status logs.
- **Main Touchpoint**: `views/view-super-admin.php`, `cora-workspace.php`.

### `feat/workspace-content-rest-api-mcp` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.391) — Workspace Content REST API & Canonical 9-Tool MCP Suite:
  - Engineered standalone REST controller `includes/class-cora-workspace-content-rest-controller.php` mounted under `/wp-json/cora/v1/content` with strict tenant isolation (`cora_assert_agency_context`).
  - Full CRUD & Batch Lifecycle: Endpoints for listing, single article inspection with block metadata, creation, updates, trashing, single publish/unpublish, and batch status transitions (`/content/batch`).
  - 11-Point Algorithmic SEO & GEO Audit Engine (`/content/{id}/seo-audit`): Automated evaluation checking title length, meta description, heading structure (H1/H2/H3), keyword density, GEO semantic entities, answer-first paragraphs, image alt text, reading ease, schema tags, URL slug structure, and link architecture.
  - Strategic Opportunity Engine (`/content/opportunities` and `/content/opportunities/seed`): Discovers and seeds high-impact topic clusters and keywords for organic growth.
  - Canonical 9-Tool MCP Suite in `includes/mcp/class-cora-mcp-tool-registry.php`: `cora_list_articles`, `cora_get_article`, `cora_create_article`, `cora_update_article`, `cora_delete_article`, `cora_publish_article`, `cora_unpublish_article`, `cora_bulk_update_articles`, `cora_audit_article_seo` (plus opportunity discovery and outline generator).
  - Dual Authentication Support: Seamless authorization via Workspace API Keys (`cora_ws_live_...`) or WordPress session nonces.
  - Comprehensive end-to-end test runner `scripts/test_content_suite_e2e.php` validating API key auth, REST CRUD, 11-point audit, and MCP execution.
- **Main Touchpoint**: `includes/class-cora-workspace-content-rest-controller.php`, `includes/mcp/class-cora-mcp-tool-registry.php`, `scripts/test_content_suite_e2e.php`.

### `feat/workspace-developer-gateway-drawer` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.391) — Workspace Developer API & MCP Gateway Docked Drawer:
  - Implemented docked drawer panel in `includes/partials/workspace-developer-gateway-drawer.php` accessible across workspace views via global header trigger (`#cora-open-dev-gateway-btn`).
  - Strict Rule 15 SOP Compliance: Universal bottom-slide motion (`translateY(100%)` to `translateY(0)`), docked directly beneath global header (`top: 52px; height: calc(100vh - 52px)`), responsive available space sizing, zero backdrop / zero dimming overlay, and Escape key dismissal.
  - 5-Tab Level 01 Segmented Pill Control:
    1. *Workspace Keys*: Multi-scope key generation (`content:read`, `content:write`, `crm:read`, `crm:write`, `media:read`, `workspace:read`) with one-time copyable secret disclosure.
    2. *Connect AI Agent*: 1-click configuration snippets for Claude Desktop (`claude_desktop_config.json`) and Cursor IDE (`cursor-mcp.json`).
    3. *Universal Tools*: Interactive registry matrix detailing all available MCP tools, parameter schemas, and scope requirements.
    4. *REST Endpoints*: Interactive OpenAPI / REST route reference with curl and fetch command generators.
    5. *Live Sandbox*: In-browser execution runner for issuing test requests and inspecting JSON-RPC responses.
  - Dedicated maximize/restore toggle button (`#cora-ws-dev-maximize-btn`) expanding panel to full viewport for deep developer workflows.
- **Main Touchpoint**: `includes/partials/workspace-developer-gateway-drawer.php`, `includes/workspace-header.php`, `cora-workspace.php`.
### `feat/google-business-profile-autopilot` (Merged to Main)
- **Status**: 🟢 Complete & Active (v4.9.388 - v4.9.389) — Google Business Profile & Google Maps AI Autopilot:
  - Overhauled `views/view-google-profile.php` from waitlist placeholder into production multi-state hub (State A: Unconnected Hero with OAuth & Places text search, State B: Multi-Branch Location Picker, State C: Connected Hub with 4 live KPI metrics).
  - Built 5-Tab Segmented Control architecture:
    1. *Reviews & AI Autopilot*: Filter pills (`All`, `Needs Reply`, `Replied`, `5 Stars`, `Critical`), tone selector (`warm`, `professional`, `concise`, `apology`), AI review response generation, and 1-click posting to Google Maps.
    2. *Maps Post Publisher*: Announcement composer with topic types (`STANDARD`, `OFFER`, `EVENT`), CTA selector, media photos, and post stream with instant delete.
    3. *Business Info & Operating Hours*: Business details plus Monday-Sunday open/close time pickers with "Closed" toggles.
    4. *Local SEO & Map-Pack Ranks*: Keyword ranking table, #1 in 3-Pack badges, 94% visibility score, and local optimization checklist.
    5. *Multi-Branch Locations*: Location switcher between Cyber City Hub and South Delhi branch.
  - Added right-sliding API Credentials Drawer (`#cora-gbp-keys-panel`) with masked OAuth Client ID/Secret, Google Maps API key, copyable OAuth redirect URI, and Escape-key dismissal.
  - Injected proactive monochromatic advisory card (`#cora-gbp-quota-advisory`) upon detecting Google Cloud 0-QPM default API quota restrictions, with a 1-click sandbox simulation fallback (`window.coraGbpAuthorizeDemoAccount('studio.director@elitestudio.local')`) enabling complete end-to-end testing without blocking agency evaluation (v4.9.389).
  - Comprehensive automated E2E Playwright test suite `tests/e2e/test-gbp-management.spec.ts` (4/4 tests passing: TC-GBP-01, TC-GBP-02, TC-GBP-03, TC-GBP-04).
- **Main Touchpoint**: `views/view-google-profile.php`, `cora-workspace.php`, `assets/js/admin-script.js`, `tests/e2e/test-gbp-management.spec.ts`.

### `feat/plugins-and-skills-engine` (Merged to Main)
- **Status**: 🟢 Complete & Merged (v4.9.387) — Dynamic Plugins, Modules & AI Skills Extensibility Runtime & Open API:
  - Engineered standalone runtime engine `class-cora-plugins-skills-engine.php` with 4 isolated tenant-scoped tables: `wp_cora_workspace_plugins`, `wp_cora_workspace_skills`, `wp_cora_workspace_api_keys`, `wp_cora_workspace_plugin_logs`.
  - Re-architected Feature Hub (`views/view-feature-hub.php`) into 3 segmented control tabs: App Modules (built-in toggles), Custom Plugins (ZIP/manifest installer, AI code generator), AI Skills (custom system prompts, parameters, execution testing).
  - Built unified Open API & MCP Gateway right-sliding drawer with API key generation, permissions scopes, curl examples, and live JSON-RPC playground.
  - Dynamically registered active skills and plugins as callable tools in Cora Universal Remote MCP Server via filter `cora_mcp_tools` and namespace resolution `cora_skill_*` / `cora_plugin_*`.
  - Added comprehensive verification suite `scripts/verify_plugins_skills_architecture.php` (14/14 tests passing).
- **Main Touchpoint**: `includes/class-cora-plugins-skills-engine.php`, `views/view-feature-hub.php`, `includes/mcp/class-cora-mcp-tool-registry.php`, `cora-workspace.php`.

### `feat/client-onboarding-and-calendar` (Merged to Main)
- **Status**: 🟢 Complete & Merged (v4.9.386 - v4.9.387) — Enterprise Client Onboarding Studio & Interactive Calendar:
  - Built 5-step Client Onboarding Studio drawer in `views/view-clients.php` (`1. Company Details` -> `2. Primary Contact` -> `3. Billing & Currency` -> `4. Services & Scope` -> `5. Portal & Invite`) with multi-currency (`INR`, `USD`, `EUR`, `GBP`, `AED`, `SGD`), tax regime selection, and custom service catalog tags.
  - Implemented automatic WordPress user provisioning with dedicated `cora_client` role and portal token generation upon client creation.
  - Enforced strict role isolation in `cora-workspace.php`: users with role `cora_client` are completely blocked from admin routes (`/workspace`, `/workspace/*`) and automatically redirected to their white-labeled client portal (`/{workspace}/client-portal?token=...`).
  - Standardized interactive scheduling calendar in `views/view-calendar.php` with Month, Week, and Day views, full appointment booking workflows, Google Calendar two-way sync, and standardized Google Meet URL formatting (`https://meet.google.com/xxx-yyyy-zzz`).
  - Added verification script `scripts/verify_client_onboarding_features.php` verifying role capabilities, portal redirection, multi-currency storage, and calendar sync.
- **Main Touchpoint**: `views/view-clients.php`, `views/view-calendar.php`, `public-client-portal.php`, `cora-workspace.php`.

### `feat/collapsible-navigation-and-mobile-touch` (Merged to Main)
- **Status**: 🟢 Complete & Merged (v4.9.385 - v4.9.386) — Collapsible Sidebar Groups & Mobile Sticky Sub-tabs:
  - Upgraded sidebar navigation in `admin-dashboard.php` with accordion collapsible section headers (`Core Workspace`, `Apps & Modules`, `Growth & Public Sites`, `System & Governance`).
  - Added persistent group toggle state via `localStorage` with fallback open state and search auto-expansion on input.
  - Fixed mobile sticky sub-tab gaps: anchored sub-tabs toolbar flush against top mobile header (`top: 48px` / `52px`) with zero layout jump and smooth horizontal scrolling.
  - Expanded search trigger touch target on mobile to minimum 28px touch area to prevent mis-clicks.
- **Main Touchpoint**: `admin-dashboard.php`, `includes/workspace-header.php`, `assets/css/admin-style.css`, `assets/js/admin-script.js`.

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
  - **SEC-012**: Scoped custom role capabilities preventing tenant admin roles from gaining global site-admin or administrative privileges.
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
| **v4.9.419**<br>`CORA-1268` | Oct 2026 | **Canvas Performance & Telemetry Command Center & Business Email Suite v2**: Self-hosted cookieless website telemetry engine (`Cora_Performance_Telemetry`) with `<1KB` beacon (`wp_cora_web_analytics`), 6 Decision KPIs matrix, dynamic directive banner, 4 interactive sub-views, and GSC striking-distance keywords; and Business Email Engine v2 (`Cora_Email_Campaigns_Engine`) supporting 1–20 inboxes (Starter, Growth, Pro, Enterprise), automated round-robin sender rotation, 14-day warmup ramp, and 8 sub-navigation tabs in `view-emails.php`. |
| **v4.9.418**<br>`CORA-1267` | Oct 2026 | **Real User Monitoring (RUM) Core Web Vitals Beacon & Closed-Loop Attribution**: `<1KB` passive beacon (`render_frontend_tracker_beacon`) tracking 75th percentile LCP, INP, CLS; closed-loop conversion tracking linking CRM leads (`wp_cora_leads`) and forms (`wp_cora_form_submissions`) to acquisition channels; and GSC striking-distance keyword extractor (positions 4–15). |
| **v4.9.417**<br>`CORA-1266` | Oct 2026 | **ChatGPT Action Discovery & 30-Action OpenAPI Ceiling Enforcement**: Curated 28-tool OpenAPI schema (`?limit=28`) strictly enforcing OpenAI's 30-action ceiling to eliminate Custom GPT schema import failures, while exposing full 246 tools unconstrained via `?all=1` and direct MCP; schemas sanitized with zero emojis and non-empty property objects. |
| **v4.9.416**<br>`CORA-1265` | Oct 2026 | **Affiliate & Referral Partner Hub Redesign (HeroUI & ShadCN Anatomy)**: Complete redesign of `views/view-affiliate-referrals.php` adopting HeroUI & ShadCN component architecture (Cards, Elevated Segmented Tabs with micro-counters, Progress milestones, URL Snippet with copy feedback, Squircle Avatar, Chips, Interactive Deal Multiplier Slider, and 5 dedicated sub-views). |
| **v4.9.415**<br>`CORA-1264` | Oct 2026 | **Canvas Global Pages, HTML 3D Deployments & Next.js Static Builds**: Dynamic route resolution in `cora-workspace.php` detecting HTML Canvas pages (`_cora_page_engine = 'html_canvas'`) vs Next.js static builds (`cora_render_nextjs_static_build`) vs live dev frames; 1-click URL Website Migrator Drawer & Elementor importer; and SPA error shield. |
| **v4.9.414**<br>`CORA-1263` | Oct 2026 | **Forms Sticky Header & Universal All Entries Drawer**: Sticky header with integrated search and quick actions; All Entries slide drawer with high-density table, form filter dropdown, export options, and submission details modal. |
| **v4.9.413**<br>`CORA-1262` | Oct 2026 | **AI Copilot Slash Menu & Right-Panel Copilot Drawer**: Workspace-wide AI Copilot with `/` slash menu trigger, contextual workspace awareness, and right-panel copilot drawer. |
| **v4.9.412**<br>`CORA-1261` | Oct 2026 | **CLA / ChatGPT Autonomous Workspace Operator & Dynamic Template Engine**: RFC 8414 Dynamic Userinfo Handshake (`/oauth/userinfo` & `/wp-json/cora-oauth/v1/userinfo`) eliminating ChatGPT `"not_installed"` handshake stalls; dynamic plugin manifest (`ai-plugin.json`) supporting both `'cla'` and `'cora'` client paths; expanded 246 tool catalog aliases (canonical, `cla_*`, `cora_*`); 9 universal operator handlers; 12/12 automated E2E test suite in `scripts/test_cla_chatgpt_mcp_e2e.php`; Dynamic Multi-Channel Template Engine (`Cora_Template_Engine` across 5 archetypes: article, page, policy, product, collection); Canvas Visual Workflow Builder & MCP Node Canvas (`views/view-canvas.php`); and Content Approval Drawer with media selection bridge. |
| **v4.9.411**<br>`CORA-1260` | Oct 2026 | **Canvas Visual Workflow Builder & Node Canvas Execution Engine**: Interactive visual automation workflow canvas (`views/view-canvas.php`) with drag-and-drop MCP automation triggers, active state node wiring, and real-time execution feedback. |
| **v4.9.410**<br>`CORA-1259` | Oct 2026 | **Media Asset Selector Modal & Content Approval Drawer Integration**: Slide-out Content Approval Drawer linking media uploads directly to dynamic template rendering slots with live thumbnail previews and MIME validation. |
| **v4.9.409**<br>`CORA-1258` | Oct 2026 | **Dynamic Multi-Channel Template Engine Schema Auto-Provisioning**: Self-healing database table `wp_cora_canvas_templates` decoupling content entities from theme skins with zero content loss on theme migration. |
| **v4.9.408**<br>`CORA-1257` | Oct 2026 | **Extended MCP Tool Aliases Architecture**: Tri-alias naming topology mapping canonical tool names to `cla_*` and `cora_*` namespaces, expanding operator tool inventory to 246 distinct endpoints. |
| **v4.9.407**<br>`CORA-1256` | Oct 2026 | **Top 3 Market Leader Connectors, Minimal High-Trust OAuth Consent & Telemetry Handshakes**: High-visibility connection matrix in `views/view-canvas.php` for ChatGPT, Claude, and Gemini; clean minimal OAuth consent screen displaying tenant workspace scopes; and live connection health monitoring with latency diagnostics. |
| **v4.9.406**<br>`CORA-1255` | Oct 2026 | **1-Click Seamless Gemini MCP Connector & Autonomous Content Campaigns Engine**: 1-click `client_credentials` grant with auto-provisioned client ID/secret and copyable JSON configuration; Autonomous Content Campaigns Engine (`includes/class-cora-autonomous-campaigns.php`) with automated topic clustering, cadence scheduling (daily, 3x/week, weekly, bi-weekly), and 11-point SEO (min 80) & GEO (min 85) quality score gating. |
| **v4.9.405**<br>`CORA-1254` | Oct 2026 | **Universal Official Vector AI Brand Logos Engine**: Centralized SVG brand helper `cora_get_ai_platform_official_logo()` in `includes/workspace-header.php` standardizing authentic official vector logos for OpenAI/ChatGPT (#10A37F), Claude (#D97757), Gemini (linear gradient #4E82EE to #9B72CF), Perplexity (#1FB8CD), and YouTube (#FF0000) globally across Frontend and Workspace. |
| **v4.9.404**<br>`CORA-1253` | Oct 2026 | **Autonomous Campaign Background Scheduler & Cron Handler**: Background automated drafting execution loop evaluating campaign cadences, triggering AI article creation, and scheduling publication. |
| **v4.9.403**<br>`CORA-1252` | Oct 2026 | **Topic Keyword Research Auto-Clustering**: High-intent keyword opportunity matching and intent categorization for automated blog campaign generation. |
| **v4.9.402**<br>`CORA-1251` | Oct 2026 | **Dynamic Userinfo Handshake & Bearer Scope Validation**: Hardened Bearer token authorization validator and dynamic userinfo payload resolution supporting external autonomous LLM agents. |
| **v4.9.401**<br>`CORA-1250` | Oct 2026 | **WhatsApp Rate Limiting, Lead Batching & Tokenized Executive PDF Report**: Enforced 3/hr rolling rate limit per recipient (`can_send_automated_notification`), smart lead batching for inbound form submissions, elimination of raw links, and 1-click cryptographic tokenized 24h Executive PDF reports via WhatsApp document delivery (`class-cora-whatsapp-gateway.php`, `cora_get_24h_report_url`). |
| **v4.9.400**<br>`CORA-1249` | Oct 2026 | **Interactive WhatsApp Quick-Reply & Media Cards**: Added structured quick-reply buttons and image card helpers for rich inbound bot interactions. |
| **v4.9.399**<br>`CORA-1248` | Oct 2026 | **WhatsApp Zero-Cost CSW Queue & Inbound Bot Engine**: Meta Graph API v22.0 integration, zero-cost 24h Customer Service Window queueing, inbound bot menu router (`[1]` to `[5]`), official DP synchronization, and automated session renewal. |
| **v4.9.397**<br>`CORA-1246` | Oct 2026 | **Super Admin Integrations & WhatsApp Telemetry Hub**: Dedicated Super Admin subtab (`#tab-super-integrations`) featuring real-time 24h window countdown timers, $0.00 Meta billed charges monitor, WABA connection health, and manual simulation test suite. |
| **v4.9.395**<br>`CORA-1244` | Oct 2026 | **WhatsApp User Mobile Routing & CSW Window Checks**: Strict routing of notifications to recipient profile mobile number and pre-send 24h session window assertions. |
| **v4.9.393**<br>`CORA-1243` | Oct 2026 | **WhatsApp Official Business Number Synchronization**: Synchronized registered business line (`+91 70153 17965`) and updated deep-link activation routes. |
| **v4.9.391**<br>`CORA-1242` | Oct 2026 | **Workspace Content REST API & Developer Gateway Docked Drawer**: Complete REST controller `/wp-json/cora/v1/content`, 11-point algorithmic SEO/GEO audit engine, canonical 9-tool MCP suite, and Rule 15 SOP compliant docked Developer API drawer with API key management and AI agent connectors. |
| **v4.9.389**<br>`CORA-1241` | Oct 2026 | **Google Business Profile Quota Error Advisory & Sandbox Fallback**: Enhanced `coraGbpLoadAccounts` in `admin-script.js` to proactively detect Google's 0-QPM default quota limit (HTTP 429 `RESOURCE_EXHAUSTED` for `mybusinessaccountmanagement.googleapis.com`), rendering a high-clarity monochromatic callout card with Google access form link and 1-click "Test in Sandbox Mode Now" fallback. |
| **v4.9.388**<br>`CORA-1240` | Oct 2026 | **Google Business Profile & Google Maps AI Autopilot**: Complete Google Business Profile integration (`/workspace/gbp`, `views/view-google-profile.php`) featuring 3 reactive states (OAuth connect hero, multi-location selector, connected management hub), Places API search, tone-driven AI review reply copilot (warm, professional, concise, apology), Maps announcement & update post publisher, 7-day business operating hours manager, Local SEO 3-Pack rank inspector, and right-sliding credentials drawer sheet. |
| **v4.9.387**<br>`CORA-1239` | Oct 2026 | **Dynamic Plugins & AI Skills Runtime Engine**: Self-contained extensibility framework (`includes/class-cora-plugins-skills-engine.php`) with isolated schema tables, dynamic MCP 2.0 tool registry, REST gateway (`/wp-json/cora-plugins/v1/`), 3-tab segmented Feature Hub view (`views/view-feature-hub.php`), and enterprise API key generation. |
| **v4.9.387**<br>`CORA-1238` | Oct 2026 | **Enterprise Client Onboarding Suite & Strict Portal Role Isolation**: 5-step guided onboarding studio (`views/view-clients.php`), multi-currency rate support, custom service catalog, automatic `cora_client` user provisioning, and absolute admin dashboard route shielding for portal clients (`cora-workspace.php`). |
| **v4.9.387**<br>`CORA-1237` | Oct 2026 | **Interactive Workspace Scheduling Calendar & Google Meet Integration**: Full month/week/day scheduling engine (`views/view-calendar.php`), two-way Google Calendar synchronization, standard 3-4-3 Google Meet meeting URL generation (`meet.google.com/xxx-yyyy-zzz`), and real-time meeting status badges. |
| **v4.9.386**<br>`CORA-1236B` | Oct 2026 | **Collapsible Sidebar Navigation Groups & Memory State**: Accordion sidebar navigation groups in `admin-dashboard.php` with `localStorage` state persistence, animated chevron rotation, active-route auto-expansion, and global search auto-unfurling. |
| **v4.9.385**<br>`CORA-1236A` | Oct 2026 | **Mobile Sticky Sub-Tabs, Zero-Gap Anchoring & 28px Search Target**: Zero-gap sticky sub-tab anchoring on mobile screens, touch-friendly 28px expandable search trigger, and layout stabilization across all viewport breakpoints. |
| **v4.9.384**<br>`CORA-1236` | Oct 2026 | **Global Sticky Workspace Header & Mobile Expandable Sub-Tabs Search**: Enforced global sticky header pinning (`position: sticky; top: 0; z-index: 100020;`) across all desktop and mobile viewports; resolved sub-tabs search horizontal overflow on mobile screens (< 640px) via 1-tap expandable search trigger (`#cora-proj-search-trigger-btn`) expanding full-width input (`#cora-proj-search-wrapper.mobile-expanded`) with Esc / close button dismiss; and synchronized version manifest to 4.9.384. |
| **v4.9.383**<br>`CORA-1235` | Oct 2026 | **Sub-Tabs Extra Actions Vertical Alignment & Level 01 Segmented Switcher**: Standardized `tabs_extra_html` vertical alignment in `includes/workspace-header.php` (`h-full flex items-center gap-1.5 sm:gap-2`); implemented Level 01 Segmented Control container for Table vs Grid view mode toggle with active button styling (`bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs`). |
| **v4.9.382**<br>`CORA-1234` | Oct 2026 | **Shopify-Inspired Canvas Maximize / Restore Toggle (⤢ Fullscreen Mode)**: Added dedicated maximize button (`#cora-proj-maximize-btn`) in docked drawer header toggling `.maximized` state, expanding drawer across full viewport (`left: 0; width: 100vw; z-index: 100060; border-left: none`) for zero-distraction case study authoring with instant sidebar synchronization on `cora:sidebar-toggle`. |
| **v4.9.382**<br>`CORA-1233` | Oct 2026 | **Shopify-Inspired Main-Area Docked View Panel Architecture & Responsive Bounds Sync**: Implemented SOP Rule 15 docked view panel in `views/view-projects.php` sliding up smoothly from bottom (`translateY(100%)` to `translateY(0)`), docking directly beneath top header (`top: 52px; height: calc(100vh - 52px)`), dynamically sizing to available main content canvas based on sidebar state (`MutationObserver` & `resize`), zero overlay / zero backdrop, scoped main scroll lock (`overflow-hidden` on `main.cora-main`), and Escape key dismissal. |
| **v4.9.381**<br>`CORA-1227` | Oct 2026 | **Agency Projects & Portfolio Case Studies Module**: Complete multi-tenant workspace project management (`wp_cora_projects`, `/workspace/projects`) and public Next.js portfolio (`/projects`, `/portfolio`), 6-tab studio editor, 4-Act narrative architecture (Summary, Challenge, Solution, Deliverables), verified testimonials, big numerical KPI cards in JetBrains Mono, and connected JSON-LD schemas. |
| **v4.9.381**<br>`CORA-1228` | Oct 2026 | **Public Micro-Tools Growth Engine & Canonical Tool Registry**: Physical audit, reconciliation, and verification of 63 search-optimized client-side micro-tools across 11 categories (`cora-frontend/app/tools/*`, `lib/tools-canonical-registry.ts`) with zero-PII privacy architecture and contextual conversion hooks into Cora workspaces. |
| **v4.9.381**<br>`CORA-1229` | Oct 2026 | **Canvas Themes GitHub CI/CD Full-Lifecycle PR & Rollback Engine**: Full Git version control docked at extreme right edge of Canvas sub-navigation (`includes/class-cora-github-integration.php`), supporting Personal PAT vs Organisation modes, branch management, automated PR creation with visual diffs, 1-click merging, and 1-click snapshot rollback (`cora_github_revert_commit`). |
| **v4.9.381**<br>`CORA-1230` | Oct 2026 | **Forms to CRM Lead Pipeline Automation & All-Submissions REST Endpoint**: Seamless inbound bridge auto-converting public form submissions into `wp_cora_leads` CRM records with client detail extraction, deal sizing, top-floating monochromatic toasts, and unified aggregate REST submissions API (`/wp-json/cora/v1/forms/submissions`). |
| **v4.9.381**<br>`CORA-1231` | Oct 2026 | **Editorial Knowledge Engine & High-Authority Article Library**: Structured long-form editorial library (`/articles`, `lib/articles-data.ts`) with schema-validated JSON-LD Knowledge Graphs, semantic entity linking, answer-first summaries, and citation anchors optimized for Generative Engine Optimization (GEO). |
| **v4.9.381**<br>`CORA-1232` | Oct 2026 | **Content Suite & Email Infrastructure Hardening**: Hardened Content AI Suite and transactional email routing with tenant context isolation, Hostinger SMTP delivery verification, and multi-tenant telemetry stability. |
| **v4.9.380** | Oct 2026 | Canonical Industry Directory Overhaul & Slug Migration: Migrated primary industry hub from `/use-cases` to canonical `/industries` with full 301 redirect parity in `.htaccess` and Next.js, refined desktop Industries Mega-Menu in `Navbar.tsx` matching exact production mockup with dedicated icon tiles and colored pill indicators, comprehensive plain-English active-voice copy across all 10+ industry verticals (`cora-frontend/lib/industry-data.ts`), and dynamic industry comparison matrix (`IndustryComparisonTable.tsx`) |
| **v4.9.379** | Oct 2026 | Autonomous AI Workforce & 4-Row Infinite Skills Marquee: Dedicated `/ai-agent` landing canvas showcasing Cora's multi-agent operating system, high-resolution neural portrait cutout asset (`cora_hero_agent_cutout.png`) seamlessly blended into canvas container, 4-row infinite skills marquee (`InfiniteSkillsSection.tsx`) displaying verified capabilities across operations, sales, finance, and creative delivery, and automated asset bundling with `/api/health` responsiveness check |
| **v4.9.378** | Oct 2026 | Features Catalog & Lenis Smooth-Scroll Boundary Isolation: Overhauled `/features` capability catalog, compact single-line sidebar navigation (`FeaturesSidebar.tsx`) with segmented status filters (*All*, *Released*, *Beta*, *Upcoming*), Lenis scroll isolation via `data-lenis-prevent` and explicit viewport height boundaries eliminating wheel-scroll trapping, search icon overlap fix, and active-voice nontechnical benefits across 40+ visual capability cards with a unified "Get Started for Free" CTA |
| **v4.9.377** | Oct 2026 | Enterprise Brand Asset Registry: Modernized `/brand` center with latest agency positioning, 1-click turnkey SVG vector download kit (`BrandLogosSection.tsx`) for Primary Wordmark, Inverted Wordmark, and Symbol Mark, complete design tokens reference (neutral color scale, typography hierarchy, co-branding standards, logo clearspace/misuse rules), and authentic production UI captures adhering strictly to Rule 13 zero-outline styling |
| **v4.9.376** | Oct 2026 | Agency Story Canvas & Connected Chronological Journey Ribbon: Redesigned `/about` page with full-width narrative layout, connected chronological timeline ribbon (`ScrollDrivenTimeline.tsx`) with seamless rail segments eliminating floating tick gaps, responsive mobile spine layout on 375px+ screens, Core Values bento cards, and dynamic operational week calendar |
| **v4.9.375** | Oct 2026 | Contact Form Validation & Documentation Mobile Optimization: Robust multi-field client/server validation suite on `/contact` (Full Name, Work Email, Phone with dialing codes, Company/Studio, Topic dropdown) with monochromatic alerts and ARIA accessibility, streamlined docs sidebar navigation and badge wrapping in `DocsClient.tsx`, mobile floating island clearance (`pb-28`), and React Hook order violation resolution in `Navbar.tsx` |
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

*Cora Platform Release Manifest v4.9.419 — Architecture & Engineering Team.*
