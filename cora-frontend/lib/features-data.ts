export interface FeatureStat {
  metric: string;
  label: string;
}

export interface FeaturePainPoint {
  problem: string;
  solution: string;
}

export interface FeatureCapability {
  title: string;
  description: string;
  tag: string;
}

export interface FeatureWorkflowStep {
  step: string;
  title: string;
  description: string;
}

export interface FeatureFaq {
  question: string;
  answer: string;
}

export interface FeatureMockupTab {
  id: string;
  label: string;
  badge?: string;
}

export interface FeatureMockupRow {
  col1: string;
  col2: string;
  col3: string;
  statusText: string;
  statusType: 'success' | 'warning' | 'neutral' | 'info';
  actionText: string;
}

export interface FeatureMockupData {
  windowTitle: string;
  activeTabLabel: string;
  tabs: FeatureMockupTab[];
  headerTitle: string;
  headerSubtitle: string;
  primaryActionLabel: string;
  metric1: { label: string; value: string };
  metric2: { label: string; value: string };
  metric3: { label: string; value: string };
  tableHeaders: [string, string, string, string, string];
  rows: FeatureMockupRow[];
}

export interface FeatureModule {
  slug: string;
  title: string;
  shortTitle: string;
  category: 'intelligence' | 'sales' | 'operations' | 'finance' | 'platform';
  categoryLabel: string;
  tagline: string;
  heroDescription: string;
  badgeColor: string;
  iconName: string;
  status: 'Live in Product' | 'Building Soon';
  tags: string[];
  industries?: string[];
  stats: FeatureStat[];
  theOldWay: string[];
  theCoraWay: string[];
  capabilities: FeatureCapability[];
  howItWorks: FeatureWorkflowStep[];
  toolsReplaced: {
    name: string;
    category: string;
    monthlySavingsUSD: number;
    monthlySavingsINR: number;
  }[];
  faqs: FeatureFaq[];
  relatedFeatureSlugs: string[];
  mockup: FeatureMockupData;
}

export interface RoadmapModule {
  id: string;
  title: string;
  desc: string;
  iconName: string;
  eta: string;
  status: string;
  categoryLabel: string;
  industries?: string[];
}

export interface IndustryItem {
  id: string;
  label: string;
  shortLabel: string;
  subtitle: string;
  iconName: string;
  badge: string;
}

export const INDUSTRIES: IndustryItem[] = [
  { 
    id: 'all', 
    label: 'All Agencies & Studios', 
    shortLabel: 'All', 
    subtitle: 'Universal Agency Operating System', 
    iconName: 'Briefcase',
    badge: 'Universal'
  },
  { 
    id: 'tech_software', 
    label: 'Software & Dev Agencies', 
    shortLabel: 'Tech & Dev', 
    subtitle: 'Web, Mobile Apps & SaaS Studios', 
    iconName: 'Code',
    badge: 'Sprints • Scopes'
  },
  { 
    id: 'marketing_growth', 
    label: 'Digital Marketing Agencies', 
    shortLabel: 'Marketing', 
    subtitle: 'Performance Media, SEO & Ads', 
    iconName: 'Sparkles',
    badge: 'Funnels • Retainers'
  },
  { 
    id: 'design_studios', 
    label: 'Design & UI/UX Studios', 
    shortLabel: 'UI/UX Design', 
    subtitle: 'Product Design, Branding & Web', 
    iconName: 'Palette',
    badge: 'Figma • Portals'
  },
  { 
    id: 'media_studios', 
    label: 'Media & Creative Production', 
    shortLabel: 'Media & Video', 
    subtitle: 'Commercial Film, Video & 4K', 
    iconName: 'Clapperboard',
    badge: 'Asset Vaults • 4K'
  },
  { 
    id: 'consulting_agencies', 
    label: 'Strategy & Consulting', 
    shortLabel: 'Consulting', 
    subtitle: 'Advisory, Audits & Tech Scopes', 
    iconName: 'Briefcase',
    badge: 'SOWs • Retainers'
  },
];

export const CATEGORIES = [
  { id: 'all', label: 'All (28)' },
  { id: 'intelligence', label: 'AI & Automation (4)' },
  { id: 'sales', label: 'Sales & CRM (4)' },
  { id: 'operations', label: 'Operations & Legal (4)' },
  { id: 'finance', label: 'Finance & Billing (4)' },
  { id: 'platform', label: 'Platform & Security (4)' },
  { id: 'roadmap', label: 'Roadmap (8)' }
];

export const BUILT_MODULES: FeatureModule[] = [
  {
    slug: 'ai-cofounder',
    title: 'Autonomous AI Co-Founder & Live Operations Chat',
    shortTitle: 'AI Co-Founder',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    tagline: 'Automate client onboarding, scoping SOWs, and daily agency operations triage.',
    heroDescription: 'Run your agency with an autonomous executive copilot that executes live database actions. Query client histories in plain English, automate stalled deal follow-ups, generate technical SOWs with 18% GST math in seconds, and eliminate 15+ hours of operational busywork every week.',
    badgeColor: 'emerald',
    iconName: 'Bot',
    status: 'Live in Product',
    tags: ['Multi-Turn Memory', 'Proactive Dialogue', 'Action Execution', '6-Tier Fallback Engine'],
    stats: [
      { metric: '10x Faster', label: 'Proposal Turnaround' },
      { metric: '24/7', label: 'Autonomous Operations' },
      { metric: '6 Tiers', label: 'Active LLM Fallback' }
    ],
    theOldWay: [
      'Losing billable hours copy-pasting client context across 5 disconnected ChatGPT tabs.',
      'Missing client follow-ups and leaking ₹50,000+ monthly in unbilled scope creep.',
      'Relying on dumb AI chatbots that cannot update CRM stages, check developer bandwidth, or generate invoices.'
    ],
    theCoraWay: [
      'Query live agency CRM deals, team sprint allocations, and rate cards with zero context switching.',
      'Trigger autonomous alerts when client deals stall or project deadlines risk team over-allocation.',
      'Execute complete business workflows in one click: generate SOWs, compute GST retainers, and dispatch WhatsApp briefs.'
    ],
    capabilities: [
      {
        title: 'Retain Unified Agency Context',
        description: 'Maintains deep multi-turn memory across client accounts, past proposals, tech deliverables, and agreed rate cards.',
        tag: 'Vector RAG Store'
      },
      {
        title: 'Execute Database Operations',
        description: 'Command Cora to draft SOWs, spin up sprint tasks, or adjust pipeline deal values using natural language function calling.',
        tag: 'Function Calling'
      },
      {
        title: 'Guarantee 99.9% Uptime with 6-Tier Fallback',
        description: 'Route complex prompts through Claude 3.5 Sonnet, Gemini 3.5 Flash, and GPT-4o with sub-100ms automatic failover.',
        tag: 'High Availability'
      },
      {
        title: 'Generate High-Converting SOWs',
        description: 'Draft comprehensive technical proposals, milestone schedules, and legal terms customized to client budget tiers.',
        tag: 'Project Scopes'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Prompt Your Command',
        description: 'Type or speak your operational request directly in the persistent command bar or mobile workspace drawer.'
      },
      {
        step: '02',
        title: 'Analyze Live Workspace Data',
        description: 'Cora instantly cross-references CRM pipelines, developer calendars, and agency rate cards to formulate the exact action.'
      },
      {
        step: '03',
        title: 'Execute & Dispatch Instantly',
        description: 'Review the auto-generated deliverable and trigger instant client delivery via WhatsApp, Email, or CRM stage updates.'
      }
    ],
    toolsReplaced: [
      { name: 'ChatGPT Plus', category: 'AI Copy & Ideas', monthlySavingsUSD: 20, monthlySavingsINR: 1999 },
      { name: 'Jasper / Copy.ai', category: 'Proposal Copy', monthlySavingsUSD: 49, monthlySavingsINR: 4200 },
      { name: 'Custom Zapier Workflows', category: 'Automation Scripts', monthlySavingsUSD: 30, monthlySavingsINR: 2500 }
    ],
    faqs: [
      {
        question: 'How does the AI Co-Founder access my agency CRM and billing data?',
        answer: 'Cora queries your workspace database using secure, tenant-isolated Model Context Protocol (MCP) tools. Your confidential client files and financial ledgers never leave your private workspace.'
      },
      {
        question: 'Can Cora execute actions without my explicit approval?',
        answer: 'Never. Destructive or external actions—such as sending formal contracts or dispatching payment links—always require your one-click confirmation.'
      },
      {
        question: 'What happens when an LLM provider experiences downtime?',
        answer: 'Cora’s autonomous 6-tier fallback engine reroutes execution to alternate LLM providers within 100ms, ensuring uninterrupted agency operations.'
      },
      {
        question: 'Can I dictate operational tasks hands-free while traveling?',
        answer: 'Yes. Built-in Whisper voice recognition transcribes complex technical briefs on mobile PWA and converts spoken notes into structured tasks.'
      }
    ],
    relatedFeatureSlugs: ['rag-mcp', 'voice-to-scope', 'lead-crm'],
    mockup: {
      windowTitle: 'Cora AI Co-Founder — Agency Intelligence Terminal',
      activeTabLabel: 'Operations Copilot',
      tabs: [
        { id: 'chat', label: 'Live Copilot', badge: 'Active' },
        { id: 'proposals', label: 'Proposal Scopes' },
        { id: 'automations', label: 'Active Workflows' }
      ],
      headerTitle: 'Autonomous Agency Intelligence',
      headerSubtitle: 'Connected to CRM, Sprint Calendar & 18% GST Invoicing engine',
      primaryActionLabel: '+ New Command',
      metric1: { label: 'Active AI Runs', value: '4,280 / 20k' },
      metric2: { label: 'Avg Latency', value: '380ms' },
      metric3: { label: 'Actions Executed', value: '142 this wk' },
      tableHeaders: ['Command / Prompt', 'Action Triggered', 'Target Entity', 'Status', 'Execution'],
      rows: [
        {
          col1: '"Draft full-stack web app proposal for Horizon Media"',
          col2: 'Create CRM Deal & Proposal PDF',
          col3: 'Horizon Media (₹3,80,000)',
          statusText: 'Completed',
          statusType: 'success',
          actionText: 'View Proposal'
        },
        {
          col1: '"Check developer availability for next week design sprint"',
          col2: 'Team Allocation & Conflict Guard',
          col3: 'Dev Team Alpha (3 Members)',
          statusText: 'No Conflicts',
          statusType: 'info',
          actionText: 'View Schedule'
        },
        {
          col1: '"Send overdue retainer reminder with UPI QR"',
          col2: 'GST Invoicing Alert Dispatch',
          col3: 'Invoice #CORA-2026-089',
          statusText: 'Dispatched',
          statusType: 'success',
          actionText: 'View Receipt'
        }
      ]
    }
  },
  {
    slug: 'content-ai',
    title: 'Content AI Suite & Editorial Publisher',
    shortTitle: 'Content AI & SEO',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    tagline: 'Turn completed client projects into high-ranking SEO case studies and inbound pipeline.',
    heroDescription: 'Dominate search engine rankings without hiring expensive content agencies. Transform your engineering logs, Figma deliverables, and client KPIs into deeply technical case studies and GEO-targeted authority guides, then push them to Google within minutes via instant IndexNow protocols.',
    badgeColor: 'amber',
    iconName: 'Sparkles',
    status: 'Live in Product',
    tags: ['Quill WYSIWYG Editor', 'GEO-Targeted SEO', 'IndexNow Auto-Ping', '7-Tab Lifecycle'],
    stats: [
      { metric: '5x Higher', label: 'Google Search Traffic' },
      { metric: '< 60 Sec', label: 'Case Study Draft' },
      { metric: '100% Valid', label: 'Schema.org JSON-LD' }
    ],
    theOldWay: [
      'Wasting 8+ hours manually writing portfolio case studies or paying ₹40,000+ to external copywriters.',
      'Waiting 3 to 6 weeks for search engine crawlers to discover and index newly published agency work.',
      'Losing high-intent local inbound leads to competitors ranking for commercial city-specific keywords.'
    ],
    theCoraWay: [
      'Generate publication-ready technical case studies from project specs and metrics in under 60 seconds.',
      'Notify Google, Bing, and Yandex instantly via one-click IndexNow API pings upon publishing.',
      'Inject hyper-local GEO schemas (e.g. Bandra, Indiranagar, Cyber Hub) to dominate commercial search intent.'
    ],
    capabilities: [
      {
        title: 'Author in Typographic WYSIWYG',
        description: 'Draft and format client stories with clear heading hierarchies, syntax-highlighted code blocks, and rich media callouts.',
        tag: 'Editorial Suite'
      },
      {
        title: 'Capture Hyper-Local GEO Search',
        description: 'Target profitable commercial zones with automated schema markup, local landmarks, and localized semantic keywords.',
        tag: 'Hyper-Local SEO'
      },
      {
        title: 'Index Content in Real Time',
        description: 'Ping search engine crawlers via the IndexNow protocol the moment you hit publish to slash discovery time from weeks to minutes.',
        tag: 'Instant Discovery'
      },
      {
        title: 'Generate Branded Social Previews',
        description: 'Produce high-contrast OpenGraph summary cards optimized for Twitter/X, LinkedIn, and WhatsApp client sharing.',
        tag: 'Social Previews'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Select Project Deliverables',
        description: 'Pick a completed client project or enter your core topic (e.g. "Migrating Legacy Systems to Next.js 15 App Router").'
      },
      {
        step: '02',
        title: 'Generate Technical Narrative',
        description: 'Cora drafts an executive summary, architectural breakdown, performance benchmarks, and client ROI metrics.'
      },
      {
        step: '03',
        title: 'Publish & Index Immediately',
        description: 'Polish copy in the rich WYSIWYG editor and hit publish to broadcast live updates to major search engine bots.'
      }
    ],
    toolsReplaced: [
      { name: 'SurferSEO', category: 'SEO Content Editor', monthlySavingsUSD: 89, monthlySavingsINR: 7500 },
      { name: 'Medium Publication Pro', category: 'Publishing CMS', monthlySavingsUSD: 15, monthlySavingsINR: 1250 },
      { name: 'RankMath / Yoast Pro', category: 'WordPress Plugins', monthlySavingsUSD: 12, monthlySavingsINR: 999 }
    ],
    faqs: [
      {
        question: 'Does the generated copy read like generic AI or seasoned agency engineers?',
        answer: 'Cora uses specialized prompts calibrated for senior engineering, UI/UX architecture, and conversion marketing syntax, eliminating generic AI filler.'
      },
      {
        question: 'How does IndexNow help my agency outrank competitors?',
        answer: 'IndexNow directly notifies search engines the second you publish, forcing bots to crawl and rank your new case studies ahead of slower competitors.'
      },
      {
        question: 'Can I embed live code snippets, metrics, and architecture diagrams?',
        answer: 'Yes. Embed syntax-highlighted code, system architecture diagrams, and verified performance metrics directly into your editorial layouts.'
      },
      {
        question: 'Does the publisher output clean, accessible semantic HTML?',
        answer: 'Yes. All published articles generate W3C-compliant semantic HTML with automated H1–H4 hierarchies and valid JSON-LD schema markup.'
      }
    ],
    relatedFeatureSlugs: ['ai-cofounder', 'canvas-builder', 'docs-portal'],
    mockup: {
      windowTitle: 'Cora Content AI & SEO Studio',
      activeTabLabel: 'Case Study: SaaS Onboarding & UX Architecture',
      tabs: [
        { id: 'editor', label: 'WYSIWYG Draft', badge: 'SEO 98/100' },
        { id: 'geo', label: 'GEO Meta & Schema' },
        { id: 'indexnow', label: 'IndexNow Logs' }
      ],
      headerTitle: 'Engineering Case Study: Scaling Next.js 15 to 1M Daily Users',
      headerSubtitle: 'Target Keywords: "Full stack dev agency Mumbai", "UI/UX product design Bangalore"',
      primaryActionLabel: 'Publish & Index',
      metric1: { label: 'SEO Score', value: '98 / 100' },
      metric2: { label: 'Reading Time', value: '4 mins' },
      metric3: { label: 'Index Status', value: 'Ready to Ping' },
      tableHeaders: ['Keyword / Entity', 'Density', 'Placement', 'Status', 'Search Impact'],
      rows: [
        {
          col1: 'SaaS Development Agency',
          col2: '2.4% (8x)',
          col3: 'H1, Meta Title, 1st Para',
          statusText: 'Optimized',
          statusType: 'success',
          actionText: 'Audit'
        },
        {
          col1: 'Next.js App Router Architecture',
          col2: '1.8% (5x)',
          col3: 'H2, Tech Specs Block',
          statusText: 'Optimized',
          statusType: 'success',
          actionText: 'Audit'
        },
        {
          col1: 'Product Design Studio Indiranagar',
          col2: '1.2% (3x)',
          col3: 'Footer Schema, Body Text',
          statusText: 'Optimized',
          statusType: 'success',
          actionText: 'Audit'
        }
      ]
    }
  },
  {
    slug: 'rag-mcp',
    title: 'Model Context Protocol (MCP) & Self-Learning RAG Memory',
    shortTitle: 'RAG Memory MCP',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    tagline: 'Ground every AI response in your agency rate cards, past proposals, and tech SOPs.',
    heroDescription: 'Eliminate hallucinated quotes and generic boilerplate. Cora transforms your rate cards, Master Services Agreements, design guidelines, and engineering playbooks into a private vector knowledge brain. Query your entire agency IP via open Model Context Protocol (MCP) standards with zero data leakage.',
    badgeColor: 'purple',
    iconName: 'BrainCircuit',
    status: 'Live in Product',
    tags: ['Model Context Protocol (MCP)', 'Vector Embeddings', 'Living Knowledge Base', 'Self-Learning Context'],
    stats: [
      { metric: '100% Private', label: 'Tenant Isolation' },
      { metric: 'Sub-80ms', label: 'Vector Retrieval' },
      { metric: 'Zero Leakage', label: 'No Public Training' }
    ],
    theOldWay: [
      'Re-typing your agency rate cards, milestone terms, and tech stacks into ChatGPT every single morning.',
      'Sending inaccurate proposals because junior team members referenced outdated pricing spreadsheets.',
      'Risking proprietary client code and confidential financials to public LLM training datasets.'
    ],
    theCoraWay: [
      'Maintain a private vector memory store indexing your exact rate cards, SOW templates, and tech guidelines.',
      'Empower AI agents to query live agency documents safely through open Model Context Protocol (MCP) tools.',
      'Enforce enterprise tenant isolation so your proprietary commercial data never trains public AI models.'
    ],
    capabilities: [
      {
        title: 'Connect Open MCP Tool Servers',
        description: 'Leverage the open Model Context Protocol to let AI agents safely inspect agency databases and trigger workspace actions.',
        tag: 'MCP Standard'
      },
      {
        title: 'Ingest Knowledge in Any Format',
        description: 'Upload PDF rate cards, Figma specs, DOCX agreements, and Markdown SOPs for instantaneous vector segmentation.',
        tag: 'Vector Search'
      },
      {
        title: 'Ground Quotes in Actual Rates',
        description: 'Ensure every generated proposal accurately reflects your current minimum engagement fees, tech stacks, and GST rules.',
        tag: 'Accurate Data'
      },
      {
        title: 'Enforce Zero-Retention Privacy',
        description: 'Process all context embeddings through enterprise endpoints backed by strict zero-retention and zero-training guarantees.',
        tag: 'Data Sovereignty'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Upload Agency Documents',
        description: 'Drop your pricing sheets, standard master contracts, tech stack guides, and design guidelines into the knowledge vault.'
      },
      {
        step: '02',
        title: 'Vectorize & Index Automatically',
        description: 'Cora parses, chunks, and indexes your proprietary documents into high-dimensional embeddings within milliseconds.'
      },
      {
        step: '03',
        title: 'Deploy Context-Aware Intelligence',
        description: 'The AI Co-Founder retrieves exact clauses and rate structures whenever you scope projects or answer client RFPs.'
      }
    ],
    toolsReplaced: [
      { name: 'Custom Pinecone / Weaviate', category: 'Vector Database', monthlySavingsUSD: 70, monthlySavingsINR: 5800 },
      { name: 'LangChain / LlamaIndex Cloud', category: 'RAG Frameworks', monthlySavingsUSD: 50, monthlySavingsINR: 4200 },
      { name: 'Notion AI Addon', category: 'Knowledge AI', monthlySavingsUSD: 10, monthlySavingsINR: 850 }
    ],
    faqs: [
      {
        question: 'Do OpenAI, Anthropic, or Google train their models on our uploaded agency data?',
        answer: 'Never. All vector retrieval and LLM queries execute through enterprise API endpoints protected by strict zero-data-retention agreements.'
      },
      {
        question: 'What file formats can I index into the agency vector brain?',
        answer: 'Upload PDF rate sheets, DOCX legal agreements, Markdown engineering guides, TXT notes, and CSV pricing tables.'
      },
      {
        question: 'How fast does vector context retrieval run during live chat?',
        answer: 'Semantic vector retrieval completes in under 80ms, delivering grounded context without slowing down AI responses.'
      },
      {
        question: 'Can I plug custom external MCP servers into Cora?',
        answer: 'Yes. Cora natively supports the Model Context Protocol, allowing you to connect custom internal APIs and external developer tools.'
      }
    ],
    relatedFeatureSlugs: ['ai-cofounder', 'voice-to-scope', 'docs-portal'],
    mockup: {
      windowTitle: 'Cora RAG Memory & MCP Knowledge Base',
      activeTabLabel: 'Agency Knowledge Vector Index',
      tabs: [
        { id: 'docs', label: 'Vector Knowledge Store', badge: '14 Indexed' },
        { id: 'mcp-tools', label: 'Active MCP Tools', badge: '9 Tools' },
        { id: 'query-test', label: 'Semantic Playground' }
      ],
      headerTitle: 'Agency Memory & Model Context Protocol (MCP)',
      headerSubtitle: 'Active Vector Store: 2,480 Chunks • Embedding Engine: text-embedding-3-small',
      primaryActionLabel: '+ Ingest Document',
      metric1: { label: 'Indexed Chunks', value: '2,480 Chunks' },
      metric2: { label: 'Search Latency', value: '48ms' },
      metric3: { label: 'MCP Tools', value: '9 Active' },
      tableHeaders: ['Document Title', 'Category', 'Chunks', 'Last Vectorized', 'Status'],
      rows: [
        {
          col1: 'Agency Rate Card & Retainer Pricing 2026.pdf',
          col2: 'Pricing & Retainers',
          col3: '142 Chunks',
          statusText: 'Indexed & Ready',
          statusType: 'success',
          actionText: 'Inspect'
        },
        {
          col1: 'Standard Digital Agency Master Services Agreement.docx',
          col2: 'Legal & SOW Terms',
          col3: '88 Chunks',
          statusText: 'Indexed & Ready',
          statusType: 'success',
          actionText: 'Inspect'
        },
        {
          col1: 'Full-Stack Architecture & Security Standards.md',
          col2: 'Engineering SOPs',
          col3: '320 Chunks',
          statusText: 'Indexed & Ready',
          statusType: 'success',
          actionText: 'Inspect'
        }
      ]
    }
  },
  {
    slug: 'voice-to-scope',
    title: 'Voice-to-Scope Autonomous Audio Scoping Engine',
    shortTitle: 'Voice-to-Scope',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    tagline: 'Convert 60-second voice memos into signed client scopes and commercial proposals.',
    heroDescription: 'Capture project briefs immediately after client discovery calls or while on the go. Dictate deliverables, budgets, and milestones into your phone—Cora transcribes the audio, extracts tech requirements, calculates pricing with 18% GST, and builds a client-ready proposal before you get back to your desk.',
    badgeColor: 'sky',
    iconName: 'Zap',
    status: 'Live in Product',
    tags: ['Hands-Free Voice Input', 'Audio Transcription', 'Structured Scope Parser', 'Instant Proposal Draft'],
    stats: [
      { metric: '< 5 Sec', label: 'Audio to Proposal' },
      { metric: '99.4%', label: 'Indian English Accuracy' },
      { metric: '0 Typing', label: 'Hands-Free Scoping' }
    ],
    theOldWay: [
      'Losing high-value client requirements scribbled on paper napkins or buried in messy WhatsApp voice notes.',
      'Wasting 2+ hours after every discovery call manually typing technical deliverables and milestone tables.',
      'Suffering costly scope creep caused by vague verbal agreements that were never formalized in writing.'
    ],
    theCoraWay: [
      'Record a 45-second voice memo: "Next.js redesign for Apex, 3 milestones, ₹4.5L budget, launch in 6 weeks.".',
      'Extract client names, deliverables, tech stacks, and milestone budgets automatically with 99.4% accuracy.',
      'Generate a complete commercial proposal and SHA-256 e-sign contract ready for one-tap client dispatch.'
    ],
    capabilities: [
      {
        title: 'Transcribe Speech with Whisper AI',
        description: 'Capture spoken requirements with high-accuracy speech-to-text tuned for technical jargon and regional accents.',
        tag: 'Accurate Voice'
      },
      {
        title: 'Extract Deliverables & Budgets',
        description: 'Automatically parse client names, currency figures, tech frameworks, milestone deadlines, and revision limits.',
        tag: 'Entity Parser'
      },
      {
        title: 'Synthesize Client-Ready Proposals',
        description: 'Convert raw audio transcriptions into structured proposals formatted with your agency rate cards and GST calculations.',
        tag: 'Auto-Formatting'
      },
      {
        title: 'Record Hands-Free on Mobile PWA',
        description: 'Tap the microphone button directly inside your mobile PWA for instant audio capture with live waveform visualizers.',
        tag: 'PWA Native'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Record Your Spoken Brief',
        description: 'Tap the microphone icon on your mobile PWA and dictate the project details, timeline, and budget constraints.'
      },
      {
        step: '02',
        title: 'Extract Milestones & Tech Stack',
        description: 'Cora transcribes the audio, extracts deliverables, calculates milestone pricing, and checks team schedule capacity.'
      },
      {
        step: '03',
        title: 'Review & Dispatch Proposal',
        description: 'Inspect the generated proposal card and dispatch it directly to your client via WhatsApp, Email, or CRM link in one tap.'
      }
    ],
    toolsReplaced: [
      { name: 'Otter.ai Business', category: 'Voice Transcription', monthlySavingsUSD: 20, monthlySavingsINR: 1700 },
      { name: 'Descript Creator', category: 'Audio Processing', monthlySavingsUSD: 24, monthlySavingsINR: 2000 },
      { name: 'Fireflies.ai', category: 'Meeting Notes', monthlySavingsUSD: 18, monthlySavingsINR: 1500 }
    ],
    faqs: [
      {
        question: 'Does Voice-to-Scope recognize specialized software and design terminology?',
        answer: 'Yes. The acoustic dictionary is optimized for modern tech and design terminology like Next.js, Figma tokens, GraphQL, and AWS Lambda.'
      },
      {
        question: 'Can I dictate voice notes in noisy environments or moving vehicles?',
        answer: 'Yes. Integrated noise-filtering algorithms isolate voice frequencies and suppress background chatter and traffic noise.'
      },
      {
        question: 'What accents and language styles does the engine understand?',
        answer: 'The engine supports Indian English, US/UK English, Hinglish colloquialisms, and global tech accents with 99.4% accuracy.'
      },
      {
        question: 'Can I edit and customize the extracted proposal before sending?',
        answer: 'Yes. You can edit line items, adjust milestone payment percentages, add custom legal clauses, or recalculate taxes before dispatching.'
      }
    ],
    relatedFeatureSlugs: ['ai-cofounder', 'lead-crm', 'esign-vault'],
    mockup: {
      windowTitle: 'Cora Voice-to-Scope — Audio Proposal Synthesizer',
      activeTabLabel: 'Voice Note #V2S-2026-089',
      tabs: [
        { id: 'record', label: 'Audio Waveform', badge: 'Transcribed' },
        { id: 'extracted', label: 'Parsed Deliverables' },
        { id: 'generated-pdf', label: 'Draft Proposal' }
      ],
      headerTitle: 'Voice Audio Scoping & Brief Synthesis',
      headerSubtitle: 'Audio Input: 00:38s • Quality: High-Def • Confidence: 99.4%',
      primaryActionLabel: '+ Generate Proposal PDF',
      metric1: { label: 'Parse Accuracy', value: '99.4%' },
      metric2: { label: 'Time Saved', value: '45 Mins' },
      metric3: { label: 'Extracted Items', value: '6 Line Items' },
      tableHeaders: ['Detected Entity', 'Extracted Value', 'Confidence', 'CRM Mapping', 'Status'],
      rows: [
        {
          col1: 'Client & Brand',
          col2: 'Raymond Luxury Apparel Ltd',
          col3: '100% Match',
          statusText: 'Matched Existing Client',
          statusType: 'success',
          actionText: 'Inspect'
        },
        {
          col1: 'Project Scope & Tier',
          col2: 'SaaS Platform Redesign + Design System',
          col3: '98.8%',
          statusText: 'Mapped to Enterprise Tier',
          statusType: 'success',
          actionText: 'Inspect'
        },
        {
          col1: 'Proposed Budget',
          col2: '₹4,50,000 + 18% GST',
          col3: '100%',
          statusText: 'Calculated (₹5,31,000 Total)',
          statusType: 'success',
          actionText: 'Inspect'
        }
      ]
    }
  },
  {
    slug: 'lead-crm',
    title: 'Lead Management CRM & Visual Kanban Pipeline',
    shortTitle: 'Kanban Lead CRM',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    tagline: 'Capture high-value agency inquiries, accelerate deal velocity, and forecast retainer revenue in real time.',
    heroDescription: 'Eliminate lead leaks and close high-ticket retainers faster. Convert inbound prospects across web forms, ads, and WhatsApp into structured Kanban deal stages with instant sliding drawer scoping, one-click contract dispatch, and live quarterly revenue forecasting.',
    badgeColor: 'blue',
    iconName: 'Kanban',
    status: 'Live in Product',
    tags: ['Kanban Pipeline', 'Revenue Forecast', 'Sliding Deal Drawer', 'Omnichannel Outreach'],
    stats: [
      { metric: '3.4x', label: 'Lead Conversion Rate' },
      { metric: '0 Min', label: 'Manual Data Entry' },
      { metric: '100%', label: 'Mobile PWA Responsive' }
    ],
    theOldWay: [
      'Leads rotting in cluttered inboxes and chaotic WhatsApp chats while competitors close your prospects.',
      'Burning $500+/month on bloated enterprise CRMs packed with unused corporate features.',
      'Wasting hours on manual spreadsheet math to estimate monthly retainer cashflow and project deposits.'
    ],
    theCoraWay: [
      'Route inbound leads automatically into customizable agency stages: Discovery → Scoping → E-Sign → Retainer Paid → In Sprint → Delivered.',
      'Inspect deal context, log call notes, and review quote history instantly using zero-latency sliding drawers.',
      'Forecast quarterly agency revenue automatically based on deal probability and milestone completion dates.'
    ],
    capabilities: [
      {
        title: 'Accelerate Deals with Visual Kanban Stages',
        description: 'Drag and drop deals across tailored agency milestones. Trigger automated client status updates via email and WhatsApp on stage transition.',
        tag: 'Pipeline Velocity'
      },
      {
        title: 'Inspect Prospects via Instant Sliding Drawers',
        description: 'Review client requirements, proposal history, and communications in zero-latency side sheets without losing board context.',
        tag: 'Zero Layout Shift'
      },
      {
        title: 'Forecast Retainer Cashflow and GST In Real Time',
        description: 'Calculate weighted pipeline value, pending GST collections, and incoming quarterly cashflow automatically as deals advance.',
        tag: 'Revenue Foresight'
      },
      {
        title: 'Capture Inbound Inquiries Across All Channels',
        description: 'Ingest leads automatically from website forms, landing page funnels, social ad webhooks, and direct WhatsApp chats.',
        tag: 'Omnichannel Ingestion'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Capture Inbound Opportunities',
        description: 'Ingest lead inquiries automatically from website forms, landing pages, or direct links into your New Inquiries column.'
      },
      {
        step: '02',
        title: 'Scope Packages and Generate Quotes',
        description: 'Open the sliding deal drawer to configure service line-items or invoke AI Co-Founder to build accurate project estimates in seconds.'
      },
      {
        step: '03',
        title: 'Seal Contracts and Collect Retainers',
        description: 'Advance the deal card to dispatch SHA-256 sealed digital contracts and instant deposit payment links.'
      }
    ],
    toolsReplaced: [
      { name: 'HubSpot Starter', category: 'Sales Pipeline', monthlySavingsUSD: 50, monthlySavingsINR: 4200 },
      { name: 'Pipedrive', category: 'Deal Tracking', monthlySavingsUSD: 39, monthlySavingsINR: 3200 },
      { name: 'Trello Premium', category: 'Kanban Boards', monthlySavingsUSD: 10, monthlySavingsINR: 850 }
    ],
    faqs: [
      {
        question: 'Can I customize pipeline stages for my specific agency services?',
        answer: 'Yes. Customize, reorder, and add stages to match your exact delivery model whether you run a dev shop, UI/UX studio, or growth agency.'
      },
      {
        question: 'Can I communicate with prospects directly via WhatsApp?',
        answer: 'Yes. Launch one-click WhatsApp chats with pre-filled milestone templates, meeting confirmations, and retainer payment reminders.'
      },
      {
        question: 'Can I restrict financial deal values from junior team members?',
        answer: 'Yes. Enforce granular role-based access controls (RBAC) to hide deal values and retainer financials while keeping task deadlines visible.'
      },
      {
        question: 'Can I export pipeline data for accounting and sales reviews?',
        answer: 'Yes. Export filtered pipeline metrics and deal ledgers directly into CSV, Excel, or CA-ready spreadsheets in one click.'
      }
    ],
    relatedFeatureSlugs: ['canvas-builder', 'form-builder', 'esign-vault'],
    mockup: {
      windowTitle: 'Cora Lead CRM — Visual Agency Pipeline',
      activeTabLabel: 'Active Pipeline (₹18.4L Value)',
      tabs: [
        { id: 'pipeline', label: 'Kanban Board', badge: '18 Deals' },
        { id: 'list', label: 'Table View' },
        { id: 'forecast', label: 'Revenue Forecast' }
      ],
      headerTitle: 'Agency Client & Retainer Pipeline',
      headerSubtitle: 'Q3 Active Pipeline: ₹18,40,000 across 6 active stages',
      primaryActionLabel: '+ New Client Deal',
      metric1: { label: 'Pipeline Value', value: '₹18,40,000' },
      metric2: { label: 'Deals In Scope', value: '8 Active' },
      metric3: { label: 'Win Rate', value: '68.4%' },
      tableHeaders: ['Client / Project', 'Stage', 'Deal Value', 'Expected Date', 'Actions'],
      rows: [
        {
          col1: 'Vogue India — Brand Campaign & Micro-Site',
          col2: 'Scope Approved',
          col3: '₹3,50,000',
          statusText: 'E-Sign Pending',
          statusType: 'warning',
          actionText: 'Open Drawer'
        },
        {
          col1: 'Mercedes Benz — Interactive Configurator Web App',
          col2: 'Retainer Received',
          col3: '₹5,20,000',
          statusText: 'In Sprint',
          statusType: 'success',
          actionText: 'View Sprint'
        },
        {
          col1: 'Zomato HQ — Growth Marketing Funnel',
          col2: 'Inquiry Review',
          col3: '₹2,80,000',
          statusText: 'New Lead',
          statusType: 'info',
          actionText: 'Draft Quote'
        }
      ]
    }
  },
  {
    slug: 'canvas-builder',
    title: 'Visual Canvas & High-Converting Funnel Builder',
    shortTitle: 'Funnel Builder',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    tagline: 'Launch high-converting agency landing pages and interactive pitch decks in minutes with zero code.',
    heroDescription: 'Turn traffic into high-paying retainers. Build lightning-fast agency landing pages, interactive project pitch decks, and client intake funnels with responsive multi-device previews, instant custom domain routing, and direct CRM data synchronization.',
    badgeColor: 'violet',
    iconName: 'LayoutTemplate',
    status: 'Live in Product',
    tags: ['Drag & Drop Canvas', 'Device Preview', 'Git Auto-Sync', 'High Conversion Rate'],
    stats: [
      { metric: 'Sub-400ms', label: 'Page Load Speed' },
      { metric: '100% SEO', label: 'Lighthouse Score' },
      { metric: 'Zero Code', label: 'Visual Customization' }
    ],
    theOldWay: [
      'Paying recurring fees to third-party page builders completely disconnected from your sales pipeline.',
      'Manually copy-pasting form submissions from disconnected landing pages into your project management tools.',
      'Losing high-intent mobile visitors due to sluggish load times and poor responsive rendering.'
    ],
    theCoraWay: [
      'Build and deploy agency funnels natively integrated with your CRM database, retainer tiers, and booking calendars.',
      'Deliver sub-50ms screen painting using pure monochromatic atomic components engineered for high conversion.',
      'Publish instantly to custom agency domains with zero-touch SSL provisioning and global edge CDN caching.'
    ],
    capabilities: [
      {
        title: 'Assemble Pre-Engineered Agency Sections',
        description: 'Stack conversion-optimized hero sections, case study reels, dynamic pricing matrices, client testimonials, and intake forms.',
        tag: 'Modular Architecture'
      },
      {
        title: 'Preview Responsive Layouts in Real Time',
        description: 'Inspect designs across iPhone 16 Pro, iPad Air, and 4K desktop viewports simultaneously to ensure flawless responsiveness.',
        tag: 'Multi-Device Emulation'
      },
      {
        title: 'Sync Lead Submissions Directly to Pipeline',
        description: 'Route every funnel submission straight into your CRM Kanban board without brittle third-party webhooks or Zapier delays.',
        tag: 'Zero-Webhook Routing'
      },
      {
        title: 'Automate SEO and Social Meta Tags',
        description: 'Generate structured JSON-LD schemas, OpenGraph social preview assets, and Google IndexNow pings automatically upon publication.',
        tag: 'Search Dominance'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Select an Agency Blueprint',
        description: 'Pick a high-converting layout engineered specifically for software development, UI/UX design, or growth marketing agencies.'
      },
      {
        step: '02',
        title: 'Customize Content and Pricing Tiers',
        description: 'Drag in portfolio case studies from your Media Hub and link your live 18% GST retainer packages.'
      },
      {
        step: '03',
        title: 'Publish to Your Custom Domain',
        description: 'Deploy instantly to global edge servers under your custom domain with automatic SSL security.'
      }
    ],
    toolsReplaced: [
      { name: 'Webflow Core', category: 'Landing Page Builder', monthlySavingsUSD: 29, monthlySavingsINR: 2400 },
      { name: 'Framer Pro', category: 'Portfolio Sites', monthlySavingsUSD: 20, monthlySavingsINR: 1700 },
      { name: 'Squarespace Business', category: 'Website CMS', monthlySavingsUSD: 33, monthlySavingsINR: 2800 }
    ],
    faqs: [
      {
        question: 'Can I connect my agency’s custom domain name?',
        answer: 'Yes. Connect your custom domain (.com, .in, .agency, .studio) with automatic zero-touch SSL certificate provisioning.'
      },
      {
        question: 'Does the canvas support video case studies and interactive reels?',
        answer: 'Yes. Embed high-bitrate video showcases via Vimeo, YouTube, or self-hosted WebM/MP4 with strict aspect-ratio locking (16:9, 9:16, 1:1).'
      },
      {
        question: 'How fast do published landing pages load on mobile devices?',
        answer: 'Pages compile to static HTML via Next.js Turbopack and serve from global edge networks with responsive WebP image optimization for sub-400ms loading.'
      },
      {
        question: 'Can I embed discovery call booking widgets directly inside the page?',
        answer: 'Yes. Embed interactive call scheduling and package selection widgets directly on any canvas page to capture qualified meetings instantly.'
      }
    ],
    relatedFeatureSlugs: ['form-builder', 'lead-crm', 'content-ai'],
    mockup: {
      windowTitle: 'Cora Visual Canvas — Agency Funnel Builder',
      activeTabLabel: 'Funnel: Digital Agency Services 2026',
      tabs: [
        { id: 'editor', label: 'Canvas Editor', badge: 'Live v4.2' },
        { id: 'settings', label: 'Domain & SEO' },
        { id: 'analytics', label: 'Conversion Funnel' }
      ],
      headerTitle: 'Digital Product Design & Engineering Pitch Deck',
      headerSubtitle: 'Live URL: https://agency.heycora.in/services-2026 (SSL Active)',
      primaryActionLabel: 'Publish Changes',
      metric1: { label: 'Conversion Rate', value: '14.2%' },
      metric2: { label: 'Mobile Score', value: '99/100' },
      metric3: { label: 'Total Inquiries', value: '48 Leads' },
      tableHeaders: ['Section Block', 'Element Type', 'Connected Module', 'Visibility', 'Action'],
      rows: [
        {
          col1: 'Hero Showcase with Landscape Reel',
          col2: 'Video & Headline Block',
          col3: 'Media Hub (4K WebM)',
          statusText: 'Published',
          statusType: 'success',
          actionText: 'Edit Block'
        },
        {
          col1: 'Interactive 3-Tier Retainer Matrix',
          col2: 'Pricing Matrix Block',
          col3: '18% GST Invoicing',
          statusText: 'Published',
          statusType: 'success',
          actionText: 'Edit Rates'
        },
        {
          col1: 'Discovery Call Booking & Intake Form',
          col2: 'Form Capture Block',
          col3: 'Kanban CRM Pipeline',
          statusText: 'Published',
          statusType: 'success',
          actionText: 'Configure'
        }
      ]
    }
  },
  {
    slug: 'form-builder',
    title: 'Visual Form Builder & Embed Code Share Engine',
    shortTitle: 'Visual Forms',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    tagline: 'Deploy custom client intake forms, capture qualified briefs, and sync submissions instantly into your CRM.',
    heroDescription: 'Qualify high-ticket leads before booking discovery calls. Build frictionless client intake questionnaires, project scoping forms, and creative brief collectors. Share via standalone branded URLs or embed seamlessly into Framer, Webflow, and custom web apps with zero webhook configuration.',
    badgeColor: 'teal',
    iconName: 'FormInput',
    status: 'Live in Product',
    tags: ['Drag & Drop Form Builder', 'Live URL Share', 'Embeddable iFrames', 'Direct CRM Auto-Sync'],
    stats: [
      { metric: '3x Higher', label: 'Form Completion' },
      { metric: '< 2 Mins', label: 'Setup Time' },
      { metric: '0 Webhooks', label: 'Direct Database Sync' }
    ],
    theOldWay: [
      'Losing qualified prospects because generic form builders display third-party branding and load slowly.',
      'Relying on fragile Zapier zaps that break silently and lose high-value prospective client briefs.',
      'Dealing with unstructured intake emails that lack project scope, timeline expectations, or budget validation.'
    ],
    theCoraWay: [
      'Create unlimited white-label forms styled in clean monochromatic aesthetic matching your agency brand.',
      'Push every submission directly into your sales Kanban board with automatic deal value and tag assignment.',
      'Generate lightweight, responsive embed codes for Framer, Webflow, WordPress, or standalone live URLs.'
    ],
    capabilities: [
      {
        title: 'Design Multi-Step Intake Workflows',
        description: 'Assemble budget sliders, conditional branching questions, timeline selectors, and file dropzones in a visual canvas.',
        tag: 'Visual Constructor'
      },
      {
        title: 'Generate Universal Responsive Embeds',
        description: 'Copy lightweight, zero-dependency embed codes that auto-resize dynamically on Framer, Webflow, or custom platforms.',
        tag: 'Universal Embeds'
      },
      {
        title: 'Trigger Instant Notifications and Follow-Ups',
        description: 'Send immediate branded confirmation emails to clients while triggering instant mobile PWA and WhatsApp alerts for your sales team.',
        tag: 'Zero-Lag Response'
      },
      {
        title: 'Track Campaigns with UTM Attribution',
        description: 'Preserve ad campaign UTM parameters and route prospects to custom thank-you pages to measure ROI accurately.',
        tag: 'Attribution Tracking'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Configure Form Fields',
        description: 'Add questions for project scope, budget thresholds, tech stack requirements, and timeline milestones.'
      },
      {
        step: '02',
        title: 'Deploy via URL or Embed Code',
        description: 'Share your branded live link or copy the responsive embed snippet into your agency website.'
      },
      {
        step: '03',
        title: 'Receive Qualified Inbound Deals',
        description: 'Watch structured submissions populate your CRM pipeline instantly with automated team notifications.'
      }
    ],
    toolsReplaced: [
      { name: 'Typeform Plus', category: 'Online Forms', monthlySavingsUSD: 29, monthlySavingsINR: 2400 },
      { name: 'JotForm Silver', category: 'Form Submissions', monthlySavingsUSD: 39, monthlySavingsINR: 3200 },
      { name: 'Tally.so Pro', category: 'Form Builder', monthlySavingsUSD: 29, monthlySavingsINR: 2400 }
    ],
    faqs: [
      {
        question: 'Can I embed intake forms inside Framer or Webflow sites?',
        answer: 'Yes. Cora generates clean, zero-dependency HTML/JS snippets with automatic height calculation and zero layout shift.'
      },
      {
        question: 'Can prospective clients upload design briefs and RFP documents?',
        answer: 'Yes. Multi-file upload fields support high-resolution PDFs, DOCX, ZIP files, and images with secure cloud storage.'
      },
      {
        question: 'Will I receive instant alerts when a high-value lead submits a brief?',
        answer: 'Yes. Receive immediate push notifications on your mobile PWA, email digests, and optional WhatsApp notifications.'
      },
      {
        question: 'How does Cora prevent spam submissions without frustrating captchas?',
        answer: 'Cora uses invisible cryptographic honeypots and behavioral analysis to block spam bots without imposing annoying visual puzzles on clients.'
      }
    ],
    relatedFeatureSlugs: ['lead-crm', 'canvas-builder', 'review-portal'],
    mockup: {
      windowTitle: 'Cora Visual Form Builder & Embed Suite',
      activeTabLabel: 'Form: Agency Client Intake 2026',
      tabs: [
        { id: 'builder', label: 'Visual Fields', badge: '7 Fields' },
        { id: 'embed', label: 'Embed & Share' },
        { id: 'submissions', label: 'Submissions Log' }
      ],
      headerTitle: 'Product Design & Web Engineering Intake Form',
      headerSubtitle: 'Target Destination: Lead CRM → "New Inquiries" Column (Auto-Dispatched)',
      primaryActionLabel: 'Copy Embed Code',
      metric1: { label: 'Submissions', value: '142 Leads' },
      metric2: { label: 'Completion Rate', value: '78.6%' },
      metric3: { label: 'Avg Time to Fill', value: '1m 24s' },
      tableHeaders: ['Field Label', 'Input Type', 'Required', 'CRM Mapping', 'Status'],
      rows: [
        {
          col1: 'Client Full Name & Company',
          col2: 'Short Text Input',
          col3: 'Yes (Mandatory)',
          statusText: 'Mapped to Lead Name',
          statusType: 'success',
          actionText: 'Edit Field'
        },
        {
          col1: 'Project Scope & Budget Range',
          col2: 'Dropdown / Radio Group',
          col3: 'Yes (Mandatory)',
          statusText: 'Mapped to Deal Value',
          statusType: 'success',
          actionText: 'Edit Field'
        },
        {
          col1: 'Upload RFP / Creative Brief',
          col2: 'Multi-File Upload',
          col3: 'No (Optional)',
          statusText: 'Mapped to Media Vault',
          statusType: 'success',
          actionText: 'Edit Field'
        }
      ]
    }
  },
  {
    slug: 'review-portal',
    title: '5★ Review & Reputation Acquisition Portal',
    shortTitle: '5★ Review Portal',
    category: 'sales',
    categoryLabel: 'Sales & CRM',
    tagline: 'Automate 5-star client testimonial collection, protect your public reputation, and dominate Google search.',
    heroDescription: 'Transform delivered client projects into an automated referral and review engine. Trigger frictionless review requests upon project delivery, route 5-star ratings directly to Google Business Profile, and intercept critical feedback privately before it touches public channels.',
    badgeColor: 'amber',
    iconName: 'Star',
    status: 'Live in Product',
    tags: ['5★ Feedback Engine', 'Automated Campaigns', 'Google Routing', 'Reputation Score'],
    stats: [
      { metric: '4.9 ★', label: 'Average Agency Rating' },
      { metric: '+84%', label: 'Google Reviews Growth' },
      { metric: 'Zero Effort', label: 'Post-Delivery Trigger' }
    ],
    theOldWay: [
      'Delivering standout client projects but forgetting to request testimonials until momentum fades.',
      'Losing reviews because clients find multi-step review links confusing and time-consuming.',
      'Suffering public negative Google ratings that could have been resolved through private founder dialogue.'
    ],
    theCoraWay: [
      'Trigger automated review requests via WhatsApp and email the moment a project reaches final delivery or invoice settlement.',
      'Route 5-star ratings directly to your Google Business Profile with one tap while auto-copying client feedback.',
      'Direct ratings below 4 stars to a private resolution channel so you can address client concerns immediately.'
    ],
    capabilities: [
      {
        title: 'Route Reviews Intelligently',
        description: 'Direct ecstatic 5-star clients to Google Maps with one click while capturing critical feedback in a private resolution drawer.',
        tag: 'Reputation Shield'
      },
      {
        title: 'Automate Post-Delivery Triggers',
        description: 'Dispatch personalized WhatsApp and email feedback requests automatically upon milestone completion or final payment.',
        tag: 'Hands-Free Acquisition'
      },
      {
        title: 'Publish Interactive Walls of Love',
        description: 'Embed dynamic testimonial carousels, masonry grids, and verified rating badges on your agency website in one click.',
        tag: 'Social Proof Engine'
      },
      {
        title: 'Capture Video Testimonials on Mobile',
        description: 'Enable clients to record and submit 30-second video testimonials directly from their mobile browser with zero app downloads.',
        tag: 'Video Testimonials'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Trigger Feedback Requests',
        description: 'When you mark a project Delivered or clear final settlement, Cora dispatches a branded WhatsApp and email request.'
      },
      {
        step: '02',
        title: 'Client Rates Their Experience',
        description: 'Your client rates project performance from 1 to 5 stars and adds feedback on a frictionless mobile interface.'
      },
      {
        step: '03',
        title: 'Route to Google in 1 Tap',
        description: '5-star reviews automatically copy to clipboard and direct clients straight to your Google Business listing.'
      }
    ],
    toolsReplaced: [
      { name: 'Birdeye / Podium', category: 'Review Management', monthlySavingsUSD: 99, monthlySavingsINR: 8200 },
      { name: 'Senja.io Pro', category: 'Testimonial Collector', monthlySavingsUSD: 29, monthlySavingsINR: 2400 },
      { name: 'Testimonial.to', category: 'Video Reviews', monthlySavingsUSD: 25, monthlySavingsINR: 2000 }
    ],
    faqs: [
      {
        question: 'How does collecting Google reviews boost my agency’s local SEO ranking?',
        answer: 'Frequent, keyword-rich 5-star reviews directly improve your Google Maps and local search authority for terms like "software development agency" or "design studio".'
      },
      {
        question: 'Can I embed verified client testimonials directly on my portfolio?',
        answer: 'Yes. Cora provides copy-paste masonry grids, testimonial carousels, and verified review badges styled to match your site.'
      },
      {
        question: 'What happens if a client submits critical or low-star feedback?',
        answer: 'Ratings below 4 stars route to a private resolution inbox accessible only to your agency leadership, enabling proactive resolution before public posting.'
      },
      {
        question: 'Can I customize the feedback request messaging and branding?',
        answer: 'Yes. Fully customize email and WhatsApp message templates using dynamic client name, project title, and delivery milestone tags.'
      }
    ],
    relatedFeatureSlugs: ['form-builder', 'lead-crm', 'content-ai'],
    mockup: {
      windowTitle: 'Cora 5★ Review & Reputation Acquisition Portal',
      activeTabLabel: 'Review Stream & Google Maps Sync',
      tabs: [
        { id: 'reviews', label: 'Verified Reviews', badge: '64 Reviews' },
        { id: 'campaigns', label: 'Automated Campaigns' },
        { id: 'widgets', label: 'Wall of Love Embeds' }
      ],
      headerTitle: 'Client Reputation & Social Proof Engine',
      headerSubtitle: 'Google Business Rating: 4.9 ★ (84 Verified Reviews in Mumbai & Bangalore)',
      primaryActionLabel: '+ Send Review Invite',
      metric1: { label: 'Overall Rating', value: '4.95 / 5.0' },
      metric2: { label: 'Total Reviews', value: '84 Reviews' },
      metric3: { label: 'Conversion Rate', value: '72% Left 5★' },
      tableHeaders: ['Client Name', 'Project / Deliverable', 'Rating', 'Google Sync', 'Action'],
      rows: [
        {
          col1: 'Vikram Singhania (CEO, Apex FinTech)',
          col2: 'Next.js 15 Web App & Design System',
          col3: '★★★★★ (5/5)',
          statusText: 'Published on Google',
          statusType: 'success',
          actionText: 'View Review'
        },
        {
          col1: 'Ananya Roy (Head of Growth, D2C)',
          col2: 'Performance Marketing & Creative Ads',
          col3: '★★★★★ (5/5)',
          statusText: 'Published on Google',
          statusType: 'success',
          actionText: 'View Review'
        },
        {
          col1: 'Siddharth Roy (Founder, SaaS Startup)',
          col2: 'UI/UX Redesign & Staging Deploy',
          col3: '★★★★★ (5/5)',
          statusText: 'Copied to Clipboard',
          statusType: 'success',
          actionText: 'View Review'
        }
      ]
    }
  },
  {
    slug: 'esign-vault',
    title: 'Secure Document Vault & SHA-256 Legal E-Sign Suite',
    shortTitle: 'SHA-256 E-Signs',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    tagline: 'Execute airtight client agreements, collect legal e-signatures in seconds, and protect project margins with cryptographic audit trails.',
    heroDescription: 'Stop losing agency revenue to scope creep and disputed terms. Close deals faster with pre-built agency MSAs, Statements of Work, and retainer agreements. Collect legally binding electronic signatures on any device and lock milestones with immutable SHA-256 cryptographic timestamps under global e-sign frameworks.',
    badgeColor: 'rose',
    iconName: 'FileText',
    status: 'Live in Product',
    tags: ['Guided 5-Step Wizard', 'SHA-256 Audit Trail', 'IT Act 2000 Compliant', 'Tamper-Evident PDF'],
    stats: [
      { metric: '100% Legal', label: 'IT Act 2000 & ESIGN Compliant' },
      { metric: '₹0 / $0', label: 'Zero Per-Envelope Fees' },
      { metric: 'SHA-256', label: 'Cryptographic Tamper Seal' }
    ],
    theOldWay: [
      'Bleeding ₹2,500+ every month on DocuSign or PandaDoc subscriptions with restrictive envelope limits.',
      'Starting client sprints without signed SOWs, leaving your agency vulnerable to scope creep and unpaid invoices.',
      'Chasing clients across WhatsApp and email to download, sign, scan, and email back cumbersome paper PDFs.'
    ],
    theCoraWay: [
      'Dispatch unlimited, tamper-evident contracts directly from your CRM with zero per-envelope fees.',
      'Lock project deliverables, milestone payout gates, and change-request terms before starting billable work.',
      'Deliver frictionless 1-click mobile signing with instant touch canvas and automatic cryptographic audit certificates.'
    ],
    capabilities: [
      {
        title: 'Deploy Guided 5-Step Contract Wizards',
        description: 'Draft bulletproof agreements in under two minutes: Client Details → Scope & Deliverables → Payment Milestones → E-Signature → SHA-256 Seal.',
        tag: '5-Step Stepper'
      },
      {
        title: 'Capture High-Precision Touchscreen Signatures',
        description: 'Enable clients to sign instantly on mobile phones, tablets, or desktop browsers with smooth vector canvas capture.',
        tag: 'Mobile Optimized'
      },
      {
        title: 'Generate Immutable SHA-256 Audit Trails',
        description: 'Seal every executed agreement with a court-admissible certificate recording signer IP, exact timestamp, browser user-agent, and cryptographic hash.',
        tag: 'Legal Protection'
      },
      {
        title: 'Standardize Agency Contract Blueprints',
        description: 'Access lawyer-vetted contract templates for Master Services Agreements (MSA), Statements of Work (SOW), Design Retainers, and NDAs.',
        tag: 'Legal Templates'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Generate Contract Blueprint',
        description: 'Select an MSA, SOW, or retainer blueprint, configure milestone payment schedules, and lock scope deliverables.'
      },
      {
        step: '02',
        title: 'Dispatch Secure One-Click Link',
        description: 'Send a branded, authenticated signing link via WhatsApp or email with zero client account creation required.'
      },
      {
        step: '03',
        title: 'Seal with SHA-256 Hash',
        description: 'Client signs on their smartphone; both parties instantly receive a certified, cryptographically sealed PDF.'
      }
    ],
    toolsReplaced: [
      { name: 'DocuSign Standard', category: 'E-Signatures', monthlySavingsUSD: 25, monthlySavingsINR: 2000 },
      { name: 'PandaDoc Individual', category: 'Contract Management', monthlySavingsUSD: 29, monthlySavingsINR: 2400 },
      { name: 'Adobe Acrobat Sign', category: 'PDF Signing', monthlySavingsUSD: 20, monthlySavingsINR: 1650 }
    ],
    faqs: [
      {
        question: 'Are Cora e-signatures legally binding and admissible in court?',
        answer: 'Yes. Cora electronic signatures comply fully with Section 10A of the Indian Information Technology Act 2000, the Indian Evidence Act, the US ESIGN Act, and eIDAS regulations worldwide.'
      },
      {
        question: 'Do clients need a Cora account or mobile app to sign contracts?',
        answer: 'No. Clients click a secure, tokenized link to review terms, approve scope, and sign instantly from any smartphone or desktop browser.'
      },
      {
        question: 'How does the SHA-256 cryptographic seal prevent contract tampering?',
        answer: 'Every signed PDF generates a unique mathematical hash. If even a single character, punctuation mark, or payment term is altered post-signing, the cryptographic hash breaks immediately.'
      },
      {
        question: 'Can I restrict sprint work until the client signs the contract?',
        answer: 'Yes. Cora automatically integrates with Task Board and Master Calendar to keep sprint tasks locked until the contract is executed and the retainer deposit is secured.'
      }
    ],
    relatedFeatureSlugs: ['gst-invoicing', 'lead-crm', 'task-board'],
    mockup: {
      windowTitle: 'Cora Document Vault — Cryptographic E-Sign Registry',
      activeTabLabel: 'Contract #CORA-MSA-2026-042',
      tabs: [
        { id: 'active', label: 'Executed Contracts', badge: '42 Sealed' },
        { id: 'pending', label: 'Awaiting Signature', badge: '3' },
        { id: 'templates', label: 'Contract Blueprints' }
      ],
      headerTitle: 'Digital Agency Master Services Agreement & SOW',
      headerSubtitle: 'Client: Raymond Luxury Apparel Ltd • SHA-256 Hash: e3b0c44298fc1c149afbf4c8996fb924',
      primaryActionLabel: '+ New E-Sign Envelope',
      metric1: { label: 'Signed Turnaround', value: '< 2.4 Hours' },
      metric2: { label: 'Total Value', value: '₹28.4L' },
      metric3: { label: 'Audit Status', value: '100% Sealed' },
      tableHeaders: ['Contract Reference', 'Signer / Client', 'Value (INR)', 'Signed Timestamp', 'Verification'],
      rows: [
        {
          col1: 'Raymond — Web App & Design System MSA',
          col2: 'Anil Mehta (Director Marketing)',
          col3: '₹4,50,000',
          statusText: 'Signed & Sealed',
          statusType: 'success',
          actionText: 'Download PDF'
        },
        {
          col1: 'Titan Watches — Q3 Growth Marketing Retainer',
          col2: 'Pooja Verma (Brand Manager)',
          col3: '₹3,20,000',
          statusText: 'Signed & Sealed',
          statusType: 'success',
          actionText: 'Download PDF'
        },
        {
          col1: 'Nykaa Beauty — UI/UX Redesign Scope',
          col2: 'Rohan Sen (Creative Producer)',
          col3: '₹2,80,000',
          statusText: 'Viewed by Client',
          statusType: 'warning',
          actionText: 'Send Reminder'
        }
      ]
    }
  },
  {
    slug: 'crew-dispatch',
    title: 'Team Resource & Sprint Dispatch Scheduler with Conflict Guard',
    shortTitle: 'Team Dispatch',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    tagline: 'Eliminate scheduling chaos, allocate team bandwidth in real time, and dispatch crystal-clear sprint briefs with zero conflict.',
    heroDescription: 'Keep engineers, UI/UX designers, copywriters, and project managers perfectly synchronized. Prevent burnout and double-booking with intelligent conflict guards, dispatch instant mobile sprint briefs, and protect agency margins with automated contractor cost tracking.',
    badgeColor: 'indigo',
    iconName: 'Send',
    status: 'Live in Product',
    tags: ['Timeline Team Grid', 'Sprint Slot Pickers', 'Resource Allocation', 'Conflict Guard'],
    stats: [
      { metric: '0 Conflicts', label: 'Overlapping Allocations' },
      { metric: '1-Click', label: 'Instant Brief Dispatch' },
      { metric: '100% Real-Time', label: 'Bandwidth & Margin Visibility' }
    ],
    theOldWay: [
      'Juggling team allocations across noisy Slack channels, messy Notion tables, and outdated spreadsheets.',
      'Over-allocating senior developers to concurrent client deadlines, causing burnout, delays, and blown margins.',
      'Watching developers build the wrong features because sprint requirements and Figma links were buried in chat threads.'
    ],
    theCoraWay: [
      'Visualize entire team bandwidth across daily, weekly, and milestone timelines on a unified visual matrix.',
      'Block overlapping sprint allocations automatically with intelligent conflict guards before committing deliverables.',
      'Dispatch structured digital sprint briefs via WhatsApp and Slack with exact Figma specs, GitHub repos, and deadlines.'
    ],
    capabilities: [
      {
        title: 'Coordinate Real-Time Multi-Team Timelines',
        description: 'Track developer, designer, and PM availability across simultaneous client sprints with instant capacity indicators.',
        tag: 'Timeline Grid'
      },
      {
        title: 'Dispatch Instant Mobile Sprint Briefs',
        description: 'Generate clean, mobile-optimized project briefs complete with acceptance criteria, design assets, and delivery milestones.',
        tag: 'Sprint Briefs'
      },
      {
        title: 'Enforce Intelligent Conflict Guards',
        description: 'Prevent double-booking and team over-allocation by automatically blocking overlapping sprint schedules and milestone dates.',
        tag: 'Conflict Guard'
      },
      {
        title: 'Track Contractor Rates & Agency Margins',
        description: 'Log freelance contractor payouts and hourly costs against project billing to calculate real-time net profitability.',
        tag: 'Margin Control'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Structure Sprint Deliverables',
        description: 'Select the client project, attach scope requirements, define milestone criteria, and set strict delivery deadlines.'
      },
      {
        step: '02',
        title: 'Assign Available Talent',
        description: 'Select qualified engineers and designers using automated bandwidth checks and real-time conflict warnings.'
      },
      {
        step: '03',
        title: 'Dispatch One-Click Briefs',
        description: 'Push personalized sprint briefs directly to team members via WhatsApp and Slack with instant read and confirmation receipts.'
      }
    ],
    toolsReplaced: [
      { name: 'Float / Resource Guru', category: 'Resource Scheduling', monthlySavingsUSD: 49, monthlySavingsINR: 4200 },
      { name: 'Harvest Forecast', category: 'Team Planning', monthlySavingsUSD: 35, monthlySavingsINR: 2900 },
      { name: 'When I Work', category: 'Team Scheduling', monthlySavingsUSD: 25, monthlySavingsINR: 2000 }
    ],
    faqs: [
      {
        question: 'Can freelance contractors view their assigned sprint tasks without seeing client billing rates?',
        answer: 'Yes. Cora strictly isolates contractor briefs to display only deliverables, repos, and deadlines, keeping client invoices and agency margins private.'
      },
      {
        question: 'How does Cora handle remote distributed teams across multiple timezones?',
        answer: 'Cora automatically translates sprint kickoff times and deliverable deadlines into the local timezone (IST, GMT, EST, PST) of each team member.'
      },
      {
        question: 'How do team members acknowledge and accept sprint briefs?',
        answer: 'Team members tap "Confirm Brief" on their mobile device or desktop to update the agency dispatch dashboard in real time.'
      },
      {
        question: 'Can I track contractor burn rate against client project budgets?',
        answer: 'Yes. Cora logs contractor hours and milestone fees against the client invoice to provide live gross margin analytics for every sprint.'
      }
    ],
    relatedFeatureSlugs: ['master-calendar', 'asset-gear', 'task-board'],
    mockup: {
      windowTitle: 'Cora Team Dispatch & Resource Scheduler',
      activeTabLabel: 'Sprint Brief: FinTech Web App Launch',
      tabs: [
        { id: 'roster', label: 'Team Roster', badge: '8 Confirmed' },
        { id: 'callsheet', label: 'Digital Sprint Brief' },
        { id: 'timeline', label: 'Timeline Grid' }
      ],
      headerTitle: 'Full-Stack Web App Development — Sprint 3 of 4',
      headerSubtitle: 'Milestone: Next.js API & Database Migration • Kickoff: 09:30 AM IST',
      primaryActionLabel: '+ Dispatch Sprint Briefs',
      metric1: { label: 'Team Confirmed', value: '8 / 8 Confirmed' },
      metric2: { label: 'Kickoff Time', value: '09:30 AM' },
      metric3: { label: 'Sprint Duration', value: '2 Weeks' },
      tableHeaders: ['Team Member', 'Role / Department', 'Allocation', 'Status', 'Action'],
      rows: [
        {
          col1: 'Kabir Sharma',
          col2: 'Lead Full-Stack Engineer',
          col3: '100% Allocation',
          statusText: 'Confirmed',
          statusType: 'success',
          actionText: 'View Brief'
        },
        {
          col1: 'Aarav Patel',
          col2: 'Senior UI/UX Designer',
          col3: '80% Allocation',
          statusText: 'Confirmed',
          statusType: 'success',
          actionText: 'View Brief'
        },
        {
          col1: 'Neha Kulkarni',
          col2: 'QA & Staging Specialist',
          col3: '50% Allocation',
          statusText: 'Dispatched',
          statusType: 'warning',
          actionText: 'Resend Brief'
        }
      ]
    }
  },
  {
    slug: 'master-calendar',
    title: 'Master Calendar & Autonomous Project Schedule Manager',
    shortTitle: 'Master Calendar',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    tagline: 'Master your agency schedule, automate client booking flows, and lock sprint dates to signed contracts and milestone deposits.',
    heroDescription: 'Unify client discovery calls, sprint kickoffs, design review milestones, and staging deployments into a synchronized agency calendar. Eliminate double-booking, automate time-zone conversions, and enforce stage locks so unconfirmed or unpaid client projects never hijack your team’s schedule.',
    badgeColor: 'purple',
    iconName: 'Calendar',
    status: 'Live in Product',
    tags: ['Day/Week/Month Grid', '5-Step Booking Modal', 'Real-Time Stage Locks', 'Google Calendar 2-Way Sync'],
    stats: [
      { metric: '0 Conflicts', label: 'Double-Booking Prevention' },
      { metric: '2-Way Sync', label: 'Google, Outlook & Apple Cal' },
      { metric: '100% Guarded', label: 'Deposit-Gated Project Slots' }
    ],
    theOldWay: [
      'Jumping between Calendly, Google Calendar, and scattered spreadsheets, constantly fixing double-booked client review meetings.',
      'Letting unvetted prospects or slow-paying clients reserve peak sprint slots without signed contracts or retainer deposits.',
      'Losing track of multi-sprint delivery deadlines across different client accounts and engineering pods.'
    ],
    theCoraWay: [
      'Unify agency scheduling in one command center linked directly to CRM leads, legal contracts, and active team rosters.',
      'Enforce automated stage locks that hold calendar slots in "Tentative" until clients sign the SOW and pay the deposit invoice.',
      'Maintain seamless two-way synchronization with Google Calendar, Microsoft Outlook, and Apple Calendar across all devices.'
    ],
    capabilities: [
      {
        title: 'Navigate Multi-View Agency Timelines',
        description: 'Switch fluidly between Day Agenda, Weekly Matrix, Monthly Bird’s Eye, and Multi-Pod Sprint allocation views.',
        tag: 'Multi-View'
      },
      {
        title: 'Lock Dates with Deposit & Contract Gates',
        description: 'Require executed SOWs and upfront milestone deposits before confirming project kickoff dates on the agency master schedule.',
        tag: 'Stage Locks'
      },
      {
        title: 'Automate Branded Client Self-Booking',
        description: 'Share custom booking links with built-in buffer times, daily meeting limits, and automated client intake questionnaires.',
        tag: 'Self-Booking'
      },
      {
        title: 'Convert Global Timezones Automatically',
        description: 'Schedule discovery calls and sprint presentations effortlessly with international clients across IST, GMT, EST, and PST.',
        tag: 'Global Timezones'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Configure Agency Availability',
        description: 'Set working hours, define buffer intervals between calls, and set minimum advance notice rules to protect focus time.'
      },
      {
        step: '02',
        title: 'Lock Project Dates via 5-Step Modal',
        description: 'Link the client record, assign the project lead, specify sprint milestones, and attach retainer deposit terms.'
      },
      {
        step: '03',
        title: 'Sync Across All Devices & Teams',
        description: 'Synchronize schedule updates in real time to Google, Apple, and Outlook calendars with automated client reminders.'
      }
    ],
    toolsReplaced: [
      { name: 'Calendly Pro', category: 'Booking Links', monthlySavingsUSD: 16, monthlySavingsINR: 1500 },
      { name: 'Acuity Scheduling', category: 'Appointment System', monthlySavingsUSD: 20, monthlySavingsINR: 1700 },
      { name: 'HoneyBook Scheduling', category: 'Client Calendar', monthlySavingsUSD: 22, monthlySavingsINR: 1900 }
    ],
    faqs: [
      {
        question: 'Can I block agency company holidays and focus blocks across the entire team?',
        answer: 'Yes. Block company off-sites, national holidays, or no-meeting focus blocks with a single click across all team calendars.'
      },
      {
        question: 'Does the calendar sync natively with iOS and Android devices?',
        answer: 'Yes. Cora provides real-time 2-way Google Calendar integration and CalDAV / iCal subscription feeds for all mobile and desktop devices.'
      },
      {
        question: 'Can I require payment before a client books a strategy or kickoff session?',
        answer: 'Yes. Integrate upfront UPI, credit card, or retainer deposit requirements directly into your self-booking links.'
      },
      {
        question: 'Can team members access their personal sprint schedules without seeing full financial data?',
        answer: 'Yes. Role-based permissions ensure developers and designers see their sprint deadlines without viewing contract values or invoice amounts.'
      }
    ],
    relatedFeatureSlugs: ['crew-dispatch', 'lead-crm', 'asset-gear'],
    mockup: {
      windowTitle: 'Cora Master Calendar & Booking Manager',
      activeTabLabel: 'Agency Schedule — August 2026',
      tabs: [
        { id: 'month', label: 'Month Grid', badge: '24 Projects' },
        { id: 'bays', label: 'Sprint Teams View' },
        { id: 'agenda', label: 'Upcoming Agenda' }
      ],
      headerTitle: 'Project Sprints & Client Review Calendar',
      headerSubtitle: 'Synced with Google Calendar & Team Dispatch Roster',
      primaryActionLabel: '+ Schedule Sprint Slot',
      metric1: { label: 'Team Bandwidth', value: '88% This Week' },
      metric2: { label: 'Active Sprints', value: '24 Deliverables' },
      metric3: { label: 'Pending Scopes', value: '3 Reviews' },
      tableHeaders: ['Time Slot / Date', 'Project Title', 'Deliverable Stage', 'Assigned Team', 'Status'],
      rows: [
        {
          col1: 'Saturday, Aug 29 • 08:00 - 18:00',
          col2: 'Raymond — SaaS App Design Sprint',
          col3: 'Figma Design System Token Review',
          statusText: 'Locked & Confirmed',
          statusType: 'success',
          actionText: 'View Details'
        },
        {
          col1: 'Sunday, Aug 30 • 10:00 - 16:00',
          col2: 'Titan Watches — Growth Campaign Sprint',
          col3: 'Performance Ad Creatives & Copy Deck',
          statusText: 'Locked & Confirmed',
          statusType: 'success',
          actionText: 'View Details'
        },
        {
          col1: 'Monday, Aug 31 • 14:00 - 18:00',
          col2: 'Nykaa Beauty — Full-Stack Staging Deploy',
          col3: 'Staging Server QA & Lighthouse Audit',
          statusText: 'Review Scheduled',
          statusType: 'warning',
          actionText: 'Follow Up'
        }
      ]
    }
  },
  {
    slug: 'task-board',
    title: 'Client Task, Milestone & Sprint Board',
    shortTitle: 'Sprint Task Board',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    tagline: 'Ship client deliverables on schedule, eliminate scope creep, and automate milestone approvals with clear visual sprint boards.',
    heroDescription: 'Guide client projects seamlessly from discovery workshops and Figma wireframes to staging deployments and production sign-offs. Assign tasks with live deadline countdowns, give clients transparent milestone visibility, and lock deliverables with one-click client approvals.',
    badgeColor: 'emerald',
    iconName: 'CheckSquare',
    status: 'Live in Product',
    tags: ['Priority Badges', 'Deadline Timers', 'Milestone Sign-Offs', 'Role-Based Assignees'],
    stats: [
      { metric: '100% On-Time', label: 'Milestone Delivery Rate' },
      { metric: '1-Click', label: 'Client Milestone Approvals' },
      { metric: '85% Fewer', label: '"Status Update" Client Messages' }
    ],
    theOldWay: [
      'Missing critical client deadlines because tasks, design files, and feedback were scattered across Asana, Trello, and WhatsApp.',
      'Wasting hours answering repetitive client messages asking "What is the status of our project?"',
      'Suffering from unapproved scope creep because clients requested new features without formal milestone sign-offs.'
    ],
    theCoraWay: [
      'Auto-generate standardized agency sprint boards: Discovery → UI/UX Design → Development → QA Testing → Production Deploy.',
      'Assign clear ownership with urgent priority tags, deadline countdown timers, and linked GitHub/Figma resources.',
      'Provide clients with a branded, read-only milestone portal where they approve deliverables with one-click digital sign-offs.'
    ],
    capabilities: [
      {
        title: 'Auto-Generate Agency Sprint Templates',
        description: 'Instantly spawn pre-configured milestone workflows for Web Development, Brand Identity, Mobile Apps, or Marketing Retainers.',
        tag: 'Smart Templates'
      },
      {
        title: 'Enforce Urgent Priority & Countdown Timers',
        description: 'Highlight critical deliverables with visual badges (Critical 🔴, In Progress 🟡, Done 🟢) and real-time deadline countdowns.',
        tag: 'Visual Priority'
      },
      {
        title: 'Capture One-Click Client Sign-Offs',
        description: 'Lock design systems and staging builds with formal digital approvals to eliminate disputed scope changes.',
        tag: 'Sign-Off Engine'
      },
      {
        title: 'Centralize Code Repos, Figma Specs & Invoices',
        description: 'Link every task card directly to relevant design files, pull requests, and contract billing milestones.',
        tag: 'Zero Context Loss'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Initialize Project Sprint Board',
        description: 'Launch from a tailored agency template or let Cora auto-generate tasks directly from your signed SOW agreement.'
      },
      {
        step: '02',
        title: 'Assign Roles & Delivery Windows',
        description: 'Assign engineers, UI designers, and QA specialists with strict deadline timers and linked asset specifications.'
      },
      {
        step: '03',
        title: 'Secure Client Milestone Approval',
        description: 'Move tasks through QA to completion, triggering automated client notifications for instant one-click milestone sign-off.'
      }
    ],
    toolsReplaced: [
      { name: 'Asana Premium', category: 'Task Management', monthlySavingsUSD: 24, monthlySavingsINR: 2000 },
      { name: 'Monday.com Pro', category: 'Project Tracking', monthlySavingsUSD: 30, monthlySavingsINR: 2500 },
      { name: 'ClickUp Business', category: 'Team Workspaces', monthlySavingsUSD: 19, monthlySavingsINR: 1600 }
    ],
    faqs: [
      {
        question: 'Can external contractors view only their assigned sprint tasks?',
        answer: 'Yes. Contractor access restricts team members to their specific task cards and technical specs with zero access to client billing data.'
      },
      {
        question: 'Can clients modify internal development tasks on our sprint board?',
        answer: 'No. Clients access a dedicated read-only milestone view where they can review progress and submit feedback without altering internal tasks.'
      },
      {
        question: 'Does Cora alert the team before tasks become overdue?',
        answer: 'Yes. Cora delivers automated notifications via email, in-app badges, and Slack 24 hours and 4 hours before milestone deadlines.'
      },
      {
        question: 'Can I attach Figma files, GitHub pull requests, and loom walkthroughs to tasks?',
        answer: 'Yes. Every task card supports rich media embeds, live Figma previews, GitHub PR links, and file attachments.'
      }
    ],
    relatedFeatureSlugs: ['crew-dispatch', 'master-calendar', 'media-hub'],
    mockup: {
      windowTitle: 'Cora Task & Project Milestone Board',
      activeTabLabel: 'Project: FinTech Web App Launch',
      tabs: [
        { id: 'board', label: 'Milestone Board', badge: '8 Tasks' },
        { id: 'timeline', label: 'Gantt Timeline' },
        { id: 'client-view', label: 'Client Milestone View' }
      ],
      headerTitle: 'Next.js 15 Web App & Design System Sprint',
      headerSubtitle: 'Target Delivery Date: Sept 04, 2026 • Lead Architect: Sarah Jenkins',
      primaryActionLabel: '+ Add Sprint Task',
      metric1: { label: 'Tasks Completed', value: '5 / 8 Done' },
      metric2: { label: 'Next Milestone', value: 'Tomorrow (Staging QA)' },
      metric3: { label: 'Client Status', value: 'On Schedule' },
      tableHeaders: ['Task / Milestone', 'Department', 'Assignee', 'Deadline', 'Status'],
      rows: [
        {
          col1: 'Figma Design System & Token Sync',
          col2: 'Product Design',
          col3: 'Sarah Jenkins',
          statusText: 'In Progress',
          statusType: 'warning',
          actionText: 'Open Task'
        },
        {
          col1: 'PostgreSQL Database Schema & Prisma Migrations',
          col2: 'Backend Engineering',
          col3: 'Arjun Nair',
          statusText: 'Ready to Start',
          statusType: 'info',
          actionText: 'Open Task'
        },
        {
          col1: 'Architecture & Security SOW Sign-Off',
          col2: 'Product Discovery',
          col3: 'Client (Apex Tech Team)',
          statusText: 'Approved by Client',
          statusType: 'success',
          actionText: 'View Sign-Off'
        }
      ]
    }
  },
  {
    slug: 'gst-invoicing',
    title: 'Financials, Automated Invoicing & 18% GST Tax Hub',
    shortTitle: '18% GST Invoicing',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    tagline: 'Auto-calculate 18% CGST/SGST splits, embed dynamic UPI QR codes, and collect client payments with 0% gateway fees.',
    heroDescription: 'Automate your entire agency cashflow and tax compliance in one place. Generate SAC 9983 compliant invoices, eliminate manual tax math errors, embed zero-fee dynamic UPI payment QR codes, and export GSTR-1 ready sales ledgers directly to your Chartered Accountant.',
    badgeColor: 'emerald',
    iconName: 'Receipt',
    status: 'Live in Product',
    tags: ['18% GST Automation', 'CGST/SGST Auto-Split', 'Dynamic UPI QR Codes', 'CA-Ready GSTR-1 Ledger'],
    stats: [
      { metric: '18% GST', label: 'Automated Tax Calculation' },
      { metric: '0% Fees', label: 'Direct UPI Bank Settlements' },
      { metric: '1-Click', label: 'GSTR-1 & CA Export' }
    ],
    theOldWay: [
      'Wasting hours calculating 9% CGST + 9% SGST vs 18% IGST splits across messy Excel sheets with costly math errors.',
      'Losing 2–3% on credit card gateway fees or chasing delayed NEFT bank transfers across endless WhatsApp threads.',
      'Scrambling through frantic quarter-end rushes compiling fragmented invoice PDFs and spreadsheets for your accountant.'
    ],
    theCoraWay: [
      'Auto-detect intra-state vs inter-state client GSTINs and compute exact CGST, SGST, or IGST tax splits instantly.',
      'Embed dynamic, invoice-specific UPI QR codes directly on branded PDFs so clients scan and settle instantly with zero fees.',
      'Export one-click GSTR-1 compliant sales registers formatted with B2B GSTIN validation, SAC 9983 codes, and taxable turnovers.'
    ],
    capabilities: [
      {
        title: 'Calculate 18% GST Splits Automatically',
        description: 'Detect supplier and client state codes instantly to apply 9%+9% CGST/SGST or 18% IGST rates with zero manual math.',
        tag: 'GST Rulebook'
      },
      {
        title: 'Generate Dynamic Zero-Fee UPI QR Codes',
        description: 'Encode invoice totals, payee VPA handles, and invoice reference numbers into high-res scannable QRs for direct bank transfers.',
        tag: 'Zero-Fee UPI'
      },
      {
        title: 'Automate Multi-Milestone Retainer Invoicing',
        description: 'Bill clients across structured milestones like 50% advance booking, 30% staging review, and 20% final delivery in one click.',
        tag: 'Milestone Invoicing'
      },
      {
        title: 'Dispatch Automated WhatsApp & Email Chasers',
        description: 'Send gentle, automated payment reminders via WhatsApp and email before and on invoice due dates to protect agency cashflow.',
        tag: 'Payment Automation'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Select Client & Billable Scope',
        description: 'Pull accepted CRM proposal items or add custom sprint milestones in one click.'
      },
      {
        step: '02',
        title: 'Auto-Generate Tax & Payment QR',
        description: 'Cora calculates exact 18% GST splits, verifies SAC Code 9983, and embeds a dynamic UPI QR code.'
      },
      {
        step: '03',
        title: 'Dispatch & Collect Instantly',
        description: 'Deliver branded invoice PDFs via WhatsApp and email. Clients scan the QR to pay directly into your bank account.'
      }
    ],
    toolsReplaced: [
      { name: 'Zoho Books / Invoice', category: 'Invoicing Software', monthlySavingsUSD: 15, monthlySavingsINR: 1200 },
      { name: 'FreshBooks Premium', category: 'Billing & Accounting', monthlySavingsUSD: 35, monthlySavingsINR: 2900 },
      { name: 'Khatabook Pro', category: 'Payment Bookkeeping', monthlySavingsUSD: 10, monthlySavingsINR: 800 }
    ],
    faqs: [
      {
        question: 'Can I generate invoices if my agency operates below the GST threshold?',
        answer: 'Yes. Generate clean non-GST commercial invoices or export-ready zero-tax LUT invoices for international agency clients.'
      },
      {
        question: 'How do dynamic UPI QR codes eliminate payment gateway commissions?',
        answer: 'Cora encodes the exact invoice balance into NPCI-compliant UPI QRs. Clients scan using GPay, PhonePe, or Paytm, depositing 100% of funds directly into your bank account with zero gateway cuts.'
      },
      {
        question: 'Can I export invoice ledgers directly for my Chartered Accountant?',
        answer: 'Yes. Export GSTR-1 ready monthly and quarterly sales registers formatted with validated B2B GSTINs, SAC codes, and taxable turnover summaries.'
      },
      {
        question: 'Does Cora support multi-currency invoicing for international clients?',
        answer: 'Yes. Bill global clients seamlessly in USD, EUR, GBP, AED, CAD, and INR with automated currency conversions.'
      }
    ],
    relatedFeatureSlugs: ['esign-vault', 'lead-crm', 'asset-gear'],
    mockup: {
      windowTitle: 'Cora Financials & 18% GST Invoicing Hub',
      activeTabLabel: 'Sales Register (FY 2026-27)',
      tabs: [
        { id: 'invoices', label: 'All Tax Invoices', badge: '₹32.6L Total' },
        { id: 'recurring', label: 'Retainer Schedules' },
        { id: 'ca-export', label: 'GSTR-1 Export' }
      ],
      headerTitle: 'Automated 18% GST Invoicing & Revenue Analytics',
      headerSubtitle: 'SAC Code: 9983 (Software Development & Digital Creative Services)',
      primaryActionLabel: '+ Generate Tax Invoice',
      metric1: { label: 'Total Invoiced', value: '₹32,60,000' },
      metric2: { label: 'GST Collected', value: '₹5,86,800' },
      metric3: { label: 'Avg Collection Time', value: '3.2 Days' },
      tableHeaders: ['Invoice #', 'Client / Entity', 'Tax Breakdown', 'Grand Total', 'Status'],
      rows: [
        {
          col1: 'INV-2026-0104',
          col2: 'Tata Motors Digital Experience Hub',
          col3: '18% IGST (Inter-State)',
          statusText: 'Paid via UPI',
          statusType: 'success',
          actionText: 'View Receipt'
        },
        {
          col1: 'INV-2026-0103',
          col2: 'FabIndia Lifestyle Brand Portal',
          col3: '9% CGST + 9% SGST',
          statusText: 'Paid via UPI',
          statusType: 'success',
          actionText: 'View Receipt'
        },
        {
          col1: 'INV-2026-0102',
          col2: 'Kalyan Jewellers Web App Scope',
          col3: '9% CGST + 9% SGST',
          statusText: 'Due in 2 Days',
          statusType: 'warning',
          actionText: 'Send QR Reminder'
        }
      ]
    }
  },
  {
    slug: 'asset-gear',
    title: 'Asset, Hardware & Software License Inventory Manager',
    shortTitle: 'Assets & Hardware',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    tagline: 'Track testing devices, studio production hardware, and software seat licenses with real-time assignment logs.',
    heroDescription: 'Maintain absolute visibility and control over agency laptops, mobile QA testing fleets, video cameras, and team software licenses. Eliminate double-booking conflicts, track hardware check-outs in one tap, and protect high-value agency assets.',
    badgeColor: 'sky',
    iconName: 'Camera',
    status: 'Live in Product',
    tags: ['Hardware Check-In/Out', 'Barcode & QR Scanning', 'Software Seat Allocation', 'Depreciation & Warranty Logs'],
    stats: [
      { metric: '0 Missing', label: 'Asset Tracking Accuracy' },
      { metric: '1-Tap', label: 'Hardware Check-Out' },
      { metric: 'Real-Time', label: 'Fleet Availability Calendar' }
    ],
    theOldWay: [
      'Deploying project teams only to discover that critical QA test devices or staging laptops went missing or uncharged.',
      'Tracking software seats, Figma licenses, and API keys on unsecure sticky notes leading to unexpected expirations mid-sprint.',
      'Relying on outdated spreadsheets that fail to track hardware depreciation, warranty expirations, and team custodian histories.'
    ],
    theCoraWay: [
      'Audit your agency hardware fleet with real-time status indicators: Available, In-Use by Dev, Maintenance, or Reserved.',
      'Assemble standardized project kits (e.g. Mobile QA Kit, 4K Media Kit) that check out devices and peripherals in a single tap.',
      'Maintain a centralized digital registry logging serial numbers, warranty documents, purchase invoices, and software seats.'
    ],
    capabilities: [
      {
        title: 'Assemble Standardized Project Kits',
        description: 'Bundle developer laptops, mobile test devices, staging servers, and audio/video gear into one-tap deployable kits.',
        tag: 'Project Kits'
      },
      {
        title: 'Prevent Schedule & Allocation Conflicts',
        description: 'Flag hardware assignment clashes instantly whenever team members request equipment already allocated to overlapping sprints.',
        tag: 'Conflict Shield'
      },
      {
        title: 'Track Software Seats & Warranty Lifecycles',
        description: 'Monitor SaaS seat allocations, OS patch schedules, and hardware warranty deadlines with automated expiration alerts.',
        tag: 'Lifecycle Manager'
      },
      {
        title: 'Link Hardware Costs to Client Invoices',
        description: 'Transfer dedicated testing device rentals, cloud compute servers, and specialized equipment costs directly into client billing line items.',
        tag: 'Cost Recovery'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Register Fleet & License Assets',
        description: 'Log serial numbers, purchase values, warranty documents, and assignable seat counts in your digital catalog.'
      },
      {
        step: '02',
        title: 'Assign Gear to Project Sprints',
        description: 'Attach hardware kits and testing suites directly to active sprints with automatic inventory reservation.'
      },
      {
        step: '03',
        title: 'Audit Condition on Check-In',
        description: 'Log gear return status, report missing peripherals, and track maintenance records in one tap.'
      }
    ],
    toolsReplaced: [
      { name: 'Cheqroom / GearTrack', category: 'Equipment Management', monthlySavingsUSD: 45, monthlySavingsINR: 3800 },
      { name: 'ShareMyToolbox', category: 'Tool Inventory', monthlySavingsUSD: 30, monthlySavingsINR: 2500 },
      { name: 'Excel Inventory Sheets', category: 'Manual Spreadsheets', monthlySavingsUSD: 0, monthlySavingsINR: 0 }
    ],
    faqs: [
      {
        question: 'Can I generate printable QR stickers and barcodes for agency equipment?',
        answer: 'Yes. Generate high-contrast QR labels that team members scan with smartphone cameras to check gear in and out instantly.'
      },
      {
        question: 'How does Cora handle third-party hardware rentals and vendor equipment?',
        answer: 'Flag assets as external vendor rentals with rental daily rates, vendor contacts, and automated return deadline alerts.'
      },
      {
        question: 'Can I track hardware distributed to remote team members?',
        answer: 'Yes. Assign hardware items directly to remote staff profiles with courier tracking numbers and digital handover acknowledgments.'
      },
      {
        question: 'Can asset rental costs flow directly into client invoices?',
        answer: 'Yes. Convert specialized hardware usage and cloud server costs into billable invoice line items in one click.'
      }
    ],
    relatedFeatureSlugs: ['crew-dispatch', 'master-calendar', 'gst-invoicing'],
    mockup: {
      windowTitle: 'Cora Asset & Hardware Inventory Manager',
      activeTabLabel: 'Agency Hardware & Testing Fleet',
      tabs: [
        { id: 'gear', label: 'Hardware Inventory', badge: '48 Items' },
        { id: 'kits', label: 'Standardized Kits' },
        { id: 'licenses', label: 'Software Licenses' }
      ],
      headerTitle: 'Agency Hardware, Device & Testing Fleet',
      headerSubtitle: 'Total Insured Asset Value: ₹42,50,000 across active agency workspaces',
      primaryActionLabel: '+ Check Out Kit',
      metric1: { label: 'Total Asset Value', value: '₹42,50,000' },
      metric2: { label: 'In-Use by Teams', value: '18 Items' },
      metric3: { label: 'Available in Hub', value: '30 Items' },
      tableHeaders: ['Asset / Serial #', 'Category', 'Assigned User', 'Assigned Sprint', 'Status'],
      rows: [
        {
          col1: 'Apple MacBook Pro M3 Max (SN: #88419)',
          col2: 'Dev Workstation',
          col3: 'Kabir Sharma',
          statusText: 'In-Use on Sprint',
          statusType: 'warning',
          actionText: 'Inspect'
        },
        {
          col1: 'iPhone 16 Pro Test Device (SN: #19203)',
          col2: 'Mobile QA Kit',
          col3: 'Neha Kulkarni',
          statusText: 'In-Use on Sprint',
          statusType: 'warning',
          actionText: 'Inspect'
        },
        {
          col1: 'Sony FX6 Cinema Camera (SN: #77412)',
          col2: 'Media & Video Kit',
          col3: 'Unassigned',
          statusText: 'Available in Studio',
          statusType: 'success',
          actionText: 'Assign'
        }
      ]
    }
  },
  {
    slug: 'media-hub',
    title: 'Agency Media Hub & Digital Asset Cloud Vault',
    shortTitle: 'Media Hub & Assets',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    tagline: 'Deliver branded client asset galleries, generate 1-click aspect ratio crops, and stream high-bitrate media at CDN speeds.',
    heroDescription: 'Distribute high-resolution brand guidelines, Figma design exports, 4K video reels, and client deliverable packages through fast, white-labeled media vaults. Eliminate messy Drive links, protect client assets with PIN security, and auto-generate social crop presets.',
    badgeColor: 'blue',
    iconName: 'HardDrive',
    status: 'Live in Product',
    tags: ['White-Labeled Media Portals', '1-Click Aspect Crop Presets', 'Sub-50ms Global CDN', 'PIN-Protected Delivery'],
    stats: [
      { metric: '0% Quality Loss', label: 'Bit-for-Bit Pristine CDN' },
      { metric: 'Sub-50ms', label: 'Global Asset Streaming' },
      { metric: '1-Click', label: '1:1, 4:5, 16:9 Aspect Crops' }
    ],
    theOldWay: [
      'Sharing chaotic Google Drive and Dropbox links that expire, demand permission requests, and confuse client executives.',
      'Wasting design hours manually exporting and re-cropping assets into square, vertical, and landscape dimensions in Photoshop.',
      'Paying expensive monthly fees for disconnected cloud storage tools that present clients with generic, unbranded interfaces.'
    ],
    theCoraWay: [
      'Deliver white-labeled client portals featuring your agency branding, custom subdomain, and zero third-party software ads.',
      'Generate 1:1 square, 4:5 social, 9:16 vertical story, and 16:9 landscape crops instantly with the built-in preset selector.',
      'Stream 4K video reels and download full-resolution ZIP archives directly through ultra-fast enterprise cloud storage.'
    ],
    capabilities: [
      {
        title: 'Deploy White-Labeled Client Portals',
        description: 'Deliver final creative packages through password-protected, branded portals on your custom agency domain.',
        tag: 'Client Delivery'
      },
      {
        title: 'Generate 1-Click Multi-Aspect Crops',
        description: 'Create instant pixel-perfect crops for Instagram Reels (9:16), Feed Posts (4:5, 1:1), and Web Banners (16:9) without re-rendering.',
        tag: 'Aspect Studio'
      },
      {
        title: 'Stream 4K Video Reels Without Buffering',
        description: 'Preview high-bitrate ProRes and H.265 video deliverables directly in the browser with ultra-smooth CDN streaming.',
        tag: 'Video Streaming'
      },
      {
        title: 'Embed Assets Across Proposals & Funnels',
        description: 'Pull media library assets directly into client proposals, CMS pages, and landing pages with zero duplicate uploads.',
        tag: 'Unified Media'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Upload High-Res Deliverables',
        description: 'Drag and drop design systems, raw vectors, and 4K footage into structured client workspace folders.'
      },
      {
        step: '02',
        title: 'Apply Aspect Crops & Branding',
        description: 'Select instant social crop dimensions and apply subtle agency watermark badges when sharing draft proofs.'
      },
      {
        step: '03',
        title: 'Distribute Secure Client Links',
        description: 'Send PIN-protected, high-speed download galleries branded with your agency logo and custom domain.'
      }
    ],
    toolsReplaced: [
      { name: 'Dropbox Standard', category: 'Cloud Storage', monthlySavingsUSD: 15, monthlySavingsINR: 1600 },
      { name: 'Google Drive Business', category: 'File Storage', monthlySavingsUSD: 13, monthlySavingsINR: 1800 },
      { name: 'WeTransfer Pro', category: 'File Transfers', monthlySavingsUSD: 15, monthlySavingsINR: 1400 }
    ],
    faqs: [
      {
        question: 'Can clients download all deliverable files in a single uncompressed ZIP archive?',
        answer: 'Yes. Clients can download complete full-resolution ZIP archives or select individual design assets with one tap.'
      },
      {
        question: 'Does Cora compress or degrade original image and video files?',
        answer: 'No. Cora preserves original assets with bit-for-bit uncompressed fidelity on global cloud infrastructure.'
      },
      {
        question: 'Can I protect sensitive client deliverables with passwords and expiration dates?',
        answer: 'Yes. Secure any gallery link with custom PIN codes, passwords, and automated expiration timers.'
      },
      {
        question: 'Can I pull Media Hub assets directly into client proposal decks and websites?',
        answer: 'Yes. The Media Hub links natively with the Proposal Engine, Canvas Builder, and Content AI modules.'
      }
    ],
    relatedFeatureSlugs: ['asset-gear', 'canvas-builder', 'content-ai'],
    mockup: {
      windowTitle: 'Cora Agency Media Hub & Asset Vault',
      activeTabLabel: 'Deliverables: Raymond Brand Refresh 2026',
      tabs: [
        { id: 'gallery', label: 'Media Browser', badge: '240 Files' },
        { id: 'crops', label: 'Aspect Crop Studio' },
        { id: 'client-portal', label: 'Client Delivery Link' }
      ],
      headerTitle: 'Digital Brand Assets & Deliverable Vault',
      headerSubtitle: 'Client Portal: https://vault.heycora.in/raymond-brand (PIN Protected)',
      primaryActionLabel: '+ Upload Assets',
      metric1: { label: 'Total Files', value: '240 Assets' },
      metric2: { label: 'Vault Size', value: '42.8 GB' },
      metric3: { label: 'Client Downloads', value: '18 Times' },
      tableHeaders: ['Asset Name', 'Resolution / Type', 'Crop Presets', 'File Size', 'Status'],
      rows: [
        {
          col1: 'RAYMOND_DESIGN_SYSTEM_V4.FIG',
          col2: 'Figma Master Tokens',
          col3: '1:1, 4:3, 16:9 Ready',
          statusText: 'Processed (CDN)',
          statusType: 'success',
          actionText: 'Download'
        },
        {
          col1: 'RAYMOND_BRAND_REEL_4K.MP4',
          col2: '3840 x 2160 (ProRes 422)',
          col3: '9:16 Vertical Crop',
          statusText: 'Streaming Live',
          statusType: 'success',
          actionText: 'Preview'
        },
        {
          col1: 'RAYMOND_LOGO_SUITE_MASTER.ZIP',
          col2: 'Vector SVG / PNG / PDF',
          col3: 'Social Crops Synced',
          statusText: 'Processed (CDN)',
          statusType: 'success',
          actionText: 'Download'
        }
      ]
    }
  },
  {
    slug: 'rbac-system',
    title: 'Multi-Tenant Role-Based Access Control (RBAC)',
    shortTitle: 'Multi-Tenant RBAC',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    tagline: 'Enforce tenant isolation, mask agency profit margins, and assign granular permissions across team roles.',
    heroDescription: 'Protect agency financials and client confidentiality with enterprise-grade multi-tenancy and granular role controls. Shield retainer margins and bank settlements from contractors while empowering developers and designers with exact role-scoped tools.',
    badgeColor: 'zinc',
    iconName: 'Users2',
    status: 'Live in Product',
    tags: ['Cryptographic Tenant Isolation', '5-Tier Agency Role Matrix', 'Financial Value Masking', 'Zero Cross-Tenant Leakage'],
    stats: [
      { metric: '5 Tiers', label: 'Pre-Configured Agency Roles' },
      { metric: '100%', label: 'Cryptographic Tenant Isolation' },
      { metric: 'Zero', label: 'Cross-Client Data Leakage' }
    ],
    theOldWay: [
      'Sharing master admin credentials with contractors and interns because existing tools lack role-specific permissioning.',
      'Exposing sensitive client retainer totals, agency profit margins, and bank accounts to freelance developers and designers.',
      'Managing multiple client accounts within unsegmented databases, risking catastrophic cross-client data exposure.'
    ],
    theCoraWay: [
      'Deploy 5 pre-configured agency roles: Super Admin, Agency Owner, Project Lead, UI/UX Designer / Dev, and External Contractor.',
      'Mask sensitive commercial metrics automatically so delivery teams build tasks and write code without seeing billing rates.',
      'Enforce cryptographic database session scoping to guarantee complete isolation across every client workspace.'
    ],
    capabilities: [
      {
        title: 'Deploy 5-Tier Agency Role Presets',
        description: 'Assign pre-built roles configured specifically for digital studios, marketing agencies, and software dev teams in seconds.',
        tag: 'Role Presets'
      },
      {
        title: 'Mask Financial & Commercial Metrics',
        description: 'Hide retainer values, gross margins, project pricing, and bank accounts from junior staff and freelance talent.',
        tag: 'Financial Shield'
      },
      {
        title: 'Configure Granular Module Permissions',
        description: 'Grant or restrict access to specific features like Invoicing, E-Signature Vault, AI Agents, or API Keys per user seat.',
        tag: 'Permission Matrix'
      },
      {
        title: 'Audit Security Sessions & Access Logs',
        description: 'Inspect real-time login sessions, IP locations, API requests, and file download timestamps across your agency.',
        tag: 'Audit Telemetry'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Invite Team Member or Contractor',
        description: 'Enter their email address and select an agency role (e.g. Project Lead, Dev, Designer, Contractor).'
      },
      {
        step: '02',
        title: 'Fine-Tune Permissions & Visibility',
        description: 'Toggle module access, enable financial masking, and assign specific client project workspaces.'
      },
      {
        step: '03',
        title: 'Deploy Secure Scoped Access',
        description: 'Team members log in with instant access restricted strictly to their assigned operational boards.'
      }
    ],
    toolsReplaced: [
      { name: 'Okta / Auth0 Enterprise Addon', category: 'User Access Control', monthlySavingsUSD: 50, monthlySavingsINR: 4200 },
      { name: '1Password Team Vaults', category: 'Shared Access', monthlySavingsUSD: 20, monthlySavingsINR: 1700 },
      { name: 'Custom User Role Plugins', category: 'WordPress Roles', monthlySavingsUSD: 10, monthlySavingsINR: 850 }
    ],
    faqs: [
      {
        question: 'Can I invite temporary freelance developers for a single sprint?',
        answer: 'Yes. Invite external contractors with scoped access that automatically expires when their sprint milestone wraps up.'
      },
      {
        question: 'Can project managers create client proposals without viewing total agency revenue?',
        answer: 'Yes. Grant proposal creation rights while locking company-wide P&L dashboards and bank settlement accounts.'
      },
      {
        question: 'Does Cora enforce two-factor authentication (2FA)?',
        answer: 'Yes. Secure all accounts with mandatory 2FA using Google Authenticator, 1Password, or standard TOTP apps.'
      },
      {
        question: 'How does Cora ensure complete client workspace separation?',
        answer: 'All database queries enforce cryptographic tenant scoping to ensure zero data leakage between client workspaces.'
      }
    ],
    relatedFeatureSlugs: ['super-admin', 'crew-dispatch', 'media-hub'],
    mockup: {
      windowTitle: 'Cora Multi-Tenant RBAC & Agency Access Governance',
      activeTabLabel: 'Team Roles & Permissions Matrix',
      tabs: [
        { id: 'members', label: 'Team Members', badge: '6 Users' },
        { id: 'roles', label: 'Role Permissions Matrix' },
        { id: 'security', label: 'Active Sessions & 2FA' }
      ],
      headerTitle: 'Role-Based Access Control & Tenant Security',
      headerSubtitle: 'Active Workspace: Apex Digital Agency (Tenant ID: #TN-98124)',
      primaryActionLabel: '+ Invite Team Member',
      metric1: { label: 'Active Users', value: '6 Team Seats' },
      metric2: { label: 'Security Score', value: '100% 2FA' },
      metric3: { label: 'Financial Masking', value: 'Active (3 Users)' },
      tableHeaders: ['User Name / Email', 'Agency Role', 'Financial Visibility', 'Assigned Modules', 'Status'],
      rows: [
        {
          col1: 'Aarav Mehta (aarav@apexdigital.in)',
          col2: 'Super Administrator',
          col3: 'Full Access (P&L, GST)',
          statusText: 'All 20 Modules',
          statusType: 'success',
          actionText: 'Manage'
        },
        {
          col1: 'Kavya Patel (kavya.dev@apexdigital.in)',
          col2: 'Lead Full-Stack Dev',
          col3: 'Masked (Zero Visibility)',
          statusText: 'Task Board, Sprints, MCP',
          statusType: 'neutral',
          actionText: 'Edit Role'
        },
        {
          col1: 'Rohan Verma (rohan.ux@apexdigital.in)',
          col2: 'Senior UI/UX Designer',
          col3: 'Masked (Zero Visibility)',
          statusText: 'Canvas, Media Hub, Review',
          statusType: 'neutral',
          actionText: 'Edit Role'
        }
      ]
    }
  },
  {
    slug: 'email-smtp',
    title: 'Custom Domain Email & SMTP Diagnostic Delivery Suite',
    shortTitle: 'Email & SMTP',
    category: 'platform',
    categoryLabel: 'Platform & Governance',
    tagline: 'Connect custom domain SMTP, verify SPF/DKIM/DMARC health, and dispatch branded proposals with 99.8% inbox deliverability.',
    heroDescription: 'Dispatch high-converting client proposals, sprint kickoff notices, milestone sign-offs, and GST tax invoices directly from your verified agency domain (e.g. proposals@youragency.com). Validate DNS health in real-time and eliminate third-party email API fees.',
    badgeColor: 'sky',
    iconName: 'Mail',
    status: 'Live in Product',
    tags: ['Custom Domain SMTP', 'Visual HTML Composer', 'SPF/DKIM/DMARC Health', 'Live Outbox Delivery Logs'],
    stats: [
      { metric: '99.8%', label: 'Inbox Deliverability' },
      { metric: '0 Spam', label: 'SPF & DKIM DNS Validation' },
      { metric: '100%', label: 'Custom Domain White-Label' }
    ],
    theOldWay: [
      'Sending high-ticket client proposals from generic personal @gmail.com accounts that destroy agency credibility.',
      'Watching critical invoice notifications and milestone sign-offs get lost in client spam folders due to broken DNS records.',
      'Paying $30–$80/month for complex transactional email services like SendGrid or Mailgun on top of your existing tools.'
    ],
    theCoraWay: [
      'Connect your Google Workspace, Microsoft 365, Zoho Mail, or custom cPanel mail server via secure TLS/SSL in 60 seconds.',
      'Run one-click SMTP diagnostics to verify SPF, DKIM, and DMARC DNS health before sending live client communications.',
      'Design polished, responsive HTML email templates with dynamic tags: {{client_name}}, {{sprint_name}}, {{invoice_total}}, and {{sign_link}}.'
    ],
    capabilities: [
      {
        title: 'Connect Custom SMTP Mail Servers',
        description: 'Route outgoing agency emails through your authenticated Google Workspace, Microsoft 365, or private server.',
        tag: 'Custom SMTP'
      },
      {
        title: 'Compose Responsive HTML Templates',
        description: 'Build sleek, branded email notifications styled with agency typography, clear CTA buttons, and logo headers.',
        tag: 'HTML Composer'
      },
      {
        title: 'Validate SPF, DKIM & DMARC Health',
        description: 'Diagnose DNS records and test server latency in one click to ensure consistent primary inbox placement.',
        tag: 'DNS Diagnostics'
      },
      {
        title: 'Track Real-Time Outbox Telemetry',
        description: 'Monitor live email statuses (Sent, Delivered, Opened, Bounced) with exact server response codes and timestamps.',
        tag: 'Outbox Telemetry'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Configure SMTP Credentials',
        description: 'Enter your mail host, port, username, and secure app password with TLS/SSL encryption.'
      },
      {
        step: '02',
        title: 'Run One-Click DNS Diagnostic',
        description: 'Verify SPF, DKIM, and DMARC records and dispatch an instant test email to confirm deliverability.'
      },
      {
        step: '03',
        title: 'Automate Client Communications',
        description: 'Send proposals, GST invoices, and SOW signature requests automatically under your verified agency domain.'
      }
    ],
    toolsReplaced: [
      { name: 'SendGrid Pro', category: 'Transactional Email', monthlySavingsUSD: 20, monthlySavingsINR: 1700 },
      { name: 'Mailgun Foundation', category: 'Email API', monthlySavingsUSD: 35, monthlySavingsINR: 2900 },
      { name: 'Postmark Basic', category: 'Email Delivery', monthlySavingsUSD: 15, monthlySavingsINR: 1250 }
    ],
    faqs: [
      {
        question: 'Can I connect Google Workspace or Zoho Mail using an App Password?',
        answer: 'Yes. Connect Google Workspace, Microsoft 365, and Zoho Mail in seconds using standard secure App Passwords.'
      },
      {
        question: 'How does Cora ensure our client proposals land in the primary inbox?',
        answer: 'Sending via your authenticated domain with verified SPF and DKIM records guarantees near 100% inbox deliverability.'
      },
      {
        question: 'Can I preview email templates across mobile and desktop devices?',
        answer: 'Yes. The template builder includes side-by-side desktop and mobile smartphone rendering previews.'
      },
      {
        question: 'Can I see when a prospective client opens our proposal email?',
        answer: 'Yes. The live outbox logs provide real-time timestamps for email opens and link clicks.'
      }
    ],
    relatedFeatureSlugs: ['lead-crm', 'esign-vault', 'gst-invoicing'],
    mockup: {
      windowTitle: 'Cora Email & SMTP Diagnostic Suite',
      activeTabLabel: 'SMTP Outbox & Server Health',
      tabs: [
        { id: 'logs', label: 'Outbox Delivery Logs', badge: '100% Delivered' },
        { id: 'templates', label: 'Email Templates' },
        { id: 'settings', label: 'SMTP Config & DNS' }
      ],
      headerTitle: 'Custom Business Email Connection',
      headerSubtitle: 'Active Host: smtp.gmail.com:587 • Sender: proposals@apexdigital.in (SPF/DKIM Valid)',
      primaryActionLabel: 'Send Test Email',
      metric1: { label: 'Delivery Rate', value: '99.8%' },
      metric2: { label: 'Emails Sent', value: '1,420 this mo' },
      metric3: { label: 'Server Latency', value: '240ms' },
      tableHeaders: ['Recipient', 'Subject Line', 'Template Used', 'Timestamp', 'Delivery Status'],
      rows: [
        {
          col1: 'procurement@raymond.in',
          col2: 'Action Required: Master Services Agreement Ready for Signature',
          col3: 'E-Sign Contract Notification',
          statusText: 'Opened (2 mins ago)',
          statusType: 'success',
          actionText: 'View Raw Log'
        },
        {
          col1: 'finance@titan.co.in',
          col2: 'Tax Invoice #INV-2026-0103 (Q3 Dev Retainer) + UPI QR',
          col3: 'GST Tax Invoice Dispatch',
          statusText: 'Delivered (Inbox)',
          statusType: 'success',
          actionText: 'View Raw Log'
        },
        {
          col1: 'growth@nykaa.com',
          col2: 'Confirmed: Q4 Performance Marketing Sprint Kickoff',
          col3: 'Sprint Confirmation',
          statusText: 'Delivered (Inbox)',
          statusType: 'success',
          actionText: 'View Raw Log'
        }
      ]
    }
  },
  {
    slug: 'pwa-push',
    title: 'Progressive Web App (PWA) & Web Push Engine',
    shortTitle: 'Installable PWA',
    category: 'platform',
    categoryLabel: 'Platform & Governance',
    tagline: 'Install native mobile agency OS, receive real-time VAPID push alerts, and manage client sprints with zero tap delay.',
    heroDescription: 'Run your entire agency from your iPhone or Android home screen with zero app store friction. Receive instant lock-screen push alerts for accepted proposals, signed SOWs, and UPI retainer payments with sub-50ms offline-ready screen hydration.',
    badgeColor: 'emerald',
    iconName: 'Smartphone',
    status: 'Live in Product',
    tags: ['Standalone Mobile PWA', 'VAPID Lock-Screen Push', 'Sub-50ms Offline Cache', 'Zero 300ms Tap Delay'],
    stats: [
      { metric: '< 50ms', label: 'Screen Hydration Speed' },
      { metric: '0ms', label: 'Hardware Tap Delay' },
      { metric: 'iOS & Android', label: 'Native Home Screen WebAPK' }
    ],
    theOldWay: [
      'Struggling with clunky web dashboards that reload on every tap and force browser navigation bars on mobile screens.',
      'Missing urgent client approvals and payment receipts because standard mobile web pages cannot dispatch background push alerts.',
      'Spending $25,000+ building and maintaining separate iOS and Android native apps that duplicate desktop features.'
    ],
    theCoraWay: [
      'Install a standalone PWA with full-screen gestures, in-app link retention, and zero browser chrome distractions.',
      'Receive instant VAPID lock-screen push alerts when clients sign SOWs, approve sprint deliverables, or settle invoices.',
      'Enjoy sub-400ms service worker caching for instantaneous page hydration, offline access, and 60fps mobile snappiness.'
    ],
    capabilities: [
      {
        title: 'Install Standalone Home Screen App',
        description: 'Add Cora to iOS Safari or Android Chrome with dynamic high-resolution app icons and instant splash screens.',
        tag: 'PWA Container'
      },
      {
        title: 'Dispatch Real-Time VAPID Push Alerts',
        description: 'Trigger instant lock-screen notifications for inbound agency leads, signed contracts, and UPI retainer receipts.',
        tag: 'Lock-Screen Push'
      },
      {
        title: 'Eliminate Mobile Tap Delays',
        description: 'Hardware-accelerated touch handling removes the standard 300ms browser delay for fluid, native-grade responsiveness.',
        tag: 'Touch Snappiness'
      },
      {
        title: 'Retain Standalone In-App Navigation',
        description: 'Intelligent routing retains all internal links inside the standalone window, preventing browser breakouts.',
        tag: 'Link Retention'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Open Mobile Agency Portal',
        description: 'Navigate to app.heycora.in on iOS Safari or Android Chrome.'
      },
      {
        step: '02',
        title: 'Add to Home Screen in 1 Tap',
        description: 'Tap "Add to Home Screen" to install the native lightweight PWA with dynamic versioned icons.'
      },
      {
        step: '03',
        title: 'Enable Push Notifications',
        description: 'Grant push permissions to receive real-time lead alerts, sprint milestones, and payment confirmations.'
      }
    ],
    toolsReplaced: [
      { name: 'OneSignal Push Pro', category: 'Web Push Service', monthlySavingsUSD: 99, monthlySavingsINR: 8200 },
      { name: 'Native iOS/Android App Fees', category: 'App Store Dev Fees', monthlySavingsUSD: 50, monthlySavingsINR: 4200 },
      { name: 'Pusher Channels', category: 'Real-Time Alerts', monthlySavingsUSD: 29, monthlySavingsINR: 2400 }
    ],
    faqs: [
      {
        question: 'Does the PWA support Apple iOS push notifications?',
        answer: 'Yes. iOS 16.4+ natively supports Web Push notifications for PWAs installed on the home screen.'
      },
      {
        question: 'Can I review sprint task boards and client scopes when offline?',
        answer: 'Yes. The Service Worker caches active project boards, SOWs, and milestone checklists for instant offline review.'
      },
      {
        question: 'How do app updates deploy in PWA mode?',
        answer: 'Cora features an automatic in-app update banner that refreshes assets in sub-300ms with zero downtime.'
      },
      {
        question: 'Does the PWA take up significant storage on mobile devices?',
        answer: 'No. The entire Cora PWA core is under 5MB, making it 50x lighter than typical native mobile applications.'
      }
    ],
    relatedFeatureSlugs: ['master-calendar', 'crew-dispatch', 'super-admin'],
    mockup: {
      windowTitle: 'Cora Progressive Web App (PWA) Lifecycle Monitor',
      activeTabLabel: 'PWA & Push Notification Console',
      tabs: [
        { id: 'pwa-status', label: 'PWA Manifest & Service Worker', badge: 'v4.2.0 Active' },
        { id: 'push-tokens', label: 'Push Subscribers', badge: '12 Devices' },
        { id: 'offline-cache', label: 'Cache Storage' }
      ],
      headerTitle: 'Mobile PWA Engine & Real-Time Push Subscriptions',
      headerSubtitle: 'Manifest: /cora-manifest.json • Service Worker: cora-service-worker.js (Active)',
      primaryActionLabel: 'Send Test Push Alert',
      metric1: { label: 'Active Devices', value: '12 Devices' },
      metric2: { label: 'Avg Push Delivery', value: '180ms' },
      metric3: { label: 'Cache Size', value: '4.2 MB' },
      tableHeaders: ['Device / Browser', 'Platform', 'VAPID Push Status', 'Last Sync', 'Action'],
      rows: [
        {
          col1: 'iPhone 16 Pro (Mobile Safari Standalone)',
          col2: 'iOS 18.2 (PWA Installed)',
          col3: 'Subscribed & Active',
          statusText: 'Online (50ms)',
          statusType: 'success',
          actionText: 'Ping Device'
        },
        {
          col1: 'Samsung Galaxy S24 (Chrome WebAPK)',
          col2: 'Android 15 (WebAPK Installed)',
          col3: 'Subscribed & Active',
          statusText: 'Online (42ms)',
          statusType: 'success',
          actionText: 'Ping Device'
        },
        {
          col1: 'MacBook Pro M3 (Chrome Standalone PWA)',
          col2: 'macOS Sonoma',
          col3: 'Subscribed & Active',
          statusText: 'Online (20ms)',
          statusType: 'success',
          actionText: 'Ping Device'
        }
      ]
    }
  },
  {
    slug: 'docs-portal',
    title: 'Public Developer Documentation & Interactive API Playground',
    shortTitle: 'Docs Portal',
    category: 'platform',
    categoryLabel: 'Platform & Governance',
    tagline: 'Explore Notion-styled 3-column documentation, trigger ⌘K search, and test live REST APIs in the interactive browser playground.',
    heroDescription: 'Supercharge custom agency integrations with open developer documentation and interactive API explorers. Connect REST endpoints, subscribe to HMAC-signed webhooks, and orchestrate Model Context Protocol (MCP) agents using copy-paste code snippets.',
    badgeColor: 'blue',
    iconName: 'BookOpen',
    status: 'Live in Product',
    tags: ['Notion 3-Column Layout', '⌘K Command Search', 'Interactive REST Playground', 'HMAC Webhooks'],
    stats: [
      { metric: '100% Open', label: 'REST API Specifications' },
      { metric: '⌘K Search', label: 'Sub-20ms Fuzzy Index' },
      { metric: '5 Languages', label: 'cURL, JS, TS, Python, PHP' }
    ],
    theOldWay: [
      'Digging through outdated, confusing PDF manuals that fail to reflect recent software updates and API changes.',
      'Getting trapped in closed proprietary platforms that restrict your ability to export data or build custom client automations.',
      'Wasting developer hours guessing JSON request payloads and webhook structures without interactive testing tools.'
    ],
    theCoraWay: [
      'Navigate a clean Notion-styled 3-column architecture with instant ⌘K search and organized integration tutorials.',
      'Test live REST endpoints directly in the browser playground without opening external tools like Postman.',
      'Copy production-ready snippets in cURL, JavaScript, TypeScript, Python, and PHP to automate agency workflows.'
    ],
    capabilities: [
      {
        title: 'Navigate 3-Column Notion Architecture',
        description: 'Explore structured sidebar categories, clear Markdown documentation, and right-hand anchor table of contents.',
        tag: 'Notion Aesthetic'
      },
      {
        title: 'Search Fast with ⌘K Command Palette',
        description: 'Search across all endpoints, webhooks, and setup guides with sub-20ms client-side fuzzy indexing.',
        tag: 'Instant Search'
      },
      {
        title: 'Execute Live in REST Playground',
        description: 'Validate API tokens, request headers, and JSON payloads directly within the built-in browser console.',
        tag: 'Live Testing'
      },
      {
        title: 'Subscribe to HMAC-Signed Webhooks',
        description: 'Receive secure HMAC-SHA256 event payloads for Lead Captured, Contract Signed, and Retainer Paid triggers.',
        tag: 'Real-Time Webhooks'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Browse Guides & Endpoints',
        description: 'Navigate to /docs to explore guides for Framer embeds, GST invoicing, or MCP agent setups.'
      },
      {
        step: '02',
        title: 'Generate Scoped API Keys',
        description: 'Create secure API tokens in workspace settings with custom read and write access permissions.'
      },
      {
        step: '03',
        title: 'Execute Snippets & Automate',
        description: 'Copy code snippets into your codebase to automate contract generation, CRM intake, and billing.'
      }
    ],
    toolsReplaced: [
      { name: 'GitBook Pro', category: 'Documentation CMS', monthlySavingsUSD: 29, monthlySavingsINR: 2400 },
      { name: 'ReadMe.io Starter', category: 'Developer Hub', monthlySavingsUSD: 99, monthlySavingsINR: 8200 },
      { name: 'Mintlify Pro', category: 'API Docs Platform', monthlySavingsUSD: 40, monthlySavingsINR: 3300 }
    ],
    faqs: [
      {
        question: 'Where can I access the developer documentation?',
        answer: 'Access the complete documentation publicly at https://heycora.in/docs with interactive guides and live examples.'
      },
      {
        question: 'Are webhooks supported for external agency workflows?',
        answer: 'Yes. Cora dispatches secure HMAC-SHA256 signed webhooks for deal closures, contract signatures, and payment events.'
      },
      {
        question: 'Can I consume Cora APIs across modern frameworks like Next.js and Python?',
        answer: 'Yes. Standard RESTful JSON endpoints integrate seamlessly into Node.js, Next.js, Python, PHP, and cURL.'
      },
      {
        question: 'Is rate limiting enforced on API tokens?',
        answer: 'Yes. Generous rate limits (120 requests/minute) protect workspace stability while supporting high-throughput automations.'
      }
    ],
    relatedFeatureSlugs: ['rag-mcp', 'super-admin', 'form-builder'],
    mockup: {
      windowTitle: 'Cora Developer Documentation & API Playground',
      activeTabLabel: 'API Reference: /api/v1/invoices/create',
      tabs: [
        { id: 'endpoints', label: 'REST API Specs', badge: 'v1.4' },
        { id: 'webhooks', label: 'Webhook Registry' },
        { id: 'mcp-guide', label: 'MCP Server Setup' }
      ],
      headerTitle: 'Cora Agency OS Developer Documentation',
      headerSubtitle: 'Base URL: https://api.heycora.in/v1 • Auth: Bearer cora_live_sec_...',
      primaryActionLabel: 'Test in Playground',
      metric1: { label: 'API Uptime', value: '99.98%' },
      metric2: { label: 'Avg Latency', value: '42ms' },
      metric3: { label: 'Endpoints', value: '28 Live' },
      tableHeaders: ['Method & Path', 'Description', 'Auth Scope', 'Rate Limit', 'Status'],
      rows: [
        {
          col1: 'POST /api/v1/contracts/sign',
          col2: 'Create and seal SHA-256 e-signature contract for client SOW',
          col3: 'write:contracts',
          statusText: '200 OK (52ms)',
          statusType: 'success',
          actionText: 'View Schema'
        },
        {
          col1: 'POST /api/v1/invoices/generate-gst',
          col2: 'Compute 18% GST and generate dynamic UPI QR for retainer billing',
          col3: 'write:invoices',
          statusText: '200 OK (38ms)',
          statusType: 'success',
          actionText: 'View Schema'
        },
        {
          col1: 'GET /api/v1/sprints/availability',
          col2: 'Query dev team capacity and project milestone schedules',
          col3: 'read:sprints',
          statusText: '200 OK (24ms)',
          statusType: 'success',
          actionText: 'View Schema'
        }
      ]
    }
  },
  {
    slug: 'super-admin',
    title: 'Super Admin Multi-Tenant Governance & Quota Suite',
    shortTitle: 'Super Admin',
    category: 'platform',
    categoryLabel: 'Platform & Governance',
    tagline: 'Command global workspaces, toggle feature modules, monitor AI token quotas, and inspect security audit logs.',
    heroDescription: 'Take complete command over all agency client workspaces, tenant provisioning, feature flags, and multi-model AI token consumption. Inspect real-time server health, enforce security policies, and govern client accounts from a single centralized console.',
    badgeColor: 'zinc',
    iconName: 'Settings',
    status: 'Live in Product',
    tags: ['Global Workspace Governance', 'Feature Module Toggles', 'AI Token Quota Monitor', 'Security Audit Logs'],
    stats: [
      { metric: '100% Control', label: 'Centralized Governance' },
      { metric: 'Real-Time', label: 'AI Token Spend Tracking' },
      { metric: '< 5 Secs', label: 'Workspace Auto-Provisioning' }
    ],
    theOldWay: [
      'Flying blind with zero visibility into team feature adoption or runaway AI token spending across client accounts.',
      'Executing manual database scripts to provision new agency client workspaces or adjust tier permissions.',
      'Lacking centralized audit logs to track unauthorized document access, credential changes, or financial data exports.'
    ],
    theCoraWay: [
      'Govern all workspaces from a centralized Super Admin console with 1-click tenant switching and live metrics.',
      'Track Gemini 3.5 Flash, Claude 3.5 Sonnet, and GPT-4o token consumption per client account in real-time.',
      'Toggle granular feature flags to enable specialized modules per agency branch or client account without code deploys.'
    ],
    capabilities: [
      {
        title: 'Provision Client Workspaces in Seconds',
        description: 'Spin up isolated, fully configured agency workspaces pre-seeded with industry blueprints in under 5 seconds.',
        tag: 'Auto-Provisioning'
      },
      {
        title: 'Monitor AI Token Quotas & Spend',
        description: 'Track prompt and completion token counts across models with automated soft-cap warnings to control costs.',
        tag: 'Token Analytics'
      },
      {
        title: 'Control Granular Feature Toggles',
        description: 'Activate specialized modules (WhatsApp Cloud API, Video Storyboard, MCP Servers) per tenant in one click.',
        tag: 'Feature Flags'
      },
      {
        title: 'Inspect Immutable Security Audit Trails',
        description: 'Maintain timestamped logs of all administrator actions, role updates, and financial exports across the platform.',
        tag: 'Security Audit'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Open Super Admin Console',
        description: 'Click the sticky sidebar admin popover widget and launch the Platform Governance Suite.'
      },
      {
        step: '02',
        title: 'Inspect Tenant Telemetry',
        description: 'Review active user sessions, token usage, revenue throughput, and database storage in real-time.'
      },
      {
        step: '03',
        title: 'Toggle Feature Modules & Quotas',
        description: 'Adjust token quotas or upgrade account tiers instantly with zero server restarts or downtime.'
      }
    ],
    toolsReplaced: [
      { name: 'Retool Custom Admin', category: 'Internal Tools', monthlySavingsUSD: 50, monthlySavingsINR: 4200 },
      { name: 'Datadog Log Monitor', category: 'Audit Logs', monthlySavingsUSD: 65, monthlySavingsINR: 5400 },
      { name: 'LaunchDarkly Starter', category: 'Feature Flags', monthlySavingsUSD: 25, monthlySavingsINR: 2000 }
    ],
    faqs: [
      {
        question: 'Who can access the Super Admin Governance Suite?',
        answer: 'Only designated Platform Super Administrators with verified credentials and mandatory multi-factor authentication.'
      },
      {
        question: 'Can Super Admins view confidential client contracts of other tenants?',
        answer: 'Cryptographic tenant isolation prevents unauthorized viewing of document contents while surfacing necessary operational metadata.'
      },
      {
        question: 'Can I enforce monthly AI token spending limits?',
        answer: 'Yes. Configure custom soft and hard caps per workspace to eliminate unexpected billing overages.'
      },
      {
        question: 'Are administrative changes recorded in immutable logs?',
        answer: 'Yes. Every feature toggle, quota update, and tenant modification is logged with IP address and timestamp.'
      }
    ],
    relatedFeatureSlugs: ['rbac-system', 'docs-portal', 'ai-cofounder'],
    mockup: {
      windowTitle: 'Cora Super Admin Multi-Tenant Governance Suite',
      activeTabLabel: 'Workspace Overview & Quota Monitor',
      tabs: [
        { id: 'workspaces', label: 'All Workspaces', badge: '4 Active' },
        { id: 'tokens', label: 'AI Token Quotas' },
        { id: 'audit', label: 'Security Event Logs' }
      ],
      headerTitle: 'Global Multi-Tenant Platform Governance',
      headerSubtitle: 'Super Admin: Platform Director (admin@cora.local) • Status: Healthy (0.36s Response)',
      primaryActionLabel: '+ Provision Workspace',
      metric1: { label: 'Active Workspaces', value: '4 Tenants' },
      metric2: { label: 'Monthly AI Runs', value: '142,800 Runs' },
      metric3: { label: 'System Health', value: '100% Uptime' },
      tableHeaders: ['Workspace Name', 'Industry Vertical', 'Plan Tier', 'AI Token Usage', 'Status'],
      rows: [
        {
          col1: 'Apex Digital Agency — Mumbai',
          col2: 'Digital Marketing & Growth',
          col3: 'Enterprise Annual (₹29,990)',
          statusText: '18,400 / 25k Tokens',
          statusType: 'success',
          actionText: 'Manage'
        },
        {
          col1: 'Nova Design & Dev Studio — BLR',
          col2: 'Software & UI/UX Studio',
          col3: 'Professional Annual (₹19,990)',
          statusText: '12,200 / 20k Tokens',
          statusType: 'success',
          actionText: 'Manage'
        },
        {
          col1: 'Vanguard Creative Media',
          col2: 'Commercial Production',
          col3: 'Starter Annual (₹9,990)',
          statusText: '4,100 / 8k Tokens',
          statusType: 'success',
          actionText: 'Manage'
        }
      ]
    }
  },
  {
    slug: 'onboarding-wizard',
    title: 'Guided Onboarding, Setup Wizard & Industry Seeding',
    shortTitle: 'Onboarding Wizard',
    category: 'platform',
    categoryLabel: 'Platform & Governance',
    tagline: 'Launch your agency operating system in under 3 minutes with pre-seeded rate cards, GST retainers, and 3D brand pedestals.',
    heroDescription: 'Get your entire agency up and running in under 3 minutes. Automatically seed tailored rate cards, GST retainers, sprint task boards, and SOW contract templates built specifically for Digital Marketing Agencies, Software Dev Studios, UI/UX Design Firms, or Creative Media.',
    badgeColor: 'amber',
    iconName: 'Compass',
    status: 'Live in Product',
    tags: ['3-Minute Setup Stepper', '3D Brand Pedestal', 'Industry Blueprint Seeding', 'Zero Friction Flow'],
    stats: [
      { metric: '< 3 Mins', label: 'Complete Setup Time' },
      { metric: '5 Verticals', label: 'Pre-Seeded Blueprints' },
      { metric: 'Zero Friction', label: 'Instant Rate Cards' }
    ],
    theOldWay: [
      'Spending weeks manually configuring empty enterprise CRMs before sending your first client proposal or SOW.',
      'Drafting contract terms, milestone schedules, and GST rate cards from blank documents with endless repetitive work.',
      'Watching frustrated team members abandon complex software setups and revert to messy WhatsApp chats.'
    ],
    theCoraWay: [
      'Pick your agency vertical (Digital Marketing, Dev Studio, UI/UX Design, Creative Media) and launch with pre-seeded data.',
      'Upload your agency logo to render immediately on an interactive monochromatic 3D brand pedestal.',
      'Deploy ready-to-use rate cards, 18% GST calculation engines, and SHA-256 e-signature contract templates right out of the box.'
    ],
    capabilities: [
      {
        title: 'Seed Tailored Agency Blueprints',
        description: 'Auto-populate specialized service packages, SAC codes, SOW terms, and pipeline stages designed for your agency.',
        tag: 'Industry Blueprints'
      },
      {
        title: 'Render Interactive 3D Brand Pedestals',
        description: 'Upload your agency logo and watch it rendered on an interactive monochromatic rotating 3D pedestal.',
        tag: '3D Brand Engine'
      },
      {
        title: 'Configure 18% GST & Dynamic UPI',
        description: 'Add your GSTIN and UPI ID to enable automated tax invoice generation and instant bank payments in 45 seconds.',
        tag: 'Fast Billing Setup'
      },
      {
        title: 'Explore Interactive Sandbox Workspaces',
        description: 'Test sample deals, sprint milestones, and signed contracts before onboarding real clients.',
        tag: 'Guided Sandbox'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Select Your Agency Vertical',
        description: 'Choose Digital Marketing, Software & Dev, UI/UX Design, or Creative Media Production.'
      },
      {
        step: '02',
        title: 'Upload Logo & Payment Handles',
        description: 'Set your agency logo, custom domain, and UPI ID for white-labeled client proposals and invoices.'
      },
      {
        step: '03',
        title: 'Launch Your Pre-Loaded OS',
        description: 'Your workspace opens with pre-loaded rate cards, e-sign contracts, and sprint pipelines ready to deploy.'
      }
    ],
    toolsReplaced: [
      { name: 'Expensive Onboarding Consultants', category: 'Software Setup', monthlySavingsUSD: 250, monthlySavingsINR: 20000 },
      { name: 'Template Packs & Contract PDFs', category: 'Legal Templates', monthlySavingsUSD: 99, monthlySavingsINR: 8000 },
      { name: 'Zapier Setup Services', category: 'Integration Work', monthlySavingsUSD: 150, monthlySavingsINR: 12000 }
    ],
    faqs: [
      {
        question: 'Can I customize or change my industry blueprint after completing onboarding?',
        answer: 'Yes. Switch or adjust all rate cards, contract terms, and pipeline stages anytime in workspace settings.'
      },
      {
        question: 'Can I easily wipe sample demo records once I begin using real client data?',
        answer: 'Yes. A single "Clear Sample Data" button removes demo records while preserving all configured workspace settings.'
      },
      {
        question: 'How long does the guided setup take to complete?',
        answer: 'Most agency founders complete the 4-step guided onboarding in under 3 minutes.'
      },
      {
        question: 'Can I invite team members during the setup flow?',
        answer: 'Yes. Invite team members with assigned roles and module permissions directly in step 3 of the wizard.'
      }
    ],
    relatedFeatureSlugs: ['super-admin', 'lead-crm', 'gst-invoicing'],
    mockup: {
      windowTitle: 'Cora Guided Onboarding & Workspace Provisioning',
      activeTabLabel: 'Step 2 of 4: Industry & Brand Identity',
      tabs: [
        { id: 'wizard', label: 'Setup Stepper', badge: 'Step 2 of 4' },
        { id: 'pedestal', label: '3D Pedestal Preview' },
        { id: 'templates', label: 'Pre-Seeded Assets' }
      ],
      headerTitle: 'Agency Workspace Customization & Blueprint Seeding',
      headerSubtitle: 'Selected Blueprint: Software & Digital Marketing Agency Studio',
      primaryActionLabel: 'Save & Continue →',
      metric1: { label: 'Setup Progress', value: '50% Complete' },
      metric2: { label: 'Est. Time Left', value: '1m 15s' },
      metric3: { label: 'Pre-Seeded Items', value: '18 Blueprints' },
      tableHeaders: ['Setup Step', 'Configuration Area', 'Status', 'Pre-Seeded Data', 'Action'],
      rows: [
        {
          col1: '1. Select Vertical',
          col2: 'Industry Specialty Blueprint',
          col3: 'Software & Dev Studio',
          statusText: 'Completed',
          statusType: 'success',
          actionText: 'Change'
        },
        {
          col1: '2. Brand Identity & Logo',
          col2: '3D Pedestal & Branded Header',
          col3: 'Logo Uploaded (agency_logo.svg)',
          statusText: 'Active Step',
          statusType: 'warning',
          actionText: 'Configure'
        },
        {
          col1: '3. 18% GST & UPI Handle',
          col2: 'Automated Retainer Styling',
          col3: 'Pending Input',
          statusText: 'Next Step',
          statusType: 'info',
          actionText: 'Start'
        }
      ]
    }
  }
];

export const UPCOMING_MODULES: RoadmapModule[] = [
  {
    id: 'whatsapp-cloud',
    title: 'Native WhatsApp Business Cloud API',
    desc: 'Engage clients with 2-way CRM chat, automated SOW reminders, instant proposal dispatches, and quick-reply scopes.',
    iconName: 'MessageCircle',
    eta: 'Q3 2026',
    status: 'Building Soon',
    categoryLabel: 'Sales & Client CRM'
  },
  {
    id: 'photo-proofing',
    title: 'Client Deliverable & Figma Proofing Portal 2.0',
    desc: 'Collect client feedback with pin-drop annotations, deliverable approvals, version comparisons, and sign-off workflows.',
    iconName: 'ImageIcon',
    eta: 'Q3 2026',
    status: 'Building Soon',
    categoryLabel: 'Finance & Media',
    industries: ['tech_software', 'design_studios', 'media_studios']
  },
  {
    id: 'integrated-payments',
    title: 'Integrated Payment Gateways (Auto-Reconcile)',
    desc: 'Collect client retainer payments via Razorpay, Stripe, and UPI links with instant automated ledger reconciliation.',
    iconName: 'CreditCard',
    eta: 'Q3 2026',
    status: 'Building Soon',
    categoryLabel: 'Finance & Assets'
  },
  {
    id: 'video-storyboard',
    title: 'AI Video Script & Ad Storyboard Engine',
    desc: 'Generate high-converting performance ad scripts, motion design storyboards, and viral short-form video concepts in 1 click.',
    iconName: 'Video',
    eta: 'Q4 2026',
    status: 'Building Soon',
    categoryLabel: 'Intelligence & AI',
    industries: ['marketing_growth', 'media_studios']
  },
  {
    id: 'multi-branch',
    title: 'Multi-Branch & Global Agency Workspace System',
    desc: 'Manage multiple agency branches, international client accounts, consolidated P&L reporting, and shared creative talent.',
    iconName: 'GitBranch',
    eta: 'Q4 2026',
    status: 'Building Soon',
    categoryLabel: 'Platform & Governance'
  },
  {
    id: 'voice-ai-agent',
    title: 'Autonomous Voice AI Lead Qualification Agent',
    desc: 'Deploy conversational Voice AI agents via ElevenLabs & Twilio to qualify inbound agency leads and book discovery calls.',
    iconName: 'PhoneCall',
    eta: 'Q4 2026',
    status: 'Building Soon',
    categoryLabel: 'Intelligence & AI'
  },
  {
    id: 'tally-zoho-export',
    title: 'Automated Accounting & Tally/Zoho Export',
    desc: 'Export GSTR-1 ready sales ledgers and client retainer data in structured XML/JSON format for Tally Prime and Zoho Books.',
    iconName: 'FileSpreadsheet',
    eta: 'Q4 2026',
    status: 'Building Soon',
    categoryLabel: 'Finance & Assets'
  },
  {
    id: 'client-mobile-app',
    title: 'White-Labeled Client Mobile Companion App',
    desc: 'Provide agency clients a native iOS & Android portal to approve deliverables, sign SOWs, track sprints, and pay invoices.',
    iconName: 'TabletSmartphone',
    eta: 'Q1 2027',
    status: 'Building Soon',
    categoryLabel: 'Platform & Governance'
  }
];
