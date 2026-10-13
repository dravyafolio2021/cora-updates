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
    description: 'Comprehensive architectural guide to Cora’s high-performance PHP framework backend runtime, Next.js 16 SSG edge layer, and isolated MySQL schemas.',
    readTime: '7 min read',
    lastUpdated: 'August 2026',
    badge: 'Core',
    toc: [
      { id: 'system-overview', title: '1. System Overview' },
      { id: 'routing-layer', title: '2. Hybrid Routing & Subdomains' },
      { id: 'database-isolation', title: '3. Multi-Tenant Database Isolation' },
      { id: 'caching-performance', title: '4. LiteSpeed & PWA Caching' },
      { id: 'security-model', title: '5. Zero-Trust Security Model' },
      { id: 'marketing-discovery', title: '6. Marketing Reform & Discovery Layer' },
      { id: 'ai-sdr-engine', title: '7. Sticky AI SDR Sales Assistant' },
      { id: 'template-ecosystem', title: '8. 4-Tier Template & Asset Ecosystem' },
      { id: 'forms-all-entries', title: '9. Forms Architect & Universal All Entries' },
      { id: 'entry-inspector-timezone', title: '10. Edge-Docked Inspector & Timezone Engine' },
      { id: 'affiliate-referral-discounts', title: '11. Dynamic Referral Tracking & Checkout Discounts' },
      { id: 'industries-canonical-directory', title: '12. Canonical Industry Solutions & 301 Redirects' },
      { id: 'ai-workforce-marquee', title: '13. Autonomous AI Workforce & Infinite Skills Marquee' },
      { id: 'features-lenis-isolation', title: '14. Features Catalog & Lenis Scroll Boundary Isolation' },
      { id: 'brand-registry-svg-kit', title: '15. Enterprise Brand Registry & Turnkey SVG Kit' },
    ],
    content: `
Cora is engineered as an enterprise-grade autonomous operating system for Indian creative studios and agencies. It operates on a **hybrid decoupled architecture** where a high-performance Next.js marketing and documentation layer is paired with a resilient, high-performance PHP 8.2 framework backend runtime.

---

## 1. System Overview

The Cora platform consists of four primary decoupled layers:

1. **Next.js Static/SSG Front-End (\`heycora.in\`)**:
   - Built on Next.js 16 (App Router) with Turbopack.
   - Deployed on LiteSpeed Web Server as pre-rendered static HTML/CSS/JS.
   - Sub-50ms Time-To-First-Byte (TTFB) globally with zero server compute overhead.
   - Houses the public website, interactive comparison benchmarks, calculator tools, and Developer Documentation Hub (\`/docs\`).

2. **Enterprise PHP Application Engine (\`app.heycora.in\` & \`cora.local\`)**:
   - Single, consolidated backend workspace engine (\`cora-workspace\`) containing all 20+ operational modules.
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
│  Next.js 16 SSG Frontend    │ │  PHP Framework / LiteSpeed  │
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
| \`app.heycora.in\` | PHP Framework Backend | \`cora-workspace.php\` | Production SaaS Tenant SPA |
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
 
---

## 6. Marketing Reform & Discovery Layer

Cora bridges client-facing website generation and private agency management into a seamless experience:
- **Homepage Positioning**: *"Build websites. Run your business, with an AI co-founder."*
- **Dual Conversion Engine**: Primary "Sign in with Google" OAuth for rapid workspace creation, and secondary "Explore free templates" routing directly into the 16+ turnkey pre-seeded schemas at \`/industries\`.
- **Tools & Templates Architecture**: The marketing navigation elevates free business calculators (18% GST calculator, retainer margin tool, proposal generator) alongside production website and workspace templates with instant lead capture bridge into \`wp_cora_leads\`.

---

## 7. Sticky AI SDR Sales Assistant

Mounted via React Portal (\`createPortal\`) directly into the document root:
- **True Viewport Docking**: Floats centered at the bottom viewport edge with zero backdrop overlay, avoiding parent container clipping or scroll interference.
- **Objection Handling Vectors**: Addresses platform switching concerns (e.g. HoneyBook/Notion vs Cora, free white-glove site migrations, annual discount codes, and multi-plugin elimination).
- **Dynamic Conversion Coupons**: Automatically generates actionable coupon cards (\`MIGRATEFREE\`, \`CORA10\`) with direct checkout links pre-populating discount parameters into \`/pricing\`.

---

## 8. 4-Tier Template & Asset Ecosystem

The \`/templates\` directory provides 16+ production-grade frontend assets across 4 distinct formats:
1. **Native Landing Pages (\`native_page\`)**: Pure Tailwind CSS and Next.js 15 pages featuring sub-50ms paint times and accessible components.
2. **AI Section Prompts (\`section_prompts\`)**: Token-engineered prompts for Claude 3.5 Sonnet and Cursor Composer.
3. **Elementor Template Kits (\`elementor_kit\`)**: Standardized Elementor JSON blueprints for instant page builder import.
4. **Lovable & React Projects (\`lovable_project\`)**: Rapid full-stack scaffolding configurations for Lovable.dev and v0.dev.

---

## 9. Forms Architect & Universal All Entries Registry (v4.9.372)

- **Ergonomic Form Cards**: Overhauled card padding (\`p-5 sm:p-6\`), clickable card surface navigating to form editor, and unlocked mobile form editing.
- **In-App Live Preview Modal**: Sandboxed viewport simulator (\`Desktop\`, \`Mobile\`) with direct embed URL launcher.
- **Universal 5th Tab (\`#all-entries\`)**: Unified submissions repository with real-time KPI counter cards, Notion-style multi-form filter toolbar, fixed column width table preventing layout blowout, and multi-form dynamic CSV exports.

---

## 10. Edge-Docked Inspector & Timezone Engine (v4.9.374)

- **Edge Docking**: Slides smoothly from right edge on desktop (\`w-[480px] max-w-[90vw]\`) and bottom sheet on mobile (\`h-[85vh]\`) with zero backdrop blur, keeping background records completely visible.
- **Data Rendering**: Renders inline digital signatures and image thumbnails (\`<img src="...">\`) rather than dumping 50,000-character raw text blobs.
- **Client Timezone Engine**: Automatically evaluates browser timezone using \`Intl.DateTimeFormat().resolvedOptions().timeZone\` to render human-readable relative and absolute timestamps.

---

## 11. Dynamic Referral Tracking & Checkout Discounts (v4.9.374)

- **Attribution API**: Dedicated REST endpoint \`POST /wp-json/cora/v1/referrals/track\` with frontend proxy route.
- **Multi-Parameter Capture**: Ingests referral codes (\`cora_ref\`, \`ref\`, \`via\`, \`partner\`) and stores 30-day secure cookies.
- **Automatic Checkout Discounts**: Automatically applies 10%–20% coupon discount deductions across pricing checkout sheets with dual conversion attribution to Endorsely and Cora backends.

---

## 12. Canonical Industry Solutions & 301 Redirects (v4.9.380)

- **Directory Migration**: Migrated primary industry hub from \`/use-cases\` to canonical \`/industries\`.
- **Backward-Compatible 301 Redirects**: Configured in \`.htaccess\` and Next.js routing, guaranteeing all legacy \`/use-cases\` links permanently redirect with 100% SEO link equity.
- **10+ Verified Vertical Schemas**: Complete plain-English active-voice directories across Real Estate, Photography, Architecture, Creative Agency, Consulting, Salons, Fitness, Events, Hospitality, and DTC E-Commerce.
- **Desktop Mega-Menu**: Multi-column dropdown in \`Navbar.tsx\` with dedicated icon tiles and subtle colored pill indicators.

---

## 13. Autonomous AI Workforce & Infinite Skills Marquee (v4.9.379)

- **Dedicated AI Agent Hub (\`/ai-agent\`)**: Visualizes Cora's multi-agent autonomous workforce.
- **Neural Portrait Blended Cutout**: Seamless high-DPI portrait cutout (\`cora_hero_agent_cutout.png\`) blended into the canvas container with responsive gradient lighting.
- **4-Row Infinite Skills Marquee**: Hardware-accelerated CSS marquee (\`InfiniteSkillsSection.tsx\`) highlighting verified capabilities across Operations, Sales, Finance, and Creative Delivery.

---

## 14. Features Catalog & Lenis Scroll Boundary Isolation (v4.9.378)

- **Directory Overhaul (\`/features\`)**: High-density directory presenting 40+ operational modules.
- **Lenis Smooth-Scroll Boundary Isolation (\`data-lenis-prevent\`)**: Encapsulates the features sidebar to isolate inner wheel and touch scroll from global Lenis page scrolling, eliminating scroll trapping.
- **High-Density Sidebar Navigation**: Single-line items with segmented status filters (*All*, *Released*, *Beta*, *Upcoming*) and zero-overlap search input icons.

---

## 15. Enterprise Brand Registry & Turnkey SVG Kit (v4.9.377)

- **Brand Asset Center (\`/brand\`)**: Complete brand guidelines, typographic hierarchy, and neutral zinc color tokens.
- **1-Click Turnkey SVG Download Kit**: Raw inline SVG clipboard copy and instant \`.svg\` file downloads for Primary, Inverted, and Symbol wordmarks.
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
    badge: 'Legal',
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
  {
    slug: 'features-catalog',
    title: 'Features Directory & Capability Matrix',
    shortTitle: 'Features Catalog',
    category: 'overview',
    categoryLabel: 'Getting Started',
    description: 'Explore Cora’s 40+ operational modules with high-density sidebar navigation and Lenis scroll boundary isolation.',
    readTime: '6 min read',
    lastUpdated: 'October 2026',
    badge: 'Features',
    toc: [
      { id: 'features-overview', title: '1. Capability Matrix Overview' },
      { id: 'sidebar-navigation', title: '2. High-Density Sidebar Navigation' },
      { id: 'lenis-isolation', title: '3. Lenis Smooth-Scroll Boundary Isolation' },
      { id: 'active-voice-cards', title: '4. Active-Voice Visual Cards & CTA' },
    ],
    content: `
The \`/features\` catalog provides an interactive, full-spectrum inspection of Cora’s 40+ core SaaS modules.

---

## 1. Capability Matrix Overview

Cora organizes its feature suite across five primary agency domains:
- **Foundations**: Multi-tenant RBAC, custom roles, single-owner governance, and audit trails.
- **Client Operations**: Visual Kanban CRM, interactive calendar, crew dispatch, and equipment custody.
- **Financial Intelligence**: GST-compliant invoicing (SAC 9983), e-sign contract vault, and margin calculators.
- **Autonomous AI**: Continuous Living Memory RAG, voice-to-scope briefings, and multi-agent workflows.
- **Website & Growth**: White-labeled Canvas builder, Next.js headless Git sync, and affiliate referral engines.

---

## 2. High-Density Sidebar Navigation

The features sidebar (\`FeaturesSidebar.tsx\`) is built for rapid discovery:
- **Single-Line Density**: Compact navigation links with vector icon glyphs and active tonal pills.
- **Segmented Status Filter**: 1-click toggling between *All*, *Released*, *Beta*, and *Upcoming* features.
- **Zero-Overlap Search**: Integrated vector SVG search input preventing layout overlapping.

---

## 3. Lenis Smooth-Scroll Boundary Isolation

To eliminate wheel-scroll trapping when browsing long module lists:
- The sidebar container is encapsulated with \`data-lenis-prevent\` and explicit viewport boundaries: \`max-h-[calc(100vh-120px)] overflow-y-auto\`.
- This ensures inner wheel and touch scroll events are consumed locally without propagating to the parent window's Lenis instance.

---

## 4. Active-Voice Visual Cards & CTA

Every module card features active-voice benefit headlines, clear operational badges, and a direct "Get Started for Free" call-to-action.
    `,
  },
  {
    slug: 'brand-registry',
    title: 'Enterprise Brand Asset Registry & SVG Kit',
    shortTitle: 'Brand Registry',
    category: 'overview',
    categoryLabel: 'Getting Started',
    description: 'Official brand guidelines, 1-click vector SVG download kit, design tokens reference, and production captures.',
    readTime: '4 min read',
    lastUpdated: 'October 2026',
    badge: 'Brand',
    toc: [
      { id: 'brand-overview', title: '1. Brand Positioning & Philosophy' },
      { id: 'svg-kit', title: '2. Turnkey SVG Vector Download Kit' },
      { id: 'design-tokens', title: '3. Master Design Tokens Scale' },
      { id: 'rule-13-standards', title: '4. Rule 13 Tonal Surface Standards' },
    ],
    content: `
The \`/brand\` center provides official brand marks, design system references, and assets for press, partners, and creators.

---

## 1. Brand Positioning & Philosophy

Cora represents understated elegance and ruthless utility for creative agencies. It rejects bloated legacy enterprise software in favor of modern, monochromatic, high-speed agency operating software.

---

## 2. Turnkey SVG Vector Download Kit

Direct 1-click downloads and clipboard copy for official vector brand assets:
- **Primary Wordmark**: Monochromatic wordmark optimized for high-DPI displays.
- **Inverted Wordmark**: High-contrast white wordmark for dark backgrounds.
- **Symbol Mark**: Distinctive Cora emblem.

---

## 3. Master Design Tokens Scale

- **Zinc Neutral Ramp**: 11-step palette (\`zinc-50\` to \`zinc-950\`).
- **Typography**: Inter for UI controls, Outfit for display titles, JetBrains Mono for metrics and code.
- **Iconography**: Thin vector SVGs (\`stroke-width: 1.8\` or \`2.2\`).

---

## 4. Rule 13 Tonal Surface Standards

All selected states, focus elements, and cards strictly avoid harsh black outline borders (\`border-zinc-900\`, \`ring-2\`), using soft tonal background fills (\`bg-zinc-100\`) and subtle structural borders (\`border-zinc-200\`).
    `,
  },
  {
    slug: 'micro-tools-growth-engine',
    title: 'Public Micro-Tools Growth Engine & Canonical Tool Registry',
    shortTitle: 'Micro-Tools Registry',
    category: 'overview',
    categoryLabel: 'Getting Started',
    description: 'Explore Cora’s 63 in-browser public micro-tools, flagship calculators, and zero-PII client-side computing architecture.',
    readTime: '6 min read',
    lastUpdated: 'October 2026',
    badge: 'Tools',
    toc: [
      { id: 'portfolio-overview', title: '1. Growth Engine Architecture & Inventory' },
      { id: 'flagship-tools', title: '2. 6 Priority Flagship Tools' },
      { id: 'privacy-architecture', title: '3. Browser-Only Computing & Privacy Modes' },
      { id: 'zero-pii-analytics', title: '4. Zero-PII Analytics Governance' },
    ],
    content: `
The **Cora Public Micro-Tools Growth Engine** provides high-utility, search-optimized tools directly in the browser. Rather than gated PDF downloads or shallow AI wrappers, each tool runs client-side calculations to resolve operational and financial bottlenecks for creative service firms.

---

## 1. Growth Engine Architecture & Inventory

The portfolio spans **63 verified public micro-tools** across 10 problem domains indexed in \`tools-canonical-registry.ts\`:
- **WordPress & Stack Health**: TCO calculator, publishing friction, and Elementor migration.
- **Conversion & Lead Funnel**: Lead response time leakage and intake calculators.
- **Agency Margins & Economics**: Scope creep exposure, effective hourly rate, and service pricing.
- **AI Search & GEO**: AI crawler access checker for OpenAI, Claude, and Perplexity bots.
- **PDF & Document Processing**: 28 in-browser PDF manipulation and compression utilities.
- **Finance & GST Tax**: SAC 9983 18% GST calculators and retainer models.

---

## 2. 6 Priority Flagship Tools

1. **WordPress Plugin Stack Cost Calculator** (\`/tools/wp-plugin-cost-calculator\`): Analyzes total cost of ownership including plugin renewals, security maintenance, and consolidation savings.
2. **Website Lead-Leakage Calculator** (\`/tools/lead-leakage-calculator\`): Quantifies pipeline losses from inquiry response latency.
3. **Agency Scope-Creep Calculator** (\`/tools/scope-creep-calculator\`): Computes unbilled revision hours and margin contraction.
4. **Service Project Pricing Calculator** (\`/tools/service-pricing-calculator\`): Models healthy service margins and generates milestone payment structures.
5. **WordPress Publishing Friction Calculator** (\`/tools/wp-publishing-friction-calculator\`): Pinpoints formatting and copy-paste bottlenecks between Google Docs and CMS.
6. **AI Crawler Access Checker** (\`/tools/ai-crawler-checker\`): Diagnoses \`robots.txt\` directives for LLM bots (OAI-SearchBot, ClaudeBot, PerplexityBot).

---

## 3. Browser-Only Computing & Privacy Modes

Tools execute deterministic models entirely in client browser memory (JavaScript and HTML5 Canvas). No proprietary figures, hourly rates, client quotes, or uploaded PDFs leave the visitor's local machine.

---

## 4. Zero-PII Analytics Governance

Telemetry via \`lib/tools-analytics.ts\` records strictly anonymous lifecycle events (tool started, calculation mode, and anonymous diagnostic tier). Input figures, business names, and financial metrics are never collected.
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
    badge: 'AI',
    toc: [
      { id: 'agent-capabilities', title: '1. Agent Capabilities' },
      { id: 'conversational-triage', title: '2. WhatsApp-Style Founder Triage' },
      { id: 'token-quotas', title: '3. Token Quotas & AI Credit Tracking' },
      { id: 'llm-backends', title: '4. Gemini 3.5 & Claude Sonnet Backends' },
      { id: 'marketing-sdr', title: '5. Public Conversational AI SDR & Conversion Engine' },
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

## 3. Token Quotas & AI Credit Tracking (v4.9.364)

AI execution is tracked per session with a real-time progress meter and anti-burnout weekly pacing:
- **Free / Starter Tier**: 500 monthly AI runs (weekly pacing: 125 runs/week).
- **Pro Tier**: 15,000 monthly AI runs (weekly soft limit: 3,750 runs/week).
- **Scale Tier**: 25,000 monthly AI runs (weekly soft limit: 6,250 runs/week).
- **Studio Enterprise**: Unlimited custom model context runs with dedicated compute instances.

### Fair Usage Anti-Burnout Pacing & Admin Bypass
- **Weekly Soft-Throttling**: Pacing alerts calculate \`ceil(monthly_quota / 4)\` to advise agency teams before exhausting monthly token pools early in billing cycles.
- **Super Admin Unrestricted Access**: Platform Super Admins and users with \`manage_options\` bypass all rate limits, AI run caps, and artificial session timeouts.
- **Ledger Synchronization**: Usage counters sync in real-time between local browser storage, the sticky admin popover, and the backend MySQL accounting ledger.

---

## 4. Gemini 3.5 & Claude Sonnet Backends

You can switch the underlying reasoning engine in the sticky administrator popover:
- **Gemini 3.5 Flash**: Optimized for sub-second speed, audio transcription, and image analysis.
- **Claude 3.5 Sonnet**: Optimized for complex multi-party commercial contracts and long-form narrative scriptwriting.

---

## 5. Public Conversational AI SDR & Conversion Engine (Oct 2026)

On the public web surface, the AI Co-Founder also powers the bottom-docked sticky AI SDR assistant:
- **Portaled Viewport Docking**: Rendered directly via \`createPortal\` into \`document.body\`, maintaining true viewport bottom centering with zero background overlay interference.
- **Real-Time Objection Handling**: Automatically counters platform comparison queries (HoneyBook, Notion, legacy multi-plugin stacks) and explains migration procedures.
- **Automated Coupon Generation**: Surfaces dynamic promotion incentives (\`MIGRATEFREE\`, \`CORA10\`) that auto-populate into checkout query parameters upon selection.
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

Schedule and preview posts across Instagram, LinkedIn, and native blog channels with brand voice continuity preserved across all channels.
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
    badge: 'MCP',
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
  {
    slug: 'autonomous-ai-workforce',
    title: 'Autonomous AI Multi-Agent Workforce & Skills Marquee',
    shortTitle: 'AI Workforce',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    description: 'Autonomous multi-agent operational workforce, neural portrait cutouts, 4-row infinite skills marquee, and background agent heartbeat monitor.',
    readTime: '6 min read',
    lastUpdated: 'October 2026',
    badge: 'New',
    toc: [
      { id: 'workforce-architecture', title: '1. Multi-Agent Workforce Architecture' },
      { id: 'neural-portrait', title: '2. Neural Portrait Cutout & Visual Canvas' },
      { id: 'skills-marquee', title: '3. 4-Row Infinite Skills Marquee' },
      { id: 'agent-heartbeat', title: '4. Autonomous Agent Heartbeat & Telemetry' },
    ],
    content: `
Deployed in **v4.9.379–v4.9.380**, the **Autonomous AI Workforce** canvas (\`/ai-agent\`) models Cora’s autonomous multi-agent operating engine, demonstrating how independent AI agents execute studio operations, sales outreach, financial reconciliation, and media delivery in parallel without human bottlenecks.

---

## 1. Multi-Agent Workforce Architecture

Cora deploys specialized autonomous agents configured to run in isolated micro-routines:
- **Operations & Logistics Agent**: Automatically coordinates crew call sheets, validates attendance punch logs, and enforces equipment custody checklists.
- **Sales & Inbound SDR Agent**: Synthesizes incoming voice notes and WhatsApp inquiries, qualifies intent, calculates SAC 9983 commercial budgets, and drafts agreements.
- **Finance & Tax Agent**: Reconciles dual-rate GST (18% / 12%), verifies SHA-256 digital signature hashes, and outputs client invoices upon shoot wrap.
- **Media Delivery & Storage Agent**: Applies client proofing watermarks, tracks hot cloud storage usage, and manages selective client download keys.

---

## 2. Neural Portrait Cutout & Visual Canvas

The \`/ai-agent\` landing experience pairs clean typography with cutting-edge visual presentation:
- **High-Resolution Cutout Blend**: Uses neural portrait cutout assets (\`cora_hero_agent_cutout.png\`) rendered with radial alpha masking that blends seamlessly into the monochromatic canvas (\`#ffffff\` light / \`#09090b\` dark).
- **Rule 13 Zero-Outline Design**: Replaces harsh box strokes with soft tonal surface layers (\`bg-zinc-100/90 dark:bg-zinc-800/80\`) and subtle structural borders.
- **Dynamic Status Indicator**: Displays a pulsating green connectivity badge (\`bg-emerald-500\`) validating live agent runtime uptime.

---

## 3. 4-Row Infinite Skills Marquee

The **InfiniteSkillsSection** component renders an automated, hardware-accelerated 4-row skill stream verifying production capabilities across every studio vertical:

1. **Stream 01 (Operations & Logistics)**:
   - Crew Shift Roster Coordination, Gear Asset Check-In/Check-Out, Studio Bay Power Management, Logistics Van Dispatch, and Restock Reorder Triggers.
2. **Stream 02 (CRM & Sales Pipeline)**:
   - Multi-Channel Lead Ingestion, WhatsApp Instant Auto-Responder, Intent-Based Deal Scoring, Dynamic Rate Sheet Quotations, and Contract Follow-Up Cadences.
3. **Stream 03 (Finance & Legal Compliance)**:
   - SAC 9983 GST Split Calculations, Instant UPI Deep-Link QR Generation, SHA-256 E-Sign Verification, Vendor TDS Ledgering, and Automated Expense Auditing.
4. **Stream 04 (Creative Delivery & Proofing)**:
   - Non-Destructive Watermark Rendering, Batch Multi-Ratio Aspect Export (1:1, 4:5, 16:9), Client Proofing Star Selects, and Expiring Access Vault Tokens.

---

## 4. Autonomous Agent Heartbeat & Telemetry

The platform provides a dedicated health check and telemetry heartbeat endpoint at \`/api/health/agent\`:
- Returns live JSON metadata regarding active model latency, queued operational routines, and system uptime.
- Emits real-time diagnostic pings ensuring 100% agent responsiveness across all connected client workspaces.
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
      { id: 'draggable-drawer', title: '4. Edge-to-Edge Draggable Drawer & Sync' },
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

---

## 4. Edge-to-Edge Draggable Drawer & Bi-Directional Sync

Starting in **v4.9.300–v4.9.302**, lead inspection features:
- **Full-Height Portaled Drawer**: Auto-portaled to the root document body with transparent backdrop, preserving live context on the underlying Kanban board.
- **Draggable Left-Edge Resizer**: Adjust drawer width smoothly, with preferences saved to \`localStorage\`.
- **Sanitized Assignee Roster**: Prevents assigning system or inactive accounts.
- **Bi-Directional Real-Time Sync**: Updates made inside the drawer instantly reflect on the Kanban card and top KPI scorecards without page reload.
    `,
  },
  {
    slug: 'projects-portfolio-module',
    title: 'Agency Projects & Portfolio Case Studies Module',
    shortTitle: 'Projects & Case Studies',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    description: 'Curate high-converting agency case studies in the workspace and publish them instantly to your public portfolio.',
    readTime: '6 min read',
    lastUpdated: 'October 2026',
    badge: 'New',
    toc: [
      { id: 'projects-overview', title: '1. Case Studies Architecture & Lifecycle' },
      { id: 'drawer-workflow', title: '2. 6-Tab Drawer Editor Workflow' },
      { id: 'storytelling-framework', title: '3. 4-Act Transformation Storytelling' },
      { id: 'nda-stealth-mode', title: '4. Stealth NDA & Confidentiality Mode' },
      { id: 'frontend-showcase', title: '5. Public Portfolio & Strategy Audit Bridge' },
    ],
    content: `
The **Agency Projects & Portfolio Case Studies Module** bridges internal workspace delivery with public marketing proof, enabling agencies to turn completed work into high-converting case studies.

---

## 1. Case Studies Architecture & Lifecycle

Case studies are persisted in the database (\`wp_cora_projects\`) and managed via the workspace at \`/workspace/projects\`:
- **Published**: Live on the public showcase at \`/projects\` and dynamic slugs \`/projects/[slug]\`.
- **Draft / In Review**: Work-in-progress drafts visible only to authorized workspace team members.
- **Archived**: Historical records removed from the live public gallery.

---

## 2. 6-Tab Drawer Editor Workflow

Editing is handled in a full-featured drawer sheet (desktop side-drawer, mobile bottom-sheet) with six dedicated tabs:
1. **Overview & Metadata**: Project title, URL slug, client name, industry, and featured status.
2. **4-Act Story Narrative**: Executive Summary, Challenge, Solution, and Deliverables scope.
3. **Impact KPIs**: Big numerical metric highlights rendered in JetBrains Mono with context labels.
4. **Media & Visual Gallery**: High-resolution hero image URL and supporting media gallery.
5. **Verified Client Testimonial**: Client quotation, executive name, title, and organization avatar.
6. **SEO & Conversion Bridge**: Custom Strategy Audit headline, button label, and destination URL.

---

## 3. 4-Act Transformation Storytelling

Rather than generic image grids, case studies follow an agency transformation framework:
- **Executive Summary**: High-level problem and outcome summary.
- **The Challenge**: Core operational or commercial hurdles faced by the client.
- **The Solution**: Strategic initiatives and systems deployed by the agency.
- **Deliverables & Scope**: Concrete assets, code, or campaigns delivered.

---

## 4. Stealth NDA & Confidentiality Mode

When a client project is governed by non-disclosure agreements, toggling **Stealth NDA** masks the client's corporate identity (e.g. "Confidential Enterprise FinTech") while preserving verified KPI statistics and narrative structure.

---

## 5. Public Portfolio & Strategy Audit Bridge

Published case studies render at \`/projects\` with responsive filter pills, search, and JetBrains Mono KPI badges. Every single study concludes with a high-converting **Free Strategy Audit CTA** linking prospective clients directly to your booking or inquiry pipeline.
    `,
  },
  {
    slug: 'forms-crm-lead-pipeline',
    title: 'Forms to CRM Lead Pipeline Dynamic Automation',
    shortTitle: 'Forms Lead Bridge',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    description: 'Automatically convert inbound form submissions into qualified CRM leads with instant pipeline synchronization.',
    readTime: '5 min read',
    lastUpdated: 'October 2026',
    toc: [
      { id: 'lead-bridge-overview', title: '1. Inbound Auto-Conversion Pipeline' },
      { id: 'tenant-isolation', title: '2. Multi-Tenant Scoping & Stage Normalization' },
      { id: 'all-submissions-api', title: '3. Universal All-Submissions REST API' },
      { id: 'embed-integration', title: '4. Embed Script & Cross-Origin Forms' },
    ],
    content: `
The **Forms to CRM Lead Pipeline** bridges web lead generation directly into the Cora Lead CRM with zero manual data entry.

---

## 1. Inbound Auto-Conversion Pipeline

Whenever an inquiry is submitted via \`POST /wp-json/cora/v1/forms/submit\` or \`cora_rest_submit_form()\`:
- The submission payload is parsed for contact information (name, email, phone, company, message).
- A normalized lead record is automatically upserted into \`wp_cora_leads\`.
- Inbound form submissions trigger automatic lead cache invalidation (\`cora_db_get_leads(true)\`), rendering new cards immediately on the Kanban pipeline without requiring manual page reload.

---

## 2. Multi-Tenant Scoping & Stage Normalization

- **Workspace Isolation**: Submissions are strictly scoped to the active workspace \`agency_id\`.
- **Stage Assignment**: New inquiries enter the canonical 'New' lead stage via \`cora_normalize_lead_stage()\`.
- **Assignee Routing**: Workspaces can automatically assign leads to specific team members or round-robin across account managers.

---

## 3. Universal All-Submissions REST API

The \`GET /wp-json/cora/v1/forms/all-submissions\` endpoint enables administrative consoles and reporting pipelines to retrieve aggregated submissions across all workspace forms with pagination, search, and form metadata.

---

## 4. Embed Script & Cross-Origin Forms

Forms can be embedded on any external website using the lightweight \`cora-form-embed.js\` script, which features resilient iframe auto-resizing, postMessage communication, and zero layout shift.
    `,
  },
  {
    slug: 'canvas-builder',
    title: 'Funnel Canvas Builder & Web Creator',
    shortTitle: 'Canvas Builder',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    description: 'Create high-converting landing pages, portfolio showreels, and booking funnels with clean monochromatic styling.',
    readTime: '6 min read',
    lastUpdated: 'October 2026',
    toc: [
      { id: 'visual-editor', title: '1. Multi-Engine Website Creation' },
      { id: 'theme-wizard', title: '2. Add Theme Wizard 2x2 Grid' },
      { id: 'drawer-standards', title: '3. Standardized Canvas Drawers & Overflow Locking' },
      { id: 'mcp-playground', title: '4. AI Tools MCP Gateway & Live Action Playground' },
      { id: 'git-sync', title: '5. Next.js Git Connector & Route Scanner' },
      { id: 'headless-preview', title: '6. Headless REST APIs & Preview Proxy' },
      { id: 'preview-resilience', title: '7. Live Preview Resilience Shield & Auto-Tailwind' },
      { id: 'ai-edit-toolbar', title: '8. Unified "Edit with AI" Toolbar' },
      { id: 'forms-embedding', title: '9. Workspace Forms Embedding Suite' },
      { id: 'templates-ecosystem', title: '10. Tools & Templates Ecosystem (16+ Schemas)' },
    ],
    content: `
The **Canvas Builder** enables creative agencies to design and publish lightning-fast client landing pages, portfolio showcases, and lead-capture funnels directly inside the Cora workspace across Elementor, Visual HTML, and Next.js engines.

---

## 1. Multi-Engine Website Creation

- **Visual HTML Engine**: In-browser inline editing with code-split view and live DOM synchronization.
- **Universal Migrator**: Ingest public websites into editable draft themes with 1 click.
- **Elementor Reskin**: 100% white-labeled builder sandboxed from CMS branding.

---

## 2. Add Theme Wizard 2x2 Grid (v4.9.349+)

The Add Theme Wizard features an active-voice, responsive 2x2 template selection grid:
- **6 Production Templates**:
  - \`creative-agency\`: Minimalist typography, portfolio showcase, case study metrics.
  - \`saas-dark\`: Modern product cards, feature matrix, interactive pricing tables.
  - \`consulting-minimal\`: Corporate advisory, appointment booking, credibility scorecards.
  - \`photography-studio\`: Full-bleed visual masonry, shoot booking flow, client proofing portal.
  - \`stationery-inventory\`: Product catalog, wholesale tier pricing, distributor intake form.
  - \`real-estate\`: Property listings, virtual tour embeds, scheduled showing requests.
- **Realistic Screenshot Previews**: High-fidelity visual cards in \`assets/img/tmpl_*\` with fallback state badges.
- **Instant Search Filter**: Live input filtering across template titles, categories, and tags.
- **Sticky Bottom Action Bar**: Pinned CTA bar providing responsive back/proceed controls across viewport sizes.

---

## 3. Standardized Canvas Drawers & Overflow Locking (v4.9.351+)

All slide-out panels across the Canvas editor are unified under the standardized \`.cora-drawer\` architecture:
- **Zero Layout Jumps**: Opening drawers applies \`body.cora-canvas-drawer-open { overflow: hidden !important; }\`, locking background page scrolling and preserving exact scroll coordinates.
- **Event Listener Resilience**: Standardized drawer open/close lifecycle restores click event listeners cleanly upon drawer termination.
- **Full Viewport Containment**: Fixed-position z-index overlays with smooth horizontal hardware-accelerated transitions.

---

## 4. AI Tools MCP Gateway & Live Action Playground (v4.9.344+)

The Canvas Template Editor includes a built-in AI Tools MCP Gateway and Live Action Playground:
- **Interactive Three-Tier Testing**:
  - **Natural Language Mode (\`nl\`)**: Plain-English prompts translated into validated MCP tool payloads.
  - **Guided Form Mode (\`guided\`)**: Form-based field inputs with dynamic validation and type checks.
  - **Developer Mode (\`dev\`)**: Raw JSON payload editing with syntax highlighting and schema validation.
- **Live MCP Tool Execution**: Executes canonical workspace tools against live workspace data with instant response rendering.
- **1-Click Code Reflection**: Direct insertion of generated HTML/CSS/JS or tool payloads straight into the Template Editor buffer.

---

## 5. Next.js Git Connector & Route Scanner (v4.9.308–v4.9.316)

Connect your production Next.js repositories directly via GitHub Personal Access Tokens (PAT):
- **App Router Scanner**: Recursively scans \`app/**/page.tsx\` files and automatically registers discovered routes and page titles.
- **Private Repo Support**: Dual authorization header support (\`Bearer <token>\` and \`token <token>\`) with dynamic branch resolution.
- **On-Demand ISR Revalidation**: Dispatches secure webhook revalidations whenever page props or content are updated.

---

## 6. Headless REST APIs & Live Dev Server Preview Proxy

- **Clean REST Endpoints**: \`/wp-json/cora-canvas/v1/manifest\`, \`/wp-json/cora-canvas/v1/page\`, and \`/wp-json/cora-canvas/v1/form-submit\`.
- **Offline Dev Server Ping**: Automatically tests \`localhost:3000\` within 150ms and provides clear instructions if offline.
- **Isolated Preview Cookies**: Scopes \`cora_canvas_preview\` cookies strictly to explicit preview queries, preventing unintended leaks to production visitors.

---

## 7. Live Preview Resilience Shield & Auto-Tailwind (v4.9.358 - v4.9.363)

- **Auto-Tailwind CDN Injection**: The compiler dynamically injects \`<script src="https://cdn.tailwindcss.com"></script>\` into preview iframes and exported static bundles if Tailwind utility classes are detected without an active stylesheet.
- **Error Boundary Encapsulation**: Isolates faulty custom scripts and guarantees zero blank screens or layout crashes.
- **100% Styling Parity**: Prevents host admin CSS leakage to ensure preview rendering exactly matches the production static site.

---

## 8. Unified "Edit with AI" Toolbar (v4.9.362)

- **Streamlined Minimalist Toolbar**: Eliminates cluttered viewport switchers from the Live Code Editor header.
- **Context-Aware Prompts**: Consolidates multi-step AI actions into a single prompt bar that generates and validates live code diffs.
- **1-Click Code Apply**: Reflects suggested HTML/CSS improvements directly into the CodeMirror editor buffer.

---

## 9. Workspace Forms Embedding Suite (v4.9.361)

- **Native Embed Codes**: Generate shortcodes (\`[cora-form id="..."]\`) or responsive iframes to embed Form AI Architect forms into any static or headless page.
- **Direct CRM Ingestion**: Submissions route automatically to \`wp_cora_leads\` with full source campaign attribution.

---

## 10. Tools & Templates Ecosystem (16+ Schemas)

Directly integrated with the public homepage ("Explore free templates") and navigation ("Tools & Templates"):
- **16+ Turnkey Industry Schemas at \`/industries\` & \`/templates\`**:
  - Photography Studios, Real Estate Brokerages, Digital Marketing Agencies, Stationery Manufacturing & Van Sales, Consulting & Professional Services, Wedding Productions, Salons & Clinics, and Creator Retainers.
- **4 Interoperable Distribution Formats**:
  - Native Landing Pages (\`native_page\`), AI Section Prompts for Claude/Cursor (\`section_prompts\`), Elementor Template Kits (\`elementor_kit\`), and Lovable / React Projects (\`lovable_project\`).
- **Interactive Live Preview Modal (\`TemplatePreviewModal.tsx\`)**:
  - Sandboxed live preview with desktop, tablet, and mobile viewport controls, 1-click clipboard prompt copy, and direct blueprint download.
- **Pre-Seeded Operational Models**: Each schema comes pre-configured with industry-specific SAC/GST codes, client intake forms, rate cards, pipeline stages, and static landing page templates.
- **Automated CRM Ingestion**: Static landing pages include embedded lead capture forms that pipe visitor inquiries directly into \`wp_cora_leads\` with instant monochromatic toast feedback.
    `,
  },
  {
    slug: 'form-builder',
    title: 'Visual Forms Builder, All Entries Registry & Inspector',
    shortTitle: 'Forms Builder & Entries',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    description: 'Form AI Architect with 15 field types, universal All Entries 5th tab registry, edge-docked entry inspector, and client timezone engine.',
    readTime: '6 min read',
    lastUpdated: 'October 2026',
    badge: 'Core',
    toc: [
      { id: 'field-types', title: '1. Supported Field Types' },
      { id: 'card-ergonomics', title: '2. Card Ergonomics & Live Preview Modal' },
      { id: 'all-entries', title: '3. Universal "All Entries" 5th Tab Registry' },
      { id: 'entry-inspector', title: '4. Edge-Docked Entry Inspector & Zero-Backdrop' },
      { id: 'timezone-engine', title: '5. Client Timezone Engine & Localized Dates' },
      { id: 'api-reference', title: '6. Submissions REST API & Deletion' },
    ],
    content: `
Form AI Architect is a permanent foundation module providing end-to-end form creation, live responsive previewing, client questionnaire ingestion, and a unified submissions registry.

---

## 1. Supported Field Types

The builder provides 15 standardized form input primitives:
- **Text & Area**: Single Line Text, Long Text (Textarea).
- **Contact**: Email Address, Numeric Phone (with regex validation).
- **Selection**: Dropdown Select, Multi-Select Checkboxes, Radio Button Groups.
- **Scheduling**: Date Picker, Call-Time Slot Selector.
- **Media & Files**: Drag-and-Drop File Upload Dropzone.
- **Scoring & Feedback**: 5-Star Rating, NPS Score Scale (0–10).
- **Legals**: Digital Signature Pad with touch-drawn canvas.

---

## 2. Card Ergonomics & Live Preview Modal

Shipped in **v4.9.372**, the Forms card list underwent an ergonomics overhaul:
- **Expanded Card Padding**: Form cards feature \`p-5 sm:p-6\` internal padding with a dedicated \`p-3.5\` container for campaign details and \`px-3 py-1.5\` status pills, preventing text from colliding with borders.
- **Full Card Clickability**: Clicking anywhere on the card body or form title navigates directly to \`#edit/\${form.id}\`.
- **Unlocked Mobile Editing**: Removed \`< 1024px\` viewport restrictions in routing handlers, allowing founders to edit forms on mobile and tablet screens.
- **In-App Interactive Form Live Preview Modal (\`#cora-form-preview-modal\`)**: Test forms in real time with responsive viewport switchers (\`Desktop\`, \`Mobile\`) and a 1-click external tab launcher.

---

## 3. Universal "All Entries" 5th Tab Registry

Located under the \`#all-entries\` sub-tab:
- **Metric Summary Strip**: Real-time counter cards for Total Submissions, Active Forms with Entries, Completion Rate, and Latest Activity.
- **Notion-Style Filter Toolbar**: Filter by specific form source, filter by completion status (Completed vs. Partial), live search input, 1-click CSV export, and refresh.
- **Table Layout Hardening**: Implemented \`table-fixed\` and rigid explicit column widths (\`w-14\`, \`w-48 md:w-56\`, \`w-auto\`, \`w-24\`, \`w-36\`, \`w-28\`, \`w-24\`) eliminating table blowouts from wide text strings.
- **Dynamic CSV Export**: Exports filtered submissions across all forms with dynamically parsed header columns.

---

## 4. Edge-Docked Entry Inspector & Zero-Backdrop

Shipped in **v4.9.374**, the Entry Inspector (\`#cora-entry-inspector\`) provides an ergonomic detail viewer:
- **Edge Docking**: Slides smoothly from the right edge on desktop (\`top-0 right-0 bottom-0 w-[480px] max-w-[90vw] border-l\`) and slides up from the bottom on mobile (\`inset-x-0 bottom-0 h-[85vh] rounded-t-3xl border-t\`).
- **Zero-Backdrop Overlay Clarity**: Dark blurred overlays have been completely removed. The background table remains 100% visible, sharp, and readable, dismissible via outside clicks.
- **Submitter Quick Overview**: Renders avatar initials, submitter name, and email/phone chips.
- **Inline Signatures & Media**: Inspects data for base64 signatures or images and renders inline \`<img src="...">\` thumbnails rather than raw text dumps.
- **Sticky Footer Controls**: 1-click "Copy Raw JSON", "Delete Entry", and "Done" actions.

---

## 5. Client Timezone Engine & Localized Dates

- Automatically detects browser timezone using \`Intl.DateTimeFormat().resolvedOptions().timeZone\` upon client hydration.
- Converts UTC server timestamps into localized relative timestamps ("10 minutes ago", "Yesterday at 4:15 PM") with full ISO datetime tooltips on hover.

---

## 6. Submissions REST API & Deletion

- \`GET /wp-json/cora/v1/forms/all-submissions\`: Fetches all submissions with form metadata JOINed.
- \`DELETE /wp-json/cora/v1/forms/submissions/{id}\`: Deletes a submission with multi-tenant tenancy verification and custom monochromatic \`coraConfirmAction\` confirmation modal.
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
  {
    slug: 'industry-verticals-directory',
    title: 'Industry Solutions Directory & 301 Redirect Architecture',
    shortTitle: 'Industry Solutions',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    description: 'Canonical industry verticals hub at /industries, 301 redirect parity from /use-cases, desktop mega-menu with dedicated icons, and 10+ vertical operational models.',
    readTime: '6 min read',
    lastUpdated: 'October 2026',
    badge: 'New',
    toc: [
      { id: 'canonical-migration', title: '1. Canonical /industries Migration & 301 Parity' },
      { id: 'desktop-mega-menu', title: '2. Desktop Mega-Menu & Mobile Drawer Parity' },
      { id: 'vertical-blueprints', title: '3. 10+ Production Industry Solutions' },
      { id: 'comparison-matrix', title: '4. Dynamic Industry Comparison Matrix' },
    ],
    content: `
Shipped in **v4.9.380**, this release establishes **\`/industries\`** as the canonical industry directory for Cora, migrating all legacy \`/use-cases\` endpoints with 301 redirect parity while deploying 10+ dedicated operational blueprints.

---

## 1. Canonical /industries Migration & 301 Parity

To elevate enterprise positioning and consolidate search authority, the directory URL structure was overhauled:
- **Canonical Slug Structure**: All solution hubs now reside under \`/industries/[slug]\` (e.g. \`/industries/real-estate\`, \`/industries/photography-studio\`).
- **301 Permanent Redirect Parity**: Server-level \`.htaccess\` rules and Next.js route mapping enforce immediate 301 redirects from \`/use-cases\` and \`/use-cases/*\` to \`/industries\`, preventing 404 errors and preserving 100% SEO link equity.
- **Internal Link Audit**: All internal navigation links, buttons, footer anchors, and sitemaps were synchronized to use the new \`/industries\` namespace.

---

## 2. Desktop Mega-Menu & Mobile Drawer Parity

The primary navigation (\`Navbar.tsx\`) features a redesigned high-density **Industries Mega-Menu**:
- **Multi-Column Visual Grid**: Categorizes solutions into logical clusters with dedicated vector SVG icon tiles and subtle category pill badges.
- **Active-Voice Microcopy**: Replaces vague marketing jargon with clear, active-voice descriptions (e.g. "Geocoded property listings & WhatsApp showing alerts" instead of "Real estate solutions").
- **Zero Heavy Outlines**: Adheres strictly to Rule 13, utilizing soft tonal background transitions (\`hover:bg-zinc-100 dark:hover:bg-zinc-800/60\`) rather than high-contrast border strokes.
- **Mobile Drawer Synchronization**: Replicates all 10+ industry verticals within the responsive mobile navigation sheet with bottom-padded clearance (\`pb-28\`).

---

## 3. 10+ Production Industry Solutions

Each vertical directory in \`industry-data.ts\` defines a complete operational blueprint:
1. **Real Estate & Property Developers** (\`/industries/real-estate\`): Geocoded property listings, virtual site tour schedules, WhatsApp showing alerts, and client inquiry pipelines.
2. **Photography & Film Studios** (\`/industries/photography-studio\`): Full-resolution media proofing, digital client contracts, multi-ratio crop presets, and shoot gear custody registries.
3. **Architecture & Interior Design** (\`/industries/architecture-interior-design\`): Material moodboards, revision milestone sign-offs, contractor tenders, and project phase tracking.
4. **Digital Marketing & Creative Agencies** (\`/industries/creative-agency\`): Retainer margin calculators, commercial proposals, multi-client campaign tracking, and GST SAC 9983 tax invoicing.
5. **Consulting & Professional Services** (\`/industries/consulting\`): Advisory engagement logs, client milestone deliverables, confidential vault contracts, and auto-scheduled call links.
6. **Salons, Spas & Wellness Clinics** (\`/industries/salon-wellness\`): Chair/therapist booking slots, customer loyalty memberships, automated reminder WhatsApps, and spot UPI billing.
7. **Fitness Studios & Personal Trainers** (\`/industries/fitness-studio\`): Session packs, automated membership renewals, client progress metrics, and trainer shift coordination.
8. **Event Production & Wedding Management** (\`/industries/events-weddings\`): Multi-vendor dispatch schedules, client milestone approvals, budget reconciliation, and run-sheet coordination.
9. **Hospitality & Boutique Restaurants** (\`/industries/hospitality\`): Table reservation workflows, private event catering agreements, distributor inventory tracking, and daily reconciliation sheets.
10. **E-Commerce & DTC Brands** (\`/industries/ecommerce-dtc\`): SKU catalog management, volume discount tiers, factory van sales dispatch, and automated GST billing.

---

## 4. Dynamic Industry Comparison Matrix

The directory features an interactive matrix component comparing capability sets across verticals:
- Displays feature coverage across Invoicing, Lead CRM, Contract E-Signing, Client Proofing, Booking Schedules, and RAG Knowledge Memory.
- Allows agency founders to quickly identify the exact operational setup suited for their business model.
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
    badge: 'Legal',
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
    badge: 'GST',
    toc: [
      { id: 'tax-math', title: '1. Automated GST Tax Splitting' },
      { id: 'hsn-sac-registry', title: '2. HSN & SAC Code Registry' },
      { id: 'invoice-pdf', title: '3. Compliant PDF Tax Invoices' },
      { id: 'vault-sync', title: '4. Automated Document Vault & SHA-256 Linkage' },
    ],
    content: `
The Cora **GST Math Engine** handles all tax calculations required for Indian service businesses and commercial studios:

- **Intra-State (Same State)**: Splits 18% GST into **9% CGST** + **9% SGST**.
- **Inter-State (Different State)**: Applies unified **18% IGST**.
- **SAC & HSN Codes**: Pre-seeded with SAC 998381 (Commercial Photography & Film Production Services) and manufacturing HSN codes.
- **Pro-Forma & Tax Invoices**: Generate 50% advance pro-formas and final tax invoices with instant payment links.

---

## 4. Automated Document Vault & SHA-256 Linkage (v4.9.303–v4.9.304)

- **Multi-Step Creator Validation**: Strict validation across client GSTIN, line item rates, and tax calculations.
- **Instant Vault Registration**: Publishing an invoice automatically creates an immutable record in the Document Vault with a cryptographic SHA-256 hash.
- **End-to-End Audit Trail**: Ties invoices directly to signed contracts and accountant tax packs.
    `,
  },
  {
    slug: 'asset-gear',
    title: 'Gear & Inventory Equipment Registry & Van Sales',
    shortTitle: 'Inventory & Van Sales',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    description: 'Catalog gear, equipment, factory SKUs, 3-step van dispatches, weight-based spot billing, and sales registers.',
    readTime: '6 min read',
    lastUpdated: 'October 2026',
    toc: [
      { id: 'inventory-registry', title: '1. Gear & Factory SKU Catalog' },
      { id: 'van-dispatch', title: '2. 3-Step Van Dispatch Wizard & Weight Allocation' },
      { id: 'spot-billing', title: '3. Spot Billing & Auto-Print GST Invoices' },
      { id: 'sales-register', title: '4. Canonical Sales Register & Ledger Export' },
    ],
    content: `
Maintain full tracking of physical production assets, factory inventory SKUs, and mobile van sales logistics across central plants and route drivers.

---

## 2. 3-Step Van Dispatch Wizard & Weight Allocation (v4.9.314)

- **Guided Dispatch**: Assign verified field drivers, delivery vehicles, and route coverage via quick city chips.
- **Weight-Based Allocation**: Allocate bulk weighted goods (standard base: ₹401.25/kg) alongside unit piece items with real-time valuation calculations.
- **Manifest Print**: Auto-generates standard 5-column warehouse dispatch manifests and 4-column inventory reports.

---

## 3. Spot Billing & Auto-Print GST Invoices (v4.9.314–v4.9.315)

- **Field POS**: Mobile drivers bill retail stores on-the-spot with standardized 4-digit bill numbers (e.g. #0001).
- **Auto-Print**: 1-click printable tax invoices with words conversion in official Indian currency.
- **Safe Stock Rollback**: Deleting or editing a spot invoice returns units back to van inventory atomically.

---

## 4. Canonical Sales Register & Ledger Export

- **Itemized Ledger**: Central ledger tracking customer names, payment channels (Cash, UPI, Credit), and item breakdowns.
- **Accountant Export**: Auditor-ready itemized CSV exports and printable sales register sheets.
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
    lastUpdated: 'October 2026',
    badge: 'PWA',
    toc: [
      { id: 'manifest-config', title: '1. Dynamic PWA Manifest' },
      { id: 'service-worker', title: '2. Service Worker Lifecycle' },
      { id: 'vapid-push', title: '3. VAPID Push Implementation' },
      { id: 'admin-badging', title: '4. Super Admin Dynamic Badging' },
      { id: 'universal-install', title: '5. Universal PWA Install Suite' },
    ],
    content: `
Cora ships with native Progressive Web App (PWA) capabilities, offering sub-50ms screen painting and native-like offline reliability.

---

## 1. Dynamic PWA Manifest

Served dynamically at \`/cora-manifest.json\`:
- \`display\`: \`standalone\`
- \`theme_color\`: \`#ffffff\` (Pure Light Mode splash to eliminate dark flashes)
- Dynamic version stamps (\`?v=CORA_WORKSPACE_VERSION\`) to trigger immediate OS icon updates on release.
- Role-scoped manifest routing: super administrators receive distinct scope (\`/workspace/dashboard?role=admin\`) and dedicated app naming.

---

## 2. Service Worker Lifecycle

Registered at root scope (\`/\`):
- **Cache-First**: Static CSS/JS and font assets.
- **Network-First**: AJAX actions and REST endpoints with sub-400ms timeout.
- **Auto-Activation**: \`skipWaiting()\` and old version cache purging executed on update within 300ms.

---

## 3. VAPID Push Implementation

- Cryptographically signed Web Push (RFC 8291/8292) using local ECDSA P-256 keys without third-party gateways.
- In-app push notification preference center per device.

---

## 4. Super Admin Dynamic Badging

- Super admin sessions dynamically render badged home screen icons (\`ADMIN\` pill badge across 512x512, 192x192, and Apple Touch icons via \`scripts/generate_admin_pwa_icons.php\`).
- Prevents visual confusion between client workspace and master control plane on mobile home screens.

---

## 5. Universal PWA Install Suite (v4.9.356+)

Cora provides persistent, frictionless PWA installation triggers across desktop and mobile devices:
- **Settings Suite Trigger**: \`#cora-settings-install-pwa-btn\` inside Settings Suite enables 1-tap installation directly from workspace preferences.
- **Sidebar Admin Popover Trigger**: \`#cora-sidebar-install-pwa-btn\` inside the sticky sidebar bottom popover allows instant access from any screen.
- **Unified \`window.coraPromptPwaInstall()\` Engine**: Gracefully handles the native \`beforeinstallprompt\` browser prompt event, while rendering contextual monochromatic platform guidance on iOS Safari (Share → Add to Home Screen).
    `,
  },
  {
    slug: 'canvas-static-compiler',
    title: 'Canvas Frontend Engine & Static Site Compiler',
    shortTitle: 'Canvas & Static Compiler',
    category: 'developers',
    categoryLabel: 'Developer APIs & PWA',
    description: 'AI Starter Prompts Grid, automated Next.js/Expo/Vite static compilation, single-CPU thread safety, and lead form ingestion.',
    readTime: '7 min read',
    lastUpdated: 'October 2026',
    badge: 'v4.9',
    toc: [
      { id: 'ai-starters', title: '1. AI Starter Prompts Grid' },
      { id: 'static-compiler', title: '2. Automated Static Compiler Pipeline' },
      { id: 'inode-resilience', title: '3. Shared Hosting Inode & Memory Resilience' },
      { id: 'form-interception', title: '4. Lead Form Ingestion & CRM Bridge' },
      { id: 'security-containment', title: '5. Path Traversal & 304 Caching' },
    ],
    content: `
The Canvas Frontend Engine allows creative studios to design, import, compile, and host custom websites directly within Cora.

---

## 1. AI Starter Prompts Grid

The Canvas Theme Wizard includes a 5-template AI Starter Prompts Grid:
- **Agency Showcase**: Monochromatic portfolio with case study drawers.
- **SaaS Platform**: Metric counters, pricing matrices, and e-sign demos.
- **Consulting / Advisory**: Lead capture funnels and appointment schedulers.
- **Photography Studio**: Dark-room gallery, booking calendar, and client proofing.
- **Custom Studio**: Universal minimalist blueprint.

Prompts can be copied with 1-click and pasted into ChatGPT, Claude, or Gemini to generate production-ready HTML/Tailwind templates.

---

## 2. Automated Static Compiler Pipeline

The background compiler (\`cora_nextjs_build_repo\`) supports Next.js, Expo, Vite, and Astro:
- Automatic detection of package managers (\`pnpm\`, \`yarn\`, \`npm\`).
- Automated dependency resolution and production export (\`out/\` or \`dist/\`).
- Single-CPU thread allocation (\`UV_THREADPOOL_SIZE=1\`, \`NODE_OPTIONS="--max-old-space-size=1536"\`) to prevent resource exhaustion on shared cPanel hosting.

---

## 3. Shared Hosting Inode & Memory Resilience

- CloudLinux Inode recovery cleanses temporary \`~/.npm/_cacache\` and build caches after every run.
- Self-healing retry logic handles directory rename locks (\`ENOTEMPTY\`) during concurrent npm extractions.

---

## 4. Lead Form Ingestion & CRM Bridge

- Ingests inbound leads from any Canvas site automatically via standard \`<form>\` tags or \`window.coraSubmitLead()\`.
- Synchronizes submissions into \`wp_cora_leads\` with instant PWA push dispatch.

---

## 5. Path Traversal & 304 Caching

- Hardened asset streaming with \`realpath\` root containment preventing \`../\` directory traversal.
- HTTP 304 Not Modified caching with SHA-256 ETags and immutable cache headers.
    `,
  },
  {
    slug: 'canvas-github-cicd',
    title: 'Canvas Themes & GitHub CI/CD Full-Lifecycle Integration',
    shortTitle: 'Canvas GitHub CI/CD',
    category: 'developers',
    categoryLabel: 'Developer APIs & PWA',
    description: 'Connect GitHub repositories to Canvas Themes with automated branch creation, PR reviews, 1-click merging, and commit rollback.',
    readTime: '7 min read',
    lastUpdated: 'October 2026',
    badge: 'CI/CD',
    toc: [
      { id: 'kpi-analytics-bar', title: '1. 4-KPI Analytical Header & Navigation' },
      { id: 'github-auth-flow', title: '2. OAuth Device Flow & PAT Authentication' },
      { id: 'pr-workflow', title: '3. Automated Branch & PR Review Workflow' },
      { id: 'merge-and-rollback', title: '4. 1-Click Merge & Commit Rollback Engine' },
      { id: 'workspace-sync', title: '5. Safe Full-Workspace Synchronization' },
    ],
    content: `
The **Canvas Themes GitHub CI/CD Integration** provides continuous deployment and version control between the Cora Visual Canvas and GitHub repositories.

---

## 1. 4-KPI Analytical Header & Navigation

The Canvas interface at \`/workspace/canvas\` features 4 real-time analytical scorecards:
- **PageSpeed Performance Score**: Production Lighthouse performance rating.
- **LCP Speed (Largest Contentful Paint)**: Core Web Vitals paint metric.
- **Total Themes & Draft Versions**: Count of active and staged template versions.
- **Theme Storage Footprint**: Asset storage consumption.

A sticky 3-subtab bar enables seamless switching between **Theme Library**, **Speed & Core Web Vitals**, and **Migration & Tools**.

---

## 2. OAuth Device Flow & PAT Authentication

GitHub integration supports dual authentication paths:
- **Device Flow OAuth 2.0**: RFC 8628 device authorization for fast, passwordless setup.
- **Personal Access Token (PAT)**: Scoped token storage with dual authentication header fallbacks (\`Bearer <token>\` and \`token <pat>\`).
- **Organization Repositories**: Provision dedicated organization repositories (\`cora_github_provision_org_repo\`) or connect personal profiles.

---

## 3. Automated Branch & PR Review Workflow

Developers and agencies can isolate theme edits from production:
- **Branch Creation**: Auto-create isolated staging branches (\`cora_github_create_branch\`).
- **Automated Pull Requests**: Generate rich PRs with automated change descriptions (\`cora_github_create_pr\`).
- **PR Inspector**: Review open PRs, diff summaries, and branch statuses directly within the workspace (\`cora_github_get_prs\`).

---

## 4. 1-Click Merge & Commit Rollback Engine

- **1-Click Merge**: Safely merge approved pull requests directly into the main production branch (\`cora_github_merge_pr\`).
- **Commit Rollback**: If an issue arises in production, revert any commit with one click (\`cora_github_revert_commit\`), instantly restoring the previous stable state.

---

## 5. Safe Full-Workspace Synchronization

The \`cora_github_sync_entire_workspace\` action commits and pushes all workspace templates, styles, and configurations to GitHub in an atomic, non-destructive operation.
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
    lastUpdated: 'October 2026',
    badge: 'REST',
    toc: [
      { id: 'auth', title: '1. Authentication & Nonces' },
      { id: 'crm-endpoints', title: '2. CRM & Lead Endpoints' },
      { id: 'invoicing-endpoints', title: '3. Invoicing & GST Endpoints' },
      { id: 'pwa-endpoints', title: '4. PWA Push Endpoints' },
      { id: 'mcp-endpoints', title: '5. Universal MCP & OAuth Endpoints' },
      { id: 'github-ajax', title: '6. GitHub CI/CD AJAX Actions' },
    ],
    content: `
All endpoints are accessible via platform REST API (\`/wp-json/cora/v1/\` or \`/wp-json/cora-pwa/v1/\` or \`/wp-json/cora-mcp/v1/\`) and standard AJAX actions.

---

## 1. Authentication & Nonces

- **REST Requests**: Require \`X-WP-Nonce\` header or Bearer API token.
- **AJAX Requests**: Require \`nonce: coraREWPData.ajaxNonce\` in POST body.
- **MCP & OAuth Requests**: Require \`Authorization: Bearer <token>\` issued via OAuth 2.1 PKCE.

---

## 2. API Endpoints Table

| Method | Route / Action | Required Permission | Description |
|---|---|---|---|
| \`GET\` | \`/cora-api/v1/projects\` | \`cora_view_dashboard\` | Query published case studies with industry, tag, and status filters |
| \`GET\` | \`/cora-api/v1/projects/{slug}\` | Public | Retrieve single 4-act project case study, numerical KPIs, and testimonial |
| \`GET\` | \`/wp-json/cora/v1/invoices\` | \`cora_manage_financials\` | Fetch agency tax invoices |
| \`POST\` | \`/wp-json/cora/v1/invoices\` | \`cora_manage_financials\` | Create 18% GST invoice |
| \`GET\` | \`/wp-json/cora/v1/forms/all-submissions\` | \`cora_view_dashboard\` | Fetch all workspace form submissions with form metadata JOINed |
| \`DELETE\` | \`/wp-json/cora/v1/forms/submissions/{id}\` | \`cora_manage_bookings\` | Securely delete single form submission record |
| \`POST\` | \`/wp-json/cora/v1/referrals/track\` | Public | Record affiliate referral impressions, clicks, and checkout conversions |
| \`POST\` | \`/wp-json/cora/v1/contracts/sign\` | Public (Token-Based) | Submit SHA-256 e-signature |
| \`POST\` | \`/wp-json/cora-pwa/v1/save-subscription\` | Authenticated User | Store VAPID Web Push subscription |
| \`GET\` | \`/wp-json/cora-pwa/v1/version-check\` | Public | Query active workspace version & changelog |
| \`GET\` | \`/wp-json/cora-mcp/v1/openapi.json\` | Public | OpenAPI 3.0.1 schema (curated 28 tools ceiling; \`?all=1\` for full registry) |
| \`POST\` | \`/wp-json/cora-mcp/v1/rpc\` | Bearer Token (OAuth 2.1) | MCP JSON-RPC 2.0 tool execution gateway |
| \`GET\` | \`/wp-json/cora-mcp/v1/tools\` | Bearer Token (OAuth 2.1) | Canonical tool registry and capabilities |
| \`GET\` | \`/.well-known/oauth-authorization-server\` | Public | RFC 8414 & RFC 9728 OAuth 2.1 server metadata |
| \`GET\` | \`/api/health\` | Public | Production edge deployment and AI agent health probe |
| \`POST\` | \`/api/ai-preview\` | Public | Sticky AI SDR query handler with objection vector analysis & coupon dispatch |
| \`POST\` | \`/api/partner-application\` | Public | Partner & creator coaching accelerator enrollment intake |

---

## 3. GitHub CI/CD AJAX Actions

| AJAX Action | Method | Description |
|---|---|---|
| \`cora_github_initiate_device_flow\` | POST | Initiate OAuth 2.0 device flow authentication |
| \`cora_github_poll_device_token\` | POST | Poll token exchange state for active device code |
| \`cora_github_create_branch\` | POST | Create isolated feature/theme branch |
| \`cora_github_create_pr\` | POST | Generate GitHub Pull Request with change summary |
| \`cora_github_get_prs\` | POST | Fetch list of active pull requests and review states |
| \`cora_github_merge_pr\` | POST | Safely merge approved pull request into main branch |
| \`cora_github_revert_commit\` | POST | Revert target commit SHA with automated rollback |
| \`cora_github_sync_entire_workspace\` | POST | Atomically commit and sync all workspace themes to GitHub |
| \`cora_github_provision_org_repo\` | POST | Provision new dedicated theme repository under organization |
    `,
  },
  {
    slug: 'mcp-gateway',
    title: 'Cora Universal Remote MCP Server & AI Integration',
    shortTitle: 'Universal MCP Server',
    category: 'developers',
    categoryLabel: 'Developer APIs & PWA',
    description: 'Connect any MCP-compatible AI assistant (ChatGPT, Claude, Gemini, Cursor, VS Code, Windsurf) to your Cora workspace via standards-compliant OAuth 2.1.',
    readTime: '8 min read',
    lastUpdated: 'October 2026',
    badge: 'MCP',
    toc: [
      { id: 'universal-architecture', title: '1. Universal Architecture' },
      { id: 'oauth-authentication', title: '2. OAuth 2.1 & Interactive Consent Panel' },
      { id: 'openapi-chatgpt', title: '3. ChatGPT Custom GPT Actions & OpenAPI 3.0.1' },
      { id: 'client-configuration', title: '4. Connecting AI Clients (ChatGPT, Claude, Cursor)' },
      { id: 'tools-catalog', title: '5. Curated 28 Core Tools Registry' },
      { id: 'canvas-playground', title: '6. Template Editor Live Action Playground' },
      { id: 'multi-tenancy', title: '7. Multi-Tenant Security & Isolation' },
    ],
    content: `
The **Cora Universal MCP Server** provides a single, standards-compliant remote endpoint (\`https://app.heycora.in/mcp\`) and OpenAPI 3.0.1 schema (\`/wp-json/cora-mcp/v1/openapi.json\`) allowing any Model Context Protocol (MCP) or OpenAI Custom Action client to securely interact with your workspace.

---

## 1. Universal Architecture

\`\`\`
                      ANY MCP / LLM CLIENT
  (ChatGPT Actions • Claude Desktop • Gemini • Cursor • VS Code • Windsurf)
                             ↕
                  CORA REMOTE MCP ENDPOINTS
            JSON-RPC 2.0: https://app.heycora.in/mcp
            OpenAPI 3.0.1: https://app.heycora.in/wp-json/cora-mcp/v1/openapi.json
                             ↕
             OAUTH 2.1 & CONSENT PRESETS
          (RFC 6749, RFC 7636 PKCE, RFC 7591 Dynamic Registration)
                             ↕
                CORA WORKSPACE MULTI-TENANCY
              (Zero-Trust Server-Side Scoping)
\`\`\`

- **Production JSON-RPC Endpoint**: \`https://app.heycora.in/mcp\` (or \`/wp-json/cora-mcp/v1/rpc\`)
- **OpenAPI 3.0.1 Endpoint**: \`https://app.heycora.in/wp-json/cora-mcp/v1/openapi.json\` (curated 28 tools ceiling; \`?all=1\` for full registry)
- **OAuth Discovery**: \`https://app.heycora.in/.well-known/oauth-authorization-server\`

---

## 2. OAuth 2.1 & Interactive Consent Panel (v4.9.345+)

Cora implements standard OAuth 2.1 with PKCE (\`S256\`) authorization code grant and an interactive granular consent panel:

1. **Authorize Endpoint**: \`https://app.heycora.in/oauth/authorize\`
2. **Token Exchange Endpoint**: \`https://app.heycora.in/oauth/token\`
3. **Revocation Endpoint**: \`https://app.heycora.in/oauth/revoke\`
4. **User Info Endpoint**: \`https://app.heycora.in/oauth/userinfo\`

### Interactive Granular Consent Screen
When an external AI client requests permissions, the workspace owner reviews an interactive consent screen:
- **Master Authorization Toggle**: Instantly grant or revoke all requested permissions in a single tap.
- **Category-Level Grouping**: Scopes are neatly organized into logical categories (*Workspace & General*, *CRM & Deals*, *Projects & Tasks*, *Finance & GST*, *Content & Canvas*, *System & Config*).
- **One-Tap Quick Presets**:
  - \`all\`: Selects all available read and write scopes.
  - \`readonly\`: Enforces a strict read-only boundary across all modules.
  - \`website\`: Grants access limited to Canvas themes and Content CMS.
- **Cryptographic Binding**: Issued access tokens are bound strictly to the authorized \`agency_id\` and automatically expire after 30 days.

---

## 3. ChatGPT Custom GPT Actions & OpenAPI 3.0.1 (v4.9.343+)

To ensure seamless, zero-error integration with OpenAI's Custom GPT Actions importer:
- **OpenAPI 3.0.1 Specification**: Upgraded from 3.1.0 to 3.0.1 to avoid undocumented OpenAI Custom GPT schema parser rejections.
- **28 Core Actions Ceiling**: OpenAI enforces an undocumented limit of ~30 actions per Custom GPT. Cora caps default discovery at 28 prioritized operational tools. Developers requiring the complete unconstrained tool registry can pass \`?all=1\` or \`limit=all\`.
- **Strict Schema Hardening**:
  - Zero emojis in operation summaries, tool names, and parameter descriptions.
  - All input schemas declare explicit \`type: object\` with valid, non-empty \`properties\` objects.
  - Empty \`required: []\` arrays are omitted to satisfy OpenAI JSON schema validators.

---

## 4. Connecting AI Clients (ChatGPT, Claude, Cursor)

### ChatGPT Custom GPT Action
1. In the GPT Builder, select **Configure → Create new action**.
2. Set **Authentication**: \`OAuth\`.
3. Set **Client ID** & **Client Secret** (from Cora Settings → AI Assistants).
4. Set **Authorization URL**: \`https://app.heycora.in/oauth/authorize\`
5. Set **Token URL**: \`https://app.heycora.in/oauth/token\`
6. Set **Scope**: \`workspace:read leads:read leads:write tasks:read tasks:write finance:read\`
7. Import Schema from URL: \`https://app.heycora.in/wp-json/cora-mcp/v1/openapi.json\`

### Claude Desktop (\`claude_desktop_config.json\`)
\`\`\`json
{
  "mcpServers": {
    "cora": {
      "url": "https://app.heycora.in/mcp"
    }
  }
}
\`\`\`

### Cursor IDE
In **Settings → Features → MCP**, add a new server:
- **Type**: \`SSE / Remote\`
- **URL**: \`https://app.heycora.in/mcp\`

---

## 5. Curated 28 Core Tools Registry

Every Cora tool exposes explicit model-independent safety annotations:

| Canonical Tool | Scope | Type | Description |
| :--- | :--- | :---: | :--- |
| \`cora.get_workspace_overview\` | \`workspace:read\` | Read-Only | Workspace revenue, active shoots, pending tasks |
| \`cora.search_knowledge_base\` | \`knowledge:read\` | Read-Only | Semantic RAG living memory search |
| \`cora.list_clients\` | \`clients:read\` | Read-Only | List client profiles and account balances |
| \`cora.create_client\` | \`clients:write\` | Write | Create a new client record |
| \`cora.list_leads\` | \`leads:read\` | Read-Only | View CRM pipeline deals and deal stages |
| \`cora.create_lead\` | \`leads:write\` | Write | Log a new sales inquiry with estimated deal value |
| \`cora.update_lead_status\` | \`leads:write\` | Write | Advance deal status (contacted, won, lost) |
| \`cora.list_projects\` | \`projects:read\` | Read-Only | List scheduled shoot bookings and deliverables |
| \`cora.create_project\` | \`projects:write\` | Write | Schedule a new shoot project |
| \`cora.list_tasks\` | \`tasks:read\` | Read-Only | Inspect Kanban task boards |
| \`cora.create_task\` | \`tasks:write\` | Write | Create and assign operational action items |
| \`cora.update_task_status\` | \`tasks:write\` | Write | Move task across Kanban columns |
| \`cora.query_financials\` | \`finance:read\` | Read-Only | Query GST tax breakdown, invoices, receivables |
| \`cora.record_financial_transaction\` | \`finance:write\` | Write | Record payment receipt or expense |
| \`cora.list_invoices\` | \`finance:read\` | Read-Only | Fetch invoice records with tax breakdown |
| \`cora.search_content\` | \`content:read\` | Read-Only | Search Growth CMS articles & guides |
| \`cora.create_article\` | \`content:write\` | Write | Draft structured article with quick_answer |
| \`cora.create_guide\` | \`content:write\` | Write | Draft flagship multi-chapter guide |
| \`cora.publish_content\` | \`content:write\` | Destructive | Validate, publish live, and verify public URL |
| \`cora.rollback_content\` | \`content:write\` | Destructive | Roll back to previous revision snapshot |
| \`cora.list_canvas_themes\` | \`canvas:read\` | Read-Only | List active and draft Canvas website themes |
| \`cora.get_canvas_theme_files\` | \`canvas:read\` | Read-Only | Inspect template HTML, CSS, and JS files |
| \`cora.save_canvas_theme_file\` | \`canvas:write\` | Write | Save or update Canvas template code assets |
| \`cora.list_inventory\` | \`inventory:read\` | Read-Only | Query warehouse stock and van consignments |
| \`cora.record_stock_movement\` | \`inventory:write\` | Write | Record stock transfer, dispatch, or spot sale |
| \`cora.list_team_members\` | \`team:read\` | Read-Only | Query team roster, roles, and status |
| \`cora.get_audit_log\` | \`system:read\` | Read-Only | Retrieve platform security and audit logs |
| \`cora.get_system_health\` | \`system:read\` | Read-Only | Query uptime, queue latency, and storage quota |

---

## 6. Template Editor Live Action Playground (v4.9.344+)

The Canvas Template Editor includes a native AI Tools MCP Gateway & Live Action Playground:
- **Interactive Three-Tier Testing**:
  - **Natural Language Mode (\`nl\`)**: Plain-English prompts translated into validated MCP tool payloads.
  - **Guided Form Mode (\`guided\`)**: Form-based field inputs with dynamic validation and type checks.
  - **Developer Mode (\`dev\`)**: Raw JSON payload editing with syntax highlighting and schema validation.
- **Live Tool Execution**: Calls the MCP gateway against real workspace records and displays response JSON.
- **1-Click Code Reflection**: Directly copies generated payloads or templates into the Template Editor code pane.

---

## 7. Multi-Tenant Security & Isolation

- **Server-Side Enforcement**: All operations are scoped by \`agency_id\` extracted from the cryptographically verified token.
- **Audit Logging**: Every AI tool execution is permanently logged in \`wp_cora_mcp_audit_log\`.
- **Instant Revocation**: Users can disconnect any AI assistant at any time from **Settings → AI Assistants**.
    `,
  },
  {
    slug: 'website-connectors',
    title: 'Website Connectors: Framer, Webflow & Custom Sites',
    shortTitle: 'Website Connectors',
    category: 'developers',
    categoryLabel: 'Developer APIs & PWA',
    description: 'Embed client portals, e-sign agreements, and lead capture forms on Framer, Webflow, and custom websites.',
    readTime: '5 min read',
    lastUpdated: 'August 2026',
    toc: [
      { id: 'framer-embed', title: '1. Framer 1-Line Embed' },
      { id: 'webflow-webhooks', title: '2. Webflow Webhook Integration' },
      { id: 'custom-embed', title: '3. Custom CMS Connector' },
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
