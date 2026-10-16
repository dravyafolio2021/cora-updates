<?php
/**
 * Cora Growth Platform — Projects & Portfolio Case Studies Module
 *
 * Provides workspace-level database schema, REST API bridge, and CRUD AJAX endpoints
 * for managing agency portfolio showcases and publishing to Next.js frontend.
 *
 * @package Cora_Workspace
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Cora_Projects_Module' ) ) {

class Cora_Projects_Module {

    /**
     * Initialize hooks and table schema.
     */
    public static function init() {
        // Table initialization hook
        add_action( 'init', array( __CLASS__, 'setup_database_table' ), 5 );
        add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ) );

        // AJAX Handlers
        add_action( 'wp_ajax_cora_ajax_get_projects', array( __CLASS__, 'ajax_get_projects' ) );
        add_action( 'wp_ajax_cora_get_projects', array( __CLASS__, 'ajax_get_projects' ) );
        add_action( 'wp_ajax_cora_ajax_save_project', array( __CLASS__, 'ajax_save_project' ) );
        add_action( 'wp_ajax_cora_save_project', array( __CLASS__, 'ajax_save_project' ) );
        add_action( 'wp_ajax_cora_ajax_delete_project', array( __CLASS__, 'ajax_delete_project' ) );
        add_action( 'wp_ajax_cora_delete_project', array( __CLASS__, 'ajax_delete_project' ) );
        add_action( 'wp_ajax_cora_ajax_toggle_project_publish', array( __CLASS__, 'ajax_toggle_project_publish' ) );
        add_action( 'wp_ajax_cora_toggle_project_publish', array( __CLASS__, 'ajax_toggle_project_publish' ) );
        add_action( 'wp_ajax_cora_ajax_duplicate_project', array( __CLASS__, 'ajax_duplicate_project' ) );
        add_action( 'wp_ajax_cora_duplicate_project', array( __CLASS__, 'ajax_duplicate_project' ) );
        add_action( 'wp_ajax_cora_ajax_seed_starter_projects', array( __CLASS__, 'ajax_seed_starter_projects' ) );
        add_action( 'wp_ajax_cora_seed_starter_projects', array( __CLASS__, 'ajax_seed_starter_projects' ) );
    }

    /**
     * Resolve current workspace agency ID.
     */
    public static function get_current_agency_id() {
        if ( isset( $_POST['agency_id'] ) && is_numeric( $_POST['agency_id'] ) ) {
            return strval( intval( $_POST['agency_id'] ) );
        }
        if ( isset( $_GET['agency_id'] ) && is_numeric( $_GET['agency_id'] ) ) {
            return strval( intval( $_GET['agency_id'] ) );
        }
        if ( isset( $_REQUEST['agency_id'] ) && is_numeric( $_REQUEST['agency_id'] ) ) {
            return strval( intval( $_REQUEST['agency_id'] ) );
        }
        if ( function_exists( 'cora_db_get_agency_id' ) ) {
            $id = cora_db_get_agency_id();
            if ( ! empty( $id ) ) {
                return strval( $id );
            }
        }
        return '1';
    }

    /**
     * Database setup & table migration.
     */
    public static function setup_database_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            agency_id varchar(100) NOT NULL DEFAULT '1',
            client_id bigint(20) unsigned NOT NULL DEFAULT 0,
            title varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            subtitle varchar(255) DEFAULT '',
            client_name varchar(255) NOT NULL,
            client_logo_url text DEFAULT NULL,
            client_industry varchar(100) NOT NULL DEFAULT 'Creative Production',
            project_duration varchar(100) DEFAULT '',
            year_completed varchar(20) DEFAULT '',
            services longtext DEFAULT NULL,
            live_url varchar(500) DEFAULT '',
            project_links longtext DEFAULT NULL,
            is_confidential tinyint(1) NOT NULL DEFAULT 0,
            is_featured tinyint(1) NOT NULL DEFAULT 0,
            status varchar(20) NOT NULL DEFAULT 'published',
            executive_summary text DEFAULT NULL,
            challenge_content longtext DEFAULT NULL,
            solution_content longtext DEFAULT NULL,
            deliverables longtext DEFAULT NULL,
            kpis longtext DEFAULT NULL,
            testimonial_quote text DEFAULT NULL,
            testimonial_author varchar(255) DEFAULT '',
            testimonial_role varchar(255) DEFAULT '',
            testimonial_company varchar(255) DEFAULT '',
            testimonial_avatar text DEFAULT NULL,
            testimonial_verified tinyint(1) NOT NULL DEFAULT 1,
            hero_media_type varchar(20) NOT NULL DEFAULT 'image',
            hero_image_url text DEFAULT NULL,
            hero_video_url text DEFAULT NULL,
            gallery_images longtext DEFAULT NULL,
            before_image_url text DEFAULT NULL,
            before_label varchar(100) DEFAULT 'Previous Baseline',
            after_image_url text DEFAULT NULL,
            after_label varchar(100) DEFAULT 'Studio Deliverable',
            video_embed_url text DEFAULT NULL,
            cta_type varchar(50) NOT NULL DEFAULT 'book_audit',
            cta_title varchar(255) DEFAULT 'Ready for a Custom Production?',
            cta_subtitle text DEFAULT NULL,
            cta_button_text varchar(100) DEFAULT 'Request Consultation',
            cta_button_url varchar(500) DEFAULT '/contact',
            meta_title varchar(255) DEFAULT '',
            meta_description text DEFAULT NULL,
            target_keywords text DEFAULT NULL,
            canonical_url varchar(500) DEFAULT '',
            views_count int(11) NOT NULL DEFAULT 0,
            cta_clicks int(11) NOT NULL DEFAULT 0,
            sort_order int(11) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY uq_slug (slug),
            KEY idx_agency_status (agency_id, status),
            KEY idx_industry (client_industry),
            KEY idx_featured (is_featured)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        // Ensure client_id, project_links and story_type columns exist on existing installations
        $col_client_id_check = $wpdb->get_results( "SHOW COLUMNS FROM {$table_name} LIKE 'client_id'" );
        if ( empty( $col_client_id_check ) ) {
            $wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN client_id bigint(20) unsigned NOT NULL DEFAULT 0 AFTER agency_id" );
        }
        $col_check = $wpdb->get_results( "SHOW COLUMNS FROM {$table_name} LIKE 'project_links'" );
        if ( empty( $col_check ) ) {
            $wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN project_links longtext DEFAULT NULL AFTER live_url" );
        }
        $col_story_check = $wpdb->get_results( "SHOW COLUMNS FROM {$table_name} LIKE 'story_type'" );
        if ( empty( $col_story_check ) ) {
            $wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN story_type varchar(50) DEFAULT 'pas' AFTER executive_summary" );
        }

        // Migrate any legacy 'super' records to proper workspace IDs
        self::migrate_legacy_records();

        // Seed sample projects for major demo workspaces if they lack records
        self::seed_sample_case_studies();
    }

    /**
     * Migrate legacy records that had agency_id = 'super'.
     */
    public static function migrate_legacy_records() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        // Check if there are legacy 'super' rows
        $legacy_rows = $wpdb->get_results( "SELECT id, slug FROM {$table_name} WHERE agency_id = 'super'", ARRAY_A );
        if ( ! empty( $legacy_rows ) ) {
            foreach ( $legacy_rows as $row ) {
                if ( $row['slug'] === 'enterprise-rag-real-estate-automation' ) {
                    $wpdb->update( $table_name, array( 'agency_id' => '1' ), array( 'id' => $row['id'] ) );
                } else {
                    $wpdb->update( $table_name, array( 'agency_id' => '3' ), array( 'id' => $row['id'] ) );
                }
            }
        }
    }

    /**
     * Seed initial showcase records partitioned strictly by workspace (agency_id).
     */
    public static function seed_sample_case_studies() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        // 1. Photography Studio Workspace (agency_id = '2')
        $studio_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE agency_id = %s", '2' ) );
        if ( intval( $studio_count ) === 0 ) {
            self::seed_photography_studio_projects( '2' );
        }

        // 2. Real Estate Workspace (agency_id = '1')
        $re_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE agency_id = %s", '1' ) );
        if ( intval( $re_count ) === 0 ) {
            self::seed_real_estate_projects( '1' );
        }

        // 3. Marketing Agency Workspace (agency_id = '3')
        $marketing_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE agency_id = %s", '3' ) );
        if ( intval( $marketing_count ) === 0 ) {
            self::seed_marketing_agency_projects( '3' );
        }
    }

    /**
     * Seed Photography Studio showcase projects (Workspace 2).
     */
    public static function seed_photography_studio_projects( $agency_id = '2' ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        // 1. Vogue India Fashion Lookbook
        $wpdb->replace( $table_name, array(
            'agency_id'            => $agency_id,
            'title'                => 'Vogue India Editorial Lookbook & High-Fashion Autumn Campaign',
            'slug'                 => 'vogue-fashion-editorial-lookbook',
            'subtitle'             => 'Full creative direction, high-fashion styling, and editorial retouching for luxury apparel.',
            'client_name'          => 'Verve Couture',
            'client_industry'      => 'Fashion & Editorial Photography',
            'project_duration'     => '3-Day Production Sprint',
            'year_completed'       => '2026',
            'services'             => json_encode( array( 'Creative Direction', 'High-Fashion Editorial Stills', 'High-End Beauty Retouching', 'Lookbook Curation' ) ),
            'live_url'             => 'https://vervecouture.demo',
            'is_confidential'      => 0,
            'is_featured'          => 1,
            'status'               => 'published',
            'executive_summary'    => 'Verve Couture required an elevated, monochromatic Autumn lookbook with high-fidelity color grading to launch their flagship luxury line across international stockists.',
            'challenge_content'    => 'Tight pre-production deadlines and complex multi-model set changes demanded instant tethered proofing and flawless light-modelling on location.',
            'solution_content'     => 'We executed a full studio and outdoor architecture shoot with instant iPad proofing, followed by precision non-destructive frequency separation for print and digital editions.',
            'deliverables'         => json_encode( array(
                '85 High-Resolution Color-Graded Stills',
                'Full-Page Print Advertising Deliverables',
                'Digital E-Commerce Catalog Layouts',
                'Behind-the-Scenes 4K B-Roll Reel'
            ) ),
            'kpis'                 => json_encode( array(
                array( 'value' => '1.2M', 'label' => 'Lookbook Impressions', 'context' => 'Organic social and print reach', 'trend' => 'up' ),
                array( 'value' => '48h', 'label' => 'Stills Turnaround', 'context' => 'Same-week retouched release', 'trend' => 'down' ),
                array( 'value' => '100%', 'label' => 'On-Time Delivery', 'context' => 'Delivered ahead of Fashion Week', 'trend' => 'up' )
            ) ),
            'testimonial_quote'    => 'The aesthetic precision and lighting execution blew us away. Cora Studio captured the exact high-fashion editorial mood our brand demanded.',
            'testimonial_author'   => 'Kavya Patel',
            'testimonial_role'     => 'Lead Fashion Director',
            'testimonial_company'  => 'Verve Couture',
            'testimonial_verified' => 1,
            'hero_media_type'      => 'image',
            'hero_image_url'       => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1600&q=80',
            'gallery_images'       => json_encode( array(
                array( 'url' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80', 'caption' => 'Autumn Editorial Stills & Studio Lighting' ),
                array( 'url' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=1200&q=80', 'caption' => 'On-Location Architectural Monochromatic Composition' )
            ) ),
            'cta_type'             => 'book_audit',
            'cta_title'            => 'Planning an Editorial Fashion Shoot?',
            'cta_subtitle'         => 'Review our production schedule and secure studio shoot dates.',
            'cta_button_text'      => 'Book Creative Consultation',
            'cta_button_url'       => '/contact',
            'meta_title'           => 'Vogue Editorial Lookbook | Cora Photography Studio',
            'meta_description'     => 'High-fashion editorial production and retouching case study by Cora Studio.',
            'views_count'          => 4180,
            'cta_clicks'           => 312,
            'sort_order'           => 1,
            'created_at'           => current_time( 'mysql' ),
            'updated_at'           => current_time( 'mysql' ),
        ) );

        // 2. Royal Udaipur Destination Wedding Film
        $wpdb->replace( $table_name, array(
            'agency_id'            => $agency_id,
            'title'                => 'Royal Udaipur Destination Wedding & Cinematic 8K Film',
            'slug'                 => 'royal-udaipur-destination-wedding',
            'subtitle'             => 'Three-day palace celebrations captured in master color-graded stills and cinema-grade film.',
            'client_name'          => 'The Singhania Family',
            'client_industry'      => 'Luxury Wedding & Cinematography',
            'project_duration'     => '3-Day Royal Celebration',
            'year_completed'       => '2026',
            'services'             => json_encode( array( 'Multi-Camera 8K Film', 'Drone Aerial Cinematography', 'Same-Day Edit (SDE)', 'Handcrafted Leather Albums' ) ),
            'live_url'             => 'https://singhaniawedding.demo',
            'is_confidential'      => 0,
            'is_featured'          => 1,
            'status'               => 'published',
            'executive_summary'    => 'A 3-day royal destination wedding at Jagmandir Island Palace requiring cinematic aerial footage, multi-camera lighting, and instant evening trailer delivery for 600 international guests.',
            'challenge_content'    => 'Coordinating an 8-person crew across multiple island venues while respecting traditional ceremonies and delivering a same-day edit trailer before midnight.',
            'solution_content'     => 'We utilized dedicated high-speed mobile DIT stations on-site with simultaneous multi-camera ingesting and LUT color grading to premiere the film at the reception dinner.',
            'deliverables'         => json_encode( array(
                'Feature-Length 45-Minute 4K Film',
                'Same-Day Edit (SDE) Evening Teaser',
                'Curated 400-Still Leather Bound Album',
                'Private Password-Protected Gallery Portal'
            ) ),
            'kpis'                 => json_encode( array(
                array( 'value' => '380+', 'label' => 'Master Color-Graded Stills', 'context' => 'Delivered in private client portal', 'trend' => 'up' ),
                array( 'value' => '18M', 'label' => 'Social Reel Views', 'context' => 'Viral wedding teaser reel', 'trend' => 'up' ),
                array( 'value' => '5/5', 'label' => 'Client Satisfaction', 'context' => 'Verified family review', 'trend' => 'up' )
            ) ),
            'testimonial_quote'    => 'Watching our wedding film felt like watching a timeless cinema feature. Every tear and laugh was preserved with magnificent elegance.',
            'testimonial_author'   => 'Aarav Mehta',
            'testimonial_role'     => 'Family Representative',
            'testimonial_company'  => 'Singhania Family Estate',
            'testimonial_verified' => 1,
            'hero_media_type'      => 'image',
            'hero_image_url'       => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1600&q=80',
            'gallery_images'       => json_encode( array(
                array( 'url' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=1200&q=80', 'caption' => 'Royal Palace Sunset Reception Stills' )
            ) ),
            'cta_type'             => 'book_audit',
            'cta_title'            => 'Preserve Your Destination Wedding In Cinema Quality',
            'cta_subtitle'         => 'Schedule an inquiry call with our executive cinematography directors.',
            'cta_button_text'      => 'Check Date Availability',
            'cta_button_url'       => '/contact',
            'meta_title'           => 'Royal Udaipur Wedding Cinematography | Cora Studio',
            'meta_description'     => 'Luxury wedding photography and 8K cinema case study.',
            'views_count'          => 3210,
            'cta_clicks'           => 240,
            'sort_order'           => 2,
            'created_at'           => current_time( 'mysql' ),
            'updated_at'           => current_time( 'mysql' ),
        ) );

        // 3. Oberoi Heritage Hospitality Architectural Interior
        $wpdb->replace( $table_name, array(
            'agency_id'            => $agency_id,
            'title'                => 'Architectural Interior & Heritage Hospitality Portfolio',
            'slug'                 => 'oberoi-heritage-hospitality-portfolio',
            'subtitle'             => 'Twilight HDR architectural documentation and interior licensing for a luxury heritage resort.',
            'client_name'          => 'Oberoi Heritage Retreats',
            'client_industry'      => 'Commercial Architecture & Hospitality',
            'project_duration'     => '5-Day On-Site Production',
            'year_completed'       => '2026',
            'services'             => json_encode( array( 'Twilight HDR Architecture', 'Virtual 3D Spatial Walkthrough', 'Interior Editorial Licensing', 'Commercial Print Collateral' ) ),
            'live_url'             => 'https://oberoiheritage.demo',
            'is_confidential'      => 0,
            'is_featured'          => 1,
            'status'               => 'published',
            'executive_summary'    => 'Oberoi Heritage commissioned complete visual renewal of their 32 suites, dining pavilions, and courtyard pools to elevate direct international bookings.',
            'challenge_content'    => 'Balancing natural daylight with delicate historic interior lighting without blowing out stone textures or window vistas.',
            'solution_content'     => 'We employed 9-bracket exposure blending and architectural tilt-shift optics to create true-to-life dimensional lighting throughout the property.',
            'deliverables'         => json_encode( array(
                '60 Architectural HDR Stills',
                '360 Virtual Interactive Tour',
                'International Press Kit Photography',
                'Architectural Digest Feature Licensing'
            ) ),
            'kpis'                 => json_encode( array(
                array( 'value' => '+44%', 'label' => 'Direct Booking Lift', 'context' => 'Attributed to visual website revamp', 'trend' => 'up' ),
                array( 'value' => '14', 'label' => 'Architectural Digest Features', 'context' => 'Earned global press coverage', 'trend' => 'up' ),
                array( 'value' => '100%', 'label' => 'Spatial Coverage', 'context' => 'All 32 suites and dining pavilions', 'trend' => 'up' )
            ) ),
            'testimonial_quote'    => 'The twilight perspectives transformed how prospective international travelers experience our property online before booking.',
            'testimonial_author'   => 'Rohan Verma',
            'testimonial_role'     => 'Director of Hospitality',
            'testimonial_company'  => 'Oberoi Heritage Retreats',
            'testimonial_verified' => 1,
            'hero_media_type'      => 'image',
            'hero_image_url'       => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1600&q=80',
            'gallery_images'       => json_encode( array(
                array( 'url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'caption' => 'Courtyard Swimming Pool Twilight Architecture' )
            ) ),
            'cta_type'             => 'book_audit',
            'cta_title'            => 'Elevate Your Property Visuals',
            'cta_subtitle'         => 'Commission commercial architectural documentation for your hospitality property.',
            'cta_button_text'      => 'Request Architecture Estimate',
            'cta_button_url'       => '/contact',
            'meta_title'           => 'Hospitality Architecture Photography | Cora Studio',
            'meta_description'     => 'Commercial architecture and interior documentation showcase.',
            'views_count'          => 2890,
            'cta_clicks'           => 185,
            'sort_order'           => 3,
            'created_at'           => current_time( 'mysql' ),
            'updated_at'           => current_time( 'mysql' ),
        ) );
    }

    /**
     * Seed Real Estate showcase projects (Workspace 1).
     */
    public static function seed_real_estate_projects( $agency_id = '1' ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        $wpdb->replace( $table_name, array(
            'agency_id'            => $agency_id,
            'title'                => 'Ultra-Luxury Penthouse Collection Showcase & Virtual VR Tours',
            'slug'                 => 'luxury-penthouse-vr-showcase',
            'subtitle'             => 'High-yield interactive 3D Matterport VR tours and cinematic drone captures for prime residences.',
            'client_name'          => 'DLF Horizon Towers',
            'client_industry'      => 'Luxury Real Estate',
            'project_duration'     => '30-Day Sprint',
            'year_completed'       => '2026',
            'services'             => json_encode( array( '3D Matterport VR Tours', 'Drone Skyline Cinematography', 'High-Yield HNW Landing Hub', 'Private Showing Portal' ) ),
            'live_url'             => 'https://dlfhorizon.demo',
            'is_confidential'      => 0,
            'is_featured'          => 1,
            'status'               => 'published',
            'executive_summary'    => 'DLF Horizon Towers launched an exclusive collection of penthouses targeting international HNW buyers who could not tour properties in person.',
            'challenge_content'    => 'Standard 2D photos failed to convey double-height ceiling volumes and skyline vistas, causing slow sales cycles for ultra-luxury units.',
            'solution_content'     => 'We engineered fully immersive 3D spatial walkthroughs paired with 8K dusk drone cinematography and an invitation-only client viewing room.',
            'deliverables'         => json_encode( array(
                'Interactive 3D Digital Twin Model',
                'Cinematic 8K Twilight Aerial Video',
                'High-Resolution Editorial Property Dossier',
                'Private HNW Buyer Viewing Room'
            ) ),
            'kpis'                 => json_encode( array(
                array( 'value' => '$38M', 'label' => 'Inventory Closed', 'context' => 'Sold within 60 days of launch', 'trend' => 'up' ),
                array( 'value' => '14 Days', 'label' => 'Avg Days on Market', 'context' => 'Down from 92 days average', 'trend' => 'down' ),
                array( 'value' => '8.4x', 'label' => 'HNW Virtual Tour Engagement', 'context' => 'Global remote buyers', 'trend' => 'up' )
            ) ),
            'testimonial_quote'    => 'The virtual walkthroughs allowed international buyers to write offers without boarding a plane. It changed how we market luxury inventory.',
            'testimonial_author'   => 'Rohan Verma',
            'testimonial_role'     => 'Managing Partner',
            'testimonial_company'  => 'DLF Horizon Towers',
            'testimonial_verified' => 1,
            'hero_media_type'      => 'image',
            'hero_image_url'       => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=80',
            'cta_type'             => 'book_audit',
            'cta_title'            => 'Ready to Accelerate Luxury Property Sales?',
            'cta_subtitle'         => 'Explore our digital twin and immersive presentation solutions.',
            'cta_button_text'      => 'Schedule Property Strategy Call',
            'cta_button_url'       => '/contact',
            'views_count'          => 3940,
            'cta_clicks'           => 260,
            'sort_order'           => 1,
            'created_at'           => current_time( 'mysql' ),
            'updated_at'           => current_time( 'mysql' ),
        ) );

        $wpdb->replace( $table_name, array(
            'agency_id'            => $agency_id,
            'title'                => 'Enterprise AI Knowledge Retrieval Engine for Multi-Branch Real Estate',
            'slug'                 => 'enterprise-rag-real-estate-automation',
            'subtitle'             => 'Living AI memory layer and automated lead qualification for 1,200+ property advisors.',
            'client_name'          => 'Stealth Realty Enterprise',
            'client_industry'      => 'Real Estate / PropTech',
            'project_duration'     => '45-Day Sprint',
            'year_completed'       => '2026',
            'services'             => json_encode( array( 'AI Agent Development', 'RAG Knowledge Architecture', 'CRM & Lead Automation', 'Enterprise Security' ) ),
            'live_url'             => 'https://stealthrealty.demo',
            'is_confidential'      => 1,
            'is_featured'          => 1,
            'status'               => 'published',
            'executive_summary'    => 'A national real estate network handling 40,000 monthly inquiries struggled with slow agent responses. Cora deployed a localized RAG Knowledge Base and autonomous qualification agent.',
            'challenge_content'    => 'With 1,200 brokers across 18 regional hubs, new property updates took days to circulate, leading to missed showings and high inquiry fatigue.',
            'solution_content'     => 'We ingested all active listings and floor plans into a real-time vector store with an autonomous WhatsApp/Web concierge that qualifies leads 24/7.',
            'deliverables'         => json_encode( array(
                'Multi-Branch RAG Knowledge Base Architecture',
                '24/7 AI Lead Qualification & Calendar Sync Agent',
                'Real-Time Property Telemetry & Broker App',
                'Enterprise Role-Based Access Governance (RBAC)'
            ) ),
            'kpis'                 => json_encode( array(
                array( 'value' => '< 15s', 'label' => 'Inquiry Response Time', 'context' => 'Down from 4.8 hours median manual turnaround', 'trend' => 'down' ),
                array( 'value' => '+68%', 'label' => 'Showing Attendance Rate', 'context' => 'Automated WhatsApp reminder and routing flows', 'trend' => 'up' ),
                array( 'value' => '94%', 'label' => 'Lead Qualification Accuracy', 'context' => 'Verified against senior broker manual audit', 'trend' => 'up' )
            ) ),
            'testimonial_quote'    => 'Deploying this AI knowledge system transformed our operation. Our advisors now spend their day closing high-value deals with pre-screened clients.',
            'testimonial_author'   => 'Aarav Mehta',
            'testimonial_role'     => 'Senior Partner',
            'testimonial_company'  => 'Confidential Real Estate Network',
            'testimonial_verified' => 1,
            'hero_media_type'      => 'image',
            'hero_image_url'       => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1600&q=80',
            'cta_type'             => 'book_audit',
            'cta_title'            => 'Want Custom AI Systems in Your Agency?',
            'cta_subtitle'         => 'Schedule a discovery session to evaluate how autonomous AI systems streamline operations.',
            'cta_button_text'      => 'Schedule AI Architecture Session',
            'cta_button_url'       => '/contact',
            'views_count'          => 1980,
            'cta_clicks'           => 142,
            'sort_order'           => 2,
            'created_at'           => current_time( 'mysql' ),
            'updated_at'           => current_time( 'mysql' ),
        ) );
    }

    /**
     * Seed Marketing Agency showcase projects (Workspace 3).
     */
    public static function seed_marketing_agency_projects( $agency_id = '3' ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        $wpdb->replace( $table_name, array(
            'agency_id'            => $agency_id,
            'title'                => 'Scaling B2B FinTech Pipeline 340% via Full-Funnel SEO & Paid Media',
            'slug'                 => 'fintech-pipeline-scale',
            'subtitle'             => 'High-intent programmatic search and targeted LinkedIn ads for an enterprise treasury platform.',
            'client_name'          => 'Apex Financial Technologies',
            'client_industry'      => 'FinTech / B2B SaaS',
            'project_duration'     => '90-Day Sprint',
            'year_completed'       => '2026',
            'services'             => json_encode( array( 'SEO & GEO Strategy', 'Performance Paid Media', 'CRO & Landing Pages', 'Design System Architecture' ) ),
            'live_url'             => 'https://apexfinancial.demo',
            'is_confidential'      => 0,
            'is_featured'          => 1,
            'status'               => 'published',
            'executive_summary'    => 'Apex Financial experienced high CAC and stagnant organic discovery. Cora engineered a multi-layered programmatic SEO architecture paired with high-intent LinkedIn demand generation.',
            'challenge_content'    => 'Before engaging our agency, Apex Financial relied on expensive broad search keywords with fewer than 12 organic demos/mo.',
            'solution_content'     => 'We built 48 high-intent comparison hubs with sub-second page performance and structured bespoke LinkedIn campaigns for CFOs.',
            'deliverables'         => json_encode( array(
                'Programmatic Content Architecture (48 Landing Hubs)',
                'High-Intent LinkedIn Demand Generation Campaigns',
                'Frictionless 2-Step Interactive Demo Funnel',
                'Executive Attribution & Pipeline Telemetry Dashboard'
            ) ),
            'kpis'                 => json_encode( array(
                array( 'value' => '+340%', 'label' => 'Organic Revenue Pipeline', 'context' => 'Within 90 days of cluster release', 'trend' => 'up' ),
                array( 'value' => '3.8x', 'label' => 'Blended Paid ROAS', 'context' => 'Scaled monthly spend efficiently', 'trend' => 'up' ),
                array( 'value' => '-42%', 'label' => 'Qualified Cost Per Acquisition', 'context' => 'Reduced from $480 to $278 per SQL', 'trend' => 'down' )
            ) ),
            'testimonial_quote'    => 'The transformation in our inbound pipeline was immediate. We went from chasing unqualified leads to having enterprise CFOs booking audits on our website every single week.',
            'testimonial_author'   => 'Aarav Mehta',
            'testimonial_role'     => 'VP of Global Growth',
            'testimonial_company'  => 'Apex Financial Technologies',
            'testimonial_verified' => 1,
            'hero_media_type'      => 'image',
            'hero_image_url'       => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1600&q=80',
            'cta_type'             => 'book_audit',
            'cta_title'            => 'Ready to Expand Your Inbound Pipeline?',
            'cta_subtitle'         => 'Book a 30-minute growth teardown with our senior strategy team.',
            'cta_button_text'      => 'Schedule Strategy Audit',
            'cta_button_url'       => '/contact',
            'views_count'          => 3420,
            'cta_clicks'           => 218,
            'sort_order'           => 1,
            'created_at'           => current_time( 'mysql' ),
            'updated_at'           => current_time( 'mysql' ),
        ) );

        $wpdb->replace( $table_name, array(
            'agency_id'            => $agency_id,
            'title'                => 'Modernizing D2C Brand Identity & E-Commerce Speed for 4.2x Conversions',
            'slug'                 => 'd2c-luxury-ecommerce-overhaul',
            'subtitle'             => 'Headless Next.js storefront rebuild, editorial design system, and multi-channel retention loops.',
            'client_name'          => 'Aura Living Collection',
            'client_industry'      => 'E-Commerce / Luxury Living',
            'project_duration'     => '60-Day Sprint',
            'year_completed'       => '2026',
            'services'             => json_encode( array( 'Headless Next.js Development', 'Brand Identity & Visuals', 'Shopify Plus Migration', 'Retention Automation' ) ),
            'live_url'             => 'https://auraliving.demo',
            'is_confidential'      => 0,
            'is_featured'          => 1,
            'status'               => 'published',
            'executive_summary'    => 'Aura Living was hindered by an outdated monolithic theme causing 5.4-second load times on mobile devices and high cart abandonment.',
            'challenge_content'    => 'Slow mobile page rendering and lack of brand cohesion caused high drop-off during peak sales hours.',
            'solution_content'     => 'We built a headless Next.js storefront with sub-420ms speed, editorial typography, and behavioral replenishment loops.',
            'deliverables'         => json_encode( array(
                'Custom Headless Next.js Storefront',
                'Editorial Monochromatic Design Tokens & UI Library',
                'Shopify Plus Checkout Extensibility Integration',
                'Automated Lifecycle Email & SMS Trigger Engine'
            ) ),
            'kpis'                 => json_encode( array(
                array( 'value' => '4.2x', 'label' => 'Mobile Conversion Rate', 'context' => 'Increase in completed checkout transactions', 'trend' => 'up' ),
                array( 'value' => '420ms', 'label' => 'Average Mobile Load Speed', 'context' => 'Down from 5.4s on legacy platform', 'trend' => 'down' ),
                array( 'value' => '+184%', 'label' => 'Repeat Purchase Revenue', 'context' => 'Driven by automated lifecycle retention loops', 'trend' => 'up' )
            ) ),
            'testimonial_quote'    => 'Our storefront feels as elevated as our physical showrooms now. The instantaneous speed on mobile phones unlocked a surge in conversions.',
            'testimonial_author'   => 'Kavya Patel',
            'testimonial_role'     => 'Creative Director & Founder',
            'testimonial_company'  => 'Aura Living Collection',
            'testimonial_verified' => 1,
            'hero_media_type'      => 'image',
            'hero_image_url'       => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1600&q=80',
            'cta_type'             => 'book_audit',
            'cta_title'            => 'Ready to Accelerate Storefront Speed & Conversions?',
            'cta_subtitle'         => 'Request a technical speed and conversion audit with our engineering team.',
            'cta_button_text'      => 'Request Storefront Audit',
            'cta_button_url'       => '/contact',
            'views_count'          => 2840,
            'cta_clicks'           => 176,
            'sort_order'           => 2,
            'created_at'           => current_time( 'mysql' ),
            'updated_at'           => current_time( 'mysql' ),
        ) );
    }

    /**
     * Register public REST routes for Next.js frontend.
     */
    public static function register_rest_routes() {
        register_rest_route( 'cora-api/v1', '/projects', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'rest_get_projects' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'cora-api/v1', '/projects/(?P<slug>[a-zA-Z0-9_-]+)', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'rest_get_project_by_slug' ),
            'permission_callback' => '__return_true',
        ) );
    }

    /**
     * REST: Fetch published projects (can filter by agency_id).
     */
    public static function rest_get_projects( $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        $agency_id = $request->get_param( 'agency_id' ) ?: $request->get_param( 'workspace' );

        if ( ! empty( $agency_id ) ) {
            $results = $wpdb->get_results(
                $wpdb->prepare( "SELECT * FROM {$table_name} WHERE status = 'published' AND agency_id = %s ORDER BY is_featured DESC, sort_order ASC, created_at DESC", $agency_id ),
                ARRAY_A
            );
        } else {
            $results = $wpdb->get_results(
                "SELECT * FROM {$table_name} WHERE status = 'published' ORDER BY is_featured DESC, sort_order ASC, created_at DESC",
                ARRAY_A
            );
        }

        $formatted = array_map( array( __CLASS__, 'format_project_for_api' ), $results );
        return rest_ensure_response( $formatted );
    }

    /**
     * REST: Fetch single project by slug.
     */
    public static function rest_get_project_by_slug( $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';
        $slug = sanitize_title( $request['slug'] );

        $row = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table_name} WHERE slug = %s", $slug ),
            ARRAY_A
        );

        if ( empty( $row ) ) {
            return new WP_Error( 'not_found', 'Project case study not found', array( 'status' => 404 ) );
        }

        return rest_ensure_response( self::format_project_for_api( $row ) );
    }

    /**
     * Format SQL row into structured JSON for frontend.
     */
    public static function format_project_for_api( $row ) {
        return array(
            'id'                => $row['id'],
            'agencyId'          => $row['agency_id'],
            'clientId'          => ! empty( $row['client_id'] ) ? intval( $row['client_id'] ) : 0,
            'title'             => $row['title'],
            'slug'              => $row['slug'],
            'subtitle'          => $row['subtitle'],
            'clientName'        => $row['client_name'],
            'clientLogoUrl'     => $row['client_logo_url'],
            'clientIndustry'    => $row['client_industry'],
            'projectDuration'   => $row['project_duration'],
            'yearCompleted'     => $row['year_completed'],
            'services'          => json_decode( $row['services'] ?? '[]', true ) ?: array(),
            'liveUrl'           => $row['live_url'],
            'projectLinks'      => ! empty( $row['project_links'] ) 
                ? ( json_decode( $row['project_links'], true ) ?: array() ) 
                : ( ! empty( $row['live_url'] ) 
                    ? array( array( 'platform' => 'website', 'url' => $row['live_url'], 'label' => 'Live Website' ) ) 
                    : array() ),
            'isConfidential'    => (bool) $row['is_confidential'],
            'isFeatured'        => (bool) $row['is_featured'],
            'status'            => $row['status'],
            'storyType'         => ! empty( $row['story_type'] ) ? $row['story_type'] : 'pas',
            'executiveSummary'  => $row['executive_summary'],
            'challengeContent'  => $row['challenge_content'],
            'solutionContent'   => $row['solution_content'],
            'deliverables'      => json_decode( $row['deliverables'] ?? '[]', true ) ?: array(),
            'kpis'              => json_decode( $row['kpis'] ?? '[]', true ) ?: array(),
            'testimonial'       => ! empty( $row['testimonial_quote'] ) ? array(
                'quote'    => $row['testimonial_quote'],
                'author'   => $row['testimonial_author'],
                'role'     => $row['testimonial_role'],
                'company'  => $row['testimonial_company'],
                'avatarUrl'=> $row['testimonial_avatar'],
                'verified' => (bool) $row['testimonial_verified'],
            ) : null,
            'heroMediaType'     => $row['hero_media_type'] ?: 'image',
            'heroImageUrl'      => $row['hero_image_url'],
            'heroVideoUrl'      => $row['hero_video_url'],
            'galleryImages'     => json_decode( $row['gallery_images'] ?? '[]', true ) ?: array(),
            'beforeImageUrl'    => $row['before_image_url'],
            'beforeLabel'       => $row['before_label'],
            'afterImageUrl'     => $row['after_image_url'],
            'afterLabel'        => $row['after_label'],
            'videoEmbedUrl'     => $row['video_embed_url'],
            'ctaType'           => $row['cta_type'],
            'ctaTitle'          => $row['cta_title'],
            'ctaSubtitle'       => $row['cta_subtitle'],
            'ctaButtonText'     => $row['cta_button_text'],
            'ctaButtonUrl'      => $row['cta_button_url'],
            'metaTitle'         => $row['meta_title'],
            'metaDescription'   => $row['meta_description'],
            'viewsCount'        => intval( $row['views_count'] ),
            'ctaClicks'         => intval( $row['cta_clicks'] ),
            'createdAt'         => $row['created_at'],
            'updatedAt'         => $row['updated_at'],
        );
    }

    /**
     * AJAX: Get all projects for workspace management (scoped strictly by agency_id).
     */
    public static function ajax_get_projects() {
        self::verify_security();

        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        $agency_id = self::get_current_agency_id();
        $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
        $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

        $query = "SELECT * FROM {$table_name} WHERE agency_id = %s";
        $params = array( $agency_id );

        if ( ! empty( $status ) && $status !== 'all' ) {
            $query .= " AND status = %s";
            $params[] = $status;
        }

        if ( ! empty( $search ) ) {
            $query .= " AND (title LIKE %s OR client_name LIKE %s OR client_industry LIKE %s)";
            $wild = '%' . $wpdb->esc_like( $search ) . '%';
            $params[] = $wild;
            $params[] = $wild;
            $params[] = $wild;
        }

        $query .= " ORDER BY is_featured DESC, sort_order ASC, created_at DESC";

        $projects = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

        wp_send_json_success( array(
            'projects' => array_map( array( __CLASS__, 'format_project_for_api' ), $projects ),
            'count'    => count( $projects ),
            'agency_id'=> $agency_id,
        ) );
    }

    /**
     * AJAX: Save or Update a project case study.
     */
    public static function ajax_save_project() {
        self::verify_security();

        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        $agency_id = self::get_current_agency_id();
        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        $title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
        $client_name = isset( $_POST['client_name'] ) ? sanitize_text_field( wp_unslash( $_POST['client_name'] ) ) : '';

        if ( empty( $title ) || empty( $client_name ) ) {
            wp_send_json_error( array( 'message' => 'Project title and client name are required.' ) );
        }

        $slug = isset( $_POST['slug'] ) ? sanitize_title( $_POST['slug'] ) : sanitize_title( $title );
        if ( empty( $slug ) ) {
            $slug = 'project-' . time();
        }

        $client_id = isset( $_POST['client_id'] ) ? intval( $_POST['client_id'] ) : 0;
        if ( $client_id <= 0 && ! empty( $client_name ) ) {
            $matched_client = $wpdb->get_row( $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}cora_clients 
                 WHERE (LOWER(TRIM(CONCAT_WS(' ', first_name, last_name))) = LOWER(TRIM(%s)) 
                    OR LOWER(TRIM(company_name)) = LOWER(TRIM(%s)))
                 LIMIT 1",
                $client_name, $client_name
            ) );
            if ( $matched_client ) {
                $client_id = intval( $matched_client->id );
            }
        }

        // Services array
        $services_raw = isset( $_POST['services'] ) ? $_POST['services'] : array();
        $services = is_array( $services_raw ) ? $services_raw : array_filter( array_map( 'trim', explode( ',', (string) $services_raw ) ) );

        // KPIs array
        $kpis_raw = isset( $_POST['kpis'] ) ? $_POST['kpis'] : '[]';
        $kpis = is_string( $kpis_raw ) ? $kpis_raw : json_encode( $kpis_raw );

        // Deliverables array
        $deliverables_raw = isset( $_POST['deliverables'] ) ? $_POST['deliverables'] : '[]';
        $deliverables = is_string( $deliverables_raw ) ? $deliverables_raw : json_encode( $deliverables_raw );

        // Gallery images
        $gallery_raw = isset( $_POST['gallery_images'] ) ? $_POST['gallery_images'] : '[]';
        $gallery_images = is_string( $gallery_raw ) ? $gallery_raw : json_encode( $gallery_raw );

        // Smart Project Links JSON array
        $links_raw = isset( $_POST['project_links'] ) ? $_POST['project_links'] : ( isset( $_POST['smart_links'] ) ? $_POST['smart_links'] : '[]' );
        $project_links = is_string( $links_raw ) ? $links_raw : json_encode( $links_raw );
        $parsed_links = json_decode( $project_links, true ) ?: array();
        $first_url = ! empty( $parsed_links[0]['url'] ) ? esc_url_raw( $parsed_links[0]['url'] ) : ( isset( $_POST['live_url'] ) ? esc_url_raw( $_POST['live_url'] ) : '' );

        $data = array(
            'agency_id'            => $agency_id,
            'client_id'            => $client_id,
            'title'                => $title,
            'slug'                 => $slug,
            'subtitle'             => isset( $_POST['subtitle'] ) ? sanitize_text_field( wp_unslash( $_POST['subtitle'] ) ) : '',
            'client_name'          => $client_name,
            'client_industry'      => isset( $_POST['client_industry'] ) ? sanitize_text_field( wp_unslash( $_POST['client_industry'] ) ) : 'Creative Production',
            'project_duration'     => isset( $_POST['project_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['project_duration'] ) ) : '',
            'year_completed'       => isset( $_POST['year_completed'] ) ? sanitize_text_field( wp_unslash( $_POST['year_completed'] ) ) : date( 'Y' ),
            'services'             => json_encode( array_values( $services ) ),
            'live_url'             => $first_url,
            'project_links'        => $project_links,
            'is_confidential'      => ! empty( $_POST['is_confidential'] ) ? 1 : 0,
            'is_featured'          => ! empty( $_POST['is_featured'] ) ? 1 : 0,
            'status'               => isset( $_POST['status'] ) && in_array( $_POST['status'], array( 'draft', 'published', 'archived' ), true ) ? $_POST['status'] : 'published',
            'story_type'           => isset( $_POST['story_type'] ) ? sanitize_text_field( wp_unslash( $_POST['story_type'] ) ) : 'pas',
            'executive_summary'    => isset( $_POST['executive_summary'] ) ? wp_kses_post( wp_unslash( $_POST['executive_summary'] ) ) : '',
            'challenge_content'    => isset( $_POST['challenge_content'] ) ? wp_kses_post( wp_unslash( $_POST['challenge_content'] ) ) : '',
            'solution_content'     => isset( $_POST['solution_content'] ) ? wp_kses_post( wp_unslash( $_POST['solution_content'] ) ) : '',
            'deliverables'         => $deliverables,
            'kpis'                 => $kpis,
            'testimonial_quote'    => isset( $_POST['testimonial_quote'] ) ? wp_kses_post( wp_unslash( $_POST['testimonial_quote'] ) ) : '',
            'testimonial_author'   => isset( $_POST['testimonial_author'] ) ? sanitize_text_field( wp_unslash( $_POST['testimonial_author'] ) ) : '',
            'testimonial_role'     => isset( $_POST['testimonial_role'] ) ? sanitize_text_field( wp_unslash( $_POST['testimonial_role'] ) ) : '',
            'testimonial_company'  => isset( $_POST['testimonial_company'] ) ? sanitize_text_field( wp_unslash( $_POST['testimonial_company'] ) ) : '',
            'testimonial_verified' => isset( $_POST['testimonial_verified'] ) ? intval( $_POST['testimonial_verified'] ) : 1,
            'hero_media_type'      => isset( $_POST['hero_media_type'] ) && $_POST['hero_media_type'] === 'video' ? 'video' : 'image',
            'hero_image_url'       => isset( $_POST['hero_image_url'] ) ? esc_url_raw( $_POST['hero_image_url'] ) : '',
            'gallery_images'       => $gallery_images,
            'video_embed_url'      => isset( $_POST['video_url'] ) ? esc_url_raw( $_POST['video_url'] ) : ( isset( $_POST['video_embed_url'] ) ? esc_url_raw( $_POST['video_embed_url'] ) : '' ),
            'cta_title'            => isset( $_POST['cta_title'] ) ? sanitize_text_field( wp_unslash( $_POST['cta_title'] ) ) : 'Ready for a Custom Production?',
            'cta_subtitle'         => isset( $_POST['cta_subtitle'] ) ? sanitize_text_field( wp_unslash( $_POST['cta_subtitle'] ) ) : '',
            'cta_button_text'      => isset( $_POST['cta_button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['cta_button_text'] ) ) : 'Request Consultation',
            'cta_button_url'       => isset( $_POST['cta_button_url'] ) ? sanitize_text_field( wp_unslash( $_POST['cta_button_url'] ) ) : '/contact',
            'meta_title'           => isset( $_POST['meta_title'] ) ? sanitize_text_field( wp_unslash( $_POST['meta_title'] ) ) : '',
            'meta_description'     => isset( $_POST['meta_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['meta_description'] ) ) : '',
            'updated_at'           => current_time( 'mysql' ),
        );

        if ( $id > 0 ) {
            $wpdb->update( $table_name, $data, array( 'id' => $id, 'agency_id' => $agency_id ) );
            $project_id = $id;
            $msg = 'Portfolio project updated successfully.';
        } else {
            $data['created_at'] = current_time( 'mysql' );
            $wpdb->insert( $table_name, $data );
            $project_id = $wpdb->insert_id;
            $msg = 'New portfolio project published successfully.';
        }

        wp_send_json_success( array(
            'message'    => $msg,
            'project_id' => $project_id,
            'slug'       => $slug,
            'public_url' => home_url( '/portfolio/' . $slug )
        ) );
    }

    /**
     * AJAX: Quick Toggle Published / Draft status.
     */
    public static function ajax_toggle_project_publish() {
        self::verify_security();

        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        if ( ! $id ) {
            wp_send_json_error( array( 'message' => 'Invalid project ID.' ) );
        }

        $agency_id = self::get_current_agency_id();
        $current_status = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM {$table_name} WHERE id = %d AND agency_id = %s", $id, $agency_id ) );
        if ( ! $current_status ) {
            wp_send_json_error( array( 'message' => 'Project not found in this workspace.' ) );
        }

        $new_status = ( $current_status === 'published' ) ? 'draft' : 'published';

        $wpdb->update(
            $table_name,
            array( 'status' => $new_status, 'updated_at' => current_time( 'mysql' ) ),
            array( 'id' => $id, 'agency_id' => $agency_id )
        );

        wp_send_json_success( array(
            'message'    => 'Project status changed to ' . ucfirst( $new_status ) . '.',
            'new_status' => $new_status,
            'project_id' => $id
        ) );
    }

    /**
     * AJAX: Duplicate project.
     */
    public static function ajax_duplicate_project() {
        self::verify_security();

        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        if ( ! $id ) {
            wp_send_json_error( array( 'message' => 'Invalid project ID.' ) );
        }

        $agency_id = self::get_current_agency_id();
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d AND agency_id = %s", $id, $agency_id ), ARRAY_A );
        if ( empty( $row ) ) {
            wp_send_json_error( array( 'message' => 'Project not found in this workspace.' ) );
        }

        unset( $row['id'] );
        $row['title'] .= ' (Copy)';
        $row['slug'] .= '-copy-' . time();
        $row['status'] = 'draft';
        $row['created_at'] = current_time( 'mysql' );
        $row['updated_at'] = current_time( 'mysql' );

        $wpdb->insert( $table_name, $row );

        wp_send_json_success( array(
            'message'    => 'Project duplicated as draft.',
            'project_id' => $wpdb->insert_id
        ) );
    }

    /**
     * AJAX: Delete project.
     */
    public static function ajax_delete_project() {
        self::verify_security();

        global $wpdb;
        $table_name = $wpdb->prefix . 'cora_projects';

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        if ( ! $id ) {
            wp_send_json_error( array( 'message' => 'Invalid project ID.' ) );
        }

        $agency_id = self::get_current_agency_id();
        $wpdb->delete( $table_name, array( 'id' => $id, 'agency_id' => $agency_id ) );

        wp_send_json_success( array(
            'message' => 'Project removed successfully.',
            'id'      => $id
        ) );
    }

    /**
     * AJAX: Seed starter projects tailored to current workspace.
     */
    public static function ajax_seed_starter_projects() {
        self::verify_security();

        $agency_id = self::get_current_agency_id();

        if ( $agency_id === '2' ) {
            self::seed_photography_studio_projects( '2' );
        } elseif ( $agency_id === '1' ) {
            self::seed_real_estate_projects( '1' );
        } else {
            self::seed_marketing_agency_projects( $agency_id );
        }

        wp_send_json_success( array(
            'message' => 'Workspace starter projects loaded successfully.',
            'agency_id' => $agency_id
        ) );
    }

    /**
     * Common security verification.
     */
    private static function verify_security() {
        $nonce = isset( $_REQUEST['security'] ) ? sanitize_text_field( $_REQUEST['security'] ) : ( isset( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : '' );
        
        $verified = false;
        if ( ! empty( $nonce ) && ( wp_verify_nonce( $nonce, 'cora_ajax_nonce' ) || wp_verify_nonce( $nonce, 'cora_workspace_nonce' ) ) ) {
            $verified = true;
        } elseif ( current_user_can( 'manage_options' ) || current_user_can( 'edit_posts' ) ) {
            $verified = true;
        }

        if ( ! $verified ) {
            wp_send_json_error( array( 'message' => 'Security verification failed.' ), 403 );
        }
    }
}

// Auto initialize
Cora_Projects_Module::init();

}
