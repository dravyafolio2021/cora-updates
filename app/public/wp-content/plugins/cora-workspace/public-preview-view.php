<?php
/**
 * Public Shareable Article Preview Template
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$preview_post_id = isset( $preview_post_id ) ? intval( $preview_post_id ) : 0;
$post = isset( $preview_post ) ? $preview_post : get_post( $preview_post_id );

if ( ! $post || 'post' !== $post->post_type ) {
    wp_die( __( 'Invalid or unavailable article preview link.', 'cora-workspace' ), __( 'Access Denied', 'cora-workspace' ), array( 'response' => 404 ) );
}

// 1. Resolve Workspace Context Name
$active_ws = function_exists( 'cora_get_current_workspace_context' ) ? cora_get_current_workspace_context() : null;
$ws_name = ! empty( $active_ws['name'] ) ? $active_ws['name'] : 'Cora Workspace';

// Fallback: Check if author belongs to an agency
$author_id = $post->post_author;
$author_agency_id = get_user_meta( $author_id, 'cora_agency_id', true );
if ( ! empty( $author_agency_id ) ) {
    if ( function_exists( 'cora_get_workspace_by_slug' ) ) {
        $ws = cora_get_workspace_by_slug( $author_agency_id );
        if ( ! empty( $ws['name'] ) ) {
            $ws_name = $ws['name'];
        }
    }
}

// 2. Resolve Author / Assignee Name and Initials
$assignee_id = intval( get_post_meta( $post->ID, '_cora_assignee_id', true ) );
$author_name = 'Cora Editor';
if ( $assignee_id > 0 ) {
    $assignee = get_userdata( $assignee_id );
    if ( $assignee ) {
        $author_name = $assignee->display_name;
    }
}

function cora_preview_initials( $name ) {
    $parts = explode( ' ', trim( $name ) );
    $parts = array_filter( $parts );
    if ( count( $parts ) >= 2 ) {
        return strtoupper( mb_substr( $parts[0], 0, 1 ) . mb_substr( end( $parts ), 0, 1 ) );
    } elseif ( count( $parts ) === 1 ) {
        return strtoupper( mb_substr( $parts[0], 0, 2 ) );
    }
    return 'CE';
}
$author_initials = cora_preview_initials( $author_name );

// 3. Cover image and metadata calculations
$cover_url = get_the_post_thumbnail_url( $post->ID, 'full' );
$word_count = str_word_count( strip_tags( $post->post_content ) );
$read_time = ceil( $word_count / 200 );
if ( $read_time < 1 ) {
    $read_time = 1;
}
$favicon_url = CORA_WORKSPACE_URL . 'assets/images/cora-favicon.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Preview: <?php echo esc_html( $post->post_title ); ?></title>
    <link rel="icon" type="image/png" href="<?php echo esc_url( $favicon_url ); ?>" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;700&display=swap');
        
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
            color: #18181b;
            background-color: #faf9f6; /* Elegant warm cream background */
            margin: 0;
            padding: 0;
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
        }

        /* Float helper toast */
        .preview-helper-toast {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #18181b;
            color: #ffffff;
            font-size: 11px;
            font-weight: 500;
            padding: 8px 16px;
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 9999px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 100;
            gap: 12px;
            font-family: 'Inter', sans-serif;
            letter-spacing: -0.01em;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .preview-helper-toast svg {
            stroke: #10b981;
        }
        .preview-helper-toast .close-toast {
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.15s;
        }
        .preview-helper-toast .close-toast:hover {
            opacity: 1;
        }
        
        /* Branded top navigation bar */
        .preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 24px;
            background: #ffffff;
            border-bottom: 1px solid #e4e4e7;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0,0,0,0.01);
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-logo {
            color: #09090b;
        }
        .workspace-name {
            font-size: 12px;
            font-weight: 700;
            color: #18181b;
            letter-spacing: -0.01em;
            text-transform: uppercase;
        }
        .status-indicator {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            display: inline-block;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .badge {
            font-size: 9px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-family: 'Inter', sans-serif;
        }
        .preview-badge {
            background: #18181b;
            color: #ffffff;
        }
        .draft-badge {
            background: #f4f4f5;
            color: #71717a;
            border: 1px solid #e4e4e7;
        }
        
        /* Centered Content Layout - Full Bleed Design (No Card Frame Boxes!) */
        .preview-container {
            max-width: 680px;
            margin: 0 auto;
            padding: 64px 24px;
        }
        
        /* Cover Image */
        .cover-image-wrap {
            width: 100%;
            height: 380px;
            overflow: hidden;
            border-radius: 16px;
            margin-bottom: 40px;
            border: 1px solid #e4e4e7;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .cover-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Article Title */
        h1.article-title {
            font-size: 2.85rem;
            font-weight: 800;
            color: #09090b;
            line-height: 1.15;
            margin-top: 0;
            margin-bottom: 24px;
            letter-spacing: -0.035em;
        }
        
        /* Meta Info Bar */
        .article-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 48px;
            border-bottom: 1px solid #e4e4e7;
            padding-bottom: 24px;
        }
        .author-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #27272a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: -0.01em;
            font-family: 'Inter', sans-serif;
        }
        .meta-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .author-name {
            font-size: 13px;
            font-weight: 600;
            color: #18181b;
        }
        .meta-sub {
            font-size: 11px;
            color: #71717a;
        }
        
        /* Article content typesetting */
        .content {
            font-family: 'Lora', Georgia, Charter, serif; /* Editorial Serif Typography */
            font-size: 1.15rem;
            color: #27272a;
            line-height: 1.85;
        }
        .content h2 {
            font-family: 'Inter', sans-serif;
            font-size: 1.75rem;
            font-weight: 850;
            color: #09090b;
            margin-top: 48px;
            margin-bottom: 18px;
            letter-spacing: -0.02em;
            line-height: 1.3;
        }
        .content h3 {
            font-family: 'Inter', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: #09090b;
            margin-top: 36px;
            margin-bottom: 14px;
            letter-spacing: -0.01em;
        }
        .content p {
            margin-top: 0;
            margin-bottom: 26px;
        }
        
        /* Table formats */
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 32px 0;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
        }
        .content th, .content td {
            border: 1px solid #e4e4e7;
            padding: 12px 16px;
            text-align: left;
        }
        .content th {
            background: #fafafa;
            font-weight: 700;
            color: #09090b;
        }
        .content tr:hover {
            background: #fafafa;
        }
        
        /* Custom interactive blocks styling overrides */
        .cora-inline-cta-card, .cora-equipment-showcase-card, .cora-related-article-card, .cora-listing-card, .cora-equipment-card, .cora-gallery-showcase, .cora-pullquote-block, .cora-editorial-signature {
            font-family: 'Inter', sans-serif !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.015) !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 14px !important;
            transition: all 0.25s ease;
        }
        .cora-inline-cta-card:hover, .cora-related-article-card:hover, .cora-listing-card:hover, .cora-equipment-card:hover {
            transform: translateY(-1.5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.03) !important;
            border-color: #d4d4d8 !important;
        }
        
        /* Signature card specific fixes */
        .cora-editorial-signature p {
            margin-bottom: 0 !important;
        }
        
        /* Footer branding */
        .preview-footer {
            text-align: center;
            margin-top: 80px;
            padding-top: 24px;
            border-top: 1px solid #e4e4e7;
            font-size: 11px;
            color: #a1a1aa;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <!-- Floating link info helper -->
    <div class="preview-helper-toast" id="cora-preview-toast">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        <span>Copy the browser URL to share this draft preview with anyone.</span>
        <span class="close-toast" onclick="document.getElementById('cora-preview-toast').remove()">&times;</span>
    </div>

    <header class="preview-header">
        <div class="header-left">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.2" fill="none" class="brand-logo"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
            <span class="workspace-name"><?php echo esc_html( $ws_name ); ?></span>
            <span class="status-indicator"></span>
        </div>
        <div class="header-right">
            <span class="badge preview-badge">PREVIEW MODE</span>
            <span class="badge draft-badge">Draft Preview</span>
        </div>
    </header>

    <div class="preview-container">
        <?php if ( ! empty( $cover_url ) ) : ?>
            <div class="cover-image-wrap">
                <img src="<?php echo esc_url( $cover_url ); ?>" alt="Cover Image">
            </div>
        <?php endif; ?>
        
        <h1 class="article-title"><?php echo esc_html( $post->post_title ); ?></h1>
        
        <div class="article-meta">
            <div class="author-avatar"><?php echo esc_html( $author_initials ); ?></div>
            <div class="meta-details">
                <span class="author-name">By <?php echo esc_html( $author_name ); ?></span>
                <span class="meta-sub">
                    Generated on <?php echo get_the_date( 'd M Y, h:i A', $post->ID ); ?>
                    &middot; 
                    <?php echo esc_html( $read_time . ' min read' ); ?>
                </span>
            </div>
        </div>
        
        <div class="content">
            <?php echo wp_kses_post( $post->post_content ); ?>
        </div>
        
        <footer class="preview-footer">
            Generated via Cora Workspace Platform &middot; Secure Draft Preview
        </footer>
    </div>
</body>
</html>
