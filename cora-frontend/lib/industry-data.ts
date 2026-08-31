export interface IndustryMetric {
  value: string;
  label: string;
}

export interface IndustryWorkspace {
  id: string;
  slug: string;
  title: string;
  sectorId: 'tech_dev' | 'legal_finance' | 'marketing_creative' | 'services_lifestyle';
  sectorLabel: string;
  sectorBadge: string;
  badgeColor: 'blue' | 'slate' | 'purple' | 'teal' | 'emerald' | 'amber';
  iconName: string;
  tagline: string;
  description: string;
  sacCode: string;
  gstRate: string;
  metrics: IndustryMetric[];
  preSeededTemplates: string[];
  workflowHighlights: string[];
  recommendedModules: {
    id: string;
    title: string;
    icon: string;
  }[];
  challengeVsSolution: {
    challenge: string;
    solution: string;
  };
  sampleRetainerText: string;
  accentBg: string;
  accentBorder: string;
  accentText: string;
  heroImage: string;
}

export interface SectorCategory {
  id: string;
  label: string;
  shortLabel: string;
  badge: string;
  color: string;
  iconName: string;
  count: number;
}

export const SECTOR_CATEGORIES: SectorCategory[] = [
  {
    id: 'all',
    label: 'All Industries',
    shortLabel: 'All',
    badge: '16 WORKSPACES',
    color: 'zinc',
    iconName: 'Layers',
    count: 16
  },
  {
    id: 'tech_dev',
    label: 'Tech & Dev',
    shortLabel: 'Tech',
    badge: 'TECH & DEV',
    color: 'blue',
    iconName: 'Code',
    count: 4
  },
  {
    id: 'legal_finance',
    label: 'Legal & Finance',
    shortLabel: 'Legal & Tax',
    badge: 'LEGAL & FINANCE',
    color: 'slate',
    iconName: 'Scale',
    count: 4
  },
  {
    id: 'marketing_creative',
    label: 'Marketing & Creative',
    shortLabel: 'Marketing',
    badge: 'MARKETING & DESIGN',
    color: 'purple',
    iconName: 'Sparkles',
    count: 4
  },
  {
    id: 'services_lifestyle',
    label: 'Services & Lifestyle',
    shortLabel: 'Services',
    badge: 'SERVICES & ADVISORY',
    color: 'teal',
    iconName: 'Briefcase',
    count: 4
  }
];

export const INDUSTRY_WORKSPACES: IndustryWorkspace[] = [
  // ── 1. TECH & DEV SECTOR ──
  {
    id: 'software-agencies',
    slug: 'software-agencies',
    title: 'Software Agencies',
    sectorId: 'tech_dev',
    sectorLabel: 'Tech & Dev',
    sectorBadge: 'TECH & DEV',
    badgeColor: 'blue',
    iconName: 'Code',
    tagline: 'Sprint retainers, milestone escrow staging & client review vaults.',
    description: 'Dev agencies and custom engineering consultancies need to manage weekly sprint contracts, milestone staging sign-offs, SLA uptime guarantees, and recurring retainers with zero manual spreadsheets.',
    sacCode: 'SAC 998314',
    gstRate: '18% GST',
    metrics: [
      { value: '3X Faster', label: 'Proposal to signed sprint agreement' },
      { value: '18% GST', label: 'Automated SAC 998314 software math' },
      { value: '100%', label: 'Milestone sign-off before deploy' }
    ],
    preSeededTemplates: [
      'Agile Sprint Retainer Agreement (Master Services Agreement)',
      'Fixed-Scope Milestone Sign-Off & Escrow Release Form',
      'Software SLA & Priority Incident Support Agreement',
      'Mutual Non-Disclosure & IP Ownership Transfer (IT Act 2000)'
    ],
    workflowHighlights: [
      'Automated recurring sprint retainer billing with direct UPI & NEFT payment links',
      'Client staging approval portals requiring cryptographic digital sign-off before production deploy',
      'SLA uptime and maintenance tracking with priority bug escalation tiers',
      'Cryptographic SHA-256 digital contracts compliant with the Indian Information Technology Act 2000'
    ],
    recommendedModules: [
      { id: 'ai-cofounder', title: 'AI Co-Founder', icon: 'Bot' },
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'gst-invoicing', title: '18% GST Invoicing', icon: 'Receipt' },
      { id: 'task-board', title: 'Sprint Boards', icon: 'Kanban' }
    ],
    challengeVsSolution: {
      challenge: 'Unsigned change-orders, delayed milestone client sign-offs, and manual GST invoicing across multi-week sprints.',
      solution: 'Milestone-gated client staging approval portals with automated SAC 998314 tax invoice generation upon sign-off.'
    },
    sampleRetainerText: 'Monthly Tech Retainer: ₹2,50,000 + 18% GST (CGST ₹22,500 + SGST ₹22,500). Includes 80 dev hours, bi-weekly sprint reviews, and 4-hour SLA response.',
    accentBg: 'bg-blue-50/60',
    accentBorder: 'border-blue-200/80',
    accentText: 'text-blue-700',
    heroImage: '/images/cora_community_crowd.jpg'
  },
  {
    id: 'web-app-studios',
    slug: 'web-app-studios',
    title: 'Web & App Studios',
    sectorId: 'tech_dev',
    sectorLabel: 'Tech & Dev',
    sectorBadge: 'TECH & DEV',
    badgeColor: 'blue',
    iconName: 'LayoutTemplate',
    tagline: 'Fixed-scope contracts, client Figma sign-offs & SLA trackers.',
    description: 'Design-led web and mobile app studios build client sites on Webflow, Next.js, and Flutter. Cora locks in project scopes, automates 50% advance deposits, and prevents scope creep with change-order e-signs.',
    sacCode: 'SAC 998315',
    gstRate: '18% GST',
    metrics: [
      { value: '4-Phase', label: 'Wireframe to launch approval gates' },
      { value: 'Zero', label: 'Unpaid scope creep revisions' },
      { value: 'Instant', label: '50% advance deposit generation' }
    ],
    preSeededTemplates: [
      'Web & App Design-Build Master Agreement',
      'Figma UI/UX Milestone Client Sign-Off Form',
      'Post-Launch Hosting & Maintenance Retainer SLA',
      'Change-Order Request & Scope Amendment Contract'
    ],
    workflowHighlights: [
      '50% advance booking deposit invoice auto-generated upon contract e-sign',
      'Interactive design review portal with client approval checklist for Figma handoffs',
      'Automated change-order generator that quotes additional features instantly',
      'Integrated SAC 998315 tax splitting for domestic and export IT services'
    ],
    recommendedModules: [
      { id: 'canvas-builder', title: 'Funnel Builder', icon: 'LayoutTemplate' },
      { id: 'form-builder', title: 'Visual Forms', icon: 'FormInput' },
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'lead-crm', title: 'Lead CRM', icon: 'Users2' }
    ],
    challengeVsSolution: {
      challenge: 'Clients requesting endless revision rounds without paying for additional scope.',
      solution: 'Strict milestone sign-off gates with automated change-order addendums that must be digitally signed before work begins.'
    },
    sampleRetainerText: 'Design-Build Milestone Schedule: 50% Advance (₹1,75,000), 25% UI Sign-Off (₹87,500), 25% Production Launch (₹87,500) + 18% GST.',
    accentBg: 'bg-indigo-50/60',
    accentBorder: 'border-indigo-200/80',
    accentText: 'text-indigo-700',
    heroImage: '/images/bento_crew_camera.jpg'
  },
  {
    id: 'it-tech-services',
    slug: 'it-tech-services',
    title: 'IT & Tech Services (MSPs)',
    sectorId: 'tech_dev',
    sectorLabel: 'Tech & Dev',
    sectorBadge: 'TECH & DEV',
    badgeColor: 'blue',
    iconName: 'ShieldCheck',
    tagline: 'Managed IT retainers, uptime SLAs & hardware asset registries.',
    description: 'Managed Service Providers (MSPs) and IT consultants oversee client servers, cloud infrastructure, and cybersecurity. Cora automates monthly support retainers, hardware gear checkouts, and escalation logs.',
    sacCode: 'SAC 998313',
    gstRate: '18% GST',
    metrics: [
      { value: '99.9%', label: 'SLA uptime compliance logging' },
      { value: 'Monthly', label: 'Automated recurring retainer runs' },
      { value: 'Zero', label: 'Billing leakage on out-of-scope support' }
    ],
    preSeededTemplates: [
      'Managed IT Services Agreement & SLA Matrix',
      'Cybersecurity Audit & Compliance Review Contract',
      'Hardware Infrastructure Deployment & Rental Form',
      '24/7 Priority Emergency Support Addendum'
    ],
    workflowHighlights: [
      'Tiered monthly IT support retainer automation with automated tax invoices',
      'Hardware and network asset tracking with serial numbers and assigned client tags',
      'Incident ticket escalation logging with verified audit timestamps',
      'One-click client portal for viewing infrastructure health and signed SLAs'
    ],
    recommendedModules: [
      { id: 'asset-gear', title: 'Asset & Gear Hub', icon: 'HardDrive' },
      { id: 'gst-invoicing', title: '18% GST Invoicing', icon: 'Receipt' },
      { id: 'email-smtp', title: 'Verified SMTP', icon: 'Send' },
      { id: 'rbac-system', title: 'RBAC Security', icon: 'Lock' }
    ],
    challengeVsSolution: {
      challenge: 'Tracking out-of-scope IT requests that go unbilled at month-end.',
      solution: 'Automatic retainer hour logging with instant overage invoicing once monthly contracted hours are exceeded.'
    },
    sampleRetainerText: 'Managed Cloud SLA Retainer: ₹1,20,000/mo + 18% GST. Covers 24/7 server monitoring, up to 40 support hours, and monthly security patch audits.',
    accentBg: 'bg-emerald-50/60',
    accentBorder: 'border-emerald-200/80',
    accentText: 'text-emerald-700',
    heroImage: '/images/usecase_commercial_studio.jpg'
  },
  {
    id: 'ai-automation',
    slug: 'ai-automation',
    title: 'AI & Automation Agencies',
    sectorId: 'tech_dev',
    sectorLabel: 'Tech & Dev',
    sectorBadge: 'TECH & DEV',
    badgeColor: 'blue',
    iconName: 'Zap',
    tagline: 'Custom agent workflows, token billing & client AI vaults.',
    description: 'Specialized agencies building custom LLM agents, voice bots, and automation workflows. Cora provides token-based billing retainers, RAG document memory bridges, and client handover agreements.',
    sacCode: 'SAC 998316',
    gstRate: '18% GST',
    metrics: [
      { value: 'Token-Based', label: 'Flexible usage retainer models' },
      { value: 'RAG Vaults', label: 'Secure isolated memory bridges' },
      { value: '3-Min', label: 'Client onboarding to live AI agent' }
    ],
    preSeededTemplates: [
      'AI Agent Development & Deployment Agreement',
      'Enterprise LLM Data Privacy & Non-Training Addendum',
      'Monthly Automation Maintenance & Token Retainer',
      'Workflow IP & Prompt Architecture Handover Deed'
    ],
    workflowHighlights: [
      'Custom AI agent build scopes with phased deployment milestones',
      'Monthly token and maintenance retainer automation with GST invoices',
      'Isolated RAG knowledge base vault for confidential client document storage',
      'Automated client onboarding wizard for configuring API keys and webhooks'
    ],
    recommendedModules: [
      { id: 'ai-cofounder', title: 'AI Co-Founder', icon: 'Bot' },
      { id: 'rag-mcp', title: 'RAG Memory MCP', icon: 'BrainCircuit' },
      { id: 'content-ai', title: 'Content AI', icon: 'Sparkles' },
      { id: 'docs-portal', title: 'API Specs Hub', icon: 'Terminal' }
    ],
    challengeVsSolution: {
      challenge: 'Client concerns regarding enterprise data privacy and non-training on private corporate IP.',
      solution: 'Pre-seeded enterprise LLM data privacy addendums with complete cryptographic workspace isolation.'
    },
    sampleRetainerText: 'Enterprise AI Agent Retainer: ₹1,80,000/mo + 18% GST. Includes 5 custom agent workflows, monthly model fine-tuning, and RAG context optimization.',
    accentBg: 'bg-amber-50/60',
    accentBorder: 'border-amber-200/80',
    accentText: 'text-amber-700',
    heroImage: '/images/usecase_production_house.jpg'
  },

  // ── 2. LEGAL & FINANCE SECTOR ──
  {
    id: 'lawyers-law-firms',
    slug: 'lawyers-law-firms',
    title: 'Lawyers & Law Firms',
    sectorId: 'legal_finance',
    sectorLabel: 'Legal & Finance',
    sectorBadge: 'LEGAL & FINANCE',
    badgeColor: 'slate',
    iconName: 'Scale',
    tagline: 'SHA-256 e-sign vaults, retainer agreements & client intake.',
    description: 'Legal practices require airtight digital contract execution, tamper-evident document logs, and secure retainer billing. Cora provides IT Act compliant digital e-signatures, automated NDA workflows, and client intake vaults.',
    sacCode: 'SAC 998211',
    gstRate: '18% GST (RCM / Forward)',
    metrics: [
      { value: 'SHA-256', label: 'Cryptographic hash signature verification' },
      { value: '100%', label: 'IT Act 2000 Section 10A legal compliance' },
      { value: 'Zero', label: 'Physical paper contracts or lost filings' }
    ],
    preSeededTemplates: [
      'Comprehensive Legal Advisory Retainer Agreement',
      'Mutual Non-Disclosure Agreement (Indian Jurisdiction)',
      'Employment & Independent Contractor Legal Agreement',
      'Client Confidential Intake & Conflict Check Questionnaire'
    ],
    workflowHighlights: [
      '5-Step guided e-signature flow with IP timestamping and immutable SHA-256 hashes',
      'Encrypted client document vault for NDAs, deeds, and court filings',
      'Automated recurring legal retainer billing with Reverse Charge Mechanism (RCM) tax splits',
      'Client intake questionnaires with conditional legal branches'
    ],
    recommendedModules: [
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'rbac-system', title: 'RBAC Security', icon: 'Lock' },
      { id: 'gst-invoicing', title: 'GST Math', icon: 'Receipt' },
      { id: 'form-builder', title: 'Intake Forms', icon: 'FormInput' }
    ],
    challengeVsSolution: {
      challenge: 'Tracking physical signatures and maintaining verifiable audit trails for client contracts.',
      solution: 'IT Act 2000 compliant digital e-sign vaults that record IP addresses, user agents, and SHA-256 hashes on every document.'
    },
    sampleRetainerText: 'Corporate Legal Retainer: ₹1,50,000/mo + 18% GST. Includes 20 advisory hours, contract reviews, and regulatory compliance filings.',
    accentBg: 'bg-slate-50/70',
    accentBorder: 'border-slate-300',
    accentText: 'text-slate-800',
    heroImage: '/images/bento_crew_camera.jpg'
  },
  {
    id: 'tax-ca-firms',
    slug: 'tax-ca-firms',
    title: 'Tax & CA Practices',
    sectorId: 'legal_finance',
    sectorLabel: 'Legal & Finance',
    sectorBadge: 'LEGAL & FINANCE',
    badgeColor: 'slate',
    iconName: 'Receipt',
    tagline: '18% GST auto-splitting, SAC audits & client retainer automation.',
    description: 'Chartered accountants and tax advisory firms manage high-volume client compliance, monthly filings, and advisory retainers. Cora automates SAC code allocation, CGST/SGST tax math, and automated recurring fee collections.',
    sacCode: 'SAC 998222',
    gstRate: '18% GST',
    metrics: [
      { value: '18% GST', label: 'Native CGST + SGST tax auto-calculation' },
      { value: 'SAC Codes', label: 'Pre-seeded professional accounting schemas' },
      { value: '+45%', label: 'Faster client invoice collection via UPI' }
    ],
    preSeededTemplates: [
      'Statutory & Tax Audit Engagement Letter (ICAI Aligned)',
      'Monthly GST & TDS Compliance Retainer Contract',
      'Accounting & Bookkeeping Service Agreement',
      'Annual Corporate Filing & ROC Advisory Mandate'
    ],
    workflowHighlights: [
      'Automated SAC tax classification for auditing, accounting, and tax representation',
      'One-click dynamic UPI QR code and NEFT/RTGS payment collection',
      'Monthly recurring client retainer automation with professional PDF tax invoices',
      'Encrypted document depository for client balance sheets, GST returns, and audit proofs'
    ],
    recommendedModules: [
      { id: 'gst-invoicing', title: 'GST Tax Engine', icon: 'Receipt' },
      { id: 'esign-vault', title: 'Audit Vault', icon: 'ShieldCheck' },
      { id: 'super-admin', title: 'Multi-Branch Hub', icon: 'Layers' },
      { id: 'pwa-push', title: 'Client Alerts', icon: 'Bell' }
    ],
    challengeVsSolution: {
      challenge: 'Manual GST calculations and delayed payment collections from hundreds of corporate clients.',
      solution: 'Automated 18% GST invoice generator with embedded dynamic UPI QR codes and automated payment reminders.'
    },
    sampleRetainerText: 'Annual Statutory Audit & GST Retainer: ₹3,00,000 + 18% GST (CGST ₹27,000 + SGST ₹27,000). Covers quarterly reconciliations and ITR filing.',
    accentBg: 'bg-emerald-50/60',
    accentBorder: 'border-emerald-200/80',
    accentText: 'text-emerald-700',
    heroImage: '/images/usecase_commercial_studio.jpg'
  },
  {
    id: 'financial-advisors',
    slug: 'financial-advisors',
    title: 'Financial Advisors',
    sectorId: 'legal_finance',
    sectorLabel: 'Legal & Finance',
    sectorBadge: 'LEGAL & FINANCE',
    badgeColor: 'slate',
    iconName: 'Briefcase',
    tagline: 'Advisory retainers, wealth portfolios & confidential agreements.',
    description: 'Wealth management firms, registered investment advisors (RIAs), and financial planners require secure client onboarding, risk-profiling questionnaires, and compliant recurring advisory fee billing.',
    sacCode: 'SAC 997159',
    gstRate: '18% GST',
    metrics: [
      { value: '₹5L+ Avg', label: 'Advisory retainer size supported' },
      { value: '100%', label: 'Digital risk-profiling compliance' },
      { value: 'SEBI-Ready', label: 'Complete immutable audit trails' }
    ],
    preSeededTemplates: [
      'Investment Advisory Agreement (SEBI RIA Aligned)',
      'Client Risk Profile & Asset Allocation Mandate',
      'Wealth Management Quarterly Retainer Contract',
      'Confidential Financial Disclosure & NDA'
    ],
    workflowHighlights: [
      'Digital risk-profile intake forms with scoring calculations for wealth clients',
      'Quarterly and annual advisory fee billing with automated GST breakdown',
      'Encrypted document vault for net-worth statements and portfolio reviews',
      'Multi-seat advisor permissions with strict client data segregation'
    ],
    recommendedModules: [
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'lead-crm', title: 'High-Net-Worth CRM', icon: 'Users2' },
      { id: 'gst-invoicing', title: 'Advisory Invoicing', icon: 'Receipt' },
      { id: 'rbac-system', title: 'Data Isolation', icon: 'Lock' }
    ],
    challengeVsSolution: {
      challenge: 'Maintaining compliant risk-profiling records and collecting recurring advisory fees.',
      solution: 'Integrated digital risk assessment intake forms paired with automated quarterly advisory retainer billing.'
    },
    sampleRetainerText: 'Wealth Advisory Fee: 1.00% AUM billed quarterly in advance: ₹1,25,000/qtr + 18% GST. Includes semi-annual rebalancing and tax-loss harvesting.',
    accentBg: 'bg-indigo-50/60',
    accentBorder: 'border-indigo-200/80',
    accentText: 'text-indigo-700',
    heroImage: '/images/usecase_production_house.jpg'
  },
  {
    id: 'audit-compliance',
    slug: 'audit-compliance',
    title: 'Audit & Compliance',
    sectorId: 'legal_finance',
    sectorLabel: 'Legal & Finance',
    sectorBadge: 'LEGAL & FINANCE',
    badgeColor: 'slate',
    iconName: 'Layers',
    tagline: 'Regulatory audit trails, capability matrix & verified logs.',
    description: 'Corporate governance consultants, ISO auditors, and secretarial firms coordinate multi-department audits, evidence collections, and executive sign-off trails with complete cryptographic verification.',
    sacCode: 'SAC 998223',
    gstRate: '18% GST',
    metrics: [
      { value: '30-Point', label: 'Security & governance audit matrix' },
      { value: 'Immutable', label: 'SHA-256 verified evidence logs' },
      { value: 'Multi-Branch', label: 'Enterprise branch data isolation' }
    ],
    preSeededTemplates: [
      'Corporate Governance & Secretarial Audit Mandate',
      'ISO 27001 / SOC-2 Compliance Review Contract',
      'Internal Financial Controls (IFC) Audit Agreement',
      'Director Sign-Off & Board Resolution Protocol'
    ],
    workflowHighlights: [
      'Multi-stage compliance checklist milestones with assigned department owners',
      'Evidence depository with tamper-evident SHA-256 digital verification',
      'Multi-party digital signature flows for board directors and statutory auditors',
      'Consolidated audit status dashboard for tracking enterprise branch readiness'
    ],
    recommendedModules: [
      { id: 'rbac-system', title: '30-Point RBAC', icon: 'Lock' },
      { id: 'esign-vault', title: 'Audit Vault', icon: 'ShieldCheck' },
      { id: 'super-admin', title: 'Multi-Branch Hub', icon: 'Layers' },
      { id: 'docs-portal', title: 'Specs & Protocols', icon: 'Terminal' }
    ],
    challengeVsSolution: {
      challenge: 'Scattered audit evidence across email threads making regulatory verification difficult.',
      solution: 'Centralized evidence vault with cryptographic timestamping and role-based auditor access.'
    },
    sampleRetainerText: 'Enterprise ISO Governance Audit: ₹4,50,000 + 18% GST. Covers 3 regional branches, 40 control checks, and final certification submission.',
    accentBg: 'bg-purple-50/60',
    accentBorder: 'border-purple-200/80',
    accentText: 'text-purple-700',
    heroImage: '/images/usecase_realestate_agency.jpg'
  },

  // ── 3. MARKETING & CREATIVE SECTOR ──
  {
    id: 'marketing-seo',
    slug: 'marketing-seo',
    title: 'Marketing & SEO',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Creative',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'BarChart2',
    tagline: 'Retainer billing, SEO audits, ad spend tracking & dashboards.',
    description: 'Digital marketing and performance agencies coordinate SEO retainers, paid media ad spend budgets, and content deliverables. Cora automates monthly retainer cycles and unifies client reporting.',
    sacCode: 'SAC 998361',
    gstRate: '18% GST',
    metrics: [
      { value: '₹1.8L/Mo', label: 'Saved by replacing fragmented SaaS apps' },
      { value: 'Monthly', label: 'Automated recurring client retainer runs' },
      { value: '1-Click', label: 'SEO audit & content brief generation' }
    ],
    preSeededTemplates: [
      'Digital Marketing & SEO Retainer Master Agreement',
      'Performance Ad Spend Management & ROAS Contract',
      'Influencer Campaign SOW & Deliverable Contract',
      'Monthly Content Strategy & Approval Protocol'
    ],
    workflowHighlights: [
      'Automated monthly marketing retainer invoices with custom SAC 998361 tax splits',
      'White-label client performance dashboard for tracking KPIs and signed deliverables',
      'Content AI assistant for generating viral 3-act scripts and SEO-optimized copy',
      'Role-based permissions for account managers, media buyers, and client stakeholders'
    ],
    recommendedModules: [
      { id: 'content-ai', title: 'Content AI & GEO', icon: 'Sparkles' },
      { id: 'lead-crm', title: 'Agency Lead CRM', icon: 'Users2' },
      { id: 'gst-invoicing', title: '18% GST Invoicing', icon: 'Receipt' },
      { id: 'canvas-builder', title: 'Campaign Builder', icon: 'LayoutTemplate' }
    ],
    challengeVsSolution: {
      challenge: 'Paying for separate tools for CRM, proposal e-signing, AI copywriting, and invoicing.',
      solution: 'One unified workspace that handles client lead intake, proposal e-signing, AI writing, and GST billing.'
    },
    sampleRetainerText: 'Comprehensive Growth Retainer: ₹1,50,000/mo + 18% GST. Includes technical SEO audits, 8 monthly articles, and ₹10L ad spend management.',
    accentBg: 'bg-sky-50/60',
    accentBorder: 'border-sky-200/80',
    accentText: 'text-sky-700',
    heroImage: '/images/usecase_realestate_agency.jpg'
  },
  {
    id: 'design-uiux',
    slug: 'design-uiux',
    title: 'Design & UI/UX',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Creative',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'Sparkles',
    tagline: 'Design sprints, asset portals & client Figma approvals.',
    description: 'Brand identity and UI/UX studios deliver design systems, mobile mockups, and visual assets. Cora coordinates sprint milestones, client Figma approvals, and final copyright transfer deeds.',
    sacCode: 'SAC 998391',
    gstRate: '18% GST',
    metrics: [
      { value: '2-Week', label: 'Structured design sprint milestones' },
      { value: 'Zero', label: 'Unapproved Figma revision loops' },
      { value: 'Instant', label: 'High-res brand asset delivery portal' }
    ],
    preSeededTemplates: [
      'Brand Identity & UI/UX Design Master Contract',
      'Design Sprint Milestone Approval & Sign-Off Form',
      'Copyright & Intellectual Property Assignment Deed',
      'Monthly Design-on-Demand Retainer Agreement'
    ],
    workflowHighlights: [
      'Design sprint milestone gates with mandatory client sign-off before final export',
      'Asset delivery portal for securely sharing Figma files, vector logos, and design tokens',
      'Automated copyright transfer documentation executed upon final invoice clearance',
      'Integrated SAC 998391 specialized design tax calculations'
    ],
    recommendedModules: [
      { id: 'media-hub', title: 'Asset & Brand Hub', icon: 'HardDrive' },
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'task-board', title: 'Sprint Board', icon: 'Kanban' },
      { id: 'gst-invoicing', title: '18% GST Invoicing', icon: 'Receipt' }
    ],
    challengeVsSolution: {
      challenge: 'Delivering design source files before the client has cleared the final balance payment.',
      solution: 'Automated asset delivery vaults that unlock download permissions only when final GST invoices are paid.'
    },
    sampleRetainerText: 'Product Design Sprint: 2-Week Sprint at ₹2,00,000 + 18% GST. Includes wireframes, high-fidelity Figma components, and clickable prototype.',
    accentBg: 'bg-violet-50/60',
    accentBorder: 'border-violet-200/80',
    accentText: 'text-violet-700',
    heroImage: '/images/usecase_solo_creator.jpg'
  },
  {
    id: 'photo-video-studios',
    slug: 'photo-video-studios',
    title: 'Photo & Video Studios',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Creative',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'Camera',
    tagline: 'Production scopes, 4K proofing, call sheets & gear tracking.',
    description: 'Commercial photography studios and video production houses coordinate multi-day shoots, camera gear inventories, crew call sheets, and 4K media deliveries for high-profile brand campaigns.',
    sacCode: 'SAC 998381',
    gstRate: '18% GST',
    metrics: [
      { value: '50% Advance', label: 'Mandatory deposit locked on e-sign' },
      { value: '8K Ready', label: 'High-res proofing and media delivery' },
      { value: 'Conflict-Free', label: 'Crew and equipment scheduling' }
    ],
    preSeededTemplates: [
      'Commercial Photography & Production Contract',
      'Crew Call Sheet & Location Release Agreement',
      'Model & Talent Release Form (Indian Jurisdiction)',
      'Equipment Rental & Gear Liability Agreement'
    ],
    workflowHighlights: [
      'Multi-day shoot master calendar with crew dispatch and location mapping',
      'Camera gear inventory check-in/out registry with barcode and serial tracking',
      'Watermarked client photo and video proofing portal with selection approvals',
      '50% advance deposit invoice generation triggered instantly upon contract signature'
    ],
    recommendedModules: [
      { id: 'crew-dispatch', title: 'Crew Dispatch', icon: 'Calendar' },
      { id: 'asset-gear', title: 'Gear Inventory', icon: 'HardDrive' },
      { id: 'media-hub', title: '4K Media Hub', icon: 'Film' },
      { id: 'master-calendar', title: 'Master Calendar', icon: 'Calendar' }
    ],
    challengeVsSolution: {
      challenge: 'Crew scheduling double-bookings and unreturned camera gear from remote shoots.',
      solution: 'Centralized master calendar with conflict-free crew dispatch and serialized gear check-in/out logs.'
    },
    sampleRetainerText: 'Commercial Campaign Package: 2-Day Shoot (₹3,50,000) + Post-Production (₹1,50,000) + 18% GST (CGST ₹45,000 + SGST ₹45,000).',
    accentBg: 'bg-rose-50/60',
    accentBorder: 'border-rose-200/80',
    accentText: 'text-rose-700',
    heroImage: '/images/usecase_commercial_studio.jpg'
  },
  {
    id: 'architecture-interiors',
    slug: 'architecture-interiors',
    title: 'Architecture & Interiors',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Creative',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'Building2',
    tagline: '3D CAD milestones, blueprint approvals & progressive draws.',
    description: 'Architecture and interior design studios coordinate multi-phase concept design, 3D CAD approvals, contractor milestone draws, and progressive billing across complex residential and commercial sites.',
    sacCode: 'SAC 998321',
    gstRate: '18% GST',
    metrics: [
      { value: '4-Stage', label: 'Concept to handover milestone draws' },
      { value: '100%', label: 'Signed client change-order protection' },
      { value: 'Zero', label: 'Uncollected contractor milestone fees' }
    ],
    preSeededTemplates: [
      'Architectural & Interior Design Consultancy Agreement',
      'Progressive Milestone Draw & Site Handover Protocol',
      'Client Change-Order & Material Variance Contract',
      'Contractor & Site Execution Supervision Mandate'
    ],
    workflowHighlights: [
      'Progressive milestone billing tied to design phase completion (Concept, 3D Render, GFC Drawings, Handover)',
      'Digital client change-order approvals with cryptographic e-signatures before execution',
      'Integrated document depository for site blueprints, CAD files, and material specs',
      'Automated SAC 998321 tax breakdown on architectural consultancy fees'
    ],
    recommendedModules: [
      { id: 'task-board', title: 'Site Milestone Board', icon: 'Kanban' },
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'gst-invoicing', title: 'Progressive Invoicing', icon: 'Receipt' },
      { id: 'media-hub', title: 'Blueprint Vault', icon: 'HardDrive' }
    ],
    challengeVsSolution: {
      challenge: 'Clients requesting structural or finish modifications mid-construction without agreeing on extra costs.',
      solution: 'Mandatory digital change-order sign-offs that automatically update the project milestone billing total.'
    },
    sampleRetainerText: 'Architectural Consultancy Fee: ₹8,00,000 + 18% GST across 4 Milestones: 25% Concept (₹2L), 30% 3D CAD (₹2.4L), 25% GFC (₹2L), 20% Handover (₹1.6L).',
    accentBg: 'bg-orange-50/60',
    accentBorder: 'border-orange-200/80',
    accentText: 'text-orange-700',
    heroImage: '/images/usecase_solo_creator.jpg'
  },

  // ── 4. SERVICES & LIFESTYLE SECTOR ──
  {
    id: 'consultants-advisors',
    slug: 'consultants-advisors',
    title: 'Consultants & Advisors',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Lifestyle',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Briefcase',
    tagline: 'Diagnostic audits, scopes & executive board decks.',
    description: 'Management consultants, strategy advisors, and corporate transformation experts deliver diagnostic audits, executive decks, and ongoing advisory retainers. Cora automates scoping, decks, and billing.',
    sacCode: 'SAC 998311',
    gstRate: '18% GST',
    metrics: [
      { value: '2 Minutes', label: 'Voice meeting brief to structured scope' },
      { value: '₹5L+ Avg', label: 'Advisory retainer size supported' },
      { value: 'Multi-Seat', label: 'Role-based access for advisory teams' }
    ],
    preSeededTemplates: [
      'Strategic Advisory & Management Consulting Master Agreement',
      'Diagnostic Audit Scope & Executive Deliverable Mandate',
      'Quarterly Advisory Retainer & Milestone Contract',
      'Mutual Non-Disclosure & Enterprise IP Protection Deed'
    ],
    workflowHighlights: [
      'Voice-to-Scope audio transcription converting meeting notes into structured advisory proposals in 2 minutes',
      'Multi-stage payment milestones (Advance, Diagnostic Wrap, Board Review)',
      'White-label executive client presentation portals for sharing strategy decks',
      'Complete multi-tenant data isolation protecting confidential corporate transformations'
    ],
    recommendedModules: [
      { id: 'voice-to-scope', title: 'Voice-to-Scope', icon: 'Sparkles' },
      { id: 'ai-cofounder', title: 'AI Co-Founder', icon: 'Bot' },
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'lead-crm', title: 'Enterprise CRM', icon: 'Users2' }
    ],
    challengeVsSolution: {
      challenge: 'Spending hours drafting long proposal documents after client discovery meetings.',
      solution: 'Voice-to-Scope AI that transforms voice recordings and notes into ready-to-sign formal advisory contracts.'
    },
    sampleRetainerText: 'Corporate Strategy Mandate: ₹4,00,000/mo + 18% GST. Includes bi-weekly executive reviews, market intelligence audits, and board decks.',
    accentBg: 'bg-indigo-50/60',
    accentBorder: 'border-indigo-200/80',
    accentText: 'text-indigo-700',
    heroImage: '/images/usecase_production_house.jpg'
  },
  {
    id: 'doctors-clinics',
    slug: 'doctors-clinics',
    title: 'Doctors & Clinics',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Lifestyle',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Heart',
    tagline: 'Patient intake booking, consent e-signs & confidential records.',
    description: 'Specialized healthcare clinics, dental studios, and wellness practices require streamlined appointment booking, paperless intake questionnaires, digital patient consent e-signatures, and instant fee receipts.',
    sacCode: 'SAC 999312',
    gstRate: 'Exempt / 18% GST (Cosmetic)',
    metrics: [
      { value: '100% Digital', label: 'Paperless intake forms and consent' },
      { value: 'Instant', label: 'Automated appointment confirmation' },
      { value: 'Encrypted', label: 'Confidential patient records vault' }
    ],
    preSeededTemplates: [
      'Patient Registration & Medical History Intake Form',
      'Informed Medical & Clinical Procedure Consent Agreement',
      'Tele-Consultation Terms & Confidentiality Form',
      'Clinical Treatment Package & Fee Schedule Agreement'
    ],
    workflowHighlights: [
      'Online appointment booking calendar with automated WhatsApp and SMS confirmation alerts',
      'Paperless patient intake questionnaires with conditional medical branches',
      'Digital patient consent e-signatures with legal timestamping before procedures',
      'Instant digital tax receipts with UPI QR codes for seamless fee settlement'
    ],
    recommendedModules: [
      { id: 'form-builder', title: 'Patient Intake', icon: 'FormInput' },
      { id: 'master-calendar', title: 'Clinic Calendar', icon: 'Calendar' },
      { id: 'esign-vault', title: 'Consent Vault', icon: 'ShieldCheck' },
      { id: 'pwa-push', title: 'SMS & App Alerts', icon: 'Bell' }
    ],
    challengeVsSolution: {
      challenge: 'Manual paper intake forms creating patient waiting room bottlenecks and lost records.',
      solution: 'Mobile-friendly digital intake forms sent to patients via WhatsApp prior to clinic arrival.'
    },
    sampleRetainerText: 'Specialized Consultation Package: 6-Session Clinical Wellness Protocol at ₹45,000 with digital intake and secure progress tracking.',
    accentBg: 'bg-teal-50/60',
    accentBorder: 'border-teal-200/80',
    accentText: 'text-teal-700',
    heroImage: '/images/usecase_solo_creator.jpg'
  },
  {
    id: 'salons-wellness',
    slug: 'salons-wellness',
    title: 'Salons & Wellness',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Lifestyle',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Scissors',
    tagline: 'Service menu packages, slot booking & instant UPI billing.',
    description: 'High-end salons, luxury spas, and wellness centers require automated chair booking, visual service menus, bridal package agreements, and rapid UPI QR billing to eliminate front-desk queues.',
    sacCode: 'SAC 999721',
    gstRate: '18% GST',
    metrics: [
      { value: 'Zero', label: 'Front-desk checkout waiting queues' },
      { value: 'Instant', label: 'Dynamic UPI QR billing' },
      { value: '-60%', label: 'Appointment no-shows with WhatsApp alerts' }
    ],
    preSeededTemplates: [
      'Bridal & Event Beauty Package Agreement & Deposit Contract',
      'Salon Membership & Subscription Wellness Agreement',
      'Chemical Treatment & Client Sensitivity Consent Form',
      'Specialist Chair & Booth Rental Contract'
    ],
    workflowHighlights: [
      'Interactive visual service menu and bridal package booking with deposit collection',
      'Specialist chair and stylist allocation calendar preventing overbooking',
      'Automated appointment reminder notifications sent via WhatsApp',
      'Rapid GST billing with dynamic UPI QR codes and instant SMS receipt delivery'
    ],
    recommendedModules: [
      { id: 'master-calendar', title: 'Stylist Calendar', icon: 'Calendar' },
      { id: 'form-builder', title: 'Service Menus', icon: 'FormInput' },
      { id: 'gst-invoicing', title: 'UPI Quick-Bill', icon: 'Receipt' },
      { id: 'review-portal', title: 'Google Reviews', icon: 'Star' }
    ],
    challengeVsSolution: {
      challenge: 'Last-minute appointment cancellations and slow manual payment collection at the reception desk.',
      solution: 'Advance deposit collection on premium bookings paired with automated WhatsApp reminders.'
    },
    sampleRetainerText: 'Luxury Bridal Hair & Makeup Package: ₹65,000 + 18% GST (50% booking deposit locked via digital agreement, 50% on event date).',
    accentBg: 'bg-pink-50/60',
    accentBorder: 'border-pink-200/80',
    accentText: 'text-pink-700',
    heroImage: '/images/bento_crew_camera.jpg'
  },
  {
    id: 'real-estate-property',
    slug: 'real-estate-property',
    title: 'Real Estate & Property',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Lifestyle',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Building2',
    tagline: 'Property listings, brokerage contracts & 4K media shoots.',
    description: 'Commercial real estate consultancies, property brokers, and developer sales teams coordinate property media shoots, client mandate agreements, commission contracts, and listing microsites.',
    sacCode: 'SAC 997212',
    gstRate: '18% GST',
    metrics: [
      { value: '1-Click', label: 'Listing AI description generator' },
      { value: '100%', label: 'Signed brokerage mandate protection' },
      { value: '4K Ready', label: 'Property virtual tour media vaults' }
    ],
    preSeededTemplates: [
      'Exclusive Real Estate Brokerage & Mandate Agreement',
      'Commercial Property Lease & Transaction Protocol',
      'Property Media Shoot & Virtual Tour Production SOW',
      'Channel Partner Commission & Fee Sharing Contract'
    ],
    workflowHighlights: [
      'Property listing microsites with built-in lead capture forms and virtual tour embeds',
      'Brokerage commission agreements with legally binding SHA-256 digital signatures',
      'Listing AI tool generating high-converting property descriptions in seconds',
      'Automated commission invoicing with SAC 997212 tax calculation'
    ],
    recommendedModules: [
      { id: 'lead-crm', title: 'Property CRM', icon: 'Users2' },
      { id: 'canvas-builder', title: 'Listing Microsites', icon: 'LayoutTemplate' },
      { id: 'esign-vault', title: 'Brokerage E-Sign', icon: 'ShieldCheck' },
      { id: 'gst-invoicing', title: 'Commission Invoicing', icon: 'Receipt' }
    ],
    challengeVsSolution: {
      challenge: 'Disputes over brokerage commission percentages after commercial lease closures.',
      solution: 'Pre-signed exclusive brokerage mandate agreements executed digitally before showing properties.'
    },
    sampleRetainerText: 'Commercial Brokerage Mandate: 1 Month Rental Commission (₹2,50,000) + 18% GST (CGST ₹22,500 + SGST ₹22,500) payable on lease execution.',
    accentBg: 'bg-emerald-50/60',
    accentBorder: 'border-emerald-200/80',
    accentText: 'text-emerald-700',
    heroImage: '/images/usecase_realestate_agency.jpg'
  }
];
