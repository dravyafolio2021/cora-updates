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

export function getContentFromDb(idOrSlug: string, workspaceId = WORKSPACE_ID) {
  const db = getDb();
  if (!db) return null;
  try {
    const row = db.prepare('SELECT * FROM cora_content_entries WHERE workspace_id = ? AND (id = ? OR slug = ?)').get(workspaceId, idOrSlug, idOrSlug);
    if (!row) return null;

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
      secondary_keywords: row.secondary_keywords_json ? JSON.parse(row.secondary_keywords_json) : [],
      search_intent: row.search_intent,
      read_time: row.read_time || '6 min read',
      seo: row.seo_json ? JSON.parse(row.seo_json) : {},
      share: row.share_json ? JSON.parse(row.share_json) : {},
      content: row.content_blocks_json ? JSON.parse(row.content_blocks_json) : [],
      chapters: row.chapters_json ? JSON.parse(row.chapters_json) : [],
      sources: row.sources_json ? JSON.parse(row.sources_json) : [],
      relationships: row.relationships_json ? JSON.parse(row.relationships_json) : [],
      assets: row.assets_json ? JSON.parse(row.assets_json) : [],
      cta: row.cta_json ? JSON.parse(row.cta_json) : {},
      author: row.author_json ? JSON.parse(row.author_json) : {},
      created_at: row.created_at,
      updated_at: row.updated_at,
      published_at: row.published_at,
      public_url: (row.type === 'guide' ? '/guides/' : '/blog/') + row.slug,
    };
  } catch (e) {
    return null;
  }
}

export function listContentFromDb(filter: any = {}, workspaceId = WORKSPACE_ID) {
  const db = getDb();
  if (!db) return [];
  try {
    let query = 'SELECT * FROM cora_content_entries WHERE workspace_id = ?';
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
    return rows.map((r: any) => ({
      id: r.id,
      workspace_id: r.workspace_id,
      type: r.type,
      schema_version: r.schema_version,
      title: r.title,
      slug: r.slug,
      status: r.status,
      excerpt: r.excerpt,
      target_icp: r.target_icp,
      primary_keyword: r.primary_keyword,
      secondary_keywords: r.secondary_keywords_json ? JSON.parse(r.secondary_keywords_json) : [],
      search_intent: r.search_intent,
      read_time: r.read_time || '6 min read',
      seo: r.seo_json ? JSON.parse(r.seo_json) : {},
      share: r.share_json ? JSON.parse(r.share_json) : {},
      content: r.content_blocks_json ? JSON.parse(r.content_blocks_json) : [],
      chapters: r.chapters_json ? JSON.parse(r.chapters_json) : [],
      sources: r.sources_json ? JSON.parse(r.sources_json) : [],
      relationships: r.relationships_json ? JSON.parse(r.relationships_json) : [],
      assets: r.assets_json ? JSON.parse(r.assets_json) : [],
      cta: r.cta_json ? JSON.parse(r.cta_json) : {},
      author: r.author_json ? JSON.parse(r.author_json) : {},
      created_at: r.created_at,
      updated_at: r.updated_at,
      published_at: r.published_at,
      public_url: (r.type === 'guide' ? '/guides/' : '/blog/') + r.slug,
    }));
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
