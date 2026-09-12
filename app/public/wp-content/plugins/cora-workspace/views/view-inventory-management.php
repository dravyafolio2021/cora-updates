<?php
/**
 * Cora Workspace - Stationery Manufacturing & Mobile Van Sales Inventory Engine
 * File: views/view-inventory-management.php
 * 
 * Single point of control for stationery plant stock, dynamic van consignment allocations (₹1k to ₹10L+),
 * live GPS route tracking, Multimodal AI invoice OCR scanning, and automated 24-hour daily audit reports.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$agency_id = function_exists( 'cora_get_current_user_agency_id' ) ? cora_get_current_user_agency_id() : 1;
?>

<div id="cora-inventory-module-root" class="w-full text-zinc-900 dark:text-zinc-100 font-sans pb-24">
    
    <!-- Header & Platform Title -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-5 border-b border-zinc-200/80 dark:border-zinc-800">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-semibold tracking-wide uppercase bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950">
                    Stationery Manufacturing
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-900 dark:bg-zinc-100"></span>
                    Live Van Telemetry Active
                </span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">
                Plant Inventory &amp; Mobile Van Sales Control
            </h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Central plant stock management, dynamic van consignment allocations, live GPS tracking &amp; 24h automated reconciliation.
            </p>
        </div>

        <!-- Right Side Header Controls -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- View Perspective Selector (Plant Director vs Mobile Van Sales Rep) -->
            <div class="inline-flex items-center p-1 rounded-xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/60 dark:border-zinc-700/60">
                <button type="button" id="cora-inv-btn-view-plant" onclick="CoraInventory.switchPerspective('plant')" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs">
                    Plant Command Center
                </button>
                <button type="button" id="cora-inv-btn-view-vendor" onclick="CoraInventory.switchPerspective('vendor')" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
                    Mobile Vendor Mode
                </button>
            </div>

            <!-- Quick Action Buttons -->
            <button type="button" onclick="CoraInventory.openConsignmentModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-semibold shadow-sm transition-colors cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                <span>Dispatch Van Load</span>
            </button>

            <button type="button" onclick="CoraInventory.openProductModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-zinc-50 text-zinc-800 dark:bg-zinc-900 dark:hover:bg-zinc-800/90 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 text-xs font-semibold shadow-2xs transition-colors cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                <span>Add SKU</span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. PLANT MANAGER COMMAND CENTER VIEW                                     -->
    <!-- ========================================================================= -->
    <div id="cora-inv-plant-container" class="space-y-6">

        <!-- 4 Hero Metric Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- KPI 1: Plant Inventory Worth -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs relative overflow-hidden">
                <div class="flex items-center justify-between text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    <span>Plant Physical Stock</span>
                    <span class="p-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                    </span>
                </div>
                <div class="text-2xl font-bold font-mono tracking-tight text-zinc-950 dark:text-zinc-50" id="cora-kpi-plant-val">
                    ₹0
                </div>
                <div class="flex items-center gap-1.5 mt-2 text-[11px] text-zinc-500">
                    <span class="font-mono font-semibold text-zinc-700 dark:text-zinc-300" id="cora-kpi-plant-units">0</span> units across <span class="font-semibold text-zinc-700 dark:text-zinc-300" id="cora-kpi-plant-skus">0</span> SKUs
                </div>
            </div>

            <!-- KPI 2: Live Stock on Wheels (In Transit / Van Allocations) -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs relative overflow-hidden">
                <div class="flex items-center justify-between text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    <span>Stock on Wheels (Vans)</span>
                    <span class="p-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                    </span>
                </div>
                <div class="text-2xl font-bold font-mono tracking-tight text-zinc-950 dark:text-zinc-50" id="cora-kpi-wheels-val">
                    ₹0
                </div>
                <div class="flex items-center gap-1.5 mt-2 text-[11px] text-zinc-500">
                    <span class="inline-block w-2 h-2 rounded-full bg-zinc-400 dark:bg-zinc-600"></span>
                    <span class="font-semibold text-zinc-700 dark:text-zinc-300" id="cora-kpi-active-consignments">0</span> active van routes in field
                </div>
            </div>

            <!-- KPI 3: Today's Realized Field Sales -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs relative overflow-hidden">
                <div class="flex items-center justify-between text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    <span>Today's Field Sales</span>
                    <span class="p-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </span>
                </div>
                <div class="text-2xl font-bold font-mono tracking-tight text-zinc-950 dark:text-zinc-50" id="cora-kpi-today-sales">
                    ₹0
                </div>
                <div class="flex items-center gap-1.5 mt-2 text-[11px] text-zinc-500">
                    <span>Collections: </span>
                    <span class="font-mono font-bold text-zinc-800 dark:text-zinc-200" id="cora-kpi-today-collections">₹0</span>
                </div>
            </div>

            <!-- KPI 4: 24h Reconciliation Discrepancy & Health -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs relative overflow-hidden">
                <div class="flex items-center justify-between text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    <span>Reconciliation Health</span>
                    <span class="p-1 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    </span>
                </div>
                <div class="text-2xl font-bold font-mono tracking-tight text-zinc-950 dark:text-zinc-50 flex items-center gap-2">
                    <span id="cora-kpi-recon-pct">99.8%</span>
                    <span class="text-xs px-2 py-0.5 rounded font-sans font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700">Clean</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2 text-[11px] text-zinc-500">
                    <span class="text-zinc-700 dark:text-zinc-300 font-semibold" id="cora-kpi-low-stock-alert">0 items</span> below reorder alert
                </div>
            </div>

        </div>

        <!-- 5-Segmented Sub-Tab Switcher -->
        <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 overflow-x-auto no-scrollbar pt-2">
            <button type="button" onclick="CoraInventory.switchSubtab('catalog')" id="cora-tab-btn-catalog" class="px-4 py-2.5 text-xs font-bold border-b-2 border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 transition-colors whitespace-nowrap flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>
                <span>Central Plant Catalog</span>
            </button>

            <button type="button" onclick="CoraInventory.switchSubtab('consignments')" id="cora-tab-btn-consignments" class="px-4 py-2.5 text-xs font-semibold border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors whitespace-nowrap flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                <span>Van Consignments Hub</span>
            </button>

            <button type="button" onclick="CoraInventory.switchSubtab('map')" id="cora-tab-btn-map" class="px-4 py-2.5 text-xs font-semibold border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors whitespace-nowrap flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                <span>Live Route &amp; GPS Map</span>
            </button>

            <button type="button" onclick="CoraInventory.switchSubtab('ocr')" id="cora-tab-btn-ocr" class="px-4 py-2.5 text-xs font-semibold border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors whitespace-nowrap flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                <span>AI Invoice OCR Inspector</span>
            </button>

            <button type="button" onclick="CoraInventory.switchSubtab('recon')" id="cora-tab-btn-recon" class="px-4 py-2.5 text-xs font-semibold border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors whitespace-nowrap flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                <span>24h Daily Audit &amp; Recon</span>
            </button>
        </div>

        <!-- SUBTAB 1: Central Plant Catalog -->
        <div id="cora-subtab-catalog" class="space-y-4">
            <!-- Filter & Search Bar -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 p-3 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative flex-1 max-w-md">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="cora-inv-search" oninput="CoraInventory.debouncedSearch()" placeholder="Search by SKU, Product Name, Barcode, Batch..." class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none focus:border-zinc-400 transition-colors">
                    </div>
                    
                    <select id="cora-inv-cat-filter" onchange="CoraInventory.loadCatalog()" class="px-3 py-1.5 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none cursor-pointer">
                        <option value="all">All Stationery Categories</option>
                        <option value="notebooks">Notebooks &amp; Registers</option>
                        <option value="paper_reams">Copier Paper Reams</option>
                        <option value="writing_instruments">Writing &amp; Pens</option>
                        <option value="office_supplies">Office Hardware &amp; Staplers</option>
                        <option value="art_kits">Art &amp; Sketching Kits</option>
                        <option value="adhesives">Adhesives &amp; Tapes</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label class="inline-flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-400 cursor-pointer">
                        <input type="checkbox" id="cora-inv-low-stock-check" onchange="CoraInventory.loadCatalog()" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                        <span>Low Stock Only</span>
                    </label>
                    <button type="button" onclick="CoraInventory.loadCatalog()" class="p-2 rounded-xl text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors" title="Refresh">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Product Table -->
            <div class="rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/40 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                                <th class="py-3 px-4">SKU &amp; Product Details</th>
                                <th class="py-3 px-4">Category / UOM</th>
                                <th class="py-3 px-4 font-mono">HSN / GST</th>
                                <th class="py-3 px-4 font-mono">Wholesale / MRP</th>
                                <th class="py-3 px-4 font-mono">Plant Stock</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cora-inv-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-sans">
                            <!-- Populated via AJAX -->
                            <tr>
                                <td colspan="7" class="py-8 text-center text-zinc-400">Loading catalog items...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SUBTAB 2: Van Consignments Hub -->
        <div id="cora-subtab-consignments" class="space-y-4 hidden">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Active Mobile Van Consignments</h2>
                    <p class="text-xs text-zinc-500">Live dynamic stock on wheels allocated to field sales agents and routes.</p>
                </div>
                <button type="button" onclick="CoraInventory.openConsignmentModal()" class="px-3.5 py-1.5 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 text-xs font-semibold shadow-2xs cursor-pointer">
                    + Dispatch New Consignment
                </button>
            </div>

            <div id="cora-consignments-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Populated via AJAX -->
            </div>
        </div>

        <!-- SUBTAB 3: Live Route & GPS Map -->
        <div id="cora-subtab-map" class="space-y-4 hidden">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Live Field Route &amp; Shop Check-in Map</h2>
                    <p class="text-xs text-zinc-500">Real-time GPS telemetry showing active van locations, visited retailers, and spot sale waypoints.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs bg-zinc-100 dark:bg-zinc-800 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-900 dark:bg-zinc-100"></span>
                        GPS 100% Free Leaflet / OSM Layer
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-2xs bg-zinc-50">
                <div id="cora-inventory-map-view" class="w-full h-[450px] relative z-0"></div>
            </div>

            <!-- Route Stop Timeline -->
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3">Today's Verified Shop Check-ins</h3>
                <div id="cora-map-stops-timeline" class="space-y-2">
                    <!-- Populated via AJAX -->
                </div>
            </div>
        </div>

        <!-- SUBTAB 4: AI Invoice OCR Inspector -->
        <div id="cora-subtab-ocr" class="space-y-4 hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left: Upload / Snap Photo -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-1">Upload or Snap Paper Invoice / Challan</h2>
                        <p class="text-xs text-zinc-500 mb-4">Gemini 2.5 Flash Multimodal Vision extracts line items, quantities, and totals instantly.</p>
                        
                        <div id="cora-ocr-dropzone" onclick="document.getElementById('cora-ocr-file-input').click()" class="w-full border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-2xl p-6 text-center hover:border-zinc-500 transition-colors cursor-pointer bg-zinc-50/50 dark:bg-zinc-800/40">
                            <input type="file" id="cora-ocr-file-input" accept="image/*,application/pdf" onchange="CoraInventory.handleOCRFile(event)" class="hidden">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-300">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                </div>
                                <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Click to Snap Photo or Upload Bill</span>
                                <span class="text-[11px] text-zinc-400">Supports JPG, PNG, PDF receipts (Handwritten or Printed)</span>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div id="cora-ocr-preview-wrap" class="mt-4 hidden">
                            <div class="relative rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700">
                                <img id="cora-ocr-preview-img" src="" alt="Invoice Preview" class="w-full max-h-56 object-cover">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="font-semibold text-zinc-600 block mb-1 text-xs">Van Consignment Assignment</label>
                            <select id="cora-ocr-consignment-select" class="w-full px-3 py-2 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                                <option value="">-- Select Active Consignment --</option>
                            </select>
                        </div>

                        <button type="button" id="cora-ocr-process-btn" onclick="CoraInventory.processOCR()" class="w-full mt-3 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold text-xs shadow-sm transition-colors flex items-center justify-center gap-2 cursor-pointer">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                            <span>Run AI Vision OCR Extraction</span>
                        </button>
                    </div>
                </div>

                <!-- Right: Parsed Results & SKU Deductions -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-zinc-100 dark:border-zinc-800">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">AI Parsed Line Items &amp; Price Match</h3>
                                <p class="text-xs text-zinc-400">Match handwritten items directly to van consignment stock.</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400" id="cora-ocr-confidence-badge">
                                Awaiting Upload
                            </span>
                        </div>

                        <div id="cora-ocr-results-container" class="space-y-3">
                            <div class="py-12 text-center text-zinc-400 text-xs select-none">
                                Upload a bill on the left to extract invoice line items.
                            </div>
                        </div>

                        <div id="cora-ocr-action-bar" class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between hidden">
                            <div class="text-xs">
                                <span class="text-zinc-500">Extracted Total: </span>
                                <span class="font-bold text-zinc-900 dark:text-zinc-100 font-mono text-sm" id="cora-ocr-parsed-total">₹0.00</span>
                            </div>
                            <button type="button" onclick="CoraInventory.confirmOCRSale()" class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-bold shadow-sm transition-colors cursor-pointer">
                                Confirm &amp; Deduct Van Stock
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- SUBTAB 5: 24h Daily Audit & Supply Recon -->
        <div id="cora-subtab-recon" class="space-y-4 hidden">
            <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">24-Hour Automated Supply Recon &amp; Audit Engine</h2>
                        <p class="text-xs text-zinc-500">End-of-day tally across all dispatched vans, cash collected, unsold returns &amp; loss prevention analysis.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="date" id="cora-recon-date-picker" value="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>" class="px-3 py-1.5 text-xs rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                        <button type="button" onclick="CoraInventory.generateDailyRecon()" class="px-3.5 py-1.5 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 text-xs font-semibold shadow-2xs cursor-pointer">
                            Run Audit
                        </button>
                        <button type="button" onclick="CoraInventory.exportDailyPDF()" class="px-3.5 py-1.5 rounded-xl bg-white text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 text-xs font-semibold shadow-2xs cursor-pointer flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span>Download PDF Summary</span>
                        </button>
                    </div>
                </div>

                <!-- AI Diagnostic Report Body -->
                <div class="mt-4 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-700/60 font-mono text-xs leading-relaxed text-zinc-800 dark:text-zinc-200 whitespace-pre-line" id="cora-recon-narrative-box">
                    Click "Run Audit" above to aggregate today's 24-hour supply reconciliation metrics and generate the AI Loss Prevention Narrative.
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 2. ULTRA-SIMPLIFIED MOBILE VENDOR VIEW (Non-Technical UI)                 -->
    <!-- ========================================================================= -->
    <div id="cora-inv-vendor-container" class="space-y-4 hidden">
        
        <!-- Active Consignment Status Banner (Dynamic) -->
        <div id="cora-vendor-consignment-banner-wrap">
            <div class="p-6 rounded-3xl bg-zinc-950 text-white shadow-lg relative overflow-hidden border border-zinc-800">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-900 text-zinc-400 border border-zinc-800 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span> Standby Mode
                    </span>
                    <span class="text-[11px] font-mono text-zinc-500">No Active Consignment</span>
                </div>
                <div class="text-2xl font-bold font-mono tracking-tight text-white mb-1">
                    ₹0 <span class="text-xs font-sans font-normal text-zinc-400">Stock on Wheels</span>
                </div>
                <p class="text-xs text-zinc-400 mt-2 leading-relaxed">No active consignment allocated for this shift. Factory dispatch will assign route stock to this terminal.</p>
                <div class="flex items-center justify-between pt-3 mt-4 border-t border-zinc-800/80 text-xs text-zinc-400">
                    <div>Sold Today: <span class="font-bold text-zinc-300 font-mono">₹0</span></div>
                    <div>Cash in Hand: <span class="font-bold text-zinc-300 font-mono">₹0</span></div>
                </div>
            </div>
        </div>

        <!-- 4 Large Touch Action Buttons for Non-Technical Field Agents -->
        <div class="grid grid-cols-2 gap-3">
            
            <!-- Button 1: Quick Spot Sale -->
            <button type="button" onclick="CoraInventory.openSpotSaleSheet()" class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center text-center gap-2 hover:bg-zinc-50 active:scale-98 transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Quick Spot Sale</div>
                    <div class="text-[10px] text-zinc-500">Bill Shop &amp; Collect Cash</div>
                </div>
            </button>

            <!-- Button 2: Scan Paper Bill (AI OCR) -->
            <button type="button" onclick="CoraInventory.openOCRSheet()" class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center text-center gap-2 hover:bg-zinc-50 active:scale-98 transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Snap Paper Bill</div>
                    <div class="text-[10px] text-zinc-500">AI Reads Handwritten Bill</div>
                </div>
            </button>

            <!-- Button 3: GPS Shop Check-in -->
            <button type="button" onclick="CoraInventory.openShopVisitSheet()" class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center text-center gap-2 hover:bg-zinc-50 active:scale-98 transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">GPS Shop Check-in</div>
                    <div class="text-[10px] text-zinc-500">1-Tap Location Stamp</div>
                </div>
            </button>

            <!-- Button 4: Day-End Return Settlement -->
            <button type="button" onclick="CoraInventory.openReconcileSheet()" class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm flex flex-col items-center justify-center text-center gap-2 hover:bg-zinc-50 active:scale-98 transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-100 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Day-End Return</div>
                    <div class="text-[10px] text-zinc-500">Restock Unsold Goods</div>
                </div>
            </button>

        </div>

        <!-- Today's Live Sales Ledger for Vendor -->
        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3">Today's Spot Invoices</h3>
            <div id="cora-vendor-sales-list" class="space-y-2">
                <!-- Populated via AJAX -->
            </div>
        </div>

    </div>

</div>

<!-- ========================================================================= -->
<!-- MOBILE BOTTOM-UP SLIDE SHEETS (translate-y-full -> translate-y-0)         -->
<!-- ========================================================================= -->

<!-- 1. ADD / EDIT PRODUCT BOTTOM SHEET -->
<div id="cora-inv-product-sheet" class="fixed inset-0 z-50 pointer-events-none transition-all duration-300">
    <div id="cora-inv-product-backdrop" onclick="CoraInventory.closeProductModal()" class="absolute inset-0 bg-zinc-950/50 backdrop-blur-xs opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-product-drawer" class="absolute bottom-0 inset-x-0 max-w-xl mx-auto bg-white dark:bg-zinc-900 rounded-t-3xl shadow-2xl p-6 transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto max-h-[90vh] overflow-y-auto">
        <div class="w-10 h-1 bg-zinc-300 dark:bg-zinc-700 rounded-full mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100" id="cora-inv-prod-title">Add Stationery SKU</h3>
            <button type="button" onclick="CoraInventory.closeProductModal()" class="p-1.5 text-zinc-400 hover:text-zinc-700">✕</button>
        </div>
        <form id="cora-inv-prod-form" onsubmit="CoraInventory.saveProduct(event)" class="space-y-3 text-xs">
            <input type="hidden" id="cora-prod-id" value="0">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">SKU Code *</label>
                    <input type="text" id="cora-prod-sku" required placeholder="e.g. STN-NB-103" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">Barcode</label>
                    <input type="text" id="cora-prod-barcode" placeholder="890123450..." class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
            </div>
            <div>
                <label class="font-semibold text-zinc-600 block mb-1">Product Title *</label>
                <input type="text" id="cora-prod-name" required placeholder="e.g. Spiral Bound Long Register (240 Pgs)" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">Category</label>
                    <select id="cora-prod-cat" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                        <option value="notebooks">Notebooks &amp; Registers</option>
                        <option value="paper_reams">Copier Paper Reams</option>
                        <option value="writing_instruments">Writing &amp; Pens</option>
                        <option value="office_supplies">Office Supplies</option>
                        <option value="art_kits">Art &amp; Sketching Kits</option>
                        <option value="adhesives">Adhesives &amp; Glue</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">Unit of Measure (UOM)</label>
                    <input type="text" id="cora-prod-uom" placeholder="e.g. Pack of 10 / Box" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">Wholesale ₹</label>
                    <input type="number" step="0.01" id="cora-prod-ws" placeholder="320.00" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">MRP ₹</label>
                    <input type="number" step="0.01" id="cora-prod-mrp" placeholder="450.00" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">GST %</label>
                    <select id="cora-prod-gst" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                        <option value="12">12% GST</option>
                        <option value="18">18% GST</option>
                        <option value="5">5% GST</option>
                        <option value="0">0% GST</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">Plant Stock Quantity</label>
                    <input type="number" id="cora-prod-stock" value="500" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">Low Stock Alert Threshold</label>
                    <input type="number" id="cora-prod-threshold" value="50" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
            </div>
            <div class="pt-3">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-bold shadow-sm">Save Stationery Product</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. DISPATCH CONSIGNMENT BOTTOM SHEET -->
<div id="cora-inv-consignment-sheet" class="fixed inset-0 z-50 pointer-events-none transition-all duration-300">
    <div id="cora-inv-consignment-backdrop" onclick="CoraInventory.closeConsignmentModal()" class="absolute inset-0 bg-zinc-950/50 backdrop-blur-xs opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-consignment-drawer" class="absolute bottom-0 inset-x-0 max-w-xl mx-auto bg-white dark:bg-zinc-900 rounded-t-3xl shadow-2xl p-6 transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto max-h-[90vh] overflow-y-auto">
        <div class="w-10 h-1 bg-zinc-300 dark:bg-zinc-700 rounded-full mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Dispatch Van Consignment</h3>
                <p class="text-[11px] text-zinc-500">Allocate stationery inventory (₹1k - ₹10L+) to field sales agent.</p>
            </div>
            <button type="button" onclick="CoraInventory.closeConsignmentModal()" class="p-1.5 text-zinc-400 hover:text-zinc-700">✕</button>
        </div>
        <form id="cora-inv-csn-form" onsubmit="CoraInventory.dispatchConsignment(event)" class="space-y-3 text-xs">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">Sales Rep / Driver</label>
                    <input type="text" id="cora-csn-vendor" required value="Rohan Verma (Van 02)" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">Vehicle / Van No</label>
                    <input type="text" id="cora-csn-vehicle" value="DL-1V-5501" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
            </div>
            <div>
                <label class="font-semibold text-zinc-600 block mb-1">Assigned Route / Territory</label>
                <input type="text" id="cora-csn-route" value="Central Stationery Market & University Belts" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
            </div>

            <!-- Item Quantity Allocation Table -->
            <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl p-3 bg-zinc-50/50 dark:bg-zinc-800/30 space-y-2">
                <div class="font-semibold text-zinc-700 dark:text-zinc-300 flex items-center justify-between">
                    <span>Allocate Products to Van</span>
                    <span id="cora-csn-calc-val" class="font-mono text-zinc-900 dark:text-zinc-100">Total: ₹0.00</span>
                </div>
                <div id="cora-csn-alloc-list" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                    <!-- Populated dynamically -->
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 font-bold shadow-sm">Confirm Van Dispatch &amp; Lock Stock</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. QUICK SPOT SALE BOTTOM SHEET (For Mobile Vendor) -->
<div id="cora-inv-spot-sale-sheet" class="fixed inset-0 z-50 pointer-events-none transition-all duration-300">
    <div id="cora-inv-spot-backdrop" onclick="CoraInventory.closeSpotSaleSheet()" class="absolute inset-0 bg-zinc-950/50 backdrop-blur-xs opacity-0 transition-opacity duration-300"></div>
    <div id="cora-inv-spot-drawer" class="absolute bottom-0 inset-x-0 max-w-xl mx-auto bg-white dark:bg-zinc-900 rounded-t-3xl shadow-2xl p-6 transform translate-y-full transition-transform duration-300 ease-out pointer-events-auto max-h-[90vh] overflow-y-auto">
        <div class="w-10 h-1 bg-zinc-300 dark:bg-zinc-700 rounded-full mx-auto mb-4"></div>
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Quick Spot Sale &amp; Cash Collection</h3>
                <p class="text-[11px] text-zinc-500">Sell directly from active van consignment.</p>
            </div>
            <button type="button" onclick="CoraInventory.closeSpotSaleSheet()" class="p-1.5 text-zinc-400 hover:text-zinc-700">✕</button>
        </div>
        <form id="cora-inv-spot-form" onsubmit="CoraInventory.submitSpotSale(event)" class="space-y-3 text-xs">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">Retailer / Shop Name *</label>
                    <input type="text" id="cora-spot-customer" required placeholder="e.g. Standard Book Store" class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
                <div>
                    <label class="font-semibold text-zinc-600 block mb-1">Retailer Phone</label>
                    <input type="text" id="cora-spot-phone" placeholder="+91 98..." class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 outline-none">
                </div>
            </div>

            <!-- Items Picker from Van Stock -->
            <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl p-3 bg-zinc-50/50 dark:bg-zinc-800/30 space-y-2">
                <div class="font-semibold text-zinc-700 dark:text-zinc-300 flex items-center justify-between">
                    <span>Select Items Sold</span>
                    <span id="cora-spot-total-display" class="font-mono text-zinc-950 dark:text-zinc-50 font-bold">Total: ₹0.00</span>
                </div>
                <div id="cora-spot-items-list" class="space-y-2 max-h-44 overflow-y-auto">
                    <!-- Populated dynamically -->
                </div>
            </div>

            <!-- Payment Mode Selection -->
            <div>
                <label class="font-semibold text-zinc-600 block mb-1">Payment Method</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="p-2 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-center gap-1.5 cursor-pointer bg-zinc-50 dark:bg-zinc-800">
                        <input type="radio" name="cora_spot_pay_mode" value="cash" checked class="text-zinc-900">
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">Cash</span>
                    </label>
                    <label class="p-2 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-center gap-1.5 cursor-pointer bg-zinc-50 dark:bg-zinc-800">
                        <input type="radio" name="cora_spot_pay_mode" value="upi" class="text-zinc-900">
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">UPI / QR</span>
                    </label>
                    <label class="p-2 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-center gap-1.5 cursor-pointer bg-zinc-50 dark:bg-zinc-800">
                        <input type="radio" name="cora_spot_pay_mode" value="credit" class="text-zinc-900">
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">Credit Bill</span>
                    </label>
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 font-bold shadow-sm cursor-pointer transition-colors">Confirm Sale &amp; Print Receipt</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT CONTROLLER FOR INVENTORY MODULE                                -->
<!-- ========================================================================= -->
<script>
window.CoraInventory = (function($) {
    'use strict';

    let currentPerspective = 'plant';
    let currentSubtab = 'catalog';
    let catalogCache = [];
    let mapInstance = null;
    let mapMarkers = [];
    let activeConsignmentId = 0;
    let ocrParsedResult = null;

    function init() {
        loadCatalog();
        loadConsignments();
        loadVendorDashboard();
    }

    function switchPerspective(mode) {
        currentPerspective = mode;
        if (mode === 'plant') {
            $('#cora-inv-plant-container').removeClass('hidden');
            $('#cora-inv-vendor-container').addClass('hidden');
            $('#cora-inv-btn-view-plant').addClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs').removeClass('text-zinc-600 dark:text-zinc-400');
            $('#cora-inv-btn-view-vendor').removeClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs').addClass('text-zinc-600 dark:text-zinc-400');
        } else {
            $('#cora-inv-plant-container').addClass('hidden');
            $('#cora-inv-vendor-container').removeClass('hidden');
            $('#cora-inv-btn-view-vendor').addClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs').removeClass('text-zinc-600 dark:text-zinc-400');
            $('#cora-inv-btn-view-plant').removeClass('bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs').addClass('text-zinc-600 dark:text-zinc-400');
            loadVendorDashboard();
        }
    }

    function switchSubtab(tab) {
        currentSubtab = tab;
        $('[id^="cora-subtab-"]').addClass('hidden');
        $('#cora-subtab-' + tab).removeClass('hidden');

        $('[id^="cora-tab-btn-"]').removeClass('border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 font-bold')
            .addClass('border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 font-semibold');
        
        $('#cora-tab-btn-' + tab).addClass('border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-50 font-bold')
            .removeClass('border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 font-semibold');

        if (tab === 'map') {
            setTimeout(initLeafletMap, 150);
        }
    }

    function loadCatalog() {
        const search = $('#cora-inv-search').val();
        const category = $('#cora-inv-cat-filter').val();
        const lowStock = $('#cora-inv-low-stock-check').is(':checked') ? 1 : 0;

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_get_catalog',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                search: search,
                category: category,
                low_stock: lowStock
            },
            success: function(res) {
                if (res.success) {
                    catalogCache = res.data.products || [];
                    renderCatalogTable(catalogCache);
                    renderKPIs(res.data.kpis || {});
                }
            }
        });
    }

    function renderCatalogTable(products) {
        const tbody = $('#cora-inv-table-body');
        if (!products.length) {
            tbody.html(`
                <tr>
                    <td colspan="7" class="py-12 text-center select-none">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-2.5 text-zinc-400">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </div>
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">No products found</span>
                            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">There are no stationery products matching to the query.</p>
                            <button type="button" onclick="$('#cora-inv-search').val(''); CoraInventory.debouncedSearch();" class="mt-3 px-3 py-1 text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg transition-colors cursor-pointer border border-zinc-200/80">
                                Clear search
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        let html = '';
        products.forEach(p => {
            const isLow = parseInt(p.stock_quantity) <= parseInt(p.low_stock_threshold);
            const lowBadge = isLow 
                ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-200 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700">Low Stock (${p.stock_quantity})</span>`
                : `<span class="font-mono font-bold text-zinc-800 dark:text-zinc-200">${p.stock_quantity}</span>`;

            html += `
                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">${escapeHtml(p.name)}</div>
                        <div class="flex items-center gap-2 mt-0.5 text-[11px] font-mono text-zinc-400">
                            <span>SKU: ${escapeHtml(p.sku)}</span>
                            ${p.barcode ? `<span>• Barcode: ${escapeHtml(p.barcode)}</span>` : ''}
                            ${p.batch_no ? `<span>• Batch: ${escapeHtml(p.batch_no)}</span>` : ''}
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="capitalize text-zinc-700 dark:text-zinc-300">${escapeHtml(p.category.replace('_', ' '))}</div>
                        <div class="text-[11px] text-zinc-400">${escapeHtml(p.uom || 'Pcs')}</div>
                    </td>
                    <td class="py-3 px-4 font-mono text-zinc-600 dark:text-zinc-400">
                        <div>HSN: ${escapeHtml(p.hsn_code || '4820')}</div>
                        <div class="text-[11px]">${p.gst_rate}% GST</div>
                    </td>
                    <td class="py-3 px-4 font-mono text-zinc-900 dark:text-zinc-100">
                        <div>₹${parseFloat(p.wholesale_rate || p.wholesale_price || 0).toFixed(2)}</div>
                        <div class="text-[11px] text-zinc-400 line-through">MRP ₹${parseFloat(p.mrp || 0).toFixed(2)}</div>
                    </td>
                    <td class="py-3 px-4">
                        ${lowBadge}
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                            Active
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <button type="button" onclick="CoraInventory.quickAdjustStock(${p.id}, '${escapeHtml(p.name)}')" class="px-2 py-1 text-[11px] rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-zinc-200 font-semibold cursor-pointer">
                            Adjust
                        </button>
                    </td>
                </tr>
            `;
        });
        tbody.html(html);
    }

    function renderKPIs(kpis) {
        $('#cora-kpi-plant-val').text('₹' + Number(kpis.total_inventory_val || 0).toLocaleString('en-IN'));
        $('#cora-kpi-plant-units').text(Number(kpis.total_stock_units || 0).toLocaleString('en-IN'));
        $('#cora-kpi-plant-skus').text(kpis.total_skus || 0);
        $('#cora-kpi-wheels-val').text('₹' + Number(kpis.total_val_on_wheels || 0).toLocaleString('en-IN'));
        $('#cora-kpi-active-consignments').text(kpis.active_consignments_count || 0);
        $('#cora-kpi-today-sales').text('₹' + Number(kpis.today_sales_val || 0).toLocaleString('en-IN'));
        $('#cora-kpi-today-collections').text('₹' + Number(kpis.today_collections || 0).toLocaleString('en-IN'));
        $('#cora-kpi-low-stock-alert').text((kpis.low_stock_count || 0) + ' items');
    }

    function loadConsignments() {
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_get_consignments',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
            },
            success: function(res) {
                if (res.success && res.data.consignments) {
                    renderConsignments(res.data.consignments);
                }
            }
        });
    }

    function renderConsignments(consignments) {
        const ocrSelect = $('#cora-ocr-consignment-select');
        if (ocrSelect.length) {
            let selectHtml = '<option value="">-- Select Active Consignment --</option>';
            if (consignments && consignments.length) {
                consignments.forEach(c => {
                    selectHtml += `<option value="${c.id}">${escapeHtml(c.consignment_no)} - ${escapeHtml(c.vendor_name)} (${escapeHtml(c.vehicle_no)})</option>`;
                });
            }
            ocrSelect.html(selectHtml);
        }

        const grid = $('#cora-consignments-grid');
        if (!consignments || !consignments.length) {
            grid.html(`
                <div class="col-span-full py-12 text-center select-none">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-2.5 text-zinc-400">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">No van consignments found</span>
                        <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">There are no active or past van allocations matching to the query.</p>
                    </div>
                </div>
            `);
            return;
        }
        let html = '';
        consignments.forEach(c => {
            const isReconciled = c.status === 'reconciled';
            const statusBadge = isReconciled
                ? '<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">Reconciled / Closed</span>'
                : '<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950">Active Selling</span>';

            html += `
                <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-mono font-bold text-xs text-zinc-900 dark:text-zinc-100">${escapeHtml(c.consignment_no)}</span>
                            <div class="text-[11px] text-zinc-500">${escapeHtml(c.vendor_name)} • ${escapeHtml(c.vehicle_no)}</div>
                        </div>
                        ${statusBadge}
                    </div>

                    <div class="text-xs text-zinc-600 dark:text-zinc-300">
                        <span class="font-semibold">Route: </span>${escapeHtml(c.route_name)}
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-[11px] text-zinc-500 font-mono">
                            <span>Sold: ₹${Number(c.total_sold_val).toLocaleString('en-IN')}</span>
                            <span>Dispatched: ₹${Number(c.total_dispatched_val).toLocaleString('en-IN')}</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                            <div class="h-full bg-zinc-950 dark:bg-zinc-100 rounded-full" style="width: ${c.sales_progress_pct}%"></div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs">
                        <div class="text-[11px] text-zinc-500">
                            <span>Visits: <strong class="text-zinc-800 dark:text-zinc-200">${c.visits_count || 0}</strong></span>
                        </div>
                        ${!isReconciled ? `
                            <button type="button" onclick="CoraInventory.settleConsignment(${c.id})" class="px-3 py-1 rounded-lg bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-bold text-[11px] cursor-pointer">
                                Settle Day Return
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        grid.html(html);
    }

    function initLeafletMap() {
        const mapEl = document.getElementById('cora-inventory-map-view');
        if (!mapEl || typeof L === 'undefined') {
            return;
        }

        if (!mapInstance) {
            // Initialize at New Delhi Commercial Core
            mapInstance = L.map('cora-inventory-map-view', {
                zoomControl: true,
                scrollWheelZoom: true
            }).setView([28.6315, 77.2167], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapInstance);
        } else {
            mapInstance.invalidateSize();
        }

        loadShopVisitsMap();
    }

    function loadShopVisitsMap() {
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_get_shop_visits',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
            },
            success: function(res) {
                if (!res.success) return;
                const visits = res.data.visits || [];

                // Clear previous markers
                if (mapMarkers && mapMarkers.length) {
                    mapMarkers.forEach(m => {
                        if (mapInstance) mapInstance.removeLayer(m);
                    });
                }
                mapMarkers = [];

                const timeline = $('#cora-map-stops-timeline');

                if (!visits.length) {
                    timeline.html(`
                        <div class="p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-dashed border-zinc-200 dark:border-zinc-700 text-center">
                            <div class="text-xs font-bold text-zinc-700 dark:text-zinc-300">No Retail Check-Ins Recorded Today</div>
                            <div class="text-[11px] text-zinc-400 mt-1">Field shop visits and spot sales will plot on the live route tracking map automatically.</div>
                        </div>
                    `);
                    return;
                }

                let timelineHtml = '';
                const bounds = [];

                visits.forEach(v => {
                    const lat = parseFloat(v.lat);
                    const lng = parseFloat(v.lng);

                    if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0 && mapInstance) {
                        bounds.push([lat, lng]);
                        const shopIcon = L.divIcon({
                            html: `<div style="background:#18181b;color:#ffffff;padding:4px 8px;border-radius:12px;font-size:10px;font-weight:bold;box-shadow:0 2px 6px rgba(0,0,0,0.3);border:1px solid #3f3f46;white-space:nowrap;">📍 ${escapeHtml(v.shop_name.substring(0, 18))}</div>`,
                            className: 'cora-custom-shop-pin',
                            iconSize: [80, 24]
                        });

                        const marker = L.marker([lat, lng], { icon: shopIcon }).addTo(mapInstance)
                            .bindPopup(`<b>${escapeHtml(v.shop_name)}</b><br>${escapeHtml(v.address || '')}<br>Order: ₹${Number(v.order_value || 0).toLocaleString('en-IN')} (${escapeHtml(v.payment_mode || 'cash').toUpperCase()})`);
                        mapMarkers.push(marker);
                    }

                    timelineHtml += `
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-zinc-700 dark:bg-zinc-300"></span>
                                <span class="font-bold text-zinc-800 dark:text-zinc-200">${escapeHtml(v.shop_name)}</span>
                                <span class="text-zinc-400">• ${escapeHtml(v.address ? v.address.substring(0, 20) : 'Spot Check-in')}</span>
                            </div>
                            <span class="font-mono font-bold text-zinc-900 dark:text-zinc-100">₹${Number(v.collected_amount || v.order_value || 0).toLocaleString('en-IN')} (${escapeHtml(v.payment_mode || 'cash').toUpperCase()})</span>
                        </div>
                    `;
                });

                timeline.html(timelineHtml);

                if (bounds.length > 0 && mapInstance) {
                    mapInstance.fitBounds(bounds, { padding: [30, 30], maxZoom: 15 });
                }
            }
        });
    }

    function handleOCRFile(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(evt) {
            $('#cora-ocr-preview-img').attr('src', evt.target.result);
            $('#cora-ocr-preview-wrap').removeClass('hidden');
            window.coraActiveOCRBase64 = evt.target.result;
        };
        reader.readAsDataURL(file);
    }

    function processOCR() {
        window.coraShowToast('Scanning invoice with Gemini Multimodal Vision...', 'info');
        $('#cora-ocr-process-btn').prop('disabled', true).text('Processing AI Vision...');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_ocr_invoice',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                image_base64: window.coraActiveOCRBase64 || '',
                consignment_id: $('#cora-ocr-consignment-select').val() || activeConsignmentId || 0
            },
            success: function(res) {
                $('#cora-ocr-process-btn').prop('disabled', false).html('<span>Run AI Vision OCR Extraction</span>');
                if (res.success && res.data.parsed_data) {
                    ocrParsedResult = res.data.parsed_data;
                    renderOCRResults(ocrParsedResult);
                    window.coraShowToast('AI extracted invoice items successfully!', 'success');
                } else {
                    window.coraShowToast(res.data || 'Failed to parse invoice.', 'error');
                }
            },
            error: function() {
                $('#cora-ocr-process-btn').prop('disabled', false).html('<span>Run AI Vision OCR Extraction</span>');
                window.coraShowToast('OCR connection failed.', 'error');
            }
        });
    }

    function renderOCRResults(data) {
        $('#cora-ocr-confidence-badge').text('Confidence: ' + (data.confidence || '98.5%'))
            .removeClass('bg-zinc-100 text-zinc-600').addClass('bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950');
        
        let itemsHtml = `
            <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/60 dark:border-zinc-700/60">
                <div class="flex justify-between font-bold text-xs text-zinc-800 dark:text-zinc-200 mb-1">
                    <span>${escapeHtml(data.retailer_name || 'Retailer')}</span>
                    <span class="font-mono text-zinc-500">${escapeHtml(data.phone || '')}</span>
                </div>
            </div>
            <div class="space-y-2">
        `;

        (data.items || []).forEach(it => {
            itemsHtml += `
                <div class="p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-between text-xs">
                    <div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">${escapeHtml(it.name)}</div>
                        <div class="text-[11px] text-zinc-400 font-mono">Qty: ${it.quantity} × ₹${parseFloat(it.unit_price).toFixed(2)}</div>
                    </div>
                    <div class="font-mono font-bold text-zinc-900 dark:text-zinc-100">
                        ₹${parseFloat(it.line_total).toFixed(2)}
                    </div>
                </div>
            `;
        });

        itemsHtml += '</div>';
        $('#cora-ocr-results-container').html(itemsHtml);
        $('#cora-ocr-parsed-total').text('₹' + parseFloat(data.grand_total || 0).toFixed(2));
        $('#cora-ocr-action-bar').removeClass('hidden');
    }

    function confirmOCRSale() {
        if (!ocrParsedResult) return;
        window.coraShowToast('Recording spot bill and deducting stock...', 'info');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_record_spot_sale',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                consignment_id: $('#cora-ocr-consignment-select').val() || activeConsignmentId || 0,
                customer_name: ocrParsedResult.retailer_name,
                phone: ocrParsedResult.phone,
                items: JSON.stringify(ocrParsedResult.items),
                payment_mode: ocrParsedResult.payment_mode || 'cash',
                ocr_raw_data: JSON.stringify(ocrParsedResult)
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Invoice verified & stock deducted in real-time!', 'success');
                    loadCatalog();
                    loadConsignments();
                    loadVendorDashboard();
                    $('#cora-ocr-action-bar').addClass('hidden');
                    $('#cora-ocr-results-container').html('<div class="py-12 text-center text-zinc-900 dark:text-zinc-100 font-semibold text-xs">✓ Spot sale confirmed and consignment stock synchronized.</div>');
                }
            }
        });
    }

    function generateDailyRecon() {
        const date = $('#cora-recon-date-picker').val();
        window.coraShowToast('Generating 24-hour supply reconciliation audit...', 'info');

        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_generate_daily_recon',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                date: date
            },
            success: function(res) {
                if (res.success && res.data.narrative) {
                    $('#cora-recon-narrative-box').text(res.data.narrative);
                    window.coraShowToast('Daily audit reconciliation completed!', 'success');
                } else {
                    window.coraShowToast('Failed to generate daily reconciliation.', 'error');
                }
            }
        });
    }

    function exportDailyPDF() {
        window.coraShowToast('Generating and downloading 24h Daily Audit PDF Summary...', 'info');
        const date = $('#cora-recon-date-picker').val() || '<?php echo date("Y-m-d"); ?>';
        window.open((ajaxurl || '/wp-admin/admin-ajax.php') + '?action=cora_inventory_export_pdf&date=' + date, '_blank');
    }

    function quickAdjustStock(prodId, prodName) {
        openProductModal(prodId);
    }

    function settleConsignment(csnId) {
        window.coraShowToast('Reconciling consignment and restocking unsold goods...', 'info');
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_reconcile_consignment',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                consignment_id: csnId
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Consignment settled and audit report generated!', 'success');
                    loadCatalog();
                    loadConsignments();
                    loadVendorDashboard();
                } else {
                    window.coraShowToast(res.data || 'Failed to settle consignment.', 'error');
                }
            }
        });
    }

    function openProductModal() {
        $('#cora-inv-product-sheet').removeClass('pointer-events-none');
        $('#cora-inv-product-backdrop').removeClass('opacity-0').addClass('opacity-100');
        $('#cora-inv-product-drawer').removeClass('translate-y-full').addClass('translate-y-0');
    }

    function closeProductModal() {
        $('#cora-inv-product-drawer').removeClass('translate-y-0').addClass('translate-y-full');
        $('#cora-inv-product-backdrop').removeClass('opacity-100').addClass('opacity-0');
        setTimeout(() => $('#cora-inv-product-sheet').addClass('pointer-events-none'), 300);
    }

    function openConsignmentModal() {
        renderConsignmentAllocList();
        $('#cora-inv-consignment-sheet').removeClass('pointer-events-none');
        $('#cora-inv-consignment-backdrop').removeClass('opacity-0').addClass('opacity-100');
        $('#cora-inv-consignment-drawer').removeClass('translate-y-full').addClass('translate-y-0');
    }

    function closeConsignmentModal() {
        $('#cora-inv-consignment-drawer').removeClass('translate-y-0').addClass('translate-y-full');
        $('#cora-inv-consignment-backdrop').removeClass('opacity-100').addClass('opacity-0');
        setTimeout(() => $('#cora-inv-consignment-sheet').addClass('pointer-events-none'), 300);
    }

    function renderConsignmentAllocList() {
        let html = '';
        catalogCache.forEach(p => {
            html += `
                <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs">
                    <div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">${escapeHtml(p.name)}</div>
                        <div class="text-[11px] text-zinc-400">Available: ${p.stock_quantity} • ₹${parseFloat(p.wholesale_price).toFixed(2)}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" data-pid="${p.id}" data-rate="${p.wholesale_price}" min="0" max="${p.stock_quantity}" value="0" oninput="CoraInventory.recalcConsignmentVal()" class="cora-csn-qty-input w-16 px-2 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-right font-mono text-xs">
                    </div>
                </div>
            `;
        });
        $('#cora-csn-alloc-list').html(html || '<div class="text-zinc-400 py-3 text-center">No products in catalog.</div>');
    }

    function recalcConsignmentVal() {
        let total = 0;
        $('.cora-csn-qty-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            const rate = parseFloat($(this).data('rate')) || 0;
            total += (qty * rate);
        });
        $('#cora-csn-calc-val').text('Total: ₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
    }

    function dispatchConsignment(e) {
        e.preventDefault();
        const items = [];
        $('.cora-csn-qty-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            const pid = parseInt($(this).data('pid')) || 0;
            if (qty > 0 && pid > 0) {
                items.push({ product_id: pid, quantity: qty });
            }
        });

        if (!items.length) {
            window.coraShowToast('Please allocate at least one product.', 'error');
            return;
        }

        window.coraShowToast('Dispatching van consignment...', 'info');
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_create_consignment',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                vendor_name: $('#cora-csn-vendor').val(),
                vehicle_no: $('#cora-csn-vehicle').val(),
                route_name: $('#cora-csn-route').val(),
                items: JSON.stringify(items)
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Consignment dispatched!', 'success');
                    closeConsignmentModal();
                    loadCatalog();
                    loadConsignments();
                } else {
                    window.coraShowToast(res.data || 'Failed to dispatch.', 'error');
                }
            }
        });
    }

    function openSpotSaleSheet() {
        renderSpotSaleItems();
        $('#cora-inv-spot-sale-sheet').removeClass('pointer-events-none');
        $('#cora-inv-spot-backdrop').removeClass('opacity-0').addClass('opacity-100');
        $('#cora-inv-spot-drawer').removeClass('translate-y-full').addClass('translate-y-0');
    }

    function closeSpotSaleSheet() {
        $('#cora-inv-spot-drawer').removeClass('translate-y-0').addClass('translate-y-full');
        $('#cora-inv-spot-backdrop').removeClass('opacity-100').addClass('opacity-0');
        setTimeout(() => $('#cora-inv-spot-sale-sheet').addClass('pointer-events-none'), 300);
    }

    function renderSpotSaleItems() {
        let html = '';
        catalogCache.forEach(p => {
            html += `
                <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs">
                    <div>
                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">${escapeHtml(p.name)}</div>
                        <div class="text-[11px] text-zinc-400">Rate: ₹${parseFloat(p.wholesale_price).toFixed(2)}</div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <input type="number" data-pid="${p.id}" data-name="${escapeHtml(p.name)}" data-sku="${p.sku}" data-rate="${p.wholesale_price}" data-gst="${p.gst_rate}" min="0" value="0" oninput="CoraInventory.recalcSpotTotal()" class="cora-spot-qty-input w-16 px-2 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-right font-mono text-xs">
                    </div>
                </div>
            `;
        });
        $('#cora-spot-items-list').html(html);
    }

    function recalcSpotTotal() {
        let subtotal = 0;
        $('.cora-spot-qty-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            const rate = parseFloat($(this).data('rate')) || 0;
            subtotal += (qty * rate);
        });
        const totalWithTax = subtotal * 1.12;
        $('#cora-spot-total-display').text('Total: ₹' + totalWithTax.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
    }

    function submitSpotSale(e) {
        e.preventDefault();
        const items = [];
        $('.cora-spot-qty-input').each(function() {
            const qty = parseInt($(this).val()) || 0;
            const pid = parseInt($(this).data('pid')) || 0;
            const name = $(this).data('name') || '';
            const sku = $(this).data('sku') || '';
            const rate = parseFloat($(this).data('rate')) || 0;
            const gst = parseFloat($(this).data('gst')) || 12;
            if (qty > 0 && pid > 0) {
                items.push({
                    product_id: pid,
                    product_name: name,
                    sku: sku,
                    quantity: qty,
                    unit_price: rate,
                    gst_rate: gst
                });
            }
        });

        if (!items.length) {
            window.coraShowToast('Select at least one product sold.', 'error');
            return;
        }

        window.coraShowToast('Recording field invoice...', 'info');
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_record_spot_sale',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                consignment_id: activeConsignmentId,
                customer_name: $('#cora-spot-customer').val(),
                phone: $('#cora-spot-phone').val(),
                payment_mode: $('input[name="cora_spot_pay_mode"]:checked').val() || 'cash',
                items: JSON.stringify(items)
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Spot bill created successfully!', 'success');
                    closeSpotSaleSheet();
                    loadCatalog();
                    loadConsignments();
                    loadVendorDashboard();
                }
            }
        });
    }

    function loadVendorDashboard() {
        $.ajax({
            url: ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_inventory_get_vendor_dashboard',
                security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
            },
            success: function(res) {
                if (!res.success) return;
                const data = res.data;
                const csn = data.active_consignment;
                const bannerWrap = $('#cora-vendor-consignment-banner-wrap');

                if (csn) {
                    activeConsignmentId = parseInt(csn.id);
                    const dispatched = parseFloat(csn.total_dispatched_val || 0);
                    const sold = parseFloat(csn.total_sold_val || 0);
                    const onWheels = Math.max(0, dispatched - sold);
                    const cash = parseFloat(csn.cash_collected || 0);

                    bannerWrap.html(`
                        <div class="p-5 rounded-3xl bg-zinc-950 text-white shadow-lg relative overflow-hidden border border-zinc-800">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-900 text-zinc-300 border border-zinc-800 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Active Consignment #${escapeHtml(csn.consignment_no)}
                                </span>
                                <span class="text-[11px] font-mono text-zinc-400">${escapeHtml(csn.vehicle_no || '')}</span>
                            </div>
                            <div class="text-3xl font-bold font-mono tracking-tight text-white mb-1">
                                ₹${Number(onWheels).toLocaleString('en-IN')} <span class="text-xs font-sans font-normal text-zinc-400">Stock on Wheels</span>
                            </div>
                            <div class="text-xs text-zinc-400 truncate mt-1">Route: ${escapeHtml(csn.route_name || 'Assigned Territory')}</div>
                            <div class="flex items-center justify-between pt-3 mt-3 border-t border-zinc-800 text-xs text-zinc-400">
                                <div>Sold Today: <span class="font-bold text-zinc-100 font-mono">₹${Number(sold).toLocaleString('en-IN')}</span></div>
                                <div>Cash in Hand: <span class="font-bold text-white font-mono">₹${Number(cash).toLocaleString('en-IN')}</span></div>
                            </div>
                        </div>
                    `);
                } else {
                    activeConsignmentId = 0;
                    bannerWrap.html(`
                        <div class="p-6 rounded-3xl bg-zinc-950 text-white shadow-lg relative overflow-hidden border border-zinc-800">
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-900 text-zinc-400 border border-zinc-800 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-600"></span> Standby Mode
                                </span>
                                <span class="text-[11px] font-mono text-zinc-500">No Active Consignment</span>
                            </div>
                            <div class="text-2xl font-bold font-mono tracking-tight text-white mb-1">
                                ₹0 <span class="text-xs font-sans font-normal text-zinc-400">Stock on Wheels</span>
                            </div>
                            <p class="text-xs text-zinc-400 mt-2 leading-relaxed">No active consignment allocated for this shift. Factory dispatch will assign route stock to this terminal.</p>
                            <div class="flex items-center justify-between pt-3 mt-4 border-t border-zinc-800/80 text-xs text-zinc-400">
                                <div>Sold Today: <span class="font-bold text-zinc-300 font-mono">₹0</span></div>
                                <div>Cash in Hand: <span class="font-bold text-zinc-300 font-mono">₹0</span></div>
                            </div>
                        </div>
                    `);
                }

                // Render Today's Spot Sales Ledger
                const salesList = $('#cora-vendor-sales-list');
                const sales = data.sales || [];
                if (!sales.length) {
                    salesList.html(`
                        <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-dashed border-zinc-200/80 dark:border-zinc-700/60 text-center">
                            <div class="w-10 h-10 mx-auto mb-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            </div>
                            <div class="text-xs font-bold text-zinc-800 dark:text-zinc-200">No Spot Sales Logged Today</div>
                            <div class="text-[11px] text-zinc-400 mt-0.5">Use "Quick Spot Sale" or "Snap Paper Bill" to bill local retail shops on this route.</div>
                        </div>
                    `);
                } else {
                    let salesHtml = '';
                    sales.forEach(s => {
                        const isCash = (s.payment_mode || '').toLowerCase() === 'cash';
                        const badgeText = isCash ? 'Cash Received' : (s.payment_mode || 'UPI').toUpperCase() + ' Verified';
                        salesHtml += `
                            <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 text-xs">
                                <div>
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100">${escapeHtml(s.customer_name || 'Retail Customer')}</div>
                                    <div class="text-[11px] text-zinc-400">${escapeHtml(s.invoice_no)} • ${escapeHtml(s.phone || 'Walk-in')}</div>
                                </div>
                                <div class="text-right font-mono">
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100">₹${Number(s.grand_total || 0).toLocaleString('en-IN')}</div>
                                    <div class="text-[10px] text-zinc-400">${badgeText}</div>
                                </div>
                            </div>
                        `;
                    });
                    salesList.html(salesHtml);
                }
            }
        });
    }

    function escapeHtml(str) {
        return (str || '').toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Initialize on DOM ready
    $(document).ready(init);

    return {
        switchPerspective,
        switchSubtab,
        loadCatalog,
        debouncedSearch: () => {
            clearTimeout(window._coraSearchTimer);
            window._coraSearchTimer = setTimeout(loadCatalog, 250);
        },
        openProductModal,
        closeProductModal,
        openConsignmentModal,
        closeConsignmentModal,
        openSpotSaleSheet,
        closeSpotSaleSheet,
        openOCRSheet: () => { switchSubtab('ocr'); switchPerspective('plant'); },
        openShopVisitSheet: () => { switchSubtab('map'); switchPerspective('plant'); },
        openReconcileSheet: () => { switchSubtab('recon'); switchPerspective('plant'); },
        handleOCRFile,
        processOCR,
        confirmOCRSale,
        generateDailyRecon,
        exportDailyPDF,
        dispatchConsignment,
        recalcConsignmentVal,
        recalcSpotTotal,
        submitSpotSale,
        settleConsignment: (id) => {
            window.coraShowToast('Settling day-end returns for Consignment #' + id + '...', 'info');
            $.ajax({
                url: ajaxurl || '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'cora_inventory_reconcile_consignment',
                    security: window.cora_nonce || '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                    consignment_id: id
                },
                success: function(res) {
                    if (res.success) {
                        window.coraShowToast('Consignment settled & unsold stock restocked to factory.', 'success');
                        loadCatalog();
                        loadConsignments();
                        loadVendorDashboard();
                    }
                }
            });
        },
        quickAdjustStock: (id, name) => {
            openProductModal(id);
        }
    };

})(jQuery);
</script>
