export type AssetFormat = 'native_page' | 'section_prompts' | 'elementor_kit' | 'lovable_project';

export interface TemplateSectionItem {
  id: string;
  name: string;
  badge: string;
  description: string;
  promptText: string;
  tailwindSnippet?: string;
  elementorBlueprint?: string;
}

export interface TemplateItem {
  id: string;
  slug: string;
  title: string;
  shortTitle: string;
  tagline: string;
  category: 'landing_pages' | 'section_prompts' | 'elementor_kits' | 'lovable_projects';
  categoryLabel: string;
  assetFormat: AssetFormat;
  formatBadge: string;
  badge: string;
  rating: number;
  reviewsCount: number;
  downloadsCount: number;
  heroDescription: string;
  accentBg: string;
  accentBorder: string;
  accentText: string;
  accentColor: string;
  coverImage: string;
  compatibleBuilders: string[];
  sectionsIncluded: TemplateSectionItem[];
  features: string[];
  toolsReplaced: { name: string; monthlyCostUSD: number }[];
  frameworkStack: string[];
  quickCopyPrompt: string;
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
  { id: 'all', label: 'All Frontend Assets', shortLabel: 'All Assets', badge: '16+ ASSETS', count: 16 },
  { id: 'landing_pages', label: 'Native Landing Pages', shortLabel: 'Landing Pages', badge: 'NATIVE TAILWIND', count: 4 },
  { id: 'section_prompts', label: 'AI Section Prompts', shortLabel: 'AI Prompts', badge: 'CLAUDE & CURSOR', count: 4 },
  { id: 'elementor_kits', label: 'Elementor Template Kits', shortLabel: 'Elementor Kits', badge: 'ELEMENTOR JSON', count: 4 },
  { id: 'lovable_projects', label: 'Lovable & React Projects', shortLabel: 'Lovable Projects', badge: 'LOVABLE & V0', count: 4 },
];

export const TEMPLATES_DATA: TemplateItem[] = [
  // ── 1. NATIVE LANDING PAGES ──
  {
    id: 'agency-flagship-landing',
    slug: 'web-development-agency',
    title: 'Modern Digital Agency Flagship Landing Page',
    shortTitle: 'Agency Flagship',
    tagline: 'High-converting interactive hero, dynamic pricing matrix, case study bento & slide-up booking sheet.',
    category: 'landing_pages',
    categoryLabel: 'Native Landing Pages',
    assetFormat: 'native_page',
    formatBadge: 'Native Tailwind / Next.js',
    badge: 'Popular Page',
    rating: 4.96,
    reviewsCount: 142,
    downloadsCount: 2840,
    heroDescription: 'Production-ready monochromatic landing page built with pure Tailwind CSS and Next.js 15. Features sub-50ms paint times, accessible ARIA focus rings, responsive mobile sheets, and zero bloated JavaScript.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-300',
    accentText: 'text-zinc-950',
    accentColor: '#18181B',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Tailwind CSS', 'Next.js 15', 'React', 'Cursor AI', 'Claude 3.5', 'v0.dev'],
    frameworkStack: ['Tailwind CSS v4', 'React 19', 'Framer Motion', 'Lucide Icons'],
    quickCopyPrompt: 'Create a monochromatic modern agency landing page with an atmospheric hero, 3-column service bento grid, interactive 3-tier pricing matrix, client testimonial ticker, and a mobile bottom slide-up lead capture sheet.',
    sectionsIncluded: [
      {
        id: 'hero-section',
        name: 'Atmospheric Hero & Value Prop',
        badge: 'Hero Section',
        description: 'Clean typography header with dual action buttons and live client badge ribbon.',
        promptText: `Create a clean, monochromatic SaaS/Agency hero section using Tailwind CSS.
- Top status pill with subtle border and sparkling icon.
- Headline: 4xl to 7xl font-display tracking tight with bold primary title and muted secondary line.
- Subtitle: text-zinc-600 max-w-2xl mx-auto leading-relaxed.
- CTA Row: Primary solid black button with arrow icon, and Secondary soft ghost button with live demo trigger.
- Background: Soft atmospheric radial wash, no heavy outlines.`,
        tailwindSnippet: `<section className="relative pt-32 pb-20 text-center px-4 max-w-5xl mx-auto">
  <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 text-xs font-semibold text-zinc-800 mb-6">
    <span>✦</span> 100% Turnkey Frontend Kit
  </div>
  <h1 className="text-5xl sm:text-7xl font-bold tracking-tight text-zinc-950 mb-6">
    Build websites that close clients effortlessly.
  </h1>
  <p className="text-lg text-zinc-600 max-w-2xl mx-auto mb-8 leading-relaxed">
    Complete monochromatic layouts, ready-to-paste AI section prompts, and native frontend kits.
  </p>
  <div className="flex items-center justify-center gap-3">
    <button className="px-6 py-3 rounded-full bg-zinc-950 text-white font-bold text-sm hover:bg-zinc-800 transition-all">
      Get Started Free
    </button>
    <button className="px-6 py-3 rounded-full bg-zinc-100 text-zinc-800 font-bold text-sm hover:bg-zinc-200 transition-all">
      View Live Demo
    </button>
  </div>
</section>`,
      },
      {
        id: 'pricing-matrix',
        name: 'Interactive 3-Tier Pricing Matrix',
        badge: 'Pricing Section',
        description: 'Monthly/Annual toggle with feature comparison checkboxes and highlight pills.',
        promptText: `Build an interactive 3-tier pricing matrix in Tailwind CSS with an Annual 20% discount switch:
- Three columns: Starter (₹0), Studio Pro (₹2,999/mo), Agency Scale (₹7,999/mo).
- Center card highlighted with subtle zinc-100 background and 'Most Popular' floating badge.
- Feature lists with thin emerald check icons.
- Instant 1-click upgrade checkout buttons.`,
        tailwindSnippet: `<div className="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto px-4">
  <div className="p-6 rounded-3xl bg-white border border-zinc-200 shadow-sm">
    <h3 className="font-bold text-lg text-zinc-950">Starter</h3>
    <div className="text-3xl font-extrabold mt-2 mb-4">₹0<span className="text-xs text-zinc-500 font-normal">/mo</span></div>
    <ul className="space-y-2 text-xs text-zinc-600 mb-6">
      <li>✓ 1 Active Workspace</li>
      <li>✓ Unlimited Invoicing</li>
    </ul>
    <button className="w-full py-2.5 rounded-xl bg-zinc-100 font-bold text-xs hover:bg-zinc-200">Start Free</button>
  </div>
</div>`,
      },
      {
        id: 'bento-grid',
        name: 'Feature Bento Showcase Grid',
        badge: 'Bento Section',
        description: 'Asymmetric 4-card grid showing metrics, mock UI cards, and capability badges.',
        promptText: `Generate a 4-card asymmetric Bento grid with monochromatic surfaces:
- Card 1 (Span 2 cols): Large interactive mockup showcase with clean typography.
- Card 2: Performance metrics badge counter (sub-50ms speed).
- Card 3: Security & compliance indicator.
- Card 4: Multi-tenant client collaboration switcher.`,
      },
      {
        id: 'lead-drawer',
        name: 'Slide-Up Lead Capture Sheet',
        badge: 'Lead Form',
        description: 'Mobile-first bottom slide-up sheet with instant email validation and calendar picker.',
        promptText: `Create a mobile-friendly bottom slide-up lead intake drawer in React and Tailwind:
- Smooth translateY open animation with dark blurred backdrop.
- Drag handle indicator at top.
- Name, Email, Project Budget range pill selector, and Project Brief textarea.
- Monochromatic primary submit button with loading spinner state.`,
      },
    ],
    features: [
      'Pure Tailwind CSS & Next.js 15 App Router codebase',
      'Instant responsive desktop, tablet, and mobile layouts',
      'Zero layout shift (CLS 0.00) & Sub-50ms static hydration',
      'Dark and light monochromatic theme tokens included',
      'WCAG AAA accessible focus states & keyboard navigation',
      'Pre-built copy prompts for instant Claude & Cursor iteration',
    ],
    toolsReplaced: [
      { name: 'Webflow Workspace', monthlyCostUSD: 39 },
      { name: 'ThemeForest Premium Theme', monthlyCostUSD: 19 },
      { name: 'Formspree Pro Form Backend', monthlyCostUSD: 15 },
    ],
    faqs: [
      {
        question: 'How do I install this native landing page?',
        answer: 'You can copy the raw React/Tailwind component code directly into your Next.js project, or download the ZIP asset pack and run npm install & npm run dev.',
      },
      {
        question: 'Can I edit the copy and layout with AI?',
        answer: 'Yes! Every section includes a prompt specifically engineered for Claude 3.5 Sonnet, Cursor AI, and v0. Just copy the prompt, modify your requirements, and paste.',
      },
    ],
  },
  {
    id: 'studio-creative-portfolio',
    slug: 'creative-design-studio',
    title: 'Minimalist Creative Studio & Portfolio',
    shortTitle: 'Creative Portfolio',
    tagline: 'Horizontal project scroll, full-bleed media case studies, interactive client review vault.',
    category: 'landing_pages',
    categoryLabel: 'Native Landing Pages',
    assetFormat: 'native_page',
    formatBadge: 'Native Tailwind / Next.js',
    badge: 'Design Favorite',
    rating: 4.98,
    reviewsCount: 118,
    downloadsCount: 2150,
    heroDescription: 'A high-aesthetic, editorial-inspired portfolio template tailored for branding agencies, UI/UX designers, and creative directors. Featuring smooth horizontal work galleries and minimal case study layouts.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-300',
    accentText: 'text-zinc-950',
    accentColor: '#18181B',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Tailwind CSS', 'Next.js', 'React', 'Cursor AI', 'Claude 3.5', 'v0.dev'],
    frameworkStack: ['Tailwind CSS', 'Framer Motion', 'React 19'],
    quickCopyPrompt: 'Design a high-fashion minimalist design studio portfolio with large editorial typography, masonry case study grid, video player placeholders, and clean project drawer overlays.',
    sectionsIncluded: [
      {
        id: 'editorial-hero',
        name: 'Editorial Typography Hero',
        badge: 'Hero Section',
        description: 'Huge serif/sans mix headline with subtle client reel marquee.',
        promptText: `Build a minimalist creative studio hero section with high-contrast editorial typography, client marquee ticker, and smooth fade-in animations in Tailwind CSS.`,
      },
      {
        id: 'case-study-masonry',
        name: 'Masonry Case Study Gallery',
        badge: 'Portfolio Grid',
        description: '2-column dynamic aspect ratio project cards with hover zoom and tags.',
        promptText: `Create a 2-column responsive masonry case study grid with hover zoom on project thumbnails, client name, category tags, and click-to-open drawer previews.`,
      },
      {
        id: 'awards-marquee',
        name: 'Awards & Recognition Ticker',
        badge: 'Trust Bar',
        description: 'Clean typographic list of Awwwards, FWA, and design accolades.',
        promptText: `Build a minimalist awards and press ticker showcasing year, award name, project, and category in a clean monochromatic table.`,
      },
    ],
    features: [
      'High-fashion editorial typography & minimal grid',
      'Smooth masonry and horizontal scroll project showcases',
      'Interactive project drawer modal preview',
      'Optimized image loaders with blur placeholder states',
      'Full dark/light monochromatic styling',
    ],
    toolsReplaced: [
      { name: 'Readymag Business', monthlyCostUSD: 45 },
      { name: 'Squarespace Portfolio Tier', monthlyCostUSD: 23 },
    ],
    faqs: [
      {
        question: 'Does this support video case studies?',
        answer: 'Yes! The case study gallery natively supports HTML5 MP4/WebM loops, YouTube/Vimeo embeds, and high-res image carousels.',
      },
    ],
  },
  {
    id: 'saas-fintech-landing',
    slug: 'saas-fintech-landing',
    title: 'Fintech & B2B SaaS High-Conversion Landing',
    shortTitle: 'Fintech SaaS Page',
    tagline: 'Interactive live calculator, compliance trust badge wall, and conversion-optimized CTA.',
    category: 'landing_pages',
    categoryLabel: 'Native Landing Pages',
    assetFormat: 'native_page',
    formatBadge: 'Native Tailwind / Next.js',
    badge: 'Fintech Ready',
    rating: 4.94,
    reviewsCount: 89,
    downloadsCount: 1650,
    heroDescription: 'Engineered for high-ticket SaaS and financial tools. Includes live client ROI calculators, bank-grade encryption badges, feature tabs, and multi-step intake flows.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-300',
    accentText: 'text-zinc-950',
    accentColor: '#18181B',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Tailwind CSS', 'Next.js', 'Cursor AI', 'Claude 3.5', 'Lovable'],
    frameworkStack: ['Tailwind CSS', 'React 19', 'Chart.js / SVG'],
    quickCopyPrompt: 'Build a fintech landing page with a live ROI savings calculator slider, SOC2/ISO compliance trust badges, tabbed feature switcher, and instant signup modal.',
    sectionsIncluded: [
      {
        id: 'fintech-hero',
        name: 'Fintech Calculator Hero',
        badge: 'Hero Section',
        description: 'Split hero with live interactive slider computing monthly agency savings.',
        promptText: `Create a split hero section with headline on the left and an interactive monthly fee savings calculator on the right with real-time slider updates.`,
      },
      {
        id: 'compliance-wall',
        name: 'Bank-Grade Compliance Wall',
        badge: 'Trust Badges',
        description: 'Certifications, RBI guidelines, UPI 2.0 integration, and SHA-256 e-sign badges.',
        promptText: `Design a monochromatic compliance banner with 4 verified trust cards: 256-Bit SSL, UPI 2.0 Auto-Debit, IT Act 2000 E-Sign, and GST SAC 9983 Automated Compliance.`,
      },
    ],
    features: [
      'Interactive client savings calculator component',
      'Pre-built Indian payment & compliance badges',
      'Tabbed product feature switcher with SVG graphs',
      'Sub-50ms static speed and instant CTA response',
    ],
    toolsReplaced: [
      { name: 'Unbounce Conversion Builder', monthlyCostUSD: 99 },
      { name: 'Calconic Calculator Widget', monthlyCostUSD: 17 },
    ],
    faqs: [
      {
        question: 'Can I connect this calculator to my CRM?',
        answer: 'Yes, the calculator component exposes an onChange callback that sends lead metrics directly to your webhook or database.',
      },
    ],
  },
  {
    id: 'consulting-advisory-page',
    slug: 'consulting-advisory-page',
    title: 'Executive Consulting & Fractional CXO Page',
    shortTitle: 'Executive Advisory',
    tagline: 'Authority personal branding hero, calendar scheduler embed, and retainer package cards.',
    category: 'landing_pages',
    categoryLabel: 'Native Landing Pages',
    assetFormat: 'native_page',
    formatBadge: 'Native Tailwind / Next.js',
    badge: 'Executive Kit',
    rating: 4.95,
    reviewsCount: 76,
    downloadsCount: 1420,
    heroDescription: 'Tailored for fractional CMOs, management consultants, and independent legal/finance advisors. Includes calendar booking slots, client case metrics, and retainer tier structures.',
    accentBg: 'bg-zinc-100',
    accentBorder: 'border-zinc-300',
    accentText: 'text-zinc-950',
    accentColor: '#18181B',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Tailwind CSS', 'Next.js', 'React', 'Cursor AI', 'Claude 3.5'],
    frameworkStack: ['Tailwind CSS', 'React 19'],
    quickCopyPrompt: 'Design a clean executive consulting landing page with personal authority bio, client revenue metrics, 3 retainer package options, and an integrated booking widget.',
    sectionsIncluded: [
      {
        id: 'advisory-hero',
        name: 'Executive Bio & Authority Hero',
        badge: 'Hero Section',
        description: 'Clean headshot layout, revenue growth statistics, and immediate consultation CTA.',
        promptText: `Build an authority bio hero section with clean monochrome styling, client growth numbers (+₹12Cr Revenue Delivered), and a 1-click consultation scheduler button.`,
      },
    ],
    features: [
      'Clean personal authority layout & bio card',
      'Retainer package cards with SLA breakdowns',
      'Built-in calendar slot picker layout',
      'Client testimonial quotes with verified LinkedIn badges',
    ],
    toolsReplaced: [
      { name: 'Kajabi Website Tier', monthlyCostUSD: 149 },
      { name: 'Calendly Pro Plan', monthlyCostUSD: 16 },
    ],
    faqs: [
      {
        question: 'Does this integrate with Google Calendar?',
        answer: 'Yes, the consultation booking component easily connects with Cal.com, Google Calendar, or Cora Workspace native scheduler.',
      },
    ],
  },

  // ── 2. AI SECTION BUILDER PROMPTS (Claude, Cursor, Lovable, v0) ──
  {
    id: 'prompt-pack-hero-headers',
    slug: 'ai-prompt-pack-hero-headers',
    title: 'Master AI Prompt Pack: 20+ High-Converting Hero Sections',
    shortTitle: '20+ Hero Prompts',
    tagline: 'Engineered prompts for Claude 3.5, Cursor AI, Lovable & v0 to generate production-ready heroes.',
    category: 'section_prompts',
    categoryLabel: 'AI Section Prompts',
    assetFormat: 'section_prompts',
    formatBadge: 'Claude & Cursor Prompts',
    badge: 'AI Prompt Pack',
    rating: 4.99,
    reviewsCount: 310,
    downloadsCount: 5400,
    heroDescription: 'A master library of battle-tested AI generation prompts. Paste these prompts into Cursor, Claude 3.5 Sonnet, Lovable, or v0 to generate modern monochromatic hero sections with zero AI hallucinations or weird layouts.',
    accentBg: 'bg-purple-50',
    accentBorder: 'border-purple-200',
    accentText: 'text-purple-900',
    accentColor: '#9333EA',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Claude 3.5 Sonnet', 'Cursor AI', 'Lovable.dev', 'v0.dev', 'Bolt.new', 'ChatGPT-4o'],
    frameworkStack: ['Prompt Engineering', 'Tailwind CSS v4', 'React 19'],
    quickCopyPrompt: 'Generate a monochromatic hero section using Tailwind CSS. It must include a subtle status badge, a 2-line high-contrast headline, an lead subtitle, two pill action buttons, and a responsive mockup preview card below.',
    sectionsIncluded: [
      {
        id: 'prompt-split-hero',
        name: 'Split Video / Mockup Hero Prompt',
        badge: 'AI Prompt',
        description: 'For SaaS tools and digital agencies needing a side-by-side software preview.',
        promptText: `Act as a senior frontend engineer. Write a responsive Split Hero component in React and Tailwind CSS:
1. Left column: Status pill with glowing green dot, 60px display headline, 18px body copy, dual action button row (Primary black, Secondary white with border).
2. Right column: Floating browser mockup card with dark header bar, tab switcher, and live stats chart.
3. Use only neutral zinc shades (zinc-50 through zinc-950) with zero colorful gradients.
4. Add smooth hover transitions (hover:scale-[1.02] duration-200).`,
      },
      {
        id: 'prompt-centered-saas',
        name: 'Centered Atmospheric SaaS Hero Prompt',
        badge: 'AI Prompt',
        description: 'Minimalist centered hero with subtle gradient blur and email waitlist form.',
        promptText: `Write a modern centered SaaS hero in React and Tailwind CSS:
- Max-width 800px centered container with text-center.
- Headline with tracking-[-0.03em] and font-semibold.
- Integrated pill input group containing email text field and 'Claim Workspace' button.
- Sub-text with 'Zero credit card required · Instant 30s setup' trust note.`,
      },
      {
        id: 'prompt-bento-hero',
        name: 'Asymmetric Bento Header Prompt',
        badge: 'AI Prompt',
        description: 'Hero that integrates live metric counters directly into the header fold.',
        promptText: `Generate an asymmetric Bento Header in Tailwind CSS with 3 metric tiles directly below the main title. Include 99.9% Uptime, Sub-50ms Latency, and ₹0 Setup Fee badges.`,
      },
    ],
    features: [
      '20+ battle-tested prompts engineered for Claude 3.5 and Cursor',
      'Guaranteed monochromatic design output with zero colorful AI noise',
      'Includes explicit responsive instructions for desktop, tablet & mobile',
      'Copy-paste ready for immediate code generation in 1 click',
    ],
    toolsReplaced: [
      { name: 'UI Prompt Packs Paid Subscriptions', monthlyCostUSD: 29 },
    ],
    faqs: [
      {
        question: 'How do I use these prompts in Cursor or Claude?',
        answer: 'Simply copy the prompt using the 1-click button, open Cursor Composer (Cmd+I) or Claude 3.5 Sonnet, paste the prompt, and specify your brand details.',
      },
    ],
  },
  {
    id: 'prompt-pack-pricing-bento',
    slug: 'ai-prompt-pack-pricing-bento',
    title: 'AI Prompt Pack: Pricing Tables, Bento Grids & Feature Sections',
    shortTitle: 'Pricing & Bento Prompts',
    tagline: 'Generate pixel-perfect feature grids, comparison tables, and interactive pricing toggles.',
    category: 'section_prompts',
    categoryLabel: 'AI Section Prompts',
    assetFormat: 'section_prompts',
    formatBadge: 'Claude & Cursor Prompts',
    badge: 'High Conversion',
    rating: 4.97,
    reviewsCount: 184,
    downloadsCount: 3900,
    heroDescription: 'Targeted prompt collection for generating pricing tables with billing toggles, 4-card bento grids, interactive feature accordions, and customer proof sliders.',
    accentBg: 'bg-purple-50',
    accentBorder: 'border-purple-200',
    accentText: 'text-purple-900',
    accentColor: '#9333EA',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Claude 3.5 Sonnet', 'Cursor AI', 'Lovable', 'v0.dev'],
    frameworkStack: ['Prompt Engineering', 'Tailwind CSS'],
    quickCopyPrompt: 'Create an interactive 3-column pricing matrix in Tailwind CSS with monthly and annual billing switcher, feature checklists, and sticky popular badge on the middle tier.',
    sectionsIncluded: [
      {
        id: 'pricing-toggle-prompt',
        name: 'Interactive Billing Toggle Prompt',
        badge: 'AI Prompt',
        description: 'Prompt to generate animated Monthly / Annual switchers with savings badges.',
        promptText: `Create a clean animated billing toggle in React/Tailwind:
- Segmented control with 'Monthly' and 'Annual (Save 20%)'.
- Smooth spring indicator transition.
- Dynamically calculates price difference for ₹1,499/mo vs ₹1,199/mo.`,
      },
      {
        id: 'feature-accordion-prompt',
        name: 'Expandable Feature Accordion Prompt',
        badge: 'AI Prompt',
        description: 'Interactive QA and feature breakdown accordion with Plus/Minus icons.',
        promptText: `Write an accessible FAQ accordion component in Tailwind CSS:
- Smooth height animation.
- Plus / Minus toggle icon with transition-transform.
- Clean neutral zinc border with subtle hover background fill.`,
      },
    ],
    features: [
      'Pre-formatted pricing math and discount calculator logic',
      'Bento grid layouts with responsive column wrapping',
      'Accessible ARIA compliance prompts',
    ],
    toolsReplaced: [
      { name: 'Elfsight Pricing Table Widget', monthlyCostUSD: 18 },
    ],
    faqs: [
      {
        question: 'Will these prompts work with plain HTML/CSS?',
        answer: 'Yes! You can ask the AI model to output pure HTML/Tailwind or React JSX as needed.',
      },
    ],
  },
  {
    id: 'prompt-pack-forms-modals',
    slug: 'ai-prompt-pack-forms-modals',
    title: 'AI Prompt Pack: Mobile Bottom Sheets, Forms & Drawers',
    shortTitle: 'Forms & Drawers Prompts',
    tagline: 'Mobile-first bottom slide-up sheets, multi-step lead funnels, and checkout drawers.',
    category: 'section_prompts',
    categoryLabel: 'AI Section Prompts',
    assetFormat: 'section_prompts',
    formatBadge: 'Claude & Cursor Prompts',
    badge: 'Mobile UX',
    rating: 4.96,
    reviewsCount: 140,
    downloadsCount: 2980,
    heroDescription: 'Mobile UX prompt guidelines that eliminate clunky side drawers and popups on phones. Generates silky bottom slide-up sheets with drag handles and instant validation.',
    accentBg: 'bg-purple-50',
    accentBorder: 'border-purple-200',
    accentText: 'text-purple-900',
    accentColor: '#9333EA',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Claude 3.5 Sonnet', 'Cursor AI', 'Lovable', 'v0.dev'],
    frameworkStack: ['Prompt Engineering', 'Tailwind CSS', 'Framer Motion'],
    quickCopyPrompt: 'Build a mobile bottom-up slide sheet in React and Tailwind with a drag indicator handle, top-rounded corners, backdrop blur, and 3-step lead intake form.',
    sectionsIncluded: [
      {
        id: 'mobile-sheet-prompt',
        name: 'Mobile Slide-Up Sheet Prompt',
        badge: 'AI Prompt',
        description: 'Eliminates side drawers on mobile with ergonomic bottom sheets.',
        promptText: `Write a React bottom slide sheet component:
- Position fixed inset-x-0 bottom-0 z-50.
- Top rounded 3xl corners with subtle zinc border.
- Drag handle at top (w-10 h-1 bg-zinc-300 rounded-full mx-auto mb-4).
- Dark blurred backdrop with click-to-close handler.`,
      },
    ],
    features: [
      'Zero mobile side drawers — pure bottom slide-up UX',
      'Integrated touch-action and gesture drag handles',
      'Step-by-step form progress indicators',
    ],
    toolsReplaced: [
      { name: 'Typeform Starter Plan', monthlyCostUSD: 29 },
    ],
    faqs: [
      {
        question: 'Does this handle keyboard focus on iOS Safari?',
        answer: 'Yes, the prompt includes specific viewport resize and virtual keyboard padding handlers for iOS.',
      },
    ],
  },
  {
    id: 'prompt-pack-dashboard-widgets',
    slug: 'ai-prompt-pack-dashboard-widgets',
    title: 'AI Prompt Pack: SaaS Dashboards & Admin Portals',
    shortTitle: 'Dashboard UI Prompts',
    tagline: 'Prompts to build Notion-style tables, admin popovers, status cards & KPI ribbons.',
    category: 'section_prompts',
    categoryLabel: 'AI Section Prompts',
    assetFormat: 'section_prompts',
    formatBadge: 'Claude & Cursor Prompts',
    badge: 'Admin SaaS',
    rating: 4.95,
    reviewsCount: 162,
    downloadsCount: 3120,
    heroDescription: 'Construct comprehensive SaaS portals and backoffice dashboards. Features sticky bottom admin popovers, searchable data tables, and client milestone trackers.',
    accentBg: 'bg-purple-50',
    accentBorder: 'border-purple-200',
    accentText: 'text-purple-900',
    accentColor: '#9333EA',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Cursor AI', 'Claude 3.5', 'Lovable', 'v0.dev'],
    frameworkStack: ['Prompt Engineering', 'React', 'Tailwind CSS'],
    quickCopyPrompt: 'Generate a Notion-styled interactive data table in Tailwind CSS with search filtering, column sorting, status tags, and row action popovers.',
    sectionsIncluded: [
      {
        id: 'admin-popover-prompt',
        name: 'Sticky Sidebar Admin Popover Prompt',
        badge: 'AI Prompt',
        description: 'Bottom sticky user profile widget that opens a floating settings popover.',
        promptText: `Create a sticky bottom sidebar admin profile widget:
- Fixed to the bottom of the sidebar with avatar, user name, and workspace status dot.
- Clicking opens a floating popover card directly above it with AI model selector and quota metrics.`,
      },
    ],
    features: [
      'Notion-inspired monochromatic data table layouts',
      'Admin popover menus with status indicators',
      'Real-time search and filter UI blueprints',
    ],
    toolsReplaced: [
      { name: 'Tailwind UI Application UI Kit', monthlyCostUSD: 299 },
    ],
    faqs: [
      {
        question: 'Can I use this for client portals?',
        answer: 'Yes! These prompts are designed for building client staging dashboards and approval portals.',
      },
    ],
  },

  // ── 3. ELEMENTOR TEMPLATE KITS (.json blueprints) ──
  {
    id: 'elementor-agency-pro-kit',
    slug: 'elementor-agency-pro-kit',
    title: 'Elementor Pro Agency Turnkey Template Kit',
    shortTitle: 'Elementor Agency Kit',
    tagline: 'Monochromatic Elementor Pro template kit (.json) with 8 ready-to-import page templates.',
    category: 'elementor_kits',
    categoryLabel: 'Elementor Template Kits',
    assetFormat: 'elementor_kit',
    formatBadge: 'Elementor Kit (.json)',
    badge: 'Elementor Pro',
    rating: 4.92,
    reviewsCount: 165,
    downloadsCount: 3450,
    heroDescription: 'Complete, lightweight Elementor Pro template kit. Clean JSON blueprints for Header, Footer, Hero, Case Studies, Pricing Table, and Lead Forms without any slow 3rd-party add-ons.',
    accentBg: 'bg-emerald-50',
    accentBorder: 'border-emerald-200',
    accentText: 'text-emerald-900',
    accentColor: '#059669',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Elementor Pro', 'WordPress', 'Hello Elementor Theme'],
    frameworkStack: ['Elementor JSON', 'CSS Flexbox Containers', 'SVG Icons'],
    quickCopyPrompt: 'Importable Elementor Pro kit with Flexbox Container layouts, custom global typography, neutral color palette, and zero external widget dependencies.',
    sectionsIncluded: [
      {
        id: 'el-hero-container',
        name: 'Flexbox Hero Container Blueprint',
        badge: 'Elementor Section',
        description: 'Clean Flexbox container layout ready to import into Elementor Theme Builder.',
        promptText: 'Elementor Flexbox Container: Hero section with heading, subtext, dual buttons, and responsive tablet/mobile breakpoints.',
        elementorBlueprint: '{"version":"0.4","title":"Cora Agency Hero Container","type":"container","elements":[{"elType":"container","settings":{"content_width":"boxed","flex_direction":"column","align_items":"center"}}]}',
      },
      {
        id: 'el-pricing-table',
        name: 'Elementor 3-Tier Pricing Blueprint',
        badge: 'Elementor Section',
        description: 'Container-based pricing layout with highlighted central card and button triggers.',
        promptText: 'Elementor Pricing Container: 3 column flexbox with custom CSS borders and button links.',
      },
    ],
    features: [
      '100% Flexbox Container based — zero legacy inner sections',
      'No slow third-party Elementor addon plugins needed',
      'Pre-configured global color palette and typography styles',
      'Includes Header, Footer, 404, Single Post, and Archive templates',
    ],
    toolsReplaced: [
      { name: 'Envato Elements Subscription', monthlyCostUSD: 33 },
      { name: 'CrocoBlock JetElements', monthlyCostUSD: 19 },
    ],
    faqs: [
      {
        question: 'How do I import this into Elementor?',
        answer: 'In WordPress, go to Elementor > Tools > Import / Export Kit, upload the template JSON package, and click Apply.',
      },
    ],
  },
  {
    id: 'elementor-creative-studio-kit',
    slug: 'elementor-creative-studio-kit',
    title: 'Elementor Creative Studio & Freelancer Kit',
    shortTitle: 'Elementor Studio Kit',
    tagline: 'Minimalist editorial portfolio kit with full-width showcase containers & booking forms.',
    category: 'elementor_kits',
    categoryLabel: 'Elementor Template Kits',
    assetFormat: 'elementor_kit',
    formatBadge: 'Elementor Kit (.json)',
    badge: 'Portfolio Kit',
    rating: 4.93,
    reviewsCount: 104,
    downloadsCount: 2100,
    heroDescription: 'Tailored for freelance photographers, designers, and visual creators who use WordPress & Elementor. Clean aesthetic, fast load times, and mobile responsive containers.',
    accentBg: 'bg-emerald-50',
    accentBorder: 'border-emerald-200',
    accentText: 'text-emerald-900',
    accentColor: '#059669',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Elementor Pro', 'WordPress', 'Hello Theme'],
    frameworkStack: ['Elementor JSON', 'Flexbox Containers'],
    quickCopyPrompt: 'Importable Elementor portfolio kit with grid gallery, lightbox triggers, bio card, and booking form popup.',
    sectionsIncluded: [
      {
        id: 'el-gallery-container',
        name: 'Full-Width Gallery Showcase',
        badge: 'Elementor Section',
        description: 'Dynamic grid showcase with built-in lightbox preview.',
        promptText: 'Elementor Portfolio Gallery: Flex container with 2-column image cards and hover zoom.',
      },
    ],
    features: [
      'Minimal editorial layout with spacious padding',
      'Pre-built lightbox gallery and case study single page',
      'Integrated contact & consultation form blueprint',
    ],
    toolsReplaced: [
      { name: 'TemplateMonster Single Theme', monthlyCostUSD: 59 },
    ],
    faqs: [
      {
        question: 'Does this work with free Elementor?',
        answer: 'The core page containers work with free Elementor; header/footer builders require Elementor Pro or Theme Builder.',
      },
    ],
  },
  {
    id: 'elementor-real-estate-kit',
    slug: 'elementor-real-estate-kit',
    title: 'Elementor Luxury Real Estate & Brokerage Kit',
    shortTitle: 'Elementor Real Estate',
    tagline: 'High-end property listing cards, floorplan tabs, and tour booking modal triggers.',
    category: 'elementor_kits',
    categoryLabel: 'Elementor Template Kits',
    assetFormat: 'elementor_kit',
    formatBadge: 'Elementor Kit (.json)',
    badge: 'Real Estate',
    rating: 4.91,
    reviewsCount: 88,
    downloadsCount: 1890,
    heroDescription: 'High-converting real estate kit for property developers and luxury brokers. Includes property detail layouts, amenity icon grids, interactive map embeds, and lead forms.',
    accentBg: 'bg-emerald-50',
    accentBorder: 'border-emerald-200',
    accentText: 'text-emerald-900',
    accentColor: '#059669',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Elementor Pro', 'WordPress'],
    frameworkStack: ['Elementor JSON', 'Flexbox Containers'],
    quickCopyPrompt: 'Elementor luxury real estate listing page with property specifications bar, photo slider, floorplan preview, and scheduled tour contact box.',
    sectionsIncluded: [
      {
        id: 'el-property-hero',
        name: 'Property Listing Hero Blueprint',
        badge: 'Elementor Section',
        description: 'High-impact hero with price badge, square footage counter, and 3D tour button.',
        promptText: 'Elementor Property Hero: Full-width banner with overlay badge and instant booking trigger.',
      },
    ],
    features: [
      'Property amenity spec bar (Beds, Baths, Sq. Ft., Price)',
      'Floorplan download trigger and high-res gallery',
      'VIP private viewing scheduler form',
    ],
    toolsReplaced: [
      { name: 'RealHomes Premium WordPress Theme', monthlyCostUSD: 69 },
    ],
    faqs: [
      {
        question: 'Can I add dynamic custom fields (ACF)?',
        answer: 'Yes, all container elements are mapped to standard Elementor dynamic tags for seamless ACF/Pods integration.',
      },
    ],
  },
  {
    id: 'elementor-law-ca-firm-kit',
    slug: 'elementor-law-ca-firm-kit',
    title: 'Elementor Corporate Legal & CA Advisory Kit',
    shortTitle: 'Elementor Legal Kit',
    tagline: 'Authoritative law firm & chartered accountant kit with practice area matrices & retainer forms.',
    category: 'elementor_kits',
    categoryLabel: 'Elementor Template Kits',
    assetFormat: 'elementor_kit',
    formatBadge: 'Elementor Kit (.json)',
    badge: 'Legal & CA',
    rating: 4.95,
    reviewsCount: 72,
    downloadsCount: 1540,
    heroDescription: 'Prestigious corporate layout designed for law firms, tax practitioners, and audit consultants. Features structured practice area grids, attorney bio cards, and retainer inquiry forms.',
    accentBg: 'bg-emerald-50',
    accentBorder: 'border-emerald-200',
    accentText: 'text-emerald-900',
    accentColor: '#059669',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Elementor Pro', 'WordPress'],
    frameworkStack: ['Elementor JSON', 'Flexbox Containers'],
    quickCopyPrompt: 'Corporate legal advisor Elementor kit with practice area cards, compliance trust badges, case victory metrics, and confidential consultation form.',
    sectionsIncluded: [
      {
        id: 'el-practice-areas',
        name: 'Practice Areas Grid Blueprint',
        badge: 'Elementor Section',
        description: '6-card corporate practice matrix with icon pills and learn more links.',
        promptText: 'Elementor Practice Area: 3x2 grid container with icon, title, short description, and link.',
      },
    ],
    features: [
      'Authoritative corporate design with understated typography',
      'Confidential consultation inquiry form with file upload support',
      'Attorney and partner biography cards',
    ],
    toolsReplaced: [
      { name: 'LawPress WordPress Theme', monthlyCostUSD: 49 },
    ],
    faqs: [
      {
        question: 'Is this kit mobile responsive?',
        answer: 'Yes, all container breakpoints are verified for iPhone, iPad, and desktop viewports.',
      },
    ],
  },

  // ── 4. LOVABLE & REACT PROJECTS ──
  {
    id: 'lovable-agency-fullstack-kit',
    slug: 'lovable-agency-fullstack-kit',
    title: 'Lovable Turnkey Agency Workspace Project',
    shortTitle: 'Lovable Agency Project',
    tagline: 'Ready-to-fork fullstack Lovable.dev / React project with auth, client portals & invoices.',
    category: 'lovable_projects',
    categoryLabel: 'Lovable & React Projects',
    assetFormat: 'lovable_project',
    formatBadge: 'Lovable Project & React',
    badge: '1-Click Fork',
    rating: 4.98,
    reviewsCount: 220,
    downloadsCount: 4100,
    heroDescription: 'A complete Lovable.dev project repository. Fork with 1 click to get a live web app featuring Supabase authentication, client portal staging vaults, GST tax math calculators, and dark/light mode.',
    accentBg: 'bg-indigo-50',
    accentBorder: 'border-indigo-200',
    accentText: 'text-indigo-900',
    accentColor: '#4F46E5',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Lovable.dev', 'v0.dev', 'Cursor AI', 'React 19', 'Vite', 'Tailwind CSS'],
    frameworkStack: ['Lovable / Vite', 'React 19', 'Tailwind CSS', 'Supabase / Mock DB'],
    quickCopyPrompt: 'Fork complete Lovable agency template with Supabase client management, milestone billing, and passwordless review portal.',
    sectionsIncluded: [
      {
        id: 'lovable-dashboard',
        name: 'Interactive Agency Dashboard View',
        badge: 'Lovable View',
        description: 'Kanban lead pipeline, client project timeline, and revenue analytics.',
        promptText: 'Build a fullstack agency dashboard in Lovable with Kanban board, revenue metrics card, and client portal access generator.',
      },
      {
        id: 'lovable-client-portal',
        name: 'Client Staging & Approval Vault',
        badge: 'Lovable View',
        description: 'Interactive client screen where prospects leave feedback and sign off milestones.',
        promptText: 'Create a passwordless client staging portal with visual feedback pins and 1-click milestone approval buttons.',
      },
    ],
    features: [
      '1-Click fork to Lovable.dev and immediate live preview',
      'Includes complete React components, routes, and UI state',
      'Pre-integrated with Tailwind CSS and Radix UI primitives',
      'Clean file structure ready for Supabase or custom API hooks',
    ],
    toolsReplaced: [
      { name: 'Lovable Pro Project Starter', monthlyCostUSD: 20 },
      { name: 'SaaS Boilerplate License', monthlyCostUSD: 199 },
    ],
    faqs: [
      {
        question: 'How do I fork this into my Lovable account?',
        answer: 'Click "Fork in Lovable" or copy the full component project code directly into Lovable.dev or v0.dev to start customizing.',
      },
    ],
  },
  {
    id: 'lovable-crm-lead-pipeline',
    slug: 'lovable-crm-lead-pipeline',
    title: 'Lovable CRM & Lead Funnel Pipeline Project',
    shortTitle: 'Lovable CRM Pipeline',
    tagline: 'Interactive Kanban pipeline, lead scoring matrix, and automated follow-up triggers.',
    category: 'lovable_projects',
    categoryLabel: 'Lovable & React Projects',
    assetFormat: 'lovable_project',
    formatBadge: 'Lovable Project & React',
    badge: 'CRM App',
    rating: 4.96,
    reviewsCount: 145,
    downloadsCount: 2750,
    heroDescription: 'Ready-to-run React/Lovable CRM web application. Features drag-and-drop Kanban columns (Inquiry -> Proposal Sent -> Negotiation -> Won), lead value calculations, and quick call log drawers.',
    accentBg: 'bg-indigo-50',
    accentBorder: 'border-indigo-200',
    accentText: 'text-indigo-900',
    accentColor: '#4F46E5',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Lovable.dev', 'Cursor AI', 'v0.dev', 'React 19'],
    frameworkStack: ['React', 'Tailwind CSS', 'dnd-kit / Drag & Drop'],
    quickCopyPrompt: 'Create an interactive CRM Kanban board in React and Tailwind with draggable deal cards, pipeline total revenue calculation, and deal detail drawer.',
    sectionsIncluded: [
      {
        id: 'lovable-kanban-board',
        name: 'Draggable Deal Kanban Pipeline',
        badge: 'Lovable Component',
        description: 'Drag-and-drop lead stage workflow with real-time deal stage summation.',
        promptText: 'Build a Kanban pipeline with 4 columns: New Lead, Qualified, In Negotiation, Closed Won. Each card shows deal value, client avatar, and last contact date.',
      },
    ],
    features: [
      'Interactive drag-and-drop deal management',
      'Real-time deal value aggregation across stages',
      'Contact card drawer with communication history timeline',
    ],
    toolsReplaced: [
      { name: 'Pipedrive Starter Tier', monthlyCostUSD: 24 },
      { name: 'HubSpot Sales Hub Starter', monthlyCostUSD: 20 },
    ],
    faqs: [
      {
        question: 'Does this support drag and drop on mobile?',
        answer: 'Yes, it uses touch-optimized gesture handlers to ensure seamless column re-ordering on mobile devices.',
      },
    ],
  },
  {
    id: 'lovable-invoice-payment-app',
    slug: 'lovable-invoice-payment-app',
    title: 'Lovable Instant GST Invoice & UPI QR App',
    shortTitle: 'Lovable Invoice App',
    tagline: '18% GST tax math engine, SAC 9983 lookup, and dynamic dynamic UPI QR code generator.',
    category: 'lovable_projects',
    categoryLabel: 'Lovable & React Projects',
    assetFormat: 'lovable_project',
    formatBadge: 'Lovable Project & React',
    badge: 'Fintech Tool',
    rating: 4.97,
    reviewsCount: 190,
    downloadsCount: 3800,
    heroDescription: 'A standalone React/Lovable billing web app. Calculate CGST/SGST/IGST breakdown in real-time, generate downloadable PDF invoices, and render live UPI QR payment codes for Indian clients.',
    accentBg: 'bg-indigo-50',
    accentBorder: 'border-indigo-200',
    accentText: 'text-indigo-900',
    accentColor: '#4F46E5',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Lovable.dev', 'Cursor AI', 'v0.dev', 'React 19'],
    frameworkStack: ['React', 'Tailwind CSS', 'QRCode.react', 'html2pdf'],
    quickCopyPrompt: 'Build an Indian GST invoicing tool in React with real-time SAC 9983 tax math, intra-state CGST/SGST split, and dynamic UPI QR code generator.',
    sectionsIncluded: [
      {
        id: 'lovable-tax-calc',
        name: 'Live GST Calculation Engine',
        badge: 'Lovable Engine',
        description: 'Auto-computes 18% GST, reverses inclusive tax, and handles reverse charge.',
        promptText: 'React GST calculator with subtotal, 9% CGST, 9% SGST or 18% IGST radio switch, and instant UPI QR code renderer.',
      },
    ],
    features: [
      'Real-time Indian GST tax calculator (CGST/SGST/IGST)',
      'Dynamic UPI string generator (upi://pay?pa=...&am=...)',
      '1-Click PDF export and client share link generator',
    ],
    toolsReplaced: [
      { name: 'Vyapar Desktop Invoice Pro', monthlyCostUSD: 15 },
      { name: 'Zoho Invoice Premium', monthlyCostUSD: 19 },
    ],
    faqs: [
      {
        question: 'Does this generate valid GST tax invoices?',
        answer: 'Yes, it complies with Rule 46 of the CGST Rules 2017 with SAC code, HSN breakdown, and supplier GSTIN fields.',
      },
    ],
  },
  {
    id: 'lovable-client-onboarding-flow',
    slug: 'lovable-client-onboarding-flow',
    title: 'Lovable 5-Step Guided Client Onboarding Flow',
    shortTitle: 'Lovable Onboarding',
    tagline: 'Multi-step onboarding wizard: Project Details -> Scope -> Agreement -> Payment -> Portal Access.',
    category: 'lovable_projects',
    categoryLabel: 'Lovable & React Projects',
    assetFormat: 'lovable_project',
    formatBadge: 'Lovable Project & React',
    badge: 'Onboarding Flow',
    rating: 4.95,
    reviewsCount: 112,
    downloadsCount: 2340,
    heroDescription: 'A friction-free multi-step client onboarding wizard. Guides new agency clients through project scope intake, digital signature sign-off, advance retainer payment, and workspace setup in under 3 minutes.',
    accentBg: 'bg-indigo-50',
    accentBorder: 'border-indigo-200',
    accentText: 'text-indigo-900',
    accentColor: '#4F46E5',
    coverImage: '/images/cora_hero_landscape.jpg',
    compatibleBuilders: ['Lovable.dev', 'Cursor AI', 'v0.dev', 'React 19'],
    frameworkStack: ['React', 'Tailwind CSS', 'Framer Motion'],
    quickCopyPrompt: 'Create a 5-step client onboarding wizard in React: Step 1 Details, Step 2 Scope Checklist, Step 3 Digital Agreement Sign, Step 4 Advance Payment UPI, Step 5 Success Screen.',
    sectionsIncluded: [
      {
        id: 'lovable-stepper',
        name: '5-Step Linear Progress Stepper',
        badge: 'Lovable Wizard',
        description: 'Guided progress stepper with validated inputs and instant step restoration.',
        promptText: 'Build a 5-step guided stepper with animated step numbers, back/next navigation, and completed step checkmarks.',
      },
    ],
    features: [
      '5-Step guided progress stepper with form state persistence',
      'Integrated digital signature canvas component',
      'Instant client workspace portal provisioning trigger',
    ],
    toolsReplaced: [
      { name: 'PandaDoc Onboarding Workflow', monthlyCostUSD: 49 },
      { name: 'Bonsai Client Onboarding', monthlyCostUSD: 32 },
    ],
    faqs: [
      {
        question: 'Can I add custom onboarding steps?',
        answer: 'Yes, the stepper is array-driven. You can add or remove steps (like KYC upload or questionnaire) with 1 line of config.',
      },
    ],
  },
];
