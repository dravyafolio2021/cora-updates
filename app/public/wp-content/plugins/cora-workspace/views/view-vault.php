<?php
/**
 * Cora Workspace - Enterprise Document Studio & Vault
 * File: views/view-vault.php
 * 5-Step Visual Guided Sub-Page Wizard with Sticky Bottom Action Bar & Visual Paper Cards
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Fetch documents from WP options or fallback to default sample documents
$cora_documents = get_option( 'cora_documents', array() );

if ( empty( $cora_documents ) || ! is_array( $cora_documents ) ) {
    $cora_documents = array(
        array(
            'id'             => 'doc_101',
            'number'         => 'PROP-2026-0101',
            'title'          => 'Proposal: Arjun & Priya Wedding Coverage',
            'type'           => 'Proposal',
            'client_name'    => 'Arjun & Priya',
            'client_email'   => 'arjun.priya@example.com',
            'client_phone'   => '9876543210',
            'client_gstin'   => '07AAAAA0000A1Z5',
            'client_address' => 'Vasant Vihar, New Delhi - 110057',
            'pos_state'      => 'Delhi (07)',
            'is_igst'        => false,
            'amount'         => 450000,
            'tax_amount'     => 81000,
            'cgst_amount'    => 40500,
            'sgst_amount'    => 40500,
            'igst_amount'    => 0,
            'grand_total'    => 531000,
            'deposit'        => 265500,
            'currency'       => 'INR',
            'upi_vpa'        => 'cora@icici',
            'status'         => 'Sent',
            'watermark'      => 'CONFIDENTIAL',
            'password'       => '1234',
            'terms'          => 'Net 15 Days',
            'created_at'     => '2026-06-15',
            'due_date'       => '2026-06-30',
            'token'          => 'vtoken_a1982b',
            'signed'         => false,
            'content'        => 'Exclusive 3-day wedding photography agreement including drone coverage and candid albums.',
            'items'          => array(
                array( 'desc' => '3-Day Full Wedding Cinematography & Aerial Drone', 'sac' => '998381', 'qty' => 1, 'rate' => 300000, 'tax' => 18 ),
                array( 'desc' => 'Candid Fine-Art Photography & Signature Album Box', 'sac' => '998381', 'qty' => 1, 'rate' => 150000, 'tax' => 18 )
            )
        ),
        array(
            'id'             => 'doc_102',
            'number'         => 'INV-2026-0042',
            'title'          => 'Invoice: Apex Realty Commercial Lease',
            'type'           => 'Invoice',
            'client_name'    => 'Apex Realty Group',
            'client_email'   => 'finance@apexrealty.com',
            'client_phone'   => '9811223344',
            'client_gstin'   => '06BBBBA1111B1Z2',
            'client_address' => 'Cyber City, Gurugram - 122002',
            'pos_state'      => 'Haryana (06)',
            'is_igst'        => true,
            'amount'         => 180000,
            'tax_amount'     => 32400,
            'cgst_amount'    => 0,
            'sgst_amount'    => 0,
            'igst_amount'    => 32400,
            'grand_total'    => 212400,
            'deposit'        => 212400,
            'currency'       => 'INR',
            'upi_vpa'        => 'cora@icici',
            'status'         => 'Paid',
            'watermark'      => 'PAID',
            'password'       => '',
            'terms'          => 'Due on Receipt',
            'created_at'     => '2026-06-10',
            'due_date'       => '2026-06-10',
            'token'          => 'vtoken_c4412e',
            'signed'         => true,
            'signed_at'      => '2026-06-12 14:30:15',
            'signer_name'    => 'Rajesh Sharma',
            'signer_email'   => 'finance@apexrealty.com',
            'signer_ip'      => '103.21.124.8',
            'content'        => 'Official tax settlement invoice for Apex Realty Group commercial representation.',
            'items'          => array(
                array( 'desc' => 'Commercial Property Lease Brokerage Fee', 'sac' => '997212', 'qty' => 1, 'rate' => 180000, 'tax' => 18 )
            )
        )
    );
    update_option( 'cora_documents', $cora_documents );
}

// Summary stats
$total_docs = count( $cora_documents );
$proposal_count = 0;
$invoice_count  = 0;
$signed_count   = 0;
$total_receivables = 0;

foreach ( $cora_documents as $doc ) {
    $t = strtolower( $doc['type'] ?? '' );
    if ( $t === 'proposal' ) $proposal_count++;
    elseif ( $t === 'invoice' ) $invoice_count++;
    
    if ( ! empty( $doc['signed'] ) ) $signed_count++;

    if ( ( $doc['status'] ?? '' ) !== 'Paid' ) {
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
</style>

<div id="cora-vault-wrapper" class="space-y-6 relative font-sans text-zinc-900 pb-20">
    <!-- Clean Top Navigation Bar -->
    <div class="flex items-center justify-between border-b border-zinc-200/80 pb-3 flex-wrap gap-3">
        <div class="flex items-center gap-2">
            <button onclick="coraSwitchVaultView('vault')" id="vault-mode-btn-vault" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-zinc-950 text-white cursor-pointer active-nav">
                Document Vault
            </button>
            <button onclick="coraSwitchVaultView('editor')" id="vault-mode-btn-editor" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer">
                Document Studio Wizard
            </button>
            <button onclick="coraSwitchVaultView('esign')" id="vault-mode-btn-esign" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer">
                E-Sign Audit
            </button>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="coraCreateNewDocInStudio()" class="px-4 py-2 bg-zinc-950 text-white text-xs font-semibold rounded-lg hover:bg-zinc-800 transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + Create Document Wizard
            </button>
            <button onclick="coraExportVaultCSV()" class="px-3 py-2 bg-white border border-zinc-200 text-zinc-700 text-xs font-semibold rounded-lg hover:bg-zinc-50 cursor-pointer">
                Export CSV
            </button>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════════
         VIEW 1: MASTER DOCUMENT VAULT DASHBOARD
         ═════════════════════════════════════════════════════════════════════════ -->
    <div id="cora-vault-view-dashboard" class="space-y-6">
        <!-- KPI Cards Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">
            <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Documents</span>
                <span class="text-2xl font-bold text-zinc-900 mt-1"><?php echo $total_docs; ?></span>
            </div>
            <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Proposals & Quotes</span>
                <span class="text-2xl font-bold text-zinc-900 mt-1"><?php echo $proposal_count; ?></span>
            </div>
            <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Signed Contracts</span>
                <span class="text-2xl font-bold text-emerald-700 mt-1"><?php echo $signed_count; ?></span>
            </div>
            <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-col justify-between">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Pending Receivables</span>
                <span class="text-2xl font-bold text-zinc-900 mt-1">₹<?php echo number_format( $total_receivables ); ?></span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 flex-wrap" id="vault-type-tabs">
                <button onclick="coraFilterVault('all')" class="cora-vtab px-3 py-1.5 rounded-lg text-xs font-semibold bg-zinc-950 text-white cursor-pointer active-vtab" data-type="all">All Documents</button>
                <button onclick="coraFilterVault('proposal')" class="cora-vtab px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer" data-type="proposal">Proposals</button>
                <button onclick="coraFilterVault('invoice')" class="cora-vtab px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer" data-type="invoice">Invoices</button>
                <button onclick="coraFilterVault('contract')" class="cora-vtab px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer" data-type="contract">Contracts</button>
                <button onclick="coraFilterVault('offer')" class="cora-vtab px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer" data-type="offer">Offer Letters</button>
            </div>

            <div class="relative flex-1 sm:w-64">
                <input type="text" id="vault-search-input" onkeyup="coraSearchVault(this.value)" placeholder="Search document title or client..." class="w-full pl-8 pr-3 py-1.5 border border-zinc-200 rounded-lg text-xs outline-none">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
        </div>

        <!-- Master Vault Table -->
        <div class="bg-white border border-zinc-200/80 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50 border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase">
                        <th class="p-3.5">Doc # & Title</th>
                        <th class="p-3.5">Type</th>
                        <th class="p-3.5">Client & GSTIN</th>
                        <th class="p-3.5">Grand Total</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">E-Sign</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="cora-vault-table-body" class="divide-y divide-zinc-100">
                    <?php foreach ( $cora_documents as $doc ) : 
                        $status = $doc['status'] ?? 'Draft';
                        $status_bg = 'bg-zinc-100 text-zinc-700';
                        if ( $status === 'Sent' ) $status_bg = 'bg-blue-50 text-blue-700 border border-blue-200/50';
                        elseif ( $status === 'Signed' ) $status_bg = 'bg-amber-50 text-amber-700 border border-amber-200/50';
                        elseif ( $status === 'Paid' ) $status_bg = 'bg-emerald-50 text-emerald-700 border border-emerald-200/50';

                        $is_signed = ! empty( $doc['signed'] );
                        $is_proposal = strtolower($doc['type'] ?? '') === 'proposal';
                    ?>
                    <tr class="hover:bg-zinc-50/60 transition-colors cora-vault-row" data-type="<?php echo esc_attr( strtolower( $doc['type'] ) ); ?>">
                        <td class="p-3.5">
                            <div class="font-mono text-[10px] text-zinc-400 font-bold"><?php echo esc_html( $doc['number'] ?? 'DOC-2026' ); ?></div>
                            <div class="font-semibold text-zinc-900 cursor-pointer hover:underline" onclick="coraOpenDocInStudio('<?php echo esc_js( $doc['id'] ); ?>')"><?php echo esc_html( $doc['title'] ); ?></div>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-700 border border-zinc-200">
                                <?php echo esc_html( $doc['type'] ); ?>
                            </span>
                        </td>
                        <td class="p-3.5">
                            <div class="font-medium text-zinc-800"><?php echo esc_html( $doc['client_name'] ); ?></div>
                            <?php if ( ! empty( $doc['client_gstin'] ) ) : ?>
                                <div class="text-[9px] font-mono text-zinc-400">GST: <?php echo esc_html( $doc['client_gstin'] ); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="p-3.5 font-bold text-zinc-950">
                            ₹<?php echo number_format( floatval( $doc['grand_total'] ?? $doc['amount'] ?? 0 ) ); ?>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold <?php echo $status_bg; ?>">
                                <?php echo esc_html( $status ); ?>
                            </span>
                        </td>
                        <td class="p-3.5">
                            <?php if ( $is_signed ) : ?>
                                <span class="text-emerald-700 font-semibold text-[11px]">✓ Signed</span>
                            <?php else : ?>
                                <button onclick="coraOpenESignDrawer('<?php echo esc_js( $doc['id'] ); ?>')" class="text-zinc-500 hover:text-zinc-950 underline text-[11px] cursor-pointer">+ E-Sign</button>
                            <?php endif; ?>
                        </td>
                        <td class="p-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <?php if ( $is_proposal ) : ?>
                                    <button onclick="coraConvertQuoteToInvoice('<?php echo esc_js( $doc['id'] ); ?>')" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[11px] font-bold cursor-pointer shadow-xs" title="1-Click Convert to GST Invoice">
                                        ⚡ Convert to Tax Invoice
                                    </button>
                                <?php endif; ?>
                                <button onclick="coraOpenBottomDocPreview('<?php echo esc_js( $doc['id'] ); ?>')" class="px-2.5 py-1 bg-white border border-zinc-200 text-zinc-800 rounded hover:bg-zinc-100 text-[11px] font-semibold cursor-pointer">View</button>
                                <button onclick="coraOpenDocInStudio('<?php echo esc_js( $doc['id'] ); ?>')" class="px-2.5 py-1 bg-zinc-950 text-white rounded hover:bg-zinc-800 text-[11px] font-semibold cursor-pointer">Edit Studio</button>
                                <button onclick="coraShareWhatsApp('<?php echo esc_js( $doc['id'] ); ?>')" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-semibold cursor-pointer" title="Share via WhatsApp">
                                    WhatsApp
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════════
         VIEW 2: SPACIOUS 5-STEP VISUAL GUIDED DOCUMENT WIZARD WITH STICKY DOCK
         ═════════════════════════════════════════════════════════════════════════ -->
    <div id="cora-vault-view-editor" class="hidden space-y-8">
        <input type="hidden" id="studio-doc-id">

        <!-- ═════════════════════════════════════════════════════════════════════
             STEP 1 SUB-PAGE: DOCUMENT CATEGORY SELECTION (SPACIOUS & ELEGANT)
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="sub-page-wiz-step-1" class="bg-white border border-zinc-200/80 rounded-3xl p-8 md:p-12 shadow-sm space-y-8">
            <div class="max-w-2xl">
                <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-widest block mb-1">Step 1 of 5</span>
                <h3 class="text-xl font-extrabold text-zinc-950 tracking-tight">What type of document are you creating?</h3>
                <p class="text-xs text-zinc-500 mt-1.5 leading-relaxed">Select a category below. In the next step, you will be able to preview visual document blueprints.</p>
            </div>

            <!-- Spacious Category Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-2">
                <div onclick="coraSelectWizCategoryCard('proposal')" id="wiz-cat-card-proposal" class="p-6 border-2 border-zinc-950 bg-zinc-50/80 rounded-2xl cursor-pointer transition-all space-y-3 shadow-sm hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-md bg-zinc-950 text-white text-[10px] font-extrabold uppercase tracking-wider">PROPOSAL & QUOTES</span>
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-900"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    </div>
                    <h4 class="font-extrabold text-zinc-950 text-base">Proposals & Quotations</h4>
                    <p class="text-zinc-500 text-xs leading-relaxed">Shoot packages, real estate listing media, commercial bids, and production estimates.</p>
                </div>

                <div onclick="coraSelectWizCategoryCard('invoice')" id="wiz-cat-card-invoice" class="p-6 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all space-y-3 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-md bg-zinc-100 text-zinc-800 text-[10px] font-extrabold uppercase tracking-wider">TAX INVOICE</span>
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                    </div>
                    <h4 class="font-extrabold text-zinc-950 text-base">Invoices & Receipts</h4>
                    <p class="text-zinc-500 text-xs leading-relaxed">Retainer tax invoices, post-production final bills, deposit slips, and statutory GST notes.</p>
                </div>

                <div onclick="coraSelectWizCategoryCard('contract')" id="wiz-cat-card-contract" class="p-6 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all space-y-3 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-md bg-zinc-100 text-zinc-800 text-[10px] font-extrabold uppercase tracking-wider">CONTRACT</span>
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <h4 class="font-extrabold text-zinc-950 text-base">Contracts & Legal SLAs</h4>
                    <p class="text-zinc-500 text-xs leading-relaxed">Service level agreements, licensing rights, non-disclosure agreements (NDAs).</p>
                </div>

                <div onclick="coraSelectWizCategoryCard('offer')" id="wiz-cat-card-offer" class="p-6 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all space-y-3 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-md bg-zinc-100 text-zinc-800 text-[10px] font-extrabold uppercase tracking-wider">OFFER LETTER</span>
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                    </div>
                    <h4 class="font-extrabold text-zinc-950 text-base">Hiring & Offer Letters</h4>
                    <p class="text-zinc-500 text-xs leading-relaxed">Associate photographer offers, editor contractor agreements, monthly retainers.</p>
                </div>

                <div onclick="coraSelectWizCategoryCard('onboarding')" id="wiz-cat-card-onboarding" class="p-6 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all space-y-3 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-md bg-zinc-100 text-zinc-800 text-[10px] font-extrabold uppercase tracking-wider">ONBOARDING</span>
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </div>
                    <h4 class="font-extrabold text-zinc-950 text-base">Client Intake & Discovery</h4>
                    <p class="text-zinc-500 text-xs leading-relaxed">Requirement discovery forms, production briefs, event moodboard questionnaires.</p>
                </div>

                <div onclick="coraSelectWizCategoryCard('equipment')" id="wiz-cat-card-equipment" class="p-6 border-2 border-zinc-200 hover:border-zinc-400 rounded-2xl cursor-pointer transition-all space-y-3 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-md bg-zinc-100 text-zinc-800 text-[10px] font-extrabold uppercase tracking-wider">EQUIPMENT</span>
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                    </div>
                    <h4 class="font-extrabold text-zinc-950 text-base">Equipment Gear Waivers</h4>
                    <p class="text-zinc-500 text-xs leading-relaxed">Camera gear rental liability waivers, model releases, location permits.</p>
                </div>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             STEP 2 SUB-PAGE: CHOOSE VISUAL TEMPLATE BLUEPRINT (WITH PAPER THUMBNAILS)
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="sub-page-wiz-step-2" class="hidden bg-white border border-zinc-200/80 rounded-3xl p-8 md:p-12 shadow-sm space-y-8">
            <div class="max-w-2xl">
                <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-widest block mb-1">Step 2 of 5</span>
                <h3 class="text-xl font-extrabold text-zinc-950 tracking-tight">Choose a Visual Template Blueprint</h3>
                <p class="text-xs text-zinc-500 mt-1.5 leading-relaxed">Below are visual paper preview cards for your selected category. Click any template to select it.</p>
            </div>

            <!-- VISUAL PREVIEW THUMBNAIL CARDS GALLERY -->
            <div id="wiz-subpage-template-gallery" class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                <!-- Populated by JS with visual A4 paper cards -->
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             STEP 3 SUB-PAGE: CLIENT & PROJECT DETAILS (SIMPLE & UNCLUTTERED)
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="sub-page-wiz-step-3" class="hidden bg-white border border-zinc-200/80 rounded-3xl p-8 md:p-12 shadow-sm space-y-8">
            <div class="max-w-2xl">
                <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-widest block mb-1">Step 3 of 5</span>
                <h3 class="text-xl font-extrabold text-zinc-950 tracking-tight">Client & Project Details</h3>
                <p class="text-xs text-zinc-500 mt-1.5 leading-relaxed">Enter your client's information. Only title and client name are required.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl text-xs">
                <div class="space-y-1.5">
                    <label class="block font-bold text-zinc-800">Document Title *</label>
                    <input type="text" id="studio-doc-title-input" placeholder="e.g. Wedding Photography Proposal..." class="w-full border border-zinc-200 rounded-xl p-3.5 bg-zinc-50 outline-none font-semibold text-sm">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-zinc-800">Document Reference #</label>
                    <input type="text" id="studio-doc-number" value="PROP-2026-0101" class="w-full border border-zinc-200 rounded-xl p-3.5 bg-zinc-50 outline-none font-mono font-bold">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-zinc-800">Client Full Name / Company *</label>
                    <input type="text" id="studio-client-name" placeholder="e.g. Arjun & Priya / Apex Realty" class="w-full border border-zinc-200 rounded-xl p-3.5 bg-white outline-none font-semibold">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-zinc-800">Client Email *</label>
                    <input type="email" id="studio-client-email" placeholder="client@example.com" class="w-full border border-zinc-200 rounded-xl p-3.5 bg-white outline-none">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-zinc-800">WhatsApp Phone Number</label>
                    <input type="text" id="studio-client-phone" placeholder="9876543210" class="w-full border border-zinc-200 rounded-xl p-3.5 bg-white outline-none font-mono">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-zinc-800">Place of Supply (POS State for Dual GST)</label>
                    <select id="studio-doc-pos" onchange="coraRecalculateStudioTotals()" class="w-full border border-zinc-200 rounded-xl p-3.5 bg-white outline-none font-semibold">
                        <option value="Delhi (07)">Delhi (07) - CGST (9%) + SGST (9%)</option>
                        <option value="Haryana (06)">Haryana (06) - IGST (18%)</option>
                        <option value="Maharashtra (27)">Maharashtra (27) - IGST (18%)</option>
                        <option value="Karnataka (29)">Karnataka (29) - IGST (18%)</option>
                    </select>
                </div>
            </div>

            <!-- Hidden Inputs needed for backend compatibility -->
            <input type="hidden" id="studio-doc-type" value="Proposal">
            <input type="hidden" id="studio-doc-status" value="Draft">
            <input type="hidden" id="studio-client-gstin" value="">
            <input type="hidden" id="studio-doc-upi" value="cora@icici">
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             STEP 4 SUB-PAGE: DYNAMIC PACKAGE, GEAR & CREW ALLOTMENT
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="sub-page-wiz-step-4" class="hidden bg-white border border-zinc-200/80 rounded-3xl p-8 md:p-12 shadow-sm space-y-8">
            <div class="max-w-2xl">
                <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-widest block mb-1">Step 4 of 5</span>
                <h3 class="text-xl font-extrabold text-zinc-950 tracking-tight">Services, Gear & Crew Allotment</h3>
                <p class="text-xs text-zinc-500 mt-1.5 leading-relaxed">Pick a package or click + Add Line Item to auto-calculate totals.</p>
            </div>

            <!-- DYNAMIC QUOTATION AUTO-FILL TOOLBAR -->
            <div class="bg-gradient-to-r from-zinc-900 to-zinc-800 text-white p-6 rounded-2xl shadow-md space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-200">⚡ 1-Click Auto-Fill (Zero Manual Entry)</h4>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs text-zinc-900">
                    <div>
                        <label class="block text-[10px] font-bold text-zinc-300 uppercase mb-1">1. Shoot Package Preset</label>
                        <select id="auto-pkg-select" onchange="coraAutoFillPackage(this.value)" class="w-full border-0 rounded-lg p-2.5 bg-white outline-none font-semibold text-xs">
                            <option value="">-- Choose Package Preset --</option>
                            <option value="wedding">Wedding Cinematography & Photos (₹4,50,000)</option>
                            <option value="realty">Real Estate 4K Walkthrough & HDR (₹1,20,000)</option>
                            <option value="event">Commercial Summit Coverage (₹3,50,000)</option>
                            <option value="retainer">Monthly Content Retainer (₹1,50,000)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-zinc-300 uppercase mb-1">2. Equipment Gear Rental</label>
                        <select id="auto-gear-select" onchange="coraAutoAddGear(this.value)" class="w-full border-0 rounded-lg p-2.5 bg-white outline-none font-semibold text-xs">
                            <option value="">+ Add Camera Gear Package</option>
                            <option value="red_komodo">RED Komodo 6K Cinema Rig (₹15,000/day)</option>
                            <option value="sony_a7s3">Sony A7S III + G-Master Lenses (₹8,000/day)</option>
                            <option value="dji_drone">DJI Mavic 3 Cine Aerial Drone (₹12,000/day)</option>
                            <option value="aputure_light">Aputure 600d Cinema Light Kit (₹6,000/day)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-zinc-300 uppercase mb-1">3. Team Crew Allotment</label>
                        <select id="auto-crew-select" onchange="coraAutoAddCrew(this.value)" class="w-full border-0 rounded-lg p-2.5 bg-white outline-none font-semibold text-xs">
                            <option value="">+ Add Crew Member Role</option>
                            <option value="lead_photog">Lead Cinematographer (₹25,000/day)</option>
                            <option value="assoc_photog">Associate Photographer (₹15,000/day)</option>
                            <option value="drone_pilot">Certified Drone Pilot (₹18,000/day)</option>
                            <option value="sr_editor">Senior Post-Production Editor (₹35,000/project)</option>
                            <option value="grip_assistant">Lighting Grip Assistant (₹5,000/day)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-zinc-300 uppercase mb-1">4. Shoot Days Multiplier</label>
                        <select id="auto-days-multiplier" onchange="coraApplyShootDaysMultiplier(this.value)" class="w-full border-0 rounded-lg p-2.5 bg-white outline-none font-bold text-xs">
                            <option value="1">1 Shoot Day (Standard)</option>
                            <option value="2">2 Shoot Days Multiplier (2x)</option>
                            <option value="3" selected>3 Shoot Days Multiplier (3x)</option>
                            <option value="5">5 Shoot Days Multiplier (5x)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- LINE ITEMS TABLE -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-extrabold text-zinc-900 uppercase tracking-wider">Line Items & Scope Matrix</h4>
                    <button type="button" onclick="coraAddStudioLineItem()" class="px-3.5 py-2 bg-zinc-950 text-white rounded-xl text-xs font-semibold hover:bg-zinc-800 cursor-pointer shadow-sm">
                        + Add Line Item
                    </button>
                </div>

                <div class="border border-zinc-200 rounded-2xl overflow-hidden shadow-xs">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-zinc-100/80 border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase">
                                <th class="p-3.5 w-1/2">Item Description & Scope</th>
                                <th class="p-3.5">SAC/HSN</th>
                                <th class="p-3.5 w-16">Qty / Days</th>
                                <th class="p-3.5">Rate (₹)</th>
                                <th class="p-3.5">GST %</th>
                                <th class="p-3.5 text-right">Amount (₹)</th>
                                <th class="p-3.5 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="studio-line-items-body" class="divide-y divide-zinc-100 bg-white">
                            <!-- Dynamic Line Rows Inserted via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- FINANCIAL SUMMARY CARD -->
            <div class="flex justify-end pt-2">
                <div class="w-full md:w-80 bg-zinc-50 p-6 rounded-2xl border border-zinc-200/80 space-y-2.5 text-xs">
                    <div class="flex justify-between text-zinc-600">
                        <span>Taxable Subtotal:</span>
                        <span id="summary-subtotal" class="font-mono font-bold">₹0</span>
                    </div>

                    <div id="row-cgst" class="flex justify-between text-zinc-600">
                        <span>CGST (9%):</span>
                        <span id="summary-cgst" class="font-mono font-bold">₹0</span>
                    </div>

                    <div id="row-sgst" class="flex justify-between text-zinc-600">
                        <span>SGST (9%):</span>
                        <span id="summary-sgst" class="font-mono font-bold">₹0</span>
                    </div>

                    <div id="row-igst" class="hidden flex justify-between text-zinc-600">
                        <span>IGST (18%):</span>
                        <span id="summary-igst" class="font-mono font-bold">₹0</span>
                    </div>

                    <div class="border-t border-zinc-200 pt-3 flex justify-between text-sm font-extrabold text-zinc-950">
                        <span>Grand Total (Incl. GST):</span>
                        <span id="summary-grandtotal" class="font-mono">₹0</span>
                    </div>

                    <div class="flex justify-between text-emerald-700 text-xs font-bold pt-1">
                        <span>Retainer Deposit Due (50%):</span>
                        <span id="summary-deposit" class="font-mono">₹0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             STEP 5 SUB-PAGE: LIVE PAPER PREVIEW, PRINT PDF & DISPATCH
             ═════════════════════════════════════════════════════════════════════ -->
        <div id="sub-page-wiz-step-5" class="hidden space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-zinc-200/80 flex items-center justify-between flex-wrap gap-4 shadow-sm">
                <div>
                    <span class="text-[10px] font-extrabold text-zinc-400 uppercase tracking-widest block mb-0.5">Step 5 of 5</span>
                    <h3 class="text-lg font-extrabold text-zinc-950">Live Paper Preview & Dispatch</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Your document is generated! Download pure A4 PDF or send 1-click WhatsApp link.</p>
                </div>

                <div class="flex items-center gap-2">
                    <button onclick="coraPrintInvoiceOnly()" class="px-5 py-2.5 bg-zinc-950 hover:bg-black text-white rounded-xl text-xs font-bold cursor-pointer shadow-sm flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        Print / Download PDF
                    </button>
                    <button onclick="coraSaveStudioDocument()" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold cursor-pointer shadow-sm">
                        Save to Vault
                    </button>
                </div>
            </div>

            <!-- LIVE PAPER CANVAS SHEET -->
            <div class="bg-zinc-100/60 p-6 md:p-12 rounded-3xl flex justify-center border border-zinc-200/50 min-h-[850px] w-full relative">
                <div id="studio-paper-canvas" class="w-full max-w-[800px] bg-white shadow-xl rounded-md p-8 md:p-14 flex flex-col justify-between font-sans min-h-[950px]">
                    <div>
                        <!-- Header Letterhead + Dynamic UPI QR Code Box -->
                        <div class="border-b border-zinc-200/80 pb-6 mb-8 flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-extrabold text-zinc-950 tracking-tight flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg bg-zinc-950 text-white text-xs font-bold flex items-center justify-center">C</span>
                                    CORA STUDIO WORKSPACE
                                </h2>
                                <p class="text-[11px] text-zinc-500 mt-1">GST registered tax billing statement & service contract</p>
                            </div>
                            <div class="flex items-center gap-4 text-right">
                                <!-- Dynamic UPI QR Code Badge -->
                                <div class="p-2 bg-zinc-50 border border-zinc-200 rounded-xl text-center flex flex-col items-center">
                                    <div class="w-14 h-14 bg-zinc-900 text-white font-mono text-[8px] flex items-center justify-center rounded p-1 text-center font-bold">
                                        [SCAN UPI QR]
                                    </div>
                                    <span class="text-[9px] font-mono text-zinc-500 mt-1" id="paper-upi-tag">UPI: cora@icici</span>
                                </div>
                                <div>
                                    <span id="paper-doc-type-badge" class="px-3 py-1 rounded-md bg-zinc-950 text-white font-bold text-[10px] uppercase tracking-wider">PROPOSAL</span>
                                    <div id="paper-doc-number" class="text-xs font-bold text-zinc-900 font-mono mt-1.5">PROP-2026-0101</div>
                                </div>
                            </div>
                        </div>

                        <!-- Live Rendered Document Body -->
                        <div id="studio-paper-body-content" class="space-y-6 text-xs text-zinc-800">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <!-- Footer Disclaimer & Statutory Tax Note -->
                    <div class="border-t border-zinc-200/80 pt-6 mt-12 text-center text-[10px] text-zinc-400">
                        © 2026 Cora Studio Workspace. GSTIN: 07AAAAA0000A1Z5. Confidential & Proprietary Document.
                    </div>
                </div>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════════
             STICKY FLOATING BOTTOM WIZARD ACTION DOCK (ALWAYS ACCESSIBLE)
             ═════════════════════════════════════════════════════════════════════ -->
        <div class="sticky bottom-4 z-[9000] w-full max-w-4xl mx-auto bg-white/95 backdrop-blur-md border border-zinc-300 rounded-2xl p-4 shadow-2xl space-y-3">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <button onclick="coraSwitchVaultView('vault')" class="px-4 py-2 border border-zinc-200 rounded-xl text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 flex items-center gap-1.5 cursor-pointer">
                        ← Back to Vault
                    </button>
                    <div>
                        <h2 class="text-xs font-extrabold text-zinc-950 tracking-tight" id="wizard-step-indicator-title">Step 1 of 5: Choose Document Category</h2>
                        <p class="text-[10px] text-zinc-500">Guided document builder designed for zero-friction client proposals & invoices.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" id="wiz-prev-step-btn" onclick="coraNavWizardStep(-1)" class="hidden px-4 py-2 border border-zinc-200 rounded-xl text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-100 cursor-pointer">
                        ← Previous Step
                    </button>
                    <button type="button" id="wiz-next-step-btn" onclick="coraNavWizardStep(1)" class="px-6 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold hover:bg-zinc-800 cursor-pointer shadow-sm">
                        Next Step →
                    </button>
                </div>
            </div>

            <!-- Visual 5-Step Progress Tracker Bar -->
            <div class="grid grid-cols-5 gap-2 pt-1 border-t border-zinc-100">
                <div id="wiz-step-pill-1" class="h-1.5 rounded-full bg-zinc-950 transition-all"></div>
                <div id="wiz-step-pill-2" class="h-1.5 rounded-full bg-zinc-200 transition-all"></div>
                <div id="wiz-step-pill-3" class="h-1.5 rounded-full bg-zinc-200 transition-all"></div>
                <div id="wiz-step-pill-4" class="h-1.5 rounded-full bg-zinc-200 transition-all"></div>
                <div id="wiz-step-pill-5" class="h-1.5 rounded-full bg-zinc-200 transition-all"></div>
            </div>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════════
         VIEW 3: E-SIGN LEGAL REGISTRY
         ═════════════════════════════════════════════════════════════════════════ -->
    <div id="cora-vault-view-esign" class="hidden space-y-6">
        <h2 class="text-base font-bold text-zinc-900">E-Signature Audit Registry</h2>
        <div class="bg-white border border-zinc-200/80 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-zinc-50 border-b border-zinc-200 text-[10px] font-bold text-zinc-500 uppercase">
                        <th class="p-3">Document Title</th>
                        <th class="p-3">Signee</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">IP Address</th>
                        <th class="p-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    <?php foreach ( $cora_documents as $doc ) : 
                        if ( empty( $doc['signed'] ) ) continue;
                    ?>
                    <tr>
                        <td class="p-3 font-semibold text-zinc-900"><?php echo esc_html( $doc['title'] ); ?></td>
                        <td class="p-3"><?php echo esc_html( $doc['signer_name'] ?? '—' ); ?></td>
                        <td class="p-3"><?php echo esc_html( $doc['signer_email'] ?? '—' ); ?></td>
                        <td class="p-3 font-mono text-[10px]"><?php echo esc_html( $doc['signer_ip'] ?? '103.21.124.8' ); ?></td>
                        <td class="p-3 font-mono text-[10px]"><?php echo esc_html( $doc['signed_at'] ?? $doc['created_at'] ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══ ISOLATED PRINTABLE INVOICE CANVAS (HIDDEN FROM UI, SHOWN ONLY ON PRINT) ═══ -->
<div id="cora-printable-canvas" class="hidden">
    <!-- Clean Printable A4 Document Sheet -->
</div>

<!-- ═══ SHOPIFY-STYLE BOTTOM PREVIEW DRAWER (SCOPED TO MAIN CONTENT PANE) ═══ -->
<div id="cora-bottom-preview-drawer" class="hidden fixed left-0 sm:left-64 lg:left-72 right-0 bottom-0 top-16 z-[9999] bg-white rounded-t-3xl border-t border-zinc-300 shadow-2xl flex flex-col overflow-hidden">
    <!-- Header Bar -->
    <div class="p-4 border-b border-zinc-200 bg-zinc-50 rounded-t-3xl flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
            <div>
                <h3 class="text-sm font-bold text-zinc-900" id="preview-doc-title">Document Preview</h3>
                <p class="text-[11px] text-zinc-500" id="preview-doc-meta">Full A4 Sheet Preview & Printing</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="coraPrintInvoiceOnly()" class="px-3.5 py-1.5 bg-zinc-900 text-white rounded-lg text-xs font-semibold hover:bg-black cursor-pointer shadow-sm">
                Print / Export PDF
            </button>
            <button id="preview-edit-btn" onclick="coraOpenStudioFromPreview()" class="px-4 py-1.5 bg-zinc-100 border border-zinc-200 text-zinc-800 rounded-lg text-xs font-semibold hover:bg-zinc-200 cursor-pointer">
                Edit Studio
            </button>
            <button onclick="coraCloseBottomPreview()" class="px-3 py-1.5 bg-zinc-200 hover:bg-zinc-300 text-zinc-800 rounded-lg text-xs font-bold cursor-pointer">
                ✕ Close
            </button>
        </div>
    </div>

    <!-- Scrollable Document Preview Body -->
    <div class="flex-1 overflow-y-auto p-6 md:p-10 bg-zinc-100/80 flex justify-center">
        <div id="preview-paper-sheet" class="w-full max-w-[780px] bg-white shadow-xl rounded-md p-8 md:p-12 flex flex-col justify-between font-sans min-h-[900px] border border-zinc-200/80">
            <div>
                <div class="border-b border-zinc-200 pb-5 mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-extrabold text-zinc-950 tracking-tight flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-zinc-950 text-white text-xs font-bold flex items-center justify-center">C</span>
                            CORA STUDIO WORKSPACE
                        </h2>
                        <p class="text-[11px] text-zinc-500 mt-1">Official Service Agreement & Document Statement</p>
                    </div>
                    <div class="text-right">
                        <span id="preview-doc-badge" class="px-3 py-1 rounded-md bg-zinc-950 text-white font-bold text-[10px] uppercase tracking-wider">PROPOSAL</span>
                        <div id="preview-doc-num" class="text-xs font-bold text-zinc-900 font-mono mt-1.5">DOC-2026</div>
                    </div>
                </div>

                <div id="preview-doc-body" class="prose prose-zinc max-w-none text-zinc-800 text-sm leading-relaxed min-h-[500px]">
                    <!-- Rendered Document HTML -->
                </div>
            </div>

            <div class="border-t border-zinc-200/80 pt-5 mt-10 text-center text-[10px] text-zinc-400">
                © 2026 Cora Studio Workspace. Confidential & Proprietary Document.
            </div>
        </div>
    </div>
</div>

<script>
window.CORA_DOCUMENTS = <?php echo json_encode( $cora_documents ); ?>;

// 18 Rich Template Presets Database across 6 Core Categories
window.CORA_TEMPLATES = {
    proposal: [
        { id: 'tpl_wedding_prop', name: 'Wedding Photography & Cinematography Proposal', amount: 450000, type: 'Proposal', desc: 'Complete 3-day luxury wedding proposal with drone aerials, candid albums, and milestones.', items: [ { desc: '3-Day Full Wedding Cinematography & Aerial Drone', sac: '998381', qty: 1, rate: 300000, tax: 18 }, { desc: 'Candid Fine-Art Photography & Signature Album Box', sac: '998381', qty: 1, rate: 150000, tax: 18 } ] },
        { id: 'tpl_re_prop', name: 'Real Estate Listing Media Proposal', amount: 120000, type: 'Proposal', desc: 'Architectural photography, 4K walkthrough video, and 3D floorplans.', items: [ { desc: 'Architectural HDR Photography & 4K Walkthrough Video', sac: '998381', qty: 1, rate: 120000, tax: 18 } ] },
        { id: 'tpl_event_prop', name: 'Commercial Event Cinematography Proposal', amount: 350000, type: 'Proposal', desc: 'Multi-camera corporate summit or conference coverage with live stream routing.', items: [ { desc: 'Multi-Camera Corporate Summit Film Coverage', sac: '998381', qty: 1, rate: 350000, tax: 18 } ] }
    ],
    invoice: [
        { id: 'tpl_retainer_inv', name: 'Monthly Retainer Billing Invoice', amount: 150000, type: 'Invoice', desc: 'Standard monthly recurring retainer invoice for ongoing content production.', items: [ { desc: 'Monthly Studio Content Retainer (4 Shoots/mo)', sac: '998381', qty: 1, rate: 150000, tax: 18 } ] },
        { id: 'tpl_post_prod_inv', name: 'Post-Production Final Invoice', amount: 200000, type: 'Invoice', desc: 'Final balance payment tax invoice prior to master file handoff.', items: [ { desc: 'Commercial Campaign Color Grading & Edit Handoff', sac: '998381', qty: 1, rate: 200000, tax: 18 } ] },
        { id: 'tpl_deposit_slip', name: 'Booking Deposit Slip & Receipt', amount: 50000, type: 'Invoice', desc: 'Official payment receipt acknowledging shoot calendar slot reservation.', items: [ { desc: 'Shoot Calendar Reservation Deposit Retainer', sac: '998381', qty: 1, rate: 50000, tax: 18 } ] }
    ],
    contract: [
        { id: 'tpl_sla_contract', name: 'Production Service Level Agreement (SLA)', amount: 250000, type: 'Contract', desc: 'Formal SLA establishing turnaround timelines, edit cycles, and archival policy.', items: [ { desc: 'Annual Content SLA Maintenance & Turnaround Service', sac: '998381', qty: 1, rate: 250000, tax: 18 } ] }
    ],
    offer: [
        { id: 'tpl_photog_offer', name: 'Associate Photographer Employment Offer', amount: 65000, type: 'Offer', desc: 'Official offer letter detailing monthly salary, shoot allowances, and gear access.', items: [ { desc: 'Associate Lead Photographer Monthly Base Fee', sac: '998381', qty: 1, rate: 65000, tax: 0 } ] }
    ],
    onboarding: [
        { id: 'tpl_intake_form', name: 'Client Requirement & Scope Discovery Form', amount: 0, type: 'Onboarding', desc: 'Structured intake questionnaire gathering brand guidelines, moodboards, and shotlists.', items: [ { desc: 'Pre-Production Creative Discovery Session', sac: '998381', qty: 1, rate: 0, tax: 0 } ] }
    ],
    equipment: [
        { id: 'tpl_gear_waiver', name: 'Camera Gear Rental & Liability Waiver', amount: 35000, type: 'Equipment', desc: 'Equipment checkout agreement transferring damage liability and return dates to renter.', items: [ { desc: 'RED Komodo 6K & Cinema Lens Package Rental', sac: '997311', qty: 1, rate: 35000, tax: 18 } ] }
    ]
};

// 5-STEP WIZARD NAVIGATION MANAGER
var currentWizStep = 1;
var selectedWizCat = 'proposal';

window.coraNavWizardStep = function(delta) {
    var targetStep = currentWizStep + delta;
    if (targetStep < 1 || targetStep > 5) return;

    if (delta > 0 && currentWizStep === 3) {
        var name = document.getElementById('studio-client-name').value.trim();
        if (!name) { coraShowToast('Enter client name to proceed.'); return; }
    }

    currentWizStep = targetStep;
    coraRenderWizardStepUI();
};

window.coraRenderWizardStepUI = function() {
    for (var s = 1; s <= 5; s++) {
        var el = document.getElementById('sub-page-wiz-step-' + s);
        if (el) el.classList.add('hidden');
    }

    var activeEl = document.getElementById('sub-page-wiz-step-' + currentWizStep);
    if (activeEl) activeEl.classList.remove('hidden');

    // Update Progress Pills in Sticky Dock
    for (var i = 1; i <= 5; i++) {
        var pill = document.getElementById('wiz-step-pill-' + i);
        if (pill) {
            if (i <= currentWizStep) pill.classList.replace('bg-zinc-200', 'bg-zinc-950');
            else pill.classList.replace('bg-zinc-950', 'bg-zinc-200');
        }
    }

    // Step Header Title
    var titles = {
        1: 'Step 1 of 5: Choose Document Category',
        2: 'Step 2 of 5: Choose Visual Template Blueprint',
        3: 'Step 3 of 5: Client & Project Details',
        4: 'Step 4 of 5: Services, Gear & Crew Allotments',
        5: 'Step 5 of 5: Live Paper Canvas Preview & Dispatch'
    };
    document.getElementById('wizard-step-indicator-title').textContent = titles[currentWizStep];

    // Prev / Next Buttons State in Sticky Dock
    var prevBtn = document.getElementById('wiz-prev-step-btn');
    var nextBtn = document.getElementById('wiz-next-step-btn');

    if (currentWizStep === 1) prevBtn.classList.add('hidden');
    else prevBtn.classList.remove('hidden');

    if (currentWizStep === 5) {
        nextBtn.classList.add('hidden');
        coraRenderPaperPreviewInStep5();
    } else {
        nextBtn.classList.remove('hidden');
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
};

window.coraSelectWizCategoryCard = function(catKey) {
    selectedWizCat = catKey;
    document.getElementById('studio-doc-type').value = catKey.charAt(0).toUpperCase() + catKey.slice(1);

    document.querySelectorAll('[id^="wiz-cat-card-"]').forEach(function(card){
        card.classList.remove('border-zinc-950', 'bg-zinc-50/80');
        card.classList.add('border-zinc-200');
    });

    var activeCard = document.getElementById('wiz-cat-card-' + catKey);
    if (activeCard) {
        activeCard.classList.add('border-zinc-950', 'bg-zinc-50/80');
        activeCard.classList.remove('border-zinc-200');
    }

    coraPopulateVisualTemplateCards(catKey);
};

window.coraPopulateVisualTemplateCards = function(catKey) {
    var gallery = document.getElementById('wiz-subpage-template-gallery');
    if (!gallery) return;
    var tpls = CORA_TEMPLATES[catKey] || [];
    var html = '';

    tpls.forEach(function(t){
        html += '<div onclick="coraSelectWizSubpageTemplate(\'' + t.id + '\')" class="p-6 border-2 border-zinc-200 hover:border-zinc-950 bg-white rounded-2xl cursor-pointer transition-all space-y-4 shadow-sm hover:shadow-md flex flex-col justify-between group">' +
                '<div class="space-y-3">' +
                '<!-- Mini A4 Paper Canvas Illustration -->' +
                '<div class="w-full bg-zinc-50 border border-zinc-200 rounded-xl p-4 space-y-2.5 font-sans relative overflow-hidden group-hover:bg-zinc-100/60 transition-colors">' +
                '<div class="flex items-center justify-between border-b border-zinc-200/80 pb-2">' +
                '<div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-zinc-950 text-white font-extrabold text-[8px] flex items-center justify-center">C</span><span class="font-extrabold text-[9px] text-zinc-900 tracking-tight">CORA STUDIO</span></div>' +
                '<span class="text-[8px] font-mono font-bold text-zinc-500 bg-white px-1.5 py-0.5 border border-zinc-200 rounded">DOC-2026</span>' +
                '</div>' +
                '<div class="h-2 bg-zinc-200 rounded w-3/4"></div>' +
                '<div class="h-1.5 bg-zinc-200/60 rounded w-1/2"></div>' +
                '<div class="space-y-1 pt-1">' +
                '<div class="h-1 bg-zinc-300/60 rounded w-full"></div>' +
                '<div class="h-1 bg-zinc-200/60 rounded w-5/6"></div>' +
                '</div>' +
                '<div class="flex justify-between items-center pt-2 border-t border-zinc-200/60 text-[9px] font-mono font-bold text-zinc-800">' +
                '<span>ESTIMATED TOTAL</span><span>' + (t.amount > 0 ? '₹' + t.amount.toLocaleString() : 'STANDARD') + '</span>' +
                '</div>' +
                '</div>' +
                '<div>' +
                '<h4 class="font-extrabold text-zinc-950 text-sm group-hover:underline">' + t.name + '</h4>' +
                '<p class="text-zinc-500 text-xs mt-1 leading-relaxed">' + t.desc + '</p>' +
                '</div>' +
                '</div>' +
                '<button type="button" class="w-full py-2 bg-zinc-100 group-hover:bg-zinc-950 group-hover:text-white text-zinc-900 font-bold text-xs rounded-xl transition-all">Select Blueprint →</button>' +
                '</div>';
    });

    gallery.innerHTML = html;
};

window.coraSelectWizSubpageTemplate = function(tplId) {
    var tpls = CORA_TEMPLATES[selectedWizCat] || [];
    var tpl = tpls.find(function(t){ return t.id === tplId; });
    if (!tpl) return;

    document.getElementById('studio-doc-title-input').value = tpl.name;
    
    var tbody = document.getElementById('studio-line-items-body');
    tbody.innerHTML = '';
    if (tpl.items) {
        tpl.items.forEach(function(it){ coraAddStudioLineItem(it); });
    }
    coraShowToast('Loaded template preset!');
    coraNavWizardStep(1); // Advance to Step 3 (Client Details)
};

window.coraRenderPaperPreviewInStep5 = function() {
    var title = document.getElementById('studio-doc-title-input').value || 'Untitled Document';
    var num = document.getElementById('studio-doc-number').value || 'DOC-2026';
    var clientName = document.getElementById('studio-client-name').value || 'Client';
    var clientGstin = document.getElementById('studio-client-gstin').value || '';
    var upi = document.getElementById('studio-doc-upi').value || 'cora@icici';
    var subtotal = document.getElementById('summary-subtotal').textContent;
    var grandtotal = document.getElementById('summary-grandtotal').textContent;

    document.getElementById('paper-doc-number').textContent = num;
    document.getElementById('paper-upi-tag').textContent = 'UPI: ' + upi;

    var html = '<h2 class="text-base font-bold text-zinc-900 border-b pb-2 mb-4">' + title + '</h2>' +
               '<div class="grid grid-cols-2 gap-4 bg-zinc-50 p-4 rounded-xl border border-zinc-200 mb-6">' +
               '<div><span class="text-[10px] font-bold text-zinc-400 uppercase block">Billed To:</span><strong class="text-zinc-900">' + clientName + '</strong>' + (clientGstin?'<br><span class="font-mono text-[10px] text-zinc-500">GST: '+clientGstin+'</span>':'') + '</div>' +
               '<div class="text-right"><span class="text-[10px] font-bold text-zinc-400 uppercase block">Total Payable:</span><strong class="text-zinc-950 text-base font-mono">' + grandtotal + '</strong></div>' +
               '</div>';

    html += '<table class="w-full text-left border-collapse text-xs mb-6"><thead class="bg-zinc-100 border-b"><tr><th class="p-2.5">Item Description</th><th class="p-2.5">SAC</th><th class="p-2.5 text-center">Qty</th><th class="p-2.5 text-right">Rate</th><th class="p-2.5 text-right">Amount</th></tr></thead><tbody>';

    var rows = document.querySelectorAll('.cora-line-item-row');
    rows.forEach(function(row){
        var d = row.querySelector('.item-desc').value;
        var s = row.querySelector('.item-sac').value;
        var q = row.querySelector('.item-qty').value;
        var r = row.querySelector('.item-rate').value;
        var a = row.querySelector('.item-line-total').textContent;
        html += '<tr class="border-b"><td class="p-2.5 font-semibold">' + d + '</td><td class="p-2.5 font-mono text-[10px]">' + s + '</td><td class="p-2.5 text-center font-bold">' + q + '</td><td class="p-2.5 text-right font-mono">₹' + parseFloat(r).toLocaleString() + '</td><td class="p-2.5 text-right font-mono font-bold">' + a + '</td></tr>';
    });

    html += '</tbody></table>';
    html += '<div class="text-right text-xs space-y-1"><div class="text-zinc-500">Subtotal: ' + subtotal + '</div><div class="text-sm font-extrabold text-zinc-950">Grand Total (Incl. GST): ' + grandtotal + '</div></div>';

    document.getElementById('studio-paper-body-content').innerHTML = html;
};

// 1-CLICK CONVERT QUOTATION TO TAX INVOICE
window.coraConvertQuoteToInvoice = function(docId) {
    var doc = CORA_DOCUMENTS.find(function(d){ return d.id === docId; });
    if (!doc) return;

    coraShowToast('Converting Quotation to GST Tax Invoice...');
    var newDoc = Object.assign({}, doc, {
        id: 'doc_' + dateStr() + '_' + Math.floor(100 + Math.random() * 900),
        number: 'INV-2026-' + Math.floor(1000 + Math.random() * 9000),
        title: doc.title.replace('Proposal:', 'Invoice:').replace('Quotation:', 'Invoice:'),
        type: 'Invoice',
        status: 'Sent',
        created_at: new Date().toISOString().split('T')[0]
    });

    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_save_document', nonce: coraREData.ajaxNonce,
        id: '', number: newDoc.number, title: newDoc.title, type: 'Invoice', status: 'Sent',
        client_name: newDoc.client_name, client_email: newDoc.client_email, client_phone: newDoc.client_phone, client_gstin: newDoc.client_gstin,
        pos_state: newDoc.pos_state, upi_vpa: newDoc.upi_vpa, items: JSON.stringify(newDoc.items)
    }, success: function(r) {
        if (r.success) {
            coraShowToast('Quotation converted to Tax Invoice!');
            location.reload();
        } else coraShowToast(r.data || 'Conversion failed.');
    }});
};

function dateStr() {
    var d = new Date();
    return d.getFullYear() + ('0' + (d.getMonth() + 1)).slice(-2) + ('0' + d.getDate()).slice(-2);
}

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

// DEDICATED PURE INVOICE PRINT FUNCTION (HIDES ALL UI & SIDEBARS)
window.coraPrintInvoiceOnly = function() {
    var title = document.getElementById('studio-doc-title-input').value || 'Invoice';
    var num = document.getElementById('studio-doc-number').value || 'INV-2026';
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
            '<div style="color: #047857; font-weight: 700; font-size: 11px; margin-top: 4px;">Retainer Deposit (50%): ' + deposit + '</div>' +
            '</div></div>' +
            '<div style="border-top: 1px solid #e4e4e7; margin-top: 40px; padding-top: 16px; text-align: center; font-size: 10px; color: #a1a1aa;">© 2026 Cora Studio Workspace. GSTIN: 07AAAAA0000A1Z5. Confidential Document.</div></div>';

    printableCanvas.innerHTML = html;
    window.print();
};

// Line Items Dynamic Manager in Studio
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
    tr.innerHTML = '<td class="p-2.5"><input type="text" class="item-desc w-full border border-zinc-200 rounded p-1.5 text-xs outline-none" value="' + desc + '" placeholder="Item name / service scope..."></td>' +
                   '<td class="p-2.5"><input type="text" class="item-sac w-20 border border-zinc-200 rounded p-1.5 text-xs font-mono outline-none" value="' + sac + '" placeholder="998381"></td>' +
                   '<td class="p-2.5"><input type="number" class="item-qty w-14 border border-zinc-200 rounded p-1.5 text-xs outline-none text-center font-bold" value="' + qty + '" onchange="coraRecalculateStudioTotals()"></td>' +
                   '<td class="p-2.5"><input type="number" class="item-rate w-24 border border-zinc-200 rounded p-1.5 text-xs outline-none font-bold" value="' + rate + '" onchange="coraRecalculateStudioTotals()"></td>' +
                   '<td class="p-2.5"><select class="item-tax border border-zinc-200 rounded p-1.5 text-xs outline-none" onchange="coraRecalculateStudioTotals()"><option value="18" ' + (tax==18?'selected':'') + '>18% GST</option><option value="12" ' + (tax==12?'selected':'') + '>12% GST</option><option value="5" ' + (tax==5?'selected':'') + '>5% GST</option><option value="0" ' + (tax==0?'selected':'') + '>0% GST</option></select></td>' +
                   '<td class="p-2.5 text-right font-bold text-zinc-950 font-mono item-line-total">₹0</td>' +
                   '<td class="p-2.5 text-center"><button type="button" onclick="document.getElementById(\'' + rowId + '\').remove(); coraRecalculateStudioTotals();" class="text-zinc-400 hover:text-red-600 font-bold">✕</button></td>';

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
    var grandTotal = subtotal + taxTotal;
    var deposit = grandTotal * 0.5;

    document.getElementById('summary-subtotal').textContent = '₹' + Math.round(subtotal).toLocaleString();
    
    if (isIgst) {
        document.getElementById('row-cgst').classList.add('hidden');
        document.getElementById('row-sgst').classList.add('hidden');
        document.getElementById('row-igst').classList.remove('hidden');
        document.getElementById('summary-igst').textContent = '₹' + Math.round(igst).toLocaleString();
    } else {
        document.getElementById('row-cgst').classList.remove('hidden');
        document.getElementById('row-sgst').classList.remove('hidden');
        document.getElementById('row-igst').classList.add('hidden');
        document.getElementById('summary-cgst').textContent = '₹' + Math.round(cgst).toLocaleString();
        document.getElementById('summary-sgst').textContent = '₹' + Math.round(sgst).toLocaleString();
    }

    document.getElementById('summary-grandtotal').textContent = '₹' + Math.round(grandTotal).toLocaleString();
    document.getElementById('summary-deposit').textContent = '₹' + Math.round(deposit).toLocaleString();
};

// URL Navigation State Switcher
window.coraSwitchVaultView = function(view, docId) {
    var newUrl = window.location.pathname + '?sub_page=vault&vtab=' + view + (docId ? '&doc_id=' + docId : '');
    window.history.replaceState({vtab: view, docId: docId}, '', newUrl);
    localStorage.setItem('cora_vault_tab', view);
    if (docId) localStorage.setItem('cora_vault_doc_id', docId);

    document.getElementById('cora-vault-view-dashboard').classList.add('hidden');
    document.getElementById('cora-vault-view-editor').classList.add('hidden');
    document.getElementById('cora-vault-view-esign').classList.add('hidden');

    document.querySelectorAll('[id^="vault-mode-btn-"]').forEach(function(b){
        b.classList.remove('bg-zinc-950', 'text-white');
        b.classList.add('text-zinc-600');
    });

    var activeBtn = document.getElementById('vault-mode-btn-' + view);
    if (activeBtn) {
        activeBtn.classList.add('bg-zinc-950', 'text-white');
        activeBtn.classList.remove('text-zinc-600');
    }

    if (view === 'vault') document.getElementById('cora-vault-view-dashboard').classList.remove('hidden');
    else if (view === 'editor') {
        document.getElementById('cora-vault-view-editor').classList.remove('hidden');
        coraSelectWizCategoryCard('proposal');
        currentWizStep = 1;
        coraRenderWizardStepUI();
    }
    else if (view === 'esign') document.getElementById('cora-vault-view-esign').classList.remove('hidden');
};

document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var tab = urlParams.get('vtab') || localStorage.getItem('cora_vault_tab') || 'vault';
    var docId = urlParams.get('doc_id') || localStorage.getItem('cora_vault_doc_id');

    if (tab === 'editor' && docId) coraOpenDocInStudio(docId);
    else coraSwitchVaultView(tab);
});

window.coraCreateNewDocInStudio = function() {
    document.getElementById('studio-doc-id').value = '';
    document.getElementById('studio-doc-number').value = 'DOC-2026-' + Math.floor(1000 + Math.random() * 9000);
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
    var doc = CORA_DOCUMENTS.find(function(d){ return d.id === docId; });
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
    var status = document.getElementById('studio-doc-status').value;

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
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_save_document', nonce: coraREData.ajaxNonce,
        id: id, number: number, title: title, type: type, status: status,
        client_name: clientName, client_email: clientEmail, client_phone: clientPhone, client_gstin: clientGstin,
        pos_state: posState, upi_vpa: upiVpa, items: JSON.stringify(items)
    }, success: function(r) {
        if (r.success) {
            coraShowToast('Document saved successfully!');
            location.reload();
        } else coraShowToast(r.data || 'Save failed.');
    }});
};

window.coraFilterVault = function(type) {
    document.querySelectorAll('.cora-vtab').forEach(function(btn){
        btn.classList.remove('bg-zinc-950', 'text-white', 'active-vtab');
        btn.classList.add('text-zinc-600', 'hover:bg-zinc-100');
    });
    if (event && event.target) {
        event.target.classList.add('bg-zinc-950', 'text-white', 'active-vtab');
        event.target.classList.remove('text-zinc-600', 'hover:bg-zinc-100');
    }

    document.querySelectorAll('.cora-vault-row').forEach(function(row){
        if (type === 'all' || row.dataset.type === type) row.style.display = '';
        else row.style.display = 'none';
    });
};

window.coraSearchVault = function(query) {
    var q = query.toLowerCase();
    document.querySelectorAll('.cora-vault-row').forEach(function(row){
        row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
    });
};

window.coraExportVaultCSV = function() {
    coraShowToast('Exporting Document Vault CSV...');
    var csv = 'Doc ID,Number,Title,Type,Client,Amount,Status,Signed,Created Date\n';
    CORA_DOCUMENTS.forEach(function(d){
        csv += '"' + d.id + '","' + (d.number||'') + '","' + d.title + '","' + d.type + '","' + d.client_name + '",' + (d.grand_total||d.amount||0) + ',"' + d.status + '",' + (d.signed?'Yes':'No') + ',"' + d.created_at + '"\n';
    });
    var blob = new Blob([csv], { type: 'text/csv' });
    var a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'cora_documents.csv'; a.click();
};
</script>
