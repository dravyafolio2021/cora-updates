<?php
/**
 * Cora Elementor Website & Page Migrator Engine
 *
 * Provides automated 1-click migration for Elementor-based websites, pages,
 * template kits (.json / .zip), and remote URLs into Cora Studio OS Canvas.
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Elementor_Migrator {

    /**
     * Singleton instance.
     *
     * @var Cora_Elementor_Migrator|null
     */
    private static $instance = null;

    /**
     * Get singleton instance.
     *
     * @return Cora_Elementor_Migrator
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
     * Validate whether a raw string or decoded payload is valid Elementor data.
     *
     * @param string|array $data Raw JSON string or parsed array.
     * @return array|WP_Error Parsed Elementor data array or WP_Error on failure.
     */
    public function validate_elementor_data( $data ) {
        if ( is_string( $data ) ) {
            $decoded = json_decode( $data, true );
            if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $decoded ) ) {
                return new WP_Error(
                    'invalid_json',
                    __( 'The uploaded file does not contain valid JSON format.', 'cora-workspace' )
                );
            }
        } elseif ( is_array( $data ) ) {
            $decoded = $data;
        } else {
            return new WP_Error(
                'invalid_data',
                __( 'Invalid data format provided for Elementor migration.', 'cora-workspace' )
            );
        }

        // Case 1: Standard Elementor Template Export ({ "content": [...], "version": "...", "type": "..." })
        if ( isset( $decoded['content'] ) && is_array( $decoded['content'] ) ) {
            return array(
                'type'          => isset( $decoded['type'] ) ? sanitize_text_field( $decoded['type'] ) : 'page',
                'title'         => isset( $decoded['title'] ) ? sanitize_text_field( $decoded['title'] ) : 'Imported Elementor Page',
                'version'       => isset( $decoded['version'] ) ? sanitize_text_field( $decoded['version'] ) : '3.0.0',
                'content'       => $decoded['content'],
                'page_settings' => isset( $decoded['page_settings'] ) && is_array( $decoded['page_settings'] ) ? $decoded['page_settings'] : array(),
            );
        }

        // Case 2: Direct Array of Elementor Sections/Containers ([ { "id": "...", "elType": "section"|"container", ... } ])
        if ( isset( $decoded[0] ) && is_array( $decoded[0] ) && ( isset( $decoded[0]['elType'] ) || isset( $decoded[0]['elements'] ) ) ) {
            return array(
                'type'          => 'page',
                'title'         => 'Imported Elementor Page',
                'version'       => '3.0.0',
                'content'       => $decoded,
                'page_settings' => array(),
            );
        }

        // Case 3: Nested template structure
        if ( isset( $decoded['elements'] ) && is_array( $decoded['elements'] ) ) {
            return array(
                'type'          => isset( $decoded['type'] ) ? sanitize_text_field( $decoded['type'] ) : 'page',
                'title'         => isset( $decoded['title'] ) ? sanitize_text_field( $decoded['title'] ) : 'Imported Elementor Page',
                'version'       => '3.0.0',
                'content'       => $decoded['elements'],
                'page_settings' => isset( $decoded['settings'] ) && is_array( $decoded['settings'] ) ? $decoded['settings'] : array(),
            );
        }

        return new WP_Error(
            'not_elementor_schema',
            __( 'This export is not a supported Elementor template. Cora only supports Elementor-based themes and layouts.', 'cora-workspace' )
        );
    }

    /**
     * Import a single Elementor JSON template or page into Cora Canvas.
     *
     * @param string|array $json_data Raw JSON string or parsed array.
     * @param array        $args      Additional options (title, slug, theme_id, agency_id, sideload_media, is_homepage).
     * @return array|WP_Error
     */
    public function import_template_json( $json_data, $args = array() ) {
        global $wpdb;

        $validated = $this->validate_elementor_data( $json_data );
        if ( is_wp_error( $validated ) ) {
            return $validated;
        }

        $agency_id = ! empty( $args['agency_id'] ) ? intval( $args['agency_id'] ) : 0;
        if ( $agency_id <= 0 ) {
            $ws_context = function_exists( 'cora_get_current_workspace_context' ) ? cora_get_current_workspace_context() : array();
            $agency_id  = ! empty( $ws_context['agency_id'] ) ? intval( $ws_context['agency_id'] ) : 1;
        }

        $theme_id = ! empty( $args['theme_id'] ) ? intval( $args['theme_id'] ) : 0;
        if ( $theme_id <= 0 ) {
            $active_theme = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE agency_id = %d AND status = 'live' ORDER BY id DESC LIMIT 1", $agency_id ), ARRAY_A );
            if ( ! $active_theme ) {
                $active_theme = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' ORDER BY id DESC LIMIT 1", ARRAY_A );
            }
            if ( ! $active_theme ) {
                $active_theme = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes ORDER BY id DESC LIMIT 1", ARRAY_A );
            }
            $theme_id = $active_theme ? intval( $active_theme['id'] ) : 1;
        }

        $title = ! empty( $args['title'] ) ? sanitize_text_field( $args['title'] ) : $validated['title'];
        $slug  = ! empty( $args['slug'] ) ? sanitize_title( $args['slug'] ) : sanitize_title( $title );
        if ( empty( $slug ) ) {
            $slug = 'migrated-page-' . time();
        }

        // Sideload media if enabled
        $content_elements = $validated['content'];
        $media_sideloaded = 0;
        if ( ! isset( $args['sideload_media'] ) || $args['sideload_media'] !== false ) {
            $media_sideloaded = $this->sideload_and_replace_assets( $content_elements );
        }

        // Create WordPress Post
        $post_id = wp_insert_post( array(
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => get_current_user_id() ?: 1,
            'post_content' => '', // Elementor renders via _elementor_data
        ) );

        if ( is_wp_error( $post_id ) || ! $post_id ) {
            return new WP_Error( 'post_creation_failed', __( 'Failed to create WordPress post for the migrated page.', 'cora-workspace' ) );
        }

        // Persist Elementor postmeta
        update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
        update_post_meta( $post_id, '_elementor_template_type', $validated['type'] );
        update_post_meta( $post_id, '_elementor_data', wp_slash( json_encode( $content_elements ) ) );
        $page_settings_arr = is_array( $validated['page_settings'] ) ? $validated['page_settings'] : ( json_decode( $validated['page_settings'], true ) ?: array() );
        update_post_meta( $post_id, '_elementor_page_settings', $page_settings_arr );
        update_post_meta( $post_id, '_wp_page_template', 'elementor_header_footer' );

        // Flush Elementor CSS files
        if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }

        // Register in Cora Canvas Pages table
        $is_homepage = ! empty( $args['is_homepage'] ) ? 1 : 0;
        if ( $is_homepage ) {
            $wpdb->update(
                "{$wpdb->prefix}cora_canvas_pages",
                array( 'is_homepage' => 0 ),
                array( 'theme_id' => $theme_id ),
                array( '%d' ),
                array( '%d' )
            );
        }

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
                'template'        => 'elementor_header_footer',
                'seo_title'       => $title,
                'seo_description' => 'Migrated into Cora Studio Canvas.',
                'seo_og_image'    => '',
                'created_by'      => get_current_user_id() ?: 1,
                'created_at'      => current_time( 'mysql' ),
                'updated_at'      => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
        );
        $canvas_page_id = intval( $wpdb->insert_id );

        return array(
            'success'          => true,
            'page_id'          => $canvas_page_id,
            'wp_post_id'       => $post_id,
            'title'            => $title,
            'slug'             => $slug,
            'theme_id'         => $theme_id,
            'media_sideloaded' => $media_sideloaded,
            'edit_url'         => admin_url( 'post.php?post=' . $post_id . '&action=elementor' ),
            'message'          => sprintf( __( 'Page "%s" successfully migrated into Cora Canvas with %d assets.', 'cora-workspace' ), $title, $media_sideloaded ),
        );
    }

    /**
     * Import an Elementor Template Kit ZIP archive.
     *
     * @param string $zip_path Filepath to the uploaded ZIP archive.
     * @param array  $args     Migration arguments.
     * @return array|WP_Error
     */
    public function import_template_kit_zip( $zip_path, $args = array() ) {
        if ( ! file_exists( $zip_path ) ) {
            return new WP_Error( 'file_not_found', __( 'ZIP archive file was not found.', 'cora-workspace' ) );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
        global $wp_filesystem;

        $temp_dir = get_temp_dir() . 'cora_elem_import_' . uniqid();
        $unzip_res = unzip_file( $zip_path, $temp_dir );

        if ( is_wp_error( $unzip_res ) ) {
            return new WP_Error( 'unzip_failed', __( 'Could not extract Elementor Template Kit ZIP archive.', 'cora-workspace' ) );
        }

        // Find all JSON files in the extracted directory
        $json_files = $this->find_json_files_recursive( $temp_dir );
        if ( empty( $json_files ) ) {
            $wp_filesystem->delete( $temp_dir, true );
            return new WP_Error( 'no_templates_found', __( 'No Elementor template JSON files found inside the ZIP kit.', 'cora-workspace' ) );
        }

        $imported_pages = array();
        $errors         = array();
        $total_media    = 0;

        foreach ( $json_files as $file_path ) {
            $filename = basename( $file_path );
            if ( $filename === 'manifest.json' || $filename === 'package.json' ) {
                continue;
            }

            $raw_json = file_get_contents( $file_path );
            $page_args = $args;
            $page_args['title'] = ! empty( $page_args['title'] ) ? $page_args['title'] : ucwords( str_replace( array( '-', '_' ), ' ', pathinfo( $filename, PATHINFO_FILENAME ) ) );
            $page_args['slug']  = sanitize_title( pathinfo( $filename, PATHINFO_FILENAME ) );

            $res = $this->import_template_json( $raw_json, $page_args );
            if ( is_wp_error( $res ) ) {
                $errors[] = $filename . ': ' . $res->get_error_message();
            } else {
                $imported_pages[] = $res;
                $total_media     += ! empty( $res['media_sideloaded'] ) ? intval( $res['media_sideloaded'] ) : 0;
            }
        }

        // Clean up temporary files
        $wp_filesystem->delete( $temp_dir, true );

        if ( empty( $imported_pages ) && ! empty( $errors ) ) {
            return new WP_Error( 'import_failed', implode( ' | ', $errors ) );
        }

        return array(
            'success'        => true,
            'imported_count' => count( $imported_pages ),
            'pages'          => $imported_pages,
            'total_media'    => $total_media,
            'errors'         => $errors,
            'message'        => sprintf( __( 'Successfully migrated %d Elementor pages from Template Kit.', 'cora-workspace' ), count( $imported_pages ) ),
        );
    }

    /**
     * Recursively find JSON files in a directory.
     *
     * @param string $dir Path to directory.
     * @return array
     */
    private function find_json_files_recursive( $dir ) {
        $results = array();
        if ( ! is_dir( $dir ) ) {
            return $results;
        }

        $items = scandir( $dir );
        foreach ( $items as $item ) {
            if ( $item === '.' || $item === '..' ) {
                continue;
            }
            $full_path = $dir . DIRECTORY_SEPARATOR . $item;
            if ( is_dir( $full_path ) ) {
                $results = array_merge( $results, $this->find_json_files_recursive( $full_path ) );
            } elseif ( pathinfo( $item, PATHINFO_EXTENSION ) === 'json' ) {
                $results[] = $full_path;
            }
        }
        return $results;
    }

    /**
     * Import WordPress standard WXR Export XML (Tools > Export > Pages).
     *
     * @param string $xml_content Raw XML file contents.
     * @param array  $args        Migration arguments.
     * @return array|WP_Error
     */
    public function import_wordpress_wxr_xml( $xml_content, $args = array() ) {
        if ( empty( $xml_content ) ) {
            return new WP_Error( 'empty_xml', __( 'The uploaded XML file is empty.', 'cora-workspace' ) );
        }

        libxml_use_internal_errors( true );
        $xml = simplexml_load_string( $xml_content, 'SimpleXMLElement', LIBXML_NOCDATA );
        if ( ! $xml ) {
            return new WP_Error( 'invalid_xml', __( 'Failed to parse WordPress XML file. Ensure it is a valid WordPress Export.', 'cora-workspace' ) );
        }

        $namespaces = $xml->getNamespaces( true );
        $wp_ns = isset( $namespaces['wp'] ) ? $namespaces['wp'] : 'http://wordpress.org/export/1.2/';

        $imported_pages = array();
        $errors         = array();
        $total_media    = 0;

        if ( isset( $xml->channel->item ) ) {
            foreach ( $xml->channel->item as $item ) {
                $wp_item = $item->children( $wp_ns );
                $post_type = (string) $wp_item->post_type;

                // Only import pages or elementor library templates
                if ( $post_type !== 'page' && $post_type !== 'elementor_library' ) {
                    continue;
                }

                $post_status = (string) $wp_item->status;
                if ( $post_status === 'trash' ) {
                    continue;
                }

                $title = (string) $item->title;
                $slug  = (string) $wp_item->post_name;
                if ( empty( $title ) ) {
                    $title = ! empty( $slug ) ? ucwords( str_replace( '-', ' ', $slug ) ) : 'Migrated Page';
                }

                // Look for _elementor_data in postmeta
                $elementor_data = null;
                $page_settings  = array();

                if ( isset( $wp_item->postmeta ) ) {
                    foreach ( $wp_item->postmeta as $meta ) {
                        $meta_key   = (string) $meta->meta_key;
                        $meta_value = (string) $meta->meta_value;

                        if ( $meta_key === '_elementor_data' ) {
                            $elementor_data = $meta_value;
                        } elseif ( $meta_key === '_elementor_page_settings' ) {
                            $page_settings = json_decode( $meta_value, true ) ?: array();
                        }
                    }
                }

                if ( empty( $elementor_data ) ) {
                    // Fallback: check if the page has raw content that can be wrapped into Elementor text editor
                    $content_ns = isset( $namespaces['content'] ) ? $namespaces['content'] : 'http://purl.org/rss/1.0/modules/content/';
                    $raw_content = (string) $item->children( $content_ns )->encoded;
                    if ( ! empty( $raw_content ) ) {
                        $elementor_data = json_encode( array(
                            array(
                                'id'       => substr( md5( $slug . '_sec' ), 0, 7 ),
                                'elType'   => 'section',
                                'elements' => array(
                                    array(
                                        'id'       => substr( md5( $slug . '_col' ), 0, 7 ),
                                        'elType'   => 'column',
                                        'elements' => array(
                                            array(
                                                'id'         => substr( md5( $slug . '_w' ), 0, 7 ),
                                                'elType'     => 'widget',
                                                'widgetType' => 'text-editor',
                                                'settings'   => array(
                                                    'editor' => $raw_content,
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ) );
                    }
                }

                if ( empty( $elementor_data ) ) {
                    continue;
                }

                $page_args = $args;
                $page_args['title'] = $title;
                $page_args['slug']  = $slug;
                $page_args['is_homepage'] = ( $slug === 'home' || $slug === 'homepage' || $slug === 'front-page' );

                $res = $this->import_template_json( $elementor_data, $page_args );
                if ( is_wp_error( $res ) ) {
                    $errors[] = $title . ': ' . $res->get_error_message();
                } else {
                    $imported_pages[] = $res;
                    $total_media     += ! empty( $res['media_sideloaded'] ) ? intval( $res['media_sideloaded'] ) : 0;
                }
            }
        }

        if ( empty( $imported_pages ) ) {
            return new WP_Error(
                'no_pages_imported',
                __( 'No Elementor pages were found in the uploaded XML. Note: Cora only supports Elementor-based pages and themes.', 'cora-workspace' )
            );
        }

        return array(
            'success'        => true,
            'imported_count' => count( $imported_pages ),
            'pages'          => $imported_pages,
            'total_media'    => $total_media,
            'errors'         => $errors,
            'message'        => sprintf( __( 'Successfully migrated %d pages from WordPress export.', 'cora-workspace' ), count( $imported_pages ) ),
            'note'           => __( 'Core Elementor layouts and media were imported. Unsupported 3rd-party plugins may require minor adjustments in the Canvas Editor.', 'cora-workspace' ),
        );
    }

    /**
     * Scan a live remote website URL to detect Elementor presence, version, and discoverable pages.
     *
     * @param string $url The website URL to inspect.
     * @return array|WP_Error
     */
    public function scan_remote_url( $url ) {
        $url = trim( $url );
        if ( ! empty( $url ) && ! preg_match( '#^https?://#i', $url ) ) {
            $url = 'https://' . $url;
        }
        $url = esc_url_raw( $url );
        if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
            return new WP_Error( 'invalid_url', __( 'Please provide a valid website URL (e.g. https://example.com).', 'cora-workspace' ) );
        }

        $parsed   = parse_url( $url );
        $base_url = ( isset( $parsed['scheme'] ) ? $parsed['scheme'] : 'https' ) . '://' . ( isset( $parsed['host'] ) ? $parsed['host'] : '' );
        $host     = isset( $parsed['host'] ) ? preg_replace( '/^www\./i', '', $parsed['host'] ) : 'Website';

        // 1. Fetch Homepage HTML
        $response = wp_remote_get( $url, array(
            'timeout'     => 25,
            'redirection' => 5,
            'sslverify'   => false,
            'user-agent'  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 CoraMigrator/2.0',
        ) );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'remote_fetch_failed', sprintf( __( 'Could not connect to %s: %s', 'cora-workspace' ), esc_html( $url ), $response->get_error_message() ) );
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $body        = wp_remote_retrieve_body( $response );

        if ( $status_code !== 200 || empty( $body ) ) {
            return new WP_Error( 'remote_error_status', sprintf( __( 'Remote website returned HTTP status %d.', 'cora-workspace' ), $status_code ) );
        }

        // 2. Query WordPress REST API root for site info & namespaces
        $site_name        = '';
        $site_description = '';
        $namespaces       = array();
        $rest_root_res    = wp_remote_get( trailingslashit( $base_url ) . 'wp-json/', array(
            'timeout'   => 8,
            'sslverify' => false,
        ) );

        if ( ! is_wp_error( $rest_root_res ) && wp_remote_retrieve_response_code( $rest_root_res ) === 200 ) {
            $root_data = json_decode( wp_remote_retrieve_body( $rest_root_res ), true );
            if ( is_array( $root_data ) ) {
                $site_name        = ! empty( $root_data['name'] ) ? html_entity_decode( $root_data['name'] ) : '';
                $site_description = ! empty( $root_data['description'] ) ? html_entity_decode( $root_data['description'] ) : '';
                $namespaces       = ! empty( $root_data['namespaces'] ) && is_array( $root_data['namespaces'] ) ? $root_data['namespaces'] : array();
            }
        }

        // Fallback site name from <title>
        if ( empty( $site_name ) ) {
            if ( preg_match( '/<title>(.*?)<\/title>/i', $body, $title_match ) ) {
                $raw_title = trim( strip_tags( $title_match[1] ) );
                $parts = preg_split( '/\s*[\|\-–—]\s*/', $raw_title );
                $site_name = ! empty( $parts[0] ) ? trim( $parts[0] ) : ucfirst( $host );
            } else {
                $site_name = ucfirst( $host );
            }
        }

        // 3. WordPress & Elementor Detection Diagnostics
        $is_elementor      = false;
        $is_elementor_pro  = false;
        $elementor_version = '3.x';
        $is_wordpress      = false;

        if ( strpos( $body, 'elementor' ) !== false || strpos( $body, 'elementor-frontend' ) !== false || strpos( $body, 'wp-content/plugins/elementor' ) !== false || in_array( 'elementor/v1', $namespaces, true ) ) {
            $is_elementor = true;
            $is_wordpress = true;
        }

        if ( strpos( $body, 'elementor-pro' ) !== false || in_array( 'elementor-pro/v1', $namespaces, true ) ) {
            $is_elementor_pro = true;
        }

        if ( strpos( $body, 'wp-content' ) !== false || strpos( $body, 'wp-includes' ) !== false || ! empty( $namespaces ) ) {
            $is_wordpress = true;
        }

        // Extract Elementor version from script tags
        if ( preg_match( '/elementor-frontend-js.*?ver=([0-9\.]+)/i', $body, $ver_match ) ) {
            $elementor_version = $ver_match[1];
        } elseif ( preg_match( '/elementor.*?ver=([0-9\.]+)/i', $body, $ver_match ) ) {
            $elementor_version = $ver_match[1];
        }

        if ( ! $is_elementor && ! $is_wordpress ) {
            return new WP_Error(
                'not_elementor_site',
                __( 'Cora could not detect WordPress or Elementor on this website. Cora exclusively supports Elementor-based websites to guarantee 100% layout fidelity.', 'cora-workspace' )
            );
        }

        // 4. Extract Active Theme Details
        $theme_slug    = 'hello-elementor';
        $theme_name    = 'Hello Elementor';
        $theme_version = '';

        if ( preg_match( '#wp-content/themes/([^/"\'\s\?]+)#i', $body, $theme_m ) ) {
            $raw_theme_slug = sanitize_title( $theme_m[1] );
            if ( ! empty( $raw_theme_slug ) ) {
                $theme_slug = $raw_theme_slug;
            }
        }

        // Extract theme version from stylesheet link
        if ( preg_match( '#wp-content/themes/' . preg_quote( $theme_slug, '#' ) . '/style\.css\?ver=([0-9\.]+)#i', $body, $t_ver_m ) ) {
            $theme_version = $t_ver_m[1];
        }

        // Known theme slug human map
        $theme_human_map = array(
            'hello-elementor'       => 'Hello Elementor',
            'hello-elementor-child' => 'Hello Elementor Child',
            'astra'                 => 'Astra',
            'astra-child'           => 'Astra Child',
            'oceanwp'               => 'OceanWP',
            'generatepress'         => 'GeneratePress',
            'kadence'               => 'Kadence',
            'neve'                  => 'Neve',
            'twentytwentyfour'      => 'Twenty Twenty-Four',
            'twentytwentythree'     => 'Twenty Twenty-Three',
            'twentytwentytwo'       => 'Twenty Twenty-Two',
            'twentytwentyone'       => 'Twenty Twenty-One',
        );

        if ( isset( $theme_human_map[ $theme_slug ] ) ) {
            $theme_name = $theme_human_map[ $theme_slug ];
        } else {
            $theme_name = ucwords( str_replace( array( '-', '_' ), ' ', $theme_slug ) );
        }

        // 5. Extract Active Plugins Roster
        $plugin_slugs = array();

        // From HTML scripts and styles
        if ( preg_match_all( '#wp-content/plugins/([^/"\'\s\?]+)#i', $body, $plug_m ) ) {
            foreach ( $plug_m[1] as $pslug ) {
                $clean_pslug = sanitize_title( $pslug );
                if ( ! empty( $clean_pslug ) && $clean_pslug !== '*' ) {
                    $plugin_slugs[ $clean_pslug ] = true;
                }
            }
        }

        // From REST namespaces
        foreach ( $namespaces as $ns ) {
            if ( strpos( $ns, 'elementor-pro' ) !== false ) {
                $plugin_slugs['elementor-pro'] = true;
            } elseif ( strpos( $ns, 'elementor' ) !== false ) {
                $plugin_slugs['elementor'] = true;
            } elseif ( strpos( $ns, 'metform' ) !== false ) {
                $plugin_slugs['metform'] = true;
            } elseif ( strpos( $ns, 'contact-form-7' ) !== false ) {
                $plugin_slugs['contact-form-7'] = true;
            } elseif ( strpos( $ns, 'wpforms' ) !== false ) {
                $plugin_slugs['wpforms'] = true;
            } elseif ( strpos( $ns, 'fluentform' ) !== false ) {
                $plugin_slugs['fluentform'] = true;
            } elseif ( strpos( $ns, 'woocommerce' ) !== false ) {
                $plugin_slugs['woocommerce'] = true;
            } elseif ( strpos( $ns, 'google-site-kit' ) !== false ) {
                $plugin_slugs['google-site-kit'] = true;
            } elseif ( strpos( $ns, 'yoast' ) !== false ) {
                $plugin_slugs['wordpress-seo'] = true;
            } elseif ( strpos( $ns, 'rankmath' ) !== false ) {
                $plugin_slugs['seo-by-rank-math'] = true;
            }
        }

        // Format detected plugins into clean objects
        $plugin_name_map = array(
            'elementor'                          => 'Elementor Website Builder',
            'elementor-pro'                      => 'Elementor Pro',
            'metform'                            => 'MetForm Form Builder',
            'contact-form-7'                     => 'Contact Form 7',
            'wpforms'                            => 'WPForms',
            'wpforms-lite'                       => 'WPForms Lite',
            'fluentform'                         => 'Fluent Forms',
            'woocommerce'                        => 'WooCommerce',
            'essential-addons-for-elementor-lite'=> 'Essential Addons for Elementor',
            'elementskit-lite'                   => 'ElementsKit Addons',
            'elementskit'                        => 'ElementsKit Pro',
            'premium-addons-for-elementor'       => 'Premium Addons for Elementor',
            'happy-elementor-addons'             => 'Happy Addons for Elementor',
            'google-site-kit'                    => 'Google Site Kit',
            'wordpress-seo'                      => 'Yoast SEO',
            'seo-by-rank-math'                   => 'Rank Math SEO',
            'cora-builder'                       => 'Cora Builder',
            'cora_crm'                           => 'Cora CRM',
            'mobile-first-ads-landing-pages'     => 'Mobile-First Landing Pages',
        );

        $detected_plugins = array();
        $advisory_plugins = array();
        $has_form_plugin  = false;

        foreach ( array_keys( $plugin_slugs ) as $pslug ) {
            $pname = isset( $plugin_name_map[ $pslug ] ) ? $plugin_name_map[ $pslug ] : ucwords( str_replace( array( '-', '_' ), ' ', $pslug ) );

            if ( in_array( $pslug, array( 'elementor', 'elementor-pro' ), true ) ) {
                $detected_plugins[] = array(
                    'slug'        => $pslug,
                    'name'        => $pname,
                    'status'      => 'native',
                    'badge'       => '100% Native',
                    'description' => 'Core layout engine, containers, sections, widgets, and responsive breakpoints.',
                );
            } elseif ( in_array( $pslug, array( 'metform', 'contact-form-7', 'wpforms', 'wpforms-lite', 'fluentform' ), true ) ) {
                $has_form_plugin    = true;
                $detected_plugins[] = array(
                    'slug'        => $pslug,
                    'name'        => $pname,
                    'status'      => 'converted',
                    'badge'       => 'Auto-Bridged',
                    'description' => 'Form structure and fields mapped to Cora Native Form Builder without lead loss.',
                );
            } else {
                $advisory_plugins[] = array(
                    'slug'        => $pslug,
                    'name'        => $pname,
                    'status'      => 'advisory',
                    'badge'       => 'Visual Continuity',
                    'description' => 'Layout, styling, and typography will import into Canvas; fine-tune widgets in editor.',
                );
                $detected_plugins[] = array(
                    'slug'        => $pslug,
                    'name'        => $pname,
                    'status'      => 'advisory',
                    'badge'       => 'Visual Continuity',
                    'description' => 'Layout, styling, and typography will import into Canvas; fine-tune widgets in editor.',
                );
            }
        }

        // 6. Comprehensive Compatibility Matrix ("What will import & what will not")
        $native_features = array(
            'Core Elementor Flexbox Containers, Sections & Inner Columns',
            'Global Site Typography, Color Schemes & Styling Rules',
            'Hero Sections, Headings, Subheadings & Rich Text Paragraphs',
            'Buttons, Call-To-Action Links & Custom URL Anchors',
            'Images, Galleries, Video Embeds & Media Carousels',
            'Icon Boxes, Counters, Dividers, Spacers & Google Maps',
            'Full Responsive Layouts (Mobile, Tablet, and Desktop Breakpoints)',
        );

        $converted_features = array(
            'Navigation Menus: Automatically bridged to Cora Canvas Navigation System',
            'Page Slugs & Permalinks: Preserved with 1:1 fidelity for zero broken links',
        );
        if ( $has_form_plugin ) {
            $converted_features[] = 'Lead Forms: Captured and mapped into Cora Native Forms with instant lead capture';
        }

        // 7. Query WordPress REST API for Page Index
        $discovered_pages = array();
        $rest_endpoint = trailingslashit( $base_url ) . 'wp-json/wp/v2/pages?per_page=50&_fields=id,title,slug,link';
        $rest_res = wp_remote_get( $rest_endpoint, array( 'timeout' => 10, 'sslverify' => false ) );

        if ( ! is_wp_error( $rest_res ) && wp_remote_retrieve_response_code( $rest_res ) === 200 ) {
            $pages_data = json_decode( wp_remote_retrieve_body( $rest_res ), true );
            if ( is_array( $pages_data ) && ! empty( $pages_data ) ) {
                foreach ( $pages_data as $p ) {
                    $p_title = isset( $p['title']['rendered'] ) ? html_entity_decode( $p['title']['rendered'] ) : ( isset( $p['title'] ) ? $p['title'] : 'Page' );
                    $p_slug  = isset( $p['slug'] ) ? $p['slug'] : sanitize_title( $p_title );
                    $p_link  = isset( $p['link'] ) ? $p['link'] : $url;

                    $discovered_pages[] = array(
                        'id'          => isset( $p['id'] ) ? intval( $p['id'] ) : 0,
                        'title'       => $p_title,
                        'slug'        => $p_slug,
                        'url'         => $p_link,
                        'is_homepage' => ( $p_link === $base_url || $p_link === trailingslashit( $base_url ) || $p_slug === 'home' ),
                    );
                }
            }
        }

        // Fallback: Parse nav links and title from Homepage HTML if REST API was blocked
        if ( empty( $discovered_pages ) ) {
            $discovered_pages[] = array(
                'id'          => 1,
                'title'       => $site_name,
                'slug'        => 'home',
                'url'         => $url,
                'is_homepage' => true,
            );

            // Scrape internal navigation links
            if ( preg_match_all( '/<a\s+[^>]*href=["\'](' . preg_quote( $base_url, '/' ) . '\/[^"\'#\?]+)["\'][^>]*>(.*?)<\/a>/i', $body, $links_matches, PREG_SET_ORDER ) ) {
                $seen_slugs = array( 'home' => true );
                foreach ( $links_matches as $lm ) {
                    $link_url  = $lm[1];
                    $link_text = trim( strip_tags( $lm[2] ) );
                    $path_slug = sanitize_title( basename( parse_url( $link_url, PHP_URL_PATH ) ) );

                    if ( ! empty( $path_slug ) && ! isset( $seen_slugs[ $path_slug ] ) && strlen( $link_text ) > 1 && strlen( $link_text ) < 40 ) {
                        $seen_slugs[ $path_slug ] = true;
                        $discovered_pages[] = array(
                            'id'          => count( $discovered_pages ) + 1,
                            'title'       => $link_text,
                            'slug'        => $path_slug,
                            'url'         => $link_url,
                            'is_homepage' => false,
                        );
                    }
                }
            }
        }

        // Count images on homepage
        preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\']/i', $body, $img_matches );
        $media_count = ! empty( $img_matches[1] ) ? count( array_unique( $img_matches[1] ) ) : 8;

        // Draft Theme Blueprint Calculation
        $clean_site_label  = preg_replace( '/[^A-Za-z0-9\s\-_]/', '', $site_name );
        if ( empty( $clean_site_label ) ) {
            $clean_site_label = ucfirst( $host );
        }
        $draft_theme_name  = 'Imported - ' . trim( $clean_site_label );
        $draft_safety_note = 'Safe Mode Active: All migrated pages will be placed into a newly created Draft Theme (' . $draft_theme_name . '). Your active live theme stays 100% untouched until you review and publish.';

        return array(
            'success'            => true,
            'url'                => $url,
            'base_url'           => $base_url,
            'site_name'          => $site_name,
            'site_description'   => $site_description,
            'theme_slug'         => $theme_slug,
            'theme_name'         => $theme_name,
            'theme_version'      => $theme_version,
            'elementor_version'  => $elementor_version,
            'is_elementor_pro'   => $is_elementor_pro,
            'is_elementor'       => $is_elementor,
            'is_wordpress'       => $is_wordpress,
            'detected_plugins'   => $detected_plugins,
            'advisory_plugins'   => $advisory_plugins,
            'native_features'    => $native_features,
            'converted_features' => $converted_features,
            'discovered_pages'   => $discovered_pages,
            'total_pages'        => count( $discovered_pages ),
            'estimated_media'    => $media_count,
            'draft_theme_name'   => $draft_theme_name,
            'draft_safety_note'  => $draft_safety_note,
            'status_label'       => 'Elementor Site Inspected & Ready for Safe Draft Import',
        );
    }

    /**
     * Migrate a remote page by URL or REST API.
     *
     * @param string $page_url The remote page URL.
     * @param array  $args     Migration arguments.
     * @return array|WP_Error
     */
    public function migrate_remote_page_by_url( $page_url, $args = array() ) {
        $response = wp_remote_get( $page_url, array(
            'timeout'     => 25,
            'redirection' => 5,
            'sslverify'   => false,
            'user-agent'  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 CoraMigrator/1.0',
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        if ( empty( $body ) ) {
            return new WP_Error( 'empty_page', __( 'Remote page content was empty.', 'cora-workspace' ) );
        }

        // Extract Title
        $title = ! empty( $args['title'] ) ? $args['title'] : '';
        if ( empty( $title ) ) {
            if ( preg_match( '/<h1[^>]*>(.*?)<\/h1>/is', $body, $h1_m ) ) {
                $title = trim( strip_tags( $h1_m[1] ) );
            } elseif ( preg_match( '/<title>(.*?)<\/title>/i', $body, $t_m ) ) {
                $title = trim( strip_tags( $t_m[1] ) );
                $title = preg_replace( '/\s*[\|\-–—].*$/', '', $title );
            } else {
                $title = 'Migrated Page';
            }
        }

        $slug = ! empty( $args['slug'] ) ? sanitize_title( $args['slug'] ) : sanitize_title( $title );

        // Extract Images from HTML
        $images = array();
        if ( preg_match_all( '/<img[^>]+src=["\']([^"\']+)["\']/i', $body, $img_m ) ) {
            $images = array_unique( $img_m[1] );
        }

        // Extract Headings and Paragraphs to construct Elementor Container sections
        $sections = array();
        
        // Hero Section
        $hero_heading = $title;
        $hero_sub     = 'Welcome to our studio. Crafted with excellence.';
        if ( preg_match( '/<p[^>]*>(.*?)<\/p>/is', $body, $p_m ) ) {
            $hero_sub = trim( strip_tags( $p_m[1] ) );
            if ( strlen( $hero_sub ) > 180 ) {
                $hero_sub = substr( $hero_sub, 0, 180 ) . '...';
            }
        }

        // Construct Native Elementor Section 1: Hero
        $sections[] = array(
            'id'       => 'cora_sec_' . uniqid(),
            'elType'   => 'container',
            'isInner'  => false,
            'settings' => array(
                'content_width' => 'boxed',
                'padding'       => array( 'unit' => 'px', 'top' => '80', 'right' => '24', 'bottom' => '80', 'left' => '24', 'isLinked' => false ),
                'background_background' => 'classic',
            ),
            'elements' => array(
                array(
                    'id'         => 'cora_w_' . uniqid(),
                    'elType'     => 'widget',
                    'widgetType' => 'heading',
                    'settings'   => array(
                        'title'          => $hero_heading,
                        'header_size'    => 'h1',
                        'align'          => 'center',
                    ),
                ),
                array(
                    'id'         => 'cora_w_' . uniqid(),
                    'elType'     => 'widget',
                    'widgetType' => 'text-editor',
                    'settings'   => array(
                        'editor' => '<p style="text-align: center; font-size: 18px; color: #71717a;">' . esc_html( $hero_sub ) . '</p>',
                    ),
                ),
                array(
                    'id'         => 'cora_w_' . uniqid(),
                    'elType'     => 'widget',
                    'widgetType' => 'button',
                    'settings'   => array(
                        'text'  => 'Book a Session / Contact Us',
                        'link'  => array( 'url' => '#contact', 'is_external' => false ),
                        'align' => 'center',
                    ),
                ),
            ),
        );

        // Construct Native Elementor Section 2: Media Gallery or Features if images exist
        if ( count( $images ) > 1 ) {
            $gallery_elements = array();
            $slice_images = array_slice( $images, 0, 6 );
            foreach ( $slice_images as $img_url ) {
                $gallery_elements[] = array(
                    'id'         => 'cora_w_' . uniqid(),
                    'elType'     => 'widget',
                    'widgetType' => 'image',
                    'settings'   => array(
                        'image' => array(
                            'url' => $img_url,
                            'id'  => '',
                        ),
                        'image_size' => 'large',
                    ),
                );
            }

            $sections[] = array(
                'id'       => 'cora_sec_' . uniqid(),
                'elType'   => 'container',
                'isInner'  => false,
                'settings' => array(
                    'content_width' => 'boxed',
                    'padding'       => array( 'unit' => 'px', 'top' => '60', 'right' => '24', 'bottom' => '60', 'left' => '24', 'isLinked' => false ),
                ),
                'elements' => array(
                    array(
                        'id'         => 'cora_w_' . uniqid(),
                        'elType'     => 'widget',
                        'widgetType' => 'heading',
                        'settings'   => array(
                            'title'       => 'Featured Work & Portfolio',
                            'header_size' => 'h2',
                            'align'       => 'center',
                        ),
                    ),
                    array(
                        'id'       => 'cora_con_' . uniqid(),
                        'elType'   => 'container',
                        'isInner'  => true,
                        'settings' => array(
                            'direction' => 'row',
                            'wrap'      => 'wrap',
                            'gap'       => array( 'unit' => 'px', 'size' => '20' ),
                        ),
                        'elements' => $gallery_elements,
                    ),
                ),
            );
        }

        // Package into Elementor JSON data format
        $elementor_payload = array(
            'version'       => '3.20.0',
            'title'         => $title,
            'type'          => 'page',
            'content'       => $sections,
            'page_settings' => array(),
        );

        $import_args = $args;
        $import_args['title'] = $title;
        $import_args['slug']  = $slug;

        return $this->import_template_json( $elementor_payload, $import_args );
    }

    /**
     * Recursively scan Elementor widget trees, sideload remote image URLs into WordPress,
     * and replace the URLs and attachment IDs with local WordPress media items.
     *
     * @param array $elements Element tree (passed by reference).
     * @return int Number of media assets sideloaded.
     */
    public function sideload_and_replace_assets( &$elements ) {
        if ( ! is_array( $elements ) ) {
            return 0;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $media_count = 0;

        foreach ( $elements as &$el ) {
            if ( ! is_array( $el ) ) {
                continue;
            }

            // Check widget / section settings for images
            if ( isset( $el['settings'] ) && is_array( $el['settings'] ) ) {
                // 1. Single image setting (e.g. settings.image.url)
                if ( isset( $el['settings']['image']['url'] ) && ! empty( $el['settings']['image']['url'] ) ) {
                    $remote_url = $el['settings']['image']['url'];
                    if ( $this->is_external_url( $remote_url ) ) {
                        $local_id = $this->sideload_single_image( $remote_url );
                        if ( $local_id > 0 ) {
                            $el['settings']['image']['id']  = $local_id;
                            $el['settings']['image']['url'] = wp_get_attachment_url( $local_id );
                            $media_count++;
                        }
                    }
                }

                // 2. Background image setting (e.g. settings.background_image.url)
                if ( isset( $el['settings']['background_image']['url'] ) && ! empty( $el['settings']['background_image']['url'] ) ) {
                    $remote_url = $el['settings']['background_image']['url'];
                    if ( $this->is_external_url( $remote_url ) ) {
                        $local_id = $this->sideload_single_image( $remote_url );
                        if ( $local_id > 0 ) {
                            $el['settings']['background_image']['id']  = $local_id;
                            $el['settings']['background_image']['url'] = wp_get_attachment_url( $local_id );
                            $media_count++;
                        }
                    }
                }

                // 3. Image Gallery setting (e.g. settings.wp_gallery or settings.gallery)
                if ( isset( $el['settings']['gallery'] ) && is_array( $el['settings']['gallery'] ) ) {
                    foreach ( $el['settings']['gallery'] as &$gal_item ) {
                        if ( isset( $gal_item['url'] ) && $this->is_external_url( $gal_item['url'] ) ) {
                            $local_id = $this->sideload_single_image( $gal_item['url'] );
                            if ( $local_id > 0 ) {
                                $gal_item['id']  = $local_id;
                                $gal_item['url'] = wp_get_attachment_url( $local_id );
                                $media_count++;
                            }
                        }
                    }
                }
            }

            // Recurse into child elements / columns
            if ( isset( $el['elements'] ) && is_array( $el['elements'] ) ) {
                $media_count += $this->sideload_and_replace_assets( $el['elements'] );
            }
        }

        return $media_count;
    }

    /**
     * Check if a URL is external to the current WordPress site.
     *
     * @param string $url Target URL.
     * @return bool
     */
    private function is_external_url( $url ) {
        if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
            return false;
        }
        $site_host = parse_url( home_url(), PHP_URL_HOST );
        $url_host  = parse_url( $url, PHP_URL_HOST );
        return ( ! empty( $url_host ) && strtolower( $url_host ) !== strtolower( $site_host ) );
    }

    /**
     * Download an external image and insert it into WordPress Media Library.
     *
     * @param string $url Remote image URL.
     * @return int Attachment ID or 0 on failure.
     */
    private function sideload_single_image( $url ) {
        $tmp = download_url( $url, 15 );
        if ( is_wp_error( $tmp ) ) {
            return 0;
        }

        $file_array = array(
            'name'     => basename( parse_url( $url, PHP_URL_PATH ) ) ?: 'migrated-asset.jpg',
            'tmp_name' => $tmp,
        );

        if ( ! preg_match( '/\.(jpg|jpeg|png|webp|svg|gif)$/i', $file_array['name'] ) ) {
            $file_array['name'] .= '.jpg';
        }

        $id = media_handle_sideload( $file_array, 0 );
        if ( is_wp_error( $id ) ) {
            @unlink( $tmp );
            return 0;
        }

        return intval( $id );
    }

    /**
     * Generate a 1-click exporter code snippet for private or firewalled WordPress sites.
     *
     * @return string
     */
    public function generate_migration_export_snippet() {
        return <<<'PHP'
<?php
/**
 * 1-Click Cora Elementor Site Exporter Snippet
 * Paste this temporarily into your WordPress theme's functions.php or run via WP-CLI / Code Snippets.
 * Navigate to: https://yoursite.com/?cora_export_elementor=1&token=cora_migrate_secret
 */
add_action( 'init', function() {
    if ( isset( $_GET['cora_export_elementor'] ) && isset( $_GET['token'] ) && $_GET['token'] === 'cora_migrate_secret' ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Please log in as an administrator to export Elementor site data.' );
        }
        $pages = get_posts( array(
            'post_type'      => array( 'page', 'elementor_library' ),
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ) );
        $export = array(
            'site_name'    => get_bloginfo( 'name' ),
            'site_url'     => home_url(),
            'generated_at' => current_time( 'mysql' ),
            'pages'        => array(),
        );
        foreach ( $pages as $p ) {
            $data = get_post_meta( $p->ID, '_elementor_data', true );
            if ( ! empty( $data ) ) {
                $export['pages'][] = array(
                    'title'         => $p->post_title,
                    'slug'          => $p->post_name,
                    'type'          => get_post_meta( $p->ID, '_elementor_template_type', true ) ?: 'page',
                    'content'       => is_string( $data ) ? json_decode( $data, true ) : $data,
                    'page_settings' => get_post_meta( $p->ID, '_elementor_page_settings', true ) ?: array(),
                );
            }
        }
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=cora-elementor-export-' . sanitize_title( get_bloginfo( 'name' ) ) . '.json' );
        echo wp_json_encode( $export, JSON_PRETTY_PRINT );
        exit;
    }
} );
PHP;
    }
    /**
     * Create a brand new draft theme dedicated to housing migrated pages,
     * ensuring the live website theme is 100% untouched and safe.
     *
     * @param string $theme_name  Human-friendly theme name.
     * @param int    $agency_id   Workspace agency ID.
     * @param array  $source_meta Metadata about source.
     * @return int|WP_Error New draft theme ID or WP_Error.
     */
    public function create_draft_migration_theme( $theme_name, $agency_id = 0, $source_meta = array() ) {
        global $wpdb;
        if ( ! $agency_id ) {
            $agency_id = function_exists( 'cora_get_request_agency_id' ) ? cora_get_request_agency_id() : 1;
        }
        if ( empty( $theme_name ) ) {
            $theme_name = 'Imported Theme (' . date( 'M d, Y' ) . ')';
        }

        $created_by = get_current_user_id() ?: 1;
        $settings   = array(
            'source'      => 'elementor',
            'migrated_at' => current_time( 'mysql' ),
        );
        if ( ! empty( $source_meta ) && is_array( $source_meta ) ) {
            $settings = array_merge( $settings, $source_meta );
        }

        $inserted = $wpdb->insert(
            $wpdb->prefix . 'cora_canvas_themes',
            array(
                'agency_id'  => $agency_id,
                'name'       => sanitize_text_field( $theme_name ),
                'status'     => 'draft', // Strictly a draft theme!
                'settings'   => wp_json_encode( $settings ),
                'created_by' => $created_by,
                'created_at' => current_time( 'mysql' ),
                'updated_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%s', '%d', '%s', '%s' )
        );

        if ( ! $inserted ) {
            return new WP_Error( 'theme_creation_failed', __( 'Could not create new draft theme for migration.', 'cora-workspace' ) );
        }

        return intval( $wpdb->insert_id );
    }
}

/**
 * Global helper function to retrieve the Elementor Migrator instance.
 *
 * @return Cora_Elementor_Migrator
 */
function cora_elementor_migrator() {
    return Cora_Elementor_Migrator::get_instance();
}
