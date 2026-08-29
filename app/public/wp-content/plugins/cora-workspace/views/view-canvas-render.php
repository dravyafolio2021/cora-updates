<?php
/**
 * Elementor Page Canvas Render Template with Resilient Theme & Kit Sync
 */
while ( ob_get_level() > 0 ) {
    ob_end_clean();
}
status_header( 200 );
nocache_headers();

global $wpdb;
$ws = function_exists( 'cora_get_current_workspace_context' ) ? cora_get_current_workspace_context() : array();
$agency_id = ! empty( $ws['agency_id'] ) ? intval( $ws['agency_id'] ) : 1;

$live_theme = $wpdb->get_row( $wpdb->prepare( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE agency_id = %d AND status = 'live' ORDER BY id DESC LIMIT 1", $agency_id ), ARRAY_A );
if ( ! $live_theme ) {
    $live_theme = $wpdb->get_row( "SELECT settings FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' ORDER BY id DESC LIMIT 1", ARRAY_A );
}
$theme_settings = $live_theme ? ( json_decode( $live_theme['settings'], true ) ?: array() ) : array();

// Auto-discover published Elementor Header library templates if not explicitly configured
if ( $header_template_id <= 0 ) {
    $found_headers = get_posts( array(
        'post_type'      => 'elementor_library',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'   => '_elementor_template_type',
                'value' => 'header',
            ),
        ),
    ) );
    if ( ! empty( $found_headers ) ) {
        $header_template_id = $found_headers[0]->ID;
    }
}

// Auto-discover published Elementor Footer library templates if not explicitly configured
if ( $footer_template_id <= 0 ) {
    $found_footers = get_posts( array(
        'post_type'      => 'elementor_library',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'   => '_elementor_template_type',
                'value' => 'footer',
            ),
        ),
    ) );
    if ( ! empty( $found_footers ) ) {
        $footer_template_id = $found_footers[0]->ID;
    }
}

echo '<!DOCTYPE html>';
echo '<html ';
language_attributes();
echo '>';
echo '<head>';
echo '<meta charset="' . get_bloginfo( 'charset' ) . '">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
wp_head();
echo '</head>';
echo '<body ';
body_class();
echo '>';

// 1. Render Elementor Header (Native Theme Builder location, Canvas linked template, or Default Header fallback)
$header_rendered = false;
if ( function_exists( 'elementor_theme_do_location' ) ) {
    $header_rendered = (bool) elementor_theme_do_location( 'header' );
}
if ( ! $header_rendered && $header_template_id > 0 && class_exists( '\Elementor\Plugin' ) ) {
    $header_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $header_template_id );
    if ( ! empty( $header_content ) ) {
        echo '<header id="cora-canvas-site-header">' . $header_content . '</header>';
        $header_rendered = true;
    }
}
if ( ! $header_rendered ) {
    $site_name = get_bloginfo( 'name' );
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo_img = $custom_logo_id ? wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'class' => 'h-8 w-auto object-contain' ) ) : '';
    ?>
    <header id="cora-canvas-site-header" style="width: 100%; background-color: #ffffff; border-bottom: 1px solid #e4e4e7; padding: 14px 24px; position: sticky; top: 0; z-index: 999; display: flex; align-items: center; justify-content: space-between; font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="display: flex; align-items: center; gap: 8px; color: #18181b; font-weight: 700; font-size: 16px; text-decoration: none; letter-spacing: -0.01em;">
                <?php if ( $logo_img ) : ?>
                    <?php echo $logo_img; ?>
                <?php else : ?>
                    <span><?php echo esc_html( $site_name ?: 'Claroverse' ); ?></span>
                <?php endif; ?>
            </a>
        </div>
        <nav style="display: flex; align-items: center; gap: 20px; font-size: 13px; font-weight: 600;">
            <?php
            $pages = get_pages( array( 'number' => 6, 'sort_column' => 'menu_order' ) );
            if ( ! empty( $pages ) ) {
                foreach ( $pages as $p ) {
                    echo '<a href="' . esc_url( get_permalink( $p->ID ) ) . '" style="color: #52525b; text-decoration: none; transition: color 0.15s ease;">' . esc_html( $p->post_title ) . '</a>';
                }
            } else {
                echo '<a href="' . esc_url( home_url( '/' ) ) . '" style="color: #52525b; text-decoration: none;">Home</a>';
            }
            ?>
        </nav>
    </header>
    <?php
}

// 2. Render Page Content
$page_id = isset( $GLOBALS['cora_canvas_render_page_id'] ) ? intval( $GLOBALS['cora_canvas_render_page_id'] ) : get_the_ID();
if ( $page_id > 0 ) {
    global $post;
    $post = get_post( $page_id );
    setup_postdata( $post );
}
if ( class_exists( '\Elementor\Plugin' ) && $page_id ) {
    $elementor_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $page_id );
    if ( ! empty( $elementor_content ) ) {
        echo '<main id="cora-canvas-main-content">' . $elementor_content . '</main>';
    } else {
        echo '<main id="cora-canvas-main-content">';
        the_content();
        echo '</main>';
    }
} else {
    echo '<main id="cora-canvas-main-content">';
    the_content();
    echo '</main>';
}

// 3. Render Elementor Footer (Native Theme Builder location or Canvas linked template)
$footer_rendered = false;
if ( function_exists( 'elementor_theme_do_location' ) ) {
    $footer_rendered = elementor_theme_do_location( 'footer' );
}
if ( ! $footer_rendered && $footer_template_id > 0 && class_exists( '\Elementor\Plugin' ) ) {
    $footer_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $footer_template_id );
    if ( ! empty( $footer_content ) ) {
        echo '<footer id="cora-canvas-site-footer">' . $footer_content . '</footer>';
    }
}

// Output preview bar script if applicable
if ( isset( $GLOBALS['cora_preview_bar_script'] ) ) {
    echo $GLOBALS['cora_preview_bar_script'];
}

wp_footer();
echo '<style>html, body { overflow-x: hidden !important; } body.hostinger-ai-builder-elementor, body.hostinger-ai-builder-gutenberg { padding-top: 0px !important; } #wpadminbar { display: none !important; } body.admin-bar { margin-top: 0 !important; padding-top: 0 !important; } *, *:before, *:after { max-width: 100% !important; box-sizing: border-box !important; }</style>';
echo '</body>';
echo '</html>';
exit;
