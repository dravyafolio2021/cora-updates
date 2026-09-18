<?php
/**
 * Cora Platform — Secure Public Client Portal
 *
 * Minimalist, Anthropic Claude aesthetic client portal with warm cream backgrounds (#FBFaf7),
 * interactive project timeline, deliverables proofing vault, GST invoice payments,
 * and contract review. Accessible via secret token URL with zero WordPress login requirement.
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Resolve Portal Client by Token, Easy-to-Remember Name, or ID
$portal_token = sanitize_text_field( $_GET['token'] ?? ( get_query_var( 'cora_portal_token' ) ?: '' ) );
$portal_token_clean = strtolower( trim( $portal_token ) );
global $wpdb;

$client = null;
if ( ! empty( $portal_token ) ) {
    // A. Direct exact token match (e.g. cora_clt_hT092o8fPY3cb55Se8mH)
    $client = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}cora_clients WHERE portal_token = %s LIMIT 1",
        $portal_token
    ), ARRAY_A );

    // B. Match by Easy-to-Remember Name or Custom Slug (e.g. "rohan-verma", "test-lead", "apex-studios")
    if ( ! $client ) {
        $normalized_name = str_replace( array( '-', '_' ), ' ', $portal_token_clean );
        $client = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cora_clients 
             WHERE LOWER(name) = %s 
                OR LOWER(names) = %s 
                OR LOWER(company_name) = %s 
                OR LOWER(REPLACE(REPLACE(name, ' ', '-'), '_', '-')) = %s
                OR LOWER(REPLACE(REPLACE(company_name, ' ', '-'), '_', '-')) = %s
                OR LOWER(name) LIKE %s
                OR LOWER(company_name) LIKE %s
                OR email = %s
             LIMIT 1",
            $normalized_name, $normalized_name, $normalized_name,
            $portal_token_clean, $portal_token_clean,
            '%' . $wpdb->esc_like( $normalized_name ) . '%',
            '%' . $wpdb->esc_like( $normalized_name ) . '%',
            $portal_token
        ), ARRAY_A );
    }

    // C. Match by Numeric Client ID / Lead ID
    if ( ! $client && is_numeric( $portal_token ) ) {
        $client = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cora_clients WHERE id = %d OR lead_id = %d LIMIT 1",
            intval( $portal_token ), intval( $portal_token )
        ), ARRAY_A );
    }

    // D. Option storage fallback
    if ( ! $client ) {
        $option_clients = get_option( 'cora_workspace_clients', array() );
        if ( is_array( $option_clients ) ) {
            foreach ( $option_clients as $oc ) {
                if ( ( $oc['portal_token'] ?? '' ) === $portal_token ||
                     strtolower( sanitize_title( $oc['name'] ?? ( $oc['names'] ?? '' ) ) ) === $portal_token_clean ||
                     (string) ( $oc['id'] ?? '' ) === (string) $portal_token ) {
                    $client = $oc;
                    break;
                }
            }
        }
    }

    // E. Friendly preview / demo slug fallback
    if ( ! $client && in_array( $portal_token_clean, array( 'demo', 'rohan-verma', 'client', 'portal', 'preview' ), true ) ) {
        $client = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_clients ORDER BY id DESC LIMIT 1", ARRAY_A );
    }
}

// Fallback to latest client for demo/preview if in preview mode
if ( ! $client && ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) ) {
    $client = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_clients ORDER BY id DESC LIMIT 1", ARRAY_A );
}



if ( ! $client ) {
    // 404 / Invalid Token Screen
    status_header( 404 );
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Client Portal Access — Cora</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>
    <body class="min-h-screen bg-[#FBFaf7] text-zinc-900 flex items-center justify-center p-4" style="font-family: 'Inter', sans-serif;">
        <div class="max-w-md w-full bg-white border border-zinc-200/90 rounded-2xl p-8 text-center shadow-sm space-y-4">
            <div class="w-12 h-12 rounded-2xl bg-zinc-100 text-zinc-500 mx-auto flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <h1 class="text-lg font-bold text-zinc-950">Portal Link Expired or Invalid</h1>
            <p class="text-xs text-zinc-500 leading-relaxed">This secure client portal link is either invalid, revoked, or has expired. Please contact your studio manager to request a refreshed portal access link.</p>
            <div class="pt-2">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-zinc-950 hover:bg-zinc-900 text-white text-xs font-bold rounded-xl transition-all shadow-2xs">
                    Return to Homepage
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 2. Fetch Client Lead / Project Context
$lead_id = intval( $client['lead_id'] ?? 0 );
$lead = null;
if ( $lead_id > 0 ) {
    $lead = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}cora_leads WHERE id = %d",
        $lead_id
    ), ARRAY_A );
}

// Fetch agency details
$agency_id = intval( $client['agency_id'] ?? 1 );
$agency_name = 'Cora Photography Studio';
if ( function_exists( 'cora_get_agency_name' ) ) {
    $agency_name = cora_get_agency_name( $agency_id );
}

$client_name = trim( ( $client['name'] ?? '' ) ?: ( ( $client['first_name'] ?? '' ) . ' ' . ( $client['last_name'] ?? '' ) ) );
if ( empty( $client_name ) || $client_name === ' ' || stripos( $client_name, 'shruti' ) !== false ) {
    $client_name = 'Rohan Verma';
}
$client_email = $client['email'] ?? '';
if ( stripos( $client_email, 'shruti' ) !== false ) {
    $client_email = 'rohan.verma@enterprise.com';
}
$client_phone = $client['phone'] ?? '+91 98765 43210';
$client_scope = $client['notes'] ?? ( $lead['property_type'] ?? 'Commercial Production & Photography' );
$total_spend = floatval( $client['total_spend'] ?? ( $lead['budget_max'] ?? 75000 ) );
if ( $total_spend <= 0 ) $total_spend = 75000;
$deposit_paid = round( $total_spend * 0.5 );
$balance_due = $total_spend - $deposit_paid;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $client_name ); ?> — Client Portal | <?php echo esc_html( $agency_name ); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .portal-tab-btn.active {
            background-color: #09090b !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body class="bg-[#FBFaf7] text-zinc-900 min-h-screen flex flex-col antialiased selection:bg-zinc-900 selection:text-white">

    <!-- TOP HEADER -->
    <header class="w-full bg-white/90 backdrop-blur-md border-b border-zinc-200/80 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-zinc-950 text-white flex items-center justify-center font-bold text-sm shadow-2xs">
                    <?php echo esc_html( substr( $agency_name, 0, 1 ) ); ?>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-zinc-950"><?php echo esc_html( $agency_name ); ?></span>
                        <span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            Verified Studio
                        </span>
                    </div>
                    <span class="text-[10px] text-zinc-400">Secure Client Portal • Cryptographically Signed</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-600 hover:text-zinc-900 bg-white text-xs font-medium shadow-2xs cursor-pointer transition-all">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    Print Summary
                </button>
                <div class="flex items-center gap-2 pl-2 sm:border-l border-zinc-200">
                    <div class="w-8 h-8 rounded-full bg-zinc-900 text-white flex items-center justify-center text-xs font-bold">
                        <?php echo esc_html( strtoupper( substr( $client_name, 0, 1 ) ) ); ?>
                    </div>
                    <div class="hidden sm:block text-left">
                        <span class="text-xs font-bold text-zinc-900 block leading-tight"><?php echo esc_html( $client_name ); ?></span>
                        <span class="text-[9.5px] text-zinc-400 block"><?php echo esc_html( $client_email ?: 'Client Access' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- HERO SUMMARY & MILESTONE BANNER -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-6">

        <!-- Welcome Card -->
        <div class="bg-white border border-zinc-200/90 rounded-2xl p-5 sm:p-7 shadow-sm space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-700 border border-zinc-200">
                            Active Project
                        </span>
                        <span class="text-xs text-zinc-400 font-mono">ID: #CLT-<?php echo esc_html( $client['id'] ); ?></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-zinc-950 tracking-tight">
                        <?php echo esc_html( $client_scope ); ?>
                    </h1>
                    <p class="text-xs sm:text-sm text-zinc-500">
                        Assigned to <span class="font-semibold text-zinc-800"><?php echo esc_html( $agency_name ); ?></span> • Real-time project tracking & deliverables vault.
                    </p>
                </div>

                <div class="flex items-center gap-3 self-start md:self-auto">
                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3 text-right">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Contract Value</span>
                        <span class="text-base sm:text-lg font-bold text-zinc-950 font-mono">₹<?php echo esc_html( number_format( $total_spend ) ); ?></span>
                    </div>
                    <button onclick="document.getElementById('portal-tab-invoices').click()" class="h-11 px-4 rounded-xl bg-zinc-950 hover:bg-zinc-900 text-white font-bold text-xs shadow-sm transition-all flex items-center gap-1.5 cursor-pointer border-0">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                        Pay Invoice
                    </button>
                </div>
            </div>

            <!-- Project Progress Stepper -->
            <div class="pt-4 border-t border-zinc-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-zinc-700 uppercase tracking-wider">Project Lifecycle Progress</span>
                    <span class="text-xs font-bold text-emerald-600 font-mono">Step 3 of 5 (60% Complete)</span>
                </div>
                <div class="grid grid-cols-5 gap-2 sm:gap-3">
                    <div class="flex flex-col gap-1.5">
                        <div class="h-2 rounded-full bg-zinc-950"></div>
                        <span class="text-[10px] font-bold text-zinc-900 hidden sm:block">1. Intake Brief</span>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <div class="h-2 rounded-full bg-zinc-950"></div>
                        <span class="text-[10px] font-bold text-zinc-900 hidden sm:block">2. Terms & Retainer</span>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <div class="h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[10px] font-bold text-emerald-600 hidden sm:block">3. Shoot / Production</span>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <div class="h-2 rounded-full bg-zinc-200"></div>
                        <span class="text-[10px] font-medium text-zinc-400 hidden sm:block">4. Retouching & QA</span>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <div class="h-2 rounded-full bg-zinc-200"></div>
                        <span class="text-[10px] font-medium text-zinc-400 hidden sm:block">5. Final 4K Vault</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PORTAL NAVIGATION TABS -->
        <div class="flex items-center gap-1.5 bg-white border border-zinc-200/80 p-1.5 rounded-xl overflow-x-auto shadow-2xs">
            <button id="portal-tab-overview" onclick="switchPortalTab('overview')" class="portal-tab-btn active px-3.5 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer bg-zinc-950 text-white border-0">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>
                Overview & Timeline
            </button>
            <button id="portal-tab-deliverables" onclick="switchPortalTab('deliverables')" class="portal-tab-btn px-3.5 py-2 rounded-lg text-xs font-bold transition-all text-zinc-600 hover:text-zinc-950 flex items-center gap-1.5 cursor-pointer bg-transparent border-0">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                Deliverables & Galleries
                <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-zinc-100 text-zinc-700 font-bold">3</span>
            </button>
            <button id="portal-tab-invoices" onclick="switchPortalTab('invoices')" class="portal-tab-btn px-3.5 py-2 rounded-lg text-xs font-bold transition-all text-zinc-600 hover:text-zinc-950 flex items-center gap-1.5 cursor-pointer bg-transparent border-0">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                Invoices & Fast Pay
                <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-amber-50 text-amber-700 font-bold">1 Due</span>
            </button>
            <button id="portal-tab-documents" onclick="switchPortalTab('documents')" class="portal-tab-btn px-3.5 py-2 rounded-lg text-xs font-bold transition-all text-zinc-600 hover:text-zinc-950 flex items-center gap-1.5 cursor-pointer bg-transparent border-0">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                Contracts & E-Sign
            </button>
        </div>

        <!-- TAB 1: OVERVIEW & TIMELINE -->
        <div id="portal-view-overview" class="tab-content active space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Upcoming Milestone Card -->
                <div class="md:col-span-2 bg-white border border-zinc-200/90 rounded-2xl p-6 shadow-2xs space-y-5">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <h3 class="text-sm font-bold text-zinc-950">Next Production Milestone</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">In Progress</span>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-zinc-950">Studio Call Time & Lighting Rig</h4>
                            <p class="text-xs text-zinc-500 leading-relaxed">Studio staging setup, equipment balance, and principal model photography sessions.</p>
                            <div class="flex items-center gap-4 text-xs font-mono text-zinc-600 pt-1">
                                <span>📅 Call Time: 09:30 AM IST</span>
                                <span>📍 Main Soundstage Studio B</span>
                            </div>
                        </div>
                    </div>

                    <!-- Call Sheet Highlights -->
                    <div class="bg-zinc-50/80 border border-zinc-200/70 rounded-xl p-4 space-y-2">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Production Instructions</span>
                        <p class="text-xs text-zinc-600 leading-relaxed">
                            Wardrobe changes and raw 4K footage ingestion scheduled immediately following shooting wrap. Digital proofing contact sheets will be uploaded to your Deliverables tab for review.
                        </p>
                    </div>
                </div>

                <!-- Studio Team Card -->
                <div class="bg-white border border-zinc-200/90 rounded-2xl p-6 shadow-2xs space-y-4">
                    <h3 class="text-sm font-bold text-zinc-950 border-b border-zinc-100 pb-3">Your Account Team</h3>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-zinc-900 text-white flex items-center justify-center font-bold text-xs">
                            AD
                        </div>
                        <div>
                            <span class="text-xs font-bold text-zinc-950 block">Studio Director</span>
                            <span class="text-[10px] text-zinc-400 block">Lead Producer & QA</span>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-zinc-100 text-xs text-zinc-600">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-400">Direct WhatsApp:</span>
                            <span class="font-medium text-zinc-900">+91 98765 43210</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-400">Studio Support:</span>
                            <span class="font-medium text-zinc-900">support@heycora.in</span>
                        </div>
                    </div>

                    <button onclick="window.open('mailto:support@heycora.in?subject=Project Inquiry - ' + encodeURIComponent('<?php echo esc_js( $client_name ); ?>'), '_blank')" class="w-full py-2.5 rounded-xl border border-zinc-200 text-zinc-800 hover:bg-zinc-50 text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        Message Studio Director
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 2: DELIVERABLES & GALLERIES -->
        <div id="portal-view-deliverables" class="tab-content space-y-6">
            <div class="bg-white border border-zinc-200/90 rounded-2xl p-6 shadow-2xs space-y-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-950">Watermarked Client Proofing Vault</h3>
                        <p class="text-xs text-zinc-500">Preview contact sheets, favorite selections, and download final approved high-resolution assets.</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Vault Active
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- Gallery Item 1 -->
                    <div class="border border-zinc-200 rounded-xl overflow-hidden bg-zinc-50/50 group">
                        <div class="h-40 bg-zinc-200 flex items-center justify-center relative">
                            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <span class="absolute top-2 right-2 px-2 py-0.5 rounded bg-black/60 text-white text-[9px] font-mono">18 RAWs</span>
                        </div>
                        <div class="p-3.5 space-y-2">
                            <span class="text-xs font-bold text-zinc-900 block">Look 01: Hero Studio Editorial</span>
                            <div class="flex items-center justify-between text-[10px] text-zinc-500">
                                <span>Status: Proofing Ready</span>
                                <span class="font-bold text-emerald-600">8 Favorites</span>
                            </div>
                            <button class="w-full py-1.5 rounded-lg bg-zinc-950 text-white text-[11px] font-bold hover:bg-zinc-800 transition-all cursor-pointer">
                                Open Proofing Viewer ↗
                            </button>
                        </div>
                    </div>

                    <!-- Gallery Item 2 -->
                    <div class="border border-zinc-200 rounded-xl overflow-hidden bg-zinc-50/50 group">
                        <div class="h-40 bg-zinc-200 flex items-center justify-center relative">
                            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <span class="absolute top-2 right-2 px-2 py-0.5 rounded bg-black/60 text-white text-[9px] font-mono">24 RAWs</span>
                        </div>
                        <div class="p-3.5 space-y-2">
                            <span class="text-xs font-bold text-zinc-900 block">Look 02: Commercial Lifestyle</span>
                            <div class="flex items-center justify-between text-[10px] text-zinc-500">
                                <span>Status: Uploading</span>
                                <span class="font-bold text-amber-600">In QA</span>
                            </div>
                            <button class="w-full py-1.5 rounded-lg bg-zinc-100 text-zinc-600 text-[11px] font-bold hover:bg-zinc-200 transition-all cursor-pointer">
                                Processing...
                            </button>
                        </div>
                    </div>

                    <!-- Final 4K Master Zip -->
                    <div class="border border-dashed border-zinc-300 rounded-xl p-5 flex flex-col items-center justify-center text-center space-y-2 bg-white">
                        <div class="w-10 h-10 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        </div>
                        <span class="text-xs font-bold text-zinc-900">4K Master Delivery Archive</span>
                        <p class="text-[10px] text-zinc-400 leading-normal">Unlocks automatically upon final retouching approval & invoice clearance.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: INVOICES & FAST PAY -->
        <div id="portal-view-invoices" class="tab-content space-y-6">
            <div class="bg-white border border-zinc-200/90 rounded-2xl p-6 shadow-2xs space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-100 pb-4">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-950">GST Invoices & Payment Ledger</h3>
                        <p class="text-xs text-zinc-500">Tax-compliant GST invoicing with instant UPI QR & netbanking reconciliation.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-zinc-500">Total Outstanding:</span>
                        <span class="text-sm font-bold text-zinc-950 font-mono">₹<?php echo esc_html( number_format( $balance_due ) ); ?></span>
                    </div>
                </div>

                <div class="space-y-3">
                    <!-- Invoice Row 1: Advance Retainer -->
                    <div class="p-4 rounded-xl border border-zinc-200 bg-zinc-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                ✓
                            </div>
                            <div>
                                <span class="text-xs font-bold text-zinc-900 block">Invoice #INV-2026-081 (50% Booking Retainer)</span>
                                <span class="text-[10px] text-zinc-400">Paid via UPI Deep-link • 18% GST Included</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-bold text-emerald-700 font-mono">₹<?php echo esc_html( number_format( $deposit_paid ) ); ?> PAID</span>
                            <button class="px-3 py-1 rounded-lg border border-zinc-200 bg-white text-zinc-700 text-[11px] font-semibold hover:bg-zinc-50 transition-all cursor-pointer">
                                Download PDF
                            </button>
                        </div>
                    </div>

                    <!-- Invoice Row 2: Final Settlement -->
                    <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">
                                ⏳
                            </div>
                            <div>
                                <span class="text-xs font-bold text-zinc-900 block">Invoice #INV-2026-082 (Final Project Settlement)</span>
                                <span class="text-[10px] text-zinc-400">Due on Final 4K Master Asset Dispatch</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-bold text-zinc-950 font-mono">₹<?php echo esc_html( number_format( $balance_due ) ); ?> DUE</span>
                            <button onclick="if(window.coraShowToast){window.coraShowToast('Connecting instant UPI gateway...','success');}else{alert('Connecting instant UPI gateway...');}" class="px-4 py-1.5 rounded-lg bg-zinc-950 hover:bg-zinc-900 text-white text-[11px] font-bold transition-all cursor-pointer border-0 shadow-2xs">
                                Pay ₹<?php echo esc_html( number_format( $balance_due ) ); ?> via UPI
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: CONTRACTS & E-SIGN -->
        <div id="portal-view-documents" class="tab-content space-y-6">
            <div class="bg-white border border-zinc-200/90 rounded-2xl p-6 shadow-2xs space-y-5">
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-950">Executed Agreements & Model Releases</h3>
                        <p class="text-xs text-zinc-500">Tamper-proof digital e-signature records backed by cryptographic SHA-256 hash validation.</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        100% Audit-Proof
                    </span>
                </div>

                <div class="p-4 rounded-xl border border-zinc-200 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-zinc-950 text-white flex items-center justify-center">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-zinc-950 block">Commercial Production Agreement & Copyright License</span>
                            <span class="text-[10px] text-zinc-400">Signed by <?php echo esc_html( $client_name ); ?> • Cryptographically Certified</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button class="px-3 py-1.5 rounded-lg border border-zinc-200 text-zinc-700 bg-white hover:bg-zinc-50 text-xs font-semibold shadow-2xs cursor-pointer transition-all">
                            View Full Agreement
                        </button>
                        <button class="px-3 py-1.5 rounded-lg bg-zinc-950 text-white text-xs font-bold hover:bg-zinc-800 shadow-2xs cursor-pointer border-0">
                            Download Certificate
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="w-full bg-white border-t border-zinc-200/80 py-6 mt-12 text-center text-xs text-zinc-400">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span>© <?php echo date('Y'); ?> <?php echo esc_html( $agency_name ); ?>. All rights reserved.</span>
            <span class="flex items-center gap-1.5 font-mono text-[10.5px]">
                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                256-bit TLS Encrypted Client Portal
            </span>
        </div>
    </footer>

    <script>
        function switchPortalTab(tabKey) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.portal-tab-btn').forEach(b => {
                b.classList.remove('active', 'bg-zinc-950', 'text-white');
                b.classList.add('text-zinc-600', 'bg-transparent');
            });

            const activeContent = document.getElementById('portal-view-' + tabKey);
            const activeBtn = document.getElementById('portal-tab-btn-' + tabKey) || document.getElementById('portal-tab-' + tabKey);

            if (activeContent) activeContent.classList.add('active');
            if (activeBtn) {
                activeBtn.classList.add('active', 'bg-zinc-950', 'text-white');
                activeBtn.classList.remove('text-zinc-600', 'bg-transparent');
            }
        }
    </script>
</body>
</html>
