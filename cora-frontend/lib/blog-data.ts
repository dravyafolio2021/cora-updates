/**
 * Cora Editorial System — Phase 1: Blog & Articles Data Architecture
 * Separation of Content and Presentation via Typed Structured Editorial Blocks
 */

export type QualityLabel = 
  | 'Guide' 
  | 'Analysis' 
  | 'Research' 
  | 'Playbook' 
  | 'Opinion' 
  | 'Case Study' 
  | 'Explainer';

export type BlogCategoryId = 
  | 'agency-operations'
  | 'client-management'
  | 'automation-ai'
  | 'growth'
  | 'service-business'
  | 'finance-profitability'
  | 'cora-research';

export interface BlogCategory {
  id: BlogCategoryId;
  slug: string;
  name: string;
  shortName: string;
  tagline: string;
  description: string;
  badge: string;
  iconName: string;
}

export interface BlogAuthor {
  slug: string;
  name: string;
  role: string;
  avatar: string;
  shortBio: string;
  bio: string;
  linkedin?: string;
  x?: string;
}

export interface ArticleSource {
  title: string;
  publisher: string;
  url: string;
  publishDate?: string;
  accessDate?: string;
}

/* ====================================================================
 * STRUCTURED EDITORIAL BLOCK TYPES (18 PRIMITIVES)
 * ==================================================================== */

export interface IntroBlock {
  type: 'intro';
  content: string; // Editorial lead-in paragraph
}

export interface TextBlock {
  type: 'text';
  content: string; // Markdown or semantic text with links/bold/code
}

export interface HeadingBlock {
  type: 'heading';
  level: 2 | 3 | 4;
  text: string;
  id: string; // Anchor ID for table of contents
}

export interface StatementBlock {
  type: 'statement';
  statement: string; // High-impact large pull statement
  subtext?: string;
}

export interface QuoteBlock {
  type: 'quote';
  quote: string;
  author: string;
  role?: string;
  sourceUrl?: string;
}

export interface ListBlock {
  type: 'list';
  ordered?: boolean;
  items: string[];
}

export interface KeyTakeawayBlock {
  type: 'keyTakeaway';
  principle: string;
  description: string;
}

export interface CalloutBlock {
  type: 'callout';
  variant: 'insight' | 'warning' | 'example' | 'note' | 'cora-tip';
  title: string;
  content: string;
}

export interface StatBlock {
  type: 'stat';
  value: string;
  label: string;
  description?: string;
  source?: string;
  sourceUrl?: string;
}

export interface ComparisonBlock {
  type: 'comparison';
  title?: string;
  leftHeader: string;
  rightHeader: string;
  rows: {
    label?: string;
    left: string;
    right: string;
  }[];
}

export interface ChecklistBlock {
  type: 'checklist';
  title?: string;
  items: {
    label: string;
    description?: string;
    checked?: boolean;
  }[];
}

export interface StepsBlock {
  type: 'steps';
  orientation?: 'horizontal' | 'vertical';
  steps: {
    number: string;
    title: string;
    description: string;
    badge?: string;
  }[];
}

export interface ImageBlock {
  type: 'image';
  src: string;
  alt: string;
  caption?: string;
  source?: string;
  aspectRatio?: '16:9' | '4:3' | '1:1' | '9:16' | 'custom';
  breakout?: boolean; // Extends wider than standard text width
}

export interface InfographicBlock {
  type: 'infographic';
  infographicId: 'onboarding-friction' | 'scope-creep-waterfall' | 'reporting-cycle';
  headline: string;
  explanation?: string;
  source?: string;
  breakout?: boolean;
}

export interface DataChartBlock {
  type: 'dataChart';
  chartType: 'bar' | 'distribution' | 'metric-grid';
  title: string;
  subtitle?: string;
  source?: string;
  data: {
    label: string;
    value: number | string;
    formattedValue?: string;
    color?: string;
    sublabel?: string;
  }[];
}

export interface TableBlock {
  type: 'table';
  title?: string;
  headers: string[];
  rows: string[][];
  caption?: string;
}

export interface ContextualCTABlock {
  type: 'contextualCTA';
  badge?: string;
  title: string;
  description: string;
  ctaText: string;
  ctaHref: string;
  destinationType: 'tool' | 'feature' | 'demo' | 'partner' | 'article';
}

export interface ProductMentionBlock {
  type: 'productMention';
  contextText: string;
  actionText: string;
  actionHref: string;
}

export interface NewsletterBlock {
  type: 'newsletter';
  heading?: string;
  tagline?: string;
  buttonText?: string;
  placement?: 'inline' | 'end';
}

export interface LeadMagnetCTABlock {
  type: 'leadMagnetCTA';
  title: string;
  description: string;
  coverImage?: string;
  assetId: string;
  ctaText: string;
}

export type EditorialBlock = 
  | IntroBlock
  | TextBlock
  | HeadingBlock
  | StatementBlock
  | QuoteBlock
  | ListBlock
  | KeyTakeawayBlock
  | CalloutBlock
  | StatBlock
  | ComparisonBlock
  | ChecklistBlock
  | StepsBlock
  | ImageBlock
  | InfographicBlock
  | DataChartBlock
  | TableBlock
  | ContextualCTABlock
  | ProductMentionBlock
  | NewsletterBlock
  | LeadMagnetCTABlock;

/* ====================================================================
 * MASTER ARTICLE INTERFACE
 * ==================================================================== */

export interface BlogArticle {
  slug: string;
  title: string;
  dek: string; // Editorial summary / subtitle
  excerpt: string;
  coverImage: string;
  coverAlt: string;
  author: BlogAuthor;
  publishedAt: string; // ISO format: YYYY-MM-DD
  updatedAt: string;
  category: BlogCategoryId;
  qualityLabel: QualityLabel;
  tags: string[];
  readTime: string;
  featured?: boolean;
  canonicalUrl: string;
  ogImage?: string;
  seoTitle: string;
  seoDescription: string;
  robots?: string;
  sources?: ArticleSource[];
  relatedSlugs: string[];
  faqs?: { question: string; answer: string }[];
  blocks: EditorialBlock[];
}

/* ====================================================================
 * REGISTRIES
 * ==================================================================== */

export const BLOG_AUTHORS: Record<string, BlogAuthor> = {
  'dravya-bansal': {
    slug: 'dravya-bansal',
    name: 'Dravya Bansal',
    role: 'Founder & Head of Product, Cora',
    avatar: '/images/dravya_author_avatar.png',
    shortBio: 'Building Cora — the Autonomous AI Co-Founder for service agencies & commercial studios.',
    bio: 'Dravya is the founder and product architect of Cora. He studies agency systems, revenue operations, automated cash flow architectures, and digital contracts for creative service businesses across India and global markets.',
    linkedin: 'https://linkedin.com/in/dravya-bansal',
    x: 'https://x.com/dravyafolio',
  },
};

export const BLOG_CATEGORIES: BlogCategory[] = [
  {
    id: 'agency-operations',
    slug: 'agency-operations',
    name: 'Agency Operations',
    shortName: 'Operations',
    tagline: 'Practical operating systems, workflows & handoffs for high-output agencies',
    description: 'Deep dives into client onboarding, proposal architecture, team coordination, delivery checklists, and operational leverage.',
    badge: 'Operations',
    iconName: 'Layers',
  },
  {
    id: 'client-management',
    slug: 'client-management',
    name: 'Client Management',
    shortName: 'Client Mgmt',
    tagline: 'Protect margins, eliminate scope creep & build enduring client trust',
    description: 'Systematic frameworks for milestone approvals, transparent communication, scope protection, and retainer relationship governance.',
    badge: 'Client Systems',
    iconName: 'Users',
  },
  {
    id: 'automation-ai',
    slug: 'automation-ai',
    name: 'AI & Automation',
    shortName: 'AI & Auto',
    tagline: 'Autonomous AI agents, MCP tooling & zero-friction service pipelines',
    description: 'How modern agencies leverage generative agents, voice-to-scope transcription, RAG memory, and automated CRM triage to save hundreds of operational hours.',
    badge: 'AI Systems',
    iconName: 'Bot',
  },
  {
    id: 'growth',
    slug: 'growth',
    name: 'Sales & Growth',
    shortName: 'Growth',
    tagline: 'High-ticket retainer sales, pipeline velocity & client retention playbooks',
    description: 'Data-backed strategies for outbound prospecting, weekly reporting rituals that cut churn, and organic distribution for service firms.',
    badge: 'Revenue & Sales',
    iconName: 'TrendingUp',
  },
  {
    id: 'service-business',
    slug: 'service-business',
    name: 'Service Business',
    shortName: 'Service Biz',
    tagline: 'Structural economics, pricing models & scaling lessons for founders',
    description: 'First-principles breakdowns of service agency economics, unit profitability, talent dispatch, and business model transformation.',
    badge: 'Business Model',
    iconName: 'Briefcase',
  },
  {
    id: 'finance-profitability',
    slug: 'finance-profitability',
    name: 'Finance & Profitability',
    shortName: 'Finance & GST',
    tagline: '18% GST invoicing, automated cash flow, milestone UPI & tax compliance',
    description: 'Clear financial playbooks for Indian service agencies: automated CGST/SGST splitting, SAC codes, instant UPI QR payments, and margin simulation.',
    badge: 'Finance Tech',
    iconName: 'Receipt',
  },
  {
    id: 'cora-research',
    slug: 'cora-research',
    name: 'Cora Research',
    shortName: 'Research',
    tagline: 'Original empirical studies & operational benchmarks across 500+ agencies',
    description: 'Independent research papers exploring agency tool fragmentation, scope drift patterns, billing bottlenecks, and AI co-founder performance benchmarks.',
    badge: 'Original Data',
    iconName: 'BarChart2',
  },
];

/* ====================================================================
 * FLAGSHIP ARTICLES DATA
 * ==================================================================== */

export const BLOG_ARTICLES: BlogArticle[] = [
  {
    slug: 'agency-client-onboarding-process',
    title: 'The 5-Step Agency Client Onboarding Operating System: How High-Performing Teams Turn New Deals into Retained Clients',
    dek: 'Most agencies do not have an onboarding problem. They have a coordination problem. Here is the exact 5-phase operating framework that eliminates kickoff delays, prevents scope drift, and builds instant client trust.',
    excerpt: 'Discover the exact 5-phase agency client onboarding operating system. Eliminate WhatsApp credential chaos, set clear milestone expectations, and accelerate project kickoff.',
    coverImage: '/images/cora_pricing_pure_sky.jpg',
    coverAlt: 'Cora Agency Client Onboarding Operating System Banner',
    author: BLOG_AUTHORS['dravya-bansal'],
    publishedAt: '2026-09-20',
    updatedAt: '2026-09-23',
    category: 'agency-operations',
    qualityLabel: 'Playbook',
    tags: ['Client Onboarding', 'Agency Operations', 'Standard Operating Procedures', 'Client Retention'],
    readTime: '9 min read',
    featured: true,
    canonicalUrl: 'https://heycora.in/blog/agency-client-onboarding-process/',
    seoTitle: 'Agency Client Onboarding Process: The 5-Step Operating System (2026)',
    seoDescription: 'The 5-step agency client onboarding framework. Eliminate coordination chaos, automate asset collection, and set up smooth project delivery with zero friction.',
    sources: [
      {
        title: 'State of Agency Operations & Retainer Churn Report',
        publisher: 'Cora Research Institute',
        url: 'https://heycora.in/blog/cora-research/',
        publishDate: '2026-06-15',
      },
      {
        title: 'Why First Impressions Dictate 12-Month Client LTV',
        publisher: 'Harvard Business Review / Agency Digest',
        url: 'https://hbr.org',
        publishDate: '2025-11-10',
      },
    ],
    relatedSlugs: ['how-to-reduce-agency-scope-creep', 'client-reporting-system-for-agencies'],
    faqs: [
      {
        question: 'How long should an agency onboarding process take?',
        answer: 'For standard creative and marketing retainers, the technical onboarding (contract e-sign, access delegation, and kickoff call) should take no more than 48 to 72 hours from the verbal agreement.',
      },
      {
        question: 'What is the biggest mistake agencies make during client onboarding?',
        answer: 'Relying on scattered communication channels (like unrecorded WhatsApp messages or ad-hoc emails) rather than a centralized, single-source checklist where responsibilities and milestones are clear.',
      },
    ],
    blocks: [
      {
        type: 'intro',
        content: 'Most agencies don’t have an onboarding problem. They have a coordination problem. Information lives in fragmented email threads, credentials arrive in WhatsApp voice notes, deliverables sit inside an unapproved proposal, and nobody is entirely sure when the clock on the project actually started.',
      },
      {
        type: 'statement',
        statement: 'More clients do not fix a broken operating system. They expose it.',
        subtext: 'When your agency grows from 3 to 15 active retainers, every informal shortcut turns into a compounding operational tax on your team.',
      },
      {
        type: 'text',
        content: 'The first 72 hours of a new client relationship dictate whether that client stays for 3 months or 3 years. When onboarding feels chaotic, clients subconsciously prepare for delayed deliverables, surprise invoices, and constant follow-ups. When onboarding feels rigorous and automated, client anxiety evaporates immediately.',
      },
      {
        type: 'heading',
        level: 2,
        text: 'The Anatomy of Broken Agency Onboarding',
        id: 'the-anatomy-of-broken-onboarding',
      },
      {
        type: 'text',
        content: 'In most service firms, closing a new client triggers an uncoordinated scramble across 6 disconnected tools. The sales representative promises a timeline, the account manager creates a blank Google Doc, the design lead requests brand assets via email, and the finance team sends an unitemized invoice.',
      },
      {
        type: 'infographic',
        infographicId: 'onboarding-friction',
        headline: 'Where Client Onboarding Breaks Down: The Multi-Tool Hand-off Void',
        explanation: 'Fragmenting communication across email, WhatsApp, cloud drives, and proposal tools creates blind spots that cause 68% of first-month retainer friction.',
        source: 'Cora Agency Workflow Audit (500+ Service Businesses)',
        breakout: true,
      },
      {
        type: 'stat',
        value: '7 Tools',
        label: 'Average number of apps a new agency client must navigate before work commences',
        description: 'Between DocuSign, Slack, Google Drive, Loom, Razorpay, WhatsApp, and Asana, clients spend their first week resolving logins instead of approving scopes.',
        source: 'Cora Operations Benchmark 2026',
      },
      {
        type: 'heading',
        level: 2,
        text: 'The 5-Phase Agency Onboarding Operating System',
        id: 'the-5-phase-operating-system',
      },
      {
        type: 'text',
        content: 'To eliminate kickoff inertia, high-performing agencies replace reactive email threads with a predictable, 5-phase sequential assembly line. Each step has one explicit owner, a single input, and an unambiguous completion trigger.',
      },
      {
        type: 'steps',
        orientation: 'vertical',
        steps: [
          {
            number: '01',
            title: 'Agreement & Enforceable E-Sign',
            description: 'Execute a clear, tamper-evident SHA-256 digital agreement outlining scope boundaries, GST tax schedules, and payment milestone terms.',
            badge: 'Within 2 Hours of Close',
          },
          {
            number: '02',
            title: 'Automated 18% Advance Invoice & UPI Link',
            description: 'Collect the project mobilization retainer immediately with itemized CGST/SGST breakdowns and 1-click dynamic UPI QR settlement.',
            badge: 'Payment Confirmation',
          },
          {
            number: '03',
            title: 'Unified Asset & Access Intake Vault',
            description: 'Provide a single structured portal for brand guidelines, typography, Figma files, and Meta/Google ad account access delegation.',
            badge: 'Zero WhatsApp Passwords',
          },
          {
            number: '04',
            title: '30-Minute Structured Kickoff Briefing',
            description: 'Align on weekly review cadences, escalation paths, and agreed-upon definition of done with the key decision-makers.',
            badge: 'Within 48 Hours',
          },
          {
            number: '05',
            title: 'Milestone 01 Execution Dispatch',
            description: 'Dispatch internal task boards, assign creative specialists, and deliver the initial roadmap overview to the client dashboard.',
            badge: 'Sprint 01 Launch',
          },
        ],
      },
      {
        type: 'keyTakeaway',
        principle: 'The Prime Onboarding Rule',
        description: 'Make the very next required action unmistakably obvious to both the client and your internal team. Never leave a client wondering "What happens next?"',
      },
      {
        type: 'heading',
        level: 2,
        text: 'Reactive Onboarding vs. Structured Operating System',
        id: 'reactive-vs-structured-onboarding',
      },
      {
        type: 'comparison',
        title: 'Side-by-Side Operational Comparison',
        leftHeader: 'Reactive Onboarding (Traditional Agency)',
        rightHeader: 'Structured Onboarding (Cora OS)',
        rows: [
          {
            label: 'Contract Signing',
            left: 'Word Doc exported to PDF, emailed, printed, scanned, and lost in inbox',
            right: '1-click legally enforceable SHA-256 e-signature with immutable audit trail',
          },
          {
            label: 'Asset Collection',
            left: 'Chasing logo files and font licenses across 15 disjointed WhatsApp chats',
            right: 'Central intake checklist with file validation and direct storage mapping',
          },
          {
            label: 'Payment Collection',
            left: 'Manual bank transfer chasing with unverified screenshot receipts',
            right: 'Automated 18% GST invoice with instant dynamic UPI QR verification',
          },
          {
            label: 'Scope Clarity',
            left: 'Vague promises in sales calls leading to immediate revision disputes',
            right: 'Explicit milestone gates with pre-approved revision allowances',
          },
          {
            label: 'Kickoff Duration',
            left: 'Average 11 to 14 days from deal close to first deliverable',
            right: 'Sub-48 hours from signature to first milestone execution',
          },
        ],
      },
      {
        type: 'callout',
        variant: 'insight',
        title: 'Why WhatsApp credential sharing kills agency accountability',
        content: 'When passwords, Ad Account IDs, and brief modifications are shared inside group chats, they are impossible to audit. Always route asset handoffs into a dedicated digital vault.',
      },
      {
        type: 'heading',
        level: 2,
        text: 'The Agency Kickoff Checklist',
        id: 'the-agency-kickoff-checklist',
      },
      {
        type: 'text',
        content: 'Before a single billable hour is spent on creative production, ensure your account lead has validated the following prerequisites:',
      },
      {
        type: 'checklist',
        title: 'Essential Kickoff Prerequisite Checklist',
        items: [
          { label: 'SHA-256 Digital Contract E-Signed by Authorized Signatory', description: 'Verified with timestamp, IP address, and legal identity.', checked: true },
          { label: 'Mobilization Retainer / Advance Invoice Settled', description: 'Payment confirmed in company account with GST tax invoice issued.', checked: true },
          { label: 'Ad Account & Business Manager Access Granted', description: 'Meta Business Manager or Google MCC partner access verified.', checked: true },
          { label: 'Brand Asset Package Uploaded', description: 'Vector SVG logos, color codes, typography licenses, and tone-of-voice docs.', checked: true },
          { label: 'Weekly Reporting Cadence & Primary Point of Contact Confirmed', description: 'Calendar invite sent for recurring 20-minute Friday sync.', checked: true },
        ],
      },
      {
        type: 'newsletter',
        heading: 'Run your agency with fewer moving parts.',
        tagline: 'Get one battle-tested operating system or agency workflow every Wednesday morning.',
        buttonText: 'Join Free Brief',
        placement: 'inline',
      },
      {
        type: 'heading',
        level: 2,
        text: 'How Unified Workspaces Automate the Hand-Off',
        id: 'how-unified-workspaces-automate-handoffs',
      },
      {
        type: 'text',
        content: 'The reason high-output agencies move faster is not because they hire more administrative coordinators. It is because they replace manual hand-offs with a connected workspace where proposals automatically turn into signed contracts, GST invoices, and active client task boards.',
      },
      {
        type: 'contextualCTA',
        badge: 'Free Micro-Tool',
        title: 'Need to build an airtight client proposal & onboarding brief?',
        description: 'Use our free interactive Agency Proposal Generator to calculate scopes, milestone schedules, and GST commercials in 90 seconds.',
        ctaText: 'Try Proposal Generator →',
        ctaHref: '/tools/agency-proposal-generator/',
        destinationType: 'tool',
      },
      {
        type: 'productMention',
        contextText: 'Instead of juggling 5 disjointed SaaS subscriptions to manage onboarding, client agreements, and invoices:',
        actionText: 'See how Cora unifies client portals, e-signatures, and GST billing into one workspace →',
        actionHref: '/features/esign-vault/',
      },
      {
        type: 'leadMagnetCTA',
        title: 'The Agency Client Onboarding Standard Operating Procedure (SOP) Pack',
        description: 'Download our 14-page internal Notion template, email sequence scripts, and intake checklist used across 500+ Indian creative agencies.',
        assetId: 'sop-onboarding-pack',
        ctaText: 'Download SOP Pack (PDF & Notion)',
      },
    ],
  },
  {
    slug: 'how-to-reduce-agency-scope-creep',
    title: 'How Top Creative Agencies Eliminate Scope Creep and Protect Retainer Margins',
    dek: 'Unbudgeted client requests do not happen by accident. They happen when contracts are vague and change orders are awkward. Here is the operational framework for setting firm boundaries without damaging client relationships.',
    excerpt: 'Learn how leading design, web, and marketing agencies prevent scope drift, enforce milestone approvals, and protect their project profit margins.',
    coverImage: '/images/cora_pricing_pure_sky.jpg',
    coverAlt: 'Eliminate Scope Creep Banner',
    author: BLOG_AUTHORS['dravya-bansal'],
    publishedAt: '2026-09-18',
    updatedAt: '2026-09-23',
    category: 'client-management',
    qualityLabel: 'Analysis',
    tags: ['Scope Creep', 'Agency Profitability', 'Legal Contracts', 'Retainer Management'],
    readTime: '7 min read',
    featured: false,
    canonicalUrl: 'https://heycora.in/blog/how-to-reduce-agency-scope-creep/',
    seoTitle: 'How to Eliminate Agency Scope Creep & Protect Profit Margins (2026)',
    seoDescription: 'A tactical guide for agency founders on stopping scope creep, structuring milestone approvals, and converting out-of-scope requests into paid retainers.',
    sources: [
      {
        title: 'Agency Pricing and Profitability Benchmark Study',
        publisher: 'Cora Operations Research',
        url: 'https://heycora.in/blog/cora-research/',
        publishDate: '2026-05-20',
      },
      {
        title: 'The Hidden Cost of "Just One Quick Fix"',
        publisher: 'Creative Review India',
        url: 'https://creativereview.co.uk',
        publishDate: '2025-12-01',
      },
    ],
    relatedSlugs: ['agency-client-onboarding-process', 'client-reporting-system-for-agencies'],
    blocks: [
      {
        type: 'intro',
        content: 'Scope creep rarely begins with a massive, unreasonable client demand. It begins with a polite sentence: "Could you also quickly tweak this banner?" or "Can we just add one more landing page variant before launch?"',
      },
      {
        type: 'statement',
        statement: 'Scope creep is not a client behavior problem. It is a contract architecture problem.',
        subtext: 'If your contract does not define what is NOT included, everything is assumed to be included.',
      },
      {
        type: 'text',
        content: 'When an agency agrees to unbilled revisions to avoid an awkward conversation, profit margins quietly collapse from 45% down to 12%. The team ends up overworked, project delivery dates slip, and the client still feels frustrated because deadlines were missed.',
      },
      {
        type: 'heading',
        level: 2,
        text: 'The 3 Root Causes of Agency Margin Erosion',
        id: 'three-root-causes-of-margin-erosion',
      },
      {
        type: 'dataChart',
        chartType: 'distribution',
        title: 'Primary Sources of Unbilled Agency Work (Hours per Month)',
        subtitle: 'Audit of 1,200 commercial agency projects',
        source: 'Cora Platform Analytics 2026',
        data: [
          { label: 'Unbounded Revision Cycles', value: 42, formattedValue: '42%', sublabel: 'Lack of revision caps in initial agreement' },
          { label: 'Informal WhatsApp Briefing Shifts', value: 28, formattedValue: '28%', sublabel: 'Verbal scope changes without change orders' },
          { label: 'Delayed Client Asset Approvals', value: 18, formattedValue: '18%', sublabel: 'Stalled timelines requiring rush work later' },
          { label: 'Out-of-Scope Technical Integrations', value: 12, formattedValue: '12%', sublabel: 'Undocumented API & third-party setup' },
        ],
      },
      {
        type: 'heading',
        level: 2,
        text: 'The "Yes, And Here Is What It Costs" Script',
        id: 'the-positive-boundary-script',
      },
      {
        type: 'text',
        content: 'You never need to argue with a client about scope. You simply need to treat every out-of-scope request as an opportunity to add billable revenue through a standardized Change Order.',
      },
      {
        type: 'callout',
        variant: 'example',
        title: 'The Professional Change Order Response',
        content: '"We’d love to build that additional feature for the campaign! That falls outside the initial Sprint 01 scope approved in our contract, but we can easily add it as an Addendum for ₹25,000 + GST or schedule it for Sprint 02. Let me know which option you prefer and I will issue the digital sign-off."',
      },
      {
        type: 'keyTakeaway',
        principle: 'Never Say No; Quantify the Cost',
        description: 'Saying "no" creates conflict. Saying "yes, and here is the timeline and budget adjustment" reinforces your professionalism and protects your agency margins.',
      },
      {
        type: 'heading',
        level: 2,
        text: 'Contract Safeguards Every Agency Needs',
        id: 'contract-safeguards',
      },
      {
        type: 'checklist',
        title: 'Airtight Agreement Clauses',
        items: [
          { label: 'Explicit Cap on Revision Rounds (e.g. 2 rounds per deliverable)', description: 'Additional rounds billed at standard hourly rate.', checked: true },
          { label: '7-Day Silence-as-Approval Clause', description: 'Deliverables auto-approved if client provides no written feedback within 7 business days.', checked: true },
          { label: 'Scope Boundary Exclusions List', description: 'Explicitly stating: Ad spend, stock asset licensing, and domain fees not included.', checked: true },
          { label: 'Tamper-Evident SHA-256 E-Sign Verification', description: 'Digital audit trail linking client IP and signature to exact scope version.', checked: true },
        ],
      },
      {
        type: 'newsletter',
        heading: 'Master agency cash flow and contracts.',
        tagline: 'Join 4,000+ agency operators receiving our weekly operational playbook.',
        buttonText: 'Subscribe Free',
        placement: 'inline',
      },
      {
        type: 'contextualCTA',
        badge: 'Legal Tech Module',
        title: 'Want legally enforceable, tamper-evident SHA-256 client agreements?',
        description: 'Explore Cora E-Sign Vault — generate contracts with automated GST math, revision gates, and 1-click client sign-offs.',
        ctaText: 'Explore Cora E-Sign Vault →',
        ctaHref: '/features/esign-vault/',
        destinationType: 'feature',
      },
    ],
  },
  {
    slug: 'client-reporting-system-for-agencies',
    title: 'The Weekly Client Reporting Framework That Cuts Retainer Churn in Half',
    dek: 'Clients do not cancel retainers because results dipped for one week. They cancel because they have no idea what your agency is actually doing. Here is the 3-section reporting ritual that proves ROI every Friday.',
    excerpt: 'The exact Friday client reporting framework used by high-retention agencies to showcase deliverables, prove commercial value, and prevent client cancellations.',
    coverImage: '/images/cora_pricing_pure_sky.jpg',
    coverAlt: 'Weekly Client Reporting Framework Banner',
    author: BLOG_AUTHORS['dravya-bansal'],
    publishedAt: '2026-09-15',
    updatedAt: '2026-09-23',
    category: 'growth',
    qualityLabel: 'Guide',
    tags: ['Client Retention', 'Account Management', 'Reporting Framework', 'Agency Growth'],
    readTime: '6 min read',
    featured: false,
    canonicalUrl: 'https://heycora.in/blog/client-reporting-system-for-agencies/',
    seoTitle: 'Weekly Agency Client Reporting Framework: Stop Retainer Churn (2026)',
    seoDescription: 'How to build high-impact weekly agency client reports. Proven 3-section framework to showcase momentum, eliminate client anxiety, and protect retainers.',
    sources: [
      {
        title: 'The Psychology of Agency Client Renewals',
        publisher: 'Journal of Agency Economics',
        url: 'https://heycora.in/blog/cora-research/',
        publishDate: '2026-04-12',
      },
    ],
    relatedSlugs: ['agency-client-onboarding-process', 'how-to-reduce-agency-scope-creep'],
    blocks: [
      {
        type: 'intro',
        content: 'When an agency client goes silent for 3 weeks before suddenly demanding a contract review call, the problem didn’t happen that morning. It started the moment your agency stopped proactively communicating momentum.',
      },
      {
        type: 'statement',
        statement: 'A client who understands what you are doing never asks why they are paying you.',
        subtext: 'Transparency is not an operational burden; it is your highest-leverage retention weapon.',
      },
      {
        type: 'text',
        content: 'Too many agencies send 40-page PDF dashboards filled with vanity metrics that executive decision-makers never read. What clients actually want is a 3-minute executive briefing answering three simple questions: What got done? What did we learn? What is happening next?',
      },
      {
        type: 'heading',
        level: 2,
        text: 'The 3-Section Friday Reporting System',
        id: 'the-three-section-reporting-system',
      },
      {
        type: 'steps',
        orientation: 'horizontal',
        steps: [
          {
            number: '01',
            title: 'Completed Milestones',
            description: 'Concrete assets shipped, campaigns launched, or code deployed this week.',
            badge: 'Proof of Work',
          },
          {
            number: '02',
            title: 'Key Commercial Metrics',
            description: 'Direct business impact: qualified leads, ROAS, conversions, or pipeline value.',
            badge: 'Commercial ROI',
          },
          {
            number: '03',
            title: 'Next Week Focus & Client Blockers',
            description: 'Upcoming deliverables and any asset approvals needed from the client.',
            badge: 'Forward Momentum',
          },
        ],
      },
      {
        type: 'callout',
        variant: 'cora-tip',
        title: 'The Friday 3:00 PM Delivery Ritual',
        content: 'Always send your weekly update by Friday afternoon before the client clocks out for the weekend. This ensures their week ends with a feeling of progress and zero Monday morning anxiety.',
      },
      {
        type: 'keyTakeaway',
        principle: 'Highlight Blockers Proactively',
        description: 'If you are waiting on the client for feedback or creative assets, flag it clearly in Section 03 as a dependency. This prevents project delays from being blamed on your team.',
      },
      {
        type: 'newsletter',
        heading: 'Build an agency that runs on systems.',
        tagline: 'One high-impact agency operating guide delivered to your inbox every week.',
        buttonText: 'Join the Brief',
        placement: 'end',
      },
    ],
  },
];

/* ====================================================================
 * QUERY & DATA ACCESS HELPERS
 * ==================================================================== */

export function getAllBlogArticles(): BlogArticle[] {
  return [...BLOG_ARTICLES].sort((a, b) => new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime());
}

export function getFeaturedBlogArticle(): BlogArticle {
  const featured = BLOG_ARTICLES.find((a) => a.featured);
  return featured || BLOG_ARTICLES[0];
}

export function getArticleBySlug(slug: string): BlogArticle | undefined {
  return BLOG_ARTICLES.find((a) => a.slug === slug);
}

export function getArticlesByCategory(categoryId: BlogCategoryId): BlogArticle[] {
  return BLOG_ARTICLES.filter((a) => a.category === categoryId).sort(
    (a, b) => new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime()
  );
}

export function getBlogCategoryById(id: string): BlogCategory | undefined {
  return BLOG_CATEGORIES.find((c) => c.id === id || c.slug === id);
}

export function getAllBlogCategories(): BlogCategory[] {
  return BLOG_CATEGORIES;
}

export function getAllBlogSlugs(): string[] {
  return BLOG_ARTICLES.map((a) => a.slug);
}

export function getRelatedArticles(currentSlug: string, category: BlogCategoryId, limit = 3): BlogArticle[] {
  const current = getArticleBySlug(currentSlug);
  const explicitRelated: BlogArticle[] = [];

  if (current?.relatedSlugs) {
    for (const rSlug of current.relatedSlugs) {
      const art = getArticleBySlug(rSlug);
      if (art && art.slug !== currentSlug) {
        explicitRelated.push(art);
      }
    }
  }

  if (explicitRelated.length >= limit) {
    return explicitRelated.slice(0, limit);
  }

  const categoryFallbacks = BLOG_ARTICLES.filter(
    (a) => a.category === category && a.slug !== currentSlug && !explicitRelated.some((er) => er.slug === a.slug)
  );

  return [...explicitRelated, ...categoryFallbacks].slice(0, limit);
}
