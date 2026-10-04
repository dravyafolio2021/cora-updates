# Cora Universal MCP — AI Client Compatibility Matrix

This document provides a standards-first audit and connection matrix for connecting any MCP-compatible AI client to the **Cora Universal MCP Server** (`https://app.heycora.in/mcp`).

---

## 1. Universal Architecture Overview

```
                      ANY MCP CLIENT
 (ChatGPT • Claude • Gemini • Cursor • VS Code • Windsurf • Generic MCP Inspector)
                            ↕
                 CORA REMOTE MCP ENDPOINT
               (https://app.heycora.in/mcp)
                            ↕
               OAUTH 2.1 & RESOURCE METADATA
     (RFC 9728 Protected Resource, RFC 8414 Auth Server, RFC 7636 PKCE)
                            ↕
               CORA WORKSPACE MULTI-TENANCY
             (Zero-Trust Server-Side Scoping)
                            ↕
                  EXISTING CORA SERVICES
  (CRM Leads • Projects/Shoots • Tasks • Financials • Living RAG • Growth CMS)
```

- **Single Universal Endpoint**: `https://app.heycora.in/mcp`
- **Protocol Specifications Supported**: MCP `2026-07-28` (Canonical), `2025-11-25`, `2024-11-05`, `2024-10-07` (Negotiated via `initialize` or `server/discover`).
- **OAuth Protected Resource Metadata**: `/.well-known/oauth-protected-resource` (RFC 9728 compliant).
- **OAuth Authorization Server Metadata**: `/.well-known/oauth-authorization-server` (RFC 8414 compliant with Client ID Metadata Document / CIMD support).
- **Challenge Header**: Protected requests without a valid Bearer token return `HTTP 401 Unauthorized` with `WWW-Authenticate: Bearer resource_metadata="https://app.heycora.in/.well-known/oauth-protected-resource", realm="cora-mcp"`.
- **Multi-Tenancy**: 100% server-side tenant isolation. Authenticated tokens strictly bind to `workspace_id`. Client-supplied workspace parameters are never trusted.

---

## 2. AI Client Compatibility Matrix

| AI Client / Environment | Remote MCP Supported? | OAuth 2.1 Supported? | Read Tools | Write Tools | User Confirmation Behavior | Additional Config / Manifest | Installation UX | Status |
| :--- | :---: | :---: | :---: | :---: | :--- | :--- | :--- | :---: |
| **ChatGPT** (Custom GPTs / Actions) | **Yes** | **Yes** | **Yes** | **Yes** | Prompted before write/destructive tools | OpenAPI 3.0.1 schema import from `/wp-json/cora/v1/mcp/openapi.json` (Curated 28 Tools) | 1-Click Import URL & OAuth | **VERIFIED** |
| **Claude Desktop** (Anthropic) | **Yes** | **Yes** | **Yes** | **Yes** | Displays tool execution callout | JSON snippet in `claude_desktop_config.json` (via stdio bridge or direct SSE) | Copy JSON configuration snippet | **VERIFIED** |
| **Gemini CLI / Workspace** | **Yes** | **Yes** | **Yes** | **Yes** | Model function calling consent | Extension / Tool configuration | Connect via Remote MCP URL | **VERIFIED** |
| **Cursor IDE** (Anysphere) | **Yes** | **Yes** | **Yes** | **Yes** | Status pill in Composer/Chat | Add SSE/Remote MCP in Settings → Features → MCP | Enter MCP URL & Connect | **VERIFIED** |
| **VS Code / GitHub Copilot** | **Yes** | **Yes** | **Yes** | **Yes** | Extension permissions modal | `.vscode/settings.json` or MCP extension manifest | Add server configuration | **VERIFIED** |
| **Windsurf** (Codeium) | **Yes** | **Yes** | **Yes** | **Yes** | Cascade tool approval prompt | `mcp_config.json` entry | Copy JSON configuration snippet | **VERIFIED** |
| **Generic MCP Inspector** | **Yes** | **Yes** | **Yes** | **Yes** | Direct execution inspector UI | MCP 2026-07-28 / 2024-11-05 JSON-RPC | Connect via URL + Bearer Token | **VERIFIED** |
| **Claude Web / Future Web Connect** | **Yes** | **Yes** | **Yes** | **Yes** | In-conversation confirmation | Web URL endpoint connector | Enter `https://app.heycora.in/mcp` | **PARTIAL** |

---

## 3. Scopes & Permission Hierarchy

Cora enforces granular, user-controlled scopes:

| Scope | Category | Level | Description |
| :--- | :--- | :---: | :--- |
| `workspace:read` | Core | `read` | Retrieve workspace overview, metrics, team metadata |
| `workspace:write` | Core | `write` | Manage workspace settings, company branding, address |
| `canvas:read` | Canvas | `read` | Retrieve website themes, page HTML, and builder metadata |
| `canvas:write` | Canvas | `write` | Edit and publish website HTML, create and activate themes |
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
| `finance:read` | Finance | `read` | Read invoices, receivables, and revenue ledger analytics |
| `finance:write` | Finance | `write` | Record payment transactions, expenses, and memos |
| `content:read` | Growth | `read` | Search articles, guides, assets, and SEO metrics |
| `content:write` | Growth | `write` | Create articles/guides, upload assets, validate, publish live |
| `workflows:read` | Automation | `read` | Inspect automated triggers and rules |
| `workflows:write` | Automation | `write` | Configure automation workflows |

---

## 4. Curated 28 Core Actions for ChatGPT & Universal MCP

To guarantee compatibility with OpenAI Custom GPT Actions (which enforce an undocumented ~30 operation ceiling and strict OpenAPI 3.0.x schema requirements), Cora curates 28 prioritized operational tools by default:

| Canonical Tool Name | Required Scope | `readOnlyHint` | `destructiveHint` | `openWorldHint` | `idempotentHint` | Description |
| :--- | :--- | :---: | :---: | :---: | :---: | :--- |
| `cora_get_workspace_overview` | `workspace:read` | `true` | `false` | `false` | `true` | Workspace pulse, deal value, receivables, active bookings |
| `cora_canvas_get_active_theme` | `canvas:read` | `true` | `false` | `false` | `true` | Active website theme, builder type, preview URL, static build status |
| `cora_canvas_list_themes` | `canvas:read` | `true` | `false` | `false` | `true` | List installed website themes and live/draft statuses |
| `cora_canvas_get_page_html` | `canvas:read` | `true` | `false` | `false` | `true` | Read HTML source code directly from static build distribution |
| `cora_canvas_update_page_html` | `canvas:write` | `false` | `false` | `false` | `true` | Publish updated HTML live immediately without recompilation |
| `cora_canvas_create_theme` | `canvas:write` | `false` | `false` | `false` | `false` | Create new website theme with custom HTML and activate live |
| `cora_canvas_activate_theme` | `canvas:write` | `false` | `false` | `false` | `true` | Set a specific theme ID to be the live production website |
| `cora_get_website_settings` | `workspace:read` | `true` | `false` | `false` | `true` | Website business name, tagline, address, contact email/phone |
| `cora_update_website_settings` | `workspace:write` | `false` | `false` | `false` | `true` | Update website business identity, tagline, address, contacts |
| `cora_list_leads` | `leads:read` | `true` | `false` | `false` | `true` | List CRM sales leads and deal pipeline |
| `cora_get_lead` | `leads:read` | `true` | `false` | `false` | `true` | Retrieve lead details and interaction history |
| `cora_create_lead` | `leads:write` | `false` | `false` | `false` | `false` | Create new CRM sales lead |
| `cora_update_lead` | `leads:write` | `false` | `false` | `false` | `true` | Update lead contact details, deal value, or status stage |
| `cora_list_clients` | `clients:read` | `true` | `false` | `false` | `true` | List client records and contact information |
| `cora_create_client` | `clients:write` | `false` | `false` | `false` | `false` | Create new client record |
| `cora_list_projects` | `projects:read` | `true` | `false` | `false` | `true` | List studio shoot bookings, project timelines, deliverables |
| `cora_create_project` | `projects:write` | `false` | `false` | `false` | `false` | Schedule new shoot booking or operational project |
| `cora_list_tasks` | `tasks:read` | `true` | `false` | `false` | `true` | List Kanban tasks by status and priority |
| `cora_create_task` | `tasks:write` | `false` | `false` | `false` | `false` | Create and assign operational task |
| `cora_update_task` | `tasks:write` | `false` | `false` | `false` | `true` | Update task status, priority, or assignee |
| `cora_query_financials` | `finance:read` | `true` | `false` | `false` | `true` | Query invoices, receivables, collected revenue, expenses |
| `cora_record_financial_transaction` | `finance:write` | `false` | `false` | `false` | `false` | Record payment collection, invoice, retainer, or expense |
| `cora_search_content` | `content:read` | `true` | `false` | `false` | `true` | Search articles & guides in Growth CMS |
| `cora_create_article` | `content:write` | `false` | `false` | `false` | `false` | Draft structured article with quick_answer and sources |
| `cora_publish_content` | `content:write` | `false` | `true` | `true` | `false` | Validate, publish live, revalidate ISR cache, verify URL |
| `cora_search_knowledge_base` | `knowledge:read` | `true` | `false` | `false` | `true` | Query living RAG memory and operational SOP documents |
| `cora_add_knowledge_fragment` | `knowledge:read` | `false` | `false` | `false` | `false` | Store new memory fragment, client preference, or SOP |
| `cora_list_forms` | `workspace:read` | `true` | `false` | `false` | `true` | List intake forms, screener questions, and response totals |

*Note: AI clients requesting the full uncapped registry can pass `?all=1` or `limit=all` to retrieve all 40+ domain tools.*

---

## 5. Security & Multi-Tenancy Guarantee

1. **Zero-Trust Workspace Isolation**: Workspace ID is strictly bound to the authenticated OAuth 2.1 access token. Any request attempting to spoof `workspace_id` in tool parameters is overridden server-side.
2. **Server-Side Scope Enforcement**: Even if an AI model invokes a write tool, the tool execution is rejected with `-32003 Forbidden` unless the token possesses the required scope.
3. **Audit Trail**: Every AI tool invocation is recorded in `wp_cora_mcp_audit_log` with user ID, workspace ID, client name, tool name, execution time, and status.
4. **Instant Revocation**: Users can disconnect any AI assistant instantly from **Settings → AI Assistants**, immediately invalidating the token server-side.

---

## 6. Interactive OAuth 2.1 Consent Panel

Starting in **v4.9.354**, the authorization flow (`/cora/v1/oauth/authorize`) features a modern, user-friendly consent panel:
- **Universal Full Access Master Switch**: 1-click toggle enabling or disabling all 20 scopes simultaneously.
- **Categorized Domain Panels**: Grouped into 10 structured cards (Core Workspace, Website & Themes, CRM & Leads, Directory, Operations & Bookings, Tasks, Finance & Ledger, Content & SEO, Living Memory RAG, Automations).
- **Quick Presets**: 1-click presets for `All Tools`, `Read-Only`, and `Website & Themes Only`.
- **Live Scope Counter**: Dynamic label indicating `X of 20 granted` before granting consent.

---

## 7. AI Tools MCP Gateway & Live Action Playground in Template Editor

Integrated in **v4.9.351** directly within the Canvas Template Editor:
- **Natural Language Mode**: Formulate plain-English instructions (e.g. "Update hero title to Luxury Architecture Studio") and execute them via the tool loop.
- **Guided Actions Mode**: Select canonical tools from a dropdown with dynamic form fields corresponding to each tool's schema.
- **Raw JSON Mode**: Low-level JSON-RPC 2.0 console for testing and inspecting responses.
- **1-Click Apply (`window.coraApplyPlaygroundCodeToEditor`)**: Reflects returned HTML code directly into the CodeMirror editor and writes immediately to the static distribution build.
