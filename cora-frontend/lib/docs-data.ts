export interface DocArticle {
  slug: string;
  title: string;
  shortTitle: string;
  category: 'overview' | 'intelligence' | 'sales' | 'operations' | 'finance' | 'developers';
  categoryLabel: string;
  description: string;
  readTime: string;
  lastUpdated: string;
  badge?: string;
  toc: { id: string; title: string }[];
  content: string;
}

export interface DocCategory {
  id: 'overview' | 'intelligence' | 'sales' | 'operations' | 'finance' | 'developers';
  label: string;
  iconName: string;
  articles: DocArticle[];
}

export const DOCS_DATA: DocArticle[] = [
  // ════════════════════════════════════════════════════════════════════════
  // ── 1. GETTING STARTED & PLATFORM ARCHITECTURE ─────────────────────────
  // ════════════════════════════════════════════════════════════════════════
  {
    slug: 'platform-architecture',
    title: 'Platform Architecture & Multi-Tenant Stack',
    shortTitle: 'Architecture & Stack',
    category: 'overview',
    categoryLabel: 'Getting Started',
    description: 'Comprehensive architectural guide to Cora’s single-plugin WordPress backend runtime, Next.js 16 SSG edge layer, and isolated MySQL schemas.',
    readTime: '7 min read',
    lastUpdated: 'August 2026',
    badge: 'Core System',
    toc: [
      { id: 'system-overview', title: '1. System Overview' },
      { id: 'routing-layer', title: '2. Hybrid Routing & Subdomains' },
      { id: 'database-isolation', title: '3. Multi-Tenant Database Isolation' },
      { id: 'caching-performance', title: '4. LiteSpeed & PWA Caching' },
      { id: 'security-model', title: '5. Zero-Trust Security Model' },
    ],
    content: `
Cora is engineered as an enterprise-grade autonomous operating system for Indian creative studios and agencies. It operates on a **hybrid decoupled architecture** where a high-performance Next.js marketing and documentation layer is paired with a resilient, single-plugin WordPress/PHP 8.2 backend runtime.

---

## 1. System Overview

The Cora platform consists of four primary decoupled layers:

1. **Next.js Static/SSG Front-End (\`heycora.in\`)**:
   - Built on Next.js 16 (App Router) with Turbopack.
   - Deployed on LiteSpeed Web Server as pre-rendered static HTML/CSS/JS.
   - Sub-50ms Time-To-First-Byte (TTFB) globally with zero server compute overhead.
   - Houses the public website, interactive comparison benchmarks, calculator tools, and Developer Documentation Hub (\`/docs\`).

2. **WordPress Core Application Engine (\`app.heycora.in\` & \`cora.local\`)**:
   - Single, consolidated core plugin (\`cora-workspace\`) containing all 20+ operational modules.
   - Custom Single Page Application (SPA) shell rendered at \`/workspace/dashboard\`.
   - Zero dependency on external bloated third-party plugins.

3. **Autonomous AI & RAG Engine**:
   - Google Gemini 3.5 Flash and Claude 3.5 Sonnet integrations.
   - Vector-less SQLite/MySQL keyword-dense semantic chunking database.
   - Bidirectional Model Context Protocol (MCP) JSON-RPC 2.0 gateway.

4. **PWA Mobile Shell & Push Layer (v3.2.46)**:
   - Dynamic \`cora-manifest.json\` generated with agency white-label assets.
   - Root-scoped Service Worker (\`cora-service-worker.js\`) with Network-First navigation and Cache-First static asset caching.
   - VAPID ES256 self-signed Web Push protocol without external messaging servers.

\`\`\`
┌─────────────────────────────────────────────────────────────┐
│                    EDGE LAYER (CLOUDFLARE)                  │
└──────────────┬──────────────────────────────┬───────────────┘
               │                              │
        [heycora.in/*]                 [app.heycora.in/*]
               ▼                              ▼
┌─────────────────────────────┐ ┌─────────────────────────────┐
│  Next.js 16 SSG Frontend    │ │  WordPress / LiteSpeed PHP  │
│  - Marketing Site           │ │  - Single 'cora-workspace'  │
│  - Interactive Benchmarks   │ │  - 20+ Modular Views (SPA)  │
│  - Developer Docs (/docs)   │ │  - REST API & VAPID Push    │
└─────────────────────────────┘ └──────────────┬──────────────┘
                                               │
                                       [Agency-Scoped SQL]
                                               ▼
                                ┌─────────────────────────────┐
                                │   MySQL Database (cora_*)   │
                                └─────────────────────────────┘
\`\`\`

---

## 2. Hybrid Routing & Subdomains

Inbound requests are handled via strict virtual host separation:

| Host / Subdomain | Target Layer | Handler File | Purpose |
|---|---|---|---|
| \`heycora.in\` | Next.js Frontend | \`out/index.html\`, \`out/docs/*\` | Marketing, Docs, Tools |
| \`app.heycora.in\` | WordPress Backend | \`cora-workspace.php\` | Production SaaS Tenant SPA |
| \`staging.heycora.in\` | Staging Instance | \`cora-workspace.php\` | QA & Feature Release Previews |
| \`cora.local\` | Local Docker Environment | \`admin-dashboard.php\` | Local Development & Tests |

---

## 3. Multi-Tenant Database Isolation

All business data is strictly isolated by \`agency_id\` (Tenant Workspace Identifier). Every query executed by the workspace engine explicitly enforces:

\`\`\`sql
SELECT * FROM wp_cora_leads 
WHERE agency_id = %d AND status = 'active' 
ORDER BY created_at DESC;
\`\`\`

Cross-tenant leakage is prevented at both the database abstraction layer and the REST authentication middleware.

---

## 4. LiteSpeed & PWA Caching

- **Static Cache**: CSS and JS bundles are stamped with dynamic version parameters (\`?v=CORA_WORKSPACE_VERSION\`) to guarantee immediate browser updates without stale cache collisions.
- **Cache Invalidation**: On deployment (\`python3 scripts/deploy_frontend.py\`), LiteSpeed cache tags are flushed instantly.
- **Service Worker Lifecycle**: On new version release, \`cora-service-worker.js\` automatically calls \`self.skipWaiting()\` and purges outdated cache buckets within 300ms.

---

## 5. Zero-Trust Security Model

- **No Browser Defaults**: The system uses monochromatic custom toast banners (\`window.coraShowToast\`) and sliding drawers instead of native popup overlays.
- **Role Capability Checks**: Every AJAX and REST handler verifies \`current_user_can()\` against a granular 30-point security matrix.
- **SHA-256 Hashes**: Document vault agreements and audit log records include cryptographically verifiable integrity checksums.
    `,
  },
  {
    slug: 'quickstart',
    title: 'Workspace Quickstart Guide',
    shortTitle: 'Quickstart Guide',
    category: 'overview',
    categoryLabel: 'Getting Started',
    description: 'Set up your creative studio workspace in under 5 minutes: business profile, GST tax rates, team roles, and initial booking.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'step-1-login', title: 'Step 1: First-Time Login' },
      { id: 'step-2-profile', title: 'Step 2: Studio Profile & GSTIN' },
      { id: 'step-3-team', title: 'Step 3: Team Members & RBAC' },
      { id: 'step-4-first-booking', title: 'Step 4: Create Your First Shoot Booking' },
      { id: 'step-5-pwa', title: 'Step 5: Install Mobile PWA' },
    ],
    content: `
Welcome to Cora Studio OS. This guide walks you through provisioning your creative studio workspace, configuring your legal GST credentials, inviting team members, and creating your first client booking.

---

## Step 1: First-Time Login

Navigate to your workspace portal at \`http://cora.local/workspace/login\` (or \`https://app.heycora.in/workspace/login\` in production).

**Default Credentials for Local Staging:**
- **Email**: \`owner.studio@cora.local\`
- **Username**: \`studio_owner\`
- **Password**: \`cora_secure_pass_123\`

Once authenticated, you will land directly in the unified **Studio Admin Dashboard**.

---

## Step 2: Studio Profile & GSTIN

Open the **Settings Suite** (\`/workspace/settings-suite\`):

1. **Studio Identity**:
   - Set **Studio Display Name** (e.g. *Apex Cinema & Photo Works*).
   - Upload your high-resolution dark and light logos (PNG/SVG, recommended 512x512).
2. **Tax & Legal Information**:
   - Enter your 15-character **GSTIN** (e.g. \`27AAACG0000A1Z5\`).
   - Select your Home State code for automated CGST/SGST vs. IGST tax math.
   - Enter your primary bank details (Bank Name, Account Number, IFSC Code) for automatic invoice generation.

---

## Step 3: Team Members & RBAC

Navigate to **Team & Roles** (\`/workspace/team-roles\`):

1. Click **+ Add Team Member**.
2. Enter Name, Email, and assign a role:
   - **Administrator**: Full operational access (Bookings, Invoices, Contracts, CRM).
   - **Crew / Photographer**: Call sheets, schedule calendar, and assigned shoot tasks.
   - **Finance / Accounts**: Invoicing, receipts, and GST math.
3. The system generates an instant magic-invite link with custom role permissions.

---

## Step 4: Create Your First Shoot Booking

1. Open **Master Calendar** (\`/workspace/bookings\`).
2. Click **+ New Shoot Booking** in the top right.
3. Fill out the slide-out drawer form:
   - **Client**: Kavya Patel (Fashion Brand Director)
   - **Shoot Title**: *Autumn Lookbook Editorial - 4K Video + Stills*
   - **Call Time**: 08:30 AM | **Wrap Time**: 05:00 PM
   - **Location**: Studio Floor B & Outdoor Terrace
   - **Crew Assigned**: Lead Cinematographer, Gaffer, 1st AC
   - **Total Commercial Fee**: ₹1,25,000 + 18% GST (Auto-calculated: ₹22,500)
4. Click **Create & Generate Contract** to automatically stage a SHA-256 digital agreement in the Document Vault.

---

## Step 5: Install Mobile PWA

Open your workspace URL in Safari (iOS) or Chrome (Android):
- **Android**: Tap **Install Cora** on the slide-in banner.
- **iOS**: Tap the Share button → **Add to Home Screen**.

You can now receive real-time push notifications for new client inquiries and call sheet updates directly on your phone.
    `,
  },
  {
    slug: 'multi-tenant-rbac',
    title: 'Multi-Tenant RBAC & Security Matrix',
    shortTitle: 'RBAC & Permissions',
    category: 'overview',
    categoryLabel: 'Getting Started',
    description: 'Detailed security permission matrix, role inheritance rules, and audit trail enforcement for creative studios.',
    readTime: '6 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'role-hierarchy', title: '1. Role Hierarchy' },
      { id: 'permission-matrix', title: '2. 30-Point Capability Matrix' },
      { id: 'custom-roles', title: '3. Creating Custom Studio Roles' },
      { id: 'audit-logging', title: '4. Tamper-Evident Audit Trails' },
    ],
    content: `
Cora enforces a granular Role-Based Access Control (RBAC) engine that isolates tenant workspaces while giving studio directors precise control over who can view financials, edit contracts, or dispatch gear.

---

## 1. Role Hierarchy

| System Role | Role Key | Scoped Access Level |
|---|---|---|
| **Super Admin** | \`cora_super_admin\` | Global infrastructure, server health, multi-tenant billing, and system backups. |
| **Studio Owner** | \`owner\` | Full root ownership of tenant workspace, role creation, and API key management. |
| **Studio Administrator** | \`administrator\` | Operational CRUD across CRM, Bookings, Contracts, Invoicing, and Media. |
| **Crew / Manager** | \`cora_manager\` | Call sheets, shoot schedules, gear checkouts, and task completion. |
| **External Client** | *(Token-Based)* | Magic-link access to public galleries, e-sign agreements, and review portals. |

---

## 2. 30-Point Capability Matrix

Each role inherits specific capabilities evaluated server-side in PHP via \`cora_user_can($capability)\`:

\`\`\`php
// Example: Checking permission before rendering financial data
if ( ! cora_user_can( 'cora_manage_financials' ) ) {
    wp_send_json_error( array( 'message' => 'Unauthorized financial access.' ), 403 );
}
\`\`\`

### Key Capabilities

- \`cora_view_dashboard\`: Access main analytics overview.
- \`cora_manage_bookings\`: Create and reschedule shoot calendar bookings.
- \`cora_manage_financials\`: Generate GST tax invoices, record payments, and export ledger.
- \`cora_manage_vault\`: Upload and send legally binding SHA-256 contracts.
- \`cora_dispatch_crew\`: Generate and send automated WhatsApp call sheets.
- \`cora_manage_gear\`: Check out camera lenses and log maintenance condition reports.
- \`cora_manage_ai_settings\`: Configure Gemini API keys and RAG memory contexts.

---

## 3. Creating Custom Studio Roles

Studio owners can create specialized roles (e.g. *Post-Production Lead*, *Junior Gaffer*, *Retainer Accountant*) in **Settings Suite → Roles Matrix**:

1. Click **+ Create Custom Role**.
2. Select parent template to inherit baseline permissions.
3. Toggle individual granular permissions (e.g. Enable *Media Hub Access* while disabling *Financial Invoicing*).
4. Save to apply instantly across all active sessions.

---

## 4. Tamper-Evident Audit Trails

Every state mutation in the workspace (contract signature, invoice cancellation, token rotation, gear checkout) is written to \`wp_cora_audit_log\` with:
- Timestamp (UTC)
- User ID & IP Address
- Target Module & Entity ID
- Previous State vs. New State JSON diff
    `,
  },
  {
    slug: 'compliance-security',
    title: 'Legal Compliance & Security Architecture',
    shortTitle: 'Compliance & Security',
    category: 'overview',
    categoryLabel: 'Getting Started',
    description: 'Indian Information Technology Act 2000 digital signature compliance, SHA-256 integrity hashing, and data encryption standards.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    badge: 'Legal Tech',
    toc: [
      { id: 'it-act-compliance', title: '1. Indian IT Act 2000 Compliance' },
      { id: 'cryptographic-integrity', title: '2. Cryptographic SHA-256 Hashing' },
      { id: 'data-protection', title: '3. Data Protection & Tenant Isolation' },
      { id: 'audit-readiness', title: '4. Statutory Audit Readiness' },
    ],
    content: `
Security and legal enforceability form the foundation of Cora Studio OS. All contracts, invoices, and financial records are engineered to withstand scrutiny in Indian courts and statutory audits.

---

## 1. Indian IT Act 2000 Compliance

All signatures captured in Cora comply with Sections 4 and 5 of the **Indian Information Technology Act, 2000** for electronic contract execution:
- **Signer Verification**: Signer IP address, browser user-agent, and email magic token are immutably bound to the record.
- **Date & Call-Time Stamp**: Exact execution date and time (IST) are stamped on every document page.
- **Non-Repudiation**: The signer must explicitly check clause checkboxes and sign the HTML5 canvas before the certificate is sealed.

---

## 2. Cryptographic SHA-256 Hashing

When an agreement is executed, the server renders a PDF and calculates its unique SHA-256 checksum:

\`\`\`
Original PDF Bytes ──> [ SHA-256 Hash Engine ] ──> e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855
\`\`\`

If even a single character or metadata byte is altered post-signature, the checksum validation fails immediately.

---

## 3. Data Protection & Tenant Isolation

- **SSL/TLS 1.3**: All traffic in transit is encrypted using modern TLS 1.3 ciphers.
- **SQL Parameterization**: Every database interaction uses parameterized prepared statements via \`$wpdb->prepare()\`.
- **Zero Third-Party Trackers**: No third-party ad pixels or tracking scripts operate inside the private workspace SaaS shell.
    `,
  },

  // ════════════════════════════════════════════════════════════════════════
  // ── 2. INTELLIGENCE & AUTONOMOUS AI SUITE ──────────────────────────────
  // ════════════════════════════════════════════════════════════════════════
  {
    slug: 'ai-cofounder',
    title: 'AI Co-Founder: Autonomous Studio Agent',
    shortTitle: 'AI Co-Founder',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    description: 'Autonomous co-founder agent for daily operations triage, automatic quote generation, and LLM reasoning.',
    readTime: '6 min read',
    lastUpdated: 'August 2026',
    badge: 'Flagship AI',
    toc: [
      { id: 'agent-capabilities', title: '1. Agent Capabilities' },
      { id: 'conversational-triage', title: '2. WhatsApp-Style Founder Triage' },
      { id: 'token-quotas', title: '3. Token Quotas & AI Credit Tracking' },
      { id: 'llm-backends', title: '4. Gemini 3.5 & Claude Sonnet Backends' },
    ],
    content: `
The **Cora AI Co-Founder** is a persistent, context-aware autonomous agent designed to act as your studio's chief operating officer. It handles inbound lead qualification, drafts custom commercial proposals, calculates production budgets, and triages daily operational bottlenecks.

---

## 1. Agent Capabilities

- **Automatic Scope Generation**: Ingests unstructured client emails and audio voice notes to output structured production scopes.
- **Budget & Rate Card Math**: Calculates crew day rates, equipment rental line items, and 18% GST margins.
- **Conflict Resolution**: Identifies crew availability overlaps and recommends alternate shooting dates.
- **Contract Drafting**: Automatically populates standard Indian IT Act 2000 service agreements with client details.

---

## 2. WhatsApp-Style Founder Triage

The AI Co-Founder features a WhatsApp-style conversational chat interface:
- **Short-Bubble Responses**: Answers are constrained to 2–3 punchy sentences per message for rapid founder decision-making on mobile.
- **RCS Checklist Cards**: Action items (e.g. *"Approve ₹85,000 quote for Lookbook shoot"*) are rendered as interactive action cards with one-tap approval buttons.
- **Zero Hallucination**: Strict fallback to your studio's configured rate cards and RAG memory knowledge base.

---

## 3. Token Quotas & AI Credit Tracking

AI execution is tracked per session with a real-time progress meter:
- **Starter Plan**: 100,000 monthly RAG tokens.
- **Pro Plan**: 1,000,000 monthly RAG tokens.
- **Studio Enterprise**: Unlimited custom model context tokens.

Credits sync in real-time between local browser storage and the backend MySQL accounting ledger.

---

## 4. Gemini 3.5 & Claude Sonnet Backends

You can switch the underlying reasoning engine in the sticky administrator popover:
- **Gemini 3.5 Flash**: Optimized for sub-second speed, audio transcription, and image analysis.
- **Claude 3.5 Sonnet**: Optimized for complex multi-party commercial contracts and long-form narrative scriptwriting.
    `,
  },
  {
    slug: 'content-ai-seo',
    title: 'Content AI & Generative Engine Optimization (GEO)',
    shortTitle: 'Content AI & GEO',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    description: 'Generate viral video scripts, social captions, and optimize website pages for AI search engine citations.',
    readTime: '6 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'script-generator', title: '1. Viral Video Script Generator' },
      { id: 'geo-inspector', title: '2. GEO Citation & Density Audit' },
      { id: 'publishing-calendar', title: '3. Multi-Channel Social Calendar' },
      { id: 'google-business-sync', title: '4. Google Business Profile Sync' },
    ],
    content: `
The **Content AI & GEO Suite** unites high-conversion marketing copywriting with next-generation search engine optimization designed for both traditional Google SERPs and modern AI answer engines (ChatGPT Search, Perplexity, Gemini).

---

## 1. Viral Video Script Generator

Generate 3-act TikTok/Reel video scripts formatted specifically for short-form retention:
- **Hook (0–3s)**: High-curiosity visual pattern interrupt.
- **Body / Value (3–45s)**: Fast-paced narrative beats with B-roll shot list instructions.
- **Call-to-Action (45–60s)**: High-converting lead magnet or booking invitation.

---

## 2. GEO Citation & Density Audit

Traditional SEO is no longer sufficient. Cora’s **GEO Inspector** scores pages against LLM retrieval benchmarks:
- **Answer-Style Density**: Measures the ratio of direct, fact-rich answers to conversational filler.
- **Entity Knowledge Graph Alignment**: Checks for schema.org structured JSON-LD data (LocalBusiness, Service, Review).
- **Question-Style H2 Subheadings**: Rewrites flat headings into conversational user query formulations.

---

## 3. Multi-Channel Social Calendar

Schedule and preview posts across Instagram, LinkedIn, and WordPress with brand voice continuity preserved across all channels.
    `,
  },
  {
    slug: 'rag-memory-mcp',
    title: 'RAG Memory & Model Context Protocol (MCP)',
    shortTitle: 'RAG Memory & MCP',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    description: 'Living studio context memory with keyword-dense semantic retrieval and bidirectional Model Context Protocol (MCP) gateway.',
    readTime: '7 min read',
    lastUpdated: 'August 2026',
    badge: 'Developer Protocol',
    toc: [
      { id: 'rag-architecture', title: '1. RAG Memory Architecture' },
      { id: 'ingestion-sources', title: '2. Dynamic Knowledge Ingestion' },
      { id: 'mcp-gateway', title: '3. MCP JSON-RPC 2.0 Gateway' },
      { id: 'token-auth', title: '4. Bearer Token Security' },
    ],
    content: `
Cora transforms static studio data into a living intelligence layer via Retrieval-Augmented Generation (RAG) and open Model Context Protocol (MCP) server endpoints.

---

## 1. RAG Memory Architecture

Unlike complex external vector database clusters that introduce latency, Cora uses a self-contained keyword-dense semantic chunking engine stored in MySQL/SQLite:

1. **Ingestion**: Uploaded PDFs, rate cards, and client history are split into 512-token chunks.
2. **Indexing**: Chunks are indexed with semantic entity tags in \`wp_cora_rag_knowledge\`.
3. **Retrieval**: When an AI prompt is executed, the top 5 most relevant studio context chunks are automatically injected into the LLM system prompt.

---

## 2. MCP JSON-RPC 2.0 Gateway

Cora acts as an **MCP Server**, allowing external desktop IDEs (Cursor, Windsurf, Claude Desktop, Antigravity) to query workspace data directly over WebSockets and REST:

\`\`\`json
{
  "jsonrpc": "2.0",
  "method": "tools/call",
  "params": {
    "name": "cora_get_shoot_schedule",
    "arguments": {
      "date": "2026-08-30"
    }
  },
  "id": 1
}
\`\`\`

### Available MCP Tools

- \`cora_query_leads\`: Search active CRM deals and stage milestones.
- \`cora_create_invoice\`: Generate 18% GST invoice PDF.
- \`cora_check_gear_availability\`: Query inventory catalog for camera/lens conflicts.
- \`cora_dispatch_call_sheet\`: Send WhatsApp notification to crew members.
    `,
  },
  {
    slug: 'voice-to-scope',
    title: 'Voice-to-Scope Audio Brief Transcription',
    shortTitle: 'Voice-to-Scope',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    description: 'Convert voice recordings and audio WhatsApp briefs into formal client scopes of work, deliverables checklists, and GST estimates.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'how-it-works', title: '1. How It Works' },
      { id: 'audio-upload', title: '2. Uploading Audio Briefs' },
      { id: 'structured-extraction', title: '3. Structured Extraction Schema' },
    ],
    content: `
Creative clients frequently send chaotic 5-minute audio voice notes instead of written briefs. **Voice-to-Scope** uses Gemini Multimodal Audio to instantly convert spoken briefings into structured production contracts.

---

## 1. How It Works

1. **Upload / Record**: Drop an \`.mp3\`, \`.m4a\`, or \`.wav\` audio file into the workspace dropzone.
2. **Multimodal Analysis**: Audio frequencies are processed natively without lossy intermediate text transcriptions.
3. **Structured Scope Extraction**: The model outputs:
   - Shoot Objectives & Deliverables (e.g. 5x Reels + 20x Retouched Stills).
   - Tentative Dates & Locations.
   - Crew Requirements (e.g. Drone Pilot, HMUA, Sound Recordist).
   - Commercial Budget & Payment Milestones.
    `,
  },

  // ════════════════════════════════════════════════════════════════════════
  // ── 3. SALES, FUNNELS & CRM ────────────────────────────────────────────
  // ════════════════════════════════════════════════════════════════════════
  {
    slug: 'lead-crm',
    title: 'Kanban Lead CRM & Pipeline Automation',
    shortTitle: 'Kanban CRM',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    description: 'Track incoming shoot inquiries, manage visual deal pipelines, and trigger automated WhatsApp follow-ups.',
    readTime: '6 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'pipeline-stages', title: '1. Visual Pipeline Stages' },
      { id: 'whatsapp-automation', title: '2. Automated WhatsApp Outreach' },
      { id: 'lead-scoring', title: '3. AI Intent Lead Scoring' },
    ],
    content: `
The Cora **Kanban Lead CRM** gives creative studios a Notion-style drag-and-drop deal board tailored for high-ticket commercial shoots, retainer contracts, and wedding bookings.

---

## 1. Visual Pipeline Stages

By default, every new workspace seeds with five industry-standard pipeline stages:

1. **New Inquiries**: Incoming submissions from web forms, Instagram DMs, and WhatsApp.
2. **Briefing / Discovery**: Active scoping and call sheet planning.
3. **Proposal & Quote Sent**: Staged SHA-256 agreement with GST breakdown.
4. **Contract Signed & Advance Paid**: 50% advance invoice settled.
5. **Shoot Scheduled**: Transferred directly to Master Calendar and Crew Dispatch.
    `,
  },
  {
    slug: 'canvas-builder',
    title: 'Funnel Canvas Builder & Web Creator',
    shortTitle: 'Canvas Builder',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    description: 'Create high-converting landing pages, portfolio showreels, and booking funnels with clean monochromatic styling.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'visual-editor', title: '1. Visual Editor Toolbar' },
      { id: 'white-labeling', title: '2. Zero Third-Party Branding' },
      { id: 'git-sync', title: '3. Version-Controlled Theme Sync' },
    ],
    content: `
The **Canvas Builder** enables creative agencies to design and publish lightning-fast client landing pages, portfolio showcases, and lead-capture funnels directly inside the Cora workspace.
    `,
  },
  {
    slug: 'form-builder',
    title: 'Visual Forms Builder & Lead Capture',
    shortTitle: 'Forms Builder',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    description: 'No-code drag-and-drop form creation system for client questionnaires, shoot booking briefs, and GST collection.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'field-types', title: '1. Supported Field Types' },
      { id: 'conditional-logic', title: '2. Conditional Flow Logic' },
      { id: 'crm-piping', title: '3. Direct CRM Lead Piping' },
    ],
    content: `
Create custom embeddable forms with conditional branching, date slot selection, and direct piping into your Kanban Lead CRM.
    `,
  },
  {
    slug: 'review-portal',
    title: '5★ Review Acquisition Portal',
    shortTitle: 'Review Acquisition',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    description: 'Automate post-shoot review collection with intelligent sentiment filtering and direct Google Business Profile synchronization.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'sentiment-funnel', title: '1. Smart Sentiment Funnel' },
      { id: 'google-sync', title: '2. Google Business Profile Sync' },
      { id: 'whatsapp-invitations', title: '3. WhatsApp Review Invitations' },
    ],
    content: `
Automate 5-star Google review collection. When a client rates their experience 4 or 5 stars, they are seamlessly guided to Google Maps. Negative feedback (1–3 stars) is captured privately for internal resolution.
    `,
  },

  // ════════════════════════════════════════════════════════════════════════
  // ── 4. OPERATIONS & LEGAL ──────────────────────────────────────────────
  // ════════════════════════════════════════════════════════════════════════
  {
    slug: 'esign-vault',
    title: 'SHA-256 E-Sign Vault & Legal Contracts',
    shortTitle: 'E-Sign Vault',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    description: 'Legally binding digital contracts with 5-step guided wizard, GST verification, canvas signatures, and Indian IT Act 2000 compliance.',
    readTime: '6 min read',
    lastUpdated: 'August 2026',
    badge: 'Legal Tech',
    toc: [
      { id: '5-step-wizard', title: '1. 5-Step Guided Signing Wizard' },
      { id: 'it-act-compliance', title: '2. Indian IT Act 2000 Compliance' },
      { id: 'sha256-hash', title: '3. SHA-256 Checksums & Watermarks' },
      { id: 'pdf-generation', title: '4. Server-Side PDF Rendering' },
    ],
    content: `
The **Document Vault** is Cora's legal and compliance engine. It eliminates paper agreements and expensive third-party tools (DocuSign, PandaDoc) by providing unlimited, cryptographically verified digital contracts.

---

## 1. 5-Step Guided Signing Wizard

Clients sign agreements through a smooth, progressive 5-step flow:

1. **Details**: Contract metadata, parties involved, shoot date, and deliverables scope.
2. **Terms**: Full scroll-to-bottom agreement terms with explicit checkbox clauses.
3. **GST Math**: Clear commercial calculation displaying Base Fee + 18% GST (CGST/SGST).
4. **E-Sign Pad**: HTML5 signature canvas with typed-name fallback and timestamp recording.
5. **Complete**: Instantly generates an encrypted, watermarked signed PDF copy for both parties.

---

## 2. Indian IT Act 2000 Compliance

All signatures captured in Cora comply with Sections 4 and 5 of the **Indian Information Technology Act, 2000** for electronic contract execution:
- Signer IP address and browser user-agent are immutably bound to the record.
- Exact date and time (IST) are stamped on every page.
- Cryptographic SHA-256 hash guarantees the document has not been altered post-signature.
    `,
  },
  {
    slug: 'crew-dispatch',
    title: 'Crew & Gear Dispatch Call Sheets',
    shortTitle: 'Crew Dispatch',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    description: 'Visual shift scheduling, double-booking conflict detection, and automated WhatsApp call sheet dispatch.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'call-sheet-builder', title: '1. Automated Call Sheet Builder' },
      { id: 'conflict-detection', title: '2. Conflict Detection Engine' },
      { id: 'whatsapp-broadcast', title: '3. One-Tap WhatsApp Broadcast' },
    ],
    content: `
Generate professional call sheets with call times, location maps, shoot agendas, gear checklists, and weather forecasts, then dispatch them to crew via WhatsApp with one click.
    `,
  },
  {
    slug: 'master-calendar',
    title: 'Master Shoot Calendar & Booking Engine',
    shortTitle: 'Master Calendar',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    description: 'Centralized multi-location calendar for shoot bookings, equipment reservations, and crew availability.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'calendar-views', title: '1. Multi-Day & Month Views' },
      { id: 'location-filtering', title: '2. Multi-Location Filtering' },
      { id: 'ical-sync', title: '3. Google & Apple Calendar Sync' },
    ],
    content: `
Manage all upcoming productions across multiple studios and outdoor locations with real-time status indicators and two-way calendar synchronization.
    `,
  },
  {
    slug: 'task-board',
    title: 'Task Board & Deliverable Milestones',
    shortTitle: 'Task Board',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    description: 'Post-production deliverable tracking, client review links, and async threaded commenting.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'post-production-flow', title: '1. Post-Production Milestone Flow' },
      { id: 'client-proofing-links', title: '2. Client Proofing Magic Links' },
      { id: 'threaded-comments', title: '3. Threaded Timecode Comments' },
    ],
    content: `
Track post-production status from RAW ingest to rough cut, color grading, client revisions, and final master delivery.
    `,
  },

  // ════════════════════════════════════════════════════════════════════════
  // ── 5. FINANCE & ASSET MANAGEMENT ──────────────────────────────────────
  // ════════════════════════════════════════════════════════════════════════
  {
    slug: 'gst-invoicing',
    title: '18% GST Invoicing & Tax Math Engine',
    shortTitle: '18% GST Invoicing',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    description: 'Automated CGST/SGST/IGST splitting, HSN/SAC code registry, pro-forma invoices, and payment reconciliation.',
    readTime: '6 min read',
    lastUpdated: 'August 2026',
    badge: 'India Compliant',
    toc: [
      { id: 'tax-math', title: '1. Automated GST Tax Splitting' },
      { id: 'hsn-sac-registry', title: '2. HSN & SAC Code Registry' },
      { id: 'invoice-pdf', title: '3. Compliant PDF Tax Invoices' },
    ],
    content: `
The Cora **GST Math Engine** handles all tax calculations required for Indian service businesses and commercial studios:

- **Intra-State (Same State)**: Splits 18% GST into **9% CGST** + **9% SGST**.
- **Inter-State (Different State)**: Applies unified **18% IGST**.
- **SAC Codes**: Pre-seeded with SAC 998381 (Commercial Photography & Film Production Services).
- **Pro-Forma & Tax Invoices**: Generate 50% advance pro-formas and final tax invoices with instant payment links.
    `,
  },
  {
    slug: 'asset-gear',
    title: 'Gear & Inventory Equipment Registry',
    shortTitle: 'Gear & Inventory',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    description: 'Catalog cameras, lenses, lighting kits, track serial numbers, condition logs, and booking checkouts.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'inventory-registry', title: '1. Gear Catalog & Serial Numbers' },
      { id: 'checkout-tracking', title: '2. Shoot Checkout Tracking' },
      { id: 'condition-logs', title: '3. Maintenance & Damage Logs' },
    ],
    content: `
Maintain full tracking of your physical production assets, prevent double-booking of specialty lenses, and log maintenance repair histories.
    `,
  },
  {
    slug: 'media-hub',
    title: 'Media Hub & 4K RAW Proofing Gallery',
    shortTitle: 'Media Hub & RAW',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    description: 'Workspace-scoped asset library with Gemini AI image tagging, client gallery share links, and in-browser image editor.',
    readTime: '6 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'ai-tagging', title: '1. Gemini Vision AI Tagging' },
      { id: 'client-galleries', title: '2. White-Labeled Client Galleries' },
      { id: 'media-editor', title: '3. In-Browser Canvas Media Editor' },
    ],
    content: `
Deliver curated photo and video collections to your clients via secure, password-protected gallery portals with one-click download controls.
    `,
  },
  {
    slug: 'deal-simulator',
    title: 'Deal Feasibility & Margin Simulator',
    shortTitle: 'Deal Simulator',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    description: 'Evaluate shoot profitability against subcontractor fees, equipment rentals, travel, and 18% GST reserves before signing.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'scoring-model', title: '1. Net Profit & Margin % Scoring' },
      { id: 'ai-verdicts', title: '2. AI Feasibility Verdicts' },
      { id: 'invoice-conversion', title: '3. 1-Click Invoice Conversion' },
    ],
    content: `
The **Deal Feasibility Simulator** calculates true take-home bottom-line profit before you commit to a commercial shoot:

- **Net Profit & Margin %**: Subtracts equipment rentals, assistant wages, catering, and 18% GST reserves.
- **AI Feasibility Verdict**: Flags low-margin quotes (< 30%) and gives actionable recommendations.
- **1-Click Conversion**: Pushes approved feasibility numbers directly into a staged client invoice.
    `,
  },
  {
    slug: 'finance-reports',
    title: 'Tax, Advance GST & CA Accountant Pack',
    shortTitle: 'Accountant Pack',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    description: 'Automate quarterly advance tax reserves, Input Tax Credit (ITC) reconciliation, and 1-click CA audit export packs.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'itc-reconciliation', title: '1. Output vs Input GST Reconciliation' },
      { id: 'advance-tax', title: '2. Advance Income Tax Allocation' },
      { id: 'ca-export', title: '3. 1-Click CA Audit Pack Export' },
    ],
    content: `
The Financials module provides complete visibility into tax obligations and streamlines end-of-quarter handoffs to your Chartered Accountant:

- **ITC Calculation**: Matches GST collected on client invoices against GST paid on gear rentals and studio expenses.
- **Advance Tax Reserve**: Suggests 15% quarterly income tax allocations to avoid interest penalties.
- **CA Export Pack**: Generates a clean ZIP containing itemized sales, purchase, and tax ledgers formatted for statutory audit.
    `,
  },

  // ════════════════════════════════════════════════════════════════════════
  // ── 6. DEVELOPER APIS, PWA & CONNECTORS ────────────────────────────────
  // ════════════════════════════════════════════════════════════════════════
  {
    slug: 'pwa-architecture',
    title: 'PWA Architecture & Service Worker Lifecycle',
    shortTitle: 'PWA & Service Worker',
    category: 'developers',
    categoryLabel: 'Developer APIs & PWA',
    description: 'Progressive Web App specifications, dynamic manifest configuration, sub-50ms cache lifecycle, and VAPID push.',
    readTime: '6 min read',
    lastUpdated: 'August 2026',
    badge: 'Mobile Protocol',
    toc: [
      { id: 'manifest-config', title: '1. Dynamic PWA Manifest' },
      { id: 'service-worker', title: '2. Service Worker Lifecycle' },
      { id: 'vapid-push', title: '3. VAPID Push Implementation' },
    ],
    content: `
Cora ships with native Progressive Web App (PWA) capabilities, offering sub-50ms screen painting and native-like offline reliability.

---

## 1. Dynamic PWA Manifest

Served dynamically at \`/cora-manifest.json\`:
- \`display\`: \`standalone\`
- \`theme_color\`: \`#ffffff\` (Pure Light Mode splash to eliminate dark flashes)
- Dynamic version stamps (\`?v=CORA_WORKSPACE_VERSION\`) to trigger immediate OS icon updates on release.

---

## 2. Service Worker Lifecycle

Registered at root scope (\`/\`):
- **Cache-First**: Static CSS/JS and font assets.
- **Network-First**: AJAX actions and REST endpoints with sub-400ms timeout.
- **Auto-Activation**: \`skipWaiting()\` and old version cache purging executed on update.
    `,
  },
  {
    slug: 'rest-api-reference',
    title: 'REST API & AJAX Endpoint Reference',
    shortTitle: 'REST API Reference',
    category: 'developers',
    categoryLabel: 'Developer APIs & PWA',
    description: 'Complete API reference for bookings, leads, invoices, e-sign contracts, and PWA push subscriptions.',
    readTime: '8 min read',
    lastUpdated: 'August 2026',
    badge: 'API Spec',
    toc: [
      { id: 'auth', title: '1. Authentication & Nonces' },
      { id: 'crm-endpoints', title: '2. CRM & Lead Endpoints' },
      { id: 'invoicing-endpoints', title: '3. Invoicing & GST Endpoints' },
      { id: 'pwa-endpoints', title: '4. PWA Push Endpoints' },
    ],
    content: `
All endpoints are accessible via WordPress REST API (\`/wp-json/cora/v1/\` or \`/wp-json/cora-pwa/v1/\`) and standard AJAX actions.

---

## 1. Authentication & Nonces

- **REST Requests**: Require \`X-WP-Nonce\` header or Bearer API token.
- **AJAX Requests**: Require \`nonce: coraREWPData.ajaxNonce\` in POST body.

---

## 2. API Endpoints Table

| Method | Route / Action | Required Permission | Description |
|---|---|---|---|
| \`GET\` | \`/wp-json/cora/v1/leads\` | \`cora_view_dashboard\` | Fetch paginated leads list |
| \`POST\` | \`/wp-json/cora/v1/leads\` | \`cora_manage_bookings\` | Create new shoot lead |
| \`POST\` | \`/wp-json/cora/v1/invoices\` | \`cora_manage_financials\` | Create 18% GST invoice |
| \`POST\` | \`/wp-json/cora/v1/contracts/sign\` | Public (Token-Based) | Submit SHA-256 e-signature |
| \`POST\` | \`/wp-json/cora-pwa/v1/save-subscription\` | Authenticated User | Store VAPID Web Push subscription |
| \`GET\` | \`/wp-json/cora-pwa/v1/version-check\` | Public | Query active workspace version & changelog |
    `,
  },
  {
    slug: 'mcp-gateway',
    title: 'Model Context Protocol (MCP) Server Gateway',
    shortTitle: 'MCP Gateway',
    category: 'developers',
    categoryLabel: 'Developer APIs & PWA',
    description: 'Connect external AI IDEs and autonomous LLM agents (Cursor, Windsurf, Claude Desktop) directly to your studio workspace.',
    readTime: '6 min read',
    lastUpdated: 'August 2026',
    badge: 'Developer Protocol',
    toc: [
      { id: 'mcp-protocol', title: '1. Model Context Protocol Overview' },
      { id: 'configuration', title: '2. IDE Configuration (claude_desktop_config.json)' },
      { id: 'tools-catalog', title: '3. Exposed Workspace Tool Registry' },
    ],
    content: `
The **Cora MCP Gateway** exposes your studio database as structured Model Context Protocol (MCP) tools so external AI coding assistants can query calendar availability, generate quotes, and create invoices.

---

## 1. Configuration

Add the following to your \`claude_desktop_config.json\` or Cursor MCP settings:

\`\`\`json
{
  "mcpServers": {
    "cora-studio": {
      "command": "npx",
      "args": ["-y", "@heycora/mcp-server"],
      "env": {
        "CORA_WORKSPACE_URL": "https://app.heycora.in",
        "CORA_API_TOKEN": "YOUR_WORKSPACE_BEARER_TOKEN"
      }
    }
  }
}
\`\`\`
    `,
  },
  {
    slug: 'website-connectors',
    title: 'Website Connectors: Framer, Webflow & WordPress',
    shortTitle: 'Website Connectors',
    category: 'developers',
    categoryLabel: 'Developer APIs & PWA',
    description: 'Embed client portals, e-sign agreements, and lead capture forms on Framer, Webflow, and custom websites.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'framer-embed', title: '1. Framer 1-Line Embed' },
      { id: 'webflow-webhooks', title: '2. Webflow Webhook Integration' },
      { id: 'wordpress-plugin', title: '3. WordPress Connector' },
    ],
    content: `
Connect your existing website or portfolio directly to your Cora backend with a single script embed or webhook URL.

---

## 1. Framer 1-Line Embed

Add this script tag to your Framer project settings to embed live booking calendars, GST calculators, or contact forms:

\`\`\`html
<script 
  src="https://heycora.in/tools/embed.js" 
  data-workspace="YOUR_STUDIO_ID" 
  async>
</script>
\`\`\`
    `,
  },
];

export const DOC_CATEGORIES: DocCategory[] = [
  {
    id: 'overview',
    label: 'Getting Started',
    iconName: 'Compass',
    articles: DOCS_DATA.filter((d) => d.category === 'overview'),
  },
  {
    id: 'intelligence',
    label: 'Intelligence & AI',
    iconName: 'Bot',
    articles: DOCS_DATA.filter((d) => d.category === 'intelligence'),
  },
  {
    id: 'sales',
    label: 'Sales & CRM',
    iconName: 'Users',
    articles: DOCS_DATA.filter((d) => d.category === 'sales'),
  },
  {
    id: 'operations',
    label: 'Operations & Legal',
    iconName: 'ShieldCheck',
    articles: DOCS_DATA.filter((d) => d.category === 'operations'),
  },
  {
    id: 'finance',
    label: 'Finance & Assets',
    iconName: 'Receipt',
    articles: DOCS_DATA.filter((d) => d.category === 'finance'),
  },
  {
    id: 'developers',
    label: 'Developer APIs & PWA',
    iconName: 'Code',
    articles: DOCS_DATA.filter((d) => d.category === 'developers'),
  },
];
