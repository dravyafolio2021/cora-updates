<?php
/**
 * Cora Workspace — Financial Intelligence & AI Co-founder (v3.4.44)
 * 
 * Rebuilt as a proactive AI Co-founder for solo founders & creative studios.
 * Includes floating bottom Ask Cora Copilot and rich, pre-populated tabs.
 *
 * @package CoraWorkspace
 * @version 3.4.44
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Fetch comprehensive financial intelligence metrics
$metrics = function_exists( 'cora_finance_get_comprehensive_metrics' ) ? cora_finance_get_comprehensive_metrics() : array();

$available_cash = $metrics['available_cash'] ?? 185000.0;
$expected_in    = $metrics['expected_in'] ?? 270000.0;
$expected_out   = $metrics['expected_out'] ?? 41249.0;
$projected_cash = $metrics['projected_cash'] ?? 413751.0;
$overdue_total  = $metrics['overdue_total'] ?? 145000.0;
$overdue_count  = $metrics['overdue_count'] ?? 2;
$monthly_rec    = $metrics['monthly_recurring_total'] ?? 41249.0;
$gross_inflow   = $metrics['gross_inflow'] ?? 270000.0;
$gross_outflow  = $metrics['gross_outflow'] ?? 85000.0;
$receivables    = $metrics['receivables'] ?? array();
$recurring_list = $metrics['recurring_expenses'] ?? array();
$client_profits = $metrics['client_profitability'] ?? array();
$attention_cards= $metrics['attention_cards'] ?? array();
$cora_take      = $metrics['cora_take'] ?? array();

// Fallback robust pre-population if empty
if ( empty( $receivables ) ) {
    $receivables = array(
        array(
            'id'             => 'inv_sample_01',
            'invoice_number' => 'INV-' . date('Y') . '-0842',
            'client_name'    => 'Acme Studios & Media',
            'client_email'   => 'finance@acmestudios.in',
            'package_name'   => 'Brand Commercial Campaign & Studio Retainer',
            'total_amount'   => 80000.0,
            'due_balance'    => 80000.0,
            'due_date'       => date( 'Y-m-d', strtotime( '-7 days' ) ),
            'days_overdue'   => 7,
            'is_overdue'     => true,
            'status'         => 'unpaid',
            'last_comm'      => date( 'Y-m-d', strtotime( '-10 days' ) ),
        ),
        array(
            'id'             => 'inv_sample_02',
            'invoice_number' => 'INV-' . date('Y') . '-0849',
            'client_name'    => 'Urban Space Developers',
            'client_email'   => 'accounts@urbanspace.co.in',
            'package_name'   => 'Luxury Penthouse 3D Render & Video Walkthrough',
            'total_amount'   => 45000.0,
            'due_balance'    => 0.0,
            'due_date'       => date( 'Y-m-d', strtotime( '-2 days' ) ),
            'days_overdue'   => 0,
            'is_overdue'     => false,
            'status'         => 'paid',
            'last_comm'      => date( 'Y-m-d', strtotime( '-1 days' ) ),
        ),
        array(
            'id'             => 'inv_sample_03',
            'invoice_number' => 'INV-' . date('Y') . '-0855',
            'client_name'    => 'Horizon Heights Luxury Living',
            'client_email'   => 'sales@horizonheights.in',
            'package_name'   => 'Quarterly Architectural Shoot & Media Licensing',
            'total_amount'   => 125000.0,
            'due_balance'    => 125000.0,
            'due_date'       => date( 'Y-m-d', strtotime( '+5 days' ) ),
            'days_overdue'   => 0,
            'is_overdue'     => false,
            'status'         => 'unpaid',
            'last_comm'      => date( 'Y-m-d', strtotime( '-3 days' ) ),
        ),
        array(
            'id'             => 'inv_sample_04',
            'invoice_number' => 'INV-' . date('Y') . '-0861',
            'client_name'    => 'Rajiv & Priya Wedding Productions',
            'client_email'   => 'rajiv.priya.events@gmail.com',
            'package_name'   => '3-Day Destination Wedding Coverage (Milestone 2)',
            'total_amount'   => 65000.0,
            'due_balance'    => 65000.0,
            'due_date'       => date( 'Y-m-d', strtotime( '-14 days' ) ),
            'days_overdue'   => 14,
            'is_overdue'     => true,
            'status'         => 'unpaid',
            'last_comm'      => date( 'Y-m-d', strtotime( '-12 days' ) ),
        ),
    );
}

if ( empty( $recurring_list ) ) {
    $recurring_list = array(
        array( 'name' => 'Adobe Creative Cloud (All Apps)', 'vendor' => 'Adobe Systems', 'amount' => 5499.0, 'frequency' => 'monthly', 'category' => 'Software & Tools', 'next_due' => date('Y-m-d', strtotime('+12 days')) ),
        array( 'name' => 'Main Studio Lease & Power', 'vendor' => 'DLF Commercial Properties', 'amount' => 28500.0, 'frequency' => 'monthly', 'category' => 'Rent & Facilities', 'next_due' => date('Y-m-d', strtotime('+5 days')) ),
        array( 'name' => 'Google Workspace (5 Business Seats)', 'vendor' => 'Google LLC', 'amount' => 1650.0, 'frequency' => 'monthly', 'category' => 'Software & Tools', 'next_due' => date('Y-m-d', strtotime('+18 days')) ),
        array( 'name' => 'Figma Organization Seats', 'vendor' => 'Figma Inc.', 'amount' => 2400.0, 'frequency' => 'monthly', 'category' => 'Software & Tools', 'next_due' => date('Y-m-d', strtotime('+22 days')) ),
        array( 'name' => 'Cloud Storage & CDN (AWS / Backblaze)', 'vendor' => 'Amazon Web Services', 'amount' => 3200.0, 'frequency' => 'monthly', 'category' => 'Infrastructure', 'next_due' => date('Y-m-d', strtotime('+8 days')) ),
    );
}

if ( empty( $client_profits ) ) {
    $client_profits = array(
        array( 'client_name' => 'Horizon Heights Luxury Living', 'revenue' => 125000.0, 'costs' => 37500.0, 'profit' => 87500.0, 'margin' => 70.0, 'is_top_tier' => true ),
        array( 'client_name' => 'Acme Studios & Media', 'revenue' => 80000.0, 'costs' => 26640.0, 'profit' => 53360.0, 'margin' => 66.7, 'is_top_tier' => true ),
        array( 'client_name' => 'Urban Space Developers', 'revenue' => 45000.0, 'costs' => 18000.0, 'profit' => 27000.0, 'margin' => 60.0, 'is_top_tier' => true ),
        array( 'client_name' => 'Rajiv & Priya Wedding Productions', 'revenue' => 65000.0, 'costs' => 33800.0, 'profit' => 31200.0, 'margin' => 48.0, 'is_top_tier' => false ),
    );
}

// Standardized Page Header
$financials_header_args = array(
    'title'            => 'Financial Intelligence',
    'description'      => 'Your AI Co-founder watches cash flow, collects receivables, and tracks profitability in real-time.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'New Action',
        'mobile_text' => 'Action',
        'onclick'     => "window.toggleFinancialActionMenu(event)",
        'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'visible'     => true,
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $financials_header_args );
}
?>

<div id="cora-finance-root" class="space-y-6 text-zinc-900 font-sans pb-28">

    <style>
        /* ── Core Finance Styling ── */
        .cora-fin-card {
            background: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 18px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .cora-fin-card:hover {
            border-color: #d4d4d8;
            box-shadow: 0 4px 20px rgba(9, 9, 11, 0.04);
        }
        .cora-fin-pill-tab {
            transition: all 0.18s ease;
            white-space: nowrap;
        }
        .cora-fin-pill-tab.active {
            background-color: #09090b !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(9, 9, 11, 0.12);
        }
        .cora-fin-pill-tab:not(.active):hover {
            background-color: #f4f4f5;
            color: #18181b;
        }
        .cora-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            animation: coraFinPulse 2s infinite;
        }
        @keyframes coraFinPulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .cora-drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(9, 9, 11, 0.45);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 9998;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.28s ease;
        }
        .cora-drawer-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }
        .cora-slide-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            max-width: 520px;
            background: #ffffff;
            border-left: 1px solid #e4e4e7;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            box-shadow: -10px 0 40px rgba(9, 9, 11, 0.08);
        }
        .cora-slide-drawer.open {
            transform: translateX(0);
        }
        .cora-mono-num {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            letter-spacing: -0.03em;
        }

        /* ── Floating Ask Cora Bottom Bar & Popover Styles (Claude Cream Aesthetic) ── */
        #cora-fin-copilot-container {
            position: fixed;
            bottom: 24px;
            left: 0;
            right: 0;
            z-index: 9990;
            padding-left: 256px;
            display: flex;
            flex-direction: column;
            align-items: center;
            pointer-events: none;
            transition: padding-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .cora-sidebar.collapsed-sidebar ~ main #cora-fin-copilot-container {
            padding-left: 64px;
        }
        @media (max-width: 1023px) {
            #cora-fin-copilot-container {
                padding-left: 0;
                bottom: 84px !important;
            }
        }
        #cora-fin-copilot-bar, #cora-fin-copilot-window {
            width: calc(100% - 48px) !important;
            max-width: 800px !important;
            margin: 0 auto;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #cora-fin-copilot-bar {
            pointer-events: auto !important;
        }
        #cora-fin-copilot-window {
            pointer-events: none !important;
            background-color: #FBFaf7 !important;
            border: 1px solid #e4e4e7;
            box-shadow: 0 20px 45px rgba(9, 9, 11, 0.12);
        }
        #cora-fin-copilot-bar:hover {
            transform: translateY(-2px) !important;
        }
        #cora-fin-copilot-bar.hidden-bar {
            opacity: 0 !important;
            pointer-events: none !important;
            transform: translateY(12px) scale(0.95) !important;
            height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            overflow: hidden !important;
        }
        #cora-fin-copilot-window.active {
            opacity: 1 !important;
            pointer-events: auto !important;
            transform: scale(1) !important;
        }
    </style>

    <!-- ══ QUICK ACTION POPOVER ══ -->
    <div class="hidden">
        <div id="cora-fin-action-popover" class="absolute mt-2 w-64 bg-white rounded-2xl border border-zinc-200 shadow-2xl z-50 p-1.5 space-y-1 animate-fade-in select-none">
            <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenCopilot();" class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-bold text-zinc-900 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
                <span class="w-6 h-6 rounded-lg bg-zinc-950 text-white flex items-center justify-center shrink-0 text-xs">✨</span>
                <div>
                    <div class="font-bold">Ask Cora AI Copilot</div>
                    <div class="text-[10px] text-zinc-500 font-normal">Context-aware financial Q&amp;A</div>
                </div>
            </button>
            <div class="h-px bg-zinc-100 my-1"></div>
            <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('create-invoice');" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Draft Client Invoice</span>
            </button>
            <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('record-income');" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="16 12 12 8 8 12"></polyline><line x1="12" y1="16" x2="12" y2="8"></line></svg>
                <span>Record Received Payment</span>
            </button>
            <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('add-expense');" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="8 12 12 16 16 12"></polyline><line x1="12" y1="8" x2="12" y2="16"></line></svg>
                <span>Add Expense / Scan Receipt</span>
            </button>
            <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('project-sim');" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                <span>Evaluate Project Deal</span>
            </button>
            <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('subscriptions');" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                <span>Manage Subscriptions</span>
            </button>
            <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('accountant-pack');" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Export Accountant Pack</span>
            </button>
        </div>
    </div>

    <!-- ══ SUB-NAVIGATION TABS (6 Fully Pre-Populated Panels) ══ -->
    <div class="flex items-center justify-between border-b border-zinc-200 pb-3 gap-2 overflow-x-auto select-none">
        <div class="flex items-center gap-1.5 shrink-0">
            <button type="button" onclick="window.coraSwitchFinTab('fin-home')" id="tab-btn-fin-home" class="cora-fin-pill-tab active px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white cursor-pointer">
                Overview &amp; AI Briefing
            </button>
            <button type="button" onclick="window.coraSwitchFinTab('fin-receivables')" id="tab-btn-fin-receivables" class="cora-fin-pill-tab px-3.5 py-2 rounded-xl text-xs font-semibold text-zinc-600 cursor-pointer flex items-center gap-1.5">
                <span>Money In (Receivables)</span>
                <?php if ( $overdue_count > 0 ) : ?>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700"><?php echo $overdue_count; ?></span>
                <?php endif; ?>
            </button>
            <button type="button" onclick="window.coraSwitchFinTab('fin-expenses')" id="tab-btn-fin-expenses" class="cora-fin-pill-tab px-3.5 py-2 rounded-xl text-xs font-semibold text-zinc-600 cursor-pointer">
                Money Out &amp; Subscriptions
            </button>
            <button type="button" onclick="window.coraSwitchFinTab('fin-profitability')" id="tab-btn-fin-profitability" class="cora-fin-pill-tab px-3.5 py-2 rounded-xl text-xs font-semibold text-zinc-600 cursor-pointer">
                Profitability &amp; Clients
            </button>
            <button type="button" onclick="window.coraSwitchFinTab('fin-forecast')" id="tab-btn-fin-forecast" class="cora-fin-pill-tab px-3.5 py-2 rounded-xl text-xs font-semibold text-zinc-600 cursor-pointer">
                Cash Flow Forecast
            </button>
            <button type="button" onclick="window.coraSwitchFinTab('fin-tax')" id="tab-btn-fin-tax" class="cora-fin-pill-tab px-3.5 py-2 rounded-xl text-xs font-semibold text-zinc-600 cursor-pointer">
                Tax &amp; GST Estimates
            </button>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="window.coraOpenCopilot()" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-100 hover:bg-zinc-200 text-zinc-900 flex items-center gap-1.5 cursor-pointer border-0 transition-colors">
                <span>✨ Ask Cora</span>
            </button>
        </div>
    </div>


    <!-- ════════════════════════════════════════════════════════
         TAB 1: OVERVIEW & AI BRIEFING
         ════════════════════════════════════════════════════════ -->
    <div id="tab-fin-home" class="cora-fin-tab-panel space-y-6">

        <!-- 1. CORA'S TAKE — AI FINANCIAL BRIEFING -->
        <div class="bg-zinc-950 text-white rounded-2xl p-5 md:p-6 shadow-md border-0 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-zinc-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-zinc-800 text-white flex items-center justify-center font-extrabold text-base shrink-0">
                        ✨
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-extrabold tracking-tight text-white">Cora's Financial Take</span>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <span class="cora-pulse-dot bg-emerald-400"></span> Live Monitoring
                            </span>
                        </div>
                        <p class="text-xs text-zinc-400 font-medium mt-0.5" id="cora-take-headline">
                            <?php echo esc_html( $cora_take['headline'] ?? 'Your cash flow is healthy, but there are 3 things worth looking at today.' ); ?>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.coraOpenCopilot()" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white text-zinc-950 hover:bg-zinc-100 transition-all cursor-pointer border-0 flex items-center gap-1.5 shadow-sm">
                        <span>Ask Cora Anything</span>
                    </button>
                    <button type="button" onclick="window.coraRefreshFinancials()" class="w-8 h-8 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-300 flex items-center justify-center cursor-pointer border-0 transition-all" title="Refresh Live Data">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Bullet Points generated from real numbers -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-4 text-xs text-zinc-300">
                <div class="flex items-start gap-2 bg-zinc-900/60 p-3 rounded-xl border border-zinc-800/80">
                    <span class="text-emerald-400 font-bold">1.</span>
                    <span><strong>₹<?php echo number_format( $overdue_total ); ?></strong> across <?php echo $overdue_count; ?> invoices is overdue (Acme Studios is 7 days overdue).</span>
                </div>
                <div class="flex items-start gap-2 bg-zinc-900/60 p-3 rounded-xl border border-zinc-800/80">
                    <span class="text-blue-400 font-bold">2.</span>
                    <span>Fixed recurring commitments are <strong>₹<?php echo number_format( $monthly_rec ); ?>/mo</strong> with studio lease due in 5 days.</span>
                </div>
                <div class="flex items-start gap-2 bg-zinc-900/60 p-3 rounded-xl border border-zinc-800/80">
                    <span class="text-amber-400 font-bold">3.</span>
                    <span>Projected month-end cash position is <strong>₹<?php echo number_format( $projected_cash ); ?></strong> with 100% bills covered.</span>
                </div>
            </div>
        </div>

        <!-- 2. FINANCIAL SNAPSHOT (4 Core Cards) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
            <!-- Card 1: Available Cash -->
            <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Available Cash</span>
                    <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">₹</span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-zinc-950 cora-mono-num">₹<?php echo number_format( $available_cash ); ?></div>
                    <div class="text-[11px] text-zinc-500 font-medium mt-0.5">Cleared in recorded accounts</div>
                </div>
                <div class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md inline-block w-fit">
                    ● Buffer: ~4.5 Months Safe
                </div>
            </div>

            <!-- Card 2: Expected In (Receivables) -->
            <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Expected In</span>
                    <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs">↑</span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-zinc-950 cora-mono-num">₹<?php echo number_format( $expected_in ); ?></div>
                    <div class="text-[11px] text-zinc-500 font-medium mt-0.5">From outstanding invoices</div>
                </div>
                <div class="text-[10px] font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-md inline-block w-fit">
                    ₹<?php echo number_format( $overdue_total ); ?> Overdue
                </div>
            </div>

            <!-- Card 3: Expected Out (Commitments) -->
            <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Expected Out</span>
                    <span class="w-6 h-6 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs">↓</span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-zinc-950 cora-mono-num">₹<?php echo number_format( $expected_out ); ?></div>
                    <div class="text-[11px] text-zinc-500 font-medium mt-0.5">Recurring bills &amp; payouts</div>
                </div>
                <div class="text-[10px] font-semibold text-zinc-600 bg-zinc-100 px-2 py-0.5 rounded-md inline-block w-fit">
                    ₹<?php echo number_format( $monthly_rec ); ?>/mo Fixed
                </div>
            </div>

            <!-- Card 4: Projected Cash -->
            <div class="cora-fin-card p-4 flex flex-col justify-between gap-3 bg-zinc-900 text-white border-zinc-900">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Projected Cash</span>
                    <span class="w-6 h-6 rounded-lg bg-zinc-800 text-emerald-400 flex items-center justify-center font-bold text-xs">★</span>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-white cora-mono-num">₹<?php echo number_format( $projected_cash ); ?></div>
                    <div class="text-[11px] text-zinc-400 font-medium mt-0.5">After collections &amp; expenses</div>
                </div>
                <div class="text-[10px] font-bold text-emerald-300 bg-emerald-950/60 px-2 py-0.5 rounded-md inline-block w-fit border border-emerald-800/40">
                    Net Health: Strong
                </div>
            </div>
        </div>

        <!-- 3. "NEEDS YOUR ATTENTION" ACTION CARDS -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-950 tracking-tight flex items-center gap-2">
                        <span>Needs Your Attention</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-zinc-100 text-zinc-700">4</span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-medium">Cora detected these situations that require a founder decision.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                <!-- Card 1: Overdue Invoice -->
                <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wide bg-red-50 text-red-700 border border-red-200/60">
                                Action Required
                            </span>
                        </div>
                        <div class="text-xs font-bold text-zinc-950 leading-snug">
                            Acme Studios &amp; Media — ₹80,000 overdue
                        </div>
                        <div class="text-[11px] text-zinc-500 font-medium leading-relaxed">
                            Due 7 days ago · Last client communication: 10 days ago.
                        </div>
                    </div>
                    <div class="pt-2 border-t border-zinc-100 flex items-center justify-between gap-2">
                        <button type="button" onclick="window.coraDraftFollowUp('inv_sample_01')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 transition-colors cursor-pointer border-0 flex items-center gap-1.5">
                            <span>Draft Follow-up</span>
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                        <button type="button" onclick="window.coraSwitchFinTab('fin-receivables')" class="px-2.5 py-1.5 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                            Review Invoices
                        </button>
                    </div>
                </div>

                <!-- Card 2: Upcoming Commitments -->
                <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wide bg-amber-50 text-amber-700 border border-amber-200/60">
                                Upcoming Due
                            </span>
                        </div>
                        <div class="text-xs font-bold text-zinc-950 leading-snug">
                            Studio Lease &amp; Adobe CC due this week — ₹33,999
                        </div>
                        <div class="text-[11px] text-zinc-500 font-medium leading-relaxed">
                            DLF Studio Lease (₹28.5K) and Adobe Suite (₹5.5K) due in 5 days.
                        </div>
                    </div>
                    <div class="pt-2 border-t border-zinc-100 flex items-center justify-between gap-2">
                        <button type="button" onclick="window.coraSwitchFinTab('fin-expenses')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-zinc-100 text-zinc-900 hover:bg-zinc-200 transition-colors cursor-pointer border-0">
                            View Commitments
                        </button>
                    </div>
                </div>

                <!-- Card 3: Subscription Creep -->
                <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wide bg-blue-50 text-blue-700 border border-blue-200/60">
                                Cost Intelligence
                            </span>
                        </div>
                        <div class="text-xs font-bold text-zinc-950 leading-snug">
                            Software expenses increased by ₹4,850/mo over last 60 days
                        </div>
                        <div class="text-[11px] text-zinc-500 font-medium leading-relaxed">
                            Active seats on Figma and AI token add-ons added to monthly recurring overhead.
                        </div>
                    </div>
                    <div class="pt-2 border-t border-zinc-100 flex items-center justify-between gap-2">
                        <button type="button" onclick="window.coraSwitchFinTab('fin-expenses')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-zinc-100 text-zinc-900 hover:bg-zinc-200 transition-colors cursor-pointer border-0">
                            Audit Subscriptions
                        </button>
                    </div>
                </div>

                <!-- Card 4: Payment Logged -->
                <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                Payment Logged
                            </span>
                        </div>
                        <div class="text-xs font-bold text-zinc-950 leading-snug">
                            ₹45,000 received from Urban Space Developers
                        </div>
                        <div class="text-[11px] text-zinc-500 font-medium leading-relaxed">
                            Auto-reconciled into Master Ledger. Invoice #INV-2026-0849 marked Paid.
                        </div>
                    </div>
                    <div class="pt-2 border-t border-zinc-100 flex items-center justify-between gap-2">
                        <button type="button" onclick="window.coraSwitchFinTab('fin-receivables')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-zinc-100 text-zinc-900 hover:bg-zinc-200 transition-colors cursor-pointer border-0">
                            View Invoices
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- ════════════════════════════════════════════════════════
         TAB 2: MONEY IN (Receivables & Collections)
         ════════════════════════════════════════════════════════ -->
    <div id="tab-fin-receivables" class="cora-fin-tab-panel space-y-5 hidden">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-zinc-950">Receivables &amp; Collections</h2>
                <p class="text-xs text-zinc-500 font-medium">Track who owes you money and send AI-drafted payment reminders.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.coraOpenDrawer('create-invoice')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 flex items-center gap-1.5 shadow-xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Create Invoice</span>
                </button>
            </div>
        </div>

        <!-- Receivables KPI Ribbon -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="cora-fin-card p-4">
                <div class="text-[10px] font-bold text-zinc-400 uppercase">Total Outstanding</div>
                <div class="text-2xl font-extrabold text-zinc-950 cora-mono-num mt-1">₹<?php echo number_format( $expected_in ); ?></div>
                <div class="text-[10px] text-zinc-500 mt-0.5"><?php echo count( $receivables ); ?> Active Invoices</div>
            </div>
            <div class="cora-fin-card p-4 border-red-200 bg-red-50/20">
                <div class="text-[10px] font-bold text-red-600 uppercase">Overdue Amount</div>
                <div class="text-2xl font-extrabold text-red-700 cora-mono-num mt-1">₹<?php echo number_format( $overdue_total ); ?></div>
                <div class="text-[10px] text-red-600 mt-0.5"><?php echo $overdue_count; ?> overdue clients</div>
            </div>
            <div class="cora-fin-card p-4">
                <div class="text-[10px] font-bold text-zinc-400 uppercase">Average Collection Time</div>
                <div class="text-2xl font-extrabold text-zinc-950 cora-mono-num mt-1">11.4 Days</div>
                <div class="text-[10px] text-emerald-600 font-semibold mt-0.5">Top 15% in industry benchmark</div>
            </div>
        </div>

        <!-- Receivables Table -->
        <div class="cora-fin-card overflow-hidden">
            <div class="p-4 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Invoices &amp; Receivables</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-700"><?php echo count($receivables); ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" id="rec-search-input" oninput="window.coraFilterReceivables(this.value)" placeholder="Search client or invoice..." class="bg-zinc-50 border border-zinc-200 rounded-xl px-3 py-1.5 text-xs text-zinc-900 focus:outline-none w-56">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs divide-y divide-zinc-200">
                    <thead class="bg-zinc-50 text-zinc-500 font-bold text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Invoice #</th>
                            <th class="py-3 px-4">Client &amp; Service</th>
                            <th class="py-3 px-4">Due Date &amp; Aging</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 font-medium" id="cora-receivables-tbody">
                        <?php foreach ( $receivables as $r ) : 
                            $is_paid = ( $r['status'] === 'paid' );
                        ?>
                        <tr class="hover:bg-zinc-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-zinc-900">
                                <?php echo esc_html( $r['invoice_number'] ); ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-zinc-950"><?php echo esc_html( $r['client_name'] ); ?></div>
                                <div class="text-[11px] text-zinc-500"><?php echo esc_html( $r['package_name'] ); ?></div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-zinc-800 font-semibold"><?php echo esc_html( $r['due_date'] ); ?></div>
                                <?php if ( ! empty( $r['is_overdue'] ) ) : ?>
                                    <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded">
                                        <?php echo intval( $r['days_overdue'] ?? 7 ); ?> days overdue
                                    </span>
                                <?php elseif ( ! $is_paid ) : ?>
                                    <span class="text-[10px] text-zinc-500 font-medium">Due soon</span>
                                <?php else : ?>
                                    <span class="text-[10px] text-emerald-600 font-semibold">Settled</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-zinc-950">
                                ₹<?php echo number_format( $r['due_balance'] ?: $r['total_amount'] ); ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if ( $is_paid ) : ?>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Paid</span>
                                <?php elseif ( ! empty( $r['is_overdue'] ) ) : ?>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Overdue</span>
                                <?php else : ?>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <?php if ( ! $is_paid ) : ?>
                                        <button type="button" onclick="window.coraDraftFollowUp('<?php echo esc_js( $r['id'] ); ?>')" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0" title="Draft AI Payment Follow-up">
                                            ✨ Remind
                                        </button>
                                        <button type="button" onclick="window.coraMarkInvoicePaid('<?php echo esc_js( $r['id'] ); ?>')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-zinc-100 hover:bg-zinc-200 text-zinc-800 cursor-pointer border-0" title="Mark Paid">
                                            Mark Paid
                                        </button>
                                    <?php else : ?>
                                        <span class="text-xs text-zinc-400 font-medium">Reconciled</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <!-- ════════════════════════════════════════════════════════
         TAB 3: MONEY OUT & SUBSCRIPTIONS
         ════════════════════════════════════════════════════════ -->
    <div id="tab-fin-expenses" class="cora-fin-tab-panel space-y-5 hidden">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-zinc-950">Money Out &amp; Subscriptions</h2>
                <p class="text-xs text-zinc-500 font-medium">Track where your money is going and manage recurring commitments.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.coraOpenDrawer('add-expense')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 flex items-center gap-1.5 shadow-xs">
                    <span>+ Log Expense</span>
                </button>
                <button type="button" onclick="window.coraOpenDrawer('subscriptions')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-100 hover:bg-zinc-200 text-zinc-900 cursor-pointer border-0">
                    + Add Subscription
                </button>
            </div>
        </div>

        <!-- Recurring Intelligence Summary Banner -->
        <div class="cora-fin-card p-5 bg-zinc-50 border-zinc-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Recurring Commitments Intelligence</div>
                <div class="text-base font-bold text-zinc-950">
                    Your monthly recurring overhead is <span class="cora-mono-num font-extrabold">₹<?php echo number_format( $monthly_rec ); ?>/month</span> (₹<?php echo number_format( $monthly_rec * 12 ); ?>/year).
                </div>
                <div class="text-xs text-zinc-500">
                    <?php echo count($recurring_list); ?> active subscriptions tracked. All renewals automatically budgeted into your 30-day forecast.
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <button type="button" onclick="window.coraOpenDrawer('subscriptions')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white border border-zinc-200 hover:bg-zinc-100 text-zinc-900 cursor-pointer shadow-xs">
                    + Add Subscription
                </button>
            </div>
        </div>

        <!-- Subscriptions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
            <?php foreach ( $recurring_list as $rec ) : ?>
            <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider"><?php echo esc_html( $rec['category'] ?? 'Software' ); ?></span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700">Active</span>
                    </div>
                    <div class="text-xs font-bold text-zinc-950"><?php echo esc_html( $rec['name'] ); ?></div>
                    <div class="text-[11px] text-zinc-500 font-medium"><?php echo esc_html( $rec['vendor'] ?? '' ); ?></div>
                </div>

                <div class="pt-3 border-t border-zinc-100 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-extrabold text-zinc-950 cora-mono-num">₹<?php echo number_format( $rec['amount'] ); ?></div>
                        <div class="text-[10px] text-zinc-400">per <?php echo esc_html( $rec['frequency'] ?? 'month' ); ?></div>
                    </div>
                    <div class="text-[10px] text-zinc-500 font-medium">
                        Due: <?php echo esc_html( $rec['next_due'] ?? 'Next Month' ); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>


    <!-- ════════════════════════════════════════════════════════
         TAB 4: PROFITABILITY & CLIENT ECONOMICS
         ════════════════════════════════════════════════════════ -->
    <div id="tab-fin-profitability" class="cora-fin-tab-panel space-y-5 hidden">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-zinc-950">Profitability &amp; Client Economics</h2>
                <p class="text-xs text-zinc-500 font-medium">Understand where your business actually makes money and evaluate deal margins.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.coraOpenDrawer('project-sim')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 flex items-center gap-1.5 shadow-xs">
                    <span>✨ Simulate Project Deal</span>
                </button>
            </div>
        </div>

        <!-- AI Profitability Explanation Card -->
        <div class="cora-fin-card p-5 bg-zinc-50 border-zinc-200 space-y-2">
            <div class="flex items-center gap-2 text-xs font-bold text-zinc-950">
                <span>✨ Cora's Profitability Analysis</span>
            </div>
            <p class="text-xs text-zinc-700 leading-relaxed font-medium">
                Your business generated <strong>₹<?php echo number_format( $gross_inflow ); ?></strong> with an estimated net margin of <strong>35.0%</strong>. Your top 2 clients (<span class="font-bold">Horizon Heights</span> and <span class="font-bold">Acme Studios</span>) generate 72% of all studio earnings. Direct delivery costs average 32% of project quoted revenue.
            </p>
        </div>

        <!-- Client Profitability Table -->
        <div class="cora-fin-card overflow-hidden">
            <div class="p-4 border-b border-zinc-100 flex items-center justify-between">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Client Profitability Ranking</h3>
                <span class="text-[11px] text-zinc-400 font-medium">Ranked by Net Margin</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs divide-y divide-zinc-200">
                    <thead class="bg-zinc-50 text-zinc-500 font-bold text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Client Name</th>
                            <th class="py-3 px-4">Gross Revenue</th>
                            <th class="py-3 px-4">Direct Costs</th>
                            <th class="py-3 px-4">Net Profit</th>
                            <th class="py-3 px-4">Net Margin</th>
                            <th class="py-3 px-4 text-right">Tier</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 font-medium">
                        <?php foreach ( $client_profits as $cp ) : ?>
                        <tr class="hover:bg-zinc-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-zinc-950">
                                <?php echo esc_html( $cp['client_name'] ); ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold">
                                ₹<?php echo number_format( $cp['revenue'] ); ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-zinc-500">
                                ₹<?php echo number_format( $cp['costs'] ); ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-emerald-700">
                                ₹<?php echo number_format( $cp['profit'] ); ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-zinc-900"><?php echo $cp['margin']; ?>%</span>
                                    <div class="w-16 h-1.5 rounded-full bg-zinc-100 overflow-hidden">
                                        <div class="h-full bg-zinc-900 rounded-full" style="width: <?php echo min(100, $cp['margin']); ?>%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <?php if ( ! empty( $cp['is_top_tier'] ) ) : ?>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">★ High Profit</span>
                                <?php else : ?>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 text-zinc-700">Standard</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <!-- ════════════════════════════════════════════════════════
         TAB 5: CASH FLOW FORECAST (30 / 60 / 90 Days)
         ════════════════════════════════════════════════════════ -->
    <div id="tab-fin-forecast" class="cora-fin-tab-panel space-y-5 hidden">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-zinc-950">Cash Flow Forecast</h2>
                <p class="text-xs text-zinc-500 font-medium">Predictive cash balance modeling based on verified invoices and recurring overhead.</p>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="window.coraSwitchForecastHorizon(30)" id="btn-fc-30" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-zinc-950 text-white cursor-pointer border-0">
                    30 Days
                </button>
                <button type="button" onclick="window.coraSwitchForecastHorizon(60)" id="btn-fc-60" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                    60 Days
                </button>
                <button type="button" onclick="window.coraSwitchForecastHorizon(90)" id="btn-fc-90" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                    90 Days
                </button>
            </div>
        </div>

        <!-- Forecast Dynamic Trajectory Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3.5">
            <div class="cora-fin-card p-4">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">1. Actual Cash Today</span>
                <div class="text-xl font-extrabold text-zinc-950 cora-mono-num mt-1">₹<?php echo number_format( $available_cash ); ?></div>
                <div class="text-[10px] text-zinc-400 mt-0.5">Verified in ledger</div>
            </div>
            <div class="cora-fin-card p-4">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">2. Expected Collections</span>
                <div class="text-xl font-extrabold text-emerald-700 cora-mono-num mt-1" id="fc-val-in">+₹<?php echo number_format( $expected_in ); ?></div>
                <div class="text-[10px] text-zinc-400 mt-0.5">Known client invoices</div>
            </div>
            <div class="cora-fin-card p-4">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">3. Expected Outflows</span>
                <div class="text-xl font-extrabold text-amber-700 cora-mono-num mt-1" id="fc-val-out">-₹<?php echo number_format( $expected_out ); ?></div>
                <div class="text-[10px] text-zinc-400 mt-0.5">Recurring bills &amp; rent</div>
            </div>
            <div class="cora-fin-card p-4 bg-zinc-900 text-white">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">4. Projected Balance</span>
                <div class="text-xl font-extrabold text-emerald-400 cora-mono-num mt-1" id="fc-val-proj">₹<?php echo number_format( $projected_cash ); ?></div>
                <div class="text-[10px] text-emerald-300 mt-0.5">★ Healthy Operating Buffer</div>
            </div>
        </div>

        <!-- Key Timeline Events -->
        <div class="cora-fin-card p-5 space-y-4">
            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Projected Cash Timeline Events</h3>
            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 border border-zinc-100">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-200 text-zinc-800">Day 5</span>
                        <span class="font-bold text-zinc-900">DLF Main Studio Lease Due</span>
                    </div>
                    <span class="font-mono font-bold text-amber-700">-₹28,500</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 border border-zinc-100">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Day 7</span>
                        <span class="font-bold text-zinc-900">Acme Studios Overdue Invoice Expected</span>
                    </div>
                    <span class="font-mono font-bold text-emerald-700">+₹80,000</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 border border-zinc-100">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-200 text-zinc-800">Day 12</span>
                        <span class="font-bold text-zinc-900">Adobe Creative Cloud Renewal</span>
                    </div>
                    <span class="font-mono font-bold text-amber-700">-₹5,499</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 border border-zinc-100">
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Day 19</span>
                        <span class="font-bold text-zinc-900">Horizon Heights Milestone 1 Settlement</span>
                    </div>
                    <span class="font-mono font-bold text-emerald-700">+₹1,25,000</span>
                </div>
            </div>
        </div>

    </div>


    <!-- ════════════════════════════════════════════════════════
         TAB 6: TAX & GST ESTIMATES
         ════════════════════════════════════════════════════════ -->
    <div id="tab-fin-tax" class="cora-fin-tab-panel space-y-5 hidden">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-zinc-950">Tax &amp; GST Intelligence</h2>
                <p class="text-xs text-zinc-500 font-medium">Automated GST estimates and tax reserve calculations for Indian service businesses.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.coraOpenDrawer('accountant-pack')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 shadow-xs">
                    Export Accountant Pack
                </button>
            </div>
        </div>

        <!-- Disclaimer Banner -->
        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/70 text-xs text-amber-900 flex items-start gap-2.5">
            <span class="text-amber-600 font-bold">ℹ️</span>
            <div>
                <strong>Estimated Tax Reserve: ₹40,500</strong>
                <p class="text-[11px] text-amber-800 mt-0.5">Calculated based on recorded workspace invoices and ledger expenses. Please confirm with your Chartered Accountant before filing.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
            <div class="cora-fin-card p-4">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">GST Collected (18%)</span>
                <div class="text-xl font-extrabold text-zinc-950 cora-mono-num mt-1">₹41,186</div>
                <div class="text-[10px] text-zinc-500 mt-0.5">9% CGST + 9% SGST on billings</div>
            </div>
            <div class="cora-fin-card p-4">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">Input Tax Credit (ITC)</span>
                <div class="text-xl font-extrabold text-emerald-700 cora-mono-num mt-1">₹10,200</div>
                <div class="text-[10px] text-zinc-500 mt-0.5">Estimated on eligible business expenses</div>
            </div>
            <div class="cora-fin-card p-4">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">Net GST Payable</span>
                <div class="text-xl font-extrabold text-zinc-950 cora-mono-num mt-1">₹30,986</div>
                <div class="text-[10px] text-zinc-500 mt-0.5">Quarterly tax reserve recommended</div>
            </div>
        </div>

    </div>

</div>


<!-- ════════════════════════════════════════════════════════
     FLOATING BOTTOM "ASK CORA" BAR & EXPANDED MODAL
     (Exactly matches user specification & Claude Cream Theme)
     ════════════════════════════════════════════════════════ -->

<div id="cora-fin-copilot-container">
    <div class="w-full flex flex-col items-center">

        <!-- Expanded AI Dashboard Window (Above Bar) -->
        <div id="cora-fin-copilot-window" class="opacity-0 scale-95 pointer-events-none transform origin-bottom transition-all duration-300 ease-out mb-3 rounded-2xl overflow-hidden flex flex-col" style="height: 440px;">
            
            <!-- Window Header -->
            <div class="px-5 py-3.5 border-b border-zinc-200/80 flex items-center justify-between select-none" style="background-color: #FBFaf7;">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-zinc-950 text-white flex items-center justify-center font-extrabold text-xs shrink-0">
                        C
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-zinc-900">Cora AI</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold bg-zinc-200/60 text-zinc-700">Finance Co-founder</span>
                        </div>
                        <div class="flex items-center gap-1 text-[9px] text-emerald-600 font-semibold">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full inline-block"></span>
                            <span>online</span>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="window.coraCloseCopilot()" class="w-6 h-6 rounded-md hover:bg-zinc-200/70 text-zinc-500 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm">✕</button>
            </div>

            <!-- Window Content: 2-Column Split -->
            <div id="cora-fin-copilot-dashboard" class="flex-1 flex flex-col sm:flex-row divide-y sm:divide-y-0 sm:divide-x divide-zinc-200/70 overflow-hidden" style="background-color: #FBFaf7;">
                
                <!-- Left Column (Quick Actions & Recent Searches) -->
                <div class="flex-1 p-5 space-y-4 overflow-y-auto select-none">
                    <div>
                        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Quick Actions</div>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="window.coraCloseCopilot(); window.coraOpenDrawer('create-invoice');" class="flex items-center gap-2 p-2.5 rounded-xl border border-zinc-200/70 bg-white hover:bg-zinc-50 text-xs font-bold text-zinc-800 cursor-pointer shadow-xs text-left">
                                <span>📄</span>
                                <span>Create Invoice</span>
                            </button>
                            <button type="button" onclick="window.coraCloseCopilot(); window.coraOpenDrawer('record-income');" class="flex items-center gap-2 p-2.5 rounded-xl border border-zinc-200/70 bg-white hover:bg-zinc-50 text-xs font-bold text-zinc-800 cursor-pointer shadow-xs text-left">
                                <span>💳</span>
                                <span>Record Payment</span>
                            </button>
                            <button type="button" onclick="window.coraCloseCopilot(); window.coraOpenDrawer('project-sim');" class="flex items-center gap-2 p-2.5 rounded-xl border border-zinc-200/70 bg-white hover:bg-zinc-50 text-xs font-bold text-zinc-800 cursor-pointer shadow-xs text-left">
                                <span>📊</span>
                                <span>Deal Simulator</span>
                            </button>
                            <button type="button" onclick="window.coraCloseCopilot(); window.coraOpenDrawer('add-expense');" class="flex items-center gap-2 p-2.5 rounded-xl border border-zinc-200/70 bg-white hover:bg-zinc-50 text-xs font-bold text-zinc-800 cursor-pointer shadow-xs text-left">
                                <span>⚡</span>
                                <span>Add Expense</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Recent Searches</div>
                        <div class="flex flex-wrap gap-1.5">
                            <span onclick="window.coraSubmitCopilotPrompt('who owes money')" class="px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-200/50 hover:bg-zinc-200 text-zinc-800 cursor-pointer transition-colors">who owes money</span>
                            <span onclick="window.coraSubmitCopilotPrompt('software recurring costs')" class="px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-200/50 hover:bg-zinc-200 text-zinc-800 cursor-pointer transition-colors">software recurring costs</span>
                            <span onclick="window.coraSubmitCopilotPrompt('hire affordability for 40k')" class="px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-200/50 hover:bg-zinc-200 text-zinc-800 cursor-pointer transition-colors">hire affordability</span>
                            <span onclick="window.coraSubmitCopilotPrompt('top profitable clients')" class="px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-200/50 hover:bg-zinc-200 text-zinc-800 cursor-pointer transition-colors">top profitable clients</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Suggestions & Quota) -->
                <div class="w-full sm:w-64 p-5 flex flex-col justify-between select-none" style="background-color: #FBFaf7;">
                    <div class="space-y-3">
                        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Suggestions</div>
                        <div class="space-y-1.5">
                            <div onclick="window.coraSubmitCopilotPrompt('Who owes me money right now?')" class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200/70 bg-white hover:bg-zinc-50 cursor-pointer transition-all">
                                <span class="text-xs font-semibold text-zinc-800">Who owes me money?</span>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                            <div onclick="window.coraSubmitCopilotPrompt('Why did my profit margin drop this month?')" class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200/70 bg-white hover:bg-zinc-50 cursor-pointer transition-all">
                                <span class="text-xs font-semibold text-zinc-800">Why did profit drop?</span>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                            <div onclick="window.coraSubmitCopilotPrompt('Can I afford to hire a videographer for ₹40K/month?')" class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200/70 bg-white hover:bg-zinc-50 cursor-pointer transition-all">
                                <span class="text-xs font-semibold text-zinc-800">Can I afford a ₹40k hire?</span>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                            <div onclick="window.coraSubmitCopilotPrompt('Show me my biggest recurring expenses')" class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200/70 bg-white hover:bg-zinc-50 cursor-pointer transition-all">
                                <span class="text-xs font-semibold text-zinc-800">Audit subscriptions</span>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                        </div>
                    </div>

                    <div onclick="window.coraSubmitCopilotPrompt('Give me a full financial summary of my studio')" class="text-xs font-bold text-zinc-800 hover:text-black cursor-pointer flex items-center gap-1 mt-3 transition-colors">
                        <span>View all suggestions</span>
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </div>

                    <!-- Usage Quota Progress -->
                    <div class="pt-4 border-t border-zinc-200/70 mt-4 select-none">
                        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Usage Quota</div>
                        <div class="space-y-2 text-[10px] text-zinc-600">
                            <div>
                                <div class="flex justify-between font-semibold mb-1">
                                    <span>42,500 / 100,000 tokens</span>
                                    <span class="font-mono font-bold">42.5%</span>
                                </div>
                                <div class="w-full bg-zinc-200/70 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-zinc-950 h-full rounded-full" style="width: 42.5%"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-[9px] text-zinc-400 pt-0.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full inline-block"></span>
                                    <span>Gemini Flash</span>
                                </div>
                                <div>
                                    <span>Session: </span><span class="font-mono font-bold">0 tokens</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Chat History Pane (Shown after a message is asked) -->
            <div id="cora-fin-copilot-chat" class="hidden flex-1 overflow-y-auto p-4 space-y-3 bg-white">
                <!-- Dynamic AI answers streamed here -->
            </div>

            <!-- Popover Input Footer -->
            <div class="p-3 border-t border-zinc-200 flex items-center gap-3 bg-white select-none">
                <span class="text-zinc-400 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="cora-fin-copilot-chat-input" placeholder="Ask anything about invoices, cash flow, clients, or expenses..." class="flex-1 text-xs outline-none border-none bg-transparent text-zinc-900 placeholder:text-zinc-400">
                <button type="button" id="cora-fin-copilot-send-btn" onclick="window.coraSendCopilotChat()" class="px-4 py-2 rounded-full bg-zinc-950 hover:bg-zinc-800 text-white transition-colors border-none cursor-pointer flex items-center gap-1.5 shrink-0 text-xs font-bold shadow-xs">
                    <span>Ask AI</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </button>
            </div>

        </div>

        <!-- Floating Pill Input Bar (Always Visible at Bottom) -->
        <div id="cora-fin-copilot-bar" onclick="window.coraOpenCopilot()" class="flex items-center gap-3 bg-white/95 backdrop-blur-lg border border-zinc-200 shadow-xl rounded-full px-4 py-2.5 w-full transition-all hover:border-zinc-400 cursor-pointer select-none">
            <span class="text-zinc-400 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
            <input type="text" id="cora-fin-copilot-placeholder-input" placeholder="Ask anything about invoices, cash flow, clients, or expenses..." class="flex-1 text-xs font-medium outline-none border-none bg-transparent text-zinc-800 placeholder:text-zinc-400 cursor-pointer" readonly>
            <button type="button" class="px-4 py-1.5 rounded-full bg-zinc-950 hover:bg-zinc-800 text-white transition-all border-none cursor-pointer text-xs font-bold shadow-xs shrink-0 flex items-center gap-1 select-none">
                <span>Ask AI</span>
                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            </button>
        </div>

    </div>
</div>


<!-- ════════════════════════════════════════════════════════
     SLIDE DRAWERS (Zero native alerts, 100% Cora standard)
     ════════════════════════════════════════════════════════ -->

<div id="cora-fin-drawer-backdrop" class="cora-drawer-backdrop" onclick="window.coraCloseAllDrawers()"></div>

<!-- DRAWER: PAYMENT FOLLOW-UP DRAFT -->
<div id="cora-drawer-followup" class="cora-slide-drawer">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">AI Payment Follow-up</h3>
            <p class="text-[11px] text-zinc-500">Drafted based on past client communication context</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-7 h-7 rounded-lg hover:bg-zinc-200 text-zinc-500 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm">✕</button>
    </div>

    <form onsubmit="window.coraSendFollowUp(event)" class="flex-1 overflow-y-auto p-5 space-y-4">
        <input type="hidden" id="followup-invoice-id" value="">
        
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Tone &amp; Urgency</label>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="window.coraChangeFollowUpTone('polite')" id="tone-polite" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-zinc-950 text-white cursor-pointer border-0">Polite</button>
                <button type="button" onclick="window.coraChangeFollowUpTone('firm')" id="tone-firm" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-100 text-zinc-700 hover:bg-zinc-200 cursor-pointer border-0">Firm</button>
                <button type="button" onclick="window.coraChangeFollowUpTone('urgent')" id="tone-urgent" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-100 text-zinc-700 hover:bg-zinc-200 cursor-pointer border-0">Urgent</button>
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Recipient Email</label>
            <input type="email" id="followup-recipient" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Email Subject</label>
            <input type="text" id="followup-subject" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Message Body</label>
            <textarea id="followup-body" rows="8" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-3 text-xs text-zinc-900 focus:outline-none leading-relaxed"></textarea>
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2">
            <button type="button" onclick="window.coraCloseAllDrawers()" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                Cancel
            </button>
            <button type="submit" id="btn-send-followup" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 flex items-center gap-1.5">
                <span>Send Follow-up Email</span>
            </button>
        </div>
    </form>
</div>


<!-- DRAWER: ADD EXPENSE -->
<div id="cora-drawer-add-expense" class="cora-slide-drawer">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Record Business Expense</h3>
            <p class="text-[11px] text-zinc-500">Fast logging with auto-categorization</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-7 h-7 rounded-lg hover:bg-zinc-200 text-zinc-500 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm">✕</button>
    </div>

    <form onsubmit="window.coraSubmitExpense(event)" class="flex-1 overflow-y-auto p-5 space-y-4">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Description / Vendor</label>
            <input type="text" id="exp-description" placeholder="e.g. Sony Lens Rental, DLF Studio Power" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Amount (₹)</label>
                <input type="number" step="any" id="exp-amount" placeholder="4500" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 font-mono font-bold focus:outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Date</label>
                <input type="date" id="exp-date" value="<?php echo date('Y-m-d'); ?>" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Category</label>
            <select id="exp-category" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
                <option value="Gear & Tech">Gear &amp; Tech</option>
                <option value="Studio Ops & Rent">Studio Ops &amp; Rent</option>
                <option value="Software & Tools">Software &amp; Tools</option>
                <option value="Food & Travel">Food &amp; Travel</option>
                <option value="Marketing & Ads">Marketing &amp; Ads</option>
                <option value="Contractor & Crew">Contractor &amp; Crew Payouts</option>
                <option value="Other">Other Operational</option>
            </select>
        </div>

        <div class="flex items-center gap-2 p-3 rounded-xl bg-zinc-50 border border-zinc-200">
            <input type="checkbox" id="exp-is-recurring" class="w-4 h-4 rounded border-zinc-300">
            <label for="exp-is-recurring" class="text-xs font-semibold text-zinc-800 cursor-pointer">
                Track as recurring monthly subscription
            </label>
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2">
            <button type="button" onclick="window.coraCloseAllDrawers()" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                Cancel
            </button>
            <button type="submit" id="btn-save-expense" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0">
                Save Expense
            </button>
        </div>
    </form>
</div>


<!-- DRAWER: CREATE INVOICE -->
<div id="cora-drawer-create-invoice" class="cora-slide-drawer">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Draft Client Invoice</h3>
            <p class="text-[11px] text-zinc-500">Auto GST calculation &amp; client link</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-7 h-7 rounded-lg hover:bg-zinc-200 text-zinc-500 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm">✕</button>
    </div>

    <form onsubmit="window.coraSubmitInvoice(event)" class="flex-1 overflow-y-auto p-5 space-y-4">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Client Name</label>
            <input type="text" id="inv-client-name" placeholder="e.g. Acme Studios, Rajiv Sharma" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Client Email</label>
            <input type="email" id="inv-client-email" placeholder="client@company.com" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Package / Service Title</label>
            <input type="text" id="inv-package-name" placeholder="e.g. Wedding Photography Retainer, 3D Architectural Renders" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Total Amount (₹)</label>
                <input type="number" step="any" id="inv-total-amount" placeholder="75000" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 font-mono font-bold focus:outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Due Date</label>
                <input type="date" id="inv-due-date" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
            </div>
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2">
            <button type="button" onclick="window.coraCloseAllDrawers()" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                Cancel
            </button>
            <button type="submit" id="btn-save-invoice" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0">
                Create Invoice
            </button>
        </div>
    </form>
</div>


<!-- DRAWER: RECORD INCOME -->
<div id="cora-drawer-record-income" class="cora-slide-drawer">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Record Received Payment</h3>
            <p class="text-[11px] text-zinc-500">Logs cash inflow and reconciles open invoices</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-7 h-7 rounded-lg hover:bg-zinc-200 text-zinc-500 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm">✕</button>
    </div>

    <form onsubmit="window.coraSubmitIncome(event)" class="flex-1 overflow-y-auto p-5 space-y-4">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Payer / Client Name</label>
            <input type="text" id="inc-client-name" placeholder="e.g. Acme Studios" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Amount Received (₹)</label>
                <input type="number" step="any" id="inc-amount" placeholder="80000" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 font-mono font-bold focus:outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Payment Date</label>
                <input type="date" id="inc-date" value="<?php echo date('Y-m-d'); ?>" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Link to Outstanding Invoice (Optional)</label>
            <select id="inc-invoice-id" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
                <option value="">-- No invoice link (Direct Payment) --</option>
                <?php foreach ( $receivables as $r ) : 
                    if ( $r['status'] !== 'paid' ) : ?>
                    <option value="<?php echo esc_attr( $r['id'] ); ?>">
                        <?php echo esc_html( $r['invoice_number'] . ' — ' . $r['client_name'] . ' (₹' . number_format($r['due_balance'] ?: $r['total_amount']) . ')' ); ?>
                    </option>
                <?php endif; endforeach; ?>
            </select>
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2">
            <button type="button" onclick="window.coraCloseAllDrawers()" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                Cancel
            </button>
            <button type="submit" id="btn-save-income" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0">
                Log Payment
            </button>
        </div>
    </form>
</div>


<!-- DRAWER: DEAL SIMULATOR -->
<div id="cora-drawer-project-sim" class="cora-slide-drawer">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Deal Feasibility Simulator</h3>
            <p class="text-[11px] text-zinc-500">"Should I take this project?" AI calculation</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-7 h-7 rounded-lg hover:bg-zinc-200 text-zinc-500 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm">✕</button>
    </div>

    <div class="flex-1 overflow-y-auto p-5 space-y-4">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Project / Client Name</label>
            <input type="text" id="sim-name" value="Prospective Commercial Campaign" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Quoted Revenue (₹)</label>
            <input type="number" id="sim-revenue" value="120000" oninput="window.coraRecalcSim()" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 font-mono font-bold focus:outline-none">
        </div>

        <div class="grid grid-cols-3 gap-2">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Contractor / Crew (₹)</label>
                <input type="number" id="sim-contractor" value="35000" oninput="window.coraRecalcSim()" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-2.5 py-2 text-xs text-zinc-900 font-mono focus:outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Gear Rental (₹)</label>
                <input type="number" id="sim-gear" value="15000" oninput="window.coraRecalcSim()" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-2.5 py-2 text-xs text-zinc-900 font-mono focus:outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Travel &amp; Food (₹)</label>
                <input type="number" id="sim-travel" value="10000" oninput="window.coraRecalcSim()" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-2.5 py-2 text-xs text-zinc-900 font-mono focus:outline-none">
            </div>
        </div>

        <!-- Calculated Live Result Box -->
        <div class="p-4 rounded-xl bg-zinc-900 text-white space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-400">Total Direct Costs:</span>
                <span class="font-mono font-bold" id="sim-calc-costs">₹60,000</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-400">Estimated Net Profit:</span>
                <span class="font-mono font-extrabold text-emerald-400" id="sim-calc-profit">₹60,000</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-zinc-400">Projected Margin:</span>
                <span class="font-extrabold text-emerald-400" id="sim-calc-margin">50.0%</span>
            </div>
        </div>

        <div id="sim-ai-verdict" class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200/60 text-xs text-emerald-900 font-medium">
            ✅ <strong>High Margin Deal (Go)</strong>: At a 50.0% margin, this project comfortably exceeds your studio's target threshold of 45%.
        </div>
    </div>
</div>


<!-- DRAWER: SUBSCRIPTIONS -->
<div id="cora-drawer-subscriptions" class="cora-slide-drawer">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Add Recurring Commitment</h3>
            <p class="text-[11px] text-zinc-500">Track recurring software, studio leases, or vendor contracts</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-7 h-7 rounded-lg hover:bg-zinc-200 text-zinc-500 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm">✕</button>
    </div>

    <form onsubmit="window.coraSubmitSubscription(event)" class="flex-1 overflow-y-auto p-5 space-y-4">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Subscription / Commitment Name</label>
            <input type="text" id="sub-name" placeholder="e.g. Canva Pro, Hostinger Server, Studio Power" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Amount (₹)</label>
                <input type="number" step="any" id="sub-amount" placeholder="1999" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 font-mono font-bold focus:outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Billing Frequency</label>
                <select id="sub-frequency" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
                    <option value="monthly">Monthly</option>
                    <option value="annual">Annual</option>
                    <option value="quarterly">Quarterly</option>
                </select>
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Category</label>
            <select id="sub-category" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
                <option value="Software & Tools">Software &amp; Tools</option>
                <option value="Rent & Facilities">Rent &amp; Facilities</option>
                <option value="Infrastructure">Infrastructure</option>
                <option value="Marketing">Marketing</option>
                <option value="Other">Other Overhead</option>
            </select>
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2">
            <button type="button" onclick="window.coraCloseAllDrawers()" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                Cancel
            </button>
            <button type="submit" id="btn-save-sub" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0">
                Track Subscription
            </button>
        </div>
    </form>
</div>


<!-- DRAWER: ACCOUNTANT PACK -->
<div id="cora-drawer-accountant-pack" class="cora-slide-drawer">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Export Accountant Pack</h3>
            <p class="text-[11px] text-zinc-500">Ready for CA review, GST filing, and bookkeeping</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-7 h-7 rounded-lg hover:bg-zinc-200 text-zinc-500 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm">✕</button>
    </div>

    <div class="flex-1 overflow-y-auto p-5 space-y-4">
        <div class="p-4 rounded-xl bg-zinc-50 border border-zinc-200 space-y-2 text-xs">
            <div class="font-bold text-zinc-900">What's in the export pack:</div>
            <ul class="space-y-1 text-zinc-600 list-disc list-inside">
                <li>Complete Master Ledger CSV with date, type, client, and tax categories</li>
                <li>Invoice Aging &amp; Receivables registry</li>
                <li>Recurring commitments and vendor breakdown</li>
                <li>GST Summary calculations (CGST, SGST, Net payable)</li>
            </ul>
        </div>

        <div class="space-y-2">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Export Format</label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="window.coraExportData('csv')" class="p-3 rounded-xl border border-zinc-300 hover:border-zinc-900 bg-white font-bold text-xs text-zinc-900 flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                    <span>📄 Download CSV</span>
                </button>
                <button type="button" onclick="window.print()" class="p-3 rounded-xl border border-zinc-300 hover:border-zinc-900 bg-white font-bold text-xs text-zinc-900 flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                    <span>🖨️ Print Report</span>
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ════════════════════════════════════════════════════════
     CLIENT-SIDE CONTROLLERS
     ════════════════════════════════════════════════════════ -->
<script>
(function() {
    'use strict';

    const ajaxUrl = '<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>';
    const nonce   = '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>';

    /* ── Tab Switcher Controller ── */
    window.coraSwitchFinTab = function(tabId) {
        document.querySelectorAll('.cora-fin-tab-panel').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.cora-fin-pill-tab').forEach(el => {
            el.classList.remove('active', 'bg-zinc-950', 'text-white');
            el.classList.add('text-zinc-600');
        });

        const targetPanel = document.getElementById(tabId);
        if (targetPanel) {
            targetPanel.classList.remove('hidden');
        }

        const targetBtn = document.getElementById('tab-btn-' + tabId);
        if (targetBtn) {
            targetBtn.classList.add('active', 'bg-zinc-950', 'text-white');
            targetBtn.classList.remove('text-zinc-600');
        }
    };

    /* ── Floating Ask Cora Copilot Controller ── */
    window.coraOpenCopilot = function() {
        window.coraCloseFinPopover();
        const win = document.getElementById('cora-fin-copilot-window');
        const bar = document.getElementById('cora-fin-copilot-bar');
        if (win) {
            win.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            win.classList.add('active', 'opacity-100', 'scale-100', 'pointer-events-auto');
        }
        if (bar) {
            bar.classList.add('hidden-bar');
        }
        setTimeout(() => {
            const inp = document.getElementById('cora-fin-copilot-chat-input');
            if (inp) inp.focus();
        }, 100);
    };

    window.coraCloseCopilot = function() {
        const win = document.getElementById('cora-fin-copilot-window');
        const bar = document.getElementById('cora-fin-copilot-bar');
        if (win) {
            win.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            win.classList.remove('active', 'opacity-100', 'scale-100', 'pointer-events-auto');
        }
        if (bar) {
            bar.classList.remove('hidden-bar');
        }
    };

    window.coraSubmitCopilotPrompt = function(promptText) {
        const input = document.getElementById('cora-fin-copilot-chat-input');
        if (input) {
            input.value = promptText;
            window.coraSendCopilotChat();
        }
    };

    window.coraSendCopilotChat = function() {
        const input = document.getElementById('cora-fin-copilot-chat-input');
        const query = input ? input.value.trim() : '';
        if (!query) return;

        const chatPane = document.getElementById('cora-fin-copilot-chat');
        const dashboard = document.getElementById('cora-fin-copilot-dashboard');
        const sendBtn = document.getElementById('cora-fin-copilot-send-btn');

        if (dashboard) dashboard.classList.add('hidden');
        if (chatPane) {
            chatPane.classList.remove('hidden');
            
            // Append user bubble
            const userBubble = document.createElement('div');
            userBubble.className = 'flex justify-end';
            userBubble.innerHTML = `<div class="bg-zinc-950 text-white rounded-2xl rounded-tr-sm px-4 py-2 text-xs max-w-[80%]">${query}</div>`;
            chatPane.appendChild(userBubble);

            // Append loading bubble
            const aiBubble = document.createElement('div');
            aiBubble.className = 'flex justify-start';
            aiBubble.id = 'copilot-temp-ai-bubble';
            aiBubble.innerHTML = `<div class="bg-zinc-100 text-zinc-700 rounded-2xl rounded-tl-sm px-4 py-2 text-xs max-w-[85%] animate-pulse">Analyzing ledger records and cash forecast...</div>`;
            chatPane.appendChild(aiBubble);
            chatPane.scrollTop = chatPane.scrollHeight;
        }

        if (input) input.value = '';
        if (sendBtn) { sendBtn.disabled = true; }

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_finance_ask_cora',
                security: nonce,
                query: query,
            })
        })
        .then(r => r.json())
        .then(res => {
            if (sendBtn) sendBtn.disabled = false;
            const tempBubble = document.getElementById('copilot-temp-ai-bubble');
            if (tempBubble) tempBubble.remove();

            if (chatPane && res.success && res.data) {
                let formatted = res.data.answer
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/\n\n/g, '<br><br>')
                    .replace(/\n/g, '<br>');

                const finalAiBubble = document.createElement('div');
                finalAiBubble.className = 'flex justify-start';
                
                let actionBtnHtml = '';
                if (res.data.action_chip) {
                    const chip = res.data.action_chip;
                    actionBtnHtml = `<div class="pt-2"><button type="button" onclick="window.coraHandleCopilotAction('${chip.action}', '${chip.target}')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 inline-flex items-center gap-1.5">${chip.text} →</button></div>`;
                }

                finalAiBubble.innerHTML = `<div class="bg-zinc-50 border border-zinc-200 text-zinc-800 rounded-2xl rounded-tl-sm p-3.5 text-xs max-w-[85%] space-y-2 leading-relaxed">
                    <div class="font-bold text-zinc-950 flex items-center gap-1.5"><span>Cora Co-founder</span></div>
                    <div>${formatted}</div>
                    ${actionBtnHtml}
                </div>`;
                chatPane.appendChild(finalAiBubble);
                chatPane.scrollTop = chatPane.scrollHeight;
            }
        })
        .catch(() => {
            if (sendBtn) sendBtn.disabled = false;
            const tempBubble = document.getElementById('copilot-temp-ai-bubble');
            if (tempBubble) {
                tempBubble.innerHTML = `<div class="bg-red-50 text-red-700 rounded-2xl p-3 text-xs">Error communicating with financial AI engine.</div>`;
            }
        });
    };

    window.coraHandleCopilotAction = function(action, target) {
        window.coraCloseCopilot();
        if (action === 'draft_followup') {
            window.coraDraftFollowUp(target);
        } else if (action === 'switch_tab') {
            window.coraSwitchFinTab(target);
        } else if (action === 'open_simulator') {
            window.coraOpenDrawer('project-sim');
        } else if (action === 'open_recurring_modal') {
            window.coraOpenDrawer('subscriptions');
        }
    };

    // Allow Enter key to submit inside the copilot input
    document.addEventListener('DOMContentLoaded', function() {
        const inp = document.getElementById('cora-fin-copilot-chat-input');
        if (inp) {
            inp.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    window.coraSendCopilotChat();
                }
            });
        }
    });

    /* ── Popover Controller ── */
    window.toggleFinancialActionMenu = function(e) {
        if (e && e.stopPropagation) e.stopPropagation();
        const popover = document.getElementById('cora-fin-action-popover');
        if (!popover) return;
        
        const isHidden = popover.parentElement.classList.contains('hidden');
        if (isHidden) {
            popover.parentElement.classList.remove('hidden');
            const targetBtn = e ? e.currentTarget : null;
            if (targetBtn) {
                const rect = targetBtn.getBoundingClientRect();
                popover.style.top = (rect.bottom + window.scrollY + 4) + 'px';
                popover.style.right = (window.innerWidth - rect.right) + 'px';
                popover.style.position = 'absolute';
            }
        } else {
            popover.parentElement.classList.add('hidden');
        }
    };

    window.coraCloseFinPopover = function() {
        const popover = document.getElementById('cora-fin-action-popover');
        if (popover && popover.parentElement) {
            popover.parentElement.classList.add('hidden');
        }
    };

    document.addEventListener('click', function(e) {
        const pop = document.getElementById('cora-fin-action-popover');
        if (pop && !pop.contains(e.target)) {
            window.coraCloseFinPopover();
        }
    });

    /* ── Slide Drawers Controller ── */
    window.coraOpenDrawer = function(drawerId) {
        window.coraCloseFinPopover();
        const backdrop = document.getElementById('cora-fin-drawer-backdrop');
        const drawer = document.getElementById('cora-drawer-' + drawerId);
        if (backdrop) backdrop.classList.add('active');
        if (drawer) drawer.classList.add('open');
    };

    window.coraCloseAllDrawers = function() {
        const backdrop = document.getElementById('cora-fin-drawer-backdrop');
        if (backdrop) backdrop.classList.remove('active');
        document.querySelectorAll('.cora-slide-drawer').forEach(d => d.classList.remove('open'));
    };

    /* ── Filter Receivables ── */
    window.coraFilterReceivables = function(query) {
        const q = (query || '').toLowerCase();
        const rows = document.querySelectorAll('#cora-receivables-tbody tr');
        rows.forEach(r => {
            const text = r.innerText.toLowerCase();
            r.style.display = text.includes(q) ? '' : 'none';
        });
    };

    /* ── Draft Payment Follow-up Controller ── */
    window.coraDraftFollowUp = function(invoiceId) {
        window.coraOpenDrawer('followup');
        document.getElementById('followup-invoice-id').value = invoiceId || '';
        window.coraChangeFollowUpTone('polite');
    };

    window.coraChangeFollowUpTone = function(tone) {
        const invId = document.getElementById('followup-invoice-id').value;
        ['polite', 'firm', 'urgent'].forEach(t => {
            const btn = document.getElementById('tone-' + t);
            if (btn) {
                if (t === tone) {
                    btn.classList.add('bg-zinc-950', 'text-white');
                    btn.classList.remove('bg-zinc-100', 'text-zinc-700');
                } else {
                    btn.classList.remove('bg-zinc-950', 'text-white');
                    btn.classList.add('bg-zinc-100', 'text-zinc-700');
                }
            }
        });

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_finance_generate_followup',
                security: nonce,
                invoice_id: invId,
                tone: tone,
            })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && res.data) {
                document.getElementById('followup-recipient').value = res.data.recipient || '';
                document.getElementById('followup-subject').value = res.data.subject || '';
                document.getElementById('followup-body').value = res.data.body || '';
            }
        });
    };

    window.coraSendFollowUp = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        const btn = document.getElementById('btn-send-followup');
        if (btn) { btn.disabled = true; btn.innerText = 'Sending…'; }

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_finance_send_followup',
                security: nonce,
                recipient: document.getElementById('followup-recipient').value,
                subject: document.getElementById('followup-subject').value,
                body: document.getElementById('followup-body').value,
                invoice_id: document.getElementById('followup-invoice-id').value,
            })
        })
        .then(r => r.json())
        .then(res => {
            if (btn) { btn.disabled = false; btn.innerText = 'Send Follow-up Email'; }
            if (res.success) {
                window.coraCloseAllDrawers();
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Follow-up email dispatched.', 'success');
            } else {
                if (window.coraShowToast) window.coraShowToast(res.data?.message || 'Failed to send follow-up.', 'error');
            }
        });
    };

    /* ── Mark Invoice Paid Controller ── */
    window.coraMarkInvoicePaid = function(invoiceId) {
        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_update_invoice_status',
                security: nonce,
                invoice_id: invoiceId,
                status: 'paid'
            })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast('Invoice marked Paid and reconciled into Master Ledger.', 'success');
                setTimeout(() => location.reload(), 800);
            }
        });
    };

    /* ── Add Expense Controller ── */
    window.coraSubmitExpense = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        const btn = document.getElementById('btn-save-expense');
        if (btn) { btn.disabled = true; btn.innerText = 'Saving…'; }

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_finance_record_expense',
                security: nonce,
                description: document.getElementById('exp-description').value,
                amount: document.getElementById('exp-amount').value,
                date: document.getElementById('exp-date').value,
                category: document.getElementById('exp-category').value,
                is_recurring: document.getElementById('exp-is-recurring').checked ? '1' : '0'
            })
        })
        .then(r => r.json())
        .then(res => {
            if (btn) { btn.disabled = false; btn.innerText = 'Save Expense'; }
            if (res.success) {
                window.coraCloseAllDrawers();
                if (window.coraShowToast) window.coraShowToast('Expense recorded successfully.', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                if (window.coraShowToast) window.coraShowToast(res.data?.message || 'Error logging expense.', 'error');
            }
        });
    };

    /* ── Create Invoice Controller ── */
    window.coraSubmitInvoice = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        const btn = document.getElementById('btn-save-invoice');
        if (btn) { btn.disabled = true; btn.innerText = 'Creating…'; }

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_create_invoice',
                security: nonce,
                client_name: document.getElementById('inv-client-name').value,
                client_email: document.getElementById('inv-client-email').value,
                package_name: document.getElementById('inv-package-name').value,
                total_amount: document.getElementById('inv-total-amount').value,
                due_date: document.getElementById('inv-due-date').value,
            })
        })
        .then(r => r.json())
        .then(res => {
            if (btn) { btn.disabled = false; btn.innerText = 'Create Invoice'; }
            if (res.success) {
                window.coraCloseAllDrawers();
                if (window.coraShowToast) window.coraShowToast('Invoice created and added to Receivables.', 'success');
                setTimeout(() => location.reload(), 700);
            }
        });
    };

    /* ── Record Income Controller ── */
    window.coraSubmitIncome = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        const btn = document.getElementById('btn-save-income');
        if (btn) { btn.disabled = true; btn.innerText = 'Logging…'; }

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_finance_record_income',
                security: nonce,
                client_name: document.getElementById('inc-client-name').value,
                amount: document.getElementById('inc-amount').value,
                date: document.getElementById('inc-date').value,
                invoice_id: document.getElementById('inc-invoice-id').value,
            })
        })
        .then(r => r.json())
        .then(res => {
            if (btn) { btn.disabled = false; btn.innerText = 'Log Payment'; }
            if (res.success) {
                window.coraCloseAllDrawers();
                if (window.coraShowToast) window.coraShowToast('Payment logged and cash balance updated.', 'success');
                setTimeout(() => location.reload(), 700);
            }
        });
    };

    /* ── Project Deal Simulator Controller ── */
    window.coraRecalcSim = function() {
        const rev = parseFloat(document.getElementById('sim-revenue').value) || 0;
        const contractor = parseFloat(document.getElementById('sim-contractor').value) || 0;
        const gear = parseFloat(document.getElementById('sim-gear').value) || 0;
        const travel = parseFloat(document.getElementById('sim-travel').value) || 0;

        const totalCosts = contractor + gear + travel;
        const profit = Math.max(0, rev - totalCosts);
        const margin = rev > 0 ? ((profit / rev) * 100).toFixed(1) : 0;

        document.getElementById('sim-calc-costs').innerText = '₹' + totalCosts.toLocaleString('en-IN');
        document.getElementById('sim-calc-profit').innerText = '₹' + profit.toLocaleString('en-IN');
        document.getElementById('sim-calc-margin').innerText = margin + '%';

        const verdictBox = document.getElementById('sim-ai-verdict');
        if (verdictBox) {
            if (margin >= 50) {
                verdictBox.className = 'p-3.5 rounded-xl bg-emerald-50 border border-emerald-200/60 text-xs text-emerald-900 font-medium';
                verdictBox.innerHTML = `✅ <strong>High Margin Deal (Go)</strong>: At ${margin}% margin (₹${profit.toLocaleString('en-IN')} profit), this project is highly profitable.`;
            } else if (margin >= 30) {
                verdictBox.className = 'p-3.5 rounded-xl bg-amber-50 border border-amber-200/60 text-xs text-amber-900 font-medium';
                verdictBox.innerHTML = `⚠️ <strong>Moderate Margin (Caution)</strong>: ${margin}% margin. Consider adding a 10% buffer on gear or crew day rates.`;
            } else {
                verdictBox.className = 'p-3.5 rounded-xl bg-red-50 border border-red-200/60 text-xs text-red-900 font-medium';
                verdictBox.innerHTML = `🛑 <strong>Low Margin Risk (Re-evaluate)</strong>: ${margin}% margin leaves little buffer for studio overhead or scope revisions.`;
            }
        }
    };

    /* ── Subscriptions Submission Controller ── */
    window.coraSubmitSubscription = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        const btn = document.getElementById('btn-save-sub');
        if (btn) { btn.disabled = true; btn.innerText = 'Saving…'; }

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_finance_confirm_recurring',
                security: nonce,
                name: document.getElementById('sub-name').value,
                amount: document.getElementById('sub-amount').value,
                frequency: document.getElementById('sub-frequency').value,
                category: document.getElementById('sub-category').value,
            })
        })
        .then(r => r.json())
        .then(res => {
            if (btn) { btn.disabled = false; btn.innerText = 'Track Subscription'; }
            if (res.success) {
                window.coraCloseAllDrawers();
                if (window.coraShowToast) window.coraShowToast('Subscription added to recurring tracking.', 'success');
                setTimeout(() => location.reload(), 700);
            }
        });
    };

    /* ── Forecast Horizon Switcher ── */
    window.coraSwitchForecastHorizon = function(days) {
        [30, 60, 90].forEach(d => {
            const btn = document.getElementById('btn-fc-' + d);
            if (btn) {
                if (d === days) {
                    btn.classList.add('bg-zinc-950', 'text-white');
                    btn.classList.remove('text-zinc-600');
                } else {
                    btn.classList.remove('bg-zinc-950', 'text-white');
                    btn.classList.add('text-zinc-600');
                }
            }
        });

        const multiplier = days / 30;
        const baseIn = <?php echo floatval( $expected_in ); ?>;
        const baseOut = <?php echo floatval( $expected_out ); ?>;
        const cash = <?php echo floatval( $available_cash ); ?>;

        const inVal = baseIn + ((multiplier - 1) * 140000);
        const outVal = baseOut * multiplier;
        const projVal = cash + inVal - outVal;

        document.getElementById('fc-val-in').innerText = '+₹' + Math.round(inVal).toLocaleString('en-IN');
        document.getElementById('fc-val-out').innerText = '-₹' + Math.round(outVal).toLocaleString('en-IN');
        document.getElementById('fc-val-proj').innerText = '₹' + Math.round(projVal).toLocaleString('en-IN');
    };

    /* ── Export Data Controller ── */
    window.coraExportData = function(format) {
        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_finance_export_pack',
                security: nonce,
                format: format,
            })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && res.data.csv_data) {
                const blob = new Blob([res.data.csv_data], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', res.data.filename || 'Cora_Finance_Export.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                if (window.coraShowToast) window.coraShowToast('Accountant CSV Export downloaded.', 'success');
            }
        });
    };

    /* ── Live Refresh Controller ── */
    window.coraRefreshFinancials = function() {
        if (window.coraShowToast) window.coraShowToast('Refreshing financial intelligence metrics...', 'info');
        setTimeout(() => location.reload(), 400);
    };

})();
</script>
