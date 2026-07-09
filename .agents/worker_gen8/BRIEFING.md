# BRIEFING — 2026-07-08T09:58:50Z

## Mission
Implement advanced real estate features for the Cora Real Estate Platform plugin, refactoring equipment to listings, adding lead frontend/API, 3rd party sync, and AI SEO optimization.

## 🔒 My Identity
- Archetype: worker
- Roles: implementer, qa, specialist
- Working directory: /Users/shrutian/Desktop/cora/.agents/worker_gen8
- Original parent: 1103075c-16d4-42d6-98e1-e602e972325a
- Milestone: Advanced Real Estate Features (R1-R5)

## 🔒 Key Constraints
- Pure monochrome-first design (Notion/Shopify minimalist).
- No browser-native alerts, confirms, or prompts.
- All feedback through window.coraShowToast.
- Drawers sliding in from the right.
- No horizontal layout overflow on 375px/430px.
- Fully functional backend logic and AJAX/REST actions.
- Real PHP tests in tests/run_ajax_tests.php and run using Local PHP.

## Current Parent
- Conversation ID: 1103075c-16d4-42d6-98e1-e602e972325a
- Updated: yes

## Task Summary
- **What to build**: 
  - Shortcode `[cora_lead_form]` and REST endpoint POST `cora/v1/leads`.
  - 3rd-party listing sync handler and frontend button.
  - Server-side automatic AI SEO generation (SEO title, description, keywords) for property listings and UI editing in the details drawer.
  - Rename/refactor "Equipment" references to "Property Listings" / "Listings".
- **Success criteria**: All PHP tests pass, UI is responsive and layout matches constraints.
- **Interface contracts**: PROJECT.md

## Change Tracker
- **Files modified**:
  - `app/public/wp-content/plugins/cora-real-estate/cora-real-estate.php` — Seed logic, lead REST API, shortcode, sync AJAX, save listings update.
  - `app/public/wp-content/plugins/cora-real-estate/admin-dashboard.php` — Sidebar button, breadcrumb page map, listings details triggers, details drawer layout.
  - `app/public/wp-content/plugins/cora-real-estate/assets/js/admin-script.js` — Client drawer functions, 3rd party sync, details saving, image preview, property refactor typos.
  - `tests/run_ajax_tests.php` — require path loading fix, GDPR anonymous check condition fix, additional test cases for R1, R2, R3.
- **Build status**: 16 tests passing, syntax verified.
- **Pending issues**: none.

## Quality Status
- **Build/test result**: PASS (16/16 tests passing)
- **Lint status**: 0 outstanding violations count
- **Tests added/modified**: 3 new test cases added (listing sync, saving listings with SEO fields, and REST API lead webhook)

## Loaded Skills
- **Source**: antigravity-guide
  - **Local copy**: `/Users/shrutian/Desktop/cora/.agents/worker_gen8/skills/antigravity_guide/SKILL.md`
  - **Core methodology**: Google Antigravity framework tools.
- **Source**: paper_editing_style
  - **Local copy**: `/Users/shrutian/Desktop/cora/.agents/worker_gen8/skills/paper_editing_style/SKILL.md`
  - **Core methodology**: Visual production styles.

## Key Decisions Made
- Use Local PHP binary to execute tests since global php is not in PATH.
- Refactored Add Listing from a static tab layout into a right-sliding drawer.
- Combined Add Listing and Listing Details drawers into one dynamic Listing drawer to reduce DOM overhead and code repetition.

## Artifact Index
- `/Users/shrutian/Desktop/cora/.agents/worker_gen8/handoff.md` — Final handoff report
