# Cora Workspace — Official ChatGPT Plugin & CLA Autonomous Operator (v4.9.419)

This package contains the official distribution files for integrating **Cora Creative & Field Studio OS (v4.9.419)** with **ChatGPT** and the **CLA Autonomous Workspace Operator** via OpenAI Custom GPT Actions, ChatGPT Plugins, and the Model Context Protocol (MCP).

---

## 📦 Package Contents

- **`manifest.json` / `ai-plugin.json`**: Official OpenAI plugin manifest supporting both `'cla'` and `'cora'` identity namespaces with dynamic RFC 8414 userinfo verification.
- **`mcp.json`**: Remote MCP server configuration schema supporting MCP Canonical 2026-07-28 and JSON-RPC 2.0 endpoints.
- **`openapi.json`**: Hardened OpenAPI 3.0.1 specification strictly enforcing the **OpenAI Custom GPT 30-action ceiling** by serving a curated catalog of 28 core studio tools (`?limit=28`). The full 246-tool unconstrained catalog remains accessible via `?all=1` for external MCP agents.
- **`skills/`**: Standard system prompts and workflow definitions for CRM, Invoicing, Bookings, Operations Copilot, Telemetry Decision Engine, Outreach Campaigns, and Autonomous Operations.
- **`assets/icon.png`**: High-resolution 512x512 official monochromatic app icon.

---

## 🚀 How to Connect Cora with ChatGPT & CLA

### Option 1: 1-Click Autonomous Workspace Operator (CLA Integration)
Starting in **v4.9.419**, Cora fully supports autonomous operation via CLA / ChatGPT with strict 30-action ceiling compliance:
1. Open [ChatGPT](https://chatgpt.com) and navigate to your Custom GPT or Agent builder.
2. In the **Actions** section, import the live schema endpoint:
   ```
   https://stagging.heycora.in/wp-json/cora/v1/mcp/openapi.json
   ```
   *(Note: By default, this endpoint serves 28 core actions, strictly adhering to OpenAI's 30-action import ceiling. For autonomous agents requiring the full unconstrained tool suite, pass `?all=1` or `?limit=250`)*.
3. Under **Authentication**:
   - Choose **OAuth**:
     - Client ID & Secret: Generated in **Cora Workspace → AI Tools / MCP → Developer Drawer**.
     - Authorization URL: `https://stagging.heycora.in/oauth/authorize`
     - Token URL: `https://stagging.heycora.in/oauth/token`
     - Scope: `*` (or custom scopes e.g. `workspace:read workspace:write leads:read leads:write tasks:read tasks:write media:read media:write finance:read canvas:read campaigns:read campaigns:write telemetry:read`)
4. In the minimal high-trust OAuth consent screen, select **"Full Access Operator"** and approve.
5. The RFC 8414 userinfo handshake (`/oauth/userinfo`) validates the account immediately, resolving any `"not_installed"` status and activating autonomous capabilities.

---

### Key ChatGPT Actions Technical Standards

#### 1. OpenAI 30-Action Import Ceiling Enforcement
OpenAI Custom GPTs enforce a hard limit of 30 actions per GPT schema import. Exceeding 30 actions causes immediate schema parsing failure with `Action limit exceeded`. Cora v4.9.419 implements a dynamic dual-view filtering topology:
- **Default View (`/openapi.json` or `?limit=28`)**: Serves exactly 28 high-frequency studio actions, leaving a 2-action buffer under the hard ceiling.
- **Uncapped Developer View (`?all=1` or `?limit=300`)**: Exposes all 246 canonical tools, CLA aliases, and specialized engines for Claude Desktop, Cursor, Antigravity, and headless MCP clients.
- **Zero-Scroll Developer Drawer**: Located in **Workspace Settings → AI / MCP Integrations**, providing 1-click schema copy buttons, token generator, and direct endpoint links.

#### 2. RFC 8414 Userinfo Handshake & OAuth 2.1
- Connects to `/oauth/userinfo` and `/wp-json/cora-oauth/v1/userinfo` to verify active tenant account identity and agency boundaries.
- Resolves ChatGPT's silent installation stalls (`status: not_installed`) by returning standard OIDC-compliant claims (`sub`, `name`, `email`, `workspace_id`, `agency_id`).

#### 3. OpenAPI 3.0.1 Compliance Engine
- Fully conforms to OpenAI's Custom GPT schema importer specifications (preventing undocumented 3.1.0 parser errors).
- Zero emojis or multibyte unicode characters in operation summaries, descriptions, and parameter names.
- All input schemas declare explicit `type: object` with non-empty properties objects.
- Empty `required: []` arrays are cleanly omitted to avoid OpenAI validator rejections.

#### 4. The Curated 28 Core Operator Tools
1. `get_authenticated_account` — Inspect connected user, active workspace, and scopes.
2. `list_workspaces` — Discover accessible tenant workspaces.
3. `get_workspace` — Inspect active modules and industry capabilities.
4. `list_leads` — Query CRM leads pipeline with status filtering.
5. `create_lead` — Ingest new lead inquiries with stage attribution.
6. `update_lead_status` — Advance leads across pipeline stages.
7. `list_tasks` — Retrieve workspace team tasks and deadlines.
8. `create_task` — Assign tasks with priority and due date.
9. `update_task` — Update task status or assignees.
10. `list_invoices` — Query GST billing documents and statuses.
11. `create_invoice` — Generate compliant invoices with HSN/SAC codes.
12. `get_financial_summary` — Executive financial overview and cleared revenue.
13. `upload_media` — Upload base64 assets and generate live CDN URLs.
14. `list_media` — Query media vault assets.
15. `get_media` — Retrieve detailed asset metadata and download URLs.
16. `delete_media` — Safely remove obsolete assets.
17. `list_bookings` — Query client photoshoot sessions and calendar.
18. `create_booking` — Schedule new client appointments.
19. `get_analytics` — Aggregate pipeline value, conversion velocity, and views.
20. `cora_get_canvas_analytics` — Ingest real-time Canvas page views, session counts, and conversions.
21. `cora_get_rum_vitals` — Retrieve 75th-percentile RUM Core Web Vitals (LCP, CLS, TTFB, INP).
22. `cora_sync_search_console_data` — Retrieve top ranking queries, impressions, and striking-distance keywords.
23. `cora_connect_inbox` — Connect business email inbox via Google Workspace or Hostinger.
24. `cora_save_outreach_campaign` — Create multi-step drip email campaigns with Spintax templates.
25. `cora_execute_campaign_batch` — Dispatch queued outreach batches with round-robin rotation.
26. `list_contracts` — Query legal contracts and e-sign statuses.
27. `create_contract` — Generate contracts from templates with GST terms.
28. `send_system_notification` — Dispatch in-app monochromatic toast alerts.

---

### Option 2: Upload to ChatGPT Plugin Directory (Submission Portal)
1. Go to the [OpenAI Plugin Submission Portal](https://chatgpt.com/plugins).
2. Compress this folder into `cora-chatgpt-plugin.zip`.
3. Drag & drop the `.zip` archive into the **Upload new or existing plugin** dropzone.
4. The automated validation scanner will verify:
   - Manifest metadata & legal URLs (`ai-plugin.json`).
   - OpenAPI endpoint schema compliance (`openapi.json`).
   - 401 unauthenticated challenge responses and RFC 8414 userinfo endpoint.
5. Click **Submit for Review** to publish.

---

## 🔒 Security & Multi-Tenant Privacy
- **Zero Cross-Tenant Leakage**: Every tool execution is strictly isolated to the authenticated workspace ID (`agency_id`). Cross-workspace queries are rejected with `-32003 Forbidden`.
- **E2E Operational Verification**: Verified 100% compliant across 12 automated verification steps (`scripts/test_cla_chatgpt_mcp_e2e.php`).
- **Statutory Compliance**: Built-in validation for Indian GST (CGST/SGST/IGST, SAC 9983) and international billing standards.
- **Audit Logging**: Every action invocation is permanently recorded in `wp_cora_mcp_audit_log` with IP, timestamp, user ID, workspace ID, and execution duration.
- **Encrypted Transmission**: All communication uses TLS/HTTPS with OAuth 2.1 PKCE or Bearer authentication.

---

*Cora Workspace ChatGPT & CLA Plugin v4.9.419 — Architecture & AI Integration Team. Last updated: October 2026.*
