<?php
/**
 * Generate Admin-badged PWA icons for Cora Super Admin
 */

$base_dir = dirname(__DIR__) . '/app/public/wp-content/plugins/cora-workspace';
$src_512 = $base_dir . '/assets/pwa/icon_512.png';
$src_192 = $base_dir . '/assets/pwa/icon_192.png';
$src_apple = $base_dir . '/assets/images/apple-touch-icon.png';
$src_favicon = $base_dir . '/assets/images/cora-favicon.png';

if (!file_exists($src_512)) {
    die("Source icon not found: $src_512\n");
}

function add_admin_badge($src_path, $dest_path, $width, $height) {
    $img = imagecreatefrompng($src_path);
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $scale = $width / 512.0;

    // Badge Dimensions
    $badge_w = round(190 * $scale);
    $badge_h = round(56 * $scale);
    $badge_r = round(16 * $scale);
    
    // Position at bottom-right or top-right
    $badge_x = $width - $badge_w - round(24 * $scale);
    $badge_y = $height - $badge_h - round(28 * $scale);

    // Colors
    $black = imagecolorallocate($img, 9, 9, 11); // zinc-950
    $border_color = imagecolorallocate($img, 63, 63, 70); // zinc-700
    $white = imagecolorallocate($img, 255, 255, 255); // pure white
    $accent_dot = imagecolorallocate($img, 16, 185, 129); // emerald-500 active dot

    // Draw rounded rectangle for badge background
    $x1 = $badge_x;
    $y1 = $badge_y;
    $x2 = $badge_x + $badge_w;
    $y2 = $badge_y + $badge_h;

    // Shadow / border
    imagefilledrectangle($img, $x1 - 1, $y1 - 1, $x2 + 1, $y2 + 1, $border_color);
    imagefilledrectangle($img, $x1, $y1, $x2, $y2, $black);

    // Green indicator dot
    $dot_r = max(2, round(4 * $scale));
    $dot_x = $x1 + round(20 * $scale);
    $dot_y = $y1 + round($badge_h / 2);
    imagefilledellipse($img, $dot_x, $dot_y, $dot_r * 2, $dot_r * 2, $accent_dot);

    // Draw ADMIN Text
    $font_size = max(2, round(5 * $scale));
    $text = "ADMIN";
    
    // We can use imagestring or TTF if available
    $ttf_font = '/System/Library/Fonts/Supplemental/Arial Bold.ttf';
    if (!file_exists($ttf_font)) {
        $ttf_font = '/System/Library/Fonts/SFNSText.ttf';
    }
    if (!file_exists($ttf_font)) {
        $ttf_font = '/Library/Fonts/Arial.ttf';
    }

    if (file_exists($ttf_font) && function_exists('imagettftext')) {
        $pt_size = round(20 * $scale);
        $bbox = imagettfbbox($pt_size, 0, $ttf_font, $text);
        $text_w = abs($bbox[4] - $bbox[0]);
        $text_h = abs($bbox[5] - $bbox[1]);
        $text_x = $dot_x + round(14 * $scale);
        $text_y = $y1 + round(($badge_h + $text_h) / 2) - round(2 * $scale);
        imagettftext($img, $pt_size, 0, $text_x, $text_y, $white, $ttf_font, $text);
    } else {
        $font = 5; // Built-in font
        $text_x = $dot_x + round(10 * $scale);
        $text_y = $y1 + round(($badge_h - imagefontheight($font)) / 2);
        imagestring($img, $font, $text_x, $text_y, $text, $white);
    }

    imagepng($img, $dest_path, 9);
    imagedestroy($img);
    echo "Generated: $dest_path ($width x $height)\n";
}

add_admin_badge($src_512, $base_dir . '/assets/pwa/icon_512_admin.png', 512, 512);
add_admin_badge($src_192, $base_dir . '/assets/pwa/icon_192_admin.png', 192, 192);
add_admin_badge($src_apple, $base_dir . '/assets/images/apple-touch-icon-admin.png', 180, 180);
add_admin_badge($src_favicon, $base_dir . '/assets/images/cora-favicon-admin.png', 64, 64);
