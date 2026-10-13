# Cora Micro-Tools Growth Engine — Implementation Specification and Verified Status

**Platform**: Cora Studio OS (`heycora.in`)  
**Audit & Verification Date**: October 2026  
**Status**: Authoritative Verified Specification  
**Canonical Specification Reference**: See [CORA_MICRO_TOOLS_GROWTH_ENGINE_SPECIFICATION.md](./CORA_MICRO_TOOLS_GROWTH_ENGINE_SPECIFICATION.md)

---

## 1. Executive Summary & Strategic Positioning

The Cora Public Micro-Tools Portfolio serves as a top-of-funnel acquisition channel designed to solve concrete operational problems for creative agencies, design studios, web development firms, and independent service businesses. Rather than deploying generic AI text wrappers or gated PDF downloads that degrade user trust, Cora publishes high-utility, search-optimized micro-tools that provide immediate value without requiring account creation.

### The Problem-to-Workspace Bridge
1. **Un-gated Utility**: Visitors receive full diagnostic calculations, itemized cost breakdowns, and exportable outputs directly in their browser.
2. **Contextual Relevance**: Each tool highlights an operational friction point (e.g., hidden WordPress plugin maintenance, unbilled scope creep, lead intake delays) that creative agencies face daily.
3. **Natural Transition to Cora**: Rather than disparaging existing client technology stacks (e.g., WordPress or WooCommerce), tools position Cora as a complementary business operations layer for lead capture, client proposals, agreements, and retainers.

---

## 2. Verified Technical State & Reconciled Inventory

### Physical File System vs. Registry Reconciliation
A physical audit of `cora-frontend/app/tools/` was performed across all 66 directories on disk:
- **3 Hub Index Pages**: `/tools/pdf`, `/tools/sheets`, `/tools/images`
- **63 Dedicated Tool Routes**: Validated on disk and registered in both `lib/tools-canonical-registry.ts` and `lib/tools-data.ts`.
- **Category Counts**: 11 distinct operational categories derived from the canonical registry with zero discrepancy.

```
Total Directories on Disk: 66
├── Category Hub Routes: 3 (/pdf, /sheets, /images)
└── Public Tool Routes: 63
    ├── WordPress & Stack Health: 2
    ├── Conversion & Lead Funnel: 1
    ├── Agency Margins & Economics: 3
    ├── AI Search & GEO: 1
    ├── Finance & GST Tax: 2
    ├── Contracts & Legal: 1
    ├── Developer & Web Embeds: 3
    ├── AI & Copywriting: 1
    ├── PDF Processing: 28
    ├── Sheets & Calculation: 9
    └── Image & Asset Processing: 12
```

### Corrections to Previous Claims
Previous drafts contained unverified assertions. The following corrections are now formally established:
- **No Claim of "Universal Sub-50ms Worldwide"**: Static HTML pages are delivered via global CDN edge caches; however, computational execution speed depends on client device hardware and browser capabilities.
- **No Claim of "100% Test Coverage"**: Automated unit test suites (`tools-engines.test.ts`) test core mathematical engines and edge cases (28 passing test cases). Browser UI components are validated via Next.js TypeScript type checking and static build compilation.
- **No Claim of "Tamper-Proof Signatures"**: Digital signature tools provide audit trails and hash tracking, but are not certified cryptographic hardware tokens. Terminology is standardized to "tamper-evident audit logs."
- **No Causal Revenue Guarantees**: Calculator outputs are deterministic mathematical models comparing user inputs against documented industry benchmarks. They do not constitute financial forecasts or guaranteed returns.
- **Transparent Data Processing Classifications**: Tools are classified into three distinct processing categories, with explicit notices rendered on each page.

---

## 3. Data Processing Architecture & Privacy Classifications

Every tool in the portfolio is categorized by its data processing mode:

| Processing Mode | Definition | Data Transmission Policy |
| :--- | :--- | :--- |
| **Browser-only** | Executes entirely within local browser memory (JavaScript, WebAssembly, HTML5 Canvas). | Zero file bytes, text inputs, financial figures, or form values leave the user's browser. |
| **Remote audit** | Requires external network retrieval (e.g., fetching a domain's public `robots.txt` or testing URL response headers). | Transmits only the target public URL; no proprietary client or financial data is processed. |
| **AI-assisted** | Calls an LLM endpoint to generate structured text or transform unstructured inputs. | Transmits user prompt parameters to the language model provider; zero training on user inputs. |

### Analytics Governance (Zero PII Policy)
Telemetry via `lib/tools-analytics.ts` is strictly non-sensitive:
- **Allowed Events**: Tool execution started, tool execution completed, calculation mode, and anonymous diagnostic result tier (e.g., "High Maintenance Bloat").
- **Strictly Prohibited**: Entered financial numbers, hourly rates, client names, uploaded documents, generated proposal contents, and IP addresses.

---

## 4. Priority Flagship Tools (Deep Audit & Implementation State)

### Flagship 1: WordPress Plugin Stack Cost Calculator
- **Route**: `/tools/wp-plugin-cost-calculator`
- **Status**: **Verified**
- **Processing Mode**: Browser-only
- **Suitability for Indexing**: Immediate (High Priority)
- **Key Capabilities**:
  - Monthly and Annual view toggle.
  - Multi-currency support (`USD`, `EUR`, `GBP`, `INR`).
  - Toggleable engineering and maintenance labor costs.
  - Editable assumptions for renewal inflation and major update breakage incidents.
  - Itemized annual cost breakdown table.
  - Working CSV export and print stylesheet.
  - Contextual Cora bridge: Keep the WordPress marketing frontend while consolidating disconnected backend operational plugins into Cora.

### Flagship 2: Agency Scope Creep Calculator
- **Route**: `/tools/scope-creep-calculator`
- **Status**: **Verified**
- **Processing Mode**: Browser-only
- **Suitability for Indexing**: Immediate (High Priority)
- **Key Capabilities**:
  - Planned delivery hours vs. actual hours delivered.
  - Realized Effective Hourly Rate (EHR) decay calculation.
  - Gross profit margin contraction percentage points.
  - Suggested change-order quote based on explicit formula.
  - Working CSV export, print action, and profile presets.
  - Contextual Cora bridge: Document Vault with tamper-evident digital signoff on change orders before revisions commence.

### Flagship 3: Website Lead Leakage Calculator
- **Route**: `/tools/lead-leakage-calculator`
- **Status**: **Verified**
- **Processing Mode**: Browser-only
- **Suitability for Indexing**: Immediate (High Priority)
- **Key Capabilities**:
  - First-response decay modeling based on empirical speed-to-lead benchmarks.
  - Side-by-side comparison of observed baseline versus a hypothetical low-friction operational benchmark.
  - Stage-by-stage funnel drop-off table (intake, proposal dispatch, agreement close).
  - Explicit disclaimer: Deterministic simulation, zero causal revenue guarantees.
  - Working CSV export and print functionality.
  - Contextual Cora bridge: Automated inquiry routing, 15-minute proposal dispatch, and integrated client billing.

---

## 5. Supporting Editorial Content Cluster

To build organic search authority, each flagship tool is paired with a comprehensive, in-depth editorial guide in `lib/articles-data.ts`:

1. **WordPress Plugin Stack TCO**:
   - *Slug*: `/articles/website-content/wordpress-plugin-stack-cost-and-maintenance-tco`
   - *Topic*: Hidden developer maintenance, staging labor, update regressions, and annual software subscription inflation.
   - *Visual*: The 4-Layer Website Stack Cost Iceberg diagram.
   - *Author*: Aarav Mehta, Systems Architect.

2. **Agency Scope Creep Guide**:
   - *Slug*: `/articles/studio-operations/agency-scope-creep-and-project-profitability-guide`
   - *Topic*: Margin erosion mechanics, effective rate decay, and structured change-order protocols.
   - *Visual*: The Project Margin Contraction Waterfall diagram.
   - *Author*: Kavya Patel, Director of Studio Operations.

3. **Lead Leakage Analysis**:
   - *Slug*: `/articles/studio-operations/website-lead-leakage-and-response-time-friction`
   - *Topic*: Empirical inquiry drop-off curves, speed-to-lead benchmarks, and proposal delivery velocity.
   - *Visual*: The 3-Stage Inbound Inquiry Funnel Decay diagram.
   - *Author*: Rohan Verma, Revenue Operations Strategist.

---

## 6. Complete Portfolio Inventory & Verification Matrix

| # | Tool Name | Route Slug | Category | Mode | Status | Search Indexing Suitability |
|:---:|:---|:---|:---|:---:|:---:|:---|
| 1 | WP Plugin Cost Calculator | `wp-plugin-cost-calculator` | `wp_stack` | Browser-only | **Verified** | Ready for Indexing |
| 2 | Agency Scope Creep Calculator | `scope-creep-calculator` | `agency_margins` | Browser-only | **Verified** | Ready for Indexing |
| 3 | Lead Leakage Calculator | `lead-leakage-calculator` | `lead_funnel` | Browser-only | **Verified** | Ready for Indexing |
| 4 | Service Pricing Calculator | `service-pricing-calculator` | `agency_margins` | Browser-only | **Implemented** | Ready for Indexing |
| 5 | WP Publishing Friction Calculator | `wp-publishing-friction-calculator` | `wp_stack` | Browser-only | **Implemented** | Ready for Indexing |
| 6 | AI Crawler robots.txt Checker | `ai-crawler-checker` | `ai_geo` | Browser-only | **Implemented** | Ready for Indexing |
| 7 | Indian GST Tax Calculator | `gst-calculator` | `finance_tax` | Browser-only | **Live** | Ready for Indexing |
| 8 | Retainer Scope Buffer Calculator | `retainer-calculator` | `finance_tax` | Browser-only | **Live** | Ready for Indexing |
| 9 | IT Act Contract Clause Builder | `contract-builder` | `contracts_legal` | Browser-only | **Live** | Ready for Indexing |
| 10 | Dynamic UPI QR Generator | `upi-qr-generator` | `developer_embed` | Browser-only | **Live** | Ready for Indexing |
| 11 | Agency Proposal Generator | `agency-proposal-generator` | `agency_margins` | Browser-only | **Implemented** | Ready for Indexing |
| 12 | Listing AI Generator | `listing-ai` | `ai_copy` | AI-assisted | **Live** | Exclude from Indexing (Tool Utility Focus) |
| 13 | Merge PDF Files | `merge-pdf` | `pdf_tools` | Browser-only | **Live** | Ready for Indexing |
| 14 | Split PDF Pages | `split-pdf` | `pdf_tools` | Browser-only | **Live** | Ready for Indexing |
| 15 | Compress PDF Size | `compress-pdf` | `pdf_tools` | Browser-only | **Live** | Ready for Indexing |
| 16 | PDF to Images Converter | `pdf-to-images` | `pdf_tools` | Browser-only | **Live** | Ready for Indexing |
| 17 | PDF Page Counter & Metadata | `pdf-page-counter` | `pdf_tools` | Browser-only | **Live** | Ready for Indexing |
| 18 | Rotate PDF Pages | `rotate-pdf` | `pdf_tools` | Browser-only | **Live** | Ready for Indexing |
| 19 | Add Watermark to PDF | `add-watermark` | `pdf_tools` | Browser-only | **Live** | Ready for Indexing |
| 20 | Add Page Numbers to PDF | `add-page-numbers` | `pdf_tools` | Browser-only | **Live** | Ready for Indexing |
| 21 | Sign PDF Document | `sign-pdf` | `pdf_tools` | Browser-only | **Live** | Ready for Indexing |
| 22 | Flatten PDF Forms | `flatten-pdf` | `pdf_tools` | Browser-only | **Live** | Ready for Indexing |
| 23 | Compare Two PDF Files | `compare-pdf` | `pdf_tools` | Browser-only | **Implemented** | Needs UI Polish Before Indexing |
| 24 | Crop PDF Margins | `crop-pdf` | `pdf_tools` | Browser-only | **Implemented** | Needs UI Polish Before Indexing |
| 25 | Excel to PDF Converter | `excel-to-pdf` | `pdf_tools` | Browser-only | **Implemented** | Needs Engine Upgrade Before Indexing |
| 26 | Extract PDF Pages | `extract-pages` | `pdf_tools` | Browser-only | **Implemented** | Ready for Indexing |
| 27 | HTML to PDF Converter | `html-to-pdf` | `pdf_tools` | Browser-only | **Implemented** | Ready for Indexing |
| 28 | OCR PDF Text Extractor | `ocr-pdf` | `pdf_tools` | Browser-only | **Implemented** | Needs Tesseract WASM Before Indexing |
| 29 | Organize PDF Pages | `organize-pdf` | `pdf_tools` | Browser-only | **Implemented** | Ready for Indexing |
| 30 | PDF to Excel Converter | `pdf-to-excel` | `pdf_tools` | Browser-only | **Implemented** | Needs Parser Upgrade Before Indexing |
| 31 | PDF to JPG Converter | `pdf-to-jpg` | `pdf_tools` | Browser-only | **Implemented** | Ready for Indexing |
| 32 | PDF to Markdown Converter | `pdf-to-markdown` | `pdf_tools` | Browser-only | **Implemented** | Ready for Indexing |
| 33 | PDF to Word Converter | `pdf-to-word` | `pdf_tools` | Browser-only | **Implemented** | Needs WASM Upgrade Before Indexing |
| 34 | PowerPoint to PDF | `powerpoint-to-pdf` | `pdf_tools` | Browser-only | **Implemented** | Needs Engine Upgrade Before Indexing |
| 35 | Password Protect PDF | `protect-pdf` | `pdf_tools` | Browser-only | **Implemented** | Ready for Indexing |
| 36 | Redact PDF Content | `redact-pdf` | `pdf_tools` | Browser-only | **Implemented** | Ready for Indexing |
| 37 | Repair Damaged PDF | `repair-pdf` | `pdf_tools` | Browser-only | **Implemented** | Needs Byte Repair Upgrade Before Indexing |
| 38 | Translate PDF Document | `translate-pdf` | `pdf_tools` | AI-assisted | **Implemented** | Needs API Gateway Before Indexing |
| 39 | Unlock Password PDF | `unlock-pdf` | `pdf_tools` | Browser-only | **Implemented** | Ready for Indexing |
| 40–48 | 9 Sheets & Formula Tools | `/tools/*` (9 routes) | `sheets_tools` | Browser-only | **Live** | Ready for Indexing |
| 49–60 | 12 Image Processing Tools | `/tools/*` (12 routes) | `image_tools` | Browser-only | **Live** | Ready for Indexing |
| 61–63 | Embed & Code Tools | `/tools/*` (3 routes) | `developer_embed`| Browser-only | **Live** | Ready for Indexing |

---

## 7. Quality Assurance & Verification Results

### Unit Test Execution
- **Command**: `npx tsx lib/tools-engines/__tests__/tools-engines.test.ts`
- **Result**: `28 passed, 0 failed`

### Static Export Compilation
- **Type Checking**: `npx tsc --noEmit` passed with 0 errors.
- **Static Export**: 320+ static routes compiled successfully, confirming zero runtime dependencies on serverless API routes during client rendering.

---

## 8. Prioritized Backlog & Technical Debt Remediation

1. **Replace Simulated Engines with In-Browser WebAssembly**:
   - *Target*: 6 complex conversion routes currently using simulated client delays (`ocr-pdf`, `pdf-to-word`, `repair-pdf`, `excel-to-pdf`, `powerpoint-to-pdf`, `translate-pdf`).
   - *Action*: Integrate `@tesseract.js` for local OCR and client-side WebAssembly parser libraries. Keep search indexing blocked for these 6 tools until native client parsing is complete.
2. **Standardize Breadcrumbs and OpenGraph Imagery**:
   - Generate dynamic SVG social share cards for the top 10 most visited tools reflecting active currency and user metrics.
3. **Automated End-to-End Regression Testing**:
   - Add Playwright E2E smoke tests verifying slider interactions, CSV export downloads, and print dialog triggers across mobile and desktop viewports.

---

## 9. 30 / 60 / 90-Day Organic Distribution & Growth Plan

### Days 1–30: Technical Foundation & Search Validation
- Publish the 3 flagship calculators with verified schemas and zero-PII analytics.
- Index the 3 supporting articles in `lib/articles-data.ts` and link them bidirectionally with their respective tools.
- Monitor Google Search Console for initial impressions on long-tail target queries (`wordpress plugin cost calculator`, `agency scope creep calculator`, `website lead leakage`).
- Baseline metric: Track organic tool sessions and conversion rate to `/features` and `/pricing`.

### Days 31–60: Authority Distribution & Community Outposts
- Publish data-driven editorial teardowns on LinkedIn and agency subreddits analyzing average agency scope creep using anonymized sample calculations.
- Submit the tools to developer and agency directory hubs (Product Hunt, AlternativeTo, SaaSHub) focusing on the "100% Free & Browser-Only" privacy angle.
- Ship WebAssembly upgrades for `ocr-pdf` and `pdf-to-word` to remove simulation notices and qualify them for search indexing.

### Days 61–90: Funnel Optimization & Activation Scaling
- Evaluate the North Star Metric: **Activated Cora workspaces per 1,000 organic tool visitors**.
- Implement contextual in-tool templates (e.g., "Export as Cora Proposal Template") allowing users who complete an audit to immediately instantiate their project in a trial workspace.
- Expand the next wave of diagnostic tools: *Agency Retainer Profitability Calculator* and *WooCommerce vs Quote Workflow Checker*.
