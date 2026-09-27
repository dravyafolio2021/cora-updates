<?php
/**
 * Cora Growth Workspace - Organic Growth Analytics & Content Discovery
 *
 * Read-only metrics query adapter for GSC/GA4 performance aggregation and internal link graphs.
 *
 * @package CoraWorkspace
 * @subpackage Growth
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Growth_Analytics {

    /**
     * Get aggregated performance metrics for a specific content entry
     *
     * @param string $content_id
     * @param string $workspace_id
     * @param int    $days
     * @return array
     */
    public static function get_content_performance( $content_id, $workspace_id = 'growth_cora_main_01', $days = 30 ) {
        global $wpdb;

        $table_metrics = $wpdb->prefix . 'cora_growth_metrics';
        $since_date = date( 'Y-m-d', strtotime( "-{$days} days" ) );

        $summary = $wpdb->get_row( $wpdb->prepare(
            "SELECT 
                SUM(impressions) as total_impressions,
                SUM(clicks) as total_clicks,
                AVG(ctr) as avg_ctr,
                AVG(position) as avg_position,
                SUM(sessions) as total_sessions,
                SUM(engaged_sessions) as total_engaged_sessions,
                SUM(conversions) as total_conversions
            FROM {$table_metrics}
            WHERE workspace_id = %s AND content_id = %s AND metric_date >= %s",
            $workspace_id,
            $content_id,
            $since_date
        ), ARRAY_A );

        // Query top keyword rankings for this content
        $top_queries = $wpdb->get_results( $wpdb->prepare(
            "SELECT query, SUM(impressions) as impressions, SUM(clicks) as clicks, AVG(ctr) as ctr, AVG(position) as position
            FROM {$table_metrics}
            WHERE workspace_id = %s AND content_id = %s AND query IS NOT NULL AND query != '' AND metric_date >= %s
            GROUP BY query
            ORDER BY clicks DESC, impressions DESC
            LIMIT 10",
            $workspace_id,
            $content_id,
            $since_date
        ), ARRAY_A );

        // Daily trend data
        $trend = $wpdb->get_results( $wpdb->prepare(
            "SELECT metric_date, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(sessions) as sessions
            FROM {$table_metrics}
            WHERE workspace_id = %s AND content_id = %s AND metric_date >= %s
            GROUP BY metric_date
            ORDER BY metric_date ASC",
            $workspace_id,
            $content_id,
            $since_date
        ), ARRAY_A );

        return array(
            'content_id' => $content_id,
            'range_days' => $days,
            'summary'    => array(
                'impressions'      => intval( $summary['total_impressions'] ?? 0 ),
                'clicks'           => intval( $summary['total_clicks'] ?? 0 ),
                'avg_ctr'          => round( floatval( $summary['avg_ctr'] ?? 0 ), 2 ),
                'avg_position'     => round( floatval( $summary['avg_position'] ?? 0 ), 1 ),
                'sessions'         => intval( $summary['total_sessions'] ?? 0 ),
                'engaged_sessions' => intval( $summary['total_engaged_sessions'] ?? 0 ),
                'conversions'      => intval( $summary['total_conversions'] ?? 0 ),
            ),
            'top_queries' => $top_queries ?: array(),
            'trend'       => $trend ?: array(),
        );
    }

    /**
     * Get search opportunities (Striking distance keywords: pos 4-20, high impressions)
     */
    public static function get_search_opportunities( $workspace_id = 'growth_cora_main_01' ) {
        global $wpdb;
        $table_metrics = $wpdb->prefix . 'cora_growth_metrics';

        $opportunities = $wpdb->get_results( $wpdb->prepare(
            "SELECT query, target_url, content_id, SUM(impressions) as impressions, SUM(clicks) as clicks, AVG(position) as avg_position
            FROM {$table_metrics}
            WHERE workspace_id = %s AND query IS NOT NULL AND query != '' AND position BETWEEN 4 AND 20
            GROUP BY query, target_url, content_id
            ORDER BY impressions DESC
            LIMIT 20",
            $workspace_id
        ), ARRAY_A );

        return $opportunities ?: array();
    }

    /**
     * Get list of all existing topics and primary keywords to prevent duplication
     */
    public static function get_existing_topics_and_keywords( $workspace_id = 'growth_cora_main_01' ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, type, title, slug, primary_keyword, secondary_keywords_json, status, published_at
            FROM {$table_content}
            WHERE workspace_id = %s
            ORDER BY created_at DESC",
            $workspace_id
        ), ARRAY_A );

        $keywords = array();
        $topics = array();

        foreach ( $results as $item ) {
            if ( ! empty( $item['primary_keyword'] ) ) {
                $keywords[] = array(
                    'keyword'    => $item['primary_keyword'],
                    'content_id' => $item['id'],
                    'slug'       => $item['slug'],
                    'type'       => $item['type'],
                );
            }
            $topics[] = array(
                'id'         => $item['id'],
                'title'      => $item['title'],
                'slug'       => $item['slug'],
                'type'       => $item['type'],
                'status'     => $item['status'],
            );
        }

        return array(
            'topics'   => $topics,
            'keywords' => $keywords,
        );
    }

    /**
     * Get internal link graph of published content
     */
    public static function get_internal_link_graph( $workspace_id = 'growth_cora_main_01' ) {
        global $wpdb;
        $table_content = $wpdb->prefix . 'cora_content_entries';

        $entries = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, type, title, slug, relationships_json, primary_keyword
            FROM {$table_content}
            WHERE workspace_id = %s AND status = 'published'",
            $workspace_id
        ), ARRAY_A );

        $graph = array();
        foreach ( $entries as $e ) {
            $relationships = ! empty( $e['relationships_json'] ) ? json_decode( $e['relationships_json'], true ) : array();
            $graph[] = array(
                'id'            => $e['id'],
                'type'          => $e['type'],
                'title'         => $e['title'],
                'slug'          => $e['slug'],
                'url'           => ( $e['type'] === 'guide' ? '/guides/' : '/blog/' ) . $e['slug'],
                'keyword'       => $e['primary_keyword'],
                'relationships' => $relationships ?: array(),
            );
        }

        return $graph;
    }
}
