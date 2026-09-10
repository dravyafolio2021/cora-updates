# Cora Platform — Comprehensive Platform & Onboarding Strategic Brief
> **Target Audience for this Document**: AI Product & Growth Strategist Agent  
> **Objective**: Comprehensive platform architectural overview, feature breakdown, and onboarding evaluation brief to formulate optimal user onboarding, product activation, and launch strategies for Cora.  
> **Platform Version**: `v4.9.32` | **Date**: September 2026

---

## 1. Executive Summary & Value Proposition

**Cora** is an enterprise-grade, all-in-one multi-tenant SaaS workspace and agency operating system specifically engineered for Indian service-based businesses and creative agencies, with deep vertical specializations in **Photography Studios**, **Real Estate Brokerages**, and **Digital Marketing Agencies**.

### The Core Problem Cora Solves
Service agencies in fast-growing markets like India are fragmented across disconnected tools:
* WhatsApp / DMs for client inquiries
* Google Sheets / Excel for lead tracking and shoot/showing schedules
* Canva / Photoshop / Google Drive for media proofing and delivery
* Tally / Manual Word templates for GST invoicing and agreements
* Generic AI tools (ChatGPT) with zero context on agency operations or past clients

### The Cora Solution
Cora consolidates the entire agency lifecycle into a single, unified, white-labeled workspace:
1. **Lead & Inquiry Capture**: Drag-and-drop Kanban CRM pipeline with instant Meta WhatsApp Cloud API and SMTP email alerts.
2. **Operations & Dispatch**: Visual monthly calendar, timeline crew assigner, equipment gear custody logs, and showings coordinator.
3. **Studio-Grade Media Hub**: Multi-ratio crop presets (`1:1`, `4:3`, `16:9`), SEO metadata tagging, and client delivery galleries.
4. **GST-Compliant Document Vault**: Legally binding E-Sign contracts, auto-calculated CGST/SGST/IGST invoicing, and SHA-256 audit registries.
5. **Continuous Hands-Free AI Voice Copilot**: Real-time voice discussion engine with 4 curated personalities, auto-endpointing, and 9 regional Indian dialects.
6. **Forms & Reviews 2.0**: 26 hardened intake widgets, AI Conversion Doctor with funnel drop-off analytics, and automated trigger flows.
7. **Dual-Engine Canvas Builder**: White-labeled Elementor + In-browser Visual HTML Editor with inline editing, media swapper, and AI Core Web Vitals optimization.
8. **App Modules (Feature Hub)**: Tenant-level module customization with explicit save workflow and batch toggles.

---

## 2. Platform Architecture & Technology Stack

```
+---------------------------------------------------------------------------------------+
|                                    CORA FRONTEND & PWA                                |
|  • Pure Light Mode (Shopify/Notion Zinc Palette)  • Custom Monochromatic Toast Engine |
|  • Adaptive Mobile Floating Island Nav            • Zero Mobile Side Drawers (Sheets) |
|  • Responsive Mobile PWA (Dynamic Icon Sync, 0ms Tap Latency, Offline Cache Fallback) |
+-------------------------------------------+-------------------------------------------+
                                            |
                                  REST / AJAX / WebSockets
                                            |
+-------------------------------------------v-------------------------------------------+
|                                   CORA WORKSPACE CORE                                 |
|  • Modular PHP 8.2 Views (50+ Modular Subviews)   • Custom Routing & Multi-Tenant RBAC|
|  • High-Speed Micro-Cache Memory Layer            • Locked Down WordPress Backend     |
|  • Multi-Tenant Isolation (`agency_id` / `branch`) • Standardized Header Action Bar   |
+-------------------------------------------+-------------------------------------------+
         |                                  |                                  |
         v                                  v                                  v
+------------------+              +-------------------+              +------------------+
|  AI COPILOT CORE |              |  BUSINESS ENGINES |              |  DATABASE & OPS  |
| • Continuous Voice|              | • GST Tax Engine  |              | • Custom MySQL   |
| • Myra AI Engine |              | • E-Sign Registry |              |   Isolation      |
| • Multi-Provider |              | • Forms 2.0 Doctor|              | • Static Micro-  |
| • MCP / RAG Sync |              | • WhatsApp Cloud  |              |   Cache Layer    |
+------------------+              +-------------------+              +------------------+
```

### Core Stack Details
* **Backend Engine**: WordPress 6.x headless/hybrid engine on PHP 8.2 with high-performance custom MySQL schema (`cora_agencies`, `cora_leads`, `cora_bookings`, `cora_ledger`, `cora_documents`, `cora_canvas_themes`, `cora_canvas_pages`, `cora_notifications`).
* **Frontend Architecture**: Monochromatic Vanilla CSS / Tailwind (strictly light-mode Notion/Shopify aesthetic; 11-step neutral `zinc` ramp `#ffffff` to `#09090b`), ES6+ JavaScript, Quill.js rich WYSIWYG, SVG vector iconography (`stroke-width: 1.8-2.2`).
* **PWA & Mobile Ergonomics**: Progressive Web App with VAPID ES256 Push notifications, dynamic version-stamped manifests, bottom-up slide sheets (`translate-y-full` to `translate-y-0`), top-down floating banners, and 0ms touch response (`touch-action: manipulation;`).
* **AI Orchestration**: Multi-provider fallback router (Google Gemini 3.5 Flash, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o) with continuous hands-free voice discussion, situational awareness RAG, and MCP gateway.

---

## 3. Target User Personas & Industry Verticals

| Persona & Industry | Core Pain Points | "Aha!" Moment in Cora | Must-Have First Day Actions |
| :--- | :--- | :--- | :--- |
| **1. Photography Studio Owner** (`photography_studio`) | Double bookings, chasing client e-signatures, manual GST billing, crew allocation chaos, slow photo proofing | Generating a full GST invoice + shoot contract with e-sign link in under 60 seconds | • Select Photography Studio preset<br>• Add first shoot booking & assign crew<br>• Issue first e-sign contract |
| **2. Real Estate Brokerage Team** (`real_estate`) | Untracked WhatsApp leads, missed showing visits, unorganized property listings, slow customer outreach | Visualizing incoming inquiries moving through Kanban stages with instant push alerts | • Select Real Estate Brokerage preset<br>• Import / create first buyer lead<br>• Schedule a property showing |
| **3. Digital Marketing Agency** (`marketing_agency`) | Client retainer tracking, campaign funnels, scattered client approvals, disparate landing pages | Setting up a campaign intake form with WhatsApp notifications & building a landing page with Canvas | • Select Marketing Agency preset<br>• Create intake form with AI Conversion Doctor<br>• Launch landing page in Visual Canvas |

---

## 4. Master Module Roster & Core Capabilities

```
+----------------------------------------------------------------------------------------------------+
|                                    14 CORE WORKSPACE MODULES                                       |
+----------------------+----------------------+----------------------+-------------------------------+
| 1. Voice AI Engine   | 2. Content AI Suite  | 3. Lead CRM Pipeline | 4. Dual Canvas Builder        |
| • Continuous Voice   | • Myra AI Copilot    | • Drag & drop Kanban | • Elementor White-Label       |
| • 4 Voice Presets    | • 7 Lifecycle Tabs   | • Lead Bottom Sheet  | • Visual HTML Lovable Engine  |
| • 9 Regional Dialects| • Quill WYSIWYG      | • WhatsApp outreach  | • Inline contenteditable      |
| • Real-time Soundwave| • GSC / IndexNow     | • Value forecasting  | • AI Core Web Vitals Optimizer|
+----------------------+----------------------+----------------------+-------------------------------+
| 5. Forms 2.0 Engine  | 6. Document Vault    | 7. Studio Media Hub  | 8. App Modules (Feature Hub)  |
| • 26 Form Widgets    | • 5-Step Doc Wizard  | • 1:1, 4:3, 16:9 crop| • Dynamic Module Enablement   |
| • AI Conversion Doc  | • GST Tax Engine     | • SEO Metadata tagger| • Explicit Save Workflow      |
| • WhatsApp/SMTP alerts| • Legal E-Sign Audit| • Client proofing    | • Batch Toggle Controls       |
+----------------------+----------------------+----------------------+-------------------------------+
| 9. Email Management  | 10. Crew & Equipment | 11. Master Calendar  | 12. Financial Intelligence    |
| • SMTP diagnostics   | • Timeline crew grid | • Day/Week/Month grid| • Multi-Tenant Cash Ledger    |
| • Dynamic variables  | • Gear check-in/out  | • Showing coordinate | • 30-Day Runway Forecast      |
| • HTML email builder | • Client task assign | • Multi-day timeline | • Deal Feasibility Simulator  |
+----------------------+----------------------+----------------------+-------------------------------+
| 13. Multi-Channel Bell| 14. Public Docs & MCP|                      |                               |
| • Web Push (VAPID)   | • 3-Column Notion doc|                      |                               |
| • Quiet Hours / DND  | • AI Docs Playground |                      |                               |
| • Daily brief digest | • JSON-RPC MCP Gate  |                      |                               |
+----------------------+----------------------+----------------------+-------------------------------+
```

---

## 5. Strategic Onboarding Recommendations

1. **Zero-Friction Industry Select**: On first launch (`/workspace/onboarding`), present clear visual cards for *Photography Studio*, *Real Estate Brokerage*, and *Digital Marketing Agency*.
2. **Contextual AI Voice Introduction**: The AI Voice Assistant (*Myra* or *Aarav*) delivers a tailored 15-second voice welcome introducing industry-specific quick actions.
3. **Progressive Activation Stepper**: Guide users through a 3-step activation:
   - Step 1: Confirm agency details & currency/GST settings.
   - Step 2: Create first CRM lead or client intake form.
   - Step 3: Preview the live dashboard with sample industry data.
4. **Mobile First Usability**: Guarantee all onboarding steps render as smooth bottom-up slide sheets on mobile viewports.

---

*Cora Strategic Onboarding Brief v4.9.32 — Architecture & Growth Team.*
