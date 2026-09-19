<?php
/**
 * Cora Affiliate & Referral Workspace View
 * 
 * Provides agency referral management, unique link sharing, impression metrics,
 * dual rewards (500 AI Credits for free signups, 40% monetary commission for paid signups),
 * real-time conversion ledger, revenue simulator, and sliding withdrawal drawer.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
$affiliate_data = class_exists( 'Cora_Affiliate_Referral_Engine' ) 
    ? Cora_Affiliate_Referral_Engine::get_dashboard_data( $user_id ) 
    : array(
        'ref_code'               => 'CR-DEMO',
        'referral_url'           => home_url( '/?ref=CR-DEMO' ),
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
$total_comm    = $affiliate_data['total_commission'];
$avail_bal     = $affiliate_data['available_balance'];
$ai_credits    = $affiliate_data['total_ai_credits'];
$clicks        = $affiliate_data['clicks_count'];
$conv_rate     = $affiliate_data['conversion_rate'];
$referrals     = $affiliate_data['referrals'];
$payouts       = $affiliate_data['payouts'];
$free_signups  = $affiliate_data['free_signups_count'];
$paid_signups  = $affiliate_data['paid_conversions_count'];
?>

<div id="cora-affiliate-root" class="space-y-6">

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
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">Foundation</span>
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

            <!-- Share Shortcuts -->
            <div class="flex flex-wrap items-center gap-2 pt-2 lg:pt-0 lg:border-l lg:border-zinc-200/80 lg:dark:border-zinc-800 lg:pl-6">
                <button type="button" onclick="coraShareWhatsApp()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-xl transition-all cursor-pointer" title="Share on WhatsApp">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                    <span>WhatsApp</span>
                </button>

                <button type="button" onclick="coraShareLinkedIn()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-xl transition-all cursor-pointer" title="Share on LinkedIn">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                        <rect x="2" y="9" width="4" height="12"></rect>
                        <circle cx="4" cy="4" r="2"></circle>
                    </svg>
                    <span>LinkedIn</span>
                </button>

                <button type="button" onclick="coraShareTwitter()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-xl transition-all cursor-pointer" title="Share on X">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                    </svg>
                    <span>Post</span>
                </button>

                <button type="button" onclick="coraOpenQRCodeModal()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 rounded-xl transition-all cursor-pointer" title="Show QR Code">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>QR Code</span>
                </button>
            </div>
        </div>

        <!-- Custom Slug Customizer -->
        <div class="mt-4 pt-3.5 border-t border-zinc-100 dark:border-zinc-800/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-zinc-400 dark:text-zinc-500">Custom Affiliate Identifier:</span>
                <span id="cora-display-slug" class="font-mono font-bold text-zinc-800 dark:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded"><?php echo esc_html( ( isset( $affiliate_data['custom_slug'] ) && $affiliate_data['custom_slug'] ) ? $affiliate_data['custom_slug'] : $ref_code ); ?></span>
                <button type="button" onclick="coraToggleSlugEditor()" class="text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 underline cursor-pointer">Customize</button>
            </div>

            <!-- Inline Slug Form -->
            <div id="cora-slug-editor" class="hidden flex items-center gap-2">
                <input id="cora-custom-slug-input" type="text" placeholder="e.g. zenith-studio" value="<?php echo esc_attr( $affiliate_data['custom_slug'] ?? '' ); ?>" class="px-2.5 py-1 text-xs bg-white dark:bg-zinc-950 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-400">
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

    <!-- 4. INTERACTIVE EARNINGS SIMULATOR & PRICING PLANS -->
    <div class="bg-zinc-900 text-white rounded-2xl p-6 shadow-sm border border-zinc-800 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold tracking-tight">Referral Earnings Calculator</h3>
                <p class="text-xs text-zinc-400 mt-0.5">Calculate your recurring monthly cash flow by bringing agencies and clients to Cora.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-zinc-400">Target Plan:</span>
                <select id="cora-sim-tier" onchange="coraRecalculateSimulator()" class="px-2.5 py-1.5 text-xs bg-zinc-800 text-zinc-100 border border-zinc-700 rounded-lg focus:outline-none cursor-pointer font-medium">
                    <optgroup label="India Localized Plans">
                        <option value="1999" data-curr="₹" selected>Professional Monthly (₹1,999/mo)</option>
                        <option value="1665" data-curr="₹">Professional Annual (₹1,665/mo • ₹19,990/yr)</option>
                        <option value="999" data-curr="₹">Starter Monthly (₹999/mo)</option>
                        <option value="833" data-curr="₹">Starter Annual (₹833/mo • ₹9,990/yr)</option>
                        <option value="2999" data-curr="₹">Scale Monthly (₹2,999/mo)</option>
                        <option value="2499" data-curr="₹">Scale Annual (₹2,499/mo • ₹29,990/yr)</option>
                        <option value="499" data-curr="₹">India Only Plan (₹499/mo • ₹5,988/yr Annual)</option>
                    </optgroup>
                    <optgroup label="Global USD Plans">
                        <option value="19" data-curr="$">Professional Global ($19/mo)</option>
                        <option value="9" data-curr="$">Starter Global ($9/mo)</option>
                        <option value="29" data-curr="$">Scale Global ($29/mo)</option>
                    </optgroup>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
            <!-- Slider Control -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <label for="cora-sim-range" class="text-xs font-semibold text-zinc-300">Referred Paid Clients / Agencies</label>
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

            <!-- Estimated Monthly Return -->
            <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-xl p-4.5 flex flex-col justify-center text-center lg:text-left">
                <span class="text-[11px] uppercase tracking-wider text-zinc-400 font-medium">Estimated Monthly Revenue (40%)</span>
                <span id="cora-sim-monthly-cash" class="text-3xl font-extrabold text-white tracking-tight mt-1">₹7,996</span>
                <span id="cora-sim-yearly-cash" class="text-[11px] text-zinc-400 mt-1">₹95,952 / year recurring + 1,000 AI credits</span>
            </div>
        </div>

        <!-- Official Plan Pricing & Commission Matrix -->
        <div class="pt-4 border-t border-zinc-800/80 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-white text-[11px]">India Only Plan</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Annual Only</span>
                </div>
                <div class="text-zinc-200 font-mono font-semibold">₹499<span class="text-[10px] text-zinc-400 font-normal">/mo (₹5,988/yr)</span></div>
                <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">₹2,395.20</span>/client/yr</div>
            </div>

            <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-white text-[11px]">Starter Tier</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-zinc-800 text-zinc-300">2 Mo. Free</span>
                </div>
                <div class="text-zinc-200 font-mono font-semibold">₹999<span class="text-[10px] text-zinc-400 font-normal">/mo ($9) • ₹833/mo ann</span></div>
                <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">₹399.60</span>/mo ($3.60)</div>
            </div>

            <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-white text-[11px]">Professional Tier</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">Recommended</span>
                </div>
                <div class="text-zinc-200 font-mono font-semibold">₹1,999<span class="text-[10px] text-zinc-400 font-normal">/mo ($19) • ₹1,665/mo ann</span></div>
                <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">₹799.60</span>/mo ($7.60)</div>
            </div>

            <div class="p-3 bg-zinc-950/60 rounded-xl border border-zinc-800/60 flex flex-col gap-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-white text-[11px]">Scale Tier</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">High Scale</span>
                </div>
                <div class="text-zinc-200 font-mono font-semibold">₹2,999<span class="text-[10px] text-zinc-400 font-normal">/mo ($29) • ₹2,499/mo ann</span></div>
                <div class="text-[10px] text-emerald-400 mt-0.5">40% Comm: <span class="font-bold font-mono">₹1,199.60</span>/mo ($11.60)</div>
            </div>
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
                <tbody class="divide-y divide-zinc-200/70 dark:divide-zinc-800/70 text-zinc-800 dark:text-zinc-200 font-mono text-[11px]">
                    <?php if ( ! empty( $payouts ) ) : ?>
                        <?php foreach ( $payouts as $p ) : ?>
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40">
                            <td class="py-3 px-4 font-bold text-zinc-900 dark:text-zinc-100">
                                <?php echo esc_html( $p['transaction_ref'] ?? 'TXN-' . $p['id'] ); ?>
                            </td>
                            <td class="py-3 px-4 text-zinc-500">
                                <?php echo esc_html( gmdate( 'M j, Y', strtotime( $p['created_at'] ) ) ); ?>
                            </td>
                            <td class="py-3 px-4 uppercase text-zinc-600 dark:text-zinc-300">
                                <?php echo esc_html( $p['payout_method'] ); ?>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-zinc-900 dark:text-zinc-50">
                                ₹<?php echo number_format( (float) $p['amount'], 2 ); ?>
                            </td>
                            <td class="py-3 px-4 text-center font-sans">
                                <?php if ( $p['status'] === 'completed' ) : ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">Disbursed</span>
                                <?php elseif ( $p['status'] === 'pending' ) : ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">Processing</span>
                                <?php else : ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 text-zinc-600"><?php echo esc_html( ucfirst( $p['status'] ) ); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="py-6 text-center text-zinc-400 text-xs font-sans">
                                No previous payout withdrawals on record.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
    availBalance: <?php echo json_encode( (float) $avail_bal ); ?>
};

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
    var box = document.getElementById('cora-slug-editor-box');
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
    var planPrice = parseFloat(tier.value);
    var selectedOpt = tier.options[tier.selectedIndex];
    var curr = selectedOpt ? (selectedOpt.getAttribute('data-curr') || '₹') : '₹';
    var rate = 0.40;

    var monthly = Math.round(count * planPrice * rate);
    var yearly = monthly * 12;
    var credits = count * 100;

    if (badge) badge.innerText = count + (count === 1 ? ' Client / Agency' : ' Clients / Agencies');
    if (monthlyCash) {
        if (curr === '₹') {
            monthlyCash.innerText = '₹' + monthly.toLocaleString('en-IN');
        } else {
            monthlyCash.innerText = '$' + monthly.toLocaleString('en-US');
        }
    }
    if (yearlyCash) {
        var yrStr = curr === '₹' ? '₹' + yearly.toLocaleString('en-IN') : '$' + yearly.toLocaleString('en-US');
        yearlyCash.innerText = yrStr + ' / year recurring + ' + credits.toLocaleString('en-IN') + ' AI credits';
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
