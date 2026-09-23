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
  content: string; // Semantic text supporting inline markdown (**bold**, *italic*, `code`, [links](url))
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
  breakout?: boolean;
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
    bio: 'Dravya is the founder and product architect of Cora. He works on agency workflows, contract systems, cash flow tooling, and client collaboration architectures for service businesses.',
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
    tagline: 'Practical operating workflows and handoff systems for service agencies',
    description: 'Breakdowns of client onboarding, scoping workflows, team coordination, delivery checklists, and operational leverage.',
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
    tagline: 'Autonomous AI agents, MCP tooling & streamlined service workflows',
    description: 'How modern agencies use generative agents, brief transcription, RAG memory, and automated CRM triage to save operational hours.',
    badge: 'AI Systems',
    iconName: 'Bot',
  },
  {
    id: 'growth',
    slug: 'growth',
    name: 'Sales & Growth',
    shortName: 'Growth',
    tagline: 'Retainer sales, pipeline management & client retention playbooks',
    description: 'Practical approaches to proposal design, outbound workflows, weekly reporting routines, and client retention for service firms.',
    badge: 'Revenue & Sales',
    iconName: 'TrendingUp',
  },
  {
    id: 'service-business',
    slug: 'service-business',
    name: 'Service Business',
    shortName: 'Service Biz',
    tagline: 'Pricing models, unit economics & scaling lessons for founders',
    description: 'First-principles breakdowns of agency pricing structures, team allocation, deliverable packaging, and business models.',
    badge: 'Business Model',
    iconName: 'Briefcase',
  },
  {
    id: 'finance-profitability',
    slug: 'finance-profitability',
    name: 'Finance & Profitability',
    shortName: 'Finance & GST',
    tagline: 'GST invoicing, cash flow structure, milestone payments & compliance',
    description: 'Clear financial playbooks for service agencies: automated CGST/SGST splitting, SAC codes, dynamic UPI QR payments, and margin protection.',
    badge: 'Finance Tech',
    iconName: 'Receipt',
  },
  {
    id: 'cora-research',
    slug: 'cora-research',
    name: 'Cora Research',
    shortName: 'Research',
    tagline: 'Operational frameworks, workflow data & research for service agencies',
    description: 'In-depth research and operational breakdowns exploring agency workflows, contract architecture, and service delivery systems.',
    badge: 'Research',
    iconName: 'BarChart2',
  },
];

/* ====================================================================
 * FLAGSHIP ARTICLES DATA
 * ==================================================================== */

export const BLOG_ARTICLES: BlogArticle[] = [
  {
    slug: 'agency-client-onboarding-process',
    title: 'The 5-Step Agency Client Onboarding System: How High-Performing Teams Turn New Deals into Retained Clients',
    dek: 'Most agencies do not have an onboarding problem. They have a coordination problem. Here is a practical 5-step framework to eliminate kickoff delays, set clear scope boundaries, and build lasting client trust.',
    excerpt: 'A practical 5-step agency client onboarding system to eliminate WhatsApp credential chaos, set clear milestone expectations, and get projects kicked off smoothly.',
    coverImage: '/images/cora_pricing_pure_sky.jpg',
    coverAlt: 'Cora Agency Client Onboarding Operating System Banner',
    author: BLOG_AUTHORS['dravya-bansal'],
    publishedAt: '2026-09-20',
    updatedAt: '2026-09-23',
    category: 'agency-operations',
    qualityLabel: 'Playbook',
    tags: ['Client Onboarding', 'Agency Operations', 'Standard Operating Procedures', 'Client Retention'],
    readTime: '8 min read',
    featured: true,
    canonicalUrl: 'https://heycora.in/blog/agency-client-onboarding-process/',
    seoTitle: 'Agency Client Onboarding Process: The 5-Step Operating System (2026)',
    seoDescription: 'The 5-step agency client onboarding framework. Eliminate coordination chaos, organize asset collection, and set up project delivery with clear expectations.',
    sources: [],
    relatedSlugs: ['how-to-reduce-agency-scope-creep', 'client-reporting-system-for-agencies'],
    faqs: [
      {
        question: 'How long should an agency onboarding process take?',
        answer: 'For standard creative and marketing retainers, the technical onboarding (contract sign-off, access delegation, deposit confirmation, and kickoff scheduling) should take no more than 48 to 72 hours from the verbal agreement.',
      },
      {
        question: 'What is the biggest mistake agencies make during client onboarding?',
        answer: 'Relying on scattered communication channels (like unrecorded WhatsApp messages or ad-hoc emails) rather than a single intake checklist where responsibilities and milestones are clear.',
      },
    ],
    blocks: [
      {
        type: 'intro',
        content: 'Most agencies do not have an onboarding problem. They have a **coordination problem**. Information lives in fragmented email threads, credentials arrive in WhatsApp voice notes, deliverables sit inside an unapproved proposal, and nobody is entirely sure when the clock on the project actually started.',
      },
      {
        type: 'statement',
        statement: 'More clients do not fix a broken operating system. They expose it.',
        subtext: 'When your agency grows from 3 to 15 active retainers, every informal shortcut creates unnecessary friction and delays for your team.',
      },
      {
        type: 'text',
        content: 'The first 72 hours of a new client relationship set the tone for the entire engagement. When onboarding feels disorganized, clients start worrying about missed deadlines and unexpected delays. When onboarding is **clear, structured, and fast**, clients gain immediate confidence in your team.',
      },
      {
        type: 'heading',
        level: 2,
        text: 'The Anatomy of Broken Agency Onboarding',
        id: 'the-anatomy-of-broken-onboarding',
      },
      {
        type: 'text',
        content: 'In most service firms, closing a new client triggers an uncoordinated scramble across multiple tools. Sales promises a timeline, account management opens a blank document, design requests brand files via email, and finance generates an unitemized invoice.',
      },
      {
        type: 'infographic',
        infographicId: 'onboarding-friction',
        headline: 'Where Client Onboarding Breaks Down: The Multi-Tool Handoff Gap',
        explanation: 'Fragmenting communication across email, chat, cloud drives, and proposal tools creates blind spots and handoff confusion right when client confidence matters most.',
        breakout: true,
      },
      {
        type: 'callout',
        variant: 'insight',
        title: 'The real cost of tool sprawl during kickoff',
        content: 'When a new client has to navigate DocuSign for contracts, Google Drive for assets, Slack for chats, and bank transfers for invoices, they spend their first week resolving logins instead of approving scopes.',
      },
      {
        type: 'heading',
        level: 2,
        text: 'The 5-Step Agency Onboarding Workflow',
        id: 'the-5-phase-operating-system',
      },
      {
        type: 'text',
        content: 'To eliminate kickoff delays, high-performing agencies replace reactive email threads with a predictable, 5-step sequence. Each step has one explicit owner, a single input, and a clear completion trigger.',
      },
      {
        type: 'steps',
        orientation: 'vertical',
        steps: [
          {
            number: '01',
            title: 'Agreement & Digital Sign-off',
            description: 'Issue a clear digital agreement outlining **scope boundaries**, payment milestone terms, and GST tax schedules.',
            badge: 'Within 2 Hours of Close',
          },
          {
            number: '02',
            title: 'Advance Invoice & Payment Confirmation',
            description: 'Issue the mobilization deposit invoice with itemized CGST/SGST breakdowns and 1-click dynamic UPI QR settlement.',
            badge: 'Deposit Confirmation',
          },
          {
            number: '03',
            title: 'Structured Asset & Access Intake',
            description: 'Provide a single checklist for brand guidelines, typography files, and Meta/Google ad account access delegation.',
            badge: 'No Scattered Passwords',
          },
          {
            number: '04',
            title: 'Structured Kickoff Briefing',
            description: 'Align on weekly review schedules, communication rules, and the agreed definition of done with key stakeholders.',
            badge: 'Within 48 Hours',
          },
          {
            number: '05',
            title: 'Milestone 01 Execution Dispatch',
            description: 'Assign internal tasks, notify specialists, and share the initial project roadmap with the client.',
            badge: 'Sprint 01 Launch',
          },
        ],
      },
      {
        type: 'keyTakeaway',
        principle: 'The Prime Onboarding Rule',
        description: 'Make the very next required action unmistakably obvious to both the client and your team. Never leave a client wondering "What happens next?"',
      },
      {
        type: 'heading',
        level: 2,
        text: 'Ad-hoc Onboarding vs. Structured Operating System',
        id: 'reactive-vs-structured-onboarding',
      },
      {
        type: 'comparison',
        title: 'Side-by-Side Operational Comparison',
        leftHeader: 'Ad-hoc Onboarding (Common Pitfalls)',
        rightHeader: 'Structured Onboarding (Cora OS)',
        rows: [
          {
            label: 'Contract Signing',
            left: 'Word Doc exported to PDF, emailed back and forth, and buried in inbox threads',
            right: '1-click digital sign-off with clear audit trail and stored copy',
          },
          {
            label: 'Asset Collection',
            left: 'Chasing logo files and vector assets across disjointed WhatsApp chats',
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
            right: 'Explicit milestone gates with pre-agreed revision allowances',
          },
          {
            label: 'Kickoff Duration',
            left: 'Multiple weeks from deal close to first deliverable',
            right: 'Under 48 hours from signature to initial sprint execution',
          },
        ],
      },
      {
        type: 'callout',
        variant: 'insight',
        title: 'Why chat-based credential sharing hurts accountability',
        content: 'When passwords, Ad Account IDs, and brief modifications are shared inside casual group chats, they are impossible to audit. Always route asset handoffs into a dedicated, organized intake checklist.',
      },
      {
        type: 'heading',
        level: 2,
        text: 'The Agency Kickoff Checklist',
        id: 'the-agency-kickoff-checklist',
      },
      {
        type: 'text',
        content: 'Before your creative or engineering team spends time on production, ensure your project lead has checked off these prerequisites:',
      },
      {
        type: 'checklist',
        title: 'Essential Kickoff Prerequisite Checklist',
        items: [
          { label: 'Digital Agreement Signed by Authorized Decision Maker', description: 'Includes scope boundaries, revision limits, and payment terms.', checked: true },
          { label: 'Mobilization Deposit Confirmed', description: 'Payment settled with official GST tax invoice issued.', checked: true },
          { label: 'Ad Account & Platform Access Granted', description: 'Meta Business Manager, Google MCC, or CMS partner access verified.', checked: true },
          { label: 'Brand Asset Package Uploaded', description: 'Vector logos, color codes, typography files, and brand guidelines.', checked: true },
          { label: 'Weekly Reporting Schedule Confirmed', description: 'Recurring Friday update schedule agreed upon with the client point of contact.', checked: true },
        ],
      },
      {
        type: 'newsletter',
        heading: 'Run your agency with fewer moving parts.',
        tagline: 'Get practical agency operating workflows and systems delivered to your inbox every week.',
        buttonText: 'Join Free Brief',
        placement: 'inline',
      },
      {
        type: 'heading',
        level: 2,
        text: 'How Unified Workspaces Simplify the Handoff',
        id: 'how-unified-workspaces-automate-handoffs',
      },
      {
        type: 'text',
        content: 'High-output agencies move faster because they replace manual hand-offs with a connected workspace where proposals turn directly into signed agreements, GST invoices, and active project task boards.',
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
        contextText: 'Instead of juggling multiple disconnected subscriptions to manage onboarding, agreements, and invoices:',
        actionText: 'See how Cora unifies client portals, digital agreements, and GST billing into one workspace →',
        actionHref: '/features/esign-vault/',
      },
      {
        type: 'leadMagnetCTA',
        title: 'The Agency Client Onboarding Standard Operating Procedure (SOP) Pack',
        description: 'Download our Notion template, email sequence scripts, and intake checklist designed for creative and technical service agencies.',
        assetId: 'sop-onboarding-pack',
        ctaText: 'Download SOP Pack (PDF & Notion)',
      },
    ],
  },
  {
    slug: 'how-to-reduce-agency-scope-creep',
    title: 'How Creative and Technical Agencies Prevent Scope Creep and Protect Margins',
    dek: 'Unbudgeted client requests do not happen by accident. They happen when agreements are vague and change orders are awkward. Here is a practical operational framework for setting firm boundaries while maintaining great client relationships.',
    excerpt: 'Learn how design, web, and marketing agencies prevent scope drift, structure milestone approvals, and protect their project margins.',
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
    seoDescription: 'A practical guide for agency founders on stopping scope creep, structuring milestone approvals, and converting out-of-scope requests into paid change orders.',
    sources: [],
    relatedSlugs: ['agency-client-onboarding-process', 'client-reporting-system-for-agencies'],
    blocks: [
      {
        type: 'intro',
        content: 'Scope creep rarely begins with a massive, unreasonable client demand. It begins with a polite sentence: *"Could you also quickly tweak this banner?"* or *"Can we just add one more landing page variant before launch?"*',
      },
      {
        type: 'statement',
        statement: 'Scope creep is not a client behavior problem. It is a contract architecture problem.',
        subtext: 'If your agreement does not define what is NOT included, everything is assumed to be included.',
      },
      {
        type: 'text',
        content: 'When an agency agrees to unbilled revisions to avoid an awkward conversation, project margins quietly erode. The team ends up overworked, delivery dates slip, and the client still feels frustrated because milestones were delayed.',
      },
      {
        type: 'heading',
        level: 2,
        text: 'The Most Common Areas Where Agency Scopes Leak',
        id: 'common-scope-leak-areas',
      },
      {
        type: 'dataChart',
        chartType: 'metric-grid',
        title: 'Core Friction Points in Scope Management',
        subtitle: 'Key operational areas where client expectations diverge from initial agreements',
        data: [
          { label: 'Uncapped Revision Cycles', value: 'Revisions', formattedValue: 'Revisions', sublabel: 'Lack of revision caps in initial agreement' },
          { label: 'Informal Brief Changes', value: 'Briefs', formattedValue: 'Brief Shifts', sublabel: 'Verbal scope changes requested without formal change orders' },
          { label: 'Delayed Feedback Loops', value: 'Feedback', formattedValue: 'Approvals', sublabel: 'Stalled reviews that push deadlines into rushed overtime' },
          { label: 'Extra Integrations', value: 'Scope', formattedValue: 'Add-ons', sublabel: 'Unplanned third-party tools and custom setups' },
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
        content: 'You never need to argue with a client about scope. You simply need to treat every out-of-scope request as an opportunity to add billable work through a standardized **Change Order**.',
      },
      {
        type: 'callout',
        variant: 'example',
        title: 'The Professional Change Order Response',
        content: '"We would love to build that additional feature for the campaign! That falls outside the initial scope approved in our agreement, but we can easily add it as a Change Order for ₹25,000 + GST or schedule it for the next sprint. Let me know which option you prefer."',
      },
      {
        type: 'keyTakeaway',
        principle: 'Never Say No; Quantify the Cost',
        description: 'Saying "no" creates friction. Saying "yes, and here is the timeline and budget adjustment" reinforces your professionalism and protects your project margins.',
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
          { label: 'Explicit Cap on Revision Rounds (e.g. 2 rounds per deliverable)', description: 'Additional rounds billed at an agreed hourly or sprint rate.', checked: true },
          { label: '7-Day Silence-as-Approval Clause', description: 'Deliverables auto-approved if client provides no written feedback within 7 business days.', checked: true },
          { label: 'Scope Boundary Exclusions List', description: 'Explicitly stating: Ad spend, stock asset licensing, and domain fees are billed separately.', checked: true },
          { label: 'Digital Sign-off & Audit Trail', description: 'Digital audit trail linking client signature and date to the exact scope version.', checked: true },
        ],
      },
      {
        type: 'newsletter',
        heading: 'Master agency cash flow and contracts.',
        tagline: 'Join agency operators receiving our weekly operational breakdown.',
        buttonText: 'Subscribe Free',
        placement: 'inline',
      },
      {
        type: 'contextualCTA',
        badge: 'Legal Tech Module',
        title: 'Need clean, structured digital agreements for clients?',
        description: 'Explore Cora E-Sign Vault — generate contracts with automated GST math, revision gates, and 1-click client sign-offs.',
        ctaText: 'Explore Cora E-Sign Vault →',
        ctaHref: '/features/esign-vault/',
        destinationType: 'feature',
      },
    ],
  },
  {
    slug: 'client-reporting-system-for-agencies',
    title: 'The Weekly Client Reporting Framework That Keeps Retainers Strong',
    dek: 'Clients do not cancel retainers because results dipped for one week. They cancel because they have no visibility into what your agency is doing. Here is the 3-section reporting ritual that demonstrates progress every Friday.',
    excerpt: 'A practical Friday client reporting framework used by high-retention agencies to showcase deliverables, prove commercial value, and prevent client cancellations.',
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
    seoTitle: 'Weekly Agency Client Reporting Framework: Retainer Retention (2026)',
    seoDescription: 'How to build high-impact weekly agency client reports. Proven 3-section framework to showcase momentum, eliminate ambiguity, and protect retainers.',
    sources: [],
    relatedSlugs: ['agency-client-onboarding-process', 'how-to-reduce-agency-scope-creep'],
    blocks: [
      {
        type: 'intro',
        content: 'When an agency client goes silent for three weeks before suddenly scheduling a contract review call, the issue did not begin that morning. It started the moment your agency stopped proactively communicating momentum.',
      },
      {
        type: 'statement',
        statement: 'A client who understands what you are doing never questions why they are paying you.',
        subtext: 'Clear communication is not an administrative chore; it is your most effective client retention tool.',
      },
      {
        type: 'text',
        content: 'Lengthy monthly dashboards filled with vanity metrics rarely get read by busy decision-makers. What clients actually value is a **concise weekly briefing** answering three core questions: What got finished? What did we learn? What is coming up next?',
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
            description: 'Concrete deliverables shipped, campaigns launched, or assets published this week.',
            badge: 'Proof of Work',
          },
          {
            number: '02',
            title: 'Key Commercial Metrics',
            description: 'Direct business outcomes: qualified leads, ROAS, conversions, or pipeline impact.',
            badge: 'Commercial Impact',
          },
          {
            number: '03',
            title: 'Next Week Focus & Client Approvals',
            description: 'Upcoming deliverables and any asset approvals or inputs needed from the client.',
            badge: 'Forward Momentum',
          },
        ],
      },
      {
        type: 'callout',
        variant: 'cora-tip',
        title: 'The Friday Afternoon Delivery Ritual',
        content: 'Send your weekly update by Friday afternoon before the client wraps up for the weekend. This ensures their week ends with a clear sense of progress and eliminates Monday morning uncertainty.',
      },
      {
        type: 'keyTakeaway',
        principle: 'Highlight Blockers Proactively',
        description: 'If you are waiting on the client for feedback or assets, flag it clearly as an upcoming dependency. This prevents project delays from being misunderstood.',
      },
      {
        type: 'newsletter',
        heading: 'Build an agency that runs on clear systems.',
        tagline: 'Practical operational workflows and client retention playbooks delivered to your inbox.',
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
