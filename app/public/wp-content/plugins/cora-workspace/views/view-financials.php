<?php
/**
 * Cora Workspace - Modular Financial View
 * 
 * Clean, Borderless Monochromatic Palette (Slate/Zinc/Gray tones, zero harsh outlines).
 * Matching reference Financial Overview dashboard design.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Fetch ledger or build fallback datasets
$financial_entries = function_exists( 'cora_db_get_ledger' ) ? cora_db_get_ledger() : array();

$kpi_inflow = 0.0;
$kpi_outflow = 0.0;
if ( is_array( $financial_entries ) ) {
    foreach ( $financial_entries as $entry ) {
        $raw_type = strtolower( trim( $entry['type'] ?? 'inflow' ) );
        $is_inflow = ( $raw_type === 'income' || $raw_type === 'inflow' );
        $amt = floatval( $entry['amount'] ?? 0 );
        if ( $is_inflow ) {
            $kpi_inflow += $amt;
        } else {
            $kpi_outflow += $amt;
        }
    }
}

$kpi_profit = $kpi_inflow - $kpi_outflow;
$kpi_margin = $kpi_inflow > 0 ? round( ($kpi_profit / $kpi_inflow) * 100, 1 ) : 0;

$invoices = get_option( 'cora_invoices', array() );
$kpi_receivables = 0.0;
$kpi_receivables_count = 0;
if ( is_array( $invoices ) ) {
    foreach ( $invoices as $inv ) {
        if ( isset( $inv['status'] ) && strtolower( $inv['status'] ) === 'pending' ) {
            $kpi_receivables += floatval( $inv['due_balance'] ?? $inv['amount'] ?? 0 );
            $kpi_receivables_count++;
        }
    }
}
if ( $kpi_receivables <= 0 ) {
    $kpi_receivables = 0;
    $kpi_receivables_count = 0;
}

// Combination Chart Calculations: last 6 months dynamically (from current month back 5 months)
$months = array();
for ( $i = 5; $i >= 0; $i-- ) {
    $m_key = date( 'Y-m', strtotime( "-$i months" ) );
    $months[$m_key] = array(
        'label' => date( 'M', strtotime( "-$i months" ) ),
        'inflow' => 0.0,
        'outflow' => 0.0,
    );
}

if ( is_array( $financial_entries ) ) {
    foreach ( $financial_entries as $entry ) {
        $date_str = $entry['date'] ?? '';
        $m_key = substr( $date_str, 0, 7 ); // 'YYYY-MM'
        if ( isset( $months[$m_key] ) ) {
            $raw_type = strtolower( trim( $entry['type'] ?? 'inflow' ) );
            $is_inflow = ( $raw_type === 'income' || $raw_type === 'inflow' );
            $amt = floatval( $entry['amount'] ?? 0 );
            if ( $is_inflow ) {
                $months[$m_key]['inflow'] += $amt;
            } else {
                $months[$m_key]['outflow'] += $amt;
            }
        }
    }
}

// Generate beautiful climbing trend data for empty state mock representation
$has_actual_data = false;
if ( is_array( $financial_entries ) && ! empty( $financial_entries ) ) {
    foreach ( $financial_entries as $entry ) {
        if ( floatval( $entry['amount'] ?? 0 ) > 0 ) {
            $has_actual_data = true;
            break;
        }
    }
}

if ( ! $has_actual_data ) {
    $mock_inflows  = array( 8000.0, 12000.0, 18000.0, 25000.0, 36000.0, 48000.0 );
    $mock_outflows = array( 5000.0,  7000.0, 11000.0, 15000.0, 22000.0, 38000.0 );
    $mock_idx = 0;
    foreach ( $months as $m_key => $data ) {
        $months[$m_key]['inflow'] = $mock_inflows[$mock_idx] ?? 0.0;
        $months[$m_key]['outflow'] = $mock_outflows[$mock_idx] ?? 0.0;
        $mock_idx++;
    }
}

// Find maximum value for scaling Y axis
$max_val = 1000.0;
foreach ( $months as $m_key => $data ) {
    if ( $data['inflow'] > $max_val )  $max_val = $data['inflow'];
    if ( $data['outflow'] > $max_val ) $max_val = $data['outflow'];
}
$max_scale = ceil( $max_val / 10000 ) * 10000;
if ( $max_scale < 50000 ) $max_scale = 50000;

// Grid lines and labels
$scale_y1 = '₹' . round( $max_scale / 1000 ) . 'K';
$scale_y2 = '₹' . round( ($max_scale * 2 / 3) / 1000 ) . 'K';
$scale_y3 = '₹' . round( ($max_scale / 3) / 1000 ) . 'K';

$idx = 0;
$chart_elements = array();
$profit_points = array();
$cumulative_profit = 0.0;

foreach ( $months as $m_key => $data ) {
    $x_center = 78 + ( $idx * 85 ); // coordinates mapping
    $x_bar1 = $x_center - 13;
    $x_bar2 = $x_center + 2;
    
    $inflow_h = ( $data['inflow'] / $max_scale ) * 150;
    $inflow_y = 170 - $inflow_h;
    
    $outflow_h = ( $data['outflow'] / $max_scale ) * 150;
    $outflow_y = 170 - $outflow_h;
    
    // Net profit for the month
    $month_profit = $data['inflow'] - $data['outflow'];
    $cumulative_profit += $month_profit;
    
    // Line charts display net cumulative profit
    if ( $cumulative_profit < 0 ) {
        $profit_y = 170;
    } else {
        $profit_y = 170 - ( $cumulative_profit / $max_scale ) * 150;
    }
    
    if ( $profit_y > 170 ) $profit_y = 170;
    if ( $profit_y < 20 ) $profit_y = 20;

    $chart_elements[] = array(
        'label' => $data['label'],
        'x_center' => $x_center,
        'x_bar1' => $x_bar1,
        'x_bar2' => $x_bar2,
        'inflow_h' => $inflow_h,
        'inflow_y' => $inflow_y,
        'outflow_h' => $outflow_h,
        'outflow_y' => $outflow_y,
        'profit_y' => $profit_y
    );
    
    $profit_points[] = "$x_center $profit_y";
    $idx++;
}
$path_d = "M " . implode( " L ", $profit_points );

// Donut Chart Calculations
$donut_inflow = $kpi_inflow;
if ( $donut_inflow <= 0 ) {
    $donut_inflow = 485000.0; // fallback to prevent division by zero
}

$cat_rent = 0.0;
$cat_gear = 0.0;
$cat_food = 0.0;
$cat_mkt = 0.0;
$cat_payouts = 0.0;

if ( is_array( $financial_entries ) ) {
    foreach ( $financial_entries as $entry ) {
        $raw_type = strtolower( trim( $entry['type'] ?? 'inflow' ) );
        $is_inflow = ( $raw_type === 'income' || $raw_type === 'inflow' );
        if ( ! $is_inflow ) {
            $amt = floatval( $entry['amount'] ?? 0 );
            $category = $entry['category'] ?? '';
            if ( empty( $category ) ) {
                $desc_lower = strtolower( $entry['description'] ?? '' );
                if ( strpos( $desc_lower, 'rent' ) !== false || strpos( $desc_lower, 'lease' ) !== false || strpos( $desc_lower, 'office' ) !== false || strpos( $desc_lower, 'electricity' ) !== false ) {
                    $category = 'Studio Ops & Rent';
                } elseif ( strpos( $desc_lower, 'marketing' ) !== false || strpos( $desc_lower, 'ads' ) !== false || strpos( $desc_lower, 'listing' ) !== false || strpos( $desc_lower, 'campaign' ) !== false ) {
                    $category = 'Marketing & Listings';
                } elseif ( strpos( $desc_lower, 'split' ) !== false || strpos( $desc_lower, 'payout' ) !== false ) {
                    $category = 'Agent / Crew Payouts';
                } elseif ( strpos( $desc_lower, 'catering' ) !== false || strpos( $desc_lower, 'food' ) !== false || strpos( $desc_lower, 'travel' ) !== false ) {
                    $category = 'Food & Travel';
                } elseif ( strpos( $desc_lower, 'rental' ) !== false || strpos( $desc_lower, 'gear' ) !== false || strpos( $desc_lower, 'sony' ) !== false || strpos( $desc_lower, 'fx6' ) !== false || strpos( $desc_lower, 'camera' ) !== false ) {
                    $category = 'Gear & Tech';
                } else {
                    $category = 'Studio Ops & Rent';
                }
            }
            
            if ( $category === 'Studio Ops & Rent' ) {
                $cat_rent += $amt;
            } elseif ( $category === 'Gear & Tech' ) {
                $cat_gear += $amt;
            } elseif ( $category === 'Food & Travel' ) {
                $cat_food += $amt;
            } elseif ( $category === 'Marketing & Listings' ) {
                $cat_mkt += $amt;
            } elseif ( $category === 'Agent / Crew Payouts' ) {
                $cat_payouts += $amt;
            }
        }
    }
}

// Compute profit remainder
$profit_val = $donut_inflow - ( $cat_rent + $cat_gear + $cat_food + $cat_mkt + $cat_payouts );
if ( $profit_val < 0 ) {
    $profit_val = 0;
}

// Map percentages
$pct_profit = round( ( $profit_val / $donut_inflow ) * 100, 1 );
$pct_rent = round( ( $cat_rent / $donut_inflow ) * 100, 1 );
$pct_gear = round( ( $cat_gear / $donut_inflow ) * 100, 1 );
$pct_food = round( ( $cat_food / $donut_inflow ) * 100, 1 );
$pct_mkt = round( ( $cat_mkt / $donut_inflow ) * 100, 1 );
$pct_payouts = round( ( $cat_payouts / $donut_inflow ) * 100, 1 );

$donut_segments = array(
    array( 'label' => 'Net Profit', 'pct' => $pct_profit, 'val' => $profit_val, 'color' => '#18181b', 'class' => 'bg-zinc-900 dark:bg-zinc-100' ),
    array( 'label' => 'Operating Rent', 'pct' => $pct_rent, 'val' => $cat_rent, 'color' => '#52525b', 'class' => 'bg-zinc-600' ),
    array( 'label' => 'Gear & Equipment', 'pct' => $pct_gear, 'val' => $cat_gear, 'color' => '#a1a1aa', 'class' => 'bg-zinc-400' ),
    array( 'label' => 'Food & Travel', 'pct' => $pct_food, 'val' => $cat_food, 'color' => '#d4d4d8', 'class' => 'bg-zinc-300' ),
    array( 'label' => 'Marketing', 'pct' => $pct_mkt, 'val' => $cat_mkt, 'color' => '#71717a', 'class' => 'bg-zinc-500' ),
    array( 'label' => 'Agent Payouts', 'pct' => $pct_payouts, 'val' => $cat_payouts, 'color' => '#e4e4e7', 'class' => 'bg-zinc-200' ),
);

$cumulative_offset = 0;
$donut_svg_html = '';
foreach ( $donut_segments as $seg ) {
    if ( $seg['pct'] <= 0 ) continue;
    $length = ( $seg['pct'] / 100 ) * 377;
    $offset = -$cumulative_offset;
    $donut_svg_html .= sprintf(
        '<circle class="cora-animate-donut-segment" style="animation-delay: 450ms;" cx="80" cy="80" r="60" stroke="%s" stroke-width="18" fill="none" stroke-dasharray="%d 377" stroke-dashoffset="%d"/>',
        $seg['color'],
        round( $length ),
        round( $offset )
    ) . "\n";
    $cumulative_offset += $length;
}
?>

<div id="cora-financial-overview-root" class="space-y-6 text-zinc-900 dark:text-zinc-100 font-sans select-none">

<style>
/* Micro-animations and Premium Motion Design System */

/* Fade-in-up animation for bento grid cards */
@keyframes coraFadeInUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.cora-animate-fade-in-up {
    animation: coraFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* SVG Line chart self-drawing animation */
@keyframes coraDrawLine {
    from {
        stroke-dashoffset: 1000;
    }
    to {
        stroke-dashoffset: 0;
    }
}

.cora-animate-svg-line {
    stroke-dasharray: 1000;
    stroke-dashoffset: 1000;
    animation: coraDrawLine 1.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* SVG Bar chart rising animation */
@keyframes coraRiseBar {
    from {
        height: 0;
        y: 170;
    }
}

.cora-animate-svg-bar {
    animation: coraRiseBar 1.2s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* SVG Donut self-drawing animation */
@keyframes coraDrawDonut {
    from {
        stroke-dasharray: 0 377;
    }
}

.cora-animate-donut-segment {
    animation: coraDrawDonut 1.2s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* Inner donut text scale in */
@keyframes coraScaleIn {
    from {
        opacity: 0;
        transform: scale(0.85);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.cora-animate-scale-in {
    animation: coraScaleIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
    transform-origin: center;
}

/* Node point scale animation */
@keyframes coraScaleNode {
    from {
        transform: scale(0);
    }
    to {
        transform: scale(1);
    }
}

.cora-animate-node {
    animation: coraScaleNode 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    transform-origin: center;
    transform-box: fill-box;
}

/* Button & interactive element scale on press */
.cora-micro-interact {
    transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.15s ease, border-color 0.15s ease, opacity 0.15s ease, box-shadow 0.15s ease !important;
}

.cora-micro-interact:hover {
    transform: translateY(-1px);
}

.cora-micro-interact:active {
    transform: translateY(0) scale(0.96) !important;
}

/* Table row interactive transition */
.cora-table-row-interact {
    transition: background-color 0.2s ease, transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
}

.cora-table-row-interact:hover {
    transform: translateY(-0.5px);
    box-shadow: 0 2px 8px -2px rgba(9, 9, 11, 0.05);
}

/* Slide-in drawer custom spring animation overrides */
/* Premium Slide-in drawer overrides */
aside[id$="-drawer"] {
    transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.45s ease, box-shadow 0.45s ease !important;
    transform: translateX(0);
    visibility: visible;
    display: flex !important;
}

aside[id$="-drawer"].collapsed {
    transform: translateX(100%) !important;
    visibility: hidden !important;
    display: flex !important;
    pointer-events: none !important;
    box-shadow: none !important;
}

/* Modal backdrop fade in */
.cora-backdrop-blur-overlay {
    backdrop-filter: blur(4px);
    background-color: rgba(9, 9, 11, 0.2);
    transition: opacity 0.3s ease;
}

/* Remove card strokes/borders globally */
#cora-financial-overview-root .rounded-2xl,
#cora-financial-overview-root .rounded-xl,
#cora-financial-overview-root .border {
    border: none !important;
    border-width: 0 !important;
}

/* =========================================================
   SKELETON LOADING SYSTEM
   ========================================================= */
@keyframes coraSkeletonPulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.4; }
}

.cora-skeleton {
    background: #f4f4f5;
    border-radius: 8px;
    animation: coraSkeletonPulse 1.6s ease-in-out infinite;
    position: relative;
    overflow: hidden;
}

.dark .cora-skeleton {
    background: #27272a;
}

.cora-skeleton-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(9,9,11,0.04);
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.dark .cora-skeleton-card {
    background: #18181b;
}

/* Skeleton shimmer shine sweep */
.cora-skeleton::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.6) 50%, transparent 100%);
    animation: coraSkeletonShine 1.6s ease-in-out infinite;
    transform: translateX(-100%);
}

.dark .cora-skeleton::after {
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.08) 50%, transparent 100%);
}

@keyframes coraSkeletonShine {
    0%   { transform: translateX(-100%); }
    60%  { transform: translateX(100%); }
    100% { transform: translateX(100%); }
}

/* Content hide/show transitions */
#cora-fin-real-content {
    transition: opacity 0.35s ease;
}

#cora-fin-skeleton-content {
    transition: opacity 0.25s ease;
}

/* =========================================================
   EMAIL AUTOMATION DRAWER – FREQUENCY TOGGLE CARDS
   ========================================================= */
.cora-freq-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    border-radius: 12px;
    background: #f9fafb;
    cursor: pointer;
    transition: background 0.15s ease, box-shadow 0.15s ease;
    user-select: none;
}

.dark .cora-freq-toggle {
    background: #27272a;
}

.cora-freq-toggle:hover {
    background: #f4f4f5;
    box-shadow: 0 1px 4px rgba(9,9,11,0.06);
}

.dark .cora-freq-toggle:hover {
    background: #3f3f46;
}

.cora-freq-toggle.active {
    background: #18181b;
    color: #ffffff;
}

.dark .cora-freq-toggle.active {
    background: #f4f4f5;
    color: #18181b;
}

/* Toggle pill switch */
.cora-switch {
    width: 36px;
    height: 20px;
    border-radius: 10px;
    background: #d4d4d8;
    position: relative;
    transition: background 0.2s ease;
    flex-shrink: 0;
}

.cora-switch::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #fff;
    transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
}

.cora-switch.on {
    background: #18181b;
}

.dark .cora-switch.on {
    background: #f4f4f5;
}

.cora-switch.on::after {
    transform: translateX(16px);
}

/* Recipient chip input */
.cora-chip-container {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 8px 10px;
    border-radius: 12px;
    background: #f9fafb;
    min-height: 44px;
    cursor: text;
}

.dark .cora-chip-container {
    background: #27272a;
}

.cora-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    background: #18181b;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
}

.dark .cora-chip {
    background: #f4f4f5;
    color: #18181b;
}

.cora-chip button {
    background: none;
    border: none;
    color: currentColor;
    opacity: 0.6;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    font-size: 13px;
}

.cora-chip-input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 12px;
    color: #18181b;
    flex: 1;
    min-width: 140px;
}

.dark .cora-chip-input {
    color: #f4f4f5;
}

</style>

    <!-- 1. HEADER SECTION & DATE SELECTOR -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div class="w-full sm:w-auto">
            <div class="flex items-center justify-between gap-2 w-full">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight flex items-center gap-2">
                    <span>Financial Overview</span>
                    <span class="text-zinc-400 text-lg font-normal">✦</span>
                </h1>
                
                <!-- Mobile-only Action Menu Button -->
                <div class="relative block sm:hidden">
                    <button type="button" onclick="window.toggleFinancialActionMenu(event)" class="px-3.5 py-2 bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-900 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 cursor-pointer shadow-sm cora-micro-interact">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Action</span>
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    
                    <!-- Mobile Action Menu Popover -->
                    <div id="cora-fin-action-popover-mobile" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-900 rounded-xl shadow-2xl z-50 p-1 space-y-0.5 animate-fade-in select-none">
                        <button type="button" onclick="window.toggleFinancialActionMenu(); window.openAddLedgerDrawer();" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Add Ledger Entry</span>
                        </button>
                        <button type="button" onclick="window.toggleFinancialActionMenu(); window.openCreateInvoiceDrawer();" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            <span>Create Client Invoice</span>
                        </button>
                        <button type="button" onclick="window.toggleFinancialActionMenu(); window.openProcessPayoutDrawer();" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            <span>Process Payout</span>
                        </button>
                    </div>
                </div>
            </div>
            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1 font-medium">
                Real-time insights into your studio's financial performance.
            </p>
        </div>

        <!-- Date Range Selector & Actions -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            
            <!-- Date Range Selector Sub-Row (contains Date toggle + Reports icon button) -->
            <div class="flex items-center gap-2 w-full sm:w-auto flex-1">
                <!-- Date Range Selector Dropdown -->
                <div class="relative flex-1 sm:flex-none">
                    <div onclick="window.toggleFinancialDatePopover(event)" class="w-full sm:w-auto justify-between flex items-center gap-2 bg-white dark:bg-zinc-900 px-3.5 py-2 rounded-xl shadow-sm cursor-pointer text-xs font-bold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-855 transition-all cora-micro-interact">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span id="cora-selected-date-range">Jan 01 – Jun 30, 2025</span>
                        </div>
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    
                    <!-- Popover Date Dropdown -->
                    <div id="cora-fin-date-popover" class="hidden absolute left-0 mt-2 w-full sm:w-48 bg-white dark:bg-zinc-900 rounded-xl shadow-2xl z-50 p-1 space-y-0.5 animate-fade-in select-none">
                        <button type="button" onclick="window.selectFinancialDateRange('This Month', '01 Jul – 31 Jul 2026')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between">
                            <span>This Month</span>
                        </button>
                        <button type="button" onclick="window.selectFinancialDateRange('Last Month', '01 Jun – 30 Jun 2026')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between">
                            <span>Last Month</span>
                        </button>
                        <button type="button" onclick="window.selectFinancialDateRange('Last 3 Months', '01 Apr – 30 Jun 2026')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between">
                            <span>Last 3 Months</span>
                        </button>
                        <button type="button" onclick="window.selectFinancialDateRange('Last 6 Months', '01 Jan – 30 Jun 2026')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between">
                            <span>Last 6 Months</span>
                        </button>
                        <button type="button" onclick="window.selectFinancialDateRange('Year to Date', '01 Jan – 31 Dec 2026')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between">
                            <span>Year to Date</span>
                        </button>
                        <button type="button" onclick="window.selectFinancialDateRange('All Time', 'Jan 01 – Jun 30, 2025')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-between">
                            <span>All Time</span>
                        </button>
                    </div>
                </div>

                <button type="button" onclick="window.openFinancialReportsDrawer()" class="p-2.5 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-xl transition-colors cursor-pointer shadow-sm cora-micro-interact flex items-center justify-center shrink-0 h-[36px] w-[36px]" title="Automated Reports & Schedule">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </button>
            </div>

            <!-- Primary Action Menu Button (Hidden on mobile) -->
            <div class="relative hidden sm:block">
                <button type="button" onclick="window.toggleFinancialActionMenu(event)" class="px-4 py-2 bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-900 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer shadow-sm cora-micro-interact">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>New Action</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>

                <!-- Popover Action Menu Dropdown -->
                <div id="cora-fin-action-popover" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-zinc-900 rounded-xl shadow-2xl z-50 p-1 space-y-0.5 animate-fade-in select-none">
                    <button type="button" onclick="window.toggleFinancialActionMenu(); window.openAddLedgerDrawer();" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Add Ledger Entry</span>
                    </button>
                    <button type="button" onclick="window.toggleFinancialActionMenu(); window.openCreateInvoiceDrawer();" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <span>Create Client Invoice</span>
                    </button>
                    <button type="button" onclick="window.toggleFinancialActionMenu(); window.openProcessPayoutDrawer();" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                        <span>Process Payout</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
    <!-- SKELETON LOADING STATE (shown briefly on page load, then hidden) -->
    <div id="cora-fin-skeleton-content" class="space-y-6">
        <!-- Skeleton KPI Row -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <?php for ( $s = 0; $s < 4; $s++ ) : ?>
            <div class="cora-skeleton-card">
                <div class="flex items-center gap-2.5">
                    <div class="cora-skeleton w-9 h-9 rounded-xl"></div>
                    <div class="cora-skeleton h-3 flex-1 rounded"></div>
                </div>
                <div class="cora-skeleton h-8 w-3/4 rounded-lg"></div>
                <div class="cora-skeleton h-3 w-2/3 rounded"></div>
            </div>
            <?php endfor; ?>
        </div>
        <!-- Skeleton Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7 cora-skeleton-card" style="min-height:320px;">
                <div class="flex items-center justify-between">
                    <div class="cora-skeleton h-5 w-48 rounded-lg"></div>
                    <div class="cora-skeleton h-7 w-24 rounded-lg"></div>
                </div>
                <div class="flex items-end gap-3 mt-4" style="height:160px;">
                    <?php for ( $s = 0; $s < 6; $s++ ) : $hh = rand(30,140); ?>
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="cora-skeleton w-full rounded-md" style="height: <?php echo $hh; ?>px;"></div>
                        <div class="cora-skeleton h-2.5 w-8 rounded"></div>
                    </div>
                    <?php endfor; ?>
                </div>
                <div class="grid grid-cols-3 gap-3 mt-4">
                    <?php for ( $s = 0; $s < 3; $s++ ) : ?>
                    <div class="cora-skeleton-card" style="padding:12px;gap:8px;">
                        <div class="cora-skeleton h-2.5 w-full rounded"></div>
                        <div class="cora-skeleton h-5 w-3/4 rounded-lg"></div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="lg:col-span-5 cora-skeleton-card" style="min-height:320px;">
                <div class="flex items-start justify-between">
                    <div class="cora-skeleton h-5 w-40 rounded-lg"></div>
                    <div class="cora-skeleton h-4 w-24 rounded"></div>
                </div>
                <div class="flex items-center justify-center mt-6">
                    <div class="cora-skeleton w-36 h-36 rounded-full"></div>
                </div>
                <div class="space-y-3 mt-4">
                    <?php for ( $s = 0; $s < 4; $s++ ) : ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="cora-skeleton w-3 h-3 rounded-sm"></div>
                            <div class="cora-skeleton h-3 w-24 rounded"></div>
                        </div>
                        <div class="cora-skeleton h-3 w-20 rounded"></div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <!-- Skeleton Ledger Table -->
        <div class="cora-skeleton-card" style="gap:12px;">
            <div class="flex items-center justify-between">
                <div class="cora-skeleton h-5 w-36 rounded-lg"></div>
                <div class="cora-skeleton h-7 w-28 rounded-xl"></div>
            </div>
            <?php for ( $s = 0; $s < 5; $s++ ) : ?>
            <div class="flex items-center gap-4">
                <div class="cora-skeleton h-4 w-20 rounded"></div>
                <div class="cora-skeleton h-4 flex-1 rounded"></div>
                <div class="cora-skeleton h-6 w-16 rounded-lg"></div>
                <div class="cora-skeleton h-4 w-16 rounded"></div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- REAL CONTENT WRAPPER (fades in after skeleton) -->
    <div id="cora-fin-real-content" style="opacity:0; pointer-events:none;">

    <!-- 2. TOP 4 KPI METRICS CARDS GRID -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        <!-- Card 1: Gross Revenue -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 shadow-sm flex flex-col gap-3 cora-animate-fade-in-up" style="animation-delay: 50ms;">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 font-extrabold text-sm flex items-center justify-center select-none">₹</span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide leading-tight">Gross Revenue</span>
            </div>
            <div>
                <div class="text-xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">₹<?php echo number_format( $kpi_inflow ); ?></div>
                <div class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5 flex items-center gap-0.5">
                    <span>↑ 12.4% vs last period</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Expenses -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 shadow-sm flex flex-col gap-3 cora-animate-fade-in-up" style="animation-delay: 100ms;">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 shrink-0 rounded-xl bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                </span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide leading-tight">Total Expenses</span>
            </div>
            <div>
                <div class="text-xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">₹<?php echo number_format( $kpi_outflow ); ?></div>
                <div class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 mt-0.5">Ops, Production &amp; Gear</div>
            </div>
        </div>

        <!-- Card 3: Net Profit -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 shadow-sm flex flex-col gap-3 cora-animate-fade-in-up" style="animation-delay: 150ms;">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 shrink-0 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
                </span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide leading-tight">Net Profit</span>
            </div>
            <div>
                <div class="text-xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">₹<?php echo number_format( $kpi_profit ); ?></div>
                <div class="mt-0.5">
                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400"><?php echo $kpi_margin; ?>% Margin</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Pending Receivables -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 shadow-sm flex flex-col gap-3 cora-animate-fade-in-up" style="animation-delay: 200ms;">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 shrink-0 rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide leading-tight">Receivables</span>
            </div>
            <div>
                <div class="text-xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">₹<?php echo number_format( $kpi_receivables ); ?></div>
                <div class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 mt-0.5"><?php echo $kpi_receivables_count; ?> Outstanding</div>
            </div>
        </div>

    </div>

    <!-- 3. MIDDLE ANALYTICS ROW -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mt-6">
        
        <!-- Left 7 Columns: Revenue Analytics & Cashflow Chart -->
        <div class="lg:col-span-7 bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm flex flex-col space-y-4 cora-animate-fade-in-up" style="animation-delay: 250ms;">
            
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 whitespace-nowrap">Revenue &amp; Cashflow</h3>
                <div class="flex items-center gap-2 shrink-0">
                    <select class="bg-zinc-50 dark:bg-zinc-800 rounded-lg px-2.5 py-1 text-xs font-semibold text-zinc-700 dark:text-zinc-300 focus:outline-none border-0">
                        <option>Monthly</option>
                        <option>Quarterly</option>
                    </select>
                    <button type="button" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                    </button>
                </div>
            </div>

            <!-- Legend -->
            <div class="flex items-center gap-4 overflow-x-auto no-scrollbar">
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="w-2.5 h-2.5 rounded-sm inline-block" style="background-color: #09090b;"></span>
                    <span class="text-[10px] font-semibold text-zinc-500 whitespace-nowrap">Inflows</span>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="w-2.5 h-2.5 rounded-sm inline-block" style="background-color: #d4d4d8;"></span>
                    <span class="text-[10px] font-semibold text-zinc-500 whitespace-nowrap">Outflows</span>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="w-4 h-0.5 inline-block" style="background-color: #09090b;"></span>
                    <span class="text-[10px] font-semibold text-zinc-500 whitespace-nowrap">Net Profit</span>
                </div>
            </div>

            <!-- SVG Combination Chart (Bar + Net Profit Line Node Dots) -->
            <div class="w-full h-56 relative pt-2">
                <svg viewBox="0 0 600 220" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                    <!-- Y Axis Grid Lines -->
                    <line x1="40" y1="20" x2="580" y2="20" stroke="#f4f4f5" stroke-dasharray="3 3"/>
                    <line x1="40" y1="70" x2="580" y2="70" stroke="#f4f4f5" stroke-dasharray="3 3"/>
                    <line x1="40" y1="120" x2="580" y2="120" stroke="#f4f4f5" stroke-dasharray="3 3"/>
                    <line x1="40" y1="170" x2="580" y2="170" stroke="#f4f4f5"/>

                    <!-- Y Axis Labels -->
                    <text x="30" y="24" text-anchor="end" font-size="10" font-weight="600" fill="#a1a1aa"><?php echo esc_html( $scale_y1 ); ?></text>
                    <text x="30" y="74" text-anchor="end" font-size="10" font-weight="600" fill="#a1a1aa"><?php echo esc_html( $scale_y2 ); ?></text>
                    <text x="30" y="124" text-anchor="end" font-size="10" font-weight="600" fill="#a1a1aa"><?php echo esc_html( $scale_y3 ); ?></text>
                    <text x="30" y="174" text-anchor="end" font-size="10" font-weight="600" fill="#a1a1aa">₹0</text>

                    <!-- Bars (Inflows + Outflows) -->
                    <?php 
                    $bar_idx = 0;
                    foreach ( $chart_elements as $el ) : 
                        $delay_inflow = 300 + ($bar_idx * 50);
                        $delay_outflow = 350 + ($bar_idx * 50);
                        ?>
                        <!-- Inflow Bar -->
                        <rect class="cora-animate-svg-bar" style="animation-delay: <?php echo $delay_inflow; ?>ms; fill: #09090b !important;" x="<?php echo $el['x_bar1']; ?>" y="<?php echo $el['inflow_y']; ?>" width="12" height="<?php echo $el['inflow_h']; ?>" rx="2" fill="#09090b"/>
                        <!-- Outflow Bar -->
                        <rect class="cora-animate-svg-bar" style="animation-delay: <?php echo $delay_outflow; ?>ms; fill: #d4d4d8 !important;" x="<?php echo $el['x_bar2']; ?>" y="<?php echo $el['outflow_y']; ?>" width="12" height="<?php echo $el['outflow_h']; ?>" rx="2" fill="#d4d4d8"/>
                    <?php 
                        $bar_idx++;
                    endforeach; ?>

                    <!-- Net Profit Connected Line with Circular Markers -->
                    <path class="cora-animate-svg-line" d="<?php echo esc_attr( $path_d ); ?>" fill="none" stroke="#09090b" stroke-width="2.5"/>

                    <!-- Node Points -->
                    <?php 
                    $node_idx = 0;
                    foreach ( $chart_elements as $el ) : 
                        $delay_node = 600 + ($node_idx * 60);
                        ?>
                        <circle class="cora-animate-node" style="animation-delay: <?php echo $delay_node; ?>ms;" cx="<?php echo $el['x_center']; ?>" cy="<?php echo $el['profit_y']; ?>" r="4.5" fill="#09090b"/>
                    <?php 
                        $node_idx++;
                    endforeach; ?>

                    <!-- X Axis Month Labels -->
                    <?php foreach ( $chart_elements as $el ) : ?>
                        <text x="<?php echo $el['x_center']; ?>" y="195" text-anchor="middle" font-size="11" font-weight="600" fill="#71717a"><?php echo esc_html( $el['label'] ); ?></text>
                    <?php endforeach; ?>
                </svg>
            </div>

            <!-- Chart Footer Metrics Row -->
            <div class="grid grid-cols-3 gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800/80">
                <!-- Inflows -->
                <div class="flex flex-col gap-0.5 bg-zinc-50 dark:bg-zinc-800/30 p-2.5 rounded-xl">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wide whitespace-nowrap">Inflows</span>
                    <span class="text-xs font-extrabold text-zinc-900 dark:text-zinc-100">₹<?php echo number_format( $kpi_inflow ); ?></span>
                </div>
                <!-- Outflows -->
                <div class="flex flex-col gap-0.5 bg-zinc-50 dark:bg-zinc-800/30 p-2.5 rounded-xl">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wide whitespace-nowrap">Outflows</span>
                    <span class="text-xs font-extrabold text-zinc-900 dark:text-zinc-100">₹<?php echo number_format( $kpi_outflow ); ?></span>
                </div>
                <!-- Net Profit -->
                <div class="flex flex-col gap-0.5 bg-zinc-50 dark:bg-zinc-800/30 p-2.5 rounded-xl">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-wide whitespace-nowrap">Net Profit</span>
                    <span class="text-xs font-extrabold text-zinc-900 dark:text-zinc-100">₹<?php echo number_format( $kpi_profit ); ?></span>
                </div>
            </div>

        </div>

        <!-- Right 5 Columns: Cash Allocation & Profit Margin Donut Chart -->
        <div class="lg:col-span-5 bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm flex flex-col justify-between space-y-5 cora-animate-fade-in-up" style="animation-delay: 300ms;">
            
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Cash Allocation & Profit Margin</h3>
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200"><?php echo $has_actual_data ? $kpi_margin : 35.0; ?>% Net Margin</span>
                </div>
                <span class="text-xs font-semibold text-zinc-400">Gross Revenue: ₹<?php echo number_format( $kpi_inflow ); ?></span>
            </div>

            <!-- SVG Donut Chart + Legend Row -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center py-2">
                
                <!-- Donut Chart Left (5 Cols) -->
                <div class="sm:col-span-5 relative flex items-center justify-center">
                    <svg viewBox="0 0 160 160" class="w-36 h-36 transform -rotate-90">
                        <!-- Background Circle -->
                        <circle cx="80" cy="80" r="60" stroke="#f4f4f5" stroke-width="18" fill="none"/>
                        <!-- Dynamic Donut Segments -->
                        <?php echo $donut_svg_html; ?>
                    </svg>
                    <!-- Inner Donut Text -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center cora-animate-scale-in" style="animation-delay: 500ms;">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Net Profit</span>
                        <span class="text-xl font-extrabold text-zinc-900 dark:text-zinc-100 leading-none my-0.5"><?php echo $pct_profit; ?>%</span>
                        <span class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400">₹<?php echo number_format( $profit_val ); ?></span>
                    </div>
                </div>

                <!-- Donut Legend List Right (7 Cols) -->
                <div class="sm:col-span-7 space-y-3 text-xs">
                    <?php foreach ( $donut_segments as $seg ) : 
                        if ( $seg['pct'] > 0 || $seg['val'] > 0 ) : ?>
                            <div class="flex items-center justify-between font-semibold">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-sm" style="background-color: <?php echo esc_attr( $seg['color'] ); ?>;"></span>
                                    <span class="text-zinc-700 dark:text-zinc-300"><?php echo esc_html( $seg['label'] ); ?></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-zinc-400"><?php echo number_format( $seg['pct'], 1 ); ?>%</span>
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100">₹<?php echo number_format( $seg['val'] ); ?></span>
                                </div>
                            </div>
                        <?php endif; 
                    endforeach; ?>
                </div>

            </div>

            <!-- Bottom Segmented Progress Bar -->
            <div class="w-full h-3 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex">
                <?php foreach ( $donut_segments as $seg ) : 
                    if ( $seg['pct'] > 0 ) : ?>
                        <div class="h-full" style="width: <?php echo $seg['pct']; ?>%; background-color: <?php echo esc_attr( $seg['color'] ); ?>;"></div>
                    <?php endif; 
                endforeach; ?>
            </div>

            <!-- On Track Blue Info Card -->
            <div class="bg-blue-50/50 dark:bg-blue-955/20 border border-blue-100/50 dark:border-blue-900/30 rounded-xl p-3 flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                </div>
                <span class="text-xs font-semibold text-blue-800 dark:text-blue-300">You're on track! Keep up the good work.</span>
            </div>

            </div>
        </div>

    </div>

    <!-- 4. MASTER LEDGER SECTION (BORDERLESS CLEAN CARD WITH SOFT SHADOW) -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm space-y-5">
        
        <!-- Header & Tabs -->
        <div class="space-y-3 border-b border-zinc-100 dark:border-zinc-800/60 pb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Master Ledger</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-medium">Consolidated view of all financial entries.</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-0.5">
                <button type="button" onclick="window.switchFinancialTab('fin-ledger')" id="btn-tab-fin-ledger" class="cora-financial-tab shrink-0 whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 shadow-xs cursor-pointer">
                    Master Ledger
                </button>
                <button type="button" onclick="window.switchFinancialTab('fin-invoices')" id="btn-tab-fin-invoices" class="cora-financial-tab shrink-0 whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer">
                    Invoices & Billing
                </button>
                <button type="button" onclick="window.switchFinancialTab('fin-payouts')" id="btn-tab-fin-payouts" class="cora-financial-tab shrink-0 whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer">
                    Agent / Crew Payouts
                </button>
            </div>
        </div>

        <!-- TAB 1: MASTER LEDGER PANEL -->
        <div id="tab-fin-ledger" class="cora-fin-tab-content space-y-4">
            
            <!-- Category Filter Pills & Search Bar Row -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <!-- Category Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                    <button type="button" onclick="window.filterLedgerByPill('all', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 cursor-pointer shrink-0">All Entries</button>
                    <button type="button" onclick="window.filterLedgerByPill('Food & Travel', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Food & Travel</button>
                    <button type="button" onclick="window.filterLedgerByPill('Gear & Tech', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Gear & Tech</button>
                    <button type="button" onclick="window.filterLedgerByPill('Studio Ops & Rent', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Studio Ops & Rent</button>
                    <button type="button" onclick="window.filterLedgerByPill('Marketing & Listings', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Marketing & Listings</button>
                    <button type="button" onclick="window.filterLedgerByPill('Agent / Crew Payouts', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Agent / Crew Payouts</button>
                    <button type="button" onclick="window.filterLedgerByPill('Inflows & Retainers', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Inflows & Retainers</button>
                </div>

                <!-- Search Input + Filter Icon Button -->
                <div class="flex items-center gap-2 shrink-0">
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="fin-ledger-search" oninput="window.filterLedgerTable()" placeholder="Search transactions..." class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl py-2 pl-9 pr-3 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <button type="button" class="p-2 bg-zinc-50 dark:bg-zinc-800 border-0 text-zinc-600 dark:text-zinc-400 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer" title="Advanced Filter">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    </button>
                </div>

            </div>

            <!-- Master Ledger Table (No Outer Border) -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left divide-y divide-zinc-100 dark:divide-zinc-800" id="cora-financial-table">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-800/30">
                        <tr>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">DATE</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">DESCRIPTION</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">CATEGORY</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">TYPE</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">AMOUNT</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">STATUS</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 font-medium" id="cora-financial-table-body">
                        <?php 
                        if ( ! empty( $financial_entries ) ) :
                            foreach ( $financial_entries as $entry ) :
                                $raw_type = strtolower( trim( $entry['type'] ?? 'inflow' ) );
                                $is_inflow = ( $raw_type === 'income' || $raw_type === 'inflow' );
                                $type_label = $is_inflow ? 'Cash Inflow' : 'Cash Outflow';
                                $type_arrow = $is_inflow ? '↑' : '↓';
                                
                                $date_ts = strtotime( $entry['date'] ?? '' );
                                $formatted_date = $date_ts ? date( 'd M Y', $date_ts ) : ( $entry['date'] ?? '' );
                                
                                $amount = floatval( $entry['amount'] ?? 0 );
                                $formatted_amount = '₹' . number_format( $amount );
                                
                                // Auto-categorize if empty
                                $category = $entry['category'] ?? '';
                                if ( empty( $category ) ) {
                                    $desc_lower = strtolower( $entry['description'] ?? '' );
                                    if ( strpos( $desc_lower, 'rent' ) !== false || strpos( $desc_lower, 'lease' ) !== false || strpos( $desc_lower, 'office' ) !== false || strpos( $desc_lower, 'electricity' ) !== false ) {
                                        $category = 'Studio Ops & Rent';
                                    } elseif ( strpos( $desc_lower, 'marketing' ) !== false || strpos( $desc_lower, 'ads' ) !== false || strpos( $desc_lower, 'listing' ) !== false || strpos( $desc_lower, 'campaign' ) !== false ) {
                                        $category = 'Marketing & Listings';
                                    } elseif ( strpos( $desc_lower, 'split' ) !== false || strpos( $desc_lower, 'payout' ) !== false ) {
                                        $category = 'Agent / Crew Payouts';
                                    } elseif ( strpos( $desc_lower, 'catering' ) !== false || strpos( $desc_lower, 'food' ) !== false || strpos( $desc_lower, 'travel' ) !== false ) {
                                        $category = 'Food & Travel';
                                    } elseif ( strpos( $desc_lower, 'rental' ) !== false || strpos( $desc_lower, 'gear' ) !== false || strpos( $desc_lower, 'sony' ) !== false || strpos( $desc_lower, 'fx6' ) !== false || strpos( $desc_lower, 'camera' ) !== false ) {
                                        $category = 'Gear & Tech';
                                    } else {
                                        $category = $is_inflow ? 'Inflows & Retainers' : 'Studio Ops & Rent';
                                    }
                                }
                                
                                $status = ucfirst( strtolower( $entry['status'] ?? ( $is_inflow ? 'received' : 'paid' ) ) );
                                if ( $status === 'Completed' ) {
                                    $status = $is_inflow ? 'Received' : 'Paid';
                                }
                                ?>
                                <tr class="cora-table-row-interact hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold"><?php echo esc_html( $formatted_date ); ?></td>
                                    <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $entry['description'] ?? '' ); ?></td>
                                    <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]"><?php echo esc_html( $category ); ?></span></td>
                                    <td class="px-5 py-4">
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 <?php echo $is_inflow ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400'; ?> flex items-center gap-1 w-fit">
                                            <span class="<?php echo $is_inflow ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-500'; ?>"><?php echo esc_html( $type_arrow ); ?></span> <?php echo esc_html( $type_label ); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm"><?php echo esc_html( $formatted_amount ); ?></td>
                                    <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300"><?php echo esc_html( $status ); ?></span></td>
                                    <td class="px-5 py-4 text-right">
                                        <button type="button" onclick="window.coraShowToast('Viewing details for transaction #<?php echo esc_attr( $entry['id'] ); ?>', 'info')" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-bold text-base">···</button>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        else :
                            ?>
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-zinc-400">No transactions recorded yet.</td>
                            </tr>
                            <?php
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- TAB 2: INVOICES & BILLING PANEL -->
        <div id="tab-fin-invoices" class="cora-fin-tab-content hidden space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-xs font-semibold text-zinc-500">Track pending and paid client billing statements.</div>
                <button type="button" onclick="openCreateInvoiceDrawer()" class="px-3.5 py-2 bg-zinc-900 text-white rounded-xl text-xs font-bold">
                    + Create Invoice
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left divide-y divide-zinc-100 dark:divide-zinc-800" id="cora-invoices-table">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-800/30">
                        <tr>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">INVOICE #</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">CLIENT</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">DESCRIPTION</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">AMOUNT</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">DUE DATE</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">STATUS</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 font-medium" id="cora-invoices-table-body">
                        <?php 
                        if ( ! empty( $invoices ) ) :
                            foreach ( $invoices as $inv ) :
                                $due_date_ts = strtotime( $inv['due_date'] ?? '' );
                                $formatted_due_date = $due_date_ts ? date( 'd M Y', $due_date_ts ) : ( $inv['due_date'] ?? '' );
                                $inv_amount = floatval( $inv['total_amount'] ?? 0 );
                                $formatted_amount = '₹' . number_format( $inv_amount );
                                $inv_status = ucfirst( strtolower( $inv['status'] ?? 'pending' ) );
                                ?>
                                <tr class="cora-table-row-interact hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $inv['invoice_number'] ?? '' ); ?></td>
                                    <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold"><?php echo esc_html( $inv['client_name'] ?? '' ); ?></td>
                                    <td class="px-5 py-4 text-zinc-600 dark:text-zinc-400"><?php echo esc_html( $inv['package_name'] ?? '' ); ?></td>
                                    <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm"><?php echo esc_html( $formatted_amount ); ?></td>
                                    <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200"><?php echo esc_html( $formatted_due_date ); ?></td>
                                    <td class="px-5 py-4">
                                        <span class="px-3 py-1 rounded-lg text-[11px] font-bold <?php echo (strtolower($inv_status) === 'paid') ? 'bg-zinc-100 dark:bg-zinc-850 text-zinc-800 dark:text-zinc-200' : 'bg-zinc-50 dark:bg-zinc-800 text-zinc-500'; ?>">
                                            <?php echo esc_html( $inv_status ); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button type="button" onclick="window.coraShowToast('Invoice share token: <?php echo esc_attr( $inv['share_token'] ?? '' ); ?>', 'info')" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-bold text-base">···</button>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        else :
                            ?>
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-zinc-400">No client invoices found.</td>
                            </tr>
                            <?php
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: AGENT / CREW PAYOUTS PANEL -->
        <div id="tab-fin-payouts" class="cora-fin-tab-content hidden space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-xs font-semibold text-zinc-500">Manage team commissions and contractor day rates.</div>
                <button type="button" onclick="openProcessPayoutDrawer()" class="px-3.5 py-2 bg-zinc-900 text-white rounded-xl text-xs font-bold">
                    + Process Payout
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left divide-y divide-zinc-100 dark:divide-zinc-800" id="cora-payouts-table">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-800/30">
                        <tr>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">PAYOUT #</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">RECIPIENT</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">ROLE</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">NET AMOUNT</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">DATE</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">STATUS</th>
                            <th class="px-5 py-3.5 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 font-medium" id="cora-payouts-table-body">
                        <?php 
                        if ( ! empty( $payouts ) ) :
                            foreach ( $payouts as $pay ) :
                                $pay_date_ts = strtotime( $pay['created_at'] ?? '' );
                                $formatted_pay_date = $pay_date_ts ? date( 'd M Y', $pay_date_ts ) : ( $pay['created_at'] ?? '' );
                                $pay_net = floatval( $pay['net_payout'] ?? 0 );
                                $formatted_net = '₹' . number_format( $pay_net );
                                $pay_status = ucfirst( strtolower( $pay['status'] ?? 'processed' ) );
                                ?>
                                <tr class="cora-table-row-interact hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $pay['payout_number'] ?? '' ); ?></td>
                                    <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold"><?php echo esc_html( $pay['recipient_name'] ?? '' ); ?></td>
                                    <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-850 text-zinc-700 dark:text-zinc-300"><?php echo esc_html( ucfirst( $pay['recipient_role'] ?? '' ) ); ?></span></td>
                                    <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm"><?php echo esc_html( $formatted_net ); ?></td>
                                    <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200"><?php echo esc_html( $formatted_pay_date ); ?></td>
                                    <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-850 text-zinc-800 dark:text-zinc-200"><?php echo esc_html( $pay_status ); ?></span></td>
                                    <td class="px-5 py-4 text-right">
                                        <button type="button" onclick="window.coraShowToast('Notes: <?php echo esc_attr( $pay['notes'] ?? '' ); ?>', 'info')" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-bold text-base">···</button>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        else :
                            ?>
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-zinc-400">No crew payouts processed yet.</td>
                            </tr>
                            <?php
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    </div><!-- /#cora-fin-real-content -->

</div><!-- /#cora-financial-overview-root -->

<!-- SIDE DRAWERS FOR ACTIONS -->

<!-- 1. ADD LEDGER ENTRY DRAWER -->
<aside id="cora-add-ledger-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Add Ledger Entry</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Log cash inflow or operational outflow.</p>
        </div>
        <button type="button" onclick="window.closeAddLedgerDrawer()" class="p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <form id="cora-add-ledger-form" onsubmit="handleAddLedgerSubmit(event)" class="p-5 space-y-4 flex-1 overflow-y-auto text-xs">
        <div class="space-y-1">
            <label class="font-bold text-zinc-700 dark:text-zinc-300">Entry Description</label>
            <input type="text" name="entry_desc" required placeholder="e.g. Camera Rental – Sony FX6" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">Amount (₹)</label>
                <input type="number" name="entry_amount" required placeholder="12500" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
            </div>
            <div class="space-y-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">Type</label>
                <select name="entry_type" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    <option value="inflow">Cash Inflow</option>
                    <option value="outflow">Cash Outflow</option>
                </select>
            </div>
        </div>
        <div class="space-y-1">
            <label class="font-bold text-zinc-700 dark:text-zinc-300">Category</label>
            <select name="entry_category" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                <option value="Inflows & Retainers">Inflows & Retainers</option>
                <option value="Food & Travel">Food & Travel</option>
                <option value="Gear & Tech">Gear & Tech</option>
                <option value="Studio Ops & Rent">Studio Ops & Rent</option>
                <option value="Marketing & Listings">Marketing & Listings</option>
                <option value="Agent / Crew Payouts">Agent / Crew Payouts</option>
            </select>
        </div>
        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-2">
            <button type="button" onclick="window.closeAddLedgerDrawer()" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold">Cancel</button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-zinc-900 text-white font-bold">Save Entry</button>
        </div>
    </form>
</aside>

<!-- 2. CREATE CLIENT INVOICE DRAWER -->
<aside id="cora-create-invoice-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Create Client Invoice</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Send a structured billing statement to a client.</p>
        </div>
        <button type="button" onclick="window.closeCreateInvoiceDrawer()" class="p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <form id="cora-create-invoice-form" onsubmit="handleCreateInvoiceSubmit(event)" class="p-5 space-y-4 flex-1 overflow-y-auto text-xs">
        <div class="space-y-1">
            <label class="font-bold text-zinc-700 dark:text-zinc-300">Client / Customer</label>
            <select name="invoice_client" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                <option value="Ananya Sharma">Ananya Sharma (Residential Buy)</option>
                <option value="Rajesh Kumar">Rajesh Kumar (Commercial Lease)</option>
                <option value="Rohit Verma">Rohit Verma (Premium Listing)</option>
                <option value="Sneha Gupta">Sneha Gupta (Studio Shoot)</option>
            </select>
        </div>
        <div class="space-y-1">
            <label class="font-bold text-zinc-700 dark:text-zinc-300">Invoice Number</label>
            <input type="text" name="invoice_number" required value="INV-<?php echo date('Ymd'); ?>-<?php echo rand(100, 999); ?>" readonly class="w-full bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-500 dark:text-zinc-400 focus:outline-none cursor-not-allowed">
        </div>
        <div class="space-y-1">
            <label class="font-bold text-zinc-700 dark:text-zinc-300">Service Description / Line Item</label>
            <input type="text" name="invoice_desc" required placeholder="e.g. Sunset Villa Professional Photography Shoot" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">Subtotal Amount (₹)</label>
                <input type="number" name="invoice_amount" required placeholder="25000" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
            </div>
            <div class="space-y-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">Tax Class</label>
                <select name="invoice_tax" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    <option value="18">18% GST (Standard)</option>
                    <option value="12">12% GST (Services)</option>
                    <option value="0">No Tax / Exempt</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">Deposit Percentage (%)</label>
                <select name="invoice_deposit" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    <option value="0">None (Full Payment on Due Date)</option>
                    <option value="25">25% Retainer / Advance</option>
                    <option value="50">50% Retainer / Advance</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">Due Date</label>
                <input type="date" name="invoice_due_date" required value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
            </div>
        </div>
        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-2">
            <button type="button" onclick="window.closeCreateInvoiceDrawer()" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold">Cancel</button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-zinc-900 text-white font-bold">Generate & Send</button>
        </div>
    </form>
</aside>

<!-- 3. PROCESS PAYOUT DRAWER -->
<aside id="cora-process-payout-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Process Payout</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Pay commissions, contractor day rates, or split payouts.</p>
        </div>
        <button type="button" onclick="window.closeProcessPayoutDrawer()" class="p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <form id="cora-process-payout-form" onsubmit="handleProcessPayoutSubmit(event)" class="p-5 space-y-4 flex-1 overflow-y-auto text-xs">
        <div class="space-y-1">
            <label class="font-bold text-zinc-700 dark:text-zinc-300">Recipient Name</label>
            <input type="text" name="payout_recipient" required placeholder="e.g. Rohit Sharma" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">Recipient Role</label>
                <select name="payout_role" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    <option value="agent">Co-Agent / Broker</option>
                    <option value="photographer">Second Photographer</option>
                    <option value="editor">Editor</option>
                    <option value="assistant">Production Assistant</option>
                    <option value="model">Model / Talent</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">Industry Sector</label>
                <select name="payout_industry" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    <option value="real_estate">Real Estate Ops</option>
                    <option value="studio">Studio Production</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div class="space-y-1 col-span-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">Base Share (₹)</label>
                <input type="number" name="payout_amount" required placeholder="15000" oninput="calculatePayoutTDS()" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
            </div>
            <div class="space-y-1 col-span-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">Split %</label>
                <input type="number" name="payout_split" value="100" min="1" max="100" required oninput="calculatePayoutTDS()" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
            </div>
            <div class="space-y-1 col-span-1">
                <label class="font-bold text-zinc-700 dark:text-zinc-300">TDS / WHT (%)</label>
                <select name="payout_tds" onchange="calculatePayoutTDS()" class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
                    <option value="10">10% TDS (Sec 194J)</option>
                    <option value="2">2% TDS (Sec 194C)</option>
                    <option value="0">No TDS</option>
                </select>
            </div>
        </div>
        <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-2xl space-y-2 border border-zinc-100 dark:border-zinc-800/80">
            <div class="flex justify-between font-semibold text-zinc-500">
                <span>Gross Share:</span>
                <span id="payout-calc-gross">₹0</span>
            </div>
            <div class="flex justify-between font-semibold text-zinc-500">
                <span>Split Subtotal:</span>
                <span id="payout-calc-subtotal">₹0</span>
            </div>
            <div class="flex justify-between font-semibold text-zinc-500">
                <span>Withholding Tax (TDS):</span>
                <span id="payout-calc-tds" class="text-zinc-900 dark:text-zinc-100 font-extrabold">- ₹0</span>
            </div>
            <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800 flex justify-between font-extrabold text-sm text-zinc-900 dark:text-zinc-100">
                <span>Net Disbursed:</span>
                <span id="payout-calc-net">₹0</span>
            </div>
        </div>
        <div class="space-y-1">
            <label class="font-bold text-zinc-700 dark:text-zinc-300">Notes / Transaction Reference</label>
            <textarea name="payout_notes" rows="2" placeholder="e.g. Split commission for Sunset Villa listing." class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none resize-none"></textarea>
        </div>
        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-2">
            <button type="button" onclick="window.closeProcessPayoutDrawer()" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold">Cancel</button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-zinc-900 text-white font-bold">Process & Payout</button>
        </div>
    </form>
</aside>

<!-- 4. FINANCIAL REPORTS & EMAIL AUTOMATION DRAWER -->
<aside id="cora-financial-reports-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[480px] max-w-[96vw] bg-white dark:bg-zinc-900 shadow-2xl flex flex-col select-none" style="border-left:1px solid #f4f4f5;">
    <!-- Header -->
    <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid #f4f4f5;">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 shrink-0">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Report Automation</h3>
                <p class="text-[10px] text-zinc-500 dark:text-zinc-400">Schedule &amp; deliver financial reports</p>
            </div>
        </div>
        <button type="button" onclick="window.closeFinancialReportsDrawer()" class="p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
            <svg viewBox="0 0 24 24" width="17" height="17" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Scrollable body -->
    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5 text-xs">

        <!-- Section 1: Delivery Frequency -->
        <div class="space-y-2">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-1 h-4 rounded-full bg-zinc-900 dark:bg-zinc-100 shrink-0"></span>
                <h4 class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-widest">Delivery Schedule</h4>
            </div>
            <!-- Daily -->
            <div class="cora-freq-toggle" id="freq-daily" onclick="window.coraToggleFreq('daily')">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div>
                        <div class="font-bold text-[11px]">Daily Digest</div>
                        <div class="text-[10px] text-zinc-400">Every morning at 8:00 AM</div>
                    </div>
                </div>
                <div class="cora-switch" id="switch-daily"></div>
            </div>
            <!-- Weekly -->
            <div class="cora-freq-toggle" id="freq-weekly" onclick="window.coraToggleFreq('weekly')">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <div>
                        <div class="font-bold text-[11px]">Weekly Summary</div>
                        <div class="text-[10px] text-zinc-400">Every Monday at 9:00 AM</div>
                    </div>
                </div>
                <div class="cora-switch" id="switch-weekly"></div>
            </div>
            <!-- Monthly -->
            <div class="cora-freq-toggle" id="freq-monthly" onclick="window.coraToggleFreq('monthly')">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    </div>
                    <div>
                        <div class="font-bold text-[11px]">Monthly P&amp;L Report</div>
                        <div class="text-[10px] text-zinc-400">1st of every month</div>
                    </div>
                </div>
                <div class="cora-switch" id="switch-monthly"></div>
            </div>
            <!-- Quarterly -->
            <div class="cora-freq-toggle" id="freq-quarterly" onclick="window.coraToggleFreq('quarterly')">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="font-bold text-[11px]">Quarterly Tax Summary</div>
                        <div class="text-[10px] text-zinc-400">Jan, Apr, Jul, Oct 1st</div>
                    </div>
                </div>
                <div class="cora-switch" id="switch-quarterly"></div>
            </div>
            <!-- Custom -->
            <div class="cora-freq-toggle" id="freq-custom" onclick="window.coraToggleFreq('custom')">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"></path></svg>
                    </div>
                    <div>
                        <div class="font-bold text-[11px]">Custom Cron Schedule</div>
                        <div class="text-[10px] text-zinc-400">Enter your own expression</div>
                    </div>
                </div>
                <div class="cora-switch" id="switch-custom"></div>
            </div>
            <div id="cora-custom-cron-wrap" class="hidden">
                <input type="text" id="custom-cron-expr" placeholder="e.g. 0 8 * * 1  (Mon 8am)" class="w-full bg-zinc-50 dark:bg-zinc-800 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none font-mono">
                <p class="text-[10px] text-zinc-400 mt-1.5">5-field cron: minute hour day month weekday</p>
            </div>
        </div>

        <div style="height:1px;background:#f4f4f5;"></div>

        <!-- Section 2: Recipients -->
        <div class="space-y-2">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-1 h-4 rounded-full bg-zinc-900 dark:bg-zinc-100 shrink-0"></span>
                <h4 class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-widest">Recipients</h4>
            </div>
            <div class="cora-chip-container" id="cora-email-chips" onclick="document.getElementById('cora-email-chip-input').focus()">
                <input type="text" id="cora-email-chip-input" class="cora-chip-input" placeholder="Add email &amp; press Enter...">
            </div>
            <p class="text-[10px] text-zinc-400">Press <kbd class="px-1 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded text-[9px] font-mono">Enter</kbd> or <kbd class="px-1 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded text-[9px] font-mono">,</kbd> after each email</p>
        </div>

        <div style="height:1px;background:#f4f4f5;"></div>

        <!-- Section 3: Report Contents -->
        <div class="space-y-2">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-1 h-4 rounded-full bg-zinc-900 dark:bg-zinc-100 shrink-0"></span>
                <h4 class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-widest">Report Contents</h4>
            </div>
            <label class="flex items-center justify-between gap-3 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 cursor-pointer" onclick="var sw=this.querySelector('.cora-switch'); var hidden=this.querySelector('input[type=checkbox]'); hidden.checked=!hidden.checked; sw.classList.toggle('on', hidden.checked);">
                <div><div class="font-semibold text-zinc-800 dark:text-zinc-200 text-[11px]">Inline HTML KPI Summary</div><div class="text-[10px] text-zinc-400 mt-0.5">Key numbers right inside the email body</div></div>
                <input type="checkbox" name="opt_inline_summary" id="opt_inline_summary" checked class="sr-only">
                <div class="cora-switch on"></div>
            </label>
            <label class="flex items-center justify-between gap-3 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 cursor-pointer" onclick="var sw=this.querySelector('.cora-switch'); var hidden=this.querySelector('input[type=checkbox]'); hidden.checked=!hidden.checked; sw.classList.toggle('on', hidden.checked);">
                <div><div class="font-semibold text-zinc-800 dark:text-zinc-200 text-[11px]">Attach PDF Statement</div><div class="text-[10px] text-zinc-400 mt-0.5">Full financial statement PDF</div></div>
                <input type="checkbox" name="opt_attach_pdf" id="opt_attach_pdf" checked class="sr-only">
                <div class="cora-switch on"></div>
            </label>
            <label class="flex items-center justify-between gap-3 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 cursor-pointer" onclick="var sw=this.querySelector('.cora-switch'); var hidden=this.querySelector('input[type=checkbox]'); hidden.checked=!hidden.checked; sw.classList.toggle('on', hidden.checked);">
                <div><div class="font-semibold text-zinc-800 dark:text-zinc-200 text-[11px]">Attach CSV Ledger Export</div><div class="text-[10px] text-zinc-400 mt-0.5">Raw data for accountant tools</div></div>
                <input type="checkbox" name="opt_attach_csv" id="opt_attach_csv" class="sr-only">
                <div class="cora-switch"></div>
            </label>
            <label class="flex items-center justify-between gap-3 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 cursor-pointer" onclick="var sw=this.querySelector('.cora-switch'); var hidden=this.querySelector('input[type=checkbox]'); hidden.checked=!hidden.checked; sw.classList.toggle('on', hidden.checked);">
                <div><div class="font-semibold text-zinc-800 dark:text-zinc-200 text-[11px]">Include Revenue Chart Image</div><div class="text-[10px] text-zinc-400 mt-0.5">Bar chart visual snapshot</div></div>
                <input type="checkbox" name="opt_include_chart" id="opt_include_chart" checked class="sr-only">
                <div class="cora-switch on"></div>
            </label>
        </div>

        <div style="height:1px;background:#f4f4f5;"></div>

        <!-- Section 4: Manual Export -->
        <div class="space-y-3">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-1 h-4 rounded-full bg-zinc-900 dark:bg-zinc-100 shrink-0"></span>
                <h4 class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-widest">Manual Export</h4>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="space-y-1">
                    <label class="font-semibold text-zinc-500 text-[10px] uppercase tracking-wide">Period</label>
                    <select id="report_period" name="report_period" class="w-full bg-zinc-50 dark:bg-zinc-800 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none font-medium">
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="last_quarter">Last Quarter</option>
                        <option value="year_to_date">Year to Date</option>
                        <option value="last_year">Last Year</option>
                        <option value="all_time">All Time</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-zinc-500 text-[10px] uppercase tracking-wide">Format</label>
                    <select id="report_format" name="report_format" class="w-full bg-zinc-50 dark:bg-zinc-800 rounded-xl p-2.5 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none font-medium">
                        <option value="pdf">PDF Statement</option>
                        <option value="csv">CSV Export</option>
                    </select>
                </div>
            </div>
            <button type="button" onclick="window.triggerReportGeneration()" class="w-full py-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-zinc-100 font-bold text-xs transition-colors flex items-center justify-center gap-2">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Generate &amp; Download
            </button>
        </div>

    </div><!-- /scrollable body -->

    <!-- Footer -->
    <div class="px-5 py-4 flex items-center gap-2" style="border-top:1px solid #f4f4f5;">
        <button type="button" onclick="window.coraTestSendReport()" class="flex-1 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs transition-colors flex items-center justify-center gap-2">
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="22 2 11 13"></polyline><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            Test Send
        </button>
        <button type="button" onclick="window.coraSaveReportSchedule()" class="flex-1 py-2.5 rounded-xl bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 font-bold text-xs transition-colors hover:bg-black dark:hover:bg-white flex items-center justify-center gap-2">
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Schedule
        </button>
    </div>
</aside>

<!-- SCRIPT LOGIC FOR TAB SWITCHING & DRAWERS -->
<script>
(function() {
    'use strict';

    // Relocate drawers to body to bypass z-index stacking context constraints relative to global backdrop
    function moveDrawersToBody() {
        var drawers = ['cora-add-ledger-drawer', 'cora-create-invoice-drawer', 'cora-process-payout-drawer', 'cora-financial-reports-drawer'];
        drawers.forEach(function(id) {
            var el = document.getElementById(id);
            if (el && el.parentNode !== document.body) {
                document.body.appendChild(el);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', moveDrawersToBody);
    } else {
        moveDrawersToBody();
    }

    // 1. VANILLA JS DRAWER & MENU MANAGEMENT
    window.toggleFinancialActionMenu = function(e) {
        if (e) e.stopPropagation();
        var popDesktop = document.getElementById('cora-fin-action-popover');
        var popMobile = document.getElementById('cora-fin-action-popover-mobile');
        var datePop = document.getElementById('cora-fin-date-popover');
        
        if (datePop) datePop.classList.add('hidden');
        
        // Toggle active one based on visibility/existence
        if (popDesktop && window.getComputedStyle(popDesktop.parentNode).display !== 'none') {
            if (popMobile) popMobile.classList.add('hidden');
            popDesktop.classList.toggle('hidden');
        } else if (popMobile) {
            if (popDesktop) popDesktop.classList.add('hidden');
            popMobile.classList.toggle('hidden');
        }
    };

    window.toggleFinancialDatePopover = function(e) {
        if (e) e.stopPropagation();
        var pop = document.getElementById('cora-fin-date-popover');
        if (pop) pop.classList.toggle('hidden');
        
        var popDesktop = document.getElementById('cora-fin-action-popover');
        var popMobile = document.getElementById('cora-fin-action-popover-mobile');
        if (popDesktop) popDesktop.classList.add('hidden');
        if (popMobile) popMobile.classList.add('hidden');
    };

    document.addEventListener('click', function() {
        var popDesktop = document.getElementById('cora-fin-action-popover');
        if (popDesktop && !popDesktop.classList.contains('hidden')) popDesktop.classList.add('hidden');
        var popMobile = document.getElementById('cora-fin-action-popover-mobile');
        if (popMobile && !popMobile.classList.contains('hidden')) popMobile.classList.add('hidden');
        var datePop = document.getElementById('cora-fin-date-popover');
        if (datePop && !datePop.classList.contains('hidden')) datePop.classList.add('hidden');
    });

    window.selectFinancialDateRange = function(label, rangeStr) {
        var el = document.getElementById('cora-selected-date-range');
        if (el) el.innerText = rangeStr;

        var pop = document.getElementById('cora-fin-date-popover');
        if (pop) pop.classList.add('hidden');

        if (window.coraShowToast) {
            window.coraShowToast('Date range filtered: ' + label, 'success');
        }

        filterLedgerTableByDate(rangeStr);
    };

    function filterLedgerTableByDate(rangeStr) {
        var rows = document.querySelectorAll('#cora-financial-table-body tr');
        if (!rows.length) return;

        // If it's the default placeholder or all time, show all
        if (rangeStr.indexOf('2025') !== -1 || rangeStr.toLowerCase() === 'all time') {
            for (var i = 0; i < rows.length; i++) {
                rows[i].style.display = '';
            }
            return;
        }

        // Parse range: e.g. "01 Apr – 30 Jun 2026"
        var parts = rangeStr.split('–');
        if (parts.length < 2) return;
        var startPart = parts[0].trim(); // e.g. "01 Apr"
        var endPart = parts[1].trim();   // e.g. "30 Jun 2026"
        
        var endTokens = endPart.split(' ');
        var year = endTokens[endTokens.length - 1]; // "2026"

        // Get months involved
        var months = [];
        var monthMap = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        
        var startMonthStr = startPart.split(' ').pop().toLowerCase().substring(0, 3);
        var endMonthStr = endTokens[endTokens.length - 2].toLowerCase().substring(0, 3);
        
        var startIndex = monthMap.indexOf(startMonthStr);
        var endIndex = monthMap.indexOf(endMonthStr);
        
        if (startIndex !== -1 && endIndex !== -1) {
            for (var m = startIndex; m <= endIndex; m++) {
                months.push(monthMap[m]);
            }
        } else {
            months.push(startMonthStr);
        }

        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var dateCell = row.cells[0];
            if (!dateCell) continue;
            var dateText = dateCell.textContent.toLowerCase(); // e.g. "12 may 2026"
            
            var matchesYear = dateText.indexOf(year) !== -1;
            var matchesMonth = false;
            for (var k = 0; k < months.length; k++) {
                if (dateText.indexOf(months[k]) !== -1) {
                    matchesMonth = true;
                    break;
                }
            }

            if (matchesYear && matchesMonth) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    // Backdrop integration helper
    function setBackdrop(show) {
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) {
            if (show) {
                backdrop.classList.remove('hidden');
                backdrop.style.display = 'block';
                backdrop.style.pointerEvents = 'auto';
            } else {
                backdrop.classList.add('hidden');
                backdrop.style.display = 'none';
                backdrop.style.pointerEvents = 'none';
            }
        }
    }

    window.openAddLedgerDrawer = function() {
        var d = document.getElementById('cora-add-ledger-drawer');
        if (d) d.classList.remove('collapsed');
        setBackdrop(true);
    };

    window.closeAddLedgerDrawer = function() {
        var d = document.getElementById('cora-add-ledger-drawer');
        if (d) d.classList.add('collapsed');
        setBackdrop(false);
    };

    window.openCreateInvoiceDrawer = function() {
        var d = document.getElementById('cora-create-invoice-drawer');
        if (d) d.classList.remove('collapsed');
        setBackdrop(true);
    };

    window.closeCreateInvoiceDrawer = function() {
        var d = document.getElementById('cora-create-invoice-drawer');
        if (d) d.classList.add('collapsed');
        setBackdrop(false);
    };

    window.openProcessPayoutDrawer = function() {
        var d = document.getElementById('cora-process-payout-drawer');
        if (d) d.classList.remove('collapsed');
        setBackdrop(true);
        if (typeof window.calculatePayoutTDS === 'function') {
            window.calculatePayoutTDS();
        }
    };

    window.closeProcessPayoutDrawer = function() {
        var d = document.getElementById('cora-process-payout-drawer');
        if (d) d.classList.add('collapsed');
        setBackdrop(false);
    };

    window.openFinancialReportsDrawer = function() {
        var d = document.getElementById('cora-financial-reports-drawer');
        if (d) d.classList.remove('collapsed');
        setBackdrop(true);
    };

    window.closeFinancialReportsDrawer = function() {
        var d = document.getElementById('cora-financial-reports-drawer');
        if (d) d.classList.add('collapsed');
        setBackdrop(false);
    };

    // Override global close drawers function to include our financials drawers
    var originalCloseAll = window.coraCloseAllDrawers;
    window.coraCloseAllDrawers = function() {
        if (typeof originalCloseAll === 'function') {
            originalCloseAll();
        }
        window.closeAddLedgerDrawer();
        window.closeCreateInvoiceDrawer();
        window.closeProcessPayoutDrawer();
        window.closeFinancialReportsDrawer();
    };

    window.calculatePayoutTDS = function() {
        var form = document.getElementById('cora-process-payout-form');
        if (!form) return;
        var amount = parseFloat(form.payout_amount.value) || 0;
        var split = parseFloat(form.payout_split.value) || 100;
        var tdsPercent = parseFloat(form.payout_tds.value) || 0;

        var subtotal = amount * (split / 100);
        var tds = subtotal * (tdsPercent / 100);
        var net = subtotal - tds;

        var grossEl = document.getElementById('payout-calc-gross');
        var subEl = document.getElementById('payout-calc-subtotal');
        var tdsEl = document.getElementById('payout-calc-tds');
        var netEl = document.getElementById('payout-calc-net');

        if (grossEl) grossEl.innerText = '₹' + amount.toLocaleString('en-IN');
        if (subEl) subEl.innerText = '₹' + subtotal.toLocaleString('en-IN');
        if (tdsEl) tdsEl.innerText = '- ₹' + tds.toLocaleString('en-IN');
        if (netEl) netEl.innerText = '₹' + net.toLocaleString('en-IN');
    };

    window.switchFinancialTab = function(tabId) {
        var contents = document.querySelectorAll('.cora-fin-tab-content');
        for (var i = 0; i < contents.length; i++) {
            contents[i].classList.add('hidden');
        }
        var target = document.getElementById(tabId);
        if (target) target.classList.remove('hidden');

        var tabs = document.querySelectorAll('.cora-financial-tab');
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('bg-zinc-900', 'text-white', 'dark:bg-zinc-100', 'dark:text-zinc-900', 'shadow-xs');
            tabs[i].classList.add('text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-800');
        }
        
        var activeBtn = document.getElementById('btn-tab-' + tabId);
        if (activeBtn) {
            activeBtn.classList.add('bg-zinc-900', 'text-white', 'dark:bg-zinc-100', 'dark:text-zinc-900', 'shadow-xs');
            activeBtn.classList.remove('text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-800');
        }
    };

    window.currentLedgerPill = 'all';

    window.filterLedgerByPill = function(catKey, btnEl) {
        window.currentLedgerPill = catKey || 'all';
        var pills = document.querySelectorAll('.fin-pill-filter');
        for (var i = 0; i < pills.length; i++) {
            pills[i].classList.remove('bg-zinc-900', 'text-white', 'dark:bg-zinc-100', 'dark:text-zinc-900');
            pills[i].classList.add('bg-zinc-50', 'dark:bg-zinc-800', 'text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-700');
        }
        
        if (btnEl) {
            btnEl.classList.add('bg-zinc-900', 'text-white', 'dark:bg-zinc-100', 'dark:text-zinc-900');
            btnEl.classList.remove('bg-zinc-50', 'dark:bg-zinc-800', 'text-zinc-600', 'dark:text-zinc-400', 'hover:bg-zinc-100', 'dark:hover:bg-zinc-700');
        }

        window.filterLedgerTable();
    };

    window.filterLedgerTable = function() {
        var searchEl = document.getElementById('fin-ledger-search');
        var query = searchEl ? searchEl.value.toLowerCase() : '';
        var pill = window.currentLedgerPill ? window.currentLedgerPill.toLowerCase() : 'all';

        var rows = document.querySelectorAll('#cora-financial-table-body tr');
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var text = row.textContent.toLowerCase();
            var matchesQuery = !query || text.indexOf(query) !== -1;
            var matchesPill = (pill === 'all') || text.indexOf(pill) !== -1 || (pill.indexOf('food') !== -1 && (text.indexOf('food') !== -1 || text.indexOf('travel') !== -1)) || (pill.indexOf('gear') !== -1 && (text.indexOf('gear') !== -1 || text.indexOf('tech') !== -1)) || (pill.indexOf('inflow') !== -1 && text.indexOf('inflow') !== -1);

            if (matchesQuery && matchesPill) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    };

    // 2. AJAX & FORM SUBMIT HANDLERS (BIND SAFE JQUERY ON DOM LOAD)
    function initHandlers($) {
        window.handleAddLedgerSubmit = function(e) {
            e.preventDefault();
            var form = e.target;
            var desc = form.entry_desc.value;
            var amount = form.entry_amount.value;
            var type = form.entry_type.value;
            var cat = form.entry_category.value;
            var status = (type === 'inflow') ? 'received' : 'paid';

            var ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
            var nonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cora_save_transaction',
                    security: nonce,
                    description: desc,
                    amount: amount,
                    type: type,
                    category: cat,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        if (window.coraShowToast) window.coraShowToast('Ledger entry saved to database.', 'success');
                        
                        var tr = document.createElement('tr');
                        tr.className = 'cora-table-row-interact hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors';
                        var isInflow = (type === 'inflow');
                        var dateStr = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

                        tr.innerHTML = `
                            <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold">${dateStr}</td>
                            <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100">${desc}</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]">${cat}</span></td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 ${isInflow ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400'} flex items-center gap-1 w-fit">
                                    <span class="${isInflow ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-500'}">${isInflow ? '↑' : '↓'}</span> ${isInflow ? 'Cash Inflow' : 'Cash Outflow'}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm">₹${Number(amount).toLocaleString('en-IN')}</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">${isInflow ? 'Received' : 'Paid'}</span></td>
                            <td class="px-5 py-4 text-right"><button type="button" onclick="window.coraShowToast('Viewing details', 'info')" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-bold text-base">···</button></td>
                        `;

                        var tbody = document.getElementById('cora-financial-table-body');
                        if (tbody) tbody.insertBefore(tr, tbody.firstChild);

                        form.reset();
                        window.closeAddLedgerDrawer();
                    } else {
                        if (window.coraShowToast) window.coraShowToast(response.data || 'Failed to save entry.', 'error');
                    }
                },
                error: function() {
                    if (window.coraShowToast) window.coraShowToast('Error connecting to server.', 'error');
                }
            });
        };

        window.handleCreateInvoiceSubmit = function(e) {
            e.preventDefault();
            var form = e.target;
            var client = form.invoice_client.value;
            var amount = form.invoice_amount.value;
            var desc = form.invoice_desc.value;
            var tax = form.invoice_tax.value;
            var deposit = form.invoice_deposit.value;
            var due_date = form.invoice_due_date.value;

            var ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
            var nonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cora_ajax_create_invoice',
                    security: nonce,
                    client_name: client,
                    amount: amount,
                    tax_rate: tax,
                    deposit_pct: deposit,
                    due_date: due_date,
                    description: desc
                },
                success: function(response) {
                    if (response.success) {
                        if (window.coraShowToast) window.coraShowToast('Client invoice generated & saved.', 'success');
                        form.reset();
                        window.closeCreateInvoiceDrawer();
                    } else {
                        if (window.coraShowToast) window.coraShowToast(response.data || 'Failed to create invoice.', 'error');
                    }
                },
                error: function() {
                    if (window.coraShowToast) window.coraShowToast('Error connecting to server.', 'error');
                }
            });
        };

        window.handleProcessPayoutSubmit = function(e) {
            e.preventDefault();
            var form = e.target;
            var recipient = form.payout_recipient.value;
            var role = form.payout_role.value;
            var amount = form.payout_amount.value;
            var split = form.payout_split.value;
            var tds = form.payout_tds.value;
            var notes = form.payout_notes.value;
            var industry = form.payout_industry.value;

            var ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
            var nonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cora_ajax_process_payout',
                    security: nonce,
                    recipient_name: recipient,
                    recipient_role: role,
                    amount: amount,
                    split_pct: split,
                    tax_pct: tds,
                    notes: notes,
                    industry: industry
                },
                success: function(response) {
                    if (response.success) {
                        if (window.coraShowToast) window.coraShowToast('Team payout processed and logged.', 'success');
                        form.reset();
                        window.closeProcessPayoutDrawer();
                        
                        var net = (amount * (split / 100)) * (1 - (tds / 100));
                        var tr = document.createElement('tr');
                        tr.className = 'cora-table-row-interact hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors';
                        var dateStr = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

                        tr.innerHTML = `
                            <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold">${dateStr}</td>
                            <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100">Payout to ${recipient} (${role.toUpperCase()})</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]">Agent / Crew Payouts</span></td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 flex items-center gap-1 w-fit">
                                    <span class="text-zinc-500">↓</span> Cash Outflow
                                </span>
                            </td>
                            <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm">₹${Number(net).toLocaleString('en-IN')}</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">Paid</span></td>
                            <td class="px-5 py-4 text-right"><button type="button" onclick="window.coraShowToast('Viewing details', 'info')" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-bold text-base">···</button></td>
                        `;

                        var tbody = document.getElementById('cora-financial-table-body');
                        if (tbody) tbody.insertBefore(tr, tbody.firstChild);
                    } else {
                        if (window.coraShowToast) window.coraShowToast(response.data || 'Failed to process payout.', 'error');
                    }
                },
                error: function() {
                    if (window.coraShowToast) window.coraShowToast('Error connecting to server.', 'error');
                }
            });
        };

        window.handleFinancialReportsSubmit = function(e) {
            e.preventDefault();
            var form = e.target;
            var schedule = form.report_schedule.value;
            var email = form.report_email.value;
            
            if (window.coraShowToast) {
                window.coraShowToast('Automated reports schedule saved for: ' + email + ' (' + schedule + ')', 'success');
            }
            window.closeFinancialReportsDrawer();
        };

        window.triggerReportGeneration = function() {
            var form = document.getElementById('cora-financial-reports-form');
            var format = form.report_format.value;
            var period = form.report_period.value;
            
            var ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
            var nonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '';

            if (window.coraShowToast) window.coraShowToast('Generating statement...', 'info');

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cora_ajax_generate_financial_pdf_report',
                    security: nonce,
                    period: period,
                    format: format
                },
                success: function(response) {
                    if (response.success) {
                        if (window.coraShowToast) window.coraShowToast('Financial statement generated successfully!', 'success');
                        window.closeFinancialReportsDrawer();
                        
                        if (format === 'pdf') {
                            var w = window.open();
                            w.document.write('<html><head><title>Financial Report</title><style>body{font-family:sans-serif;padding:40px;color:#09090b}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{border-bottom:1px solid #e4e4e7;padding:12px;text-align:left}</style></head><body>');
                            w.document.write('<h2>Agency Financial Statement</h2>');
                            w.document.write('<p>Report Period: ' + period.replace('_', ' ').toUpperCase() + '</p>');
                            w.document.write('<table><thead><tr><th>Date</th><th>Description</th><th>Type</th><th>Amount</th></tr></thead><tbody>');
                            if (response.data && response.data.ledger) {
                                response.data.ledger.forEach(function(item) {
                                    w.document.write('<tr><td>' + item.date + '</td><td>' + item.description + '</td><td>' + item.type + '</td><td>₹' + Number(item.amount).toLocaleString('en-IN') + '</td></tr>');
                                });
                            }
                            w.document.write('</tbody></table></body></html>');
                            w.document.close();
                            w.print();
                        } else {
                            var csvContent = "data:text/csv;charset=utf-8,Date,Description,Type,Amount\n";
                            if (response.data && response.data.ledger) {
                                response.data.ledger.forEach(function(item) {
                                    csvContent += item.date + ',"' + item.description.replace(/"/g, '""') + '",' + item.type + ',' + item.amount + "\n";
                                });
                            }
                            var encodedUri = encodeURI(csvContent);
                            var link = document.createElement("a");
                            link.setAttribute("href", encodedUri);
                            link.setAttribute("download", "financial_report_" + period + ".csv");
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                    } else {
                        if (window.coraShowToast) window.coraShowToast(response.data || 'Generation failed.', 'error');
                    }
                },
                error: function() {
                    if (window.coraShowToast) window.coraShowToast('Error connecting to server.', 'error');
                }
            });
        };
    }

    // Safely bootstrap jQuery
    if (window.jQuery) {
        initHandlers(window.jQuery);
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            if (window.jQuery) {
                initHandlers(window.jQuery);
            }
        });
    }



    // ═══════════════════════════════════════════════════════════════════════
    // SKELETON REVEAL
    // ═══════════════════════════════════════════════════════════════════════
    (function initSkeletonReveal() {
        function revealContent() {
            var skeleton = document.getElementById('cora-fin-skeleton-content');
            var real     = document.getElementById('cora-fin-real-content');
            if (!skeleton || !real) return;
            skeleton.style.transition = 'opacity 0.3s ease';
            skeleton.style.opacity    = '0';
            setTimeout(function() {
                skeleton.style.display = 'none';
                real.style.opacity     = '1';
                real.style.pointerEvents = 'auto';
            }, 300);
        }
        // Simulate a short async load (650ms) to let CSS animations finish
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() { setTimeout(revealContent, 650); });
        } else {
            setTimeout(revealContent, 650);
        }
    })();

    // ═══════════════════════════════════════════════════════════════════════
    // FREQ TOGGLE SYSTEM
    // ═══════════════════════════════════════════════════════════════════════
    window._coraFreqState = { daily: false, weekly: false, monthly: false, quarterly: false, custom: false };

    window.coraToggleFreq = function(freq) {
        var state = window._coraFreqState;
        state[freq] = !state[freq];

        var toggle = document.getElementById('freq-' + freq);
        var sw     = document.getElementById('switch-' + freq);
        if (toggle) toggle.classList.toggle('active', state[freq]);
        if (sw)     sw.classList.toggle('on', state[freq]);

        // Show/hide custom cron field
        if (freq === 'custom') {
            var wrap = document.getElementById('cora-custom-cron-wrap');
            if (wrap) wrap.classList.toggle('hidden', !state[freq]);
        }
    };

    // Load saved schedule state
    (function loadSavedSchedule() {
        var ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
        var nonce   = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '';
        if (typeof jQuery === 'undefined') return;
        jQuery.ajax({
            url: ajaxUrl, type: 'POST',
            data: { action: 'cora_get_financial_schedule', security: nonce },
            success: function(res) {
                if (!res.success || !res.data) return;
                var s = res.data;
                ['daily','weekly','monthly','quarterly','custom'].forEach(function(f) {
                    if (s[f]) {
                        window._coraFreqState[f] = false; // will be toggled to true
                        window.coraToggleFreq(f);
                    }
                });
                // restore recipients
                if (s.recipients && Array.isArray(s.recipients)) {
                    s.recipients.forEach(function(email) { window.coraAddEmailChip(email); });
                }
                if (s.custom_cron) {
                    var inp = document.getElementById('custom-cron-expr');
                    if (inp) inp.value = s.custom_cron;
                }
            }
        });
    })();

    // ═══════════════════════════════════════════════════════════════════════
    // EMAIL CHIP INPUT
    // ═══════════════════════════════════════════════════════════════════════
    window._coraEmailRecipients = [];

    window.coraAddEmailChip = function(email) {
        email = email.trim().replace(/,+$/, '');
        if (!email || !email.includes('@')) return;
        if (window._coraEmailRecipients.indexOf(email) !== -1) return;
        window._coraEmailRecipients.push(email);

        var container = document.getElementById('cora-email-chips');
        var input     = document.getElementById('cora-email-chip-input');
        if (!container || !input) return;

        var chip = document.createElement('div');
        chip.className = 'cora-chip';
        chip.dataset.email = email;
        chip.innerHTML = '<span>' + email + '</span><button type="button" onclick="window.coraRemoveChip(this)" title="Remove">&times;</button>';
        container.insertBefore(chip, input);
        input.value = '';
    };

    window.coraRemoveChip = function(btn) {
        var chip  = btn.closest('.cora-chip');
        var email = chip ? chip.dataset.email : null;
        if (email) {
            var idx = window._coraEmailRecipients.indexOf(email);
            if (idx !== -1) window._coraEmailRecipients.splice(idx, 1);
        }
        if (chip) chip.remove();
    };

    (function initChipInput() {
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('cora-email-chip-input');
            if (!input) return;
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    window.coraAddEmailChip(input.value);
                } else if (e.key === 'Backspace' && !input.value) {
                    var chips = document.querySelectorAll('#cora-email-chips .cora-chip');
                    if (chips.length) {
                        var last = chips[chips.length - 1];
                        var email = last.dataset.email;
                        var idx   = window._coraEmailRecipients.indexOf(email);
                        if (idx !== -1) window._coraEmailRecipients.splice(idx, 1);
                        last.remove();
                    }
                }
            });
            input.addEventListener('blur', function() {
                if (input.value.trim()) window.coraAddEmailChip(input.value);
            });
        });
    })();

    // ═══════════════════════════════════════════════════════════════════════
    // SAVE SCHEDULE
    // ═══════════════════════════════════════════════════════════════════════
    window.coraSaveReportSchedule = function() {
        var state     = window._coraFreqState;
        var recipients = window._coraEmailRecipients.slice();
        var customCron = (document.getElementById('custom-cron-expr') || {}).value || '';
        var incPdf    = (document.getElementById('opt_attach_pdf')    || {}).checked ? 1 : 0;
        var incCsv    = (document.getElementById('opt_attach_csv')    || {}).checked ? 1 : 0;
        var incChart  = (document.getElementById('opt_include_chart') || {}).checked ? 1 : 0;
        var incInline = (document.getElementById('opt_inline_summary')|| {}).checked ? 1 : 0;

        if (!recipients.length) {
            if (window.coraShowToast) window.coraShowToast('Please add at least one recipient email.', 'error');
            return;
        }

        var ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
        var nonce   = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '';

        if (typeof jQuery === 'undefined') return;
        jQuery.ajax({
            url: ajaxUrl, type: 'POST',
            data: {
                action:          'cora_save_financial_schedule_v2',
                security:        nonce,
                daily:           state.daily    ? 1 : 0,
                weekly:          state.weekly   ? 1 : 0,
                monthly:         state.monthly  ? 1 : 0,
                quarterly:       state.quarterly? 1 : 0,
                custom:          state.custom   ? 1 : 0,
                custom_cron:     customCron,
                recipients:      recipients.join(','),
                include_pdf:     incPdf,
                include_csv:     incCsv,
                include_chart:   incChart,
                include_inline:  incInline
            },
            success: function(res) {
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast('Report schedule saved successfully.', 'success');
                    window.closeFinancialReportsDrawer();
                } else {
                    if (window.coraShowToast) window.coraShowToast(res.data || 'Failed to save schedule.', 'error');
                }
            },
            error: function() {
                if (window.coraShowToast) window.coraShowToast('Server error. Please try again.', 'error');
            }
        });
    };

    // ═══════════════════════════════════════════════════════════════════════
    // TEST SEND
    // ═══════════════════════════════════════════════════════════════════════
    window.coraTestSendReport = function() {
        var recipients = window._coraEmailRecipients.slice();
        if (!recipients.length) {
            if (window.coraShowToast) window.coraShowToast('Add at least one recipient to test send.', 'error');
            return;
        }
        var period = (document.getElementById('report_period') || {}).value || 'this_month';
        var ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
        var nonce   = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '';

        if (window.coraShowToast) window.coraShowToast('Sending test report to ' + recipients[0] + '...', 'info');

        if (typeof jQuery === 'undefined') return;
        jQuery.ajax({
            url: ajaxUrl, type: 'POST',
            data: {
                action:    'cora_test_send_financial_report',
                security:  nonce,
                recipient: recipients[0],
                period:    period
            },
            success: function(res) {
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast('Test report sent to ' + recipients[0], 'success');
                } else {
                    if (window.coraShowToast) window.coraShowToast(res.data || 'Test send failed.', 'error');
                }
            },
            error: function() {
                if (window.coraShowToast) window.coraShowToast('Server error during test send.', 'error');
            }
        });
    };

})();
</script>
