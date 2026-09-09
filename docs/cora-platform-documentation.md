# Cora Platform — Comprehensive Platform Documentation

This document serves as the master technical specification and architectural manual for the Cora Workspace Platform (v4.9.32).

---

## Section 1: Core Theme System, PWA & Mobile Performance SOP

### 1.1 Pure Light Mode Enforcement & Dark Mode Removal
Starting in **version 3.2.83** and hardened through **v4.9.32**, dark mode support has been completely removed across all Cora platform plugins, workspace views, design tokens, and components. The platform strictly enforces a **pure light mode visual standard** platform-wide.

* **Deprecation Rationale**: Eliminates theme-switching flash/rendering artifacts, reduces CSS bundle complexity, guarantees predictable color contrast compliance, and enforces strict visual continuity between workspace dashboards and AI-generated B-roll visual presentation assets.
* **Template Cleanout**: All `dark:` Tailwind CSS utility classes have been purged from all DOM templates (`admin-dashboard.php`, view sub-templates, and modal/drawer layouts).
* **Root Background**: The platform container enforces pure white (`#ffffff` / `bg-white`) for cards/tables and `zinc-50` (`#fafafa`) for canvas/page backgrounds.

---

### 1.2 Monochromatic Zinc Color Palette System (Notion / Shopify Standard)
Cora follows a strict Notion/Shopify-inspired monochromatic aesthetic, built upon Tailwind CSS's 11-step `zinc` neutral color ramp.

#### Neutral Color Scale Matrix
| Token Class | Hex Value | Primary Usage |
| :--- | :--- | :--- |
| `bg-white` | `#ffffff` | Primary container background, cards, table rows, drawer sheets, toast backgrounds |
| `bg-zinc-50` | `#fafafa` | Canvas background, sub-panel callouts, table header rows, sidebar popovers |
| `bg-zinc-100` / `border-zinc-100` | `#f4f4f5` | Subtle dividers, secondary element hover states, card inner borders |
| `border-zinc-200` | `#e4e4e7` | Standard input borders, default structural card/drawer borders |
| `text-zinc-400` | `#a1a1aa` | Muted icons, placeholder text, disabled UI controls |
| `text-zinc-500` | `#71717a` | Secondary text labels, timestamps, metadata captions |
| `text-zinc-700` | `#3f3f46` | Form input text, table body text, secondary action buttons |
| `text-zinc-800` | `#27272a` | Primary body text, card headers, navigation titles |
| `text-zinc-900` / `bg-zinc-900` | `#18181b` | Primary dark headings, active navigation tabs, badge backgrounds |
| `bg-zinc-950` | `#09090b` | Primary high-contrast CTA buttons, active status pills |

#### Purpose-Bound Accent & State Indicators
To preserve the monochromatic design language, colorful accents are strictly prohibited for decorative purposes and are reserved exclusively for functional state indicators:

```
[ Active / Success ]  --->  Emerald/Green  (bg-emerald-500, text-green-500)
[ Pending / Warning ]  --->  Amber/Yellow   (bg-amber-500, text-amber-500)
[ Critical / Error ]   --->  Red           (bg-red-500, text-red-500)
[ Neutral / Info ]     --->  Blue/Zinc     (bg-blue-500, text-zinc-500)
```

#### Typography Stack & Iconography Rules
* **UI Sans-Serif**: `Inter`, `-apple-system`, `BlinkMacSystemFont`, `sans-serif` for all UI text, headings, buttons, and form labels.
* **Display Headings**: `Outfit`, `sans-serif` for large page titles, hero headings, and documentation headers.
* **Monospace Stack**: `JetBrains Mono`, `ui-monospace`, `monospace` for version tags, code blocks, numeric IDs, financial values, and system metrics.
* **Vector Iconography**: All icons must use thin-lined vector SVGs (`stroke-width: 1.8` or `2.2`). Native emojis, outdated web-font icons, or browser glyphs are strictly forbidden.

---

### 1.3 Custom Monochromatic Toast Feedback System
Cora enforces a **Zero Browser Defaults Policy**: standard browser popups (`alert()`, `confirm()`, `prompt()`) are completely prohibited. All user feedback, success confirmations, and error alerts are handled programmatically via `window.coraShowToast(message, type)`.

```javascript
// Function Signature
window.coraShowToast(message, type = 'info');
// Supported types: 'info' | 'success' | 'warning' | 'error'
```

#### Implementation Architecture
1. **DOM Container**: Automatically injected into the DOM on first call at `#cora-toast-container` with fixed positioning.
2. **Responsive Positioning**: Floats from **top-center** (`top: 68px`) on mobile viewports to prevent collision with bottom sheets and navigation island, and anchors **bottom-right** (or top-right alert badges) on desktop.
3. **Duplicate Deduplication & Scale Bounce**: If a toast with identical message text is dispatched while already visible, prevents duplicate stacking and triggers a subtle 120ms scale bounce animation.
4. **Lifecycle & Animation**: Slide and fade in at 50ms, auto-dismiss at 3000ms with slide-out.

---

### 1.4 Mobile Sheet & Drawer Standard Operating Procedure (SOP)
To preserve screen layout context and guarantee ergonomic mobile usability:
* **Strict Prohibition of Mobile Side Drawers**: Side-sliding panels, side drawers, and off-canvas flyouts are strictly forbidden on mobile viewports (`< 768px`).
* **Bottom-Up Slide Sheets for Actions & Forms**: All mobile form panels, multi-step creators, filter trays, settings sheets, and sub-navigation menus open as **bottom slide-up sheets** (`translate-y-full` to `translate-y-0`) featuring:
  - Drag indicator handle (`w-10 h-1 rounded-full bg-zinc-300`).
  - Top rounded corners (`rounded-t-3xl`).
  - Dark blurred backdrop overlay (`rgba(9,9,11,0.45)` with `backdrop-filter: blur(8px)`).
  - Spring-like entrance easing (`cubic-bezier(0.16, 1, 0.3, 1)`).
* **Desktop Sliding Drawers**: On desktop (`>= 768px`), workflow sheets cleanly transition to right-sliding side drawer panels.

#### Drawer & Bottom-Sheet Roster Across Platform
* **Shoot Booking & Showing Sheet**: Add shoot schedules and site visits.
* **Lead CRM Sheet**: Tabbed panel (`General`, `Assets`, `Equipment`) for lead profiling.
* **Form Submissions & Embed Sheet**: Form responses viewer and embed script generator.
* **Property Listing / Equipment Sheet**: Inventory tracking and asset assignment.
* **Document Share & E-Sign Sheet**: Document Vault link generator and access token management.
* **Workspace Switcher & Creator Sheet**: Super Admin workspace provisioner.
* **HTML AI Insights Sheet**: Core Web Vitals and element rewriting panel in Canvas.

---

### 1.5 Mobile Touch Snappiness & Click Interception Shield (v4.9.24 - v4.9.27)
* **Zero 300ms Tap Delay**: Enforced `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` across all interactive elements (buttons, inputs, island nav, drawer sheets) to eliminate mobile tap latency.
* **Platform Click Interception Shield**: Enforced strict pointer-events isolation across all drawer containers, modals, and backdrops (`pointer-events: none` when closed; `pointer-events: auto` only when open).
* **Skeleton Dismissal Engine**: Guarantees that loading skeletons are cleanly dismissed upon DOM hydration, preventing ghost overlay blocking.
* **Purged Global `!important` Overrides**: Eliminated un-namespaced `.hidden { display: none !important; }` rules in favor of scoped lifecycle classes, allowing Tailwind's responsive grid classes (`hidden md:flex`) to cascade without interference.

---

### 1.6 PWA Architecture & Mobile Performance Engine (v4.9.32)

#### Pure Light Mode Native Splash & Theme Color
* **Instant Light Splash Screen**: Both `/cora-manifest.json` and `<meta name="theme-color">` enforce `#ffffff` and `apple-mobile-web-app-status-bar-style: default` to eliminate dark-to-light flash delays and render the native OS mobile splash instantaneously.
* **Zero Artificial Delays**: Page load and route transitions execute with zero artificial `setTimeout` preloader holds. Server-rendered HTML hydrates immediately on `DOMContentLoaded`.

#### Dynamic Manifest & Version-Stamped Icon Synchronization
* **Automatic App Icon Refresh**: `/cora-manifest.json` dynamically injects full absolute URLs with version stamps (`?v=CORA_WORKSPACE_VERSION`) across all icon definitions (`192x192`, `512x512`, `any`, `maskable`, and shortcut icons) to trigger automatic OS and browser WebAPK icon updates on release.
* **Modern PWA Launch Handlers**: Configured with `display_override: ["standalone", "window-controls-overlay"]`, `launch_handler: { "client_mode": "navigate-existing" }`, `capture_links: "existing-client-navigate"`, and `handle_links: "preferred"`.

#### Standalone PWA In-App Navigation Engine (Breakout Prevention)
* **iOS Safari & Android PWA Link Retention**: Standalone mode (`navigator.standalone === true` or `(display-mode: standalone)`) captures all internal anchor clicks (`/workspace/**`, `/docs/**`, `?page=cora-workspace`) and retains execution inside the installed PWA window using `window.location.assign()`, preventing external browser tab popouts.

#### Universal In-App Update & Asset Sync Engine
* **Multi-Device Lifecycle Prompting**: All platforms (iOS Safari WebClip, Android Chrome WebAPK, Desktop PWA, and browser tabs) support the universal monochromatic in-app update prompt system (`#cora-pwa-update-banner`, `#cora-pwa-update-pill`, and `#cora-pwa-update-drawer`).
* **Zero-Downtime Cache Invalidation**: On applying an update (`window.coraApplyPwaUpdate`), the client purges all version-mismatched caches, activates the new Service Worker via `skipWaiting`, synchronizes dynamic touch icons/favicons, and smoothly reloads the active screen within 300ms.
* **REST Version Heartbeat**: The system provides `/wp-json/cora-pwa/v1/version-check` returning active version metadata, release notes, and manifest URLs.
* **Device-Aware Guidance**: The update drawer dynamically detects the client OS:
  * **Apple iOS**: Explains springboard icon refresh via Share ⎋ → Add to Home Screen.
  * **Android**: Confirms background automatic WebAPK icon synchronization.
  * **Desktop Browser / PWA**: 1-click instantaneous cache purge and refresh.

#### Service Worker Lifecycle & High-Speed Cache Strategy (v4.9.32)
| Request Type | Strategy | Latency Target | Description |
| :--- | :--- | :--- | :--- |
| **HTML Navigation** | `Network-First with Offline Fallback` | `Real-time / Instant` | Always fetches the exact live requested URL from the network; cleanly falls back to offline cache only if disconnected (<400ms timeout). |
| **Core JS & CSS** | `Stale-While-Revalidate` | `Instant (<10ms)` | Instant delivery from dynamic cache with background asset freshness updates. |
| **Fonts (Inter / Mono)** | `Cache-First` | `Instant (<5ms)` | Cached permanently in dedicated font cache. |
| **API & AJAX** | `Network-Only` | `Real-time` | Bypasses service worker for real-time live database synchronization. |
| **Auto Cache Eviction** | `On Activate` | `Immediate` | Purges all prior version cache partitions when `CORA_WORKSPACE_VERSION` increments. |

---

## Section 2: Core SaaS Business Modules

### 2.1 Content AI Suite & Myra Assistant
The **Content AI Suite** is an enterprise-grade content lifecycle and SEO optimization engine. At the core is **Myra** — a floating, state-aware AI Content Manager.

#### Myra AI Assistant
* **Floating Launcher & Copilot Sheet**: Bottom-center position with online badge. Collapsible panel design.
* **Workspace State Awareness**: Evaluates active subtab, editor context (document ID/title/keyword/word count), library state, and opportunity pipeline.
* **Provider & Model Switching**: Google Gemini 3.5 Flash, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o with live token tracking.
* **Action Tag Execution**: `[ACTION:set_title]`, `[ACTION:set_keyword]`, `[ACTION:insert_text]`, `[ACTION:save_article]`, `[ACTION:create_article]`, `[ACTION:scan_opportunities]`.

### 2.2 Content Editor (Quill WYSIWYG)
* **Sticky Docked Toolbar**: Remains visible while scrolling through long-form content.
* **Slash Command Hint**: Placeholder "Type / for commands..." for quick formatting access.
* **Document Outline & Metrics**: Real-time word count, character count, paragraph count, reading time.
* **Mobile Quick Action Bar**: Compact floating bar with Bold, Italic, Link, Heading, List.
* **Landscape Auto-Rotate Lock**: Enforces vertical scroll in editor.

### 2.3 The 7 Content Suite Dashboards
| Subtab | ID | Key Capabilities |
|---|---|---|
| **Overview** | `ct-overview` | KPI cards, timeframe selectors, quick launchers |
| **Opportunities** | `ct-opportunities` | Funnel charts, topic clusters, keyword intent |
| **Calendar** | `ct-calendar` | Monthly/Weekly/Kanban editorial planner |
| **Content Library** | `ct-library` | Notion-styled data table, pagination, inline editors |
| **SEO Visibility** | `ct-seo` | GEO tracking, 7 audit tabs, backlink badges |
| **Performance** | `ct-performance` | GSC API integration, CTR graphs |
| **Automations** | `ct-automations` | IndexNow, GSC submission, sitemap refresh |

### 2.4 Lead Management Suite (CRM)
* **Kanban Pipeline**: Drag-and-drop across *New*, *Contacted*, *Qualified*, *Proposal Sent*, *Won*, *Lost*.
* **Lead Detail Sheet**: Metadata, activity timeline, direct outreach, client conversion.
* **Dynamic Industry Terminology**: Automatically switches between *Client Leads* (Studio/Marketing) and *Buyer Leads* (Real Estate).

### 2.5 Media Library & Advanced Editor
* **MIME Filters**, **Dropzone Uploader**, **Storage Quota Meter**
* **Crop Presets**: 1:1, 4:3, 16:9, Free Crop with rotation and flipping.
* **Left Sidebar Controls**: Segment tabs, media card presets, locate and delete mapping.
* **SEO Metadata Manager**: Alt text, caption, description fields.

### 2.6 Email Management Suite
* **Outbox & Compose**: Recipient auto-complete, personalization variables, live HTML preview.
* **Hostinger SMTP Integration**: Port 587/465, connection diagnostics.
* **Sequences & Drip Workflows**: Automated scheduling linked to CRM pipeline stages.

### 2.7 Document Vault & Document Studio
* **5-Step Wizard**: Document type, line items with SAC codes, GST math, e-sign audit.
* **GST Engine**: Auto CGST/SGST (intra-state 9% + 9%) or IGST (inter-state 18%) calculation.
* **Legal E-Sign Audit Registry**: SHA-256 fingerprinting, IP address capture, timestamp certification.

### 2.8 Forms & Reviews 2.0 (Overhauled in v4.9.3 - v4.9.23)
The Forms & Reviews module provides a unified customer intake, contract signing, and review collection engine:
* **26 Hardened Form Widgets**: Full audit and hardening across all field types (Text, Long Text, Phone, Email, NPS Rating, Star Rating, SAC Code, Signature Pad, File Dropzone, Date Picker, Multi-Select, etc.).
* **AI Conversion Doctor / Funnel Analytics**: Replaced static charts with an actionable diagnostic engine identifying high drop-off questions, calculating a Form Health Score (0-100), and providing 1-click recommendations.
* **Global & Per-Form Settings Suite**:
  - **Meta WhatsApp Cloud API**: Automated submission confirmations and review follow-ups with Hinglish presets.
  - **SMTP Email Notifications**: Monochromatic transactional submission confirmations.
  - **Webhook Integrations**: Real-time JSON payload dispatch to CRM, Zapier, or custom webhooks.
* **Automation Flows Tab**: Visual trigger-action sequencing (e.g. On Submission → Send WhatsApp → Issue E-Sign Vault Contract).
* **Embed Engine**: Responsive bottom slide-up modal generating clean iframe embed codes and standalone runtime scripts.

### 2.9 Crew Scheduler & Equipment Management
* **Crew Scheduler**: Timeline-based crew assignment for studio shoots, production sets, and site showings.
* **Equipment Manager**: Asset check-in/check-out lifecycle, barcode/QR custody logs, condition audits.
* **Client Task Manager**: Shared collaborative milestones with file attachments.

### 2.10 Financial Module & Event Timeline
* **Financials**: Revenue tracking, payment status monitoring, cash flow runway.
* **Event Timeline**: Chronological activity feed across all platform operations.

### 2.11 App Modules & Feature Hub (v4.9.28 - v4.9.30)
The Feature Hub (`view-feature-hub.php`) allows workspace owners to configure their active platform footprint:
* **20+ Modular Features**: Grouped into *Workspace & Core*, *Operations*, *Sales Channel*, and *AI Marketing & Tools*.
* **Explicit Save Workflow**: Modifications trigger a sticky bottom unsaved changes banner. Changes are staged in memory and committed atomically via AJAX to `cora_agency_modules_{agency_id}`.
* **Batch Controls**: 1-click "Select All", "Deselect All", and "Reset to Industry Defaults".
* **Scoped CSS Isolation**: Completely namespaced (`.cora-fh-*`) to eliminate side effects on neighboring views.

---

## Section 3: Canvas Theme Builder & Dual-Engine Architecture (v4.9.31 - v4.9.32)

Canvas has evolved into a **Dual Builder Engine**, supporting both Elementor white-labeled editing and a modern Visual HTML Canvas (Lovable-compatible).

```
+-----------------------------------------------------------------------------------+
|                            CORA CANVAS THEME BUILDER                              |
+-----------------------------------------+-----------------------------------------+
|        Engine A: Elementor White-Label  |      Engine B: Visual HTML Canvas       |
|  • Overrides native Elementor UI        |  • Live sandboxed iframe preview        |
|  • Injected 2-row custom toolbar        |  • Direct inline text contenteditable   |
|  • Strips WP branding and upsells       |  • Asset scanner & 1-click media swap   |
|  • Git commit & push workflow           |  • AI Element Rewriter & SEO Optimizer  |
+-----------------------------------------+-----------------------------------------+
```

### 3.1 Dual-Engine Theme Architecture
1. **Elementor Engine**: Wraps Elementor in a sandboxed, white-labeled two-row toolbar (`cora-elementor-reskin.js` and `.css`), completely hiding native headers, admin bars, upsells, and promo banners.
2. **Visual HTML Canvas Engine**: Directly renders clean, semantic HTML inside `#cora-html-canvas-iframe`, providing instant client-side editing without heavy builder overhead.

### 3.2 In-Browser Visual HTML Editor
* **Inline `contenteditable` Editing**: Click any heading, paragraph, or label inside the iframe to edit text in-place with real-time focus outline highlights (`.cora-editing-active`).
* **Media Asset Inventory Scanner (`renderHtmlInventoryList`)**: Automatically inspects the loaded page, extracts all `<img>` tags, and displays a thumbnail gallery in the editor sidebar.
* **1-Click Image Replacement Modal**: Selecting "Swap" on any detected media asset triggers `#cora-image-replacer-popover`, enabling instant image swapping from the Cora Media Library or external URLs.
* **Clean HTML Serialization Engine (`getCleanIframeHtml`)**: Before saving, clones the DOM and strips temporary editor attributes (`contenteditable`, `.cora-editing-active`), returning pure HTML5 code.
* **Atomic Save & Publish (`cora_ajax_save_html_visual`)**: Commits clean HTML directly to the database and syncs mapped WordPress post content.

### 3.3 AI-Powered Canvas Tools
* **AI Element Rewriting (`cora_ajax_canvas_ai_rewrite_element`)**: Context-aware re-drafting of headlines, body paragraphs, and CTAs across various tones (*Punchy*, *Professional*, *High-Converting*, *Minimal*).
* **AI Core Web Vitals & SEO Optimizer (`cora_ajax_canvas_ai_optimize_page`)**: Automated page audit diagnosing LCP, CLS, and FID metrics, injecting optimized image tags, meta tags, and structured data.

### 3.4 Add Theme Wizard
Features dual-choice selection cards (*Elementor Builder* vs. *Lovable Visual HTML Builder*) with dedicated reset handlers (`window.wizardResetCards`) and automated page scaffolding.

---

## Section 4: Public Developer Documentation Portal (`/docs`)

### 4.1 Three-Column Notion-Like Layout
The `/docs` endpoint renders a premium three-column documentation portal:
* `view-public-docs.php`: Master layout container.
* `view-public-docs-header.php`: Sticky header with branding, search, actions.
* `view-public-docs-sidebar.php`: Left navigation with collapsible categories.
* `view-public-docs-content.php`: Main prose content with feature cards.
* `view-public-docs-widgets.php`: Right AI Playground panel.
* `view-public-docs-search.php`: Command palette search overlay and AJAX router.

### 4.2 AI Playground Sidebar
RAG-powered interactive assistant answering technical queries, suggesting quick prompts, and offering real-time streaming answers.

### 4.3 Command Palette Search
`Cmd+K` / `Ctrl+K` modal overlay with keyboard navigation and AJAX page routing via `history.pushState`.

---

## Section 5: UI Shell & Standardized Page Layouts

### 5.1 Standardized Header Action Bar
All active workspace subviews adhere to a unified page header design framework:
* **Branding & Visuals**: Bold typography using Outfit display headings and clear descriptive subtitles.
* **Integrated AI Platform Shortcuts Stack**: Overlapping brand icon stack (ChatGPT, Gemini, Claude, Perplexity, YouTube) for 1-click workspace redirection.
* **On-Demand Tutorial Walkthroughs**: Dedicated tutorial trigger opening guides in sliding sheets.

### 5.2 Responsive Telemetry Metrics Display
* **Mobile (2x2 Grid)**: Key performance indicators render as a compact, balanced 2x2 grid filling available viewport width.
* **Desktop (1x4 Centered Row)**: Telemetry metrics align horizontally with snug 6-8px gaps and 120px minimum card widths.

### 5.3 Mobile Adaptive Floating Island Navigation
On mobile devices (`< 768px`), all bottom controls are consolidated into a single floating island navigation bar:
* Integrated AI voice copilot launcher.
* View switcher and quick action triggers.
* Eliminates visual clutter and respects device safe areas (`env(safe-area-inset-bottom)`).

---

## Section 6: Multi-Tenant Database Architecture

### 6.1 Core Custom Tables
| Table | Purpose |
| :--- | :--- |
| `wp_cora_agencies` | Root tenant isolation |
| `wp_cora_branches` | Sub-office segmentation |
| `wp_cora_leads` | Lead CRM pipeline |
| `wp_cora_clients` | Converted client accounts |
| `wp_cora_bookings` | Showings and shoot bookings |
| `wp_cora_ledger` | Financial transaction log |
| `wp_cora_canvas_themes` | Theme builder themes |
| `wp_cora_canvas_pages` | Theme builder pages |
| `wp_cora_documents` | Document vault records |
| `wp_cora_notifications` | In-app notification queue |

### 6.2 Agency Isolation Pattern
All SQL queries strictly filter by `agency_id = %d`. Multi-branch views filter by `branch_id`. Tenant data never leaks across workspace boundaries.

---

## Section 7: AI Integration, Situational RAG & MCP Gateway

* **Multi-Provider AI Routing**: Google Gemini 3.5 Flash, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o with automatic fallback.
* **Situational Awareness RAG**: Injects active view, tenant context, open documents, and selected leads into prompts.
* **MCP Gateway (`views/view-mcp.php`)**: JSON-RPC over WebSockets with role-based permissions, connecting local agent tools to the workspace.

---

## Section 8: Testing & Quality Assurance

* **Playwright E2E**: Tiered test suites (Tier 1-4) covering auth, CRUD, integration, and workload flows.
* **Build Validation**: `scripts/build.sh` verifies version consistency across plugin header, constants, and release manifests.

---

## Section 9: Notification Management System & Event Trigger Engine

* **In-App Bell**: Real-time counter badge and slide-out notification drawer.
* **Web Push (VAPID ES256)**: Native browser and lock-screen alerts.
* **Monochromatic HTML Email**: Responsive Notion/Claude-style transactional emails via `wp_mail()`.
* **Quiet Hours / DND**: Configurable quiet hours with automatic queuing and morning briefing dispatch.

---

## Section 10: Financial AI Co-founder & Financial Intelligence System

* **Multi-Tenant Workspace Isolation**: `agency_id = %d` on all ledger records; clean `₹0` empty states for new tenants.
* **Dynamic Chart.js Bridge**: Feeds live numbers directly to client-side line and doughnut charts.
* **4 Financial Pillars**: Available Cash, Expected In (30 Days), Expected Out (30 Days), Projected Buffer & Runway.
* **GST Engine**: 3-step invoice drawer with auto Place of Supply tax detection (CGST+SGST vs. IGST).
* **Deal Feasibility Simulator**: Evaluates net margins before committing to client projects.

---

## Section 11: Multi-Industry Engine & WP Lockdown (v4.9.0)

Cora supports 3 distinct agency archetypes with full vertical adaptation:
1. **Photography Studio (`photography_studio`)**: Pre-configures Shoot Bookings, Crew Scheduler, Camera Gear Tracker, and Photo Proofing.
2. **Real Estate Brokerage (`real_estate`)**: Pre-configures Property Showings, Listing Catalog, and Buyer Lead Pipeline.
3. **Marketing Agency (`marketing_agency`)**: Pre-configures Client Retainers, Campaign Funnels, Ad Spend Tracking, and Brand Content AI.

### WordPress Backend Lockdown & Virtual URL Masking
* **WP-Admin Lockdown**: Agency users attempting to access `/wp-admin/` are automatically redirected to `/workspace/dashboard`.
* **Virtual URLs**: Clean endpoints for `/workspace/dashboard`, `/workspace/login`, `/workspace/register`, `/workspace/onboarding`.
* **Admin Bar Suppression**: Hides native WordPress toolbar completely for a true white-label SaaS experience.

---

## Section 12: Continuous Hands-Free AI Voice Discussion Engine (v4.9.0 - v4.9.13)

The Voice AI Discussion Engine (`window.coraVoiceEngine`) provides real-time, hands-free conversational assistance:
* **Continuous Hands-Free Conversation**: Features auto-endpointing (silence detection) and dynamic speech synthesis (TTS).
* **4 Curated Voice Personalities**:
  1. *Myra*: Studio Co-founder & Creative Lead
  2. *Aarav*: Senior Growth & Marketing Strategist
  3. *Vikram*: Executive Operations & Real Estate Broker
  4. *Kavya*: Client Success & Communication Director
* **Multi-Lingual Synchronization**: Auto-detects and transliterates across 9 regional dialects (Indian English, Hindi, Bengali, Tamil, Telugu, Marathi, Gujarati, Kannada, US English).
* **Situational Awareness**: Contextually understands active leads, calendar events, invoices, and active views.
* **Discussion UI**: Live scrollable conversation feed, interactive soundwave visualizer (`.cora-voice-bar`), mic mute/unmute, and "Insert Text" fallback.

---

## Section 13: High-Performance Architecture & Micro-Cache Layer (v4.9.20 - v4.9.26)

* **Sub-Millisecond Micro-Cache Layer**: `cora_cache_get()` and `cora_cache_set()` provide static runtime caching combined with `wp_cache_*` for sub-millisecond query resolution.
* **SSR Pre-rendering & Batch Queries**: Pre-renders dashboard views on the server to achieve instant client hydration.
* **Self-Healing Database Error Drop-in**: Intercepts transient database connection errors and gracefully retries or renders clean fallback states.
* **Debounce Locks & Event Isolation**: Prevents race conditions and duplicate executions in multi-tab sessions.

---

*Cora Platform v4.9.32 — Last updated: September 2026.*
