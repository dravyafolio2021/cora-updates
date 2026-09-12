# Cora Platform — Comprehensive Platform & Onboarding Strategic Brief
> **Target Audience for this Document**: AI Product & Growth Strategist Agent  
> **Objective**: Comprehensive platform architectural overview, feature breakdown, and onboarding evaluation brief to formulate optimal user onboarding, product activation, and launch strategies for Cora.  
> **Platform Version**: `v4.9.59` | **Date**: September 2026

---

## 1. Executive Summary & Value Proposition

**Cora** is an enterprise-grade, all-in-one multi-tenant SaaS workspace and agency operating system specifically engineered for Indian service-based businesses and creative agencies, with deep vertical specializations in **Photography Studios**, **Real Estate Brokerages**, and **Digital Marketing Agencies**.

### The Core Problem Cora Solves
Service agencies in fast-growing markets like India are fragmented across disconnected tools:
* WhatsApp / DMs for client inquiries
* Google Sheets / Excel for lead tracking and shoot/showing schedules
* Paper registers and scattered spreadsheets for employee rosters and commissions
* Canva / Photoshop / Google Drive for media proofing and delivery
* Tally / Manual Word templates for GST invoicing and agreements
* Generic AI tools (ChatGPT) with zero context on agency operations or past clients

### The Cora Solution
Cora consolidates the entire agency lifecycle into a single, unified, white-labeled workspace:
1. **Lead & Inquiry Capture**: Drag-and-drop Kanban CRM pipeline with numeric phone validation, instant Meta WhatsApp Cloud API, and SMTP email alerts.
2. **Operations & Dispatch**: Visual monthly calendar, timeline crew assigner, equipment gear custody logs, and showings coordinator.
3. **Field Ops & Live Geolocation Tracking**: Real-time GPS tracking, stop/rest detection, average velocity math, route replay, and free HD multi-layer maps (Esri Satellite, Streets, OSM, CartoDB Dark).
4. **Multimodal Team Migration Hub**: Vision OCR roster ingestion parsing physical attendance sheets/PDFs into active user accounts in seconds with strict single workspace owner enforcement.
5. **Studio-Grade Media Hub**: Multi-ratio crop presets (`1:1`, `4:3`, `16:9`), SEO metadata tagging, and client delivery galleries.
6. **GST-Compliant Document Vault**: Legally binding E-Sign contracts, auto-calculated CGST/SGST/IGST invoicing, and SHA-256 audit registries.
7. **Dynamic AI Co-Founder Panel**: Dual-mode (Chat & Live Duplex Voice) assistant with bidirectional self-learning RAG and contextual action cards.
8. **Forms & Reviews 2.0**: 26 hardened intake widgets, AI Conversion Doctor with funnel drop-off analytics, and automated trigger flows.
9. **Dual-Engine Canvas Builder & Migrator**: White-labeled Elementor + In-browser Visual HTML Editor + 1-Click Universal Website Migrator scraping external sites into editable draft themes.
10. **Interactive Onboarding Tour System**: High-contrast guided walkthrough introducing platform KPIs, lead flows, and builder tools.
11. **App Modules (Feature Hub)**: Tenant-level module customization with explicit save workflow and batch toggles.
12. **God-Level Super Admin Console**: 11-tab administrative control center with real-time MRR telemetry, global AI token pool, tenant capability matrix, and emergency controls.

---

## 2. Platform Architecture & Technology Stack

```
+---------------------------------------------------------------------------------------+
|                                    CORA FRONTEND & PWA                                |
|  • Pure Light Mode (Shopify/Notion Zinc Palette)  • Custom Monochromatic Toast Engine |
|  • Adaptive Mobile Floating Island Nav            • Zero Mobile Side Drawers (Sheets) |
|  • Responsive Mobile PWA (Dynamic Icon Sync, 0ms Tap Latency, Offline Cache Fallback) |
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
+-------------------------------------------+-------------------------------------------+
         |                                  |                                  |
         v                                  v                                  v
+------------------+              +-------------------+              +------------------+
|  AI COPILOT CORE |              |  BUSINESS ENGINES |              |  DATABASE & OPS  |
| • Continuous Voice|              | • GST Tax Engine  |              | • Custom MySQL   |
| • Dual-Mode Panel |              | • E-Sign Registry |              |   Isolation      |
| • OCR Team Parser |              | • Forms 2.0 Doctor|              | • Static Micro-  |
| • Bidirectional  |              | • WhatsApp Cloud  |              |   Cache Layer    |
|   Continuous RAG |              | • Field Ops GPS   |              | • Security Symlink|
+------------------+              +-------------------+              +------------------+
```

### Core Stack Details
* **Backend Engine**: WordPress 6.x headless/hybrid engine on PHP 8.2 with high-performance custom MySQL schema (`cora_agencies`, `cora_leads`, `cora_bookings`, `cora_ledger`, `cora_documents`, `cora_canvas_themes`, `cora_canvas_pages`, `cora_notifications`).
* **Frontend Architecture**: Monochromatic Vanilla CSS / Tailwind (strictly light-mode Notion/Shopify aesthetic; 11-step neutral `zinc` ramp `#ffffff` to `#09090b`), ES6+ JavaScript, Leaflet.js mapping, Quill.js rich WYSIWYG, SVG vector iconography (`stroke-width: 1.8-2.2`).
* **PWA & Mobile Ergonomics**: Progressive Web App with VAPID ES256 Push notifications, dynamic version-stamped manifests, bottom-up slide sheets (`translate-y-full` to `translate-y-0`), top-down floating banners, and 0ms touch response (`touch-action: manipulation;`).
* **AI Orchestration**: Multi-provider fallback router (Google Gemini 3.5 Flash / Pro Multimodal, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o) with continuous hands-free duplex voice discussion, tenant-scoped situational RAG, and MCP gateway.

---

## 3. Target User Personas & Industry Verticals

| Persona & Industry | Core Pain Points | "Aha!" Moment in Cora | Must-Have First Day Actions |
| :--- | :--- | :--- | :--- |
| **1. Photography Studio Owner** (`photography_studio`) | Double bookings, chasing client e-signatures, manual GST billing, crew allocation chaos, slow photo proofing | Generating a full GST invoice + shoot contract with e-sign link in under 60 seconds | • Select Photography Studio preset<br>• Add first shoot booking & assign crew<br>• Issue first e-sign contract |
| **2. Real Estate Brokerage Team** (`real_estate`) | Untracked WhatsApp leads, missed showing visits, unorganized property listings, slow customer outreach, dispatch blindness | Tracking live field agents on the high-definition map while leads move through Kanban stages | • Select Real Estate Brokerage preset<br>• Import / create first buyer lead<br>• View live Field Ops map |
| **3. Digital Marketing Agency** (`marketing_agency`) | Client retainer tracking, campaign funnels, scattered client approvals, disparate landing pages | Ingesting an existing client site with 1-click Website Migrator and customizing it in Visual Canvas | • Select Marketing Agency preset<br>• Migrate website in Canvas<br>• Launch intake form with AI Conversion Doctor |

---

## 4. Master Module Roster & Core Capabilities

```
+----------------------------------------------------------------------------------------------------+
|                                    16 CORE WORKSPACE MODULES                                       |
+----------------------+----------------------+----------------------+-------------------------------+
| 1. Dynamic AI Panel  | 2. Content AI Suite  | 3. Lead CRM Pipeline | 4. Dual Canvas & Migrator     |
| • Dual Chat & Voice  | • Myra AI Copilot    | • Drag & drop Kanban | • Elementor White-Label       |
| • Bidirectional RAG  | • 7 Lifecycle Tabs   | • Lead Bottom Sheet  | • Visual HTML Lovable Engine  |
| • Action Cards       | • Quill WYSIWYG      | • WhatsApp outreach  | • Universal Website Migrator  |
| • AI Quota Meter     | • GSC / IndexNow     | • Numeric Phone Guard| • AI Core Web Vitals Optimizer|
+----------------------+----------------------+----------------------+-------------------------------+
| 5. Team & Field Ops  | 6. Forms 2.0 Engine  | 7. Document Vault    | 8. Studio Media Hub           |
| • Multimodal Vision  | • 26 Form Widgets    | • 5-Step Doc Wizard  | • 1:1, 4:3, 16:9 crop         |
| • OCR Roster Parser  | • AI Conversion Doc  | • GST Tax Engine     | • Synced breadcrumb header    |
| • Field Ops Live GPS | • WhatsApp/SMTP alerts| • Legal E-Sign Audit| • Client proofing             |
| • Free HD Multi-Maps | • Embed Runtime Modal| • SAC code catalog   | • SEO Metadata tagger         |
+----------------------+----------------------+----------------------+-------------------------------+
| 9. App Modules Hub   | 10. Email & SMTP     | 11. Crew & Equipment | 12. Financial Intelligence    |
| • Dynamic Enablement | • Hostinger Relay    | • Timeline crew grid | • Multi-Tenant Cash Ledger    |
| • Explicit Save Bar  | • Dynamic variables  | • Gear check-in/out  | • 30-Day Runway Forecast      |
| • Batch Toggles      | • HTML email builder | • Client task assign | • Deal Feasibility Simulator  |
+----------------------+----------------------+----------------------+-------------------------------+
| 13. Voice AI Duplex  | 14. Super Admin Base | 15. Notification Bell| 16. Public Docs & Tour        |
| • Web Speech Stream  | • 11-Tab Super Admin | • Web Push (VAPID)   | • 3-Column Notion doc (/docs) |
| • Natural Indian TTS | • MRR/ARR Telemetry  | • Attendance Push    | • AI Docs Playground          |
| • Full-Height Canvas | • Token Allocation   | • Morning briefing   | • Platform Onboarding Tour    |
+----------------------+----------------------+----------------------+-------------------------------+
```

---

## 5. Strategic Onboarding Recommendations

1. **Interactive Platform Tour Trigger**: Upon first workspace login, automatically offer or trigger `window.coraStartPlatformTour()` guiding the user across telemetry KPIs, AI Co-Founder panel, CRM pipeline, and Document Vault.
2. **Zero-Friction Industry Select**: On first launch (`/workspace/onboarding`), present clear visual cards for *Photography Studio*, *Real Estate Brokerage*, and *Digital Marketing Agency*.
3. **Multimodal Team Quick-Import & Field Ops**: Allow owners to snap a picture of their existing team sheet to batch-invite their whole staff on Day 1, and immediately toggle Field Ops GPS tracking for field operations.
4. **Universal Website Ingestion**: For marketing agencies and web studios, offer instant site crawling in Canvas to import their existing brand assets and landing pages within minutes.
5. **Contextual AI Voice Introduction**: The AI Voice Assistant (*Myra* or *Aarav*) delivers a tailored 15-second voice welcome introducing industry-specific quick actions.
6. **Mobile First Usability**: Guarantee all onboarding steps render as smooth bottom-up slide sheets on mobile viewports.

---

*Cora Strategic Onboarding Brief v4.9.59 — Architecture & Growth Team.*
