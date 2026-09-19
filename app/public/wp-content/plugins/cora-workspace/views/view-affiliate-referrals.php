<?php
/**
 * Cora Affiliate & Referral Workspace View
 * 
 * Provides:
 * 1. 3-Step Screener / Partner Onboarding Application for unenrolled workspace owners:
 *    - Step 1: Program Benefits, 40% Commission & Free 100 AI Credits, Interactive Earnings Calculator.
 *    - Step 2: Agency Profile, Custom Referral Slug, Promotion Strategy, and Payout Preferences.
 *    - Step 3: Industry Standards, Anti-Spam Code of Conduct, Eligibility & Payout Compliance Terms.
 * 2. Full Active Partner Workspace Dashboard for enrolled users:
 *    - Dedicated Referral Link Bar with official brand sharing shortcuts (WhatsApp, LinkedIn, X, QR Code).
 *    - Key metrics (Total Cash Earned @ 40%, Available Payout, AI Credits Granted @ 100/signup, Funnel Traffic).
 *    - Annual Earnings Calculator & Geolocation-based Annual Pricing Matrix.
 *    - Real-time Conversion Activity Ledger with category filters.
 *    - Payout & Withdrawal Ledger with Sliding Request Drawer.
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
        'commission_rate'        => 40.0,
        'min_payout'             => 1000.0,
    );

$ref_url       = $affiliate_data['referral_url'];
$ref_code      = $affiliate_data['ref_code'];
$custom_slug   = isset( $affiliate_data['custom_slug'] ) && $affiliate_data['custom_slug'] ? $affiliate_data['custom_slug'] : $ref_code;
$total_comm    = $affiliate_data['total_commission'];
$avail_bal     = $affiliate_data['available_balance'];
$ai_credits    = $affiliate_data['total_ai_credits'];
$clicks        = $affiliate_data['clicks_count'];
$conv_rate     = $affiliate_data['conversion_rate'];
$referrals     = $affiliate_data['referrals'];
$payouts       = $affiliate_data['payouts'];
$free_signups  = $affiliate_data['free_signups_count'];
$paid_signups  = $affiliate_data['paid_conversions_count'];

// Geolocation-based currency detection (Default India / INR; fallback to USD if configured or query param set)
$currency_format = get_option( 'cora_currency_format', 'INR_LAKHS' );
$is_india_geo = true;
if ( strpos( $currency_format, 'USD' ) !== false || ( isset( $_GET['currency'] ) && strtoupper( sanitize_text_field( wp_unslash( $_GET['currency'] ) ) ) === 'USD' ) || ( isset( $_GET['geo'] ) && strtolower( sanitize_text_field( wp_unslash( $_GET['geo'] ) ) ) === 'global' ) ) {
    $is_india_geo = false;
}
?>

<div id="cora-affiliate-root" class="space-y-6">

    <!-- ================================================================= -->
    <!-- VIEW A: 3-STEP PARTNER ENROLLMENT SCREENER (FOR UNENROLLED USERS) -->
    <!-- ================================================================= -->
    <div id="cora-affiliate-screener-view" class="<?php echo $is_enrolled ? 'hidden' : ''; ?> max-w-4xl mx-auto space-y-6">
        
        <!-- Screener Header -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-xs relative overflow-hidden">
            <div class="relative z-10 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Partner & Affiliate Program</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-zinc-900 dark:text-zinc-50 tracking-tight">
                    Partner with Cora & Monetize Your Client Network
                </h1>
                <p class="text-xs md:text-sm text-zinc-500 dark:text-zinc-400 max-w-2xl leading-relaxed">
                    Enroll your agency into the official Cora Partner Network. Earn <span class="font-semibold text-zinc-900 dark:text-zinc-100">40% recurring cash commission</span> on all paid annual subscriptions and <span class="font-semibold text-zinc-900 dark:text-zinc-100">100 bonus AI credits</span> for every free client signup.
                </p>
            </div>

            <!-- 3-Step Stepper Progress Header -->
            <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800/80 grid grid-cols-3 gap-2 text-xs font-semibold">
                <div id="cora-step-tab-1" class="flex items-center gap-2 p-2.5 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 transition-all">
                    <span class="w-5 h-5 rounded-full bg-white/20 dark:bg-zinc-900/20 flex items-center justify-center text-[10px] font-bold">1</span>
                    <span class="truncate">1. Benefits & Returns</span>
                </div>
                <div id="cora-step-tab-2" class="flex items-center gap-2 p-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-all">
                    <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold">2</span>
                    <span class="truncate">2. Agency Profile</span>
                </div>
                <div id="cora-step-tab-3" class="flex items-center gap-2 p-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-all">
                    <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold">3</span>
                    <span class="truncate">3. Terms & Eligibility</span>
                </div>
            </div>
        </div>

        <!-- STEP 1: BENEFITS & EARNINGS MODEL -->
        <div id="cora-screener-step-1" class="space-y-6">
            
            <!-- 4 Pillar Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl shadow-xs space-y-2">
                    <div class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-900 dark:text-zinc-100 font-bold text-sm">
                        40%
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">40% Recurring Cash Commission</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Earn a massive 40% payout on all converted clients who subscribe to Cora Annual Plans (including Starter, Pro, and Scale). Paid directly to your bank account or UPI.
                    </p>
                </div>

                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl shadow-xs space-y-2">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-bold">
                        +100
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">100 AI Credits per Free Signup</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Even if your client only starts on the free tier, your agency instantly receives 100 AI Generation Credits credited to your workspace quota immediately upon email verification.
                    </p>
                </div>

                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl shadow-xs space-y-2">
                    <div class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-900 dark:text-zinc-100">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">30-Day Attribution Cookie</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Clients who click your dedicated link or scan your QR code are attributed to your workspace for 30 days, guaranteeing you receive credit whenever they complete their subscription.
                    </p>
                </div>

                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl shadow-xs space-y-2">
                    <div class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-900 dark:text-zinc-100">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Direct Bank & UPI Disbursements</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Request withdrawal payouts anytime your balance reaches ₹1,000 / $50. Transferred within 24-48 business hours with zero hidden processing fees.
                    </p>
                </div>
            </div>

            <!-- Embedded Annual Earnings Calculator Preview -->
            <div class="bg-zinc-900 text-white rounded-3xl p-6 shadow-sm border border-zinc-800 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold tracking-tight">Projected Annual Earnings Simulator</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Annual Plans Only</span>
                        </div>
                        <p class="text-xs text-zinc-400 mt-0.5">Simulate your annual partner payout based on client referrals.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-zinc-400">Target Plan:</span>
                        <select id="cora-screener-sim-tier" onchange="coraRecalculateScreenerSimulator()" class="px-2.5 py-1.5 text-xs bg-zinc-800 text-zinc-100 border border-zinc-700 rounded-lg focus:outline-none cursor-pointer font-medium">
                            <?php if ( $is_india_geo ) : ?>
                                <option value="19990" data-curr="₹" data-annual="19990" data-monthly="1665" selected>Professional Annual (₹1,665/mo • ₹19,990/yr)</option>
                                <option value="9990" data-curr="₹" data-annual="9990" data-monthly="833">Starter Annual (₹833/mo • ₹9,990/yr)</option>
                                <option value="29990" data-curr="₹" data-annual="29990" data-monthly="2499">Scale Annual (₹2,499/mo • ₹29,990/yr)</option>
                                <option value="5988" data-curr="₹" data-annual="5988" data-monthly="499">India Only Plan (₹499/mo • ₹5,988/yr)</option>
                            <?php else : ?>
                                <option value="190" data-curr="$" data-annual="190" data-monthly="15.83" selected>Professional Global ($15.83/mo • $190/yr)</option>
                                <option value="90" data-curr="$" data-annual="90" data-monthly="7.50">Starter Global ($7.50/mo • $90/yr)</option>
                                <option value="290" data-curr="$" data-annual="290" data-monthly="24.16">Scale Global ($24.16/mo • $290/yr)</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
                    <div class="lg:col-span-2 space-y-4">
                        <div class="flex items-center justify-between">
                            <label for="cora-screener-sim-range" class="text-xs font-semibold text-zinc-300">Referred Annual Clients / Agencies</label>
                            <span id="cora-screener-sim-clients-badge" class="px-3 py-1 bg-zinc-800 border border-zinc-700 rounded-lg text-xs font-mono font-bold text-zinc-100">10 Agencies</span>
                        </div>
                        <input id="cora-screener-sim-range" type="range" min="1" max="50" value="10" step="1" oninput="coraRecalculateScreenerSimulator()" class="w-full h-2 bg-zinc-800 rounded-lg appearance-none cursor-pointer accent-white">
                        <div class="flex justify-between text-[10px] text-zinc-500 font-mono">
                            <span>1 Client</span>
                            <span>10 Clients</span>
                            <span>25 Clients</span>
                            <span>50 Clients</span>
                        </div>
                    </div>

                    <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-2xl p-4.5 flex flex-col justify-center text-center lg:text-left">
                        <span class="text-[11px] uppercase tracking-wider text-zinc-400 font-medium">Estimated 40% Cash Payout</span>
                        <span id="cora-screener-sim-monthly-cash" class="text-3xl font-extrabold text-white tracking-tight mt-1"><?php echo $is_india_geo ? '₹79,960' : '$760'; ?></span>
                        <span id="cora-screener-sim-yearly-cash" class="text-[11px] text-zinc-400 mt-1"><?php echo $is_india_geo ? '₹6,663/mo equivalent + 1,000 AI credits' : '$63.33/mo equivalent + 1,000 AI credits'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Step 1 Bottom Bar -->
            <div class="flex items-center justify-end pt-2">
                <button type="button" onclick="coraScreenerGoToStep(2)" class="inline-flex items-center gap-2 px-6 py-3 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-2xl shadow-sm transition-all cursor-pointer">
                    <span>Continue to Agency Profile</span>
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <!-- STEP 2: AGENCY PROFILE & PAYOUT PREFERENCE -->
        <div id="cora-screener-step-2" class="hidden space-y-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-xs space-y-6">
                
                <div>
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Agency Profile & Referral Customization</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Your partner identity is automatically linked to your authenticated workspace.</p>
                </div>

                <!-- Account Information (Pre-filled) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Partner Full Name</label>
                        <input type="text" id="cora-enroll-name" value="<?php echo esc_attr( $user_display_name ); ?>" class="w-full px-3.5 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Workspace / Studio</label>
                        <input type="text" id="cora-enroll-workspace" value="<?php echo esc_attr( $workspace_name ); ?>" class="w-full px-3.5 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Registered Email</label>
                        <input type="email" id="cora-enroll-email" readonly value="<?php echo esc_attr( $user_email ); ?>" class="w-full px-3.5 py-2 text-xs bg-zinc-100/70 dark:bg-zinc-950/70 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-500 dark:text-zinc-400 cursor-not-allowed">
                    </div>
                </div>

                <!-- Custom Slug Selector -->
                <div class="space-y-1.5 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                    <label class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">Choose Your Dedicated Referral Code / Slug</label>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">This handle will appear in your referral URL and dynamic QR code.</p>
                    <div class="relative flex items-center max-w-md">
                        <span class="absolute left-3.5 text-xs text-zinc-400 font-mono select-none">cora.local/?ref=</span>
                        <input id="cora-enroll-slug" type="text" value="<?php echo esc_attr( $default_slug ); ?>" placeholder="your-agency-name" class="w-full pl-32 pr-3.5 py-2.5 text-xs font-mono font-bold bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-zinc-400">
                    </div>
                </div>

                <!-- Promotion Channels Multi-Select -->
                <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                    <label class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">Primary Promotion & Referral Channels</label>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Select where you plan to share Cora with your clients and network:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 pt-1">
                        <label class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer hover:bg-zinc-100/60 dark:hover:bg-zinc-800/60 transition-all text-xs">
                            <input type="checkbox" name="cora_promo_channel" value="client_invoicing" checked class="accent-zinc-900 dark:accent-zinc-100 rounded">
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">Client Invoices & Proposals</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer hover:bg-zinc-100/60 dark:hover:bg-zinc-800/60 transition-all text-xs">
                            <input type="checkbox" name="cora_promo_channel" value="social_media" checked class="accent-zinc-900 dark:accent-zinc-100 rounded">
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">Social Media (LinkedIn, X, IG)</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer hover:bg-zinc-100/60 dark:hover:bg-zinc-800/60 transition-all text-xs">
                            <input type="checkbox" name="cora_promo_channel" value="agency_network" checked class="accent-zinc-900 dark:accent-zinc-100 rounded">
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">Agency & Creator Networks</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer hover:bg-zinc-100/60 dark:hover:bg-zinc-800/60 transition-all text-xs">
                            <input type="checkbox" name="cora_promo_channel" value="email_newsletter" class="accent-zinc-900 dark:accent-zinc-100 rounded">
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">Email Newsletters & Case Studies</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer hover:bg-zinc-100/60 dark:hover:bg-zinc-800/60 transition-all text-xs">
                            <input type="checkbox" name="cora_promo_channel" value="word_of_mouth" checked class="accent-zinc-900 dark:accent-zinc-100 rounded">
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">Direct Word-of-Mouth</span>
                        </label>
                    </div>
                </div>

                <!-- Preferred Payout Method (Optional at setup) -->
                <div class="space-y-3 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                    <div>
                        <label class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">Preferred Payout Method (Optional)</label>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">You can also configure or update your bank details later from your withdrawal drawer.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="cora-enroll-upi" class="text-xs font-medium text-zinc-700 dark:text-zinc-300">VPA / UPI ID</label>
                            <input id="cora-enroll-upi" type="text" placeholder="agency@okhdfcbank" class="w-full px-3.5 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                        </div>
                        <div class="space-y-1">
                            <label for="cora-enroll-bank" class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Bank Account & IFSC (if preferred)</label>
                            <input id="cora-enroll-bank" type="text" placeholder="Account No. / IFSC Code" class="w-full px-3.5 py-2 text-xs bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Step 2 Bottom Bar -->
            <div class="flex items-center justify-between pt-2">
                <button type="button" onclick="coraScreenerGoToStep(1)" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Back to Overview</span>
                </button>

                <button type="button" onclick="coraScreenerGoToStep(3)" class="inline-flex items-center gap-2 px-6 py-3 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-2xl shadow-sm transition-all cursor-pointer">
                    <span>Continue to Eligibility & Terms</span>
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <!-- STEP 3: INDUSTRY STANDARDS, ELIGIBILITY & TERMS COMPLIANCE -->
        <div id="cora-screener-step-3" class="hidden space-y-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-3xl p-6 md:p-8 shadow-xs space-y-6">
                
                <div>
                    <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Partner Eligibility & Operating Standards</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Please review the industry standard compliance policies before activating your partner portal.</p>
                </div>

                <!-- 4 Standards Cards -->
                <div class="space-y-3">
                    
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center text-[10px] font-bold">1</span>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Verified Agency Eligibility</h4>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 pl-7 leading-relaxed">
                            The program is dedicated to verified creative agencies, production studios, freelance professionals, consultants, and active workspace owners. Accounts generating fraudulent bot clicks or automated signups are subject to immediate termination.
                        </p>
                    </div>

                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center text-[10px] font-bold">2</span>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Ethical Promotion & Zero Spam Policy</h4>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 pl-7 leading-relaxed">
                            Partners must adhere to transparent, honest marketing practices. Spamming unsolicited emails, posting in unauthorized groups, using trademark brand spoofing on paid ads (e.g. Google Ads on "Cora"), or misrepresenting features is strictly prohibited.
                        </p>
                    </div>

                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center text-[10px] font-bold">3</span>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Strict No Self-Referral Rule</h4>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 pl-7 leading-relaxed">
                            Referral rewards are intended exclusively for introducing external client workspaces and partner businesses. Signing up secondary personal accounts to claim commission discounts on your own workspace is strictly disqualified by our audit engine.
                        </p>
                    </div>

                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 flex items-center justify-center text-[10px] font-bold">4</span>
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Payout Processing & Minimum Threshold</h4>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 pl-7 leading-relaxed">
                            The minimum withdrawal payout balance is ₹1,000 (or $50 USD for global accounts). Payout requests are verified and disbursed directly to your designated UPI or Bank account within 24 to 48 business hours.
                        </p>
                    </div>

                </div>

                <!-- Mandatory Agreement Checkbox -->
                <div class="p-4 bg-zinc-100/80 dark:bg-zinc-800/80 rounded-2xl border border-zinc-200 dark:border-zinc-700 space-y-2">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input id="cora-enroll-agree" type="checkbox" class="mt-0.5 accent-zinc-900 dark:accent-zinc-100 rounded w-4 h-4 cursor-pointer">
                        <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100 leading-snug">
                            I verify that I represent an active agency / studio and agree to the <strong>Cora Partner & Affiliate Agreement</strong>, 40% Commission Structure, Anti-Spam Code of Conduct, and Payout Guidelines.
                        </span>
                    </label>
                </div>

            </div>

            <!-- Step 3 Bottom Bar -->
            <div class="flex items-center justify-between pt-2">
                <button type="button" onclick="coraScreenerGoToStep(2)" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Back to Agency Profile</span>
                </button>

                <button id="cora-enroll-submit-btn" type="button" onclick="coraSubmitAffiliateEnrollment()" class="inline-flex items-center gap-2 px-7 py-3 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-2xl shadow-sm transition-all cursor-pointer">
                    <span>Complete Enrollment & Launch Dashboard</span>
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </button>
            </div>
        </div>

    </div>


    <!-- ================================================================= -->
    <!-- VIEW B: ACTIVE PARTNER WORKSPACE DASHBOARD (FOR ENROLLED USERS)  -->
    <!-- ================================================================= -->
    <div id="cora-affiliate-dashboard-view" class="<?php echo ! $is_enrolled ? 'hidden' : ''; ?> space-y-6">

        <!-- 1. HEADER SECTION -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-1 border-b border-zinc-200/80 dark:border-zinc-800">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-zinc-900 text-white flex items-center justify-center shrink-0 shadow-sm dark:bg-zinc-100 dark:text-zinc-900">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-50 tracking-tight">Affiliates & Referrals</h1>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">Partner Active</span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Earn 100 AI credits per free signup and 40% recurring cash commission on paid client conversions.</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <button type="button" onclick="coraOpenAffiliatePayoutDrawer()" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-zinc-800 dark:text-zinc-200 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    <span>Request Payout</span>
                </button>

                <button type="button" onclick="coraCopyReferralLink()" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-xl shadow-sm transition-all cursor-pointer">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span>Copy Referral Link</span>
                </button>
            </div>
        </div>

        <!-- 2. HERO REFERRAL LINK BAR -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl p-5 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="space-y-1.5 flex-1 max-w-xl">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Your Dedicated Referral Link</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">30-Day Cookie Active</span>
                    </div>
                    <div class="relative flex items-center">
                        <input id="cora-ref-link-input" type="text" readonly value="<?php echo esc_attr( $ref_url ); ?>" class="w-full pl-3.5 pr-28 py-2.5 text-xs font-mono bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 select-all focus:outline-none focus:ring-1 focus:ring-zinc-400">
                        <button type="button" onclick="coraCopyReferralLink()" class="absolute right-1.5 px-3 py-1.5 text-xs font-medium bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 rounded-lg transition-all cursor-pointer">
                            Copy
                        </button>
                    </div>
                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500">Share this link with agencies, clients, and partners. All signups track automatically under your workspace.</p>
                </div>

                <!-- Share Shortcuts with Official Icons -->
                <div class="flex flex-wrap items-center gap-2 pt-2 lg:pt-0 lg:border-l lg:border-zinc-200/80 lg:dark:border-zinc-800 lg:pl-6">
                    <button type="button" onclick="coraShareWhatsApp()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-xl transition-all cursor-pointer" title="Share on WhatsApp">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2zm0 18.16c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.32a8.21 8.21 0 0 1-1.25-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.26.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24zm4.52-6.16c-.25-.12-1.47-.72-1.69-.8-.23-.09-.39-.13-.56.12-.16.24-.63.8-.78.96-.14.16-.29.18-.54.06-.25-.12-1.05-.38-2-1.23-.74-.66-1.24-1.47-1.39-1.72-.14-.24-.01-.37.11-.49.11-.11.25-.29.37-.43.13-.14.17-.25.25-.41.08-.16.04-.3-.02-.42s-.51-1.2-.7-1.7c-.19-.48-.39-.42-.54-.43h-.46c-.16 0-.42.06-.65.31-.22.24-.86.84-.86 2.06 0 1.22.89 2.4 1.01 2.56.13.17 1.75 2.67 4.24 3.74.59.26 1.05.41 1.41.53.6.19 1.14.16 1.57.1.48-.07 1.48-.61 1.69-1.2.2-.59.2-1.09.14-1.2-.06-.11-.21-.17-.46-.29z"/>
                        </svg>
                        <span>WhatsApp</span>
                    </button>

                    <button type="button" onclick="coraShareLinkedIn()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-xl transition-all cursor-pointer" title="Share on LinkedIn">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                            <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.2V10.9H6.46M7.83 6.25c-.9 0-1.63.73-1.63 1.63s.73 1.63 1.63 1.63a1.63 1.63 0 0 0 1.63-1.63c0-.9-.73-1.63-1.63-1.63z"/>
                        </svg>
                        <span>LinkedIn</span>
                    </button>

                    <button type="button" onclick="coraShareTwitter()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-xl transition-all cursor-pointer" title="Share on X">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                        <span>X</span>
                    </button>

                    <button type="button" onclick="coraOpenQRCodeModal()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-xl transition-all cursor-pointer" title="Show QR Code">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                            <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm1 1h2v2H5V5zm9-3h8v8h-8V2zm2 2v4h4V4h-4zm1 1h2v2h-2V5zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm1 1h2v2H5v-2zm9-1h2v2h-2v-2zm4 0h2v2h-2v-2zm-4 4h2v2h-2v-2zm2-2h2v2h-2v-2zm2 2h2v2h-2v-2zm-6 2h2v2h-2v-2zm6 0h2v2h-2v-2zm-2-6h2v2h-2v-2z"/>
                        </svg>
                        <span>QR Code</span>
                    </button>
                </div>
            </div>

            <!-- Custom Slug Customizer -->
            <div class="mt-4 pt-3.5 border-t border-zinc-100 dark:border-zinc-800/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-zinc-400 dark:text-zinc-500">Custom Affiliate Identifier:</span>
                    <span id="cora-display-slug" class="font-mono font-bold text-zinc-800 dark:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded"><?php echo esc_html( $custom_slug ); ?></span>
                    <button type="button" onclick="coraToggleSlugEditor()" class="text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 underline cursor-pointer">Customize</button>
                </div>

                <!-- Inline Slug Form -->
                <div id="cora-slug-editor" class="hidden flex items-center gap-2">
                    <input id="cora-custom-slug-input" type="text" placeholder="e.g. zenith-studio" value="<?php echo esc_attr( $custom_slug ); ?>" class="px-2.5 py-1 text-xs bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-400">
                    <button type="button" onclick="coraSaveCustomSlug()" class="px-3 py-1 text-xs font-semibold bg-zinc-900 hover:bg-zinc-800 text-white rounded-lg transition-all cursor-pointer">Save</button>
                    <button type="button" onclick="coraToggleSlugEditor()" class="px-3 py-1 text-xs font-medium text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 cursor-pointer">Cancel</button>
                </div>
            </div>
        </div>

        <!-- 3. FOUR KEY PERFORMANCE CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- CARD 1: Total Commission -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl p-4.5 shadow-xs">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400 mb-2">
                    <span class="text-xs font-medium">Total Cash Earned</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">40% Rate</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 tracking-tight">₹<?php echo number_format( $total_comm, 2 ); ?></span>
                </div>
                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-1.5 flex items-center gap-1">
                    <span class="text-emerald-600 dark:text-emerald-400 font-medium"><?php echo (int) $paid_signups; ?> Paid</span> conversions logged
                </p>
            </div>

            <!-- CARD 2: Available Withdrawal Balance -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl p-4.5 shadow-xs">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400 mb-2">
                    <span class="text-xs font-medium">Available for Payout</span>
                    <button type="button" onclick="coraOpenAffiliatePayoutDrawer()" class="text-[10px] font-semibold text-zinc-900 dark:text-zinc-100 hover:underline cursor-pointer">Withdraw &rarr;</button>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 tracking-tight">₹<?php echo number_format( $avail_bal, 2 ); ?></span>
                </div>
                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-1.5">Direct deposit via UPI or Bank IMPS</p>
            </div>

            <!-- CARD 3: AI Credits Earned -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl p-4.5 shadow-xs">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400 mb-2">
                    <span class="text-xs font-medium">AI Credits Granted</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">+100/Signup</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 tracking-tight"><?php echo number_format( $ai_credits ); ?></span>
                    <span class="text-xs font-medium text-zinc-400">Credits</span>
                </div>
                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-1.5 flex items-center gap-1">
                    <span class="text-zinc-800 dark:text-zinc-200 font-medium"><?php echo (int) $free_signups; ?> Free</span> signups credited
                </p>
            </div>

            <!-- CARD 4: Traffic & Conversion Rate -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl p-4.5 shadow-xs">
                <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400 mb-2">
                    <span class="text-xs font-medium">Funnel Traffic & Rate</span>
                    <span class="text-[10px] font-mono text-zinc-400"><?php echo (int) $clicks; ?> Clicks</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 tracking-tight"><?php echo number_format( $conv_rate, 1 ); ?>%</span>
                    <span class="text-xs font-medium text-zinc-400">Conv Rate</span>
                </div>
                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-1.5">
                    <?php echo (int) $affiliate_data['total_conversions']; ?> total referred accounts
                </p>
            </div>

        </div>

        <!-- 4. INTERACTIVE EARNINGS SIMULATOR & ANNUAL PRICING PLANS -->
        <div class="bg-zinc-900 text-white rounded-2xl p-6 shadow-sm border border-zinc-800 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold tracking-tight">Annual Referral Earnings Calculator</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Annual Plans Only</span>
                    </div>
                    <p class="text-xs text-zinc-400 mt-0.5">Calculate your annual cash flow and AI credits by referring clients to Cora on annual commitments (includes 2 months free).</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-zinc-400">Target Plan:</span>
                    <select id="cora-sim-tier" onchange="coraRecalculateSimulator()" class="px-2.5 py-1.5 text-xs bg-zinc-800 text-zinc-100 border border-zinc-700 rounded-lg focus:outline-none cursor-pointer font-medium">
                        <?php if ( $is_india_geo ) : ?>
                            <option value="19990" data-curr="₹" data-annual="19990" data-monthly="1665" selected>Professional Annual (₹1,665/mo • ₹19,990/yr)</option>
                            <option value="9990" data-curr="₹" data-annual="9990" data-monthly="833">Starter Annual (₹833/mo • ₹9,990/yr)</option>
                            <option value="29990" data-curr="₹" data-annual="29990" data-monthly="2499">Scale Annual (₹2,499/mo • ₹29,990/yr)</option>
                            <option value="5988" data-curr="₹" data-annual="5988" data-monthly="499">India Only Plan (₹499/mo • ₹5,988/yr)</option>
                        <?php else : ?>
                            <option value="190" data-curr="$" data-annual="190" data-monthly="15.83" selected>Professional Global ($15.83/mo • $190/yr)</option>
                            <option value="90" data-curr="$" data-annual="90" data-monthly="7.50">Starter Global ($7.50/mo • $90/yr)</option>
                            <option value="290" data-curr="$" data-annual="290" data-monthly="24.16">Scale Global ($24.16/mo • $290/yr)</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
                <!-- Slider Control -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center justify-between">
                        <label for="cora-sim-range" class="text-xs font-semibold text-zinc-300">Referred Annual Clients / Agencies</label>
                        <span id="cora-sim-clients-badge" class="px-3 py-1 bg-zinc-800 border border-zinc-700 rounded-lg text-xs font-mono font-bold text-zinc-100">10 Agencies</span>
                    </div>
                    <input id="cora-sim-range" type="range" min="1" max="50" value="10" step="1" oninput="coraRecalculateSimulator()" class="w-full h-2 bg-zinc-800 rounded-lg appearance-none cursor-pointer accent-white">
                    <div class="flex justify-between text-[10px] text-zinc-500 font-mono">
                        <span>1 Client</span>
                        <span>10 Clients</span>
                        <span>25 Clients</span>
                        <span>50 Clients</span>
                    </div>
                </div>

                <!-- Estimated Return Card -->
                <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-xl p-4.5 flex flex-col justify-center text-center lg:text-left">
                    <span class="text-[11px] uppercase tracking-wider text-zinc-400 font-medium">Annual Commission Payout (40%)</span>
                    <span id="cora-sim-monthly-cash" class="text-3xl font-extrabold text-white tracking-tight mt-1"><?php echo $is_india_geo ? '₹79,960' : '$760'; ?></span>
                    <span id="cora-sim-yearly-cash" class="text-[11px] text-zinc-400 mt-1"><?php echo $is_india_geo ? '₹6,663/mo equivalent payout + 1,000 AI credits' : '$63.33/mo equivalent payout + 1,000 AI credits'; ?></span>
                </div>
            </div>

            <!-- Official Plan Pricing & Commission Matrix (Annual Plans Only) -->
            <div class="pt-4 border-t border-zinc-800/80 grid grid-cols-1 sm:grid-cols-2 <?php echo $is_india_geo ? 'lg:grid-cols-4' : 'lg:grid-cols-3'; ?> gap-3 text-xs">
                <?php if ( $is_india_geo ) : ?>
                    <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white text-[11px]">India Only Plan</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Annual Only</span>
                        </div>
                        <div class="text-zinc-200 font-mono font-semibold">₹499<span class="text-[10px] text-zinc-400 font-normal">/mo (₹5,988 billed annually)</span></div>
                        <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">₹2,395.20</span>/client/yr</div>
                    </div>

                    <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white text-[11px]">Starter Tier</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-zinc-800 text-zinc-300">2 Mo. Free</span>
                        </div>
                        <div class="text-zinc-200 font-mono font-semibold">₹833<span class="text-[10px] text-zinc-400 font-normal">/mo (₹9,990 billed annually)</span></div>
                        <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">₹3,996.00</span>/client/yr</div>
                    </div>

                    <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white text-[11px]">Professional Tier</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">Recommended</span>
                        </div>
                        <div class="text-zinc-200 font-mono font-semibold">₹1,665<span class="text-[10px] text-zinc-400 font-normal">/mo (₹19,990 billed annually)</span></div>
                        <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">₹7,996.00</span>/client/yr</div>
                    </div>

                    <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white text-[11px]">Scale Tier</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">High Scale</span>
                        </div>
                        <div class="text-zinc-200 font-mono font-semibold">₹2,499<span class="text-[10px] text-zinc-400 font-normal">/mo (₹29,990 billed annually)</span></div>
                        <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">₹11,996.00</span>/client/yr</div>
                    </div>
                <?php else : ?>
                    <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white text-[11px]">Starter Global</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-zinc-800 text-zinc-300">2 Mo. Free</span>
                        </div>
                        <div class="text-zinc-200 font-mono font-semibold">$7.50<span class="text-[10px] text-zinc-400 font-normal">/mo ($90 billed annually)</span></div>
                        <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">$36.00</span>/client/yr</div>
                    </div>

                    <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white text-[11px]">Professional Global</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">Recommended</span>
                        </div>
                        <div class="text-zinc-200 font-mono font-semibold">$15.83<span class="text-[10px] text-zinc-400 font-normal">/mo ($190 billed annually)</span></div>
                        <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">$76.00</span>/client/yr</div>
                    </div>

                    <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white text-[11px]">Scale Global</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">High Scale</span>
                        </div>
                        <div class="text-zinc-200 font-mono font-semibold">$24.16<span class="text-[10px] text-zinc-400 font-normal">/mo ($290 billed annually)</span></div>
                        <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">$116.00</span>/client/yr</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 5. REFERRAL ACTIVITY & CONVERSION AUDIT LEDGER -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl shadow-xs overflow-hidden">
            
            <!-- Table Header & Filter Tabs -->
            <div class="p-4.5 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Conversion Activity Ledger</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Real-time log of all referred signups, conversions, and commission earnings.</p>
                </div>

                <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-xl">
                    <button type="button" onclick="coraFilterReferralTable('all')" id="cora-filter-all" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs transition-all cursor-pointer">All</button>
                    <button type="button" onclick="coraFilterReferralTable('paid')" id="cora-filter-paid" class="px-2.5 py-1 text-xs font-medium rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer">Paid (40%)</button>
                    <button type="button" onclick="coraFilterReferralTable('free')" id="cora-filter-free" class="px-2.5 py-1 text-xs font-medium rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer">Free (100 Credits)</button>
                </div>
            </div>

            <!-- Table View -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50/80 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <tr>
                            <th class="py-3 px-4">Referred Client / Agency</th>
                            <th class="py-3 px-4">Joined Date</th>
                            <th class="py-3 px-4">Conversion Tier</th>
                            <th class="py-3 px-4 text-right">Plan Value</th>
                            <th class="py-3 px-4 text-right">Reward Earned</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="cora-referral-table-body" class="divide-y divide-zinc-200/70 dark:divide-zinc-800/70 text-zinc-800 dark:text-zinc-200">
                        <?php if ( ! empty( $referrals ) ) : ?>
                            <?php foreach ( $referrals as $ref ) : 
                                $is_paid = $ref['conversion_type'] === 'paid_conversion';
                                $row_class = $is_paid ? 'cora-row-paid' : 'cora-row-free';
                                $time_ago = human_time_diff( strtotime( $ref['created_at'] ), current_time( 'timestamp' ) ) . ' ago';
                            ?>
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition-colors <?php echo esc_attr( $row_class ); ?>">
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $ref['referred_name'] ); ?></div>
                                    <div class="text-[11px] text-zinc-400 font-mono"><?php echo esc_html( $ref['referred_email'] ); ?></div>
                                </td>
                                <td class="py-3 px-4 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">
                                    <?php echo esc_html( $time_ago ); ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if ( $is_paid ) : ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">
                                            <?php echo esc_html( $ref['plan_name'] ); ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                            Free Signup
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-right font-mono">
                                    <?php echo $is_paid ? '₹' . number_format( (float) $ref['converted_value'], 2 ) : '₹0.00'; ?>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold <?php echo $is_paid ? 'text-zinc-950 dark:text-zinc-50' : 'text-emerald-600 dark:text-emerald-400'; ?>">
                                    <?php if ( $is_paid ) : ?>
                                        ₹<?php echo number_format( (float) $ref['commission_earned'], 2 ); ?> <span class="text-[10px] font-normal text-zinc-400">(40%)</span>
                                    <?php else : ?>
                                        +100 AI Credits
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        Confirmed
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-zinc-400 text-xs">
                                    No referral conversions recorded yet. Share your referral link above to start earning!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- 6. PAYOUT HISTORY LEDGER -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-4.5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">Payout & Withdrawal History</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Records of all processed bank transfers and UPI disbursements.</p>
                </div>
                <button type="button" onclick="coraOpenAffiliatePayoutDrawer()" class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 hover:underline cursor-pointer">
                    + Request Withdrawal
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50/80 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-semibold">
                        <tr>
                            <th class="py-3 px-4">Transaction ID</th>
                            <th class="py-3 px-4">Requested Date</th>
                            <th class="py-3 px-4">Payout Method</th>
                            <th class="py-3 px-4 text-right">Amount (₹)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/70 dark:divide-zinc-800/70 text-zinc-800 dark:text-zinc-200">
                        <?php if ( ! empty( $payouts ) ) : ?>
                            <?php foreach ( $payouts as $p ) : 
                                $status_badge = $p['status'] === 'completed' 
                                    ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' 
                                    : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20';
                            ?>
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition-colors">
                                <td class="py-3 px-4 font-mono font-medium text-zinc-900 dark:text-zinc-100">
                                    <?php echo esc_html( $p['transaction_ref'] ); ?>
                                </td>
                                <td class="py-3 px-4 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">
                                    <?php echo esc_html( gmdate( 'd M Y, h:i A', strtotime( $p['created_at'] ) ) ); ?>
                                </td>
                                <td class="py-3 px-4 uppercase text-[11px] font-semibold text-zinc-600 dark:text-zinc-300">
                                    <?php echo esc_html( $p['payout_method'] ); ?>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-zinc-900 dark:text-zinc-50">
                                    ₹<?php echo number_format( (float) $p['amount'], 2 ); ?>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border <?php echo esc_attr( $status_badge ); ?>">
                                        <?php echo esc_html( ucfirst( $p['status'] ) ); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="py-6 text-center text-zinc-400 text-xs">
                                    No withdrawal transactions requested yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>

<!-- 7. SLIDING WITHDRAWAL PAYOUT DRAWER -->
<div id="cora-affiliate-payout-drawer" class="fixed inset-0 z-50 pointer-events-none opacity-0 transition-opacity duration-300">
    <!-- Backdrop Overlay -->
    <div onclick="coraCloseAffiliatePayoutDrawer()" class="absolute inset-0 bg-zinc-950/45 backdrop-blur-xs cursor-pointer"></div>

    <!-- Drawer Panel (Right sliding on desktop, Bottom sliding on mobile) -->
    <div class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 flex flex-col justify-between transform translate-x-full transition-transform duration-300 ease-out pointer-events-auto">
        
        <div>
            <!-- Drawer Header -->
            <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Request Payout Withdrawal</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Transfer your earned referral commission.</p>
                </div>
                <button type="button" onclick="coraCloseAffiliatePayoutDrawer()" class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 cursor-pointer">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Balance Summary -->
            <div class="mt-5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4">
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Available Balance:</span>
                <div class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-50 mt-0.5">₹<?php echo number_format( $avail_bal, 2 ); ?></div>
                <span class="text-[11px] text-zinc-400">Minimum withdrawal amount: ₹1,000</span>
            </div>

            <!-- Form -->
            <form id="cora-payout-form" onsubmit="coraSubmitPayoutRequest(event)" class="mt-5 space-y-4">
                
                <!-- Amount Input -->
                <div class="space-y-1.5">
                    <label for="cora-payout-amount" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Withdrawal Amount (₹)</label>
                    <input id="cora-payout-amount" type="number" min="1000" max="<?php echo esc_attr( $avail_bal ); ?>" step="100" value="<?php echo esc_attr( min( 5000, $avail_bal ) ); ?>" class="w-full px-3.5 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-zinc-400">
                    <div class="flex items-center gap-2 pt-1">
                        <button type="button" onclick="document.getElementById('cora-payout-amount').value = 1000" class="px-2 py-0.5 text-[10px] bg-zinc-100 dark:bg-zinc-800 rounded text-zinc-600 dark:text-zinc-400">₹1,000</button>
                        <button type="button" onclick="document.getElementById('cora-payout-amount').value = 5000" class="px-2 py-0.5 text-[10px] bg-zinc-100 dark:bg-zinc-800 rounded text-zinc-600 dark:text-zinc-400">₹5,000</button>
                        <button type="button" onclick="document.getElementById('cora-payout-amount').value = <?php echo esc_attr( $avail_bal ); ?>" class="px-2 py-0.5 text-[10px] bg-zinc-100 dark:bg-zinc-800 rounded text-zinc-600 dark:text-zinc-400">Max Balance</button>
                    </div>
                </div>

                <!-- Payout Method Selection -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Payout Method</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer">
                            <input type="radio" name="payout_method" value="upi" checked onchange="coraTogglePayoutFields('upi')" class="accent-zinc-900 dark:accent-zinc-100">
                            <span class="text-xs font-medium text-zinc-800 dark:text-zinc-200">UPI ID</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl cursor-pointer">
                            <input type="radio" name="payout_method" value="bank_transfer" onchange="coraTogglePayoutFields('bank_transfer')" class="accent-zinc-900 dark:accent-zinc-100">
                            <span class="text-xs font-medium text-zinc-800 dark:text-zinc-200">Bank Transfer</span>
                        </label>
                    </div>
                </div>

                <!-- UPI Fields -->
                <div id="cora-payout-field-upi" class="space-y-1.5">
                    <label for="cora-upi-id" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">VPA / UPI ID</label>
                    <input id="cora-upi-id" type="text" placeholder="username@okaxis or mobile@upi" class="w-full px-3.5 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-zinc-400">
                </div>

                <!-- Bank Fields -->
                <div id="cora-payout-field-bank" class="hidden space-y-3">
                    <div class="space-y-1">
                        <label for="cora-bank-beneficiary" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Beneficiary Account Name</label>
                        <input id="cora-bank-beneficiary" type="text" placeholder="Account Holder Name" class="w-full px-3.5 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-zinc-400">
                    </div>
                    <div class="space-y-1">
                        <label for="cora-bank-account" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Account Number</label>
                        <input id="cora-bank-account" type="password" placeholder="Account Number" class="w-full px-3.5 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-1 focus:ring-zinc-400">
                    </div>
                    <div class="space-y-1">
                        <label for="cora-bank-ifsc" class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">IFSC Code</label>
                        <input id="cora-bank-ifsc" type="text" placeholder="e.g. HDFC0001234" class="w-full px-3.5 py-2 text-xs bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-100 uppercase focus:outline-none focus:ring-1 focus:ring-zinc-400">
                    </div>
                </div>

            </form>
        </div>

        <!-- Drawer Footer -->
        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end gap-2.5">
            <button type="button" onclick="coraCloseAffiliatePayoutDrawer()" class="px-4 py-2 text-xs font-semibold text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 cursor-pointer">Cancel</button>
            <button type="button" onclick="document.getElementById('cora-payout-form').requestSubmit()" class="px-5 py-2 text-xs font-semibold text-white bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-100 dark:hover:bg-zinc-200 dark:text-zinc-950 rounded-xl shadow-sm transition-all cursor-pointer">Confirm Withdrawal</button>
        </div>

    </div>
</div>

<!-- 8. QR CODE PREVIEW MODAL -->
<div id="cora-affiliate-qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div onclick="coraCloseQRCodeModal()" class="absolute inset-0 bg-zinc-950/45 backdrop-blur-xs cursor-pointer"></div>
    <div class="relative w-full max-w-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-2xl z-10 text-center space-y-4">
        <div>
            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">Scan Referral QR Code</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Let clients scan this code to sign up directly under your agency.</p>
        </div>

        <!-- Generated QR Vector -->
        <div class="bg-zinc-50 dark:bg-zinc-950 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 flex items-center justify-center">
            <div id="cora-qr-canvas-holder" class="p-2 bg-white rounded-xl shadow-xs">
                <img id="cora-qr-img" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?php echo urlencode( $ref_url ); ?>&color=18-18-1b" alt="QR Code" width="180" height="180" class="rounded-lg">
            </div>
        </div>

        <div class="pt-2 flex items-center justify-center gap-2">
            <button type="button" onclick="coraCloseQRCodeModal()" class="px-5 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all cursor-pointer">Close</button>
            <button type="button" onclick="coraCopyReferralLink(); coraCloseQRCodeModal();" class="px-5 py-2 text-xs font-semibold text-white bg-zinc-950 dark:bg-zinc-100 dark:text-zinc-950 rounded-xl hover:opacity-90 transition-all cursor-pointer">Copy Link</button>
        </div>
    </div>
</div>

<!-- JAVASCRIPT LOGIC -->
<script>
window.coraAffiliateState = {
    referralUrl: <?php echo json_encode( $ref_url ); ?>,
    refCode: <?php echo json_encode( $ref_code ); ?>,
    customSlug: <?php echo json_encode( $custom_slug ); ?>,
    availBalance: <?php echo json_encode( (float) $avail_bal ); ?>,
    isEnrolled: <?php echo json_encode( (bool) $is_enrolled ); ?>
};

/* --- 3-STEP SCREENER CONTROLLER --- */
function coraScreenerGoToStep(stepNum) {
    if (stepNum === 2) {
        // Validate or proceed to step 2
    } else if (stepNum === 3) {
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
                tab.className = 'flex items-center gap-2 p-2.5 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 transition-all';
            } else if (s < stepNum) {
                tab.className = 'flex items-center gap-2 p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 transition-all';
            } else {
                tab.className = 'flex items-center gap-2 p-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-all';
            }
        }
    });

    // Scroll smoothly to top of screener
    var root = document.getElementById('cora-affiliate-screener-view');
    if (root) root.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function coraRecalculateScreenerSimulator() {
    var range = document.getElementById('cora-screener-sim-range');
    var tier = document.getElementById('cora-screener-sim-tier');
    var badge = document.getElementById('cora-screener-sim-clients-badge');
    var monthlyCash = document.getElementById('cora-screener-sim-monthly-cash');
    var yearlyCash = document.getElementById('cora-screener-sim-yearly-cash');

    if (!range || !tier) return;
    var count = parseInt(range.value, 10);
    var annualPlanPrice = parseFloat(tier.value);
    var selectedOpt = tier.options[tier.selectedIndex];
    var curr = selectedOpt ? (selectedOpt.getAttribute('data-curr') || '₹') : '₹';
    var rate = 0.40;

    var annualComm = Math.round(count * annualPlanPrice * rate);
    var monthlyEquiv = Math.round(annualComm / 12);
    var credits = count * 100;

    if (badge) badge.innerText = count + (count === 1 ? ' Client / Agency' : ' Clients / Agencies');
    if (monthlyCash) {
        if (curr === '₹') {
            monthlyCash.innerText = '₹' + annualComm.toLocaleString('en-IN');
        } else {
            monthlyCash.innerText = '$' + annualComm.toLocaleString('en-US');
        }
    }
    if (yearlyCash) {
        var eqStr = curr === '₹' ? '₹' + monthlyEquiv.toLocaleString('en-IN') : '$' + monthlyEquiv.toLocaleString('en-US');
        yearlyCash.innerText = eqStr + '/mo equivalent + ' + credits.toLocaleString('en-IN') + ' AI credits';
    }
}

function coraSubmitAffiliateEnrollment() {
    var agree = document.getElementById('cora-enroll-agree');
    if (!agree || !agree.checked) {
        if (window.coraShowToast) window.coraShowToast('Please accept the Partner Agreement & Compliance terms to continue.', 'error');
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

    var channels = [];
    document.querySelectorAll('input[name="cora_promo_channel"]:checked').forEach(function(cb){
        channels.push(cb.value);
    });

    var formData = new FormData();
    formData.append('action', 'cora_affiliate_enroll');
    formData.append('custom_slug', slug);
    formData.append('upi_id', upi);
    formData.append('account_number', bank);
    channels.forEach(function(c){ formData.append('promotion_channels[]', c); });

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

            // Smooth transition from screener to dashboard
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
                submitBtn.innerHTML = '<span>Complete Enrollment & Launch Dashboard</span>';
            }
            if (window.coraShowToast) {
                window.coraShowToast(res.data.message || 'Error completing enrollment', 'error');
            }
        }
    })
    .catch(function(){
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>Complete Enrollment & Launch Dashboard</span>';
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
                window.coraShowToast('Custom referral code updated!', 'success');
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

function coraRecalculateSimulator() {
    var range = document.getElementById('cora-sim-range');
    var tier = document.getElementById('cora-sim-tier');
    var badge = document.getElementById('cora-sim-clients-badge');
    var monthlyCash = document.getElementById('cora-sim-monthly-cash');
    var yearlyCash = document.getElementById('cora-sim-yearly-cash');

    if (!range || !tier) return;
    var count = parseInt(range.value, 10);
    var annualPlanPrice = parseFloat(tier.value);
    var selectedOpt = tier.options[tier.selectedIndex];
    var curr = selectedOpt ? (selectedOpt.getAttribute('data-curr') || '₹') : '₹';
    var rate = 0.40;

    var annualComm = Math.round(count * annualPlanPrice * rate);
    var monthlyEquiv = Math.round(annualComm / 12);
    var credits = count * 100;

    if (badge) badge.innerText = count + (count === 1 ? ' Client / Agency' : ' Clients / Agencies');
    if (monthlyCash) {
        if (curr === '₹') {
            monthlyCash.innerText = '₹' + annualComm.toLocaleString('en-IN');
        } else {
            monthlyCash.innerText = '$' + annualComm.toLocaleString('en-US');
        }
    }
    if (yearlyCash) {
        var eqStr = curr === '₹' ? '₹' + monthlyEquiv.toLocaleString('en-IN') : '$' + monthlyEquiv.toLocaleString('en-US');
        yearlyCash.innerText = eqStr + '/mo equivalent payout + ' + credits.toLocaleString('en-IN') + ' AI credits';
    }
}

function coraFilterReferralTable(filter) {
    var rows = document.querySelectorAll('#cora-referral-table-body tr');
    var btnAll = document.getElementById('cora-filter-all');
    var btnPaid = document.getElementById('cora-filter-paid');
    var btnFree = document.getElementById('cora-filter-free');

    // Reset styles
    [btnAll, btnPaid, btnFree].forEach(function(btn){
        if (btn) {
            btn.className = 'px-2.5 py-1 text-xs font-medium rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer';
        }
    });

    var activeBtn = filter === 'paid' ? btnPaid : (filter === 'free' ? btnFree : btnAll);
    if (activeBtn) {
        activeBtn.className = 'px-2.5 py-1 text-xs font-semibold rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs transition-all cursor-pointer';
    }

    rows.forEach(function(row){
        if (filter === 'all') {
            row.style.display = '';
        } else if (filter === 'paid') {
            row.style.display = row.classList.contains('cora-row-paid') ? '' : 'none';
        } else if (filter === 'free') {
            row.style.display = row.classList.contains('cora-row-free') ? '' : 'none';
        }
    });
}

function coraOpenAffiliatePayoutDrawer() {
    var drawer = document.getElementById('cora-affiliate-payout-drawer');
    if (drawer) {
        drawer.classList.remove('pointer-events-none', 'opacity-0');
        drawer.classList.add('opacity-100');
        var panel = drawer.querySelector('div.absolute.right-0');
        if (panel) {
            panel.classList.remove('translate-x-full');
            panel.classList.add('translate-x-0');
        }
    }
}

function coraCloseAffiliatePayoutDrawer() {
    var drawer = document.getElementById('cora-affiliate-payout-drawer');
    if (drawer) {
        var panel = drawer.querySelector('div.absolute.right-0');
        if (panel) {
            panel.classList.add('translate-x-full');
            panel.classList.remove('translate-x-0');
        }
        setTimeout(function(){
            drawer.classList.add('pointer-events-none', 'opacity-0');
            drawer.classList.remove('opacity-100');
        }, 200);
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
                window.coraShowToast(res.data.message, 'success');
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
    var text = encodeURIComponent("Hey! Join Cora, the ultimate AI operating system for agencies and creative studios. Sign up with my referral link to get 100 bonus AI credits: " + decodeURIComponent(url));
    window.open("https://api.whatsapp.com/send?text=" + text, '_blank');
}

function coraShareLinkedIn() {
    var url = encodeURIComponent(window.coraAffiliateState.referralUrl);
    window.open("https://www.linkedin.com/sharing/share-offsite/?url=" + url, '_blank');
}

function coraShareTwitter() {
    var url = encodeURIComponent(window.coraAffiliateState.referralUrl);
    var text = encodeURIComponent("Check out Cora - the AI workspace for agencies and studios. Join via my link for bonus AI credits:");
    window.open("https://twitter.com/intent/tweet?text=" + text + "&url=" + url, '_blank');
}
</script>
