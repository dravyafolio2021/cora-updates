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
    // Check if the specific serial SN-774921 exists, if not we must re-init
    $has_target_serial = false;
    foreach ( $cora_studio_gear as $item ) {
        $serial_val = $item['serial'] ?? $item['serial_no'] ?? '';
        if ( $serial_val === 'SN-774921' ) {
            $has_target_serial = true;
            break;
        }
    }
    if ( ! $has_target_serial ) {
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
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-zinc-200">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">● Camera Equipment & Gear Inventory</h1>
            </div>
            <p class="text-xs font-medium text-zinc-500 mt-1">Manage studio camera gear assets, shoot checkouts, crew allocations, and financial maintenance costs.</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <button onclick="openCheckoutGearDrawer()" class="px-4 py-2.5 bg-white border border-zinc-200 hover:bg-zinc-100 hover:border-zinc-300 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                Check Out Gear
            </button>
            <button onclick="openMaintenanceDrawer()" class="px-4 py-2.5 bg-white border border-zinc-200 hover:bg-zinc-100 hover:border-zinc-300 text-zinc-800 text-xs font-bold rounded-xl transition-all shadow-2xs flex items-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                Log Repair & Cost
            </button>
            <!-- Primary Action CTA -->
            <button onclick="openAddGearDrawer()" class="px-4.5 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + Register New Gear
            </button>
        </div>
    </header>

    <!-- ═══ 2. MONOCHROMATIC 4-KPI METRIC STAT CARDS ═════════════════════════════════ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <!-- 1. Total Asset Valuation -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[110px]">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest">Total Asset Valuation</span>
                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded text-zinc-650 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700/50">CapEx</span>
            </div>
            <div class="mt-2.5">
                <span id="kpi-total-val" class="text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight">₹<?php echo number_format( $total_capex_valuation ); ?></span>
                <span class="text-[10px] text-zinc-400 dark:text-zinc-500 block mt-1 font-medium">Total acquisition cost</span>
            </div>
        </div>

        <!-- 2. Available in Studio -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[110px]">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest">Available in Studio</span>
                <span class="inline-flex items-center gap-1.5 text-[9px] font-extrabold uppercase px-2 py-0.5 rounded text-emerald-700 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-950/20 border border-emerald-100/50 dark:border-emerald-900/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ready
                </span>
            </div>
            <div class="mt-2.5">
                <span id="kpi-avail-count" class="text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight"><?php echo $available_count; ?> <span class="text-sm font-semibold text-zinc-450">Items</span></span>
                <span class="text-[10px] text-zinc-400 dark:text-zinc-500 block mt-1 font-medium">Ready in studio vault</span>
            </div>
        </div>

        <!-- 3. Checked Out on Shoots -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[110px]">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-455 dark:text-zinc-500 uppercase tracking-widest">Checked Out</span>
                <span class="inline-flex items-center gap-1.5 text-[9px] font-extrabold uppercase px-2 py-0.5 rounded text-amber-700 bg-amber-50 dark:text-amber-400 dark:bg-amber-950/20 border border-amber-100/50 dark:border-amber-900/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> On Field
                </span>
            </div>
            <div class="mt-2.5">
                <span id="kpi-checkout-count" class="text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight"><?php echo $checked_out_count; ?> <span class="text-sm font-semibold text-zinc-455">Allocated</span></span>
                <span class="text-[10px] text-zinc-450 dark:text-zinc-500 block mt-1 font-medium">Active shoot checkouts</span>
            </div>
        </div>

        <!-- 4. Under Maintenance / Servicing -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs flex flex-col justify-between min-h-[110px]">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-450 dark:text-zinc-500 uppercase tracking-widest">In Repair</span>
                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded text-zinc-650 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700/50">OpEx</span>
            </div>
            <div class="mt-2.5">
                <span id="kpi-maint-count" class="text-2xl font-extrabold text-zinc-950 dark:text-zinc-50 tracking-tight"><?php echo $maintenance_count; ?> <span class="text-sm font-semibold text-zinc-450">Item<?php echo $maintenance_count === 1 ? '' : 's'; ?></span></span>
                <span class="text-[10px] text-zinc-400 dark:text-zinc-500 block mt-1 font-medium">Servicing Cost: ₹<?php echo number_format( $total_repair_expense ); ?></span>
            </div>
        </div>
    </div>

    <!-- ═══ 3. STANDARDIZED PLATFORM SUB-TAB BAR ═════════════════════════════════ -->
    <div class="cora-sub-tabs border-b border-zinc-200 flex gap-6 text-xs font-bold text-zinc-550 select-none pb-0.5">
        <button id="tab-btn-registry" onclick="coraSwitchEquipmentTab('registry')" class="cora-eq-tab-btn active pb-2.5 border-b-2 border-zinc-950 text-zinc-950 cursor-pointer flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
            Gear Registry <span id="cnt-tab-registry" class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-350 px-2 py-0.5 rounded-full border border-zinc-200/40 dark:border-zinc-700/40 font-mono"><?php echo count( $cora_studio_gear ); ?></span>
        </button>

        <button id="tab-btn-checkouts" onclick="coraSwitchEquipmentTab('checkouts')" class="cora-eq-tab-btn pb-2.5 border-b-2 border-transparent hover:text-zinc-900 text-zinc-500 cursor-pointer flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            Shoot Checkouts <span id="cnt-tab-checkouts" class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-350 px-2 py-0.5 rounded-full border border-zinc-200/40 dark:border-zinc-700/40 font-mono"><?php echo count( $cora_gear_checkouts ); ?></span>
        </button>

        <button id="tab-btn-maintenance" onclick="coraSwitchEquipmentTab('maintenance')" class="cora-eq-tab-btn pb-2.5 border-b-2 border-transparent hover:text-zinc-900 text-zinc-500 cursor-pointer flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
            Maintenance & Financial Ledger <span id="cnt-tab-maintenance" class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-650 dark:text-zinc-350 px-2 py-0.5 rounded-full border border-zinc-200/40 dark:border-zinc-700/40 font-mono"><?php echo count( $cora_gear_maintenance ); ?></span>
        </button>

        <button id="tab-btn-kits" onclick="coraSwitchEquipmentTab('kits')" class="cora-eq-tab-btn pb-2.5 border-b-2 border-transparent hover:text-zinc-900 text-zinc-500 cursor-pointer flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            Studio Gear Kits <span id="cnt-tab-kits" class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-650 dark:text-zinc-350 px-2 py-0.5 rounded-full border border-zinc-200/40 dark:border-zinc-700/40 font-mono"><?php echo count( $cora_gear_kits ); ?></span>
        </button>
    </div>

    <!-- ═══ 4. SUB-TAB PANELS CONTAINER ═════════════════════════════════════════════ -->
    
    <!-- SUB-TAB 1: GEAR REGISTRY -->
    <div id="cora-eq-tab-registry" class="cora-eq-tab-content space-y-4">
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-5">
            <div class="flex items-center justify-between flex-wrap gap-4 pb-4 border-b border-zinc-100">
                <div>
                    <h3 class="text-sm font-bold text-zinc-950">Master Studio Camera & Equipment Registry</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Asset serial tracking, condition grading, CapEx valuation and crew assignments.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" id="gear-search-input" onkeyup="coraFilterGearTable()" placeholder="Search gear or serial #..." class="pl-8 pr-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-900 focus:outline-none focus:border-zinc-400 w-60">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <select id="gear-category-filter" onchange="coraFilterGearTable()" class="px-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-medium text-zinc-700 focus:outline-none cursor-pointer">
                        <option value="">All Categories</option>
                        <option value="Camera">Camera</option>
                        <option value="Lens">Lens</option>
                        <option value="Lighting">Lighting</option>
                        <option value="Drone">Drone</option>
                        <option value="Audio">Audio</option>
                        <option value="Accessories">Accessories</option>
                    </select>
                </div>
            </div>

            <div class="border border-zinc-200/80 rounded-xl overflow-hidden shadow-2xs">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50/60 border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                            <th class="p-3.5">Equipment Unit</th>
                            <th class="p-3.5">Serial No #</th>
                            <th class="p-3.5">Category</th>
                            <th class="p-3.5">Condition</th>
                            <th class="p-3.5">CapEx Valuation</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Assigned Crew / Shoot</th>
                            <th class="p-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cora-gear-tbody" class="divide-y divide-zinc-150 bg-white">
                        <?php foreach ( $cora_studio_gear as $gear ) : 
                            $status = $gear['status'] ?? 'Available';
                            $status_badge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available</span>';
                            if ( $status === 'In Use' || $status === 'On Shoot' ) {
                                $status_badge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> On Shoot</span>';
                            } elseif ( $status === 'Maintenance' || $status === 'In Repair' ) {
                                $status_badge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> In Repair</span>';
                            }

                            $category = $gear['category'] ?? 'Camera';
                            $cat_icon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>';
                            if ( $category === 'Lens' ) {
                                $cat_icon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>';
                            } elseif ( $category === 'Lighting' ) {
                                $cat_icon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line></svg>';
                            } elseif ( $category === 'Drone' ) {
                                $cat_icon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>';
                            } elseif ( $category === 'Audio' ) {
                                $cat_icon = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line></svg>';
                            }

                            $img_html = '';
                            if ( ! empty( $gear['image'] ) ) {
                                $img_url = '/wp-content/plugins/cora-workspace/assets/images/' . $gear['image'];
                                $img_html = '<img src="' . esc_url( $img_url ) . '" class="w-8 h-8 rounded-lg object-cover shrink-0 border border-zinc-200" alt="' . esc_attr( $gear['name'] ) . '">';
                            } else {
                                $img_html = '<div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-800 border border-zinc-200 flex items-center justify-center shrink-0">' . $cat_icon . '</div>';
                            }
                        ?>
                        <tr id="gear-row-<?php echo esc_attr( $gear['id'] ); ?>" class="gear-table-row hover:bg-zinc-50/60 transition-colors">
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    <?php echo $img_html; ?>
                                    <div>
                                        <div class="font-bold text-zinc-950 gear-item-name text-xs"><?php echo esc_html( $gear['name'] ); ?></div>
                                        <div class="text-[10px] text-zinc-400 font-normal">Purchased: <?php echo esc_html( $gear['purchase_date'] ?? 'N/A' ); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5 font-mono text-xs font-semibold text-zinc-700 gear-item-serial">
                                <?php echo esc_html( $gear['serial'] ?? $gear['serial_no'] ?? '' ); ?>
                            </td>
                            <td class="p-3.5 gear-item-category">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-zinc-100 border border-zinc-200 text-zinc-800">
                                    <?php echo esc_html( $gear['category'] ); ?>
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="text-xs text-zinc-600 font-medium">
                                    <?php echo esc_html( $gear['condition'] ); ?>
                                </span>
                            </td>
                            <td class="p-3.5 font-mono font-bold text-zinc-950 text-xs">
                                ₹<?php echo number_format( floatval( $gear['capex'] ?? $gear['purchase_price'] ?? 0 ) ); ?>
                            </td>
                            <td class="p-3.5">
                                <?php echo $status_badge; ?>
                            </td>
                            <td class="p-3.5 text-zinc-600 font-medium text-xs">
                                <?php echo esc_html( $gear['assigned'] ?? $gear['assigned_to'] ?? '' ); ?>
                            </td>
                            <td class="p-3.5 text-right space-x-1.5 whitespace-nowrap">
                                <?php if ( $status === 'In Repair' || $status === 'Maintenance' ) : ?>
                                    <button onclick="openViewRepairDrawer('<?php echo esc_attr( $gear['id'] ); ?>')" title="View repair details" class="px-3 py-1 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-700 rounded-lg text-xs font-semibold transition-all cursor-pointer">
                                        View Repair
                                    </button>
                                <?php else : ?>
                                    <button onclick="openCheckoutGearDrawer('<?php echo esc_attr( $gear['name'] ); ?>')" title="Check out gear to shoot" class="px-3 py-1 bg-zinc-950 text-white hover:bg-zinc-800 rounded-lg text-xs font-semibold transition-all cursor-pointer">
                                        Check Out
                                    </button>
                                    <button onclick="openMaintenanceDrawer('<?php echo esc_attr( $gear['name'] ); ?>')" title="Log repair or maintenance" class="px-3 py-1 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-700 rounded-lg text-xs font-semibold transition-all cursor-pointer">
                                        Log Repair
                                    </button>
                                <?php endif; ?>
                                <button onclick="coraDeleteGearItem('<?php echo esc_attr( $gear['id'] ); ?>')" title="Delete gear item" class="w-7 h-7 inline-flex items-center justify-center border border-zinc-200 rounded-lg text-zinc-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 transition-all cursor-pointer">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 2: SHOOT CHECKOUTS & ALLOCATIONS -->
    <div id="cora-eq-tab-checkouts" class="cora-eq-tab-content space-y-4 hidden">
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-5">
            <div class="flex items-center justify-between flex-wrap gap-4 pb-4 border-b border-zinc-100">
                <div>
                    <h3 class="text-sm font-bold text-zinc-950">Active & Historic Shoot Gear Checkouts</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Linking camera packages and kits directly to active shoots, client contracts & field operators.</p>
                </div>
                <button onclick="openCheckoutGearDrawer()" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer shadow-xs">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    + New Shoot Checkout
                </button>
            </div>

            <div class="border border-zinc-200/80 rounded-xl overflow-hidden shadow-2xs">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50/60 border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                            <th class="p-3.5">Equipment / Package</th>
                            <th class="p-3.5">Shoot Title & Client</th>
                            <th class="p-3.5">Assigned DoP / Operator</th>
                            <th class="p-3.5">Checkout Date</th>
                            <th class="p-3.5">Return Due Date</th>
                            <th class="p-3.5">Checkout Status</th>
                            <th class="p-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cora-checkouts-tbody" class="divide-y divide-zinc-150 bg-white">
                        <?php foreach ( $cora_gear_checkouts as $chk ) : 
                            $chk_st = $chk['status'] ?? 'Active';
                            $chk_badge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Active Shoot</span>';
                            if ( $chk_st === 'Returned' ) {
                                $chk_badge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-600 border border-zinc-200"><span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span> Returned</span>';
                            }
                        ?>
                        <tr id="checkout-row-<?php echo esc_attr( $chk['id'] ); ?>" class="hover:bg-zinc-50/60 transition-colors">
                            <td class="p-3.5">
                                <div class="font-bold text-zinc-950 text-xs"><?php echo esc_html( $chk['gear_name'] ); ?></div>
                                <div class="text-[10px] text-zinc-400 font-mono">Serial: <?php echo esc_html( $chk['serial'] ); ?></div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-semibold text-zinc-900 text-xs"><?php echo esc_html( $chk['shoot_title'] ); ?></div>
                                <div class="text-[10px] text-zinc-500">Client: <?php echo esc_html( $chk['client'] ); ?></div>
                            </td>
                            <td class="p-3.5 font-medium text-zinc-800 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-zinc-100 text-zinc-600 border border-zinc-200 text-[9px] font-bold flex items-center justify-center">
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    </span>
                                    <?php echo esc_html( $chk['dop_pilot'] ); ?>
                                </div>
                            </td>
                            <td class="p-3.5 font-mono text-xs text-zinc-700">
                                <?php echo esc_html( $chk['checkout_date'] ); ?>
                            </td>
                            <td class="p-3.5 font-mono text-xs font-bold text-zinc-900">
                                <?php echo esc_html( $chk['return_due_date'] ); ?>
                            </td>
                            <td class="p-3.5">
                                <span id="chk-status-badge-<?php echo esc_attr( $chk['id'] ); ?>"><?php echo $chk_badge; ?></span>
                            </td>
                            <td class="p-3.5 text-right">
                                <?php if ( $chk_st === 'Active' ) : ?>
                                    <button id="chk-return-btn-<?php echo esc_attr( $chk['id'] ); ?>" onclick="coraReturnCheckoutItem('<?php echo esc_attr( $chk['id'] ); ?>', '<?php echo esc_attr( $chk['gear_name'] ); ?>')" class="px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold transition-all cursor-pointer">
                                        Return to Studio
                                    </button>
                                <?php else : ?>
                                    <span class="text-xs text-zinc-400 font-semibold italic">Checked In</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 3: MAINTENANCE & FINANCIAL LEDGER -->
    <div id="cora-eq-tab-maintenance" class="cora-eq-tab-content space-y-4 hidden">
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-5">
            <div class="flex items-center justify-between flex-wrap gap-4 pb-4 border-b border-zinc-100">
                <div>
                    <h3 class="text-sm font-bold text-zinc-950">Equipment Servicing & Maintenance Financial Logs</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Tracking repair history, vendor invoices, and automatic CapEx/OpEx financial sync.</p>
                </div>
                <button onclick="openMaintenanceDrawer()" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer shadow-xs">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                    + Log Service Expense
                </button>
            </div>

            <div class="border border-zinc-200/80 rounded-xl overflow-hidden shadow-2xs">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-zinc-50/60 border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase tracking-wider">
                            <th class="p-3.5">Equipment Unit</th>
                            <th class="p-3.5">Repair / Service Type</th>
                            <th class="p-3.5">Service Date</th>
                            <th class="p-3.5">Vendor / Workshop</th>
                            <th class="p-3.5">Repair Expense</th>
                            <th class="p-3.5">Financial Ledger Sync</th>
                        </tr>
                    </thead>
                    <tbody id="cora-maintenance-tbody" class="divide-y divide-zinc-150 bg-white">
                        <?php foreach ( $cora_gear_maintenance as $mnt ) : ?>
                        <tr class="hover:bg-zinc-50/60 transition-colors">
                            <td class="p-3.5 font-bold text-zinc-950 text-xs"><?php echo esc_html( $mnt['equipment'] ); ?></td>
                            <td class="p-3.5 text-zinc-800 font-medium text-xs">
                                <?php echo esc_html( $mnt['repair_type'] ); ?>
                                <?php if ( ! empty( $mnt['notes'] ) ) : ?>
                                    <div class="text-[10px] text-zinc-400 font-normal mt-0.5"><?php echo esc_html( $mnt['notes'] ); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 font-mono text-xs text-zinc-700"><?php echo esc_html( $mnt['service_date'] ); ?></td>
                            <td class="p-3.5 text-zinc-800 font-medium text-xs"><?php echo esc_html( $mnt['vendor'] ); ?></td>
                            <td class="p-3.5 font-mono font-bold text-zinc-950 text-xs">₹<?php echo number_format( floatval( $mnt['repair_cost'] ) ); ?></td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Synced to Financial Ledger
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 4: STUDIO GEAR KITS -->
    <div id="cora-eq-tab-kits" class="cora-eq-tab-content space-y-4 hidden">
        <div class="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs space-y-5">
            <div class="flex items-center justify-between flex-wrap gap-4 pb-4 border-b border-zinc-100">
                <div>
                    <h3 class="text-sm font-bold text-zinc-950">Pre-Configured Studio Production Kits</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Bundled equipment packages ready for instant 1-click shoot assignment.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <?php foreach ( $cora_gear_kits as $kit ) : ?>
                <div class="border border-zinc-200 rounded-2xl p-5 bg-white shadow-2xs space-y-4 flex flex-col justify-between hover:border-zinc-300 transition-all">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-zinc-100 border border-zinc-200 text-zinc-800">
                                <?php echo esc_html( $kit['category'] ); ?>
                            </span>
                            <span class="text-xs font-mono font-bold text-zinc-900">₹<?php echo number_format( floatval( $kit['daily_rate'] ) ); ?>/day</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-zinc-950"><?php echo esc_html( $kit['name'] ); ?></h4>
                            <p class="text-xs text-zinc-500 mt-1 leading-relaxed"><?php echo esc_html( $kit['description'] ); ?></p>
                        </div>
                        <div class="space-y-1.5 pt-1">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Included Gear Units:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <?php foreach ( (array)$kit['items'] as $it ) : ?>
                                    <span class="px-2 py-0.5 bg-zinc-50 border border-zinc-200 rounded text-[11px] font-medium text-zinc-700"><?php echo esc_html( $it ); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-zinc-100">
                        <button onclick="openCheckoutGearDrawer('[KIT] <?php echo esc_attr( $kit['name'] ); ?>')" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-2 shadow-xs">
                            Allocate Kit to Shoot
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- ═══ 5. RIGHT-SLIDING SIDE DRAWER SHEETS ═════════════════════════════════════ -->

<!-- 1. REGISTER NEW GEAR DRAWER -->
<aside id="cora-add-gear-drawer" class="cora-drawer-sheet hidden fixed top-0 right-0 z-50 h-full w-[460px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
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

    <form id="cora-add-gear-form" onsubmit="coraSubmitAddGearForm(event)" class="flex-1 overflow-y-auto p-6 space-y-4">
        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Equipment Name / Model *</label>
            <input type="text" id="add-gear-name" required placeholder="e.g. Sony Alpha a7 IV Cinema Camera" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Serial Number # *</label>
                <input type="text" id="add-gear-serial" required placeholder="SN-774921" class="w-full px-3 py-2 text-xs font-mono border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Category *</label>
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

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">CapEx Valuation (₹) *</label>
                <input type="number" id="add-gear-capex" required placeholder="245000" class="w-full px-3 py-2 text-xs font-mono border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Purchase Date</label>
                <input type="date" id="add-gear-date" value="<?php echo date('Y-m-d'); ?>" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Condition Rating *</label>
                <select id="add-gear-condition" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                    <option value="Mint">Mint</option>
                    <option value="Excellent" selected>Excellent</option>
                    <option value="Good">Good</option>
                    <option value="Needs Repair">Needs Repair</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Initial Status *</label>
                <select id="add-gear-status" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950 cursor-pointer">
                    <option value="Available" selected>Available in Studio</option>
                    <option value="In Use">Checked Out / In Use</option>
                    <option value="Maintenance">In Maintenance</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Assigned Location / Operator</label>
            <input type="text" id="add-gear-assigned" placeholder="e.g. Unassigned (Studio Vault)" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
        </div>

        <div class="pt-4 border-t border-zinc-100 flex items-center justify-end gap-2">
            <button type="button" onclick="closeGearDrawers()" class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 text-xs font-bold rounded-xl hover:bg-zinc-50 cursor-pointer">Cancel</button>
            <button type="submit" class="px-5 py-2 bg-zinc-950 text-white text-xs font-bold rounded-xl hover:bg-zinc-800 cursor-pointer shadow-xs">Save & Register Gear</button>
        </div>
    </form>
</aside>

<!-- 2. SHOOT CHECKOUT & GEAR ASSIGNMENT DRAWER -->
<aside id="cora-checkout-gear-drawer" class="cora-drawer-sheet hidden fixed top-0 right-0 z-50 h-full w-[460px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
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
                <input type="text" id="checkout-client-name" required placeholder="e.g. Oberoi Group" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Assigned DoP / Operator *</label>
                <input type="text" id="checkout-dop-pilot" required placeholder="e.g. Karan Malhotra" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-xl focus:border-zinc-900 focus:outline-none bg-white text-zinc-950">
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
<aside id="cora-log-maintenance-drawer" class="cora-drawer-sheet hidden fixed top-0 right-0 z-50 h-full w-[460px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
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
<aside id="cora-view-repair-drawer" class="cora-drawer-sheet hidden fixed top-0 right-0 z-50 h-full w-[460px] max-w-[90vw] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
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

<!-- ═══ 6. INLINE DYNAMIC JAVASCRIPT ENGINE & REAL BACKEND AJAX INTEGRATION ═════════════════════════════════════ -->
<script>
// Initial Repair Data for View Repair details
window.coraRepairData = <?php echo json_encode( $initial_repair_data ); ?>;

// Standardized Sub-Tab Switching Functionality
window.coraSwitchEquipmentTab = function(tabId) {
    // Hide all tab contents
    var contents = document.querySelectorAll('.cora-eq-tab-content');
    contents.forEach(function(el) {
        el.classList.add('hidden');
    });

    // Reset sub-tab button states
    var buttons = document.querySelectorAll('.cora-eq-tab-btn');
    buttons.forEach(function(btn) {
        btn.classList.remove('active', 'border-b-2', 'border-zinc-950', 'text-zinc-950');
        btn.classList.add('border-b-2', 'border-transparent', 'text-zinc-500');
    });

    // Show target panel & activate button
    var target = document.getElementById('cora-eq-tab-' + tabId);
    var targetBtn = document.getElementById('tab-btn-' + tabId);

    if (target) target.classList.remove('hidden');
    if (targetBtn) {
        targetBtn.classList.remove('border-transparent', 'text-zinc-500');
        targetBtn.classList.add('active', 'border-zinc-950', 'text-zinc-950');
    }
};

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
window.openAddGearDrawer = function() {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    var backdrop = document.getElementById('cora-drawer-backdrop');
    if (backdrop) backdrop.classList.remove('hidden');

    var drawer = document.getElementById('cora-add-gear-drawer');
    if (drawer) {
        drawer.classList.remove('hidden');
        drawer.classList.remove('collapsed');
    }
};

window.openCheckoutGearDrawer = function(presetGearName) {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    var backdrop = document.getElementById('cora-drawer-backdrop');
    if (backdrop) backdrop.classList.remove('hidden');

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
        drawer.classList.remove('hidden');
        drawer.classList.remove('collapsed');
    }
};

window.openMaintenanceDrawer = function(presetGearName) {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    var backdrop = document.getElementById('cora-drawer-backdrop');
    if (backdrop) backdrop.classList.remove('hidden');

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
        drawer.classList.remove('hidden');
        drawer.classList.remove('collapsed');
    }
};

window.openViewRepairDrawer = function(gearId) {
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
    var backdrop = document.getElementById('cora-drawer-backdrop');
    if (backdrop) backdrop.classList.remove('hidden');

    var drawer = document.getElementById('cora-view-repair-drawer');
    if (drawer) {
        var details = (window.coraRepairData && window.coraRepairData[gearId]) ? window.coraRepairData[gearId] : null;
        if (details) {
            document.getElementById('view-repair-gear-name').textContent = details.name || 'Equipment Unit';
            document.getElementById('view-repair-cost').textContent = '₹' + parseInt(details.cost || 0).toLocaleString();
            document.getElementById('view-repair-date').textContent = details.date || 'N/A';
            document.getElementById('view-repair-vendor').textContent = details.vendor || 'N/A';
            document.getElementById('view-repair-notes').textContent = details.notes || 'No notes recorded.';
        } else {
            // Fallback default info for gear_aputure_300d if not found
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
    }
};

window.closeGearDrawers = function() {
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
window.coraSubmitAddGearForm = function(e) {
    e.preventDefault();
    var name = document.getElementById('add-gear-name').value.trim();
    var serial = document.getElementById('add-gear-serial').value.trim();
    var category = document.getElementById('add-gear-category').value;
    var capex = document.getElementById('add-gear-capex').value;
    var condition = document.getElementById('add-gear-condition').value;
    var status = document.getElementById('add-gear-status').value;
    var assigned = document.getElementById('add-gear-assigned').value.trim() || 'Unassigned (Studio Vault)';

    if (!name || !serial) return;

    var payload = {
        action: 'cora_ajax_save_gear_item',
        security: (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : '',
        name: name,
        serial_no: serial,
        category: category,
        purchase_price: capex,
        current_value: capex,
        condition: condition,
        status: status === 'In Repair' ? 'In Repair' : (status === 'On Shoot' ? 'On Shoot' : status),
        assigned_to: assigned
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

// Handle Delete Gear Item
window.coraDeleteGearItem = function(gearId) {
    if (!confirm('Are you sure you want to delete this gear item from the inventory?')) {
        return;
    }

    var payload = {
        action: 'cora_ajax_delete_studio_gear',
        security: (typeof coraData !== 'undefined' && coraData.nonce) ? coraData.nonce : '',
        gear_id: gearId
    };

    if (typeof jQuery !== 'undefined') {
        jQuery.post((typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php'), payload, function(res) {
            console.log('AJAX delete response:', res);
            location.reload();
        });
    } else {
        location.reload();
    }
};
</script>
