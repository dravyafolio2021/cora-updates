# Cora Platform — Comprehensive Platform Documentation

This document serves as the master technical specification and architectural manual for the Cora Workspace Platform (v3.4.28).

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
2. **Duplicate Deduplication & Scale Bounce**: If a toast with identical message text is dispatched while already visible, prevents duplicate stacking and triggers a subtle 120ms scale bounce animation.
3. **Lifecycle & Animation**: Slide and fade in at 50ms, auto-dismiss at 3000ms with slide-out.

---

### 1.4 Drawer-Based UI Sheets (No Native Modals)
To preserve screen layout context and maintain workspace continuity, modal overlays for complex workflows are replaced with right-sliding side drawer sheets.

#### Drawer Roster Across Platform
* **Shoot Booking & Showing Drawer**: Form drawer for adding shoot schedules and site visits.
* **Lead CRM Drawer**: Tabbed panel (`General`, `Assets`, `Equipment`) for lead profiling.
* **Property Listing Drawer**: Property asset creator and editor.
* **Equipment & Gear Drawer**: Inventory tracking and asset assignment.
* **Document Share & E-Sign Drawer**: Document Vault link generator and access token management.
* **Workspace Switcher & Creator Drawer**: Super Admin workspace provisioner.
* **Feedback & Support Drawer**: In-app feedback collector.

---

### 1.5 PWA Architecture & Onboarding (v3.3.0+)

#### PWA Onboarding Wizard (v3.3.0)
When a workspace is first accessed on a mobile device, Cora displays a premium PWA onboarding wizard that guides users through the "Add to Home Screen" installation flow:
* **Animated Loading Splash Screen**: Full-screen premium branded splash during initial asset load.
* **Step-by-Step Install Instructions**: Visual guide for iOS Safari Share and Android Chrome Install App flows.
* **Persistent Preference Storage**: Tracks `cora-pwa-installed` in `localStorage` to prevent repeat prompts.

#### Screen Orientation Lock (v3.2.85)
1. **Manifest Lock**: `"orientation": "portrait-primary"` in `/cora-manifest.json`.
2. **JavaScript Screen Orientation API Lock**: Executed during service worker boot.
3. **Landscape Shield Overlay (v3.3.0)**: A full-screen blocking overlay with rotation icon displayed when users rotate to landscape.

#### Service Worker Dynamic Cache Eviction (v3.2.84)
| Request Type | Caching Strategy | Target Assets / Routes |
| :--- | :--- | :--- |
| **Scripts & Styles** | `Stale-While-Revalidate` | `.css`, `.js`, `.woff2`, `.ttf` |
| **HTML Navigation** | `Network-First` | Full HTML page views (`/workspace/*`) |
| **API & AJAX** | `Network-Only` | `admin-ajax.php`, `/wp-json/*` |
| **Static Assets** | `Cache-First` | Images, icons, static graphics (FIFO eviction at 150 items) |

---

### 1.6 Deployment Pipeline & Atomic Backup Strategy (v3.3.9)
* **Atomic Backup**: Uses `mv` instead of `cp -r` for backup operations to avoid symlink stat errors on the target server.
* **Build Script** (`scripts/build.sh`): Validates version consistency across plugin header, `CORA_WORKSPACE_VERSION` constant, and `updates/cora-workspace.json` manifest.
* **Release Artifacts**: Packaged at `updates/cora-workspace.zip` with versioned manifest at `updates/cora-workspace.json`.

---

## Section 2: Core SaaS Business Modules

### 2.1 Content AI Suite & Myra Assistant
The **Content AI Suite** is an enterprise-grade content lifecycle and SEO optimization engine. At the core is **Myra** — a floating, state-aware AI Content Manager.

#### Myra AI Assistant
* **Floating Launcher & Copilot Drawer**: Bottom-center position with online badge. Collapsible panel design (v3.4.0).
* **Workspace State Awareness**: Evaluates active subtab, editor context (document ID/title/keyword/word count), library state, and opportunity pipeline.
* **Provider & Model Switching**: Google Gemini 3.5 Flash, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o with live token tracking.
* **Action Tag Execution**: `[ACTION:set_title]`, `[ACTION:set_keyword]`, `[ACTION:insert_text]`, `[ACTION:save_article]`, `[ACTION:create_article]`, `[ACTION:scan_opportunities]`.

### 2.2 Content Editor (Quill WYSIWYG) — v3.3.0+
* **Sticky Docked Toolbar (v3.4.0)**: Remains visible while scrolling through long-form content.
* **Slash Command Hint**: Placeholder "Type / for commands..." for quick formatting access.
* **Document Outline & Metrics (v3.3.9)**: Real-time word count, character count, paragraph count, reading time.
* **Mobile Quick Action Bar (v3.3.7)**: Compact floating bar with Bold, Italic, Link, Heading, List.
* **Landscape Auto-Rotate Lock (v3.4.0)**: Enforces vertical scroll in editor.

### 2.3 The 7 Content Suite Dashboards
| Subtab | ID | Key Capabilities |
|---|---|---|
| **Overview** | `ct-overview` | KPI cards, timeframe selectors, quick launchers |
| **Opportunities** | `ct-opportunities` | Funnel charts, topic clusters, keyword intent |
| **Calendar** | `ct-calendar` | Monthly/Weekly/Kanban editorial planner (v3.3.1 day number fix) |
| **Content Library** | `ct-library` | Notion-styled data table, pagination, inline editors |
| **SEO Visibility** | `ct-seo` | GEO tracking, 7 audit tabs, backlink badges |
| **Performance** | `ct-performance` | GSC API integration, CTR graphs |
| **Automations** | `ct-automations` | IndexNow, GSC submission, sitemap refresh |

### 2.4 Lead Management Suite
* **Kanban Pipeline**: Drag-and-drop across *New*, *Contacted*, *Qualified*, *Proposal Sent*, *Won*, *Lost*.
* **Lead Detail Drawer**: Metadata, activity timeline, direct outreach, client conversion.

### 2.5 Media Library & Advanced Editor
* **MIME Filters**, **Dropzone Uploader**, **Storage Quota Meter**
* **Crop Presets**: 1:1, 4:3, 16:9, Free Crop with rotation and flipping.
* **Left Sidebar Controls (v3.4.0)**: Segment tabs, media card presets, locate and delete mapping.
* **SEO Metadata Manager**: Alt text, caption, description fields.

### 2.6 Email Management Suite
* **Outbox & Compose**: Recipient auto-complete, personalization variables, live HTML preview.
* **Hostinger SMTP Integration**: Port 587/465, connection diagnostics.

### 2.7 Document Vault & Document Studio
* **5-Step Wizard**: Document type, line items with SAC codes, GST math, e-sign audit.
* **GST Engine**: Auto CGST/SGST (intra-state) or IGST (inter-state) calculation.

### 2.8 Forms & Review Acquisition
* Multi-channel review settings, WhatsApp automation with Hinglish presets.
* Public review portal at `view-public-review-portal.php`.

### 2.9 Crew Scheduler & Equipment Management
* **Crew Scheduler**: Timeline-based crew assignment for studio shoots.
* **Equipment Manager**: Asset check-in/check-out lifecycle.
* **Client Task Manager**: Task assignment and progress tracking.

### 2.10 Financial Module & Event Timeline
* **Financials**: Revenue tracking, payment status monitoring.
* **Event Timeline**: Chronological activity feed across all modules.

### 2.11 Workspace Calendar Subsystem (v3.4.28)
The Workspace Calendar acts as the master planning scheduler for photography studio shoots, client site visits, and team shifts:
* **Multi-View Scheduling Planner**: Full support for Monthly grid calendars, Weekly timeline grids, and Daily agenda planner layouts.
* **5-Step Guided Event Wizard Modal**: Dynamic, step-by-step event creation wizard prompting details (Title, Type, Date, Assignee, Notifications) inside a clean overlay modal.
* **Item Persistence**: Custom calendar day numbers remain visible and styled even when cells are populated with multi-day events or crew allocations.

---

## Section 3: Canvas Theme Builder & Elementor/Lovable Integration

### 3.1 Draft vs. Live Theme State Management
* **Draft Themes**: Isolated sandbox accessible via `?preview=true&cv_theme=ID`.
* **Live Theme**: Production-active, exactly 1 per agency.

### 3.2 Pages & Navigation Menu Management
* **Dual-Layer Storage**: Canvas pages map to WordPress posts for Elementor rendering.
* **Bidirectional Menu Sync**: Canvas menus sync two-ways with WordPress `nav_menu` taxonomy.

### 3.3 Elementor Editor Reskin & White-Labeling
* **2-Row Custom Toolbar**: Context bar + tooling controls.
* **Complete White-Labeling**: Native header, admin bar, upsells, and AI tooltips all removed.

### 3.4 Git & Lovable AI Integration
* **OAuth 2.0 Device Flow** for GitHub authentication.
* **Auto-Commit on Publish** to linked repository.
* **Lovable AI Prompting Bridge** enforcing Cora design system.

---

## Section 4: Public Developer Documentation Portal (v3.2.90+)

### 4.1 Three-Column Notion-Like Layout
The `/docs` endpoint renders a premium three-column documentation portal.

| Component File | Purpose |
| :--- | :--- |
| `view-public-docs.php` | Master layout container |
| `view-public-docs-header.php` | Sticky header with branding, search, actions |
| `view-public-docs-sidebar.php` | Left navigation with collapsible categories |
| `view-public-docs-content.php` | Main prose content with feature cards |
| `view-public-docs-widgets.php` | Right AI Playground panel |
| `view-public-docs-search.php` | Command palette search overlay and AJAX router |

### 4.2 AI Playground Sidebar (v3.2.94 to v3.2.101)
* RAG-powered chatbot, suggested quick questions, streaming responses.

### 4.3 Command Palette Search
* `Cmd+K` / `Ctrl+K` overlay with keyboard navigation and AJAX page loading via `history.pushState`.

### 4.4 Mobile Responsiveness (v3.4.0)
* **Mobile**: Single column, hamburger sidebar overlay, simplified header.
* **Tablet**: Two columns (sidebar + content).
* **Desktop**: Full three-column layout.

---

## Section 5: UI Shell & Standardized Page Layouts (v3.4.28)

### 5.1 Standardized Header Action Bar
All active workspace subviews (Calendar, Event Timeline, Analytics, Email Suite, Financials, Media Library, Forms & Reviews, Settings Suite, Modules Hub, and Google Profile View) adhere to a unified page header design framework:
* **Branding & Visuals**: Titles are styled using high-contrast bold margins and Outfit display headings, aligned with description subtitles detailing key module workflows.
* **Integrated AI Platform Shortcuts Stack**: An overlapping brand icon stack (ChatGPT, Gemini, Claude, Perplexity, YouTube) is positioned inside the header, providing 1-click workspace assistance and redirection.
* **On-Demand Tutorial Walkthroughs**: A dedicated YouTube tutorial trigger button is standard on all headers, opening helpful walkthrough guides inside side drawer sheets.

### 5.2 Dynamic Module Access & Locked States
* **Padlock Sidebar Indicators**: Sidebar navigation targets that are locked or unavailable on the active workspace subscription tier (e.g., Social Suite, Inbox, Automations) dynamically replace standard icons with padlock vectors.
* **Premium Locked State Card**: Attempting to access these locked pages renders a premium, centralized monochromatic subscription callout card rather than standard blanks, requesting updates or quota elevation.
* **Custom Workspace Mode Toggling**: Enables administrators to personalize workspace configurations by toggling specific operational sub-tabs or marketing features.

---

## Section 6: Multi-Tenant Database Architecture

### 6.1 Core Custom Tables
| Table | Purpose |
| :--- | :--- |
| `cora_agencies` | Root tenant isolation |
| `cora_branches` | Sub-office segmentation |
| `cora_leads` | Lead CRM pipeline |
| `cora_clients` | Converted client accounts |
| `cora_bookings` | Showings and shoot bookings |
| `cora_ledger` | Financial transaction log |
| `cora_canvas_themes` | Theme builder themes |
| `cora_canvas_pages` | Theme builder pages |
| `cora_documents` | Document vault records |

### 6.2 Agency Isolation Pattern
All queries filter by `agency_id`. Owner roles see all branches; branch-level roles are filtered by `branch_id`.

---

## Section 7: AI Integration & MCP Gateway

* **Multi-Provider AI Routing**: Gemini 3.5 Flash, Claude 3.5 Sonnet, GPT-4o with automatic fallback.
* **RAG Knowledge Base** (`views/view-rag.php`): Per-tenant workspace knowledge sync.
* **MCP Gateway** (`views/view-mcp.php`): JSON-RPC over WebSockets with role-based permissions.

---

## Section 8: Testing & Quality Assurance

* **Playwright E2E**: Tiered test suites (Tier 1-4) covering auth, CRUD, integration, and workload flows.
* **Build Validation**: `scripts/build.sh` checks version consistency and packages `updates/cora-workspace.zip`.

---

## Section 9: Notification Management System & Event Trigger Engine (v3.4.38)

The Notification Management Module (`settings-suite?settings_tab=notifications`) provides centralized, studio-grade notification preference controls, multi-channel routing, quiet hours enforcement, automated email digests, and real-time event triggers across the Cora Platform.

```
+-------------------------------------------------------------------------+
|                        Workspace Event Trigger                          |
|         (e.g., Lead Created, Shoot Booked, Invoice Paid, E-Sign)         |
+------------------------------------+------------------------------------+
                                     |
                                     v
                        [ cora_notify() Dispatcher ]
                                     |
         +---------------------------+---------------------------+
         |                           |                           |
         v                           v                           v
  [ In-App Bell ]            [ Web Push (PWA) ]          [ Email Routing ]
  • wp_cora_notifications    • VAPID ES256 Push          • User preference check
  • Topbar count badge       • Lock-screen alert         • Instant: wp_mail()
  • Slide drawer history     • DND Quiet Hours check     • Digest: Daily / Weekly
                                                         • WP-Cron hourly batch
```

---

### 9.1 Multi-Channel Delivery Infrastructure

1. **In-App Notification Bell Channel**:
   * Stored canonically in the database table `{$wpdb->prefix}cora_notifications` with legacy fallback support.
   * Real-time unread badge counter in the topbar and dedicated slide-out notification drawer.
   * Supports one-click "Mark as Read" and "Clear All".

2. **Web Push Notifications (PWA VAPID ES256)**:
   * Native browser and lock-screen alerts using Web Push API standards (RFC 8292).
   * Cryptographic ES256 authentication using local OpenSSL VAPID key pairs.
   * Device subscription sync status indicator (`Active & Subscribed`, `Ready / Not Synced`, `Unsupported`).

3. **Monochromatic Transactional HTML Email Channel**:
   * High-contrast Notion/Claude-style responsive HTML email template generator (`cora_send_monochromatic_notification_email`).
   * Clean typography, metadata category badge, body text formatting, direct workspace action CTA button, and notification management footer links.
   * Dispatched natively via `wp_mail()` with whitelabel sender headers.

---

### 9.2 Granular Trigger & Channel Routing Matrix

Users can customize In-App, Push, and Email delivery frequency independently for every workspace trigger:

| Category | Trigger Event | Trigger Key | Default Channels | Default Email Mode |
| :--- | :--- | :--- | :--- | :--- |
| **CRM & Leads** | New Lead Captured | `lead_created` | In-App, Push, Email | Instant |
| | Pipeline Stage Changed | `lead_status_changed` | In-App, Push | Daily Digest |
| | Lead Reassigned to User | `lead_reassigned` | In-App, Push, Email | Instant |
| | Follow-up Reminder Due | `lead_followup_reminder` | In-App, Push, Email | Instant |
| **Bookings** | New Shoot Scheduled | `booking_created` | In-App, Push, Email | Instant |
| | Booking Rescheduled | `booking_rescheduled` | In-App, Push, Email | Instant |
| | 24h & 1h Shoot Reminder | `booking_reminder` | In-App, Push, Email | Instant |
| | Crew / Photographer Assigned | `crew_assigned` | In-App, Push, Email | Instant |
| **Financials** | New Invoice Issued | `invoice_created` | In-App, Push, Email | Instant |
| | Payment Logged & Cleared | `payment_received` | In-App, Push, Email | Instant |
| | Overdue Invoice Alert | `invoice_overdue` | In-App, Push | Daily Digest |
| | Revenue & Tax GST Digest | `financial_summary` | In-App, Email | Weekly Digest |
| **Document Vault** | Document Sent for E-Sign | `doc_sent_sign` | In-App, Push, Email | Instant |
| | Agreement Viewed by Client | `doc_viewed` | In-App | Daily Digest |
| | Document Fully Executed | `doc_signed` | In-App, Push, Email | Instant |
| | Agreement Expiring Soon | `doc_expiring` | In-App, Push | Daily Digest |
| **Team & Shifts** | Team Member Joined | `team_member_joined` | In-App | Daily Digest |
| | Shift / Roster Assigned | `shift_assigned` | In-App, Push, Email | Instant |
| | Attendance Punch Reminder | `attendance_reminder` | In-App, Push | Never |
| | Role / Permission Changed | `role_changed` | In-App, Push, Email | Instant |
| **Security & System** | New Device Login Detected | `security_login` | In-App, Push, Email | Instant (Urgent) |
| | System Backup Completed | `backup_completed` | In-App | Weekly Digest |
| | AI Token Quota Alert | `ai_quota_alert` | In-App, Push, Email | Instant (Urgent) |

---

### 9.3 Periodicity & Digest Engine

* **Instant Mode**: Real-time delivery via `wp_mail()` immediately when an event occurs.
* **Daily Digest Mode**: Non-urgent notifications are queued in `cora_notification_digest_queue` and dispatched as a consolidated morning briefing at 09:00 AM local time.
* **Weekly Digest Mode**: Queued events are compiled and delivered every Monday morning at 09:00 AM.
* **WP-Cron Execution**: Handled automatically by the hourly cron job `cora_cron_notification_digest_hook`.

---

### 9.4 Quiet Hours & Do Not Disturb (DND)

* Users can set customized quiet hours (e.g. `22:00` to `08:00`).
* During active quiet hours, non-urgent Web Push notifications and instant emails are held and queued for morning delivery.
* Critical security and high-priority payment alerts (`urgent => true`) automatically bypass DND rules.

---

## Section 10: Finance AI Co-founder & Financial Intelligence System (v3.4.44)

Starting in **version 3.4.44**, the Financial Overview module has been completely rebuilt from a traditional static ledger into an **AI Financial Co-founder** designed specifically for solo founders, creative agencies, and service businesses.

```
+-----------------------------------------------------------------------------------+
|                           CORA FINANCIAL AI CO-FOUNDER                            |
|  [ Watch Background ] -> [ Detect Risks ] -> [ Explain Why ] -> [ Prepare Action ] |
+-----------------------------------------------------------------------------------+
```

---

### 10.1 Core Philosophy & Interaction Model
Instead of forcing founders to manage bookkeeping inside a complex accounting ledger, Cora proactively:
1. **Watches** all bank inflows, receivables aging, GST tax reserves, and recurring commitments in the background.
2. **Explains** the financial impact in plain English (runway, tax liabilities, top-heavy revenue concentration).
3. **Prepares and Executes** proactive next actions (1-click client payment reminders, 3-step GST invoices linked to E-Sign contracts, deal margin stress testing).

---

### 10.2 Sub-Tab Navigation & Dynamic URL Synchronization
The Financial module is partitioned into 6 focused sub-tabs. All tabs dynamically synchronize their active state with browser history (`?tab=fin-*` and `#fin-*`), ensuring that page refreshes and direct links preserve the exact view and re-render interactive Chart.js canvases:

| Tab ID | Tab Title | Core Utility |
| :--- | :--- | :--- |
| `fin-home` | **Overview** | Executive morning briefing, 4 snapshot metrics, attention cards, 6-month historical & 90-day predictive trajectory chart. |
| `fin-receivables` | **Receivables** | Aging buckets (Overdue, Due Soon, Paid), state-level GST classifications, 1-click AI payment reminders. |
| `fin-expenses` | **Money Out** | Outflow ledger, recurring software & lease subscriptions, annual run-rate forecasts. |
| `fin-profitability` | **Profitability** | Client margin matrix (70%+ high-tier), revenue concentration doughnut chart, Deal Simulator trigger. |
| `fin-forecast` | **Forecast** | 30/60/90-Day predictive cash trajectories, seasonal warnings, and runway indicators. |
| `fin-tax` | **Tax & GST** | Output CGST/SGST/IGST breakdown, Input Tax Credit (ITC) offsets, net GST payable, quarterly advance tax reserve calculator. |

---

### 10.3 4 Snapshot Financial Pillars & Morning Briefing
* **Available Cash**: Net liquid working capital computed atomically from master ledger inflows minus outflows.
* **Expected In (30 Days)**: Sum of all pending and overdue client receivables due within the next 30 days.
* **Expected Out (30 Days)**: Projected recurring software commitments, studio leases, contractor payouts, and verified tax reserves.
* **Projected 30-Day Buffer & Runway**: Net liquidity after expected collections and committed payouts, accompanied by total months of operational runway.
* **Dynamic "Cora's Take" Briefing**: An actionable natural-language summary comparing current cash buffer against monthly burn rate, highlighting overdue receivables, and warning against single-client revenue concentration (>40%).

---

### 10.4 Action-Oriented Attention Cards & 1-Click Reminders
When invoices become overdue or subscriptions approach renewal, Cora surfaces proactive action cards at the top of the dashboard:
* **Overdue Receivable Follow-Up**: Clicking **"Remind Client"** opens a right-sliding drawer (`#cora-fin-followup-drawer`) with 3 pre-drafted tone templates (*Polite Check-in*, *Firm Professional*, *Urgent Final Notice*).
* **Multi-Channel Dispatch**: Founders can send the reminder directly via **Email** or copy a **WhatsApp Direct** link with invoice reference and payment terms pre-formatted.

---

### 10.5 Indian GST Multi-Step Dynamic Invoice Creator
The invoice creator operates as a 3-step right-sliding drawer (`#cora-fin-invoice-drawer`):
* **Step 1 (Client & Place of Supply)**: 1-click CRM lead auto-fill (`wp_cora_leads`), client billing address, GSTIN, and dynamic Place of Supply state picker that auto-detects Intra-State (CGST 9% + SGST 9%) vs Inter-State (IGST 18%).
* **Step 2 (Line Items & SAC Codes)**: Dynamic item rows with industry-standard Service Accounting Codes (`998386` Commercial Photography, `998314` Video Post-Production), unit rates, quantities, and live GST math breakdown.
* **Step 3 (Terms & Vault E-Sign Linking)**: Milestone payment schedule configuration and 1-click integration with the **Document Vault** to automatically bind legal agreements and E-Sign contracts to invoice balances.

---

### 10.6 Dynamic User-Customizable Categories System
Categories are fully customizable rather than rigid:
* **Inline Add Option**: Selecting `+ Add Custom Category...` or clicking `+ Custom` in Expense and Subscription drawers displays an inline creation field.
* **Workspace Persistence**: New categories are dynamically injected into DOM selectors across drawers and permanently saved to `cora_custom_expense_categories` via `cora_ajax_finance_save_category`.

---

### 10.7 Deal Feasibility Simulator ("Should I Take This Project?")
The Deal Simulator drawer (`#cora-fin-sim-drawer`) allows solo founders to stress-test prospective client quotes before committing:
* Calculates quoted revenue minus direct subcontractor fees, equipment rentals, travel/food logistics, and mandatory 18% GST tax reserves.
* Evaluates calculated net margin against agency target thresholds (e.g. 45%+) and delivers an instant AI Feasibility Verdict (**High Margin Go** 🟢, **Moderate Margin Review** 🟡, or **Low Margin Warning** 🔴).

---

### 10.8 Ask Cora Financial Intelligence Copilot
* **Desktop & Tablet**: A persistent floating pill positioned cleanly above the workspace footer, which smoothly expands into a Claude Cream styled (`#FBFaf7`) financial advisory window.
* **Pre-Baked Prompts**: Instant 1-click queries (*"How much can I safely withdraw as owner pay?"*, *"Who owes me money and is past due?"*, *"Can I afford a ₹1.5L camera gear upgrade?"*, *"What is my estimated GST liability this quarter?"*).
* **Responsive Mobile Isolation**: The floating copilot bar is cleanly hidden on mobile viewports (`< 1024px`) to yield to the global mobile AI search bar without visual stacking.

---

### 10.9 Financial Developer API Reference

| AJAX Action | HTTP Parameters | Response / Behavior |
| :--- | :--- | :--- |
| `cora_ajax_finance_ask_cora` | `security`, `query` | Context-aware AI financial advisory response with optional direct action chips. |
| `cora_ajax_finance_record_expense` | `security`, `description`, `amount`, `category`, `vendor`, `date`, `is_recurring` | Writes outflow to `wp_cora_ledger` and registers subscription if marked recurring. |
| `cora_ajax_finance_record_income` | `security`, `client_name`, `amount`, `date`, `invoice_id` | Writes inflow to `wp_cora_ledger` and marks linked invoice balance as paid. |
| `cora_ajax_finance_save_category` | `security`, `category` | Saves custom category to `cora_custom_expense_categories` workspace options. |
| `cora_ajax_finance_send_reminder` | `security`, `invoice_id`, `recipient_email`, `tone`, `message` | Dispatches monochromatic payment reminder email via `wp_mail()`. |
| `cora_ajax_finance_export_pack` | `security`, `period`, `include_invoices`, `include_expenses` | Generates a consolidated ZIP package with GST CSV ledgers for external CA audit. |

---

*Cora Platform v3.4.44 — Last updated: August 15, 2026.*


