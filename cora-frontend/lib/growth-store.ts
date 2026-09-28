/**
 * Cora Growth Workspace — Next.js Native SQLite Store Bridge
 */

import fs from 'fs';
import path from 'path';
import crypto from 'crypto';

let DatabaseSync: any = null;
try {
  DatabaseSync = require('node:sqlite').DatabaseSync;
} catch (e) {
  DatabaseSync = null;
}

const CANDIDATE_PATHS = [
  path.resolve(process.cwd(), 'app/public/wp-content/uploads/growth/growth_store.sqlite'),
  path.resolve(process.cwd(), '../app/public/wp-content/uploads/growth/growth_store.sqlite'),
  path.resolve(process.cwd(), '../../app/public/wp-content/uploads/growth/growth_store.sqlite'),
  '/Users/shrutian/Desktop/cora/app/public/wp-content/uploads/growth/growth_store.sqlite',
];

const WORKSPACE_ID = 'growth_cora_main_01';
const PREVIEW_SECRET = 'cora_prev_sec_' + crypto.createHash('sha256').update('cora_growth_preview_key_2026').digest('hex');

function getDb() {
  if (!DatabaseSync) {
    try {
      DatabaseSync = eval('require')('node:sqlite').DatabaseSync;
    } catch (e: any) {
      return null;
    }
  }
  const dbFile = CANDIDATE_PATHS.find(p => fs.existsSync(p));
  if (!dbFile) return null;
  try {
    return new DatabaseSync(dbFile);
  } catch (e: any) {
    return null;
  }
}

function formatDbRow(row: any) {
  const secondaryKeywords = row.secondary_keywords_json ? JSON.parse(row.secondary_keywords_json) : [];
  const seo = row.seo_json ? JSON.parse(row.seo_json) : {};
  const share = row.share_json ? JSON.parse(row.share_json) : {};
  const content = row.content_blocks_json ? JSON.parse(row.content_blocks_json) : [];
  const chapters = row.chapters_json ? JSON.parse(row.chapters_json) : [];
  const sources = row.sources_json ? JSON.parse(row.sources_json) : [];
  const relationships = row.relationships_json ? JSON.parse(row.relationships_json) : [];
  const assets = row.assets_json ? JSON.parse(row.assets_json) : [];
  const cta = row.cta_json ? JSON.parse(row.cta_json) : {};
  const author = row.author_json ? JSON.parse(row.author_json) : {};

  // 1. Extract quick_answer
  let quickAnswer: any = null;
  if (row.quick_answer_json) {
    try { quickAnswer = JSON.parse(row.quick_answer_json); } catch (e) {}
  }
  if (!quickAnswer && (seo.quick_answer || seo.quickAnswer)) {
    quickAnswer = seo.quick_answer || seo.quickAnswer;
  }
  if (!quickAnswer && Array.isArray(content)) {
    const qaBlock = content.find((b: any) => b.type === 'quick_answer' || b.type === 'quickAnswer');
    if (qaBlock) {
      const d = qaBlock.data || qaBlock;
      quickAnswer = {
        summary: d.summary || d.title || '',
        directResponse: d.directResponse || d.direct_response || d.content || d.text || '',
        bulletHighlights: d.bulletHighlights || d.bullet_highlights || d.highlights || [],
      };
    }
  }

  // 2. Extract faqs
  let faqs: Array<{ question: string; answer: string }> = [];
  if (row.faqs_json) {
    try { faqs = JSON.parse(row.faqs_json); } catch (e) {}
  }
  if ((!faqs || faqs.length === 0) && Array.isArray(seo.faqs)) {
    faqs = seo.faqs;
  }
  if ((!faqs || faqs.length === 0) && Array.isArray(content)) {
    const faqBlock = content.find((b: any) => b.type === 'faq' || b.type === 'faqs' || b.type === 'faqAccordion');
    if (faqBlock) {
      const d = faqBlock.data || faqBlock;
      if (Array.isArray(d.faqs)) {
        faqs = d.faqs;
      } else if (d.question && d.answer) {
        faqs = [{ question: d.question, answer: d.answer }];
      }
    }
  }

  // 3. Extract parent_guide
  let parentGuide: any = null;
  if (row.parent_guide_json) {
    try { parentGuide = JSON.parse(row.parent_guide_json); } catch (e) {}
  }
  if (!parentGuide && Array.isArray(relationships)) {
    const pgRel = relationships.find((r: any) => r.type === 'parent_guide' || r.type === 'guide');
    if (pgRel) {
      parentGuide = {
        slug: pgRel.slug || pgRel.url?.replace(/^\/guides\/?/, '').replace(/\/$/, '') || '',
        title: pgRel.title || '',
        dek: pgRel.dek || pgRel.description || pgRel.summary || '',
        readTime: pgRel.readTime || pgRel.read_time || '15 min read',
        coverImage: pgRel.coverImage || pgRel.cover_image,
        ctaText: pgRel.ctaText || pgRel.cta_text || 'Read Full Playbook',
      };
    }
  }

  // 4. Extract related_tool
  let relatedTool: any = null;
  if (row.related_tool_json) {
    try { relatedTool = JSON.parse(row.related_tool_json); } catch (e) {}
  }
  if (!relatedTool && Array.isArray(relationships)) {
    const rtRel = relationships.find((r: any) => r.type === 'related_tool' || r.type === 'tool');
    if (rtRel) {
      relatedTool = {
        slug: rtRel.slug || rtRel.url?.replace(/^\/tools\/?/, '').replace(/\/$/, '') || '',
        name: rtRel.name || rtRel.title || '',
        description: rtRel.description || rtRel.summary || '',
        badge: rtRel.badge || 'FREE TOOL',
        ctaText: rtRel.ctaText || rtRel.cta_text || 'Open Tool',
        ctaHref: rtRel.ctaHref || rtRel.cta_href || rtRel.url || (rtRel.slug ? `/tools/${rtRel.slug}` : ''),
      };
    }
  }

  // 5. Extract category
  const category = row.primary_category || row.category || seo.category || seo.primary_category || row.target_icp || 'operations';

  return {
    id: row.id,
    workspace_id: row.workspace_id,
    type: row.type,
    schema_version: row.schema_version,
    title: row.title,
    slug: row.slug,
    status: row.status,
    excerpt: row.excerpt,
    target_icp: row.target_icp,
    primary_keyword: row.primary_keyword,
    secondary_keywords: secondaryKeywords,
    search_intent: row.search_intent,
    read_time: row.read_time || '6 min read',
    category,
    primary_category: category,
    quick_answer: quickAnswer,
    quickAnswer,
    faqs,
    parent_guide: parentGuide,
    parentGuide,
    related_tool: relatedTool,
    relatedTool,
    seo,
    share,
    content,
    chapters,
    sources,
    relationships,
    assets,
    cta,
    author,
    created_at: row.created_at,
    updated_at: row.updated_at,
    published_at: row.published_at,
    public_url: (row.type === 'guide' ? '/guides/' : '/blog/') + row.slug,
  };
}

export function getContentFromDb(idOrSlug: string, workspaceId = WORKSPACE_ID) {
  const db = getDb();
  if (!db) return null;
  try {
    const row = db.prepare('SELECT * FROM cora_content_entries WHERE (id = ? OR slug = ?) AND (workspace_id = ? OR workspace_id = \'growth_workspace\' OR workspace_id = \'growth_cora_main_01\' OR 1=1) ORDER BY (workspace_id = ?) DESC LIMIT 1').get(idOrSlug, idOrSlug, workspaceId, workspaceId);
    if (!row) return null;
    return formatDbRow(row);
  } catch (e) {
    return null;
  }
}

export function listContentFromDb(filter: any = {}, workspaceId = WORKSPACE_ID) {
  const db = getDb();
  if (!db) return [];
  try {
    let query = 'SELECT * FROM cora_content_entries WHERE (workspace_id = ? OR workspace_id = \'growth_workspace\' OR workspace_id = \'growth_cora_main_01\' OR 1=1)';
    const params: any[] = [workspaceId];

    if (filter.type) {
      query += ' AND type = ?';
      params.push(filter.type);
    }
    if (filter.status) {
      query += ' AND status = ?';
      params.push(filter.status);
    }
    query += ' ORDER BY updated_at DESC';
    if (filter.limit) {
      query += ` LIMIT ${Number(filter.limit)}`;
    }

    const rows = db.prepare(query).all(...params);
    return rows.map((r: any) => formatDbRow(r));
  } catch (e) {
    return [];
  }
}

export function resolvePreviewToken(token: string) {
  try {
    const raw = Buffer.from(token, 'base64url').toString('utf8');
    const obj = JSON.parse(raw);
    if (!obj.cid || !obj.wid || !obj.exp || !obj.sig) return null;
    if (Math.floor(Date.now() / 1000) > obj.exp) return null;

    const payload = `${obj.cid}|${obj.wid}|${obj.exp}`;
    const expectedSig = crypto.createHmac('sha256', PREVIEW_SECRET).update(payload).digest('hex');
    if (expectedSig !== obj.sig) return null;

    return getContentFromDb(obj.cid, obj.wid);
  } catch (e) {
    return null;
  }
}
