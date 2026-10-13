# Cora Platform — Developer Feature & Optimization Guide (v4.9.381)

This guide defines the standardized architectural patterns, blueprints, and performance guidelines for engineering new modules and extending features across the Cora SaaS Workspace (`app/public/wp-content/plugins/cora-workspace`) and Marketing Frontend (`cora-frontend`).

---

## 1. Core Architectural Principles & Zero-Regression Policy

1. **Strict Module Isolation**: Modifying or introducing a module must NEVER break, alter, or cause side effects in neighboring views.
2. **Dedicated Schema & Tenant Scope**: Always isolate records via `agency_id = %d` (and optionally `branch_id = %d`) on custom `wp_cora_*` tables. Never mix multi-tenant operational data in `wp_posts` / `wp_postmeta`.
3. **Atomic Design System Tokens**: Utilize Level 00–04 tokens from `cora-design-tokens.js` (and `cora-tokens.ts` for Next.js).
4. **Dialogue & Mobile Ergonomics SOP**:
   - Direct all alerts, errors, and system confirmations to the custom monochromatic Toast Notification system (`window.coraShowToast`).
   - **Zero Mobile Side Drawers**: All mobile action panels, multi-step creators, and filter trays MUST open as bottom-up slide sheets (`translate-y-full` to `translate-y-0`).
   - **Top-Down Floating Banners (Mobile) / Dynamic Offset (Desktop)**: Alerts and toasts float from top-center (`top: 68px` on mobile) to eliminate collisions with bottom sheets and navigation islands. On desktop, toasts anchor bottom-right and dynamically elevate above active Studio Drawers.
5. **Universal Body Scroll Lock System**: Always call `window.coraLockScroll()` when opening any modal, drawer, or bottom sheet, and `window.coraUnlockScroll()` upon closing. Ensure scrollable inner containers are marked with `.cora-drawer-scrollable` or `[data-cora-scrollable]`.
6. **Zero Naked Global `!important` Utilities**: Never declare un-namespaced global utility overrides with `!important` (e.g. `.hidden { display: none !important; }`). All visibility states must use scoped component classes (e.g. `.cora-drawer.collapsed`, `.cora-modal:not(.open)`).
7. **Mobile Touch Snappiness & GPU Compositing (v4.9.207)**: Enforce `touch-action: manipulation; -webkit-tap-highlight-color: transparent;` on all interactive buttons and triggers to eliminate the 300ms tap delay. Use `transform: translateZ(0); will-change: transform;` on mobile sheet drawers and modals for GPU-accelerated 60fps transitions.
8. **High-Speed Micro-Cache Layer**: Use `cora_cache_get()` and `cora_cache_set()` for sub-millisecond query caching.
9. **Semantic RESTful Routing**: Always use semantic path routing (`/workspace/{subpage}`) rather than JavaScript void links.
10. **Phone Input Validation**: Enforce numeric regex checks (`/^[0-9+ -]{7,15}$/`) across all contact forms and profiles.
11. **Security URL Masking**: Ensure all internal asset and core requests route through `/assets/` and `/core/` without exposing raw backend paths.
12. **Strict Single Workspace Owner Policy**: Every agency must have exactly one Workspace Owner. Never allow multiple owners per tenant or expose the Workspace Owner role in standard member assignment dropdowns.
13. **Free Map Tiles & Geolocation SOP**: All GIS/mapping features must utilize free, unmetered tiles (Esri World Imagery, Esri Streets, OpenStreetMap, CartoDB Dark) with zero watermarks and zero external API keys.
14. **Strict Role Scoping & Terminal Isolation**: For operational field roles (such as `cora_field_vendor`), implement server-side route redirection, omit central administrative containers from the DOM, lock mobile island navigation strictly to terminal/AI tools, strip topbar chrome (`.cora-driver-mode-active`), suppress simulation banners, and ground the AI Copilot strictly to route and sales operations.
15. **Safe Restocking Rollback Protocol**: Any deletion or editing of field transactions (consignments or spot invoices) must atomically reverse stock debits and restore unallocated units back to parent inventory balances.
16. **Single Consolidated 24-Hour Executive PDF Reporting & Anti-Spam Policy**: Ephemeral and recurring micro-events (SEO ranking shifts, morning/evening attendance pings, individual location updates, user status logs, 0-task briefings) are strictly prohibited from dispatching emails and MUST route 100% to In-App Bell and PWA Web Push alerts. Master operational summaries, sales figures, and AI directives are consolidated into a single 24-Hour Executive PDF Report delivered strictly once per 24 hours per owner.
17. **CRM Kanban & High-Density Card Pattern**: Lead cards must adhere to the ultra-compact 3-level anatomy (Header, Context Badges, 1-tap single-row quick outreach footer: WhatsApp/Phone/Email). Kanban columns must feature in-column search, context sorting, custom pastel column tints, and real-time live count/valuation synchronization.
18. **Agency Team Governance & Dynamic Roles Blueprint**: Dynamic custom roles must be stored in `wp_cora_roles` with tenant scoping. Permissions matrices must gate access at runtime across active features, with strict immutable lock on Workspace Owner privileges.
19. **Tab Customization Engine Pattern**: Desktop and mobile tab reordering drawers must support drag-and-drop handles, toggleable visibility, and dual-layer persistence (localStorage for instant 0ms painting + asynchronous user meta AJAX updates).
20. **Dedicated CRM Group Navigation Hierarchy**: The platform establishes CRM as an independent first-class navigation group housing Leads Pipeline, Calendar, and Finance across all industry verticals, ensuring high-velocity deal tracking and revenue visibility.
21. **Floating Resizable Task Drawer Pattern (v4.9.124)**: When building detailed subtask drawers or item inspectors, anchor below the 48px top navbar (`top: 48px`), implement a left-edge rounded arc (`rounded-l-2xl`) and ambient shadow, use zero dark backdrop to preserve underlying board context, and wire an interactive left drag handle (`window.coraInitTaskDrawerResize`) with `localStorage` width persistence.
22. **White-Labeled Public Client Portal Routing (v4.9.122)**: Public guest access (deliverables, proofing, invoices) must use tokenized URLs (`?token=cora_clt_*`, slug, or ID) bypassing session authentication while enforcing tenant-scoped security. Pages must strictly adhere to the Anthropic Claude aesthetic (`#FBFaf7`) and provide official vector SVG marks for payment gateways.
23. **Dual-Reward Affiliate Attribution Pattern (v4.9.119)**: Affiliate mechanisms must implement dual-rewards (+100 AI credits on free signup, 40% commission on paid plans), 3-step partner screening before dashboard unlock, 30-day attribution cookies, and geolocation-based pricing tiers.
24. **Universal AI Drawer Background Scroll Lock SOP (v4.9.133)**: Invocations of the AI Co-Founder drawer or modals must call `window.coraLockScroll()` on open and `window.coraUnlockScroll()` on close, with momentum touch scroll preserved on inner containers.
25. **Enterprise AI Safety Guardrails & Dual Escalation (v4.9.139)**: All user interactions with conversational AI systems must be validated against the 6 core policy violation categories (`explosives_weapons`, `terrorism_violence`, `nudity_explicit`, `religious_defamation_conflict`, `self_harm`, `jailbreak_injection`). Breaches must be recorded in `wp_cora_security_incidents` and concurrently escalated in real-time to Platform Super Admins and Workspace Owners. AI-executed backend mutations must undergo RBAC action permission verification (`cora_user_can_execute_ai_action`).
26. **Sticky & Sleek Sub-Tabs Architecture (v4.9.158, v4.9.166)**: Sub-navigation bars across complex modules (Content AI Suite, Forms & Reviews 2.0) must adhere to a standardized ~36px height (`height: 36px`), smooth transitions on scroll, and zero horizontal offset. On mobile screens (<768px), sub-tabs must align flush to the viewport edges (`px-0`, `left: 0`, margins removed) to prevent clipping under the topbar.
27. **Bulk Operations Engine & Floating Toolbar Pattern (v4.9.156)**: Multi-record management interfaces must provide individual row checkboxes and a master "Select All" toggle. Selecting items triggers an elevated, docked bottom selection toolbar (`#content-bulk-actions-bar`) providing live selection counts, batch status progression, export triggers, and safe bulk deletion. Dropdowns inside bulk toolbars must enforce rigid fixed widths (`w-48`) and standard typography to eliminate layout jitter.
28. **Rule 13 Zero-Outline Tonal Surface Selection Standard (v4.9.144)**: High-contrast, dark bounding outline borders (`border-zinc-900`, `border-black`, `ring-2`, `border-2`) are strictly prohibited across all selected cards, lists, and toggles. Selection and active states MUST use soft monochromatic tonal fills (`bg-zinc-100/90 dark:bg-zinc-800/80`) paired with subtle, uniform structural borders (`border-zinc-200/80 dark:border-zinc-800`).
29. **User-First AI Conciseness & Balanced Brace Parsing Pattern (v4.9.160 - v4.9.162)**: Conversational AI responses must deliver high-velocity executive value, adhering to a 1–2 line conversational response rule followed by structured generative action cards or 1-click blog draft generators. Raw database and telemetry dumps are strictly prohibited in chat prose. LLM action payloads (`[ACTION:name {...}]`) must be parsed using balanced brace counting algorithms (`cora_ai_extract_balanced_json`) to safely handle arbitrarily nested JSON structures without regex breakage.
30. **Native Touch Scroll & Past-Time Scheduling Guarding (v4.9.140, v4.9.150)**: Eliminate synthetic touch-intercepting pull-to-refresh JS engines in favor of fluid native hardware scrolling. Task schedulers and milestone creators must evaluate timestamps against the current local time to prevent accidental scheduling of past times on the current date, accompanied by auto-calculated upcoming time slot defaults.
31. **Full-Width Sticky Sub-Tabs Bar & `pan-x` Touch Swipe Standard (v4.9.179)**: Sub-navigation bars across complex modules must span 100% of workspace content width, pin cleanly below the global topbar on scroll, enforce `touch-action: pan-x` for frictionless one-thumb horizontal swiping on mobile screens, and eliminate focus rings/harsh outline borders.
32. **Universal Foundation Modules Locking & Immutable Domain Hardening (v4.9.189, v4.9.200)**: Core operational features (`blogs`, `forms`, `team-roles`, `media`, `vault`, and `affiliates` alongside `dashboard`) are permanently declared immutable across all 6 industry domain class files. `cora_get_custom_enabled_features()` acts as a runtime micro-guard to ensure foundation modules are always active, displaying locked badges in the Feature Hub and preventing accidental tenant disabling.
33. **Public Media Route Interception & Telemetry Tracking Pattern (v4.9.187 - v4.9.188)**: Guest-accessible proofing routes (`/workspace/shared-media/{token}`, `share-media.php?cora_share={token}`) must resolve without standard login barriers using tokenized lookups. Telemetry engines must record both total and unique impressions and high-resolution asset downloads, feeding real-time KPI scorecards and audit activity trails.
34. **Multi-Dimensional Digital Storage Footprint Calculator (v4.9.183)**: Storage auditing must not be limited to media uploads alone; always calculate total digital footprint across media attachments, vault documents, AI chats/vector memory, and user activity/telemetry logs using `cora_get_workspace_storage_details()`.
35. **Field Ops Telemetry Lifecycle: Session Login Auto-Start & Beacon-Backed Flush (v4.9.182)**: Field tracking engines must detect active sessions on dashboard initialization to start background GPS logging automatically. Upon tab close, logout, or navigation (`beforeunload` / `pagehide`), the client must use `navigator.sendBeacon` to reliably deliver final location points and punch-out timestamps without risking dropped network packets.
36. **Active-Only Equal AI Token Budget Distribution Architecture (v4.9.180)**: Token pools must be divided equally across verified active workspace members (`status === 'active'`), strictly filtering out pending invitations, suspended accounts, or inactive users to maximize computing resources for working staff.
37. **Specialized Action-Oriented AI Copilot Architecture (v4.9.190, v4.9.191, v4.9.198)**: Domain-specific modules must ground Cora AI with real-time operational context (People Ops rosters, Media storage telemetry, System Settings options) and parse actionable machine tags (`[ACTION:verb {params}]`) that execute mutations directly without leaving conversational context.
38. **Floating Monochromatic Mobile Action Dock Pattern (v4.9.192)**: On mobile viewports, high-frequency actions in dense asset or inventory views must anchor into a floating monochromatic bar (`#cm-mobile-bottom-bar`) positioned above the mobile island navigation (`bottom: calc(76px + env(safe-area-inset-bottom))`) with 160px container bottom scroll padding (`pb-40`).
39. **AI Tools MCP Developer Gateway & Live Duplex Voice Stage (v4.9.193 - v4.9.194)**: Advanced AI tools must support dual operating modes: high-density multi-turn text chat with code execution and a live duplex voice stage powered by Web Speech API, ElevenLabs neural voice synthesis, and an animated pulsing sphere visualizer (`.cora-voice-sphere`).
40. **Notion/Linear-Grade Model Selector Popover Pattern (v4.9.195)**: Active LLM model selection must be presented as a compact header button (`[ ◆ Model Name ▾ ]`) that opens an elevated popover card directly below the header with an invisible dismiss shield, multi-provider tier groupings, and immunity to parent drawer pointer locks.
41. **Desktop Sticky Chat Input Dock Architecture (v4.9.197)**: Conversational chat workspaces must enforce a full-height flex column layout (`height: calc(100vh - 195px)`) with message inputs pinned permanently at `bottom: 0` with a frosted glass backdrop (`backdrop-blur-md bg-white/95 dark:bg-zinc-900/95`), while message history scrolls independently in a `flex-1 overflow-y-auto` container.
42. **Canvas Themes Single-Stream Library & CWV Diagnostic Pattern (v4.9.199)**: Theme builders must unify draft and live themes into a single-stream library accompanied by a 4-card Speed/Core Web Vitals diagnostic strip (CWV score, LCP velocity, quota, published pages), 1-click optimization recommendations, and viewable-only fallback on mobile viewports.
43. **Permanent Foundation Lock Expansion & Alphanumeric Badge Pattern (v4.9.200)**: Foundation locks must protect both administrative core and growth infrastructure (`affiliates`), displaying dynamic alphanumeric partner rank badges in sidebar navigation.
44. **Gamified Partner Growth & Milestone Reward Architecture (v4.9.202 - v4.9.206)**: Partner growth ecosystems must combine tiered progress meters (Bronze → Silver → Gold → Diamond), streak velocity multipliers, Top 3 podium leaderboards, dual sign-up rewards (+100 Runes), recurring commissions (20% monthly / 30% annual), and milestone cash bonuses (₹1,000 / ₹5,000 / ₹10,000).
45. **Early Route Interception & Cryptographic CSPRNG Verification (v4.9.201, v4.9.208)**: Public token validation endpoints (`/workspace/verify`) must intercept requests before standard template routing, validate 32-byte CSPRNG token hashes (`hash_equals`), handle email security pre-fetches safely, set authentication cookies, and seamlessly redirect to authenticated dashboards.
46. **Central Tenant Authorization & Outbound HTTP SSRF Filter Framework (v4.9.209 / SEC-001 - SEC-023)**: Replace fragmented SQL tenancy checks with central policy assertions (`Cora_Authorization::assert_agency_context`). Wrap all outbound HTTP calls with `Cora_SSRF_Filter::is_safe_url()` to block private IPv4/IPv6 subnets, link-local addresses, and cloud provider metadata services (`169.254.169.254`).
47. **Next.js Headless Git Repository Integration & Route Scanner Pattern (v4.9.308 - v4.9.313)**: Remote GitHub repositories connect directly into Cora Canvas via GitHub REST APIs. The engine dynamically queries branches, scans recursive Git trees for App Router (`app/**/page.tsx`, `page.jsx`, `page.js`) and Pages Router (`pages/**.tsx`) paths while filtering out route groups (`(marketing)`, `(auth)`), supports public and private repositories using Personal Access Tokens (PAT) with dual authorization header fallback (`token <pat>` and `Bearer <pat>`), and synchronizes routes directly into the Canvas pages registry.
48. **Incremental Static Regeneration (ISR) Webhook Revalidation Architecture (v4.9.308)**: Content and theme configuration changes trigger on-demand Next.js ISR webhooks (`POST /api/revalidate?secret=...&path=...`) with sub-15-second timeouts, timing diagnostics (`duration_ms`), and automatic revalidation on page prop updates, keeping edge CDN caches synchronized without requiring complete production site rebuilds.
49. **Live Next.js Dev Server Detection & Preview Proxy Isolation Standard (v4.9.316)**: Canvas Level 1 embeds a high-density, dark-themed Next.js App Preview Mockup card with real-time route chips and quick actions. Live previews run sub-150ms probe pings via non-blocking `fetch()` to `http://localhost:3000` (`mode: 'no-cors'`). If offline, a dedicated Terminal Instruction Card overlay renders immediately (`npm run dev`). Preview cookies (`cora_preview_theme_id`) are strictly isolated to explicit `?preview_canvas=...` or `?preview=1` query parameters and purged on normal site browsing to eliminate cookie leaks and prevent unwanted fallbacks.
50. **Universal Remote MCP Server & OAuth 2.1 PKCE Architecture (RFC 7591 / RFC 9728) (v4.9.220 - v4.9.233)**: Cora Universal MCP exposes standard Model Context Protocol (v2026-07-28 / v2024-11-05) over HTTP JSON-RPC 2.0 and SSE streaming transports (`/wp-json/cora-mcp/v1/sse`). It implements RFC 6749 / RFC 7636 OAuth 2.1 with PKCE (`S256`), RFC 7591 dynamic client registration for 1-click AI tool setup (Claude, ChatGPT, Gemini, Cursor), RFC 9728 protected resource metadata (`/.well-known/oauth-protected-resource`), and token-bound tenant hydration with 120 RPM rate limiting.
51. **3-Step Van Dispatch Wizard & Weight-Based Stock Allocation (v4.9.314)**: Mobile van distribution workflows enforce a structured 3-step modal wizard (`1. Driver & Van Assignment` -> `2. Cargo & Weight Allocation` -> `3. Review & Dispatch Manifest`). Allocations compute gross, tare, and net weights, enforcing real-time plant stock limits and decrementing inventory atomically upon manifest generation.
52. **Real-Time Spot Billing, Numeric 4-Digit Invoicing & GST Print Engine (v4.9.314 - v4.9.315)**: Field sales spot billing generates consecutive 4-digit numeric invoice serials (e.g. `1001`, `1002`) with date-only timestamps (`d M Y`), displaying customer/firm name ahead of dates. Spot sales apply standardized kilogram pricing (e.g., ₹401.25/kg) and trigger browser-native GST tax invoice print layouts with QR codes, HSN breakdowns, and reverse-charge disclaimers.
53. **Itemized Sales Register, Ledger Export & Manifest Print Generation Standard (v4.9.314 - v4.9.315)**: Field operations feature a consolidated sales register with an itemized transaction breakdown, 5-column dispatch reports, and 4-column plant inventory sheets. One-click CSV exports stream UTF-8 itemized files with sold weights, unit counts, and bill totals, while printable van stock sheets provide offline delivery drivers with physical signed handover manifests.
54. **Full-Height Edge-to-Edge Lead Drawer with Body Portaling & Draggable Resizer Pattern (v4.9.300 - v4.9.302)**: Lead and contact detail drawers portal dynamically to `document.body` to eliminate clipping from nested workspace containers. They render full viewport height (`top: 0; bottom: 0; height: 100vh`) with soft transparent backdrops, a continuous left-edge drag resizer handle (`window.coraInitLeadDrawerResize`) with mouse and touch listeners, and bi-directional DOM synchronization across Kanban board and table rows.
55. **Automated GST Invoice Document Vault Synchronization & SHA-256 Linkage Blueprint (v4.9.303 - v4.9.304)**: Publishing finalized GST invoices in Financials automatically scaffolds and archives an immutable PDF record in the Document Vault (`wp_cora_vault_documents`). The record is cryptographically stamped with a SHA-256 content checksum, categorized under `Invoices & Billing`, and tagged with bidirectional foreign keys (`invoice_id` <-> `document_id`) to ensure non-repudiation and compliance auditing.
56. **Centered Pop-Up Modal & Curated Color Tag Presets Architecture (v4.9.296 - v4.9.298)**: Modal dialogs (such as Media Manager folder creation and property editors) use centered, floating cards with transparent backdrops (`bg-zinc-950/20 backdrop-blur-xs`), flex-centered header alignments, curated monochromatic and muted pastel color tag presets, and real-time custom color input pickers, adhering to atomic design standards.
57. **Tokenized Administration Email Verification & Locked Security Workflow (v4.9.269 - v4.9.273)**: Updating sensitive administration email addresses triggers a secure dual-stage verification workflow. The existing verified email remains locked with an active shield badge until a 32-byte CSPRNG token sent to the new email address is verified via `/workspace/verify?admin_email_token=...`, preventing accidental lockout or unauthorized account redirection across multi-domain tenants.
58. **Rule 13 Tonal Surface Selection & Zero-Dark-Overlay Strict Enforcement Standard (v4.9.255 - v4.9.300)**: All UI inspection panels, team migration tools, drawer backdrops, and active cards must strictly eliminate high-contrast dark overlay backdrops (`rgba(0,0,0,0.8)` or heavy dark borders). Visual selection is achieved exclusively through soft monochromatic tonal fills (`bg-zinc-100/90 dark:bg-zinc-800/80`) and subtle structural borders (`border-zinc-200/80 dark:border-zinc-800`), ensuring full underlying workspace context remains visible.
59. **AI HTML & Tailwind Starter Prompts Grid & Master Prompt Engineering (v4.9.334 - v4.9.337)**: Add Theme Wizard Step 2 provides 5 production template prompts (Creative Agency, AI SaaS, B2B Advisory, Photography Studio, Custom Niche) with 1-click clipboard extraction for Claude & ChatGPT. Prompts instruct models to output single-file HTML5 with Tailwind CSS CDN, Lucide vector icons, and native Cora lead form hooks. Master prompt PHP strings must escape closing script tags (`<\/script>`) to prevent browser parsing collisions.
60. **Automated Static Site Compiler & Exporter Pipeline (v4.9.317 - v4.9.331)**: Built-in compilation runner (`cora_nextjs_build_repo`) auto-detecting Next.js, Expo React Native Web, and Vite, compiling to standalone static distributions (`out/`, `dist/`), streaming static assets (`/_next/*`, `/_expo/*`) directly with zero external server dependencies, and supporting GitHub CI/CD webhooks.
61. **Shared Hosting Node.js Resilience & Inode Quota Recovery (v4.9.323 - v4.9.331)**: Low-memory Webpack configuration (`NODE_OPTIONS="--max-old-space-size=768"`, `RAYON_NUM_THREADS=1`), single-CPU thread isolation (`UV_THREADPOOL_SIZE=1`), WebAssembly memory flags (`--no-wasm-code-gc`), automated stale npm cache purging (`~/.npm/_cacache`), and self-healing npm fallbacks to prevent `ENOTEMPTY` directory rename locks and CloudLinux `-122 EDQUOT` crashes.
62. **Universal Lead Form Ingestion & Live Site CRM Interception Bridge (v4.9.333 - v4.9.335)**: Direct CRM bridge automatically intercepting standard HTML `<form>` submissions and `.cora-form-embed` widgets across static builds, persisting lead records into `wp_cora_leads` with instant monochromatic toast feedback and tenant activity logging.
63. **Security Path Containment, ZipArchive Sanitization & HTTP 304 Caching (v4.9.337)**: Static asset streaming implements strict `realpath` containment to prevent path traversal attacks, sanitizes ZipArchive file extractions against `../` directory escapes, issues HTTP 304 Not Modified responses on matching ETags, and enforces administrator capabilities on all Next.js endpoints.
64. **Super Admin Dynamic PWA Badging & Role-Scoped Web App Manifest (v4.9.338)**: Automatically serves dynamic `ADMIN`-badged icons (512x512, 192x192, Apple touch icons, favicons) for super administrators with role-scoped Web App Manifest (`CORA Workspace (Admin)`), emerald active status dots, and separate standalone home screen identities.
65. **Official ChatGPT Plugin & Remote MCP 2.0 Integration Package (v4.9.334)**: Distribution package (`cora-chatgpt-plugin/`) providing `manifest.json`, OpenAPI 3.1.0 specifications for 25+ studio tools, remote MCP 2.0 configuration (`mcp.json`), and turnkey skills for ChatGPT, Claude, and Google Gemini integration.
66. **OpenAPI 3.0.1 Specification & Curated 28 Core Actions Ceiling (v4.9.355 - v4.9.357)**: When generating live OpenAPI documentation for ChatGPT Custom GPT Actions (`cora_rest_mcp_openapi_handler`), output OpenAPI version 3.0.1 rather than 3.1.0 to comply with OpenAI's validation engine. Cap exposed operations to a curated 28-tool ceiling to prevent import failures against OpenAI's ~30 action limitation. Strip all emojis and non-conforming parameters from summaries and descriptions.
67. **Universal Read/Write OAuth 2.1 Consent Screen Architecture (v4.9.354)**: Consent screens must offer an interactive "Universal Full Access (Read + Write)" master toggle, categorized scope groups with descriptive badges, quick presets (`All Tools`, `Read-Only`, `Website Only`), and live selection counters (`X of 20 granted`). Multi-tenant workspace binding must be enforced cryptographically on token issue.
68. **AI Tools MCP Gateway & Live Action Playground in Template Editor (v4.9.351)**: Canvas Template Editor embeds an action playground supporting Natural Language, Guided Actions, and Raw JSON modes. Actions execute live against active themes and workspace tables, providing live formatted responses and a 1-click `📥 Apply to Live Editor` trigger (`coraApplyPlaygroundCodeToEditor`) that reflects HTML directly into the CodeMirror editor and writes to static distribution storage.
69. **Zero-Copy Native MCP File Management & Page Publishing (v4.9.349)**: MCP tool implementations for website editing (`cora_canvas_update_page_html`, `cora_canvas_get_page_html`, `cora_canvas_create_theme`) must write directly into the localized static distribution directory (`theme_{id}/out/index.html` and `theme_{id}/index.html`), updating database metadata simultaneously to achieve instant live production publishing without recompilation overhead.
70. **Active-Voice 2x2 Add Theme Wizard & Realistic Preview Assets (v4.9.344 - v4.9.345)**: The theme creation wizard must utilize an active-voice 2x2 grid (Build with AI Prompts, Connect Headless Code, Scrape & Migrate, White-Labeled Visual Builder), edge-to-edge sticky bottom bar, client-side template search filtering, and 6 production templates with realistic screenshot previews (Agency, Consulting, E-Commerce, Photography, Real Estate, SaaS). Emojis are strictly purged in favor of 1.8px vector SVGs.
71. **Standardized Canvas Drawers & Overflow Locking Standard (v4.9.352 - v4.9.353)**: Canvas drawers must implement full-height right-sliding drawer sheets (`.cora-drawer`) replacing floating popup modals. Opening drawers must apply `body.cora-canvas-drawer-open { overflow: hidden !important; }` to lock body overflow and eliminate scroll jumping, restoring scroll positions upon close.
72. **Super Admin Multi-Tenancy Action Button Portaling & Launch Routing (v4.9.343)**: Multi-tenant action buttons in Super Admin tables must portal dialogs to `document.body` to avoid container clipping and z-index traps, pairing with clean URL routing (`/workspace/dashboard?agency_id={id}&industry={type}`) for launching specific tenant workspaces.
73. **Universal In-App PWA Install Trigger Architecture (v4.9.342)**: PWA installation prompts must be exposed programmatically in two strategic touchpoints: the System Settings Suite install card and the sticky sidebar admin popover. Prompts must hook the `beforeinstallprompt` event and support manual fallback guides for iOS Safari WebClips.
74. **Admin Role Quota & Session Timeout Exemption Standard (v4.9.339)**: Administrative accounts (`current_user_can('manage_options')`) are exempt from artificial 6-hour session timeouts and rate limit throttles, ensuring continuous access during deep administrative configuration and tenant maintenance.
75. **AI Run Quota Standardization & Fair Usage Pacing (v4.9.364)**: Standardize monthly quotas across tiers (Starter: 500, Pro: 15,000, Scale: 25,000 runs/mo) with weekly soft-limit throttling (`ceil($monthly_quota / 4)`: Pro 3,750/wk, Scale 6,250/wk) to prevent quota burnout while granting Super Admins complete bypass and continuous quota monitoring.
76. **Endorsely Affiliate & Conversion Tracking Suite (v4.9.364)**: Platform-wide integration of Endorsely affiliate tracking script (`https://app.endorsely.com/assets/affiliate-tracking.js`, Affiliate ID: `d56e72c8-89fa-47cb-bc87-920aa9e3a6bb`) across frontend layouts and checkout flows, triggering `window.endorsely?.trackConversion()` upon successful payment verification.
77. **Live HTML Preview Resilience Shield & Auto-Tailwind Injection (v4.9.358 - v4.9.359)**: Automatically inject the Tailwind CSS CDN script (`https://cdn.tailwindcss.com`) into preview iframes and exported static bundles if missing, wrapping preview DOM in error boundaries to eliminate blank sections.
78. **Unified 'Edit with AI' Toolbar Architecture (v4.9.362)**: Consolidate fragmented AI editing triggers in the Live Code Editor into a unified, high-visibility "Edit with AI" action bar with context-aware prompts and instant diff previewing.
79. **Workspace Forms Embedding Suite & Dynamic Form Interoperability (v4.9.361)**: Support embedding dynamic Cora lead capture forms into Canvas landing page themes using `[cora-form id="..."]` shortcodes or `<iframe src="...">` embed codes with responsive height autosizing and AJAX submission handling.
80. **Live Static Site Styling Parity & CSS Scope Containment (v4.9.363)**: Guarantee 100% styling parity between preview iframes and published static sites by encapsulating Tailwind styles and preventing host stylesheet leakage.
81. **Marketing Site Reform & Dual-CTA Discovery Architecture (Oct 2026)**: Repositioned homepage value proposition to *"Build websites. Run your business, with an AI co-founder."* Implemented dual high-velocity conversion CTAs: primary Google One-Tap onboarding and secondary "Explore free templates" linking directly to the 16+ pre-seeded schemas at `/use-cases` (Photography, Real Estate, Agency, Manufacturing, Consulting, etc.).
82. **Tools & Templates Ecosystem & Unified Lead Attribution (Oct 2026)**: Overhauled marketing navigation into "Tools & Templates", unifying free agency calculators (18% GST calculator, retainer margin tool, proposal generator) with production website templates. Form submissions across static landing pages automatically bridge into `wp_cora_leads` with full campaign and schema attribution.
83. **Sticky AI SDR Bar & True Viewport Portaling Architecture (Oct 2026)**: Portals the conversational AI sales co-founder directly to `document.body` for true viewport bottom-center positioning without backdrop overlay. Delivers query-aligned active voice answers, industry pills, automated objection handling, and dedicated conversion coupons (`MIGRATEFREE`, `CORA10`) linking to checkout.
84. **Multi-Format Frontend Asset & Template Distribution Architecture (Oct 2026)**: Categorizes website assets into 4 interoperable formats at `/templates` (Native Tailwind/Next.js pages, AI Section Prompts for Claude/Cursor, Elementor JSON Kits, and Lovable React projects), with live modal previews, direct code copy triggers, and automatic lead capture bridge to `wp_cora_leads`.
85. **Autonomous AI Workforce & Structured Domain Agents (Oct 2026)**: Deploys dedicated domain AI agent personas across Operations, Sales, Contracts, Knowledge, Creative, Delivery, Finance, Legal, and Marketing with high-resolution portraits, role-scoped permissions, and specialized system prompts.
86. **Forms Cards Padding Overhaul & Mobile Unlocking Standard (v4.9.372)**: Form cards implement expanded internal padding (`p-5 sm:p-6`), dedicated `p-3.5` details container, and `px-3 py-1.5` status badges, with full-card clickability navigating directly to `#edit/${form.id}` and zero `< 1024px` mobile editing restrictions.
87. **Universal "All Entries" 5th Tab & Dynamic CSV Registry Pattern (v4.9.372)**: Forms feature an integrated 5th top tab (`#all-entries`) with dynamic live count badge, metric summary strip (Submissions, Active Forms, Completion Rate, Activity), Notion-style filter toolbar, `table-fixed` column widths preventing blowout, and multi-form dynamic CSV exports.
88. **Universal Edge-Docked Entry Inspector & Zero-Backdrop Architecture (v4.9.374)**: Inspector sheets dock cleanly to the right on desktop (`w-[480px] max-w-[90vw] border-l`) and slide up from the bottom on mobile (`h-[85vh] rounded-t-3xl border-t`), eliminating dark blurred backdrops completely to leave background records 100% visible, rendering inline image/signature previews, and offering sticky JSON/Delete/Done action controls.
89. **Client Timezone Engine & Localized Timestamps Standard (v4.9.374)**: Automatically evaluates browser timezone using `Intl.DateTimeFormat().resolvedOptions().timeZone` to render relative and absolute localized date stamps across entries and inspectors.
90. **Dynamic Affiliate Referral Tracking, Coupon Deductions & Multi-Tier Attribution (v4.9.374)**: Referral attribution engine (`POST /wp-json/cora/v1/referrals/track`, `cora-frontend/lib/referral.ts`) capturing query parameters (`cora_ref`, `ref`, `via`, `partner`), persisting to 30-day cookies, automatically deducting 10%–20% coupon discounts in checkout sheets (`CheckoutSheet.tsx`, `FastRazorpayModal.tsx`), and reporting conversion back to partners.
91. **Pricing SaaS Hierarchy Pills, Bandwidth/Storage Specs & Migration Badging (Oct 2026)**: Plan cards enforce SaaS hierarchy pills, active-voice copy, explicit storage (5GB–50GB) and bandwidth (25GB–500GB) specs, floating "Free Site Migration" badge on Scale, and pure monochromatic switcher pills.
92. **Unified Global Resources Hero Section Architecture (Oct 2026)**: Standardized `GlobalResourcesHero.tsx` component unifying top banner presentation across Templates, Tools, Guides, Blog, Newsletter, and Articles with consistent typography, search, and category filter chips.
93. **Canonical Industry Solutions Directory & 301 Redirect Parity Standard (v4.9.380)**: Primary industry vertical hub relocated from legacy `/use-cases` to canonical `/industries`, backed by automated 301 redirect mapping in `.htaccess` and Next.js route configuration to maintain 100% SEO link equity across all 10+ vertical paths.
94. **Refined Industries Mega-Menu & Icon Tile Navigation Standard (v4.9.380)**: Desktop navigation implements a multi-column dropdown matching production design specifications with dedicated icon tiles, subtle colored pill indicators, and synchronized mobile drawer links.
95. **Autonomous AI Multi-Agent Workforce & 4-Row Infinite Skills Marquee Pattern (v4.9.379)**: Multi-agent landing page (`/ai-agent`) showcasing dedicated domain agent personas, high-resolution neural cutout hero graphics seamlessly blended into the canvas, and a 4-row hardware-accelerated infinite CSS marquee highlighting verified capabilities across Operations, Sales, Finance, and Creative Delivery.
96. **Lenis Smooth-Scroll Boundary Isolation & Independent Sidebar Scrolling SOP (v4.9.378)**: Complex directory sidebars (such as `FeaturesSidebar.tsx`) must be encapsulated with `data-lenis-prevent` and explicit height boundaries (`max-h-[calc(100vh-120px)] overflow-y-auto`) to isolate inner wheel/touch scrolling from the global Lenis smooth-scroll instance, preventing scroll trapping.
97. **Enterprise Brand Asset Registry & 1-Click SVG Vector Kit Architecture (v4.9.377)**: Public brand center (`/brand`) provides 1-click clipboard SVG copy and direct `.svg` downloads for Primary, Inverted, and Symbol wordmarks, complete design tokens documentation, and authentic production captures adhering strictly to Rule 13 zero-outline styling.
98. **Agency Story Canvas & Connected Chronological Journey Ribbon Standard (v4.9.376)**: Full-width storytelling canvas (`/about`) featuring a continuous vertical milestone timeline ribbon (`ScrollDrivenTimeline.tsx`) with seamless rail segments eliminating floating tick gaps, accompanied by a mobile timeline spine, Core Values bento cards, and dynamic operational week calendar.
99. **Robust Multi-Field Contact Form Validation & State Feedback Engine (v4.9.375)**: Public inquiry forms implement strict client-side and server-side validation across Full Name, RFC 5322 Work Email, numeric Phone regex (`/^[0-9+ -]{7,15}$/`), Company/Studio, and Topic dropdown, paired with accessible ARIA alerts and monochromatic toast dispatch.
100. **Unified Agency Portfolio & 4-Act Storytelling Architecture (v4.9.381)**: Agency case studies must strictly adhere to the 4-act transformation narrative (Act 1: Executive Summary, Act 2: Challenge / Friction Narrative, Act 3: Strategic Solution Narrative, Act 4: Tangible Deliverables List). Case study metrics must render numerical proof in high-contrast `JetBrains Mono` font (`font-mono font-bold tracking-tight text-zinc-950`). Client NDA confidentiality must be strictly enforceable via `is_confidential = 1`, which automatically redacts brand identities and swaps client names with anonymized industry monikers without compromising verified outcome metrics. Editing interfaces must employ a 6-tab drawer editor (`views/view-projects.php`) adhering to Rule 12 mobile bottom-sheet and desktop side-drawer specifications.
101. **Public Micro-Tools Growth Engine & Zero-PII Telemetry Governance (v4.9.381)**: Public micro-tools in `tools-canonical-registry.ts` operate as frictionless organic acquisition utilities solving concrete agency operational bottlenecks (TCO calculation, speed-to-lead modeling, scope creep economics, margin reverse-engineering, crawler audit). Tools must strictly enforce client-side calculation with zero mandatory registration. All tool analytics (`lib/tools-analytics.ts`) must adhere to strict Zero-PII governance: NEVER transmit submitted URLs, client identities, file contents, prompts, or revenue figures; transmit only sanitized operational metadata (`tool_id`, `category`, `execution_mode`, `result_band`, `duration_ms`, `cta_target`).
102. **GitHub CI/CD Full-Lifecycle PR, Merge & Rollback Engine (v4.9.381)**: The Canvas themes ecosystem integrates enterprise Git lifecycle capabilities via `Cora_GitHub_Integration`. Development workflows must support RFC 8628 OAuth 2.0 Device Authorization Grant (`cora_github_initiate_device_flow`, `cora_github_poll_device_token`) with user-code authorization, secure PAT persistence with Bearer fallback, dynamic branch creation, automated Pull Request generation (`cora_github_create_pr`) with structured diff summaries, 1-click administrative merging (`cora_github_merge_pr`) supporting merge/squash/rebase, automated commit rollback/revert (`cora_github_revert_commit`) creating clean revert commits to restore stable production states, atomic workspace synchronization (`cora_github_sync_entire_workspace`), and multi-tenant organization repository provisioning (`cora_github_provision_org_repo`).
103. **Dynamic Forms-to-Lead CRM Auto-Conversion Pipeline (v4.9.381)**: Public and embedded form submissions intercepted in `cora_rest_submit_form()` must automatically bridge into the CRM Sales Pipeline (`wp_cora_leads`). Field detection must case-insensitively map or auto-detect contact details (`name`, `email`, `phone`, `notes`, `budget`, `city`, `service`). Records must enforce tenant isolation by `agency_id` and `branch_id`. The engine must resolve assignees via direct assignment or round-robin workload balancing, compute predictive deal qualification (`hot`, `warm`, `cold`), invalidate lead caches immediately via `cora_db_get_leads(true)`, prepend to `cora_workspace_leads` for 0ms reactivity, and ingest lead creation events into `cora_rag_ingest_event` for real-time AI Co-Founder awareness.
104. **Editorial Generative Engine Optimization (GEO) & Knowledge Graph Engine (v4.9.381)**: Long-form editorial assets in `articles-data.ts` must be structured for Generative Engine Optimization (GEO) to maximize direct extraction and attribution by AI search engines (Perplexity, ChatGPT Search, Claude, Gemini AI Overviews). Articles must enforce rich technical depth, explicit reading times, difficulty indicators, structured key takeaways, interactive widget bindings (GST calculators, margin simulators), verified author credentials with Rule 3 compliance (`Aarav Mehta`, `Kavya Patel`, `Rohan Verma`, editorial teams), and Schema.org `FAQPage` and `Article` JSON-LD schemas.
105. **Multi-Submission Aggregated REST Data Routing (v4.9.381)**: Form data pipelines must expose centralized multi-submission aggregation endpoints (`GET /wp-json/cora/v1/forms/all-submissions` and `/forms/submissions`) that consolidate entries across all tenant forms. Handlers must support tenant-scoped isolation, pagination (`page`, `per_page`), date filtering, keyword search, and CSV streaming, powering the universal 5th tab "All Entries" registry in `views/view-forms.php` without degrading per-form performance.

---
---

## 2. 5-Step Feature Creation Blueprint

### Step 1: Database Migration & Multi-Tenant Model
Add table creation to `cora_workspace_install_schema()` in `cora-workspace.php` using `dbDelta`:
```php
$table_name = $wpdb->prefix . 'cora_my_feature';
$sql = "CREATE TABLE $table_name (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    agency_id bigint(20) unsigned NOT NULL,
    branch_id bigint(20) unsigned DEFAULT NULL,
    title varchar(255) NOT NULL,
    status varchar(50) NOT NULL DEFAULT 'active',
    metadata longtext DEFAULT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    KEY agency_idx (agency_id),
    KEY status_idx (status)
) $charset_collate;";
dbDelta($sql);
```

---

### Step 2: View Template (`views/view-{feature}.php`)
Create an isolated view file inside `app/public/wp-content/plugins/cora-workspace/views/`. Follow standard multi-industry scoping and monochromatic styling:
```php
<?php
if (!defined('ABSPATH')) exit;

$agency_id = function_exists('cora_get_current_agency_id') ? cora_get_current_agency_id() : 1;
$industry  = function_exists('cora_get_active_industry') ? cora_get_active_industry() : 'real_estate';
$is_studio = ( strpos( strtolower( $industry ), 'photo' ) !== false || strpos( strtolower( $industry ), 'studio' ) !== false );
?>
<div class="cora-view-container max-w-[1280px] mx-auto p-4 md:p-8 pb-28 md:pb-12">
    <!-- Header with Action Button & AI Brand Stack -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-zinc-950 font-display">
                <?php echo $is_studio ? 'Studio Resource Hub' : 'Feature Management'; ?>
            </h1>
            <p class="text-xs md:text-sm text-zinc-500">Manage tenant resources with real-time synchronization.</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="cora-create-item-btn" onclick="window.coraOpenFeatureDrawer()" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-950 text-white text-xs font-semibold rounded-xl hover:bg-zinc-800 active:scale-95 transition-all shadow-xs" style="touch-action: manipulation;">
                <span>+ New Item</span>
            </button>
        </div>
    </div>

    <!-- Data Table / Notion Container -->
    <div class="bg-white border border-zinc-200/80 rounded-2xl overflow-hidden shadow-xs">
        <!-- Content items -->
    </div>
</div>
```

---

### Step 3: Secure AJAX Router with Micro-Cache Integration
Register AJAX hooks in `cora-workspace.php` with nonce verification, tenant guard, and micro-caching:
```php
add_action('wp_ajax_cora_save_my_feature', 'cora_handle_save_my_feature');
function cora_handle_save_my_feature() {
    check_ajax_referer('cora_workspace_nonce', 'nonce');
    
    $agency_id = cora_get_current_agency_id();
    if (!$agency_id) {
        wp_send_json_error(['message' => 'Unauthorized workspace session.'], 403);
    }
    
    global $wpdb;
    $title = sanitize_text_field($_POST['title'] ?? '');
    
    if (empty($title)) {
        wp_send_json_error(['message' => 'Title is required.'], 400);
    }
    
    $table = $wpdb->prefix . 'cora_my_feature';
    $wpdb->insert($table, [
        'agency_id' => $agency_id,
        'title'     => $title,
        'status'    => 'active'
    ]);
    
    // Invalidate micro-cache
    if (function_exists('cora_cache_set')) {
        cora_cache_set("my_feature_list_{$agency_id}", null);
    }
    
    wp_send_json_success(['message' => 'Item saved successfully.', 'id' => $wpdb->insert_id]);
}
```

---

### Step 4: Standard Mobile Bottom-Sheet & Desktop Drawer Markup
Add standard responsive drawer/sheet markup with drag handle, rounded corners, and spring easing:
```html
<!-- Responsive Action Sheet / Drawer -->
<div id="cora-feature-drawer-backdrop" class="fixed inset-0 bg-zinc-950/40 backdrop-blur-xs z-50 transition-opacity duration-200 hidden opacity-0" onclick="window.coraCloseFeatureDrawer()"></div>

<div id="cora-feature-drawer" class="fixed z-50 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]
    inset-x-0 bottom-0 max-h-[90vh] rounded-t-3xl translate-y-full
    md:inset-y-0 md:right-0 md:left-auto md:w-[480px] md:rounded-l-3xl md:rounded-tr-none md:translate-y-0 md:translate-x-full
    bg-white border-t md:border-t-0 md:border-l border-zinc-200 p-6 overflow-y-auto flex flex-col shadow-2xl">
    
    <!-- Mobile Drag Handle -->
    <div class="md:hidden w-10 h-1 rounded-full bg-zinc-300 mx-auto mb-4 shrink-0"></div>
    
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-zinc-100 shrink-0">
        <h3 class="text-sm font-bold text-zinc-900 font-display">Create New Resource</h3>
        <button type="button" onclick="window.coraCloseFeatureDrawer()" class="w-7 h-7 rounded-lg hover:bg-zinc-100 flex items-center justify-center text-zinc-400 hover:text-zinc-900 transition-colors">
            ✕
        </button>
    </div>
    
    <!-- Form Body -->
    <div class="py-4 space-y-4 flex-1">
        <!-- Form Fields -->
    </div>
    
    <!-- Actions -->
    <div class="pt-4 border-t border-zinc-100 flex items-center justify-end gap-2 shrink-0">
        <button type="button" onclick="window.coraCloseFeatureDrawer()" class="px-4 py-2 text-xs font-semibold text-zinc-600 hover:text-zinc-900">Cancel</button>
        <button type="button" onclick="window.coraSubmitFeatureForm()" class="px-4 py-2 bg-zinc-950 text-white text-xs font-semibold rounded-xl hover:bg-zinc-800">Save Resource</button>
    </div>
</div>
```

---

### Step 5: Route Registration & Tab Dispatcher
In `admin-dashboard.php`, register the new route inside the view tab router:
```php
case 'my-feature':
    require_once plugin_dir_path(__FILE__) . 'views/view-my-feature.php';
    break;
```

---

## 3. Explicit Save Workflow Pattern

For configuration panels and module customizers (such as `view-feature-hub.php` and `view-settings-suite.php`), enforce the **Explicit Save Workflow**:
1. State changes update a local JavaScript state object in memory without immediate AJAX calls.
2. An unsaved changes banner slides up from the bottom (`translate-y-0`) displaying an alert indicator and a "Save Changes" button.
3. Clicking "Save Changes" dispatches a single batch AJAX payload to atomically persist all modifications.
4. On success, `window.coraShowToast('Settings saved successfully.', 'success')` is called and the unsaved changes banner slides away.

---

## 4. Field Ops & Geolocation Tracking Pattern (v4.9.58)

When implementing or extending geolocation services:
1. **Touch Pan Mode**: Always wrap interactive Leaflet maps with a touch pan mode toggle (`isTouchPanEnabled`) to allow mobile users to scroll past the map without getting trapped in map dragging.
2. **Tile Layer Definitions**:
```javascript
const mapLayers = {
    streets: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 }),
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 }),
    osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }),
    dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { maxZoom: 19 })
};
```
3. **Stop & Dwell Calculation**: Minimum 300 seconds (5 minutes) within a 50m radius constitutes a stationary stop.

---

## 5. Super Admin Feature Flag Enforcement

To gate features dynamically at runtime:
```php
if (function_exists('cora_is_feature_enabled_for_agency')) {
    if (!cora_is_feature_enabled_for_agency('field_ops', $agency_id)) {
        wp_send_json_error(['message' => 'This module is not enabled for your workspace tier.'], 403);
    }
}
```

---

## 7. Inventory & Field Logistics Engineering Blueprint (v4.9.60 - v4.9.103)

When extending the Stationery Manufacturing or Field Logistics engines:

### 7.1 3-Step Guided SKU Studio Drawer Pattern
Always structure factory product creation/editing forms into the standardized 3-step sequence:
1. `Step 1: Identity & Media` (Product name, SKU, HSN code, packaging units, and image upload via FormData keys `image` and `image_file`).
2. `Step 2: Pricing & GST Margin Math` (Factory cost, margin %, wholesale rate, retailer spread %, MRP, and GST slab).
3. `Step 3: Factory Stock & Logistics` (Opening units, reorder alert threshold, and warehouse bin location).

### 7.2 GST & Margin Mathematical Formulations
All financial calculations must execute identically on both client (`admin-script.js`) and server (`class-cora-inventory-engine.php`):
```javascript
// Mathematical Formula Standards
const factoryMargin = wholesalePrice - baseCost;
const factoryMarginPercent = baseCost > 0 ? ((factoryMargin / baseCost) * 100).toFixed(1) : 0;
const retailerSpread = mrp - wholesalePrice;
const retailerMarginPercent = mrp > 0 ? ((retailerSpread / mrp) * 100).toFixed(1) : 0;
const gstTaxLiability = (wholesalePrice * (gstRate / 100)).toFixed(2);
const netBasePrice = (wholesalePrice / (1 + (gstRate / 100))).toFixed(2);
```

### 7.3 Zero-Cache 1x1 Email Open Tracking Pixel Pattern
When dispatching consignment assignment emails:
```php
$tracking_url = admin_url('admin-ajax.php?action=cora_inventory_track_email_open&token=' . rawurlencode($consignment['tracking_token']));
$pixel_html = '<img src="' . esc_url($tracking_url) . '" width="1" height="1" style="display:none;width:1px;height:1px;border:0;" alt="" />';
```
The endpoint must return a transparent 1x1 GIF with `Cache-Control: no-cache, no-store, must-revalidate` and immediately update `email_opened_at = current_time('mysql')`.

### 7.4 Safe Restocking Rollback on Invoice / Consignment Deletion
When an invoice or consignment is deleted, never leave stock stranded:
```php
// Rollback spot sales items back to active consignment stock
$items = $wpdb->get_results($wpdb->prepare(
    "SELECT product_id, quantity FROM {$wpdb->prefix}cora_inventory_sales_items WHERE sale_id = %d",
    $sale_id
));
foreach ($items as $item) {
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->prefix}cora_inventory_consignment_items 
         SET remaining_quantity = remaining_quantity + %d 
         WHERE consignment_id = %d AND product_id = %d",
        $item->quantity, $consignment_id, $item->product_id
    ));
}
```

---

## 8. Dynamic Dashboard Analytics & Mobile Navigation Customizer (v4.9.63, v4.9.92)

When integrating new dashboard metrics or modules:
1. **Metric Catalog Registration**: Register key and calculation callback in `$cora_available_metrics` array.
2. **Mobile Island Slot Registration**: Add module slug and SVG icon path to `$cora_nav_customizer_modules`.
3. **Empty State Requirement**: Always render a clean monochromatic empty state when search filters produce zero matches.
4. **Human-Readable Labels**: Ensure all slot preview pills render clean title-cased labels (e.g. `coraFormatModuleName(slug)`).

---

## 9. Single Consolidated 24-Hour Executive PDF Reporting Blueprint (v4.9.103)

When extending executive reporting or event notification triggers:
1. **Micro-Event Email Ban**: Never invoke `wp_mail()` for transient state notifications (e.g., SEO score updates, check-in pings, location alerts). Always use `cora_add_notification()` and `cora_pwa_send_push_notification()`.
2. **24-Hour Master Briefing Generation**: Aggregate tenant activity across `wp_cora_ledger`, `wp_cora_inventory_sales`, `wp_cora_inventory_consignments`, and `wp_cora_attendance` into a structured daily audit snapshot.
3. **Recipient Deduplication**: When dispatching executive reports across multiple tenants, deduplicate recipient email arrays so each unique workspace owner receives exactly one aggregated or clean per-tenant report.
4. **Rate Limit Lock**: Check `get_transient("cora_exec_report_lock_{$agency_id}")` before triggering report dispatches to prevent concurrent or repeated runs.

---

## 10. Regression & E2E Validation

Verify every new feature against the following automated and manual criteria:
* **Tenant Isolation**: Confirms data created in Agency 1 is completely invisible to Agency 2.
* **Single Owner Guard**: Confirms existing workspace owners cannot be duplicated or downgraded via user management.
* **Driver Mode Isolation**: Confirms driver accounts and vendor mode strip administrative headers, sidebars, and navigation islands.
* **Mobile Responsiveness**: Confirms all action panels open as bottom sheets without horizontal overflow.
* **No Browser Defaults**: Ensures zero native `alert()` or `confirm()` calls exist.
* **Zero Naked `!important`**: Ensures no un-namespaced global CSS overrides were introduced.
* **Phone Digit Constraints**: Confirms contact fields enforce numeric validation.
* **Scroll Lock State**: Confirms `html.cora-scroll-locked` is applied on drawer open and removed on close.
* **Tour Step Compatibility**: Validates that new high-level actions integrate with `#cora-platform-tour` targets.

---

## 11. CRM Kanban & High-Density Card Engineering Blueprint (v4.9.109 - v4.9.118)

When developing or extending CRM lead pipelines:

### 11.1 Ultra-Compact 3-Level Lead Card Schema
Always structure lead card DOM elements into 3 distinct functional tiers:
1. **Header Tier**: Client/Company name, urgent/priority dot, formatted deal amount in monospace currency (`₹XX,XXX`).
2. **Context Tier**: Stage badge, source pill (WhatsApp/Referral/Ads), estimated close timeline chip.
3. **Footer Action Tier**: Single-row compact CTA buttons (`wa.me` WhatsApp direct trigger, `tel:` dialer, `mailto:` composer, and stage shift popover).

### 11.2 In-Column Filtering & Dynamic Counter Synchronization
Kanban column controllers must implement client-side instant filtering:
```javascript
// Filter cards in-column with 0ms lag
function coraFilterKanbanColumn(stageId, query) {
    const cards = document.querySelectorAll(`[data-stage="${stageId}"] .cora-lead-card`);
    let visibleCount = 0;
    let visibleTotal = 0;
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const matches = text.includes(query.toLowerCase());
        card.style.display = matches ? '' : 'none';
        if (matches) {
            visibleCount++;
            visibleTotal += parseFloat(card.dataset.dealValue || 0);
        }
    });
    
    // Update live column count and valuation
    document.getElementById(`count-${stageId}`).textContent = visibleCount;
    document.getElementById(`total-${stageId}`).textContent = coraFormatCurrency(visibleTotal);
}
```

---

## 12. Dynamic Role Governance & Tab Customization Blueprint (v4.9.104 - v4.9.108)

When implementing RBAC or tab customizer features:
1. **Tenant-Scoped Roles**: Store custom agency roles in `wp_cora_roles` with `agency_id = %d` and unique slugs.
2. **Immutable Owner Lock**: Ensure the Workspace Owner role permissions cannot be edited or unassigned:
```php
if ($role_slug === 'workspace_owner') {
    wp_send_json_error(['message' => 'Workspace Owner permissions are immutable.'], 403);
}
```
3. **Dual-Layer Tab Customization Persistence**:
   - Save tab ordering immediately to `localStorage.setItem('cora_tabs_order', JSON.stringify(order))` for instantaneous 0ms painting on refresh.
   - Fire a background AJAX request to `cora_save_tabs_order` to persist preferences to user meta (`cora_user_tabs_order`).

---

## 13. Resizable Task Details Drawer & CRM Tasks Blueprint (v4.9.119 - v4.9.124)

When implementing task or deal inspection drawers:
1. **Drawer Anchor & Topbar Clearance**: Anchor precisely below the 48px global topbar (`top: 48px; height: calc(100vh - 48px);`) to keep system search and notifications accessible.
2. **Left-Edge Drag Handle**: Wire `#cora-task-drawer-resize-handle` to mouse/touch drag events (`window.coraInitTaskDrawerResize`), clamping width between `420px` and `840px` and persisting to `localStorage.getItem('cora_task_drawer_width')`.
3. **Zero Dark Backdrop**: Avoid darkening or intercepting clicks on the underlying board (`backdrop: none; pointer-events: none` on outer wrapper) so users can compare tasks against adjacent cards.
4. **Subtask Checklist with Progress Sync**: Dynamically calculate checklist completion:
```javascript
function coraUpdateSubtaskProgress() {
    const total = document.querySelectorAll('.cora-subtask-item').length;
    const done = document.querySelectorAll('.cora-subtask-checkbox:checked').length;
    const pct = total > 0 ? Math.round((done / total) * 100) : 0;
    document.getElementById('cora-task-progress-bar').style.width = pct + '%';
    document.getElementById('cora-task-progress-pct').textContent = pct + '%';
}
```
5. **Context Command Popover**: Right-click context menus (`#cora-task-context-menu`) must position relative to cursor with window viewport collision boundary guards.

---

## 14. White-Labeled Public Client Portal Blueprint (`public-client-portal.php`)

When engineering guest or client-facing views:
1. **Tokenized Authentication**: Authenticate via `token` query parameters against encrypted tenant access keys without requiring internal user accounts.
2. **Claude Cream Aesthetics**: Enforce Anthropic Claude warm cream backgrounds (`#FBFaf7`), minimal container cards, and `#E8E5DE` borders.
3. **Official SVG Marks Only**: Always use official SVG paths for external integrations (WhatsApp, Google Pay, PhonePe, Paytm, LinkedIn, X). Native emojis are strictly forbidden.

---

## 15. Dual-Reward Affiliate & Referral Architecture (v4.9.119 - v4.9.121)

When extending the growth and referral engine:
1. **Attribution Lifecycle**: Check incoming referral codes via cookie (`COOKIE_NAME = 'cora_referral_code'`) with 30-day expiration.
2. **Dual-Reward Dispatch**:
   - On free registration: Award `FREE_CREDITS = 100` to both referrer and referee.
   - On subscription activation: Record recurring `COMMISSION_PCT = 40.0` ledger entry.
3. **3-Step Partner Screener**: Enforce qualification step validation before returning affiliate links or dashboard metrics.

---

## 16. Dynamic AI Co-Founder & Continuous Voice AI Blueprint (v4.9.125 - v4.9.166)

When extending the conversational or voice AI engines:
1. **Conversation State Reset**: Implement 1-click `+ New Chat` reset (`window.coraStartNewConversation`) by clearing active chat arrays, restoring default greeting markup, and resetting token conversation IDs without page reloads.
2. **Strict 3px Quota Progress Bars**: All quota percentage bars must enforce `height: 3px !important` in CSS to prevent vertical ballooning into oval shapes across browsers.
3. **Background Scroll Lock SOP**: Always invoke `window.coraLockScroll()` when the AI drawer opens and `window.coraUnlockScroll()` when closed.
4. **Voice Mode Keyboard Suppression**: On voice mode activation, automatically suppress bottom text inputs and remove active element focus (`document.activeElement.blur()`) to prevent mobile keyboard popups.
5. **Standardized "CORA AI" Branding & Modern Sparkle SVG (v4.9.140)**: Standardize all assistant nomenclature to **CORA AI** / **Cora AI**, and render only the clean modern AI sparkle vector SVG (`stroke-width: 1.8`), avoiding legacy 5-point star icons.
6. **Chat History Drawer Integration (`#cora-ai-history-drawer`)**: Route past conversation retrieval and transcript switching through the dedicated slide-in drawer triggered via the header hamburger toggle.
7. **Balanced Brace Action Parsing (`cora_ai_extract_balanced_json`) (v4.9.162)**: Always extract action tags from model completions using algorithmic balanced brace counting to handle arbitrarily nested JSON objects safely.
8. **Enterprise Safety & Dual-Escalation Protocol (v4.9.139)**: Enforce policy evaluation against the 6 standard categories, persist incident records to `wp_cora_security_incidents`, and trigger simultaneous dual escalations to Platform Super Admins and Workspace Owners.

---

## 17. Core Foundation Modules Locking Blueprint (v4.9.189, v4.9.200)

When defining, modifying, or registering industry domain modules:
1. **Immutable Foundation List**: The following feature slugs are designated as permanent core platform infrastructure:
   ```php
   $foundation_features = ['blogs', 'forms', 'team-roles', 'media', 'vault', 'affiliates'];
   ```
2. **Domain Class Enforcement**: All industry classes extending or defining features (`class-*-module.php`) must mark foundation modules as enabled and protected.
3. **Runtime Micro-Guard**: In `cora-workspace.php`, `cora_get_custom_enabled_features()` automatically merges `$foundation_features` into both default and custom saved feature arrays, guaranteeing that foundation modules cannot be omitted or disabled even if tenant metadata becomes corrupt or out-of-sync.
4. **Feature Hub UI Rendering**: In `views/view-feature-hub.php`, foundation cards render `<span class="cora-foundation-badge">Foundation</span>` with a lock vector SVG and a disabled toggle switch. Batch controls (*Select All*, *Deselect All*, *Reset Defaults*) must preserve the active state of foundation modules.

---

## 18. Public Media Route Interception & Telemetry Blueprint (v4.9.187 - v4.9.188)

When engineering public share routes and proofing suites:
1. **Public Route Interception**: Intercept public routes via `template_redirect` or rewrite endpoints (`/workspace/shared-media/{token}`, `/workspace/share-media/{token}`, `share-media.php?cora_share={token}`) without requiring user session authentication.
2. **Client Impression Telemetry**: On page load, dispatch asynchronous AJAX request `cora_track_media_share_impression` with the share token. The backend increments total impressions, logs unique visitors via IP hashing/session identifiers, and records client user-agents.
3. **Download Tracking**: Download button triggers must call `cora_track_media_share_download` before triggering the asset stream to record download events.
4. **Audit KPI Cards & Filter Chips**: `view-media.php` must render 4 real-time scorecards (*Total Views*, *Unique Views*, *Total Downloads*, *Unique Downloads*) and interactive filter chips (`All`, `Downloads`, `Views`) to inspect public client interaction.
5. **Claude Cream Aesthetics**: Public proofing screens must strictly adhere to the Anthropic Claude design system (`#FBFaf7` warm cream background, minimal container cards, and `#E8E5DE` borders).

---

## 19. Multi-Dimensional Workspace Storage Footprint Blueprint (v4.9.183)

When auditing or displaying tenant storage usage:
1. **Comprehensive Footprint Function (`cora_get_workspace_storage_details`)**: Never calculate storage based solely on media attachments. Calculate across all four digital dimensions:
   - **Media Attachments**: File size on disk + database metadata records in `wp_posts`.
   - **Document Vault**: Legal PDF agreements, invoices, and contracts stored in uploads or encrypted directories.
   - **AI Chats & Memory**: Vector embeddings in `wp_cora_rag_knowledge`, cached options (`cora_ai_*`, `cora_rag_*`), and user metadata conversation logs.
   - **User Activity & Telemetry**: Logs in `wp_cora_activity_logs`, GPS tracking coordinates in `wp_cora_gps_telemetry`, audit events in `wp_cora_form_audit_log`, notifications in `wp_cora_notifications`, security incidents in `wp_cora_security_incidents`, and attendance stamps.
2. **Standardized Formatting**: Use `size_format()` to convert raw byte totals into human-readable MB / GB metrics. Display category pill breakdowns directly in the storage quota meters.

---

## 20. Field Ops Telemetry Lifecycle: Session Login Auto-Start & Beacon-Backed Flush Blueprint (v4.9.182)

When developing field operations, GPS tracking, and attendance modules:
1. **Auto-Start on Login Detection**: In `cora-field-ops-tracker.js`, detect authenticated user sessions upon workspace dashboard load. If user has field tracking permissions, automatically initiate `navigator.geolocation.watchPosition` with high accuracy and debounced interval throttling.
2. **Periodic Telemetry Heartbeat**: Send batched GPS points (`lat`, `lng`, `speed`, `accuracy`, `timestamp`) via AJAX action `cora_save_gps_telemetry` to populate real-time live maps and stop/rest detection algorithms.
3. **Zero-Loss Beacon Flush on Session Exit**:
   - Register window listeners for `beforeunload` and `pagehide`.
   - When closing the tab, navigating away, or logging out, flush pending GPS points and punch-out records using `navigator.sendBeacon(ajaxurl, payload)`.
   - `sendBeacon` guarantees asynchronous transmission without blocking UI thread unload or dropping pending punch-out records.

---

## 21. Central Tenant Authorization (`Cora_Authorization`) & Object-Level Gatekeeping Blueprint (v4.9.209 / SEC-002)

To prevent BOLA (Broken Object Level Authorization) and cross-tenant data leakage:
1. **Central Policy Gatekeeper (`includes/class-cora-authorization.php`)**: All tenant validation must route through `Cora_Authorization`.
2. **Agency Context Assertion (`cora_assert_agency_context`)**:
   ```php
   // At the top of every authenticated AJAX handler or REST endpoint:
   $agency_id = isset($_POST['agency_id']) ? intval($_POST['agency_id']) : 0;
   $auth_check = cora_assert_agency_context($agency_id);
   if (is_wp_error($auth_check)) {
       wp_send_json_error(['message' => $auth_check->get_error_message()], 403);
   }
   ```
3. **Object-Level Tenant Ownership Verification**: Never query records purely by primary key (`id = %d`). Always verify object-to-tenant association via `Cora_Authorization`:
   - `Cora_Authorization::can_access_task($task_id, $agency_id)`
   - `Cora_Authorization::can_access_form($form_id, $agency_id)`
   - `Cora_Authorization::can_access_document($doc_id, $agency_id)`
   - `Cora_Authorization::can_access_theme($theme_id, $agency_id)`
   - `Cora_Authorization::can_access_media($media_id, $agency_id)`

---

## 22. Outbound HTTP SSRF Defense Shield (`Cora_SSRF_Filter`) Blueprint (v4.9.209 / SEC-009)

When fetching external resources (URL migrators, webhooks, logo scrapers, RSS feeds):
1. **SSRF Guarding (`includes/class-cora-ssrf-filter.php`)**: Always validate external URLs prior to making remote requests:
   ```php
   if (!Cora_SSRF_Filter::is_safe_url($target_url)) {
       wp_send_json_error(['message' => 'The provided URL is restricted or invalid.'], 400);
   }
   ```
2. **Restricted Targets**:
   - Loopback IPs: `127.0.0.0/8`, `::1`.
   - RFC1918 Private Ranges: `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`.
   - Link-Local & Cloud Metadata Endpoints: `169.254.0.0/16`, specifically `169.254.169.254`.
3. **Pre-flight DNS Resolution**: Inspect the resolved IPv4/IPv6 address, preventing DNS rebinding attacks between check and request time.

---

## 23. Dedicated Early Route Interception (`/workspace/verify`) & Token Validation Blueprint (v4.9.201, v4.9.208)

When implementing public verification links, one-time passwords, or magic links:
1. **Early Route Hooking**: Intercept routes at priority `1` inside `cora_workspace_handle_workspace_route()` in `cora-workspace.php` before standard framework `template_redirect` or login guards execute.
2. **CSPRNG Cryptographic Entropy**:
   - Generate tokens using `bin2hex(random_bytes(32))` (64 hexadecimal characters / 256-bit entropy).
   - Store SHA-256 hash in user meta: `update_user_meta($user_id, 'cora_email_verification_token_hash', hash('sha256', $raw_token));`.
   - Validate using timing-safe comparisons: `hash_equals($stored_hash, hash('sha256', $input_token));`.
3. **Safe Pre-fetch Handling**: Allow HEAD or pre-fetch scanner requests without consuming or expiring the token prematurely.
4. **Seamless Authentication & Hydration**:
   - Mark `cora_email_verified = 1` and `cora_workspace_email_verified = 1`.
   - Invoke `wp_set_auth_cookie($user_id, true)` to authenticate the user's browser session.
   - Delete the consumed token hash to prevent replay attacks.
   - Redirect directly to `/workspace/dashboard` with a welcome toast, or mount `views/verify.php` for manual confirmation.

---

## 24. Action-Oriented Specialized AI Copilot & Synchronized DOM Blueprint (v4.9.190, v4.9.191, v4.9.198)

When equipping domain views with specialized AI assistants:
1. **Domain Context Injection**: Ground the assistant with live workspace data (e.g. storage metrics in `view-media.php`, staff roster in `view-users.php`, settings toggles in `view-settings-suite.php`).
2. **Structured Action Tag Contract**:
   - Instruct the LLM to output executable tags: `[ACTION:verb {"param1": "val1"}]`.
   - Parse completions using `cora_ai_extract_balanced_json` in PHP or matching balanced brace parsers in JavaScript.
3. **Bidirectional DOM Synchronization**:
   - When the AI executes an action (e.g. `update_settings` or `switch_settings_tab`), trigger corresponding client-side state changes immediately:
   ```javascript
   if (action.verb === 'switch_settings_tab') {
       window.coraSwitchSettingsTab(action.params.tab);
   } else if (action.verb === 'update_settings') {
       document.querySelector(`[name="${action.params.field}"]`).value = action.params.value;
       window.coraTriggerSettingsSave();
   }
   ```
4. **Strict Name Privacy**: Comply with Rule 3: never inject real owner names into copilot instructions, placeholders, or system prompts. Use generic names (`Rohan Verma`, `Kavya Patel`, `Aarav Mehta`).

---

## 25. Gamified Partner Growth & Recurring Commission Architecture Blueprint (v4.9.200 - v4.9.206)

When engineering growth and referral mechanisms:
1. **Permanent Foundation Status**: Protect the referral module as an immutable foundation module across all industry classes.
2. **Gamification Anatomy**:
   - **Progress Meter**: Visual milestone bar tracking referral milestones (Bronze → Silver → Gold → Diamond).
   - **Streak Multiplier**: Real-time velocity tracker (`⚡ 3-Streak Active (+5% Bonus)`).
   - **Podium Leaderboard**: Highlight top 3 performing partners with gold, silver, and bronze badges.
3. **Dual-Incentive Model**:
   - Free account signups award +100 AI Runes immediately to both referrer and referee.
   - Paid conversions trigger 20% monthly or 30% annual recurring commissions.
   - Milestone cash bonuses (₹1,000 / ₹5,000 / ₹10,000) incentivize scaling.
4. **Monochromatic Compliance**: Adhere strictly to Rule 4 and Rule 13: use tonal surface fills (`bg-zinc-100 dark:bg-zinc-800`), thin-line vector SVGs, and zero heavy black outline strokes.

---

## 26. Next.js Headless Git Repository Integration, Route Scanner & On-Demand ISR Revalidation (`class-cora-nextjs-integration.php`)

When connecting remote Next.js repositories (App Router / Pages Router) to the Cora Canvas ecosystem:
1. **GitHub REST Dynamic Tree Scanner (`scan_repository_routes`)**:
   - The scanner parses repository slugs (`owner/repo` or full GitHub URL).
   - Validates access against GitHub REST API with dual authorization header fallback:
     ```php
     $headers = array(
         'Authorization'        => ( str_starts_with( $pat, 'github_pat_' ) ? 'Bearer ' : 'token ' ) . $pat,
         'Accept'               => 'application/vnd.github+json',
         'User-Agent'           => 'Cora-Platform-NextJS/1.0',
         'X-GitHub-Api-Version' => '2022-11-28'
     );
     // On HTTP 401/404, automatically toggle between 'token ' and 'Bearer ' format before failing.
     ```
   - Dynamically discovers branches via `/repos/{owner}/{repo}/branches?per_page=100`, auto-selecting the repository's default branch (`main` or `master`).
   - Fetches the recursive Git tree (`/repos/{owner}/{repo}/git/trees/{branch}?recursive=1`).
2. **App Router Route Extraction Algorithm**:
   - Matches route page files via regex:
     ```php
     if ( preg_match( '#^(?:.+/)?(?:src/)?app/(.+/)?page\.(tsx|jsx|js|ts)$#i', $path, $matches ) ) {
         $sub_path = isset( $matches[1] ) ? trim( $matches[1], '/' ) : '';
         // Automatically filter out route groups such as (marketing), (auth), (dashboard)
         $cleaned_segments = array();
         if ( ! empty( $sub_path ) ) {
             foreach ( explode( '/', $sub_path ) as $seg ) {
                 if ( ! preg_match( '/^\(.*\)$/', $seg ) ) {
                     $cleaned_segments[] = $seg;
                 }
             }
         }
         $clean_route = empty( $cleaned_segments ) ? '/' : '/' . implode( '/', $cleaned_segments );
         $slug        = empty( $cleaned_segments ) ? 'home' : implode( '-', $cleaned_segments );
     }
     ```
   - Automatically supports Next.js Pages Router paths (`pages/(.+)\.(tsx|jsx|js|ts)$`) excluding internal Next.js entries (`_app`, `_document`, `_error`, `api/`).
3. **Canvas Database Ingestion**:
   - Ingests discovered paths as Canvas pages in `wp_cora_canvas_pages` bound to the target theme ID, marking `/` as `is_homepage = 1`.
4. **On-Demand ISR Revalidation Hook (`cora_nextjs_trigger_isr` / `/nextjs/revalidate`)**:
   - Dispatches authenticated webhook POST request to `{live_url}/api/revalidate?secret={secret}&path={path}`.
   - Enforces 12-second timeout, tracking roundtrip execution time in milliseconds (`duration_ms`).
   - Updates page props (`_cora_nextjs_props`) with auto-revalidation trigger flag upon save.

---

## 27. Live Next.js Dev Server Detection, Cookie-Isolated Preview Router & Proxy Engine (`cora-workspace.php`)

When serving Next.js applications through the Canvas live preview and public theme router:
1. **Frontend Router Delegation (`cora_canvas_theme_frontend_router`)**:
   - Intercepts requests destined for public Canvas pages.
   - If the active theme or preview theme source is `nextjs`, it skips static HTML generation and routes immediately to `cora_render_nextjs_live_frame()` or `cora_render_nextjs_connector_screen()`, completely eliminating generic welcome fallback screens.
2. **Fast Sub-150ms Offline Dev Server Detection**:
   - Before iframe hydration, executes an asynchronous non-blocking probe check:
     ```javascript
     if (window.fetch && window.CORA_NEXTJS_LIVE_URL) {
         fetch(window.CORA_NEXTJS_LIVE_URL, { mode: 'no-cors', cache: 'no-store' })
             .then(function() { /* Server reachable */ })
             .catch(function(err) { showOffline(); });
     }
     ```
   - If `http://localhost:3000` is offline or blocked, `showOffline()` activates instantly (sub-150ms), revealing a dark blurred terminal instruction card overlay (`#cora-error-overlay`) instructing the developer to run `npm run dev` with terminal copy shortcuts, change URL controls, and external launch links.
3. **Strict Preview Cookie Isolation Standard (`cora_preview_theme_id`)**:
   - In `cora_get_preview_theme_id()`, the preview cookie is strictly scoped.
   - If a request is received on `/site/` without explicit `preview=1` or `cv_preview_theme` query parameters, and a live production theme exists for the workspace, the system purges the cookie via `setcookie('cora_preview_theme_id', '', time() - 3600, '/')`, unsets `$_COOKIE['cora_preview_theme_id']`, and returns `0`.
   - Prevents stale preview state from leaking into organic tenant traffic.
4. **Bidirectional PostMessage Bridge**:
   - Synchronizes iframe routing state with parent dashboard URL via `CORA_ROUTE_CHANGE` events.
   - Emits `CORA_INIT` payload carrying API URLs, REST nonce, theme ID, and target routes on iframe load.

---

## 28. Universal Remote MCP Server, OAuth 2.1 PKCE & Dynamic Client Registration (`class-cora-universal-mcp-server.php`, `class-cora-oauth-server.php`)

When integrating Cora with AI agents via the Model Context Protocol (MCP) and secure OAuth 2.1:
1. **Dual Transport Architecture**:
   - **JSON-RPC 2.0 over HTTP POST**: Dispatches tool execution and prompt completions directly to `/wp-json/cora-mcp/v1/rpc`.
   - **Server-Sent Events (SSE) Streaming**: Dedicated streaming transport on `/wp-json/cora-mcp/v1/sse` for real-time agent notifications and continuous context delivery.
   - Supports protocol versions `2026-07-28`, `2025-11-25`, `2024-11-05`, and `2024-10-07`.
2. **OAuth 2.1 PKCE Enforcement (RFC 6749, RFC 7636)**:
   - Eliminates legacy implicit and password grants in favor of authorization code exchange with mandatory PKCE (`code_challenge_method=S256`).
   - Authorization codes expire after 10 minutes (`AUTH_CODE_EXPIRY_SECONDS = 600`).
   - Access tokens expire after 30 days; refresh tokens expire after 90 days with rotation.
3. **RFC 7591 Dynamic Client Registration (`POST /oauth/register`)**:
   - Enables zero-configuration connection setup for AI platforms (Google Gemini, Claude Desktop, Cursor, Windsurf, ChatGPT).
   - Generates unique client credentials (`client_id`, `client_secret`) dynamically with registered redirect URIs and grant types.
4. **RFC 9728 Protected Resource Metadata & WWW-Authenticate**:
   - Serves discovery metadata at `/.well-known/oauth-protected-resource` and authorization metadata at `/.well-known/oauth-authorization-server`.
   - Unauthorized requests return HTTP 401 with explicit header:
     `WWW-Authenticate: Bearer resource_metadata=".../.well-known/oauth-protected-resource", realm="cora-mcp", error="invalid_token"`
5. **Tenant Context Hydration & Rate Limiting**:
   - Bearer token authentication automatically hydrates tenant scoping (`cora_set_current_tenant_agency_id`) and sets the active user session context.
   - Transient-backed sliding window rate limiter strictly enforces 120 requests per minute (`RATE_LIMIT_RPM = 120`).

---

## 29. 3-Step Van Dispatch Wizard, Weight-Based Allocation & Real-Time Spot Billing (`class-cora-inventory-engine.php`)

When implementing mobile field sales and warehouse logistics:
1. **3-Step Modal Dispatch Wizard**:
   - **Step 1: Driver & Van Assignment**: Binds field driver/vendor account, vehicle registration number, route territory, and departure odometer.
   - **Step 2: Cargo & Weight Allocation**: Dynamic product inventory selector displaying available plant quantities in kilograms and unit packs. Computes live Gross Weight, Tare Weight, and Net Weight, validating against vehicle payload capacities and plant stock balance.
   - **Step 3: Review & Dispatch Manifest**: Renders complete consignment summary, verifies stock availability, and issues unique alphanumeric manifest code (e.g. `MAN-2026-0042`).
2. **Atomic Inventory Reservation**:
   - Generating a consignment atomically deducts allocated kilograms/units from parent plant stock in `wp_cora_inventory_products` and inserts records in `wp_cora_inventory_consignments` and `wp_cora_inventory_consignment_items`.
3. **Real-Time Spot Billing Engine (`cora_inventory_record_spot_sale`)**:
   - Mobile field sales vendors record shop deliveries on location.
   - Implements standardized unit pricing per kilogram (e.g. ₹401.25/kg) with dynamic gross/net calculations.
   - Emits automatic 4-digit sequential invoice identifiers (`#1001`, `#1002`).
   - Deducts sold stock directly from the driver's active mobile consignment balance.
4. **Safe Restocking Rollback Protocol**:
   - Voiding or deleting spot sales or consignments executes transactional rollback queries, returning unconsumed weights and unit packs back to warehouse inventory.

---

## 30. Itemized Sales Register, Ledger Export & Auto-Print Manifest Architecture

When generating accounting registers and warehouse shipping documentation:
1. **Standardized Sales Register Structure (`cora_inventory_get_sales_register`)**:
   - Tabular layout enforces standardized column ordering:
     1. `Bill #` (Formatted as clean 4-digit numeric string: `1001`, `1002`).
     2. `Customer / Firm Name` (Prominently displayed before transaction date).
     3. `Date` (Formatted strictly as date-only: `d M Y`, omitting cluttering time stamps).
     4. `Itemized Cargo Details` (Sold weight in kg, product names, pack counts).
     5. `Total Amount (₹)` (GST-inclusive final bill amount).
     6. `Actions` (View Invoice, Print Tax Receipt, Delete / Rollback).
2. **Standardized 5-Column Dispatch Report**:
   - For warehouse logistics audits: `Manifest #`, `Vehicle / Driver`, `Allocated Weight (kg)`, `Dispatched Date`, `Status`.
3. **Standardized 4-Column Plant Inventory Sheet**:
   - For factory floor audits: `SKU / Item Name`, `Category`, `Available Stock (kg / Packs)`, `Total Plant Valuation (₹)`.
4. **Streaming CSV Exporter**:
   - `cora_inventory_export_sales_csv` streams itemized transaction data with UTF-8 BOM encoding for complete Excel/Sheets compatibility, splitting sold line items into distinct data rows.
5. **Print Manifest Engine (`@media print`)**:
   - Dedicated print-optimized layouts (`cora_inventory_render_van_stock_sheet`, `cora_inventory_render_sale_invoice`) featuring high-density monochromatic styling, carrier details, cargo checklists, driver signature lines, and receiver acknowledgment stamps.

---

## 31. Full-Height Edge-to-Edge Draggable Lead Drawer & Bi-Directional Synchronizer (`view-leads.php`, `admin-script.js`)

When building CRM lead inspection and contact management interfaces:
1. **Dynamic Body Portaling Pattern**:
   - To eliminate clipping issues caused by parent scroll containers (`overflow: hidden`) and CSS transform contexts, drawers are portaled directly into `document.body`:
     ```javascript
     if (drawer && drawer.parentElement !== document.body) {
         document.body.appendChild(drawer);
     }
     ```
2. **Edge-to-Edge Anatomy**:
   - Replaces floating centered dialogs and snapping drawers with a full-height right sheet (`top: 0; bottom: 0; right: 0; height: 100vh; position: fixed; z-index: 99999;`).
   - Pairs with a soft transparent backdrop (`background: rgba(9, 9, 11, 0.25); backdrop-filter: blur(4px);`) to preserve visibility of underlying pipeline cards.
3. **Continuous Draggable Resizer Engine (`window.coraInitLeadDrawerResize`)**:
   - Integrated left-edge drag resizer handle (`#lead-drawer-resizer`).
   - Binds `mousedown` and `touchstart` events with hardware-accelerated movement handlers.
   - Enforces clamp boundaries (minimum width: `420px`, maximum width: `960px`).
   - Persists width preferences in `localStorage['cora_lead_drawer_width']` for instantaneous restoration across workspace sessions.
4. **Bi-Directional State Synchronization**:
   - Form mutations inside the drawer (stage transition, qualification score, assignee update, notes) immediately patch corresponding Kanban cards and table row nodes in real time without triggering full view re-renders.

---

## 32. Automated GST Invoice Document Vault Registration & SHA-256 Linkage (`view-financials.php`, `cora-workspace.php`)

When publishing tax-compliant GST invoices and archiving fiscal records:
1. **Automated Vault Archiving Pipeline**:
   - Finalizing and publishing a GST tax invoice in Financials automatically scaffolds an immutable legal record in the Document Vault (`wp_cora_vault_documents`).
   - Automatically files the record under the `Invoices & Billing` vault category with `verified` audit status.
2. **Cryptographic SHA-256 Tamper Protection**:
   - On invoice creation or finalization, the system computes a 256-bit cryptographic digest over invoice attributes (seller GSTIN, buyer GSTIN, invoice number, HSN codes, taxable subtotal, tax breakdown, and timestamp):
     ```php
     $checksum_sha256 = hash( 'sha256', wp_json_encode( array(
         'invoice_number' => $invoice_number,
         'agency_id'      => $agency_id,
         'gstin'          => $client_gstin,
         'subtotal'       => $subtotal,
         'tax_amount'     => $tax_amount,
         'total'          => $grand_total,
         'line_items'     => $line_items,
         'issued_at'      => $issued_at
     ) ) );
     ```
3. **Bidirectional Foreign Key Linkage**:
   - The invoice record stores `vault_document_id` in its metadata.
   - The vault document record stores `invoice_id`, `invoice_number`, and `checksum_sha256` in its metadata.
   - Guarantees non-repudiation, automated PDF delivery to client portals, and instantaneous compliance auditing.

---

## 33. Tokenized Administration Email Verification & Locked Security Workflow (`view-settings-suite.php`, `cora-workspace.php`)

When updating primary administration email addresses across workspace tenants:
1. **Threat Mitigation**:
   - Prevents unauthorized tenant hijacking, accidental typos, or malicious email redirections.
2. **Dual-Stage State Machine**:
   - **Active Verified State**: Renders the current operational email in a read-only locked input with a green shield badge (`Verified & Protected`) and an explicit "Change" action trigger.
   - **Pending Verification State**: Initiating an email change creates a pending request while keeping the current admin email operational. The UI renders a dedicated alert card with the pending address, an amber status pill (`Verification Pending`), a 24-hour expiration notice, a resend trigger, and a cancel button.
3. **CSPRNG Cryptographic Token Generation**:
   - Generates a 32-byte cryptographically secure pseudorandom token (`bin2hex(random_bytes(32))`).
   - Stores the SHA-256 hash in option `cora_pending_admin_email_hash_{agency_id}` with expiration timestamp `cora_pending_admin_email_expires_{agency_id}`.
4. **Early Route Interception & Atomic Migration**:
   - The verification email links to `/workspace/verify?admin_email_token={raw_token}&agency_id={id}`.
   - Intercepted early in `cora_workspace_handle_workspace_route()` prior to standard framework template execution.
   - Validates token using timing-safe `hash_equals()`.
   - On successful verification, atomically updates the administration email, deletes pending options, logs an audit event, and redirects to Settings with a monochromatic confirmation toast.

---

## 34. Canvas AI Starter Prompt Generator & Master Prompt Template Architecture (`view-canvas.php`, `cora-workspace.php`)

When generating turnkey prompt-driven frontend templates for Claude & ChatGPT:
1. **Curated Template Factory (`cora_canvas_get_default_starter_html`)**:
   - Returns production-ready, fully responsive HTML5 landing pages themed for target business verticals (`agency`, `saas`, `consulting`, `photography`, `custom`).
   - Includes semantic metadata, hero section, interactive components, customer proof, and lead capture forms.
2. **Script Tag Sanitization Standard**:
   - When injecting prompt template strings or inline JavaScript, all closing `</script>` tags must be escaped as `<\/script>`.
   - Prevents premature script parser termination in modern browser HTML engines.
3. **1-Click Extraction & Clipboard Integration**:
   - Add Theme Wizard Step 2 provides dedicated `📋 Copy Prompt for Claude / ChatGPT` buttons.
   - Formats a comprehensive system prompt enforcing Tailwind CSS via CDN, Lucide vector icons, single-file HTML structure, and native Cora lead form hooks.

---

## 35. Automated Static Site Compiler & Exporter Pipeline (`class-cora-nextjs-integration.php`, `cora-workspace.php`)

When executing native static compilation of modern web frameworks on Cora hosting:
1. **Runner Process Isolation (`proc_open`)**:
   - Executes Node.js builds via `proc_open` with isolated process pipes and environment variables, bypassing disabled `exec()` functions on hardened PHP environments.
2. **Shared Hosting Resource Limits**:
   - Sets `NODE_OPTIONS="--max-old-space-size=768"` and `RAYON_NUM_THREADS=1` to remain within CloudLinux memory ceilings.
   - Automatically cleans stale npm caches (`~/.npm/_cacache`) before compilation to prevent Inode quota exhaustion (`-122 EDQUOT`).
3. **Static Distribution Extraction**:
   - Bundles compiled static files (`out/`, `dist/`, `build/`) directly into localized workspace storage (`wp-content/uploads/cora-canvas/{theme_id}/dist`).
   - Streams static JS/CSS assets (`/_next/*`, `/_expo/*`) directly on `init` priority 1 with zero CMS template overhead.

---

## 36. Universal Lead Form Ingestion & Live Site CRM Interception Bridge (`cora-workspace.php`, `views/view-canvas.php`)

When connecting static website forms to the Cora CRM pipeline:
1. **Client-Side Form Interception**:
   - Auto-injects lightweight JavaScript into served static HTML pages.
   - Intercepts form submit events targeting `/wp-json/cora-canvas/v1/nextjs/forms/submit` or `.cora-form-embed`.
2. **Lead Database Persistence**:
   - Extracts and sanitizes input fields (Name, Email, Phone, Budget, Message).
   - Inserts record into `wp_cora_leads` with `status = 'new'` and tags the active workspace tenant.
3. **In-Place Feedback**:
   - Renders a clean monochromatic confirmation card in-place without page reloads or layout shifts.

---

## 37. Security Path Containment, ZipArchive Sanitization & HTTP 304 Caching (`cora-workspace.php`)

When serving static theme assets and processing user archive uploads:
1. **Strict `realpath` Containment**:
   - Resolves real canonical paths and ensures target files strictly reside within `$real_static_dir`.
   - Rejects traversal attempts (`../`) before file access.
2. **ZipArchive Security Scrutiny**:
   - Iterates through zip entries and skips any filename containing directory traversal sequences.
3. **Conditional HTTP 304 ETag Caching**:
   - Computes deterministic MD5 ETags from file path and timestamp.
   - Evaluates `HTTP_IF_NONE_MATCH` and returns instant 304 Not Modified headers.

---

## 38. Super Admin Dynamic PWA Badging & Role-Scoped Web App Manifest (`cora-workspace.php`, `admin-dashboard.php`)

When rendering PWA icons and manifests for privileged administrators:
1. **Dynamic Icon Generation (`scripts/generate_admin_pwa_icons.php`)**:
   - Overlays high-contrast `#09090b` badge with `#10b981` emerald dot and bold white `ADMIN` text across 512x512, 192x192, Apple touch, and favicon assets.
2. **Role-Scoped Manifest Scoping**:
   - Dynamically checks `current_user_can('manage_options')` or `?admin=1`.
   - Outputs `CORA Workspace (Admin)` name and `/workspace/dashboard?role=admin` start URL to guarantee independent home screen app instances.

---

## 39. OpenAPI 3.0.1 Specification & Curated Action Generator (`cora-workspace.php`)

When serving OpenAPI documentation for ChatGPT Custom Actions:
1. **Strict 3.0.1 Compatibility**:
   - Outputs `'openapi' => '3.0.1'` to guarantee seamless ingestion by OpenAI Custom GPT action validators.
2. **28-Tool Priority Ceiling**:
   - Default discovery filters tools to 28 priority operations (Canvas, Leads, Clients, Projects, Tasks, Financials, Content, RAG, Overview).
   - Allows opt-in full exposure via `?all=1` or `limit=all`.
3. **Schema Sanitization**:
   - Forces input schemas to `type: object` with non-empty properties objects.
   - Omits empty `required` arrays and purges emojis from summaries and descriptions.

---

## 40. Interactive OAuth 2.1 PKCE Consent Architecture (`class-cora-oauth-server.php`)

When authenticating third-party AI assistants via OAuth 2.1:
1. **Master Full-Access Control**:
   - Provides an interactive master toggle that selects or deselects all 20 scopes instantaneously.
2. **Preset Categorization**:
   - Exposes quick preset triggers: `All Tools`, `Read-Only`, and `Website & Themes Only`.
   - Displays real-time granted scope counts before authorization.
3. **Cryptographic Binding**:
   - Issues 32-byte CSPRNG authorization codes and access tokens strictly bound to the authenticated workspace tenant (`agency_id`).

---

## 41. Template Editor Live Action Playground & MCP Code Reflection (`view-canvas.php`)

When testing and applying AI actions within the Canvas Template Editor:
1. **Tri-Mode Operational Interface**:
   - Supports Natural Language, Guided Tool Selection, and Raw JSON-RPC 2.0 modes.
2. **Live Tool Execution**:
   - Dispatches requests directly to `/wp-json/cora/v1/mcp` or localized REST tool routes.
3. **1-Click Code Reflection**:
   - `window.coraApplyPlaygroundCodeToEditor()` automatically injects returned HTML into the CodeMirror editor and commits updates directly to the static build distribution (`out/index.html`).

---

## 42. Canvas Drawer Standard & Body Overflow Lock State Machine (`view-canvas.php`)

When presenting editor drawers and template settings:
1. **Right-Sliding Side Drawer Architecture**:
   - All drawers use full-height slide-in sheets (`.cora-drawer`) instead of floating modal dialogs.
2. **Body Overflow Lock**:
   - Adds `body.cora-canvas-drawer-open` on drawer mount (`overflow: hidden !important`).
   - Removes class on drawer unmount, completely eliminating background page scroll jumping and layout jitter.

---

## 43. AI Run Quota Pacing, Fair Usage Architecture & Super Admin Bypass (`cora-workspace.php`)

When managing conversational and tool-calling AI token allowances:
1. **Tier Allocation**:
   - Free / Starter: 500 runs/month (weekly pacing: 125 runs/week)
   - Pro: 15,000 runs/month (weekly soft limit: 3,750 runs/week)
   - Scale: 25,000 runs/month (weekly soft limit: 6,250 runs/week)
2. **Anti-Burnout Pacing**:
   - Soft throttling checks warning thresholds at `ceil($monthly_quota / 4)` to notify agencies before depleting monthly pools early.
3. **Super Admin Unrestricted Access**:
   - Super Admins and users with `manage_options` bypass all rate limiting, session timeouts, and AI quotas.

---

## 44. Platform-Wide Affiliate & Conversion Tracking Suite (`Endorsely`)

When attributing partner referrals across the marketing site and workspace checkout:
1. **High-Speed CDN Loading**:
   - Injected in root Next.js layout (`cora-frontend/app/layout.tsx`) using `https://app.endorsely.com/assets/affiliate-tracking.js` with defer loading.
2. **Checkout Conversion Bridge**:
   - Dispatches conversion events on verified Razorpay payments with order ID, monetary value, and customer email.

---

## 45. Live HTML Preview Resilience Shield & Workspace Forms Embedding (`view-canvas.php`)

When rendering and embedding custom landing pages:
1. **Automatic Tailwind CSS CDN Injection**:
   - Injects `<script src="https://cdn.tailwindcss.com"></script>` into preview frames and static distribution bundles if Tailwind classes are detected without existing stylesheets.
2. **Iframe Error Boundary**:
   - Catches broken scripts, eliminating blank screen freezes.
3. **Dynamic Forms Shortcode & Iframe Generator**:
   - Supports `[cora-form id="..."]` shortcodes and responsive `<iframe src="...">` embeds with instant CRM lead pipeline routing.

---

## 46. Marketing Site Reform & Discovery Layer Architecture (`cora-frontend`)

When developing and extending marketing pages, public calculators, and use case showcases:
1. **Unified Headline & Value Proposition**:
   - Strictly adhere to the canonical platform positioning: *"Build websites. Run your business, with an AI co-founder."*
2. **Dual-Track Conversion Pathways**:
   - Primary Action: Google One-Tap onboarding with immediate tenant workspace creation.
   - Secondary Action: "Explore free templates" linking directly to the 16+ pre-seeded turnkey schemas at `/use-cases`.
3. **Tools & Templates Directory Architecture**:
   - Marketing navigation must route users to interactive business calculators (GST simulator, retainer target margin calculator, proposal builder) and production website templates.
   - Lead forms across static landing page templates must automatically submit to `/api/v1/lead` (or `wp_cora_leads`) with attribution metadata for campaign and schema tracking.

---

## 47. Conversational AI SDR & Conversion Coupon Engine (`HeroAIInput.tsx` & `/api/ai-preview`)

When implementing and extending conversational lead acquisition components:
1. **True Viewport Body Portaling**:
   - Always mount floating widgets via `createPortal(..., document.body)` to avoid container bounding box clipping and scroll jitter.
   - Maintain zero backdrop dimming overlay so visitors can continue reading marketing copy while conversing with the assistant.
2. **Active-Voice Query Processing**:
   - Routes user queries through `cora-frontend/app/api/ai-preview/route.ts` with domain-specific system prompts.
   - Responses must be concise, active-voice, and under 3 sentences with clear next steps.
3. **Automated Objection Vector Handling**:
   - Address common switching hurdles (e.g. Notion/HoneyBook vs Cora, white-glove migration, annual discounts, legacy multi-plugin instability).
4. **Dynamic Promotional Offer Generation**:
   - Attach structured `DedicatedOffer` cards (`MIGRATEFREE`, `CORA10`) that auto-apply coupon query parameters when routing to `/pricing`.

---

## 48. Multi-Format Template & Theme Distribution Engine (`/templates`, `lib/templates-data.ts`)

When adding or updating templates in the template registry:
1. **4 Canonical Distribution Formats**:
   - `native_page`: Pure Tailwind CSS and Next.js 15 production page with responsive mobile bottom sheets.
   - `section_prompts`: Engineered prompts for Claude 3.5 Sonnet and Cursor Composer with design system token references.
   - `elementor_kit`: Standardized Elementor JSON blueprints for instant page builder import.
   - `lovable_project`: Full-stack scaffolding blueprints tailored for Lovable.dev and v0.dev.
2. **Interactive Modal Preview**:
   - Provide sandboxed iframe preview with responsive viewport switchers (`desktop`, `tablet`, `mobile`).
   - Include 1-click clipboard prompt export and raw asset download actions.
3. **Automated CRM Pipeline Bridge**:
   - Ensure all contact forms in landing templates post to `/api/v1/lead` (or `wp_cora_leads`) with schema attribution.

---

## 49. Autonomous AI Workforce & Modular Agent Portfolios

When configuring domain agent behaviors and user experiences:
1. **Domain Agent Personas**:
   - Maintain dedicated system prompts for 9 business domains: Operations, Sales, Contracts, Knowledge, Creative, Delivery, Finance, Legal, and Marketing.
2. **Visual Consistency**:
   - Use standardized monochrome-first agent portrait assets located at `cora-frontend/public/images/cora_agent_*.jpg`.
3. **Active Voice Specifications**:
   - UI cards must use concise single-line titles and maximum 2-line active-voice descriptions.

---

## 50. Form Cards Overhaul & Universal "All Entries" 5th Tab Architecture (`view-forms.php`, `cora-workspace.php`)

Shipped in **v4.9.372**, this blueprint standardizes form response discovery and ergonomics:
1. **Card Padding & Mobile Unlocking Standard**:
   - Form card container padding expands to `p-5 sm:p-6` with dedicated `p-3.5` inner container for campaign info and `px-3 py-1.5` status badges.
   - Clicking `.form-card` routes directly to `#edit/${form.id}`, preserving separate click handlers on preview and action buttons.
   - Removed `< 1024px` viewport blockers in `handleRouting()` and `loadFormIntoEditor()`, allowing mobile and tablet users to edit forms.
2. **In-App Interactive Form Live Preview Modal (`#cora-form-preview-modal`)**:
   - Sandboxed live preview modal allowing founders to test forms with responsive viewport toggles (`Desktop`, `Mobile`) and an external tab trigger.
3. **Universal 5th Top Tab: "All Entries" Registry (`#all-entries`)**:
   - Registered in `$forms_header_args['tabs']` with dynamic live count badge (`#tab-all-entries-badge`).
   - Metric summary strip (Total Submissions, Active Forms with Entries, Completion Rate, Latest Activity).
   - Notion-style filter toolbar: Form source dropdown, Status dropdown (Completed / Partial), live search input, CSV export, and refresh.
   - `table-fixed` column widths (`w-14`, `w-48 md:w-56`, `w-auto`, `w-24`, `w-36`, `w-28`, `w-24`) preventing text overflow and column blowing.
4. **Submissions REST API**:
   - `GET /wp-json/cora/v1/forms/all-submissions`: Returns all agency submissions with form title, form key, and settings JOINed.
   - `DELETE /wp-json/cora/v1/forms/submissions/(?P<id>\d+)`: Secure REST deletion endpoint with multi-tenant tenancy verification and custom `coraConfirmAction` modal.
5. **Dynamic Field CSV Export**:
   - Dynamically parses distinct field keys across all submitted data payloads and streams standard CSV files.

---

## 51. Universal Edge-Docked Entry Inspector & Zero-Backdrop Architecture (`view-forms.php`, `assets/css/admin-style.css`)

Deployed in **v4.9.374**, the Entry Inspector (`#cora-entry-inspector`) provides an ergonomic detail viewer:
1. **Edge-Docked Layout Standard**:
   - **Desktop Viewports (>= 768px)**: Slides smoothly from the right edge (`top-0 right-0 bottom-0 w-[480px] max-w-[90vw] border-l border-zinc-200 bg-white translate-x-0`).
   - **Mobile Viewports (< 768px)**: Slides up from the bottom as a high-density sheet (`inset-x-0 bottom-0 h-[85vh] max-h-[85vh] rounded-t-3xl border-t border-zinc-200 bg-white translate-y-0`).
2. **Zero-Backdrop Overlay Clarity**:
   - Completely purged dark and blurred backdrop overlays. The underlying data table remains 100% visible, sharp, and readable.
   - Seamless outside click listener on the document body dismisses the inspector without modal friction.
3. **Submitter Quick Overview Strip**:
   - Renders avatar initials, primary submitter name, and email/phone quick chips at the top of the inspector panel.
4. **Inline Signature & Media Asset Rendering**:
   - Inspects field values for image MIME types and base64 digital signatures (`data:image/...`).
   - Renders inline `<img src="..." class="max-h-32 rounded-lg border border-zinc-200 object-contain bg-zinc-50">` rather than dumping 50,000-character raw base64 strings.
5. **Sanitized Summary & Sticky Action Footer**:
   - Long JSON blobs and signatures are sanitized in table views as `[Uploaded File / Signature]`.
   - Sticky footer with 1-click "Copy Raw JSON", "Delete Entry", and "Done" actions.

---

## 52. Client Timezone Engine & Localized Timestamps Architecture (`cora-workspace.php`, `view-forms.php`)

1. **Automatic Client Timezone Detection**:
   - Automatically detects browser timezone using `Intl.DateTimeFormat().resolvedOptions().timeZone` on client hydration.
   - Converts UTC server timestamps to localized timestamps across submission tables, entry inspectors, and audit logs.
2. **Dual-Format Display**:
   - Renders human-readable relative timestamps ("10 minutes ago", "Yesterday at 4:15 PM") paired with full localized ISO tooltips on hover.

---

## 53. Dynamic Affiliate Referral Tracking API & Checkout Discount Engine (`includes/affiliate-referral-engine.php`, `cora-frontend/lib/referral.ts`)

1. **Backend Referral Tracking API**:
   - Dedicated REST endpoint: `POST /wp-json/cora/v1/referrals/track` logging impressions, referral links, UTM tags, user agents, and conversion milestones into `wp_cora_referrals`.
   - Frontend proxy route: `cora-frontend/app/api/referral/track/route.ts` bridging public marketing visitors with backend attribution databases.
2. **Multi-Cookie & Parameter Ingestion Engine (`cora-frontend/lib/referral.ts`)**:
   - Captures query parameters: `?cora_ref=...`, `?ref=...`, `?via=...`, `?partner=...`.
   - Stores referral tokens in secure 30-day cookies and `localStorage` fallback.
   - Dispatches automated impression pings on page landing.
3. **Automated Checkout Coupon Discount Application**:
   - When users navigate to checkout via an affiliate link, `CheckoutSheet.tsx`, `FastRazorpayModal.tsx`, and `PricingSection.tsx` automatically detect the active referral code.
   - Applies instant 10%–20% coupon discount deductions directly against plan pricing.
   - Displays a clean monochromatic referral savings badge showing partner discount savings.
   - Fires dual attribution hooks upon payment verification: Endorsely script tracking (`window.endorsely?.trackConversion()`) and Cora backend conversion logging.

---

## 54. Unified Global Resources Hero Section Architecture (`cora-frontend/components/resources/GlobalResourcesHero.tsx`)

1. **Standardized Header Component**:
   - Unifies the top hero presentation across all 6 platform knowledge directories: Templates (`/templates`), Tools (`/tools`), Guides (`/guides`), Blog (`/blog`), Newsletter (`/newsletter`), and Articles (`/articles`).
2. **Visual Continuity & Search Experience**:
   - Consistent editorial typography, subtle monochromatic badges, centered search input with keyboard shortcut hints (`⌘K`), and fluid category pills.
   - Responsive layout scaling cleanly from 320px mobile screens to 4K ultra-wide displays.

---

## 55. Pricing SaaS Hierarchy, Explicit Resource Specs & Coaching Accelerator Portal

1. **Pricing Hierarchy & Explicit Specs**:
   - Plan cards feature SaaS hierarchy pills, active-voice copy, explicit storage (5GB–50GB) and bandwidth (25GB–500GB) specifications, floating "Free Site Migration" badge on Scale, and pure monochromatic switcher pills.
2. **Partner Coaching Accelerator Portal (`/vnxdigital`, `/vanshrajput`)**:
   - Co-branded partner landing page for VNX Digital members with creator social proof, high-converting walkthroughs, and locked 3-day trial to Full Coaching Accelerator.

---

## 56. Industry Solutions Directory & 301 Redirect Parity Blueprint (`cora-frontend/app/industries/*`, `lib/industry-data.ts`)

When expanding or modifying industry vertical directories:
1. **Canonical `/industries` Route Structure**:
   - Root index at `cora-frontend/app/industries/page.tsx` rendering `IndustriesPageClient.tsx`.
   - Dynamic dynamic routes at `cora-frontend/app/industries/[slug]/page.tsx` rendering `IndustryDetailClient.tsx`.
2. **Backward-Compatible 301 Redirects**:
   - Maintain route rewrites in `cora-frontend/public/.htaccess` and Next.js route mapping:
     ```apache
     RewriteRule ^use-cases/?$ /industries [R=301,L]
     RewriteRule ^use-cases/(.*)$ /industries/$1 [R=301,L]
     ```
   - Retain legacy `/use-cases` page handlers delegating directly to industry data, preserving external inbound links and organic rank.
3. **10+ Verified Vertical Schemas (`lib/industry-data.ts`)**:
   - Enforce plain-English, active-voice copy across all verticals (Real Estate, Photography, Architecture, Creative Agency, Consulting, Salons, Fitness, Events, Hospitality, DTC).
   - Structure data objects with `hero`, `capabilities`, `workflows`, `metrics`, `comparison`, and `faqs`.
4. **Desktop Mega-Menu & Mobile Drawer Alignment (`Navbar.tsx`)**:
   - Synchronize dropdown categories with vertical icon tiles, subtle pill indicators, and high-contrast hover states adhering strictly to Rule 13 tonal backgrounds.

---

## 57. Autonomous AI Multi-Agent Workforce & 4-Row Infinite Skills Marquee Architecture (`cora-frontend/app/ai-agent/page.tsx`)

When developing agent workforce canvases and skills visualizers:
1. **High-Resolution Neural Cutout Assets**:
   - Store segmented PNG cutouts at `cora-frontend/public/images/cora_hero_agent_cutout.png`.
   - Blend cutouts into the canvas using CSS background gradients and soft lighting radial blends without harsh container bounding lines.
2. **4-Row Infinite Skills Marquee Engine (`InfiniteSkillsSection.tsx`)**:
   - Implement staggered continuous CSS animation (`@keyframes marquee`) across 4 distinct operational streams:
     * Stream 1: Operations, shifts, and scheduling logistics.
     * Stream 2: Sales pipeline, lead capture, and outreach automation.
     * Stream 3: GST invoicing, contract vaults, and tax math.
     * Stream 4: Creative asset proofing, storage accounting, and deliverables.
   - Use `will-change: transform; transform: translate3d(0, 0, 0);` to ensure smooth 60fps rendering without jank on mobile viewports.
3. **Deployment Asset Bundling & Health Check (`scripts/deploy_frontend.py`)**:
   - Ensure all agent portraits and artwork assets are packaged in the deployment payload.
   - Run pre-release curl check against `/api/health` before updating the production symlink.

---

## 58. Features Interactive Directory & Lenis Scroll Boundary Isolation Blueprint (`cora-frontend/app/features/page.tsx`)

When engineering high-density capability catalogs with sticky sidebars:
1. **Lenis Scroll Isolation Standard**:
   - Wrap interactive sidebar containers with `data-lenis-prevent`:
     ```tsx
     <aside 
       data-lenis-prevent 
       className="max-h-[calc(100vh-120px)] overflow-y-auto pr-3 scrollbar-thin"
     >
     ```
   - This ensures inner wheel and touch scroll events are consumed locally, preventing Lenis from intercepting and locking the parent window scroll.
2. **High-Density Sidebar Navigation (`FeaturesSidebar.tsx`)**:
   - Single-line list items with icon glyphs and active tonal pills (`bg-zinc-100 dark:bg-zinc-800`).
   - Segmented status pill filter (`All`, `Released`, `Beta`, `Upcoming`).
   - Search input with positioned vector SVG preventing text overlap.
3. **Natural Active-Voice Benefit Copywriting**:
   - Module cards must use active voice titles (e.g., "Collect GST-Compliant Retainers" instead of "Retainer Billing Module").
   - Action buttons must state direct value ("Get Started for Free").

---

## 59. Enterprise Brand Asset Registry & SVG Vector Download Kit Blueprint (`cora-frontend/app/brand/page.tsx`)

When deploying public brand registries and digital press kits:
1. **Turnkey Vector SVG Kit (`BrandLogosSection.tsx`)**:
   - Provide raw inline SVG markup accessible via 1-click clipboard copy (`navigator.clipboard.writeText(svgString)`).
   - Provide direct `.svg` blob download triggers (`URL.createObjectURL(blob)`).
   - Include Primary Wordmark, Inverted Wordmark, and Symbol Mark in both light and dark variants.
2. **Design Tokens Reference**:
   - Document the 11-step neutral zinc color scale, typographic hierarchy, co-branding guidelines, and clearspace rules.
3. **Production Capture Showcase**:
   - Showcase authentic workspace UI captures rather than ungrounded marketing renders.
   - Adhere strictly to Rule 13: zero heavy black border outlines or dark bounding strokes.

---

## 60. Agency Storytelling Canvas & Connected Chronological Journey Ribbon Blueprint (`cora-frontend/app/about/page.tsx`)

When constructing timeline narratives and founder story canvases:
1. **Connected Chronological Timeline Ribbon (`ScrollDrivenTimeline.tsx`)**:
   - Maintain a single, continuous vertical rail connecting all chronological milestone cards.
   - Unify rail segments to eliminate gaps between tick marks and cards.
   - Provide a responsive mobile timeline spine ensuring clear vertical narrative flow on screens `< 768px`.
2. **Structured Bento Grid Architecture**:
   - Core Values bento grid (`CoreValuesSection.tsx`) with high-contrast vector icons and concise value statements.
   - Dynamic operational week calendar (`DynamicWeekCalendar.tsx`) showing Monday-to-Sunday workflow cadence.

---

## 61. Robust Multi-Field Contact Form Validation & State Feedback Blueprint (`cora-frontend/app/contact/page.tsx`)

When implementing public inquiry and contact lead capture forms:
1. **Field Validation Matrix**:
   - **Full Name**: Trimmed length >= 2, validating standard personal naming conventions.
   - **Work Email**: Strict RFC 5322 regex test (`/^[^\s@]+@[^\s@]+\.[^\s@]+$/`).
   - **Phone Number**: International phone pattern validation (`/^[0-9+ -]{7,15}$/`).
   - **Company / Studio**: Mandatory non-empty string.
   - **Topic**: Mandatory selection from pre-defined inquiry topics.
2. **Accessible Monochromatic Feedback**:
   - Render field-level error messages with `aria-live="polite"` and `role="alert"`.
   - Dispatch feedback via the custom monochromatic Toast notification system (`window.coraShowToast`).
   - Zero browser default popups (`alert()`, `confirm()`, `prompt()`) in accordance with Rule 1.

---

## 62. Public Documentation Mobile Floating Island Clearance & Navigation Blueprint (`cora-frontend/components/docs/DocsClient.tsx`)

When building public documentation reading interfaces:
1. **Non-Wrapping Sidebar Tabs & Badges**:
   - Ensure category headers, subpage titles, and status badges (`Core`, `REST`, `Beta`) wrap gracefully without layout clipping.
2. **Mobile Floating Island Bottom Clearance**:
   - Ensure the article content container provides generous bottom padding (`pb-28`) so text and next/previous pagination buttons remain completely unobstructed by mobile floating island bars.
3. **Unconditional React Hook Lifecycle (`Navbar.tsx`)**:
   - Ensure all React hooks (`useState`, `useEffect`, `useCallback`) are declared unconditionally before any early returns, avoiding React error #310 hook violations.

---

## 63. Agency Projects & Portfolio Case Studies Module Blueprint (`views/view-projects.php` & `cora-frontend/app/projects/`)

When developing agency portfolio showcase modules, case study management panels, and Next.js frontend presentation layers:

### 1. Database Schema Specification (`wp_cora_projects`)
The database layer isolates case study records in a dedicated multi-tenant table initialized via `dbDelta`:
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

### 2. AJAX & REST Controllers (`Cora_Projects_Module`)
- **AJAX Actions**:
  * `cora_ajax_get_projects`: Fetches tenant projects with KPI aggregations.
  * `cora_ajax_save_project`: Atomic upsert with JSON encoding for `services`, `deliverables`, `kpis`, and `gallery_images`.
  * `cora_ajax_delete_project`: Tenant-scoped hard deletion with nonce verification.
  * `cora_ajax_toggle_project_publish`: Fast status toggle between `published` and `draft`.
  * `cora_ajax_duplicate_project`: Clones an existing case study with appended `-copy` slug.
- **REST Endpoints**:
  * `GET /wp-json/cora-api/v1/projects`: Public endpoint returning all published projects ordered by `is_featured DESC, sort_order ASC, created_at DESC`.
  * `GET /wp-json/cora-api/v1/projects/(?P<slug>[a-zA-Z0-9_-]+)`: Fetches a single case study by unique slug.

### 3. Stealth NDA Masking & 4-Act Storytelling Architecture
- **Confidentiality Masking**: When `is_confidential = 1`, client names are masked with anonymized descriptors (e.g. *"Confidential Real Estate Enterprise"*), logos are redacted, proprietary links are omitted, and demo URLs are masked, protecting client NDAs.
- **Numerical Proof in JetBrains Mono**: KPIs render values in `font-mono font-bold text-zinc-950` with contextual baseline metrics and directional arrows (`up`, `down`, `neutral`).
- **4-Act Narrative Arc**: Case study prose enforces a 4-act progression:
  * Act 1: Executive Summary (high-level transformation impact).
  * Act 2: Challenge Narrative (operational bottlenecks and baseline friction).
  * Act 3: Solution Narrative (agency architecture and custom interventions).
  * Act 4: Deliverables List (concrete list of shipped assets and automations).

### 4. 6-Tab Drawer Editor (`views/view-projects.php`)
The management interface implements a 6-tab editing drawer conforming to Rule 12 (desktop slide-over drawer `w-[640px]`, mobile bottom sheet `max-h-[88vh] rounded-t-3xl`):
* Tab 1: Overview (`proj-tab-overview`)
* Tab 2: Narrative Story (`proj-tab-narrative`)
* Tab 3: Impact KPIs (`proj-tab-kpis`)
* Tab 4: Media & Visuals (`proj-tab-media`)
* Tab 5: Endorsement (`proj-tab-testimonial`)
* Tab 6: SEO & CTA (`proj-tab-seo`)

---

## 64. Public Micro-Tools Growth Engine & Canonical Tool Registry Blueprint (`cora-frontend/lib/tools-canonical-registry.ts`)

When engineering organic acquisition micro-tools, calculation engines, and lead generation utilities:

### 1. Canonical Registry Architecture
All public tools must be declared in `cora-frontend/lib/tools-canonical-registry.ts` conforming to the 24-field `CanonicalToolRecord` schema:
```typescript
export interface CanonicalToolRecord {
  slug: string;
  publicTitle: string;
  shortTitle: string;
  category: ToolCategory;
  categoryLabel: string;
  status: ToolStatus;
  owner: string;
  icp: string[];
  jobToBeDone: string;
  targetSearchCluster: string[];
  metadataTitle: string;
  metadataDescription: string;
  canonicalUrl: string;
  inputs: ToolIOField[];
  outputs: ToolIOField[];
  features: string[];
  methodologyVersion: string;
  executionMode: ExecutionMode; // 'browser-only' | 'remote-audit' | 'ai-assisted'
  privacyDisclosure: string;
  securityRisk: SecurityRisk;
  relatedTools: string[];
  supportingArticles: string[];
  supportingDocs: string[];
  contextualCoraAction: ContextualCoraAction;
}
```
The registry defines **63 verified tools across 10 problem domains**: `agency_economics`, `wordpress_stack`, `ai_discovery`, `finance_tax`, `contracts_legal`, `developer_embed`, `pdf_tools`, `sheets_tools`, `image_tools`, and `real_estate`.

### 2. Core Framework Components (`cora-frontend/components/tools/`)
- `ToolInputGroup`: Controlled inputs with real-time validation, numeric steppers, and range sliders.
- `ToolResultSummary`: Immediate scorecard cards with high-contrast numerical highlights in JetBrains Mono.
- `ToolActionBanner`: Conversion bridge connecting the calculated pain point to Cora's workspace platform.
- `ToolFaqAccordion`: Semantic FAQ accordion embedding Schema.org `FAQPage` JSON-LD.
- `ToolBreakdownTable`: Tabular financial ledger breaking down formula variables and calculations.

### 3. Zero-PII Analytics Governance (`cora-frontend/lib/tools-analytics.ts`)
To protect user privacy and comply with enterprise data governance:
- **Strict Prohibition**: Never transmit URLs, client names, email addresses, uploaded documents, prompt text, or raw financial figures.
- **Allowed Telemetry Payload**:
  ```typescript
  export interface ToolAnalyticsPayload {
    tool_id: string;
    category?: string;
    execution_mode?: 'browser-only' | 'remote-audit' | 'ai-assisted';
    result_band?: string;
    step_completed?: number;
    duration_ms?: number;
    cta_target?: string;
    error_code?: string;
  }
  ```
- **Logging Method**: Use `logToolEvent(eventName, payload)` wrapping `trackEvent` from `@/components/analytics/Analytics`.

### 4. 6 Flagship Growth Tools
1. `wp-plugin-cost-calculator`: 3-year TCO computation of plugin bloat and maintenance overhead.
2. `lead-leakage-calculator`: Pipeline decay simulator based on speed-to-lead benchmarks.
3. `scope-creep-calculator`: Profit margin erosion model across unbilled scope iterations.
4. `service-pricing-calculator`: Margin target reverse-engineer computing hourly billable floor.
5. `wp-publishing-friction-calculator`: Editorial turnaround friction scoring and bottleneck detector.
6. `ai-crawler-checker`: User-agent robots.txt analyzer checking GPTBot, ClaudeBot, PerplexityBot, and Google-Extended.

---

## 65. Canvas Frontend Themes Engine & GitHub CI/CD Full-Lifecycle Architecture (`views/view-canvas.php` & `Cora_GitHub_Integration`)

When building visual website theme customizers, PageSpeed diagnostics, and Git deployment integrations:

### 1. Analytical 4-KPI Overview Bar
Positioned in `tab-canvas-overview` of `views/view-canvas.php`:
- **Core Web Vitals Score**: Live mobile performance score with target threshold badge (`> 89`).
- **Largest Contentful Paint (LCP)**: Millisecond paint speed (`420ms`) with live/simulated connection indicator.
- **Themes Library Count**: Live vs Draft count with draft quota limit.
- **Published Pages & SEO Readiness**: Live published pages count with real-time SEO readiness indicator (`100% SEO Ready`).

### 2. Sticky ~40px Subtab Navigation
Pinned below the global topbar at ~40px height:
- `Theme Library` (`tab-canvas-overview`)
- `Speed & Core Web Vitals` (`tab-canvas-vitals`)
- `Migration & Tools` (`tab-canvas-migration`)
- `GitHub Integration` (`tab-canvas-github`)

### 3. Full-Lifecycle GitHub Integration (`Cora_GitHub_Integration`)
Implemented in `includes/class-cora-github-integration.php`:
- **Device Authorization Grant**: RFC 8628 OAuth 2.0 Device Flow (`cora_github_initiate_device_flow`, `cora_github_poll_device_token`) with user-code authorization.
- **PAT Storage**: Encrypted token persistence in user meta and `cora_git_sync_token` option.
- **Branch Management**: Remote branch creation (`cora_github_create_branch`) and listing (`cora_github_get_branches`).
- **Automated PR Generation**: `cora_github_create_pr` stages changes and creates pull requests against the target base branch with structured markdown diff logs.
- **1-Click Merge Engine**: `cora_github_merge_pr` executes atomic merges via GitHub REST API supporting `merge`, `squash`, and `rebase` strategies.
- **Commit Rollback & Revert Engine**: `cora_github_revert_commit` generates automated git revert commits to restore previous known-good production states upon error.
- **Workspace Synchronization**: `cora_github_sync_entire_workspace` bundles active themes, template assets, and page HTML into an atomic git tree commit.
- **Organization Repository Provisioning**: `cora_github_provision_org_repo` provisions dedicated private repositories under agency GitHub organizations for white-label client projects.

---

## 66. Forms to CRM Lead Pipeline Dynamic Automation & Multi-Submission Aggregation Blueprint (`cora_rest_submit_form` & `wp_cora_leads`)

When connecting public form submissions into the sales CRM pipeline and aggregating submission records:

### 1. Forms-to-CRM Auto-Conversion Pipeline (`cora_rest_submit_form`)
Inbound submissions to `POST /wp-json/cora-api/v1/forms/{id}/submit` or `POST /wp-json/cora/v1/forms/{id}/submit` trigger automated CRM lead creation:
- **Honeypot & Rate Limiting**: Validates `cora_hp_verify` honeypot and enforces IP rate limiting (max 10 submissions/minute).
- **Intelligent Field Extraction**: Discovers contact details across submitted payload case-insensitively:
  * Full Name: Maps `name`, `full_name`, `client_name`, `prospect_name`.
  * Email: Maps `email`, `user_email`, `email_address`.
  * Phone: Maps `phone`, `mobile`, `whatsapp`, `contact`.
  * Budget: Regex extracts numbers from `budget`, `price`, `amount`, `investment`.
  * City / Location: Maps `city`, `location`, `venue`, `address`.
  * Service Scope: Maps `service`, `package`, `project_type`.
- **Multi-Tenant Isolation**: Scopes the generated record strictly by `agency_id` and `branch_id`.
- **Assignee Routing**: Supports direct team member assignment or round-robin workload balancing selecting the active team member with the lowest active lead count.
- **Predictive Scoring**: Classifies lead temperature into `hot` (budget >= ₹50,000 or complete contact info), `warm` (standard inquiry), or `cold` (minimal contact info).
- **Micro-Cache Invalidation & RAG Ingestion**: Calls `cora_db_get_leads(true)` to flush micro-caches, prepends record into `cora_workspace_leads` option for 0ms UI reactivity, and ingests lead creation events into `cora_rag_ingest_event` for real-time AI Co-Founder awareness.

### 2. Multi-Submission Aggregation REST Endpoint (`GET /wp-json/cora/v1/forms/all-submissions`)
- **Route**: `GET /wp-json/cora/v1/forms/all-submissions` (and `/forms/submissions`).
- **Permission Check**: `cora_forms_rest_permission_check`.
- **Query Parameters**: `page`, `per_page`, `form_id`, `search`, `start_date`, `end_date`.
- **Functionality**: Consolidates entries across all forms for the active tenant, supporting pagination, full-text search, and multi-form CSV streaming.

---

## 67. Editorial Knowledge Engine & High-Authority Industry Article Library Blueprint (`cora-frontend/lib/articles-data.ts`)

When engineering long-form authoritative editorial guides, Generative Engine Optimization (GEO) content, and comparison matrices:

### 1. Article Data Model (`Article`)
All editorial guides must conform to the TypeScript interface:
```typescript
export interface Article {
  slug: string;
  title: string;
  shortTitle: string;
  category: 'website-content' | 'product-guides' | 'studio-operations' | 'finance-gst' | 'ai-automation' | 'legal-contracts' | 'comparisons';
  categoryLabel: string;
  eyebrow: string;
  description: string;
  readTime: string;
  difficulty: 'Beginner' | 'Intermediate' | 'Advanced';
  publishedAt: string;
  updatedAt: string;
  author: {
    name: string;
    role: string;
    avatar: string;
  };
  badge?: string;
  featured?: boolean;
  keyTakeaways: string[];
  toc: { id: string; title: string }[];
  interactiveWidget?: 'gst-calculator' | 'deal-simulator' | 'comparison-matrix' | 'none';
  visualGuide?: 'publishing-postmortem';
  content: string;
  faqs: { question: string; answer: string }[];
  relatedSlugs: string[];
}
```

### 2. Generative Engine Optimization (GEO) Standards
- **Direct Definitional Answers**: Lead each guide with an executive summary and definitional answer in the first 100 words to facilitate AI snippet citation.
- **Structured Takeaways**: Provide 3–5 bulleted `keyTakeaways` highlighting quantitative benchmarks and actionable conclusions.
- **Schema.org Integration**: Generate `Article` and `FAQPage` JSON-LD schemas dynamically on every article route.
- **Rule 3 Privacy Compliance**: All author credentials must use generic personas (`Aarav Mehta`, `Kavya Patel`, `Rohan Verma`, `Studio Director Team`, `Product Strategy Desk`).

### 3. Interactive Widget Embeddings
Articles bind specialized interactive tools based on `interactiveWidget`:
- `'gst-calculator'`: Renders live 18% GST and SAC 9983 tax computation.
- `'deal-simulator'`: Renders agency retainer margin simulator and pricing modeler.
- `'comparison-matrix'`: Renders head-to-head feature comparisons against legacy platforms (HoneyBook, Studio Ninja, HubSpot, Dubsado, Pixieset).

---

*Cora Developer Feature Guide v4.9.381 — Last updated: October 2026.*
