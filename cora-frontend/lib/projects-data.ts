/**
 * Cora Growth Platform — Projects & Case Studies Data Layer
 * 
 * Provides TypeScript schemas, seed case studies, and dynamic API client
 * for agency portfolio publishing.
 */

export interface ProjectKPI {
  value: string;
  label: string;
  context?: string;
  trend?: 'up' | 'down' | 'neutral';
}

export interface ProjectTestimonial {
  quote: string;
  author: string;
  role: string;
  company: string;
  avatarUrl?: string;
  verified?: boolean;
}

export interface ProjectDeliverable {
  title: string;
  category?: string;
  description?: string;
}

export interface ProjectGalleryItem {
  url: string;
  caption?: string;
  alt?: string;
}

export interface ProjectCaseStudy {
  id: string | number;
  agencyId?: string;
  title: string;
  slug: string;
  subtitle?: string;
  clientName: string;
  clientLogoUrl?: string;
  clientIndustry: string;
  projectDuration?: string;
  yearCompleted?: string;
  services: string[];
  liveUrl?: string;
  isConfidential: boolean;
  isFeatured: boolean;
  status: 'draft' | 'published' | 'archived';
  executiveSummary: string;
  challengeContent: string;
  solutionContent: string;
  deliverables: string[];
  kpis: ProjectKPI[];
  testimonial?: ProjectTestimonial;
  heroMediaType: 'image' | 'video';
  heroImageUrl: string;
  heroVideoUrl?: string;
  galleryImages?: ProjectGalleryItem[];
  beforeImageUrl?: string;
  beforeLabel?: string;
  afterImageUrl?: string;
  afterLabel?: string;
  videoEmbedUrl?: string;
  ctaType: string;
  ctaTitle: string;
  ctaSubtitle?: string;
  ctaButtonText: string;
  ctaButtonUrl: string;
  metaTitle?: string;
  metaDescription?: string;
  targetKeywords?: string[];
  canonicalUrl?: string;
  viewsCount?: number;
  ctaClicks?: number;
  createdAt?: string;
  updatedAt?: string;
}

export const SEED_PROJECTS: ProjectCaseStudy[] = [
  {
    id: 'proj_01',
    agencyId: 'super',
    title: 'Scaling B2B FinTech Pipeline 340% via Full-Funnel SEO & Paid Media',
    slug: 'fintech-pipeline-scale',
    subtitle: 'High-intent programmatic search and targeted LinkedIn ads for an enterprise treasury management platform.',
    clientName: 'Apex Financial Technologies',
    clientIndustry: 'FinTech / B2B SaaS',
    projectDuration: '90-Day Sprint',
    yearCompleted: '2026',
    services: ['SEO & GEO Strategy', 'Performance Paid Media', 'CRO & Landing Pages', 'Design System Architecture'],
    liveUrl: 'https://apexfinancial.demo',
    isConfidential: false,
    isFeatured: true,
    status: 'published',
    executiveSummary: 'Apex Financial was experiencing rising CAC across Meta and Google Search with a stagnant organic discovery footprint. Cora engineered a multi-layered programmatic SEO architecture paired with high-intent LinkedIn demand generation, slashing acquisition costs and driving $1.4M in qualified pipeline.',
    challengeContent: 'Before engaging our agency, Apex Financial relied almost entirely on outbound SDR efforts and high-cost general broad search keywords. Their organic traffic was generating fewer than 12 demo requests per month, and their enterprise sales cycle suffered from a 68% drop-off at the product evaluation phase due to fragmented messaging.',
    solutionContent: 'We executed a comprehensive 3-stage transformation:\n\n1. **Programmatic Search Architecture**: Built 48 high-intent comparison and workflow hubs targeting enterprise treasury keywords with sub-second page performance.\n2. **Account-Based Paid Engine**: Structured bespoke LinkedIn Sponsored Content and Google Search campaigns focused exclusively on CFOs and VPs of Finance.\n3. **Conversion Funnel Overhaul**: Redesigned their product overview and demo booking flow into a frictionless 2-step experience with instant calendar routing.',
    deliverables: [
      'Programmatic Content Architecture (48 Landing Hubs)',
      'High-Intent LinkedIn Demand Generation Campaigns',
      'Frictionless 2-Step Interactive Demo Funnel',
      'Executive Attribution & Pipeline Telemetry Dashboard'
    ],
    kpis: [
      {
        value: '+340%',
        label: 'Organic Revenue Pipeline',
        context: 'Within 90 days of cluster release',
        trend: 'up'
      },
      {
        value: '3.8x',
        label: 'Blended Paid ROAS',
        context: 'Scaled monthly spend 4.5x efficiently',
        trend: 'up'
      },
      {
        value: '-42%',
        label: 'Qualified Cost Per Acquisition',
        context: 'Reduced from $480 to $278 per SQL',
        trend: 'down'
      }
    ],
    testimonial: {
      quote: 'The transformation in our inbound pipeline was immediate. We went from chasing unqualified leads to having Fortune 500 treasury teams booking audits directly on our website every single week.',
      author: 'Aarav Mehta',
      role: 'VP of Global Growth',
      company: 'Apex Financial Technologies',
      verified: true
    },
    heroMediaType: 'image',
    heroImageUrl: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1600&q=80',
    galleryImages: [
      {
        url: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
        caption: 'Executive Dashboard & Inbound Revenue Telemetry',
        alt: 'Dashboard analytics visualization'
      },
      {
        url: 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=1200&q=80',
        caption: 'Conversion-Optimized Enterprise Landing Experience',
        alt: 'Landing page UI layout'
      }
    ],
    beforeLabel: 'Legacy Broadcast Campaign',
    afterLabel: 'Cora Full-Funnel Growth Engine',
    ctaType: 'book_audit',
    ctaTitle: 'Ready to Expand Your Inbound Pipeline?',
    ctaSubtitle: 'Book a 30-minute growth teardown with our senior strategy team to identify your top pipeline leaks.',
    ctaButtonText: 'Schedule Inbound Growth Audit',
    ctaButtonUrl: '/contact',
    metaTitle: 'Apex Financial Case Study: +340% Pipeline Expansion | Cora Agency',
    metaDescription: 'How our growth team generated 340% inbound revenue pipeline for Apex Financial using programmatic SEO and high-intent paid acquisition.',
    targetKeywords: ['fintech case study', 'b2b saas pipeline growth', 'programmatic seo agency'],
    viewsCount: 3420,
    ctaClicks: 218
  },
  {
    id: 'proj_02',
    agencyId: 'super',
    title: 'Modernizing D2C Brand Identity & E-Commerce Speed for 4.2x Conversions',
    slug: 'd2c-luxury-ecommerce-overhaul',
    subtitle: 'Headless Next.js storefront rebuild, editorial design system, and multi-channel retention loops.',
    clientName: 'Aura Living Collection',
    clientIndustry: 'E-Commerce / Luxury Living',
    projectDuration: '60-Day Sprint',
    yearCompleted: '2026',
    services: ['Headless Next.js Development', 'Brand Identity & Visuals', 'Shopify Plus Migration', 'Retention Automation'],
    liveUrl: 'https://auraliving.demo',
    isConfidential: false,
    isFeatured: true,
    status: 'published',
    executiveSummary: 'Aura Living was hindered by an outdated monolithic theme causing 5.4-second load times on mobile devices and an 82% cart abandonment rate. Cora redesigned their visual design system and deployed a headless Next.js architecture that cut page latency to 420ms, increasing mobile conversions by 4.2x.',
    challengeContent: 'Despite strong editorial press and social brand affinity, Aura Living suffered from slow mobile page rendering, lack of brand cohesion across PDPs, and an unoptimized one-page checkout that caused high customer drop-off during peak sales hours.',
    solutionContent: 'We rebuilt their digital experience from the ground up:\n\n1. **Headless Speed Architecture**: Implemented Next.js with edge caching, sub-50ms font rendering, and zero-layout-shift image optimization.\n2. **Editorial Visual Language**: Crafted a warm Claude cream and monochromatic aesthetic highlighting tactile product textures and editorial typography.\n3. **Post-Purchase Retention Loops**: Deployed behavioral SMS and automated email replenishment flows driven by customer purchase timing.',
    deliverables: [
      'Custom Headless Next.js Storefront',
      'Editorial Monochromatic Design Tokens & UI Library',
      'Shopify Plus Checkout Extensibility Integration',
      'Automated Lifecycle Email & SMS Trigger Engine'
    ],
    kpis: [
      {
        value: '4.2x',
        label: 'Mobile Conversion Rate',
        context: 'Increase in completed checkout transactions',
        trend: 'up'
      },
      {
        value: '420ms',
        label: 'Average Mobile Load Speed',
        context: 'Down from 5.4s on legacy platform',
        trend: 'down'
      },
      {
        value: '+184%',
        label: 'Repeat Purchase Revenue',
        context: 'Driven by automated lifecycle retention loops',
        trend: 'up'
      }
    ],
    testimonial: {
      quote: 'Our storefront feels as elevated as our physical showrooms now. The instantaneous speed on mobile phones unlocked a surge in conversions we had been trying to achieve for over two years.',
      author: 'Kavya Patel',
      role: 'Creative Director & Founder',
      company: 'Aura Living Collection',
      verified: true
    },
    heroMediaType: 'image',
    heroImageUrl: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1600&q=80',
    galleryImages: [
      {
        url: 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1200&q=80',
        caption: 'Mobile Responsive PDP Experience',
        alt: 'Mobile layout showcasing luxury products'
      }
    ],
    beforeLabel: 'Legacy 5.4s Monolith',
    afterLabel: 'Sub-420ms Headless Storefront',
    ctaType: 'book_audit',
    ctaTitle: 'Ready to Accelerate Your Storefront Speed & Conversions?',
    ctaSubtitle: 'Request a technical speed and conversion audit to see how headless performance can multiply your sales.',
    ctaButtonText: 'Request Storefront Audit',
    ctaButtonUrl: '/contact',
    metaTitle: 'Aura Living Case Study: 4.2x Mobile Conversions | Cora Agency',
    metaDescription: 'How we rebuilt Aura Living with headless Next.js, cutting mobile page load to 420ms and scaling repeat purchase revenue +184%.',
    targetKeywords: ['ecommerce case study', 'headless nextjs agency', 'luxury brand redesign'],
    viewsCount: 2840,
    ctaClicks: 176
  },
  {
    id: 'proj_03',
    agencyId: 'super',
    title: 'Enterprise AI Knowledge Retrieval Engine for Multi-Branch Real Estate',
    slug: 'enterprise-rag-real-estate-automation',
    subtitle: 'Living AI memory layer and automated lead qualification for 1,200+ property advisors.',
    clientName: 'Stealth Realty Enterprise',
    clientIndustry: 'Real Estate / PropTech',
    projectDuration: '45-Day Sprint',
    yearCompleted: '2026',
    services: ['AI Agent Development', 'RAG Knowledge Architecture', 'CRM & Lead Automation', 'Enterprise Security'],
    liveUrl: 'https://stealthrealty.demo',
    isConfidential: true,
    isFeatured: true,
    status: 'published',
    executiveSummary: 'A national real estate network handling over 40,000 monthly inquiries struggled with slow agent response times and mismatched property recommendations. Cora deployed a localized AI Co-Founder and RAG Knowledge Base that answers complex client queries in real-time and qualifies leads with 94% precision.',
    challengeContent: 'With 1,200 brokers across 18 regional hubs, new property updates took days to circulate, leading to missed showings, lost sales commissions, and high manual inquiry fatigue among frontline agents.',
    solutionContent: 'We architected a unified AI knowledge network:\n\n1. **Living RAG Vector Store**: Ingested all active property listings, floor plans, legal encumbrances, and price metrics into a secure real-time vector memory.\n2. **Autonomous WhatsApp & Web AI Concierge**: Built an interactive conversational agent that pre-qualifies buyers, calculates GST, and reserves showings directly in the broker calendar.\n3. **Broker Copilot Workspace**: Created a mobile-first dashboard enabling brokers to generate tailored brochures and contract terms in seconds.',
    deliverables: [
      'Multi-Branch RAG Knowledge Base Architecture',
      '24/7 AI Lead Qualification & Calendar Sync Agent',
      'Real-Time Property Telemetry & Broker App',
      'Enterprise Role-Based Access Governance (RBAC)'
    ],
    kpis: [
      {
        value: '< 15s',
        label: 'Inquiry Response Time',
        context: 'Down from 4.8 hours median manual turnaround',
        trend: 'down'
      },
      {
        value: '+68%',
        label: 'Showing Attendance Rate',
        context: 'Automated WhatsApp reminder and routing flows',
        trend: 'up'
      },
      {
        value: '94%',
        label: 'Lead Qualification Accuracy',
        context: 'Verified against senior broker manual audit',
        trend: 'up'
      }
    ],
    testimonial: {
      quote: 'Deploying this AI knowledge system transformed our entire operation. Our advisors now spend their day closing high-value deals with pre-screened clients instead of digging through PDF listings.',
      author: 'Rohan Verma',
      role: 'Managing Partner',
      company: 'Confidential Real Estate Network',
      verified: true
    },
    heroMediaType: 'image',
    heroImageUrl: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1600&q=80',
    galleryImages: [
      {
        url: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80',
        caption: 'Autonomous Property Intelligence Engine',
        alt: 'Real estate portfolio management UI'
      }
    ],
    beforeLabel: 'Manual Spreadsheets & 4h Delay',
    afterLabel: 'Instant AI Dispatch & 15s Response',
    ctaType: 'book_audit',
    ctaTitle: 'Want to Deploy Custom AI Intelligence in Your Agency?',
    ctaSubtitle: 'Schedule a discovery session to evaluate how autonomous AI systems can streamline your operations.',
    ctaButtonText: 'Schedule AI Architecture Session',
    ctaButtonUrl: '/contact',
    metaTitle: 'PropTech Case Study: Real-Time RAG & AI Lead Automation | Cora Agency',
    metaDescription: 'How our enterprise AI implementation cut inquiry turnaround from 4.8 hours to under 15 seconds for 1,200+ property advisors.',
    targetKeywords: ['ai agency case study', 'rag knowledge architecture', 'enterprise automation'],
    viewsCount: 1980,
    ctaClicks: 142
  }
];

/**
/**
 * Fetch all published projects.
 * Returns SEED_PROJECTS directly during static compilation for instant zero-hang builds,
 * or queries the WordPress REST API with a tight 3s timeout when available.
 */
export async function getPublishedProjects(): Promise<ProjectCaseStudy[]> {
  // During build / SSG or when no remote URL configured, use direct SEED data
  if (process.env.NEXT_PHASE === 'phase-production-build' || !process.env.WORDPRESS_API_URL) {
    return SEED_PROJECTS.filter(p => p.status === 'published');
  }

  try {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 2500);

    const wpBase = process.env.WORDPRESS_API_URL || 'http://cora.local';
    const res = await fetch(`${wpBase}/wp-json/cora-api/v1/projects`, {
      signal: controller.signal,
      next: { revalidate: 60 }
    });
    clearTimeout(timeoutId);

    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data) && data.length > 0) {
        return data;
      }
    }
  } catch (err) {
    // Graceful fallback to static seed data
  }
  return SEED_PROJECTS.filter(p => p.status === 'published');
}

/**
 * Fetch a single project by its slug.
 */
export async function getProjectBySlug(slug: string): Promise<ProjectCaseStudy | null> {
  const match = SEED_PROJECTS.find(p => p.slug === slug);
  if (match) return match;

  const projects = await getPublishedProjects();
  return projects.find(p => p.slug === slug) || null;
}
