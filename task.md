# Platform Task Tracker: Complete Content Suite Audit & Full MCP/API Workspace Compatibility

## Task Overview
Execute a comprehensive audit and implementation of the AI Content Suite across Cora Platform (`http://cora.local/workspace/blogs?ct=ct-library`), dispatching multiple specialized subagents in parallel to inspect and bridge:
1. Direct User Operations in Workspace UI (`views/view-content-suite.php`)
2. Model Context Protocol (MCP) Tools compatibility (`includes/mcp/class-cora-mcp-tool-registry.php`, `class-cora-universal-mcp-server.php`)
3. Open Workspace REST API (`/wp-json/cora/v1/content/*`)
Ensure complete parity so that whatever a user wants to do with their content, they can do:
- Directly inside the Workspace UI
- By connecting external preferred/referred AI agents (Claude Desktop, Cursor, ChatGPT, etc.) via MCP
- Programmatically via the fully opened Workspace-Level REST API.

---

## Real-Time Subtask Tracker

### Phase 1: Parallel Subagent Audit & Discovery
- [x] **Subagent 1: Content Suite UI & Data Layer Auditor** (Completed by `Content UI Auditor`)
  - [x] Audited Content Library UI table, filters, bulk actions, search, and DOM pagination.
  - [x] Audited Article Drawers (Create / Edit), AI Content Generators, and metadata mapping (`_cora_seo_score`, `_cora_focus_keyword`, `_cora_editorial_status`, `_cora_geo_score`).
  - [x] Audited Opportunities tab, SEO & AI Visibility tab, and unlocked Calendar, Performance, Automations, Brain tabs.
  - [x] Identified missing AJAX handlers (`cora_update_article_date`, 4 GEO handlers, and brief-to-post creation).
- [x] **Subagent 2: MCP Tool Registry & Execution Compatibility Auditor** (Completed by `MCP Tools Auditor`)
  - [x] Analyzed `Cora_MCP_Tool_Registry`, `Cora_Universal_MCP_Server`, and `cora_execute_ai_action`.
  - [x] Identified critical gap: MCP was routing all content tools to `wp_cora_content_entries` (`growth_cora_main_01`), completely disconnected from the 45 library articles in `wp_posts`.
  - [x] Formulated 9-tool canonical MCP Content Suite blueprint with dual `source: 'workspace' | 'growth'` support.
- [x] **Subagent 3: Workspace REST API & Auth Openness Auditor** (Completed by `Workspace API Auditor`)
  - [x] Analyzed REST namespaces (`/cora/v1`, `/cora-growth/v1`) and auth tables (`wp_cora_workspace_api_keys`).
  - [x] Identified gap: No `/wp-json/cora/v1/content` REST controller existed for workspace articles, and API keys (`cora_ws_live_...`) lacked universal REST middleware.
  - [x] Formulated blueprint for universal REST auth filter, full Content REST controller, and Developer Gateway UI drawer.

### Phase 2: Architectural Delivery & Integration
- [/] **Step 1: Universal REST & MCP Workspace Authentication Bridge**:
  - [ ] Add `cora_rest_api_key_determine_user` filter in `class-cora-plugins-skills-engine.php` recognizing `X-Cora-API-Key` and `Authorization: Bearer cora_ws_live_...`.
  - [ ] Update `Cora_OAuth_Server::authenticate_token` to accept `cora_ws_live_...` API keys directly for unified REST and MCP authentication.
- [ ] **Step 2: Workspace Content REST Controller (`/wp-json/cora/v1/content`)**:
  - [ ] Create `class-cora-workspace-content-rest-controller.php` in `includes/`.
  - [ ] Implement `GET /content` (list with filters, search, pagination), `GET /content/{id}`, `POST /content` (create), `PATCH /content/{id}` (update), `DELETE /content/{id}` (delete/trash).
  - [ ] Implement `POST /content/{id}/publish`, `POST /content/{id}/unpublish`, `POST /content/batch` (bulk actions).
  - [ ] Implement `POST /content/{id}/seo-audit` (11-point algorithmic audit) and `GET /content/opportunities`.
- [ ] **Step 3: Unified MCP Content Tools Suite**:
  - [ ] In `class-cora-mcp-tool-registry.php`, implement the 9 canonical tools: `cora_list_articles`, `cora_get_article`, `cora_create_article`, `cora_update_article`, `cora_delete_article`, `cora_publish_article`, `cora_unpublish_article`, `cora_bulk_update_articles`, `cora_audit_article_seo`, `cora_list_content_opportunities`, `cora_generate_article_outline`.
  - [ ] Support dual source: workspace `wp_posts` and headless growth `wp_cora_content_entries`.
  - [ ] Update curated 28-tool priority list in `Cora_Universal_MCP_Server::handle_list_tools()`.
- [ ] **Step 4: Content Suite UI Enhancements & Missing Handlers**:
  - [ ] Implement missing AJAX handlers in `cora-workspace.php`: `cora_ajax_update_article_date`, 4 GEO handlers, and link `cora_create_brief_from_opportunity` to `wp_posts`.
  - [ ] Unlock Calendar, Performance, Automations, and Business Brain tabs in `views/view-content-suite.php`.
  - [ ] Add "Developer API & MCP Gateway" button in Content Suite header.
  - [ ] Embed Shopify-inspired docked Developer Gateway drawer (`#cora-developer-gateway-drawer`) per Rule 15 with API key generation, REST endpoint reference, and MCP client config snippets.

### Phase 3: Verification & Regression Testing
- [ ] Run automated PHP test suite (`scripts/test_content_mcp_api_full.php`) validating REST CRUD, MCP tools, and AJAX actions.
- [ ] Strict Rule 3 Privacy verification (zero owner name).
- [ ] Monochrome Design System & Shopify Docked Panel standard compliance.
