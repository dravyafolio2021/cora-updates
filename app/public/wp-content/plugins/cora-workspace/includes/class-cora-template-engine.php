<?php
/**
 * Cora Dynamic Template Engine
 * Shopify-Style Dynamic Multi-Engine Presentation Layer
 * 
 * Decouples content entities (Articles, Pages, Policies, Products, Collections)
 * from themes, ensuring zero content loss when switching themes or publishing drafts.
 * 
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Template_Engine {

    /**
     * Singleton instance
     * @var Cora_Template_Engine|null
     */
    private static $instance = null;

    /**
     * Supported Archetypes
     */
    public const ARCHETYPE_ARTICLE    = 'article';
    public const ARCHETYPE_PAGE       = 'page';
    public const ARCHETYPE_POLICY     = 'policy';
    public const ARCHETYPE_PRODUCT    = 'product';
    public const ARCHETYPE_COLLECTION = 'collection';

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
        add_action( 'init', array( $this, 'maybe_create_table' ) );
    }

    /**
     * Return all supported archetypes with metadata
     */
    public static function get_archetypes() {
        return array(
            self::ARCHETYPE_ARTICLE => array(
                'label'       => 'Articles & Blogs',
                'description' => 'Dynamic layout for Content Suite blog posts, editorial news, and case studies.',
                'icon'        => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
                'slots'       => array( 'title', 'content', 'excerpt', 'featured_image', 'author', 'date', 'read_time', 'form_embed' ),
            ),
            self::ARCHETYPE_PAGE => array(
                'label'       => 'Standard Pages',
                'description' => 'Layout for general studio webpages (About Us, Studio Hire, Contact, Team).',
                'icon'        => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>',
                'slots'       => array( 'title', 'subtitle', 'content', 'hero_image', 'form_embed' ),
            ),
            self::ARCHETYPE_POLICY => array(
                'label'       => 'Legal Policies',
                'description' => 'Formal layout for Terms of Service, Privacy Policy, Refund & Cancellation terms.',
                'icon'        => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>',
                'slots'       => array( 'title', 'content', 'effective_date', 'version', 'jurisdiction' ),
            ),
            self::ARCHETYPE_PRODUCT => array(
                'label'       => 'Products & Packages',
                'description' => 'Showcase for studio session packages, camera equipment rentals, or real estate tiers.',
                'icon'        => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>',
                'slots'       => array( 'title', 'description', 'price', 'gst_rate', 'specs', 'gallery', 'booking_form' ),
            ),
            self::ARCHETYPE_COLLECTION => array(
                'label'       => 'Collections & Archives',
                'description' => 'Grid and catalog layout for category archives, portfolio showcases, and work registries.',
                'icon'        => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>',
                'slots'       => array( 'title', 'description', 'item_loop', 'pagination', 'filters' ),
            ),
        );
    }

    /**
     * Self-healing table creation
     */
    public function maybe_create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_canvas_templates';
        
        // Fast check if table already exists in this request
        if ( get_transient( 'cora_canvas_templates_table_exists' ) ) {
            return;
        }

        $check = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );
        if ( $check !== $table_name ) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE {$table_name} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                agency_id bigint(20) unsigned NOT NULL DEFAULT 1,
                theme_id bigint(20) unsigned NOT NULL,
                archetype varchar(50) NOT NULL,
                slug varchar(100) NOT NULL,
                name varchar(255) NOT NULL,
                engine varchar(50) NOT NULL DEFAULT 'html',
                layout_data longtext,
                is_default tinyint(1) NOT NULL DEFAULT 0,
                created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                updated_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (id),
                UNIQUE KEY theme_archetype_slug (theme_id, archetype, slug),
                KEY agency_id (agency_id),
                KEY theme_id (theme_id),
                KEY archetype (archetype)
            ) {$charset_collate};";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
        }
        set_transient( 'cora_canvas_templates_table_exists', 1, DAY_IN_SECONDS );
    }

    /**
     * Register REST API Endpoints
     */
    public function register_rest_routes() {
        register_rest_route( 'cora-canvas/v1', '/templates', array(
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'rest_get_templates' ),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'rest_save_template' ),
                'permission_callback' => array( $this, 'rest_check_auth' ),
            ),
            array(
                'methods'             => 'DELETE',
                'callback'            => array( $this, 'rest_delete_template' ),
                'permission_callback' => array( $this, 'rest_check_auth' ),
            ),
        ) );

        register_rest_route( 'cora-canvas/v1', '/template-data', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'rest_get_template_data' ),
            'permission_callback' => '__return_true', // Public with workspace/tenant isolation
        ) );
    }

    public function rest_check_auth() {
        return is_user_logged_in();
    }

    /**
     * REST Handler: Get all templates for a theme
     */
    public function rest_get_templates( WP_REST_Request $request ) {
        $theme_id = intval( $request->get_param( 'theme_id' ) );
        if ( ! $theme_id ) {
            return new WP_Error( 'invalid_theme', 'theme_id is required.', array( 'status' => 400 ) );
        }
        $archetype = sanitize_key( $request->get_param( 'archetype' ) );
        
        // Auto-seed if theme has no templates
        $this->seed_default_templates_for_theme( $theme_id );

        $templates = $this->get_theme_templates( $theme_id, $archetype );
        return rest_ensure_response( array(
            'success'   => true,
            'theme_id'  => $theme_id,
            'archetypes'=> self::get_archetypes(),
            'templates' => $templates,
        ) );
    }

    /**
     * REST Handler: Save / Update a template
     */
    public function rest_save_template( WP_REST_Request $request ) {
        $theme_id    = intval( $request->get_param( 'theme_id' ) );
        $archetype   = sanitize_key( $request->get_param( 'archetype' ) );
        $slug        = sanitize_title( $request->get_param( 'slug' ) );
        $name        = sanitize_text_field( $request->get_param( 'name' ) );
        $engine      = sanitize_text_field( $request->get_param( 'engine' ) ?: 'html' );
        $layout_data = $request->get_param( 'layout_data' );
        $is_default  = ! empty( $request->get_param( 'is_default' ) ) ? 1 : 0;

        if ( ! $theme_id || ! $archetype || ! $slug || ! $name ) {
            return new WP_Error( 'missing_params', 'theme_id, archetype, slug, and name are required.', array( 'status' => 400 ) );
        }

        $res = $this->save_template( $theme_id, $archetype, $slug, $name, $engine, $layout_data, $is_default );
        if ( is_wp_error( $res ) ) {
            return $res;
        }

        return rest_ensure_response( array(
            'success' => true,
            'message' => 'Template saved successfully.',
            'template'=> $res,
        ) );
    }

    /**
     * REST Handler: Delete a custom template (defaults are protected)
     */
    public function rest_delete_template( WP_REST_Request $request ) {
        $template_id = intval( $request->get_param( 'template_id' ) );
        if ( ! $template_id ) {
            return new WP_Error( 'invalid_id', 'template_id is required.', array( 'status' => 400 ) );
        }

        $res = $this->delete_template( $template_id );
        if ( is_wp_error( $res ) ) {
            return $res;
        }

        return rest_ensure_response( array(
            'success' => true,
            'message' => 'Template deleted successfully.',
        ) );
    }

    /**
     * REST Handler: Public dynamic data endpoint for Next.js and Headless Frontends
     */
    public function rest_get_template_data( WP_REST_Request $request ) {
        $route = sanitize_text_field( $request->get_param( 'route' ) ?: '/' );
        $theme_id = intval( $request->get_param( 'theme_id' ) );

        // If no theme provided, resolve active live theme
        if ( ! $theme_id ) {
            global $wpdb;
            $theme_id = intval( $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' ORDER BY id DESC LIMIT 1" ) );
        }

        $clean_slug = trim( $route, '/' );
        if ( empty( $clean_slug ) ) {
            $clean_slug = 'home';
        }

        // 1. Check if route matches an Article
        $article = get_page_by_path( $clean_slug, OBJECT, 'post' );
        if ( $article && 'publish' === $article->post_status ) {
            $tpl_slug = get_post_meta( $article->ID, '_cora_template_slug', true ) ?: 'default';
            $template = $this->resolve_template( $theme_id, self::ARCHETYPE_ARTICLE, $tpl_slug );
            $entity_data = $this->format_article_entity( $article );

            return rest_ensure_response( array(
                'success'   => true,
                'archetype' => self::ARCHETYPE_ARTICLE,
                'entity'    => $entity_data,
                'template'  => $template,
            ) );
        }

        // 2. Check if route matches a Canvas Page
        global $wpdb;
        $page_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE slug = %s ORDER BY id DESC LIMIT 1", $clean_slug ), ARRAY_A );
        if ( $page_row ) {
            $tpl_slug = ! empty( $page_row['template'] ) ? $page_row['template'] : 'default';
            $template = $this->resolve_template( $theme_id, self::ARCHETYPE_PAGE, $tpl_slug );
            $entity_data = $this->format_page_entity( $page_row );

            return rest_ensure_response( array(
                'success'   => true,
                'archetype' => self::ARCHETYPE_PAGE,
                'entity'    => $entity_data,
                'template'  => $template,
            ) );
        }

        // 3. Check if route matches a Policy
        if ( in_array( $clean_slug, array( 'privacy-policy', 'terms-of-service', 'terms', 'privacy', 'refund-policy', 'cancellation-policy', 'policy' ), true ) ) {
            $template = $this->resolve_template( $theme_id, self::ARCHETYPE_POLICY, 'default' );
            $entity_data = $this->format_policy_entity( $clean_slug );

            return rest_ensure_response( array(
                'success'   => true,
                'archetype' => self::ARCHETYPE_POLICY,
                'entity'    => $entity_data,
                'template'  => $template,
            ) );
        }

        // 4. Check if route matches a Collection
        if ( in_array( $clean_slug, array( 'blog', 'journal', 'articles', 'portfolio', 'work', 'gallery', 'packages', 'services' ), true ) ) {
            $template = $this->resolve_template( $theme_id, self::ARCHETYPE_COLLECTION, 'default' );
            $entity_data = $this->format_collection_entity( $clean_slug );

            return rest_ensure_response( array(
                'success'   => true,
                'archetype' => self::ARCHETYPE_COLLECTION,
                'entity'    => $entity_data,
                'template'  => $template,
            ) );
        }

        // 5. Check if route matches a Product / Package
        if ( strpos( $clean_slug, 'package' ) !== false || strpos( $clean_slug, 'rental' ) !== false || strpos( $clean_slug, 'product' ) !== false ) {
            $template = $this->resolve_template( $theme_id, self::ARCHETYPE_PRODUCT, 'default' );
            $entity_data = $this->format_product_entity( $clean_slug );

            return rest_ensure_response( array(
                'success'   => true,
                'archetype' => self::ARCHETYPE_PRODUCT,
                'entity'    => $entity_data,
                'template'  => $template,
            ) );
        }

        return new WP_Error( 'not_found', 'No entity found matching route.', array( 'status' => 404 ) );
    }

    /**
     * Retrieve all templates belonging to a theme
     */
    public function get_theme_templates( $theme_id, $archetype = null ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_canvas_templates';
        $theme_id = intval( $theme_id );

        if ( $archetype ) {
            return $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE theme_id = %d AND archetype = %s ORDER BY is_default DESC, name ASC",
                $theme_id, $archetype
            ), ARRAY_A ) ?: array();
        }

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE theme_id = %d ORDER BY archetype ASC, is_default DESC, name ASC",
            $theme_id
        ), ARRAY_A ) ?: array();
    }

    /**
     * Get single template
     */
    public function get_template( $theme_id, $archetype, $slug ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_canvas_templates';
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE theme_id = %d AND archetype = %s AND slug = %s LIMIT 1",
            $theme_id, $archetype, $slug
        ), ARRAY_A );
    }

    /**
     * 3-Tier Resolution & Fallback Cascade
     * Tier 1: Exact Match in Theme (e.g. archetype=article, slug=case-study)
     * Tier 2: Archetype Default in Theme (e.g. archetype=article, is_default=1 or slug=default)
     * Tier 3: Universal System Baseline Fallback
     */
    public function resolve_template( $theme_id, $archetype, $requested_slug = 'default' ) {
        $theme_id = intval( $theme_id );
        $archetype = sanitize_key( $archetype );
        $requested_slug = sanitize_title( $requested_slug ?: 'default' );

        // Auto-seed theme templates if empty
        $this->seed_default_templates_for_theme( $theme_id );

        // Tier 1: Exact match
        $exact = $this->get_template( $theme_id, $archetype, $requested_slug );
        if ( $exact && ! empty( $exact['layout_data'] ) ) {
            $exact['resolved_tier'] = 'exact';
            return $exact;
        }

        // Tier 2: Theme default for this archetype
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_canvas_templates';
        $default_tpl = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE theme_id = %d AND archetype = %s AND (is_default = 1 OR slug = 'default') ORDER BY is_default DESC LIMIT 1",
            $theme_id, $archetype
        ), ARRAY_A );

        if ( $default_tpl && ! empty( $default_tpl['layout_data'] ) ) {
            $default_tpl['resolved_tier'] = 'archetype_default';
            return $default_tpl;
        }

        // Tier 3: Universal System Blueprint
        return array(
            'id'            => 0,
            'theme_id'      => $theme_id,
            'archetype'     => $archetype,
            'slug'          => 'system_fallback',
            'name'          => 'Universal System Baseline',
            'engine'        => 'html',
            'layout_data'   => $this->get_seed_blueprint( $archetype, 'default' ),
            'is_default'    => 1,
            'resolved_tier' => 'system_fallback',
        );
    }

    /**
     * Save / Update Template
     */
    public function save_template( $theme_id, $archetype, $slug, $name, $engine = 'html', $layout_data = '', $is_default = 0 ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_canvas_templates';
        $theme_id = intval( $theme_id );
        $now = current_time( 'mysql' );

        // Check if archetype is valid
        $archetypes = self::get_archetypes();
        if ( ! isset( $archetypes[ $archetype ] ) ) {
            return new WP_Error( 'invalid_archetype', 'Invalid template archetype specified.' );
        }

        // If setting as default, clear previous default flag for this archetype in theme
        if ( $is_default ) {
            $wpdb->update(
                $table_name,
                array( 'is_default' => 0 ),
                array( 'theme_id' => $theme_id, 'archetype' => $archetype ),
                array( '%d' ),
                array( '%d', '%s' )
            );
        }

        $existing = $this->get_template( $theme_id, $archetype, $slug );
        if ( $existing ) {
            $wpdb->update(
                $table_name,
                array(
                    'name'        => $name,
                    'engine'      => $engine,
                    'layout_data' => $layout_data,
                    'is_default'  => $is_default,
                    'updated_at'  => $now,
                ),
                array( 'id' => $existing['id'] ),
                array( '%s', '%s', '%s', '%d', '%s' ),
                array( '%d' )
            );
            return $this->get_template( $theme_id, $archetype, $slug );
        }

        $wpdb->insert(
            $table_name,
            array(
                'agency_id'   => 1,
                'theme_id'    => $theme_id,
                'archetype'   => $archetype,
                'slug'        => $slug,
                'name'        => $name,
                'engine'      => $engine,
                'layout_data' => $layout_data,
                'is_default'  => $is_default,
                'created_at'  => $now,
                'updated_at'  => $now,
            ),
            array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
        );

        return $this->get_template( $theme_id, $archetype, $slug );
    }

    /**
     * Delete custom template (defaults are protected)
     */
    public function delete_template( $template_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_canvas_templates';
        $tpl = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $template_id ), ARRAY_A );
        if ( ! $tpl ) {
            return new WP_Error( 'not_found', 'Template not found.' );
        }
        if ( ! empty( $tpl['is_default'] ) || $tpl['slug'] === 'default' ) {
            return new WP_Error( 'protected', 'Default archetype templates cannot be deleted.' );
        }

        $wpdb->delete( $table_name, array( 'id' => $template_id ), array( '%d' ) );
        return true;
    }

    /**
     * Seed initial default templates for a theme across all 5 archetypes
     */
    public function seed_default_templates_for_theme( $theme_id, $force = false ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_canvas_templates';
        $theme_id = intval( $theme_id );
        if ( ! $theme_id ) return;

        if ( ! $force ) {
            $count = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE theme_id = %d", $theme_id ) ) );
            if ( $count >= 5 ) {
                return; // Already seeded
            }
        }

        $theme = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE id = %d", $theme_id ), ARRAY_A );
        $settings = $theme ? ( json_decode( $theme['settings'], true ) ?: array() ) : array();
        $engine = ! empty( $settings['source'] ) ? $settings['source'] : 'html';

        $defaults = array(
            self::ARCHETYPE_ARTICLE => array(
                'slug' => 'default',
                'name' => 'Default Article Layout',
            ),
            self::ARCHETYPE_PAGE => array(
                'slug' => 'default',
                'name' => 'Default Standard Page',
            ),
            self::ARCHETYPE_POLICY => array(
                'slug' => 'default',
                'name' => 'Default Legal Policy',
            ),
            self::ARCHETYPE_PRODUCT => array(
                'slug' => 'default',
                'name' => 'Default Package & Service',
            ),
            self::ARCHETYPE_COLLECTION => array(
                'slug' => 'default',
                'name' => 'Default Catalog Grid',
            ),
        );

        foreach ( $defaults as $arch => $def ) {
            $exists = $this->get_template( $theme_id, $arch, $def['slug'] );
            if ( ! $exists ) {
                $layout = $this->get_seed_blueprint( $arch, $def['slug'] );
                $this->save_template( $theme_id, $arch, $def['slug'], $def['name'], $engine, $layout, 1 );
            }
        }
    }

    /**
     * Slot Compiler: Substitute dynamic tokens inside template markup
     */
    public function render_dynamic_slots( $template_html, $entity_data, $context = array() ) {
        if ( empty( $template_html ) ) {
            return '';
        }

        // 1. Process Conditionals: {{ #if key }} ... {{ /if }}
        $template_html = preg_replace_callback( '/\{\{\s*#if\s+([a-zA-Z0-9_\.]+)\s*\}\}([\s\S]*?)\{\{\s*\/if\s*\}\}/i', function( $matches ) use ( $entity_data ) {
            $key = $matches[1];
            $inner = $matches[2];
            $val = $this->extract_nested_value( $entity_data, $key );
            return ! empty( $val ) ? $inner : '';
        }, $template_html );

        // 2. Process Form Embeds: {{ cora_form_embed id=123 }}
        $template_html = preg_replace_callback( '/\{\{\s*cora_form_embed(?:\s+id=([0-9]+))?\s*\}\}/i', function( $matches ) use ( $entity_data ) {
            $form_id = ! empty( $matches[1] ) ? intval( $matches[1] ) : ( ! empty( $entity_data['connected_form_id'] ) ? intval( $entity_data['connected_form_id'] ) : 1 );
            return '<div class="cora-lead-form-embed-wrapper my-8 max-w-xl mx-auto p-6 bg-zinc-50 border border-zinc-200/80 rounded-2xl shadow-sm" data-form-id="' . esc_attr( $form_id ) . '">
                <div class="space-y-1.5 text-center mb-5">
                    <h4 class="text-sm font-bold text-zinc-950">Inquire & Book Session</h4>
                    <p class="text-xs text-zinc-500">Leave your project details below and our team will get in touch within 2 hours.</p>
                </div>
                <form class="space-y-3.5" data-cora-lead-form="' . esc_attr( $form_id ) . '" onsubmit="if(window.submitCoraLead){ event.preventDefault(); window.submitCoraLead(this); }">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-1">Full Name</label>
                        <input type="text" name="lead_name" required placeholder="e.g. Kavya Patel" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-xs outline-none focus:border-zinc-900 transition-colors" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-700 mb-1">Email</label>
                            <input type="email" name="lead_email" required placeholder="kavya@example.com" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-xs outline-none focus:border-zinc-900 transition-colors" />
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-700 mb-1">Phone</label>
                            <input type="tel" name="lead_phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-xs outline-none focus:border-zinc-900 transition-colors" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-700 mb-1">Project Details</label>
                        <textarea name="lead_notes" rows="3" placeholder="Tell us about your shoot requirements..." class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-xs outline-none focus:border-zinc-900 transition-colors resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold transition-all shadow-3xs cursor-pointer border-none">
                        Send Inquiry
                    </button>
                </form>
            </div>';
        }, $template_html );

        // 3. Process Simple Entity Tokens: {{ entity.key }}
        $template_html = preg_replace_callback( '/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/i', function( $matches ) use ( $entity_data, $context ) {
            $key = $matches[1];
            
            // Check in entity data
            $val = $this->extract_nested_value( $entity_data, $key );
            if ( null !== $val ) {
                return (string) $val;
            }

            // Check in context / workspace
            $val = $this->extract_nested_value( $context, $key );
            if ( null !== $val ) {
                return (string) $val;
            }

            // Built-in standard helpers
            if ( $key === 'site.year' || $key === 'current_year' ) {
                return date('Y');
            }

            return '';
        }, $template_html );

        return $template_html;
    }

    /**
     * Helper to extract nested values like "article.title" or "workspace.name"
     */
    private function extract_nested_value( $data, $key ) {
        if ( ! is_array( $data ) ) return null;
        if ( isset( $data[ $key ] ) ) return $data[ $key ];

        $parts = explode( '.', $key );
        $curr = $data;
        foreach ( $parts as $p ) {
            if ( is_array( $curr ) && isset( $curr[ $p ] ) ) {
                $curr = $curr[ $p ];
            } else {
                return null;
            }
        }
        return $curr;
    }

    /**
     * Entity Formatter: Article / Blog Post
     */
    public function format_article_entity( $post_obj ) {
        $p = is_numeric( $post_obj ) ? get_post( $post_obj ) : $post_obj;
        if ( ! $p ) return array();

        $featured_img_id = get_post_thumbnail_id( $p->ID );
        $featured_img_url = $featured_img_id ? wp_get_attachment_image_url( $featured_img_id, 'full' ) : '';
        $author_name = get_the_author_meta( 'display_name', $p->post_author ) ?: 'Studio Director';

        // Estimate read time
        $word_count = str_word_count( strip_tags( $p->post_content ) );
        $read_time = max( 1, ceil( $word_count / 200 ) ) . ' min read';

        return array(
            'id'             => $p->ID,
            'title'          => $p->post_title,
            'slug'           => $p->post_name,
            'excerpt'        => ! empty( $p->post_excerpt ) ? $p->post_excerpt : wp_trim_words( strip_tags( $p->post_content ), 30 ),
            'content'        => apply_filters( 'the_content', $p->post_content ),
            'featured_image' => $featured_img_url,
            'author'         => $author_name,
            'published_date' => get_the_date( 'd M Y', $p ),
            'read_time'      => $read_time,
            'article'        => array(
                'title'          => $p->post_title,
                'content'        => apply_filters( 'the_content', $p->post_content ),
                'excerpt'        => ! empty( $p->post_excerpt ) ? $p->post_excerpt : wp_trim_words( strip_tags( $p->post_content ), 30 ),
                'featured_image' => $featured_img_url,
                'author'         => $author_name,
                'published_date' => get_the_date( 'd M Y', $p ),
                'read_time'      => $read_time,
            ),
        );
    }

    /**
     * Entity Formatter: Canvas Standard Page
     */
    public function format_page_entity( $page_row ) {
        $wp_post_id = ! empty( $page_row['wp_post_id'] ) ? intval( $page_row['wp_post_id'] ) : 0;
        $wp_post = $wp_post_id ? get_post( $wp_post_id ) : null;
        $content = $wp_post ? apply_filters( 'the_content', $wp_post->post_content ) : '';

        return array(
            'id'       => $page_row['id'],
            'title'    => $page_row['title'],
            'slug'     => $page_row['slug'],
            'subtitle' => ! empty( $page_row['seo_description'] ) ? $page_row['seo_description'] : '',
            'content'  => $content,
            'page'     => array(
                'title'    => $page_row['title'],
                'slug'     => $page_row['slug'],
                'content'  => $content,
            ),
            'entity'   => array(
                'title'    => $page_row['title'],
                'slug'     => $page_row['slug'],
                'content'  => $content,
            ),
        );
    }

    /**
     * Entity Formatter: Legal Policy
     */
    public function format_policy_entity( $policy_data ) {
        if ( is_string( $policy_data ) ) {
            $policy_slug = sanitize_title( $policy_data );
            $title = ucwords( str_replace( array( '-', '_' ), ' ', $policy_slug ) );
            $content = '<p>This document governs the operational terms, rights, and responsibilities for all clients engaging with our studio services and facilities.</p><h3 class="text-base font-bold text-zinc-900 mt-4 mb-2">1. Scope of Agreement</h3><p>All bookings, media productions, and digital assets are subject to the verified timeline and equipment allocations specified at intake.</p><h3 class="text-base font-bold text-zinc-900 mt-4 mb-2">2. Cancellation & Rescheduling</h3><p>Cancellations made greater than 48 hours prior to call time are eligible for a full credit toward future studio bookings.</p>';
            $policy_data = array(
                'title'          => $title,
                'slug'           => $policy_slug,
                'content'        => $content,
                'effective_date' => date('d M Y'),
                'version'        => '1.2',
                'jurisdiction'   => 'Republic of India',
            );
        }

        return array(
            'id'             => ! empty( $policy_data['id'] ) ? $policy_data['id'] : 1,
            'title'          => $policy_data['title'],
            'slug'           => ! empty( $policy_data['slug'] ) ? $policy_data['slug'] : 'policy',
            'content'        => $policy_data['content'],
            'effective_date' => ! empty( $policy_data['effective_date'] ) ? $policy_data['effective_date'] : date('d M Y'),
            'version'        => ! empty( $policy_data['version'] ) ? $policy_data['version'] : '1.0',
            'jurisdiction'   => ! empty( $policy_data['jurisdiction'] ) ? $policy_data['jurisdiction'] : 'Republic of India',
            'entity'         => $policy_data,
        );
    }

    /**
     * Entity Formatter: Product / Package
     */
    public function format_product_entity( $product_data ) {
        if ( is_string( $product_data ) ) {
            $slug = sanitize_title( $product_data );
            $title = ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
            $product_data = array(
                'title'       => $title,
                'slug'        => $slug,
                'description' => 'Comprehensive studio session with professional lighting grids, backdrops, and dedicated equipment technician.',
                'price'       => '18,500',
                'gst_rate'    => '18%',
                'content'     => '<ul class="space-y-1.5 list-disc pl-4"><li>8 Hours Dedicated Soundstage Access</li><li>Godox / Profoto Strobe Lighting Kit</li><li>Zero-Reflection Cyclorama Wall</li><li>Live Monitoring Feed Station</li></ul>',
            );
        }

        return array(
            'id'          => ! empty( $product_data['id'] ) ? $product_data['id'] : 1,
            'title'       => $product_data['title'],
            'slug'        => ! empty( $product_data['slug'] ) ? $product_data['slug'] : 'package',
            'description' => ! empty( $product_data['description'] ) ? $product_data['description'] : '',
            'price'       => ! empty( $product_data['price'] ) ? $product_data['price'] : '0',
            'gst_rate'    => ! empty( $product_data['gst_rate'] ) ? $product_data['gst_rate'] : '18%',
            'content'     => ! empty( $product_data['content'] ) ? $product_data['content'] : '',
            'entity'      => $product_data,
        );
    }

    /**
     * Entity Formatter: Collection / Catalog
     */
    public function format_collection_entity( $collection_data ) {
        if ( is_string( $collection_data ) ) {
            $slug = sanitize_title( $collection_data );
            $title = ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
            
            // If collection is blog, pull recent posts
            $content_items = '';
            if ( in_array( $slug, array( 'blog', 'journal', 'articles' ), true ) ) {
                $posts = get_posts( array( 'numberposts' => 6, 'post_status' => 'publish' ) );
                foreach ( $posts as $p ) {
                    $feat = get_the_post_thumbnail_url( $p->ID, 'medium' ) ?: 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=600&q=80';
                    $content_items .= '<div class="bg-white border border-zinc-200/80 rounded-2xl overflow-hidden shadow-xs hover:border-zinc-400 transition-all group">';
                    $content_items .= '<img src="' . esc_url( $feat ) . '" alt="' . esc_attr( $p->post_title ) . '" class="w-full h-44 object-cover" />';
                    $content_items .= '<div class="p-5 space-y-2">';
                    $content_items .= '<h3 class="text-sm font-bold text-zinc-950 group-hover:underline"><a href="/site/' . esc_attr( $p->post_name ) . '">' . esc_html( $p->post_title ) . '</a></h3>';
                    $content_items .= '<p class="text-xs text-zinc-500 line-clamp-2">' . esc_html( wp_trim_words( strip_tags( $p->post_content ), 18 ) ) . '</p>';
                    $content_items .= '</div></div>';
                }
            }
            if ( empty( $content_items ) ) {
                $content_items = '<div class="col-span-full py-12 text-center text-xs text-zinc-400">No records found in this collection.</div>';
            }

            $collection_data = array(
                'title'   => $title,
                'slug'    => $slug,
                'content' => $content_items,
            );
        }

        return array(
            'id'      => ! empty( $collection_data['id'] ) ? $collection_data['id'] : 1,
            'title'   => $collection_data['title'],
            'slug'    => ! empty( $collection_data['slug'] ) ? $collection_data['slug'] : 'collection',
            'content' => ! empty( $collection_data['content'] ) ? $collection_data['content'] : '',
            'entity'  => $collection_data,
        );
    }

    /**
     * Render an entity with its resolved template
     */
    public function render_entity_template( $theme_id, $archetype, $entity_data, $template_slug = 'default', $context = array() ) {
        $resolved = $this->resolve_template( $theme_id, $archetype, $template_slug );
        $layout = ! empty( $resolved['layout_data'] ) ? $resolved['layout_data'] : '';
        if ( empty( $layout ) ) {
            $layout = $this->get_seed_blueprint( $archetype, $template_slug );
        }
        return $this->render_dynamic_slots( $layout, $entity_data, $context );
    }

    /**
     * Built-in production blueprints for each archetype
     */
    public function get_seed_blueprint( $archetype, $slug = 'default' ) {
        switch ( $archetype ) {
            case self::ARCHETYPE_ARTICLE:
                return '<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ article.title }} – Cora Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ["Inter", "sans-serif"], mono: ["JetBrains Mono", "monospace"] }
          }
        }
      }
    </script>
</head>
<body class="bg-[#FBFaf7] text-zinc-900 font-sans antialiased selection:bg-zinc-200">
    <!-- Navbar Navigation -->
    <header class="border-b border-zinc-200/80 bg-white/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="/" class="text-sm font-black uppercase tracking-tight text-zinc-950 no-underline">CORA STUDIO</a>
            <nav class="flex items-center gap-4 text-xs font-semibold text-zinc-600">
                <a href="/site" class="hover:text-zinc-950 no-underline">Home</a>
                <a href="/site/blog" class="text-zinc-950 font-bold no-underline">Journal</a>
                <a href="/site/contact" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg no-underline shadow-3xs transition-all">Book Session</a>
            </nav>
        </div>
    </header>

    <!-- Main Editorial Article Container -->
    <main class="max-w-3xl mx-auto px-4 py-12 sm:py-16">
        <div class="mb-4 flex items-center gap-2 text-[11px] font-mono text-zinc-400">
            <a href="/site/blog" class="hover:text-zinc-900 no-underline text-zinc-500">Journal</a>
            <span>/</span>
            <span>{{ article.read_time }}</span>
            <span>·</span>
            <span>{{ article.published_date }}</span>
        </div>

        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-zinc-950 tracking-tight leading-tight mb-6">
            {{ article.title }}
        </h1>

        <p class="text-base sm:text-lg text-zinc-600 leading-relaxed mb-8 font-normal">
            {{ article.excerpt }}
        </p>

        {{ #if article.featured_image }}
        <div class="my-8 rounded-2xl overflow-hidden border border-zinc-200/80 shadow-xs">
            <img src="{{ article.featured_image }}" alt="{{ article.title }}" class="w-full object-cover max-h-[460px]" />
        </div>
        {{ /if }}

        <!-- Dynamic Body Slot -->
        <div class="cora-article-content text-sm sm:text-base text-zinc-800 leading-relaxed space-y-5 border-t border-zinc-100 pt-8">
            {{ article.content }}
        </div>

        <!-- Integrated Dynamic Lead Capture Form Slot -->
        {{ cora_form_embed }}

        <!-- Editorial Author Footer -->
        <div class="mt-12 pt-8 border-t border-zinc-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-zinc-950 text-white font-bold text-xs flex items-center justify-center">S</div>
                <div>
                    <strong class="block text-xs font-bold text-zinc-900">{{ article.author }}</strong>
                    <span class="text-[11px] text-zinc-500">Editorial & Creative Direction</span>
                </div>
            </div>
            <a href="/site/blog" class="text-xs font-bold text-zinc-900 hover:underline">← Back to Articles</a>
        </div>
    </main>

    <!-- Workspace Footer -->
    <footer class="border-t border-zinc-200 bg-white py-10 mt-16 text-center text-xs text-zinc-500">
        <p>© {{ current_year }} Cora Studio Workspace. All rights reserved.</p>
    </footer>
</body>
</html>';

            case self::ARCHETYPE_PAGE:
                return '<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ page.title }} – Cora Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-[#FBFaf7] text-zinc-900 font-sans antialiased">
    <header class="border-b border-zinc-200/80 bg-white/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="/" class="text-sm font-black uppercase tracking-tight text-zinc-950 no-underline">CORA STUDIO</a>
            <nav class="flex items-center gap-4 text-xs font-semibold text-zinc-600">
                <a href="/site" class="hover:text-zinc-950 no-underline">Home</a>
                <a href="/site/contact" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg no-underline shadow-3xs">Inquire</a>
            </nav>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-12 sm:py-16">
        <div class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-zinc-950 tracking-tight mb-2">{{ page.title }}</h1>
            {{ #if page.subtitle }}
            <p class="text-sm text-zinc-500">{{ page.subtitle }}</p>
            {{ /if }}
        </div>

        <div class="cora-page-content bg-white border border-zinc-200/80 rounded-2xl p-6 sm:p-10 shadow-xs space-y-6 text-sm text-zinc-700 leading-relaxed">
            {{ page.content }}
        </div>

        {{ cora_form_embed }}
    </main>

    <footer class="border-t border-zinc-200 bg-white py-8 text-center text-xs text-zinc-500">
        <p>© {{ current_year }} Cora Studio. All rights reserved.</p>
    </footer>
</body>
</html>';

            case self::ARCHETYPE_POLICY:
                return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ entity.title }} – Legal Policy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="bg-[#FBFaf7] text-zinc-900 font-sans antialiased py-12 px-4">
    <article class="max-w-3xl mx-auto bg-white border border-zinc-200/90 rounded-2xl p-8 sm:p-12 shadow-sm space-y-6">
        <div class="border-b border-zinc-100 pb-4 flex items-center justify-between">
            <span class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400">LEGAL COMPLIANCE</span>
            <span class="text-xs font-mono text-zinc-500">Version 1.2 · Active</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-zinc-950">{{ entity.title }}</h1>
        <div class="cora-policy-body text-xs sm:text-sm text-zinc-700 leading-relaxed space-y-4">
            {{ entity.content }}
        </div>
        <div class="pt-6 border-t border-zinc-100 flex items-center justify-between text-[11px] text-zinc-400 font-mono">
            <span>Jurisdiction: Republic of India</span>
            <button onclick="window.print()" class="text-zinc-900 hover:underline cursor-pointer bg-transparent border-none p-0 font-bold">Print Policy ⎙</button>
        </div>
    </article>
</body>
</html>';

            case self::ARCHETYPE_PRODUCT:
                return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ entity.title }} – Studio Packages</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-[#FBFaf7] text-zinc-900 font-sans antialiased py-12 px-4">
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-xs space-y-4">
            <h1 class="text-2xl font-black text-zinc-950">{{ entity.title }}</h1>
            <p class="text-xs text-zinc-600 leading-relaxed">{{ entity.description }}</p>
            <div class="p-4 bg-zinc-50 rounded-xl border border-zinc-100 space-y-1">
                <span class="text-[10px] font-mono uppercase text-zinc-400">Starting At</span>
                <div class="text-2xl font-black text-zinc-950">₹{{ entity.price }} <span class="text-xs font-normal text-zinc-500">+ 18% GST</span></div>
            </div>
            <div class="cora-specs-slot space-y-2 text-xs text-zinc-700">
                {{ entity.content }}
            </div>
        </div>
        <div>
            {{ cora_form_embed }}
        </div>
    </div>
</body>
</html>';

            case self::ARCHETYPE_COLLECTION:
                return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ entity.title }} – Directory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-[#FBFaf7] text-zinc-900 font-sans antialiased py-12 px-4">
    <div class="max-w-5xl mx-auto space-y-8">
        <header class="text-center space-y-2">
            <h1 class="text-3xl font-black text-zinc-950">{{ entity.title }}</h1>
            <p class="text-xs text-zinc-500">Explore curated records and collections</p>
        </header>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {{ entity.content }}
        </div>
    </div>
</body>
</html>';
        }
        return '';
    }
}

// Initialize engine singleton
Cora_Template_Engine::instance();
