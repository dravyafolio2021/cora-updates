<?php
/**
 * View: Feature Hub & Platform Showcase
 * Redesigned to match high-converting SaaS showcase carousel architecture.
 * Engineered with bulletproof CSS Layout (100% resilient to Tailwind purging).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cora_industry = get_option( 'cora_workspace_industry', 'real_estate' );
$industry_title = ( $cora_industry === 'real_estate' ) ? 'Real Estate Professionals.' : 'Photography & Studio Professionals.';
?>

<style>
/* Core Feature Hub Layout Engine */
.cora-fh-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 16px 8px 40px 8px;
    box-sizing: border-box;
    user-select: none;
}

.cora-fh-header {
    text-align: center;
    max-width: 720px;
    margin: 0 auto 32px auto;
}

.cora-fh-badge-pill {
    display: inline-block;
    padding: 4px 14px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: #52525b;
    background: #f4f4f5;
    border: 1px solid #e4e4e7;
    border-radius: 9999px;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.cora-fh-title {
    font-size: 32px;
    font-weight: 800;
    color: #09090b;
    letter-spacing: -0.02em;
    line-height: 1.2;
    margin: 0 0 8px 0;
}

.cora-fh-subtitle {
    font-size: 15px;
    color: #71717a;
    font-weight: 500;
    margin: 0;
}

/* Showcase Card & Carousel Wrap */
.cora-fh-carousel-wrap {
    position: relative;
    width: 100%;
}

.cora-fh-card {
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 24px;
    padding: 36px;
    box-shadow: 0 20px 40px -15px rgba(0,0,0,0.07);
    transition: all 0.25s ease-in-out;
    box-sizing: border-box;
    width: 100%;
}

.cora-fh-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
    align-items: start !important;
    gap: 40px !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

.cora-fh-left-col {
    width: 100% !important;
    min-width: 0 !important;
    box-sizing: border-box !important;
}

.cora-fh-right-col {
    width: 100% !important;
    min-width: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 18px !important;
    box-sizing: border-box !important;
}

@media (max-width: 1024px) {
    .cora-fh-grid {
        grid-template-columns: minmax(0, 1fr) !important;
        gap: 28px !important;
    }
}

/* Left Visual Graphic Container */
.cora-fh-graphic-box {
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 50%, #f1f5f9 100%);
    border: 1px solid #ddd6fe;
    border-radius: 20px;
    padding: 32px 24px;
    position: relative;
    overflow: hidden;
    min-height: 380px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 2px 6px rgba(0,0,0,0.03);
    box-sizing: border-box;
}

/* Browser Mockup Window */
.cora-fh-browser-mockup {
    width: 100%;
    max-width: 400px;
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 12px;
    box-shadow: 0 20px 35px -10px rgba(0,0,0,0.14);
    overflow: hidden;
    position: relative;
    z-index: 5;
    transform: rotate(-1deg);
    transition: transform 0.3s ease;
}

.cora-fh-browser-mockup:hover {
    transform: rotate(0deg);
}

.cora-fh-browser-header {
    background: #f4f4f5;
    padding: 8px 12px;
    border-bottom: 1px solid #e4e4e7;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cora-fh-dots {
    display: flex;
    gap: 5px;
}

.cora-fh-dot-red { width: 9px; height: 9px; border-radius: 50%; background: #ef4444; }
.cora-fh-dot-yellow { width: 9px; height: 9px; border-radius: 50%; background: #f59e0b; }
.cora-fh-dot-green { width: 9px; height: 9px; border-radius: 50%; background: #10b981; }

.cora-fh-url-bar {
    margin: 0 auto;
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 6px;
    padding: 3px 10px;
    font-size: 10px;
    color: #71717a;
    font-family: monospace;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.03);
}

.cora-fh-mockup-body {
    position: relative;
    width: 100%;
    height: 180px;
    overflow: hidden;
    background: #18181b;
}

.cora-fh-mockup-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.cora-fh-mockup-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 12px;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
    color: #ffffff;
}

.cora-fh-mockup-tag {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    opacity: 0.85;
    display: block;
}

.cora-fh-mockup-title {
    font-size: 13px;
    font-weight: 700;
    margin: 2px 0 0 0;
}

.cora-fh-thumbs-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    padding: 8px;
    background: #fafafa;
}

.cora-fh-thumb-img {
    width: 100%;
    height: 38px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #e4e4e7;
}

/* Floating Overlay Badges */
.cora-fh-badge-overlay {
    position: absolute;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    border: 1px solid #e4e4e7;
    border-radius: 12px;
    padding: 8px 12px;
    box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1);
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 8px;
}

.cora-fh-badge-top-left { top: 16px; left: 16px; }
.cora-fh-badge-bottom-left { bottom: 16px; left: 16px; }
.cora-fh-badge-top-right { top: 16px; right: 16px; }

.cora-fh-badge-icon-wrap {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: #eeef4ff;
    color: #4f46e5;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cora-fh-badge-text-title {
    font-size: 11px;
    font-weight: 700;
    color: #18181b;
    line-height: 1.2;
}

.cora-fh-badge-text-sub {
    font-size: 9px;
    color: #059669;
    font-weight: 600;
}

/* Mobile Frame Overlay */
.cora-fh-mobile-frame {
    position: absolute;
    right: -10px;
    bottom: -10px;
    width: 115px;
    background: #09090b;
    padding: 6px;
    border-radius: 16px;
    box-shadow: 0 20px 30px rgba(0,0,0,0.25);
    border: 1px solid #27272a;
    z-index: 12;
    transform: rotate(3deg);
}

@media (max-width: 640px) {
    .cora-fh-mobile-frame, .cora-fh-badge-top-right {
        display: none !important;
    }
}

.cora-fh-mobile-inner {
    background: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    padding: 6px;
}

/* Right Specification Styling */
.cora-fh-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 700;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}

.cora-fh-feature-title {
    font-size: 26px;
    font-weight: 800;
    color: #09090b;
    margin: 0;
    line-height: 1.25;
    letter-spacing: -0.01em;
    word-break: normal !important;
    overflow-wrap: normal !important;
}

.cora-fh-feature-desc {
    font-size: 14px;
    color: #52525b;
    line-height: 1.6;
    margin: 0;
    word-break: normal !important;
    overflow-wrap: normal !important;
}

/* Why You'll Love It Section */
.cora-fh-section-label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #09090b;
    margin-bottom: 8px;
}

.cora-fh-reasons-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    width: 100%;
}

.cora-fh-reason-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #3f3f46;
}

.cora-fh-reason-icon {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    background: #e0e7ff;
    color: #4338ca;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Perfect For Tags */
.cora-fh-tags-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.cora-fh-tag-pill {
    padding: 4px 10px;
    border-radius: 6px;
    background: #f4f4f5;
    border: 1px solid #e4e4e7;
    font-size: 11px;
    font-weight: 600;
    color: #52525b;
}

/* Stats Bar Grid */
.cora-fh-stats-bar {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    background: #fafafa;
    border: 1px solid #e4e4e7;
    border-radius: 16px;
    padding: 12px;
    text-align: center;
}

.cora-fh-stat-val {
    font-size: 18px;
    font-weight: 800;
    color: #09090b;
}

.cora-fh-stat-lbl {
    font-size: 10px;
    color: #71717a;
    font-weight: 500;
}

/* Action Buttons */
.cora-fh-actions-row {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 4px;
}

.cora-fh-btn-primary {
    padding: 12px 24px;
    background: #09090b;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.cora-fh-btn-primary:hover {
    background: #27272a;
    transform: translateY(-1px);
}

.cora-fh-btn-secondary {
    font-size: 12px;
    font-weight: 700;
    color: #52525b;
    background: none;
    border: none;
    text-decoration: underline;
    text-underline-offset: 4px;
    cursor: pointer;
}

.cora-fh-btn-secondary:hover {
    color: #09090b;
}

/* Navigation Arrow Buttons */
.cora-fh-nav-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid #e4e4e7;
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #18181b;
    cursor: pointer;
    z-index: 25;
    transition: all 0.2s ease;
}

.cora-fh-nav-arrow:hover {
    background: #f4f4f5;
    transform: translateY(-50%) scale(1.06);
}

.cora-fh-nav-left { left: -22px; }
.cora-fh-nav-right { right: -22px; }

/* Pagination Dots Bar */
.cora-fh-pagination-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    margin-top: 24px;
}

.cora-fh-dots-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

.cora-fh-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d4d4d8;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.cora-fh-dot.active {
    width: 10px;
    height: 10px;
    background: #09090b;
    transform: scale(1.15);
}

.cora-fh-counter {
    font-size: 11px;
    font-weight: 700;
    color: #a1a1aa;
    letter-spacing: 0.05em;
}

/* Bottom Horizontal Module Nav Bar */
.cora-fh-bottom-nav-wrap {
    margin-top: 28px;
    width: 100%;
}

.cora-fh-bottom-nav-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    overflow-x: auto;
    padding: 10px 16px;
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 18px;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08);
    scrollbar-width: none;
}

.cora-fh-bottom-nav-bar::-webkit-scrollbar {
    display: none;
}

.cora-fh-module-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
    color: #52525b;
    background: transparent;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.cora-fh-module-btn:hover {
    background: #f4f4f5;
    color: #09090b;
}

.cora-fh-module-btn.active {
    background: #09090b;
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
</style>

<div class="cora-fh-container">

    <!-- 1. TOP HEADER SECTION -->
    <div class="cora-fh-header">
        <span class="cora-fh-badge-pill">FEATURE HUB</span>
        <h1 class="cora-fh-title">
            Powerful Features. Built for <?php echo esc_html( $industry_title ); ?>
        </h1>
        <p class="cora-fh-subtitle">
            Explore everything our platform offers to simplify your workflow and impress your clients.
        </p>
    </div>

    <!-- 2. MAIN FEATURE SHOWCASE CAROUSEL CONTAINER -->
    <div class="cora-fh-carousel-wrap">

        <!-- Navigation Arrow Left -->
        <button type="button" onclick="coraFeatureHubPrev()" class="cora-fh-nav-arrow cora-fh-nav-left" aria-label="Previous Feature">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>

        <!-- Main Showcase Card -->
        <div id="cora-feature-showcase-card" class="cora-fh-card">
            
            <div class="cora-fh-grid">
                
                <!-- Left Side: Visual Interactive Mockup Graphic -->
                <div class="cora-fh-left-col">
                    <div class="cora-fh-graphic-box">
                        
                        <!-- Floating Badge Top-Left -->
                        <div class="cora-fh-badge-overlay cora-fh-badge-top-left">
                            <div class="cora-fh-badge-icon-wrap">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            <div>
                                <div class="cora-fh-badge-text-title">Password Protected</div>
                                <div style="font-size: 9px; color: #a1a1aa; font-family: monospace;">• • • • •</div>
                            </div>
                        </div>

                        <!-- Floating Badge Top-Right Stats -->
                        <div class="cora-fh-badge-overlay cora-fh-badge-top-right">
                            <div>
                                <div style="font-size: 9px; font-weight: 700; color: #a1a1aa; text-transform: uppercase;">Total Views</div>
                                <div id="cora-mockup-stat-views" style="font-size: 13px; font-weight: 800; color: #09090b; display: flex; align-items: center; gap: 4px;">
                                    1,248 <span style="font-size: 9px; color: #059669; background: #ecfdf5; padding: 1px 4px; border-radius: 4px; font-weight: 700;">+32%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Browser Mockup Card -->
                        <div class="cora-fh-browser-mockup">
                            <div class="cora-fh-browser-header">
                                <div class="cora-fh-dots">
                                    <span class="cora-fh-dot-red"></span>
                                    <span class="cora-fh-dot-yellow"></span>
                                    <span class="cora-fh-dot-green"></span>
                                </div>
                                <div class="cora-fh-url-bar">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    <span id="cora-mockup-url-text">portfolio.yourbrand.com/sunnyvale-villa</span>
                                </div>
                            </div>
                            <div class="cora-fh-mockup-body">
                                <img id="cora-mockup-image" src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80" alt="Feature Preview" class="cora-fh-mockup-img">
                                <div class="cora-fh-mockup-overlay">
                                    <span id="cora-mockup-tag" class="cora-fh-mockup-tag">Modern 4BHK Villa with Infinity Pool</span>
                                    <h4 id="cora-mockup-title" class="cora-fh-mockup-title">Sunnyvale Luxury Villa</h4>
                                </div>
                            </div>
                            <div id="cora-mockup-thumbs" class="cora-fh-thumbs-strip">
                                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=200&q=80" class="cora-fh-thumb-img">
                                <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=200&q=80" class="cora-fh-thumb-img">
                                <img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=200&q=80" class="cora-fh-thumb-img">
                                <img src="https://images.unsplash.com/photo-1600573472591-ee6b68d14c68?auto=format&fit=crop&w=200&q=80" class="cora-fh-thumb-img">
                            </div>
                        </div>

                        <!-- Floating Badge Bottom-Left -->
                        <div class="cora-fh-badge-overlay cora-fh-badge-bottom-left">
                            <div class="cora-fh-badge-icon-wrap" style="background: #ecfdf5; color: #059669;">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            </div>
                            <div>
                                <div class="cora-fh-badge-text-title">Share Link</div>
                                <div class="cora-fh-badge-text-sub">Copied! ✓</div>
                            </div>
                        </div>

                        <!-- Mobile Phone Frame Overlay Right -->
                        <div class="cora-fh-mobile-frame">
                            <div class="cora-fh-mobile-inner">
                                <div style="width: 24px; height: 3px; background: #e4e4e7; border-radius: 99px; margin: 0 auto 4px auto;"></div>
                                <img id="cora-mockup-mobile-img" src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=200&q=80" style="width: 100%; height: 50px; object-fit: cover; border-radius: 4px; display: block;">
                                <div style="font-size: 8px; font-weight: 700; color: #18181b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 4px;">Sunnyvale Villa</div>
                                <div style="font-size: 7px; color: #a1a1aa;">Modern 4BHK</div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right Side: Feature Specification & Details -->
                <div class="cora-fh-right-col">
                    
                    <!-- Status Badge -->
                    <div>
                        <span id="cora-showcase-status" class="cora-fh-status-badge">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span>
                            ACTIVE
                        </span>
                    </div>

                    <!-- Title & Description -->
                    <div>
                        <h2 id="cora-showcase-title" class="cora-fh-feature-title">
                            Beautiful Property Portfolios
                        </h2>
                        <p id="cora-showcase-desc" class="cora-fh-feature-desc" style="margin-top: 8px;">
                            Create stunning, password-protected property showcases with photos, videos, and detailed information that impress high-paying clients.
                        </p>
                    </div>

                    <!-- Why You'll Love It (2-Column Grid) -->
                    <div style="border-top: 1px solid #f4f4f5; padding-top: 14px;">
                        <div class="cora-fh-section-label">Why you'll love it</div>
                        <div id="cora-showcase-features-grid" class="cora-fh-reasons-grid">
                            <div class="cora-fh-reason-item">
                                <span class="cora-fh-reason-icon"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
                                <span>Password Protection</span>
                            </div>
                            <div class="cora-fh-reason-item">
                                <span class="cora-fh-reason-icon"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg></span>
                                <span>Instant Sharing</span>
                            </div>
                            <div class="cora-fh-reason-item">
                                <span class="cora-fh-reason-icon"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg></span>
                                <span>Video Support</span>
                            </div>
                            <div class="cora-fh-reason-item">
                                <span class="cora-fh-reason-icon"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg></span>
                                <span>Download Options</span>
                            </div>
                            <div class="cora-fh-reason-item">
                                <span class="cora-fh-reason-icon"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></span>
                                <span>Beautiful Galleries</span>
                            </div>
                            <div class="cora-fh-reason-item">
                                <span class="cora-fh-reason-icon"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg></span>
                                <span>Analytics & Insights</span>
                            </div>
                        </div>
                    </div>

                    <!-- Perfect For Tags -->
                    <div>
                        <div class="cora-fh-section-label">Perfect for</div>
                        <div id="cora-showcase-tags" class="cora-fh-tags-row">
                            <span class="cora-fh-tag-pill">Luxury Realtors</span>
                            <span class="cora-fh-tag-pill">Property Agencies</span>
                            <span class="cora-fh-tag-pill">Developers</span>
                            <span class="cora-fh-tag-pill">Architects</span>
                        </div>
                    </div>

                    <!-- Metrics Stats Bar -->
                    <div class="cora-fh-stats-bar">
                        <div>
                            <div id="cora-mockup-stat-1" class="cora-fh-stat-val">98%</div>
                            <div id="cora-mockup-stat-1-lbl" class="cora-fh-stat-lbl">Client Satisfaction</div>
                        </div>
                        <div style="border-left: 1px solid #e4e4e7; border-right: 1px solid #e4e4e7;">
                            <div id="cora-mockup-stat-2" class="cora-fh-stat-val">8 hrs</div>
                            <div id="cora-mockup-stat-2-lbl" class="cora-fh-stat-lbl">Saved Per Listing</div>
                        </div>
                        <div>
                            <div id="cora-mockup-stat-3" class="cora-fh-stat-val">2 Clicks</div>
                            <div id="cora-mockup-stat-3-lbl" class="cora-fh-stat-lbl">To Share</div>
                        </div>
                    </div>

                    <!-- Action CTA Buttons -->
                    <div class="cora-fh-actions-row">
                        <button type="button" id="cora-showcase-btn-primary" onclick="coraLaunchCurrentFeature()" class="cora-fh-btn-primary">
                            <span>Explore Feature</span>
                            <span style="font-size: 14px;">→</span>
                        </button>
                        <button type="button" onclick="window.coraShowToast('Use cases guide loaded!')" class="cora-fh-btn-secondary">
                            View Use Cases
                        </button>
                    </div>

                </div>

            </div>

        </div>

        <!-- Navigation Arrow Right -->
        <button type="button" onclick="coraFeatureHubNext()" class="cora-fh-nav-arrow cora-fh-nav-right" aria-label="Next Feature">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </button>

    </div>

    <!-- 3. CAROUSEL PAGINATION & COUNTER -->
    <div class="cora-fh-pagination-wrap">
        <div id="cora-feature-dots-bar" class="cora-fh-dots-row">
            <!-- Dots dynamically generated -->
        </div>
        <div id="cora-feature-counter" class="cora-fh-counter">
            01 / 12
        </div>
    </div>

    <!-- 4. BOTTOM HORIZONTAL MODULE SELECTOR BAR -->
    <div class="cora-fh-bottom-nav-wrap">
        <div class="cora-fh-bottom-nav-bar">
            
            <button type="button" onclick="coraSelectFeatureSlide(0)" class="cora-fh-module-btn active">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                <span>Portfolios</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(1)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span>Showcases</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(2)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                <span>Print Storefront</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(3)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                <span>Quotations</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(4)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M9 15l2 2 4-4"></path></svg>
                <span>Invoicing</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(5)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                <span>Contracts</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(6)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <span>Client Portal</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(7)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="10 9 9 9 8 9"></polyline></svg>
                <span>Lead Form</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(8)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span>Maps SEO</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(9)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <span>Notifications</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(10)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                <span>Reviews</span>
            </button>

            <button type="button" onclick="coraSelectFeatureSlide(11)" class="cora-fh-module-btn">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
                <span>Referrals</span>
            </button>

        </div>
    </div>

</div>

<!-- FEATURE HUB CAROUSEL DATA & LOGIC SCRIPT -->
<script>
(function($) {
    'use strict';

    var coraFeaturesData = [
        {
            title: "Beautiful Property Portfolios",
            status: "ACTIVE",
            desc: "Create stunning, password-protected property showcases with photos, videos, and detailed information that impress high-paying clients.",
            url: "<?php echo home_url('/workspace/canvas'); ?>",
            mockupUrl: "portfolio.yourbrand.com/sunnyvale-villa",
            mockupTag: "Modern 4BHK Villa with Infinity Pool",
            mockupTitle: "Sunnyvale Luxury Villa",
            imgMain: "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1600573472591-ee6b68d14c68?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["Password Protection", "Instant Sharing", "Video Support", "Download Options", "Beautiful Galleries", "Analytics & Insights"],
            perfectFor: ["Luxury Realtors", "Property Agencies", "Developers", "Architects"],
            stat1: "98%", stat1Lbl: "Client Satisfaction",
            stat2: "8 hrs", stat2Lbl: "Saved Per Listing",
            stat3: "2 Clicks", stat3Lbl: "To Share"
        },
        {
            title: "Easy Property Showcases",
            status: "ACTIVE",
            desc: "Couples and buyers can easily tap the heart icon on their phone to select favorite photos for printed albums or listing shortlists, synced live with the admin panel.",
            url: "<?php echo home_url('/workspace/canvas'); ?>",
            mockupUrl: "showcase.yourbrand.com/curated-selection",
            mockupTag: "Live Client Photo Selection Feed",
            mockupTitle: "Client Photo Selection Studio",
            imgMain: "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1600573472591-ee6b68d14c68?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["1-Tap Heart Selection", "Live Admin Sync", "Favorite Filtering", "Proofing Notes", "Instant PDF Proof", "Mobile Responsive"],
            perfectFor: ["Listing Agents", "Wedding Photographers", "Studio Directors", "Client Managers"],
            stat1: "100%", stat1Lbl: "Real-time Sync",
            stat2: "15 min", stat2Lbl: "Avg Selection Time",
            stat3: "0 Setup", stat3Lbl: "Required"
        },
        {
            title: "Branded Print Storefront",
            status: "SOON",
            desc: "Sell premium layflat print albums, canvas prints, and custom frames directly to portfolio visitors with automated print lab fulfillment.",
            url: "#",
            mockupUrl: "shop.yourbrand.com/storefront",
            mockupTag: "Automated eCommerce Storefront",
            mockupTitle: "Custom Frame & Print Shop",
            imgMain: "https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1579783902614-a3fb3927b675?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1582555172866-f73bb12a2ab3?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["Automated Print Lab", "Stripe / UPI Checkout", "Custom Markup Prices", "Digital Download Bundles", "Zero Inventory Risk", "Client Self-Serve"],
            perfectFor: ["High-End Studios", "Commercial Photographers", "Art Galleries", "Boutique Agencies"],
            stat1: "3.5x", stat1Lbl: "Margin Revenue",
            stat2: "100%", stat2Lbl: "Automated Lab",
            stat3: "Instant", stat3Lbl: "Digital Delivery"
        },
        {
            title: "Instant Quotations",
            status: "ACTIVE",
            desc: "Generate professional PDF proposals with your listing packages or photo shoot packages and send them to prospective clients in 1 click.",
            url: "<?php echo home_url('/workspace/vault'); ?>",
            mockupUrl: "vault.yourbrand.com/quote-builder",
            mockupTag: "Automated Quotation Generator",
            mockupTitle: "Instant Proposal Engine",
            imgMain: "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["1-Click PDF Generation", "Custom Tier Pricing", "WhatsApp Share Link", "Client E-Acceptance", "Brand Watermarking", "CRM Integration"],
            perfectFor: ["Sales Representatives", "Account Managers", "Studio Executives", "Realtors"],
            stat1: "95%", stat1Lbl: "Proposal Win Rate",
            stat2: "30 sec", stat2Lbl: "PDF Creation",
            stat3: "100%", stat3Lbl: "Mobile Friendly"
        },
        {
            title: "Automated Invoicing & Receipts",
            status: "ACTIVE",
            desc: "Automatically generate listing agreements, advance booking receipts, GST/tax-compliant invoices, and payment tracking for all clients.",
            url: "<?php echo home_url('/workspace/vault'); ?>",
            mockupUrl: "billing.yourbrand.com/invoices",
            mockupTag: "Tax-Compliant Invoicing System",
            mockupTitle: "Automated Billing & Ledger",
            imgMain: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["GST/Tax Compliant", "Advance Deposit Tracking", "Auto Payment Reminders", "Partial Payment Support", "Instant PDF Download", "Ledger Auto-Post"],
            perfectFor: ["Finance Managers", "Studio Owners", "Accountants", "Brokers"],
            stat1: "0", stat1Lbl: "Overdue Invoices",
            stat2: "100%", stat2Lbl: "Tax Compliance",
            stat3: "2x", stat3Lbl: "Faster Collection"
        },
        {
            title: "Contracts & E-Signatures",
            status: "ACTIVE",
            desc: "Legally binding online contracts with digital signatures, built-in legal templates, and automated PDF copy delivery upon execution.",
            url: "<?php echo home_url('/workspace/vault'); ?>",
            mockupUrl: "contracts.yourbrand.com/e-sign",
            mockupTag: "Legally Binding E-Signatures",
            mockupTitle: "Digital Contract Execution",
            imgMain: "https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["Finger / Stylus Drawing", "Audit Log & IP Stamp", "Pre-built Templates", "Automatic Copy Email", "Encrypted Storage", "Zero Paperwork"],
            perfectFor: ["Agency Owners", "Legal Teams", "Project Coordinators", "Brokers"],
            stat1: "100%", stat1Lbl: "Legal Validity",
            stat2: "< 2 min", stat2Lbl: "Sign Duration",
            stat3: "100%", stat3Lbl: "Paperless"
        },
        {
            title: "Private Client Portal",
            status: "SOON",
            desc: "A private dashboard for clients to access timelines, upload shot list requests, sign contracts, download media, and check payment status.",
            url: "#",
            mockupUrl: "portal.yourbrand.com/client-login",
            mockupTag: "Dedicated Client Dashboard",
            mockupTitle: "Client Self-Serve Workspace",
            imgMain: "https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["Custom Branding", "Live Timeline Progress", "Shot List Upload", "Invoice History", "Direct Message Feed", "Secure Access"],
            perfectFor: ["VIP Clients", "Corporate Buyers", "High-Net-Worth Leads"],
            stat1: "24/7", stat1Lbl: "Self-Serve Portal",
            stat2: "99%", stat2Lbl: "Client Delight",
            stat3: "0 Calls", stat3Lbl: "Needed for Updates"
        },
        {
            title: "Universal Lead Form",
            status: "SOON",
            desc: "Create and embed custom booking forms on your website, Google Business site, or Instagram bio. Captured leads flow directly into your CRM feed.",
            url: "<?php echo home_url('/workspace/forms'); ?>",
            mockupUrl: "forms.yourbrand.com/inquiry-form",
            mockupTag: "Embeddable High-Converting Lead Form",
            mockupTitle: "Smart Booking & Lead Form",
            imgMain: "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["Embed Anywhere", "Auto-CRM Entry", "Instant WhatsApp Alert", "Date Availability Check", "Custom Styling", "SPAM Filtered"],
            perfectFor: ["Marketing Leads", "Website Visitors", "Instagram Bio Link", "Landing Pages"],
            stat1: "4.2x", stat1Lbl: "Lead Conversion",
            stat2: "Instant", stat2Lbl: "WhatsApp Alert",
            stat3: "100%", stat3Lbl: "CRM Auto-Sync"
        },
        {
            title: "Google Maps SEO Booster",
            status: "ACTIVE",
            desc: "Manage your Google Business Profile, track search map rankings, view and reply to customer reviews, and post geotagged updates.",
            url: "<?php echo home_url('/workspace/gbp'); ?>",
            mockupUrl: "maps.google.com/business/cora-profile",
            mockupTag: "Google Local SEO & Maps Optimizer",
            mockupTitle: "Google Profile & Review Manager",
            imgMain: "https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["1-Click Google OAuth", "AI Review Responder", "Post Publisher", "Keyword SEO Analytics", "Rank Tracking", "Local Maps Dominance"],
            perfectFor: ["Local Agencies", "Studio Owners", "Local SEO Managers", "Franchise Outlets"],
            stat1: "14,820", stat1Lbl: "Maps Impressions",
            stat2: "4.9 ★", stat2Lbl: "Avg Rating",
            stat3: "284", stat3Lbl: "Direct Calls/Mo"
        },
        {
            title: "Client Notifications",
            status: "SOON",
            desc: "Automatically send booking confirmations, contract signature reminders, and advance deposit due date alerts to clients via SMS and WhatsApp.",
            url: "#",
            mockupUrl: "notify.yourbrand.com/triggers",
            mockupTag: "Automated WhatsApp & SMS Gateway",
            mockupTitle: "Client Communication Engine",
            imgMain: "https://images.unsplash.com/photo-1616469829941-c7200edec809?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1616469829941-c7200edec809?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["WhatsApp API Direct", "Custom Variables", "Automated Timing", "Delivery Confirmation", "Zero Spam Reports", "Template Library"],
            perfectFor: ["Client Managers", "Operations Teams", "Coordinators"],
            stat1: "99.4%", stat1Lbl: "Open Rate",
            stat2: "< 5 sec", stat2Lbl: "Delivery Speed",
            stat3: "0", stat3Lbl: "Missed Deadlines"
        },
        {
            title: "Smart Review Acquisition",
            status: "SOON",
            desc: "Auto-send custom review requests via WhatsApp/SMS after property handover or deal closure to collect 5-star Google Business ratings.",
            url: "#",
            mockupUrl: "reviews.yourbrand.com/collector",
            mockupTag: "Automated 5-Star Review Collector",
            mockupTitle: "Reputation Builder Engine",
            imgMain: "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1616469829941-c7200edec809?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["Automatic Post-Handover", "Negative Feedback Shield", "Direct Google Maps Link", "AI Review Responder", "High Conversion Rate"],
            perfectFor: ["Brand Owners", "Reputation Managers", "Local Services"],
            stat1: "84%", stat1Lbl: "Review Response Rate",
            stat2: "4.9 ★", stat2Lbl: "Target Rating",
            stat3: "5x", stat3Lbl: "More Google Reviews"
        },
        {
            title: "Referral Rewards Engine",
            status: "SOON",
            desc: "Create automated referral links for past clients. Give them print storefront discounts or gift cards when their friends book a viewing or shoot.",
            url: "#",
            mockupUrl: "referrals.yourbrand.com/dashboard",
            mockupTag: "Automated Viral Referral Engine",
            mockupTitle: "Client Referral & Loyalty Program",
            imgMain: "https://images.unsplash.com/photo-1556742049-0a67568d0d9f?auto=format&fit=crop&w=800&q=80",
            imgThumbs: [
                "https://images.unsplash.com/photo-1556742049-0a67568d0d9f?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1616469829941-c7200edec809?auto=format&fit=crop&w=200&q=80",
                "https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=200&q=80"
            ],
            whyList: ["Unique Referral Links", "Automated Rewards", "Track Payouts", "Client Leaderboard", "Coupon Code Integration"],
            perfectFor: ["Growth Marketers", "Studio Owners", "Sales Directors"],
            stat1: "28%", stat1Lbl: "New Client Referrals",
            stat2: "0 Ad Spend", stat2Lbl: "Organic Lead Gen",
            stat3: "100%", stat3Lbl: "Automated Tracking"
        }
    ];

    var currentSlideIndex = 0;

    function renderDotsBar() {
        var $bar = $('#cora-feature-dots-bar');
        $bar.empty();
        $.each(coraFeaturesData, function(idx, item) {
            var activeClass = (idx === currentSlideIndex) ? ' active' : '';
            var $dot = $('<button type="button" class="cora-fh-dot' + activeClass + '"></button>');
            $dot.on('click', function() {
                coraSelectFeatureSlide(idx);
            });
            $bar.append($dot);
        });

        // Update counter
        var formattedCurrent = (currentSlideIndex + 1 < 10 ? '0' : '') + (currentSlideIndex + 1);
        var formattedTotal = (coraFeaturesData.length < 10 ? '0' : '') + coraFeaturesData.length;
        $('#cora-feature-counter').text(formattedCurrent + ' / ' + formattedTotal);
    }

    window.coraSelectFeatureSlide = function(index) {
        if (index < 0 || index >= coraFeaturesData.length) return;
        currentSlideIndex = index;

        var data = coraFeaturesData[currentSlideIndex];

        // Animate showcase card transition
        var $card = $('#cora-feature-showcase-card');
        $card.css('opacity', '0.5');

        setTimeout(function() {
            // Update Title & Desc
            $('#cora-showcase-title').text(data.title);
            $('#cora-showcase-desc').text(data.desc);

            // Update Status Badge
            var $status = $('#cora-showcase-status');
            if (data.status === 'ACTIVE') {
                $status.attr('style', 'display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;')
                       .html('<span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span> ACTIVE');
            } else {
                $status.attr('style', 'display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: #f4f4f5; color: #71717a; border: 1px solid #e4e4e7;')
                       .html('SOON');
            }

            // Update Mockups
            $('#cora-mockup-url-text').text(data.mockupUrl);
            $('#cora-mockup-tag').text(data.mockupTag);
            $('#cora-mockup-title').text(data.mockupTitle);
            $('#cora-mockup-image').attr('src', data.imgMain);
            if (data.imgThumbs && data.imgThumbs.length >= 4) {
                $('#cora-mockup-mobile-img').attr('src', data.imgThumbs[1]);
                var $t = $('#cora-mockup-thumbs').empty();
                $.each(data.imgThumbs, function(i, srcUrl) {
                    $t.append('<img src="' + srcUrl + '" class="cora-fh-thumb-img">');
                });
            }

            // Update Features Grid
            var $grid = $('#cora-showcase-features-grid').empty();
            $.each(data.whyList, function(i, featText) {
                $grid.append(
                    '<div class="cora-fh-reason-item">' +
                        '<span class="cora-fh-reason-icon">' +
                            '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
                        '</span>' +
                        '<span>' + featText + '</span>' +
                    '</div>'
                );
            });

            // Update Perfect For Tags
            var $tags = $('#cora-showcase-tags').empty();
            $.each(data.perfectFor, function(i, tagText) {
                $tags.append('<span class="cora-fh-tag-pill">' + tagText + '</span>');
            });

            // Update Stats Bar
            $('#cora-mockup-stat-1').text(data.stat1);
            $('#cora-mockup-stat-1-lbl').text(data.stat1Lbl);
            $('#cora-mockup-stat-2').text(data.stat2);
            $('#cora-mockup-stat-2-lbl').text(data.stat2Lbl);
            $('#cora-mockup-stat-3').text(data.stat3);
            $('#cora-mockup-stat-3-lbl').text(data.stat3Lbl);

            // Update CTA Button
            var $btn = $('#cora-showcase-btn-primary');
            if (data.status === 'ACTIVE' && data.url && data.url !== '#') {
                $btn.css({'opacity': '1', 'cursor': 'pointer'}).html('<span>Explore Feature</span> <span style="font-size: 14px;">→</span>');
            } else {
                $btn.css({'opacity': '0.5', 'cursor': 'not-allowed'}).html('<span>Coming Soon</span>');
            }

            // Update Nav Pills Active State
            $('.cora-fh-module-btn').removeClass('active');
            $('.cora-fh-module-btn').eq(currentSlideIndex).addClass('active');

            renderDotsBar();
            $card.css('opacity', '1');
        }, 120);
    };

    window.coraFeatureHubNext = function() {
        var nextIdx = (currentSlideIndex + 1) % coraFeaturesData.length;
        coraSelectFeatureSlide(nextIdx);
    };

    window.coraFeatureHubPrev = function() {
        var prevIdx = (currentSlideIndex - 1 + coraFeaturesData.length) % coraFeaturesData.length;
        coraSelectFeatureSlide(prevIdx);
    };

    window.coraLaunchCurrentFeature = function() {
        var data = coraFeaturesData[currentSlideIndex];
        if (data.status === 'ACTIVE' && data.url && data.url !== '#') {
            window.location.href = data.url;
        } else {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast("This feature is coming soon in the next platform release!");
            }
        }
    };

    // Initialize on load
    $(document).ready(function() {
        renderDotsBar();
    });

})(jQuery);
</script>
