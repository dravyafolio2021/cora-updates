# Cora Platform — Comprehensive Platform & Onboarding Strategic Brief
> **Target Audience for this Document**: AI Product & Growth Strategist Agent  
> **Objective**: Comprehensive platform architectural overview, feature breakdown, and onboarding evaluation brief to formulate optimal user onboarding, product activation, and launch strategies for Cora.  
> **Platform Version**: `v4.9.401` | **Date**: October 2026

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
5. **Field Ops & Live Geolocation Tracking**: Real-time GPS tracking with session login auto-start, background watchPosition heartbeat pings, beacon-backed logout flush (`navigator.sendBeacon`), stop/rest detection, average velocity math, route replay, and free HD multi-layer maps (Esri Satellite, Streets, OSM, CartoDB Dark).
6. **Multimodal Team Migration & Dynamic Role Governance**: Vision OCR roster ingestion parsing physical attendance sheets/PDFs, dynamic custom role creator with module-level capability scoping, safe deletion with member reassignment, scroll-freeze-free drawer architecture, active-only equal AI token budget allocation, desktop/mobile tab customizer, and initials-based SVG avatars.
7. **Studio-Grade Media Hub & Proofing Telemetry**: Multi-ratio crop presets (`1:1`, `4:3`, `16:9`), public media proofing route interception (`/workspace/shared-media/{token}`, `share-media.php`), real-time view & download telemetry scorecards with audit activity trail, and 4-pillar digital storage footprint calculator (Media, Vault, AI RAG, Telemetry).
8. **GST-Compliant Document Vault**: Legally binding E-Sign contracts, auto-calculated CGST/SGST/IGST invoicing, and SHA-256 audit registries.
9. **CORA AI Assistant & Dual-Mode Copilot**: Dual-mode (Chat & Live Duplex Voice) assistant with standardized CORA AI branding, modern sparkle SVG icon, `#cora-ai-history-drawer` chat history slide-in drawer, 1-click `+ New Chat` reset, generative action cards, 1-click blog draft generator, 1-2 line conciseness standard, and balanced brace action tag parsing (`cora_ai_extract_balanced_json`).
10. **Enterprise AI Safety Guardrails**: Built-in automated policy enforcement across 6 violation categories (`explosives_weapons`, `terrorism_violence`, `nudity_explicit`, `religious_defamation_conflict`, `self_harm`, `jailbreak_injection`), security audit logging in `wp_cora_security_incidents`, real-time automated dual escalation to Platform Super Admins AND Workspace Owners, and RBAC action execution capability checks.
11. **Content AI Suite & Bulk Operations Engine**: Notion-styled Content Library with multi-row checkboxes, floating docked bulk toolbar (`#content-bulk-actions-bar`), bulk stage progression/category/export/delete, fixed-width dropdown architecture, 3-column mobile Opportunities grid, flush sticky sub-tabs with `pan-x` touch swipe, and Quill WYSIWYG editor.
12. **Form AI Architect & Funnel Analytics**: Full CRUD lifecycle engine supporting 15 native field types, multi-step wizards, sandboxed full-height live preview modal, 3-stage funnel analytics (*Views*, *Started*, *Leads Captured*), AI Conversion Doctor recommendations with estimated lift, and question completion breakdown.
13. **Multi-Engine Canvas Website & Theme Engineering Platform**: White-labeled Elementor + In-browser Visual HTML Editor + 1-Click Universal Website Migrator + Next.js Headless Git Repository Integration (`class-cora-nextjs-integration.php`) with GitHub App Router route scanner, headless REST endpoints (`/wp-json/cora-canvas/v1/*`), on-demand ISR revalidation, live dev server detection, and build preview proxy router.
14. **Affiliate & Referral Ecosystem**: Dual-reward growth engine (+100 AI credits on free signup, 40% recurring commission on paid plans), 3-step partner enrollment screener, 6-plan pricing & commission matrix, and official vector share marks.
15. **Stationery Manufacturing & Van Sales Dual Engine**: Central plant command center, 3-step SKU studio drawer, margin telemetry cards, 3-step van dispatch wizard with weight-based allocation (standard base: ₹401.25/kg), real-time spot billing with auto-print GST tax invoices, itemized Sales Register table, printable ledger, and warehouse dispatch manifest print with 4-digit numeric bill numbers.
16. **Single 24-Hour Executive PDF Reporting & Anti-Spam Policy**: Strips email dispatch from ephemeral micro-events (routing 100% to in-app bell & PWA push) and delivers a single, consolidated 24-Hour Executive PDF Report strictly once per 24 hours per owner.
17. **Desktop Centered KPI Analytics Container & Guaranteed 4 Scorecards**: Centered dashboard analytics container constrained to max 60% viewport width with a guaranteed 4-metric scorecard layout (2x2 mobile grid, 1x4 desktop row) and dynamic user name resolution.
18. **App Modules (Feature Hub 24-Module Directory) & Core Foundation Lock**: 24 Core Foundation and Domain modules across 5 categories with immutable foundation module locks (`blogs`, `forms`, `team-roles`, `media`, `vault`), Rule 13 zero-outline tonal surface styling, unified search/status filters, reactive toggles, and route decoupling.
19. **God-Level Super Admin Console**: 11-tab administrative control center with real-time MRR telemetry, global AI token pool, tenant capability matrix, security incident logs, emergency controls, 1-click industry switcher, and Web Push device telemetry.
20. **Universal Remote MCP Server & OAuth 2.1 Gateway**: Connects external AI assistants (ChatGPT, Claude, Google Gemini) via Model Context Protocol JSON-RPC 2.0 (`/wp-json/cora-mcp/v1/rpc`) and OpenAPI 3.0.1 (`/wp-json/cora-mcp/v1/openapi.json`) with RFC 7591 dynamic client registration, interactive Read/Write OAuth 2.1 consent panel, and a curated 28 Core Actions ceiling for ChatGPT Custom GPTs.
21. **Edge-to-Edge Draggable Lead Drawer & Bi-Directional Sync**: CRM leads inspector auto-portaled to `document.body` with transparent backdrop, left-edge draggable resizer (persisted to localStorage), collapsible accordion groups, and sanitized team assignee dropdowns.
22. **Tokenized Administration Email Verification & Locked Security State**: Dynamic status badges (Verified 🟢 / Pending 🟡), read-only locked state, 24-hour expiration token dispatch via authenticated sender `heycora@claraverse.in`, and 100 daily AI credits quota matrix.
23. **Canvas Add Theme Wizard 2x2 Overhaul & Starter Prompts**: Add Theme Wizard with active-voice 2x2 grid layout, live search filtering, sticky bottom action bar, 6 production template presets (Agency, SaaS, Consulting, Photography Studio, Stationery & Van Sales, Real Estate Brokerage) with realistic preview screenshots (`assets/img/tmpl_*`), 1-click prompt extraction for Claude & ChatGPT, dual-mode code connector (HTML paste / file dropzone), and native lead form ingestion into `wp_cora_leads`.
24. **Turnkey Official ChatGPT Plugin & Remote MCP 3.0.1 Package**: `cora-chatgpt-plugin/` distribution containing `ai-plugin.json`, hardened OpenAPI 3.0.1 specifications for 28 curated tools adhering strictly to ChatGPT Custom GPT Actions parameter validation (zero emojis in descriptions, non-empty properties objects, omitted empty required arrays), and prompt definitions.
25. **Super Admin Dynamic PWA Badging & Action Restoration**: Dynamic `ADMIN` badge rendering across 512/192/Apple/Favicon icons for Super Admins, role-scoped Web App Manifest (`CORA Workspace (Admin)`), and Super Admin workspace action button portaling to `document.body` with seamless launch routing restoration.
26. **Standardized Canvas Drawers & Live Action Playground**: Unified `.cora-drawer` slide-in sheets across the Canvas Template Editor with background scroll lock (`body.cora-canvas-drawer-open`), event listener recovery, and an integrated AI Tools MCP Gateway & Live Action Playground (`nl`, `guided`, `dev` modes) for live tool testing and 1-click code reflection.
27. **AI Run Quota Standardization & Fair Usage Pacing (v4.9.364)**: Standardized monthly quotas (Pro: 15,000 runs/mo, Scale: 25,000 runs/mo) with weekly soft-limit throttling (3,750 Pro / 6,250 Scale runs/week), Super Admin rate-limit bypass, and session lock elimination.
28. **Endorsely Affiliate & Referral Tracking Suite (v4.9.364)**: Platform-wide Endorsely tracking (`app.endorsely.com/assets/affiliate-tracking.js`) with dynamic conversion attribution on verified payments, seamless INR/USD checkout sheets, and automatic 18% GST calculation.
29. **Canvas Live HTML Preview Resilience Shield & Auto-Tailwind (v4.9.358 - v4.9.363)**: Auto-Tailwind CDN injection, error boundaries, zero blank sections guarantee, postMessage sync, and live static site styling parity.
30. **Unified AI Editor & Workspace Forms Embedding Suite (v4.9.360 - v4.9.362)**: Unified 'Edit with AI' toolbar action in Live Code Editor, streamlined minimalist toolbar without viewport switchers, and `[cora-form id="..."]` / iframe generator to embed dynamic lead capture forms in static pages.
31. **Marketing Site Reform & Dual-Track Onboarding (Oct 2026)**: Repositioned homepage value proposition to *"Build websites. Run your business, with an AI co-founder."* Implemented dual high-velocity onboarding: primary Google One-Tap onboarding for instantaneous workspace creation, and secondary "Explore free templates" linking to 16+ pre-seeded turnkey schemas at `/use-cases`. Upgraded marketing navigation into "Tools & Templates" uniting free agency business calculators with production website themes.
32. **Sticky Floating AI SDR Bar & Objection Handling Engine (Oct 2026)**: Portals the AI sales co-founder directly to `document.body` for true viewport bottom-center positioning without backdrop overlay. Delivers query-aligned active voice answers, industry pills (Web Design, Branding, Retainers, App Dev, Media), automated objection handling, and dedicated conversion coupons (`MIGRATEFREE`, `CORA10`) linking to checkout.
33. **Multi-Format Frontend Asset & Template Ecosystem (16+ Schemas) (Oct 2026)**: Complete distribution engine at `/templates` providing 16+ production-ready frontend assets across 4 distinct formats: Native Landing Pages (Tailwind/Next.js), AI Section Prompts (Claude/Cursor), Elementor Template Kits (JSON blueprints), and Lovable/React Projects, complete with live modal previews and automated lead routing into `wp_cora_leads`.
34. **Autonomous AI Workforce & Structured Domain Personas (Oct 2026)**: Integrated autonomous AI workforce spanning 9 specialized domain agents (Operations, Sales, Contracts, Knowledge, Creative, Delivery, Finance, Legal, Marketing) with dedicated operational capabilities, active-voice descriptions, and curated high-resolution portraits.
35. **Cora Forms Cards Overhaul & Universal 5th Tab "All Entries" (v4.9.372)**: Overhauled Forms dashboard cards with live conversion rate badges, question counts, and quick actions. Introduced the universal 5th tab `#all-entries` providing a single cross-form aggregate submissions table with real-time text search, form filtering, status pills, and direct detail inspection.
36. **Universal Edge-Docked Entry Inspector & Zero-Backdrop Clarity Shield (v4.9.374)**: Edge-docked right sheet drawer (`#form-submission-inspector-drawer`) for inspecting form submissions with transparent zero-backdrop clarity shield (`bg-transparent pointer-events-none`), allowing full visibility of underlying table rows while viewing questions, answers, metadata, IP, user-agent, and delete actions.
37. **Client Timezone Engine & Localized Timestamps (v4.9.374)**: Client-side timezone detection (`Intl.DateTimeFormat().resolvedOptions().timeZone`) and formatting engine automatically converting all submission timestamps to the user's localized date/time format with UTC tooltip disclosures.
38. **Dynamic Affiliate Referral Tracking, Coupon Discount Engine & Checkout Attribution (v4.9.374)**: Ingests `ref` parameters across URL queries, localStorage, and cookies (`cora_ref`, `cora_referral`, `endorsely_ref`), exposes `POST /wp-json/cora/v1/referrals/track` for click/conversion logging, and applies automated partner coupon discounts (10%–20%) directly in checkout sheets with instant price adjustments.
39. **Canonical Industry Solutions Directory & 301 Redirect Architecture (v4.9.380)**: Primary industry vertical directory migration from `/use-cases` to canonical `/industries` with 301 permanent redirect parity, refined desktop mega-menu with dedicated vector icon tiles and status pills, and 10+ operational vertical models with live cross-industry capability matrix.
40. **Autonomous AI Multi-Agent Workforce & 4-Row Infinite Skills Marquee (v4.9.379)**: Dedicated `/ai-agent` landing canvas with neural portrait cutout blend (`cora_hero_agent_cutout.png`), hardware-accelerated 4-row infinite skills marquee across Operations, Sales, Finance, and Creative Delivery, and deployment health check telemetry (`/api/health/agent`).
41. **Features Catalog Directory & Lenis Scroll Boundary Isolation (v4.9.378)**: Overhauled `/features` capability catalog with compact single-line sidebar navigation, `data-lenis-prevent` scroll boundary isolation eliminating wheel-scroll trapping, search icon overlap fix, and 40+ active-voice visual capability cards.
42. **Enterprise Brand Asset Registry & Turnkey Vector SVG Kit (v4.9.377)**: Modernized `/brand` center with 1-click turnkey SVG vector download kit for Primary, Inverted, and Symbol marks, official design token reference, and Rule 13 zero-outline styling compliance.
43. **Agency Story Canvas & Chronological Journey Ribbon (v4.9.376)**: Redesigned `/about` full-width narrative layout with connected vertical milestone rail ribbon eliminating floating tick gaps, responsive mobile spine on 375px+ viewports, Core Values bento cards, and dynamic operational week calendar.
44. **Contact Form Multi-Field Validation Suite & Navigation Synchronization (v4.9.375)**: Multi-field client/server validation suite on `/contact` across Name, Email, Phone, Company, and Topic with monochromatic alerts and ARIA accessibility, unified global navigation across desktop/mobile/footer, and documentation layout polish.
45. **Agency Projects & Portfolio Case Studies Module (`/workspace/projects`, `/projects`) (v4.9.381)**: Full-lifecycle agency case study management in `wp_cora_projects` with 6-tab studio editor (Overview, Narrative Story, Impact KPIs, Media Gallery, Testimonial, SEO/CTA), live publish toggling, and public frontend showcase gallery featuring 4-Act transformation storytelling (Executive Summary, Challenge, Solution, Deliverables), verified client testimonials, numerical KPI cards in `JetBrains Mono` (`+340% ROAS`, `₹4.2 Cr Revenue`), and connected JSON-LD schemas.
46. **Public Micro-Tools Growth Engine & Canonical Tool Registry (`/tools`) (v4.9.381)**: Reconciled, 63 search-optimized client-side micro-tools across 11 categories (GST calculator, retainer margin tool, scope creep calculator, PDF/Sheets/Image suites) with zero-PII privacy architecture, canonical tool registry (`lib/tools-canonical-registry.ts`), deterministic mathematical modeling, and seamless contextual conversion bridges to Cora workspace modules without intrusive paywalls.
47. **Canvas Themes GitHub CI/CD & Full-Lifecycle Integration (`/workspace/canvas`) (v4.9.381)**: Native Git version control docked at extreme right edge of Canvas sub-navigation (`includes/class-cora-github-integration.php`), supporting Personal GitHub PAT vs Organisation Managed Workspace modes, dynamic branch discovery, automated Pull Request creation with visual layout diffs, 1-click merging, and 1-click snapshot rollback (`cora_github_revert_commit`) with tenant-scoped cryptographic sandboxing (`ws-{agency_id}`).
48. **Dynamic Forms-to-Lead CRM Auto-Conversion Pipeline (v4.9.381)**: Inbound submissions from public lead capture forms and embedded widgets automatically synthesize into high-velocity CRM lead records (`wp_cora_leads`), extracting client contact details, projected deal value, and source attribution while triggering instantaneous top-floating monochromatic toast feedback (`window.coraShowToast`) and Web Push alerts without third-party webhooks.
49. **Canvas Themes 4-KPI Analytical Bar & Sticky 3-Tab Sub-Navigation (v4.9.381)**: High-density 2x2 mobile / 1x4 desktop diagnostic scorecard bar (`CWV Score`, `LCP Velocity`, `Themes Library Quota`, `Published Pages & SEO`) paired with sleek ~40px sticky sub-navigation bar (`Theme Library`, `Speed & Core Web Vitals`, `Migration & Tools`) with extreme-edge GitHub docking and touch-action swipe support.
50. **Editorial Knowledge Engine & High-Authority Article Library (`/articles`) (v4.9.381)**: Structured long-form editorial library (`lib/articles-data.ts`) with schema-validated JSON-LD Knowledge Graphs, semantic entity linking, answer-first executive summaries, and citation anchors optimized for Generative Engine Optimization (GEO) across ChatGPT Search, Perplexity, and Google AI Overviews.
51. **Shopify-Inspired Main-Area Docked View Panel Architecture (v4.9.382 / SOP Rule 15)**: Workspace editing drawers, creators, and case study builders slide up smoothly from the bottom into a clean, docked main-area view panel below the global top header (`top: 52px; height: calc(100vh - 52px)`). Panels dynamically scale to available canvas width based on sidebar status, enforce zero dimming backdrops to preserve full underlying context, and lock scrolling strictly to `main.cora-main` without freezing the body or sidebar.
52. **Shopify-Inspired Canvas Maximize / Restore Toggle (⤢ Fullscreen Mode) (v4.9.382)**: Dedicated maximize trigger (`#cora-proj-maximize-btn`) expanding docked panels across the entire viewport width (`100vw`) for distraction-free case study composing, narrative drafting, and metric calculation, with 1-click restoration back to docked canvas mode.
53. **Global Sticky Workspace Header & Mobile Expandable Sub-Tabs Search (v4.9.383 - v4.9.384)**: Global sticky header pinning (`top: 0; z-index: 100020;`) paired with pixel-perfect sub-tabs vertical alignment, Level 01 Segmented view switchers (Table vs Grid), and 1-tap expandable search inputs (< 640px) that prevent horizontal mobile overflow.
54. **Google Business Profile & Google Maps AI Autopilot (v4.9.388 - v4.9.389)**: Unified local presence autopilot suite (`/workspace/google-profile`, `views/view-google-profile.php`) featuring a multi-state reactive hub (Disconnected, Pending OAuth, Connected), 5-tab segmented control (Overview & Performance, Reviews AI Copilot, Google Posts Publisher, Business Hours, Local SEO & Keywords), 4 AI review reply tones (Professional, Empathetic, Promotional, Brief), 7-day operating hours editor with 1-click 'Apply Mon-Fri', Local SEO 3-Pack rank tracker, API credentials drawer (`#cora-gbp-credentials-drawer`), and proactive Google Cloud 0-quota advisory warning card (`#cora-gbp-quota-advisory`) with instant 1-click sandbox simulation fallback (`window.coraGbpAuthorizeDemoAccount`) preventing onboarding and testing blockages.
55. **Dynamic Plugins, Modules & AI Skills Extensibility Engine (v4.9.387)**: Full modular extensibility runtime (`class-cora-plugins-skills-engine.php`) supporting dynamic plugin registration, AI skill manifests, execution hooks, and external API key governance across 4 dedicated tables (`wp_cora_workspace_plugins`, `wp_cora_workspace_skills`, `wp_cora_workspace_api_keys`, `wp_cora_workspace_plugin_logs`). Bridges dynamic custom skills directly into the Universal Remote MCP Tool Registry (`cora_mcp_tools`) and OpenAPI 3.0.1 gateway for external AI assistants (ChatGPT, Claude, Gemini).
56. **Enterprise Client Onboarding Studio & Strict Role Isolation (v4.9.386 - v4.9.387)**: Multi-step client onboarding wizard (`views/view-clients.php`) with step-by-step progress tracking, dedicated WordPress client role provisioning (`cora_client`), tokenized portal access (`public-client-portal.php`), and strict route interception in `cora-workspace.php` automatically redirecting `cora_client` users away from admin workspace screens directly to their client portal.
57. **Interactive Workspace Scheduling Calendar Suite & Two-Way Google Meet Engine (v4.9.386 - v4.9.387)**: Full-featured visual calendar scheduling suite (`views/view-calendar.php`) with Month, Week, and Day views, drag-and-drop event creation, client association, automated Google Meet video conference link generation (standardized 3-4-3 syntax `meet.google.com/xxx-yyyy-zzz`), multi-crew scheduling, and real-time appointment booking synchronization into the CRM sales pipeline.
58. **Collapsible Sidebar Navigation Groups & Memory State Persistence (v4.9.385 - v4.9.386)**: Accordion-collapsible navigation group categories across the workspace sidebar (`admin-dashboard.php`), persistent collapsed/expanded state management via localStorage and AJAX user options (`cora_sidebar_group_*`), and mobile sticky sub-tabs navigation ensuring clean one-handed navigation without visual clutter.
59. **Workspace Content REST API & Canonical 9-Tool MCP Content Suite**: Full-lifecycle headless content publishing and intelligence suite under namespace `/wp-json/cora/v1/content` strictly scoped to the workspace tenant (`agency_id = %d`), authenticated via workspace API keys (`cora_ws_live_...`) or session cookies. Features 12 REST endpoints including automated keyword and topic opportunity discovery, AI content generation, bulk batch operations, and an 11-point algorithmic SEO audit (`POST /cora/v1/content/{id}/seo-audit`) checking title length, meta description, keyword density, heading hierarchy, readability, and link integrity. Fully bridged to external AI assistants via the Canonical 9-Tool MCP Content Suite (`cora_content_list_articles`, `cora_content_get_article`, `cora_content_create_article`, `cora_content_update_article`, `cora_content_delete_article`, `cora_content_publish_article`, `cora_content_unpublish_article`, `cora_content_bulk_action`, `cora_content_seo_audit`, `cora_content_get_opportunities`, `cora_content_generate_opportunities`, `cora_content_generate_draft`).
60. **Workspace Developer API & MCP Gateway Docked Drawer (Rule 15 SOP)**: Slide-out workspace developer console docked seamlessly beneath the workspace header adhering to Rule 15 SOP (`workspace-developer-gateway-drawer.php`). Provides self-service tenant API key provisioning with secure generation (`cora_ws_live_...`), SHA-256 cryptographic storage, customizable RBAC permission scopes (`content:read`, `content:write`, `plugins:read`, `plugins:write`, `skills:read`, `skills:write`), configurable rate limits (100–10,000 req/hr), live copyable client setup snippets for cURL, Python (`requests`), Node.js (`fetch`), and MCP configuration (`claude_desktop_config.json`, Cursor, ChatGPT Custom GPTs), and an interactive REST sandbox tester with 1-click endpoint execution.
61. **Super Admin Integrations & WhatsApp Telemetry Hub (v4.9.397)**: Centralized mission control hub within the Super Admin console (`views/view-super-admin.php`) delivering real-time multi-tenant WhatsApp Business API telemetry. Features a live 24-Hour Customer Service Window (CSW) monitor with dynamic countdown timers, window status indicators (Active 🟢 / Expired ⚪), tenant phone number registry, automated CSW cost savings calculator (tracking estimated zero-cost marketing/utility conversation savings in INR), one-click simulated test alert trigger, and emergency kill-switch controls.
62. **WhatsApp Zero-Cost Customer Service Window (CSW) Queue Engine & Automated Messaging Suite (v4.9.390 - v4.9.401)**: High-performance WhatsApp Business API engine (`class-cora-whatsapp-gateway.php`, `cora-workspace.php`) engineered to eliminate Meta template messaging fees by maximizing free 24-Hour Customer Service Windows. Features include:
   - **24h Free Messaging Window Quick Activator**: Sticky 1-click WhatsApp activator in the profile popover (`#cora-whatsapp-24h-activator`) allowing owners to send a quick inbound ping (`START`) to open a free 24-hour conversation session, complete with automated confirmation replies.
   - **Zero-Cost CSW Queue Engine**: Intelligent dispatch router that checks recipient 24h CSW status before sending; non-urgent alerts are queued at zero cost during active windows, with automated fallback to approved utility templates (`cora_lead_alert`) when windows expire.
   - **Inbound Bot & Interactive Quick-Reply Engine**: Autonomous inbound webhook listener processing incoming client/lead messages, supporting interactive quick-reply button arrays and rich image cards without third-party bot fees.
   - **3/hr Rate Limiter & Anti-Spam Throttling**: Restricts automated outbound alerts to a maximum of 3 messages per hour per recipient, preventing notification fatigue and phone bans.
   - **Smart Lead Batching**: Automatically groups rapid successive lead inquiries arriving within a 15-minute window into a single consolidated WhatsApp alert summary.
   - **1-Click Tokenized Executive PDF Report Delivery**: Delivers daily business summaries directly to WhatsApp with 1-click secure tokenized PDF links eliminating raw dashboard URL exposure and authentication friction.

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
|  • High-Speed Micro-Cache Memory Layer            • Locked Down Modular PHP Engine    |
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
* **Backend Engine**: Enterprise PHP Framework & High-Performance Engine on PHP 8.2 with custom MySQL multi-tenant schema (`cora_agencies`, `cora_leads`, `cora_clients`, `cora_referral_links`, `cora_referrals`, `cora_affiliate_payouts`, `cora_bookings`, `cora_ledger`, `cora_documents`, `cora_canvas_themes`, `cora_canvas_pages`, `cora_inventory_products`, `cora_inventory_consignments`, `cora_notifications`, `cora_roles`).
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
|                                 30 CORE ENGINES & WORKSPACE MODULES                                |
+----------------------+----------------------+----------------------+-------------------------------+
| 1. Dynamic AI Panel  | 2. Content AI Suite  | 3. Lead CRM Pipeline | 4. Dual Canvas & Migrator     |
| • Dual Chat & Voice  | • Myra AI Copilot    | • Ultra-Compact 3-Lvl| • Elementor White-Label       |
| • 1-Click New Chat   | • 7 Lifecycle Tabs   | • In-Column Sort/Find| • Visual HTML Lovable Engine  |
| • 3px Quota Bars     | • Quill WYSIWYG      | • 1-Tap Quick Outreach| • Add Theme Wizard 2x2 Grid   |
| • Generative Cards   | • GSC / IndexNow     | • Multi-Filter Popover| • Standardized Drawer Engine  |
+----------------------+----------------------+----------------------+-------------------------------+
| 5. Team & Governance | 6. Form AI Architect | 7. Document Vault    | 8. Studio Media Hub           |
| • Multimodal Vision  | • 15 Field Types     | • 5-Step Doc Wizard  | • 1:1, 4:3, 16:9 crop         |
| • OCR Roster Parser  | • Multi-Step Wizards | • GST Tax Engine     | • Public Proofing Route       |
| • Dynamic Custom Role| • Live Modal Preview | • Legal E-Sign Audit | • View/Download Telemetry     |
| • Active-Only AI Pool| • Funnel Analytics   | • SAC code catalog   | • 4-Pillar Storage Footprint  |
+----------------------+----------------------+----------------------+-------------------------------+
| 9. App Modules Hub   | 10. Email & SMTP     | 11. Crew & Equipment | 12. Financial Intelligence    |
| • Core Foundation    | • Hostinger Relay    | • Timeline crew grid | • Multi-Tenant Cash Ledger    |
|   Module Lock        | • Dynamic variables  | • Gear check-in/out  | • 30-Day Runway Forecast      |
| • 24-Module Registry | • HTML email builder | • Client task assign | • Deal Feasibility Simulator  |
| • Category Filter Bar| • Persistent Settings| • Field Ops Beacon   | • GST Invoice Breakdown       |
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
| 22. Universal Remote MCP Gateway  | 23. Next.js Git Connector    | 24. Van Sales & Register        |
| • OpenAPI 3.0.1 & JSON-RPC 2.0    | • App Router Route Scanner   | • 3-Step Dispatch Wizard        |
| • Curated 28 Core Actions Ceiling | • Headless REST API Suite    | • Rate per kg Allocation        |
| • Interactive OAuth Consent Panel | • On-Demand ISR Revalidation | • Spot Billing & GST Print      |
| • Template Action Playground      | • Build Preview Proxy Router | • Itemized Sales Register & CSV |
+-----------------------------------+------------------------------+---------------------------------+
| 25. Google Profile & Maps Auto    | 26. Enterprise Client Studio | 27. Scheduling & Google Meet    |
| • 4-KPI Local Metrics (Maps/SEO)  | • 5-Step Guided Onboarding   | • Month/Week/Day Visual Grids   |
| • AI Review Copilot (4 Tones)     | • cora_client Role Isolation | • 3-4-3 Google Meet Syntax      |
| • 0-Quota Advisory & Sandbox Mode | • Tokenized Portal Redirect  | • 2-Way Sync & Slot Booking     |
| • 7-Day Hours & 3-Pack Ranker     | • Custom Service Catalogues  | • Auto CRM Pipeline Ingestion   |
+-----------------------------------+------------------------------+---------------------------------+
| 28. WhatsApp CSW Queue & Reports  | 29. Super Admin Integrations | 30. Developer API & MCP Gateway |
| • Zero-Cost CSW Queue Engine      | • Real-Time 24h CSW Monitor  | • Tenant API Keys (cora_ws_...) |
| • 24h Free Window Quick Activator | • Live Countdown Timers      | • Content REST API (/content)   |
| • Inbound Bot & Quick-Reply Cards | • INR Cost Savings Telemetry | • 11-Point Algorithmic SEO Audit|
| • 3/hr Rate Limit & Lead Batching | • Multi-Tenant Window Status | • Canonical 9-Tool MCP Suite    |
| • 1-Click Tokenized Exec PDF Rep  | • 1-Click Simulated Alerts   | • Live Sandbox & Setup Snippets |
+-----------------------------------+------------------------------+---------------------------------+
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
11. **1-Click WhatsApp 24h Free CSW Activation**: Prompt new workspace owners during onboarding to trigger the 1-click WhatsApp 24h Free Customer Service Window activator (`#cora-whatsapp-24h-activator`) on their smartphone, immediately establishing an active 24-hour zero-cost messaging session to receive real-time lead alerts, batch notifications, and daily tokenized executive PDF reports without incurring Meta conversation fees.
12. **Google Business Profile 1-Click Sandbox Activation**: For local service agencies and studios, provide an immediate 1-click "Test in Sandbox Mode Now" activation (`window.coraGbpAuthorizeDemoAccount`) during local presence onboarding. This allows owners to explore GBP performance scorecards, 3-Pack rank tracking, AI review response copilot, and Google post scheduling instantly without waiting days for Google Cloud project OAuth consent verification and 0 QPM quota limit lifts.
13. **Developer API & MCP Gateway Onboarding**: For engineering teams, technical agency founders, and AI power users, introduce the docked Developer API & MCP Gateway drawer directly from onboarding. Allow instantaneous generation of workspace API keys (`cora_ws_live_...`), copy-paste integration snippets for cURL/Python/Node.js, and turnkey configuration for AI assistants (Claude Desktop via `claude_desktop_config.json`, Cursor, and ChatGPT Custom GPTs) to manage content, leads, and clients autonomously.

---

*Cora Strategic Onboarding Brief v4.9.401 — Architecture & Growth Team. Last updated: October 2026.*
