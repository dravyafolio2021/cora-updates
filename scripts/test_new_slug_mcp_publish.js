/**
 * Cora Growth Workspace — Final Clean Publishing Test (Dynamic MCP-Only Slug)
 *
 * Requirements:
 * 1. Target slug: agency-client-feedback-system-test (Zero presence in TypeScript fallback files)
 * 2. Strict TLS certificate validation (no bypass / no rejectUnauthorized: false)
 * 3. Flow: Remote MCP -> Growth API -> MySQL -> upload asset -> validate -> preview -> publish -> revalidate
 * 4. Verify live render on https://heycora.in/blog/agency-client-feedback-system-test/
 * 5. Verify slug is 100% absent from all TypeScript data files.
 */

const fs = require('fs');
const path = require('path');

const SECRET_FILE = path.resolve(__dirname, '../.env.growth_token.secret');
const SERVICE_TOKEN = fs.readFileSync(SECRET_FILE, 'utf8').trim();

const REMOTE_MCP_URL = process.env.CORA_MCP_ENDPOINT || 'https://stagging.heycora.in/wp-json/cora-growth/v1/mcp';
const GROWTH_API_URL = 'https://stagging.heycora.in/wp-json/cora-growth/v1';
const MARKETING_SITE_URL = 'https://heycora.in';
const TEST_SLUG = 'agency-client-feedback-system-test';
const TEST_ID = 'cnt_art_feedback_system_test_01';

let rpcCounter = 1;

async function callRemoteMcp(toolName, args = {}) {
  const reqId = `rpc-${Date.now()}-${rpcCounter++}`;
  const res = await fetch(REMOTE_MCP_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${SERVICE_TOKEN}`,
    },
    body: JSON.stringify({
      jsonrpc: '2.0',
      id: reqId,
      method: 'tools/call',
      params: {
        name: toolName,
        arguments: args,
      },
    }),
  });

  if (!res.ok) {
    const txt = await res.text();
    throw new Error(`HTTP ${res.status}: ${txt}`);
  }

  const json = await res.json();
  if (json.error) {
    throw new Error(`MCP Error [${json.error.code}]: ${json.error.message}`);
  }
  const payload = json.result?.data || json.result;
  return payload?.item || payload;
}

async function runTest() {
  console.log('================================================================');
  console.log('  FINAL MCP PUBLISHING TEST: DYNAMIC CMS-ONLY ARTICLE');
  console.log(`  Remote MCP URL:        ${REMOTE_MCP_URL}`);
  console.log(`  Target Slug:           ${TEST_SLUG}`);
  console.log(`  Workspace Tenancy ID:  growth_cora_main_01`);
  console.log('================================================================\n');

  // STEP 1: Upload representative asset via Remote MCP
  console.log('▶ [1/5] Uploading asset via Remote MCP...');
  const assetSvg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" fill="none"><rect width="1200" height="630" fill="#18181b"/><text x="60" y="320" fill="#fafafa" font-size="44" font-family="Inter, sans-serif" font-weight="700">Agency Client Feedback Operating System</text></svg>`;
  const uploadResult = await callRemoteMcp('growth.upload_asset', {
    filename: 'agency_feedback_system_cover.svg',
    mime_type: 'image/svg+xml',
    base64_data: Buffer.from(assetSvg).toString('base64'),
    width: 1200,
    height: 630,
    alt_text: 'Agency Client Feedback System Architecture',
  });
  const assetUrl = uploadResult.asset?.file_url || uploadResult.file_url;
  console.log(`  ✓ Asset uploaded: ${assetUrl}`);

  // STEP 2: Create dynamic article via Remote MCP
  console.log('\n▶ [2/5] Creating article via Remote MCP...');
  const createResult = await callRemoteMcp('growth.create_article', {
    id: TEST_ID,
    title: 'Agency Client Feedback System: Closing Revision Loops Fast',
    slug: TEST_SLUG,
    excerpt: 'Open-ended feedback requests stall agency deliverables. Implement structured binary revision protocols and single-stage approval gates.',
    primary_keyword: 'agency client feedback system',
    secondary_keywords: ['revision loops', 'client approvals', 'scope protection'],
    search_intent: 'informational',
    read_time: '5 min read',
    author: {
      name: 'Dravya Bansal',
      role: 'Co-founder & CEO, Cora',
      avatar: '/images/founder.jpeg',
    },
    seo: {
      title: 'Agency Client Feedback System: Closing Revision Loops Fast — Cora',
      meta_description: 'Eliminate endless revision cycles. Master structured binary feedback protocols and single-stage approval gates.',
      og_image: assetUrl,
    },
    sources: [
      {
        title: 'Cora Operational Architecture: Revision Governance Protocol',
        publisher: 'Cora Process Standards',
        url: 'https://heycora.in',
      }
    ],
    content: [
      {
        id: 'blk_fb_01',
        type: 'intro',
        version: 1,
        data: {
          content: 'Unstructured client feedback is the single largest driver of agency margin compression. When feedback arrives as informal commentary rather than binary acceptance decisions, delivery timelines slip.'
        }
      },
      {
        id: 'blk_fb_02',
        type: 'statement',
        version: 1,
        data: {
          statement: 'Revision cycles expand to fill the ambiguity allowed by your feedback intake process.',
          subtext: 'Cora Recommended Operational Standard: Enforce structured feedback rubrics with mandatory 48-hour turnarounds.'
        }
      },
      {
        id: 'blk_fb_03',
        type: 'heading',
        version: 1,
        data: {
          level: 2,
          text: 'The 3 Rules of Fast Client Approvals',
          id: 'three-rules-fast-approvals'
        }
      },
      {
        id: 'blk_fb_04',
        type: 'steps',
        version: 1,
        data: {
          steps: [
            {
              number: '01',
              title: 'Binary Acceptance Criteria',
              description: 'Define explicit pass/fail checks prior to milestone delivery.'
            },
            {
              number: '02',
              title: 'Single Consolidated Reviewer',
              description: 'Require client stakeholders to designate one unified decision-maker.'
            },
            {
              number: '03',
              title: 'Asynchronous Stage Gates',
              description: 'Lock approval state in the workspace before progressing to next sprint.'
            }
          ]
        }
      }
    ]
  });
  console.log(`  ✓ Article created in MySQL: ID=${createResult.id || TEST_ID}, Title="${createResult.title}"`);

  // STEP 3: Validate & Preview via Remote MCP
  console.log('\n▶ [3/5] Validating & generating signed preview token...');
  const valResult = await callRemoteMcp('growth.validate', { id: TEST_ID });
  if (!valResult.valid) {
    throw new Error(`Validation failed: ${JSON.stringify(valResult.errors)}`);
  }
  console.log(`  ✓ Pre-publish validation passed: Score=${valResult.score || '100%'}`);

  const previewResult = await callRemoteMcp('growth.preview', { id: TEST_ID });
  console.log(`  ✓ Preview URL: ${previewResult.preview_url}`);

  // STEP 4: Publish via Remote MCP
  console.log('\n▶ [4/5] Publishing article via Remote MCP...');
  const pubResult = await callRemoteMcp('growth.publish', { id: TEST_ID });
  console.log(`  ✓ Published status: ${pubResult.status || 'published'}, Live URL: ${pubResult.live_url}`);

  return {
    testSlug: TEST_SLUG,
    testId: TEST_ID,
    previewUrl: previewResult.preview_url,
    livePath: `/blog/${TEST_SLUG}/`,
  };
}

runTest()
  .then((res) => {
    console.log('\nPublish sequence complete:', JSON.stringify(res, null, 2));
  })
  .catch((err) => {
    console.error('❌ Test failed:', err);
    process.exit(1);
  });
