// ══════════════════════════════════════════════════════════════════════════
// CORA PUBLIC MICRO-TOOLS CANONICAL REGISTRY
// Authoritative single source of truth conforming to Master Specification.
// Contains full 24-field schema, execution modes, ICP mapping & dynamic counts.
// ══════════════════════════════════════════════════════════════════════════

export type ToolCategory =
  | 'agency_economics'
  | 'wordpress_stack'
  | 'ai_discovery'
  | 'finance_tax'
  | 'contracts_legal'
  | 'developer_embed'
  | 'pdf_tools'
  | 'sheets_tools'
  | 'image_tools'
  | 'real_estate';

export type ToolStatus = 'draft' | 'review' | 'live' | 'deprecated';
export type ExecutionMode = 'browser-only' | 'remote-audit' | 'ai-assisted';
export type SecurityRisk = 'none' | 'low' | 'moderate';

export interface ToolIOField {
  name: string;
  type: 'currency' | 'number' | 'percentage' | 'text' | 'string' | 'select' | 'boolean' | 'array' | 'score' | 'status' | 'file' | 'image' | 'url';
  description: string;
}

export interface ContextualCoraAction {
  title: string;
  description: string;
  href: string;
  label: string;
}

export interface CanonicalToolRecord {
  slug: string;
  publicTitle: string;
  shortTitle: string;
  category: ToolCategory;
  categoryLabel: string;
  status: ToolStatus;
  owner: string;
  icp: string[];
  jobToBeDone: string;
  targetSearchCluster: string[];
  metadataTitle: string;
  metadataDescription: string;
  canonicalUrl: string;
  inputs: ToolIOField[];
  outputs: ToolIOField[];
  features: string[];
  methodologyVersion: string;
  executionMode: ExecutionMode;
  privacyDisclosure: string;
  securityRisk: SecurityRisk;
  relatedTools: string[];
  supportingArticles: string[];
  supportingDocs: string[];
  contextualCoraAction: ContextualCoraAction;
  analyticsIdentifier: string;
  publicationDate: string;
  lastReviewedDate: string;
}

export interface CategorySummary {
  id: string;
  label: string;
  count: number;
}

export const CANONICAL_TOOLS_REGISTRY: CanonicalToolRecord[] = [
  {
    "slug": "wp-plugin-cost-calculator",
    "publicTitle": "WordPress Plugin Stack Cost & TCO Calculator",
    "shortTitle": "WP Plugin Cost Calculator",
    "category": "wordpress_stack",
    "categoryLabel": "WordPress & Stack",
    "status": "live",
    "owner": "Economics Agent",
    "icp": [
      "WordPress Agencies",
      "WooCommerce Merchants",
      "Web Development Studios",
      "Elementor Freelancers"
    ],
    "jobToBeDone": "Calculate the true annual subscription and maintenance cost of disconnected WordPress plugins and evaluate consolidation savings.",
    "targetSearchCluster": [
      "wordpress plugin cost calculator",
      "annual wordpress maintenance cost",
      "cost of wordpress plugin stack",
      "woocommerce plugin sprawl calculator"
    ],
    "metadataTitle": "WordPress Plugin Stack Cost Calculator | Calculate Annual TCO & Maintenance",
    "metadataDescription": "Calculate the true annual subscription fees, hidden developer maintenance hours, and fragility risk of your WordPress plugin stack. Free, private, zero signup.",
    "canonicalUrl": "https://heycora.in/tools/wp-plugin-cost-calculator",
    "inputs": [
      {
        "name": "pluginStack",
        "type": "array",
        "description": "Selected plugin categories and annual license costs"
      },
      {
        "name": "monthlyHosting",
        "type": "currency",
        "description": "Monthly managed hosting and staging fee"
      },
      {
        "name": "monthlyMaintenanceHours",
        "type": "number",
        "description": "Developer hours spent updating, testing, and debugging plugin conflicts per month"
      },
      {
        "name": "hourlyLabourRate",
        "type": "currency",
        "description": "Blended internal or agency hourly developer cost"
      },
      {
        "name": "customIntegrationsCount",
        "type": "number",
        "description": "Number of custom API, Zapier, or webhook connections between plugins"
      },
      {
        "name": "annualIncidentEstimate",
        "type": "number",
        "description": "Estimated billable hours lost to downtime or checkout breaks per year"
      }
    ],
    "outputs": [
      {
        "name": "annualDirectCost",
        "type": "currency",
        "description": "Annual direct license subscription fees"
      },
      {
        "name": "annualLabourCost",
        "type": "currency",
        "description": "Annual hidden developer maintenance labor cost"
      },
      {
        "name": "threeYearTco",
        "type": "currency",
        "description": "Projected 3-year total cost of ownership including renewal inflation"
      },
      {
        "name": "complexityScore",
        "type": "score",
        "description": "Fragility and maintenance risk index from 0 to 100"
      },
      {
        "name": "duplicatedCategories",
        "type": "array",
        "description": "Overlapping feature categories identified in the stack"
      },
      {
        "name": "actionRecommendations",
        "type": "array",
        "description": "Prioritized recommendations to keep, consolidate, or replace"
      }
    ],
    "features": [
      "12 Common Plugin Categories Pre-loaded",
      "Custom License Cost Overrides",
      "Hidden Maintenance Hours Formula",
      "3-Year Inflation Model",
      "Complexity Risk Radar",
      "Consolidation ROI Comparison"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Your plugin selections, costs, and labor rates are computed locally and never sent to any server.",
    "securityRisk": "none",
    "relatedTools": [
      "wp-publishing-friction-calculator",
      "lead-leakage-calculator",
      "service-pricing-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/platform-architecture"
    ],
    "contextualCoraAction": {
      "title": "Keep WordPress for publishing. Consolidate operations in Cora.",
      "description": "Retain your existing WordPress frontend while replacing $1,400+/yr in disconnected forms, booking, e-sign, and CRM plugins with Cora\u2019s native operating system.",
      "href": "/features",
      "label": "Explore Cora Workspace Integration"
    },
    "analyticsIdentifier": "wp_plugin_cost_calculator",
    "publicationDate": "2026-10-11",
    "lastReviewedDate": "2026-10-11"
  },
  {
    "slug": "lead-leakage-calculator",
    "publicTitle": "Website Lead-Leakage & Follow-Up Revenue Calculator",
    "shortTitle": "Lead-Leakage Calculator",
    "category": "agency_economics",
    "categoryLabel": "Agency Economics",
    "status": "live",
    "owner": "Economics Agent",
    "icp": [
      "Digital Agencies",
      "Creative Studios",
      "B2B Service Providers",
      "Consultancies"
    ],
    "jobToBeDone": "Calculate revenue lost at each stage between website form submission and signed proposal due to slow response times and broken handoffs.",
    "targetSearchCluster": [
      "website lead conversion calculator",
      "how many leads am I losing",
      "agency lead leakage calculator",
      "speed to lead revenue loss"
    ],
    "metadataTitle": "Website Lead-Leakage Calculator | Calculate Lost Pipeline & Revenue",
    "metadataDescription": "Calculate how many qualified client enquiries you lose between website submission, intake, and proposal due to response delays. Free, instant, private.",
    "canonicalUrl": "https://heycora.in/tools/lead-leakage-calculator",
    "inputs": [
      {
        "name": "monthlyEnquiries",
        "type": "number",
        "description": "Total monthly contact form, booking, or inbound enquiries"
      },
      {
        "name": "averageDealValue",
        "type": "currency",
        "description": "Average closed contract or project retainer value"
      },
      {
        "name": "firstResponseTimeMinutes",
        "type": "number",
        "description": "Average time to make first contact with a new inbound lead"
      },
      {
        "name": "qualificationRatePct",
        "type": "percentage",
        "description": "Percentage of raw enquiries that match your target client profile"
      },
      {
        "name": "proposalDispatchRatePct",
        "type": "percentage",
        "description": "Percentage of qualified leads that receive a formal proposal/estimate"
      },
      {
        "name": "proposalCloseRatePct",
        "type": "percentage",
        "description": "Percentage of sent proposals that convert to paying clients"
      },
      {
        "name": "structuredFollowUpRatePct",
        "type": "percentage",
        "description": "Percentage of pending proposals followed up at least 3 times"
      }
    ],
    "outputs": [
      {
        "name": "currentMonthlyRevenue",
        "type": "currency",
        "description": "Estimated current monthly closed revenue from inbound enquiries"
      },
      {
        "name": "optimizedMonthlyRevenue",
        "type": "currency",
        "description": "Potential monthly revenue with <15m response and automated follow-ups"
      },
      {
        "name": "monthlyRevenueLeaked",
        "type": "currency",
        "description": "Estimated revenue lost every month across the funnel"
      },
      {
        "name": "annualRevenueLeaked",
        "type": "currency",
        "description": "Annualized lost pipeline and revenue"
      },
      {
        "name": "largestDropoffStage",
        "type": "string",
        "description": "The specific stage causing the highest dollar leakage"
      },
      {
        "name": "speedToLeadDecayRate",
        "type": "percentage",
        "description": "Qualification penalty factor based on Harvard/MIT response time research"
      },
      {
        "name": "prioritizedOperationalFixes",
        "type": "array",
        "description": "Top 3 operational fixes to plug the pipeline leak"
      }
    ],
    "features": [
      "MIT Speed-to-Lead Decay Curve",
      "Stage-by-Stage Funnel Drop-off Analysis",
      "Annualized Leakage Projection",
      "Interactive Response Time Slider",
      "Operational Action Plan",
      "1-Click PDF Report Export"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% Client-Side. Your traffic, conversion rates, and revenue numbers are calculated in browser memory and never recorded.",
    "securityRisk": "none",
    "relatedTools": [
      "scope-creep-calculator",
      "service-pricing-calculator",
      "wp-plugin-cost-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-automate-shoot-booking-and-contracts"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Connect every website enquiry to an instant follow-up workflow.",
      "description": "Route incoming leads automatically to WhatsApp notifications, automated intake forms, and 1-click proposal builders in Cora Lead CRM.",
      "href": "/features/lead-crm",
      "label": "Automate Lead Follow-ups in Cora"
    },
    "analyticsIdentifier": "lead_leakage_calculator",
    "publicationDate": "2026-10-11",
    "lastReviewedDate": "2026-10-11"
  },
  {
    "slug": "scope-creep-calculator",
    "publicTitle": "Agency Scope-Creep & Margin Loss Calculator",
    "shortTitle": "Scope-Creep Calculator",
    "category": "agency_economics",
    "categoryLabel": "Agency Economics",
    "status": "live",
    "owner": "Commercial Agent",
    "icp": [
      "Software Agencies",
      "Design Studios",
      "Marketing Agencies",
      "Freelance Developers"
    ],
    "jobToBeDone": "Calculate unbilled revision hours, margin erosion, and effective hourly wage dilution caused by out-of-scope client requests.",
    "targetSearchCluster": [
      "scope creep calculator",
      "agency project profitability calculator",
      "unbilled agency revisions calculator",
      "how much is scope creep costing me"
    ],
    "metadataTitle": "Agency Scope-Creep Calculator | Calculate Unbilled Revision Costs",
    "metadataDescription": "Calculate how unbilled client revisions and out-of-scope requests erode project margins and dilute your hourly rate. Free, instant, private.",
    "canonicalUrl": "https://heycora.in/tools/scope-creep-calculator",
    "inputs": [
      {
        "name": "projectAgreedFee",
        "type": "currency",
        "description": "Total agreed fixed contract or milestone fee"
      },
      {
        "name": "plannedHours",
        "type": "number",
        "description": "Original estimated hours to complete the scope of work"
      },
      {
        "name": "additionalUnbilledHours",
        "type": "number",
        "description": "Extra unbilled hours spent on client favors and revisions"
      },
      {
        "name": "blendedInternalHourlyCost",
        "type": "currency",
        "description": "Direct hourly cost of team members working on the project"
      },
      {
        "name": "includedRevisions",
        "type": "number",
        "description": "Contractually agreed rounds of revisions"
      },
      {
        "name": "actualRevisionsDelivered",
        "type": "number",
        "description": "Actual revision cycles requested by client"
      },
      {
        "name": "unplannedDelayDays",
        "type": "number",
        "description": "Days project was held in pending client review or feedback limbo"
      }
    ],
    "outputs": [
      {
        "name": "plannedMarginPct",
        "type": "percentage",
        "description": "Originally budgeted gross project margin"
      },
      {
        "name": "realizedMarginPct",
        "type": "percentage",
        "description": "Actual realized gross margin after unbilled hours"
      },
      {
        "name": "unpaidLabourValue",
        "type": "currency",
        "description": "Market value of unbilled labor delivered for free"
      },
      {
        "name": "effectiveHourlyRate",
        "type": "currency",
        "description": "Actual effective hourly revenue earned vs target rate"
      },
      {
        "name": "marginErosionPct",
        "type": "percentage",
        "description": "Percentage points of profitability lost to scope creep"
      },
      {
        "name": "recommendedChangeOrderAmount",
        "type": "currency",
        "description": "Fair billable change-order value to recover lost hours"
      },
      {
        "name": "clientSafeChangeSummary",
        "type": "string",
        "description": "Professional, non-confrontational change-order copy ready to send"
      }
    ],
    "features": [
      "Margin Erosion Metric",
      "Effective Hourly Rate Dilution",
      "Change-Order Amount Generator",
      "Client-Safe Scope Adjustment Copy",
      "Revision Cap Enforcer",
      "1-Click PDF Scope Defense Protocol"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser. No project fees, labor costs, or client details are stored or transmitted.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "retainer-calculator",
      "lead-leakage-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/multi-tenant-rbac"
    ],
    "contextualCoraAction": {
      "title": "Turn out-of-scope client requests into approved, paid change-orders.",
      "description": "Lock revision limits with legally binding digital e-signatures and generate instant addendum invoices in Cora E-Sign Vault.",
      "href": "/features/esign-vault",
      "label": "Prevent Scope Creep with Cora"
    },
    "analyticsIdentifier": "scope_creep_calculator",
    "publicationDate": "2026-10-11",
    "lastReviewedDate": "2026-10-11"
  },
  {
    "slug": "service-pricing-calculator",
    "publicTitle": "Service Project & Milestone Pricing Calculator",
    "shortTitle": "Service Pricing Calculator",
    "category": "agency_economics",
    "categoryLabel": "Agency Economics",
    "status": "live",
    "owner": "Commercial Agent",
    "icp": [
      "Creative Studios",
      "Web & App Agencies",
      "Consulting Practices",
      "Video Production Houses"
    ],
    "jobToBeDone": "Calculate defensible project fee floors, risk buffers, and 3-stage milestone schedules with 18% GST tax segregation.",
    "targetSearchCluster": [
      "agency project pricing calculator",
      "service project cost estimator",
      "how to price a website project",
      "agency milestone payment schedule calculator"
    ],
    "metadataTitle": "Service Project Pricing Calculator | Calculate Scope, Margin & Milestones",
    "metadataDescription": "Calculate defensible fixed-fee project quotes, contingency risk buffers, deposit recommendations, and 18% GST tax breakdowns. Free & private.",
    "canonicalUrl": "https://heycora.in/tools/service-pricing-calculator",
    "inputs": [
      {
        "name": "serviceType",
        "type": "select",
        "description": "Project discipline (Web & App, Brand Identity, Video Production, Marketing Sprint, Custom Software)"
      },
      {
        "name": "estimatedDeliveryDays",
        "type": "number",
        "description": "Project duration in working calendar days"
      },
      {
        "name": "seniorHours",
        "type": "number",
        "description": "Senior Lead / Strategy / Architecture hours"
      },
      {
        "name": "seniorHourlyRate",
        "type": "currency",
        "description": "Internal senior hourly rate"
      },
      {
        "name": "midHours",
        "type": "number",
        "description": "Production / Design / Development hours"
      },
      {
        "name": "midHourlyRate",
        "type": "currency",
        "description": "Internal production hourly rate"
      },
      {
        "name": "overheadFactorPct",
        "type": "percentage",
        "description": "Studio operational overhead percentage (software, workspace, admin)"
      },
      {
        "name": "targetMarginPct",
        "type": "percentage",
        "description": "Target gross profit margin (typically 40% - 65%)"
      },
      {
        "name": "scopeRiskLevel",
        "type": "select",
        "description": "Client risk profile and scope clarity (Clear Spec, Medium Variance, High Uncertainty)"
      },
      {
        "name": "taxRegime",
        "type": "select",
        "description": "Tax structure (18% GST Intra-State, 18% GST Inter-State, or 0% Export of Services)"
      }
    ],
    "outputs": [
      {
        "name": "baseLabourCost",
        "type": "currency",
        "description": "Direct team labor cost required to execute deliverables"
      },
      {
        "name": "overheadAllocation",
        "type": "currency",
        "description": "Operational overhead cost allocated to this project"
      },
      {
        "name": "costFloor",
        "type": "currency",
        "description": "Absolute break-even cost floor before profit"
      },
      {
        "name": "contingencyBuffer",
        "type": "currency",
        "description": "Recommended risk contingency buffer for scope uncertainty"
      },
      {
        "name": "recommendedFixedFee",
        "type": "currency",
        "description": "Recommended profitable project fee excluding taxes"
      },
      {
        "name": "gstAmount",
        "type": "currency",
        "description": "18% GST tax liability (CGST/SGST or IGST)"
      },
      {
        "name": "totalInvoicePrice",
        "type": "currency",
        "description": "Final client-facing contract amount including taxes"
      },
      {
        "name": "depositRecommendation",
        "type": "currency",
        "description": "Recommended 50% advance deposit to begin work"
      },
      {
        "name": "milestoneSchedule",
        "type": "array",
        "description": "Structured 50-25-25 milestone release schedule"
      }
    ],
    "features": [
      "Direct Labor + Overhead Cost Floor",
      "Scope Risk Contingency Algorithm",
      "50-25-25 Milestone Schedule",
      "18% GST Compliant Tax Segregation",
      "Fixed vs Retainer vs Hourly Comparison",
      "Proposal-Ready Statement"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser. No labor rates, project estimates, or margin targets are stored or transmitted.",
    "securityRisk": "none",
    "relatedTools": [
      "scope-creep-calculator",
      "gst-calculator",
      "retainer-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Turn this estimate into a live, e-signable client proposal.",
      "description": "Convert these milestone deliverables and pricing into a professional proposal with built-in digital contract signing and instant UPI QR payments.",
      "href": "/features/canvas-builder",
      "label": "Create Proposal in Cora"
    },
    "analyticsIdentifier": "service_pricing_calculator",
    "publicationDate": "2026-10-11",
    "lastReviewedDate": "2026-10-11"
  },
  {
    "slug": "wp-publishing-friction-calculator",
    "publicTitle": "WordPress Publishing Friction & Production Cost Calculator",
    "shortTitle": "Publishing Friction Calculator",
    "category": "wordpress_stack",
    "categoryLabel": "WordPress & Stack",
    "status": "live",
    "owner": "Economics Agent",
    "icp": [
      "Content Marketing Agencies",
      "Publishers",
      "SEO Teams",
      "WordPress Blog Operators"
    ],
    "jobToBeDone": "Calculate hours and payroll wasted per article on formatting, copy-paste handoffs, broken Gutenberg blocks, and manual SEO metadata entry.",
    "targetSearchCluster": [
      "wordpress publishing friction calculator",
      "cost of publishing blog post wordpress",
      "content production bottleneck calculator",
      "wordpress workflow waste"
    ],
    "metadataTitle": "WordPress Publishing Friction Calculator | Calculate Hidden Content Costs",
    "metadataDescription": "Calculate how manual copy-paste handoffs, block formatting friction, and multi-tool workflows inflate your cost per published article. Free & private.",
    "canonicalUrl": "https://heycora.in/tools/wp-publishing-friction-calculator",
    "inputs": [
      {
        "name": "monthlyArticlesPublished",
        "type": "number",
        "description": "Number of articles, case studies, or pages published per month"
      },
      {
        "name": "researchDraftingHours",
        "type": "number",
        "description": "Hours spent researching and drafting in Docs/Notion per article"
      },
      {
        "name": "formattingStagingMinutes",
        "type": "number",
        "description": "Minutes spent copying, fixing broken Gutenberg blocks, and formatting in WordPress"
      },
      {
        "name": "seoMetadataMinutes",
        "type": "number",
        "description": "Minutes spent setting up RankMath/Yoast, alt tags, schema, and social meta"
      },
      {
        "name": "approvalReviewMinutes",
        "type": "number",
        "description": "Minutes spent in review loops and revision handoffs between tools"
      },
      {
        "name": "blendedEditorHourlyCost",
        "type": "currency",
        "description": "Blended hourly cost of content manager, editor, or SEO specialist"
      },
      {
        "name": "toolHandoffCount",
        "type": "number",
        "description": "Number of disconnected tools used (Docs, Figma, Grammarly, WordPress, Asana, Drive)"
      }
    ],
    "outputs": [
      {
        "name": "totalTimePerArticleHours",
        "type": "number",
        "description": "Total end-to-end hours required to produce and publish one article"
      },
      {
        "name": "frictionHoursPerArticle",
        "type": "number",
        "description": "Hours spent purely on formatting, handoffs, and tool friction per article"
      },
      {
        "name": "annualFrictionLabourCost",
        "type": "currency",
        "description": "Annual payroll cost spent on non-creative mechanical publishing tasks"
      },
      {
        "name": "primaryBottleneckStage",
        "type": "string",
        "description": "The exact production stage causing the highest operational delay"
      },
      {
        "name": "achievableCadenceBoostPct",
        "type": "percentage",
        "description": "Potential content throughput increase with a unified publishing workflow"
      },
      {
        "name": "recommendedWorkflowRedesign",
        "type": "array",
        "description": "Specific steps to eliminate copy-paste handoffs and automate meta insertion"
      }
    ],
    "features": [
      "Per-Article Friction Time Breakdown",
      "Annual Hidden Publishing Payroll Cost",
      "Bottleneck Stage Detector",
      "Production Cadence Multiplier",
      "Workflow Optimization Checklist"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser. No articles, rates, or publishing metrics are saved or sent over the network.",
    "securityRisk": "none",
    "relatedTools": [
      "wp-plugin-cost-calculator",
      "ai-crawler-checker",
      "service-pricing-calculator"
    ],
    "supportingArticles": [
      "/articles/website-content/why-our-agency-published-no-articles-for-two-months"
    ],
    "supportingDocs": [
      "/docs/platform-architecture"
    ],
    "contextualCoraAction": {
      "title": "Publish directly from unified AI research to live indexed articles.",
      "description": "Eliminate 4 copy-paste handoffs. Draft, optimize for GEO/SEO, and publish directly to your website with Cora Content AI.",
      "href": "/features/content-ai",
      "label": "Streamline Publishing with Cora"
    },
    "analyticsIdentifier": "wp_publishing_friction_calculator",
    "publicationDate": "2026-10-11",
    "lastReviewedDate": "2026-10-11"
  },
  {
    "slug": "ai-crawler-checker",
    "publicTitle": "AI Crawler Access & robots.txt Directive Checker",
    "shortTitle": "AI Crawler Access Checker",
    "category": "ai_discovery",
    "categoryLabel": "AI & Discovery",
    "status": "live",
    "owner": "Search Agent",
    "icp": [
      "SEO Agencies",
      "Web Developers",
      "Content Publishers",
      "Brand Directors"
    ],
    "jobToBeDone": "Audit whether OpenAI OAI-SearchBot, GPTBot, ClaudeBot, Claude-SearchBot, and PerplexityBot are allowed or blocked by robots.txt directives and meta tags.",
    "targetSearchCluster": [
      "check OAI-SearchBot",
      "AI robots.txt tester",
      "Claude crawler checker",
      "is my website visible in ChatGPT",
      "PerplexityBot robots.txt test"
    ],
    "metadataTitle": "AI Crawler Access Checker | Test OAI-SearchBot, ClaudeBot & PerplexityBot",
    "metadataDescription": "Audit your robots.txt and meta directives for OpenAI OAI-SearchBot, GPTBot, ClaudeBot, and PerplexityBot. Generate an optimized AI crawler policy.",
    "canonicalUrl": "https://heycora.in/tools/ai-crawler-checker",
    "inputs": [
      {
        "name": "robotsTxtContent",
        "type": "text",
        "description": "Raw content of your website's robots.txt file or meta directives"
      },
      {
        "name": "targetUrlOptional",
        "type": "url",
        "description": "Optional specific URL path to test against directive rules (e.g. /blog/ or /pricing)"
      }
    ],
    "outputs": [
      {
        "name": "oaiSearchBotStatus",
        "type": "status",
        "description": "Permission status for OpenAI real-time search discovery crawler"
      },
      {
        "name": "gptBotStatus",
        "type": "status",
        "description": "Permission status for OpenAI foundational model training crawler"
      },
      {
        "name": "claudeSearchBotStatus",
        "type": "status",
        "description": "Permission status for Anthropic real-time retrieval crawler"
      },
      {
        "name": "claudeBotStatus",
        "type": "status",
        "description": "Permission status for Anthropic foundational model training crawler"
      },
      {
        "name": "perplexityBotStatus",
        "type": "status",
        "description": "Permission status for Perplexity AI conversational indexer"
      },
      {
        "name": "googlebotStatus",
        "type": "status",
        "description": "Permission status for Google search indexing crawler"
      },
      {
        "name": "bingbotStatus",
        "type": "status",
        "description": "Permission status for Microsoft Bing / Copilot crawler"
      },
      {
        "name": "aiSearchReadinessScore",
        "type": "score",
        "description": "Overall AI crawler discoverability rating from 0 to 100"
      },
      {
        "name": "criticalFindings",
        "type": "array",
        "description": "Detected syntax errors, accidental broad disallows, and meta tag conflicts"
      },
      {
        "name": "recommendedRobotsTxt",
        "type": "text",
        "description": "Optimized, publisher-safe robots.txt policy tailored to your preferences"
      }
    ],
    "features": [
      "Real-time Local Directive Parser",
      "OAI-SearchBot Search vs Training Differentiation",
      "Anthropic ClaudeBot vs Claude-SearchBot Inspection",
      "Syntax Error & Wildcard Conflict Detector",
      "1-Click Copy Optimized robots.txt",
      "100% Private Client-Side Engine"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% Client-Side. Your pasted robots.txt and directives are parsed locally in browser JavaScript and never stored.",
    "securityRisk": "none",
    "relatedTools": [
      "wp-publishing-friction-calculator",
      "wp-plugin-cost-calculator",
      "lead-leakage-calculator"
    ],
    "supportingArticles": [
      "/articles/website-content/why-our-agency-published-no-articles-for-two-months"
    ],
    "supportingDocs": [
      "/docs/platform-architecture"
    ],
    "contextualCoraAction": {
      "title": "Connect what your website promises with the workflow delivering it.",
      "description": "Ensure AI search engines discover structured, high-authority case studies and live service availability powered by Cora RAG Memory.",
      "href": "/features/rag-mcp",
      "label": "Explore Cora RAG Memory"
    },
    "analyticsIdentifier": "ai_crawler_checker",
    "publicationDate": "2026-10-11",
    "lastReviewedDate": "2026-10-11"
  },
  {
    "slug": "gst-calculator",
    "publicTitle": "Indian GST & B2B Tax Calculator",
    "shortTitle": "GST Tax Calculator",
    "category": "finance_tax",
    "categoryLabel": "Finance & Tax",
    "status": "live",
    "owner": "Commercial Agent",
    "icp": [
      "Indian Agencies",
      "CA & Tax Firms",
      "Freelance Creators",
      "Consultants"
    ],
    "jobToBeDone": "Calculate inclusive and exclusive 18% SAC 9983 GST tax math with CGST, SGST, and IGST segregation.",
    "targetSearchCluster": [
      "gst calculator india",
      "18 percent gst calculator",
      "b2b gst invoice calculator",
      "cgst sgst calculator"
    ],
    "metadataTitle": "Indian GST Calculator Online | 18%, 12%, 5% CGST SGST Tax Math",
    "metadataDescription": "Calculate GST inclusive and exclusive pricing, split intra-state vs inter-state tax liabilities for client proposals. Free & private.",
    "canonicalUrl": "https://heycora.in/tools/gst-calculator",
    "inputs": [
      {
        "name": "baseAmount",
        "type": "currency",
        "description": "Invoice or proposal value"
      },
      {
        "name": "gstRate",
        "type": "percentage",
        "description": "Applicable GST slab (18%, 12%, 5%, 28%)"
      },
      {
        "name": "isInclusive",
        "type": "boolean",
        "description": "Toggle tax inclusive vs exclusive"
      }
    ],
    "outputs": [
      {
        "name": "netAmount",
        "type": "currency",
        "description": "Base taxable amount"
      },
      {
        "name": "totalGst",
        "type": "currency",
        "description": "Total GST tax liability"
      },
      {
        "name": "grossAmount",
        "type": "currency",
        "description": "Total invoice payable amount"
      }
    ],
    "features": [
      "18% SAC 9983 Support",
      "CGST & SGST 50/50 Split",
      "IGST Inter-State Toggle",
      "1-Click Breakdown Copy"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Tax figures are calculated locally.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "retainer-calculator",
      "upi-qr-generator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Automate 18% GST Invoicing with instant UPI QR settlements in Cora.",
      "description": "Generate compliant GST invoices with automated tax ledgering.",
      "href": "/features/gst-invoicing",
      "label": "Use Cora GST Invoicing"
    },
    "analyticsIdentifier": "gst_calculator",
    "publicationDate": "2026-08-24",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "retainer-calculator",
    "publicTitle": "Service Retainer & Scope Buffer Calculator",
    "shortTitle": "Retainer Math Calculator",
    "category": "agency_economics",
    "categoryLabel": "Agency Economics",
    "status": "live",
    "owner": "Commercial Agent",
    "icp": [
      "Agencies",
      "Consultants",
      "Design Studios"
    ],
    "jobToBeDone": "Calculate sustainable monthly retainer tiers with capacity utilization and scope buffers.",
    "targetSearchCluster": [
      "agency retainer calculator",
      "monthly retainer math",
      "hourly to retainer calculator"
    ],
    "metadataTitle": "Service Retainer Calculator | Calculate Monthly Retainer Packages",
    "metadataDescription": "Calculate optimal monthly agency retainer tiers and billable hourly baseline rates with automatic 20% scope buffers.",
    "canonicalUrl": "https://heycora.in/tools/retainer-calculator",
    "inputs": [
      {
        "name": "targetMonthlyRevenue",
        "type": "currency",
        "description": "Target monthly studio revenue"
      },
      {
        "name": "billableCapacityHours",
        "type": "number",
        "description": "Max billable hours per month"
      }
    ],
    "outputs": [
      {
        "name": "hourlyBaselineRate",
        "type": "currency",
        "description": "Baseline break-even hourly rate"
      },
      {
        "name": "retainerTiers",
        "type": "array",
        "description": "Calculated Starter, Growth, and Enterprise retainer packages"
      }
    ],
    "features": [
      "Hourly to Retainer Conversion",
      "20% Scope Buffer",
      "Capacity Math"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution.",
    "securityRisk": "none",
    "relatedTools": [
      "scope-creep-calculator",
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage recurring retainer billing in Cora.",
      "description": "Automate client retainer collection with zero gateway fees.",
      "href": "/features/gst-invoicing",
      "label": "Manage Retainers in Cora"
    },
    "analyticsIdentifier": "retainer_calculator",
    "publicationDate": "2026-08-31",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "contract-builder",
    "publicTitle": "Indian IT Act 2000 Contract Clause Builder",
    "shortTitle": "Contract Clause Builder",
    "category": "contracts_legal",
    "categoryLabel": "Contracts & Legal",
    "status": "live",
    "owner": "Commercial Agent",
    "icp": [
      "Law Firms",
      "Consultants",
      "Agencies",
      "Studios"
    ],
    "jobToBeDone": "Generate legal service clauses, milestone escrow terms, and Section 10A digital agreements.",
    "targetSearchCluster": [
      "service agreement contract builder",
      "it act 2000 contract generator",
      "agency client contract template"
    ],
    "metadataTitle": "Contract Clause Builder | IT Act 2000 Compliant Legal Terms",
    "metadataDescription": "Build customized legal agreements with Section 10A digital signature recognition and milestone escrow clauses.",
    "canonicalUrl": "https://heycora.in/tools/contract-builder",
    "inputs": [
      {
        "name": "clientName",
        "type": "text",
        "description": "Client company name"
      },
      {
        "name": "retainerAmount",
        "type": "currency",
        "description": "Contract fee amount"
      }
    ],
    "outputs": [
      {
        "name": "legalClauseDraft",
        "type": "text",
        "description": "Generated compliant contract agreement text"
      }
    ],
    "features": [
      "Section 10A Digital Signatures",
      "Milestone Escrow",
      "IP Assignment Terms"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser. No client or contract data stored.",
    "securityRisk": "none",
    "relatedTools": [
      "esign-pdf",
      "service-pricing-calculator",
      "agency-proposal-generator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-automate-shoot-booking-and-contracts"
    ],
    "supportingDocs": [
      "/docs/multi-tenant-rbac"
    ],
    "contextualCoraAction": {
      "title": "Store signed contracts in Cora E-Sign Vault.",
      "description": "Collect legally binding digital signatures with audit trails.",
      "href": "/features/esign-vault",
      "label": "Open Cora E-Sign Vault"
    },
    "analyticsIdentifier": "contract_builder",
    "publicationDate": "2026-08-31",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "upi-qr-generator",
    "publicTitle": "Dynamic UPI QR & Payment Intent Generator",
    "shortTitle": "UPI QR Generator",
    "category": "developer_embed",
    "categoryLabel": "Developer & Embeds",
    "status": "live",
    "owner": "Commercial Agent",
    "icp": [
      "Indian Freelancers",
      "Agencies",
      "Small Businesses"
    ],
    "jobToBeDone": "Generate instant zero-fee UPI QR codes with custom invoice amounts and payee VPAs.",
    "targetSearchCluster": [
      "upi qr generator with amount",
      "dynamic upi payment link generator",
      "zero fee upi invoice qr"
    ],
    "metadataTitle": "Dynamic UPI QR Code Generator | 0% Fee Payment Intent",
    "metadataDescription": "Create zero-fee dynamic UPI payment links and printable QR codes for Indian bank settlements. Google Pay, PhonePe, Paytm ready.",
    "canonicalUrl": "https://heycora.in/tools/upi-qr-generator",
    "inputs": [
      {
        "name": "upiVpa",
        "type": "text",
        "description": "Payee VPA address"
      },
      {
        "name": "amount",
        "type": "currency",
        "description": "Exact invoice amount"
      }
    ],
    "outputs": [
      {
        "name": "qrCanvas",
        "type": "image",
        "description": "Rendered UPI QR code PNG/SVG"
      }
    ],
    "features": [
      "0% Payment Gateway Fees",
      "Exact Amount Embedding",
      "Instant Client-Side QR Render"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser. VPA and transaction details processed locally.",
    "securityRisk": "none",
    "relatedTools": [
      "gst-calculator",
      "service-pricing-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Embed dynamic UPI QR codes automatically in Cora Invoices.",
      "description": "Collect client payments directly to your bank account with zero platform fees.",
      "href": "/features/gst-invoicing",
      "label": "Use Cora Invoicing"
    },
    "analyticsIdentifier": "upi_qr_generator",
    "publicationDate": "2026-08-31",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "embed-builder",
    "publicTitle": "Autonomous AI Copilot & Embed Builder",
    "shortTitle": "Embed Widget Builder",
    "category": "developer_embed",
    "categoryLabel": "Developer & Embeds",
    "status": "live",
    "owner": "Search Agent",
    "icp": [
      "Web Developers",
      "Agencies",
      "Framer/Webflow Designers"
    ],
    "jobToBeDone": "Generate lightweight embeddable booking and lead capture widgets for Framer, Webflow, and WordPress.",
    "targetSearchCluster": [
      "ai chatbot embed builder",
      "framer lead widget builder",
      "webflow booking embed"
    ],
    "metadataTitle": "AI Booking & Lead Embed Widget Builder | Framer & Webflow",
    "metadataDescription": "Generate zero-code iframe embeds to capture leads and book client call slots on Framer, Webflow, and WordPress.",
    "canonicalUrl": "https://heycora.in/tools/embed-builder",
    "inputs": [
      {
        "name": "workspaceDomain",
        "type": "text",
        "description": "Cora workspace slug or domain"
      }
    ],
    "outputs": [
      {
        "name": "embedCode",
        "type": "text",
        "description": "Responsive iframe HTML code snippet"
      }
    ],
    "features": [
      "Zero-Code Snippet",
      "Light / Dark Mode",
      "Direct Webhook Sync"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% Client-Side Execution.",
    "securityRisk": "none",
    "relatedTools": [
      "lead-leakage-calculator",
      "wp-plugin-cost-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-automate-shoot-booking-and-contracts"
    ],
    "supportingDocs": [
      "/docs/platform-architecture"
    ],
    "contextualCoraAction": {
      "title": "Sync embed bookings directly to Cora Lead CRM.",
      "description": "Manage all client enquiries in a central pipeline.",
      "href": "/features/lead-crm",
      "label": "Explore Cora Lead CRM"
    },
    "analyticsIdentifier": "embed_builder",
    "publicationDate": "2026-08-24",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "agency-proposal-generator",
    "publicTitle": "Free Agency Proposal & Scope Generator",
    "shortTitle": "Agency Proposal Generator",
    "category": "agency_economics",
    "categoryLabel": "Agency Economics",
    "status": "live",
    "owner": "Commercial Agent",
    "icp": [
      "Digital Agencies",
      "Creative Studios",
      "Freelancers"
    ],
    "jobToBeDone": "Generate structured agency proposals with deliverables, timelines, milestone payments, and terms.",
    "targetSearchCluster": [
      "agency proposal generator free",
      "freelance proposal builder",
      "scope of work proposal generator"
    ],
    "metadataTitle": "Free Agency Proposal & Scope Generator | Cora Studio",
    "metadataDescription": "Generate professional agency proposals, scope breakdowns, and milestone terms in 3 minutes. Free & private.",
    "canonicalUrl": "https://heycora.in/tools/agency-proposal-generator",
    "inputs": [
      {
        "name": "projectName",
        "type": "text",
        "description": "Project title"
      },
      {
        "name": "deliverables",
        "type": "text",
        "description": "Scope items"
      }
    ],
    "outputs": [
      {
        "name": "proposalDocument",
        "type": "text",
        "description": "Structured proposal markdown / PDF"
      }
    ],
    "features": [
      "Deliverable Scoping",
      "Milestone Schedule",
      "1-Click PDF Export"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser. No proposal data retained.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "contract-builder",
      "scope-creep-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Send interactive, e-signable proposals with Cora.",
      "description": "Track proposal views and collect digital signatures seamlessly.",
      "href": "/features/canvas-builder",
      "label": "Build Proposals in Cora"
    },
    "analyticsIdentifier": "agency_proposal_generator",
    "publicationDate": "2026-09-23",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "listing-ai",
    "publicTitle": "Real Estate & Studio Listing AI Generator",
    "shortTitle": "Listing AI Generator",
    "category": "real_estate",
    "categoryLabel": "Real Estate",
    "status": "live",
    "owner": "Search Agent",
    "icp": [
      "Real Estate Brokers",
      "Property Developers",
      "Photo Studios"
    ],
    "jobToBeDone": "Transform property features and shoot locations into luxury marketing brochures and listings copy.",
    "targetSearchCluster": [
      "real estate listing description generator",
      "property description ai",
      "luxury real estate copy generator"
    ],
    "metadataTitle": "Real Estate Listing AI Generator | High-Converting Property Copy",
    "metadataDescription": "Generate luxury real estate brochures, MLS copy, and social posts from property bullet points in seconds.",
    "canonicalUrl": "https://heycora.in/tools/listing-ai",
    "inputs": [
      {
        "name": "propertyDetails",
        "type": "text",
        "description": "Specs, amenities, and location"
      }
    ],
    "outputs": [
      {
        "name": "listingCopy",
        "type": "text",
        "description": "Typeset brochure and social copy"
      }
    ],
    "features": [
      "Luxury & Commercial Tone",
      "Social Presets",
      "Multi-Format Export"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "embed-builder"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-automate-shoot-booking-and-contracts"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage property listings and client tours in Cora Real Estate Workspace.",
      "description": "Pre-seeded listing catalogs and client booking pipelines.",
      "href": "/industries/real-estate-property",
      "label": "Explore Real Estate Workspace"
    },
    "analyticsIdentifier": "listing_ai",
    "publicationDate": "2026-08-24",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "elementor-migrator",
    "publicTitle": "Elementor to Cora Website Migrator",
    "shortTitle": "Elementor Migrator",
    "category": "wordpress_stack",
    "categoryLabel": "WordPress & Stack",
    "status": "live",
    "owner": "Search Agent",
    "icp": [
      "WordPress Agencies",
      "Web Designers",
      "Elementor Site Owners"
    ],
    "jobToBeDone": "Extract layout schemas from Elementor pages and map them to clean, lightweight, high-speed blocks.",
    "targetSearchCluster": [
      "elementor to nextjs migrator",
      "convert elementor to lightweight html",
      "elementor export tool"
    ],
    "metadataTitle": "Elementor Website Migrator | Convert Heavy Pages to High-Speed Layouts",
    "metadataDescription": "Audit and migrate heavy Elementor landing pages into clean, sub-second loading layouts without DOM bloat.",
    "canonicalUrl": "https://heycora.in/tools/elementor-migrator",
    "inputs": [
      {
        "name": "elementorJson",
        "type": "text",
        "description": "Elementor exported template JSON or layout markup"
      }
    ],
    "outputs": [
      {
        "name": "cleanedSchema",
        "type": "text",
        "description": "Optimized lightweight block structure"
      }
    ],
    "features": [
      "DOM Bloat Removal",
      "Clean Component Extraction",
      "Zero Cloud Storage"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% Client-Side. Template JSON parsed in browser RAM.",
    "securityRisk": "none",
    "relatedTools": [
      "wp-plugin-cost-calculator",
      "wp-publishing-friction-calculator"
    ],
    "supportingArticles": [
      "/articles/website-content/why-our-agency-published-no-articles-for-two-months"
    ],
    "supportingDocs": [
      "/docs/platform-architecture"
    ],
    "contextualCoraAction": {
      "title": "Host ultra-fast landing pages natively in Cora Canvas.",
      "description": "Sub-50ms paint speeds with zero WordPress maintenance overhead.",
      "href": "/features/canvas-builder",
      "label": "Explore Cora Canvas"
    },
    "analyticsIdentifier": "elementor_migrator",
    "publicationDate": "2026-09-06",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "merge-pdf",
    "publicTitle": "Merge PDF Files Online Free",
    "shortTitle": "Merge PDF Files",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Combine multiple PDF documents with drag-and-drop page reordering.",
    "targetSearchCluster": [
      "merge pdf files free",
      "merge pdf online",
      "private merge pdf files"
    ],
    "metadataTitle": "Merge PDF Files Online Free | Cora Free Online Tools",
    "metadataDescription": "Combine multiple PDF documents with drag-and-drop page reordering. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/merge-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "merge_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "split-pdf",
    "publicTitle": "Split & Extract PDF Pages",
    "shortTitle": "Split & Extract PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Separate PDF pages or extract custom page ranges into individual documents.",
    "targetSearchCluster": [
      "split & extract pdf free",
      "split pdf online",
      "private split & extract pdf"
    ],
    "metadataTitle": "Split & Extract PDF Pages | Cora Free Online Tools",
    "metadataDescription": "Separate PDF pages or extract custom page ranges into individual documents. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/split-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "split_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "compress-pdf",
    "publicTitle": "Compress PDF Online Free",
    "shortTitle": "Compress PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Reduce PDF file size up to 70% in seconds while preserving crisp vector text.",
    "targetSearchCluster": [
      "compress pdf free",
      "compress pdf online",
      "private compress pdf"
    ],
    "metadataTitle": "Compress PDF Online Free | Cora Free Online Tools",
    "metadataDescription": "Reduce PDF file size up to 70% in seconds while preserving crisp vector text. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/compress-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "compress_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "esign-pdf",
    "publicTitle": "Digital eSign PDF & Contract Signer",
    "shortTitle": "Digital eSign PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Draw or type your signature and place it securely on any PDF agreement.",
    "targetSearchCluster": [
      "digital esign pdf free",
      "esign pdf online",
      "private digital esign pdf"
    ],
    "metadataTitle": "Digital eSign PDF & Contract Signer | Cora Free Online Tools",
    "metadataDescription": "Draw or type your signature and place it securely on any PDF agreement. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/esign-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "esign_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "watermark-pdf",
    "publicTitle": "Add Watermark to PDF Documents",
    "shortTitle": "Add Watermark to PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Stamp text or image watermarks across PDF pages with opacity controls.",
    "targetSearchCluster": [
      "add watermark to pdf free",
      "watermark pdf online",
      "private add watermark to pdf"
    ],
    "metadataTitle": "Add Watermark to PDF Documents | Cora Free Online Tools",
    "metadataDescription": "Stamp text or image watermarks across PDF pages with opacity controls. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/watermark-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "watermark_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "rotate-pdf",
    "publicTitle": "Rotate PDF Pages Permanently",
    "shortTitle": "Rotate PDF Pages",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Rotate orientation of individual or all pages in your PDF document permanently.",
    "targetSearchCluster": [
      "rotate pdf pages free",
      "rotate pdf online",
      "private rotate pdf pages"
    ],
    "metadataTitle": "Rotate PDF Pages Permanently | Cora Free Online Tools",
    "metadataDescription": "Rotate orientation of individual or all pages in your PDF document permanently. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/rotate-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "rotate_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "images-to-pdf",
    "publicTitle": "Images to PDF Converter (JPG, PNG, WebP)",
    "shortTitle": "Images to PDF Converter",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Convert photos, design mockups, and receipts into a unified high-res PDF.",
    "targetSearchCluster": [
      "images to pdf converter free",
      "images to pdf online",
      "private images to pdf converter"
    ],
    "metadataTitle": "Images to PDF Converter (JPG, PNG, WebP) | Cora Free Online Tools",
    "metadataDescription": "Convert photos, design mockups, and receipts into a unified high-res PDF. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/images-to-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "images_to_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "word-to-pdf",
    "publicTitle": "Word to PDF Converter Online",
    "shortTitle": "Word to PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Transform raw document text, terms, and drafts into clean typeset A4 PDF files.",
    "targetSearchCluster": [
      "word to pdf free",
      "word to pdf online",
      "private word to pdf"
    ],
    "metadataTitle": "Word to PDF Converter Online | Cora Free Online Tools",
    "metadataDescription": "Transform raw document text, terms, and drafts into clean typeset A4 PDF files. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/word-to-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "word_to_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "number-pdf",
    "publicTitle": "Add Page Numbers to PDF",
    "shortTitle": "Add Page Numbers",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Stamp automated pagination headers and footers with customizable format and offsets.",
    "targetSearchCluster": [
      "add page numbers free",
      "number pdf online",
      "private add page numbers"
    ],
    "metadataTitle": "Add Page Numbers to PDF | Cora Free Online Tools",
    "metadataDescription": "Stamp automated pagination headers and footers with customizable format and offsets. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/number-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "number_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "remove-pages",
    "publicTitle": "Remove Pages from PDF",
    "shortTitle": "Remove Pages",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Delete unwanted sheets, blank pages, or outdated appendixes from your PDF.",
    "targetSearchCluster": [
      "remove pages free",
      "remove pages online",
      "private remove pages"
    ],
    "metadataTitle": "Remove Pages from PDF | Cora Free Online Tools",
    "metadataDescription": "Delete unwanted sheets, blank pages, or outdated appendixes from your PDF. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/remove-pages",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "remove_pages",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "ai-pdf-summarizer",
    "publicTitle": "AI PDF Summarizer & Contract Scanner",
    "shortTitle": "AI PDF Summarizer",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Scan contracts, find hidden risk clauses, and extract executive summaries in seconds.",
    "targetSearchCluster": [
      "ai pdf summarizer free",
      "ai pdf summarizer online",
      "private ai pdf summarizer"
    ],
    "metadataTitle": "AI PDF Summarizer & Contract Scanner | Cora Free Online Tools",
    "metadataDescription": "Scan contracts, find hidden risk clauses, and extract executive summaries in seconds. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/ai-pdf-summarizer",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "ai_pdf_summarizer",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "compare-pdf",
    "publicTitle": "Compare Two PDF Documents",
    "shortTitle": "Compare PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Visual diff tool highlighting changes and additions between two PDF revisions.",
    "targetSearchCluster": [
      "compare pdf free",
      "compare pdf online",
      "private compare pdf"
    ],
    "metadataTitle": "Compare Two PDF Documents | Cora Free Online Tools",
    "metadataDescription": "Visual diff tool highlighting changes and additions between two PDF revisions. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/compare-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "compare_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "crop-pdf",
    "publicTitle": "Crop PDF Document Margins",
    "shortTitle": "Crop PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Trim unwanted white margins and standardize page dimensions in browser.",
    "targetSearchCluster": [
      "crop pdf free",
      "crop pdf online",
      "private crop pdf"
    ],
    "metadataTitle": "Crop PDF Document Margins | Cora Free Online Tools",
    "metadataDescription": "Trim unwanted white margins and standardize page dimensions in browser. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/crop-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "crop_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "excel-to-pdf",
    "publicTitle": "Excel & Spreadsheet to PDF Converter",
    "shortTitle": "Excel to PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Render formatted spreadsheet tables into standardized A4 landscape/portrait PDF.",
    "targetSearchCluster": [
      "excel to pdf free",
      "excel to pdf online",
      "private excel to pdf"
    ],
    "metadataTitle": "Excel & Spreadsheet to PDF Converter | Cora Free Online Tools",
    "metadataDescription": "Render formatted spreadsheet tables into standardized A4 landscape/portrait PDF. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/excel-to-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "excel_to_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "extract-pages",
    "publicTitle": "Extract PDF Pages to Separate Files",
    "shortTitle": "Extract Pages",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Isolate specific sheets or chapter pages into standalone downloadable PDFs.",
    "targetSearchCluster": [
      "extract pages free",
      "extract pages online",
      "private extract pages"
    ],
    "metadataTitle": "Extract PDF Pages to Separate Files | Cora Free Online Tools",
    "metadataDescription": "Isolate specific sheets or chapter pages into standalone downloadable PDFs. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/extract-pages",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "extract_pages",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "html-to-pdf",
    "publicTitle": "HTML & Markdown to PDF Converter",
    "shortTitle": "HTML to PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Convert styled HTML or Markdown documentation into print-ready PDF files.",
    "targetSearchCluster": [
      "html to pdf free",
      "html to pdf online",
      "private html to pdf"
    ],
    "metadataTitle": "HTML & Markdown to PDF Converter | Cora Free Online Tools",
    "metadataDescription": "Convert styled HTML or Markdown documentation into print-ready PDF files. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/html-to-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "html_to_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "ocr-pdf",
    "publicTitle": "OCR PDF Text Extractor",
    "shortTitle": "OCR PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Extract selectable, searchable text from scanned image PDFs in browser memory.",
    "targetSearchCluster": [
      "ocr pdf free",
      "ocr pdf online",
      "private ocr pdf"
    ],
    "metadataTitle": "OCR PDF Text Extractor | Cora Free Online Tools",
    "metadataDescription": "Extract selectable, searchable text from scanned image PDFs in browser memory. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/ocr-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "ocr_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "organize-pdf",
    "publicTitle": "Visual PDF Page Organizer & Sorter",
    "shortTitle": "Organize PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Drag, drop, rotate, and reorder PDF sheets visually in an interactive grid.",
    "targetSearchCluster": [
      "organize pdf free",
      "organize pdf online",
      "private organize pdf"
    ],
    "metadataTitle": "Visual PDF Page Organizer & Sorter | Cora Free Online Tools",
    "metadataDescription": "Drag, drop, rotate, and reorder PDF sheets visually in an interactive grid. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/organize-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "organize_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "pdf-to-excel",
    "publicTitle": "Convert PDF Tables to Excel (.xlsx)",
    "shortTitle": "PDF to Excel",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Extract tabular numbers and billing grids from PDF invoices into Excel spreadsheets.",
    "targetSearchCluster": [
      "pdf to excel free",
      "pdf to excel online",
      "private pdf to excel"
    ],
    "metadataTitle": "Convert PDF Tables to Excel (.xlsx) | Cora Free Online Tools",
    "metadataDescription": "Extract tabular numbers and billing grids from PDF invoices into Excel spreadsheets. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/pdf-to-excel",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "pdf_to_excel",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "pdf-to-jpg",
    "publicTitle": "Convert PDF Pages to High-Res JPG",
    "shortTitle": "PDF to JPG",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Rasterize PDF document pages into high-definition JPG images in memory.",
    "targetSearchCluster": [
      "pdf to jpg free",
      "pdf to jpg online",
      "private pdf to jpg"
    ],
    "metadataTitle": "Convert PDF Pages to High-Res JPG | Cora Free Online Tools",
    "metadataDescription": "Rasterize PDF document pages into high-definition JPG images in memory. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/pdf-to-jpg",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "pdf_to_jpg",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "pdf-to-markdown",
    "publicTitle": "Convert PDF to Clean Markdown",
    "shortTitle": "PDF to Markdown",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Extract formatted headings, tables, and bullet points from PDFs into Markdown.",
    "targetSearchCluster": [
      "pdf to markdown free",
      "pdf to markdown online",
      "private pdf to markdown"
    ],
    "metadataTitle": "Convert PDF to Clean Markdown | Cora Free Online Tools",
    "metadataDescription": "Extract formatted headings, tables, and bullet points from PDFs into Markdown. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/pdf-to-markdown",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "pdf_to_markdown",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "pdf-to-word",
    "publicTitle": "Convert PDF to Editable Document",
    "shortTitle": "PDF to Word",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Transform read-only PDF agreements into editable text documents.",
    "targetSearchCluster": [
      "pdf to word free",
      "pdf to word online",
      "private pdf to word"
    ],
    "metadataTitle": "Convert PDF to Editable Document | Cora Free Online Tools",
    "metadataDescription": "Transform read-only PDF agreements into editable text documents. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/pdf-to-word",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "pdf_to_word",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "powerpoint-to-pdf",
    "publicTitle": "PowerPoint Presentation to PDF Converter",
    "shortTitle": "PowerPoint to PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Convert slide decks into universal high-resolution PDF presentations.",
    "targetSearchCluster": [
      "powerpoint to pdf free",
      "powerpoint to pdf online",
      "private powerpoint to pdf"
    ],
    "metadataTitle": "PowerPoint Presentation to PDF Converter | Cora Free Online Tools",
    "metadataDescription": "Convert slide decks into universal high-resolution PDF presentations. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/powerpoint-to-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "powerpoint_to_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "protect-pdf",
    "publicTitle": "Password Protect & Encrypt PDF",
    "shortTitle": "Protect PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Encrypt PDF agreements with AES-128 passwords to restrict unauthorized access.",
    "targetSearchCluster": [
      "protect pdf free",
      "protect pdf online",
      "private protect pdf"
    ],
    "metadataTitle": "Password Protect & Encrypt PDF | Cora Free Online Tools",
    "metadataDescription": "Encrypt PDF agreements with AES-128 passwords to restrict unauthorized access. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/protect-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "protect_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "redact-pdf",
    "publicTitle": "Redact Sensitive Text & Numbers in PDF",
    "shortTitle": "Redact PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Black out confidential client numbers, bank details, and personal data permanently.",
    "targetSearchCluster": [
      "redact pdf free",
      "redact pdf online",
      "private redact pdf"
    ],
    "metadataTitle": "Redact Sensitive Text & Numbers in PDF | Cora Free Online Tools",
    "metadataDescription": "Black out confidential client numbers, bank details, and personal data permanently. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/redact-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "redact_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "repair-pdf",
    "publicTitle": "Repair Corrupted PDF Header Data",
    "shortTitle": "Repair PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Rebuild broken xref tables and damaged headers in unreadable PDF documents.",
    "targetSearchCluster": [
      "repair pdf free",
      "repair pdf online",
      "private repair pdf"
    ],
    "metadataTitle": "Repair Corrupted PDF Header Data | Cora Free Online Tools",
    "metadataDescription": "Rebuild broken xref tables and damaged headers in unreadable PDF documents. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/repair-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "repair_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "translate-pdf",
    "publicTitle": "Translate PDF Document Text",
    "shortTitle": "Translate PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Extract and translate multi-lingual contract text into English or target languages.",
    "targetSearchCluster": [
      "translate pdf free",
      "translate pdf online",
      "private translate pdf"
    ],
    "metadataTitle": "Translate PDF Document Text | Cora Free Online Tools",
    "metadataDescription": "Extract and translate multi-lingual contract text into English or target languages. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/translate-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "translate_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "unlock-pdf",
    "publicTitle": "Remove Password & Restrictions from PDF",
    "shortTitle": "Unlock PDF",
    "category": "pdf_tools",
    "categoryLabel": "PDF Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Remove print and copy restriction locks from password-known PDF documents.",
    "targetSearchCluster": [
      "unlock pdf free",
      "unlock pdf online",
      "private unlock pdf"
    ],
    "metadataTitle": "Remove Password & Restrictions from PDF | Cora Free Online Tools",
    "metadataDescription": "Remove print and copy restriction locks from password-known PDF documents. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/unlock-pdf",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "unlock_pdf",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "excel-formula-generator",
    "publicTitle": "AI Excel & Google Sheets Formula Generator",
    "shortTitle": "Formula Generator",
    "category": "sheets_tools",
    "categoryLabel": "Sheets & Excel",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Translate plain English descriptions into complex, error-free Excel and Sheets formulas.",
    "targetSearchCluster": [
      "formula generator free",
      "excel formula generator online",
      "private formula generator"
    ],
    "metadataTitle": "AI Excel & Google Sheets Formula Generator | Cora Free Online Tools",
    "metadataDescription": "Translate plain English descriptions into complex, error-free Excel and Sheets formulas. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/excel-formula-generator",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "excel_formula_generator",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "vlookup-generator",
    "publicTitle": "Visual VLOOKUP & XLOOKUP Builder",
    "shortTitle": "VLOOKUP Builder",
    "category": "sheets_tools",
    "categoryLabel": "Sheets & Excel",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Build accurate VLOOKUP and XLOOKUP formulas interactively without memorizing argument orders.",
    "targetSearchCluster": [
      "vlookup builder free",
      "vlookup generator online",
      "private vlookup builder"
    ],
    "metadataTitle": "Visual VLOOKUP & XLOOKUP Builder | Cora Free Online Tools",
    "metadataDescription": "Build accurate VLOOKUP and XLOOKUP formulas interactively without memorizing argument orders. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/vlookup-generator",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "vlookup_generator",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "clean-sheet-data",
    "publicTitle": "Clean & Format Spreadsheet Data",
    "shortTitle": "Clean Sheet Data",
    "category": "sheets_tools",
    "categoryLabel": "Sheets & Excel",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Normalize messy customer sheets, format phone numbers, trim spaces, and fix dates.",
    "targetSearchCluster": [
      "clean sheet data free",
      "clean sheet data online",
      "private clean sheet data"
    ],
    "metadataTitle": "Clean & Format Spreadsheet Data | Cora Free Online Tools",
    "metadataDescription": "Normalize messy customer sheets, format phone numbers, trim spaces, and fix dates. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/clean-sheet-data",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "clean_sheet_data",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "csv-to-excel",
    "publicTitle": "CSV to Excel Converter (.xlsx)",
    "shortTitle": "CSV to Excel",
    "category": "sheets_tools",
    "categoryLabel": "Sheets & Excel",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Convert CSV and TSV text files into clean, styled Excel workbooks (.xlsx) directly in memory.",
    "targetSearchCluster": [
      "csv to excel free",
      "csv to excel online",
      "private csv to excel"
    ],
    "metadataTitle": "CSV to Excel Converter (.xlsx) | Cora Free Online Tools",
    "metadataDescription": "Convert CSV and TSV text files into clean, styled Excel workbooks (.xlsx) directly in memory. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/csv-to-excel",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "csv_to_excel",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "excel-to-csv",
    "publicTitle": "Excel & Sheet to CSV Converter",
    "shortTitle": "Excel to CSV",
    "category": "sheets_tools",
    "categoryLabel": "Sheets & Excel",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Convert spreadsheet grids into escaped RFC 4180 standard CSV or TSV files with custom delimiters.",
    "targetSearchCluster": [
      "excel to csv free",
      "excel to csv online",
      "private excel to csv"
    ],
    "metadataTitle": "Excel & Sheet to CSV Converter | Cora Free Online Tools",
    "metadataDescription": "Convert spreadsheet grids into escaped RFC 4180 standard CSV or TSV files with custom delimiters. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/excel-to-csv",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "excel_to_csv",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "excel-to-json",
    "publicTitle": "Excel & CSV to JSON Converter",
    "shortTitle": "Excel to JSON",
    "category": "sheets_tools",
    "categoryLabel": "Sheets & Excel",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Convert tabular spreadsheet rows into structured JSON objects, 2D arrays, or indexed maps.",
    "targetSearchCluster": [
      "excel to json free",
      "excel to json online",
      "private excel to json"
    ],
    "metadataTitle": "Excel & CSV to JSON Converter | Cora Free Online Tools",
    "metadataDescription": "Convert tabular spreadsheet rows into structured JSON objects, 2D arrays, or indexed maps. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/excel-to-json",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "excel_to_json",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "remove-duplicates-csv",
    "publicTitle": "Remove Duplicates from CSV & Sheets",
    "shortTitle": "Remove Duplicates",
    "category": "sheets_tools",
    "categoryLabel": "Sheets & Excel",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Deduplicate spreadsheet rows by specific key columns or entire records with real-time stats.",
    "targetSearchCluster": [
      "remove duplicates free",
      "remove duplicates csv online",
      "private remove duplicates"
    ],
    "metadataTitle": "Remove Duplicates from CSV & Sheets | Cora Free Online Tools",
    "metadataDescription": "Deduplicate spreadsheet rows by specific key columns or entire records with real-time stats. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/remove-duplicates-csv",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "remove_duplicates_csv",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "merge-csv",
    "publicTitle": "Merge Multiple CSV Files",
    "shortTitle": "Merge CSV",
    "category": "sheets_tools",
    "categoryLabel": "Sheets & Excel",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Combine dozens of separate CSV spreadsheets into a single consolidated master table.",
    "targetSearchCluster": [
      "merge csv free",
      "merge csv online",
      "private merge csv"
    ],
    "metadataTitle": "Merge Multiple CSV Files | Cora Free Online Tools",
    "metadataDescription": "Combine dozens of separate CSV spreadsheets into a single consolidated master table. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/merge-csv",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "merge_csv",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "split-csv",
    "publicTitle": "Split CSV by Rows or Column Value",
    "shortTitle": "Split CSV",
    "category": "sheets_tools",
    "categoryLabel": "Sheets & Excel",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Split large CSV files into smaller chunks by row count limit or by unique column categories.",
    "targetSearchCluster": [
      "split csv free",
      "split csv online",
      "private split csv"
    ],
    "metadataTitle": "Split CSV by Rows or Column Value | Cora Free Online Tools",
    "metadataDescription": "Split large CSV files into smaller chunks by row count limit or by unique column categories. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/split-csv",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "split_csv",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "compress-image",
    "publicTitle": "Compress Image (JPG, PNG, WebP)",
    "shortTitle": "Compress Image",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Reduce JPG, PNG, and WebP file size up to 80% with visual quality and KB target controls.",
    "targetSearchCluster": [
      "compress image free",
      "compress image online",
      "private compress image"
    ],
    "metadataTitle": "Compress Image (JPG, PNG, WebP) | Cora Free Online Tools",
    "metadataDescription": "Reduce JPG, PNG, and WebP file size up to 80% with visual quality and KB target controls. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/compress-image",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "compress_image",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "resize-image",
    "publicTitle": "Resize Image Dimensions",
    "shortTitle": "Resize Image",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Scale image dimensions, DPI, and export preset social sizes with aspect-ratio locking.",
    "targetSearchCluster": [
      "resize image free",
      "resize image online",
      "private resize image"
    ],
    "metadataTitle": "Resize Image Dimensions | Cora Free Online Tools",
    "metadataDescription": "Scale image dimensions, DPI, and export preset social sizes with aspect-ratio locking. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/resize-image",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "resize_image",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "crop-image",
    "publicTitle": "Crop Image & Circular Cutout",
    "shortTitle": "Crop Image",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Crop photos with standard aspect ratios, zoom adjustments, and circular avatar masks.",
    "targetSearchCluster": [
      "crop image free",
      "crop image online",
      "private crop image"
    ],
    "metadataTitle": "Crop Image & Circular Cutout | Cora Free Online Tools",
    "metadataDescription": "Crop photos with standard aspect ratios, zoom adjustments, and circular avatar masks. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/crop-image",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "crop_image",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "convert-image",
    "publicTitle": "Image Format Converter (JPG, PNG, WebP)",
    "shortTitle": "Convert Image",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Convert between JPG, PNG, and WebP with background transparency fill options.",
    "targetSearchCluster": [
      "convert image free",
      "convert image online",
      "private convert image"
    ],
    "metadataTitle": "Image Format Converter (JPG, PNG, WebP) | Cora Free Online Tools",
    "metadataDescription": "Convert between JPG, PNG, and WebP with background transparency fill options. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/convert-image",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "convert_image",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "svg-to-png",
    "publicTitle": "SVG to PNG Vector Rasterizer",
    "shortTitle": "SVG to PNG",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Rasterize SVG vectors to crystal clear PNGs at 1x, 2x, 4x, and 8x print resolution.",
    "targetSearchCluster": [
      "svg to png free",
      "svg to png online",
      "private svg to png"
    ],
    "metadataTitle": "SVG to PNG Vector Rasterizer | Cora Free Online Tools",
    "metadataDescription": "Rasterize SVG vectors to crystal clear PNGs at 1x, 2x, 4x, and 8x print resolution. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/svg-to-png",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "svg_to_png",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "heic-to-jpg",
    "publicTitle": "HEIC to JPG iPhone Photo Converter",
    "shortTitle": "HEIC to JPG",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Convert Apple iPhone .heic and .heif photos to universal JPG or PNG format in memory.",
    "targetSearchCluster": [
      "heic to jpg free",
      "heic to jpg online",
      "private heic to jpg"
    ],
    "metadataTitle": "HEIC to JPG iPhone Photo Converter | Cora Free Online Tools",
    "metadataDescription": "Convert Apple iPhone .heic and .heif photos to universal JPG or PNG format in memory. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/heic-to-jpg",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "heic_to_jpg",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "remove-background",
    "publicTitle": "AI Background Remover (Transparent PNG)",
    "shortTitle": "Remove Background",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Isolate subjects and remove backgrounds with transparent PNG export and color backdrops.",
    "targetSearchCluster": [
      "remove background free",
      "remove background online",
      "private remove background"
    ],
    "metadataTitle": "AI Background Remover (Transparent PNG) | Cora Free Online Tools",
    "metadataDescription": "Isolate subjects and remove backgrounds with transparent PNG export and color backdrops. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/remove-background",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "remove_background",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "image-to-text",
    "publicTitle": "Image to Text OCR Scanner",
    "shortTitle": "Image to Text",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Extract text from receipts, book pages, infographics, and screenshots into editable copy.",
    "targetSearchCluster": [
      "image to text free",
      "image to text online",
      "private image to text"
    ],
    "metadataTitle": "Image to Text OCR Scanner | Cora Free Online Tools",
    "metadataDescription": "Extract text from receipts, book pages, infographics, and screenshots into editable copy. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/image-to-text",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "image_to_text",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "profile-photo-maker",
    "publicTitle": "Professional Profile Photo Studio",
    "shortTitle": "Profile Photo Maker",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Craft LinkedIn, Twitter/X, and Slack avatars with circular framing, studio backdrops, and borders.",
    "targetSearchCluster": [
      "profile photo maker free",
      "profile photo maker online",
      "private profile photo maker"
    ],
    "metadataTitle": "Professional Profile Photo Studio | Cora Free Online Tools",
    "metadataDescription": "Craft LinkedIn, Twitter/X, and Slack avatars with circular framing, studio backdrops, and borders. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/profile-photo-maker",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "profile_photo_maker",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "watermark-image",
    "publicTitle": "Watermark & Protect Photos",
    "shortTitle": "Watermark Image",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Stamp confidential watermarks, copyright notices, or logo graphics over client photo proofs.",
    "targetSearchCluster": [
      "watermark image free",
      "watermark image online",
      "private watermark image"
    ],
    "metadataTitle": "Watermark & Protect Photos | Cora Free Online Tools",
    "metadataDescription": "Stamp confidential watermarks, copyright notices, or logo graphics over client photo proofs. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/watermark-image",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "watermark_image",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "combine-images",
    "publicTitle": "Combine & Stitch Photos Together",
    "shortTitle": "Combine Images",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Merge multiple photos side-by-side, stacked vertically, or arranged in a 2x2 collage grid.",
    "targetSearchCluster": [
      "combine images free",
      "combine images online",
      "private combine images"
    ],
    "metadataTitle": "Combine & Stitch Photos Together | Cora Free Online Tools",
    "metadataDescription": "Merge multiple photos side-by-side, stacked vertically, or arranged in a 2x2 collage grid. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/combine-images",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "combine_images",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  },
  {
    "slug": "split-image",
    "publicTitle": "Split Image into Grids & Carousels",
    "shortTitle": "Split Image",
    "category": "image_tools",
    "categoryLabel": "Image Tools",
    "status": "live",
    "owner": "Foundation Agent",
    "icp": [
      "Creative Studios",
      "Agencies",
      "Freelancers"
    ],
    "jobToBeDone": "Slice wide photos into multi-slide Instagram swipe carousels or 3x3 grid puzzle posts.",
    "targetSearchCluster": [
      "split image free",
      "split image online",
      "private split image"
    ],
    "metadataTitle": "Split Image into Grids & Carousels | Cora Free Online Tools",
    "metadataDescription": "Slice wide photos into multi-slide Instagram swipe carousels or 3x3 grid puzzle posts. 100% private in-browser tool with zero server uploads.",
    "canonicalUrl": "https://heycora.in/tools/split-image",
    "inputs": [
      {
        "name": "fileOrText",
        "type": "file",
        "description": "Input document or asset"
      }
    ],
    "outputs": [
      {
        "name": "resultFile",
        "type": "file",
        "description": "Processed output file or text"
      }
    ],
    "features": [
      "100% Client-Side Engine",
      "Zero Cloud Storage",
      "Lossless Quality",
      "Instant Download"
    ],
    "methodologyVersion": "1.0.0",
    "executionMode": "browser-only",
    "privacyDisclosure": "100% In-Browser Execution. Zero file transmission or retention.",
    "securityRisk": "none",
    "relatedTools": [
      "service-pricing-calculator",
      "gst-calculator"
    ],
    "supportingArticles": [
      "/articles/product-guides/how-to-set-up-creative-studio-workspace"
    ],
    "supportingDocs": [
      "/docs/quickstart"
    ],
    "contextualCoraAction": {
      "title": "Manage client deliverables and files in Cora Media Hub.",
      "description": "Organize creative assets, project vaults, and client approval galleries.",
      "href": "/features/media-hub",
      "label": "Explore Cora Media Hub"
    },
    "analyticsIdentifier": "split_image",
    "publicationDate": "2026-09-05",
    "lastReviewedDate": "2026-10-10"
  }
];

// Helper: Get tool by slug
export function getToolBySlug(slug: string): CanonicalToolRecord | undefined {
  return CANONICAL_TOOLS_REGISTRY.find(t => t.slug === slug);
}

// Helper: Get live tools for public index & sitemap
export function getLiveTools(): CanonicalToolRecord[] {
  return CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live');
}

// Dynamically computed category statistics
export const CANONICAL_CATEGORY_SUMMARIES: CategorySummary[] = [
  { id: 'all', label: 'All Tools', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live').length },
  { id: 'agency_economics', label: 'Agency Economics', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live' && t.category === 'agency_economics').length },
  { id: 'wordpress_stack', label: 'WordPress & Stack', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live' && t.category === 'wordpress_stack').length },
  { id: 'ai_discovery', label: 'AI & Discovery', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live' && t.category === 'ai_discovery').length },
  { id: 'pdf_tools', label: 'PDF Tools', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live' && t.category === 'pdf_tools').length },
  { id: 'sheets_tools', label: 'Sheets & Excel', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live' && t.category === 'sheets_tools').length },
  { id: 'image_tools', label: 'Image Tools', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live' && t.category === 'image_tools').length },
  { id: 'finance_tax', label: 'Finance & Tax', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live' && t.category === 'finance_tax').length },
  { id: 'contracts_legal', label: 'Contracts & Legal', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live' && t.category === 'contracts_legal').length },
  { id: 'developer_embed', label: 'Developer & Embeds', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live' && t.category === 'developer_embed').length },
  { id: 'real_estate', label: 'Real Estate', count: CANONICAL_TOOLS_REGISTRY.filter(t => t.status === 'live' && t.category === 'real_estate').length },
];
