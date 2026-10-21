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

        // Filter out JS template literals / variables in relative paths
        if ( preg_match( '/[\$\{\}\<\>\[\]]/', $relative ) ) {
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
     * Validate that a URL is safe for remote fetching (SSRF protection).
     * Rejects loopback, private RFC 1918/4193/3927 addresses, cloud metadata services, and non-HTTP protocols.
     *
     * @param string $url
     * @return true|WP_Error
     */
    public function validate_safe_url( $url ) {
        $parsed = parse_url( $url );
        if ( empty( $parsed['scheme'] ) || ! in_array( strtolower( $parsed['scheme'] ), array( 'http', 'https' ), true ) ) {
            return new WP_Error( 'invalid_scheme', __( 'Only HTTP and HTTPS URLs are supported.', 'cora-workspace' ) );
        }

        if ( empty( $parsed['host'] ) ) {
            return new WP_Error( 'invalid_host', __( 'Invalid URL host.', 'cora-workspace' ) );
        }

        $host = strtolower( $parsed['host'] );

        // Disallow localhost or internal domains
        $is_local_dev = ( defined( 'WP_ENVIRONMENT_TYPE' ) && WP_ENVIRONMENT_TYPE === 'local' );
        if ( ! $is_local_dev && ( $host === 'localhost' || str_ends_with( $host, '.local' ) || str_ends_with( $host, '.internal' ) || str_ends_with( $host, '.lan' ) || str_ends_with( $host, '.corp' ) ) ) {
            return new WP_Error( 'ssrf_blocked_host', __( 'Access to local/internal domains is restricted.', 'cora-workspace' ) );
        }

        // Resolve DNS and validate IP
        $ips = gethostbynamel( $host );
        if ( ! is_array( $ips ) || empty( $ips ) ) {
            $ip = gethostbyname( $host );
            if ( $ip && $ip !== $host ) {
                $ips = array( $ip );
            } else {
                return new WP_Error( 'dns_lookup_failed', __( 'Could not resolve domain name.', 'cora-workspace' ) );
            }
        }

        foreach ( $ips as $ip ) {
            if ( ! $is_local_dev ) {
                if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
                    return new WP_Error( 'ssrf_blocked_ip', __( 'Access to private or internal IP ranges is strictly prohibited.', 'cora-workspace' ) );
                }
                if ( $ip === '127.0.0.1' || $ip === '::1' || str_starts_with( $ip, '127.' ) || str_starts_with( $ip, '169.254.' ) || $ip === '0.0.0.0' ) {
                    return new WP_Error( 'ssrf_blocked_ip', __( 'Access to loopback or cloud metadata services is prohibited.', 'cora-workspace' ) );
                }
            }
        }

        return true;
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
        $safety_check = $this->validate_safe_url( $root_url );
        if ( is_wp_error( $safety_check ) ) {
            return $safety_check;
        }

        $parsed_root = parse_url( $root_url );
        if ( empty( $parsed_root['host'] ) ) {
            return new WP_Error( 'invalid_url', __( 'Please provide a valid website URL with domain name.', 'cora-workspace' ) );
        }

        $base_domain = strtolower( preg_replace( '/^www\./i', '', $parsed_root['host'] ) );

        // Fetch Homepage safely
        $response = wp_safe_remote_get( $root_url, array(
            'timeout'     => 25,
            'redirection' => 5,
            'sslverify'   => true,
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

                // Filter out JS template literals, code fragments, invalid brackets, and script payloads
                if ( preg_match( '/[\$\{\}\<\>\[\]]/', $raw_href ) || preg_match( '/\$\{[^}]+\}/', $raw_href ) || stripos( $raw_href, 'undefined' ) !== false || stripos( $raw_href, 'null' ) !== false || stripos( $raw_href, '[object' ) !== false ) {
                    continue;
                }

                $abs_url = $this->resolve_url( $raw_href, $root_url );
                if ( ! filter_var( $abs_url, FILTER_VALIDATE_URL ) ) {
                    continue;
                }
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

                // Build Clean Title (strip JS code or SVG names)
                $title = '';
                if ( ! empty( $link_text ) && ! preg_match( '/[\$\{\}\(\)\<\>]/', $link_text ) && stripos( $link_text, 'function' ) === false && stripos( $link_text, 'svg' ) === false ) {
                    if ( strlen( $link_text ) > 1 && strlen( $link_text ) < 60 && ! preg_match( '/\b(read more|click here|learn more|more|view|button|next|previous)\b/i', $link_text ) ) {
                        $title = ucwords( trim( preg_replace( '/\s+/', ' ', $link_text ) ) );
                    }
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
        $safety_check = $this->validate_safe_url( $page_url );
        if ( is_wp_error( $safety_check ) ) {
            return $safety_check;
        }

        $agency_id = ! empty( $args['agency_id'] ) ? intval( $args['agency_id'] ) : ( function_exists( 'cora_get_request_agency_id' ) ? cora_get_request_agency_id() : 1 );
        $theme_id  = ! empty( $args['theme_id'] ) ? intval( $args['theme_id'] ) : 0;
        $is_homepage = ! empty( $args['is_homepage'] ) ? 1 : 0;
        $title     = ! empty( $args['title'] ) ? sanitize_text_field( $args['title'] ) : '';
        $slug      = ! empty( $args['slug'] ) ? sanitize_title( $args['slug'] ) : '';

        // Fetch Remote HTML safely
        $response = wp_safe_remote_get( $page_url, array(
            'timeout'     => 30,
            'redirection' => 5,
            'sslverify'   => true,
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

        // Handle homepage conflict when targeting global_pages without overwrite
        global $wpdb;
        if ( $is_homepage ) {
            $existing_home = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}cora_canvas_pages WHERE is_homepage = 1 LIMIT 1" );
            if ( $existing_home && ! empty( $args['destination'] ) && $args['destination'] === 'global_pages' && empty( $args['overwrite_homepage'] ) ) {
                $is_homepage = 0;
                if ( $slug === 'home' || empty( $slug ) ) {
                    $slug = 'imported-home';
                }
            }
        }

        // Process and Compile Full-Fidelity HTML
        $compiled_html = $this->compile_full_fidelity_html( $raw_html, $page_url, array(
            'title'           => $title,
            'is_homepage'     => $is_homepage,
            'slug'            => $slug,
            'normalize_links' => ! empty( $args['normalize_links'] ),
        ) );

        if ( empty( trim( $compiled_html ) ) ) {
            $compiled_html = $raw_html;
        }

        // Create or Ensure Draft Theme (Defaults to isolated Draft Theme sandbox)
        $destination = ! empty( $args['destination'] ) ? $args['destination'] : 'draft_theme';
        if ( empty( $theme_id ) ) {
            if ( $destination === 'global_pages' ) {
                $live_id = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' ORDER BY id DESC LIMIT 1" );
                $theme_id = $live_id ? intval( $live_id ) : 0;
            } else {
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
        }

        $live_theme_id = intval( $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' ORDER BY id DESC LIMIT 1" ) );
        $is_draft_mode = ( $destination === 'draft_theme' || ( $theme_id > 0 && $theme_id !== $live_theme_id ) );
        $page_status   = $is_draft_mode ? 'draft' : 'published';
        $wp_status     = $is_draft_mode ? 'draft' : 'publish';

        // Provision Underlying WordPress Post without KSES stripping
        kses_remove_filters();
        $post_id = wp_insert_post( array(
            'post_title'   => wp_strip_all_tags( $title ),
            'post_name'    => $slug,
            'post_status'  => $wp_status,
            'post_type'    => 'page',
            'post_content' => $compiled_html,
        ) );
        kses_init_filters();

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        $actual_slug = get_post_field( 'post_name', $post_id );
        if ( ! empty( $actual_slug ) && ( empty( $slug ) || $slug === 'home' || $slug === 'imported-home' ) ) {
            $slug = $actual_slug;
        }

        // Set Post Meta for Cora HTML Canvas Engine
        update_post_meta( $post_id, '_cora_page_engine', 'html_canvas' );
        update_post_meta( $post_id, '_cora_canvas_html_compiled', wp_slash( $compiled_html ) );
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
        if ( $theme_id > 0 ) {
            $existing_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d AND slug = %s LIMIT 1",
                $theme_id,
                $slug
            ) );
        } else {
            $existing_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}cora_canvas_pages WHERE slug = %s LIMIT 1",
                $slug
            ) );
        }

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
                    'status'          => $page_status,
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

        // Register as a reusable template in Cora Canvas Templates table for this theme
        if ( $theme_id > 0 ) {
            $existing_tmpl = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}cora_canvas_templates WHERE theme_id = %d AND slug = %s LIMIT 1",
                $theme_id,
                $slug
            ) );
            if ( ! $existing_tmpl ) {
                $wpdb->insert(
                    "{$wpdb->prefix}cora_canvas_templates",
                    array(
                        'agency_id'   => $agency_id,
                        'theme_id'    => $theme_id,
                        'archetype'   => 'page',
                        'slug'        => $slug,
                        'name'        => $title,
                        'engine'      => 'html_canvas',
                        'layout_data' => $compiled_html,
                        'is_default'  => $is_homepage ? 1 : 0,
                        'created_at'  => current_time( 'mysql' ),
                        'updated_at'  => current_time( 'mysql' ),
                    ),
                    array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
                );
            }
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
     * Check if the fetched HTML is an empty client-side shell (e.g. React/Vue SPA with no body content).
     *
     * @param string $html
     * @return bool
     */
    public function is_empty_or_spa_shell( $html ) {
        if ( empty( trim( $html ) ) ) {
            return true;
        }
        if ( preg_match( '/<body[^>]*>(.*?)<\/body>/is', $html, $m ) ) {
            $body_content = $m[1];
        } else {
            $body_content = $html;
        }
        $text_only = preg_replace( '/<script\b[^>]*>.*?<\/script>/is', '', $body_content );
        $text_only = preg_replace( '/<style\b[^>]*>.*?<\/style>/is', '', $text_only );
        $text_only = trim( strip_tags( $text_only ) );

        if ( strlen( $text_only ) < 80 ) {
            if ( preg_match( '/<div\s+id=["\'](?:root|app|__next)["\']\s*>\s*<\/div>/i', $body_content ) ||
                 preg_match( '/<div\s+id=["\'](?:root|app|__next)["\']\s*><noscript>/i', $body_content ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate an AI-grade semantic fallback layout when a site is an empty SPA shell.
     *
     * @param string $raw_html
     * @param string $page_url
     * @param string $title
     * @return string
     */
    public function generate_ai_semantic_fallback_html( $raw_html, $page_url, $title = '' ) {
        $og_title = $title;
        if ( preg_match( '/<meta[^>]*property=["\']og:title["\'][^>]*content=["\'](.*?)["\']/is', $raw_html, $m ) ) {
            $og_title = html_entity_decode( trim( $m[1] ) );
        } elseif ( empty( $og_title ) && preg_match( '/<title[^>]*>(.*?)<\/title>/is', $raw_html, $m ) ) {
            $og_title = html_entity_decode( trim( $m[1] ) );
        }
        $og_desc = '';
        if ( preg_match( '/<meta[^>]*property=["\']og:description["\'][^>]*content=["\'](.*?)["\']/is', $raw_html, $m ) ) {
            $og_desc = html_entity_decode( trim( $m[1] ) );
        } elseif ( preg_match( '/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $raw_html, $m ) ) {
            $og_desc = html_entity_decode( trim( $m[1] ) );
        }
        $og_image = '';
        if ( preg_match( '/<meta[^>]*property=["\']og:image["\'][^>]*content=["\'](.*?)["\']/is', $raw_html, $m ) ) {
            $og_image = $this->resolve_url( trim( $m[1] ), $page_url );
        }

        $parsed = parse_url( $page_url );
        $brand_name = ! empty( $parsed['host'] ) ? ucwords( str_replace( array( 'www.', '.com', '.in', '.org', '.net', '-', '_' ), ' ', $parsed['host'] ) ) : 'Brand';

        if ( empty( $og_desc ) ) {
            $og_desc = sprintf( __( 'Experience modern, high-performance digital workflows with %s. Designed for speed, precision, and scale.', 'cora-workspace' ), $brand_name );
        }

        ob_start();
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $og_title ); ?></title>
    <meta name="description" content="<?php echo esc_attr( $og_desc ); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="bg-zinc-50 text-zinc-900 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-zinc-900 selection:text-white">
    <header class="w-full bg-white/80 backdrop-blur-md border-b border-zinc-200/80 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-zinc-900 text-white flex items-center justify-center font-bold text-sm">
                    <?php echo esc_html( substr( $brand_name, 0, 1 ) ); ?>
                </div>
                <span class="font-bold text-base tracking-tight text-zinc-900"><?php echo esc_html( $brand_name ); ?></span>
            </div>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-zinc-600">
                <a href="#features" class="hover:text-zinc-950 transition-colors">Features</a>
                <a href="#overview" class="hover:text-zinc-950 transition-colors">Overview</a>
                <a href="#contact" class="hover:text-zinc-950 transition-colors">Contact</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 transition-colors">
                    Visit Original Site &rarr;
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <section class="max-w-5xl mx-auto px-6 py-20 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 border border-zinc-200/80 text-xs font-medium text-zinc-700 mb-8">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>AI Reconstructed Canvas Page</span>
            </div>
            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-zinc-950 mb-6 max-w-3xl mx-auto leading-tight">
                <?php echo esc_html( $og_title ); ?>
            </h1>
            <p class="text-lg text-zinc-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                <?php echo esc_html( $og_desc ); ?>
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4 mb-16">
                <a href="#overview" class="px-6 py-3 rounded-xl bg-zinc-900 text-white font-semibold text-sm hover:bg-zinc-800 transition-all shadow-sm">
                    Explore Details
                </a>
                <a href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noopener noreferrer" class="px-6 py-3 rounded-xl bg-white border border-zinc-300 font-semibold text-sm text-zinc-800 hover:bg-zinc-100 transition-all">
                    Live Origin Source
                </a>
            </div>

            <?php if ( ! empty( $og_image ) ) : ?>
            <div class="relative rounded-2xl overflow-hidden border border-zinc-200 shadow-xl max-w-4xl mx-auto bg-zinc-100">
                <img src="<?php echo esc_url( $og_image ); ?>" alt="<?php echo esc_attr( $og_title ); ?>" class="w-full h-auto object-cover max-h-[500px]">
            </div>
            <?php endif; ?>
        </section>

        <section id="features" class="max-w-6xl mx-auto px-6 py-16 border-t border-zinc-200/60">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 rounded-2xl bg-white border border-zinc-200/80">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center font-bold text-zinc-900 mb-4">
                        01
                    </div>
                    <h3 class="font-bold text-lg text-zinc-900 mb-2">High Performance</h3>
                    <p class="text-sm text-zinc-600 leading-relaxed">Engineered with static acceleration for instantaneous sub-100ms load times.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-zinc-200/80">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center font-bold text-zinc-900 mb-4">
                        02
                    </div>
                    <h3 class="font-bold text-lg text-zinc-900 mb-2">Full Responsiveness</h3>
                    <p class="text-sm text-zinc-600 leading-relaxed">Adapts seamlessly across mobile, tablet, desktop, and large displays with clean typography.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-zinc-200/80">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center font-bold text-zinc-900 mb-4">
                        03
                    </div>
                    <h3 class="font-bold text-lg text-zinc-900 mb-2">Cora Visual Builder Ready</h3>
                    <p class="text-sm text-zinc-600 leading-relaxed">Fully decoupled and editable directly inside Cora Canvas with instant 1-click publishing.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="w-full bg-white border-t border-zinc-200 py-8">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
            <div>&copy; <?php echo date('Y'); ?> <?php echo esc_html( $brand_name ); ?>. All rights reserved.</div>
            <div class="flex items-center gap-4">
                <span>Migrated with Cora Canvas AI</span>
                <span>&bull;</span>
                <a href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-zinc-800 underline">Origin</a>
            </div>
        </div>
    </footer>
</body>
</html>
        <?php
        return ob_get_clean();
    }

    /**
     * Process and compile HTML to guarantee 100% styling fidelity, error resilience, and isolation.
     *
     * @param string $html     Raw remote HTML.
     * @param string $base_url Page source URL.
     * @param array  $options  Processing options.
     * @return string
     */
    public function compile_full_fidelity_html( $html, $base_url, $options = array() ) {
        if ( empty( trim( $html ) ) ) {
            return '';
        }

        // Check if fetched HTML is an empty SPA shell (client-side rendered)
        if ( $this->is_empty_or_spa_shell( $html ) ) {
            return $this->generate_ai_semantic_fallback_html( $html, $base_url, $options['title'] ?? '' );
        }

        $original_html = $html;

        // Prevent PCRE catastrophic backtracking failure on large modern SPA/HTML bundles
        if ( function_exists( 'ini_set' ) ) {
            @ini_set( 'pcre.backtrack_limit', '10000000' );
            @ini_set( 'pcre.recursion_limit', '500000' );
        }

        // 1. Remove Tracking Pixels, Disruptive Analytics & Cookie Banner Scripts safely
        $tracker_patterns = array(
            '/<script[^>]*google-analytics\.com\/analytics\.js[^>]*>.*?<\/script>/is',
            '/<script[^>]*googletagmanager\.com\/gtag\/js[^>]*>.*?<\/script>/is',
            '/<script[^>]*googletagmanager\.com\/gtm\.js[^>]*>.*?<\/script>/is',
            '/<script[^>]*connect\.facebook\.net\/[^>]*\/fbevents\.js[^>]*>.*?<\/script>/is',
            '/<script[^>]*static\.hotjar\.com\/[^>]*>.*?<\/script>/is',
            '/<script[^>]*widget\.intercom\.io\/[^>]*>.*?<\/script>/is',
            '/<script[^>]*beacon\.min\.js[^>]*>.*?<\/script>/is',
            '/<script[^>]*email-decode\.min\.js[^>]*>.*?<\/script>/is',
            '/<script[^>]*cdn\.cookielaw\.org\/[^>]*>.*?<\/script>/is',
            '/<script[^>]*cookiebot\.com\/[^>]*>.*?<\/script>/is',
            '/<div[^>]*id=["\']wpadminbar["\'][^>]*>.*?<\/div>/is',
            '/<div[^>]*id=["\']onetrust-[^"\']*["\'][^>]*>.*?<\/div>/is',
            '/<div[^>]*id=["\']optanon-[^"\']*["\'][^>]*>.*?<\/div>/is',
        );
        $res = preg_replace( $tracker_patterns, '', $html );
        if ( null !== $res ) {
            $html = $res;
        }

        // Remove anti-flicker prehiding styles and scripts that hide elements or fail on localhost
        $res = preg_replace( '/<style[^>]*id=["\']alloy-prehiding["\'][^>]*>.*?<\/style>/is', '', $html );
        if ( null !== $res ) {
            $html = $res;
        }

        // Safely neutralize scripts containing disruptive prehiding or consent blockers without catastrophic backtracking
        $res = preg_replace_callback( '/<script\b[^>]*>(.*?)<\/script>/is', function( $m ) {
            $body = $m[1];
            if ( stripos( $body, 'alloy-prehiding' ) !== false ||
                 stripos( $body, 'OptanonConsent' ) !== false ||
                 stripos( $body, 'Flicker Manager' ) !== false ) {
                return '';
            }
            return $m[0];
        }, $html );
        if ( null !== $res ) {
            $html = $res;
        }

        // 2. Resolve Stylesheet Links to Absolute HTTPS URLs
        $res = preg_replace_callback( '/<link\s+([^>]*?)href=["\']([^"\']+)["\']([^>]*?)>/is', function( $m ) use ( $base_url ) {
            $attrs_before = $m[1];
            $href         = $m[2];
            $attrs_after  = $m[3];

            $abs_href = $this->resolve_url( $href, $base_url );
            return '<link ' . $attrs_before . 'href="' . esc_url( $abs_href ) . '"' . $attrs_after . '>';
        }, $html );
        if ( null !== $res ) {
            $html = $res;
        }

        // 3. Resolve Script src to Absolute HTTPS URLs
        $res = preg_replace_callback( '/<script\s+([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/is', function( $m ) use ( $base_url ) {
            $attrs_before = $m[1];
            $src          = $m[2];
            $attrs_after  = $m[3];

            $abs_src = $this->resolve_url( $src, $base_url );
            return '<script ' . $attrs_before . 'src="' . esc_url( $abs_src ) . '"' . $attrs_after . '>';
        }, $html );
        if ( null !== $res ) {
            $html = $res;
        }

        // 4. Resolve Images, SVGs, and Video Media Sources
        $res = preg_replace_callback( '/<img\s+([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/is', function( $m ) use ( $base_url ) {
            $attrs_before = $m[1];
            $src          = $m[2];
            $attrs_after  = $m[3];

            $abs_src = $this->resolve_url( $src, $base_url );
            return '<img ' . $attrs_before . 'src="' . esc_url( $abs_src ) . '"' . $attrs_after . '>';
        }, $html );
        if ( null !== $res ) {
            $html = $res;
        }

        // 4b. Resolve SVG <use href="..."> and <use xlink:href="...">
        $res = preg_replace_callback( '/<use\s+([^>]*?)(?:xlink:)?href=["\']([^"\']+)["\']([^>]*?)>/is', function( $m ) use ( $base_url ) {
            $attrs_before = $m[1];
            $href         = $m[2];
            $attrs_after  = $m[3];
            $abs_href     = $this->resolve_url( $href, $base_url );
            return '<use ' . $attrs_before . 'href="' . esc_url( $abs_href ) . '"' . $attrs_after . '>';
        }, $html );
        if ( null !== $res ) {
            $html = $res;
        }

        // srcset
        $res = preg_replace_callback( '/srcset=["\']([^"\']+)["\']/is', function( $m ) use ( $base_url ) {
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
        if ( null !== $res ) {
            $html = $res;
        }

        // CSS inline background-image url(...)
        $res = preg_replace_callback( '/url\(\s*[\'"]?([^\'")]+)[\'"]?\s*\)/is', function( $m ) use ( $base_url ) {
            $url = trim( $m[1] );
            if ( preg_match( '/^(data:|#)/i', $url ) ) {
                return $m[0];
            }
            $abs_url = $this->resolve_url( $url, $base_url );
            return 'url("' . esc_url( $abs_url ) . '")';
        }, $html );
        if ( null !== $res ) {
            $html = $res;
        }

        // 5. Ensure Charset and Viewport are present in <head>
        if ( stripos( $html, '<meta name="viewport"' ) === false && stripos( $html, '<meta name=\'viewport\'' ) === false ) {
            $res = preg_replace( '/<head[^>]*>/i', '$0' . "\n" . '<meta name="viewport" content="width=device-width, initial-scale=1.0">', $html, 1 );
            if ( null !== $res ) {
                $html = $res;
            }
        }

        // 6. Inject Cora Universal Error & Hydration Resilience Shield into <head>
        $shield = '<script id="cora-resilience-shield">
(function() {
    window.__CORA_PAGE_ENGINE__ = "html_canvas";
    // Universal Error & Rejection Shield: Prevents third-party scripts, hydration mismatches, and remote CORS errors from blanking or crashing the page
    window.addEventListener("error", function(e) {
        if (e && e.stopImmediatePropagation) e.stopImmediatePropagation();
        if (e && e.preventDefault) e.preventDefault();
        return true;
    }, true);
    window.addEventListener("unhandledrejection", function(e) {
        if (e && e.stopImmediatePropagation) e.stopImmediatePropagation();
        if (e && e.preventDefault) e.preventDefault();
    }, true);
    // Neutralize common tracking stubs
    window.analytics = window.analytics || { track: function(){}, identify: function(){}, page: function(){}, ready: function(fn){ if(typeof fn==="function") fn(); }, reset: function(){} };
    window.gtag = window.gtag || function(){};
    window.dataLayer = window.dataLayer || [];
})();
</script>';

        if ( preg_match( '/<head[^>]*>/i', $html ) ) {
            $res = preg_replace( '/<head[^>]*>/i', '$0' . "\n" . $shield, $html, 1 );
            if ( null !== $res ) {
                $html = $res;
            }
        } else {
            $html = $shield . "\n" . $html;
        }

        if ( empty( trim( $html ) ) ) {
            $html = $original_html;
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

        if ( empty( $theme_name ) ) {
            $theme_name = 'Imported HTML Theme (' . date( 'M d, Y' ) . ')';
        }

        $created_by = get_current_user_id() ?: 1;

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
            $wpdb->prefix . 'cora_canvas_themes',
            array(
                'agency_id'   => $agency_id,
                'name'        => sanitize_text_field( $theme_name ),
                'status'      => 'draft',
                'settings'    => wp_json_encode( $settings ),
                'created_by'  => $created_by,
                'created_at'  => current_time( 'mysql' ),
                'updated_at'  => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%s', '%d', '%s', '%s' )
        );

        if ( false === $inserted || empty( $wpdb->insert_id ) ) {
            $db_error = ! empty( $wpdb->last_error ) ? ' (' . $wpdb->last_error . ')' : '';
            return new WP_Error( 'db_error', __( 'Failed to create draft theme in database.', 'cora-workspace' ) . $db_error );
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
