export interface IndustryStat {
  metric: string;
  label: string;
}

export interface IndustryCapability {
  title: string;
  description: string;
  tag: string;
}

export interface IndustryWorkflowStep {
  step: string;
  title: string;
  description: string;
}

export interface IndustryToolReplaced {
  name: string;
  category: string;
  monthlySavingsINR: number;
}

export interface IndustryFaq {
  question: string;
  answer: string;
}

export interface IndustryWorkspace {
  id: string;
  slug: string;
  title: string;
  shortTitle: string;
  sectorId: 'tech_dev' | 'legal_finance' | 'marketing_creative' | 'services_lifestyle';
  sectorLabel: string;
  sectorBadge: string;
  badgeColor: 'blue' | 'slate' | 'purple' | 'teal' | 'emerald' | 'amber';
  iconName: string;
  tagline: string;
  heroDescription: string;
  sacCode: string;
  gstRate: string;
  stats: IndustryStat[];
  theOldWay: string[];
  theCoraWay: string[];
  capabilities: IndustryCapability[];
  howItWorks: IndustryWorkflowStep[];
  toolsReplaced: IndustryToolReplaced[];
  faqs: IndustryFaq[];
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
  relatedIndustrySlugs: string[];
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
    shortTitle: 'Software Agencies',
    sectorId: 'tech_dev',
    sectorLabel: 'Tech & Dev',
    sectorBadge: 'TECH & DEV',
    badgeColor: 'blue',
    iconName: 'Code',
    tagline: 'Sprint retainers, milestone escrow staging & client review vaults.',
    heroDescription: 'Purpose-built operating system for custom software engineering firms, dev boutiques, and IT consultancies. Manage sprint retainers, milestone staging gates, SLA uptime guarantees, and automated 18% GST SAC 998314 billing.',
    sacCode: 'SAC 998314',
    gstRate: '18% GST',
    stats: [
      { metric: '3X Faster', label: 'Proposal to signed contract' },
      { metric: '18% GST', label: 'SAC 998314 auto-splitting' },
      { metric: '100%', label: 'Milestone sign-off before deploy' }
    ],
    theOldWay: [
      'Unsigned client change-orders causing painful scope creep and unbilled dev hours',
      'Manually calculating 18% GST on sprint retainers and sending disconnected PDF invoices',
      'Deploying code to production without recorded, legally binding client milestone sign-offs'
    ],
    theCoraWay: [
      'Digital change-order generator that calculates additional hours and requires e-sign before work begins',
      'Automated SAC 998314 tax engine that splits CGST/SGST and triggers recurring UPI & NEFT payment links',
      'Milestone-gated client staging portals requiring cryptographic SHA-256 sign-offs before deploy'
    ],
    capabilities: [
      {
        title: 'Agile Sprint Retainer Engine',
        description: 'Automate weekly or bi-weekly client retainers with integrated hour tracking, roll-over controls, and instant overage invoicing.',
        tag: 'Sprint Retainers'
      },
      {
        title: 'Milestone Escrow & Staging Approvals',
        description: 'Client staging portals where stakeholders inspect test builds and sign off on completion before production deployment.',
        tag: 'Staging Gates'
      },
      {
        title: 'SAC 998314 Tax & Invoicing',
        description: 'Pre-seeded Indian tax schemas for domestic and export IT services with reverse charge (RCM) and SEZ zero-tax support.',
        tag: '18% GST Engine'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Scope & Sprint Definition', description: 'Convert discovery notes into structured sprint milestones using AI Voice-to-Scope.' },
      { step: '02', title: 'SHA-256 Master Agreement', description: 'Client executes master services agreement (MSA) with IT Act 2000 digital signatures.' },
      { step: '03', title: 'Staging Milestone Approval', description: 'Client reviews test build and approves release checklist with one click.' },
      { step: '04', title: 'Automated GST Invoicing', description: 'SAC 998314 invoice generated automatically with embedded UPI QR and bank links.' }
    ],
    toolsReplaced: [
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Jira Core', category: 'Task Management', monthlySavingsINR: 4200 },
      { name: 'FreshBooks', category: 'Invoicing & Tax', monthlySavingsINR: 2800 },
      { name: 'Zapier', category: 'Automations', monthlySavingsINR: 3900 }
    ],
    faqs: [
      {
        question: 'How does Cora handle software sprint retainers vs fixed-price milestones?',
        answer: 'Cora supports both agile sprint retainers (recurring monthly/bi-weekly fee with allocated dev hours) and fixed-price milestone schedules (e.g. 50% advance, 25% staging sign-off, 25% production launch). Both trigger automated GST invoices upon approval.'
      },
      {
        question: 'Does Cora support Indian IT Act 2000 compliant digital signatures for NDAs and MSAs?',
        answer: 'Yes. Every agreement executed on Cora generates a tamper-evident SHA-256 cryptographic hash with recorded IP addresses, device user agents, and ISO timestamps fully compliant with Section 10A of the Indian Information Technology Act 2000.'
      },
      {
        question: 'Can we bill international clients with 0% GST (Export of Services)?',
        answer: 'Yes. Cora automatically detects foreign currency invoicing and applies 0% export GST classification with mandatory LUT (Letter of Undertaking) declaration fields.'
      }
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
    heroImage: '/images/cora_community_crowd.jpg',
    relatedIndustrySlugs: ['web-app-studios', 'it-tech-services', 'ai-automation', 'consultants-advisors']
  },
  {
    id: 'web-app-studios',
    slug: 'web-app-studios',
    title: 'Web & App Studios',
    shortTitle: 'Web & App Studios',
    sectorId: 'tech_dev',
    sectorLabel: 'Tech & Dev',
    sectorBadge: 'TECH & DEV',
    badgeColor: 'blue',
    iconName: 'LayoutTemplate',
    tagline: 'Fixed-scope contracts, client Figma sign-offs & SLA trackers.',
    heroDescription: 'The operating system for modern Webflow, Next.js, and mobile app studios. Lock in design-build scopes, collect 50% advance booking deposits, and eliminate scope creep with client Figma sign-off portals.',
    sacCode: 'SAC 998315',
    gstRate: '18% GST',
    stats: [
      { metric: '4-Phase', label: 'Wireframe to launch gates' },
      { metric: 'Zero', label: 'Unpaid scope creep revisions' },
      { metric: 'Instant', label: '50% advance deposit generation' }
    ],
    theOldWay: [
      'Clients requesting endless design revisions without paying for additional scope',
      'Delivering Figma files or website transfers before collecting the final payment balance',
      'No formal SLA for post-launch hosting, security updates, and bug fixes'
    ],
    theCoraWay: [
      'Pre-seeded 4-phase milestone contracts with built-in revision limit rules and change-orders',
      'Automated client hand-off vaults that release credentials and asset files only upon invoice payment',
      'Turnkey monthly website hosting and maintenance retainer templates with automated debit reminders'
    ],
    capabilities: [
      {
        title: 'Design-Build Milestone Schedule',
        description: 'Pre-configured 4-phase milestone structure: 50% Deposit, 25% UI Sign-Off, 25% Launch Handover.',
        tag: 'Phase Gates'
      },
      {
        title: 'Client Asset Handover Vault',
        description: 'Secure client portal that automatically delivers production source code, assets, and DNS keys upon invoice clearance.',
        tag: 'Delivery Vault'
      },
      {
        title: 'Change-Order Scope Amendment',
        description: 'One-click change order builder for extra pages or features that clients can approve and pay directly.',
        tag: 'Scope Protection'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Proposal & Scope Builder', description: 'Assemble deliverables, revision limits, and payment terms in 3 minutes.' },
      { step: '02', title: 'E-Sign & Advance Payment', description: 'Client signs digital contract and clears 50% advance deposit via UPI or bank transfer.' },
      { step: '03', title: 'Figma Review & Sign-Off', description: 'Client signs off on design milestone checklist before engineering begins.' },
      { step: '04', title: 'Launch & Retainer Handover', description: 'Final balance invoice clears automatically and client transitions to monthly maintenance retainer.' }
    ],
    toolsReplaced: [
      { name: 'HoneyBook', category: 'CRM & Proposals', monthlySavingsINR: 3200 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Zoho Invoice', category: 'GST Billing', monthlySavingsINR: 1900 }
    ],
    faqs: [
      {
        question: 'How does Cora prevent scope creep for web design projects?',
        answer: 'Cora specifies revision round limits directly in the contract. When a client requests changes outside the original brief, you can generate an instant Change-Order Addendum with one click that the client must digitally sign before work begins.'
      },
      {
        question: 'Can we manage monthly website hosting and maintenance retainers in Cora?',
        answer: 'Yes. You can set up recurring monthly maintenance retainers with automated SAC 998315 tax invoices sent to clients on the 1st of every month.'
      }
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
    heroImage: '/images/bento_crew_camera.jpg',
    relatedIndustrySlugs: ['software-agencies', 'design-uiux', 'marketing-seo', 'consultants-advisors']
  },
  {
    id: 'it-tech-services',
    slug: 'it-tech-services',
    title: 'IT & Tech Services (MSPs)',
    shortTitle: 'IT & Tech Services',
    sectorId: 'tech_dev',
    sectorLabel: 'Tech & Dev',
    sectorBadge: 'TECH & DEV',
    badgeColor: 'blue',
    iconName: 'ShieldCheck',
    tagline: 'Managed IT retainers, uptime SLAs & hardware asset registries.',
    heroDescription: 'Complete operational command center for Managed Service Providers (MSPs), cloud infrastructure consultants, and network engineering firms. Coordinate monthly support SLAs, server hardware check-ins, and automated recurring billing.',
    sacCode: 'SAC 998313',
    gstRate: '18% GST',
    stats: [
      { metric: '99.9%', label: 'SLA uptime logging' },
      { metric: 'Monthly', label: 'Auto recurring retainer runs' },
      { metric: 'Zero', label: 'Billing leakage on support' }
    ],
    theOldWay: [
      'Out-of-scope IT emergency support going unrecorded and unbilled',
      'Hardware equipment and client loaner laptops tracked on scattered spreadsheets',
      'Manual SLA reporting and painful monthly reconciliation for corporate accounts'
    ],
    theCoraWay: [
      'Automated retainer hour metering with instant overage invoicing once contracted hours are exceeded',
      'Hardware asset registry with barcode, serial number, and assigned employee check-out logs',
      'Client portal displaying real-time SLA uptime guarantees and signed IT maintenance contracts'
    ],
    capabilities: [
      {
        title: 'Tiered Managed IT Retainers',
        description: 'Pre-configured support tiers (Standard, Gold, 24/7 Mission-Critical) with automated monthly SAC 998313 invoicing.',
        tag: 'SLA Retainers'
      },
      {
        title: 'Hardware & Infrastructure Registry',
        description: 'Track client servers, network switches, and loaner laptops with depreciation logs and maintenance schedules.',
        tag: 'Asset Hub'
      },
      {
        title: 'Priority Escalation Matrix',
        description: 'Contractually bound response time tiers (P1 Critical 1-hour, P2 Urgent 4-hour, P3 Normal 24-hour).',
        tag: 'SLA Matrix'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Onboarding & Asset Audit', description: 'Log client hardware inventory and infrastructure credentials into secure vault.' },
      { step: '02', title: 'SLA Contract Execution', description: 'Client signs tiered managed services agreement with uptime guarantees.' },
      { step: '03', title: 'Incident & Retainer Metering', description: 'Support hours are tracked against contracted retainer allowance with auto-alerts.' },
      { step: '04', title: 'Monthly GST Ledger', description: 'Automated 18% GST billing runs on the 1st of every month with direct payment links.' }
    ],
    toolsReplaced: [
      { name: 'ConnectWise', category: 'MSP Software', monthlySavingsINR: 8500 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Zoho Books', category: 'Invoicing', monthlySavingsINR: 2400 }
    ],
    faqs: [
      {
        question: 'Can we manage multiple client corporate locations and branch offices in Cora?',
        answer: 'Yes. Cora includes Multi-Branch governance allowing you to segment client hardware assets, user permissions, and SLA contracts by specific branch locations while maintaining consolidated billing.'
      },
      {
        question: 'Does Cora support annual upfront IT maintenance contracts (AMCs)?',
        answer: 'Yes. You can issue Annual Maintenance Contracts (AMCs) with automated quarterly tax invoice schedules or single lump-sum 18% GST billing.'
      }
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
    heroImage: '/images/usecase_commercial_studio.jpg',
    relatedIndustrySlugs: ['software-agencies', 'ai-automation', 'audit-compliance', 'consultants-advisors']
  },
  {
    id: 'ai-automation',
    slug: 'ai-automation',
    title: 'AI & Automation Agencies',
    shortTitle: 'AI Agencies',
    sectorId: 'tech_dev',
    sectorLabel: 'Tech & Dev',
    sectorBadge: 'TECH & DEV',
    badgeColor: 'blue',
    iconName: 'Zap',
    tagline: 'Custom agent workflows, token billing & client AI vaults.',
    heroDescription: 'Specialized business platform for agencies building autonomous AI agents, voice bots, and LLM automation pipelines. Manage token usage billing, RAG document memory bridges, and enterprise IP handover deeds.',
    sacCode: 'SAC 998316',
    gstRate: '18% GST',
    stats: [
      { metric: 'Token-Based', label: 'Flexible usage retainer models' },
      { metric: 'RAG Vaults', label: 'Secure memory bridges' },
      { metric: '3-Min', label: 'Client onboarding to live AI' }
    ],
    theOldWay: [
      'Unclear contract terms regarding AI hallucinations, token overage costs, and prompt IP ownership',
      'Client enterprise security fears regarding corporate data leakage and model training',
      'Manually compiling API token costs from OpenAI, Anthropic, and voice models at month-end'
    ],
    theCoraWay: [
      'Pre-seeded AI Agent Master Services Agreement with explicit IP handover and non-liability terms',
      'Pre-configured Enterprise Data Privacy Addendums ensuring zero data training on private IP',
      'Automated hybrid retainer billing combining fixed monthly maintenance with variable token usage'
    ],
    capabilities: [
      {
        title: 'Enterprise AI Privacy Addendum',
        description: 'Legally vetted contract clauses ensuring client corporate data is strictly isolated and never used for model training.',
        tag: 'Data Privacy'
      },
      {
        title: 'RAG Document Knowledge Depository',
        description: 'Encrypted document vault for client SOPs, PDFs, and API keys that feed private context to your deployed agents.',
        tag: 'RAG Memory'
      },
      {
        title: 'Hybrid Retainer & Token Billing',
        description: 'Combine a monthly agent maintenance retainer with automated usage-based tier invoicing under SAC 998316.',
        tag: 'Token Billing'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Agent Architecture Proposal', description: 'Generate AI workflow scope with model architecture and API requirements.' },
      { step: '02', title: 'Privacy & MSA Execution', description: 'Client executes enterprise privacy deed and development contract with SHA-256 verification.' },
      { step: '03', title: 'RAG Knowledge Ingestion', description: 'Upload client documents and connect API webhooks inside isolated client vault.' },
      { step: '04', title: 'Live Deployment & Retainer', description: 'Agent goes live with automated monthly maintenance and token usage billing.' }
    ],
    toolsReplaced: [
      { name: 'PandaDoc', category: 'Proposals', monthlySavingsINR: 3800 },
      { name: 'Notion Teams', category: 'Knowledge Base', monthlySavingsINR: 2400 },
      { name: 'Zapier Pro', category: 'Workflows', monthlySavingsINR: 4900 }
    ],
    faqs: [
      {
        question: 'Who owns the Intellectual Property (IP) of the AI agents and custom prompts built on Cora?',
        answer: 'Our pre-seeded agreements provide full IP transfer to the client upon final invoice clearance, while licensing generic underlying workflow scaffolding to the agency.'
      },
      {
        question: 'Does Cora integrate with Claude 3.5 Sonnet, Gemini, and GPT-4o?',
        answer: 'Yes. Cora features native multi-model orchestration, allowing your team to draft proposals, extract meeting scopes, and triage client inquiries with the world\'s best AI models.'
      }
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
    heroImage: '/images/usecase_production_house.jpg',
    relatedIndustrySlugs: ['software-agencies', 'web-app-studios', 'consultants-advisors', 'marketing-seo']
  },

  // ── 2. LEGAL & FINANCE SECTOR ──
  {
    id: 'lawyers-law-firms',
    slug: 'lawyers-law-firms',
    title: 'Lawyers & Law Firms',
    shortTitle: 'Lawyers & Law Firms',
    sectorId: 'legal_finance',
    sectorLabel: 'Legal & Finance',
    sectorBadge: 'LEGAL & FINANCE',
    badgeColor: 'slate',
    iconName: 'Scale',
    tagline: 'SHA-256 e-sign vaults, retainer agreements & client intake.',
    heroDescription: 'Airtight digital practice management for advocates, corporate legal counsels, and law firms. Execute IT Act 2000 compliant digital e-signatures, manage monthly corporate retainers, and maintain confidential client document vaults.',
    sacCode: 'SAC 998211',
    gstRate: '18% GST (RCM / Forward)',
    stats: [
      { metric: 'SHA-256', label: 'Cryptographic hash signatures' },
      { metric: '100%', label: 'IT Act 2000 Section 10A valid' },
      { metric: 'Zero', label: 'Lost paper contracts or filings' }
    ],
    theOldWay: [
      'Printing, physical courier delivery, and manual scanning of multi-page agreements',
      'Chasing corporate clients for quarterly retainer payments without automated billing',
      'Client intake details scattered across unencrypted emails and WhatsApp threads'
    ],
    theCoraWay: [
      '5-step guided e-signature flow that captures IP address, user agent, and SHA-256 document hash',
      'Automated corporate legal retainer billing with Reverse Charge (RCM) tax categorization',
      'Confidential client intake questionnaires with conflict-of-interest check verification'
    ],
    capabilities: [
      {
        title: 'IT Act 2000 Digital E-Sign Vault',
        description: 'Execute legally binding contracts with Section 10A validity under Indian jurisdiction with tamper-evident audit logs.',
        tag: 'Legal E-Sign'
      },
      {
        title: 'Corporate Retainer Automation',
        description: 'Automate monthly and quarterly advisory retainers with custom advisory hour tracking and overage invoicing.',
        tag: 'Legal Retainers'
      },
      {
        title: 'Confidential Client Intake & Conflict Check',
        description: 'Secure digital intake questionnaires that screen opposing parties and capture confidential case briefs.',
        tag: 'Intake Vault'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Conflict Check & Intake', description: 'Client completes secure intake form verifying parties and matter details.' },
      { step: '02', title: 'Engagement Letter E-Sign', description: 'Advocate issues pre-seeded engagement letter signed digitally by client.' },
      { step: '03', title: 'Encrypted Matter Depository', description: 'Upload case pleadings, NDAs, and corporate filings into isolated document vault.' },
      { step: '04', title: 'Retainer Billing & RCM', description: 'Automated legal invoice issued with professional SAC 998211 tax classification.' }
    ],
    toolsReplaced: [
      { name: 'DocuSign Pro', category: 'E-Signatures', monthlySavingsINR: 4200 },
      { name: 'Clio Legal', category: 'Practice Management', monthlySavingsINR: 6500 },
      { name: 'Dropbox Business', category: 'Document Storage', monthlySavingsINR: 2800 }
    ],
    faqs: [
      {
        question: 'Are e-signatures generated on Cora legally enforceable in Indian courts?',
        answer: 'Yes. Cora\'s e-signature system complies with Section 10A of the Information Technology Act 2000. Each document generates a cryptographic SHA-256 hash, verified timestamp, IP address, and signer email log.'
      },
      {
        question: 'How does Cora handle GST Reverse Charge Mechanism (RCM) for legal services?',
        answer: 'Under GST notification No. 13/2017, legal services provided by individual advocates or firms to business entities are subject to RCM. Cora includes one-click RCM toggle switches that format tax invoices accordingly.'
      }
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
    heroImage: '/images/bento_crew_camera.jpg',
    relatedIndustrySlugs: ['tax-ca-firms', 'financial-advisors', 'audit-compliance', 'consultants-advisors']
  },
  {
    id: 'tax-ca-firms',
    slug: 'tax-ca-firms',
    title: 'Tax & CA Practices',
    shortTitle: 'Tax & CA Practices',
    sectorId: 'legal_finance',
    sectorLabel: 'Legal & Finance',
    sectorBadge: 'LEGAL & FINANCE',
    badgeColor: 'slate',
    iconName: 'Receipt',
    tagline: '18% GST auto-splitting, SAC audits & client retainer automation.',
    heroDescription: 'The dedicated practice OS for Chartered Accountants, tax advisory firms, and GST practitioners. Automate SAC code allocation, client compliance retainers, dynamic UPI QR collections, and audit proof vaults.',
    sacCode: 'SAC 998222',
    gstRate: '18% GST',
    stats: [
      { metric: '18% GST', label: 'CGST + SGST tax auto-calculation' },
      { metric: 'SAC Codes', label: 'Pre-seeded accounting schemas' },
      { metric: '+45%', label: 'Faster collection via dynamic UPI' }
    ],
    theOldWay: [
      'Manually typing hundreds of recurring GST and TDS compliance invoices every month',
      'Chasing business clients for fee payments with delayed NEFT transfers and lost cheque proofs',
      'Client audit balance sheets and tax returns scattered across messy WhatsApp chats'
    ],
    theCoraWay: [
      'One-click batch generation of recurring monthly GST compliance invoices with instant WhatsApp delivery',
      'Dynamic UPI QR code embedded directly on every invoice for instant phone-to-phone settlement',
      'Encrypted client depository with year-wise categorization for ITR, ROC, and GST filing proofs'
    ],
    capabilities: [
      {
        title: 'Native 18% GST Auto-Tax Engine',
        description: 'Auto-calculates 9% CGST + 9% SGST for intra-state and 18% IGST for inter-state clients with SAC 998222 classification.',
        tag: 'GST Engine'
      },
      {
        title: 'Dynamic UPI QR Billing',
        description: 'Embeds UPI intent QR codes directly on invoices so clients can pay instantly from Google Pay, PhonePe, or Paytm.',
        tag: 'UPI Collections'
      },
      {
        title: 'Audit & Compliance Depository',
        description: 'Organize client tax filings, challans, and ICAI engagement letters in tamper-evident year-wise vaults.',
        tag: 'Audit Vault'
      }
    ],
    howItWorks: [
      { step: '01', title: 'ICAI Engagement Letter', description: 'Issue pre-seeded statutory audit engagement letter with digital client sign-off.' },
      { step: '02', title: 'Monthly Retainer Schedule', description: 'Configure recurring GST & TDS monthly compliance fee schedule.' },
      { step: '03', title: 'Dynamic Invoice Dispatch', description: 'Invoices generated automatically on the 1st with embedded UPI QR codes.' },
      { step: '04', title: 'Audit Proof Archival', description: 'Store filed GSTR-3B, GSTR-1, and 26AS proofs in client document vault.' }
    ],
    toolsReplaced: [
      { name: 'Zoho Books', category: 'Accounting Billing', monthlySavingsINR: 2900 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Google Workspace Storage', category: 'Storage', monthlySavingsINR: 2100 }
    ],
    faqs: [
      {
        question: 'Does Cora comply with the Institute of Chartered Accountants of India (ICAI) engagement norms?',
        answer: 'Yes. Our pre-seeded engagement letter templates adhere to the ICAI Standard on Auditing (SA 210) regarding agreeing the terms of audit engagements.'
      },
      {
        question: 'Can CA firms export sales and billing ledgers to Tally or Zoho Books?',
        answer: 'Yes. Cora provides one-click CSV and JSON ledger exports formatted specifically for seamless import into Tally Prime and Zoho Books.'
      }
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
    heroImage: '/images/usecase_commercial_studio.jpg',
    relatedIndustrySlugs: ['lawyers-law-firms', 'financial-advisors', 'audit-compliance', 'consultants-advisors']
  },
  {
    id: 'financial-advisors',
    slug: 'financial-advisors',
    title: 'Financial Advisors',
    shortTitle: 'Financial Advisors',
    sectorId: 'legal_finance',
    sectorLabel: 'Legal & Finance',
    sectorBadge: 'LEGAL & FINANCE',
    badgeColor: 'slate',
    iconName: 'Briefcase',
    tagline: 'Advisory retainers, wealth portfolios & confidential agreements.',
    heroDescription: 'Client management and compliance platform for SEBI-registered investment advisors (RIAs), wealth managers, and corporate financial planners. Coordinate digital risk profiling, quarterly fee billing, and portfolio reviews.',
    sacCode: 'SAC 997159',
    gstRate: '18% GST',
    stats: [
      { metric: '₹5L+ Avg', label: 'Advisory retainer size supported' },
      { metric: '100%', label: 'Digital risk-profiling compliance' },
      { metric: 'SEBI-Ready', label: 'Complete immutable audit trails' }
    ],
    theOldWay: [
      'Paper risk assessment forms that get misplaced before compliance audits',
      'Manual quarterly advisory fee calculation based on varying AUM tiers',
      'Unencrypted email delivery of confidential net-worth statements'
    ],
    theCoraWay: [
      'Digital risk-profiling questionnaire that calculates investor risk score and stores signed mandate',
      'Automated quarterly advisory retainer invoicing with pre-set percentage and flat fee models',
      'Encrypted client wealth portal with role-based access for HNW clients and family offices'
    ],
    capabilities: [
      {
        title: 'SEBI-Aligned Risk Profiler',
        description: 'Interactive client intake questionnaire that scores risk tolerance and executes investment advisory mandates.',
        tag: 'Risk Profiler'
      },
      {
        title: 'Quarterly Advisory Retainers',
        description: 'Automate quarterly fee invoices under SAC 997159 with clear GST tax math and payment links.',
        tag: 'Wealth Retainers'
      },
      {
        title: 'HNW Client Depository',
        description: 'Isolated client vaults for portfolio rebalancing notes, wealth statements, and tax-loss harvesting reports.',
        tag: 'Wealth Vault'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Risk Assessment Intake', description: 'Client completes digital risk questionnaire determining investment horizon.' },
      { step: '02', title: 'Advisory Mandate E-Sign', description: 'Client signs SEBI-aligned investment advisory contract with SHA-256 hash.' },
      { step: '03', title: 'Quarterly Invoicing', description: 'Advisory fee invoice is generated with automated 18% GST tax breakdown.' },
      { step: '04', title: 'Portfolio Review Archival', description: 'Upload quarterly review notes and allocation decks to client portal.' }
    ],
    toolsReplaced: [
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Typeform', category: 'Risk Forms', monthlySavingsINR: 2800 },
      { name: 'FreshBooks', category: 'Invoicing', monthlySavingsINR: 2400 }
    ],
    faqs: [
      {
        question: 'Does Cora support both flat-fee and percentage AUM advisory billing?',
        answer: 'Yes. You can configure fixed retainer schedules (e.g. ₹50,000/quarter) or custom asset-under-management (AUM) fee calculations with SAC 997159 tax breakdowns.'
      },
      {
        question: 'How is confidential client financial data secured in Cora?',
        answer: 'Every workspace features complete multi-tenant database isolation, AES-256 storage encryption, and role-based permissions preventing unauthorized team or cross-client data access.'
      }
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
    heroImage: '/images/usecase_production_house.jpg',
    relatedIndustrySlugs: ['lawyers-law-firms', 'tax-ca-firms', 'audit-compliance', 'consultants-advisors']
  },
  {
    id: 'audit-compliance',
    slug: 'audit-compliance',
    title: 'Audit & Compliance',
    shortTitle: 'Audit & Compliance',
    sectorId: 'legal_finance',
    sectorLabel: 'Legal & Finance',
    sectorBadge: 'LEGAL & FINANCE',
    badgeColor: 'slate',
    iconName: 'Layers',
    tagline: 'Regulatory audit trails, capability matrix & verified logs.',
    heroDescription: 'Enterprise compliance management system for secretarial audit firms, ISO certification consultants, and internal risk auditors. Coordinate multi-branch inspection checklists, evidence vaults, and board resolution sign-offs.',
    sacCode: 'SAC 998223',
    gstRate: '18% GST',
    stats: [
      { metric: '30-Point', label: 'Governance audit matrix' },
      { metric: 'Immutable', label: 'SHA-256 verified evidence logs' },
      { metric: 'Multi-Branch', label: 'Enterprise data isolation' }
    ],
    theOldWay: [
      'Scattered audit evidence across disconnected email chains and personal drives',
      'Unverifiable timestamps on corporate governance inspection checklists',
      'Manual compilation of executive audit reports taking weeks of associate time'
    ],
    theCoraWay: [
      'Centralized evidence vault where uploaded compliance proofs are timestamped with SHA-256 hashes',
      'Structured 30-point capability and security matrix with assigned department owners',
      'One-click consolidated audit dashboard for presenting findings to the board of directors'
    ],
    capabilities: [
      {
        title: '30-Point Security & Audit Matrix',
        description: 'Pre-seeded compliance checklist covering statutory filings, access controls, and financial reconciliations.',
        tag: 'Audit Matrix'
      },
      {
        title: 'Immutable Evidence Depository',
        description: 'Upload audit proofs with cryptographic verification ensuring evidence cannot be altered after the fact.',
        tag: 'Evidence Vault'
      },
      {
        title: 'Multi-Branch Governance Hub',
        description: 'Audit and monitor compliance status across multiple corporate regional branches from a single unified screen.',
        tag: 'Multi-Branch'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Engagement Mandate', description: 'Issue pre-seeded corporate governance audit agreement to client board.' },
      { step: '02', title: 'Checklist Assignment', description: 'Assign compliance control points to specific company department leads.' },
      { step: '03', title: 'Evidence Ingestion', description: 'Collect and verify timestamped compliance proofs in the secure vault.' },
      { step: '04', title: 'Executive Sign-Off', description: 'Issue final audit report with multi-party digital signatures from statutory auditors.' }
    ],
    toolsReplaced: [
      { name: 'AuditBoard', category: 'Compliance Software', monthlySavingsINR: 12000 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Box Enterprise', category: 'Vault', monthlySavingsINR: 3800 }
    ],
    faqs: [
      {
        question: 'Can multiple auditors and company directors review and sign off on audit findings simultaneously?',
        answer: 'Yes. Cora supports multi-party sequential or parallel digital signature workflows where each director and auditor signs with their own verified credentials.'
      },
      {
        question: 'How does Cora maintain branch-level data segregation for corporate groups?',
        answer: 'Cora uses isolated workspace architecture allowing corporate groups to create dedicated sub-workspaces for each branch while giving the lead auditor super-admin oversight.'
      }
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
    heroImage: '/images/usecase_realestate_agency.jpg',
    relatedIndustrySlugs: ['lawyers-law-firms', 'tax-ca-firms', 'financial-advisors', 'consultants-advisors']
  },

  // ── 3. MARKETING & CREATIVE SECTOR ──
  {
    id: 'marketing-seo',
    slug: 'marketing-seo',
    title: 'Marketing & SEO',
    shortTitle: 'Marketing & SEO',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Creative',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'BarChart2',
    tagline: 'Retainer billing, SEO audits, ad spend tracking & dashboards.',
    heroDescription: 'Consolidated operating system for performance marketing agencies, SEO consultancies, and growth studios. Replace 5+ fragmented apps with automated monthly retainer billing, SEO brief vaults, and white-label client reporting.',
    sacCode: 'SAC 998361',
    gstRate: '18% GST',
    stats: [
      { metric: '₹1.8L/Mo', label: 'Saved by consolidating SaaS' },
      { metric: 'Monthly', label: 'Auto recurring retainer runs' },
      { metric: '1-Click', label: 'SEO audit & script generator' }
    ],
    theOldWay: [
      'Paying thousands of dollars every month for Notion, HoneyBook, DocuSign, ChatGPT, and Zapier',
      'Clients disputing monthly ad spend fees and SEO deliverable completion',
      'Manually compiling monthly PDF performance reports taking entire team weekends'
    ],
    theCoraWay: [
      'One single unified workspace handling client CRM, proposal e-signing, AI copywriting, and 18% GST billing',
      'Clear deliverable milestones and monthly retainer agreements executed with digital e-signatures',
      'White-label client portal displaying real-time deliverable status, approved scripts, and paid invoices'
    ],
    capabilities: [
      {
        title: 'Monthly Growth Retainer Engine',
        description: 'Automate recurring agency retainers with custom deliverable scopes and SAC 998361 tax breakdown.',
        tag: 'Agency Retainers'
      },
      {
        title: 'Content AI & Viral Scriptwriter',
        description: 'Generate 3-act viral video scripts, SEO articles, and ad headlines using built-in Claude 3.5 Sonnet and GPT-4o.',
        tag: 'Content AI'
      },
      {
        title: 'White-Label Client Portal',
        description: 'Branded client dashboard where clients review deliverables, approve campaign briefs, and download tax invoices.',
        tag: 'Client Portal'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Retainer Scope Assembly', description: 'Assemble monthly deliverable package (SEO, Meta Ads, Copywriting) in 2 minutes.' },
      { step: '02', title: 'Contract E-Execution', description: 'Client signs digital retainer agreement with monthly automated billing terms.' },
      { step: '03', title: 'Content AI Production', description: 'Team drafts high-converting scripts and articles with living workspace context.' },
      { step: '04', title: 'Monthly GST Invoicing', description: 'Recurring 18% GST invoice is generated and delivered automatically via WhatsApp.' }
    ],
    toolsReplaced: [
      { name: 'HoneyBook', category: 'Proposals & Billing', monthlySavingsINR: 3200 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'ChatGPT Plus (5 seats)', category: 'AI Copywriting', monthlySavingsINR: 9000 },
      { name: 'Zapier', category: 'Automations', monthlySavingsINR: 3900 }
    ],
    faqs: [
      {
        question: 'How does Cora replace multiple ChatGPT or Claude seats for our marketing agency?',
        answer: 'Cora includes built-in multi-model AI (Claude 3.5 Sonnet, Gemini, GPT-4o) directly inside your team workspace with living context of your client briefs, contracts, and tone of voice.'
      },
      {
        question: 'Can we white-label the client portal with our agency domain and logo?',
        answer: 'Yes. You can connect your custom domain (e.g. portal.youragency.com) with custom logos, branding colors, and verified SMTP emails.'
      }
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
    heroImage: '/images/usecase_realestate_agency.jpg',
    relatedIndustrySlugs: ['design-uiux', 'photo-video-studios', 'web-app-studios', 'consultants-advisors']
  },
  {
    id: 'design-uiux',
    slug: 'design-uiux',
    title: 'Design & UI/UX',
    shortTitle: 'Design & UI/UX',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Creative',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'Sparkles',
    tagline: 'Design sprints, asset portals & client Figma approvals.',
    heroDescription: 'Purpose-built workspace for brand identity studios, visual designers, and UI/UX product teams. Manage 2-week design sprints, client Figma sign-offs, high-resolution asset vaults, and copyright assignment deeds.',
    sacCode: 'SAC 998391',
    gstRate: '18% GST',
    stats: [
      { metric: '2-Week', label: 'Structured design sprint gates' },
      { metric: 'Zero', label: 'Unapproved Figma revision loops' },
      { metric: 'Instant', label: 'Brand asset delivery vaults' }
    ],
    theOldWay: [
      'Clients requesting endless design variations without formal milestone sign-offs',
      'Sending final vector logos and source files before collecting the final invoice payment',
      'No formal copyright transfer deed protecting both designer and client'
    ],
    theCoraWay: [
      'Design sprint gates requiring written client checklist sign-off before progressing to next phase',
      'Gated asset delivery vault that unlocks high-res exports only after final GST invoice clearance',
      'Pre-seeded Intellectual Property Assignment Deed executed automatically upon project wrap'
    ],
    capabilities: [
      {
        title: 'Design Sprint Milestone Gates',
        description: 'Structured 3-phase delivery (Moodboard & Concept, High-Fi Prototype, Final Token Export).',
        tag: 'Sprint Gates'
      },
      {
        title: 'Gated Brand Asset Vault',
        description: 'Deliver vector SVG logos, design tokens, and source files with automated download locking until payment clears.',
        tag: 'Asset Vault'
      },
      {
        title: 'Copyright Assignment Deed',
        description: 'Formal legal transfer of design copyright compliant with the Indian Copyright Act 1957.',
        tag: 'IP Transfer'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Interactive Brief Intake', description: 'Client fills visual design brief questionnaire with brand references.' },
      { step: '02', title: '50% Advance E-Sign', description: 'Client signs design contract and clears 50% booking deposit.' },
      { step: '03', title: 'Figma Milestone Sign-Off', description: 'Client reviews interactive prototype and signs milestone approval.' },
      { step: '04', title: 'Final Invoice & IP Deed', description: 'Final invoice clears and high-res asset vault unlocks with signed IP deed.' }
    ],
    toolsReplaced: [
      { name: 'HoneyBook', category: 'Proposals', monthlySavingsINR: 3200 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Dropbox Pro', category: 'Asset Delivery', monthlySavingsINR: 2200 }
    ],
    faqs: [
      {
        question: 'How does Cora handle copyright assignment for graphic design and branding projects?',
        answer: 'Cora includes pre-seeded Intellectual Property and Copyright Assignment Deeds under Section 19 of the Indian Copyright Act 1957. The deed is executed digitally upon receipt of the final project balance.'
      },
      {
        question: 'Can we share clickable prototype links and asset downloads directly in the client portal?',
        answer: 'Yes. You can embed Figma links, design tokens, and high-resolution downloadable packages directly inside the client\'s branded vault.'
      }
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
    heroImage: '/images/usecase_solo_creator.jpg',
    relatedIndustrySlugs: ['web-app-studios', 'marketing-seo', 'photo-video-studios', 'architecture-interiors']
  },
  {
    id: 'photo-video-studios',
    slug: 'photo-video-studios',
    title: 'Photo & Video Studios',
    shortTitle: 'Photo & Video Studios',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Creative',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'Camera',
    tagline: 'Production scopes, 4K proofing, call sheets & gear tracking.',
    heroDescription: 'Complete studio management system for commercial photography studios, film production houses, and fashion creators. Coordinate multi-day shoot bookings, camera gear registries, crew call sheets, and 4K proofing galleries.',
    sacCode: 'SAC 998381',
    gstRate: '18% GST',
    stats: [
      { metric: '50% Advance', label: 'Deposit locked on contract e-sign' },
      { metric: '8K Ready', label: 'RAW proofing & delivery vaults' },
      { metric: 'Conflict-Free', label: 'Crew & equipment scheduling' }
    ],
    theOldWay: [
      'Crew scheduling double-bookings and unreturned camera gear from remote shoots',
      'Clients canceling shoots at the last minute without signed non-refundable deposit agreements',
      'Delivering unwatermarked RAW photos and waiting months for final client payment'
    ],
    theCoraWay: [
      'Centralized master calendar that automatically verifies crew availability and gear checkout status',
      'Mandatory 50% advance booking deposit invoice generated automatically upon contract signature',
      'Watermarked client photo and video proofing portal where clients select favorites and unlock downloads upon payment'
    ],
    capabilities: [
      {
        title: 'Master Shoot Calendar & Call Sheets',
        description: 'Generate professional crew call sheets with location maps, weather notes, and talent contact lists.',
        tag: 'Call Sheets'
      },
      {
        title: 'Camera Gear & Asset Registry',
        description: 'Track cameras, cinema lenses, lighting kits, and audio gear with serial numbers and check-out logs.',
        tag: 'Gear Hub'
      },
      {
        title: 'Watermarked 4K/8K Proofing Portal',
        description: 'Client review gallery with digital watermarking, selection star ratings, and instant approval locks.',
        tag: 'Proofing Hub'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Shoot Scope & Quote', description: 'Assemble crew day-rates, gear rentals, and post-production hours in 2 minutes.' },
      { step: '02', title: 'Contract & 50% Advance', description: 'Client signs commercial production contract and clears 50% booking deposit.' },
      { step: '03', title: 'Crew Dispatch & Call Sheet', description: 'Automated call sheets sent to camera ops, gaffers, and stylists via WhatsApp.' },
      { step: '04', title: 'Proofing & Final Delivery', description: 'Client approves watermarked gallery and clears final balance to unlock 4K downloads.' }
    ],
    toolsReplaced: [
      { name: 'Studio Ninja', category: 'Studio CRM', monthlySavingsINR: 3200 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Pixieset', category: 'Photo Proofing', monthlySavingsINR: 2800 },
      { name: 'Cheqroom', category: 'Gear Tracking', monthlySavingsINR: 4500 }
    ],
    faqs: [
      {
        question: 'Does Cora include model and talent release agreements?',
        answer: 'Yes. Cora includes pre-seeded Indian jurisdiction Model and Location Release forms that models and venue managers can sign directly on a mobile phone on shoot day.'
      },
      {
        question: 'Can we track multi-day commercial shoots with separate pre-pro, shoot, and editing phases?',
        answer: 'Yes. Cora\'s master calendar and milestone engine allow you to schedule pre-production prep days, active shoot dates, and post-production review milestones seamlessly.'
      }
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
    heroImage: '/images/usecase_commercial_studio.jpg',
    relatedIndustrySlugs: ['design-uiux', 'marketing-seo', 'architecture-interiors', 'real-estate-property']
  },
  {
    id: 'architecture-interiors',
    slug: 'architecture-interiors',
    title: 'Architecture & Interiors',
    shortTitle: 'Architecture & Interiors',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Creative',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'Building2',
    tagline: '3D CAD milestones, blueprint approvals & progressive draws.',
    heroDescription: 'Project governance and progressive billing operating system for architectural practices, interior design consultancies, and turnkey contracting firms. Coordinate 3D CAD sign-offs, change-orders, and progressive construction draw schedules.',
    sacCode: 'SAC 998321',
    gstRate: '18% GST',
    stats: [
      { metric: '4-Stage', label: 'Concept to handover draw schedules' },
      { metric: '100%', label: 'Signed client change-order protection' },
      { metric: 'Zero', label: 'Uncollected contractor milestone fees' }
    ],
    theOldWay: [
      'Clients requesting material or structural changes on site without written cost approval',
      'Contractors executing work before the client has cleared the progressive milestone draw',
      'Scattered blueprint versions and CAD files across emails and WhatsApp groups'
    ],
    theCoraWay: [
      'Mandatory digital change-order sign-offs that update the project total before site execution',
      'Progressive draw schedule (25% Concept, 30% 3D CAD, 25% GFC, 20% Handover) with automated GST invoicing',
      'Integrated document depository for Good-for-Construction (GFC) drawings, 3D renders, and material specifications'
    ],
    capabilities: [
      {
        title: 'Progressive Milestone Draw Engine',
        description: 'Link progressive payment draws to verified construction and design completion stages under SAC 998321.',
        tag: 'Progressive Draws'
      },
      {
        title: 'Digital Site Change-Order Builder',
        description: 'Quote client material variances and structural additions instantly with mandatory digital signature.',
        tag: 'Change-Orders'
      },
      {
        title: 'GFC Blueprint & CAD Depository',
        description: 'Secure client portal for sharing Good-for-Construction drawings, 3D walkthroughs, and finish schedules.',
        tag: 'Blueprint Vault'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Consultancy Mandate', description: 'Client executes architectural master agreement with progressive draw milestones.' },
      { step: '02', title: 'Concept & 3D Approval', description: 'Client approves 3D renders and signs milestone sign-off form in portal.' },
      { step: '03', title: 'GFC Drawing Release', description: 'Good-for-Construction drawings released to site contractors upon milestone payment.' },
      { step: '04', title: 'Site Handover Wrap', description: 'Final 20% handover invoice cleared with executed completion protocol.' }
    ],
    toolsReplaced: [
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Houzz Pro', category: 'Design Management', monthlySavingsINR: 5800 },
      { name: 'Zoho Invoice', category: 'Invoicing', monthlySavingsINR: 1900 }
    ],
    faqs: [
      {
        question: 'Does Cora support Indian Council of Architecture (CoA) fee guidelines?',
        answer: 'Yes. Our pre-seeded architectural agreements can be aligned with Council of Architecture percentage-of-cost fee schedules or fixed per-sqft consultancy models.'
      },
      {
        question: 'How does Cora handle site variance change-orders?',
        answer: 'You can create a change-order addendum with one click detailing the material difference, added contractor labor, and updated GST tax total. Work proceeds only when the client signs.'
      }
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
    heroImage: '/images/usecase_solo_creator.jpg',
    relatedIndustrySlugs: ['design-uiux', 'real-estate-property', 'photo-video-studios', 'consultants-advisors']
  },

  // ── 4. SERVICES & LIFESTYLE SECTOR ──
  {
    id: 'consultants-advisors',
    slug: 'consultants-advisors',
    title: 'Consultants & Advisors',
    shortTitle: 'Consultants & Advisors',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Lifestyle',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Briefcase',
    tagline: 'Diagnostic audits, scopes & executive board decks.',
    heroDescription: 'Executive operating system for management consultants, strategy advisors, and corporate transformation experts. Turn discovery meeting notes into formal advisory agreements in 2 minutes, manage retainer milestones, and share executive decks.',
    sacCode: 'SAC 998311',
    gstRate: '18% GST',
    stats: [
      { metric: '2 Minutes', label: 'Voice brief to formal proposal' },
      { metric: '₹5L+ Avg', label: 'Advisory retainer size supported' },
      { metric: 'Multi-Seat', label: 'Role-based access for teams' }
    ],
    theOldWay: [
      'Spending 4-6 hours typing formal proposal documents after discovery meetings',
      'Unclear milestone deliverables leading to executive scope disputes and delayed retainer payments',
      'Delivering strategy decks over unencrypted emails with zero download tracking'
    ],
    theCoraWay: [
      'Voice-to-Scope AI that converts meeting audio or notes into ready-to-sign advisory contracts in 2 minutes',
      'Clear multi-stage milestone structure (Advance, Diagnostic Wrap, Board Review) with automated GST invoicing',
      'White-label executive presentation depository for sharing strategy decks and tracking client engagement'
    ],
    capabilities: [
      {
        title: 'Voice-to-Scope AI Proposal Engine',
        description: 'Upload discovery call audio or paste notes to generate formal advisory agreements with deliverables and pricing.',
        tag: 'Voice-to-Scope'
      },
      {
        title: 'Strategic Advisory Retainers',
        description: 'Automate monthly and quarterly advisory retainers with custom advisory hour tracking and board review milestones.',
        tag: 'Advisory Retainers'
      },
      {
        title: 'Executive Presentation Vault',
        description: 'Share diagnostic audits, transformation decks, and financial models in an encrypted white-label executive portal.',
        tag: 'Executive Vault'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Voice Discovery Capture', description: 'Record meeting notes and convert into structured advisory scope with AI.' },
      { step: '02', title: 'Mandate E-Signature', description: 'Client board signs engagement contract with SHA-256 digital verification.' },
      { step: '03', title: 'Executive Portal Handover', description: 'Share diagnostic decks and roadmap milestones in branded client vault.' },
      { step: '04', title: 'Retainer Tax Settlement', description: 'Automated 18% GST invoice issued with professional SAC 998311 tax classification.' }
    ],
    toolsReplaced: [
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Otter.ai', category: 'Transcription', monthlySavingsINR: 2200 },
      { name: 'PandaDoc', category: 'Proposals', monthlySavingsINR: 3800 }
    ],
    faqs: [
      {
        question: 'How accurate is the Voice-to-Scope AI in capturing consulting industry terminology?',
        answer: 'Our Voice-to-Scope engine is fine-tuned specifically on management consulting, M&A, diagnostic audits, and IT advisory terminology to generate professional, legally sound scopes.'
      },
      {
        question: 'Can we configure multi-phase advisory milestones with advance deposits?',
        answer: 'Yes. You can structure advisory engagements with upfront diagnostic advances, milestone deliverables, and ongoing monthly governance retainers.'
      }
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
    heroImage: '/images/usecase_production_house.jpg',
    relatedIndustrySlugs: ['lawyers-law-firms', 'financial-advisors', 'software-agencies', 'marketing-seo']
  },
  {
    id: 'doctors-clinics',
    slug: 'doctors-clinics',
    title: 'Doctors & Clinics',
    shortTitle: 'Doctors & Clinics',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Lifestyle',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Heart',
    tagline: 'Patient intake booking, consent e-signs & confidential records.',
    heroDescription: 'Paperless clinic workflow and patient appointment OS for specialized wellness practices, dental clinics, physiotherapy centers, and medical consultancies. Coordinate online intake forms, digital patient consent e-signs, and instant fee receipts.',
    sacCode: 'SAC 999312',
    gstRate: 'Exempt / 18% GST (Cosmetic)',
    stats: [
      { metric: '100% Digital', label: 'Paperless intake forms and consent' },
      { metric: 'Instant', label: 'Automated booking confirmation' },
      { metric: 'Encrypted', label: 'Confidential patient vault' }
    ],
    theOldWay: [
      'Manual paper intake forms creating long waiting room queues and illegible hand-written records',
      'Patients missing appointments due to lack of automated WhatsApp reminders',
      'Unsigned or misplaced procedure consent forms exposing practitioners to legal liabilities'
    ],
    theCoraWay: [
      'Mobile-friendly digital intake forms sent to patients via WhatsApp prior to clinic arrival',
      'Automated appointment calendar with WhatsApp reminders that reduce no-shows by 60%',
      'Digital patient consent e-signatures with legal timestamping before medical or aesthetic procedures'
    ],
    capabilities: [
      {
        title: 'Paperless Digital Intake Forms',
        description: 'Send mobile intake questionnaires capturing medical history, allergies, and symptoms prior to appointment.',
        tag: 'Patient Intake'
      },
      {
        title: 'Digital Medical Consent Vault',
        description: 'Pre-seeded procedure consent agreements executed with verified digital signature before treatment begins.',
        tag: 'Consent Vault'
      },
      {
        title: 'Instant UPI Fee Settlement',
        description: 'Generate instant digital tax receipts with dynamic UPI QR codes for seamless front-desk checkout.',
        tag: 'UPI Quick-Bill'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Online Slot Booking', description: 'Patient selects doctor and consultation slot on interactive clinic calendar.' },
      { step: '02', title: 'WhatsApp Intake Link', description: 'Patient receives digital intake questionnaire and medical history form via WhatsApp.' },
      { step: '03', title: 'Digital Consent Sign-Off', description: 'Patient digitally signs procedure consent form on their phone.' },
      { step: '04', title: 'Instant Receipt & Review', description: 'Fee receipt delivered instantly via SMS/WhatsApp with 1-click Google review request.' }
    ],
    toolsReplaced: [
      { name: 'Practo Ray', category: 'Clinic Management', monthlySavingsINR: 4500 },
      { name: 'DocuSign', category: 'Consent Forms', monthlySavingsINR: 3500 },
      { name: 'Typeform', category: 'Intake Forms', monthlySavingsINR: 2800 }
    ],
    faqs: [
      {
        question: 'Is healthcare consultation exempt from GST in India?',
        answer: 'Yes. Under GST Notification No. 12/2017, health care services by a clinical establishment or authorized medical practitioner are exempt from GST (SAC 999312). However, specialized cosmetic or hair transplant services are taxable at 18%, both of which Cora handles seamlessly.'
      },
      {
        question: 'How are patient medical records and intake details kept private?',
        answer: 'Cora encrypts all patient data at rest with AES-256 and provides role-based access so only authorized clinical staff can view confidential medical histories.'
      }
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
    sampleRetainerText: 'Specialized Consultation Package: 6-Session Clinical Wellness Protocol at ₹45,00,000 with digital intake and secure progress tracking.',
    accentBg: 'bg-teal-50/60',
    accentBorder: 'border-teal-200/80',
    accentText: 'text-teal-700',
    heroImage: '/images/usecase_solo_creator.jpg',
    relatedIndustrySlugs: ['salons-wellness', 'consultants-advisors', 'lawyers-law-firms', 'financial-advisors']
  },
  {
    id: 'salons-wellness',
    slug: 'salons-wellness',
    title: 'Salons, Spas & Wellness',
    shortTitle: 'Salons & Wellness',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Lifestyle',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Scissors',
    tagline: 'Service menu packages, slot booking & instant UPI billing.',
    heroDescription: 'Turnkey operating system for high-end luxury salons, boutique spas, and aesthetic wellness centers. Manage specialist stylist calendars, bridal beauty package deposits, rapid UPI checkout, and automated Google review collection.',
    sacCode: 'SAC 999721',
    gstRate: '18% GST',
    stats: [
      { metric: 'Zero', label: 'Front-desk checkout queues' },
      { metric: 'Instant', label: 'Dynamic UPI QR billing' },
      { metric: '-60%', label: 'No-shows with WhatsApp alerts' }
    ],
    theOldWay: [
      'Clients booking expensive bridal or package slots and canceling at the last minute without deposits',
      'Slow manual billing at the front desk creating checkout queues during peak weekend hours',
      'Stylist chair overbooking leading to frustrated clients and negative online reviews'
    ],
    theCoraWay: [
      'Online visual service menu with mandatory advance deposit collection on premium bridal packages',
      'Rapid dynamic UPI QR billing allowing clients to scan and pay from their chairs in seconds',
      'Specialist chair allocation calendar with automated WhatsApp reminders that eliminate no-shows'
    ],
    capabilities: [
      {
        title: 'Visual Service Menu & Bridal Booking',
        description: 'Interactive catalog for hair, skin, and spa packages with built-in deposit collection.',
        tag: 'Service Menu'
      },
      {
        title: 'Stylist & Chair Allocation Calendar',
        description: 'Manage specialist stylists and treatment rooms preventing double-bookings and scheduling conflicts.',
        tag: 'Stylist Hub'
      },
      {
        title: 'Rapid UPI Quick-Billing & Reviews',
        description: 'Generate dynamic UPI QR codes and trigger automated 5-star Google review requests upon checkout.',
        tag: 'UPI Quick-Bill'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Package Selection', description: 'Client selects beauty service package or bridal service from visual menu.' },
      { step: '02', title: 'Deposit E-Agreement', description: 'Client clears 50% deposit locking specialist stylist and date.' },
      { step: '03', title: 'WhatsApp Appointment Alert', description: 'Automated WhatsApp reminder sent 24 hours and 2 hours before appointment.' },
      { step: '04', title: 'UPI Quick-Bill & Review', description: 'Client scans QR code to pay balance and receives automated review prompt.' }
    ],
    toolsReplaced: [
      { name: 'Fresha / Shedul', category: 'Salon Software', monthlySavingsINR: 4200 },
      { name: 'DocuSign', category: 'Deposit Agreements', monthlySavingsINR: 3500 },
      { name: 'WhatsApp Business API', category: 'Alerts', monthlySavingsINR: 2000 }
    ],
    faqs: [
      {
        question: 'Can we collect advance booking deposits for high-ticket bridal and event packages?',
        answer: 'Yes. Cora allows you to require a mandatory 50% booking deposit on premium packages executed with a digital agreement that protects your salon against last-minute cancellations.'
      },
      {
        question: 'How does the automated Google review collection work?',
        answer: 'When a bill is marked paid at checkout, Cora automatically sends a polite WhatsApp message thanking the client and providing a direct link to leave a 5-star Google review.'
      }
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
    heroImage: '/images/bento_crew_camera.jpg',
    relatedIndustrySlugs: ['doctors-clinics', 'photo-video-studios', 'consultants-advisors', 'real-estate-property']
  },
  {
    id: 'real-estate-property',
    slug: 'real-estate-property',
    title: 'Real Estate & Property',
    shortTitle: 'Real Estate & Property',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Lifestyle',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Building2',
    tagline: 'Property listings, brokerage contracts & 4K media shoots.',
    heroDescription: 'All-in-one brokerage operating system for commercial real estate consultants, property advisory firms, and luxury realtors. Manage exclusive mandate contracts, listing microsites, property media shoots, and commission invoicing.',
    sacCode: 'SAC 997212',
    gstRate: '18% GST',
    stats: [
      { metric: '1-Click', label: 'Listing AI description builder' },
      { metric: '100%', label: 'Signed mandate protection' },
      { metric: '4K Ready', label: 'Virtual tour media vaults' }
    ],
    theOldWay: [
      'Disputes over brokerage commission percentages after commercial lease closures',
      'Spending hours writing property listing descriptions across multiple portals',
      'No formal digital agreement with property owners before spending money on professional photography'
    ],
    theCoraWay: [
      'Pre-signed exclusive brokerage mandate agreements executed digitally before showing properties',
      'Listing AI tool that generates high-converting, compliant property copy in 10 seconds',
      'Dedicated property listing microsites with built-in lead capture forms and virtual tour embeds'
    ],
    capabilities: [
      {
        title: 'Exclusive Brokerage Mandates',
        description: 'Legally binding commission agreements under Indian contract law securing your 1-2 month brokerage fee.',
        tag: 'Brokerage Mandate'
      },
      {
        title: 'Listing AI Description Generator',
        description: 'Generate luxury property descriptions, social copy, and email blasts from basic carpet area and amenity inputs.',
        tag: 'Listing AI'
      },
      {
        title: 'Property Microsite & Media Hub',
        description: 'Host 4K virtual tours, floor plans, and RERA registration documents on branded property landing pages.',
        tag: 'Property Hub'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Owner Mandate Execution', description: 'Property owner executes exclusive brokerage contract with agreed commission terms.' },
      { step: '02', title: 'Listing AI Generation', description: 'Generate high-converting property copy and floor plan specs in seconds.' },
      { step: '03', title: 'Virtual Tour & Media Hub', description: 'Publish property microsite with lead capture form and 4K photo gallery.' },
      { step: '04', title: 'Commission Invoicing', description: 'Issue automated SAC 997212 commission invoice upon lease or sale execution.' }
    ],
    toolsReplaced: [
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'LeadSquared', category: 'Real Estate CRM', monthlySavingsINR: 6500 },
      { name: 'Copy.ai', category: 'Listing Copy', monthlySavingsINR: 2800 }
    ],
    faqs: [
      {
        question: 'Does Cora protect brokers against direct deals between landlords and clients?',
        answer: 'Yes. Our pre-seeded Exclusive Brokerage Mandate agreements include non-circumvention and client introduction registration clauses valid under Indian contract law.'
      },
      {
        question: 'Can we create standalone single-property landing pages for luxury listings?',
        answer: 'Yes. Cora\'s Funnel Builder lets you launch branded property microsites with high-resolution photo galleries, floor plans, and lead capture forms in minutes.'
      }
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
    heroImage: '/images/usecase_realestate_agency.jpg',
    relatedIndustrySlugs: ['photo-video-studios', 'architecture-interiors', 'lawyers-law-firms', 'consultants-advisors']
  }
];
