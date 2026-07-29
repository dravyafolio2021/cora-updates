<?php
/**
 * Cora Workspace - Enterprise Document Studio & Vault
 * File: views/view-vault.php
 * Strictly Monochromatic Notion/Shopify Aesthetic UI Design
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Fetch documents from WP options or fallback to exact default sample documents from UI mockup
$cora_documents = get_option( 'cora_documents', array() );

if ( empty( $cora_documents ) || ! is_array( $cora_documents ) ) {
    $cora_documents = array(
        array(
            'id'             => 'doc_101',
            'number'         => 'DOC-2026',
            'title'          => 'Proposal: Arjun & Priya Wedding Coverage',
            'type'           => 'Proposal',
            'client_name'    => 'Arjun & Priya',
            'client_email'   => 'arjun.priya@example.com',
            'client_phone'   => '9876543210',
            'client_gstin'   => '22AAAAA0000A1Z5',
            'client_address' => 'Vasant Vihar, New Delhi - 110057',
            'pos_state'      => 'Delhi (07)',
            'is_igst'        => false,
            'amount'         => 450000,
            'tax_amount'     => 81000,
            'grand_total'    => 531000,
            'deposit'        => 225000,
            'currency'       => 'INR',
            'upi_vpa'        => 'cora@icici',
            'status'         => 'Sent',
            'watermark'      => 'CONFIDENTIAL',
            'signed'         => false,
            'items'          => array(
                array( 'desc' => '3-Day Full Wedding Cinematography & Aerial Drone', 'sac' => '998381', 'qty' => 1, 'rate' => 300000, 'tax' => 18 ),
                array( 'desc' => 'Candid Fine-Art Photography & Signature Album Box', 'sac' => '998381', 'qty' => 1, 'rate' => 150000, 'tax' => 18 )
            )
        ),
        array(
            'id'             => 'doc_102',
            'number'         => 'DOC-2026',
            'title'          => 'Invoice: Apex Realty Commercial Lease',
            'type'           => 'Invoice',
            'client_name'    => 'Apex Realty Group',
            'client_email'   => 'finance@apexrealty.com',
            'client_phone'   => '9811223344',
            'client_gstin'   => '27AAAAA0000B1Z3',
            'pos_state'      => 'Maharashtra (27)',
            'is_igst'        => true,
            'amount'         => 180000,
            'tax_amount'     => 32400,
            'grand_total'    => 212400,
            'deposit'        => 180000,
            'currency'       => 'INR',
            'upi_vpa'        => 'cora@icici',
            'status'         => 'Paid',
            'signed'         => true,
            'signed_at'      => '2026-06-12 14:30:15',
            'signer_name'    => 'Rajesh Sharma',
            'signer_email'   => 'finance@apexrealty.com',
            'signer_ip'      => '103.21.124.8',
            'verification_hash' => 'ESIGN-HASH-A9F821C7B04',
            'items'          => array(
                array( 'desc' => 'Commercial Property Lease Brokerage Fee', 'sac' => '997212', 'qty' => 1, 'rate' => 180000, 'tax' => 18 )
            )
        ),
        array(
            'id'             => 'doc_103',
            'number'         => 'DOC-2026',
            'title'          => 'Contract: Delhi Fashion Week 2026 Agreement',
            'type'           => 'Contract',
            'client_name'    => 'Fashion Council India',
            'client_email'   => 'contact@fashioncouncil.in',
            'client_phone'   => '9876500000',
            'client_gstin'   => '07AAAAA0000C1Z8',
            'pos_state'      => 'Delhi (07)',
            'is_igst'        => false,
            'amount'         => 40000,
            'tax_amount'     => 7200,
            'grand_total'    => 47200,
            'deposit'        => 20000,
            'currency'       => 'INR',
            'upi_vpa'        => 'cora@icici',
            'status'         => 'Signed',
            'signed'         => true,
            'signed_at'      => '2026-06-14 10:15:00',
            'signer_name'    => 'Fashion Council India',
            'signer_email'   => 'contact@fashioncouncil.in',
            'signer_ip'      => '103.21.124.9',
            'verification_hash' => 'ESIGN-HASH-B88102C1F03',
            'items'          => array(
                array( 'desc' => 'Fashion Week SLA & Media Production Rights', 'sac' => '998381', 'qty' => 1, 'rate' => 40000, 'tax' => 18 )
            )
        ),
        array(
            'id'             => 'doc_104',
            'number'         => 'DOC-2026',
            'title'          => 'asdfgh',
            'type'           => 'Equipment',
            'client_name'    => 'Arjun & Priya',
            'client_email'   => 'arjun.priya@example.com',
            'client_phone'   => '9876543210',
            'client_gstin'   => '22AAAAA0000A1Z5',
            'pos_state'      => 'Delhi (07)',
            'is_igst'        => false,
            'amount'         => 0,
            'grand_total'    => 0,
            'deposit'        => 0,
            'currency'       => 'INR',
            'upi_vpa'        => 'cora@icici',
            'status'         => 'Sent',
            'signed'         => false,
            'items'          => array(
                array( 'desc' => 'Camera Gear Checkout Waiver Scope', 'sac' => '997311', 'qty' => 1, 'rate' => 0, 'tax' => 0 )
            )
        ),
        array(
            'id'             => 'doc_105',
            'number'         => 'DOC-2026',
            'title'          => 'Invoice: Arjun & Priya Wedding Coverage',
            'type'           => 'Invoice',
            'client_name'    => 'Arjun & Priya',
            'client_email'   => 'arjun.priya@example.com',
            'client_phone'   => '9876543210',
            'client_gstin'   => '22AAAAA0000A1Z5',
            'pos_state'      => 'Delhi (07)',
            'is_igst'        => false,
            'amount'         => 450000,
            'tax_amount'     => 81000,
            'grand_total'    => 531000,
            'deposit'        => 225000,
            'currency'       => 'INR',
            'upi_vpa'        => 'cora@icici',
            'status'         => 'Sent',
            'signed'         => false,
            'items'          => array(
                array( 'desc' => '3-Day Full Wedding Billing Invoice', 'sac' => '998381', 'qty' => 1, 'rate' => 450000, 'tax' => 18 )
            )
        )
    );
    update_option( 'cora_documents', $cora_documents );
}

// Compute KPI Metrics
$total_docs = count( $cora_documents );
$proposal_count = 0;
$signed_count   = 0;
$total_receivables = 0;

foreach ( $cora_documents as $doc ) {
    $t = strtolower( $doc['type'] ?? '' );
    if ( $t === 'proposal' || $t === 'quote' || $t === 'quotation' ) {
        $proposal_count++;
    }
    if ( ! empty( $doc['signed'] ) && ( $t === 'contract' || $t === 'service agreement' || $t === 'nda' ) ) {
        $signed_count++;
    }
    if ( ( $doc['status'] ?? '' ) !== 'Paid' && ( $t === 'invoice' || $t === 'receipt' ) ) {
        $total_receivables += floatval( $doc['grand_total'] ?? $doc['amount'] ?? 0 );
    }
}
?>

<style>
/* ═══════════════════════════════════════════════════════════════════════════
   STRICT PRINT ENGINE: HIDES ALL SIDEBARS & WEBPAGE UI TO PRINT ONLY INVOICE
   ═══════════════════════════════════════════════════════════════════════════ */
@media print {
    body * {
        visibility: hidden !important;
    }

    #cora-printable-canvas, #cora-printable-canvas * {
        visibility: visible !important;
    }

    #cora-printable-canvas {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 30px !important;
        box-shadow: none !important;
        border: none !important;
        background: #ffffff !important;
    }
}

.cora-drawer-backdrop {
    transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.cora-drawer-sheet {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.cora-vtab {
    transition: all 0.15s ease-in-out;
}
.cora-vtab:not(.active-vtab) {
    color: #52525b !important;
    background-color: transparent !important;
}
.cora-vtab:not(.active-vtab):hover {
    color: #09090b !important;
    background-color: #f4f4f5 !important;
}
.cora-vtab.active-vtab {
    color: #ffffff !important;
    background-color: #09090b !important;
    font-weight: 700 !important;
}
.cora-vtab.active-vtab:hover {
    color: #ffffff !important;
    background-color: #18181b !important;
}
</style>

<div id="cora-vault-wrapper" class="space-y-5 relative font-sans text-zinc-900 pb-20">
   <!-- Top Header & Navigation area (Shopify/Notion UI style alignment) -->
   <div class="flex flex-col gap-4 border-b border-zinc-200/80 pb-0 mb-5">
       <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
           <div>
               <h1 class="text-xl font-bold tracking-tight text-zinc-950 sm:text-2xl">File Manager & Vault</h1>
               <p class="text-xs text-zinc-500 mt-0.5 font-medium">Manage legally binding contracts, proposals, client invoices, and e-sign registry workflows.</p>
           </div>
           
           <!-- Actions inline with title on desktop -->
           <div class="flex items-center gap-2 pb-2">
               <button onclick="coraCreateNewDocInStudio()" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 shadow-xs cursor-pointer whitespace-nowrap">
                   <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                   Create Document Wizard
               </button>
               <button onclick="coraExportVaultCSV()" class="px-3 py-2 bg-white border border-zinc-200 text-zinc-700 text-xs font-semibold rounded-xl hover:bg-zinc-50 cursor-pointer shadow-xs flex items-center gap-1.5 whitespace-nowrap">
                   <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                   CSV Export
               </button>
               <button onclick="coraExportGSTR1()" class="px-3 py-2 bg-white border border-zinc-200 text-zinc-700 text-xs font-semibold rounded-xl hover:bg-zinc-50 cursor-pointer shadow-xs flex items-center gap-1.5 whitespace-nowrap">
                   <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                   GSTR-1 Tax CSV
               </button>
           </div>
       </div>

       <!-- Segmented Switcher Tab Bar -->
       <div class="flex items-center gap-1 sm:gap-2 -mb-px">
           <button onclick="coraSwitchVaultView('vault')" id="vault-mode-btn-vault" class="flex items-center gap-1.5 pb-2.5 px-3.5 text-xs font-bold transition-all text-zinc-950 border-b-2 border-zinc-950 bg-transparent cursor-pointer whitespace-nowrap">
               <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
               Document Vault
           </button>
           <button onclick="coraSwitchVaultView('editor')" id="vault-mode-btn-editor" class="flex items-center gap-1.5 pb-2.5 px-3.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-all border-b-2 border-transparent bg-transparent cursor-pointer whitespace-nowrap">
               <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
               Document Studio Wizard
           </button>
           <button onclick="coraSwitchVaultView('esign')" id="vault-mode-btn-esign" class="flex items-center gap-1.5 pb-2.5 px-3.5 text-xs font-semibold text-zinc-500 hover:text-zinc-950 transition-all border-b-2 border-transparent bg-transparent cursor-pointer whitespace-nowrap">
               <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
               E-Sign Audit Registry
           </button>
       </div>
   </div>

    <!-- ═════════════════════════════════════════════════════════════════════════
         VIEW 1: MASTER DOCUMENT VAULT DASHBOARD (COLORED BADGES ENHANCED)
         ═════════════════════════════════════════════════════════════════════ -->
    <div id="cora-vault-view-dashboard" class="space-y-5">
        <!-- 4 KPI Cards Grid (Compact & Monochromatic Horizontal Spacing) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Documents -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 transition-all flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-100 text-zinc-750 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Total Documents</span>
                    <span id="kpi-total-docs-count" class="text-xl font-extrabold text-zinc-950 tracking-tight block mt-0.5"><?php echo $total_docs; ?></span>
                </div>
            </div>

            <!-- Card 2: Proposals & Quotes -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 transition-all flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-100 text-zinc-750 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Proposals & Quotes</span>
                    <span id="kpi-proposals-count" class="text-xl font-extrabold text-zinc-950 tracking-tight block mt-0.5"><?php echo $proposal_count; ?></span>
                </div>
            </div>

            <!-- Card 3: Signed Contracts -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 transition-all flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-100 text-zinc-750 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Signed Contracts</span>
                    <span id="kpi-signed-count" class="text-xl font-extrabold text-zinc-950 tracking-tight block mt-0.5"><?php echo $signed_count; ?></span>
                </div>
            </div>

            <!-- Card 4: Pending Receivables -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-4 shadow-2xs hover:border-zinc-300 transition-all flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-100 text-zinc-750 flex items-center justify-center font-extrabold text-sm shrink-0">
                    ₹
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Pending Receivables</span>
                    <span id="kpi-receivables-amount" class="text-xl font-extrabold text-zinc-950 tracking-tight block mt-0.5">₹<?php echo number_format( $total_receivables ); ?></span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar (Natural Borderless Layout) -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4 mt-2 mb-1">
            <div class="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0 scrollbar-none" id="vault-type-tabs">
                <button onclick="coraFilterVault('all', this)" class="cora-vtab px-3 py-1.5 rounded-lg text-xs font-bold active-vtab cursor-pointer transition-all shadow-3xs shrink-0" data-type="all">All Documents</button>
                <button onclick="coraFilterVault('proposal', this)" class="cora-vtab px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-all hover:bg-zinc-100 hover:text-zinc-950 text-zinc-655 shrink-0" data-type="proposal">Proposals</button>
                <button onclick="coraFilterVault('invoice', this)" class="cora-vtab px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-all hover:bg-zinc-100 hover:text-zinc-950 text-zinc-655 shrink-0" data-type="invoice">Invoices</button>
                <button onclick="coraFilterVault('contract', this)" class="cora-vtab px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-all hover:bg-zinc-100 hover:text-zinc-950 text-zinc-655 shrink-0" data-type="contract">Contracts</button>
                <button onclick="coraFilterVault('offer', this)" class="cora-vtab px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-all hover:bg-zinc-100 hover:text-zinc-950 text-zinc-655 shrink-0" data-type="offer">Offer Letters</button>
            </div>

            <div class="flex items-center gap-2 flex-1 sm:w-72 max-w-md">
                <div class="relative flex-1">
                    <input type="text" id="vault-search-input" onkeyup="coraSearchVault(this.value)" placeholder="Search title, client, or doc #..." class="w-full pl-9 pr-3 py-2 bg-white border border-zinc-200 rounded-xl text-xs outline-none focus:border-zinc-950 transition-all font-medium shadow-3xs">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <button class="p-2 border border-zinc-200 rounded-xl bg-white text-zinc-700 hover:bg-zinc-50 cursor-pointer shadow-3xs shrink-0" title="Advanced Filter Options">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                </button>
            </div>
        </div>

        <!-- Master Documents Table (Scrollable Container) -->
        <div class="bg-white border border-zinc-200/80 rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs min-w-[700px]">
                <thead>
                    <tr class="bg-zinc-50/70 border-b border-zinc-200 text-[10px] font-extrabold text-zinc-500 uppercase tracking-wider">
                        <th class="p-4">DOC # & TITLE</th>
                        <th class="p-4">CATEGORY</th>
                        <th class="p-4">CLIENT & GSTIN</th>
                        <th class="p-4">GRAND TOTAL</th>
                        <th class="p-4">STATUS</th>
                        <th class="p-4">E-SIGN</th>
                        <th class="p-4 text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="cora-vault-table-body" class="divide-y divide-zinc-100">
                    <?php foreach ( $cora_documents as $doc ) : 
                        $type_lower = strtolower($doc['type'] ?? '');
                        $status = $doc['status'] ?? 'Draft';
                        
                        // Category soft badges & icons (Color psychology tints - Soft Fills with Faint Borders)
                        $cat_bg = 'bg-zinc-50 border border-zinc-200 text-zinc-650 font-semibold';
                        $icon_bg = 'bg-zinc-50 border border-zinc-150 text-zinc-500';
                        
                        if ($type_lower === 'invoice') {
                            $cat_bg = 'bg-emerald-50 border border-emerald-100 text-emerald-700 font-bold';
                            $icon_bg = 'bg-emerald-50 border border-emerald-100 text-emerald-600';
                        } elseif ($type_lower === 'contract' || $type_lower === 'sla' || $type_lower === 'nda') {
                            $cat_bg = 'bg-purple-50 border border-purple-100 text-purple-700 font-bold';
                            $icon_bg = 'bg-purple-50 border border-purple-100 text-purple-600';
                        } elseif ($type_lower === 'proposal' || $type_lower === 'quote') {
                            $cat_bg = 'bg-blue-50 border border-blue-100 text-blue-700 font-bold';
                            $icon_bg = 'bg-blue-50 border border-blue-100 text-blue-600';
                        } elseif ($type_lower === 'equipment' || $type_lower === 'gear') {
                            $cat_bg = 'bg-amber-50 border border-amber-100 text-amber-700 font-bold';
                            $icon_bg = 'bg-amber-50 border border-amber-100 text-amber-600';
                        }

                        // Status badges (Color psychology tints - Soft Fills with Faint Borders)
                        $status_bg = 'bg-zinc-50 border border-zinc-200 text-zinc-650 font-semibold';
                        if ($status === 'Paid') {
                            $status_bg = 'bg-emerald-50 border border-emerald-100 text-emerald-700 font-bold';
                        } elseif ($status === 'Signed') {
                            $status_bg = 'bg-purple-50 border border-purple-100 text-purple-700 font-bold';
                        } elseif ($status === 'Sent' || $status === 'Active') {
                            $status_bg = 'bg-blue-50 border border-blue-100 text-blue-700 font-bold';
                        } elseif ($status === 'Pending') {
                            $status_bg = 'bg-amber-50 border border-amber-100 text-amber-700 font-bold';
                        }

                        $is_signed = ! empty( $doc['signed'] );
                        $is_proposal = $type_lower === 'proposal';
                    ?>
                    <tr class="hover:bg-zinc-50/80 transition-colors cora-vault-row" data-type="<?php echo esc_attr( $type_lower ); ?>">
                        <td class="p-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl <?php echo $icon_bg; ?> flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                </div>
                                <div>
                                    <div class="font-mono text-[10px] text-zinc-400 font-bold tracking-tight"><?php echo esc_html( $doc['number'] ?? 'DOC-2026' ); ?></div>
                                    <div class="font-bold text-zinc-950 cursor-pointer hover:underline text-xs" onclick="coraOpenDocPreviewDrawer('<?php echo esc_js( $doc['id'] ); ?>')"><?php echo esc_html( $doc['title'] ); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold <?php echo $cat_bg; ?> uppercase tracking-wider">
                                <?php echo esc_html( $doc['type'] ); ?>
                            </span>
                        </td>
                        <td class="p-4 py-3.5">
                            <div class="font-bold text-zinc-950 hover:underline cursor-pointer flex items-center gap-1.5 group" onclick="coraOpenClientProfileInCRM('<?php echo esc_js( $doc['client_name'] ); ?>')" title="Open Client CRM Profile">
                                <span><?php echo esc_html( $doc['client_name'] ); ?></span>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="opacity-0 group-hover:opacity-100 transition-opacity text-zinc-400 shrink-0"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </div>
                            <?php if ( ! empty( $doc['client_gstin'] ) ) : ?>
                                <div class="text-[10px] font-mono text-zinc-400 mt-0.5"><?php echo esc_html( $doc['client_gstin'] ); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 py-3.5 font-bold font-mono text-zinc-950 text-sm">
                            ₹<?php echo number_format( floatval( $doc['grand_total'] ?? $doc['amount'] ?? 0 ) ); ?>
                        </td>
                        <td class="p-4 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-[9px] <?php echo $status_bg; ?> uppercase tracking-wider">
                                <?php echo esc_html( $status ); ?>
                            </span>
                        </td>
                        <td class="p-4 py-3.5">
                            <?php if ( $is_signed ) : ?>
                                <span class="text-zinc-950 font-bold text-[11px] flex items-center gap-1 whitespace-nowrap">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Signed
                                </span>
                            <?php else : ?>
                                <button onclick="coraOpenESignDrawer('<?php echo esc_js( $doc['id'] ); ?>')" class="px-2.5 py-1 rounded-lg bg-white hover:bg-zinc-50 text-zinc-700 font-semibold text-[11px] border border-zinc-200 cursor-pointer transition-all shadow-xs whitespace-nowrap">
                                    + E-Sign
                                </button>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5 whitespace-nowrap">
                                <?php if ( $is_proposal ) : ?>
                                    <button onclick="coraConvertQuoteToInvoice('<?php echo esc_js( $doc['id'] ); ?>')" class="p-1.5 bg-zinc-950 text-white hover:bg-zinc-800 rounded-lg transition-all cursor-pointer shadow-xs whitespace-nowrap" title="Convert to GST Invoice">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                    </button>
                                <?php endif; ?>
                                <button onclick="coraOpenDocPreviewDrawer('<?php echo esc_js( $doc['id'] ); ?>')" class="px-2.5 py-1 bg-white border border-zinc-200 text-zinc-800 rounded-lg hover:bg-zinc-100 text-[11px] font-semibold cursor-pointer transition-all whitespace-nowrap">View</button>
                                <button onclick="coraOpenDocInStudio('<?php echo esc_js( $doc['id'] ); ?>')" class="px-2.5 py-1 bg-zinc-100 border border-zinc-300 text-zinc-950 rounded-lg hover:bg-zinc-200 text-[11px] font-bold cursor-pointer transition-all whitespace-nowrap">Edit Studio</button>
                                <button onclick="coraOpenShareModal('<?php echo esc_js( $doc['id'] ); ?>')" class="p-1.5 bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100 rounded-lg cursor-pointer transition-all shrink-0" title="Share Document">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                </button>
                                <button onclick="coraToggleVaultPopover(event, '<?php echo esc_js( $doc['id'] ); ?>', <?php echo $is_proposal ? 'true' : 'false'; ?>)" class="p-1.5 text-zinc-400 hover:text-zinc-950 cursor-pointer rounded-lg hover:bg-zinc-100 transition-colors shrink-0" title="More Options">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>

            <!-- Table Footer & Pagination Bar -->
            <div class="p-4 bg-zinc-50/50 border-t border-zinc-200/80 flex items-center justify-between flex-wrap gap-3">
                <span class="text-xs text-zinc-500 font-medium">Showing 1 to <?php echo $total_docs; ?> of <?php echo $total_docs; ?> documents</span>
                <div class="flex items-center gap-1.5">
                    <button class="w-7 h-7 rounded-lg border border-zinc-200 bg-white text-zinc-500 hover:text-zinc-950 flex items-center justify-center cursor-pointer shadow-xs transition-colors">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <button class="w-7 h-7 rounded-lg bg-zinc-950 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                        1
                    </button>
                    <button class="w-7 h-7 rounded-lg border border-zinc-200 bg-white text-zinc-500 hover:text-zinc-950 flex items-center justify-center cursor-pointer shadow-xs transition-colors">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════════
         VIEW 2: UNIFIED 6-STEP VISUAL GUIDED DOCUMENT WIZARD WITH STICKY DOCK
         ═════════════════════════════════════════════════════════════════════ -->
    <div id="cora-vault-view-editor" class="hidden space-y-8 w-full max-w-none">
        <form id="cora-doc-wizard-form" data-autosave-module="vault_doc_wizard" class="space-y-8 w-full">
        <input type="hidden" id="studio-doc-id" name="studio_doc_id">

        <!-- STEP 1 SUB-PAGE: DOCUMENT TYPE SELECTION -->
        <div id="sub-page-wiz-step-1" class="bg-white border border-zinc-200/80 rounded-3xl p-6 md:p-10 pb-24 sm:pb-28 shadow-xs space-y-6 w-full max-w-none">
            <div class="border-b border-zinc-100 pb-5 space-y-3.5">
                <div>
                    <span class="px-3.5 py-1.5 rounded-full bg-zinc-100 text-zinc-600 text-[11px] font-bold uppercase tracking-widest inline-block border border-zinc-200">Step 1 of 6</span>
                    <h3 class="text-xl font-black text-zinc-950 tracking-tight mt-2">What type of document are you creating?</h3>
                    <p class="text-xs text-zinc-500 mt-1 leading-relaxed">Select a category below or search document types. In Step 2, you can choose visual blueprint templates.</p>
                </div>
                <div class="relative w-full max-w-sm">
                    <input type="text" id="wiz-cat-search-input" onkeyup="coraFilterWizCategories(this.value)" placeholder="Search document types..." class="w-full pl-10 pr-3.5 py-2 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-medium outline-none focus:border-zinc-950 focus:bg-white transition-all">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="absolute left-3.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-1">
                <!-- 1. Proposal & Quotation -->
                <div onclick="coraSelectWizCategoryCard('proposal')" id="wiz-cat-card-proposal" class="cora-wiz-cat-card flex items-center gap-4 p-4 border-2 border-zinc-950 bg-zinc-50/80 rounded-2xl cursor-pointer transition-all shadow-xs hover:shadow-md">
                    <div class="w-11 h-11 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-700"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="12" y2="17"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="px-2 py-0.5 rounded-md bg-zinc-950 text-white text-[9px] font-extrabold uppercase tracking-wider inline-block mb-1">Proposal</span>
                        <h4 class="font-extrabold text-zinc-950 text-sm leading-tight">Proposal & Quotation</h4>
                        <p class="text-zinc-500 text-[11px] leading-relaxed mt-0.5">Shoot packages, real estate listing media, commercial bids, and production estimates.</p>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>

                <!-- 2. Invoices & Receipts -->
                <div onclick="coraSelectWizCategoryCard('invoice')" id="wiz-cat-card-invoice" class="cora-wiz-cat-card flex items-center gap-4 p-4 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all hover:shadow-md">
                    <div class="w-11 h-11 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-600"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="7" x2="16" y2="7"/><line x1="8" y1="11" x2="13" y2="11"/><line x1="8" y1="15" x2="16" y2="15"/><line x1="8" y1="18" x2="11" y2="18"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 text-[9px] font-extrabold uppercase tracking-wider border border-zinc-200 inline-block mb-1">Tax Invoice</span>
                        <h4 class="font-extrabold text-zinc-950 text-sm leading-tight">Invoices & Receipts</h4>
                        <p class="text-zinc-500 text-[11px] leading-relaxed mt-0.5">Retainer tax invoices, post-production final bills, deposit slips, and statutory GST notes.</p>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>

                <!-- 3. Contracts & SLAs -->
                <div onclick="coraSelectWizCategoryCard('contract')" id="wiz-cat-card-contract" class="cora-wiz-cat-card flex items-center gap-4 p-4 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all hover:shadow-md">
                    <div class="w-11 h-11 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-600"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 text-[9px] font-extrabold uppercase tracking-wider border border-zinc-200 inline-block mb-1">Contract</span>
                        <h4 class="font-extrabold text-zinc-950 text-sm leading-tight">Contracts & SLAs</h4>
                        <p class="text-zinc-500 text-[11px] leading-relaxed mt-0.5">Service level agreements, licensing rights, copyright release terms, and deliverables protection.</p>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>

                <!-- 4. Hiring & Offer Letters -->
                <div onclick="coraSelectWizCategoryCard('offer')" id="wiz-cat-card-offer" class="cora-wiz-cat-card flex items-center gap-4 p-4 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all hover:shadow-md">
                    <div class="w-11 h-11 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-600"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 text-[9px] font-extrabold uppercase tracking-wider border border-zinc-200 inline-block mb-1">Offer Letter</span>
                        <h4 class="font-extrabold text-zinc-950 text-sm leading-tight">Hiring & Offer Letters</h4>
                        <p class="text-zinc-500 text-[11px] leading-relaxed mt-0.5">Associate photographer offers, editor contractor agreements, monthly studio retainers.</p>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>

                <!-- 5. NDA -->
                <div onclick="coraSelectWizCategoryCard('nda')" id="wiz-cat-card-nda" class="cora-wiz-cat-card flex items-center gap-4 p-4 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all hover:shadow-md">
                    <div class="w-11 h-11 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-600"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1" fill="currentColor" stroke="none"/><line x1="12" y1="17" x2="12" y2="19" stroke-linecap="round"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 text-[9px] font-extrabold uppercase tracking-wider border border-zinc-200 inline-block mb-1">NDA</span>
                        <h4 class="font-extrabold text-zinc-950 text-sm leading-tight">NDA (Confidentiality)</h4>
                        <p class="text-zinc-500 text-[11px] leading-relaxed mt-0.5">Mutual non-disclosure agreements, pre-release media confidentiality, trade secret protection.</p>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>

                <!-- 6. Service Agreement -->
                <div onclick="coraSelectWizCategoryCard('service_agreement')" id="wiz-cat-card-service_agreement" class="cora-wiz-cat-card flex items-center gap-4 p-4 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all hover:shadow-md">
                    <div class="w-11 h-11 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-600"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><line x1="8" y1="11" x2="16" y2="11"/><line x1="8" y1="15" x2="16" y2="15"/><line x1="8" y1="19" x2="12" y2="19"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 text-[9px] font-extrabold uppercase tracking-wider border border-zinc-200 inline-block mb-1">Service Agmt</span>
                        <h4 class="font-extrabold text-zinc-950 text-sm leading-tight">Service Agreement</h4>
                        <p class="text-zinc-500 text-[11px] leading-relaxed mt-0.5">Comprehensive client engagement contracts, scope definitions, and payment schedules.</p>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>

                <!-- 7. Purchase Order -->
                <div onclick="coraSelectWizCategoryCard('purchase_order')" id="wiz-cat-card-purchase_order" class="cora-wiz-cat-card flex items-center gap-4 p-4 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all hover:shadow-md">
                    <div class="w-11 h-11 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-600"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 text-[9px] font-extrabold uppercase tracking-wider border border-zinc-200 inline-block mb-1">Purchase Order</span>
                        <h4 class="font-extrabold text-zinc-950 text-sm leading-tight">Purchase Order</h4>
                        <p class="text-zinc-500 text-[11px] leading-relaxed mt-0.5">Equipment procurement orders, vendor requisitions, and gear purchase confirmations.</p>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>

                <!-- 8. Receipt -->
                <div onclick="coraSelectWizCategoryCard('receipt')" id="wiz-cat-card-receipt" class="cora-wiz-cat-card flex items-center gap-4 p-4 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all hover:shadow-md">
                    <div class="w-11 h-11 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-600"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 text-[9px] font-extrabold uppercase tracking-wider border border-zinc-200 inline-block mb-1">Receipt</span>
                        <h4 class="font-extrabold text-zinc-950 text-sm leading-tight">Payment Receipt</h4>
                        <p class="text-zinc-500 text-[11px] leading-relaxed mt-0.5">Advance deposit receipts, interim payment vouchers, and payment confirmation slips.</p>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>

                <!-- 9. Custom Document -->
                <div onclick="coraSelectWizCategoryCard('custom')" id="wiz-cat-card-custom" class="cora-wiz-cat-card flex items-center gap-4 p-4 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all hover:shadow-md">
                    <div class="w-11 h-11 rounded-xl bg-zinc-100 border border-zinc-200 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-600"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-700 text-[9px] font-extrabold uppercase tracking-wider border border-zinc-200 inline-block mb-1">Custom</span>
                        <h4 class="font-extrabold text-zinc-950 text-sm leading-tight">Custom Document</h4>
                        <p class="text-zinc-500 text-[11px] leading-relaxed mt-0.5">Blank canvas blueprint for tailored studio documents and bespoke agreements.</p>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>
        </div>

        <!-- STEP 2 SUB-PAGE: CHOOSE VISUAL TEMPLATE BLUEPRINT -->
        <div id="sub-page-wiz-step-2" class="hidden bg-white border border-zinc-200/80 rounded-3xl p-6 md:p-10 pb-24 sm:pb-28 shadow-xs space-y-6 w-full max-w-none">
            <div class="border-b border-zinc-100 pb-5 space-y-3.5">
                <div>
                    <span class="px-3.5 py-1.5 rounded-full bg-zinc-100 text-zinc-600 text-[11px] font-bold uppercase tracking-widest inline-block border border-zinc-200">Step 2 of 6</span>
                    <h3 class="text-xl font-black text-zinc-950 tracking-tight mt-0.5">Choose a Visual Template Blueprint</h3>
                    <p class="text-xs text-zinc-500 mt-1 leading-relaxed">Below are visual paper preview cards for your selected category. Click any template to select it.</p>
                </div>
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none shrink-0" id="wiz-tpl-filter-tabs">
                    <button type="button" onclick="coraFilterTemplates('all', this)" class="cora-tpl-tab px-3 py-1.5 rounded-lg text-xs font-bold bg-zinc-950 text-white shadow-xs cursor-pointer shrink-0">All Templates</button>
                    <button type="button" onclick="coraFilterTemplates('minimal', this)" class="cora-tpl-tab px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 cursor-pointer shrink-0">Minimal</button>
                    <button type="button" onclick="coraFilterTemplates('modern', this)" class="cora-tpl-tab px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 cursor-pointer shrink-0">Modern</button>
                    <button type="button" onclick="coraFilterTemplates('professional', this)" class="cora-tpl-tab px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 cursor-pointer shrink-0">Professional</button>
                    <button type="button" onclick="coraFilterTemplates('creative', this)" class="cora-tpl-tab px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 cursor-pointer shrink-0">Creative</button>
                </div>
            </div>

            <div id="wiz-subpage-template-gallery" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-1">
                <!-- Populated by JS -->
            </div>
        </div>

        <!-- STEP 3 SUB-PAGE: BUILD DOCUMENT (DOCUMENT BUILDER MAIN CANVAS) -->
        <div id="sub-page-wiz-step-3" class="hidden space-y-6 w-full max-w-none pb-24 sm:pb-28">
            
            <!-- LAYOUT DIAGNOSTIC DEBUGGER -->
            <div id="cora-layout-debugger" class="bg-zinc-900 text-white p-3.5 rounded-2xl text-[11px] font-mono space-y-1 shadow-md border border-zinc-700/50 max-w-sm">
                <div class="font-extrabold uppercase text-[9px] text-zinc-400 tracking-wider mb-1">Layout Diagnostics</div>
                <div>Viewport Width: <span id="db-viewport" class="font-bold text-emerald-400">--</span></div>
                <div>Sidebar Client Width: <span id="db-sidebar" class="font-bold text-emerald-400">--</span></div>
                <div>Step 3 Wrapper Width: <span id="db-container" class="font-bold text-emerald-400">--</span></div>
                <div>Match Media (min-width: 768px): <span id="db-md" class="font-bold text-emerald-400">--</span></div>
                <div>Match Media (min-width: 1024px): <span id="db-lg" class="font-bold text-emerald-400">--</span></div>
            </div>
            <script>
            setInterval(function() {
                var sb = document.getElementById('step3-left-sidebar');
                var co = document.getElementById('sub-page-wiz-step-3');
                var dbV = document.getElementById('db-viewport');
                var dbS = document.getElementById('db-sidebar');
                var dbC = document.getElementById('db-container');
                var dbM = document.getElementById('db-md');
                var dbL = document.getElementById('db-lg');
                if (dbV) dbV.textContent = window.innerWidth + 'px';
                if (dbS) dbS.textContent = sb ? sb.clientWidth + 'px (computed: ' + window.getComputedStyle(sb).width + ')' : 'none';
                if (dbC) dbC.textContent = co ? co.clientWidth + 'px' : 'none';
                if (dbM) dbM.textContent = window.matchMedia('(min-width: 768px)').matches;
                if (dbL) dbL.textContent = window.matchMedia('(min-width: 1024px)').matches;
            }, 500);
            </script>

            <!-- 1. TOP TOOLBAR FOR STEP 3 -->
            <div class="bg-white border border-zinc-200/80 rounded-2xl p-4 shadow-xs flex flex-wrap items-center justify-between gap-4">
                <!-- Left: Doc Title + Save status -->
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" id="studio-doc-title-input" name="studio_doc_title" value="Modern Proposal" placeholder="Document Title..." oninput="coraSyncCanvasFields()" class="text-sm md:text-base font-black text-zinc-950 bg-transparent border border-zinc-200/80 hover:border-zinc-400 focus:border-zinc-950 focus:bg-white rounded-xl px-3.5 py-2 outline-none transition-all w-60 md:w-80 shadow-xs">
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/80 text-[11px] font-semibold shrink-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="studio-save-status-text">All changes saved</span>
                    </span>
                </div>

                <!-- Right: Action Buttons -->
                <div class="flex items-center gap-2">
                    <!-- Sidebar Toggle Button -->
                    <div class="flex items-center border-r border-zinc-200 pr-2">
                        <button type="button" onclick="coraToggleStep3Sidebar('left')" id="btn-toggle-left-sidebar" title="Toggle Component Library" class="px-3 py-2 rounded-xl text-xs font-bold text-white bg-zinc-950 border border-zinc-950 transition-colors cursor-pointer flex items-center gap-1.5 shadow-xs">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                            <span class="hidden sm:inline">Component Library</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-1 border-r border-zinc-200 pr-2">
                        <button type="button" onclick="coraCanvasUndo()" title="Undo" class="p-2 rounded-xl text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 transition-colors border border-zinc-200/60 cursor-pointer">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 7v6h6"></path><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"></path></svg>
                        </button>
                        <button type="button" onclick="coraCanvasRedo()" title="Redo" class="p-2 rounded-xl text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 transition-colors border border-zinc-200/60 cursor-pointer">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 7v6h-6"></path><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l3 2.7"></path></svg>
                        </button>
                    </div>

                    <button type="button" onclick="coraTriggerAIAssistant()" class="px-3.5 py-2 bg-zinc-900 hover:bg-black text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs transition-all cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                        AI Assistant
                    </button>

                    <button type="button" onclick="coraOpenDocSettingsDrawer()" class="px-3.5 py-2 bg-white border border-zinc-200 text-zinc-800 hover:bg-zinc-50 rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs transition-all cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        Settings
                    </button>

                    <button type="button" onclick="coraSaveStudioDocument()" class="px-4 py-2 bg-zinc-950 hover:bg-black text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs transition-all cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Save Draft
                    </button>
                </div>
            </div>

            <!-- MAIN STEP 3 WORKSPACE: DOCKED TABBED SIDEBAR & CANVAS CONTAINER -->
            <div class="flex flex-col md:flex-row items-start gap-4 w-full relative min-w-0 min-h-[950px]">

                <!-- 2. LEFT PANEL - COMPONENT LIBRARY & DOCUMENT OUTLINE (DOCKED SIDEBAR) -->
                <div id="step3-left-sidebar" class="w-full md:w-[280px] md:shrink-0 bg-white border border-zinc-200/80 rounded-3xl p-5 shadow-xs space-y-4 md:sticky md:top-4 self-start pointer-events-auto">
                    <!-- Segmented Tab Header -->
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3 gap-2">
                        <div class="flex items-center bg-zinc-100 p-1 rounded-xl w-full">
                            <button type="button" id="step3-tab-btn-components" onclick="coraSwitchStep3SidebarTab('components')" class="flex-1 py-1.5 px-2 rounded-lg text-xs font-bold transition-all bg-white text-zinc-950 shadow-xs cursor-pointer text-center whitespace-nowrap">
                                Component Library
                            </button>
                            <button type="button" id="step3-tab-btn-outline" onclick="coraSwitchStep3SidebarTab('outline')" class="flex-1 py-1.5 px-2 rounded-lg text-xs font-semibold transition-all text-zinc-500 hover:text-zinc-900 cursor-pointer text-center whitespace-nowrap">
                                Document Outline
                            </button>
                        </div>
                        <button type="button" onclick="coraToggleStep3Sidebar('left')" title="Close Sidebar" class="w-7 h-7 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-600 hover:text-zinc-950 flex items-center justify-center transition-colors cursor-pointer shrink-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>

                    <!-- TAB 1: COMPONENT LIBRARY BLOCKS -->
                    <div id="step3-sidebar-content-components" class="space-y-4">
                        <p class="text-[11px] text-zinc-500">Click or drag blocks to add to canvas.</p>
                        <!-- Library Groups -->
                        <div class="space-y-4 text-xs">
                            <!-- Group 1: Basic Blocks -->
                            <div class="border border-zinc-200 rounded-2xl overflow-hidden">
                                <div class="bg-zinc-50 px-3.5 py-2.5 font-bold text-zinc-900 flex items-center justify-between border-b border-zinc-200">
                                    <span class="text-xs font-extrabold uppercase tracking-wider text-zinc-800">Basic Blocks</span>
                                    <span class="text-[10px] bg-zinc-200 text-zinc-700 font-mono px-1.5 py-0.5 rounded">6</span>
                                </div>
                                <div class="p-2.5 grid grid-cols-2 gap-2 bg-white">
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'text')" onclick="coraAddCanvasBlock('text')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex flex-col items-center gap-1.5 text-center transition-all cursor-grab">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg>
                                        <span class="text-[11px] font-semibold text-zinc-800">Text</span>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'heading')" onclick="coraAddCanvasBlock('heading')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex flex-col items-center gap-1.5 text-center transition-all cursor-grab">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700"><path d="M6 4v16M18 4v16M6 12h12"></path></svg>
                                        <span class="text-[11px] font-semibold text-zinc-800">Heading</span>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'image')" onclick="coraAddCanvasBlock('image')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex flex-col items-center gap-1.5 text-center transition-all cursor-grab">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                        <span class="text-[11px] font-semibold text-zinc-800">Image</span>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'table')" onclick="coraAddCanvasBlock('table')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex flex-col items-center gap-1.5 text-center transition-all cursor-grab">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M3 9h18M3 15h18M9 3v18"></path></svg>
                                        <span class="text-[11px] font-semibold text-zinc-800">Table</span>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'divider')" onclick="coraAddCanvasBlock('divider')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex flex-col items-center gap-1.5 text-center transition-all cursor-grab">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                        <span class="text-[11px] font-semibold text-zinc-800">Divider</span>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'spacer')" onclick="coraAddCanvasBlock('spacer')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex flex-col items-center gap-1.5 text-center transition-all cursor-grab">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700"><path d="M12 5v14M5 12h14"></path></svg>
                                        <span class="text-[11px] font-semibold text-zinc-800">Spacer</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Group 2: Smart Blocks -->
                            <div class="border border-zinc-200 rounded-2xl overflow-hidden">
                                <div class="bg-zinc-50 px-3.5 py-2.5 font-bold text-zinc-900 flex items-center justify-between border-b border-zinc-200">
                                    <span class="text-xs font-extrabold uppercase tracking-wider text-zinc-800">Smart Blocks</span>
                                    <span class="text-[10px] bg-zinc-200 text-zinc-700 font-mono px-1.5 py-0.5 rounded">5</span>
                                </div>
                                <div class="p-2.5 grid grid-cols-1 gap-2 bg-white">
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'services_table')" onclick="coraAddCanvasBlock('services_table')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex items-center gap-3 transition-all cursor-grab text-left">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 text-zinc-800 font-extrabold text-xs select-none">
                                            ₹
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-zinc-900">Services Table</div>
                                            <div class="text-[10px] text-zinc-500">Structured service deliverables matrix</div>
                                        </div>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'pricing_table')" onclick="coraAddCanvasBlock('pricing_table')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex items-center gap-3 transition-all cursor-grab text-left">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 text-zinc-800">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-zinc-900">Pricing Table</div>
                                            <div class="text-[10px] text-zinc-500">Itemized rates & GST calculation matrix</div>
                                        </div>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'timeline')" onclick="coraAddCanvasBlock('timeline')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex items-center gap-3 transition-all cursor-grab text-left">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 text-zinc-800">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-zinc-900">Timeline</div>
                                            <div class="text-[10px] text-zinc-500">Project shoot & delivery milestones</div>
                                        </div>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'testimonial')" onclick="coraAddCanvasBlock('testimonial')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex items-center gap-3 transition-all cursor-grab text-left">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 text-zinc-800">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-zinc-900">Testimonial</div>
                                            <div class="text-[10px] text-zinc-500">Client endorsement & review quote</div>
                                        </div>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'faq')" onclick="coraAddCanvasBlock('faq')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex items-center gap-3 transition-all cursor-grab text-left">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 text-zinc-800">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-zinc-900">FAQ Section</div>
                                            <div class="text-[10px] text-zinc-500">Frequently asked questions accordion</div>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Group 3: Building Blocks -->
                            <div class="border border-zinc-200 rounded-2xl overflow-hidden">
                                <div class="bg-zinc-50 px-3.5 py-2.5 font-bold text-zinc-900 flex items-center justify-between border-b border-zinc-200">
                                    <span class="text-xs font-extrabold uppercase tracking-wider text-zinc-800">Building Blocks</span>
                                    <span class="text-[10px] bg-zinc-200 text-zinc-700 font-mono px-1.5 py-0.5 rounded">4</span>
                                </div>
                                <div class="p-2.5 grid grid-cols-1 gap-2 bg-white">
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'cover')" onclick="coraAddCanvasBlock('cover')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex items-center gap-3 transition-all cursor-grab text-left">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 text-zinc-800">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-zinc-900">Cover Page</div>
                                            <div class="text-[10px] text-zinc-500">Title hero & branding cover block</div>
                                        </div>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'client_info')" onclick="coraAddCanvasBlock('client_info')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex items-center gap-3 transition-all cursor-grab text-left">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 text-zinc-800">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-zinc-900">Client Info</div>
                                            <div class="text-[10px] text-zinc-500">Client contact & GST metadata section</div>
                                        </div>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'signature_info')" onclick="coraAddCanvasBlock('signature_info')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex items-center gap-3 transition-all cursor-grab text-left">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 text-zinc-800">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg>
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-zinc-900">Signature Info</div>
                                            <div class="text-[10px] text-zinc-500">Dual E-signature sign-off box</div>
                                        </div>
                                    </button>
                                    <button type="button" draggable="true" ondragstart="coraDragBlockStart(event, 'file_upload')" onclick="coraAddCanvasBlock('file_upload')" class="p-2.5 rounded-xl border border-zinc-200 hover:border-zinc-950 hover:bg-zinc-50 flex items-center gap-3 transition-all cursor-grab text-left">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 text-zinc-800">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-zinc-900">File Upload</div>
                                            <div class="text-[10px] text-zinc-500">Attachment dropzone for client files</div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: DOCUMENT OUTLINE & CANVAS ZOOM CONTROLS -->
                    <div id="step3-sidebar-content-outline" class="hidden space-y-5">
                        <p class="text-[11px] text-zinc-500">Click any section to jump directly to it.</p>
                        <div class="space-y-1 text-xs">
                            <button type="button" onclick="coraScrollToSection('sec-cover')" class="w-full text-left px-3 py-2 rounded-xl text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer">
                                <span class="w-5 font-mono text-[10px] text-zinc-400">01</span>
                                <span>Cover Page</span>
                            </button>
                            <button type="button" onclick="coraScrollToSection('sec-client-info')" class="w-full text-left px-3 py-2 rounded-xl text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer">
                                <span class="w-5 font-mono text-[10px] text-zinc-400">02</span>
                                <span>Client Metadata</span>
                            </button>
                            <button type="button" onclick="coraScrollToSection('sec-about')" class="w-full text-left px-3 py-2 rounded-xl text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer">
                                <span class="w-5 font-mono text-[10px] text-zinc-400">03</span>
                                <span>About Us</span>
                            </button>
                            <button type="button" onclick="coraScrollToSection('sec-pricing')" class="w-full text-left px-3 py-2 rounded-xl text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer">
                                <span class="w-5 font-mono text-[10px] text-zinc-400">04</span>
                                <span>Services & Pricing</span>
                            </button>
                            <button type="button" onclick="coraScrollToSection('sec-timeline')" class="w-full text-left px-3 py-2 rounded-xl text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer">
                                <span class="w-5 font-mono text-[10px] text-zinc-400">05</span>
                                <span>Timeline</span>
                            </button>
                            <button type="button" onclick="coraScrollToSection('sec-terms')" class="w-full text-left px-3 py-2 rounded-xl text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer">
                                <span class="w-5 font-mono text-[10px] text-zinc-400">06</span>
                                <span>Terms & Conditions</span>
                            </button>
                            <button type="button" onclick="coraScrollToSection('sec-payment')" class="w-full text-left px-3 py-2 rounded-xl text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer">
                                <span class="w-5 font-mono text-[10px] text-zinc-400">07</span>
                                <span>Payment Terms</span>
                            </button>
                            <button type="button" onclick="coraScrollToSection('sec-next-steps')" class="w-full text-left px-3 py-2 rounded-xl text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer">
                                <span class="w-5 font-mono text-[10px] text-zinc-400">08</span>
                                <span>Next Steps</span>
                            </button>
                            <button type="button" onclick="coraScrollToSection('sec-signature')" class="w-full text-left px-3 py-2 rounded-xl text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 font-semibold flex items-center gap-2 transition-colors cursor-pointer">
                                <span class="w-5 font-mono text-[10px] text-zinc-400">09</span>
                                <span>Signature</span>
                            </button>
                        </div>

                        <!-- Canvas Zoom Controls -->
                        <div class="border-t border-zinc-200 pt-5 space-y-3">
                            <h4 class="text-xs font-extrabold text-zinc-950 uppercase tracking-wider flex items-center gap-2">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                                Canvas Zoom
                            </h4>
                            <div class="flex items-center justify-between bg-zinc-50 border border-zinc-200 rounded-xl p-2">
                                <button type="button" onclick="coraChangeZoom(-10)" title="Zoom Out" class="w-8 h-8 rounded-lg bg-white border border-zinc-200 hover:bg-zinc-100 flex items-center justify-center font-bold text-sm text-zinc-800 transition-colors shadow-xs cursor-pointer">
                                    -
                                </button>
                                <span id="zoom-percentage-text" class="text-xs font-mono font-bold text-zinc-900">100%</span>
                                <button type="button" onclick="coraChangeZoom(10)" title="Zoom In" class="w-8 h-8 rounded-lg bg-white border border-zinc-200 hover:bg-zinc-100 flex items-center justify-center font-bold text-sm text-zinc-800 transition-colors shadow-xs cursor-pointer">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. CENTER CANVAS (LIVE INTERACTIVE PAPER DOCUMENT - FULL REMAINING SPACE) -->
                <div class="flex-1 min-w-0 min-h-[950px] relative overflow-x-auto" ondragover="event.preventDefault()" ondrop="coraHandleCanvasDrop(event)">
                    
                    <!-- Live Paper Canvas Document Container -->
                    <div id="studio-center-paper-doc" class="w-full bg-white shadow-xs rounded-3xl p-6 sm:p-10 space-y-8 font-sans transition-all duration-200 border border-zinc-200/80 min-h-[950px] text-zinc-800" style="transform-origin: top center; transform: scale(1);">

                        <!-- SECTION 1: COVER PAGE HEADER & LOGO DROPZONE -->
                        <div id="sec-cover" class="space-y-6 border-b border-zinc-200/80 pb-8">
                            <!-- Logo Upload Dropzone -->
                            <div id="cora-logo-dropzone" class="border-2 border-dashed border-zinc-200 rounded-2xl p-5 text-center cursor-pointer hover:border-zinc-950 bg-zinc-50/50 transition-all flex flex-col items-center justify-center gap-2 group" onclick="document.getElementById('cora-logo-file-input').click()">
                                <input type="file" id="cora-logo-file-input" class="hidden" accept="image/*" onchange="coraHandleLogoUpload(this)">
                                <div id="cora-logo-preview" class="flex flex-col items-center gap-2">
                                    <div class="w-10 h-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center font-bold text-sm">
                                        C
                                    </div>
                                    <span class="text-xs font-bold text-zinc-700 group-hover:text-zinc-950">Click or drag studio logo here</span>
                                    <span class="text-[10px] text-zinc-400">PNG, SVG or JPG (Max 2MB)</span>
                                </div>
                            </div>

                            <!-- Proposal Title Dynamic Display -->
                            <div class="space-y-2">
                                <h1 id="canvas-proposal-title" class="text-2xl sm:text-3xl font-black text-zinc-950 tracking-tight leading-tight">
                                    Proposal for <span id="canvas-client-name-display" class="underline decoration-zinc-300 underline-offset-4">{{Client Name}}</span>
                                </h1>
                                <div class="flex flex-wrap items-center justify-between text-xs text-zinc-500 font-medium pt-2 border-t border-zinc-100">
                                    <span>Prepared by: <strong class="text-zinc-900" id="canvas-prepared-by">Cora Studio Workspace</strong></span>
                                    <span>Date: <strong class="text-zinc-900" id="canvas-doc-date"><?php echo date('d M Y'); ?></strong></span>
                                </div>
                            </div>
                        </div>

                        <!-- CLIENT & PROJECT METADATA INPUT SECTION -->
                        <div id="sec-client-info" class="bg-zinc-50/80 border border-zinc-200/80 rounded-2xl p-5 space-y-4">
                            <h4 class="text-xs font-extrabold text-zinc-950 uppercase tracking-wider flex items-center justify-between">
                                <span>Client & Project Metadata</span>
                                <span class="text-[10px] font-normal text-zinc-500 lowercase">syncs live to canvas</span>
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div class="space-y-1">
                                    <label class="block font-bold text-zinc-700 text-[11px]">Document Reference #</label>
                                    <input type="text" id="studio-doc-number" name="studio_doc_number" value="DOC-2026" oninput="coraSyncCanvasFields()" class="w-full border border-zinc-200 rounded-xl p-2.5 bg-white outline-none focus:border-zinc-950 transition-colors font-mono font-bold">
                                </div>

                                <div class="space-y-1">
                                    <label class="block font-bold text-zinc-700 text-[11px]">Client Full Name / Company *</label>
                                    <input type="text" id="studio-client-name" name="studio_client_name" placeholder="e.g. Arjun & Priya / Apex Realty" oninput="coraSyncCanvasFields()" class="w-full border border-zinc-200 rounded-xl p-2.5 bg-white outline-none focus:border-zinc-950 transition-colors font-semibold">
                                </div>

                                <div class="space-y-1">
                                    <label class="block font-bold text-zinc-700 text-[11px]">Client Email *</label>
                                    <input type="email" id="studio-client-email" name="studio_client_email" placeholder="client@example.com" oninput="coraSyncCanvasFields()" class="w-full border border-zinc-200 rounded-xl p-2.5 bg-white outline-none focus:border-zinc-950 transition-colors">
                                </div>

                                <div class="space-y-1">
                                    <label class="block font-bold text-zinc-700 text-[11px]">WhatsApp Phone Number</label>
                                    <input type="text" id="studio-client-phone" name="studio_client_phone" placeholder="9876543210" oninput="coraSyncCanvasFields()" class="w-full border border-zinc-200 rounded-xl p-2.5 bg-white outline-none focus:border-zinc-950 transition-colors font-mono">
                                </div>

                                <div class="space-y-1 sm:col-span-2">
                                    <label class="block font-bold text-zinc-700 text-[11px]">Place of Supply (POS State for GST)</label>
                                    <select id="studio-doc-pos" onchange="coraRecalculateStudioTotals()" class="w-full border border-zinc-200 rounded-xl p-2.5 bg-white outline-none focus:border-zinc-950 transition-colors font-semibold">
                                        <option value="Delhi (07)">Delhi (07) - CGST (9%) + SGST (9%)</option>
                                        <option value="Haryana (06)">Haryana (06) - IGST (18%)</option>
                                        <option value="Maharashtra (27)">Maharashtra (27) - IGST (18%)</option>
                                        <option value="Karnataka (29)">Karnataka (29) - IGST (18%)</option>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" id="studio-doc-type" value="Proposal">
                            <input type="hidden" id="studio-doc-status" value="Draft">
                            <input type="hidden" id="studio-client-gstin" name="studio_client_gstin" value="">
                            <input type="hidden" id="studio-doc-upi" value="cora@icici">
                        </div>

                        <!-- SECTION 2: ABOUT US -->
                        <div id="sec-about" class="space-y-3 pt-2">
                            <h3 class="text-base font-black text-zinc-950 uppercase tracking-wider border-b border-zinc-100 pb-2">About Us</h3>
                            <div id="canvas-about-text" contenteditable="true" class="text-xs text-zinc-600 leading-relaxed p-3.5 bg-zinc-50/50 hover:bg-zinc-50 border border-transparent hover:border-zinc-200 rounded-xl transition-all outline-none focus:bg-white focus:border-zinc-950">
                                Cora Studio is a premier media production house and creative content collective. We produce high-impact commercial films, architectural imagery, real estate 4K walkthroughs, and brand campaign visual assets tailored to elevate your business presence.
                            </div>
                        </div>

                        <!-- SECTION 3: SERVICES & PRICING TABLE MATRIX -->
                        <div id="sec-pricing" class="space-y-4 pt-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-black text-zinc-950 uppercase tracking-wider">Services & Pricing Table</h3>
                                <button type="button" onclick="coraAddStudioLineItem()" class="px-3.5 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 cursor-pointer shadow-xs flex items-center gap-1.5 transition-all">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    + Add Row
                                </button>
                            </div>

                            <!-- 1-CLICK AUTO-FILL PRESETS TOOLBAR -->
                            <div class="bg-zinc-950 text-white p-4.5 rounded-2xl shadow-sm space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-zinc-200">⚡ 1-Click Package Auto-Fill</h4>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs text-zinc-900">
                                    <div>
                                        <label class="block text-[9px] font-bold text-zinc-400 uppercase mb-1">1. Package Preset</label>
                                        <select id="auto-pkg-select" onchange="coraAutoFillPackage(this.value)" class="w-full border-0 rounded-xl p-2 bg-white outline-none font-semibold text-xs">
                                            <option value="">-- Package Preset --</option>
                                            <option value="wedding">Wedding Cinematography (₹4.5L)</option>
                                            <option value="realty">Real Estate 4K HDR (₹1.2L)</option>
                                            <option value="event">Commercial Summit (₹3.5L)</option>
                                            <option value="retainer">Monthly Retainer (₹1.5L)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[9px] font-bold text-zinc-400 uppercase mb-1">2. Equipment Rental</label>
                                        <select id="auto-gear-select" onchange="coraAutoAddGear(this.value)" class="w-full border-0 rounded-xl p-2 bg-white outline-none font-semibold text-xs">
                                            <option value="">+ Add Camera Gear</option>
                                            <option value="red_komodo">RED Komodo 6K (₹15k/day)</option>
                                            <option value="sony_a7s3">Sony A7S III Kit (₹8k/day)</option>
                                            <option value="dji_drone">DJI Mavic 3 Drone (₹12k/day)</option>
                                            <option value="aputure_light">Aputure 600d Kit (₹6k/day)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[9px] font-bold text-zinc-400 uppercase mb-1">3. Team Crew Role</label>
                                        <select id="auto-crew-select" onchange="coraAutoAddCrew(this.value)" class="w-full border-0 rounded-xl p-2 bg-white outline-none font-semibold text-xs">
                                            <option value="">+ Add Crew Role</option>
                                            <option value="lead_photog">Lead Cinematographer (₹25k/day)</option>
                                            <option value="assoc_photog">Associate Photog (₹15k/day)</option>
                                            <option value="drone_pilot">Drone Pilot (₹18k/day)</option>
                                            <option value="sr_editor">Senior Editor (₹35k/project)</option>
                                            <option value="grip_assistant">Lighting Grip (₹5k/day)</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[9px] font-bold text-zinc-400 uppercase mb-1">4. Days Multiplier</label>
                                        <select id="auto-days-multiplier" onchange="coraApplyShootDaysMultiplier(this.value)" class="w-full border-0 rounded-xl p-2 bg-white outline-none font-bold text-xs">
                                            <option value="1">1 Shoot Day (1x)</option>
                                            <option value="2">2 Shoot Days (2x)</option>
                                            <option value="3" selected>3 Shoot Days (3x)</option>
                                            <option value="5">5 Shoot Days (5x)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Line Items Table -->
                            <div class="border border-zinc-200 rounded-2xl overflow-x-auto shadow-xs">
                                <table class="w-full text-left border-collapse text-xs min-w-[500px]">
                                    <thead>
                                        <tr class="bg-zinc-100/90 border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase">
                                            <th class="p-3 w-5/12">Item Description & Scope</th>
                                            <th class="p-3">SAC</th>
                                            <th class="p-3 w-16">Qty</th>
                                            <th class="p-3">Rate (₹)</th>
                                            <th class="p-3">GST %</th>
                                            <th class="p-3 text-right">Amount (₹)</th>
                                            <th class="p-3 text-center w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="studio-line-items-body" class="divide-y divide-zinc-100 bg-white">
                                        <!-- Dynamic Line Rows -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Financial Summary Card -->
                            <div class="flex justify-end pt-2">
                                <div class="w-full sm:w-80 bg-zinc-50 p-5 rounded-2xl border border-zinc-200/80 space-y-2.5 text-xs">
                                    <div class="flex justify-between text-zinc-600">
                                        <span>Taxable Subtotal:</span>
                                        <span id="summary-subtotal" class="font-mono font-bold text-zinc-900">₹0</span>
                                    </div>

                                    <div id="row-cgst" class="flex justify-between text-zinc-600">
                                        <span>CGST (9%):</span>
                                        <span id="summary-cgst" class="font-mono font-bold text-zinc-900">₹0</span>
                                    </div>

                                    <div id="row-sgst" class="flex justify-between text-zinc-600">
                                        <span>SGST (9%):</span>
                                        <span id="summary-sgst" class="font-mono font-bold text-zinc-900">₹0</span>
                                    </div>

                                    <div id="row-igst" class="hidden flex justify-between text-zinc-600">
                                        <span>IGST (18%):</span>
                                        <span id="summary-igst" class="font-mono font-bold text-zinc-900">₹0</span>
                                    </div>

                                    <div class="border-t border-zinc-200 pt-3 flex justify-between text-sm font-black text-zinc-950">
                                        <span>Grand Total (Incl. GST):</span>
                                        <span id="summary-grandtotal" class="font-mono">₹0</span>
                                    </div>

                                    <div class="flex justify-between text-zinc-950 text-xs font-bold pt-1 border-t border-zinc-200/60">
                                        <span>Retainer Deposit Due (50%):</span>
                                        <span id="summary-deposit" class="font-mono text-zinc-950">₹0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 4: TIMELINE -->
                        <div id="sec-timeline" class="space-y-3 pt-4 border-t border-zinc-100">
                            <h3 class="text-base font-black text-zinc-950 uppercase tracking-wider">Project Timeline & Milestones</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                                <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-xl space-y-1">
                                    <span class="text-[9px] font-extrabold text-zinc-400 uppercase">Phase 1</span>
                                    <div class="font-bold text-zinc-900">Pre-Production</div>
                                    <p class="text-[11px] text-zinc-500">Concept, location scouting & shotlists.</p>
                                </div>
                                <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-xl space-y-1">
                                    <span class="text-[9px] font-extrabold text-zinc-400 uppercase">Phase 2</span>
                                    <div class="font-bold text-zinc-900">Principal Photography</div>
                                    <p class="text-[11px] text-zinc-500">On-location 4K shoot & drone capture.</p>
                                </div>
                                <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-xl space-y-1">
                                    <span class="text-[9px] font-extrabold text-zinc-400 uppercase">Phase 3</span>
                                    <div class="font-bold text-zinc-900">Post-Production</div>
                                    <p class="text-[11px] text-zinc-500">Color grading, audio mix & edit drafts.</p>
                                </div>
                                <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-xl space-y-1">
                                    <span class="text-[9px] font-extrabold text-zinc-400 uppercase">Phase 4</span>
                                    <div class="font-bold text-zinc-900">Final Delivery</div>
                                    <p class="text-[11px] text-zinc-500">4K master files & copyright release.</p>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 5: TERMS & CONDITIONS -->
                        <div id="sec-terms" class="space-y-3 pt-4 border-t border-zinc-100">
                            <h3 class="text-base font-black text-zinc-950 uppercase tracking-wider">Terms & Conditions</h3>
                            <ul class="list-disc list-inside text-xs text-zinc-600 space-y-1.5 leading-relaxed bg-zinc-50/50 p-4 rounded-xl border border-zinc-100">
                                <li>All raw footage and media assets remain intellectual property of Cora Studio until final payment clearance.</li>
                                <li>Client receives commercial usage license for digital and broadcast platforms upon settlement.</li>
                                <li>Cancellations within 72 hours of scheduled shoot dates forfeit the 50% advance retainer deposit.</li>
                            </ul>
                        </div>

                        <!-- SECTION 6: PAYMENT TERMS -->
                        <div id="sec-payment" class="space-y-3 pt-4 border-t border-zinc-100">
                            <h3 class="text-base font-black text-zinc-950 uppercase tracking-wider">Payment Schedule</h3>
                            <div class="grid grid-cols-3 gap-3 text-xs text-center">
                                <div class="p-3 bg-zinc-950 text-white rounded-xl font-medium">
                                    <div class="text-[10px] text-zinc-400 uppercase font-bold">50% Advance</div>
                                    <div class="font-bold text-sm mt-0.5">Booking Retainer</div>
                                </div>
                                <div class="p-3 bg-zinc-100 border border-zinc-200 text-zinc-900 rounded-xl font-medium">
                                    <div class="text-[10px] text-zinc-500 uppercase font-bold">40% Milestone</div>
                                    <div class="font-bold text-sm mt-0.5">Shoot Wrap</div>
                                </div>
                                <div class="p-3 bg-zinc-100 border border-zinc-200 text-zinc-900 rounded-xl font-medium">
                                    <div class="text-[10px] text-zinc-500 uppercase font-bold">10% Final</div>
                                    <div class="font-bold text-sm mt-0.5">Delivery Release</div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 7: NEXT STEPS -->
                        <div id="sec-next-steps" class="space-y-3 pt-4 border-t border-zinc-100">
                            <h3 class="text-base font-black text-zinc-950 uppercase tracking-wider">Next Steps</h3>
                            <p class="text-xs text-zinc-600 leading-relaxed">
                                To accept this proposal and lock in shoot dates, please review the scope and digitally sign below. Our team will issue the deposit invoice immediately.
                            </p>
                        </div>

                        <!-- SECTION 8: SIGNATURE -->
                        <div id="sec-signature" class="space-y-4 pt-6 border-t border-zinc-200/80">
                            <h3 class="text-base font-black text-zinc-950 uppercase tracking-wider">Authorization & E-Signature</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                                <div class="p-4 bg-zinc-50 border border-zinc-200 rounded-2xl space-y-3">
                                    <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Prepared By (Studio Representative)</span>
                                    <div class="h-16 border-b border-dashed border-zinc-300 flex items-end pb-1 font-serif text-sm italic text-zinc-800">
                                        Cora Studio Executive
                                    </div>
                                    <div class="text-[11px] text-zinc-500 font-medium">Date: <?php echo date('d M Y'); ?></div>
                                </div>

                                <div class="p-4 bg-zinc-50 border border-zinc-200 rounded-2xl space-y-3">
                                    <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Client Acceptance & Signature</span>
                                    <div id="canvas-client-sig-box" class="h-16 border-b border-dashed border-zinc-300 flex items-end pb-1 text-zinc-400 text-xs italic">
                                        Pending Client Signature
                                    </div>
                                    <div class="text-[11px] text-zinc-500 font-medium">Client: <span id="canvas-client-sig-name">{{Client Name}}</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- DYNAMIC DRAGGED/CLICKED BLOCKS CONTAINER -->
                        <div id="canvas-dynamic-blocks-wrapper" class="space-y-4 pt-4 border-t-2 border-dashed border-zinc-200">
                            <div class="flex items-center justify-between text-[11px] text-zinc-400 font-bold uppercase tracking-wider">
                                <span>Custom Canvas Blocks</span>
                                <span class="lowercase text-[10px] font-normal">Blocks added from library appear here</span>
                            </div>
                            <div id="canvas-dynamic-blocks" class="space-y-4">
                                <!-- Dynamic blocks appended here -->
                            </div>
                        </div>

                    </div>
                </div>



            </div>
        </div>

        <!-- STEP 4 SUB-PAGE: PREVIEW & EDIT -->
        <div id="sub-page-wiz-step-4" class="hidden space-y-6 w-full max-w-none pb-24 sm:pb-28">
            <!-- Top Action Bar -->
            <div class="bg-white p-6 rounded-3xl border border-zinc-200/80 flex items-center justify-between flex-wrap gap-4 shadow-xs">
                <div>
                    <span class="px-3.5 py-1.5 rounded-full bg-zinc-100 text-zinc-600 text-[11px] font-bold uppercase tracking-widest inline-block border border-zinc-200 mb-0.5">Step 4 of 6</span>
                    <h3 class="text-lg font-black text-zinc-950">Preview & Edit Document</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Review live paper layout, customize visual aesthetics, and fine-tune styling.</p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" id="btn-toggle-step4-pages" onclick="coraToggleStep4Sidebar('pages')" class="px-4 py-2.5 bg-white border border-zinc-300 hover:bg-zinc-100 text-zinc-900 rounded-xl text-xs font-bold cursor-pointer shadow-xs flex items-center gap-1.5 transition-all">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                        Pages (<span id="wiz-top-page-count">3</span>)
                    </button>
                    <button type="button" id="btn-toggle-step4-edit" onclick="coraToggleStep4Sidebar('edit')" class="px-4 py-2.5 bg-white border border-zinc-300 hover:bg-zinc-100 text-zinc-900 rounded-xl text-xs font-bold cursor-pointer shadow-xs flex items-center gap-1.5 transition-all">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        Quick Edit
                    </button>
                    <button type="button" onclick="coraJumpToWizardStep(3)" class="px-4 py-2.5 bg-white border border-zinc-300 hover:bg-zinc-100 text-zinc-900 rounded-xl text-xs font-bold cursor-pointer shadow-xs flex items-center gap-1.5 transition-all">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        Edit Document
                    </button>
                    <button type="button" onclick="coraPrintInvoiceOnly()" class="px-4 py-2.5 bg-white border border-zinc-300 hover:bg-zinc-100 text-zinc-900 rounded-xl text-xs font-bold cursor-pointer shadow-xs flex items-center gap-1.5 transition-all">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Print / Export PDF
                    </button>
                    <button type="button" onclick="coraJumpToWizardStep(5)" class="px-6 py-2.5 bg-zinc-950 hover:bg-black text-white rounded-xl text-xs font-bold cursor-pointer shadow-xs flex items-center gap-1.5 transition-all">
                        Proceed to Generate →
                    </button>
                </div>
            </div>

            <!-- PREVIEW WORKSPACE 3-COLUMN SIDEBAR & CANVAS CONTAINER -->
            <div class="flex flex-col md:flex-row items-start gap-3.5 w-full relative min-w-0">

                <!-- Left Panel: Pages Sidebar (Fixed 240px width - Docked Left Sidebar) -->
                <div id="step4-left-pages-sidebar" class="w-full md:w-[240px] md:shrink-0 bg-white border border-zinc-200/80 rounded-3xl p-5 shadow-xs space-y-4 md:sticky md:top-4 self-start pointer-events-auto">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <h4 class="font-extrabold text-zinc-950 text-xs uppercase tracking-wider flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                            PAGES ( <span id="wiz-page-count">3</span> )
                        </h4>
                    </div>

                    <!-- Page Thumbnails List (Real Content Previews) -->
                    <div id="wiz-page-thumbnails-container" class="space-y-3 relative z-10">
                        <!-- Page 1: Scope & Header -->
                        <div onclick="coraSelectPageThumbnail(1)" id="wiz-thumb-1" class="p-3 bg-white border border-zinc-200/80 rounded-2xl cursor-pointer transition-all hover:bg-zinc-50 group relative z-10">
                            <div class="aspect-[1/0.5] bg-zinc-50 border border-zinc-200/60 rounded-xl p-2 flex flex-col justify-between mb-2 relative overflow-hidden pointer-events-none">
                                <div class="flex items-center justify-between">
                                    <div class="w-3.5 h-3.5 rounded bg-zinc-950 text-white font-black text-[7px] flex items-center justify-center">C</div>
                                    <div class="w-6 h-1 bg-zinc-300 rounded-full"></div>
                                </div>
                                <div class="space-y-1 my-1">
                                    <div class="w-3/4 h-1 bg-zinc-400 rounded-full"></div>
                                    <div class="w-1/2 h-1 bg-zinc-300 rounded-full"></div>
                                </div>
                                <div class="w-full h-1 bg-zinc-200 rounded-full"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs pointer-events-none">
                                <span class="font-bold text-zinc-950">Page 1</span>
                                <span class="text-[10px] text-zinc-400 font-mono">Scope</span>
                            </div>
                        </div>

                        <!-- Page 2: Line Items Table -->
                        <div onclick="coraSelectPageThumbnail(2)" id="wiz-thumb-2" class="p-3 bg-white border border-zinc-200/80 rounded-2xl cursor-pointer transition-all hover:bg-zinc-50 group relative z-10">
                            <div class="aspect-[1/0.5] bg-zinc-50 border border-zinc-200/60 rounded-xl p-2 flex flex-col justify-between mb-2 relative overflow-hidden pointer-events-none">
                                <div class="w-full h-1.5 bg-zinc-300 rounded-full mb-1"></div>
                                <div class="space-y-1">
                                    <div class="w-full h-1 bg-zinc-200 rounded-full"></div>
                                    <div class="w-full h-1 bg-zinc-200 rounded-full"></div>
                                    <div class="w-3/4 h-1 bg-zinc-200 rounded-full"></div>
                                </div>
                                <div class="flex justify-end mt-1">
                                    <div class="w-1/3 h-1.5 bg-zinc-800 rounded-full"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs pointer-events-none">
                                <span class="font-bold text-zinc-700">Page 2</span>
                                <span class="text-[10px] text-zinc-400 font-mono">Line Items</span>
                            </div>
                        </div>

                        <!-- Page 3: Terms, UPI & Signature -->
                        <div onclick="coraSelectPageThumbnail(3)" id="wiz-thumb-3" class="p-3 bg-white border-2 border-zinc-950 rounded-2xl cursor-pointer transition-all hover:bg-zinc-50 group shadow-xs relative z-10">
                            <div class="aspect-[1/0.5] bg-zinc-50 border border-zinc-200/60 rounded-xl p-2 flex flex-col justify-between mb-2 relative overflow-hidden pointer-events-none">
                                <div class="space-y-1">
                                    <div class="w-full h-1 bg-zinc-300 rounded-full"></div>
                                    <div class="w-2/3 h-1 bg-zinc-200 rounded-full"></div>
                                </div>
                                <div class="flex items-center gap-1 my-1">
                                    <div class="w-3.5 h-3.5 rounded bg-black text-white text-[5px] flex items-center justify-center font-bold">LP</div>
                                    <div class="w-full h-1 bg-zinc-200 rounded-full"></div>
                                </div>
                                <div class="flex justify-between items-end border-t border-zinc-200 pt-1">
                                    <div class="w-1/3 h-1 bg-zinc-300 rounded-full"></div>
                                    <div class="w-1/4 h-1 bg-zinc-900 rounded-full"></div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs pointer-events-none">
                                <span class="font-bold text-zinc-950">Page 3</span>
                                <span class="text-[10px] text-zinc-400 font-mono">Terms</span>
                            </div>
                        </div>
                    </div>

                    <!-- Add Page Button -->
                    <button type="button" onclick="coraAddWizardPage()" class="w-full py-2.5 border border-zinc-200/80 hover:border-zinc-950 text-zinc-700 hover:text-zinc-950 font-bold text-xs rounded-xl flex items-center justify-center gap-1.5 transition-all cursor-pointer bg-zinc-50/70 hover:bg-white">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        + Add Page
                    </button>
                </div>

                <!-- Main Preview Workspace: High-Fidelity Paper Document Container -->
                <div class="flex-1 min-w-0 min-h-[900px] relative overflow-x-auto">
                    <div id="wiz-step4-paper-preview" class="w-full space-y-8 font-sans min-h-[850px] text-zinc-800">
                        <!-- Live paper document populated by coraRenderPaperPreviewInStep5() -->
                    </div>
                </div>

                <!-- Right Docked Sidebar Panel: Quick Edit Panel (Fixed 280px width - Docked Right Sidebar) -->
                <div id="step4-right-edit-sidebar" class="hidden w-full md:w-[280px] md:shrink-0 bg-white border border-zinc-200/80 rounded-3xl p-5 shadow-xs space-y-4 md:sticky md:top-4 self-start pointer-events-auto">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <h4 class="font-extrabold text-zinc-950 text-xs uppercase tracking-wider flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            Quick Edit Panel
                        </h4>
                        <button type="button" onclick="coraToggleStep4Sidebar('edit')" class="w-6 h-6 rounded-lg text-zinc-400 hover:text-zinc-950 hover:bg-zinc-100 flex items-center justify-center text-xs font-bold transition-all cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <!-- Document Title Input -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-zinc-700 block">Document Title</label>
                        <input type="text" id="step4-quick-title" oninput="coraSyncStep4QuickEdit()" class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs font-semibold outline-none focus:border-zinc-950 transition-all text-zinc-900 bg-zinc-50/50 focus:bg-white" placeholder="Document Title...">
                    </div>

                    <!-- Company Name Input -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-zinc-700 block">Company Name</label>
                        <input type="text" id="step4-quick-company" value="CORA STUDIO WORKSPACE" oninput="coraSyncStep4QuickEdit()" class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs font-semibold outline-none focus:border-zinc-950 transition-all text-zinc-900 bg-zinc-50/50 focus:bg-white" placeholder="Studio / Company Name...">
                    </div>

                    <!-- Date Picker -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-zinc-700 block">Document Date</label>
                        <input type="date" id="step4-quick-date" value="<?php echo date('Y-m-d'); ?>" onchange="coraSyncStep4QuickEdit()" class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs font-mono outline-none focus:border-zinc-950 transition-all text-zinc-900 bg-zinc-50/50 focus:bg-white">
                    </div>

                    <!-- Primary Color Palette Picker -->
                    <div class="space-y-2 pt-1 border-t border-zinc-100">
                        <label class="text-[11px] font-bold text-zinc-700 block">Primary Accent Color</label>
                        <div class="flex items-center gap-2 flex-wrap" id="wiz-color-swatches">
                            <button type="button" data-color="#09090b" onclick="coraSelectPrimaryColor('#09090b', this)" title="Black (#09090b)" class="w-7 h-7 rounded-full bg-[#09090b] border-2 border-zinc-950 ring-2 ring-zinc-950 ring-offset-2 transition-all cursor-pointer cora-color-swatch active-swatch"></button>
                            <button type="button" data-color="#3f3f46" onclick="coraSelectPrimaryColor('#3f3f46', this)" title="Zinc (#3f3f46)" class="w-7 h-7 rounded-full bg-[#3f3f46] border-2 border-white hover:scale-105 transition-all cursor-pointer cora-color-swatch"></button>
                            <button type="button" data-color="#2563eb" onclick="coraSelectPrimaryColor('#2563eb', this)" title="Blue (#2563eb)" class="w-7 h-7 rounded-full bg-[#2563eb] border-2 border-white hover:scale-105 transition-all cursor-pointer cora-color-swatch"></button>
                            <button type="button" data-color="#059669" onclick="coraSelectPrimaryColor('#059669', this)" title="Emerald (#059669)" class="w-7 h-7 rounded-full bg-[#059669] border-2 border-white hover:scale-105 transition-all cursor-pointer cora-color-swatch"></button>
                            <button type="button" data-color="#7c3aed" onclick="coraSelectPrimaryColor('#7c3aed', this)" title="Purple (#7c3aed)" class="w-7 h-7 rounded-full bg-[#7c3aed] border-2 border-white hover:scale-105 transition-all cursor-pointer cora-color-swatch"></button>
                            <button type="button" data-color="#d97706" onclick="coraSelectPrimaryColor('#d97706', this)" title="Amber (#d97706)" class="w-7 h-7 rounded-full bg-[#d97706] border-2 border-white hover:scale-105 transition-all cursor-pointer cora-color-swatch"></button>
                        </div>
                    </div>

                    <!-- Font Family Selector Dropdown -->
                    <div class="space-y-1.5 pt-1 border-t border-zinc-100">
                        <label class="text-[11px] font-bold text-zinc-700 block">Font Family</label>
                        <select id="step4-quick-font" onchange="coraSelectFontFamily(this.value)" class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs font-semibold outline-none focus:border-zinc-950 transition-all text-zinc-900 bg-zinc-50/50 focus:bg-white cursor-pointer">
                            <option value="font-sans">Inter / System Sans (Default)</option>
                            <option value="font-serif">Serif (Editorial Classic)</option>
                            <option value="font-mono">Monospace (Technical / Minimal)</option>
                        </select>
                    </div>

                    <!-- Internal Document Notes -->
                    <div class="space-y-1.5 pt-1 border-t border-zinc-100">
                        <label class="text-[11px] font-bold text-zinc-700 block">Internal Document Notes</label>
                        <textarea id="step4-quick-notes" name="step4_quick_notes" rows="3" class="w-full border border-zinc-200 rounded-xl p-2.5 text-xs outline-none focus:border-zinc-950 transition-all text-zinc-800 bg-zinc-50/50 focus:bg-white" placeholder="Internal studio notes (not visible to client)..."></textarea>
                    </div>
                </div>

            </div>
        </div>

        <!-- STEP 5 SUB-PAGE: GENERATE DOCUMENT & DOCUMENT READY -->
        <div id="sub-page-wiz-step-5" class="hidden bg-white border border-zinc-200/80 rounded-3xl p-6 md:p-12 pb-24 sm:pb-28 shadow-xs space-y-8 w-full max-w-none">
            <!-- Center Hero State -->
            <div class="text-center max-w-xl mx-auto space-y-3 py-4">
                <div class="w-16 h-16 rounded-full bg-zinc-950 text-white flex items-center justify-center shadow-lg mx-auto border-4 border-zinc-100">
                    <svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <h3 class="text-2xl font-black text-zinc-950 tracking-tight">Your Document is Ready!</h3>
                <p class="text-xs text-zinc-500 leading-relaxed">Your document has been generated successfully and passed all compliance checks.</p>
            </div>

            <!-- Generated File Card Box -->
            <div class="max-w-xl mx-auto bg-zinc-50 border border-zinc-200 rounded-2xl p-5 shadow-xs flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-zinc-950 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <div>
                        <div id="wiz-step5-filename" class="font-extrabold text-zinc-950 text-sm tracking-tight">Proposal_Arjun_Sharma_24052025.pdf</div>
                        <div class="text-xs text-zinc-500 font-mono mt-0.5 flex items-center gap-2">
                            <span>PDF • 3 Pages • 1.2 MB</span>
                            <span class="w-1 h-1 rounded-full bg-zinc-300"></span>
                            <span class="text-emerald-700 font-bold text-[10px] uppercase">Verified Output</span>
                        </div>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-wider">Ready</span>
            </div>

            <div id="wiz-generate-summary-box" class="max-w-xl mx-auto">
                <!-- Populated by JS -->
            </div>

            <!-- Primary Action -->
            <div class="max-w-xl mx-auto text-center space-y-4">
                <button type="button" onclick="coraPrintInvoiceOnly()" class="w-full py-4 bg-zinc-950 hover:bg-black text-white font-bold text-sm rounded-xl shadow-md cursor-pointer flex items-center justify-center gap-2 transition-all">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Download PDF
                </button>

                <!-- Secondary Actions -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="coraJumpToWizardStep(4)" class="py-3 bg-white border border-zinc-300 hover:bg-zinc-100 text-zinc-900 font-bold text-xs rounded-xl shadow-xs cursor-pointer flex items-center justify-center gap-1.5 transition-all">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        Preview Document
                    </button>
                    <button type="button" onclick="coraSwitchVaultView('esign')" class="py-3 bg-white border border-zinc-300 hover:bg-zinc-100 text-zinc-900 font-bold text-xs rounded-xl shadow-xs cursor-pointer flex items-center justify-center gap-1.5 transition-all">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg>
                        Send for E-Sign
                    </button>
                </div>

                <!-- Other Actions -->
                <div class="pt-2 border-t border-zinc-100 flex items-center justify-center gap-2 flex-wrap">
                    <button type="button" onclick="coraDownloadWordDoc('DOC')" class="px-4 py-2 bg-zinc-100 border border-zinc-200 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-xl transition-all cursor-pointer">
                        Download Word
                    </button>
                    <button type="button" onclick="coraDownloadWordDoc('DOCX')" class="px-4 py-2 bg-zinc-100 border border-zinc-200 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-xl transition-all cursor-pointer">
                        Download DOCX
                    </button>
                    <button type="button" onclick="coraPrintInvoiceOnly()" class="px-4 py-2 bg-zinc-100 border border-zinc-200 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-xl transition-all cursor-pointer flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        Print Document
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP 6 SUB-PAGE: SAVE & SHARE WITH SECURITY -->
        <div id="sub-page-wiz-step-6" class="hidden bg-white border border-zinc-200/80 rounded-3xl p-5 md:p-8 pb-24 sm:pb-28 shadow-xs space-y-6 w-full max-w-none">
            <!-- Header Bar -->
            <div class="border-b border-zinc-100 pb-4 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <span class="px-3.5 py-1.5 rounded-full bg-zinc-100 text-zinc-600 text-[11px] font-bold uppercase tracking-widest inline-block border border-zinc-200 mb-0.5">Step 6 of 6</span>
                    <h3 class="text-xl font-black text-zinc-950 tracking-tight">Save & Share with Security</h3>
                    <p class="text-xs text-zinc-500 mt-0.5 leading-relaxed">Save your document to vault, configure security protections, and share via email or WhatsApp.</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold flex items-center gap-1.5 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Security Active
                </span>
            </div>

            <!-- 12-COLUMN RESPONSIVE GRID (7 cols + 5 cols) -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start w-full">
                
                <!-- Left 7 Columns: Share Options & Security Options Cards -->
                <div class="md:col-span-7 space-y-5 w-full">
                    
                    <!-- Card 1: SHARE OPTIONS -->
                    <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-3 flex-wrap gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-900 flex items-center justify-center shrink-0">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-zinc-950">SHARE OPTIONS</h4>
                                    <p class="text-[11px] text-zinc-500">Share your document securely with people.</p>
                                </div>
                            </div>
                            <button type="button" onclick="coraShowToast('Security & Sharing Documentation')" class="px-2.5 py-1 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[11px] font-bold transition-all cursor-pointer flex items-center gap-1 border border-zinc-200/80">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                Learn more
                            </button>
                        </div>

                        <!-- 2x2 Feature Cards Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            
                            <!-- Block 1: Copy Share Link -->
                            <div class="p-3.5 bg-zinc-50/80 rounded-xl space-y-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-white text-zinc-800 flex items-center justify-center shrink-0 shadow-2xs">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="font-extrabold text-xs text-zinc-900">Copy Share Link</h5>
                                        <p class="text-[10px] text-zinc-500">Anyone with link can view</p>
                                    </div>
                                </div>
                                <div class="flex gap-1.5">
                                    <input type="text" id="wiz-step6-link-input" readonly class="w-full border border-zinc-200/60 rounded-lg p-2 bg-white font-mono text-[11px] outline-none text-zinc-700">
                                    <button type="button" onclick="coraCopyStep6Link()" class="px-3 py-1.5 bg-zinc-950 hover:bg-black text-white rounded-lg text-xs font-bold transition-all cursor-pointer shrink-0">Copy</button>
                                </div>
                            </div>

                            <!-- Block 2: Send via Email (Official Blue Accent) -->
                            <div class="p-3.5 bg-blue-50/40 rounded-xl space-y-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-blue-100/80 text-blue-700 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    </div>
                                    <div>
                                        <h5 class="font-extrabold text-xs text-zinc-900">Send via Email</h5>
                                        <p class="text-[10px] text-zinc-500">Send document via email</p>
                                    </div>
                                </div>
                                <div class="flex gap-1.5">
                                    <input type="email" id="wiz-step6-email-input" placeholder="Enter email..." class="w-full border border-zinc-200/60 rounded-lg p-2 text-xs font-medium outline-none focus:border-blue-600 text-zinc-900 bg-white">
                                    <button type="button" onclick="coraSendStep6Email()" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all cursor-pointer shrink-0 shadow-2xs">Send</button>
                                </div>
                            </div>

                            <!-- Block 3: Request E-Signature (Official Purple Accent) -->
                            <div class="p-3.5 bg-purple-50/40 rounded-xl space-y-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-purple-100/80 text-purple-700 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg>
                                    </div>
                                    <div>
                                        <h5 class="font-extrabold text-xs text-zinc-900">Request E-Signature</h5>
                                        <p class="text-[10px] text-zinc-500">Request digital signatures</p>
                                    </div>
                                </div>
                                <button type="button" onclick="coraTriggerESignFromStep6()" class="w-full py-2 bg-purple-100/60 hover:bg-purple-100 text-purple-900 font-extrabold text-xs rounded-lg transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg>
                                    Request E-Sign Directly
                                </button>
                            </div>

                            <!-- Block 4: Share via WhatsApp (Official WhatsApp Brand Logo) -->
                            <div class="p-3.5 bg-emerald-50/40 border border-emerald-100/80 rounded-xl space-y-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-[#25D366] text-white flex items-center justify-center shrink-0 shadow-2xs">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" class="shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.572-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    </div>
                                    <div>
                                        <h5 class="font-extrabold text-xs text-zinc-900">Share via WhatsApp</h5>
                                        <p class="text-[10px] text-zinc-500">Send direct WhatsApp link</p>
                                    </div>
                                </div>
                                <div class="flex gap-1.5">
                                    <input type="text" id="wiz-step6-phone-input" placeholder="Enter phone number" class="w-full border border-zinc-200/60 rounded-lg p-2 font-mono text-xs outline-none focus:border-[#25D366] text-zinc-900 bg-white">
                                    <button type="button" onclick="coraShareWhatsAppDirectStep6()" class="px-3.5 py-2 bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-bold rounded-xl flex items-center gap-1.5 cursor-pointer transition-all shrink-0 shadow-2xs">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.572-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        Share
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Card 2: SECURITY OPTIONS & PROTECTION -->
                    <div class="bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-xs space-y-4">
                        <div class="flex items-center gap-2.5 border-b border-zinc-100 pb-3">
                            <div class="w-8 h-8 rounded-lg bg-zinc-100 text-zinc-900 flex items-center justify-center shrink-0">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-zinc-950">SECURITY OPTIONS & PROTECTION</h4>
                                <p class="text-[11px] text-zinc-500">Add extra security protection to your document.</p>
                            </div>
                        </div>

                        <!-- Stacked Security Option Rows (Robust Active State Toggles) -->
                        <div class="space-y-2.5">
                            
                            <!-- Password Protection (Indigo Accent) -->
                            <div class="p-3.5 bg-indigo-50/40 rounded-xl space-y-2">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        </div>
                                        <div>
                                            <h5 class="font-extrabold text-xs text-zinc-900">Password Protection</h5>
                                            <p class="text-[10px] text-zinc-500">Require password to open or view this document.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input type="checkbox" id="wiz-sec-password-toggle" onchange="coraToggleSecPassword(this.checked)" class="sr-only">
                                        <div id="wiz-sec-password-track" class="w-10 h-5.5 bg-zinc-200 rounded-full transition-colors duration-200 relative p-0.5">
                                            <div id="wiz-sec-password-knob" class="w-4.5 h-4.5 bg-white rounded-full shadow-md transition-transform duration-200 transform translate-x-0"></div>
                                        </div>
                                    </label>
                                </div>
                                <div id="wiz-sec-password-fields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 border-t border-indigo-100">
                                    <input type="password" id="wiz-sec-pass-input" placeholder="Enter Password..." class="border border-zinc-200/60 rounded-lg p-2 text-xs font-mono outline-none focus:border-indigo-600 bg-white">
                                    <input type="password" id="wiz-sec-pass-confirm" placeholder="Confirm Password..." class="border border-zinc-200/60 rounded-lg p-2 text-xs font-mono outline-none focus:border-indigo-600 bg-white">
                                </div>
                            </div>

                            <!-- Document Expiry (Amber Accent) -->
                            <div class="p-3.5 bg-amber-50/40 rounded-xl space-y-2">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        </div>
                                        <div>
                                            <h5 class="font-extrabold text-xs text-zinc-900">Document Expiry</h5>
                                            <p class="text-[10px] text-zinc-500">Set automatic link expiration date for secure sharing.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input type="checkbox" id="wiz-sec-expiry-toggle" onchange="coraToggleSecExpiry(this.checked)" class="sr-only">
                                        <div id="wiz-sec-expiry-track" class="w-10 h-5.5 bg-zinc-200 rounded-full transition-colors duration-200 relative p-0.5">
                                            <div id="wiz-sec-expiry-knob" class="w-4.5 h-4.5 bg-white rounded-full shadow-md transition-transform duration-200 transform translate-x-0"></div>
                                        </div>
                                    </label>
                                </div>
                                <div id="wiz-sec-expiry-field" class="hidden pt-2 border-t border-amber-100">
                                    <input type="date" id="wiz-sec-expiry-date" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" class="w-full border border-zinc-200/60 rounded-lg p-2 text-xs font-mono outline-none focus:border-amber-600 bg-white">
                                </div>
                            </div>

                            <!-- Add Watermark to PDF (Emerald Accent) -->
                            <div class="p-3.5 bg-emerald-50/40 rounded-xl flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="font-extrabold text-xs text-zinc-900">Add Watermark to PDF</h5>
                                        <p class="text-[10px] text-zinc-500">Stamp "CONFIDENTIAL" watermark across document pages.</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" id="wiz-sec-watermark-toggle" name="wiz_sec_watermark_toggle" onchange="coraToggleSecWatermark(this.checked)" class="sr-only">
                                    <div id="wiz-sec-watermark-track" class="w-10 h-5.5 bg-zinc-200 rounded-full transition-colors duration-200 relative p-0.5">
                                        <div id="wiz-sec-watermark-knob" class="w-4.5 h-4.5 bg-white rounded-full shadow-md transition-transform duration-200 transform translate-x-0"></div>
                                    </div>
                                </label>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Right 5 Columns: Document Summary Card Box -->
                <div class="md:col-span-5 space-y-5 w-full sticky top-4 self-start">
                    <div class="bg-white border border-zinc-200/80 p-5 rounded-2xl shadow-xs space-y-5">
                        <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-zinc-950">
                                DOCUMENT SUMMARY
                            </h4>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-extrabold flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                Security Active
                            </span>
                        </div>

                        <!-- Visual Mini Paper Preview card -->
                        <div class="aspect-[1/0.45] bg-zinc-100/90 rounded-xl border border-zinc-200/60 p-3.5 flex flex-col justify-between shadow-inner relative overflow-hidden">
                            <div class="flex items-center justify-between">
                                <div class="w-5 h-5 rounded bg-zinc-950 text-white font-bold text-[9px] flex items-center justify-center">C</div>
                                <span class="text-[9px] font-mono font-bold text-zinc-500 bg-white/80 px-1.5 py-0.5 rounded border border-zinc-200/60">PDF</span>
                            </div>
                            <div class="my-auto">
                                <div class="w-3/4 h-1.5 bg-zinc-300 rounded mb-1"></div>
                                <div class="w-full h-1 bg-zinc-200 rounded mb-1"></div>
                                <div class="w-5/6 h-1 bg-zinc-200 rounded"></div>
                            </div>
                            <div class="text-[8px] font-mono text-zinc-400 uppercase tracking-widest text-center">
                                CORA STUDIO VAULT
                            </div>
                        </div>

                        <!-- Summary Meta Info -->
                        <div class="space-y-2.5 text-xs">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider">DOCUMENT TITLE</span>
                                    <span id="step6-summary-category-badge" class="px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-zinc-100 text-zinc-800 border border-zinc-200/60 uppercase tracking-wider">PROPOSAL</span>
                                </div>
                                <h5 id="step6-summary-title" class="text-sm font-extrabold text-zinc-950 tracking-tight leading-snug mt-1">Proposal: Arjun & Priya</h5>
                            </div>

                            <div class="grid grid-cols-2 gap-3 border-t border-zinc-100 pt-3 text-xs">
                                <div>
                                    <span class="text-[10px] font-extrabold text-zinc-400 uppercase">PAGES</span>
                                    <span class="font-bold text-zinc-900 text-xs block mt-0.5">3 Pages</span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-extrabold text-zinc-400 uppercase">FILE SIZE</span>
                                    <span class="font-mono font-bold text-zinc-900 text-xs block mt-0.5">1.2 MB</span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-extrabold text-zinc-400 uppercase">CREATED DATE</span>
                                    <span id="step6-summary-created-date" class="font-mono font-bold text-zinc-900 text-xs block mt-0.5"><?php echo date('d M Y'); ?></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-extrabold text-zinc-400 uppercase">VALUATION</span>
                                    <span id="step6-summary-grandtotal" class="font-mono font-bold text-zinc-950 text-xs block mt-0.5">₹0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Action Button -->
                        <button type="button" onclick="coraSaveAndShareFinalDocument()" class="w-full py-3.5 bg-zinc-950 hover:bg-black text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer flex items-center justify-center gap-2 transition-all">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            Save & Share Document
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- STICKY FLOATING BOTTOM WIZARD STEPPER DOCK -->
        <div class="sticky bottom-4 z-[8000] w-full max-w-4xl mx-auto font-sans">
            <div class="bg-white border border-zinc-200/80 rounded-2xl px-4 py-2.5 shadow-[0_-4px_24px_-2px_rgba(0,0,0,0.08),0_8px_32px_-4px_rgba(0,0,0,0.12)]">
                <div class="flex items-center gap-3">
                    <!-- Previous Button -->
                    <button type="button" id="wiz-prev-step-btn" onclick="coraNavWizardStep(-1)" class="hidden w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center text-zinc-500 bg-white hover:bg-zinc-50 cursor-pointer transition-all shrink-0">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>

                    <!-- Stepper Track -->
                    <div class="flex items-center flex-1 gap-1.5 min-w-0 overflow-x-auto scrollbar-none">
                        <!-- Step 1 -->
                        <button type="button" onclick="coraJumpToWizardStep(1)" id="wiz-step-pill-1" class="wiz-step-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-950 cursor-pointer transition-all shrink-0" data-step="1">
                            <span class="wiz-step-num w-5 h-5 rounded-full bg-white/20 text-white text-[10px] font-bold flex items-center justify-center shrink-0">1</span>
                            <span class="wiz-step-label text-[11px] font-semibold text-white whitespace-nowrap">Type</span>
                        </button>
                        <div class="wiz-step-line w-4 border-t border-dashed border-zinc-300 shrink-0" data-line="1"></div>

                        <!-- Step 2 -->
                        <button type="button" onclick="coraJumpToWizardStep(2)" id="wiz-step-pill-2" class="wiz-step-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 cursor-pointer transition-all shrink-0 hover:bg-zinc-150" data-step="2">
                            <span class="wiz-step-num w-5 h-5 rounded-full bg-zinc-200 text-zinc-500 text-[10px] font-bold flex items-center justify-center shrink-0">2</span>
                            <span class="wiz-step-label text-[11px] font-medium text-zinc-400 whitespace-nowrap">Template</span>
                        </button>
                        <div class="wiz-step-line w-4 border-t border-dashed border-zinc-300 shrink-0" data-line="2"></div>

                        <!-- Step 3 -->
                        <button type="button" onclick="coraJumpToWizardStep(3)" id="wiz-step-pill-3" class="wiz-step-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 cursor-pointer transition-all shrink-0 hover:bg-zinc-150" data-step="3">
                            <span class="wiz-step-num w-5 h-5 rounded-full bg-zinc-200 text-zinc-500 text-[10px] font-bold flex items-center justify-center shrink-0">3</span>
                            <span class="wiz-step-label text-[11px] font-medium text-zinc-400 whitespace-nowrap">Build</span>
                        </button>
                        <div class="wiz-step-line w-4 border-t border-dashed border-zinc-300 shrink-0" data-line="3"></div>

                        <!-- Step 4 -->
                        <button type="button" onclick="coraJumpToWizardStep(4)" id="wiz-step-pill-4" class="wiz-step-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 cursor-pointer transition-all shrink-0 hover:bg-zinc-150" data-step="4">
                            <span class="wiz-step-num w-5 h-5 rounded-full bg-zinc-200 text-zinc-500 text-[10px] font-bold flex items-center justify-center shrink-0">4</span>
                            <span class="wiz-step-label text-[11px] font-medium text-zinc-400 whitespace-nowrap">Preview</span>
                        </button>
                        <div class="wiz-step-line w-4 border-t border-dashed border-zinc-300 shrink-0" data-line="4"></div>

                        <!-- Step 5 -->
                        <button type="button" onclick="coraJumpToWizardStep(5)" id="wiz-step-pill-5" class="wiz-step-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 cursor-pointer transition-all shrink-0 hover:bg-zinc-150" data-step="5">
                            <span class="wiz-step-num w-5 h-5 rounded-full bg-zinc-200 text-zinc-500 text-[10px] font-bold flex items-center justify-center shrink-0">5</span>
                            <span class="wiz-step-label text-[11px] font-medium text-zinc-400 whitespace-nowrap">Generate</span>
                        </button>
                        <div class="wiz-step-line w-4 border-t border-dashed border-zinc-300 shrink-0" data-line="5"></div>

                        <!-- Step 6 -->
                        <button type="button" onclick="coraJumpToWizardStep(6)" id="wiz-step-pill-6" class="wiz-step-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 cursor-pointer transition-all shrink-0 hover:bg-zinc-150" data-step="6">
                            <span class="wiz-step-num w-5 h-5 rounded-full bg-zinc-200 text-zinc-500 text-[10px] font-bold flex items-center justify-center shrink-0">6</span>
                            <span class="wiz-step-label text-[11px] font-medium text-zinc-400 whitespace-nowrap">Share</span>
                        </button>
                    </div>

                    <!-- Next Button -->
                    <button type="button" id="wiz-next-step-btn" onclick="coraNavWizardStep(1)" class="px-4 py-2 bg-zinc-950 text-white rounded-xl text-[11px] font-bold hover:bg-zinc-800 cursor-pointer shadow-sm transition-all whitespace-nowrap shrink-0 flex items-center gap-1.5">
                        <span>Next</span>
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>
        </form>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════════
         VIEW 3: E-SIGN LEGAL AUDIT REGISTRY
         ═════════════════════════════════════════════════════════════════════════ -->
    <div id="cora-vault-view-esign" class="hidden space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-zinc-950">E-Signature Audit Registry</h2>
                <p class="text-xs text-zinc-500">Tamper-evident log of legally binding electronic signature certificates.</p>
            </div>
            <span class="px-3 py-1 bg-zinc-100 text-zinc-950 rounded-lg text-xs font-bold border border-zinc-300 font-mono">
                <?php echo $signed_count; ?> Verified Signatures
            </span>
        </div>

        <div class="bg-white border border-zinc-200/80 rounded-2xl shadow-xs overflow-hidden">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50/80 border-b border-zinc-200 text-[10px] font-extrabold text-zinc-500 uppercase tracking-wider">
                        <th class="p-3.5">Document Title & #</th>
                        <th class="p-3.5">Signer Name</th>
                        <th class="p-3.5">Signer Email</th>
                        <th class="p-3.5">IP Address</th>
                        <th class="p-3.5">Verification Hash</th>
                        <th class="p-3.5">Timestamp</th>
                        <th class="p-3.5 text-right">Certificate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    <?php 
                    $has_signed = false;
                    foreach ( $cora_documents as $doc ) : 
                        if ( empty( $doc['signed'] ) ) continue;
                        $has_signed = true;
                    ?>
                    <tr class="hover:bg-zinc-50/80 transition-colors">
                        <td class="p-3.5">
                            <div class="font-bold text-zinc-950"><?php echo esc_html( $doc['title'] ); ?></div>
                            <div class="font-mono text-[10px] text-zinc-400"><?php echo esc_html( $doc['number'] ?? 'DOC-2026' ); ?></div>
                        </td>
                        <td class="p-3.5 font-semibold text-zinc-900"><?php echo esc_html( $doc['signer_name'] ?? '—' ); ?></td>
                        <td class="p-3.5 text-zinc-600"><?php echo esc_html( $doc['signer_email'] ?? '—' ); ?></td>
                        <td class="p-3.5 font-mono text-[10px] text-zinc-500"><?php echo esc_html( $doc['signer_ip'] ?? '103.21.124.8' ); ?></td>
                        <td class="p-3.5 font-mono text-[10px] font-bold text-zinc-950"><?php echo esc_html( $doc['verification_hash'] ?? 'ESIGN-HASH-V1' ); ?></td>
                        <td class="p-3.5 font-mono text-[10px] text-zinc-500"><?php echo esc_html( $doc['signed_at'] ?? $doc['created_at'] ); ?></td>
                        <td class="p-3.5 text-right">
                            <button onclick="coraOpenDocPreviewDrawer('<?php echo esc_js( $doc['id'] ); ?>')" class="px-2.5 py-1 bg-zinc-950 text-white rounded-lg text-[11px] font-bold cursor-pointer hover:bg-zinc-800 transition-all">
                                View Audit
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; 
                    if ( ! $has_signed ) : ?>
                    <tr>
                        <td colspan="7" class="p-8 text-center text-zinc-400 text-xs">
                            No e-signed documents recorded yet. Click "+ E-Sign" on any document to collect a signature.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══ ISOLATED PRINTABLE INVOICE CANVAS ═══ -->
<div id="cora-printable-canvas" class="hidden"></div>

<!-- ═════════════════════════════════════════════════════════════════════════
     RIGHT-SLIDING SIDE DRAWER 1: E-SIGNATURE CAPTURE DRAWER (#cora-esign-drawer)
     ═════════════════════════════════════════════════════════════════════════ -->
<div id="cora-esign-drawer" class="hidden fixed inset-0 z-[99999] overflow-hidden pointer-events-none">
    <div onclick="coraCloseESignDrawer()" class="cora-drawer-backdrop absolute inset-0 bg-zinc-950/40 backdrop-blur-xs pointer-events-auto"></div>
    <div class="cora-drawer-sheet absolute inset-y-0 right-0 max-w-full flex w-full sm:w-[480px] pointer-events-auto">
        <div class="w-full bg-white border-l border-zinc-200 shadow-2xl flex flex-col justify-between overflow-y-auto p-6 md:p-8 space-y-6">
            <div>
                <div class="flex items-center justify-between border-b border-zinc-200/80 pb-4 mb-6">
                    <div>
                        <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-widest block">Legal Audit Trail</span>
                        <h3 class="text-base font-black text-zinc-950 mt-0.5">Collect E-Signature</h3>
                    </div>
                    <button onclick="coraCloseESignDrawer()" class="p-2 text-zinc-400 hover:text-zinc-950 text-base font-bold cursor-pointer rounded-lg hover:bg-zinc-100">✕</button>
                </div>

                <input type="hidden" id="esign-target-doc-id">

                <div class="space-y-4 text-xs">
                    <div class="bg-zinc-50 border border-zinc-200 p-4 rounded-2xl space-y-1">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase block">Document Target</span>
                        <h4 id="esign-doc-title-display" class="font-bold text-zinc-950 text-sm">Proposal</h4>
                        <div id="esign-doc-num-display" class="font-mono text-[10px] text-zinc-500">DOC-2026</div>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-zinc-800">Signer Full Name *</label>
                        <input type="text" id="esign-signer-name-input" placeholder="e.g. Rajesh Sharma" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none focus:border-zinc-950 transition-colors font-semibold">
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-zinc-800">Signer Email Address *</label>
                        <input type="email" id="esign-signer-email-input" placeholder="signer@example.com" class="w-full border border-zinc-200 rounded-xl p-3 bg-white outline-none focus:border-zinc-950 transition-colors">
                    </div>

                    <div class="space-y-1 pt-2">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-bold text-zinc-800">Digital Signature Pad</label>
                            <button type="button" onclick="coraClearSigCanvas()" class="text-[10px] font-bold text-zinc-500 hover:text-zinc-950 underline cursor-pointer">Clear Pad</button>
                        </div>
                        <div class="border-2 border-dashed border-zinc-300 rounded-2xl bg-zinc-50 p-1 flex justify-center">
                            <canvas id="cora-sig-canvas" width="400" height="150" class="w-full h-36 bg-white rounded-xl cursor-crosshair border border-zinc-200/60"></canvas>
                        </div>
                        <span class="text-[10px] text-zinc-400 block mt-1">Draw signature inside the box using mouse or touch screen.</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-zinc-200 pt-4 space-y-3">
                <button onclick="coraSubmitESign()" class="w-full py-3 bg-zinc-950 hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer">
                    Confirm & Execute E-Signature Stamp →
                </button>
                <button onclick="coraCloseESignDrawer()" class="w-full py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-xl transition-all cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════════════
     RIGHT-SLIDING SIDE DRAWER 2: SHARE DOCUMENT DRAWER (#cora-share-doc-drawer)
     ═════════════════════════════════════════════════════════════════════════ -->
<div id="cora-share-doc-drawer" class="hidden fixed inset-0 z-[99999] overflow-hidden pointer-events-none">
    <div onclick="coraCloseShareDrawer()" class="cora-drawer-backdrop absolute inset-0 bg-zinc-950/40 backdrop-blur-xs pointer-events-auto"></div>
    <div class="cora-drawer-sheet absolute inset-y-0 right-0 max-w-full flex w-full sm:w-[460px] pointer-events-auto">
        <div class="w-full bg-white border-l border-zinc-200 shadow-2xl flex flex-col justify-between overflow-y-auto p-6 md:p-8 space-y-6">
            <div>
                <div class="flex items-center justify-between border-b border-zinc-200/80 pb-4 mb-6">
                    <div>
                        <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-widest block">Dispatch Hub</span>
                        <h3 class="text-base font-black text-zinc-950 mt-0.5">Share Document</h3>
                    </div>
                    <button onclick="coraCloseShareDrawer()" class="p-2 text-zinc-400 hover:text-zinc-950 text-base font-bold cursor-pointer rounded-lg hover:bg-zinc-100">✕</button>
                </div>

                <input type="hidden" id="share-target-doc-id">

                <div class="space-y-5 text-xs">
                    <div class="bg-zinc-50 border border-zinc-200 p-4 rounded-2xl">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase block">Selected Document</span>
                        <h4 id="share-doc-title-display" class="font-bold text-zinc-950 text-sm">Proposal</h4>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-bold text-zinc-800">Tokenized Direct Access URL</label>
                        <div class="flex gap-2">
                            <input type="text" id="share-link-url" readonly class="w-full border border-zinc-200 rounded-xl p-2.5 bg-zinc-100 font-mono text-[11px] outline-none text-zinc-700">
                            <button onclick="coraCopyShareLink()" class="px-3.5 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 transition-all cursor-pointer shrink-0">Copy</button>
                        </div>
                    </div>

                    <div class="border-t border-zinc-200 pt-4 space-y-3">
                        <h4 class="font-bold text-zinc-900">Email Dispatch</h4>
                        <div class="space-y-1">
                            <label class="block font-bold text-zinc-700">Recipient Email</label>
                            <input type="email" id="share-email-input" placeholder="client@example.com" class="w-full border border-zinc-200 rounded-xl p-2.5 bg-white outline-none focus:border-zinc-950 transition-colors">
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-zinc-700">Custom Message (Optional)</label>
                            <textarea id="share-email-msg" rows="3" placeholder="Here is your official document link..." class="w-full border border-zinc-200 rounded-xl p-2.5 bg-white outline-none focus:border-zinc-950 transition-colors"></textarea>
                        </div>
                        <button onclick="coraSendShareEmail()" class="w-full py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer">
                            Send Email Access Link
                        </button>
                    </div>

                    <div class="border-t border-zinc-200 pt-4 space-y-3">
                        <h4 class="font-bold text-zinc-900">WhatsApp Dispatch</h4>
                        <button onclick="coraShareWhatsAppDirect()" class="w-full py-2.5 bg-zinc-950 hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer flex items-center justify-center gap-2">
                            Open WhatsApp Web Direct Link →
                        </button>
                    </div>
                </div>
            </div>

            <div class="border-t border-zinc-200 pt-4">
                <button onclick="coraCloseShareDrawer()" class="w-full py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-xl transition-all cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════════════
     RIGHT-SLIDING SIDE DRAWER 3: DOCUMENT QUICK PREVIEW DRAWER (#cora-doc-preview-drawer)
     ═════════════════════════════════════════════════════════════════════════ -->
<div id="cora-doc-preview-drawer" class="hidden fixed inset-0 z-[99999] overflow-hidden pointer-events-none">
    <div onclick="coraCloseDocPreviewDrawer()" class="cora-drawer-backdrop absolute inset-0 bg-zinc-950/40 backdrop-blur-xs pointer-events-auto"></div>
    <div class="cora-drawer-sheet absolute inset-y-0 right-0 max-w-full flex w-full sm:w-[640px] pointer-events-auto">
        <div class="w-full bg-white border-l border-zinc-200 shadow-2xl flex flex-col justify-between overflow-y-auto p-6 md:p-8 space-y-6">
            <div>
                <div class="flex items-center justify-between border-b border-zinc-200/80 pb-4 mb-6">
                    <div>
                        <span id="preview-drawer-badge" class="px-2.5 py-0.5 rounded text-[10px] font-extrabold bg-zinc-950 text-white uppercase tracking-wider">PROPOSAL</span>
                        <h3 id="preview-drawer-title" class="text-base font-black text-zinc-950 mt-1">Document Title</h3>
                    </div>
                    <button onclick="coraCloseDocPreviewDrawer()" class="p-2 text-zinc-400 hover:text-zinc-950 text-base font-bold cursor-pointer rounded-lg hover:bg-zinc-100">✕</button>
                </div>

                <div id="preview-drawer-content" class="space-y-6 text-xs text-zinc-800">
                    <!-- Populated dynamically -->
                </div>
            </div>

            <div class="border-t border-zinc-200 pt-4 flex items-center justify-between gap-3">
                <button onclick="coraPrintInvoiceOnly()" class="px-4 py-2.5 bg-zinc-950 text-white font-bold text-xs rounded-xl shadow-xs cursor-pointer hover:bg-zinc-800 transition-all">
                    Print / Export PDF
                </button>
                <div class="flex items-center gap-2">
                    <button id="preview-drawer-edit-btn" onclick="coraOpenStudioFromPreviewDrawer()" class="px-4 py-2.5 bg-zinc-100 border border-zinc-200 text-zinc-900 font-bold text-xs rounded-xl cursor-pointer hover:bg-zinc-200 transition-all">
                        Edit Studio
                    </button>
                    <button onclick="coraCloseDocPreviewDrawer()" class="px-4 py-2.5 bg-zinc-100 text-zinc-700 font-semibold text-xs rounded-xl hover:bg-zinc-200 cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═════════════════════════════════════════════════════════════════════════
     RIGHT-SLIDING SIDE DRAWER 4: DELETE CONFIRMATION DRAWER (#cora-delete-doc-drawer)
     ═════════════════════════════════════════════════════════════════════════ -->
<div id="cora-delete-doc-drawer" class="hidden fixed inset-0 z-[99999] overflow-hidden pointer-events-none">
    <div onclick="coraCloseDeleteDrawer()" class="cora-drawer-backdrop absolute inset-0 bg-zinc-950/40 backdrop-blur-xs pointer-events-auto"></div>
    <div class="cora-drawer-sheet absolute inset-y-0 right-0 max-w-full flex w-full sm:w-[420px] pointer-events-auto">
        <div class="w-full bg-white border-l border-zinc-200 shadow-2xl flex flex-col justify-between overflow-y-auto p-6 md:p-8 space-y-6">
            <div>
                <div class="flex items-center justify-between border-b border-zinc-200/80 pb-4 mb-6">
                    <div>
                        <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-widest block">Safe Workspace Guard</span>
                        <h3 class="text-base font-black text-zinc-950 mt-0.5">Delete Document</h3>
                    </div>
                    <button onclick="coraCloseDeleteDrawer()" class="p-2 text-zinc-400 hover:text-zinc-950 text-base font-bold cursor-pointer rounded-lg hover:bg-zinc-100">✕</button>
                </div>

                <input type="hidden" id="delete-target-doc-id">

                <div class="space-y-4 text-xs">
                    <p class="text-zinc-600 leading-relaxed">Are you sure you want to delete this document from the vault? This action cannot be undone.</p>
                    <div class="bg-zinc-50 border border-zinc-200 p-4 rounded-2xl">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase block">Target Document</span>
                        <h4 id="delete-doc-title-display" class="font-bold text-zinc-950 text-sm mt-0.5">Document Title</h4>
                    </div>
                </div>
            </div>

            <div class="border-t border-zinc-200 pt-4 space-y-2">
                <button onclick="coraConfirmDeleteDoc()" class="w-full py-3 bg-zinc-950 hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer">
                    Permanently Delete Document
                </button>
                <button onclick="coraCloseDeleteDrawer()" class="w-full py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-xl transition-all cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FLOATING ACTION POPOVER CARD (NOTION / GOOGLE DRIVE STYLE) -->
<div id="cora-vault-action-popover" class="hidden fixed z-50 bg-white border border-zinc-200 rounded-2xl shadow-md p-1.5 w-52 font-sans text-xs space-y-0.5" style="top: 0; left: 0;">
    <button onclick="coraPopoverAction('view')" class="w-full flex items-center gap-2.5 px-3 py-2 text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 rounded-xl transition-all font-semibold cursor-pointer text-left">
        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
        View Preview
    </button>
    <button onclick="coraPopoverAction('edit')" class="w-full flex items-center gap-2.5 px-3 py-2 text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 rounded-xl transition-all font-semibold cursor-pointer text-left">
        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
        Edit in Studio
    </button>
    <button onclick="coraPopoverAction('share')" class="w-full flex items-center gap-2.5 px-3 py-2 text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 rounded-xl transition-all font-semibold cursor-pointer text-left">
        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
        Share Document
    </button>
    <button id="cora-popover-convert-btn" onclick="coraPopoverAction('convert')" class="w-full flex items-center gap-2.5 px-3 py-2 text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 rounded-xl transition-all font-semibold cursor-pointer text-left">
        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
        Convert to Invoice
    </button>
    <button onclick="coraPopoverAction('esign')" class="w-full flex items-center gap-2.5 px-3 py-2 text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 rounded-xl transition-all font-semibold cursor-pointer text-left">
        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
        E-Sign & Audit Trail
    </button>
    <div class="h-px bg-zinc-200/80 my-1"></div>
    <button onclick="coraPopoverAction('delete')" class="w-full flex items-center gap-2.5 px-3 py-2 text-red-600 hover:bg-red-50 rounded-xl transition-all font-bold cursor-pointer text-left">
        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
        Delete Document
    </button>
</div>

<!-- GOOGLE DRIVE-STYLE CENTER SHARE MODAL POPUP -->
<div id="cora-share-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/40 backdrop-blur-xs p-4">
    <div class="bg-white border border-zinc-200 rounded-3xl shadow-lg max-w-md w-full p-6 space-y-5 font-sans relative text-zinc-900">
        <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-950 flex items-center justify-center border border-zinc-200 shrink-0">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                </div>
                <div>
                    <h3 class="font-black text-sm text-zinc-950">Share Document Access</h3>
                    <p id="share-modal-doc-title" class="text-xs text-zinc-500 font-medium truncate max-w-[220px]"></p>
                </div>
            </div>
            <button onclick="coraCloseShareModal()" class="w-7 h-7 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-600 flex items-center justify-center cursor-pointer transition-colors text-xs font-bold">✕</button>
        </div>

        <div class="space-y-4 text-xs">
            <div class="space-y-1.5">
                <label class="font-bold text-zinc-800 block">Copy Shareable Link</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="share-modal-link-input" readonly class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-3 font-mono text-zinc-700 text-xs outline-none select-all font-medium">
                    <button onclick="coraCopyShareModalLink()" class="px-4 py-3 bg-zinc-950 text-white font-bold rounded-xl hover:bg-zinc-800 cursor-pointer transition-all shrink-0">Copy</button>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="font-bold text-zinc-800 block">Send Direct Client Email</label>
                <div class="flex items-center gap-2">
                    <input type="email" id="share-modal-email-input" placeholder="client@example.com" class="w-full bg-white border border-zinc-200 rounded-xl p-3 font-medium text-xs outline-none focus:border-zinc-950">
                    <button onclick="coraSendShareModalEmail()" class="px-4 py-3 bg-zinc-100 border border-zinc-300 text-zinc-950 font-bold rounded-xl hover:bg-zinc-200 cursor-pointer transition-all shrink-0">Send</button>
                </div>
            </div>

            <div class="space-y-1.5 pt-2 border-t border-zinc-100">
                <div class="flex items-center justify-between">
                    <label class="font-bold text-zinc-800 block">Share on WhatsApp</label>
                    <span id="share-modal-phone-badge" class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">System Contact</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1 flex items-center">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 text-zinc-400 pointer-events-none shrink-0"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <input type="text" id="share-modal-phone-input" placeholder="Enter phone # e.g. +91 9876543210" class="w-full pl-9 pr-3 py-3 bg-white border border-zinc-200 rounded-xl font-mono text-xs outline-none focus:border-zinc-950 font-semibold">
                    </div>
                    <button onclick="coraShareModalWhatsApp()" class="px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl flex items-center gap-2 transition-all cursor-pointer shadow-xs shrink-0 whitespace-nowrap">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" class="shrink-0"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.572-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347z"/><path d="M12 2a10 10 0 0 0-7.79 16.27L3 22l3.86-1.15A10 10 0 1 0 12 2zm0 18a7.96 7.96 0 0 1-4.07-1.12l-.29-.17-2.29.68.69-2.24-.19-.3A7.96 7.96 0 1 1 12 20z"/></svg>
                        Send WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CENTER DELETE CONFIRMATION MODAL POPUP -->
<div id="cora-delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/40 backdrop-blur-xs p-4">
    <div class="bg-white border border-zinc-200 rounded-3xl shadow-lg max-w-sm w-full p-6 space-y-5 font-sans relative text-zinc-900 text-center">
        <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mx-auto border border-red-100">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
        </div>
        <div class="space-y-1.5">
            <h3 class="font-black text-base text-zinc-950">Delete Document?</h3>
            <p id="delete-modal-doc-title" class="text-xs text-zinc-500 leading-relaxed font-medium"></p>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button onclick="coraCloseDeleteModal()" class="flex-1 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-bold rounded-xl text-xs transition-colors cursor-pointer">Cancel</button>
            <button onclick="coraConfirmDeleteModal()" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition-colors shadow-xs cursor-pointer">Delete</button>
        </div>
    </div>
</div>

<script>
window.CORA_DOCUMENTS = <?php echo json_encode( $cora_documents ); ?>;

// CORA STUDIO 6-STEP STATE ENGINE
window.CORA_STUDIO_STATE = {
    currentStep: 1,
    totalSteps: 6,
    selectedCategory: 'proposal',
    selectedTemplate: null,
    searchQuery: '',
    templateFilter: 'all',
    documentData: {
        id: '',
        number: 'DOC-2026',
        title: '',
        type: 'Proposal',
        status: 'Draft',
        client_name: '',
        client_email: '',
        client_phone: '',
        client_gstin: '',
        pos_state: 'Delhi (07)',
        upi_vpa: 'cora@icici',
        items: []
    }
};

var currentWizStep = 1;
var selectedWizCat = 'proposal';

window.coraFilterWizCategories = function(query) {
    CORA_STUDIO_STATE.searchQuery = query || '';
    var q = (query || '').toLowerCase().trim();
    var cards = document.querySelectorAll('.cora-wiz-cat-card');
    cards.forEach(function(card) {
        if (!q || card.textContent.toLowerCase().indexOf(q) > -1) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
};

window.CORA_TEMPLATES_ALL = [
    { id: 'tpl_blank', category: 'custom', style: 'minimal', name: 'Blank Template', amount: 0, type: 'Custom', desc: 'Start with a clean slate canvas and add custom line items and terms.', items: [] },
    { id: 'tpl_modern_prop', category: 'proposal', style: 'modern', name: 'Modern Proposal', amount: 450000, type: 'Proposal', desc: 'Sleek, modern layout designed for high-end photography & videography packages.', items: [ { desc: '3-Day Full Wedding Cinematography & Aerial Drone', sac: '998381', qty: 1, rate: 300000, tax: 18 }, { desc: 'Candid Fine-Art Photography & Signature Album Box', sac: '998381', qty: 1, rate: 150000, tax: 18 } ] },
    { id: 'tpl_classic_prop', category: 'proposal', style: 'professional', name: 'Classic Proposal', amount: 350000, type: 'Proposal', desc: 'Traditional structured business proposal with formal scope breakdowns.', items: [ { desc: 'Multi-Camera Corporate Summit Film Coverage', sac: '998381', qty: 1, rate: 350000, tax: 18 } ] },
    { id: 'tpl_minimal_prop', category: 'proposal', style: 'minimal', name: 'Minimal Proposal', amount: 120000, type: 'Proposal', desc: 'Clean, minimalist proposal focusing purely on core deliverables and pricing.', items: [ { desc: 'Architectural HDR Photography & 4K Walkthrough Video', sac: '998381', qty: 1, rate: 120000, tax: 18 } ] },
    { id: 'tpl_creative_prop', category: 'proposal', style: 'creative', name: 'Creative Proposal', amount: 280000, type: 'Proposal', desc: 'Expressive layout for creative campaigns, branding shoots, and social media reels.', items: [ { desc: 'Brand Campaign Creative Direction & Video Production', sac: '998381', qty: 1, rate: 280000, tax: 18 } ] },
    { id: 'tpl_business_prop', category: 'proposal', style: 'professional', name: 'Business Proposal', amount: 500000, type: 'Proposal', desc: 'Enterprise photography & videography proposal with full licensing rights.', items: [ { desc: 'Enterprise Commercial Production & Broadcast Licensing', sac: '998381', qty: 1, rate: 500000, tax: 18 } ] },
    { id: 'tpl_tax_inv', category: 'invoice', style: 'professional', name: 'Standard Tax Invoice', amount: 150000, type: 'Invoice', desc: 'Official GST tax invoice with statutory CGST/SGST/IGST breakdown.', items: [ { desc: 'Monthly Studio Content Retainer (4 Shoots/mo)', sac: '998381', qty: 1, rate: 150000, tax: 18 } ] },
    { id: 'tpl_minimal_inv', category: 'invoice', style: 'minimal', name: 'Minimal Retainer Invoice', amount: 75000, type: 'Invoice', desc: 'Simple, elegant retainer invoice for ongoing client assignments.', items: [ { desc: 'Post-Production Video Editing Retainer', sac: '998381', qty: 1, rate: 75000, tax: 18 } ] },
    { id: 'tpl_service_contract', category: 'contract', style: 'modern', name: 'Modern Service SLA Contract', amount: 250000, type: 'Contract', desc: 'Service Level Agreement defining turnarounds, edit cycles, and usage rights.', items: [ { desc: 'Annual Content SLA Maintenance & Turnaround Service', sac: '998381', qty: 1, rate: 250000, tax: 18 } ] },
    { id: 'tpl_offer_letter', category: 'offer', style: 'professional', name: 'Associate Offer Letter', amount: 65000, type: 'Offer', desc: 'Official employment offer detailing monthly salary, allowances, and duties.', items: [ { desc: 'Associate Lead Photographer Monthly Base Fee', sac: '998381', qty: 1, rate: 65000, tax: 0 } ] },
    { id: 'tpl_nda_agreement', category: 'nda', style: 'minimal', name: 'Mutual NDA Agreement', amount: 0, type: 'NDA', desc: 'Mutual non-disclosure agreement protecting studio and client intellectual property.', items: [ { desc: 'Mutual Non-Disclosure & Confidentiality Terms', sac: '998381', qty: 1, rate: 0, tax: 0 } ] },
    { id: 'tpl_service_agrmt', category: 'service_agreement', style: 'modern', name: 'Standard Service Agreement', amount: 180000, type: 'Service Agreement', desc: 'Comprehensive client agreement covering scope, payment schedules, and liability.', items: [ { desc: 'Commercial Media Service Rights & Agreement', sac: '998381', qty: 1, rate: 180000, tax: 18 } ] },
    { id: 'tpl_purchase_ord', category: 'purchase_order', style: 'professional', name: 'Gear Purchase Order', amount: 85000, type: 'Purchase Order', desc: 'Official studio purchase requisition for camera gear and lighting inventory.', items: [ { desc: 'Cinema Lighting & Lens Accessory Purchase', sac: '997311', qty: 1, rate: 85000, tax: 18 } ] },
    { id: 'tpl_payment_rcpt', category: 'receipt', style: 'minimal', name: 'Advance Payment Receipt', amount: 50000, type: 'Receipt', desc: 'Official receipt acknowledging payment deposit for shoot reservation.', items: [ { desc: 'Shoot Reservation Advance Deposit Receipt', sac: '998381', qty: 1, rate: 50000, tax: 18 } ] }
];

window.CORA_TEMPLATES = {
    proposal: window.CORA_TEMPLATES_ALL.filter(function(t){ return t.category === 'proposal'; }),
    invoice: window.CORA_TEMPLATES_ALL.filter(function(t){ return t.category === 'invoice'; }),
    contract: window.CORA_TEMPLATES_ALL.filter(function(t){ return t.category === 'contract'; }),
    offer: window.CORA_TEMPLATES_ALL.filter(function(t){ return t.category === 'offer'; }),
    nda: window.CORA_TEMPLATES_ALL.filter(function(t){ return t.category === 'nda'; }),
    service_agreement: window.CORA_TEMPLATES_ALL.filter(function(t){ return t.category === 'service_agreement'; }),
    purchase_order: window.CORA_TEMPLATES_ALL.filter(function(t){ return t.category === 'purchase_order'; }),
    receipt: window.CORA_TEMPLATES_ALL.filter(function(t){ return t.category === 'receipt'; }),
    custom: window.CORA_TEMPLATES_ALL.filter(function(t){ return t.category === 'custom'; })
};

window.coraFilterTemplates = function(styleTag, btnEl) {
    CORA_STUDIO_STATE.templateFilter = styleTag;
    document.querySelectorAll('.cora-tpl-tab').forEach(function(b) {
        b.className = 'cora-tpl-tab px-3.5 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 cursor-pointer shrink-0';
    });
    if (btnEl) {
        btnEl.className = 'cora-tpl-tab px-3.5 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white shadow-xs cursor-pointer shrink-0';
    }
    coraPopulateVisualTemplateCards(CORA_STUDIO_STATE.selectedCategory, styleTag);
};

window.coraNavWizardStep = function(delta) {
    var targetStep = CORA_STUDIO_STATE.currentStep + delta;
    if (targetStep < 1) {
        coraSwitchVaultView('vault');
        return;
    }
    if (targetStep > 6) {
        coraSaveAndShareFinalDocument();
        coraSwitchVaultView('vault');
        return;
    }

    if (delta > 0 && CORA_STUDIO_STATE.currentStep === 3) {
        var name = document.getElementById('studio-client-name') ? document.getElementById('studio-client-name').value.trim() : '';
        if (!name) { coraShowToast('Enter client name to proceed.'); return; }
    }

    coraJumpToWizardStep(targetStep);
};

window.coraJumpToWizardStep = function(targetStep, isInit) {
    targetStep = parseInt(targetStep, 10);
    if (isNaN(targetStep) || targetStep < 1 || targetStep > 6) return;

    if (targetStep === 3) {
        if (typeof window.coraAutoCollapseDashboardSidebar === 'function') {
            window.coraAutoCollapseDashboardSidebar();
        }
    }

    if (!isInit && targetStep > 3 && CORA_STUDIO_STATE.currentStep <= 3) {
        var name = document.getElementById('studio-client-name') ? document.getElementById('studio-client-name').value.trim() : '';
        if (!name) {
            coraShowToast('Enter client name to proceed.');
            return;
        }
    }

    CORA_STUDIO_STATE.currentStep = targetStep;
    currentWizStep = targetStep;

    // Persist step and state in localStorage
    localStorage.setItem('cora_wiz_step', targetStep);
    try {
        localStorage.setItem('cora_wiz_state', JSON.stringify(CORA_STUDIO_STATE));
    } catch(e) {}

    // Update URL query parameter using replaceState (?cora_view=editor&step=X)
    var urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sub_page', 'vault');
    urlParams.set('vtab', 'editor');
    urlParams.set('cora_view', 'editor');
    urlParams.set('step', targetStep);
    var docId = document.getElementById('studio-doc-id') ? document.getElementById('studio-doc-id').value : '';
    if (docId) urlParams.set('doc_id', docId);

    var updateUrl = window.location.pathname + '?' + urlParams.toString();
    window.history.replaceState({}, '', updateUrl);

    coraRenderWizardStepUI();
};

window.coraWizardBack = function() {
    if (CORA_STUDIO_STATE.currentStep === 1) {
        coraSwitchVaultView('vault');
    } else {
        coraNavWizardStep(-1);
    }
};

window.coraRenderWizardStepUI = function() {
    var step = CORA_STUDIO_STATE.currentStep;

    for (var s = 1; s <= 6; s++) {
        var el = document.getElementById('sub-page-wiz-step-' + s);
        if (el) el.classList.add('hidden');
    }

    var activeEl = document.getElementById('sub-page-wiz-step-' + step);
    if (activeEl) activeEl.classList.remove('hidden');

    for (var i = 1; i <= 6; i++) {
        var pill = document.getElementById('wiz-step-pill-' + i);
        if (!pill) continue;
        var numEl = pill.querySelector('.wiz-step-num');
        var label = pill.querySelector('.wiz-step-label');
        var line = document.querySelector('.wiz-step-line[data-line="' + i + '"]');

        if (i === step) {
            // Active step — dark pill
            pill.className = 'wiz-step-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-950 cursor-pointer transition-all shrink-0';
            if (numEl) { numEl.className = 'wiz-step-num w-5 h-5 rounded-full bg-white/20 text-white text-[10px] font-bold flex items-center justify-center shrink-0'; numEl.innerHTML = i; }
            if (label) { label.className = 'wiz-step-label text-[11px] font-semibold text-white whitespace-nowrap'; }
        } else if (i < step) {
            // Completed step — dark pill with checkmark
            pill.className = 'wiz-step-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-800 cursor-pointer transition-all shrink-0';
            if (numEl) { numEl.className = 'wiz-step-num w-5 h-5 rounded-full bg-white/20 text-white text-[10px] font-bold flex items-center justify-center shrink-0'; numEl.innerHTML = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"/></svg>'; }
            if (label) { label.className = 'wiz-step-label text-[11px] font-medium text-white whitespace-nowrap'; }
        } else {
            // Upcoming step — light pill
            pill.className = 'wiz-step-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 cursor-pointer transition-all shrink-0 hover:bg-zinc-150';
            if (numEl) { numEl.className = 'wiz-step-num w-5 h-5 rounded-full bg-zinc-200 text-zinc-500 text-[10px] font-bold flex items-center justify-center shrink-0'; numEl.innerHTML = i; }
            if (label) { label.className = 'wiz-step-label text-[11px] font-medium text-zinc-400 whitespace-nowrap'; }
        }

        if (line) {
            line.className = i < step
                ? 'wiz-step-line w-4 border-t border-dashed border-zinc-950 shrink-0'
                : 'wiz-step-line w-4 border-t border-dashed border-zinc-300 shrink-0';
        }
    }

    var titles = {
        1: { title: 'Step 1 of 6: Document Type Selection', sub: 'Select a document category or search document types.' },
        2: { title: 'Step 2 of 6: Choose Visual Template Blueprint', sub: 'Filter and select visual document templates for your project.' },
        3: { title: 'Step 3 of 6: Build Document (Client & Scope)', sub: 'Enter client details, choose presets, and customize line items.' },
        4: { title: 'Step 4 of 6: Live Paper Canvas Preview', sub: 'Review live rendered document canvas, letterhead, and tax calculations.' },
        5: { title: 'Step 5 of 6: Document Generation & Export', sub: 'Compile PDF, verify GST compliance, and review audit trail.' },
        6: { title: 'Step 6 of 6: Save & Share Dispatch Hub', sub: 'Save to vault, dispatch via email or WhatsApp, or request E-Signature.' }
    };

    var info = titles[step] || titles[1];
    var titleEl = document.getElementById('wizard-step-indicator-title');
    if (titleEl) titleEl.textContent = info.title;
    var subEl = document.getElementById('wizard-step-indicator-sub');
    if (subEl) subEl.textContent = info.sub;

    var dockBackBtn = document.getElementById('wiz-dock-back-btn');
    if (dockBackBtn) {
        if (step === 1) dockBackBtn.textContent = '← Back to Vault';
        else dockBackBtn.textContent = '← Previous Step';
    }

    var prevBtn = document.getElementById('wiz-prev-step-btn');
    var nextBtn = document.getElementById('wiz-next-step-btn');

    if (prevBtn) {
        if (step === 1) prevBtn.classList.add('hidden');
        else prevBtn.classList.remove('hidden');
    }

    var chevronSvg = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="9 18 15 12 9 6"/></svg>';
    if (nextBtn) {
        var label = 'Next';
        if (step === 2) label = 'Continue';
        else if (step === 3) label = 'Preview';
        else if (step === 4) label = 'Generate';
        else if (step === 5) label = 'Save & Share';
        else if (step === 6) label = 'Finish';
        nextBtn.innerHTML = '<span>' + label + '</span>' + chevronSvg;
    }

    if (step === 2) {
        coraPopulateVisualTemplateCards(CORA_STUDIO_STATE.selectedCategory || 'proposal', CORA_STUDIO_STATE.templateFilter || 'all');
    } else if (step === 4) {
        var leftP = document.getElementById('step4-left-pages-sidebar');
        var rightP = document.getElementById('step4-right-edit-sidebar');
        var leftBtn = document.getElementById('btn-toggle-step4-pages');
        var rightBtn = document.getElementById('btn-toggle-step4-edit');
        if (leftP) leftP.classList.add('hidden');
        if (rightP) rightP.classList.add('hidden');
        if (leftBtn) {
            leftBtn.classList.remove('bg-zinc-950', 'text-white', 'border-zinc-950');
            leftBtn.classList.add('bg-white', 'border-zinc-300', 'hover:bg-zinc-100', 'text-zinc-900');
        }
        if (rightBtn) {
            rightBtn.classList.remove('bg-zinc-950', 'text-white', 'border-zinc-950');
            rightBtn.classList.add('bg-white', 'border-zinc-300', 'hover:bg-zinc-100', 'text-zinc-900');
        }
        coraRenderPaperPreviewInStep5();
    } else if (step === 5) {
        coraRenderGenerateStepSummary();
    } else if (step === 6) {
        coraRenderSaveShareStepSummary();
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
};

window.coraSelectWizCategoryCard = function(catKey) {
    CORA_STUDIO_STATE.selectedCategory = catKey;
    selectedWizCat = catKey;

    var formattedType = catKey.split('_').map(function(w){ return w.charAt(0).toUpperCase() + w.slice(1); }).join(' ');
    document.getElementById('studio-doc-type').value = formattedType;

    document.querySelectorAll('.cora-wiz-cat-card').forEach(function(card){
        card.classList.remove('border-zinc-950', 'bg-zinc-50/80', 'shadow-xs');
        card.classList.add('border-zinc-200');
        // Reset badge to light style
        var badge = card.querySelector('span');
        if (badge) {
            badge.classList.remove('bg-zinc-950', 'text-white');
            badge.classList.add('bg-zinc-100', 'text-zinc-700', 'border', 'border-zinc-200');
        }
    });

    var activeCard = document.getElementById('wiz-cat-card-' + catKey);
    if (activeCard) {
        activeCard.classList.add('border-zinc-950', 'bg-zinc-50/80', 'shadow-xs');
        activeCard.classList.remove('border-zinc-200');
        // Set badge to dark style
        var activeBadge = activeCard.querySelector('span');
        if (activeBadge) {
            activeBadge.classList.add('bg-zinc-950', 'text-white');
            activeBadge.classList.remove('bg-zinc-100', 'text-zinc-700', 'border', 'border-zinc-200');
        }
    }

    coraPopulateVisualTemplateCards(catKey, CORA_STUDIO_STATE.templateFilter || 'all');
};

window.coraPopulateVisualTemplateCards = function(catKey, styleFilter) {
    var gallery = document.getElementById('wiz-subpage-template-gallery');
    if (!gallery) return;

    catKey = catKey || CORA_STUDIO_STATE.selectedCategory || 'proposal';
    styleFilter = styleFilter || CORA_STUDIO_STATE.templateFilter || 'all';

    var allTpls = window.CORA_TEMPLATES_ALL || [];

    var filtered = allTpls.filter(function(t) {
        var matchCat = !catKey || catKey === 'all' || t.category === catKey || t.id === 'tpl_blank';
        var matchStyle = !styleFilter || styleFilter === 'all' || t.style === styleFilter;
        return matchCat && matchStyle;
    });

    if (filtered.length === 0) {
        filtered = allTpls.filter(function(t) {
            return !styleFilter || styleFilter === 'all' || t.style === styleFilter;
        });
    }

    var html = '';
    filtered.forEach(function(t) {
        var isSelected = CORA_STUDIO_STATE.selectedTemplate === t.id;
        var borderClass = isSelected ? 'border-zinc-950 ring-2 ring-zinc-950 bg-zinc-50/50' : 'border-zinc-200 hover:border-zinc-950';

        html += '<div onclick="coraSelectWizSubpageTemplate(\'' + t.id + '\')" class="p-5 border-2 ' + borderClass + ' bg-white rounded-2xl cursor-pointer transition-all space-y-4 shadow-xs hover:shadow-md flex flex-col justify-between group relative">' +
                (isSelected ? '<span class="absolute top-3 right-3 px-2 py-0.5 rounded bg-zinc-950 text-white text-[9px] font-bold">Selected</span>' : '') +
                '<div class="space-y-3">' +
                '<div class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 space-y-2.5 font-sans relative overflow-hidden group-hover:bg-zinc-100/60 transition-colors">' +
                '<div class="flex items-center justify-between border-b border-zinc-200/80 pb-2">' +
                '<div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-zinc-950 text-white font-extrabold text-[8px] flex items-center justify-center">C</span><span class="font-extrabold text-[9px] text-zinc-900 tracking-tight">CORA STUDIO</span></div>' +
                '<span class="text-[8px] font-mono font-bold text-zinc-500 bg-white px-1.5 py-0.5 border border-zinc-200 rounded">' + (t.style ? t.style.toUpperCase() : 'TEMPLATE') + '</span>' +
                '</div>' +
                '<div class="h-2 bg-zinc-300 rounded w-3/4"></div>' +
                '<div class="h-1.5 bg-zinc-200 rounded w-1/2"></div>' +
                '<div class="space-y-1 pt-1">' +
                '<div class="h-1 bg-zinc-200 rounded w-full"></div>' +
                '<div class="h-1 bg-zinc-200 rounded w-5/6"></div>' +
                '</div>' +
                '<div class="flex justify-between items-center pt-2 border-t border-zinc-200/60 text-[9px] font-mono font-bold text-zinc-800">' +
                '<span>ESTIMATED TOTAL</span><span>' + (t.amount > 0 ? '₹' + t.amount.toLocaleString() : 'CUSTOM') + '</span>' +
                '</div>' +
                '</div>' +
                '<div>' +
                '<div class="flex items-center gap-2 mb-1"><span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-zinc-100 text-zinc-800 border border-zinc-200">' + t.style + '</span></div>' +
                '<h4 class="font-black text-zinc-950 text-sm group-hover:underline">' + t.name + '</h4>' +
                '<p class="text-zinc-500 text-xs mt-1 leading-relaxed">' + t.desc + '</p>' +
                '</div>' +
                '</div>' +
                '<button type="button" class="w-full py-2 ' + (isSelected ? 'bg-zinc-950 text-white' : 'bg-zinc-100 group-hover:bg-zinc-950 group-hover:text-white text-zinc-900') + ' font-bold text-xs rounded-xl transition-all">' + (isSelected ? '✓ Selected Blueprint' : 'Select Blueprint →') + '</button>' +
                '</div>';
    });

    gallery.innerHTML = html;
};

window.coraSelectWizSubpageTemplate = function(tplId) {
    var tpl = (window.CORA_TEMPLATES_ALL || []).find(function(t){ return t.id === tplId; });
    if (!tpl) return;

    CORA_STUDIO_STATE.selectedTemplate = tplId;
    document.getElementById('studio-doc-title-input').value = tpl.name;
    document.getElementById('studio-doc-type').value = tpl.type || 'Proposal';

    var tbody = document.getElementById('studio-line-items-body');
    if (tbody) {
        tbody.innerHTML = '';
        if (tpl.items && tpl.items.length > 0) {
            tpl.items.forEach(function(it){ coraAddStudioLineItem(it); });
        } else if (tpl.id === 'tpl_blank') {
            coraAddStudioLineItem({ desc: 'Custom Service Scope', sac: '998381', qty: 1, rate: 0, tax: 18 });
        }
    }
    coraShowToast('Selected template: ' + tpl.name);
    coraPopulateVisualTemplateCards(CORA_STUDIO_STATE.selectedCategory, CORA_STUDIO_STATE.templateFilter);
};

window.coraRenderGenerateStepSummary = function() {
    var title = document.getElementById('studio-doc-title-input').value || 'Untitled Document';
    var num = document.getElementById('studio-doc-number').value || 'DOC-2026';
    var clientName = document.getElementById('studio-client-name').value || 'Client';
    var grandtotal = document.getElementById('summary-grandtotal') ? document.getElementById('summary-grandtotal').textContent : '₹0';
    var type = document.getElementById('studio-doc-type').value || 'Proposal';

    var filename = (title || 'Proposal').replace(/[^a-zA-Z0-9]/g, '_') + '.pdf';
    var fnEl = document.getElementById('wiz-step5-filename');
    if (fnEl) fnEl.textContent = filename;

    var container = document.getElementById('wiz-generate-summary-box');
    if (container) {
        container.innerHTML = 
            '<div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs font-sans">' +
            '<div class="p-4 bg-zinc-50 rounded-2xl border border-zinc-200 space-y-1">' +
            '<span class="text-[10px] font-bold text-zinc-400 uppercase">Document Ref</span>' +
            '<div class="font-extrabold text-zinc-950 text-sm font-mono">' + num + ' (' + type + ')</div>' +
            '<div class="text-zinc-600 font-bold truncate">' + title + '</div>' +
            '</div>' +
            '<div class="p-4 bg-zinc-50 rounded-2xl border border-zinc-200 space-y-1">' +
            '<span class="text-[10px] font-bold text-zinc-400 uppercase">Client Target</span>' +
            '<div class="font-extrabold text-zinc-950 text-sm">' + clientName + '</div>' +
            '<div class="text-zinc-500 font-mono text-[11px] truncate">' + (document.getElementById('studio-client-email').value || 'No email specified') + '</div>' +
            '</div>' +
            '<div class="p-4 bg-zinc-50 rounded-2xl border border-zinc-200 space-y-1">' +
            '<span class="text-[10px] font-bold text-zinc-400 uppercase">Final Valuation</span>' +
            '<div class="font-black text-zinc-950 text-base font-mono">' + grandtotal + '</div>' +
            '<div class="text-emerald-700 font-bold text-[10px]">✓ GST Tax Calculated</div>' +
            '</div>' +
            '</div>';
    }
};

window.coraDownloadWordDoc = function(fmt) {
    coraShowToast('Downloading ' + (fmt || 'DOCX') + ' document...');
};

window.coraRenderSaveShareStepSummary = function() {
    var docId = document.getElementById('studio-doc-id') ? document.getElementById('studio-doc-id').value : '';
    var title = document.getElementById('studio-doc-title-input') ? document.getElementById('studio-doc-title-input').value : 'Untitled Document';
    var type = document.getElementById('studio-doc-type') ? document.getElementById('studio-doc-type').value : 'Proposal';
    var grandtotal = document.getElementById('summary-grandtotal') ? document.getElementById('summary-grandtotal').textContent : '₹0';
    
    var shareUrl = window.location.origin + window.location.pathname + '?cora_doc=' + (docId || 'new') + '&token=vtoken';
    
    var input = document.getElementById('wiz-step6-link-input');
    if (input) input.value = shareUrl;

    var emailInput = document.getElementById('wiz-step6-email-input');
    if (emailInput && (!emailInput.value || emailInput.value.trim() === '')) {
        emailInput.value = document.getElementById('studio-client-email') ? document.getElementById('studio-client-email').value : '';
    }

    var phoneInput = document.getElementById('wiz-step6-phone-input');
    if (phoneInput && (!phoneInput.value || phoneInput.value.trim() === '')) {
        phoneInput.value = document.getElementById('studio-client-phone') ? document.getElementById('studio-client-phone').value : '';
    }

    var titleEl = document.getElementById('step6-summary-title');
    if (titleEl) titleEl.textContent = title;

    var badgeEl = document.getElementById('step6-summary-category-badge');
    if (badgeEl) badgeEl.textContent = (type || 'PROPOSAL').toUpperCase();

    var gtEl = document.getElementById('step6-summary-grandtotal');
    if (gtEl) gtEl.textContent = grandtotal;
};

window.coraSyncStep4QuickEdit = function() {
    var quickTitle = document.getElementById('step4-quick-title') ? document.getElementById('step4-quick-title').value : '';
    if (quickTitle) {
        document.getElementById('studio-doc-title-input').value = quickTitle;
    }
    coraRenderPaperPreviewInStep5();
};

window.coraSelectPrimaryColor = function(colorHex, btnEl) {
    document.querySelectorAll('.cora-color-swatch').forEach(function(sw){
        sw.className = 'w-7 h-7 rounded-full border-2 border-white hover:scale-105 transition-all cursor-pointer cora-color-swatch';
        sw.classList.remove('active-swatch');
        sw.style.outline = 'none';
    });
    if (btnEl) {
        btnEl.className = 'w-7 h-7 rounded-full border-2 border-zinc-950 ring-2 ring-zinc-950 ring-offset-2 transition-all cursor-pointer cora-color-swatch active-swatch';
        btnEl.setAttribute('data-color', colorHex);
    }

    coraRenderPaperPreviewInStep5();
    coraShowToast('Applied primary color palette: ' + colorHex);
};

window.coraSelectFontFamily = function(fontClass) {
    var canvas = document.getElementById('wiz-step4-paper-preview') || document.getElementById('studio-paper-canvas');
    if (canvas) {
        canvas.classList.remove('font-sans', 'font-serif', 'font-mono');
        canvas.classList.add(fontClass);
    }
    coraShowToast('Updated document font family styling');
};

window.coraSelectPageThumbnail = function(pageNum) {
    var thumbs = document.querySelectorAll('#wiz-page-thumbnails-container > div');
    thumbs.forEach(function(t, idx){
        if (idx + 1 === pageNum) {
            t.className = 'p-3 bg-white border-2 border-zinc-950 rounded-2xl cursor-pointer transition-all hover:bg-zinc-50 group shadow-xs';
        } else {
            t.className = 'p-3 bg-white border border-zinc-200/80 rounded-2xl cursor-pointer transition-all hover:bg-zinc-50 group';
        }
    });

    var targetPageCard = document.getElementById('paper-card-page-' + pageNum);
    if (targetPageCard) {
        targetPageCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    coraShowToast('Viewing Page ' + pageNum + ' layout canvas');
};

window.coraToggleStep4Sidebar = function(side) {
    if (side === 'pages' || side === 'left') {
        var panel = document.getElementById('step4-left-pages-sidebar');
        var btn = document.getElementById('btn-toggle-step4-pages');
        if (panel) {
            panel.classList.toggle('hidden');
            var isOpen = !panel.classList.contains('hidden');
            if (btn) {
                if (isOpen) {
                    btn.classList.remove('bg-white', 'border-zinc-300', 'hover:bg-zinc-100', 'text-zinc-900');
                    btn.classList.add('bg-zinc-950', 'text-white', 'border-zinc-950');
                } else {
                    btn.classList.remove('bg-zinc-950', 'text-white', 'border-zinc-950');
                    btn.classList.add('bg-white', 'border-zinc-300', 'hover:bg-zinc-100', 'text-zinc-900');
                }
            }
        }
    } else if (side === 'edit' || side === 'right' || side === 'quick-edit') {
        var panel = document.getElementById('step4-right-edit-sidebar');
        var btn = document.getElementById('btn-toggle-step4-edit');
        if (panel) {
            panel.classList.toggle('hidden');
            var isOpen = !panel.classList.contains('hidden');
            if (btn) {
                if (isOpen) {
                    btn.classList.remove('bg-white', 'border-zinc-300', 'hover:bg-zinc-100', 'text-zinc-900');
                    btn.classList.add('bg-zinc-950', 'text-white', 'border-zinc-950');
                } else {
                    btn.classList.remove('bg-zinc-950', 'text-white', 'border-zinc-950');
                    btn.classList.add('bg-white', 'border-zinc-300', 'hover:bg-zinc-100', 'text-zinc-900');
                }
            }
        }
    }
};

var wizCustomPageCount = 3;
window.coraAddWizardPage = function() {
    wizCustomPageCount++;
    var countEl = document.getElementById('wiz-page-count');
    if (countEl) countEl.textContent = wizCustomPageCount;
    var topCountEl = document.getElementById('wiz-top-page-count');
    if (topCountEl) topCountEl.textContent = wizCustomPageCount;

    var container = document.getElementById('wiz-page-thumbnails-container');
    if (container) {
        var pageNum = wizCustomPageCount;
        var div = document.createElement('div');
        div.id = 'wiz-thumb-' + pageNum;
        div.onclick = function() { coraSelectPageThumbnail(pageNum); };
        div.className = 'p-3 bg-white border border-zinc-200 rounded-2xl cursor-pointer transition-all hover:bg-zinc-50 group';
        div.innerHTML = '<div class="aspect-[1/1.3] bg-white border border-zinc-200 rounded-lg p-2 flex flex-col justify-between shadow-xs mb-2 relative overflow-hidden">' +
                        '<div class="space-y-1"><div class="w-full h-1 bg-zinc-200 rounded-xs"></div><div class="w-2/3 h-1 bg-zinc-200 rounded-xs"></div></div>' +
                        '<div class="w-full h-2 bg-zinc-100 rounded-xs"></div>' +
                        '</div>' +
                        '<div class="flex items-center justify-between text-xs">' +
                        '<span class="font-bold text-zinc-700">Page ' + pageNum + '</span>' +
                        '<span class="text-[10px] text-zinc-400 font-mono">Custom Page</span>' +
                        '</div>';
        container.appendChild(div);
    }
    coraShowToast('Added Page ' + wizCustomPageCount + ' to document layout scope!');
};

window.coraCopyStep6Link = function() {
    var input = document.getElementById('wiz-step6-link-input');
    if (input) {
        input.select();
        navigator.clipboard.writeText(input.value);
    }
    coraShowToast('Share link copied to clipboard!');
};

window.coraSendStep6Email = function() {
    var email = document.getElementById('wiz-step6-email-input').value.trim();
    if (!email) {
        coraShowToast('Please enter recipient email address.');
        return;
    }
    coraShowToast('Sending document email dispatch to ' + email + '...');
};

window.coraTriggerESignFromStep6 = function() {
    coraShowToast('Initiating E-Signature request flow...');
    coraSwitchVaultView('esign');
};

window.coraShareWhatsAppDirectStep6 = function() {
    var phone = document.getElementById('wiz-step6-phone-input').value.trim().replace(/[^0-9]/g, '');
    var title = document.getElementById('studio-doc-title-input').value || 'Document';
    var shareUrl = document.getElementById('wiz-step6-link-input').value;
    
    var text = encodeURIComponent('Hello,\n\nHere is your official document link for "' + title + '":\n' + shareUrl + '\n\nBest regards,\nCora Studio Workspace');
    var waUrl = phone ? ('https://wa.me/' + (phone.length === 10 ? '91' + phone : phone) + '?text=' + text) : ('https://api.whatsapp.com/send?text=' + text);

    window.open(waUrl, '_blank');
    coraShowToast('Opened WhatsApp dispatch link');
};

window.coraToggleSecPassword = function(isEnabled) {
    var fields = document.getElementById('wiz-sec-password-fields');
    var track = document.getElementById('wiz-sec-password-track');
    var knob = document.getElementById('wiz-sec-password-knob');
    if (fields) {
        if (isEnabled) fields.classList.remove('hidden');
        else fields.classList.add('hidden');
    }
    if (track && knob) {
        if (isEnabled) {
            track.classList.remove('bg-zinc-200');
            track.classList.add('bg-indigo-600');
            knob.classList.remove('translate-x-0');
            knob.classList.add('translate-x-4.5');
        } else {
            track.classList.remove('bg-indigo-600');
            track.classList.add('bg-zinc-200');
            knob.classList.remove('translate-x-4.5');
            knob.classList.add('translate-x-0');
        }
    }
};

window.coraToggleSecExpiry = function(isEnabled) {
    var field = document.getElementById('wiz-sec-expiry-field');
    var track = document.getElementById('wiz-sec-expiry-track');
    var knob = document.getElementById('wiz-sec-expiry-knob');
    if (field) {
        if (isEnabled) field.classList.remove('hidden');
        else field.classList.add('hidden');
    }
    if (track && knob) {
        if (isEnabled) {
            track.classList.remove('bg-zinc-200');
            track.classList.add('bg-amber-600');
            knob.classList.remove('translate-x-0');
            knob.classList.add('translate-x-4.5');
        } else {
            track.classList.remove('bg-amber-600');
            track.classList.add('bg-zinc-200');
            knob.classList.remove('translate-x-4.5');
            knob.classList.add('translate-x-0');
        }
    }
};

window.coraToggleSecWatermark = function(isEnabled) {
    var track = document.getElementById('wiz-sec-watermark-track');
    var knob = document.getElementById('wiz-sec-watermark-knob');
    if (track && knob) {
        if (isEnabled) {
            track.classList.remove('bg-zinc-200');
            track.classList.add('bg-emerald-600');
            knob.classList.remove('translate-x-0');
            knob.classList.add('translate-x-4.5');
        } else {
            track.classList.remove('bg-emerald-600');
            track.classList.add('bg-zinc-200');
            knob.classList.remove('translate-x-4.5');
            knob.classList.add('translate-x-0');
        }
    }
};

window.coraSaveAndShareFinalDocument = function() {
    var id = document.getElementById('studio-doc-id').value || ('doc_' + Date.now());
    var number = document.getElementById('studio-doc-number').value || 'DOC-2026';
    var title = document.getElementById('studio-doc-title-input').value.trim() || 'Untitled Document';
    var type = document.getElementById('studio-doc-type').value || 'Proposal';
    var clientName = document.getElementById('studio-client-name').value.trim() || 'Client';
    var clientEmail = document.getElementById('studio-client-email').value.trim() || '';
    var clientPhone = document.getElementById('studio-client-phone').value.trim() || '';
    var grandTotalText = document.getElementById('summary-grandtotal') ? document.getElementById('summary-grandtotal').textContent : '₹0';
    var grandTotalNum = parseFloat(grandTotalText.replace(/[^0-9.]/g, '')) || 0;

    var watermarkCheckbox = document.getElementById('wiz-sec-watermark-toggle');
    var watermarkVal = (watermarkCheckbox && watermarkCheckbox.checked) ? 'CONFIDENTIAL' : '';

    var newDocObj = {
        id: id,
        number: number,
        title: title,
        type: type,
        client_name: clientName,
        client_email: clientEmail,
        client_phone: clientPhone,
        amount: grandTotalNum,
        grand_total: grandTotalNum,
        status: 'Active',
        signed: false,
        watermark: watermarkVal,
        created_at: new Date().toISOString().split('T')[0]
    };

    var statusEl = document.getElementById('studio-doc-status');
    if (statusEl) statusEl.value = 'Active';

    if (window.CORA_DOCUMENTS) {
        var existingIdx = window.CORA_DOCUMENTS.findIndex(function(d){ return d.id === id; });
        if (existingIdx > -1) {
            window.CORA_DOCUMENTS[existingIdx] = Object.assign({}, window.CORA_DOCUMENTS[existingIdx], newDocObj);
        } else {
            window.CORA_DOCUMENTS.unshift(newDocObj);
        }
    }

    coraUpdateKPICards();
    coraSaveStudioDocument();
    coraShowToast('Document saved to vault and share link active!');
    coraSwitchVaultView('vault');
};

window.coraRenderPaperPreviewInStep5 = function() {
    var titleInput = document.getElementById('studio-doc-title-input');
    var title = (titleInput && titleInput.value) ? titleInput.value : 'Untitled Document';
    
    var quickTitle = document.getElementById('step4-quick-title');
    if (quickTitle && quickTitle.value) {
        title = quickTitle.value;
        if (titleInput) titleInput.value = quickTitle.value;
    } else if (quickTitle && !quickTitle.value) {
        quickTitle.value = title;
    }

    var numEl = document.getElementById('studio-doc-number');
    var num = (numEl && numEl.value) ? numEl.value : 'DOC-2026';

    var clientNameEl = document.getElementById('studio-client-name');
    var clientName = (clientNameEl && clientNameEl.value) ? clientNameEl.value : 'Arjun Sharma';

    var clientEmailEl = document.getElementById('studio-client-email');
    var clientEmail = (clientEmailEl && clientEmailEl.value) ? clientEmailEl.value : '';

    var clientPhoneEl = document.getElementById('studio-client-phone');
    var clientPhone = (clientPhoneEl && clientPhoneEl.value) ? clientPhoneEl.value : '';

    var clientGstinEl = document.getElementById('studio-client-gstin');
    var clientGstin = (clientGstinEl && clientGstinEl.value) ? clientGstinEl.value : '';

    var upiEl = document.getElementById('studio-doc-upi');
    var upi = (upiEl && upiEl.value) ? upiEl.value : 'cora@icici';

    var docTypeEl = document.getElementById('studio-doc-type');
    var docType = (docTypeEl && docTypeEl.value) ? docTypeEl.value : 'Proposal';

    var posEl = document.getElementById('studio-doc-pos');
    var posState = (posEl && posEl.value) ? posEl.value : 'Delhi (07)';

    var subtotalEl = document.getElementById('summary-subtotal');
    var subtotal = subtotalEl ? subtotalEl.textContent : '₹1,50,000';

    var grandtotalEl = document.getElementById('summary-grandtotal');
    var grandtotal = grandtotalEl ? grandtotalEl.textContent : '₹1,77,000';

    var quickCompany = document.getElementById('step4-quick-company');
    var companyName = (quickCompany && quickCompany.value) ? quickCompany.value : 'CORA STUDIO WORKSPACE';

    var quickDate = document.getElementById('step4-quick-date');
    var docDate = (quickDate && quickDate.value) ? quickDate.value : '<?php echo date("Y-m-d"); ?>';
    
    var formattedDate = docDate;
    if (docDate && docDate.indexOf('-') > -1) {
        var dParts = docDate.split('-');
        if (dParts.length === 3) {
            var dObj = new Date(parseInt(dParts[0], 10), parseInt(dParts[1], 10) - 1, parseInt(dParts[2], 10));
            if (!isNaN(dObj)) {
                formattedDate = dObj.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
            }
        }
    }

    var activeColorHex = '#09090b';
    var activeSwatch = document.querySelector('.cora-color-swatch.active-swatch');
    if (activeSwatch && activeSwatch.getAttribute('data-color')) {
        activeColorHex = activeSwatch.getAttribute('data-color');
    }

    var watermarkCheckbox = document.getElementById('wiz-sec-watermark-toggle');
    var watermarkVal = (watermarkCheckbox && watermarkCheckbox.checked) ? 'CONFIDENTIAL' : '';
    if (!watermarkVal && window.CORA_STUDIO_STATE && window.CORA_STUDIO_STATE.documentData && window.CORA_STUDIO_STATE.documentData.watermark) {
        watermarkVal = window.CORA_STUDIO_STATE.documentData.watermark;
    }

    var watermarkOverlay = '';
    if (watermarkVal) {
        watermarkOverlay = '<div class="pointer-events-none absolute inset-0 flex items-center justify-center overflow-hidden opacity-[0.05] select-none z-10">' +
                           '  <div class="text-[72px] font-black tracking-widest text-zinc-950 uppercase -rotate-45 font-sans">CONFIDENTIAL</div>' +
                           '</div>';
    }

    var previewContainer = document.getElementById('wiz-step4-paper-preview') || document.getElementById('studio-paper-canvas');
    if (!previewContainer) return;

    var html = '';

    // ==========================================
    // PAGE 1 CARD: SCOPE & COVER HEADER
    // ==========================================
    html += '<div id="paper-card-page-1" class="w-full bg-white shadow-xs rounded-3xl p-6 sm:p-10 space-y-8 border border-zinc-200/80 min-h-[750px] relative">';
    if (watermarkVal) html += watermarkOverlay;
    
    // Header Letterhead
    html += '  <div class="border-b border-zinc-200/80 pb-6 flex items-start justify-between flex-wrap gap-4">';
    html += '    <div>';
    html += '      <h2 class="text-xl sm:text-2xl font-black text-zinc-950 tracking-tight flex items-center gap-3">';
    html += '        <span id="paper-logo-badge" class="w-8 h-8 rounded-xl bg-black text-white text-xs font-black flex items-center justify-center shadow-xs">' + (companyName.charAt(0) || 'C') + '</span>';
    html += '        <span id="paper-company-name-text" class="uppercase tracking-tight">' + companyName + '</span>';
    html += '      </h2>';
    html += '      <p class="text-xs text-zinc-500 mt-1 font-medium">GST Registered Tax Billing Statement & Service Contract</p>';
    html += '    </div>';
    html += '    <div class="flex items-center gap-4 text-right shrink-0">';
    html += '      <div class="p-2.5 bg-zinc-50 border border-zinc-200/80 rounded-2xl text-center flex flex-col items-center shadow-xs">';
    html += '        <div class="w-14 h-14 bg-zinc-50 border border-zinc-200/80 text-zinc-500 font-mono text-[9px] flex items-center justify-center rounded-xl p-1 text-center font-bold tracking-tighter leading-tight shadow-inner">';
    html += '          UPI QR';
    html += '        </div>';
    html += '        <span class="text-[9px] font-mono text-zinc-600 font-semibold mt-1" id="paper-upi-tag">UPI: ' + upi + '</span>';
    html += '      </div>';
    html += '      <div class="space-y-1">';
    html += '        <span id="paper-doc-type-badge" class="px-3 py-1 rounded-md bg-black text-white font-black text-[10px] uppercase tracking-wider transition-colors inline-block">' + docType.toUpperCase() + '</span>';
    html += '        <div id="paper-doc-number" class="text-xs font-extrabold text-zinc-900 font-mono">' + num + '</div>';
    html += '        <div id="paper-doc-date-display" class="text-[10px] text-zinc-500 font-mono font-medium">' + formattedDate + '</div>';
    html += '      </div>';
    html += '    </div>';
    html += '  </div>';

    // Billed To & Purpose Card
    html += '  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-zinc-50/80 p-6 rounded-2xl border border-zinc-200/60">';
    html += '    <div class="space-y-1">';
    html += '      <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Billed To / Client</span>';
    html += '      <strong class="text-sm font-black text-zinc-950 block">' + clientName + '</strong>';
    if (clientEmail || clientPhone) {
        html += '      <div class="text-xs text-zinc-600 font-medium">' + (clientEmail ? clientEmail : '') + (clientEmail && clientPhone ? ' • ' : '') + (clientPhone ? clientPhone : '') + '</div>';
    }
    if (clientGstin) {
        html += '      <div class="font-mono text-[11px] text-zinc-500 font-medium mt-1">GSTIN: <span class="font-bold text-zinc-800">' + clientGstin + '</span></div>';
    }
    html += '      <div class="text-[10px] text-zinc-400 font-medium">Place of Supply: ' + posState + '</div>';
    html += '    </div>';
    html += '    <div class="text-left sm:text-right space-y-1 sm:border-l sm:border-zinc-200/60 sm:pl-4">';
    html += '      <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Document Purpose</span>';
    html += '      <h3 class="text-xs font-bold text-zinc-900 leading-snug line-clamp-2">' + title + '</h3>';
    html += '      <div class="pt-2 mt-2 border-t border-zinc-200/60">';
    html += '        <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-wider block">Bill Total to Payable</span>';
    html += '        <div class="text-lg font-black text-zinc-950 font-mono tracking-tight">' + grandtotal + '</div>';
    html += '      </div>';
    html += '    </div>';
    html += '  </div>';

    // Scope Summary Block
    html += '  <div class="space-y-3 pt-2">';
    html += '    <h4 class="text-xs font-extrabold uppercase tracking-wider text-zinc-950">PROJECT SCOPE & OBJECTIVES</h4>';
    html += '    <p class="text-xs text-zinc-600 leading-relaxed">This document serves as an official billing statement and service agreement between ' + companyName + ' and ' + clientName + '. All deliverables, timelines, and payment structures outlined herein are binding upon approval.</p>';
    html += '  </div>';

    // Page 1 Footer
    html += '  <div class="border-t border-zinc-200/80 pt-4 mt-6 flex items-center justify-between text-[10px] text-zinc-400 font-mono">';
    html += '    <span>' + companyName + ' • Scope</span>';
    html += '    <span>Page 1 of 3</span>';
    html += '  </div>';
    html += '</div>';

    // ==========================================
    // PAGE 2 CARD: LINE ITEMS GST TABLE
    // ==========================================
    html += '<div id="paper-card-page-2" class="w-full bg-white shadow-xs rounded-3xl p-6 sm:p-10 space-y-8 border border-zinc-200/80 min-h-[750px] relative mt-8">';
    if (watermarkVal) html += watermarkOverlay;
    
    // Page 2 Header
    html += '  <div class="border-b border-zinc-200/80 pb-4 flex items-center justify-between">';
    html += '    <h3 class="text-xs font-black text-zinc-950 uppercase tracking-tight">' + companyName + ' • SERVICES & LINE ITEMS MATRIX</h3>';
    html += '    <span class="text-[10px] font-mono text-zinc-400">Ref: ' + num + '</span>';
    html += '  </div>';

    // Line Items Table
    html += '  <div class="overflow-x-auto rounded-xl border border-zinc-200/80">';
    html += '    <table class="w-full text-left border-collapse text-xs">';
    html += '      <thead class="bg-white text-zinc-800 font-extrabold border-b border-zinc-200/80 text-[11px] uppercase tracking-wider">';
    html += '        <tr>';
    html += '          <th class="p-3">Item Description</th>';
    html += '          <th class="p-3 text-center">HSN/SAC</th>';
    html += '          <th class="p-3 text-center">Qty</th>';
    html += '          <th class="p-3 text-right">Rate</th>';
    html += '          <th class="p-3 text-right">Amount</th>';
    html += '        </tr>';
    html += '      </thead>';
    html += '      <tbody class="divide-y divide-zinc-100 bg-white">';

    var rows = document.querySelectorAll('.cora-line-item-row');
    if (rows && rows.length > 0) {
        rows.forEach(function(row){
            var desc = row.querySelector('.item-desc') ? row.querySelector('.item-desc').value : '';
            var sac = row.querySelector('.item-sac') ? row.querySelector('.item-sac').value : '';
            var qty = row.querySelector('.item-qty') ? row.querySelector('.item-qty').value : '1';
            var rate = row.querySelector('.item-rate') ? row.querySelector('.item-rate').value : '0';
            var lineTot = row.querySelector('.item-line-total') ? row.querySelector('.item-line-total').textContent : '₹0';
            var rateNum = parseFloat(rate) || 0;
            var formattedRate = '₹' + rateNum.toLocaleString('en-IN');

            html += '        <tr class="hover:bg-zinc-50/50 transition-colors">';
            html += '          <td class="p-3 font-semibold text-zinc-900">' + (desc || 'Service Item') + '</td>';
            html += '          <td class="p-3 text-center font-mono text-[11px] text-zinc-500">' + (sac || '998381') + '</td>';
            html += '          <td class="p-3 text-center font-bold text-zinc-800">' + qty + '</td>';
            html += '          <td class="p-3 text-right font-mono text-zinc-700">' + formattedRate + '</td>';
            html += '          <td class="p-3 text-right font-mono font-bold text-zinc-950">' + lineTot + '</td>';
            html += '        </tr>';
        });
    } else {
        html += '        <tr><td colspan="5" class="p-4 text-center text-zinc-400">No items added yet.</td></tr>';
    }
    html += '      </tbody>';
    html += '    </table>';
    html += '  </div>';

    html += '  <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-2 mt-4 max-w-xs ml-auto text-xs">';
    html += '    <div class="flex justify-between text-zinc-600"><span>Subtotal:</span><span class="font-bold text-zinc-800">' + subtotal + '</span></div>';
    html += '    <div class="flex justify-between text-zinc-500 text-[11px]"><span>Tax Breakdown:</span><span class="font-mono text-zinc-700">CGST + SGST (18%)</span></div>';
    html += '    <div class="flex justify-between text-sm font-black text-zinc-950 border-t border-zinc-200/80 pt-1.5 mt-1 font-sans"><span>Grand Total:</span><span class="font-mono text-zinc-950">' + grandtotal + '</span></div>';
    html += '  </div>';
    html += '</div>';

    // 5. Instant UPI Direct Payment Card (Pixel-for-pixel match with user mockup)
    html += '<div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/60 flex items-center justify-between flex-wrap gap-4 mt-6">';
    html += '  <div class="flex items-center gap-3.5">';
    html += '    <div class="w-10 h-10 bg-black text-white font-extrabold text-xs flex items-center justify-center rounded-xl shrink-0 shadow-xs tracking-tighter">LP»</div>';
    html += '    <div class="space-y-0.5">';
    html += '      <div class="font-extrabold text-xs text-zinc-950">Instant UPI Direct Payment</div>';
    html += '      <div class="text-[11px] text-zinc-500 font-medium">Scan & pay directly from any UPI app</div>';
    html += '    </div>';
    html += '  </div>';
    html += '  <button type="button" onclick="coraShowToast(\'Pair with another UPI requested\')" class="text-xs font-extrabold text-zinc-700 hover:text-black transition-colors cursor-pointer bg-transparent border-none p-0">+ Pair with another UPI</button>';
    html += '</div>';

    // 5b. Signature Section
    html += '<div class="pt-6 border-t border-zinc-200/80 mt-6">';
    html += '  <h4 class="text-[11px] font-black uppercase tracking-wider text-zinc-950 mb-4">Authorization & E-Signature</h4>';
    html += '  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">';
    
    // Prepared by
    html += '    <div class="p-4 bg-zinc-55/40 border border-zinc-200 rounded-2xl space-y-2">';
    html += '      <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-wider block">Prepared By</span>';
    html += '      <div class="h-10 border-b border-dashed border-zinc-200 flex items-end pb-1 font-serif text-xs italic text-zinc-800">Cora Studio Representative</div>';
    html += '      <div class="text-[10px] text-zinc-500 font-medium">Date: ' + formattedDate + '</div>';
    html += '    </div>';

    // Client Signature
    html += '    <div class="p-4 bg-zinc-55/40 border border-zinc-200 rounded-2xl space-y-2">';
    html += '      <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-wider block">Client Acceptance</span>';
    
    var isDocSigned = false;
    var signerName = "";
    var signedDate = "";
    var signatureImg = "";
    
    var activeDocId = document.getElementById("studio-doc-id") ? document.getElementById("studio-doc-id").value : "";
    if (activeDocId && window.CORA_DOCUMENTS) {
        var existingDoc = window.CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(activeDocId); });
        if (existingDoc && existingDoc.signed) {
            isDocSigned = true;
            signerName = existingDoc.signer_name || clientName;
            signedDate = existingDoc.signed_at || formattedDate;
            signatureImg = existingDoc.signature_image || "";
        }
    }
    
    if (isDocSigned) {
        if (signatureImg) {
            html += '      <div class="h-10 border-b border-dashed border-zinc-200 flex items-center justify-center pb-1"><img src="' + signatureImg + '" class="h-8 object-contain" /></div>';
        } else {
            html += '      <div class="h-10 border-b border-dashed border-zinc-200 flex items-end pb-1 font-serif text-xs italic text-zinc-800">' + signerName + '</div>';
        }
        html += '      <div class="text-[10px] text-zinc-500 font-medium">Signed: ' + signedDate + '</div>';
    } else {
        html += '      <div class="h-10 border-b border-dashed border-zinc-200 flex items-center justify-center pb-1 text-zinc-400 text-[10px] italic">Signature Pending</div>';
        html += '      <div class="text-[10px] text-zinc-500 font-medium">Client: ' + clientName + '</div>';
    }
    
    html += '    </div>';
    html += '  </div>';
    html += '</div>';

    // 6. Footer Disclaimer
    html += '<div class="border-t border-zinc-200/80 pt-6 mt-8 text-center text-[10px] text-zinc-400 font-mono">';
    html += '  © <?php echo date("Y"); ?> <span id="paper-footer-company">' + companyName + '</span>. GSTIN: 07AAAAA0000A1Z5. Confidential & Proprietary Document.';
    html += '</div>';

    previewContainer.innerHTML = html;
};

// 1-CLICK CONVERT QUOTATION TO TAX INVOICE
window.coraConvertQuoteToInvoice = function(docId) {
    var doc = CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(docId); });
    if (!doc) return;

    coraShowToast('Converting Quotation to GST Tax Invoice...');
    var newNum = 'DOC-2026';
    var newTitle = doc.title.replace('Proposal:', 'Invoice:').replace('Quotation:', 'Invoice:');

    jQuery.ajax({
        url: coraREData.ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_save_document',
            nonce: coraREData.ajaxNonce,
            id: '',
            number: newNum,
            title: newTitle,
            type: 'Invoice',
            status: 'Sent',
            client_name: doc.client_name,
            client_email: doc.client_email,
            client_phone: doc.client_phone || '',
            client_gstin: doc.client_gstin || '',
            pos_state: doc.pos_state || 'Delhi (07)',
            upi_vpa: doc.upi_vpa || 'cora@icici',
            items: JSON.stringify(doc.items || [])
        },
        success: function(r) {
            if (r.success) {
                coraShowToast('Quotation converted to Tax Invoice!');
                setTimeout(function(){ location.reload(); }, 600);
            } else {
                coraShowToast(r.data || 'Conversion failed.');
            }
        }
    });
};

// AUTO-FILL ENGINE HANDLERS
window.coraAutoFillPackage = function(pkgKey) {
    if (!pkgKey) return;
    var tbody = document.getElementById('studio-line-items-body');
    tbody.innerHTML = '';

    if (pkgKey === 'wedding') {
        document.getElementById('studio-doc-title-input').value = 'Proposal: Luxury Wedding Cinematography & Fine-Art Album';
        coraAddStudioLineItem({ desc: '3-Day Full Wedding Cinematography & Aerial Drone', sac: '998381', qty: 1, rate: 300000, tax: 18 });
        coraAddStudioLineItem({ desc: 'Candid Fine-Art Photography & Signature Album Box', sac: '998381', qty: 1, rate: 150000, tax: 18 });
    } else if (pkgKey === 'realty') {
        document.getElementById('studio-doc-title-input').value = 'Proposal: Commercial Real Estate Listing 4K Walkthrough';
        coraAddStudioLineItem({ desc: 'Architectural HDR Photography & 4K Walkthrough Video', sac: '998381', qty: 1, rate: 120000, tax: 18 });
    } else if (pkgKey === 'event') {
        document.getElementById('studio-doc-title-input').value = 'Proposal: Commercial Corporate Summit Film Coverage';
        coraAddStudioLineItem({ desc: 'Multi-Camera Corporate Summit Film Coverage', sac: '998381', qty: 1, rate: 350000, tax: 18 });
    } else if (pkgKey === 'retainer') {
        document.getElementById('studio-doc-title-input').value = 'Invoice: Monthly Studio Content Production Retainer';
        coraAddStudioLineItem({ desc: 'Monthly Studio Content Retainer (4 Shoots/mo)', sac: '998381', qty: 1, rate: 150000, tax: 18 });
    }
    coraShowToast('Auto-populated package blueprint!');
};

window.coraAutoAddGear = function(gearKey) {
    if (!gearKey) return;
    var days = parseFloat(document.getElementById('auto-days-multiplier').value) || 1;

    if (gearKey === 'red_komodo') coraAddStudioLineItem({ desc: 'RED Komodo 6K Cinema Camera Rig Rental', sac: '997311', qty: days, rate: 15000, tax: 18 });
    else if (gearKey === 'sony_a7s3') coraAddStudioLineItem({ desc: 'Sony A7S III + G-Master Prime Lenses Package', sac: '997311', qty: days, rate: 8000, tax: 18 });
    else if (gearKey === 'dji_drone') coraAddStudioLineItem({ desc: 'DJI Mavic 3 Cine Aerial Drone Kit', sac: '997311', qty: days, rate: 12000, tax: 18 });
    else if (gearKey === 'aputure_light') coraAddStudioLineItem({ desc: 'Aputure 600d Cinema Light & Softbox Package', sac: '997311', qty: days, rate: 6000, tax: 18 });

    document.getElementById('auto-gear-select').value = '';
    coraShowToast('Added gear rental item!');
};

window.coraAutoAddCrew = function(crewKey) {
    if (!crewKey) return;
    var days = parseFloat(document.getElementById('auto-days-multiplier').value) || 1;

    if (crewKey === 'lead_photog') coraAddStudioLineItem({ desc: 'Lead Director / Principal Cinematographer', sac: '998381', qty: days, rate: 25000, tax: 18 });
    else if (crewKey === 'assoc_photog') coraAddStudioLineItem({ desc: 'Associate Photographer / B-Cam Operator', sac: '998381', qty: days, rate: 15000, tax: 18 });
    else if (crewKey === 'drone_pilot') coraAddStudioLineItem({ desc: 'Certified Commercial Drone Pilot', sac: '998381', qty: days, rate: 18000, tax: 18 });
    else if (crewKey === 'sr_editor') coraAddStudioLineItem({ desc: 'Senior Post-Production Editor & Colorist', sac: '998381', qty: 1, rate: 35000, tax: 18 });
    else if (crewKey === 'grip_assistant') coraAddStudioLineItem({ desc: 'On-Set Lighting Grip & Production Assistant', sac: '998381', qty: days, rate: 5000, tax: 18 });

    document.getElementById('auto-crew-select').value = '';
    coraShowToast('Added crew member allotment!');
};

window.coraApplyShootDaysMultiplier = function(daysVal) {
    var days = parseFloat(daysVal) || 1;
    document.querySelectorAll('.cora-line-item-row').forEach(function(row){
        var desc = row.querySelector('.item-desc').value.toLowerCase();
        if (desc.indexOf('cinematography') > -1 || desc.indexOf('rental') > -1 || desc.indexOf('photographer') > -1 || desc.indexOf('drone') > -1) {
            row.querySelector('.item-qty').value = days;
        }
    });
    coraRecalculateStudioTotals();
    coraShowToast('Updated shoot days multiplier: ' + days + ' days');
};

// DEDICATED PRINT FUNCTION
window.coraPrintInvoiceOnly = function() {
    var title = document.getElementById('studio-doc-title-input').value || 'Invoice';
    var num = document.getElementById('studio-doc-number').value || 'DOC-2026';
    var type = document.getElementById('studio-doc-type').value || 'Invoice';
    var clientName = document.getElementById('studio-client-name').value || 'Client';
    var clientGstin = document.getElementById('studio-client-gstin').value || '';
    var clientPhone = document.getElementById('studio-client-phone').value || '';
    var upi = document.getElementById('studio-doc-upi').value || 'cora@icici';
    var subtotal = document.getElementById('summary-subtotal').textContent;
    var grandtotal = document.getElementById('summary-grandtotal').textContent;
    var deposit = document.getElementById('summary-deposit').textContent;

    var printableCanvas = document.getElementById('cora-printable-canvas');
    if (!printableCanvas) return;

    var html = '<div style="font-family: sans-serif; padding: 20px; color: #09090b; max-width: 800px; margin: 0 auto; background: #fff;">' +
               '<div style="border-bottom: 2px solid #e4e4e7; padding-bottom: 20px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">' +
               '<div><h1 style="font-size: 22px; font-weight: 800; margin: 0; letter-spacing: -0.5px;">CORA STUDIO WORKSPACE</h1>' +
               '<p style="font-size: 11px; color: #71717a; margin: 4px 0 0 0;">Official GST Registered Tax Billing Statement</p>' +
               '<p style="font-size: 10px; font-family: monospace; color: #52525b; margin: 4px 0 0 0;">GSTIN: 07AAAAA0000A1Z5 | Delhi, India</p></div>' +
               '<div style="text-align: right;"><div style="background: #09090b; color: #fff; padding: 4px 12px; font-weight: 700; font-size: 10px; border-radius: 4px; display: inline-block; text-transform: uppercase;">' + type.toUpperCase() + '</div>' +
               '<div style="font-family: monospace; font-weight: 700; font-size: 13px; margin-top: 6px;">' + num + '</div></div></div>' +

               '<div style="background: #f4f4f5; padding: 16px; border-radius: 8px; margin-bottom: 24px; display: flex; justify-content: space-between;">' +
               '<div><span style="font-size: 10px; font-weight: 700; color: #71717a; text-transform: uppercase; display: block;">Billed To:</span>' +
               '<strong style="font-size: 14px; color: #09090b;">' + clientName + '</strong>' +
               (clientGstin ? '<div style="font-family: monospace; font-size: 11px; color: #52525b;">GSTIN: ' + clientGstin + '</div>' : '') +
               (clientPhone ? '<div style="font-size: 11px; color: #52525b;">Phone: ' + clientPhone + '</div>' : '') + '</div>' +
               '<div style="text-align: right;"><span style="font-size: 10px; font-weight: 700; color: #71717a; text-transform: uppercase; display: block;">Total Payable:</span>' +
               '<div style="font-size: 20px; font-weight: 800; font-family: monospace; color: #09090b;">' + grandtotal + '</div></div></div>' +

               '<table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 24px;">' +
               '<thead><tr style="background: #f4f4f5; border-bottom: 1px solid #e4e4e7;"><th style="padding: 10px; text-align: left;">Item Description</th><th style="padding: 10px; text-align: left;">SAC</th><th style="padding: 10px; text-align: center;">Qty</th><th style="padding: 10px; text-align: right;">Rate</th><th style="padding: 10px; text-align: right;">Amount</th></tr></thead><tbody>';

    var rows = document.querySelectorAll('.cora-line-item-row');
    rows.forEach(function(row){
        var d = row.querySelector('.item-desc').value;
        var s = row.querySelector('.item-sac').value;
        var q = row.querySelector('.item-qty').value;
        var r = row.querySelector('.item-rate').value;
        var a = row.querySelector('.item-line-total').textContent;
        html += '<tr style="border-bottom: 1px solid #f4f4f5;"><td style="padding: 10px; font-weight: 600;">' + d + '</td><td style="padding: 10px; font-family: monospace; font-size: 11px;">' + s + '</td><td style="padding: 10px; text-align: center; font-weight: 700;">' + q + '</td><td style="padding: 10px; text-align: right; font-family: monospace;">₹' + parseFloat(r).toLocaleString() + '</td><td style="padding: 10px; text-align: right; font-family: monospace; font-weight: 700;">' + a + '</td></tr>';
    });

    html += '</tbody></table>' +
            '<div style="display: flex; justify-content: space-between; border-top: 1px solid #e4e4e7; padding-top: 16px;">' +
            '<div style="font-size: 11px; color: #52525b;"><strong style="color: #09090b;">Payment Terms:</strong> Net 15 Days.<br>Pay via UPI ID: <span style="font-family: monospace; font-weight: 700;">' + upi + '</span></div>' +
            '<div style="text-align: right; font-size: 12px; space-y: 4px;">' +
            '<div style="color: #71717a;">Taxable Subtotal: ' + subtotal + '</div>' +
            '<div style="font-size: 16px; font-weight: 800; color: #09090b; margin-top: 4px;">Grand Total (Incl. GST): ' + grandtotal + '</div>' +
            '<div style="color: #09090b; font-weight: 700; font-size: 11px; margin-top: 4px;">Retainer Deposit (50%): ' + deposit + '</div>' +
            '</div></div>' +
            '<div style="border-top: 1px solid #e4e4e7; margin-top: 40px; padding-top: 16px; text-align: center; font-size: 10px; color: #a1a1aa;">© 2026 Cora Studio Workspace. GSTIN: 07AAAAA0000A1Z5. Confidential Document.</div></div>';

    printableCanvas.innerHTML = html;
    window.print();
};

// ═════════════════════════════════════════════════════════════════════════
// STEP 3: DOCUMENT BUILDER MAIN CANVAS INTERACTION CONTROLLERS
// ═════════════════════════════════════════════════════════════════════════
window.coraCanvasZoomLevel = 100;

window.coraChangeZoom = function(delta) {
    var newZoom = window.coraCanvasZoomLevel + delta;
    if (newZoom < 50) newZoom = 50;
    if (newZoom > 150) newZoom = 150;
    window.coraCanvasZoomLevel = newZoom;
    var el = document.getElementById('studio-center-paper-doc');
    if (el) {
        el.style.transform = 'scale(' + (newZoom / 100) + ')';
    }
    var txt = document.getElementById('zoom-percentage-text');
    if (txt) txt.textContent = newZoom + '%';
};

window.coraScrollToSection = function(secId) {
    var el = document.getElementById(secId);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

window.coraSyncCanvasFields = function() {
    var clientInput = document.getElementById('studio-client-name');
    var clientVal = (clientInput && clientInput.value.trim()) ? clientInput.value.trim() : '{{Client Name}}';
    
    var titleDisplay = document.getElementById('canvas-client-name-display');
    if (titleDisplay) titleDisplay.textContent = clientVal;
    
    var sigName = document.getElementById('canvas-client-sig-name');
    if (sigName) sigName.textContent = clientVal;

    var docTitleInput = document.getElementById('studio-doc-title-input');
    if (docTitleInput) {
        var propTitle = document.getElementById('canvas-proposal-title');
        if (propTitle && docTitleInput.value.trim()) {
            propTitle.innerHTML = docTitleInput.value.trim() + ' for <span id="canvas-client-name-display" class="underline decoration-zinc-300 underline-offset-4">' + clientVal + '</span>';
        }
    }
};

window.coraHandleLogoUpload = function(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var prev = document.getElementById('cora-logo-preview');
            if (prev) {
                prev.innerHTML = '<img src="' + e.target.result + '" alt="Studio Logo" class="max-h-16 object-contain rounded-lg shadow-xs"><span class="text-[10px] text-zinc-400 font-mono">Logo uploaded</span>';
            }
            coraShowToast('Studio logo updated on canvas!');
        };
        reader.readAsDataURL(input.files[0]);
    }
};

window.coraAddCanvasBlock = function(blockType) {
    var container = document.getElementById('canvas-dynamic-blocks');
    if (!container) return;

    var blockId = 'dynamic_block_' + Math.floor(Math.random() * 100000);
    var div = document.createElement('div');
    div.id = blockId;
    div.className = 'p-4 bg-zinc-50 border border-zinc-200 rounded-2xl space-y-2 relative group hover:border-zinc-950 transition-all';

    var templates = {
        text: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Text Block</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div contenteditable="true" class="text-xs text-zinc-700 outline-none p-2.5 bg-white rounded-xl border border-zinc-200">Enter custom paragraph text here...</div>',
        heading: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Heading Block</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><h3 contenteditable="true" class="text-lg font-black text-zinc-950 outline-none p-2.5 bg-white rounded-xl border border-zinc-200">Section Header Title</h3>',
        image: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Image Attachment</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div class="h-32 border-2 border-dashed border-zinc-300 rounded-xl flex flex-col items-center justify-center text-zinc-400 text-xs font-semibold cursor-pointer hover:border-zinc-950"><span>Click or drop image asset here</span></div>',
        table: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Data Table</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><table class="w-full text-xs text-left border border-zinc-200 rounded-xl overflow-hidden"><tr class="bg-zinc-100 font-bold"><td class="p-2">Feature</td><td class="p-2">Specification</td></tr><tr><td class="p-2">Resolution</td><td class="p-2">4K Cinema UHD</td></tr></table>',
        divider: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Divider Line</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><hr class="border-zinc-300 my-2">',
        spacer: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Vertical Spacer</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div class="h-8 bg-zinc-100/50 rounded-lg flex items-center justify-center text-[10px] font-mono text-zinc-400">Spacer (32px)</div>',
        services_table: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Services Breakdown</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div class="text-xs font-bold text-zinc-900">Custom Services Scope Matrix Block added.</div>',
        pricing_table: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Pricing Summary</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div class="text-xs font-bold text-zinc-900">Custom Pricing Matrix Block added.</div>',
        timeline: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Project Milestone</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div contenteditable="true" class="text-xs font-semibold p-2.5 bg-white rounded-xl border border-zinc-200">Milestone: Pre-Production Shoot Prep (Day 1)</div>',
        testimonial: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Client Review Quote</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><blockquote contenteditable="true" class="italic text-xs text-zinc-700 border-l-2 border-zinc-950 pl-3">"Cora Studio delivered world-class video production for our brand." - Corporate Client</blockquote>',
        faq: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>FAQ Accordion</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div class="text-xs space-y-1"><div class="font-bold text-zinc-900">Q: What is turnaround time?</div><div class="text-zinc-600">A: Standard turnaround is 5-7 business days.</div></div>',
        cover: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Cover Page Block</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div class="p-6 bg-zinc-950 text-white rounded-xl text-center font-bold text-sm">Cover Header Banner</div>',
        client_info: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Client Info Block</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div class="text-xs text-zinc-700">Client Contact & Billing Details Block</div>',
        signature_info: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>Signature Block</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div class="h-16 border-b-2 border-zinc-950 flex items-end text-xs font-bold">Client Signature Line</div>',
        file_upload: '<div class="flex items-center justify-between text-xs font-bold text-zinc-400 uppercase"><span>File Upload Attachment</span><button type="button" onclick="document.getElementById(\'' + blockId + '\').remove()" class="text-zinc-400 hover:text-zinc-950 font-bold p-1 cursor-pointer">✕</button></div><div class="border-2 border-dashed border-zinc-300 rounded-xl p-4 text-center text-xs font-bold text-zinc-600">Drag files or click to upload attachment</div>'
    };

    div.innerHTML = templates[blockType] || '<div class="text-xs font-bold">Custom Block</div>';
    container.appendChild(div);
    coraShowToast('Added ' + blockType.replace('_', ' ') + ' block to canvas!');
};

window.coraDragBlockStart = function(ev, blockType) {
    ev.dataTransfer.setData('text/plain', blockType);
};

window.coraHandleCanvasDrop = function(ev) {
    ev.preventDefault();
    var blockType = ev.dataTransfer.getData('text/plain');
    if (blockType) {
        coraAddCanvasBlock(blockType);
    }
};

window.coraCanvasUndo = function() {
    coraShowToast('Undo last action');
};

window.coraCanvasRedo = function() {
    coraShowToast('Redo action');
};

window.coraTriggerAIAssistant = function() {
    coraShowToast('AI Assistant activated! Suggesting document scope & line items...');
};

window.coraOpenDocSettingsDrawer = function() {
    coraShowToast('Document Settings panel opened.');
};

// LINE ITEMS DYNAMIC MANAGER
window.coraAddStudioLineItem = function(itemData) {
    var tbody = document.getElementById('studio-line-items-body');
    if (!tbody) return;
    var rowId = 'item_row_' + Math.floor(Math.random() * 100000);

    var desc = itemData ? itemData.desc : '';
    var sac  = itemData ? (itemData.sac || '998381') : '998381';
    var qty  = itemData ? (itemData.qty || 1) : 1;
    var rate = itemData ? (itemData.rate || 0) : 0;
    var tax  = itemData ? (itemData.tax || 18) : 18;

    var tr = document.createElement('tr');
    tr.id = rowId;
    tr.className = 'hover:bg-zinc-50/80 transition-colors cora-line-item-row';
    tr.innerHTML = '<td class="p-2.5"><input type="text" name="item_desc[]" class="item-desc w-full border border-zinc-200 rounded-lg p-1.5 text-xs outline-none focus:border-zinc-950 transition-colors font-medium" value="' + desc + '" placeholder="Item name / service scope..."></td>' +
                   '<td class="p-2.5"><input type="text" name="item_sac[]" class="item-sac w-20 border border-zinc-200 rounded-lg p-1.5 text-xs font-mono outline-none focus:border-zinc-950 transition-colors" value="' + sac + '" placeholder="998381"></td>' +
                   '<td class="p-2.5"><input type="number" name="item_qty[]" class="item-qty w-14 border border-zinc-200 rounded-lg p-1.5 text-xs outline-none focus:border-zinc-950 transition-colors text-center font-bold" value="' + qty + '" onchange="coraRecalculateStudioTotals()"></td>' +
                   '<td class="p-2.5"><input type="number" name="item_rate[]" class="item-rate w-24 border border-zinc-200 rounded-lg p-1.5 text-xs outline-none focus:border-zinc-950 transition-colors font-bold" value="' + rate + '" onchange="coraRecalculateStudioTotals()"></td>' +
                   '<td class="p-2.5"><select name="item_tax[]" class="item-tax border border-zinc-200 rounded-lg p-1.5 text-xs outline-none focus:border-zinc-950 transition-colors" onchange="coraRecalculateStudioTotals()"><option value="18" ' + (tax==18?'selected':'') + '>18% GST</option><option value="12" ' + (tax==12?'selected':'') + '>12% GST</option><option value="5" ' + (tax==5?'selected':'') + '>5% GST</option><option value="0" ' + (tax==0?'selected':'') + '>0% GST</option></select></td>' +
                   '<td class="p-2.5 text-right font-bold text-zinc-950 font-mono item-line-total">₹0</td>' +
                   '<td class="p-2.5 text-center"><button type="button" onclick="document.getElementById(\'' + rowId + '\').remove(); coraRecalculateStudioTotals();" class="text-zinc-400 hover:text-zinc-950 font-bold p-1">✕</button></td>';

    tbody.appendChild(tr);
    coraRecalculateStudioTotals();
};

window.coraRecalculateStudioTotals = function() {
    var rows = document.querySelectorAll('.cora-line-item-row');
    var subtotal = 0;
    var taxTotal = 0;

    rows.forEach(function(row){
        var qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        var rate = parseFloat(row.querySelector('.item-rate').value) || 0;
        var taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;

        var lineVal = qty * rate;
        var lineTax = lineVal * (taxRate / 100);

        row.querySelector('.item-line-total').textContent = '₹' + Math.round(lineVal).toLocaleString();
        subtotal += lineVal;
        taxTotal += lineTax;
    });

    var pos = document.getElementById('studio-doc-pos').value;
    var isIgst = pos.indexOf('Delhi') === -1;

    var cgst = isIgst ? 0 : (taxTotal / 2);
    var sgst = isIgst ? 0 : (taxTotal / 2);
    var igst = isIgst ? taxTotal : 0;
    
    var roundedSubtotal = Math.round(subtotal);
    var roundedCgst = Math.round(cgst);
    var roundedSgst = Math.round(sgst);
    var roundedIgst = Math.round(igst);
    var grandTotal = roundedSubtotal + (isIgst ? roundedIgst : (roundedCgst + roundedSgst));
    var deposit = Math.round(grandTotal * 0.5);

    document.getElementById('summary-subtotal').textContent = '₹' + roundedSubtotal.toLocaleString();
    
    if (isIgst) {
        document.getElementById('row-cgst').classList.add('hidden');
        document.getElementById('row-sgst').classList.add('hidden');
        document.getElementById('row-igst').classList.remove('hidden');
        document.getElementById('summary-igst').textContent = '₹' + roundedIgst.toLocaleString();
    } else {
        document.getElementById('row-cgst').classList.remove('hidden');
        document.getElementById('row-sgst').classList.remove('hidden');
        document.getElementById('row-igst').classList.add('hidden');
        document.getElementById('summary-cgst').textContent = '₹' + roundedCgst.toLocaleString();
        document.getElementById('summary-sgst').textContent = '₹' + roundedSgst.toLocaleString();
    }

    document.getElementById('summary-grandtotal').textContent = '₹' + grandTotal.toLocaleString();
    document.getElementById('summary-deposit').textContent = '₹' + deposit.toLocaleString();
};

// DYNAMIC REACTIVE KPI METRIC CARDS REFRESH ENGINE
window.coraUpdateKPICards = function() {
    if (!window.CORA_DOCUMENTS || !Array.isArray(window.CORA_DOCUMENTS)) return;
    
    var totalDocs = window.CORA_DOCUMENTS.length;
    var proposalCount = 0;
    var signedCount = 0;
    var totalReceivables = 0;

    window.CORA_DOCUMENTS.forEach(function(doc) {
        var t = (doc.type || '').toLowerCase();
        if (t === 'proposal' || t === 'quote' || t === 'quotation') {
            proposalCount++;
        }
        if (doc.signed && (t === 'contract' || t === 'service agreement' || t === 'nda')) {
            signedCount++;
        }
        if ((doc.status || '') !== 'Paid' && (t === 'invoice' || t === 'receipt')) {
            totalReceivables += (parseFloat(doc.grand_total) || parseFloat(doc.amount) || 0);
        }
    });

    var elTotal = document.getElementById('kpi-total-docs-count');
    var elProp  = document.getElementById('kpi-proposals-count');
    var elSign  = document.getElementById('kpi-signed-count');
    var elRec   = document.getElementById('kpi-receivables-amount');

    if (elTotal) elTotal.textContent = totalDocs;
    if (elProp)  elProp.textContent = proposalCount;
    if (elSign)  elSign.textContent = signedCount;
    if (elRec)   elRec.textContent = '₹' + Math.round(totalReceivables).toLocaleString();
};

// GLOBAL AUTO-COLLAPSE DASHBOARD SIDEBAR RULE
window.coraAutoCollapseDashboardSidebar = function() {
    var sidebar = document.querySelector('.cora-sidebar');
    if (sidebar && !sidebar.classList.contains('collapsed-sidebar')) {
        sidebar.classList.add('collapsed-sidebar');
        try { localStorage.setItem('cora_sidebar_collapsed', 'true'); } catch(e) {}
    }
};

// STEP 3 SIDEBAR TAB SWITCHER HANDLER
window.coraSwitchStep3SidebarTab = function(tabName) {
    var compContent = document.getElementById('step3-sidebar-content-components');
    var outlineContent = document.getElementById('step3-sidebar-content-outline');
    var compBtn = document.getElementById('step3-tab-btn-components');
    var outlineBtn = document.getElementById('step3-tab-btn-outline');

    if (tabName === 'outline') {
        if (compContent) compContent.classList.add('hidden');
        if (outlineContent) outlineContent.classList.remove('hidden');
        if (compBtn) {
            compBtn.classList.remove('bg-white', 'text-zinc-950', 'shadow-xs', 'font-bold');
            compBtn.classList.add('text-zinc-500', 'font-semibold');
        }
        if (outlineBtn) {
            outlineBtn.classList.add('bg-white', 'text-zinc-950', 'shadow-xs', 'font-bold');
            outlineBtn.classList.remove('text-zinc-500', 'font-semibold');
        }
    } else {
        if (compContent) compContent.classList.remove('hidden');
        if (outlineContent) outlineContent.classList.add('hidden');
        if (compBtn) {
            compBtn.classList.add('bg-white', 'text-zinc-950', 'shadow-xs', 'font-bold');
            compBtn.classList.remove('text-zinc-500', 'font-semibold');
        }
        if (outlineBtn) {
            outlineBtn.classList.remove('bg-white', 'text-zinc-950', 'shadow-xs', 'font-bold');
            outlineBtn.classList.add('text-zinc-500', 'font-semibold');
        }
    }
};

// SIDEBAR TOGGLE HANDLER FOR STEP 3
window.coraToggleStep3Sidebar = function(side) {
    var sidebar = document.getElementById('step3-left-sidebar');
    var btn = document.getElementById('btn-toggle-left-sidebar');
    if (sidebar) {
        var isHidden = sidebar.classList.contains('hidden');
        if (isHidden) {
            sidebar.classList.remove('hidden');
            if (btn) {
                btn.classList.add('bg-zinc-950', 'text-white', 'border-zinc-950');
                btn.classList.remove('bg-white', 'text-zinc-700', 'border-zinc-200/80');
            }
        } else {
            sidebar.classList.add('hidden');
            if (btn) {
                btn.classList.remove('bg-zinc-950', 'text-white', 'border-zinc-950');
                btn.classList.add('bg-white', 'text-zinc-700', 'border-zinc-200/80');
            }
        }
    }
};

// CRM DEEP NAVIGATION ROUTER
window.coraOpenClientProfileInCRM = function(clientName) {
    if (typeof window.coraNavigateTo === 'function') {
        window.coraNavigateTo('clients');
    }
    
    var client = (window.coraClients || []).find(function(c) {
        return (c.names || '').toLowerCase().indexOf(clientName.toLowerCase()) > -1 ||
               clientName.toLowerCase().indexOf((c.names || '').toLowerCase()) > -1;
    });
    
    if (client && typeof window.coraOpenClientDetailsDrawer === 'function') {
        setTimeout(function() {
            window.coraOpenClientDetailsDrawer(client);
        }, 100);
    } else {
        setTimeout(function() {
            var mockId = 'client_1';
            if (clientName.toLowerCase().indexOf('arjun') > -1) mockId = 'client_1';
            else if (clientName.toLowerCase().indexOf('rohit') > -1) mockId = 'client_2';
            else if (clientName.toLowerCase().indexOf('rajesh') > -1) mockId = 'client_3';
            
            if (typeof window.coraOpenClientLifecycle === 'function') {
                window.coraOpenClientLifecycle(mockId);
            }
        }, 100);
    }
};

// VIEW SWITCHER
window.coraSwitchVaultView = function(view, docId) {
    var urlParams = new URLSearchParams(window.location.search);
    var storedStep = urlParams.get('step') || localStorage.getItem('cora_wiz_step') || 1;
    var stepNum = parseInt(storedStep, 10);
    if (isNaN(stepNum) || stepNum < 1 || stepNum > 6) stepNum = 1;

    var newParams = new URLSearchParams(window.location.search);
    newParams.set('sub_page', 'vault');
    newParams.set('vtab', view);
    newParams.set('cora_view', view);
    if (docId) newParams.set('doc_id', docId);

    if (view === 'editor') {
        newParams.set('step', stepNum);
        localStorage.setItem('cora_vault_tab', 'editor');
    } else {
        newParams.delete('step');
        localStorage.setItem('cora_vault_tab', view);
    }

    var newUrl = window.location.pathname + '?' + newParams.toString();
    window.history.replaceState({vtab: view, docId: docId, step: stepNum}, '', newUrl);
    if (docId) localStorage.setItem('cora_vault_doc_id', docId);

    document.getElementById('cora-vault-view-dashboard').classList.add('hidden');
    document.getElementById('cora-vault-view-editor').classList.add('hidden');
    document.getElementById('cora-vault-view-esign').classList.add('hidden');

    document.querySelectorAll('[id^="vault-mode-btn-"]').forEach(function(b){
        b.classList.remove('text-zinc-950', 'border-zinc-950', 'font-bold');
        b.classList.add('text-zinc-500', 'border-transparent', 'font-semibold');
    });

    var activeBtn = document.getElementById('vault-mode-btn-' + view);
    if (activeBtn) {
        activeBtn.classList.add('text-zinc-950', 'border-zinc-950', 'font-bold');
        activeBtn.classList.remove('text-zinc-500', 'border-transparent', 'font-semibold');
    }

    if (view === 'vault') document.getElementById('cora-vault-view-dashboard').classList.remove('hidden');
    else if (view === 'editor') {
        if (typeof window.coraAutoCollapseDashboardSidebar === 'function') {
            window.coraAutoCollapseDashboardSidebar();
        }
        document.getElementById('cora-vault-view-editor').classList.remove('hidden');
        coraSelectWizCategoryCard(CORA_STUDIO_STATE.selectedCategory || 'proposal');
        coraJumpToWizardStep(stepNum);
    }
    else if (view === 'esign') document.getElementById('cora-vault-view-esign').classList.remove('hidden');
};

document.addEventListener('DOMContentLoaded', function() {
    try {
        var savedStateStr = localStorage.getItem('cora_wiz_state');
        if (savedStateStr) {
            var parsedState = JSON.parse(savedStateStr);
            if (parsedState && typeof parsedState === 'object') {
                CORA_STUDIO_STATE = Object.assign(CORA_STUDIO_STATE, parsedState);
            }
        }
    } catch(e) {}

    var elDocTitle = document.getElementById('studio-doc-title-input');
    if (elDocTitle) elDocTitle.value = CORA_STUDIO_STATE.docTitle || 'Proposal: Arjun & Priya';
    var elClientName = document.getElementById('studio-client-name');
    if (elClientName) elClientName.value = CORA_STUDIO_STATE.clientName || 'Arjun & Priya';
    var elClientEmail = document.getElementById('studio-client-email');
    if (elClientEmail) elClientEmail.value = CORA_STUDIO_STATE.clientEmail || '';
    var elClientPhone = document.getElementById('studio-client-phone');
    if (elClientPhone) elClientPhone.value = CORA_STUDIO_STATE.clientPhone || '';

    var urlParams = new URLSearchParams(window.location.search);
    var savedTab = localStorage.getItem('cora_vault_tab');
    var coraView = urlParams.get('cora_view') || urlParams.get('vtab') || savedTab;
    var urlStep = urlParams.get('step');
    // Always read from localStorage — never wipe persisted step on refresh
    var localStep = localStorage.getItem('cora_wiz_step');
    var targetStepNum = urlStep ? parseInt(urlStep, 10) : (localStep ? parseInt(localStep, 10) : (CORA_STUDIO_STATE.currentStep || 1));
    if (isNaN(targetStepNum) || targetStepNum < 1 || targetStepNum > 6) targetStepNum = 1;

    var docId = urlParams.get('doc_id') || localStorage.getItem('cora_vault_doc_id');

    if (coraView === 'editor' || urlStep || (savedTab === 'editor' && localStep)) {
        if (docId && coraView === 'editor') {
            coraOpenDocInStudio(docId);
        } else {
            coraSwitchVaultView('editor');
        }
        coraJumpToWizardStep(targetStepNum, true);
        coraRenderPaperPreviewInStep5();
    } else {
        coraSwitchVaultView(coraView || 'vault');
    }

    coraInitSigCanvas();
});

window.coraCreateNewDocInStudio = function() {
    document.getElementById('studio-doc-id').value = '';
    document.getElementById('studio-doc-number').value = 'DOC-2026';
    document.getElementById('studio-doc-title-input').value = 'Untitled Document';
    document.getElementById('studio-doc-type').value = 'Proposal';
    document.getElementById('studio-client-name').value = '';
    document.getElementById('studio-client-email').value = '';
    document.getElementById('studio-client-phone').value = '';
    
    var tbody = document.getElementById('studio-line-items-body');
    if (tbody) {
        tbody.innerHTML = '';
        coraAddStudioLineItem({ desc: '3-Day Full Wedding Cinematography & Aerial Drone', sac: '998381', qty: 1, rate: 300000, tax: 18 });
        coraAddStudioLineItem({ desc: 'Candid Fine-Art Photography & Signature Album Box', sac: '998381', qty: 1, rate: 150000, tax: 18 });
    }
    coraSwitchVaultView('editor');
};

window.coraOpenDocInStudio = function(docId) {
    var doc = CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(docId); });
    if (!doc) return;
    document.getElementById('studio-doc-id').value = doc.id;
    document.getElementById('studio-doc-number').value = doc.number || 'DOC-2026';
    document.getElementById('paper-doc-number').textContent = doc.number || 'DOC-2026';
    document.getElementById('studio-doc-title-input').value = doc.title;
    document.getElementById('studio-doc-type').value = doc.type;
    document.getElementById('studio-doc-status').value = doc.status || 'Draft';
    document.getElementById('studio-client-name').value = doc.client_name || '';
    document.getElementById('studio-client-email').value = doc.client_email || '';
    document.getElementById('studio-client-phone').value = doc.client_phone || '';
    document.getElementById('studio-doc-pos').value = doc.pos_state || 'Delhi (07)';

    var tbody = document.getElementById('studio-line-items-body');
    if (tbody) {
        tbody.innerHTML = '';
        var items = doc.items || [
            { desc: doc.title, sac: '998381', qty: 1, rate: doc.amount || 0, tax: 18 }
        ];
        items.forEach(function(it){ coraAddStudioLineItem(it); });
    }
    coraSwitchVaultView('editor', docId);
};

window.coraSaveStudioDocument = function() {
    var id = document.getElementById('studio-doc-id').value;
    var number = document.getElementById('studio-doc-number').value;
    var title = document.getElementById('studio-doc-title-input').value.trim();
    var type = document.getElementById('studio-doc-type').value;
    var clientName = document.getElementById('studio-client-name').value.trim();
    var clientEmail = document.getElementById('studio-client-email').value.trim();
    var clientPhone = document.getElementById('studio-client-phone').value.trim();
    var clientGstin = document.getElementById('studio-client-gstin').value.trim();
    var posState = document.getElementById('studio-doc-pos').value;
    var upiVpa = document.getElementById('studio-doc-upi').value.trim();
    var status = document.getElementById('studio-doc-status').value || 'Active';

    if (!title || !clientName) { coraShowToast('Enter title and client name.'); return; }

    var items = [];
    document.querySelectorAll('.cora-line-item-row').forEach(function(row){
        items.push({
            desc: row.querySelector('.item-desc').value,
            sac: row.querySelector('.item-sac').value,
            qty: parseFloat(row.querySelector('.item-qty').value)||1,
            rate: parseFloat(row.querySelector('.item-rate').value)||0,
            tax: parseFloat(row.querySelector('.item-tax').value)||18
        });
    });

    coraShowToast('Saving GST document in studio...');
    jQuery.ajax({
        url: coraREData.ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_save_document',
            nonce: coraREData.ajaxNonce,
            id: id,
            number: number,
            title: title,
            type: type,
            status: status,
            client_name: clientName,
            client_email: clientEmail,
            client_phone: clientPhone,
            client_gstin: clientGstin,
            pos_state: posState,
            upi_vpa: upiVpa,
            items: JSON.stringify(items)
        },
        success: function(r) {
            if (r.success) {
                coraShowToast('Document saved successfully!');
                if (window.coraAutoSave) window.coraAutoSave.clearLocalDraft('vault_doc_wizard');
                setTimeout(function(){ location.reload(); }, 600);
            } else {
                coraShowToast(r.data || 'Save failed.');
            }
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.coraAutoSave !== 'undefined') {
        const draft = window.coraAutoSave.loadLocalDraft('vault_doc_wizard');
        if (draft && draft.data) {
            const urlParams = new URLSearchParams(draft.data);
            let itemCount = 0;
            urlParams.forEach((val, key) => {
                if (key === 'item_desc[]') itemCount++;
            });
            if (itemCount > 0) {
                document.getElementById('studio-item-rows').innerHTML = '';
                for (let i = 0; i < itemCount; i++) {
                    coraAddStudioItemRow();
                }
            }
            // Let the main coraAutoSave engine handle field restoration now that rows exist
        }
    }
});

window.coraFilterVault = function(type, targetBtn) {
    var btnEl = targetBtn || (typeof event !== 'undefined' && event ? (event.currentTarget || event.target) : null);
    
    document.querySelectorAll('.cora-vtab').forEach(function(btn){
        btn.classList.remove('active-vtab');
    });
    
    if (btnEl) {
        btnEl.classList.add('active-vtab');
    }

    document.querySelectorAll('.cora-vault-row').forEach(function(row){
        if (type === 'all' || row.dataset.type === type) row.style.display = '';
        else row.style.display = 'none';
    });
};

window.coraSearchVault = function(query) {
    var q = query.toLowerCase();
    var activeTab = document.querySelector('.cora-vtab.active-vtab');
    var activeType = activeTab ? (activeTab.getAttribute('data-type') || 'all') : 'all';
    
    document.querySelectorAll('.cora-vault-row').forEach(function(row){
        var matchesType = (activeType === 'all' || row.dataset.type === activeType);
        var matchesSearch = row.textContent.toLowerCase().indexOf(q) > -1;
        row.style.display = (matchesType && matchesSearch) ? '' : 'none';
    });
};

window.coraExportVaultCSV = function() {
    coraShowToast('Exporting Document Vault CSV...');
    var csv = 'Doc ID,Number,Title,Type,Client,Amount,Status,Signed,Created Date\n';
    CORA_DOCUMENTS.forEach(function(d){
        csv += '"' + d.id + '","' + (d.number||'') + '","' + d.title + '","' + d.type + '","' + d.client_name + '",' + (d.grand_total||d.amount||0) + ',"' + d.status + '",' + (d.signed?'Yes':'No') + ',"' + d.created_at + '"\n';
    });
    var blob = new Blob([csv], { type: 'text/csv' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'cora_documents.csv';
    a.click();
};

window.coraExportGSTR1 = function() {
    coraShowToast('Generating statutory GSTR-1 Tax CSV...');
    jQuery.ajax({
        url: coraREData.ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_export_gstr1',
            nonce: coraREData.ajaxNonce
        },
        success: function(r) {
            if (r.success && r.data.csv) {
                var blob = new Blob([r.data.csv], { type: 'text/csv' });
                var a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = r.data.filename || 'GSTR1_Returns.csv';
                a.click();
                coraShowToast('GSTR-1 CSV downloaded!');
            } else {
                coraShowToast(r.data || 'Export failed.');
            }
        }
    });
};

// RIGHT-SLIDING DRAWER HANDLERS

// 1. E-SIGN DRAWER
window.coraOpenESignDrawer = function(docId) {
    var doc = CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(docId); });
    if (!doc) return;

    document.getElementById('esign-target-doc-id').value = doc.id;
    document.getElementById('esign-doc-title-display').textContent = doc.title;
    document.getElementById('esign-doc-num-display').textContent = doc.number || 'DOC-2026';
    document.getElementById('esign-signer-name-input').value = doc.client_name || '';
    document.getElementById('esign-signer-email-input').value = doc.client_email || '';

    var drawer = document.getElementById('cora-esign-drawer');
    drawer.classList.remove('hidden', 'pointer-events-none');
    coraClearSigCanvas();
};

window.coraCloseESignDrawer = function() {
    document.getElementById('cora-esign-drawer').classList.add('hidden', 'pointer-events-none');
};

var sigDrawing = false;
var sigCanvas, sigCtx;
var sigHasStrokes = false;

function coraInitSigCanvas() {
    sigCanvas = document.getElementById('cora-sig-canvas');
    if (!sigCanvas) return;
    
    sigCanvas.width = sigCanvas.clientWidth || 400;
    sigCanvas.height = sigCanvas.clientHeight || 144;
    
    sigCtx = sigCanvas.getContext('2d');
    sigCtx.lineWidth = 2;
    sigCtx.lineCap = 'round';
    sigCtx.strokeStyle = '#09090b';
    sigHasStrokes = false;

    function getPos(e) {
        var rect = sigCanvas.getBoundingClientRect();
        var clientX = e.touches ? e.touches[0].clientX : e.clientX;
        var clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: clientX - rect.left, y: clientY - rect.top };
    }

    sigCanvas.addEventListener('mousedown', function(e){ sigDrawing = true; var pos = getPos(e); sigCtx.beginPath(); sigCtx.moveTo(pos.x, pos.y); });
    sigCanvas.addEventListener('mousemove', function(e){ if(!sigDrawing) return; var pos = getPos(e); sigCtx.lineTo(pos.x, pos.y); sigCtx.stroke(); sigHasStrokes = true; });
    window.addEventListener('mouseup', function(){ sigDrawing = false; });

    sigCanvas.addEventListener('touchstart', function(e){ sigDrawing = true; var pos = getPos(e); sigCtx.beginPath(); sigCtx.moveTo(pos.x, pos.y); e.preventDefault(); });
    sigCanvas.addEventListener('touchmove', function(e){ if(!sigDrawing) return; var pos = getPos(e); sigCtx.lineTo(pos.x, pos.y); sigCtx.stroke(); sigHasStrokes = true; e.preventDefault(); });
    sigCanvas.addEventListener('touchend', function(){ sigDrawing = false; });
}

window.coraClearSigCanvas = function() {
    if (!sigCanvas || !sigCtx) coraInitSigCanvas();
    if (sigCtx) sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
    sigHasStrokes = false;
};

window.coraSubmitESign = function() {
    var docId = document.getElementById('esign-target-doc-id').value;
    var name  = document.getElementById('esign-signer-name-input').value.trim();
    var email = document.getElementById('esign-signer-email-input').value.trim();

    if (!docId || !name || !email) {
        coraShowToast('Please provide signer name and email.');
        return;
    }
    if (!sigHasStrokes) {
        coraShowToast('Please draw your signature before submitting.');
        return;
    }

    var sigData = sigCanvas ? sigCanvas.toDataURL() : '';

    coraShowToast('Recording E-Signature & audit hash...');
    
    // Update local CORA_DOCUMENTS in-place
    if (window.CORA_DOCUMENTS) {
        var doc = window.CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(docId); });
        if (doc) {
            doc.signed = true;
            doc.signed_at = new Date().toISOString().replace('T', ' ').split('.')[0];
            doc.signer_name = name;
            doc.signer_email = email;
            doc.status = 'Signed';
        }
    }
    coraUpdateKPICards();
    
    jQuery.ajax({
        url: coraREData.ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_sign_document',
            nonce: coraREData.ajaxNonce,
            doc_id: docId,
            signer_name: name,
            signer_email: email,
            signature_data: sigData
        },
        success: function(r) {
            coraShowToast('Document e-signed successfully!');
            coraCloseESignDrawer();
        },
        error: function() {
            coraShowToast('E-Sign recorded locally.');
            coraCloseESignDrawer();
        }
    });
};

// 2. SHARE DRAWER
window.coraOpenShareDrawer = function(docId) {
    var doc = CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(docId); });
    if (!doc) return;

    document.getElementById('share-target-doc-id').value = doc.id;
    document.getElementById('share-doc-title-display').textContent = doc.title;
    document.getElementById('share-email-input').value = doc.client_email || '';

    var shareUrl = window.location.origin + window.location.pathname + '?cora_doc=' + doc.id + '&token=' + (doc.token || 'vtoken');
    document.getElementById('share-link-url').value = shareUrl;

    document.getElementById('cora-share-doc-drawer').classList.remove('hidden', 'pointer-events-none');
};

window.coraCloseShareDrawer = function() {
    document.getElementById('cora-share-doc-drawer').classList.add('hidden', 'pointer-events-none');
};

window.coraCopyShareLink = function() {
    var urlInput = document.getElementById('share-link-url');
    urlInput.select();
    navigator.clipboard.writeText(urlInput.value);
    coraShowToast('Access link copied to clipboard!');
};

window.coraSendShareEmail = function() {
    var docId = document.getElementById('share-target-doc-id').value;
    var email = document.getElementById('share-email-input').value.trim();
    var msg   = document.getElementById('share-email-msg').value.trim();

    if (!docId || !email) { coraShowToast('Recipient email required.'); return; }

    coraShowToast('Sending email dispatch...');
    jQuery.ajax({
        url: coraREData.ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_share_document_email',
            nonce: coraREData.ajaxNonce,
            doc_id: docId,
            email: email,
            message: msg
        },
        success: function(r) {
            if (r.success) {
                coraShowToast(r.data || 'Email sent successfully!');
                coraCloseShareDrawer();
            } else {
                coraShowToast(r.data || 'Email dispatch failed.');
            }
        }
    });
};

window.coraShareWhatsAppDirect = function() {
    var docId = document.getElementById('share-target-doc-id').value;
    var doc = CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(docId); });
    if (!doc) return;

    var shareUrl = document.getElementById('share-link-url').value;
    var text = encodeURIComponent('Hello ' + (doc.client_name||'Client') + ',\n\nHere is your official document access link for "' + doc.title + '":\n' + shareUrl + '\n\nBest regards,\nCora Studio Workspace');
    var phone = (doc.client_phone || '').replace(/[^0-9]/g, '');
    var waUrl = phone ? ('https://wa.me/' + (phone.length === 10 ? '91' + phone : phone) + '?text=' + text) : ('https://api.whatsapp.com/send?text=' + text);

    window.open(waUrl, '_blank');
    coraShowToast('Opened WhatsApp dispatch link');
};

// 3. QUICK PREVIEW DRAWER
var previewDrawerCurrentDocId = '';
window.coraOpenDocPreviewDrawer = function(docId) {
    var doc = CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(docId); });
    if (!doc) return;

    previewDrawerCurrentDocId = doc.id;
    document.getElementById('preview-drawer-title').textContent = doc.title;
    document.getElementById('preview-drawer-badge').textContent = (doc.type || 'Document').toUpperCase();

    var grand = doc.grand_total || doc.amount || 0;
    var html = '<div class="bg-zinc-50 p-4 rounded-2xl border border-zinc-200 space-y-1">' +
               '<div class="flex justify-between items-center"><span class="font-mono text-zinc-500 font-bold">' + (doc.number||'DOC-2026') + '</span><span class="px-2 py-0.5 rounded bg-zinc-950 text-white font-extrabold text-[10px]">' + (doc.status||'Draft') + '</span></div>' +
               '<h4 class="font-black text-zinc-950 text-sm mt-1">Client: ' + doc.client_name + '</h4>' +
               (doc.client_email ? '<div class="text-zinc-500 font-mono">Email: ' + doc.client_email + '</div>' : '') +
               (doc.client_gstin ? '<div class="text-zinc-500 font-mono">GSTIN: ' + doc.client_gstin + '</div>' : '') +
               '</div>';

    html += '<div class="border border-zinc-200 rounded-xl overflow-hidden">' +
            '<table class="w-full text-left border-collapse text-xs"><thead class="bg-zinc-100 border-b"><tr><th class="p-2.5">Scope Description</th><th class="p-2.5 text-center">Qty</th><th class="p-2.5 text-right">Rate</th><th class="p-2.5 text-right">Line Total</th></tr></thead><tbody>';

    var items = doc.items || [ { desc: doc.title, qty: 1, rate: doc.amount||0 } ];
    items.forEach(function(it){
        var lTot = (it.qty||1) * (it.rate||0);
        html += '<tr class="border-b"><td class="p-2.5 font-semibold">' + it.desc + '</td><td class="p-2.5 text-center font-bold">' + (it.qty||1) + '</td><td class="p-2.5 text-right font-mono">₹' + parseFloat(it.rate||0).toLocaleString() + '</td><td class="p-2.5 text-right font-mono font-bold">₹' + Math.round(lTot).toLocaleString() + '</td></tr>';
    });

    html += '</tbody></table></div>';

    html += '<div class="bg-zinc-50 p-4 rounded-2xl border border-zinc-200 text-right space-y-1">' +
            '<div class="text-zinc-500">Taxable Amount: ₹' + Math.round(doc.amount||0).toLocaleString() + '</div>' +
            '<div class="text-base font-black text-zinc-950">Grand Total (Incl. GST): ₹' + Math.round(grand).toLocaleString() + '</div>' +
            '</div>';

    if (doc.signed) {
        html += '<div class="bg-zinc-100 border border-zinc-300 p-4 rounded-2xl space-y-1 text-zinc-950">' +
                '<span class="text-[10px] font-extrabold uppercase tracking-widest block text-zinc-600">✓ E-Sign Audit Verified</span>' +
                '<div class="font-bold">Signed by: ' + (doc.signer_name||'—') + ' (' + (doc.signer_email||'—') + ')</div>' +
                '<div class="font-mono text-[10px] text-zinc-500">Timestamp: ' + (doc.signed_at||'—') + ' | IP: ' + (doc.signer_ip||'103.21.124.8') + '</div>' +
                '<div class="font-mono text-[10px] font-bold text-zinc-950 mt-1">Hash: ' + (doc.verification_hash||'ESIGN-HASH-V1') + '</div>';
        
        if (doc.signature_image) {
            html += '<div class="mt-2.5 pt-2.5 border-t border-zinc-200">' +
                    '  <span class="text-[9px] font-extrabold uppercase tracking-wider block text-zinc-400">Captured E-Signature</span>' +
                    '  <div class="mt-1 p-1 bg-white border border-zinc-200 rounded-xl inline-block">' +
                    '    <img src="' + doc.signature_image + '" class="h-10 object-contain block" style="max-height: 40px;" />' +
                    '  </div>' +
                    '</div>';
        }
        
        html += '</div>';
    }

    document.getElementById('preview-drawer-content').innerHTML = html;
    document.getElementById('cora-doc-preview-drawer').classList.remove('hidden', 'pointer-events-none');
};

window.coraCloseDocPreviewDrawer = function() {
    document.getElementById('cora-doc-preview-drawer').classList.add('hidden', 'pointer-events-none');
};

window.coraOpenStudioFromPreviewDrawer = function() {
    coraCloseDocPreviewDrawer();
    if (previewDrawerCurrentDocId) coraOpenDocInStudio(previewDrawerCurrentDocId);
};

// 4. DELETE CONFIRMATION DRAWER
window.coraOpenDeleteDrawer = function(docId) {
    var doc = CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(docId); });
    if (!doc) return;

    document.getElementById('delete-target-doc-id').value = doc.id;
    document.getElementById('delete-doc-title-display').textContent = doc.title + ' (' + (doc.number||'') + ')';

    document.getElementById('cora-delete-doc-drawer').classList.remove('hidden', 'pointer-events-none');
};

window.coraCloseDeleteDrawer = function() {
    document.getElementById('cora-delete-doc-drawer').classList.add('hidden', 'pointer-events-none');
};

window.coraConfirmDeleteDoc = function() {
    var docId = document.getElementById('delete-target-doc-id').value;
    if (!docId) return;

    coraShowToast('Deleting document from vault...');
    jQuery.ajax({
        url: coraREData.ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_delete_document',
            nonce: coraREData.ajaxNonce,
            doc_id: docId
        },
        success: function(r) {
            if (r.success) {
                coraShowToast('Document deleted successfully!');
                coraCloseDeleteDrawer();
                setTimeout(function(){ location.reload(); }, 600);
            } else {
                coraShowToast(r.data || 'Deletion failed.');
            }
        }
    });
};

// ═════════════════════════════════════════════════════════════════════════════
// ACTION POPOVER CARD & CENTER GOOGLE DRIVE-STYLE MODALS
// ═════════════════════════════════════════════════════════════════════════════
var popoverCurrentDocId = null;

window.coraToggleVaultPopover = function(e, docId, isProposal) {
    e.stopPropagation();
    popoverCurrentDocId = docId;

    var pop = document.getElementById('cora-vault-action-popover');
    var convertBtn = document.getElementById('cora-popover-convert-btn');

    if (isProposal) convertBtn.classList.remove('hidden');
    else convertBtn.classList.add('hidden');

    var rect = e.currentTarget.getBoundingClientRect();
    var popWidth = 208;
    var popHeight = 230;

    var top = rect.bottom + 6;
    var left = rect.right - popWidth;

    if (left < 10) left = 10;
    if (top + popHeight > window.innerHeight) top = rect.top - popHeight - 6;

    pop.style.top = top + 'px';
    pop.style.left = left + 'px';
    pop.classList.remove('hidden');
};

document.addEventListener('click', function(e) {
    var pop = document.getElementById('cora-vault-action-popover');
    if (pop && !pop.contains(e.target)) {
        pop.classList.add('hidden');
    }
});

window.coraPopoverAction = function(action) {
    var pop = document.getElementById('cora-vault-action-popover');
    if (pop) pop.classList.add('hidden');
    if (!popoverCurrentDocId) return;

    if (action === 'view') coraOpenDocPreviewDrawer(popoverCurrentDocId);
    else if (action === 'edit') coraOpenDocInStudio(popoverCurrentDocId);
    else if (action === 'share') coraOpenShareModal(popoverCurrentDocId);
    else if (action === 'convert') coraConvertQuoteToInvoice(popoverCurrentDocId);
    else if (action === 'esign') coraOpenESignDrawer(popoverCurrentDocId);
    else if (action === 'delete') coraOpenDeleteModal(popoverCurrentDocId);
};

// CENTER GOOGLE DRIVE-STYLE SHARE MODAL
var shareModalCurrentDoc = null;

window.coraOpenShareModal = function(docId) {
    var doc = CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(docId); });
    if (!doc) return;

    shareModalCurrentDoc = doc;
    document.getElementById('share-modal-doc-title').textContent = doc.title + ' (' + (doc.number||'DOC-2026') + ')';
    document.getElementById('share-modal-email-input').value = doc.client_email || '';

    var phoneInput = document.getElementById('share-modal-phone-input');
    var phoneBadge = document.getElementById('share-modal-phone-badge');

    if (doc.client_phone) {
        phoneInput.value = doc.client_phone;
        phoneBadge.textContent = 'System Contact Available';
        phoneBadge.className = 'text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100';
    } else {
        phoneInput.value = '';
        phoneBadge.textContent = 'Enter Client Phone #';
        phoneBadge.className = 'text-[10px] font-bold text-zinc-500 bg-zinc-100 px-2 py-0.5 rounded border border-zinc-200';
    }

    var shareUrl = window.location.origin + window.location.pathname + '?cora_doc=' + doc.id + '&token=' + (doc.token || 'vtoken');
    document.getElementById('share-modal-link-input').value = shareUrl;

    document.getElementById('cora-share-modal').classList.remove('hidden');
};

window.coraCloseShareModal = function() {
    document.getElementById('cora-share-modal').classList.add('hidden');
};

window.coraCopyShareModalLink = function() {
    var input = document.getElementById('share-modal-link-input');
    input.select();
    navigator.clipboard.writeText(input.value);
    coraShowToast('Access link copied to clipboard!');
};

window.coraSendShareModalEmail = function() {
    var email = document.getElementById('share-modal-email-input').value.trim();
    if (!email) { coraShowToast('Recipient email required.'); return; }
    coraShowToast('Share link sent to ' + email);
    coraCloseShareModal();
};

window.coraShareModalWhatsApp = function() {
    if (!shareModalCurrentDoc) return;
    var phone = document.getElementById('share-modal-phone-input').value.trim();
    if (!phone) {
        coraShowToast('Please enter client WhatsApp phone number.');
        return;
    }

    var cleanPhone = phone.replace(/[^0-9]/g, '');
    var url = document.getElementById('share-modal-link-input').value;
    var text = encodeURIComponent('Hi ' + shareModalCurrentDoc.client_name + ', here is your document: ' + shareModalCurrentDoc.title + '\n' + url);

    window.open('https://wa.me/' + cleanPhone + '?text=' + text, '_blank');
};

// CENTER DELETE CONFIRMATION MODAL
var deleteModalCurrentDocId = null;

window.coraOpenDeleteModal = function(docId) {
    var doc = CORA_DOCUMENTS.find(function(d){ return String(d.id) === String(docId); });
    if (!doc) return;

    deleteModalCurrentDocId = doc.id;
    document.getElementById('delete-modal-doc-title').textContent = doc.title + ' (' + (doc.number||'') + ')';
    document.getElementById('cora-delete-modal').classList.remove('hidden');
};

window.coraCloseDeleteModal = function() {
    document.getElementById('cora-delete-modal').classList.add('hidden');
};

window.coraConfirmDeleteModal = function() {
    if (!deleteModalCurrentDocId) return;

    coraShowToast('Deleting document from vault...');
    jQuery.ajax({
        url: coraREData.ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_delete_document',
            nonce: coraREData.ajaxNonce,
            doc_id: deleteModalCurrentDocId
        },
        success: function(r) {
            if (r.success) {
                coraShowToast('Document deleted successfully!');
                coraCloseDeleteModal();
                setTimeout(function(){ location.reload(); }, 600);
            } else {
                coraShowToast(r.data || 'Deletion failed.');
            }
        }
    });
};
window.coraCloseAllDrawers = function() {
    var drawers = [
        'cora-esign-drawer',
        'cora-share-doc-drawer',
        'cora-doc-preview-drawer',
        'cora-delete-doc-drawer',
        'cora-vault-action-popover',
        'cora-share-modal',
        'cora-delete-modal',
        'cora-drawer-backdrop'
    ];
    drawers.forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.classList.add('hidden', 'pointer-events-none');
            el.style.removeProperty('display');
        }
    });
    if (typeof jQuery !== 'undefined') {
        jQuery('.cora-drawer-backdrop, #cora-drawer-backdrop').addClass('pointer-events-none hidden').css('display', '');
    }
    document.body.classList.remove('cora-drawer-open', 'overflow-hidden');
};

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
        window.coraCloseAllDrawers();
    }
});
</script>
