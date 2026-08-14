# Cora Platform — Comprehensive Platform & Onboarding Strategic Brief
> **Target Audience for this Document**: AI Product & Growth Strategist Agent  
> **Objective**: Comprehensive platform architectural overview, feature breakdown, and onboarding evaluation brief to formulate optimal user onboarding, product activation, and launch strategies for Cora.  
> **Platform Version**: `v3.4.38` | **Date**: August 2026

---

## 1. Executive Summary & Value Proposition

**Cora** is an all-in-one, multi-tenant SaaS workspace and agency operating system specifically engineered for service-based businesses and creative agencies (with deep vertical specializations in **Photography Studios** and **Real Estate Brokerages**).

### The Core Problem Cora Solves
Service agencies in fast-growing markets like India are fragmented across disconnected tools:
* WhatsApp / DMs for client inquiries
* Google Sheets / Excel for lead tracking and shoot schedules
* Canva / Photoshop / Google Drive for media proofing and delivery
* Tally / Manual Word templates for GST invoicing and agreements
* Generic AI tools (ChatGPT) with no context on agency data or past shoots

### The Cora Solution
Cora consolidates the entire agency lifecycle into a single, unified, white-labeled workspace:
1. **Lead & Inquiry Capture**: Drag-and-drop Kanban CRM pipeline with instant WhatsApp/Email alerts.
2. **Shoot & Shift Management**: Visual calendar, timeline crew assigner, equipment check-in/out.
3. **Studio-Grade Media Hub**: Multi-ratio crop presets (`1:1`, `4:3`, `16:9`), SEO metadata tagging, client galleries.
4. **GST-Compliant Document Vault**: Legally binding E-Sign contracts, auto-calculated CGST/SGST/IGST invoicing, audit logs.
5. **Content & SEO AI Suite**: Native AI Copilot (**Myra**) supporting Gemini 3.5 Flash, Claude 3.5 Sonnet, and GPT-4o with live editor automation.
6. **Canvas Theme Builder**: White-labeled Elementor/Lovable theme engine with auto-Git commits and custom domains.

---

## 2. Platform Architecture & Technology Stack

```
+---------------------------------------------------------------------------------------+
|                                    CORA FRONTEND & PWA                                |
|  • Pure Light Mode (Shopify/Notion Zinc Palette)  • Custom Monochromatic Toast Engine |
|  • Responsive Mobile PWA (VAPID Push Notifications, Orientation Lock, Offline Cache)  |
+-------------------------------------------+-------------------------------------------+
                                            |
                                  REST / AJAX / WebSockets
                                            |
+-------------------------------------------v-------------------------------------------+
|                                   CORA WORKSPACE CORE                                 |
|  • Modular PHP 8.2 Views (50+ Specialized Subviews)  • Custom Routing & RBAC Engine   |
|  • Multi-Tenant Isolation (`agency_id` / `branch_id`) • Unified Header & Navigation   |
+-------------------------------------------+-------------------------------------------+
         |                                  |                                  |
         v                                  v                                  v
+------------------+              +-------------------+              +------------------+
|  AI COPILOT CORE |              |  BUSINESS ENGINES |              |  DATABASE & OPS  |
| • Myra AI Engine |              | • GST Tax Engine  |              | • Custom MySQL   |
| • Gemini/Claude/ |              | • E-Sign Registry |              |   Isolation      |
|   GPT-4o Gateway |              | • VAPID Push      |              | • WP-Cron Batch  |
| • MCP / RAG Sync |              | • SMTP Mailer     |              | • Playwright E2E |
+------------------+              +-------------------+              +------------------+
```

### Core Stack Details
* **Backend Framework**: WordPress 6.x headless/hybrid engine on PHP 8.2 with high-performance custom MySQL schema (`cora_agencies`, `cora_leads`, `cora_bookings`, `cora_ledger`, `cora_documents`, `cora_notifications`).
* **Frontend Architecture**: Monochromatic Vanilla CSS / Tailwind (strictly light-mode Notion/Shopify aesthetic; 11-step neutral `zinc` ramp `#ffffff` to `#09090b`), ES6+ JavaScript, Quill.js rich WYSIWYG, SVG vector iconography (`stroke-width: 1.8-2.2`).
* **PWA & Mobile**: Progressive Web App with VAPID ES256 Push notifications, dynamic `Stale-While-Revalidate` service workers, portrait screen orientation locking, and native "Add to Home Screen" prompts.
* **AI Orchestration**: Multi-provider fallback router (Google Gemini 3.5 Flash, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o) with contextual prompt injection (RAG knowledge base) and Model Context Protocol (MCP) gateway.

---

## 3. Target User Personas & Verticals

| Persona | Core Pain Points | "Aha!" Moment in Cora | Must-Have First Day Actions |
| :--- | :--- | :--- | :--- |
| **1. Photography Studio Owner** (Wedding, Commercial, Fashion) | Double bookings, chasing client e-signatures, manual GST billing, crew allocation chaos, slow photo proofing | Generating a full GST invoice + shoot contract with e-sign link in under 60 seconds | • Pick Photography preset<br>• Add first shoot booking<br>• Issue first e-sign contract |
| **2. Real Estate Brokerage Team** | Untracked WhatsApp leads, missed showing visits, unorganized property listings, slow customer outreach | Visualizing incoming inquiries moving through Kanban stages with instant push alerts | • Pick Real Estate preset<br>• Import / create first lead<br>• Schedule a property showing |
| **3. Creative Agency / Freelancer** | Disorganized portfolio site, content bottleneck, scattered client communication, lack of brand polish | Launching a branded portfolio page with Canvas builder & drafting SEO articles with Myra AI | • Pick Custom/Agency preset<br>• Generate blog post via AI<br>• Configure custom domain / SMTP |

---

## 4. Master Module Roster & Core Capabilities

```
+----------------------------------------------------------------------------------------------------+
|                                    12 CORE WORKSPACE MODULES                                       |
+----------------------+----------------------+----------------------+-------------------------------+
| 1. Content AI Suite  | 2. Lead CRM Pipeline | 3. Document Vault    | 4. Studio Media Hub           |
| • Myra AI Copilot    | • Drag & drop Kanban | • 5-Step Doc Wizard  | • 1:1, 4:3, 16:9 crop presets |
| • 7 Lifecycle Tabs   | • Lead Drawer        | • GST Engine (CGST/  | • SEO Alt / Caption tagger    |
| • Quill WYSIWYG      | • WhatsApp outreach  |   SGST / IGST)       | • Client proofing gallery     |
| • GSC / IndexNow     | • Value forecasting  | • Legal E-Sign Audit | • Storage quota monitor       |
+----------------------+----------------------+----------------------+-------------------------------+
| 5. Email Management  | 6. Crew & Equipment  | 7. Master Calendar   | 8. Canvas Theme Builder       |
| • SMTP diagnostics   | • Timeline crew grid | • Day/Week/Month grid| • White-label Elementor       |
| • Dynamic variables  | • Gear check-in/out  | • 5-Step Event Modal | • Lovable AI prompt bridge    |
| • HTML email builder | • Client task assign | • Shoot coordination | • Auto-Git repository sync    |
+----------------------+----------------------+----------------------+-------------------------------+
| 9. Multi-Channel Bell| 10. Forms & Reviews  | 11. Financial Hub    | 12. Public Docs & MCP         |
| • Web Push (VAPID)   | • Public review link | • Payment tracking   | • 3-Column Notion docs (/docs)|
| • Quiet Hours / DND  | • WhatsApp Hinglish  | • Revenue analytics  | • Tenant RAG Knowledge Base   |
| • Daily/Weekly digest| • Star rating badges | • Tax summaries      | • JSON-RPC MCP Gateway        |
+----------------------+----------------------+----------------------+-------------------------------+
```

---

## 5. Current Onboarding Flow & User Journey

### The Current Step-by-Step Sequence (`/workspace/onboarding`):
1. **Step 1: Authentication & Entry**
   * Google OAuth 1-Click login or Email + Password registration.
   * Dual-column layout: Left sidebar showcases interactive 3D logo pedestal, right side contains form.
2. **Step 2: Business Profile**
   * Agency Name, Contact Phone Number, Primary Business Email, Location/City.
3. **Step 3: Industry Vertical Selection**
   * User selects one of three workspace archetypes:
     - **Photography Studio** (Pre-configures Shoot Bookings, Crew Scheduler, Gear Tracker, Photo Proofing).
     - **Real Estate** (Pre-configures Property Showings, Listing Catalog, Buyer Lead Stages).
     - **Creative Agency / Custom** (General CRM, Document Studio, Content AI).
4. **Step 4: Automated Provisioning & Activation**
   * Animated setup state: initializes tenant tables, seeds sample industry data, configures workspace permissions, and redirects to `/workspace/dashboard`.
5. **Mobile PWA Add-to-Home Screen Prompt**:
   * For mobile visitors, an install guide modal triggers with iOS Safari / Android Chrome installation steps.

---

## 6. Onboarding Friction Points & Challenges to Solve

When evaluating Cora for user onboarding, the AI agent should address these key strategic challenges:

### Challenge A: Time-to-First-Value (TTFV) & The "Blank Canvas" Problem
* **Issue**: Once the workspace initializes, users arrive at a rich dashboard with 12+ navigation tabs. If there are no active leads or shoot bookings, the dashboard can feel overwhelming or empty.
* **Goal**: How can we guide the user to their first meaningful action (e.g. creating their first shoot contract, sending their first lead message, or generating an AI article) in under 3 minutes?

### Challenge B: Industry Archetype Customization vs. Information Overload
* **Issue**: Photography studios care about call times, gear, and high-res media. Real estate agents care about property square footage, price per sqft, and client showings.
* **Goal**: How can onboarding dynamically tailor the sidebar, dashboard widgets, and terminology so each industry feels like a bespoke tool built exclusively for them?

### Challenge C: Team Member & Crew Onboarding
* **Issue**: Agency owners sign up first, but the system’s true utility unlocks when their photographers, editors, and brokers log in.
* **Goal**: What is the most frictionless mechanism to prompt team invites during or immediately after the onboarding flow?

### Challenge D: Multi-Channel Activation (WhatsApp & Push)
* **Issue**: Indian SMBs rely heavily on WhatsApp and instant mobile notifications over desktop email.
* **Goal**: How to structure the onboarding sequence to get instant opt-ins for Web Push (VAPID) and WhatsApp automation setup without high drop-off?

---

## 7. Key User Journey Milestones (Day 0 to Day 30)

```
[ Day 0: Onboarding & Instant "Aha!" ]
  └─ Auth -> Industry Selection -> Pre-populated Demo Data -> First Document / Lead Created
[ Day 1 - 3: First Real Workflow ]
  └─ Real client added -> Contract with GST auto-math generated -> E-Sign link sent via WhatsApp
[ Day 7: Team & Process Habituation ]
  └─ 2+ Crew members invited -> Shoot schedule tracked on Calendar -> Push notifications active
[ Day 30: Essential Agency Operating System ]
  └─ Invoices settled -> Content Suite producing SEO pages -> Client reviews collected
```

---

## 8. Specific Areas Where Suggestions Are Needed From the AI Agent

When reviewing this platform brief, please provide concrete, actionable recommendations on:

1. **Onboarding Funnel Optimization**:
   * Should we use a modal checklist, a guided interactive walkthrough / hotspot tour, or a gamified milestone bar (e.g. "40% setup complete")?
2. **Sample / Demo Data Strategy**:
   * Should new workspaces come with pre-loaded interactive dummy data (e.g. "Sample Wedding Shoot - Rajiv & Priya" or "Sample 3BHK Penthouse Inquiry") that users can test and delete with 1 click?
3. **Empty States & Progressive Disclosure**:
   * What should the empty states across the 12 modules look like to guide action rather than show blank tables?
4. **Mobile & PWA Conversion**:
   * How best to time the "Install App / Enable Push Notifications" prompt so users don't dismiss it prematurely?
5. **Growth Levers & Viral Loops**:
   * How can client-facing artifacts (E-Sign links, Public Review forms, Client Gallery proofing links) double as an organic acquisition loop for other agency owners ("Powered by Cora")?

---
*End of Cora Platform Onboarding Brief — Ready for AI Growth & Onboarding Strategy Analysis.*
