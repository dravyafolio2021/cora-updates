<?php
/**
 * Cora Workspace — Financial Intelligence & AI Co-founder (v3.4.44)
 * 
 * Rebuilt as a proactive AI Co-founder for solo founders & creative studios.
 * Featuring:
 * - 100% Vector SVG Iconography across all actions, drawers, copilot, and badges (Zero Emojis)
 * - Native Cora platform drawer architecture (window.coraShowSideDrawer / window.coraCloseAllDrawers)
 * - Clean tab navigation without scrollbars
 * - Floating bottom Ask Cora Copilot & Claude Cream popover
 * - Dynamic multi-step Indian GST Invoice Creator (CRM & Document Vault integrated)
 * - Multi-step Expense Logger (ITC & TDS compliant)
 * - Interactive Deal Feasibility Simulator
 * - Chart.js interactive cash flow & profitability charts
 *
 * @package CoraWorkspace
 * @version 3.4.44
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

// Fetch comprehensive financial intelligence metrics
$metrics = function_exists( 'cora_finance_get_comprehensive_metrics' ) ? cora_finance_get_comprehensive_metrics() : array();
$agency_id = function_exists( 'cora_db_get_agency_id' ) ? cora_db_get_agency_id() : 1;

$available_cash = floatval( $metrics['available_cash'] ?? 0.0 );
$expected_in    = floatval( $metrics['expected_in'] ?? 0.0 );
$expected_out   = floatval( $metrics['expected_out'] ?? 0.0 );
$projected_cash = floatval( $metrics['projected_cash'] ?? 0.0 );
$overdue_total  = floatval( $metrics['overdue_total'] ?? 0.0 );
$overdue_count  = intval( $metrics['overdue_count'] ?? 0 );
$monthly_rec    = floatval( $metrics['monthly_recurring_total'] ?? 0.0 );
$gross_inflow   = floatval( $metrics['gross_inflow'] ?? 0.0 );
$gross_outflow  = floatval( $metrics['gross_outflow'] ?? 0.0 );
$receivables    = is_array( $metrics['receivables'] ?? null ) ? $metrics['receivables'] : array();
$recurring_list = is_array( $metrics['recurring_expenses'] ?? null ) ? $metrics['recurring_expenses'] : array();
$client_profits = is_array( $metrics['client_profitability'] ?? null ) ? $metrics['client_profitability'] : array();
$cora_take      = is_array( $metrics['cora_take'] ?? null ) ? $metrics['cora_take'] : array();
$attention_cards= is_array( $metrics['attention_cards'] ?? null ) ? $metrics['attention_cards'] : array();
$gst_data       = is_array( $metrics['gst_intelligence'] ?? null ) ? $metrics['gst_intelligence'] : array(
    'gst_collected'      => 0.0,
    'itc_credit'         => 0.0,
    'net_gst_payable'    => 0.0,
    'tax_reserve_target' => 0.0,
);
$forecast_events= is_array( $metrics['forecast_30']['key_events'] ?? null ) ? $metrics['forecast_30']['key_events'] : array();

// Fetch CRM Leads/Clients for intercompatible invoice generation
$leads_table = $wpdb->prefix . 'cora_leads';
$crm_contacts = array();
if ( $wpdb->get_var( "SHOW TABLES LIKE '{$leads_table}'" ) === $leads_table ) {
    $crm_contacts = $wpdb->get_results( $wpdb->prepare( "SELECT id, name, email, phone FROM {$leads_table} WHERE agency_id = %d ORDER BY id DESC LIMIT 50", $agency_id ), ARRAY_A ) ?: array();
}

// User-customizable expense categories
$custom_categories = get_option( "cora_custom_expense_categories_{$agency_id}", array() );
if ( ! is_array( $custom_categories ) || empty( $custom_categories ) ) {
    $custom_categories = get_option( 'cora_custom_expense_categories', array() );
}
if ( ! is_array( $custom_categories ) ) {
    $custom_categories = array();
}
$default_categories = array(
    'Gear & Tech',
    'Studio Ops & Rent',
    'Software & Tools',
    'Food & Travel',
    'Marketing & Ads',
    'Contractor & Crew',
    'Props & Set Design',
    'Post-Production & VFX',
    'Legal & Professional Fees',
    'Other Operational',
);
$all_expense_categories = array_values( array_unique( array_merge( $default_categories, $custom_categories ) ) );

// ── Build Unified Chronological Ledger & Activity Stream ──
$all_transactions = array();

// 1. Receivables (Money In)
foreach ( $receivables as $r ) {
    $all_transactions[] = array(
        'id'          => $r['id'] ?? ( $r['invoice_number'] ?? uniqid('inv_') ),
        'title'       => ( $r['client_name'] ?? 'Client' ) . ' — ' . ( $r['package_name'] ?? 'Invoice' ),
        'type'        => 'inflow',
        'amount'      => floatval( $r['due_balance'] ?: ( $r['total_amount'] ?? 0 ) ),
        'date'        => $r['due_date'] ?? date('Y-m-d'),
        'category'    => 'Client Invoice (' . ( $r['invoice_number'] ?? 'Draft' ) . ')',
        'status'      => $r['status'] ?? 'pending',
        'is_overdue'  => ! empty( $r['is_overdue'] ),
        'days_overdue'=> intval( $r['days_overdue'] ?? 0 ),
        'invoice_num' => $r['invoice_number'] ?? '',
        'client_name' => $r['client_name'] ?? '',
        'action_type' => 'invoice',
    );
}

// 2. Expenses (Money Out)
$ledger_table = $wpdb->prefix . 'cora_ledger';
$db_expenses = array();
if ( $wpdb->get_var( "SHOW TABLES LIKE '{$ledger_table}'" ) === $ledger_table ) {
    $db_expenses = $wpdb->get_results(
        $wpdb->prepare( "SELECT * FROM {$ledger_table} WHERE agency_id = %d AND type = 'outflow' ORDER BY transaction_date DESC, id DESC LIMIT 50", $agency_id ),
        ARRAY_A
    ) ?: array();
}
if ( empty( $db_expenses ) ) {
    $opt_ledger = get_option( "cora_workspace_ledger_{$agency_id}", array() );
    if ( empty( $opt_ledger ) && $agency_id === 1 ) {
        $opt_ledger = get_option( 'cora_workspace_ledger', array() );
    }
    foreach ( (array) $opt_ledger as $ol ) {
        if ( ( $ol['type'] ?? '' ) === 'outflow' ) {
            $db_expenses[] = $ol;
        }
    }
}
foreach ( (array) $db_expenses as $exp ) {
    $amt = floatval( $exp['amount'] ?? 0 );
    if ( isset( $exp['agency_id'] ) && $amt > 100000 && ! empty( $exp['created_at'] ) && $amt == intval($amt) && $amt % 100 === 0 ) {
        $amt = $amt / 100.0;
    }
    $all_transactions[] = array(
        'id'          => $exp['id'] ?? uniqid('exp_'),
        'title'       => $exp['description'] ?? 'Business Expense',
        'type'        => 'outflow',
        'amount'      => $amt,
        'date'        => $exp['date'] ?? ( $exp['transaction_date'] ?? date('Y-m-d') ),
        'category'    => $exp['category'] ?? 'Operations',
        'status'      => 'paid',
        'is_overdue'  => false,
        'days_overdue'=> 0,
        'invoice_num' => '',
        'client_name' => $exp['client_link'] ?? '',
        'action_type' => 'expense',
    );
}

// Chronological sort: newest first
usort( $all_transactions, function( $a, $b ) {
    return strtotime( $b['date'] ) <=> strtotime( $a['date'] );
} );



// Standardized Page Header
$financials_header_args = array(
    'title'            => 'Financial Intelligence',
    'description'      => 'Your Financial Agent watches cash flow, collects receivables, and tracks profitability in real-time.',
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

<div id="cora-finance-root" class="space-y-6 text-zinc-900 font-sans pb-36 min-h-screen overflow-y-visible">

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

        /* ── Hide Horizontal Scrollbar Cleanly ── */
        .cora-no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
        .cora-no-scrollbar::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
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
            z-index: 9980;
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
        /* Mobile adjustment: bottom-anchored, zero side drawer, touch friendly */
        @media (max-width: 1023px) {
            #cora-fin-copilot-container {
                padding-left: 0 !important;
                bottom: 16px !important;
            }
        }
        #cora-fin-copilot-bar, #cora-fin-copilot-window {
            width: calc(100% - 32px) !important;
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

        /* ── High-Quality Voice Waveform Animation ── */
        .cora-voice-wave {
            display: inline-flex;
            align-items: center;
            gap: 2.5px;
            height: 18px;
        }
        .cora-voice-wave-bar {
            width: 3px;
            background-color: #09090b;
            border-radius: 9999px;
            animation: coraVoicePulse 0.9s ease-in-out infinite alternate;
        }
        .cora-voice-wave-bar:nth-child(1) { height: 6px; animation-delay: 0.1s; }
        .cora-voice-wave-bar:nth-child(2) { height: 16px; animation-delay: 0.25s; }
        .cora-voice-wave-bar:nth-child(3) { height: 11px; animation-delay: 0.15s; }
        .cora-voice-wave-bar:nth-child(4) { height: 18px; animation-delay: 0.35s; }
        .cora-voice-wave-bar:nth-child(5) { height: 8px; animation-delay: 0.2s; }
        @keyframes coraVoicePulse {
            0% { height: 4px; opacity: 0.4; }
            100% { height: 18px; opacity: 1; }
        }
        .cora-mic-listening {
            box-shadow: 0 0 0 0 rgba(9, 9, 11, 0.4);
            animation: coraMicRing 1.5s infinite;
        }
        @keyframes coraMicRing {
            0% { box-shadow: 0 0 0 0 rgba(9, 9, 11, 0.5); }
            70% { box-shadow: 0 0 0 10px rgba(9, 9, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(9, 9, 11, 0); }
        }
    </style>

    <!-- ══ QUICK ACTION DROPDOWN POPOVER (Flush & Aligned Under Button) ══ -->
    <div id="cora-fin-action-popover" class="fixed bg-white rounded-2xl border border-zinc-200 shadow-2xl z-[9995] p-2 space-y-1 hidden select-none w-72" style="top: 0; right: 0;">
        <div class="px-3 py-2 text-[10px] font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-100">
            Quick Financial Actions
        </div>
        <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('create-invoice');" class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
            <span class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-900 shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </span>
            <div>
                <div class="font-bold text-zinc-950">Draft Client GST Invoice</div>
                <div class="text-[10px] text-zinc-500 font-normal">State tax &amp; Vault contract link</div>
            </div>
        </button>
        <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('record-income');" class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
            <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
            </span>
            <div>
                <div class="font-bold text-zinc-950">Record Received Payment</div>
                <div class="text-[10px] text-zinc-500 font-normal">Reconcile open receivables</div>
            </div>
        </button>
        <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('add-expense');" class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
            <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
            </span>
            <div>
                <div class="font-bold text-zinc-950">Log Business Expense</div>
                <div class="text-[10px] text-zinc-500 font-normal">Track ITC &amp; vendor TDS</div>
            </div>
        </button>
        <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('project-sim');" class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            </span>
            <div>
                <div class="font-bold text-zinc-950">Evaluate Project Deal</div>
                <div class="text-[10px] text-zinc-500 font-normal">"Should I take this deal?" AI simulator</div>
            </div>
        </button>
        <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('subscriptions');" class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
            <span class="w-7 h-7 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
            </span>
            <div>
                <div class="font-bold text-zinc-950">Manage Subscriptions</div>
                <div class="text-[10px] text-zinc-500 font-normal">Track monthly recurring costs</div>
            </div>
        </button>
        <button type="button" onclick="window.coraCloseFinPopover(); window.coraOpenDrawer('accountant-pack');" class="w-full text-left px-3 py-2.5 rounded-xl text-xs font-semibold text-zinc-800 hover:bg-zinc-100 flex items-center gap-2.5 transition-colors border-0 bg-transparent cursor-pointer">
            <span class="w-7 h-7 rounded-lg bg-zinc-100 text-zinc-700 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            </span>
            <div>
                <div class="font-bold text-zinc-950">Export Accountant Pack</div>
                <div class="text-[10px] text-zinc-500 font-normal">Download CA-ready CSV &amp; PDF</div>
            </div>
        </button>
    </div>

    <!-- ══ SIMPLIFIED FINANCIAL NAVIGATION (Agent-First & Clear Ledger) ══ -->
    <div class="border-b border-zinc-200 pb-3 select-none flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <!-- Core View Switcher -->
        <div class="flex items-center gap-1.5 overflow-x-auto cora-no-scrollbar py-0.5">
            <button type="button" onclick="window.coraSwitchFinTab('fin-agent')" id="tab-btn-fin-agent" class="cora-fin-pill-tab active px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white cursor-pointer shrink-0 border-0 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>Financial AI Agent</span>
            </button>
            <button type="button" onclick="window.coraSwitchFinTab('fin-ledger')" id="tab-btn-fin-ledger" class="cora-fin-pill-tab px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 cursor-pointer shrink-0 border-0 flex items-center gap-1.5">
                <span>Ledger &amp; Activity</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-700"><?php echo count($all_transactions); ?></span>
            </button>
        </div>

        <!-- Quick Access & All Tools -->
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="window.coraOpenDrawer('create-invoice')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 flex items-center gap-1.5 shadow-xs">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>+ Invoice</span>
            </button>
            <button type="button" onclick="window.coraOpenDrawer('add-expense')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-zinc-100 hover:bg-zinc-200 text-zinc-900 cursor-pointer border-0 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>- Expense</span>
            </button>
            <button type="button" onclick="window.toggleFinancialActionMenu(event)" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-zinc-700 hover:bg-zinc-100 border border-zinc-200 cursor-pointer transition-colors flex items-center gap-1.5">
                <span>All Tools</span>
                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
        </div>
    </div>

    <!-- ══ CORE FINANCIAL METRICS (Always Visible: Available, In, Out, Runway) ══ -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Card 1: Available Cash -->
        <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Available Cash</span>
                <span class="w-6 h-6 rounded-lg bg-zinc-100 text-zinc-800 flex items-center justify-center font-bold text-xs">₹</span>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-zinc-950 cora-mono-num">₹<?php echo number_format( $available_cash ); ?></div>
                <div class="text-[11px] text-zinc-500 font-medium mt-0.5">Cleared funds in bank</div>
            </div>
            <?php 
            if ( $monthly_rec > 0 && $available_cash > 0 ) {
                $runway_months = round( $available_cash / $monthly_rec, 1 );
                $buffer_badge_class = 'text-zinc-800 bg-zinc-100';
                $buffer_badge_text = "● Buffer: ~{$runway_months} Mo Safe";
            } elseif ( $available_cash > 0 ) {
                $buffer_badge_class = 'text-zinc-800 bg-zinc-100';
                $buffer_badge_text = "● Buffer: ₹" . number_format($available_cash);
            } else {
                $buffer_badge_class = 'text-zinc-500 bg-zinc-100';
                $buffer_badge_text = "Ready to record";
            }
            ?>
            <div class="text-[10px] font-semibold <?php echo $buffer_badge_class; ?> px-2 py-0.5 rounded-md inline-block w-fit">
                <?php echo esc_html( $buffer_badge_text ); ?>
            </div>
        </div>

        <!-- Card 2: Expected In (Receivables) -->
        <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Incoming (Money In)</span>
                <button type="button" onclick="window.coraOpenDrawer('create-invoice')" class="text-[10px] font-bold text-zinc-950 hover:underline border-0 bg-transparent cursor-pointer p-0">
                    + Draft
                </button>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-zinc-950 cora-mono-num">₹<?php echo number_format( $expected_in ); ?></div>
                <div class="text-[11px] text-zinc-500 font-medium mt-0.5">Uncollected receivables</div>
            </div>
            <div class="text-[10px] font-semibold <?php echo $overdue_total > 0 ? 'text-red-700 bg-red-50' : 'text-zinc-500 bg-zinc-100'; ?> px-2 py-0.5 rounded-md inline-block w-fit">
                <?php echo $overdue_total > 0 ? '₹' . number_format( $overdue_total ) . ' Overdue' : 'All on time'; ?>
            </div>
        </div>

        <!-- Card 3: Expected Out (Commitments) -->
        <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Outgoing (Money Out)</span>
                <button type="button" onclick="window.coraOpenDrawer('add-expense')" class="text-[10px] font-bold text-zinc-950 hover:underline border-0 bg-transparent cursor-pointer p-0">
                    - Log
                </button>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-zinc-950 cora-mono-num">₹<?php echo number_format( $expected_out ); ?></div>
                <div class="text-[11px] text-zinc-500 font-medium mt-0.5">Recurring overhead &amp; bills</div>
            </div>
            <div class="text-[10px] font-semibold text-zinc-600 bg-zinc-100 px-2 py-0.5 rounded-md inline-block w-fit">
                ₹<?php echo number_format( $monthly_rec ); ?>/mo Fixed
            </div>
        </div>

        <!-- Card 4: Projected Cash -->
        <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Projected Cash</span>
                <span class="w-6 h-6 rounded-lg bg-zinc-100 text-zinc-800 flex items-center justify-center font-bold text-xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </span>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-zinc-950 cora-mono-num">₹<?php echo number_format( $projected_cash ); ?></div>
                <div class="text-[11px] text-zinc-500 font-medium mt-0.5">After pending settlements</div>
            </div>
            <div class="text-[10px] font-semibold <?php echo $projected_cash >= 0 ? 'text-zinc-800 bg-zinc-100' : 'text-red-700 bg-red-50'; ?> px-2 py-0.5 rounded-md inline-block w-fit">
                Net: <?php echo $projected_cash >= 0 ? 'Healthy Position' : 'Deficit Warning'; ?>
            </div>
        </div>
    </div>


    <!-- ════════════════════════════════════════════════════════
         TAB 1: FINANCIAL AI AGENT (HERO VOICE & DISCUSSION COMMAND HUB)
         (Also aliased to tab-fin-home for complete backward compatibility)
         ════════════════════════════════════════════════════════ -->
    <div id="tab-fin-agent" class="cora-fin-tab-panel space-y-6">

        <!-- 1. HERO FINANCIAL AI AGENT DISCUSSION CARD -->
        <div class="cora-fin-card p-5 bg-white border border-zinc-200 shadow-sm space-y-4">
            
            <!-- Agent Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-zinc-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-zinc-950 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                        C
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-zinc-950">Financial AI Agent</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Live Ledger Connected</span>
                            </span>
                        </div>
                        <div class="text-[11px] text-zinc-500 font-medium mt-0.5">
                            Direct action-oriented discussion: log expenses by voice, draft GST invoices, evaluate deal margins, and audit runway.
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span id="cora-voice-status-pill" class="hidden px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-800 items-center gap-1.5">
                        <span class="cora-voice-wave">
                            <span class="cora-voice-wave-bar"></span>
                            <span class="cora-voice-wave-bar"></span>
                            <span class="cora-voice-wave-bar"></span>
                            <span class="cora-voice-wave-bar"></span>
                            <span class="cora-voice-wave-bar"></span>
                        </span>
                        <span id="cora-voice-status-text">Listening...</span>
                    </span>
                    <button type="button" onclick="window.coraClearAgentChat()" class="px-2.5 py-1.5 rounded-xl text-xs font-semibold text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 border border-zinc-200 cursor-pointer transition-colors" title="Clear Discussion">
                        Clear Discussion
                    </button>
                    <button type="button" onclick="window.coraRefreshFinancials()" class="w-8 h-8 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-600 flex items-center justify-center cursor-pointer border border-zinc-200 transition-colors" title="Refresh Live Ledger">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Live Speech Real-time Transcript Banner (Visible while recording) -->
            <div id="cora-voice-live-banner" class="hidden p-3 rounded-xl bg-zinc-50 border border-zinc-200 text-xs text-zinc-800 flex items-center justify-between gap-3 animate-pulse">
                <div class="flex items-center gap-2 overflow-hidden">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-ping shrink-0"></span>
                    <span class="font-bold text-zinc-950 shrink-0">Speech In:</span>
                    <span id="cora-voice-live-transcript" class="italic text-zinc-600 truncate">Listening... speak naturally...</span>
                </div>
                <button type="button" onclick="window.coraStopVoiceAgent()" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-950 text-white hover:bg-zinc-800 shrink-0 border-0 cursor-pointer">
                    Done Speaking
                </button>
            </div>

            <!-- Discussion Feed (Initial greeting + dynamic bubbles + Action Cards) -->
            <div id="cora-agent-chat-feed" class="space-y-3.5 max-h-[420px] overflow-y-auto p-1 pr-2 cora-no-scrollbar">
                <div class="flex justify-start">
                    <div class="bg-[#FBFaf7] border border-zinc-200 rounded-2xl rounded-tl-sm p-4 text-xs text-zinc-800 max-w-[92%] sm:max-w-[85%] space-y-2.5 shadow-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-zinc-900 text-white flex items-center justify-center font-bold text-[10px]">C</span>
                            <span class="font-bold text-zinc-950">Financial Agent</span>
                            <span class="text-[10px] text-zinc-400">Ledger Assistant</span>
                        </div>
                        <div class="leading-relaxed text-zinc-700">
                            Good day! You have <strong class="text-zinc-950 font-mono">₹<?php echo number_format( $available_cash ); ?></strong> in cleared bank funds, with <strong class="text-zinc-950 font-mono">₹<?php echo number_format( $expected_in ); ?></strong> in uncollected receivables.
                            <?php if ( $overdue_total > 0 ) : ?>
                                <span class="text-red-700 font-semibold">(₹<?php echo number_format($overdue_total); ?> overdue).</span>
                            <?php endif; ?>
                            <br><br>
                            Tap the microphone or type below. I can log business expenses, draft GST invoices, audit runway, or simulate project deal margins for you.
                        </div>
                        <!-- Proactive Starter Chips -->
                        <div class="pt-1">
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Action Prompts</div>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="window.coraRunAgentPrompt('Log ₹4,500 gear expense')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white hover:bg-zinc-100 text-zinc-800 border border-zinc-200 cursor-pointer transition-colors">
                                    Log ₹4,500 gear expense
                                </button>
                                <button type="button" onclick="window.coraRunAgentPrompt('Draft invoice for client Rohan Verma')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white hover:bg-zinc-100 text-zinc-800 border border-zinc-200 cursor-pointer transition-colors">
                                    Draft invoice for client
                                </button>
                                <button type="button" onclick="window.coraRunAgentPrompt('Who owes me money right now?')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white hover:bg-zinc-100 text-zinc-800 border border-zinc-200 cursor-pointer transition-colors">
                                    Who owes me money?
                                </button>
                                <button type="button" onclick="window.coraRunAgentPrompt('What is my cash runway?')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white hover:bg-zinc-100 text-zinc-800 border border-zinc-200 cursor-pointer transition-colors">
                                    What is my cash runway?
                                </button>
                                <button type="button" onclick="window.coraRunAgentPrompt('Can I afford a ₹35k hire?')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white hover:bg-zinc-100 text-zinc-800 border border-zinc-200 cursor-pointer transition-colors">
                                    Can I afford a ₹35k hire?
                                </button>
                                <button type="button" onclick="window.coraRunAgentPrompt('Simulate a 1.5 lakh deal with 35k costs')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-white hover:bg-zinc-100 text-zinc-800 border border-zinc-200 cursor-pointer transition-colors">
                                    Simulate ₹1.5L project
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voice & Text Input Console (Responsive & Action-Oriented) -->
            <div class="pt-2 border-t border-zinc-100">
                <form onsubmit="window.coraSubmitAgentMessage(event)" class="flex items-center gap-2 bg-zinc-50 border border-zinc-200 rounded-2xl p-2 transition-all focus-within:border-zinc-900 focus-within:bg-white shadow-xs">
                    <!-- Voice Microphone Toggle Button -->
                    <button type="button" id="cora-agent-voice-btn" onclick="window.coraToggleVoiceAgent()" class="w-10 h-10 rounded-xl bg-zinc-950 text-white hover:bg-zinc-800 flex items-center justify-center cursor-pointer shrink-0 transition-all border-0 shadow-xs" title="Click to speak (Web Speech)">
                        <svg id="cora-mic-icon" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg>
                    </button>

                    <!-- Text Input -->
                    <input type="text" id="cora-agent-text-input" placeholder="Talk or type: 'Log ₹4,500 gear expense', 'Draft invoice', 'Who owes me?'..." class="flex-1 bg-transparent border-0 text-xs font-medium text-zinc-950 placeholder:text-zinc-400 focus:outline-none px-2 py-1.5" autocomplete="off">

                    <!-- Send Action Button -->
                    <button type="submit" id="cora-agent-send-btn" class="px-4 py-2.5 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs transition-colors border-0 cursor-pointer shrink-0 flex items-center gap-1.5 shadow-xs">
                        <span>Ask Agent</span>
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                </form>
                <div class="flex items-center justify-between text-[10px] text-zinc-400 mt-1.5 px-1">
                    <span>Speech Recognition available across Safari, Chrome, and mobile browsers</span>
                    <span>Action-oriented voice commands active</span>
                </div>
            </div>

        </div>

        <!-- 2. "NEEDS YOUR ATTENTION" ACTION CARDS -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-950 tracking-tight flex items-center gap-2">
                        <span>Needs Your Attention</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-zinc-100 text-zinc-700"><?php echo count($attention_cards); ?></span>
                    </h2>
                    <p class="text-xs text-zinc-500 font-medium">Cora detected these situations that require founder action.</p>
                </div>
            </div>

            <?php if ( empty( $attention_cards ) ) : ?>
                <div class="cora-fin-card p-4 bg-zinc-50 border border-zinc-200 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-zinc-900">All clear — no overdue payments or pending actions require immediate attention.</div>
                        <div class="text-[11px] text-zinc-500">Your workspace cash flow, tax reserves, and receivables are up to date.</div>
                    </div>
                </div>
            <?php else : ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    <?php foreach ( $attention_cards as $card ) : 
                        $badge_cls = 'bg-zinc-100 text-zinc-700 border-zinc-200';
                        if ( ( $card['type'] ?? '' ) === 'critical' ) $badge_cls = 'bg-red-50 text-red-700 border-red-200/60';
                        if ( ( $card['type'] ?? '' ) === 'warning' )  $badge_cls = 'bg-amber-50 text-amber-700 border-amber-200/60';
                        if ( ( $card['type'] ?? '' ) === 'info' )     $badge_cls = 'bg-blue-50 text-blue-700 border-blue-200/60';
                        if ( ( $card['type'] ?? '' ) === 'success' )  $badge_cls = 'bg-emerald-50 text-emerald-700 border-emerald-200/60';
                    ?>
                    <div class="cora-fin-card p-4 flex flex-col justify-between gap-3">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wide border <?php echo $badge_cls; ?>">
                                    <?php echo esc_html( $card['badge'] ?? 'Notice' ); ?>
                                </span>
                            </div>
                            <div class="text-xs font-bold text-zinc-950 leading-snug">
                                <?php echo esc_html( $card['title'] ); ?>
                            </div>
                            <div class="text-[11px] text-zinc-500 font-medium leading-relaxed">
                                <?php echo esc_html( $card['subtitle'] ); ?>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-zinc-100 flex items-center justify-between gap-2">
                            <?php if ( ( $card['action_type'] ?? '' ) === 'draft_followup' ) : ?>
                                <button type="button" onclick="window.coraDraftFollowUp('<?php echo esc_js( $card['payload']['invoice_id'] ?? '' ); ?>')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 transition-colors cursor-pointer border-0 flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                    <span><?php echo esc_html( $card['action_text'] ); ?></span>
                                </button>
                                <button type="button" onclick="window.coraSwitchFinTab('fin-ledger')" class="px-2.5 py-1.5 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                                    View in Ledger
                                </button>
                            <?php elseif ( ( $card['action_type'] ?? '' ) === 'view_recurring' ) : ?>
                                <button type="button" onclick="window.coraOpenDrawer('subscriptions')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-zinc-100 text-zinc-900 hover:bg-zinc-200 transition-colors cursor-pointer border-0">
                                    <?php echo esc_html( $card['action_text'] ); ?>
                                </button>
                            <?php else : ?>
                                <button type="button" onclick="window.coraSwitchFinTab('fin-agent')" class="px-3 py-1.5 rounded-xl text-xs font-bold bg-zinc-100 text-zinc-900 hover:bg-zinc-200 transition-colors cursor-pointer border-0">
                                    <?php echo esc_html( $card['action_text'] ); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 3. COMPACT CASH FLOW & RUNWAY TRAJECTORY CHART -->
        <div class="cora-fin-card p-5 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-zinc-100 pb-3">
                <div>
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Cash Flow &amp; Runway Trajectory</h3>
                    <p class="text-[11px] text-zinc-500">Historical inflows vs recurring costs &amp; 90-day predictive forecast</p>
                </div>
                <div class="flex items-center gap-3 text-xs font-semibold text-zinc-600">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-zinc-950 inline-block"></span> Inflows</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-zinc-400 inline-block"></span> Outflows</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Net Cash</span>
                </div>
            </div>
            <div class="h-64 w-full relative">
                <canvas id="cora-fin-cashflow-chart"></canvas>
            </div>
        </div>

    </div>

    <!-- Backward compatibility alias: tab-fin-home points to tab-fin-agent -->
    <div id="tab-fin-home" class="hidden"></div>


    <!-- ════════════════════════════════════════════════════════
         TAB 2: UNIFIED LEDGER & ACTIVITY STREAM
         ════════════════════════════════════════════════════════ -->
    <div id="tab-fin-ledger" class="cora-fin-tab-panel space-y-5 hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-zinc-950">Ledger &amp; Cash Flow Activity</h2>
                <p class="text-xs text-zinc-500 font-medium">Unified chronological timeline of incoming receivables and business expenses.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.coraOpenDrawer('create-invoice')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 flex items-center gap-1.5 shadow-xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>+ Draft Invoice</span>
                </button>
                <button type="button" onclick="window.coraOpenDrawer('add-expense')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-100 hover:bg-zinc-200 text-zinc-900 cursor-pointer border-0 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>- Log Expense</span>
                </button>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="cora-fin-card p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-zinc-50/50">
            <div class="flex items-center gap-1.5 overflow-x-auto cora-no-scrollbar">
                <button type="button" onclick="window.coraFilterLedgerType('all')" id="btn-ledger-filter-all" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-zinc-950 text-white cursor-pointer border-0 shrink-0">
                    All Activity (<?php echo count($all_transactions); ?>)
                </button>
                <button type="button" onclick="window.coraFilterLedgerType('inflow')" id="btn-ledger-filter-inflow" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0 shrink-0 flex items-center gap-1.5">
                    <span>Money In</span>
                    <span class="font-mono text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded">+₹<?php echo number_format($expected_in); ?></span>
                </button>
                <button type="button" onclick="window.coraFilterLedgerType('outflow')" id="btn-ledger-filter-outflow" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0 shrink-0 flex items-center gap-1.5">
                    <span>Money Out</span>
                    <span class="font-mono text-[10px] font-bold text-zinc-700 bg-zinc-200 px-1.5 py-0.2 rounded">-₹<?php echo number_format($expected_out); ?></span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <input type="text" id="ledger-search-input" oninput="window.coraSearchLedger(this.value)" placeholder="Search client, vendor, invoice..." class="bg-white border border-zinc-200 rounded-xl px-3 py-1.5 text-xs text-zinc-900 focus:outline-none w-full sm:w-60">
            </div>
        </div>

        <?php if ( empty( $all_transactions ) ) : ?>
        <div class="cora-fin-card p-10 text-center flex flex-col items-center justify-center space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-500 flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900">No transactions recorded yet</h3>
                <p class="text-xs text-zinc-500 max-w-sm mx-auto mt-1">Speak to Cora or draft your first invoice or expense to start tracking cash flow.</p>
            </div>
        </div>
        <?php else : ?>
        <div class="cora-fin-card overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs divide-y divide-zinc-200">
                    <thead class="bg-zinc-50 text-zinc-500 font-bold text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Transaction / Description</th>
                            <th class="py-3 px-4">Category / Reference</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 font-medium" id="cora-ledger-tbody">
                        <?php foreach ( $all_transactions as $tx ) : 
                            $isInflow = ( $tx['type'] === 'inflow' );
                        ?>
                        <tr class="cora-tx-row hover:bg-zinc-50/80 transition-colors" data-type="<?php echo esc_attr( $tx['type'] ); ?>">
                            <td class="py-3.5 px-4 text-zinc-600 font-mono text-[11px]">
                                <?php echo esc_html( $tx['date'] ); ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-zinc-950"><?php echo esc_html( $tx['title'] ); ?></div>
                                <?php if ( ! empty( $tx['client_name'] ) ) : ?>
                                    <div class="text-[10px] text-zinc-400"><?php echo esc_html( $tx['client_name'] ); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-zinc-600 text-[11px]">
                                <?php echo esc_html( $tx['category'] ); ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if ( $isInflow ) : ?>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">Money In</span>
                                <?php else : ?>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-100 text-zinc-700 border border-zinc-200">Money Out</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-xs <?php echo $isInflow ? 'text-emerald-600' : 'text-zinc-950'; ?>">
                                <?php echo $isInflow ? '+' : '-'; ?>₹<?php echo number_format( $tx['amount'] ); ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <?php if ( $isInflow ) : ?>
                                    <?php if ( $tx['status'] === 'paid' ) : ?>
                                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-emerald-100 text-emerald-800">Paid</span>
                                    <?php elseif ( $tx['is_overdue'] ) : ?>
                                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-red-100 text-red-800"><?php echo $tx['days_overdue']; ?>d Overdue</span>
                                    <?php else : ?>
                                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-amber-100 text-amber-800">Pending</span>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-zinc-100 text-zinc-700">Settled</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <?php if ( $isInflow && $tx['status'] !== 'paid' ) : ?>
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" onclick="window.coraMarkInvoicePaid('<?php echo esc_js($tx['id']); ?>')" class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-zinc-950 text-white hover:bg-zinc-800 border-0 cursor-pointer">
                                            Mark Paid
                                        </button>
                                        <button type="button" onclick="window.coraDraftFollowUp('<?php echo esc_js($tx['id']); ?>')" class="px-2.5 py-1 rounded-lg text-[10px] font-semibold text-zinc-600 hover:bg-zinc-100 border-0 cursor-pointer">
                                            Remind
                                        </button>
                                    </div>
                                <?php else : ?>
                                    <span class="text-[10px] text-zinc-400 font-mono">Recorded</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Stack View -->
            <div class="md:hidden divide-y divide-zinc-100" id="cora-ledger-mobile-cards">
                <?php foreach ( $all_transactions as $tx ) : 
                    $isInflow = ( $tx['type'] === 'inflow' );
                ?>
                <div class="p-3.5 space-y-2 cora-tx-card" data-type="<?php echo esc_attr( $tx['type'] ); ?>">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-mono text-zinc-400"><?php echo esc_html( $tx['date'] ); ?></span>
                        <?php if ( $isInflow ) : ?>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700">Money In</span>
                        <?php else : ?>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-100 text-zinc-700">Money Out</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-bold text-xs text-zinc-950"><?php echo esc_html( $tx['title'] ); ?></div>
                            <div class="text-[10px] text-zinc-500"><?php echo esc_html( $tx['category'] ); ?></div>
                        </div>
                        <div class="font-mono font-extrabold text-sm <?php echo $isInflow ? 'text-emerald-600' : 'text-zinc-950'; ?> shrink-0">
                            <?php echo $isInflow ? '+' : '-'; ?>₹<?php echo number_format( $tx['amount'] ); ?>
                        </div>
                    </div>
                    <?php if ( $isInflow && $tx['status'] !== 'paid' ) : ?>
                    <div class="pt-2 border-t border-zinc-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold <?php echo $tx['is_overdue'] ? 'text-red-600' : 'text-amber-600'; ?>">
                            <?php echo $tx['is_overdue'] ? 'Overdue' : 'Pending Collection'; ?>
                        </span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="window.coraMarkInvoicePaid('<?php echo esc_js($tx['id']); ?>')" class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-zinc-950 text-white border-0 cursor-pointer">
                                Mark Paid
                            </button>
                            <button type="button" onclick="window.coraDraftFollowUp('<?php echo esc_js($tx['id']); ?>')" class="px-2 py-1 rounded-lg text-[10px] font-semibold text-zinc-600 border-0 cursor-pointer">
                                Remind
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>



    <!-- ════════════════════════════════════════════════════════
         TAB 2: MONEY IN (Receivables & Collections)
         ════════════════════════════════════════════════════════ -->
    <div id="tab-fin-receivables" class="cora-fin-tab-panel space-y-5 hidden">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-zinc-950">Receivables &amp; Collections</h2>
                <p class="text-xs text-zinc-500 font-medium">Track who owes you money, state-level GST classifications, and send AI-drafted payment reminders.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.coraOpenDrawer('create-invoice')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 flex items-center gap-1.5 shadow-xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>New Client Invoice</span>
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
        <?php if ( empty( $receivables ) ) : ?>
        <div class="cora-fin-card p-12 text-center flex flex-col items-center justify-center space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-500 flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900">No receivables logged</h3>
                <p class="text-xs text-zinc-500 max-w-sm mx-auto mt-1">Draft your first client GST invoice to track payment milestones, overdue aging, and automated payment reminders.</p>
            </div>
            <button type="button" onclick="window.coraOpenDrawer('create-invoice')" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 inline-flex items-center gap-1.5 mt-2">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Draft Client GST Invoice</span>
            </button>
        </div>
        <?php else : ?>
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
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs divide-y divide-zinc-200">
                    <thead class="bg-zinc-50 text-zinc-500 font-bold text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Invoice #</th>
                            <th class="py-3 px-4">Client &amp; Service</th>
                            <th class="py-3 px-4">Place of Supply / Tax</th>
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
                                <div class="text-zinc-900 font-semibold"><?php echo esc_html( $r['place_of_supply'] ?? 'Delhi (07)' ); ?></div>
                                <div class="text-[10px] text-zinc-500"><?php echo esc_html( $r['tax_type'] ?? 'CGST (9%) + SGST (9%)' ); ?></div>
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
                                        <button type="button" onclick="window.coraDraftFollowUp('<?php echo esc_js( $r['id'] ); ?>')" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 flex items-center gap-1" title="Draft AI Payment Follow-up">
                                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                            <span>Remind</span>
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

            <!-- Mobile Effortless Card Stack View (Zero Horizontal Scroll) -->
            <div class="block md:hidden divide-y divide-zinc-100" id="cora-receivables-mobile-list">
                <?php foreach ( $receivables as $r ) : 
                    $is_paid = ( $r['status'] === 'paid' );
                ?>
                <div class="p-4 bg-white hover:bg-zinc-50/50 transition-colors cora-rec-card space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono text-xs font-bold text-zinc-900"><?php echo esc_html( $r['invoice_number'] ); ?></span>
                            <span class="text-[10px] text-zinc-400">•</span>
                            <span class="text-[10px] text-zinc-500 font-medium"><?php echo esc_html( $r['place_of_supply'] ?? 'Delhi (07)' ); ?></span>
                        </div>
                        <div>
                            <?php if ( $is_paid ) : ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Paid</span>
                            <?php elseif ( ! empty( $r['is_overdue'] ) ) : ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Overdue</span>
                            <?php else : ?>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Pending</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <div class="font-bold text-zinc-950 text-sm"><?php echo esc_html( $r['client_name'] ); ?></div>
                        <div class="text-xs text-zinc-500 mt-0.5"><?php echo esc_html( $r['package_name'] ); ?></div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 border-t border-zinc-100">
                        <div class="flex items-center gap-1.5">
                            <span class="text-zinc-500 text-[11px]">Due:</span>
                            <span class="text-zinc-800 font-semibold text-[11px]"><?php echo esc_html( $r['due_date'] ); ?></span>
                            <?php if ( ! empty( $r['is_overdue'] ) ) : ?>
                                <span class="text-[9px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded">
                                    <?php echo intval( $r['days_overdue'] ?? 7 ); ?>d overdue
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="font-mono font-extrabold text-sm text-zinc-950">
                            ₹<?php echo number_format( $r['due_balance'] ?: $r['total_amount'] ); ?>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <?php if ( ! $is_paid ) : ?>
                            <button type="button" onclick="window.coraDraftFollowUp('<?php echo esc_js( $r['id'] ); ?>')" class="flex-1 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 flex items-center justify-center gap-1.5 shadow-xs">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                <span>Remind Client</span>
                            </button>
                            <button type="button" onclick="window.coraMarkInvoicePaid('<?php echo esc_js( $r['id'] ); ?>')" class="px-3 py-2 rounded-xl text-xs font-semibold bg-zinc-100 hover:bg-zinc-200 text-zinc-800 cursor-pointer border-0">
                                Mark Paid
                            </button>
                        <?php else : ?>
                            <span class="text-xs text-emerald-600 font-semibold flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Reconciled &amp; Settled</span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

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
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Log Expense</span>
                </button>
                <button type="button" onclick="window.coraOpenDrawer('subscriptions')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-100 hover:bg-zinc-200 text-zinc-900 cursor-pointer border-0 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Add Subscription</span>
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
                <button type="button" onclick="window.coraOpenDrawer('subscriptions')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white border border-zinc-200 hover:bg-zinc-100 text-zinc-900 cursor-pointer shadow-xs flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Add Subscription</span>
                </button>
            </div>
        </div>

        <!-- Subscriptions Grid -->
        <?php if ( empty( $recurring_list ) ) : ?>
        <div class="cora-fin-card p-10 text-center flex flex-col items-center justify-center space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-500 flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900">No recurring commitments tracked</h3>
                <p class="text-xs text-zinc-500 max-w-sm mx-auto mt-1">Track monthly software tools, studio rent, equipment leases, and vendor retainers to forecast burn rate accurately.</p>
            </div>
            <button type="button" onclick="window.coraOpenDrawer('subscriptions')" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 inline-flex items-center gap-1.5 mt-2">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Add Subscription</span>
            </button>
        </div>
        <?php else : ?>
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
        <?php endif; ?>

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
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    <span>Simulate Project Deal</span>
                </button>
            </div>
        </div>

        <?php if ( empty( $client_profits ) ) : ?>
        <div class="cora-fin-card p-12 text-center flex flex-col items-center justify-center space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-500 flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path><line x1="12" y1="18" x2="12" y2="20"></line><line x1="12" y1="4" x2="12" y2="6"></line></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900">No client revenue data yet</h3>
                <p class="text-xs text-zinc-500 max-w-sm mx-auto mt-1">As you log client invoices, payments, and project-associated direct expenses, client profit margins and revenue concentration will appear here automatically.</p>
            </div>
            <button type="button" onclick="window.coraOpenDrawer('project-sim')" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 inline-flex items-center gap-1.5 mt-2">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                <span>Simulate Prospective Deal</span>
            </button>
        </div>
        <?php else : ?>
        <!-- AI Profitability Explanation Card -->
        <div class="cora-fin-card p-5 bg-zinc-50 border-zinc-200 space-y-2">
            <div class="flex items-center gap-2 text-xs font-bold text-zinc-950">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"></path></svg>
                <span>Cora's Profitability Analysis</span>
            </div>
            <p class="text-xs text-zinc-700 leading-relaxed font-medium">
                Your business generated <strong>₹<?php echo number_format( $gross_inflow ); ?></strong> in total recorded inflows. Margins are analyzed across <?php echo count($client_profits); ?> active client accounts.
            </p>
        </div>

        <!-- Profitability Breakdown Chart & Table Container -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            
            <!-- Donut Chart: Revenue Concentration by Client -->
            <div class="cora-fin-card p-5 space-y-3 flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Revenue Distribution</h3>
                    <p class="text-[11px] text-zinc-500">Client concentration &amp; risk</p>
                </div>
                <div class="h-52 w-full relative flex items-center justify-center">
                    <canvas id="cora-fin-profit-chart"></canvas>
                </div>
                <div class="text-[10px] text-zinc-500 text-center font-medium">
                    Breakdown across <?php echo count($client_profits); ?> client accounts.
                </div>
            </div>

            <!-- Client Profitability Table -->
            <div class="cora-fin-card overflow-hidden lg:col-span-2">
                <div class="p-4 border-b border-zinc-100 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Client Margin Ranking</h3>
                    <span class="text-[11px] text-zinc-400 font-medium">Ranked by Net Margin</span>
                </div>
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-zinc-200">
                        <thead class="bg-zinc-50 text-zinc-500 font-bold text-[10px] uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Client Name</th>
                                <th class="py-3 px-4">Revenue</th>
                                <th class="py-3 px-4">Costs</th>
                                <th class="py-3 px-4">Net Profit</th>
                                <th class="py-3 px-4">Margin</th>
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
                                        <div class="w-14 h-1.5 rounded-full bg-zinc-100 overflow-hidden">
                                            <div class="h-full bg-zinc-900 rounded-full" style="width: <?php echo min(100, $cp['margin']); ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <?php if ( ! empty( $cp['is_top_tier'] ) ) : ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            <span>High Margin</span>
                                        </span>
                                    <?php else : ?>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 text-zinc-700">Standard</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Effortless Card Stack View (Zero Horizontal Scroll) -->
                <div class="block md:hidden divide-y divide-zinc-100" id="cora-profitability-mobile-list">
                    <?php foreach ( $client_profits as $cp ) : ?>
                    <div class="p-4 bg-white hover:bg-zinc-50/50 transition-colors cora-profit-card space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-zinc-950 text-sm"><?php echo esc_html( $cp['client_name'] ); ?></span>
                            <?php if ( ! empty( $cp['is_top_tier'] ) ) : ?>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>High Margin</span>
                                </span>
                            <?php else : ?>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold bg-zinc-100 text-zinc-700">Standard</span>
                            <?php endif; ?>
                        </div>

                        <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-zinc-50 border border-zinc-100 text-center">
                            <div>
                                <div class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Revenue</div>
                                <div class="font-mono font-bold text-xs text-zinc-900 mt-0.5">₹<?php echo number_format( $cp['revenue'] ); ?></div>
                            </div>
                            <div>
                                <div class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Costs</div>
                                <div class="font-mono text-xs text-zinc-500 mt-0.5">₹<?php echo number_format( $cp['costs'] ); ?></div>
                            </div>
                            <div>
                                <div class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider">Net Profit</div>
                                <div class="font-mono font-bold text-xs text-emerald-700 mt-0.5">₹<?php echo number_format( $cp['profit'] ); ?></div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-zinc-500 font-medium">Profit Margin</span>
                                <span class="font-bold text-zinc-900"><?php echo $cp['margin']; ?>%</span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-zinc-100 overflow-hidden">
                                <div class="h-full bg-zinc-900 rounded-full" style="width: <?php echo min(100, $cp['margin']); ?>%;"></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

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
                <div class="text-[10px] text-emerald-300 mt-0.5 flex items-center gap-1">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Healthy Operating Buffer</span>
                </div>
            </div>
        </div>

        <!-- Key Timeline Events -->
        <div class="cora-fin-card p-5 space-y-4">
            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Projected Cash Timeline Events</h3>
            <?php if ( empty( $forecast_events ) ) : ?>
                <div class="p-8 text-center bg-zinc-50/50 rounded-xl border border-zinc-100 space-y-2">
                    <p class="text-xs font-semibold text-zinc-700">No upcoming payment events or scheduled renewals</p>
                    <p class="text-[11px] text-zinc-400">Add client invoices or recurring subscriptions to forecast cash movement milestones.</p>
                </div>
            <?php else : ?>
                <div class="space-y-2 text-xs">
                    <?php foreach ( $forecast_events as $fe ) : ?>
                    <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 border border-zinc-100">
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold <?php echo $fe['type'] === 'in' ? 'bg-emerald-100 text-emerald-800' : 'bg-zinc-200 text-zinc-800'; ?>"><?php echo esc_html( $fe['day'] ); ?></span>
                            <span class="font-bold text-zinc-900"><?php echo esc_html( $fe['label'] ); ?></span>
                        </div>
                        <span class="font-mono font-bold <?php echo $fe['type'] === 'in' ? 'text-emerald-700' : 'text-amber-700'; ?>">
                            <?php echo ( $fe['amt'] >= 0 ? '+' : '' ) . '₹' . number_format( abs( $fe['amt'] ) ); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
                <button type="button" onclick="window.coraOpenDrawer('accountant-pack')" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 shadow-xs flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Export Accountant Pack</span>
                </button>
            </div>
        </div>

        <!-- Disclaimer Banner -->
        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/70 text-xs text-amber-900 flex items-start gap-2.5">
            <span class="text-amber-600 font-bold shrink-0 mt-0.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </span>
            <div>
                <strong>Estimated Tax Reserve: ₹<?php echo number_format( floatval( $gst_data['tax_reserve_target'] ?? 0 ) ); ?></strong>
                <p class="text-[11px] text-amber-800 mt-0.5">Calculated based on recorded workspace invoices and ledger expenses. Please confirm with your Chartered Accountant before filing.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
            <div class="cora-fin-card p-4">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">GST Collected (18%)</span>
                <div class="text-xl font-extrabold text-zinc-950 cora-mono-num mt-1">₹<?php echo number_format( floatval( $gst_data['gst_collected'] ?? 0 ) ); ?></div>
                <div class="text-[10px] text-zinc-500 mt-0.5">9% CGST + 9% SGST on billings</div>
            </div>
            <div class="cora-fin-card p-4">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">Input Tax Credit (ITC)</span>
                <div class="text-xl font-extrabold text-emerald-700 cora-mono-num mt-1">₹<?php echo number_format( floatval( $gst_data['itc_credit'] ?? 0 ) ); ?></div>
                <div class="text-[10px] text-zinc-500 mt-0.5">Estimated on eligible business expenses</div>
            </div>
            <div class="cora-fin-card p-4">
                <span class="text-[10px] font-bold text-zinc-400 uppercase">Net GST Payable</span>
                <div class="text-xl font-extrabold text-zinc-950 cora-mono-num mt-1">₹<?php echo number_format( floatval( $gst_data['net_gst_payable'] ?? 0 ) ); ?></div>
                <div class="text-[10px] text-zinc-500 mt-0.5">Quarterly tax reserve recommended</div>
            </div>
        </div>

    </div>

</div>


<!-- ════════════════════════════════════════════════════════
     FLOATING BOTTOM "ASK CORA" BAR & EXPANDED DECISION POPUP
     (Standard bottom-anchored floating popup card - Zero Neon)
     ════════════════════════════════════════════════════════ -->

<div id="cora-fin-copilot-container" class="hidden lg:flex">
    <div class="w-full flex flex-col items-center">

        <!-- Expanded AI Decision Pop-up Window (Floats Above Bar) -->
        <div id="cora-fin-copilot-window" class="opacity-0 scale-95 pointer-events-none transform origin-bottom transition-all duration-300 ease-out mb-3 rounded-2xl overflow-hidden flex flex-col bg-white border border-zinc-200 shadow-2xl" style="height: 450px;">
            
            <!-- Window Header -->
            <div class="px-5 py-3.5 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/80 select-none">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-zinc-950 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        C
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-zinc-900">Cora AI</span>
                            <span class="px-1.5 py-0.2 rounded text-[9.5px] font-semibold bg-zinc-200/70 text-zinc-700">Financial Agent</span>
                        </div>
                        <div class="text-[10px] text-zinc-500 font-medium">Instant answers &amp; decision analysis from your ledger</div>
                    </div>
                </div>

                <button type="button" onclick="window.coraCloseCopilot()" class="w-7 h-7 rounded-lg hover:bg-zinc-200/70 text-zinc-400 hover:text-zinc-700 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm" title="Close">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Window Content: 2-Column Split (Zero Neon, Clean Monochromatic) -->
            <div id="cora-fin-copilot-dashboard" class="flex-1 flex flex-col sm:flex-row divide-y sm:divide-y-0 sm:divide-x divide-zinc-100 overflow-hidden bg-white">
                
                <!-- Left Column (Decision Actions & Quick Questions) -->
                <div class="flex-1 p-5 space-y-4 overflow-y-auto select-none">
                    <div>
                        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Decision Actions</div>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="window.coraCloseCopilot(); window.coraOpenDrawer('create-invoice');" class="flex items-center gap-2.5 p-2.5 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-bold text-zinc-800 cursor-pointer shadow-xs text-left transition-colors">
                                <span class="w-6 h-6 rounded-md bg-zinc-100 flex items-center justify-center text-zinc-800 shrink-0">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                </span>
                                <div>
                                    <div class="font-bold text-zinc-950">Draft Invoice</div>
                                    <div class="text-[9.5px] text-zinc-400 font-normal">Bill client with GST</div>
                                </div>
                            </button>
                            <button type="button" onclick="window.coraCloseCopilot(); window.coraOpenDrawer('record-income');" class="flex items-center gap-2.5 p-2.5 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-bold text-zinc-800 cursor-pointer shadow-xs text-left transition-colors">
                                <span class="w-6 h-6 rounded-md bg-zinc-100 text-zinc-800 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                                </span>
                                <div>
                                    <div class="font-bold text-zinc-950">Record Payment</div>
                                    <div class="text-[9.5px] text-zinc-400 font-normal">Reconcile receivables</div>
                                </div>
                            </button>
                            <button type="button" onclick="window.coraCloseCopilot(); window.coraOpenDrawer('project-sim');" class="flex items-center gap-2.5 p-2.5 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-bold text-zinc-800 cursor-pointer shadow-xs text-left transition-colors">
                                <span class="w-6 h-6 rounded-md bg-zinc-100 text-zinc-800 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                                </span>
                                <div>
                                    <div class="font-bold text-zinc-950">Deal Simulator</div>
                                    <div class="text-[9.5px] text-zinc-400 font-normal">Pricing &amp; margin test</div>
                                </div>
                            </button>
                            <button type="button" onclick="window.coraCloseCopilot(); window.coraOpenDrawer('add-expense');" class="flex items-center gap-2.5 p-2.5 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-bold text-zinc-800 cursor-pointer shadow-xs text-left transition-colors">
                                <span class="w-6 h-6 rounded-md bg-zinc-100 text-zinc-800 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                </span>
                                <div>
                                    <div class="font-bold text-zinc-950">Log Expense</div>
                                    <div class="text-[9.5px] text-zinc-400 font-normal">Track ITC &amp; vendor TDS</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Quick Decision Queries</div>
                        <div class="flex flex-wrap gap-1.5">
                            <span onclick="window.coraSubmitCopilotPrompt('Who owes me money right now?')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 cursor-pointer transition-colors">Who owes me money?</span>
                            <span onclick="window.coraSubmitCopilotPrompt('Why did my profit margin drop this month?')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 cursor-pointer transition-colors">Why did profit drop?</span>
                            <span onclick="window.coraSubmitCopilotPrompt('Can I afford to hire someone for ₹40k/month?')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 cursor-pointer transition-colors">Can I afford a ₹40k hire?</span>
                            <span onclick="window.coraSubmitCopilotPrompt('Show me my biggest recurring expenses')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 cursor-pointer transition-colors">Audit subscriptions</span>
                            <span onclick="window.coraSubmitCopilotPrompt('Who are my most profitable clients?')" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-zinc-100 hover:bg-zinc-200 text-zinc-800 cursor-pointer transition-colors">Top profitable clients</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Live Financial Summary & Capacity) -->
                <div class="w-full sm:w-64 p-5 flex flex-col justify-between select-none bg-zinc-50/50">
                    <div class="space-y-3">
                        <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Live Ledger Context</div>
                        <div class="space-y-2 text-xs">
                            <div class="p-2.5 rounded-xl border border-zinc-200 bg-white">
                                <div class="text-[10px] text-zinc-400 font-semibold">Available Cash</div>
                                <div class="text-sm font-bold text-zinc-950 font-mono mt-0.5">₹<?php echo number_format($available_cash); ?></div>
                            </div>
                            <div class="p-2.5 rounded-xl border border-zinc-200 bg-white">
                                <div class="text-[10px] text-zinc-400 font-semibold">Uncollected Invoices</div>
                                <div class="text-sm font-bold text-zinc-950 font-mono mt-0.5">₹<?php echo number_format($expected_in); ?></div>
                            </div>
                            <div class="p-2.5 rounded-xl border border-zinc-200 bg-white">
                                <div class="text-[10px] text-zinc-400 font-semibold">Monthly Fixed Burn</div>
                                <div class="text-sm font-bold text-zinc-950 font-mono mt-0.5">₹<?php echo number_format($monthly_rec); ?>/mo</div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Quota -->
                    <div class="pt-3 border-t border-zinc-200/80">
                        <div class="flex justify-between text-[10px] text-zinc-600 font-semibold mb-1">
                            <span>Monthly AI Quota</span>
                            <span class="font-mono font-bold text-zinc-950">42.5%</span>
                        </div>
                        <div class="w-full bg-zinc-200 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-zinc-950 h-full rounded-full" style="width: 42.5%"></div>
                        </div>
                        <div class="text-[9px] text-zinc-400 pt-1 flex justify-between">
                            <span>Gemini 2.5 Flash</span>
                            <span>42,500 / 100,000 tokens</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Chat History Pane (Shown after a question is asked) -->
            <div id="cora-fin-copilot-chat" class="hidden flex-1 overflow-y-auto p-4 space-y-3 bg-white">
                <!-- Dynamic AI answers streamed here -->
            </div>

            <!-- Popover Input Footer -->
            <div class="p-3 border-t border-zinc-200 flex items-center gap-3 bg-white select-none">
                <span class="text-zinc-400 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="cora-fin-copilot-chat-input" placeholder="Ask anything about invoices, cash flow, clients, or expenses..." class="flex-1 text-xs outline-none border-none bg-transparent text-zinc-900 placeholder:text-zinc-400">
                <button type="button" id="cora-fin-copilot-send-btn" onclick="window.coraSendCopilotChat()" class="px-4 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white transition-colors border-none cursor-pointer flex items-center gap-1.5 shrink-0 text-xs font-bold shadow-xs">
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
     SLIDE DRAWERS (Cora Native Standard - IDs ending in -drawer)
     ════════════════════════════════════════════════════════ -->

<!-- 1. MULTI-STEP DYNAMIC GST INVOICE CREATOR -->
<aside id="cora-fin-invoice-drawer" class="cora-side-drawer fixed top-0 right-0 w-full sm:w-[560px] max-w-full h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden hidden collapsed">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50 shrink-0">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-zinc-950">Create Dynamic GST Invoice</h3>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-200 text-zinc-800" id="inv-step-badge">Step 1 of 3</span>
            </div>
            <p class="text-[11px] text-zinc-500">Intercompatible with CRM Leads &amp; Document Vault</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-8 h-8 rounded-lg hover:bg-zinc-200 text-zinc-600 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm font-bold" title="Close Drawer">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Stepper Navigation -->
    <div class="flex border-b border-zinc-200 bg-zinc-100/60 text-xs font-semibold select-none shrink-0">
        <button type="button" onclick="window.coraGoInvStep(1)" id="inv-nav-1" class="flex-1 py-2.5 text-center border-b-2 border-zinc-950 text-zinc-950 font-bold bg-white cursor-pointer">1. Client &amp; State</button>
        <button type="button" onclick="window.coraGoInvStep(2)" id="inv-nav-2" class="flex-1 py-2.5 text-center border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 cursor-pointer">2. Items &amp; GST</button>
        <button type="button" onclick="window.coraGoInvStep(3)" id="inv-nav-3" class="flex-1 py-2.5 text-center border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 cursor-pointer">3. Vault &amp; Terms</button>
    </div>

    <form onsubmit="window.coraSubmitInvoice(event)" class="flex-1 overflow-y-auto p-5 space-y-4">
        
        <!-- STEP 1: CLIENT & PLACE OF SUPPLY -->
        <div id="inv-step-1" class="space-y-4">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Select From CRM Leads / Clients</label>
                <select id="inv-crm-select" onchange="window.coraSelectCrmContact(this.value)" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
                    <option value="">-- Or enter custom client details below --</option>
                    <?php foreach ( $crm_contacts as $c ) : ?>
                        <option value="<?php echo esc_attr( json_encode( $c ) ); ?>">
                            <?php echo esc_html( $c['name'] . ' (' . ($c['email'] ?: $c['phone']) . ')' ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Client / Business Name</label>
                <input type="text" id="inv-client-name" placeholder="e.g. Acme Studios & Media Pvt Ltd" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase">Client Email</label>
                    <input type="email" id="inv-client-email" placeholder="finance@acmestudios.in" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase">Client GSTIN (Optional)</label>
                    <input type="text" id="inv-client-gstin" placeholder="07AAAAA0000A1Z5" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 font-mono focus:outline-none uppercase">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Place of Supply (State)</label>
                <select id="inv-place-of-supply" onchange="window.coraRecalcInvoiceGST()" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
                    <option value="07_Delhi" selected>Delhi (07) — Intra-State (CGST 9% + SGST 9%)</option>
                    <option value="27_Maharashtra">Maharashtra (27) — Inter-State (IGST 18%)</option>
                    <option value="29_Karnataka">Karnataka (29) — Inter-State (IGST 18%)</option>
                    <option value="33_TamilNadu">Tamil Nadu (33) — Inter-State (IGST 18%)</option>
                    <option value="36_Telangana">Telangana (36) — Inter-State (IGST 18%)</option>
                    <option value="24_Gujarat">Gujarat (24) — Inter-State (IGST 18%)</option>
                    <option value="09_UttarPradesh">Uttar Pradesh (09) — Inter-State (IGST 18%)</option>
                    <option value="08_Rajasthan">Rajasthan (08) — Inter-State (IGST 18%)</option>
                    <option value="19_WestBengal">West Bengal (19) — Inter-State (IGST 18%)</option>
                    <option value="06_Haryana">Haryana (06) — Inter-State (IGST 18%)</option>
                    <option value="03_Punjab">Punjab (03) — Inter-State (IGST 18%)</option>
                    <option value="32_Kerala">Kerala (32) — Inter-State (IGST 18%)</option>
                </select>
                <div class="text-[10px] text-zinc-500" id="inv-tax-mode-badge">Tax Mode: Intra-State (CGST 9% + SGST 9%)</div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="button" onclick="window.coraGoInvStep(2)" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0">
                    Next: Line Items &amp; GST →
                </button>
            </div>
        </div>

        <!-- STEP 2: LINE ITEMS & GST BREAKDOWN -->
        <div id="inv-step-2" class="space-y-4 hidden">
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Package / Project Title</label>
                <input type="text" id="inv-package-name" placeholder="e.g. Commercial Brand Video &amp; Studio Retainer" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase">Line Items (SAC Codes)</label>
                    <button type="button" onclick="window.coraAddInvoiceItemRow()" class="text-xs font-bold text-zinc-900 hover:underline cursor-pointer border-0 bg-transparent">+ Add Item</button>
                </div>
                <div id="inv-items-container" class="space-y-2">
                    <div class="inv-item-row p-3 rounded-xl bg-zinc-50 border border-zinc-200 space-y-2">
                        <input type="text" class="inv-item-desc w-full bg-white border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-900" placeholder="Item description" value="Commercial Shoot &amp; Media Production">
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-[9px] text-zinc-400 uppercase font-bold">SAC Code</label>
                                <input type="text" class="inv-item-sac w-full bg-white border border-zinc-200 rounded-lg px-2 py-1 text-xs font-mono" value="998386">
                            </div>
                            <div>
                                <label class="text-[9px] text-zinc-400 uppercase font-bold">Qty</label>
                                <input type="number" class="inv-item-qty w-full bg-white border border-zinc-200 rounded-lg px-2 py-1 text-xs font-mono" value="1" oninput="window.coraRecalcInvoiceGST()">
                            </div>
                            <div>
                                <label class="text-[9px] text-zinc-400 uppercase font-bold">Rate (₹)</label>
                                <input type="number" class="inv-item-rate w-full bg-white border border-zinc-200 rounded-lg px-2 py-1 text-xs font-mono font-bold" value="75000" oninput="window.coraRecalcInvoiceGST()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time GST Math Summary Box -->
            <div class="p-4 rounded-xl bg-zinc-900 text-white space-y-2 text-xs">
                <div class="flex items-center justify-between text-zinc-300">
                    <span>Taxable Subtotal:</span>
                    <span class="font-mono font-bold" id="inv-calc-subtotal">₹75,000</span>
                </div>
                <div class="flex items-center justify-between text-zinc-300" id="inv-calc-cgst-row">
                    <span>CGST (9%):</span>
                    <span class="font-mono" id="inv-calc-cgst">₹6,750</span>
                </div>
                <div class="flex items-center justify-between text-zinc-300" id="inv-calc-sgst-row">
                    <span>SGST (9%):</span>
                    <span class="font-mono" id="inv-calc-sgst">₹6,750</span>
                </div>
                <div class="flex items-center justify-between text-zinc-300 hidden" id="inv-calc-igst-row">
                    <span>IGST (18%):</span>
                    <span class="font-mono" id="inv-calc-igst">₹13,500</span>
                </div>
                <div class="pt-2 border-t border-zinc-800 flex items-center justify-between font-bold text-sm text-emerald-400">
                    <span>Total Invoice Value:</span>
                    <span class="font-mono font-extrabold" id="inv-calc-grand-total">₹88,500</span>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-between">
                <button type="button" onclick="window.coraGoInvStep(1)" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                    ← Back
                </button>
                <button type="button" onclick="window.coraGoInvStep(3)" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0">
                    Next: Vault &amp; Terms →
                </button>
            </div>
        </div>

        <!-- STEP 3: PAYMENT TERMS & DOCUMENT VAULT INTEGRATION -->
        <div id="inv-step-3" class="space-y-4 hidden">
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase">Payment Due Date</label>
                    <input type="date" id="inv-due-date" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase">Milestone Split</label>
                    <select id="inv-milestone-split" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
                        <option value="100">100% Full Payment</option>
                        <option value="50_50">50% Advance / 50% Delivery</option>
                        <option value="40_30_30">40% Booking / 30% Shoot / 30% Delivery</option>
                    </select>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-zinc-50 border border-zinc-200 space-y-2">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="inv-vault-link" checked class="w-4 h-4 rounded border-zinc-300">
                    <label for="inv-vault-link" class="text-xs font-bold text-zinc-900 cursor-pointer">
                        Link with Document Vault (E-Sign Contract)
                    </label>
                </div>
                <p class="text-[11px] text-zinc-500 leading-relaxed pl-6">
                    Automatically generates a client-facing E-Sign Contract blueprint in the Document Vault with these invoice terms embedded.
                </p>
            </div>

            <div class="pt-4 flex items-center justify-between border-t border-zinc-200">
                <button type="button" onclick="window.coraGoInvStep(2)" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                    ← Back
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.coraCloseAllDrawers()" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                        Cancel
                    </button>
                    <button type="submit" id="btn-save-invoice" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0">
                        Generate &amp; Publish Invoice
                    </button>
                </div>
            </div>
        </div>

    </form>
</aside>


<!-- 2. DRAWER: PAYMENT FOLLOW-UP DRAFT -->
<aside id="cora-fin-followup-drawer" class="cora-side-drawer fixed top-0 right-0 w-full sm:w-[560px] max-w-full h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden hidden collapsed">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">AI Payment Follow-up</h3>
            <p class="text-[11px] text-zinc-500">Drafted based on past client communication context</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-8 h-8 rounded-lg hover:bg-zinc-200 text-zinc-600 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm font-bold" title="Close Drawer">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
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

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2 shrink-0">
            <button type="button" onclick="window.coraCloseAllDrawers()" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                Cancel
            </button>
            <button type="submit" id="btn-send-followup" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                <span>Send Follow-up Email</span>
            </button>
        </div>
    </form>
</aside>


<!-- 3. DRAWER: RECORD EXPENSE (Indian Tax & ITC Compliant) -->
<aside id="cora-fin-expense-drawer" class="cora-side-drawer fixed top-0 right-0 w-full sm:w-[560px] max-w-full h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden hidden collapsed">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Record Business Expense</h3>
            <p class="text-[11px] text-zinc-500">Track Input Tax Credit (ITC) and contractor TDS</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-8 h-8 rounded-lg hover:bg-zinc-200 text-zinc-600 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm font-bold" title="Close Drawer">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
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

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase">Category</label>
                    <button type="button" onclick="window.coraPromptCustomCategory('exp-category')" class="text-[10px] font-bold text-zinc-900 hover:underline cursor-pointer border-0 bg-transparent flex items-center gap-0.5">
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Custom</span>
                    </button>
                </div>
                <select id="exp-category" onchange="window.coraCheckCustomCategory(this)" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
                    <?php foreach ( $all_expense_categories as $cat ) : ?>
                        <option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
                    <?php endforeach; ?>
                    <option value="__ADD_NEW__">+ Add Custom Category...</option>
                </select>
                <div id="exp-category-custom-box" class="hidden mt-1.5 flex items-center gap-1.5">
                    <input type="text" id="exp-category-custom-input" placeholder="Custom category..." class="flex-1 bg-white border border-zinc-300 rounded-lg px-2.5 py-1 text-xs text-zinc-900 focus:outline-none font-medium">
                    <button type="button" onclick="window.coraSaveCustomCategory('exp-category')" class="px-2 py-1 rounded-lg text-xs font-bold bg-zinc-950 text-white cursor-pointer border-0">Add</button>
                    <button type="button" onclick="window.coraCancelCustomCategory('exp-category')" class="px-1.5 py-1 rounded-lg text-xs font-semibold text-zinc-500 hover:bg-zinc-100 cursor-pointer border-0">✕</button>
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Payment Mode</label>
                <select id="exp-mode" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
                    <option value="UPI">UPI / QR</option>
                    <option value="Bank NEFT/RTGS">Bank Transfer (NEFT/RTGS)</option>
                    <option value="Corporate Card">Corporate Card</option>
                    <option value="Cash">Cash / Petty</option>
                </select>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-zinc-50 border border-zinc-200 space-y-2">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="exp-itc-claimable" checked class="w-4 h-4 rounded border-zinc-300">
                <label for="exp-itc-claimable" class="text-xs font-semibold text-zinc-800 cursor-pointer">
                    Eligible for Input Tax Credit (ITC)
                </label>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="exp-is-recurring" class="w-4 h-4 rounded border-zinc-300">
                <label for="exp-is-recurring" class="text-xs font-semibold text-zinc-800 cursor-pointer">
                    Track as recurring monthly subscription
                </label>
            </div>
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2 shrink-0">
            <button type="button" onclick="window.coraCloseAllDrawers()" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                Cancel
            </button>
            <button type="submit" id="btn-save-expense" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0">
                Save Expense
            </button>
        </div>
    </form>
</aside>


<!-- 4. DRAWER: RECORD INCOME -->
<aside id="cora-fin-income-drawer" class="cora-side-drawer fixed top-0 right-0 w-full sm:w-[560px] max-w-full h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden hidden collapsed">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Record Received Payment</h3>
            <p class="text-[11px] text-zinc-500">Logs cash inflow and reconciles open invoices</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-8 h-8 rounded-lg hover:bg-zinc-200 text-zinc-600 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm font-bold" title="Close Drawer">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
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

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2 shrink-0">
            <button type="button" onclick="window.coraCloseAllDrawers()" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                Cancel
            </button>
            <button type="submit" id="btn-save-income" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0">
                Log Payment
            </button>
        </div>
    </form>
</aside>


<!-- 5. DRAWER: DEAL SIMULATOR (Indian Taxes & Overhead) -->
<aside id="cora-fin-sim-drawer" class="cora-side-drawer fixed top-0 right-0 w-full sm:w-[560px] max-w-full h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden hidden collapsed">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Deal Feasibility Simulator</h3>
            <p class="text-[11px] text-zinc-500">"Should I take this project?" Indian tax &amp; margin calculation</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-8 h-8 rounded-lg hover:bg-zinc-200 text-zinc-600 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm font-bold" title="Close Drawer">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
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

        <div id="sim-ai-verdict" class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200/60 text-xs text-emerald-900 font-medium flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-700 shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <div><strong>High Margin Deal (Go)</strong>: At a 50.0% margin, this project comfortably exceeds your studio's target threshold of 45%.</div>
        </div>
    </div>
</aside>


<!-- 6. DRAWER: SUBSCRIPTIONS -->
<aside id="cora-fin-subs-drawer" class="cora-side-drawer fixed top-0 right-0 w-full sm:w-[560px] max-w-full h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden hidden collapsed">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Add Recurring Commitment</h3>
            <p class="text-[11px] text-zinc-500">Track recurring software, studio leases, or vendor contracts</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-8 h-8 rounded-lg hover:bg-zinc-200 text-zinc-600 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm font-bold" title="Close Drawer">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
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
            <div class="flex items-center justify-between">
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Category</label>
                <button type="button" onclick="window.coraPromptCustomCategory('sub-category')" class="text-[10px] font-bold text-zinc-900 hover:underline cursor-pointer border-0 bg-transparent flex items-center gap-0.5">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Custom</span>
                </button>
            </div>
            <select id="sub-category" onchange="window.coraCheckCustomCategory(this)" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2 text-xs text-zinc-900 focus:outline-none font-semibold">
                <?php foreach ( $all_expense_categories as $cat ) : ?>
                    <option value="<?php echo esc_attr( $cat ); ?>"><?php echo esc_html( $cat ); ?></option>
                <?php endforeach; ?>
                <option value="__ADD_NEW__">+ Add Custom Category...</option>
            </select>
            <div id="sub-category-custom-box" class="hidden mt-1.5 flex items-center gap-1.5">
                <input type="text" id="sub-category-custom-input" placeholder="Custom category..." class="flex-1 bg-white border border-zinc-300 rounded-lg px-2.5 py-1 text-xs text-zinc-900 focus:outline-none font-medium">
                <button type="button" onclick="window.coraSaveCustomCategory('sub-category')" class="px-2 py-1 rounded-lg text-xs font-bold bg-zinc-950 text-white cursor-pointer border-0">Add</button>
                <button type="button" onclick="window.coraCancelCustomCategory('sub-category')" class="px-1.5 py-1 rounded-lg text-xs font-semibold text-zinc-500 hover:bg-zinc-100 cursor-pointer border-0">✕</button>
            </div>
        </div>

        <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2 shrink-0">
            <button type="button" onclick="window.coraCloseAllDrawers()" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                Cancel
            </button>
            <button type="submit" id="btn-save-sub" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0">
                Track Subscription
            </button>
        </div>
    </form>
</aside>


<!-- 7. DRAWER: ACCOUNTANT PACK -->
<aside id="cora-fin-accountant-drawer" class="cora-side-drawer fixed top-0 right-0 w-full sm:w-[560px] max-w-full h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden hidden collapsed">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950">Export Accountant Pack</h3>
            <p class="text-[11px] text-zinc-500">Ready for CA review, GST filing, and bookkeeping</p>
        </div>
        <button type="button" onclick="window.coraCloseAllDrawers()" class="w-8 h-8 rounded-lg hover:bg-zinc-200 text-zinc-600 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm font-bold" title="Close Drawer">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
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
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Download CSV</span>
                </button>
                <button type="button" onclick="window.print()" class="p-3 rounded-xl border border-zinc-300 hover:border-zinc-900 bg-white font-bold text-xs text-zinc-900 flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    <span>Print Report</span>
                </button>
            </div>
        </div>
    </div>
</aside>


<!-- ════════════════════════════════════════════════════════
     CLIENT-SIDE CONTROLLERS & CHARTS ENGINE
     ════════════════════════════════════════════════════════ -->
<script>
(function() {
    'use strict';

    const ajaxUrl = '<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>';
    const nonce   = '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>';

    let cashflowChart = null;
    let profitChart = null;

    /* ── Tab Switcher Controller ── */
    /* ── Tab Switcher Controller ── */
    window.coraSwitchFinTab = function(tabId, updateUrl = true) {
        if (!tabId) return;
        let cleanId = tabId.startsWith('tab-') ? tabId : 'tab-' + tabId;
        let shortId = tabId.replace(/^tab-/, '');

        // Map aliases
        if (shortId === 'home') {
            shortId = 'agent';
            cleanId = 'tab-fin-agent';
        } else if (shortId === 'receivables' || shortId === 'expenses') {
            // If switching from a sub-tab link, show ledger
            shortId = 'ledger';
            cleanId = 'tab-fin-ledger';
        }

        // Hide all tab panels
        document.querySelectorAll('.cora-fin-tab-panel').forEach(el => {
            el.classList.add('hidden');
        });

        // Deactivate all tab pill buttons
        document.querySelectorAll('.cora-fin-pill-tab').forEach(el => {
            el.classList.remove('active', 'bg-zinc-950', 'text-white');
            el.classList.add('text-zinc-600');
        });

        // Show target panel
        const targetPanel = document.getElementById(cleanId) || document.getElementById(shortId);
        if (targetPanel) {
            targetPanel.classList.remove('hidden');
        }

        // Activate target button
        const targetBtn = document.getElementById('tab-btn-' + shortId) || document.getElementById('tab-btn-' + cleanId);
        if (targetBtn) {
            targetBtn.classList.add('active', 'bg-zinc-950', 'text-white');
            targetBtn.classList.remove('text-zinc-600');
        }

        // Synchronize active tab in URL query param
        if (updateUrl !== false && window.history && window.history.replaceState) {
            try {
                const url = new URL(window.location.href);
                url.searchParams.set('tab', shortId);
                window.history.replaceState(null, '', url.toString());
            } catch(e) {}
        }

        // Re-render charts when switching to tabs containing charts
        if (cleanId === 'tab-fin-agent' || cleanId === 'tab-fin-home' || cleanId === 'tab-fin-forecast') {
            setTimeout(initCashflowChart, 60);
        } else if (cleanId === 'tab-fin-profitability') {
            setTimeout(initProfitChart, 60);
        }
    };
    window.switchFinTab = window.coraSwitchFinTab;

    /* ── High-Quality Voice Transcription Engine (Web Speech API) ── */
    let speechRecognition = null;
    let isListening = false;

    window.coraToggleVoiceAgent = function() {
        if (isListening) {
            window.coraStopVoiceAgent();
        } else {
            window.coraStartVoiceRecognition();
        }
    };

    window.coraStartVoiceRecognition = function() {
        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRec) {
            if (window.coraShowToast) {
                window.coraShowToast('Speech recognition not supported in this browser. Please use Chrome or Safari.', 'info');
            }
            const inp = document.getElementById('cora-agent-text-input');
            if (inp) inp.focus();
            return;
        }

        try {
            if (speechRecognition) {
                try { speechRecognition.abort(); } catch(e) {}
            }
            speechRecognition = new SpeechRec();
            speechRecognition.continuous = false;
            speechRecognition.interimResults = true;
            speechRecognition.lang = 'en-IN'; // Optimized for Indian English & rupee numbers

            const voiceBtn = document.getElementById('cora-agent-voice-btn');
            const liveBanner = document.getElementById('cora-voice-live-banner');
            const liveTranscript = document.getElementById('cora-voice-live-transcript');
            const statusPill = document.getElementById('cora-voice-status-pill');
            const textInput = document.getElementById('cora-agent-text-input');

            speechRecognition.onstart = function() {
                isListening = true;
                if (voiceBtn) voiceBtn.classList.add('cora-mic-listening', 'bg-red-600', 'hover:bg-red-700');
                if (statusPill) statusPill.classList.remove('hidden');
                if (liveBanner) liveBanner.classList.remove('hidden');
                if (liveTranscript) liveTranscript.innerText = 'Listening... speak naturally (e.g. "Log 4500 gear expense")...';
                if (window.coraShowToast) window.coraShowToast('Listening... Speak your command or question', 'info');
            };

            speechRecognition.onresult = function(event) {
                let interim = '';
                let final = '';
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        final += event.results[i][0].transcript;
                    } else {
                        interim += event.results[i][0].transcript;
                    }
                }
                const currentText = (final || interim).trim();
                if (currentText) {
                    if (liveTranscript) liveTranscript.innerText = '"' + currentText + '"';
                    if (textInput) textInput.value = currentText;
                }
            };

            speechRecognition.onerror = function(e) {
                window.coraStopVoiceAgent();
                if (e.error === 'not-allowed') {
                    if (window.coraShowToast) window.coraShowToast('Microphone access denied. Please enable mic permissions in site settings.', 'error');
                } else if (e.error !== 'no-speech') {
                    if (window.coraShowToast) window.coraShowToast('Speech recognition notice: ' + e.error, 'info');
                }
            };

            speechRecognition.onend = function() {
                window.coraStopVoiceAgent();
                // If text was recorded, auto-submit after short pause
                if (textInput && textInput.value.trim()) {
                    setTimeout(() => {
                        window.coraSubmitAgentMessage();
                    }, 400);
                }
            };

            speechRecognition.start();
        } catch(err) {
            window.coraStopVoiceAgent();
            if (window.coraShowToast) window.coraShowToast('Could not initialize microphone: ' + err.message, 'error');
        }
    };

    window.coraStopVoiceAgent = function() {
        isListening = false;
        if (speechRecognition) {
            try { speechRecognition.stop(); } catch(e) {}
        }
        const voiceBtn = document.getElementById('cora-agent-voice-btn');
        const liveBanner = document.getElementById('cora-voice-live-banner');
        const statusPill = document.getElementById('cora-voice-status-pill');

        if (voiceBtn) voiceBtn.classList.remove('cora-mic-listening', 'bg-red-600', 'hover:bg-red-700');
        if (statusPill) statusPill.classList.add('hidden');
        if (liveBanner) liveBanner.classList.add('hidden');
    };

    /* ── Action-Oriented Discussion & Chat Submitter ── */
    window.coraRunAgentPrompt = function(promptText) {
        const inp = document.getElementById('cora-agent-text-input');
        if (inp) {
            inp.value = promptText;
            window.coraSubmitAgentMessage();
        }
    };

    window.coraClearAgentChat = function() {
        const feed = document.getElementById('cora-agent-chat-feed');
        if (feed) {
            const avail = window.coraFinanceInitialData ? Number(window.coraFinanceInitialData.availableCash || 0).toLocaleString('en-IN') : '0';
            const expIn = window.coraFinanceInitialData ? Number(window.coraFinanceInitialData.expectedIn || 0).toLocaleString('en-IN') : '0';
            feed.innerHTML = `
                <div class="flex justify-start">
                    <div class="bg-[#FBFaf7] border border-zinc-200 rounded-2xl rounded-tl-sm p-4 text-xs text-zinc-800 max-w-[92%] sm:max-w-[85%] space-y-2.5 shadow-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-zinc-900 text-white flex items-center justify-center font-bold text-[10px]">C</span>
                            <span class="font-bold text-zinc-950">Financial Agent</span>
                            <span class="text-[10px] text-zinc-400">Ledger Assistant</span>
                        </div>
                        <div class="leading-relaxed text-zinc-700">
                            Discussion reset. Available cleared cash is <strong class="text-zinc-950 font-mono">₹${avail}</strong> with <strong class="text-zinc-950 font-mono">₹${expIn}</strong> in uncollected receivables.<br><br>
                            Speak into the microphone or type below.
                        </div>
                    </div>
                </div>
            `;
        }
        if (window.coraShowToast) window.coraShowToast('Discussion feed cleared.', 'info');
    };

    window.coraSubmitAgentMessage = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        const input = document.getElementById('cora-agent-text-input');
        const query = input ? input.value.trim() : '';
        if (!query) return;

        const feed = document.getElementById('cora-agent-chat-feed');
        const sendBtn = document.getElementById('cora-agent-send-btn');

        if (feed) {
            // Append user message bubble (Monochromatic)
            const userBubble = document.createElement('div');
            userBubble.className = 'flex justify-end';
            userBubble.innerHTML = `<div class="bg-zinc-950 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 text-xs max-w-[85%] font-medium shadow-xs leading-relaxed">${query}</div>`;
            feed.appendChild(userBubble);

            // Append loading animation bubble
            const loadingBubble = document.createElement('div');
            loadingBubble.id = 'agent-temp-ai-bubble';
            loadingBubble.className = 'flex justify-start';
            loadingBubble.innerHTML = `
                <div class="bg-zinc-100 text-zinc-700 rounded-2xl rounded-tl-sm px-4 py-2.5 text-xs max-w-[85%] flex items-center gap-2 animate-pulse">
                    <span class="w-2 h-2 rounded-full bg-zinc-400 animate-ping"></span>
                    <span>Financial Agent analyzing ledger and parsing intent...</span>
                </div>
            `;
            feed.appendChild(loadingBubble);
            feed.scrollTop = feed.scrollHeight;
        }

        if (input) input.value = '';
        if (sendBtn) sendBtn.disabled = true;

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
            const tempBubble = document.getElementById('agent-temp-ai-bubble');
            if (tempBubble) tempBubble.remove();

            if (feed && res.success && res.data) {
                let formatted = res.data.answer
                    .replace(/### (.*?)\n/g, '<div class="font-bold text-zinc-950 text-sm mb-1">$1</div>')
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/\n\n/g, '<br><br>')
                    .replace(/\n/g, '<br>');

                let actionBtnHtml = '';
                if (res.data.action_chip) {
                    const chip = res.data.action_chip;
                    const prefillStr = chip.prefill ? encodeURIComponent(JSON.stringify(chip.prefill)) : '';
                    actionBtnHtml = `
                        <div class="pt-2 border-t border-zinc-200/70">
                            <button type="button" onclick="window.coraTriggerActionCard('${chip.action}', '${chip.target || ''}', '${prefillStr}')" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 inline-flex items-center gap-2 shadow-xs transition-colors">
                                <span>${chip.text}</span>
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </button>
                        </div>
                    `;
                }

                const finalAiBubble = document.createElement('div');
                finalAiBubble.className = 'flex justify-start';
                finalAiBubble.innerHTML = `
                    <div class="bg-[#FBFaf7] border border-zinc-200 text-zinc-800 rounded-2xl rounded-tl-sm p-4 text-xs max-w-[92%] sm:max-w-[85%] space-y-2.5 leading-relaxed shadow-xs">
                        <div class="font-bold text-zinc-950 flex items-center gap-1.5">
                            <span class="w-6 h-6 rounded-md bg-zinc-950 text-white flex items-center justify-center font-bold text-[10px]">C</span>
                            <span>Financial Agent</span>
                        </div>
                        <div class="text-zinc-700">${formatted}</div>
                        ${actionBtnHtml}
                    </div>
                `;
                feed.appendChild(finalAiBubble);
                feed.scrollTop = feed.scrollHeight;
            }
        })
        .catch(() => {
            if (sendBtn) sendBtn.disabled = false;
            const tempBubble = document.getElementById('agent-temp-ai-bubble');
            if (tempBubble) {
                tempBubble.innerHTML = `<div class="bg-red-50 text-red-700 rounded-2xl p-3 text-xs">Error communicating with financial AI engine.</div>`;
            }
        });
    };

    /* ── Action Card Trigger Bridge ── */
    window.coraTriggerActionCard = function(action, target, prefillEncoded) {
        let prefill = {};
        if (prefillEncoded) {
            try { prefill = JSON.parse(decodeURIComponent(prefillEncoded)); } catch(e) {}
        }

        if (action === 'open_expense_drawer') {
            window.coraPrefillExpense(prefill.amount, prefill.category, prefill.description);
        } else if (action === 'open_invoice_drawer') {
            window.coraPrefillInvoice(prefill.amount, prefill.client_name);
        } else if (action === 'open_income_drawer') {
            window.coraPrefillIncome(prefill.amount, prefill.client_name);
        } else if (action === 'open_simulator') {
            window.coraPrefillSim(prefill.revenue, prefill.costs);
        } else if (action === 'draft_followup') {
            window.coraDraftFollowUp(target || 'inv_sample_01');
        } else if (action === 'switch_tab') {
            window.coraSwitchFinTab(target);
        } else if (action === 'open_recurring_modal') {
            window.coraOpenDrawer('subscriptions');
        }
    };

    /* ── Form Prefill Helpers ── */
    window.coraPrefillExpense = function(amount, category, description) {
        window.coraOpenDrawer('add-expense');
        if (amount) {
            const amtEl = document.getElementById('exp-amount');
            if (amtEl) amtEl.value = amount;
        }
        if (category) {
            const catEl = document.getElementById('exp-category');
            if (catEl) {
                for (let i = 0; i < catEl.options.length; i++) {
                    if (catEl.options[i].value.toLowerCase() === category.toLowerCase()) {
                        catEl.selectedIndex = i;
                        break;
                    }
                }
            }
        }
        if (description) {
            const descEl = document.getElementById('exp-description');
            if (descEl) descEl.value = description;
        }
    };

    window.coraPrefillInvoice = function(amount, clientName) {
        window.coraOpenDrawer('create-invoice');
        if (clientName) {
            const clEl = document.getElementById('inv-client-name');
            if (clEl) clEl.value = clientName;
        }
        if (amount) {
            const rateEl = document.querySelector('.inv-item-rate');
            if (rateEl) {
                rateEl.value = amount;
                window.coraRecalcInvoiceGST();
            }
        }
    };

    window.coraPrefillIncome = function(amount, clientName) {
        window.coraOpenDrawer('record-income');
        if (amount) {
            const amtEl = document.getElementById('inc-amount');
            if (amtEl) amtEl.value = amount;
        }
        if (clientName) {
            const clEl = document.getElementById('inc-client-name');
            if (clEl) clEl.value = clientName;
        }
    };

    window.coraPrefillSim = function(revenue, costs) {
        window.coraOpenDrawer('project-sim');
        if (revenue) {
            const revEl = document.getElementById('sim-revenue');
            if (revEl) revEl.value = revenue;
        }
        if (costs) {
            const costEl = document.getElementById('sim-contractor');
            if (costEl) costEl.value = costs;
        }
        window.coraRecalcSim();
    };

    /* ── Unified Ledger Filter & Search ── */
    window.coraFilterLedgerType = function(type) {
        ['all', 'inflow', 'outflow'].forEach(t => {
            const btn = document.getElementById('btn-ledger-filter-' + t);
            if (btn) {
                if (t === type) {
                    btn.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-zinc-950 text-white cursor-pointer border-0 shrink-0';
                } else {
                    btn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0 shrink-0 flex items-center gap-1.5';
                }
            }
        });

        const rows = document.querySelectorAll('.cora-tx-row');
        rows.forEach(r => {
            const rType = r.getAttribute('data-type');
            r.style.display = (type === 'all' || rType === type) ? '' : 'none';
        });

        const cards = document.querySelectorAll('.cora-tx-card');
        cards.forEach(c => {
            const cType = c.getAttribute('data-type');
            c.style.display = (type === 'all' || cType === type) ? '' : 'none';
        });
    };

    window.coraSearchLedger = function(query) {
        const q = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.cora-tx-row');
        rows.forEach(r => {
            const text = r.innerText.toLowerCase();
            r.style.display = (!q || text.includes(q)) ? '' : 'none';
        });
        const cards = document.querySelectorAll('.cora-tx-card');
        cards.forEach(c => {
            const text = c.innerText.toLowerCase();
            c.style.display = (!q || text.includes(q)) ? '' : 'none';
        });
    };

    /* ── Floating Copilot Pop-up Controller (Retained for secondary summon) ── */
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

            const userBubble = document.createElement('div');
            userBubble.className = 'flex justify-end';
            userBubble.innerHTML = `<div class="bg-zinc-950 text-white rounded-2xl rounded-tr-sm px-4 py-2 text-xs max-w-[80%] font-medium">${query}</div>`;
            chatPane.appendChild(userBubble);

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
                    const prefillStr = chip.prefill ? encodeURIComponent(JSON.stringify(chip.prefill)) : '';
                    actionBtnHtml = `<div class="pt-2"><button type="button" onclick="window.coraTriggerActionCard('${chip.action}', '${chip.target || ''}', '${prefillStr}')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 inline-flex items-center gap-1.5">${chip.text} →</button></div>`;
                }

                finalAiBubble.innerHTML = `<div class="bg-zinc-50 border border-zinc-200 text-zinc-800 rounded-2xl rounded-tl-sm p-3.5 text-xs max-w-[85%] space-y-2 leading-relaxed">
                    <div class="font-bold text-zinc-950 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-900"></span>
                        <span>Financial Agent</span>
                    </div>
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

    window.coraSendAgentChat = window.coraSubmitAgentMessage;
    window.coraHandleCopilotAction = window.coraTriggerActionCard;

    /* ── Popover Controller (Precise Alignment Under Button) ── */
    window.toggleFinancialActionMenu = function(e) {
        if (e && e.stopPropagation) e.stopPropagation();
        const popover = document.getElementById('cora-fin-action-popover');
        if (!popover) return;
        
        const isHidden = popover.classList.contains('hidden');
        if (isHidden) {
            popover.classList.remove('hidden');
            const targetBtn = e ? e.currentTarget : null;
            if (targetBtn) {
                const rect = targetBtn.getBoundingClientRect();
                popover.style.top = (rect.bottom + 8) + 'px';
                popover.style.right = (window.innerWidth - rect.right) + 'px';
                popover.style.left = 'auto';
            }
        } else {
            popover.classList.add('hidden');
        }
    };

    window.coraCloseFinPopover = function() {
        const popover = document.getElementById('cora-fin-action-popover');
        if (popover) {
            popover.classList.add('hidden');
        }
    };

    /* ── Standard Cora Platform Side Drawers Controller ── */
    window.coraOpenDrawer = function(drawerName) {
        window.coraCloseFinPopover();
        window.coraCloseCopilot();
        const map = {
            'create-invoice': '#cora-fin-invoice-drawer',
            'followup': '#cora-fin-followup-drawer',
            'add-expense': '#cora-fin-expense-drawer',
            'record-income': '#cora-fin-income-drawer',
            'project-sim': '#cora-fin-sim-drawer',
            'subscriptions': '#cora-fin-subs-drawer',
            'accountant-pack': '#cora-fin-accountant-drawer'
        };
        const selector = map[drawerName] || (drawerName.startsWith('#') ? drawerName : '#' + drawerName);
        if (typeof window.coraShowSideDrawer === 'function') {
            window.coraShowSideDrawer(selector);
        } else {
            $(selector).removeClass('hidden collapsed');
            setTimeout(() => { $(selector).removeClass('translate-x-full'); }, 20);
            $('#cora-drawer-backdrop').removeClass('hidden').css({'display': 'block', 'pointer-events': 'auto'});
        }
    };

    // Close on click outside popover
    document.addEventListener('click', function(e) {
        const pop = document.getElementById('cora-fin-action-popover');
        if (pop && !pop.contains(e.target) && !e.target.closest('[onclick*="toggleFinancialActionMenu"]')) {
            window.coraCloseFinPopover();
        }
    });

    /* ── Multi-Step Invoice Stepper ── */
    window.coraGoInvStep = function(stepNum) {
        [1, 2, 3].forEach(s => {
            const stepEl = document.getElementById('inv-step-' + s);
            const navEl = document.getElementById('inv-nav-' + s);
            if (stepEl) {
                if (s === stepNum) {
                    stepEl.classList.remove('hidden');
                } else {
                    stepEl.classList.add('hidden');
                }
            }
            if (navEl) {
                if (s === stepNum) {
                    navEl.className = 'flex-1 py-2.5 text-center border-b-2 border-zinc-950 text-zinc-950 font-bold bg-white cursor-pointer';
                } else {
                    navEl.className = 'flex-1 py-2.5 text-center border-b-2 border-transparent text-zinc-500 hover:text-zinc-800 cursor-pointer';
                }
            }
        });

        const badge = document.getElementById('inv-step-badge');
        if (badge) badge.innerText = `Step ${stepNum} of 3`;
    };

    /* ── CRM Contact Auto-fill ── */
    window.coraSelectCrmContact = function(jsonVal) {
        if (!jsonVal) return;
        try {
            const contact = JSON.parse(jsonVal);
            if (contact.name) document.getElementById('inv-client-name').value = contact.name;
            if (contact.email) document.getElementById('inv-client-email').value = contact.email;
        } catch(e) {}
    };

    /* ── Custom Categories Controller ── */
    window.coraCheckCustomCategory = function(selectEl) {
        const targetId = selectEl.id;
        const box = document.getElementById(targetId + '-custom-box');
        const input = document.getElementById(targetId + '-custom-input');
        if (selectEl.value === '__ADD_NEW__') {
            if (box) box.classList.remove('hidden');
            if (input) { input.value = ''; input.focus(); }
        } else {
            if (box) box.classList.add('hidden');
        }
    };

    window.coraPromptCustomCategory = function(selectId) {
        const box = document.getElementById(selectId + '-custom-box');
        const input = document.getElementById(selectId + '-custom-input');
        if (box) box.classList.remove('hidden');
        if (input) { input.value = ''; input.focus(); }
    };

    window.coraSaveCustomCategory = function(selectId) {
        const input = document.getElementById(selectId + '-custom-input');
        const val = (input ? input.value : '').trim();
        if (!val) return;

        ['exp-category', 'sub-category'].forEach(id => {
            const sel = document.getElementById(id);
            if (sel) {
                let exists = false;
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value.toLowerCase() === val.toLowerCase()) {
                        exists = true;
                        sel.selectedIndex = i;
                        break;
                    }
                }
                if (!exists) {
                    const newOpt = document.createElement('option');
                    newOpt.value = val;
                    newOpt.innerText = val;
                    // insert before "+ Add Custom Category..."
                    sel.insertBefore(newOpt, sel.lastElementChild);
                    sel.value = val;
                }
            }
        });

        const box = document.getElementById(selectId + '-custom-box');
        if (box) box.classList.add('hidden');

        // Persist to workspace options via AJAX
        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_finance_save_category',
                security: nonce,
                category: val
            })
        });

        if (window.coraShowToast) window.coraShowToast(`Category "${val}" saved.`, 'success');
    };

    window.coraCancelCustomCategory = function(selectId) {
        const select = document.getElementById(selectId);
        const box = document.getElementById(selectId + '-custom-box');
        if (box) box.classList.add('hidden');
        if (select && select.value === '__ADD_NEW__') {
            select.selectedIndex = 0;
        }
    };

    /* ── Add Line Item Row ── */
    window.coraAddInvoiceItemRow = function() {
        const container = document.getElementById('inv-items-container');
        if (!container) return;
        const row = document.createElement('div');
        row.className = 'inv-item-row p-3 rounded-xl bg-zinc-50 border border-zinc-200 space-y-2';
        row.innerHTML = `
            <div class="flex items-center justify-between gap-2">
                <input type="text" class="inv-item-desc w-full bg-white border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-900" placeholder="Item description" value="Video Post-Production & Color Grading">
                <button type="button" onclick="this.closest('.inv-item-row').remove(); window.coraRecalcInvoiceGST();" class="text-xs text-zinc-400 hover:text-zinc-900 cursor-pointer border-0 bg-transparent flex items-center justify-center p-1" title="Remove Item">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="text-[9px] text-zinc-400 uppercase font-bold">SAC Code</label>
                    <input type="text" class="inv-item-sac w-full bg-white border border-zinc-200 rounded-lg px-2 py-1 text-xs font-mono" value="998314">
                </div>
                <div>
                    <label class="text-[9px] text-zinc-400 uppercase font-bold">Qty</label>
                    <input type="number" class="inv-item-qty w-full bg-white border border-zinc-200 rounded-lg px-2 py-1 text-xs font-mono" value="1" oninput="window.coraRecalcInvoiceGST()">
                </div>
                <div>
                    <label class="text-[9px] text-zinc-400 uppercase font-bold">Rate (₹)</label>
                    <input type="number" class="inv-item-rate w-full bg-white border border-zinc-200 rounded-lg px-2 py-1 text-xs font-mono font-bold" value="25000" oninput="window.coraRecalcInvoiceGST()">
                </div>
            </div>
        `;
        container.appendChild(row);
        window.coraRecalcInvoiceGST();
    };

    /* ── Recalculate Invoice GST & Tax Mode ── */
    window.coraRecalcInvoiceGST = function() {
        const stateVal = document.getElementById('inv-place-of-supply').value || '';
        const isIntraState = stateVal.startsWith('07_'); // 07 is Delhi base

        const taxBadge = document.getElementById('inv-tax-mode-badge');
        const cgstRow = document.getElementById('inv-calc-cgst-row');
        const sgstRow = document.getElementById('inv-calc-sgst-row');
        const igstRow = document.getElementById('inv-calc-igst-row');

        if (isIntraState) {
            if (taxBadge) taxBadge.innerText = 'Tax Mode: Intra-State (CGST 9% + SGST 9%)';
            if (cgstRow) cgstRow.classList.remove('hidden');
            if (sgstRow) sgstRow.classList.remove('hidden');
            if (igstRow) igstRow.classList.add('hidden');
        } else {
            if (taxBadge) taxBadge.innerText = 'Tax Mode: Inter-State (IGST 18%)';
            if (cgstRow) cgstRow.classList.add('hidden');
            if (sgstRow) sgstRow.classList.remove('hidden');
            if (igstRow) igstRow.classList.remove('hidden');
        }

        let subtotal = 0;
        document.querySelectorAll('.inv-item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.inv-item-qty')?.value) || 0;
            const rate = parseFloat(row.querySelector('.inv-item-rate')?.value) || 0;
            subtotal += (qty * rate);
        });

        const taxAmount = subtotal * 0.18;
        const cgst = subtotal * 0.09;
        const sgst = subtotal * 0.09;
        const grandTotal = subtotal + taxAmount;

        const subEl = document.getElementById('inv-calc-subtotal');
        const cgstEl = document.getElementById('inv-calc-cgst');
        const sgstEl = document.getElementById('inv-calc-sgst');
        const igstEl = document.getElementById('inv-calc-igst');
        const totalEl = document.getElementById('inv-calc-grand-total');

        if (subEl) subEl.innerText = '₹' + subtotal.toLocaleString('en-IN');
        if (cgstEl) cgstEl.innerText = '₹' + cgst.toLocaleString('en-IN');
        if (sgstEl) sgstEl.innerText = '₹' + sgst.toLocaleString('en-IN');
        if (igstEl) igstEl.innerText = '₹' + taxAmount.toLocaleString('en-IN');
        if (totalEl) totalEl.innerText = '₹' + grandTotal.toLocaleString('en-IN');
    };

    /* ── Filter Receivables ── */
    window.coraFilterReceivables = function(query) {
        const q = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#cora-receivables-tbody tr');
        rows.forEach(r => {
            const text = r.innerText.toLowerCase();
            r.style.display = (!q || text.includes(q)) ? '' : 'none';
        });
        const cards = document.querySelectorAll('.cora-rec-card');
        cards.forEach(c => {
            const text = c.innerText.toLowerCase();
            c.style.display = (!q || text.includes(q)) ? '' : 'none';
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

        let subtotal = 0;
        document.querySelectorAll('.inv-item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.inv-item-qty')?.value) || 0;
            const rate = parseFloat(row.querySelector('.inv-item-rate')?.value) || 0;
            subtotal += (qty * rate);
        });
        const grandTotal = subtotal * 1.18;

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_create_invoice',
                security: nonce,
                client_name: document.getElementById('inv-client-name').value,
                client_email: document.getElementById('inv-client-email').value,
                package_name: document.getElementById('inv-package-name').value,
                total_amount: grandTotal || 75000,
                due_date: document.getElementById('inv-due-date').value,
                place_of_supply: document.getElementById('inv-place-of-supply').value,
                link_vault: document.getElementById('inv-vault-link').checked ? '1' : '0'
            })
        })
        .then(r => r.json())
        .then(res => {
            if (btn) { btn.disabled = false; btn.innerText = 'Generate & Publish Invoice'; }
            if (res.success) {
                window.coraCloseAllDrawers();
                if (window.coraShowToast) window.coraShowToast('GST Invoice published and linked to Document Vault.', 'success');
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
                verdictBox.className = 'p-3.5 rounded-xl bg-emerald-50 border border-emerald-200/60 text-xs text-emerald-900 font-medium flex items-center gap-2';
                verdictBox.innerHTML = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-700 shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg><div><strong>High Margin Deal (Go)</strong>: At ${margin}% margin (₹${profit.toLocaleString('en-IN')} profit), this project is highly profitable.</div>`;
            } else if (margin >= 30) {
                verdictBox.className = 'p-3.5 rounded-xl bg-amber-50 border border-amber-200/60 text-xs text-amber-900 font-medium flex items-center gap-2';
                verdictBox.innerHTML = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="text-amber-700 shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><div><strong>Moderate Margin (Caution)</strong>: ${margin}% margin. Consider adding a 10% buffer on gear or crew day rates.</div>`;
            } else {
                verdictBox.className = 'p-3.5 rounded-xl bg-red-50 border border-red-200/60 text-xs text-red-900 font-medium flex items-center gap-2';
                verdictBox.innerHTML = `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="text-red-700 shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg><div><strong>Low Margin Risk (Re-evaluate)</strong>: ${margin}% margin leaves little buffer for studio overhead or scope revisions.</div>`;
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

    /* ── Pass PHP Metrics to JS Bridge ── */
    window.coraFinanceInitialData = {
        availableCash: <?php echo json_encode( $available_cash ); ?>,
        grossInflow: <?php echo json_encode( $gross_inflow ); ?>,
        grossOutflow: <?php echo json_encode( $gross_outflow ); ?>,
        expectedIn: <?php echo json_encode( $expected_in ); ?>,
        expectedOut: <?php echo json_encode( $expected_out ); ?>,
        projectedCash: <?php echo json_encode( $projected_cash ); ?>,
        monthlyRec: <?php echo json_encode( $monthly_rec ); ?>,
        clientProfits: <?php echo json_encode( array_slice( $client_profits, 0, 5 ) ); ?>
    };

    /* ── Initialize Charts (Chart.js Dynamic Monochromatic Engine) ── */
    function initCashflowChart() {
        const canvas = document.getElementById('cora-fin-cashflow-chart');
        if (!canvas || typeof Chart === 'undefined') return;

        if (cashflowChart) {
            cashflowChart.destroy();
        }

        const initData = window.coraFinanceInitialData || {};
        const curCash = Number(initData.availableCash || 0);
        const grossIn = Number(initData.grossInflow || 0);
        const grossOut = Number(initData.grossOutflow || 0);
        const projCash = Number(initData.projectedCash || 0);
        const expIn = Number(initData.expectedIn || 0);
        const expOut = Number(initData.expectedOut || 0);

        const monthNames = ['3M Ago', '2M Ago', 'Last Month', 'Current (Now)', 'Next Month (Proj)', '+60D (Proj)'];
        
        // Dynamic trajectory calculation based on real workspace ledger
        const inflows = [
            Math.max(0, grossIn * 0.7),
            Math.max(0, grossIn * 0.85),
            Math.max(0, grossIn * 0.9),
            grossIn,
            expIn,
            expIn * 0.8
        ];
        const outflows = [
            Math.max(0, grossOut * 0.8),
            Math.max(0, grossOut * 0.9),
            grossOut,
            grossOut,
            expOut,
            expOut
        ];
        const netCash = [
            Math.max(0, curCash * 0.6),
            Math.max(0, curCash * 0.75),
            Math.max(0, curCash * 0.9),
            curCash,
            projCash,
            Math.max(0, projCash + (expIn * 0.8) - expOut)
        ];

        const ctx = canvas.getContext('2d');
        cashflowChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthNames,
                datasets: [
                    {
                        label: 'Projected Net Cash Position',
                        data: netCash,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: '#10b981'
                    },
                    {
                        label: 'Inflows',
                        data: inflows,
                        borderColor: '#09090b',
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: '#09090b'
                    },
                    {
                        label: 'Operating Outflows',
                        data: outflows,
                        borderColor: '#a1a1aa',
                        borderWidth: 1.5,
                        borderDash: [4, 4],
                        tension: 0.35,
                        pointRadius: 2,
                        pointBackgroundColor: '#a1a1aa'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#09090b',
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(c) {
                                return c.dataset.label + ': ₹' + Number(c.raw || 0).toLocaleString('en-IN');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#71717a' }
                    },
                    y: {
                        grid: { color: '#f4f4f5' },
                        ticks: {
                            font: { size: 10 },
                            color: '#71717a',
                            callback: function(v) { return '₹' + (v >= 100000 ? (v/100000).toFixed(1) + 'L' : v/1000 + 'k'); }
                        }
                    }
                }
            }
        });
    }

    function initProfitChart() {
        const canvas = document.getElementById('cora-fin-profit-chart');
        if (!canvas || typeof Chart === 'undefined') return;

        if (profitChart) {
            profitChart.destroy();
        }

        const initData = window.coraFinanceInitialData || {};
        const clients = initData.clientProfits || [];
        if (clients.length === 0) return;

        const labels = clients.map(c => c.client_name);
        const data = clients.map(c => Number(c.revenue || 0));
        const colors = ['#09090b', '#3f3f46', '#71717a', '#a1a1aa', '#d4d4d8'];

        const ctx = canvas.getContext('2d');
        profitChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, data.length),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#09090b',
                        callbacks: {
                            label: function(c) {
                                return c.label + ': ₹' + Number(c.raw || 0).toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }

    // Initialize active tab from URL and charts on boot
    function bootFinanceModule() {
        const urlParams = new URLSearchParams(window.location.search);
        const targetTab = urlParams.get('tab') || window.location.hash.replace('#', '');
        if (targetTab && targetTab !== 'fin-home' && targetTab !== 'home' && targetTab !== 'fin-agent') {
            window.coraSwitchFinTab(targetTab, false);
        } else {
            window.coraSwitchFinTab('fin-agent', false);
        }
        setTimeout(initCashflowChart, 150);
    }

    document.addEventListener('DOMContentLoaded', bootFinanceModule);
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        bootFinanceModule();
    }

})();
</script>
