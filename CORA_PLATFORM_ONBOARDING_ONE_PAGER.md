# Cora Platform — Comprehensive Platform & Onboarding Strategic Brief
> **Target Audience for this Document**: AI Product & Growth Strategist Agent  
> **Objective**: Comprehensive platform architectural overview, feature breakdown, and onboarding evaluation brief to formulate optimal user onboarding, product activation, and launch strategies for Cora.  
> **Platform Version**: `v4.9.166` | **Date**: September 2026

---

## 1. Executive Summary & Value Proposition

**Cora** is an enterprise-grade, all-in-one multi-tenant SaaS workspace and agency operating system specifically engineered for Indian service-based businesses, retail distributors, creative agencies, and consulting firms, with deep vertical specializations in **Photography Studios**, **Real Estate Brokerages**, **Digital Marketing Agencies**, **Stationery Manufacturing & Van Route Distribution**, and **Professional Services & Consulting**.

### The Core Problem Cora Solves
Service agencies in fast-growing markets like India are fragmented across disconnected tools:
* WhatsApp / DMs for client inquiries
* Google Sheets / Excel for lead tracking and shoot/showing schedules
* Paper registers and scattered spreadsheets for employee rosters and commissions
* Canva / Photoshop / Google Drive for media proofing and delivery
* Tally / Manual Word templates for GST invoicing and agreements
* Notification spam and duplicate alerts cluttering owner inboxes
* Generic AI tools (ChatGPT) with zero context on agency operations or past clients

### The Cora Solution
Cora consolidates the entire agency lifecycle into a single, unified, white-labeled workspace:
1. **Lead & Inquiry Capture**: Drag-and-drop Kanban CRM pipeline with ultra-compact 3-level cards, 1-tap quick outreach footer (WhatsApp, Phone, Email), in-column search & sort, pastel column tints, unified multi-filter popovers, decision analytics, and AI call synthesizer.
2. **Client Management Suite & Resizable Task Manager**: 4-subtab client CRM hub (Overview, Active Deals, Client Tasks, Financials sync), Kanban pipeline, floating resizable Task Details Drawer with left arc, subtask checklist with live progress bar, past-time scheduling validation for today's date with auto-computed upcoming slots, and multi-industry dictionaries.
3. **Public White-Labeled Client Portal**: Passwordless, tokenized URL access (`public-client-portal.php`) adhering to Anthropic Claude aesthetic (`#FBFaf7`) for deliverables review, milestone tracking, and instant UPI/Card payments (WhatsApp, Google Pay, PhonePe, Paytm).
4. **Operations & Dispatch**: Visual monthly calendar, timeline crew assigner, equipment gear custody logs, and showings coordinator.
5. **Field Ops & Live Geolocation Tracking**: Real-time GPS tracking, stop/rest detection, average velocity math, route replay, and free HD multi-layer maps (Esri Satellite, Streets, OSM, CartoDB Dark).
6. **Multimodal Team Migration & Dynamic Role Governance**: Vision OCR roster ingestion parsing physical attendance sheets/PDFs, custom dynamic role creator, tenant-scoped permission matrix, desktop/mobile drag-and-drop tab customizer, and initials-based SVG avatars.
7. **Studio-Grade Media Hub**: Multi-ratio crop presets (`1:1`, `4:3`, `16:9`), SEO metadata tagging, and client delivery galleries.
8. **GST-Compliant Document Vault**: Legally binding E-Sign contracts, auto-calculated CGST/SGST/IGST invoicing, and SHA-256 audit registries.
9. **CORA AI Assistant & Dual-Mode Copilot**: Dual-mode (Chat & Live Duplex Voice) assistant with standardized CORA AI branding, modern sparkle SVG icon, `#cora-ai-history-drawer` chat history slide-in drawer, 1-click `+ New Chat` reset, generative action cards, 1-click blog draft generator, 1-2 line conciseness standard, and balanced brace action tag parsing (`cora_ai_extract_balanced_json`).
10. **Enterprise AI Safety Guardrails**: Built-in automated policy enforcement across 6 violation categories (`explosives_weapons`, `terrorism_violence`, `nudity_explicit`, `religious_defamation_conflict`, `self_harm`, `jailbreak_injection`), security audit logging in `wp_cora_security_incidents`, real-time automated dual escalation to Platform Super Admins AND Workspace Owners, and RBAC action execution capability checks.
11. **Content AI Suite & Bulk Operations Engine**: Notion-styled Content Library with multi-row checkboxes, floating docked bulk toolbar (`#content-bulk-actions-bar`), bulk stage progression/category/export/delete, fixed-width dropdown architecture, 3-column mobile Opportunities grid, flush sticky sub-tabs, and Quill WYSIWYG editor.
12. **Forms & Reviews 2.0**: 26 hardened intake widgets, ~36px sleek sticky sub-navigation tabs matching Content Suite 1:1, flush mobile viewports (`px-0`), live interactive modal form preview, AI Conversion Doctor with funnel drop-off analytics, and automated campaign lead intake engine.
13. **Dual-Engine Canvas Builder & Migrator**: White-labeled Elementor + In-browser Visual HTML Editor + 1-Click Universal Website Migrator scraping external sites into editable draft themes.
14. **Affiliate & Referral Ecosystem**: Dual-reward growth engine (+100 AI credits on free signup, 40% recurring commission on paid plans), 3-step partner enrollment screener, 6-plan pricing & commission matrix, and official vector share marks.
15. **Stationery Manufacturing & Van Sales Dual Engine**: Central plant command center, 3-step SKU studio drawer, margin telemetry cards, van dispatch with city chips, branded Hostinger emails with 1x1 tracking pixel, and 100% full-width driver POS terminal with total chrome stripping.
16. **Single 24-Hour Executive PDF Reporting & Anti-Spam Policy**: Strips email dispatch from ephemeral micro-events (routing 100% to in-app bell & PWA push) and delivers a single, consolidated 24-Hour Executive PDF Report strictly once per 24 hours per owner.
17. **Desktop Centered KPI Analytics Container & Guaranteed 4 Scorecards**: Centered dashboard analytics container constrained to max 60% viewport width with a guaranteed 4-metric scorecard layout (2x2 mobile grid, 1x4 desktop row) and dynamic user name resolution.
18. **App Modules (Feature Hub 24-Module Directory)**: 24 Core Foundation and Domain modules across 5 categories with compact single-column horizontal mobile cards, Rule 13 zero-outline tonal surface styling, unified search/status filters, reactive toggles, and route decoupling.
19. **God-Level Super Admin Console**: 11-tab administrative control center with real-time MRR telemetry, global AI token pool, tenant capability matrix, security incident logs, and emergency controls.

---

## 2. Platform Architecture & Technology Stack

```
+---------------------------------------------------------------------------------------+
|                                    CORA FRONTEND & PWA                                |
|  • Pure Light Mode (Shopify/Notion Zinc Palette)  • Custom Monochromatic Toast Engine |
|  • Adaptive Mobile Floating Island Nav            • Zero Mobile Side Drawers (Sheets) |
|  • Responsive Mobile PWA (Dynamic Icon Sync, 0ms Tap Latency, Offline Cache Fallback) |
|  • Docked Top-Right PWA Update Pill               • Seamless Mobile Warm Cream Base   |
|  • Security URL Masking (/assets/ & /core/)       • Clean Semantic RESTful URLs       |
|  • Global Scroll Bottom Spacer (pb-28 md:pb-12)   • Interactive Leaflet Map Engine    |
+-------------------------------------------+-------------------------------------------+
                                            |
                                  REST / AJAX / WebSockets
                                            |
+-------------------------------------------v-------------------------------------------+
|                                   CORA WORKSPACE CORE                                 |
|  • Modular PHP 8.2 Views (50+ Modular Subviews)   • Custom Routing & Multi-Tenant RBAC|
|  • High-Speed Micro-Cache Memory Layer            • Locked Down WordPress Backend     |
|  • Strict Tenant Isolation (`agency_id` = %d)     • Standardized Header Action Bar    |
|  • Super Admin Menu & Analytics Isolation         • Default Hostinger SMTP Relay      |
|  • Universal HTML Multi-Page Website Migrator     • Strict Single Workspace Owner     |
|  • Dedicated CRM Sidebar Group (Leads, Cal, Fin)  • Client Tasks & Portal Bridges     |
|  • Affiliate & Referral Attribution Engine        • App Modules 14-Foundation Matrix  |
+-------------------------------------------+-------------------------------------------+
         |                                  |                                  |
         v                                  v                                  v
+------------------+              +-------------------+              +------------------+
|  AI COPILOT CORE |              |  BUSINESS ENGINES |              |  DATABASE & OPS  |
| • Continuous Voice|              | • GST Tax Engine  |              | • Custom MySQL   |
| • 1-Click New Chat|              | • E-Sign Registry |              |   Isolation      |
| • Dual-Mode Panel |              | • Client CRM Hub  |              | • Static Micro-  |
| • OCR Team Parser |              | • Forms 2.0 Doctor|              |   Cache Layer    |
| • Bidirectional  |              | • Affiliate Engine|              | • Security Symlink|
|   Continuous RAG |              | • Field Ops GPS   |              | • PWA Sync & Pill|
| • AI Call Synth  |              | • Dynamic Roles   |              | • Client Tokens  |
+------------------+              +-------------------+              +------------------+
```

### Core Stack Details
* **Backend Engine**: WordPress 6.x headless/hybrid engine on PHP 8.2 with high-performance custom MySQL schema (`cora_agencies`, `cora_leads`, `cora_clients`, `cora_referral_links`, `cora_referrals`, `cora_affiliate_payouts`, `cora_bookings`, `cora_ledger`, `cora_documents`, `cora_canvas_themes`, `cora_canvas_pages`, `cora_inventory_products`, `cora_inventory_consignments`, `cora_notifications`, `cora_roles`).
* **Frontend Architecture**: Monochromatic Vanilla CSS / Tailwind (strictly light-mode Notion/Shopify aesthetic; 11-step neutral `zinc` ramp `#ffffff` to `#09090b`), ES6+ JavaScript, Leaflet.js mapping, Quill.js rich WYSIWYG, SVG vector iconography (`stroke-width: 1.8-2.2`).
* **PWA & Mobile Ergonomics**: Progressive Web App with VAPID ES256 Push notifications, dynamic version-stamped manifests, docked top-right update pill, bottom-up slide sheets (`translate-y-full` to `translate-y-0`), top-down floating banners, and 0ms touch response (`touch-action: manipulation;`).
* **AI Orchestration**: Multi-provider fallback router (Google Gemini 3.5 Flash / Pro Multimodal, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o) with continuous hands-free duplex voice discussion, 1-click conversation reset, tenant-scoped situational RAG, and MCP gateway.

---

## 3. Target User Personas & Industry Verticals

| Persona & Industry | Core Pain Points | "Aha!" Moment in Cora | Must-Have First Day Actions |
| :--- | :--- | :--- | :--- |
| **1. Photography Studio Owner** (`photography_studio`) | Double bookings, chasing client e-signatures, manual GST billing, crew allocation chaos, slow photo proofing | Generating a full GST invoice + shoot contract with e-sign link in under 60 seconds, sharing white-labeled client portal | • Select Photography Studio preset<br>• Add first shoot booking & assign crew<br>• Issue first e-sign contract<br>• Share client portal |
| **2. Real Estate Brokerage Team** (`real_estate`) | Untracked WhatsApp leads, missed showing visits, unorganized property listings, slow customer outreach, dispatch blindness | Tracking live field agents on the high-definition map while leads move through Kanban stages | • Select Real Estate Brokerage preset<br>• Import / create first buyer lead<br>• View live Field Ops map |
| **3. Digital Marketing Agency** (`marketing_agency`) | Client retainer tracking, campaign funnels, scattered client approvals, disparate landing pages | Ingesting an existing client site with 1-click Website Migrator and managing client tasks in the resizable floating drawer | • Select Marketing Agency preset<br>• Migrate website in Canvas<br>• Launch intake form with AI Conversion Doctor<br>• Create client task pipeline |
| **4. Stationery Manufacturer & Distributor** (`stationery_inventory` / `manufacturing`) | Blind van dispatches, paper challan spot sales, stock shrinkage/theft, unverified retail check-ins, delayed day-end cash recon | Dispatching a van with auto-suggested top 5 SKUs, tracking open status via 1x1 pixel, and running 1-tap mobile POS with 24h recon | • Select Stationery Manufacturing preset<br>• Add factory SKUs with live margin math<br>• Dispatch first van consignment<br>• Launch Field Van POS terminal |
| **5. Professional Services & Consulting Agency** (`professional_services`) | Fragmented client retainers, scope creep, untracked advisory hours, complex role-based access, scattered contracts | Structuring custom dynamic team roles with granular feature permissions and managing client retainers in the unified CRM group | • Select Professional Services preset<br>• Create dynamic team roles & permissions<br>• Set up client accounts & retainers<br>• Issue first advisory agreement |

---

## 4. Master Module Roster & Core Capabilities

```
+----------------------------------------------------------------------------------------------------+
|                                    21 CORE WORKSPACE MODULES                                       |
+----------------------+----------------------+----------------------+-------------------------------+
| 1. Dynamic AI Panel  | 2. Content AI Suite  | 3. Lead CRM Pipeline | 4. Dual Canvas & Migrator     |
| • Dual Chat & Voice  | • Myra AI Copilot    | • Ultra-Compact 3-Lvl| • Elementor White-Label       |
| • 1-Click New Chat   | • 7 Lifecycle Tabs   | • In-Column Sort/Find| • Visual HTML Lovable Engine  |
| • 3px Quota Bars     | • Quill WYSIWYG      | • 1-Tap Quick Outreach| • Universal Website Migrator  |
| • Generative Cards   | • GSC / IndexNow     | • Multi-Filter Popover| • AI Core Web Vitals Optimizer|
+----------------------+----------------------+----------------------+-------------------------------+
| 5. Team & Governance | 6. Forms 2.0 Engine  | 7. Document Vault    | 8. Studio Media Hub           |
| • Multimodal Vision  | • 26 Form Widgets    | • 5-Step Doc Wizard  | • 1:1, 4:3, 16:9 crop         |
| • OCR Roster Parser  | • AI Conversion Doc  | • GST Tax Engine     | • Synced breadcrumb header    |
| • Dynamic Role RBAC  | • WhatsApp/SMTP alert| • Legal E-Sign Audit | • Client proofing             |
| • Tab Customizer UI  | • Campaign Intake Map| • SAC code catalog   | • SEO Metadata tagger         |
+----------------------+----------------------+----------------------+-------------------------------+
| 9. App Modules Hub   | 10. Email & SMTP     | 11. Crew & Equipment | 12. Financial Intelligence    |
| • 14 Foundation Grid | • Hostinger Relay    | • Timeline crew grid | • Multi-Tenant Cash Ledger    |
| • Explicit Save Bar  | • Dynamic variables  | • Gear check-in/out  | • 30-Day Runway Forecast      |
| • Category Filter Bar| • HTML email builder | • Client task assign | • Deal Feasibility Simulator  |
+----------------------+----------------------+----------------------+-------------------------------+
| 13. Inventory Engine | 14. Field Van POS    | 15. Nav Customizer   | 16. Voice AI Duplex           |
| • Central Plant Hub  | • Stock on Wheels    | • 14 KPI Selector    | • Web Speech Stream           |
| • 3-Step SKU Studio  | • Spot Invoicing POS | • 16-Module Nav Slots| • Indian Dialect Accents      |
| • Consignment Van Hub| • Chrome Stripping   | • Search & Filtering | • Integrated Voice Settings   |
| • 24h Supply Recon   | • Safe Unit Rollback | • Title-Cased Badges | • Suppressed Keyboard         |
+----------------------+----------------------+----------------------+-------------------------------+
| 17. 24h Exec Reports | 18. Super Admin Base | 19. Public Docs/Tour | 20. Client Task Manager       |
| • Single Daily Report| • 11-Tab Super Admin | • 3-Column Notion doc| • 4-Subtab Client Hub         |
| • Printable PDF Doc  | • MRR/ARR Telemetry  | • AI Docs Playground | • Resizable Floating Drawer   |
| • Micro-Event In-App | • Token Allocation   | • Platform Tour      | • Subtask Checklist & Math    |
+----------------------+----------------------+----------------------+-------------------------------+
| 21. Affiliate & Referral Growth Engine                                                             |
| • Dual-Reward Engine (+100 AI credits on free signup, 40% recurring commission on paid plans)      |
| • 3-Step Partner Enrollment Screener Flow before unlocking personalized partner dashboard          |
| • Geolocation annual pricing matrix & official SVG vector share suite (WhatsApp, LinkedIn, X, QR)  |
+----------------------------------------------------------------------------------------------------+
```

---

## 5. Strategic Onboarding Recommendations

1. **Interactive Platform Tour Trigger**: Upon first workspace login, automatically offer or trigger `window.coraStartPlatformTour()` guiding the user across telemetry KPIs, AI Co-Founder panel, CRM pipeline, and Document Vault.
2. **Zero-Friction Industry Select**: On first launch (`/workspace/onboarding`), present clear visual cards for *Photography Studio*, *Real Estate Brokerage*, *Digital Marketing Agency*, *Stationery Manufacturing & Van Route Distribution*, and *Professional Services & Consulting*.
3. **Multimodal Team Quick-Import & Field Ops**: Allow owners to snap a picture of their existing team sheet to batch-invite their whole staff on Day 1, configure dynamic custom roles with granular feature gates, and immediately toggle Field Ops GPS tracking for field operations.
4. **Client Onboarding & White-Labeled Client Portal**: Onboard first clients via the Client Management Suite (`/workspace/clients`), assign deliverables in the Client Task Manager, and issue custom tokenized client portals (`public-client-portal.php`) for seamless proofing and UPI/Card payments.
5. **Partner & Affiliate Growth Launch**: Activate the Affiliate & Referral Engine (`/workspace/affiliates`) allowing agency owners and creators to earn recurring 40% revenue share and +100 AI credits per referral.
6. **Instant Van Consignment & Route Setup**: For manufacturing plants and distribution hubs, pre-load top starter SKUs (e.g. Spiral Notebooks, A4 Copier Reams) so drivers can execute their first test spot sale within 2 minutes.
7. **Universal Website Ingestion**: For marketing agencies and web studios, offer instant site crawling in Canvas to import their existing brand assets and landing pages within minutes.
8. **Contextual AI Voice Introduction**: The AI Voice Assistant (*Myra* or *Aarav*) delivers a tailored 15-second voice welcome introducing industry-specific quick actions, with 1-click `+ New Chat` reset for clean starts.
9. **Personalized Workspace Customization**: Highlight `#cora-customize-dashboard-btn` during onboarding so owners can curate their top 4 metrics and pin favorite tools to the mobile navigation island.
10. **Mobile First Usability**: Guarantee all onboarding steps render as smooth bottom-up slide sheets on mobile viewports.

---

*Cora Strategic Onboarding Brief v4.9.166 — Architecture & Growth Team.*
