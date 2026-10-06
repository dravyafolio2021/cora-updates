# Cora Workspace — Official ChatGPT Plugin & MCP Connector

This package contains the official distribution files for integrating **Cora Creative & Field Studio OS (v4.9.364)** with **ChatGPT** via OpenAI Custom GPT Actions, ChatGPT Plugins, and the Model Context Protocol (MCP).

---

## 📦 Package Contents

- **`manifest.json` / `ai-plugin.json`**: Official OpenAI plugin manifest for the ChatGPT Plugin Directory.
- **`mcp.json`**: Remote MCP server configuration schema.
- **`openapi.json`**: Hardened OpenAPI 3.0.1 specification defining the curated 28 core studio tools ceiling.
- **`skills/`**: Standard system prompts and workflow definitions for CRM, Invoicing, Bookings, and Copilot operations.
- **`assets/icon.png`**: High-resolution 512x512 monochromatic app icon.

---

## 🚀 How to Connect Cora with ChatGPT

### Option 1: 1-Click Connect via ChatGPT Custom Actions (Recommended)
1. Open [ChatGPT](https://chatgpt.com) and go to **Explore GPTs → Create a GPT**.
2. In the **Configure** tab, scroll down to **Actions** and click **Create new action**.
3. In the **Schema** box, import the OpenAPI spec:
   - Paste the contents of `openapi.json`, OR enter URL: `https://stagging.heycora.in/wp-json/cora/v1/mcp/openapi.json` (or `/mcp/openapi.json`).
4. Under **Authentication**:
   - Choose **OAuth**:
     - Client ID & Client Secret: From **Cora Workspace → AI Tools / MCP**.
     - Authorization URL: `https://stagging.heycora.in/oauth/authorize`
     - Token URL: `https://stagging.heycora.in/oauth/token`
     - Scope: `workspace:read leads:read leads:write tasks:read tasks:write finance:read canvas:read`
   - OR choose **API Key** (Auth Type: `Bearer`) and supply your static workspace MCP token.
5. In the interactive OAuth 2.1 Consent Panel, choose authorization scopes or use 1-tap presets (`all`, `readonly`, `website`).
6. Click **Save** & publish!

---

### Key ChatGPT Actions Technical Standards
- **OpenAPI 3.0.1 Compliance**: Fully conforms to OpenAI's Custom GPT schema importer requirements (avoiding undocumented 3.1.0 parser failures).
- **Curated 28 Core Tools Ceiling**: OpenAI enforces an undocumented limit of ~30 actions per Custom GPT. Cora curates the top 28 operational tools by default (pass `?all=1` to the schema endpoint if unrestricted access is needed).
- **Strict Parameter Hardening**:
  - Zero emojis in operation summaries, descriptions, and parameter names.
  - All input schemas declare explicit `type: object` with non-empty properties objects.
  - Empty `required: []` arrays are cleanly omitted.

---

### Option 2: Upload to ChatGPT Plugin Directory (Submission Portal)
1. Go to the [OpenAI Plugin Submission Portal](https://chatgpt.com/plugins).
2. Compress this folder into `cora-chatgpt-plugin.zip` (run `./package.sh`).
3. Drag & drop the `.zip` archive into the **Upload new or existing plugin** dropzone.
4. The automated validation scanner will verify:
   - Manifest metadata & legal URLs.
   - OpenAPI endpoint schema compliance.
   - 401 unauthenticated challenge responses.
5. Click **Submit for Review** to publish on the official directory.

---

## 🔒 Security & Multi-Tenant Privacy
- **Zero Cross-Tenant Leakage**: Every tool execution is strictly isolated to the authenticated workspace ID (`agency_id`).
- **Statutory Compliance**: Built-in validation for Indian GST (CGST/SGST/IGST) and international billing standards.
- **Audit Logging**: Every action invocation is permanently recorded in `wp_cora_mcp_audit_log` with IP, timestamp, and duration.
- **Encrypted Transmission**: All communication uses TLS/HTTPS with OAuth 2.1 PKCE or Bearer authentication.
