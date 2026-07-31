<?php
/**
 * Cora Workspace - Studio Camera Equipment & Gear Management View
 * File: views/view-equipment.php
 * Studio-grade asset tracking, shoot checkouts, crew allocations & maintenance financial logs.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Fetch options from WP database or fallback to sample data
$cora_studio_gear      = get_option( 'cora_studio_gear', array() );
$cora_gear_checkouts   = get_option( 'cora_gear_checkouts', array() );
$cora_gear_maintenance = get_option( 'cora_gear_maintenance', array() );
$cora_gear_kits        = get_option( 'cora_gear_kits', array() );

// Check if we need to force re-initialize the new fallback (so changes are visible immediately)
$cora_needs_init = false;
if ( empty( $cora_studio_gear ) || ! is_array( $cora_studio_gear ) ) {
    $cora_needs_init = true;
} else {
    // Check if the first item has the new ID gear_sony_a7iv
    $first_item = reset( $cora_studio_gear );
    if ( ! isset( $first_item['id'] ) || $first_item['id'] !== 'gear_sony_a7iv' ) {
        $cora_needs_init = true;
    }
}

// 1. Fallback Sample Data for Studio Gear Registry
if ( $cora_needs_init ) {
    $cora_studio_gear = array(
        array(
            'id'            => 'gear_sony_a7iv',
            'name'          => 'Sony Alpha a7 IV Cinema Camera',
            'serial'        => 'SN-774921',
            'serial_no'     => 'SN-774921',
            'category'      => 'Camera',
            'capex'         => 245000,
            'purchase_price'=> 245000,
            'current_value' => 245000,
            'condition'     => 'Excellent',
            'status'        => 'On Shoot',
            'assigned'      => 'Wedding Shoot - Rahul & Priya',
            'assigned_to'   => 'Wedding Shoot - Rahul & Priya',
            'purchase_date' => '2025-08-15',
            'image'         => 'gear_sony_a7iv.jpg',
            'operator'      => 'Karan Malhotra',
        ),
        array(
            'id'            => 'gear_red_komodo',
            'name'          => 'RED Komodo 6K Cinema Package',
            'serial'        => 'RED-9941',
            'serial_no'     => 'RED-9941',
            'category'      => 'Camera',
            'capex'         => 680000,
            'purchase_price'=> 680000,
            'current_value' => 680000,
            'condition'     => 'Mint',
            'status'        => 'Available',
            'assigned'      => 'Unassigned (Studio Vault)',
            'assigned_to'   => 'Unassigned (Studio Vault)',
            'purchase_date' => '2025-07-10',
            'image'         => 'gear_red_komodo.jpg',
            'operator'      => 'N/A',
        ),
        array(
            'id'            => 'gear_canon_2470',
            'name'          => 'Canon RF 24-70mm f/2.8L IS USM',
            'serial'        => 'RF24-7028',
            'serial_no'     => 'RF24-7028',
            'category'      => 'Lens',
            'capex'         => 152000,
            'purchase_price'=> 152000,
            'current_value' => 152000,
            'condition'     => 'Available',
            'status'        => 'Available',
            'assigned'      => 'Unassigned (Studio Vault)',
            'assigned_to'   => 'Unassigned (Studio Vault)',
            'purchase_date' => '2025-05-22',
            'image'         => 'gear_canon_2470.jpg',
            'operator'      => 'N/A',
        ),
        array(
            'id'            => 'gear_manfrotto_tripod',
            'name'          => 'Manfrotto 504X Fluid Head Tripod',
            'serial'        => 'MF-504X-221',
            'serial_no'     => 'MF-504X-221',
            'category'      => 'Accessories',
            'capex'         => 72000,
            'purchase_price'=> 72000,
            'current_value' => 72000,
            'condition'     => 'Excellent',
            'status'        => 'On Shoot',
            'assigned'      => 'Ad Shoot - ACME Agency',
            'assigned_to'   => 'Ad Shoot - ACME Agency',
            'purchase_date' => '2025-03-18',
            'image'         => 'gear_manfrotto_tripod.jpg',
            'operator'      => 'N/A',
        ),
        array(
            'id'            => 'gear_aputure_300d',
            'name'          => 'Aputure 300D II LED Light',
            'serial'        => 'AP300D-5567',
            'serial_no'     => 'AP300D-5567',
            'category'      => 'Lighting',
            'capex'         => 98000,
            'purchase_price'=> 98000,
            'current_value' => 98000,
            'condition'     => 'Needs Repair',
            'status'        => 'In Repair',
            'assigned'      => 'Repair: Driver Issue Est. Cost: ₹2,500',
            'assigned_to'   => 'Repair: Driver Issue Est. Cost: ₹2,500',
            'purchase_date' => '2025-02-11',
            'image'         => 'gear_aputure_300d.jpg',
            'operator'      => 'N/A',
        ),
    );
    update_option( 'cora_studio_gear', $cora_studio_gear );
}

// Check and pre-populate maintenance for Aputure 300D II LED Light
$has_aputure_repair = false;
if ( is_array( $cora_gear_maintenance ) ) {
    foreach ( $cora_gear_maintenance as $maint ) {
        if ( isset( $maint['gear_id'] ) && $maint['gear_id'] === 'gear_aputure_300d' ) {
            $has_aputure_repair = true;
            break;
        }
    }
}
if ( ! $has_aputure_repair ) {
    if ( ! is_array( $cora_gear_maintenance ) ) {
        $cora_gear_maintenance = array();
    }
    $cora_gear_maintenance[] = array(
        'id'               => 'maint_aputure',
        'gear_id'          => 'gear_aputure_300d',
        'gear_name'        => 'Aputure 300D II LED Light',
        'maintenance_type' => 'Driver Issue',
        'cost'             => 2500,
        'vendor'           => 'Light Source Delhi',
        'notes'            => 'Driver Issue',
        'serviced_date'    => '2025-02-11',
        'created_at'       => '2025-02-11 12:00:00',
    );
    update_option( 'cora_gear_maintenance', $cora_gear_maintenance );
}

$initial_repair_data = array();
if ( is_array( $cora_gear_maintenance ) ) {
    foreach ( $cora_gear_maintenance as $maint ) {
        if ( ! empty( $maint['gear_id'] ) ) {
            $gid = $maint['gear_id'];
            if ( ! isset( $initial_repair_data[$gid] ) ) {
                $initial_repair_data[$gid] = array(
                    'vendor' => $maint['vendor'] ?? '',
                    'cost'   => floatval( $maint['cost'] ?? 0 ),
                    'notes'  => $maint['notes'] ?? $maint['maintenance_type'] ?? '',
                    'date'   => $maint['serviced_date'] ?? '',
                    'name'   => $maint['gear_name'] ?? '',
                );
            }
        }
    }
}

// 2. Fallback Sample Data for Shoot Checkouts
if ( empty( $cora_gear_checkouts ) || ! is_array( $cora_gear_checkouts ) ) {
    $cora_gear_checkouts = array(
        array(
            'id'              => 'chk_501',
            'gear_id'         => 'gear_101',
            'gear_name'       => 'Sony Alpha a7 IV Cinema Camera',
            'serial'          => 'SN-774921',
            'shoot_title'     => 'Wedding 4K Film - Rahul & Priya',
            'client'          => 'Rahul Sharma',
            'dop_pilot'       => 'Karan Malhotra',
            'checkout_date'   => '2026-07-25',
            'return_due_date' => '2026-07-28',
            'status'          => 'Active'
        ),
        array(
            'id'              => 'chk_502',
            'gear_id'         => 'gear_103',
            'gear_name'       => 'DJI Mavic 3 Pro Cine Drone',
            'serial'          => 'DJI-88301',
            'shoot_title'     => 'DLF Cyber City Commercial 4K',
            'client'          => 'DLF Properties',
            'dop_pilot'       => 'Rohan Verma',
            'checkout_date'   => '2026-07-26',
            'return_due_date' => '2026-07-27',
            'status'          => 'Active'
        ),
        array(
            'id'              => 'chk_503',
            'gear_id'         => 'gear_104',
            'gear_name'       => 'Canon RF 70-200mm f/2.8L IS USM Lens',
            'serial'          => 'CN-11244',
            'shoot_title'     => 'Luxury Penthouse Walkthrough',
            'client'          => 'Oberoi Realty',
            'dop_pilot'       => 'Amit Kumar',
            'checkout_date'   => '2026-07-20',
            'return_due_date' => '2026-07-22',
            'status'          => 'Returned'
        )
    );
    update_option( 'cora_gear_checkouts', $cora_gear_checkouts );
}

// 3. Fallback Sample Data for Maintenance & Financial Ledger Logs
if ( empty( $cora_gear_maintenance ) || ! is_array( $cora_gear_maintenance ) ) {
    $cora_gear_maintenance = array(
        array(
            'id'           => 'mnt_801',
            'gear_id'      => 'gear_105',
            'equipment'    => 'Aputure 600d Pro LED Light Kit (APT-6001)',
            'repair_type'  => 'COB LED Array Replacement & Fan Cleaning',
            'service_date' => '2026-07-24',
            'vendor'       => 'Light Source India Tech',
            'repair_cost'  => 18500,
            'sync_status'  => 'Synced to Financial Ledger',
            'notes'        => 'Replaced overheating diode chip module under warranty extension.'
        ),
        array(
            'id'           => 'mnt_802',
            'gear_id'      => 'gear_102',
            'equipment'    => 'RED Komodo 6K Cinema Package (RED-9941)',
            'repair_type'  => 'Sensor Calibration & Firmware Upgrade',
            'service_date' => '2026-06-15',
            'vendor'       => 'RED Digital Cinema Service Center',
            'repair_cost'  => 12000,
            'sync_status'  => 'Synced to Financial Ledger',
            'notes'        => 'Annual sensor cleaning and v2.1.4 color science update.'
        )
    );
    update_option( 'cora_gear_maintenance', $cora_gear_maintenance );
}

// 4. Fallback Sample Data for Studio Gear Kits
if ( empty( $cora_gear_kits ) || ! is_array( $cora_gear_kits ) ) {
    $cora_gear_kits = array(
        array(
            'id'          => 'kit_201',
            'name'        => 'Wedding 4K Dual-Camera Kit',
            'category'    => 'Cinema Production',
            'description' => 'Complete multi-camera cinema package equipped with prime & zoom lenses, wireless audio receivers, and video stabilization.',
            'items'       => array('Sony Alpha a7 IV', 'Canon RF 70-200mm f/2.8L', 'Sennheiser EW-DP Mic Set', 'Manfrotto Video Tripod'),
            'daily_rate'  => 15000,
            'status'      => 'Available'
        ),
        array(
            'id'          => 'kit_202',
            'name'        => 'Drone Aerial Survey & Promo Kit',
            'category'    => 'Aerial Cinematography',
            'description' => 'DGCA-ready 4K drone cinematography setup complete with high-bright smart controller, 4 intelligent flight batteries, and ND filters.',
            'items'       => array('DJI Mavic 3 Pro Cine', 'Smart Controller', '4x Intelligent Batteries', 'PolarPro ND Filter Set'),
            'daily_rate'  => 12500,
            'status'      => 'In Use'
        ),
        array(
            'id'          => 'kit_203',
            'name'        => 'Studio Lighting & Grip Package',
            'category'    => 'Lighting & Grip',
            'description' => 'High-output key daylight fixture with parabolic softbox modifier, heavy-duty C-stands, and weighted sandbags.',
            'items'       => array('Aputure 600d Pro', 'Light Dome III Softbox', '2x Heavy Duty C-Stands', '4x Sandbags & Cables'),
            'daily_rate'  => 8000,
            'status'      => 'Maintenance'
        )
    );
    update_option( 'cora_gear_kits', $cora_gear_kits );
}

// Compute Dynamic Financial & Inventory Metrics
$total_capex_valuation = 0;
$available_count       = 0;
$checked_out_count     = 0;
$maintenance_count     = 0;

foreach ( $cora_studio_gear as $gear ) {
    $total_capex_valuation += floatval( $gear['capex'] ?? $gear['purchase_price'] ?? 0 );
    $st = $gear['status'] ?? 'Available';
    if ( $st === 'Available' )        $available_count++;
    elseif ( $st === 'In Use' || $st === 'On Shoot' )      $checked_out_count++;
    elseif ( $st === 'Maintenance' || $st === 'In Repair' ) $maintenance_count++;
}

$total_repair_expense = 0;
if ( is_array( $cora_gear_maintenance ) ) {
    foreach ( $cora_gear_maintenance as $maint ) {
        $total_repair_expense += floatval( $maint['cost'] ?? 0 );
    }
}
?>
<div id="cora-equipment-view-wrapper" class="space-y-6 font-sans text-zinc-900 max-w-[1700px] mx-auto pb-12">
    
    <!-- ═══ 1. STANDARDIZED PAGE HEADER & CTA ACTION BAR ═════════════════════════════════ -->
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 sm:gap-4 w-full">
        <div class="min-w-0">
            <h1 class="text-lg sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 leading-snug">Camera Equipment & Gear Inventory</h1>
            <p class="text-[11px] sm:text-xs text-zinc-550 dark:text-zinc-400 mt-0.5 sm:mt-1">Manage studio camera gear assets, shoot checkouts, crew allocations, and financial maintenance costs.</p>
        </div>

        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            <button onclick="openCheckoutGearDrawer()" class="flex-1 sm:flex-none px-3.5 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-850 text-zinc-800 dark:text-zinc-250 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center justify-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-450 dark:text-zinc-500 shrink-0"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                Check Out Gear
            </button>
            <button onclick="openMaintenanceDrawer()" class="flex-1 sm:flex-none px-3.5 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-850 text-zinc-800 dark:text-zinc-250 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center justify-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-450 dark:text-zinc-500 shrink-0"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                Log Repair & Cost
            </button>
            <!-- Primary Action CTA -->
            <button onclick="openAddGearDrawer()" class="w-full sm:w-auto px-3.5 py-2 bg-zinc-950 dark:bg-white hover:bg-zinc-850 dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Register New Gear
            </button>
        </div>
    </div>

    <!-- ═══ 2. MONOCHROMATIC 4-KPI METRIC STAT CARDS ═════════════════════════════════ -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <!-- 1. Total Asset Valuation -->
        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[100px] sm:min-h-[110px]">
            <div class="flex items-start justify-between gap-2">
                <span class="text-[9px] sm:text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest leading-tight">Total Asset Valuation</span>
                <span class="text-[8px] sm:text-[9px] font-bold uppercase px-2 py-0.5 rounded-full text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700/50 shrink-0">CapEx</span>
            </div>
            <div class="mt-2">
                <span id="kpi-total-val" class="text-base sm:text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight leading-none">₹<?php echo number_format( $total_capex_valuation ); ?></span>
                <span class="text-[9px] sm:text-[10px] text-zinc-400 dark:text-zinc-500 block mt-1 font-medium">Total acquisition cost</span>
            </div>
        </div>

        <!-- 2. Available in Studio -->
        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[100px] sm:min-h-[110px]">
            <div class="flex items-start justify-between gap-2">
                <span class="text-[9px] sm:text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest leading-tight">Available in Studio</span>
                <span class="inline-flex items-center gap-1 text-[8px] sm:text-[9px] font-bold uppercase px-2 py-0.5 rounded-full text-emerald-700 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-950/20 border border-emerald-200/50 dark:border-emerald-900/30 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ready
                </span>
            </div>
            <div class="mt-2">
                <span id="kpi-avail-count" class="text-lg sm:text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight leading-none"><?php echo $available_count; ?> <span class="text-xs sm:text-sm font-semibold text-zinc-450">Items</span></span>
                <span class="text-[9px] sm:text-[10px] text-zinc-400 dark:text-zinc-500 block mt-1 font-medium">Ready in studio vault</span>
            </div>
        </div>

        <!-- 3. Checked Out on Shoots -->
        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[100px] sm:min-h-[110px]">
            <div class="flex items-start justify-between gap-2">
                <span class="text-[9px] sm:text-[10px] font-bold text-zinc-455 dark:text-zinc-500 uppercase tracking-widest leading-tight">Checked Out</span>
                <span class="inline-flex items-center gap-1 text-[8px] sm:text-[9px] font-bold uppercase px-2 py-0.5 rounded-full text-amber-700 bg-amber-50 dark:text-amber-400 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/30 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> On Field
                </span>
            </div>
            <div class="mt-2">
                <span id="kpi-checkout-count" class="text-lg sm:text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight leading-none"><?php echo $checked_out_count; ?> <span class="text-xs sm:text-sm font-semibold text-zinc-455">Allocated</span></span>
                <span class="text-[9px] sm:text-[10px] text-zinc-450 dark:text-zinc-500 block mt-1 font-medium">Active shoot checkouts</span>
            </div>
        </div>

        <!-- 4. Under Maintenance / Servicing -->
        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[100px] sm:min-h-[110px]">
            <div class="flex items-start justify-between gap-2">
                <span class="text-[9px] sm:text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest leading-tight">In Repair</span>
                <span class="text-[8px] sm:text-[9px] font-bold uppercase px-2 py-0.5 rounded-full text-rose-700 dark:text-rose-455 bg-rose-50 dark:bg-rose-950/20 border border-rose-200/50 dark:border-rose-900/30 shrink-0">OpEx</span>
            </div>
            <div class="mt-2">
                <span id="kpi-maint-count" class="text-lg sm:text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight leading-none"><?php echo $maintenance_count; ?> <span class="text-xs sm:text-sm font-semibold text-zinc-450">Item<?php echo $maintenance_count === 1 ? '' : 's'; ?></span></span>
                <span class="text-[9px] sm:text-[10px] text-zinc-400 dark:text-zinc-500 block mt-1 font-medium">Servicing Cost: ₹<?php echo number_format( $total_repair_expense ); ?></span>
            </div>
        </div>
    </div>

    <!-- ═══ 3. STANDARDIZED PLATFORM SUB-TAB BAR ═════════════════════════════════ -->
    <div class="cora-sub-tabs border-b border-zinc-200 dark:border-zinc-800 overflow-x-auto select-none pb-0.5 -mb-px">
        <div class="flex gap-5 text-xs font-bold text-zinc-450 dark:text-zinc-500 whitespace-nowrap min-w-max">
            <button id="tab-btn-registry" onclick="coraSwitchEquipmentTab('registry')" class="cora-eq-tab-btn active pb-2.5 border-b-2 border-zinc-950 dark:border-white text-zinc-950 dark:text-white cursor-pointer flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                Gear Registry <span id="cnt-tab-registry" class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-650 dark:text-zinc-350 px-2 py-0.5 rounded-full border border-zinc-200/40 dark:border-zinc-700/40 font-mono"><?php echo count( $cora_studio_gear ); ?></span>
            </button>

            <button id="tab-btn-checkouts" onclick="coraSwitchEquipmentTab('checkouts')" class="cora-eq-tab-btn pb-2.5 border-b-2 border-transparent hover:text-zinc-900 dark:hover:text-white text-zinc-450 dark:text-zinc-500 cursor-pointer flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Shoot Checkouts <span id="cnt-tab-checkouts" class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-650 dark:text-zinc-350 px-2 py-0.5 rounded-full border border-zinc-200/40 dark:border-zinc-700/40 font-mono"><?php echo count( $cora_gear_checkouts ); ?></span>
            </button>

            <button id="tab-btn-maintenance" onclick="coraSwitchEquipmentTab('maintenance')" class="cora-eq-tab-btn pb-2.5 border-b-2 border-transparent hover:text-zinc-900 dark:hover:text-white text-zinc-450 dark:text-zinc-500 cursor-pointer flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                Maintenance & Financial Ledger <span id="cnt-tab-maintenance" class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-650 dark:text-zinc-350 px-2 py-0.5 rounded-full border border-zinc-200/40 dark:border-zinc-700/40 font-mono"><?php echo count( $cora_gear_maintenance ); ?></span>
            </button>

            <button id="tab-btn-kits" onclick="coraSwitchEquipmentTab('kits')" class="cora-eq-tab-btn pb-2.5 border-b-2 border-transparent hover:text-zinc-900 dark:hover:text-white text-zinc-450 dark:text-zinc-500 cursor-pointer flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                Studio Gear Kits <span id="cnt-tab-kits" class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-650 dark:text-zinc-350 px-2 py-0.5 rounded-full border border-zinc-200/40 dark:border-zinc-700/40 font-mono"><?php echo count( $cora_gear_kits ); ?></span>
            </button>
        </div>
    </div>

    <!-- ═══ 4. SUB-TAB PANELS CONTAINER ═════════════════════════════════════════════ -->
    
    <!-- SUB-TAB 1: GEAR REGISTRY -->
    <div id="cora-eq-tab-registry" class="cora-eq-tab-content space-y-3 pt-3">
        <!-- Row 1: Title -->
        <div>
            <h3 class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-white">Gear Registry</h3>
            <p class="text-[10px] sm:text-xs text-zinc-500 dark:text-zinc-450 mt-0.5">Asset tracking, condition grading &amp; CapEx valuation.</p>
        </div>
        <!-- Row 2: Search + Filter -->
        <div class="flex items-center gap-2 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <div class="relative flex-1">
                <input type="text" id="gear-search-input" onkeyup="coraFilterGearTable()" placeholder="Search gear or serial #..." class="w-full pl-7 pr-2 py-1.5 bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-350 focus:bg-white">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2 top-2 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <select id="gear-category-filter" onchange="coraFilterGearTable()" class="shrink-0 px-2 py-1.5 bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs font-semibold text-zinc-700 dark:text-zinc-300 focus:outline-none cursor-pointer">
                <option value="">All Categories</option>
                <option value="Camera">Camera</option>
                <option value="Lens">Lens</option>
                <option value="Lighting">Lighting</option>
                <option value="Drone">Drone</option>
                <option value="Audio">Audio</option>
                <option value="Accessories">Accessories</option>
            </select>
        </div>

        <div id="cora-gear-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php foreach ( $cora_studio_gear as $gear ) :
                $status = $gear['status'] ?? 'Available';
                $status_badge = '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full text-emerald-700 dark:text-emerald-450 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-250/20 dark:border-emerald-900/50 select-none"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available</span>';
                if ( $status === 'In Use' || $status === 'On Shoot' || $status === 'On-Site' || $status === 'Allocated' ) {
                    $status_badge = '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full text-amber-700 dark:text-amber-450 bg-amber-50 dark:bg-amber-950/20 border border-amber-250/20 dark:border-amber-900/50 select-none"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> On Shoot</span>';
                } elseif ( $status === 'Maintenance' || $status === 'In Repair' ) {
                    $status_badge = '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full text-rose-700 dark:text-rose-455 bg-rose-50 dark:bg-rose-950/20 border border-rose-250/20 dark:border-rose-900/50 select-none"><span class="w-1.5 h-1.5 rounded-full bg-rose-550 animate-pulse"></span> In Repair</span>';
                }

                $category = $gear['category'] ?? 'Camera';
                $cat_icon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>';
                if ( $category === 'Lens' )        $cat_icon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>';
                elseif ( $category === 'Lighting' ) $cat_icon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line></svg>';
                elseif ( $category === 'Drone' )    $cat_icon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>';
                elseif ( $category === 'Audio' )    $cat_icon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line></svg>';

                $img_html = '';
                if ( ! empty( $gear['image'] ) ) {
                    $img_src = $gear['image'];
                    if ( strpos( $img_src, 'data:' ) === 0 || strpos( $img_src, 'http' ) === 0 ) $img_url = $img_src;
                    elseif ( strpos( $img_src, '/' ) === 0 ) $img_url = $img_src;
                    elseif ( strpos( $img_src, '/' ) !== false ) $img_url = '/wp-content/' . $img_src;
                    else $img_url = '/wp-content/plugins/cora-workspace/assets/images/' . $img_src;
                    $img_html = '<img src="' . esc_url( $img_url ) . '" class="w-10 h-10 rounded-xl object-cover shrink-0 border border-zinc-200 dark:border-zinc-800" alt="' . esc_attr( $gear['name'] ) . '">';
                } else {
                    $img_html = '<div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-750/60 flex items-center justify-center shrink-0">' . $cat_icon . '</div>';
                }
            ?>
            <div id="gear-row-<?php echo esc_attr( $gear['id'] ); ?>" class="gear-table-row bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 hover:shadow-sm transition-all flex flex-col gap-3">
                <!-- Card Header: Image + Name + Status -->
                <div class="flex items-start gap-3">
                    <?php echo $img_html; ?>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <span class="font-bold text-zinc-900 dark:text-white gear-item-name text-xs leading-snug"><?php echo esc_html( $gear['name'] ); ?></span>
                            <?php echo $status_badge; ?>
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700/50 text-zinc-600 dark:text-zinc-400 gear-item-category"><?php echo esc_html( $gear['category'] ); ?></span>
                            <span class="text-[9px] text-zinc-400 dark:text-zinc-500 font-mono gear-item-serial"><?php echo esc_html( $gear['serial'] ?? $gear['serial_no'] ?? '' ); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Card Meta Grid -->
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">CapEx Value</div>
                        <div class="font-mono font-bold text-zinc-900 dark:text-white text-xs mt-0.5">₹<?php echo number_format( floatval( $gear['capex'] ?? $gear['purchase_price'] ?? 0 ) ); ?></div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Condition</div>
                        <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mt-0.5"><?php echo esc_html( $gear['condition'] ); ?></div>
                    </div>
                    <?php if ( ! empty( $gear['assigned'] ?? $gear['assigned_to'] ?? '' ) ) : ?>
                    <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Assigned To</div>
                        <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mt-0.5"><?php echo esc_html( $gear['assigned'] ?? $gear['assigned_to'] ?? '' ); ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Purchased</div>
                        <div class="font-mono text-[10px] text-zinc-500 dark:text-zinc-400 mt-0.5"><?php echo esc_html( $gear['purchase_date'] ?? 'N/A' ); ?></div>
                    </div>
                </div>

                <!-- Card Footer: Actions -->
                <div class="flex items-center gap-2 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                    <?php if ( $status === 'In Repair' || $status === 'Maintenance' ) : ?>
                        <button onclick="openViewRepairDrawer('<?php echo esc_attr( $gear['id'] ); ?>')" class="flex-1 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg text-xs font-semibold transition-all cursor-pointer text-center">View Repair</button>
                    <?php elseif ( $status === 'On Shoot' || $status === 'In Use' || $status === 'On-Site' || $status === 'Allocated' ) : ?>
                        <button onclick="coraReturnCheckoutItem('', '<?php echo esc_attr( esc_js( $gear['name'] ) ); ?>', '<?php echo esc_attr( $gear['id'] ); ?>')" class="flex-1 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 rounded-lg text-xs font-semibold transition-all cursor-pointer text-center">Return</button>
                    <?php else : ?>
                        <button onclick="openCheckoutGearDrawer('<?php echo esc_attr( $gear['id'] ); ?>')" class="flex-1 px-3 py-1.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-100 rounded-lg text-xs font-semibold transition-all cursor-pointer text-center">Check Out</button>
                    <?php endif; ?>

                    <!-- More Options -->
                    <div class="relative inline-block">
                        <button onclick="coraToggleRowActions(event, '<?php echo esc_attr( $gear['id'] ); ?>')" class="w-7 h-7 inline-flex items-center justify-center border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg text-zinc-400 dark:text-zinc-500 transition-all cursor-pointer">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                        </button>
                        <div id="cora-row-actions-<?php echo esc_attr( $gear['id'] ); ?>" class="cora-row-actions-dropdown hidden absolute right-0 bottom-9 w-36 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-lg py-1.5 z-40 space-y-0.5">
                            <?php if ( $status !== 'In Repair' && $status !== 'Maintenance' ) : ?>
                                <button onclick="openMaintenanceDrawer('<?php echo esc_attr( $gear['id'] ); ?>'); coraHideAllDropdowns();" class="w-full text-left px-3 py-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-850 text-zinc-700 dark:text-zinc-300 text-xs font-semibold flex items-center gap-2 cursor-pointer">
                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                                    Log Repair
                                </button>
                            <?php endif; ?>
                            <button onclick="openEditGearDrawer('<?php echo esc_attr( $gear['id'] ); ?>'); coraHideAllDropdowns();" class="w-full text-left px-3 py-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-850 text-zinc-700 dark:text-zinc-300 text-xs font-semibold flex items-center gap-2 cursor-pointer">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Edit Specs
                            </button>
                            <button onclick="coraDeleteGearItem('<?php echo esc_attr( $gear['id'] ); ?>'); coraHideAllDropdowns();" class="w-full text-left px-3 py-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/20 text-rose-700 dark:text-rose-400 text-xs font-semibold flex items-center gap-2 cursor-pointer border-t border-zinc-100 dark:border-zinc-800">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                Delete Item
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SUB-TAB 2: SHOOT CHECKOUTS & ALLOCATIONS -->
    <div id="cora-eq-tab-checkouts" class="cora-eq-tab-content space-y-3 hidden pt-3">
        <!-- Row 1: Title -->
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-white">Shoot Checkouts</h3>
                <p class="text-[10px] sm:text-xs text-zinc-500 dark:text-zinc-450 mt-0.5">Camera packages &amp; kits linked to active shoots &amp; field operators.</p>
            </div>
        </div>
        <!-- Row 2: CTA -->
        <div class="pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <button onclick="openCheckoutGearDrawer()" class="w-full sm:w-auto px-3.5 py-2 bg-zinc-950 dark:bg-white hover:bg-zinc-850 dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                New Shoot Checkout
            </button>
        </div>

        <div id="cora-checkouts-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php foreach ( $cora_gear_checkouts as $chk ) :
                $chk_st = $chk['status'] ?? 'Active';
                $chk_badge = '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full text-amber-700 dark:text-amber-450 bg-amber-50 dark:bg-amber-950/20 border border-amber-250/20 dark:border-amber-900/50 select-none"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Active</span>';
                if ( $chk_st === 'Returned' ) {
                    $chk_badge = '<span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full text-zinc-550 dark:text-zinc-450 bg-zinc-100 dark:bg-zinc-800/60 border border-zinc-200/50 dark:border-zinc-700/50 select-none"><span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span> Returned</span>';
                }
            ?>
            <div id="checkout-row-<?php echo esc_attr( $chk['id'] ); ?>" class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 hover:shadow-sm transition-all flex flex-col gap-3">
                <!-- Header: Gear name + status badge -->
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="font-bold text-zinc-900 dark:text-white text-xs leading-snug"><?php echo esc_html( $chk['gear_name'] ); ?></div>
                        <div class="font-mono text-[9px] text-zinc-400 dark:text-zinc-500 mt-0.5"><?php echo esc_html( $chk['serial'] ); ?></div>
                    </div>
                    <span id="chk-status-badge-<?php echo esc_attr( $chk['id'] ); ?>"><?php echo $chk_badge; ?></span>
                </div>

                <!-- Meta grid -->
                <div class="grid grid-cols-2 gap-2">
                    <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Shoot</div>
                        <div class="text-xs font-semibold text-zinc-900 dark:text-white mt-0.5"><?php echo esc_html( $chk['shoot_title'] ); ?></div>
                        <div class="text-[9px] text-zinc-500 dark:text-zinc-450 mt-0.5">Client: <?php echo esc_html( $chk['client'] ); ?></div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">DoP / Operator</div>
                        <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mt-0.5"><?php echo esc_html( $chk['dop_pilot'] ); ?></div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Return Due</div>
                        <div class="font-mono text-xs font-bold text-zinc-900 dark:text-white mt-0.5"><?php echo esc_html( $chk['return_due_date'] ); ?></div>
                        <div class="font-mono text-[9px] text-zinc-400 dark:text-zinc-500">Out: <?php echo esc_html( $chk['checkout_date'] ); ?></div>
                    </div>
                </div>

                <!-- Footer: Action -->
                <div class="pt-1 border-t border-zinc-100 dark:border-zinc-800">
                    <?php if ( $chk_st === 'Active' ) : ?>
                        <button id="chk-return-btn-<?php echo esc_attr( $chk['id'] ); ?>" onclick="coraReturnCheckoutItem('<?php echo esc_attr( $chk['id'] ); ?>', '<?php echo esc_attr( $chk['gear_name'] ); ?>')" class="w-full px-3 py-1.5 bg-zinc-950 dark:bg-white hover:bg-zinc-800 dark:hover:bg-zinc-100 text-white dark:text-zinc-950 rounded-lg text-xs font-semibold transition-all cursor-pointer text-center">Return to Studio</button>
                    <?php else : ?>
                        <span class="block text-center text-xs text-zinc-400 dark:text-zinc-500 font-semibold italic py-1">Checked In</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SUB-TAB 3: MAINTENANCE & FINANCIAL LEDGER -->
    <div id="cora-eq-tab-maintenance" class="cora-eq-tab-content space-y-3 hidden pt-3">
        <!-- Row 1: Title -->
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-white">Maintenance &amp; Financial Ledger</h3>
                <p class="text-[10px] sm:text-xs text-zinc-500 dark:text-zinc-450 mt-0.5">Repair history, vendor invoices &amp; CapEx/OpEx financial sync.</p>
            </div>
        </div>
        <!-- Row 2: CTA -->
        <div class="pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <button onclick="openMaintenanceDrawer()" class="w-full sm:w-auto px-3.5 py-2 bg-zinc-950 dark:bg-white hover:bg-zinc-850 dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                Log Service Expense
            </button>
        </div>

        <div id="cora-maintenance-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php foreach ( $cora_gear_maintenance as $mnt ) : ?>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 hover:shadow-sm transition-all flex flex-col gap-3">
                <!-- Header -->
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="font-bold text-zinc-900 dark:text-white text-xs leading-snug"><?php echo esc_html( $mnt['equipment'] ); ?></div>
                        <div class="text-[9px] text-zinc-500 dark:text-zinc-450 mt-0.5"><?php echo esc_html( $mnt['repair_type'] ); ?></div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[9px] font-bold uppercase px-2 py-0.5 rounded-full text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200/40 dark:border-emerald-900/50 shrink-0">
                        <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Synced
                    </span>
                </div>

                <?php if ( ! empty( $mnt['notes'] ) ) : ?>
                <p class="text-[10px] text-zinc-500 dark:text-zinc-450 leading-relaxed -mt-1"><?php echo esc_html( $mnt['notes'] ); ?></p>
                <?php endif; ?>

                <!-- Meta grid -->
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Repair Cost</div>
                        <div class="font-mono font-bold text-zinc-900 dark:text-white text-xs mt-0.5">₹<?php echo number_format( floatval( $mnt['repair_cost'] ) ); ?></div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Service Date</div>
                        <div class="font-mono text-xs text-zinc-600 dark:text-zinc-400 mt-0.5"><?php echo esc_html( $mnt['service_date'] ); ?></div>
                    </div>
                    <div class="col-span-2 bg-zinc-50 dark:bg-zinc-800/40 rounded-xl p-2.5">
                        <div class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Vendor / Workshop</div>
                        <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mt-0.5"><?php echo esc_html( $mnt['vendor'] ); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>



    <!-- SUB-TAB 4: STUDIO GEAR KITS -->
    <div id="cora-eq-tab-kits" class="cora-eq-tab-content space-y-4 hidden pt-3">
        <!-- Row 1: Title and Add Action -->
        <div class="pb-3 border-b border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-white">Studio Gear Kits</h3>
                <p class="text-[10px] sm:text-xs text-zinc-500 dark:text-zinc-450 mt-0.5">Bundled equipment packages ready for instant 1-click shoot assignment.</p>
            </div>
            <button onclick="openCreateKitDrawer()" class="w-full sm:w-auto px-3.5 py-2 bg-zinc-950 dark:bg-white hover:bg-zinc-850 dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl transition-all shadow-sm flex items-center justify-center gap-1.5 cursor-pointer border border-transparent">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Create Gear Kit
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ( $cora_gear_kits as $kit ) : ?>
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 sm:p-5 bg-white dark:bg-zinc-900 shadow-2xs space-y-4 flex flex-col justify-between hover:border-zinc-300 dark:hover:border-zinc-700 hover:shadow-sm transition-all">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700/50 text-zinc-650 dark:text-zinc-400">
                            <?php echo esc_html( $kit['category'] ); ?>
                        </span>
                        <span class="text-xs sm:text-sm font-mono font-bold text-zinc-900 dark:text-white shrink-0">₹<?php echo number_format( floatval( $kit['daily_rate'] ?? 0 ) ); ?>/day</span>
                    </div>
                    <div>
                        <h4 class="text-sm sm:text-base font-bold text-zinc-900 dark:text-white leading-snug"><?php echo esc_html( $kit['name'] ); ?></h4>
                        <p class="text-[11px] sm:text-xs text-zinc-550 dark:text-zinc-400 mt-1 leading-relaxed"><?php echo esc_html( $kit['description'] ); ?></p>
                    </div>
                    <div class="space-y-1.5 pt-1">
                        <span class="text-[9px] sm:text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Included Gear Units:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <?php if ( ! empty( $kit['items'] ) && is_array( $kit['items'] ) ) : ?>
                                <?php foreach ( $kit['items'] as $it ) : ?>
                                    <span class="px-2 py-0.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-full text-[10px] font-medium text-zinc-700 dark:text-zinc-300"><?php echo esc_html( $it ); ?></span>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <span class="text-[10px] text-zinc-400">No items configured in this kit.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800">
                    <button onclick="openCheckoutGearDrawer('[KIT] <?php echo esc_attr( $kit['name'] ); ?>')" class="w-full py-2 bg-zinc-950 dark:bg-white hover:bg-zinc-850 dark:hover:bg-zinc-100 text-white dark:text-zinc-950 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-2">
                        Allocate Kit to Shoot
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<!-- ═══ 5. RIGHT-SLIDING SIDE DRAWER SHEETS ═════════════════════════════════════ -->

<!-- 1. REGISTER NEW GEAR DRAWER -->
<aside id="cora-add-gear-drawer" class="cora-drawer-sheet hidden fixed top-0 right-0 z-[10000] h-full w-[500px] max-w-[95vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Register New Equipment Unit
            </h3>
            <p class="text-[11px] text-zinc-500 mt-0.5">Add camera, lens, lighting or drone unit to studio vault inventory.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeGearDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-add-gear-form" onsubmit="coraSubmitAddGearForm(event)" class="flex-1 flex flex-col overflow-hidden">
        <!-- Topbar Step Tracker -->
        <div class="px-6 py-4 bg-zinc-50 border-b border-zinc-200 shrink-0 flex items-center justify-between gap-2 select-none">
            <div class="cora-step-indicator flex items-center gap-2.5 cursor-pointer" data-step="1" onclick="if(addGearStep > 1 || validateCurrentStep('cora-add-gear-drawer', addGearStep)) setAddGearStep(1)">
                <div class="step-circle w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[10px] font-bold shrink-0 transition-all">1</div>
                <div class="step-label text-[11px] font-bold text-zinc-950 leading-tight">Specifications</div>
            </div>
            <div class="h-px bg-zinc-250 flex-1 max-w-[50px] mx-1"></div>
            <div class="cora-step-indicator flex items-center gap-2.5 cursor-pointer" data-step="2" onclick="if(addGearStep > 2 || validateCurrentStep('cora-add-gear-drawer', addGearStep)) setAddGearStep(2)">
                <div class="step-circle w-6 h-6 rounded-full border border-zinc-200 text-zinc-400 flex items-center justify-center text-[10px] font-medium shrink-0 bg-white transition-all">2</div>
                <div class="step-label text-[11px] font-medium text-zinc-400 leading-tight">Acquisition</div>
            </div>
            <div class="h-px bg-zinc-250 flex-1 max-w-[50px] mx-1"></div>
            <div class="cora-step-indicator flex items-center gap-2.5 cursor-pointer" data-step="3" onclick="if(validateCurrentStep('cora-add-gear-drawer', addGearStep)) setAddGearStep(3)">
                <div class="step-circle w-6 h-6 rounded-full border border-zinc-200 text-zinc-400 flex items-center justify-center text-[10px] font-medium shrink-0 bg-white transition-all">3</div>
                <div class="step-label text-[11px] font-medium text-zinc-400 leading-tight">Lifecycle</div>
            </div>
        </div>

        <!-- Content Panel -->
        <div class="flex-1 overflow-y-auto p-6 space-y-5">
            <!-- Step 1: General Details -->
            <div class="cora-step-content space-y-4" data-step="1">
                <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-3">General Specifications</h4>
                
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Equipment Name / Model *</label>
                    <input type="text" id="add-gear-name" required placeholder="e.g. Sony Alpha a7 IV Cinema Camera" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Serial Number # *</label>
                        <input type="text" id="add-gear-serial" required placeholder="SN-774921" class="w-full px-3 py-2 text-xs font-mono border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Category *</label>
                        <select id="add-gear-category" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                            <option value="Camera">Camera</option>
                            <option value="Lens">Lens</option>
                            <option value="Lighting">Lighting</option>
                            <option value="Drone">Drone</option>
                            <option value="Audio">Audio</option>
                            <option value="Accessories">Accessories</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Included Accessories</label>
                    <textarea id="add-gear-accessories" rows="4" placeholder="e.g. Charger, 2x Batteries, Lens Cap" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 resize-none"></textarea>
                </div>
            </div>

            <!-- Step 2: Financials -->
            <div class="cora-step-content space-y-4 hidden" data-step="2">
                <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-3">Acquisition & Financials</h4>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">CapEx Valuation (₹) *</label>
                        <input type="number" id="add-gear-capex" required placeholder="245000" class="w-full px-3 py-2 text-xs font-mono border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Purchase Date</label>
                        <input type="date" id="add-gear-date" value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Condition Rating *</label>
                        <select id="add-gear-condition" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                            <option value="Mint">Mint</option>
                            <option value="Excellent" selected>Excellent</option>
                            <option value="Good">Good</option>
                            <option value="Needs Repair">Needs Repair</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Initial Status *</label>
                        <select id="add-gear-status" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                            <option value="Available" selected>Available in Studio</option>
                            <option value="On Shoot">On Shoot</option>
                            <option value="In Repair">In Repair</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Storage Location</label>
                    <input type="text" id="add-gear-storage-location" placeholder="e.g. Shelf A-3" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                </div>
            </div>

            <!-- Step 3: Media & Lifecycle -->
            <div class="cora-step-content space-y-4 hidden" data-step="3">
                <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-3">Lifecycle & Media Asset</h4>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Next Service Due</label>
                        <input type="date" id="add-gear-next-service" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Insurance Expiry</label>
                        <input type="date" id="add-gear-insurance" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Assigned Crew / Shoot</label>
                    <input type="text" id="add-gear-assigned" placeholder="e.g. Unassigned (Studio Vault)" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Product Photo</label>
                    <div class="border border-zinc-200 rounded-xl bg-white overflow-hidden p-4 space-y-4">
                        <div id="add-gear-upload-box" class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-lg bg-zinc-100 border border-zinc-250 flex items-center justify-center shrink-0 overflow-hidden relative">
                                <img id="add-gear-image-preview" src="" class="w-full h-full object-cover hidden">
                                <svg id="add-gear-image-placeholder" viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-450"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="text-[11px] font-bold text-zinc-900 leading-tight">Attach a visual asset:</div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" onclick="document.getElementById('add-gear-image-file').click()" class="px-3 py-1.5 bg-zinc-950 text-white text-[10px] font-bold rounded-lg hover:bg-zinc-800 shadow-xs cursor-pointer flex items-center gap-1.5 border border-transparent">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                        Upload File
                                    </button>
                                    <button type="button" onclick="coraSelectMediaLibrary('add-gear-image-preview', 'add-gear-image-path')" class="px-3 py-1.5 bg-white text-zinc-700 text-[10px] font-bold rounded-lg hover:bg-zinc-50 cursor-pointer flex items-center gap-1.5 border border-zinc-200">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                        Media Library
                                    </button>
                                    <button type="button" onclick="coraStartWebcam('add')" class="px-3 py-1.5 bg-white text-zinc-700 text-[10px] font-bold rounded-lg hover:bg-zinc-50 cursor-pointer flex items-center gap-1.5 border border-zinc-200">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                        Access Camera
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="add-gear-camera-container" class="hidden border border-zinc-200 rounded-xl overflow-hidden bg-black relative w-full aspect-video">
                            <video id="add-gear-video" autoplay playsinline class="w-full h-full object-cover"></video>
                            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2 z-20">
                                <button type="button" onclick="coraCaptureWebcamPhoto('add-gear-image-preview', 'add-gear-image-path', 'add')" class="px-4 py-1.5 bg-zinc-950 text-white text-xs font-bold rounded-lg hover:bg-zinc-800 shadow-sm cursor-pointer border border-zinc-700">Capture</button>
                                <button type="button" onclick="coraStopWebcam('add')" class="px-4 py-1.5 bg-white text-zinc-800 text-xs font-bold rounded-lg hover:bg-zinc-100 shadow-sm cursor-pointer border border-zinc-250">Cancel</button>
                            </div>
                        </div>

                        <input type="file" id="add-gear-image-file" onchange="coraUploadGearImage(this, 'add-gear-image-preview', 'add-gear-image-path')" accept="image/*" class="hidden">
                        <input type="hidden" id="add-gear-image-path" value="">
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-4 border-t border-zinc-100 flex items-center justify-between bg-zinc-50/50 shrink-0">
            <button type="button" onclick="closeGearDrawers()" class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-50 cursor-pointer">Cancel</button>
            <div class="flex items-center gap-2">
                <button type="button" id="add-gear-back-btn" onclick="setAddGearStep(addGearStep - 1)" class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-50 cursor-pointer hidden">Back</button>
                <button type="button" id="add-gear-next-btn" onclick="if(validateCurrentStep('cora-add-gear-drawer', addGearStep)) setAddGearStep(addGearStep + 1)" class="px-4.5 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 cursor-pointer shadow-xs">Next</button>
                <button type="submit" id="add-gear-submit-btn" class="px-5 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 cursor-pointer shadow-xs hidden">Save & Register Gear</button>
            </div>
        </div>
    </form>
</aside>

<!-- 1.1 EDIT EXISTING GEAR DRAWER -->
<aside id="cora-edit-gear-drawer" class="cora-drawer-sheet hidden fixed top-0 right-0 z-[10000] h-full w-[500px] max-w-[95vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Edit Equipment Information
            </h3>
            <p class="text-[11px] text-zinc-500 mt-0.5">Modify camera gear, location, specifications, and active status.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeGearDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-edit-gear-form" onsubmit="coraSubmitEditGearForm(event)" class="flex-1 flex flex-col overflow-hidden">
        <input type="hidden" id="edit-gear-id">

        <!-- Topbar Step Tracker -->
        <div class="px-6 py-4 bg-zinc-50 border-b border-zinc-200 shrink-0 flex items-center justify-between gap-2 select-none">
            <div class="cora-step-indicator flex items-center gap-2.5 cursor-pointer" data-step="1" onclick="if(editGearStep > 1 || validateCurrentStep('cora-edit-gear-drawer', editGearStep)) setEditGearStep(1)">
                <div class="step-circle w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[10px] font-bold shrink-0 transition-all">1</div>
                <div class="step-label text-[11px] font-bold text-zinc-950 leading-tight">Specifications</div>
            </div>
            <div class="h-px bg-zinc-250 flex-1 max-w-[50px] mx-1"></div>
            <div class="cora-step-indicator flex items-center gap-2.5 cursor-pointer" data-step="2" onclick="if(editGearStep > 2 || validateCurrentStep('cora-edit-gear-drawer', editGearStep)) setEditGearStep(2)">
                <div class="step-circle w-6 h-6 rounded-full border border-zinc-200 text-zinc-400 flex items-center justify-center text-[10px] font-medium shrink-0 bg-white transition-all">2</div>
                <div class="step-label text-[11px] font-medium text-zinc-400 leading-tight">Acquisition</div>
            </div>
            <div class="h-px bg-zinc-250 flex-1 max-w-[50px] mx-1"></div>
            <div class="cora-step-indicator flex items-center gap-2.5 cursor-pointer" data-step="3" onclick="if(validateCurrentStep('cora-edit-gear-drawer', editGearStep)) setEditGearStep(3)">
                <div class="step-circle w-6 h-6 rounded-full border border-zinc-200 text-zinc-400 flex items-center justify-center text-[10px] font-medium shrink-0 bg-white transition-all">3</div>
                <div class="step-label text-[11px] font-medium text-zinc-400 leading-tight">Lifecycle</div>
            </div>
        </div>

        <!-- Content Panel -->
        <div class="flex-1 overflow-y-auto p-6 space-y-5">
            <!-- Step 1: General Details -->
            <div class="cora-step-content space-y-4" data-step="1">
                <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-3">General Specifications</h4>
                
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Equipment Name / Model *</label>
                    <input type="text" id="edit-gear-name" required placeholder="e.g. Sony Alpha a7 IV Cinema Camera" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Serial Number # *</label>
                        <input type="text" id="edit-gear-serial" required placeholder="SN-774921" class="w-full px-3 py-2 text-xs font-mono border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Category *</label>
                        <select id="edit-gear-category" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                            <option value="Camera">Camera</option>
                            <option value="Lens">Lens</option>
                            <option value="Lighting">Lighting</option>
                            <option value="Drone">Drone</option>
                            <option value="Audio">Audio</option>
                            <option value="Accessories">Accessories</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Included Accessories</label>
                    <textarea id="edit-gear-accessories" rows="4" placeholder="e.g. Charger, 2x Batteries, Lens Cap" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 resize-none"></textarea>
                </div>
            </div>

            <!-- Step 2: Financials -->
            <div class="cora-step-content space-y-4 hidden" data-step="2">
                <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-3">Acquisition & Financials</h4>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">CapEx Valuation (₹) *</label>
                        <input type="number" id="edit-gear-capex" required placeholder="245000" class="w-full px-3 py-2 text-xs font-mono border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Purchase Date</label>
                        <input type="date" id="edit-gear-date" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Condition Rating *</label>
                        <select id="edit-gear-condition" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                            <option value="Mint">Mint</option>
                            <option value="Excellent">Excellent</option>
                            <option value="Good">Good</option>
                            <option value="Needs Repair">Needs Repair</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Status *</label>
                        <select id="edit-gear-status" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                            <option value="Available">Available in Studio</option>
                            <option value="On Shoot">On Shoot</option>
                            <option value="In Repair">In Repair</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Storage Location</label>
                    <input type="text" id="edit-gear-storage-location" placeholder="e.g. Shelf A-3" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                </div>
            </div>

            <!-- Step 3: Media & Lifecycle -->
            <div class="cora-step-content space-y-4 hidden" data-step="3">
                <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-3">Lifecycle & Media Asset</h4>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Next Service Due</label>
                        <input type="date" id="edit-gear-next-service" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1">Insurance Expiry</label>
                        <input type="date" id="edit-gear-insurance" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Assigned Crew / Shoot</label>
                    <input type="text" id="edit-gear-assigned" placeholder="e.g. Unassigned (Studio Vault)" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 mb-1">Product Photo</label>
                    <div class="border border-zinc-200 rounded-xl bg-white overflow-hidden p-4 space-y-4">
                        <div id="edit-gear-upload-box" class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-lg bg-zinc-100 border border-zinc-250 flex items-center justify-center shrink-0 overflow-hidden relative">
                                <img id="edit-gear-image-preview" src="" class="w-full h-full object-cover hidden">
                                <svg id="edit-gear-image-placeholder" viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-450"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="text-[11px] font-bold text-zinc-900 leading-tight">Attach a visual asset:</div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" onclick="document.getElementById('edit-gear-image-file').click()" class="px-3 py-1.5 bg-zinc-950 text-white text-[10px] font-bold rounded-lg hover:bg-zinc-800 shadow-xs cursor-pointer flex items-center gap-1.5 border border-transparent">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                        Upload File
                                    </button>
                                    <button type="button" onclick="coraSelectMediaLibrary('edit-gear-image-preview', 'edit-gear-image-path')" class="px-3 py-1.5 bg-white text-zinc-700 text-[10px] font-bold rounded-lg hover:bg-zinc-50 cursor-pointer flex items-center gap-1.5 border border-zinc-200">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                        Media Library
                                    </button>
                                    <button type="button" onclick="coraStartWebcam('edit')" class="px-3 py-1.5 bg-white text-zinc-700 text-[10px] font-bold rounded-lg hover:bg-zinc-50 cursor-pointer flex items-center gap-1.5 border border-zinc-200">
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                        Access Camera
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="edit-gear-camera-container" class="hidden border border-zinc-200 rounded-xl overflow-hidden bg-black relative w-full aspect-video">
                            <video id="edit-gear-video" autoplay playsinline class="w-full h-full object-cover"></video>
                            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2 z-20">
                                <button type="button" onclick="coraCaptureWebcamPhoto('edit-gear-image-preview', 'edit-gear-image-path', 'edit')" class="px-4 py-1.5 bg-zinc-950 text-white text-xs font-bold rounded-lg hover:bg-zinc-800 shadow-sm cursor-pointer border border-zinc-700">Capture</button>
                                <button type="button" onclick="coraStopWebcam('edit')" class="px-4 py-1.5 bg-white text-zinc-800 text-xs font-bold rounded-lg hover:bg-zinc-100 shadow-sm cursor-pointer border border-zinc-250">Cancel</button>
                            </div>
                        </div>

                        <input type="file" id="edit-gear-image-file" onchange="coraUploadGearImage(this, 'edit-gear-image-preview', 'edit-gear-image-path')" accept="image/*" class="hidden">
                        <input type="hidden" id="edit-gear-image-path" value="">
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-4 border-t border-zinc-100 flex items-center justify-between bg-zinc-50/50 shrink-0">
            <button type="button" onclick="closeGearDrawers()" class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-50 cursor-pointer">Cancel</button>
            <div class="flex items-center gap-2">
                <button type="button" id="edit-gear-back-btn" onclick="setEditGearStep(editGearStep - 1)" class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-50 cursor-pointer hidden">Back</button>
                <button type="button" id="edit-gear-next-btn" onclick="if(validateCurrentStep('cora-edit-gear-drawer', editGearStep)) setEditGearStep(editGearStep + 1)" class="px-4.5 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 cursor-pointer shadow-xs">Next</button>
                <button type="submit" id="edit-gear-submit-btn" class="px-5 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 cursor-pointer shadow-xs hidden">Save Changes</button>
            </div>
        </div>
    </form>
</aside>

<!-- 2. SHOOT CHECKOUT & GEAR ASSIGNMENT DRAWER -->
<aside id="cora-checkout-gear-drawer" class="cora-drawer-sheet hidden fixed top-0 right-0 z-[10000] h-full w-[460px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                Check Out / Allocate Gear to Shoot
            </h3>
            <p class="text-[11px] text-zinc-500 mt-0.5">Assign equipment packages or kits to crew members and client shoots.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeGearDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-checkout-gear-form" onsubmit="coraSubmitCheckoutForm(event)" class="flex-1 overflow-y-auto p-6 space-y-4">
        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Select Equipment / Package Kit *</label>
            <select id="checkout-gear-select" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                <optgroup label="Studio Equipment Units">
                    <?php foreach ( $cora_studio_gear as $g ) : ?>
                        <option value="<?php echo esc_attr( $g['id'] ); ?>" data-type="gear" data-name="<?php echo esc_attr( $g['name'] ); ?>" data-serial="<?php echo esc_attr( $g['serial'] ?? $g['serial_no'] ?? '' ); ?>">
                            <?php echo esc_html( $g['name'] . ' (' . ($g['serial'] ?? $g['serial_no'] ?? '') . ')' ); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Bundled Gear Packages">
                    <?php foreach ( $cora_gear_kits as $k ) : ?>
                        <option value="<?php echo esc_attr( $k['id'] ); ?>" data-type="kit" data-name="<?php echo esc_attr( $k['name'] ); ?>" data-serial="KIT-PKG">
                            [KIT] <?php echo esc_html( $k['name'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Shoot Title / Project Name *</label>
            <input type="text" id="checkout-shoot-title" required placeholder="e.g. Oberoi Realty Commercial 4K" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Client Name *</label>
                <select id="checkout-client-name" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                    <option value="" disabled selected>Select Client...</option>
                    <?php 
                    $cora_existing_clients = get_option( 'cora_workspace_clients', array() );
                    if ( is_array( $cora_existing_clients ) ) :
                        foreach ( $cora_existing_clients as $client ) :
                            $client_name = $client['names'] ?? $client['name'] ?? '';
                            if ( ! empty( $client_name ) ) :
                                ?>
                                <option value="<?php echo esc_attr( $client_name ); ?>"><?php echo esc_html( $client_name ); ?></option>
                                <?php
                            endif;
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Assigned DoP / Operator *</label>
                <select id="checkout-dop-pilot" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                    <option value="" disabled selected>Select Crew / DoP...</option>
                    <?php 
                    $cora_all_users = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
                    if ( is_array( $cora_all_users ) ) :
                        foreach ( $cora_all_users as $u ) :
                            if ( ! empty( $u->display_name ) ) :
                                ?>
                                <option value="<?php echo esc_attr( $u->display_name ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                                <?php
                            endif;
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Checkout Date *</label>
                <input type="date" id="checkout-date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Return Due Date *</label>
                <input type="date" id="checkout-return-date" required value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Checkout / Field Notes</label>
            <textarea id="checkout-notes" rows="3" placeholder="Special shooting conditions, extra lens filters, battery counts..." class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950"></textarea>
        </div>

        <div class="pt-4 border-t border-zinc-100 flex items-center justify-end gap-2">
            <button type="button" onclick="closeGearDrawers()" class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-50 cursor-pointer">Cancel</button>
            <button type="submit" class="px-5 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 cursor-pointer shadow-xs">Confirm Checkout Allocation</button>
        </div>
    </form>
</aside>

<!-- 3. LOG REPAIR & MAINTENANCE EXPENSE DRAWER -->
<aside id="cora-log-maintenance-drawer" class="cora-drawer-sheet hidden fixed top-0 right-0 z-[10000] h-full w-[460px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                Log Maintenance & Service Expense
            </h3>
            <p class="text-[11px] text-zinc-500 mt-0.5">Record repair history and sync costs to financial ledger.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeGearDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-maintenance-form" onsubmit="coraSubmitMaintenanceForm(event)" class="flex-1 overflow-y-auto p-6 space-y-4">
        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Equipment Unit *</label>
            <select id="mnt-gear-select" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                <?php foreach ( $cora_studio_gear as $g ) : ?>
                    <option value="<?php echo esc_attr( $g['id'] ); ?>" data-name="<?php echo esc_attr( $g['name'] ); ?>">
                        <?php echo esc_html( $g['name'] . ' (' . ($g['serial'] ?? $g['serial_no'] ?? '') . ')' ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Repair / Service Type *</label>
            <input type="text" id="mnt-type" required placeholder="e.g. Sensor Calibration & Firmware Upgrade" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Service Date *</label>
                <input type="date" id="mnt-date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Repair Expense (₹) *</label>
                <input type="number" id="mnt-cost" required placeholder="12500" class="w-full px-3 py-2 text-xs font-mono border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Vendor / Repair Workshop *</label>
            <input type="text" id="mnt-vendor" required placeholder="e.g. RED Authorized Service India" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Service Notes / Work Performed</label>
            <textarea id="mnt-notes" rows="3" placeholder="Replaced optical low pass filter, cleaned sensor unit..." class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950"></textarea>
        </div>

        <!-- Automatic Financial Sync Notification -->
        <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-xl flex items-start gap-2.5">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600 shrink-0 mt-0.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <p class="text-[11px] text-zinc-600 leading-normal">
                <strong class="text-zinc-900">Financial Ledger Sync:</strong> This maintenance cost will automatically sync to Cora Studio Financial Ledger under CapEx & Operational Repair Expenses.
            </p>
        </div>

        <div class="pt-4 border-t border-zinc-100 flex items-center justify-end gap-2">
            <button type="button" onclick="closeGearDrawers()" class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-50 cursor-pointer">Cancel</button>
            <button type="submit" class="px-5 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 cursor-pointer shadow-xs">Log Expense & Sync Ledger</button>
        </div>
    </form>
</aside>

<!-- 4. VIEW REPAIR DETAILS DRAWER -->
<aside id="cora-view-repair-drawer" class="cora-drawer-sheet hidden fixed top-0 right-0 z-[10000] h-full w-[460px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                View Repair & Maintenance Details
            </h3>
            <p class="text-[11px] text-zinc-500 mt-0.5">Historical and active repair logs for this equipment unit.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeGearDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-6 space-y-6">
        <div>
            <div class="text-[10px] uppercase font-bold text-zinc-400 tracking-wider">Equipment Unit</div>
            <div id="view-repair-gear-name" class="text-sm font-bold text-zinc-950 mt-1">-</div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <div class="text-[10px] uppercase font-bold text-zinc-400 tracking-wider">Repair Expense</div>
                <div id="view-repair-cost" class="text-sm font-mono font-bold text-zinc-950 mt-1">₹0</div>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-zinc-400 tracking-wider">Service Date</div>
                <div id="view-repair-date" class="text-sm font-mono font-bold text-zinc-950 mt-1">-</div>
            </div>
        </div>

        <div>
            <div class="text-[10px] uppercase font-bold text-zinc-400 tracking-wider">Vendor / Repair Workshop</div>
            <div id="view-repair-vendor" class="text-sm font-semibold text-zinc-900 mt-1">-</div>
        </div>

        <div>
            <div class="text-[10px] uppercase font-bold text-zinc-400 tracking-wider">Service Notes / Work Performed</div>
            <div id="view-repair-notes" class="text-xs text-zinc-700 bg-zinc-50 border border-zinc-150 rounded-xl p-3.5 leading-relaxed whitespace-pre-wrap mt-1.5">-</div>
        </div>
    </div>

    <div class="p-5 border-t border-zinc-150 bg-zinc-50/50 flex items-center justify-end shrink-0">
        <button type="button" onclick="closeGearDrawers()" class="px-5 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 cursor-pointer shadow-xs">Close Details</button>
    </div>
</aside>

<!-- 4.1 CREATE GEAR KIT DRAWER -->
<aside id="cora-create-kit-drawer" class="cora-drawer-sheet hidden fixed top-0 right-0 z-[10000] h-full w-[500px] max-w-[95vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-950/50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-950 dark:text-white flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Create Custom Gear Kit
            </h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-450 mt-0.5">Bundle multiple inventory items into a single kit for quick allocation.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white cursor-pointer p-1" onclick="closeGearDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="cora-create-kit-form" onsubmit="coraSubmitCreateKitForm(event)" class="flex-1 flex flex-col overflow-hidden">
        <!-- Content Panel -->
        <div class="flex-1 overflow-y-auto p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Kit Bundle Name *</label>
                <input type="text" id="create-kit-name" required placeholder="e.g. Cinema Production Pro Bundle" class="w-full text-xs px-3.5 py-2.5 bg-white dark:bg-zinc-950 border border-zinc-250 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-xl focus:border-zinc-950 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-950 outline-none transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Category / Group *</label>
                    <select id="create-kit-category" class="w-full text-xs px-3 py-2.5 bg-white dark:bg-zinc-950 border border-zinc-250 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-xl focus:border-zinc-950 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-950 outline-none transition-all">
                        <option value="Cinema Production">Cinema Production</option>
                        <option value="Aerial Cinematography">Aerial Cinematography</option>
                        <option value="Lighting & Grip">Lighting & Grip</option>
                        <option value="Audio Recording">Audio Recording</option>
                        <option value="Custom Bundle">Custom Bundle</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Daily Rental Rate (₹) *</label>
                    <input type="number" id="create-kit-daily-rate" required placeholder="e.g. 12000" class="w-full text-xs px-3.5 py-2.5 bg-white dark:bg-zinc-950 border border-zinc-250 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-xl focus:border-zinc-950 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-950 outline-none transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Kit Description</label>
                <textarea id="create-kit-description" placeholder="Brief summary of what this kit includes and its main usage..." rows="3" class="w-full text-xs px-3.5 py-2.5 bg-white dark:bg-zinc-950 border border-zinc-250 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-xl focus:border-zinc-950 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-950 outline-none transition-all resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Select Included Gear Units *</label>
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden max-h-[220px] overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-950">
                    <?php if ( ! empty( $cora_studio_gear ) && is_array( $cora_studio_gear ) ) : ?>
                        <?php foreach ( $cora_studio_gear as $gear ) : ?>
                            <label class="flex items-center gap-3 p-3 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all select-none">
                                <input type="checkbox" name="kit_item_ids[]" value="<?php echo esc_attr( $gear['id'] ); ?>" class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-zinc-950 w-4 h-4 cursor-pointer">
                                <div class="text-xs">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $gear['name'] ); ?></div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-450 mt-0.5"><?php echo esc_html( $gear['category'] ); ?> · SN: <?php echo esc_html( $gear['serial_no'] ?? $gear['serial'] ?? '-' ); ?></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="p-4 text-center text-xs text-zinc-400 dark:text-zinc-500">No equipment items registered in the inventory yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="p-5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/50 flex items-center justify-end gap-2.5 shrink-0">
            <button type="button" onclick="closeGearDrawers()" class="px-4 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-bold rounded-xl cursor-pointer shadow-2xs">Cancel</button>
            <button type="submit" class="px-5 py-2 bg-zinc-950 dark:bg-white hover:bg-zinc-900 dark:hover:bg-zinc-100 text-white dark:text-zinc-950 text-xs font-bold rounded-xl cursor-pointer shadow-xs border border-transparent">Create Kit Bundle</button>
        </div>
    </form>
</aside>

<!-- 5. CUSTOM CONFIRMATION DELETE MODAL (No Browser Defaults) -->
<div id="cora-delete-confirm-modal" class="hidden fixed inset-0 z-55 flex items-center justify-center bg-zinc-950/40 backdrop-blur-xs p-4" style="z-index: 9999;">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-155">
        <div class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-full bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-450 border border-rose-100/50 dark:border-rose-900/30 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </span>
            <div>
                <h3 class="text-sm font-bold text-zinc-950 dark:text-zinc-50">Delete Inventory Item</h3>
                <p class="text-[11px] text-zinc-500 mt-0.5">Are you sure you want to delete this gear item from the inventory? This action is permanent.</p>
            </div>
        </div>
        
        <input type="hidden" id="cora-delete-target-id">

        <div class="flex items-center justify-end gap-2.5 pt-2">
            <button type="button" onclick="closeCoraDeleteModal()" class="px-4 py-2 bg-white border border-zinc-200 hover:bg-zinc-100 hover:border-zinc-300 text-zinc-800 text-xs font-bold rounded-xl transition-all cursor-pointer shadow-2xs">Cancel</button>
            <button type="button" onclick="executeCoraDeleteGearItem()" class="px-4.5 py-2 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-sm border border-transparent">Delete Item</button>
        </div>
    </div>
</div>

<!-- ═══ 6. INLINE DYNAMIC JAVASCRIPT ENGINE & REAL BACKEND AJAX INTEGRATION ═════════════════════════════════════ -->
<script>
// Nonce and localization bridge fallback
window.coraData = window.coraData || {};
Object.defineProperty(window.coraData, 'nonce', {
    get: function() {
        return (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '';
    },
    configurable: true
});

if (typeof window.ajaxurl === 'undefined') {
    Object.defineProperty(window, 'ajaxurl', {
        get: function() {
            return (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php';
        },
        configurable: true
    });
}

// Initial Repair Data for View Repair details
window.coraRepairData = <?php echo json_encode( $initial_repair_data ); ?>;
window.coraStudioGearList = <?php echo json_encode( $cora_studio_gear ); ?>;

// Standardized Sub-Tab Switching Functionality with Dynamic URL Persistence
window.coraSwitchEquipmentTab = function(tabId) {
    // Hide all tab contents
    var contents = document.querySelectorAll('.cora-eq-tab-content');
    contents.forEach(function(el) {
        el.classList.add('hidden');
    });

    // Reset sub-tab button states
    var buttons = document.querySelectorAll('.cora-eq-tab-btn');
    buttons.forEach(function(btn) {
        btn.classList.remove('active', 'border-zinc-950', 'dark:border-white', 'text-zinc-950', 'dark:text-white');
        btn.classList.add('border-transparent', 'text-zinc-450', 'dark:text-zinc-500');
    });

    // Show target panel & activate button
    var target = document.getElementById('cora-eq-tab-' + tabId);
    var targetBtn = document.getElementById('tab-btn-' + tabId);

    if (target) target.classList.remove('hidden');
    if (targetBtn) {
        targetBtn.classList.remove('border-transparent', 'text-zinc-450', 'dark:text-zinc-500');
        targetBtn.classList.add('active', 'border-zinc-950', 'dark:border-white', 'text-zinc-950', 'dark:text-white');
    }

    // Dynamic URL Update
    if (typeof URLSearchParams !== 'undefined' && window.history && window.history.replaceState) {
        var params = new URLSearchParams(window.location.search);
        params.set('eq_tab', tabId);
        var newUrl = window.location.pathname + '?' + params.toString() + window.location.hash;
        window.history.replaceState({ path: newUrl }, '', newUrl);
    } else {
        window.location.hash = 'eq_tab=' + tabId;
    }
};

// Auto-activate tab from URL query parameter or hash on page load
(function() {
    var activeTab = null;
    
    // 1. Try URL Query Parameter
    if (typeof URLSearchParams !== 'undefined') {
        var params = new URLSearchParams(window.location.search);
        activeTab = params.get('eq_tab');
    }
    
    // 2. Try URL Hash
    if (!activeTab && window.location.hash) {
        var hash = window.location.hash;
        if (hash.indexOf('eq_tab=') > -1) {
            activeTab = hash.split('eq_tab=')[1];
        } else if (hash.indexOf('#tab-') > -1) {
            activeTab = hash.split('#tab-')[1];
        } else if (hash.indexOf('#') > -1) {
            activeTab = hash.replace('#', '');
        }
    }
    
    // Validate if it is one of the available tabs
    var validTabs = ['registry', 'checkouts', 'maintenance', 'kits'];
    if (activeTab && validTabs.indexOf(activeTab) > -1) {
        setTimeout(function() {
            window.coraSwitchEquipmentTab(activeTab);
        }, 50);
    }
})();

// Global Helper to Close All Drawers
if (typeof window.coraCloseAllDrawers !== 'function') {
    window.coraCloseAllDrawers = function() {
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) backdrop.classList.add('hidden');

        var drawers = document.querySelectorAll('.cora-drawer-sheet');
        drawers.forEach(function(d) {
            d.classList.add('hidden');
            d.classList.add('collapsed');
        });
    };
}

// Open Drawer Functions
// Open Drawer Functions protected from overrides using Object.defineProperty
Object.defineProperty(window, 'openAddGearDrawer', {
    value: function() {
        if (typeof window.coraDebugLog === 'function') window.coraDebugLog('openAddGearDrawer triggered');
        if (typeof window.coraStopWebcam === 'function') window.coraStopWebcam('add');
        if (typeof window.coraStopWebcam === 'function') window.coraStopWebcam('edit');
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
        
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) {
            backdrop.classList.remove('hidden');
            backdrop.style.display = 'block';
            backdrop.style.pointerEvents = 'auto';
        }

        var form = document.getElementById('cora-add-gear-form');
        if (form) form.reset();

        var preview = document.getElementById('add-gear-image-preview');
        if (preview) {
            preview.src = '';
            preview.classList.add('hidden');
        }
        var placeholder = document.getElementById('add-gear-image-placeholder');
        if (placeholder) placeholder.classList.remove('hidden');

        var pathInput = document.getElementById('add-gear-image-path');
        if (pathInput) pathInput.value = '';

        var drawer = document.getElementById('cora-add-gear-drawer');
        if (typeof window.setAddGearStep === 'function') window.setAddGearStep(1);
        if (drawer) {
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Found add drawer. Initial classes: ' + drawer.className);
            drawer.classList.remove('hidden');
            drawer.classList.remove('collapsed');
            drawer.classList.remove('translate-x-full');
            drawer.classList.remove('pointer-events-none');
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Add drawer classes after open: ' + drawer.className);
        } else {
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Add drawer NOT found in DOM!');
        }
        jQuery('body').addClass('cora-drawer-open overflow-hidden');
    },
    writable: false,
    configurable: true
});

Object.defineProperty(window, 'openCheckoutGearDrawer', {
    value: function(presetGearName) {
        if (typeof window.coraDebugLog === 'function') window.coraDebugLog('openCheckoutGearDrawer triggered for: ' + presetGearName);
        if (typeof window.coraStopWebcam === 'function') window.coraStopWebcam('add');
        if (typeof window.coraStopWebcam === 'function') window.coraStopWebcam('edit');
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
        
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) {
            backdrop.classList.remove('hidden');
            backdrop.style.display = 'block';
            backdrop.style.pointerEvents = 'auto';
        }

        if (presetGearName) {
            var select = document.getElementById('checkout-gear-select');
            if (select) {
                for (var i = 0; i < select.options.length; i++) {
                    var optName = select.options[i].getAttribute('data-name') || '';
                    if (optName === presetGearName || optName.indexOf(presetGearName) !== -1 || select.options[i].value === presetGearName) {
                        select.options[i].selected = true;
                        break;
                    }
                }
            }
        }

        var drawer = document.getElementById('cora-checkout-gear-drawer');
        if (drawer) {
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Found checkout drawer. Initial classes: ' + drawer.className);
            drawer.classList.remove('hidden');
            drawer.classList.remove('collapsed');
            drawer.classList.remove('translate-x-full');
            drawer.classList.remove('pointer-events-none');
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Checkout drawer classes after open: ' + drawer.className);
        } else {
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Checkout drawer NOT found in DOM!');
        }
        jQuery('body').addClass('cora-drawer-open overflow-hidden');
    },
    writable: false,
    configurable: true
});

Object.defineProperty(window, 'openMaintenanceDrawer', {
    value: function(presetGearName) {
        if (typeof window.coraDebugLog === 'function') window.coraDebugLog('openMaintenanceDrawer triggered for: ' + presetGearName);
        if (typeof window.coraStopWebcam === 'function') window.coraStopWebcam('add');
        if (typeof window.coraStopWebcam === 'function') window.coraStopWebcam('edit');
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
        
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) {
            backdrop.classList.remove('hidden');
            backdrop.style.display = 'block';
            backdrop.style.pointerEvents = 'auto';
        }

        if (presetGearName) {
            var select = document.getElementById('mnt-gear-select');
            if (select) {
                for (var i = 0; i < select.options.length; i++) {
                    var optName = select.options[i].getAttribute('data-name') || '';
                    if (optName.indexOf(presetGearName) !== -1 || select.options[i].value === presetGearName) {
                        select.options[i].selected = true;
                        break;
                    }
                }
            }
        }

        var drawer = document.getElementById('cora-log-maintenance-drawer');
        if (drawer) {
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Found maintenance drawer. Initial classes: ' + drawer.className);
            drawer.classList.remove('hidden');
            drawer.classList.remove('collapsed');
            drawer.classList.remove('translate-x-full');
            drawer.classList.remove('pointer-events-none');
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Maintenance drawer classes after open: ' + drawer.className);
        } else {
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Maintenance drawer NOT found in DOM!');
        }
        jQuery('body').addClass('cora-drawer-open overflow-hidden');
    },
    writable: false,
    configurable: true
});

Object.defineProperty(window, 'openViewRepairDrawer', {
    value: function(gearId) {
        if (typeof window.coraDebugLog === 'function') window.coraDebugLog('openViewRepairDrawer triggered for: ' + gearId);
        if (typeof window.coraStopWebcam === 'function') window.coraStopWebcam('add');
        if (typeof window.coraStopWebcam === 'function') window.coraStopWebcam('edit');
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
        
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) {
            backdrop.classList.remove('hidden');
            backdrop.style.display = 'block';
            backdrop.style.pointerEvents = 'auto';
        }

        var drawer = document.getElementById('cora-view-repair-drawer');
        if (drawer) {
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Found repair details drawer. Initial classes: ' + drawer.className);
            var details = (window.coraRepairData && window.coraRepairData[gearId]) ? window.coraRepairData[gearId] : null;
            if (details) {
                document.getElementById('view-repair-gear-name').textContent = details.name || 'Equipment Unit';
                document.getElementById('view-repair-cost').textContent = '₹' + parseInt(details.cost || 0).toLocaleString();
                document.getElementById('view-repair-date').textContent = details.date || 'N/A';
                document.getElementById('view-repair-vendor').textContent = details.vendor || 'N/A';
                document.getElementById('view-repair-notes').textContent = details.notes || 'No notes recorded.';
            } else {
                if (gearId === 'gear_aputure_300d') {
                    document.getElementById('view-repair-gear-name').textContent = 'Aputure 300D II LED Light';
                    document.getElementById('view-repair-cost').textContent = '₹2,500';
                    document.getElementById('view-repair-date').textContent = '2025-02-11';
                    document.getElementById('view-repair-vendor').textContent = 'Light Source Delhi';
                    document.getElementById('view-repair-notes').textContent = 'Driver Issue';
                } else {
                    document.getElementById('view-repair-gear-name').textContent = 'Equipment Unit';
                    document.getElementById('view-repair-cost').textContent = '₹0';
                    document.getElementById('view-repair-date').textContent = 'N/A';
                    document.getElementById('view-repair-vendor').textContent = 'N/A';
                    document.getElementById('view-repair-notes').textContent = 'No details found.';
                }
            }
            drawer.classList.remove('hidden');
            drawer.classList.remove('collapsed');
            drawer.classList.remove('translate-x-full');
            drawer.classList.remove('pointer-events-none');
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Repair drawer classes after open: ' + drawer.className);
        } else {
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Repair drawer NOT found in DOM!');
        }
        jQuery('body').addClass('cora-drawer-open overflow-hidden');
    },
    writable: false,
    configurable: true
});

Object.defineProperty(window, 'openEditGearDrawer', {
    value: function(gearId) {
        if (typeof window.coraDebugLog === 'function') window.coraDebugLog('openEditGearDrawer triggered for: ' + gearId);
        if (typeof window.coraStopWebcam === 'function') window.coraStopWebcam('add');
        if (typeof window.coraStopWebcam === 'function') window.coraStopWebcam('edit');
        if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
        
        var backdrop = document.getElementById('cora-drawer-backdrop');
        if (backdrop) {
            backdrop.classList.remove('hidden');
            backdrop.style.display = 'block';
            backdrop.style.pointerEvents = 'auto';
        }

        var drawer = document.getElementById('cora-edit-gear-drawer');
        if (!drawer) {
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Edit drawer element NOT found in DOM!');
            return;
        }

        if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Found edit drawer. Initial classes: ' + drawer.className);
        var item = null;
        if (window.coraStudioGearList) {
            for (var i = 0; i < window.coraStudioGearList.length; i++) {
                if (window.coraStudioGearList[i].id === gearId) {
                    item = window.coraStudioGearList[i];
                    break;
                }
            }
        }

        if (item) {
            document.getElementById('edit-gear-id').value = item.id;
            document.getElementById('edit-gear-name').value = item.name || '';
            document.getElementById('edit-gear-serial').value = item.serial || item.serial_no || '';
            document.getElementById('edit-gear-category').value = item.category || 'Camera';
            document.getElementById('edit-gear-capex').value = item.capex || item.purchase_price || 0;
            document.getElementById('edit-gear-date').value = item.purchase_date || '';
            document.getElementById('edit-gear-condition').value = item.condition || 'Excellent';
            document.getElementById('edit-gear-status').value = item.status || 'Available';
            document.getElementById('edit-gear-storage-location').value = item.storage_location || '';
            document.getElementById('edit-gear-next-service').value = item.next_service_due || '';
            document.getElementById('edit-gear-insurance').value = item.insurance_expiry || '';
            document.getElementById('edit-gear-assigned').value = item.assigned || item.assigned_to || '';
            document.getElementById('edit-gear-accessories').value = item.accessories_included || '';

            var pathInput = document.getElementById('edit-gear-image-path');
            var preview = document.getElementById('edit-gear-image-preview');
            var placeholder = document.getElementById('edit-gear-image-placeholder');

            if (pathInput) pathInput.value = item.image || '';
            if (preview) {
                if (item.image) {
                    var imgSrc = item.image;
                    var imgUrl = '';
                    if (imgSrc.indexOf('data:') === 0 || imgSrc.indexOf('http') === 0) {
                        imgUrl = imgSrc;
                    } else if (imgSrc.indexOf('/') === 0) {
                        imgUrl = imgSrc;
                    } else if (imgSrc.indexOf('/') !== -1) {
                        imgUrl = '/wp-content/' + imgSrc;
                    } else {
                        imgUrl = '/wp-content/plugins/cora-workspace/assets/images/' + imgSrc;
                    }
                    preview.src = imgUrl;
                    preview.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                    if (placeholder) placeholder.classList.remove('hidden');
                }
            }

            drawer.classList.remove('hidden');
            drawer.classList.remove('collapsed');
            drawer.classList.remove('translate-x-full');
            drawer.classList.remove('pointer-events-none');
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Edit drawer classes after open: ' + drawer.className);
        } else {
            if (typeof window.coraDebugLog === 'function') window.coraDebugLog('Item data for ID ' + gearId + ' not found in coraStudioGearList!');
        }
        jQuery('body').addClass('cora-drawer-open overflow-hidden');
    },
    writable: false,
    configurable: true
});


// Multi-step form state management
window.addGearStep = 1;
window.editGearStep = 1;

window.validateCurrentStep = function(drawerId, step) {
    var stepEl = document.querySelector('#' + drawerId + ' .cora-step-content[data-step="' + step + '"]');
    if (!stepEl) return true;
    var inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
    for (var i = 0; i < inputs.length; i++) {
        if (!inputs[i].checkValidity()) {
            inputs[i].reportValidity();
            return false;
        }
    }
    return true;
};

window.setAddGearStep = function(step) {
    window.addGearStep = step;
    var drawer = document.getElementById('cora-add-gear-drawer');
    if (!drawer) return;

    // Show/hide content panels
    drawer.querySelectorAll('.cora-step-content').forEach(function(el) {
        var s = parseInt(el.getAttribute('data-step'));
        if (s === step) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });

    // Update left sidebar indicators
    drawer.querySelectorAll('.cora-step-indicator').forEach(function(el) {
        var s = parseInt(el.getAttribute('data-step'));
        var circle = el.querySelector('.step-circle');
        var label = el.querySelector('.step-label');
        if (s === step) {
            circle.className = 'step-circle w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[10px] font-bold shrink-0 transition-all';
            if (label) label.className = 'step-label text-[11px] font-bold text-zinc-950 leading-tight';
            circle.innerHTML = s;
        } else if (s < step) {
            circle.className = 'step-circle w-6 h-6 rounded-full bg-zinc-100 text-zinc-800 border border-zinc-200 flex items-center justify-center text-[10px] font-bold shrink-0 transition-all';
            if (label) label.className = 'step-label text-[11px] font-semibold text-zinc-700 leading-tight';
            circle.innerHTML = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        } else {
            circle.className = 'step-circle w-6 h-6 rounded-full border border-zinc-200 text-zinc-400 flex items-center justify-center text-[10px] font-medium shrink-0 bg-white transition-all';
            if (label) label.className = 'step-label text-[11px] font-medium text-zinc-400 leading-tight';
            circle.innerHTML = s;
        }
    });

    // Update buttons
    var backBtn = document.getElementById('add-gear-back-btn');
    var nextBtn = document.getElementById('add-gear-next-btn');
    var submitBtn = document.getElementById('add-gear-submit-btn');

    if (step === 1) {
        if (backBtn) backBtn.classList.add('hidden');
        if (nextBtn) nextBtn.classList.remove('hidden');
        if (submitBtn) submitBtn.classList.add('hidden');
    } else if (step === 2) {
        if (backBtn) backBtn.classList.remove('hidden');
        if (nextBtn) nextBtn.classList.remove('hidden');
        if (submitBtn) submitBtn.classList.add('hidden');
    } else if (step === 3) {
        if (backBtn) backBtn.classList.remove('hidden');
        if (nextBtn) nextBtn.classList.add('hidden');
        if (submitBtn) submitBtn.classList.remove('hidden');
    }
};

window.setEditGearStep = function(step) {
    window.editGearStep = step;
    var drawer = document.getElementById('cora-edit-gear-drawer');
    if (!drawer) return;

    // Show/hide content panels
    drawer.querySelectorAll('.cora-step-content').forEach(function(el) {
        var s = parseInt(el.getAttribute('data-step'));
        if (s === step) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });

    // Update left sidebar indicators
    drawer.querySelectorAll('.cora-step-indicator').forEach(function(el) {
        var s = parseInt(el.getAttribute('data-step'));
        var circle = el.querySelector('.step-circle');
        var label = el.querySelector('.step-label');
        if (s === step) {
            circle.className = 'step-circle w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center text-[10px] font-bold shrink-0 transition-all';
            if (label) label.className = 'step-label text-[11px] font-bold text-zinc-950 leading-tight';
            circle.innerHTML = s;
        } else if (s < step) {
            circle.className = 'step-circle w-6 h-6 rounded-full bg-zinc-100 text-zinc-800 border border-zinc-200 flex items-center justify-center text-[10px] font-bold shrink-0 transition-all';
            if (label) label.className = 'step-label text-[11px] font-semibold text-zinc-700 leading-tight';
            circle.innerHTML = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        } else {
            circle.className = 'step-circle w-6 h-6 rounded-full border border-zinc-200 text-zinc-400 flex items-center justify-center text-[10px] font-medium shrink-0 bg-white transition-all';
            if (label) label.className = 'step-label text-[11px] font-medium text-zinc-400 leading-tight';
            circle.innerHTML = s;
        }
    });

    // Update buttons
    var backBtn = document.getElementById('edit-gear-back-btn');
    var nextBtn = document.getElementById('edit-gear-next-btn');
    var submitBtn = document.getElementById('edit-gear-submit-btn');

    if (step === 1) {
        if (backBtn) backBtn.classList.add('hidden');
        if (nextBtn) nextBtn.classList.remove('hidden');
        if (submitBtn) submitBtn.classList.add('hidden');
    } else if (step === 2) {
        if (backBtn) backBtn.classList.remove('hidden');
        if (nextBtn) nextBtn.classList.remove('hidden');
        if (submitBtn) submitBtn.classList.add('hidden');
    } else if (step === 3) {
        if (backBtn) backBtn.classList.remove('hidden');
        if (nextBtn) nextBtn.classList.add('hidden');
        if (submitBtn) submitBtn.classList.remove('hidden');
    }
};


// WordPress Media Library Selector
window.coraSelectMediaLibrary = function(previewId, pathId) {
    if (typeof wp !== 'undefined' && wp.media) {
        var file_frame = wp.media({
            title: 'Select or Upload Product Photo',
            button: {
                text: 'Use this photo'
            },
            multiple: false
        });

        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            var preview = document.getElementById(previewId);
            var pathInput = document.getElementById(pathId);
            var placeholderId = previewId.replace('-preview', '-placeholder');
            var placeholder = document.getElementById(placeholderId);

            if (preview) {
                preview.src = attachment.url;
                preview.classList.remove('hidden');
            }
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
            if (pathInput) {
                var relativePath = attachment.url;
                var contentIndex = attachment.url.indexOf('/wp-content/');
                if (contentIndex !== -1) {
                    relativePath = attachment.url.substring(contentIndex);
                }
                pathInput.value = relativePath;
            }
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Image selected from library.', 'success');
            }
        });

        file_frame.open();
    } else {
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('WordPress Media Library is not enqueued.', 'error');
        }
    }
};

// HTML5 Webcam Capture Integration
window.coraWebcamStream = null;

window.coraStartWebcam = function(prefix) {
    var container = document.getElementById(prefix + '-gear-camera-container');
    var video = document.getElementById(prefix + '-gear-video');
    var uploadBox = document.getElementById(prefix + '-gear-upload-box');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Camera access is not supported by your browser or requires a secure HTTPS connection.', 'error');
        }
        return;
    }

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function(stream) {
            window.coraWebcamStream = stream;
            if (video) {
                video.srcObject = stream;
                video.play();
            }
            if (container) container.classList.remove('hidden');
            if (uploadBox) uploadBox.classList.add('hidden');
        })
        .catch(function(err) {
            console.error('Camera access error:', err);
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Could not access camera. Please check permissions.', 'error');
            }
        });
};

window.coraStopWebcam = function(prefix) {
    var container = document.getElementById(prefix + '-gear-camera-container');
    var uploadBox = document.getElementById(prefix + '-gear-upload-box');
    var video = document.getElementById(prefix + '-gear-video');

    if (window.coraWebcamStream) {
        window.coraWebcamStream.getTracks().forEach(function(track) {
            track.stop();
        });
        window.coraWebcamStream = null;
    }
    if (video) video.srcObject = null;
    if (container) container.classList.add('hidden');
    if (uploadBox) uploadBox.classList.remove('hidden');
};

window.coraCaptureWebcamPhoto = function(previewId, pathId, prefix) {
    var video = document.getElementById(prefix + '-gear-video');
    var canvas = document.getElementById('cora-webcam-canvas');
    if (!canvas) {
        canvas = document.createElement('canvas');
        canvas.id = 'cora-webcam-canvas';
        canvas.width = 640;
        canvas.height = 480;
        canvas.className = 'hidden';
        document.body.appendChild(canvas);
    }

    if (video && canvas) {
        var ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        var dataUrl = canvas.toDataURL('image/jpeg');

        var preview = document.getElementById(previewId);
        var pathInput = document.getElementById(pathId);
        var placeholderId = previewId.replace('-preview', '-placeholder');
        var placeholder = document.getElementById(placeholderId);

        if (preview) {
            preview.src = dataUrl;
            preview.classList.remove('hidden');
        }
        if (placeholder) {
            placeholder.classList.add('hidden');
        }
        if (pathInput) {
            pathInput.value = dataUrl;
        }

        window.coraStopWebcam(prefix);
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Photo captured successfully.', 'success');
        }
    }
};

window.closeGearDrawers = function() {
    window.coraStopWebcam('add');
    window.coraStopWebcam('edit');
    window.coraCloseAllDrawers();
};

// Search & Filter Gear Table
window.coraFilterGearTable = function() {
    var search = document.getElementById('gear-search-input').value.toLowerCase();
    var category = document.getElementById('gear-category-filter').value;
    var rows = document.querySelectorAll('.gear-table-row');

    rows.forEach(function(row) {
        var name = row.querySelector('.gear-item-name').textContent.toLowerCase();
        var serial = row.querySelector('.gear-item-serial').textContent.toLowerCase();
        var cat = row.querySelector('.gear-item-category').textContent.trim();

        var matchesSearch = !search || name.indexOf(search) !== -1 || serial.indexOf(search) !== -1;
        var matchesCategory = !category || cat.indexOf(category) !== -1;

        row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
    });
};

// Live Dynamic Form Handlers with Real AJAX Submission
// Upload Gear Image via AJAX
window.coraUploadGearImage = function(input, previewId, pathId) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    var formData = new FormData();
    formData.append('action', 'cora_ajax_upload_gear_image');
    formData.append('security', (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : '');
    formData.append('gear_image', file);

    var preview = document.getElementById(previewId);
    var pathInput = document.getElementById(pathId);

    if (typeof jQuery !== 'undefined') {
        jQuery.ajax({
            url: (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success && res.data) {
                    if (preview) {
                        preview.src = res.data.url;
                        preview.classList.remove('hidden');
                    }
                    var placeholderId = previewId.replace('-preview', '-placeholder');
                    var placeholder = document.getElementById(placeholderId);
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                    if (pathInput) {
                        pathInput.value = res.data.relative_path;
                    }
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast('Image uploaded successfully.', 'success');
                    }
                } else {
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast(res.data.message || 'Upload failed.', 'error');
                    }
                }
            },
            error: function() {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast('Error uploading image.', 'error');
                }
            }
        });
    }
};

// Live Dynamic Form Handlers with Real AJAX Submission
window.coraSubmitAddGearForm = function(e) {
    e.preventDefault();
    var name = document.getElementById('add-gear-name').value.trim();
    var serial = document.getElementById('add-gear-serial').value.trim();
    var category = document.getElementById('add-gear-category').value;
    var capex = document.getElementById('add-gear-capex').value;
    var date = document.getElementById('add-gear-date').value;
    var condition = document.getElementById('add-gear-condition').value;
    var status = document.getElementById('add-gear-status').value;
    var locationVal = document.getElementById('add-gear-storage-location').value.trim();
    var nextService = document.getElementById('add-gear-next-service').value;
    var insurance = document.getElementById('add-gear-insurance').value;
    var assigned = document.getElementById('add-gear-assigned').value.trim() || 'Unassigned (Studio Vault)';
    var accessories = document.getElementById('add-gear-accessories').value.trim();
    var image = document.getElementById('add-gear-image-path').value;

    if (!name || !serial) return;

    var payload = {
        action: 'cora_ajax_save_gear_item',
        security: (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : '',
        name: name,
        serial_no: serial,
        category: category,
        purchase_price: capex,
        purchase_date: date,
        current_value: capex,
        condition: condition,
        status: status,
        assigned_to: assigned,
        storage_location: locationVal,
        next_service_due: nextService,
        insurance_expiry: insurance,
        accessories_included: accessories,
        image: image
    };

    if (typeof jQuery !== 'undefined') {
        jQuery.post((typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), payload, function(res) {
            console.log('AJAX save gear response:', res);
            location.reload();
        });
    } else {
        location.reload();
    }
};

// Submit Edit Gear Form
window.coraSubmitEditGearForm = function(e) {
    e.preventDefault();
    var id = document.getElementById('edit-gear-id').value;
    var name = document.getElementById('edit-gear-name').value.trim();
    var serial = document.getElementById('edit-gear-serial').value.trim();
    var category = document.getElementById('edit-gear-category').value;
    var capex = document.getElementById('edit-gear-capex').value;
    var date = document.getElementById('edit-gear-date').value;
    var condition = document.getElementById('edit-gear-condition').value;
    var status = document.getElementById('edit-gear-status').value;
    var locationVal = document.getElementById('edit-gear-storage-location').value.trim();
    var nextService = document.getElementById('edit-gear-next-service').value;
    var insurance = document.getElementById('edit-gear-insurance').value;
    var assigned = document.getElementById('edit-gear-assigned').value.trim() || 'Unassigned (Studio Vault)';
    var accessories = document.getElementById('edit-gear-accessories').value.trim();
    var image = document.getElementById('edit-gear-image-path').value;

    if (!id || !name || !serial) return;

    var payload = {
        action: 'cora_ajax_save_gear_item',
        security: (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : '',
        gear_id: id,
        name: name,
        serial_no: serial,
        category: category,
        purchase_price: capex,
        purchase_date: date,
        current_value: capex,
        condition: condition,
        status: status,
        assigned_to: assigned,
        storage_location: locationVal,
        next_service_due: nextService,
        insurance_expiry: insurance,
        accessories_included: accessories,
        image: image
    };

    if (typeof jQuery !== 'undefined') {
        jQuery.post((typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), payload, function(res) {
            console.log('AJAX save gear response:', res);
            location.reload();
        });
    } else {
        location.reload();
    }
};

// Handle Checkout Submission
window.coraSubmitCheckoutForm = function(e) {
    e.preventDefault();
    var gearSelect = document.getElementById('checkout-gear-select');
    var selectedOpt = gearSelect.options[gearSelect.selectedIndex];
    var val = gearSelect.value;
    var type = selectedOpt.getAttribute('data-type');
    var gearName = selectedOpt.getAttribute('data-name');
    var serial = selectedOpt.getAttribute('data-serial') || 'SN-ALLOC';
    
    var shootTitle = document.getElementById('checkout-shoot-title').value.trim();
    var client = document.getElementById('checkout-client-name').value.trim();
    var dop = document.getElementById('checkout-dop-pilot').value.trim();
    var checkDate = document.getElementById('checkout-date').value;
    var returnDate = document.getElementById('checkout-return-date').value;

    if (!shootTitle || !dop) return;

    var payload = {
        action: 'cora_ajax_checkout_gear',
        security: (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : '',
        gear_id: type === 'gear' ? val : '',
        kit_id: type === 'kit' ? val : '',
        assigned_to: dop,
        shoot_title: shootTitle,
        client_name: client,
        checkout_date: checkDate,
        expected_return: returnDate
    };

    if (typeof jQuery !== 'undefined') {
        jQuery.post((typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), payload, function(res) {
            console.log('AJAX checkout response:', res);
            location.reload();
        });
    } else {
        location.reload();
    }
};

// Handle Maintenance Logging Submission
window.coraSubmitMaintenanceForm = function(e) {
    e.preventDefault();
    var gearSelect = document.getElementById('mnt-gear-select');
    var gearId = gearSelect.value;
    var gearName = gearSelect.options[gearSelect.selectedIndex].getAttribute('data-name');
    var type = document.getElementById('mnt-type').value.trim();
    var date = document.getElementById('mnt-date').value;
    var cost = document.getElementById('mnt-cost').value;
    var vendor = document.getElementById('mnt-vendor').value.trim();
    var notes = document.getElementById('mnt-notes').value.trim();

    if (!type || !cost) return;

    var payload = {
        action: 'cora_ajax_log_gear_maintenance',
        security: (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : '',
        gear_id: gearId,
        gear_name: gearName,
        maintenance_type: type,
        serviced_date: date,
        cost: cost,
        vendor: vendor,
        notes: notes
    };

    if (typeof jQuery !== 'undefined') {
        jQuery.post((typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), payload, function(res) {
            console.log('AJAX log maintenance response:', res);
            location.reload();
        });
    } else {
        location.reload();
    }
};

// Handle Gear Return CTA
window.coraReturnCheckoutItem = function(chkId, gearName, gearId) {
    var payload = {
        action: 'cora_ajax_return_gear',
        security: (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : '',
        checkout_id: chkId,
        gear_id: gearId
    };

    if (typeof jQuery !== 'undefined') {
        jQuery.post((typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), payload, function(res) {
            console.log('AJAX return gear response:', res);
            location.reload();
        });
    } else {
        location.reload();
    }
};

// Handle Delete Gear Item (Opens Custom Modal instead of confirm)
window.coraDeleteGearItem = function(gearId) {
    var modal = document.getElementById('cora-delete-confirm-modal');
    var targetInput = document.getElementById('cora-delete-target-id');
    if (modal && targetInput) {
        targetInput.value = gearId;
        modal.classList.remove('hidden');
    }
};

window.closeCoraDeleteModal = function() {
    var modal = document.getElementById('cora-delete-confirm-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

window.executeCoraDeleteGearItem = function() {
    var gearId = document.getElementById('cora-delete-target-id').value;
    if (!gearId) return;

    var payload = {
        action: 'cora_ajax_delete_studio_gear',
        security: (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : '',
        gear_id: gearId
    };

    if (typeof jQuery !== 'undefined') {
        jQuery.post((typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), payload, function(res) {
            console.log('AJAX delete response:', res);
            closeCoraDeleteModal();
            location.reload();
        });
    } else {
        closeCoraDeleteModal();
        location.reload();
    }
};

// Handle Create Custom Gear Kit Drawer Actions
window.openCreateKitDrawer = function() {
    if (typeof window.coraCloseAllDrawers === 'function') {
        window.coraCloseAllDrawers();
    }
    var backdrop = document.getElementById('cora-drawer-backdrop');
    if (backdrop) {
        backdrop.classList.remove('hidden');
        backdrop.style.display = 'block';
        backdrop.style.pointerEvents = 'auto';
    }
    
    var form = document.getElementById('cora-create-kit-form');
    if (form) {
        form.reset();
    }

    var drawer = document.getElementById('cora-create-kit-drawer');
    if (drawer) {
        drawer.classList.remove('hidden');
        drawer.classList.remove('collapsed');
        drawer.classList.remove('translate-x-full');
        drawer.classList.remove('pointer-events-none');
    }
    jQuery('body').addClass('cora-drawer-open overflow-hidden');
};

window.coraSubmitCreateKitForm = function(e) {
    e.preventDefault();
    var name = document.getElementById('create-kit-name').value.trim();
    var category = document.getElementById('create-kit-category').value;
    var dailyRate = document.getElementById('create-kit-daily-rate').value.trim();
    var description = document.getElementById('create-kit-description').value.trim();

    var checkedItems = [];
    var checkboxes = document.querySelectorAll('input[name="kit_item_ids[]"]:checked');
    checkboxes.forEach(function(cb) {
        checkedItems.push(cb.value);
    });

    if (!name) {
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Kit name is required.', 'error');
        }
        return;
    }

    var payload = {
        action: 'cora_save_gear_kit',
        security: (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : '',
        kit_name: name,
        category: category,
        daily_rate: dailyRate,
        description: description,
        item_ids: JSON.stringify(checkedItems)
    };

    if (typeof jQuery !== 'undefined') {
        jQuery.post((typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), payload, function(res) {
            console.log('AJAX save kit response:', res);
            if (res.success) {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast('Gear kit created successfully.', 'success');
                }
                location.reload();
            } else {
                var msg = res.data && res.data.message ? res.data.message : 'Failed to create kit.';
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(msg, 'error');
                }
            }
        });
    } else {
        location.reload();
    }
};

// Toggle Row Actions Dropdown Menu
window.coraToggleRowActions = function(e, gearId) {
    e.stopPropagation();
    var dropdown = document.getElementById('cora-row-actions-' + gearId);
    var isHidden = dropdown.classList.contains('hidden');
    
    // Hide all first
    window.coraHideAllDropdowns();
    
    if (isHidden && dropdown) {
        dropdown.classList.remove('hidden');
    }
};

window.coraHideAllDropdowns = function() {
    var dropdowns = document.querySelectorAll('.cora-row-actions-dropdown');
    dropdowns.forEach(function(d) {
        d.classList.add('hidden');
    });
};

// Document click listener to close dropdowns
document.addEventListener('click', function() {
    window.coraHideAllDropdowns();
});
</script>
