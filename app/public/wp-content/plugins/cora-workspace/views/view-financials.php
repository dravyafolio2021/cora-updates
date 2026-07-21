<?php
/**
 * Cora Workspace - Modular Financial View
 * 
 * Notion/Shopify Monochromatic Aesthetic (Slate/Zinc/Gray tones, thin clean vector SVGs).
 * High-performance cashflow intelligence, multi-tab accounting, and right-sliding side drawers.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Fetch ledger or build fallback realistic datasets
$financial_entries = function_exists( 'cora_db_get_ledger' ) ? cora_db_get_ledger() : array();
?>

<div id="cora-financial-overview-root" class="space-y-6 text-zinc-900 dark:text-zinc-100 font-sans">

    <!-- 1. HEADER & ACTION TOOLBAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-zinc-200/80 dark:border-zinc-800">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shrink-0">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Financial Control & Intelligence</h1>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 pl-11">
                Unified financial workspace for cash inflows, operating outflows, retainer billing, and team payouts.
            </p>
        </div>

        <!-- Primary Action Toolbar -->
        <div class="flex items-center gap-2 shrink-0 relative">
            <!-- Floating Popover Action Menu Button -->
            <div class="relative">
                <button type="button" onclick="toggleFinancialActionMenu(event)" class="px-3.5 py-2 bg-zinc-950 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-900 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer shadow-xs active:scale-95">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>+ New Action</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>

                <!-- Popover Action Menu Dropdown -->
                <div id="cora-fin-action-popover" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl z-50 p-1 space-y-0.5 animate-fade-in select-none">
                    <button type="button" onclick="toggleFinancialActionMenu(); openAddLedgerDrawer();" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Add Ledger Entry</span>
                    </button>
                    <button type="button" onclick="toggleFinancialActionMenu(); openCreateInvoiceDrawer();" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <span>Create Client Invoice</span>
                    </button>
                    <button type="button" onclick="toggleFinancialActionMenu(); openProcessPayoutDrawer();" class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center gap-2 transition-colors">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                        <span>Process Payout</span>
                    </button>
                </div>
            </div>

            <button type="button" onclick="openFinancialReportsDrawer()" class="px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-750 text-xs font-semibold rounded-xl transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Automated Reports</span>
            </button>

            <button type="button" onclick="exportFinancialsCSV()" class="p-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-750 rounded-xl transition-colors cursor-pointer shadow-2xs" title="Export Ledger CSV">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            </button>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800 p-2.5 rounded-xl flex items-center justify-between gap-3 text-xs select-none">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <label for="financial-filter-period" class="font-medium text-zinc-500">Period:</label>
                <select id="financial-filter-period" onchange="handlePeriodChange(this.value)" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-2.5 py-1 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none font-medium">
                    <option value="this_month" selected>This Month</option>
                    <option value="all_time">All Time</option>
                    <option value="today">Today</option>
                    <option value="this_week">This Week</option>
                    <option value="this_quarter">This Quarter</option>
                    <option value="ytd">Year to Date (YTD)</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <div id="financial-custom-date-container" class="hidden flex items-center gap-2">
                <input type="date" id="financial-custom-start-date" onchange="handleCustomDateChange()" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-2 py-1 text-xs text-zinc-800 dark:text-zinc-200">
                <span class="text-zinc-400">to</span>
                <input type="date" id="financial-custom-end-date" onchange="handleCustomDateChange()" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-2 py-1 text-xs text-zinc-800 dark:text-zinc-200">
            </div>
        </div>

        <div class="flex items-center gap-2 text-[11px] text-zinc-500 font-medium">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Single Workspace Control</span>
        </div>
    </div>

    <!-- 2. KPI METRIC CARDS ROW (STRICT NOTION/SHOPIFY MONOCHROMATIC PALETTE) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Gross Revenue (Inflows) -->
        <div id="financial-kpi-revenue" class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Gross Revenue (Inflows)</span>
                <span class="p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                        <polyline points="17 6 23 6 23 12"></polyline>
                    </svg>
                </span>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight" id="kpi-val-revenue">₹485,000</div>
                <div class="flex items-center gap-1.5 mt-1.5">
                    <span class="inline-flex items-center text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded border border-zinc-200 dark:border-zinc-700">
                        +12.4% vs last period
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Expenses (Outflows) -->
        <div id="financial-kpi-expenses" class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Total Expenses (Outflows)</span>
                <span class="p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline>
                        <polyline points="17 18 23 18 23 12"></polyline>
                    </svg>
                </span>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight" id="kpi-val-expenses">₹162,400</div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1.5">Operating, Production & Gear Costs</p>
            </div>
        </div>

        <!-- Card 3: Net Profit & Profit Margin % -->
        <div id="financial-kpi-net-profit" class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Net Profit</span>
                <span class="p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="20" x2="12" y2="10"></line>
                        <line x1="18" y1="20" x2="18" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="16"></line>
                    </svg>
                </span>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight" id="kpi-val-net-profit">₹322,600</div>
                <div class="flex items-center gap-1.5 mt-1.5">
                    <span class="inline-flex items-center text-[11px] font-bold text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 px-2 py-0.5 rounded-full bg-zinc-50 dark:bg-zinc-800" id="kpi-val-margin-pill">
                        66.5% Margin
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 4: Pending Receivables & Client Dues -->
        <div id="financial-kpi-pending" class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Pending Receivables</span>
                <span class="p-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </span>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight" id="kpi-val-pending">₹95,000</div>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1.5" id="kpi-val-pending-count">4 Outstanding Client Invoices</p>
            </div>
        </div>
    </div>

    <!-- 3. REVENUE ANALYTICS & CASHFLOW GRAPH -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/85 dark:border-zinc-800 rounded-xl p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3 flex-wrap gap-2">
            <div class="flex items-center gap-2">
                <div class="p-1.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Revenue Analytics & Cashflow Graph</h3>
            </div>

            <!-- Monochromatic Legend -->
            <div class="flex items-center gap-4 text-xs font-medium">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-sm bg-zinc-900 dark:bg-zinc-100 inline-block"></span>
                    <span class="text-zinc-700 dark:text-zinc-300 font-semibold">Cash Inflows</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-sm bg-zinc-300 dark:bg-zinc-700 inline-block"></span>
                    <span class="text-zinc-600 dark:text-zinc-400 font-medium">Cash Outflows</span>
                </div>
            </div>
        </div>

        <!-- Monochromatic SVG Visualization Container -->
        <div id="financial-analytics-chart-container" class="relative w-full h-[220px] pt-2 select-none">
            <svg class="w-full h-full overflow-visible" viewBox="0 0 700 200" preserveAspectRatio="none">
                <!-- Background Gridlines -->
                <line x1="40" y1="20" x2="680" y2="20" stroke="currentColor" class="text-zinc-100 dark:text-zinc-800" stroke-dasharray="3 3"/>
                <line x1="40" y1="60" x2="680" y2="60" stroke="currentColor" class="text-zinc-100 dark:text-zinc-800" stroke-dasharray="3 3"/>
                <line x1="40" y1="100" x2="680" y2="100" stroke="currentColor" class="text-zinc-100 dark:text-zinc-800" stroke-dasharray="3 3"/>
                <line x1="40" y1="140" x2="680" y2="140" stroke="currentColor" class="text-zinc-100 dark:text-zinc-800" stroke-dasharray="3 3"/>
                <line x1="40" y1="170" x2="680" y2="170" stroke="currentColor" class="text-zinc-200 dark:text-zinc-700" stroke-width="1"/>

                <!-- Monthly Inflow Line Path (Solid Black/White) -->
                <path d="M 60 140 Q 150 110, 250 90 T 450 40 T 650 30" fill="none" stroke="currentColor" class="text-zinc-900 dark:text-zinc-100" stroke-width="2.5" stroke-linecap="round"/>
                
                <!-- Monthly Outflow Line Path (Dashed Gray) -->
                <path d="M 60 160 Q 150 150, 250 130 T 450 110 T 650 120" fill="none" stroke="currentColor" class="text-zinc-400 dark:text-zinc-600" stroke-width="2" stroke-dasharray="4 4" stroke-linecap="round"/>

                <!-- Data Points & Bars for Inflows/Outflows -->
                <!-- Jan -->
                <rect x="75" y="110" width="10" height="60" rx="2" class="fill-zinc-900 dark:fill-zinc-100 opacity-90 hover:opacity-100 transition-all cursor-pointer"><title>Jan Inflows: ₹320,000</title></rect>
                <rect x="87" y="135" width="10" height="35" rx="2" class="fill-zinc-300 dark:fill-zinc-700 opacity-80 hover:opacity-100 transition-all cursor-pointer"><title>Jan Outflows: ₹110,000</title></rect>

                <!-- Feb -->
                <rect x="175" y="90" width="10" height="80" rx="2" class="fill-zinc-900 dark:fill-zinc-100 opacity-90 hover:opacity-100 transition-all cursor-pointer"><title>Feb Inflows: ₹390,000</title></rect>
                <rect x="187" y="125" width="10" height="45" rx="2" class="fill-zinc-300 dark:fill-zinc-700 opacity-80 hover:opacity-100 transition-all cursor-pointer"><title>Feb Outflows: ₹140,000</title></rect>

                <!-- Mar -->
                <rect x="275" y="80" width="10" height="90" rx="2" class="fill-zinc-900 dark:fill-zinc-100 opacity-90 hover:opacity-100 transition-all cursor-pointer"><title>Mar Inflows: ₹420,000</title></rect>
                <rect x="287" y="120" width="10" height="50" rx="2" class="fill-zinc-300 dark:fill-zinc-700 opacity-80 hover:opacity-100 transition-all cursor-pointer"><title>Mar Outflows: ₹150,000</title></rect>

                <!-- Apr -->
                <rect x="375" y="65" width="10" height="105" rx="2" class="fill-zinc-900 dark:fill-zinc-100 opacity-90 hover:opacity-100 transition-all cursor-pointer"><title>Apr Inflows: ₹450,000</title></rect>
                <rect x="387" y="115" width="10" height="55" rx="2" class="fill-zinc-300 dark:fill-zinc-700 opacity-80 hover:opacity-100 transition-all cursor-pointer"><title>Apr Outflows: ₹155,000</title></rect>

                <!-- May -->
                <rect x="475" y="50" width="10" height="120" rx="2" class="fill-zinc-900 dark:fill-zinc-100 opacity-90 hover:opacity-100 transition-all cursor-pointer"><title>May Inflows: ₹470,000</title></rect>
                <rect x="487" y="110" width="10" height="60" rx="2" class="fill-zinc-300 dark:fill-zinc-700 opacity-80 hover:opacity-100 transition-all cursor-pointer"><title>May Outflows: ₹160,000</title></rect>

                <!-- Jun -->
                <rect x="575" y="35" width="10" height="135" rx="2" class="fill-zinc-900 dark:fill-zinc-100 opacity-95 hover:opacity-100 transition-all cursor-pointer"><title>Jun Inflows: ₹485,000</title></rect>
                <rect x="587" y="108" width="10" height="62" rx="2" class="fill-zinc-300 dark:fill-zinc-700 opacity-80 hover:opacity-100 transition-all cursor-pointer"><title>Jun Outflows: ₹162,400</title></rect>

                <!-- X Axis Month Labels -->
                <text x="85" y="190" text-anchor="middle" font-size="11" font-weight="600" class="fill-zinc-500 dark:fill-zinc-400">Jan</text>
                <text x="185" y="190" text-anchor="middle" font-size="11" font-weight="600" class="fill-zinc-500 dark:fill-zinc-400">Feb</text>
                <text x="285" y="190" text-anchor="middle" font-size="11" font-weight="600" class="fill-zinc-500 dark:fill-zinc-400">Mar</text>
                <text x="385" y="190" text-anchor="middle" font-size="11" font-weight="600" class="fill-zinc-500 dark:fill-zinc-400">Apr</text>
                <text x="485" y="190" text-anchor="middle" font-size="11" font-weight="600" class="fill-zinc-500 dark:fill-zinc-400">May</text>
                <text x="585" y="190" text-anchor="middle" font-size="11" font-weight="600" class="fill-zinc-500 dark:fill-zinc-400">Jun</text>
            </svg>
        </div>
    </div>

    <!-- 4. VISUAL CASH ALLOCATION & PROFIT MARGIN BAR (MONOCHROMATIC STYLING) -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm space-y-3.5 select-none">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">Cash Allocation & Profit Margin Breakdown</span>
                <span class="text-[10px] font-bold px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-full border border-zinc-200 dark:border-zinc-700">66.5% Net Margin</span>
            </div>
            <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Gross Revenue: ₹485,000</span>
        </div>

        <!-- Multi-segment visual stacked progress bar (Monochromatic) -->
        <div class="w-full h-3 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex shadow-inner">
            <div class="h-full bg-zinc-900 dark:bg-zinc-100 transition-all" style="width: 66.5%" title="Net Profit Margin: 66.5% (₹322,600)"></div>
            <div class="h-full bg-zinc-600 transition-all" style="width: 15%" title="Operating Rent: 15% (₹72,750)"></div>
            <div class="h-full bg-zinc-400 transition-all" style="width: 8%" title="Gear & Equipment: 8% (₹38,800)"></div>
            <div class="h-full bg-zinc-300 dark:bg-zinc-500 transition-all" style="width: 5%" title="Food & Travel: 5% (₹24,250)"></div>
            <div class="h-full bg-zinc-200 dark:bg-zinc-600 transition-all" style="width: 5.5%" title="Marketing: 5.5% (₹26,675)"></div>
        </div>

        <!-- Legend Pills (Monochromatic Neutral Badges) -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs pt-1">
            <div class="flex items-center gap-2 p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                <span class="w-2.5 h-2.5 rounded-sm bg-zinc-900 dark:bg-zinc-100 shrink-0"></span>
                <div>
                    <div class="text-[11px] font-semibold text-zinc-900 dark:text-zinc-100">Net Profit Margin</div>
                    <div class="text-[11px] font-bold text-zinc-800 dark:text-zinc-200 font-mono">66.5% · ₹322,600</div>
                </div>
            </div>

            <div class="flex items-center gap-2 p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                <span class="w-2.5 h-2.5 rounded-sm bg-zinc-600 shrink-0"></span>
                <div>
                    <div class="text-[11px] font-semibold text-zinc-900 dark:text-zinc-100">Operating Rent</div>
                    <div class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 font-mono">15.0% · ₹72,750</div>
                </div>
            </div>

            <div class="flex items-center gap-2 p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                <span class="w-2.5 h-2.5 rounded-sm bg-zinc-400 shrink-0"></span>
                <div>
                    <div class="text-[11px] font-semibold text-zinc-900 dark:text-zinc-100">Gear & Equipment</div>
                    <div class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 font-mono">8.0% · ₹38,800</div>
                </div>
            </div>

            <div class="flex items-center gap-2 p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                <span class="w-2.5 h-2.5 rounded-sm bg-zinc-300 dark:bg-zinc-500 shrink-0"></span>
                <div>
                    <div class="text-[11px] font-semibold text-zinc-900 dark:text-zinc-100">Food & Travel</div>
                    <div class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 font-mono">5.0% · ₹24,250</div>
                </div>
            </div>

            <div class="flex items-center gap-2 p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                <span class="w-2.5 h-2.5 rounded-sm bg-zinc-200 dark:bg-zinc-600 shrink-0"></span>
                <div>
                    <div class="text-[11px] font-semibold text-zinc-900 dark:text-zinc-100">Marketing</div>
                    <div class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 font-mono">5.5% · ₹26,675</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. SUB-TABS NAVIGATION & TABLES CONTAINER -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/85 dark:border-zinc-800 rounded-xl p-5 shadow-sm space-y-4">
        
        <!-- Sub-Tabs Navigation Bar -->
        <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-3 overflow-x-auto">
            <button type="button" onclick="switchFinancialTab('fin-ledger')" id="btn-tab-fin-ledger" class="cora-financial-tab px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 transition-colors flex items-center gap-1.5 cursor-pointer">
                <span>●</span> Master Ledger
            </button>
            <button type="button" onclick="switchFinancialTab('fin-invoices')" id="btn-tab-fin-invoices" class="cora-financial-tab px-3.5 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors flex items-center gap-1.5 cursor-pointer">
                <span>●</span> Invoices & Billing
            </button>
            <button type="button" onclick="switchFinancialTab('fin-payouts')" id="btn-tab-fin-payouts" class="cora-financial-tab px-3.5 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors flex items-center gap-1.5 cursor-pointer">
                <span>●</span> Agent / Crew Payouts
            </button>
        </div>

        <!-- TAB 1: MASTER LEDGER PANEL -->
        <div id="tab-fin-ledger" class="cora-fin-tab-content space-y-4">
            <!-- Expense Category Quick Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 select-none">
                <button type="button" onclick="filterLedgerByPill('all', this)" class="fin-pill-filter px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 border border-zinc-900 dark:border-zinc-100 transition-all cursor-pointer shrink-0 shadow-xs">All Entries</button>
                <button type="button" onclick="filterLedgerByPill('Food & Travel', this)" class="fin-pill-filter px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 border border-zinc-200 dark:border-zinc-700 transition-all cursor-pointer shrink-0">Food & Travel</button>
                <button type="button" onclick="filterLedgerByPill('Gear & Tech', this)" class="fin-pill-filter px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 border border-zinc-200 dark:border-zinc-700 transition-all cursor-pointer shrink-0">Gear & Tech</button>
                <button type="button" onclick="filterLedgerByPill('Studio Ops & Rent', this)" class="fin-pill-filter px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 border border-zinc-200 dark:border-zinc-700 transition-all cursor-pointer shrink-0">Studio Ops & Rent</button>
                <button type="button" onclick="filterLedgerByPill('Marketing & Listings', this)" class="fin-pill-filter px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 border border-zinc-200 dark:border-zinc-700 transition-all cursor-pointer shrink-0">Marketing & Listings</button>
                <button type="button" onclick="filterLedgerByPill('Agent / Crew Payouts', this)" class="fin-pill-filter px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 border border-zinc-200 dark:border-zinc-700 transition-all cursor-pointer shrink-0">Agent / Crew Payouts</button>
                <button type="button" onclick="filterLedgerByPill('Inflows & Retainers', this)" class="fin-pill-filter px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 border border-zinc-200 dark:border-zinc-700 transition-all cursor-pointer shrink-0">Inflows & Retainers</button>
            </div>

            <!-- Filter Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <select id="fin-ledger-category-filter" onchange="filterLedgerTable()" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-2.5 py-1.5 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none">
                        <option value="all">All Categories</option>
                        <option value="Retainer Fee">Retainer Fee</option>
                        <option value="Commission Split">Commission Split</option>
                        <option value="Gear Rental">Gear Rental</option>
                        <option value="Software & SaaS">Software & SaaS</option>
                        <option value="Day Rate">Day Rate</option>
                        <option value="Studio Ops">Studio Ops</option>
                        <option value="Marketing">Marketing</option>
                    </select>

                    <select id="fin-ledger-type-filter" onchange="filterLedgerTable()" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-2.5 py-1.5 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none">
                        <option value="all">All Types</option>
                        <option value="Inflow">Cash Inflow</option>
                        <option value="Outflow">Cash Outflow</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <input type="text" id="fin-ledger-search" oninput="filterLedgerTable()" placeholder="Search ledger entries..." class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg py-1.5 pl-8 pr-3 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2 text-zinc-400">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            <!-- Master Ledger Table -->
            <div class="overflow-x-auto border border-zinc-200/80 dark:border-zinc-800 rounded-lg">
                <table class="w-full text-xs text-left divide-y divide-zinc-200 dark:divide-zinc-800" id="cora-financial-table">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Date</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Description</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Category</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Amount</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Type</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Property / Shoot Tag</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-150 dark:divide-zinc-800" id="cora-financial-table-body">
                        <!-- Default Seed / Filtered Rows -->
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3 font-medium text-zinc-700 dark:text-zinc-300">2026-07-20</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">Apex Villa 4K Drone Retainer</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400"><span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-[11px]">Retainer Fee</span></td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">+₹45,000</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">Inflow</span></td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700">Completed</span></td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">#SHOOT-8821</td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <button type="button" onclick="window.coraShowToast('Viewing ledger details.', 'info')" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 font-medium">View</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3 font-medium text-zinc-700 dark:text-zinc-300">2026-07-18</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">RED V-Raptor Gear Rental Fee</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400"><span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-[11px]">Gear Rental</span></td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">-₹18,500</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">Outflow</span></td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700">Completed</span></td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">#GEAR-409</td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <button type="button" onclick="window.coraShowToast('Viewing ledger details.', 'info')" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 font-medium">View</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3 font-medium text-zinc-700 dark:text-zinc-300">2026-07-15</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">Brokerage Deal Commission - DLF Greens</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400"><span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-[11px]">Commission Split</span></td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">+₹150,000</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">Inflow</span></td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700">Completed</span></td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">#PROP-1029</td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <button type="button" onclick="window.coraShowToast('Viewing ledger details.', 'info')" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 font-medium">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: INVOICES & BILLING PANEL -->
        <div id="tab-fin-invoices" class="cora-fin-tab-content hidden space-y-4">
            <!-- Filter Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <label for="fin-invoice-status-filter" class="text-xs font-medium text-zinc-500">Status Filter:</label>
                    <select id="fin-invoice-status-filter" onchange="filterInvoiceTable()" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-2.5 py-1.5 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none">
                        <option value="all">All Statuses</option>
                        <option value="Draft">Draft</option>
                        <option value="Deposit Paid">Deposit Paid</option>
                        <option value="Paid in Full">Paid in Full</option>
                        <option value="Overdue">Overdue</option>
                    </select>
                </div>

                <div class="relative w-full sm:w-64">
                    <input type="text" id="fin-invoice-search" oninput="filterInvoiceTable()" placeholder="Search invoice # or client..." class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg py-1.5 pl-8 pr-3 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2 text-zinc-400">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="overflow-x-auto border border-zinc-200/80 dark:border-zinc-800 rounded-lg">
                <table class="w-full text-xs text-left divide-y divide-zinc-200 dark:divide-zinc-800" id="cora-invoice-table">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Invoice #</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Client Name</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Package / Deal</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Total Amount</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Retainer Paid</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Due Balance</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px] text-right">PDF / Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-150 dark:divide-zinc-800" id="cora-invoice-table-body">
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3 font-mono font-bold text-zinc-900 dark:text-zinc-100">INV-2026-001</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">Apex Realty Group</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">Luxury Villa 4K Drone Pass</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹45,000</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">₹22,500 (50%)</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹22,500</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">Deposit Paid</span></td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <button type="button" onclick="window.coraShowToast('Invoice PDF generated', 'success')" class="text-zinc-700 hover:text-zinc-950 dark:text-zinc-300 font-semibold">PDF</button>
                                <button type="button" onclick="window.coraShowToast('Invoice link copied to clipboard', 'info')" class="text-zinc-500 hover:text-zinc-900 font-medium">Share</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3 font-mono font-bold text-zinc-900 dark:text-zinc-100">INV-2026-002</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">Oberoi Luxury Estates</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">Commercial Tower Campaign</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹120,000</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">₹120,000 (100%)</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹0</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700">Paid in Full</span></td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <button type="button" onclick="window.coraShowToast('Invoice PDF generated', 'success')" class="text-zinc-700 hover:text-zinc-950 dark:text-zinc-300 font-semibold">PDF</button>
                                <button type="button" onclick="window.coraShowToast('Invoice link copied to clipboard', 'info')" class="text-zinc-500 hover:text-zinc-900 font-medium">Share</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3 font-mono font-bold text-zinc-900 dark:text-zinc-100">INV-2026-003</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">Skylight Penthouse LLC</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">Interior HDR & Matterport 3D</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹72,500</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">₹0 (0%)</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹72,500</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100">Overdue</span></td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <button type="button" onclick="window.coraShowToast('Invoice PDF generated', 'success')" class="text-zinc-700 hover:text-zinc-950 dark:text-zinc-300 font-semibold">PDF</button>
                                <button type="button" onclick="window.coraShowToast('Payment reminder dispatched via email', 'info')" class="text-zinc-500 hover:text-zinc-900 font-medium">Reminder</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: AGENT / CREW PAYOUTS PANEL -->
        <div id="tab-fin-payouts" class="cora-fin-tab-content hidden space-y-4">
            <!-- Filter Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <label for="fin-payout-role-filter" class="text-xs font-medium text-zinc-500">Payout Category:</label>
                    <select id="fin-payout-role-filter" onchange="filterPayoutTable()" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-2.5 py-1.5 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none">
                        <option value="all">All Roles & Categories</option>
                        <option value="Agent Commission">Agent Commission (70/30 Split)</option>
                        <option value="Photographer">Photographer Day-Rate</option>
                        <option value="Video Editor">Video Editor Day-Rate</option>
                    </select>
                </div>

                <div class="relative w-full sm:w-64">
                    <input type="text" id="fin-payout-search" oninput="filterPayoutTable()" placeholder="Search recipient..." class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg py-1.5 pl-8 pr-3 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2 text-zinc-400">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            <!-- Payouts Table -->
            <div class="overflow-x-auto border border-zinc-200/80 dark:border-zinc-800 rounded-lg">
                <table class="w-full text-xs text-left divide-y divide-zinc-200 dark:divide-zinc-800" id="cora-payout-table">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Payout ID</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Recipient</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Role / Deal</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Gross Amount</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Split / Deductions</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Net Payout</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Date</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-4 py-3 font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-150 dark:divide-zinc-800" id="cora-payout-table-body">
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3 font-mono font-bold text-zinc-900 dark:text-zinc-100">PAY-8801</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">Rahul Sharma</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">Agent Commission (DLF Capital)</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">₹150,000</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">70% Split (-₹45,000)</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹105,000</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">2026-07-18</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">Processed</span></td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <button type="button" onclick="window.coraShowToast('Payout advice receipt generated', 'info')" class="text-zinc-600 hover:text-zinc-900 font-medium">Receipt</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3 font-mono font-bold text-zinc-900 dark:text-zinc-100">PAY-8802</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">Vikramaditya S.</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">Photographer Day-Rate (Architectural Shoot)</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">₹15,000</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">TDS 10% (-₹1,500)</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹13,500</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">2026-07-16</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">Processed</span></td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <button type="button" onclick="window.coraShowToast('Payout advice receipt generated', 'info')" class="text-zinc-600 hover:text-zinc-900 font-medium">Receipt</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- 5. SIDE DRAWER SHEETS -->

<!-- DRAWER 1: ADD LEDGER ENTRY SHEET -->
<aside id="cora-add-ledger-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/40">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Log Financial Entry</h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Record cash inflow or operating expense in master ledger.</p>
        </div>
        <button type="button" onclick="closeAddLedgerDrawer()" class="p-1 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-add-ledger-form" onsubmit="submitAddLedgerForm(event)" class="p-5 space-y-4 flex-1 overflow-y-auto">
        <div>
            <label for="ledger-entry-type" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Transaction Type</label>
            <select id="ledger-entry-type" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
                <option value="Inflow" selected>Cash Inflow (Income / Retainer)</option>
                <option value="Outflow">Cash Outflow (Expense / Gear / Ops)</option>
            </select>
        </div>

        <div>
            <label for="ledger-entry-category" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Category</label>
            <select id="ledger-entry-category" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
                <option value="Food & Travel">Food & Travel</option>
                <option value="Gear & Equipment">Gear & Equipment</option>
                <option value="Studio Ops & Rent">Studio Ops & Rent</option>
                <option value="Software & SaaS">Software & SaaS</option>
                <option value="Marketing & Advertising">Marketing & Advertising</option>
                <option value="Agent & Crew Payouts">Agent & Crew Payouts</option>
                <option value="Client Retainer Fee" selected>Client Retainer Fee</option>
                <option value="Deal Commission Inflow">Deal Commission Inflow</option>
            </select>
        </div>

        <div>
            <label for="ledger-entry-payment-method" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Payment Method</label>
            <select id="ledger-entry-payment-method" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
                <option value="Bank Transfer" selected>Bank Transfer</option>
                <option value="Corporate Credit Card">Corporate Credit Card</option>
                <option value="UPI / Digital Wallet">UPI / Digital Wallet</option>
                <option value="Cash">Cash</option>
            </select>
        </div>

        <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/80 dark:border-zinc-800">
            <div>
                <span class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Tax Deductible Expense</span>
                <span class="block text-[11px] text-zinc-500 dark:text-zinc-400">Mark entry for automated quarterly tax audit deduction.</span>
            </div>
            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                <input type="checkbox" id="ledger-entry-tax-deductible" class="sr-only peer">
                <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:after:border-zinc-600 peer-checked:bg-zinc-900 dark:peer-checked:bg-zinc-100"></div>
            </label>
        </div>

        <div>
            <label for="ledger-entry-amount" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Amount (₹)</label>
            <input type="number" id="ledger-entry-amount" placeholder="e.g. 25000" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
        </div>

        <div>
            <label for="ledger-entry-tag" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Property / Shoot Tag</label>
            <input type="text" id="ledger-entry-tag" placeholder="e.g. #SHOOT-9021 or #PROP-DLF" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
        </div>

        <div>
            <label for="ledger-entry-date" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Transaction Date</label>
            <input type="date" id="ledger-entry-date" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
        </div>

        <div>
            <label for="ledger-entry-notes" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Description & Notes</label>
            <textarea id="ledger-entry-notes" rows="3" placeholder="Enter transaction details..." class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400"></textarea>
        </div>

        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center gap-2">
            <button type="submit" class="flex-1 py-2 px-4 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 font-semibold text-xs rounded-lg transition-colors cursor-pointer">
                Log Ledger Entry
            </button>
            <button type="button" onclick="closeAddLedgerDrawer()" class="py-2 px-4 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs rounded-lg hover:bg-zinc-50 transition-colors cursor-pointer">
                Cancel
            </button>
        </div>
    </form>
</aside>

<!-- DRAWER 2: CREATE INVOICE SHEET -->
<aside id="cora-create-invoice-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/40">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Create Client Invoice</h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Generate structured retainer billing for client shoots & deals.</p>
        </div>
        <button type="button" onclick="closeCreateInvoiceDrawer()" class="p-1 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-create-invoice-form" onsubmit="submitCreateInvoiceForm(event)" class="p-5 space-y-4 flex-1 overflow-y-auto">
        <div>
            <label for="invoice-client-name" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Client / Agency Name</label>
            <input type="text" id="invoice-client-name" placeholder="e.g. DLF Commercial Group" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
        </div>

        <div>
            <label for="invoice-client-email" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Client Email</label>
            <input type="email" id="invoice-client-email" placeholder="billing@client.com" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
        </div>

        <div>
            <label for="invoice-deal-package" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Deal / Shoot Package</label>
            <input type="text" id="invoice-deal-package" placeholder="e.g. Architectural Video & Drone Cinema" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="invoice-total-amount" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Total Package Value (₹)</label>
                <input type="number" id="invoice-total-amount" placeholder="60000" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
            </div>

            <div>
                <label for="invoice-retainer-pct" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Deposit Retainer %</label>
                <select id="invoice-retainer-pct" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
                    <option value="25">25% Retainer</option>
                    <option value="50" selected>50% Retainer (Standard)</option>
                    <option value="100">100% Upfront</option>
                </select>
            </div>
        </div>

        <div>
            <label for="invoice-due-date" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Payment Due Date</label>
            <input type="date" id="invoice-due-date" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
        </div>

        <div>
            <label for="invoice-line-items" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Line Items & Scope Breakup</label>
            <textarea id="invoice-line-items" rows="3" placeholder="1. 4K Drone Filming (2 Hours) - ₹25,000&#10;2. Interior Lighting & HDR Stills - ₹25,000&#10;3. Color Grading & Master Export - ₹10,000" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400 font-mono text-[11px]"></textarea>
        </div>

        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center gap-2">
            <button type="submit" class="flex-1 py-2 px-4 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 font-semibold text-xs rounded-lg transition-colors cursor-pointer">
                Generate & Dispatch Invoice
            </button>
            <button type="button" onclick="closeCreateInvoiceDrawer()" class="py-2 px-4 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs rounded-lg hover:bg-zinc-50 transition-colors cursor-pointer">
                Cancel
            </button>
        </div>
    </form>
</aside>

<!-- DRAWER 3: PROCESS PAYOUT SHEET -->
<aside id="cora-process-payout-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/40">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Process Commission / Day-Rate Payout</h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Calculate agent commission splits & freelancer compensation.</p>
        </div>
        <button type="button" onclick="closeProcessPayoutDrawer()" class="p-1 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-process-payout-form" onsubmit="submitProcessPayoutForm(event)" class="p-5 space-y-4 flex-1 overflow-y-auto">
        <div>
            <label for="payout-recipient-name" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Recipient Name (Agent / Freelancer)</label>
            <input type="text" id="payout-recipient-name" placeholder="e.g. Rahul Sharma" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
        </div>

        <div>
            <label for="payout-role-select" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Payout Role Type</label>
            <select id="payout-role-select" onchange="calculatePayoutPreview()" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
                <option value="agent" selected>Agent Commission Split (70/30)</option>
                <option value="photographer">Photographer Day-Rate</option>
                <option value="editor">Video Editor Day-Rate</option>
                <option value="drone">Drone Operator Day-Rate</option>
            </select>
        </div>

        <div>
            <label for="payout-gross-amount" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Gross Deal / Day Amount (₹)</label>
            <input type="number" id="payout-gross-amount" oninput="calculatePayoutPreview()" placeholder="100000" required class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="payout-split-pct" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Recipient Split %</label>
                <input type="number" id="payout-split-pct" oninput="calculatePayoutPreview()" value="70" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
            </div>

            <div>
                <label for="payout-tax-pct" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Tax / TDS Deduction %</label>
                <input type="number" id="payout-tax-pct" oninput="calculatePayoutPreview()" value="10" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400">
            </div>
        </div>

        <div>
            <label for="payout-net-amount" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Calculated Net Payout Amount (₹)</label>
            <input type="text" id="payout-net-amount" readonly class="w-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs font-bold text-zinc-900 dark:text-zinc-100 focus:outline-none cursor-not-allowed" value="₹63,000">
        </div>

        <div>
            <label for="payout-notes" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Payment Reference & Notes</label>
            <textarea id="payout-notes" rows="2" placeholder="UPI transaction ID / Bank transfer ref..." class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400"></textarea>
        </div>

        <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center gap-2">
            <button type="submit" class="flex-1 py-2 px-4 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 font-semibold text-xs rounded-lg transition-colors cursor-pointer">
                Process Payout
            </button>
            <button type="button" onclick="closeProcessPayoutDrawer()" class="py-2 px-4 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs rounded-lg hover:bg-zinc-50 transition-colors cursor-pointer">
                Cancel
            </button>
        </div>
    </form>
</aside>

<!-- DRAWER 4: AUTOMATED FINANCIAL REPORTS SHEET -->
<aside id="cora-financial-reports-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[480px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/40">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Automated Financial Reports & Schedule</h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Configure recurring P&L digests and instant report delivery.</p>
        </div>
        <button type="button" onclick="closeFinancialReportsDrawer()" class="p-1 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="p-5 space-y-6 flex-1 overflow-y-auto">
        <!-- Instant Report Generators -->
        <div class="space-y-3">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">One-Click Instant Reports</span>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="generateInstantReport('daily')" class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/60 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-left transition-all cursor-pointer">
                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Daily Cash Digest</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5">Today's inflows vs outflows</div>
                </button>
                <button type="button" onclick="generateInstantReport('weekly')" class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/60 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-left transition-all cursor-pointer">
                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Weekly Summary</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5">7-day cashflow & dues</div>
                </button>
                <button type="button" onclick="generateInstantReport('monthly')" class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/60 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-left transition-all cursor-pointer">
                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Monthly P&L Sheet</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5">Full income & expense statement</div>
                </button>
                <button type="button" onclick="generateInstantReport('quarterly')" class="p-3 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/60 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-left transition-all cursor-pointer">
                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Quarterly Audit</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5">Tax, splits & growth audit</div>
                </button>
            </div>
        </div>

        <!-- Automated Delivery Schedule Options -->
        <div class="space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Automated Delivery Schedule</span>
            
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3 border border-zinc-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800">
                    <div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100">Daily Morning Digest</div>
                        <div class="text-[10px] text-zinc-500">Delivered daily at 8:00 AM</div>
                    </div>
                    <input type="checkbox" id="sched-daily" checked class="accent-zinc-900 dark:accent-zinc-100 w-4 h-4 cursor-pointer">
                </div>

                <div class="flex items-center justify-between p-3 border border-zinc-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800">
                    <div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100">Weekly Cashflow Summary</div>
                        <div class="text-[10px] text-zinc-500">Delivered every Monday at 9:00 AM</div>
                    </div>
                    <input type="checkbox" id="sched-weekly" checked class="accent-zinc-900 dark:accent-zinc-100 w-4 h-4 cursor-pointer">
                </div>

                <div class="flex items-center justify-between p-3 border border-zinc-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800">
                    <div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100">Monthly P&L Statement</div>
                        <div class="text-[10px] text-zinc-500">Delivered on the 1st of every month</div>
                    </div>
                    <input type="checkbox" id="sched-monthly" checked class="accent-zinc-900 dark:accent-zinc-100 w-4 h-4 cursor-pointer">
                </div>

                <div class="flex items-center justify-between p-3 border border-zinc-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800">
                    <div>
                        <div class="font-bold text-zinc-900 dark:text-zinc-100">Quarterly Tax & Commission Breakdown</div>
                        <div class="text-[10px] text-zinc-500">Delivered at end of each fiscal quarter</div>
                    </div>
                    <input type="checkbox" id="sched-quarterly" class="accent-zinc-900 dark:accent-zinc-100 w-4 h-4 cursor-pointer">
                </div>
            </div>

            <!-- Recipient Email -->
            <div class="space-y-1 pt-2">
                <label for="sched-recipient-email" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Recipient Email Digest Address</label>
                <input type="email" id="sched-recipient-email" value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>" class="w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none">
            </div>
        </div>
    </div>

    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center gap-2 bg-zinc-50/50 dark:bg-zinc-800/40">
        <button type="button" onclick="saveFinancialSchedule()" class="flex-1 py-2 px-4 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 font-semibold text-xs rounded-lg transition-colors cursor-pointer">
            Save Schedule Preferences
        </button>
        <button type="button" onclick="closeFinancialReportsDrawer()" class="py-2 px-4 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs rounded-lg hover:bg-zinc-50 transition-colors cursor-pointer">
            Cancel
        </button>
    </div>
</aside>

<!-- 6. JAVASCRIPT HANDLERS & CONTROLLERS -->
<script>
(function() {
    // 6.1 DRAWER CONTROLLERS
    window.toggleFinancialActionMenu = function(e) {
        if (e && e.stopPropagation) e.stopPropagation();
        var pop = document.getElementById('cora-fin-action-popover');
        if (pop) pop.classList.toggle('hidden');
    };

    // Close + New Action popover when clicking anywhere outside
    document.addEventListener('click', function(e) {
        var pop = document.getElementById('cora-fin-action-popover');
        var btn = e.target.closest('button[onclick*="toggleFinancialActionMenu"]');
        if (pop && !pop.classList.contains('hidden')) {
            if (!pop.contains(e.target) && !btn) {
                pop.classList.add('hidden');
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            var pop = document.getElementById('cora-fin-action-popover');
            if (pop) pop.classList.add('hidden');
        }
    });

    window.openFinancialReportsDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            var drawers = document.querySelectorAll('aside[id$="-drawer"]');
            drawers.forEach(function(d) { d.classList.add('collapsed'); });
        }
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');

        var drawer = document.getElementById('cora-financial-reports-drawer');
        if (drawer) drawer.classList.remove('collapsed');
    };

    window.closeFinancialReportsDrawer = function() {
        var drawer = document.getElementById('cora-financial-reports-drawer');
        if (drawer) drawer.classList.add('collapsed');
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) backdrop.classList.add('hidden');
    };

    window.generateInstantReport = function(type) {
        type = type || 'monthly';
        if (window.coraShowToast) window.coraShowToast('Generating ' + type.toUpperCase() + ' financial report...', 'info');

        var ajaxUrl = (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php';
        var nonce   = (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '';

        var fd = new FormData();
        fd.append('action', 'cora_generate_financial_report');
        fd.append('security', nonce);
        fd.append('nonce', nonce);
        fd.append('report_type', type);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast(res.data.message || 'Report generated successfully.', 'success');
                    var overlay = document.getElementById('cora-report-preview-overlay');
                    if (!overlay) {
                        overlay = document.createElement('div');
                        overlay.id = 'cora-report-preview-overlay';
                        overlay.className = 'fixed inset-0 z-[10000] bg-zinc-950/70 backdrop-blur-sm flex items-center justify-center p-4';
                        overlay.innerHTML = `
                            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
                                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/40">
                                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                        Financial Report Summary
                                    </h3>
                                    <button type="button" onclick="document.getElementById('cora-report-preview-overlay').classList.add('hidden')" class="p-1 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </button>
                                </div>
                                <div id="cora-report-preview-content" class="p-5 overflow-y-auto flex-1 text-xs"></div>
                                <div class="p-3.5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/40 flex items-center justify-end gap-2">
                                    <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 font-semibold text-xs rounded-lg hover:bg-zinc-800 transition-colors">Print Report</button>
                                    <button type="button" onclick="document.getElementById('cora-report-preview-overlay').classList.add('hidden')" class="px-3 py-1.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs rounded-lg hover:bg-zinc-50 transition-colors">Close</button>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(overlay);
                    }
                    var content = document.getElementById('cora-report-preview-content');
                    if (content) content.innerHTML = res.data.report_html || '';
                    overlay.classList.remove('hidden');
                } else {
                    if (window.coraShowToast) window.coraShowToast(res.data?.message || 'Report generation failed.', 'error');
                }
            })
            .catch(function() {
                if (window.coraShowToast) window.coraShowToast('Network error while generating report.', 'error');
            });
    };

    window.saveFinancialSchedule = function() {
        var ajaxUrl = (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php';
        var nonce   = (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '';

        var daily    = (document.getElementById('sched-daily') || document.getElementById('fin-sched-daily'))?.checked ? '1' : '0';
        var weekly   = (document.getElementById('sched-weekly') || document.getElementById('fin-sched-weekly'))?.checked ? '1' : '0';
        var monthly  = (document.getElementById('sched-monthly') || document.getElementById('fin-sched-monthly'))?.checked ? '1' : '0';
        var quarterly= (document.getElementById('sched-quarterly') || document.getElementById('fin-sched-quarterly'))?.checked ? '1' : '0';
        var email    = (document.getElementById('sched-recipient-email') || document.getElementById('fin-sched-email'))?.value || '';

        if (window.coraShowToast) window.coraShowToast('Saving financial report schedule...', 'info');

        var fd = new FormData();
        fd.append('action', 'cora_save_financial_schedule');
        fd.append('security', nonce);
        fd.append('nonce', nonce);
        fd.append('daily_digest', daily);
        fd.append('weekly_summary', weekly);
        fd.append('monthly_pnl', monthly);
        fd.append('quarterly_tax', quarterly);
        fd.append('recipient_email', email);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast(res.data.message || 'Report schedule settings saved successfully.', 'success');
                    closeFinancialReportsDrawer();
                } else {
                    if (window.coraShowToast) window.coraShowToast(res.data?.message || 'Failed to save schedule.', 'error');
                }
            })
            .catch(function() {
                if (window.coraShowToast) window.coraShowToast('Network error while saving schedule.', 'error');
            });
    };

    window.filterLedgerByPill = function(category) {
        document.querySelectorAll('.fin-pill-filter').forEach(function(btn) {
            btn.classList.remove('bg-zinc-900', 'text-white', 'dark:bg-zinc-100', 'dark:text-zinc-900');
            btn.classList.add('bg-zinc-100', 'dark:bg-zinc-800', 'text-zinc-600', 'dark:text-zinc-400');
        });
        event.target.classList.remove('bg-zinc-100', 'dark:bg-zinc-800', 'text-zinc-600', 'dark:text-zinc-400');
        event.target.classList.add('bg-zinc-900', 'text-white', 'dark:bg-zinc-100', 'dark:text-zinc-900');

        var select = document.getElementById('fin-ledger-category-filter');
        if (select) {
            select.value = category === 'all' ? 'all' : category;
        }
        window.filterLedgerTable();
    };

    window.openAddLedgerDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            var drawers = document.querySelectorAll('aside[id$="-drawer"]');
            drawers.forEach(function(d) { d.classList.add('collapsed'); });
        }
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');
        
        var drawer = document.getElementById('cora-add-ledger-drawer');
        if (drawer) drawer.classList.remove('collapsed');

        // Set default date to today
        var dateInput = document.getElementById('ledger-entry-date');
        if (dateInput && !dateInput.value) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }
    };

    window.closeAddLedgerDrawer = function() {
        var drawer = document.getElementById('cora-add-ledger-drawer');
        if (drawer) drawer.classList.add('collapsed');
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
    };

    window.openCreateInvoiceDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            var drawers = document.querySelectorAll('aside[id$="-drawer"]');
            drawers.forEach(function(d) { d.classList.add('collapsed'); });
        }
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');

        var drawer = document.getElementById('cora-create-invoice-drawer');
        if (drawer) drawer.classList.remove('collapsed');

        var dueInput = document.getElementById('invoice-due-date');
        if (dueInput && !dueInput.value) {
            var nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            dueInput.value = nextWeek.toISOString().split('T')[0];
        }
    };

    window.closeCreateInvoiceDrawer = function() {
        var drawer = document.getElementById('cora-create-invoice-drawer');
        if (drawer) drawer.classList.add('collapsed');
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
    };

    window.openProcessPayoutDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            var drawers = document.querySelectorAll('aside[id$="-drawer"]');
            drawers.forEach(function(d) { d.classList.add('collapsed'); });
        }
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) backdrop.classList.remove('hidden');

        var drawer = document.getElementById('cora-process-payout-drawer');
        if (drawer) drawer.classList.remove('collapsed');
        calculatePayoutPreview();
    };

    window.closeProcessPayoutDrawer = function() {
        var drawer = document.getElementById('cora-process-payout-drawer');
        if (drawer) drawer.classList.add('collapsed');
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
    };

    // 6.2 SUB-TAB SWITCHER with URL param persistence
    window.switchFinancialTab = function(tabId) {
        var contents = document.querySelectorAll('.cora-fin-tab-content');
        contents.forEach(function(el) { el.classList.add('hidden'); });

        var targetContent = document.getElementById(tabId);
        if (targetContent) targetContent.classList.remove('hidden');

        var tabs = document.querySelectorAll('.cora-financial-tab');
        tabs.forEach(function(tab) {
            tab.className = 'cora-financial-tab px-3.5 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors flex items-center gap-1.5 cursor-pointer';
        });

        var activeBtn = document.getElementById('btn-' + tabId);
        if (activeBtn) {
            activeBtn.className = 'cora-financial-tab px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 transition-colors flex items-center gap-1.5 cursor-pointer';
        }

        // Persist active tab in URL
        try {
            var url = new URL(window.location.href);
            url.searchParams.set('fin_tab', tabId);
            window.history.replaceState(null, '', url.toString());
        } catch(e) {}
    };

    // Restore tab from URL on page load
    (function() {
        try {
            var params = new URLSearchParams(window.location.search);
            var activeFinTab = params.get('fin_tab');
            if (activeFinTab && document.getElementById(activeFinTab)) {
                window.switchFinancialTab(activeFinTab);
            }
        } catch(e) {}
    })();

    // 6.3 TOOLBAR & FILTER HANDLERS
    window.handlePeriodChange = function(val) {
        var customContainer = document.getElementById('financial-custom-date-container');
        if (customContainer) {
            if (val === 'custom') {
                customContainer.classList.remove('hidden');
            } else {
                customContainer.classList.add('hidden');
            }
        }
        updateKPIsForFilters();
    };

    window.handleCustomDateChange = function() {
        updateKPIsForFilters();
    };

    window.handleIndustryChange = function(val) {
        updateKPIsForFilters();
    };

    function updateKPIsForFilters() {
        var period = document.getElementById('financial-filter-period')?.value || 'this_month';
        var scope = document.getElementById('financial-filter-industry')?.value || 'consolidated';

        var revEl = document.getElementById('kpi-val-revenue');
        var expEl = document.getElementById('kpi-val-expenses');
        var profitEl = document.getElementById('kpi-val-net-profit');
        var marginEl = document.getElementById('kpi-val-margin-pill');
        var pendingEl = document.getElementById('kpi-val-pending');

        if (scope === 'real_estate') {
            if (revEl) revEl.innerText = '₹320,000';
            if (expEl) expEl.innerText = '₹95,000';
            if (profitEl) profitEl.innerText = '₹225,000';
            if (marginEl) marginEl.innerText = '70.3% Margin';
            if (pendingEl) pendingEl.innerText = '₹60,000';
        } else if (scope === 'photography') {
            if (revEl) revEl.innerText = '₹165,000';
            if (expEl) expEl.innerText = '₹67,400';
            if (profitEl) profitEl.innerText = '₹97,600';
            if (marginEl) marginEl.innerText = '59.1% Margin';
            if (pendingEl) pendingEl.innerText = '₹35,000';
        } else {
            if (revEl) revEl.innerText = '₹485,000';
            if (expEl) expEl.innerText = '₹162,400';
            if (profitEl) profitEl.innerText = '₹322,600';
            if (marginEl) marginEl.innerText = '66.5% Margin';
            if (pendingEl) pendingEl.innerText = '₹95,000';
        }

        if (window.coraShowToast) {
            window.coraShowToast('Financial view updated for ' + scope.replace('_', ' ') + ' (' + period + ')', 'info');
        }
    }

    // 6.4 PAYOUT PREVIEW CALCULATOR
    window.calculatePayoutPreview = function() {
        var gross = parseFloat(document.getElementById('payout-gross-amount')?.value || 0);
        var splitPct = parseFloat(document.getElementById('payout-split-pct')?.value || 70) / 100;
        var taxPct = parseFloat(document.getElementById('payout-tax-pct')?.value || 10) / 100;

        var afterSplit = gross * splitPct;
        var net = afterSplit * (1 - taxPct);

        var netEl = document.getElementById('payout-net-amount');
        if (netEl) {
            netEl.value = '₹' + Math.round(net).toLocaleString('en-IN');
        }
    };

    // 6.5 FORM SUBMISSIONS – wired to AJAX endpoints for persistence
    window.submitAddLedgerForm = function(e) {
        e.preventDefault();
        var form = document.getElementById('cora-add-ledger-form');
        var submitBtn = form ? form.querySelector('[type="submit"]') : null;
        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Saving...'; }

        var type    = document.getElementById('ledger-entry-type')?.value || 'Inflow';
        var cat     = document.getElementById('ledger-entry-category')?.value || 'Retainer Fee';
        var amount  = parseFloat(document.getElementById('ledger-entry-amount')?.value || 0);
        var tag     = document.getElementById('ledger-entry-tag')?.value || '';
        var date    = document.getElementById('ledger-entry-date')?.value || new Date().toISOString().split('T')[0];
        var notes   = document.getElementById('ledger-entry-notes')?.value || '';
        var scope   = document.getElementById('financial-filter-industry')?.value || 'real_estate';

        var nonce   = (typeof coraREWPData !== 'undefined') ? coraREWPData.ajaxNonce : '';
        var ajaxUrl = (typeof coraREWPData !== 'undefined') ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php';

        var fd = new FormData();
        fd.append('action', 'cora_add_ledger_entry');
        fd.append('security', nonce);
        fd.append('type', type.toLowerCase());
        fd.append('category', cat);
        fd.append('description', notes);
        fd.append('amount', amount);
        fd.append('payment_status', 'paid');
        fd.append('property_tag', tag);
        fd.append('industry', scope === 'consolidated' ? 'all' : scope);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Log Ledger Entry'; }
                if (data.success) {
                    var entry = data.data.entry;
                    var tbody = document.getElementById('cora-financial-table-body');
                    if (tbody) {
                        var tr = document.createElement('tr');
                        tr.className = 'hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40';
                        var fmtAmt = (type === 'Inflow' ? '+₹' : '-₹') + parseFloat(entry.amount||0).toLocaleString('en-IN');
                        var typeBadge = type === 'Inflow'
                            ? '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">Inflow</span>'
                            : '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">Outflow</span>';
                        tr.innerHTML = `
                            <td class="px-4 py-3 font-medium text-zinc-700 dark:text-zinc-300">${(entry.date||'').split(' ')[0]}</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">${entry.description||''}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400"><span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-[11px]">${entry.category||''}</span></td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">${fmtAmt}</td>
                            <td class="px-4 py-3">${typeBadge}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700">Logged</span></td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">${entry.property_tag||'—'}</td>
                            <td class="px-4 py-3 text-right"><span class="text-zinc-400 text-[10px]">${entry.id||''}</span></td>
                        `;
                        tbody.insertBefore(tr, tbody.firstChild);
                    }
                    form && form.reset();
                    closeAddLedgerDrawer();
                    if (window.coraShowToast) window.coraShowToast('Ledger entry saved successfully.', 'success');
                } else {
                    if (window.coraShowToast) window.coraShowToast(data.data?.message || 'Save failed.', 'error');
                }
            })
            .catch(function() {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Log Ledger Entry'; }
                if (window.coraShowToast) window.coraShowToast('Network error. Please retry.', 'error');
            });
    };

    window.submitCreateInvoiceForm = function(e) {
        e.preventDefault();
        var form = document.getElementById('cora-create-invoice-form');
        var submitBtn = form ? form.querySelector('[type="submit"]') : null;
        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Generating...'; }

        var client       = document.getElementById('invoice-client-name')?.value || '';
        var email        = document.getElementById('invoice-client-email')?.value || '';
        var pkg          = document.getElementById('invoice-deal-package')?.value || '';
        var total        = parseFloat(document.getElementById('invoice-total-amount')?.value || 0);
        var retainerPct  = parseInt(document.getElementById('invoice-retainer-pct')?.value || 50);
        var dueDate      = document.getElementById('invoice-due-date')?.value || '';
        var lineItems    = document.getElementById('invoice-line-items')?.value || '';
        var scope        = document.getElementById('financial-filter-industry')?.value || 'real_estate';

        var nonce   = (typeof coraREWPData !== 'undefined') ? coraREWPData.ajaxNonce : '';
        var ajaxUrl = (typeof coraREWPData !== 'undefined') ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php';

        var fd = new FormData();
        fd.append('action', 'cora_create_invoice');
        fd.append('security', nonce);
        fd.append('client_name', client);
        fd.append('client_email', email);
        fd.append('package_name', pkg);
        fd.append('total_amount', total);
        fd.append('deposit_pct', retainerPct);
        fd.append('tax_pct', 0);
        fd.append('due_date', dueDate);
        fd.append('line_items', JSON.stringify([{ description: lineItems }]));
        fd.append('industry', scope === 'consolidated' ? 'real_estate' : scope);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Generate & Dispatch Invoice'; }
                if (data.success) {
                    var inv = data.data.invoice;
                    var paidRetainer = total * (retainerPct / 100);
                    var due = total;
                    var tbody = document.getElementById('cora-invoice-table-body');
                    if (tbody) {
                        var tr = document.createElement('tr');
                        tr.className = 'hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40';
                        tr.innerHTML = `
                            <td class="px-4 py-3 font-mono font-bold text-zinc-900 dark:text-zinc-100">${inv.invoice_number}</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">${inv.client_name}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">${inv.package_name}</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹${parseFloat(inv.total_amount||0).toLocaleString('en-IN')}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">₹${paidRetainer.toLocaleString('en-IN')} (${retainerPct}%)</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹${due.toLocaleString('en-IN')}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 border border-zinc-300 dark:border-zinc-700">Unpaid</span></td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <button type="button" onclick="window.coraShowToast('PDF for ${inv.invoice_number} generated', 'success')" class="text-zinc-700 hover:text-zinc-950 dark:text-zinc-300 font-semibold">PDF</button>
                                <button type="button" onclick="navigator.clipboard?.writeText('${window.location.origin}/invoice/${inv.share_token}'); window.coraShowToast('Share link copied', 'info')" class="text-zinc-500 hover:text-zinc-900 font-medium">Share</button>
                            </td>
                        `;
                        tbody.insertBefore(tr, tbody.firstChild);
                    }
                    form && form.reset();
                    closeCreateInvoiceDrawer();
                    if (window.coraShowToast) window.coraShowToast('Invoice ' + inv.invoice_number + ' created & saved.', 'success');
                } else {
                    if (window.coraShowToast) window.coraShowToast(data.data?.message || 'Invoice creation failed.', 'error');
                }
            })
            .catch(function() {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Generate & Dispatch Invoice'; }
                if (window.coraShowToast) window.coraShowToast('Network error. Please retry.', 'error');
            });
    };

    window.submitProcessPayoutForm = function(e) {
        e.preventDefault();
        var form = document.getElementById('cora-process-payout-form');
        var submitBtn = form ? form.querySelector('[type="submit"]') : null;
        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Processing...'; }

        var name    = document.getElementById('payout-recipient-name')?.value || '';
        var role    = document.getElementById('payout-role-select')?.value || 'agent';
        var gross   = parseFloat(document.getElementById('payout-gross-amount')?.value || 0);
        var split   = parseFloat(document.getElementById('payout-split-pct')?.value || 70);
        var tax     = parseFloat(document.getElementById('payout-tax-pct')?.value || 10);
        var notes   = document.getElementById('payout-notes')?.value || '';
        var scope   = document.getElementById('financial-filter-industry')?.value || 'studio';

        var nonce   = (typeof coraREWPData !== 'undefined') ? coraREWPData.ajaxNonce : '';
        var ajaxUrl = (typeof coraREWPData !== 'undefined') ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php';

        var fd = new FormData();
        fd.append('action', 'cora_process_payout');
        fd.append('security', nonce);
        fd.append('recipient_name', name);
        fd.append('recipient_role', role);
        fd.append('gross_amount', gross);
        fd.append('split_pct', split);
        fd.append('tax_pct', tax);
        fd.append('notes', notes);
        fd.append('industry', scope === 'consolidated' ? 'studio' : scope);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Process Payout'; }
                if (data.success) {
                    var pay = data.data.payout;
                    var tbody = document.getElementById('cora-payout-table-body');
                    if (tbody) {
                        var tr = document.createElement('tr');
                        tr.className = 'hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40';
                        tr.innerHTML = `
                            <td class="px-4 py-3 font-mono font-bold text-zinc-900 dark:text-zinc-100">${pay.payout_number}</td>
                            <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">${pay.recipient_name}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">${(pay.recipient_role||'').toUpperCase()} Compensation</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">₹${parseFloat(pay.gross_amount||0).toLocaleString('en-IN')}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">${pay.split_pct}% Split (-TDS ${pay.tax_pct}%)</td>
                            <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100">₹${parseFloat(pay.net_payout||0).toLocaleString('en-IN')}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">${(pay.created_at||'').split(' ')[0]}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900">Processed</span></td>
                            <td class="px-4 py-3 text-right space-x-1">
                                <button type="button" onclick="window.coraShowToast('Payout advice for ${pay.payout_number} generated', 'info')" class="text-zinc-600 hover:text-zinc-900 font-medium">Receipt</button>
                            </td>
                        `;
                        tbody.insertBefore(tr, tbody.firstChild);
                    }
                    form && form.reset();
                    closeProcessPayoutDrawer();
                    if (window.coraShowToast) window.coraShowToast('Payout ' + pay.payout_number + ' processed for ' + pay.recipient_name, 'success');
                } else {
                    if (window.coraShowToast) window.coraShowToast(data.data?.message || 'Payout failed.', 'error');
                }
            })
            .catch(function() {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Process Payout'; }
                if (window.coraShowToast) window.coraShowToast('Network error. Please retry.', 'error');
            });
    };

    // 6.6 FILTER FUNCTIONS FOR TABLES
    window.currentLedgerPill = 'all';

    window.filterLedgerByPill = function(catKey, btnEl) {
        window.currentLedgerPill = catKey || 'all';
        
        var pills = document.querySelectorAll('.fin-pill-filter');
        pills.forEach(function(pill) {
            pill.className = 'fin-pill-filter px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 border border-zinc-200 dark:border-zinc-700 transition-all cursor-pointer shrink-0';
        });

        if (btnEl) {
            btnEl.className = 'fin-pill-filter px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 border border-zinc-900 dark:border-zinc-100 transition-all cursor-pointer shrink-0 shadow-xs';
        }

        filterLedgerTable();
    };

    window.filterLedgerTable = function() {
        var cat = document.getElementById('fin-ledger-category-filter')?.value.toLowerCase() || 'all';
        var pill = window.currentLedgerPill ? window.currentLedgerPill.toLowerCase() : 'all';
        var type = document.getElementById('fin-ledger-type-filter')?.value.toLowerCase() || 'all';
        var query = document.getElementById('fin-ledger-search')?.value.toLowerCase() || '';

        var rows = document.querySelectorAll('#cora-financial-table-body tr');
        rows.forEach(function(row) {
            var text = row.innerText.toLowerCase();
            var matchesCat = (cat === 'all') || text.includes(cat);

            var matchesPill = true;
            if (pill !== 'all') {
                if (pill.includes('food')) matchesPill = text.includes('food') || text.includes('travel');
                else if (pill.includes('gear')) matchesPill = text.includes('gear') || text.includes('tech') || text.includes('equipment');
                else if (pill.includes('studio')) matchesPill = text.includes('ops') || text.includes('rent') || text.includes('studio');
                else if (pill.includes('marketing')) matchesPill = text.includes('marketing') || text.includes('advertising') || text.includes('listing');
                else if (pill.includes('agent') || pill.includes('crew') || pill.includes('payout')) matchesPill = text.includes('payout') || text.includes('split') || text.includes('commission') || text.includes('day rate');
                else if (pill.includes('inflow') || pill.includes('retainer')) matchesPill = text.includes('inflow') || text.includes('retainer') || text.includes('income');
                else matchesPill = text.includes(pill);
            }

            var matchesType = (type === 'all') || text.includes(type);
            var matchesQuery = !query || text.includes(query);

            if (matchesCat && matchesPill && matchesType && matchesQuery) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    window.filterInvoiceTable = function() {
        var status = document.getElementById('fin-invoice-status-filter')?.value.toLowerCase() || 'all';
        var query = document.getElementById('fin-invoice-search')?.value.toLowerCase() || '';

        var rows = document.querySelectorAll('#cora-invoice-table-body tr');
        rows.forEach(function(row) {
            var text = row.innerText.toLowerCase();
            var matchesStatus = (status === 'all') || text.includes(status);
            var matchesQuery = !query || text.includes(query);

            if (matchesStatus && matchesQuery) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    window.filterPayoutTable = function() {
        var role = document.getElementById('fin-payout-role-filter')?.value.toLowerCase() || 'all';
        var query = document.getElementById('fin-payout-search')?.value.toLowerCase() || '';

        var rows = document.querySelectorAll('#cora-payout-table-body tr');
        rows.forEach(function(row) {
            var text = row.innerText.toLowerCase();
            var matchesRole = (role === 'all') || text.includes(role);
            var matchesQuery = !query || text.includes(query);

            if (matchesRole && matchesQuery) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    // 6.7 EXPORT FINANCIALS CSV
    window.exportFinancialsCSV = function() {
        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Date,Description,Category,Amount,Type,Status,Tag\n";

        var rows = document.querySelectorAll('#cora-financial-table-body tr');
        rows.forEach(function(row) {
            var cols = row.querySelectorAll('td');
            if (cols.length >= 7) {
                var rowData = [
                    '"' + cols[0].innerText.trim() + '"',
                    '"' + cols[1].innerText.trim() + '"',
                    '"' + cols[2].innerText.trim() + '"',
                    '"' + cols[3].innerText.trim() + '"',
                    '"' + cols[4].innerText.trim() + '"',
                    '"' + cols[5].innerText.trim() + '"',
                    '"' + cols[6].innerText.trim() + '"'
                ];
                csvContent += rowData.join(",") + "\n";
            }
        });

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "cora_financial_ledger_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        if (window.coraShowToast) {
            window.coraShowToast('Financial ledger exported as CSV successfully.', 'success');
        }
    };
})();
</script>
