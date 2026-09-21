# Cora Platform — Comprehensive Platform Documentation

This document serves as the master technical specification and architectural manual for the Cora Workspace Platform (v4.9.189).

---

## Section 1: Core Theme System, PWA & Mobile Performance SOP

### 1.1 Pure Light Mode Enforcement & Dark Mode Removal
Starting in **version 3.2.83** and hardened through **v4.9.166**, dark mode support has been completely removed across all Cora platform plugins, workspace views, design tokens, and components. The platform strictly enforces a **pure light mode visual standard** platform-wide.

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
2. **Responsive Positioning & Dynamic CTA Collision Avoidance (v4.9.79)**: Floats from **top-center** (`top: 68px`) on mobile viewports (< 768px) to eliminate visual collision with bottom sheets and navigation island. On desktop (>= 768px), anchors **bottom-right** (`bottom: 84px`, `right: 32px`), dynamically calculating open full-height Studio Drawers and floating CTA buttons to elevate the container smoothly (`transition: bottom 0.22s cubic-bezier(0.16, 1, 0.3, 1)`), ensuring primary action buttons (Save SKU, Dispatch, Next) remain 100% visible and unobstructed.
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
* **Desktop Sliding Drawers & Full-Height Studio Drawers**: On desktop (`>= 768px`), workflow sheets cleanly transition to right-sliding side drawer panels or full-height Studio Drawers positioned dynamically below `#cora-global-topbar`.

#### Drawer & Bottom-Sheet Roster Across Platform
* **Stationery SKU Creator / Editor Studio Drawer (v4.9.72 - v4.9.81)**: Full-height 3-step creation studio (`1. Identity & Media` -> `2. Pricing & GST Margin Math` -> `3. Factory Stock & Logistics`).
* **Bulk CSV & Starter Kits Ingestion Sheet**: Pre-configured sample inventory kits and custom CSV ingestion.
* **Van Consignment Dispatch Sheet**: Driver assignment, route quick chips, Google Maps link, and multi-product allocation.
* **Quick Spot Sale & Billing Sheet**: Field retail cash/UPI spot billing with live stock deduction.
* **Executive 24h Supply Recon & Share Drawer**: Live financial scorecards, loss-prevention diagnostics, and WhatsApp share studio.
* **Dashboard & Mobile Nav Customizer Drawer (v4.9.63, v4.9.92)**: 14 KPI card selector and 16-module mobile navigation island customizer.
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

### 1.5 Universal Mobile & Desktop Body Scroll Lock System & Click Interception Shield (v4.9.137)
* **Universal Body Scroll Lock Architecture**: Injected `html.cora-scroll-locked` and `body.cora-scroll-locked` styles into the document header (`position: fixed; left: 0; right: 0; width: 100%; overflow: hidden; touch-action: none; overscroll-behavior: none; -webkit-overflow-scrolling: auto;`). Controlled globally via:
  ```javascript
  window.coraLockScroll();
  window.coraUnlockScroll(immediate = false);
  ```
  Eliminates background page scrolling, rubber-banding, and scroll jump across iOS Safari and Android Chrome when any drawer, modal, or bottom sheet is open.
* **AI Drawer Background Scroll Lock SOP (v4.9.133)**: Whenever the AI Co-Founder or Voice AI drawer is opened (`window.coraOpenDrawerAI`), the background page scroll is automatically locked via `window.coraLockScroll()`, freezing viewport coordinates. When closed via backdrop, escape key, or close trigger (`window.coraCloseDrawerAI`), `window.coraUnlockScroll()` restores scroll smoothly. The drawer scroll container (`.cora-ai-sidebar-body`) maintains momentum touch scrolling (`overscroll-behavior-y: contain`).
* **Seamless Mobile Warm Cream Background Extension (v4.9.122)**: The mobile viewport background is seamlessly extended to the absolute bottom of the screen (`#fafafa` / `#FBFaf7`) including safe-area insets (`env(safe-area-inset-bottom)`), eliminating jarring white or black seams beneath the mobile navigation island bar and bottom sheets.
* **Scrollable Drawer Container Opt-In**: Elements designated as scrollable (`.cora-drawer-scrollable`, `[data-cora-scrollable]`, `.overflow-y-auto`) are explicitly granted `touch-action: pan-y !important; overscroll-behavior-y: contain !important; -webkit-overflow-scrolling: touch !important;` to ensure internal forms, catalogs, and steppers scroll smoothly with momentum while the body remains locked.
* **Zero 300ms Tap Delay**: Enforced `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` across all interactive elements (buttons, inputs, island nav, drawer sheets) to eliminate mobile tap latency.
* **Platform Click Interception Shield**: Enforced strict pointer-events isolation across all drawer containers, modals, and backdrops (`pointer-events: none` when closed; `pointer-events: auto` only when open).
* **Skeleton Dismissal Engine**: Guarantees that loading skeletons are cleanly dismissed upon DOM hydration, preventing ghost overlay blocking.
* **Purged Global `!important` Overrides**: Eliminated un-namespaced `.hidden { display: none !important; }` rules in favor of scoped lifecycle classes, allowing Tailwind's responsive grid classes (`hidden md:flex`) to cascade without interference.
* **Global Scroll & Padding Buffer**: Enforced `.cora-scroll-spacer` and responsive bottom padding (`pb-28 md:pb-12`) across all views to eliminate bottom-edge clipping and scroll lock behind mobile navigation bars.

---

### 1.6 PWA Architecture & Mobile Performance Engine (v4.9.137)

#### Pure Light Mode Native Splash & Theme Color
* **Instant Light Splash Screen**: Both `/cora-manifest.json` and `<meta name="theme-color">` enforce `#ffffff` and `apple-mobile-web-app-status-bar-style: default` to eliminate dark-to-light flash delays and render the native OS mobile splash instantaneously.
* **Zero Artificial Delays**: Page load and route transitions execute with zero artificial `setTimeout` preloader holds. Server-rendered HTML hydrates immediately on `DOMContentLoaded`.

#### Dynamic Manifest & Version-Stamped Icon Synchronization
* **Automatic App Icon Refresh**: `/cora-manifest.json` dynamically injects full absolute URLs with version stamps (`?v=CORA_WORKSPACE_VERSION`) across all icon definitions (`192x192`, `512x512`, `any`, `maskable`, and shortcut icons) to trigger automatic OS and browser WebAPK icon updates on release.
* **Modern PWA Launch Handlers**: Configured with `display_override: ["standalone", "window-controls-overlay"]`, `launch_handler: { "client_mode": "navigate-existing" }`, `capture_links: "existing-client-navigate"`, and `handle_links: "preferred"`.

#### Standalone PWA In-App Navigation Engine (Breakout Prevention)
* **iOS Safari & Android PWA Link Retention**: Standalone mode (`navigator.standalone === true` or `(display-mode: standalone)`) captures all internal anchor clicks (`/workspace/**`, `/docs/**`, `?page=cora-workspace`) and retains execution inside the installed PWA window using `window.location.assign()`, preventing external browser tab popouts.

#### Universal In-App Update & Asset Sync Engine
* **Docked Top-Right PWA Update Pill (v4.9.121)**: The update notification has been redesigned from an intrusive full-width banner into a sleek, docked top-right update pill (`#cora-pwa-update-pill`). It displays the new version tag, a 1-tap "Update" CTA, and a clean monochromatic dismiss icon (`✕`).
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

### 1.7 Sticky & Sleek Sub-Navigation Tabs Architectural Standard (~36px Height, Flush Mobile) (v4.9.163 - v4.9.166)
To maximize vertical workspace utility and deliver seamless sub-navigation across complex modules (Forms 2.0 and Content AI Suite):
* **Compact Sleek Height (~36px)**: Sub-navigation bars (`#forms-sticky-tabs-bar`, `#content-suite-tabs-bar`) adhere to a compact ~36px height (`min-height: 36px`, `height: 36px`) with smooth transitions on scroll (`transition: all 0.2s ease`).
* **Flush Mobile Alignment (Zero Padding / Margins)**: On mobile viewports (`< 768px`), sticky tabs are positioned flush with the screen edges (`left: 0`, `right: 0`, `px-0`, zero top margin), completely eliminating offset clipping and horizontal bleeding under the topbar.
* **Unified Sticky Scroll Architecture**: Tabs integrate with `window.coraRegisterStickyHeader` and sticky viewport scroll observers, ensuring sticky subtabs lock cleanly beneath the 48px global topbar on scroll with elevated z-index (`z-30`) and subtle bottom border (`border-b border-zinc-200/80`).
* **Interactive Active Indicators & Badges**: Active tabs use monochromatic tonal fills (`bg-zinc-900 text-white` or `bg-zinc-100 text-zinc-900`) with matching numeric badge counts.

---

### 1.8 Rule 13: Zero-Outline Tonal Surface Selection Architecture (v4.9.144)
High-contrast bounding boxes and heavy outline borders create visual fatigue and violate the Cora Design System:
* **Zero Heavy Outline Strokes**: High-contrast, solid black/dark bounding outline borders (such as `border-zinc-900`, `border-black`, `border-white`, `border-2`, or `ring-2` on selected cards, lists, or customizer elements) are strictly forbidden across all platform modules.
* **Tonal Surface Selection Architecture**: Card selection, active states, and focus elements MUST use soft, monochromatic tonal background fills (`bg-zinc-100/90 dark:bg-zinc-800/80`) combined with uniform, subtle structural borders (`border-zinc-200/80`).
* **Indicator Hierarchy**: Selection state is conveyed cleanly through:
  1. Soft surface tonal shift (`bg-zinc-100` vs. unselected `bg-white`).
  2. Monochromatic filled checkbox/pill (`bg-zinc-900 text-white`).
  3. Clean vector icon tile accent.
  4. Never through harsh bounding box outlines, dark perimeter strokes, or high-contrast frames.

---

### 1.9 Fluid Native Touch Scrolling & Removal of Synthetic Pull-To-Refresh (v4.9.140)
* **Elimination of Synthetic Touch Gestures**: Synthetic pull-to-refresh JavaScript engines and touch-hijacking listeners have been completely removed from `admin-script.js` and `admin-style.css`.
* **Zero False Reloads**: Eliminates false page reloads, scroll jitter, and gesture trapping during long-form list and table scrolling on iOS Safari and Android Chrome.
* **Native Browser Momentum Scroll**: Restores 100% native momentum scrolling with `-webkit-overflow-scrolling: touch; overscroll-behavior-y: contain;`.

---

### 1.10 Desktop Centered KPI Analytics Container (Max 60% Width) & Guaranteed 4 Scorecards (v4.9.138 - v4.9.139)
* **Desktop Centered Scoping**: On desktop viewports (`>= 1024px`), the dashboard analytics scorecard container is constrained to a maximum width of 60% (`max-w-[60%] mx-auto`), preventing ultra-wide card stretching on 4K and widescreen monitors while keeping metrics focal.
* **Guaranteed 4 Scorecards Architecture**: Ensures exactly 4 core KPI cards render across all industry modes:
  - Responsive **2x2 grid** on mobile viewports (`grid-cols-2`), optimizing vertical space.
  - Centered **1x4 row** on desktop viewports (`lg:grid-cols-4`).

---

### 1.11 Full-Width Sticky Sub-Tabs Bar & `pan-x` Horizontal Touch Swipe Architecture (v4.9.179)
* **Full-Width Content Alignment**: Sub-navigation bars across complex modules (`view-forms.php`, `view-content-suite.php`, `view-settings-suite.php`, `view-users.php`) span the full width of workspace content seamlessly without floating side gaps or margins.
* **Pinning Below Global Topbar**: When scrolling down, sub-navigation bars pin directly below `#cora-global-topbar` (`top: 48px` or `top: 0` depending on context), keeping active tabs and filters visible at all times.
* **Smooth Horizontal Touch Gestures (`pan-x`)**: On mobile devices, the sub-tabs bar enforces `overflow-x: auto; -webkit-overflow-scrolling: touch; touch-action: pan-x;`, allowing frictionless one-thumb horizontal swiping without interfering with vertical viewport scrolling.
* **Zero Outline & Ring-Free Design**: Focus outlines, high-contrast dark border strokes, and blue rings on tab triggers have been eliminated in accordance with Rule 13, using subtle tonal backgrounds (`bg-zinc-100 dark:bg-zinc-800`) and soft indicator underlines.

---

## Section 2: Core SaaS Business Modules

### 2.1 Content AI Suite & Myra Assistant
The **Content AI Suite** is an enterprise-grade content lifecycle and SEO optimization engine. At the core is **Myra** — a floating, state-aware AI Content Manager.

#### Myra AI Assistant & 1-Click Blog Draft Generator
* **Floating Launcher & Copilot Sheet**: Bottom-center position with online badge. Collapsible panel design.
* **Workspace State Awareness**: Evaluates active subtab, editor context (document ID/title/keyword/word count), library state, and opportunity pipeline.
* **Provider & Model Switching**: Google Gemini 3.5 Flash, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o with live token tracking.
* **Action Tag Execution**: `[ACTION:set_title]`, `[ACTION:set_keyword]`, `[ACTION:insert_text]`, `[ACTION:save_article]`, `[ACTION:create_article]`, `[ACTION:scan_opportunities]`.
* **1-Click Blog Draft Generator (v4.9.161)**: Cora AI chat cards can directly spawn pre-populated, structured blog drafts straight into the Content Library via `cora_ai_create_blog_draft` AJAX action, bridging conversational ideation with the production WYSIWYG editor in a single click.

#### Enterprise Bulk Operations Engine & Floating Selection Toolbar (v4.9.156)
* **Multi-Item Checkbox Selection**: Content Library table features row-level checkboxes alongside a Master "Select All" header toggle (`#content-select-all`).
* **Docked Floating Bulk Toolbar (`#content-bulk-actions-bar`)**: Automatically surfaces at the bottom of the screen upon selecting one or more articles, displaying live selection count (`X articles selected`), clear selection trigger, and 4 bulk operation triggers:
  - **Bulk Stage Progression**: Batch update workflow status (`Draft`, `In Review`, `Published`, `Archived`) in a single atomic transaction.
  - **Bulk Category Assignment**: Reassign editorial tags and categories across selected documents simultaneously.
  - **Bulk CSV Export (`cora_ajax_content_bulk_export`)**: Export selected articles into an audit-ready CSV manifest containing word counts, SEO target keywords, author, and publication dates.
  - **Bulk Safe Deletion**: Multi-item deletion with confirmation dialog and automatic unlinking from editorial calendars.
* **Fixed-Width Dropdown Architecture (v4.9.156)**: Enforces rigid `w-48` dropdown widths and strict typography rules, eliminating horizontal layout jitter, border overlap, and text truncation during fast menu toggling.

#### Responsive Mobile UX Polish (v4.9.157 - v4.9.159)
* **3-Column Mobile Opportunities Grid (v4.9.157)**: Opportunities filter tabs and Overview action cards render in an equal-width 3-column mobile grid (`grid-cols-3 gap-2`), maximizing thumb reach and visual hierarchy.
* **Flush Mobile Sticky Sub-Tabs (v4.9.158)**: Subtab navigation bar aligns flush to mobile viewports (`px-0`, margins removed), preventing horizontal offset under the topbar.
* **Retained Mobile Navigation & AI Drawer (v4.9.159)**: Guarantees that the bottom floating island navigation and universal AI Co-Founder drawer remain accessible throughout all Content Suite sub-dashboards.

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
| **Opportunities** | `ct-opportunities` | Funnel charts, topic clusters, keyword intent, 3-column mobile grid |
| **Calendar** | `ct-calendar` | Monthly/Weekly/Kanban editorial planner |
| **Content Library** | `ct-library` | Notion-styled data table, multi-select bulk operations, pagination, inline editors |
| **SEO Visibility** | `ct-seo` | GEO tracking, 7 audit tabs, backlink badges |
| **Performance** | `ct-performance` | GSC API integration, CTR graphs |
| **Automations** | `ct-automations` | IndexNow, GSC submission, sitemap refresh |

### 2.4 Lead Management & CRM Pipeline Suite (v4.9.109 - v4.9.118)
The Cora Lead Management & CRM Pipeline Suite (`views/view-leads.php`) provides an enterprise-grade sales pipeline engineered for service agencies and high-velocity deal tracking.

#### Ultra-Compact 3-Level Lead Card Architecture (v4.9.118)
To maximize vertical density and minimize layout friction, lead cards use an ultra-compact 3-level anatomy:
1. **Level 1 (Card Header)**: Lead name/company, priority indicator (Urgent, Warm, Cold), and deal value in bold monospace currency (`₹X,XX,XXX`).
2. **Level 2 (Deal Context & Badges)**: Status pill, acquisition source badge (WhatsApp, Referral, Website, Meta Ads, Cold Outreach), estimated close timeline, and service scope chips.
3. **Level 3 (Card Footer & Single-Row Outreach CTAs)**: Compact single-row action bar featuring 1-tap WhatsApp direct chat (`wa.me`), phone dialer (`tel:`), email compose (`mailto:`), and a mini 3-dot context menu for instant stage progression or lead assignment.

#### In-Column Search & Context Sorting (v4.9.116 - v4.9.118)
* **Dedicated Column Search**: Each Kanban column header features a collapsible micro-search input for instant, real-time client filtering within that specific stage.
* **Column Context Sorting**: 1-click sorting per column:
  - *Deal Value (High to Low / Low to High)*
  - *Recency / Creation Date*
  - *Alphabetical (A-Z)*
  - *Urgency / Activity SLA*
* **Live Counter Synchronization**: Column count badges and aggregate deal values update instantaneously in real-time as cards are dragged, filtered, or sorted.

#### Customizable Pastel Column Color Tinting (v4.9.116 - v4.9.118)
* Distinct subtle pastel background tints per pipeline stage (e.g. `bg-sky-50/50` for New, `bg-amber-50/50` for Contacted, `bg-purple-50/50` for Qualified, `bg-indigo-50/50` for Proposal Sent, `bg-emerald-50/50` for Won, `bg-rose-50/50` for Lost) with crisp `border-zinc-200/80` container strokes.
* Column customizer allowing workspace owners to personalize column background tints and visibility.

#### Unified Multi-Filter Popover Engine (v4.9.117 - v4.9.118)
* Clean independent multi-select popover floating above the toolbar:
  - **Source Filter**: Dynamic multi-select across all active lead acquisition sources.
  - **Deal Size Range**: Tiered filters (Micro < ₹25k, Standard ₹25k-₹1L, Enterprise > ₹1L).
  - **Team Assignment**: Filter by assigned team member or unassigned pool.
  - **Date Range**: Today, Last 7 Days, This Month, Custom Range.
  - **Priority / Status**: Cold, Warm, Hot, Urgent.
* Instant 0ms client-side evaluation with dynamic live badge reflecting active filter count (`Filters (3)`).

#### Decision-Oriented Analytics & Customizable Top KPI Cards (v4.9.115 - v4.9.116)
* Compact top analytics bar displaying 4 decision-oriented KPI scorecards:
  1. *Total Pipeline Value*: Active deal volume across all open stages.
  2. *Won Revenue (MTD / QTD)*: Real-time closed-won financial total.
  3. *Pipeline Conversion Velocity*: Average days from Ingestion to Won stage.
  4. *Active Opportunities*: High-intent leads requiring immediate follow-up.
* **KPI Customizer Drawer**: Lets managers toggle specific scorecards on/off or reorder them based on agency focus.

#### Dynamic Form Ingestion Bridge & AI Sales Call Synthesizer (v4.9.109)
* **Dynamic Forms Bridge**: Seamlessly captures inbound responses from Forms 2.0 (`view-forms.php`) and instantiates lead cards directly into the *New* Kanban stage in real-time.
* **AI Sales Call Synthesizer**: Upload or paste sales call transcripts/recordings to extract:
  - Client Pain Points & Project Scope
  - Budget Expectation & Timeline Constraints
  - Sentiment Analysis Score (1-100)
  - Actionable Next Steps & Suggested Outreach Script
* **Polished Lead Detail Drawer**: Full-height sliding drawer providing complete client activity timeline, outreach logs, note taker, deal stage changer, and custom field editor.

#### Numeric Phone Constraint & Multi-Industry Scoping
* Strict numeric regex validation (`/^[0-9+ -]{7,15}$/`) across all contact inputs.
* Dynamic terminology auto-switching between *Client Leads* (Studio/Marketing/Consulting) and *Buyer Leads* (Real Estate).

### 2.5 Media Library & Advanced Editor

* **MIME Filters**, **Dropzone Uploader**, **Storage Quota Meter**.
* **Synchronized Header**: Real-time breadcrumb file count calculation matching active folder contents.
* **Crop Presets**: 1:1, 4:3, 16:9, Free Crop with rotation and flipping.
* **Left Sidebar Controls**: Segment tabs, media card presets, locate and delete mapping.
* **SEO Metadata Manager**: Alt text, caption, description fields.

#### 2.5.1 Public Media Proofing Route Interception & Telemetry Suite (v4.9.187 - v4.9.188)
* **Public Route Interception Architecture**: Intercepts public proofing URLs (`/workspace/shared-media/{token}`, `/workspace/share-media/{token}`, and `/share-media.php?cora_share={token}`) without requiring client authentication.
* **Claude Cream Minimalist Proofing Interface**: Implements Anthropic Claude visual aesthetic (`#FBFaf7` warm cream background), thin vector SVGs, and monochromatic container cards for white-labeled client photo proofing.
* **Granular Telemetry Tracking**:
  - **Impression Tracking (`cora_track_media_share_impression`)**: Automatically increments total and unique client views on proofing portal access.
  - **Download Telemetry (`cora_track_media_share_download`)**: Captures high-resolution asset downloads with actor identifier and timestamp.
* **Proofing Audit Feed & KPI Cards (`view-media.php`)**:
  - Real-time KPI scorecards: *Total Views*, *Unique Views*, *Total Downloads*, and *Unique Downloads*.
  - Interactive filter chips: `All Activity`, `Downloads Only`, and `Views Only`.
  - Detailed telemetry audit log recording actor name, IP address, timestamp, and accessed assets.

#### 2.5.2 Multi-Dimensional Workspace Storage Footprint Engine (v4.9.183)
* **Comprehensive Digital Footprint Calculation (`cora_get_workspace_storage_details`)**: Extends storage auditing beyond media uploads to encompass the entire workspace database and file ecosystem:
  - **Media Attachments**: File size on disk + database metadata records in `wp_posts`.
  - **Document Vault**: Legal PDF contracts, GST invoices, and signed agreements.
  - **AI Chats & Memory**: Vector embeddings in `wp_cora_rag_knowledge`, cached options (`cora_ai_*`, `cora_rag_*`), and user metadata transcripts.
  - **User Activity & Telemetry**: Event logs (`wp_cora_activity_logs`), GPS coordinates (`wp_cora_gps_telemetry`), form submission audits (`wp_cora_form_audit_log`), notifications (`wp_cora_notifications`), security incidents (`wp_cora_security_incidents`), and attendance stamps.
* **Visual Storage Breakdown**: Displays category badges and human-readable storage metrics (MB / GB) directly in the Media Library header and dashboard storage widgets.

### 2.6 Email Management Suite (Hostinger Relay)
* **Outbox & Compose**: Recipient auto-complete, personalization variables, live HTML preview.
* **Enforced Default Active Hostinger SMTP Relay**: Built-in default SMTP relay configuration for transactional notifications, e-sign link delivery, and lead dispatch with zero manual setup required.
* **Sequences & Drip Workflows**: Automated scheduling linked to CRM pipeline stages.

### 2.7 Document Vault & Document Studio
* **5-Step Wizard**: Document type, line items with SAC codes, GST math, e-sign audit.
* **GST Engine**: Auto CGST/SGST (intra-state 9% + 9%) or IGST (inter-state 18%) calculation.
* **Legal E-Sign Audit Registry**: SHA-256 fingerprinting, IP address capture, timestamp certification.

### 2.8 Form AI Architect & Full Lifecycle Engine (v4.9.175 - v4.9.177)
* **Full CRUD Lifecycle**: Comprehensive form builder supporting draft staging, publishing, editing, field reordering, and safe deletion without page reloads.
* **15 Standardized Field Types**: Text, Long Text (Textarea), Numeric Phone, Email, Number, Dropdown Select, Multi-Select, Checkbox Group, Radio Buttons, Date Picker, Time Slot, File Dropzone, Star Rating, NPS Score (0-10), and Signature Pad.
* **Multi-Step Wizard Engine**: Supports multi-page forms with animated progress steppers, step validation barriers, and dynamic conditional branching.
* **Sandboxed Full-Height Live Preview Modal**: Real-time rendering inside a sandboxed viewport simulator supporting Desktop (`100%`), Tablet (`768px`), and Mobile (`375px`) form testing prior to publication.
* **Isolated Top Control Panel**: Form builder header isolated with full-screen focus mode, publishing actions, and preview triggers without interfering with workspace navigation.
* **3-Metric Stage Funnel Analytics**:
  - `1. Form Views` ➔ `2. Started Submissions` ➔ `3. Completed Leads Captured` with drop-off percentages at each barrier.
  - **AI Conversion Doctor**: Highlighted Recommendation Banner detailing diagnostic bottlenecks and estimated conversion lift percentage.
  - **Question Completion Breakdown**: Field-by-field abandon rate telemetry identifying difficult or abandoned form inputs.
* **Dual-Mode Responsive Data Cards**:
  - **Desktop**: Notion-styled interactive data table with sortable columns, CSV export, and batch operations.
  - **Mobile**: High-density touch activity cards displaying applicant details, completion timestamps, and status pills with zero horizontal clipping.
* **1:1 Sticky Sub-Tabs Alignment (v4.9.163 - v4.9.166)**: Compact ~36px height sub-navigation tabs (`#forms-sticky-tabs-bar`) with flush mobile alignment (`px-0`, margins removed) matching Content Suite 1:1.

### 2.9 Crew Scheduler & Equipment Management
* **Crew Scheduler**: Timeline-based crew assignment for studio shoots, production sets, and site showings.
* **Equipment Manager**: Asset check-in/check-out lifecycle, barcode/QR custody logs, condition audits.
* **Client Task Manager**: Shared collaborative milestones with file attachments.

### 2.10 Financial Module & Event Timeline
* **Financials**: Revenue tracking, payment status monitoring, cash flow runway.
* **Event Timeline**: Chronological activity feed across all platform operations.

### 2.11 App Modules & Feature Hub Matrix (v4.9.104 - v4.9.106, v4.9.141 - v4.9.144, v4.9.189)
Located in `views/view-feature-hub.php`, the Feature Hub provides full tenant-level feature governance across **24 Core Foundation & Domain Modules**:
* **Structured 5-Category Matrix (24 Modules)**:
  1. *Core Foundation*: Dashboard, Users & Roles, Document Vault, Media Library (Foundation Asset Hub), App Settings.
  2. *Operations & Delivery*: Crew Scheduler, Equipment Manager, Property Listings (Real Estate), Field Ops & Live Tracking, Stationery & Plant Inventory.
  3. *CRM & Revenue*: Lead Management (CRM Pipeline), Interactive Calendar, Financial Ledger, Client Management, Client Tasks Kanban.
  4. *Studio & Content*: Canvas Dual Theme Builder, Content AI Suite & Myra Assistant, Forms & Reviews 2.0, Photo Proofing Vault.
  5. *AI & Automation*: Dynamic AI Co-Founder (Cora AI), Continuous Voice Discussion Engine, Email Suite & Hostinger Relay, AI Conversion Doctor, Affiliate & Referral Engine.
* **Core Foundation Modules Locking Architecture (v4.9.189)**:
  - Starting in **v4.9.189**, the Cora platform enforces a permanent architecture lock on the 5 foundational operational modules (`blogs`, `forms`, `team-roles`, `media`, `vault`, alongside `dashboard`).
  - **Universal Domain Hardening**: Across all 6 industry domain class files (`class-custom-module.php`, `class-manufacturing-inventory-module.php`, `class-marketing-agency-module.php`, `class-studio-module.php`, `class-professional-services-module.php`, `class-re-module.php`), foundation modules are permanently declared active and immutable.
  - **Tenant Capability Micro-Guard**: In `cora-workspace.php`, `cora_get_custom_enabled_features()` automatically merges foundation features into the enabled tenant feature array, ensuring core capabilities remain available even if tenant meta has outdated entries.
  - **Feature Hub Immutable UI**: In `views/view-feature-hub.php`, locked foundation cards display an immutable `Foundation` badge with a clean lock vector SVG and disabled toggle switches.
  - **Batch Operation Immunity**: Global actions (*Select All*, *Deselect All*, *Reset Defaults*, *Discard*) preserve the active state of foundation modules, preventing tenants from disabling essential infrastructure.
* **24-Module Compact Mobile Grid (v4.9.141 - v4.9.143)**: Re-architected mobile layout into a compact single-column horizontal card list (`flex-row items-center gap-3 p-3`), eliminating excessive vertical scrolling and visual clutter on mobile screens.
* **Unified Search & Control Bar (v4.9.142)**: Global search input coupled with live status filtering (*All Modules*, *Active Only*, *Inactive Only*) and dynamic industry preset tags.
* **Rule 13 Zero-Outline Tonal Selection Compliance (v4.9.144)**: Active and selected states strictly use soft monochromatic tonal background fills (`bg-zinc-100/90 dark:bg-zinc-800/80`) with subtle borders (`border-zinc-200/80`), completely eliminating heavy black outlines, `ring-2`, and dark border strokes.
* **Strict Navigation Decoupling**: Enabling or disabling any module automatically mounts or unmounts its corresponding navigation item from the sidebar and mobile island nav, preventing ghost routes or 404 dead ends.
* **Category Filter Bar & Search**: Instant zero-lag category filtering (*All*, *Foundation*, *Operations*, *CRM*, *Studio*, *AI*) and real-time module keyword search.
* **Explicit Save & Staging Workflow**: Toggle modifications trigger a sticky bottom unsaved changes banner (`.cora-fh-save-banner`). Changes stage cleanly in memory and commit atomically via AJAX to `cora_agency_modules_{agency_id}`.
* **Batch Controls**: 1-click "Enable All Recommended", "Deselect All", and "Reset to Industry Defaults".

### 2.12 Users, Team Governance & Dynamic Role Engine (v4.9.104 - v4.9.108, v4.9.180 - v4.9.186)
Located in `views/view-users.php`, the Agency Team & Governance module delivers comprehensive role-based access control (RBAC), team onboarding, and desktop/mobile customization:

#### 1. Dynamic Role Creation & Permission Matrix (`tab-roles`, `tab-permissions`)
* **Custom Dynamic Roles**: Workspace owners can create custom roles with unique identifiers, color badges, and descriptions tailored to agency hierarchy (e.g. Lead Photographer, Senior Broker, Route Supervisor, Media Editor).
* **Granular Feature Gatekeeping Matrix**: Clean matrix table allowing owners to grant or restrict permissions (`View`, `Create`, `Edit`, `Delete`, `Admin`) per module per role.
* **Strict Owner Gatekeeping**: Workspace Owner permissions are locked and immutable, preventing accidental self-lockout or privilege escalation.
* **Sticky Column Matrix UI (v4.9.105)**: Hardened table with sticky module column headers, zero border-collapse bleed, and responsive horizontal panning.

#### 2. Desktop & Mobile Tab Customization Drawer (`tab-customizer`)
* **Drag-and-Drop Subtab Reordering**: Full drag-and-drop handles (`cursor-grab`) allowing administrators to reorder user navigation tabs seamlessly.
* **Visibility Toggles**: Instantly show or hide specific subtabs per workspace workflow.
* **Persistent Preferences**: Saves tab order and visibility to `localStorage` with real-time AJAX persistence to user meta.

#### 3. Redesigned Mobile Member & Invite Cards (v4.9.107 - v4.9.108)
* **Initials-Based Atomic Avatars**: Replaced broken/mystery gravatars with high-contrast, initials-based SVG avatars (`cora-initials-avatar`) with deterministic monochrome tinting.
* **Modern Atomic Card Layout**: High-density mobile cards with clean typography, status pills (Active, Invited, Suspended), quick contact chips (Phone, WhatsApp, Email), and 1-tap action popovers.
* **Permanent Team Member Deletion Lifecycle (v4.9.107)**: Safe deletion modal with cascade unassignment and zero orphaned database records.
* **Tenant-Scoped Branch Isolation (v4.9.106)**: Strict database query scoping by `agency_id`, eliminating duplicate or leaked test branches.

#### 4. Multimodal OCR Team Migration & Roster Ingestion
* **Vision OCR Roster Ingestion**: Agencies can upload photos, scans, or PDFs of existing employee rosters, attendance registers, or spreadsheets.
* **Vision Model Intelligence**: Utilizes Gemini 3.5 Pro / GPT-4o Vision to parse names, contact phone numbers, emails, designated agency roles, and commission percentages.
* **Interactive Staging & Batch Provisioning**: Displays an editable preview table allowing managers to verify extracted records before executing 1-click batch user account creation.
* **24h Memory Rotation**: Scanned files and staging payloads expire and clean up automatically after 24 hours.
* **Strict Single Workspace Owner Policy (v4.9.58)**: Enforces that each agency has exactly one designated Workspace Owner. The Workspace Owner role is removed from general role assignment dropdowns to prevent accidental permission escalation or multi-owner conflicts.

#### 5. Custom Dynamic Role Builder & Module-Level Capability Scoping (v4.9.184 - v4.9.186)
* **Real Module Titles in Role Drawer**: The custom role editor drawer renders human-readable module names (`Dashboard`, `Content AI Suite`, `Forms & Reviews`, `Users & Roles`, `Document Vault`, `Media Library`, `CRM & Leads`, `Client Management`, `Financials & Accounting`, `Field Operations & Attendance`, `Inventory & POS`, `Canvas Website Builder`) instead of raw internal database slugs.
* **Dynamic Active Workspace Capability Scoping**: Role permission checkboxes dynamically filter strictly to active, enabled modules in the current workspace. Inactive or disabled platform modules are automatically omitted from the role builder, preventing permission drift.
* **Desktop & Mobile Scroll Lock Freeze Resolution**: Resolved touch and scroll locking issues on custom role drawers by utilizing the universal `window.coraLockScroll()` and `window.coraUnlockScroll()` lifecycle with `.cora-drawer-scrollable`.
* **Safe Custom Role Deletion Modal**: Added a monochromatic role deletion confirmation dialog featuring safe cascade re-assignment of existing team members to default roles prior to role removal.
* **Zero-Overflow Mobile Role Cards**: Designed custom role cards with responsive flex wrapping, high-contrast role badges, and zero horizontal clipping on narrow mobile viewports.

#### 6. Active-Only Equal AI Token Budget Distribution (v4.9.180)
* **Active Member Token Allocation**: Workspace monthly AI token credits are divided equally among active team members by default (`status === 'active'`).
* **Exclusion of Inactive & Pending Accounts**: Pending email invitations, suspended staff, and deactivated accounts are strictly excluded from the token budget denominator, ensuring active team members receive their full proportionate share of AI computing power.
* **Dynamic Industry Roles & Specializations**: User roles, specializations, and departmental categories automatically adjust according to the active industry vertical.
* **Decluttered Edit User Drawer**: Streamlined the user profile edit drawer by removing redundant compensation blocks and unused role tag tabs.

#### 7. Mobile Attendance Cards & Zero-Truncation Layout (v4.9.181)
* **Responsive Attendance Telemetry Wrapping**: Mobile attendance logs in `view-users.php` utilize responsive flex wrapping to ensure staff member names, check-in dates, timestamps, and exact GPS coordinates render cleanly with zero ellipsis truncation on 375px screens.
* **JetBrains Mono Coordinate Formatting**: Geolocation lat/long coordinates are rendered in high-legibility monospace font for field verification.

### 2.13 Field Ops & Real-Time Geolocation Tracking Engine (v4.9.58, v4.9.182)
Engineered for mobile dispatch, site visits, shoot crews, and property inspections:
* **Field Ops Telemetry Lifecycle: Session Login Auto-Start & Beacon-Backed Flush (v4.9.182)**:
  - **Login Session Auto-Start**: Located in `assets/js/cora-field-ops-tracker.js` and initialized via `admin-dashboard.php`, the tracking engine automatically detects authenticated user sessions and begins background GPS telemetry capture without requiring manual punch-in clicks for field agents.
  - **Background Heartbeat Pings**: Dispatches periodic location breadcrumbs to `wp_cora_gps_telemetry` with battery-efficient watchPosition throttling.
  - **Beacon-Backed Logout Flush (`navigator.sendBeacon`)**: When a field user logs out, closes their browser tab, or unloads the PWA (`beforeunload` / `pagehide`), pending location coordinates and exact punch-out timestamps are flushed atomically via `navigator.sendBeacon`, guaranteeing zero lost punch-out records during network disconnects.
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

### 2.14 Stationery Manufacturing & Mobile Van Sales Inventory Engine (v4.9.60 - v4.9.99)
Located in `views/view-inventory-management.php` and powered by `includes/class-cora-inventory-engine.php`, this enterprise engine bridges heavy factory production with agile field route distribution:

```
+-----------------------------------------------------------------------------------+
|               STATIONERY MANUFACTURING & VAN SALES DUAL ARCHITECTURE              |
+-----------------------------------------+-----------------------------------------+
| Mode A: Central Plant Command Center    | Mode B: Mobile Field Van Sales POS      |
| • 4 Analytical Telemetry Scorecards     | • Mobile-First Driver Terminal          |
| • Central Plant Catalog & SKU Stepper   | • Live Stock on Wheels Allocation       |
| • Live GST & Mathematical Margin Cards  | • Instant Spot Sale & Cash/UPI Billing  |
| • Consignments Hub & Route City Chips   | • Editable & Deletable Spot Invoices    |
| • Branded Email with 1x1 Tracking Pixel | • Multimodal AI Bill / Receipt OCR      |
| • Executive 24h Supply Recon & Audit    | • GPS Counter Check-in & Stop Logging   |
| • Standalone Print-Ready PDF & WhatsApp | • Day-End Return Settlement             |
+-----------------------------------------+-----------------------------------------+
```

#### Mode A: Central Plant Inventory Command Center
1. **Analytical Telemetry Scorecards (2x2 Mobile Grid / 1x4 Desktop Row)**:
   - **Total Factory Inventory Valuation (₹)**: Real-time net valuation across all raw material and finished stock held in plant bins.
   - **Dispatched / In-Transit Stock Value (₹)**: Current wholesale value of inventory actively on wheels across all deployed vans.
   - **Active Fleet Vans on Route**: Count of operational consignment runs currently serving retail territories.
   - **Factory SKU Catalog Count**: Total number of registered products and unit lots.
2. **Central Factory SKU Catalog**:
   - Notion-styled interactive data table displaying item visual thumbnails, SKU/HSN codes, category, stock health pills (`In Stock`, `Low Stock`, `Out of Stock`), packaging units (Boxes, Pcs, Reams, Packets, Carton, Gross), Factory Base Price, Wholesale Price, MRP, and GST Slab (0%, 5%, 12%, 18%, 28%).
   - Quick action controls: `+ Adjust Stock`, `Edit SKU`, and `Delete SKU` (with safe un-linking and archival).
   - Instant Search & Filter with clean zero-result empty state ("No items match your criteria").
   - 1-Click CSV Catalog Export (`cora_ajax_inventory_export_csv`) and Bulk CSV & Starter Kits Ingestion modal.
3. **Unified Add/Edit SKU Studio Drawer (v4.9.72 - v4.9.81)**:
   - Dynamic full-height Studio Drawer positioned immediately below `#cora-global-topbar` (zero background blur overlay, max-w-5xl body, sticky bottom navigation).
   - Structured 3-Step Guided Stepper:
     - **Step 1: Identity & Media**: Product Name, SKU Code, Category, drag-and-drop product photo uploader (max 2MB, FormData `image` / `image_file`) or direct image URL preview, packaging unit, lot size, and Barcode / HSN code.
     - **Step 2: Pricing & GST Margin Math**: Factory Base Price, Factory Markup Margin %, Wholesale Rate, Retailer Margin Spread %, MRP, and GST Slab. Renders **4 Live Margin Telemetry Cards**:
       * *Factory Margin (₹)*: Net profit per unit realized at the plant gate.
       * *Retailer Spread (%)*: Dealer profit margin incentivizing retail counter distribution.
       * *GST Tax Liability (₹)*: Mandatory tax component calculated by active Place of Supply.
       * *Net Base Price (₹)*: Pre-tax wholesale production benchmark.
     - **Step 3: Factory Stock & Logistics**: Initial Opening Stock, Minimum Reorder Alert Threshold, Warehouse Bin / Rack Location (e.g. `Rack C-04`), and Primary Vendor / Mill sourcing details.
4. **Van Consignments Hub & Route Dispatch Engine**:
   - **Dual Driver Selection**: Assign an existing team member from the directory or invite a new field driver on the fly with auto-assigned role `cora_field_vendor`, tenant scoping, and automated email dispatch via `cora_invitations`.
   - **Vehicle & Territory Logistics**: Vehicle registration number (e.g. `DL-1V-8821`), Google Maps route link, and 1-tap quick city chips (Delhi NCR, Noida, Gurgaon, Ghaziabad, Faridabad, Mumbai, Pune, Bangalore).
   - **Product Allocation Suite**:
     * *1-Tap Top 5 Fast-Selling Suggestions*: Instantly populates the top 5 high-velocity factory catalog SKUs with positive stock.
     * *Multi-Product Catalog Picker Modal*: Search, browse, and multi-select products in bulk with checkboxes, batch select all in-stock items, or adjust dispatch quantities inline.
     * *Interactive Quantity Steppers*: Intuitive `[-] [qty] [+]` buttons with real-time valuation, SKU count, and total dispatched unit badges. Enforces strict zero-unit prevention (quantity >= 1).
5. **Automated Branded Dispatch Emails & 1x1 Zero-Cache Tracking Pixel (v4.9.97)**:
   - Dispatches a professional monochromatic assignment email via Hostinger SMTP relay upon consignment creation, containing route briefs, vehicle details, dispatched valuation, and 1-tap POS activation link.
   - Embedded zero-cache 1x1 tracking pixel (`/wp-admin/admin-ajax.php?action=cora_inventory_track_email_open&token=...`) tracks email engagement in real time.
   - Consignment cards render live status badges:
     * 🟢 **Email Opened** (with relative timestamp, e.g. "Opened 4m ago")
     * 🟡 **Email Sent • Unopened** (dispatched, awaiting recipient view)
     * ⚪ **Email Pending** (drafting or queued)
   - Multi-Channel Share Suite: 1-tap Copy Direct Invite Link, WhatsApp Share Brief, and Re-send Email.
6. **Consignment Lifecycle Governance & Safe Restocking (v4.9.86)**:
   - Full lifecycle states: *In Transit*, *Completed*, *Settled*, *Cancelled*.
   - Editable consignments (update vehicle, route, target coverage) and deletable consignments with **automated safe restocking** of unsold allocated units back to central factory stock.
7. **Executive 24-Hour Daily Supply Recon & Audit Engine (v4.9.87)**:
   - **Daily Financial Scorecards**: Total Stock Dispatched (₹), Spot Sales Recovery % (e.g. 68.2%), Collected Cash/UPI Liquidity (₹), Unsold Restock Value (₹), and Net Discrepancy / Variance (₹0.00 Clean).
   - **Multi-Route Van Settlement Table**: Per-van audit breaking down driver, territory, dispatched valuation, spot sales revenue, restocked units, and settlement status.
   - **Top-Velocity SKU Revenue Rankings**: Identifies fast-selling products across all retail delivery routes.
   - **Strategic AI Diagnostics**: 3 diagnostic cards evaluating fleet sell-through conversion rate, payment channel liquidity risk (cash vs. UPI vs. retailer credit), and inventory shrinkage prevention.
   - **Print-Ready Executive PDF View**: Standalone printable audit report with corporate letterhead, financial scorecards, route breakdowns, and loss-prevention certification stamp.
   - **Share Studio**: 1-tap WhatsApp report sharing with pre-formatted executive summary, briefing text clipboard copy, and printable PDF URL.

#### Mode B: Mobile Field Van Sales & Route POS Terminal
Engineered specifically for field sales representatives and delivery drivers navigating retail distribution routes:
1. **Live Stock on Wheels**: Real-time inventory allocated to the driver's active consignment. Displays remaining in-van units, item rates, and stock health.
2. **Quick Spot Sale & Cash/UPI Billing**:
   - Rapid retail invoicing at retailer shop counters.
   - Selects customer/shop name, items sold, and payment method (Cash, UPI, Retailer Credit).
   - Automatically calculates applicable GST, updates remaining in-van stock in real time, and increments consignment collections.
3. **Editable & Deletable Spot Invoices (v4.9.86)**:
   - All sales entries are fully editable and deletable via dedicated sliding Studio Drawers.
   - Updating an invoice recalculates consignment revenue and payment balances; deleting an invoice safely restores sold items back to van stock and updates ledger totals.
4. **Multimodal AI Bill / Receipt OCR Scanner (Gemini Vision)**:
   - Field reps can snap a photo or upload an image of a physical handwritten retailer challan, counter receipt, or paper invoice.
   - Multimodal Vision model extracts store name, line items, quantities, and totals, staging them for 1-tap spot sale creation.
5. **GPS Shop Visits & Counter Check-In**:
   - Geolocation-verified check-in at retail stores capturing GPS coordinates, counter name, visit duration, and delivery notes.
6. **Day-End Return Settlement**:
   - Field-level return entry recording unsold products, damaged goods, or customer returns, facilitating 100% loss-prevention reconciliation upon return to the factory.

---

### 2.15 Dedicated Field Sales Driver Role (`cora_field_vendor`) & Strict Terminal Isolation (v4.9.98 - v4.9.102)
To enforce strict enterprise security, prevent administrative privilege escalation, and deliver a clean, distraction-free workflow for mobile van drivers:

1. **Dedicated User Role (`cora_field_vendor`)**:
   - Registered in WordPress as "Field Sales Driver".
   - Excluded from workspace owner promotion and standard admin role selectors.
2. **1-Step Driver Activation & 1-Tap Google Sign-Up (`views/setup-account.php`)**:
   - When a user accesses an invitation token assigned role `cora_field_vendor`, the onboarding wizard automatically transforms into a streamlined "Activate Van Terminal" layout.
   - Supports 1-tap Google Sign-Up with auto-redirection straight to the driver's assigned Van Sales POS terminal.
3. **Server-Side Route Guarding & Redirection**:
   - `cora_workspace_handle_workspace_route()` detects users with role `cora_field_vendor`.
   - Any attempt to navigate to other workspace views (`/workspace/dashboard`, `/workspace/leads`, `/workspace/financials`, `/workspace/vault`, `/workspace/settings`) is immediately redirected to `/workspace/dashboard?industry=stationery_inventory&subpage=plant_inventory&mode=vendor`.
4. **Universal Driver Mode Real-Time Chrome Stripping (v4.9.100 - v4.9.102)**:
   - Injected body class `.cora-driver-mode-active` and dynamic CSS overrides that instantly hide the global topbar search (`⌘K` Command Palette), notifications bell, profile avatar/popovers, and desktop sidebar navigation.
   - The header for drivers renders strictly a minimal logo and direct **Sign Out** button.
   - Replaces the 80% boxed layout with **100% full-screen width** without sidebar gaps.
5. **Driver Mobile Navigation & Simulation Banner Suppression (v4.9.100 - v4.9.102)**:
   - Floating mobile navigation island (`#cora-mobile-floating-island`) and mobile navigation drawer (`#cora-mobile-nav-drawer`) are completely omitted from the DOM when accessed by a driver.
   - The role simulation banner (`#cora-role-preview-banner`) is strictly suppressed for drivers via server-side checks and guarded JavaScript execution.
6. **Server-Side Plant DOM Omission**:
   - In `view-inventory-management.php`, the `$is_driver_terminal` flag completely strips central factory plant containers, SKU creation drawers, bulk CSV ingestion modals, and consignment dispatch tools from the HTML markup sent to the browser.
7. **Strictly Grounded Driver AI Copilot**:
   - When a field driver activates the AI Copilot, the system prompt is strictly constrained to the driver's active route, vehicle registration number, allocated in-van stock, retail pricing, spot billing math, and day-end return reconciliation.
   - Platform administration, factory-level stock valuation, team rosters, and workspace management discussions are strictly blocked.
8. **Driver-Safe Controller Actions**:
   - Gemini Vision receipt OCR scanning and shop visit GPS recording are gated directly to the spot billing workflows without switching perspective to central plant mode.

---

### 2.16 Dynamic Dashboard Analytics & Mobile Navigation Customizer (v4.9.63, v4.9.92)
Workspace owners and team members can fully personalize their dashboard experience via the Customizer Drawer (`#cora-dashboard-customizer-drawer`):

```
+-----------------------------------------------------------------------------------+
|               DYNAMIC DASHBOARD & MOBILE NAVIGATION CUSTOMIZER                    |
+-----------------------------------------+-----------------------------------------+
| Tab 1: Dashboard KPI Scorecards         | Tab 2: Mobile Bottom Island Navigation  |
| • Pick up to 4 custom metric cards      | • Personalize 3 middle quick slots      |
| • 14 Available System Metrics           | • 16 Available Platform Modules         |
| • Live check/uncheck indicators         | • Real-time search filter with empty UI |
| • Live metric preview badges            | • Human-readable title preview pills    |
| • Instant AJAX persistence to user meta | • "Reset to System Defaults" trigger    |
+-----------------------------------------+-----------------------------------------+
```

1. **Trigger & Presentation**:
   - Triggered via the top-right pen icon button (`#cora-customize-dashboard-btn`) on the dashboard telemetry row.
   - Opens a responsive panel: sliding right drawer on desktop (>= 768px) and ergonomic bottom-up sheet on mobile (< 768px).
2. **Tab 1: Dashboard KPI Scorecards (14 Metrics)**:
   - Allows users to select up to 4 primary KPI cards to display on their dashboard header.
   - Metric catalog includes: Total Revenue, MRR, Active CRM Leads, Shoot Bookings, Property Listings, Factory Inventory Valuation, Fleet Vans on Route, E-Sign Documents, Dynamic Form Responses, Conversion Health Score, Team Attendance, Media Proofing Files, Pending Tasks, and Field Ops Mileage.
3. **Tab 2: Mobile Bottom Island Navigation Customizer (16 Modules)**:
   - Allows users to personalize the 3 middle quick-access navigation slots on the mobile floating island bar (between Home and Profile).
   - Module catalog dynamically pulls all enabled modules from Feature Hub (Inventory, Content Suite, Finance, Users & Team, Bookings/Shoots, Calendar, CRM Leads, Gear, Crew Roster, Document Vault, Dynamic Forms, Media Assets, Tasks, Canvas Builder, Automations, Staff Attendance).
   - Real-time search input with clean zero-match empty state ("No modules found matching your search").
   - Human-readable slot preview pills (e.g. `Inventory` instead of `plant_inventory`).
4. **Atomic Persistence & Reset**:
   - Saved asynchronously via AJAX (`cora_ajax_save_dashboard_customization`) to user meta key `cora_dashboard_customization_{user_id}`.
   - 1-Click "Reset to System Defaults" (`cora_ajax_reset_dashboard_customization`) restores standard industry presets instantly.

---

### 2.17 Single Consolidated 24-Hour Executive PDF Report & Anti-Spam Notification Engine (v4.9.103)
To eliminate notification fatigue, guarantee inbox hygiene, and deliver high-value operational intelligence to workspace owners:

```
+-----------------------------------------------------------------------------------+
|               SINGLE CONSOLIDATED 24-HOUR EXECUTIVE REPORTING ENGINE              |
+-----------------------------------------+-----------------------------------------+
| Micro-Events & Ephemeral Telemetry      | Consolidated 24-Hour Executive Briefing |
| • SEO Ranking Fluctuations              | • Delivered Strictly ONCE Every 24 Hours|
| • Morning & Evening Attendance Pings    | • Standalone Print-Ready PDF Report     |
| • Field Geofence & Location Updates     | • 4 High-Contrast KPI Scorecards        |
| • User Status & Member Event Logs       | • Multi-Route Consignment Audit Table   |
| • 0-Task Morning Briefings              | • AI Strategic Operational Directives   |
|   ---> 100% In-App Bell & PWA Push Only |   ---> Monochromatic HTML & PDF Email   |
+-----------------------------------------+-----------------------------------------+
```

1. **Disarming Repetitive Micro-Event Emails**:
   - All ephemeral and recurring micro-events (SEO ranking fluctuations, morning/evening staff check-in reminders, individual attendance logs, GPS geofence pings, member role modifications, and 0-task briefings) are completely stripped from email dispatch routines.
   - Micro-events route **100% exclusively to the in-app Bell Notification Center and PWA Web Push alerts**.
2. **Single Consolidated 24-Hour Executive Delivery**:
   - All operational metrics, daily sales revenues, staff attendance registers, completed task milestones, van route settlements, and AI strategic recommendations are consolidated into a single master briefing.
   - Delivered strictly **once every 24 hours per workspace owner** via automated cron (`cora_send_daily_executive_report_cron`) or manual trigger in the Executive Recon Drawer.
3. **Print-Ready Executive PDF Report & Digital Verification Seals**:
   - Renders a clean, print-ready document formatted with enterprise letterhead, 4 KPI telemetry scorecards, multi-route van settlement tables, loss-prevention diagnostics, and digital verification security stamps.
4. **Multi-Agency Recipient Deduplication & Rate Limiting**:
   - Integrated recipient deduplication to guarantee owners overseeing multiple workspaces receive clean, deduplicated reports without inbox flooding.
   - Enforces strict 24-hour rate limit locks preventing accidental duplicate report dispatches.

---

### 2.18 Client Management Suite & Client Task Manager (v4.9.119 - v4.9.124)
The **Client Management Suite** (`views/view-clients.php`) and **Client Task Manager** (`views/view-client-task-manager.php`, `views/partials/partial-clients-kanban-tasks.php`) provide a unified CRM operations platform tailored to agencies and service studios:

```
+-----------------------------------------------------------------------------------+
|               CLIENT MANAGEMENT SUITE & CLIENT TASK MANAGER (CRM)                 |
+----------------------+-----------------------------+------------------------------+
| 4-Subtab Client Hub  | Client Tasks Kanban Pipeline| Floating Task Details Drawer |
| • Directory Overview | • Stage Progression Columns | • Left-Edge Drag Resize Handle|
| • Active Deals/Vault | • In-Column Counter & Deals | • Anchored 48px Below Topbar |
| • Client Tasks Board | • Multi-Filter Popover      | • Rounded-l-2xl Arc & Shadow |
| • Ledger Recon Sync  | • Context Command Menu      | • Checklist, Scope, Assets   |
+----------------------+-----------------------------+------------------------------+
```

1. **4-Subtab Client Management Hub (`views/view-clients.php`)**:
   - **Subtab 1: Directory Overview**: Client search, industry tags, contact cards, outstanding balance summaries, and activity counters.
   - **Subtab 2: Active Projects & Deals**: Contract milestone tracking, deal values, and pipeline stage progression.
   - **Subtab 3: Client Tasks Board**: High-performance Kanban pipeline synced directly to client records.
   - **Subtab 4: Financials & Ledger Synchronization**: Direct bridge to `views/view-financials.php`, calculating real-time reconciliation metrics, total invoiced amounts, settled revenues, and outstanding receivables per client account.
2. **Client Task Manager (`views/view-client-task-manager.php`)**:
   - High-density Kanban board columns with live task counters and aggregated deal values.
   - Smooth drag-and-drop and 1-tap stage progression across industry lifecycle stages.
   - **Multi-Filter Popover**: Filter by status, assigned team member, priority level, or client account with zero emojis.
   - **Right-Click Context Command Menu (`#cora-task-context-menu`)**: Quick action popover enabling 1-click stage advancement, priority reassignment, member delegation, and task deletion.
   - **Multi-Industry Dictionaries**: Industry-specific stage terminology (`photography_studio`, `real_estate`, `marketing_agency`, `stationery_inventory`, `professional_services`).
3. **Resizable Floating Task Details Drawer**:
   - Floating ergonomic panel anchored 48px below the top navigation bar (`top: 48px`).
   - Distinctive left-edge arc (`rounded-l-2xl`) and ambient drop-shadow (`shadow-2xl`).
   - Zero dark backdrop overlay (`backdrop: none`), allowing full visibility of underlying board columns during active inspection.
   - Drag-to-Resize Left Handle (`#cora-task-drawer-resize-handle`, `window.coraInitTaskDrawerResize`) with persisted width in `localStorage`.
   - **4 Dedicated In-Drawer Tabs**:
     - `Checklist`: Interactive subtask list with automated progress percentage bar, 1-click completion toggle, and inline subtask adder.
     - `Scope`: Core project metadata, deal values, and property/deliverable requirements.
     - `Assets`: Proofing asset review vault, download links, and revision tracker.
     - `Activity`: Immutable timestamped audit trail of task transitions, assignees, and comments.
   - **Mobile Form Factor**: Automatically switches to an ergonomic bottom-up slide sheet with top drag handle on screens < 768px.
4. **Task Scheduling Past-Time Validation & Auto-Slot Selection (v4.9.150)**:
   - **Past-Time Prevention for Today's Date**: Evaluates selected task dates against current system timestamps, preventing accidental scheduling of tasks at past times on the current day.
   - **Auto-Computed Next Time Slot**: When opening task creation or rescheduling drawers, dynamically calculates the next clean 15-minute or 30-minute upcoming interval (e.g. if current time is 11:18 AM, automatically selects 11:30 AM).
   - **Client & Server Guarding**: Enforces dual-layer validation with instant monochromatic feedback toasts (`coraShowToast`), preventing invalid time submissions from entering the task ledger.

---

### 2.19 Public White-Labeled Client Portal (`public-client-portal.php`)
The **Public Client Portal** delivers a branded, mobile-first, passwordless experience for agency and studio clients:
1. **White-Labeled Client Routing**:
   - Tokenized URLs (`?token=cora_clt_*`, slug, or ID) allow clients to review deliverables, project progress, and invoices securely without requiring WordPress user accounts.
2. **Claude Cream Design Aesthetic**:
   - Strict adherence to the Anthropic Claude design theme: warm cream background (`#FBFaf7`), minimal container cards with thin border strokes (`#E8E5DE`), and high-contrast typography.
3. **Proofing Vault & Deliverables Review**:
   - High-resolution gallery preview, client approval triggers, revision notes input, and batch asset downloads.
4. **Official Vector Payment Gateways (India & Global)**:
   - Integrated payment options for instant settlement.
   - Uses official vector SVG brand marks for Indian payment rails: **WhatsApp Pay**, **Google Pay**, **PhonePe**, and **Paytm**, alongside international Stripe/Card gateways.

---

### 2.20 Affiliate & Referral Ecosystem (`includes/affiliate-referral-engine.php`, `views/view-affiliate-referrals.php`)
The **Affiliate & Referral Engine** provides an end-to-end partner growth engine with dual-incentive attribution:
1. **Dual-Reward Incentive Architecture**:
   - **Free Signups**: Referring partner and new user both receive +100 AI credits immediately upon registration.
   - **Paid Conversions**: Referring partner earns a recurring 40% commission (`COMMISSION_PCT = 40.0`) on all active subscription billings.
2. **3-Step Partner Enrollment Screener**:
   - Prospective partners complete a lightweight 3-step qualification screener (Audience Profile -> Promotional Channels -> Payout Details) via `cora_affiliate_enroll` AJAX before unlocking their personalized affiliate dashboard.
3. **Dedicated Schema & Attribution**:
   - Tables: `wp_cora_referral_links` (custom tracking codes, click counts, conversion counters), `wp_cora_referrals` (referred user IDs, status `signed_up`/`subscribed`, recurring commission ledger), `wp_cora_affiliate_payouts` (withdrawal history, payout method, status).
   - 30-Day Attribution Cookie (`cora_referral_code`, `COOKIE_DAYS = 30`).
   - ₹1,000 Minimum Payout (`MIN_PAYOUT = 1000.0`) with automated UPI / IMPS bank transfer requests.
4. **Geolocation-Based Annual Pricing & Commission Matrix**:
   - Tiered commission structure across 6 plans (Creator, Starter, Professional, Growth, Agency, Enterprise) with automatic geo-detection for INR (₹4,999 to ₹19,999/yr) and USD ($99 to $399/yr).
5. **Multi-Channel Vector Share Suite**:
   - Official SVG brand marks for instant social sharing: WhatsApp, LinkedIn, X (Twitter), and client-side QR Code generator.

---

### 2.21 System Settings Suite & Persistent Notification Preferences (v4.9.179)
Located in `views/view-settings-suite.php` and accessed via `/workspace/settings`, the System Settings Suite centralizes workspace-level governance, integrations, and communications:
* **Persistent Notification Channels & Trigger Matrix**:
  - Toggles for delivery channels: **Email (Hostinger SMTP Relay)**, **WhatsApp (Meta Cloud API)**, and **In-App / Web Push Alerts**.
  - Event-level trigger toggles: *Lead Inbound Capture*, *Task Due & Assignment Alerts*, *E-Sign Contract Signatures*, *Attendance Punch Logs*, and *Financial Invoice Settlements*.
  - **AJAX State Persistence (`cora_ajax_save_notification_settings`)**: Channel toggles and event matrix checkboxes automatically persist to tenant options atomically via AJAX, ensuring toggle settings survive browser reloads and device switches.
* **Sticky Full-Width Sub-Navigation Tabs**:
  - Sub-navigation tabs (*General*, *Notifications*, *Integrations*, *Security & Audit*) adhere to the standardized ~36px height, pinning directly below the global topbar on scroll.
  - Features `touch-action: pan-x` smooth horizontal touch scrolling on mobile devices, with zero heavy outlines or focus rings.

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
| `wp_cora_inventory_products` | Central factory catalog (SKU, barcode, HSN, wholesale/retail rates, GST slabs, stock, thresholds, storage bin) |
| `wp_cora_inventory_consignments` | Van dispatch manifests (driver ID, vehicle number, territory, target cities chips, route URL, status, valuation, email open status) |
| `wp_cora_inventory_consignment_items` | Allocated van inventory line items, dispatched qty, returned qty, sold qty |
| `wp_cora_inventory_shop_visits` | GPS check-ins at retail stores with geo-coordinates, counter name, and visit notes |
| `wp_cora_inventory_sales` | Field van spot sales invoices with customer, payment mode (cash/UPI/credit), net/gross totals |
| `wp_cora_inventory_sales_items` | Line items per spot invoice with product ID, qty, rate, and tax slab |
| `wp_cora_inventory_daily_audits` | 24-hour daily supply recon snapshots, total dispatched, sold, restocked, and variance metrics |
| `wp_cora_security_incidents` | Security incident and policy violation audit logs (ref, agency, user, IP, category, severity, prompt excerpt, dual escalation flags) |

### 6.2 Strict Agency Isolation & Tenant-Scoped Queries
All SQL queries and AI contextual retrievers strictly filter by `agency_id = %d`. Data from one tenant is cryptographically and logically isolated from all other workspaces.

---

## Section 7: Dynamic AI Co-Founder Panel, Cora AI Engine & Enterprise Safety Guardrails (v4.9.125 - v4.9.166)

The Cora AI engine delivers an enterprise-grade, action-oriented Co-Founder experience backed by dual-mode chat/duplex voice, comprehensive safety guardrails, and conversation history management:

```
+-----------------------------------------------------------------------------------+
|                        DYNAMIC AI CO-FOUNDER ENGINE (CORA AI)                     |
+-----------------------------------------+-----------------------------------------+
|        Unified Dual-Mode Panel          |      Enterprise Safety & Reliability    |
|  • Mode A: Interactive Text Chat Copilot|  • 6 Policy Violation Categories        |
|  • Mode B: Live Continuous Voice Duplex |  • Real-Time Dual-Escalation Engine     |
|  • History Drawer (#cora-ai-history)    |  • wp_cora_security_incidents Audit Log |
|  • 1-Click '+ New Chat' Quick Reset     |  • Balanced Brace Parser (JSON Actions) |
|  • 1-2 Line Conciseness Standard        |  • RBAC Action Execution Guarding       |
|  • Generative Cards & Draft Generator   |  • Tenant-Scoped RAG Memory & Quotas    |
+-----------------------------------------+-----------------------------------------+
```

### 7.1 Standardized "CORA AI" Branding & Modern Sparkle Icon (v4.9.140)
* **Unified Nomenclature**: Standardized assistant name across all interface templates, system prompts, error toasts, and documentation to **CORA AI** / **Cora AI**.
* **Modern Vector Iconography**: Replaced legacy 5-point star graphics with a bespoke, thin-line AI sparkle vector SVG (`stroke-width: 1.8`).
* **Pulsing Desktop Shortcut Button**: Dashboard header features a high-visibility AI quick-launcher button with a dynamic moving purple gradient pulse animation (`.cora-ai-trigger-pulse`), allowing 1-click drawer activation from anywhere on the screen.

### 7.2 Dedicated Chat History Drawer (`#cora-ai-history-drawer`) (v4.9.140)
* **Header Hamburger Toggle**: The top-left avatar in the AI drawer header has been converted into a responsive hamburger toggle icon (`#cora-ai-history-toggle`).
* **Slide-In Session Management**: Clicking the hamburger icon smoothly slides in the dedicated Chat History Drawer (`#cora-ai-history-drawer`), displaying chronologically grouped previous conversation threads (Today, Yesterday, Last 7 Days, Older).
* **1-Click Conversation Restoration**: Selecting any past conversation item retrieves the transcript from `cora_agency_ai_memory`, clears current DOM state, and hydrates the conversation thread seamlessly without page reload.
* **Thread Isolation & Deletion**: Allows users to archive or delete specific chat sessions safely with zero orphaned memory logs.

### 7.3 User-First Conversational AI Architecture & Conciseness SOP (v4.9.160 - v4.9.162)
To maintain an executive, high-velocity user experience and prevent cognitive overload:
* **1-2 Line Conversational Response Rule**: Cora AI is strictly instructed to respond in 1 to 2 conversational, helpful sentences before presenting action options or generative cards.
* **Elimination of Telemetry & Raw Data Dumps**: The assistant is strictly prohibited from dumping raw JSON payloads, database schemas, SQL queries, or unsolicited quota telemetry in conversational chat prose.
* **Structured Generative Response Cards (v4.9.161)**: When complex information or multiple options are requested, the response renders clean, mobile-first rich text cards, numbered option blocks, or interactive buttons rather than dense paragraph blocks.
* **1-Click Blog Draft Generator Integration (v4.9.161)**: Ideation cards generated during content brainstorming include a direct 1-click CTA to spawn structured draft articles into the Content Library via `cora_ai_create_blog_draft`.

### 7.4 Airtight Balanced Brace Action Tag Parsing (`cora_ai_extract_balanced_json`) (v4.9.162)
Action execution between LLM text streams and platform controllers uses structured action tags (`[ACTION:action_name {...}]`). To eliminate parsing failures caused by trailing text or markdown backticks:
* **Algorithmic Balanced Brace Parser**: Implemented `cora_ai_extract_balanced_json($str, $start_pos)` in PHP.
* **Brace Counter Architecture**: Instead of fragile regex boundary matching, the parser tracks opening `{` and closing `}` braces sequentially, ignoring escaped strings, to extract valid nested JSON structures with 100% precision.
* **Graceful Fallback**: Automatically sanitizes trailing triple backticks (`` ``` ``) and formatting prose before passing parameters to action dispatchers (`cora_ai_handle_action`).

### 7.5 Enterprise AI Safety Guardrails & Dual-Escalation Engine (v4.9.139)
To enforce strict enterprise compliance, mitigate liability, and protect agency tenants:
* **Dedicated Security Audit Table (`wp_cora_security_incidents`)**: Automatically provisions a structured audit ledger capturing `incident_ref`, `agency_id`, `user_id`, `user_login`, `user_email`, `user_role`, `ip_address`, `user_agent`, `violation_category`, `severity`, `prompt_excerpt`, `escalated_to_platform`, `escalated_to_workspace`, and timestamp.
* **6 Mandatory Policy Violation Categories**:
  1. `explosives_weapons`: Chemical, biological, radiological weapons or munitions synthesis.
  2. `terrorism_violence`: Extremist propaganda, physical attacks, or mass violence incitement.
  3. `nudity_explicit`: Explicit adult media, child exploitation, or non-consensual content.
  4. `religious_defamation_conflict`: Targeted sectarian hate speech or incitement of communal conflict.
  5. `self_harm`: Suicide, deliberate self-injury, or eating disorder instructions.
  6. `jailbreak_injection`: System prompt injection, adversarial prefix attacks, or role-break exploits.
* **Real-Time Automated Dual-Escalation Engine**:
  - Automatically flags policy violations with `severity = 'critical'`.
  - Concurrently dispatches immediate priority notifications to **Platform Super Admins** (`cora_get_platform_super_admin_user_ids`) AND **Workspace Owners** (`cora_get_workspace_owner_user_ids`).
  - Logs notification dispatch status flags (`escalated_to_platform = 1`, `escalated_to_workspace = 1`).
* **RBAC Action Execution Validation**: All AI-invoked backend actions undergo strict server-side capability and role checking (`cora_user_can_execute_ai_action`) before mutation execution, preventing unauthorized privilege escalation via AI copilot commands.

### 7.6 Continuous Voice AI Mode & Hands-Free Discussion (v4.9.129 - v4.9.135)
* **Direct Voice Switch via Footer Mic (v4.9.132)**: The footer microphone icon button triggers an instant switch to Voice Mode (`window.coraSwitchToVoiceMode`), activating live duplex speech processing.
* **Auto-Suppressed Mobile Keyboard & Input Stripping (v4.9.129)**: When entering Voice Mode, bottom text inputs are hidden and input focus is blocked to eliminate disruptive mobile software keyboard popups.
* **Integrated Voice Settings Tab (v4.9.130)**: Voice engine configuration (speech rate, pitch, language selector, auto-endpointing sensitivity, and Indian dialect accents) is accessible directly via an integrated tab in the drawer header (`window.coraToggleDrawerAIQuota(..., 'voice')`).
* **Seamless In-Drawer Mode Tab Navigation (v4.9.135)**: Mode switch tabs (Chat vs. Voice) use borderless, outline-free styling for seamless integration into the monochromatic drawer header.

### 7.7 In-Drawer Telemetry, Monthly Parity & Quota Architecture (v4.9.126 - v4.9.134)
* **Compact 1-Row Telemetry Bar (v4.9.131)**: AI usage statistics are formatted into an ultra-compact single-row bar / 2-column pacing cards display (`#cora-ai-quota-card`), saving 65% vertical drawer space.
* **Monthly Parity Pacing Calculations (v4.9.128)**: Analyzes current day-of-month consumption velocity against total monthly allowance, indicating whether the agency is `On Track`, `Pacing High`, or approaching exhaustion.
* **Strict 3px Progress Bar Height SOP (v4.9.134)**: Progress indicator bars enforce a strict `height: 3px !important` constraint in CSS (`.cora-quota-progress-fill`) to prevent vertical ballooning into oval shapes across different browser rendering engines.
* **Tier-Based AI Quota Limits & High-Z-Index Modal (v4.9.126)**: Dynamic allowance tiers (Standard, Pro, Enterprise). Quota upgrade and token replenishment are handled through a dedicated modal with `z-index: 999999` to ensure visibility above all platform drawers.

### 7.8 Bidirectional Continuous Self-Learning RAG Loop
* **Knowledge Ingestion**: AI captures agency preferences, client feedback, and operational patterns back into tenant memory (`cora_agency_ai_memory`).
* **24h Memory Auto-Rotation**: Cleans up stale short-term session states while committing hardened operational guidelines to long-term memory.
* **Strict Tenant Scoping**: All RAG vector lookups and prompt contexts are strictly partitioned by `agency_id`.

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

Cora supports 5 distinct industry archetypes with full vertical adaptation, dynamic role scoping, and custom terminology:
1. **Photography Studio (`photography_studio`)**: Shoot Bookings, Crew Scheduler, Camera Gear Tracker, and Photo Proofing.
2. **Real Estate Brokerage (`real_estate`)**: Property Showings, Listing Catalog, and Buyer Lead Pipeline.
3. **Marketing Agency (`marketing_agency`)**: Client Retainers, Campaign Funnels, Ad Spend Tracking, and Brand Content AI.
4. **Stationery Manufacturing & Plant Operations (`stationery_inventory` / `manufacturing`)**: Central Plant Catalog, 3-Step SKU Studio Drawer, Margin Math, Consignment Van Dispatch with Multi-City Route Chips, Tracking Pixel Email Telemetry, Field Van POS Terminal, AI Receipt OCR, and Executive 24h Supply Recon & Loss Prevention Audit Engine.
5. **Professional Services & Consulting Agency (`professional_services`)**: Client Accounts, Retainers & Engagements, Project Deliverables, Time & Utilization Billing, and 22-module agency operating roadmap with P0/P1/P2 lifecycle gates.

### 11.1 Standardized Sidebar Grouping (v4.9.110 - v4.9.113, v4.9.178)
Across all industry templates, navigation headers are unified into an intuitive, high-velocity hierarchy:
* **CRM & Revenue (Independent Group)**: Lead Pipeline (Kanban), Interactive Calendar (Bookings/Schedules), and Financial Ledger (Cash flow & runway).
* **Workspace Foundation**: Forms & Reviews 2.0, App Modules (Feature Hub), Users & Dynamic Roles (including Attendance subtab), and System Settings.
* **Operations & Delivery**: Crew scheduling, bookings/showings, task manager, property listings, and equipment inventory.
* **Studio & Content**: Canvas Dual Theme Builder, Content AI Suite, and Media Proofing Manager.
* **Universal Role Permissions Fallback & Defensive Navigation Shield (v4.9.178)**:
  - Located in `cora_filter_sidebar_nav_by_role()` inside `cora-workspace.php`.
  - When custom role permissions restrict or omit modules, the sidebar defensive filter checks both primary slugs and parent group configurations.
  - If all child items within a group are disabled or unauthorized for a user's role, the entire parent group heading is gracefully suppressed, preventing empty floating category headings or orphaned dividers in the sidebar.
  - Standard fallback guarantees that every authenticated user retains access to essential core workspace routes without hitting 403 access dead-ends.

### 11.2 Staging Environment Dynamic Icon & Branding (`stagging.heycora.in`)
The platform dynamically senses staging runtime execution via `cora_is_staging_env()`:
* **Dynamic App Icon Ribbon**: Overlays a dedicated high-contrast `'STG'` corner ribbon badge on app icons, Apple touch icons, and browser favicons.
* **PWA Web App Manifest Sync**: Updates the web app title dynamically to `CORA Staging` (short name: `CORA (STG)`), preventing icon confusion on mobile home screens when multiple builds are installed concurrently.

### 11.3 Security URL Masking & WP Lockdown
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
| **v4.9.189** | Sep 2026 | Enforced universal Core Foundation Modules locking (`blogs`, `forms`, `team-roles`, `media`, `vault` alongside `dashboard`) across all 6 industry domain class files, Feature Hub immutable lock badges, and tenant capability micro-guard in `cora-workspace.php` |
| **v4.9.188** | Sep 2026 | Public Media Proofing route interception (`/workspace/shared-media/{token}`, `/workspace/share-media/{token}`, `share-media.php?cora_share={token}`) with Claude cream interface, unique impression tracking (`cora_track_media_share_impression`), and download telemetry |
| **v4.9.187** | Sep 2026 | Media Proofing telemetry suite with KPI scorecards (Total/Unique Views & Downloads), interactive filter chips (`All`, `Downloads`, `Views`), and detailed audit log in `view-media.php` |
| **v4.9.186** | Sep 2026 | Custom role builder capability checkboxes strictly filtered to active workspace modules; resolved desktop and mobile touch-scroll lock freeze via `window.coraLockScroll()` |
| **v4.9.185** | Sep 2026 | Replaced raw module database slugs with human-readable module titles in custom role builder drawer; responsive zero-overflow mobile role cards |
| **v4.9.184** | Sep 2026 | Safe custom role deletion modal with cascade member reassignment, dynamic capability grid, and role status pills |
| **v4.9.183** | Sep 2026 | Multi-dimensional workspace digital storage footprint calculator (`cora_get_workspace_storage_details`) accounting for media attachments, document vault contracts, AI chats/vectors, and user activity/telemetry/attendance logs |
| **v4.9.182** | Sep 2026 | Field Ops telemetry lifecycle with session login auto-start, background watchPosition heartbeat pings, and beacon-backed logout flush (`navigator.sendBeacon`) on window unload |
| **v4.9.181** | Sep 2026 | Mobile attendance card responsive flex wrapping in `view-users.php`, preventing staff name, date, and GPS coordinate truncation on 375px viewports |
| **v4.9.180** | Sep 2026 | Active-only equal AI token budget allocation (strictly excluding inactive/pending accounts), dynamic industry roles and specializations, and decluttered edit user drawer |
| **v4.9.179** | Sep 2026 | System Settings notification persistence (`cora_ajax_save_notification_settings`) across Email/WhatsApp/Push and sticky horizontal sub-tabs with `pan-x` smooth touch swipe |
| **v4.9.178** | Sep 2026 | Universal sidebar navigation defensive shield (`cora_filter_sidebar_nav_by_role`) preventing empty menu groups when role permissions restrict child modules |
| **v4.9.177** | Sep 2026 | Dual-mode responsive form data cards: Notion-styled interactive table on desktop, responsive activity feed cards on mobile devices |
| **v4.9.176** | Sep 2026 | Form AI Architect 3-stage funnel analytics (*Views*, *Started*, *Leads Captured*), AI Conversion Doctor recommendations with estimated lift, and question completion breakdown |
| **v4.9.175** | Sep 2026 | Form AI Architect full CRUD lifecycle, 15 native field types, multi-step wizards, and sandboxed full-height live preview modal |
| **v4.9.166** | Sep 2026 | Matched Forms sub-navigation tabs 1:1 with Content Suite UX, badges, and dimensions (`#forms-sticky-tabs-bar`), ~36px sleek compact height on scroll, flush on mobile viewports (`px-0`, margins removed), aligned scroll architecture, live modal form preview |
| **v4.9.165** | Sep 2026 | Constrained Forms sticky sub-navigation tabs width and container alignment to eliminate viewport offset |
| **v4.9.164** | Sep 2026 | Flush mobile sticky sub-tabs layout eliminating horizontal bleed and topbar clipping |
| **v4.9.163** | Sep 2026 | Compact ~36px height, smooth scroll transitions, and active tonal indicators for Forms sub-navigation tabs |
| **v4.9.162** | Sep 2026 | Airtight balanced brace JSON extractor (`cora_ai_extract_balanced_json`) for LLM action tag parsing |
| **v4.9.161** | Sep 2026 | 1-Click blog draft generator directly into Content Suite drafts, mobile rich text cards, multi-line numbered options in Cora AI |
| **v4.9.160** | Sep 2026 | Enforced 1-2 line concise conversational responses in Cora AI; eliminated raw database and unsolicited telemetry dumping |
| **v4.9.159** | Sep 2026 | Restored bottom mobile floating island navigation and universal AI drawer throughout Content Suite sub-dashboards |
| **v4.9.158** | Sep 2026 | Content Suite sticky sub-tabs flush mobile alignment with zero left padding |
| **v4.9.157** | Sep 2026 | Responsive 3-column mobile Opportunities grid and equal action cards in Content Suite |
| **v4.9.156** | Sep 2026 | Enterprise bulk operations engine in Content Library (multi-selection checkboxes, floating bulk toolbar `#content-bulk-actions-bar`, bulk status/category/export/delete), fixed-width dropdown layout |
| **v4.9.155** | Sep 2026 | Content Suite library view stabilization and bulk selection staging |
| **v4.9.154** | Sep 2026 | Content Suite responsive table padding and column alignment |
| **v4.9.153** | Sep 2026 | Content Suite pagination and bulk selection state persistence |
| **v4.9.152** | Sep 2026 | Task scheduling time input formatting and client validation |
| **v4.9.151** | Sep 2026 | Task scheduling auto-time slot computation and timezone sync |
| **v4.9.150** | Sep 2026 | Task scheduling past-time validation for today's date with auto-computed upcoming 15/30-min slots |
| **v4.9.149** | Sep 2026 | Dashboard header responsive flexbox spacing and mobile profile card polish |
| **v4.9.148** | Sep 2026 | Dynamic user display name resolution and profile card parity |
| **v4.9.147** | Sep 2026 | Moving purple gradient AI shortcut button pulse animation (`cora-ai-trigger-pulse`) |
| **v4.9.146** | Sep 2026 | Desktop dashboard KPI analytics container centered with 60% max-width constraint |
| **v4.9.145** | Sep 2026 | Feature Hub category tab responsive scrolling and pill alignment |
| **v4.9.144** | Sep 2026 | Rule 13 zero-outline tonal surface selection compliance across Feature Hub and Customizers |
| **v4.9.143** | Sep 2026 | Feature Hub 24-module compact single-column horizontal card mobile layout |
| **v4.9.142** | Sep 2026 | Feature Hub unified search bar, status filters, and dynamic industry presets |
| **v4.9.141** | Sep 2026 | Live interactive form preview modal in Forms 2.0; Feature Hub 24-module directory expansion |
| **v4.9.140** | Sep 2026 | Standardized "CORA AI" branding, modern sparkle SVG icon, `#cora-ai-history-drawer` chat history slide-in drawer, removed synthetic pull-to-refresh JS engine |
| **v4.9.139** | Sep 2026 | Enterprise AI Safety Guardrails (`wp_cora_security_incidents`, 6 policy violation categories, automated dual escalation to Super Admin & Workspace Owner, RBAC action validation), guaranteed 4 KPI scorecards (2x2 mobile, 1x4 desktop) |
| **v4.9.138** | Sep 2026 | Desktop centered KPI scorecards container (max 60% width), guaranteed 4-metric scorecards |
| **v4.9.137** | Sep 2026 | Add 1-click + New Chat action control to AI drawer header, reset conversation state without page reload (`admin-dashboard.php`) |
| **v4.9.136** | Sep 2026 | Action-oriented chat UI with generative cards, structured markdown formatting, and right-aligned user speech bubbles |
| **v4.9.135** | Sep 2026 | Remove distracting outline and border from in-drawer tab selector for clean seamless visual integration |
| **v4.9.134** | Sep 2026 | Enforce strict 3px height on AI quota progress bar to eliminate vertical oval ballooning and maintain clean horizontal bar geometry |
| **v4.9.133** | Sep 2026 | Universal AI drawer background page scroll lock SOP (`coraLockScroll` / `coraUnlockScroll`) eliminating background viewport jitter |
| **v4.9.132** | Sep 2026 | Footer mic button wired as direct Voice Mode switch, auto-focus prevention, and shortened input placeholders |
| **v4.9.131** | Sep 2026 | In-drawer telemetry redesigned into compact 1-row block and resolved 0% percentage rounding glitch |
| **v4.9.130** | Sep 2026 | Redesign in-drawer telemetry to compact 2-column pacing cards and integrated voice settings tab |
| **v4.9.129** | Sep 2026 | Hide bottom input in Voice Mode and prevent mobile keyboard popup on voice mic triggers |
| **v4.9.128** | Sep 2026 | Refine in-drawer quota telemetry to monthly parity, minimal directional cards, and simplified model branding |
| **v4.9.127** | Sep 2026 | Implement in-drawer AI quota accordion expansion and airtight mobile scroll lock SOP |
| **v4.9.126** | Sep 2026 | Tier-based AI quota system, high-z-index quota modal, and universal drawer scroll lock SOP |
| **v4.9.125** | Sep 2026 | AI Co-Founder quick action presets converted to compact horizontal scroll rail |
| **v4.9.124** | Sep 2026 | Reduce side padding on Live Metrics container and cards |
| **v4.9.123** | Sep 2026 | Live metrics telemetry card hover effect, alignment, and currency vector icons |
| **v4.9.122** | Sep 2026 | Mobile warm cream background extended seamlessly to bottom of mobile screen with zero color seams |
| **v4.9.121** | Sep 2026 | Redesign PWA update notification into sleek top-right docked pill with dismiss action |
| **v4.9.120** | Sep 2026 | Add 3-step partner enrollment screener flow before unlocking affiliate dashboard |
| **v4.9.119** | Sep 2026 | End-to-end Affiliate & Referral System: dual-reward engine (+100 AI credits on free signup, 40% commission on paid plans), 6-plan pricing & commission matrix, geolocation-based annual pricing, and official vector share marks (WhatsApp, LinkedIn, X, QR code) |
| **v4.9.118-CRM** | Sep 2026 | Client Management Suite (4 subtabs) & Client Task Manager with Kanban pipeline, right-click command menu, and resizable floating Task Details Drawer with left arc, subtask checklist & multi-industry dictionaries |
| **v4.9.118** | Sep 2026 | Polish high-density Kanban lead card layout with ultra-compact single-row action footer (1-tap WhatsApp, phone, email, stage progression context menu), live column lead counter & deal value synchronization, and release package updates |
| **v4.9.117** | Sep 2026 | Simplify filter dropdown into a clean, independent multi-select popover with dynamic live filter badges, enforce 2 core CRM tabs (Pipeline & Analytics), and optimize real-time card filtering |
| **v4.9.116** | Sep 2026 | Unify toolbar multi-filters, introduce in-column micro-search & context sorting (Deal Value, Recency, Alphabetical), and apply customizable subtle pastel column tints (`bg-sky-50`, `bg-amber-50`, `bg-purple-50`, `bg-emerald-50`) across Kanban stages |
| **v4.9.115** | Sep 2026 | Decision-oriented analytics & customizable top KPI scorecards (Total Pipeline Value, Won Revenue MTD/QTD, Conversion Velocity, Active Opportunities), compact scorecard styling, and KPI selector drawer |
| **v4.9.114** | Sep 2026 | Streamline leads action bar, modernize customize columns drawer into global design system with drag-and-drop column management |
| **v4.9.113** | Sep 2026 | Reorganize Finance into CRM sidebar group and Forms into Workspace Foundation group across all industry modules for unified revenue and pipeline tracking |
| **v4.9.112** | Sep 2026 | Relocate Interactive Calendar into CRM navigation group across all industry modules for seamless shoot, showing, and appointment scheduling |
| **v4.9.111** | Sep 2026 | Standardize leads page header with global workspace header, interactive AI brand stack, and responsive filter trigger |
| **v4.9.110** | Sep 2026 | Establish CRM as an independent first-class sidebar navigation group across all industry modules |
| **v4.9.109** | Sep 2026 | Dynamic Forms to Leads Kanban bridge (instant inbound lead generation), AI Sales Call Synthesizer extracting pain points, deal size, sentiment, and next actions, and AI morning briefing integration |
| **v4.9.108** | Sep 2026 | Hardened mobile drawer sheet lifecycle, optimized mobile team card layout, and responsive touch gestures |
| **v4.9.107** | Sep 2026 | Permanent team member deletion with cascade unassignment and zero orphaned records, modernize active team members mobile card UI, and replace mystery gravatars with high-contrast initials-based SVG avatars |
| **v4.9.106** | Sep 2026 | Dynamic role creation engine, tenant-scoped permission matrix (`tab-roles`, `tab-permissions`), strict workspace owner gatekeeping, and scope workspace locations strictly by tenant |
| **v4.9.105** | Sep 2026 | Desktop & mobile tab customization drawer (`tab-customizer`) with drag-and-drop reordering, and sticky column permissions matrix |
| **v4.9.104** | Sep 2026 | App Modules & Feature Hub comprehensive 14-module foundation matrix across 5 categories with category filter bar, reactive toggles, and navigation route decoupling |
| **v4.9.103** | Sep 2026 | Single consolidated 24-Hour Executive PDF Report delivered strictly once per 24 hours per owner; disarmed repetitive micro-event notification emails (SEO ranking alerts, attendance briefs, check-in pings, 0-task briefings) and routed 100% of micro-events to in-app Bell & PWA Web Push alerts; multi-agency deduplication and rate limit protections |
| **v4.9.102** | Sep 2026 | Purged role simulation preview banner (`#cora-role-preview-banner`) from driver views via server-side checks and guarded JS; minimal topbar with direct sign-out and complete island suppression for field drivers |
| **v4.9.101** | Sep 2026 | Universal driver mode real-time chrome stripping with `.cora-driver-mode-active` body class; dynamically hides global topbar search, notifications bell, profile avatar/popovers, and mobile island navigation; 100% full-screen POS terminal |
| **v4.9.100** | Sep 2026 | Field Sales Driver total terminal isolation; excluded desktop sidebar, top header search (`⌘K`), notifications, profile popovers, and mobile navigation drawers; driver-safe controller actions for Gemini Vision OCR bill scanning and shop visit GPS recording |
| **v4.9.99** | Sep 2026 | Complete Field Driver view isolation, plant inventory removal, mobile island navigation scoped strictly to Home and AI Sparkle, and strictly grounded Driver AI Copilot |
| **v4.9.98** | Sep 2026 | Dedicated Field Sales Driver role (`cora_field_vendor`), 1-step activation & 1-tap Google Sign-Up, server-side route guarding and perspective locking |
| **v4.9.97** | Sep 2026 | Branded van consignment dispatch emails via Hostinger SMTP, zero-cache 1x1 tracking pixel, real-time open status badges (🟢 Opened, 🟡 Sent, ⚪ Pending), WhatsApp brief & direct invite share suite |
| **v4.9.96** | Sep 2026 | Click unlocking & ReferenceError fix during IIFE initialization, bulletproof event delegation across all 84 inventory controller methods |
| **v4.9.95** | Sep 2026 | Sub-millisecond hydration optimization, elimination of trailing template artifacts, full mobile responsive polish |
| **v4.9.94** | Sep 2026 | Variable redeclaration syntax error fix, modal DOM selector alignment, and balanced container tags |
| **v4.9.93** | Sep 2026 | 3-card Dispatch Mobile Drawer with city chips, Top 5 fast-selling auto-suggestions, multi-product catalog picker modal, interactive quantity steppers, safe-area elevation |
| **v4.9.92** | Sep 2026 | Mobile navigation island customizer expanded to all 16 platform modules with real-time search, zero-match empty state, and title-cased preview pills |
| **v4.9.89** | Sep 2026 | Responsive action bar buttons (`whitespace-nowrap`), stock health pill geometry, subtab flex wrapping |
| **v4.9.88** | Sep 2026 | 2x2 mobile KPI scorecard grid (70% vertical scroll savings), purposeful subtle state accents, 3-column mobile SKU cards |
| **v4.9.87** | Sep 2026 | Executive 24h Supply Recon & Daily Audit Engine, multi-route settlement, top SKU rankings, AI diagnostics, standalone print-ready PDF, WhatsApp share studio |
| **v4.9.86** | Sep 2026 | Editable & deletable spot billing invoices and consignments with automated safe stock restoration back to active van or central plant stock |
| **v4.9.85** | Sep 2026 | Clean SVG plus and receipt iconography across inventory views, eliminating dollar sign graphics |
| **v4.9.84** | Sep 2026 | Intelligent Top 5 pre-selected product allocation, instant catalog search dropdown, driver email/phone validation, Google Maps integration & multi-city target coverage |
| **v4.9.83** | Sep 2026 | Permanent Import/Export button visibility in header and catalog toolbar, zero-wrap button formatting |
| **v4.9.82** | Sep 2026 | Context-aware isolated action bars for Plant Inventory vs. Field Van Sales, dual-mode driver selection with directory integration & on-the-fly invites |
| **v4.9.81** | Sep 2026 | Unified full-height Studio Drawer architecture across all inventory modals positioned dynamically below topbar |
| **v4.9.80** | Sep 2026 | Opt-in modal sheet display states, smooth requestAnimationFrame entrance, elimination of ghost pointer interception |
| **v4.9.79** | Sep 2026 | Smart toast placement & dynamic CTA collision avoidance, elevating notifications above open Studio Drawers and primary buttons |
| **v4.9.78** | Sep 2026 | Multi-key FormData handling (`image` and `image_file`) for product photo uploads with instant preview |
| **v4.9.75** | Sep 2026 | 3-step numbered circular stepper, active step scoped styles, enterprise margin telemetry cards (Factory Margin, Retailer Spread, GST Liability, Net Base Price) |
| **v4.9.72** | Sep 2026 | Add/Edit Product 3-step bottom drawer (`Identity & Media` -> `Pricing & Margins` -> `Stock & Logistics`), drag-and-drop media upload, monochromatic delete confirmation |
| **v4.9.63** | Sep 2026 | Dynamic Dashboard Analytics & Mobile Navigation Customizer (14 KPI cards, 3 middle island slots, AJAX persistence) |
| **v4.9.62** | Sep 2026 | Pure clean-slate inventory zero-state, demo database cleanup routine, dynamic DB-backed vendor dashboard, monochromatic standby cards |
| **v4.9.61** | Sep 2026 | Dedicated staging environment app icon with 'STG' ribbon, PWA manifest sync (`CORA Staging`) via `cora_is_staging_env()` |
| **v4.9.60** | Sep 2026 | Stationery Manufacturing & Mobile Van Sales Inventory Engine initial release, single point of control plant command center, mobile field vendor mode, standardized `Inventory & Leads` sidebar grouping |
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

*Cora Platform v4.9.189 — Master Architectural Manual. Last updated: September 2026.*
