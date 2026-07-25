<?php
define('WP_USE_THEMES', false);
require_once('/Users/shrutian/Desktop/cora/app/public/wp-load.php');

echo "--- 1. Deleting duplicate test posts ---" . PHP_EOL;

$existing_posts = get_posts(array(
    'post_type'   => 'post',
    'numberposts' => -1,
    'post_status' => 'any'
));

foreach ($existing_posts as $p) {
    if ($p->ID !== 1) { // Preserve ID 1 if needed
        wp_delete_post($p->ID, true);
        echo "Deleted post ID: {$p->ID}" . PHP_EOL;
    }
}

echo "--- 2. Creating 10 distinct SEO test articles ---" . PHP_EOL;

$articles_data = array(
    array(
        'title'        => 'Commercial Lease Agreement Guide: Essential Terms for Real Estate Tenants in 2026',
        'focus_kw'     => 'commercial lease agreement',
        'meta_title'   => 'Commercial Lease Agreement: Essential Guide & Terms 2026',
        'meta_desc'    => 'Complete 2026 guide to commercial lease agreements. Learn essential tenant clauses, rent escalation formulas, security deposits, and negotiation strategies.',
        'slug'         => 'commercial-lease-agreement-guide',
        'word_target'  => 2150,
        'headers_cnt'  => 6,
        'images_cnt'   => 4,
        'kw_density'   => 1.6,
        'has_schema'   => true,
        'internal_lnks'=> 5
    ),
    array(
        'title'        => 'DLF CyberCity Commercial Space Rates: Pricing Analysis & Office Trends',
        'focus_kw'     => 'DLF CyberCity office rates',
        'meta_title'   => 'DLF CyberCity Office Rates & Commercial Space Guide 2026',
        'meta_desc'    => 'Detailed breakdown of commercial rental rates inside DLF CyberCity Gurgaon. Compare Phase 1, Phase 2, and Cyber Hub tech park leasing costs.',
        'slug'         => 'dlf-cybercity-commercial-space-rates',
        'word_target'  => 1640,
        'headers_cnt'  => 5,
        'images_cnt'   => 3,
        'kw_density'   => 1.5,
        'has_schema'   => true,
        'internal_lnks'=> 4
    ),
    array(
        'title'        => 'Co-Working vs Traditional Office Space: Cost Comparison for Growing Startups',
        'focus_kw'     => 'coworking vs traditional office',
        'meta_title'   => 'Co-Working vs Traditional Office: Startup Cost Guide 2026',
        'meta_desc'    => 'Compare co-working space flexibility with traditional commercial office leases. Calculate total cost per desk, deposit terms, and hidden amenities fee.',
        'slug'         => 'coworking-vs-traditional-office-space',
        'word_target'  => 1420,
        'headers_cnt'  => 4,
        'images_cnt'   => 2,
        'kw_density'   => 1.8,
        'has_schema'   => false,
        'internal_lnks'=> 3
    ),
    array(
        'title'        => 'Top 10 Commercial Real Estate Locations in Gurgaon for IT Companies',
        'focus_kw'     => 'Gurgaon commercial real estate',
        'meta_title'   => 'Top 10 Commercial Real Estate Hubs in Gurgaon for Tech Firms',
        'meta_desc'    => 'Explore top commercial hubs in Gurgaon including Golf Course Road, Sohna Road, and MG Road for tech companies.',
        'slug'         => 'top-10-gurgaon-commercial-locations',
        'word_target'  => 1180,
        'headers_cnt'  => 3,
        'images_cnt'   => 1,
        'kw_density'   => 0.7, // slightly low
        'has_schema'   => false,
        'internal_lnks'=> 2
    ),
    array(
        'title'        => 'Understanding Commercial Property Tax and CAM Charges in India',
        'focus_kw'     => 'CAM charges commercial lease',
        'meta_title'   => 'Commercial Property Tax & CAM Charges Explained',
        'meta_desc'    => 'What are Common Area Maintenance (CAM) charges in commercial property leases? Learn how landlords calculate utility overheads and property tax.',
        'slug'         => 'commercial-property-tax-cam-charges',
        'word_target'  => 920,
        'headers_cnt'  => 2,
        'images_cnt'   => 1,
        'kw_density'   => 1.2,
        'has_schema'   => false,
        'internal_lnks'=> 2
    ),
    array(
        'title'        => 'What is a Lock-In Period in Commercial Lease Agreements?',
        'focus_kw'     => 'lock in period commercial lease',
        'meta_title'   => 'Lock-In Period in Commercial Leases',
        'meta_desc'    => 'Quick guide to lock-in periods in commercial leases and tenant penalties for early exit.',
        'slug'         => 'lock-in-period-commercial-lease',
        'word_target'  => 650,
        'headers_cnt'  => 2,
        'images_cnt'   => 0,
        'kw_density'   => 2.8, // slightly high
        'has_schema'   => false,
        'internal_lnks'=> 1
    ),
    array(
        'title'        => 'Commercial Office Lease Gurgaon: Cheap Commercial Office Space Lease',
        'focus_kw'     => 'commercial office lease',
        'meta_title'   => 'Commercial Office Lease Gurgaon: Best Commercial Office Lease Rates',
        'meta_desc'    => 'Find cheap commercial office lease options. A commercial office lease in Gurgaon offers great commercial office lease terms.',
        'slug'         => 'cheap-commercial-office-lease-gurgaon',
        'word_target'  => 780,
        'headers_cnt'  => 3,
        'images_cnt'   => 2,
        'kw_density'   => 4.2, // extreme over-stuffed!
        'has_schema'   => false,
        'internal_lnks'=> 1
    ),
    array(
        'title'        => 'Office Rental Tips for New Businesses',
        'focus_kw'     => 'office rental tips',
        'meta_title'   => 'Office Rental Tips',
        'meta_desc'    => '',
        'slug'         => 'office-rental-tips-new-businesses',
        'word_target'  => 450,
        'headers_cnt'  => 0,
        'images_cnt'   => 0,
        'kw_density'   => 0.4,
        'has_schema'   => false,
        'internal_lnks'=> 0
    ),
    array(
        'title'        => 'Commercial Real Estate Trends Update',
        'focus_kw'     => 'real estate trends',
        'meta_title'   => '',
        'meta_desc'    => '',
        'slug'         => 'commercial-real-estate-trends-update',
        'word_target'  => 280,
        'headers_cnt'  => 0,
        'images_cnt'   => 0,
        'kw_density'   => 0.2,
        'has_schema'   => false,
        'internal_lnks'=> 0
    ),
    array(
        'title'        => 'How to Optimize Commercial Real Estate Websites for Core Web Vitals',
        'focus_kw'     => 'real estate core web vitals',
        'meta_title'   => 'Optimize Real Estate Websites for Core Web Vitals 2026',
        'meta_desc'    => 'Step-by-step guide to boosting LCP, FCP, and CLS performance for commercial real estate portals. Accelerate page loading speed and mobile UX.',
        'slug'         => 'optimize-real-estate-websites-core-web-vitals',
        'word_target'  => 1890,
        'headers_cnt'  => 6,
        'images_cnt'   => 3,
        'kw_density'   => 1.4,
        'has_schema'   => true,
        'internal_lnks'=> 5
    )
);

foreach ($articles_data as $idx => $item) {
    // Generate realistic HTML body text
    $body_html = "<h1>" . esc_html($item['title']) . "</h1>\n";
    $body_html .= "<p>Searching for the best <strong>" . esc_html($item['focus_kw']) . "</strong> strategies in 2026? In this comprehensive report, we analyze market benchmarks, tenant considerations, and financial structures.</p>\n";
    
    for ($h = 1; $h <= $item['headers_cnt']; $h++) {
        $body_html .= "<h2>Section {$h}: Key Insights on " . esc_html($item['focus_kw']) . "</h2>\n";
        $body_html .= "<p>Navigating commercial real estate contracts requires a deep understanding of lease duration, escalation percentage, and maintenance fee structures. Ensure your legal team reviews all liability clauses before execution.</p>\n";
        $body_html .= "<p>Furthermore, evaluating property amenities, high-speed fiber internet infrastructure, power backup capacity, and dedicated parking allocations guarantees long-term operational smooth sailing for your corporate workforce.</p>\n";
    }

    for ($img = 1; $img <= $item['images_cnt']; $img++) {
        $body_html .= "<p><img src='https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80' alt='" . esc_attr($item['focus_kw']) . " image {$img}' class='rounded-lg'></p>\n";
    }

    for ($link = 1; $link <= $item['internal_lnks']; $link++) {
        $body_html .= "<p>For further legal guidance on property terms, visit our <a href='/commercial-lease-guide'>commercial lease resource portal</a> or check authority <a href='https://gurgaon.gov.in' target='_blank' rel='noopener'>government real estate guidelines</a>.</p>\n";
    }

    if ($item['has_schema']) {
        $body_html .= '<script type="application/ld+json">{"@context":"https://schema.org","@type":"Article","headline":"' . esc_js($item['title']) . '"}</script>';
    }

    // Pad content to match word target
    $current_words = str_word_count(wp_strip_all_tags($body_html));
    if ($current_words < $item['word_target']) {
        $needed = $item['word_target'] - $current_words;
        $filler = " Commercial real estate investments require thorough financial analysis, lease audit verification, and tenant representation expertise to ensure long-term value creation across asset portfolios.";
        $padding_text = str_repeat($filler, ceil($needed / 20));
        $body_html .= "\n<p>" . $padding_text . "</p>";
    }

    $post_id = wp_insert_post(array(
        'post_title'   => $item['title'],
        'post_content' => $body_html,
        'post_name'    => $item['slug'],
        'post_status'  => 'publish',
        'post_type'    => 'post',
        'post_author'  => 1
    ));

    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_cora_focus_keyword', $item['focus_kw']);
        if ($item['meta_title']) update_post_meta($post_id, '_cora_seo_title', $item['meta_title']);
        if ($item['meta_desc']) update_post_meta($post_id, '_cora_meta_description', $item['meta_desc']);
        update_post_meta($post_id, '_cora_canonical_url', 'https://example.com/' . $item['slug']);

        echo "Created Article #{$post_id}: '{$item['title']}' (Target Words: {$item['word_target']})" . PHP_EOL;
    }
}

echo "--- 3. Done creating 10 distinct articles ---" . PHP_EOL;
