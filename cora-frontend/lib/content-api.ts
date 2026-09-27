/**
 * Cora Growth Workspace — Content API Client & Local Server Resolver
 *
 * Scoped API client connecting Next.js marketing site to the Cora Growth Workspace backend.
 * Integrates directly with the Growth Engine store with zero latency, and provides fallback to local static data during migration.
 */

import { getContentFromDb, listContentFromDb, resolvePreviewToken } from './growth-store';

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
  target_icp?: string;
  primary_keyword?: string;
  secondary_keywords?: string[];
  search_intent?: string;
  read_time: string;
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
