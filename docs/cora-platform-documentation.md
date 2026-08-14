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

### 9.5 Developer API Reference

#### Central Dispatcher: `cora_notify()`
```php
cora_notify(
    string $event_key,       // e.g., 'lead_created', 'booking_created', 'doc_signed'
    int|array|string $target, // User ID, array of user IDs, or 'agency_owners'
    array $payload = array(
        'title'      => 'New Lead Captured',
        'body'       => 'Inquiry from Dravya Bansal entered your pipeline.',
        'action_url' => home_url( '/workspace/dashboard?sub_page=leads' ),
        'category'   => 'CRM & Leads',
        'urgent'     => false,
    )
);
```

#### Preferences Helpers:
```php
// Retrieve parsed preferences with fallbacks
$prefs = cora_get_user_notification_prefs( $user_id );

// Persist sanitized preferences
cora_save_user_notification_prefs( $user_id, $_POST );
```

---

*Cora Platform v3.4.38 — Last updated: August 14, 2026.*

