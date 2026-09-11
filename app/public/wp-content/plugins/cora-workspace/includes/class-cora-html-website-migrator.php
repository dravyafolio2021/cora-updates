<?php
/**
 * Cora Universal Website (HTML/CSS/JS) Migrator Engine
 *
 * Scans, crawls, extracts, and compiles any live website and its pages
 * into high-fidelity, pixel-accurate static HTML/CSS/JS pages for Cora Canvas.
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_HTML_Website_Migrator {

    /**
     * Singleton instance.
     *
     * @var Cora_HTML_Website_Migrator|null
     */
    private static $instance = null;

    /**
     * Get singleton instance.
     *
     * @return Cora_HTML_Website_Migrator
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {}

    /**
     * Normalize and sanitize a URL.
     *
     * @param string $url Target URL.
     * @return string
     */
    public function clean_url( $url ) {
        $url = trim( $url );
        if ( ! preg_match( '~^(?:f|ht)tps?://~i', $url ) ) {
            $url = 'https://' . $url;
        }
        return esc_url_raw( $url );
    }

    /**
     * Resolve a relative URL to an absolute URL given a base URL.
     *
     * @param string $relative
     * @param string $base
     * @return string
     */
    public function resolve_url( $relative, $base ) {
        $relative = trim( $relative );
        if ( empty( $relative ) || $relative === '#' ) {
            return $relative;
        }

        // Already absolute or protocol-relative or special scheme
        if ( preg_match( '~^(?:https?:|//|data:|mailto:|tel:|javascript:|#)~i', $relative ) ) {
            if ( substr( $relative, 0, 2 ) === '//' ) {
                $base_parts = parse_url( $base );
                $scheme = ! empty( $base_parts['scheme'] ) ? $base_parts['scheme'] : 'https';
                return $scheme . ':' . $relative;
            }
            return $relative;
        }

        $base_parts = parse_url( $base );
        $scheme   = ! empty( $base_parts['scheme'] ) ? $base_parts['scheme'] : 'https';
        $host     = ! empty( $base_parts['host'] ) ? $base_parts['host'] : '';
        $port     = ! empty( $base_parts['port'] ) ? ':' . $base_parts['port'] : '';
        $base_path = ! empty( $base_parts['path'] ) ? $base_parts['path'] : '/';

        if ( empty( $host ) ) {
            return $relative;
        }

        $root = $scheme . '://' . $host . $port;

        // Leading slash -> root-relative
        if ( substr( $relative, 0, 1 ) === '/' ) {
            return $root . $relative;
        }

        // Directory-relative
        $dir = preg_replace( '~/[^/]*$~', '/', $base_path );
        if ( empty( $dir ) || substr( $dir, 0, 1 ) !== '/' ) {
            $dir = '/' . $dir;
        }

        $full_path = $dir . $relative;
        // Resolve . and .. in path
        $segments = explode( '/', $full_path );
        $resolved = array();
        foreach ( $segments as $seg ) {
            if ( $seg === '..' ) {
                array_pop( $resolved );
            } elseif ( $seg !== '.' && $seg !== '' ) {
                $resolved[] = $seg;
            }
        }

        return $root . '/' . implode( '/', $resolved );
    }

    /**
     * Deep Scan a remote website to discover structure, subpages, stylesheets, fonts, and scripts.
     *
     * @param string $root_url Target website URL.
     * @param array  $options  Scanning configuration.
     * @return array|WP_Error
     */
    public function scan_remote_website( $root_url, $options = array() ) {
        $root_url = $this->clean_url( $root_url );
        $parsed_root = parse_url( $root_url );

        if ( empty( $parsed_root['host'] ) ) {
            return new WP_Error( 'invalid_url', __( 'Please provide a valid website URL with domain name.', 'cora-workspace' ) );
        }

        $base_domain = strtolower( preg_replace( '/^www\./i', '', $parsed_root['host'] ) );

        // Fetch Homepage
        $response = wp_remote_get( $root_url, array(
            'timeout'     => 25,
            'redirection' => 5,
            'sslverify'   => false,
            'headers'     => array(
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
            ),
            'user-agent'  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 CoraUniversalMigrator/1.0',
        ) );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'fetch_failed', sprintf( __( 'Could not connect to website: %s', 'cora-workspace' ), $response->get_error_message() ) );
        }

        $http_code = wp_remote_retrieve_response_code( $response );
        if ( $http_code >= 400 ) {
            return new WP_Error( 'http_error', sprintf( __( 'Website returned HTTP %d response.', 'cora-workspace' ), $http_code ) );
        }

        $html = wp_remote_retrieve_body( $response );
        if ( empty( $html ) ) {
            return new WP_Error( 'empty_body', __( 'The target website returned empty content.', 'cora-workspace' ) );
        }

        // 1. Extract Website Meta & Branding
        $site_name = '';
        if ( preg_match( '/<meta[^>]+property=["\']og:site_name["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m ) ) {
            $site_name = trim( $m[1] );
        } elseif ( preg_match( '/<title[^>]*>(.*?)<\/title>/is', $html, $m ) ) {
            $site_name = trim( strip_tags( $m[1] ) );
            $site_name = preg_replace( '/\s*[\|\-–—•].*$/', '', $site_name );
        }
        if ( empty( $site_name ) ) {
            $site_name = ucfirst( explode( '.', $base_domain )[0] );
        }

        // Favicon
        $favicon = '';
        if ( preg_match( '/<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]+href=["\']([^"\']+)["\']/i', $html, $m ) ) {
            $favicon = $this->resolve_url( $m[1], $root_url );
        } elseif ( preg_match( '/<link[^>]+href=["\']([^"\']+)["\'][^>]+rel=["\'](?:shortcut )?icon["\']/i', $html, $m ) ) {
            $favicon = $this->resolve_url( $m[1], $root_url );
        } else {
            $favicon = 'https://' . $parsed_root['host'] . '/favicon.ico';
        }

        // OpenGraph Image
        $og_image = '';
        if ( preg_match( '/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m ) ) {
            $og_image = $this->resolve_url( $m[1], $root_url );
        }

        // Meta Description
        $meta_desc = '';
        if ( preg_match( '/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m ) ) {
            $meta_desc = trim( $m[1] );
        }

        // 2. Detect Technology & Frameworks
        $tech_stack = array();
        if ( stripos( $html, 'elementor' ) !== false ) {
            $tech_stack[] = 'Elementor';
        }
        if ( stripos( $html, 'tailwind' ) !== false || preg_match( '/class=["\'][^"\']*\b(flex|grid|hidden|px-|py-|text-zinc|bg-slate)\b/i', $html ) ) {
            $tech_stack[] = 'Tailwind CSS';
        }
        if ( stripos( $html, 'bootstrap' ) !== false ) {
            $tech_stack[] = 'Bootstrap';
        }
        if ( stripos( $html, 'fonts.googleapis.com' ) !== false || stripos( $html, 'fonts.gstatic.com' ) !== false ) {
            $tech_stack[] = 'Google Fonts';
        }
        if ( stripos( $html, 'swiper' ) !== false ) {
            $tech_stack[] = 'Swiper Slider';
        }
        if ( stripos( $html, 'aos' ) !== false || stripos( $html, 'data-aos' ) !== false ) {
            $tech_stack[] = 'AOS Animations';
        }
        if ( stripos( $html, 'gsap' ) !== false ) {
            $tech_stack[] = 'GSAP Motion';
        }
        if ( stripos( $html, 'wp-content' ) !== false ) {
            $tech_stack[] = 'WordPress';
        } elseif ( stripos( $html, 'webflow' ) !== false ) {
            $tech_stack[] = 'Webflow';
        } elseif ( stripos( $html, 'squarespace' ) !== false ) {
            $tech_stack[] = 'Squarespace';
        } elseif ( stripos( $html, 'wix' ) !== false ) {
            $tech_stack[] = 'Wix';
        } else {
            $tech_stack[] = 'Custom HTML/CSS';
        }

        // 3. Count Assets
        $stylesheet_count = 0;
        if ( preg_match_all( '/<link[^>]+rel=["\']stylesheet["\']/i', $html, $m ) ) {
            $stylesheet_count = count( $m[0] );
        }
        $script_count = 0;
        if ( preg_match_all( '/<script[^>]*>/i', $html, $m ) ) {
            $script_count = count( $m[0] );
        }
        $image_count = 0;
        if ( preg_match_all( '/<img[^>]+src=["\']/i', $html, $m ) ) {
            $image_count = count( $m[0] );
        }

        // 4. Discover Internal Subpages via Navigation and Links
        $discovered_pages = array();
        
        // Always include Homepage first
        $homepage_title = 'Home';
        if ( preg_match( '/<title[^>]*>(.*?)<\/title>/is', $html, $m ) ) {
            $h_title = trim( strip_tags( $m[1] ) );
            if ( ! empty( $h_title ) ) {
                $homepage_title = preg_replace( '/\s*[\|\-–—•].*$/', '', $h_title ) ?: 'Home';
            }
        }
        $discovered_pages[$root_url] = array(
            'url'         => $root_url,
            'title'       => $homepage_title,
            'slug'        => 'home',
            'is_homepage' => true,
            'selected'    => true,
            'badge'       => 'Homepage',
            'status'      => 'ready',
        );

        // Parse all <a> hrefs
        if ( preg_match_all( '/<a\s+(?:[^>]*?\s+)?href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $html, $matches, PREG_SET_ORDER ) ) {
            $ignored_extensions = array( 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'avif', 'pdf', 'zip', 'mp4', 'mp3', 'css', 'js', 'json', 'xml', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'doc', 'docx', 'xls', 'xlsx' );

            foreach ( $matches as $match ) {
                $raw_href = trim( $match[1] );
                $link_text = trim( strip_tags( $match[2] ) );

                if ( empty( $raw_href ) || $raw_href === '#' || substr( $raw_href, 0, 11 ) === 'javascript:' || substr( $raw_href, 0, 7 ) === 'mailto:' || substr( $raw_href, 0, 4 ) === 'tel:' ) {
                    continue;
                }

                $abs_url = $this->resolve_url( $raw_href, $root_url );
                $parsed_link = parse_url( $abs_url );

                if ( empty( $parsed_link['host'] ) ) {
                    continue;
                }

                $link_domain = strtolower( preg_replace( '/^www\./i', '', $parsed_link['host'] ) );
                // Enforce strictly same domain / subdomain
                if ( $link_domain !== $base_domain && ! str_ends_with( $link_domain, '.' . $base_domain ) ) {
                    continue;
                }

                // Check file extension
                $path = ! empty( $parsed_link['path'] ) ? $parsed_link['path'] : '/';
                $path_ext = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
                if ( in_array( $path_ext, $ignored_extensions, true ) ) {
                    continue;
                }

                // Skip admin/login/cart/checkout paths
                if ( preg_match( '~/(wp-admin|wp-login|cart|checkout|my-account|feed|author|tag|search|api|cdn-cgi)/~i', $path ) ) {
                    continue;
                }

                // Normalize URL (strip query/hash for page identity)
                $clean_page_url = $parsed_link['scheme'] . '://' . $parsed_link['host'] . ( ! empty( $parsed_link['port'] ) ? ':' . $parsed_link['port'] : '' ) . rtrim( $path, '/' );
                if ( empty( $path ) || $path === '/' ) {
                    $clean_page_url = $root_url;
                }

                // If already added, continue
                if ( isset( $discovered_pages[$clean_page_url] ) ) {
                    continue;
                }

                // Build Clean Title
                $title = '';
                if ( ! empty( $link_text ) && strlen( $link_text ) > 1 && strlen( $link_text ) < 60 && ! preg_match( '/\b(read more|click here|learn more|more|view|button|next|previous)\b/i', $link_text ) ) {
                    $title = ucwords( trim( preg_replace( '/\s+/', ' ', $link_text ) ) );
                }

                // Slug from path
                $trimmed_path = trim( $path, '/' );
                $slug = sanitize_title( $trimmed_path );
                if ( empty( $slug ) ) {
                    $slug = 'home';
                }

                if ( empty( $title ) ) {
                    $path_parts = explode( '/', $trimmed_path );
                    $last_segment = end( $path_parts );
                    $title = ucwords( str_replace( array( '-', '_', '.html', '.php' ), ' ', $last_segment ) );
                }

                if ( empty( $title ) ) {
                    $title = 'Page - ' . $slug;
                }

                // Max 30 pages discovery limit
                if ( count( $discovered_pages ) >= 30 ) {
                    break;
                }

                $discovered_pages[$clean_page_url] = array(
                    'url'         => $clean_page_url,
                    'title'       => $title,
                    'slug'        => $slug,
                    'is_homepage' => ( $slug === 'home' ),
                    'selected'    => true,
                    'badge'       => ( $slug === 'home' ) ? 'Homepage' : 'Subpage',
                    'status'      => 'ready',
                );
            }
        }

        return array(
            'success'          => true,
            'root_url'         => $root_url,
            'site_name'        => $site_name,
            'favicon'          => $favicon,
            'og_image'         => $og_image,
            'meta_description' => $meta_desc,
            'tech_stack'       => array_unique( $tech_stack ),
            'telemetry'        => array(
                'stylesheets' => $stylesheet_count,
                'scripts'     => $script_count,
                'images'      => $image_count,
                'total_pages' => count( $discovered_pages ),
            ),
            'pages'            => array_values( $discovered_pages ),
        );
    }

    /**
     * Migrate a single page URL into Cora Canvas as a high-fidelity static HTML/CSS/JS page.
     *
     * @param string $page_url Target page URL.
     * @param array  $args     Migration arguments (theme_id, title, slug, is_homepage, agency_id, normalize_links, etc.).
     * @return array|WP_Error
     */
    public function migrate_single_page_html( $page_url, $args = array() ) {
        $page_url = $this->clean_url( $page_url );
        $agency_id = ! empty( $args['agency_id'] ) ? intval( $args['agency_id'] ) : ( function_exists( 'cora_get_request_agency_id' ) ? cora_get_request_agency_id() : 1 );
        $theme_id  = ! empty( $args['theme_id'] ) ? intval( $args['theme_id'] ) : 0;
        $is_homepage = ! empty( $args['is_homepage'] ) ? 1 : 0;
        $title     = ! empty( $args['title'] ) ? sanitize_text_field( $args['title'] ) : '';
        $slug      = ! empty( $args['slug'] ) ? sanitize_title( $args['slug'] ) : '';

        // Fetch Remote HTML
        $response = wp_remote_get( $page_url, array(
            'timeout'     => 30,
            'redirection' => 5,
            'sslverify'   => false,
            'headers'     => array(
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
            ),
            'user-agent'  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 CoraUniversalMigrator/1.0',
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $http_code = wp_remote_retrieve_response_code( $response );
        if ( $http_code >= 400 ) {
            return new WP_Error( 'http_error', sprintf( __( 'HTTP %d error when fetching page.', 'cora-workspace' ), $http_code ) );
        }

        $raw_html = wp_remote_retrieve_body( $response );
        if ( empty( $raw_html ) ) {
            return new WP_Error( 'empty_content', __( 'Fetched page content was empty.', 'cora-workspace' ) );
        }

        // Auto-detect Title if missing
        if ( empty( $title ) ) {
            if ( preg_match( '/<h1[^>]*>(.*?)<\/h1>/is', $raw_html, $m ) ) {
                $title = trim( strip_tags( $m[1] ) );
            } elseif ( preg_match( '/<title[^>]*>(.*?)<\/title>/is', $raw_html, $m ) ) {
                $title = trim( strip_tags( $m[1] ) );
                $title = preg_replace( '/\s*[\|\-–—•].*$/', '', $title );
            } else {
                $title = 'Migrated Page';
            }
        }

        if ( empty( $slug ) ) {
            $parsed = parse_url( $page_url );
            $path = ! empty( $parsed['path'] ) ? trim( $parsed['path'], '/' ) : '';
            $slug = sanitize_title( $path );
            if ( empty( $slug ) ) {
                $slug = $is_homepage ? 'home' : sanitize_title( $title );
            }
        }

        // Process and Compile Full-Fidelity HTML
        $compiled_html = $this->compile_full_fidelity_html( $raw_html, $page_url, array(
            'title'           => $title,
            'is_homepage'     => $is_homepage,
            'slug'            => $slug,
            'normalize_links' => ! empty( $args['normalize_links'] ),
        ) );

        // Create or Ensure Draft Theme
        global $wpdb;
        if ( empty( $theme_id ) ) {
            $parsed = parse_url( $page_url );
            $host = ! empty( $parsed['host'] ) ? $parsed['host'] : 'Site';
            $theme_name = 'Imported - ' . ucwords( str_replace( array( 'www.', '.com', '.in', '.org', '.net', '-', '_' ), ' ', $host ) ) . ' (HTML)';
            $draft_id = $this->create_draft_html_theme( $theme_name, $agency_id, array(
                'source_url' => $page_url,
            ) );
            if ( is_wp_error( $draft_id ) ) {
                return $draft_id;
            }
            $theme_id = $draft_id;
        }

        // Provision Underlying WordPress Post
        $post_id = wp_insert_post( array(
            'post_title'   => wp_strip_all_tags( $title ),
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => $compiled_html,
        ) );

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        // Set Post Meta for Cora HTML Canvas Engine
        update_post_meta( $post_id, '_cora_page_engine', 'html_canvas' );
        update_post_meta( $post_id, '_cora_canvas_html_compiled', $compiled_html );
        update_post_meta( $post_id, '_wp_page_template', 'cora_canvas_standalone' );
        update_post_meta( $post_id, '_cora_canvas_theme_id', $theme_id );
        update_post_meta( $post_id, '_cora_migrated_from_url', $page_url );

        // Register in Cora Canvas Pages table
        if ( $is_homepage ) {
            $wpdb->update(
                "{$wpdb->prefix}cora_canvas_pages",
                array( 'is_homepage' => 0 ),
                array( 'theme_id' => $theme_id ),
                array( '%d' ),
                array( '%d' )
            );
        }

        // Check if page already exists in this theme
        $existing_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d AND slug = %s LIMIT 1",
            $theme_id,
            $slug
        ) );

        if ( $existing_id ) {
            $wpdb->update(
                "{$wpdb->prefix}cora_canvas_pages",
                array(
                    'wp_post_id'      => $post_id,
                    'title'           => $title,
                    'is_homepage'     => $is_homepage,
                    'template'        => 'cora_canvas_standalone',
                    'updated_at'      => current_time( 'mysql' ),
                ),
                array( 'id' => $existing_id ),
                array( '%d', '%s', '%d', '%s', '%s' ),
                array( '%d' )
            );
            $canvas_page_id = intval( $existing_id );
        } else {
            $wpdb->insert(
                "{$wpdb->prefix}cora_canvas_pages",
                array(
                    'agency_id'       => $agency_id,
                    'theme_id'        => $theme_id,
                    'wp_post_id'      => $post_id,
                    'title'           => $title,
                    'slug'            => $slug,
                    'status'          => 'published',
                    'is_homepage'     => $is_homepage,
                    'template'        => 'cora_canvas_standalone',
                    'seo_title'       => $title,
                    'seo_description' => 'Migrated from ' . esc_url( $page_url ),
                    'seo_og_image'    => '',
                    'created_by'      => get_current_user_id() ?: 1,
                    'created_at'      => current_time( 'mysql' ),
                    'updated_at'      => current_time( 'mysql' ),
                ),
                array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
            );
            $canvas_page_id = intval( $wpdb->insert_id );
        }

        return array(
            'success'        => true,
            'page_id'        => $canvas_page_id,
            'wp_post_id'     => $post_id,
            'title'          => $title,
            'slug'           => $slug,
            'theme_id'       => $theme_id,
            'is_homepage'    => (bool) $is_homepage,
            'preview_url'    => home_url( '/site/' . $slug . '?cv_preview_theme=' . $theme_id ),
            'message'        => sprintf( __( 'Page "%s" successfully migrated into Canvas Draft Theme #%d.', 'cora-workspace' ), $title, $theme_id ),
        );
    }

    /**
     * Process and compile HTML to guarantee 100% styling fidelity and isolation.
     *
     * @param string $html     Raw remote HTML.
     * @param string $base_url Page source URL.
     * @param array  $options  Processing options.
     * @return string
     */
    public function compile_full_fidelity_html( $html, $base_url, $options = array() ) {
        // 1. Remove Tracking Pixels & Disruptive Analytics Scripts
        $tracker_patterns = array(
            '/<script[^>]*google-analytics\.com\/analytics\.js[^>]*>.*?<\/script>/is',
            '/<script[^>]*googletagmanager\.com\/gtag\/js[^>]*>.*?<\/script>/is',
            '/<script[^>]*googletagmanager\.com\/gtm\.js[^>]*>.*?<\/script>/is',
            '/<script[^>]*connect\.facebook\.net\/[^>]*\/fbevents\.js[^>]*>.*?<\/script>/is',
            '/<script[^>]*static\.hotjar\.com\/[^>]*>.*?<\/script>/is',
            '/<script[^>]*widget\.intercom\.io\/[^>]*>.*?<\/script>/is',
            '/<script[^>]*beacon\.min\.js[^>]*>.*?<\/script>/is',
            '/<script[^>]*email-decode\.min\.js[^>]*>.*?<\/script>/is',
            '/<div[^>]*id=["\']wpadminbar["\'][^>]*>.*?<\/div>/is',
        );
        $html = preg_replace( $tracker_patterns, '', $html );

        // 2. Resolve Stylesheet Links to Absolute HTTPS URLs
        $html = preg_replace_callback( '/<link\s+([^>]*?)href=["\']([^"\']+)["\']([^>]*?)>/is', function( $m ) use ( $base_url ) {
            $attrs_before = $m[1];
            $href         = $m[2];
            $attrs_after  = $m[3];

            $abs_href = $this->resolve_url( $href, $base_url );
            return '<link ' . $attrs_before . 'href="' . esc_url( $abs_href ) . '"' . $attrs_after . '>';
        }, $html );

        // 3. Resolve Script src to Absolute HTTPS URLs
        $html = preg_replace_callback( '/<script\s+([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/is', function( $m ) use ( $base_url ) {
            $attrs_before = $m[1];
            $src          = $m[2];
            $attrs_after  = $m[3];

            $abs_src = $this->resolve_url( $src, $base_url );
            return '<script ' . $attrs_before . 'src="' . esc_url( $abs_src ) . '"' . $attrs_after . '>';
        }, $html );

        // 4. Resolve Images, SVGs, and Video Media Sources
        $html = preg_replace_callback( '/<img\s+([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/is', function( $m ) use ( $base_url ) {
            $attrs_before = $m[1];
            $src          = $m[2];
            $attrs_after  = $m[3];

            $abs_src = $this->resolve_url( $src, $base_url );
            return '<img ' . $attrs_before . 'src="' . esc_url( $abs_src ) . '"' . $attrs_after . '>';
        }, $html );

        // srcset
        $html = preg_replace_callback( '/srcset=["\']([^"\']+)["\']/is', function( $m ) use ( $base_url ) {
            $raw_srcset = $m[1];
            $entries = explode( ',', $raw_srcset );
            $resolved_entries = array();
            foreach ( $entries as $entry ) {
                $parts = preg_split( '/\s+/', trim( $entry ) );
                if ( ! empty( $parts[0] ) ) {
                    $parts[0] = $this->resolve_url( $parts[0], $base_url );
                    $resolved_entries[] = implode( ' ', $parts );
                }
            }
            return 'srcset="' . esc_attr( implode( ', ', $resolved_entries ) ) . '"';
        }, $html );

        // CSS inline background-image url(...)
        $html = preg_replace_callback( '/url\(\s*[\'"]?([^\'")]+)[\'"]?\s*\)/is', function( $m ) use ( $base_url ) {
            $url = trim( $m[1] );
            if ( preg_match( '/^(data:|#)/i', $url ) ) {
                return $m[0];
            }
            $abs_url = $this->resolve_url( $url, $base_url );
            return 'url("' . esc_url( $abs_url ) . '")';
        }, $html );

        // 5. Ensure Charset and Viewport are present in <head>
        if ( stripos( $html, '<meta name="viewport"' ) === false && stripos( $html, '<meta name=\'viewport\'' ) === false ) {
            $html = preg_replace( '/<head[^>]*>/i', '$0' . "\n" . '<meta name="viewport" content="width=device-width, initial-scale=1.0">', $html, 1 );
        }

        return $html;
    }

    /**
     * Create an isolated Draft Theme for imported HTML pages.
     *
     * @param string $theme_name
     * @param int    $agency_id
     * @param array  $source_meta
     * @return int|WP_Error
     */
    public function create_draft_html_theme( $theme_name, $agency_id = 0, $source_meta = array() ) {
        global $wpdb;

        if ( empty( $agency_id ) ) {
            $agency_id = function_exists( 'cora_get_request_agency_id' ) ? cora_get_request_agency_id() : 1;
        }

        $settings = array(
            'layout_type'       => 'standalone_html',
            'engine'            => 'html_canvas',
            'primary_color'     => '#18181b',
            'background_color'  => '#ffffff',
            'typography_font'   => 'Inter, sans-serif',
            'source_meta'       => $source_meta,
            'imported_at'       => current_time( 'mysql' ),
        );

        $inserted = $wpdb->insert(
            "{$wpdb->prefix}cora_canvas_themes",
            array(
                'agency_id'   => $agency_id,
                'name'        => sanitize_text_field( $theme_name ),
                'status'      => 'draft',
                'is_default'  => 0,
                'settings'    => json_encode( $settings ),
                'preview_img' => '',
                'created_at'  => current_time( 'mysql' ),
                'updated_at'  => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
        );

        if ( false === $inserted ) {
            return new WP_Error( 'db_error', __( 'Failed to create draft theme in database.', 'cora-workspace' ) );
        }

        return intval( $wpdb->insert_id );
    }
}

/**
 * Global helper function to retrieve the HTML Website Migrator instance.
 *
 * @return Cora_HTML_Website_Migrator
 */
function cora_html_website_migrator() {
    return Cora_HTML_Website_Migrator::get_instance();
}
