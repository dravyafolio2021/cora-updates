# Walkthrough — Platform Analysis & Comprehensive Documentation Synchronization (v4.9.357 → v4.9.364)

This document provides a complete walkthrough of the platform-wide architectural analysis and documentation synchronization across the Cora Workspace and Marketing platforms, reflecting all developments up to version `v4.9.364` (latest git `HEAD: 914bb118e`).

---

## 1. Executive Summary & Audit Trajectory

Between **v4.9.357** and **v4.9.364**, 7 major releases across 8 major commits (~14,700 lines of code) were developed and deployed into the Cora platform. The complete technical documentation suite across internal developer guides, architecture manuals, module rosters, marketing frontend documentation, and external AI connector packages has been synchronized to reflect these developments:

1. **AI Quotas & Fair Usage Standardization (v4.9.364)**:
   - Standardized monthly AI run quotas:
     * **Free / Starter**: 500 AI runs/month (weekly pacing: 125 runs/week)
     * **Pro Tier**: 15,000 AI runs/month (weekly soft limit: 3,750 runs/week)
     * **Scale Tier**: 25,000 AI runs/month (weekly soft limit: 6,250 runs/week)
     * **Enterprise Tier**: Custom unlimited AI runs with dedicated tenant compute clusters
   - **Weekly Anti-Burnout Soft Throttling**: Checks `ceil($monthly_quota / 4)` to warn agency owners before burning monthly token pools early in billing cycles.
   - **Super Admin Unrestricted Access**: Super Admins and users with `manage_options` bypass all rate limits, AI run caps, and artificial 6-hour session timeouts.

2. **Affiliate & Referral Tracking Suite (Endorsely Integration) (v4.9.364)**:
   - Platform-wide integration of Endorsely affiliate tracking script (`https://app.endorsely.com/assets/affiliate-tracking.js`, Affiliate ID: `d56e72c8-89fa-47cb-bc87-920aa9e3a6bb`) in `cora-frontend/app/layout.tsx`.
   - Dynamic conversion attribution bridge in `FastRazorpayModal.tsx` and `CheckoutSheet.tsx` firing `window.endorsely?.trackConversion()` upon successful payment verification.
   - Seamless INR (`₹`) and USD (`$`) checkout sheets with automatic 18% GST calculation (SAC 9983) and instant workspace provisioning.

3. **Live HTML Preview Resilience Shield & Auto-Tailwind Injection (v4.9.358 - v4.9.363)**:
   - **Automatic Tailwind CSS CDN Injection**: Injects `<script src="https://cdn.tailwindcss.com"></script>` into preview iframes and exported static bundles if missing, guaranteeing 100% fidelity without Node.js build overhead.
   - **Iframe Error Boundaries**: Catches runtime script exceptions gracefully, eliminating blank screens or layout freezes across all 6 niche templates.
   - **Live Static Site Styling Parity**: Encapsulates host dashboard CSS to guarantee pixel-for-pixel rendering parity between local preview iframes and exported production static distributions.

4. **Canvas AI Editor & Workspace Forms Embedding Suite (v4.9.360 - v4.9.362)**:
   - **Unified "Edit with AI" Toolbar**: Consolidated fragmented AI editing triggers in the Live Code Editor into a unified, high-visibility action bar with context-aware prompt parsing and instant diff previewing.
   - **Streamlined Minimalist Toolbar**: Eliminated noisy viewport switchers from the Live Code Editor header bar.
   - **Workspace Forms Embedding Suite**: Shortcode (`[cora-form id="..."]`) and responsive iframe generator to embed Form AI Architect forms into any static landing page with automatic CRM lead capture into `wp_cora_leads`.

5. **Universal Remote MCP Server & OpenAPI 3.0.1 Specification (v4.9.349 - v4.9.357)**:
   - Curated 28 Core Actions ceiling for ChatGPT Custom GPT Actions and OpenAI Developer Plugin compliance.
   - Interactive OAuth 2.1 Consent Panel with master toggle, category-level permissions, and quick presets (`all`, `readonly`, `website`).
   - Zero-copy native MCP file management and page publishing (`cora_canvas_update_page_html`, `cora_canvas_get_page_html`, `cora_canvas_create_theme`).

6. **Universal PWA & Dynamic Super Admin Badging (v4.9.341 - v4.9.342)**:
   - Universal in-app PWA install trigger in System Settings Suite and sidebar admin popover.
   - Dynamic `ADMIN` badge rendering across 512, 192, Apple Touch, and favicon icons for Super Admin sessions.

---

## 2. Updated Documentation Artifacts

The following 10 documentation files were systematically updated to `v4.9.364`:

| # | Documentation File | Target Version | Key Updates Applied |
| :-: | :--- | :--- | :--- |
| 1 | [`README.md`](file:///Users/shrutian/Desktop/cora/README.md) | `v4.9.364` | Bumped version; updated architecture tree; added Principles 75–80 covering AI Quotas, Endorsely tracking, Live Preview Resilience Shield, Unified AI edit toolbar, Workspace forms embedding, and Static site parity. |
| 2 | [`MODULES_STATUS.md`](file:///Users/shrutian/Desktop/cora/MODULES_STATUS.md) | `v4.9.364` | Updated single source of truth manifest to `v4.9.364`; added active module rows for AI Run Quotas, Endorsely tracking, Live Static Site & Tailwind parity, Unified AI edit, Forms embedding suite, and Live Preview Resilience Shield; updated release manifest history through v4.9.364. |
| 3 | [`docs/cora-platform-documentation.md`](file:///Users/shrutian/Desktop/cora/docs/cora-platform-documentation.md) | `v4.9.364` | Added Sections 1.22 (Dynamic AI Quota Pacing & Super Admin Bypass), 1.23 (Endorsely Affiliate Suite), 3.17 (Live Preview Resilience Shield), 3.18 (Streamlined Code Editor Header), 3.19 (Forms Embedding Suite), 3.20 (Unified Edit with AI Toolbar), 3.21 (Static Site Styling Parity); updated Section 16 release manifest to v4.9.364. |
| 4 | [`docs/DEVELOPER_FEATURE_GUIDE.md`](file:///Users/shrutian/Desktop/cora/docs/DEVELOPER_FEATURE_GUIDE.md) | `v4.9.364` | Added Architectural Principles 75–80; added Sections 43 (AI Run Quota Pacing), 44 (Endorsely Affiliate Tracking Suite), and 45 (Live Preview Resilience Shield & Forms Embedding). |
| 5 | [`docs/canvas-frontend-module.md`](file:///Users/shrutian/Desktop/cora/docs/canvas-frontend-module.md) | `v4.9.364` | Added Sections 20 (Live Preview Resilience Shield & Auto-Tailwind), 21 (Streamlined Code Editor Header), 22 (Workspace Forms Embedding Suite), 23 (Unified Edit with AI Toolbar), and 24 (Live Static Site Styling Parity). |
| 6 | [`docs/mcp/compatibility.md`](file:///Users/shrutian/Desktop/cora/docs/mcp/compatibility.md) | `v4.9.364` | Verified curated 28-tool OpenAPI 3.0.1 compatibility matrix, interactive OAuth 2.1 consent panel, and updated version footer. |
| 7 | [`CORA_PLATFORM_ONBOARDING_ONE_PAGER.md`](file:///Users/shrutian/Desktop/cora/CORA_PLATFORM_ONBOARDING_ONE_PAGER.md) | `v4.9.364` | Updated Executive Summary with items 27 (AI Run Quota Pacing), 28 (Endorsely Tracking), 29 (Canvas Live Preview Resilience), and 30 (Unified AI Editor & Forms Embedding). |
| 8 | [`cora-frontend/lib/docs-data.ts`](file:///Users/shrutian/Desktop/cora/cora-frontend/lib/docs-data.ts) | `v4.9.364` | Synchronized Next.js public documentation articles: `canvas-builder` (Resilience Shield, Unified AI Edit, Forms Embedding Suite), `ai-cofounder` (15K Pro / 25K Scale AI Quotas, Weekly soft limits, Super Admin bypass), and `canvas-static-compiler` badge to v4.9.364. |
| 9 | [`cora-chatgpt-plugin/README.md`](file:///Users/shrutian/Desktop/cora/cora-chatgpt-plugin/README.md) | `v4.9.364` | Updated to version v4.9.364, verified curated 28 core actions ceiling, and connection instructions. |
| 10 | [`task.md`](file:///Users/shrutian/Desktop/cora/task.md) | `v4.9.364` | Synchronized live task progress tracker with complete subtask status. |

---

## 3. Compliance & Governance Verification

- **Rule 3 (Strict Name & Identity Privacy)**: 100% verified across all modified documentation files. Zero occurrences of the owner's real name; all examples, mockups, and placeholders strictly use generic fictitious placeholders (`Rohan Verma`, `Kavya Patel`, `Aarav Mehta`, `Studio Admin`, `Workspace Owner`).
- **Rules 4 & 13 (Monochromatic Design Tokens & Tonal Surfaces)**: Verified that all newly documented UI patterns adhere to the Notion/Shopify monochromatic palette (`zinc-50` through `zinc-950`), soft tonal background fills (`bg-zinc-100/90`), subtle uniform structural borders (`border-zinc-200`), and zero harsh dark outlines (`border-zinc-900`, `ring-2`).
- **Rule 8 (Module Isolation & Scoping)**: Verified that all CSS selectors, JavaScript event listeners, and API endpoints are strictly scoped to their respective modules, preventing regressions across neighboring views.
