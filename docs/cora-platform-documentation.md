# Cora Platform — Comprehensive Platform Documentation

This document serves as the master technical specification and architectural manual for the Cora Workspace Platform (v3.2.88+).

---

## Section 1: Core Theme System & PWA Architecture

### 1.1 Pure Light Mode Enforcement & Dark Mode Removal (v3.2.83)
Starting in **version 3.2.83**, dark mode support has been completely removed across all Cora platform plugins, workspace views, design tokens, and components. The platform strictly enforces a **pure light mode visual standard** platform-wide.

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
[ Active / Success ]  --->  🟢 Emerald/Green  (`bg-emerald-500`, `text-green-500`)
[ Pending / Warning ]  --->  🟡 Amber/Yellow   (`bg-amber-500`, `text-amber-500`)
[ Critical / Error ]   --->  🔴 Red           (`bg-red-500`, `text-red-500`)
[ Neutral / Info ]     --->  🔵 Blue/Zinc     (`bg-blue-500`, `text-zinc-500`)
```

#### Typography Stack & Iconography Rules
* **UI Sans-Serif**: `Inter`, `-apple-system`, `BlinkMacSystemFont`, `sans-serif` for all UI text, headings, buttons, and form labels.
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
1. **DOM Container**: Automatically injected into the DOM on first call at `#cora-toast-container` with fixed positioning:
   `fixed bottom-5 right-5 z-[9999] flex flex-col-reverse gap-2.5 pointer-events-none`
2. **Toast Component Styling**:
   `bg-white text-zinc-800 text-xs font-semibold px-4 py-3 rounded-xl shadow-lg border border-zinc-200 flex items-center gap-3 pointer-events-auto transition-all duration-300 transform translate-y-3 opacity-0 select-none max-w-sm`
3. **Duplicate Deduplication & Scale Bounce**: If a toast with identical message text is dispatched while already visible:
   - Prevents duplicate stacking.
   - Triggers a subtle **120ms scale bounce animation** (`transform: scale(1.06)` $\rightarrow$ `scale(1)`).
   - Resets the 3000ms auto-dismiss timer.
4. **Lifecycle & Animation**:
   - Slide & Fade In: 50ms post-mount removal of `translate-y-3 opacity-0`.
   - Auto-Dismiss: Auto-triggers slide-out animation at 3000ms, followed by DOM removal at 3300ms.

---

### 1.4 Drawer-Based UI Sheets (No Native Modals)
To preserve screen layout context and maintain workspace continuity, modal overlays for complex workflows are replaced with right-sliding side drawer sheets.

#### Architectural Specification
```html
<div class="fixed inset-y-0 right-0 z-50 w-full max-w-xl bg-white shadow-2xl border-l border-zinc-200 transform transition-transform duration-300 translate-x-full">
    <!-- Header with Close Button -->
    <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
        <h3 class="text-sm font-bold text-zinc-900">Drawer Title</h3>
        <button class="text-zinc-400 hover:text-zinc-900 transition-colors p-1" onclick="closeDrawer()">✕</button>
    </div>
    <!-- Scrollable Drawer Body -->
    <div class="p-6 overflow-y-auto space-y-4">
        <!-- Form Controls / Data Panels -->
    </div>
</div>
```

#### Drawer Roster Across Platform
* **Shoot Booking & Showing Drawer**: Form drawer for adding shoot schedules and site visits.
* **Lead CRM Drawer**: Tabbed panel (`General`, `Assets`, `Equipment`) for lead profiling.
* **Property Listing Drawer**: Property asset creator and editor.
* **Equipment & Gear Drawer**: Inventory tracking and asset assignment.
* **Document Share & E-Sign Drawer**: Document Vault link generator and access token management.
* **Workspace Switcher & Creator Drawer**: Super Admin workspace provisioner.
* **Feedback & Support Drawer**: In-app feedback collector.

---

### 1.5 PWA Screen Orientation Lock (v3.2.85)
To ensure optimal UI density and prevent layout breakages on handheld devices, Cora locks screen orientation to portrait mode.

1. **Manifest Lock**: Defined in `/cora-manifest.json`:
   ```json
   {
     "name": "Cora Workspace",
     "short_name": "Cora Admin",
     "start_url": "/workspace/dashboard",
     "display": "standalone",
     "orientation": "portrait-primary",
     "background_color": "#ffffff",
     "theme_color": "#ffffff"
   }
   ```
2. **JavaScript Screen Orientation API Lock (v3.2.85)**: Executed during service worker boot in `admin-dashboard.php`:
   ```javascript
   if (window.screen && window.screen.orientation && typeof window.screen.orientation.lock === 'function') {
       window.screen.orientation.lock('portrait-primary').catch(function() {});
   }
   ```

---

### 1.6 Service Worker Dynamic Cache Eviction (v3.2.84)
The service worker (`/cora-service-worker.js`) intercepts all platform requests at root scope (`/`). In **v3.2.84**, a dynamic cache eviction engine was introduced to prevent mobile browser storage bloat.

#### Cache Configuration & Bounds
```javascript
const CACHE_NAME = 'cora-workspace-v3.2.88';
const DYNAMIC_CACHE = 'cora-dynamic-v3.2.88';
const MAX_DYNAMIC_CACHE_ITEMS = 150;
```

#### FIFO Cache Eviction Algorithm
```javascript
async function trimCache(cacheName, maxItems) {
  const cache = await caches.open(cacheName);
  const keys = await cache.keys();
  if (keys.length > maxItems) {
    const toDelete = keys.slice(0, keys.length - maxItems);
    await Promise.all(toDelete.map(key => cache.delete(key)));
  }
}
```

#### Strategy Mapping Matrix
| Request Type | Caching Strategy | Target Assets / Routes | Fallback / Eviction Policy |
| :--- | :--- | :--- | :--- |
| **Scripts & Styles** | `Stale-While-Revalidate` | `.css`, `.js`, `.woff2`, `.ttf` | Background revalidation; cached assets served immediately. |
| **HTML Navigation** | `Network-First` | Full HTML page views (`/workspace/*`) | On network failure or HTTP 5xx errors (502, 503, 504), falls back to cache or `/cora-offline.html`. |
| **API & AJAX** | `Network-Only` | `admin-ajax.php`, `/wp-json/*` | Bypasses SW completely; never cached to ensure 100% data freshness. |
| **Static Assets** | `Cache-First` | Images, icons, static graphics | Served from cache; missing assets fetched and stored in dynamic cache with `trimCache(150)` FIFO eviction. |

---

### 1.7 Asset Versioning & Zero-Downtime Service Worker Lifecycle
To guarantee that users always receive updated CSS/JS bundles without stale cache issues, asset enqueues and Service Worker registrations include explicit versioning parameters.

1. **Asset Cache-Busting Parameters**:
   ```html
   <link rel="stylesheet" href="/assets/css/tailwind-built.css?v=3.2.88" />
   <link rel="stylesheet" href="/assets/css/admin-style.css?v=3.2.88" />
   ```
2. **Service Worker Registration Query Parameters**:
   ```javascript
   navigator.serviceWorker.register('/cora-service-worker.js?v=3.2.88&token=' + token, { scope: '/' });
   ```
3. **Automated SW Update Handshake**:
   - On SW update detection (`reg.addEventListener('updatefound')`), the new worker is sent a `skipWaiting` message:
     ```javascript
     newWorker.postMessage({ type: 'skipWaiting' });
     ```
   - When the new SW assumes control, the client page automatically reloads via the `controllerchange` event listener for a seamless, zero-downtime update:
     ```javascript
     var refreshing = false;
     navigator.serviceWorker.addEventListener('controllerchange', function() {
         if (!refreshing) {
             refreshing = true;
             window.location.reload();
         }
     });
     ```

---

### 1.8 Mobile Version Update Banner (v3.2.73)
Introduced in **v3.2.73**, an in-app update notification card sits sticky within the sidebar administrator popover menu (`#cora-in-app-update-notice`).

```html
<div id="cora-in-app-update-notice" class="hidden px-2 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl flex flex-col gap-1.5">
    <div class="flex items-center gap-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
        <span class="text-[10px] font-bold text-zinc-800 uppercase tracking-wide">Update Available</span>
    </div>
    <p class="text-[10px] text-zinc-500 leading-normal font-medium">New version <code class="font-mono text-zinc-700 font-bold" id="cora-update-ver">v1.4.0</code> is ready. Upgrade instantly.</p>
    <button type="button" id="cora-btn-app-upgrade" class="w-full py-1.5 bg-zinc-950 hover:opacity-85 text-white font-bold rounded-lg text-[10px] transition-colors cursor-pointer text-center select-none shadow-3xs" onclick="coraTriggerInAppUpgrade(this)">
        Upgrade Workspace
    </button>
</div>
```

* **Behavior**: Displays real-time platform upgrade availability with an animated pulse indicator.
* **One-Click Upgrade**: Clicking **Upgrade Workspace** triggers client-side service worker cache clearing and immediate asset revalidation without forcing manual browser cache resets.

---

## Section 2: Core SaaS Business Modules

### 2.1 Content AI Suite & Myra Assistant
The **Content AI Suite** is an enterprise-grade content lifecycle and SEO optimization engine. At the core of the suite is **Myra** — a floating, state-aware AI Content Manager designed specifically for high-volume content studios, digital agencies, and real estate operations.

#### Myra AI Assistant ("The Cora Expert")
Myra operates as an active workspace copilot with real-time awareness of user activity across all 7 Content Suite dashboards.
* **Floating Launcher & Copilot Drawer**: Positioned at the bottom-center of the dashboard viewport. Displays an online badge and active status.
* **Workspace State Awareness**: Extracted automatically via `getContentSuiteState()`. Myra evaluates:
  - **Active Subtab**: Current view ID (e.g., `ct-overview`, `ct-opportunities`, `ct-library`, `ct-seo`).
  - **Editor Context**: Document ID, document title, target focus keyword, and real-time word count when the full-page editor is active.
  - **Library State**: Top 3 recent articles and their workflow status (*Draft*, *In Review*, *Published*).
  - **Opportunity Pipeline**: Top 3 detected keyword opportunities along with search volume and intent impact scores.
* **Provider & Model Switching**: Supports dynamic switching between configured AI providers (Google Gemini 3.5 Flash, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o) with live token tracking.
* **Direct Workspace Action Tag Execution**: Myra can issue actionable system directives appended to text responses:
  - `[ACTION:set_title:New Title Here]`: Updates active article title.
  - `[ACTION:set_keyword:Target Keyword]`: Sets focus SEO keyword.
  - `[ACTION:insert_text:<p>Text</p>]`: Injects formatted content into the editor.
  - `[ACTION:save_article:draft]`: Triggers background auto-save.
  - `[ACTION:create_article:Title]`: Instantiates a new content draft.
  - `[ACTION:scan_opportunities:now]`: Re-runs keyword and topic gap scans.

---

### 2.2 The 7 Content Suite Dashboards
The Content Suite consolidates content operations into seven specialized, synchronized subtabs:

| Subtab | ID | Key Capabilities & Components |
|---|---|---|
| **Overview** | `ct-overview` | Timeframe selectors (`7D`, `30D`, `90D`, `12M`), top KPI cards (Total Published, Organic Sessions, AI Visibility Score, Active Workflows), and quick launcher shortcuts. |
| **Opportunities** | `ct-opportunities` | Visual conversion funnel charts (TOFU, MOFU, BOFU), topic cluster filters, keyword intent classification, search volume metrics, and automated gap detection. |
| **Calendar** | `ct-calendar` | Multi-view editorial planner featuring **Monthly**, **Weekly**, and **Kanban** subviews with drag-and-drop stage updates (*Draft*, *Scheduled*, *Published*). |
| **Content Library** | `ct-library` | Interactive Notion-styled data table with synchronized pagination, inline title editors, instant status dropdowns, tag management, and bulk operations toolbar. |
| **SEO Visibility** | `ct-seo` | Generative Engine Optimization (GEO) tracking across ChatGPT, Perplexity, Gemini, and Claude. Features 7 audit tabs: *Checklist*, *Meta Descriptors*, *Core Web Vitals*, *DOM Structure*, *Keyword Density*, *Backlink Badges*, and *AI Insights*. |
| **Performance** | `ct-performance` | Direct Google Search Console (GSC) API integration displaying organic impressions, clicks, average position, CTR graphs, and a closed-loop revenue attribution ledger. |
| **Automations** | `ct-automations` | Instant IndexNow pinger engine, automated GSC URL submission, XML sitemap auto-refresh, and AI internal linking rule triggers. |

---

### 2.3 Lead Management Suite
The **Lead Management Suite** (`views/view-leads.php`) provides an enterprise CRM pipeline designed for fast lead capture, qualification, and revenue conversion.

#### Drag-and-Drop Kanban Pipeline
* **Pipeline Stages**: *New Leads*, *Contacted*, *Qualified*, *Proposal Sent*, *Won*, *Lost*.
* **Interactive Drag-and-Drop**: Built using standard HTML5 drag-and-drop primitives with instant AJAX status synchronization.
* **Real-Time Column Recalculation**: Moving a lead card automatically updates column lead counts and aggregates deal monetary totals.

#### Lead Profiles & Right-Sliding Side Drawers
The Lead Detail Side Drawer preserves workspace context and includes:
* **Lead Metadata**: Contact name, email, phone number, GSTIN, deal value, and lead score.
* **Activity Timeline**: Chronological log of form submissions, email opens, call logs, and status updates.
* **Direct Outreach Engine**: Send templated email communications directly from the lead profile.
* **Client Conversion Action**: Single-click transformation of qualified leads into active platform Client Accounts.

---

### 2.4 Media Library & Advanced Editor
The **Media Library & Editor Suite** (`views/view-media.php`, `views/view-media-editor.php`) manages binary assets, media transformations, and SEO metadata.

#### Folder Structure & File Filtering
* **MIME Filters**: Instant client-side and server-side filtering across asset classes (*All Media*, *Images*, *Videos*, *Documents*, *Audio*).
* **Dropzone Uploader**: Multi-file drag-and-drop uploader with real-time upload progress indicators.
* **Storage Quota Meter**: Monitors disk space utilization.

#### Dynamic Crop Presets & Canvas Transformations
* **Aspect Ratio Crop Presets**: `1:1 Square`, `4:3 Standard`, `16:9 Widescreen`, and `Free Crop`.
* **Transform Tools**: Clockwise (+90°) and counter-clockwise (-90°) canvas rotation, horizontal/vertical axis flipping.
* **Dimension Rescaling**: Custom width and height pixel input fields with aspect ratio lock options.

#### SEO Metadata Manager
* **Attachment Title**: Internal title attribute.
* **Alt Text (Alternative Text)**: Essential tag required for WCAG accessibility compliance and image search ranking.
* **Caption & Description**: Form fields for frontend rendering and internal cataloging.

---

### 2.5 Email Management Suite
The **Email Suite** (`views/view-emails.php`) handles official transactional emails, promotional campaigns, and automated sequence execution.

#### Outbox & Compose Center
* **Recipient Selector**: Auto-completes client contacts and lead records.
* **Dynamic Personalization Variables**: Supports one-click tag insertion (`{{client_name}}`, `{{property_address}}`, `{{invoice_total}}`).
* **Live Preview Card**: Split-screen editor showing real-time HTML email preview.
* **Hostinger Business SMTP Integration**: Connected directly to `smtp.hostinger.com` (Port 587/465) with a dynamic connection status badge and test outbox logs.

---

### 2.6 Document Vault & Document Studio
The **Document Vault** (`views/view-vault.php`) manages business documentation, automated GST tax calculations, and legally binding E-Sign workflows.

#### Guided 5-Step Stepper Wizard
1. **Step 1 (Document Type & Details)**: Select category (*Proposal*, *Invoice*, *Contract*, *SLA*) and document number prefix.
2. **Step 3 (Line Items & SAC Codes)**: Add billable items, quantities, rates, and Service Accounting Codes (SAC 998381 / 997212).
3. **Step 4 (GST Math & Payment Terms)**: Automatic tax breakdown calculation and Place of Supply (POS) validation.
4. **Step 5 (E-Sign Auditing & Verification)**: Apply digital signatures, watermark overlays, compile PDF, and generate share links.

#### GST Tax Calculation Breakdown Card
The Document Studio contains an automated Indian GST engine adhering to state tax rules:
* **Intra-State Transaction (Same State POS)**: CGST (9%) + SGST (9%).
* **Inter-State Transaction (Different State POS)**: IGST (18%).

---

## Section 3: Canvas Theme Builder & Elementor/Lovable Integration

### 3.1 Draft vs. Live Theme State Management
Cora implements a strict dual-state theme architecture managed via the database table `{$wpdb->prefix}cora_canvas_themes`.

* **Draft Themes**: Isolated sandbox for styling and layout experimentation. Accessible only via preview parameters (`?preview=true&cv_theme=ID`).
* **Live Theme**: Production-active styling and global theme context. served to all public site visitors. Exactly 1 live theme is active per agency workspace.

---

### 3.2 Pages & Navigation Menu Management
* **Dual-Layer Storage**: `cora_canvas_pages` stores workspace-specific layout types (`header`, `footer`, `single`, `archive`, `error-404`, `page`), SEO fields, and agency tenancy locks, while delegating content rendering to Elementor via `wp_posts.ID`.
* **Bidirectional Navigation Menu Synchronization**: Navigation menus created in Canvas automatically synchronize two-ways with native WordPress menus (`wp_terms`, `wp_term_taxonomy`, `wp_term_relationships`). Any menu item reordered, added, or modified inside Canvas updates the underlying WordPress term taxonomy, ensuring Elementor Nav Menu widgets render updated menu trees instantly.

---

### 3.3 Elementor Editor Reskin & White-Labeling
The native Elementor top bar is replaced with a custom, high-density 2-row Cora toolbar (`cora-elementor-reskin.js` and `cora-elementor-reskin.css`).

#### Row 1: Context & Metadata Bar
1. **Brand & Navigation**: Cora typography logo (`CORA`) and `Theme Dashboard` back button.
2. **Breadcrumb Trail**: Dynamic path displaying system section (`Theme Builder`), document title, and document type badge (`HEADER`, `FOOTER`, `PAGE`).
3. **Save Status Indicator**: Live status dot (Green for Live, Slate for Draft) and status text.

#### Row 2: Tooling & Action Controls
1. **Widget Panel Trigger (`+ Add`)**: Invokes `$e.run('document/elements/deselect-all')` to trigger Elementor's editor lifecycle.
2. **Library & Extensions**: Direct access to `Templates` modal, `Git` version control drawer, and Page `Settings`.
3. **History Controls**: Native `Undo` and `Redo` triggers.
4. **Responsive Device Switcher**: Toggles viewport breakpoint modes (`desktop`, `tablet`, `mobile`).
5. **Publish Split Button**: Primary `Publish` button paired with a dropdown chevron offering `Save Draft`.

#### Total White-Labeling & Branding Scrubbing
* **Native Header Removal**: Hides Elementor MUI AppBars and top bar headers.
* **WordPress Admin Bar Scrubbing**: Suppresses `#wpadminbar` and hides all panel menu items referencing "WordPress" or "Exit to Dashboard".
* **Upsell & Notice Suppression**: Strips promotional banners, upgrade notices, and plugin install CTAs.
* **AI Tooltip & Sparkle Scrubbing**: Removes Elementor Angie AI sparkle buttons and tooltips.

---

### 3.4 Git & Lovable AI Integration
* **OAuth 2.0 Device Flow**: Generates a user code and directs the user to `github.com/login/device` while polling token state.
* **Automated Repository Creation**: Creates a private repository under the user's GitHub account named after the workspace (e.g. `cora-my-agency-site`).
* **Auto-Commit on Publish**: Listening hooks automatically construct a commit payload whenever a user publishes changes, pushing the commit to GitHub.
* **Lovable AI Prompting Bridge**: Constructs structured design prompts enforcing the Cora notion/monochromatic design system, color palettes, and component hierarchy.
