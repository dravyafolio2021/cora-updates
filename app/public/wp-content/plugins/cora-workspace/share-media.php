<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$token = isset( $path_parts[1] ) ? sanitize_text_field( $path_parts[1] ) : '';
if ( empty( $token ) ) {
    wp_die( 'Access Denied: Sharing token is missing.', 'Access Denied', array( 'response' => 403 ) );
}

// Find attachment by token
$attachments = get_posts(array(
    'post_type'      => 'attachment',
    'post_status'    => 'inherit',
    'posts_per_page' => 1,
    'meta_query'     => array(
        array(
            'key'     => '_cora_media_share_token',
            'value'   => $token,
            'compare' => '='
        )
    )
));

if ( empty( $attachments ) ) {
    wp_die( 'Access Denied: Invalid sharing link.', 'Access Denied', array( 'response' => 403 ) );
}

$attachment = $attachments[0];
$attachment_id = $attachment->ID;

// Verify public status
$share_public = get_post_meta( $attachment_id, '_cora_media_share_public', true );
if ( ! $share_public ) {
    wp_die( 'Access Denied: This file is no longer shared publicly.', 'Access Denied', array( 'response' => 403 ) );
}

// Verify expiry
$share_expires = get_post_meta( $attachment_id, '_cora_media_share_expires', true );
if ( ! empty( $share_expires ) && intval( $share_expires ) > 0 ) {
    if ( time() > intval( $share_expires ) ) {
        wp_die( 'Access Denied: This secure sharing link has expired.', 'Access Denied', array( 'response' => 403 ) );
    }
}

// Verify password
$share_password = get_post_meta( $attachment_id, '_cora_media_share_password', true );
$password_verified = true;
$password_error = false;

if ( ! empty( $share_password ) ) {
    $password_verified = false;
    
    // Check submission
    if ( isset( $_POST['cora_share_password'] ) ) {
        if ( $_POST['cora_share_password'] === $share_password ) {
            $password_verified = true;
        } else {
            $password_error = true;
        }
    }
}

$file_url = wp_get_attachment_url( $attachment_id );
$file_title = $attachment->post_title;
$file_mime = $attachment->post_mime_type;
$file_caption = $attachment->post_excerpt;
$file_description = $attachment->post_content;

// Retrieve size
$file_path = get_attached_file( $attachment_id );
$file_size = 0;
if ( $file_path && file_exists( $file_path ) ) {
    $file_size = filesize( $file_path );
}

function cora_format_size( $bytes ) {
    if ( $bytes >= 1073741824 ) {
        return number_format( $bytes / 1073741824, 2 ) . ' GB';
    } elseif ( $bytes >= 1048576 ) {
        return number_format( $bytes / 1048576, 2 ) . ' MB';
    } elseif ( $bytes >= 1024 ) {
        return number_format( $bytes / 1024, 1 ) . ' KB';
    } elseif ( $bytes > 1 ) {
        return $bytes . ' bytes';
    } elseif ( $bytes == 1 ) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}

$size_display = cora_format_size( $file_size );

// SEO Details
$seo_title = get_post_meta( $attachment_id, '_cora_media_seo_title', true ) ?: $file_title;
$seo_desc = get_post_meta( $attachment_id, '_cora_media_seo_desc', true ) ?: $file_caption;
$seo_keywords = get_post_meta( $attachment_id, '_cora_media_seo_keywords', true );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($seo_title); ?> - Secure Share | Cora</title>
    <?php if ( ! empty($seo_desc) ) : ?>
    <meta name="description" content="<?php echo esc_attr($seo_desc); ?>">
    <?php endif; ?>
    <?php if ( ! empty($seo_keywords) ) : ?>
    <meta name="keywords" content="<?php echo esc_attr($seo_keywords); ?>">
    <?php endif; ?>
    <script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/tailwind-cdn.min.js'; ?>"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-neutral-50 text-neutral-900 min-h-screen flex flex-col justify-between">
    <!-- Header -->
    <header class="border-b border-neutral-200/80 bg-white/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" class="text-neutral-950">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
                <span class="text-xs font-bold uppercase tracking-wider text-neutral-950">Cora Secure File Share</span>
            </div>
            <div class="flex items-center gap-1.5 text-[10px] font-semibold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200/60 uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Encrypted Link
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-2xl w-full mx-auto px-4 py-12 flex flex-col justify-center">
        <?php if ( ! $password_verified ) : ?>
            <!-- Password Form -->
            <div class="bg-white border border-neutral-200/80 rounded-2xl p-6 shadow-sm space-y-6">
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-neutral-100 flex items-center justify-center mx-auto text-neutral-600">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-neutral-950">This file is password protected</h2>
                    <p class="text-xs text-neutral-500">Please enter the access password provided by the owner to view or download this file.</p>
                </div>

                <form method="POST" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Access Password</label>
                        <input type="password" name="cora_share_password" required autofocus class="w-full px-3 py-2 text-xs border border-neutral-200 rounded-md bg-white text-neutral-950 focus:outline-none focus:border-neutral-450" placeholder="••••••••" />
                        <?php if ( $password_error ) : ?>
                            <span class="text-[10px] font-semibold text-red-650 block mt-1">Invalid password. Please try again.</span>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="w-full py-2 bg-neutral-950 text-white font-semibold rounded-md hover:bg-neutral-800 transition-all active:scale-[0.98] cursor-pointer text-xs">
                        Unlock File
                    </button>
                </form>
            </div>
        <?php else : ?>
            <!-- File Display -->
            <div class="bg-white border border-neutral-200/80 rounded-2xl p-6 shadow-sm space-y-6">
                <!-- Preview Canvas -->
                <div class="w-full h-80 rounded-xl border border-neutral-200/60 bg-neutral-50 overflow-hidden flex items-center justify-center relative">
                    <?php if ( strpos( $file_mime, 'image/' ) === 0 ) : ?>
                        <img src="<?php echo esc_url($file_url); ?>" alt="<?php echo esc_attr($file_title); ?>" class="w-full h-full object-contain" />
                    <?php elseif ( strpos( $file_mime, 'video/' ) === 0 ) : ?>
                        <video src="<?php echo esc_url($file_url); ?>" controls class="w-full h-full object-contain"></video>
                    <?php elseif ( strpos( $file_mime, 'audio/' ) === 0 ) : ?>
                        <audio src="<?php echo esc_url($file_url); ?>" controls class="w-2/3"></audio>
                    <?php elseif ( $file_mime === 'application/pdf' ) : ?>
                        <iframe src="<?php echo esc_url($file_url); ?>" class="w-full h-full border-none"></iframe>
                    <?php else : ?>
                        <div class="flex flex-col items-center gap-3">
                            <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1.2" fill="none" class="text-neutral-400">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            <span class="text-xs font-semibold text-neutral-400 font-mono"><?php echo esc_html(strtoupper(substr(strrchr($file_url, '.'), 1))); ?> File</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Info Block -->
                <div class="space-y-4">
                    <div class="border-b border-neutral-100 pb-3 flex items-start justify-between gap-3">
                        <div>
                            <h1 class="text-base font-bold text-neutral-950 leading-snug"><?php echo esc_html($file_title); ?></h1>
                            <span class="text-[10px] text-neutral-450 mt-1 block font-mono">Mime: <?php echo esc_html($file_mime); ?> • Size: <?php echo esc_html($size_display); ?></span>
                        </div>
                        <a href="<?php echo esc_url($file_url); ?>" download class="px-4 py-2 bg-neutral-950 text-white font-semibold rounded-md hover:bg-neutral-800 transition-all active:scale-[0.98] text-xs shrink-0 flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Download
                        </a>
                    </div>

                    <?php if ( ! empty($file_caption) || ! empty($file_description) ) : ?>
                    <div class="text-xs text-neutral-600 space-y-2">
                        <?php if ( ! empty($file_caption) ) : ?>
                            <p class="italic">"<?php echo esc_html($file_caption); ?>"</p>
                        <?php endif; ?>
                        <?php if ( ! empty($file_description) ) : ?>
                            <p class="leading-relaxed"><?php echo nl2br(esc_html($file_description)); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="border-t border-neutral-200/60 py-6 bg-white">
        <div class="max-w-4xl mx-auto px-4 text-center text-[10px] text-neutral-400 font-medium">
            &copy; <?php echo date('Y'); ?> Cora Platform. Powered by Google Advanced Agentic Coding.
        </div>
    </footer>
</body>
</html>
