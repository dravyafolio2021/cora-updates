<?php
/**
 * Cora Affiliate & Referral Workspace View - Gamified Dashboard, Screener & Leaderboard
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

// --- GAMIFIED TIER & PROGRESS CALCULATION (Monochromatic & Clean) ---
$current_tier_name = 'Bronze Partner';
$current_tier_icon = '🥉';
$next_tier_name = 'Silver Creator (₹25,000)';
$next_tier_threshold = 25000.0;
$prev_tier_threshold = 0.0;
$tier_badge_class = 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border-zinc-200 dark:border-zinc-700';

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
    $tier_badge_class = 'bg-zinc-150 dark:bg-zinc-750 text-zinc-850 dark:text-zinc-150 border-zinc-200 dark:border-zinc-700';
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

<div id="cora-affiliate-root" class="space-y-4 max-w-full">

    <!-- ================================================================= -->
    <!-- VIEW A: 3-STEP PARTNER ENROLLMENT SCREENER (FOR UNENROLLED USERS) -->
    <!-- ================================================================= -->
    <div id="cora-affiliate-screener-view" class="<?php echo $is_enrolled ? 'hidden' : ''; ?> max-w-4xl mx-auto space-y-4">
        
        <!-- Screener Header -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-6 shadow-3xs relative overflow-hidden">
            <div class="relative z-10 space-y-2">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Official Partner & Affiliate Program</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-zinc-900 dark:text-zinc-50 tracking-tight">
                    Partner with Cora & Monetize Your Client Network
                </h1>
                <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 max-w-2xl leading-relaxed">
                    Enroll your studio or agency. Earn <strong class="text-zinc-800 dark:text-zinc-200">30% recurring commission on annual plans</strong>, <strong class="text-zinc-800 dark:text-zinc-200">20% on monthly plans</strong>, <strong class="text-zinc-800 dark:text-zinc-200">100 Free AI Runes</strong> per verified signup, plus up to <strong class="text-zinc-800 dark:text-zinc-200"><?php echo $is_india_geo ? '₹10,000' : '$100'; ?> in milestone cash bonuses</strong>.
                </p>
            </div>

            <!-- 3-Step Stepper Header -->
            <div class="pt-4 mt-4 border-t border-zinc-100 dark:border-zinc-800/80 grid grid-cols-3 gap-2 text-xs font-semibold">
                <div id="cora-step-tab-1" class="flex items-center gap-2 p-2 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 transition-all">
                    <span class="w-4.5 h-4.5 rounded-full bg-white/20 dark:bg-zinc-900/20 flex items-center justify-center text-[10px] font-bold">1</span>
                    <span class="truncate text-[11px]">1. Benefits</span>
                </div>
                <div id="cora-step-tab-2" class="flex items-center gap-2 p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-all">
                    <span class="w-4.5 h-4.5 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold">2</span>
                    <span class="truncate text-[11px]">2. Profile</span>
                </div>
                <div id="cora-step-tab-3" class="flex items-center gap-2 p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-all">
                    <span class="w-4.5 h-4.5 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold">3</span>
                    <span class="truncate text-[11px]">3. Terms</span>
                </div>
            </div>
        </div>

        <!-- STEP 1: BENEFITS, REWARDS & EARNINGS MODEL -->
        <div id="cora-screener-step-1" class="space-y-4">
            
            <!-- 4 Pillar Core Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl shadow-3xs space-y-1.5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md">30%</span>
                        <span class="text-[10px] text-zinc-400 font-medium">Annual</span>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Annual Commission</h3>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug mt-0.5">Earn 30% recurring payout on all yearly subscriptions.</p>
                    </div>
                </div>

                <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl shadow-3xs space-y-1.5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md">20%</span>
                        <span class="text-[10px] text-zinc-400 font-medium">Monthly</span>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Monthly Commission</h3>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug mt-0.5">Continuous 20% cash flow on monthly active clients.</p>
                    </div>
                </div>

                <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl shadow-3xs space-y-1.5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md">⚡ 100</span>
                        <span class="text-[10px] text-zinc-400 font-medium">Per Free User</span>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">100 Free AI Runes</h3>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug mt-0.5">Credited automatically on every verified free signup.</p>
                    </div>
                </div>

                <div class="p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl shadow-3xs space-y-1.5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md">30 Days</span>
                        <span class="text-[10px] text-zinc-400 font-medium">Cookie</span>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Instant UPI Payouts</h3>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-snug mt-0.5">Fast direct transfers to Bank or UPI from <?php echo $is_india_geo ? '₹1,000' : '$10'; ?>.</p>
                    </div>
                </div>
            </div>

            <!-- MILESTONE CASH BONUSES CARD -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-5 shadow-3xs space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100">Milestone Partner Cash Bonuses 🏆</h3>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Guaranteed cash rewards unlocked on top of recurring commissions.</p>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">Extra Rewards</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">🎯 10 Paid Referrals</div>
                            <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-0.5">Starter partner unlock</div>
                        </div>
                        <span class="text-sm font-extrabold font-mono text-zinc-900 dark:text-zinc-100"><?php echo $is_india_geo ? '+₹1,000' : '+$10'; ?></span>
                    </div>

                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">🚀 50 Paid Referrals</div>
                            <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-0.5">Growth milestone</div>
                        </div>
                        <span class="text-sm font-extrabold font-mono text-zinc-900 dark:text-zinc-100"><?php echo $is_india_geo ? '+₹5,000' : '+$50'; ?></span>
                    </div>

                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">👑 100 Paid Referrals</div>
                            <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-0.5">Titan ambassador unlock</div>
                        </div>
                        <span class="text-sm font-extrabold font-mono text-zinc-900 dark:text-zinc-100"><?php echo $is_india_geo ? '+₹10,000' : '+$100'; ?></span>
                    </div>
                </div>
            </div>

            <!-- EFFORTLESS GAMIFIED CALCULATOR (SCREENER) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-5 shadow-3xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100">Earnings Potential Calculator</h3>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Simulate recurring revenue + milestone bonuses + AI Runes.</p>
                    </div>

                    <!-- Billing Mode Switcher -->
                    <div class="inline-flex p-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-xs self-start sm:self-auto">
                        <button type="button" onclick="coraScreenerSetBillingMode('annual')" id="cora-screener-mode-annual" class="px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-3xs cursor-pointer transition-all">Annual (30%)</button>
                        <button type="button" onclick="coraScreenerSetBillingMode('monthly')" id="cora-screener-mode-monthly" class="px-2.5 py-1 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all">Monthly (20%)</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">Client Plan</label>
                        <select id="cora-screener-sim-tier" onchange="coraRecalculateScreenerSimulator()" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-800 rounded-xl focus:outline-none cursor-pointer font-medium">
                            <?php if ( $is_india_geo ) : ?>
                                <option value="19990" data-monthly-price="1999" data-annual-price="19990" data-curr="₹" selected>Professional (₹1,999/mo • ₹19,990/yr)</option>
                                <option value="9990" data-monthly-price="999" data-annual-price="9990" data-curr="₹">Starter (₹999/mo • ₹9,990/yr)</option>
                                <option value="29990" data-monthly-price="2999" data-annual-price="29990" data-curr="₹">Scale (₹2,999/mo • ₹29,990/yr)</option>
                                <option value="5988" data-monthly-price="499" data-annual-price="5988" data-curr="₹">India Only (₹499/mo • ₹5,988/yr)</option>
                            <?php else : ?>
                                <option value="190" data-monthly-price="19" data-annual-price="190" data-curr="$" selected>Professional ($19/mo • $190/yr)</option>
                                <option value="90" data-monthly-price="9" data-annual-price="90" data-curr="$">Starter ($9/mo • $90/yr)</option>
                                <option value="290" data-monthly-price="29" data-annual-price="290" data-curr="$">Scale ($29/mo • $290/yr)</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">
                            <span>Referred Clients</span>
                            <span id="cora-screener-sim-clients-badge" class="font-mono font-bold text-zinc-900 dark:text-zinc-100">10 Studios</span>
                        </div>
                        <input id="cora-screener-sim-range" type="range" min="1" max="100" value="10" step="1" oninput="coraRecalculateScreenerSimulator()" class="w-full h-2 bg-zinc-200 dark:bg-zinc-800 rounded-lg appearance-none cursor-pointer accent-zinc-900 dark:accent-zinc-100">
                        <div class="flex justify-between text-[9px] text-zinc-400 font-mono">
                            <span>1</span>
                            <span>10 🎯 (+<?php echo $is_india_geo ? '₹1k' : '$10'; ?>)</span>
                            <span>50 🚀 (+<?php echo $is_india_geo ? '₹5k' : '$50'; ?>)</span>
                            <span>100 👑 (+<?php echo $is_india_geo ? '₹10k' : '$100'; ?>)</span>
                        </div>
                    </div>
                </div>

                <!-- 3 Compact Metric Return Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl flex flex-col justify-between">
                        <span id="cora-screener-sim-payout-label" class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Est. Cash Payout</span>
                        <div class="mt-1">
                            <div id="cora-screener-sim-monthly-cash" class="text-xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight"><?php echo $is_india_geo ? '₹60,970' : '$580'; ?></div>
                            <div id="cora-screener-sim-yearly-cash" class="text-[10px] text-zinc-400 font-mono mt-0.5"><?php echo $is_india_geo ? '₹5,081/mo equiv' : '$48/mo equiv'; ?></div>
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl flex flex-col justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Milestone Bonus</span>
                        <div class="mt-1">
                            <div id="cora-screener-sim-bonus-cash" class="text-xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight"><?php echo $is_india_geo ? '+₹1,000' : '+$10'; ?></div>
                            <div class="text-[10px] text-zinc-400 mt-0.5">Instant bonus reward</div>
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl flex flex-col justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Free AI Runes</span>
                        <div class="mt-1">
                            <div id="cora-screener-sim-runes-val" class="text-xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight">+1,000</div>
                            <div class="text-[10px] text-zinc-400 mt-0.5">+100 Runes per signup</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 1 Bottom Bar -->
            <div class="flex items-center justify-end pt-1">
                <button type="button" onclick="coraScreenerGoToStep(2)" class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-xl shadow-3xs transition-all cursor-pointer">
                    <span>Continue to Agency Profile</span>
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>
        </div>

        <!-- STEP 2: AGENCY PROFILE & PAYOUT PREFERENCE -->
        <div id="cora-screener-step-2" class="hidden space-y-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-6 shadow-3xs space-y-4">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-zinc-900 dark:text-zinc-50">Agency Profile & Referral Handle</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Your partner identity is automatically linked to your authenticated workspace.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Partner Name</label>
                        <input type="text" id="cora-enroll-name" value="<?php echo esc_attr( $user_display_name ); ?>" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Workspace / Studio</label>
                        <input type="text" id="cora-enroll-workspace" value="<?php echo esc_attr( $workspace_name ); ?>" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Registered Email</label>
                        <input type="email" id="cora-enroll-email" readonly value="<?php echo esc_attr( $user_email ); ?>" class="w-full px-3 py-2 text-xs bg-zinc-100/70 dark:bg-zinc-950/70 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-500 dark:text-zinc-400 cursor-not-allowed">
                    </div>
                </div>

                <div class="space-y-1 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Custom Referral Handle / Slug</label>
                    <div class="flex items-center">
                        <span class="px-3 py-2 text-xs bg-zinc-100 dark:bg-zinc-800 border border-r-0 border-zinc-200 dark:border-zinc-700 rounded-l-xl text-zinc-500 font-mono"><?php echo esc_html( home_url( '/?ref=' ) ); ?></span>
                        <input type="text" id="cora-enroll-slug" value="<?php echo esc_attr( $default_slug ); ?>" class="w-full max-w-xs px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-r-xl text-zinc-900 dark:text-zinc-100 font-mono font-bold focus:outline-none">
                    </div>
                </div>

                <!-- Payout Details -->
                <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Payout Preferences</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="cora-enroll-upi" class="text-xs font-medium text-zinc-700 dark:text-zinc-300">UPI ID / VPA</label>
                            <input id="cora-enroll-upi" type="text" placeholder="agency@okhdfcbank" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                        </div>
                        <div class="space-y-1">
                            <label for="cora-enroll-bank" class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Bank Account & IFSC</label>
                            <input id="cora-enroll-bank" type="text" placeholder="Account No. / IFSC Code" class="w-full px-3 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2 Bottom Bar -->
            <div class="flex items-center justify-between pt-1">
                <button type="button" onclick="coraScreenerGoToStep(1)" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Back</span>
                </button>
                <button type="button" onclick="coraScreenerGoToStep(3)" class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-xl shadow-3xs transition-all cursor-pointer">
                    <span>Continue to Terms</span>
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>
        </div>

        <!-- STEP 3: TERMS & COMPLIANCE -->
        <div id="cora-screener-step-3" class="hidden space-y-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-6 shadow-3xs space-y-4">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-zinc-900 dark:text-zinc-50">Partner Standards & Eligibility</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Please review the compliance policies before activating your partner portal.</p>
                </div>

                <div class="space-y-2">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center text-[9px] font-bold">1</span>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Commission & AI Runes Distribution</h4>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 pl-6 leading-relaxed">
                            30% on Annual Plans, 20% on Monthly Plans, and 100 Free AI Runes per verified free signup. Milestone bonuses unlock automatically at 10, 50, and 100 paid conversions.
                        </p>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center text-[9px] font-bold">2</span>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Zero Spam & Brand Keywords Policy</h4>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 pl-6 leading-relaxed">
                            Spamming unsolicited messages, advertising on trademarked brand keywords, or generating bot signups is strictly prohibited.
                        </p>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center text-[9px] font-bold">3</span>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Strict No Self-Referral Policy</h4>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 pl-6 leading-relaxed">
                            Referrals are intended exclusively for introducing external client studios and businesses. Self-referrals are automatically disqualified.
                        </p>
                    </div>
                </div>

                <div class="p-3 bg-zinc-50 dark:bg-zinc-950 rounded-xl border border-zinc-200/80 dark:border-zinc-800">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input id="cora-enroll-agree" type="checkbox" class="mt-0.5 accent-zinc-900 dark:accent-zinc-100 rounded w-4 h-4 cursor-pointer">
                        <span class="text-xs font-medium text-zinc-800 dark:text-zinc-200 leading-snug">
                            I verify that I represent an active agency/studio and agree to the <strong>Cora Partner Agreement</strong>, 20%-30% Commission Structure, Milestone Bonus Terms, and Payout Guidelines.
                        </span>
                    </label>
                </div>
            </div>

            <!-- Step 3 Bottom Bar -->
            <div class="flex items-center justify-between pt-1">
                <button type="button" onclick="coraScreenerGoToStep(2)" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Back</span>
                </button>
                <button id="cora-enroll-submit-btn" type="button" onclick="coraSubmitAffiliateEnrollment()" class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-xl shadow-3xs transition-all cursor-pointer">
                    <span>Complete Enrollment & Launch Portal</span>
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </button>
            </div>
        </div>

    </div>


    <!-- ================================================================= -->
    <!-- VIEW B: ACTIVE PARTNER GAMIFIED DASHBOARD & LEADERBOARD (ENROLLED) -->
    <!-- ================================================================= -->
    <div id="cora-affiliate-dashboard-view" class="<?php echo ! $is_enrolled ? 'hidden' : ''; ?> space-y-4 max-w-full">

        <!-- 1. TOP HEADER BAR -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-zinc-900 text-white flex items-center justify-center shrink-0 shadow-3xs dark:bg-zinc-100 dark:text-zinc-900">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-zinc-50 tracking-tight">Affiliates & Partner Network</h1>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold <?php echo esc_attr( $tier_badge_class ); ?> border">
                            <span><?php echo esc_html( $current_tier_icon ); ?></span>
                            <span><?php echo esc_html( $current_tier_name ); ?></span>
                        </span>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">30% Annual • 20% Monthly • 100 Free AI Runes per signup + Milestone Bonuses.</p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto">
                <button type="button" onclick="coraOpenAffiliatePayoutDrawer()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-zinc-800 dark:text-zinc-200 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 rounded-xl transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                    <span>Withdraw (<?php echo esc_html( $curr_sym . number_format( $avail_bal, 0 ) ); ?>)</span>
                </button>

                <button type="button" onclick="coraCopyReferralLink()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-xl shadow-3xs transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <span>Copy Link</span>
                </button>
            </div>
        </div>

        <!-- 2. STICKY SUB-NAVIGATION TAB BAR (Edge-to-Edge on all viewports, Pinned below header) -->
        <div class="sticky top-0 z-30 -mx-3 sm:-mx-4 md:-mx-5 px-3 sm:px-4 md:px-5 -mt-3 sm:-mt-4 md:-mt-5 pt-3 sm:pt-4 md:pt-5 pb-2 bg-[#FBFaf7]/95 dark:bg-[#0c0c0e]/95 backdrop-blur-md border-b border-zinc-200/80 dark:border-zinc-800 transition-all">
            <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-none py-0.5 touch-pan-x select-none" id="cora-affiliate-tabs-container">
                <button type="button" onclick="coraSwitchAffiliateTab('overview')" id="cora-tab-btn-overview" class="cora-aff-tab-btn active inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer text-zinc-950 dark:text-zinc-50 bg-zinc-200/70 dark:bg-zinc-800 whitespace-nowrap">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <span>Overview & Meter</span>
                </button>

                <button type="button" onclick="coraSwitchAffiliateTab('leaderboard')" id="cora-tab-btn-leaderboard" class="cora-aff-tab-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 whitespace-nowrap">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.45 1-1 1H7c-.55 0-1-.45-1-1v-2.34"></path><path d="M18 14.66V17c0 .55-.45 1-1 1h-2c-.55 0-1-.45-1-1v-2.34"></path><path d="M6 9v1a6 6 0 0 0 12 0V9H6z"></path></svg>
                    <span>Leaderboard & Ranks</span>
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-zinc-200/60 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">Podium</span>
                </button>

                <button type="button" onclick="coraSwitchAffiliateTab('calculator')" id="cora-tab-btn-calculator" class="cora-aff-tab-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 whitespace-nowrap">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="16" y1="14" x2="16" y2="18"></line><path d="M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M8 18h.01M12 18h.01"></path></svg>
                    <span>Earnings Simulator</span>
                </button>

                <button type="button" onclick="coraSwitchAffiliateTab('referrals')" id="cora-tab-btn-referrals" class="cora-aff-tab-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 whitespace-nowrap">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span>Referrals & Logs</span>
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold bg-zinc-200/60 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300"><?php echo count( $referrals ); ?></span>
                </button>

                <button type="button" onclick="coraSwitchAffiliateTab('payouts')" id="cora-tab-btn-payouts" class="cora-aff-tab-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 whitespace-nowrap">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                    <span>Payouts & Banking</span>
                </button>
            </div>
        </div>

        <!-- ─────────────────────────────────────────────────────────────────── -->
        <!-- SUB-TAB 1: OVERVIEW & GAMIFIED EARNINGS METER                       -->
        <!-- ─────────────────────────────────────────────────────────────────── -->
        <div id="cora-aff-subtab-overview" class="cora-aff-subtab space-y-3.5">
            
            <!-- GAMIFIED EARNINGS METER & MILESTONE REWARDS (Monochromatic & Clean) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-5 shadow-3xs space-y-3.5">
                <!-- Top Row: Earnings & Current Tier -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-400 font-bold">Partner Tier Progression</span>
                            <span class="px-2 py-0.2 rounded text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">⚡ 3-Streak Active</span>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 font-mono tracking-tight"><?php echo esc_html( $curr_sym . number_format( $total_comm, 2 ) ); ?></span>
                            <span class="text-xs text-zinc-400 font-medium">Total Lifetime Earnings</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl px-3 py-2 shrink-0">
                        <span class="text-xl"><?php echo esc_html( $current_tier_icon ); ?></span>
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $current_tier_name ); ?></div>
                            <div class="text-[10px] text-zinc-400">30% Annual • 20% Monthly Rate</div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-1.5 pt-1">
                    <div class="flex items-center justify-between text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                        <span>Level: <strong class="text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $current_tier_name ); ?></strong></span>
                        <span>Target: <strong class="text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $next_tier_name ); ?></strong></span>
                    </div>

                    <div class="w-full h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-zinc-900 dark:bg-zinc-100 rounded-full transition-all duration-500" style="width: <?php echo esc_attr( $tier_progress_pct ); ?>%"></div>
                    </div>

                    <div class="flex items-center justify-between text-[10px] text-zinc-400 font-mono">
                        <span><?php echo esc_html( $tier_progress_pct ); ?>% Completed</span>
                        <?php if ( $to_next_tier > 0 ) : ?>
                            <span><?php echo esc_html( $curr_sym . number_format( $to_next_tier, 0 ) ); ?> to unlock next upgrade</span>
                        <?php else : ?>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">Apex Tier Achieved 🎉</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 3 Milestone Unlocks Grid (Small, clean cards) -->
                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                    <div class="p-2.5 rounded-xl <?php echo $paid_signups >= 10 ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100' : 'bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400'; ?> border border-zinc-200/60 dark:border-zinc-800 space-y-0.5">
                        <div class="flex items-center justify-between text-[10px] font-bold">
                            <span>🎯 10 Paid</span>
                            <span class="font-mono"><?php echo $is_india_geo ? '+₹1k' : '+$10'; ?></span>
                        </div>
                        <div class="text-[9px] font-mono"><?php echo $paid_signups >= 10 ? '✓ Unlocked' : ((10 - $paid_signups) . ' more needed'); ?></div>
                    </div>

                    <div class="p-2.5 rounded-xl <?php echo $paid_signups >= 50 ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100' : 'bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400'; ?> border border-zinc-200/60 dark:border-zinc-800 space-y-0.5">
                        <div class="flex items-center justify-between text-[10px] font-bold">
                            <span>🚀 50 Paid</span>
                            <span class="font-mono"><?php echo $is_india_geo ? '+₹5k' : '+$50'; ?></span>
                        </div>
                        <div class="text-[9px] font-mono"><?php echo $paid_signups >= 50 ? '✓ Unlocked' : ((50 - $paid_signups) . ' more needed'); ?></div>
                    </div>

                    <div class="p-2.5 rounded-xl <?php echo $paid_signups >= 100 ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100' : 'bg-zinc-50 dark:bg-zinc-950/60 text-zinc-500 dark:text-zinc-400'; ?> border border-zinc-200/60 dark:border-zinc-800 space-y-0.5">
                        <div class="flex items-center justify-between text-[10px] font-bold">
                            <span>👑 100 Paid</span>
                            <span class="font-mono"><?php echo $is_india_geo ? '+₹10k' : '+$100'; ?></span>
                        </div>
                        <div class="text-[9px] font-mono"><?php echo $paid_signups >= 100 ? '✓ Unlocked' : ((100 - $paid_signups) . ' more needed'); ?></div>
                    </div>
                </div>
            </div>

            <!-- 2x2 HIGH-DENSITY KPI GRID (1x4 ON DESKTOP) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
                
                <!-- KPI 1: Available Payout -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3.5 shadow-3xs flex flex-col justify-between">
                    <div class="flex items-center justify-between text-zinc-400 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Available Balance</span>
                        <div class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight"><?php echo esc_html( $curr_sym . number_format( $avail_bal, 2 ) ); ?></div>
                        <div class="flex items-center justify-between text-[10px] text-zinc-400 mt-1">
                            <span>Min: <?php echo esc_html( $curr_sym . number_format( $min_payout, 0 ) ); ?></span>
                            <button type="button" onclick="coraOpenAffiliatePayoutDrawer()" class="font-bold text-zinc-900 dark:text-zinc-100 hover:underline cursor-pointer">Withdraw &rarr;</button>
                        </div>
                    </div>
                </div>

                <!-- KPI 2: AI Runes Granted -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3.5 shadow-3xs flex flex-col justify-between">
                    <div class="flex items-center justify-between text-zinc-400 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider">AI Runes Granted</span>
                        <div class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-xs text-zinc-700 dark:text-zinc-300">
                            ⚡
                        </div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight"><?php echo number_format( $ai_credits ); ?></div>
                        <div class="text-[10px] text-zinc-400 mt-1">
                            <strong class="text-zinc-800 dark:text-zinc-200">+100</strong> per free signup
                        </div>
                    </div>
                </div>

                <!-- KPI 3: Paid Referrals -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3.5 shadow-3xs flex flex-col justify-between">
                    <div class="flex items-center justify-between text-zinc-400 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Paid Conversions</span>
                        <div class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight"><?php echo (int) $paid_signups; ?> <span class="text-xs font-normal text-zinc-400">/ <?php echo (int) $affiliate_data['total_conversions']; ?></span></div>
                        <div class="text-[10px] text-zinc-400 mt-1">
                            <?php echo (int) $free_signups; ?> active free trials
                        </div>
                    </div>
                </div>

                <!-- KPI 4: Conversion Rate -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3.5 shadow-3xs flex flex-col justify-between">
                    <div class="flex items-center justify-between text-zinc-400 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Conversion Rate</span>
                        <div class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight"><?php echo number_format( $conv_rate, 1 ); ?>%</div>
                        <div class="text-[10px] text-zinc-400 mt-1 font-mono">
                            <?php echo (int) $clicks; ?> clicks • <?php echo (int) $unique_visits; ?> visits
                        </div>
                    </div>
                </div>

            </div>

            <!-- DEDICATED REFERRAL LINK & MULTI-CHANNEL SHARE BAR -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs space-y-3">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    <div class="space-y-1 flex-1 max-w-xl">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Your Dedicated Affiliate Link</span>
                            <span class="text-[9px] font-semibold px-2 py-0.2 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">30-Day Cookie Active</span>
                        </div>
                        <div class="relative flex items-center">
                            <input id="cora-ref-link-input" type="text" readonly value="<?php echo esc_attr( $ref_url ); ?>" class="w-full pl-3 pr-20 py-2 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 select-all focus:outline-none">
                            <button type="button" onclick="coraCopyReferralLink()" class="absolute right-1 px-3 py-1 text-xs font-semibold bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 rounded-lg transition-all cursor-pointer">
                                Copy
                            </button>
                        </div>
                    </div>

                    <!-- Share Chips -->
                    <div class="flex flex-wrap items-center gap-1.5 pt-1 lg:pt-0 lg:border-l lg:border-zinc-100 lg:dark:border-zinc-800 lg:pl-4">
                        <button type="button" onclick="coraShareWhatsApp()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm0 18.16c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.32a8.21 8.21 0 0 1-1.25-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.26.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24zm4.52-6.16c-.25-.12-1.47-.72-1.69-.8-.23-.09-.39-.13-.56.12-.16.24-.63.8-.78.96-.14.16-.29.18-.54.06-.25-.12-1.05-.38-2-1.23-.74-.66-1.24-1.47-1.39-1.72-.14-.24-.01-.37.11-.49.11-.11.25-.29.37-.43.13-.14.17-.25.25-.41.08-.16.04-.3-.02-.42s-.51-1.2-.7-1.7c-.19-.48-.39-.42-.54-.43h-.46c-.16 0-.42.06-.65.31-.22.24-.86.84-.86 2.06 0 1.22.89 2.4 1.01 2.56.13.17 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.53.6.19 1.14.16 1.57.1.48-.07 1.48-.61 1.69-1.2.2-.59.2-1.09.14-1.2-.06-.11-.21-.17-.46-.29z"/></svg>
                            <span>WhatsApp</span>
                        </button>

                        <button type="button" onclick="coraShareLinkedIn()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.2V10.9H6.46M7.83 6.25c-.9 0-1.63.73-1.63 1.63s.73 1.63 1.63 1.63a1.63 1.63 0 0 0 1.63-1.63c0-.9-.73-1.63-1.63-1.63z"/></svg>
                            <span>LinkedIn</span>
                        </button>

                        <button type="button" onclick="coraShareTwitter()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            <span>X</span>
                        </button>

                        <button type="button" onclick="coraOpenQRCodeModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm1 1h2v2H5V5zm9-3h8v8h-8V2zm2 2v4h4V4h-4zm1 1h2v2h-2V5zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm1 1h2v2H5v-2zm9-1h2v2h-2v-2zm4 0h2v2h-2v-2zm-4 4h2v2h-2v-2zm2-2h2v2h-2v-2zm2 2h2v2h-2v-2zm-6 2h2v2h-2v-2zm6 0h2v2h-2v-2zm-2-6h2v2h-2v-2z"/></svg>
                            <span>QR Code</span>
                        </button>
                    </div>
                </div>

                <!-- Custom Slug Form -->
                <div class="pt-2.5 border-t border-zinc-100 dark:border-zinc-800/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-zinc-400">Custom Affiliate Handle:</span>
                        <span id="cora-display-slug" class="font-mono font-bold text-zinc-800 dark:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded"><?php echo esc_html( $custom_slug ); ?></span>
                        <button type="button" onclick="coraToggleSlugEditor()" class="text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 underline cursor-pointer">Customize</button>
                    </div>

                    <div id="cora-slug-editor" class="hidden flex items-center gap-2">
                        <input id="cora-custom-slug-input" type="text" placeholder="e.g. zenith-studio" value="<?php echo esc_attr( $custom_slug ); ?>" class="px-2.5 py-1 text-xs bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 font-mono focus:outline-none">
                        <button type="button" onclick="coraSaveCustomSlug()" class="px-3 py-1 text-xs font-semibold bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900 rounded-lg transition-all cursor-pointer">Save</button>
                        <button type="button" onclick="coraToggleSlugEditor()" class="px-2 py-1 text-xs text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 cursor-pointer">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- RECENT CONVERSIONS SNAPSHOT (Zero horizontal scroll) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs space-y-2.5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-50">Recent Referral Activity</h3>
                        <p class="text-[11px] text-zinc-400">Latest clients that signed up via your link.</p>
                    </div>
                    <button type="button" onclick="coraSwitchAffiliateTab('referrals')" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:underline cursor-pointer">View All &rarr;</button>
                </div>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                    <?php if ( ! empty( $referrals ) ) : ?>
                        <?php 
                        $recent_slice = array_slice( $referrals, 0, 3 );
                        foreach ( $recent_slice as $ref ) : 
                            $is_paid = $ref['conversion_type'] === 'paid_conversion';
                            $time_ago = human_time_diff( strtotime( $ref['created_at'] ), current_time( 'timestamp' ) ) . ' ago';
                        ?>
                        <div class="py-2 flex items-center justify-between gap-2.5 text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-[11px] text-zinc-700 dark:text-zinc-300 shrink-0">
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
                                    <div class="text-[9px] text-zinc-400">Commission</div>
                                <?php else : ?>
                                    <div class="font-bold text-zinc-800 dark:text-zinc-200">+100 Runes</div>
                                    <div class="text-[9px] text-zinc-400">Free Trial</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="py-3 text-center text-zinc-400 text-xs">
                            No referrals logged yet. Share your link above to start earning!
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- ─────────────────────────────────────────────────────────────────── -->
        <!-- SUB-TAB 2: PARTNER LEADERBOARD & SPRINT CHALLENGE                   -->
        <!-- ─────────────────────────────────────────────────────────────────── -->
        <div id="cora-aff-subtab-leaderboard" class="cora-aff-subtab hidden space-y-3.5">
            
            <!-- SPRINT CHALLENGE BANNER -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs space-y-2.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.2 rounded text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Active Monthly Sprint</span>
                            <span class="text-xs text-zinc-400 font-mono">Ends in 8 days</span>
                        </div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mt-1">September Partner Agency Sprint 🚀</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Reach 10 paid studio clients to unlock <strong class="text-zinc-900 dark:text-zinc-100"><?php echo $is_india_geo ? '₹1,000' : '$10'; ?> Milestone Bonus</strong> + <strong class="text-zinc-900 dark:text-zinc-100">1,000 Extra AI Runes</strong>.</p>
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl px-3 py-2 text-center sm:text-right shrink-0">
                        <div class="text-[10px] uppercase font-bold text-zinc-400">Sprint Target</div>
                        <div class="text-lg font-extrabold text-zinc-900 dark:text-zinc-50 font-mono"><?php echo (int) $paid_signups; ?> <span class="text-xs text-zinc-400 font-normal">/ 10</span></div>
                    </div>
                </div>

                <div class="w-full h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-zinc-900 dark:bg-zinc-100 rounded-full transition-all duration-500" style="width: <?php echo esc_attr( min( 100, max( 10, round( ( $paid_signups / 10 ) * 100 ) ) ) ); ?>%"></div>
                </div>
            </div>

            <!-- TOP 3 PODIUM (Clean Monochromatic Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
                
                <!-- 2nd Place (Silver) -->
                <div class="order-2 md:order-1 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3.5 shadow-3xs flex flex-col justify-between">
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xl">🥈</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">Rank #2</span>
                        </div>
                        <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100">LensCraft_BLR</div>
                        <div class="text-[10px] text-zinc-400">Bangalore Creative Hub</div>
                    </div>
                    <div class="pt-2.5 mt-2.5 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between font-mono text-xs">
                        <span class="text-zinc-400">18 Referrals</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">₹71,964.00</span>
                    </div>
                </div>

                <!-- 1st Place (Gold) -->
                <div class="order-1 md:order-2 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-xl p-3.5 shadow-3xs flex flex-col justify-between">
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">🥇</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">Rank #1 Champion</span>
                        </div>
                        <div class="font-extrabold text-sm text-zinc-900 dark:text-zinc-50">ApexMedia_Mumbai</div>
                        <div class="text-[10px] text-zinc-400">Enterprise Agency Partner</div>
                    </div>
                    <div class="pt-2.5 mt-2.5 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between font-mono text-xs">
                        <span class="text-zinc-500 font-bold">29 Referrals</span>
                        <span class="font-extrabold text-zinc-900 dark:text-zinc-50 text-sm">₹1,15,942.00</span>
                    </div>
                </div>

                <!-- 3rd Place (Bronze) -->
                <div class="order-3 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3.5 shadow-3xs flex flex-col justify-between">
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xl">🥉</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">Rank #3</span>
                        </div>
                        <div class="font-bold text-xs text-zinc-900 dark:text-zinc-100">StudioPro_DL</div>
                        <div class="text-[10px] text-zinc-400">Delhi Production Suite</div>
                    </div>
                    <div class="pt-2.5 mt-2.5 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between font-mono text-xs">
                        <span class="text-zinc-400">12 Referrals</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100">₹47,976.00</span>
                    </div>
                </div>

            </div>

            <!-- YOUR RANKING STANDING CARD -->
            <div class="bg-zinc-100/80 dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/80 rounded-xl p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center font-bold text-xs">
                        #4
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Your Standing (<?php echo esc_html( $custom_slug ); ?>)</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">Rising +2</span>
                        </div>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400">7 more paid referrals to enter the Top 3 Podium!</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-xs font-mono self-end sm:self-auto">
                    <div>
                        <div class="text-[9px] text-zinc-400 uppercase">Referrals</div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100"><?php echo (int) $paid_signups; ?> Paid</div>
                    </div>
                    <div>
                        <div class="text-[9px] text-zinc-400 uppercase">Earnings</div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $curr_sym . number_format( $total_comm, 2 ) ); ?></div>
                    </div>
                </div>
            </div>

            <!-- FULL LEADERBOARD LIST (Zero horizontal scrollbar) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-3xs overflow-hidden">
                <div class="p-3.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-50">All-Time Partner Network Ranking</h3>
                        <p class="text-[11px] text-zinc-400">Updated hourly based on verified client conversions.</p>
                    </div>
                    <span class="text-[10px] font-mono text-zinc-400">Global Registry</span>
                </div>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-xs">
                    <!-- Rank 1 -->
                    <div class="p-3 flex items-center justify-between gap-2.5 hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="font-mono font-bold text-sm w-7 text-zinc-900 dark:text-zinc-100">🥇 #1</span>
                            <div class="truncate">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate">ApexMedia_Mumbai</div>
                                <div class="text-[10px] text-zinc-400">💎 Diamond Titan</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 font-mono">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100">₹1,15,942.00</div>
                            <div class="text-[10px] text-zinc-400">29 Referrals</div>
                        </div>
                    </div>

                    <!-- Rank 2 -->
                    <div class="p-3 flex items-center justify-between gap-2.5 hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="font-mono font-bold text-sm w-7 text-zinc-700 dark:text-zinc-300">🥈 #2</span>
                            <div class="truncate">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate">LensCraft_BLR</div>
                                <div class="text-[10px] text-zinc-400">🥇 Gold Ambassador</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 font-mono">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100">₹71,964.00</div>
                            <div class="text-[10px] text-zinc-400">18 Referrals</div>
                        </div>
                    </div>

                    <!-- Rank 3 -->
                    <div class="p-3 flex items-center justify-between gap-2.5 hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="font-mono font-bold text-sm w-7 text-zinc-700 dark:text-zinc-300">🥉 #3</span>
                            <div class="truncate">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate">StudioPro_DL</div>
                                <div class="text-[10px] text-zinc-400">🥈 Silver Creator</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 font-mono">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100">₹47,976.00</div>
                            <div class="text-[10px] text-zinc-400">12 Referrals</div>
                        </div>
                    </div>

                    <!-- Rank 4: Current User -->
                    <div class="p-3 flex items-center justify-between gap-2.5 bg-zinc-100/70 dark:bg-zinc-800/60">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="font-mono font-bold text-sm w-7 text-zinc-900 dark:text-zinc-100">#4</span>
                            <div class="truncate">
                                <div class="font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5 truncate">
                                    <span><?php echo esc_html( $custom_slug ); ?></span>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">You</span>
                                </div>
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400">🥉 Bronze Partner</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 font-mono">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $curr_sym . number_format( $total_comm, 2 ) ); ?></div>
                            <div class="text-[10px] text-zinc-500 dark:text-zinc-400"><?php echo (int) $paid_signups; ?> Referrals</div>
                        </div>
                    </div>

                    <!-- Rank 5 -->
                    <div class="p-3 flex items-center justify-between gap-2.5 hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="font-mono text-zinc-400 text-sm w-7">#5</span>
                            <div class="truncate">
                                <div class="text-zinc-800 dark:text-zinc-200 font-medium truncate">VerveFilms_HYD</div>
                                <div class="text-[10px] text-zinc-400">🥉 Bronze Partner</div>
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
        <div id="cora-aff-subtab-calculator" class="cora-aff-subtab hidden space-y-3.5">
            
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-5 shadow-3xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100">Earnings Potential Calculator</h3>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Simulate recurring payouts + milestone cash bonuses + AI Runes.</p>
                    </div>

                    <!-- Segmented Switcher -->
                    <div class="inline-flex p-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-xs self-start sm:self-auto">
                        <button type="button" onclick="coraSetBillingMode('annual')" id="cora-mode-annual" class="px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-3xs cursor-pointer transition-all">Annual (30%)</button>
                        <button type="button" onclick="coraSetBillingMode('monthly')" id="cora-mode-monthly" class="px-2.5 py-1 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all">Monthly (20%)</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">Client Plan</label>
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

                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">
                            <span>Referred Paid Clients</span>
                            <span id="cora-sim-clients-badge" class="font-mono font-bold text-zinc-900 dark:text-zinc-100">10 Studios</span>
                        </div>
                        <input id="cora-sim-range" type="range" min="1" max="100" value="10" step="1" oninput="coraRecalculateSimulator()" class="w-full h-2 bg-zinc-200 dark:bg-zinc-800 rounded-lg appearance-none cursor-pointer accent-zinc-900 dark:accent-zinc-100">
                        <div class="flex justify-between text-[9px] text-zinc-400 font-mono">
                            <span>1</span>
                            <span>10 🎯 (+<?php echo $is_india_geo ? '₹1k' : '$10'; ?>)</span>
                            <span>50 🚀 (+<?php echo $is_india_geo ? '₹5k' : '$50'; ?>)</span>
                            <span>100 👑 (+<?php echo $is_india_geo ? '₹10k' : '$100'; ?>)</span>
                        </div>
                    </div>
                </div>

                <!-- 3 Return Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl flex flex-col justify-between">
                        <span id="cora-sim-payout-label" class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Est. Cash Payout</span>
                        <div class="mt-1">
                            <div id="cora-sim-monthly-cash" class="text-xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight"><?php echo $is_india_geo ? '₹60,970' : '$580'; ?></div>
                            <div id="cora-sim-yearly-cash" class="text-[10px] text-zinc-400 font-mono mt-0.5"><?php echo $is_india_geo ? '₹5,081/mo equiv' : '$48/mo equiv'; ?></div>
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl flex flex-col justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Milestone Bonus</span>
                        <div class="mt-1">
                            <div id="cora-sim-bonus-cash" class="text-xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight"><?php echo $is_india_geo ? '+₹1,000' : '+$10'; ?></div>
                            <div class="text-[10px] text-zinc-400 mt-0.5">Instant bonus reward</div>
                        </div>
                    </div>

                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl flex flex-col justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Free AI Runes</span>
                        <div class="mt-1">
                            <div id="cora-sim-runes-val" class="text-xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight">+1,000</div>
                            <div class="text-[10px] text-zinc-400 mt-0.5">+100 Runes per signup</div>
                        </div>
                    </div>
                </div>

                <!-- High-density 4-card matrix -->
                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800/80 grid grid-cols-2 lg:grid-cols-4 gap-2 text-xs">
                    <?php if ( $is_india_geo ) : ?>
                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-0.5 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">India Only</div>
                            <div class="text-zinc-400 font-mono text-[10px]">₹499/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>₹1,796</strong>/yr</div>
                        </div>

                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-0.5 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Starter Tier</div>
                            <div class="text-zinc-400 font-mono text-[10px]">₹999/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>₹2,997</strong>/yr</div>
                        </div>

                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-0.5 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Professional</div>
                            <div class="text-zinc-400 font-mono text-[10px]">₹1,999/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>₹5,997</strong>/yr</div>
                        </div>

                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-0.5 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Scale Tier</div>
                            <div class="text-zinc-400 font-mono text-[10px]">₹2,999/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>₹8,997</strong>/yr</div>
                        </div>
                    <?php else : ?>
                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-0.5 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Starter Global</div>
                            <div class="text-zinc-400 font-mono text-[10px]">$9/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>$27</strong>/yr</div>
                        </div>

                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-0.5 text-center">
                            <div class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px]">Professional Global</div>
                            <div class="text-zinc-400 font-mono text-[10px]">$19/mo</div>
                            <div class="text-[10px] font-mono text-zinc-800 dark:text-zinc-200">30%: <strong>$57</strong>/yr</div>
                        </div>

                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-950/60 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 space-y-0.5 text-center">
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
        <div id="cora-aff-subtab-referrals" class="cora-aff-subtab hidden space-y-3.5">
            
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-3xs overflow-hidden">
                <!-- Header & Filter Tabs -->
                <div class="p-3.5 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-50">Conversion Activity Ledger</h3>
                        <p class="text-[11px] text-zinc-400">Real-time log of all referred signups, plan conversions, and earned rewards.</p>
                    </div>

                    <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-800 p-0.5 rounded-lg self-start sm:self-auto">
                        <button type="button" onclick="coraFilterReferralTable('all')" id="cora-filter-all" class="px-2.5 py-1 text-xs font-bold rounded-md bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-3xs transition-all cursor-pointer">All</button>
                        <button type="button" onclick="coraFilterReferralTable('paid')" id="cora-filter-paid" class="px-2.5 py-1 text-xs font-medium rounded-md text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer">Paid</button>
                        <button type="button" onclick="coraFilterReferralTable('free')" id="cora-filter-free" class="px-2.5 py-1 text-xs font-medium rounded-md text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer">Free (100 Runes)</button>
                    </div>
                </div>

                <!-- High-density Row List (Zero horizontal scrolling) -->
                <div id="cora-referral-list-container" class="divide-y divide-zinc-100 dark:divide-zinc-800/80 text-xs">
                    <?php if ( ! empty( $referrals ) ) : ?>
                        <?php foreach ( $referrals as $ref ) : 
                            $is_paid = $ref['conversion_type'] === 'paid_conversion';
                            $row_class = $is_paid ? 'cora-row-paid' : 'cora-row-free';
                            $time_ago = human_time_diff( strtotime( $ref['created_at'] ), current_time( 'timestamp' ) ) . ' ago';
                        ?>
                        <div class="cora-ref-item p-3 flex items-center justify-between gap-2.5 hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors <?php echo esc_attr( $row_class ); ?>">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-xs text-zinc-700 dark:text-zinc-300 shrink-0">
                                    <?php echo esc_html( strtoupper( substr( $ref['referred_name'] ?? 'P', 0, 1 ) ) ); ?>
                                </div>
                                <div class="truncate">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html( $ref['referred_name'] ); ?></div>
                                    <div class="text-[10px] text-zinc-400 font-mono truncate"><?php echo esc_html( $ref['referred_email'] ); ?> • <?php echo esc_html( $time_ago ); ?></div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <div class="hidden sm:block text-right">
                                    <?php if ( $is_paid ) : ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                                            <?php echo esc_html( $ref['plan_name'] ); ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                            Free Signup
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="text-right font-mono">
                                    <?php if ( $is_paid ) : ?>
                                        <div class="font-bold text-zinc-900 dark:text-zinc-100">+<?php echo esc_html( $curr_sym . number_format( (float) $ref['commission_earned'], 2 ) ); ?></div>
                                        <div class="text-[9px] text-zinc-400">Paid Commission</div>
                                    <?php else : ?>
                                        <div class="font-bold text-zinc-800 dark:text-zinc-200">+100 Runes</div>
                                        <div class="text-[9px] text-zinc-400">Free Trial</div>
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
        <div id="cora-aff-subtab-payouts" class="cora-aff-subtab hidden space-y-3.5">
            
            <!-- Available Balance & Threshold Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-3xs space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Available Payout Balance</span>
                        <div class="text-2xl sm:text-3xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 tracking-tight mt-0.5"><?php echo esc_html( $curr_sym . number_format( $avail_bal, 2 ) ); ?></div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Direct transfer to your UPI VPA or Bank IMPS account.</p>
                    </div>

                    <button type="button" onclick="coraOpenAffiliatePayoutDrawer()" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-xl shadow-3xs transition-all cursor-pointer self-start sm:self-auto">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        <span>Request Payout Transfer</span>
                    </button>
                </div>

                <!-- Minimum Threshold Progress -->
                <div class="pt-2.5 border-t border-zinc-100 dark:border-zinc-800/80 space-y-1">
                    <div class="flex items-center justify-between text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                        <span>Withdrawal Threshold: <strong class="text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $curr_sym . number_format( $min_payout, 0 ) ); ?> Minimum</strong></span>
                        <span class="font-mono text-zinc-700 dark:text-zinc-300"><?php echo esc_html( $payout_progress_pct ); ?>% Ready</span>
                    </div>
                    <div class="w-full h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-zinc-900 dark:bg-zinc-100 rounded-full transition-all duration-500" style="width: <?php echo esc_attr( $payout_progress_pct ); ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- PAYOUT & WITHDRAWAL HISTORY (Zero horizontal scroll) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-3xs overflow-hidden">
                <div class="p-3.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-900 dark:text-zinc-50">Payout & Withdrawal History</h3>
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
                                ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border-zinc-200 dark:border-zinc-700' 
                                : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700';
                        ?>
                        <div class="p-3 flex items-center justify-between gap-2.5 hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors">
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
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold border <?php echo esc_attr( $status_badge ); ?> mt-0.5">
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
<div id="cora-affiliate-payout-drawer" class="fixed inset-0 z-50 pointer-events-none opacity-0 transition-opacity duration-300">
    <!-- Backdrop Overlay -->
    <div onclick="coraCloseAffiliatePayoutDrawer()" class="absolute inset-0 bg-zinc-950/45 backdrop-blur-xs cursor-pointer"></div>

    <!-- Drawer Panel: Bottom-Sheet on Mobile (<640px), Right-Sliding on Desktop (>=640px) -->
    <div class="cora-payout-drawer-panel absolute sm:right-0 bottom-0 sm:top-0 w-full sm:max-w-md max-h-[85vh] sm:max-h-full bg-white dark:bg-zinc-900 border-t sm:border-t-0 sm:border-l border-zinc-200 dark:border-zinc-800 rounded-t-3xl sm:rounded-none shadow-2xl p-4 sm:p-6 flex flex-col justify-between transform translate-y-full sm:translate-y-0 sm:translate-x-full transition-transform duration-300 ease-out pointer-events-auto overflow-y-auto">
        
        <div>
            <!-- Mobile Drag Handle -->
            <div class="w-10 h-1 rounded-full bg-zinc-300 dark:bg-zinc-700 mx-auto mb-3 sm:hidden"></div>

            <!-- Drawer Header -->
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Request Payout Transfer</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Transfer your earned referral commission.</p>
                </div>
                <button type="button" onclick="coraCloseAffiliatePayoutDrawer()" class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Balance Summary -->
            <div class="mt-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl p-3">
                <span class="text-[11px] text-zinc-500 dark:text-zinc-400">Available Balance:</span>
                <div class="text-xl font-extrabold font-mono text-zinc-900 dark:text-zinc-50 mt-0.5"><?php echo esc_html( $curr_sym . number_format( $avail_bal, 2 ) ); ?></div>
                <span class="text-[10px] text-zinc-400">Minimum withdrawal amount: <?php echo esc_html( $curr_sym . number_format( $min_payout, 0 ) ); ?></span>
            </div>

            <!-- Form -->
            <form id="cora-payout-form" onsubmit="coraSubmitPayoutRequest(event)" class="mt-3.5 space-y-3">
                
                <!-- Amount Input -->
                <div class="space-y-1">
                    <label for="cora-payout-amount" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Withdrawal Amount (<?php echo esc_html( $curr_sym ); ?>)</label>
                    <input id="cora-payout-amount" type="number" min="<?php echo esc_attr( $min_payout ); ?>" max="<?php echo esc_attr( $avail_bal ); ?>" step="100" value="<?php echo esc_attr( min( 5000, max( $min_payout, $avail_bal ) ) ); ?>" class="w-full px-3 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    <div class="flex items-center gap-1.5 pt-1">
                        <button type="button" onclick="document.getElementById('cora-payout-amount').value = <?php echo esc_attr( $min_payout ); ?>" class="px-2 py-0.5 text-[10px] bg-zinc-100 dark:bg-zinc-800 rounded text-zinc-600 dark:text-zinc-400 cursor-pointer"><?php echo esc_html( $curr_sym . number_format( $min_payout, 0 ) ); ?></button>
                        <button type="button" onclick="document.getElementById('cora-payout-amount').value = 5000" class="px-2 py-0.5 text-[10px] bg-zinc-100 dark:bg-zinc-800 rounded text-zinc-600 dark:text-zinc-400 cursor-pointer"><?php echo esc_html( $curr_sym ); ?>5,000</button>
                        <button type="button" onclick="document.getElementById('cora-payout-amount').value = <?php echo esc_attr( $avail_bal ); ?>" class="px-2 py-0.5 text-[10px] bg-zinc-100 dark:bg-zinc-800 rounded text-zinc-600 dark:text-zinc-400 cursor-pointer">Max Balance</button>
                    </div>
                </div>

                <!-- Payout Method Selection -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Payout Method</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer">
                            <input type="radio" name="payout_method" value="upi" checked onchange="coraTogglePayoutFields('upi')" class="accent-zinc-900 dark:accent-zinc-100">
                            <span class="text-xs font-medium text-zinc-800 dark:text-zinc-200">UPI ID</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer">
                            <input type="radio" name="payout_method" value="bank_transfer" onchange="coraTogglePayoutFields('bank_transfer')" class="accent-zinc-900 dark:accent-zinc-100">
                            <span class="text-xs font-medium text-zinc-800 dark:text-zinc-200">Bank IMPS</span>
                        </label>
                    </div>
                </div>

                <!-- UPI Fields -->
                <div id="cora-payout-field-upi" class="space-y-1">
                    <label for="cora-upi-id" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">UPI ID / VPA</label>
                    <input id="cora-upi-id" type="text" placeholder="username@okaxis or mobile@upi" class="w-full px-3 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                </div>

                <!-- Bank Fields -->
                <div id="cora-payout-field-bank" class="hidden space-y-2">
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
        <div class="pt-3 mt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end gap-2">
            <button type="button" onclick="coraCloseAffiliatePayoutDrawer()" class="px-3.5 py-1.5 text-xs font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 cursor-pointer">Cancel</button>
            <button type="button" onclick="document.getElementById('cora-payout-form').requestSubmit()" class="px-4 py-1.5 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-xl shadow-3xs transition-all cursor-pointer">Confirm Withdrawal</button>
        </div>

    </div>
</div>

<!-- ================================================================= -->
<!-- QR CODE PREVIEW MODAL                                             -->
<!-- ================================================================= -->
<div id="cora-affiliate-qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div onclick="coraCloseQRCodeModal()" class="absolute inset-0 bg-zinc-950/45 backdrop-blur-xs cursor-pointer"></div>
    <div class="relative w-full max-w-xs bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-2xl z-10 text-center space-y-3">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Scan Partner QR Code</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Let clients scan this code to sign up under your agency.</p>
        </div>

        <!-- Generated QR Vector -->
        <div class="bg-zinc-50 dark:bg-zinc-950 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center justify-center">
            <div id="cora-qr-canvas-holder" class="p-2 bg-white rounded-lg shadow-3xs">
                <img id="cora-qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?php echo urlencode( $ref_url ); ?>&color=18-18-1b" alt="QR Code" width="160" height="160" class="rounded-md">
            </div>
        </div>

        <div class="pt-1 flex items-center justify-center gap-2">
            <button type="button" onclick="coraCloseQRCodeModal()" class="px-3.5 py-1.5 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all cursor-pointer">Close</button>
            <button type="button" onclick="coraCopyReferralLink(); coraCloseQRCodeModal();" class="px-4 py-1.5 text-xs font-semibold text-white bg-zinc-950 dark:bg-zinc-100 dark:text-zinc-950 rounded-xl hover:opacity-90 transition-all cursor-pointer">Copy Link</button>
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- JAVASCRIPT CONTROLLERS                                            -->
<!-- ================================================================= -->
<script>
window.coraAffiliateState = {
    referralUrl: <?php echo json_encode( $ref_url ); ?>,
    refCode: <?php echo json_encode( $ref_code ); ?>,
    customSlug: <?php echo json_encode( $custom_slug ); ?>,
    availBalance: <?php echo json_encode( (float) $avail_bal ); ?>,
    isEnrolled: <?php echo json_encode( (bool) $is_enrolled ); ?>,
    activeTab: 'overview',
    billingMode: 'annual',
    screenerBillingMode: 'annual'
};

/* --- SUB-TAB SWITCHER --- */
function coraSwitchAffiliateTab(tabKey) {
    window.coraAffiliateState.activeTab = tabKey;
    var subtabs = ['overview', 'leaderboard', 'calculator', 'referrals', 'payouts'];

    subtabs.forEach(function(key) {
        var tabView = document.getElementById('cora-aff-subtab-' + key);
        var tabBtn = document.getElementById('cora-tab-btn-' + key);

        if (tabView) {
            if (key === tabKey) {
                tabView.classList.remove('hidden');
            } else {
                tabView.classList.add('hidden');
            }
        }

        if (tabBtn) {
            if (key === tabKey) {
                tabBtn.className = 'cora-aff-tab-btn active inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer text-zinc-950 dark:text-zinc-50 bg-zinc-200/70 dark:bg-zinc-800 whitespace-nowrap';
            } else {
                tabBtn.className = 'cora-aff-tab-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/50 whitespace-nowrap';
            }
        }
    });

    var activeBtn = document.getElementById('cora-tab-btn-' + tabKey);
    var container = document.getElementById('cora-affiliate-tabs-container');
    if (activeBtn && container) {
        var btnOffset = activeBtn.offsetLeft;
        var btnWidth = activeBtn.offsetWidth;
        var containerWidth = container.offsetWidth;
        container.scrollTo({
            left: btnOffset - (containerWidth / 2) + (btnWidth / 2),
            behavior: 'smooth'
        });
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
                tab.className = 'flex items-center gap-2 p-2 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 transition-all';
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
        if (btnAnnual) btnAnnual.className = 'px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-3xs cursor-pointer transition-all';
        if (btnMonthly) btnMonthly.className = 'px-2.5 py-1 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all';
    } else {
        if (btnAnnual) btnAnnual.className = 'px-2.5 py-1 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all';
        if (btnMonthly) btnMonthly.className = 'px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-3xs cursor-pointer transition-all';
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
        if (btnAnnual) btnAnnual.className = 'px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-3xs cursor-pointer transition-all';
        if (btnMonthly) btnMonthly.className = 'px-2.5 py-1 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all';
    } else {
        if (btnAnnual) btnAnnual.className = 'px-2.5 py-1 rounded-lg font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer transition-all';
        if (btnMonthly) btnMonthly.className = 'px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-3xs cursor-pointer transition-all';
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
                submitBtn.innerHTML = '<span>Complete Enrollment & Launch Portal</span>';
            }
            if (window.coraShowToast) {
                window.coraShowToast(res.data.message || 'Error completing enrollment', 'error');
            }
        }
    })
    .catch(function(){
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>Complete Enrollment & Launch Portal</span>';
        }
        if (window.coraShowToast) window.coraShowToast('Network error while completing partner enrollment', 'error');
    });
}

/* --- DASHBOARD HANDLERS --- */
function coraCopyReferralLink() {
    var input = document.getElementById('cora-ref-link-input');
    if (input) {
        input.select();
        navigator.clipboard.writeText(input.value).then(function() {
            if (window.coraShowToast) {
                window.coraShowToast('Referral link copied to clipboard!', 'success');
            }
        });
    }
}

function coraToggleSlugEditor() {
    var box = document.getElementById('cora-slug-editor');
    if (box) {
        box.classList.toggle('hidden');
    }
}

function coraSaveCustomSlug() {
    var input = document.getElementById('cora-custom-slug-input');
    var slug = input ? input.value.trim() : '';
    if (!slug) return;

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
        if (res.success) {
            document.getElementById('cora-ref-link-input').value = res.data.referral_url;
            document.getElementById('cora-display-slug').innerText = res.data.custom_slug;
            window.coraAffiliateState.referralUrl = res.data.referral_url;
            coraToggleSlugEditor();
            if (window.coraShowToast) {
                window.coraShowToast('Custom referral handle updated!', 'success');
            }
        } else {
            if (window.coraShowToast) {
                window.coraShowToast(res.data.message || 'Error updating slug', 'error');
            }
        }
    })
    .catch(function(){
        if (window.coraShowToast) window.coraShowToast('Network error while updating slug', 'error');
    });
}

function coraFilterReferralTable(filter) {
    var rows = document.querySelectorAll('.cora-ref-item');
    var btnAll = document.getElementById('cora-filter-all');
    var btnPaid = document.getElementById('cora-filter-paid');
    var btnFree = document.getElementById('cora-filter-free');

    [btnAll, btnPaid, btnFree].forEach(function(btn){
        if (btn) {
            btn.className = 'px-2.5 py-1 text-xs font-medium rounded-md text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer';
        }
    });

    var activeBtn = filter === 'paid' ? btnPaid : (filter === 'free' ? btnFree : btnAll);
    if (activeBtn) {
        activeBtn.className = 'px-2.5 py-1 text-xs font-bold rounded-md bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-3xs transition-all cursor-pointer';
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
        drawer.classList.remove('pointer-events-none', 'opacity-0');
        drawer.classList.add('opacity-100');
        var panel = drawer.querySelector('.cora-payout-drawer-panel');
        if (panel) {
            panel.classList.remove('translate-y-full', 'sm:translate-x-full');
            panel.classList.add('translate-y-0', 'sm:translate-x-0');
        }
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
        setTimeout(function(){
            drawer.classList.add('pointer-events-none', 'opacity-0');
            drawer.classList.remove('opacity-100');
        }, 250);
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
    if (modal) modal.classList.remove('hidden'), modal.classList.add('flex');
}

function coraCloseQRCodeModal() {
    var modal = document.getElementById('cora-affiliate-qr-modal');
    if (modal) modal.classList.add('hidden'), modal.classList.remove('flex');
}

function coraShareWhatsApp() {
    var url = encodeURIComponent(window.coraAffiliateState.referralUrl);
    var text = encodeURIComponent("Join Cora - the AI workspace for creative studios and agencies. Sign up with my referral link to get 100 free bonus AI Runes: " + decodeURIComponent(url));
    window.open("https://api.whatsapp.com/send?text=" + text, '_blank');
}

function coraShareLinkedIn() {
    var url = encodeURIComponent(window.coraAffiliateState.referralUrl);
    window.open("https://www.linkedin.com/sharing/share-offsite/?url=" + url, '_blank');
}

function coraShareTwitter() {
    var url = encodeURIComponent(window.coraAffiliateState.referralUrl);
    var text = encodeURIComponent("Check out Cora - the AI workspace for agencies and studios. Join via my link for 100 bonus AI Runes: ");
    window.open("https://twitter.com/intent/tweet?text=" + text + "&url=" + url, '_blank');
}
</script>
