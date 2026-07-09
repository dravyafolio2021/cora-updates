# BRIEFING — 2026-07-08T10:52:09Z

## Mission
Analyze Visual Canvas integration requirements for Cora Page Builder.

## 🔒 My Identity
- Archetype: explorer
- Roles: Teamwork explorer, investigator, reporter
- Working directory: /Users/shrutian/Desktop/cora/.agents/explorer_vb_1
- Original parent: 9207c6c1-3c81-434f-a792-7ff064740574
- Milestone: Visual Canvas Page Builder Integration Analysis

## 🔒 Key Constraints
- Read-only investigation — do NOT implement
- Network Restrictions: CODE_ONLY mode (no external HTTP/HTTPS requests)
- Adhere strictly to Cora style rules (No browser defaults, custom toasts, monochromatic theme, form drawers, thin SVGs, user global rules).

## Current Parent
- Conversation ID: 9207c6c1-3c81-434f-a792-7ff064740574
- Updated: 2026-07-08T10:52:09Z

## Investigation State
- **Explored paths**:
  - `app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php` (sidebar rendering, subpage rendering blocks)
  - `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php` (subpage routing, role permissions, template redirect, AI Chat proxy, page saving AJAX handlers)
  - `app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js` (frontend navigation, role permissions)
  - `app/public/wp-content/plugins/cora-real-estate/views/view-pages.php` (reference for Notion/Shopify UI structure and drawers)
- **Key findings**:
  - Sidebar rendering located at `admin-dashboard.php` lines 2318-2575.
  - Subpage capability checks found in `cora-real-estate.php` at `cora_real_estate_ai_handle_workspace_route()` (lines 151-171), `cora_real_estate_ai_seed_data()` (lines 722-771), `cora_ajax_save_role_permissions()` (lines 1117-1130), and JS at `admin-script.js` (line 99).
  - High-priority hook `template_redirect` at priority 5 can bypass WP theme.
  - AI API keys and model routing structure (BYOK) exists and can be leveraged for Prompt-to-Layout generation.
- **Unexplored areas**: None, the core integration paths are fully located.

## Key Decisions Made
- Visual Canvas subpage will be registered under the target slug `'visual-builder'`.
- The GrapesJS UI will be integrated cleanly inside a Tailwind monochromatic card in `views/view-visual-builder.php`.
- The AI AJAX handler will be registered as `wp_ajax_cora_prompt_to_layout` using the existing proxy keys.

## Artifact Index
- `/Users/shrutian/Desktop/cora/.agents/explorer_vb_1/handoff.md` — Final report to the parent agent.
