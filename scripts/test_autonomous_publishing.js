const store = require('./cora-growth-store');

async function runE2ETests() {
  console.log('=== STARTING E2E AUTONOMOUS PUBLISHING MCP TESTS ===\n');

  // Test 1: Check Content Overlap
  console.log('Test 1: Content Overlap Check');
  const overlapExisting = store.checkContentOverlap({
    title: 'Agency Client Onboarding Playbook',
    slug: 'agency-client-onboarding-playbook',
    primary_keyword: 'client onboarding',
    search_intent: 'informational',
    type: 'guide'
  });
  console.log('Overlap with existing guide:', JSON.stringify(overlapExisting, null, 2));

  const overlapNew = store.checkContentOverlap({
    title: 'Advanced Agency Margin Optimization in 2026',
    slug: 'advanced-agency-margin-optimization-2026',
    primary_keyword: 'agency margin optimization',
    search_intent: 'informational',
    type: 'article'
  });
  console.log('Overlap with new article:', JSON.stringify(overlapNew, null, 2));
  if (overlapNew.risk !== 'low') throw new Error('Expected low risk for unique article');

  // Test 2: Create Article with Sources, Stats, Quick Answer, FAQs, Relationships
  console.log('\nTest 2: Create Structured CMS Article');
  const articleSlug = 'mcp-autonomous-article-test-' + Date.now();
  const articlePayload = {
    title: 'The Blueprint for Autonomous Agency Scaling',
    slug: articleSlug,
    type: 'article',
    category: 'agency-profitability',
    primary_category: 'agency-profitability',
    primary_keyword: 'autonomous agency scaling',
    excerpt: 'How modern digital agencies implement autonomous delivery pipelines.',
    dek: 'Strategic methodology for autonomous operations and margin defense.',
    quick_answer: 'Autonomous agency scaling combines structured handoffs, dynamic resource allocation, and automated client communication to boost net margins by 18-24%.',
    author: {
      name: 'Studio Director',
      role: 'Head of Operations',
      avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=160&auto=format&fit=crop&q=80'
    },
    sources: [
      {
        id: 'cora-ops-2026',
        title: 'Cora 2026 Agency Profitability Benchmark Report',
        publisher: 'Cora Research Lab',
        url: 'https://heycora.in/research/profitability-benchmark-2026',
        source_type: 'first_party_dataset',
        published_at: '2026-01-15'
      }
    ],
    content: [
      {
        id: 'intro-p1',
        type: 'paragraph',
        content: 'Scaling an agency traditionally meant hiring more account managers and developers.'
      },
      {
        id: 'stat-margin',
        type: 'stat',
        value: '23.4%',
        label: 'Average EBITDA Margin Improvement',
        description: 'Observed across 140+ agencies utilizing automated delivery handoffs.',
        source_ids: ['cora-ops-2026']
      },
      {
        id: 'faq-1',
        type: 'faq',
        question: 'What is the implementation timeline for autonomous delivery?',
        answer: 'Most boutique agencies achieve full operational automation within 14 business days.'
      }
    ],
    relationships: [
      {
        type: 'parent_guide',
        target_id: 'agency-profitability-margin-guide',
        target_type: 'guide',
        title: 'Agency Profitability & Margin Optimization Guide',
        slug: 'agency-profitability-margin-guide'
      },
      {
        type: 'related_tool',
        target_id: 'retainer-calculator',
        target_type: 'tool',
        title: 'Retainer & Profitability Calculator',
        slug: 'retainer-calculator',
        url: '/tools/retainer-calculator'
      }
    ],
    assets: [
      {
        id: 'cover-1',
        type: 'cover_image',
        url: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&auto=format&fit=crop&q=80',
        alt: 'Agency Scaling Analytics Dashboard'
      }
    ],
    faqs: [
      {
        question: 'Does autonomous publishing require code changes?',
        answer: 'Zero TypeScript or React code modifications are required for newly published articles.'
      }
    ]
  };

  const articleEntry = store.createContent(articlePayload);
  console.log('Created Article ID:', articleEntry.id, 'Slug:', articleEntry.slug);

  // Test 3: Validate Article
  console.log('\nTest 3: Validate Article');
  const articleValidation = store.validateContent(articleEntry);
  console.log('Article Validation:', JSON.stringify(articleValidation, null, 2));
  if (!articleValidation.valid) throw new Error('Article validation failed: ' + JSON.stringify(articleValidation.errors));

  // Test 4: Create Preview Token
  console.log('\nTest 4: Create Preview Token');
  const preview = store.generatePreview(articleEntry.id);
  console.log('Preview Result:', preview);
  if (!preview.token && !preview.preview_token) throw new Error('Preview token creation failed');

  // Test 5: Publish Content with Live Verification
  console.log('\nTest 5: Publish Article (Live Verification)');
  const publishResult = await store.publishContent(articleEntry.id);
  console.log('Publish Result:', JSON.stringify(publishResult, null, 2));
  if (!publishResult.success || !publishResult.published) throw new Error('Publishing failed');

  // Test 6: Check Revisions
  console.log('\nTest 6: Get Revisions');
  const revisions = store.getRevisions(articleEntry.id);
  console.log('Revisions count:', revisions.length);
  if (revisions.length === 0) throw new Error('No revision snapshots recorded');

  // Test 7: Create and Validate Guide
  console.log('\nTest 7: Create Structured CMS Guide');
  const guideSlug = 'mcp-autonomous-guide-test-' + Date.now();
  const guidePayload = {
    title: 'The Master Guide to Modern Digital Agency Operations',
    slug: guideSlug,
    type: 'guide',
    category: 'operations',
    primary_category: 'operations',
    excerpt: 'Comprehensive operational manual for boutique creative and dev agencies.',
    dek: 'Architecture, resource planning, and profit systems for 2026.',
    author: {
      name: 'Studio Director',
      role: 'Principal Consultant'
    },
    sources: [
      {
        id: 'cora-benchmark-2026',
        title: '2026 Agency Operations Report',
        publisher: 'Cora Research Lab',
        source_type: 'first_party_dataset'
      }
    ],
    chapters: [
      {
        id: 'chap-1',
        title: 'Foundation of Streamlined Operations',
        slug: 'foundation-streamlined-ops',
        order: 1,
        excerpt: 'Standardizing client handoffs and internal SOPs.',
        blocks: [
          {
            id: 'c1-p1',
            type: 'paragraph',
            content: 'Every agency bottleneck traces back to ambiguous client handoffs.'
          },
          {
            id: 'c1-stat',
            type: 'stat',
            value: '4.2 hrs',
            label: 'Saved Per Week Per Account Lead',
            source_ids: ['cora-benchmark-2026']
          }
        ]
      }
    ],
    assets: [
      {
        id: 'guide-cover',
        type: 'cover_image',
        url: 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1200&auto=format&fit=crop&q=80',
        alt: 'Agency operations planning board'
      }
    ]
  };

  const guideEntry = store.createContent(guidePayload);
  console.log('Created Guide ID:', guideEntry.id, 'Slug:', guideEntry.slug);

  const guideValidation = store.validateContent(guideEntry);
  console.log('Guide Validation:', JSON.stringify(guideValidation, null, 2));
  if (!guideValidation.valid) throw new Error('Guide validation failed: ' + JSON.stringify(guideValidation.errors));

  const guidePublish = await store.publishContent(guideEntry.id);
  console.log('Guide Publish Result:', JSON.stringify(guidePublish, null, 2));
  if (!guidePublish.success) throw new Error('Guide publish failed');

  console.log('\n ALL E2E AUTONOMOUS PUBLISHING TESTS PASSED SUCCESSFULLY! ===\n');
}

runE2ETests().catch((err) => {
  console.error('Test failed with error:', err);
  process.exit(1);
});
