# Cora Platform — Comprehensive Platform Documentation

This document serves as the master technical specification and architectural manual for the Cora Workspace Platform (v4.9.59).

---

## Section 1: Core Theme System, PWA & Mobile Performance SOP

### 1.1 Pure Light Mode Enforcement & Dark Mode Removal
Starting in **version 3.2.83** and hardened through **v4.9.59**, dark mode support has been completely removed across all Cora platform plugins, workspace views, design tokens, and components. The platform strictly enforces a **pure light mode visual standard** platform-wide.

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
* **Multimodal OCR Ingestion Sheet**: Team registration and roster ingestion wizard.
* **Property Listing / Equipment Sheet**: Inventory tracking and asset assignment.
* **Document Share & E-Sign Sheet**: Document Vault link generator and access token management.
* **Workspace Switcher & Creator Sheet**: Super Admin workspace provisioner.
* **Universal Website Migration Sheet**: Scraper parameters, domain crawl depth, page limit controls.
* **Super Admin Drawer Suite**: AI token allocation, feature flag matrix, and emergency command triggers.

---

### 1.5 Mobile Touch Snappiness & Click Interception Shield
* **Zero 300ms Tap Delay**: Enforced `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` across all interactive elements (buttons, inputs, island nav, drawer sheets) to eliminate mobile tap latency.
* **Platform Click Interception Shield**: Enforced strict pointer-events isolation across all drawer containers, modals, and backdrops (`pointer-events: none` when closed; `pointer-events: auto` only when open).
* **Skeleton Dismissal Engine**: Guarantees that loading skeletons are cleanly dismissed upon DOM hydration, preventing ghost overlay blocking.
* **Purged Global `!important` Overrides**: Eliminated un-namespaced `.hidden { display: none !important; }` rules in favor of scoped lifecycle classes, allowing Tailwind's responsive grid classes (`hidden md:flex`) to cascade without interference.
* **Global Scroll & Padding Buffer (v4.9.59)**: Enforced `.cora-scroll-spacer` and responsive bottom padding (`pb-28 md:pb-12`) across all views to eliminate bottom-edge clipping and scroll lock behind mobile navigation bars.

---

### 1.6 PWA Architecture & Mobile Performance Engine (v4.9.59)

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

#### Service Worker Lifecycle & High-Speed Cache Strategy
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
* **Numeric Phone Constraint**: Strict validation (`/^[0-9+ -]{7,15}$/`) restricting phone inputs to numeric digits and standard international dialing symbols.
* **Dynamic Industry Terminology**: Automatically switches between *Client Leads* (Studio/Marketing) and *Buyer Leads* (Real Estate).

### 2.5 Media Library & Advanced Editor
* **MIME Filters**, **Dropzone Uploader**, **Storage Quota Meter**.
* **Synchronized Header**: Real-time breadcrumb file count calculation matching active folder contents.
* **Crop Presets**: 1:1, 4:3, 16:9, Free Crop with rotation and flipping.
* **Left Sidebar Controls**: Segment tabs, media card presets, locate and delete mapping.
* **SEO Metadata Manager**: Alt text, caption, description fields.

### 2.6 Email Management Suite (Hostinger Relay)
* **Outbox & Compose**: Recipient auto-complete, personalization variables, live HTML preview.
* **Enforced Default Active Hostinger SMTP Relay**: Built-in default SMTP relay configuration for transactional notifications, e-sign link delivery, and lead dispatch with zero manual setup required.
* **Sequences & Drip Workflows**: Automated scheduling linked to CRM pipeline stages.

### 2.7 Document Vault & Document Studio
* **5-Step Wizard**: Document type, line items with SAC codes, GST math, e-sign audit.
* **GST Engine**: Auto CGST/SGST (intra-state 9% + 9%) or IGST (inter-state 18%) calculation.
* **Legal E-Sign Audit Registry**: SHA-256 fingerprinting, IP address capture, timestamp certification.

### 2.8 Forms & Reviews 2.0
* **26 Hardened Form Widgets**: Full audit and hardening across all field types (Text, Long Text, Numeric Phone, Email, NPS Rating, Star Rating, SAC Code, Signature Pad, File Dropzone, Date Picker, Multi-Select, etc.).
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

### 2.11 App Modules & Feature Hub
* **20+ Modular Features**: Grouped into *Workspace & Core*, *Operations*, *Sales Channel*, and *AI Marketing & Tools*.
* **Explicit Save Workflow**: Modifications trigger a sticky bottom unsaved changes banner. Changes are staged in memory and committed atomically via AJAX to `cora_agency_modules_{agency_id}`.
* **Batch Controls**: 1-click "Select All", "Deselect All", and "Reset to Industry Defaults".
* **Scoped CSS Isolation**: Completely namespaced (`.cora-fh-*`) to eliminate side effects on neighboring views.

### 2.12 Multimodal Team Migration & OCR Ingestion Hub
Located in `view-users.php`, the Multimodal Team Migration Hub accelerates team onboarding:
* **Multimodal OCR Roster Ingestion**: Agencies can upload photos, scans, or PDFs of existing employee rosters, attendance registers, or spreadsheets.
* **Vision Model Intelligence**: Utilizes Gemini 1.5 Pro / GPT-4o Vision to parse names, contact phone numbers, emails, designated agency roles, and commission percentages.
* **Interactive Staging & Batch Provisioning**: Displays an editable preview table allowing managers to verify extracted records before executing 1-click batch user account creation.
* **24h Memory Rotation**: Scanned files and staging payloads expire and clean up automatically after 24 hours.
* **Strict Single Workspace Owner Policy (v4.9.58)**: Enforces that each agency has exactly one designated Workspace Owner. The Workspace Owner role is removed from general role assignment dropdowns to prevent accidental permission escalation or multi-owner conflicts.

### 2.13 Field Ops & Real-Time Geolocation Tracking Engine (v4.9.58)
Engineered for mobile dispatch, site visits, shoot crews, and property inspections:
* **Real-Time GPS Tracking**: Captures live location breadcrumbs (`latitude`, `longitude`, `speed`, `accuracy`, `timestamp`) via browser and PWA Geolocation API.
* **Stop & Rest Detection Algorithm**: Automatically clusters sequential coordinates to detect stationary stops (> 5 minutes dwell time), calculating arrival times, departure times, and rest durations.
* **Velocity & Moving Time Math**: Computes true average velocity (excluding stationary dwell periods) and tracks total moving time vs. idle time.
* **High-Definition Multi-Layer Map Engine**:
  - **Streets**: Esri World Street Map (Crisp, high-contrast vector cartography).
  - **Satellite HD**: Esri World Imagery (High-resolution satellite view with street overlays).
  - **OpenStreetMap**: Standard open-source street tiles with monochromatic filtering.
  - **Dark / Monochrome**: CartoDB Dark Matter for low-light tracking.
  - **100% Free**: Zero map tile watermarks, zero map provider licensing fees, and zero external API key requirements.
* **Interactive Route Replay Engine**: Animate historical field agent journeys with scrubber controls, speed multipliers (1x, 2x, 5x), interpolated vehicle/marker positions, and 1-click stop inspection cards.
* **Mobile-First Touch Pan Mode**: Features a dedicated `Touch Pan` toggle button to prevent accidental scroll trapping while navigating maps on mobile touchscreens.

---

## Section 3: Canvas Theme Builder, Dual-Engine Architecture & Universal Website Migrator (v4.9.38 - v4.9.59)

Canvas is a **Dual Builder Engine & Universal Website Migration Platform**, supporting Elementor white-labeled editing, modern Visual HTML Canvas, and 1-click site ingestion:

```
+-----------------------------------------------------------------------------------+
|                            CORA CANVAS THEME BUILDER                              |
+----------------------+-----------------------------+------------------------------+
| Engine A: Elementor  | Engine B: Visual HTML Canvas| Engine C: Universal Migrator |
| • Reskinned Sandbox  | • Live Sandboxed Iframe     | • Multi-Page URL Crawler     |
| • Strips WP Branding | • Inline contenteditable    | • CSS/JS/Asset Localizer     |
| • 2-Row Top Toolbar  | • Code-Split Visual Editor  | • DOM Sanitizer & Cleaner    |
| • Git Commit / Sync  | • Media Swap & Scanner      | • Auto Draft Theme Generator |
+----------------------+-----------------------------+------------------------------+
```

### 3.1 Dual-Engine Theme Architecture
1. **Elementor Engine**: Wraps Elementor in a sandboxed, white-labeled two-row toolbar (`cora-elementor-reskin.js` and `.css`), completely hiding native headers, admin bars, upsells, and promo banners.
2. **Visual HTML Canvas Engine**: Directly renders clean, semantic HTML inside `#cora-html-canvas-iframe`, providing instant client-side editing without heavy builder overhead.

### 3.2 In-Browser Visual HTML Editor & Code Split Engine
* **Inline `contenteditable` Editing**: Click any heading, paragraph, or label inside the iframe to edit text in-place with real-time focus outline highlights (`.cora-editing-active`).
* **Code-Split Visual Editor**: Split view displaying live visual canvas alongside source HTML code editor with instant bidirectional synchronization.
* **URL Edit State Persistence**: Automatically maintains active edit state in browser URL parameters (`?page_id={id}&edit_mode=visual`), enabling lossless browser reloads and deep linking.
* **Media Asset Inventory Scanner (`renderHtmlInventoryList`)**: Automatically inspects the loaded page, extracts all `<img>` tags, and displays a thumbnail gallery in the editor sidebar.
* **1-Click Image Replacement Modal**: Selecting "Swap" on any detected media asset triggers `#cora-image-replacer-popover`, enabling instant image swapping from the Cora Media Library or external URLs.
* **Clean HTML Serialization Engine (`getCleanIframeHtml`)**: Before saving, clones the DOM and strips temporary editor attributes (`contenteditable`, `.cora-editing-active`), returning pure HTML5 code.
* **Atomic Save & Publish (`cora_ajax_save_html_visual`)**: Commits clean HTML directly to the database and syncs mapped WordPress post content.

### 3.3 Universal Website (HTML/CSS/JS) Multi-Page Migrator Engine (v4.9.58 - v4.9.59)
The Universal Website Migrator (`class-cora-html-website-migrator.php`) allows agencies to ingest, scrape, and migrate any existing public website into Cora Canvas with a single click:
* **Multi-Page Domain Crawler**: Recursively discovers internal links matching the root domain up to configurable crawl depths (1-5 levels) and page limits (up to 25 pages).
* **Asset Isolation & Downloader**: Parses all external and relative CSS stylesheets, JavaScript files, fonts, and images, downloading them to localized workspace media folders and rewriting HTML paths.
* **HTML Sanitization & Sandboxing**: Strips third-party analytics trackers, malicious scripts, and external ads while preserving core layout styles, CSS variables, and interactive JavaScript.
* **Draft Theme & Page Generation**: Automatically provisions a new draft theme in `wp_cora_canvas_themes` and scaffolds all crawled pages inside `wp_cora_canvas_pages`.
* **Instant Visual Canvas Editing**: Migrated pages immediately open in the In-Browser Visual HTML Editor for live inline text modifications, image replacements, and AI copy adjustments.
* **Streamlined Monochromatic Migration Modal**: Provides URL input, scan depth controls, page limit sliders, live crawling progress indicators, and atomic error handling.

### 3.4 AI-Powered Canvas Tools
* **AI Element Rewriting (`cora_ajax_canvas_ai_rewrite_element`)**: Context-aware re-drafting of headlines, body paragraphs, and CTAs across various tones (*Punchy*, *Professional*, *High-Converting*, *Minimal*).
* **AI Core Web Vitals & SEO Optimizer (`cora_ajax_canvas_ai_optimize_page`)**: Automated page audit diagnosing LCP, CLS, and FID metrics, injecting optimized image tags, meta tags, and structured data.

### 3.5 Add Theme Wizard
Features triple-choice selection cards (*Elementor Builder*, *Lovable Visual HTML Builder*, *Universal Website Migrator*) with dedicated reset handlers (`window.wizardResetCards`) and automated page scaffolding.

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

### 5.1 Standardized Header Action Bar & Semantic Navigation
* **Clean Semantic URLs**: Replaced all legacy `javascript:void(0)` links with clean RESTful paths (`/workspace/{subview}`) enabling browser back/forward history and tab opening.
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
| `wp_cora_agencies` | Root tenant isolation and subscription state |
| `wp_cora_branches` | Sub-office segmentation |
| `wp_cora_leads` | Lead CRM pipeline |
| `wp_cora_clients` | Converted client accounts |
| `wp_cora_bookings` | Showings and shoot bookings |
| `wp_cora_ledger` | Financial transaction log and MRR accounting |
| `wp_cora_canvas_themes` | Theme builder themes (Elementor, Visual HTML, Migrated) |
| `wp_cora_canvas_pages` | Theme builder pages and clean HTML content |
| `wp_cora_documents` | Document vault records and e-sign metadata |
| `wp_cora_notifications` | In-app notification queue and PWA push queue |

### 6.2 Strict Agency Isolation & Tenant-Scoped Queries
All SQL queries and AI contextual retrievers strictly filter by `agency_id = %d`. Data from one tenant is cryptographically and logically isolated from all other workspaces.

---

## Section 7: Dynamic AI Co-Founder Panel & Bidirectional Continuous RAG

The Cora AI engine features an action-oriented Co-Founder architecture:

```
+-----------------------------------------------------------------------------------+
|                        DYNAMIC AI CO-FOUNDER ENGINE                               |
+-----------------------------------------+-----------------------------------------+
|        Unified Dual-Mode Panel          |      Bidirectional Continuous RAG       |
|  • Mode A: Interactive Text Chat Copilot|  • Tenant-Scoped Knowledge Memory       |
|  • Mode B: Live Continuous Voice Duplex |  • 24h Auto-Rotating Learning Loop      |
|  • Page-Aware Situational Action Cards  |  • Multi-Provider LLM Fallback Chain    |
|  • Scoped AI Quota Usage Meter          |  • Contextual Token Optimizer           |
+-----------------------------------------+-----------------------------------------+
```

### 7.1 Unified Dynamic AI Co-Founder Panel
* **Dual Text & Voice Modes**: Switch seamlessly between typed chat and hands-free voice discussion within a single responsive drawer.
* **Space-Efficient Two-Tier Header**: Clean SVG icons, top rounded corners (`rounded-t-3xl`), and active persona indicators (Myra, Aarav, Vikram, Kavya).
* **Page-Aware Context Cards**: Dynamically inspects the user's active subpage (e.g. Leads, Vault, Forms, Canvas) and surfaces contextual 1-click action recommendations.

### 7.2 Bidirectional Continuous Self-Learning RAG Loop
* **Knowledge Ingestion**: AI captures agency preferences, client feedback, and operational patterns back into tenant memory (`cora_agency_ai_memory`).
* **24h Memory Auto-Rotation**: Cleans up stale short-term session states while committing hardened operational guidelines to long-term memory.
* **Strict Tenant Scoping**: All RAG vector lookups and prompt contexts are strictly partitioned by `agency_id`.

### 7.3 Scoped AI Quota Hub
* **Real-Time Token Tracking**: Shows monthly AI allowance, tokens consumed, and remaining quota.
* **Tier-Based Quota Limits**: Seamless rate governance across Standard, Pro, and Enterprise tiers with automatic reset cycles.

---

## Section 8: Testing & Quality Assurance

* **Playwright E2E**: Tiered test suites (Tier 1-4) covering auth, CRUD, integration, and workload flows.
* **Build Validation**: `scripts/build.sh` verifies version consistency across plugin header, constants, and release manifests.

---

## Section 9: Notification Management System & Event Trigger Engine

* **In-App Bell**: Real-time counter badge and slide-out notification drawer.
* **Web Push (VAPID ES256)**: Native browser and lock-screen alerts.
* **Attendance PWA Push Reminders (v4.9.57)**: Morning (9:00 AM) and evening (6:00 PM) attendance reminders migrated from email to interactive PWA push notifications with 1-click check-in/out deep links.
* **Monochromatic HTML Email**: Responsive Notion/Claude-style transactional emails via `wp_mail()` with Hostinger default relay.
* **Quiet Hours / DND**: Configurable quiet hours with automatic queuing and morning briefing dispatch.

---

## Section 10: Financial AI Co-founder & Financial Intelligence System

* **Multi-Tenant Workspace Isolation**: `agency_id = %d` on all ledger records; clean `₹0` empty states for new tenants.
* **Dynamic Chart.js Bridge**: Feeds live numbers directly to client-side line and doughnut charts.
* **4 Financial Pillars**: Available Cash, Expected In (30 Days), Expected Out (30 Days), Projected Buffer & Runway.
* **GST Engine**: 3-step invoice drawer with auto Place of Supply tax detection (CGST+SGST vs. IGST).
* **Deal Feasibility Simulator**: Evaluates net margins before committing to client projects.

---

## Section 11: Multi-Industry Engine & WP Security Masking

Cora supports 3 distinct agency archetypes with full vertical adaptation:
1. **Photography Studio (`photography_studio`)**: Shoot Bookings, Crew Scheduler, Camera Gear Tracker, and Photo Proofing.
2. **Real Estate Brokerage (`real_estate`)**: Property Showings, Listing Catalog, and Buyer Lead Pipeline.
3. **Marketing Agency (`marketing_agency`)**: Client Retainers, Campaign Funnels, Ad Spend Tracking, and Brand Content AI.

### Security URL Masking & WP Lockdown
To completely mask the underlying WordPress engine:
* **Native Symlinks & Rewrites**: Masks `/wp-content/` to `/assets/` and `/wp-includes/` to `/core/` via server rewrites and symlinks.
* **WP-Admin Lockdown**: Agency users attempting to access `/wp-admin/` are automatically redirected to `/workspace/dashboard`.
* **Virtual URLs**: Clean endpoints for `/workspace/dashboard`, `/workspace/login`, `/workspace/register`, `/workspace/onboarding`.
* **Admin Bar Suppression**: Hides native WordPress toolbar completely for a true white-label SaaS experience.

---

## Section 12: Continuous Hands-Free AI Voice Discussion Engine

The Voice AI Discussion Engine (`window.coraVoiceEngine`) provides real-time, hands-free conversational assistance:
* **Real-Time Streaming Transcription**: Live speech-to-text transcription via Web Speech API with sub-200ms latency.
* **Natural Indian Voice Synthesis & Duplex Audio**: Natural TTS speech synthesis with automatic interruption / barge-in detection.
* **Full-Height Voice Canvas UI**: Dedicated voice interface with animated soundwave visualizer (`.cora-voice-bar`), transcript history, and mic controls.
* **4 Curated Voice Personalities**:
  1. *Myra*: Studio Co-founder & Creative Lead
  2. *Aarav*: Senior Growth & Marketing Strategist
  3. *Vikram*: Executive Operations & Real Estate Broker
  4. *Kavya*: Client Success & Communication Director
* **Multi-Lingual Synchronization**: Auto-detects and transliterates across 9 regional dialects (Indian English, Hindi, Bengali, Tamil, Telugu, Marathi, Gujarati, Kannada, US English).

---

## Section 13: High-Performance Architecture & Micro-Cache Layer

* **Sub-Millisecond Micro-Cache Layer**: `cora_cache_get()` and `cora_cache_set()` provide static runtime caching combined with `wp_cache_*` for sub-millisecond query resolution.
* **SSR Pre-rendering & Batch Queries**: Pre-renders dashboard views on the server to achieve instant client hydration.
* **Self-Healing Database Error Drop-in**: Intercepts transient database connection errors and gracefully retries or renders clean fallback states.
* **Debounce Locks & Event Isolation**: Prevents race conditions and duplicate executions in multi-tab sessions.

---

## Section 14: God-Level Super Admin Console & MRR Telemetry Suite (v4.9.54 - v4.9.59)

When authenticated as Super Admin (`cora_admin` / `admin@cora.local`), the platform delivers a dedicated 11-tab administrative command center (`view-super-admin.php`):

```
+-----------------------------------------------------------------------------------+
|                        SUPER ADMIN CONTROL CONSOLE (v4.9.59)                      |
+-----------------------------------------------------------------------------------+
| [1. Overview]   [2. Revenue MRR]  [3. Tenants]      [4. AI Token Pool]            |
| [5. Feature Matrix] [6. Emergency]    [7. Forensics]    [8. System Health]        |
| [9. Error Logs]     [10. Security]    [11. PWA & Build]                           |
+-----------------------------------------------------------------------------------+
```

### 14.1 11-Tab Administrative Sub-Systems
1. **Platform Overview (`tab-super-overview`)**: Real-time platform KPI cards (Total Active Workspaces, Platform MRR, Global AI Token Burn, Server Load).
2. **Financial & Revenue Telemetry (`tab-super-revenue`)**: Live MRR / ARR metrics, tenant plan distribution (Starter, Professional, Enterprise), and transaction records.
3. **Tenant Directory & Workspace Provisioner (`tab-super-tenants`)**: Multi-tenant search, 1-click workspace provisioning, tenant status toggling (Active, Suspended, Archived), and Impersonation Godmode.
4. **Master AI Token Pool (`tab-super-ai-tokens`)**: Dynamic quota allocation across Gemini, Claude, and OpenAI token pools with custom per-tenant threshold overrides.
5. **Dynamic Feature Flags Matrix (`tab-super-feature-flags`)**: Real-time tenant capability matrix enabling/disabling individual modules per workspace on the fly.
6. **Emergency Command Center (`tab-super-emergency`)**: Platform-wide maintenance mode trigger, emergency database lock, cache flush, and global broadcast announcements.
7. **Forensics & Audit Trail Inspector (`tab-super-audit`)**: Real-time tamper-evident audit log stream recording all admin actions, role changes, and API calls with IP stamps.
8. **System Health & Diagnostics (`tab-super-system`)**: PHP/MySQL runtime telemetry, memory usage, opcache status, cron queue health, and background job metrics.
9. **Error Logs & Incident Monitor (`tab-super-errors`)**: Live error monitor streaming PHP error logs and failed AJAX calls.
10. **Security Symlink & URL Masking (`tab-super-security`)**: Real-time validator ensuring `/assets/` and `/core/` masking symlinks and `.htaccess` rules remain intact.
11. **Release & PWA Version Control (`tab-super-release`)**: View manifest version status, trigger remote cache eviction, and package updates.

### 14.2 Impersonation Godmode HUD
* Allows Super Admins to seamlessly impersonate any agency workspace for troubleshooting.
* Displays a floating top-docked monochromatic HUD bar indicating active impersonation state, tenant ID, session duration timer, and a 1-click "Exit Godmode" trigger that restores Super Admin credentials immediately.

### 14.3 Mobile Super Admin Experience & Navigation
* Dedicated mobile navigation island tailored for Super Admin tasks.
* Full-width responsive subtab bar and bottom slide-up sheets for provisioning and token allocation.

---

## Section 15: Interactive Platform Onboarding Tour System

* **Guided Step-by-Step Walkthrough**: High-contrast monochromatic tour engine (`window.coraStartPlatformTour`) introducing new workspace owners to core modules (Dashboard KPIs, Dynamic AI Co-Founder, Lead Pipeline, Document Vault, Canvas Builder, Field Ops).
* **Beacon Highlighting & Spring Tooltips**: Highlights target DOM elements with a pulsing ring and renders informative popover cards with Next, Back, and Skip triggers.
* **State Persistence**: Tour completion status is stored in local storage and synced to user meta to prevent repeated prompts.

---

## Section 16: Version History & Release Manifest

| Version | Release Date | Key Features & Enhancements |
| :--- | :--- | :--- |
| **v4.9.59** | Sep 2026 | Super Admin mobile navigation overhaul, container isolation fixes, MRR telemetry suite, global scroll bottom clipping fix with flex spacers |
| **v4.9.58** | Sep 2026 | Field Ops & Geolocation Live Tracking with HD multi-layer maps (Esri Satellite, Esri Streets, OSM, CartoDB Dark), stop/rest detection, route replay engine, Touch Pan mode, and strict Single Workspace Owner policy |
| **v4.9.57** | Sep 2026 | Migrated morning/evening attendance reminders from email to interactive PWA push notifications and in-app alerts |
| **v4.9.56** | Sep 2026 | Universal Website (HTML/CSS/JS) multi-page crawler & migrator engine, numeric phone input validation, interactive Platform Onboarding Tour |
| **v4.9.55** | Sep 2026 | Enforced default active Hostinger SMTP relay configuration for instantaneous transactional email delivery |
| **v4.9.54** | Sep 2026 | Super Admin Sidebar Menu Isolation, administrative analytics dashboard, and universal sign-out handler |
| **v4.9.52** | Sep 2026 | Security URL masking: `/wp-content/` masked to `/assets/` and `/wp-includes/` masked to `/core/` via symlinks and rewrites |
| **v4.9.51** | Sep 2026 | Media Proofing folder header breadcrumb file count sync, mobile folder header polish, auto-chat toast silencing |
| **v4.9.50** | Sep 2026 | Replaced legacy `javascript:void(0)` with clean semantic RESTful URLs across all navigation links |
| **v4.9.49** | Sep 2026 | Public Canvas theme routing isolation preventing subpage collision with workspace dashboard views |
| **v4.9.48** | Sep 2026 | Overhauled real-time speech transcription & duplex TTS audio engine with barge-in interruption detection |
| **v4.9.46** | Sep 2026 | Enforced strict tenant-scoped RAG vector lookups and absolute multi-tenant database isolation for workspace AI |
| **v4.9.45** | Sep 2026 | Real-time live streaming speech transcription, natural Indian voice synthesis, and full-height voice canvas UI |
| **v4.9.44** | Sep 2026 | Multimodal Team Migration Hub with Vision OCR roster parsing, page-aware AI copilot, 24h memory auto-rotation, and AI Quota Hub |
| **v4.9.43** | Sep 2026 | Action-oriented AI Co-Founder with bidirectional continuous self-learning RAG loop and multi-module action cards |
| **v4.9.42** | Sep 2026 | Unified Dynamic AI Co-Founder panel with dual text & voice mode, clean SVG icons, and quota progress showcase |
| **v4.9.38** | Sep 2026 | Visual HTML Canvas code-split editor with live DOM synchronization and URL edit state persistence (`?page_id=...&edit_mode=visual`) |
| **v4.9.32** | Sep 2026 | Dual-Engine Canvas Page Builder (Elementor + Visual HTML Lovable), inline contenteditable, clean HTML export, asset scanner |
| **v4.9.30** | Sep 2026 | App Modules & Feature Hub with explicit save workflow, batch toggle controls, and tenant module registry |
| **v4.9.27** | Sep 2026 | Mobile click interception shield, skeleton dismissal engine, and 0ms touch latency removal |
| **v4.9.23** | Sep 2026 | Forms & Reviews 2.0 with AI Conversion Doctor, funnel analytics, WhatsApp Cloud API, and 26 hardened widgets |
| **v4.9.13** | Sep 2026 | Continuous Hands-Free AI Voice Discussion Engine with 4 personalities, auto-endpointing, and 9 regional dialects |
| **v4.9.0**  | Sep 2026 | Multi-Industry Engine expansion (Marketing Agency vertical), WordPress backend lockdown, and virtual URL masking |
| **v4.0.0**  | Aug 2026 | Major platform consolidation release uniting all workspace modules into a unified clean-slate main branch |

---

*Cora Platform v4.9.59 — Master Architectural Manual. Last updated: September 2026.*
