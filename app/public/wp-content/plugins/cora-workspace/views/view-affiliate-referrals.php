<?php
/**
 * Cora Affiliate & Referral Workspace View - Redesigned to HeroUI & ShadCN UI Component Anatomy
 * 
 * Component System Architecture:
 * - HeroUI Card: Modular CardHeader, CardBody, and CardFooter with subtle neutral borders and soft elevation
 * - HeroUI Tabs: Elevated segmented pill control with subtle background track and micro-counters
 * - HeroUI Progress: Smooth pill track with solid monochromatic fill and milestone progress indicators
 * - HeroUI Snippet: Dedicated bordered container with font-mono URL, link icon prefix, and copy feedback
 * - HeroUI User & Avatar: Clean squircle avatar with bold name and font-mono metadata
 * - HeroUI Chip & Badge: Monochromatic status pills with micro-dots
 * - HeroUI Slider: Custom track with interactive thumb and real-time client count badge
 * - ShadCN UI Form Inputs & Table: Clean inputs with prefix labels and high-density data tables
 * 
 * Commission Structure:
 * - 100 Free AI Runes (Credits) on each signup.
 * - 20% Recurring Commission on all Monthly Subscriptions.
 * - 30% Recurring Commission on all Annual Subscriptions.
 * - Milestone Cash Bonuses:
 *   • 10 Paid Referrals  -> ₹1,000 ($10 USD) Cash Bonus
 *   • 50 Paid Referrals  -> ₹5,000 ($50 USD) Cash Bonus
 *   • 100 Paid Referrals -> ₹10,000 ($100 USD) Cash Bonus
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
$current_user = wp_get_current_user();
$user_display_name = $current_user && $current_user->display_name ? $current_user->display_name : ( $current_user ? $current_user->user_login : 'Studio Owner' );
$user_email = $current_user ? $current_user->user_email : 'owner@studio.local';
$workspace_name = get_bloginfo( 'name' );
$default_slug = sanitize_title( $user_display_name ? $user_display_name : 'partner' );

$is_enrolled = class_exists( 'Cora_Affiliate_Referral_Engine' ) 
    ? Cora_Affiliate_Referral_Engine::is_user_enrolled( $user_id ) 
    : false;

$affiliate_data = class_exists( 'Cora_Affiliate_Referral_Engine' ) 
    ? Cora_Affiliate_Referral_Engine::get_dashboard_data( $user_id ) 
    : array(
        'ref_code'               => 'CR-' . strtoupper( substr( md5( (string) $user_id ), 0, 6 ) ),
        'custom_slug'            => $default_slug,
        'referral_url'           => home_url( '/?ref=' . $default_slug ),
        'clicks_count'           => 142,
        'unique_visits'          => 86,
        'total_conversions'      => 5,
        'free_signups_count'     => 2,
        'paid_conversions_count' => 3,
        'conversion_rate'        => 18.4,
        'total_commission'       => 17998.80,
        'available_balance'      => 10498.80,
        'total_ai_credits'       => 1000,
        'referrals'              => array(),
        'payouts'                => array(),
        'commission_rate'        => 30.0,
        'min_payout'             => 1000.0,
    );

$ref_url       = $affiliate_data['referral_url'];
$ref_code      = $affiliate_data['ref_code'];
$custom_slug   = isset( $affiliate_data['custom_slug'] ) && $affiliate_data['custom_slug'] ? $affiliate_data['custom_slug'] : $ref_code;
$total_comm    = (float) $affiliate_data['total_commission'];
$avail_bal     = (float) $affiliate_data['available_balance'];
$ai_credits    = (int) $affiliate_data['total_ai_credits'];
$clicks        = (int) $affiliate_data['clicks_count'];
$unique_visits = (int) ( $affiliate_data['unique_visits'] ?? 86 );
$conv_rate     = (float) $affiliate_data['conversion_rate'];
$referrals     = $affiliate_data['referrals'];
$payouts       = $affiliate_data['payouts'];
$free_signups  = (int) $affiliate_data['free_signups_count'];
$paid_signups  = (int) $affiliate_data['paid_conversions_count'];
$min_payout    = (float) ( $affiliate_data['min_payout'] ?? 1000.0 );

// Currency detection
$currency_format = get_option( 'cora_currency_format', 'INR_LAKHS' );
$is_india_geo = true;
if ( strpos( $currency_format, 'USD' ) !== false || ( isset( $_GET['currency'] ) && strtoupper( sanitize_text_field( wp_unslash( $_GET['currency'] ) ) ) === 'USD' ) || ( isset( $_GET['geo'] ) && strtolower( sanitize_text_field( wp_unslash( $_GET['geo'] ) ) ) === 'global' ) ) {
    $is_india_geo = false;
}
$curr_sym = $is_india_geo ? '₹' : '$';

// --- GAMIFIED TIER & PROGRESS CALCULATION (HeroUI Component Data) ---
$current_tier_name = 'Bronze Partner';
$current_tier_icon = '🥉';
$next_tier_name = 'Silver Creator (₹25,000)';
$next_tier_threshold = 25000.0;
$prev_tier_threshold = 0.0;
$tier_badge_class = 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border-zinc-200/80 dark:border-zinc-700/80';

if ( $total_comm >= 100000.0 ) {
    $current_tier_name = 'Diamond Titan';
    $current_tier_icon = '💎';
    $next_tier_name = 'Apex Milestone Achieved';
    $next_tier_threshold = 100000.0;
    $prev_tier_threshold = 50000.0;
    $tier_badge_class = 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 border-zinc-900 dark:border-zinc-100';
} elseif ( $total_comm >= 50000.0 ) {
    $current_tier_name = 'Gold Ambassador';
    $current_tier_icon = '🥇';
    $next_tier_name = 'Diamond Titan (₹1,00,000)';
    $next_tier_threshold = 100000.0;
    $prev_tier_threshold = 50000.0;
    $tier_badge_class = 'bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 border-zinc-300 dark:border-zinc-600';
} elseif ( $total_comm >= 25000.0 ) {
    $current_tier_name = 'Silver Creator';
    $current_tier_icon = '🥈';
    $next_tier_name = 'Gold Ambassador (₹50,000)';
    $next_tier_threshold = 50000.0;
    $prev_tier_threshold = 25000.0;
    $tier_badge_class = 'bg-zinc-150 dark:bg-zinc-750 text-zinc-850 dark:text-zinc-150 border-zinc-200/80 dark:border-zinc-700/80';
}

$tier_progress_pct = 0;
if ( $next_tier_threshold > $prev_tier_threshold && $total_comm < 100000.0 ) {
    $tier_progress_pct = min( 100, max( 8, round( ( ( $total_comm - $prev_tier_threshold ) / ( $next_tier_threshold - $prev_tier_threshold ) ) * 100 ) ) );
} elseif ( $total_comm >= 100000.0 ) {
    $tier_progress_pct = 100;
}
$to_next_tier = max( 0.0, $next_tier_threshold - $total_comm );

// Milestone Bonus Unlocks based on paid referrals count
$milestone_bonus_earned = 0;
if ( $paid_signups >= 100 ) {
    $milestone_bonus_earned = $is_india_geo ? 10000 : 100;
} elseif ( $paid_signups >= 50 ) {
    $milestone_bonus_earned = $is_india_geo ? 5000 : 50;
} elseif ( $paid_signups >= 10 ) {
    $milestone_bonus_earned = $is_india_geo ? 1000 : 10;
}

$payout_progress_pct = min( 100, max( 0, round( ( $avail_bal / $min_payout ) * 100 ) ) );
?>

<style>
/* HeroUI & ShadCN Component Anatomy Overrides */
#cora-affiliate-root {
    --heroui-card-radius: 1rem;
    --heroui-chip-radius: 9999px;
}

/* HeroUI Segmented Tabs Container */
#cora-affiliate-tabs-bar.cora-sub-tabs-container {
    padding: 4px 6px !important;
    gap: 4px !important;
}

#cora-affiliate-tabs-bar .cora-sub-tabs-nav {
    display: inline-flex !important;
    align-items: center !important;
    padding: 3px !important;
    background-color: rgba(244, 244, 245, 0.85) !important;
    border-radius: 0.875rem !important;
    border: 1px solid rgba(228, 228, 231, 0.8) !important;
    gap: 2px !important;
}

.dark #cora-affiliate-tabs-bar .cora-sub-tabs-nav {
    background-color: rgba(39, 39, 42, 0.6) !important;
    border-color: rgba(63, 63, 70, 0.7) !important;
}

/* HeroUI Elevated Tab Buttons */
#cora-affiliate-tabs-bar .cora-sub-tab {
    border-bottom: none !important;
    border-radius: 0.625rem !important;
    height: 32px !important;
    min-height: 32px !important;
    padding: 0 12px !important;
    font-size: 0.75rem !important;
    font-weight: 500 !important;
    color: #71717a !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    box-shadow: none !important;
}

.dark #cora-affiliate-tabs-bar .cora-sub-tab {
    color: #a1a1aa !important;
}

#cora-affiliate-tabs-bar .cora-sub-tab:hover {
    color: #09090b !important;
    background-color: rgba(255, 255, 255, 0.5) !important;
}

.dark #cora-affiliate-tabs-bar .cora-sub-tab:hover {
    color: #fafafa !important;
    background-color: rgba(63, 63, 70, 0.4) !important;
}

#cora-affiliate-tabs-bar .cora-sub-tab.active {
    background-color: #ffffff !important;
    color: #09090b !important;
    font-weight: 600 !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px -1px rgba(0, 0, 0, 0.08) !important;
}

.dark #cora-affiliate-tabs-bar .cora-sub-tab.active {
    background-color: #18181b !important;
    color: #fafafa !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.4) !important;
}

/* HeroUI Slider Track Styling */
.heroui-slider-track {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 6px;
    border-radius: 9999px;
    background: #e4e4e7;
    outline: none;
    transition: background 0.15s ease-in-out;
}

.dark .heroui-slider-track {
    background: #27272a;
}

.heroui-slider-track::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #09090b;
    border: 2px solid #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
    cursor: pointer;
    transition: transform 0.15s ease, background 0.15s ease;
}

.heroui-slider-track::-webkit-slider-thumb:hover {
    transform: scale(1.15);
}

.dark .heroui-slider-track::-webkit-slider-thumb {
    background: #fafafa;
    border-color: #18181b;
}

.heroui-slider-track::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #09090b;
    border: 2px solid #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
    cursor: pointer;
}

.dark .heroui-slider-track::-moz-range-thumb {
    background: #fafafa;
    border-color: #18181b;
}
</style>

<script>
window.coraAffiliateState = window.coraAffiliateState || {
    referralUrl: <?php echo json_encode( $ref_url ); ?>,
    refCode: <?php echo json_encode( $ref_code ); ?>,
    customSlug: <?php echo json_encode( $custom_slug ); ?>,
    availBalance: <?php echo json_encode( (float) $avail_bal ); ?>,
    isEnrolled: <?php echo json_encode( (bool) $is_enrolled ); ?>,
    activeTab: 'overview',
    billingMode: 'annual',
    screenerBillingMode: 'annual'
};

window.coraNormalizeAffiliateTabKey = function(key) {
    if (!key) return 'overview';
    var k = String(key).toLowerCase().trim();
    k = k.replace(/^cora-tab-btn-/, '').replace(/^cora-aff-subtab-/, '').replace(/^tab-/, '').replace(/^#/, '');
    if (k === 'ranks' || k === 'board' || k === 'leaderboards') return 'leaderboard';
    if (k === 'calc' || k === 'simulator' || k === 'sim' || k === 'earnings') return 'calculator';
    if (k === 'logs' || k === 'log' || k === 'referral' || k === 'conversions' || k === 'converts') return 'referrals';
    if (k === 'payout' || k === 'banking' || k === 'withdraw' || k === 'withdrawals' || k === 'disbursements') return 'payouts';
    if (k === 'meter' || k === 'home' || k === 'summary') return 'overview';
    return k;
};

window.coraSwitchAffiliateTab = function(tabKey) {
    var rawKey = tabKey;
    tabKey = window.coraNormalizeAffiliateTabKey(tabKey);
    if (!tabKey) return;

    try {
        if (window.coraAffiliateState) {
            window.coraAffiliateState.activeTab = tabKey;
        }

        var subtabs = ['overview', 'leaderboard', 'calculator', 'referrals', 'payouts'];

        subtabs.forEach(function(key) {
            var tabView = document.getElementById('cora-aff-subtab-' + key);
            var isTarget = (key === tabKey);

            if (tabView) {
                if (isTarget) {
                    tabView.classList.remove('hidden');
                    tabView.style.setProperty('display', 'block', 'important');
                } else {
                    tabView.classList.add('hidden');
                    tabView.style.setProperty('display', 'none', 'important');
                }
            }

            // Update all button variants (by ID, class, and data-target)
            var tabBtns = document.querySelectorAll('#cora-tab-btn-' + key + ', #cora-affiliate-tabs-bar .cora-sub-tab[data-target="' + key + '"], .cora-sub-tabs-container .cora-sub-tab[data-target="' + key + '"]');
            tabBtns.forEach(function(btn) {
                if (isTarget) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        });

        if (tabKey === 'calculator' && typeof window.coraRecalculateSimulator === 'function') {
            window.coraRecalculateSimulator();
        }

        if (tabKey === 'referrals' && typeof window.coraFilterReferralTable === 'function') {
            window.coraFilterReferralTable('all');
        }

        // Auto-scroll the active tab into view in the tabs bar
        var activeBtn = document.getElementById('cora-tab-btn-' + tabKey) || document.querySelector('#cora-affiliate-tabs-bar [data-target="' + tabKey + '"]');
        var container = document.getElementById('cora-affiliate-tabs-bar') || document.querySelector('.cora-sub-tabs-container.cora-sticky-sub-tabs');
        if (activeBtn && container) {
            var btnOffset = activeBtn.offsetLeft;
            var btnWidth = activeBtn.offsetWidth;
            var containerWidth = container.offsetWidth;
            container.scrollTo({
                left: Math.max(0, btnOffset - (containerWidth / 2) + (btnWidth / 2)),
                behavior: 'smooth'
            });
        }

        // Sync URL hash
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, null, '#' + tabKey);
        }
    } catch (err) {
        console.error('Error switching affiliate tab:', err);
    }
};

window.coraOpenAffiliateTab = window.coraSwitchAffiliateTab;
var coraSwitchAffiliateTab = window.coraSwitchAffiliateTab;

// Unified delegated click listener
document.addEventListener('click', function(e) {
    if (!e.target || !e.target.closest) return;
    var btn = e.target.closest('#cora-affiliate-tabs-bar .cora-sub-tab, #cora-affiliate-tabs-bar button, .cora-sub-tab[id^="cora-tab-btn-"], [data-aff-tab]');
    if (btn) {
        var tabId = btn.getAttribute('data-target') || btn.getAttribute('data-aff-tab') || (btn.id ? btn.id.replace('cora-tab-btn-', '') : '');
        if (tabId && typeof window.coraSwitchAffiliateTab === 'function') {
            e.preventDefault();
            e.stopPropagation();
            window.coraSwitchAffiliateTab(tabId);
        }
    }
}, true);

// Initialize active tab from URL hash or search params
(function() {
    function initAffiliateTabFromUrl() {
        var params = new URLSearchParams(window.location.search);
        var urlTab = params.get('tab') || params.get('subtab') || (window.location.hash ? window.location.hash.replace('#', '') : '');
        if (urlTab && typeof window.coraSwitchAffiliateTab === 'function') {
            window.coraSwitchAffiliateTab(urlTab);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAffiliateTabFromUrl);
    } else {
        initAffiliateTabFromUrl();
    }
    window.addEventListener('hashchange', initAffiliateTabFromUrl);
})();
</script>

<div id="cora-affiliate-root" class="space-y-5 max-w-full">

    <!-- ================================================================= -->
    <!-- VIEW A: 3-STEP PARTNER ENROLLMENT SCREENER (FOR UNENROLLED USERS) -->
    <!-- ================================================================= -->
    <div id="cora-affiliate-screener-view" class="<?php echo $is_enrolled ? 'hidden' : ''; ?> max-w-5xl mx-auto space-y-6 py-2">
        
        <!-- HeroUI Card: Hero Agency Opportunity Card -->
        <div class="w-full bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-3xl p-6 sm:p-8 lg:p-10 shadow-3xs relative overflow-hidden">
            <!-- Hidden profile & payout inputs for backend AJAX compatibility -->
            <input type="hidden" id="cora-enroll-name" value="<?php echo esc_attr( $user_display_name ); ?>">
            <input type="hidden" id="cora-enroll-workspace" value="<?php echo esc_attr( $workspace_name ); ?>">
            <input type="hidden" id="cora-enroll-email" value="<?php echo esc_attr( $user_email ); ?>">
            <input type="hidden" id="cora-enroll-upi" value="">
            <input type="hidden" id="cora-enroll-bank" value="">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <div class="lg:col-span-7 space-y-4 flex flex-col items-start text-left">
                    <!-- HeroUI Chip: Partner Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-200/80 dark:border-zinc-700/80">
                        <span class="w-2 h-2 rounded-full bg-zinc-950 dark:bg-zinc-100 animate-pulse"></span>
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>
                        <span class="font-bold">Official Partner Network</span>
                        <span class="text-zinc-300 dark:text-zinc-700">|</span>
                        <span class="text-zinc-500 dark:text-zinc-400 font-medium">Up to 30% Recurring Revenue</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-[34px] font-bold text-zinc-950 dark:text-zinc-50 tracking-tight leading-[1.2]">
                        Turn Your Creative Network<br class="hidden sm:inline"> Into Recurring Lifetime Revenue
                    </h1>

                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-lg">
                        Recommend Cora to fellow studios, production houses, and agency directors. Earn 30% recurring on annual plans, unlock milestone bonuses up to ₹10,000, and collect 100 free AI Runes per signup.
                    </p>

                    <div class="pt-2 space-y-3 w-full">
                        <!-- HeroUI Snippet Handle Activation Row -->
                        <div class="flex flex-wrap items-center gap-2.5">
                            <div class="flex items-center">
                                <span class="px-3 py-2 text-xs bg-zinc-100 dark:bg-zinc-800 border border-r-0 border-zinc-200 dark:border-zinc-700 rounded-l-xl text-zinc-500 font-mono text-[11px] shrink-0"><?php echo esc_html( home_url( '/?ref=' ) ); ?></span>
                                <input type="text" id="cora-enroll-slug" value="<?php echo esc_attr( $default_slug ); ?>" class="w-36 px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-r-xl text-zinc-900 dark:text-zinc-100 font-mono font-bold focus:outline-none">
                            </div>

                            <button id="cora-enroll-submit-btn" type="button" onclick="coraSubmitAffiliateEnrollment()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-xl shadow-2xs transition-all cursor-pointer whitespace-nowrap active:scale-98">
                                <span>Activate Partner Link</span>
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </button>

                            <button type="button" onclick="coraToggleHowItWorksModal(true)" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors cursor-pointer">
                                <span>How It Works</span>
                            </button>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-2 cursor-pointer select-none text-[11px] text-zinc-500 dark:text-zinc-400">
                                <input id="cora-enroll-agree" type="checkbox" checked class="accent-zinc-900 dark:accent-zinc-100 rounded w-3.5 h-3.5 cursor-pointer">
                                <span>I accept the <button type="button" onclick="coraToggleHowItWorksModal(true)" class="underline hover:text-zinc-800 dark:hover:text-zinc-200">Partner Agreement</button> (30-day tracking cookie, direct monthly payouts, no self-referrals).</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Visual Ecosystem Card -->
                <div class="lg:col-span-5 relative flex items-center justify-center p-6 bg-zinc-50 dark:bg-zinc-950/60 rounded-2xl border border-zinc-200/80 dark:border-zinc-800">
                    <div class="w-full space-y-3">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Commission Highlights</div>
                        
                        <div class="p-3 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold">30%</div>
                                <div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Annual Plans</div>
                                    <div class="text-[10px] text-zinc-400">Recurring every billing cycle</div>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-bold text-zinc-900 dark:text-zinc-100">Top Earner</span>
                        </div>

                        <div class="p-3 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold">20%</div>
                                <div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Monthly Plans</div>
                                    <div class="text-[10px] text-zinc-400">Continuous cash flow</div>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-medium text-zinc-500">Recurring</span>
                        </div>

                        <div class="p-3 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold">100</div>
                                <div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">AI Runes on Signups</div>
                                    <div class="text-[10px] text-zinc-400">Awarded immediately</div>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-bold text-zinc-900 dark:text-zinc-100">Free Bonus</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- ================================================================= -->
    <!-- VIEW B: ACTIVE PARTNER GAMIFIED DASHBOARD & LEADERBOARD (ENROLLED) -->
    <!-- ================================================================= -->
    <div id="cora-affiliate-dashboard-view" class="<?php echo ! $is_enrolled ? 'hidden' : ''; ?> space-y-4 max-w-full">

<?php
$affiliate_header_args = array(
    'title'            => 'Affiliates & Partner Network',
    'description'      => '30% Annual • 20% Monthly • 100 Free AI Runes per signup + Milestone Bonuses.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line></svg>',
    'ai_stack'         => true,
    'cta'              => array(
        'text'        => 'Copy Link',
        'mobile_text' => 'Copy Link',
        'onclick'     => 'coraCopyReferralLink()',
        'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>',
        'visible'     => true,
    ),
    'extra_actions_html' => '<button type="button" onclick="coraOpenAffiliatePayoutDrawer()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-zinc-800 dark:text-zinc-200 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 rounded-xl transition-all cursor-pointer"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg><span>Withdraw (' . esc_html( $curr_sym . number_format( $avail_bal, 0 ) ) . ')</span></button>',
    'show_extra_actions_on_mobile' => true,
    'tabs_dom_id'      => 'cora-affiliate-tabs-bar',
    'tabs'             => array(
        array(
            'id'       => 'overview',
            'dom_id'   => 'cora-tab-btn-overview',
            'label'    => 'Overview & Meter',
            'icon'     => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>',
            'active'   => true,
            'onclick'  => "window.coraSwitchAffiliateTab('overview')",
        ),
        array(
            'id'       => 'leaderboard',
            'dom_id'   => 'cora-tab-btn-leaderboard',
            'label'    => 'Leaderboard & Ranks',
            'badge'    => '<span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-200/70 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">Podium</span>',
            'icon'     => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.45 1-1 1H7c-.55 0-1-.45-1-1v-2.34"></path><path d="M18 14.66V17c0 .55-.45 1-1 1h-2c-.55 0-1-.45-1-1v-2.34"></path><path d="M6 9v1a6 6 0 0 0 12 0V9H6z"></path></svg>',
            'active'   => false,
            'onclick'  => "window.coraSwitchAffiliateTab('leaderboard')",
        ),
        array(
            'id'       => 'calculator',
            'dom_id'   => 'cora-tab-btn-calculator',
            'label'    => 'Earnings Simulator',
            'icon'     => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="16" y1="14" x2="16" y2="18"></line><path d="M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M8 18h.01M12 18h.01"></path></svg>',
            'active'   => false,
            'onclick'  => "window.coraSwitchAffiliateTab('calculator')",
        ),
        array(
            'id'       => 'referrals',
            'dom_id'   => 'cora-tab-btn-referrals',
            'label'    => 'Referrals & Logs',
            'badge'    => '<span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-zinc-200/70 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">' . count( $referrals ) . '</span>',
            'icon'     => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
            'active'   => false,
            'onclick'  => "window.coraSwitchAffiliateTab('referrals')",
        ),
        array(
            'id'       => 'payouts',
            'dom_id'   => 'cora-tab-btn-payouts',
            'label'    => 'Payouts & Banking',
            'icon'     => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>',
            'active'   => false,
            'onclick'  => "window.coraSwitchAffiliateTab('payouts')",
        ),
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $affiliate_header_args );
}
?>

        <!-- ─────────────────────────────────────────────────────────────────── -->
        <!-- SUB-TAB 1: OVERVIEW & GAMIFIED EARNINGS METER                       -->
        <!-- ─────────────────────────────────────────────────────────────────── -->
        <div id="cora-aff-subtab-overview" class="cora-aff-subtab space-y-4">
            
            <!-- HeroUI Card: Partner Tier Progression Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 sm:p-6 shadow-3xs space-y-5">
                <!-- CardHeader: Earnings & Current Tier -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-400 font-bold">Partner Tier Progression</span>
                            <!-- HeroUI Chip: Streak Indicator -->
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>3-Streak Active</span>
                            </span>
                        </div>
                        <div class="flex items-baseline gap-2.5">
                            <span class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 font-mono tracking-tight"><?php echo esc_html( $curr_sym . number_format( $total_comm, 2 ) ); ?></span>
                            <span class="text-xs text-zinc-400 font-medium">Total Lifetime Earnings</span>
                        </div>
                    </div>

                    <!-- HeroUI User / Tier Badge -->
                    <div class="flex items-center gap-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl px-3.5 py-2.5 shrink-0">
                        <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-lg shrink-0">
                            <?php echo esc_html( $current_tier_icon ); ?>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $current_tier_name ); ?></div>
                            <div class="text-[10px] text-zinc-400 font-medium">30% Annual • 20% Monthly Rate</div>
                        </div>
                    </div>
                </div>

                <!-- HeroUI Progress Bar -->
                <div class="space-y-2 pt-1">
                    <div class="flex items-center justify-between text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        <span>Level: <strong class="text-zinc-900 dark:text-zinc-100 font-semibold"><?php echo esc_html( $current_tier_name ); ?></strong></span>
                        <span>Target: <strong class="text-zinc-900 dark:text-zinc-100 font-semibold"><?php echo esc_html( $next_tier_name ); ?></strong></span>
                    </div>

                    <!-- Progress Track & Fill -->
                    <div class="w-full h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden p-0.5">
                        <div class="h-full bg-zinc-900 dark:bg-zinc-100 rounded-full transition-all duration-700 ease-out" style="width: <?php echo esc_attr( $tier_progress_pct ); ?>%"></div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-zinc-400 font-mono">
                        <span><?php echo esc_html( $tier_progress_pct ); ?>% Completed</span>
                        <?php if ( $to_next_tier > 0 ) : ?>
                            <span><?php echo esc_html( $curr_sym . number_format( $to_next_tier, 0 ) ); ?> to unlock next upgrade</span>
                        <?php else : ?>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Apex Tier Achieved 🎉</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- CardFooter: 3 Milestone Unlocks Grid (HeroUI Flat Card variant) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-3 border-t border-zinc-100 dark:border-zinc-800/80">
                    <div class="p-3 rounded-xl <?php echo $paid_signups >= 10 ? 'bg-zinc-100/90 dark:bg-zinc-800/80 text-zinc-900 dark:text-zinc-100' : 'bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400'; ?> border border-zinc-200/70 dark:border-zinc-800 space-y-1">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="flex items-center gap-1.5">
                                <span>🎯</span>
                                <span>10 Paid Referrals</span>
                            </span>
                            <span class="font-mono text-xs"><?php echo $is_india_geo ? '+₹1,000' : '+$10'; ?></span>
                        </div>
                        <div class="text-[10px] font-mono <?php echo $paid_signups >= 10 ? 'text-zinc-900 dark:text-zinc-100 font-bold' : 'text-zinc-400'; ?>">
                            <?php echo $paid_signups >= 10 ? '✓ Milestone Unlocked' : ((10 - $paid_signups) . ' more needed'); ?>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl <?php echo $paid_signups >= 50 ? 'bg-zinc-100/90 dark:bg-zinc-800/80 text-zinc-900 dark:text-zinc-100' : 'bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400'; ?> border border-zinc-200/70 dark:border-zinc-800 space-y-1">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="flex items-center gap-1.5">
                                <span>🚀</span>
                                <span>50 Paid Referrals</span>
                            </span>
                            <span class="font-mono text-xs"><?php echo $is_india_geo ? '+₹5,000' : '+$50'; ?></span>
                        </div>
                        <div class="text-[10px] font-mono <?php echo $paid_signups >= 50 ? 'text-zinc-900 dark:text-zinc-100 font-bold' : 'text-zinc-400'; ?>">
                            <?php echo $paid_signups >= 50 ? '✓ Milestone Unlocked' : ((50 - $paid_signups) . ' more needed'); ?>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl <?php echo $paid_signups >= 100 ? 'bg-zinc-100/90 dark:bg-zinc-800/80 text-zinc-900 dark:text-zinc-100' : 'bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400'; ?> border border-zinc-200/70 dark:border-zinc-800 space-y-1">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="flex items-center gap-1.5">
                                <span>👑</span>
                                <span>100 Paid Referrals</span>
                            </span>
                            <span class="font-mono text-xs"><?php echo $is_india_geo ? '+₹10,000' : '+$100'; ?></span>
                        </div>
                        <div class="text-[10px] font-mono <?php echo $paid_signups >= 100 ? 'text-zinc-900 dark:text-zinc-100 font-bold' : 'text-zinc-400'; ?>">
                            <?php echo $paid_signups >= 100 ? '✓ Milestone Unlocked' : ((100 - $paid_signups) . ' more needed'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ShadCN / HeroUI 4-Metric Decision KPI Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                
                <!-- KPI 1: Available Balance -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs flex flex-col justify-between space-y-2">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Available Balance</span>
                        <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-bold font-mono text-zinc-950 dark:text-zinc-50 tracking-tight"><?php echo esc_html( $curr_sym . number_format( $avail_bal, 2 ) ); ?></div>
                        <div class="flex items-center justify-between text-[10px] text-zinc-400 mt-1.5 pt-1.5 border-t border-zinc-100 dark:border-zinc-800/80">
                            <span>Min: <?php echo esc_html( $curr_sym . number_format( $min_payout, 0 ) ); ?></span>
                            <button type="button" onclick="coraOpenAffiliatePayoutDrawer()" class="font-bold text-zinc-900 dark:text-zinc-100 hover:underline cursor-pointer">Withdraw &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- KPI 2: AI Runes Granted -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs flex flex-col justify-between space-y-2">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-[10px] font-bold uppercase tracking-wider">AI Runes Granted</span>
                        <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-xs text-zinc-700 dark:text-zinc-300">
                            ⚡
                        </div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-bold font-mono text-zinc-950 dark:text-zinc-50 tracking-tight"><?php echo number_format( $ai_credits ); ?></div>
                        <div class="text-[10px] text-zinc-400 mt-1.5 pt-1.5 border-t border-zinc-100 dark:border-zinc-800/80">
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">+100</span> per free signup
                        </div>
                    </div>
                </div>

                <!-- KPI 3: Paid Referrals -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs flex flex-col justify-between space-y-2">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Paid Conversions</span>
                        <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-bold font-mono text-zinc-950 dark:text-zinc-50 tracking-tight"><?php echo (int) $paid_signups; ?> <span class="text-xs font-normal text-zinc-400">/ <?php echo (int) $affiliate_data['total_conversions']; ?></span></div>
                        <div class="text-[10px] text-zinc-400 mt-1.5 pt-1.5 border-t border-zinc-100 dark:border-zinc-800/80">
                            <?php echo (int) $free_signups; ?> active free trials
                        </div>
                    </div>
                </div>

                <!-- KPI 4: Conversion Rate -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs flex flex-col justify-between space-y-2">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Conversion Rate</span>
                        <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-bold font-mono text-zinc-950 dark:text-zinc-50 tracking-tight"><?php echo number_format( $conv_rate, 1 ); ?>%</div>
                        <div class="text-[10px] text-zinc-400 mt-1.5 pt-1.5 border-t border-zinc-100 dark:border-zinc-800/80 font-mono">
                            <?php echo (int) $clicks; ?> clicks • <?php echo (int) $unique_visits; ?> visits
                        </div>
                    </div>
                </div>

            </div>

            <!-- HeroUI Snippet: Dedicated Referral Link & Multi-Channel Sharing -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-3xs space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Link Snippet Block -->
                    <div class="space-y-2 flex-1 max-w-xl">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Your Dedicated Referral Link</span>
                            <!-- HeroUI Chip -->
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-700/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>30-Day Cookie Active</span>
                            </span>
                        </div>

                        <!-- HeroUI Snippet Container -->
                        <div class="flex items-center justify-between p-2 pl-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl gap-2">
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                <svg class="w-4 h-4 text-zinc-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                <input id="cora-ref-link-input" type="text" readonly value="<?php echo esc_attr( $ref_url ); ?>" class="w-full text-xs font-mono bg-transparent text-zinc-900 dark:text-zinc-100 select-all focus:outline-none">
                            </div>
                            <button type="button" id="cora-copy-link-btn" onclick="coraCopyReferralLink(this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-950 rounded-lg shadow-2xs transition-all cursor-pointer shrink-0 active:scale-95">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                <span>Copy Link</span>
                            </button>
                        </div>
                    </div>

                    <!-- Multi-Channel Share Buttons (HeroUI ButtonGroup variant) -->
                    <div class="flex flex-wrap items-center gap-2 pt-2 lg:pt-0 lg:border-l lg:border-zinc-100 lg:dark:border-zinc-800 lg:pl-5">
                        <button type="button" onclick="coraShareWhatsApp()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer active:scale-95">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm0 18.16c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.32a8.21 8.21 0 0 1-1.25-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.26.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24zm4.52-6.16c-.25-.12-1.47-.72-1.69-.8-.23-.09-.39-.13-.56.12-.16.24-.63.8-.78.96-.14.16-.29.18-.54.06-.25-.12-1.05-.38-2-1.23-.74-.66-1.24-1.47-1.39-1.72-.14-.24-.01-.37.11-.49.11-.11.25-.29.37-.43.13-.14.17-.25.25-.41.08-.16.04-.3-.02-.42s-.51-1.2-.7-1.7c-.19-.48-.39-.42-.54-.43h-.46c-.16 0-.42.06-.65.31-.22.24-.86.84-.86 2.06 0 1.22.89 2.4 1.01 2.56.13.17 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.53.6.19 1.14.16 1.57.1.48-.07 1.48-.61 1.69-1.2.2-.59.2-1.09.14-1.2-.06-.11-.21-.17-.46-.29z"/></svg>
                            <span>WhatsApp</span>
                        </button>

                        <button type="button" onclick="coraShareLinkedIn()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer active:scale-95">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.2V10.9H6.46M7.83 6.25c-.9 0-1.63.73-1.63 1.63s.73 1.63 1.63 1.63a1.63 1.63 0 0 0 1.63-1.63c0-.9-.73-1.63-1.63-1.63z"/></svg>
                            <span>LinkedIn</span>
                        </button>

                        <button type="button" onclick="coraShareTwitter()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer active:scale-95">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            <span>X</span>
                        </button>

                        <button type="button" onclick="coraShareEmail()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer active:scale-95">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <span>Email</span>
                        </button>

                        <button type="button" onclick="coraOpenQRCodeModal()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer active:scale-95">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm1 1h2v2H5V5zm9-3h8v8h-8V2zm2 2v4h4V4h-4zm1 1h2v2h-2V5zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm1 1h2v2H5v-2zm9-1h2v2h-2v-2zm4 0h2v2h-2v-2zm-4 4h2v2h-2v-2zm2-2h2v2h-2v-2zm2 2h2v2h-2v-2zm-6 2h2v2h-2v-2zm6 0h2v2h-2v-2zm-2-6h2v2h-2v-2z"/></svg>
                            <span>QR Code</span>
                        </button>
                    </div>
                </div>

                <!-- Custom Slug Form with Live URL Preview (ShadCN Form Layout) -->
                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800/70 space-y-2.5 text-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-500 dark:text-zinc-400 font-medium">Custom Affiliate Handle:</span>
                            <span id="cora-display-slug" class="font-mono font-bold text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 px-2.5 py-0.5 rounded-lg border border-zinc-200/60 dark:border-zinc-700/60"><?php echo esc_html( $custom_slug ); ?></span>
                            <button type="button" id="cora-btn-customize-slug" onclick="coraToggleSlugEditor()" class="text-zinc-600 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-zinc-100 underline font-medium cursor-pointer">Customize</button>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-mono" id="cora-live-slug-preview-bar">
                            <span>Link Preview: </span><code class="text-zinc-700 dark:text-zinc-300 font-bold" id="cora-display-slug-url"><?php echo esc_html( home_url( '/?ref=' . $custom_slug ) ); ?></code>
                        </div>
                    </div>

                    <div id="cora-slug-editor" class="hidden p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <div class="flex items-center flex-1">
                                <span class="px-3 py-2 text-xs bg-zinc-100 dark:bg-zinc-800 border border-r-0 border-zinc-200 dark:border-zinc-700 rounded-l-xl text-zinc-500 font-mono text-[11px] shrink-0"><?php echo esc_html( home_url( '/?ref=' ) ); ?></span>
                                <input id="cora-custom-slug-input" type="text" oninput="coraOnSlugInput(this.value)" placeholder="e.g. zenith-studio" value="<?php echo esc_attr( $custom_slug ); ?>" class="w-full px-3 py-2 text-xs bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-r-xl text-zinc-900 dark:text-zinc-100 font-mono font-bold focus:outline-none">
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="cora-save-slug-btn" onclick="coraSaveCustomSlug()" class="px-4 py-2 text-xs font-semibold bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 rounded-xl shadow-2xs transition-all cursor-pointer">Save Handle</button>
                                <button type="button" onclick="coraToggleSlugEditor(false)" class="px-3 py-2 text-xs text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 cursor-pointer">Cancel</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-zinc-400">
                            <span>Use 3-32 alphanumeric characters (letters, numbers, hyphens, underscores).</span>
                            <span id="cora-slug-char-count" class="font-mono"><?php echo strlen( $custom_slug ); ?>/32</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HeroUI User List: Recent Referral Activity -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-3xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-zinc-100 dark:border-zinc-800/80">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-50">Recent Referral Activity</h3>
                        <p class="text-[11px] text-zinc-400">Latest clients that converted through your link.</p>
                    </div>
                    <button type="button" onclick="window.coraSwitchAffiliateTab('referrals')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:underline cursor-pointer">View Complete Ledger &rarr;</button>
                </div>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                    <?php if ( ! empty( $referrals ) ) : ?>
                        <?php 
                        $recent_slice = array_slice( $referrals, 0, 4 );
                        foreach ( $recent_slice as $ref ) : 
                            $is_paid = $ref['conversion_type'] === 'paid_conversion';
                            $time_ago = human_time_diff( strtotime( $ref['created_at'] ), current_time( 'timestamp' ) ) . ' ago';
                        ?>
                        <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- HeroUI User Avatar -->
                                <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60 flex items-center justify-center font-bold text-xs text-zinc-800 dark:text-zinc-200 shrink-0">
                                    <?php echo esc_html( strtoupper( substr( $ref['referred_name'] ?? 'P', 0, 1 ) ) ); ?>
                                </div>
                                <div class="truncate">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html( $ref['referred_name'] ); ?></div>
                                    <div class="text-[10px] text-zinc-400 font-mono"><?php echo esc_html( $time_ago ); ?> • <?php echo esc_html( $ref['plan_name'] ); ?></div>
                                </div>
                            </div>
                            <div class="text-right shrink-0 font-mono">
                                <?php if ( $is_paid ) : ?>
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100">+<?php echo esc_html( $curr_sym . number_format( (float) $ref['commission_earned'], 2 ) ); ?></div>
                                    <div class="text-[10px] text-zinc-400">Commission</div>
                                <?php else : ?>
                                    <div class="font-bold text-zinc-800 dark:text-zinc-200">+100 Runes</div>
                                    <div class="text-[10px] text-zinc-400">Free Trial</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="py-4 text-center text-zinc-400 text-xs">
                            No referrals logged yet. Share your link above to start earning!
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- ─────────────────────────────────────────────────────────────────── -->
        <!-- SUB-TAB 2: PARTNER LEADERBOARD & SPRINT CHALLENGE                   -->
        <!-- ─────────────────────────────────────────────────────────────────── -->
        <div id="cora-aff-subtab-leaderboard" class="cora-aff-subtab hidden space-y-4">
            
            <!-- HeroUI Card: Monthly Sprint Challenge Banner -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-3xs space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 uppercase tracking-wider border border-zinc-200/60 dark:border-zinc-700/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Active Monthly Sprint</span>
                            </span>
                            <span class="text-xs text-zinc-400 font-mono">Ends in 8 days</span>
                        </div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mt-1.5">September Partner Sprint 🚀</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Reach 10 paid studio clients to unlock <strong class="text-zinc-900 dark:text-zinc-100"><?php echo $is_india_geo ? '₹1,000' : '$10'; ?> Milestone Bonus</strong> + <strong class="text-zinc-900 dark:text-zinc-100">1,000 Extra AI Runes</strong>.</p>
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl px-4 py-2.5 text-center sm:text-right shrink-0">
                        <div class="text-[10px] uppercase font-bold text-zinc-400">Sprint Target</div>
                        <div class="text-lg font-bold text-zinc-950 dark:text-zinc-50 font-mono"><?php echo (int) $paid_signups; ?> <span class="text-xs text-zinc-400 font-normal">/ 10</span></div>
                    </div>
                </div>

                <!-- HeroUI Progress Bar -->
                <div class="w-full h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden p-0.5">
                    <div class="h-full bg-zinc-950 dark:bg-zinc-100 rounded-full transition-all duration-500 ease-out" style="width: <?php echo esc_attr( min( 100, max( 10, round( ( $paid_signups / 10 ) * 100 ) ) ) ); ?>%"></div>
                </div>
            </div>

            <!-- Top 3 Podium Cards (HeroUI Elevated Podium) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                
                <!-- 2nd Place (Silver) -->
                <div class="order-2 md:order-1 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xl">🥈</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60">Rank #2</span>
                        </div>
                        <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100">LensCraft_BLR</div>
                        <div class="text-[10px] text-zinc-400 font-mono">Bangalore Creative Hub</div>
                    </div>
                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between font-mono text-xs">
                        <span class="text-zinc-400">18 Referrals</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">₹71,964.00</span>
                    </div>
                </div>

                <!-- 1st Place (Gold Champion) -->
                <div class="order-1 md:order-2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-2xl p-5 shadow-2xs flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">🥇</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950">Rank #1 Champion</span>
                        </div>
                        <div class="font-bold text-sm text-zinc-950 dark:text-zinc-50">ApexMedia_Mumbai</div>
                        <div class="text-[10px] text-zinc-400 font-mono">Enterprise Agency Partner</div>
                    </div>
                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between font-mono text-xs">
                        <span class="text-zinc-500 font-semibold">29 Referrals</span>
                        <span class="font-bold text-zinc-950 dark:text-zinc-50 text-sm">₹1,15,942.00</span>
                    </div>
                </div>

                <!-- 3rd Place (Bronze) -->
                <div class="order-3 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xl">🥉</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60">Rank #3</span>
                        </div>
                        <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100">StudioPro_DL</div>
                        <div class="text-[10px] text-zinc-400 font-mono">Delhi Production Suite</div>
                    </div>
                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between font-mono text-xs">
                        <span class="text-zinc-400">12 Referrals</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">₹47,976.00</span>
                    </div>
                </div>

            </div>

            <!-- Your Standing Card (HeroUI Tonal Highlight) -->
            <div class="bg-zinc-100/90 dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/80 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 flex items-center justify-center font-bold text-xs shadow-2xs">
                        #4
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Your Standing (<span class="cora-standing-slug"><?php echo esc_html( $custom_slug ); ?></span>)</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">Rising +2</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">7 more paid referrals to enter the Top 3 Podium!</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 text-xs font-mono self-end sm:self-auto">
                    <div>
                        <div class="text-[10px] text-zinc-400 uppercase">Referrals</div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100"><?php echo (int) $paid_signups; ?> Paid</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-zinc-400 uppercase">Earnings</div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $curr_sym . number_format( $total_comm, 2 ) ); ?></div>
                    </div>
                </div>
            </div>

            <!-- ShadCN UI Table: Full Leaderboard List -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-3xs overflow-hidden">
                <div class="p-4 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-50">All-Time Partner Network Ranking</h3>
                        <p class="text-[11px] text-zinc-400">Updated hourly based on verified client conversions.</p>
                    </div>
                    <span class="text-[10px] font-mono text-zinc-400">Global Registry</span>
                </div>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-xs">
                    <!-- Rank 1 -->
                    <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="font-mono font-bold text-sm w-7 text-zinc-900 dark:text-zinc-100">🥇 #1</span>
                            <div class="truncate">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate">ApexMedia_Mumbai</div>
                                <div class="text-[10px] text-zinc-400 font-mono">💎 Diamond Titan</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 font-mono">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100">₹1,15,942.00</div>
                            <div class="text-[10px] text-zinc-400">29 Referrals</div>
                        </div>
                    </div>

                    <!-- Rank 2 -->
                    <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="font-mono font-bold text-sm w-7 text-zinc-700 dark:text-zinc-300">🥈 #2</span>
                            <div class="truncate">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate">LensCraft_BLR</div>
                                <div class="text-[10px] text-zinc-400 font-mono">🥇 Gold Ambassador</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 font-mono">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100">₹71,964.00</div>
                            <div class="text-[10px] text-zinc-400">18 Referrals</div>
                        </div>
                    </div>

                    <!-- Rank 3 -->
                    <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="font-mono font-bold text-sm w-7 text-zinc-700 dark:text-zinc-300">🥉 #3</span>
                            <div class="truncate">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate">StudioPro_DL</div>
                                <div class="text-[10px] text-zinc-400 font-mono">🥈 Silver Creator</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 font-mono">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100">₹47,976.00</div>
                            <div class="text-[10px] text-zinc-400">12 Referrals</div>
                        </div>
                    </div>

                    <!-- Rank 4: Current User -->
                    <div class="p-3.5 flex items-center justify-between gap-3 bg-zinc-100/80 dark:bg-zinc-800/70">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="font-mono font-bold text-sm w-7 text-zinc-900 dark:text-zinc-100">#4</span>
                            <div class="truncate">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5 truncate">
                                    <span class="cora-standing-slug"><?php echo esc_html( $custom_slug ); ?></span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950">You</span>
                                </div>
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400 font-mono">🥉 Bronze Partner</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 font-mono">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $curr_sym . number_format( $total_comm, 2 ) ); ?></div>
                            <div class="text-[10px] text-zinc-500 dark:text-zinc-400"><?php echo (int) $paid_signups; ?> Referrals</div>
                        </div>
                    </div>

                    <!-- Rank 5 -->
                    <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="font-mono text-zinc-400 text-sm w-7">#5</span>
                            <div class="truncate">
                                <div class="text-zinc-800 dark:text-zinc-200 font-medium truncate">VerveFilms_HYD</div>
                                <div class="text-[10px] text-zinc-400 font-mono">🥉 Bronze Partner</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 font-mono">
                            <div class="text-zinc-800 dark:text-zinc-200 font-semibold">₹7,992.00</div>
                            <div class="text-[10px] text-zinc-400">2 Referrals</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ─────────────────────────────────────────────────────────────────── -->
        <!-- SUB-TAB 3: EFFORTLESS GAMIFIED EARNINGS SIMULATOR                   -->
        <!-- ─────────────────────────────────────────────────────────────────── -->
        <div id="cora-aff-subtab-calculator" class="cora-aff-subtab hidden space-y-4">
            
            <!-- HeroUI Card: Simulator Controls -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 sm:p-6 shadow-3xs space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Earnings Potential Calculator</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Simulate recurring payouts + milestone cash bonuses + AI Runes.</p>
                    </div>

                    <!-- HeroUI Segmented Control -->
                    <div class="inline-flex p-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-xs self-start sm:self-auto border border-zinc-200/60 dark:border-zinc-700/60">
                        <button type="button" onclick="coraSetBillingMode('annual')" id="cora-mode-annual" class="px-3 py-1.5 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 shadow-2xs cursor-pointer transition-all">Annual (30%)</button>
                        <button type="button" onclick="coraSetBillingMode('monthly')" id="cora-mode-monthly" class="px-3 py-1.5 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all">Monthly (20%)</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Client Plan</label>
                        <select id="cora-sim-tier" onchange="coraRecalculateSimulator()" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-800 rounded-xl focus:outline-none cursor-pointer font-medium">
                            <?php if ( $is_india_geo ) : ?>
                                <option value="19990" data-monthly-price="1999" data-annual-price="19990" data-curr="₹" selected>Professional Tier (₹1,999/mo • ₹19,990/yr)</option>
                                <option value="9990" data-monthly-price="999" data-annual-price="9990" data-curr="₹">Starter Tier (₹999/mo • ₹9,990/yr)</option>
                                <option value="29990" data-monthly-price="2999" data-annual-price="29990" data-curr="₹">Scale Tier (₹2,999/mo • ₹29,990/yr)</option>
                                <option value="5988" data-monthly-price="499" data-annual-price="5988" data-curr="₹">India Only Plan (₹499/mo • ₹5,988/yr)</option>
                            <?php else : ?>
                                <option value="190" data-monthly-price="19" data-annual-price="190" data-curr="$" selected>Professional Global ($19/mo • $190/yr)</option>
                                <option value="90" data-monthly-price="9" data-annual-price="90" data-curr="$">Starter Global ($9/mo • $90/yr)</option>
                                <option value="290" data-monthly-price="29" data-annual-price="290" data-curr="$">Scale Global ($29/mo • $290/yr)</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- HeroUI Slider -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs font-medium text-zinc-700 dark:text-zinc-300">
                            <span>Referred Clients Count</span>
                            <span id="cora-sim-clients-badge" class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-200/60 dark:border-zinc-700/60">10 Studios</span>
                        </div>
                        <input id="cora-sim-range" type="range" min="1" max="100" value="10" oninput="coraRecalculateSimulator()" class="heroui-slider-track cursor-pointer">
                        <div class="flex items-center justify-between text-[10px] text-zinc-400 font-mono">
                            <span>1 Studio</span>
                            <span>50 Studios</span>
                            <span>100 Studios</span>
                        </div>
                    </div>
                </div>

                <!-- Projection Hero Card (HeroUI Elevated Card) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
                    <div class="md:col-span-2 p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl flex flex-col justify-between space-y-3">
                        <div>
                            <span id="cora-sim-payout-label" class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Est. Annual Payout (30%)</span>
                            <div id="cora-sim-monthly-cash" class="text-2xl sm:text-3xl font-extrabold font-mono text-zinc-950 dark:text-zinc-50 tracking-tight mt-1"><?php echo $is_india_geo ? '₹60,970' : '$670'; ?></div>
                            <div id="cora-sim-yearly-cash" class="text-xs text-zinc-400 font-mono mt-0.5"><?php echo $is_india_geo ? '₹5,081/mo equiv' : '$56/mo equiv'; ?></div>
                        </div>

                        <div class="pt-2 border-t border-zinc-200/60 dark:border-zinc-800 flex items-center justify-between text-xs">
                            <span class="text-zinc-500 dark:text-zinc-400">Milestone Cash Bonus:</span>
                            <span id="cora-sim-bonus-cash" class="font-mono font-bold text-zinc-900 dark:text-zinc-100"><?php echo $is_india_geo ? '+₹1,000' : '+$10'; ?></span>
                        </div>
                    </div>

                    <!-- AI Runes Bonus Card -->
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl flex flex-col justify-between space-y-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Free AI Runes</span>
                        <div>
                            <div id="cora-sim-runes-val" class="text-2xl font-extrabold font-mono text-zinc-950 dark:text-zinc-50 tracking-tight">+1,000</div>
                            <div class="text-[10px] text-zinc-400 mt-1">+100 Runes per signup</div>
                        </div>
                        <div class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400 pt-2 border-t border-zinc-200/60 dark:border-zinc-800">
                            Instant AI credit bonus
                        </div>
                    </div>
                </div>

                <!-- 4 Plan Benchmark Matrix -->
                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800/80 grid grid-cols-2 lg:grid-cols-4 gap-2.5 text-xs">
                    <?php if ( $is_india_geo ) : ?>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-1 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">India Only</div>
                            <div class="text-zinc-400 font-mono text-[10px]">₹499/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>₹1,796</strong>/yr</div>
                        </div>

                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-1 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Starter Tier</div>
                            <div class="text-zinc-400 font-mono text-[10px]">₹999/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>₹2,997</strong>/yr</div>
                        </div>

                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-1 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Professional</div>
                            <div class="text-zinc-400 font-mono text-[10px]">₹1,999/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>₹5,997</strong>/yr</div>
                        </div>

                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-1 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Scale Tier</div>
                            <div class="text-zinc-400 font-mono text-[10px]">₹2,999/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>₹8,997</strong>/yr</div>
                        </div>
                    <?php else : ?>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-1 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Starter Global</div>
                            <div class="text-zinc-400 font-mono text-[10px]">$9/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>$27</strong>/yr</div>
                        </div>

                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-1 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Professional Global</div>
                            <div class="text-zinc-400 font-mono text-[10px]">$19/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>$57</strong>/yr</div>
                        </div>

                        <div class="p-3 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-1 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Scale Global</div>
                            <div class="text-zinc-400 font-mono text-[10px]">$29/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>$87</strong>/yr</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- ─────────────────────────────────────────────────────────────────── -->
        <!-- SUB-TAB 4: REFERRALS ACTIVITY & TRACKER LOGS                        -->
        <!-- ─────────────────────────────────────────────────────────────────── -->
        <div id="cora-aff-subtab-referrals" class="cora-aff-subtab hidden space-y-4">
            
            <!-- ShadCN UI Table: Conversion Activity Ledger -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-3xs overflow-hidden">
                <div class="p-4 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-50">Conversion Activity Ledger</h3>
                        <p class="text-[11px] text-zinc-400">Real-time log of all referred signups, plan conversions, and earned rewards.</p>
                    </div>

                    <!-- Filter Pill Segmented Control -->
                    <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-xl self-start sm:self-auto border border-zinc-200/60 dark:border-zinc-700/60">
                        <button type="button" onclick="coraFilterReferralTable('all')" id="cora-filter-all" class="px-3 py-1 text-xs font-bold rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs transition-all cursor-pointer">All</button>
                        <button type="button" onclick="coraFilterReferralTable('paid')" id="cora-filter-paid" class="px-3 py-1 text-xs font-medium rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer">Paid</button>
                        <button type="button" onclick="coraFilterReferralTable('free')" id="cora-filter-free" class="px-3 py-1 text-xs font-medium rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer">Free (100 Runes)</button>
                    </div>
                </div>

                <!-- Ledger Row List -->
                <div id="cora-referral-list-container" class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-xs">
                    <?php if ( ! empty( $referrals ) ) : ?>
                        <?php foreach ( $referrals as $ref ) : 
                            $is_paid = $ref['conversion_type'] === 'paid_conversion';
                            $row_class = $is_paid ? 'cora-row-paid' : 'cora-row-free';
                            $time_ago = human_time_diff( strtotime( $ref['created_at'] ), current_time( 'timestamp' ) ) . ' ago';
                        ?>
                        <div class="cora-ref-item p-3.5 flex items-center justify-between gap-3 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors <?php echo esc_attr( $row_class ); ?>">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60 flex items-center justify-center font-bold text-xs text-zinc-700 dark:text-zinc-300 shrink-0">
                                    <?php echo esc_html( strtoupper( substr( $ref['referred_name'] ?? 'P', 0, 1 ) ) ); ?>
                                </div>
                                <div class="truncate">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html( $ref['referred_name'] ); ?></div>
                                    <div class="text-[10px] text-zinc-400 font-mono truncate"><?php echo esc_html( $ref['referred_email'] ); ?> • <?php echo esc_html( $time_ago ); ?></div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3.5 shrink-0">
                                <div class="hidden sm:block text-right">
                                    <?php if ( $is_paid ) : ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200/60 dark:border-zinc-700/60">
                                            <?php echo esc_html( $ref['plan_name'] ); ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-700/60">
                                            Free Signup
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="text-right font-mono">
                                    <?php if ( $is_paid ) : ?>
                                        <div class="font-bold text-zinc-900 dark:text-zinc-100">+<?php echo esc_html( $curr_sym . number_format( (float) $ref['commission_earned'], 2 ) ); ?></div>
                                        <div class="text-[10px] text-zinc-400">Paid Commission</div>
                                    <?php else : ?>
                                        <div class="font-bold text-zinc-800 dark:text-zinc-200">+100 Runes</div>
                                        <div class="text-[10px] text-zinc-400">Free Trial</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="p-6 text-center text-zinc-400 text-xs">
                            No referral conversions recorded yet. Share your referral link to start earning!
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- ─────────────────────────────────────────────────────────────────── -->
        <!-- SUB-TAB 5: PAYOUTS & BANKING DISBURSEMENTS                          -->
        <!-- ─────────────────────────────────────────────────────────────────── -->
        <div id="cora-aff-subtab-payouts" class="cora-aff-subtab hidden space-y-4">
            
            <!-- HeroUI Card: Available Balance & Threshold -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-3xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Available Payout Balance</span>
                        <div class="text-2xl sm:text-3xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight mt-0.5"><?php echo esc_html( $curr_sym . number_format( $avail_bal, 2 ) ); ?></div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Direct transfer to your verified UPI ID or Bank Wire Account.</p>
                    </div>

                    <button type="button" onclick="coraOpenAffiliatePayoutDrawer()" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 rounded-xl shadow-2xs transition-all cursor-pointer self-start sm:self-auto active:scale-95">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        <span>Request Payout</span>
                    </button>
                </div>

                <!-- Threshold Progress -->
                <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                    <div class="flex items-center justify-between text-xs font-mono text-zinc-500 dark:text-zinc-400">
                        <span>Withdrawal Threshold</span>
                        <span><?php echo esc_html( $curr_sym . number_format( $avail_bal, 0 ) ); ?> / <?php echo esc_html( $curr_sym . number_format( $min_payout, 0 ) ); ?> Min</span>
                    </div>

                    <div class="w-full h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden p-0.5">
                        <div class="h-full bg-zinc-900 dark:bg-zinc-100 rounded-full transition-all duration-500 ease-out" style="width: <?php echo esc_attr( $payout_progress_pct ); ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Settlement Options (HeroUI Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-3xs space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-xs">⚡</div>
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">UPI Instant Settlement</div>
                            <div class="text-[10px] text-zinc-400">Disbursed directly within 4-6 business hours</div>
                        </div>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Supported by PhonePe, Google Pay, Paytm, and all NPCI certified UPI handles.</p>
                </div>

                <div class="p-4 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-3xs space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-xs">🏦</div>
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Direct Bank Wire (NEFT/RTGS)</div>
                            <div class="text-[10px] text-zinc-400">Processed on 1st &amp; 15th of every month</div>
                        </div>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Transferred directly to your company or personal bank checking account with zero gateway fee deductions.</p>
                </div>
            </div>

            <!-- Payout & Withdrawal History (ShadCN Table) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-3xs overflow-hidden">
                <div class="p-4 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-50">Payout &amp; Withdrawal History</h3>
                        <p class="text-[11px] text-zinc-400">Complete audit log of all processed bank transfers and UPI disbursements.</p>
                    </div>
                    <button type="button" onclick="coraOpenAffiliatePayoutDrawer()" class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 hover:underline cursor-pointer">
                        + Request
                    </button>
                </div>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-xs">
                    <?php if ( ! empty( $payouts ) ) : ?>
                        <?php foreach ( $payouts as $p ) : 
                            $status_badge = $p['status'] === 'completed' 
                                ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border-zinc-200/60 dark:border-zinc-700/60' 
                                : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-200/60 dark:border-zinc-700/60';
                        ?>
                        <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors">
                            <div class="min-w-0">
                                <div class="font-mono font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                    <?php echo esc_html( $p['transaction_ref'] ?? ('TXN-' . substr( md5( (string) $p['id'] ), 0, 8 )) ); ?>
                                </div>
                                <div class="text-[10px] text-zinc-400 font-mono">
                                    <?php echo esc_html( gmdate( 'd M Y, h:i A', strtotime( $p['created_at'] ) ) ); ?> • <span class="uppercase font-semibold text-zinc-600 dark:text-zinc-300"><?php echo esc_html( $p['payout_method'] ); ?></span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <div class="font-mono font-extrabold text-zinc-900 dark:text-zinc-50 text-sm">
                                    <?php echo esc_html( $curr_sym . number_format( (float) $p['amount'], 2 ) ); ?>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border <?php echo esc_attr( $status_badge ); ?> mt-1">
                                    <?php echo esc_html( ucfirst( $p['status'] ) ); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="p-6 text-center text-zinc-400 text-xs">
                            No withdrawal transactions requested yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- ================================================================= -->
<!-- MOBILE BOTTOM-SHEET & DESKTOP SLIDING WITHDRAWAL PAYOUT DRAWER   -->
<!-- ================================================================= -->
<div id="cora-affiliate-payout-drawer" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
    <!-- Backdrop Overlay -->
    <div onclick="coraCloseAffiliatePayoutDrawer()" class="absolute inset-0 bg-zinc-950/45 backdrop-blur-xs cursor-pointer"></div>

    <!-- Drawer Panel: Bottom-Sheet on Mobile (<640px), Right-Sliding on Desktop (>=640px) -->
    <div class="cora-payout-drawer-panel absolute sm:right-0 bottom-0 sm:top-0 w-full sm:max-w-md max-h-[85vh] sm:max-h-full bg-white dark:bg-zinc-900 border-t sm:border-t-0 sm:border-l border-zinc-200 dark:border-zinc-800 rounded-t-3xl sm:rounded-none shadow-2xl p-5 sm:p-6 flex flex-col justify-between transform translate-y-full sm:translate-y-0 sm:translate-x-full transition-transform duration-300 ease-out pointer-events-auto overflow-y-auto">
        
        <div>
            <!-- Mobile Drag Handle -->
            <div class="w-10 h-1 rounded-full bg-zinc-300 dark:bg-zinc-700 mx-auto mb-3 sm:hidden"></div>

            <!-- Drawer Header -->
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Withdraw Affiliate Earnings</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Available Balance: <strong class="text-zinc-900 dark:text-zinc-100 font-mono"><?php echo esc_html( $curr_sym . number_format( $avail_bal, 2 ) ); ?></strong></p>
                </div>
                <button type="button" onclick="coraCloseAffiliatePayoutDrawer()" class="p-1.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg cursor-pointer">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Drawer Body / Form -->
            <form id="cora-payout-form" onsubmit="coraSubmitPayoutRequest(event)" class="space-y-4 pt-4">
                
                <div class="space-y-1.5">
                    <label for="cora-payout-amount" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Withdrawal Amount (<?php echo esc_html( $curr_sym ); ?>)</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3 text-zinc-400 font-mono text-xs"><?php echo esc_html( $curr_sym ); ?></span>
                        <input id="cora-payout-amount" type="number" step="0.01" min="<?php echo esc_attr( $min_payout ); ?>" max="<?php echo esc_attr( $avail_bal ); ?>" value="<?php echo esc_attr( min( $avail_bal, 10000 ) ); ?>" class="w-full pl-8 pr-3 py-2 text-xs font-mono font-bold bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    </div>
                    <p class="text-[10px] text-zinc-400">Minimum withdrawal: <?php echo esc_html( $curr_sym . number_format( $min_payout, 0 ) ); ?></p>
                </div>

                <!-- Method Segmented Pills -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Disbursement Method</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer">
                            <input type="radio" name="payout_method" value="upi" checked onchange="coraTogglePayoutFields('upi')" class="accent-zinc-900 dark:accent-zinc-100">
                            <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100">UPI Instant</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer">
                            <input type="radio" name="payout_method" value="bank" onchange="coraTogglePayoutFields('bank')" class="accent-zinc-900 dark:accent-zinc-100">
                            <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100">Bank Wire</span>
                        </label>
                    </div>
                </div>

                <!-- UPI Fields -->
                <div id="cora-payout-field-upi" class="space-y-1.5">
                    <label for="cora-upi-id" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">UPI ID / VPA</label>
                    <input id="cora-upi-id" type="text" placeholder="username@okaxis or mobile@upi" class="w-full px-3 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                </div>

                <!-- Bank Fields -->
                <div id="cora-payout-field-bank" class="hidden space-y-2.5">
                    <div class="space-y-1">
                        <label for="cora-bank-beneficiary" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Beneficiary Name</label>
                        <input id="cora-bank-beneficiary" type="text" placeholder="Account Holder Name" class="w-full px-3 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label for="cora-bank-account" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Account Number</label>
                        <input id="cora-bank-account" type="password" placeholder="Account Number" class="w-full px-3 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label for="cora-bank-ifsc" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">IFSC Code</label>
                        <input id="cora-bank-ifsc" type="text" placeholder="e.g. HDFC0001234" class="w-full px-3 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 uppercase focus:outline-none">
                    </div>
                </div>

            </form>
        </div>

        <!-- Drawer Footer -->
        <div class="pt-4 mt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end gap-2">
            <button type="button" onclick="coraCloseAffiliatePayoutDrawer()" class="px-4 py-2 text-xs font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 cursor-pointer">Cancel</button>
            <button type="button" onclick="document.getElementById('cora-payout-form').requestSubmit()" class="px-4 py-2 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 rounded-xl shadow-2xs transition-all cursor-pointer">Confirm Withdrawal</button>
        </div>

    </div>
</div>

<!-- ================================================================= -->
<!-- QR CODE PREVIEW MODAL                                             -->
<!-- ================================================================= -->
<div id="cora-affiliate-qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div onclick="coraCloseQRCodeModal()" class="absolute inset-0 bg-zinc-950/45 backdrop-blur-xs cursor-pointer"></div>
    <div class="relative w-full max-w-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-5 sm:p-6 shadow-2xl z-10 text-center space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-left">
                <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm1 1h2v2H5V5zm9-3h8v8h-8V2zm2 2v4h4V4h-4zm1 1h2v2h-2V5zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm1 1h2v2H5v-2zm9-1h2v2h-2v-2zm4 0h2v2h-2v-2zm-4 4h2v2h-2v-2zm2-2h2v2h-2v-2zm2 2h2v2h-2v-2zm-6 2h2v2h-2v-2zm6 0h2v2h-2v-2zm-2-6h2v2h-2v-2z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Partner QR Code</h3>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Scan to claim 10% off + 100 free AI Runes</p>
                </div>
            </div>
            <button type="button" onclick="coraCloseQRCodeModal()" class="p-1.5 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 rounded-lg cursor-pointer">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="bg-zinc-50 dark:bg-zinc-950 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 flex flex-col items-center justify-center space-y-3">
            <div id="cora-qr-canvas-holder" class="p-3 bg-white rounded-xl shadow-3xs border border-zinc-200/60 inline-block">
                <img id="cora-qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode( $ref_url ); ?>&color=18-18-1b&bgcolor=ffffff" alt="Partner QR Code" width="170" height="170" class="rounded-md block mx-auto">
            </div>
            <div class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 break-all px-2 max-w-xs" id="cora-qr-modal-url-text">
                <?php echo esc_html( $ref_url ); ?>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-1">
            <button type="button" id="cora-qr-download-btn" onclick="coraDownloadQRCode()" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-zinc-800 dark:text-zinc-200 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 rounded-xl transition-all cursor-pointer">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Download QR</span>
            </button>
            <button type="button" id="cora-qr-copy-btn" onclick="coraCopyReferralLink(this)" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 rounded-xl shadow-2xs transition-all cursor-pointer">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                <span>Copy Link</span>
            </button>
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- JAVASCRIPT CONTROLLERS                                            -->
<!-- ================================================================= -->
<script>
/* --- HERO CONTROLLERS --- */
function coraScrollToEnroll() {
    var card = document.getElementById('cora-enroll-activation-card');
    var slugInput = document.getElementById('cora-enroll-slug');
    if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(function() {
            if (slugInput) slugInput.focus();
        }, 300);
    }
}

function coraToggleHowItWorksModal(show) {
    var modal = document.getElementById('cora-how-it-works-modal');
    if (!modal) return;
    if (show) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

/* --- 3-STEP SCREENER CONTROLLER --- */
function coraScreenerGoToStep(stepNum) {
    if (stepNum === 3) {
        var slugInput = document.getElementById('cora-enroll-slug');
        var slug = slugInput ? slugInput.value.trim() : '';
        if (slug.length < 3) {
            if (window.coraShowToast) window.coraShowToast('Please enter a custom referral handle with at least 3 characters.', 'error');
            return;
        }
    }

    [1, 2, 3].forEach(function(s) {
        var view = document.getElementById('cora-screener-step-' + s);
        var tab = document.getElementById('cora-step-tab-' + s);
        if (view) {
            if (s === stepNum) {
                view.classList.remove('hidden');
            } else {
                view.classList.add('hidden');
            }
        }
        if (tab) {
            if (s === stepNum) {
                tab.className = 'flex items-center gap-2 p-2 rounded-xl bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 transition-all';
            } else if (s < stepNum) {
                tab.className = 'flex items-center gap-2 p-2 rounded-xl bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 transition-all';
            } else {
                tab.className = 'flex items-center gap-2 p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-all';
            }
        }
    });

    var root = document.getElementById('cora-affiliate-screener-view');
    if (root) root.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function coraScreenerSetBillingMode(mode) {
    window.coraAffiliateState.screenerBillingMode = mode;
    var btnAnnual = document.getElementById('cora-screener-mode-annual');
    var btnMonthly = document.getElementById('cora-screener-mode-monthly');

    if (mode === 'annual') {
        if (btnAnnual) btnAnnual.className = 'px-3 py-1.5 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 shadow-2xs cursor-pointer transition-all';
        if (btnMonthly) btnMonthly.className = 'px-3 py-1.5 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all';
    } else {
        if (btnAnnual) btnAnnual.className = 'px-3 py-1.5 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all';
        if (btnMonthly) btnMonthly.className = 'px-3 py-1.5 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 shadow-2xs cursor-pointer transition-all';
    }
    coraRecalculateScreenerSimulator();
}

function coraRecalculateScreenerSimulator() {
    var range = document.getElementById('cora-screener-sim-range');
    var tier = document.getElementById('cora-screener-sim-tier');
    var badge = document.getElementById('cora-screener-sim-clients-badge');
    var monthlyCash = document.getElementById('cora-screener-sim-monthly-cash');
    var yearlyCash = document.getElementById('cora-screener-sim-yearly-cash');
    var bonusCash = document.getElementById('cora-screener-sim-bonus-cash');
    var runesVal = document.getElementById('cora-screener-sim-runes-val');
    var payoutLabel = document.getElementById('cora-screener-sim-payout-label');

    if (!range || !tier) return;
    var count = parseInt(range.value, 10);
    var selectedOpt = tier.options[tier.selectedIndex];
    var curr = selectedOpt ? (selectedOpt.getAttribute('data-curr') || '₹') : '₹';
    var isAnnual = window.coraAffiliateState.screenerBillingMode === 'annual';

    var monthlyPrice = parseFloat(selectedOpt.getAttribute('data-monthly-price') || (tier.value / 12));
    var annualPrice = parseFloat(selectedOpt.getAttribute('data-annual-price') || tier.value);

    // Milestone bonus computation
    var milestoneBonus = 0;
    if (count >= 100) {
        milestoneBonus = curr === '₹' ? 10000 : 100;
    } else if (count >= 50) {
        milestoneBonus = curr === '₹' ? 5000 : 50;
    } else if (count >= 10) {
        milestoneBonus = curr === '₹' ? 1000 : 10;
    }

    var baseCommission = 0;
    var totalPayout = 0;
    var runes = count * 100;

    if (isAnnual) {
        baseCommission = Math.round(count * annualPrice * 0.30);
        totalPayout = baseCommission + milestoneBonus;
        if (payoutLabel) payoutLabel.innerText = 'Est. Annual Payout (30%)';
        if (monthlyCash) monthlyCash.innerText = curr + totalPayout.toLocaleString(curr === '₹' ? 'en-IN' : 'en-US');
        if (yearlyCash) {
            yearlyCash.innerText = curr + Math.round(totalPayout / 12).toLocaleString(curr === '₹' ? 'en-IN' : 'en-US') + '/mo equiv';
        }
    } else {
        var monthlyComm = Math.round(count * monthlyPrice * 0.20);
        baseCommission = monthlyComm * 12;
        totalPayout = baseCommission + milestoneBonus;
        if (payoutLabel) payoutLabel.innerText = 'Est. Monthly Payout (20%)';
        if (monthlyCash) monthlyCash.innerText = curr + monthlyComm.toLocaleString(curr === '₹' ? 'en-IN' : 'en-US') + '/mo';
        if (yearlyCash) {
            yearlyCash.innerText = curr + totalPayout.toLocaleString(curr === '₹' ? 'en-IN' : 'en-US') + '/yr annualized';
        }
    }

    if (bonusCash) bonusCash.innerText = milestoneBonus > 0 ? ('+' + curr + milestoneBonus.toLocaleString(curr === '₹' ? 'en-IN' : 'en-US')) : (curr + '0');
    if (runesVal) runesVal.innerText = '+' + runes.toLocaleString();
    if (badge) badge.innerText = count + (count === 1 ? ' Studio' : ' Studios');
}

function coraSetBillingMode(mode) {
    window.coraAffiliateState.billingMode = mode;
    var btnAnnual = document.getElementById('cora-mode-annual');
    var btnMonthly = document.getElementById('cora-mode-monthly');

    if (mode === 'annual') {
        if (btnAnnual) btnAnnual.className = 'px-3 py-1.5 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 shadow-2xs cursor-pointer transition-all';
        if (btnMonthly) btnMonthly.className = 'px-3 py-1.5 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all';
    } else {
        if (btnAnnual) btnAnnual.className = 'px-3 py-1.5 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all';
        if (btnMonthly) btnMonthly.className = 'px-3 py-1.5 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-zinc-100 shadow-2xs cursor-pointer transition-all';
    }
    coraRecalculateSimulator();
}

function coraRecalculateSimulator() {
    var range = document.getElementById('cora-sim-range');
    var tier = document.getElementById('cora-sim-tier');
    var badge = document.getElementById('cora-sim-clients-badge');
    var monthlyCash = document.getElementById('cora-sim-monthly-cash');
    var yearlyCash = document.getElementById('cora-sim-yearly-cash');
    var bonusCash = document.getElementById('cora-sim-bonus-cash');
    var runesVal = document.getElementById('cora-sim-runes-val');
    var payoutLabel = document.getElementById('cora-sim-payout-label');

    if (!range || !tier) return;
    var count = parseInt(range.value, 10);
    var selectedOpt = tier.options[tier.selectedIndex];
    var curr = selectedOpt ? (selectedOpt.getAttribute('data-curr') || '₹') : '₹';
    var isAnnual = window.coraAffiliateState.billingMode === 'annual';

    var monthlyPrice = parseFloat(selectedOpt.getAttribute('data-monthly-price') || (tier.value / 12));
    var annualPrice = parseFloat(selectedOpt.getAttribute('data-annual-price') || tier.value);

    // Milestone bonus computation
    var milestoneBonus = 0;
    if (count >= 100) {
        milestoneBonus = curr === '₹' ? 10000 : 100;
    } else if (count >= 50) {
        milestoneBonus = curr === '₹' ? 5000 : 50;
    } else if (count >= 10) {
        milestoneBonus = curr === '₹' ? 1000 : 10;
    }

    var baseCommission = 0;
    var totalPayout = 0;
    var runes = count * 100;

    if (isAnnual) {
        baseCommission = Math.round(count * annualPrice * 0.30);
        totalPayout = baseCommission + milestoneBonus;
        if (payoutLabel) payoutLabel.innerText = 'Est. Annual Payout (30%)';
        if (monthlyCash) monthlyCash.innerText = curr + totalPayout.toLocaleString(curr === '₹' ? 'en-IN' : 'en-US');
        if (yearlyCash) {
            yearlyCash.innerText = curr + Math.round(totalPayout / 12).toLocaleString(curr === '₹' ? 'en-IN' : 'en-US') + '/mo equiv';
        }
    } else {
        var monthlyComm = Math.round(count * monthlyPrice * 0.20);
        baseCommission = monthlyComm * 12;
        totalPayout = baseCommission + milestoneBonus;
        if (payoutLabel) payoutLabel.innerText = 'Est. Monthly Payout (20%)';
        if (monthlyCash) monthlyCash.innerText = curr + monthlyComm.toLocaleString(curr === '₹' ? 'en-IN' : 'en-US') + '/mo';
        if (yearlyCash) {
            yearlyCash.innerText = curr + totalPayout.toLocaleString(curr === '₹' ? 'en-IN' : 'en-US') + '/yr annualized';
        }
    }

    if (bonusCash) bonusCash.innerText = milestoneBonus > 0 ? ('+' + curr + milestoneBonus.toLocaleString(curr === '₹' ? 'en-IN' : 'en-US')) : (curr + '0');
    if (runesVal) runesVal.innerText = '+' + runes.toLocaleString();
    if (badge) badge.innerText = count + (count === 1 ? ' Studio' : ' Studios');
}

function coraSubmitAffiliateEnrollment() {
    var agree = document.getElementById('cora-enroll-agree');
    if (!agree || !agree.checked) {
        if (window.coraShowToast) window.coraShowToast('Please accept the Partner Agreement terms to continue.', 'error');
        return;
    }

    var submitBtn = document.getElementById('cora-enroll-submit-btn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span>Activating Partner Portal...</span>';
    }

    var slug = document.getElementById('cora-enroll-slug') ? document.getElementById('cora-enroll-slug').value.trim() : '';
    var upi = document.getElementById('cora-enroll-upi') ? document.getElementById('cora-enroll-upi').value.trim() : '';
    var bank = document.getElementById('cora-enroll-bank') ? document.getElementById('cora-enroll-bank').value.trim() : '';

    var formData = new FormData();
    formData.append('action', 'cora_affiliate_enroll');
    formData.append('custom_slug', slug);
    formData.append('upi_id', upi);
    formData.append('account_number', bank);

    if (window.coraREData && window.coraREData.ajaxNonce) {
        formData.append('security', window.coraREData.ajaxNonce);
    }

    fetch(window.coraREData ? window.coraREData.ajaxUrl : '/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(function(r){ return r.json(); })
    .then(function(res){
        if (res.success) {
            window.coraAffiliateState.isEnrolled = true;
            if (res.data && res.data.referral_url) {
                window.coraAffiliateState.referralUrl = res.data.referral_url;
                var refInput = document.getElementById('cora-ref-link-input');
                if (refInput) refInput.value = res.data.referral_url;
                var dispSlug = document.getElementById('cora-display-slug');
                if (dispSlug) dispSlug.innerText = res.data.ref_code || slug;
            }

            if (window.coraShowToast) {
                window.coraShowToast(res.data.message || 'Welcome to Cora Partner Network! Your dashboard is now active.', 'success');
            }

            var screenerView = document.getElementById('cora-affiliate-screener-view');
            var dashboardView = document.getElementById('cora-affiliate-dashboard-view');
            if (screenerView && dashboardView) {
                screenerView.classList.add('hidden');
                dashboardView.classList.remove('hidden');
                dashboardView.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Activate Partner Link</span>';
            }
            if (window.coraShowToast) {
                window.coraShowToast(res.data.message || 'Error completing enrollment', 'error');
            }
        }
    })
    .catch(function(){
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>Activate Partner Link</span>';
        }
        if (window.coraShowToast) window.coraShowToast('Network error while completing partner enrollment', 'error');
    });
}

function coraCopyReferralLink(btnEl) {
    var url = window.coraAffiliateState ? window.coraAffiliateState.referralUrl : '';
    if (!url) {
        var input = document.getElementById('cora-ref-link-input');
        if (input) url = input.value;
    }

    if (!url) return;

    function showFeedback() {
        var btns = [
            document.getElementById('cora-copy-link-btn'),
            document.getElementById('cora-header-copy-link-btn'),
            document.getElementById('cora-qr-copy-btn'),
            btnEl
        ];
        btns.forEach(function(b) {
            if (b) {
                var orig = b.innerHTML;
                b.innerHTML = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Copied!</span>';
                setTimeout(function(){ b.innerHTML = orig; }, 2000);
            }
        });
        if (window.coraShowToast) {
            window.coraShowToast('Referral link copied to clipboard!', 'success');
        }
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(showFeedback).catch(function(){
            fallbackCopy(url);
        });
    } else {
        fallbackCopy(url);
    }

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            showFeedback();
        } catch(e) {
            if (window.coraShowToast) window.coraShowToast('Please copy link manually', 'info');
        }
        document.body.removeChild(ta);
    }
}

function coraToggleSlugEditor(forceState) {
    var editor = document.getElementById('cora-slug-editor');
    if (!editor) return;
    var isHidden = editor.classList.contains('hidden');
    var shouldOpen = (typeof forceState === 'boolean') ? forceState : isHidden;

    if (shouldOpen) {
        editor.classList.remove('hidden');
        var input = document.getElementById('cora-custom-slug-input');
        if (input) {
            input.focus();
            input.select();
        }
    } else {
        editor.classList.add('hidden');
    }
}

function coraOnSlugInput(val) {
    var clean = val.toLowerCase().replace(/[^a-z0-9_-]/g, '').substring(0, 32);
    var countEl = document.getElementById('cora-slug-char-count');
    var previewEl = document.getElementById('cora-display-slug-url');
    if (countEl) countEl.innerText = clean.length + '/32';
    if (previewEl) {
        var base = (window.coraAffiliateState && window.coraAffiliateState.referralUrl) ? window.coraAffiliateState.referralUrl.split('?ref=')[0] : (window.location.origin + '/');
        previewEl.innerText = base + '?ref=' + clean;
    }
}

function coraSaveCustomSlug() {
    var input = document.getElementById('cora-custom-slug-input');
    if (!input) return;
    var slug = input.value.trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');

    if (slug.length < 3) {
        if (window.coraShowToast) window.coraShowToast('Handle must contain at least 3 characters.', 'error');
        return;
    }

    var saveBtn = document.getElementById('cora-save-slug-btn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerText = 'Saving...';
    }

    var formData = new FormData();
    formData.append('action', 'cora_affiliate_update_slug');
    formData.append('custom_slug', slug);
    if (window.coraREData && window.coraREData.ajaxNonce) {
        formData.append('security', window.coraREData.ajaxNonce);
    }

    fetch(window.coraREData ? window.coraREData.ajaxUrl : '/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(function(r){ return r.json(); })
    .then(function(res){
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Handle';
        }
        if (res.success) {
            window.coraAffiliateState.customSlug = res.data.custom_slug || slug;
            window.coraAffiliateState.referralUrl = res.data.referral_url;

            var dispSlug = document.getElementById('cora-display-slug');
            if (dispSlug) dispSlug.innerText = res.data.custom_slug || slug;

            var standings = document.querySelectorAll('.cora-standing-slug');
            standings.forEach(function(s){ s.innerText = res.data.custom_slug || slug; });

            var refInput = document.getElementById('cora-ref-link-input');
            if (refInput) refInput.value = res.data.referral_url;

            var previewEl = document.getElementById('cora-display-slug-url');
            if (previewEl) previewEl.innerText = res.data.referral_url;

            var qrImg = document.getElementById('cora-qr-img');
            if (qrImg) qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(res.data.referral_url) + '&color=18-18-1b&bgcolor=ffffff';

            var qrUrl = document.getElementById('cora-qr-modal-url-text');
            if (qrUrl) qrUrl.innerText = res.data.referral_url;

            coraToggleSlugEditor(false);

            if (window.coraShowToast) window.coraShowToast('Affiliate handle updated successfully!', 'success');
        } else {
            if (window.coraShowToast) window.coraShowToast(res.data.message || 'Error updating handle', 'error');
        }
    })
    .catch(function(){
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Handle';
        }
        if (window.coraShowToast) window.coraShowToast('Network error while updating handle', 'error');
    });
}

function coraDownloadQRCode() {
    var qrImg = document.getElementById('cora-qr-img');
    if (!qrImg || !qrImg.src) return;

    fetch(qrImg.src)
    .then(function(res){ return res.blob(); })
    .then(function(blob){
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'cora-partner-qr-' + (window.coraAffiliateState.customSlug || 'partner') + '.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        if (window.coraShowToast) window.coraShowToast('QR code downloaded!', 'success');
    })
    .catch(function(){
        window.open(qrImg.src, '_blank');
    });
}

function coraShareWhatsApp() {
    var url = window.coraAffiliateState.referralUrl;
    var text = encodeURIComponent("Hey! I'm using Cora for our studio and client workflows. Check it out here: " + url);
    window.open('https://api.whatsapp.com/send?text=' + text, '_blank');
}

function coraShareLinkedIn() {
    var url = encodeURIComponent(window.coraAffiliateState.referralUrl);
    window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + url, '_blank');
}

function coraShareTwitter() {
    var url = encodeURIComponent(window.coraAffiliateState.referralUrl);
    var text = encodeURIComponent("Streamline your studio & agency workflows with Cora Workspace: " + window.coraAffiliateState.referralUrl);
    window.open('https://twitter.com/intent/tweet?text=' + text, '_blank');
}

function coraShareEmail() {
    var url = window.coraAffiliateState.referralUrl;
    var subject = encodeURIComponent("Invitation to Cora Studio Workspace");
    var body = encodeURIComponent("Hi there,\n\nI wanted to share Cora Workspace with you. It includes an integrated visual canvas, client lead CRM, and automated invoicing.\n\nSign up through my partner link here:\n" + url + "\n\nBest regards,\n" + (window.coraAffiliateState.customSlug || 'Studio Partner'));
    window.location.href = 'mailto:?subject=' + subject + '&body=' + body;
}

function coraFilterReferralTable(filter) {
    var container = document.getElementById('cora-referral-list-container');
    if (!container) return;
    var rows = container.querySelectorAll('.cora-ref-item');
    var btnAll = document.getElementById('cora-filter-all');
    var btnPaid = document.getElementById('cora-filter-paid');
    var btnFree = document.getElementById('cora-filter-free');

    // Reset styles
    [btnAll, btnPaid, btnFree].forEach(function(btn){
        if (btn) btn.className = 'px-3 py-1 text-xs font-medium rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer';
    });

    if (filter === 'all' && btnAll) {
        btnAll.className = 'px-3 py-1 text-xs font-bold rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs transition-all cursor-pointer';
    } else if (filter === 'paid' && btnPaid) {
        btnPaid.className = 'px-3 py-1 text-xs font-bold rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs transition-all cursor-pointer';
    } else if (filter === 'free' && btnFree) {
        btnFree.className = 'px-3 py-1 text-xs font-bold rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs transition-all cursor-pointer';
    }

    rows.forEach(function(row){
        if (filter === 'all') {
            row.style.display = 'flex';
        } else if (filter === 'paid') {
            row.style.display = row.classList.contains('cora-row-paid') ? 'flex' : 'none';
        } else if (filter === 'free') {
            row.style.display = row.classList.contains('cora-row-free') ? 'flex' : 'none';
        }
    });
}

function coraOpenAffiliatePayoutDrawer() {
    var drawer = document.getElementById('cora-affiliate-payout-drawer');
    if (drawer) {
        drawer.classList.remove('hidden');
        drawer.style.display = 'block';
        setTimeout(function() {
            drawer.classList.remove('opacity-0');
            drawer.classList.add('opacity-100');
            var panel = drawer.querySelector('.cora-payout-drawer-panel');
            if (panel) {
                panel.classList.remove('translate-y-full', 'sm:translate-x-full');
                panel.classList.add('translate-y-0', 'sm:translate-x-0');
            }
        }, 10);
    }
}

function coraCloseAffiliatePayoutDrawer() {
    var drawer = document.getElementById('cora-affiliate-payout-drawer');
    if (drawer) {
        var panel = drawer.querySelector('.cora-payout-drawer-panel');
        if (panel) {
            panel.classList.add('translate-y-full', 'sm:translate-x-full');
            panel.classList.remove('translate-y-0', 'sm:translate-x-0');
        }
        drawer.classList.remove('opacity-100');
        drawer.classList.add('opacity-0');
        setTimeout(function(){
            drawer.classList.add('hidden');
            drawer.style.display = 'none';
        }, 280);
    }
}

function coraTogglePayoutFields(method) {
    var upiBox = document.getElementById('cora-payout-field-upi');
    var bankBox = document.getElementById('cora-payout-field-bank');
    if (method === 'upi') {
        if (upiBox) upiBox.classList.remove('hidden');
        if (bankBox) bankBox.classList.add('hidden');
    } else {
        if (upiBox) upiBox.classList.add('hidden');
        if (bankBox) bankBox.classList.remove('hidden');
    }
}

function coraSubmitPayoutRequest(e) {
    if (e) e.preventDefault();
    var amount = parseFloat(document.getElementById('cora-payout-amount').value || 0);
    var method = document.querySelector('input[name="payout_method"]:checked').value;
    var upiId = document.getElementById('cora-upi-id') ? document.getElementById('cora-upi-id').value : '';
    var bankBeneficiary = document.getElementById('cora-bank-beneficiary') ? document.getElementById('cora-bank-beneficiary').value : '';
    var bankAccount = document.getElementById('cora-bank-account') ? document.getElementById('cora-bank-account').value : '';
    var bankIfsc = document.getElementById('cora-bank-ifsc') ? document.getElementById('cora-bank-ifsc').value : '';

    var formData = new FormData();
    formData.append('action', 'cora_affiliate_request_payout');
    formData.append('amount', amount);
    formData.append('payout_method', method);
    formData.append('upi_id', upiId);
    formData.append('beneficiary_name', bankBeneficiary);
    formData.append('account_number', bankAccount);
    formData.append('ifsc_code', bankIfsc);
    if (window.coraREData && window.coraREData.ajaxNonce) {
        formData.append('security', window.coraREData.ajaxNonce);
    }

    fetch(window.coraREData ? window.coraREData.ajaxUrl : '/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(function(r){ return r.json(); })
    .then(function(res){
        if (res.success) {
            coraCloseAffiliatePayoutDrawer();
            if (window.coraShowToast) {
                window.coraShowToast(res.data.message || 'Payout request submitted successfully!', 'success');
            }
        } else {
            if (window.coraShowToast) {
                window.coraShowToast(res.data.message || 'Error processing payout request', 'error');
            }
        }
    })
    .catch(function(){
        if (window.coraShowToast) window.coraShowToast('Network error while requesting payout', 'error');
    });
}

function coraOpenQRCodeModal() {
    var modal = document.getElementById('cora-affiliate-qr-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function coraCloseQRCodeModal() {
    var modal = document.getElementById('cora-affiliate-qr-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Auto-initialize screener simulator values on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof coraRecalculateScreenerSimulator === 'function') coraRecalculateScreenerSimulator();
    });
} else {
    if (typeof coraRecalculateScreenerSimulator === 'function') coraRecalculateScreenerSimulator();
}
</script>
