# Cora Universal MCP — AI Client Compatibility Matrix (v4.9.419)

This document provides a standards-first audit, connection matrix, and operational specification for connecting any MCP-compatible AI client to the **Cora Universal MCP Server** (`https://app.heycora.in/mcp` or `/wp-json/cora/v1/mcp`).

---

## 1. Universal Architecture Overview

```
                                  ANY MCP CLIENT / OPERATOR
  (Google Gemini • Anthropic Claude • ChatGPT / CLA Operator • Cursor • VS Code • Windsurf • Generic Inspector)
                                             ↕
                                  CORA REMOTE MCP ENDPOINT
                      (https://app.heycora.in/mcp & /wp-json/cora/v1/mcp)
                                             ↕
                                OAUTH 2.1 & RFC 8414 METADATA
     (RFC 9728 Protected Resource • RFC 8414 Auth Server & Userinfo • RFC 7636 PKCE • client_credentials)
                                             ↕
                               CORA WORKSPACE MULTI-TENANCY
                             (Zero-Trust Server-Side Scoping)
                                             ↕
                                  EXPANDED 246-TOOL CATALOG
       (Canonical Base • cla_* Aliases • cora_* Aliases • Dynamic Skills & Autonomous Operators)
                                             ↕
                                   CORE WORKSPACE ENGINES
  (Autonomous Campaigns • Dynamic Template Engine • CRM & Leads • Media Vault • Finance & GST • Living RAG)
```

- **Single Universal Endpoint**: `https://app.heycora.in/mcp` and `/wp-json/cora/v1/mcp`
- **Protocol Specifications Supported**: MCP `2026-07-28` (Canonical), `2025-11-25`, `2024-11-05`, `2024-10-07` (Negotiated dynamically via `initialize` or `server/discover`).
- **OAuth Protected Resource Metadata**: `/.well-known/oauth-protected-resource` (RFC 9728 compliant).
- **OAuth Authorization Server Metadata**: `/.well-known/oauth-authorization-server` (RFC 8414 compliant with Client ID Metadata Document / CIMD support).
- **RFC 8414 Userinfo Endpoint**: `/oauth/userinfo` and `/wp-json/cora-oauth/v1/userinfo` returning active account identity, workspace binding, and verified scopes.
- **Challenge Header**: Protected requests without a valid Bearer token return `HTTP 401 Unauthorized` with `WWW-Authenticate: Bearer resource_metadata="https://app.heycora.in/.well-known/oauth-protected-resource", realm="cora-mcp"`.
- **Grant Types**: `authorization_code` (with PKCE `S256`), `client_credentials` (for 1-click autonomous server-to-server agents like Gemini & CLA), and `refresh_token`.
- **Multi-Tenancy**: 100% server-side tenant isolation (`agency_id = %d`). Authenticated tokens strictly bind to `workspace_id`. Client-supplied workspace parameters are never trusted or permitted to bleed cross-tenant.

---

## 2. AI Client Compatibility Matrix

| AI Client / Operator | Remote MCP Supported? | OAuth 2.1 / Auth Grants | Read Tools | Write Tools | User Confirmation Behavior | Additional Config / Manifest | Installation UX | Status |
| :--- | :---: | :---: | :---: | :---: | :--- | :--- | :--- | :---: |
| **ChatGPT / CLA Autonomous Operator** | **Yes** | **Yes** (Auth Code + PKCE & Userinfo) | **Yes** | **Yes** | Autonomous execution or prompt on write | Dynamic `ai-plugin.json` (`cla` / `cora`) & RFC 8414 userinfo; Full 246 tools catalog | 1-Click Connect URL & OAuth | **VERIFIED (12/12 E2E Steps)** |
| **Google Gemini** (1-Click Connector) | **Yes** | **Yes** (`client_credentials` & Bearer) | **Yes** | **Yes** | Model function calling consent | 1-Click Seamless Connector in Developer Drawer | 1-Click Authorize & Generate | **VERIFIED** |
| **Claude Desktop** (Anthropic) | **Yes** | **Yes** (Bearer Token & OAuth) | **Yes** | **Yes** | Displays tool execution callout | JSON snippet in `claude_desktop_config.json` (stdio bridge or direct SSE) | Copy JSON configuration snippet | **VERIFIED** |
| **Cursor IDE** (Anysphere) | **Yes** | **Yes** (Bearer Token) | **Yes** | **Yes** | Status pill in Composer/Chat | Add SSE/Remote MCP in Settings → Features → MCP | Enter MCP URL & Connect | **VERIFIED** |
| **VS Code / GitHub Copilot** | **Yes** | **Yes** (Bearer Token) | **Yes** | **Yes** | Extension permissions modal | `.vscode/settings.json` or MCP extension manifest | Add server configuration | **VERIFIED** |
| **Windsurf** (Codeium) | **Yes** | **Yes** (Bearer Token) | **Yes** | **Yes** | Cascade tool approval prompt | `mcp_config.json` entry | Copy JSON configuration snippet | **VERIFIED** |
| **Generic MCP Inspector** | **Yes** | **Yes** (Bearer Token) | **Yes** | **Yes** | Direct execution inspector UI | MCP 2026-07-28 / 2024-11-05 JSON-RPC | Connect via URL + Bearer Token | **VERIFIED** |
| **Claude Web / Remote Connector** | **Yes** | **Yes** (OAuth 2.1) | **Yes** | **Yes** | In-conversation confirmation | Web URL endpoint connector | Enter `https://app.heycora.in/mcp` | **VERIFIED** |

---

## 3. Scopes & Permission Hierarchy

Cora enforces granular, tenant-controlled scopes:

| Scope | Category | Level | Description |
| :--- | :--- | :---: | :--- |
| `workspace:read` | Core | `read` | Retrieve workspace overview, metrics, team metadata, account info |
| `workspace:write` | Core | `write` | Manage workspace settings, company branding, operational address |
| `campaigns:read` | Growth | `read` | Retrieve autonomous campaign statuses, cadences, and execution logs |
| `campaigns:write` | Growth | `write` | Create, configure, and trigger autonomous multi-channel marketing campaigns |
| `canvas:read` | Canvas | `read` | Retrieve website themes, page HTML, templates, and builder metadata |
| `canvas:write` | Canvas | `write` | Edit and publish website HTML, manage dynamic templates, activate themes |
| `knowledge:read` | Intelligence | `read` | Query living RAG memory and operational policy documents |
| `knowledge:write` | Intelligence | `write` | Store new memory fragments and operational policies |
| `clients:read` | CRM | `read` | View client profiles, contact details, account history |
| `clients:write` | CRM | `write` | Create and update client records |
| `leads:read` | CRM | `read` | View CRM inquiries, deal stages, and pipeline value |
| `leads:write` | CRM | `write` | Create leads, update deal stages, log interaction notes |
| `projects:read` | Operations | `read` | View shoot bookings, project timelines, deliverables |
| `projects:write` | Operations | `write` | Schedule shoots, update deliverables and statuses |
| `tasks:read` | Operations | `read` | View workspace Kanban tasks and assignments |
| `tasks:write` | Operations | `write` | Create, assign, update, and complete tasks |
| `media:read` | Media | `read` | List media files, folders, and asset telemetry |
| `media:write` | Media | `write` | Upload media assets, generate thumbnails, delete assets |
| `finance:read` | Finance | `read` | Read invoices, receivables, and revenue ledger analytics |
| `finance:write` | Finance | `write` | Record payment transactions, expenses, and memos |
| `content:read` | Growth | `read` | Search articles, guides, assets, and SEO metrics |
| `content:write` | Growth | `write` | Create articles/guides, upload assets, validate, publish live |
| `workflows:read` | Automation | `read` | Inspect automated triggers and rules |
| `workflows:write` | Automation | `write` | Configure automation workflows |

---

## 4. Full 246-Tool Catalog Architecture & Namespace Expansion

Starting in **v4.9.412**, Cora exposes a universal catalog expanded to **246 tool aliases**. Every operational tool registered in `Cora_MCP_Tool_Registry` automatically registers across three interoperable naming conventions:
1. **Canonical Base Name** (e.g. `list_leads`, `create_task`, `upload_media`)
2. **`cla_*` Prefixed Alias** (e.g. `cla_list_leads`, `cla_create_task`, `cla_upload_media` for ChatGPT & CLA Autonomous Operators)
3. **`cora_*` Prefixed Alias** (e.g. `cora_list_leads`, `cora_create_task`, `cora_upload_media` for Claude, Cursor, and Gemini)

When clients request filtered OpenAPI schemas (such as OpenAI Custom GPT Actions), Cora serves the curated 28-tool ceiling by default to prevent import rejections. When an autonomous operator connects via MCP or passes `?all=1`, the full uncapped 246-tool matrix is exposed without truncation.

### The 9 Universal Core Operator Tool Handlers (v4.9.412)
These foundational handlers empower an external AI agent to operate as an autonomous workspace director:

| Tool Identifier | Scope | Type | Description |
| :--- | :--- | :---: | :--- |
| `get_authenticated_account` | `workspace:read` | `read` | Returns active user display name, email, workspace ID, plan, and granted scopes. |
| `list_workspaces` | `workspace:read` | `read` | Lists all accessible tenant workspaces with status, industry vertical, and role. |
| `get_workspace` | `workspace:read` | `read` | Detailed capability inspection: active modules, industry settings, and branding. |
| `upload_media` | `media:write` | `write` | Uploads base64 or binary media, sanitizes metadata, and returns live URL. |
| `list_media` | `media:read` | `read` | Lists media vault items with MIME types, thumbnails, and size telemetry. |
| `get_media` | `media:read` | `read` | Fetches specific media asset metadata, direct download link, and attachment relations. |
| `delete_media` | `media:write` | `write` | Safely purges media assets and invalidates related cached URLs. |
| `get_analytics` | `workspace:read` | `read` | Aggregated analytics covering CRM pipeline value, conversion velocity, and views. |
| `get_financial_summary` | `finance:read` | `read` | Executive financial health: cleared revenue, pending receivables, and tax summaries. |

### Autonomous Campaigns Tools (v4.9.406)
| Tool Identifier | Scope | Type | Description |
| :--- | :--- | :---: | :--- |
| `cora_get_campaigns` | `campaigns:read` | `read` | Lists autonomous content campaigns, schedules, and historical run logs. |
| `cora_create_campaign` | `campaigns:write` | `write` | Creates an autonomous campaign with topic keywords, cadence, and quality thresholds. |
| `cora_trigger_campaign` | `campaigns:write` | `write` | Manually executes an immediate autonomous campaign run with AI generation. |
| `cora_improve_article` | `content:write` | `write` | Audits an existing article and applies 11-point algorithmic SEO & GEO enhancements. |

### Dynamic Template Engine Tools (v4.9.412)
| Tool Identifier | Scope | Type | Description |
| :--- | :--- | :---: | :--- |
| `cora_canvas_get_templates` | `canvas:read` | `read` | Lists registered archetypes (Article, Page, Policy, Product, Collection) and slot definitions. |
| `cora_canvas_render_template` | `canvas:read` | `read` | Dynamically renders content entity data through archetype slots into clean HTML. |

### Decision Telemetry & Performance Tools (v4.9.418 - v4.9.419)
| Tool Identifier | Scope | Type | Description |
| :--- | :--- | :---: | :--- |
| `cora_get_canvas_analytics` | `workspace:read` | `read` | Retrieves real-time visitors, session dwell times, bounce rates, and closed-loop conversion metrics. |
| `cora_get_rum_vitals` | `workspace:read` | `read` | Returns 75th percentile Real User Monitoring (RUM) LCP, INP, and CLS scores. |
| `cora_sync_search_console_data` | `workspace:write` | `write` | Triggers GSC query sync, identifying page-1 striking distance keywords (positions 4–15). |

### Business Email & Outreach Tools (v4.9.418 - v4.9.419)
| Tool Identifier | Scope | Type | Description |
| :--- | :--- | :---: | :--- |
| `cora_connect_inbox` | `workspace:write` | `write` | Connects Google Workspace or Hostinger Webmail inboxes with automated health diagnostics. |
| `cora_save_outreach_campaign` | `campaigns:write` | `write` | Configures multi-account cold email outreach cadences with Spintax and sender rotation. |
| `cora_execute_campaign_batch` | `campaigns:write` | `write` | Dispatches outbound batch emails distributed evenly across connected sender pools. |

---

## 5. Top 3 Market Leader Connectors, High-Trust OAuth & Developer Gateway

Integrated in **v4.9.407** and perfected in **v4.9.419**:

### 1. Google Gemini 1-Click Connector
- Employs OAuth `client_credentials` grant for effortless server-to-server connection.
- Pre-configured in the Workspace Developer Gateway Drawer (`workspace-developer-gateway-drawer.php`).
- Instantaneous client credential issuance and connection verification within 200ms.

### 2. Anthropic Claude Desktop Integration
- Fully compatible with `claude_desktop_config.json` via remote SSE transport (`https://app.heycora.in/wp-json/cora-mcp/v1/sse`).
- Copy-paste configuration generator with pre-filled workspace Bearer tokens.

### 3. ChatGPT & CLA Autonomous Workspace Operator
- Fully resolves the ChatGPT `"not_installed"` issue via RFC 8414 Dynamic Userinfo handshake (`/oauth/userinfo`).
- Validated with 12/12 Automated Operational Verification steps (`scripts/test_cla_chatgpt_mcp_e2e.php`).
- Enforces 30-action ceiling (`?limit=28`) for standard Custom GPT schema validation while exposing 246 tools unconstrained via `?all=1`.

### 4. Minimal High-Trust Consent Screen (`class-cora-oauth-server.php`)
- Streamlined visual hierarchy eliminating security anxiety.
- Live scope grant counter (`X of Y granted`).
- 1-Click authorization presets: **"Full Access Operator"**, **"Read-Only Auditor"**, and **"Website & Media Only"**.
- Transparent display of client application name, logo, and requested permissions.

### 5. Zero-Scroll Developer Gateway Drawer (`workspace-developer-gateway-drawer.php`)
- Docked directly beneath the header complying with Rule 15 SOP (`top: 52px; height: calc(100vh - 52px)`).
- Tabbed organization: Workspace Keys, Connect AI Agents (Claude, Cursor, ChatGPT, Gemini), Universal Tools Matrix (246 tools), REST Endpoints Reference, and Live Sandbox Execution.

---

## 6. Security, Multi-Tenancy & Audit Governance

1. **Zero-Trust Multi-Tenancy**: The workspace ID is cryptographically anchored to the issued OAuth 2.1 access token. Any attempt by an AI client to supply a spoofed `workspace_id` parameter is strictly overridden by the authenticated token's context.
2. **Strict Cross-Tenant Rejection**: Verified by Step 11 of the E2E verification suite: queries using tokens from Workspace A attempting to read records in Workspace B return `-32003 Forbidden`.
3. **Comprehensive Audit Trail**: Every tool execution is recorded in `wp_cora_mcp_audit_log` with user ID, workspace ID, client ID, tool name, execution duration, and status.
4. **Instant Revocation**: Tokens can be revoked immediately from **Settings → AI Assistants** or via the RFC 7009 revocation endpoint (`/oauth/revoke`).

---

*Cora Universal MCP Compatibility Matrix v4.9.419 — Architecture & AI Integration Team. Last updated: October 2026.*
