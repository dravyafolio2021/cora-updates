<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$portfolio_id = $found_portfolio['id'];
$cookie_name = 'cora_portfolio_auth_' . md5( $portfolio_id );
$authenticated = false;
$password_error = false;

if ( empty( $found_portfolio['password'] ) ) {
    $authenticated = true;
} else {
    if ( isset( $_POST['portfolio_password'] ) ) {
        if ( $_POST['portfolio_password'] === $found_portfolio['password'] ) {
            setcookie( $cookie_name, md5( $found_portfolio['password'] ), time() + 86400 * 7, '/' );
            $_COOKIE[$cookie_name] = md5( $found_portfolio['password'] ); // Force mock load
            $authenticated = true;
        } else {
            $password_error = true;
        }
    } elseif ( isset( $_COOKIE[$cookie_name] ) && $_COOKIE[$cookie_name] === md5( $found_portfolio['password'] ) ) {
        $authenticated = true;
    }
}

$assets = isset( $found_portfolio['assets'] ) ? $found_portfolio['assets'] : array();
$share_images = !isset( $found_portfolio['share_images'] ) || $found_portfolio['share_images'] !== false;
$share_videos = !isset( $found_portfolio['share_videos'] ) || $found_portfolio['share_videos'] !== false;

// Filter assets based on sharing preferences
$filtered_assets = array();
foreach ( $assets as $asset ) {
    if ( $asset['type'] === 'video' && !$share_videos ) {
        continue;
    }
    if ( $asset['type'] === 'image' && !$share_images ) {
        continue;
    }
    $filtered_assets[] = $asset;
}
$assets = $filtered_assets;

$likes = isset( $found_portfolio['likes'] ) ? $found_portfolio['likes'] : array();
$template = isset( $found_portfolio['template'] ) ? $found_portfolio['template'] : 'grid';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php
    $favicon_url = get_option( 'cora_brand_favicon_url', '' );
    if ( empty( $favicon_url ) ) {
        $favicon_url = CORA_REAL_ESTATE_AI_URL . 'assets/images/cora-favicon.png';
    }
    ?>
    <link rel="icon" type="image/png" href="<?php echo esc_url( $favicon_url ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $found_portfolio['title'] ); ?> • Property Portfolio</title>
    <!-- Premium Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --font-display: 'Outfit', sans-serif;
        }
        body {
            font-family: var(--font-sans);
            background-color: #0c0c0e;
            color: #f4f4f5;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        
        /* Lock Screen Styling */
        .lock-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            box-sizing: border-box;
        }
        .lock-card {
            background-color: #121214;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 32px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .lock-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            color: #a1a1aa;
        }
        .lock-title {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #ffffff;
        }
        .lock-desc {
            font-size: 12px;
            color: #71717a;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .lock-input {
            width: 100%;
            background: #1c1c1f;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            padding: 10px 14px;
            color: #ffffff;
            font-size: 13px;
            text-align: center;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.15s;
            margin-bottom: 14px;
        }
        .lock-input:focus {
            border-color: #ffffff;
        }
        .lock-btn {
            width: 100%;
            background: #ffffff;
            color: #0c0c0e;
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }
        .lock-btn:hover {
            background: #e4e4e7;
        }
        .lock-error {
            color: #f87171;
            font-size: 11px;
            margin-top: 8px;
            font-weight: 500;
        }

        /* Gallery Page Header */
        header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background-color: rgba(12, 12, 14, 0.8);
            backdrop-filter: blur(8px);
            position: sticky;
            top: 0;
            z-index: 40;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .header-title-sec {
            display: flex;
            flex-direction: column;
        }
        .portfolio-title {
            font-family: var(--font-display);
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: #ffffff;
            letter-spacing: -0.3px;
        }
        .portfolio-subtitle {
            font-size: 11px;
            color: #71717a;
            margin-top: 2px;
            font-weight: 500;
        }
        .selection-counter {
            font-size: 12px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 6px 14px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Filters and Tabs */
        .filter-bar {
            max-width: 1200px;
            margin: 24px auto 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .filter-bar::-webkit-scrollbar {
            display: none;
        }
        .filter-btn {
            background: #1c1c1f;
            border: 1px solid rgba(255, 255, 255, 0.05);
            color: #a1a1aa;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 16px;
            border-radius: 9999px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s;
        }
        .filter-btn:hover {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
        }
        .filter-btn.active {
            background: #ffffff;
            color: #0c0c0e;
            border-color: #ffffff;
        }

        /* Gallery Layout Container */
        .portfolio-container {
            max-width: 1200px;
            margin: 24px auto 80px auto;
            padding: 0 24px;
        }

        /* Layout 1: Grid */
        .grid-layout {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        @media (max-width: 640px) {
            .grid-layout {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        /* Layout 2: Masonry */
        .masonry-layout {
            column-count: 3;
            column-gap: 20px;
        }
        @media (max-width: 900px) {
            .masonry-layout {
                column-count: 2;
            }
        }
        @media (max-width: 600px) {
            .masonry-layout {
                column-count: 1;
            }
        }
        .masonry-layout .portfolio-card {
            display: inline-block;
            width: 100%;
            margin-bottom: 20px;
            break-inside: avoid;
        }

        /* Layout 3: Carousel */
        .carousel-layout {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            gap: 24px;
            padding-bottom: 20px;
        }
        .carousel-layout::-webkit-scrollbar {
            height: 6px;
        }
        .carousel-layout::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 99px;
        }
        .carousel-layout::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 99px;
        }
        .carousel-layout .portfolio-card {
            flex: 0 0 85vw;
            scroll-snap-align: start;
        }
        @media (min-width: 768px) {
            .carousel-layout .portfolio-card {
                flex: 0 0 500px;
            }
        }

        /* Card Styling */
        .portfolio-card {
            background-color: #121214;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            transition: border-color 0.2s;
            display: flex;
            flex-direction: column;
        }
        .portfolio-card:hover {
            border-color: rgba(255, 255, 255, 0.15);
        }
        
        .media-wrapper {
            position: relative;
            background-color: #1c1c1f;
            width: 100%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .media-wrapper.image-type {
            aspect-ratio: 3/2;
        }
        .media-wrapper.video-type {
            aspect-ratio: 16/9;
        }
        
        .portfolio-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }
        .portfolio-card:hover .portfolio-img {
            transform: scale(1.03);
        }
        
        .portfolio-iframe {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
            background: #000;
        }
        
        /* Heart/Selection button styling */
        .heart-btn {
            position: absolute;
            top: 14px;
            right: 14px;
            z-index: 10;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(12, 12, 14, 0.7);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            outline: none;
            -webkit-tap-highlight-color: transparent;
        }
        .heart-btn:hover {
            transform: scale(1.1);
            background: rgba(12, 12, 14, 0.9);
            border-color: #ffffff;
        }
        .heart-btn:active {
            transform: scale(0.95);
        }
        .heart-btn.liked {
            background: #ffffff;
            color: #0c0c0e;
            border-color: #ffffff;
        }
        .heart-btn svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.2;
            transition: fill 0.2s;
        }
        .heart-btn.liked svg {
            fill: currentColor;
        }

        /* Asset Information overlay or card footer */
        .card-info {
            padding: 12px 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .asset-title {
            font-size: 12px;
            font-weight: 600;
            color: #ffffff;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 75%;
        }
        .asset-badge {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: rgba(255, 255, 255, 0.08);
            padding: 2px 6px;
            border-radius: 4px;
            color: #a1a1aa;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }
        .empty-icon {
            color: #3f3f46;
            margin-bottom: 16px;
        }
        .empty-title {
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 6px 0;
        }
        .empty-desc {
            font-size: 12px;
            color: #71717a;
            max-width: 300px;
            margin: 0 auto;
        }

        /* Custom Responsive Layout Toggles for Shared View */
        .layout-toggles {
            display: flex;
            align-items: center;
            background: #1c1c1f;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 9999px;
            padding: 2px;
        }
        .layout-toggle-btn {
            background: transparent;
            border: none;
            color: #a1a1aa;
            padding: 6px 12px;
            border-radius: 9999px;
            cursor: pointer;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.15s;
        }
        .layout-toggle-btn:hover {
            color: #ffffff;
        }
        .layout-toggle-btn.active {
            background: rgba(255,255,255,0.08);
            color: #ffffff;
        }
        /* Premium Monochromatic Lightbox */
        .cora-public-lightbox-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(9, 9, 11, 0.95);
            backdrop-filter: blur(8px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.25s ease-in-out;
        }
        .cora-public-lightbox-overlay.active {
            opacity: 1;
        }
        .cora-lightbox-content-wrapper {
            position: relative;
            max-width: 90vw;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .cora-lightbox-media-container {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cora-lightbox-media-container img {
            max-width: 90vw;
            max-height: 78vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .cora-lightbox-media-container video {
            max-width: 90vw;
            max-height: 78vh;
            border-radius: 8px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.05);
            background: #000;
        }
        .cora-lightbox-close {
            position: absolute;
            top: 24px;
            right: 32px;
            color: #a1a1aa;
            font-size: 36px;
            font-weight: 300;
            cursor: pointer;
            transition: color 0.15s, transform 0.15s;
            user-select: none;
            z-index: 10001;
        }
        .cora-lightbox-close:hover {
            color: #ffffff;
            transform: scale(1.1);
        }
        .cora-lightbox-title {
            margin-top: 16px;
            color: #ffffff;
            font-family: var(--font-sans);
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-align: center;
        }
        
        .video-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #121214;
            color: #52525b;
            height: 100%;
            width: 100%;
            border-radius: inherit;
        }

        .portfolio-card {
            cursor: pointer;
        }
        .video-play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 44px;
            height: 44px;
            background: rgba(12, 12, 14, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            z-index: 5;
        }
        .portfolio-card:hover .video-play-overlay {
            transform: translate(-50%, -50%) scale(1.1);
            background: #ffffff;
            color: #0c0c0e;
            border-color: #ffffff;
        }
        .video-play-overlay svg {
            margin-left: 2px;
        }
        .cora-lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(12, 12, 14, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #a1a1aa;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 10002;
            outline: none;
        }
        .cora-lightbox-nav:hover {
            background: #ffffff;
            color: #0c0c0e;
            border-color: #ffffff;
        }
        .cora-lightbox-nav.prev-btn {
            left: 32px;
        }
        .cora-lightbox-nav.next-btn {
            right: 32px;
        }
        @media (max-width: 768px) {
            .cora-lightbox-nav {
                width: 40px;
                height: 40px;
            }
            .cora-lightbox-nav.prev-btn {
                left: 12px;
            }
            .cora-lightbox-nav.next-btn {
                right: 12px;
            }
        }
    </style>
</head>
<body>

<?php if ( ! $authenticated ) : ?>
    <!-- Lock screen -->
    <div class="lock-container">
        <div class="lock-card">
            <div class="lock-icon">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h2 class="lock-title">Password Protected</h2>
            <p class="lock-desc">This client portfolio folder requires a password to view. Please enter the access code provided by your photographer.</p>
            <form method="POST" action="">
                <input type="password" name="portfolio_password" placeholder="Access Code" class="lock-input" required autofocus>
                <button type="submit" class="lock-btn">Unlock Gallery</button>
            </form>
            <?php if ( $password_error ) : ?>
                <div class="lock-error">Incorrect access code. Please try again.</div>
            <?php endif; ?>
        </div>
    </div>
<?php else : ?>
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="header-title-sec">
                <h1 class="portfolio-title"><?php echo esc_html( $found_portfolio['title'] ); ?></h1>
                <span class="portfolio-subtitle"><?php echo count( $assets ); ?> Assets • Indian Standard Format</span>
            </div>
            <div class="selection-counter">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="text-white"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                <span id="selected-count-label"><?php echo count( $likes ); ?></span> Selected
            </div>
        </div>
    </header>

    <!-- Filters & Layout Toggles -->
    <div class="filter-bar">
        <button class="filter-btn active" onclick="coraFilterAssets('all', this)">All</button>
        <button class="filter-btn" onclick="coraFilterAssets('image', this)">Photos</button>
        <button class="filter-btn" onclick="coraFilterAssets('video', this)">Videos</button>
        <button class="filter-btn" onclick="coraFilterAssets('hearted', this)">
            Hearted (<span id="hearted-filter-count"><?php echo count( $likes ); ?></span>)
        </button>
        
        <div class="layout-toggles" style="margin-left: auto;">
            <button class="layout-toggle-btn <?php echo $template === 'grid' ? 'active' : ''; ?>" onclick="coraChangeLayout('grid', this)">Grid</button>
            <button class="layout-toggle-btn <?php echo $template === 'masonry' ? 'active' : ''; ?>" onclick="coraChangeLayout('masonry', this)">Masonry</button>
            <button class="layout-toggle-btn <?php echo $template === 'carousel' ? 'active' : ''; ?>" onclick="coraChangeLayout('carousel', this)">Carousel</button>
        </div>
    </div>

    <!-- Gallery Container -->
    <div class="portfolio-container">
        <div id="cora-shared-portfolio-view" class="<?php 
            if ( $template === 'masonry' ) { echo 'masonry-layout'; }
            elseif ( $template === 'carousel' ) { echo 'carousel-layout'; }
            else { echo 'grid-layout'; }
        ?>">
            <?php if ( empty( $assets ) ) : ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" width="36" height="36" stroke="currentColor" stroke-width="1.5" fill="none">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </div>
                    <h4 class="empty-title">Gallery is Empty</h4>
                    <p class="empty-desc">No photos or videos have been linked to this shared folder yet.</p>
                </div>
            <?php else : ?>
                <?php foreach ( $assets as $asset ) : 
                    $is_liked = in_array( $asset['id'], $likes );
                    $url = isset( $asset['url'] ) ? trim( $asset['url'] ) : '';
                    $is_placeholder = ( empty( $url ) || $url === '#' || strpos( $url, 'javascript:' ) === 0 );

                    $is_direct_video = false;
                    if ( ! $is_placeholder ) {
                        $path = parse_url( $url, PHP_URL_PATH );
                        $ext = $path ? strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) : '';
                        if ( in_array( $ext, array( 'mp4', 'webm', 'ogg', 'mov' ) ) || strpos( $url, 'mixkit.co/videos' ) !== false ) {
                            $is_direct_video = true;
                        }
                    }
                ?>
                    <div class="portfolio-card" data-asset-id="<?php echo esc_attr( $asset['id'] ); ?>" data-type="<?php echo esc_attr( $asset['type'] ); ?>" data-liked="<?php echo $is_liked ? 'true' : 'false'; ?>" onclick="openCoraPublicLightbox('<?php echo esc_js($asset['id']); ?>')">
                        <button class="heart-btn <?php echo $is_liked ? 'liked' : ''; ?>" onclick="event.stopPropagation(); coraToggleLike('<?php echo esc_js($asset['id']); ?>')">
                            <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                        </button>
                        
                        <?php if ( $asset['type'] === 'video' ) : ?>
                            <div class="media-wrapper video-type" style="position: relative;">
                                <?php if ( $is_placeholder ) : ?>
                                    <div class="video-placeholder">
                                        <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 6px;">
                                            <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                                        </svg>
                                        <span style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #71717a;">Video Pending</span>
                                    </div>
                                <?php elseif ( $is_direct_video ) : ?>
                                    <video src="<?php echo esc_url( $url ); ?>" class="portfolio-img" style="object-fit: cover; pointer-events: none;" muted playsinline preload="metadata"></video>
                                    <div class="video-play-overlay">
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                        </svg>
                                    </div>
                                <?php else : ?>
                                    <iframe src="<?php echo esc_url( $url ); ?>" class="portfolio-iframe" style="pointer-events: none;" allow="autoplay; encrypted-media" tabindex="-1"></iframe>
                                    <div class="video-play-overlay">
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <div class="media-wrapper image-type">
                                <img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $asset['name'] ); ?>" class="portfolio-img" loading="lazy">
                            </div>
                        <?php endif; ?>

                        <div class="card-info">
                            <h3 class="asset-title"><?php echo esc_html( $asset['name'] ); ?></h3>
                            <span class="asset-badge"><?php echo esc_html( $asset['type'] ); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Toast elements placeholder -->
    <script>
        const portfolioHash = '<?php echo esc_js( $found_portfolio['hash'] ); ?>';
        const ajaxUrl = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';

        function showToast(message) {
            const oldToast = document.querySelector('.cora-public-toast');
            if (oldToast) oldToast.remove();

            const toast = document.createElement('div');
            toast.className = 'cora-public-toast fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-zinc-900 text-white text-xs font-semibold px-5 py-3 rounded-full shadow-2xl border border-zinc-800 transition-all duration-300 opacity-0 translate-y-3 z-50 flex items-center gap-2';
            toast.style.cssText = 'position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: #18181b; color: #ffffff; font-size: 12px; font-weight: 600; padding: 10px 20px; border-radius: 99px; border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.5); z-index: 100; transition: all 0.25s ease-out; opacity: 0;';
            toast.innerText = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.bottom = '32px';
            }, 50);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.bottom = '24px';
                setTimeout(() => toast.remove(), 250);
            }, 2500);
        }

        function coraToggleLike(assetId) {
            const card = document.querySelector(`.portfolio-card[data-asset-id="${assetId}"]`);
            if (!card) return;

            const button = card.querySelector('.heart-btn');
            const isCurrentlyLiked = card.getAttribute('data-liked') === 'true';
            const newLikedState = !isCurrentlyLiked;
            
            // Optimistic UI update
            card.setAttribute('data-liked', newLikedState ? 'true' : 'false');
            if (newLikedState) {
                button.classList.add('liked');
            } else {
                button.classList.remove('liked');
            }

            // AJAX trigger
            const data = new FormData();
            data.append('action', 'cora_toggle_portfolio_like');
            data.append('portfolio_hash', portfolioHash);
            data.append('asset_id', assetId);
            data.append('liked', newLikedState ? 'true' : 'false');

            fetch(ajaxUrl, {
                method: 'POST',
                body: data
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const likesCount = res.data.likes_count;
                    document.getElementById('selected-count-label').innerText = likesCount;
                    document.getElementById('hearted-filter-count').innerText = likesCount;
                    showToast(newLikedState ? 'Added to selection.' : 'Removed from selection.');
                    
                    // If we are currently in "hearted" filter and unliked, hide the card
                    const activeFilter = document.querySelector('.filter-btn.active').innerText.toLowerCase();
                    if (activeFilter.includes('hearted') && !newLikedState) {
                        card.style.display = 'none';
                    }
                } else {
                    // Revert UI on error
                    card.setAttribute('data-liked', isCurrentlyLiked ? 'true' : 'false');
                    if (isCurrentlyLiked) {
                        button.classList.add('liked');
                    } else {
                        button.classList.remove('liked');
                    }
                    showToast(res.data || 'Failed to update selection.');
                }
            })
            .catch(() => {
                // Revert UI on network error
                card.setAttribute('data-liked', isCurrentlyLiked ? 'true' : 'false');
                if (isCurrentlyLiked) {
                    button.classList.add('liked');
                } else {
                    button.classList.remove('liked');
                }
                showToast('Connection error. Please check your internet.');
            });
        }

        function coraFilterAssets(filterType, btnElement) {
            // Update active state in buttons
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            btnElement.classList.add('active');

            const cards = document.querySelectorAll('.portfolio-card');
            cards.forEach(card => {
                const type = card.getAttribute('data-type');
                const isLiked = card.getAttribute('data-liked') === 'true';

                if (filterType === 'all') {
                    card.style.display = 'flex';
                } else if (filterType === 'hearted') {
                    card.style.display = isLiked ? 'flex' : 'none';
                } else {
                    card.style.display = (type === filterType) ? 'flex' : 'none';
                }
            });
        }

        function coraChangeLayout(layoutType, btnElement) {
            document.querySelectorAll('.layout-toggle-btn').forEach(btn => btn.classList.remove('active'));
            btnElement.classList.add('active');

            const container = document.getElementById('cora-shared-portfolio-view');
            container.className = ''; // Reset class list

            if (layoutType === 'masonry') {
                container.className = 'masonry-layout';
            } else if (layoutType === 'carousel') {
                container.className = 'carousel-layout';
            } else {
                container.className = 'grid-layout';
            }
        }

        // Lightbox integration
        const coraGalleryAssets = <?php echo json_encode( array_values( $assets ) ); ?>;
        let coraCurrentAssetIndex = -1;

        function openCoraPublicLightbox(assetId) {
            const assetIndex = coraGalleryAssets.findIndex(a => a.id === assetId);
            if (assetIndex === -1) return;
            
            coraCurrentAssetIndex = assetIndex;
            renderCoraLightboxAsset();
            
            const lightbox = document.getElementById('cora-public-lightbox');
            lightbox.style.display = 'flex';
            // Force a reflow
            lightbox.offsetHeight;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            window.addEventListener('keydown', handleCoraLightboxKeys);
        }

        function closeCoraPublicLightbox() {
            const lightbox = document.getElementById('cora-public-lightbox');
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
            
            // Stop playing videos/audio
            const mediaContainer = document.getElementById('cora-lightbox-media');
            const videos = mediaContainer.querySelectorAll('video');
            videos.forEach(v => {
                v.pause();
                v.src = '';
                v.load();
            });
            const iframes = mediaContainer.querySelectorAll('iframe');
            iframes.forEach(f => {
                f.src = '';
            });
            
            setTimeout(() => {
                if (!lightbox.classList.contains('active')) {
                    lightbox.style.display = 'none';
                    mediaContainer.innerHTML = '';
                }
            }, 250);
            
            window.removeEventListener('keydown', handleCoraLightboxKeys);
        }

        function renderCoraLightboxAsset() {
            const asset = coraGalleryAssets[coraCurrentAssetIndex];
            if (!asset) return;
            
            const mediaContainer = document.getElementById('cora-lightbox-media');
            const captionContainer = document.getElementById('cora-lightbox-caption');
            
            mediaContainer.innerHTML = '';
            captionContainer.innerText = asset.name || '';
            
            const url = asset.url ? asset.url.trim() : '';
            const isPlaceholder = (!url || url === '#' || url.startsWith('javascript:'));
            
            if (asset.type === 'video') {
                if (isPlaceholder) {
                    mediaContainer.innerHTML = `
                        <div class="video-placeholder" style="width: 60vw; height: 40vh; max-width: 600px; padding: 24px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 20px 50px rgba(0,0,0,0.8);">
                            <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 12px; color: #52525b;">
                                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                            </svg>
                            <span style="font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #71717a;">Video Pending</span>
                        </div>
                    `;
                } else {
                    const isDirectVideo = (
                        url.endsWith('.mp4') || 
                        url.endsWith('.webm') || 
                        url.endsWith('.ogg') || 
                        url.endsWith('.mov') || 
                        url.includes('mixkit.co/videos')
                    );
                    
                    if (isDirectVideo) {
                        mediaContainer.innerHTML = `
                            <video src="${escapeHtml(url)}" controls autoplay style="max-width: 90vw; max-height: 78vh; border-radius: 8px; box-shadow: 0 20px 50px rgba(0,0,0,0.8); border: 1px solid rgba(255,255,255,0.05); background: #000;"></video>
                        `;
                    } else {
                        let embedUrl = url;
                        if (url.includes('drive.google.com')) {
                            embedUrl = url.replace('/view', '/preview');
                        }
                        mediaContainer.innerHTML = `
                            <iframe src="${escapeHtml(embedUrl)}" style="width: 80vw; height: 60vh; max-width: 960px; max-height: 600px; border: 0; background: #000; border-radius: 8px; box-shadow: 0 20px 50px rgba(0,0,0,0.8); border: 1px solid rgba(255,255,255,0.05);" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                        `;
                    }
                }
            } else {
                mediaContainer.innerHTML = `
                    <img src="${escapeHtml(url)}" alt="${escapeHtml(asset.name || '')}">
                `;
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function coraLightboxNext() {
            if (coraGalleryAssets.length <= 1) return;
            
            const mediaContainer = document.getElementById('cora-lightbox-media');
            const videos = mediaContainer.querySelectorAll('video');
            videos.forEach(v => { v.pause(); v.src = ''; v.load(); });
            const iframes = mediaContainer.querySelectorAll('iframe');
            iframes.forEach(f => { f.src = ''; });
            
            coraCurrentAssetIndex = (coraCurrentAssetIndex + 1) % coraGalleryAssets.length;
            renderCoraLightboxAsset();
        }

        function coraLightboxPrev() {
            if (coraGalleryAssets.length <= 1) return;
            
            const mediaContainer = document.getElementById('cora-lightbox-media');
            const videos = mediaContainer.querySelectorAll('video');
            videos.forEach(v => { v.pause(); v.src = ''; v.load(); });
            const iframes = mediaContainer.querySelectorAll('iframe');
            iframes.forEach(f => { f.src = ''; });
            
            coraCurrentAssetIndex = (coraCurrentAssetIndex - 1 + coraGalleryAssets.length) % coraGalleryAssets.length;
            renderCoraLightboxAsset();
        }

        function handleCoraLightboxKeys(e) {
            if (e.key === 'Escape') {
                closeCoraPublicLightbox();
            } else if (e.key === 'ArrowRight') {
                coraLightboxNext();
            } else if (e.key === 'ArrowLeft') {
                coraLightboxPrev();
            }
        }
    </script>

    <!-- Cora Public Lightbox Markup -->
    <div id="cora-public-lightbox" class="cora-public-lightbox-overlay" style="display: none;" onclick="if(event.target === this) closeCoraPublicLightbox()">
        <span class="cora-lightbox-close" onclick="closeCoraPublicLightbox()">&times;</span>
        
        <button class="cora-lightbox-nav prev-btn" onclick="coraLightboxPrev()" aria-label="Previous">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        
        <div class="cora-lightbox-content-wrapper">
            <div id="cora-lightbox-media" class="cora-lightbox-media-container"></div>
            <div id="cora-lightbox-caption" class="cora-lightbox-title"></div>
        </div>
        
        <button class="cora-lightbox-nav next-btn" onclick="coraLightboxNext()" aria-label="Next">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>
<?php endif; ?>

</body>
</html>
