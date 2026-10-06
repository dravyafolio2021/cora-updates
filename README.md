# Cora Platform

> Multi-tenant SaaS workspace engine for Indian service agencies — Photography Studios, Real Estate Brokerages, Marketing Agencies, Stationery Manufacturing Plants & Professional Services.

---

## Overview

Cora is a full-stack, enterprise-grade WordPress-based SaaS platform that provides white-labeled, multi-tenant workspace dashboards. Each workspace operates as an isolated business environment equipped with Lead CRM & Pipeline Kanban, Content AI Suite with Bulk Operations, Financial Intelligence, Team Governance & Dynamic Custom Roles, Active-Only Equal AI Token Allocation, Field Ops & Geolocation Live Tracking with Beacon-backed telemetry, Universal Foundation Modules Locking (6 core modules + dashboard), Media Proofing Portal with route interception and client telemetry, Multi-Dimensional Workspace Storage Footprint, Form AI Architect with 15 field types and live preview, Stationery Manufacturing & Field Van Sales POS, Single Consolidated 24-Hour Executive PDF Reporting, Visual Website Canvas with 4-metric diagnostic strip, Universal Website Migrator, Continuous Hands-Free Voice AI, Multimodal Team Migration, Enterprise AI Safety Guardrails, Central Tenant Authorization Engine, Outbound Request SSRF Filters, AI Tools MCP Developer Gateway with Live Voice Canvas, Gamified Affiliate & Referral Ecosystem, Cryptographic Email Verification Link System with early route interception, Mobile GPU Hardware Acceleration, and per-tenant module customization.

* **Current Version**: `v4.9.364`
* **Supported Verticals**: Photography Studio (`photography_studio`), Real Estate Brokerage (`real_estate`), Marketing Agency (`marketing_agency`), Stationery Manufacturing & Van Sales (`stationery_inventory` / `manufacturing`), Professional Services & Consulting Agency (`professional_services`), Custom Workspace (`custom-workspace`)
* **Tech Stack**: WordPress 6.x (Locked Down Backend), PHP 8.2+, Tailwind CSS (Monochromatic Zinc Ramp), JavaScript (ES6+), Leaflet.js, Next.js 16.3+, Expo React Native Web, Quill.js, Elementor, Sandboxed Visual HTML Engine
* **AI Providers**: Google Gemini 3.5 Flash / Pro Multimodal, Anthropic Claude 3.5 Sonnet, OpenAI GPT-4o, Groq Ultra-Fast

---

## Architecture

```
cora/
├── app/public/wp-content/plugins/
│   ├── cora-workspace/          # Core platform plugin (v4.9.364)
│   │   ├── admin-dashboard.php  # Main dashboard controller, navigation, PWA manifest & dynamic routing
│   │   ├── cora-workspace.php   # Core AJAX handlers, hooks, static site streaming, micro-cache, RAG & AI Safety
│   │   ├── share-media.php      # Public media proofing portal with route interception & telemetry
│   │   ├── public-client-portal.php # Public white-labeled client portal with Claude aesthetic
│   │   ├── includes/            # Backend engines (auth, ssrf, affiliates, inventory, docs, RAG, MCP, PWA, tour, Next.js)
│   │   │   ├── class-cora-authorization.php # Central tenant authorization & policy gatekeeper (SEC-002)
│   │   │   ├── class-cora-ssrf-filter.php   # Outbound HTTP SSRF protection filter (SEC-009)
│   │   │   ├── class-cora-whatsapp-gateway.php # WhatsApp Cloud API with HMAC-SHA256 verification (SEC-005)
│   │   │   ├── class-cora-nextjs-integration.php # Next.js/Expo Git Connector, Static Exporter Pipeline, REST APIs & ISR Revalidation
│   │   │   ├── class-cora-inventory-engine.php # Extended 3-step Van Dispatch Wizard, Weight Allocation @ ₹401.25/kg, Spot Billing, Sales Register, Van Stock Print & Itemized CSV Export
│   │   │   ├── mcp/                         # Universal Remote MCP Server & OAuth 2.1 Engine (RFC 7591 / RFC 9728)
│   │   │   │   ├── class-cora-universal-mcp-server.php # Remote MCP Server (SSE & JSON-RPC)
│   │   │   │   ├── class-cora-oauth-server.php         # OAuth 2.1 PKCE Server (RFC 7591 / RFC 9728)
│   │   │   │   └── class-cora-mcp-tool-registry.php    # Scoped Multi-Tenant Tool Registry (Curated 28 Core Actions)
│   │   │   ├── affiliate-referral-engine.php # Affiliate attribution, dual rewards & payout engine
│   │   │   └── class-cora-html-website-migrator.php # Universal website scraper & theme scaffolder
│   │   ├── modules/             # Modular industry domain engines & permanent foundation definitions
│   │   ├── views/               # 50+ modular PHP view files (Clients, Forms, Media, Tasks, Users, Verify, Inventory, Canvas, etc.)
│   │   │   └── verify.php       # Early route interception view for email verification & auto-login
│   │   └── assets/              # JS (Field Ops, Voice AI, Admin UI), CSS, PWA service worker, versioned & admin-badged icons
│   ├── cora-real-estate/        # Real estate industry extension
│   ├── cora-studio-ai/          # Photography studio extension
│   └── cora-frontend/           # Marketing frontend module (Next.js 16.3+, Sharp 0.35+)
├── cora-chatgpt-plugin/         # Official ChatGPT Plugin & Remote MCP 2.0 distribution package
│   ├── manifest.json            # OpenAI plugin manifest
│   ├── openapi.json             # Complete OpenAPI 3.0.1 specification (Curated 28 tools ceiling)
│   ├── mcp.json                 # Remote MCP 2.0 server configuration
│   └── skills/                  # Core workflow definitions for AI assistants
├── docs/                        # Master technical documentation suite
├── tests/                       # Playwright E2E test suites (Tier 1-4, Canvas, Affiliates, Emails)
├── scripts/                     # Build, deploy, PWA icon generation, account provisioning & security test scripts
├── updates/                     # Release artifacts (.zip + .json manifests)
└── .agents/                     # AI agent configuration & workspace rules
```

---

## Core Modules & Capabilities

| Module | Primary View | Description |
| :--- | :--- | :--- |
| **Workspace Dashboard** | `admin-dashboard.php` | Adaptive workspace landing with centered 60% max-width KPI scorecards (guaranteed 4 cards: 2x2 mobile, 1x4 desktop), moving purple gradient AI pulse trigger, dynamic user display name resolution, desktop agenda task card, and Interactive Platform Tour |
| **CRM & Leads High-Velocity Pipeline** | `view-leads.php`, `cora-workspace.php` | High-velocity lead funnel featuring full-height edge-to-edge side drawers auto-portaled to `document.body` with transparent backdrop (zero dark overlay), interactive left draggable resizer with persistent width, collapsible accordion groups, team assignee roster sanitization, and bi-directional Kanban card sync |
| **Client Management Suite** | `view-clients.php` | 4-subtab client management hub with Overview/Directory, Active Projects & Deals, Client Tasks (Kanban), and automated Financials ledger sync |
| **Client Task Manager (CRM Tasks)** | `view-client-task-manager.php` | High-performance Kanban boards with real-time AJAX persistence, right-click command menu, resizable floating Task Details Drawer with rounded left arc, and past-time scheduling validation with auto-computed upcoming slots |
| **Public Client Portal** | `public-client-portal.php` | 100% white-labeled mobile-first portal with Anthropic Claude aesthetic (`#FBFaf7`), milestones, deliverables proofing vault, UPI/card checkout, and official vector payment marks |
| **Affiliate & Referral System & Endorsely Integration** | `view-affiliate-referrals.php`, `cora-frontend/app/layout.tsx` | Permanent workspace foundation module across all industries. Gamified partner dashboard with tier progression meter (Bronze → Silver → Gold → Diamond), active streak multiplier (`⚡ 3-Streak Active (+5% Bonus)`), Top 3 podium leaderboard, 5 sticky edge-to-edge sub-tabs, reward model (+100 AI Runes, 20% monthly / 30% annual commission, milestone bonuses), 3-step onboarding screener with progressive disclosure accordions, mobile bottom-sheet withdrawals, and platform-wide Endorsely affiliate script tracking suite (`app.endorsely.com/assets/affiliate-tracking.js`) with dynamic conversion attribution on checkout completion |
| **Users & Role Governance** | `view-users.php` | Dynamic custom role builder with real module titles, permissions strictly filtered to active workspace modules, active-only equal AI token allocation, desktop & mobile tab customizer, initials avatars, scroll-freeze-free drawer architecture, and safe role deletion |
| **User & People Ops AI Copilot** | `view-users.php`, `admin-dashboard.php` | Action-oriented AI Copilot for user and crew management with real-time team RAG, machine-executable action tags (`invite_member`, `edit_member_role`, `open_team_migration`, `open_permissions_matrix`, `filter_members`, `resend_invite`, `check_ai_quota`), and synchronized mobile island presets |
| **App Modules (Feature Hub)**| `view-feature-hub.php`| Streamlined directory with Universal Foundation Modules Lock (6 foundation modules: `blogs`, `forms`, `team-roles`, `media`, `vault`, `affiliates` + `dashboard`) across all industry modules, immutable lock badges, single-column horizontal mobile cards, unified search & control bar, and Rule 13 zero-outline styling |
| **Interactive Calendar** | `view-calendar.php` | Unified scheduling for bookings, showings, and milestones situated in the independent CRM sidebar group |
| **Finance & GST Invoicing Suite**| `view-financials.php`, `view-vault.php` | GST-compliant invoicing suite with multi-step creator validation, line items serialization, real-time CGST/SGST/IGST tax calculation (SAC 9983), default inputs validation, and automatic Document Vault sync with SHA-256 legal hash immutability |
| **Dashboard & Nav Customizer** | `admin-dashboard.php` | Personalize 14 KPI telemetry scorecards with dynamic industry filtering, Rule 13 zero-outline tonal cards, and customize the 3 middle mobile island slots across all 16 platform modules |
| **Inventory & Van Sales Dual Engine** | `view-inventory-management.php`, `includes/class-cora-inventory-engine.php` | Dual-mode plant command center and mobile field van terminal with 3-step Van Dispatch Wizard, weight-based allocation (@ ₹401.25/kg), 4-digit numeric bill numbers, spot billing invoices, sales register ledger with itemized breakdown, printable van stock manifests, itemized CSV exports, and exact 4-column inventory & 5-column dispatch reports |
| **Executive 24h PDF Reports** | `view-inventory-management.php` | Single consolidated 24-Hour Executive PDF Report delivered strictly once per 24 hours; pure in-app/push alerts for micro-events |
| **Field Driver Isolated POS** | `view-inventory-management.php` | 100% full-width dedicated Van POS terminal with total chrome stripping (`.cora-driver-mode-active`) and grounded driver AI |
| **AI Co-Founder, Quotas & Continuous RAG** | `admin-dashboard.php`, `cora-workspace.php` | Standardized Cora AI executive co-founder with continuous RAG living memory self-learning, standardized AI run quotas (15,000 Pro / 25,000 Scale runs/mo with weekly pacing soft caps: 3,750/wk Pro, 6,250/wk Scale), rate-limit bypass for Super Admins, ultra-concise 1-2 line responses, 1-click CTA action pills, and automated real-time client SPA navigation router executing direct UI actions |
| **Universal Remote MCP Server & OpenAPI 3.0.1 Engine** | `views/view-mcp.php`, `includes/mcp/*` | Standards-compliant Model Context Protocol server (`/wp-json/cora/v1/mcp`, `/wp-json/cora-mcp/v1/endpoint`), live OpenAPI 3.0.1 schema generation (`/wp-json/cora/v1/mcp/openapi.json`), 28 curated tools ceiling for ChatGPT Custom GPT & Developer Plugin compliance, RFC 7591 Dynamic Client Registration, RFC 9728 Protected Resource Metadata, and live SSE streaming |
| **Interactive OAuth 2.1 Consent Panel** | `includes/mcp/class-cora-oauth-server.php` | User-facing OAuth 2.1 PKCE authorization interface with master full-access switch, 10 categorized scope sections, quick presets (`all`, `readonly`, `website`), real-time scope counter, and instant token revocation in Settings |
| **AI Tools MCP Gateway & Live Action Playground** | `views/view-canvas.php`, `cora-workspace.php` | Unified action test bench built directly into the Canvas Template Editor with 3 operating modes (Natural Language, Guided Actions, Raw JSON), live output inspect, and 1-click `📥 Apply to Live Editor` integration |
| **Canvas Add Theme Wizard (2x2 Active-Voice Grid)** | `views/view-canvas.php`, `cora-workspace.php` | Complete UI overhaul featuring active-voice 2x2 grid (Build with AI Prompts, Connect Headless Code, Scrape & Migrate, White-Labeled Visual Builder), edge-to-edge sticky bottom bar, template search filter, and 6 production templates with realistic screenshot previews |
| **Canvas Live Preview Resilience Shield & Auto-Tailwind** | `views/view-canvas.php`, `cora-workspace.php` | Universal live HTML preview resilience shield automatically injecting Tailwind CSS CDN (`https://cdn.tailwindcss.com`), wrapping iframes with error boundaries and postMessage sync, guaranteeing zero blank sections across all niche templates, and supporting live static site styling parity |
| **Canvas AI Editor & Workspace Forms Embedding Suite** | `views/view-canvas.php`, `cora-workspace.php` | Consolidated unified 'Edit with AI' action bar in Live Code Editor with instant diff previewing, streamlined minimalist toolbar (zero viewport switcher noise), and native shortcode/iframe generator to embed dynamic Cora lead capture forms (`[cora-form id="..."]`) directly into static pages |
| **Standardized Canvas Drawers & Overflow Locking** | `views/view-canvas.php`, `cora-workspace.php` | Full-height side drawer sheet architecture (`.cora-drawer`) replacing abrupt popup modals, locked body overflow (`body.cora-canvas-drawer-open`) eliminating scroll jumping, and resilient button event delegation |
| **Super Admin Action Restoration & Modal Portaling** | `views/view-super-admin.php`, `cora-workspace.php` | Restored multi-tenant workspace action buttons, direct launch routing (`/workspace/dashboard?agency_id={id}&industry={type}`), modal portaling to `document.body`, and quota bypass for administrative roles |
| **Universal PWA Install Suite & Dynamic Admin Badging** | `admin-dashboard.php`, `views/view-settings-suite.php` | Universal in-app PWA install trigger in System Settings Suite & sidebar admin popover, dynamic `ADMIN` badging across 512/192/Apple/Favicon icons for Super Admins, and role-scoped Web App Manifest (`CORA Workspace (Admin)`) |
| **Official ChatGPT Plugin & Remote MCP 2.0 Gateway** | `cora-chatgpt-plugin/*` | Turnkey OpenAI ChatGPT Plugin package with `manifest.json`, `ai-plugin.json`, OpenAPI 3.0.1 specification (curated 28 tools), MCP 2.0 configuration, and standardized assistant prompts |
| **Enterprise AI Safety Guardrails** | `cora-workspace.php` | Enterprise policy enforcement engine (`wp_cora_security_incidents`) scanning for 6 violation categories (weapons, violence, nudity, religious defamation, self-harm, jailbreaks) with automated dual-escalation to Platform Super Admins and Workspace Owners, plus RBAC action execution validation |
| **Voice AI Discussion & Mode Switch** | `admin-dashboard.php` | Real-time continuous duplex voice engine with footer mic Voice Mode switch, integrated Voice Settings tab, auto-suppressed mobile keyboard, and 9 regional Indian dialects |
| **Canvas AI Starter Prompts Grid & 10-Min Launch** | `views/view-canvas.php`, `cora-workspace.php` | Add Theme Wizard Step 2 featuring production template prompts (Creative Agency, AI SaaS, B2B Consulting, Photography Studio, Custom Niche), 1-click prompt copying for Claude/ChatGPT, dual-mode code connector (HTML paste / file dropzone), and instant live launch |
| **Automated Static Site Compiler & Stream Router** | `includes/class-cora-nextjs-integration.php`, `cora-workspace.php` | Automated static site builder (Next.js App/Pages Router, Expo React Native Web, Vite) compiling to zero-dependency static bundles with direct asset streaming (`/_next/*`, `/_expo/*`), CloudLinux Inode recovery, and single-CPU thread isolation |
| **Universal Lead Form Ingestion & Live Site Bridge** | `cora-workspace.php`, `views/view-canvas.php` | Native form submission bridge across static themes and `.cora-form-embed` widgets, auto-routing inquiries into `wp_cora_leads` with instant in-place monochromatic toast feedback |
| **Security Hardening & HTTP Asset Caching** | `cora-workspace.php`, `includes/class-cora-nextjs-integration.php` | Strict `realpath` containment preventing path traversal, ZipArchive extraction sanitization, 304 Not Modified HTTP ETag caching, immutable asset headers, and admin capability enforcement |
| **Multimodal Team Migration**| `view-users.php` | AI-powered roster OCR ingestion (PDF/PNG/JPG), automatic role mapping, and 1-click batch team provisioning |
| **Field Ops & Live Tracking**| `views/view-users.php`, `cora-field-ops-tracker.js` | Live GPS tracking with auto-start on login session detection, heartbeat pings, auto-stop with `navigator.sendBeacon` flush on logout, stop/rest detection, velocity telemetry, route replay, and free HD multi-layer maps |
| **Content AI Suite** | `view-content-suite.php`| 7-dashboard content lifecycle engine with bulk actions engine (floating selection toolbar for bulk status, category, export, delete), fixed-width dropdowns, responsive 3-column mobile Opportunities grid, sticky flush sub-tabs, and 1-click blog draft generator |
| **Canvas Next.js Headless & Live Dev Server Preview** | `view-canvas.php`, `includes/class-cora-nextjs-integration.php` | Full-stack Next.js Git Connector Wizard, dynamic GitHub branch discovery, private repo PAT with dual auth headers (`Bearer` / `token`), route scanner, headless REST APIs, on-demand ISR revalidation, live dev server detection (`http://localhost:3000`), proxy router, preview cookie isolation (`cora_canvas_preview`), Level 2 live theme customizer editor, and sidebar hover quick-actions |
| **Universal Website Migrator**| `view-canvas.php`, `includes/class-cora-html-website-migrator.php` | 1-click multi-page crawler scraping external HTML/CSS/JS sites into editable draft themes |
| **Forms & Reviews 2.0 (Form AI Architect)** | `view-forms.php` | Full-featured Form AI Architect with complete CRUD lifecycle, 15 field types, multi-step wizards, live full-height preview modal, top control panel isolation, 3-metric stage funnel analytics, and dual-mode responsive data cards (table on desktop, activity cards on mobile) |
| **Media Manager & Asset Hub** | `view-media.php`, `share-media.php` | Studio-grade digital asset management with centered pop-up modals for folder creation/settings, curated color tag presets, custom color picker, edge-to-edge asset detail drawers, public shared media route interception (`/workspace/shared-media/{token}`) with Claude cream aesthetic, Total/Unique views and download telemetry tracking, and floating mobile action dock |
| **Multi-Dimensional Storage Footprint** | `view-media.php`, `cora-workspace.php` | Dynamic workspace storage calculator encompassing media files, vault contracts, AI chat history/vectors, and user activity/attendance telemetry with visual UI category breakdown |
| **Settings Suite & Identity Security** | `view-settings-suite.php`, `cora-workspace.php` | Multi-tab settings suite with Principal Systems Architect AI Copilot control, two-step administration email verification with dynamic status badges (`Verified`, `Pending Verification`) and tokenized confirmation links, authenticated mail delivery (`heycora@claraverse.in`), and enforced 100 daily AI credits quota |
| **Email Communications Suite** | `view-emails.php` | High-density 2x2 mobile / 1x4 desktop analytical cards, edge-to-edge sticky sub-tabs, industry-personalized email templates for all 5 verticals, cross-module dynamic CRM contacts sync (`wp_cora_clients`, `wp_cora_leads`), and Rule 13 tonal surfaces |
| **Authentication & Verification Engine** | `views/verify.php`, `login.php`, `register.php` | End-to-end sign in/signup flow with 32-byte cryptographic email verification tokens, SHA-256 hash-at-rest verification, early route interception for `/workspace/verify` with instant user activation and auto sign-on, and resilient resend verification |
| **Central Tenant Authorization & SSRF Filter** | `includes/class-cora-authorization.php`, `includes/class-cora-ssrf-filter.php` | Central tenant context assertion (`cora_assert_agency_context`), object-level capability gatekeeping across Tasks/Forms/Vault/Canvas, WordPress capability scoping (SEC-012), WhatsApp Cloud API HMAC-SHA256 signature verification (SEC-005), and outbound request filtering guarding against private/loopback/cloud metadata SSRF |
| **PWA & Push Notifications** | `admin-dashboard.php`, `cora-service-worker.js` | Universal Web Push notification engine with dynamic VAPID key sync, service worker push delivery, custom app icon branding, device subscription telemetry, and 1-click broadcast push announcements |
| **Sidebar & Nav Architecture** | `admin-dashboard.php`, `cora-workspace.php` | Defensive navigation shield with universal role permissions fallback (`cora_filter_sidebar_nav_by_role`) preventing empty menu groups |
| **Document Vault** | `view-vault.php` | GST-compliant invoicing (CGST/SGST/IGST, SAC 9983) with SHA-256 legal e-sign audit registry, 500KB payload caps, and contract immutability |
| **Crew & Team Scheduler** | `view-crew-scheduler.php`| Timeline-based shift scheduling, crew allocation, and dispatch management |
| **Equipment & Listings** | `view-equipment.php` | Camera gear custody tracking / Geocoded real estate inventory |
| **Super Admin Multi-Tenancy Console** | `view-super-admin.php` | 11-tab administrative control center with Danger Zone workspace deletion confirmation modal (zero dark overlay, quoted IDs, cascade removal), 1-click real-time industry vertical switcher, and Web Push device telemetry |
| **Developer Docs Portal** | `view-public-docs.php` | 3-column public documentation portal with AI Playground and Command Palette (`/docs`) |

---

## Core Architectural Principles & Design System Standards

1. **Pure Light Mode**: Zero dark mode for instant splash rendering and strict visual continuity (`#ffffff` / `zinc-50` through `zinc-950`).
2. **Monochromatic Neutral Palette**: Notion/Shopify-inspired zinc color ramp with color accents strictly bound to functional states (🟢 Active, 🟡 Pending, 🔴 Critical).
3. **Rule 13: Zero-Outline Tonal Surface Selection Architecture**: Heavy bounding box outlines, high-contrast dark border strokes (`border-zinc-900`, `border-black`, `ring-2`), and harsh perimeter borders are strictly prohibited. Active, selected, and focused items MUST use soft monochromatic tonal fills (`bg-zinc-100/90 dark:bg-zinc-800/80`) with subtle, uniform structural borders (`border-zinc-200/80`).
4. **Sticky Sub-Navigation Tabs SOP (~36px Sleek Height)**: Sub-navigation bars (Forms, Content Suite, Settings, Users, Canvas, Emails, Affiliates, MCP) adhere to a compact ~36px height with smooth transitions on scroll. On mobile viewports, tabs are flush (`left: 0`, `px-0`, zero top margin) to eliminate offset clipping under the topbar.
5. **Mobile Sheet & Drawer SOP**: Zero mobile side drawers. All action sheets, creators, and filters open as **bottom slide-up sheets** (`translate-y-full` to `translate-y-0`) with drag handles and spring easing.
6. **Top-Down Floating Toasts (Mobile) / Dynamic Offset (Desktop)**: Monochromatic notification feedback (`window.coraShowToast`) floating top-center on mobile and elevated above Studio Drawers on desktop.
7. **Universal Body Scroll Lock SOP**: `window.coraLockScroll()` & `window.coraUnlockScroll()` with scrollable drawer container opt-in (`.cora-drawer-scrollable`) eliminating background page jitter across all modals, drawers, and the AI panel.
8. **Native Fluid Touch Scroll & Zero Synthetic Reloads**: Native mobile scrolling preserved across all viewports; synthetic pull-to-refresh JavaScript engines are strictly prohibited to prevent false reloads and jitter during scroll.
9. **0ms Touch Latency & Instant-Tap Responsiveness**: `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` applied across all interactive controls to eliminate the mobile 300ms tap delay.
10. **Desktop Centered KPI Scorecards (Max 60% Width)**: Main dashboard metrics are constrained to a maximum 60% container width and centered horizontally on desktop viewports, guaranteeing a 1x4 row on desktop and 2x2 grid on mobile viewports.
11. **Security URL Masking**: Direct rewrites and symlinks masking `wp-content` to `/assets/` and `wp-includes` to `/core/` to shield internal platform architecture.
12. **Semantic URL Navigation**: Clean RESTful navigation paths across all dashboard views (`/workspace/{subpage}`) replacing legacy JavaScript links.
13. **Dedicated Independent CRM Navigation Group**: CRM elevated to a first-class independent group housing Leads Pipeline, Clients, Tasks, Calendar, and Finance across all industry verticals.
14. **Strict Single Owner Policy**: One workspace owner per tenant with guarded role assignability.
15. **Strict Role & Terminal Scoping**: Dedicated Field Driver role (`cora_field_vendor`) with server-side route redirection, DOM container isolation, complete chrome stripping, and terminal-locked AI copilot.
16. **Anti-Spam & Single 24-Hour Executive Delivery**: Disarms repetitive micro-event notification emails; all transient events route to In-App Bell & Web Push, consolidating executive business summaries into a single printable PDF delivered strictly once every 24 hours.
17. **Sleek Docked PWA Update Notification**: Redesigned update pill notification docked top-right with dismiss action, seamless cache purging, and version synchronization.
18. **Resizable Floating Task Drawer**: Drag-to-resize left handle with persistence, anchored below 48px top navbar with rounded left arc (`rounded-l-2xl`), ambient shadow, and zero background backdrop overlay.
19. **Seamless Mobile Warm Cream Background**: Mobile viewport background extended seamlessly with warm cream tone to eliminate bottom-edge color seams on mobile devices.
20. **Official Brand Vector Marks**: Strict enforcement of official vector SVGs for payments (WhatsApp, Google Pay, PhonePe, Paytm) and social sharing (WhatsApp, LinkedIn, X, QR code), completely eliminating emojis and raster images.
21. **Full-Width Sticky Sub-Tabs Bar & `pan-x` Horizontal Touch Swipe (v4.9.179)**: Sub-navigation tabs span the full width of workspace content seamlessly without floating gaps, pin directly below the topbar on scroll, and support smooth horizontal touch scrolling with outline removal.
22. **Universal Foundation Modules Architecture (v4.9.189, v4.9.200)**: The 6 core operational modules (`blogs`, `forms`, `team-roles`, `media`, `vault`, `affiliates` alongside `dashboard`) are universally locked and permanently merged across all industry modules to protect core tenant infrastructure.
23. **Multi-Dimensional Workspace Storage Footprint (v4.9.183)**: Storage telemetry accounts for media uploads, vault PDFs, AI vector embeddings and transcripts, and activity/GPS audit logs with UI category breakdowns.
24. **Beacon-Backed Field Telemetry Lifecycle (v4.9.182)**: Field GPS tracking automatically starts upon verified user login and cleanly flushes pending coordinates via `navigator.sendBeacon` upon logout.
25. **Active-Only Equal AI Token Allocation (v4.9.180)**: Workspace monthly AI token credits are divided equally among active team members by default, strictly ignoring pending invitations or deactivated users.
26. **Mobile GPU Hardware Acceleration (v4.9.207)**: Bottom sheets, mobile floating islands, and drawers enforce GPU layer acceleration (`transform: translateZ(0); will-change: transform;`) for 60fps interaction smoothness.
27. **Desktop Sticky Chat Input Dock (v4.9.197)**: Full-height flex layouts pin the chat input bar (`.cora-ai-input-wrapper`) with `position: sticky; bottom: 0;`, backdrop blur, and isolated inner messages scrolling.
28. **Linear-Grade Model Selector Popover (v4.9.195)**: Modern monochromatic popover trigger pill (`[ ◆ Gemini 3.5 Flash | Real-time ▾ ]`) with grouped intelligence tiers across Google Gemini, Anthropic Claude, OpenAI, and Groq Ultra-Fast.
29. **Immediate Route Interception for Email Verification (v4.9.208)**: Verification routes (`/workspace/verify`) intercept requests before template redirection or auth barriers, completing cryptographic verification, user activation, and session creation seamlessly.
30. **Central Tenant Authorization & SSRF Defense Shield (v4.9.209)**: Centralized tenant context verification (`Cora_Authorization::assert_agency_context`), capability mapping (SEC-012), and outbound HTTP SSRF filtering (`Cora_SSRF_Filter`) blocking private/loopback/metadata destinations.
31. **Specialized Action-Oriented AI Copilot Architecture (v4.9.190, v4.9.191, v4.9.198)**: Domain-specific modules ground Cora AI with real-time operational context (People Ops rosters, Media storage telemetry, System Settings options) and parse actionable machine tags (`[ACTION:verb {params}]`) that execute mutations directly without leaving conversational context.
32. **Floating Monochromatic Mobile Action Dock Pattern (v4.9.192)**: High-frequency mobile actions in dense asset or inventory views anchor into a floating monochromatic bar (`#cm-mobile-bottom-bar`) positioned above the mobile island navigation with 160px container bottom scroll padding.
33. **AI Tools MCP Developer Gateway & Live Duplex Voice Stage (v4.9.193 - v4.9.194)**: Dual operating modes featuring high-density text chat with code execution and a live duplex voice stage powered by Web Speech API, ElevenLabs neural voice synthesis, and an animated pulsing sphere visualizer (`.cora-voice-sphere`).
34. **Canvas Themes Single-Stream Library & CWV Diagnostic Pattern (v4.9.199)**: Theme builders unify draft and live themes into a single-stream library accompanied by a 4-card Speed/Core Web Vitals diagnostic strip (CWV score, LCP velocity, quota, published pages) and viewable-only fallback on mobile viewports.
35. **Permanent Foundation Lock Expansion & Alphanumeric Badge Pattern (v4.9.200)**: Foundation locks protect both administrative core and growth infrastructure (`affiliates`), displaying dynamic alphanumeric partner rank badges in sidebar navigation.
36. **Gamified Partner Growth & Milestone Reward Architecture (v4.9.202 - v4.9.206)**: Partner growth ecosystems combine tiered progress meters (Bronze → Silver → Gold → Diamond), streak velocity multipliers, Top 3 podium leaderboards, dual sign-up rewards (+100 Runes), recurring commissions (20% monthly / 30% annual), and milestone cash bonuses.
37. **Bulk Operations Engine & Floating Selection Toolbar Pattern (v4.9.156)**: Multi-record interfaces provide item checkboxes and a master "Select All" toggle triggering an elevated bottom selection toolbar (`#content-bulk-actions-bar`) for live counts, batch status progression, export triggers, and safe bulk deletion.
38. **User-First AI Conciseness & Balanced Brace Parsing Pattern (v4.9.160 - v4.9.162)**: Conversational AI responses adhere to a 1–2 line conversational response rule followed by structured generative action cards or 1-click blog draft generators, parsing LLM payloads with balanced brace counting algorithms (`cora_ai_extract_balanced_json`).
39. **Public Media Route Interception & Telemetry Tracking Pattern (v4.9.187 - v4.9.188)**: Guest-accessible proofing routes (`/workspace/shared-media/{token}`) resolve without WordPress login barriers using tokenized lookups, tracking impressions and downloads in real-time KPI scorecards.
40. **White-Labeled Public Client Portal Routing (v4.9.122)**: Public guest access for deliverables and proofing uses tokenized URLs bypassing WordPress auth, adhering strictly to the Anthropic Claude aesthetic (`#FBFaf7`) and official vector SVG payment marks.
41. **Enterprise AI Safety Guardrails & Dual Escalation (v4.9.139)**: All user interactions with conversational AI systems are validated against 6 core policy violation categories, recorded in `wp_cora_security_incidents`, and escalated concurrently to Platform Super Admins and Workspace Owners.
42. **High-Speed Micro-Cache Layer (v4.9.150)**: In-memory and transient caching via `cora_cache_get()` and `cora_cache_set()` ensures sub-millisecond query execution across high-velocity database lookups.
43. **Safe Restocking Rollback Protocol (v4.9.103)**: Any deletion or editing of field transactions (consignments or spot invoices) atomically reverses stock debits and restores unallocated units back to parent inventory balances.
44. **Dynamic Custom Role Governance & Module Scoping (v4.9.184 - v4.9.186)**: Custom roles are scoped strictly to active tenant modules with human-readable titles, preventing privilege escalation while protecting Workspace Owner permissions immutably.
45: 45. **Tab Customization Engine Architecture (v4.9.147)**: Desktop and mobile tab reordering drawers feature drag-and-drop handles, toggleable visibility, and dual-layer persistence (localStorage for instant 0ms painting + asynchronous user meta AJAX updates).
46. **Phone Input Validation & Sanitization Standard**: Enforce strict numeric regex checks (`/^[0-9+ -]{7,15}$/`) across all contact forms, checkout flows, and lead captures.
47. **Universal Remote MCP & OAuth 2.1 PKCE Architecture (v4.9.220 - v4.9.230)**: Remote Model Context Protocol server (`/wp-json/cora-mcp/v1/sse`, `/wp-json/cora-mcp/v1/messages`) integrated with OAuth 2.1 PKCE authorization code grant, RFC 7591 Dynamic Client Registration, RFC 9728 Protected Resource Metadata, `WWW-Authenticate` challenge headers, and 1-click effortless AI prompts for ChatGPT, Claude, and Google Gemini.
48. **Unified Single-Domain Routing & Clean Slug URL Architecture (v4.9.232 - v4.9.247)**: Centralized multi-tenancy URL routing consolidating multi-tenant operations into clean single-domain paths (`/workspace/{subpage}`, `/site`, `/`) with skeleton mutual exclusion, single-tenant clean URLs, and immutable preservation of archive virtual subdomain refs.
49. **Super Admin Multi-Tenancy Governance & Safe Workspace Deletion (v4.9.235 - v4.9.239, v4.9.267)**: Super Admin administrative suite with Danger Zone workspace deletion confirmation modal (zero dark overlays, quoted identifier safeguards, option-store cascade removal), 1-click real-time industry vertical switcher, and Web Push device subscription telemetry.
50. **Administration Identity Verification & Tokenized Security Workflow (v4.9.252, v4.9.269 - v4.9.273)**: Dual-step administrative email verification with 32-byte cryptographic verification tokens, dynamic status badges (`Verified`, `Pending Verification`), locked input states, authenticated mail transport (`heycora@claraverse.in`), and cross-subdomain authentication guards.
51. **Universal Web Push & Dynamic VAPID Notification Engine (v4.9.262, v4.9.266 - v4.9.267)**: Service Worker push delivery overhaul with dynamic VAPID key exchange, custom app icon branding, device subscription telemetry, and 1-click broadcast push announcements without third-party push dependencies.
52. **Continuous RAG Self-Learning & High-Velocity AI Navigation Router (v4.9.274 - v4.9.276)**: Conversational AI Co-Founder backed by continuous RAG living memory self-learning, strict 1-2 line concise responses, 1-click CTA action pills, and automated real-time client SPA navigation router executing direct user commands without leaving chat.
53. **Edge-to-Edge Portal Drawers & Draggable Left Resizer Architecture (v4.9.290 - v4.9.301)**: CRM leads and management panels re-engineered into full-height edge-to-edge side drawers auto-portaled to `document.body` with transparent backdrops (zero trapped dark overlays), interactive left-edge drag resize with persistent width memory, and clean collapsible accordion groups.
54. **CRM Lead Roster Sanitization & Bi-Directional Synchronous State (v4.9.300 - v4.9.302)**: Lead management and deal tracking enforce strict team assignee sanitization matching active workspace members only, with bi-directional synchronization between Kanban cards and edge-to-edge drawer forms.
55. **Financial GST Invoicing Engine & Document Vault Legal Hash Sync (v4.9.303 - v4.9.304)**: Multi-step GST invoice builder with itemized serialization, real-time CGST/SGST/IGST tax calculation (SAC 9983), default inputs validation, and automatic Document Vault synchronization with SHA-256 legal audit hashing.
56. **Headless Next.js Integration & On-Demand ISR Revalidation Pipeline (v4.9.308 - v4.9.313)**: Decoupled CMS integration via `Cora_Nextjs_Integration` supporting Next.js Git Connector Wizard, dynamic GitHub branch discovery, dual-header PAT authentication fallback (`Authorization: Bearer` & `token`), headless REST APIs, scanned routes ingestion, and on-demand ISR revalidation.
57. **Stationery & Field Van Dispatch Dual-Tier Inventory Allocation Engine (v4.9.314 - v4.9.315)**: Dual-tier plant and van sales inventory engine featuring a 3-step Van Dispatch Wizard, weight-based allocation (@ ₹401.25/kg), 4-digit numeric bill numbers, spot invoice ledger, sales register with itemized breakdown, printable van stock manifests, itemized CSV exports, and exact 4-column inventory & 5-column dispatch reports.
58. **Next.js Live Dev Server Detection & Isolated Preview Cookie Sandbox (v4.9.316)**: Canvas live Next.js build preview & proxy router with fast offline dev server detection (`http://localhost:3000`), preview cookie isolation to explicit preview parameters (`cora_canvas_preview`), Level 2 live theme customizer routing, and hover quick-actions.
59. **AI HTML & Tailwind Starter Prompts Grid & 10-Min Launch (v4.9.334 - v4.9.337)**: Add Theme Wizard Step 2 provides 5 production template prompts (Creative Agency, AI SaaS, B2B Consulting, Photography Studio, Custom Niche) with 1-click clipboard prompt extraction for Claude & ChatGPT, dual-mode code connector (HTML paste / file dropzone), and instant live launch with automatic script tag sanitization (`<\/script>`).
60. **Automated Static Site Compiler & Exporter Pipeline (v4.9.317 - v4.9.331)**: Built-in compilation runner (`cora_nextjs_build_repo`) auto-detecting Next.js, Expo React Native Web, and Vite, compiling to standalone static distributions (`out/`, `dist/`), streaming static assets (`/_next/*`, `/_expo/*`) directly with zero external server dependencies, and supporting GitHub CI/CD webhooks.
61. **Shared Hosting Resilience & Inode Quota Recovery (v4.9.323 - v4.9.331)**: Low-memory Webpack configuration (`NODE_OPTIONS="--max-old-space-size=768"`, `RAYON_NUM_THREADS=1`), single-CPU thread isolation (`UV_THREADPOOL_SIZE=1`), WebAssembly memory flags (`--no-wasm-code-gc`), automated stale npm cache purging (`~/.npm/_cacache`), and self-healing npm fallbacks to prevent `ENOTEMPTY` and CloudLinux `-122 EDQUOT` crashes.
62. **Universal Lead Form Ingestion & Live Site Interception (v4.9.333 - v4.9.335)**: Direct CRM bridge automatically intercepting standard HTML `<form>` submissions and `.cora-form-embed` widgets across static builds, persisting lead records into `wp_cora_leads` with instant monochromatic toast feedback and tenant activity logging.
63. **Security Path Containment & 304 Not Modified Caching (v4.9.337)**: Static asset streaming implements strict `realpath` containment to prevent path traversal attacks, sanitizes ZipArchive file extractions against `../` escapes, issues HTTP 304 Not Modified responses on matching ETags, and enforces administrator capabilities on all Next.js endpoints.
64. **Super Admin Dynamic PWA Badging & Role-Scoped Manifest (v4.9.338)**: Automatically serves dynamic `ADMIN`-badged icons (512x512, 192x192, Apple touch icons, favicons) for super administrators with role-scoped Web App Manifest (`CORA Workspace (Admin)`), emerald active status dots, and separate standalone home screen identities.
65. **Turnkey ChatGPT Plugin & OpenAI Actions Distribution (v4.9.334)**: Distribution package (`cora-chatgpt-plugin/`) providing `manifest.json`, OpenAPI 3.1.0 specifications for 25+ studio tools, remote MCP 2.0 configuration (`mcp.json`), and turnkey skills for ChatGPT, Claude, and Google Gemini integration.
66. **OpenAPI 3.0.1 Specification & Curated 28 Core Actions Ceiling (v4.9.355 - v4.9.357)**: Dynamic OpenAPI output upgraded to version 3.0.1 (`/wp-json/cora/v1/mcp/openapi.json`) with a strict 28-tool ceiling to guarantee compatibility with OpenAI Custom GPT action limits, stripping non-conforming parameters and emojis.
67. **Universal Read/Write OAuth 2.1 Consent Architecture (v4.9.354)**: Interactive OAuth 2.1 authorization screen featuring a master full-access toggle, 10 categorized permission sections, quick presets (`all`, `readonly`, `website`), and real-time scope selection counters.
68. **AI Tools MCP Gateway & Live Action Playground in Template Editor (v4.9.351)**: Unified action playground integrated directly into the Canvas Template Editor supporting Natural Language, Guided Actions, and Raw JSON modes with 1-click `📥 Apply to Live Editor` code reflection.
69. **Zero-Copy Native MCP File Management & Page Publishing (v4.9.349)**: Native tool registry tools (`cora_canvas_update_page_html`, `cora_canvas_get_page_html`, `cora_canvas_create_theme`) writing directly into static build distributions (`out/index.html`) for instant sub-millisecond production updates without recompilation.
70. **Active-Voice 2x2 Add Theme Wizard & Realistic Preview Assets (v4.9.344 - v4.9.345)**: Overhauled theme creation wizard featuring an active-voice 2x2 grid, sticky bottom action bar, template search filter, and 6 production templates with realistic screenshot previews (Agency, Consulting, E-Commerce, Photography, Real Estate, SaaS).
71. **Standardized Canvas Drawers & Overflow Locking Standard (v4.9.352 - v4.9.353)**: Full-height side drawer sheet architecture (`.cora-drawer`) replacing abrupt popup modals, locked body overflow (`body.cora-canvas-drawer-open`) eliminating scroll jumping, and resilient button event delegation.
72. **Super Admin Multi-Tenancy Action Button Portaling & Launch Routing (v4.9.343)**: Administrative workspace action buttons portaled directly to `document.body` to eliminate container clipping, paired with clean launch routing (`/workspace/dashboard?agency_id={id}&industry={type}`).
73. **Universal In-App PWA Install Trigger Architecture (v4.9.342)**: Native PWA install prompts integrated into System Settings Suite and the sticky sidebar admin popover, providing reliable home screen installation across iOS, Android, and Desktop environments.
74. **Admin Role Quota & Session Timeout Exemption Standard (v4.9.339)**: Bypasses rate limits and eliminates artificial 6-hour session timeouts for administrative users (`current_user_can('manage_options')`), ensuring continuous, uninterrupted platform management.
75. **AI Run Quota Standardization & Fair Usage Pacing (v4.9.364)**: Standardized monthly quotas across tiers (Starter: 500, Pro: 15,000, Scale: 25,000 runs/mo) with weekly soft-limit throttling (Pro: 3,750/wk, Scale: 6,250/wk) to prevent quota burnout while granting Super Admins complete bypass and continuous quota monitoring.
76. **Endorsely Affiliate & Conversion Tracking Suite (v4.9.364)**: Platform-wide integration of Endorsely affiliate tracking script (`https://app.endorsely.com/assets/affiliate-tracking.js`, Affiliate ID: `d56e72c8-89fa-47cb-bc87-920aa9e3a6bb`) across frontend layouts and checkout flows, triggering `window.endorsely?.trackConversion()` upon successful payment verification.
77. **Live HTML Preview Resilience Shield & Auto-Tailwind Injection (v4.9.358 - v4.9.359)**: Automatically injects the Tailwind CSS CDN script (`https://cdn.tailwindcss.com`) into preview iframes and exported static bundles if missing, wrapping preview DOM in error boundaries to eliminate blank sections.
78. **Unified 'Edit with AI' Toolbar Architecture (v4.9.362)**: Consolidates fragmented AI editing triggers in the Live Code Editor into a unified, high-visibility "Edit with AI" action bar with context-aware prompts and instant diff previewing.
79. **Workspace Forms Embedding Suite & Dynamic Form Interoperability (v4.9.361)**: Supports embedding dynamic Cora lead capture forms into Canvas landing page themes using `[cora-form id="..."]` shortcodes or `<iframe src="...">` embed codes with responsive height autosizing and AJAX submission handling.
80. **Live Static Site Styling Parity & CSS Scope Containment (v4.9.363)**: Guarantees 100% styling parity between preview iframes and published static sites by encapsulating Tailwind styles and preventing host stylesheet leakage.

---

## Local Development & Test Environment

### Prerequisites
* [Local by Flywheel](https://localwp.com/) or WordPress 6.x on PHP 8.2+
* Node.js 18+ and Playwright for E2E validation

### Quick Start
```bash
# Clone the repository
git clone https://github.com/dravyafolio2021/heycora.git cora
cd cora

# Provision local testing accounts
/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php scripts/setup_local_accounts.php
```

### Pre-Configured Test Accounts (`http://cora.local`)

| Workspace Archetype | Username / Email | Password | Direct URL |
| :--- | :--- | :--- | :--- |
| 🏡 **Real Estate Brokerage** | `re_owner`<br>`owner.realestate@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=real_estate](http://cora.local/workspace/dashboard?industry=real_estate) |
| 📸 **Photography Studio** | `studio_owner`<br>`owner.studio@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=photography_studio](http://cora.local/workspace/dashboard?industry=photography_studio) |
| 📈 **Marketing Agency** | `marketing_owner`<br>`owner.marketing@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=marketing_agency](http://cora.local/workspace/dashboard?industry=marketing_agency) |
| 🏭 **Stationery Manufacturing** | `stationery_owner`<br>`owner.stationery@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=stationery_inventory](http://cora.local/workspace/dashboard?industry=stationery_inventory) |
| 💼 **Professional Services Agency** | `prof_owner`<br>`owner.profservices@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=professional_services](http://cora.local/workspace/dashboard?industry=professional_services) |
| 🚚 **Field Sales Van Driver** | `driver_rohan`<br>`driver.rohan@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard?industry=stationery_inventory&subpage=plant_inventory&mode=vendor](http://cora.local/workspace/dashboard?industry=stationery_inventory&subpage=plant_inventory&mode=vendor) |
| 👑 **Platform Super Admin** | `cora_admin`<br>`admin@cora.local` | `cora_secure_pass_123` | [http://cora.local/workspace/dashboard](http://cora.local/workspace/dashboard) |

---

## Testing & Quality Assurance

```bash
# Install Playwright test dependencies
npx playwright install

# Run full E2E test suite
npx playwright test

# Run Security Audit Regression Test Suite
php scripts/test_sec_audit_all.php
```

---

## Documentation Index

* [Master Technical Platform Documentation](docs/cora-platform-documentation.md)
* [Canvas & Frontend Module Documentation](docs/canvas-frontend-module.md)
* [Developer Feature & Optimization Guide](docs/DEVELOPER_FEATURE_GUIDE.md)
* [Universal MCP Compatibility & Testing Matrix](docs/mcp/compatibility.md)
* [Security Audit and Repair Specification](SECURITY_AUDIT_REPORT.md)
* [Local Test Credentials Directory](LOCAL_CREDENTIALS.md)
* [Platform Onboarding One-Pager](CORA_PLATFORM_ONBOARDING_ONE_PAGER.md)
* [Modules Status & Release Manifest](MODULES_STATUS.md)

---

*Cora Platform v4.9.357 — Architecture & Development Team.*
