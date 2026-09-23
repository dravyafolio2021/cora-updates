/**
 * Cora Editorial System — Phase 2: Guides & Playbooks Data Architecture
 * Chaptered, High-Authority Operating Playbooks + Lead Magnets
 */

import {
  BlogAuthor,
  BlogCategoryId,
  ArticleEditorialStatus,
  ArticleSource,
  EditorialBlock,
  BLOG_AUTHORS,
  BLOG_CATEGORIES,
} from '@/lib/blog-data';

export interface GuideChapter {
  number: string; // e.g., '01', '02', '03'
  slug: string;   // Anchor ID e.g., 'why-onboarding-breaks'
  title: string;
  summary?: string;
  readTime?: string;
  blocks: EditorialBlock[];
}

export interface DownloadableAsset {
  assetId: string;
  title: string;
  description: string;
  fileUrl: string; // Dynamic resolver path e.g., '/api/guides/download/agency-onboarding-pack'
  fileType: 'pdf' | 'zip' | 'docx' | 'xlsx' | 'template';
  fileSize?: string;
  thumbnail?: string;
  ctaText?: string;
  highlights: string[];
}

export interface Guide {
  slug: string;
  status: ArticleEditorialStatus; // 'draft' | 'review' | 'published'
  title: string;
  dek: string; // Editorial summary / subtitle
  excerpt: string;
  coverImage: string;
  coverAlt: string;
  ogImage?: string;
  ogImageAlt?: string;
  shareTitle?: string;
  shareDescription?: string;
  shareText?: string;
  author: BlogAuthor;
  publishedAt: string; // ISO format: YYYY-MM-DD
  updatedAt: string;
  category: BlogCategoryId;
  qualityLabel: string;
  tags: string[];
  readTime: string;
  chapterCount: number;
  featured?: boolean;
  canonicalUrl: string;
  seoTitle: string;
  seoDescription: string;
  robots?: string;
  sources?: ArticleSource[];
  faqs?: { question: string; answer: string }[];
  downloadableAsset?: DownloadableAsset;
  relatedArticles?: string[]; // Slugs from BLOG_ARTICLES
  relatedTools?: { title: string; href: string; badge: string; description: string }[];
  relatedGuides?: string[];   // Slugs from GUIDES_DATA
  chapters: GuideChapter[];
}

/* ====================================================================
 * LAUNCH GUIDES REGISTRY (INITIAL PLACEHOLDER IN REVIEW)
 * ==================================================================== */

export const GUIDES_DATA: Guide[] = [
  {
    slug: 'agency-client-onboarding-playbook',
    status: 'review', // In review — excluded from public sitemaps and search indexing until approved
    title: 'The Agency Client Onboarding Playbook: From Verbal Yes to Active Retainer in 48 Hours',
    dek: 'A complete chaptered operational playbook for creative studios, software firms, and marketing agencies to eliminate kickoff friction, lock scope boundaries, and turn new deals into high-retention client relationships.',
    excerpt: 'The complete agency client onboarding playbook: 6 chapters on contract architecture, asset intake, kickoff rituals, and milestone governance.',
    coverImage: '/images/guides/agency-client-onboarding-playbook-cover.webp',
    coverAlt: 'The Agency Client Onboarding Playbook Cover Banner',
    ogImage: '/images/guides/agency-client-onboarding-playbook-og.webp',
    ogImageAlt: 'The Agency Client Onboarding Playbook Social Preview',
    shareTitle: 'The Agency Client Onboarding Playbook: 48-Hour Kickoff System',
    shareDescription: 'Complete chaptered operational playbook for creative and technical service agencies. Eliminate kickoff delays and protect project margins.',
    shareText: 'Read the comprehensive Agency Client Onboarding Playbook by Cora — from verbal agreement to active retainer in 48 hours:',
    author: BLOG_AUTHORS['dravya-bansal'],
    publishedAt: '2026-09-23',
    updatedAt: '2026-09-23',
    category: 'agency-operations',
    qualityLabel: 'Operational Playbook',
    tags: ['Client Onboarding', 'Agency Operations', 'Standard Operating Procedures', 'Contract Governance', 'Client Retention'],
    readTime: '18 min read',
    chapterCount: 6,
    featured: true,
    canonicalUrl: 'https://heycora.in/guides/agency-client-onboarding-playbook/',
    seoTitle: 'Agency Client Onboarding Playbook: 48-Hour Kickoff Operating System (2026)',
    seoDescription: 'The definitive agency client onboarding playbook. 6 chapters covering digital agreements, asset intake checklists, kickoff meetings, and milestone dispatch.',
    downloadableAsset: {
      assetId: 'agency-onboarding-pack',
      title: 'The Complete Agency Client Onboarding Pack',
      description: 'The production-tested SOP pack used by modern service firms to eliminate onboarding lag and protect project margins.',
      fileUrl: '/api/guides/download/agency-onboarding-pack',
      fileType: 'zip',
      fileSize: '4.8 MB',
      ctaText: 'Download Complete Pack (ZIP + Notion + PDF)',
      highlights: [
        'Client Intake & Brand Asset Checklist (Notion Template)',
        'Kickoff Meeting Agenda & Alignment Slide Deck',
        'Scope Boundary & Revision Cap Contract Clauses',
        '30-Day Project Roadmap & Handoff Schedule Template',
      ],
    },
    relatedArticles: [
      'agency-client-onboarding-process',
      'how-to-reduce-agency-scope-creep',
      'client-reporting-system-for-agencies',
    ],
    relatedTools: [
      {
        title: 'Agency Proposal Generator',
        href: '/tools/agency-proposal-generator/',
        badge: 'Interactive Tool',
        description: 'Generate itemized project scopes, milestone timelines, and GST tax breakdowns in 90 seconds.',
      },
      {
        title: 'Cora E-Sign Vault',
        href: '/features/esign-vault/',
        badge: 'Legal Tech',
        description: 'Digital agreements with SHA-256 audit trails and 1-click client sign-offs.',
      },
    ],
    relatedGuides: [],
    sources: [
      {
        title: 'Standard Operating Procedures for Service Operations',
        publisher: 'Cora Operational Standards',
        url: 'https://heycora.in/about/',
      },
    ],
    faqs: [
      {
        question: 'Who is this onboarding playbook designed for?',
        answer: 'This playbook is specifically tailored for founders, directors, and operations leads at creative studios, web agencies, software consultancies, and marketing service firms managing multiple ongoing client engagements.',
      },
      {
        question: 'How quickly can an agency implement this 48-hour onboarding workflow?',
        answer: 'Using the provided checklists and digital agreement templates, an agency can transition from ad-hoc email kickoff to the 5-phase structured onboarding sequence in less than one business week.',
      },
    ],
    chapters: [
      {
        number: '01',
        slug: 'why-client-onboarding-breaks',
        title: 'Why Client Onboarding Breaks at Scale',
        summary: 'How multi-tool fragmentation and ad-hoc communication create friction right when client trust is being formed.',
        readTime: '3 min',
        blocks: [
          {
            type: 'intro',
            content: 'When an agency grows past its first five clients, informal onboarding practices immediately collapse. What worked when founders handled every email personally turns into a chaotic scramble across email threads, WhatsApp messages, and forgotten file requests.',
          },
          {
            type: 'statement',
            statement: 'Onboarding is not an administrative chore. It is the foundation of client retention.',
            subtext: 'The first 72 hours of an engagement dictate whether a client views your team as strategic partners or disorganized contractors.',
          },
          {
            type: 'callout',
            variant: 'warning',
            title: 'The Multi-Tool Handoff Gap',
            content: 'When clients must navigate separate portals for contracts, cloud drives for files, chat apps for updates, and banks for payments, they experience immediate buyer anxiety.',
          },
        ],
      },
      {
        number: '02',
        slug: 'defining-airtight-scope-boundaries',
        title: 'Defining Airtight Scope Boundaries Before Kickoff',
        summary: 'Structuring revision caps, milestone triggers, and out-of-scope change order terms.',
        readTime: '3 min',
        blocks: [
          {
            type: 'text',
            content: 'Before any work begins, the agreement must clearly distinguish between core deliverables and potential change orders. Without explicit revision caps and exclusions, scope drift begins on day one.',
          },
          {
            type: 'checklist',
            title: 'Mandatory Agreement Safeguards',
            items: [
              { label: 'Explicit revision limits per milestone (e.g. 2 rounds included)', description: 'Additional rounds billed at agreed sprint rate.', checked: true },
              { label: '7-day silence-as-approval clause for submitted deliverables', description: 'Prevents projects from idling indefinitely in client review.', checked: true },
              { label: 'Explicit exclusion list (media spend, stock licenses, domain fees)', description: 'Eliminates unexpected commercial disputes.', checked: true },
            ],
          },
        ],
      },
      {
        number: '03',
        slug: 'structured-asset-and-access-intake',
        title: 'Structured Asset & Access Intake',
        summary: 'Replacing scattered chat messages with a unified prerequisite intake checklist.',
        readTime: '3 min',
        blocks: [
          {
            type: 'text',
            content: 'High-performing teams never start sprint production until all prerequisite brand assets, credentials, and partner permissions are verified.',
          },
          {
            type: 'comparison',
            title: 'Intake Workflow Comparison',
            leftHeader: 'Ad-Hoc Asset Gathering',
            rightHeader: 'Structured Intake Protocol',
            rows: [
              { label: 'Access Sharing', left: 'Passwords sent across unsecured chats', right: 'Partner delegation via Meta MCC / Google Workspace' },
              { label: 'Brand Guidelines', left: 'Low-res PNGs and scattered PDF decks', right: 'Single vector package verified against requirements' },
              { label: 'Stakeholder Roles', left: 'Undefined feedback authority', right: 'Designated primary approval owner' },
            ],
          },
        ],
      },
      {
        number: '04',
        slug: 'the-kickoff-alignment-ritual',
        title: 'The Kickoff Alignment Ritual',
        summary: 'Conducting an efficient 30-minute kickoff meeting that establishes momentum.',
        readTime: '3 min',
        blocks: [
          {
            type: 'steps',
            orientation: 'vertical',
            steps: [
              { number: '01', title: 'Review Agreed Scope & Success Metrics', description: 'Reiterate the primary objective and business outcomes.' },
              { number: '02', title: 'Establish Weekly Reporting Cadence', description: 'Confirm Friday milestone updates and feedback expectations.' },
              { number: '03', title: 'Present Sprint 01 Deliverable Timeline', description: 'Walk through the initial deliverables schedule and owner assignments.' },
            ],
          },
        ],
      },
      {
        number: '05',
        slug: 'first-delivery-momentum-and-approvals',
        title: 'First Delivery Momentum & Approval Protocol',
        summary: 'Securing early client validation to build long-term confidence.',
        readTime: '3 min',
        blocks: [
          {
            type: 'keyTakeaway',
            principle: 'The First Milestone Rule',
            description: 'Deliver an early, tangible milestone within the first 7 business days. Early momentum cements client confidence and eliminates second-guessing.',
          },
          {
            type: 'productMention',
            contextText: 'To keep agreements, intake checklists, approvals, and milestone invoices connected in one place:',
            actionText: 'See how Cora unifies agency client operations →',
            actionHref: '/features/lead-crm/',
          },
        ],
      },
      {
        number: '06',
        slug: 'the-48-hour-operating-checklist',
        title: 'The 48-Hour Implementation Checklist',
        summary: 'Your step-by-step master checklist to deploy this system across your next client engagement.',
        readTime: '3 min',
        blocks: [
          {
            type: 'checklist',
            title: 'Master 48-Hour Onboarding Sequence',
            items: [
              { label: 'Hour 0-2: Digital agreement issued with GST breakdown', description: 'Includes scope terms and 1-click client sign-off.', checked: true },
              { label: 'Hour 2-12: Mobilization advance invoice settled', description: 'CGST/SGST split with dynamic payment confirmation.', checked: true },
              { label: 'Hour 12-24: Central intake checklist completed by client', description: 'Brand assets and platform access verified.', checked: true },
              { label: 'Hour 24-48: Kickoff alignment call and Sprint 01 task dispatch', description: 'Internal team assigned and client notified.', checked: true },
            ],
          },
        ],
      },
    ],
  },
];

/* ====================================================================
 * QUERY HELPERS
 * ==================================================================== */

/**
 * Returns all guides (filters out draft/review unless explicitly requested)
 */
export function getAllGuides(includeUnpublished = false): Guide[] {
  if (includeUnpublished) return GUIDES_DATA;
  return GUIDES_DATA.filter((g) => g.status === 'published');
}

/**
 * Returns the featured guide (published only unless includeUnpublished is true)
 */
export function getFeaturedGuide(includeUnpublished = false): Guide | undefined {
  const guides = getAllGuides(includeUnpublished);
  return guides.find((g) => g.featured) || guides[0];
}

/**
 * Returns a guide by slug
 */
export function getGuideBySlug(slug: string, includeUnpublished = false): Guide | undefined {
  return GUIDES_DATA.find((g) => {
    if (g.slug !== slug) return false;
    if (!includeUnpublished && g.status !== 'published') return false;
    return true;
  });
}

/**
 * Returns guides belonging to a specific category
 */
export function getGuidesByCategory(category: BlogCategoryId, includeUnpublished = false): Guide[] {
  return getAllGuides(includeUnpublished).filter((g) => g.category === category);
}

/**
 * Returns all guide slugs for static path generation
 */
export function getAllGuideSlugs(includeUnpublished = false): string[] {
  return getAllGuides(includeUnpublished).map((g) => g.slug);
}
