<?php
/**
 * Cora Workspace - Google Business Profile Manager
 * 
 * Production Google Business Profile Listing & Review Management View.
 * ZERO hardcoded business names, ZERO dummy reviews.
 * Allows searching/connecting your real business listing & configuring your Google API credentials.
 */

if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();
$role = ! empty( $user->roles ) ? $user->roles[0] : 'subscriber';
$is_admin = current_user_can('manage_options') || in_array( $role, array( 'administrator', 'cora_manager', 'super_admin', 'agency_owner' ) );

$cora_gbp_profile       = get_option( 'cora_gbp_profile', array() );
$cora_gbp_tokens        = get_option( 'cora_gbp_tokens', array() );
$cora_gbp_posts         = get_option( 'cora_gbp_posts', array() );
$cora_gbp_client_id     = function_exists('cora_gbp_get_client_id') ? cora_gbp_get_client_id() : get_option( 'cora_google_client_id', '' );
$cora_gbp_client_secret = function_exists('cora_gbp_get_client_secret') ? cora_gbp_get_client_secret() : get_option( 'cora_google_client_secret', '' );
$cora_google_maps_key   = function_exists('cora_gbp_get_maps_api_key') ? cora_gbp_get_maps_api_key() : get_option( 'cora_gbp_maps_api_key', '' );

$cora_gbp_is_connected  = ! empty( $cora_gbp_profile['business_name'] );
$cora_gbp_is_auth       = ! empty( $cora_gbp_tokens['access_token'] );

$gbp_name     = esc_html( $cora_gbp_profile['business_name'] ?? '' );
$gbp_cat      = esc_html( $cora_gbp_profile['category'] ?? '' );
$gbp_addr     = esc_html( $cora_gbp_profile['address'] ?? '' );
$gbp_phone    = esc_html( $cora_gbp_profile['phone'] ?? '' );
$gbp_website  = esc_url( $cora_gbp_profile['website'] ?? '' );
$gbp_rating   = floatval( $cora_gbp_profile['rating'] ?? 0 );
$gbp_reviews  = intval( $cora_gbp_profile['review_count'] ?? 0 );
$gbp_initials = !empty($gbp_name) ? strtoupper( mb_substr( $gbp_name, 0, 2 ) ) : 'GP';
?>

<style>
    .cora-skeleton {
        position: relative;
        overflow: hidden;
        background-color: #f4f4f5;
    }
    .dark .cora-skeleton {
        background-color: #27272a;
    }
    .cora-skeleton::after {
        content: "";
        position: absolute;
        inset: 0;
        transform: translateX(-100%);
        background: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.4) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        animation: coraSkelShimmer 1.6s infinite;
    }
    .dark .cora-skeleton::after {
        background: linear-gradient(
            90deg,
            rgba(39, 39, 42, 0) 0%,
            rgba(39, 39, 42, 0.5) 50%,
            rgba(39, 39, 42, 0) 100%
        );
    }
    @keyframes coraSkelShimmer {
        100% { transform: translateX(100%); }
    }
    .cora-input {
        background-color: #ffffff;
        border: 1px solid #e4e4e7;
        transition: all 0.2s ease;
    }
    .dark .cora-input {
        background-color: #18181b;
        border: 1px solid #27272a;
        color: #f4f4f5;
    }
    .cora-input:focus {
        border-color: #09090b;
        box-shadow: 0 0 0 2px rgba(9, 9, 11, 0.05);
    }
    .dark .cora-input:focus {
        border-color: #f4f4f5;
        box-shadow: 0 0 0 2px rgba(244, 244, 245, 0.1);
    }
    .cora-glass-card {
        background: #ffffff;
        border: 1px solid #e4e4e7;
        transition: all 0.25s ease;
    }
    .dark .cora-glass-card {
        background: #18181b;
        border: 1px solid #27272a;
    }
    .cora-glass-card:hover {
        border-color: #d4d4d8;
    }
    .dark .cora-glass-card:hover {
        border-color: #3f3f46;
    }

    /* Bulletproof SaaS Drawer Engine CSS */
    .cora-drawer-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        background-color: rgba(9, 9, 11, 0.25) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 999999 !important;
        display: flex !important;
        justify-content: flex-end !important;
        opacity: 0 !important;
        pointer-events: none !important;
        transition: opacity 0.3s ease !important;
    }

    .cora-drawer-overlay.drawer-open {
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    .cora-drawer-sheet {
        background-color: #ffffff !important;
        height: 100% !important;
        width: 100% !important;
        max-width: 460px !important;
        box-shadow: -10px 0 25px -5px rgba(0, 0, 0, 0.08), -8px 0 10px -6px rgba(0, 0, 0, 0.08) !important;
        display: flex !important;
        flex-direction: column !important;
        transform: translateX(100%) !important;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
        border-left: 1px solid #e4e4e7 !important;
    }

    .dark .cora-drawer-sheet {
        background-color: #18181b !important;
        border-left: 1px solid #27272a !important;
    }

    .cora-drawer-overlay.drawer-open .cora-drawer-sheet {
        transform: translateX(0) !important;
    }
</style>

<!-- PAGE HEADER -->
<div class="cora-page-header flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200/80 dark:border-zinc-800/80 pb-6 mb-6">
    <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-2xl bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 flex items-center justify-center text-xs font-bold shadow-2xs shrink-0">
            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
        </div>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Google Business Profile Broker Owner</h1>
                <span class="px-2.5 py-0.5 text-[9px] font-black bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 rounded-md uppercase tracking-wider">AI Marketing</span>
            </div>
            <p class="cora-section-desc text-xs font-medium text-zinc-500 mt-0.5">Manage your Google Maps listing, reply to live client reviews with AI assistance, and publish updates to Maps.</p>
        </div>
    </div>

    <!-- Header Actions -->
    <div class="flex items-center gap-3 font-extrabold">
        <?php if ( $is_admin ) : ?>
            <button onclick="coraGbpToggleKeysPanel()" class="px-3.5 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-xs rounded-xl transition-all shadow-xs flex items-center gap-2 cursor-pointer border-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                API Credentials
            </button>
        <?php endif; ?>
        
        <?php if ( $cora_gbp_is_connected ) : ?>
            <div class="flex items-center gap-2 bg-white dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700/80 px-3.5 py-2 rounded-xl shadow-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-zinc-900 dark:text-zinc-200"><?php echo $gbp_name; ?></span>
                <button onclick="coraGbpDisconnect()" class="ml-2 text-[10px] font-bold text-zinc-400 hover:text-red-650 transition-colors cursor-pointer bg-transparent border-0">Disconnect</button>
            </div>
        <?php else : ?>
            <div class="flex items-center gap-2 bg-zinc-100 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800/80 px-3.5 py-2 rounded-xl">
                <span class="w-2.5 h-2.5 rounded-full bg-zinc-400"></span>
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-450">Not Connected</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ( $is_admin ) : ?>
<!-- API CREDENTIALS CONFIGURATION PANEL (Right-Sliding Side Drawer) -->
<div id="cora-gbp-keys-panel" class="cora-drawer-overlay">
    <div class="cora-drawer-sheet animate-none" id="gbp-keys-drawer-card">
        <!-- Header -->
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-850/40">
            <div>
                <h3 class="text-sm font-extrabold text-zinc-900 dark:text-zinc-100">Google Cloud API Credentials</h3>
                <p class="text-[9px] text-zinc-450 mt-0.5 uppercase tracking-wider font-extrabold">System Config</p>
            </div>
            <button class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-105 cursor-pointer p-1 bg-transparent border-0" onclick="coraGbpToggleKeysPanel()">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 select-none">
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed font-medium">Enter your Google Cloud project credentials to enable live Google Business Profile sync and Places search. These keys are secured and applied globally.</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-extrabold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">Google OAuth Client ID</label>
                    <div class="relative">
                        <input type="password" id="cora-gbp-input-client-id" value="<?php echo esc_attr($cora_gbp_client_id); ?>" placeholder="e.g. 123456-abc.apps.googleusercontent.com" class="cora-input w-full rounded-xl pl-3 pr-10 py-2.5 text-xs font-mono focus:outline-none">
                        <button type="button" onclick="coraToggleGbpInputMask('cora-gbp-input-client-id')" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white bg-transparent border-0 cursor-pointer p-0.5">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-extrabold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">Google OAuth Client Secret</label>
                    <div class="relative">
                        <input type="password" id="cora-gbp-input-client-secret" value="<?php echo esc_attr($cora_gbp_client_secret); ?>" placeholder="GOCSPX-..." class="cora-input w-full rounded-xl pl-3 pr-10 py-2.5 text-xs font-mono focus:outline-none">
                        <button type="button" onclick="coraToggleGbpInputMask('cora-gbp-input-client-secret')" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white bg-transparent border-0 cursor-pointer p-0.5">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-extrabold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">Google Maps / Places API Key (Optional)</label>
                    <div class="relative">
                        <input type="password" id="cora-gbp-input-api-key" value="<?php echo esc_attr($cora_google_maps_key); ?>" placeholder="AIzaSy..." class="cora-input w-full rounded-xl pl-3 pr-10 py-2.5 text-xs font-mono focus:outline-none">
                        <button type="button" onclick="coraToggleGbpInputMask('cora-gbp-input-api-key')" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white bg-transparent border-0 cursor-pointer p-0.5">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="p-5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/40 flex items-center justify-end gap-3 font-extrabold">
            <button onclick="coraGbpToggleKeysPanel()" class="px-4 py-2 border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-850 hover:bg-zinc-50 dark:hover:bg-zinc-750 text-zinc-700 dark:text-zinc-300 rounded-lg text-xs transition-colors cursor-pointer border-0 shadow-xs">Cancel</button>
            <button onclick="coraGbpSaveApiKeys()" class="px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-extrabold rounded-xl transition-colors shadow-sm flex items-center gap-2 cursor-pointer border-0">Save Changes</button>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ( ! $cora_gbp_is_connected ) : ?>
<!-- ===== UNCONNECTED STATE: Search & Connect Your Real Business ===== -->
<div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl shadow-xs overflow-hidden max-w-2xl mx-auto my-8">
    <div class="p-8 text-center space-y-6">
        
        <div class="w-14 h-14 rounded-2xl bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 mx-auto flex items-center justify-center shadow-sm">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>

        <div class="space-y-2 max-w-md mx-auto">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-50">Connect Your Business Profile</h2>
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 leading-relaxed">Enter your exact business name below to connect your listing to Cora Workspace.</p>
        </div>

        <!-- Search Input Box -->
        <div class="max-w-lg mx-auto space-y-3">
            <div class="flex gap-2">
                <div class="flex-1 relative">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input
                        type="text"
                        id="cora-gbp-search-q"
                        placeholder="Enter your exact business name"
                        class="cora-input w-full pl-9 pr-4 py-3 text-xs font-medium rounded-xl focus:outline-none transition-all"
                        onkeydown="if(event.key==='Enter') coraGbpSearchRealBusiness()"
                        autocomplete="off"
                    >
                </div>
                <button id="cora-gbp-search-btn" onclick="coraGbpSearchRealBusiness()" class="px-5 py-3 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-extrabold rounded-xl transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer shadow-sm border-0">
                    Connect Listing
                </button>
            </div>
        </div>

        <!-- Search Results Panel (Container for dynamically loaded places) -->
        <div id="cora-gbp-search-results-wrap" class="pt-2 space-y-3 text-left"></div>

        <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-xs font-bold text-zinc-500">Connect via 1-Click Google OAuth:</span>
            <button onclick="coraGbpConnectPlatformGoogleOAuth()" class="px-5 py-2.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-150 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-xs font-extrabold rounded-xl transition-all shadow-xs flex items-center gap-2 cursor-pointer border-0">
                <svg viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Sign in with Google Account
            </button>
        </div>

    </div>
</div>

<?php else : ?>
<!-- ===== CONNECTED MANAGEMENT DASHBOARD ===== -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column (2 Cols): Reviews Inbox & Maps Publisher -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Google Reviews Inbox -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-55">Google Reviews Inbox</h3>
                    <?php if ($gbp_rating > 0) : ?>
                        <span class="text-xs bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-250 dark:border-emerald-900/35 px-2.5 py-0.5 rounded-full font-bold"><?php echo number_format($gbp_rating, 1); ?> ★ Rating</span>
                    <?php endif; ?>
                </div>
                <button onclick="coraGbpLoadReviews()" class="text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-xl px-3.5 py-2 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Refresh Reviews
                </button>
            </div>

            <!-- Custom Skeleton Loading State (Instead of generic spinner) -->
            <div id="cora-gbp-reviews-loading" class="space-y-4 py-4">
                <div class="flex items-center gap-2 text-zinc-400 mb-2">
                    <svg class="animate-spin" viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Syncing review stream...</span>
                </div>
                <?php for ($s = 0; $s < 3; $s++) : ?>
                    <div class="space-y-3 py-3 border-b border-zinc-100 dark:border-zinc-800/40 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="cora-skeleton w-10 h-10 rounded-full"></div>
                            <div class="space-y-1.5 flex-1">
                                <div class="cora-skeleton h-3 w-1/4 rounded"></div>
                                <div class="cora-skeleton h-2.5 w-1/3 rounded"></div>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <div class="cora-skeleton h-2.5 w-full rounded"></div>
                            <div class="cora-skeleton h-2.5 w-5/6 rounded"></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Live Reviews List Feed -->
            <div id="cora-gbp-reviews-list" class="space-y-4 divide-y divide-zinc-100 dark:divide-zinc-800 hidden"></div>

            <!-- Empty Reviews State -->
            <div id="cora-gbp-reviews-empty" class="hidden py-8 flex flex-col items-center gap-2 text-center">
                <svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="1.4" fill="none" class="text-zinc-300"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <div>
                    <h4 class="text-xs font-bold text-zinc-850 dark:text-zinc-200">No reviews retrieved for <?php echo $gbp_name; ?></h4>
                    <p class="text-xs text-zinc-400 mt-0.5 max-w-sm mx-auto font-medium">Connect your Google OAuth Client ID using the credentials panel to authorize live review synchronisation.</p>
                </div>
            </div>
        </div>

        <!-- Google Maps Post & Offer Publisher -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-55">Publish to Google Maps</h3>
                <p class="text-xs text-zinc-500 mt-0.5 font-medium">Posts appear on your Google Business listing within minutes.</p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-550 uppercase tracking-wider block mb-1.5">Post Content &amp; Announcement *</label>
                    <textarea id="cora-gbp-post-content" rows="3" class="cora-input w-full rounded-xl p-3 text-xs font-medium focus:outline-none" placeholder="Share a recent update, announcements, or seasonal offer..."></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-555 uppercase tracking-wider block mb-1.5">Call to Action Button</label>
                        <select id="cora-gbp-post-cta" class="cora-input w-full rounded-xl p-2.5 text-xs font-bold focus:outline-none text-zinc-800 cursor-pointer">
                            <option value="NONE">None</option>
                            <option value="BOOK_NOW" selected>Book Now</option>
                            <option value="LEARN_MORE">Learn More</option>
                            <option value="CALL_NOW">Call Now</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-505 uppercase tracking-wider block mb-1.5">CTA Link URL</label>
                        <input type="url" id="cora-gbp-post-cta-url" class="cora-input w-full rounded-xl p-2.5 text-xs font-medium focus:outline-none" placeholder="https://yourwebsite.com">
                    </div>
                </div>
            </div>
            <div class="flex justify-end border-t border-zinc-100 dark:border-zinc-800 pt-4">
                <button id="cora-gbp-publish-btn" onclick="coraGbpPublishPost()" class="px-6 py-2.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-extrabold rounded-xl transition-colors flex items-center gap-2 cursor-pointer shadow-sm border-0">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Publish Post to Google Maps
                </button>
            </div>
        </div>

        <?php if (!empty($cora_gbp_posts)): ?>
        <!-- Published Posts History -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl p-6 shadow-xs space-y-3">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 border-b border-zinc-100 dark:border-zinc-800 pb-3 uppercase tracking-wider text-[11px] text-zinc-400">Published Posts History</h3>
            <div class="space-y-2">
                <?php foreach (array_slice($cora_gbp_posts, 0, 5) as $gbp_post): ?>
                <div class="flex items-start justify-between gap-3 py-2.5 border-b border-zinc-50 dark:border-zinc-800 last:border-0">
                    <p class="text-xs text-zinc-700 dark:text-zinc-300 font-medium leading-relaxed flex-1"><?php echo esc_html($gbp_post['content'] ?? ''); ?></p>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-emerald-50 dark:bg-emerald-955 text-emerald-700 dark:text-emerald-400 border border-emerald-250 dark:border-emerald-900/30 shrink-0">Published</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Right Column (1 Col): Connected Listing Profile Card -->
    <div class="space-y-6">
        
        <!-- Connected Business Card -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 uppercase tracking-wider text-[11px] text-zinc-400">Connected Listing Profile</h3>
                <span class="text-[10px] bg-emerald-50 dark:bg-emerald-955 text-emerald-700 dark:text-emerald-400 border border-emerald-250 dark:border-emerald-900/30 px-2 py-0.5 rounded-full font-bold">ACTIVE</span>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 flex items-center justify-center text-sm font-extrabold shadow-xs shrink-0"><?php echo $gbp_initials; ?></div>
                    <div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 leading-tight"><?php echo $gbp_name; ?></h4>
                        <?php if ($gbp_cat) : ?><p class="text-xs text-zinc-555 font-medium mt-0.5"><?php echo $gbp_cat; ?></p><?php endif; ?>
                    </div>
                </div>

                <?php if ($gbp_rating > 0) : ?>
                <div class="flex items-center gap-2 p-3 bg-amber-50/50 dark:bg-amber-955/15 rounded-xl border border-amber-255/60 dark:border-amber-900/25">
                    <span class="text-amber-500 text-sm font-bold"><?php echo number_format($gbp_rating, 1); ?> ★</span>
                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-250"><?php echo number_format($gbp_reviews); ?> Google Reviews</span>
                </div>
                <?php endif; ?>

                <div class="space-y-2.5 text-xs text-zinc-700 dark:text-zinc-350">
                    <?php if ($gbp_addr) : ?>
                    <div>
                        <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-0.5">Address</span>
                        <p class="font-medium leading-relaxed text-zinc-650 dark:text-zinc-300"><?php echo $gbp_addr; ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($gbp_phone) : ?>
                    <div>
                        <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-0.5">Phone</span>
                        <p class="font-bold text-zinc-900 dark:text-zinc-100"><?php echo $gbp_phone; ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($gbp_website) : ?>
                    <div>
                        <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-0.5">Website</span>
                        <a href="<?php echo $gbp_website; ?>" target="_blank" class="font-bold text-blue-600 dark:text-blue-400 hover:underline truncate block"><?php echo $gbp_website; ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                <button onclick="coraGbpDisconnect()" class="text-[11px] font-bold text-zinc-400 hover:text-red-655 transition-colors cursor-pointer bg-transparent border-0">
                    Disconnect listing
                </button>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>

<script>
window.coraGbpToggleKeysPanel = function() {
    $('#cora-gbp-keys-panel').toggleClass('drawer-open');
};

window.coraToggleGbpInputMask = function(inputId) {
    var el = $('#' + inputId);
    if (el.attr('type') === 'password') {
        el.attr('type', 'text');
    } else {
        el.attr('type', 'password');
    }
};

window.coraGbpSaveApiKeys = function() {
    var cId = $('#cora-gbp-input-client-id').val().trim();
    var cSec = $('#cora-gbp-input-client-secret').val().trim();
    var apiKey = $('#cora-gbp-input-api-key').val().trim();

    window.coraShowToast("Saving API Credentials...");
    $.post(coraREData.ajaxUrl, {
        action: 'cora_gbp_save_keys',
        security: coraREData.ajaxNonce,
        client_id: cId,
        client_secret: cSec,
        api_key: apiKey
    }, function(res) {
        window.coraShowToast("API Credentials Saved!");
        $('#cora-gbp-keys-panel').removeClass('drawer-open');
        setTimeout(function() { window.location.reload(); }, 600);
    });
};

window.coraGbpConnectPlatformGoogleOAuth = function() {
    var clientId = "<?php echo esc_js($cora_gbp_client_id); ?>";
    if (!clientId) {
        window.coraShowToast("Google Platform OAuth Client ID is missing.");
        return;
    }
    var redirectUri = encodeURIComponent("<?php echo home_url('/workspace/auth/google/callback'); ?>");
    var scope = encodeURIComponent("https://www.googleapis.com/auth/business.manage");
    var authUrl = "https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id=" + clientId + "&redirect_uri=" + redirectUri + "&scope=" + scope + "&access_type=offline&prompt=consent";
    
    window.coraShowToast("Redirecting to Google Account Sign-In...");
    window.location.href = authUrl;
};

// Upgraded Dynamic Google Places Search Connector Flow
window.coraGbpSearchRealBusiness = function() {
    var query = $('#cora-gbp-search-q').val().trim();
    if (!query) {
        window.coraShowToast("Please enter your business name.");
        return;
    }

    var wrap = $('#cora-gbp-search-results-wrap');
    wrap.empty().removeClass('hidden');

    // Render skeleton loading cards
    var skel = `
        <div class="space-y-3">
            <div class="text-[9px] font-bold text-zinc-455 uppercase tracking-widest flex items-center gap-1.5 mb-1.5 animate-pulse">
                <svg class="animate-spin" viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                Searching Google Places stream...
            </div>
            <div class="cora-skeleton rounded-2xl p-4 h-20 w-full"></div>
            <div class="cora-skeleton rounded-2xl p-4 h-20 w-full"></div>
        </div>
    `;
    wrap.html(skel);

    // Call Google Places Search API (via Plugin backend router)
    $.post(coraREData.ajaxUrl, {
        action: 'cora_gbp_search_places',
        query: query
    }, function(res) {
        wrap.empty();
        if (res && res.success && res.data && res.data.length > 0) {
            wrap.append('<p class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider mb-2.5">Select Your Business Listing:</p>');
            
            res.data.forEach(function(place) {
                var name = place.displayName ? place.displayName.text : 'Unknown Business';
                var type = place.primaryTypeDisplayName ? place.primaryTypeDisplayName.text : 'Business Listing';
                var address = place.formattedAddress || 'No address provided';
                var ratingVal = place.rating ? parseFloat(place.rating) : 0;
                var reviewCount = place.userRatingCount ? parseInt(place.userRatingCount) : 0;

                var stars = '';
                if (ratingVal > 0) {
                    stars = `<span class="bg-amber-50 dark:bg-amber-955/15 text-amber-600 dark:text-amber-450 px-2 py-0.5 rounded font-black text-[9px] border border-amber-250/40">${ratingVal.toFixed(1)} ★ (${reviewCount} reviews)</span>`;
                }

                var card = `
                    <div class="p-4 bg-zinc-55/20 dark:bg-zinc-800/10 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:border-zinc-350 dark:hover:border-zinc-700 transition-all shadow-2xs">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-black text-zinc-900 dark:text-zinc-100 leading-tight">${name}</h4>
                                <span class="text-[9px] font-black uppercase bg-zinc-100 dark:bg-zinc-850 px-2 py-0.5 rounded text-zinc-500">${type}</span>
                            </div>
                            <p class="text-[10px] text-zinc-555 dark:text-zinc-400 font-medium leading-relaxed">${address}</p>
                            ${stars}
                        </div>
                        <button onclick='coraGbpSaveSelectedPlace(${JSON.stringify(place).replace(/'/g, "&apos;")})' class="w-full sm:w-auto px-4 py-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-[10px] font-extrabold rounded-xl transition-all cursor-pointer shadow-xs shrink-0 border-0">Connect listing</button>
                    </div>
                `;
                wrap.append(card);
            });
        } else {
            // Fallback for API failure or no places found
            var msg = res.data && typeof res.data === 'string' ? res.data : 'No matching Places found.';
            var fallback = `
                <div class="p-4 bg-zinc-50/50 dark:bg-zinc-800/20 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl space-y-3.5">
                    <div>
                        <h4 class="text-xs font-bold text-zinc-850 dark:text-zinc-200">Google Places API Fallback Connection</h4>
                        <p class="text-[10px] text-zinc-505 dark:text-zinc-450 mt-1 leading-relaxed">${msg} You can connect manually as a custom listing below.</p>
                    </div>
                    <button onclick="coraGbpSaveCustomBusiness('${query.replace(/'/g, "\\'")}')" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-[10px] font-extrabold rounded-xl transition-colors cursor-pointer border-0 shadow-xs">
                        Connect manual listing: "${query}"
                    </button>
                </div>
            `;
            wrap.html(fallback);
        }
    });
};

window.coraGbpSaveSelectedPlace = function(place) {
    var name = place.displayName ? place.displayName.text : 'Business Listing';
    window.coraShowToast("Connecting " + name + "...");
    
    $.post(coraREData.ajaxUrl, {
        action: 'cora_gbp_connect_place',
        security: coraREData.ajaxNonce,
        place: place,
        category: place.primaryTypeDisplayName ? place.primaryTypeDisplayName.text : 'Google Listing',
        address: place.formattedAddress || '',
        phone: place.nationalPhoneNumber || '',
        website: place.websiteUri || '',
        rating: place.rating || 0,
        review_count: place.userRatingCount || 0
    }, function(res) {
        if (res.success) {
            window.coraShowToast("Business Listing Connected!");
            setTimeout(function() { window.location.reload(); }, 400);
        } else {
            window.coraShowToast(res.data.message || 'Failed to connect listing.');
        }
    });
};

window.coraGbpSaveCustomBusiness = function(bName) {
    window.coraShowToast("Connecting " + bName + "...");
    $.post(coraREData.ajaxUrl, {
        action: 'cora_gbp_connect_place',
        security: coraREData.ajaxNonce,
        business_name: bName,
        category: 'Business Profile',
        address: '',
        phone: '',
        rating: 5,
        review_count: 1
    }, function(res) {
        if (res.success) {
            window.coraShowToast("Business Listing Connected!");
            setTimeout(function() { window.location.reload(); }, 400);
        } else {
            window.coraShowToast(res.data.message || 'Failed to connect listing.');
        }
    });
};

window.coraGbpDisconnect = function() {
    window.coraShowToast("Disconnecting business listing...");
    $.post(coraREData.ajaxUrl, {
        action: 'cora_gbp_disconnect',
        security: coraREData.ajaxNonce,
        nonce: coraREData.ajaxNonce
    }, function(res) {
        window.coraShowToast("Listing Disconnected!");
        setTimeout(function() { window.location.reload(); }, 400);
    });
};

window.coraGbpLoadReviews = function() {
    $('#cora-gbp-reviews-loading').removeClass('hidden');
    $('#cora-gbp-reviews-list').empty().addClass('hidden');
    $('#cora-gbp-reviews-empty').addClass('hidden');

    $.post(coraREData.ajaxUrl, {
        action: 'cora_gbp_fetch_reviews',
        security: coraREData.ajaxNonce
    }, function(res) {
        $('#cora-gbp-reviews-loading').addClass('hidden');
        if (res && res.success && res.data && res.data.reviews && res.data.reviews.length > 0) {
            $('#cora-gbp-reviews-list').removeClass('hidden');
            
            res.data.reviews.forEach(function(rev) {
                var author = rev.reviewer && rev.reviewer.displayName ? rev.reviewer.displayName : (rev.authorName || 'Google User');
                var avatar = rev.reviewer && rev.reviewer.profilePhotoUri ? rev.reviewer.profilePhotoUri : '';
                var comment = rev.comment || 'No comment text provided.';
                var ratingStr = rev.starRating || 'FIVE';
                var ratingNum = 5;
                if (ratingStr === 'FOUR') ratingNum = 4;
                else if (ratingStr === 'THREE') ratingNum = 3;
                else if (ratingStr === 'TWO') ratingNum = 2;
                else if (ratingStr === 'ONE') ratingNum = 1;

                var starsHtml = '';
                for (var i = 1; i <= 5; i++) {
                    if (i <= ratingNum) {
                        starsHtml += '<span class="text-amber-500 font-bold">★</span>';
                    } else {
                        starsHtml += '<span class="text-zinc-200 dark:text-zinc-800">★</span>';
                    }
                }

                var dateStr = rev.createTime ? new Date(rev.createTime).toLocaleDateString() : 'Recent';
                var replyHtml = '';
                
                // Construct safe selector ID for dynamic targeting
                var revKey = rev.reviewId || btoa(rev.name).substring(0, 16);
                
                if (rev.reviewReply && rev.reviewReply.comment) {
                    replyHtml = `
                        <div class="mt-3 p-3 bg-zinc-55 dark:bg-zinc-800/40 rounded-xl border border-zinc-100 dark:border-zinc-800/80">
                            <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">My Response</span>
                            <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-1 font-medium">${rev.reviewReply.comment}</p>
                        </div>
                    `;
                } else {
                    replyHtml = `
                        <div class="mt-3 space-y-2">
                            <textarea id="reply-text-${revKey}" rows="2" class="cora-input w-full rounded-xl p-2.5 text-xs focus:outline-none" placeholder="Write reply comment..."></textarea>
                            <div class="flex justify-end">
                                <button onclick="coraGbpSubmitReply('${rev.name}', '${revKey}')" class="px-3.5 py-1.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-[10px] font-extrabold rounded-lg transition-colors cursor-pointer border-0 shadow-sm">Submit AI Assistant Reply</button>
                            </div>
                        </div>
                    `;
                }

                var avatarHtml = avatar 
                    ? `<img src="${avatar}" class="w-10 h-10 rounded-full object-cover border border-zinc-200 dark:border-zinc-700 shadow-2xs" onerror="this.style.display='none'; $(this).next().show();">` 
                    : '';
                var avatarFallbackHtml = `<div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-black text-zinc-500 dark:text-zinc-400 text-xs shadow-2xs shrink-0">${author.substring(0,2).toUpperCase()}</div>`;
                var finalAvatar = `<div class="shrink-0 relative">${avatarHtml}<div style="${avatar ? 'display:none;' : ''}" class="fallback-wrap">${avatarFallbackHtml}</div></div>`;

                var item = `
                    <div class="py-4 first:pt-0 last:pb-0 space-y-2.5">
                        <div class="flex items-center gap-3">
                            ${finalAvatar}
                            <div>
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 leading-tight">${author}</h4>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <div class="flex text-xs">${starsHtml}</div>
                                    <span class="text-[10px] text-zinc-400 font-medium">${dateStr}</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-zinc-650 dark:text-zinc-350 leading-relaxed font-medium mt-1">${comment}</p>
                        ${replyHtml}
                    </div>
                `;
                $('#cora-gbp-reviews-list').append(item);
            });
        } else {
            $('#cora-gbp-reviews-empty').removeClass('hidden');
        }
    });
};

window.coraGbpSubmitReply = function(reviewName, revKey) {
    var txt = $('#reply-text-' + revKey).val().trim();
    if (!txt) {
        window.coraShowToast("Please enter reply message.");
        return;
    }

    window.coraShowToast("Submitting reply to Google...");
    $.post(coraREData.ajaxUrl, {
        action: 'cora_gbp_reply_review',
        security: coraREData.ajaxNonce,
        review_name: reviewName,
        reply: txt
    }, function(res) {
        if (res.success) {
            window.coraShowToast("Reply posted successfully!");
            setTimeout(function() { window.location.reload(); }, 600);
        } else {
            window.coraShowToast(res.data.message || 'Failed to submit reply.');
        }
    });
};

window.coraGbpPublishPost = function() {
    var content = $('#cora-gbp-post-content').val().trim();
    var cta = $('#cora-gbp-post-cta').val();
    var ctaUrl = $('#cora-gbp-post-cta-url').val().trim();

    if (!content) {
        window.coraShowToast("Please enter post content.");
        return;
    }

    window.coraShowToast("Publishing update to Google Maps...");
    $.post(coraREData.ajaxUrl, {
        action: 'cora_gbp_publish_post',
        security: coraREData.ajaxNonce,
        content: content,
        cta: cta,
        cta_url: ctaUrl
    }, function(res) {
        if (res.success) {
            window.coraShowToast("Post published successfully!");
            setTimeout(function() { window.location.reload(); }, 600);
        } else {
            window.coraShowToast(res.data.message || 'Failed to publish post.');
        }
    });
};

$(document).ready(function() {
    if ($('#cora-gbp-reviews-loading').length > 0) {
        window.coraGbpLoadReviews();
    }
});
</script>
