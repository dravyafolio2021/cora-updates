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

the_content();

// Render Elementor Footer
if ( function_exists( 'elementor_theme_do_location' ) ) {
    elementor_theme_do_location( 'footer' );
}

// Output preview bar script if applicable
if ( isset( $GLOBALS['cora_preview_bar_script'] ) ) {
    echo $GLOBALS['cora_preview_bar_script'];
}

wp_footer();
echo '<style>html, body { overflow-x: hidden !important; } *, *:before, *:after { max-width: 100% !important; box-sizing: border-box !important; }</style>';
echo '</body>';
echo '</html>';
exit;
