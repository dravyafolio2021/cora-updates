# Cora Platform — Comprehensive Platform Documentation

This document serves as the master technical specification and architectural manual for the Cora Workspace Platform (v4.9.387).

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

### 1.12 Mobile GPU Hardware Acceleration & Universal 0ms Instant-Tap Responsiveness (v4.9.207)
* **Hardware Compositing Layers**: Mobile bottom sheets, slide drawers, floating islands, and interactive modals enforce GPU hardware acceleration using `transform: translateZ(0); will-change: transform; backface-visibility: hidden;`. This eliminates rendering repaints, GPU stall latency, and screen tearing during mobile swipe and gesture transitions.
* **Universal 0ms Instant-Tap Standard**: Across all interactive controls (buttons, inputs, select pills, dropdown toggles, bottom sheet triggers), the platform enforces `touch-action: manipulation; -webkit-tap-highlight-color: transparent;`. This eradicates the 300ms mobile touch delay across iOS Safari and Android Chrome, ensuring instantaneous tactile feedback.

---

### 1.13 Desktop Sticky Chat Input Dock Architecture (v4.9.197)
* **Viewport-Locked Flex Layout**: Conversational AI workspaces (such as AI Tools MCP in `views/view-mcp.php`) enforce a full-height flex column layout (`height: calc(100vh - 195px); display: flex; flex-direction: column;`).
* **Sticky Input Dock (`position: sticky; bottom: 0;`)**: Chat message inputs anchor permanently at the bottom of the viewport with a frosted glass backdrop (`backdrop-blur-md bg-white/95 dark:bg-zinc-900/95`) and a subtle top border (`border-t border-zinc-200/80 dark:border-zinc-800/80`).
* **Independent Conversation Scrolling**: The message transcript container occupies `flex-1 overflow-y-auto` with smooth momentum scrolling, preventing input boxes from getting pushed below the fold during extended multi-turn AI interactions.

---

### 1.14 Notion/Linear-Grade Active AI Model Selector Popover Standard (v4.9.195)
* **Header-Anchored Compact Trigger**: In `views/view-mcp.php` and platform headers, the model switcher renders as a sleek, low-profile trigger button (`[ ◆ Gemini 3.5 Flash | Real-time ▾ ]`) using tonal styling (`bg-zinc-100 hover:bg-zinc-200 text-zinc-900 text-xs font-mono font-medium px-2.5 py-1.5 rounded-lg`).
* **Floating Linear-Style Popover**: Clicking the trigger toggles an elevated popover card directly below the header with `z-index: 999` and an invisible backdrop dismiss shield (`#mcp-model-backdrop`).
* **Multi-Provider Tier Selection**: Displays active models categorized cleanly by provider (Google Gemini 3.5 Flash / Pro, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o, and Groq Llama 3.3 70B Ultra-Fast) with model capabilities, context window specs, and real-time active checkmark indicator pills.
* **Drawer Pointer Lock Immunity**: Prevents parent drawer containers from swallowing click events or freezing pointer-events when the popover opens.

---

### 1.15 Cryptographic Email Verification & Route Interception Engine (v4.9.201, v4.9.208)
* **Early Route Interception (`/workspace/verify`)**: In `cora-workspace.php`, `cora_workspace_handle_workspace_route()` intercepts `/workspace/verify` requests at priority `1` before standard framework template redirect and login enforcement fire.
* **32-Byte Cryptographic CSPRNG Entropy (SEC-001 & SEC-006)**: Verification tokens are generated using `bin2hex(random_bytes(32))` and stored as SHA-256 hashes at rest in user meta (`cora_email_verification_token_hash`).
* **Pre-Fetched Link Safety**: Email security scanners (Outlook SafeLinks, Google Defender) pre-fetching the URL are safely handled without prematurely invalidating tokens or producing 403 errors.
* **Dual Meta Flagging & Instant Sign-On**: Upon token validation, the engine sets both `cora_email_verified = 1` and `cora_workspace_email_verified = 1`, initializes authentication cookies via `wp_set_auth_cookie($user_id, true)`, deletes the consumed token hash, and smoothly redirects to `/workspace/dashboard` with a monochromatic welcome toast.
* **Dedicated Standalone Verification Template**: If accessed in an edge-case or unauthenticated state, `views/verify.php` presents a pure light mode, Claude cream-styled status card with automated verification triggers and direct login redirection.

---

### 1.16 Central Tenant Authorization Gatekeeper (`Cora_Authorization`) (v4.9.209 / SEC-002)
* **Central Policy Gatekeeper (`includes/class-cora-authorization.php`)**: Replaces fragmented inline SQL tenancy checks with a unified, centralized authorization policy gatekeeper.
* **Agency Context Assertion (`Cora_Authorization::assert_agency_context($agency_id)`)**: Strict assertion verifying that the currently authenticated user belongs to the requested agency ID before processing data queries or mutations. Throws a standardized `WP_Error('cora_auth_forbidden', ...)` on mismatch.
* **Object-Level Tenant Policy Checks**:
  - `Cora_Authorization::can_access_task($task_id, $agency_id)`: Verifies CRM task ownership.
  - `Cora_Authorization::can_access_form($form_id, $agency_id)`: Verifies Dynamic Form ownership.
  - `Cora_Authorization::can_access_document($doc_id, $agency_id)`: Verifies Document Vault contract ownership.
  - `Cora_Authorization::can_access_theme($theme_id, $agency_id)`: Verifies Canvas Theme ownership.
  - `Cora_Authorization::can_access_media($media_id, $agency_id)`: Verifies Media asset ownership.
* **Central AJAX Assertion Helper**: `cora_assert_agency_context($agency_id)` provides an instant 1-line guard across all AJAX action endpoints, eliminating BOLA (Broken Object Level Authorization) across the entire platform.

---

### 1.17 Outbound HTTP SSRF Protection Filter (`Cora_SSRF_Filter`) (v4.9.209 / SEC-009)
* **Outbound Request Guard (`includes/class-cora-ssrf-filter.php`)**: Protects all server-side outbound HTTP requests (Canvas Website Migrator, Webhook dispatchers, image URL importers, and AI link scrapers) against Server-Side Request Forgery (SSRF).
* **`Cora_SSRF_Filter::is_safe_url($url)` Validation Rules**:
  - **Scheme Whitelist**: Strictly allows `http://` and `https://`. Blocks `file://`, `gopher://`, `dict://`, `ftp://`, etc.
  - **Loopback Blocking**: Resolves host IP and blocks `127.0.0.0/8` and `::1`.
  - **RFC1918 Private Subnet Blocking**: Blocks `10.0.0.0/8`, `172.16.0.0/12`, and `192.168.0.0/16`.
  - **Link-Local & Cloud Metadata Blocking**: Blocks `169.254.0.0/16` and explicit cloud metadata endpoints (`169.254.169.254` for AWS, GCP, Azure, and DigitalOcean metadata services).
  - **DNS Rebinding Guard**: Enforces post-resolution IP checking on the actual resolved destination IP address prior to dispatching `wp_remote_get()` or `wp_remote_post()`.

---

### 1.18 Rule 13: Zero-Outline Tonal Surface Selection & Zero-Dark-Overlay Strict Standard (v4.9.255 - v4.9.300)
* **Zero Heavy Outline Strokes**: High-contrast, solid black/dark bounding outline borders (such as `border-zinc-900`, `border-black`, `border-white`, `border-2`, or `ring-2` on selected cards, lists, or containers) are **strictly forbidden** across the Cora Design System.
* **Tonal Surface Selection Architecture**: Card selection, active states, and focus elements MUST use soft, monochromatic tonal background fills (`bg-zinc-100/90 dark:bg-zinc-800/80` or `bg-zinc-100 dark:bg-zinc-800`) combined with uniform, subtle structural borders (`border-zinc-200/80 dark:border-zinc-800`).
* **Zero Dark Overlay Enclosure**: Modals, slide drawers, and floating panels must eliminate heavy, trapped dark backdrops (`rgba(0,0,0,0.85)`). All dialogs employ clean transparent backdrops or subtle frosted glass blur (`backdrop-blur-sm bg-black/20`), preserving visual connection with the underlying workspace view and preventing trapped mobile overlays.

---

### 1.19 Live Next.js Dev Server Detection & Preview Cookie Isolation Engine (v4.9.316)
* **Local Dev Server Auto-Detection**: When previewing Next.js themes, the system executes a rapid sub-150ms timeout ping to local development ports (`http://localhost:3000`). If the dev server is offline or unreachable, the UI immediately presents a clean offline banner with startup instructions (`npm run dev`) rather than hanging or failing silently.
* **Preview Cookie Isolation (`cora_canvas_preview`)**: The theme preview cookie is strictly scoped to explicit preview query requests (`?preview_canvas=...` or `?cora_preview_theme=...`). Under normal navigation, the cookie is not evaluated, eliminating accidental theme overrides or leaking preview templates onto production customer routes.
* **Next.js Build Proxy Router**: The core router in `cora-workspace.php` intercepts live Next.js build previews, proxying rendered assets and HTML streams directly into the iframe sandbox while eliminating fallback screens or generic welcome pages.

---

### 1.20 Universal Remote MCP Server & OAuth 2.1 PKCE Architecture (RFC 7591 / RFC 9728) (v4.9.220 - v4.9.233)
* **RFC 7591 Dynamic Client Registration**: AI assistants (ChatGPT, Claude Web/Desktop, Google Gemini) can dynamically register client credentials against `/wp-json/cora-oauth/v1/register` without manual developer portal provisioning.
* **OAuth 2.1 PKCE Handshake**: Implements Proof Key for Code Exchange (RFC 7636) with SHA-256 code challenges (`code_challenge_method: S256`), authorization code redemption, and secure refresh token rotation.
* **RFC 9728 Protected Resource Metadata**: Advertises canonical OAuth discovery metadata at `/.well-known/oauth-protected-resource` and returns standards-compliant `WWW-Authenticate` challenge headers on unauthenticated 401 requests.
* **JSON-RPC 2.0 Tool Execution Gateway**: Exposes all platform capabilities (Leads, Deals, Financials, Content, Inventory, Invoicing) via a single unified endpoint (`/wp-json/cora-mcp/v1/rpc`) following the official Model Context Protocol (MCP 2026-07-28 specification) with strict tenant isolation and object-level permissions.

---

### 1.21 Dynamic Super Admin PWA Badging & Role-Scoped Web App Manifest (v4.9.338)
* **Dynamic Administrator Badged Icons**: For platform super administrators (`current_user_can('manage_options')` or `is_super_admin()`), Cora dynamically generates and serves dedicated `ADMIN`-badged icons across all PWA touchpoints:
  - `icon_512_admin.png` (512x512 PWA splash & launcher)
  - `icon_192_admin.png` (192x192 WebAPK home screen icon)
  - `apple-touch-icon-admin.png` (180x180 iOS WebClip)
  - `cora-favicon-admin.png` (64x64 browser tab favicon)
* **Visual Icon Overlay Geometry**: Generated via `scripts/generate_admin_pwa_icons.php`, each icon is dynamically stamped with a high-contrast dark badge (`#09090b` zinc-950) outlined in `#3f3f46` (zinc-700), containing an active emerald status dot (`#10b981`) and bold white `ADMIN` typography.
* **Role-Scoped Web App Manifest (`cora-manifest.json`)**: When accessed by an administrator or via `?admin=1`:
  - `name`: `CORA Workspace (Admin)`
  - `short_name`: `CORA Admin`
  - `id`: `/workspace/dashboard?role=admin`
  - `start_url`: `/workspace/dashboard`
* **Isolated Standalone App Identity**: Prevents Android WebAPK and iOS WebClip identity collisions between regular staff workspace apps and platform administration consoles.

---

### 1.22 Dynamic AI Quota Pacing, Weekly Soft Throttling & Super Admin Bypass (v4.9.364)
* **Standardized Monthly AI Quota Tiers**:
  - **Free / Starter Tier**: 500 AI runs/month (weekly pacing: 125 runs/week)
  - **Pro Tier**: 15,000 AI runs/month (weekly soft-limit pacing: 3,750 runs/week)
  - **Scale Tier**: 25,000 AI runs/month (weekly soft-limit pacing: 6,250 runs/week)
  - **Enterprise Tier**: Custom unlimited AI runs with dedicated tenant compute clusters
* **Weekly Pacing & Anti-Burnout Engine**: Soft throttling calculates `ceil($monthly_quota / 4)` to warn agency owners when weekly consumption exceeds baseline pacing, preventing catastrophic token exhaustion during early billing cycles while permitting burst allocations on critical client deliverables.
* **Super Admin Rate-Limit & Session Unlock**: Users with administrative capabilities (`current_user_can('manage_options')` or Super Admin) completely bypass all rate limits, AI run caps, and artificial 6-hour session timeouts, ensuring continuous 24/7 maintenance operations.
* **Real-Time Quota Telemetry**: AI usage counters are synchronized in real-time across the sticky sidebar admin popover, the AI drawer header progress bar, and the System Settings Suite.

---

### 1.23 Platform-Wide Affiliate & Referral Tracking Suite (Endorsely Integration) (v4.9.364)
* **Global Affiliate Script Integration**: Injected across the Next.js marketing site (`cora-frontend/app/layout.tsx`) and workspace checkout modals via Endorsely's high-speed tracking CDN:
  ```html
  <script 
    src="https://app.endorsely.com/assets/affiliate-tracking.js" 
    data-affiliate-id="d56e72c8-89fa-47cb-bc87-920aa9e3a6bb" 
    defer>
  </script>
  ```
* **Dynamic Payment Attribution Bridge**: On successful Razorpay order verification (`FastRazorpayModal.tsx`, `CheckoutSheet.tsx`), the client fires conversion telemetry to credit referring affiliates:
  ```javascript
  if (window.endorsely && typeof window.endorsely.trackConversion === 'function') {
    window.endorsely.trackConversion({
      orderId: paymentResponse.razorpay_order_id,
      amount: planAmount,
      customerEmail: userEmail
    });
  }
  ```
* **Dual Currency & GST Checkout Engine**: Supports seamless INR (`₹`) and USD (`$`) checkouts, automatic 18% GST calculation (SAC 9983), real-time promo code validation, and instant workspace provisioning upon payment confirmation.

---

### 1.24 Marketing Site Reform & Tools & Templates Discovery Engine (Oct 2026)
* **Repositioned Core Value Proposition**:
  - **Headline**: "Build websites. Run your business, with an AI co-founder."
  - **Subtitle**: "The operating system for Indian service businesses and agencies. Invoicing, lead CRM, team dispatch, client portal, and custom websites — powered by your personal AI co-founder."
* **Dual-Track Conversion Funnel**:
  1. **Primary High-Velocity CTA ("Sign in with Google")**: Instant OAuth registration bypassing manual credential setup, configuring agency tenant databases in under 3 seconds.
  2. **Secondary Discovery CTA ("Explore free templates")**: Links directly to the `/use-cases` directory showcasing 16+ pre-seeded turnkey schemas with interactive live previews.
* **Tools & Templates Architecture**:
  - Overhauled marketing navigation into "Tools & Templates", replacing passive resources with interactive agency utilities.
  - **Free Business Calculators**: Interactive 18% GST invoice simulator, agency retainer target margin calculator, and commercial proposal generator.
  - **Turnkey Production Schemas (16+ Verticals)**: Photography Studios, Real Estate Brokerages, Digital Marketing Agencies, Stationery Manufacturing & Van Sales, Consulting & Professional Services, Wedding Media, Salons & Clinics, and Creator Retainers.
  - **Universal Lead Ingestion**: Embedded forms across static theme landing pages automatically pipe inbound inquiries directly into `wp_cora_leads` with full campaign attribution.

---

### 1.25 Sticky AI SDR Sales Engine & Objection Handling Architecture (Oct 2026)
* **True Viewport Docking via Portal**:
  - Implemented in `HeroAIInput.tsx` via `createPortal(..., document.body)`.
  - Guarantees true viewport-anchored bottom-center docking (`bottom-0 sm:bottom-4`), completely bypassing parent container clipping or relative positioning offsets.
  - **Zero Backdrop Overlay**: The bar floats cleanly above the marketing canvas without dimming background content, ensuring frictionless browsing.
* **Conversational AI SDR & Objection Handling Engine**:
  - High-velocity query parser in `cora-frontend/app/api/ai-preview/route.ts` delivering crisp, query-aligned active voice answers.
  - **Agency Routing Pills**: Instant 1-tap inquiries for Web Design & Dev, Creative & Branding, Performance & SEO, App & Software Dev, and Media & Production.
  - **Pre-Configured Objection Vectors**:
    1. *Notion / HoneyBook vs Cora*: Highlights Cora’s Indian SAC 9983 GST compliance, client proofing vaults, and zero multi-tool subscription sprawl.
    2. *Website Migration*: Explains 100% white-glove migration services for agency client themes and assets.
    3. *Annual Discounts*: Dynamically surfaces conversion coupons (`MIGRATEFREE`, `CORA10`).
    4. *Legacy Multi-Plugin Sprawl*: Details the performance advantages of Cora’s unified single-engine architecture over fragmented 15-plugin stacks.
    5. *AI Run Quotas*: Clarifies the 15,000 (Pro) and 25,000 (Scale) monthly run allowances with anti-burnout pacing.
* **Dynamic Conversion Coupon Attribution**:
  - Generates actionable promotional offer cards directly inside the conversation stream.
  - 1-click CTA buttons (`Claim Free Migration →`, `Claim 10% Off Now →`) automatically redirect to `/pricing/?coupon={code}&plan=pro`, auto-applying the discount token into the checkout state.

---

### 1.26 Multi-Format Frontend Asset & Template Distribution Ecosystem (Oct 2026)
* **Universal 4-Format Asset Directory (`/templates` and `/templates/[slug]`)**:
  - Complete frontend asset repository featuring 16+ production-ready themes and blueprints categorized into 4 distinct distribution formats:
    1. **Native Landing Pages (`native_page`)**: Full-bleed production landing pages built with Next.js 15 and Tailwind CSS. Delivers sub-50ms paint times, accessible ARIA focus rings, and responsive mobile bottom sheets.
    2. **AI Section Prompts (`section_prompts`)**: Battle-tested prompt blueprints engineered for Claude 3.5 Sonnet, Cursor Composer, and ChatGPT, enabling developers to generate pixel-perfect Hero, Bento, Pricing, and Form sections with matching design tokens.
    3. **Elementor Template Kits (`elementor_kit`)**: Downloadable and copyable Elementor JSON blueprints configured with standardized typography tokens and responsive layouts.
    4. **Lovable & React Projects (`lovable_project`)**: Production-ready React project configurations tailored for Lovable.dev and v0.dev rapid full-stack scaffolding.
* **Interactive Live Preview Modal (`TemplatePreviewModal.tsx`)**:
  - Responsive preview modal with viewport controls (`Desktop`, `Tablet`, `Mobile`), full-height iframe sandboxing, and direct clipboard prompt export.
* **Turnkey Schema Models & Lead Bridge**:
  - Pre-seeded operational and website schemas across 16+ agency niches.
  - Forms embedded across static landing pages automatically bridge to `wp_cora_leads` with full campaign and schema tracking.

---

### 1.27 Autonomous AI Workforce & Modular Agent Portfolios (Oct 2026)
* **Specialized Domain AI Workforce**:
  - Integrates 9 dedicated autonomous domain agents across the agency lifecycle:
    1. **Operations Agent**: Crew call sheets, equipment custody, and task dispatch.
    2. **Sales Agent**: Lead qualification, proposal generation, and pipeline acceleration.
    3. **Contracts Agent**: Commercial agreements, model NDAs, and SHA-256 e-sign verification.
    4. **Knowledge Agent**: Living RAG retrieval, agency SOP search, and memory ingestion.
    5. **Creative Agent**: Visual asset curation, brand guideline compliance, and media proofing.
    6. **Delivery Agent**: Client portal links, milestone tracking, and asset handoffs.
    7. **Finance Agent**: 18% GST invoicing (SAC 9983), advance payment links, and GSTR-1 ledgers.
    8. **Legal Agent**: Retainer terms, liability clauses, and dispute prevention.
    9. **Marketing Agent**: SEO optimization, blog draft generation, and multichannel growth.
* **Visual Identity & Design Continuity**:
  - Each agent features high-resolution monochrome-first portrait assets (`cora_agent_*.jpg`), active-voice capability descriptions, and strict system prompt isolation.

---

### 1.28 Cora Forms Cards Overhaul & Universal "All Entries" 5th Tab Architecture (v4.9.372)
Shipped in **v4.9.372**, the Forms module underwent an architectural overhaul eliminating card layout distortions and unlocking global form response management:
* **Form Card Padding & Interactive Surface Overhaul**:
  - Expanded internal padding to `p-5 sm:p-6` with dedicated `p-3.5` inner container for campaign details and `px-3 py-1.5` status chips, preventing any text or badge clipping at card borders.
  - Made the entire `.form-card` surface and title fully clickable, navigating directly to `#edit/${form.id}` without interfering with action buttons.
  - Removed `< 1024px` viewport restrictions in `handleRouting()` and `loadFormIntoEditor()`, unlocking full form editing capabilities on mobile and tablet screens.
  - Resolved `ReferenceError: coraREData is not defined` in the live view handler.
* **In-App Interactive Form Live Preview Modal (`#cora-form-preview-modal`)**:
  - Sandboxed live preview modal allowing agency founders to test forms in real time.
  - Features responsive viewport toggles (`Desktop`, `Mobile`) and a 1-click external tab launcher (`/workspace/forms/embed?id={form_id}`).
* **Universal 5th Top Tab: "All Entries" (`#all-entries`)**:
  - Registered as the 5th tab in `$forms_header_args['tabs']` with dynamic live count badge (`#tab-all-entries-badge`).
  - **Metric Summary Strip**: Real-time KPI counter cards for Total Submissions, Active Forms with Entries, Completion Rate, and Latest Activity.
  - **Notion-Style Filter Toolbar**: Form source selector dropdown, Status dropdown (Completed vs. Partial), live search input, 1-click CSV export, and instant refresh.
  - **Table Layout Hardening**: Implemented `table-fixed` and rigid explicit column widths (`w-14`, `w-48 md:w-56`, `w-auto`, `w-24`, `w-36`, `w-28`, `w-24`) preventing horizontal table blowout from wide text strings.
  - **Backend REST Enhancement**:
    - `cora_rest_get_all_form_submissions` joins `cora_forms` (`SELECT s.*, f.title as form_title, f.form_key, f.settings as form_settings`).
    - Dedicated deletion endpoint: `DELETE /wp-json/cora/v1/forms/submissions/(?P<id>\d+)` with multi-tenant agency context assertions.
  - **Dynamic Multi-Form CSV Export**: Exports filtered submissions across all forms with dynamic field columns parsed from `submitted_data`.

---

### 1.29 Universal Edge-Docked Entry Inspector & Zero-Backdrop Shield Architecture (v4.9.374)
Deployed in **v4.9.374**, the Entry Inspector (`#cora-entry-inspector`) provides an ergonomic detail viewer across both single-form submissions and the universal All Entries registry:
* **Edge-Docked Layout Standard**:
  - **Desktop Viewports (>= 768px)**: Slides smoothly from the right edge (`top-0 right-0 bottom-0 w-[480px] max-w-[90vw] border-l border-zinc-200 bg-white translate-x-0`).
  - **Mobile Viewports (< 768px)**: Slides up from the bottom as a high-density sheet (`inset-x-0 bottom-0 h-[85vh] max-h-[85vh] rounded-t-3xl border-t border-zinc-200 bg-white translate-y-0`).
* **Zero-Backdrop Overlay Clarity**:
  - Completely purged intrusive dark/blurred backdrop overlays. The underlying submissions data table remains 100% visible, sharp, and readable.
  - Integrated outside click listener on the document body to dismiss the inspector seamlessly.
* **Submitter Quick Overview Strip**:
  - Renders avatar initials, primary submitter name, and email/phone quick chips at the top of the inspector panel.
* **Inline Signature & Media Asset Rendering**:
  - Inspects field values for image MIME types and base64 digital signatures (`data:image/...`).
  - Renders inline `<img src="..." class="max-h-32 rounded-lg border border-zinc-200 object-contain bg-zinc-50">` rather than dumping 50,000-character raw base64 string dumps.
* **Sanitized Summary & Sticky Action Footer**:
  - Long JSON blobs and signatures are sanitized in table views as `[Uploaded File / Signature]`, eliminating column stretching.
  - Sticky footer with 1-click "Copy Raw JSON", "Delete Entry", and "Done" action triggers.

---

### 1.30 Client Timezone Engine & Localized Timestamps Architecture (v4.9.374)
* **Automatic Client Timezone Detection**:
  - Automatically detects browser timezone using `Intl.DateTimeFormat().resolvedOptions().timeZone` on client hydration.
  - Converts UTC server timestamps to localized timestamps across submission tables, entry inspectors, and audit logs.
* **Dual-Format Display**:
  - Renders human-readable relative timestamps ("10 minutes ago", "Yesterday at 4:15 PM") paired with full localized ISO tooltips on hover.
  - Ensures accurate operational scheduling for agency teams operating across diverse geographic markets.

---

### 1.31 Dynamic Affiliate Referral Tracking, Coupon Discount Engine & Multi-Tier Checkout Attribution (v4.9.374)
* **Backend Referral Tracking API**:
  - Dedicated REST endpoint: `POST /wp-json/cora/v1/referrals/track` logging impressions, referral links, UTM tags, user agents, and conversion milestones into `wp_cora_referrals`.
  - Frontend proxy route: `cora-frontend/app/api/referral/track/route.ts` bridging public marketing visitors with backend attribution databases.
* **Multi-Cookie & Parameter Ingestion Engine (`cora-frontend/lib/referral.ts`)**:
  - Listens for query parameters: `?cora_ref=...`, `?ref=...`, `?via=...`, `?partner=...`.
  - Stores referral tokens in secure 30-day cookies and `localStorage` fallback.
  - Dispatches automated impression pings on page landing.
* **Automated Checkout Coupon Discount Application**:
  - When users navigate to checkout via an affiliate link, `CheckoutSheet.tsx`, `FastRazorpayModal.tsx`, and `PricingSection.tsx` automatically detect the active referral code.
  - Applies instant 10%–20% coupon discount deductions directly against plan pricing.
  - Displays a clean monochromatic referral savings badge showing partner discount savings.
  - Fires dual attribution hooks upon payment verification: Endorsely script tracking (`window.endorsely?.trackConversion()`) and Cora backend conversion logging.

---

### 1.32 Pricing Architecture, Plan Hierarchy & Bandwidth/Storage Specifications (Oct 2026)
* **Visual Hierarchy & Framer/Webflow Pill Design**:
  - Overhauled plan cards with SaaS hierarchy pills (`Starter`, `Pro`, `Scale`, `Enterprise`).
  - Active-voice card headlines and feature descriptions capped at max 2–3 words per badge.
  - Removed all neon color accents and blue focus rings in strict accordance with Rule 4 monochromatic principles.
* **Explicit Resource Allocations**:
  - Standardized bandwidth and storage specifications across all tiers:
    * **Starter**: 5GB Cloud Storage, 25GB Monthly Bandwidth.
    * **Professional (Pro)**: 15GB Cloud Storage, 100GB Monthly Bandwidth.
    * **Scale**: 50GB Cloud Storage, 500GB Monthly Bandwidth.
    * **Enterprise**: Custom High-Performance Storage & Unmetered Bandwidth.
* **Scale Plan White-Glove Migration Badge**:
  - Floats an external "Free Site Migration" badge above the Scale card frame, converting legacy CMS and site builder users into turnkey agency tenants.

---

### 1.33 Unified Global Resources Hero Section Architecture (Oct 2026)
* **Standardized Header Component (`GlobalResourcesHero.tsx`)**:
  - Unifies the top hero presentation across all 6 platform knowledge directories: Templates (`/templates`), Tools (`/tools`), Guides (`/guides`), Blog (`/blog`), Newsletter (`/newsletter`), and Articles (`/articles`).
* **Visual Continuity & Search Experience**:
  - Consistent editorial typography, subtle monochromatic badges, centered search input with keyboard shortcut hints (`⌘K`), and fluid category pills.
  - Responsive layout scaling cleanly from 320px mobile screens to 4K ultra-wide displays.

---

### 1.34 Partner & Coaching Accelerator Portal Architecture (VNX Digital / Vansh Rajput) (Oct 2026)
* **Dedicated Partner Landing Page (`/vnxdigital`, `/vanshrajput`)**:
  - Deployed co-branded partner landing page for VNX Digital accelerator members.
  - Includes creator social proof, high-converting feature walkthroughs, and bespoke video production assets.
* **Locked 3-Day Trial to Full Coaching Accelerator**:
  - Custom onboarding flow granting exclusive 3-day full-access trial to the Coaching Accelerator and Cora Studio operational suite.

---

### 1.35 Industry Hub Architecture, Multi-Vertical Directory & Slug Migration (`/industries`) (Oct 2026)
* **Canonical Directory Migration (`/use-cases` → `/industries`)**:
  - Successfully migrated the primary industry solutions directory from `/use-cases` to `/industries` across both desktop and mobile routing layers.
  - Implemented 301 redirect rewrites in `.htaccess` and Next.js route mapping, ensuring all legacy `/use-cases` and `/use-cases/[slug]` links resolve transparently with 100% SEO link equity preservation.
* **10+ Verified Industry Verticals with Plain-English Copy**:
  - Overhauled `cora-frontend/lib/industry-data.ts` (1,500+ lines) with high-signal, non-technical plain English and active voice:
    1. **Real Estate & Property Developers** (`/industries/real-estate`): Geocoded property listings, virtual site tour schedules, WhatsApp showing alerts, and client inquiry pipelines.
    2. **Photography & Film Studios** (`/industries/photography-studio`): Full-resolution media proofing, digital client contracts, multi-ratio crop presets, and shoot gear custody registries.
    3. **Architecture & Interior Design** (`/industries/architecture-interior-design`): Material moodboards, revision milestone sign-offs, contractor tenders, and project phase tracking.
    4. **Digital Marketing & Creative Agencies** (`/industries/creative-agency`): Retainer margin calculators, commercial proposals, multi-client campaign tracking, and GST SAC 9983 tax invoicing.
    5. **Consulting & Professional Services** (`/industries/consulting`): Advisory engagement logs, client milestone deliverables, confidential vault contracts, and auto-scheduled call links.
    6. **Salons, Spas & Wellness Clinics** (`/industries/salon-wellness`): Chair/therapist booking slots, customer loyalty memberships, automated reminder WhatsApps, and spot UPI billing.
    7. **Fitness Studios & Personal Trainers** (`/industries/fitness-studio`): Session packs, automated membership renewals, client progress metrics, and trainer shift coordination.
    8. **Event Production & Wedding Management** (`/industries/events-weddings`): Multi-vendor dispatch schedules, client milestone approvals, budget reconciliation, and run-sheet coordination.
    9. **Hospitality & Boutique Restaurants** (`/industries/hospitality`): Table reservation workflows, private event catering agreements, distributor inventory tracking, and daily reconciliation sheets.
    10. **E-Commerce & DTC Brands** (`/industries/ecommerce-dtc`): SKU catalog management, volume discount tiers, factory van sales dispatch, and automated GST billing.
* **Refined Industries Mega-Menu (`Navbar.tsx`)**:
  - Deployed an exact-match multi-column desktop mega-menu with categorized icon tiles, subtle colored pill indicators, and synchronized mobile drawer links.
* **Dynamic Industry Comparison Matrix (`IndustryComparisonTable.tsx`)**:
  - Cross-industry feature matrix comparing module availability, tax workflows, and automation presets across all 10 verticals.

---

### 1.36 Autonomous AI Workforce & 4-Row Infinite Skills Marquee Architecture (`/ai-agent`) (Oct 2026)
* **Dedicated AI Agent Hub (`/ai-agent`)**:
  - Landing architecture showcasing Cora's autonomous multi-agent operating system and dedicated domain agent workforce.
* **High-Resolution Neural Cutout & Blended Canvas**:
  - Integrated high-DPI portrait cutout asset (`cora_hero_agent_cutout.png`) seamlessly blended into the canvas container with responsive gradient lighting and zero rendering seams.
* **4-Row Infinite Skills Marquee (`InfiniteSkillsSection.tsx`)**:
  - High-performance, hardware-accelerated CSS marquee featuring verified platform product capabilities across four specialized operational streams:
    * **Stream 1: Operations & Logistics**: Resource booking, crew dispatch, calendar synchronization, inventory restock alerts, and attendance audits.
    * **Stream 2: CRM & Sales Pipeline**: Lead scoring, WhatsApp Cloud outreach, instant commercial proposals, Kanban stage progression, and AI sales call synthesis.
    * **Stream 3: Finance & Legal Vault**: GST SAC 9983 tax math, spot billing, e-sign SHA-256 audit registries, and 24-hour executive PDF reporting.
    * **Stream 4: Creative & Proofing**: Watermark protection, multi-ratio cropping, client selection telemetry, and 4-pillar digital storage footprint accounting.
* **Automated Asset & Health Check Pipeline (`scripts/deploy_frontend.py`)**:
  - Deployment tooling bundles high-resolution agent portrait assets and verifies live `/api/health` responsiveness prior to traffic release.

---

### 1.37 Features Catalog, High-Density Sidebar Navigation & Lenis Scroll Isolation (`/features`) (Oct 2026)
* **Interactive Capability Directory (`/features`)**:
  - Comprehensive overhaul of the `/features` experience, presenting over 40+ operational modules organized by workflow categories.
* **Compact Single-Line Sidebar Navigation (`FeaturesSidebar.tsx`)**:
  - High-density single-line layout with integrated segmented status filters (*All*, *Released*, *Beta*, *Upcoming*).
  - Search input with zero-overlap vector icon placement and keyboard focus shortcuts.
* **Lenis Smooth-Scroll Boundary Isolation (`data-lenis-prevent`)**:
  - Features sidebar is encapsulated with `data-lenis-prevent` and explicit viewport height boundaries (`max-h-[calc(100vh-120px)] overflow-y-auto`).
  - Isolates inner sidebar mouse-wheel and touch scrolling from the global Lenis smooth-scroll instance, eliminating wheel-scroll trapping and enabling effortless independent navigation.
* **Natural Active-Voice Copywriting & Visual Cards**:
  - Hero and 40+ visual capability cards (`CapabilityVisualCard.tsx`, `ModuleCardVisual.tsx`, `ModuleAppGlyphs.tsx`, `FeatureSecurityTrustBar.tsx`) rewritten into punchy active-voice benefits with a unified "Get Started for Free" CTA.

---

### 1.38 Brand Asset Registry, SVG Vector Download Kit & Design Tokens Reference (`/brand`) (Oct 2026)
* **Enterprise Brand Registry (`/brand`)**:
  - Centralized branding guidelines hub providing production assets and design standards for press, partners, and developers.
* **Turnkey SVG Vector Download Kit (`BrandLogosSection.tsx`)**:
  - Direct 1-click clipboard SVG markup copy and instant `.svg` file download for Primary Wordmark, Inverted Wordmark, and Symbol Mark.
* **Comprehensive Design Tokens Reference**:
  - Neutral Zinc Ramp (`BrandColorsSection.tsx`), Typography Scale (`BrandTypographySection.tsx`), Co-branding Architecture (`BrandCobrandingSection.tsx`), and Clearspace/Misuse Rules (`BrandLogoGuidanceSection.tsx`).
* **Purged Legacy Mockups**:
  - Removed outdated synthetic mockups in favor of authentic production captures adhering strictly to Rule 13 zero-outline design principles.

---

### 1.39 Story Canvas & Chronological Scroll-Driven Journey Timeline Ribbon (`/about`) (Oct 2026)
* **Agency Storytelling Canvas (`/about`)**:
  - Full-width narrative layout communicating the founding mission and agency operating philosophy.
* **Connected Chronological Timeline Ribbon (`ScrollDrivenTimeline.tsx`)**:
  - Continuous vertical milestone rail with unified rail segments, completely eliminating floating tick gaps.
  - Mobile spine layout ensuring uninterrupted responsive narrative flow on 375px+ screens.
* **Structured Bento Architecture**:
  - Core Values bento cards (`CoreValuesSection.tsx`) and dynamic operational week calendar (`DynamicWeekCalendar.tsx`) showcasing the weekly operating rhythm of high-growth agencies.

---

### 1.40 Contact Form Multi-Field Input Validation & Monochromatic State Feedback (`/contact`) (Oct 2026)
* **Robust Multi-Field Validation Engine (`cora-frontend/app/contact/page.tsx`)**:
  - Client-side and server-side validation enforcing strict format requirements across all inquiry fields:
    * **Full Name**: Minimum 2 characters, alpha-numeric and whitespace validation.
    * **Work Email**: RFC 5322 regex validation checking standard mailbox format.
    * **Phone Number**: International phone pattern validation (`/^[0-9+ -]{7,15}$/`).
    * **Company / Studio**: Mandatory entity identification.
    * **Topic Selection**: Categorized routing dropdown (Sales, Enterprise Migration, Partnership, Technical Support).
* **Accessible Monochromatic Feedback**:
  - Inline error notifications, accessible ARIA alert attributes, and top-floating monochromatic toast dispatch (`window.coraShowToast`).

---

### 1.41 Public Documentation Header Polish & Mobile Floating Island Optimization (`/docs`) (Oct 2026)
* **Docs Navigation Polish (`DocsClient.tsx`)**:
  - Streamlined sidebar navigation tabs and status badges to guarantee non-wrapping layout on narrow desktop and tablet displays.
* **Mobile Floating Island Clearance**:
  - Enforced bottom buffer clearance (`pb-28`) and header padding adjustments to prevent article clipping beneath the mobile floating island bar.
* **React Hook Lifecycle Guard (`Navbar.tsx`)**:
  - Fixed early return violation in navigation bars, guaranteeing unconditional React hook execution order across all route transitions.

---

### 1.42 Agency Projects & Portfolio Case Studies Module Architecture (v4.9.381)
The **Agency Projects & Portfolio Case Studies Module** (`modules/projects/class-cora-projects-module.php`, `views/view-projects.php`, and `cora-frontend/app/projects/`) delivers a complete enterprise portfolio showcase engine. It empowers agencies to curate, manage, and publish high-converting transformation case studies with verified metrics, confidentiality NDA controls, and seamless synchronization to the Next.js marketing storefront.

#### Database Schema (`wp_cora_projects`)
The underlying data layer utilizes a dedicated multi-tenant table created via `dbDelta` during module initialization:
```sql
CREATE TABLE wp_cora_projects (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    agency_id varchar(100) NOT NULL DEFAULT 'super',
    title varchar(255) NOT NULL,
    slug varchar(255) NOT NULL,
    subtitle varchar(255) DEFAULT '',
    client_name varchar(255) NOT NULL,
    client_logo_url text DEFAULT NULL,
    client_industry varchar(100) NOT NULL DEFAULT 'SaaS / B2B',
    project_duration varchar(100) DEFAULT '',
    year_completed varchar(20) DEFAULT '',
    services longtext DEFAULT NULL,
    live_url varchar(500) DEFAULT '',
    is_confidential tinyint(1) NOT NULL DEFAULT 0,
    is_featured tinyint(1) NOT NULL DEFAULT 0,
    status varchar(20) NOT NULL DEFAULT 'published',
    executive_summary text DEFAULT NULL,
    challenge_content longtext DEFAULT NULL,
    solution_content longtext DEFAULT NULL,
    deliverables longtext DEFAULT NULL,
    kpis longtext DEFAULT NULL,
    testimonial_quote text DEFAULT NULL,
    testimonial_author varchar(255) DEFAULT '',
    testimonial_role varchar(255) DEFAULT '',
    testimonial_company varchar(255) DEFAULT '',
    testimonial_avatar text DEFAULT NULL,
    testimonial_verified tinyint(1) NOT NULL DEFAULT 1,
    hero_media_type varchar(20) NOT NULL DEFAULT 'image',
    hero_image_url text DEFAULT NULL,
    hero_video_url text DEFAULT NULL,
    gallery_images longtext DEFAULT NULL,
    before_image_url text DEFAULT NULL,
    before_label varchar(100) DEFAULT 'Previous Baseline',
    after_image_url text DEFAULT NULL,
    after_label varchar(100) DEFAULT 'Agency Overhaul',
    video_embed_url text DEFAULT NULL,
    cta_type varchar(50) NOT NULL DEFAULT 'book_audit',
    cta_title varchar(255) DEFAULT 'Ready for Similar Growth Results?',
    cta_subtitle text DEFAULT NULL,
    cta_button_text varchar(100) DEFAULT 'Schedule Strategy Audit',
    cta_button_url varchar(500) DEFAULT '/contact',
    meta_title varchar(255) DEFAULT '',
    meta_description text DEFAULT NULL,
    target_keywords text DEFAULT NULL,
    canonical_url varchar(500) DEFAULT '',
    views_count int(11) NOT NULL DEFAULT 0,
    cta_clicks int(11) NOT NULL DEFAULT 0,
    sort_order int(11) NOT NULL DEFAULT 0,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_slug (slug),
    KEY idx_agency_status (agency_id, status),
    KEY idx_industry (client_industry),
    KEY idx_featured (is_featured)
);
```

#### Core Architectural Capabilities
1. **Status Lifecycle**: Case studies progress through an explicit 3-stage lifecycle: `draft` (internal drafting and staging), `published` (live on Next.js frontend and public API), and `archived` (delisted from indexation while preserving historical telemetry).
2. **Confidentiality & Stealth NDA Masking**: When `is_confidential = 1`, the frontend and API automatically mask sensitive identifiers. Client brand names are replaced with anonymized industry monikers (e.g., *"Confidential Real Estate Enterprise"*), brand logos are redacted, proprietary URLs are hidden, and live demo links are masked, protecting client NDAs while showcasing audited operational outcomes.
3. **High-Impact Numerical KPIs in JetBrains Mono**: Featured metrics (e.g., `+340% Organic Revenue Pipeline`, `4.2x Mobile Conversion Rate`, `< 15s Inquiry Response Time`) are rendered in high-contrast `JetBrains Mono` font (`font-mono font-bold tracking-tight text-zinc-950`). Each KPI pairs a numerical value with operational context and trend direction (`up`, `down`, `neutral`).
4. **4-Act Transformation Narrative Framework**:
   * **Act 1: Executive Summary**: High-level transformation context and headline business impact.
   * **Act 2: Challenge Narrative**: Baseline operational friction, legacy technical bottlenecks, and prior CAC/LTV imbalances.
   * **Act 3: Solution Narrative**: Strategic architecture, custom engineering, and targeted workflows deployed by the agency.
   * **Act 4: Deliverables List**: Concrete inventory of shipped assets, workflows, automations, and digital infrastructure.
5. **Verified Client Testimonials**: Authenticated endorsement cards featuring direct client quotes, executive author credentials (`Aarav Mehta`, `Kavya Patel`, `Rohan Verma`), title, company name, and a verified badge indicator (`testimonial_verified = 1`).
6. **Strategy Audit Conversion CTA Bridge**: Bottom conversion banner featuring customizable triggers (`Schedule Strategy Audit`, `Request Storefront Audit`, `Schedule AI Architecture Session`) funneling qualified high-intent prospects directly to `/contact` with lead attribution.
7. **6-Tab Drawer Editor (`views/view-projects.php`)**: Full-height slide-over drawer on desktop (`w-[640px]`) and bottom slide-up sheet on mobile (`max-h-[88vh] rounded-t-3xl`) adhering strictly to Rule 12:
   * **Tab 1: Overview** (`proj-tab-overview`) — Title, slug, client name, industry, duration, year, confidential toggle, featured toggle, live URL.
   * **Tab 2: Narrative Story** (`proj-tab-narrative`) — 4-act textareas for Executive Summary, Challenge, Solution, and Deliverables list.
   * **Tab 3: Impact KPIs** (`proj-tab-kpis`) — Dynamic KPI matrix editor with values, labels, context, and trend selectors.
   * **Tab 4: Media & Visuals** (`proj-tab-media`) — Hero media type (Image/Video), hero URL, gallery array editor, and Before/After image comparison sliders.
   * **Tab 5: Endorsement** (`proj-tab-testimonial`) — Quote, author name, executive title, company, avatar URL, and verified toggle.
   * **Tab 6: SEO & CTA** (`proj-tab-seo`) — Meta title, description, keywords, conversion CTA type, custom CTA title, button text, and destination URL.
8. **Next.js Storefront Presentation (`cora-frontend/app/projects/`)**: High-performance App Router frontend with dynamic slug routing (`app/projects/[slug]/page.tsx`), static metadata generation, and local fallback data layer (`cora-frontend/lib/projects-data.ts`) hydrating seamlessly from `GET /wp-json/cora-api/v1/projects`.

---

### 1.43 Public Micro-Tools Growth Engine & Canonical Tool Registry (v4.9.381)
The **Public Micro-Tools Growth Engine** is Cora's organic acquisition flywheel, providing utility-first client-side calculators and analyzers that solve concrete agency and studio bottlenecks without registration friction.

#### Canonical Tool Registry (`cora-frontend/lib/tools-canonical-registry.ts`)
The registry acts as the authoritative single source of truth defining **63 verified tools across 10 problem domains**:
1. **Agency Economics** (`agency_economics`): Retainer margin calculators, scope creep estimators, pricing tier modelers.
2. **WordPress Stack** (`wordpress_stack`): Plugin TCO analyzers, database bloat checks, migration cost estimators.
3. **AI Discovery** (`ai_discovery`): AI crawler audit tools, robots.txt validator, LLM search readiness.
4. **Finance & Tax** (`finance_tax`): Indian GST SAC 9983 tax calculators, reverse charge calculators, advance payment estimators.
5. **Contracts & Legal** (`contracts_legal`): Commercial production contract generators, NDA builders, copyright assignment calculators.
6. **Developer & Embed** (`developer_embed`): Iframe embed code generators, webhook payload testers, CORS diagnostic utilities.
7. **PDF Tools** (`pdf_tools`): Client-side PDF compression, merging, watermarking, and OCR extraction.
8. **Sheets Tools** (`sheets_tools`): CSV deduplication, Excel-to-JSON transformers, data hygiene cleaners.
9. **Image Tools** (`image_tools`): Aspect-ratio crop presets, client proofing watermark applicators, EXIF scrubbers.
10. **Real Estate** (`real_estate`): Property showing ROI calculators, commission split modelers, broker scheduling audits.

#### 24-Field Canonical Record Specification
Every registered tool implements a strictly typed schema: `slug`, `publicTitle`, `shortTitle`, `category`, `categoryLabel`, `status`, `owner`, `icp`, `jobToBeDone`, `targetSearchCluster`, `metadataTitle`, `metadataDescription`, `canonicalUrl`, `inputs`, `outputs`, `features`, `methodologyVersion`, `executionMode` (`browser-only` | `remote-audit` | `ai-assisted`), `privacyDisclosure`, `securityRisk`, `relatedTools`, `supportingArticles`, `supportingDocs`, `contextualCoraAction`.

#### Core Framework Components (`cora-frontend/components/tools/`)
* `ToolInputGroup`: Accessible form input matrix supporting currency, percentage, numeric steppers, and range sliders with live client validation.
* `ToolResultSummary`: Immediate visual scorecards, margin meters, loss indicators, and high-contrast numerical highlights.
* `ToolActionBanner`: Contextual conversion banner connecting the free calculation to Cora's workspace platform.
* `ToolFaqAccordion`: Semantic question-and-answer accordions embedded with Schema.org `FAQPage` structured data for rich snippet indexing.
* `ToolBreakdownTable`: Itemized tabular financial ledgers and line-by-line computational models.

#### Zero-PII Analytics Governance (`cora-frontend/lib/tools-analytics.ts`)
The analytics subsystem enforces an uncompromising **Zero-PII Privacy Policy**:
* **Strict Prohibition**: Never tracks submitted domains, client names, email addresses, uploaded file bytes, prompt texts, financial figures, or raw computational results.
* **Sanitized Operational Telemetry**: Emits only generic metadata: `tool_id`, `category`, `execution_mode`, `result_band`, `duration_ms`, `cta_target`.
* **Event Taxonomy**: `tool_viewed`, `tool_started`, `tool_completed`, `tool_result_band`, `tool_report_downloaded`, `tool_related_opened`, `tool_cora_cta_clicked`, `tool_error`.

#### 6 Flagship Growth Tools
1. `wp-plugin-cost-calculator`: Analyzes the true 3-year Total Cost of Ownership of WordPress plugin sprawl, quantifying license renewal inflation, maintenance developer hours, and security patch overhead.
2. `lead-leakage-calculator`: Models inbound pipeline revenue lost to slow response times using empirical speed-to-lead decay benchmarks.
3. `scope-creep-calculator`: Quantifies unbilled agency hours, revision runaway, and profit margin decay per project type.
4. `service-pricing-calculator`: Reverse-engineers required billable rates and team utilization targets to achieve healthy studio margins.
5. `wp-publishing-friction-calculator`: Diagnoses editorial bottlenecks, WYSIWYG lag, and media optimization drag across publishing workflows.
6. `ai-crawler-checker`: Evaluates robots.txt directives and indexation permissions for AI web scrapers (GPTBot, ClaudeBot, PerplexityBot).

#### Problem-to-Workspace Bridge Positioning
The micro-tools funnel operates as a frictionless customer acquisition engine:
```
[ Free Browser Utility ]  --->  Immediate Mathematical Clarity  --->  [ Contextual Cora Action ]
(Zero PII / No Signup)          (Pain Point Quantified)                 (All-in-One OS Bridge)
```

---

### 1.44 Canvas Frontend Themes Engine & GitHub CI/CD Full-Lifecycle Architecture (v4.9.381)
The **Canvas Frontend Themes Engine** (`views/view-canvas.php`, `includes/class-cora-github-integration.php`) merges visual web design, headless Next.js rendering, and automated Git version control into a single unified workspace.

#### Analytical 4-KPI Overview Bar
Located in `tab-canvas-overview`, this consolidated bar provides real-time frontend health metrics:
* **Core Web Vitals Score**: Live mobile performance score with color-coded status badges and target threshold indicators (> 89).
* **Largest Contentful Paint (LCP)**: Paint latency measurement in milliseconds (e.g. `420ms`) with connected/simulated status dots.
* **Themes Library Count**: Live vs Draft theme tally with tenant draft quota meter (e.g., `1 Live · 4 Drafts`).
* **Published Pages & SEO Readiness**: Live published pages count with real-time SEO issue diagnostics (`100% SEO Ready`).

#### Sticky 3-Subtab Navigation Bar
Pinned below the global topbar at ~40px height:
* **Theme Library** (`tab-canvas-overview`): Active live theme preview, draft themes grid, and template creator triggers.
* **Speed & Core Web Vitals** (`tab-canvas-vitals`): In-depth PageSpeed diagnostic panels and mobile loading audits.
* **Migration & Tools** (`tab-canvas-migration`): HTML/CSS crawler and Elementor kit importer.
* **Extreme-Right: GitHub Integration** (`tab-canvas-github`): Edge-anchored subtab displaying live Git synchronization badges.

#### Full-Lifecycle GitHub Integration (`Cora_GitHub_Integration`)
The `Cora_GitHub_Integration` class provides enterprise-grade Git workflows directly from the workspace:
* **OAuth 2.0 Device Authorization Grant**: Implements RFC 8628 Device Flow (`cora_github_initiate_device_flow`, `cora_github_poll_device_token`) allowing users to authorize repositories via `github.com/login/device` with user codes, completely eliminating complex app setup.
* **Personal Access Token (PAT) Storage**: Secure token persistence in user meta and options with Bearer authorization fallback.
* **Dynamic Branch Management**: Branch creation (`cora_github_create_branch`) and branch listing (`cora_github_get_branches`).
* **Automated Pull Request Generation**: `cora_github_create_pr` automatically stages theme changes and creates pull requests against the target base branch with structured commit summaries.
* **PR Review & 1-Click Merge Engine**: `cora_github_get_prs` lists open pull requests, while `cora_github_merge_pr` executes atomic merges directly from the UI supporting `merge`, `squash`, and `rebase` strategies.
* **Commit Rollback & Revert Engine**: `cora_github_revert_commit` generates automated git revert commits to restore previous known-good production states upon error.
* **Full Workspace Synchronization**: `cora_github_sync_entire_workspace` bundles active themes, template assets, and page HTML into an atomic git tree commit.
* **Organization Repository Provisioning**: `cora_github_provision_org_repo` provisions dedicated private repositories under agency GitHub organizations for white-label client projects.

---

### 1.45 Forms to CRM Lead Pipeline Dynamic Automation & Multi-Submission Aggregation (v4.9.381)
The **Forms to CRM Lead Pipeline** bridges inbound lead capture forms with the CRM Sales Pipeline (`views/view-leads.php`) and centralizes entry registries across all tenant forms.

#### Automatic Lead Conversion Engine (`cora_rest_submit_form`)
When an external or embedded form is submitted via `POST /wp-json/cora-api/v1/forms/{id}/submit` or `POST /wp-json/cora/v1/forms/{id}/submit`:
1. **Intelligent Field Extraction**: Automatically maps or detects `name`, `email`, `phone`, `notes`, `budget`, `city`, and `service` across submitted field payloads case-insensitively.
2. **Multi-Tenant Isolation**: Strictly scopes the generated record by `agency_id` and corresponding `branch_id`, preventing cross-tenant lead leaks.
3. **Automated Assignee Routing**: Assigns leads to specified team members or triggers round-robin load balancing across active agency users based on lowest active lead workload.
4. **Predictive Qualification Scoring**: Automatically categorizes inbound leads into `hot`, `warm`, or `cold` based on budget thresholds (>= ₹50,000) and contact completeness.
5. **Real-Time Cache Invalidation**: Calls `cora_db_get_leads(true)` to flush micro-caches and prepends entry to `cora_workspace_leads` option for instantaneous UI reactivity without page refresh.
6. **Self-Learning RAG Ingestion**: Ingests the inbound lead event into `cora_rag_ingest_event`, providing the conversational AI Co-Founder with immediate deal context.

#### Multi-Submission Aggregation REST Endpoint (`GET /wp-json/cora/v1/forms/all-submissions`)
* **Centralized Submissions Repository**: Aggregates form entries across all active forms for the tenant.
* **Unified Query Interface**: Supports pagination (`page`, `per_page`), date range filtering, form ID filtering, and text search across submitted field payloads.
* **Universal 5th Tab Registry**: Powers the "All Entries" registry in `views/view-forms.php`, enabling multi-form search, detail inspection, and consolidated CSV export.
* **Hardened AJAX Controllers**: Lead update, delete, and stage movement controllers feature strict nonce verification, permission checks, and transactional database integrity.

---

### 1.46 Editorial Knowledge Engine & High-Authority Industry Article Library (v4.9.381)
The **Editorial Knowledge Engine** (`cora-frontend/lib/articles-data.ts`) houses a curated library of **15+ high-authority technical guides and teardowns** engineered for Generative Engine Optimization (GEO) and executive readership.

#### 7 Editorial Categorical Domains
1. **Websites, Content & Organic Growth** (`website-content`): Systems for turning static websites into discoverable business engines.
2. **Product & How-To Guides** (`product-guides`): Step-by-step implementation playbooks for workspace workflows.
3. **Studio Operations & Scale** (`studio-operations`): Resource allocation, equipment conflict avoidance, and crew dispatch.
4. **Finance & 18% GST Invoicing** (`finance-gst`): SAC code 9983 tax math, reverse charge mechanisms, and deal margin simulations.
5. **Autonomous AI & MCP** (`ai-automation`): Multi-agent architectures, voice-to-scope processing, and Model Context Protocol integration.
6. **Legal Tech & Contracts** (`legal-contracts`): Indian IT Act 2000 digital signature validity and tamper-evident SHA-256 vault audits.
7. **Product Comparisons & Benchmarks** (`comparisons`): Objective teardowns of legacy vertical SaaS platforms.

#### High-Authority Guide Portfolio
* **WordPress Plugin Stack Cost & TCO**: Deep mathematical teardown of plugin licensing costs, renewal inflation, maintenance developer hours, and security patch overhead.
* **Agency Scope Creep & Project Profitability**: Comprehensive playbook for pricing revisions, change order automation, and protecting studio gross margins.
* **Speed-to-Lead & Response Time Friction**: Statistical analysis of lead decay curves proving why responding in under 15 minutes increases qualification rates by 391%.
* **Autonomous AI Co-Founders for Creative Studios**: Architectural blueprint of autonomous multi-agent systems coordinating daily operations.
* **Voice-to-Scope Multimodal Processing**: Pipeline converting unstructured WhatsApp audio notes into structured client proposals and contracts.
* **Connecting Cursor & Claude to Cora via MCP**: Complete guide to Cora's Universal Model Context Protocol gateway.
* **Generative Engine Optimization (GEO)**: Framework for achieving citation visibility in Perplexity, Claude, and Google Gemini AI Overviews.
* **Indian IT Act 2000 Electronic Contract Validity**: Legal analysis of Section 10A digital contract enforceability in commercial production.
* **Tamper-Evident SHA-256 Digital Contracts**: Cryptographic document hashing, audit trails, and non-repudiation in the Document Vault.
* **Legacy Platform Teardowns**: In-depth competitive teardowns against HoneyBook, Studio Ninja, HubSpot, Dubsado, and Pixieset highlighting Cora's all-in-one architecture.

---

### 1.47 Shopify-Inspired Main-Area Docked View Panel Architecture & Responsive Bounds Sync (v4.9.382 / SOP Rule 15)
Introduced and hardened across **v4.9.382 - v4.9.384**, workspace drawers, editors, and multi-step sheet forms implement the **Shopify-Inspired Main-Area Docked View Panel Standard Operating Procedure** (`views/view-projects.php`, `admin-dashboard.php`):
* **Universal Bottom-Slide Motion (`translateY(0)`)**: All workspace drawers slide up smoothly from the bottom (`translateY(100%)` to `translateY(0)`) into a clean, docked main-area view panel.
* **Direct Below-Header Docking**: Panels dock directly beneath the global top header (`top: var(--cora-main-top, 52px); height: var(--cora-main-height, calc(100vh - 52px)); bottom: 0;`).
* **Dynamic Available Space Sizing**: The panel strictly occupies the exact available space in the main content canvas synchronized in real time with sidebar status:
  - **Sidebar Expanded** (`w-64` / 16rem): `left: 16rem; width: calc(100vw - 16rem); right: 0;`
  - **Sidebar Collapsed** (`w-16` / 4rem): `left: 4rem; width: calc(100vw - 4rem); right: 0;`
  - **Mobile / Off-Canvas** (`< 1024px`): `left: 0; width: 100vw; right: 0;`
  - Bounds dynamically synchronize via `MutationObserver` on `.cora-sidebar` class changes, `resize` window events, and the custom `cora:sidebar-toggle` event listener.
* **Zero Overlay / Zero Backdrop**: No dark dimming, blur overlay, or backdrop element behind the panel (`backdrop: false`). The underlying workspace view remains 100% visible, professional, and integrated.
* **Zero Shadow & Clear Structural Boundaries**: Floating card shadows and rounded corners are strictly prohibited (`box-shadow: none !important; border-radius: 0 !important;`). The panel is demarcated by clear formal architectural borders (`border-left: 1px solid var(--border); border-top: 1px solid var(--border);`).
* **Sidebar Remains 100% Accessible & Visible**: The sidebar is never covered, overlapped, or locked. Users can navigate to any sidebar link at all times.
* **Scoped Main Scroll Lock**: Scroll locking applies strictly to `main.cora-main` (`mainEl.classList.add('overflow-hidden')`), leaving `document.body` and the sidebar completely unlocked.
* **Escape Key Dismissal**: Registers a global `Escape` keyboard event listener to close immediately on press.
* **Level 01 Segmented Pill Tabs**: Multi-tab panels use the Level 01 Segmented Control container: `p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80` with active tab styled as `bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs font-bold rounded-lg px-3 py-1.5`.

---

### 1.48 Shopify-Inspired Canvas Maximize / Restore Toggle (⤢ Fullscreen Mode) (v4.9.382)
In **v4.9.382**, the docked view panel architecture added a 1-click **Canvas Maximize / Restore Toggle** (`coraToggleProjectDrawerMaximize()`) for zero-distraction focus:
* **Header Maximize Trigger**: Dedicated control button (`#cora-proj-maximize-btn`) positioned alongside the close trigger in the drawer header.
* **Fullscreen Canvas Mode (`.maximized`)**:
  - Expands the drawer across the entire viewport width (`left: 0 !important; width: 100vw !important; max-width: 100vw !important; border-left: none !important; z-index: 100060 !important;`).
  - Toggles the SVG action icon between Expand (`⤢`) and Restore (`⤡`).
  - Eliminates distraction during long-form case study authoring, complex transformation metrics entry, and narrative script writing.
* **1-Click Restore**: Clicking restore instantly collapses the panel back to the docked canvas state, recalculating sidebar boundaries via `coraSyncMainAreaBounds()`.

---

### 1.49 Global Sticky Workspace Header & Sub-Tabs Navigation with Mobile Expandable Search (v4.9.383 - v4.9.384)
Hardened in **v4.9.383** and **v4.9.384**, workspace headers and sub-navigation bars implement seamless sticky pinning and responsive mobile search:
* **Global Sticky Header**: The main workspace header pins sticky at `top: 0` (`position: sticky; top: 0; z-index: 100020;`) across both desktop and mobile viewports, ensuring brand identity, quick actions, and profile controls remain instantly accessible during deep vertical page scrolling.
* **Sub-Tabs Extra Actions Alignment**: Standardized `tabs_extra_html` in `workspace-header.php` with `h-full flex items-center gap-1.5 sm:gap-2` container, guaranteeing pixel-perfect vertical alignment between title tabs, search inputs, and view switchers.
* **Level 01 Segmented View Switcher**: Table vs Grid switcher styled with Level 01 Segmented Control container tokens (`h-7 inline-flex items-center p-0.5 bg-zinc-200/60 dark:bg-zinc-800/80 rounded-lg`), providing instantaneous client-side view toggling with active button state caching.
* **Mobile Expandable Search Bar (< 640px)**:
  - On mobile screens, replacing fixed search inputs that cause horizontal overflow with a compact search button trigger (`#cora-proj-search-trigger-btn`).
  - Tapping the trigger expands a full-width overlay input (`#cora-proj-search-wrapper.mobile-expanded`) inside the sub-tabs bar (`top: 0; bottom: 0; left: 0; right: 70px; z-index: 30;`).
  - Includes 1-tap close icon and `Escape` key handler to dismiss the search bar and clear query filters without losing page context.

---

### 1.50 Mobile Sticky Offsets, Sub-Tabs Stacking & Touch Target Sizing (v4.9.385)
Hardened in **v4.9.385**, workspace sub-navigation sticky positioning and mobile touch targets received targeted optimizations:
* **Elimination of Mobile Header Double-Stack Gap**:
  - The runtime calculation in `window.coraUpdateStickyOffsets` (`admin-script.js`) sets `--cora-sticky-tabs-top` directly to the topbar height (`h + 'px'`) while pinning `--cora-mobile-header-height` to `0px`.
  - Guarantees sticky sub-navigation tabs pin seamlessly beneath the top navigation bar with zero layout gap or double-header overlap on mobile viewports.
* **Preservation of Mobile Search Touch Target Sizing**:
  - In `workspace-header.php`, the global `.cora-sub-tabs-extra button` 22px height constraint now explicitly exempts `#cora-proj-search-trigger-btn` and `#cora-proj-search-close-btn` (`:not(#cora-proj-search-trigger-btn):not(#cora-proj-search-close-btn)`).
  - Preserves comfortable 28px (`w-7 h-7`) tap targets for mobile search triggers, ensuring effortless 1-tap engagement on touchscreens.
* **Root Canvas Page Mount Layout (`view-projects.php`)**:
  - Relocated `<div id="cora-projects-module">` to mount directly below the standard workspace header organism with `mt-4`, maintaining consistent vertical canvas rhythm across all viewports.

---

### 1.51 Collapsible Sidebar Navigation Groups & Memory State Persistence (v4.9.386 - v4.9.387)
Hardened in **v4.9.386** and **v4.9.387**, the workspace sidebar navigation received enhanced grouping ergonomics:
* **Interactive Section Collapsibility**:
  - Sidebar group headers (`.cora-nav-group-label`) feature a smooth chevron vector icon (`.cora-group-chevron`) with spring transition (`transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1)`).
  - Tapping/clicking any group header toggles `.is-collapsed`, rotating the chevron 90 degrees (`rotate(-90deg)`) and collapsing the child navigation list (`display: none !important;`).
  - Headers enforce `user-select: none !important;` and clean hover states, preventing text selection during quick collapses.
* **Persistent LocalStorage State Memory**:
  - The toggle state of each navigation group is saved immediately to browser `localStorage` using keys `cora_sidebar_group_{group_id}`.
  - On page load and route changes, collapsed groups hydrate instantly from storage, eliminating layout shift and preserving the operator's preferred workspace density.
* **Instant Auto-Expansion on Search**:
  - When the user focuses the sidebar search input (`#cora-nav-search-input`), the parent navigation container enters `.is-searching` mode.
  - CSS rule `.cora-sidebar-nav.is-searching .cora-nav-group.is-collapsed .cora-nav-list { display: block !important; }` automatically expands all collapsed groups while searching, ensuring every matching item is visible.
  - Clearing the search automatically restores each group's saved collapsed state.
* **Closed-by-Default Information Architecture Standard**:
  - Standardizes accordion controls across complex drawers, settings panels, and inspection views to be closed by default, presenting high-density executive summaries upfront and revealing granular details only upon operator intent.

---

### 1.52 Dynamic Plugins, Modules & AI Skills Extensibility Architecture (v4.9.387)
Introduced in **v4.9.387**, the Cora platform overhauled its modular engine into a multi-dimensional extensibility hub:
* **Level 01 Segmented Control Matrix (`views/view-feature-hub.php`)**:
  - Replaces flat module toggles with a standardized 3-tab segmented control: **Plugins**, **Modules**, and **Skills**.
  - Accessible via semantic paths `/workspace/plugins`, `/workspace/modules`, and `/workspace/skills`.
* **Workspace-Level Isolation & Dedicated Schema**:
  - Backed by isolated multi-tenant tables (`wp_cora_workspace_plugins`, `wp_cora_workspace_skills`, `wp_cora_workspace_api_keys`, and `wp_cora_workspace_plugin_logs`).
  - Plugins and skills belong strictly to the tenant's `agency_id`, preventing cross-tenant leakage.
* **Open Architecture for REST APIs & Model Context Protocol (MCP) 2.0**:
  - Active workspace plugins dynamically inject custom MCP tools into `Cora_MCP_Tool_Registry::get_tools()` via the `cora_mcp_tools` filter hook.
  - Tool names automatically resolve with dynamic prefixes (`cora_plugin_*`, `cora_skill_*`).
* **API & MCP Gateway Drawer**:
  - Provides tenant administrators with self-service API key provisioning (`cora_ws_live_...`), custom RBAC scopes (`plugins:read`, `plugins:write`, `skills:read`, `skills:write`), configurable rate limits, and real-time usage telemetry.
  - Generates live OpenAPI 3.0.1 specifications at `/wp-json/cora/v1/workspace/openapi` for instant agent connection.
* **Multi-Format Ingestion & AI Generation**:
  - **JSON Plugin Manifest**: Ingest and export plugins containing metadata, configuration schemas, and MCP tool definitions.
  - **Antigravity SKILL.md Format**: Upload agent skills formatted with YAML frontmatter (`name`, `skill_slug`, `description`, `trigger_phrases`, `bound_mcp_tools`, `model_preference`) and prompt bodies.
  - **Autonomous AI Generator**: Built-in prompts allowing operators to generate complete, validated plugins or skills directly from natural language operational briefs.

---

### 1.53 Enterprise Client Onboarding Studio & Strict Role Isolation (v4.9.386 - v4.9.387)
Introduced in **v4.9.386** and perfected in **v4.9.387**, client relationship operations were transformed into an enterprise onboarding pipeline:
* **Guided 5-Step Client Creation Studio (`views/view-clients.php`)**:
  - **Step 1: Entity & Contact**: Client name, primary contact person, company name, agency partner name, and engagement type (Direct Client vs Agency Sub-Contract).
  - **Step 2: Commercial & Tax Regimes**: Multi-currency support (INR, USD, EUR, GBP, AED, SGD) with automatic tax handling (Domestic GST 18%, Zero-Rated Export LUT 0%, Custom VAT), GSTIN validation, and customizable milestone payment terms (e.g. 50/50, 25/50/25, Net 30).
  - **Step 3: Production Service Matrix**: Pre-configured services (Commercial Photography, Video Production, Creative Direction, Post-Production CGI, Client Portal Vault, Monthly Retainer) with automatic persistence of custom services to `cora_workspace_services_{agency_id}`.
  - **Step 4: Initial Project Showcase**: Option to simultaneously initialize an active showcase record in `wp_cora_projects`, linking the client to their first portfolio case study.
  - **Step 5: User Account Provisioning & Invitation**: Automatic creation of a dedicated WordPress user account with role `cora_client`, temporary password generation, tokenized portal linkage (`cora_portal_token`), and welcome email delivery with direct login credentials.
* **Strict Client Role Security Isolation (`cora_client`)**:
  - Clients with the `cora_client` role are strictly quarantined from workspace administration interfaces.
  - Server-side route handler in `cora-workspace.php` intercepts any client visiting `/workspace/*` and automatically redirects them to their private portal (`/{workspace}/client-portal?token={portal_token}`).
  - Standard WordPress login redirect (`login_redirect` filter) guarantees clients logging in via `wp-login.php` are seamlessly routed to their personal client portal.
* **CRM Lead Conversion Linkage**:
  - 1-click conversion from CRM pipeline leads (`wp_cora_leads`) automatically marks the lead as `won`, records `converted_to_client = 1`, and synchronizes options caches for zero-latency dashboard reflection.
* **Portal Lifecycle & Access Governance**:
  - Public Client Portal (`public-client-portal.php`) verifies client `portal_status`. If marked as `suspended` or concluded, renders a clean, respectful monochromatic account conclusion screen while preserving historical records in studio registries.

---

### 1.54 Interactive Workspace Scheduling Calendar & Google Meet Standardization (v4.9.386 - v4.9.387)
Deployed in **v4.9.386** and stabilized in **v4.9.387**, workspace scheduling received a full interactive engine in `views/view-calendar.php`:
* **Multi-View Interactive Calendar**:
  - Comprehensive view switcher supporting Month, Week, and Day calendar grids with real-time event markers.
  - Color-coded event categorization adhering to monochromatic neutral tones and subtle functional status accents.
* **2-Way Bidirectional Google Calendar Synchronization**:
  - Real-time two-way synchronization bridge cross-referencing studio bookings, production shoots, and team shifts with external Google Calendar accounts.
  - Automatic conflict guard checking for overlapping appointments before confirming client bookings.
* **Standard 3-4-3 Google Meet URL Format**:
  - Enforces official Google Meet URL syntax (`https://meet.google.com/xxx-yyyy-zzz`), eliminating non-standard meeting slugs.
  - Generates valid virtual consultation links automatically upon booking discovery calls.
* **Public Appointment Booking Flows**:
  - Pre-configured, shareable booking links (e.g. 1-Hour Studio Session, 15-Min Quick Intro Call, Half-Day Editorial Shoot, Portfolio Review Session).
  - Configurable durations, setup/teardown buffers, pricing tags, and location specifications (Physical Bay vs Google Meet).
* **Automated CRM Reflection**:
  - Appointments booked via public links automatically reflect into the CRM sales pipeline as qualified leads, bridging client scheduling directly into revenue forecasting.
* **Shopify-Inspired Docked Drawer Architecture**:
  - Appointment creation and event inspection modals follow SOP Rule 15, docking below the header with sticky non-clipped footers and `Escape` key dismissal.

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

### 2.8 Form AI Architect & Full Lifecycle Engine (v4.9.175 - v4.9.374)
* **Full CRUD Lifecycle**: Comprehensive form builder supporting draft staging, publishing, editing, field reordering, and safe deletion without page reloads.
* **15 Standardized Field Types**: Text, Long Text (Textarea), Numeric Phone, Email, Number, Dropdown Select, Multi-Select, Checkbox Group, Radio Buttons, Date Picker, Time Slot, File Dropzone, Star Rating, NPS Score (0-10), and Signature Pad.
* **Multi-Step Wizard Engine**: Supports multi-page forms with animated progress steppers, step validation barriers, and dynamic conditional branching.
* **Form Card Padding & Ergonomics Overhaul (v4.9.372)**:
  - Redesigned form cards with expanded internal padding (`p-5 sm:p-6`), dedicated `p-3.5` inner container for campaign info, and `px-3 py-1.5` status chips, preventing text from colliding with borders.
  - Made the entire `.form-card` and title clickable to open `#edit/${form.id}` directly without interfering with action buttons.
  - Unlocked mobile editing by removing `< 1024px` viewport blockers in `handleRouting()` and `loadFormIntoEditor()`.
* **In-App Interactive Form Live Preview Modal (`#cora-form-preview-modal`)**:
  - Sandboxed live preview modal allowing instant real-time form testing with responsive viewport toggles (`Desktop`, `Mobile`) and a 1-click external tab launcher (`/workspace/forms/embed?id={form_id}`).
* **Universal 5th Top Tab: "All Entries" Registry (`#all-entries`) (v4.9.372)**:
  - 5th tab registered in `$forms_header_args['tabs']` with dynamic live count badge (`#tab-all-entries-badge`).
  - **Metric Summary Strip**: Real-time KPI counter cards for Total Submissions, Active Forms with Entries, Completion Rate, and Latest Activity.
  - **Notion-Style Filter Toolbar**: Form source selector dropdown, Status dropdown (Completed vs. Partial), live search input, 1-click CSV export, and refresh button.
  - **Table Layout Hardening**: Implemented `table-fixed` and rigid explicit column widths (`w-14`, `w-48 md:w-56`, `w-auto`, `w-24`, `w-36`, `w-28`, `w-24`) eliminating horizontal table blowout.
  - **Submissions REST API**:
    - `GET /wp-json/cora/v1/forms/all-submissions`: Returns all agency submissions with form title, form key, and settings JOINed.
    - `DELETE /wp-json/cora/v1/forms/submissions/{id}`: Secure REST deletion endpoint with multi-tenant tenancy verification and custom `coraConfirmAction` modal.
* **Universal Edge-Docked Entry Inspector & Zero-Backdrop Architecture (v4.9.374)**:
  - Universal inspector panel (`#cora-entry-inspector`) shared across single-form drawers and the All Entries registry.
  - Desktop right-edge docking (`top-0 right-0 bottom-0 w-[480px] border-l`) and mobile bottom slide-up (`inset-x-0 bottom-0 h-[85vh] rounded-t-3xl border-t`).
  - Zero-backdrop overlay: completely eliminated dark backdrop blur, leaving the underlying data table 100% visible and sharp. Dismissible via outside click.
  - Submitter quick overview strip (Avatar initials, Name, Email/Phone).
  - Inline signature & image rendering (`<img src="...">`) replacing 50k base64 string dumps.
  - Sticky footer with Copy Raw JSON, Delete, and Done actions.
* **Client Timezone Engine (v4.9.374)**:
  - Automatically detects browser timezone (`Intl.DateTimeFormat().resolvedOptions().timeZone`) and localized timestamps into human-readable relative ("2 hours ago") and absolute dates.
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

### 2.11 App Modules & Extensibility Engine (Plugins, Modules, Skills & MCP 2.0) (v4.9.104 - v4.9.387)
Located in `views/view-feature-hub.php` and powered by `includes/class-cora-plugins-skills-engine.php`, the Feature Hub provides tenant-level feature governance, dynamic modular extension, and open Model Context Protocol (MCP) 2.0 execution:

* **Level 01 Segmented Control Extensibility Matrix**:
  1. **Plugins (`/workspace/plugins`)**: Dynamic workspace-level extensions, third-party webhook dispatchers, payment gateway listeners, and remote MCP tool bridges.
  2. **Modules (`/workspace/modules`)**: The 24 Core Foundation & Domain Modules toggle matrix across 5 functional categories with strict 6 Foundation Modules locking.
  3. **Skills (`/workspace/skills`)**: Custom AI Agent skills with behavioral system prompts, input parameter JSON schemas, model preferences (Gemini 3.5 Flash, Claude 3.5 Sonnet, GPT-4o), and bound MCP tools.
* **Core Foundation Modules Permanent Locking Architecture (v4.9.189, v4.9.200)**:
  - The Cora platform enforces a permanent architecture lock on **6 foundational operational modules**: `blogs`, `forms`, `team-roles`, `media`, `vault`, and `affiliates` (alongside `dashboard`).
  - **Universal Domain Hardening**: Across all 6 industry domain class files (`class-custom-module.php`, `class-manufacturing-inventory-module.php`, `class-marketing-agency-module.php`, `class-studio-module.php`, `class-professional-services-module.php`, `class-re-module.php`), these foundation modules are permanently declared active and immutable.
  - **Tenant Capability Micro-Guard**: In `cora-workspace.php`, `cora_get_custom_enabled_features()` automatically merges foundation features into the enabled tenant feature array, ensuring core capabilities remain available even if tenant meta has outdated entries.
  - **Feature Hub Immutable UI**: Locked foundation cards display an immutable `Foundation` badge with a clean lock vector SVG and disabled toggle switches.
  - **Batch Operation Immunity**: Global actions (*Select All*, *Deselect All*, *Reset Defaults*, *Discard*) preserve the active state of foundation modules, preventing tenants from disabling essential infrastructure.
* **API & MCP Gateway Drawer (`#cora-api-gateway-drawer`)**:
  - High-density administrative drawer for generating, inspecting, and revoking tenant-scoped API keys (`cora_ws_live_...`).
  - Granular RBAC API Scopes: `workspace:read`, `plugins:read`, `plugins:write`, `skills:read`, `skills:write`, `webhooks:read`, `webhooks:write`.
  - Configurable rate limits (default 1,000 req/hr) and telemetry audit logging (`wp_cora_workspace_plugin_logs`).
  - Real-time OpenAPI 3.0.1 schema generation at `/wp-json/cora/v1/workspace/openapi` and dynamic MCP tool resolution.
* **Multi-Format Plugin & Skill Ingestion**:
  - **Visual Creation Drawer**: Clean UI forms for authoring webhooks, custom bridges, or agent behaviors.
  - **JSON Manifest & MCP Import/Export**: Standardized JSON manifests for one-click installation and export.
  - **Antigravity SKILL.md Format**: Native support for importing Anthropic/Google Antigravity skills with YAML frontmatter.
  - **Autonomous AI Generator**: In-drawer prompt builder allowing operators to generate complete, validated code or skill instructions from natural language briefs.
* **24-Module Compact Mobile Grid**: Re-architected mobile layout into a compact single-column horizontal card list (`flex-row items-center gap-3 p-3`), eliminating excessive vertical scrolling and visual clutter on mobile screens.
* **Unified Search & Control Bar**: Global search input coupled with live status filtering (*All*, *Active Only*, *Inactive Only*) and dynamic industry preset tags.
* **Rule 13 Zero-Outline Tonal Selection Compliance**: Active and selected states strictly use soft monochromatic tonal background fills (`bg-zinc-100/90 dark:bg-zinc-800/80`) with subtle borders (`border-zinc-200/80`), completely eliminating heavy black outlines, `ring-2`, and dark border strokes.
* **Strict Navigation Decoupling**: Enabling or disabling any module automatically mounts or unmounts its corresponding navigation item from the sidebar and mobile island nav, preventing ghost routes or 404 dead ends.
* **Explicit Save & Staging Workflow**: Toggle modifications stage cleanly in memory and commit atomically via AJAX to `cora_agency_modules_{agency_id}`.
* **Batch Controls**: 1-click "Enable All Recommended", "Deselect All", and "Reset to Industry Defaults".

### 2.12 Users, Team Governance & Dynamic Role Engine (v4.9.104 - v4.9.108, v4.9.180 - v4.9.186, v4.9.190)
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

#### 8. User & People Ops Specialized AI Copilot (v4.9.190)
* **Dedicated People Ops Architect**: In `views/view-users.php`, Cora AI is grounded with real-time agency team rosters, role distribution, pending invitations, and individual token consumption telemetry.
* **Executable Machine Action Tags**: Parses and executes team actions directly from conversational prompts:
  - `[ACTION:invite_member {"name": "...", "email": "...", "role": "..."}]`: Pre-populates and triggers new member invitation.
  - `[ACTION:edit_member_role {"user_id": ..., "role": "..."}]`: Adjusts member permissions or roles safely.
  - `[ACTION:open_team_migration]`: Opens the multimodal OCR roster scanner drawer.
  - `[ACTION:open_permissions_matrix]`: Switches tab to the granular RBAC capability matrix.
  - `[ACTION:filter_members {"status": "..."}]`: Filters directory by active, pending, or custom roles.
  - `[ACTION:resend_invite {"invite_id": ...}]`: Re-dispatches invitation link.
  - `[ACTION:check_ai_quota]`: Telemetry breakdown of team AI token utilization.
* **Rule 3 Name Privacy Sanitization**: Grounding prompts strictly use fictitious generic names (`Rohan Verma`, `Kavya Patel`, `Aarav Mehta`) and scrub any personal identifiers.

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
   - Registered in the role store as "Field Sales Driver".
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

### 2.18 Client Management Suite, Enterprise Onboarding Studio & Task Manager (v4.9.119 - v4.9.387)
The **Client Management Suite** (`views/view-clients.php`) and **Client Task Manager** (`views/view-client-task-manager.php`, `views/partials/partial-clients-kanban-tasks.php`) provide a unified CRM operations platform tailored to agencies and service studios:

```
+------------------------------------------------------------------------------------------------------+
|                     CLIENT MANAGEMENT SUITE & ENTERPRISE ONBOARDING STUDIO (CRM)                     |
+----------------------+-----------------------------+------------------------------+------------------+
| 4-Subtab Client Hub  | Guided 5-Step Creator Studio| Client Tasks Kanban Pipeline | Strict Isolation |
| • Directory Overview | • 1. Entity & Contact       | • Stage Progression Columns  | • Role: cora_client
| • Active Deals/Vault | • 2. Multi-Currency / Tax   | • In-Column Counter & Deals  | • Zero /workspace
| • Client Tasks Board | • 3. Service Catalog Matrix | • Multi-Filter Popover       | • Auto Portal Redir
| • Ledger Recon Sync  | • 4. Initial Project Linked | • Context Command Menu       | • Welcome Email  |
|                      | • 5. User Account & Password| • Checklist, Scope, Assets   |   & Credentials  |
+----------------------+-----------------------------+------------------------------+------------------+
```

1. **Guided 5-Step Client Onboarding Studio (`views/view-clients.php`)**:
   - **Step 1: Entity & Contact Profile**: Captures primary contact person, client firm name, optional agency partner name, and engagement classification (Direct Client vs Agency Sub-Contract).
   - **Step 2: International Tax Regimes & Commercial Terms**:
     - **Multi-Currency Support**: Full localization across INR (`₹`), USD (`$`), EUR (`€`), GBP (`£`), AED (`AED`), and SGD (`S$`).
     - **Tax Regimes**: Supports Domestic GST (18% SAC 9983), Zero-Rated Export LUT (0% under Letter of Undertaking for foreign clients), and Custom VAT.
     - **GSTIN Validation**: Enforces standardized 15-character alphanumeric GSTIN formatting for Indian corporate clients.
     - **Milestone Payment Terms**: Configurable terms (100% Upfront, 50% Advance / 50% Delivery, 25% Advance / 50% Wrap / 25% Delivery, Net 15, Net 30, or Custom Retainer).
   - **Step 3: Production Service Matrix & Dynamic Catalog**:
     - Pre-populated standard services (*Commercial Photography*, *Video Production*, *Creative Direction*, *Post-Production CGI*, *Client Portal Vault*, *Monthly Retainer*).
     - Allows on-the-fly authoring of custom workspace production services, automatically persisting them to the agency's catalog option (`cora_workspace_services_{agency_id}`) for future reuse.
   - **Step 4: Multi-Project Showcase Linking**:
     - Offers simultaneous initialization of an active project case study in `wp_cora_projects`, automatically linking client identity, deliverables, and industry.
   - **Step 5: User Account Provisioning & Invitation**:
     - Automatically creates a secure WordPress user account with role `cora_client`.
     - Generates cryptographically secure temporary passwords and binds metadata (`cora_portal_token`, `cora_workspace_slug`, `cora_agency_id`, `cora_client_id`).
     - Dispatches a professional transactional welcome email with direct portal link and credentials.
2. **Strict Client Role Security Isolation (`cora_client`)**:
   - Users with the `cora_client` role are quarantined strictly to their personalized portal.
   - Server-side route handler in `cora-workspace.php` intercepts clients attempting to access `/workspace/*` (dashboard, financials, leads, settings) and immediately redirects them to `/{workspace}/client-portal?token={portal_token}`.
   - WordPress `login_redirect` hook seamlessly directs `cora_client` logins directly into their private portal.
3. **CRM Lead Conversion Linkage**:
   - 1-click conversion from CRM pipeline leads (`wp_cora_leads`) automatically marks the lead as `won`, records `converted_to_client = 1`, and synchronizes options caches for zero-latency dashboard reflection.
4. **Client Task Manager (`views/view-client-task-manager.php`)**:
   - High-density Kanban board columns with live task counters and aggregated deal values.
   - Smooth drag-and-drop and 1-tap stage progression across industry lifecycle stages.
   - **Multi-Filter Popover**: Filter by status, assigned team member, priority level, or client account with zero emojis.
   - **Right-Click Context Command Menu (`#cora-task-context-menu`)**: Quick action popover enabling 1-click stage advancement, priority reassignment, member delegation, and task deletion.
   - **Multi-Industry Dictionaries**: Industry-specific stage terminology (`photography_studio`, `real_estate`, `marketing_agency`, `stationery_inventory`, `professional_services`).
5. **Resizable Floating Task Details Drawer**:
   - Floating ergonomic panel anchored 48px below the top navigation bar (`top: 48px`).
   - Distinctive left-edge arc (`rounded-l-2xl`) and ambient drop-shadow (`shadow-2xl`).
   - Zero dark backdrop overlay (`backdrop: none`), allowing full visibility of underlying board columns during active inspection.
   - Drag-to-Resize Left Handle (`#cora-task-drawer-resize-handle`, `window.coraInitTaskDrawerResize`) with persisted width in `localStorage`.
   - **4 Dedicated In-Drawer Tabs**: `Checklist`, `Scope`, `Assets`, and `Activity`.

---

### 2.19 Public White-Labeled Client Portal (`public-client-portal.php`)
The **Public Client Portal** delivers a branded, mobile-first, passwordless experience for agency and studio clients:
1. **White-Labeled Client Routing & Governance**:
   - Tokenized URLs (`/{slug}/client-portal?token=cora_clt_*`) allow clients to review deliverables, project progress, and invoices securely without requiring internal user accounts.
   - **Portal Access Governance**: The portal inspects `$client['portal_status']`. If marked as `suspended` or concluded, it renders an elegant, respectful account conclusion screen (`Portal Access Suspended • Account Concluded`), preserving historical invoices and documents in studio registries while preventing active deliverable modification.
2. **Claude Cream Design Aesthetic**:
   - Strict adherence to the Anthropic Claude design theme: warm cream background (`#FBFaf7`), minimal container cards with thin border strokes (`#E8E5DE`), and high-contrast typography.
3. **Active Projects & Deliverables Showcase**:
   - Embedded showcase cards for client projects, displaying completed deliverables, verified KPI proof tags, external live links (`Live Showcase ↗`), and direct triggers to open the Proofing Vault.
4. **Proofing Vault & Deliverables Review**:
   - High-resolution gallery preview, client approval triggers, revision notes input, and batch asset downloads.
5. **Official Vector Payment Gateways (India & Global)**:
   - Integrated payment options for instant settlement.
   - Uses official vector SVG brand marks for Indian payment rails: **WhatsApp Pay**, **Google Pay**, **PhonePe**, and **Paytm**, alongside international Stripe/Card gateways.

---

### 2.20 Affiliate & Referral Ecosystem (v4.9.115 - v4.9.121, v4.9.200 - v4.9.206)
Located in `views/view-affiliate-referrals.php` and powered by `includes/affiliate-referral-engine.php`, the Affiliate & Referral Ecosystem delivers an enterprise growth and partner incentive engine:
1. **Permanent Workspace Foundation Placement (v4.9.200)**:
   - Permanently locked as a foundation module alongside `blogs`, `forms`, `team-roles`, `media`, and `vault` across all 6 industry vertical classes.
   - Dynamic alphanumeric sidebar badges displaying active partner rank and pending earnings.
2. **Gamified Partner Dashboard & Earnings Meter (v4.9.202)**:
   - Dynamic tier progression meter tracking lifetime referrals: **Bronze** (0–9 referrals, 10% commission), **Silver** (10–49 referrals, 15% commission), **Gold** (50–99 referrals, 20% commission), and **Diamond** (100+ referrals, 25–30% commission).
   - Dynamic streak multiplier badge (`⚡ 3-Streak Active (+5% Bonus)`), rewarding partners who maintain active monthly referral velocity.
   - Top 3 Podium Leaderboard cards (🥇 Gold, 🥈 Silver, 🥉 Bronze) highlighting top-performing agency partners, accompanied by a dedicated 'Your Studio Ranking' highlight card.
3. **Structured 5-Subtab Sticky Navigation (v4.9.202, v4.9.204)**:
   - Edge-to-edge sticky sub-navigation tabs bar:
     1. `Overview & Meter`: Gamified progress meter, 4 telemetry scorecards, streak multiplier, and quick link share tools.
     2. `Leaderboard & Ranks`: Top 3 podium, tiered partner rankings, and conversion velocity scorecards.
     3. `Earnings Simulator`: Interactive dynamic payout calculator modeling monthly vs. annual subscription commissions across volume sliders.
     4. `Referrals & Logs`: Real-time referral activity log with attribution status, plan tier, and commission ledger.
     5. `Payouts & Banking`: Monochromatic withdrawal drawer, UPI/IMPS bank transfer details, and payout history.
4. **Overhauled Partner Reward & Incentive Structure (v4.9.203)**:
   - **Free Signups**: +100 Free AI Runes awarded immediately to both the referrer and the referee upon verified email registration.
   - **Recurring Commission**: 20% recurring monthly commission or 30% recurring annual commission on all active subscriptions.
   - **Milestone Cash Bonuses**: Guaranteed lump-sum milestone payouts: 10 Referrals → ₹1,000, 50 Referrals → ₹5,000, 100 Referrals → ₹10,000.
5. **Streamlined Partner Onboarding Screener (v4.9.206)**:
   - Streamlined 3-step partner screener with 3 punchy revenue pillars and progressive disclosure accordions, reducing enrollment drop-off while qualifying promotional channels.
6. **Rule 4 & Rule 13 Monochromatic Polish (v4.9.204)**:
   - Completely purged neon styling, harsh contrast outlines, and emoji decorations in favor of soft monochromatic tonal fills (`bg-zinc-100 dark:bg-zinc-800`), clean 1.8px vector SVGs, and responsive single-column mobile card feeds.
7. **Dynamic Affiliate Referral Tracking API & Client Attribution (v4.9.374)**:
   - Dedicated REST endpoint: `POST /wp-json/cora/v1/referrals/track` logging impressions, referral links, UTM tags, user agents, and conversion milestones into `wp_cora_referrals`.
   - Frontend proxy route: `cora-frontend/app/api/referral/track/route.ts` bridging public marketing visitors with backend attribution databases.
   - Client tracking library (`cora-frontend/lib/referral.ts`) capturing URL query parameters (`cora_ref`, `ref`, `via`, `partner`), persisting to 30-day cookies and `localStorage` fallback.
8. **Automated Checkout Coupon Discount Engine (v4.9.374)**:
   - When users navigate to checkout via an affiliate link, `CheckoutSheet.tsx`, `FastRazorpayModal.tsx`, and `PricingSection.tsx` automatically detect the active referral code.
   - Applies instant 10%–20% coupon discount deductions directly against plan pricing.
   - Displays a clean monochromatic referral savings badge showing partner discount savings.
   - Fires dual attribution hooks upon payment verification: Endorsely script tracking (`window.endorsely?.trackConversion()`) and Cora backend conversion logging.

---

### 2.21 System Settings Suite & AI Agent Controller (v4.9.179, v4.9.198)
Located in `views/view-settings-suite.php` and accessed via `/workspace/settings`, the System Settings Suite centralizes workspace-level governance, integrations, and communications:
* **Persistent Notification Channels & Trigger Matrix (v4.9.179)**:
  - Toggles for delivery channels: **Email (Hostinger SMTP Relay)**, **WhatsApp (Meta Cloud API)**, and **In-App / Web Push Alerts**.
  - Event-level trigger toggles: *Lead Inbound Capture*, *Task Due & Assignment Alerts*, *E-Sign Contract Signatures*, *Attendance Punch Logs*, and *Financial Invoice Settlements*.
  - **AJAX State Persistence (`cora_ajax_save_notification_settings`)**: Channel toggles and event matrix checkboxes automatically persist to tenant options atomically via AJAX, ensuring toggle settings survive browser reloads and device switches.
* **Full AI Agent Management & Synchronized Control (v4.9.198)**:
  - Trained Cora AI as Principal Systems Architect with real-time RAG context across all 12 settings modules (Branding, Notifications, Integrations, Backups, Caching, Security Audit, Localization, Storage Quotas).
  - Executable machine action tags:
    - `[ACTION:update_settings {"section": "...", "data": {...}}]`: Programmatically updates configuration parameters.
    - `[ACTION:switch_settings_tab {"tab": "..."}]`: Navigates to target settings tab.
    - `[ACTION:trigger_backup]`: Dispatches on-demand workspace backup routine.
    - `[ACTION:clear_system_cache]`: Purges micro-caches and transient options.
    - `[ACTION:check_platform_updates]`: Triggers heartbeat version check.
    - `[ACTION:view_activity_logs]`: Streams administrative audit trails.
  - Direct live DOM synchronization updating input values and toggle switches in real-time as AI executes changes.
  - Strict Rule 3 privacy sanitization ensuring all placeholder and grounding data remain fictitious.
* **Sticky Full-Width Sub-Navigation Tabs**:
  - Sub-navigation tabs (*General*, *Notifications*, *Integrations*, *Security & Audit*) adhere to the standardized ~36px height, pinning directly below the global topbar on scroll.
  - Features `touch-action: pan-x` smooth horizontal touch scrolling on mobile devices, with zero heavy outlines or focus rings.

---

### 2.22 Media Management AI Copilot & Floating Mobile Action Dock (v4.9.191 - v4.9.192)
Located in `views/view-media.php`, the Media Management module provides enterprise asset management, proofing telemetry, and automated AI assistance:
* **Chief Creative Director & Media Asset Architect AI (v4.9.191)**:
  - Cora AI is grounded with real-time knowledge of workspace storage consumption, file counts, folder hierarchies, proofing download logs, and telemetry views.
  - Direct action execution tags:
    - `[ACTION:upload_media]`: Triggers the upload dropzone modal.
    - `[ACTION:create_folder {"name": "..."}]`: Creates a structured media collection folder.
    - `[ACTION:share_media {"file_id": ..., "expires_in": 7}]`: Generates white-labeled proofing link.
    - `[ACTION:filter_media {"type": "..."}]`: Filters gallery by images, videos, documents, or raw assets.
    - `[ACTION:inspect_media_telemetry {"file_id": ...}]`: Displays view/download audit logs.
    - `[ACTION:view_storage_breakdown]`: Renders multi-dimensional digital footprint visualizer.
* **Floating Monochromatic Mobile Action Dock (v4.9.192)**:
  - On mobile screens, secondary action buttons are consolidated into a sleek floating action bar (`#cm-mobile-bottom-bar`).
  - Positioned cleanly above the mobile island navigation bar (`bottom: calc(76px + env(safe-area-inset-bottom))`) with 160px bottom viewport padding (`pb-40`), preventing content occlusion and navigation tap overlap.

---

### 2.23 AI Tools MCP Developer Gateway & Live Voice Assistant Stage (v4.9.193 - v4.9.197)
Located in `views/view-mcp.php`, the AI Tools module delivers an advanced Model Context Protocol developer gateway and live conversational voice environment:
* **Dual Mode AI Executive Co-Founder (v4.9.193)**:
  - **Mode A: Interactive Text Chat**: Full-featured chat interface with generative cards, code snippets, and sticky input dock.
  - **Mode B: Live Voice Assistant Stage**: Central animated pulsing sphere orb (`.cora-voice-sphere`), soundwave waveform bars, Web Speech API speech-to-text recognition, and ElevenLabs neural voice synthesis delivering sub-200ms spoken duplex dialog.
* **3 Unified Sticky Sub-Navigation Tabs (v4.9.194)**:
  - `AI Assistant & Voice`: Dual-mode conversational copilot and voice stage.
  - `MCP Developer Gateway`: Model Context Protocol server registration, tool inspection, schema explorer, and request testing harness.
  - `Living Memory RAG`: Tenant-scoped knowledge domain vectors, documents ingestion, memory search, and semantic indexing.
* **Notion/Linear-Grade Model Selector Popover (v4.9.195)**:
  - Header-anchored trigger (`[ ◆ Gemini 3.5 Flash | Real-time ▾ ]`) toggling an elevated popover with zero-outline tonal styling across Google Gemini, Anthropic Claude, OpenAI GPT-4o, and Groq Ultra-Fast.
* **Zero-Outline Monochromatic Design Compliance (v4.9.196)**:
  - Strictly adheres to Rule 13 across RAG vector lists and MCP tool tables, replacing borders with soft tonal fills (`bg-zinc-100/90 dark:bg-zinc-800/80`).
* **Desktop Sticky Chat Input Dock (v4.9.197)**:
  - Full-height flex column layout (`calc(100vh - 195px)`) with message input permanently pinned at `bottom: 0` with frosted glass backdrop.

---

### 2.24 Canvas Themes UI/UX Revamp & Speed Diagnostic Suite (v4.9.199)
Located in `views/view-canvas.php`, the Canvas Theme management interface has been overhauled for streamlined operations and speed optimization:
* **Single-Stream Unified Theme Library (`#tab-canvas-overview`)**:
  - Merged separate Live Theme and Draft Theme views into a unified single-stream Theme Library, displaying active deployment badges, page counts, last edited timestamps, and 1-click publishing actions.
* **4-Card Speed & Core Web Vitals Diagnostic Strip**:
  - Displays high-density diagnostic metrics:
    1. *Core Web Vitals Performance Score* (e.g. 98/100 Mobile Speed).
    2. *Largest Contentful Paint (LCP)* benchmark (e.g. 1.2s High Velocity).
    3. *Themes Quota & Storage* utilization.
    4. *Live Published Pages* counter.
  - Interactive 1-click `Optimize Now →` recommendation banner suggesting automated WebP compression, lazy loading, and script deferral.
* **Edge-to-Edge Frosted Sticky Navigation Bar**:
  - Sub-navigation tabs span edge-to-edge with frosted glass backdrop blur and zero margin clipping.
* **Viewable-Only Mobile Mode**:
  - On mobile viewports (< 768px), heavy visual builder iframes and migrator tools are gracefully replaced with a lightweight viewable-only theme preview mode, preventing mobile browser memory crashes.

---

### 2.25 High-Density Email Communications Suite (v4.9.199)
Located in `views/view-emails.php` and connected to the Hostinger SMTP relay:
* **High-Density Metric Scorecards**:
  - Responsive 2x2 mobile grid and 1x4 desktop row displaying: *Total Dispatched Emails*, *Average Open Rate %*, *Click-Through Rate %*, and *Bounce / Deliverability Rate*.
* **Dynamic Cross-Module CRM Contacts Synchronization**:
  - Real-time recipient synchronization pulling active client accounts from `wp_cora_clients` and active inbound leads from `wp_cora_leads`, enabling 1-click targeted outreach without manual contact exports.
* **Pre-Configured Industry Email Templates**:
  - Ready-to-send templates tailored per industry (Photography Shoot Delivery, Real Estate Showing Confirmation, Stationery Consignment Dispatch Brief, Agency Milestone Sign-Off).

---

### 2.26 Van Dispatch Wizard, Weight Allocation & Spot Billing Engine (v4.9.314 - v4.9.316)
The **Inventory & Mobile Van Sales Engine** (`includes/class-cora-inventory-engine.php` and `views/view-inventory-management.php`) was substantially upgraded with an advanced field distribution and spot billing subsystem:
* **3-Step Guided Dispatch Wizard**:
  1. *Step 1: Driver & Route Selection*: Select from verified active workspace drivers (scoped to role `cora_field_vendor`) or provision new field members on-the-fly with automated email dispatch. Configure vehicle registration numbers and target delivery routes with quick city chips.
  2. *Step 2: Inventory & Weight Allocation*: Select catalog items with support for both piece-count and weighted bulk goods. Implements dynamic weight rate allocation (standard base: ₹401.25/kg) and calculates total dispatch weights and expected gross valuations in real time.
  3. *Step 3: Review & Dispatch Confirmation*: Displays comprehensive consignment summary, generates the initial cargo manifest, and triggers transactional dispatch emails with real-time tracking pixels.
* **Real-Time Spot Billing & Field Point-of-Sale (POS)**:
  - Mobile drivers execute counter billing at retail shops directly from allocated in-van stock.
  - Automatically calculates applicable GST splits (CGST + SGST for intra-state, IGST for inter-state), converts bill amounts into official Indian currency words via `Cora_Inventory_Engine::convert_number_to_indian_words()`, and formats bills with standardized 4-digit numeric identifiers (e.g. `#0001`, `#0015`).
  - **Auto-Print GST Invoice**: Once confirmed, spot sales can immediately trigger `ajax_render_sale_invoice()` which streams a clean printable tax invoice with automated client-side print execution (`window.print()`).
  - **Safe Stock Restoration Protocol**: Deleting or modifying a spot invoice safely rolls back stock debits, returning items atomically to active van stock without orphaned units.

---

### 2.27 Itemized Sales Register, CSV Export & Dispatch Manifest Auto-Print (v4.9.314 - v4.9.315)
To satisfy rigorous accounting and statutory audit standards for wholesale distribution:
* **Canonical Sales Register Table (`#sales-register-table`)**:
  - Central ledger tracking every field transaction with 4-digit bill numbers, sale date, customer/firm name, itemized products sold, payment modes (Cash, UPI, Retailer Credit), and total net amount.
  - Interactive search bar, date range filtering, driver selector, and payment mode breakdown cards.
* **Itemized CSV Export (`ajax_export_sales_csv`)**:
  - Exports a comprehensive, auditor-ready spreadsheet breakdown containing Bill #, Date, Customer Name, Contact, Product Name, SKU, Quantity, Unit/Weight Rate, Subtotal, GST, and Grand Total.
* **Printable Sales Ledger (`ajax_render_sales_register_pdf`)**:
  - Clean, monochrome-formatted printable HTML ledger formatted for statutory accountant packs and physical filing.
* **Van Stock Sheet & Dispatch Manifest Print (`ajax_render_van_stock_sheet`, `ajax_export_van_stock_csv`)**:
  - Generates official warehouse dispatch manifests formatted in strict 4-column (Inventory Report) and 5-column (Dispatch Manifest) structures:
    - *Dispatch Columns*: `Sr No`, `Item Name`, `Pages per Unit / Spec`, `Weight (kg)`, `Total Price (₹)`.
  - Includes warehouse supervisor signature lines, vehicle verification blocks, and driver acceptance acknowledgments.

---

### 2.28 Edge-to-Edge Draggable Lead Drawer & Bi-Directional Synchronizer (v4.9.300 - v4.9.302)
Located in `views/view-leads.php` and `assets/js/admin-script.js`:
* **Edge-to-Edge Full-Height Drawer Layout**:
  - The Lead Details Drawer is auto-portaled directly to `document.body`, eliminating parent container clipping and CSS stacking context issues.
  - Operates with a clean transparent backdrop or subtle frosted blur, keeping the underlying Kanban pipeline context visible.
* **Left-Edge Draggable Width Resizer**:
  - Users can smoothly resize the drawer by dragging its left edge (`.cora-drawer-resize-handle`).
  - Drawer width preference is automatically persisted to `localStorage` (`cora_lead_drawer_width`), restoring the user's preferred layout instantly on subsequent openings.
* **Decluttered Collapsible Accordion Anatomy**:
  - Detailed lead information is organized into clean, monochromatic accordion sections: *Lead Details*, *Contact Information*, *AI Intent Score & Call Briefing*, *Deal Valuation & Stage*, and *Activity Timeline*.
* **Team Assignee Roster Sanitization & Bi-Directional Sync**:
  - Restricts lead assignment dropdowns strictly to valid, verified agency team members, filtering out system accounts and inactive users.
  - Updates made inside the drawer (status progression, stage change, notes, deal value) immediately synchronize bi-directionally with the corresponding Kanban board card and top KPI scorecards without requiring a full page refresh.

---

### 2.29 GST Invoicing Multi-Step Validation & Automated Document Vault Linkage (v4.9.303 - v4.9.304)
Located in `views/view-financials.php` and `cora-workspace.php`:
* **Multi-Step Creator Validation Engine**:
  - Enforces rigorous field validation across client selection, 15-character GSTIN regex validation (`^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-Z]{1}Z[0-9A-Z]{1}$`), invoice date, due date, SAC/HSN codes, and positive line item quantities.
  - Structured line items are safely serialized as JSON arrays with itemized rates, tax rates (9% CGST + 9% SGST or 18% IGST), and automated discount computations.
* **Automated Document Vault Synchronization**:
  - When a GST invoice is created or published, the system automatically registers a corresponding legal record in the Document Vault (`wp_cora_vault_documents`).
  - Computes a tamper-evident SHA-256 cryptographic checksum of the invoice payload, tags the document with e-sign and payment readiness metadata, and links the vault document ID directly to the financial invoice record for end-to-end statutory audit trails.

---

### 2.30 Media Manager Centered Modals & Curated Color Tag Presets (v4.9.295 - v4.9.298)
Located in `views/view-media.php`:
* **Centered Pop-Up Modal Architecture**:
  - Quick management actions (such as *New Folder Creation* and *Folder Settings & Renaming*) are engineered as centered pop-up modals (`#cora-media-folder-modal`) rather than heavy side drawers, aligning with natural mental models for modal dialogs.
  - Features transparent blurred backdrops, keyboard escape dismissal, and automatic focus management.
* **Curated Color Tag Presets & Custom Hex Color Picker**:
  - Folders support visual categorization via a palette of curated monochromatic and subtle pastel color tags (`zinc`, `slate`, `neutral`, `emerald`, `sky`, `amber`, `rose`).
  - Includes an integrated native hex color picker for custom brand alignment, persisting color preferences in `wp_cora_media_folders`.
* **Edge-to-Edge Asset Inspector Drawer**:
  - High-resolution asset inspection, EXIF metadata review, and public proofing link generation operate within edge-to-edge side drawers with zero dark backdrop overlays.

---

### 2.31 Tokenized Administration Email Verification & Security State Machine (v4.9.269 - v4.9.273)
Located in `views/view-settings-suite.php` and `cora-workspace.php`:
* **Dynamic Administration Email Status Badges**:
  - Displays dynamic visual state badges: 🟢 **Verified Administration Email** vs. 🟡 **Verification Pending** with relative timestamp expiration indicators.
* **Locked Security State Machine**:
  - Administration email inputs remain in a locked read-only state to prevent accidental modifications or unauthorized account takeovers.
  - Clicking "Change Email" reveals an expanded input flow with cancel/confirm safeguards.
* **Tokenized Verification Dispatch & Authenticated Sender Relay**:
  - Triggers a secure 24-hour expiration token dispatched via the authenticated sender address (`heycora@claraverse.in`).
  - Verification links safely intercept via `/workspace/verify?token=...` or AJAX validation handlers, completing identity verification without cross-subdomain authentication failures.
* **100 Daily AI Credits Quota Matrix**:
  - Automatically provisions new signups with a Free Workspace tier enforcing an official quota limit of 100 daily AI credits, synchronized in real time with the topbar quota badge and admin popover.

---

### 2.32 Universal Remote MCP Server & AI Connected Tools (v4.9.220 - v4.9.233)
Located in `includes/mcp/class-cora-universal-mcp-server.php`, `class-cora-oauth-server.php`, and `views/view-mcp.php`:
* **Universal Remote MCP Endpoint**:
  - Connects external AI assistants (ChatGPT Custom GPTs, Claude Web & Desktop, Google Gemini, Cursor IDE) directly to the agency's live Cora Workspace via JSON-RPC 2.0 at `/wp-json/cora-mcp/v1/rpc`.
* **OAuth 2.1 PKCE Authentication**:
  - Provides a complete RFC 7591 dynamic client registration endpoint (`/wp-json/cora-oauth/v1/register`) and interactive authorization consent screen (`/workspace/oauth/authorize`).
* **Canonical Tool Registry Across Workspace Domains**:
  - Exposes 25+ structured tools spanning CRM Leads (`cora_crm_create_lead`, `cora_crm_get_leads`), Finance & Invoicing (`cora_finance_create_invoice`, `cora_finance_get_financials`), Inventory (`cora_inventory_get_catalog`, `cora_inventory_create_consignment`), and Content Marketing (`cora_content_create_article`).
* **1-Click Assistant Setup Matrix**:
  - The AI Tools MCP view provides ready-to-copy setup instructions, prompt directives, and live connection test buttons for ChatGPT, Claude, and Gemini.

---

### 2.33 Dynamic Plugins & AI Skills Runtime Engine (v4.9.387)
Implemented in `includes/class-cora-plugins-skills-engine.php` and managed in `views/view-feature-hub.php`:
* **Multi-Tenant Extensibility Architecture**:
  - Provides tenant-isolated database tables (`wp_cora_workspace_plugins`, `wp_cora_workspace_skills`, `wp_cora_workspace_api_keys`, `wp_cora_workspace_plugin_logs`) scoped strictly by `agency_id`.
  - Supports custom plugins, external webhooks, MCP tool bridges, and AI agent prompt skills with parameter validation.
* **Dynamic MCP Tool Registration & Filter**:
  - Automatically hooks into `cora_mcp_tools` filter in `Cora_MCP_Tool_Registry::get_tools()`.
  - Active workspace plugins inject their defined MCP tools into Cora's Universal MCP Server dynamically without code modifications.
  - Normalized tool name resolution handles `skill_*` and `plugin_*` prefixes seamlessly (`cora_skill_*`, `cora_plugin_*`).
* **Pre-Seeded Starter Plugins & AI Skills**:
  - **Starter Plugins**: `webhook-dispatcher` (real-time HTTP POST webhooks to Zapier, Make, Slack), `google-calendar-sync` (2-way calendar sync bridge), `payment-gateway-listener` (Stripe/Razorpay webhook ingestion clearing GST SAC 9983 invoices), `client-portal-notifier` (milestone updates), and `daily-ledger-reconciler` (daily bank feed audit).
  - **Starter AI Skills**: `client-intake-specialist` (production scope extraction), `gst-invoice-auditor` (18% GST SAC 9983 validation), `high-ticket-lead-qualifier` (deal sizing and intent scoring), `shoot-gear-readiness-auditor` (equipment checklist verification), and `portfolio-storyteller` (4-act narrative generation).
* **Workspace Scoped API Keys (`cora_ws_live_...`)**:
  - Self-service API key provisioning with custom cryptographic SHA-256 hashing at rest.
  - Configurable RBAC permission scopes (`plugins:read`, `plugins:write`, `skills:read`, `skills:write`, `workspace:read`) and per-key rate limits (default 1,000 req/hr).
* **Multi-Format Upload & Ingestion**:
  - Supports standard **JSON Plugin Manifests** and **Antigravity SKILL.md** files (YAML frontmatter + Markdown instruction prompt).
  - Built-in AI generation endpoints (`/workspace/ai/generate-plugin`, `/workspace/ai/generate-skill`) enabling natural language prompt-to-code creation.
* **Audit Logging & Telemetry Engine**:
  - Every skill execution, webhook dispatch, and API key authentication records an audit log entry in `wp_cora_workspace_plugin_logs` with payload details, caller model, and execution status.

---

### 2.34 Interactive Workspace Calendar & Google Scheduling Engine (v4.9.386 - v4.9.387)
Implemented in `views/view-calendar.php`:
* **Multi-View Interactive Calendar (Month, Week, Day)**:
  - Interactive grid switcher supporting full Month, Week, and Day calendar views with responsive event rendering.
  - Pure monochromatic Notion/Shopify styling with subtle functional status indicators.
* **2-Way Bidirectional Google Calendar Synchronization**:
  - Real-time synchronization bridge cross-referencing studio bookings, production shoots, and team shifts with external Google Calendar accounts.
  - Built-in conflict guard preventing overlapping booking requests.
* **Standard 3-4-3 Google Meet Syntax**:
  - Enforces official Google Meet URL format (`https://meet.google.com/xxx-yyyy-zzz`), eliminating arbitrary meeting slugs.
  - Automatically attaches valid Google Meet consultation links to video discovery appointments.
* **Public Appointment Booking Flows**:
  - Shareable client booking links (1-Hour Studio Session, 15-Min Quick Intro Call, Half-Day Editorial Shoot, Portfolio Review & Retouching Session).
  - Configurable durations, buffer times, pricing tags, and meeting location scopes (Physical Studio Bay vs Google Meet).
* **Direct CRM Reflection**:
  - Bookings made via public flows automatically log into `wp_cora_leads` as qualified CRM leads, linking calendar appointments directly to pipeline deal stages.
* **Shopify-Inspired Docked Drawer Architecture**:
  - Follows SOP Rule 15 with full docked view panel below the header, sticky non-clipped footers, and `Escape` key dismissal.

---

### 2.35 Agency Projects & Portfolio Case Studies Production Suite (v4.9.381 - v4.9.385)
Implemented in `views/view-projects.php` and `modules/projects/class-cora-projects-module.php`:
* **Dedicated Multi-Tenant Schema (`wp_cora_projects`)**:
  - 46-column database table capturing project metadata, title, slug, client name, industry, services, status (published, draft, archived), featured flags, and confidentiality settings.
* **4-Act Case Study Storytelling Framework**:
  - **Act 1: Background & Strategic Challenge**: Executive summary and organizational friction.
  - **Act 2: Strategic Intervention**: Creative and operational solution narrative.
  - **Act 3: Execution & Deliverables**: Structured deliverable breakdown list.
  - **Act 4: Verified ROI Metrics & Proof KPIs**: High-contrast numerical proof rendered in `JetBrains Mono` font.
* **Dual-Surface Integration**:
  - Synchronizes seamlessly with the Next.js marketing frontend at `cora-frontend/app/projects/[slug]`.
  - Links directly into the Client Management Suite (`views/view-clients.php`), Public Client Portal (`public-client-portal.php`), and Proofing Vault.
* **Confidentiality / Stealth NDA Masking (`is_confidential = 1`)**:
  - Automatically redacts brand names and logos for sensitive client engagements, replacing identities with generic industry monikers without compromising verified outcome metrics.
* **Shopify-Inspired Docked Drawer Editor**:
  - 6-tab editing drawer (Overview, Narrative Story, Impact KPIs, Media & Visuals, Endorsement, SEO & CTA) with maximize / restore toggle (`⤢`), responsive sidebar sync, and zero backdrop overlay.

---

## Section 3: Canvas Theme Builder, Multi-Engine Architecture & Next.js Headless System (v4.9.38 - v4.9.357)

Canvas is a **Multi-Engine Website & Theme Engineering Platform**, supporting Elementor white-labeled editing, modern Visual HTML Canvas, 1-click site ingestion, high-performance headless Next.js Git repository integration, and AI-assisted prompt template code generation:

```
+-----------------------------------------------------------------------------------------------------------------------+
|                                              CORA CANVAS THEME BUILDER                                                |
+----------------------+-----------------------------+------------------------------+---------------+-------------------+
| Engine A: Elementor  | Engine B: Visual HTML Canvas| Engine C: Universal Migrator | Engine D:     | Engine E: AI HTML |
| • Reskinned Sandbox  | • Live Sandboxed Iframe     | • Multi-Page URL Crawler     | Next.js Git   | & Tailwind Grid   |
| • Strips WP Branding | • Inline contenteditable    | • CSS/JS/Asset Localizer     | • App Router  | • 6 Pro Prompts   |
| • 2-Row Top Toolbar  | • Code-Split Visual Editor  | • DOM Sanitizer & Cleaner    | • Route Scan  | • 1-Click Claude  |
| • Git Commit / Sync  | • Media Swap & Scanner      | • Auto Draft Theme Generator | • Static Site |   & GPT Copy      |
|                      | • Clean HTML Export         |                              |   Compiler    | • 10-Min Live POS |
+----------------------+-----------------------------+------------------------------+---------------+-------------------+
```

### 3.1 Multi-Engine Theme Architecture
1. **Elementor Engine**: Wraps Elementor in a sandboxed, white-labeled two-row toolbar (`cora-elementor-reskin.js` and `.css`), completely hiding native headers, admin bars, upsells, and promo banners.
2. **Visual HTML Canvas Engine**: Directly renders clean, semantic HTML inside `#cora-html-canvas-iframe`, providing instant client-side editing without heavy builder overhead.
3. **Universal Website Migrator Engine**: Ingests, cleanses, and converts existing external sites into customizable workspace themes.
4. **Next.js & Expo Headless Static Engine (`class-cora-nextjs-integration.php`)**: Connects production Next.js and Expo React Native Web repositories via Git, ingests discovered App Router routes, executes automated static compilation, and coordinates on-demand ISR revalidations and live dev server previews.
5. **AI HTML & Tailwind Starter Engine (`cora_canvas_get_default_starter_html`)**: Delivers curated, production-ready landing page prompts for Claude & ChatGPT, dual-mode code ingestion (paste / file dropzone), and instant live static website deployment.

### 3.2 In-Browser Visual HTML Editor & Code Split Engine
* **Inline `contenteditable` Editing**: Click any heading, paragraph, or label inside the iframe to edit text in-place with real-time focus outline highlights (`.cora-editing-active`).
* **Code-Split Visual Editor**: Split view displaying live visual canvas alongside source HTML code editor with instant bidirectional synchronization.
* **URL Edit State Persistence**: Automatically maintains active edit state in browser URL parameters (`?page_id={id}&edit_mode=visual`), enabling lossless browser reloads and deep linking.
* **Media Asset Inventory Scanner (`renderHtmlInventoryList`)**: Automatically inspects the loaded page, extracts all `<img>` tags, and displays a thumbnail gallery in the editor sidebar.
* **1-Click Image Replacement Modal**: Selecting "Swap" on any detected media asset triggers `#cora-image-replacer-popover`, enabling instant image swapping from the Cora Media Library or external URLs.
* **Clean HTML Serialization Engine (`getCleanIframeHtml`)**: Before saving, clones the DOM and strips temporary editor attributes (`contenteditable`, `.cora-editing-active`), returning pure HTML5 code.
* **Atomic Save & Publish (`cora_ajax_save_html_visual`)**: Commits clean HTML directly to the database and syncs mapped page content.

### 3.3 Universal Website (HTML/CSS/JS) Multi-Page Migrator Engine (v4.9.58 - v4.9.59)
The Universal Website Migrator (`class-cora-html-website-migrator.php`) allows agencies to ingest, scrape, and migrate any existing public website into Cora Canvas with a single click:
* **Multi-Page Domain Crawler**: Recursively discovers internal links matching the root domain up to configurable crawl depths (1-5 levels) and page limits (up to 25 pages).
* **Asset Isolation & Downloader**: Parses all external and relative CSS stylesheets, JavaScript files, fonts, and images, downloading them to localized workspace media folders and rewriting HTML paths.
* **HTML Sanitization & Sandboxing**: Strips third-party analytics trackers, malicious scripts, and external ads while preserving core layout styles, CSS variables, and interactive JavaScript.
* **Draft Theme & Page Generation**: Automatically provisions a new draft theme in `wp_cora_canvas_themes` and scaffolds all crawled pages inside `wp_cora_canvas_pages`.
* **Instant Visual Canvas Editing**: Migrated pages immediately open in the In-Browser Visual HTML Editor for live inline text modifications, image replacements, and AI copy adjustments.
* **Streamlined Monochromatic Migration Modal**: Provides URL input, scan depth controls, page limit sliders, live crawling progress indicators, and atomic error handling.

### 3.4 Next.js Headless Git Repository Integration, Route Scanner & Build Preview Proxy (v4.9.308 - v4.9.316)
Engineered in `includes/class-cora-nextjs-integration.php` and `cora-workspace.php`:
* **GitHub Git Connector Wizard**: Connects Next.js web repositories via GitHub Personal Access Tokens (PAT) supporting both public and private repositories with dual authorization header fallbacks (`token <pat>` and `Bearer <pat>`).
* **App Router Route Scanner (`cora_nextjs_scan_routes`)**: Recursively scans the GitHub tree for Next.js App Router entry points (`app/**/page.tsx`, `page.jsx`, `page.js`), automatically ingesting discovered pages into Canvas theme registries.
* **Headless REST API Layer**: Delivers manifest metadata (`/manifest`), single page props (`/page`), ISR revalidation webhooks (`/revalidate`), and form submission ingestion (`/form-submit`).
* **Live Next.js Dev Server Detection & Preview Proxy**: Sub-150ms timeout ping detects local development servers (`http://localhost:3000`), presenting offline directives when inactive and isolating preview cookies (`cora_canvas_preview`) strictly to explicit preview requests.
* **Level 2 Live Customizer Integration**: Direct pen icon quick-actions in sidebar navigation launch the Level 2 live theme customizer editor without intermediate clicks.

### 3.5 AI-Powered Canvas Tools
* **AI Element Rewriting (`cora_ajax_canvas_ai_rewrite_element`)**: Context-aware re-drafting of headlines, body paragraphs, and CTAs across various tones (*Punchy*, *Professional*, *High-Converting*, *Minimal*).
* **AI Core Web Vitals & SEO Optimizer (`cora_ajax_canvas_ai_optimize_page`)**: Automated page audit diagnosing LCP, CLS, and FID metrics, injecting optimized image tags, meta tags, and structured data.

### 3.6 Add Theme Wizard UI Overhaul: Active-Voice 2x2 Grid & Production Templates (v4.9.344 - v4.9.345)
Overhauled to deliver a frictionless, high-velocity onboarding experience for agency websites:
* **Active-Voice 2x2 Selection Grid**: Replaces passive cards with four explicit, action-oriented engineering paths:
  1. **Build with AI Prompts**: Generate instantaneous HTML/Tailwind landing pages with calibrated AI prompts for Claude & ChatGPT, dual-mode code connector, and 1-click live launch.
  2. **Connect Headless Code**: Link production Next.js and Expo React Native Web Git repositories with dynamic route discovery and automated static site compilation.
  3. **Scrape & Migrate Website**: 1-click multi-page crawler that ingests any live URL, downloads and isolates assets, sanitizes DOM code, and provisions editable draft themes.
  4. **White-Labeled Visual Builder**: Launch sandboxed visual drag-and-drop builder in an isolated workspace with a custom two-row monochromatic toolbar and zero CMS admin chrome.
* **Edge-to-Edge Sticky Bottom Bar**: Modal actions anchor permanently to the bottom of the viewport (`sticky bottom-0 bg-white border-t border-zinc-200`) with high-contrast `Cancel` and `Continue` buttons, preventing layout shifts or obscured CTA triggers on smaller laptop screens.
* **Real-Time Template Search Filter**: In Step 2, a responsive search input dynamically filters template cards by name and description without reloading or DOM destruction.
* **6 Production Template Presets with Realistic Preview Assets**: Step 2 includes 6 pre-built, production-calibrated industry layouts accompanied by high-fidelity screenshot assets (`assets/img/tmpl_*`):
  - **Creative Agency & Video Production Studio** (`tmpl_agency_preview.jpg`): High-contrast portfolio, showreel banner, client proofing links, and brief request intake.
  - **Management Consulting & Advisory** (`tmpl_consulting_preview.jpg`): Editorial corporate layout, practice areas, partner credentials, and executive advisory booking.
  - **E-Commerce Direct-to-Consumer Store** (`tmpl_ecommerce_preview.jpg`): High-converting product showcase, feature callouts, customer reviews, and cart hooks.
  - **Commercial Photography Studio & Production House** (`tmpl_photography_preview.jpg`): Masonry visual gallery, shoot package comparison, and booking intake screener.
  - **Luxury Real Estate Brokerage** (`tmpl_realestate_preview.jpg`): Property showcase cards, neighborhood highlights, agent roster, and showing request forms.
  - **Modern B2B AI SaaS Platform** (`tmpl_saas_preview.jpg`): Interactive product UI preview card, feature grid, 3-tier pricing matrix, and lead capture.
* **Zero-Emoji Policy Compliance**: Purged all emojis from labels, descriptions, and wizard cards, strictly utilizing thin-lined vector SVGs (`stroke-width: 1.8`) to uphold the Cora Design System.

### 3.7 AI HTML & Tailwind Starter Prompts Grid & 10-Min Zero-Build Launch (v4.9.334 - v4.9.337)
* **Curated 5-Template Production Prompts Grid**: Add Theme Wizard Step 2 provides 5 industry-calibrated master prompts engineered for maximum aesthetic fidelity and conversion:
  1. **Creative Agency & Video Production Studio**: Monochromatic portfolio with video reel hero, showcase case studies, client testimonial ticker, and project brief request form.
  2. **AI SaaS & Software Product Landing**: Dark-accented hero with live interactive preview mockup, feature metric grid, interactive pricing calculator, and lead capture.
  3. **B2B Advisory & Management Consulting**: Institutional editorial layout with practice areas, leadership partner credentials, and strategic executive audit form.
  4. **Commercial Photography Studio & Production House**: High-density gallery masonry layout, photoshoot booking calendar integration, and proofing portal highlights.
  5. **Custom Niche Enterprise Studio**: Flexible modular framework ready for custom industry branding and localized service offerings.
* **1-Click Prompt Extraction**: Each template card features a 1-click `📋 Copy Prompt for Claude / ChatGPT` button that formats a comprehensive, battle-tested prompt instructing AI models to generate pure HTML5, Tailwind CSS via CDN, Lucide vector icons, and native Cora lead form hooks.
* **Dual-Mode Code Ingestion & Live Preview**: Step 2 includes a dual-mode code connector:
  - **Direct HTML Paste / Dropzone**: Instant client-side parsing and validation of uploaded `.html` or `.zip` archives.
  - **Sandboxed Live Preview**: Real-time rendering in a scaled 1280x760 preview box before finalizing theme creation.
* **Script Tag Escape Sanitization (v4.9.337)**: All prompt templates dynamically escape closing script tags (`<\/script>`) in PHP rendering, completely preventing browser HTML parser premature script termination vulnerabilities.

### 3.8 Automated Static Site Compiler & Exporter Pipeline (v4.9.317 - v4.9.331)
* **Automated Repository Build Engine (`cora_nextjs_build_repo`)**: Executes direct compilation of connected GitHub repositories on Cora hosting without requiring external Vercel/Netlify infrastructure or long-running Node.js daemon processes.
* **Framework Auto-Detection**: Automatically identifies project architecture (Next.js App Router, Next.js Pages Router, Expo React Native Web, Vite, and standalone React).
* **Static HTML Route Extraction Pipeline**: Automatically extracts compiled static distributions (`out/`, `dist/`, `build/`) and server-rendered HTML chunks from `.next/server/app` and `.next/server/pages`, bundling CSS assets, JS chunks, and public media into localized workspace storage.
* **Instant Static Asset Streaming Router**: `cora_render_nextjs_static_build()` intercepts frontend routes on `init` (priority 1), streaming static JS/CSS assets (`/_next/*`, `/_expo/*`, `/brand/*`) directly with proper MIME types, immutable cache headers (`Cache-Control: public, max-age=31536000`), and zero CMS template engine overhead.
* **Automated GitHub CI/CD Webhooks**: Integrates incoming GitHub push deployment webhooks (`/wp-json/cora-canvas/v1/nextjs/github-webhook`) with HMAC-SHA256 signature verification for zero-touch automatic redeployments.

### 3.9 Shared Hosting Resilience & CloudLinux Inode Quota Recovery (v4.9.323 - v4.9.331)
To ensure reliable Next.js builds on constrained CloudLinux and shared cPanel/Hostinger hosting:
* **Inode Quota Auto-Recovery (`-122 EDQUOT`)**: Proactively purges stale npm caches (`~/.npm/_cacache`) and redundant `node_modules` folders from inactive build directories prior to compilation.
* **Self-Healing Dependency Installation**: Implements resilient npm install fallbacks (`npm install --no-audit || (rm -rf node_modules package-lock.json && npm install)`), permanently eradicating directory rename locks (`ENOTEMPTY`).
* **Low-Memory & Process Hardening**: Restricts Node.js memory footprint (`NODE_OPTIONS="--max-old-space-size=768"`), locks single-CPU thread allocation (`RAYON_NUM_THREADS=1`, `UV_THREADPOOL_SIZE=1`, `NEXT_CPU_COUNT=1`), and applies WebAssembly flags (`--no-wasm-code-gc`, `--disable-wasm-trap-handler`) to eliminate worker pool crashes.
* **Real-Time 5-Step Build Terminal**: Step 3 of the Add Theme Wizard renders a live streaming build terminal with progressive checklist badges and root-cause diagnostic cards with 1-click `⚡ Retry Build` controls.

### 3.10 Universal Lead Form Ingestion & Live Site CRM Interception (v4.9.333 - v4.9.335)
* **Unbreakable Lead CRM Bridge**: Bridges frontend static websites with backend CRM pipelines without requiring custom serverless functions or Formspree subscriptions.
* **Client-Side Form Interception**: Automatically injects lightweight form interception script into served static HTML pages.
* **Dual Ingestion Support**:
  - Standard HTML `<form action="/wp-json/cora-canvas/v1/nextjs/forms/submit" method="POST">`
  - Embedded Cora Form widgets (`.cora-form-embed`)
* **CRM Ingestion & Feedback**: Intercepts `submit` events, serializes form fields (Name, Email, Phone, Company, Budget, Message), inserts records into `wp_cora_leads` with `status = 'new'`, dispatches push notifications, and displays an instant monochromatic confirmation card in-place without page reloads.

### 3.11 Path Traversal Containment, ZipArchive Sanitization & HTTP 304 Caching (v4.9.337)
* **Strict `realpath` Path Containment**: All static asset streaming operations evaluate candidate file paths against the real root directory (`realpath($static_dir)`), strictly rejecting any paths outside the designated workspace storage tree and neutralizing path traversal exploits.
* **ZipArchive Extraction Sanitization**: Unpacking `.zip` theme archives iterates through file indices, explicitly skipping entries containing directory traversal characters (`../` or `..\`) and verifying real target directories before file copying.
* **HTTP 304 Not Modified Caching**: Computes deterministic MD5 ETags based on file modification timestamps (`filemtime`). When incoming requests provide matching `HTTP_IF_NONE_MATCH` headers, Cora responds immediately with HTTP 304 Not Modified, saving server bandwidth and delivering sub-10ms response times.
* **Capability Gatekeeping**: Enforces strict `edit_theme_options` and `manage_options` capability checks across all Next.js route scanning, ISR triggering, and page props endpoints.

### 3.12 Turnkey Official ChatGPT Plugin & Remote MCP 2.0 Ecosystem (v4.9.334)
* **Turnkey Distribution Package (`cora-chatgpt-plugin/`)**: Provides official deployment assets for OpenAI ChatGPT Plugins, Custom GPT Actions, and Remote MCP 2.0 servers.
* **OpenAPI 3.1.0 Specification (`openapi.json`)**: Formulates strict schema definitions for 25+ agency tools across CRM, Invoicing, Bookings, Inventory, and Media management.
* **Authentication Standards**: Supports Bearer API token authorization and OAuth 2.1 PKCE authorization code grant with multi-tenant workspace scoping.

### 3.13 Standardized Canvas Drawers & Overflow Locking Standard (v4.9.352 - v4.9.353)
* **Unified Right-Sliding Drawer Architecture (`.cora-drawer`)**: Abrupt, floating modal dialogs for template configuration, custom HTML code editing, and AI prompt copying are replaced with full-height, right-sliding side drawer sheets.
* **Body Scroll Jump Prevention**: Opening any Canvas drawer adds the `.cora-canvas-drawer-open` class to `document.body` (`body.cora-canvas-drawer-open { overflow: hidden !important; }`). This eliminates viewport jumping, locks background scroll chaining, and guarantees a smooth entrance animation without layout destabilization.
* **Resilient Event Delegation & Syntax Recovery (v4.9.353)**: Completely repaired JavaScript syntax parsing blocks in `view-canvas.php`, re-binding click event handlers to all primary action buttons (`window.coraOpenAddThemeWizard`, `window.coraApplyPlaygroundCodeToEditor`, and theme switcher controls).

### 3.14 AI Tools MCP Gateway & Live Action Playground in Template Editor (v4.9.351)
Directly integrated into the visual Template Editor view in `views/view-canvas.php`:
* **Tri-Mode Operational Switcher**:
  1. **Natural Language Mode**: Allows developers and workspace users to formulate instructions in plain English (e.g. "Change the hero headline to award-winning studio and set package pricing to 50,000 INR"), dispatching to the underlying AI tool execution loop.
  2. **Guided Actions Mode**: Presents an interactive dropdown of all 28 registered operational tools with dynamic form fields corresponding to each tool's JSON input schema.
  3. **Raw JSON Mode**: Provides a CodeMirror-powered raw JSON-RPC 2.0 interface for low-level protocol inspection and debugging.
* **Live Response Inspector**: Displays formatted tool outputs, server response headers, and generated HTML code in real-time.
* **Instant Code Reflection (`window.coraApplyPlaygroundCodeToEditor`)**: Clicking `📥 Apply to Live Editor` immediately pipes tool-generated HTML into the CodeMirror editor buffer and triggers zero-copy static distribution updates.

### 3.15 Universal Remote MCP Server & OpenAPI 3.0.1 Specification (v4.9.354 - v4.9.357)
Engineered in `includes/mcp/class-cora-universal-mcp-server.php` and `cora-workspace.php`:
* **OpenAPI 3.0.1 Standard Upgrade**: Upgraded live schema generation (`/wp-json/cora/v1/mcp/openapi.json`) from 3.1.0 to 3.0.1 to guarantee 100% compatibility with OpenAI Custom GPT Actions importer, which strictly validates against OpenAPI 3.0.x.
* **Curated 28 Core Actions Ceiling**: OpenAI Custom GPT imposes an undocumented 30-action ceiling per GPT. Cora dynamically caps default action exposure to 28 prioritized operational tools (Workspace Overview, Active Theme, Leads, Clients, Projects, Tasks, Financials, Content, RAG, and Forms), ensuring instant action discovery without schema import truncation. Pass `?all=1` or `limit=all` to retrieve the entire registry.
* **Schema Hardening & Emoji Purging**: Stripped all emojis from tool descriptions and summaries, sanitized input schema parameter types to valid objects, and omitted empty `required` arrays to prevent ChatGPT validation failures.
* **ChatGPT Developer Plugin Handshake Support**: Supports native ChatGPT plugin handshakes, session tool discovery (`tools/list`, `tools/call`, `initialize`), and unified REST routing.

### 3.16 Interactive OAuth 2.1 Consent Panel Architecture (v4.9.354)
Engineered in `includes/mcp/class-cora-oauth-server.php`:
* **Master Full-Access Toggle**: Includes an interactive "Universal Full Access (Read + Write)" master switch that toggles all 20 scopes in a single click.
* **Categorized Scope Architecture**: Groups permissions across 10 functional business domains (Core Workspace, Website & Themes, CRM & Leads, Directory, Operations & Bookings, Tasks, Finance & Ledger, Content & SEO, Living Memory RAG, Automations).
* **1-Tap Quick Presets**: Provides one-click permission presets: `All Tools`, `Read-Only`, and `Website & CMS Only`.
* **Dynamic Scope Selection Counter**: Displays live granted counts (`X of 20 granted`) before authorization.
* **Multi-Tenant Security Guarantee**: OAuth tokens are cryptographically generated (32-byte CSPRNG) and bound strictly to authenticated tenant agencies (`agency_id`). Any request attempting parameter-level tenant spoofing is overridden server-side.

### 3.17 Live HTML Preview Resilience Shield & Auto-Tailwind Injection (v4.9.358 - v4.9.359)
* **Automatic Tailwind CSS CDN Injection**: When rendering live HTML previews in `#cora-html-canvas-iframe` or exporting static sites, the compiler automatically detects if Tailwind CSS is referenced. If missing, it dynamically injects `<script src="https://cdn.tailwindcss.com"></script>` into the document `<head>`, guaranteeing that Tailwind utility classes render with 100% fidelity without build-time compiling.
* **Iframe Error Boundary & Zero Blank Section Shield**: Wrapped preview DOM elements in defensive error boundaries. If an inline JavaScript snippet or external script fails, the preview catches the runtime exception gracefully, rendering fallback mockups and preventing blank screen freezes.
* **PostMessage Bidirectional Synchronization**: The parent workspace and preview iframe exchange DOM state changes via secure `window.postMessage()` channels, enabling zero-latency live text editing and real-time element style mutations.

### 3.18 Streamlined Live Code Editor Header Architecture (v4.9.360)
* **Distraction-Free Minimalist Toolbar**: Removed redundant portrait and viewport switcher controls from the Live Code Editor header bar, eliminating visual noise and giving developers maximal vertical and horizontal real estate for CodeMirror code editing and preview inspection.
* **Monochromatic Action Clusters**: Grouped primary controls (File Explorer, Edit with AI, Play, Save & Publish, New Tab Live Site) into distinct, tonal clusters adhering to Rule 13 zero-outline specifications.

### 3.19 Cora AI Editor Interoperability & Workspace Forms Embedding Suite (v4.9.361)
* **Dynamic Lead Form Shortcode & Iframe Generator**: Allows agencies to embed any active Form AI Architect lead form directly into Canvas landing pages and static themes:
  - **Native Embed Shortcode**: `[cora-form id="12" theme="light"]`
  - **Standalone Responsive Iframe**: `<iframe src="https://app.heycora.in/workspace/forms/embed?id=12" class="w-full border-0 rounded-2xl min-h-[480px]"></iframe>`
* **Real-Time Cross-Module Lead Pipeline Ingestion**: Submissions from embedded forms route directly into `wp_cora_leads` with full campaign attribution, triggering in-app bell alerts and PWA push notifications.

### 3.20 Unified "Edit with AI" Toolbar Architecture (v4.9.362)
* **Consolidated Single-Action AI Trigger**: Replaced fragmented multiple AI edit buttons with a unified, high-visibility "Edit with AI" action bar docked in the Live Code Editor.
* **Context-Aware Prompt Pipeline**: Passing natural language directives (e.g. "Add a modern FAQ accordion with 4 questions below the pricing table") automatically inspects the active DOM, generates target diffs, and reflects updated markup into the CodeMirror editor with 1-click apply.

### 3.21 Live Static Site Styling Parity & CSS Scope Containment (v4.9.363)
* **100% Visual Parity Guarantee**: Enforces identical CSS reset standards and font loading between in-editor preview iframes and exported production static distributions (`out/`, `dist/`).
* **Host Stylesheet Isolation**: Prevents parent dashboard stylesheets from leaking into the sandboxed website DOM, ensuring published static sites look pixel-identical to developer previews.

### 3.22 GitHub Enterprise Multi-Tenant Workspace & Reversibility Engine (v4.9.381)
Engineered in `includes/class-cora-github-integration.php` and rendered at the extreme edge of the Canvas Sub-Navigation tabs bar (`views/view-canvas.php`):
* **Extreme-Edge Sub-Navigation Tab**: Docked at the far right of the Canvas sticky tabs bar via responsive utility `sm:ml-auto` (`#subtab-btn-tab-canvas-github`), providing zero visual friction with Theme Library, Speed & Core Web Vitals, and Migration & Tools tabs.
* **Dual-Architecture Mode**:
  1. *Personal GitHub Account Mode*: Users connect personal GitHub Personal Access Tokens (PAT) (`repo`, `workflow`, `read:org` scopes), linking existing personal repositories or provisioning new repos.
  2. *Organisation-Managed Workspace Mode*: Automatically provisions dedicated tenant-isolated repositories or sandboxed branches under Cora's primary GitHub Organisation (`cora-platform`), handling OAuth and PAT complexity transparently.
* **Granular Change Tracking & Auto-PR Reversibility**:
  - Automatically converts any workspace change—down to a single text string, layout block, or metadata attribute—into tracked commits or automated Pull Requests (`cora_github_create_pr`).
  - Dual sync engines: **Pull Request Workflow** (strict change review, preview checks, and merge gate) and **Direct Commit Workflow** (high-velocity continuous sync).
* **1-Click Commit Snapshot Rollback (`cora_github_revert_commit`)**:
  - Restores historical design JSON from any commit SHA directly back into active WordPress post meta.
  - Automatically publishes a reversion commit to GitHub to preserve an unbroken, auditable Git history.
* **Enterprise Multi-Tenant Cryptographic Isolation (100k+ Scale)**:
  - Sandboxes every workspace into isolated tenant identifier prefixes (`ws-{agency_id}` / `ws-{user_id}`).
  - Multi-tenant repositories segregate branches per workspace (`ws-{agency_id}/main`, `ws-{agency_id}/draft-{page_id}`) to prevent cross-tenant contamination across hundreds of thousands of concurrent users.

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
| `wp_cora_projects` | Workspace portfolio case studies, 4-act transformation narratives, proof KPIs, and client testimonials |
| `wp_cora_workspace_plugins` | Isolated workspace-level dynamic plugins, webhook connectors, and custom MCP tool bridges |
| `wp_cora_workspace_skills` | Custom AI agent skills, system prompt behaviors, parameter schemas, and bound MCP tools |
| `wp_cora_workspace_api_keys` | Tenant-scoped API keys (`cora_ws_live_...`) with rate limiting and RBAC permission scopes |
| `wp_cora_workspace_plugin_logs` | Audit logging of plugin executions, webhook dispatches, and AI skill telemetry |

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

## Section 8: Testing, Automated Verification & Quality Assurance

Cora maintains an extensive, multi-tier testing framework ensuring zero regressions, strict multi-tenant isolation, and complete architectural compliance:

### 8.1 Automated Backend & Architectural Verification Suites (PHP CLI)
* **Client Onboarding & Role Isolation Suite (`scripts/verify_client_onboarding_features.php`)**:
  - Validates active registration of WordPress role `cora_client`.
  - Tests dynamic service catalog persistence (`cora_workspace_services_{agency_id}`) via `cora_ajax_save_workspace_custom_service`.
  - Tests end-to-end client creation with multi-currency (USD), Zero-Rated Export LUT (0%), custom milestone payment terms, and direct `wp_cora_clients` insertion.
  - Verifies linked project showcase auto-creation in `wp_cora_projects`.
  - Verifies WordPress user account provisioning (`role: cora_client`), password hashing, and server-side login redirection to the client portal.
* **Plugins, Modules & AI Skills Engine Suite (`scripts/verify_plugins_skills_architecture.php`)**:
  - 14 automated test cases covering the entire extensibility subsystem:
    1. *Database Schema*: Confirms existence and column definitions for `wp_cora_workspace_plugins`, `wp_cora_workspace_skills`, `wp_cora_workspace_api_keys`, `wp_cora_workspace_plugin_logs`.
    2. *Starter Plugins Seeding*: Verifies automatic seeding of 5 starter plugins (`webhook-dispatcher`, `google-calendar-sync`, `payment-gateway-listener`, `client-portal-notifier`, `daily-ledger-reconciler`).
    3. *Starter Skills Seeding*: Verifies automatic seeding of 5 starter skills (`client-intake-specialist`, `gst-invoice-auditor`, `high-ticket-lead-qualifier`, `shoot-gear-readiness-auditor`, `portfolio-storyteller`).
    4. *Custom Plugin CRUD*: Verifies creation, status toggling, and deletion.
    5. *Custom Skill CRUD*: Verifies custom agent skill authoring, trigger phrases, and parameter schemas.
    6. *Execution Runtime & Telemetry*: Tests execution of prompt and script skills, verifying audit logging in `wp_cora_workspace_plugin_logs`.
    7. *AI Plugin Generator*: Validates natural language prompt-to-plugin compilation.
    8. *AI Skill Generator*: Validates natural language prompt-to-skill compilation.
    9. *Workspace Scoped API Keys*: Validates cryptographic generation (`cora_ws_live_...`), request header authentication (`X-Cora-API-Key`), scope gating, and revocation.
    10. *Dynamic MCP Tool Registration*: Confirms dynamic injection into `Cora_MCP_Tool_Registry::get_tools()` via `cora_mcp_tools` filter and prefix mapping (`cora_skill_*`, `cora_plugin_*`).
    11. *Rule 3 Privacy Compliance*: Verifies zero owner names across newly introduced engine code.
    12. *JSON Plugin Manifest Upload*: Tests ingestion of standardized JSON plugin specifications.
    13. *Antigravity SKILL.md Upload*: Tests ingestion of YAML frontmatter + Markdown instruction prompt files.
    14. *Cross-Tenant Isolation*: Verifies absolute isolation between Tenant A and Tenant B, guaranteeing zero asset leakage.
* **Remote MCP & OAuth 2.1 Suite (`scripts/test_mcp_full_access.php`)**:
  - Validates RFC 7591 dynamic client registration, RFC 6749 / RFC 7636 OAuth 2.1 PKCE authorization flows, token issuance, and MCP JSON-RPC 2.0 tool execution.
* **Local Test Accounts Provisioning (`scripts/setup_local_accounts.php`)**:
  - Provisions seed accounts for Super Admin, Real Estate Owner, and Photography Studio Owner.

### 8.2 Playwright End-to-End (E2E) Test Harness (`tests/e2e/`)
* **Interactive Calendar & Scheduling MVP (`test-calendar-mvp.spec.ts`)**:
  - Verifies Month, Week, and Day grid rendering, 2-way Google sync trigger, valid 3-4-3 Google Meet URLs (`https://meet.google.com/xxx-yyyy-zzz`), appointment creation drawers, and non-clipped sticky footers.
* **Agency Projects Multi-Step Drawer (`test-project-multistep-drawer.spec.ts`)**:
  - Tests the 6-tab portfolio case study editor in `views/view-projects.php`, Shopify-inspired docked panel architecture, fullscreen maximize toggle (`⤢`), dynamic sidebar space bounds synchronization, and form input retention.
* **Collapsible Sidebar Groups (`test-sidebar-group-toggle.spec.ts`)**:
  - Validates interactive chevron rotation, per-group `localStorage` state persistence, and automatic group expansion during active navigation search (`.is-searching`).
* **Sub-Tabs Extra Actions & AI Hover (`test-subtabs-toolbar-and-ai-hover.spec.ts`)**:
  - Validates pixel-perfect vertical centering of sub-tabs action bars, Level 01 Segmented Control view mode switchers (Table vs Grid), comfortable 28px search trigger tap targets, and AI quick action popovers.
* **Closed-by-Default Accordion Standard (`test-accordions-closed-by-default.spec.ts`)**:
  - Enforces closed-by-default state across drawers and cards to preserve high-density visual hierarchy.
* **Workspace Media Asset Picker (`test-workspace-media-picker.spec.ts`)**:
  - Validates media selection modal integration, aspect ratio presets, and responsive thumbnail grids.
* **Compact Mobile Multi-Step Drawers (`test-mobile-all-steps-compact.spec.ts`)**:
  - Tests mobile drawer transitions (`translate-y-full` to `translate-y-0`), bottom drag handles, safe-area elevation, and zero horizontal scroll overflow.

### 8.3 Design System & Build Verification
* **Frontend Design System Validator (`npm run check:design-system` in `cora-frontend`)**:
  - Validates strict token usage, typography enforcement (Inter for workspace, General Sans for marketing), semantic color scales, and contrast compliance.
* **Core Build Pipeline (`scripts/build.sh`)**:
  - Verifies version consistency across plugin headers, PHP constants (`CORA_WORKSPACE_VERSION`), PWA manifests, and release packages.

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

### 11.3 Security URL Masking & Framework Hardening
To deliver a pure white-label enterprise SaaS experience and mask underlying framework internals:
* **Native Symlinks & Rewrites**: Masks internal directories to `/assets/` and `/core/` via server rewrites and symlinks.
* **Administrative Endpoint Lockdown**: Unauthenticated direct access attempts are automatically redirected to `/workspace/dashboard` or `/workspace/login`.
* **Virtual URLs**: Clean endpoints for `/workspace/dashboard`, `/workspace/login`, `/workspace/register`, `/workspace/onboarding`.
* **Admin Bar Suppression**: Hides native toolbars completely for a true white-label SaaS experience.

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
| **v4.9.387 (CORA-1239)** | Oct 2026 | Plugins in App Module, Dynamic MCP 2.0 & AI Skills Engine: Level 01 Segmented Control (Plugins, Modules, Skills) at `/workspace/plugins`, `/workspace/modules`, `/workspace/skills`; isolated multi-tenant schema (`wp_cora_workspace_plugins`, `wp_cora_workspace_skills`, `wp_cora_workspace_api_keys`, `wp_cora_workspace_plugin_logs`); dynamic MCP tool filter (`cora_mcp_tools`) registering dynamic tools into `Cora_MCP_Tool_Registry`; API & MCP Gateway drawer with tenant API key provisioning (`cora_ws_live_...`), scopes, rate limits, and live OpenAPI 3.0.1 export (`/workspace/openapi`); multi-format ingestion (JSON Plugin Manifests, Antigravity SKILL.md, Autonomous AI generator); 5 starter plugins (Webhook Dispatcher, Google Calendar Sync, Payment Gateway Listener, Client Portal Notifier, Daily Ledger Reconciler) and 5 starter skills (Client Intake Specialist, GST Invoice Auditor, High-Ticket Lead Qualifier, Shoot Gear Readiness Auditor, Portfolio Storyteller) |
| **v4.9.386 (CORA-1238)** | Oct 2026 | Enterprise Client Onboarding Suite, Strict Role Isolation & Two-Way Calendar Synchronization: Guided 5-step client onboarding studio in `views/view-clients.php` (Entity, Multi-Currency / Tax Regimes, Production Service Matrix & Catalog, Initial Project Showcase, User Account Provisioning & Credentials); strict WordPress role `cora_client` portal isolation with server-side `/workspace` redirection and login redirect filter; dynamic service catalog persistence (`cora_workspace_services_{agency_id}`); multi-currency (INR, USD, EUR, GBP, AED, SGD) and tax regimes (Domestic GST 18%, Zero-Rated Export LUT 0%, Custom VAT); public client portal deliverables showcase, proofing vault button, and suspended account state; full interactive scheduling calendar in `views/view-calendar.php` (Month, Week, Day), 2-way Google Calendar sync, and standard 3-4-3 Google Meet syntax (`https://meet.google.com/xxx-yyyy-zzz`) |
| **v4.9.385 (CORA-1237)** | Oct 2026 | Mobile Sticky Offsets, Sub-Tabs Stacking & Search Touch Target Sizing: Resolved mobile header double-stack gap via dynamic `--cora-sticky-tabs-top` calculation in `admin-script.js`; preserved comfortable 28px (`w-7 h-7`) tap target sizing for mobile search trigger and close buttons (`#cora-proj-search-trigger-btn`, `#cora-proj-search-close-btn`); relocated projects canvas module mount directly below workspace header with `mt-4`; collapsible sidebar navigation groups with per-group localStorage state persistence (`cora_sidebar_group_{key}`) and instant auto-expansion during active navigation search (`.is-searching`) |
| **v4.9.384 (CORA-1236)** | Oct 2026 | Global Sticky Workspace Header & Mobile Expandable Sub-Tabs Search: Enforced global sticky header pinning (`position: sticky; top: 0; z-index: 100020;`) across all desktop and mobile viewports; resolved sub-tabs search horizontal overflow on mobile screens (< 640px) via 1-tap expandable search trigger (`#cora-proj-search-trigger-btn`) expanding full-width input (`#cora-proj-search-wrapper.mobile-expanded`) with Esc / close button dismiss; and synchronized version manifest to 4.9.384 |
| **v4.9.383 (CORA-1235)** | Oct 2026 | Sub-Tabs Extra Actions Vertical Alignment & Level 01 Segmented Switcher: Standardized `tabs_extra_html` vertical alignment in `includes/workspace-header.php` (`h-full flex items-center gap-1.5 sm:gap-2`); implemented Level 01 Segmented Control container for Table vs Grid view mode toggle with active button styling (`bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs`) |
| **v4.9.382 (CORA-1234)** | Oct 2026 | Shopify-Inspired Canvas Maximize / Restore Toggle (⤢ Fullscreen Mode): Added dedicated maximize button (`#cora-proj-maximize-btn`) in docked drawer header toggling `.maximized` state, expanding drawer across full viewport (`left: 0; width: 100vw; z-index: 100060; border-left: none`) for zero-distraction case study authoring with instant sidebar synchronization on `cora:sidebar-toggle` |
| **v4.9.382 (CORA-1233)** | Oct 2026 | Shopify-Inspired Main-Area Docked View Panel Architecture & Responsive Bounds Sync: Implemented SOP Rule 15 docked view panel in `views/view-projects.php` sliding up smoothly from bottom (`translateY(100%)` to `translateY(0)`), docking directly beneath top header (`top: 52px; height: calc(100vh - 52px)`), dynamically sizing to available main content canvas based on sidebar state (`MutationObserver` & `resize`), zero overlay / zero backdrop, scoped main scroll lock (`overflow-hidden` on `main.cora-main`), and Escape key dismissal |
| **v4.9.381 (CORA-1227)** | Oct 2026 | Agency Projects & Portfolio Case Studies Module Architecture: Dedicated multi-tenant schema `wp_cora_projects` with 46 fields (title, slug, client_name, industry, services_rendered, status, is_featured, is_confidential, hero_image_url, gallery_images, featured_kpis, executive_summary, challenge_narrative, solution_narrative, deliverables_list, testimonial_quote, testimonial_author, testimonial_role, conversion_cta_url, conversion_cta_text, views_count); 3-stage status lifecycle (published, draft, archived); automated confidentiality / stealth NDA masking; numerical KPI highlights rendered in JetBrains Mono font (`font-mono font-bold tracking-tight text-zinc-950`); 4-act transformation narrative framework (Act 1: Executive Summary, Act 2: Challenge / Friction, Act 3: Solution, Act 4: Deliverables); verified client testimonials (`Aarav Mehta`, `Kavya Patel`, `Rohan Verma`); Strategy Audit conversion CTA bridge (`Schedule Strategy Audit` -> `/contact`); 6-tab drawer editor in `views/view-projects.php` (Overview, Narrative Story, Impact KPIs, Media & Visuals, Endorsement, SEO & CTA); and high-performance Next.js frontend at `cora-frontend/app/projects/` powered by `projects-data.ts` |
| **v4.9.381 (CORA-1228)** | Oct 2026 | Public Micro-Tools Growth Engine & Canonical Tool Registry: Single source of truth in `tools-canonical-registry.ts` defining 63 verified tools across 10 problem domains (Agency Economics, WordPress Stack, AI Discovery, Finance & Tax, Contracts & Legal, Developer & Embed, PDF Tools, Sheets Tools, Image Tools, Real Estate) with full 24-field schema; core framework UI components (`ToolInputGroup`, `ToolResultSummary`, `ToolActionBanner`, `ToolFaqAccordion` with Schema.org JSON-LD, `ToolBreakdownTable`); strict Zero-PII analytics governance in `lib/tools-analytics.ts` forbidding transmission of client names, documents, prompts, or revenue figures; 6 flagship growth tools (`wp-plugin-cost-calculator`, `lead-leakage-calculator`, `scope-creep-calculator`, `service-pricing-calculator`, `wp-publishing-friction-calculator`, `ai-crawler-checker`); and frictionless Problem-to-Workspace conversion bridge |
| **v4.9.381 (CORA-1229)** | Oct 2026 | Canvas Frontend Themes Engine & GitHub CI/CD Full-Lifecycle Architecture: Consolidated 4-KPI analytical overview bar in `views/view-canvas.php` (Core Web Vitals score, LCP load speed, Themes Library count with live/draft quota, Published Pages count with 100% SEO readiness); ~40px edge-to-edge sticky 3-subtab bar (`Theme Library`, `Speed & Core Web Vitals`, `Migration & Tools`, plus extreme-right `GitHub Integration`); complete Git lifecycle in `Cora_GitHub_Integration` (`includes/class-cora-github-integration.php`) supporting RFC 8628 OAuth 2.0 Device Authorization Grant (`cora_github_initiate_device_flow`, `cora_github_poll_device_token`), secure PAT storage with Bearer fallbacks, dynamic branch creation (`cora_github_create_branch`), automated PR creation (`cora_github_create_pr`), PR review list & 1-click atomic merging (`cora_github_merge_pr`), automated commit rollback/revert (`cora_github_revert_commit`), atomic workspace synchronization (`cora_github_sync_entire_workspace`), and organization repository provisioning (`cora_github_provision_org_repo`) |
| **v4.9.381 (CORA-1230)** | Oct 2026 | Forms to CRM Lead Pipeline Dynamic Automation & Multi-Submission Aggregation: Automatic lead conversion in `cora_rest_submit_form()` (`cora-workspace.php`) into `wp_cora_leads` with multi-tenant isolation (`agency_id`, `branch_id`), case-insensitive field extraction (`name`, `email`, `phone`, `notes`, `budget`, `city`, `service`), round-robin assignee assignment, predictive qualification scoring (hot, warm, cold), micro-cache invalidation via `cora_db_get_leads(true)` and `cora_workspace_leads` option update, and bidirectional self-learning RAG ingestion (`cora_rag_ingest_event`); aggregate submissions REST endpoint `GET /wp-json/cora/v1/forms/all-submissions` (and `/forms/submissions`); and hardened lead AJAX controllers |
| **v4.9.381 (CORA-1231)** | Oct 2026 | Editorial Knowledge Engine & High-Authority Industry Article Library: 15+ comprehensive technical guides and industry teardowns in `cora-frontend/lib/articles-data.ts` across 7 editorial domains (Websites & Content, Product Guides, Studio Operations, Finance & 18% GST, Autonomous AI & MCP, Legal Tech & Contracts, Product Comparisons & Benchmarks); key teardowns covering WordPress plugin stack TCO, agency scope creep economics, Harvard Business Review speed-to-lead benchmarks, autonomous AI studio co-founders, voice-to-scope multimodal processing, MCP agency integration, Generative Engine Optimization (GEO), Indian IT Act 2000 Section 10A electronic contract validity, SHA-256 tamper-evident vault contracts, and head-to-head teardowns against HoneyBook, Studio Ninja, HubSpot, Dubsado, and Pixieset |
| **v4.9.381 (CORA-1232)** | Oct 2026 | Platform Hardening, Design Tokens Parity & Documentation Architecture: Full platform release synchronization to v4.9.381, Rule 3 privacy compliance (zero owner names, generic placeholders `Aarav Mehta`, `Kavya Patel`, `Rohan Verma`, `Studio Admin`), Rule 4/13 pure light mode & zero-outline tonal tokens, developer feature guide principles 100-105, and complete cross-module architectural documentation updates |
| **v4.9.380** | Oct 2026 | Industry Hub Architecture & Slug Migration: Canonical directory migration from `/use-cases` to `/industries` with 301 redirect parity in `.htaccess` and Next.js, refined desktop Industries Mega-Menu in `Navbar.tsx` matching exact production mockup with categorized icon tiles and colored pill accents, comprehensive plain-English active-voice copy across all 10+ industry verticals (`cora-frontend/lib/industry-data.ts`), and dynamic industry comparison table (`IndustryComparisonTable.tsx`) |
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
| **v4.9.364** | Oct 2026 | Standardized AI run quotas (15K Pro / 25K Scale monthly), weekly soft-limit throttling (3,750 Pro / 6,250 Scale runs/week), Super Admin rate-limit bypass, and platform-wide Endorsely affiliate tracking integration (`app.endorsely.com/assets/affiliate-tracking.js`) with dynamic conversion attribution on checkout completion |
| **v4.9.363** | Oct 2026 | Live static site styling parity, automated Tailwind CSS CDN injection, and CSS scope containment across Canvas previews |
| **v4.9.362** | Oct 2026 | Unified 'Edit with AI' toolbar action in Live Code Editor with instant diff previewing and context-aware prompt execution |
| **v4.9.361** | Oct 2026 | Cora AI editor interoperability and workspace forms embedding suite with `[cora-form id="..."]` shortcode and responsive iframe generator for static sites |
| **v4.9.360** | Oct 2026 | Streamlined Live Code Editor header by eliminating redundant portrait and viewport switchers for clean, distraction-free editing |
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
| **v4.9.338** | Oct 2026 | Dynamic Super Admin PWA Badging: dynamic `ADMIN`-badged app icons (512x512, 192x192, Apple touch, favicon), role-scoped Web App Manifest (`CORA Workspace (Admin)`), standalone link retention, and local admin provisioning script updates |
| **v4.9.337** | Oct 2026 | Security & Performance Hardening: Master prompt script tag sanitization (`<\/script>`) preventing premature script termination, strict `realpath` containment preventing path traversal, ZipArchive extraction sanitization against `../` directory escapes, HTTP 304 Not Modified ETag caching, immutable asset headers, and admin capability enforcement on Next.js endpoints |
| **v4.9.336** | Oct 2026 | Restored DOM hierarchy and pointer events across Add Theme Wizard and Canvas, polished monochromatic AI shortcuts and popover contrast |
| **v4.9.335** | Oct 2026 | AI Template Prompts & Form Interception: Curated starter HTML generator (`cora_canvas_get_default_starter_html`), master prompt templates, dual-mode code connector, and native form submission interception |
| **v4.9.334** | Oct 2026 | AI Starter Prompts Grid in Add Theme Wizard (5 production templates: Agency, SaaS, Consulting, Photography, Custom), 1-click clipboard prompt export for Claude/ChatGPT, native lead form ingestion into `wp_cora_leads`, and official ChatGPT Plugin distribution package (`cora-chatgpt-plugin/`) |
| **v4.9.333** | Oct 2026 | Unbreakable 10-minute website launch with instant HTML static mode and universal lead CRM bridge |
| **v4.9.331** | Oct 2026 | Build pipeline resilience: CloudLinux Inode recovery (`-122 EDQUOT`), low-memory Webpack configuration (`NODE_OPTIONS="--max-old-space-size=768"`), self-healing npm directory rename locks (`ENOTEMPTY`), and single-CPU thread allocation |
| **v4.9.328** | Oct 2026 | Next.js Dynamic & API Routes static compilation architecture without forced `output: 'export'`, single-CPU compilation (`experimental.cpus = 1`), and automatic environment variable pre-population |
| **v4.9.327** | Oct 2026 | Fixed `--webpack` CLI option for Next.js 14/15, enhanced static route exporter (`cora_export_html.mjs`) to recursively parse server HTML and manifest files |
| **v4.9.326** | Oct 2026 | Add Theme Wizard Step 3 real-time 5-step build terminal with live streaming logs, progressive checklist updates, and root-cause diagnostic error cards with 1-click `⚡ Retry Build` |
| **v4.9.325** | Oct 2026 | Zero-config native Next.js ISR & static route previews on Cora with zero external deployment server dependencies |
| **v4.9.324** | Oct 2026 | Automated Node.js static HTML route exporter pipeline extracting App/Pages router paths and CSS/JS chunks into distribution folders |
| **v4.9.323** | Oct 2026 | Shared hosting compiler hardening: single-CPU compilation (`UV_THREADPOOL_SIZE=1`, `NEXT_CPU_COUNT=1`), precompiled Tailwind v4, static sitemap patching, and `_next/static` asset bundling |
| **v4.9.322** | Oct 2026 | Automated Next.js static build on repository connection with dynamic agency ID resolution and auto-compilation on connector load |
| **v4.9.321** | Oct 2026 | Active theme live iframe preview box in Canvas Level 1 with 1280x760 scaling, Mac window top bar, and strict Next.js repository validation |
| **v4.9.320** | Oct 2026 | Live theme precedence hierarchy and instant static asset streaming on `init` priority 1 for `/_next/*`, `/_expo/*`, and brand assets |
| **v4.9.319** | Oct 2026 | Direct static asset routing and global live theme fallback preventing unwanted welcome screen fallbacks |
| **v4.9.318** | Oct 2026 | Connected Next.js static build directly to Cora frontend and Canvas Level 1/Level 2 previews with instant viewport switching (Desktop, Tablet, Mobile) |
| **v4.9.317** | Oct 2026 | 1-Click static site build from GitHub & webhook auto-deploy (`/wp-json/cora-canvas/v1/nextjs/github-webhook`) with HMAC-SHA256 signature verification, standalone Node.js compilation runner (`proc_open`), and Canvas schema self-healing (`cora_ensure_canvas_tables_exist`) |
| **v4.9.316** | Oct 2026 | Live Next.js Build Preview & Proxy Router in `cora-workspace.php`, offline dev server detection (sub-150ms timeout ping), preview cookie isolation (`cora_canvas_preview`) scoped strictly to explicit `?preview_canvas=...` requests, and elimination of random welcome screen fallbacks |
| **v4.9.315** | Oct 2026 | Van Stock & Manifest Print Engine: 5-column dispatch manifest and 4-column inventory reports (`ajax_render_van_stock_sheet`), itemized CSV export (`ajax_export_van_stock_csv`), 4-digit numeric bill numbers, and warehouse supervisor sign-offs |
| **v4.9.314** | Oct 2026 | 3-step van dispatch wizard, weight-based allocation & spot billing at ₹401.25/kg, GST invoice auto-print, spot invoices card, export CSV, print table controls, sales register table, itemized breakdown, CSV export, and printable ledger |
| **v4.9.313** | Oct 2026 | Canvas Next.js theme badge and route ingestion pipeline from scanned GitHub repositories |
| **v4.9.312** | Oct 2026 | Save GitHub token action and dual auth header fallbacks (`token <pat>` and `Bearer <pat>`) |
| **v4.9.311** | Oct 2026 | Private repository support and dynamic GitHub branch discovery for App Router scanner |
| **v4.9.310** | Oct 2026 | Route Add Theme wizard button directly to Next.js Git Connector Wizard |
| **v4.9.309** | Oct 2026 | Unified sidebar bottom notifications bell with canonical header notification drawer |
| **v4.9.308** | Oct 2026 | Canvas Next.js Git repository integration, route scanner (`app/**/page.tsx`), headless REST APIs (`/wp-json/cora-canvas/v1/*`), and on-demand ISR revalidation webhooks |
| **v4.9.307** | Oct 2026 | Pen icon routing to Level 2 live theme customizer editor with direct page props management |
| **v4.9.306** | Oct 2026 | Normalized sidebar navigation group item rendering and vertical spacing below Canvas |
| **v4.9.305** | Oct 2026 | Canvas hover quick-actions for live theme editor and new-tab live site view |
| **v4.9.304** | Oct 2026 | GST invoice publishing and automated Document Vault synchronization with SHA-256 hash linkage |
| **v4.9.303** | Oct 2026 | GST invoice multi-step creator validation, line items JSON serialization, and legal vault linking |
| **v4.9.302** | Oct 2026 | Sanitized team assignee roster in CRM leads and bi-directional drawer sync with Kanban cards |
| **v4.9.301** | Oct 2026 | Left-edge draggable resize on lead drawer, localStorage width persistence, and decluttered accordion groups |
| **v4.9.300** | Oct 2026 | Edge-to-edge full-height lead drawer, auto-portaling to `document.body`, transparent backdrop, and lead data sanitization |
| **v4.9.299** | Oct 2026 | Hardened Affiliate & Partner Network sub-tab routing, normalization, and click switching |
| **v4.9.298** | Oct 2026 | Curated color tag presets and custom hex color picker for Media Manager folders |
| **v4.9.296** | Oct 2026 | Centered pop-up modals for Media Manager folder creation and settings dialogs |
| **v4.9.295** | Oct 2026 | Edge-to-edge side drawers with zero dark overlays across Media Manager asset dialogs |
| **v4.9.294** | Oct 2026 | Permissions Matrix sticky column and monochromatic badge styling in team management |
| **v4.9.290** | Oct 2026 | AI Team Migration Hub converted to right-sliding side drawer with hardened state machine |
| **v4.9.285** | Sep 2026 | Content AI Suite: Autonomous article writing, editing, state management, and rich card previews |
| **v4.9.282** | Sep 2026 | Content AI Suite: Compact sleek KPI cards, authentic workspace authors with avatar placeholders, and table column consolidation |
| **v4.9.281** | Sep 2026 | Persistent mobile island navigation state and zero-flicker rendering |
| **v4.9.280** | Sep 2026 | Dynamic task notification alerts and modernized search icon SVGs |
| **v4.9.279** | Sep 2026 | Streamlined sidebar admin profile popover with AI Connected status badge |
| **v4.9.276** | Sep 2026 | Real-time client SPA navigation router (`cora-ai-router`) for direct AI action execution |
| **v4.9.274** | Sep 2026 | Continuous RAG self-learning, ultra-concise guided responses, and 1-click CTA action pills |
| **v4.9.269** | Sep 2026 | Administration Email verification with dynamic badges, locked state, token dispatch, and authenticated sender `heycora@claraverse.in` |
| **v4.9.266** | Sep 2026 | Web Push notification subsystem overhaul, VAPID key sync, and Super Admin push device telemetry |
| **v4.9.262** | Sep 2026 | Custom app icon branding & 1-click broadcast push updates in PWA engine |
| **v4.9.258** | Sep 2026 | Free Workspace tier default on signup with 100 daily AI credits quota matrix enforcement |
| **v4.9.255** | Sep 2026 | Eradicated trapped dark backdrop overlays across all modals, drawers, and sheets |
| **v4.9.247** | Sep 2026 | Single-tenant clean URLs without workspace slug, and Claraverse global control center support |
| **v4.9.234** | Sep 2026 | Super Admin Danger Zone, delete workspace confirmation modal, god-level access and universal onboarding bypass |
| **v4.9.232** | Sep 2026 | Transition to unified single-domain routing architecture |
| **v4.9.229** | Sep 2026 | RFC 7591 dynamic client registration for Google Gemini & MCP connected apps, underscore tool schemas |
| **v4.9.220** | Sep 2026 | MCP 2026-07-28 upgrade, RFC 9728 protected resource metadata, WWW-Authenticate headers, universal token extraction for ChatGPT/Claude |
| **v4.9.210** | Sep 2026 | Cora Universal MCP Server & OAuth 2.1 PKCE authorization server implementation |
| **v4.9.209** | Sep 2026 | Comprehensive Security Hardening & Remediation (SEC-001 - SEC-023): central tenant authorization policy gatekeeper (`Cora_Authorization`), outbound HTTP SSRF filter (`Cora_SSRF_Filter`), Custom role capability scoping (SEC-012), WhatsApp Cloud API HMAC-SHA256 signature verification (SEC-005), 32-byte CSPRNG token entropy, Next.js 16.3.3 and Sharp 0.35.4 dependencies |
| **v4.9.208** | Sep 2026 | Dedicated Early Route Interception for Email Verification (`/workspace/verify`), direct template mounting via `views/verify.php` bypassing framework template-redirect delays, auto-login state hydration, and monochromatic welcome redirection |
| **v4.9.207** | Sep 2026 | Mobile GPU hardware acceleration (`transform: translateZ(0); will-change: transform;`) and universal 0ms instant-tap responsiveness (`touch-action: manipulation; -webkit-tap-highlight-color: transparent;`) across mobile drawers, islands, and action controls |
| **v4.9.206** | Sep 2026 | Streamlined 3-step partner onboarding screener with progressive disclosure accordions and punchy value propositions |
| **v4.9.205** | Sep 2026 | Rule 13 zero-outline monochromatic design system enforcement across the Affiliate & Referral Ecosystem, replacing high-contrast borders with soft zinc tonal fills |
| **v4.9.204** | Sep 2026 | Edge-to-edge frosted sticky sub-navigation tabs across Affiliates (`Overview & Meter`, `Leaderboard & Ranks`, `Earnings Simulator`, `Referrals & Logs`, `Payouts & Banking`), eliminating side margin bleed |
| **v4.9.203** | Sep 2026 | Overhauled partner reward model: +100 Free AI Runes, 20% Monthly / 30% Annual recurring commission, and milestone cash bonuses (₹1,000 for 10, ₹5,000 for 50, ₹10,000 for 100 referrals) |
| **v4.9.202** | Sep 2026 | Gamified Affiliate Dashboard with dynamic tier progression meter (Bronze, Silver, Gold, Diamond), streak multiplier badge (`⚡ 3-Streak Active (+5% Bonus)`), and Top 3 podium leaderboard cards |
| **v4.9.201** | Sep 2026 | Cryptographic token-based email verification engine (`/workspace/verify?token=...`) with 32-byte entropy, automated user auto-login, and welcome redirection |
| **v4.9.200** | Sep 2026 | Affiliates & Referrals permanently locked as the 6th Core Foundation Module across all 6 industry domain class files, Feature Hub immutable badges, and alphanumeric partner rank badges |
| **v4.9.199** | Sep 2026 | Canvas Themes single-stream Theme Library (`#tab-canvas-overview`), 4-metric Speed/CWV diagnostic strip (CWV score, LCP speed, quota, published pages), 1-click `Optimize Now →` recommendation banner, viewable-only mobile mode, and High-Density Email module with dynamic CRM contacts sync (`wp_cora_clients`, `wp_cora_leads`) |
| **v4.9.198** | Sep 2026 | Full AI Agent Control in System Settings Suite (`update_settings`, `switch_settings_tab`, `trigger_backup`, `clear_system_cache`, `check_platform_updates`, `view_activity_logs`), live DOM synchronization, and Rule 3 privacy sanitization |
| **v4.9.197** | Sep 2026 | Desktop sticky chat input dock (`position: sticky; bottom: 0;`) in conversational AI workspaces (`calc(100vh - 195px)` flex column) with independent transcript momentum scrolling |
| **v4.9.196** | Sep 2026 | Rule 13 zero-outline monochromatic design system enforcement across AI Tools MCP Gateway and Living Memory RAG vector tables |
| **v4.9.195** | Sep 2026 | Notion/Linear-grade Model Selector Popover (`[ ◆ Gemini 3.5 Flash | Real-time ▾ ]`) with header-anchored low-profile trigger, dismiss shield, and drawer pointer-lock immunity |
| **v4.9.194** | Sep 2026 | AI Tools MCP unified 3-subtab architecture (`AI Assistant & Voice`, `MCP Developer Gateway`, `Living Memory RAG`) with edge-to-edge frosted sticky tabs |
| **v4.9.193** | Sep 2026 | AI Tools MCP Developer Gateway & Live Voice Stage (`views/view-mcp.php`) with dual mode switcher, central animated pulsing sphere orb (`.cora-voice-sphere`), soundwave waveform bars, and ElevenLabs neural voice synthesis |
| **v4.9.192** | Sep 2026 | Floating monochromatic mobile action dock in Media Management (`#cm-mobile-bottom-bar`) positioned cleanly above island navigation with 160px scroll padding |
| **v4.9.191** | Sep 2026 | Media Management Chief Creative Director AI Copilot (`upload_media`, `create_folder`, `share_media`, `filter_media`, `inspect_media_telemetry`, `view_storage_breakdown`) and real-time storage telemetry |
| **v4.9.190** | Sep 2026 | People Ops & User Management AI Copilot (`invite_member`, `edit_member_role`, `open_team_migration`, `open_permissions_matrix`, `filter_members`, `resend_invite`, `check_ai_quota`) with Rule 3 privacy compliance |
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
| **v4.9.0**  | Sep 2026 | Multi-Industry Engine expansion (Marketing Agency vertical), PHP framework backend lockdown, and virtual URL masking |
| **v4.0.0**  | Aug 2026 | Major platform consolidation release uniting all workspace modules into a unified clean-slate main branch |

---

*Cora Platform v4.9.387 — Master Architectural Manual. Last updated: October 2026.*
