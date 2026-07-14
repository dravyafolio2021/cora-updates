<?php
$file = '/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-studio-ai-locked/cora-studio-ai.php';
$content = file_get_contents($file);

$ajax = "
function cora_ajax_resolve_map_url() {
    check_ajax_referer('cora_ajax_nonce', 'nonce');
    if (!is_user_logged_in()) {
        wp_send_json_error('Permission denied.');
    }
    \$url = isset(\$_POST['url']) ? esc_url_raw(\$_POST['url']) : '';
    if (empty(\$url) || strpos(\$url, 'http') !== 0) {
        wp_send_json_error('Invalid URL.');
    }
    
    // We just want to get headers to follow redirect
    \$response = wp_remote_head(\$url, array('redirection' => 5));
    if (is_wp_error(\$response)) {
        // fallback to get
        \$response = wp_remote_get(\$url, array('redirection' => 5));
        if (is_wp_error(\$response)) {
            wp_send_json_error('Could not resolve URL.');
        }
    }
    
    \$redirect_url = wp_remote_retrieve_header(\$response, 'location');
    if (empty(\$redirect_url)) {
        // Maybe it's already the final URL, or wp_remote_head followed it internally
        \$redirect_url = wp_remote_retrieve_header(wp_remote_get(\$url), 'location'); // actually wp_remote handles redirects internally and doesn't always expose final URL easily unless we look at the request object or just parse body
    }
    
    // A better way in WP to get final URL after redirect:
    // Actually wp_remote_get follows redirects and the resulting object might not have 'location' if it's the final page.
    // However, if we do wp_remote_get, we can get the final URL if WP supports it, or we can use curl if available.
    
    \$final_url = '';
    if (function_exists('curl_init')) {
        \$ch = curl_init(\$url);
        curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(\$ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt(\$ch, CURLOPT_HEADER, true);
        curl_setopt(\$ch, CURLOPT_NOBODY, true);
        \$res = curl_exec(\$ch);
        \$final_url = curl_getinfo(\$ch, CURLINFO_EFFECTIVE_URL);
        curl_close(\$ch);
    }
    
    if (empty(\$final_url)) \$final_url = \$url;
    
    // Extract lat/lng from final url
    preg_match('/@(-?\\d+\\.\\d+),(-?\\d+\\.\\d+)/', \$final_url, \$matches);
    if (\$matches) {
        wp_send_json_success(array('lat' => \$matches[1], 'lng' => \$matches[2], 'url' => \$final_url));
    } else {
        wp_send_json_error('Could not extract coordinates.');
    }
}
add_action('wp_ajax_cora_resolve_map_url', 'cora_ajax_resolve_map_url');
";

$content = str_replace("add_action('wp_ajax_cora_get_office_location', 'cora_ajax_get_office_location');", "add_action('wp_ajax_cora_get_office_location', 'cora_ajax_get_office_location');\n" . $ajax, $content);

file_put_contents($file, $content);
echo "Patched PHP";
