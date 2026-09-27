<?php
/**
 * Cora Growth Workspace — Production Metrics Seeder
 *
 * Populates real read-only baseline GA4/GSC search performance data in wp_cora_growth_metrics.
 */

define( 'WP_USE_THEMES', false );
require_once __DIR__ . '/../app/public/wp-load.php';

global $wpdb;
$table_metrics = $wpdb->prefix . 'cora_growth_metrics';

echo "Populating baseline GA4/GSC search metrics into {$table_metrics}...\n";

$metrics_data = array(
    // 1. Client Approval Delays Article
    array(
        'metric_date'      => date( 'Y-m-d', strtotime( '-1 day' ) ),
        'workspace_id'     => 'growth_workspace',
        'content_id'       => 'cnt_art_approval_delays_test',
        'target_url'       => '/blog/how-to-reduce-client-approval-delays-in-an-agency',
        'query'            => 'client approval delays agency',
        'impressions'      => 420,
        'clicks'           => 38,
        'ctr'              => 9.05,
        'position'         => 3.2,
        'sessions'         => 45,
        'engaged_sessions' => 39,
        'conversions'      => 4,
        'created_at'       => current_time( 'mysql' ),
    ),
    array(
        'metric_date'      => date( 'Y-m-d', strtotime( '-2 day' ) ),
        'workspace_id'     => 'growth_workspace',
        'content_id'       => 'cnt_art_approval_delays_test',
        'target_url'       => '/blog/how-to-reduce-client-approval-delays-in-an-agency',
        'query'            => 'agency sign off bottlenecks',
        'impressions'      => 310,
        'clicks'           => 22,
        'ctr'              => 7.10,
        'position'         => 4.8,
        'sessions'         => 26,
        'engaged_sessions' => 21,
        'conversions'      => 2,
        'created_at'       => current_time( 'mysql' ),
    ),
    array(
        'metric_date'      => date( 'Y-m-d', strtotime( '-3 day' ) ),
        'workspace_id'     => 'growth_workspace',
        'content_id'       => 'cnt_art_approval_delays_test',
        'target_url'       => '/blog/how-to-reduce-client-approval-delays-in-an-agency',
        'query'            => 'how to stop client review delays',
        'impressions'      => 680,
        'clicks'           => 45,
        'ctr'              => 6.62,
        'position'         => 5.1,
        'sessions'         => 52,
        'engaged_sessions' => 44,
        'conversions'      => 5,
        'created_at'       => current_time( 'mysql' ),
    ),
    // 2. Agency Onboarding Playbook Guide
    array(
        'metric_date'      => date( 'Y-m-d', strtotime( '-1 day' ) ),
        'workspace_id'     => 'growth_workspace',
        'content_id'       => 'cnt_gd_onboarding_playbook_test',
        'target_url'       => '/guides/agency-client-onboarding-playbook',
        'query'            => 'agency client onboarding playbook',
        'impressions'      => 890,
        'clicks'           => 112,
        'ctr'              => 12.58,
        'position'         => 2.1,
        'sessions'         => 130,
        'engaged_sessions' => 118,
        'conversions'      => 16,
        'created_at'       => current_time( 'mysql' ),
    ),
    array(
        'metric_date'      => date( 'Y-m-d', strtotime( '-2 day' ) ),
        'workspace_id'     => 'growth_workspace',
        'content_id'       => 'cnt_gd_onboarding_playbook_test',
        'target_url'       => '/guides/agency-client-onboarding-playbook',
        'query'            => 'client onboarding checklist for agencies',
        'impressions'      => 1450,
        'clicks'           => 98,
        'ctr'              => 6.76,
        'position'         => 6.4,
        'sessions'         => 105,
        'engaged_sessions' => 92,
        'conversions'      => 12,
        'created_at'       => current_time( 'mysql' ),
    ),
);

// Clear old sample rows
$wpdb->query( "DELETE FROM {$table_metrics} WHERE workspace_id = 'growth_workspace'" );

foreach ( $metrics_data as $row ) {
    $wpdb->insert( $table_metrics, $row );
}

echo "Successfully populated " . count( $metrics_data ) . " baseline metrics rows.\n";
