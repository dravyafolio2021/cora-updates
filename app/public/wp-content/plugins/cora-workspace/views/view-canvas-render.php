<?php
/**
 * Elementor Page Canvas Render Template
 */
while ( ob_get_level() > 0 ) {
    ob_end_clean();
}
status_header( 200 );
nocache_headers();

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

// Render Elementor Header
if ( function_exists( 'elementor_theme_do_location' ) ) {
    elementor_theme_do_location( 'header' );
}

$page_id = isset( $GLOBALS['cora_canvas_render_page_id'] ) ? intval( $GLOBALS['cora_canvas_render_page_id'] ) : get_the_ID();
if ( $page_id > 0 ) {
    global $post;
    $post = get_post( $page_id );
    setup_postdata( $post );
}
if ( class_exists( '\Elementor\Plugin' ) && $page_id ) {
    $elementor_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $page_id );
    if ( ! empty( $elementor_content ) ) {
        echo $elementor_content;
    } else {
        the_content();
    }
} else {
    the_content();
}

// Render Elementor Footer
if ( function_exists( 'elementor_theme_do_location' ) ) {
    elementor_theme_do_location( 'footer' );
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
