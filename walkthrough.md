# Walkthrough — Platform Analysis & Comprehensive Documentation Synchronization (v4.9.364 + Marketing Site Reform)

This document provides a comprehensive walkthrough of the platform-wide architectural analysis and documentation synchronization across the Cora Workspace and Marketing platforms, reflecting all developments up to git `HEAD: 7eb2bc42c` (`feat/marketing-site-reform-2026-10-06`).

---

## 1. Executive Summary & Audit Trajectory

Across recent development cycles (from **v4.9.357** through **v4.9.364** and the subsequent **Marketing Site Reform**), the Cora Platform underwent major architectural advancements spanning frontend positioning, AI sales automation, multi-format template delivery, AI quota engineering, headless website compilation, affiliate attribution, and developer tooling:

1. **Marketing Site Reform & Value Proposition Alignment (Oct 2026)**:
   - **Canonical Headline**: *"Build websites. Run your business, with an AI co-founder."*
   - **Subtitle**: *"The operating system for Indian service businesses and agencies. Invoicing, lead CRM, team dispatch, client portal, and custom websites — powered by your personal AI co-founder."*
   - **Dual High-Velocity CTAs**:
     * Primary: "Sign in with Google" — Instant OAuth onboarding provisioning production workspaces in seconds.
     * Secondary: "Explore free templates" — Directly connects prospective founders to the 16+ turnkey pre-seeded schemas and assets at `/templates` and `/use-cases`.
   - **Tools & Templates Navigation Hub**:
     * Overhauled main navigation: Card 1 elevated to "Free Tools", bottom banner to "Template & Theme Library", and Resources card to "Tools & Templates".
     * Connects free standalone agency calculators (18% GST calculator, retainer margin tool, proposal generator) alongside production website and workspace templates with direct lead capture into `wp_cora_leads`.

2. **Sticky Floating AI SDR Bar & Objection Handling Engine (Oct 2026)**:
   - **True Viewport Docking via Portal**: Mounted directly via `createPortal(..., document.body)` in `HeroAIInput.tsx`. Anchored bottom-center (`bottom-0 sm:bottom-4`) with responsive width, elevation shadows, and zero backdrop overlay to allow uninterrupted page browsing.
   - **Active-Voice Conversational Engine**: High-velocity query parser in `/api/ai-preview/route.ts` delivering concise, query-aligned active voice answers.
   - **Agency Routing Pills**: 1-tap inquiries for Web Design & Dev, Creative & Branding, Performance & SEO, App & Software Dev, and Media & Production.
   - **Automated Objection Vector Matcher**: Pre-configured vectors addressing Notion/HoneyBook displacement, free white-glove site migration, annual discounts, WordPress multi-plugin stability, and monthly AI quotas.
   - **Dynamic Conversion Coupon Attribution**: Automatically attaches actionable promotional offer cards (`MIGRATEFREE`, `CORA10`) that auto-populate coupon parameters upon redirect to `/pricing`.

3. **Multi-Format Frontend Asset & Template Distribution Ecosystem (Oct 2026)**:
   - **16+ Production Assets & Turnkey Schemas**: Complete asset library at `/templates` and `/templates/[slug]` spanning 16+ agency verticals.
   - **4 Canonical Distribution Formats**:
     * `native_page`: Pure Tailwind CSS and Next.js 15 pages featuring sub-50ms paint times and accessible components.
     * `section_prompts`: Structured prompts engineered for Claude 3.5 Sonnet and Cursor Composer with design token variables.
     * `elementor_kit`: Standardized Elementor JSON blueprints for instant page builder import.
     * `lovable_project`: Full-stack scaffolding configurations for Lovable.dev and v0.dev rapid app prototyping.
   - **Interactive Modal Preview**: Sandboxed iframe preview with responsive viewport switchers (`Desktop`, `Tablet`, `Mobile`), 1-click clipboard prompt copy, and direct blueprint asset downloads.
   - **Universal Lead Ingestion**: Static landing pages include embedded lead capture forms that pipe visitor inquiries directly into `wp_cora_leads` with instant monochromatic toast feedback.

4. **Autonomous AI Workforce & Structured Domain Personas (Oct 2026)**:
   - Integrated autonomous AI workforce spanning 9 specialized domain agents: Operations, Sales, Contracts, Knowledge, Creative, Delivery, Finance, Legal, and Marketing.
   - Monochrome-first high-resolution portrait assets (`cora_agent_*.jpg`), active-voice capability descriptions, and strict system prompt isolation.

5. **Homepage Copy & Executive Confidence Grid Overhaul (Oct 2026)**:
   - **Executive Confidence Grid (`ExecutiveConfidenceSection.tsx`)**: Centered active-voice heading (*"Stay on top of your agency."* / *"See what’s due, who’s doing it, and what’s still unpaid."*) and 6 active-voice feature cards (*"Check your business at a glance"*, *"See who’s doing what"*, *"Start with ready-made templates"*, *"Find what needs attention"*, *"Keep clients in the loop"*, *"Know what’s paid and pending"*).
   - **Industry Capability Grid (`IndustryGridSection.tsx`)**: Cleaned single-line titles, max 2-line active-voice descriptions, and renamed "Voice to Scope" to "Voice to Text".
   - **Unified Hub Sprawl (`UnifiedHubSprawlSection.tsx`)**: Centered 2-word badge, modernized clean vector icons, and 3 business workflow columns.

6. **AI Quotas & Fair Usage Standardization (v4.9.364)**:
   - Standardized monthly quotas: 500 (Starter), 15,000 (Pro), 25,000 (Scale), and Unlimited (Enterprise).
   - Anti-burnout weekly soft-limit pacing (`ceil($monthly_quota / 4)`: 3,750 Pro / 6,250 Scale runs/week).
   - Super Admin & Admin Role rate-limit bypass and session timeout elimination.

7. **Endorsely Affiliate & Conversion Tracking Suite (v4.9.364)**:
   - Platform-wide client script integration (`app.endorsely.com/assets/affiliate-tracking.js`, Affiliate ID: `d56e72c8-89fa-47cb-bc87-920aa9e3a6bb`).
   - Dynamic conversion attribution in checkout modals upon verified payment capture.

8. **Canvas Live HTML Preview Resilience Shield & Auto-Tailwind (v4.9.358 - v4.9.363)**:
   - Auto-Tailwind CDN injection, error boundaries, zero blank sections guarantee, postMessage sync, and live static site styling parity.

9. **Canvas AI Editor & Workspace Forms Embedding Suite (v4.9.360 - v4.9.362)**:
   - Unified "Edit with AI" toolbar in Live Code Editor, streamlined minimalist header, and shortcode (`[cora-form id="..."]`) / responsive iframe generator with direct CRM lead routing.

10. **Universal Remote MCP Server & OpenAPI 3.0.1 Specification (v4.9.349 - v4.9.357)**:
    - Curated 28 Core Actions ceiling for ChatGPT Custom GPT Actions, interactive OAuth 2.1 consent panel, and zero-copy native MCP static publishing.

---

## 2. Updated Documentation Artifacts

The entire platform documentation suite across 14 files was systematically audited and synchronized:

| # | Documentation File | Target Version / Phase | Key Updates Applied |
| :-: | :--- | :--- | :--- |
| 1 | [`README.md`](file:///Users/shrutian/Desktop/cora/README.md) | `v4.9.364` + Marketing Reform | Added Sticky AI SDR Bar and Template & Theme Asset Ecosystem (16+ Schemas) to Core Modules; added Principles 81–85 covering Marketing Reform, Tools & Templates, Sticky AI SDR Viewport Portaling, 4-Tier Asset Architecture, and Autonomous AI Workforce. |
| 2 | [`MODULES_STATUS.md`](file:///Users/shrutian/Desktop/cora/MODULES_STATUS.md) | `v4.9.364` + Marketing Reform | Added active module rows for Marketing Site Reform, Sticky AI SDR Bar & Objection Engine, and Multi-Format Template & Asset Library; updated Version History table with detailed release summary. |
| 3 | [`docs/cora-platform-documentation.md`](file:///Users/shrutian/Desktop/cora/docs/cora-platform-documentation.md) | `v4.9.364` + Marketing Reform | Added Section 1.24 (Marketing Site Reform & Tools & Templates Discovery Engine), Section 1.25 (Sticky AI SDR Sales Engine & Objection Handling), Section 1.26 (Multi-Format Frontend Asset & Template Distribution Ecosystem), Section 1.27 (Autonomous AI Workforce & Modular Agent Portfolios); updated Section 16 release manifest. |
| 4 | [`docs/DEVELOPER_FEATURE_GUIDE.md`](file:///Users/shrutian/Desktop/cora/docs/DEVELOPER_FEATURE_GUIDE.md) | `v4.9.364` + Marketing Reform | Added Architectural Principles 81–85; added Section 46 (Marketing Site Reform & Discovery Layer), Section 47 (Conversational AI SDR & Conversion Coupon Engine), Section 48 (Multi-Format Template & Theme Distribution Engine), and Section 49 (Autonomous AI Workforce & Modular Agent Portfolios). |
| 5 | [`docs/canvas-frontend-module.md`](file:///Users/shrutian/Desktop/cora/docs/canvas-frontend-module.md) | `v4.9.364` + Marketing Reform | Added Section 25 (Tools & Templates Ecosystem, 16+ Pre-Seeded Schemas & Marketing Bridge) and Section 26 (Multi-Format Asset Transformation & Distribution across 4 tiers). |
| 6 | [`docs/mcp/compatibility.md`](file:///Users/shrutian/Desktop/cora/docs/mcp/compatibility.md) | `v4.9.364` | Verified curated 28-tool OpenAPI 3.0.1 compatibility matrix, interactive OAuth 2.1 consent panel, and updated version footer. |
| 7 | [`CORA_PLATFORM_ONBOARDING_ONE_PAGER.md`](file:///Users/shrutian/Desktop/cora/CORA_PLATFORM_ONBOARDING_ONE_PAGER.md) | `v4.9.364` + Marketing Reform | Added items 31 (Marketing Site Reform & Dual-Track Onboarding), 32 (Sticky Floating AI SDR Bar & Objection Handling), 33 (Multi-Format Frontend Asset & Template Ecosystem), and 34 (Autonomous AI Workforce & Structured Domain Personas). |
| 8 | [`cora-frontend/lib/docs-data.ts`](file:///Users/shrutian/Desktop/cora/cora-frontend/lib/docs-data.ts) | `v4.9.364` + Marketing Reform | Synchronized public documentation articles: `platform-architecture` (Sections 6–8: Marketing Reform, Sticky AI SDR Assistant, 4-Tier Template Ecosystem), `canvas-builder` (Section 10: Tools & Templates Ecosystem, 4-tier formats & modal preview), and `ai-cofounder` (Section 5: Public Conversational AI SDR & Conversion Engine). |
| 9 | [`cora-frontend/CONTENT_PUBLISHING_SYSTEM.md`](file:///Users/shrutian/Desktop/cora/cora-frontend/CONTENT_PUBLISHING_SYSTEM.md) | `v4.9.364` + Marketing Reform | Added `/templates/` frontend template & asset library to canonical architecture tree. |
| 10 | [`app/public/wp-content/plugins/cora-workspace/views/view-documentation.php`](file:///Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-workspace/views/view-documentation.php) | Dynamic `CORA_WORKSPACE_VERSION` | Replaced hardcoded version badge with dynamic `CORA_WORKSPACE_VERSION` reading `v4.9.364`. |
| 11 | [`app/public/wp-content/plugins/cora-workspace/includes/docs-engine.php`](file:///Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-workspace/includes/docs-engine.php) | `v4.9.364` + Marketing Reform | Added 8 in-dashboard documentation pages in `cora_docs_get_new_pages_v49364()` (Marketing Site Reform, Canvas Resilience Shield, AI Quotas Pacing, MCP OpenAPI 3.0.1, Endorsely Affiliate Suite, Forms Embedding Suite, Cora AI SDR, Templates Asset Library) and corresponding changelog entries. |
| 12 | [`cora-chatgpt-plugin/README.md`](file:///Users/shrutian/Desktop/cora/cora-chatgpt-plugin/README.md) | `v4.9.364` | Verified curated 28 core actions ceiling, OpenAPI 3.0.1 specification compliance, and connection instructions. |
| 13 | [`task.md`](file:///Users/shrutian/Desktop/cora/task.md) | `v4.9.364` + Marketing Reform | Synchronized live task progress tracker with complete subtask status across all phases. |
| 14 | [`walkthrough.md`](file:///Users/shrutian/Desktop/cora/walkthrough.md) | `v4.9.364` + Marketing Reform | Comprehensive walkthrough documentation created. |

---

## 3. Compliance & Governance Verification

- **Rule 3 (Strict Name & Identity Privacy)**: 100% verified across all modified documentation and codebase files. Zero occurrences of the owner's real name; all examples, mockups, and placeholders strictly use generic fictitious placeholders (`Rohan Verma`, `Kavya Patel`, `Aarav Mehta`, `Studio Admin`, `Workspace Owner`).
- **Rules 4 & 13 (Monochromatic Design Tokens & Tonal Surfaces)**: Verified that all newly documented UI patterns adhere to the Notion/Shopify monochromatic palette (`zinc-50` through `zinc-950`), soft tonal background fills (`bg-zinc-100/90`), subtle uniform structural borders (`border-zinc-200`), and zero harsh dark outlines (`border-zinc-900`, `ring-2`).
- **Rule 8 (Module Isolation & Scoping)**: Verified that all CSS selectors, JavaScript event listeners, and API endpoints are strictly scoped to their respective modules, preventing regressions across neighboring views.
- **PHP Syntax & TypeScript Integrity**: Verified `docs-engine.php` and `view-documentation.php` with PHP 8.2 CLI (`No syntax errors detected`), and validated `cora-frontend` with TypeScript compiler (`npx tsc --noEmit` passed with 0 errors).
