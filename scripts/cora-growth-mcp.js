#!/usr/bin/env node
/**
 * Cora Growth Workspace — Scoped MCP Server
 *
 * Exposes scoped Growth Control Plane tools (growth.*) for authorized Growth Agents.
 * Enforces strict workspace tenancy isolation (growth_workspace) and prevents access to
 * customer CRM, invoices, billing, or global platform admin.
 */

const readline = require('readline');
const store = require('./cora-growth-store');

const WORKSPACE_ID = store.WORKSPACE_ID;

/**
 * Registered MCP Tool Handlers
 */
const TOOLS = {
  // 1. List Content
  'growth.list_content': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    return store.listContent({
      type: args.type,
      status: args.status,
      search: args.search,
      limit: args.limit,
    }, args.workspace_id);
  },

  // 2. Get Single Content
  'growth.get_content': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    if (!args.id_or_slug) throw new Error('Argument id_or_slug is required.');
    const content = store.getContent(args.id_or_slug, args.workspace_id);
    if (!content) throw new Error(`Content '${args.id_or_slug}' not found.`);
    return content;
  },

  // 3. Search Existing Content
  'growth.search_content': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    return store.listContent({
      search: args.query || args.search || '',
      type: args.type,
    }, args.workspace_id);
  },

  // 4. Create Article
  'growth.create_article': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    const author = args.author || {
      name: 'Dravya Bansal',
      role: 'Co-founder & CEO, Cora',
      avatar: '/images/founder.jpeg',
    };
    if (author.name === 'Dravya Agarwal') {
      author.name = 'Dravya Bansal';
    }

    return store.createContent({
      id: args.id,
      type: 'article',
      title: args.title,
      slug: args.slug,
      excerpt: args.excerpt,
      primary_keyword: args.primary_keyword,
      secondary_keywords: args.secondary_keywords || [],
      search_intent: args.search_intent || 'informational',
      read_time: args.read_time || '6 min read',
      content: args.content_blocks || args.content || [],
      sources: args.sources || [],
      relationships: args.relationships || [],
      seo: args.seo || { title: args.title, meta_description: args.excerpt || '' },
      cta: args.cta || {},
      status: args.status || 'draft',
      author,
    }, 'Cora Growth Agent', args.workspace_id);
  },

  // 5. Update Article
  'growth.update_article': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    if (!args.id) throw new Error('Article ID is required.');
    return store.updateContent(args.id, args, args.change_reason || 'Updated article', 'Cora Growth Agent', args.workspace_id);
  },

  // 6. Create Guide
  'growth.create_guide': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    const author = args.author || {
      name: 'Dravya Bansal',
      role: 'Co-founder & CEO, Cora',
      avatar: '/images/founder.jpeg',
    };
    if (author.name === 'Dravya Agarwal') {
      author.name = 'Dravya Bansal';
    }

    return store.createContent({
      id: args.id,
      type: 'guide',
      title: args.title,
      slug: args.slug,
      excerpt: args.excerpt,
      primary_keyword: args.primary_keyword,
      secondary_keywords: args.secondary_keywords || [],
      search_intent: args.search_intent || 'commercial_informational',
      read_time: args.read_time || '18 min read',
      chapters: args.chapters || [],
      sources: args.sources || [],
      relationships: args.relationships || [],
      seo: args.seo || { title: args.title, meta_description: args.excerpt || '' },
      cta: args.cta || {},
      status: args.status || 'draft',
      author,
    }, 'Cora Growth Agent', args.workspace_id);
  },

  // 7. Update Guide
  'growth.update_guide': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    if (!args.id) throw new Error('Guide ID is required.');
    return store.updateContent(args.id, args, args.change_reason || 'Updated guide', 'Cora Growth Agent', args.workspace_id);
  },

  // 8. Upload Real Asset (Stores file to disk)
  'growth.upload_asset': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    if (!args.filename) throw new Error('Filename is required.');
    return store.uploadAsset(args, args.workspace_id);
  },

  // 9. Attach Asset
  'growth.attach_asset': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    if (!args.content_id || !args.asset_id || !args.role) {
      throw new Error('content_id, asset_id, and role are required.');
    }
    return store.attachAsset(args.content_id, args.asset_id, args.role, args.chapter_index ?? null, args.workspace_id);
  },

  // 10. Pre-publish Validation
  'growth.validate': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    if (!args.id) throw new Error('Content ID is required.');
    const entry = store.getContent(args.id, args.workspace_id);
    if (!entry) throw new Error(`Content '${args.id}' not found.`);
    return store.validateContent(entry, 'publish');
  },

  // 11. Generate Preview Link
  'growth.preview': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    if (!args.id) throw new Error('Content ID is required.');
    return store.generatePreview(args.id, args.workspace_id);
  },

  // 12. Publish Content
  'growth.publish': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    if (!args.id) throw new Error('Content ID is required.');
    return store.publishContent(args.id, 'Cora Growth Agent', args.workspace_id);
  },

  // 13. Revisions & Rollback
  'growth.get_revisions': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    if (!args.id) throw new Error('Content ID is required.');
    return store.getRevisions(args.id, args.workspace_id);
  },

  'growth.rollback': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    if (!args.id || !args.revision_id) throw new Error('Content id and revision_id are required.');
    return store.rollbackContent(args.id, args.revision_id, 'Cora Growth Agent', args.workspace_id);
  },

  // 14. Performance Analytics (GA4/GSC read-only connector)
  'growth.get_performance': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    return {
      status: 'NOT VERIFIED — requires production credentials',
      workspace_id: WORKSPACE_ID,
      content_id: args.content_id || 'all',
      note: 'Search Console and GA4 read-only connector is active in staging; local mock fallback active without live service account key.',
      summary: {
        impressions: 0,
        clicks: 0,
        avg_ctr: 0.0,
        avg_position: 0.0,
        sessions: 0,
        conversions: 0,
      },
    };
  },

  // 15. Search Opportunities
  'growth.get_search_opportunities': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    return {
      status: 'NOT VERIFIED — requires production credentials',
      opportunities: [],
    };
  },

  // 16. Manage Growth Queue
  'growth.manage_queue': async (args) => {
    store.assertWorkspaceScope(args.workspace_id);
    return store.manageQueue(args.action || 'list', args, args.workspace_id);
  },
};

/**
 * List tool definitions
 */
function getToolDefinitions() {
  return [
    {
      name: 'growth.list_content',
      description: 'List content entries in the Cora Growth Workspace with optional type, status, or search filters.',
      inputSchema: {
        type: 'object',
        properties: {
          type: { type: 'string', description: 'article, guide, landing_page, tool_page, etc.' },
          status: { type: 'string', description: 'draft, review, published, archived' },
          search: { type: 'string', description: 'Keyword search query' },
          limit: { type: 'number', description: 'Maximum number of records to return' },
        },
      },
    },
    {
      name: 'growth.get_content',
      description: 'Get full content payload including structured blocks and chapters by ID or slug.',
      inputSchema: {
        type: 'object',
        properties: {
          id_or_slug: { type: 'string', description: 'Content ID (cnt_...) or slug' },
        },
        required: ['id_or_slug'],
      },
    },
    {
      name: 'growth.create_article',
      description: 'Create a new strategic article in the Cora Growth Workspace.',
      inputSchema: {
        type: 'object',
        properties: {
          title: { type: 'string' },
          slug: { type: 'string' },
          excerpt: { type: 'string' },
          primary_keyword: { type: 'string' },
          secondary_keywords: { type: 'array', items: { type: 'string' } },
          search_intent: { type: 'string' },
          content_blocks: { type: 'array', description: 'Array of structured JSON blocks' },
          sources: { type: 'array' },
          seo: { type: 'object' },
        },
        required: ['title', 'content_blocks'],
      },
    },
    {
      name: 'growth.create_guide',
      description: 'Create a chaptered pillar guide with infographics, auto TOC, and lead magnets.',
      inputSchema: {
        type: 'object',
        properties: {
          title: { type: 'string' },
          slug: { type: 'string' },
          excerpt: { type: 'string' },
          primary_keyword: { type: 'string' },
          chapters: { type: 'array', description: 'Array of structured chapters with blocks & featured assets' },
          sources: { type: 'array' },
          seo: { type: 'object' },
        },
        required: ['title', 'chapters'],
      },
    },
    {
      name: 'growth.upload_asset',
      description: 'Upload a real file (JPG, PNG, WebP, SVG, PDF) to disk storage and register metadata.',
      inputSchema: {
        type: 'object',
        properties: {
          filename: { type: 'string' },
          mime_type: { type: 'string' },
          alt_text: { type: 'string' },
          title: { type: 'string' },
          caption: { type: 'string' },
          base64: { type: 'string' },
        },
        required: ['filename'],
      },
    },
    {
      name: 'growth.attach_asset',
      description: 'Attach a stored asset to a content role (cover, og, chapter_featured, infographic, pdf_lead_magnet).',
      inputSchema: {
        type: 'object',
        properties: {
          content_id: { type: 'string' },
          asset_id: { type: 'string' },
          role: { type: 'string', enum: ['cover', 'og', 'chapter_featured', 'infographic', 'pdf_lead_magnet'] },
          chapter_index: { type: 'number' },
        },
        required: ['content_id', 'asset_id', 'role'],
      },
    },
    {
      name: 'growth.validate',
      description: 'Run pre-publish validation on content and return errors/warnings.',
      inputSchema: {
        type: 'object',
        properties: {
          id: { type: 'string', description: 'Content ID' },
        },
        required: ['id'],
      },
    },
    {
      name: 'growth.preview',
      description: 'Generate a signed 24h temporary Next.js preview URL for unpublished content.',
      inputSchema: {
        type: 'object',
        properties: {
          id: { type: 'string', description: 'Content ID' },
        },
        required: ['id'],
      },
    },
    {
      name: 'growth.publish',
      description: 'Validate, publish content entry and trigger Next.js on-demand ISR revalidation.',
      inputSchema: {
        type: 'object',
        properties: {
          id: { type: 'string', description: 'Content ID' },
        },
        required: ['id'],
      },
    },
    {
      name: 'growth.get_revisions',
      description: 'Get revision history for a content entry.',
      inputSchema: {
        type: 'object',
        properties: {
          id: { type: 'string' },
        },
        required: ['id'],
      },
    },
    {
      name: 'growth.rollback',
      description: 'Roll back a content entry to a specific revision snapshot.',
      inputSchema: {
        type: 'object',
        properties: {
          id: { type: 'string' },
          revision_id: { type: 'string' },
        },
        required: ['id', 'revision_id'],
      },
    },
    {
      name: 'growth.get_performance',
      description: 'Get read-only Search Console & GA4 organic performance metrics.',
      inputSchema: {
        type: 'object',
        properties: {
          content_id: { type: 'string' },
        },
      },
    },
    {
      name: 'growth.manage_queue',
      description: 'List, create, or advance tasks in the organic Growth Queue.',
      inputSchema: {
        type: 'object',
        properties: {
          action: { type: 'string', enum: ['list', 'create', 'update'] },
          id: { type: 'string' },
          type: { type: 'string' },
          title: { type: 'string' },
          status: { type: 'string' },
          requirement_spec: { type: 'object' },
        },
      },
    },
  ];
}

/**
 * Standard JSON-RPC stdin/stdout interface for MCP
 */
function startStdioServer() {
  const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
    terminal: false,
  });

  rl.on('line', async (line) => {
    if (!line.trim()) return;

    try {
      const msg = JSON.parse(line);
      const { id, method, params } = msg;

      if (method === 'tools/list') {
        const response = {
          jsonrpc: '2.0',
          id,
          result: { tools: getToolDefinitions() },
        };
        process.stdout.write(JSON.stringify(response) + '\n');
      } else if (method === 'tools/call') {
        const toolName = params?.name;
        const toolArgs = params?.arguments || {};
        const handler = TOOLS[toolName];

        if (!handler) {
          const errResponse = {
            jsonrpc: '2.0',
            id,
            error: { code: -32601, message: `Unknown tool: ${toolName}` },
          };
          process.stdout.write(JSON.stringify(errResponse) + '\n');
          return;
        }

        const result = await handler(toolArgs);
        const response = {
          jsonrpc: '2.0',
          id,
          result: { content: [{ type: 'text', text: JSON.stringify(result, null, 2) }] },
        };
        process.stdout.write(JSON.stringify(response) + '\n');
      } else if (method === 'initialize') {
        const response = {
          jsonrpc: '2.0',
          id,
          result: {
            protocolVersion: '2024-11-05',
            serverInfo: { name: 'cora-growth-mcp', version: '1.0.0' },
            capabilities: { tools: {} },
          },
        };
        process.stdout.write(JSON.stringify(response) + '\n');
      }
    } catch (err) {
      const errResponse = {
        jsonrpc: '2.0',
        id: null,
        error: { code: -32700, message: err.message },
      };
      process.stdout.write(JSON.stringify(errResponse) + '\n');
    }
  });
}

// Start stdio listener if invoked directly
if (require.main === module) {
  startStdioServer();
}

module.exports = { TOOLS, getToolDefinitions, store };
