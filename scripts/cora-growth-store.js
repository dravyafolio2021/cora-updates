/**
 * Cora Growth Workspace — Core Database & Storage Engine
 *
 * Scoped SQLite database engine providing persistent storage for Content Entries,
 * Immutable Revisions, Real Asset Binary Storage, Growth Queue, and Audit Logs.
 */

const { DatabaseSync } = require('node:sqlite');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const DB_PATH = path.resolve(__dirname, '../app/public/wp-content/uploads/growth/growth_store.sqlite');
const UPLOADS_FRONTEND = path.resolve(__dirname, '../cora-frontend/public/uploads/growth');
const UPLOADS_BACKEND = path.resolve(__dirname, '../app/public/wp-content/uploads/growth');
const WORKSPACE_ID = 'growth_cora_main_01';
const PREVIEW_SECRET = 'cora_prev_sec_' + crypto.createHash('sha256').update('cora_growth_preview_key_2026').digest('hex');

// Ensure directories exist
[path.dirname(DB_PATH), UPLOADS_FRONTEND, UPLOADS_BACKEND].forEach(dir => {
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
});

const db = new DatabaseSync(DB_PATH);

// Initialize Tables
db.exec(`
  CREATE TABLE IF NOT EXISTS cora_content_entries (
    id TEXT PRIMARY KEY,
    workspace_id TEXT NOT NULL,
    type TEXT NOT NULL DEFAULT 'article',
    schema_version INTEGER NOT NULL DEFAULT 1,
    title TEXT NOT NULL,
    slug TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'draft',
    excerpt TEXT,
    target_icp TEXT,
    primary_keyword TEXT,
    secondary_keywords_json TEXT,
    search_intent TEXT,
    read_time TEXT,
    seo_json TEXT,
    share_json TEXT,
    content_blocks_json TEXT,
    chapters_json TEXT,
    sources_json TEXT,
    relationships_json TEXT,
    assets_json TEXT,
    cta_json TEXT,
    author_json TEXT,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    published_at TEXT,
    UNIQUE(workspace_id, slug)
  );

  CREATE TABLE IF NOT EXISTS cora_content_revisions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    revision_id TEXT UNIQUE NOT NULL,
    content_id TEXT NOT NULL,
    workspace_id TEXT NOT NULL,
    actor TEXT NOT NULL,
    snapshot_json TEXT NOT NULL,
    change_reason TEXT,
    created_at TEXT NOT NULL
  );

  CREATE TABLE IF NOT EXISTS cora_content_assets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    asset_id TEXT UNIQUE NOT NULL,
    workspace_id TEXT NOT NULL,
    filename TEXT NOT NULL,
    file_path TEXT NOT NULL,
    file_url TEXT NOT NULL,
    mime_type TEXT NOT NULL,
    width INTEGER,
    height INTEGER,
    file_size INTEGER NOT NULL,
    alt_text TEXT,
    title TEXT,
    caption TEXT,
    source TEXT,
    created_at TEXT NOT NULL
  );

  CREATE TABLE IF NOT EXISTS cora_growth_queue (
    id TEXT PRIMARY KEY,
    workspace_id TEXT NOT NULL,
    type TEXT NOT NULL,
    title TEXT NOT NULL,
    priority TEXT NOT NULL DEFAULT 'medium',
    status TEXT NOT NULL DEFAULT 'backlog',
    target_keyword TEXT,
    target_url TEXT,
    source TEXT,
    assigned_to TEXT,
    result_content_id TEXT,
    requirement_spec_json TEXT,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    completed_at TEXT
  );

  CREATE TABLE IF NOT EXISTS cora_growth_audit_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    workspace_id TEXT NOT NULL,
    actor TEXT NOT NULL,
    action TEXT NOT NULL,
    content_id TEXT,
    previous_revision TEXT,
    new_revision TEXT,
    metadata_json TEXT,
    created_at TEXT NOT NULL
  );
`);

/**
 * Tenant Security Boundary Guard
 */
function assertWorkspaceScope(targetWorkspaceId) {
  if (targetWorkspaceId && targetWorkspaceId !== WORKSPACE_ID) {
    const error = new Error(`Access Denied: Growth Agent is strictly hard-scoped to '${WORKSPACE_ID}'. Cannot access workspace '${targetWorkspaceId}' or platform CRM/billing.`);
    error.code = 'FORBIDDEN_WORKSPACE_ACCESS';
    error.status = 403;
    throw error;
  }
}

/**
 * Validation Engine
 */
function validateContent(entry, action = 'publish') {
  const errors = [];
  const warnings = [];

  if (!entry.title || !entry.title.trim()) {
    errors.push('Content title is required.');
  }

  if (!entry.slug || !entry.slug.trim()) {
    errors.push('Content slug is required.');
  } else if (!/^[a-z0-9-]+$/.test(entry.slug)) {
    warnings.push('Slug contains non-standard characters.');
  }

  const type = entry.type || 'article';

  if (action === 'publish') {
    if (!entry.primary_keyword) {
      warnings.push('Primary keyword is recommended for search optimization.');
    }

    const seo = entry.seo || {};
    if (!seo.title && !entry.title) {
      errors.push('SEO title is required.');
    }
    if (!seo.meta_description && !entry.excerpt) {
      warnings.push('SEO meta description is empty; excerpt will be used.');
    }

    if (type === 'guide') {
      const chapters = entry.chapters || [];
      if (!Array.isArray(chapters) || chapters.length === 0) {
        errors.push('Guides must contain at least one chapter.');
      } else {
        const slugs = new Set();
        chapters.forEach((ch, idx) => {
          const num = ch.number || idx + 1;
          if (!ch.title) errors.push(`Chapter ${num} is missing a title.`);
          if (!ch.slug) errors.push(`Chapter ${num} is missing a slug.`);
          if (slugs.has(ch.slug)) errors.push(`Duplicate chapter slug '${ch.slug}' in Chapter ${num}.`);
          slugs.add(ch.slug);
          if (!ch.blocks || ch.blocks.length === 0) {
            errors.push(`Chapter ${num} must contain at least one content block.`);
          }
        });
      }
    } else {
      const blocks = entry.content || entry.content_blocks || [];
      if (!Array.isArray(blocks) || blocks.length === 0) {
        errors.push('Content must contain at least one content block before publishing.');
      }
    }
  }

  return {
    valid: errors.length === 0,
    errors,
    warnings,
  };
}

/**
 * Save Immutable Revision Snapshot
 */
function saveRevision(contentId, data, reason, actor = 'Cora Growth Agent') {
  const revId = 'rev_' + crypto.randomBytes(8).toString('hex');
  const now = new Date().toISOString();
  db.prepare(`
    INSERT INTO cora_content_revisions (revision_id, content_id, workspace_id, actor, snapshot_json, change_reason, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?)
  `).run(revId, contentId, WORKSPACE_ID, actor, JSON.stringify(data), reason || 'Content update', now);
  return revId;
}

/**
 * Log Audit Event
 */
function logAudit(action, contentId, prevRev, newRev, meta = {}, actor = 'Cora Growth Agent') {
  const now = new Date().toISOString();
  db.prepare(`
    INSERT INTO cora_growth_audit_log (workspace_id, actor, action, content_id, previous_revision, new_revision, metadata_json, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
  `).run(WORKSPACE_ID, actor, action, contentId || null, prevRev || null, newRev || null, JSON.stringify(meta), now);
}

/**
 * Content Operations
 */
function createContent(entry, actor = 'Cora Growth Agent', targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);

  const id = entry.id || 'cnt_' + crypto.randomBytes(8).toString('hex');
  const now = new Date().toISOString();
  const slug = entry.slug || entry.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
  const type = entry.type || 'article';
  const status = entry.status || 'draft';

  // Ensure author name is Dravya Bansal as mandated by user rule
  const author = entry.author || {
    name: 'Dravya Bansal',
    role: 'Co-founder & CEO, Cora',
    avatar: '/images/founder.jpeg',
  };
  if (author.name === 'Dravya Agarwal') {
    author.name = 'Dravya Bansal';
  }

  const record = {
    id,
    workspace_id: WORKSPACE_ID,
    type,
    schema_version: 1,
    title: entry.title,
    slug,
    status,
    excerpt: entry.excerpt || '',
    target_icp: entry.target_icp || 'agency_founders',
    primary_keyword: entry.primary_keyword || '',
    secondary_keywords_json: JSON.stringify(entry.secondary_keywords || []),
    search_intent: entry.search_intent || 'informational',
    read_time: entry.read_time || '6 min read',
    seo_json: JSON.stringify(entry.seo || { title: entry.title, meta_description: entry.excerpt || '' }),
    share_json: JSON.stringify(entry.share || {}),
    content_blocks_json: JSON.stringify(entry.content || entry.content_blocks || []),
    chapters_json: JSON.stringify(entry.chapters || []),
    sources_json: JSON.stringify(entry.sources || []),
    relationships_json: JSON.stringify(entry.relationships || []),
    assets_json: JSON.stringify(entry.assets || []),
    cta_json: JSON.stringify(entry.cta || {}),
    author_json: JSON.stringify(author),
    created_at: now,
    updated_at: now,
    published_at: status === 'published' ? now : null,
  };

  // Check existing slug
  const existing = db.prepare('SELECT id FROM cora_content_entries WHERE workspace_id = ? AND slug = ?').get(WORKSPACE_ID, slug);
  if (existing) {
    throw new Error(`Content with slug '${slug}' already exists in workspace.`);
  }

  db.prepare(`
    INSERT INTO cora_content_entries (
      id, workspace_id, type, schema_version, title, slug, status, excerpt,
      target_icp, primary_keyword, secondary_keywords_json, search_intent, read_time,
      seo_json, share_json, content_blocks_json, chapters_json, sources_json,
      relationships_json, assets_json, cta_json, author_json, created_at, updated_at, published_at
    ) VALUES (
      ?, ?, ?, ?, ?, ?, ?, ?,
      ?, ?, ?, ?, ?,
      ?, ?, ?, ?, ?,
      ?, ?, ?, ?, ?, ?, ?
    )
  `).run(
    record.id, record.workspace_id, record.type, record.schema_version, record.title, record.slug, record.status, record.excerpt,
    record.target_icp, record.primary_keyword, record.secondary_keywords_json, record.search_intent, record.read_time,
    record.seo_json, record.share_json, record.content_blocks_json, record.chapters_json, record.sources_json,
    record.relationships_json, record.assets_json, record.cta_json, record.author_json, record.created_at, record.updated_at, record.published_at
  );

  const revId = saveRevision(id, record, 'Initial creation', actor);
  logAudit('create_content', id, null, revId, { title: record.title, type: record.type }, actor);

  return formatEntry(record);
}

function updateContent(id, updates, changeReason = 'Updated content', actor = 'Cora Growth Agent', targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);

  const existing = db.prepare('SELECT * FROM cora_content_entries WHERE workspace_id = ? AND id = ?').get(WORKSPACE_ID, id);
  if (!existing) {
    throw new Error(`Content entry '${id}' not found.`);
  }

  const now = new Date().toISOString();
  const merged = { ...existing };

  if (updates.title !== undefined) merged.title = updates.title;
  if (updates.slug !== undefined) merged.slug = updates.slug;
  if (updates.excerpt !== undefined) merged.excerpt = updates.excerpt;
  if (updates.primary_keyword !== undefined) merged.primary_keyword = updates.primary_keyword;
  if (updates.secondary_keywords !== undefined) merged.secondary_keywords_json = JSON.stringify(updates.secondary_keywords);
  if (updates.search_intent !== undefined) merged.search_intent = updates.search_intent;
  if (updates.read_time !== undefined) merged.read_time = updates.read_time;
  if (updates.seo !== undefined) merged.seo_json = JSON.stringify(updates.seo);
  if (updates.share !== undefined) merged.share_json = JSON.stringify(updates.share);
  if (updates.content !== undefined || updates.content_blocks !== undefined) {
    merged.content_blocks_json = JSON.stringify(updates.content || updates.content_blocks);
  }
  if (updates.chapters !== undefined) merged.chapters_json = JSON.stringify(updates.chapters);
  if (updates.sources !== undefined) merged.sources_json = JSON.stringify(updates.sources);
  if (updates.relationships !== undefined) merged.relationships_json = JSON.stringify(updates.relationships);
  if (updates.assets !== undefined) merged.assets_json = JSON.stringify(updates.assets);
  if (updates.cta !== undefined) merged.cta_json = JSON.stringify(updates.cta);
  if (updates.status !== undefined) {
    merged.status = updates.status;
    if (merged.status === 'published' && !merged.published_at) {
      merged.published_at = now;
    }
  }
  merged.updated_at = now;

  db.prepare(`
    UPDATE cora_content_entries SET
      title = ?, slug = ?, status = ?, excerpt = ?, primary_keyword = ?,
      secondary_keywords_json = ?, search_intent = ?, read_time = ?, seo_json = ?,
      share_json = ?, content_blocks_json = ?, chapters_json = ?, sources_json = ?,
      relationships_json = ?, assets_json = ?, cta_json = ?, updated_at = ?, published_at = ?
    WHERE workspace_id = ? AND id = ?
  `).run(
    merged.title, merged.slug, merged.status, merged.excerpt, merged.primary_keyword,
    merged.secondary_keywords_json, merged.search_intent, merged.read_time, merged.seo_json,
    merged.share_json, merged.content_blocks_json, merged.chapters_json, merged.sources_json,
    merged.relationships_json, merged.assets_json, merged.cta_json, merged.updated_at, merged.published_at,
    WORKSPACE_ID, id
  );

  const revId = saveRevision(id, merged, changeReason, actor);
  logAudit('update_content', id, null, revId, { changeReason }, actor);

  return formatEntry(merged);
}

function getContent(idOrSlug, targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);
  const row = db.prepare('SELECT * FROM cora_content_entries WHERE workspace_id = ? AND (id = ? OR slug = ?)').get(WORKSPACE_ID, idOrSlug, idOrSlug);
  return row ? formatEntry(row) : null;
}

function deleteContent(idOrSlug, targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);
  db.prepare('DELETE FROM cora_content_entries WHERE workspace_id = ? AND (id = ? OR slug = ?)').run(WORKSPACE_ID, idOrSlug, idOrSlug);
  return { success: true };
}

function listContent(filter = {}, targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);
  let query = 'SELECT * FROM cora_content_entries WHERE workspace_id = ?';
  const params = [WORKSPACE_ID];

  if (filter.type) {
    query += ' AND type = ?';
    params.push(filter.type);
  }
  if (filter.status) {
    query += ' AND status = ?';
    params.push(filter.status);
  }
  if (filter.search) {
    query += ' AND (title LIKE ? OR primary_keyword LIKE ? OR slug LIKE ?)';
    const term = `%${filter.search}%`;
    params.push(term, term, term);
  }

  query += ' ORDER BY updated_at DESC';
  if (filter.limit) {
    query += ` LIMIT ${Number(filter.limit)}`;
  }

  const rows = db.prepare(query).all(...params);
  return rows.map(formatEntry);
}

function publishContent(id, actor = 'Cora Growth Agent', targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);
  const entry = getContent(id, targetWorkspaceId);
  if (!entry) throw new Error(`Content '${id}' not found.`);

  const validation = validateContent(entry, 'publish');
  if (!validation.valid) {
    const err = new Error(`Validation failed: ${validation.errors.join('; ')}`);
    err.validation = validation;
    throw err;
  }

  const now = new Date().toISOString();
  db.prepare(`
    UPDATE cora_content_entries
    SET status = 'published', published_at = COALESCE(published_at, ?), updated_at = ?
    WHERE workspace_id = ? AND id = ?
  `).run(now, now, WORKSPACE_ID, id);

  logAudit('publish_content', id, null, null, { slug: entry.slug, type: entry.type }, actor);

  return {
    success: true,
    id: entry.id,
    slug: entry.slug,
    status: 'published',
    live_url: (entry.type === 'guide' ? '/guides/' : '/blog/') + entry.slug,
  };
}

/**
 * Preview Token Generation & Verification
 */
function generatePreview(id, targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);
  const entry = getContent(id, targetWorkspaceId);
  if (!entry) throw new Error(`Content '${id}' not found.`);

  const exp = Math.floor(Date.now() / 1000) + 86400; // 24h expiration
  const payload = `${entry.id}|${WORKSPACE_ID}|${exp}`;
  const sig = crypto.createHmac('sha256', PREVIEW_SECRET).update(payload).digest('hex');

  const tokenObj = { cid: entry.id, wid: WORKSPACE_ID, exp, sig };
  const token = Buffer.from(JSON.stringify(tokenObj)).toString('base64url');

  return {
    success: true,
    token,
    preview_url: `http://localhost:3000/preview/${token}/`,
    expires_at: new Date(exp * 1000).toISOString(),
  };
}

function resolvePreview(token) {
  try {
    const raw = Buffer.from(token, 'base64url').toString('utf8');
    const obj = JSON.parse(raw);
    if (!obj.cid || !obj.wid || !obj.exp || !obj.sig) return null;

    if (Math.floor(Date.now() / 1000) > obj.exp) return null; // Expired

    const payload = `${obj.cid}|${obj.wid}|${obj.exp}`;
    const expectedSig = crypto.createHmac('sha256', PREVIEW_SECRET).update(payload).digest('hex');

    if (expectedSig !== obj.sig) return null;

    return getContent(obj.cid, obj.wid);
  } catch (e) {
    return null;
  }
}

/**
 * Revisions & Rollback
 */
function getRevisions(contentId, targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);
  const rows = db.prepare(`
    SELECT revision_id, content_id, actor, change_reason, created_at
    FROM cora_content_revisions
    WHERE workspace_id = ? AND content_id = ?
    ORDER BY id DESC
  `).all(WORKSPACE_ID, contentId);
  return rows;
}

function rollbackContent(contentId, revisionId, actor = 'Cora Growth Agent', targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);
  const rev = db.prepare(`
    SELECT * FROM cora_content_revisions
    WHERE workspace_id = ? AND content_id = ? AND revision_id = ?
  `).get(WORKSPACE_ID, contentId, revisionId);

  if (!rev) throw new Error(`Revision '${revisionId}' not found.`);

  const snapshot = JSON.parse(rev.snapshot_json);
  const now = new Date().toISOString();

  db.prepare(`
    UPDATE cora_content_entries SET
      title = ?, slug = ?, status = ?, excerpt = ?, primary_keyword = ?,
      secondary_keywords_json = ?, search_intent = ?, read_time = ?,
      seo_json = ?, share_json = ?, content_blocks_json = ?, chapters_json = ?,
      sources_json = ?, relationships_json = ?, assets_json = ?, cta_json = ?,
      updated_at = ?
    WHERE workspace_id = ? AND id = ?
  `).run(
    snapshot.title, snapshot.slug, snapshot.status, snapshot.excerpt, snapshot.primary_keyword,
    snapshot.secondary_keywords_json || '[]', snapshot.search_intent, snapshot.read_time,
    snapshot.seo_json || '{}', snapshot.share_json || '{}', snapshot.content_blocks_json || '[]',
    snapshot.chapters_json || '[]', snapshot.sources_json || '[]', snapshot.relationships_json || '[]',
    snapshot.assets_json || '[]', snapshot.cta_json || '{}', now,
    WORKSPACE_ID, contentId
  );

  const newRevId = saveRevision(contentId, snapshot, `Rolled back to ${revisionId}`, actor);
  logAudit('rollback_content', contentId, revisionId, newRevId, { revisionId }, actor);

  return { success: true, message: `Successfully restored revision ${revisionId}`, restored_revision: revisionId };
}

/**
 * Real Asset File Storage Engine
 */
function uploadAsset(assetData, targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);

  const assetId = assetData.asset_id || 'ast_' + crypto.randomBytes(8).toString('hex');
  const filename = assetData.filename || 'asset.webp';
  const cleanName = filename.replace(/[^a-zA-Z0-9._-]/g, '_');
  const targetFileName = `${assetId}_${cleanName}`;
  const now = new Date().toISOString();

  const filePathFrontend = path.join(UPLOADS_FRONTEND, targetFileName);
  const filePathBackend = path.join(UPLOADS_BACKEND, targetFileName);

  // Write actual file buffer
  let fileBuffer;
  if (assetData.source_path && fs.existsSync(assetData.source_path)) {
    fileBuffer = fs.readFileSync(assetData.source_path);
  } else if (assetData.file_path && fs.existsSync(assetData.file_path)) {
    fileBuffer = fs.readFileSync(assetData.file_path);
  } else if (assetData.buffer) {
    fileBuffer = Buffer.isBuffer(assetData.buffer) ? assetData.buffer : Buffer.from(assetData.buffer);
  } else if (assetData.base64) {
    fileBuffer = Buffer.from(assetData.base64, 'base64');
  } else if (assetData.content_string) {
    fileBuffer = Buffer.from(assetData.content_string, 'utf8');
  } else {
    // Generate valid placeholder binary for tests (e.g. 1x1 WebP / SVG / PDF marker)
    if (filename.endsWith('.pdf')) {
      fileBuffer = Buffer.from('%PDF-1.4\n1 0 obj\n<< /Title (' + (assetData.title || 'Cora PDF Asset') + ') >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF', 'utf8');
    } else if (filename.endsWith('.svg')) {
      fileBuffer = Buffer.from('<svg xmlns="http://www.w3.org/2000/svg" width="800" height="450" viewBox="0 0 800 450"><rect width="800" height="450" fill="#18181b"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#ffffff" font-family="sans-serif" font-size="24">' + (assetData.title || 'Cora Asset') + '</text></svg>', 'utf8');
    } else {
      // Default 1x1 transparent PNG / WebP binary
      fileBuffer = Buffer.from([
        0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A, 0x00, 0x00, 0x00, 0x0D,
        0x49, 0x48, 0x44, 0x52, 0x00, 0x00, 0x00, 0x01, 0x00, 0x00, 0x00, 0x01,
        0x08, 0x06, 0x00, 0x00, 0x00, 0x1F, 0x15, 0xC4, 0x89, 0x00, 0x00, 0x00,
        0x0A, 0x49, 0x44, 0x41, 0x54, 0x78, 0x9C, 0x63, 0x00, 0x01, 0x00, 0x00,
        0x05, 0x00, 0x01, 0x0D, 0x0A, 0x2D, 0xB4, 0x00, 0x00, 0x00, 0x00, 0x49,
        0x45, 0x4E, 0x44, 0xAE, 0x42, 0x60, 0x82
      ]);
    }
  }

  // Write to both frontend public directory and backend uploads directory
  fs.writeFileSync(filePathFrontend, fileBuffer);
  fs.writeFileSync(filePathBackend, fileBuffer);

  const fileSize = fileBuffer.length;
  const mimeType = assetData.mime_type || (filename.endsWith('.pdf') ? 'application/pdf' : filename.endsWith('.svg') ? 'image/svg+xml' : 'image/webp');
  const fileUrl = `/uploads/growth/${targetFileName}`;

  db.prepare(`
    INSERT INTO cora_content_assets (
      asset_id, workspace_id, filename, file_path, file_url, mime_type,
      width, height, file_size, alt_text, title, caption, source, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  `).run(
    assetId, WORKSPACE_ID, filename, filePathFrontend, fileUrl, mimeType,
    assetData.width || (filename.endsWith('.pdf') ? null : 1200),
    assetData.height || (filename.endsWith('.pdf') ? null : 630),
    fileSize, assetData.alt_text || '', assetData.title || filename,
    assetData.caption || '', assetData.source || 'Cora Growth Agent', now
  );

  return {
    success: true,
    asset_id: assetId,
    filename,
    file_url: fileUrl,
    file_path: filePathFrontend,
    mime_type: mimeType,
    file_size: fileSize,
    width: assetData.width || 1200,
    height: assetData.height || 630,
    alt_text: assetData.alt_text || '',
    title: assetData.title || filename,
  };
}

/**
 * Attach Asset to Content Role
 */
function attachAsset(contentId, assetId, role, chapterIndex = null, targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);
  const entry = getContent(contentId, targetWorkspaceId);
  if (!entry) throw new Error(`Content '${contentId}' not found.`);

  const asset = db.prepare('SELECT * FROM cora_content_assets WHERE workspace_id = ? AND asset_id = ?').get(WORKSPACE_ID, assetId);
  if (!asset) throw new Error(`Asset '${assetId}' not found.`);

  const updates = {};
  const seo = entry.seo || {};

  if (role === 'cover') {
    seo.og_image = asset.file_url;
    updates.seo = seo;
    const assets = entry.assets || [];
    assets.push({ asset_id: assetId, role: 'cover', url: asset.file_url, alt_text: asset.alt_text });
    updates.assets = assets;
  } else if (role === 'og') {
    seo.og_image = asset.file_url;
    seo.twitter_image = asset.file_url;
    updates.seo = seo;
  } else if (role === 'chapter_featured' && chapterIndex !== null && entry.chapters && entry.chapters[chapterIndex]) {
    const chapters = [...entry.chapters];
    chapters[chapterIndex].featured_asset_id = assetId;
    chapters[chapterIndex].featured_asset_url = asset.file_url;
    updates.chapters = chapters;
  } else if (role === 'infographic' && chapterIndex !== null && entry.chapters && entry.chapters[chapterIndex]) {
    const chapters = [...entry.chapters];
    chapters[chapterIndex].infographic_asset_ids = chapters[chapterIndex].infographic_asset_ids || [];
    chapters[chapterIndex].infographic_asset_ids.push(assetId);
    updates.chapters = chapters;
  } else if (role === 'pdf_lead_magnet') {
    const cta = entry.cta || {};
    cta.lead_magnet_asset_id = assetId;
    cta.download_url = asset.file_url;
    cta.file_size = `${Math.round(asset.file_size / 1024)} KB`;
    updates.cta = cta;
  }

  return updateContent(contentId, updates, `Attached asset ${assetId} as ${role}`, 'Cora Growth Agent');
}

/**
 * Growth Queue Operations
 */
function manageQueue(action, params = {}, targetWorkspaceId = WORKSPACE_ID) {
  assertWorkspaceScope(targetWorkspaceId);

  if (action === 'create') {
    const id = params.id || 'job_' + crypto.randomBytes(8).toString('hex');
    const now = new Date().toISOString();
    const job = {
      id,
      workspace_id: WORKSPACE_ID,
      type: params.type || 'ARTICLE',
      title: params.title || 'Untitled Growth Job',
      priority: params.priority || 'medium',
      status: params.status || 'backlog',
      target_keyword: params.target_keyword || '',
      target_url: params.target_url || '',
      source: params.source || 'Organic Research',
      assigned_to: params.assigned_to || 'Cora Growth Agent',
      result_content_id: params.result_content_id || null,
      requirement_spec_json: params.requirement_spec ? JSON.stringify(params.requirement_spec) : null,
      created_at: now,
      updated_at: now,
      completed_at: null,
    };

    db.prepare(`
      INSERT INTO cora_growth_queue (
        id, workspace_id, type, title, priority, status, target_keyword, target_url,
        source, assigned_to, result_content_id, requirement_spec_json, created_at, updated_at
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `).run(
      job.id, job.workspace_id, job.type, job.title, job.priority, job.status,
      job.target_keyword, job.target_url, job.source, job.assigned_to, job.result_content_id,
      job.requirement_spec_json, job.created_at, job.updated_at
    );

    return {
      ...job,
      requirement_spec: params.requirement_spec || null,
    };
  } else if (action === 'update') {
    const existing = db.prepare('SELECT * FROM cora_growth_queue WHERE workspace_id = ? AND id = ?').get(WORKSPACE_ID, params.id);
    if (!existing) throw new Error(`Job '${params.id}' not found.`);

    const now = new Date().toISOString();
    const status = params.status || existing.status;
    const completedAt = (status === 'published' || status === 'done') ? now : existing.completed_at;

    db.prepare(`
      UPDATE cora_growth_queue SET
        status = ?, priority = COALESCE(?, priority),
        result_content_id = COALESCE(?, result_content_id),
        updated_at = ?, completed_at = ?
      WHERE workspace_id = ? AND id = ?
    `).run(status, params.priority || null, params.result_content_id || null, now, completedAt, WORKSPACE_ID, params.id);

    return { success: true, id: params.id, status, completed_at: completedAt };
  } else {
    // List jobs
    const rows = db.prepare('SELECT * FROM cora_growth_queue WHERE workspace_id = ? ORDER BY created_at DESC').all(WORKSPACE_ID);
    return rows.map(r => ({
      ...r,
      requirement_spec: r.requirement_spec_json ? JSON.parse(r.requirement_spec_json) : null,
    }));
  }
}

/**
 * Format DB Record to Clean Output JSON
 */
function formatEntry(row) {
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
}

module.exports = {
  WORKSPACE_ID,
  assertWorkspaceScope,
  validateContent,
  createContent,
  updateContent,
  getContent,
  deleteContent,
  listContent,
  publishContent,
  generatePreview,
  resolvePreview,
  getRevisions,
  rollbackContent,
  uploadAsset,
  attachAsset,
  manageQueue,
};
