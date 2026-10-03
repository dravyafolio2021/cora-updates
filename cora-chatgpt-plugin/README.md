# Cora Workspace — Official ChatGPT Plugin & MCP Connector

This package contains the official distribution files for integrating **Cora Creative & Field Studio OS** with **ChatGPT** via OpenAI Actions, ChatGPT Plugins, and the Model Context Protocol (MCP 2.0).

---

## 📦 Package Contents

- **`manifest.json` / `ai-plugin.json`**: Official OpenAI plugin manifest for the ChatGPT Plugin Directory.
- **`mcp.json`**: Remote MCP 2.0 server configuration schema.
- **`openapi.json`**: Complete OpenAPI 3.1.0 specification defining all Cora studio tools.
- **`skills/`**: Standard system prompts and workflow definitions for CRM, Invoicing, Bookings, and Copilot operations.
- **`assets/icon.png`**: High-resolution 512x512 monochromatic app icon.

---

## 🚀 How to Connect Cora with ChatGPT

### Option 1: 1-Click Connect via ChatGPT Custom Actions
1. Open [ChatGPT](https://chatgpt.com) and go to **Explore GPTs → Create a GPT**.
2. In the **Configure** tab, scroll down to **Actions** and click **Create new action**.
3. In the **Schema** box, import the OpenAPI spec:
   - Paste the contents of `openapi.json`, OR enter URL: `https://stagging.heycora.in/wp-json/cora/v1/mcp/openapi.json`
4. Under **Authentication**:
   - Choose **API Key** (Auth Type: `Bearer`).
   - Enter your workspace MCP token from **Cora Workspace → AI Tools / MCP** (or connect via OAuth 2.1).
5. Click **Save** & publish!

---

### Option 2: Upload to ChatGPT Plugin Directory (Submission Portal)
1. Go to the [OpenAI Plugin Submission Portal](https://chatgpt.com/plugins).
2. Compress this folder into `cora-chatgpt-plugin.zip`.
3. Drag & drop the `.zip` archive into the **Upload new or existing plugin** dropzone.
4. The automated validation scanner will verify:
   - Manifest metadata & legal URLs.
   - OpenAPI endpoint schema compliance.
   - 401 unauthenticated challenge responses.
5. Click **Submit for Review** to publish on the official directory.

---

## 🔒 Security & Multi-Tenant Privacy
- **Zero Cross-Tenant Leakage**: Every tool execution is strictly isolated to the authenticated workspace ID.
- **Statutory Compliance**: Built-in validation for Indian GST and international billing standards.
- **Encrypted Transmission**: All communication uses TLS/HTTPS with OAuth 2.1 or Bearer auth.
