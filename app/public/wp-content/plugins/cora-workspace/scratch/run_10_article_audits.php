<?php
define('WP_USE_THEMES', false);
require_once('/Users/shrutian/Desktop/cora/app/public/wp-load.php');
wp_set_current_user(1);

$posts = get_posts(array('post_type' => 'post', 'numberposts' => 15, 'orderby' => 'ID', 'order' => 'ASC'));
echo '--- AUDIT RESULTS FOR 10 DISTINCT ARTICLES ---' . PHP_EOL;

foreach ($posts as $p) {
    if ($p->ID === 1) continue;

    $kw      = get_post_meta($p->ID, '_cora_focus_keyword', true);
    $m_title = get_post_meta($p->ID, '_cora_seo_title', true);
    $m_desc  = get_post_meta($p->ID, '_cora_meta_description', true);
    
    $content       = $p->post_content;
    $post_title    = $p->post_title;
    $clean_content = wp_strip_all_tags($content);
    $word_count    = str_word_count($clean_content);
    $kw_lower      = mb_strtolower(trim($kw));

    $passed_1 = ($word_count >= 1000);
    $passed_2 = ($kw_lower !== '' && (mb_stripos($m_title, $kw_lower) !== false || mb_stripos($post_title, $kw_lower) !== false));
    $passed_3 = ($kw_lower !== '' && mb_stripos($post_title, $kw_lower) !== false);
    $passed_4 = (preg_match('/<h[23][^>]*>/i', $content) === 1);
    $words_array = preg_split('/\s+/', $clean_content, 151);
    $first_150 = implode(' ', array_slice($words_array, 0, 150));
    $passed_5 = ($kw_lower !== '' && mb_stripos($first_150, $kw_lower) !== false);

    $kw_density = 0.0;
    if ($word_count > 0 && $kw_lower !== '') {
        $kw_occurrences = mb_substr_count(mb_strtolower($clean_content), $kw_lower);
        $kw_word_len    = max(1, count(preg_split('/\s+/', $kw_lower)));
        $kw_density = round((($kw_occurrences * $kw_word_len) / $word_count) * 100, 2);
    }
    $passed_6 = ($kw_density >= 0.8 && $kw_density <= 2.5);

    $m_title_len = mb_strlen(trim($m_title));
    $passed_7 = ($m_title_len >= 45 && $m_title_len <= 65);

    $m_desc_len = mb_strlen(trim($m_desc));
    $passed_8 = ($m_desc_len >= 120 && $m_desc_len <= 165);

    $sanitized_kw = sanitize_title($kw);
    $passed_9 = (strpos($p->post_name, '-') !== false && !empty($sanitized_kw) && strpos($p->post_name, $sanitized_kw) !== false);

    $passed_10 = (preg_match('/<a\s+[^>]*href=["\'][^"\']+["\'][^>]*>/i', $content) === 1);
    $passed_11 = (preg_match('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>/i', $content) === 1);

    $passed_count = ($passed_1?1:0) + ($passed_2?1:0) + ($passed_3?1:0) + ($passed_4?1:0) + ($passed_5?1:0) + ($passed_6?1:0) + ($passed_7?1:0) + ($passed_8?1:0) + ($passed_9?1:0) + ($passed_10?1:0) + ($passed_11?1:0);
    $seo_score = intval(round(($passed_count / 11) * 100));

    $geo_score = 0;
    if ($passed_1)  $geo_score += 20;
    if ($passed_4)  $geo_score += 20;
    if ($passed_11) $geo_score += 20;
    if ($passed_5)  $geo_score += 15;
    if ($passed_8)  $geo_score += 15;
    if ($passed_10) $geo_score += 10;

    update_post_meta($p->ID, '_cora_seo_score', $seo_score);
    update_post_meta($p->ID, '_cora_geo_score', $geo_score);

    echo sprintf("ID: %d | Title: %-45s | Words: %4d | Score: %3d/100 | GEO: %3d%% | Density: %4.1f%% | Passed: %2d/11",
        $p->ID, substr($p->post_title, 0, 42) . '...', $word_count, $seo_score, $geo_score, $kw_density, $passed_count
    ) . PHP_EOL;
}
