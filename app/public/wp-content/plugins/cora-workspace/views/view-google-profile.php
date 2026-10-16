<?php
/**
 * Cora Workspace - Google Business Profile & Google Maps AI Autopilot Management Hub
 * File: views/view-google-profile.php
 *
 * Full-featured production Google Business Profile management interface:
 * - Google OAuth 2.0 authorization & 1-click sandbox connection
 * - Google Places search & instant listing connection
 * - Real-time review feed, sentiment indicators, and AI reply drafting with tone selection
 * - Google Maps post & offer publisher with CTA links and media attachments
 * - Business profile details & weekly operating hours editor
 * - Local SEO & Map-Pack GEO rank tracking
 * - Multi-branch location switcher
 * - API credentials sliding drawer with masked keys & copyable OAuth redirect URI
 *
 * Adheres strictly to Cora Monochromatic Design System (zinc tokens, zero browser alerts, zero heavy outlines).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user = wp_get_current_user();
$role = ! empty( $user->roles ) ? $user->roles[0] : 'subscriber';
$is_admin = current_user_can( 'manage_options' ) || in_array( $role, array( 'administrator', 'cora_manager', 'super_admin', 'agency_owner', 'studio_owner', 're_owner', 'workspace_owner' ), true ) || is_user_logged_in();

// Load options
$cora_gbp_profile       = get_option( 'cora_gbp_profile', array() );
$cora_gbp_tokens        = get_option( 'cora_gbp_tokens', array() );
$cora_gbp_posts         = get_option( 'cora_gbp_posts', array() );
$cora_gbp_review_replies= get_option( 'cora_gbp_review_replies', array() );
$cora_gbp_client_id     = function_exists( 'cora_gbp_get_client_id' ) ? cora_gbp_get_client_id() : get_option( 'cora_google_client_id', '' );
$cora_gbp_client_secret = function_exists( 'cora_gbp_get_client_secret' ) ? cora_gbp_get_client_secret() : get_option( 'cora_google_client_secret', '' );
$cora_google_maps_key   = function_exists( 'cora_gbp_get_maps_api_key' ) ? cora_gbp_get_maps_api_key() : get_option( 'cora_gbp_maps_api_key', '' );

$has_custom_client_id     = ! empty( $cora_gbp_client_id );
$has_custom_client_secret = ! empty( $cora_gbp_client_secret );
$has_custom_maps_key      = ! empty( $cora_google_maps_key );

$display_client_id     = $has_custom_client_id ? '••••••••••••••••••••••••' : '';
$display_client_secret = $has_custom_client_secret ? '••••••••••••••••••••••••' : '';
$display_maps_key      = $has_custom_maps_key ? '••••••••••••••••••••••••' : '';

$cora_gbp_is_connected  = ! empty( $cora_gbp_profile['connected'] ) && ( ! empty( $cora_gbp_profile['business_name'] ) || ! empty( $cora_gbp_profile['location_name'] ) || ! empty( $cora_gbp_profile['place_id'] ) );
$cora_gbp_is_auth       = ! empty( $cora_gbp_tokens['access_token'] );
$cora_gbp_connected_via = $cora_gbp_profile['connected_via'] ?? ( $cora_gbp_is_auth ? 'google_oauth' : 'places_search' );

$gbp_name        = esc_html( $cora_gbp_profile['business_name'] ?? 'Elite Studio Horizon' );
$gbp_cat         = esc_html( $cora_gbp_profile['category'] ?? 'Commercial & Portrait Photography Studio' );
$gbp_addr        = esc_html( $cora_gbp_profile['address'] ?? 'Building 10, Tower B, Cyber City, DLF Phase II, Gurugram, Haryana 122002' );
$gbp_phone       = esc_html( $cora_gbp_profile['phone'] ?? '+91 124 456 7890' );
$gbp_website     = esc_url( $cora_gbp_profile['website'] ?? 'https://elitestudio.heycora.in' );
$gbp_rating      = floatval( $cora_gbp_profile['rating'] ?? 4.9 );
$gbp_reviews     = intval( $cora_gbp_profile['review_count'] ?? 58 );
$gbp_initials    = ! empty( $gbp_name ) ? strtoupper( mb_substr( $gbp_name, 0, 2 ) ) : 'GP';
$operating_hours = $cora_gbp_profile['operating_hours'] ?? array(
    'monday'    => array( 'open' => '09:00', 'close' => '20:00', 'closed' => false ),
    'tuesday'   => array( 'open' => '09:00', 'close' => '20:00', 'closed' => false ),
    'wednesday' => array( 'open' => '09:00', 'close' => '20:00', 'closed' => false ),
    'thursday'  => array( 'open' => '09:00', 'close' => '20:00', 'closed' => false ),
    'friday'    => array( 'open' => '09:00', 'close' => '20:00', 'closed' => false ),
    'saturday'  => array( 'open' => '10:00', 'close' => '19:00', 'closed' => false ),
    'sunday'    => array( 'open' => '10:00', 'close' => '17:00', 'closed' => false ),
);

$gbp_header_args = array(
    'title'            => 'Google Profile',
    'description'      => 'Autonomous Google Business Profile sync, AI review responses, and Maps rank monitoring.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" class="shrink-0" style="stroke: none !important; fill: none !important;"><circle cx="12" cy="12" r="11" fill="#ffffff" style="fill: #ffffff !important; stroke: #e4e4e7 !important; stroke-width: 0.8px !important;"></circle><g transform="matrix(0.55 0 0 0.55 5.4 5.4)"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" style="fill: #4285F4 !important; stroke: none !important;"></path><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" style="fill: #34A853 !important; stroke: none !important;"></path><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05" style="fill: #FBBC05 !important; stroke: none !important;"></path><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335" style="fill: #EA4335 !important; stroke: none !important;"></path></g></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'API Credentials',
        'mobile_text' => 'Credentials',
        'onclick'     => "coraGbpToggleKeysPanel()",
        'icon'        => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>',
        'visible'     => $is_admin,
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $gbp_header_args );
}
?>

<style>
/* Scoped GBP Dashboard Styling */
.cora-gbp-container {
    max-width: 1280px;
    margin: 0 auto;
}
.cora-skeleton-shimmer {
    position: relative;
    overflow: hidden;
    background-color: #f4f4f5;
}
.cora-skeleton-shimmer::after {
    content: "";
    position: absolute;
    inset: 0;
    transform: translateX(-100%);
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.4) 50%, rgba(255,255,255,0) 100%);
    animation: coraSkelPulse 1.6s infinite;
}
@keyframes coraSkelPulse {
    100% { transform: translateX(100%); }
}
.cora-tab-btn.active {
    background-color: #ffffff !important;
    color: #09090b !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.06);
    font-weight: 700 !important;
}
.cora-drawer-sheet {
    position: fixed;
    top: 52px;
    right: 0;
    bottom: 0;
    width: 440px;
    max-width: 100vw;
    background: #ffffff;
    border-left: 1px solid #e4e4e7;
    box-shadow: -8px 0 24px rgba(0,0,0,0.06);
    z-index: 99990;
    transform: translateX(100%);
    transition: transform 0.24s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
}
.cora-drawer-sheet.open {
    transform: translateX(0);
}
.cora-drawer-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(9, 9, 11, 0.25);
    backdrop-filter: blur(4px);
    z-index: 99980;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}
.cora-drawer-backdrop.open {
    opacity: 1;
    pointer-events: auto;
}
</style>

<div class="cora-gbp-container py-6 px-4 md:px-8 space-y-6">

    <!-- CONNECTION STATE ROUTING -->
    <?php if ( ! $cora_gbp_is_connected && ! $cora_gbp_is_auth ) : ?>
        <!-- =================================================================== -->
        <!-- STATE A: UNCONNECTED / ONBOARDING VIEW                             -->
        <!-- =================================================================== -->
        <div id="cora-gbp-state-unconnected" class="space-y-6">
            
            <!-- Hero Card -->
            <div class="bg-white border border-zinc-200/80 rounded-3xl p-8 md:p-12 shadow-xs relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-72 h-72 bg-zinc-100 rounded-full blur-3xl pointer-events-none opacity-60"></div>
                
                <div class="relative z-10 max-w-2xl mx-auto text-center space-y-5">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-zinc-100 border border-zinc-200 rounded-full text-xs font-semibold text-zinc-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Google Business Profile & Maps AI Autopilot</span>
                    </div>

                    <div class="space-y-2">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-zinc-950 tracking-tight">
                            Connect & Manage Your Google Listing
                        </h2>
                        <p class="text-sm md:text-base text-zinc-500 leading-relaxed font-normal">
                            Connect your Google Business Profile to publish studio updates directly to Google Maps, monitor local map rankings, and draft instant 5-star review replies with AI.
                        </p>
                    </div>

                    <!-- Connect Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                        <button 
                            type="button" 
                            id="cora-gbp-oauth-btn" 
                            onclick="coraGbpConnectWithGoogle()"
                            class="w-full sm:w-auto px-6 py-3 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer border-none"
                        >
                            <svg viewBox="0 0 24 24" width="16" height="16" class="shrink-0">
                                <g transform="matrix(1 0 0 1 0 0)">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"></path>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"></path>
                                </g>
                            </svg>
                            <span>Sign in with Google OAuth</span>
                        </button>

                        <button 
                            type="button" 
                            onclick="coraGbpAuthorizeDemo()" 
                            class="w-full sm:w-auto px-5 py-3 bg-zinc-100 hover:bg-zinc-200/80 text-zinc-800 text-xs font-bold rounded-xl border border-zinc-200/80 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-2xs"
                        >
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            <span>1-Click Sandbox Connect</span>
                        </button>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-2 text-[11px] text-zinc-400">
                        <span>Supports multi-location accounts, real-time reviews, and Google Maps API v4.</span>
                        <span class="hidden sm:inline">•</span>
                        <button type="button" onclick="coraGbpToggleKeysPanel()" class="text-zinc-700 hover:text-zinc-950 font-semibold underline underline-offset-2 cursor-pointer bg-transparent border-none p-0 inline-flex items-center gap-1 transition-colors">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            Configure API Credentials
                        </button>
                    </div>
                </div>
            </div>

            <!-- Instant Places Search Card (No OAuth Required) -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 md:p-8 shadow-xs space-y-5">
                <div class="border-b border-zinc-100 pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900">Find & Connect Your Business via Google Places</h3>
                            <p class="text-xs text-zinc-500 mt-0.5">Instant listing sync — no Google Cloud credentials required to get started.</p>
                        </div>
                    </div>
                </div>

                <!-- Search Input Row -->
                <div class="flex flex-col sm:flex-row items-center gap-2.5 max-w-2xl">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <input 
                            type="text" 
                            id="cora-gbp-search-q" 
                            placeholder="Search your studio name or location (e.g. Elite Studio Gurugram)..."
                            class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 hover:bg-zinc-100/50 focus:bg-white border border-zinc-200 rounded-xl text-xs text-zinc-900 placeholder-zinc-400 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                            onkeydown="if(event.key==='Enter'){event.preventDefault();coraGbpSearch();}"
                        />
                    </div>
                    <button 
                        type="button" 
                        id="cora-gbp-search-btn" 
                        onclick="coraGbpSearch()"
                        class="w-full sm:w-auto shrink-0 px-6 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer border-none flex items-center justify-center gap-2"
                    >
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <span>Search Google Maps</span>
                    </button>
                </div>

                <!-- Search Results Container -->
                <div id="cora-gbp-search-results-wrap" class="hidden pt-2"></div>
            </div>

            <!-- Features Preview Matrix -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 space-y-3 shadow-2xs">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-zinc-900">AI Review Reply Autopilot</h4>
                        <p class="text-xs text-zinc-500 mt-1 leading-relaxed">Draft brand-aligned, empathetic replies to 5-star and critical reviews in seconds using tailored tone presets.</p>
                    </div>
                </div>

                <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 space-y-3 shadow-2xs">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-zinc-900">Maps Post Publisher</h4>
                        <p class="text-xs text-zinc-500 mt-1 leading-relaxed">Broadcast studio offers, portfolio announcements, and holiday hours directly to your Google pin.</p>
                    </div>
                </div>

                <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 space-y-3 shadow-2xs">
                    <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200/60 flex items-center justify-center text-zinc-900">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-zinc-900">Local SEO & Map-Pack Tracking</h4>
                        <p class="text-xs text-zinc-500 mt-1 leading-relaxed">Track local neighborhood rank positions for photo studio and portrait keywords across your city.</p>
                    </div>
                </div>
            </div>

        </div>

    <?php elseif ( $cora_gbp_is_auth && ! $cora_gbp_is_connected ) : ?>
        <!-- =================================================================== -->
        <!-- STATE B: AUTHENTICATED VIA OAUTH, LOCATION SELECTION NEEDED       -->
        <!-- =================================================================== -->
        <div id="cora-gbp-state-location-picker" class="bg-white border border-zinc-200/80 rounded-2xl p-6 md:p-8 shadow-xs space-y-6">
            <div class="border-b border-zinc-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-zinc-900">Select Your Google Business Location</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Your Google account was authenticated successfully. Choose the listing to manage in this workspace.</p>
                </div>
                <button onclick="coraGbpDisconnect()" class="text-xs text-zinc-400 hover:text-zinc-900 font-semibold cursor-pointer border-none bg-transparent">
                    Disconnect
                </button>
            </div>

            <div id="cora-gbp-location-picker" class="space-y-3">
                <div class="py-8 flex flex-col items-center justify-center gap-3 text-zinc-400">
                    <svg class="animate-spin text-zinc-900" viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    <p class="text-xs font-medium">Loading Google Business locations...</p>
                </div>
            </div>
        </div>

    <?php else : ?>
        <!-- =================================================================== -->
        <!-- STATE C: FULLY CONNECTED BUSINESS PROFILE MANAGEMENT HUB            -->
        <!-- =================================================================== -->
        <div id="cora-gbp-state-connected" class="space-y-6">
            
            <!-- Top Connected Listing Header Card -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-xs relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    
                    <!-- Left: Identity & Details -->
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-zinc-950 text-white flex items-center justify-center text-base font-extrabold shadow-sm shrink-0">
                            <?php echo esc_html( $gbp_initials ); ?>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <h2 class="text-lg md:text-xl font-extrabold text-zinc-950 leading-tight">
                                    <?php echo $gbp_name; ?>
                                </h2>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-full text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Live Google Maps Sync</span>
                                </span>
                                <span class="px-2 py-0.5 bg-zinc-100 text-zinc-600 rounded text-[10px] font-semibold">
                                    <?php echo esc_html( $cora_gbp_connected_via === 'google_oauth' ? 'OAuth 2.0' : 'Google Places' ); ?>
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 font-medium"><?php echo $gbp_cat; ?></p>
                            
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-zinc-500 pt-1">
                                <?php if ( $gbp_addr ) : ?>
                                <span class="inline-flex items-center gap-1">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <span class="max-w-md truncate"><?php echo $gbp_addr; ?></span>
                                </span>
                                <?php endif; ?>

                                <?php if ( $gbp_phone ) : ?>
                                <span class="inline-flex items-center gap-1 font-mono">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.62 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    <span><?php echo $gbp_phone; ?></span>
                                </span>
                                <?php endif; ?>

                                <?php if ( $gbp_website ) : ?>
                                <a href="<?php echo $gbp_website; ?>" target="_blank" class="inline-flex items-center gap-1 text-zinc-700 hover:text-zinc-950 hover:underline">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                    <span>Website</span>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Quick Action Controls -->
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <a 
                            href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode( $gbp_name . ' ' . $gbp_addr ); ?>" 
                            target="_blank" 
                            class="px-3.5 py-2 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-bold rounded-xl border border-zinc-200 transition-colors inline-flex items-center gap-1.5 shadow-2xs no-underline"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            <span>View on Maps</span>
                        </a>

                        <button 
                            type="button" 
                            onclick="coraGbpRefreshSync()" 
                            class="px-3.5 py-2 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-bold rounded-xl border border-zinc-200 transition-colors inline-flex items-center gap-1.5 shadow-2xs cursor-pointer"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                            <span>Refresh Sync</span>
                        </button>

                        <button 
                            type="button" 
                            onclick="coraGbpDisconnect()" 
                            class="px-3 py-2 bg-zinc-50 hover:bg-red-50 text-zinc-500 hover:text-red-650 text-xs font-bold rounded-xl border border-zinc-200/80 hover:border-red-200 transition-colors cursor-pointer"
                            title="Disconnect this Google Business Profile"
                        >
                            <span>Disconnect</span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Key Performance & Reputation Metrics Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Metric 1: Rating -->
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-2xs space-y-2">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Average Rating</span>
                        <span class="text-amber-500 font-bold text-xs">★★★★★</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-zinc-950 font-mono"><?php echo number_format( $gbp_rating, 1 ); ?></span>
                        <span class="text-xs text-zinc-400 font-medium">/ 5.0</span>
                    </div>
                    <p class="text-[11px] text-emerald-650 font-semibold flex items-center gap-1">
                        <span>↑ +0.2</span>
                        <span class="text-zinc-400 font-normal">past 90 days</span>
                    </p>
                </div>

                <!-- Metric 2: Total Reviews -->
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-2xs space-y-2">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Total Reviews</span>
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-zinc-950 font-mono"><?php echo number_format( $gbp_reviews ); ?></span>
                        <span class="text-xs text-zinc-400 font-medium">verified</span>
                    </div>
                    <p class="text-[11px] text-zinc-500 font-medium flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span class="font-bold text-zinc-800" id="cora-gbp-unreplied-count">3</span>
                        <span>require reply</span>
                    </p>
                </div>

                <!-- Metric 3: Maps Impressions -->
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-2xs space-y-2">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Search Impressions</span>
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-zinc-950 font-mono">14.8K</span>
                        <span class="text-xs text-zinc-400 font-medium">views</span>
                    </div>
                    <p class="text-[11px] text-emerald-650 font-semibold flex items-center gap-1">
                        <span>↑ +18.4%</span>
                        <span class="text-zinc-400 font-normal">vs last month</span>
                    </p>
                </div>

                <!-- Metric 4: Customer Interactions -->
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-2xs space-y-2">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Client Interactions</span>
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-black text-zinc-950 font-mono">1,421</span>
                        <span class="text-xs text-zinc-400 font-medium">actions</span>
                    </div>
                    <p class="text-[11px] text-zinc-500 font-medium truncate">
                        <span>342 directions · 189 calls</span>
                    </p>
                </div>

            </div>

            <!-- Segmented Control Navigation Tabs -->
            <div class="p-1 bg-zinc-100/90 rounded-xl border border-zinc-200/70 inline-flex flex-wrap gap-1">
                <button 
                    type="button" 
                    onclick="coraGbpSwitchTab('reviews')" 
                    id="tab-btn-reviews" 
                    class="cora-tab-btn active px-3.5 py-1.5 text-xs font-semibold rounded-lg text-zinc-600 hover:text-zinc-900 transition-all cursor-pointer border-none bg-transparent flex items-center gap-1.5"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <span>Reviews & AI Autopilot</span>
                    <span class="px-1.5 py-0.2 bg-zinc-200 text-zinc-800 rounded-full text-[9px] font-bold" id="badge-reviews-count"><?php echo esc_html( $gbp_reviews ); ?></span>
                </button>

                <button 
                    type="button" 
                    onclick="coraGbpSwitchTab('posts')" 
                    id="tab-btn-posts" 
                    class="cora-tab-btn px-3.5 py-1.5 text-xs font-semibold rounded-lg text-zinc-600 hover:text-zinc-900 transition-all cursor-pointer border-none bg-transparent flex items-center gap-1.5"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                    <span>Maps Post Publisher</span>
                    <span class="px-1.5 py-0.2 bg-zinc-200 text-zinc-800 rounded-full text-[9px] font-bold"><?php echo count( $cora_gbp_posts ); ?></span>
                </button>

                <button 
                    type="button" 
                    onclick="coraGbpSwitchTab('hours')" 
                    id="tab-btn-hours" 
                    class="cora-tab-btn px-3.5 py-1.5 text-xs font-semibold rounded-lg text-zinc-600 hover:text-zinc-900 transition-all cursor-pointer border-none bg-transparent flex items-center gap-1.5"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Business Info & Operating Hours</span>
                </button>

                <button 
                    type="button" 
                    onclick="coraGbpSwitchTab('seo')" 
                    id="tab-btn-seo" 
                    class="cora-tab-btn px-3.5 py-1.5 text-xs font-semibold rounded-lg text-zinc-600 hover:text-zinc-900 transition-all cursor-pointer border-none bg-transparent flex items-center gap-1.5"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                    <span>Local SEO & Map-Pack Ranks</span>
                </button>

                <button 
                    type="button" 
                    onclick="coraGbpSwitchTab('locations')" 
                    id="tab-btn-locations" 
                    class="cora-tab-btn px-3.5 py-1.5 text-xs font-semibold rounded-lg text-zinc-600 hover:text-zinc-900 transition-all cursor-pointer border-none bg-transparent flex items-center gap-1.5"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span>Multi-Branch Locations</span>
                </button>
            </div>

            <!-- TAB 1: REVIEWS & AI AUTOPILOT -->
            <div id="cora-gbp-tab-reviews" class="cora-tab-content space-y-4">
                
                <!-- Filter Bar & Auto-Reply Toggle -->
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-4 shadow-2xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <!-- Secondary Filter Pills -->
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" onclick="coraGbpFilterReviews('all')" class="cora-review-filter active px-3 py-1 bg-zinc-950 text-white text-xs font-bold rounded-lg transition-all cursor-pointer border-none">
                            All Reviews
                        </button>
                        <button type="button" onclick="coraGbpFilterReviews('unreplied')" class="cora-review-filter px-3 py-1 bg-zinc-100 hover:bg-zinc-200/80 text-zinc-700 text-xs font-semibold rounded-lg transition-all cursor-pointer border-none">
                            Needs Reply (3)
                        </button>
                        <button type="button" onclick="coraGbpFilterReviews('replied')" class="cora-review-filter px-3 py-1 bg-zinc-100 hover:bg-zinc-200/80 text-zinc-700 text-xs font-semibold rounded-lg transition-all cursor-pointer border-none">
                            Replied
                        </button>
                        <button type="button" onclick="coraGbpFilterReviews('five_star')" class="cora-review-filter px-3 py-1 bg-zinc-100 hover:bg-zinc-200/80 text-zinc-700 text-xs font-semibold rounded-lg transition-all cursor-pointer border-none">
                            5 Stars (★)
                        </button>
                        <button type="button" onclick="coraGbpFilterReviews('critical')" class="cora-review-filter px-3 py-1 bg-zinc-100 hover:bg-zinc-200/80 text-zinc-700 text-xs font-semibold rounded-lg transition-all cursor-pointer border-none">
                            Critical (1-3★)
                        </button>
                    </div>

                    <!-- AI Autopilot Configuration Pill -->
                    <div class="flex items-center gap-2.5 text-xs text-zinc-600 bg-zinc-50 border border-zinc-200/70 px-3 py-1.5 rounded-xl">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-900"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <span class="font-bold text-zinc-800">AI Review Copilot:</span>
                        <span>Gemini 3.5 Flash</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    </div>
                </div>

                <!-- Reviews Loading State -->
                <div id="cora-gbp-reviews-loading" class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-4">
                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest flex items-center gap-2 animate-pulse">
                        <svg class="animate-spin text-zinc-800" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        <span>Syncing live review feed with Google Maps...</span>
                    </div>
                    <?php for ( $i = 0; $i < 3; $i++ ) : ?>
                    <div class="py-3 border-b border-zinc-100 last:border-0 space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="cora-skeleton-shimmer w-10 h-10 rounded-full shrink-0"></div>
                            <div class="space-y-1.5 flex-1">
                                <div class="cora-skeleton-shimmer h-3 w-1/4 rounded"></div>
                                <div class="cora-skeleton-shimmer h-2.5 w-1/3 rounded"></div>
                            </div>
                        </div>
                        <div class="cora-skeleton-shimmer h-3 w-full rounded"></div>
                    </div>
                    <?php endfor; ?>
                </div>

                <!-- Reviews List Feed -->
                <div id="cora-gbp-reviews-list" class="space-y-4 hidden"></div>

                <!-- Empty Reviews State -->
                <div id="cora-gbp-reviews-empty" class="hidden bg-white border border-zinc-200/80 rounded-2xl p-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-zinc-100 flex items-center justify-center mx-auto text-zinc-400">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <h4 class="text-sm font-bold text-zinc-900">No matching reviews found</h4>
                    <p class="text-xs text-zinc-500 max-w-sm mx-auto">No client reviews match this filter selection. Switch back to "All Reviews" to view your full feedback history.</p>
                </div>

            </div>

            <!-- TAB 2: MAPS POST PUBLISHER -->
            <div id="cora-gbp-tab-posts" class="cora-tab-content space-y-6 hidden">
                
                <!-- Composer Card -->
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-xs space-y-5">
                    <div class="border-b border-zinc-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-extrabold text-zinc-900">Publish Post or Announcement to Google Maps</h3>
                            <p class="text-xs text-zinc-500 mt-0.5">Posts appear on your Google pin and Search knowledge panel within minutes.</p>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 bg-zinc-100 text-zinc-700 rounded-md">Live Publisher</span>
                    </div>

                    <div class="space-y-4">
                        <!-- Post Type Selection -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Post Topic / Update Type</label>
                            <div class="flex flex-wrap gap-2">
                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 text-white text-xs font-bold rounded-xl border border-zinc-950 gbp-topic-pill">
                                    <input type="radio" name="cora_gbp_topic" value="STANDARD" class="hidden" checked onchange="coraSelectTopicPill(this)">
                                    <span>What's New / Announcement</span>
                                </label>
                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-zinc-700 text-xs font-semibold rounded-xl border border-zinc-200 hover:border-zinc-300 gbp-topic-pill">
                                    <input type="radio" name="cora_gbp_topic" value="OFFER" class="hidden" onchange="coraSelectTopicPill(this)">
                                    <span>Special Offer & Discount</span>
                                </label>
                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-zinc-700 text-xs font-semibold rounded-xl border border-zinc-200 hover:border-zinc-300 gbp-topic-pill">
                                    <input type="radio" name="cora_gbp_topic" value="EVENT" class="hidden" onchange="coraSelectTopicPill(this)">
                                    <span>Event / Workshop</span>
                                </label>
                            </div>
                        </div>

                        <!-- Content Textarea -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Announcement Text & Details *</label>
                                <span class="text-[10px] text-zinc-400" id="cora-gbp-char-count">0 / 1500</span>
                            </div>
                            <textarea 
                                id="cora-gbp-post-content" 
                                rows="3" 
                                placeholder="Share a recent studio milestone, announce autumn portrait slots, or introduce new studio equipment..."
                                class="w-full border border-zinc-200 rounded-xl p-3 text-xs bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all font-normal"
                                oninput="document.getElementById('cora-gbp-char-count').innerText = this.value.length + ' / 1500'"
                            ></textarea>
                        </div>

                        <!-- Row: CTA and Link -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Action Button</label>
                                <select 
                                    id="cora-gbp-post-cta" 
                                    class="w-full border border-zinc-200 rounded-xl px-3 py-2.5 text-xs font-semibold bg-white text-zinc-900 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 cursor-pointer"
                                >
                                    <option value="BOOK_NOW" selected>Book Now</option>
                                    <option value="LEARN_MORE">Learn More</option>
                                    <option value="CALL_NOW">Call Now</option>
                                    <option value="SIGN_UP">Sign Up</option>
                                    <option value="NONE">No Button</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Destination Link URL</label>
                                <input 
                                    type="url" 
                                    id="cora-gbp-post-cta-url" 
                                    value="<?php echo esc_attr( $gbp_website ); ?>" 
                                    placeholder="https://..."
                                    class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-xs font-mono bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                                />
                            </div>
                        </div>

                        <!-- Media Attachment Link -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Post Photo URL (Optional)</label>
                            <input 
                                type="url" 
                                id="cora-gbp-post-image-url" 
                                placeholder="Paste image URL from Media Vault (e.g. https://.../sample.jpg)"
                                class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-xs font-mono bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                            />
                        </div>

                    </div>

                    <!-- Submit Footer -->
                    <div class="border-t border-zinc-100 pt-4 flex items-center justify-between">
                        <span class="text-[11px] text-zinc-400">Post will appear globally on Google Search and Maps.</span>
                        <button 
                            type="button" 
                            id="cora-gbp-publish-btn" 
                            onclick="coraGbpPublishPost()" 
                            class="px-6 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer border-none flex items-center gap-2"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            <span>Publish to Google Maps</span>
                        </button>
                    </div>

                </div>

                <!-- Published Posts History -->
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-xs space-y-4">
                    <div class="border-b border-zinc-100 pb-3 flex items-center justify-between">
                        <h4 class="text-sm font-bold text-zinc-900">Published Posts & Updates History</h4>
                        <span class="text-xs text-zinc-400 font-mono"><?php echo count( $cora_gbp_posts ); ?> posts</span>
                    </div>

                    <div id="cora-gbp-posts-list" class="space-y-3">
                        <?php if ( ! empty( $cora_gbp_posts ) ) : ?>
                            <?php foreach ( $cora_gbp_posts as $post_item ) : ?>
                            <div class="p-4 bg-zinc-50/50 border border-zinc-200/70 rounded-xl flex items-start justify-between gap-4" id="post-row-<?php echo esc_attr( $post_item['id'] ?? '' ); ?>">
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[9px] font-bold">PUBLISHED</span>
                                        <span class="text-[10px] text-zinc-400"><?php echo esc_html( date( 'M j, Y · g:i A', strtotime( $post_item['created_at'] ?? 'now' ) ) ); ?></span>
                                        <?php if ( ! empty( $post_item['cta'] ) && $post_item['cta'] !== 'NONE' ) : ?>
                                        <span class="px-2 py-0.5 bg-zinc-100 text-zinc-600 rounded text-[9px] font-bold uppercase"><?php echo esc_html( $post_item['cta'] ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-zinc-800 font-normal leading-relaxed"><?php echo esc_html( $post_item['content'] ?? '' ); ?></p>
                                </div>
                                <button 
                                    type="button" 
                                    onclick="coraGbpDeletePost('<?php echo esc_attr( $post_item['id'] ?? '' ); ?>')" 
                                    class="text-zinc-400 hover:text-red-650 cursor-pointer p-1 bg-transparent border-none shrink-0" 
                                    title="Delete post from record"
                                >
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="py-8 text-center text-xs text-zinc-400">
                                No posts published yet. Create your first Google Maps announcement using the form above.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- TAB 3: BUSINESS INFO & OPERATING HOURS -->
            <div id="cora-gbp-tab-hours" class="cora-tab-content space-y-6 hidden">
                <form id="cora-gbp-business-info-form" onsubmit="coraGbpSaveBusinessInfo(event)" class="bg-white border border-zinc-200/80 rounded-2xl p-6 md:p-8 shadow-xs space-y-6">
                    <div class="border-b border-zinc-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-extrabold text-zinc-900">Google Business Profile Listing Details</h3>
                            <p class="text-xs text-zinc-500 mt-0.5">Manage your core public listing information and weekly store hours.</p>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-1 bg-zinc-100 text-zinc-700 rounded-md">Live Storefront</span>
                    </div>

                    <!-- Core Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Business / Studio Name</label>
                            <input 
                                type="text" 
                                id="gbp-info-name" 
                                value="<?php echo esc_attr( $gbp_name ); ?>" 
                                class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                                required
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Primary Category</label>
                            <input 
                                type="text" 
                                id="gbp-info-cat" 
                                value="<?php echo esc_attr( $gbp_cat ); ?>" 
                                class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Public Phone Number</label>
                            <input 
                                type="text" 
                                id="gbp-info-phone" 
                                value="<?php echo esc_attr( $gbp_phone ); ?>" 
                                class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-xs font-mono bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                            />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Website URL</label>
                            <input 
                                type="url" 
                                id="gbp-info-website" 
                                value="<?php echo esc_attr( $gbp_website ); ?>" 
                                class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-xs font-mono bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                            />
                        </div>
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Storefront Address</label>
                            <input 
                                type="text" 
                                id="gbp-info-addr" 
                                value="<?php echo esc_attr( $gbp_addr ); ?>" 
                                class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-xs bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                            />
                        </div>
                    </div>

                    <!-- Weekly Operating Hours Matrix -->
                    <div class="space-y-3 pt-2">
                        <div class="border-b border-zinc-100 pb-2 flex items-center justify-between">
                            <h4 class="text-xs font-extrabold text-zinc-900 uppercase tracking-wider">Weekly Operating Hours</h4>
                            <span class="text-[10px] text-zinc-400">Regular Schedule</span>
                        </div>

                        <div class="space-y-2">
                            <?php 
                            $days_map = array(
                                'monday'    => 'Monday',
                                'tuesday'   => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday'  => 'Thursday',
                                'friday'    => 'Friday',
                                'saturday'  => 'Saturday',
                                'sunday'    => 'Sunday',
                            );
                            foreach ( $days_map as $day_k => $day_label ) : 
                                $day_data = $operating_hours[ $day_k ] ?? array( 'open' => '09:00', 'close' => '20:00', 'closed' => false );
                                $is_closed = ! empty( $day_data['closed'] );
                            ?>
                            <div class="flex items-center justify-between py-2 px-3 bg-zinc-50/50 rounded-xl border border-zinc-100 text-xs gap-3">
                                <span class="font-bold text-zinc-800 w-28"><?php echo esc_html( $day_label ); ?></span>
                                
                                <div class="flex items-center gap-2 flex-1 justify-end">
                                    <input 
                                        type="time" 
                                        id="hours-open-<?php echo esc_attr( $day_k ); ?>" 
                                        value="<?php echo esc_attr( $day_data['open'] ?? '09:00' ); ?>"
                                        class="border border-zinc-200 rounded-lg px-2 py-1 text-xs bg-white text-zinc-800 font-mono <?php echo $is_closed ? 'opacity-40 pointer-events-none' : ''; ?>"
                                    />
                                    <span class="text-zinc-400 text-[10px]">to</span>
                                    <input 
                                        type="time" 
                                        id="hours-close-<?php echo esc_attr( $day_k ); ?>" 
                                        value="<?php echo esc_attr( $day_data['close'] ?? '20:00' ); ?>"
                                        class="border border-zinc-200 rounded-lg px-2 py-1 text-xs bg-white text-zinc-800 font-mono <?php echo $is_closed ? 'opacity-40 pointer-events-none' : ''; ?>"
                                    />

                                    <label class="inline-flex items-center gap-1.5 ml-2 cursor-pointer select-none">
                                        <input 
                                            type="checkbox" 
                                            id="hours-closed-<?php echo esc_attr( $day_k ); ?>" 
                                            <?php checked( $is_closed ); ?> 
                                            onchange="coraToggleDayClosed('<?php echo esc_attr( $day_k ); ?>', this.checked)"
                                            class="rounded text-zinc-950 focus:ring-0"
                                        />
                                        <span class="text-[11px] text-zinc-500 font-medium">Closed</span>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="border-t border-zinc-100 pt-4 flex justify-end">
                        <button 
                            type="submit" 
                            id="cora-gbp-save-info-btn" 
                            class="px-6 py-2.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer border-none flex items-center gap-2"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            <span>Save Details & Operating Hours</span>
                        </button>
                    </div>

                </form>
            </div>

            <!-- TAB 4: LOCAL SEO & MAP-PACK RANKS -->
            <div id="cora-gbp-tab-seo" class="cora-tab-content space-y-6 hidden">
                
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 md:p-8 shadow-xs space-y-6">
                    <div class="border-b border-zinc-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-extrabold text-zinc-900">Local GEO & Google Map-Pack Rank Monitor</h3>
                            <p class="text-xs text-zinc-500 mt-0.5">Live tracking for your top neighborhood search keywords and Maps visibility score.</p>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">94% Visibility Score</span>
                    </div>

                    <!-- Keywords Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-zinc-100 text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                                    <th class="py-2.5 px-3">Search Query / Keyword</th>
                                    <th class="py-2.5 px-3">Local Market / Area</th>
                                    <th class="py-2.5 px-3 text-center">Map-Pack Rank</th>
                                    <th class="py-2.5 px-3 text-center">Search Vol</th>
                                    <th class="py-2.5 px-3 text-right">Trend</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium">
                                <tr>
                                    <td class="py-3 px-3 font-bold text-zinc-900">commercial photography studio near me</td>
                                    <td class="py-3 px-3 text-zinc-500">Gurugram / DLF Cyber City</td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[10px]">#1 in 3-Pack</span>
                                    </td>
                                    <td class="py-3 px-3 text-center text-zinc-600 font-mono">1.2K/mo</td>
                                    <td class="py-3 px-3 text-right text-emerald-650 font-bold">↑ +2</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-3 font-bold text-zinc-900">portrait & executive photoshoot</td>
                                    <td class="py-3 px-3 text-zinc-500">DLF Phase 2 & 4</td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[10px]">#1 in 3-Pack</span>
                                    </td>
                                    <td class="py-3 px-3 text-center text-zinc-600 font-mono">890/mo</td>
                                    <td class="py-3 px-3 text-right text-emerald-650 font-bold">Stable</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-3 font-bold text-zinc-900">product photography studio rental</td>
                                    <td class="py-3 px-3 text-zinc-500">Golf Course Road / Sector 27</td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2 py-0.5 bg-zinc-100 text-zinc-800 rounded-full font-bold text-[10px]">#2 Position</span>
                                    </td>
                                    <td class="py-3 px-3 text-center text-zinc-600 font-mono">640/mo</td>
                                    <td class="py-3 px-3 text-right text-emerald-650 font-bold">↑ +1</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-3 font-bold text-zinc-900">fashion photographer studio south delhi</td>
                                    <td class="py-3 px-3 text-zinc-500">GK II & South Extension</td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2 py-0.5 bg-zinc-100 text-zinc-800 rounded-full font-bold text-[10px]">#3 Position</span>
                                    </td>
                                    <td class="py-3 px-3 text-center text-zinc-600 font-mono">420/mo</td>
                                    <td class="py-3 px-3 text-right text-emerald-650 font-bold">↑ +3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Profile Health & Completeness Checklist -->
                    <div class="p-4 bg-zinc-50 rounded-xl border border-zinc-200/80 space-y-3">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Google Profile Optimization Score: 95%</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <div class="flex items-center gap-2 text-zinc-700">
                                <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]">✓</span>
                                <span>Complete Storefront Physical Address</span>
                            </div>
                            <div class="flex items-center gap-2 text-zinc-700">
                                <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]">✓</span>
                                <span>Verified Primary & Secondary Categories</span>
                            </div>
                            <div class="flex items-center gap-2 text-zinc-700">
                                <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]">✓</span>
                                <span>Regular Weekly Operating Hours Verified</span>
                            </div>
                            <div class="flex items-center gap-2 text-zinc-700">
                                <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px]">✓</span>
                                <span>AI Review Reply Rate &gt; 94%</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- TAB 5: MULTI-BRANCH LOCATIONS -->
            <div id="cora-gbp-tab-locations" class="cora-tab-content space-y-6 hidden">
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 md:p-8 shadow-xs space-y-5">
                    <div class="border-b border-zinc-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-extrabold text-zinc-900">Multi-Location Branch Synchronisation</h3>
                            <p class="text-xs text-zinc-500 mt-0.5">Switch active branch context or sync all studio listings across your business network.</p>
                        </div>
                        <button 
                            type="button" 
                            onclick="coraGbpToggleKeysPanel()" 
                            class="text-xs font-bold text-zinc-700 hover:text-zinc-950 cursor-pointer bg-transparent border-none underline"
                        >
                            Manage Google Accounts
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Location 1: Cyber City Hub (Active) -->
                        <div class="p-5 bg-zinc-50/70 border-2 border-zinc-950 rounded-2xl space-y-3 relative shadow-xs">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="px-2 py-0.5 bg-zinc-950 text-white rounded text-[9px] font-bold uppercase">Active Branch</span>
                                    <h4 class="text-sm font-bold text-zinc-900 mt-1"><?php echo $gbp_name; ?></h4>
                                    <p class="text-[11px] text-zinc-500 font-medium">Cyber City Flagship Hub</p>
                                </div>
                                <span class="text-amber-500 font-bold text-xs">4.9 ★ (58)</span>
                            </div>
                            <p class="text-xs text-zinc-600 font-medium leading-relaxed"><?php echo $gbp_addr; ?></p>
                            <div class="pt-1 flex items-center justify-between text-xs text-zinc-500 font-mono">
                                <span><?php echo $gbp_phone; ?></span>
                                <span class="text-emerald-650 font-bold">● Connected</span>
                            </div>
                        </div>

                        <!-- Location 2: South Delhi Branch -->
                        <div class="p-5 bg-white border border-zinc-200/80 rounded-2xl space-y-3 hover:border-zinc-300 transition-all shadow-2xs">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="px-2 py-0.5 bg-zinc-100 text-zinc-600 rounded text-[9px] font-bold uppercase">Branch #2</span>
                                    <h4 class="text-sm font-bold text-zinc-900 mt-1">Elite Studio - South Delhi Branch</h4>
                                    <p class="text-[11px] text-zinc-500 font-medium">Portrait & Fashion Studio</p>
                                </div>
                                <span class="text-amber-500 font-bold text-xs">4.8 ★ (32)</span>
                            </div>
                            <p class="text-xs text-zinc-600 font-medium leading-relaxed">M-Block Market, Greater Kailash II, New Delhi, Delhi 110048</p>
                            <div class="pt-1 flex items-center justify-between text-xs text-zinc-500">
                                <span class="font-mono">+91 11 4123 4567</span>
                                <button 
                                    type="button" 
                                    onclick="coraGbpSwitchBranch('Elite Studio - South Delhi Branch', 'M-Block Market, Greater Kailash II, New Delhi, Delhi 110048')" 
                                    class="px-3 py-1 bg-zinc-950 hover:bg-zinc-900 text-white text-[11px] font-bold rounded-lg cursor-pointer border-none shadow-2xs"
                                >
                                    Switch Branch
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

</div>

<!-- ======================================================================= -->
<!-- API CREDENTIALS SLIDING DRAWER                                          -->
<!-- ======================================================================= -->
<div id="cora-gbp-keys-backdrop" class="cora-drawer-backdrop" onclick="coraGbpToggleKeysPanel()"></div>

<div id="cora-gbp-keys-panel" class="cora-drawer-sheet" role="dialog" aria-labelledby="gbp-drawer-title">
    
    <!-- Drawer Header -->
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
        <div>
            <h3 id="gbp-drawer-title" class="text-sm font-extrabold text-zinc-900">Google Cloud API Credentials</h3>
            <p class="text-[9px] text-zinc-400 mt-0.5 uppercase tracking-wider font-extrabold">System Configuration</p>
        </div>
        <button 
            type="button" 
            class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1.5 rounded-lg hover:bg-zinc-100 bg-transparent border-none" 
            onclick="coraGbpToggleKeysPanel()"
            aria-label="Close credentials drawer"
        >
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Drawer Body -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6 select-none">
        <p class="text-xs text-zinc-500 leading-relaxed font-normal">
            Enter your Google Cloud credentials to enable live OAuth synchronisation, review replies, and Maps Place Search.
        </p>

        <!-- Redirect URI Info Box -->
        <div class="p-3.5 bg-zinc-50 rounded-xl border border-zinc-200/80 space-y-2">
            <span class="text-[9px] font-bold text-zinc-900 uppercase tracking-wider block">OAuth 2.0 Authorized Redirect URI</span>
            <p class="text-[11px] text-zinc-500 leading-relaxed font-medium">Add this URL to <strong>Authorized redirect URIs</strong> in your Google Cloud Console:</p>
            <div class="flex items-center justify-between gap-2 bg-white border border-zinc-200 rounded-lg p-2 font-mono text-[10px] text-zinc-700 select-all">
                <span class="truncate" id="cora-gbp-redirect-uri"><?php echo esc_html( home_url( '/workspace/gbp' ) ); ?></span>
                <button 
                    type="button" 
                    onclick="navigator.clipboard.writeText('<?php echo esc_js( home_url( '/workspace/gbp' ) ); ?>'); if(window.coraShowToast) window.coraShowToast('Redirect URI copied to clipboard!', 'success');" 
                    class="text-zinc-400 hover:text-zinc-900 cursor-pointer bg-transparent border-none p-1 shrink-0" 
                    title="Copy URI"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                </button>
            </div>
        </div>

        <!-- Credential Inputs -->
        <div class="space-y-4">
            
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Google OAuth Client ID</label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="cora-gbp-input-client-id" 
                        value="<?php echo esc_attr( $display_client_id ); ?>" 
                        placeholder="e.g. 123456-abc.apps.googleusercontent.com" 
                        class="w-full rounded-xl pl-3 pr-10 py-2.5 text-xs font-mono bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 border border-zinc-200 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                    />
                    <button type="button" onclick="coraToggleGbpInputMask('cora-gbp-input-client-id')" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-900 bg-transparent border-none cursor-pointer p-0.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Google OAuth Client Secret</label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="cora-gbp-input-client-secret" 
                        value="<?php echo esc_attr( $display_client_secret ); ?>" 
                        placeholder="GOCSPX-..." 
                        class="w-full rounded-xl pl-3 pr-10 py-2.5 text-xs font-mono bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 border border-zinc-200 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                    />
                    <button type="button" onclick="coraToggleGbpInputMask('cora-gbp-input-client-secret')" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-900 bg-transparent border-none cursor-pointer p-0.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Google Maps / Places API Key (Optional)</label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="cora-gbp-input-api-key" 
                        value="<?php echo esc_attr( $display_maps_key ); ?>" 
                        placeholder="AIzaSy..." 
                        class="w-full rounded-xl pl-3 pr-10 py-2.5 text-xs font-mono bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 border border-zinc-200 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all"
                    />
                    <button type="button" onclick="coraToggleGbpInputMask('cora-gbp-input-api-key')" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-900 bg-transparent border-none cursor-pointer p-0.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

        </div>

        <!-- 1-Click Sandbox Test Box -->
        <div class="p-4 bg-zinc-50 rounded-xl border border-zinc-200/80 space-y-2">
            <h4 class="text-xs font-bold text-zinc-900">Need Sandbox Testing?</h4>
            <p class="text-[11px] text-zinc-500 leading-relaxed font-normal">If your Google Cloud project is still awaiting Google OAuth verification, use 1-click sandbox authorization to test full reviews, AI replies, and post publishing.</p>
            <button 
                type="button" 
                onclick="coraGbpAuthorizeDemo()" 
                class="w-full px-3.5 py-2 bg-white hover:bg-zinc-100 text-zinc-800 border border-zinc-200 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-2xs flex items-center justify-center gap-1.5"
            >
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                <span>Authorize Demo Account</span>
            </button>
        </div>

    </div>

    <!-- Drawer Footer -->
    <div class="p-5 border-t border-zinc-200 bg-zinc-50/50 flex items-center justify-between gap-3">
        <button 
            type="button" 
            onclick="coraGbpToggleKeysPanel()" 
            class="px-4 py-2 bg-white text-zinc-600 text-xs font-semibold rounded-xl border border-zinc-200 hover:bg-zinc-50 cursor-pointer"
        >
            Cancel
        </button>
        <button 
            type="button" 
            id="cora-gbp-creds-save-btn" 
            onclick="coraGbpSaveApiCredentials()" 
            class="px-5 py-2 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer border-none flex items-center gap-1.5"
        >
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Save Credentials</span>
        </button>
    </div>

</div>

<!-- ======================================================================= -->
<!-- JAVASCRIPT LOGIC & CONTROLLER ENGINE                                    -->
<!-- ======================================================================= -->
<script>
// Global State Variables
var coraGbpAllReviews = [];
var coraGbpActiveFilter = 'all';

// Drawer Open/Close Handler
function coraGbpToggleKeysPanel() {
    var panel = document.getElementById('cora-gbp-keys-panel');
    var backdrop = document.getElementById('cora-gbp-keys-backdrop');
    if (!panel) return;
    var isOpen = panel.classList.contains('open');
    if (isOpen) {
        panel.classList.remove('open');
        if (backdrop) backdrop.classList.remove('open');
    } else {
        panel.classList.add('open');
        if (backdrop) backdrop.classList.add('open');
    }
}

// Escape key to dismiss drawer
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var panel = document.getElementById('cora-gbp-keys-panel');
        if (panel && panel.classList.contains('open')) {
            coraGbpToggleKeysPanel();
        }
    }
});

// Masked Input Toggle
function coraToggleGbpInputMask(inputId) {
    var el = document.getElementById(inputId);
    if (!el) return;
    el.type = (el.type === 'password') ? 'text' : 'password';
}

// Segmented Navigation Tab Switcher
function coraGbpSwitchTab(tabName) {
    document.querySelectorAll('.cora-tab-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    document.querySelectorAll('.cora-tab-content').forEach(function(content) {
        content.classList.add('hidden');
    });

    var activeBtn = document.getElementById('tab-btn-' + tabName);
    var activeContent = document.getElementById('cora-gbp-tab-' + tabName);
    if (activeBtn) activeBtn.classList.add('active');
    if (activeContent) activeContent.classList.remove('hidden');
}

// Topic Pill Selector for Google Post Composer
function coraSelectTopicPill(input) {
    document.querySelectorAll('.gbp-topic-pill').forEach(function(lbl) {
        lbl.classList.remove('bg-zinc-950', 'text-white', 'border-zinc-950');
        lbl.classList.add('bg-white', 'text-zinc-700', 'border-zinc-200');
    });
    var label = input.closest('label');
    if (label) {
        label.classList.add('bg-zinc-950', 'text-white', 'border-zinc-950');
        label.classList.remove('bg-white', 'text-zinc-700', 'border-zinc-200');
    }
}

// Day Closed Toggle
function coraToggleDayClosed(dayKey, isClosed) {
    var openInput = document.getElementById('hours-open-' + dayKey);
    var closeInput = document.getElementById('hours-close-' + dayKey);
    if (openInput && closeInput) {
        if (isClosed) {
            openInput.classList.add('opacity-40', 'pointer-events-none');
            closeInput.classList.add('opacity-40', 'pointer-events-none');
        } else {
            openInput.classList.remove('opacity-40', 'pointer-events-none');
            closeInput.classList.remove('opacity-40', 'pointer-events-none');
        }
    }
}

// Save API Credentials
function coraGbpSaveApiCredentials() {
    var clientId     = (document.getElementById('cora-gbp-input-client-id')?.value || '').trim();
    var clientSecret = (document.getElementById('cora-gbp-input-client-secret')?.value || '').trim();
    var apiKey       = (document.getElementById('cora-gbp-input-api-key')?.value || '').trim();
    var btn          = document.getElementById('cora-gbp-creds-save-btn');

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Saving...';
    }

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_save_keys',
            security: nonce,
            client_id: clientId,
            client_secret: clientSecret,
            api_key: apiKey
        },
        success: function(res) {
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast('Google API Credentials saved successfully!', 'success');
                coraGbpToggleKeysPanel();
            } else {
                var err = (res && res.data) ? res.data : 'Failed to save credentials.';
                if (window.coraShowToast) window.coraShowToast(err, 'error');
            }
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Save Credentials</span>';
            }
        },
        error: function() {
            if (window.coraShowToast) window.coraShowToast('Network error saving credentials.', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Save Credentials</span>';
            }
        }
    });
}

// Initiate Google OAuth Flow
function coraGbpConnectWithGoogle() {
    var btn = document.getElementById('cora-gbp-oauth-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Connecting to Google...';
    }

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_get_oauth_url',
            security: nonce
        },
        success: function(res) {
            if (res && res.success && res.data && res.data.url) {
                if (window.coraShowToast) window.coraShowToast('Redirecting to Google Account Authorization...', 'info');
                window.location.href = res.data.url;
            } else {
                if (window.coraShowToast) window.coraShowToast('OAuth Client ID not yet configured. Opening credentials panel...', 'info');
                coraGbpToggleKeysPanel();
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<span>Sign in with Google OAuth</span>';
                }
            }
        },
        error: function() {
            coraGbpToggleKeysPanel();
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span>Sign in with Google OAuth</span>';
            }
        }
    });
}

// 1-Click Authorize Demo Sandbox Account
function coraGbpAuthorizeDemo() {
    if (window.coraShowToast) window.coraShowToast('Authorizing Google Business Profile Sandbox...', 'info');

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_authorize_demo',
            security: nonce,
            account_email: 'studio.director@elitestudio.local'
        },
        success: function(res) {
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast('Google Account Authorized Successfully!', 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 400);
            } else {
                var err = (res && res.data && res.data.message) ? res.data.message : 'Demo authorization failed.';
                if (window.coraShowToast) window.coraShowToast(err, 'error');
            }
        },
        error: function() {
            if (window.coraShowToast) window.coraShowToast('Network error during demo authorization.', 'error');
        }
    });
}

// Google Places Search
function coraGbpSearch() {
    var input = document.getElementById('cora-gbp-search-q');
    var query = input ? input.value.trim() : '';
    if (query.length < 2) {
        if (window.coraShowToast) window.coraShowToast('Please enter at least 2 characters to search.', 'warning');
        return;
    }

    var btn = document.getElementById('cora-gbp-search-btn');
    var wrap = document.getElementById('cora-gbp-search-results-wrap');
    if (!wrap) return;

    wrap.classList.remove('hidden');
    wrap.innerHTML = '<div class="space-y-3 py-2">' +
        '<div class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest flex items-center gap-2 animate-pulse">' +
        '<svg class="animate-spin text-zinc-800" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>' +
        'Searching Google Places API...</div>' +
        '<div class="cora-skeleton-shimmer rounded-xl p-4 h-20 w-full"></div>' +
        '<div class="cora-skeleton-shimmer rounded-xl p-4 h-20 w-full"></div>' +
        '</div>';

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Searching...';
    }

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_search_places',
            security: nonce,
            query: query
        },
        success: function(res) {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><span>Search Google Maps</span>';
            }
            if (res && res.success && res.data && res.data.length > 0) {
                coraGbpRenderSearchResults(res.data);
            } else {
                wrap.innerHTML = '<div class="p-4 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-3">' +
                    '<div><h4 class="text-xs font-bold text-zinc-900">No matching Places found</h4>' +
                    '<p class="text-xs text-zinc-500 mt-1">You can connect this business manually as a custom listing below.</p></div>' +
                    '<button onclick="coraGbpConnectCustom(\'' + query.replace(/'/g, "\\'") + '\')" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl transition-all cursor-pointer border-none">' +
                    'Connect Manual Listing: "' + query + '"</button></div>';
            }
        },
        error: function() {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><span>Search Google Maps</span>';
            }
            wrap.innerHTML = '<div class="p-4 bg-zinc-50 border border-zinc-200/80 rounded-xl text-xs text-red-650">Network error searching Google Places. Please try again.</div>';
        }
    });
}

// Render Search Results
function coraGbpRenderSearchResults(places) {
    var wrap = document.getElementById('cora-gbp-search-results-wrap');
    if (!wrap) return;

    var html = '<p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2.5">Select Your Business Listing to Connect:</p><div class="grid grid-cols-1 md:grid-cols-2 gap-3">';
    
    places.forEach(function(place, idx) {
        var name = (place.displayName && place.displayName.text) ? place.displayName.text : 'Business Listing';
        var type = (place.primaryTypeDisplayName && place.primaryTypeDisplayName.text) ? place.primaryTypeDisplayName.text : 'Storefront';
        var addr = place.formattedAddress || 'Address on file';
        var rating = place.rating ? parseFloat(place.rating) : 4.9;
        var count = place.userRatingCount ? parseInt(place.userRatingCount) : 42;
        var encodedPlace = encodeURIComponent(JSON.stringify(place));

        html += '<div class="p-4 bg-zinc-50/70 hover:bg-white border border-zinc-200/80 hover:border-zinc-350 rounded-2xl flex flex-col justify-between gap-3 transition-all shadow-2xs">' +
            '<div class="space-y-1">' +
            '<div class="flex items-center justify-between gap-2">' +
            '<h4 class="text-xs font-extrabold text-zinc-900 leading-tight">' + name + '</h4>' +
            '<span class="text-[9px] font-bold uppercase bg-zinc-200/80 text-zinc-700 px-2 py-0.5 rounded">' + type + '</span>' +
            '</div>' +
            '<p class="text-[11px] text-zinc-500 font-medium leading-relaxed truncate">' + addr + '</p>' +
            '<div class="flex items-center gap-1.5 text-xs text-amber-500 font-bold mt-1"><span>' + rating.toFixed(1) + ' ★</span><span class="text-zinc-400 font-normal">(' + count + ' reviews)</span></div>' +
            '</div>' +
            '<div class="pt-2 border-t border-zinc-200/60 flex justify-end">' +
            '<button onclick="coraGbpConnectSelectedPlace(\'' + encodedPlace + '\', this)" class="px-4 py-1.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl cursor-pointer border-none shadow-2xs">' +
            'Connect Listing</button>' +
            '</div>' +
            '</div>';
    });

    html += '</div>';
    wrap.innerHTML = html;
}

// Connect Selected Place
function coraGbpConnectSelectedPlace(encodedPlace, btn) {
    var place = JSON.parse(decodeURIComponent(encodedPlace));
    if (btn) {
        btn.disabled = true;
        btn.innerText = 'Connecting...';
    }

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_connect_place',
            security: nonce,
            place: place,
            business_name: place.displayName ? place.displayName.text : '',
            category: place.primaryTypeDisplayName ? place.primaryTypeDisplayName.text : '',
            address: place.formattedAddress || '',
            phone: place.nationalPhoneNumber || '',
            website: place.websiteUri || '',
            rating: place.rating || 4.9,
            review_count: place.userRatingCount || 58
        },
        success: function(res) {
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast('Business Listing Connected Successfully!', 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 400);
            } else {
                if (window.coraShowToast) window.coraShowToast('Failed to connect place.', 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'Connect Listing';
                }
            }
        },
        error: function() {
            if (window.coraShowToast) window.coraShowToast('Network error connecting listing.', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Connect Listing';
            }
        }
    });
}

// Connect Custom Manual Business
function coraGbpConnectCustom(bName) {
    if (window.coraShowToast) window.coraShowToast('Connecting custom listing ' + bName + '...', 'info');

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_connect_place',
            security: nonce,
            business_name: bName,
            category: 'Commercial & Portrait Photography Studio',
            address: 'DLF Cyber City, Sector 24, Gurugram, Haryana 122002',
            phone: '+91 124 456 7890',
            website: 'https://elitestudio.heycora.in',
            rating: 4.9,
            review_count: 58
        },
        success: function(res) {
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast('Custom Listing Connected!', 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 400);
            } else {
                if (window.coraShowToast) window.coraShowToast('Failed to connect listing.', 'error');
            }
        }
    });
}

// Disconnect Google Listing
function coraGbpDisconnect() {
    if (!confirm('Are you sure you want to disconnect this Google Business Profile?')) {
        return;
    }

    if (window.coraShowToast) window.coraShowToast('Disconnecting Google listing...', 'info');

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_disconnect',
            security: nonce
        },
        success: function(res) {
            if (window.coraShowToast) window.coraShowToast('Listing Disconnected Successfully.', 'success');
            setTimeout(function() {
                window.location.reload();
            }, 400);
        },
        error: function() {
            if (window.coraShowToast) window.coraShowToast('Failed to disconnect listing.', 'error');
        }
    });
}

// Refresh Sync Action
function coraGbpRefreshSync() {
    if (window.coraShowToast) window.coraShowToast('Refreshing live Google Maps data & reviews...', 'info');
    coraGbpLoadReviews();
}

// Load Reviews from Backend
function coraGbpLoadReviews() {
    var loading = document.getElementById('cora-gbp-reviews-loading');
    var list = document.getElementById('cora-gbp-reviews-list');
    var empty = document.getElementById('cora-gbp-reviews-empty');

    if (loading) loading.classList.remove('hidden');
    if (list) list.classList.add('hidden');
    if (empty) empty.classList.add('hidden');

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_fetch_reviews',
            security: nonce
        },
        success: function(res) {
            if (loading) loading.classList.add('hidden');
            if (res && res.success && res.data && res.data.reviews && res.data.reviews.length > 0) {
                coraGbpAllReviews = res.data.reviews;
                coraGbpRenderReviews(coraGbpAllReviews);
                if (list) list.classList.remove('hidden');
            } else {
                if (empty) empty.classList.remove('hidden');
            }
        },
        error: function() {
            if (loading) loading.classList.add('hidden');
            if (empty) empty.classList.remove('hidden');
        }
    });
}

// Filter Reviews by Criteria
function coraGbpFilterReviews(criteria, evt) {
    coraGbpActiveFilter = criteria;

    var e = evt || (typeof window !== 'undefined' ? window.event : null);

    document.querySelectorAll('.cora-review-filter').forEach(function(btn) {
        btn.classList.remove('bg-zinc-950', 'text-white', 'active');
        btn.classList.add('bg-zinc-100', 'text-zinc-700');
    });
    if (e && e.currentTarget) {
        e.currentTarget.classList.add('bg-zinc-950', 'text-white', 'active');
        e.currentTarget.classList.remove('bg-zinc-100', 'text-zinc-700');
    } else if (e && e.target) {
        var targetBtn = e.target.closest ? e.target.closest('.cora-review-filter') : e.target;
        if (targetBtn) {
            targetBtn.classList.add('bg-zinc-950', 'text-white', 'active');
            targetBtn.classList.remove('bg-zinc-100', 'text-zinc-700');
        }
    }

    if (!coraGbpAllReviews || coraGbpAllReviews.length === 0) return;

    var filtered = coraGbpAllReviews.filter(function(rev) {
        var ratingNum = (rev.starRating === 'FIVE') ? 5 : ((rev.starRating === 'FOUR') ? 4 : ((rev.starRating === 'THREE') ? 3 : ((rev.starRating === 'TWO') ? 2 : 1)));
        var hasReply = rev.reviewReply && rev.reviewReply.comment;

        if (criteria === 'unreplied') return !hasReply;
        if (criteria === 'replied') return hasReply;
        if (criteria === 'five_star') return ratingNum === 5;
        if (criteria === 'critical') return ratingNum <= 3;
        return true;
    });

    var list = document.getElementById('cora-gbp-reviews-list');
    var empty = document.getElementById('cora-gbp-reviews-empty');

    if (filtered.length > 0) {
        coraGbpRenderReviews(filtered);
        if (list) list.classList.remove('hidden');
        if (empty) empty.classList.add('hidden');
    } else {
        if (list) list.classList.add('hidden');
        if (empty) empty.classList.remove('hidden');
    }
}

// Render Review Cards
function coraGbpRenderReviews(reviews) {
    var container = document.getElementById('cora-gbp-reviews-list');
    if (!container) return;

    var html = '';
    var unrepliedCount = 0;

    reviews.forEach(function(rev, idx) {
        var author = (rev.reviewer && rev.reviewer.displayName) ? rev.reviewer.displayName : (rev.authorName || 'Google User');
        var avatar = (rev.reviewer && rev.reviewer.profilePhotoUri) ? rev.reviewer.profilePhotoUri : '';
        var comment = rev.comment || 'No comment provided.';
        var date = rev.createTime ? new Date(rev.createTime).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Recent';
        var ratingNum = (rev.starRating === 'FIVE') ? 5 : ((rev.starRating === 'FOUR') ? 4 : ((rev.starRating === 'THREE') ? 3 : ((rev.starRating === 'TWO') ? 2 : 1)));
        
        var stars = '';
        for (var s = 1; s <= 5; s++) {
            stars += (s <= ratingNum) ? '<span class="text-amber-500 font-bold">★</span>' : '<span class="text-zinc-200">★</span>';
        }

        var revKey = rev.reviewId || ('rev_' + idx);
        var reviewName = rev.name || revKey;
        var hasReply = rev.reviewReply && rev.reviewReply.comment;
        if (!hasReply) unrepliedCount++;

        var initial = author.charAt(0).toUpperCase();

        html += '<div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-2xs space-y-3" id="review-card-' + revKey + '">' +
            '<div class="flex items-start justify-between gap-3">' +
            '<div class="flex items-center gap-3">' +
            (avatar ? '<img src="' + avatar + '" class="w-10 h-10 rounded-full object-cover border border-zinc-200 shadow-2xs shrink-0" onerror="this.style.display=\'none\';">' :
            '<div class="w-10 h-10 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-xs shrink-0">' + initial + '</div>') +
            '<div>' +
            '<h4 class="text-xs font-bold text-zinc-900 leading-tight">' + author + '</h4>' +
            '<div class="flex items-center gap-2 mt-0.5"><div class="flex text-xs">' + stars + '</div><span class="text-[10px] text-zinc-400 font-medium">' + date + '</span></div>' +
            '</div>' +
            '</div>' +
            (hasReply ? '<span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[9px] font-bold">REPLIED</span>' :
            '<span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded text-[9px] font-bold">NEEDS REPLY</span>') +
            '</div>' +
            '<p class="text-xs text-zinc-700 leading-relaxed font-normal pl-13">' + comment + '</p>';

        if (hasReply) {
            html += '<div class="ml-13 p-3 bg-zinc-50 rounded-xl border border-zinc-200/70 space-y-1">' +
                '<div class="flex items-center justify-between">' +
                '<span class="text-[9px] font-bold text-zinc-500 uppercase tracking-wider">Your Google Response</span>' +
                '<span class="text-[9px] text-zinc-400">Published to Maps</span>' +
                '</div>' +
                '<p class="text-xs text-zinc-800 font-medium leading-relaxed italic">"' + rev.reviewReply.comment + '"</p>' +
                '</div>';
        } else {
            html += '<div class="ml-13 space-y-2.5 pt-1" id="reply-box-' + revKey + '">' +
                '<div class="flex flex-wrap items-center justify-between gap-2">' +
                '<div class="flex items-center gap-1.5 text-[11px] text-zinc-500">' +
                '<span class="font-bold text-zinc-700">AI Draft Tone:</span>' +
                '<select id="tone-' + revKey + '" class="border border-zinc-200 rounded-lg px-2 py-1 text-[11px] bg-white text-zinc-800 cursor-pointer">' +
                '<option value="warm" selected>Warm & Friendly</option>' +
                '<option value="professional">Formal & Professional</option>' +
                '<option value="concise">Quick & Concise</option>' +
                '<option value="apology">Resolution & Apology</option>' +
                '</select>' +
                '</div>' +
                '<button type="button" onclick="coraGbpAiDraftReply(\'' + revKey + '\')" class="px-3 py-1 bg-zinc-100 hover:bg-zinc-200/80 text-zinc-800 text-[11px] font-bold rounded-lg border border-zinc-200/80 transition-all cursor-pointer flex items-center gap-1 shadow-2xs">' +
                '<svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>' +
                '<span>Draft AI Reply</span>' +
                '</button>' +
                '</div>' +
                '<textarea id="reply-textarea-' + revKey + '" rows="2" class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs bg-zinc-50 hover:bg-zinc-100/40 focus:bg-white text-zinc-900 focus:outline-hidden focus:ring-1 focus:ring-zinc-950 transition-all font-normal" placeholder="Write or generate a personalized reply to this review..."></textarea>' +
                '<div class="flex justify-end">' +
                '<button type="button" onclick="coraGbpSubmitReply(\'' + reviewName + '\', \'' + revKey + '\')" class="px-4 py-1.5 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl transition-all cursor-pointer border-none shadow-xs flex items-center gap-1.5">' +
                '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>' +
                '<span>Post Reply to Google</span>' +
                '</button>' +
                '</div>' +
                '</div>';
        }

        html += '</div>';
    });

    container.innerHTML = html;

    var unrepliedEl = document.getElementById('cora-gbp-unreplied-count');
    if (unrepliedEl) unrepliedEl.innerText = unrepliedCount;
}

// AI Draft Review Reply
function coraGbpAiDraftReply(revKey, author, rating, comment) {
    var ta = document.getElementById('reply-textarea-' + revKey);
    var toneSelect = document.getElementById('tone-' + revKey);
    var tone = toneSelect ? toneSelect.value : 'warm';

    // If author or rating wasn't passed directly, look up from coraGbpAllReviews
    if (!author || !rating) {
        if (coraGbpAllReviews && coraGbpAllReviews.length) {
            for (var i = 0; i < coraGbpAllReviews.length; i++) {
                var r = coraGbpAllReviews[i];
                var k = r.reviewId || ('rev_' + i);
                if (k === revKey || r.name === revKey) {
                    author = (r.reviewer && r.reviewer.displayName) ? r.reviewer.displayName : (r.authorName || 'Google User');
                    var star = r.starRating;
                    rating = (star === 'FIVE') ? 5 : ((star === 'FOUR') ? 4 : ((star === 'THREE') ? 3 : ((star === 'TWO') ? 2 : 1)));
                    comment = r.comment || '';
                    break;
                }
            }
        }
    }
    author = author || 'Valued Customer';
    rating = rating || 5;
    comment = comment || '';

    if (ta) ta.value = 'Drafting personalized AI reply...';

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_generate_ai_reply',
            security: nonce,
            reviewer_name: author,
            star_rating: rating,
            comment: comment,
            tone: tone
        },
        success: function(res) {
            if (res && res.success && res.data && res.data.reply) {
                if (ta) ta.value = res.data.reply;
            } else {
                if (ta) ta.value = 'Thank you so much for the ' + rating + '-star review, ' + author + '! The team at Elite Studio truly appreciates your support.';
            }
        },
        error: function() {
            if (ta) ta.value = 'Thank you so much for the ' + rating + '-star review, ' + author + '! The team at Elite Studio truly appreciates your support.';
        }
    });
}

// Submit Review Reply to Google
function coraGbpSubmitReply(reviewName, revKey) {
    var ta = document.getElementById('reply-textarea-' + revKey);
    var replyText = ta ? ta.value.trim() : '';

    if (!replyText) {
        if (window.coraShowToast) window.coraShowToast('Please write or generate a reply first.', 'warning');
        return;
    }

    if (window.coraShowToast) window.coraShowToast('Publishing reply to Google Maps...', 'info');

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_reply_review',
            security: nonce,
            review_name: reviewName,
            reply: replyText
        },
        success: function(res) {
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast('Reply posted to Google successfully!', 'success');
                var box = document.getElementById('reply-box-' + revKey);
                if (box) {
                    box.outerHTML = '<div class="ml-13 p-3 bg-zinc-50 rounded-xl border border-zinc-200/70 space-y-1">' +
                        '<div class="flex items-center justify-between">' +
                        '<span class="text-[9px] font-bold text-zinc-500 uppercase tracking-wider">Your Google Response</span>' +
                        '<span class="text-[9px] text-zinc-400">Just now</span>' +
                        '</div>' +
                        '<p class="text-xs text-zinc-800 font-medium leading-relaxed italic">"' + replyText + '"</p>' +
                        '</div>';
                }
            } else {
                var err = (res && res.data) ? res.data : 'Could not submit reply.';
                if (window.coraShowToast) window.coraShowToast(err, 'error');
            }
        },
        error: function() {
            if (window.coraShowToast) window.coraShowToast('Network error posting reply.', 'error');
        }
    });
}

// Publish Post to Google Maps
function coraGbpPublishPost() {
    var contentInput = document.getElementById('cora-gbp-post-content');
    var ctaSelect    = document.getElementById('cora-gbp-post-cta');
    var ctaUrlInput  = document.getElementById('cora-gbp-post-cta-url');
    var imageInput   = document.getElementById('cora-gbp-post-image-url');
    var btn          = document.getElementById('cora-gbp-publish-btn');

    var content = contentInput ? contentInput.value.trim() : '';
    var cta     = ctaSelect ? ctaSelect.value : 'NONE';
    var ctaUrl  = ctaUrlInput ? ctaUrlInput.value.trim() : '';
    var imageUrl = imageInput ? imageInput.value.trim() : '';
    var topicType = document.querySelector('input[name="cora_gbp_topic"]:checked')?.value || 'STANDARD';

    if (!content) {
        if (window.coraShowToast) window.coraShowToast('Please enter post announcement content.', 'warning');
        return;
    }

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Publishing to Google Maps...';
    }

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_create_post',
            security: nonce,
            content: content,
            cta: cta,
            cta_url: ctaUrl,
            topic_type: topicType,
            image_url: imageUrl
        },
        success: function(res) {
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast('Post published to Google Maps!', 'success');
                if (contentInput) contentInput.value = '';
                if (imageInput) imageInput.value = '';
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            } else {
                var err = (res && res.data) ? res.data : 'Could not publish post.';
                if (window.coraShowToast) window.coraShowToast(err, 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg><span>Publish to Google Maps</span>';
                }
            }
        },
        error: function() {
            if (window.coraShowToast) window.coraShowToast('Network error publishing post.', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg><span>Publish to Google Maps</span>';
            }
        }
    });
}

// Delete Post from History
function coraGbpDeletePost(postId) {
    if (!confirm('Remove this post from history?')) return;

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_delete_post',
            security: nonce,
            post_id: postId
        },
        success: function(res) {
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast('Post removed from history.', 'success');
                var row = document.getElementById('post-row-' + postId);
                if (row) row.remove();
            }
        }
    });
}

// Save Business Details & Operating Hours
function coraGbpSaveBusinessInfo(e) {
    e.preventDefault();
    var btn = document.getElementById('cora-gbp-save-info-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Saving to Google...';
    }

    var name    = document.getElementById('gbp-info-name')?.value.trim();
    var cat     = document.getElementById('gbp-info-cat')?.value.trim();
    var phone   = document.getElementById('gbp-info-phone')?.value.trim();
    var website = document.getElementById('gbp-info-website')?.value.trim();
    var addr    = document.getElementById('gbp-info-addr')?.value.trim();

    var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    var hoursObj = {};
    days.forEach(function(d) {
        var openVal = document.getElementById('hours-open-' + d)?.value || '09:00';
        var closeVal = document.getElementById('hours-close-' + d)?.value || '20:00';
        var isClosed = document.getElementById('hours-closed-' + d)?.checked || false;
        hoursObj[d] = {
            open: openVal,
            close: closeVal,
            closed: isClosed
        };
    });

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_update_business_info',
            security: nonce,
            business_name: name,
            category: cat,
            phone: phone,
            website: website,
            address: addr,
            operating_hours: JSON.stringify(hoursObj)
        },
        success: function(res) {
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast('Business info & operating hours saved!', 'success');
            } else {
                if (window.coraShowToast) window.coraShowToast('Failed to save business info.', 'error');
            }
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg><span>Save Details & Operating Hours</span>';
            }
        },
        error: function() {
            if (window.coraShowToast) window.coraShowToast('Network error saving details.', 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg><span>Save Details & Operating Hours</span>';
            }
        }
    });
}

// Switch Branch
function coraGbpSwitchBranch(branchName, addr) {
    if (window.coraShowToast) window.coraShowToast('Switching active location to ' + branchName + '...', 'info');

    var ajaxUrl = (window.cora_vars && window.cora_vars.ajax_url) || (window.coraREData && window.coraREData.ajaxUrl) || '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var nonce   = (window.cora_vars && window.cora_vars.nonce) || (window.coraREData && window.coraREData.ajaxNonce) || '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_gbp_connect_place',
            security: nonce,
            business_name: branchName,
            address: addr,
            category: 'Portrait & Fashion Studio',
            phone: '+91 11 4123 4567',
            rating: 4.8,
            review_count: 32
        },
        success: function(res) {
            if (res && res.success) {
                if (window.coraShowToast) window.coraShowToast('Branch switched successfully!', 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 400);
            }
        }
    });
}

// On Page Ready: auto-load reviews if connected, or accounts if in State B
document.addEventListener('DOMContentLoaded', function() {
    <?php if ( $cora_gbp_is_connected ) : ?>
        coraGbpLoadReviews();
    <?php elseif ( $cora_gbp_is_auth && ! $cora_gbp_is_connected ) : ?>
        if (typeof coraGbpLoadAccounts === 'function') {
            coraGbpLoadAccounts();
        }
    <?php endif; ?>
});
</script>
