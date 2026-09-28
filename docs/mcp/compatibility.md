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
| **ChatGPT** (Custom GPTs / Actions) | **Yes** | **Yes** | **Yes** | **Yes** | Prompted before write/destructive tools | OpenAPI 3.1.0 schema import from `/mcp/openapi.json` | 1-Click Import URL & OAuth | **VERIFIED** |
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
| `knowledge:read` | Intelligence | `read` | Query living RAG memory and operational policy documents |
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
| `proposals:read` | Sales | `read` | View client proposals and quotes |
| `proposals:write` | Sales | `write` | Generate and send proposals |
| `workflows:read` | Automation | `read` | Inspect automated triggers and rules |
| `workflows:write` | Automation | `write` | Configure automation workflows |

---

## 4. Canonical Tool Definitions & Safety Annotations Wire Format

Every tool uses the exact canonical wire annotations: `readOnlyHint`, `destructiveHint`, `openWorldHint`, and `idempotentHint` inside both `annotations` and top-level fields:

| Canonical Tool Name | Required Scope | `readOnlyHint` | `destructiveHint` | `openWorldHint` | `idempotentHint` | Description |
| :--- | :--- | :---: | :---: | :---: | :---: | :--- |
| `cora.get_workspace_overview` | `workspace:read` | `true` | `false` | `false` | `true` | Workspace pulse, deal value, receivables, active bookings |
| `cora.search_knowledge_base` | `knowledge:read` | `true` | `false` | `false` | `true` | Semantic living RAG knowledge search |
| `cora.list_clients` | `clients:read` | `true` | `false` | `false` | `true` | List client records and contact information |
| `cora.get_client` | `clients:read` | `true` | `false` | `false` | `true` | Retrieve specific client profile and active bookings |
| `cora.create_client` | `clients:write` | `false` | `false` | `false` | `false` | Create new client record |
| `cora.update_client` | `clients:write` | `false` | `false` | `false` | `true` | Update existing client details |
| `cora.list_leads` | `leads:read` | `true` | `false` | `false` | `true` | List CRM sales leads and deal pipeline |
| `cora.get_lead` | `leads:read` | `true` | `false` | `false` | `true` | Retrieve lead details and communication notes |
| `cora.create_lead` | `leads:write` | `false` | `false` | `false` | `false` | Create new CRM inquiry / deal |
| `cora.update_lead_status` | `leads:write` | `false` | `false` | `false` | `true` | Advance lead stage (new, contacted, qualified, won, lost) |
| `cora.list_projects` | `projects:read` | `true` | `false` | `false` | `true` | List studio shoot bookings and milestones |
| `cora.get_project` | `projects:read` | `true` | `false` | `false` | `true` | Retrieve full booking and deliverables details |
| `cora.create_project` | `projects:write` | `false` | `false` | `false` | `false` | Schedule new shoot booking |
| `cora.update_project` | `projects:write` | `false` | `false` | `false` | `true` | Update shoot status or deliverables |
| `cora.list_tasks` | `tasks:read` | `true` | `false` | `false` | `true` | List Kanban tasks by status and priority |
| `cora.create_task` | `tasks:write` | `false` | `false` | `false` | `false` | Create and assign operational task |
| `cora.update_task` | `tasks:write` | `false` | `false` | `false` | `true` | Update task status or priority |
| `cora.query_financials` | `finance:read` | `true` | `false` | `false` | `true` | Query invoices, receivables, and revenue |
| `cora.record_financial_transaction` | `finance:write` | `false` | `false` | `false` | `false` | Record payment collection or expense |
| `cora.search_content` | `content:read` | `true` | `false` | `false` | `true` | Search articles & guides in Growth CMS |
| `cora.get_content` | `content:read` | `true` | `false` | `false` | `true` | Retrieve complete structured article / guide |
| `cora.create_article` | `content:write` | `false` | `false` | `false` | `false` | Draft structured article with quick_answer and sources |
| `cora.create_guide` | `content:write` | `false` | `false` | `false` | `false` | Draft multi-chapter guide |
| `cora.validate_content` | `content:read` | `true` | `false` | `false` | `true` | Run evidence and schema validation checks |
| `cora.publish_content` | `content:write` | `false` | `true` | `true` | `false` | Validate, publish live, revalidate ISR cache, verify URL |
| `cora.rollback_content` | `content:write` | `false` | `true` | `true` | `false` | Restore previous revision snapshot |
| `cora.get_content_revisions` | `content:read` | `true` | `false` | `false` | `true` | Retrieve version history snapshots |
| `cora.check_content_overlap` | `content:read` | `true` | `false` | `false` | `true` | Check title/keyword cannibalization |
| `cora.upload_asset` | `content:write` | `false` | `false` | `false` | `false` | Upload image or document asset |

---

## 5. Security & Multi-Tenancy Guarantee

1. **Zero-Trust Workspace Isolation**: Workspace ID is strictly bound to the authenticated OAuth 2.1 access token. Any request attempting to spoof `workspace_id` in tool parameters is overridden server-side.
2. **Server-Side Scope Enforcement**: Even if an AI model invokes a write tool, the tool execution is rejected with `-32003 Forbidden` unless the token possesses the required scope.
3. **Audit Trail**: Every AI tool invocation is recorded in `wp_cora_mcp_audit_log` with user ID, workspace ID, client name, tool name, execution time, and status.
4. **Instant Revocation**: Users can disconnect any AI assistant instantly from **Settings → AI Assistants**, immediately invalidating the token server-side.
