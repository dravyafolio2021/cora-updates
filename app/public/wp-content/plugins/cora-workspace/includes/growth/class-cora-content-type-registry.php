<?php
/**
 * Cora Growth Workspace - Content Type Registry
 *
 * Extensible registry defining allowed content models, metadata validation rules, and render strategies.
 *
 * @package CoraWorkspace
 * @subpackage Growth
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Content_Type_Registry {

    private static $types = array();

    /**
     * Initialize standard content types
     */
    public static function init() {
        self::register_default_types();
    }

    /**
     * Register a content type
     *
     * @param string $type_key Unique identifier (e.g. 'article', 'guide')
     * @param array  $config   Type configuration array
     */
    public static function register_type( $type_key, $config ) {
        $defaults = array(
            'label'                 => ucfirst( str_replace( '_', ' ', $type_key ) ),
            'schema_version'        => 1,
            'description'           => '',
            'supports_chapters'     => false,
            'allowed_blocks'        => array(), // Empty means all registered blocks allowed
            'required_fields'       => array( 'title', 'slug', 'excerpt', 'primary_keyword' ),
            'seo_required'          => array( 'title', 'meta_description', 'og_image' ),
            'allowed_relationships' => array( 'related_content', 'topic_cluster', 'parent_topic', 'lead_magnet' ),
            'public_url_prefix'     => '/' . $type_key,
            'preview_route'         => '/preview',
        );

        self::$types[ $type_key ] = wp_parse_args( $config, $defaults );
    }

    /**
     * Get all registered types
     *
     * @return array
     */
    public static function get_all_types() {
        if ( empty( self::$types ) ) {
            self::register_default_types();
        }
        return self::$types;
    }

    /**
     * Get a specific type definition
     *
     * @param string $type_key
     * @return array|null
     */
    public static function get_type( $type_key ) {
        if ( empty( self::$types ) ) {
            self::register_default_types();
        }
        return isset( self::$types[ $type_key ] ) ? self::$types[ $type_key ] : null;
    }

    /**
     * Check if a type is valid
     *
     * @param string $type_key
     * @return bool
     */
    public static function is_valid_type( $type_key ) {
        return self::get_type( $type_key ) !== null;
    }

    /**
     * Register default content types
     */
    private static function register_default_types() {
        // 1. Article (Standard deep-dive blog / essay)
        self::register_type( 'article', array(
            'label'                 => 'Article',
            'schema_version'        => 1,
            'description'           => 'Strategic long-form articles, tutorials, and educational essays.',
            'supports_chapters'     => false,
            'public_url_prefix'     => '/blog',
            'required_fields'       => array( 'title', 'slug', 'excerpt', 'primary_keyword' ),
            'seo_required'          => array( 'title', 'meta_description' ),
        ) );

        // 2. Guide (Chaptered pillar playbook with lead magnets & featured chapter assets)
        self::register_type( 'guide', array(
            'label'                 => 'Guide',
            'schema_version'        => 1,
            'description'           => 'Multi-chapter pillar guides with infographics, auto TOC, and downloadable PDFs.',
            'supports_chapters'     => true,
            'public_url_prefix'     => '/guides',
            'required_fields'       => array( 'title', 'slug', 'excerpt', 'primary_keyword', 'chapters' ),
            'seo_required'          => array( 'title', 'meta_description', 'og_image' ),
        ) );

        // 3. Landing Page (Targeted campaign or industry acquisition page)
        self::register_type( 'landing_page', array(
            'label'                 => 'Landing Page',
            'schema_version'        => 1,
            'description'           => 'High-converting organic landing page for specific personas or industries.',
            'supports_chapters'     => false,
            'public_url_prefix'     => '/landing',
            'required_fields'       => array( 'title', 'slug', 'primary_keyword' ),
            'seo_required'          => array( 'title', 'meta_description' ),
        ) );

        // 4. Comparison (Alternative & competitor breakdown matrix)
        self::register_type( 'comparison', array(
            'label'                 => 'Comparison Matrix',
            'schema_version'        => 1,
            'description'           => 'Objective product/workflow comparisons and alternative evaluation pages.',
            'supports_chapters'     => false,
            'public_url_prefix'     => '/compare',
            'required_fields'       => array( 'title', 'slug', 'primary_keyword' ),
            'seo_required'          => array( 'title', 'meta_description' ),
        ) );

        // 5. Research Report (Benchmark studies, surveys, and proprietary data)
        self::register_type( 'research_report', array(
            'label'                 => 'Research Report',
            'schema_version'        => 1,
            'description'           => 'Data-backed industry reports, benchmarks, and downloadable research papers.',
            'supports_chapters'     => true,
            'public_url_prefix'     => '/research',
            'required_fields'       => array( 'title', 'slug', 'primary_keyword' ),
            'seo_required'          => array( 'title', 'meta_description' ),
        ) );

        // 6. Tool Page (Free utility or calculator landing spec)
        self::register_type( 'tool_page', array(
            'label'                 => 'Tool Page',
            'schema_version'        => 1,
            'description'           => 'Interactive free utility, generator, or calculation tool landing page.',
            'supports_chapters'     => false,
            'public_url_prefix'     => '/tools',
            'required_fields'       => array( 'title', 'slug', 'primary_keyword' ),
            'seo_required'          => array( 'title', 'meta_description' ),
        ) );

        // 7. Lead Magnet (Downloadable checklist, workbook, or template)
        self::register_type( 'lead_magnet', array(
            'label'                 => 'Lead Magnet',
            'schema_version'        => 1,
            'description'           => 'Gated or ungated asset page for templates, worksheets, and resources.',
            'supports_chapters'     => false,
            'public_url_prefix'     => '/resources',
            'required_fields'       => array( 'title', 'slug', 'primary_keyword' ),
            'seo_required'          => array( 'title', 'meta_description' ),
        ) );
    }
}
