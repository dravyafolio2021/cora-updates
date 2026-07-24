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

<!-- PAGE HEADER -->
<div class="cora-page-header flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200/80 pb-6 mb-6">
    <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-2xl bg-zinc-950 text-white flex items-center justify-center text-xs font-bold shadow-2xs shrink-0">
            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
        </div>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-950">Google Business Profile Broker Owner</h1>
                <span class="px-2.5 py-0.5 text-xs font-bold bg-zinc-950 text-white rounded-md uppercase tracking-wider">AI Marketing</span>
            </div>
            <p class="cora-section-desc text-xs font-medium text-zinc-500 mt-0.5">Manage your Google Maps listing, reply to live client reviews with AI assistance, and publish updates to Maps.</p>
        </div>
    </div>

    <!-- Header Actions -->
    <div class="flex items-center gap-3">
        <button onclick="coraGbpToggleKeysPanel()" class="px-3.5 py-2 bg-white border border-zinc-200 text-zinc-800 hover:bg-zinc-50 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-2 cursor-pointer">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            API Credentials
        </button>
        <?php if ( $cora_gbp_is_connected ) : ?>
            <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-3.5 py-2 rounded-xl shadow-2xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-zinc-950"><?php echo $gbp_name; ?></span>
                <button onclick="coraGbpDisconnect()" class="ml-2 text-[10px] font-bold text-zinc-400 hover:text-red-600 transition-colors cursor-pointer">Disconnect</button>
            </div>
        <?php else : ?>
            <div class="flex items-center gap-2 bg-zinc-100 border border-zinc-200/80 px-3.5 py-2 rounded-xl">
                <span class="w-2.5 h-2.5 rounded-full bg-zinc-400"></span>
                <span class="text-xs font-semibold text-zinc-600">Not Connected</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- API CREDENTIALS CONFIGURATION PANEL (Collapsible) -->
<div id="cora-gbp-keys-panel" class="hidden bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs mb-6 max-w-3xl">
    <div class="flex items-center justify-between border-b border-zinc-100 pb-3 mb-4">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Google Cloud API Credentials</h3>
            <p class="text-xs text-zinc-500 mt-0.5">Enter your Google Cloud project credentials to enable live Google Business Profile sync and Places search.</p>
        </div>
        <button onclick="coraGbpToggleKeysPanel()" class="text-xs text-zinc-400 hover:text-zinc-950 font-bold">✕ Close</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
        <div>
            <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Google OAuth Client ID</label>
            <input type="text" id="cora-gbp-input-client-id" value="<?php echo esc_attr($cora_gbp_client_id); ?>" placeholder="e.g. 123456789-abc.apps.googleusercontent.com" class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs font-mono bg-white focus:border-zinc-950 focus:outline-none text-zinc-900">
        </div>
        <div>
            <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Google OAuth Client Secret</label>
            <input type="password" id="cora-gbp-input-client-secret" value="<?php echo esc_attr($cora_gbp_client_secret); ?>" placeholder="GOCSPX-..." class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs font-mono bg-white focus:border-zinc-950 focus:outline-none text-zinc-900">
        </div>
        <div class="md:col-span-2">
            <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1">Google Maps / Places API Key (Optional for Live Places Search)</label>
            <input type="text" id="cora-gbp-input-api-key" value="<?php echo esc_attr($cora_google_maps_key); ?>" placeholder="AIzaSy..." class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs font-mono bg-white focus:border-zinc-950 focus:outline-none text-zinc-900">
        </div>
    </div>
    <div class="flex justify-end border-t border-zinc-100 pt-4 mt-4">
        <button onclick="coraGbpSaveApiKeys()" class="px-5 py-2.5 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-colors shadow-sm flex items-center gap-2 cursor-pointer">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save API Credentials
        </button>
    </div>
</div>

<?php if ( ! $cora_gbp_is_connected ) : ?>
<!-- ===== UNCONNECTED STATE: Search & Connect Your Real Business ===== -->
<div class="bg-white border border-zinc-200/80 rounded-2xl shadow-2xs overflow-hidden max-w-2xl mx-auto my-8">
    <div class="p-8 text-center space-y-6">
        
        <div class="w-14 h-14 rounded-2xl bg-zinc-950 text-white mx-auto flex items-center justify-center shadow-md">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>

        <div class="space-y-2 max-w-md mx-auto">
            <h2 class="text-xl font-bold text-zinc-950">Connect Your Business Profile</h2>
            <p class="text-xs font-medium text-zinc-500 leading-relaxed">Enter your exact business name below to connect your listing to Cora Workspace.</p>
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
                        class="w-full pl-9 pr-4 py-3 border border-zinc-200 rounded-xl text-xs font-medium bg-white focus:border-zinc-950 focus:outline-none transition-all text-zinc-900"
                        onkeydown="if(event.key==='Enter') coraGbpSearchRealBusiness()"
                        autocomplete="off"
                    >
                </div>
                <button id="cora-gbp-search-btn" onclick="coraGbpSearchRealBusiness()" class="px-5 py-3 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer shadow-sm">
                    Connect Listing
                </button>
            </div>
        </div>

        <!-- Search Results / Direct Connect Action -->
        <div id="cora-gbp-search-results-wrap" class="pt-2 space-y-3 text-left"></div>

        <div class="pt-6 border-t border-zinc-100 flex items-center justify-between">
            <span class="text-xs font-bold text-zinc-600">Connect via 1-Click Google OAuth:</span>
            <button onclick="coraGbpConnectPlatformGoogleOAuth()" class="px-5 py-2.5 bg-white border border-zinc-200 text-zinc-950 hover:bg-zinc-100 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-2 cursor-pointer">
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
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-bold text-zinc-950">Google Reviews Inbox</h3>
                    <?php if ($gbp_rating > 0) : ?>
                        <span class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full font-bold"><?php echo number_format($gbp_rating, 1); ?> ★ Rating</span>
                    <?php endif; ?>
                </div>
                <button onclick="coraGbpLoadReviews()" class="text-xs font-bold text-zinc-700 bg-zinc-100 hover:bg-zinc-200 border border-zinc-200 rounded-xl px-3.5 py-2 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Refresh Reviews
                </button>
            </div>

            <!-- Loading State -->
            <div id="cora-gbp-reviews-loading" class="flex items-center justify-center py-10 gap-2.5 text-zinc-400">
                <svg class="animate-spin" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                <span class="text-xs font-semibold">Checking Google API for live reviews...</span>
            </div>

            <!-- Live Reviews List Feed -->
            <div id="cora-gbp-reviews-list" class="space-y-4 divide-y divide-zinc-100 hidden"></div>

            <!-- Empty Reviews State -->
            <div id="cora-gbp-reviews-empty" class="hidden py-8 flex flex-col items-center gap-2 text-center">
                <svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="1.4" fill="none" class="text-zinc-300"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <div>
                    <h4 class="text-xs font-bold text-zinc-800">No reviews retrieved for <?php echo $gbp_name; ?></h4>
                    <p class="text-xs text-zinc-400 mt-0.5 max-w-sm mx-auto">Connect your Google OAuth Client ID using the button above to authorize live Google Business Profile sync.</p>
                </div>
            </div>
        </div>

        <!-- Google Maps Post & Offer Publisher -->
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-4">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-base font-bold text-zinc-950">Publish to Google Maps</h3>
                <p class="text-xs text-zinc-500 mt-0.5">Posts appear on your Google Business listing within minutes.</p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1.5">Post Content &amp; Announcement *</label>
                    <textarea id="cora-gbp-post-content" rows="3" class="w-full border border-zinc-200 rounded-xl p-3 text-xs font-medium bg-white focus:border-zinc-950 focus:outline-none text-zinc-900" placeholder="Share a recent update, announcements, or seasonal offer..."></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1.5">Call to Action Button</label>
                        <select id="cora-gbp-post-cta" class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs font-bold bg-white focus:border-zinc-950 focus:outline-none text-zinc-800 cursor-pointer">
                            <option value="NONE">None</option>
                            <option value="BOOK_NOW" selected>Book Now</option>
                            <option value="LEARN_MORE">Learn More</option>
                            <option value="CALL_NOW">Call Now</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1.5">CTA Link URL</label>
                        <input type="url" id="cora-gbp-post-cta-url" class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs font-medium bg-white focus:border-zinc-950 focus:outline-none text-zinc-900" placeholder="https://yourwebsite.com">
                    </div>
                </div>
            </div>
            <div class="flex justify-end border-t border-zinc-100 pt-4">
                <button id="cora-gbp-publish-btn" onclick="coraGbpPublishPost()" class="px-6 py-2.5 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 transition-colors flex items-center gap-2 cursor-pointer shadow-sm">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Publish Post to Google Maps
                </button>
            </div>
        </div>

        <?php if (!empty($cora_gbp_posts)): ?>
        <!-- Published Posts History -->
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-3">
            <h3 class="text-sm font-bold text-zinc-950 border-b border-zinc-100 pb-3">Published Posts History</h3>
            <div class="space-y-2">
                <?php foreach (array_slice($cora_gbp_posts, 0, 5) as $gbp_post): ?>
                <div class="flex items-start justify-between gap-3 py-2.5 border-b border-zinc-50 last:border-0">
                    <p class="text-xs text-zinc-700 font-medium leading-relaxed flex-1"><?php echo esc_html($gbp_post['content'] ?? ''); ?></p>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Published</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Right Column (1 Col): Connected Listing Profile Card -->
    <div class="space-y-6">
        
        <!-- Connected Business Card -->
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-950">Connected Listing Profile</h3>
                <span class="text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-bold">ACTIVE</span>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-950 text-white flex items-center justify-center text-sm font-extrabold shadow-2xs shrink-0"><?php echo $gbp_initials; ?></div>
                    <div>
                        <h4 class="text-sm font-bold text-zinc-950 leading-tight"><?php echo $gbp_name; ?></h4>
                        <?php if ($gbp_cat) : ?><p class="text-xs text-zinc-500 font-medium mt-0.5"><?php echo $gbp_cat; ?></p><?php endif; ?>
                    </div>
                </div>

                <?php if ($gbp_rating > 0) : ?>
                <div class="flex items-center gap-2 p-3 bg-amber-50/50 rounded-xl border border-amber-200/60">
                    <span class="text-amber-500 text-sm font-bold"><?php echo number_format($gbp_rating, 1); ?> ★</span>
                    <span class="text-xs font-bold text-zinc-800"><?php echo number_format($gbp_reviews); ?> Google Reviews</span>
                </div>
                <?php endif; ?>

                <div class="space-y-2.5 text-xs text-zinc-700">
                    <?php if ($gbp_addr) : ?>
                    <div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-0.5">Address</span>
                        <p class="font-medium leading-relaxed text-zinc-600"><?php echo $gbp_addr; ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($gbp_phone) : ?>
                    <div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-0.5">Phone</span>
                        <p class="font-bold text-zinc-950"><?php echo $gbp_phone; ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($gbp_website) : ?>
                    <div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-0.5">Website</span>
                        <a href="<?php echo $gbp_website; ?>" target="_blank" class="font-bold text-blue-600 hover:underline truncate block"><?php echo $gbp_website; ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pt-3 border-t border-zinc-100 flex justify-between items-center">
                <button onclick="coraGbpDisconnect()" class="text-[11px] font-bold text-zinc-400 hover:text-red-600 transition-colors cursor-pointer">
                    Disconnect listing
                </button>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>

<script>
window.coraGbpToggleKeysPanel = function() {
    $('#cora-gbp-keys-panel').toggleClass('hidden');
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
        $('#cora-gbp-keys-panel').addClass('hidden');
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

window.coraGbpSearchRealBusiness = function() {
    var query = $('#cora-gbp-search-q').val().trim();
    if (!query) {
        window.coraShowToast("Please enter your business name.");
        return;
    }
    window.coraGbpSaveCustomBusiness(query);
};

window.coraGbpSaveCustomBusiness = function(bName) {
    window.coraShowToast("Connecting " + bName + "...");
    $.post(coraREData.ajaxUrl, {
        action: 'cora_gbp_connect_place',
        security: coraREData.ajaxNonce,
        business_name: bName,
        category: 'Business Profile',
        address: '',
        phone: ''
    }, function(res) {
        window.coraShowToast("Business Listing Connected!");
        setTimeout(function() { window.location.reload(); }, 300);
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
        setTimeout(function() { window.location.reload(); }, 300);
    });
};

window.coraGbpLoadReviews = function() {
    $('#cora-gbp-reviews-loading').removeClass('hidden');
    $('#cora-gbp-reviews-list').addClass('hidden');
    $('#cora-gbp-reviews-empty').addClass('hidden');

    $.post(coraREData.ajaxUrl, {
        action: 'cora_gbp_fetch_reviews',
        security: coraREData.ajaxNonce
    }, function(res) {
        $('#cora-gbp-reviews-loading').addClass('hidden');
        if (res && res.success && res.data && res.data.reviews && res.data.reviews.length > 0) {
            // Render live reviews
            $('#cora-gbp-reviews-list').removeClass('hidden');
        } else {
            // No reviews found or unauthenticated empty state
            $('#cora-gbp-reviews-empty').removeClass('hidden');
        }
    });
};

$(document).ready(function() {
    if ($('#cora-gbp-reviews-loading').length > 0) {
        window.coraGbpLoadReviews();
    }
});
</script>
