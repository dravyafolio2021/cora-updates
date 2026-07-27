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
?>

<div id="cora-financial-overview-root" class="space-y-6 text-zinc-900 dark:text-zinc-100 font-sans select-none">

    <!-- 1. HEADER SECTION & DATE SELECTOR -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight flex items-center gap-2">
                <span>Financial Overview</span>
                <span class="text-zinc-400 text-lg font-normal">✦</span>
            </h1>
            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1 font-medium">
                Real-time insights into your studio's financial performance.
            </p>
        </div>

        <!-- Date Range Selector & Actions -->
        <div class="flex items-center gap-3">
            <div class="relative flex items-center gap-2 bg-white dark:bg-zinc-900 px-3.5 py-2 rounded-xl shadow-sm cursor-pointer text-xs font-bold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-850 transition-all">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <span>Jan 01 – Jun 30, 2025</span>
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>

            <!-- Primary Action Menu Button -->
            <div class="relative">
                <button type="button" onclick="toggleFinancialActionMenu(event)" class="px-4 py-2 bg-zinc-900 hover:bg-black dark:bg-zinc-100 dark:hover:bg-white text-white dark:text-zinc-900 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer shadow-sm">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>New Action</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>

                <!-- Popover Action Menu Dropdown -->
                <div id="cora-fin-action-popover" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-zinc-900 rounded-xl shadow-2xl z-50 p-1 space-y-0.5 animate-fade-in select-none">
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

            <button type="button" onclick="openFinancialReportsDrawer()" class="p-2 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-xl transition-colors cursor-pointer shadow-sm" title="Automated Reports & Schedule">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </button>
        </div>
    </div>

    <!-- 2. TOP 4 KPI METRICS CARDS GRID (BORDERLESS CLEAN CARDS WITH SOFT SHADOWS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Gross Revenue (Inflows) -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </span>
                    <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">GROSS REVENUE (INFLOWS)</span>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">₹485,000</div>
                    <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mt-1 flex items-center gap-1">
                        <span>↑ 12.4% vs last period</span>
                    </div>
                </div>
                <!-- Sparkline SVG -->
                <div class="w-16 h-8 shrink-0">
                    <svg viewBox="0 0 60 30" class="w-full h-full text-zinc-800 dark:text-zinc-200" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M 0 25 Q 15 20, 30 15 T 60 5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Expenses (Outflows) -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                    </span>
                    <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">TOTAL EXPENSES (OUTFLOWS)</span>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">₹162,400</div>
                    <div class="text-xs font-medium text-zinc-400 mt-1">Operating, Production & Gear</div>
                </div>
                <!-- Sparkline SVG -->
                <div class="w-16 h-8 shrink-0">
                    <svg viewBox="0 0 60 30" class="w-full h-full text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M 0 22 Q 15 10, 30 20 T 60 12" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Net Profit -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">NET PROFIT</span>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">₹322,600</div>
                    <div class="mt-1">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">66.5% Margin</span>
                    </div>
                </div>
                <!-- Sparkline SVG -->
                <div class="w-16 h-8 shrink-0">
                    <svg viewBox="0 0 60 30" class="w-full h-full text-zinc-800 dark:text-zinc-200" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M 0 26 Q 20 20, 35 12 T 60 4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Pending Receivables -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </span>
                    <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">PENDING RECEIVABLES</span>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-zinc-100 tracking-tight">₹95,000</div>
                    <div class="text-xs font-medium text-zinc-400 mt-1">4 Outstanding Invoices</div>
                </div>
                <!-- Sparkline SVG -->
                <div class="w-16 h-8 shrink-0">
                    <svg viewBox="0 0 60 30" class="w-full h-full text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M 0 15 Q 15 25, 35 15 T 60 20" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. MIDDLE ANALYTICS ROW (BORDERLESS FLAT WHITE CARDS WITH SOFT SHADOWS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left 7 Columns: Revenue Analytics & Cashflow Chart -->
        <div class="lg:col-span-7 bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm flex flex-col justify-between space-y-6">
            
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Revenue Analytics & Cashflow</h3>
                <div class="flex items-center gap-3">
                    <select class="bg-zinc-50 dark:bg-zinc-800 rounded-lg px-2.5 py-1 text-xs font-semibold text-zinc-700 dark:text-zinc-300 focus:outline-none border-0">
                        <option>Monthly</option>
                        <option>Quarterly</option>
                    </select>
                    <button type="button" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                    </button>
                </div>
            </div>

            <!-- Legend Header Bar -->
            <div class="flex items-center gap-6 text-xs font-semibold text-zinc-600 dark:text-zinc-400">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-sm inline-block" style="background-color: #09090b !important;"></span>
                    <span>Cash Inflows</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-sm inline-block" style="background-color: #d4d4d8 !important;"></span>
                    <span>Cash Outflows</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-0.5 inline-block" style="background-color: #09090b !important;"></span>
                    <span>Net Profit (Cumulative)</span>
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
                    <text x="30" y="24" text-anchor="end" font-size="10" font-weight="600" fill="#a1a1aa">₹600K</text>
                    <text x="30" y="74" text-anchor="end" font-size="10" font-weight="600" fill="#a1a1aa">₹400K</text>
                    <text x="30" y="124" text-anchor="end" font-size="10" font-weight="600" fill="#a1a1aa">₹200K</text>
                    <text x="30" y="174" text-anchor="end" font-size="10" font-weight="600" fill="#a1a1aa">₹0</text>

                    <!-- Bars (Inflows + Outflows) -->
                    <!-- Jan -->
                    <rect x="65" y="120" width="12" height="50" rx="2" fill="#09090b" style="fill: #09090b !important;"/>
                    <rect x="80" y="145" width="12" height="25" rx="2" fill="#d4d4d8" style="fill: #d4d4d8 !important;"/>

                    <!-- Feb -->
                    <rect x="150" y="90" width="12" height="80" rx="2" fill="#09090b" style="fill: #09090b !important;"/>
                    <rect x="165" y="130" width="12" height="40" rx="2" fill="#d4d4d8" style="fill: #d4d4d8 !important;"/>

                    <!-- Mar -->
                    <rect x="235" y="65" width="12" height="105" rx="2" fill="#09090b" style="fill: #09090b !important;"/>
                    <rect x="250" y="120" width="12" height="50" rx="2" fill="#d4d4d8" style="fill: #d4d4d8 !important;"/>

                    <!-- Apr -->
                    <rect x="320" y="80" width="12" height="90" rx="2" fill="#09090b" style="fill: #09090b !important;"/>
                    <rect x="335" y="135" width="12" height="35" rx="2" fill="#d4d4d8" style="fill: #d4d4d8 !important;"/>

                    <!-- May -->
                    <rect x="405" y="60" width="12" height="110" rx="2" fill="#09090b" style="fill: #09090b !important;"/>
                    <rect x="420" y="125" width="12" height="45" rx="2" fill="#d4d4d8" style="fill: #d4d4d8 !important;"/>

                    <!-- Jun -->
                    <rect x="490" y="70" width="12" height="100" rx="2" fill="#09090b" style="fill: #09090b !important;"/>
                    <rect x="505" y="130" width="12" height="40" rx="2" fill="#d4d4d8" style="fill: #d4d4d8 !important;"/>

                    <!-- Net Profit Connected Line with Circular Markers -->
                    <path d="M 78 150 L 163 120 L 248 85 L 333 60 L 418 45 L 503 45" fill="none" stroke="#09090b" stroke-width="2.5"/>

                    <!-- Node Points -->
                    <circle cx="78" cy="150" r="4.5" fill="#09090b"/>
                    <circle cx="163" cy="120" r="4.5" fill="#09090b"/>
                    <circle cx="248" cy="85" r="4.5" fill="#09090b"/>
                    <circle cx="333" cy="60" r="4.5" fill="#09090b"/>
                    <circle cx="418" cy="45" r="4.5" fill="#09090b"/>
                    <circle cx="503" cy="45" r="4.5" fill="#09090b"/>

                    <!-- X Axis Month Labels -->
                    <text x="78" y="195" text-anchor="middle" font-size="11" font-weight="600" fill="#71717a">Jan</text>
                    <text x="163" y="195" text-anchor="middle" font-size="11" font-weight="600" fill="#71717a">Feb</text>
                    <text x="248" y="195" text-anchor="middle" font-size="11" font-weight="600" fill="#71717a">Mar</text>
                    <text x="333" y="195" text-anchor="middle" font-size="11" font-weight="600" fill="#71717a">Apr</text>
                    <text x="418" y="195" text-anchor="middle" font-size="11" font-weight="600" fill="#71717a">May</text>
                    <text x="503" y="195" text-anchor="middle" font-size="11" font-weight="600" fill="#71717a">Jun</text>
                </svg>
            </div>

        </div>

        <!-- Right 5 Columns: Cash Allocation & Profit Margin Donut Chart -->
        <div class="lg:col-span-5 bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm flex flex-col justify-between space-y-5">
            
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Cash Allocation & Profit Margin</h3>
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">66.5% Net Margin</span>
                </div>
                <span class="text-xs font-semibold text-zinc-400">Gross Revenue: ₹485,000</span>
            </div>

            <!-- SVG Donut Chart + Legend Row -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center py-2">
                
                <!-- Donut Chart Left (5 Cols) -->
                <div class="sm:col-span-5 relative flex items-center justify-center">
                    <svg viewBox="0 0 160 160" class="w-36 h-36 transform -rotate-90">
                        <!-- Background Circle -->
                        <circle cx="80" cy="80" r="60" stroke="#f4f4f5" stroke-width="18" fill="none"/>
                        <!-- Net Profit Arc (66.5%) -->
                        <circle cx="80" cy="80" r="60" stroke="#18181b" stroke-width="18" fill="none" stroke-dasharray="250 377" stroke-dashoffset="0"/>
                        <!-- Operating Rent Arc (15%) -->
                        <circle cx="80" cy="80" r="60" stroke="#52525b" stroke-width="18" fill="none" stroke-dasharray="56 377" stroke-dashoffset="-252"/>
                        <!-- Gear & Equipment Arc (8%) -->
                        <circle cx="80" cy="80" r="60" stroke="#a1a1aa" stroke-width="18" fill="none" stroke-dasharray="30 377" stroke-dashoffset="-310"/>
                        <!-- Food & Travel Arc (5%) -->
                        <circle cx="80" cy="80" r="60" stroke="#d4d4d8" stroke-width="18" fill="none" stroke-dasharray="19 377" stroke-dashoffset="-342"/>
                    </svg>
                    <!-- Inner Donut Text -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Net Profit</span>
                        <span class="text-xl font-extrabold text-zinc-900 dark:text-zinc-100 leading-none my-0.5">66.5%</span>
                        <span class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400">₹322,600</span>
                    </div>
                </div>

                <!-- Donut Legend List Right (7 Cols) -->
                <div class="sm:col-span-7 space-y-3 text-xs">
                    <div class="flex items-center justify-between font-semibold">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-sm bg-zinc-900 dark:bg-zinc-100"></span>
                            <span class="text-zinc-700 dark:text-zinc-300">Operating Rent</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-zinc-400">15.0%</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">₹72,750</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between font-semibold">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-sm bg-zinc-600"></span>
                            <span class="text-zinc-700 dark:text-zinc-300">Gear & Equipment</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-zinc-400">8.0%</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">₹38,800</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between font-semibold">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-sm bg-zinc-400"></span>
                            <span class="text-zinc-700 dark:text-zinc-300">Food & Travel</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-zinc-400">5.0%</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">₹24,250</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between font-semibold">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-sm bg-zinc-300"></span>
                            <span class="text-zinc-700 dark:text-zinc-300">Marketing</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-zinc-400">5.5%</span>
                            <span class="font-bold text-zinc-900 dark:text-zinc-100">₹26,675</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Segmented Progress Bar -->
            <div class="w-full h-3 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden flex">
                <div class="h-full bg-zinc-900 dark:bg-zinc-100" style="width: 66.5%"></div>
                <div class="h-full bg-zinc-600" style="width: 15%"></div>
                <div class="h-full bg-zinc-400" style="width: 8%"></div>
                <div class="h-full bg-zinc-300" style="width: 5%"></div>
                <div class="h-full bg-zinc-200" style="width: 5.5%"></div>
            </div>

        </div>

    </div>

    <!-- 4. MASTER LEDGER SECTION (BORDERLESS CLEAN CARD WITH SOFT SHADOW) -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm space-y-5">
        
        <!-- Header & Top Right Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800/60 pb-4">
            <div>
                <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Master Ledger</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-medium">Consolidated view of all financial entries.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="switchFinancialTab('fin-ledger')" id="btn-tab-fin-ledger" class="cora-financial-tab px-4 py-2 rounded-xl text-xs font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 shadow-xs cursor-pointer">
                    Master Ledger
                </button>
                <button type="button" onclick="switchFinancialTab('fin-invoices')" id="btn-tab-fin-invoices" class="cora-financial-tab px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer">
                    Invoices & Billing
                </button>
                <button type="button" onclick="switchFinancialTab('fin-payouts')" id="btn-tab-fin-payouts" class="cora-financial-tab px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer">
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
                    <button type="button" onclick="filterLedgerByPill('all', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 cursor-pointer shrink-0">All Entries</button>
                    <button type="button" onclick="filterLedgerByPill('Food & Travel', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Food & Travel</button>
                    <button type="button" onclick="filterLedgerByPill('Gear & Tech', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Gear & Tech</button>
                    <button type="button" onclick="filterLedgerByPill('Studio Ops & Rent', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Studio Ops & Rent</button>
                    <button type="button" onclick="filterLedgerByPill('Marketing & Listings', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Marketing & Listings</button>
                    <button type="button" onclick="filterLedgerByPill('Agent / Crew Payouts', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Agent / Crew Payouts</button>
                    <button type="button" onclick="filterLedgerByPill('Inflows & Retainers', this)" class="fin-pill-filter px-3.5 py-1.5 rounded-xl text-xs font-bold bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer shrink-0">Inflows & Retainers</button>
                </div>

                <!-- Search Input + Filter Icon Button -->
                <div class="flex items-center gap-2 shrink-0">
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="fin-ledger-search" oninput="filterLedgerTable()" placeholder="Search transactions..." class="w-full bg-zinc-50 dark:bg-zinc-800 border-0 rounded-xl py-2 pl-9 pr-3 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none">
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
                        
                        <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold">24 Jun 2025</td>
                            <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100">Sunset Villa Shoot – Final Payment</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]">Inflows & Retainers</span></td>
                            <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center gap-1 w-fit"><span class="text-zinc-900">↑</span> Cash Inflow</span></td>
                            <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm">₹125,000</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">Received</span></td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" onclick="window.coraShowToast('Viewing details for Sunset Villa Shoot', 'info')" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-bold text-base">···</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold">22 Jun 2025</td>
                            <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100">Camera Rental – Sony FX6</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]">Gear & Tech</span></td>
                            <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 flex items-center gap-1 w-fit"><span class="text-zinc-500">↓</span> Cash Outflow</span></td>
                            <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm">₹12,000</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">Paid</span></td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" onclick="window.coraShowToast('Viewing details for Sony FX6 rental', 'info')" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-bold text-base">···</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold">18 Jun 2025</td>
                            <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100">DLF Estate Listing Drone Photography</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]">Inflows & Retainers</span></td>
                            <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center gap-1 w-fit"><span class="text-zinc-900">↑</span> Cash Inflow</span></td>
                            <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm">₹85,000</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">Received</span></td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" onclick="window.coraShowToast('Viewing details for DLF Drone shoot', 'info')" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-bold text-base">···</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold">15 Jun 2025</td>
                            <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100">Studio Office Lease Rent</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]">Studio Ops & Rent</span></td>
                            <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 flex items-center gap-1 w-fit"><span class="text-zinc-500">↓</span> Cash Outflow</span></td>
                            <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm">₹45,000</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">Paid</span></td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" onclick="window.coraShowToast('Viewing details for Lease Rent', 'info')" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-bold text-base">···</button>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold">10 Jun 2025</td>
                            <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100">Team Catering & Travel Expenses</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]">Food & Travel</span></td>
                            <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 flex items-center gap-1 w-fit"><span class="text-zinc-500">↓</span> Cash Outflow</span></td>
                            <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm">₹8,500</td>
                            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">Paid</span></td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" onclick="window.coraShowToast('Viewing details for Catering & Travel', 'info')" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 font-bold text-base">···</button>
                            </td>
                        </tr>

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
            <div class="rounded-xl p-8 text-center text-xs text-zinc-500 font-medium bg-zinc-50 dark:bg-zinc-800/40">
                Showing all 4 active client invoices.
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
            <div class="rounded-xl p-8 text-center text-xs text-zinc-500 font-medium bg-zinc-50 dark:bg-zinc-800/40">
                All team payouts up to date for June 2025.
            </div>
        </div>

    </div>

</div>

<!-- SIDE DRAWERS FOR ACTIONS -->

<!-- 1. ADD LEDGER ENTRY DRAWER -->
<aside id="cora-add-ledger-drawer" class="collapsed fixed top-0 right-0 z-[9999] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out select-none">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Add Ledger Entry</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">Log cash inflow or operational outflow.</p>
        </div>
        <button type="button" onclick="closeAddLedgerDrawer()" class="p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
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
            <button type="button" onclick="closeAddLedgerDrawer()" class="px-4 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold">Cancel</button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-zinc-900 text-white font-bold">Save Entry</button>
        </div>
    </form>
</aside>

<!-- SCRIPT LOGIC FOR TAB SWITCHING & DRAWERS -->
<script>
(function($) {
    'use strict';

    window.toggleFinancialActionMenu = function(e) {
        if (e) e.stopPropagation();
        var pop = document.getElementById('cora-fin-action-popover');
        if (pop) pop.classList.toggle('hidden');
    };

    document.addEventListener('click', function() {
        var pop = document.getElementById('cora-fin-action-popover');
        if (pop && !pop.classList.contains('hidden')) pop.classList.add('hidden');
    });

    window.openAddLedgerDrawer = function() {
        var d = document.getElementById('cora-add-ledger-drawer');
        if (d) d.classList.remove('collapsed');
    };

    window.closeAddLedgerDrawer = function() {
        var d = document.getElementById('cora-add-ledger-drawer');
        if (d) d.classList.add('collapsed');
    };

    window.openCreateInvoiceDrawer = function() {
        if (window.coraShowToast) window.coraShowToast('Invoice builder ready.', 'info');
    };

    window.openProcessPayoutDrawer = function() {
        if (window.coraShowToast) window.coraShowToast('Payout processor ready.', 'info');
    };

    window.openFinancialReportsDrawer = function() {
        if (window.coraShowToast) window.coraShowToast('Financial reports & schedule panel ready.', 'info');
    };

    window.switchFinancialTab = function(tabId) {
        $('.cora-fin-tab-content').addClass('hidden');
        $('#' + tabId).removeClass('hidden');

        $('.cora-financial-tab').removeClass('bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 shadow-xs')
                                .addClass('text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800');
        
        var $activeBtn = $('#btn-tab-' + tabId);
        $activeBtn.addClass('bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 shadow-xs')
                  .removeClass('text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800');
    };

    window.currentLedgerPill = 'all';

    window.filterLedgerByPill = function(catKey, btnEl) {
        window.currentLedgerPill = catKey || 'all';
        $('.fin-pill-filter').removeClass('bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900')
                            .addClass('bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700');
        
        if (btnEl) {
            $(btnEl).addClass('bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900')
                    .removeClass('bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700');
        }

        window.filterLedgerTable();
    };

    window.filterLedgerTable = function() {
        var query = $('#fin-ledger-search').val().toLowerCase();
        var pill = window.currentLedgerPill ? window.currentLedgerPill.toLowerCase() : 'all';

        $('#cora-financial-table-body tr').each(function() {
            var text = $(this).text().toLowerCase();
            var matchesQuery = !query || text.indexOf(query) !== -1;
            var matchesPill = (pill === 'all') || text.indexOf(pill) !== -1 || (pill.indexOf('food') !== -1 && (text.indexOf('food') !== -1 || text.indexOf('travel') !== -1)) || (pill.indexOf('gear') !== -1 && (text.indexOf('gear') !== -1 || text.indexOf('tech') !== -1)) || (pill.indexOf('inflow') !== -1 && text.indexOf('inflow') !== -1);

            if (matchesQuery && matchesPill) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    };

    window.handleAddLedgerSubmit = function(e) {
        e.preventDefault();
        var form = e.target;
        var desc = form.entry_desc.value;
        var amount = form.entry_amount.value;
        var type = form.entry_type.value;
        var cat = form.entry_category.value;

        var tr = document.createElement('tr');
        tr.className = 'hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors';
        var isInflow = (type === 'inflow');
        var dateStr = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

        tr.innerHTML = `
            <td class="px-5 py-4 text-zinc-800 dark:text-zinc-200 font-semibold">${dateStr}</td>
            <td class="px-5 py-4 font-bold text-zinc-900 dark:text-zinc-100">${desc}</td>
            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]">${cat}</span></td>
            <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center gap-1 w-fit"><span class="text-zinc-900">${isInflow ? '↑' : '↓'}</span> Cash ${isInflow ? 'Inflow' : 'Outflow'}</span></td>
            <td class="px-5 py-4 font-extrabold text-zinc-900 dark:text-zinc-100 text-sm">₹${Number(amount).toLocaleString('en-IN')}</td>
            <td class="px-5 py-4"><span class="px-3 py-1 rounded-lg text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">${isInflow ? 'Received' : 'Paid'}</span></td>
            <td class="px-5 py-4 text-right"><button type="button" onclick="window.coraShowToast('Viewing details', 'info')" class="text-zinc-400 hover:text-zinc-900 font-bold text-base">···</button></td>
        `;

        var tbody = document.getElementById('cora-financial-table-body');
        if (tbody) tbody.insertBefore(tr, tbody.firstChild);

        form.reset();
        window.closeAddLedgerDrawer();
        if (window.coraShowToast) window.coraShowToast('Ledger entry added successfully.', 'success');
    };

})(jQuery);
</script>
