import { Calculator, Sparkles, Code, Scale, Receipt, QrCode, FileText, Briefcase, Zap, ShieldCheck } from 'lucide-react';

export interface ToolItem {
  id: string;
  slug: string;
  title: string;
  shortTitle: string;
  category: 'finance_tax' | 'retainer_agency' | 'ai_copy' | 'contracts_legal' | 'developer_embed' | 'payments_upi';
  categoryLabel: string;
  badge: string;
  badgeColor: string;
  tagline: string;
  description: string;
  iconName: string;
  highlights: string[];
  runs: string;
  inputPlaceholder: string;
}

export interface ToolCategoryBlock {
  id: string;
  title: string;
  subtitle: string;
  count: string;
  iconName: string;
  bgColor: string;
  accentText: string;
  featuredToolName: string;
  featuredToolSlug: string;
  featuredToolBadgeColor: string;
}

export const TOOL_CATEGORY_BLOCKS: ToolCategoryBlock[] = [
  {
    id: 'finance_tax',
    title: 'Tax & GST',
    subtitle: 'Solve Your GST Problems',
    count: '5+ tools',
    iconName: 'Calculator',
    bgColor: 'bg-[#6366F1]',
    accentText: 'text-[#6366F1]',
    featuredToolName: 'GST Calculator',
    featuredToolSlug: 'gst-calculator',
    featuredToolBadgeColor: 'bg-indigo-50 text-indigo-700',
  },
  {
    id: 'retainer_agency',
    title: 'Retainer Math',
    subtitle: 'Solve Your Scope Problems',
    count: '4+ tools',
    iconName: 'Receipt',
    bgColor: 'bg-[#F97316]',
    accentText: 'text-[#F97316]',
    featuredToolName: 'Retainer Math',
    featuredToolSlug: 'retainer-calculator',
    featuredToolBadgeColor: 'bg-orange-50 text-orange-700',
  },
  {
    id: 'ai_copy',
    title: 'AI Studio',
    subtitle: 'Solve Your Copy Problems',
    count: '6+ tools',
    iconName: 'Sparkles',
    bgColor: 'bg-[#E11D48]',
    accentText: 'text-[#E11D48]',
    featuredToolName: 'Listing AI',
    featuredToolSlug: 'listing-ai',
    featuredToolBadgeColor: 'bg-rose-50 text-rose-700',
  },
  {
    id: 'contracts_legal',
    title: 'Legal Contracts',
    subtitle: 'Solve Your Contract Problems',
    count: '8+ tools',
    iconName: 'Scale',
    bgColor: 'bg-[#2563EB]',
    accentText: 'text-[#2563EB]',
    featuredToolName: 'IT Act E-Sign',
    featuredToolSlug: 'contract-builder',
    featuredToolBadgeColor: 'bg-blue-50 text-blue-700',
  },
  {
    id: 'developer_embed',
    title: 'Embed Widgets',
    subtitle: 'Solve Your Lead Problems',
    count: '5+ tools',
    iconName: 'Code',
    bgColor: 'bg-[#0D9488]',
    accentText: 'text-[#0D9488]',
    featuredToolName: 'Embed Builder',
    featuredToolSlug: 'embed-builder',
    featuredToolBadgeColor: 'bg-teal-50 text-teal-700',
  },
  {
    id: 'payments_upi',
    title: 'UPI & Invoicing',
    subtitle: 'Solve Your Payment Problems',
    count: '4+ tools',
    iconName: 'QrCode',
    bgColor: 'bg-[#7C3AED]',
    accentText: 'text-[#7C3AED]',
    featuredToolName: 'Dynamic UPI QR',
    featuredToolSlug: 'upi-qr-generator',
    featuredToolBadgeColor: 'bg-purple-50 text-purple-700',
  },
];

export const TOOL_CATEGORIES = [
  { id: 'all', label: 'All Tools', count: 6 },
  { id: 'finance_tax', label: 'Finance & Tax', count: 2 },
  { id: 'ai_copy', label: 'AI & Copywriting', count: 1 },
  { id: 'contracts_legal', label: 'Contracts & Legal', count: 1 },
  { id: 'developer_embed', label: 'Developer & Embeds', count: 2 },
];

export const TOOLS_DATA: ToolItem[] = [
  {
    id: 'gst-calculator',
    slug: 'gst-calculator',
    title: 'Indian GST & B2B Tax Calculator',
    shortTitle: 'GST Tax Calculator',
    category: 'finance_tax',
    categoryLabel: 'Finance & Tax',
    badge: 'Popular in India',
    badgeColor: 'bg-emerald-50 text-emerald-700 border border-emerald-200/80',
    tagline: 'Instant 18%, 12%, 5%, 28% tax breakdown with CGST/SGST vs IGST segregation.',
    description: 'Calculate GST inclusive and exclusive pricing, split intra-state vs inter-state tax liabilities, and generate compliant breakdown summaries for your client proposals.',
    iconName: 'Calculator',
    highlights: ['18% SAC 9983 Support', 'CGST & SGST 50/50 Split', 'IGST Inter-State Toggle', '1-Click Breakdown Copy'],
    runs: 'Zero Login Required',
    inputPlaceholder: '₹50,000',
  },
  {
    id: 'retainer-calculator',
    slug: 'retainer-calculator',
    title: 'Service Retainer & Scope Buffer Calculator',
    shortTitle: 'Retainer Math Calculator',
    category: 'finance_tax',
    categoryLabel: 'Finance & Tax',
    badge: 'Agency Tool',
    badgeColor: 'bg-blue-50 text-blue-700 border border-blue-200/80',
    tagline: 'Calculate optimal monthly agency retainer tiers and billable hourly baseline rates.',
    description: 'Input your revenue targets, billable client capacity, and team overheads to calculate sustainable monthly retainer packages with automatic 20% scope creep buffers.',
    iconName: 'Receipt',
    highlights: ['Hourly to Retainer Conversion', '20% Scope Creep Buffer', 'Capacity Utilization Math', 'Client Tier Allocation'],
    runs: 'Zero Login Required',
    inputPlaceholder: '₹3,00,000 / mo',
  },
  {
    id: 'listing-ai',
    slug: 'listing-ai',
    title: 'Real Estate & Studio Listing AI Generator',
    shortTitle: 'Listing AI Generator',
    category: 'ai_copy',
    categoryLabel: 'AI & Copywriting',
    badge: 'AI Powered',
    badgeColor: 'bg-purple-50 text-purple-700 border border-purple-200/80',
    tagline: 'Turn raw property or shoot details into high-converting marketing copy and client briefs.',
    description: 'Leverage domain-trained AI prompts to generate luxury real estate brochures, Instagram captions, commercial shoot briefs, and client welcome decks in seconds.',
    iconName: 'Sparkles',
    highlights: ['3 Output Formats', 'Commercial & Residential Modes', 'Tone Tuning (Luxury / Minimal)', 'Multi-Platform Export'],
    runs: 'Zero Login Required',
    inputPlaceholder: '3BHK Sea-Facing Penthouse, Bandra West...',
  },
  {
    id: 'contract-builder',
    slug: 'contract-builder',
    title: 'Indian IT Act 2000 Contract Clause Builder',
    shortTitle: 'Contract Clause Builder',
    category: 'contracts_legal',
    categoryLabel: 'Contracts & Legal',
    badge: 'IT Act 2000 Compliant',
    badgeColor: 'bg-slate-100 text-slate-800 border border-slate-200/80',
    tagline: 'Generate legally binding contract clauses, milestone escrow terms, and NDAs.',
    description: 'Build customized Indian legal agreements with Section 10A digital signature recognition, SHA-256 tamper-evident integrity clauses, and 18% GST tax provisions.',
    iconName: 'Scale',
    highlights: ['Section 10A Electronic Signatures', 'Scope Lock & Milestone Escrow', 'IP & Copyright Assignment', 'Mutual Non-Disclosure Terms'],
    runs: 'Zero Login Required',
    inputPlaceholder: 'Client: Acme Corp, Retainer: ₹1,50,000...',
  },
  {
    id: 'embed-builder',
    slug: 'embed-builder',
    title: 'Autonomous AI Copilot & Embed Builder',
    shortTitle: 'Embed Widget Builder',
    category: 'developer_embed',
    categoryLabel: 'Developer & Embeds',
    badge: 'Developer Tool',
    badgeColor: 'bg-amber-50 text-amber-700 border border-amber-200/80',
    tagline: 'Generate embeddable AI booking widgets for Framer, Webflow, WordPress & Shopify.',
    description: 'Customize zero-code iframe embeds to capture leads, book call slots, and calculate quotes directly on your marketing site with auto-sync to your Cora workspace.',
    iconName: 'Code',
    highlights: ['Zero-Code iFrame Snippet', 'Framer & Webflow Ready', 'Light / Dark Mode Customization', 'Direct Webhook Integration'],
    runs: 'Zero Login Required',
    inputPlaceholder: '<iframe src="https://heycora.in/embed/..." />',
  },
  {
    id: 'upi-qr-generator',
    slug: 'upi-qr-generator',
    title: 'Dynamic UPI QR & Payment Intent Generator',
    shortTitle: 'UPI QR Generator',
    category: 'developer_embed',
    categoryLabel: 'Developer & Embeds',
    badge: 'UPI 2.0 Ready',
    badgeColor: 'bg-teal-50 text-teal-700 border border-teal-200/80',
    tagline: 'Generate instant UPI QR codes with custom invoice amount, payee VPA & transaction note.',
    description: 'Create zero-fee dynamic UPI payment links and printable QR codes for Indian bank settlements. Compatible with Google Pay, PhonePe, Paytm, and BHIM.',
    iconName: 'QrCode',
    highlights: ['Zero Payment Gateway Fees (0%)', 'Exact Invoice Amount Embedding', 'Instant Client-Side QR Render', 'PNG & SVG Download'],
    runs: 'Zero Login Required',
    inputPlaceholder: 'business@okhdfcbank',
  },
];
