<?php
/**
 * Cora Media Proofing & Secure File Delivery View
 *
 * Provides a secure, monochromatic, branded media proofing and download portal
 * with comprehensive view impressions, download tracking, and client interaction telemetry.
 *
 * @package Cora_Workspace
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Resolve Token & Attachment ID
$token = isset( $media_share_token ) && ! empty( $media_share_token ) ? sanitize_text_field( $media_share_token ) : '';
if ( empty( $token ) ) {
    $token = sanitize_text_field( $_GET['cora_share'] ?? $_GET['cora_media_share'] ?? '' );
}
if ( empty( $token ) && isset( $path_parts ) && is_array( $path_parts ) ) {
    $sm_idx = array_search( 'shared-media', $path_parts, true );
    if ( false === $sm_idx ) $sm_idx = array_search( 'share-media', $path_parts, true );
    if ( false === $sm_idx ) $sm_idx = array_search( 'shared-asset', $path_parts, true );
    if ( false !== $sm_idx && isset( $path_parts[ $sm_idx + 1 ] ) ) {
        $token = sanitize_text_field( $path_parts[ $sm_idx + 1 ] );
    }
}

$attachment_id = isset( $media_share_aid ) && $media_share_aid > 0 ? intval( $media_share_aid ) : 0;
if ( $attachment_id <= 0 ) {
    $attachment_id = intval( $_GET['aid'] ?? $_GET['attachment_id'] ?? 0 );
}

$attachment = null;
$found_link = null;
$is_expired = false;
$is_invalid = false;

global $wpdb;

// 2. Lookup Attachment Post
if ( $attachment_id > 0 ) {
    $post_candidate = get_post( $attachment_id );
    if ( $post_candidate && 'attachment' === $post_candidate->post_type ) {
        $attachment = $post_candidate;
    }
}

// Fallback search by token if attachment ID was omitted or not found
if ( ! $attachment && ! empty( $token ) ) {
    // Check single legacy meta key first
    $legacy_post_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_cora_media_share_token' AND meta_value = %s LIMIT 1",
        $token
    ) );
    if ( $legacy_post_id ) {
        $attachment = get_post( intval( $legacy_post_id ) );
        $attachment_id = $attachment ? $attachment->ID : 0;
    } else {
        // Query serialized / array meta
        $matching_post_ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_cora_media_share_links' AND meta_value LIKE %s LIMIT 20",
            '%' . $wpdb->esc_like( $token ) . '%'
        ) );
        if ( ! empty( $matching_post_ids ) ) {
            foreach ( $matching_post_ids as $mp_id ) {
                $p_links = get_post_meta( $mp_id, '_cora_media_share_links', true );
                if ( is_array( $p_links ) ) {
                    foreach ( $p_links as $pl ) {
                        if ( isset( $pl['token'] ) && hash_equals( (string)$pl['token'], (string)$token ) ) {
                            $attachment = get_post( intval( $mp_id ) );
                            $attachment_id = $attachment ? $attachment->ID : 0;
                            $found_link = $pl;
                            break 2;
                        }
                    }
                }
            }
        }
    }
}

// 3. Verify Share Token Validity & Expiry
if ( ! $attachment ) {
    $is_invalid = true;
} else {
    $attachment_id = $attachment->ID;
    $share_links = get_post_meta( $attachment_id, '_cora_media_share_links', true );
    
    if ( is_array( $share_links ) && ! empty( $share_links ) ) {
        foreach ( $share_links as $l ) {
            if ( isset( $l['token'] ) && hash_equals( (string)$l['token'], (string)$token ) ) {
                $found_link = $l;
                break;
            }
        }
    }

    if ( ! $found_link ) {
        // Check legacy single token meta
        $legacy_token = get_post_meta( $attachment_id, '_cora_media_share_token', true );
        if ( ! empty( $legacy_token ) && hash_equals( (string)$legacy_token, (string)$token ) ) {
            $legacy_exp = get_post_meta( $attachment_id, '_cora_media_share_expires', true );
            $found_link = array(
                'token'        => $legacy_token,
                'expires'      => intval( $legacy_exp ),
                'expiry_label' => $legacy_exp ? ( time() > intval( $legacy_exp ) ? 'Expired' : 'Expires ' . date( 'M j, Y', intval( $legacy_exp ) ) ) : 'No expiry',
                'created'      => strtotime( $attachment->post_date ),
            );
        }
    }

    if ( ! $found_link ) {
        $is_invalid = true;
    } else {
        if ( ! empty( $found_link['expires'] ) && intval( $found_link['expires'] ) > 0 && time() > intval( $found_link['expires'] ) ) {
            $is_expired = true;
        }
    }
}

// 4. Password Protection Verification
$required_password = '';
if ( $found_link && ! empty( $found_link['password'] ) ) {
    $required_password = (string) $found_link['password'];
} elseif ( $attachment ) {
    $required_password = (string) get_post_meta( $attachment_id, '_cora_media_share_password', true );
}

$password_verified = true;
$password_error = false;

if ( ! empty( $required_password ) ) {
    $session_key = 'cora_pass_ok_' . md5( $token . $attachment_id );
    if ( ! empty( $_COOKIE[ $session_key ] ) && hash_equals( md5( $required_password . wp_salt( 'auth' ) ), $_COOKIE[ $session_key ] ) ) {
        $password_verified = true;
    } else {
        $password_verified = false;
        if ( isset( $_POST['cora_share_password'] ) ) {
            $input_pass = sanitize_text_field( $_POST['cora_share_password'] );
            if ( hash_equals( $required_password, $input_pass ) ) {
                $password_verified = true;
                setcookie( $session_key, md5( $required_password . wp_salt( 'auth' ) ), time() + 86400 * 7, COOKIEPATH ?: '/', COOKIE_DOMAIN, is_ssl(), true );
            } else {
                $password_error = true;
            }
        }
    }
}

// 5. File Data & Direct Download Handling
$file_url = '';
$file_title = '';
$file_mime = '';
$file_caption = '';
$file_description = '';
$file_size_formatted = '0 KB';
$file_dimensions = '';
$file_ext = '';
$file_date_formatted = '';

if ( $attachment ) {
    $file_url = wp_get_attachment_url( $attachment_id );
    $file_title = $attachment->post_title ?: basename( (string)$file_url );
    $file_mime = $attachment->post_mime_type ?: 'application/octet-stream';
    $file_caption = $attachment->post_excerpt;
    $file_description = $attachment->post_content;
    $file_date_formatted = get_the_date( 'M j, Y', $attachment );

    $file_path = get_attached_file( $attachment_id );
    $file_size = 0;
    if ( $file_path && file_exists( $file_path ) ) {
        $file_size = filesize( $file_path );
    }

    if ( $file_size >= 1073741824 ) {
        $file_size_formatted = number_format( $file_size / 1073741824, 2 ) . ' GB';
    } elseif ( $file_size >= 1048576 ) {
        $file_size_formatted = number_format( $file_size / 1048576, 2 ) . ' MB';
    } elseif ( $file_size >= 1024 ) {
        $file_size_formatted = number_format( $file_size / 1024, 1 ) . ' KB';
    } elseif ( $file_size > 0 ) {
        $file_size_formatted = $file_size . ' B';
    }

    $file_ext = strtoupper( pathinfo( (string)$file_url, PATHINFO_EXTENSION ) ?: 'FILE' );

    $meta = wp_get_attachment_metadata( $attachment_id );
    if ( is_array( $meta ) && ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
        $file_dimensions = $meta['width'] . ' × ' . $meta['height'] . ' px';
    }

    // Direct Download Action Trigger
    if ( ! $is_invalid && ! $is_expired && $password_verified && isset( $_GET['download'] ) && in_array( (string)$_GET['download'], array( '1', 'true', 'yes' ), true ) ) {
        if ( $file_path && file_exists( $file_path ) ) {
            $clean_filename = basename( $file_path );

            // Record Download Telemetry Event
            if ( function_exists( 'cora_record_media_activity' ) ) {
                cora_record_media_activity( $attachment_id, 'download', array(
                    'token' => $token,
                    'via'   => 'Direct Download Stream',
                    'note'  => 'Saved ' . $clean_filename . ' (' . $file_size_formatted . ')',
                ) );
            }

            nocache_headers();
            header( 'Content-Description: File Transfer' );
            header( 'Content-Type: ' . $file_mime );
            header( 'Content-Disposition: attachment; filename="' . str_replace( '"', '', $clean_filename ) . '"' );
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Pragma: public' );
            header( 'Content-Length: ' . filesize( $file_path ) );
            readfile( $file_path );
            exit;
        }
    }

    // Record View Impression Telemetry (On valid view)
    if ( ! $is_invalid && ! $is_expired && $password_verified && ( ! isset( $_GET['download'] ) || ! in_array( (string)$_GET['download'], array( '1', 'true', 'yes' ), true ) ) ) {
        if ( function_exists( 'cora_record_media_activity' ) ) {
            cora_record_media_activity( $attachment_id, 'view', array(
                'token' => $token,
                'via'   => 'Secure Public Link',
            ) );
        }
    }
}

// SEO Details
$seo_title = $attachment ? ( get_post_meta( $attachment_id, '_cora_media_seo_title', true ) ?: $file_title ) : 'Secure Media Delivery';
$seo_desc = $attachment ? ( get_post_meta( $attachment_id, '_cora_media_seo_desc', true ) ?: $file_caption ?: 'Secure media delivery powered by Cora.' ) : 'Secure media delivery powered by Cora.';

$direct_download_url = add_query_arg( array(
    'cora_share' => $token,
    'aid'        => $attachment_id,
    'download'   => '1',
), home_url( '/' ) );

$share_full_url = add_query_arg( array(
    'cora_share' => $token,
    'aid'        => $attachment_id,
), home_url( '/' ) );
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo esc_html( $seo_title ); ?> &bull; Cora Media Proofing</title>
    <meta name="description" content="<?php echo esc_attr( $seo_desc ); ?>">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="<?php echo CORA_WORKSPACE_URL . 'assets/js/tailwind-cdn.min.js'; ?>"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        cream: '#FBFaf7',
                        'cream-subtle': '#F5F2EC',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        .cora-toast-container {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 99999;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }
        .cora-toast {
            pointer-events: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            background: #09090b;
            color: #ffffff;
            font-size: 12px;
            font-weight: 500;
            border-radius: 9999px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
            animation: coraToastIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .dark .cora-toast {
            background: #ffffff;
            color: #09090b;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        @keyframes coraToastIn {
            from { opacity: 0; transform: translateY(-12px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes coraToastOut {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-12px) scale(0.96); }
        }
        .image-viewport {
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            transform-origin: center center;
            cursor: zoom-in;
        }
        .image-viewport.zoomed {
            cursor: grab;
        }
        .image-viewport.zoomed:active {
            cursor: grabbing;
        }
        /* Fullscreen Lightbox */
        #cora-lightbox-modal {
            display: none;
        }
        #cora-lightbox-modal.active {
            display: flex;
        }
    </style>
</head>
<body class="bg-cream dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 min-h-screen flex flex-col justify-between selection:bg-zinc-200 dark:selection:bg-zinc-800 transition-colors duration-200">

    <!-- Monochromatic Toast Container -->
    <div id="cora-toast-root" class="cora-toast-container"></div>

    <!-- Header -->
    <header class="sticky top-0 z-40 w-full border-b border-zinc-200/80 dark:border-zinc-800/80 bg-cream/90 dark:bg-zinc-950/90 backdrop-blur-md">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between gap-4">
            <!-- Brand & Status -->
            <div class="flex items-center gap-3 min-w-0">
                <a href="<?php echo esc_url( home_url('/') ); ?>" class="flex items-center gap-2 shrink-0 group focus:outline-none">
                    <div class="w-7 h-7 rounded-lg bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 flex items-center justify-center font-bold text-xs shadow-sm">
                        C
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold tracking-tight text-zinc-900 dark:text-zinc-100 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors">
                            Cora Studio
                        </span>
                        <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono tracking-tight -mt-0.5">
                            Proofing & Delivery
                        </span>
                    </div>
                </a>

                <div class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 text-[11px] font-medium border border-zinc-200/60 dark:border-zinc-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Encrypted Share
                </div>
            </div>

            <!-- Top Actions -->
            <div class="flex items-center gap-2">
                <?php if ( ! $is_invalid && ! $is_expired && $password_verified && $attachment ) : ?>
                    <!-- Copy Link -->
                    <button type="button" onclick="coraCopyShareUrl()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-100 hover:bg-zinc-200/80 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 text-zinc-800 dark:text-zinc-200 text-xs font-semibold transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5 stroke-zinc-600 dark:stroke-zinc-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span class="hidden sm:inline">Copy Link</span>
                    </button>

                    <!-- Direct Download -->
                    <a href="<?php echo esc_url( $direct_download_url ); ?>" onclick="coraTrackDownloadClick()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-900 text-xs font-semibold transition-all shadow-sm active:scale-95 cursor-pointer">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Download</span>
                        <span class="text-[10px] opacity-75 font-mono hidden md:inline">(<?php echo esc_html( $file_size_formatted ); ?>)</span>
                    </a>
                <?php endif; ?>

                <!-- Theme Toggle -->
                <button type="button" onclick="coraToggleTheme()" class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 bg-transparent hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors" title="Toggle Theme">
                    <svg class="w-4 h-4 hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                    <svg class="w-4 h-4 block dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Stage -->
    <main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 py-8 md:py-12 flex flex-col justify-center">

        <?php if ( $is_invalid ) : ?>
            <!-- State 1: Invalid / Not Found Link -->
            <div class="max-w-md w-full mx-auto bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl p-8 text-center shadow-sm space-y-6">
                <div class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
                <div class="space-y-2">
                    <h1 class="text-base font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Shared Asset Unavailable</h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        This secure delivery link does not exist, has been revoked by the workspace administrator, or the URL contains an invalid token.
                    </p>
                </div>
                <a href="<?php echo esc_url( home_url('/') ); ?>" class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-900 text-xs font-semibold transition-all">
                    Return to Home
                </a>
            </div>

        <?php elseif ( $is_expired ) : ?>
            <!-- State 2: Expired Link -->
            <div class="max-w-md w-full mx-auto bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl p-8 text-center shadow-sm space-y-6">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto border border-amber-200/50 dark:border-amber-900/40">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="space-y-2">
                    <h1 class="text-base font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Secure Link Expired</h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        This media sharing link was configured with a strict expiration window and has now expired. Please contact the asset owner to generate a fresh sharing link.
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/50 dark:border-zinc-800/50 text-[11px] font-mono text-zinc-500 dark:text-zinc-400">
                    Token: <?php echo esc_html( substr( $token, 0, 12 ) . '...' ); ?>
                </div>
            </div>

        <?php elseif ( ! $password_verified ) : ?>
            <!-- State 3: Password Locked -->
            <div class="max-w-md w-full mx-auto bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl p-8 shadow-sm space-y-6">
                <div class="text-center space-y-2">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center mx-auto">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h1 class="text-base font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Password Protected Asset</h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Please enter the secure passphrase provided by the workspace owner to unlock and preview this media file.
                    </p>
                </div>

                <form method="POST" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Access Passcode</label>
                        <div class="relative">
                            <input type="password" id="cora-pass-input" name="cora_share_password" required autofocus class="w-full px-3.5 py-2.5 text-xs border border-zinc-200 dark:border-zinc-700 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:bg-white dark:focus:bg-zinc-800 font-mono tracking-widest" placeholder="••••••••" />
                            <button type="button" onclick="coraTogglePassVisibility()" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-1">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <?php if ( $password_error ) : ?>
                            <span class="text-[11px] font-medium text-rose-600 dark:text-rose-400 block mt-1">Invalid passcode. Please verify and try again.</span>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-900 font-semibold rounded-xl transition-all active:scale-[0.98] text-xs cursor-pointer shadow-sm">
                        Unlock &amp; View Media
                    </button>
                </form>
            </div>

        <?php else : ?>
            <!-- State 4: Verified Media Showcase -->
            <div class="space-y-6">

                <!-- Asset Stage Canvas -->
                <div class="relative bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl overflow-hidden shadow-sm flex flex-col">
                    
                    <!-- Media Viewer Box -->
                    <div class="relative w-full min-h-[360px] md:min-h-[520px] max-h-[75vh] bg-zinc-950 flex items-center justify-center overflow-hidden select-none group">

                        <?php if ( strpos( $file_mime, 'image/' ) === 0 ) : ?>
                            <!-- Image Viewer -->
                            <img id="cora-main-image" src="<?php echo esc_url( $file_url ); ?>" alt="<?php echo esc_attr( $file_title ); ?>" class="image-viewport max-w-full max-h-[75vh] object-contain" onclick="coraOpenLightbox()" />

                            <!-- Image Floating Zoom Controls -->
                            <div class="absolute bottom-4 right-4 z-20 flex items-center gap-1.5 p-1.5 rounded-xl bg-zinc-900/80 backdrop-blur-md border border-white/10 text-white shadow-lg opacity-90 hover:opacity-100 transition-opacity">
                                <button type="button" onclick="coraZoomImage(0.2)" class="p-1.5 hover:bg-white/10 rounded-lg text-zinc-200 hover:text-white transition-colors" title="Zoom In">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                                </button>
                                <button type="button" onclick="coraZoomImage(-0.2)" class="p-1.5 hover:bg-white/10 rounded-lg text-zinc-200 hover:text-white transition-colors" title="Zoom Out">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                                </button>
                                <button type="button" onclick="coraResetZoom()" class="px-2 py-1 hover:bg-white/10 rounded-lg text-[10px] font-mono text-zinc-300 hover:text-white transition-colors" title="Reset Zoom">
                                    1:1
                                </button>
                                <div class="w-px h-3 bg-white/20"></div>
                                <button type="button" onclick="coraOpenLightbox()" class="p-1.5 hover:bg-white/10 rounded-lg text-zinc-200 hover:text-white transition-colors" title="Fullscreen Lightbox">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                                </button>
                            </div>

                        <?php elseif ( strpos( $file_mime, 'video/' ) === 0 ) : ?>
                            <!-- Video Player -->
                            <video src="<?php echo esc_url( $file_url ); ?>" controls playsinline preload="metadata" class="w-full max-h-[75vh] object-contain bg-black"></video>

                        <?php elseif ( strpos( $file_mime, 'audio/' ) === 0 ) : ?>
                            <!-- Audio Player Card -->
                            <div class="w-full max-w-lg p-8 flex flex-col items-center gap-6 text-center">
                                <div class="w-20 h-20 rounded-3xl bg-zinc-900 border border-zinc-800 text-white flex items-center justify-center shadow-lg">
                                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                                </div>
                                <div class="space-y-1">
                                    <h2 class="text-sm font-bold text-white"><?php echo esc_html( $file_title ); ?></h2>
                                    <span class="text-xs text-zinc-400 font-mono"><?php echo esc_html( $file_size_formatted ); ?></span>
                                </div>
                                <audio src="<?php echo esc_url( $file_url ); ?>" controls class="w-full"></audio>
                            </div>

                        <?php elseif ( $file_mime === 'application/pdf' ) : ?>
                            <!-- PDF Viewer -->
                            <iframe src="<?php echo esc_url( $file_url ); ?>#toolbar=1" class="w-full h-[70vh] border-none bg-zinc-100"></iframe>

                        <?php elseif ( in_array( $file_ext, array( 'TXT', 'MD', 'JSON', 'JS', 'CSS', 'HTML', 'CSV', 'LOG', 'XML' ), true ) ) : ?>
                            <!-- Text / Code Viewer -->
                            <div class="w-full h-[60vh] p-4 sm:p-6 overflow-auto bg-zinc-950 font-mono text-xs text-zinc-200 text-left">
                                <pre class="whitespace-pre-wrap break-words leading-relaxed"><?php 
                                    $raw_content = file_exists( $file_path ) ? file_get_contents( $file_path, false, null, 0, 50000 ) : '';
                                    echo esc_html( $raw_content );
                                ?></pre>
                            </div>

                        <?php else : ?>
                            <!-- Other Generic Archive / RAW Files -->
                            <div class="w-full max-w-md p-10 flex flex-col items-center gap-5 text-center">
                                <div class="w-20 h-20 rounded-3xl bg-zinc-900 border border-zinc-800 text-zinc-400 flex items-center justify-center shadow-lg">
                                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                </div>
                                <div class="space-y-1">
                                    <span class="inline-block px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-300 text-[10px] font-mono uppercase tracking-wider font-bold">
                                        <?php echo esc_html( $file_ext ); ?> File
                                    </span>
                                    <h2 class="text-sm font-bold text-white"><?php echo esc_html( $file_title ); ?></h2>
                                    <p class="text-xs text-zinc-400">Click download to inspect or process this file locally.</p>
                                </div>
                                <a href="<?php echo esc_url( $direct_download_url ); ?>" onclick="coraTrackDownloadClick()" class="px-5 py-2.5 bg-white text-zinc-900 hover:bg-zinc-100 font-semibold rounded-xl text-xs flex items-center gap-2 transition-all shadow-sm active:scale-95">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    Download <?php echo esc_html( $file_ext ); ?> Asset (<?php echo esc_html( $file_size_formatted ); ?>)
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Metadata & Inspector Bar -->
                    <div class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border-t border-zinc-100 dark:border-zinc-800/80 space-y-4">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h1 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight truncate">
                                        <?php echo esc_html( $file_title ); ?>
                                    </h1>
                                    <span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px] font-mono uppercase font-bold shrink-0">
                                        <?php echo esc_html( $file_ext ); ?>
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 font-mono">
                                    Uploaded <?php echo esc_html( $file_date_formatted ); ?> &bull; <?php echo esc_html( $found_link['expiry_label'] ?? 'Active Link' ); ?>
                                </p>
                            </div>

                            <!-- Action Button Row -->
                            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                                <button type="button" onclick="coraCopyShareUrl()" class="px-3.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200/80 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 text-zinc-800 dark:text-zinc-200 text-xs font-semibold transition-all cursor-pointer flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 stroke-zinc-600 dark:stroke-zinc-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                    <span>Copy Link</span>
                                </button>
                                <a href="<?php echo esc_url( $direct_download_url ); ?>" onclick="coraTrackDownloadClick()" class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 dark:bg-white dark:hover:bg-zinc-100 text-white dark:text-zinc-900 text-xs font-semibold transition-all shadow-sm active:scale-95 flex items-center gap-2 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    <span>Download Asset</span>
                                </a>
                            </div>
                        </div>

                        <!-- Technical Specs Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800/60 text-xs">
                            <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800/50">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-zinc-400 dark:text-zinc-500 block">File Size</span>
                                <span class="text-xs font-mono font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5 block"><?php echo esc_html( $file_size_formatted ); ?></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800/50">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-zinc-400 dark:text-zinc-500 block">Dimensions</span>
                                <span class="text-xs font-mono font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5 block"><?php echo esc_html( ! empty( $file_dimensions ) ? $file_dimensions : 'Vector / N/A' ); ?></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800/50">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-zinc-400 dark:text-zinc-500 block">MIME Type</span>
                                <span class="text-xs font-mono font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5 block truncate" title="<?php echo esc_attr( $file_mime ); ?>"><?php echo esc_html( $file_mime ); ?></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800/50">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-zinc-400 dark:text-zinc-500 block">Security</span>
                                <span class="text-xs font-mono font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5 block">256-bit Token</span>
                            </div>
                        </div>

                        <?php if ( ! empty( $file_caption ) || ! empty( $file_description ) ) : ?>
                            <div class="pt-2 text-xs text-zinc-600 dark:text-zinc-300 space-y-1 bg-zinc-50/60 dark:bg-zinc-800/20 p-3 rounded-xl border border-zinc-100 dark:border-zinc-800/50">
                                <?php if ( ! empty( $file_caption ) ) : ?>
                                    <p class="font-medium text-zinc-800 dark:text-zinc-200">"<?php echo esc_html( $file_caption ); ?>"</p>
                                <?php endif; ?>
                                <?php if ( ! empty( $file_description ) ) : ?>
                                    <p class="text-zinc-500 dark:text-zinc-400 leading-relaxed"><?php echo nl2br( esc_html( $file_description ) ); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        <?php endif; ?>

    </main>

    <!-- Fullscreen Lightbox Modal (For Images) -->
    <?php if ( ! $is_invalid && ! $is_expired && $password_verified && strpos( $file_mime, 'image/' ) === 0 ) : ?>
        <div id="cora-lightbox-modal" class="fixed inset-0 z-50 bg-black/95 backdrop-blur-md items-center justify-center p-4">
            <button type="button" onclick="coraCloseLightbox()" class="absolute top-5 right-5 z-50 p-2.5 rounded-full bg-zinc-900/80 hover:bg-zinc-800 text-white transition-all cursor-pointer" title="Close Lightbox (Esc)">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="relative max-w-full max-h-full flex items-center justify-center">
                <img id="cora-lightbox-img" src="<?php echo esc_url( $file_url ); ?>" alt="<?php echo esc_attr( $file_title ); ?>" class="max-w-[95vw] max-h-[92vh] object-contain select-none" />
            </div>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="w-full border-t border-zinc-200/80 dark:border-zinc-800/80 bg-cream dark:bg-zinc-950 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-zinc-400 dark:text-zinc-500 font-medium">
            <div class="flex items-center gap-2">
                <span>&copy; <?php echo date('Y'); ?> Cora Studio Platform</span>
                <span>&bull;</span>
                <span>Secure Asset Delivery</span>
            </div>
            <div class="flex items-center gap-1.5 text-[10px] font-mono">
                <span>Proofing Engine v<?php echo esc_html( defined('CORA_WORKSPACE_VERSION') ? CORA_WORKSPACE_VERSION : '4.9.188' ); ?></span>
            </div>
        </div>
    </footer>

    <!-- Interactive Client Scripts & Telemetry Beacon -->
    <script>
        // Media Telemetry Beacon Logger
        function coraTrackEvent(eventType, note) {
            const aid = <?php echo intval( $attachment_id ); ?>;
            const token = <?php echo json_encode( $token ); ?>;
            const ajaxUrl = <?php echo json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
            if (!aid) return;

            const payload = new URLSearchParams();
            payload.append('action', 'cora_media_track_event');
            payload.append('attachment_id', aid);
            payload.append('token', token);
            payload.append('event_type', eventType);
            if (note) payload.append('note', note);

            if (navigator.sendBeacon) {
                navigator.sendBeacon(ajaxUrl, payload);
            } else {
                fetch(ajaxUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: payload.toString(),
                    keepalive: true
                }).catch(() => {});
            }
        }

        // Monochromatic Toast System
        window.coraShowToast = function(message) {
            const root = document.getElementById('cora-toast-root');
            if (!root) return;
            const toast = document.createElement('div');
            toast.className = 'cora-toast';
            toast.innerHTML = `
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>${message}</span>
            `;
            root.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'coraToastOut 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards';
                setTimeout(() => toast.remove(), 250);
            }, 3000);
        };

        // Copy Share Link
        function coraCopyShareUrl() {
            const shareUrl = <?php echo json_encode( $share_full_url ); ?>;
            coraTrackEvent('copy_link', 'Link copied to clipboard');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(shareUrl).then(() => {
                    window.coraShowToast('Secure link copied to clipboard.');
                }).catch(() => {
                    fallbackCopy(shareUrl);
                });
            } else {
                fallbackCopy(shareUrl);
            }
        }

        function fallbackCopy(text) {
            const input = document.createElement('input');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            window.coraShowToast('Secure link copied to clipboard.');
        }

        function coraTrackDownloadClick() {
            coraTrackEvent('download', 'Download triggered from view stage');
        }

        // Image Zoom State
        let currentScale = 1;
        function coraZoomImage(delta) {
            const img = document.getElementById('cora-main-image');
            if (!img) return;
            currentScale = Math.min(Math.max(0.5, currentScale + delta), 3.0);
            img.style.transform = `scale(${currentScale})`;
            if (currentScale > 1) {
                img.classList.add('zoomed');
                coraTrackEvent('zoom', 'Zoomed image to ' + Math.round(currentScale * 100) + '%');
            } else {
                img.classList.remove('zoomed');
            }
        }

        function coraResetZoom() {
            const img = document.getElementById('cora-main-image');
            if (!img) return;
            currentScale = 1;
            img.style.transform = 'scale(1)';
            img.classList.remove('zoomed');
        }

        // Lightbox
        function coraOpenLightbox() {
            const modal = document.getElementById('cora-lightbox-modal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                coraTrackEvent('lightbox', 'Opened fullscreen lightbox');
            }
        }

        function coraCloseLightbox() {
            const modal = document.getElementById('cora-lightbox-modal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                coraCloseLightbox();
            }
        });

        // Passcode Input Toggle
        function coraTogglePassVisibility() {
            const pass = document.getElementById('cora-pass-input');
            if (!pass) return;
            pass.type = pass.type === 'password' ? 'text' : 'password';
        }

        // Theme Toggle & Persistence
        function coraToggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('cora_theme', isDark ? 'dark' : 'light');
        }

        // Hydrate Theme
        (function() {
            const saved = localStorage.getItem('cora_theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</body>
</html>
