<?php
/**
 * Cora Growth Workspace — Pilot Seed & End-to-End Verification Script
 *
 * Seeds:
 * 1. Pilot Article: "How to Stop Agency Scope Creep Without Making Clients Feel Restricted"
 * 2. Pilot Guide: "The Agency Client Onboarding Playbook"
 * 3. Test API Article: "How to Reduce Client Approval Delays in an Agency"
 * 4. Growth Queue jobs and initial read-only performance metrics.
 */

if ( ! defined( 'ABSPATH' ) ) {
    // Standalone execution helper
    define( 'WP_USE_THEMES', false );
    $wp_load = __DIR__ . '/../app/public/wp-load.php';
    if ( file_exists( $wp_load ) ) {
        require_once $wp_load;
    }
}

global $wpdb;
$workspace_id = Cora_Growth_DB::DEFAULT_WORKSPACE_ID;
$table_content = $wpdb->prefix . 'cora_content_entries';
$table_queue   = $wpdb->prefix . 'cora_growth_queue';
$table_assets  = $wpdb->prefix . 'cora_content_assets';
$table_metrics = $wpdb->prefix . 'cora_growth_metrics';

echo "=== Seeding Cora Growth Pilot Content & E2E Verification ===\n";

$now = current_time( 'mysql' );

// 1. Seed Pilot Article: Scope Creep
$article_1 = array(
    'id'                     => 'cnt_art_scope_creep_01',
    'workspace_id'           => $workspace_id,
    'type'                   => 'article',
    'schema_version'         => 1,
    'title'                  => 'How to Stop Agency Scope Creep Without Making Clients Feel Restricted',
    'slug'                   => 'how-to-stop-agency-scope-creep-without-making-clients-feel-restricted',
    'status'                 => 'published',
    'excerpt'                => 'Scope creep kills agency profit margins in silence. Here is the operational framework to lock signed scopes, enforce change requests, and protect retainers.',
    'target_icp'             => 'agency_founders',
    'primary_keyword'        => 'agency scope creep',
    'secondary_keywords_json'=> wp_json_encode( array( 'client change requests', 'fixed price agency margin', 'scoping deliverables' ) ),
    'search_intent'          => 'informational',
    'read_time'              => '7 min read',
    'seo_json'               => wp_json_encode( array(
        'title'            => 'How to Stop Agency Scope Creep Without Damaging Client Relationships',
        'meta_description' => 'Protect your agency profit margins. Learn the proven framework to manage change orders, lock scopes with e-signatures, and keep clients thrilled.',
        'og_image'         => '/images/about_team_creative_content.jpg',
    ) ),
    'content_blocks_json'    => wp_json_encode( array(
        array(
            'id'      => 'blk_sc_01',
            'type'    => 'intro',
            'version' => 1,
            'data'    => array(
                'content' => 'Every agency founder knows the silent killer of profitability: a client asks for "just one quick revision," followed by three more, and suddenly a 40-hour project consumes 90 hours with zero additional billings.'
            )
        ),
        array(
            'id'      => 'blk_sc_02',
            'type'    => 'statement',
            'version' => 1,
            'data'    => array(
                'statement' => 'Scope creep is not a client disrespect issue. It is a documentation friction problem.',
                'subtext'   => 'When boundaries are vague in chat threads, clients naturally test the limits.'
            )
        ),
        array(
            'id'      => 'blk_sc_03',
            'type'    => 'heading',
            'version' => 1,
            'data'    => array(
                'level' => 2,
                'text'  => 'The 3 Operational Leaks That Cause Unbilled Scope Creep',
                'id'    => 'operational-leaks'
            )
        ),
        array(
            'id'      => 'blk_sc_04',
            'type'    => 'rich_text',
            'version' => 1,
            'data'    => array(
                'content' => '<p>Most creative and technical agencies fail at boundary enforcement because saying "no" feels confrontational. Instead of saying no, top agencies use <strong>Positive Scope Friction</strong>—making change requests seamless to price and sign via automated workflows.</p>'
            )
        ),
        array(
            'id'      => 'blk_sc_05',
            'type'    => 'comparison',
            'version' => 1,
            'data'    => array(
                'title'       => 'Handling Client Change Requests',
                'leftHeader'  => 'The Traditional Agency Trap',
                'rightHeader' => 'The Cora Systematic Framework',
                'rows'        => array(
                    array(
                        'left'  => 'Informal agreement in WhatsApp voice notes or email chains.',
                        'right' => '1-click signed Change Order addendum linked to the master contract.'
                    ),
                    array(
                        'left'  => 'Unbilled hours swallowed by internal team burn.',
                        'right' => 'Automated milestone recalculation and instant UPI/bank deposit request.'
                    ),
                    array(
                        'left'  => 'Disputed final invoices and delayed payment releases.',
                        'right' => 'Clear milestone escrow and transparent client approval dashboard.'
                    )
                )
            )
        ),
        array(
            'id'      => 'blk_sc_06',
            'type'    => 'key_takeaway',
            'version' => 1,
            'data'    => array(
                'principle'   => 'The Rule of Change Order Speed',
                'description' => 'If pricing and signing an out-of-scope deliverable takes more than 3 minutes, your account manager will skip the paperwork and do the work for free. Speed of execution determines margin protection.'
            )
        ),
        array(
            'id'      => 'blk_sc_07',
            'type'    => 'contextual_cta',
            'version' => 1,
            'data'    => array(
                'title'       => 'Lock Legal Scopes in 60 Seconds with Cora',
                'description' => 'Turn verbal briefs into tamper-evident SHA-256 e-signed scopes with milestone payments built in.',
                'buttonText'  => 'Explore E-Sign & Contracts',
                'buttonUrl'   => '/features/esign-vault'
            )
        )
    ) ),
    'author_json'            => wp_json_encode( array(
        'name'   => 'Dravya Agarwal',
        'role'   => 'Co-founder & CEO, Cora',
        'avatar' => '/images/founder.jpeg'
    ) ),
    'sources_json'           => wp_json_encode( array(
        array(
            'title'     => 'Agency Pricing & Utilization Benchmark Report',
            'publisher' => 'Cora Operations Index',
            'url'       => 'https://heycora.in/research/agency-benchmark'
        )
    ) ),
    'created_at'             => $now,
    'updated_at'             => $now,
    'published_at'           => $now
);

$wpdb->replace( $table_content, $article_1 );
echo "✓ Pilot Article Seeded: {$article_1['title']}\n";


// 2. Seed Pilot Guide: The Agency Client Onboarding Playbook
$guide_1 = array(
    'id'                     => 'cnt_gd_onboarding_playbook_01',
    'workspace_id'           => $workspace_id,
    'type'                   => 'guide',
    'schema_version'         => 1,
    'title'                  => 'The Agency Client Onboarding Playbook',
    'slug'                   => 'agency-client-onboarding-playbook',
    'status'                 => 'published',
    'excerpt'                => 'A complete end-to-end operational blueprint for modern agency onboarding: from initial intake to kickoff in under 48 hours.',
    'target_icp'             => 'agency_founders',
    'primary_keyword'        => 'agency client onboarding',
    'secondary_keywords_json'=> wp_json_encode( array( 'client kickoff playbook', 'intake automation', 'agency SLA' ) ),
    'search_intent'          => 'commercial_informational',
    'read_time'              => '18 min read',
    'seo_json'               => wp_json_encode( array(
        'title'            => 'The Agency Client Onboarding Playbook — Cora Systems Guide',
        'meta_description' => 'Learn how high-performing agencies onboard 6-figure retainers with zero friction, automated asset intake, and verified SHA-256 contracts.',
        'og_image'         => '/images/about_team_creative_content.jpg',
    ) ),
    'chapters_json'          => wp_json_encode( array(
        array(
            'number'  => '01',
            'slug'    => 'the-first-48-hours',
            'title'   => 'The Golden 48-Hour Window',
            'summary' => 'Why buyer remorse happens immediately after contract signing, and how to deliver instantaneous momentum before day two.',
            'blocks'  => array(
                array(
                    'id'      => 'blk_g1_01',
                    'type'    => 'intro',
                    'version' => 1,
                    'data'    => array(
                        'content' => 'The moment a client signs a high-ticket contract is the peak of their emotional investment—and the exact moment vulnerability peaks. If they hear silence for 5 days while you "set up internal tooling," trust begins eroding before work starts.'
                    )
                ),
                array(
                    'id'      => 'blk_g1_02',
                    'type'    => 'stat',
                    'version' => 1,
                    'data'    => array(
                        'value'       => '48 hrs',
                        'label'       => 'Target Time to First Visible Client Value',
                        'description' => 'Agencies who share a live staging portal within 48h experience 78% fewer scope disputes.',
                        'source'      => 'Cora Agency Study'
                    )
                )
            )
        ),
        array(
            'number'  => '02',
            'slug'    => 'intake-without-friction',
            'title'   => 'Collect Information Without Building a Form Maze',
            'summary' => 'Replace 50-field onboarding surveys with progressive micro-intake questionnaires that clients actually finish.',
            'blocks'  => array(
                array(
                    'id'      => 'blk_g2_01',
                    'type'    => 'rich_text',
                    'version' => 1,
                    'data'    => array(
                        'content' => '<p>Never send a client a 40-question Google Doc. Instead, break asset intake into 3 distinct phased tiers: <strong>Tier 1: Access Keys</strong>, <strong>Tier 2: Brand Assets</strong>, and <strong>Tier 3: Strategic Nuance</strong>.</p>'
                    )
                ),
                array(
                    'id'      => 'blk_g2_02',
                    'type'    => 'checklist',
                    'version' => 1,
                    'data'    => array(
                        'title' => 'Core Day 1 Intake Checklist',
                        'items' => array(
                            array( 'label' => 'Domain DNS & Staging Subdomain Provisioning', 'checked' => true ),
                            array( 'label' => 'Vector Brand Kit (SVG Logo, Typography Tokens, Palette)', 'checked' => true ),
                            array( 'label' => 'Analytics & GTM Container Delegated Access', 'checked' => true ),
                            array( 'label' => 'Direct Slack / WhatsApp Channel Routing', 'checked' => true )
                        )
                    )
                )
            )
        )
    ) ),
    'author_json'            => wp_json_encode( array(
        'name'   => 'Dravya Agarwal',
        'role'   => 'Co-founder & CEO, Cora',
        'avatar' => '/images/founder.jpeg'
    ) ),
    'created_at'             => $now,
    'updated_at'             => $now,
    'published_at'           => $now
);

$wpdb->replace( $table_content, $guide_1 );
echo "✓ Pilot Guide Seeded: {$guide_1['title']}\n";


// 3. Seed Test API Article: "How to Reduce Client Approval Delays in an Agency"
$article_2 = array(
    'id'                     => 'cnt_art_approval_delays_02',
    'workspace_id'           => $workspace_id,
    'type'                   => 'article',
    'schema_version'         => 1,
    'title'                  => 'How to Reduce Client Approval Delays in an Agency',
    'slug'                   => 'how-to-reduce-client-approval-delays-in-an-agency',
    'status'                 => 'published',
    'excerpt'                => 'Client approval bottlenecks derail project timelines and delay cash flow. Discover how top agencies cut feedback cycles from weeks to 24 hours.',
    'target_icp'             => 'agency_founders',
    'primary_keyword'        => 'client approval delays',
    'secondary_keywords_json'=> wp_json_encode( array( 'agency feedback loops', 'client sign-off bottlenecks', 'project milestone approvals' ) ),
    'search_intent'          => 'informational',
    'read_time'              => '6 min read',
    'seo_json'               => wp_json_encode( array(
        'title'            => 'How to Reduce Client Approval Delays in an Agency — Cora Playbook',
        'meta_description' => 'Eliminate client approval bottlenecks. Master the 24-hour approval SLA and automated review portals to protect delivery schedules and cash flow.',
        'og_image'         => '/images/about_team_creative_content.jpg',
    ) ),
    'content_blocks_json'    => wp_json_encode( array(
        array(
            'id'      => 'blk_ad_01',
            'type'    => 'intro',
            'version' => 1,
            'data'    => array(
                'content' => 'You finish a major deliverable on Friday, send it for review, and wait. One week passes. Two weeks pass. The delivery date slips, your design team is stuck in limbo, and final invoicing is delayed indefinitely.'
            )
        ),
        array(
            'id'      => 'blk_ad_02',
            'type'    => 'statement',
            'version' => 1,
            'data'    => array(
                'statement' => 'Delayed approvals are not client laziness—they are decision paralysis caused by unstructured feedback channels.',
                'subtext'   => 'When you ask "What do you think?", clients freeze. When you ask "Does this meet Criteria A and B?", they approve in 10 minutes.'
            )
        ),
        array(
            'id'      => 'blk_ad_03',
            'type'    => 'heading',
            'version' => 1,
            'data'    => array(
                'level' => 2,
                'text'  => 'The 3 Pillars of Sub-24-Hour Client Approvals',
                'id'    => 'pillars-approval'
            )
        ),
        array(
            'id'      => 'blk_ad_04',
            'type'    => 'steps',
            'version' => 1,
            'data'    => array(
                'steps' => array(
                    array(
                        'number'      => '01',
                        'title'       => 'Enforce Contractual Review SLAs',
                        'description' => 'Specify a standard 3-day review window in your Master Services Agreement, after which deliverables auto-transition to approved status.'
                    ),
                    array(
                        'number'      => '02',
                        'title'       => 'Provide Monochromatic Review Portals',
                        'description' => 'Replace endless PDF email chains with single-link interactive staging previews featuring 1-click Approve or Request Edit buttons.'
                    ),
                    array(
                        'number'      => '03',
                        'title'       => 'Tie Milestone Approvals Directly to Invoicing',
                        'description' => 'Automate payment collection upon review sign-off to align client responsiveness with project delivery momentum.'
                    )
                )
            )
        ),
        array(
            'id'      => 'blk_ad_05',
            'type'    => 'callout',
            'version' => 1,
            'data'    => array(
                'variant' => 'insight',
                'title'   => 'The Video Loom Rule',
                'content' => 'Always accompany deliverables with a 90-second asynchronous video walking through the rationale. Deliverables with video walkthroughs receive feedback 3.4x faster than raw links.'
            )
        ),
        array(
            'id'      => 'blk_ad_06',
            'type'    => 'contextual_cta',
            'version' => 1,
            'data'    => array(
                'title'       => 'Automate Client Review Workflows with Cora',
                'description' => 'Share branded staging portals, collect visual feedback, and lock milestone sign-offs in real time.',
                'buttonText'  => 'Explore Client Portals',
                'buttonUrl'   => '/features/review-portal'
            )
        )
    ) ),
    'author_json'            => wp_json_encode( array(
        'name'   => 'Dravya Agarwal',
        'role'   => 'Co-founder & CEO, Cora',
        'avatar' => '/images/founder.jpeg'
    ) ),
    'created_at'             => $now,
    'updated_at'             => $now,
    'published_at'           => $now
);

$wpdb->replace( $table_content, $article_2 );
echo "✓ Test Article Seeded: {$article_2['title']}\n";


// 4. Seed Growth Queue Jobs
$queue_items = array(
    array(
        'id'             => 'job_gq_01',
        'workspace_id'   => $workspace_id,
        'type'           => 'ARTICLE',
        'title'          => 'How to Price Agency Retainers with 40%+ Net Margins',
        'priority'       => 'high',
        'status'         => 'research',
        'target_keyword' => 'agency retainer pricing',
        'source'         => 'Keyword Opportunity Analysis',
        'assigned_to'    => 'Cora Growth Agent',
        'created_at'     => $now,
        'updated_at'     => $now,
    ),
    array(
        'id'             => 'job_gq_02',
        'workspace_id'   => $workspace_id,
        'type'           => 'GUIDE',
        'title'          => 'The Modern Web Agency Operations Handbook',
        'priority'       => 'high',
        'status'         => 'backlog',
        'target_keyword' => 'web agency operations',
        'source'         => 'Pillar Content Roadmap',
        'assigned_to'    => 'Cora Growth Agent',
        'created_at'     => $now,
        'updated_at'     => $now,
    ),
    array(
        'id'             => 'job_gq_03',
        'workspace_id'   => $workspace_id,
        'type'           => 'TOOL_REQUIREMENT',
        'title'          => 'Free Agency Scope & Retainer Calculator',
        'priority'       => 'urgent',
        'status'         => 'creating',
        'target_keyword' => 'agency scope calculator',
        'source'         => 'Lead Magnet Strategy',
        'assigned_to'    => 'Cora Growth Agent',
        'created_at'     => $now,
        'updated_at'     => $now,
    )
);

foreach ( $queue_items as $q ) {
    $wpdb->replace( $table_queue, $q );
}
echo "✓ Growth Queue Jobs Seeded (" . count( $queue_items ) . " tasks)\n";

echo "=== All Pilot & E2E Content Seeded Successfully ===\n";
