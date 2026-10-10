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
    label: 'All Workspaces',
    shortLabel: 'All',
    badge: '16 WORKSPACES',
    color: 'zinc',
    iconName: 'Layers',
    count: 16
  },
  {
    id: 'tech_dev',
    label: 'Tech & Dev',
    shortLabel: 'Tech & Dev',
    badge: 'TECH & DEV',
    color: 'blue',
    iconName: 'Code',
    count: 4
  },
  {
    id: 'marketing_creative',
    label: 'Marketing & Design',
    shortLabel: 'Marketing & Design',
    badge: 'MARKETING & DESIGN',
    color: 'purple',
    iconName: 'Sparkles',
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
    id: 'services_lifestyle',
    label: 'Services & Advisory',
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
    tagline: 'Project retainers, milestone sign-offs & client delivery portals.',
    heroDescription: 'The all-in-one operating system for software development agencies and IT consultancies. Manage monthly retainers, milestone approvals, client staging sign-offs, and automated GST billing in one place.',
    sacCode: 'SAC 998314',
    gstRate: '18% GST',
    stats: [
      { metric: '3X Faster', label: 'Proposal to signed contract' },
      { metric: '18% GST', label: 'Automated tax calculations' },
      { metric: '100%', label: 'Milestone sign-off before launch' }
    ],
    theOldWay: [
      'Unsigned client change requests causing unbilled developer hours and scope creep',
      'Manually calculating 18% GST on monthly retainers and sending separate PDF invoices',
      'Delivering code to production without recorded, legally binding client milestone sign-offs'
    ],
    theCoraWay: [
      'Instant change-order generator that quotes extra hours and collects client signatures before work starts',
      'Automated GST invoicing with instant UPI QR codes and bank transfer payment links',
      'Milestone approval portals where clients review and sign off before final launch'
    ],
    capabilities: [
      {
        title: 'Monthly Retainer & Hours Engine',
        description: 'Automate monthly or sprint retainers with tracked hours, rollover balances, and automated overage billing.',
        tag: 'Retainers'
      },
      {
        title: 'Milestone Staging Approvals',
        description: 'Client staging portals where stakeholders inspect deliverables and approve completion before production launch.',
        tag: 'Milestone Gates'
      },
      {
        title: 'Automated GST Invoicing',
        description: 'Pre-configured Indian tax engine for domestic and export services with automatic tax splitting.',
        tag: '18% GST Engine'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Scope Definition', description: 'Turn client conversations and notes into structured project milestones in minutes.' },
      { step: '02', title: 'Digital Contract', description: 'Client signs the master service agreement online with legally binding digital signatures.' },
      { step: '03', title: 'Milestone Review', description: 'Client reviews the test build and approves the release checklist with one click.' },
      { step: '04', title: 'Automated Payment', description: 'GST invoice is generated automatically with instant UPI and bank transfer links.' }
    ],
    toolsReplaced: [
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Jira Core', category: 'Task Management', monthlySavingsINR: 4200 },
      { name: 'FreshBooks', category: 'Invoicing & Tax', monthlySavingsINR: 2800 },
      { name: 'Zapier', category: 'Automations', monthlySavingsINR: 3900 }
    ],
    faqs: [
      {
        question: 'How does Cora handle recurring retainers vs fixed-price milestones?',
        answer: 'Cora supports both recurring monthly retainers (fixed monthly fee with allocated hours) and fixed-price milestone schedules (e.g. 50% advance, 25% staging sign-off, 25% launch). Both trigger automated GST invoices upon approval.'
      },
      {
        question: 'Are digital contracts signed in Cora legally binding in India?',
        answer: 'Yes. Every agreement signed on Cora generates a secure verification audit trail with IP address, device details, and timestamp fully compliant with Section 10A of the Indian Information Technology Act 2000.'
      },
      {
        question: 'Can we bill international clients with 0% GST (Export of Services)?',
        answer: 'Yes. Cora automatically detects foreign currency invoicing and applies 0% export GST classification with mandatory LUT (Letter of Undertaking) declaration fields.'
      }
    ],
    preSeededTemplates: [
      'Master Services Agreement (Software Development)',
      'Agile Sprint Retainer & SLA Schedule',
      'Milestone Staging Sign-Off & Release Approval',
      'Change-Order Request & Scope Amendment'
    ],
    workflowHighlights: [
      'Digital change-order generator that requires client signature before adding hours',
      'Milestone-gated staging approval portals for clean client sign-offs',
      'Automated GST invoicing with embedded UPI QR codes and NEFT details',
      'Legally binding digital contracts compliant with the Indian IT Act 2000'
    ],
    recommendedModules: [
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'lead-crm', title: 'Client CRM', icon: 'Users2' },
      { id: 'gst-invoicing', title: '18% GST Invoicing', icon: 'Receipt' },
      { id: 'task-board', title: 'Project Boards', icon: 'Kanban' }
    ],
    challengeVsSolution: {
      challenge: 'Scope creep and delayed payments from informal client approvals on chat apps.',
      solution: 'Milestone approval portals with automated GST invoice generation upon client sign-off.'
    },
    sampleRetainerText: 'Software Retainer: 40 Dev Hours / Month at ₹1,20,000 + 18% GST. Includes bi-weekly staging reviews and deployment support.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_production_house.jpg',
    relatedIndustrySlugs: ['web-app-studios', 'it-tech-services', 'ai-automation', 'design-uiux']
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
    tagline: 'Figma sign-offs, revision controls & milestone deposit billing.',
    heroDescription: 'Purpose-built client management for UI/UX studios and web design agencies. Coordinate design sprint milestones, collect upfront deposits, manage revisions, and bill monthly maintenance retainers.',
    sacCode: 'SAC 998315',
    gstRate: '18% GST',
    stats: [
      { metric: '50% Upfront', label: 'Advance deposit locked on contract sign' },
      { metric: '0 Scope Creep', label: 'Clear revision limits in every agreement' },
      { metric: '1-Click', label: 'Figma milestone sign-offs' }
    ],
    theOldWay: [
      'Clients requesting endless design revisions without paying for additional scope',
      'Chasing overdue milestone payments through manual email threads',
      'Sharing Figma files before final payment is cleared'
    ],
    theCoraWay: [
      'Clear revision boundaries with one-click change-orders when clients request extra features',
      'Automated 50/25/25 milestone payment schedules tied directly to client approvals',
      'Secure client portals that deliver design assets only after final invoice clearance'
    ],
    capabilities: [
      {
        title: 'Milestone Deposit Engine',
        description: 'Auto-split project payments into 50% advance, 25% design sign-off, and 25% launch balance with instant GST invoicing.',
        tag: 'Milestone Deposits'
      },
      {
        title: 'Design Review & Revision Gates',
        description: 'Structured review portals where clients leave feedback within agreed revision rounds and formally approve deliverables.',
        tag: 'Revision Limits'
      },
      {
        title: 'Hosting & Maintenance Retainers',
        description: 'Automate monthly website hosting, security maintenance, and support retainers with recurring tax invoices.',
        tag: 'Maintenance Retainers'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Proposal & Scope', description: 'Assemble deliverables, revision limits, and payment terms in 3 minutes.' },
      { step: '02', title: 'E-Sign & Advance', description: 'Client signs digital contract and clears 50% advance deposit via UPI or bank transfer.' },
      { step: '03', title: 'Design Sign-Off', description: 'Client signs off on design milestone checklist before engineering begins.' },
      { step: '04', title: 'Launch & Retainer', description: 'Final balance invoice clears automatically and client transitions to monthly maintenance.' }
    ],
    toolsReplaced: [
      { name: 'HoneyBook', category: 'CRM & Proposals', monthlySavingsINR: 3200 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Zoho Invoice', category: 'GST Billing', monthlySavingsINR: 1900 }
    ],
    faqs: [
      {
        question: 'How does Cora prevent scope creep for web design projects?',
        answer: 'Cora specifies revision limits directly in the contract. When a client requests changes outside the original brief, you can generate an instant Change-Order Addendum with one click that the client must digitally sign before work begins.'
      },
      {
        question: 'Can we manage monthly website hosting and maintenance retainers in Cora?',
        answer: 'Yes. You can set up recurring monthly maintenance retainers with automated GST invoices sent to clients on the 1st of every month.'
      },
      {
        question: 'Can clients preview and approve design milestones on mobile?',
        answer: 'Yes. The client review portal is fully mobile-responsive, allowing clients to review checklists and sign milestone approvals on their phone.'
      }
    ],
    preSeededTemplates: [
      'Web & App Design-Build Master Agreement',
      'Design Milestone Client Sign-Off Form',
      'Post-Launch Hosting & Maintenance Retainer SLA',
      'Change-Order Request & Scope Amendment Contract'
    ],
    workflowHighlights: [
      '50% advance booking deposit invoice auto-generated upon contract e-sign',
      'Interactive design review portal with client approval checklist',
      'Automated change-order generator that quotes additional features instantly',
      'Integrated GST tax calculations for domestic and export services'
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
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/bento_crew_camera.jpg',
    relatedIndustrySlugs: ['software-agencies', 'design-uiux', 'marketing-seo', 'ai-automation']
  },

  {
    id: 'it-tech-services',
    slug: 'it-tech-services',
    title: 'IT & Tech Services',
    shortTitle: 'IT Services',
    sectorId: 'tech_dev',
    sectorLabel: 'Tech & Dev',
    sectorBadge: 'TECH & DEV',
    badgeColor: 'blue',
    iconName: 'Cpu',
    tagline: 'Support retainers, SLA guarantees & automated monthly billing.',
    heroDescription: 'Streamlined operations for MSPs, cloud consultancies, and IT support providers. Automate monthly service retainers, track response times, and generate compliant GST invoices effortlessly.',
    sacCode: 'SAC 998313',
    gstRate: '18% GST',
    stats: [
      { metric: '100% SLA', label: 'Guaranteed response time tracking' },
      { metric: 'Monthly Auto', label: 'Recurring retainer billing' },
      { metric: 'Zero Manual', label: 'Automated invoice generation' }
    ],
    theOldWay: [
      'Tracking monthly support hours across fragmented spreadsheets with disputed time logs',
      'Late invoice payments leading to cashflow gaps on ongoing managed infrastructure',
      'Unsigned annual maintenance contracts (AMCs) exposing the firm to uncontracted liability'
    ],
    theCoraWay: [
      'Automated monthly support hour tracking with clear client-facing balance reports',
      'Automated recurring GST billing on the 1st of every month with UPI and bank transfer links',
      'Pre-seeded Annual Maintenance Contracts (AMC) with digital signatures and SLA definitions'
    ],
    capabilities: [
      {
        title: 'Monthly AMC Retainer Billing',
        description: 'Set up recurring monthly or annual IT maintenance contracts with automatic GST invoice dispatch and payment tracking.',
        tag: 'AMC Retainers'
      },
      {
        title: 'SLA Response Tier Management',
        description: 'Define clear service levels (Standard, Priority, 24/7 Mission-Critical) with automated monthly retainer invoicing.',
        tag: 'SLA Tiers'
      },
      {
        title: 'Hardware & Cloud Procurement Invoicing',
        description: 'Separate service hours from hardware and cloud infrastructure pass-through costs with transparent line-item GST billing.',
        tag: 'Procurement Billing'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Select Service Tier', description: 'Choose from pre-configured IT support packages with defined response times.' },
      { step: '02', title: 'Sign Annual AMC', description: 'Client executes digital support agreement with SLA terms and renewal dates.' },
      { step: '03', title: 'Automated Monthly Billing', description: 'System issues 18% GST invoices on the 1st of every month automatically.' },
      { step: '04', title: 'Support Tracking', description: 'Client accesses portal to request assistance, view SLA metrics, and log tickets.' }
    ],
    toolsReplaced: [
      { name: 'ConnectWise', category: 'PSA & Invoicing', monthlySavingsINR: 5500 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'QuickBooks', category: 'Accounting', monthlySavingsINR: 2500 }
    ],
    faqs: [
      {
        question: 'Can Cora handle annual maintenance contracts (AMCs) with quarterly billing?',
        answer: 'Yes. You can configure annual AMC agreements that bill monthly, quarterly, or annually with automated GST invoices and payment reminders.'
      },
      {
        question: 'How are SLA breach liabilities handled in the contract templates?',
        answer: 'Cora pre-seeded AMC templates include industry-standard liability caps and response time exclusions, safeguarding your IT firm from open-ended financial liabilities.'
      },
      {
        question: 'Does Cora support export IT consultancy with LUT?',
        answer: 'Yes. For overseas tech clients, Cora automatically generates 0% GST export invoices with your registered GST LUT number.'
      }
    ],
    preSeededTemplates: [
      'Annual Maintenance Contract (AMC) for IT Infrastructure',
      'Cloud Managed Services Agreement & SLA Schedule',
      'Emergency Incident Response & Escalation Matrix',
      'Hardware & Software Procurement Supply Agreement'
    ],
    workflowHighlights: [
      'Automated recurring monthly IT retainer billing with instant UPI links',
      'Pre-seeded AMC legal contracts with clear SLA performance definitions',
      'Separate service labor and hardware procurement line-item invoicing',
      'Client portal for reviewing active contracts and invoice payment history'
    ],
    recommendedModules: [
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'gst-invoicing', title: 'GST Invoicing', icon: 'Receipt' },
      { id: 'lead-crm', title: 'Account CRM', icon: 'Users2' },
      { id: 'analytics-pwa', title: 'Performance Stats', icon: 'BarChart2' }
    ],
    challengeVsSolution: {
      challenge: 'Managing recurring AMC contracts and collecting monthly fees on time.',
      solution: 'Automated recurring billing cycles with instant digital contracts and payment notifications.'
    },
    sampleRetainerText: 'Managed Cloud Infrastructure: ₹65,000/mo + 18% GST. Includes 24/7 uptime monitoring, security patching, and 15 support hours.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_realestate_agency.jpg',
    relatedIndustrySlugs: ['software-agencies', 'ai-automation', 'consultants-advisors', 'audit-compliance']
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
    iconName: 'Bot',
    tagline: 'AI workflow retainers, client knowledge bases & privacy protection.',
    heroDescription: 'The complete platform for agencies building AI assistants, automations, and intelligent workflows. Manage client knowledge documents, monthly support retainers, and strict enterprise privacy guarantees.',
    sacCode: 'SAC 998316',
    gstRate: '18% GST',
    stats: [
      { metric: 'Smart Hub', label: 'Secure client knowledge bases' },
      { metric: 'Monthly Retainers', label: 'Predictable recurring billing' },
      { metric: '100% Private', label: 'Client data privacy guarantee' }
    ],
    theOldWay: [
      'Client worries about proprietary business data being used for public AI training',
      'Manually invoicing variable monthly maintenance and API usage fees',
      'Scattered workflow documentation and lost API configuration details'
    ],
    theCoraWay: [
      'Built-in data privacy guarantee agreements assuring complete confidentiality',
      'Combined monthly support retainers with automated GST invoices',
      'Encrypted client workspaces storing all workflow logic and documentation safely'
    ],
    capabilities: [
      {
        title: 'Client Data Privacy Guarantees',
        description: 'Pre-seeded non-training enterprise data agreements assuring enterprise clients that their private data is never used to train public models.',
        tag: 'Data Privacy'
      },
      {
        title: 'Secure Client Knowledge Base',
        description: 'Organize client documents, prompts, and automation rules in dedicated, encrypted workspaces.',
        tag: 'Knowledge Base'
      },
      {
        title: 'Monthly Automation Retainers',
        description: 'Combine fixed monthly maintenance with automated usage-tier invoicing and GST billing.',
        tag: 'AI Retainers'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Workflow Scoping', description: 'Define the automation brief, required integrations, and deliverables.' },
      { step: '02', title: 'Privacy & Contract Sign', description: 'Client signs the master service agreement and enterprise privacy addendum.' },
      { step: '03', title: 'Knowledge Base Setup', description: 'Upload client documents and connect automation endpoints inside their secure workspace.' },
      { step: '04', title: 'Live Deployment & Retainer', description: 'Automation goes live with automated monthly maintenance and support billing.' }
    ],
    toolsReplaced: [
      { name: 'PandaDoc', category: 'Proposals', monthlySavingsINR: 3800 },
      { name: 'Notion Teams', category: 'Knowledge Base', monthlySavingsINR: 2400 },
      { name: 'Zapier Pro', category: 'Workflows', monthlySavingsINR: 4900 }
    ],
    faqs: [
      {
        question: 'Who owns the intellectual property of the automations built on Cora?',
        answer: 'Our pre-seeded agreements ensure complete intellectual property transfer to the client upon final invoice clearance, protecting both parties.'
      },
      {
        question: 'Does Cora integrate with modern AI models like Claude, Gemini, and GPT-4o?',
        answer: 'Yes. Cora features multi-model orchestration, allowing your team to draft proposals, extract meeting scopes, and organize client inquiries with top AI models.'
      },
      {
        question: 'How do we handle monthly maintenance and fine-tuning retainers?',
        answer: 'You can establish recurring monthly retainers that automatically generate GST invoices on the 1st of every month with integrated payment links.'
      }
    ],
    preSeededTemplates: [
      'AI Automation & Agent Development Agreement',
      'Enterprise Data Privacy & Non-Training Addendum',
      'Monthly Automation Maintenance & Support Retainer',
      'Workflow IP & Prompt Architecture Handover Agreement'
    ],
    workflowHighlights: [
      'Custom AI automation scopes with phased deployment milestones',
      'Monthly support and maintenance retainer automation with GST invoices',
      'Dedicated knowledge base workspace for confidential client document storage',
      'Automated client onboarding wizard for configuring integrations safely'
    ],
    recommendedModules: [
      { id: 'ai-cofounder', title: 'AI Assistant', icon: 'Bot' },
      { id: 'content-ai', title: 'Content AI', icon: 'Sparkles' },
      { id: 'docs-portal', title: 'Workflow Hub', icon: 'Terminal' },
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' }
    ],
    challengeVsSolution: {
      challenge: 'Client concerns regarding enterprise data privacy and model training.',
      solution: 'Pre-seeded enterprise data privacy addendums with complete workspace data isolation.'
    },
    sampleRetainerText: 'Enterprise Automation Retainer: ₹1,80,000/mo + 18% GST. Includes 5 custom automated workflows, monthly maintenance, and optimization.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
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
    tagline: 'Legally binding e-signatures, retainer mandates & confidential vaults.',
    heroDescription: 'Cloud practice management for advocates, legal consultants, and corporate law chambers. Manage client intake questionnaires, execute digital agreements under the IT Act 2000, and track billable hours.',
    sacCode: 'SAC 998211',
    gstRate: '18% GST / Reverse Charge (RCM)',
    stats: [
      { metric: 'IT Act Valid', label: '100% legally enforceable digital signatures' },
      { metric: 'Encrypted', label: 'Confidential client document vaults' },
      { metric: 'Instant', label: 'Retainer mandate agreements' }
    ],
    theOldWay: [
      'Printing, signing, and courier-scanning paper retainer mandates taking weeks',
      'Confidential case briefs and agreements shared insecurely over email attachments',
      'Manual calculation of Reverse Charge Mechanism (RCM) tax on legal invoices'
    ],
    theCoraWay: [
      'Instant digital contract execution with tamper-proof audit trails valid in Indian courts',
      'Dedicated encrypted client vaults with strict access permissions for case files',
      'Automated legal fee invoicing with built-in RCM reverse-charge tax indicators'
    ],
    capabilities: [
      {
        title: 'IT Act 2000 E-Signatures',
        description: 'Execute legal retainer mandates, NDAs, and settlement terms with tamper-evident digital signatures valid under Section 10A.',
        tag: 'Legal E-Sign'
      },
      {
        title: 'Confidential Case Vaults',
        description: 'Bank-grade encrypted storage for case briefs, evidence files, and court filings with role-based access control.',
        tag: 'Case Vault'
      },
      {
        title: 'Legal Retainer & RCM Invoicing',
        description: 'Automated legal fee billing supporting standard 18% GST and business-to-business Reverse Charge Mechanism (RCM).',
        tag: 'Legal Invoicing'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Digital Client Intake', description: 'Client completes secure intake questionnaire with conflict-of-interest check.' },
      { step: '02', title: 'Sign Retainer Mandate', description: 'Both parties sign the legal representation agreement with digital verification.' },
      { step: '03', title: 'Secure File Exchange', description: 'Client uploads confidential evidence into their private encrypted workspace.' },
      { step: '04', title: 'Compliant Fee Invoicing', description: 'System issues compliant legal fee invoice with RCM tax classification.' }
    ],
    toolsReplaced: [
      { name: 'DocuSign Enterprise', category: 'E-Signatures', monthlySavingsINR: 4800 },
      { name: 'Clio Manage', category: 'Legal CRM', monthlySavingsINR: 6500 },
      { name: 'Box Enterprise', category: 'Encrypted Storage', monthlySavingsINR: 3200 }
    ],
    faqs: [
      {
        question: 'Are digital signatures in Cora admissible in Indian courts?',
        answer: 'Yes. Digital signatures on Cora meet the requirements of Section 10A and Section 65B of the Indian Evidence Act, with complete IP, timestamp, and audit trail logs.'
      },
      {
        question: 'How does Cora handle GST Reverse Charge Mechanism (RCM) on legal services?',
        answer: 'Under GST rules, legal services provided to business entities fall under RCM. Cora invoices automatically display the mandatory RCM flag so your corporate clients can self-remit GST.'
      },
      {
        question: 'Can junior associates be restricted from seeing sensitive partner billing data?',
        answer: 'Yes. Cora provides role-based access control, allowing associates to work on case files without seeing overall firm billing or partner fee agreements.'
      }
    ],
    preSeededTemplates: [
      'Legal Representation & Retainer Mandate Agreement',
      'Mutual Non-Disclosure Agreement (Indian Jurisdiction)',
      'Employment Contract & IP Assignment Deed',
      'Commercial Settlement & Release Protocol'
    ],
    workflowHighlights: [
      '100% IT Act 2000 compliant digital contract execution in under 2 minutes',
      'Automated RCM tax classification on legal professional fee invoices',
      'Encrypted document repository for litigation evidence and corporate contracts',
      'Digital client intake forms with built-in conflict-of-interest checks'
    ],
    recommendedModules: [
      { id: 'esign-vault', title: 'Legal E-Sign', icon: 'ShieldCheck' },
      { id: 'lead-crm', title: 'Client Registry', icon: 'Users2' },
      { id: 'gst-invoicing', title: 'Legal Invoicing', icon: 'Receipt' },
      { id: 'rbac-system', title: 'Data Isolation', icon: 'Lock' }
    ],
    challengeVsSolution: {
      challenge: 'Slow physical signing of legal retainer agreements delaying the start of legal representation.',
      solution: 'Legally enforceable digital contracts that clients can review and sign on their phone in 60 seconds.'
    },
    sampleRetainerText: 'Corporate Legal Advisory: ₹1,50,000/mo under Reverse Charge Mechanism (RCM). Includes 15 hours of contract review and legal counsel.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/bento_crew_camera.jpg',
    relatedIndustrySlugs: ['tax-ca-firms', 'financial-advisors', 'audit-compliance', 'consultants-advisors']
  },

  {
    id: 'tax-ca-firms',
    slug: 'tax-ca-firms',
    title: 'Tax & CA Firms',
    shortTitle: 'Tax & CA Firms',
    sectorId: 'legal_finance',
    sectorLabel: 'Legal & Finance',
    sectorBadge: 'LEGAL & FINANCE',
    badgeColor: 'slate',
    iconName: 'Receipt',
    tagline: 'Tax audit retainers, statutory checklists & instant UPI payments.',
    heroDescription: 'The modern operating system for chartered accountants and tax advisory practices. Streamline client document collection, audit milestone checklists, and automated 18% GST billing with UPI QR codes.',
    sacCode: 'SAC 998222',
    gstRate: '18% GST',
    stats: [
      { metric: '18% GST', label: 'Automated statutory tax splitting' },
      { metric: '1-Click UPI', label: 'Instant QR payment collection' },
      { metric: 'Zero Paper', label: 'Digital audit document intake' }
    ],
    theOldWay: [
      'Chasing business clients for bank statements and GST receipts over endless WhatsApp messages',
      'Manually drafting audit engagement letters for hundreds of annual tax clients',
      'Delayed CA professional fee payments with manual bank reconciliation'
    ],
    theCoraWay: [
      'Digital client intake portals where businesses securely upload financial statements and tax documents',
      'Pre-seeded audit engagement letters ready to sign digitally in one click',
      'Automated professional fee invoicing with instant UPI QR codes and automated reminders'
    ],
    capabilities: [
      {
        title: 'Audit Engagement Mandates',
        description: 'Standardized statutory audit and tax audit engagement letters with digital signatures.',
        tag: 'Audit Engagement'
      },
      {
        title: 'Client Tax Document Vault',
        description: 'Structured checklists for GST returns, TDS certificates, and balance sheet schedules.',
        tag: 'Tax Document Vault'
      },
      {
        title: 'CA Retainer & UPI Billing',
        description: 'Automate monthly and quarterly accounting retainers with dynamic UPI QR payment links.',
        tag: 'UPI Retainers'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Intake Checklist', description: 'Send automated document request checklist for GST, TDS, and financial records.' },
      { step: '02', title: 'Sign Engagement Letter', description: 'Client signs statutory audit or tax filing mandate digitally.' },
      { step: '03', title: 'Secure Document Intake', description: 'Client uploads trial balance and bank statements into their encrypted portal.' },
      { step: '04', title: 'UPI Fee Settlement', description: 'Automated GST invoice is generated with embedded UPI QR code for instant payment.' }
    ],
    toolsReplaced: [
      { name: 'QuickBooks CA', category: 'Accounting', monthlySavingsINR: 3500 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Google Workspace Storage', category: 'File Storage', monthlySavingsINR: 1500 }
    ],
    faqs: [
      {
        question: 'Are the CA engagement letters aligned with ICAI professional standards?',
        answer: 'Yes. Our pre-seeded audit engagement templates incorporate standard terms and statutory declarations recommended for Indian accounting practices.'
      },
      {
        question: 'Can clients pay CA professional fees directly via UPI QR on the invoice?',
        answer: 'Yes. Cora automatically embeds dynamic UPI QR codes and NEFT bank details directly into every invoice for instant phone settlement.'
      },
      {
        question: 'How is client financial data kept secure and confidential?',
        answer: 'Every client has a dedicated, encrypted workspace with strict access controls, ensuring complete data segregation across all your firm clients.'
      }
    ],
    preSeededTemplates: [
      'Statutory Audit Engagement Letter',
      'Tax Audit & GST Compliance Retainer Agreement',
      'Monthly Bookkeeping & Accounting Services SLA',
      'Transfer Pricing & International Tax Advisory Mandate'
    ],
    workflowHighlights: [
      'Automated monthly and quarterly accounting retainer invoicing with instant UPI QR codes',
      'Pre-seeded audit engagement letters ready for immediate digital signature',
      'Encrypted document checklist portal for collecting bank statements and vouchers',
      'Automatic CGST and SGST tax splitting on all professional fee invoices'
    ],
    recommendedModules: [
      { id: 'gst-invoicing', title: 'CA Invoicing', icon: 'Receipt' },
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'form-builder', title: 'Document Intake', icon: 'FormInput' },
      { id: 'lead-crm', title: 'Client CRM', icon: 'Users2' }
    ],
    challengeVsSolution: {
      challenge: 'Chasing clients for tax documents and waiting weeks for professional fee payments.',
      solution: 'Automated document request checklists combined with instant UPI QR invoice settlement.'
    },
    sampleRetainerText: 'Tax & GST Retainer: ₹25,000/mo + 18% GST (CGST ₹2,250 + SGST ₹2,250). Includes monthly GSTR-1, GSTR-3B filings, and TDS reconciliation.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_solo_creator.jpg',
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
    iconName: 'BarChart2',
    tagline: 'Client risk profiles, advisory retainers & encrypted vaults.',
    heroDescription: 'Enterprise client portal for registered investment advisors, wealth managers, and financial planners. Automate digital risk profiling, quarterly advisory billing, and secure financial statement vaults.',
    sacCode: 'SAC 997159',
    gstRate: '18% GST',
    stats: [
      { metric: 'Digital Intake', label: 'Automated risk profile scoring' },
      { metric: 'Quarterly Auto', label: 'Advisory fee retainer billing' },
      { metric: 'Bank-Grade', label: 'Encrypted document storage' }
    ],
    theOldWay: [
      'Collecting confidential net-worth statements over insecure email threads',
      'Manually calculating quarterly advisory fees on spreadsheets and sending separate bills',
      'Tracking client risk profiles on paper forms with missing compliance timestamps'
    ],
    theCoraWay: [
      'Bank-grade encrypted client portals for uploading net-worth and portfolio statements',
      'Automated quarterly advisory fee invoicing with clear GST breakdowns',
      'Digital risk-profiling intake questionnaires with automated scoring and audit timestamps'
    ],
    capabilities: [
      {
        title: 'Digital Risk Profile Intake',
        description: 'Interactive questionnaires that calculate client risk tolerance scores and generate investment mandates.',
        tag: 'Risk Profiling'
      },
      {
        title: 'Advisory Retainer Invoicing',
        description: 'Automate fixed quarterly retainers or asset-based advisory billing with compliant GST invoices.',
        tag: 'Advisory Billing'
      },
      {
        title: 'Encrypted Wealth Vault',
        description: 'Bank-grade encrypted storage for client tax returns, net-worth statements, and portfolio reviews.',
        tag: 'Wealth Vault'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Risk Assessment', description: 'Client completes interactive digital risk profiling questionnaire on mobile.' },
      { step: '02', title: 'Sign Advisory Mandate', description: 'Client executes investment advisory agreement with digital verification.' },
      { step: '03', title: 'Vault Upload', description: 'Client uploads portfolio statements into their private encrypted workspace.' },
      { step: '04', title: 'Quarterly Invoicing', description: 'System generates quarterly advisory retainer invoice with instant payment links.' }
    ],
    toolsReplaced: [
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Wealthbox', category: 'Advisory CRM', monthlySavingsINR: 4900 },
      { name: 'Egnyte', category: 'Secure Storage', monthlySavingsINR: 3800 }
    ],
    faqs: [
      {
        question: 'Can Cora handle quarterly advisory retainers?',
        answer: 'Yes. You can configure fixed retainer schedules (e.g. ₹50,000/quarter) or custom advisory fee calculations with automated GST breakdowns.'
      },
      {
        question: 'How is confidential client financial data secured in Cora?',
        answer: 'Every workspace features complete multi-tenant database isolation, AES-256 storage encryption, and role-based permissions preventing unauthorized access.'
      },
      {
        question: 'Can we customize the risk assessment questions for our practice?',
        answer: 'Yes. You can customize the intake questions, scoring logic, and risk categories using the built-in form builder.'
      }
    ],
    preSeededTemplates: [
      'Investment Advisory Agreement',
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
      { id: 'lead-crm', title: 'Wealth CRM', icon: 'Users2' },
      { id: 'gst-invoicing', title: 'Advisory Invoicing', icon: 'Receipt' },
      { id: 'rbac-system', title: 'Data Isolation', icon: 'Lock' }
    ],
    challengeVsSolution: {
      challenge: 'Maintaining compliant risk-profiling records and collecting recurring advisory fees on time.',
      solution: 'Digital risk assessment intake forms paired with automated quarterly advisory retainer billing.'
    },
    sampleRetainerText: 'Wealth Advisory Fee: Quarterly Retainer of ₹75,000/qtr + 18% GST. Includes quarterly portfolio reviews and rebalancing advisory.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
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
    tagline: 'Compliance checklists, verified audit logs & evidence vaults.',
    heroDescription: 'Comprehensive compliance tracking for internal auditors, ISO consultants, and secretarial audit practices. Manage multi-branch inspection checklists, timestamped evidence logs, and sign-off approvals.',
    sacCode: 'SAC 998223',
    gstRate: '18% GST',
    stats: [
      { metric: '30-Point', label: 'Standardized governance checklist' },
      { metric: 'Timestamped', label: 'Tamper-proof evidence logs' },
      { metric: 'Multi-Entity', label: 'Separate branch workspaces' }
    ],
    theOldWay: [
      'Scattered audit evidence across disconnected email chains and personal drives',
      'Unsigned audit findings with disputes over inspection completion dates',
      'Manual compilation of governance reports taking dozens of hours per client'
    ],
    theCoraWay: [
      'Centralized evidence vault with timestamped logs for all uploaded documents',
      'Digital sign-offs on audit findings and executive management representations',
      'Standardized compliance checklists with one-click report generation'
    ],
    capabilities: [
      {
        title: '30-Point Compliance Checklist',
        description: 'Standardized inspection matrix for statutory governance, ESG disclosures, and ISO certifications.',
        tag: 'Audit Matrix'
      },
      {
        title: 'Timestamped Evidence Vault',
        description: 'Secure digital repository for inspection photos, ledger samples, and management representations.',
        tag: 'Evidence Vault'
      },
      {
        title: 'Board Sign-Offs & Mandates',
        description: 'Execute audit engagement letters and final findings reports with legally binding digital signatures.',
        tag: 'Board Sign-Off'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Audit Scope Setup', description: 'Select the compliance standard (ISO, ESG, Secretarial) and assign branch tasks.' },
      { step: '02', title: 'Evidence Collection', description: 'Branch teams upload required documentation into structured category folders.' },
      { step: '03', title: 'Inspector Review', description: 'Lead auditor evaluates evidence and marks compliance status on each checkpoint.' },
      { step: '04', title: 'Final Report Sign-Off', description: 'Executive summary generated automatically and signed digitally by stakeholders.' }
    ],
    toolsReplaced: [
      { name: 'AuditBoard', category: 'Compliance OS', monthlySavingsINR: 8500 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Smartsheet', category: 'Tracking', monthlySavingsINR: 2800 }
    ],
    faqs: [
      {
        question: 'Can we run multi-branch audits with separate permissions?',
        answer: 'Yes. Cora allows you to create isolated branch workspaces while giving lead auditors a central master dashboard to monitor overall progress.'
      },
      {
        question: 'How are evidence files timestamped and verified?',
        answer: 'Every uploaded file receives an immutable timestamp, user identity tag, and verification record, providing complete audit integrity.'
      },
      {
        question: 'Can we export the final compliance audit report to PDF?',
        answer: 'Yes. You can generate a branded executive summary report complete with findings, completion percentages, and digital signatures.'
      }
    ],
    preSeededTemplates: [
      'Secretarial & Statutory Audit Engagement Mandate',
      'ISO 27001 / ISO 9001 Compliance Inspection Checklist',
      'Internal Financial Controls (IFC) Testing Protocol',
      'Board Management Representation Letter'
    ],
    workflowHighlights: [
      'Pre-seeded 30-point governance and statutory compliance inspection checklists',
      'Timestamped evidence vault for storing audit workpapers and documentation',
      'Digital sign-off on board representation letters and final audit reports',
      'Multi-branch workspace isolation for enterprise corporate audits'
    ],
    recommendedModules: [
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'task-board', title: 'Audit Kanban', icon: 'Kanban' },
      { id: 'form-builder', title: 'Checklists', icon: 'FormInput' },
      { id: 'rbac-system', title: 'Data Isolation', icon: 'Lock' }
    ],
    challengeVsSolution: {
      challenge: 'Disorganized audit evidence across spreadsheets leading to compliance delays.',
      solution: 'Structured digital checklist matrix with timestamped file uploads and formal sign-offs.'
    },
    sampleRetainerText: 'Annual Compliance Audit: ₹3,50,000 + 18% GST. Phased across 4 quarterly reviews with executive board reporting.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_realestate_agency.jpg',
    relatedIndustrySlugs: ['tax-ca-firms', 'lawyers-law-firms', 'financial-advisors', 'consultants-advisors']
  },

  // ── 3. MARKETING & CREATIVE SECTOR ──
  {
    id: 'marketing-seo',
    slug: 'marketing-seo',
    title: 'Marketing & SEO Agencies',
    shortTitle: 'Marketing & SEO',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Design',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'Sparkles',
    tagline: 'Monthly retainer billing, campaign roadmaps & client portals.',
    heroDescription: 'The growth engine for performance marketing, SEO, and social media agencies. Manage monthly client retainers, deliverable sign-offs, performance report portals, and instant UPI payment links.',
    sacCode: 'SAC 998361',
    gstRate: '18% GST',
    stats: [
      { metric: 'Monthly Auto', label: 'Recurring retainer invoices' },
      { metric: '5-Min Setup', label: 'Client onboarding portals' },
      { metric: '18% GST', label: 'Automated agency billing' }
    ],
    theOldWay: [
      'Clients disputing monthly marketing fees due to lack of transparent deliverable sign-offs',
      'Manually chasing monthly retainer payments over WhatsApp and email every month',
      'Scattered campaign briefs and lost creative assets across disconnected folders'
    ],
    theCoraWay: [
      'Dedicated client portal showing real-time monthly deliverables, reports, and approvals',
      'Automated recurring retainer billing on the 1st of every month with instant UPI links',
      'Centralized creative asset library for campaign copies, banners, and monthly reports'
    ],
    capabilities: [
      {
        title: 'Monthly Retainer Automation',
        description: 'Set up recurring monthly agency retainers with automated GST invoice generation and payment tracking.',
        tag: 'Monthly Retainers'
      },
      {
        title: 'Client Reporting & Deliverables Portal',
        description: 'Share monthly SEO rankings, ad spend reports, and creative assets in a clean, white-labeled client portal.',
        tag: 'Client Portals'
      },
      {
        title: 'Performance & Ad-Spend Invoicing',
        description: 'Easily separate agency management retainers from direct ad-spend budgets with clean itemized billing.',
        tag: 'Agency Invoicing'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Scope & Retainer Agreement', description: 'Assemble monthly deliverables, ad-spend limits, and retainer fees.' },
      { step: '02', title: 'Sign Digital Contract', description: 'Client executes master retainer agreement with digital signature.' },
      { step: '03', title: 'Deliverable Sign-Off', description: 'Client reviews and approves monthly campaign roadmaps and creative assets.' },
      { step: '04', title: 'Automated Retainer Billing', description: 'System issues recurring 18% GST invoice with instant UPI QR payment link.' }
    ],
    toolsReplaced: [
      { name: 'HoneyBook', category: 'Proposals & CRM', monthlySavingsINR: 3200 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Zoho Invoice', category: 'GST Billing', monthlySavingsINR: 1900 }
    ],
    faqs: [
      {
        question: 'How do monthly marketing retainers work in Cora?',
        answer: 'You configure the monthly retainer amount and billing date. Cora automatically issues an 18% GST invoice with UPI and bank links on the 1st of every month.'
      },
      {
        question: 'Can we separate agency management fees from client ad spend?',
        answer: 'Yes. Cora lets you invoice agency professional fees and client ad budgets as distinct line items with correct tax classifications.'
      },
      {
        question: 'Can clients approve monthly content calendars and ad copy in Cora?',
        answer: 'Yes. Clients can log in to their dedicated review portal to inspect monthly content schedules, leave feedback, and approve copy.'
      }
    ],
    preSeededTemplates: [
      'Performance Marketing & Media Buying Master Contract',
      'Monthly SEO & Content Marketing Retainer Agreement',
      'Social Media Management & Creator Retainer SOW',
      'Agency Ad-Spend Management & Billing Authorization'
    ],
    workflowHighlights: [
      'Automated recurring monthly retainer billing with embedded UPI QR codes',
      'White-label client portal for deliverable tracking and monthly reports',
      'Pre-seeded agency master agreements with clear performance boundaries',
      'Instant change-orders for out-of-scope campaign requests'
    ],
    recommendedModules: [
      { id: 'lead-crm', title: 'Agency CRM', icon: 'Users2' },
      { id: 'gst-invoicing', title: '18% GST Invoicing', icon: 'Receipt' },
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'task-board', title: 'Campaign Boards', icon: 'Kanban' }
    ],
    challengeVsSolution: {
      challenge: 'Late payments on monthly retainers and scope creep on extra campaign requests.',
      solution: 'Automated recurring billing cycles paired with digital change-orders for extra deliverables.'
    },
    sampleRetainerText: 'Growth Marketing Retainer: ₹1,20,000/mo + 18% GST. Includes Google & Meta ad management, weekly optimizations, and monthly reporting.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_solo_creator.jpg',
    relatedIndustrySlugs: ['design-uiux', 'photo-video-studios', 'software-agencies', 'consultants-advisors']
  },

  {
    id: 'design-uiux',
    slug: 'design-uiux',
    title: 'Design & UI/UX Studios',
    shortTitle: 'Design & UI/UX',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Design',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'LayoutTemplate',
    tagline: 'Design sprint milestones, copyright transfer & asset delivery.',
    heroDescription: 'Complete operating system for brand identity and product design studios. Run structured design sprints, collect upfront booking deposits, and transfer copyright ownership automatically on final payment.',
    sacCode: 'SAC 998391',
    gstRate: '18% GST',
    stats: [
      { metric: '50% Upfront', label: 'Advance deposit locked on contract sign' },
      { metric: 'IP Transfer', label: 'Automated copyright handover' },
      { metric: 'Figma Ready', label: 'Milestone review portals' }
    ],
    theOldWay: [
      'Sending final design source files before the client has cleared the final invoice',
      'Unclear revision boundaries leading to endless subjective design tweaks',
      'Complicated copyright assignment agreements requiring expensive lawyer drafts'
    ],
    theCoraWay: [
      'Asset delivery vaults that unlock source files only after final GST invoice clearance',
      'Clear revision round limits built directly into the signed digital contract',
      'Automated copyright and intellectual property assignment executed upon final payment'
    ],
    capabilities: [
      {
        title: 'Design Sprint Milestone Staging',
        description: 'Split design projects into clear milestones (Wireframes, High-Fidelity UI, Design System) with client sign-offs.',
        tag: 'Design Sprints'
      },
      {
        title: 'Automated IP & Copyright Transfer',
        description: 'Legally assign copyright to the client automatically when the final balance invoice is settled.',
        tag: 'IP Transfer'
      },
      {
        title: 'Asset Delivery Vault',
        description: 'Securely share Figma links, vector logos, and design tokens with automatic access gating.',
        tag: 'Asset Vault'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Proposal & Scope', description: 'Assemble design deliverables, revision limits, and sprint timelines in 3 minutes.' },
      { step: '02', title: 'E-Sign & Advance', description: 'Client signs digital contract and clears 50% advance deposit via UPI or bank link.' },
      { step: '03', title: 'Milestone Approval', description: 'Client reviews design deliverables and formally signs off on completion.' },
      { step: '04', title: 'Payment & IP Handover', description: 'Final invoice clears and copyright assignment document is generated automatically.' }
    ],
    toolsReplaced: [
      { name: 'HoneyBook', category: 'Proposals & CRM', monthlySavingsINR: 3200 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Zoho Books', category: 'Invoicing', monthlySavingsINR: 2200 }
    ],
    faqs: [
      {
        question: 'When is copyright ownership transferred to the client?',
        answer: 'Our contracts specify that copyright and IP transfer occurs only upon 100% settlement of the final project invoice, protecting your creative work.'
      },
      {
        question: 'How do revision limits protect our design studio?',
        answer: 'Every contract includes defined revision rounds. If a client requests modifications beyond that limit, you can generate an instant Change-Order with one click.'
      },
      {
        question: 'Can we bill monthly design-on-demand retainers?',
        answer: 'Yes. You can configure ongoing monthly design retainers that bill automatically on the 1st of every month.'
      }
    ],
    preSeededTemplates: [
      'Brand Identity & UI/UX Design Master Contract',
      'Design Sprint Milestone Approval & Sign-Off Form',
      'Copyright & Intellectual Property Assignment Agreement',
      'Monthly Design-on-Demand Retainer Agreement'
    ],
    workflowHighlights: [
      'Design sprint milestone gates with mandatory client sign-off before final export',
      'Asset delivery portal for securely sharing Figma files, vector logos, and design tokens',
      'Automated copyright transfer documentation executed upon final invoice clearance',
      'Integrated GST tax calculations for specialized design services'
    ],
    recommendedModules: [
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'task-board', title: 'Sprint Board', icon: 'Kanban' },
      { id: 'gst-invoicing', title: '18% GST Invoicing', icon: 'Receipt' },
      { id: 'lead-crm', title: 'Client CRM', icon: 'Users2' }
    ],
    challengeVsSolution: {
      challenge: 'Delivering design source files before the client has cleared the final balance payment.',
      solution: 'Automated asset delivery vaults that unlock download permissions only when final GST invoices are paid.'
    },
    sampleRetainerText: 'Product Design Sprint: 2-Week Sprint at ₹2,00,000 + 18% GST. Includes wireframes, high-fidelity Figma components, and clickable prototype.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_solo_creator.jpg',
    relatedIndustrySlugs: ['web-app-studios', 'marketing-seo', 'photo-video-studios', 'architecture-interiors']
  },

  {
    id: 'photo-video-studios',
    slug: 'photo-video-studios',
    title: 'Photo & Video Studios',
    shortTitle: 'Photo & Video Studios',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Design',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'Camera',
    tagline: 'Shoot scheduling, 4K media proofing & crew call sheets.',
    heroDescription: 'All-in-one studio management for commercial photography, film production houses, and content creators. Coordinate multi-day shoot bookings, crew schedules, and secure client proofing galleries.',
    sacCode: 'SAC 998381',
    gstRate: '18% GST',
    stats: [
      { metric: '50% Advance', label: 'Deposit collected before shoot dates' },
      { metric: '4K Proofing', label: 'Private client review galleries' },
      { metric: 'Zero Clashes', label: 'Team and equipment scheduling' }
    ],
    theOldWay: [
      'Clients canceling shoots last-minute without signed non-refundable deposit contracts',
      'Sharing unwatermarked high-res photos and waiting months for final client payment',
      'Crew scheduling clashes and unreturned camera gear from location shoots'
    ],
    theCoraWay: [
      'Mandatory 50% advance booking deposit invoice generated automatically on contract sign',
      'Private 4K proofing galleries with watermarking that unlock high-res exports on final payment',
      'Centralized studio calendar for managing crew call sheets, shoot dates, and equipment'
    ],
    capabilities: [
      {
        title: 'Shoot Booking & Advance Deposit',
        description: 'Lock in shoot dates with digital contract e-signatures and instant 50% advance deposit invoicing.',
        tag: 'Shoot Booking'
      },
      {
        title: 'Private 4K Proofing Galleries',
        description: 'Client review galleries where stakeholders select favorites, leave comments, and approve edits.',
        tag: 'Media Proofing'
      },
      {
        title: 'Crew & Gear Call Sheets',
        description: 'Generate digital call sheets with location details, crew schedules, and equipment checkout lists.',
        tag: 'Call Sheets'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Production Scope', description: 'Assemble shoot dates, deliverables, crew requirements, and licensing terms.' },
      { step: '02', title: 'Sign & Deposit', description: 'Client signs digital production contract and clears 50% advance deposit.' },
      { step: '03', title: 'Shoot & Proofing', description: 'Upload watermarked previews to client proofing portal for selection and review.' },
      { step: '04', title: 'Balance & High-Res', description: 'Final invoice clears and full-resolution unwatermarked media is unlocked.' }
    ],
    toolsReplaced: [
      { name: 'Pixieset', category: 'Client Galleries', monthlySavingsINR: 2800 },
      { name: 'HoneyBook', category: 'Contracts & Invoicing', monthlySavingsINR: 3200 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 }
    ],
    faqs: [
      {
        question: 'How do advance booking deposits protect our studio from cancellations?',
        answer: 'Our pre-seeded production contracts clearly define non-refundable deposit terms and cancellation windows, ensuring your studio is paid for reserved shoot dates.'
      },
      {
        question: 'Can we include commercial image usage licensing terms in the agreement?',
        answer: 'Yes. Every template includes commercial usage rights clauses defining whether licenses are editorial, digital, or exclusive broadcast.'
      },
      {
        question: 'Can clients select and favorite raw photos from the gallery?',
        answer: 'Yes. Clients can review proofing galleries, select their favorite shots, and submit their final edit list with one click.'
      }
    ],
    preSeededTemplates: [
      'Commercial Photography & Video Production Master Contract',
      'Shoot Cancellation & Non-Refundable Deposit Protocol',
      'Model & Location Release Agreement (Indian Jurisdiction)',
      'Media Licensing & Commercial Usage Rights Agreement'
    ],
    workflowHighlights: [
      '50% advance booking deposit invoice auto-generated upon contract signature',
      'Private 4K client proofing gallery with favorite selection and review tools',
      'Automated high-res asset unlock only after final invoice settlement',
      'Pre-seeded commercial usage licensing and model release legal agreements'
    ],
    recommendedModules: [
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'lead-crm', title: 'Studio CRM', icon: 'Users2' },
      { id: 'gst-invoicing', title: '18% GST Invoicing', icon: 'Receipt' },
      { id: 'task-board', title: 'Shoot Schedule', icon: 'Kanban' }
    ],
    challengeVsSolution: {
      challenge: 'Last-minute shoot cancellations and unpaid high-resolution photo deliveries.',
      solution: 'Automated 50% non-refundable advance deposits and gated high-res downloads upon final payment.'
    },
    sampleRetainerText: 'Commercial Brand Shoot: 2-Day Production at ₹2,50,000 + 18% GST. Includes 4K video reel, 50 retouched photos, and commercial digital license.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/bento_crew_camera.jpg',
    relatedIndustrySlugs: ['design-uiux', 'marketing-seo', 'real-estate-property', 'web-app-studios']
  },

  {
    id: 'architecture-interiors',
    slug: 'architecture-interiors',
    title: 'Architecture & Interiors',
    shortTitle: 'Architecture & Interiors',
    sectorId: 'marketing_creative',
    sectorLabel: 'Marketing & Design',
    sectorBadge: 'MARKETING & DESIGN',
    badgeColor: 'purple',
    iconName: 'Building2',
    tagline: 'Phased stage billing, 3D blueprint vaults & site approvals.',
    heroDescription: 'Project management for architectural firms and interior design studios. Manage phased milestone billing (Concept, 3D Renders, Working Drawings), change-orders, and on-site sign-offs.',
    sacCode: 'SAC 998321',
    gstRate: '18% GST',
    stats: [
      { metric: '4-Stage', label: 'Milestone payment schedule' },
      { metric: 'Zero Creep', label: 'Digital change-order approvals' },
      { metric: '18% GST', label: 'Architectural fee tax calculation' }
    ],
    theOldWay: [
      'Clients changing floor plans and finishes midway without paying for revised drawings',
      'Delays in releasing site contractor payments due to disorganized milestone approvals',
      'Disputes over design fees versus material procurement budgets'
    ],
    theCoraWay: [
      'Digital change-orders quoting revised drawing fees that clients must sign before execution',
      'Clear 4-stage milestone schedules linked directly to architectural phase sign-offs',
      'Clean separation between architectural design fees and material procurement invoices'
    ],
    capabilities: [
      {
        title: 'Phased Architectural Milestone Billing',
        description: 'Link progressive payments to verified stages: Concept Design, 3D Renders, Working Drawings, and Site Completion.',
        tag: 'Phased Billing'
      },
      {
        title: '3D Render & Drawing Vault',
        description: 'Centralized repository for sharing CAD drawings, 3D walkthroughs, and material spec sheets with clients.',
        tag: 'Drawing Vault'
      },
      {
        title: 'Material Procurement Invoicing',
        description: 'Separate professional consultancy fees from interior material and contractor pass-through expenses.',
        tag: 'Material Billing'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Design Proposal', description: 'Set square-foot consultancy rates, 4-stage milestones, and scope boundaries.' },
      { step: '02', title: 'Sign Master Mandate', description: 'Client executes architectural agreement with digital verification.' },
      { step: '03', title: 'Stage Sign-Off', description: 'Client approves 3D renders and drawing sets at each project phase.' },
      { step: '04', title: 'Phased Invoicing', description: 'System issues 18% GST invoice upon each milestone approval.' }
    ],
    toolsReplaced: [
      { name: 'HoneyBook', category: 'Proposals & CRM', monthlySavingsINR: 3200 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Zoho Books', category: 'Invoicing', monthlySavingsINR: 2200 }
    ],
    faqs: [
      {
        question: 'Are the architectural contract templates aligned with Council of Architecture guidelines?',
        answer: 'Yes. Our pre-seeded templates follow standard practice guidelines for Indian architectural consultancies, including standard stage percentages and liability definitions.'
      },
      {
        question: 'How do change-orders handle client modifications during site execution?',
        answer: 'When a client modifies layouts or specifications, you can generate an instant Digital Change-Order with revised fees that must be signed before updated drawings are released.'
      },
      {
        question: 'Can we manage separate invoices for design fees and contractor procurement?',
        answer: 'Yes. Cora lets you maintain clear separate billing streams for professional design consultancy and material procurement.'
      }
    ],
    preSeededTemplates: [
      'Architectural Consultancy & Interior Design Master Agreement',
      'Phased Design Stage Sign-Off & Drawing Approval Form',
      'Material Specification & Contractor Procurement Mandate',
      'Change-Order Request & Scope Revision Addendum'
    ],
    workflowHighlights: [
      '4-stage milestone schedule (Concept, 3D Renders, Working Drawings, Site Handover)',
      'Digital client change-order approvals with digital signatures before execution',
      'Secure document repository for 3D walkthroughs, floor plans, and material sheets',
      'Automated GST tax breakdown on architectural consultancy fees'
    ],
    recommendedModules: [
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'lead-crm', title: 'Project CRM', icon: 'Users2' },
      { id: 'gst-invoicing', title: '18% GST Invoicing', icon: 'Receipt' },
      { id: 'task-board', title: 'Site Stages', icon: 'Kanban' }
    ],
    challengeVsSolution: {
      challenge: 'Clients changing specifications during construction without paying for updated drawings.',
      solution: 'Formal stage sign-off gates paired with instant digital change-orders for plan revisions.'
    },
    sampleRetainerText: 'Architectural Consultancy: ₹4,50,000 + 18% GST. Phased across Concept (20%), 3D Renders (30%), Working Drawings (30%), and Site Handover (20%).',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_realestate_agency.jpg',
    relatedIndustrySlugs: ['photo-video-studios', 'real-estate-property', 'design-uiux', 'consultants-advisors']
  },

  // ── 4. SERVICES & ADVISORY SECTOR ──
  {
    id: 'consultants-advisors',
    slug: 'consultants-advisors',
    title: 'Consultants & Advisors',
    shortTitle: 'Consultants & Advisors',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Advisory',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Briefcase',
    tagline: 'Voice-to-Proposal AI, advisory retainers & strategy vaults.',
    heroDescription: 'Executive operating system for management consultants and strategy advisors. Convert voice memos into polished client proposals in seconds, execute digital contracts, and bill recurring advisory retainers.',
    sacCode: 'SAC 998311',
    gstRate: '18% GST',
    stats: [
      { metric: '2-Min', label: 'Voice notes converted to proposals' },
      { metric: 'Monthly Auto', label: 'Recurring retainer billing' },
      { metric: 'Executive', label: 'Branded client portals' }
    ],
    theOldWay: [
      'Spending hours manually drafting long proposal documents after client discovery meetings',
      'Unsigned scope changes leading to unbilled advisory time and unpaid strategic guidance',
      'Sharing confidential boardroom strategies over unencrypted email chains'
    ],
    theCoraWay: [
      'Voice-to-Scope AI that turns 2-minute voice recordings into polished advisory proposals instantly',
      'Pre-seeded advisory retainers with digital signatures valid under the Indian IT Act 2000',
      'Branded executive client portals for sharing confidential strategy decks and roadmap reviews'
    ],
    capabilities: [
      {
        title: 'Voice-to-Proposal AI',
        description: 'Speak your client discovery notes aloud and let AI generate structured deliverables and pricing tables in seconds.',
        tag: 'Voice AI'
      },
      {
        title: 'Monthly Advisory Retainers',
        description: 'Automate executive advisory retainers with recurring GST invoices, meeting logs, and hours tracking.',
        tag: 'Advisory Retainers'
      },
      {
        title: 'Confidential Strategy Vaults',
        description: 'Bank-grade encrypted workspace for sharing market intelligence, board decks, and strategic roadmaps.',
        tag: 'Strategy Vault'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Record Discovery Brief', description: 'Speak discovery notes into Cora to generate a formal advisory proposal.' },
      { step: '02', title: 'Sign Advisory Mandate', description: 'Client board signs engagement contract with digital verification.' },
      { step: '03', title: 'Strategy Delivery', description: 'Share executive decks and roadmaps inside the client private portal.' },
      { step: '04', title: 'Retainer Settlement', description: 'Automated 18% GST invoice issued with professional tax classification.' }
    ],
    toolsReplaced: [
      { name: 'PandaDoc', category: 'Proposals', monthlySavingsINR: 3800 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Notion Teams', category: 'Knowledge Base', monthlySavingsINR: 2400 }
    ],
    faqs: [
      {
        question: 'How does Voice-to-Scope AI work for consulting proposals?',
        answer: 'You simply record an audio summary of your client meeting. Cora extracts key objectives, milestones, timeline, and deliverables, generating a ready-to-sign formal contract.'
      },
      {
        question: 'Can we protect proprietary consulting frameworks with built-in NDAs?',
        answer: 'Yes. Our pre-seeded engagement agreements feature comprehensive intellectual property and confidentiality terms protecting your proprietary frameworks.'
      },
      {
        question: 'Can we bill monthly retainers or project milestone fees?',
        answer: 'Yes. Cora supports ongoing monthly advisory retainers as well as phased milestone engagements (e.g. Diagnostic, Strategy, Execution).'
      }
    ],
    preSeededTemplates: [
      'Management Consulting & Strategic Advisory Agreement',
      'Voice-Generated Executive Proposal & SOW',
      'Board Advisory Retainer & Governance Protocol',
      'Mutual Non-Disclosure & Confidentiality Agreement'
    ],
    workflowHighlights: [
      'Voice-to-Scope AI converting audio discovery notes into formal proposals in 2 minutes',
      'Multi-stage payment milestones (Advance, Diagnostic Wrap, Board Review)',
      'Branded executive client presentation portals for sharing strategy decks',
      'Complete multi-tenant data isolation protecting confidential corporate transformations'
    ],
    recommendedModules: [
      { id: 'voice-to-scope', title: 'Voice-to-Scope', icon: 'Sparkles' },
      { id: 'ai-cofounder', title: 'AI Assistant', icon: 'Bot' },
      { id: 'esign-vault', title: 'E-Sign Vault', icon: 'ShieldCheck' },
      { id: 'lead-crm', title: 'Client CRM', icon: 'Users2' }
    ],
    challengeVsSolution: {
      challenge: 'Spending hours drafting long proposal documents after client discovery meetings.',
      solution: 'Voice-to-Scope AI that transforms voice recordings and notes into ready-to-sign formal advisory contracts.'
    },
    sampleRetainerText: 'Corporate Strategy Mandate: ₹4,00,000/mo + 18% GST. Includes bi-weekly executive reviews, market intelligence audits, and board decks.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_production_house.jpg',
    relatedIndustrySlugs: ['lawyers-law-firms', 'financial-advisors', 'software-agencies', 'marketing-seo']
  },

  {
    id: 'doctors-clinics',
    slug: 'doctors-clinics',
    title: 'Doctors & Clinics',
    shortTitle: 'Doctors & Clinics',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Advisory',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Heart',
    tagline: 'Paperless patient intake, digital consent & appointment booking.',
    heroDescription: 'Modern clinic management for dental clinics, wellness centers, physiotherapy practices, and specialized medical consultancies. Coordinate digital intake forms, patient consent signatures, and automated receipts.',
    sacCode: 'SAC 999312',
    gstRate: 'Exempt / 18% GST (Cosmetic)',
    stats: [
      { metric: '100% Digital', label: 'Paperless intake forms and consent' },
      { metric: 'Instant', label: 'Automated booking confirmations' },
      { metric: 'Confidential', label: 'Encrypted patient records' }
    ],
    theOldWay: [
      'Manual paper intake forms creating waiting room delays and unorganized physical files',
      'Patients missing appointments due to lack of automated confirmation messages',
      'Unsigned or misplaced procedure consent forms exposing practitioners to liability'
    ],
    theCoraWay: [
      'Mobile-friendly digital intake forms sent to patients prior to clinic arrival',
      'Automated appointment confirmations and reminders reducing no-shows',
      'Digital patient consent signatures with timestamped audit records before procedures'
    ],
    capabilities: [
      {
        title: 'Paperless Digital Intake Forms',
        description: 'Send mobile intake questionnaires capturing medical history, allergies, and symptoms prior to appointment.',
        tag: 'Digital Intake'
      },
      {
        title: 'Digital Patient Consent',
        description: 'Collect legally binding digital consent signatures on tablets or mobile phones before procedures.',
        tag: 'Patient Consent'
      },
      {
        title: 'Instant Receipts & Billing',
        description: 'Issue instant digital fee receipts and GST invoices (with medical exemption handling).',
        tag: 'Clinic Billing'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Online Intake Link', description: 'Patient receives digital intake questionnaire to complete on their phone.' },
      { step: '02', title: 'Sign Digital Consent', description: 'Patient reviews treatment details and signs informed consent form digitally.' },
      { step: '03', title: 'Consultation & Notes', description: 'Practitioner records clinical notes in the secure patient profile.' },
      { step: '04', title: 'Instant Receipt', description: 'Digital fee receipt issued with integrated UPI QR code for contactless checkout.' }
    ],
    toolsReplaced: [
      { name: 'Practo Ray', category: 'Clinic Management', monthlySavingsINR: 3500 },
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'Paper Intake Filing', category: 'Admin Overhead', monthlySavingsINR: 2000 }
    ],
    faqs: [
      {
        question: 'Are health care consultation fees exempt from GST?',
        answer: 'Yes. Under standard GST rules, primary health care services are exempt from GST. For aesthetic or cosmetic procedures where 18% GST applies, Cora handles both automatically.'
      },
      {
        question: 'Can patients sign consent forms on their own mobile phone or clinic tablet?',
        answer: 'Yes. Patients can sign consent forms on their phone via a simple link or directly on a clinic iPad or tablet at reception.'
      },
      {
        question: 'Is patient medical data stored securely and privately?',
        answer: 'Yes. Every workspace uses encrypted storage with strict role-based access, ensuring patient records remain confidential.'
      }
    ],
    preSeededTemplates: [
      'General Medical & Dental Informed Consent Form',
      'Specialized Aesthetic Procedure Consent Protocol',
      'Patient Registration & Medical History Intake',
      'Tele-Consultation & Digital Prescription Consent'
    ],
    workflowHighlights: [
      'Paperless mobile patient intake forms capturing medical history prior to arrival',
      'Digital informed consent signatures timestamped before procedures',
      'Instant fee receipt generation with UPI QR code checkout',
      'Automated appointment calendar reminders'
    ],
    recommendedModules: [
      { id: 'form-builder', title: 'Patient Intake', icon: 'FormInput' },
      { id: 'esign-vault', title: 'Consent E-Sign', icon: 'ShieldCheck' },
      { id: 'gst-invoicing', title: 'Fee Receipts', icon: 'Receipt' },
      { id: 'rbac-system', title: 'Data Isolation', icon: 'Lock' }
    ],
    challengeVsSolution: {
      challenge: 'Long waiting room queues and misplaced paper consent forms exposing clinics to liability.',
      solution: 'Mobile digital intake questionnaires paired with instant digital consent signatures.'
    },
    sampleRetainerText: 'Specialized Clinic Package: ₹35,000 (GST Exempt for Health Care / 18% GST for Aesthetic Procedures). Includes full consultation and follow-up review.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_solo_creator.jpg',
    relatedIndustrySlugs: ['salons-wellness', 'consultants-advisors', 'lawyers-law-firms', 'tax-ca-firms']
  },

  {
    id: 'salons-wellness',
    slug: 'salons-wellness',
    title: 'Salons & Wellness',
    shortTitle: 'Salons & Wellness',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Advisory',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Scissors',
    tagline: 'Online booking deposits, staff scheduling & automated review requests.',
    heroDescription: 'Effortless salon and wellness management for premium hair studios, aesthetic clinics, and spa centers. Collect advance booking deposits, manage staff calendars, and send automated Google review requests.',
    sacCode: 'SAC 999721',
    gstRate: '18% GST',
    stats: [
      { metric: '60% Fewer', label: 'No-shows with upfront deposits' },
      { metric: '5-Star', label: 'Automated Google review collection' },
      { metric: '1-Click', label: 'Instant UPI payment checkout' }
    ],
    theOldWay: [
      'Clients missing booked appointments without penalty, leaving staff idle',
      'Paper appointment books causing double-bookings and scheduling errors',
      'Losing out on 5-star Google reviews due to no automated follow-up system'
    ],
    theCoraWay: [
      'Upfront booking deposits collected online via UPI to prevent last-minute no-shows',
      'Real-time staff booking calendar accessible from any phone or front-desk computer',
      'Automated review requests sent after checkout to grow positive Google reviews'
    ],
    capabilities: [
      {
        title: 'Booking Deposit Collection',
        description: 'Collect small advance booking deposits online via UPI to secure appointment slots and eliminate no-shows.',
        tag: 'Booking Deposits'
      },
      {
        title: 'Staff & Chair Scheduling',
        description: 'Coordinate multiple stylists and treatment rooms with a visual, real-time master calendar.',
        tag: 'Staff Scheduling'
      },
      {
        title: 'Automated Review Requests',
        description: 'Send automated thank-you messages and 5-star Google review links immediately after client checkout.',
        tag: 'Google Reviews'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Select Service & Slot', description: 'Client chooses treatment, stylist, and time slot on your mobile booking page.' },
      { step: '02', title: 'Pay Advance Deposit', description: 'Client pays booking deposit instantly via UPI QR to lock their reservation.' },
      { step: '03', title: 'Service Execution', description: 'Stylist checks client notes and delivers treatment at scheduled time.' },
      { step: '04', title: 'Checkout & Review', description: 'Final balance settled at reception and automated review link is sent to client.' }
    ],
    toolsReplaced: [
      { name: 'Fresha', category: 'Salon Software', monthlySavingsINR: 2500 },
      { name: 'Zenoti', category: 'Spa Management', monthlySavingsINR: 6500 },
      { name: 'Paper Books', category: 'Admin Overhead', monthlySavingsINR: 1500 }
    ],
    faqs: [
      {
        question: 'How do advance booking deposits reduce salon no-shows?',
        answer: 'Requiring a modest advance deposit (e.g. ₹500) creates commitment, reducing appointment no-shows by up to 60%.'
      },
      {
        question: 'Can individual stylists view their own daily appointment schedules?',
        answer: 'Yes. Stylists can view their daily schedule on their phone with client notes and service durations.'
      },
      {
        question: 'How does automated Google review collection work?',
        answer: 'When a bill is marked as settled, Cora sends a friendly thank-you message with a direct link to your Google Business profile.'
      }
    ],
    preSeededTemplates: [
      'Salon Service Booking & Cancellation Policy',
      'Hair Treatment & Skin Patch Test Consent Form',
      'Membership & Pre-Paid Package Agreement',
      'Staff Commission & Stylist Employment Contract'
    ],
    workflowHighlights: [
      'Advance booking deposit collection via UPI QR reducing appointment no-shows',
      'Visual staff and treatment room master calendar',
      'Automated 5-star Google review request messages upon service checkout',
      'Membership and package balance tracking with automated GST invoicing'
    ],
    recommendedModules: [
      { id: 'form-builder', title: 'Booking Page', icon: 'FormInput' },
      { id: 'gst-invoicing', title: 'POS Invoicing', icon: 'Receipt' },
      { id: 'lead-crm', title: 'Client CRM', icon: 'Users2' },
      { id: 'task-board', title: 'Staff Calendar', icon: 'Kanban' }
    ],
    challengeVsSolution: {
      challenge: 'High appointment no-show rates and missed customer reviews.',
      solution: 'Online booking deposits combined with automated post-service Google review requests.'
    },
    sampleRetainerText: 'Annual Wellness Membership: ₹45,000 + 18% GST. Includes 12 monthly premium treatments, priority booking, and family sharing.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_solo_creator.jpg',
    relatedIndustrySlugs: ['doctors-clinics', 'consultants-advisors', 'photo-video-studios', 'marketing-seo']
  },

  {
    id: 'real-estate-property',
    slug: 'real-estate-property',
    title: 'Real Estate & Property',
    shortTitle: 'Real Estate',
    sectorId: 'services_lifestyle',
    sectorLabel: 'Services & Advisory',
    sectorBadge: 'SERVICES & ADVISORY',
    badgeColor: 'teal',
    iconName: 'Building2',
    tagline: 'Property microsites, brokerage mandates & commission billing.',
    heroDescription: 'The high-converting platform for real estate brokerages and commercial leasing firms. Build property listing microsites, execute exclusive broker mandates digitally, and invoice commissions effortlessly.',
    sacCode: 'SAC 997212',
    gstRate: '18% GST',
    stats: [
      { metric: '100% Signed', label: 'Exclusive digital broker mandates' },
      { metric: 'Fast Launch', label: 'Property listing microsites' },
      { metric: '18% GST', label: 'Compliant commission invoices' }
    ],
    theOldWay: [
      'Disputes over brokerage commission percentages after commercial lease or sale closures',
      'Sharing property photos and videos over messy WhatsApp messages that get buried',
      'Losing high-intent buyer leads due to slow follow-up and lack of organized tracking'
    ],
    theCoraWay: [
      'Pre-signed exclusive brokerage mandate agreements executed digitally before property showings',
      'Branded property listing microsites with built-in virtual tours and lead capture forms',
      'Automated commission invoicing with compliant 18% GST tax calculation upon deal closure'
    ],
    capabilities: [
      {
        title: 'Property Listing Microsites',
        description: 'Create beautiful property landing pages with virtual tours, floor plans, and lead capture forms in minutes.',
        tag: 'Property Microsites'
      },
      {
        title: 'Exclusive Brokerage Mandates',
        description: 'Execute exclusive representation agreements and commission fee deeds digitally before showing properties.',
        tag: 'Broker Mandates'
      },
      {
        title: 'Commission Invoicing & GST',
        description: 'Issue professional brokerage commission invoices with automated 18% GST tax calculations upon deal closure.',
        tag: 'Commission Invoicing'
      }
    ],
    howItWorks: [
      { step: '01', title: 'Sign Mandate Agreement', description: 'Property owner executes exclusive brokerage representation agreement digitally.' },
      { step: '02', title: 'Launch Listing Site', description: 'Publish property microsite with 4K photos, floor plans, and lead capture.' },
      { step: '03', title: 'Lead Triage & Site Visits', description: 'Track buyer inquiries and coordinate property site visit schedules.' },
      { step: '04', title: 'Commission Invoicing', description: 'Issue automated commission invoice upon successful lease or sale execution.' }
    ],
    toolsReplaced: [
      { name: 'DocuSign', category: 'E-Signatures', monthlySavingsINR: 3500 },
      { name: 'LeadSquared', category: 'Real Estate CRM', monthlySavingsINR: 5500 },
      { name: 'Unbounce', category: 'Landing Pages', monthlySavingsINR: 4200 }
    ],
    faqs: [
      {
        question: 'How do digital brokerage mandates protect our commission?',
        answer: 'Our pre-seeded agreements lock in commission percentages, exclusivity terms, and validity periods, providing full legal enforceability under Indian contract law.'
      },
      {
        question: 'Can we generate property listing landing pages without coding?',
        answer: 'Yes. You can build branded property microsites with floor plans, photo galleries, and lead capture forms in minutes.'
      },
      {
        question: 'Does Cora support channel partner commission sharing agreements?',
        answer: 'Yes. Pre-seeded templates include channel partner fee-sharing contracts for seamless co-broking collaboration.'
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
      'Brokerage commission agreements with legally binding digital signatures',
      'Listing AI tool generating high-converting property descriptions in seconds',
      'Automated commission invoicing with compliant GST tax calculations'
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
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-200',
    accentText: 'text-zinc-900',
    heroImage: '/images/usecase_realestate_agency.jpg',
    relatedIndustrySlugs: ['photo-video-studios', 'architecture-interiors', 'lawyers-law-firms', 'consultants-advisors']
  }
];
