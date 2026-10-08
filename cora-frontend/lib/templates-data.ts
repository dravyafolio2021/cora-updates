export interface TemplatePageItem {
  name: string;
  slug: string;
  description: string;
  type: 'landing' | 'portal' | 'form' | 'invoice' | 'contract' | 'dashboard';
}

export interface TemplateSchemaField {
  name: string;
  type: string;
  required: boolean;
  description: string;
}

export interface TemplateSchemaEntity {
  name: string;
  badge: string;
  description: string;
  fields: TemplateSchemaField[];
}

export interface TemplateItem {
  id: string;
  slug: string;
  title: string;
  shortTitle: string;
  tagline: string;
  category: 'tech_dev' | 'creative_design' | 'legal_finance' | 'marketing_growth' | 'services_lifestyle';
  categoryLabel: string;
  badge: string;
  sacCode: string;
  gstRate: string;
  rating: number;
  reviewsCount: number;
  downloadsCount: number;
  heroDescription: string;
  accentBg: string;
  accentBorder: string;
  accentText: string;
  accentColor: string;
  coverImage: string;
  includedPages: TemplatePageItem[];
  schemaStructures: TemplateSchemaEntity[];
  features: string[];
  recommendedModules: { id: string; title: string; icon: string }[];
  toolsReplaced: { name: string; monthlyCostUSD: number }[];
  workflowHighlights: string[];
  faqs: { question: string; answer: string }[];
}

export interface TemplateCategory {
  id: string;
  label: string;
  shortLabel: string;
  badge: string;
  count: number;
}

export const TEMPLATE_CATEGORIES: TemplateCategory[] = [
  { id: 'all', label: 'All Templates & Schemas', shortLabel: 'All', badge: '16+ SCHEMAS', count: 16 },
  { id: 'tech_dev', label: 'Tech & Development', shortLabel: 'Tech & Dev', badge: 'TECH & DEV', count: 4 },
  { id: 'creative_design', label: 'Creative & Design', shortLabel: 'Design & Media', badge: 'DESIGN & MEDIA', count: 4 },
  { id: 'legal_finance', label: 'Legal & Finance', shortLabel: 'Legal & Finance', badge: 'LEGAL & FINANCE', count: 4 },
  { id: 'marketing_growth', label: 'Marketing & Advisory', shortLabel: 'Marketing', badge: 'MARKETING', count: 2 },
  { id: 'services_lifestyle', label: 'Services & Lifestyle', shortLabel: 'Services', badge: 'SERVICES', count: 2 },
];

export const TEMPLATES_DATA: TemplateItem[] = [
  {
    id: 'web-dev-agency',
    slug: 'web-development-agency',
    title: 'Web & App Development Agency Kit',
    shortTitle: 'Web & Dev Agency',
    tagline: 'Turnkey client staging vaults, milestone sprint billing & SAC 9983 invoicing.',
    category: 'tech_dev',
    categoryLabel: 'Tech & Development',
    badge: 'Popular Kit',
    sacCode: 'SAC 998314',
    gstRate: '18% GST',
    rating: 4.96,
    reviewsCount: 142,
    downloadsCount: 1840,
    heroDescription: 'Complete operating framework for web agencies. Features staging client approval portals, scope change request locks, and automated milestone invoicing.',
    accentBg: 'bg-blue-50',
    accentBorder: 'border-blue-200',
    accentText: 'text-blue-900',
    accentColor: '#2563EB',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Agency Flagship Landing', slug: 'home', description: 'High-converting interactive hero with instant project estimator', type: 'landing' },
      { name: 'Interactive Services Matrix', slug: 'services', description: 'Tiered dev packages with dynamic scope calculator', type: 'landing' },
      { name: 'Client Staging Portal', slug: 'portal', description: 'Passwordless client preview and visual feedback hub', type: 'portal' },
      { name: 'Milestone Tax Invoice', slug: 'invoice', description: '18% GST SAC 9983 B2B invoice with instant UPI QR', type: 'invoice' },
      { name: 'Master Services Agreement', slug: 'contract', description: 'SHA-256 digital signature legal agreement with IP transfer', type: 'contract' },
    ],
    schemaStructures: [
      {
        name: 'Sprint Deliverables',
        badge: 'Dev Registry',
        description: 'Tracks milestones, staging URLs, QA signoffs, and release dates',
        fields: [
          { name: 'milestone_title', type: 'String', required: true, description: 'Sprint or milestone phase label' },
          { name: 'staging_url', type: 'URL', required: true, description: 'Isolated staging review subdomain' },
          { name: 'signoff_status', type: 'Enum', required: true, description: 'Pending, In-Review, Approved, Revision' },
          { name: 'payment_trigger', type: 'Boolean', required: true, description: 'Auto-release invoice upon approval' },
        ],
      },
      {
        name: 'Scope Change Registry',
        badge: 'Contract Gate',
        description: 'Locks out-of-scope client requests with digital addendums',
        fields: [
          { name: 'request_details', type: 'Text', required: true, description: 'Client request specification' },
          { name: 'additional_hours', type: 'Number', required: true, description: 'Billable sprint hour estimation' },
          { name: 'approval_signature', type: 'DigitalSign', required: true, description: 'SHA-256 client authorization' },
        ],
      },
    ],
    features: ['Staging feedback collection', 'Automated GST SAC 9983 calculation', 'SHA-256 MSA e-signatures', 'Milestone release payment gates'],
    recommendedModules: [
      { id: 'portals', title: 'Client Portals', icon: 'Globe' },
      { id: 'invoices', title: 'GST Invoicing', icon: 'Receipt' },
      { id: 'contracts', title: 'E-Sign Vault', icon: 'ShieldCheck' },
    ],
    toolsReplaced: [
      { name: 'HoneyBook / Dubsado', monthlyCostUSD: 40 },
      { name: 'PandaDoc', monthlyCostUSD: 35 },
      { name: 'Staging Feedback Plugin', monthlyCostUSD: 29 },
    ],
    workflowHighlights: [
      'Automatic staging subdomain generation upon contract execution',
      'Client feedback pinned directly to UI coordinates',
      'Instant UPI payment QR embedded directly into invoice PDF',
    ],
    faqs: [
      { question: 'Can I connect our custom domain?', answer: 'Yes, every template supports custom white-label domains and SSL.' },
      { question: 'Is 18% GST calculation automated?', answer: 'Yes, both intra-state CGST/SGST and inter-state IGST with SAC 9983 are automated.' },
    ],
  },
  {
    id: 'creative-branding-kit',
    slug: 'creative-branding-studio',
    title: 'Brand Design & Creative Studio Schema',
    shortTitle: 'Branding Studio',
    tagline: 'High-res brand asset delivery vaults, moodboards & digital copyright transfer.',
    category: 'creative_design',
    categoryLabel: 'Creative & Design',
    badge: 'Turnkey Studio',
    sacCode: 'SAC 998311',
    gstRate: '18% GST',
    rating: 4.98,
    reviewsCount: 118,
    downloadsCount: 1520,
    heroDescription: 'Designed for brand identity agencies. Delivers vector assets, style guides, and font licenses inside password-protected client download vaults.',
    accentBg: 'bg-purple-50',
    accentBorder: 'border-purple-200',
    accentText: 'text-purple-900',
    accentColor: '#7C3AED',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Creative Studio Showreel', slug: 'home', description: 'Minimalist editorial showcase with grid case studies', type: 'landing' },
      { name: 'Brand Identity Vault', slug: 'asset-vault', description: 'Gated SVG, PNG, PDF asset delivery vault with licensing', type: 'portal' },
      { name: 'Scope Defense Contract', slug: 'contract', description: 'Revision limits & copyright assignment agreement', type: 'contract' },
      { name: 'Advance Retainer Invoice', slug: 'invoice', description: '50% upfront retainer invoice with SAC 9983 billing', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Brand Deliverables Vault',
        badge: 'Asset Gate',
        description: 'Categorizes typography, vector logos, color palettes, and guidelines',
        fields: [
          { name: 'asset_type', type: 'Enum', required: true, description: 'Primary Logo, Secondary Mark, Typography, Palette' },
          { name: 'file_download_url', type: 'StorageURL', required: true, description: 'Protected high-resolution asset link' },
          { name: 'license_grant', type: 'Text', required: true, description: 'Commercial usage rights certificate' },
        ],
      },
    ],
    features: ['High-res asset delivery gates', '3-round revision limit contract', 'Instant advance deposit checkout', 'Brand guidelines interactive view'],
    recommendedModules: [
      { id: 'vault', title: 'Asset Vaults', icon: 'FolderKanban' },
      { id: 'contracts', title: 'Copyright E-Sign', icon: 'ShieldCheck' },
      { id: 'invoices', title: 'Milestone Billing', icon: 'Receipt' },
    ],
    toolsReplaced: [
      { name: 'Dropbox Pro', monthlyCostUSD: 20 },
      { name: 'DocuSign', monthlyCostUSD: 40 },
      { name: 'Invoicing SaaS', monthlyCostUSD: 25 },
    ],
    workflowHighlights: [
      'Client e-signs contract before download link unlocks',
      'Automatic watermark protection on unapproved concept rounds',
    ],
    faqs: [
      { question: 'How do clients download final vectors?', answer: 'Once final milestone payment is received, the high-res download gate unlocks automatically.' },
    ],
  },
  {
    id: 'performance-marketing-kit',
    slug: 'performance-marketing-seo',
    title: 'Performance & SEO Agency OS',
    shortTitle: 'Performance & SEO',
    tagline: 'Automated recurring monthly retainers, client analytics & 1st-of-month invoicing.',
    category: 'marketing_growth',
    categoryLabel: 'Marketing & Advisory',
    badge: 'Retainer Schema',
    sacCode: 'SAC 998313',
    gstRate: '18% GST',
    rating: 4.94,
    reviewsCount: 96,
    downloadsCount: 1340,
    heroDescription: 'Built for growth agencies. Automatically bills clients on the 1st of every month, tracks ad budgets, and hosts client KPI summary portals.',
    accentBg: 'bg-emerald-50',
    accentBorder: 'border-emerald-200',
    accentText: 'text-emerald-900',
    accentColor: '#059669',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Growth Agency Homepage', slug: 'home', description: 'Performance case studies & dynamic ROI calculator', type: 'landing' },
      { name: 'Client Retainer Portal', slug: 'portal', description: 'Live KPI reporting & campaign task backlog', type: 'portal' },
      { name: 'Monthly Recurring Invoice', slug: 'invoice', description: 'Automated 1st-of-month GST retainer invoice', type: 'invoice' },
      { name: 'Quarterly Service Agreement', slug: 'contract', description: '3-month minimum commitment contract with auto-renew', type: 'contract' },
    ],
    schemaStructures: [
      {
        name: 'Retainer Schedules',
        badge: 'Recurring Ops',
        description: 'Manages recurring billing cycles, ad budget management fees, and renewals',
        fields: [
          { name: 'monthly_retainer_inr', type: 'Currency', required: true, description: 'Fixed base agency retainer' },
          { name: 'ad_spend_percentage', type: 'Number', required: false, description: 'Variable performance fee percentage' },
          { name: 'next_invoice_date', type: 'Date', required: true, description: 'Auto-generation trigger date' },
        ],
      },
    ],
    features: ['Recurring monthly retainer billing', 'Ad spend fee calculation', 'Quarterly auto-renewal contracts', 'Client deliverable dashboard'],
    recommendedModules: [
      { id: 'retainers', title: 'Recurring Billing', icon: 'Receipt' },
      { id: 'portals', title: 'Client Reporting', icon: 'Globe' },
      { id: 'contracts', title: 'Auto-Renew Agreements', icon: 'ShieldCheck' },
    ],
    toolsReplaced: [
      { name: 'Chargebee / Recurring Tool', monthlyCostUSD: 99 },
      { name: 'Contract Tool', monthlyCostUSD: 30 },
    ],
    workflowHighlights: [
      'Invoices sent automatically on the 1st with automated WhatsApp reminder links',
      'Client payment receipts sent automatically upon UPI confirmation',
    ],
    faqs: [
      { question: 'Does it support recurring UPI auto-debit?', answer: 'Yes, instant UPI intent and automated recurring invoice links are included.' },
    ],
  },
  {
    id: 'saas-launchpad-kit',
    slug: 'saas-product-launchpad',
    title: 'SaaS & Micro-Product Starter Kit',
    shortTitle: 'SaaS Launchpad',
    tagline: 'Product marketing, feature comparisons, pricing matrices & self-serve signup.',
    category: 'tech_dev',
    categoryLabel: 'Tech & Development',
    badge: 'Launchpad',
    sacCode: 'SAC 998315',
    gstRate: '18% GST',
    rating: 4.95,
    reviewsCount: 88,
    downloadsCount: 1120,
    heroDescription: 'Modern dark/light SaaS marketing kit. Features interactive 3-tier pricing matrix, feature comparison matrix, changelog, and API doc layout.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-300',
    accentText: 'text-zinc-950',
    accentColor: '#18181B',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'SaaS Marketing Homepage', slug: 'home', description: 'Product hero with animated UI showcase & customer proof', type: 'landing' },
      { name: 'Tiered Pricing Matrix', slug: 'pricing', description: 'Monthly/annual toggle with feature breakdown', type: 'landing' },
      { name: 'Feature Deep-Dives', slug: 'features', description: 'Modular product capability layout', type: 'landing' },
      { name: 'Public Changelog', slug: 'changelog', description: 'Versioned product update feed with RSS', type: 'landing' },
    ],
    schemaStructures: [
      {
        name: 'Product Subscriptions',
        badge: 'SaaS Engine',
        description: 'Manages user seats, tier quotas, and plan upgrades',
        fields: [
          { name: 'plan_id', type: 'Enum', required: true, description: 'Starter, Professional, Enterprise' },
          { name: 'billing_cycle', type: 'Enum', required: true, description: 'Monthly or Annual' },
          { name: 'seats_quota', type: 'Number', required: true, description: 'Active user seat count' },
        ],
      },
    ],
    features: ['3-tier annual/monthly pricing matrix', 'Interactive feature comparison table', 'Changelog timeline', 'Instant onboarding forms'],
    recommendedModules: [
      { id: 'pricing', title: 'Pricing Matrix', icon: 'Receipt' },
      { id: 'changelog', title: 'Changelog Feed', icon: 'Layers' },
    ],
    toolsReplaced: [
      { name: 'Webflow Workspace', monthlyCostUSD: 49 },
      { name: 'Feature Base Changelog', monthlyCostUSD: 29 },
    ],
    workflowHighlights: [
      'Instant static compilation with sub-50ms page load speeds',
      'Built-in analytics event tracking for signups and pricing toggles',
    ],
    faqs: [
      { question: 'Can I customize the pricing plans?', answer: 'Yes, change plan names, prices, and features with simple JSON edits.' },
    ],
  },
  {
    id: 'photo-video-kit',
    slug: 'photo-video-production',
    title: 'Commercial Photography & Cinema Studio',
    shortTitle: 'Photo & Video Studio',
    tagline: 'Shoot booking forms, call sheet vaults, client proofing & model releases.',
    category: 'creative_design',
    categoryLabel: 'Creative & Design',
    badge: 'Media Kit',
    sacCode: 'SAC 998381',
    gstRate: '18% GST',
    rating: 4.97,
    reviewsCount: 104,
    downloadsCount: 1410,
    heroDescription: 'Turnkey operating kit for photo and film studios. Manage shoot dates, client gallery proofing, SHA-256 model releases, and milestone day-rate billing.',
    accentBg: 'bg-rose-50',
    accentBorder: 'border-rose-200',
    accentText: 'text-rose-900',
    accentColor: '#E11D48',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Production House Showreel', slug: 'home', description: 'Full-bleed video hero with category portfolio filters', type: 'landing' },
      { name: 'Client Proofing Vault', slug: 'proofing', description: 'Grid photo selection portal with client approvals', type: 'portal' },
      { name: 'Shoot Day Rate Agreement', slug: 'contract', description: 'Commercial shoot contract with copyright licensing', type: 'contract' },
      { name: 'Production Tax Invoice', slug: 'invoice', description: 'Line-item shoot gear, crew, and studio billing', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Production Shoots',
        badge: 'Shoot Ops',
        description: 'Coordinates shoot dates, call sheets, crew lists, and client deliverables',
        fields: [
          { name: 'shoot_date', type: 'DateTime', required: true, description: 'Production day schedule' },
          { name: 'location_address', type: 'String', required: true, description: 'Studio or on-location venue' },
          { name: 'crew_count', type: 'Number', required: true, description: 'Assigned assistants and operators' },
        ],
      },
    ],
    features: ['Client photo proofing portal', 'Day-rate and crew line-item invoices', 'Model release digital agreements', 'Call sheet distribution'],
    recommendedModules: [
      { id: 'gallery', title: 'Proofing Gallery', icon: 'Camera' },
      { id: 'contracts', title: 'Model Releases', icon: 'ShieldCheck' },
      { id: 'invoices', title: 'Shoot Billing', icon: 'Receipt' },
    ],
    toolsReplaced: [
      { name: 'Pixieset / Pic-Time', monthlyCostUSD: 30 },
      { name: 'Studio Ninja', monthlyCostUSD: 29 },
    ],
    workflowHighlights: [
      'Clients select favorite proof photos and trigger automated editing workflow',
    ],
    faqs: [
      { question: 'Is photo proofing client-side secure?', answer: 'Yes, proofing galleries feature PIN protection and download authorization gates.' },
    ],
  },
  {
    id: 'law-firm-kit',
    slug: 'law-firm-legal-practice',
    title: 'Corporate Law & Legal Practice Schema',
    shortTitle: 'Law & Legal Practice',
    tagline: 'Client intake briefs, retainer agreements, court milestone billing & case vaults.',
    category: 'legal_finance',
    categoryLabel: 'Legal & Finance',
    badge: 'Legal Practice',
    sacCode: 'SAC 998211',
    gstRate: '18% GST',
    rating: 4.99,
    reviewsCount: 78,
    downloadsCount: 890,
    heroDescription: 'Operating suite for advocates, corporate legal advisors, and boutique law firms. Complete with matter intake forms, fee structures, and SHA-256 contracts.',
    accentBg: 'bg-slate-100',
    accentBorder: 'border-slate-300',
    accentText: 'text-slate-900',
    accentColor: '#334155',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Legal Practice Homepage', slug: 'home', description: 'Practice area directory with confidential inquiry form', type: 'landing' },
      { name: 'Client Matter Vault', slug: 'matter-vault', description: 'Confidential client document room with access logs', type: 'portal' },
      { name: 'Legal Retainer Engagement', slug: 'contract', description: 'Bar Council compliant legal engagement letter', type: 'contract' },
      { name: 'Professional Legal Fee Invoice', slug: 'invoice', description: 'SAC 9982 legal advisory invoice with GST split', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Legal Matters',
        badge: 'Case Registry',
        description: 'Organizes case files, court hearing dates, opposing counsel, and briefs',
        fields: [
          { name: 'matter_ref_no', type: 'String', required: true, description: 'Unique case identifier' },
          { name: 'practice_area', type: 'Enum', required: true, description: 'Corporate, Tax, IP, Litigation' },
          { name: 'court_forum', type: 'String', required: false, description: 'High Court, NCLT, Supreme Court' },
        ],
      },
    ],
    features: ['Confidential client intake forms', 'SHA-256 engagement letters', 'Hourly & fixed-fee legal invoices', 'Matter document repository'],
    recommendedModules: [
      { id: 'vault', title: 'Case Vault', icon: 'FolderKanban' },
      { id: 'contracts', title: 'Engagement Agreements', icon: 'ShieldCheck' },
    ],
    toolsReplaced: [
      { name: 'Clio Legal Software', monthlyCostUSD: 89 },
      { name: 'DocuSign', monthlyCostUSD: 40 },
    ],
    workflowHighlights: [
      'Client identity verification and conflict check intake form',
    ],
    faqs: [
      { question: 'Is digital e-signing valid under Indian law?', answer: 'Yes, all contracts comply with Section 10A of the Information Technology Act 2000.' },
    ],
  },
  {
    id: 'ca-tax-firm-kit',
    slug: 'chartered-accountant-tax',
    title: 'CA & Tax Advisory Practice Kit',
    shortTitle: 'CA & Tax Practice',
    tagline: 'Audit document requests, GST filing checklists, digital signing & fee notes.',
    category: 'legal_finance',
    categoryLabel: 'Legal & Finance',
    badge: 'CA Practice',
    sacCode: 'SAC 998221',
    gstRate: '18% GST',
    rating: 4.97,
    reviewsCount: 112,
    downloadsCount: 1680,
    heroDescription: 'Tailored for Chartered Accountants and Tax Consultants. Streamline annual audit document collections, GST compliance workflows, and professional debit notes.',
    accentBg: 'bg-emerald-50',
    accentBorder: 'border-emerald-200',
    accentText: 'text-emerald-900',
    accentColor: '#059669',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'CA Practice Homepage', slug: 'home', description: 'Tax filing services, statutory audit, and direct booking', type: 'landing' },
      { name: 'Tax Document Upload Room', slug: 'portal', description: 'Encrypted client financial statement dropzone', type: 'portal' },
      { name: 'Annual Retainer Fee Note', slug: 'invoice', description: 'SAC 9982 accounting fee invoice with GST breakups', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Statutory Filings',
        badge: 'Tax Engine',
        description: 'Tracks GSTR-1, GSTR-3B, TDS returns, and corporate audit deadlines',
        fields: [
          { name: 'client_gstin', type: 'String', required: true, description: '15-digit GST identification number' },
          { name: 'filing_period', type: 'String', required: true, description: 'Fiscal quarter / month' },
          { name: 'acknowledgement_no', type: 'String', required: false, description: 'Filed ARN or acknowledgement' },
        ],
      },
    ],
    features: ['Encrypted tax document dropzone', 'Recurring monthly compliance fee notes', 'Statutory audit checklist generator', 'GSTIN verification'],
    recommendedModules: [
      { id: 'invoices', title: 'CA Fee Notes', icon: 'Receipt' },
      { id: 'vault', title: 'Audit Dropzone', icon: 'FolderKanban' },
    ],
    toolsReplaced: [
      { name: 'Accounting Invoicing Tools', monthlyCostUSD: 45 },
      { name: 'Secure File Portal', monthlyCostUSD: 35 },
    ],
    workflowHighlights: [
      'Clients receive automated checklist for Form 26AS, bank statements, and invoices',
    ],
    faqs: [
      { question: 'Can we issue debit notes and fee notes?', answer: 'Yes, full B2B debit notes and 18% GST invoices are built in.' },
    ],
  },
  {
    id: 'consulting-advisory-kit',
    slug: 'management-consulting',
    title: 'Strategic Consulting & Advisory Kit',
    shortTitle: 'Management Consulting',
    tagline: 'Client discovery diagnostics, executive proposals, NDA vaults & milestone roadmaps.',
    category: 'marketing_growth',
    categoryLabel: 'Marketing & Advisory',
    badge: 'Advisory Kit',
    sacCode: 'SAC 998311',
    gstRate: '18% GST',
    rating: 4.96,
    reviewsCount: 84,
    downloadsCount: 960,
    heroDescription: 'For executive advisors and consulting firms. Includes diagnostic lead capture funnels, multi-tiered advisory proposals, and client project management vaults.',
    accentBg: 'bg-indigo-50',
    accentBorder: 'border-indigo-200',
    accentText: 'text-indigo-900',
    accentColor: '#4F46E5',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Advisory Firm Flagship', slug: 'home', description: 'Thought leadership showcase with diagnostic assessment', type: 'landing' },
      { name: 'Executive Strategy Vault', slug: 'portal', description: 'Deliverable roadmaps, slide decks, and meeting records', type: 'portal' },
      { name: 'Mutual Non-Disclosure Agreement', slug: 'contract', description: 'Standard mutual NDA with SHA-256 e-signatures', type: 'contract' },
      { name: 'Advisory Retainer Invoice', slug: 'invoice', description: 'Monthly management consulting retainer invoice', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Advisory Engagements',
        badge: 'Engagement Ops',
        description: 'Tracks executive roadmaps, steering committee meetings, and milestones',
        fields: [
          { name: 'engagement_phase', type: 'String', required: true, description: 'Discovery, Analysis, Implementation' },
          { name: 'advisory_hours_used', type: 'Number', required: true, description: 'Monthly consumed partner hours' },
        ],
      },
    ],
    features: ['Diagnostic lead capture quizzes', 'Mutual NDA digital contracts', 'Executive client strategy portals', 'Retainer billing'],
    recommendedModules: [
      { id: 'contracts', title: 'NDA & MSA Vault', icon: 'ShieldCheck' },
      { id: 'portals', title: 'Strategy Vault', icon: 'Globe' },
    ],
    toolsReplaced: [
      { name: 'Typeform Pro', monthlyCostUSD: 50 },
      { name: 'Qwilr Proposals', monthlyCostUSD: 49 },
    ],
    workflowHighlights: [
      'Lead completes diagnostic assessment -> Instant customized proposal generated',
    ],
    faqs: [
      { question: 'Can I customize the diagnostic questions?', answer: 'Yes, the assessment questions and logic can be modified anytime.' },
    ],
  },
  {
    id: 'architecture-interior-kit',
    slug: 'architecture-interior-design',
    title: 'Architect & Interior Design Showcase',
    shortTitle: 'Architecture & Interiors',
    tagline: '3D render galleries, material moodboards, site inspection reports & fee schedules.',
    category: 'creative_design',
    categoryLabel: 'Creative & Design',
    badge: 'Design Kit',
    sacCode: 'SAC 998321',
    gstRate: '18% GST',
    rating: 4.98,
    reviewsCount: 92,
    downloadsCount: 1240,
    heroDescription: 'Purpose-built for architects and interior design practices. Features full-bleed project galleries, material selection approval portals, and stage-gate invoices.',
    accentBg: 'bg-amber-50',
    accentBorder: 'border-amber-200',
    accentText: 'text-amber-900',
    accentColor: '#D97706',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Architectural Portfolio', slug: 'home', description: 'Editorial layout with residential & commercial filters', type: 'landing' },
      { name: 'Material & Spec Approval Vault', slug: 'portal', description: 'Client finishes, fixture approvals, and 3D renders', type: 'portal' },
      { name: 'Architectural Service Agreement', slug: 'contract', description: 'Stage-wise design contract with revision terms', type: 'contract' },
      { name: 'Phase Gate Tax Invoice', slug: 'invoice', description: 'Concept, GFC, and Site Execution milestone billing', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Project Phases',
        badge: 'Arch Ops',
        description: 'Tracks Concept Design, Tender Drawings, Site Visits, and Handover',
        fields: [
          { name: 'project_area_sqft', type: 'Number', required: true, description: 'Super built-up area in square feet' },
          { name: 'current_stage', type: 'Enum', required: true, description: 'Concept, Schematic, GFC, Handover' },
        ],
      },
    ],
    features: ['Material spec client approvals', 'Stage-gate architectural fee billing', 'Site progress inspection logs', '3D render vaults'],
    recommendedModules: [
      { id: 'gallery', title: 'Render Gallery', icon: 'FolderKanban' },
      { id: 'invoices', title: 'Stage-Gate Invoices', icon: 'Receipt' },
    ],
    toolsReplaced: [
      { name: 'Houzz Pro', monthlyCostUSD: 85 },
      { name: 'PandaDoc', monthlyCostUSD: 35 },
    ],
    workflowHighlights: [
      'Clients approve material swatches and fixtures with 1-click digital signoffs',
    ],
    faqs: [
      { question: 'Does it support sq.ft rate calculation?', answer: 'Yes, billing can be calculated by fixed price, stage gates, or per-sq.ft rates.' },
    ],
  },
  {
    id: 'real-estate-deck-kit',
    slug: 'real-estate-property-deck',
    title: 'Luxury Real Estate & Brokerage Hub',
    shortTitle: 'Real Estate Hub',
    tagline: 'Property listings, digital brochures, site visit scheduler & broker commission notes.',
    category: 'services_lifestyle',
    categoryLabel: 'Services & Lifestyle',
    badge: 'Property Kit',
    sacCode: 'SAC 997211',
    gstRate: '18% GST',
    rating: 4.95,
    reviewsCount: 130,
    downloadsCount: 1750,
    heroDescription: 'Complete property showcase and brokerage management kit. High-converting floor plan views, instant WhatsApp site tour booking, and digital buyer agreements.',
    accentBg: 'bg-teal-50',
    accentBorder: 'border-teal-200',
    accentText: 'text-teal-900',
    accentColor: '#0D9488',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Property Showcase Flagship', slug: 'home', description: 'Filterable luxury villa and commercial unit listings', type: 'landing' },
      { name: 'Interactive Floor Plan Deck', slug: 'listing-detail', description: 'Unit specs, pricing breakdown, and brochure download', type: 'landing' },
      { name: 'Buyer Token Agreement', slug: 'contract', description: 'Expression of Interest (EOI) digital booking form', type: 'contract' },
      { name: 'Brokerage Commission Note', slug: 'invoice', description: 'Real estate advisory invoice with 18% GST', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Property Inventory',
        badge: 'Realty Engine',
        description: 'Manages unit configurations, carpet area, pricing, and availability',
        fields: [
          { name: 'unit_type', type: 'Enum', required: true, description: '2BHK, 3BHK, 4BHK Penthouse, Commercial' },
          { name: 'carpet_area_sqft', type: 'Number', required: true, description: 'RERA carpet area' },
          { name: 'quoted_price_inr', type: 'Currency', required: true, description: 'Base property asking price' },
        ],
      },
    ],
    features: ['Filterable property directory', 'WhatsApp 1-click site visit scheduler', 'RERA compliant buyer booking forms', 'Brokerage fee invoicing'],
    recommendedModules: [
      { id: 'listings', title: 'Property Listings', icon: 'Building2' },
      { id: 'contracts', title: 'EOI Agreements', icon: 'ShieldCheck' },
    ],
    toolsReplaced: [
      { name: 'Real Estate CRM Plugin', monthlyCostUSD: 60 },
      { name: 'Booking Calendar Tool', monthlyCostUSD: 20 },
    ],
    workflowHighlights: [
      'Instant brochure download captures buyer phone number with verified OTP lead generation',
    ],
    faqs: [
      { question: 'Is it RERA compliance friendly?', answer: 'Yes, RERA registration numbers and carpet area disclosures are pre-formatted.' },
    ],
  },
  {
    id: 'ai-automation-agency-kit',
    slug: 'ai-automation-agency',
    title: 'AI Automation & Workflow Agency Schema',
    shortTitle: 'AI Automation Agency',
    tagline: 'AI workflow scopes, API consumption billing, sprint pilots & SLA agreements.',
    category: 'tech_dev',
    categoryLabel: 'Tech & Development',
    badge: 'AI Agency Kit',
    sacCode: 'SAC 998313',
    gstRate: '18% GST',
    rating: 4.97,
    reviewsCount: 86,
    downloadsCount: 1190,
    heroDescription: 'Tailored for agencies building LLM workflows, custom AI chatbots, and automated voice agents. Features pilot project agreements and monthly maintenance retainer billing.',
    accentBg: 'bg-violet-50',
    accentBorder: 'border-violet-200',
    accentText: 'text-violet-900',
    accentColor: '#8B5CF6',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'AI Agency Landing Page', slug: 'home', description: 'Live chatbot demo embedding & ROI calculator', type: 'landing' },
      { name: 'Client Workflow Portal', slug: 'portal', description: 'API uptime logs, webhook status, and prompt registries', type: 'portal' },
      { name: 'AI Pilot Scope Agreement', slug: 'contract', description: '2-week sprint pilot contract with API liability clauses', type: 'contract' },
      { name: 'Monthly AI Maintenance Invoice', slug: 'invoice', description: 'Fixed retainer + OpenAI/Anthropic API passthrough', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'AI Agent Deployments',
        badge: 'AI Registry',
        description: 'Coordinates agent webhooks, system prompts, token budgets, and SLAs',
        fields: [
          { name: 'agent_name', type: 'String', required: true, description: 'AI agent / workflow title' },
          { name: 'underlying_model', type: 'String', required: true, description: 'Gemini 1.5, Claude 3.5, GPT-4o' },
          { name: 'monthly_token_budget', type: 'Number', required: true, description: 'Allocated client API capacity' },
        ],
      },
    ],
    features: ['Embedded AI agent demo widget', 'API token budget pass-through billing', 'AI liability & SLA digital contracts', 'Webhook health dashboard'],
    recommendedModules: [
      { id: 'ai', title: 'AI Copilot', icon: 'Sparkles' },
      { id: 'invoices', title: 'Token Invoicing', icon: 'Receipt' },
    ],
    toolsReplaced: [
      { name: 'Custom Portal Dev', monthlyCostUSD: 120 },
      { name: 'Billing SaaS', monthlyCostUSD: 40 },
    ],
    workflowHighlights: [
      'Clients can test interactive AI bot directly on the landing page before booking a discovery call',
    ],
    faqs: [
      { question: 'Can I embed custom Make / n8n workflows?', answer: 'Yes, webhooks and embed endpoints can be attached seamlessly.' },
    ],
  },
  {
    id: 'event-wedding-production-kit',
    slug: 'event-wedding-production',
    title: 'Event & Wedding Production Vault',
    shortTitle: 'Event & Wedding',
    tagline: 'Vendor quote management, event itinerary decks, RSVP tracking & deposit billing.',
    category: 'services_lifestyle',
    categoryLabel: 'Services & Lifestyle',
    badge: 'Events Kit',
    sacCode: 'SAC 998596',
    gstRate: '18% GST',
    rating: 4.96,
    reviewsCount: 102,
    downloadsCount: 1430,
    heroDescription: 'Operating suite for luxury wedding planners, event curators, and experiential production agencies. Features vendor budgets, itineraries, and digital contracts.',
    accentBg: 'bg-rose-50',
    accentBorder: 'border-rose-200',
    accentText: 'text-rose-900',
    accentColor: '#F43F5E',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Event Production Flagship', slug: 'home', description: 'Atmospheric gallery with event type filters', type: 'landing' },
      { name: 'Client Wedding & Event Vault', slug: 'portal', description: 'Timeline schedules, moodboards, and budget tracking', type: 'portal' },
      { name: 'Production Engagement Contract', slug: 'contract', description: 'Force majeure & cancellation clause agreements', type: 'contract' },
      { name: 'Staged Milestone Invoices', slug: 'invoice', description: 'Venue deposit, vendor advance, and final settlement', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Event Itineraries',
        badge: 'Event Ops',
        description: 'Manages multi-day event timelines, guest counts, and vendor allocations',
        fields: [
          { name: 'event_date_start', type: 'DateTime', required: true, description: 'Event kickoff date and time' },
          { name: 'expected_guest_count', type: 'Number', required: true, description: 'Total invited attendees' },
          { name: 'total_budget_inr', type: 'Currency', required: true, description: 'Overall production budget' },
        ],
      },
    ],
    features: ['Multi-day event timeline vaults', 'Vendor advance invoice management', 'Guest RSVP collection forms', 'Cancellation policy contracts'],
    recommendedModules: [
      { id: 'vault', title: 'Event Vault', icon: 'FolderKanban' },
      { id: 'invoices', title: 'Vendor Settlement', icon: 'Receipt' },
    ],
    toolsReplaced: [
      { name: 'Aisle Planner', monthlyCostUSD: 49 },
      { name: 'Form Builder Tool', monthlyCostUSD: 25 },
    ],
    workflowHighlights: [
      'Couples receive interactive mobile-friendly itinerary vault with live weather and vendor updates',
    ],
    faqs: [
      { question: 'Can vendors access their specific section?', answer: 'Yes, selective client and vendor portal links can be shared.' },
    ],
  },
  {
    id: 'fitness-wellness-kit',
    slug: 'fitness-wellness-coaching',
    title: 'Fitness, Gym & Wellness Studio Hub',
    shortTitle: 'Fitness & Wellness',
    tagline: 'Class booking schedules, client health waivers, recurring memberships & trainer vaults.',
    category: 'services_lifestyle',
    categoryLabel: 'Services & Lifestyle',
    badge: 'Wellness Kit',
    sacCode: 'SAC 999311',
    gstRate: '18% GST',
    rating: 4.93,
    reviewsCount: 76,
    downloadsCount: 980,
    heroDescription: 'Built for high-end boutique gyms, yoga studios, and personal coaches. Includes class timetables, automated recurring membership passes, and digital liability waivers.',
    accentBg: 'bg-emerald-50',
    accentBorder: 'border-emerald-200',
    accentText: 'text-emerald-900',
    accentColor: '#10B981',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Wellness Studio Homepage', slug: 'home', description: 'Interactive timetable & trainer bios with 1-click booking', type: 'landing' },
      { name: 'Member Progress Vault', slug: 'portal', description: 'Workout plans, nutrition guides, and attendance pass', type: 'portal' },
      { name: 'Physical Activity Readiness Waiver', slug: 'contract', description: 'PAR-Q digital health waiver with e-signature', type: 'contract' },
      { name: 'Monthly Membership Tax Invoice', slug: 'invoice', description: 'Automated recurring gym membership invoice', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Member Passes',
        badge: 'Fitness Engine',
        description: 'Tracks membership tier, remaining personal training credits, and renewal',
        fields: [
          { name: 'membership_type', type: 'Enum', required: true, description: 'Monthly Unlimited, 10-Class Pack, Annual VIP' },
          { name: 'pt_credits_remaining', type: 'Number', required: true, description: 'Available 1-on-1 coaching sessions' },
        ],
      },
    ],
    features: ['Weekly class timetable scheduler', 'Digital PAR-Q health waivers', 'Recurring monthly membership billing', 'Trainer bio showcases'],
    recommendedModules: [
      { id: 'schedules', title: 'Class Timetables', icon: 'Calendar' },
      { id: 'invoices', title: 'Membership Billing', icon: 'Receipt' },
    ],
    toolsReplaced: [
      { name: 'Mindbody', monthlyCostUSD: 139 },
      { name: 'Waiver Electronic Tool', monthlyCostUSD: 19 },
    ],
    workflowHighlights: [
      'New members sign liability waiver on phone before first class booking',
    ],
    faqs: [
      { question: 'Does it support pack-based credit deductions?', answer: 'Yes, 10-class passes and recurring monthly subscriptions are fully supported.' },
    ],
  },
  {
    id: 'education-creator-kit',
    slug: 'education-creator-academy',
    title: 'Cohort Course & Creator Academy',
    shortTitle: 'Creator Academy',
    tagline: 'Curriculum syllabus decks, student portal vaults, cohort billing & certificates.',
    category: 'services_lifestyle',
    categoryLabel: 'Services & Lifestyle',
    badge: 'Education Kit',
    sacCode: 'SAC 999293',
    gstRate: '18% GST',
    rating: 4.97,
    reviewsCount: 114,
    downloadsCount: 1560,
    heroDescription: 'For educators, coaches, and creators running live cohort courses. Features interactive syllabus breakdowns, student resource rooms, and installment checkout.',
    accentBg: 'bg-sky-50',
    accentBorder: 'border-sky-200',
    accentText: 'text-sky-900',
    accentColor: '#0284C7',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Cohort Academy Flagship', slug: 'home', description: 'Curriculum breakdown, alumni reviews, and apply button', type: 'landing' },
      { name: 'Student Resource Room', slug: 'portal', description: 'Session recordings, templates, and homework submissions', type: 'portal' },
      { name: 'Course Enrollment Agreement', slug: 'contract', description: 'Academic integrity & refund policy agreement', type: 'contract' },
      { name: 'Tuition Fee Tax Invoice', slug: 'invoice', description: 'B2B/B2C education invoice with GST tax invoice', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Cohort Enrollments',
        badge: 'Academy Engine',
        description: 'Manages student batches, attendance signoffs, and certificate issuance',
        fields: [
          { name: 'cohort_batch_id', type: 'String', required: true, description: 'e.g. Cohort Spring 2026' },
          { name: 'enrollment_status', type: 'Enum', required: true, description: 'Applied, Enrolled, Graduated' },
        ],
      },
    ],
    features: ['Interactive chaptered syllabus', 'Student resource vault with video embeds', 'Installment fee invoice splits', 'Digital course completion certificates'],
    recommendedModules: [
      { id: 'syllabus', title: 'Curriculum View', icon: 'BookOpen' },
      { id: 'invoices', title: 'Tuition Invoices', icon: 'Receipt' },
    ],
    toolsReplaced: [
      { name: 'Teachable / Kajabi', monthlyCostUSD: 119 },
      { name: 'DocuSign', monthlyCostUSD: 40 },
    ],
    workflowHighlights: [
      'Students submit application -> Approved -> Instant payment link and student portal access generated',
    ],
    faqs: [
      { question: 'Can students pay in 2 installments?', answer: 'Yes, split milestone tuition invoices are supported natively.' },
    ],
  },
  {
    id: 'healthcare-clinic-kit',
    slug: 'healthcare-clinic-practice',
    title: 'Private Medical & Dental Clinic OS',
    shortTitle: 'Clinic & Healthcare',
    tagline: 'Patient appointment triage, treatment estimate notes, consent forms & billing.',
    category: 'services_lifestyle',
    categoryLabel: 'Services & Lifestyle',
    badge: 'Clinical Kit',
    sacCode: 'SAC 999312',
    gstRate: 'Exempt / 18%',
    rating: 4.96,
    reviewsCount: 68,
    downloadsCount: 790,
    heroDescription: 'Tailored for private healthcare practices, dental studios, and wellness doctors. Manage appointment triage, treatment estimates, and patient consent forms.',
    accentBg: 'bg-cyan-50',
    accentBorder: 'border-cyan-200',
    accentText: 'text-cyan-900',
    accentColor: '#0891B2',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'Clinical Practice Homepage', slug: 'home', description: 'Doctor profiles, specialty treatments & instant appointment triage', type: 'landing' },
      { name: 'Patient Treatment Estimate', slug: 'invoice', description: 'Itemized procedure estimate note with payment options', type: 'invoice' },
      { name: 'Informed Procedure Consent Form', slug: 'contract', description: 'Digital patient medical consent form with SHA-256 e-sign', type: 'contract' },
    ],
    schemaStructures: [
      {
        name: 'Patient Consultations',
        badge: 'Clinical Ops',
        description: 'Coordinates appointment slots, doctor assignment, and treatment records',
        fields: [
          { name: 'patient_mrn', type: 'String', required: true, description: 'Medical record number' },
          { name: 'attending_specialist', type: 'String', required: true, description: 'Doctor name' },
        ],
      },
    ],
    features: ['Specialist treatment estimate breakdowns', 'Informed consent digital e-signatures', 'Mobile appointment triage forms', 'Bilingual patient instructions'],
    recommendedModules: [
      { id: 'consent', title: 'Consent Forms', icon: 'ShieldCheck' },
      { id: 'invoices', title: 'Treatment Billing', icon: 'Receipt' },
    ],
    toolsReplaced: [
      { name: 'Practo Practice', monthlyCostUSD: 65 },
      { name: 'Form Software', monthlyCostUSD: 29 },
    ],
    workflowHighlights: [
      'Patients sign medical consent form on iPad / mobile prior to clinical procedure',
    ],
    faqs: [
      { question: 'Is patient data stored securely?', answer: 'Yes, all sensitive records utilize client-side encryption and secure vaults.' },
    ],
  },
  {
    id: 'ecommerce-dtc-kit',
    slug: 'ecommerce-dtc-brand',
    title: 'DTC Brand & E-Commerce Flagship Kit',
    shortTitle: 'DTC E-Commerce',
    tagline: 'Direct product showcases, customer reviews, B2B wholesale orders & GST invoices.',
    category: 'tech_dev',
    categoryLabel: 'Tech & Development',
    badge: 'DTC Kit',
    sacCode: 'HSN 9983 / Goods',
    gstRate: '18% / Slab',
    rating: 4.95,
    reviewsCount: 122,
    downloadsCount: 1690,
    heroDescription: 'Modern luxury DTC brand showcase. Features high-converting product hero, wholesale B2B bulk inquiry builder, customer social proof, and tax invoices.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-300',
    accentText: 'text-zinc-950',
    accentColor: '#18181B',
    coverImage: '/images/cora_hero_landscape.jpg',
    includedPages: [
      { name: 'DTC Brand Flagship', slug: 'home', description: 'Luxury visual product hero with lifestyle storytelling', type: 'landing' },
      { name: 'Product Deep-Dive & Specs', slug: 'product-detail', description: 'Material breakdown, size guide, and customer photo reviews', type: 'landing' },
      { name: 'B2B Wholesale Invoicing Deck', slug: 'invoice', description: 'Bulk order quotation with GST breakups and freight terms', type: 'invoice' },
    ],
    schemaStructures: [
      {
        name: 'Product Inventory & SKUs',
        badge: 'Commerce Engine',
        description: 'Manages product variants, stock alerts, wholesale pricing, and barcodes',
        fields: [
          { name: 'sku_code', type: 'String', required: true, description: 'Unique product stock keeping unit' },
          { name: 'wholesale_moq', type: 'Number', required: true, description: 'Minimum order quantity for B2B' },
        ],
      },
    ],
    features: ['High-resolution product galleries', 'Wholesale B2B quotation builder', 'Customer review photo submission', 'Instant UPI checkout'],
    recommendedModules: [
      { id: 'products', title: 'Product Catalog', icon: 'FolderKanban' },
      { id: 'invoices', title: 'Wholesale Billing', icon: 'Receipt' },
    ],
    toolsReplaced: [
      { name: 'Shopify Plus Addons', monthlyCostUSD: 80 },
      { name: 'B2B Wholesale Plugin', monthlyCostUSD: 49 },
    ],
    workflowHighlights: [
      'B2B wholesale buyers calculate bulk order volume pricing and receive instant GST proforma invoices',
    ],
    faqs: [
      { question: 'Can I connect my existing payment gateway?', answer: 'Yes, Stripe, Razorpay, and direct UPI QR links are pre-configured.' },
    ],
  },
];
