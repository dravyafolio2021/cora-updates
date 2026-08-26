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
    label: 'All Workspaces', 
    shortLabel: 'All', 
    subtitle: 'Universal Business Stack', 
    iconName: 'Briefcase',
    badge: 'Universal'
  },
  { 
    id: 'photo_film', 
    label: 'Photo & Film Production', 
    shortLabel: 'Photo & Film', 
    subtitle: 'Commercial, Video & Wedding Studios', 
    iconName: 'Clapperboard',
    badge: 'Call Sheets • Gear'
  },
  { 
    id: 'real_estate', 
    label: 'Real Estate Media', 
    shortLabel: 'Real Estate', 
    subtitle: 'Architecture, MLS & Drone Walkthroughs', 
    iconName: 'Building',
    badge: 'MLS • GEO SEO'
  },
  { 
    id: 'creative_agencies', 
    label: 'Creative & Content Agencies', 
    shortLabel: 'Agencies', 
    subtitle: 'Design, Content & Social Media Teams', 
    iconName: 'Palette',
    badge: 'Portals • White-Label'
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
    tagline: 'Multi-turn conversational memory, proactive operational execution, and studio automation.',
    heroDescription: 'An autonomous executive assistant integrated directly into your workspace. Execute natural language database queries, automate booking follow-ups, draft high-ticket commercial proposals, and trigger studio actions through a 6-tier fallback intelligence engine.',
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
      'Copy-pasting context across 5 disconnected ChatGPT tabs without access to your CRM database.',
      'Manual proposal calculation errors and forgotten follow-up emails costing ₹50,000+ per month.',
      'No ability for AI to actually execute database actions, update deal stages, or create invoices.'
    ],
    theCoraWay: [
      'Native database access to clients, contracts, calendar bookings, and equipment availability.',
      'Proactive suggestions when deals stall or client call-times conflict with existing shoot schedules.',
      'Direct one-click execution of actions: generate contracts, calculate 18% GST, and dispatch WhatsApp alerts.'
    ],
    capabilities: [
      {
        title: 'Contextual Studio Memory',
        description: 'Retains multi-turn conversation context across your entire studio history, client preferences, and past pricing agreements.',
        tag: 'Vector RAG Store'
      },
      {
        title: 'Natural Language Action Dispatch',
        description: 'Simply tell Cora "Book a 3-camera commercial shoot for Acme Corp next Friday at ₹1.5L" and let the engine create CRM deals, calendar slots, and draft contracts.',
        tag: 'Function Calling'
      },
      {
        title: '6-Tier Resilient Fallback Engine',
        description: 'Zero downtime routing across Gemini 3.5 Flash, Claude 3.5 Sonnet, GPT-4o, and specialized local fallbacks.',
        tag: 'High Availability'
      },
      {
        title: 'Automated Proposal & Copy Generator',
        description: 'Generates detailed commercial shoot scopes, equipment line-items, and licensing terms tailored to client budget tiers.',
        tag: 'Commercial Scopes'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Initiate or Ingest Prompt',
        description: 'Type or voice-dictate a studio request in the persistent bottom workspace command bar or AI drawer.'
      },
      {
        step: '02',
        title: 'Autonomous Database Reasoning',
        description: 'Cora queries your live CRM leads, calendar slots, gear inventory, and rate cards to construct an accurate action plan.'
      },
      {
        step: '03',
        title: 'One-Click Execution & Dispatch',
        description: 'Review the generated summary, edit parameters, and trigger instant delivery via WhatsApp, Email, or CRM stage movement.'
      }
    ],
    toolsReplaced: [
      { name: 'ChatGPT Plus', category: 'AI Copy & Ideas', monthlySavingsUSD: 20, monthlySavingsINR: 1999 },
      { name: 'Jasper / Copy.ai', category: 'Proposal Copy', monthlySavingsUSD: 49, monthlySavingsINR: 4200 },
      { name: 'Custom Zapier Workflows', category: 'Automation Scripts', monthlySavingsUSD: 30, monthlySavingsINR: 2500 }
    ],
    faqs: [
      {
        question: 'How does the AI Co-Founder access my studio data?',
        answer: 'Cora uses secure Model Context Protocol (MCP) tool bindings to query only the tenant-isolated data belonging to your workspace. Your financial ledgers and client documents are never used for public model training.'
      },
      {
        question: 'Can the AI trigger actions without my confirmation?',
        answer: 'No. Destructive or external communication actions (such as sending legal contracts or generating tax invoices) always render an interactive confirmation card for your approval.'
      },
      {
        question: 'What happens if a major LLM provider experiences an outage?',
        answer: 'Cora’s autonomous 6-tier fallback engine instantly reroutes execution to an alternate tier (e.g. from Claude Sonnet to Gemini 3.5 Flash or GPT-4o) with sub-100ms switchover.'
      },
      {
        question: 'Is voice input supported for hands-free studio use?',
        answer: 'Yes, full Voice-to-Scope audio transcription is built into the workspace interface for on-set and on-the-go shoot management.'
      }
    ],
    relatedFeatureSlugs: ['rag-mcp', 'voice-to-scope', 'lead-crm'],
    mockup: {
      windowTitle: 'Cora AI Co-Founder — Studio Intelligence Terminal',
      activeTabLabel: 'Operations Copilot',
      tabs: [
        { id: 'chat', label: 'Live Copilot', badge: 'Active' },
        { id: 'proposals', label: 'Proposal Scopes' },
        { id: 'automations', label: 'Active Workflows' }
      ],
      headerTitle: 'Autonomous Studio Intelligence',
      headerSubtitle: 'Connected to CRM, Master Calendar & 18% GST Invoicing engine',
      primaryActionLabel: '+ New Command',
      metric1: { label: 'Active AI Runs', value: '4,280 / 20k' },
      metric2: { label: 'Avg Latency', value: '380ms' },
      metric3: { label: 'Actions Executed', value: '142 this wk' },
      tableHeaders: ['Command / Prompt', 'Action Triggered', 'Target Entity', 'Status', 'Execution'],
      rows: [
        {
          col1: '"Draft commercial video proposal for Horizon Media"',
          col2: 'Create CRM Deal & Proposal PDF',
          col3: 'Horizon Media (₹2,40,000)',
          statusText: 'Completed',
          statusType: 'success',
          actionText: 'View Proposal'
        },
        {
          col1: '"Check gear conflicts for Saturday 4K Sony FX6 shoot"',
          col2: 'Inventory Scan & Conflict Guard',
          col3: 'Studio Kit #A + 3 Lenses',
          statusText: 'No Conflicts',
          statusType: 'info',
          actionText: 'View Schedule'
        },
        {
          col1: '"Send overdue WhatsApp reminder with UPI QR"',
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
    title: 'Content AI Suite & Studio Editorial Publisher',
    shortTitle: 'Content AI & SEO',
    category: 'intelligence',
    categoryLabel: 'Intelligence & AI',
    tagline: 'WYSIWYG rich text editor, GEO-targeted SEO generator, and instant search indexing.',
    heroDescription: 'Supercharge your organic search traffic with AI-assisted creative studio case studies, behind-the-scenes journal posts, and GEO-targeted client guides. Features automatic IndexNow search engine pinging.',
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
      'Spending 6+ hours manually writing studio case studies or hiring expensive external SEO copywriters.',
      'Waiting weeks for Google to discover new portfolio shoots and client galleries without instant indexing.',
      'Missing out on high-intent local search keywords (e.g. "Commercial studio hire Mumbai", "Fashion photographer Delhi").'
    ],
    theCoraWay: [
      'AI drafts comprehensive technical case studies from gear metadata, shoot call-sheets, and lighting setups.',
      'Instant IndexNow protocol integration automatically alerts Bing, Google, and Yandex the moment you publish.',
      'Built-in GEO-targeting generator injects localized semantic keywords and structured JSON-LD schemas.'
    ],
    capabilities: [
      {
        title: 'Full WYSIWYG Rich Text Suite',
        description: 'Clean typographic editor with heading hierarchies, syntax highlighting, callout cards, and inline gallery carousels.',
        tag: 'Editorial Workspace'
      },
      {
        title: 'GEO Local Search Engine',
        description: 'Target specific studio zones (Bandra West, Indiranagar, Connaught Place) with automated localized schema injection.',
        tag: 'Hyper-Local SEO'
      },
      {
        title: 'Instant IndexNow Protocols',
        description: 'One-click publish automatically triggers API calls to search engine bots for rapid zero-lag search indexing.',
        tag: 'Instant Discovery'
      },
      {
        title: 'Social Share Card Generator',
        description: 'Auto-generates high-contrast OpenGraph preview cards for Twitter/X, LinkedIn, and WhatsApp messaging.',
        tag: 'Automated Previews'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Select Shoot or Topic',
        description: 'Choose a completed client shoot or enter a creative topic (e.g. "Lighting Luxury Watch Commercials").'
      },
      {
        step: '02',
        title: 'Generate Structured Story',
        description: 'AI generates executive summary, lighting schematics, gear inventory used, and client testimonial excerpts.'
      },
      {
        step: '03',
        title: 'Publish & Auto-Index',
        description: 'Review in the WYSIWYG editor and publish. Cora pings search engines for instant indexing within minutes.'
      }
    ],
    toolsReplaced: [
      { name: 'SurferSEO', category: 'SEO Content Editor', monthlySavingsUSD: 89, monthlySavingsINR: 7500 },
      { name: 'Medium Publication Pro', category: 'Publishing CMS', monthlySavingsUSD: 15, monthlySavingsINR: 1250 },
      { name: 'RankMath / Yoast Pro', category: 'WordPress Plugins', monthlySavingsUSD: 12, monthlySavingsINR: 999 }
    ],
    faqs: [
      {
        question: 'Does the AI content sound generic or like a real creative professional?',
        answer: 'Cora’s prompts are specifically tuned for photography, film, and architectural vocabulary, avoiding corporate AI fluff.'
      },
      {
        question: 'What is IndexNow and how does it help my studio?',
        answer: 'IndexNow is a protocol developed by Microsoft and major search engines that notifies crawlers immediately when a page is updated, cutting indexing time from weeks to minutes.'
      },
      {
        question: 'Can I include image EXIF data and lighting diagrams?',
        answer: 'Yes. You can attach camera bodies, shutter speeds, f-stops, and studio lighting grids directly in the article metadata.'
      },
      {
        question: 'Is the generated HTML clean and semantic?',
        answer: 'Yes. All output adheres to W3C semantic standards with proper H1-H4 headings, alt tags, and clean class names.'
      }
    ],
    relatedFeatureSlugs: ['ai-cofounder', 'canvas-builder', 'docs-portal'],
    mockup: {
      windowTitle: 'Cora Content AI & SEO Studio',
      activeTabLabel: 'Article Editor: Luxury Watch Commercial',
      tabs: [
        { id: 'editor', label: 'WYSIWYG Draft', badge: 'SEO 98/100' },
        { id: 'geo', label: 'GEO Meta & Schema' },
        { id: 'indexnow', label: 'IndexNow Logs' }
      ],
      headerTitle: 'Behind the Scenes: Lighting 4K Macro Product Shots',
      headerSubtitle: 'Target Keywords: "Luxury product photography Mumbai", "Macro lighting commercial studio"',
      primaryActionLabel: 'Publish & Index',
      metric1: { label: 'SEO Score', value: '98 / 100' },
      metric2: { label: 'Reading Time', value: '4 mins' },
      metric3: { label: 'Index Status', value: 'Ready to Ping' },
      tableHeaders: ['Keyword / Entity', 'Density', 'Placement', 'Status', 'Search Impact'],
      rows: [
        {
          col1: 'Product Photography Mumbai',
          col2: '2.4% (8x)',
          col3: 'H1, Meta Title, 1st Para',
          statusText: 'Optimized',
          statusType: 'success',
          actionText: 'Audit'
        },
        {
          col1: 'Sony FX6 4K Macro Setup',
          col2: '1.8% (5x)',
          col3: 'H2, Gear Inventory Block',
          statusText: 'Optimized',
          statusType: 'success',
          actionText: 'Audit'
        },
        {
          col1: 'Local Studio Hire Bandra',
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
    tagline: 'MCP server, vector memory store, and living business context sync for hyper-personalized studio responses.',
    heroDescription: 'Turn your private business data into an intelligent context brain. Uses Model Context Protocol (MCP) and local vector embeddings to ground AI responses in your exact studio pricing, rate cards, and client history.',
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
      'Generic AI models giving bland corporate responses that know nothing about your pricing or studio rules.',
      'Having to re-type your rate cards, cancellation policies, and gear inventory into ChatGPT every session.',
      'High risk of proprietary client contracts and financial records being ingested for public AI training.'
    ],
    theCoraWay: [
      'Isolated vector memory store indexing your studio rate cards, past proposals, equipment lists, and SOPs.',
      'Standardized Model Context Protocol (MCP) tools allow AI agents to query your database dynamically.',
      'Strict tenant boundaries guarantee your business data is never leaked or shared with other workspaces.'
    ],
    capabilities: [
      {
        title: 'Model Context Protocol (MCP) Interface',
        description: 'Standardized agent protocol allowing LLMs to read studio data and invoke discrete system tools safely.',
        tag: 'MCP Standard'
      },
      {
        title: 'Vector Knowledge Ingestion',
        description: 'Drop in studio PDF rate cards, brand guidelines, and contract terms for automatic vectorization.',
        tag: 'Vector Search'
      },
      {
        title: 'Real-Time Dynamic Grounding',
        description: 'AI responses quote your exact studio policies, cancellation penalty percentages, and package prices.',
        tag: 'Accurate Data'
      },
      {
        title: 'Zero-Training Privacy Shield',
        description: 'All embeddings and context tokens are processed with enterprise zero-retention API guarantees.',
        tag: 'Data Sovereignty'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Upload Studio SOPs & Guidelines',
        description: 'Upload your studio rate cards, gear lists, and standard contract clauses into the Knowledge Base.'
      },
      {
        step: '02',
        title: 'Automatic Vector Indexing',
        description: 'Cora segments and embeds your text into high-dimensional vector representations in milliseconds.'
      },
      {
        step: '03',
        title: 'Context-Aware AI Execution',
        description: 'AI Co-Founder automatically retrieves relevant chunks when generating proposals or answering queries.'
      }
    ],
    toolsReplaced: [
      { name: 'Custom Pinecone / Weaviate', category: 'Vector Database', monthlySavingsUSD: 70, monthlySavingsINR: 5800 },
      { name: 'LangChain / LlamaIndex Cloud', category: 'RAG Frameworks', monthlySavingsUSD: 50, monthlySavingsINR: 4200 },
      { name: 'Notion AI Addon', category: 'Knowledge AI', monthlySavingsUSD: 10, monthlySavingsINR: 850 }
    ],
    faqs: [
      {
        question: 'Is my studio data used to train OpenAI, Anthropic, or Google models?',
        answer: 'No. All MCP API calls are executed via enterprise endpoints with strict zero-data-retention and zero-training policies.'
      },
      {
        question: 'What file types can I upload to the knowledge base?',
        answer: 'You can upload PDF rate cards, DOCX agreements, Markdown guides, TXT notes, and raw CSV price lists.'
      },
      {
        question: 'How fast is vector retrieval during live conversation?',
        answer: 'Semantic vector retrieval executes in under 80 milliseconds directly in the context pipeline.'
      },
      {
        question: 'Can I connect external MCP servers to Cora?',
        answer: 'Yes. Cora is built on the open Model Context Protocol standard, allowing custom tool integrations.'
      }
    ],
    relatedFeatureSlugs: ['ai-cofounder', 'voice-to-scope', 'docs-portal'],
    mockup: {
      windowTitle: 'Cora RAG Memory & MCP Knowledge Base',
      activeTabLabel: 'Studio Knowledge Vector Index',
      tabs: [
        { id: 'docs', label: 'Vector Knowledge Store', badge: '14 Indexed' },
        { id: 'mcp-tools', label: 'Active MCP Tools', badge: '9 Tools' },
        { id: 'query-test', label: 'Semantic Playground' }
      ],
      headerTitle: 'Studio Memory & Model Context Protocol (MCP)',
      headerSubtitle: 'Active Vector Store: 2,480 Chunks • Embedding Engine: text-embedding-3-small',
      primaryActionLabel: '+ Ingest Document',
      metric1: { label: 'Indexed Chunks', value: '2,480 Chunks' },
      metric2: { label: 'Search Latency', value: '48ms' },
      metric3: { label: 'MCP Tools', value: '9 Active' },
      tableHeaders: ['Document Title', 'Category', 'Chunks', 'Last Vectorized', 'Status'],
      rows: [
        {
          col1: 'Studio Rate Card & Commercial Pricing 2026.pdf',
          col2: 'Pricing & Packages',
          col3: '142 Chunks',
          statusText: 'Indexed & Ready',
          statusType: 'success',
          actionText: 'Inspect'
        },
        {
          col1: 'Standard Commercial Video Shoot Terms & Conditions.docx',
          col2: 'Legal & Contract Terms',
          col3: '88 Chunks',
          statusText: 'Indexed & Ready',
          statusType: 'success',
          actionText: 'Inspect'
        },
        {
          col1: 'Equipment Inventory & Gear Replacement Costs.csv',
          col2: 'Asset & Gear Rules',
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
    tagline: 'Hands-free voice transcription, structured shoot briefs, and instant proposal generation.',
    heroDescription: 'Dictate shoot requirements while on set or driving between client meetings. Cora transcribes voice memos in real-time, extracts deliverables, crew requirements, and budgets, and converts spoken ideas into formal proposals.',
    badgeColor: 'sky',
    iconName: 'Zap',
    status: 'Live in Product',
    tags: ['Hands-Free Voice Input', 'Audio Transcription', 'Structured Scope Parser', 'Instant Proposal Draft'],
    stats: [
      { metric: '< 5 Sec', label: 'Audio to Proposal' },
      { metric: '99.4%', label: 'Indian English Accuracy' },
      { metric: '0 Typing', label: 'On-Set Hands Free' }
    ],
    theOldWay: [
      'Scrawling messy notes on napkins or voice notes that get forgotten and never converted into quotes.',
      'Spending an hour after a long shoot day typing up equipment lists and client deliverable scopes.',
      'Misunderstandings between verbal client meetings and the final written contract.'
    ],
    theCoraWay: [
      'Record a 30-second audio note on your phone: "Shoot for Raymond, 2 days in Mumbai, 4K video + stills, ₹3.5L budget".',
      'AI automatically extracts client name, dates, line items, camera specs, and deliverables into a structured draft.',
      'Generates a complete commercial quote and SHA-256 e-signature contract ready for one-tap client dispatch.'
    ],
    capabilities: [
      {
        title: 'Whisper AI Speech-to-Text',
        description: 'State-of-the-art voice transcription with high tolerance for Indian accents, ambient studio noise, and photography jargon.',
        tag: 'Accurate Voice'
      },
      {
        title: 'Semantic Entity Extraction',
        description: 'Auto-detects dates, currency figures (INR/USD), gear models, deliverable counts, and turnarounds.',
        tag: 'Entity Parser'
      },
      {
        title: 'Instant Proposal Synthesis',
        description: 'Converts unstructured audio ramblings into clean, structured PDF proposals formatted with your rate cards.',
        tag: 'Auto-Formatting'
      },
      {
        title: 'Mobile PWA Microphone Integration',
        description: 'One-tap voice recording directly inside the PWA with visual audio waveform feedback.',
        tag: 'PWA Native'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Tap to Record Voice',
        description: 'Tap the microphone icon on your mobile PWA and speak naturally about the upcoming client shoot.'
      },
      {
        step: '02',
        title: 'AI Transcribes & Extracts',
        description: 'Cora transcribes the audio, identifies deliverables, calculates pricing math, and checks calendar slots.'
      },
      {
        step: '03',
        title: 'Review & Dispatch Scope',
        description: 'A complete proposal card appears on your screen with one-tap WhatsApp and Email dispatch.'
      }
    ],
    toolsReplaced: [
      { name: 'Otter.ai Business', category: 'Voice Transcription', monthlySavingsUSD: 20, monthlySavingsINR: 1700 },
      { name: 'Descript Creator', category: 'Audio Processing', monthlySavingsUSD: 24, monthlySavingsINR: 2000 },
      { name: 'Fireflies.ai', category: 'Meeting Notes', monthlySavingsUSD: 18, monthlySavingsINR: 1500 }
    ],
    faqs: [
      {
        question: 'Does Voice-to-Scope recognize creative industry jargon like "FX6", "Aputure", or "LUTs"?',
        answer: 'Yes. The voice dictionary is specifically tuned for cinematography, commercial photography, and audio production vocabulary.'
      },
      {
        question: 'Can I record in noisy environments like active studio sets?',
        answer: 'Yes. Background noise cancellation algorithms filter out studio hum and ambient chatter.'
      },
      {
        question: 'What languages and accents are supported?',
        answer: 'Supports Indian English, American English, British English, Hindi-English (Hinglish), and global accents.'
      },
      {
        question: 'Can I edit the generated proposal before sending it to the client?',
        answer: 'Yes. You always get a full interactive review screen where you can tweak line items, taxes, or dates.'
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
          col1: 'Shoot Package & Scope',
          col2: '2-Day 4K Video Commercial + Stills',
          col3: '98.8%',
          statusText: 'Mapped to Commercial Tier',
          statusType: 'success',
          actionText: 'Inspect'
        },
        {
          col1: 'Proposed Budget',
          col2: '₹3,50,000 + 18% GST',
          col3: '100%',
          statusText: 'Calculated (₹4,13,000 Total)',
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
    tagline: 'Visual deal stages, dynamic revenue forecasting, and automated WhatsApp/Email outreach.',
    heroDescription: 'A high-velocity sales CRM purpose-built for commercial photographers, video production agencies, and creative freelancers. Track inbound leads from first inquiry to contract signature with sliding deal drawers and revenue forecasting.',
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
      'Losing high-ticket inquiries buried in unorganized WhatsApp chats and personal Gmail threads.',
      'Bloated enterprise CRMs like HubSpot charging $500+/mo for simple creative studio workflows.',
      'Manual math required every month to estimate upcoming production revenue and shoot deposits.'
    ],
    theCoraWay: [
      'Visual drag-and-drop Kanban pipeline with customized creative stages: Inquiry → Scope → E-Sign → Deposit → Production → Delivered.',
      'One-click sliding deal drawers with embedded client call-logs, contract statuses, and invoice ledgers.',
      'Automatic revenue forecasting dynamically adjusted by deal probability and estimated call dates.'
    ],
    capabilities: [
      {
        title: 'Drag-and-Drop Deal Stages',
        description: 'Move projects through customizable milestone columns. Every stage change can trigger automatic email or WhatsApp client updates.',
        tag: 'Visual Funnel'
      },
      {
        title: 'Sliding Side Drawer Sheets',
        description: 'Zero disruptive screen jumps. Inspect client contact details, quote history, and shoot logistics in a seamless right-sliding drawer.',
        tag: 'Zero Layout Shift'
      },
      {
        title: 'Dynamic Revenue Forecasting',
        description: 'Real-time calculation of pipeline revenue, expected GST collection, and cashflow projections for the current quarter.',
        tag: 'Financial Foresight'
      },
      {
        title: 'Omnichannel Inbound Capture',
        description: 'Automatically create new deal cards from website embed forms, Instagram DM webhooks, and direct WhatsApp inquiries.',
        tag: 'Instant Capture'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Capture Lead Inquiries',
        description: 'Inquiries from your website form, embed builder, or direct links instantly populate the New Inquiries column.'
      },
      {
        step: '02',
        title: 'Scope & Quote in 1-Click',
        description: 'Open the sliding drawer, choose package line-items or invoke AI Co-Founder to calculate shoot estimates.'
      },
      {
        step: '03',
        title: 'Advance to Contract & Payment',
        description: 'Drag the card to E-Sign Sent to automatically dispatch a SHA-256 sealed contract and 50% booking deposit link.'
      }
    ],
    toolsReplaced: [
      { name: 'HubSpot Starter', category: 'Sales Pipeline', monthlySavingsUSD: 50, monthlySavingsINR: 4200 },
      { name: 'Pipedrive', category: 'Deal Tracking', monthlySavingsUSD: 39, monthlySavingsINR: 3200 },
      { name: 'Trello Premium', category: 'Kanban Boards', monthlySavingsUSD: 10, monthlySavingsINR: 850 }
    ],
    faqs: [
      {
        question: 'Can I customize the pipeline stages for my specific business model?',
        answer: 'Yes. Whether you run a wedding photography studio, an architectural visualization firm, or a commercial film agency, you can rename, reorder, and add stages freely.'
      },
      {
        question: 'Does the CRM integrate with WhatsApp?',
        answer: 'Yes. You can click to chat directly with client phone numbers with pre-filled milestone templates, booking confirmations, and payment reminders.'
      },
      {
        question: 'Can my team members have restricted access to deal values?',
        answer: 'Yes. Cora’s multi-tenant RBAC engine allows you to hide financial revenue values from junior editors while keeping shoot dates visible.'
      },
      {
        question: 'Is there an export feature for sales reporting?',
        answer: 'You can export all filtered pipeline data directly to CSV/Excel or CA-ready financial spreadsheets in one click.'
      }
    ],
    relatedFeatureSlugs: ['canvas-builder', 'form-builder', 'esign-vault'],
    mockup: {
      windowTitle: 'Cora Lead CRM — Visual Studio Pipeline',
      activeTabLabel: 'Active Pipeline (₹14.8L Value)',
      tabs: [
        { id: 'pipeline', label: 'Kanban Board', badge: '18 Deals' },
        { id: 'list', label: 'Table View' },
        { id: 'forecast', label: 'Revenue Forecast' }
      ],
      headerTitle: 'Commercial & Studio Production Funnel',
      headerSubtitle: 'Q3 Active Pipeline: ₹14,80,000 across 6 active stages',
      primaryActionLabel: '+ New Deal Lead',
      metric1: { label: 'Pipeline Value', value: '₹14,80,000' },
      metric2: { label: 'Deals In Scope', value: '8 Active' },
      metric3: { label: 'Win Rate', value: '68.4%' },
      tableHeaders: ['Client / Project', 'Stage', 'Deal Value', 'Expected Date', 'Actions'],
      rows: [
        {
          col1: 'Vogue India — Autumn Fashion Editorial',
          col2: 'Scope Approved',
          col3: '₹3,50,000',
          statusText: 'E-Sign Pending',
          statusType: 'warning',
          actionText: 'Open Drawer'
        },
        {
          col1: 'Mercedes Benz — Dealership Showcase Video',
          col2: 'Deposit Received',
          col3: '₹5,20,000',
          statusText: 'In Production',
          statusType: 'success',
          actionText: 'View Shoot'
        },
        {
          col1: 'Zomato HQ — Food Menu Campaign',
          col2: 'Inquiry Review',
          col3: '₹1,80,000',
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
    tagline: 'Drag-and-drop landing page & shoot funnel designer with responsive device preview.',
    heroDescription: 'Build high-converting portfolio landing pages, commercial shoot pitch decks, and client booking funnels with zero code. Features responsive mobile previews, custom domain routing, and automatic Git auto-sync.',
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
      'Paying $29/mo to Webflow or Squarespace for landing pages disconnected from your CRM database.',
      'Leads filling website forms having to be manually entered into your project management software.',
      'Slow mobile load speeds degrading Google PageSpeed and reducing mobile Instagram ad conversions.'
    ],
    theCoraWay: [
      'Native builder directly linked to your Cora CRM lead database, packages, and calendar slots.',
      'Sub-50ms screen painting with pure monochromatic atomic components optimized for mobile devices.',
      'Automatic SSL encryption and instant custom domain routing (yourstudio.com / yourname.in).'
    ],
    capabilities: [
      {
        title: 'Pre-Engineered Creative Blocks',
        description: 'Assemble hero sections, portfolio photo carousels, dynamic pricing matrices, client proof testimonials, and interactive booking widgets.',
        tag: 'Component Library'
      },
      {
        title: 'Instant Multi-Device Emulation',
        description: 'Toggle between iPhone 16 Pro, iPad Air, and Desktop 4K viewport modes with live responsive layout inspection.',
        tag: 'Responsive Emulation'
      },
      {
        title: 'Connected Form Capture',
        description: 'Every form element automatically pushes submissions directly into your Kanban pipeline with zero webhook setup.',
        tag: 'CRM Auto-Bridge'
      },
      {
        title: 'SEO & GEO Schema Generation',
        description: 'Auto-generates JSON-LD schema, OpenGraph social preview cards, and Google IndexNow search pinging.',
        tag: 'Instant Ranking'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Choose Canvas Template',
        description: 'Select an optimized studio blueprint for wedding shoots, commercial real estate, or film production.'
      },
      {
        step: '02',
        title: 'Customize Media & Pricing',
        description: 'Drag in high-res RAW crop assets from your Media Hub and connect your live 18% GST packages.'
      },
      {
        step: '03',
        title: 'Publish to Custom Domain',
        description: 'Hit Publish to deploy to global CDN edge servers under your custom domain with instant SSL certificates.'
      }
    ],
    toolsReplaced: [
      { name: 'Webflow Core', category: 'Landing Page Builder', monthlySavingsUSD: 29, monthlySavingsINR: 2400 },
      { name: 'Framer Pro', category: 'Portfolio Sites', monthlySavingsUSD: 20, monthlySavingsINR: 1700 },
      { name: 'Squarespace Business', category: 'Website CMS', monthlySavingsUSD: 33, monthlySavingsINR: 2800 }
    ],
    faqs: [
      {
        question: 'Can I connect my own custom domain?',
        answer: 'Yes. Growth, Starter, and Professional plans include custom domain connection (.com, .in, .studio, .agency) with automated zero-touch SSL provisioning.'
      },
      {
        question: 'Does the canvas support video embeds and high-res galleries?',
        answer: 'Yes. High-bitrate Vimeo, YouTube, and self-hosted MP4/WebM video players with aspect-ratio locking (16:9, 9:16 vertical, 1:1) are natively supported.'
      },
      {
        question: 'Are published pages fast on slow mobile connections?',
        answer: 'All assets are statically compiled via Next.js Turbopack with responsive WebP image optimization and LiteSpeed edge caching.'
      },
      {
        question: 'Can I embed booking slots directly on the page?',
        answer: 'Yes. Clients can choose call-time slots or deposit packages directly inside the funnel page.'
      }
    ],
    relatedFeatureSlugs: ['form-builder', 'lead-crm', 'content-ai'],
    mockup: {
      windowTitle: 'Cora Visual Canvas — Studio Funnel Builder',
      activeTabLabel: 'Funnel: Commercial Portfolio 2026',
      tabs: [
        { id: 'editor', label: 'Canvas Editor', badge: 'Live v4.2' },
        { id: 'settings', label: 'Domain & SEO' },
        { id: 'analytics', label: 'Conversion Funnel' }
      ],
      headerTitle: 'Commercial Photography & Video Pitch Deck',
      headerSubtitle: 'Live URL: https://studio.heycora.in/commercial-2026 (SSL Active)',
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
          col1: 'Interactive 3-Tier Rate Card',
          col2: 'Pricing Matrix Block',
          col3: '18% GST Invoicing',
          statusText: 'Published',
          statusType: 'success',
          actionText: 'Edit Rates'
        },
        {
          col1: 'Instant Shoot Booking & Deposit Form',
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
    tagline: 'Drag-and-drop form builder, live standalone URLs, embeddable iframes, and direct CRM auto-sync.',
    heroDescription: 'Create beautiful, high-converting client intake questionnaires, shoot inquiry forms, and feedback surveys. Embed them on Framer, Webflow, or WordPress, or share as standalone branded links.',
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
      'Paying $29/mo for Typeform or Jotform with ugly third-party branding on free tiers.',
      'Messy Zapier webhooks breaking constantly and dropping high-value client inquiries.',
      'Forms that do not calculate package estimates or attach directly to client CRM records.'
    ],
    theCoraWay: [
      'Unlimited visual forms with zero external logos, completely styled in Cora’s clean aesthetic.',
      'Instant connection: submissions automatically create new deal cards in your CRM pipeline.',
      'Generate 1-click embed codes for Framer, Webflow, Squarespace, and WordPress with auto-resizing.'
    ],
    capabilities: [
      {
        title: 'Multi-Field Visual Designer',
        description: 'Drag in text fields, date pickers, budget sliders, multi-select checkboxes, and file upload dropzones.',
        tag: 'Visual Builder'
      },
      {
        title: 'Embed Builder for All Platforms',
        description: 'Generate zero-dependency clean HTML/JS embed codes tailored for Framer, Webflow, or custom sites.',
        tag: 'Universal Embeds'
      },
      {
        title: 'Direct CRM & Auto-Responder Sync',
        description: 'Every submission triggers an immediate branded confirmation email to the client and alerts you on WhatsApp.',
        tag: 'Instant Follow-Up'
      },
      {
        title: 'Custom Redirects & UTM Tracking',
        description: 'Track ad campaigns with full UTM parameter preservation and custom thank-you page routing.',
        tag: 'Campaign Analytics'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Build Form Fields',
        description: 'Drag and configure your questions: shoot type, preferred date, budget range, and location.'
      },
      {
        step: '02',
        title: 'Copy Share Link or Embed Code',
        description: 'Grab the standalone URL (heycora.in/form/your-form) or copy the responsive iframe embed snippet.'
      },
      {
        step: '03',
        title: 'Collect Inbound Deals',
        description: 'New submissions arrive directly in your CRM Kanban board with immediate notifications.'
      }
    ],
    toolsReplaced: [
      { name: 'Typeform Plus', category: 'Online Forms', monthlySavingsUSD: 29, monthlySavingsINR: 2400 },
      { name: 'JotForm Silver', category: 'Form Submissions', monthlySavingsUSD: 39, monthlySavingsINR: 3200 },
      { name: 'Tally.so Pro', category: 'Form Builder', monthlySavingsUSD: 29, monthlySavingsINR: 2400 }
    ],
    faqs: [
      {
        question: 'Can I embed the form inside my existing Framer or Webflow website?',
        answer: 'Yes. Cora provides a copy-paste embed snippet with auto-height adjustment and zero layout shifting.'
      },
      {
        question: 'Can clients upload reference moodboards or PDF briefs in the form?',
        answer: 'Yes. File upload fields support high-res JPEG, PNG, PDF, and ZIP files with direct cloud storage.'
      },
      {
        question: 'Do I get notified when someone fills out my form?',
        answer: 'Yes. You receive real-time push notifications on your mobile PWA, email alerts, and optional WhatsApp notifications.'
      },
      {
        question: 'Are forms protected against spam bots?',
        answer: 'All Cora forms include invisible cryptographic honeypot spam protection with zero annoying captchas.'
      }
    ],
    relatedFeatureSlugs: ['lead-crm', 'canvas-builder', 'review-portal'],
    mockup: {
      windowTitle: 'Cora Visual Form Builder & Embed Suite',
      activeTabLabel: 'Form: Studio Shoot Inquiry 2026',
      tabs: [
        { id: 'builder', label: 'Visual Fields', badge: '7 Fields' },
        { id: 'embed', label: 'Embed & Share' },
        { id: 'submissions', label: 'Submissions Log' }
      ],
      headerTitle: 'Commercial & Fashion Shoot Intake Form',
      headerSubtitle: 'Target Destination: Lead CRM → "New Inquiries" Column (Auto-Dispatched)',
      primaryActionLabel: 'Copy Embed Code',
      metric1: { label: 'Submissions', value: '142 Leads' },
      metric2: { label: 'Completion Rate', value: '78.6%' },
      metric3: { label: 'Avg Time to Fill', value: '1m 24s' },
      tableHeaders: ['Field Label', 'Input Type', 'Required', 'CRM Mapping', 'Status'],
      rows: [
        {
          col1: 'Client Full Name & Brand',
          col2: 'Short Text Input',
          col3: 'Yes (Mandatory)',
          statusText: 'Mapped to Lead Name',
          statusType: 'success',
          actionText: 'Edit Field'
        },
        {
          col1: 'Shoot Package & Budget Range',
          col2: 'Dropdown / Radio Group',
          col3: 'Yes (Mandatory)',
          statusText: 'Mapped to Deal Value',
          statusType: 'success',
          actionText: 'Edit Field'
        },
        {
          col1: 'Upload Moodboard / Creative Brief',
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
    tagline: 'Public 5-star client feedback portal, automated review campaigns, and Google Business routing.',
    heroDescription: 'Turn delighted clients into an unstoppable referral engine. Collect verified 5-star testimonials, route positive reviews directly to Google Business Profile, and prevent negative feedback with private resolution workflows.',
    badgeColor: 'amber',
    iconName: 'Star',
    status: 'Live in Product',
    tags: ['5★ Feedback Engine', 'Automated Campaigns', 'Google Routing', 'Reputation Score'],
    stats: [
      { metric: '4.9 ★', label: 'Average Studio Rating' },
      { metric: '+84%', label: 'Google Reviews Growth' },
      { metric: 'Zero Effort', label: 'Post-Delivery Trigger' }
    ],
    theOldWay: [
      'Delivering incredible client work and forgetting to ask for a Google review until months later.',
      'Clients intending to leave a review but getting confused by complex sign-in links.',
      'Negative feedback posted publicly on Google before you had a chance to resolve the issue privately.'
    ],
    theCoraWay: [
      'Automatic review prompt sent 24 hours after final gallery delivery when client satisfaction is at its peak.',
      'Smart routing: 5-star ratings are seamlessly directed to your Google Maps review page with 1 tap.',
      'Any rating under 4 stars opens a private feedback dialog so you can resolve concerns before public posting.'
    ],
    capabilities: [
      {
        title: 'Smart Review Filtering',
        description: 'Route happy 5-star ratings directly to Google Business while catching critical feedback privately.',
        tag: 'Reputation Shield'
      },
      {
        title: 'Automated Post-Shoot Triggers',
        description: 'Trigger automated review requests via WhatsApp and Email upon final invoice settlement or gallery approval.',
        tag: 'Autonomous Delivery'
      },
      {
        title: 'Embeddable Wall of Love',
        description: 'Generate beautiful responsive testimonial cards to embed on your portfolio website in 1 click.',
        tag: 'Social Proof'
      },
      {
        title: 'Client Video Testimonial Capture',
        description: 'Allow clients to record 30-second video testimonials directly from their smartphone browser.',
        tag: 'Video Proof'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Auto-Trigger Request',
        description: 'When a shoot reaches "Delivered" stage, Cora sends a branded WhatsApp review link.'
      },
      {
        step: '02',
        title: 'Client Rates Experience',
        description: 'Client rates 1-5 stars and writes their thoughts on a frictionless mobile interface.'
      },
      {
        step: '03',
        title: '1-Tap Google Transfer',
        description: '5-star reviews automatically copy to clipboard and redirect to your Google Maps listing.'
      }
    ],
    toolsReplaced: [
      { name: 'Birdeye / Podium', category: 'Review Management', monthlySavingsUSD: 99, monthlySavingsINR: 8200 },
      { name: 'Senja.io Pro', category: 'Testimonial Collector', monthlySavingsUSD: 29, monthlySavingsINR: 2400 },
      { name: 'Testimonial.to', category: 'Video Reviews', monthlySavingsUSD: 25, monthlySavingsINR: 2000 }
    ],
    faqs: [
      {
        question: 'Does this directly increase my studio’s Google Maps ranking?',
        answer: 'Yes. Frequent, verified 5-star reviews with keywords (e.g. "photographer", "commercial studio") are the #1 local SEO ranking factor.'
      },
      {
        question: 'Can I display these testimonials on my website?',
        answer: 'Yes. Cora provides interactive masonry grids, testimonial carousels, and quote badges ready to embed.'
      },
      {
        question: 'What happens if a client gives a low rating?',
        answer: 'Ratings below 4 stars prompt the client for constructive feedback sent only to your private admin email, avoiding public negative Google reviews.'
      },
      {
        question: 'Can I customize the wording of the review request messages?',
        answer: 'Yes. Full template customization for WhatsApp and email with dynamic client and project tags is supported.'
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
      headerSubtitle: 'Google Business Rating: 4.9 ★ (84 Verified Reviews in Mumbai)',
      primaryActionLabel: '+ Send Review Invite',
      metric1: { label: 'Overall Rating', value: '4.95 / 5.0' },
      metric2: { label: 'Total Reviews', value: '84 Reviews' },
      metric3: { label: 'Conversion Rate', value: '72% Left 5★' },
      tableHeaders: ['Client Name', 'Project / Shoot', 'Rating', 'Google Sync', 'Action'],
      rows: [
        {
          col1: 'Vikram Singhania (CEO, Apex)',
          col2: 'Corporate Headshots & Brand Video',
          col3: '★★★★★ (5/5)',
          statusText: 'Published on Google',
          statusType: 'success',
          actionText: 'View Review'
        },
        {
          col1: 'Ananya Roy (Creative Producer)',
          col2: 'Lakme Fashion Week Lookbook',
          col3: '★★★★★ (5/5)',
          statusText: 'Published on Google',
          statusType: 'success',
          actionText: 'View Review'
        },
        {
          col1: 'Siddharth Roy (Founder, D2C Brand)',
          col2: 'Product Commercial Shoot',
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
    tagline: 'Guided 5-step document wizard, legal canvas e-signatures, and immutable cryptographic audit logs.',
    heroDescription: 'Eliminate expensive DocuSign subscriptions with built-in, tamper-evident legal e-signatures. Legally binding under the Indian Information Technology Act 2000 and global electronic signature frameworks with cryptographic SHA-256 timestamping.',
    badgeColor: 'rose',
    iconName: 'FileText',
    status: 'Live in Product',
    tags: ['Guided 5-Step Wizard', 'SHA-256 Audit Trail', 'IT Act 2000 Compliant', 'Tamper Evident PDF'],
    stats: [
      { metric: '100% Legal', label: 'Indian IT Act 2000' },
      { metric: '₹0', label: 'Per-Envelope Fee' },
      { metric: 'SHA-256', label: 'Cryptographic Hash' }
    ],
    theOldWay: [
      'Paying ₹1,500–₹3,000 every month for DocuSign or PandaDoc to send simple shoot agreements.',
      'Clients complaining about printing, signing, scanning, and emailing back physical paper agreements.',
      'Unprotected agreements lacking verifiable IP timestamps or cryptographic tamper-evident proof in court.'
    ],
    theCoraWay: [
      'Unlimited* tamper-evident electronic signatures included free in all Cora tiers with zero per-envelope costs.',
      'Frictionless client experience: open link on mobile, review terms, draw signature with touch, and receive PDF.',
      'Every signed document receives an immutable cryptographic SHA-256 hash, IP address stamp, and audit certificate.'
    ],
    capabilities: [
      {
        title: 'Guided 5-Step Contract Wizard',
        description: 'Step-by-step workflow: 1. Client Details → 2. Terms & Deliverables → 3. GST Calculation → 4. E-Signature → 5. Cryptographic Seal.',
        tag: '5-Step Stepper'
      },
      {
        title: 'Touchscreen Signature Canvas',
        description: 'Smooth vector signature capture for smartphone screens, Apple Pencil / iPad, and desktop mouse.',
        tag: 'Mobile Optimized'
      },
      {
        title: 'SHA-256 Audit Certificate',
        description: 'Generates a final tamper-evident certificate page recording timestamp, signer email, user agent, and cryptographic hash.',
        tag: 'Legal Protection'
      },
      {
        title: 'Automated Commercial Contract Blueprints',
        description: 'Pre-loaded with legal templates for Photography Shoot Terms, Film Licensing Agreements, NDA Contracts, and Model Releases.',
        tag: 'Legal Templates'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Create Agreement',
        description: 'Select a template or generate contract terms in seconds with your client name and GST breakdown.'
      },
      {
        step: '02',
        title: 'Dispatch Secure Link',
        description: 'Send the client a secure, branded link via WhatsApp or email with no account creation required.'
      },
      {
        step: '03',
        title: 'Sign & Cryptographic Seal',
        description: 'Client signs on their phone. Both parties receive a certified, SHA-256 sealed PDF copy automatically.'
      }
    ],
    toolsReplaced: [
      { name: 'DocuSign Standard', category: 'E-Signatures', monthlySavingsUSD: 25, monthlySavingsINR: 2000 },
      { name: 'PandaDoc Individual', category: 'Contract Management', monthlySavingsUSD: 29, monthlySavingsINR: 2400 },
      { name: 'Adobe Acrobat Sign', category: 'PDF Signing', monthlySavingsUSD: 20, monthlySavingsINR: 1650 }
    ],
    faqs: [
      {
        question: 'Are Cora e-signatures legally valid in India?',
        answer: 'Yes. Cora e-signatures are fully recognized and enforceable under Section 10A of the Indian Information Technology Act 2000 and the Indian Evidence Act.'
      },
      {
        question: 'Do my clients need to create a Cora account to sign?',
        answer: 'No. Clients receive a direct, authenticated web link where they can review terms and sign directly on their smartphone or computer.'
      },
      {
        question: 'How do I prove the contract was not altered after signing?',
        answer: 'The final PDF is cryptographically hashed with SHA-256. Any modification to a single character in the document invalidates the mathematical hash.'
      },
      {
        question: 'Are there limits on how many contracts I can send per month?',
        answer: 'Free Forever includes generous complimentary contract envelopes, while all paid plans include unlimited document signing.'
      }
    ],
    relatedFeatureSlugs: ['gst-invoicing', 'lead-crm', 'task-board'],
    mockup: {
      windowTitle: 'Cora Document Vault — Cryptographic E-Sign Registry',
      activeTabLabel: 'Contract #CORA-CNT-2026-042',
      tabs: [
        { id: 'active', label: 'Executed Contracts', badge: '42 Sealed' },
        { id: 'pending', label: 'Awaiting Signature', badge: '3' },
        { id: 'templates', label: 'Contract Blueprints' }
      ],
      headerTitle: 'Commercial Photography & Film Production Agreement',
      headerSubtitle: 'Client: Raymond Apparel Ltd • SHA-256 Hash: e3b0c44298fc1c149afbf4c8996fb924',
      primaryActionLabel: '+ New E-Sign Envelope',
      metric1: { label: 'Signed Turnaround', value: '< 2.4 Hours' },
      metric2: { label: 'Total Value', value: '₹28.4L' },
      metric3: { label: 'Audit Status', value: '100% Sealed' },
      tableHeaders: ['Contract Reference', 'Signer / Client', 'Value (INR)', 'Signed Timestamp', 'Verification'],
      rows: [
        {
          col1: 'Raymond — Autumn Winter Shoot Contract',
          col2: 'Anil Mehta (Director Marketing)',
          col3: '₹4,50,000',
          statusText: 'Signed & Sealed',
          statusType: 'success',
          actionText: 'Download PDF'
        },
        {
          col1: 'Titan Watches — Macro Video Commercial',
          col2: 'Pooja Verma (Brand Manager)',
          col3: '₹3,20,000',
          statusText: 'Signed & Sealed',
          statusType: 'success',
          actionText: 'Download PDF'
        },
        {
          col1: 'Nykaa Beauty — Studio Lookbook Shoot',
          col2: 'Rohan Sen (Creative Producer)',
          col3: '₹2,10,000',
          statusText: 'Viewed by Client',
          statusType: 'warning',
          actionText: 'Send Reminder'
        }
      ]
    }
  },
  {
    slug: 'crew-dispatch',
    title: 'Crew & Team Dispatch Scheduler with Conflict Guard',
    shortTitle: 'Crew Dispatch',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    tagline: 'Timeline crew grid, call-time slot pickers, GPS shoot mapping, and automated schedule conflict guards.',
    heroDescription: 'Coordinate directors of photography, assistants, sound engineers, hair/makeup stylists, and editors without endless group messaging chaos. Provides clear call-sheets, call-time slots, and automated GPS shoot pin dispatch.',
    badgeColor: 'indigo',
    iconName: 'Send',
    status: 'Live in Product',
    industries: ['photo_film', 'real_estate'],
    tags: ['Timeline Crew Grid', 'Call-Time Slot Pickers', 'GPS Shoot Pins', 'Conflict Guard'],
    stats: [
      { metric: '0 Conflicting', label: 'Double Bookings' },
      { metric: '1-Click', label: 'Call-Sheet SMS/WhatsApp' },
      { metric: 'Live GPS', label: 'Location Directions' }
    ],
    theOldWay: [
      'Coordinating 10-person film crews over noisy WhatsApp groups where call-times and addresses get lost.',
      'Crew members showing up late because they were given the wrong location pin or outdated call-times.',
      'Accidentally assigning your lead DP to two simultaneous shoots in different parts of the city.'
    ],
    theCoraWay: [
      'Clean visual timeline showing who is booked, on set, or available on any given date.',
      'Automated digital call-sheets sent directly to crew smartphones with exact call-time, wardrobe notes, and Google Maps pin.',
      'Intelligent conflict guard blocks overlapping dispatch assignments before they happen.'
    ],
    capabilities: [
      {
        title: 'Timeline Multi-Crew Grid',
        description: 'See your entire roster of photographers, video operators, and assistants across daily and weekly timelines.',
        tag: 'Timeline Grid'
      },
      {
        title: 'Digital Call-Sheet Generator',
        description: 'Generates mobile-optimized digital call-sheets with sunrise/sunset times, weather forecasts, and emergency contacts.',
        tag: 'Call-Sheets'
      },
      {
        title: 'Automated WhatsApp Dispatch',
        description: 'One-click call-sheet distribution via WhatsApp with instant delivery confirmations.',
        tag: 'Instant Push'
      },
      {
        title: 'Crew Payout & Rate Logging',
        description: 'Log day-rates and half-day rates for freelance contractors and auto-calculate shoot production margins.',
        tag: 'Margin Control'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Create Shoot Call-Sheet',
        description: 'Select shoot booking, location GPS coordinates, and set call-times for each crew department.'
      },
      {
        step: '02',
        title: 'Assign Verified Crew',
        description: 'Pick available team members with automatic qualification checks and conflict warnings.'
      },
      {
        step: '03',
        title: '1-Click Mobile Dispatch',
        description: 'Dispatches personalized WhatsApp links so every crew member arrives on time with exact instructions.'
      }
    ],
    toolsReplaced: [
      { name: 'StudioBinder Pro', category: 'Call Sheets & Crew', monthlySavingsUSD: 49, monthlySavingsINR: 4200 },
      { name: 'Setkeeper', category: 'Production Dispatch', monthlySavingsUSD: 60, monthlySavingsINR: 5000 },
      { name: 'When I Work', category: 'Team Scheduling', monthlySavingsUSD: 25, monthlySavingsINR: 2000 }
    ],
    faqs: [
      {
        question: 'Can freelance crew access only their specific call-sheet without seeing client fees?',
        answer: 'Yes. Crew call-sheets contain only logistical details (call-time, shoot location, assigned gear) with zero pricing visibility.'
      },
      {
        question: 'Does it work for multi-day outstation destination shoots?',
        answer: 'Yes. You can schedule multi-day travel schedules, hotel accommodations, and flight itinerary attachments.'
      },
      {
        question: 'Can crew confirm their availability via mobile?',
        answer: 'Yes. Crew can tap "Confirm Attendance" on the mobile call-sheet to update your dispatch dashboard instantly.'
      },
      {
        question: 'Is GPS location mapping accurate for remote outdoor shoots?',
        answer: 'Yes. Cora supports Google Maps geo-coordinates and precise Plus Codes for remote outdoor and desert locations.'
      }
    ],
    relatedFeatureSlugs: ['master-calendar', 'asset-gear', 'task-board'],
    mockup: {
      windowTitle: 'Cora Crew Dispatch & Call-Sheet Engine',
      activeTabLabel: 'Shoot Call-Sheet: Mahindra EV Commercial',
      tabs: [
        { id: 'roster', label: 'Crew Roster', badge: '8 Confirmed' },
        { id: 'callsheet', label: 'Digital Call-Sheet' },
        { id: 'timeline', label: 'Timeline Grid' }
      ],
      headerTitle: 'Commercial Auto Shoot — Day 1 of 2',
      headerSubtitle: 'Location: Film City Studio 4, Goregaon East • Call Time: 06:30 AM IST',
      primaryActionLabel: '+ Dispatch Call-Sheets',
      metric1: { label: 'Crew Confirmed', value: '8 / 8 Confirmed' },
      metric2: { label: 'Call Time', value: '06:30 AM' },
      metric3: { label: 'Shoot Duration', value: '10 Hours' },
      tableHeaders: ['Crew Member', 'Role / Department', 'Call Time', 'Status', 'Action'],
      rows: [
        {
          col1: 'Kabir Sharma',
          col2: 'Director of Photography (DP)',
          col3: '06:30 AM',
          statusText: 'Confirmed',
          statusType: 'success',
          actionText: 'View Call-Sheet'
        },
        {
          col1: 'Aarav Patel',
          col2: '1st Assistant Camera (1st AC)',
          col3: '06:15 AM (Gear Prep)',
          statusText: 'Confirmed',
          statusType: 'success',
          actionText: 'View Call-Sheet'
        },
        {
          col1: 'Neha Kulkarni',
          col2: 'Gaffer / Chief Lighting Tech',
          col3: '06:00 AM (Rigging)',
          statusText: 'Dispatched (Unread)',
          statusType: 'warning',
          actionText: 'Resend WhatsApp'
        }
      ]
    }
  },
  {
    slug: 'master-calendar',
    title: 'Master Calendar & Autonomous Studio Booking Manager',
    shortTitle: 'Master Calendar',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    tagline: 'Day/Week/Month multi-view grid, 5-step booking modal, client scheduling, and real-time showing locks.',
    heroDescription: 'The central nervous system of your studio schedule. Syncs multi-bay studio bookings, on-location commercial shoots, client review calls, and editing deadlines with zero risk of double booking.',
    badgeColor: 'purple',
    iconName: 'Calendar',
    status: 'Live in Product',
    tags: ['Day/Week/Month Grid', '5-Step Booking Modal', 'Real-Time Stage Locks', 'Google Calendar 2-Way Sync'],
    stats: [
      { metric: '0 Errors', label: 'Scheduling Conflict Rate' },
      { metric: '2-Way', label: 'Google & Apple Cal Sync' },
      { metric: '5-Step', label: 'Guided Booking Flow' }
    ],
    theOldWay: [
      'Switching between Calendly, Google Calendar, and paper diaries, constantly fearing double bookings.',
      'Clients booking dates on Calendly without having signed a contract or paid a 50% deposit.',
      'No visual map of multiple studio bays, editing suites, or concurrent shooting teams.'
    ],
    theCoraWay: [
      'Unified master calendar linking bookings directly to contracts, GST invoices, and assigned equipment kits.',
      'Showing locks: dates remain held in "Tentative" status until contract is signed and advance deposit is collected.',
      'Two-way sync with Google Calendar, Apple Calendar, and Outlook to keep your personal schedule clean.'
    ],
    capabilities: [
      {
        title: 'Multi-View Studio Timeline',
        description: 'Toggle effortlessly between Day Schedule, Week Matrix, Month Bird’s Eye, and List agenda views.',
        tag: 'Multi-View'
      },
      {
        title: 'Studio Bay & Suite Allocation',
        description: 'Manage Studio Bay A (Cyc Wall), Studio Bay B (Daylight Studio), and Podcast Suite concurrently.',
        tag: 'Space Management'
      },
      {
        title: 'Autonomous Client Self-Booking',
        description: 'Share custom branded availability links with minimum notice rules and automated buffers.',
        tag: 'Self-Booking'
      },
      {
        title: 'Automatic Timezone Conversion',
        description: 'Seamless handling of remote international client review meetings across IST, GMT, EST, and PST.',
        tag: 'Global Timezones'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Set Studio Availability',
        description: 'Define your operating hours, shoot buffers (e.g. 1 hour between shoots), and minimum advance notice.'
      },
      {
        step: '02',
        title: 'Lock Dates with 5-Step Modal',
        description: 'Input client details, assign bays, select equipment kits, and attach deposit payment terms.'
      },
      {
        step: '03',
        title: 'Automated 2-Way Sync',
        description: 'Event syncs to all crew devices and Google Calendars with automatic change alert notifications.'
      }
    ],
    toolsReplaced: [
      { name: 'Calendly Pro', category: 'Booking Links', monthlySavingsUSD: 16, monthlySavingsINR: 1500 },
      { name: 'Acuity Scheduling', category: 'Appointment System', monthlySavingsUSD: 20, monthlySavingsINR: 1700 },
      { name: 'HoneyBook Scheduling', category: 'Client Calendar', monthlySavingsUSD: 22, monthlySavingsINR: 1900 }
    ],
    faqs: [
      {
        question: 'Can I block blackout dates and national holidays easily?',
        answer: 'Yes. You can mark holidays, studio maintenance days, or personal leave with a single drag-to-block.'
      },
      {
        question: 'Does it sync with my phone’s native calendar app?',
        answer: 'Yes. Cora provides real-time iCal / CalDAV subscription feeds compatible with iOS, macOS, Android, and Outlook.'
      },
      {
        question: 'Can I require a deposit before a client confirms a date on my calendar?',
        answer: 'Yes. You can enable mandatory UPI/Credit Card deposits or contract signing before a booking slot is locked.'
      },
      {
        question: 'Can multiple team members see the calendar without having admin access?',
        answer: 'Yes. Role-based permissions allow assistants to view call-times without editing booking details.'
      }
    ],
    relatedFeatureSlugs: ['crew-dispatch', 'lead-crm', 'asset-gear'],
    mockup: {
      windowTitle: 'Cora Master Calendar & Booking Manager',
      activeTabLabel: 'Studio Schedule — August 2026',
      tabs: [
        { id: 'month', label: 'Month Grid', badge: '24 Shoots' },
        { id: 'bays', label: 'Studio Bays View' },
        { id: 'agenda', label: 'Upcoming Agenda' }
      ],
      headerTitle: 'Studio Bay & Location Production Calendar',
      headerSubtitle: 'Synced with Google Calendar & Crew Dispatch Roster',
      primaryActionLabel: '+ Book Shoot Slot',
      metric1: { label: 'Bays Occupied', value: '88% This Week' },
      metric2: { label: 'Confirmed Shoots', value: '24 Bookings' },
      metric3: { label: 'Tentative Holds', value: '3 Holds' },
      tableHeaders: ['Time Slot / Date', 'Project Title', 'Studio Bay / Location', 'Assigned Team', 'Status'],
      rows: [
        {
          col1: 'Saturday, Aug 29 • 08:00 - 18:00',
          col2: 'Raymond Autumn Fashion Shoot',
          col3: 'Main Cyc Wall (Bay A)',
          statusText: 'Locked & Confirmed',
          statusType: 'success',
          actionText: 'View Details'
        },
        {
          col1: 'Sunday, Aug 30 • 10:00 - 16:00',
          col2: 'Titan Watches Macro Commercial',
          col3: 'Daylight Studio (Bay B)',
          statusText: 'Locked & Confirmed',
          statusType: 'success',
          actionText: 'View Details'
        },
        {
          col1: 'Monday, Aug 31 • 14:00 - 18:00',
          col2: 'Nykaa Beauty Lookbook',
          col3: 'Location (Bandra Studio)',
          statusText: 'Tentative Hold',
          statusType: 'warning',
          actionText: 'Follow Up'
        }
      ]
    }
  },
  {
    slug: 'task-board',
    title: 'Client Task, Milestone & Production Board',
    shortTitle: 'Task Board',
    category: 'operations',
    categoryLabel: 'Operations & Legal',
    tagline: 'Production task board with priority badges, deadline countdowns, team assignments, and sign-offs.',
    heroDescription: 'Keep multi-stage creative projects on track from pre-production moodboards to post-production color grading and final client handoff. Features priority tagging, countdown timers, and client milestone approvals.',
    badgeColor: 'emerald',
    iconName: 'CheckSquare',
    status: 'Live in Product',
    tags: ['Priority Badges', 'Deadline Timers', 'Milestone Sign-Offs', 'Role-Based Assignees'],
    stats: [
      { metric: '0 Missed', label: 'Production Deadlines' },
      { metric: '1-Click', label: 'Milestone Sign-Off' },
      { metric: 'Live', label: 'Task Activity Audit' }
    ],
    theOldWay: [
      'Post-production edits getting delayed because editors and retouchers were unaware of revised deadlines.',
      'Clients endlessly asking "What is the status of our video?" over email and WhatsApp.',
      'Unstructured task lists scattered across Asana, Monday.com, and Slack with no link to client contracts.'
    ],
    theCoraWay: [
      'Every project automatically creates standard production milestones: Pre-Production → Shoot → Rough Cut → Color Grade → Delivery.',
      'Assign tasks directly to specific internal team members with automated deadline countdown timers.',
      'Clients get a live read-only milestone view, reducing "Where is my project?" emails by 85%.'
    ],
    capabilities: [
      {
        title: 'Automated Milestone Templates',
        description: 'Auto-generate 12 standardized production tasks when a new project agreement is signed.',
        tag: 'Smart Templates'
      },
      {
        title: 'Urgent Priority Badges & Timers',
        description: 'Visual status badges (Critical 🔴, In Progress 🟡, Done 🟢) with live deadline countdown clocks.',
        tag: 'Visual Priority'
      },
      {
        title: 'Client Milestone Sign-Offs',
        description: 'Allow clients to formally approve rough cuts or moodboards with 1-click digital sign-offs.',
        tag: 'Sign-Off Engine'
      },
      {
        title: 'Linked Asset & Invoice Context',
        description: 'Every task links directly to relevant media folders, crew call-sheets, and billing milestones.',
        tag: 'Zero Context Loss'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Create Project Board',
        description: 'Select project template or let AI Co-Founder auto-populate tasks based on your contract terms.'
      },
      {
        step: '02',
        title: 'Assign Tasks & Deadlines',
        description: 'Assign retouchers, colorists, and sound designers with specific call-times and delivery dates.'
      },
      {
        step: '03',
        title: 'Track to Final Delivery',
        description: 'Tasks move across columns to completion, automatically notifying the client when milestones are achieved.'
      }
    ],
    toolsReplaced: [
      { name: 'Asana Premium', category: 'Task Management', monthlySavingsUSD: 24, monthlySavingsINR: 2000 },
      { name: 'Monday.com Pro', category: 'Project Tracking', monthlySavingsUSD: 30, monthlySavingsINR: 2500 },
      { name: 'ClickUp Business', category: 'Team Workspaces', monthlySavingsUSD: 19, monthlySavingsINR: 1600 }
    ],
    faqs: [
      {
        question: 'Can my freelance editors log into Cora to see only their assigned tasks?',
        answer: 'Yes. The Editor RBAC role gives team members access only to their assigned task cards and media assets.'
      },
      {
        question: 'Can clients add tasks to my internal production board?',
        answer: 'Clients have a dedicated read-only milestone portal and can submit revision requests without disrupting internal workflows.'
      },
      {
        question: 'Does the task board trigger automatic overdue notifications?',
        answer: 'Yes. Email and in-app notifications are sent when a task is within 24 hours of its deadline.'
      },
      {
        question: 'Can I attach revision notes and timecoded video feedback to tasks?',
        answer: 'Yes. Tasks support rich text notes, attachments, and specific timestamp markers (e.g. "Fix audio at 01:24").'
      }
    ],
    relatedFeatureSlugs: ['crew-dispatch', 'master-calendar', 'media-hub'],
    mockup: {
      windowTitle: 'Cora Task & Production Milestone Board',
      activeTabLabel: 'Project: Titan Watches Macro Commercial',
      tabs: [
        { id: 'board', label: 'Milestone Board', badge: '8 Tasks' },
        { id: 'timeline', label: 'Gantt Timeline' },
        { id: 'client-view', label: 'Client Milestone View' }
      ],
      headerTitle: 'Post-Production & Color Grading Pipeline',
      headerSubtitle: 'Target Delivery Date: Sept 04, 2026 • Lead Colorist: Sarah Jenkins',
      primaryActionLabel: '+ Add Production Task',
      metric1: { label: 'Tasks Completed', value: '5 / 8 Done' },
      metric2: { label: 'Next Deadline', value: 'Tomorrow (Rough Cut)' },
      metric3: { label: 'Client Status', value: 'On Schedule' },
      tableHeaders: ['Task / Milestone', 'Department', 'Assignee', 'Deadline', 'Status'],
      rows: [
        {
          col1: 'DaVinci Resolve Color Grade Pass 1',
          col2: 'Post-Production',
          col3: 'Sarah Jenkins',
          statusText: 'In Progress',
          statusType: 'warning',
          actionText: 'Open Task'
        },
        {
          col1: 'Sound Design & 5.1 Mix Master',
          col2: 'Audio Engineering',
          col3: 'Arjun Nair',
          statusText: 'Ready to Start',
          statusType: 'info',
          actionText: 'Open Task'
        },
        {
          col1: 'Moodboard & Treatment Deck Approval',
          col2: 'Pre-Production',
          col3: 'Client (Titan Brand Team)',
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
    tagline: 'Automated 18% CGST/SGST/IGST tax engine, dynamic UPI QR codes, and CA-ready ledger export.',
    heroDescription: 'An automated financial operations suite built specifically for Indian service providers and creative agencies. Calculate 18% GST splits, embed dynamic UPI QR codes for instant 0% fee payments, and export GSTR-1 ready ledgers.',
    badgeColor: 'emerald',
    iconName: 'Receipt',
    status: 'Live in Product',
    tags: ['18% GST Engine', 'CGST/SGST Split', 'Dynamic UPI QR', 'CA-Ready Ledger'],
    stats: [
      { metric: '18% GST', label: 'Auto Tax Calculation' },
      { metric: '0%', label: 'UPI Transaction Fees' },
      { metric: '1-Click', label: 'CA & Tally Export' }
    ],
    theOldWay: [
      'Manually calculating 9% CGST + 9% SGST vs 18% IGST splits in Excel with frequent mathematical mistakes.',
      'Paying 2-3% payment gateway fees on credit cards or waiting days for NEFT wire transfers.',
      'Frantic quarter-end rushes compiling fragmented invoice spreadsheets for your chartered accountant.'
    ],
    theCoraWay: [
      'Automated intra-state (CGST+SGST) vs inter-state (IGST) tax calculation based on client State / GSTIN.',
      'Dynamic UPI QR code embedded directly on PDF invoices allowing clients to scan and pay directly via GPay/PhonePe.',
      'Clean GSTR-1 compliant sales register with B2B GSTIN validation, HSN/SAC codes (9983), and one-click CA export.'
    ],
    capabilities: [
      {
        title: 'Intelligent Tax Calculation Engine',
        description: 'Auto-detects supplier vs client state code to apply exact 9%+9% CGST/SGST or 18% IGST rates automatically.',
        tag: 'GST Rulebook'
      },
      {
        title: 'Dynamic UPI QR Code Generation',
        description: 'Encodes your exact invoice total, payee VPA, and invoice reference into a high-res scanable QR code for zero-fee bank transfers.',
        tag: 'Zero Fee UPI'
      },
      {
        title: 'Multi-Milestone Retainer Billing',
        description: 'Split project payments into 50% advance deposit, 30% on-set milestone, and 20% final delivery invoices.',
        tag: 'Milestone Invoicing'
      },
      {
        title: 'Automated Overdue Reminders',
        description: 'Gentle, professional WhatsApp and email reminders triggered automatically 3 days before and on due date.',
        tag: 'Payment Chaser'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Select Client & Line Items',
        description: 'Choose client or pull scope directly from your accepted CRM quote in 1 click.'
      },
      {
        step: '02',
        title: 'Auto-Calculate GST & UPI QR',
        description: 'Cora computes tax breakdowns, validates SAC Code 9983, and generates a dynamic UPI payment QR.'
      },
      {
        step: '03',
        title: 'Dispatch & Collect Payment',
        description: 'Send branded PDF via WhatsApp or email. Client scans UPI QR to pay directly into your bank account.'
      }
    ],
    toolsReplaced: [
      { name: 'Zoho Books / Invoice', category: 'Invoicing Software', monthlySavingsUSD: 15, monthlySavingsINR: 1200 },
      { name: 'FreshBooks Premium', category: 'Billing & Accounting', monthlySavingsUSD: 35, monthlySavingsINR: 2900 },
      { name: 'Khatabook Pro', category: 'Payment Bookkeeping', monthlySavingsUSD: 10, monthlySavingsINR: 800 }
    ],
    faqs: [
      {
        question: 'Does Cora support businesses without a GSTIN number?',
        answer: 'Yes. Freelancers and solopreneurs below the ₹20L/₹40L threshold can generate clean non-GST commercial invoices or LUT-exempt export invoices.'
      },
      {
        question: 'How do UPI QR code payments work?',
        answer: 'Cora encodes the exact payment amount and your UPI ID directly into standard NPCI QR format. When the client scans with GPay, Paytm, or PhonePe, the funds land instantly in your bank account with 0% gateway commission.'
      },
      {
        question: 'Can I export invoice reports for my Chartered Accountant?',
        answer: 'Yes. You can export GSTR-1 compliant monthly and quarterly sales registers formatted with B2B vs B2C splits and taxable turnover.'
      },
      {
        question: 'Are multi-currency invoices supported for international clients?',
        answer: 'Yes. You can generate invoices in USD, EUR, GBP, AED, and INR with zero hassle.'
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
      headerSubtitle: 'SAC Code: 9983 (Photography & Commercial Video Production)',
      primaryActionLabel: '+ Generate Tax Invoice',
      metric1: { label: 'Total Invoiced', value: '₹32,60,000' },
      metric2: { label: 'GST Collected', value: '₹5,86,800' },
      metric3: { label: 'Avg Collection Time', value: '3.2 Days' },
      tableHeaders: ['Invoice #', 'Client / Entity', 'Tax Breakdown', 'Grand Total', 'Status'],
      rows: [
        {
          col1: 'INV-2026-0104',
          col2: 'Tata Motors Design Studio',
          col3: '18% IGST (Inter-State)',
          statusText: 'Paid via UPI',
          statusType: 'success',
          actionText: 'View Receipt'
        },
        {
          col1: 'INV-2026-0103',
          col2: 'FabIndia Lifestyle Pvt Ltd',
          col3: '9% CGST + 9% SGST',
          statusText: 'Paid via UPI',
          statusType: 'success',
          actionText: 'View Receipt'
        },
        {
          col1: 'INV-2026-0102',
          col2: 'Kalyan Jewellers Commercial',
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
    title: 'Asset, Equipment & Property Listing Inventory Manager',
    shortTitle: 'Gear & Inventory',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    tagline: 'Studio camera gear check-in/out tracking and comprehensive property listing catalog.',
    heroDescription: 'Keep 100% control over expensive studio equipment, cinema lenses, lighting kits, and real estate property listings. Prevents double-booking disasters and tracks asset depreciation and maintenance logs.',
    badgeColor: 'sky',
    iconName: 'Camera',
    status: 'Live in Product',
    industries: ['photo_film', 'real_estate'],
    tags: ['Gear Check-In/Out', 'Barcode & QR Tracking', 'Property Listing MLS', 'Maintenance Logs'],
    stats: [
      { metric: '0 Missing', label: 'Gear Tracking Accuracy' },
      { metric: '1-Tap', label: 'Kit Check-Out' },
      { metric: 'Real-Time', label: 'Availability Calendar' }
    ],
    theOldWay: [
      'Sending crews to multi-camera shoots only to find essential lenses were left behind or double-booked.',
      'Equipment maintenance history kept on sticky notes leading to gear failure on critical client sets.',
      'Disorganized property catalogs making it impossible to quickly show commercial real estate listings.'
    ],
    theCoraWay: [
      'Visual asset registry with status badges: Available, In-Use on Set, Under Maintenance, or Reserved.',
      'Pre-packaged "Shoot Kits" (e.g. 4K Commercial Interview Kit) that check out 12 individual items with 1 tap.',
      'Real estate property listing manager with square footage, floor plans, high-res photos, and MLS tags.'
    ],
    capabilities: [
      {
        title: 'Smart Kit Assembly',
        description: 'Bundle camera bodies, prime lenses, wireless audio, and lighting gear into standardized production kits.',
        tag: 'Production Kits'
      },
      {
        title: 'Real-Time Schedule Conflict Guard',
        description: 'Warns you immediately if a requested camera body is already assigned to another shoot on the same day.',
        tag: 'Conflict Prevention'
      },
      {
        title: 'Maintenance & Service History',
        description: 'Track sensor cleaning schedules, firmware updates, and lens calibration records with reminders.',
        tag: 'Asset Longevity'
      },
      {
        title: 'Property Listing Portfolio',
        description: 'Organize real estate staging shoots, property addresses, GPS coordinates, and client viewing portals.',
        tag: 'Real Estate Hub'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Register Equipment & Assets',
        description: 'Add serial numbers, purchase dates, insurance values, and daily rental rate values.'
      },
      {
        step: '02',
        title: 'Assign to Shoot Bookings',
        description: 'Attach required gear kits to confirmed master calendar bookings with automatic inventory locking.'
      },
      {
        step: '03',
        title: 'Check-In & Condition Audit',
        description: 'Crew checks gear back in post-shoot with one-tap condition logging and missing item alerts.'
      }
    ],
    toolsReplaced: [
      { name: 'Cheqroom / GearTrack', category: 'Equipment Management', monthlySavingsUSD: 45, monthlySavingsINR: 3800 },
      { name: 'ShareMyToolbox', category: 'Tool Inventory', monthlySavingsUSD: 30, monthlySavingsINR: 2500 },
      { name: 'Excel Inventory Sheets', category: 'Manual Spreadsheets', monthlySavingsUSD: 0, monthlySavingsINR: 0 }
    ],
    faqs: [
      {
        question: 'Can I print QR codes or barcodes for my camera cases?',
        answer: 'Yes. Cora can generate printable high-contrast QR labels that can be scanned with any smartphone camera.'
      },
      {
        question: 'Does this handle gear rented from third-party rental houses?',
        answer: 'Yes. You can mark assets as "External Rental" with vendor contact, rental cost, and return deadlines.'
      },
      {
        question: 'How does the Property Listing mode work for real estate photographers?',
        answer: 'It lets you organize property shoot media by address, MLS listing ID, broker details, and virtual tour links.'
      },
      {
        question: 'Can gear costs be auto-added to client invoice line items?',
        answer: 'Yes. Studio kit rental rates can be transferred directly into the 18% GST invoice in one click.'
      }
    ],
    relatedFeatureSlugs: ['crew-dispatch', 'master-calendar', 'gst-invoicing'],
    mockup: {
      windowTitle: 'Cora Asset & Equipment Inventory Manager',
      activeTabLabel: 'Studio Cinema Gear Registry',
      tabs: [
        { id: 'gear', label: 'Equipment Inventory', badge: '48 Items' },
        { id: 'kits', label: 'Standardized Kits' },
        { id: 'properties', label: 'Property Listings' }
      ],
      headerTitle: 'Cinema Camera, Lens & Lighting Fleet',
      headerSubtitle: 'Total Insured Asset Value: ₹42,50,000 across 4 active shoot locations',
      primaryActionLabel: '+ Check Out Kit',
      metric1: { label: 'Total Gear Value', value: '₹42,50,000' },
      metric2: { label: 'In-Use On Set', value: '18 Items' },
      metric3: { label: 'Available in Studio', value: '30 Items' },
      tableHeaders: ['Asset / Serial #', 'Category', 'Daily Rate', 'Assigned Booking', 'Status'],
      rows: [
        {
          col1: 'Sony FX6 Cinema Camera (SN: #88419)',
          col2: 'Camera Body',
          col3: '₹4,500 / day',
          statusText: 'In-Use on Set',
          statusType: 'warning',
          actionText: 'Inspect'
        },
        {
          col1: 'Sony G-Master 24-70mm f/2.8 II (SN: #19203)',
          col2: 'Cinema Prime Lens',
          col3: '₹1,800 / day',
          statusText: 'In-Use on Set',
          statusType: 'warning',
          actionText: 'Inspect'
        },
        {
          col1: 'Aputure 600d Pro Daylight LED (SN: #77412)',
          col2: 'Studio Lighting',
          col3: '₹2,200 / day',
          statusText: 'Available in Studio',
          statusType: 'success',
          actionText: 'Assign'
        }
      ]
    }
  },
  {
    slug: 'media-hub',
    title: 'Studio Media Hub & RAW Asset Cloud Vault',
    shortTitle: 'Media Hub & RAW',
    category: 'finance',
    categoryLabel: 'Finance & Assets',
    tagline: 'Folder-based cloud media library, aspect-ratio crop presets, and automatic SEO metadata tagging.',
    heroDescription: 'A high-performance cloud storage and digital asset management hub designed for high-resolution photography and 4K/8K video footage. Features instant 1:1, 4:3, 16:9 aspect crop presets and SEO metadata tagging.',
    badgeColor: 'blue',
    iconName: 'HardDrive',
    status: 'Live in Product',
    industries: ['photo_film', 'real_estate'],
    tags: ['Folder Cloud Library', 'Aspect Crop Presets', 'SEO Tagging', 'RAW Storage Vault'],
    stats: [
      { metric: '0 Quality Loss', label: 'Full Resolution RAW' },
      { metric: 'Sub-50ms', label: 'Asset CDN Delivery' },
      { metric: '3 Presets', label: '1:1, 4:3, 16:9 Crops' }
    ],
    theOldWay: [
      'Messy Google Drive and Dropbox folder links expiring or confusing clients with permission errors.',
      'Manually resizing images into square, vertical, and landscape crops in Photoshop for social media posts.',
      'Paying massive monthly storage fees across 3 different personal cloud drives with zero client branding.'
    ],
    theCoraWay: [
      'Branded, white-labeled client media portals with your studio logo, custom domain, and zero third-party ads.',
      'Instant built-in aspect crop selector: generate 1:1 Instagram squares, 4:3 web headers, and 16:9 video thumbs in 1 click.',
      'Assets connect seamlessly into your CMS, website funnels, and proposal decks with zero duplicate uploads.'
    ],
    capabilities: [
      {
        title: 'Client Media Delivery Vaults',
        description: 'Send high-speed download links with optional watermark protections and PIN-code access.',
        tag: 'Branded Delivery'
      },
      {
        title: 'Instant 1-Click Crop Presets',
        description: 'Auto-generate exact pixel crops for Instagram Stories (9:16), Feed Posts (4:5 / 1:1), and Web Banners (16:9).',
        tag: 'Aspect Presets'
      },
      {
        title: 'Automatic SEO & EXIF Metadata',
        description: 'Preserves camera EXIF data and injects optimized alt-text, copyright notices, and schema markup.',
        tag: 'SEO Metadata'
      },
      {
        title: 'High-Bitrate Video Streaming',
        description: 'Ultra-smooth video preview player supporting 4K ProRes and H.265 video reels without buffering.',
        tag: 'Video CDN'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Upload Shoot Assets',
        description: 'Drag and drop RAW files, high-res JPEGs, or 4K video reels into structured client folders.'
      },
      {
        step: '02',
        title: 'Apply Crops & Watermarks',
        description: 'Select social crop presets or apply subtle studio watermark badges with 1 click.'
      },
      {
        step: '03',
        title: 'Share Branded Link',
        description: 'Send client a private, high-speed download link branded with your custom studio domain.'
      }
    ],
    toolsReplaced: [
      { name: 'Dropbox Standard', category: 'Cloud Storage', monthlySavingsUSD: 15, monthlySavingsINR: 1600 },
      { name: 'Google Drive Business', category: 'File Storage', monthlySavingsUSD: 13, monthlySavingsINR: 1800 },
      { name: 'WeTransfer Pro', category: 'File Transfers', monthlySavingsUSD: 15, monthlySavingsINR: 1400 }
    ],
    faqs: [
      {
        question: 'Can clients download all high-res photos in a single ZIP file?',
        answer: 'Yes. Clients can download full-resolution ZIP archives or individual images with one tap.'
      },
      {
        question: 'Is my RAW footage compressed or degraded during upload?',
        answer: 'No. Cora stores original files in pristine, uncompressed bit-for-bit quality on global enterprise cloud storage.'
      },
      {
        question: 'Can I set password protection and link expiration dates?',
        answer: 'Yes. You can protect delivery galleries with custom PINs, passwords, and auto-expire schedules.'
      },
      {
        question: 'Can I use media assets directly inside the Visual Funnel Builder?',
        answer: 'Yes. The Media Hub is universally connected to the Funnel Builder, Content AI, and Proposal engine.'
      }
    ],
    relatedFeatureSlugs: ['asset-gear', 'canvas-builder', 'content-ai'],
    mockup: {
      windowTitle: 'Cora Studio Media Hub & Asset Vault',
      activeTabLabel: 'Gallery: Raymond Autumn Winter 2026',
      tabs: [
        { id: 'gallery', label: 'Media Browser', badge: '240 Files' },
        { id: 'crops', label: 'Aspect Crop Studio' },
        { id: 'client-portal', label: 'Client Delivery Link' }
      ],
      headerTitle: 'Commercial RAW & High-Res Master Vault',
      headerSubtitle: 'Client Portal: https://vault.heycora.in/raymond-aw26 (PIN Protected)',
      primaryActionLabel: '+ Upload 4K Assets',
      metric1: { label: 'Total Files', value: '240 Assets' },
      metric2: { label: 'Vault Size', value: '42.8 GB' },
      metric3: { label: 'Client Downloads', value: '18 Times' },
      tableHeaders: ['Asset Name', 'Resolution / Type', 'Crop Presets', 'File Size', 'Status'],
      rows: [
        {
          col1: 'RAYMOND_HERO_CAM1_0042.RAW',
          col2: '8640 x 5760 (Sony A1)',
          col3: '1:1, 4:3, 16:9 Ready',
          statusText: 'Processed (CDN)',
          statusType: 'success',
          actionText: 'Download'
        },
        {
          col1: 'RAYMOND_LOOKBOOK_REEL_4K.MP4',
          col2: '3840 x 2160 (ProRes 422)',
          col3: '9:16 Vertical Crop',
          statusText: 'Streaming Live',
          statusType: 'success',
          actionText: 'Preview'
        },
        {
          col1: 'RAYMOND_STILL_PORTRAIT_0019.JPG',
          col2: '6000 x 4000 (Color Graded)',
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
    tagline: 'Tenant isolation and granular permissions matrix for Super Admins, Managers, Photographers, and Editors.',
    heroDescription: 'Safeguard your studio data with enterprise-grade multi-tenancy and granular role permissions. Keep financial earnings private while empowering photographers, videographers, and freelance retouchers with exact role permissions.',
    badgeColor: 'zinc',
    iconName: 'Users2',
    status: 'Live in Product',
    tags: ['Multi-Tenant Isolation', '5-Tier Role Matrix', 'Granular Permissions', 'Zero Data Leakage'],
    stats: [
      { metric: '5 Tiers', label: 'Preset Studio Roles' },
      { metric: '100%', label: 'Tenant Isolation' },
      { metric: 'Zero', label: 'Cross-Account Leakage' }
    ],
    theOldWay: [
      'Giving all team members the same admin password because software lacks role-based permissions.',
      'Freelance retouchers accidentally seeing sensitive client pricing, invoice totals, and profit margins.',
      'Chaotic multi-location studio operations with no segregation between branches.'
    ],
    theCoraWay: [
      '5 distinct pre-configured roles: Super Admin, Studio Owner, Studio Manager, Photographer/Crew, and Retoucher/Editor.',
      'Strict financial masking: non-admin roles see assigned call-times and media without viewing financial numbers.',
      'Multi-tenant database segregation ensures complete data privacy across different creative workspaces.'
    ],
    capabilities: [
      {
        title: '5-Tier Structured Role Presets',
        description: 'Instantly assign pre-configured roles tailored for creative studios without complex permission tinkering.',
        tag: 'Role Presets'
      },
      {
        title: 'Financial Value Masking',
        description: 'Hide revenue metrics, deal values, and bank account details from creative crew and freelance contractors.',
        tag: 'Privacy Shield'
      },
      {
        title: 'Granular Module Toggles',
        description: 'Enable or disable specific features (e.g. Invoicing, E-Signs, AI Co-Founder) per team member.',
        tag: 'Permission Matrix'
      },
      {
        title: 'Audit Trail & Login History',
        description: 'Monitor active sessions, IP addresses, and document download timestamps across your entire studio team.',
        tag: 'Security Audit'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Invite Team Member',
        description: 'Enter your team member’s email and select their studio role (e.g. Photographer, Editor).'
      },
      {
        step: '02',
        title: 'Assign Permissions Matrix',
        description: 'Toggle access to specific studio bays, media folders, or calendar schedules.'
      },
      {
        step: '03',
        title: 'Secure Isolated Access',
        description: 'Team member logs in with instant access only to their relevant operational tools.'
      }
    ],
    toolsReplaced: [
      { name: 'Okta / Auth0 Enterprise Addon', category: 'User Access Control', monthlySavingsUSD: 50, monthlySavingsINR: 4200 },
      { name: '1Password Team Vaults', category: 'Shared Access', monthlySavingsUSD: 20, monthlySavingsINR: 1700 },
      { name: 'Custom User Role Plugins', category: 'WordPress Roles', monthlySavingsUSD: 10, monthlySavingsINR: 850 }
    ],
    faqs: [
      {
        question: 'Can I add freelance contractors for a single weekend shoot?',
        answer: 'Yes. You can invite temporary contractors with automatic account expiration after the shoot date.'
      },
      {
        question: 'Can my studio manager generate invoices without seeing total annual revenue?',
        answer: 'Yes. Permissions can be configured to allow single-invoice generation while locking aggregate P&L analytics.'
      },
      {
        question: 'Is two-factor authentication (2FA) supported?',
        answer: 'Yes. Multi-factor authentication via authenticator apps (Google Authenticator, 1Password) is supported.'
      },
      {
        question: 'How is tenant data isolated on the database level?',
        answer: 'All database queries enforce strict tenant_id scoping with cryptographic session tokens to prevent data leakage.'
      }
    ],
    relatedFeatureSlugs: ['super-admin', 'crew-dispatch', 'media-hub'],
    mockup: {
      windowTitle: 'Cora Multi-Tenant RBAC & Team Access Governance',
      activeTabLabel: 'Team Roles & Permissions Matrix',
      tabs: [
        { id: 'members', label: 'Team Members', badge: '6 Users' },
        { id: 'roles', label: 'Role Permissions Matrix' },
        { id: 'security', label: 'Active Sessions & 2FA' }
      ],
      headerTitle: 'Role-Based Access Control & Tenant Security',
      headerSubtitle: 'Active Workspace: Studio Cora Mumbai (Tenant ID: #TN-98124)',
      primaryActionLabel: '+ Invite Team Member',
      metric1: { label: 'Active Users', value: '6 Team Seats' },
      metric2: { label: 'Security Score', value: '100% 2FA' },
      metric3: { label: 'Financial Masking', value: 'Active (3 Users)' },
      tableHeaders: ['User Name / Email', 'Studio Role', 'Financial Visibility', 'Assigned Modules', 'Status'],
      rows: [
        {
          col1: 'Studio Director (admin@cora.local)',
          col2: 'Super Administrator',
          col3: 'Full Access (P&L, GST)',
          statusText: 'All 20 Modules',
          statusType: 'success',
          actionText: 'Manage'
        },
        {
          col1: 'Kabir Sharma (kabir.dp@gmail.com)',
          col2: 'Photographer / DP',
          col3: 'Masked (Zero Visibility)',
          statusText: 'Calendar, Crew, Gear',
          statusType: 'neutral',
          actionText: 'Edit Role'
        },
        {
          col1: 'Pooja Verma (pooja.edit@gmail.com)',
          col2: 'Retoucher / Colorist',
          col3: 'Masked (Zero Visibility)',
          statusText: 'Task Board, Media Hub',
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
    tagline: 'Visual HTML template composer, dynamic variable tags, SMTP tester, and live outbox delivery logs.',
    heroDescription: 'Send professional studio proposals, booking confirmations, and invoices from your own custom domain email address (e.g. bookings@yourstudio.com). Includes DKIM/SPF diagnostics and live delivery logs.',
    badgeColor: 'sky',
    iconName: 'Mail',
    status: 'Live in Product',
    tags: ['Custom Domain SMTP', 'Visual HTML Composer', 'SPF/DKIM Health Check', 'Live Delivery Outbox'],
    stats: [
      { metric: '99.8%', label: 'Inbox Deliverability' },
      { metric: 'Zero Spam', label: 'DKIM/SPF Diagnostic' },
      { metric: '100% Brand', label: 'Your Custom Domain' }
    ],
    theOldWay: [
      'Sending business proposals from free @gmail.com addresses that look unprofessional to enterprise clients.',
      'Transactional emails landing in client spam folders because of missing SPF, DKIM, or DMARC DNS records.',
      'Paying $20–$50/mo for separate transactional email services like SendGrid or Mailgun.'
    ],
    theCoraWay: [
      'Connect any custom domain SMTP server (Google Workspace, Microsoft 365, Zoho, or custom cPanel mail).',
      'Built-in SMTP diagnostics verify SPF/DKIM DNS health with one-click test email dispatches.',
      'Visual template composer with dynamic variables: {{client_name}}, {{shoot_date}}, {{invoice_total}}, and {{sign_link}}.'
    ],
    capabilities: [
      {
        title: 'Custom SMTP Connection',
        description: 'Route all outgoing emails through your verified Google Workspace, Office 365, or private mail server.',
        tag: 'Custom SMTP'
      },
      {
        title: 'Visual Email Template Builder',
        description: 'Create elegant, responsive email templates styled with clean typography, buttons, and studio logos.',
        tag: 'HTML Composer'
      },
      {
        title: 'Live Outbox Audit Logs',
        description: 'Track real-time email delivery statuses: Sent, Delivered, Opened, and Bounced with detailed server responses.',
        tag: 'Delivery Audit'
      },
      {
        title: 'Dynamic Personalization Tags',
        description: 'Auto-populate client names, invoice totals, call-times, and contract URLs with dynamic variables.',
        tag: 'Dynamic Tags'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Connect SMTP Server',
        description: 'Enter your host, port, username, and password with automatic TLS/SSL encryption.'
      },
      {
        step: '02',
        title: 'Run Health Diagnostic',
        description: 'Cora tests connection latency, authenticates credentials, and verifies SPF/DKIM DNS records.'
      },
      {
        step: '03',
        title: 'Automate Outbox Dispatch',
        description: 'All system notifications, contracts, and invoices dispatch seamlessly under your custom address.'
      }
    ],
    toolsReplaced: [
      { name: 'SendGrid Pro', category: 'Transactional Email', monthlySavingsUSD: 20, monthlySavingsINR: 1700 },
      { name: 'Mailgun Foundation', category: 'Email API', monthlySavingsUSD: 35, monthlySavingsINR: 2900 },
      { name: 'Postmark Basic', category: 'Email Delivery', monthlySavingsUSD: 15, monthlySavingsINR: 1250 }
    ],
    faqs: [
      {
        question: 'Can I connect Google Workspace or Gmail with an App Password?',
        answer: 'Yes. Google Workspace and Gmail SMTP connection via standard secure 16-character App Passwords is fully supported.'
      },
      {
        question: 'Does this help prevent my invoices from landing in spam folders?',
        answer: 'Yes. Sending from a verified domain with proper SPF/DKIM authentication ensures near 100% inbox placement.'
      },
      {
        question: 'Can I preview how templates look on mobile screens before sending?',
        answer: 'Yes. The template composer includes a live side-by-side mobile smartphone preview.'
      },
      {
        question: 'Can I see if a client has opened my proposal email?',
        answer: 'Yes. The delivery log tracks email opens and link click-throughs in real-time.'
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
      headerSubtitle: 'Active Host: smtp.gmail.com:587 • Sender: bookings@studiocora.in (SPF/DKIM Valid)',
      primaryActionLabel: 'Send Test Email',
      metric1: { label: 'Delivery Rate', value: '99.8%' },
      metric2: { label: 'Emails Sent', value: '1,420 this mo' },
      metric3: { label: 'Server Latency', value: '240ms' },
      tableHeaders: ['Recipient', 'Subject Line', 'Template Used', 'Timestamp', 'Delivery Status'],
      rows: [
        {
          col1: 'client@raymond.in',
          col2: 'Action Required: Your Shoot Contract is Ready',
          col3: 'E-Sign Contract Notification',
          statusText: 'Opened (2 mins ago)',
          statusType: 'success',
          actionText: 'View Raw Log'
        },
        {
          col1: 'accounts@titan.co.in',
          col2: 'Tax Invoice #INV-2026-0103 + UPI QR',
          col3: 'GST Tax Invoice Dispatch',
          statusText: 'Delivered (Inbox)',
          statusType: 'success',
          actionText: 'View Raw Log'
        },
        {
          col1: 'producer@nykaa.com',
          col2: 'Confirmed: Commercial Shoot Booking Aug 29',
          col3: 'Booking Confirmation',
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
    tagline: 'Installable mobile PWA, VAPID background push notifications, offline service worker, and zero tap delay.',
    heroDescription: 'Experience native app performance directly in your mobile browser with zero app store downloads. Install Cora on your iPhone or Android home screen with instant VAPID push notifications and offline caching.',
    badgeColor: 'emerald',
    iconName: 'Smartphone',
    status: 'Live in Product',
    tags: ['Installable PWA', 'VAPID Push Notifications', 'Offline Cache Lifecycle', 'Zero Tap Delay'],
    stats: [
      { metric: '< 50ms', label: 'App Load Speed' },
      { metric: '0ms', label: 'Mobile Tap Delay' },
      { metric: 'iOS & Android', label: 'Home Screen Native' }
    ],
    theOldWay: [
      'Clunky mobile web dashboards that force browser URL bars, reload on every tap, and feel sluggish.',
      'Missing urgent client booking alerts because web apps cannot send native push notifications.',
      'Paying thousands of dollars for native mobile app store developers to build separate iOS and Android apps.'
    ],
    theCoraWay: [
      'Pure standalone PWA experience: runs full-screen with native gestures, standalone link retention, and zero lag.',
      'VAPID background web push notifications alert you instantly when clients sign contracts or pay invoices.',
      'Sub-400ms service worker caching enables rapid screen transitions and offline shoot schedule inspection.'
    ],
    capabilities: [
      {
        title: 'Native Home Screen Installation',
        description: 'Add to iPhone or Android home screen with dynamic versioned high-res app icons and native splash screens.',
        tag: 'PWA WebAPK'
      },
      {
        title: 'Instant VAPID Push Alerts',
        description: 'Receive real-time push alerts on your lock screen for new inquiries, contract e-signs, and UPI payments.',
        tag: 'Lock Screen Push'
      },
      {
        title: 'Zero Tap Delay Optimization',
        description: 'Hardware accelerated touch handling eliminates the mobile browser 300ms tap delay completely.',
        tag: 'Touch Snappiness'
      },
      {
        title: 'Standalone In-App Link Retention',
        description: 'Smart routing prevents browser breakout, keeping all workspace interactions inside the standalone app window.',
        tag: 'Native Container'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Open in Mobile Browser',
        description: 'Visit app.heycora.in on Safari (iOS) or Chrome (Android).'
      },
      {
        step: '02',
        title: 'Add to Home Screen',
        description: 'Tap "Add to Home Screen" to install the native lightweight WebAPK with custom Cora icon.'
      },
      {
        step: '03',
        title: 'Enable Push Notifications',
        description: 'Allow push notifications to receive instant lead, booking, and payment alerts directly on your device.'
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
        answer: 'Yes. Apple iOS 16.4+ natively supports Web Push notifications for PWAs added to the home screen.'
      },
      {
        question: 'Can I access my shoot schedule offline without internet on set?',
        answer: 'Yes. The Cora Service Worker caches your recent calendar bookings, shoot call-sheets, and gear checklists.'
      },
      {
        question: 'How do app updates work in PWA mode?',
        answer: 'Cora includes an automatic in-app update banner that refreshes assets in sub-300ms with zero downtime.'
      },
      {
        question: 'Does it take up massive storage space on my phone?',
        answer: 'No. The entire Cora PWA core is under 5MB, making it 50x lighter than typical bloated native apps.'
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
    tagline: 'Notion-styled 3-column documentation at /docs, command palette search (⌘K), and API playground.',
    heroDescription: 'Comprehensive developer documentation and studio integration guides. Build custom workflow integrations with REST endpoints, webhooks, and Model Context Protocol (MCP) servers with interactive code snippets.',
    badgeColor: 'blue',
    iconName: 'BookOpen',
    status: 'Live in Product',
    tags: ['Notion 3-Column Layout', '⌘K Command Palette', 'Interactive API Playground', 'Open Guides'],
    stats: [
      { metric: '100% Open', label: 'REST API Specs' },
      { metric: '⌘K Search', label: 'Instant Navigation' },
      { metric: '5 Languages', label: 'cURL, JS, Python, PHP' }
    ],
    theOldWay: [
      'Messy, outdated PDF user manuals that team members never read.',
      'Closed-source SaaS tools that prevent you from exporting your data or connecting custom workflows.',
      'No API documentation or webhook specs for developers building custom studio software.'
    ],
    theCoraWay: [
      'Clean Notion-styled documentation layout with instant ⌘K search and categorized visual guides.',
      'Interactive API playground letting developers test live REST endpoints directly in the browser.',
      'Copy-paste code snippets in cURL, JavaScript, TypeScript, Python, and PHP for rapid development.'
    ],
    capabilities: [
      {
        title: '3-Column Notion Design System',
        description: 'Left navigation tree, center high-density Markdown documentation, and right on-page anchor table of contents.',
        tag: 'Notion Aesthetic'
      },
      {
        title: '⌘K Quick-Search Command Palette',
        description: 'Instant client-side fuzzy search finding any endpoint, webhook, or guide in under 20 milliseconds.',
        tag: 'Fast Search'
      },
      {
        title: 'Interactive REST API Playground',
        description: 'Test API keys, request headers, and payload structures directly without opening Postman.',
        tag: 'Live Testing'
      },
      {
        title: 'Comprehensive Webhook Guides',
        description: 'Complete documentation for Lead Captured, Contract Signed, and Invoice Paid webhook triggers.',
        tag: 'Webhooks'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Browse Guides & Endpoints',
        description: 'Navigate to /docs to explore tutorials for Framer embeds, GST invoicing, or MCP agent setups.'
      },
      {
        step: '02',
        title: 'Generate API Key',
        description: 'Create a scoped API token in your workspace settings with read/write access permissions.'
      },
      {
        step: '03',
        title: 'Execute & Automate',
        description: 'Paste the snippet into your code to automate contract creation or lead intake seamlessly.'
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
        answer: 'The documentation is publicly accessible at https://heycora.in/docs with full guides and interactive examples.'
      },
      {
        question: 'Are webhooks supported for real-time external notifications?',
        answer: 'Yes. Secure HMAC-SHA256 signed webhooks are dispatched for all major deal, booking, and payment events.'
      },
      {
        question: 'Can I use Cora APIs in Node.js, Next.js, and Python?',
        answer: 'Yes. Standard RESTful JSON endpoints can be consumed in any modern programming language.'
      },
      {
        question: 'Is rate limiting enforced on developer API keys?',
        answer: 'Yes. Generous rate limits (120 requests/minute) protect workspace stability with burst allowance.'
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
      headerTitle: 'Cora Studio OS Developer Documentation',
      headerSubtitle: 'Base URL: https://api.heycora.in/v1 • Auth: Bearer cora_live_sec_...',
      primaryActionLabel: 'Test in Playground',
      metric1: { label: 'API Uptime', value: '99.98%' },
      metric2: { label: 'Avg Latency', value: '42ms' },
      metric3: { label: 'Endpoints', value: '28 Live' },
      tableHeaders: ['Method & Path', 'Description', 'Auth Scope', 'Rate Limit', 'Status'],
      rows: [
        {
          col1: 'POST /api/v1/contracts/sign',
          col2: 'Create and seal SHA-256 e-signature contract',
          col3: 'write:contracts',
          statusText: '200 OK (52ms)',
          statusType: 'success',
          actionText: 'View Schema'
        },
        {
          col1: 'POST /api/v1/invoices/generate-gst',
          col2: 'Compute 18% GST and generate dynamic UPI QR',
          col3: 'write:invoices',
          statusText: '200 OK (38ms)',
          statusType: 'success',
          actionText: 'View Schema'
        },
        {
          col1: 'GET /api/v1/calendar/availability',
          col2: 'Query studio bay and gear schedule conflicts',
          col3: 'read:calendar',
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
    tagline: 'Global workspace management, module toggles, AI token usage metrics, and audit log inspector.',
    heroDescription: 'Complete administrative control over all studio workspaces, tenant provisioning, feature flag toggles, and AI token consumption. Inspect real-time server health and security event logs.',
    badgeColor: 'zinc',
    iconName: 'Settings',
    status: 'Live in Product',
    tags: ['Global Workspace Manager', 'Feature Module Toggles', 'Token Quota Monitor', 'Security Audit Logs'],
    stats: [
      { metric: '100% Control', label: 'Platform Governance' },
      { metric: 'Real-Time', label: 'AI Token Tracking' },
      { metric: 'Zero-Lag', label: 'Workspace Provisioning' }
    ],
    theOldWay: [
      'No central visibility into which features your team is actually utilizing or where AI token limits are being exceeded.',
      'Manual database scripts required to provision new client workspaces or update subscription limits.',
      'Zero security audit logs to investigate suspicious IP logins or unauthorized contract deletions.'
    ],
    theCoraWay: [
      'Centralized Super Admin governance panel with one-click workspace switching and tenant metric inspection.',
      'Real-time AI model token monitor tracking Gemini 3.5 Flash, Claude 3.5 Sonnet, and GPT-4o usage per workspace.',
      'Granular feature flag toggles to enable alpha and beta modules on a per-tenant basis.'
    ],
    capabilities: [
      {
        title: 'Global Tenant Provisioning',
        description: 'Spin up new, fully isolated creative agency workspaces with seeded templates in under 5 seconds.',
        tag: 'Auto-Provisioning'
      },
      {
        title: 'AI Model Token Quota Engine',
        description: 'Track prompt and completion token counts across models with automatic soft-cap warnings.',
        tag: 'Token Analytics'
      },
      {
        title: 'Granular Feature Flag Toggles',
        description: 'Activate specific advanced modules (e.g. WhatsApp API, Video Storyboard) per individual tenant.',
        tag: 'Feature Flags'
      },
      {
        title: 'Immutable Security Audit Logs',
        description: 'Timestamped record of all administrative actions, credential changes, and financial data exports.',
        tag: 'Audit Logs'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Access Admin Console',
        description: 'Open the sticky sidebar admin popover widget and launch the Governance Suite.'
      },
      {
        step: '02',
        title: 'Inspect Tenant Metrics',
        description: 'View active user sessions, token consumption, revenue throughput, and database storage sizes.'
      },
      {
        step: '03',
        title: 'Manage Feature Toggles',
        description: 'Enable custom quotas or upgrade tiers with zero server restart or service interruption.'
      }
    ],
    toolsReplaced: [
      { name: 'Retool Custom Admin', category: 'Internal Tools', monthlySavingsUSD: 50, monthlySavingsINR: 4200 },
      { name: 'Datadog Log Monitor', category: 'Audit Logs', monthlySavingsUSD: 65, monthlySavingsINR: 5400 },
      { name: 'LaunchDarkly Starter', category: 'Feature Flags', monthlySavingsUSD: 25, monthlySavingsINR: 2000 }
    ],
    faqs: [
      {
        question: 'Who has access to the Super Admin Governance Suite?',
        answer: 'Only designated Platform Super Administrators with verified credentials and multi-factor authentication.'
      },
      {
        question: 'Can Super Admins view private client contracts of other tenants?',
        answer: 'Tenant isolation architecture prevents unauthorized document content viewing while exposing operational metadata.'
      },
      {
        question: 'Can I set monthly AI token spending limits?',
        answer: 'Yes. You can configure hard and soft caps per workspace to prevent accidental overages.'
      },
      {
        question: 'Are administrative changes recorded in immutable logs?',
        answer: 'Yes. Every feature toggle, quota change, and tenant modification is logged with IP address and timestamp.'
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
      headerSubtitle: 'Super Admin: Platform Admin (admin@cora.local) • Status: Healthy (0.36s Response)',
      primaryActionLabel: '+ Provision Workspace',
      metric1: { label: 'Active Workspaces', value: '4 Tenants' },
      metric2: { label: 'Monthly AI Runs', value: '142,800 Runs' },
      metric3: { label: 'System Health', value: '100% Uptime' },
      tableHeaders: ['Workspace Name', 'Industry Vertical', 'Plan Tier', 'AI Token Usage', 'Status'],
      rows: [
        {
          col1: 'Horizon Film & Commercial Studio',
          col2: 'Commercial Photography',
          col3: 'Professional Annual (₹19,990)',
          statusText: '14,200 / 21k Tokens',
          statusType: 'success',
          actionText: 'Manage'
        },
        {
          col1: 'Apex Architectural Media',
          col2: 'Real Estate & Video',
          col3: 'Starter Annual (₹9,990)',
          statusText: '4,800 / 6k Tokens',
          statusType: 'success',
          actionText: 'Manage'
        },
        {
          col1: 'Solopreneur Studio — Goa',
          col2: 'Wedding & Events',
          col3: 'India Only Plan (₹499/mo)',
          statusText: '2,100 / 3.5k Tokens',
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
    tagline: 'Multi-step setup wizard, 3D logo pedestal, and automatic industry schema seeding.',
    heroDescription: 'Get your entire creative studio operational in under 3 minutes. Automatically seeds tailored rate cards, contract templates, and CRM pipeline stages for Commercial Photography, Real Estate, Video, or Creative Agencies.',
    badgeColor: 'amber',
    iconName: 'Compass',
    status: 'Live in Product',
    tags: ['3D Logo Pedestal', 'Industry Schema Seeding', '3-Minute Setup', 'Zero Friction Flow'],
    stats: [
      { metric: '< 3 Mins', label: 'Complete Setup' },
      { metric: '4 Verticals', label: 'Pre-Seeded Blueprints' },
      { metric: 'Zero Effort', label: 'Instant Rate Cards' }
    ],
    theOldWay: [
      'Spending weeks configuring empty enterprise software before sending your first proposal.',
      'Having to write contract legal terms, invoice formats, and rate cards from scratch.',
      'Frustrated team members abandoning complex setups and returning to disorganized WhatsApp chats.'
    ],
    theCoraWay: [
      'Select your creative vertical (Commercial Studio, Real Estate, Film/Video, Solopreneur) and get instant pre-seeded data.',
      'Interactive 3D logo pedestal displays your brand identity in high-fidelity immediately on launch.',
      'Ready-to-use rate cards, 18% GST calculations, and SHA-256 legal contract templates populated out of the box.'
    ],
    capabilities: [
      {
        title: 'Industry Vertical Seeding',
        description: 'Auto-populates specialized packages, SAC codes, contract clauses, and pipeline stages tailored to your field.',
        tag: 'Vertical Blueprints'
      },
      {
        title: 'Interactive 3D Brand Pedestal',
        description: 'Upload your studio logo and watch it rendered on an elegant monochromatic rotating 3D pedestal.',
        tag: '3D Brand Engine'
      },
      {
        title: 'GST & Bank QR Configuration',
        description: 'Input your UPI ID and GSTIN for automated invoice styling in under 45 seconds.',
        tag: 'Fast Billing Setup'
      },
      {
        title: 'Sample Data Sandbox',
        description: 'Explore live sample deals, bookings, and signed contracts to understand the workflow before going live.',
        tag: 'Guided Demo'
      }
    ],
    howItWorks: [
      {
        step: '01',
        title: 'Pick Your Creative Industry',
        description: 'Choose Commercial Photography, Real Estate Video, Film Production, or Solopreneur.'
      },
      {
        step: '02',
        title: 'Upload Studio Logo & UPI ID',
        description: 'Set your brand assets and payment handle for instant white-labeled client portals.'
      },
      {
        step: '03',
        title: 'Launch Pre-Configured OS',
        description: 'Your workspace opens with pre-loaded rate cards, e-sign contracts, and CRM deals ready to go.'
      }
    ],
    toolsReplaced: [
      { name: 'Expensive Onboarding Consultants', category: 'Software Setup', monthlySavingsUSD: 250, monthlySavingsINR: 20000 },
      { name: 'Template Packs & Contract PDFs', category: 'Legal Templates', monthlySavingsUSD: 99, monthlySavingsINR: 8000 },
      { name: 'Zapier Setup Services', category: 'Integration Work', monthlySavingsUSD: 150, monthlySavingsINR: 12000 }
    ],
    faqs: [
      {
        question: 'Can I change my industry blueprint after completing onboarding?',
        answer: 'Yes. You can switch or customize all rate cards, contract terms, and pipeline stages anytime in workspace settings.'
      },
      {
        question: 'Is sample demo data easy to clear once I start using real client data?',
        answer: 'Yes. A single "Clear Sample Data" button removes demo records while keeping your configured settings intact.'
      },
      {
        question: 'How long does the entire setup take?',
        answer: 'Most studios complete the 4-step guided onboarding in under 3 minutes.'
      },
      {
        question: 'Can I invite my team during the onboarding flow?',
        answer: 'Yes. You can invite team members with assigned roles during step 3 of the wizard.'
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
      headerTitle: 'Studio Workspace Customization & Blueprint Seeding',
      headerSubtitle: 'Selected Blueprint: Commercial Photography & Film Production Studio',
      primaryActionLabel: 'Save & Continue →',
      metric1: { label: 'Setup Progress', value: '50% Complete' },
      metric2: { label: 'Est. Time Left', value: '1m 15s' },
      metric3: { label: 'Pre-Seeded Items', value: '18 Blueprints' },
      tableHeaders: ['Setup Step', 'Configuration Area', 'Status', 'Pre-Seeded Data', 'Action'],
      rows: [
        {
          col1: '1. Select Vertical',
          col2: 'Industry Specialty Blueprint',
          col3: 'Commercial Photography',
          statusText: 'Completed',
          statusType: 'success',
          actionText: 'Change'
        },
        {
          col1: '2. Brand Identity & Logo',
          col2: '3D Pedestal & Branded Header',
          col3: 'Logo Uploaded (studio_logo.svg)',
          statusText: 'Active Step',
          statusType: 'warning',
          actionText: 'Configure'
        },
        {
          col1: '3. 18% GST & UPI Handle',
          col2: 'Automated Invoice Styling',
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
    desc: 'Direct 2-way client chat embedded inside CRM, template broadcasts, automated booking reminder bots, and quick-reply scopes.',
    iconName: 'MessageCircle',
    eta: 'Q3 2026',
    status: 'Building Soon',
    categoryLabel: 'Sales & Client CRM'
  },
  {
    id: 'photo-proofing',
    title: 'Client Photo Proofing & Selection Portal 2.0',
    desc: 'Watermarked client selection galleries, favorite star ratings, photo feedback pin drops, and multi-version album approvals.',
    iconName: 'ImageIcon',
    eta: 'Q3 2026',
    status: 'Building Soon',
    categoryLabel: 'Finance & Media',
    industries: ['photo_film', 'real_estate']
  },
  {
    id: 'integrated-payments',
    title: 'Integrated Payment Gateways (Auto-Reconcile)',
    desc: 'Direct Razorpay, Stripe, and UPI payment collection links embedded on invoices with instant automated ledger reconciliation.',
    iconName: 'CreditCard',
    eta: 'Q3 2026',
    status: 'Building Soon',
    categoryLabel: 'Finance & Assets'
  },
  {
    id: 'video-storyboard',
    title: 'AI Video Script & Motion Graphic Storyboard',
    desc: 'Presentation deck generator, video editing prompts, and viral YouTube Shorts & Instagram Reels scriptwriting engine.',
    iconName: 'Video',
    eta: 'Q4 2026',
    status: 'Building Soon',
    categoryLabel: 'Intelligence & AI',
    industries: ['photo_film', 'creative_agencies']
  },
  {
    id: 'multi-branch',
    title: 'Multi-Branch & Franchise Workspace System',
    desc: 'Multi-location studio management, cross-branch consolidated financial reporting, and shared regional gear inventory pools.',
    iconName: 'GitBranch',
    eta: 'Q4 2026',
    status: 'Building Soon',
    categoryLabel: 'Platform & Governance'
  },
  {
    id: 'voice-ai-agent',
    title: 'Autonomous Voice AI Call Agent',
    desc: 'Inbound and outbound conversational voice AI via ElevenLabs & Twilio for automated booking confirmations and buyer qualification.',
    iconName: 'PhoneCall',
    eta: 'Q4 2026',
    status: 'Building Soon',
    categoryLabel: 'Intelligence & AI'
  },
  {
    id: 'tally-zoho-export',
    title: 'Automated Accounting & Tally/Zoho Export',
    desc: 'One-click GSTR-1 ready sales ledger export in structured XML/JSON format for Tally Prime and Zoho Books CA sync.',
    iconName: 'FileSpreadsheet',
    eta: 'Q4 2026',
    status: 'Building Soon',
    categoryLabel: 'Finance & Assets'
  },
  {
    id: 'client-mobile-app',
    title: 'White-Labeled Client Mobile Companion App',
    desc: 'Native iOS & Android app for studio clients to sign contracts, track shoot milestones, pay invoices, and view proofs.',
    iconName: 'TabletSmartphone',
    eta: 'Q1 2027',
    status: 'Building Soon',
    categoryLabel: 'Platform & Governance'
  }
];
