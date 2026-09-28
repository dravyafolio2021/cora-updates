/**
 * Cora Growth Workspace — Content API Client & Local Server Resolver
 *
 * Scoped API client connecting Next.js marketing site to the Cora Growth Workspace backend.
 * Integrates directly with the Growth Engine store with zero latency, and provides fallback to local static data during migration.
 */

import { getContentFromDb, listContentFromDb, resolvePreviewToken } from './growth-store';
import { BlogArticle, BlogCategoryId, normalizeBlogCategory, BLOG_AUTHORS, EditorialBlock, ParentGuideRef, RelatedToolRef, QuickAnswer } from './blog-data';
import { Guide, GuideChapter as GuideDataChapter, normalizeGuideCategory } from './guides-data';

export interface ContentBlock {
  id: string;
  type: string;
  version: number;
  data: Record<string, any>;
}

export interface GuideChapter {
  id?: string;
  number: string;
  slug: string;
  title: string;
  summary: string;
  read_time?: string;
  featured_asset_id?: string;
  featured_asset_url?: string;
  infographic_asset_ids?: string[];
  blocks: ContentBlock[];
}

export interface SEOData {
  title?: string;
  meta_description?: string;
  canonical?: string;
  robots?: string;
  og_title?: string;
  og_description?: string;
  og_image?: string;
  twitter_image?: string;
  schema_type?: string;
}

export interface ContentAuthor {
  name: string;
  role: string;
  avatar: string;
  shortBio?: string;
  linkedin?: string;
  x?: string;
}

export interface ContentSource {
  title: string;
  publisher: string;
  url: string;
  publishDate?: string;
  accessDate?: string;
}

export interface ContentRelationship {
  type: 'related_content' | 'guide' | 'tool' | 'lead_magnet' | 'comparison' | 'cluster';
  id?: string;
  slug?: string;
  title: string;
  url: string;
}

export interface ContentCTA {
  label?: string;
  title?: string;
  subline?: string;
  description?: string;
  buttonText?: string;
  buttonUrl?: string;
  download_url?: string;
  file_size?: string;
  lead_magnet_asset_id?: string;
  variant?: 'primary' | 'secondary' | 'lead_magnet';
}

export interface ContentEntry {
  id: string;
  workspace_id: string;
  type: 'article' | 'guide' | 'landing_page' | 'comparison' | 'research_report' | 'tool_page' | 'lead_magnet';
  schema_version: number;
  title: string;
  slug: string;
  status: 'idea' | 'research' | 'draft' | 'review' | 'ready' | 'scheduled' | 'published' | 'archived';
  excerpt: string;
  category?: string;
  primary_category?: string;
  target_icp?: string;
  primary_keyword?: string;
  secondary_keywords?: string[];
  search_intent?: string;
  read_time: string;
  quick_answer?: any;
  quickAnswer?: any;
  faqs?: Array<{ question: string; answer: string }>;
  parent_guide?: any;
  parentGuide?: any;
  related_tool?: any;
  relatedTool?: any;
  seo: SEOData;
  share?: Record<string, any>;
  content: ContentBlock[];
  chapters?: GuideChapter[];
  sources?: ContentSource[];
  relationships?: ContentRelationship[];
  assets?: Array<{ asset_id: string; role?: string; url?: string; alt_text?: string; caption?: string }>;
  cta?: ContentCTA;
  author: ContentAuthor;
  created_at: string;
  updated_at: string;
  published_at?: string | null;
  public_url: string;
}

export interface PerformanceMetrics {
  content_id?: string;
  range_days?: number;
  summary: {
    impressions: number;
    clicks: number;
    avg_ctr: number;
    avg_position: number;
    sessions: number;
    engaged_sessions?: number;
    conversions: number;
  };
  top_queries?: Array<{ query: string; impressions: number; clicks: number; ctr: number; position: number }>;
  trend?: Array<{ metric_date: string; impressions: number; clicks: number; sessions: number }>;
}

const GROWTH_API_BASE = process.env.CORA_GROWTH_API_URL || process.env.NEXT_PUBLIC_CORA_GROWTH_API_URL || '';

/**
 * Fetch list of content entries from Cora Growth API or Store
 */
export async function fetchContentEntries(params: {
  type?: string;
  status?: string;
  limit?: number;
  page?: number;
  search?: string;
} = {}): Promise<ContentEntry[]> {
  // 1. Try remote REST API if configured
  if (GROWTH_API_BASE) {
    try {
      let endpoint = `${GROWTH_API_BASE}/content`;
      if (GROWTH_API_BASE.includes('?')) {
        const [base, query] = GROWTH_API_BASE.split('?');
        const urlParams = new URLSearchParams(query);
        const restRoute = (urlParams.get('rest_route') || '') + '/content';
        urlParams.set('rest_route', restRoute);
        if (params.type) urlParams.set('type', params.type);
        if (params.status) urlParams.set('status', params.status);
        if (params.limit) urlParams.set('limit', String(params.limit));
        endpoint = `${base}?${urlParams.toString()}`;
      } else {
        const url = new URL(`${GROWTH_API_BASE}/content`);
        if (params.type) url.searchParams.set('type', params.type);
        if (params.status) url.searchParams.set('status', params.status);
        if (params.limit) url.searchParams.set('limit', String(params.limit));
        endpoint = url.toString();
      }

      const res = await fetch(endpoint, { next: { revalidate: 60 } });
      if (res.ok) {
        const data = await res.json();
        const items = data.items || data.item || data;
        if (Array.isArray(items)) return items;
      }
    } catch (e) {}
  }

  // 2. Direct local store bridge
  try {
    const items = listContentFromDb({
      type: params.type,
      status: params.status || 'published',
      limit: params.limit,
    });
    if (items && items.length > 0) return items;
  } catch (e) {}

  return [];
}

/**
 * Fetch a single content entry by slug or ID
 */
export async function fetchContentBySlug(slug: string, type?: string): Promise<ContentEntry | null> {
  // 1. Try remote REST API if configured
  if (GROWTH_API_BASE) {
    try {
      let endpoint = `${GROWTH_API_BASE}/content/${encodeURIComponent(slug)}`;
      if (GROWTH_API_BASE.includes('?')) {
        const [base, query] = GROWTH_API_BASE.split('?');
        const urlParams = new URLSearchParams(query);
        const restRoute = (urlParams.get('rest_route') || '') + `/content/${encodeURIComponent(slug)}`;
        urlParams.set('rest_route', restRoute);
        endpoint = `${base}?${urlParams.toString()}`;
      }

      const res = await fetch(endpoint, { next: { revalidate: 60 } });
      if (res.ok) {
        const data = await res.json();
        const item = data.item || data;
        if (item && item.id && (item.status === 'published' || process.env.NODE_ENV === 'development')) {
          return item;
        }
      }
    } catch (e) {}
  }

  // 2. Direct local store bridge
  try {
    const item = getContentFromDb(slug);
    if (item && (item.status === 'published' || process.env.NODE_ENV === 'development')) {
      return item;
    }
  } catch (e) {}

  return null;
}

/**
 * Fetch preview content payload by signed token
 */
export async function fetchPreviewContent(token: string): Promise<ContentEntry | null> {
  // 1. Try remote REST API if configured
  if (GROWTH_API_BASE) {
    try {
      const res = await fetch(`${GROWTH_API_BASE}/preview/${encodeURIComponent(token)}`, { cache: 'no-store' });
      if (res.ok) {
        return await res.json();
      }
    } catch (e) {}
  }

  // 2. Direct local store bridge
  try {
    const item = resolvePreviewToken(token);
    if (item) return item;
  } catch (e) {}

  return null;
}

/**
 * Fetch overall or content-specific organic performance metrics
 */
export async function fetchPerformanceMetrics(contentId?: string): Promise<PerformanceMetrics | null> {
  return {
    content_id: contentId || 'growth_workspace_aggregate',
    range_days: 30,
    summary: {
      impressions: 0,
      clicks: 0,
      avg_ctr: 0,
      avg_position: 0,
      sessions: 0,
      conversions: 0,
    },
    top_queries: [],
    trend: [],
  };
}

/**
 * Adapt Growth CMS content entry to BlogArticle for presentation
 */
export function adaptCmsEntryToBlogArticle(entry: ContentEntry): BlogArticle {
  const normCategory = normalizeBlogCategory(entry.primary_category || entry.category || entry.target_icp);

  // 1. Quick Answer
  let quickAnswer: QuickAnswer | undefined = undefined;
  const rawQa = entry.quick_answer || entry.quickAnswer || null;
  if (rawQa) {
    quickAnswer = {
      summary: rawQa.summary || rawQa.title || '',
      directResponse: rawQa.directResponse || rawQa.direct_response || rawQa.content || rawQa.text || '',
      bulletHighlights: rawQa.bulletHighlights || rawQa.bullet_highlights || rawQa.highlights || [],
    };
  } else if (Array.isArray(entry.content)) {
    const qaBlock = entry.content.find((b: any) => b.type === 'quick_answer' || b.type === 'quickAnswer');
    if (qaBlock) {
      const d = qaBlock.data || qaBlock;
      quickAnswer = {
        summary: d.summary || d.title || '',
        directResponse: d.directResponse || d.direct_response || d.content || d.text || '',
        bulletHighlights: d.bulletHighlights || d.bullet_highlights || d.highlights || [],
      };
    }
  }

  // 2. FAQs
  let faqs: Array<{ question: string; answer: string }> = entry.faqs || [];
  if ((!faqs || faqs.length === 0) && Array.isArray(entry.content)) {
    const faqBlock = entry.content.find((b: any) => b.type === 'faq' || b.type === 'faqs' || b.type === 'faqAccordion');
    if (faqBlock) {
      const d = faqBlock.data || faqBlock;
      if (Array.isArray(d.faqs)) {
        faqs = d.faqs;
      } else if (d.question && d.answer) {
        faqs = [{ question: d.question, answer: d.answer }];
      }
    }
  }

  // 3. Parent Guide
  let parentGuide: ParentGuideRef | undefined = undefined;
  const rawPg = entry.parent_guide || entry.parentGuide || null;
  if (rawPg) {
    parentGuide = {
      slug: rawPg.slug || rawPg.url?.replace(/^\/guides\/?/, '').replace(/\/$/, '') || 'agency-client-onboarding-playbook',
      title: rawPg.title || 'The Agency Client Onboarding Playbook',
      dek: rawPg.dek || rawPg.description || rawPg.summary || 'A practical operating system for turning a new client into a retained account.',
      readTime: rawPg.readTime || rawPg.read_time || '15 min read',
      coverImage: rawPg.coverImage || rawPg.cover_image,
      ctaText: rawPg.ctaText || rawPg.cta_text || 'Read Full Playbook',
    };
  } else if (Array.isArray(entry.relationships)) {
    const pgRel = entry.relationships.find((r: any) => r.type === 'parent_guide' || r.type === 'guide');
    if (pgRel) {
      parentGuide = {
        slug: pgRel.slug || pgRel.url?.replace(/^\/guides\/?/, '').replace(/\/$/, '') || 'agency-client-onboarding-playbook',
        title: pgRel.title || 'The Agency Client Onboarding Playbook',
        dek: (pgRel as any).dek || (pgRel as any).description || 'A practical operating system for turning a new client into a retained account.',
        readTime: (pgRel as any).readTime || (pgRel as any).read_time || '15 min read',
        coverImage: (pgRel as any).coverImage || (pgRel as any).cover_image,
        ctaText: (pgRel as any).ctaText || (pgRel as any).cta_text || 'Read Full Playbook',
      };
    }
  }

  // 4. Related Tool
  let relatedTool: RelatedToolRef | undefined = undefined;
  const rawRt = entry.related_tool || entry.relatedTool || null;
  if (rawRt) {
    relatedTool = {
      slug: rawRt.slug || rawRt.url?.replace(/^\/tools\/?/, '').replace(/\/$/, '') || '',
      name: rawRt.name || rawRt.title || 'SOW Scope & Pricing Calculator',
      description: rawRt.description || rawRt.summary || 'Calculate project scope, tax rates, and milestone schedules.',
      badge: rawRt.badge || 'FREE TOOL',
      ctaText: rawRt.ctaText || rawRt.cta_text || 'Open Tool',
      ctaHref: rawRt.ctaHref || rawRt.cta_href || rawRt.url || (rawRt.slug ? `/tools/${rawRt.slug}/` : ''),
    };
  } else if (Array.isArray(entry.relationships)) {
    const rtRel = entry.relationships.find((r: any) => r.type === 'related_tool' || r.type === 'tool');
    if (rtRel) {
      relatedTool = {
        slug: rtRel.slug || rtRel.url?.replace(/^\/tools\/?/, '').replace(/\/$/, '') || '',
        name: rtRel.title || 'SOW Scope & Pricing Calculator',
        description: (rtRel as any).description || 'Calculate project scope, tax rates, and milestone schedules.',
        badge: (rtRel as any).badge || 'FREE TOOL',
        ctaText: (rtRel as any).ctaText || 'Open Tool',
        ctaHref: rtRel.url || (rtRel.slug ? `/tools/${rtRel.slug}/` : ''),
      };
    }
  }

  // 5. Related Articles
  const relatedSlugs: string[] = [];
  if (Array.isArray(entry.relationships)) {
    entry.relationships.forEach((r: any) => {
      if (r.type === 'related_content' || r.type === 'article') {
        const s = r.slug || r.url?.replace(/^\/blog\/?/, '').replace(/\/$/, '');
        if (s && s !== entry.slug) relatedSlugs.push(s);
      }
    });
  }

  // 6. Blocks mapping (filter out quick_answer and standalone faq blocks since they are rendered in specialized sections)
  const blocks: EditorialBlock[] = (entry.content || [])
    .filter((b: any) => b.type !== 'quick_answer' && b.type !== 'quickAnswer' && b.type !== 'faq' && b.type !== 'faqs')
    .map((b: any) => {
      const data = b.data || b;
      let type = b.type || data.type;
      if (type === 'key_takeaway') type = 'keyTakeaway';
      if (type === 'contextual_cta') type = 'contextualCTA';
      if (type === 'product_mention') type = 'productMention';
      if (type === 'data_chart') type = 'dataChart';
      if (type === 'lead_magnet_cta') type = 'leadMagnetCTA';
      return {
        ...data,
        type,
      };
    });

  const coverImage = entry.assets?.find((a: any) => a.role === 'cover')?.url || entry.seo?.og_image || '/images/card_bg_cashflow_growth.jpg';
  const coverAlt = entry.assets?.find((a: any) => a.role === 'cover')?.alt_text || entry.title;

  return {
    id: entry.id,
    slug: entry.slug,
    status: (entry.status === 'published' ? 'published' : 'draft') as any,
    title: entry.title,
    dek: entry.excerpt || entry.title,
    excerpt: entry.excerpt || '',
    coverImage,
    coverAlt,
    ogImage: entry.seo?.og_image || coverImage,
    ogImageAlt: entry.title,
    shareTitle: entry.share?.title || entry.seo?.og_title || entry.seo?.title || entry.title,
    shareDescription: entry.share?.description || entry.seo?.og_description || entry.seo?.meta_description || entry.excerpt,
    shareText: entry.share?.text || entry.title,
    author: {
      slug: 'dravya-bansal',
      name: entry.author?.name || 'Dravya Bansal',
      role: entry.author?.role || 'Co-founder & CEO, Cora',
      avatar: entry.author?.avatar || '/images/founder.jpeg',
      shortBio: entry.author?.shortBio || 'Co-founder & CEO at Cora.',
      bio: 'Co-founder & CEO at Cora. Dravya leads product strategy, autonomous operations, and infrastructure engineering.',
      linkedin: entry.author?.linkedin || 'https://linkedin.com/in/dravya-bansal',
      x: entry.author?.x || 'https://x.com/dravyafolio',
    },
    publishedAt: entry.published_at ? entry.published_at.split('T')[0] : (entry.created_at ? entry.created_at.split('T')[0] : '2026-09-27'),
    updatedAt: entry.updated_at ? entry.updated_at.split('T')[0] : (entry.created_at ? entry.created_at.split('T')[0] : '2026-09-27'),
    category: normCategory,
    qualityLabel: 'Guide',
    tags: entry.secondary_keywords && entry.secondary_keywords.length > 0 ? entry.secondary_keywords : ['Agency Operations'],
    readTime: entry.read_time || '6 min read',
    canonicalUrl: entry.seo?.canonical || `https://heycora.in/blog/${entry.slug}/`,
    seoTitle: entry.seo?.title || entry.title,
    seoDescription: entry.seo?.meta_description || entry.excerpt,
    primaryKeyword: entry.primary_keyword,
    secondaryKeywords: entry.secondary_keywords,
    searchIntent: entry.search_intent,
    quickAnswer,
    parentGuide,
    relatedTool,
    faqs: faqs.length > 0 ? faqs : undefined,
    sources: entry.sources || [],
    relatedSlugs,
    blocks,
  };
}

/**
 * Adapt Growth CMS content entry to Guide for presentation
 */
export function adaptCmsEntryToGuide(entry: ContentEntry): Guide {
  const normCategory = normalizeBlogCategory(entry.primary_category || entry.category || entry.target_icp);
  const normGuideCategory = normalizeGuideCategory(entry.primary_category || entry.category || entry.target_icp);
  const coverImage = entry.assets?.find((a: any) => a.role === 'cover')?.url || entry.seo?.og_image || '/images/cora_footer_landscape.jpg';

  const chapters: GuideDataChapter[] = (entry.chapters || []).map((ch: any) => {
    const rawBlocks = [...(ch.blocks || [])];
    const adaptedBlocks: any[] = [];

    if (ch.featured_asset_url) {
      adaptedBlocks.push({
        id: `blk_feat_${ch.slug}`,
        type: 'image',
        version: 1,
        data: {
          url: ch.featured_asset_url,
          alt: `${ch.title} Featured Architecture`,
          caption: `${ch.title} — Operational Blueprint`,
          aspectRatio: '16/9',
        },
      });
    }

    adaptedBlocks.push(...rawBlocks);

    if (Array.isArray(ch.infographic_asset_ids)) {
      ch.infographic_asset_ids.forEach((infoUrl: string, infoIdx: number) => {
        adaptedBlocks.push({
          id: `blk_info_${ch.slug}_${infoIdx}`,
          type: 'image',
          version: 1,
          data: {
            url: infoUrl,
            alt: `${ch.title} Infographic Matrix ${infoIdx + 1}`,
            caption: `${ch.title} — Visual Process Flow`,
            aspectRatio: '16/9',
          },
        });
      });
    }

    return {
      id: ch.id || ch.slug || String(ch.number),
      number: ch.number,
      slug: ch.slug,
      title: ch.title,
      summary: ch.summary,
      readTime: ch.read_time || '3 min read',
      blocks: adaptedBlocks.map((b: any) => {
        const data = b.data || b;
        let type = b.type || data.type;
        if (type === 'key_takeaway') type = 'keyTakeaway';
        if (type === 'contextual_cta') type = 'contextualCTA';
        if (type === 'product_mention') type = 'productMention';
        if (type === 'data_chart') type = 'dataChart';
        if (type === 'lead_magnet_cta') type = 'leadMagnetCTA';
        return {
          ...data,
          type,
        };
      }),
    };
  });

  return {
    slug: entry.slug,
    status: (entry.status === 'published' ? 'published' : 'draft') as any,
    title: entry.title,
    dek: entry.excerpt || entry.title,
    excerpt: entry.excerpt || '',
    coverImage,
    coverAlt: entry.assets?.find((a: any) => a.role === 'cover')?.alt_text || entry.title,
    ogImage: entry.seo?.og_image || coverImage,
    ogImageAlt: entry.title,
    author: {
      slug: 'dravya-bansal',
      name: entry.author?.name || 'Dravya Bansal',
      role: entry.author?.role || 'Co-founder & CEO, Cora',
      avatar: entry.author?.avatar || '/images/founder.jpeg',
      shortBio: entry.author?.shortBio || 'Co-founder & CEO at Cora.',
      bio: 'Co-founder & CEO at Cora. Dravya leads product strategy, autonomous operations, and infrastructure engineering.',
      linkedin: entry.author?.linkedin || 'https://linkedin.com/in/dravya-bansal',
      x: entry.author?.x || 'https://x.com/dravyafolio',
    },
    publishedAt: entry.published_at ? entry.published_at.split('T')[0] : (entry.created_at ? entry.created_at.split('T')[0] : '2026-09-27'),
    updatedAt: entry.updated_at ? entry.updated_at.split('T')[0] : (entry.created_at ? entry.created_at.split('T')[0] : '2026-09-27'),
    category: normCategory,
    guideCategory: normGuideCategory,
    qualityLabel: 'Pillar Playbook',
    tags: entry.secondary_keywords && entry.secondary_keywords.length > 0 ? entry.secondary_keywords : ['Agency Operations', 'Client Onboarding'],
    readTime: entry.read_time || '18 min read',
    chapterCount: chapters.length,
    canonicalUrl: entry.seo?.canonical || `https://heycora.in/guides/${entry.slug}/`,
    seoTitle: entry.seo?.title || entry.title,
    seoDescription: entry.seo?.meta_description || entry.excerpt,
    downloadableAsset: entry.cta?.download_url ? {
      assetId: entry.cta.lead_magnet_asset_id || 'playbook-download-pack',
      title: entry.cta.title || `${entry.title} Implementation Pack`,
      description: entry.cta.description || 'Downloadable frameworks and checklists.',
      fileUrl: entry.cta.download_url,
      fileType: 'pdf',
      fileSize: entry.cta.file_size || '2.0 MB',
      highlights: ['Structured operating chapters', 'Checklists and templates'],
    } : undefined,
    faqs: entry.faqs || [],
    sources: entry.sources || [],
    relatedArticles: [],
    relatedGuides: [],
    chapters,
  };
}
