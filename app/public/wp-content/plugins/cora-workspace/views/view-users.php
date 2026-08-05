<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_user_id = get_current_user_id();
$current_role = ! empty( wp_get_current_user()->roles ) ? wp_get_current_user()->roles[0] : '';
$current_agency = cora_get_current_user_agency_id();
$current_branch = cora_get_current_user_branch_id();

// Active industry mode resolution (Real Estate vs Studio/Photography)
$active_industry = function_exists( 'cora_get_active_industry' ) 
    ? cora_get_active_industry() 
    : ( ! empty( $_COOKIE['cora_workspace_industry'] ) 
        ? sanitize_text_field( $_COOKIE['cora_workspace_industry'] ) 
        : get_option( 'cora_workspace_industry', 'real_estate' ) );
$is_studio_mode = ( strpos( strtolower( $active_industry ), 'photo' ) !== false || strpos( strtolower( $active_industry ), 'studio' ) !== false );

$feature_labels = array(
    'crm_leads'         => $is_studio_mode ? 'Client Leads (CRM)' : 'Buyer Leads (CRM)',
    'showings_bookings' => $is_studio_mode ? 'Shoots & Bookings' : 'Showings & Bookings',
    'financials'        => 'Financials',
    'media_vault'       => 'Media & Vault',
    'equipment'         => $is_studio_mode ? 'Camera Equipment' : 'Property Listings',
    'ai_suite'          => 'AI Suite',
    'attendance'        => 'Attendance',
    'quota_label'       => $is_studio_mode ? 'Max Shoot/Booking Quota (Monthly)' : 'Max Showing/Listing Quota (Monthly)'
);

// Build user roles labels dynamically (including custom roles)
$role_labels = cora_get_all_roles();
if ( ! cora_is_real_shruti() ) {
    unset( $role_labels['administrator'], $role_labels['cora_shruti'] );
}

// Fetch all users in active agency (multi-tenant scope)
$user_query_args = array();
if ( $current_agency !== 'super' ) {
    $user_query_args['meta_query'] = array(
        array(
            'key'     => 'cora_agency_id',
            'value'   => function_exists('cora_get_agency_identifiers') ? cora_get_agency_identifiers( $current_agency ) : $current_agency,
            'compare' => 'IN'
        )
    );
}
$all_wp_users = get_users( $user_query_args );

// Filter by branch if current user is branch scoped
$users = array();
foreach ( $all_wp_users as $u ) {
    $u_branch = cora_normalize_branch_id( get_user_meta( $u->ID, 'cora_branch_id', true ) );
    $norm_current_branch = cora_normalize_branch_id( $current_branch );
    if ( ! cora_is_workspace_owner() && ! empty( $current_branch ) && $u_branch !== $norm_current_branch ) {
        continue;
    }
    // Set default status if missing
    $status = get_user_meta( $u->ID, 'cora_user_status', true );
    if ( empty( $status ) ) {
        update_user_meta( $u->ID, 'cora_user_status', 'active' );
    }
    $users[] = $u;
}

// Fetch invitations
$pending_invites = cora_db_get_invitations();

// Fetch branches
$branches = cora_db_get_branches();
$agency_branches = $branches;

// Permissions Matrix options
$cora_permissions = get_option( 'cora_role_permissions', array() );
?>

<style>
    /* Reset scroll blocks and heights on mobile screens */
    @media (max-width: 1023px) {
        html, body, #wpwrap, #wpbody, #wpbody-content, #wpcontent {
            height: auto !important;
            overflow: auto !important;
        }
        #cora-workspace {
            height: auto !important;
            min-height: 100vh !important;
            overflow: visible !important;
        }
        .cora-main {
            height: auto !important;
            min-height: 100vh !important;
            overflow-y: visible !important;
        }
    }

    /* Hide scrollbars for chrome/safari/firefox */
    .scrollbar-hide::-webkit-scrollbar {
        display: none !important;
    }
    .scrollbar-hide {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }

    /* Mobile Responsive Tables to Cards Transformation */
    @media (max-width: 767px) {
        .responsive-table thead {
            display: none !important;
        }
        .responsive-table tbody,
        .responsive-table tr,
        .responsive-table td {
            display: block !important;
            width: 100% !important;
        }
        .responsive-table tr {
            background-color: #fafafa !important;
            border: 1px solid #f4f4f5 !important;
            border-radius: 12px !important;
            padding: 12px 14px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.01) !important;
            text-align: left !important;
        }
        .dark .responsive-table tr {
            background-color: #1a1a1e !important;
            border: 1px solid #2d2d30 !important;
        }
        .responsive-table td {
            border: 0 !important;
            padding: 6px 0 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            text-align: right !important;
        }
        .responsive-table td::before {
            content: attr(data-label) !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            font-size: 9px !important;
            color: #71717a !important;
            text-align: left !important;
            display: inline-block !important;
        }
        .dark .responsive-table td::before {
            color: #a1a1aa !important;
        }
    }

    /* Floating popover for permissions matrix cell */
    .cora-matrix-popover {
        animation: coraPopoverFadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes coraPopoverFadeIn {
        from { opacity: 0; transform: translateY(-4px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>

<div class="p-0 m-0 border-0 outline-none md:space-y-6 space-y-4">
    <!-- Desktop Header -->
    <div class="hidden md:flex items-center justify-between border-b border-zinc-200/50 dark:border-zinc-800/40 pb-5 mb-5 select-none">
        <div class="cora-page-header flex items-center gap-4.5">
            <div class="flex flex-col min-w-0">
                <h1 class="cora-page-title text-xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 leading-tight">User Management</h1>
                <p class="cora-section-desc text-xs text-zinc-400 mt-1 leading-normal"><?php echo $is_studio_mode ? 'Add studio crew members, manage active user accounts, and control workspace permissions.' : 'Add brokerage team members, manage active user accounts, and control workspace permissions.'; ?></p>
            </div>
        </div>
        
        <div class="flex items-center gap-3 shrink-0">
            <!-- LLM Platforms Stacked Overlapping Shortcuts (Desktop) -->
            <div class="cora-platform-stack flex items-center -space-x-2.5 select-none mr-1">
                <!-- ChatGPT Button -->
                <button onclick="coraAskExternalPlatform('openai')" class="group relative z-[5] w-7 h-7 rounded-full border-0 bg-emerald-50/70 hover:bg-emerald-100/50 dark:bg-emerald-950/20 dark:hover:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" class="w-3 h-3"><path d="M9.205 8.658v-2.26c0-.19.072-.333.238-.428l4.543-2.616c.619-.357 1.356-.523 2.117-.523 2.854 0 4.662 2.212 4.662 4.566 0 .167 0 .357-.024.547l-4.71-2.759a.797.797 0 00-.856 0l-5.97 3.473zm10.609 8.8V12.06c0-.333-.143-.57-.429-.737l-5.97-3.473 1.95-1.118a.433.433 0 01.476 0l4.543 2.617c1.309.76 2.189 2.378 2.189 3.948 0 1.808-1.07 3.473-2.76 4.163zM7.802 12.703l-1.95-1.142c-.167-.095-.239-.238-.239-.428V5.899c0-2.545 1.95-4.472 4.591-4.472 1 0 1.927.333 2.712.928L8.23 5.067c-.285.166-.428.404-.428.737v6.898zM12 15.128l-2.795-1.57v-3.33L12 8.658l2.795 1.57v3.33L12 15.128zm1.796 7.23c-1 0-1.927-.332-2.712-.927l4.686-2.712c.285-.166.428-.404.428-.737v-6.898l1.974 1.142c.167.095.238.238.238.428v5.233c0 2.545-1.974 4.472-4.614 4.472zm-5.637-5.303l-4.544-2.617c-1.308-.761-2.188-2.378-2.188-3.948A4.482 4.482 0 014.21 6.327v5.423c0 .333.143.571.428.738l5.947 3.449-1.95 1.118a.432 4.432 0 01-.476 0zm-.262 3.9c-2.688 0-4.662-2.021-4.662-4.519 0-.19.024-.38.047-.57l4.686 2.71c.286.167.571.167.856 0l5.97-3.448v2.26c0 .19-.07.333-.237.428l-4.543 2.616c-.619.357-1.356.523-2.117.523z"/></svg>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                        Ask ChatGPT
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-t-zinc-950"></span>
                    </span>
                </button>
                
                <!-- Claude Button -->
                <button onclick="coraAskExternalPlatform('claude')" class="group relative z-[4] w-7 h-7 rounded-full border-0 bg-amber-50/70 hover:bg-amber-100/50 dark:bg-amber-950/20 dark:hover:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-500 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" class="w-3 h-3"><path d="m4.7144 15.9555 4.7174-2.6471.079-.2307-.079-.1275h-.2307l-.7893-.0486-2.6956-.0729-2.3375-.0971-2.2646-.1214-.5707-.1215-.5343-.7042.0546-.3522.4797-.3218.686.0608 1.5179.1032 2.2767.1578 1.6514.0972 2.4468.255h.3886l.0546-.1579-.1336-.0971-.1032-.0972L6.973 9.8356l-2.55-1.6879-1.3356-.9714-.7225-.4918-.3643-.4614-.1578-1.0078.6557-.7225.8803.0607.2246.0607.8925.686 1.9064 1.4754 2.4893 1.8336.3643.3035.1457-.1032.0182-.0728-.164-.2733-1.3539-2.4467-1.445-2.4893-.6435-1.032-.17-.6194c-.0607-.255-.1032-.4674-.1032-.7285L6.287.1335 6.6997 0l.9957.1336.419.3642.6192 1.4147 1.0018 2.2282 1.5543 3.0296.4553.8985.2429.8318.091.255h.1579v-.1457l.1275-1.706.2368-2.0947.2307-2.6957.0789-.7589.3764-.9107.7468-.4918.5828.2793.4797.686-.0668.4433-.2853 1.8517-.5586 2.9021-.3643 1.9429h.2125l.2429-.2429.9835-1.3053 1.6514-2.0643.7286-.8196.85-.9046.5464-.4311h1.0321l.759 1.1293-.34 1.1657-1.0625 1.3478-.8804 1.1414-1.2628 1.7-.7893 1.36.0729.1093.1882-.0183 2.8535-.607 1.5421-.2794 1.8396-.3157.8318.3886.091.3946-.3278.8075-1.967.4857-2.3072.4614-3.4364.8136-.0425.0304.0486.0607 1.5482.1457.6618.0364h1.621l3.0175.2247.7892.522.4736.6376-.079.4857-1.2142.6193-1.6393-.3886-3.825-.9107-1.3113-.3279h-.1822v.1093l1.0929 1.0686 2.0035 1.8092 2.5075 2.3314.1275.5768-.3218.4554-.34-.0486-2.2039-1.6575-.85-.7468-1.9246-1.621h-.1275v.17l.4432.6496 2.3436 3.5214.1214 1.0807-.17.3521-.6071.2125-.6679-.1214-1.3721-1.9246L14.38 17.959l-1.1414-1.9428-.1397.079-.674 7.2552-.3156.3703-.7286.2793-.6071-.4614-.3218-.7468.3218-1.4753.3886-1.9246.3157-1.53.2853-1.9004.17-.6314-.0121-.0425-.1397.0182-1.4328 1.9672-2.1796 2.9446-1.7243 1.8456-.4128.164-.7164-.3704.0667-.6618.4008-.5889 2.386-3.0357 1.4389-1.882.929-1.0868-.0062-.1579h-.0546l-6.3385 4.1164-1.1293.1457-.4857-.4554.0608-.7467.2307-.2429 1.9064-1.3114Z"/></svg>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                        Ask Claude
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-t-zinc-950"></span>
                    </span>
                </button>
                
                <!-- Gemini Button -->
                <button onclick="coraAskExternalPlatform('gemini')" class="group relative z-[3] w-7 h-7 rounded-full border-0 bg-blue-5/70 hover:bg-blue-100/50 dark:bg-blue-950/20 dark:hover:bg-blue-950/40 flex items-center justify-center text-blue-600 dark:text-blue-400 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" class="w-3 h-3"><path d="M11.04 19.32Q12 21.51 12 24q0-2.49.93-4.68.96-2.19 2.58-3.81t3.81-2.55Q21.51 12 24 12q-2.49 0-4.68-.93a12.3 12.3 0 0 1-3.81-2.58 12.3 12.3 0 0 1-2.58-3.81Q12 2.49 12 0q0 2.49-.96 4.68-.93 2.19-2.55 3.81a12.3 12.3 0 0 1-3.81 2.58Q2.49 12 0 12q2.49 0 4.68.96 2.19.93 3.81 2.55t2.55 3.81"/></svg>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                        Ask Gemini
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-t-zinc-950"></span>
                    </span>
                </button>
                
                <!-- Perplexity Button -->
                <button onclick="coraAskExternalPlatform('perplexity')" class="group relative z-[2] w-7 h-7 rounded-full border-0 bg-zinc-50/50 hover:bg-zinc-100/80 dark:bg-zinc-800/20 dark:hover:bg-zinc-800/40 flex items-center justify-center text-zinc-650 dark:text-zinc-400 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><line x1="12" y1="2" x2="12" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line><line x1="4.93" y1="19.07" x2="19.07" y2="4.93"></line></svg>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                        Ask Perplexity
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-t-zinc-950"></span>
                    </span>
                </button>
                
                <!-- YouTube Button -->
                <button onclick="openPermissionsVideoDrawer()" class="group relative z-[1] w-7 h-7 rounded-full border-0 bg-red-5/70 hover:bg-red-100/50 dark:bg-red-950/20 dark:hover:bg-red-950/40 flex items-center justify-center text-red-600 dark:text-red-500 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" class="w-3 h-3"><path d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.107C19.518 3.545 12 3.545 12 3.545s-7.518 0-9.388.511a3.002 3.002 0 0 0-2.11 2.107C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 0 0 2.11 2.107c1.87.511 9.388.511 9.388.511s7.518 0 9.388-.511a3.003 3.003 0 0 0 2.11-2.107c.502-1.87.502-5.837.502-5.837s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                        Tutorial Walkthrough
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-t-zinc-950"></span>
                    </span>
                </button>
            </div>
            
            <?php if ( cora_is_super_owner() || current_user_can( 'manage_options' ) || in_array( $current_role, array( 'administrator', 'cora_shruti', 'cora_super_admin', 'cora_manager', 'cora_branch_manager', 'cora_re_broker_owner', 'cora_re_managing_agent', 'cora_studio_owner', 'cora_studio_manager', 'cora_workspace_owner', 'owner' ) ) ) : ?>
                <button onclick="openInviteDrawer()" class="bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-50 dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-xs px-4 py-2.5 rounded-xl transition-all cursor-pointer active:scale-98 shadow-sm flex items-center gap-1.5 border border-zinc-950 dark:border-zinc-50 shrink-0">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Invite User
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Header (Visible only on mobile) -->
    <div class="flex md:hidden items-center justify-between gap-3 mb-2 px-0 py-3 border-b border-zinc-200/60 dark:border-zinc-800 bg-white dark:bg-zinc-900 select-none">
        <div class="flex items-center gap-2 min-w-0">
            <span class="text-zinc-900 dark:text-zinc-100 flex shrink-0">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </span>
            <div class="min-w-0">
                <h1 class="text-sm font-bold tracking-tight text-zinc-900 dark:text-zinc-100 truncate"><?php echo $is_studio_mode ? 'Crew' : 'Team'; ?></h1>
                <p class="text-[10px] text-zinc-400 truncate"><?php echo $is_studio_mode ? 'Add studio crew & manage permissions.' : 'Add brokerage team & manage permissions.'; ?></p>
            </div>
        </div>
        
        <div class="flex items-center gap-2.5 shrink-0">
            <!-- LLM Platforms Stacked Overlapping Shortcuts (Mobile) -->
            <div class="cora-platform-stack flex items-center -space-x-2.5 select-none mr-0.5">
                <!-- ChatGPT Button -->
                <button onclick="coraAskExternalPlatform('openai')" class="group relative z-[5] w-7 h-7 rounded-full border-0 bg-emerald-50/70 hover:bg-emerald-100/50 dark:bg-emerald-950/20 dark:hover:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" class="w-3 h-3"><path d="M9.205 8.658v-2.26c0-.19.072-.333.238-.428l4.543-2.616c.619-.357 1.356-.523 2.117-.523 2.854 0 4.662 2.212 4.662 4.566 0 .167 0 .357-.024.547l-4.71-2.759a.797.797 0 00-.856 0l-5.97 3.473zm10.609 8.8V12.06c0-.333-.143-.57-.429-.737l-5.97-3.473 1.95-1.118a.433.433 0 01.476 0l4.543 2.617c1.309.76 2.189 2.378 2.189 3.948 0 1.808-1.07 3.473-2.76 4.163zM7.802 12.703l-1.95-1.142c-.167-.095-.239-.238-.239-.428V5.899c0-2.545 1.95-4.472 4.591-4.472 1 0 1.927.333 2.712.928L8.23 5.067c-.285.166-.428.404-.428.737v6.898zM12 15.128l-2.795-1.57v-3.33L12 8.658l2.795 1.57v3.33L12 15.128zm1.796 7.23c-1 0-1.927-.332-2.712-.927l4.686-2.712c.285-.166.428-.404.428-.737v-6.898l1.974 1.142c.167.095.238.238.238.428v5.233c0 2.545-1.974 4.472-4.614 4.472zm-5.637-5.303l-4.544-2.617c-1.308-.761-2.188-2.378-2.188-3.948A4.482 4.482 0 014.21 6.327v5.423c0 .333.143.571.428.738l5.947 3.449-1.95 1.118a.432 4.432 0 01-.476 0zm-.262 3.9c-2.688 0-4.662-2.021-4.662-4.519 0-.19.024-.38.047-.57l4.686 2.71c.286.167.571.167.856 0l5.97-3.448v2.26c0 .19-.07.333-.237.428l-4.543 2.616c-.619.357-1.356.523-2.117.523z"/></svg>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                        Ask ChatGPT
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-t-zinc-950"></span>
                    </span>
                </button>
                
                <!-- Claude Button -->
                <button onclick="coraAskExternalPlatform('claude')" class="group relative z-[4] w-7 h-7 rounded-full border-0 bg-amber-50/70 hover:bg-amber-100/50 dark:bg-amber-950/20 dark:hover:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-500 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" class="w-3 h-3"><path d="m4.7144 15.9555 4.7174-2.6471.079-.2307-.079-.1275h-.2307l-.7893-.0486-2.6956-.0729-2.3375-.0971-2.2646-.1214-.5707-.1215-.5343-.7042.0546-.3522.4797-.3218.686.0608 1.5179.1032 2.2767.1578 1.6514.0972 2.4468.255h.3886l.0546-.1579-.1336-.0971-.1032-.0972L6.973 9.8356l-2.55-1.6879-1.3356-.9714-.7225-.4918-.3643-.4614-.1578-1.0078.6557-.7225.8803.0607.2246.0607.8925.686 1.9064 1.4754 2.4893 1.8336.3643.3035.1457-.1032.0182-.0728-.164-.2733-1.3539-2.4467-1.445-2.4893-.6435-1.032-.17-.6194c-.0607-.255-.1032-.4674-.1032-.7285L6.287.1335 6.6997 0l.9957.1336.419.3642.6192 1.4147 1.0018 2.2282 1.5543 3.0296.4553.8985.2429.8318.091.255h.1579v-.1457l.1275-1.706.2368-2.0947.2307-2.6957.0789-.7589.3764-.9107.7468-.4918.5828.2793.4797.686-.0668.4433-.2853 1.8517-.5586 2.9021-.3643 1.9429h.2125l.2429-.2429.9835-1.3053 1.6514-2.0643.7286-.8196.85-.9046.5464-.4311h1.0321l.759 1.1293-.34 1.1657-1.0625 1.3478-.8804 1.1414-1.2628 1.7-.7893 1.36.0729.1093.1882-.0183 2.8535-.607 1.5421-.2794 1.8396-.3157.8318.3886.091.3946-.3278.8075-1.967.4857-2.3072.4614-3.4364.8136-.0425.0304.0486.0607 1.5482.1457.6618.0364h1.621l3.0175.2247.7892.522.4736.6376-.079.4857-1.2142.6193-1.6393-.3886-3.825-.9107-1.3113-.3279h-.1822v.1093l1.0929 1.0686 2.0035 1.8092 2.5075 2.3314.1275.5768-.3218.4554-.34-.0486-2.2039-1.6575-.85-.7468-1.9246-1.621h-.1275v.17l.4432.6496 2.3436 3.5214.1214 1.0807-.17.3521-.6071.2125-.6679-.1214-1.3721-1.9246L14.38 17.959l-1.1414-1.9428-.1397.079-.674 7.2552-.3156.3703-.7286.2793-.6071-.4614-.3218-.7468.3218-1.4753.3886-1.9246.3157-1.53.2853-1.9004.17-.6314-.0121-.0425-.1397.0182-1.4328 1.9672-2.1796 2.9446-1.7243 1.8456-.4128.164-.7164-.3704.0667-.6618.4008-.5889 2.386-3.0357 1.4389-1.882.929-1.0868-.0062-.1579h-.0546l-6.3385 4.1164-1.1293.1457-.4857-.4554.0608-.7467.2307-.2429 1.9064-1.3114Z"/></svg>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                        Ask Claude
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-t-zinc-950"></span>
                    </span>
                </button>
                
                <!-- Gemini Button -->
                <button onclick="coraAskExternalPlatform('gemini')" class="group relative z-[3] w-7 h-7 rounded-full border-0 bg-blue-5/70 hover:bg-blue-100/50 dark:bg-blue-950/20 dark:hover:bg-blue-950/40 flex items-center justify-center text-blue-600 dark:text-blue-400 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" class="w-3 h-3"><path d="M11.04 19.32Q12 21.51 12 24q0-2.49.93-4.68.96-2.19 2.58-3.81t3.81-2.55Q21.51 12 24 12q-2.49 0-4.68-.93a12.3 12.3 0 0 1-3.81-2.58 12.3 12.3 0 0 1-2.58-3.81Q12 2.49 12 0q0 2.49-.96 4.68-.93 2.19-2.55 3.81a12.3 12.3 0 0 1-3.81 2.58Q2.49 12 0 12q2.49 0 4.68.96 2.19.93 3.81 2.55t2.55 3.81"/></svg>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                        Ask Gemini
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-t-zinc-950"></span>
                    </span>
                </button>
                
                <!-- Perplexity Button -->
                <button onclick="coraAskExternalPlatform('perplexity')" class="group relative z-[2] w-7 h-7 rounded-full border-0 bg-zinc-50/50 hover:bg-zinc-100/80 dark:bg-zinc-800/20 dark:hover:bg-zinc-800/40 flex items-center justify-center text-zinc-650 dark:text-zinc-400 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><line x1="12" y1="2" x2="12" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line><line x1="4.93" y1="19.07" x2="19.07" y2="4.93"></line></svg>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                        Ask Perplexity
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-t-zinc-950"></span>
                    </span>
                </button>
                
                <!-- YouTube Button -->
                <button onclick="openPermissionsVideoDrawer()" class="group relative z-[1] w-7 h-7 rounded-full border-0 bg-red-5/70 hover:bg-red-100/50 dark:bg-red-950/20 dark:hover:bg-red-950/40 flex items-center justify-center text-red-600 dark:text-red-500 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" class="w-3 h-3"><path d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.107C19.518 3.545 12 3.545 12 3.545s-7.518 0-9.388.511a3.002 3.002 0 0 0-2.11 2.107C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 0 0 2.11 2.107c1.87.511 9.388.511 9.388.511s7.518 0 9.388-.511a3.003 3.003 0 0 0 2.11-2.107c.502-1.87.502-5.837.502-5.837s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                        Tutorial Walkthrough
                        <span class="absolute top-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-t-zinc-950"></span>
                    </span>
                </button>
            </div>
            
            <?php if ( cora_is_super_owner() || current_user_can( 'manage_options' ) || in_array( $current_role, array( 'administrator', 'cora_shruti', 'cora_super_admin', 'cora_manager', 'cora_branch_manager', 'cora_re_broker_owner', 'cora_re_managing_agent', 'cora_studio_owner', 'cora_studio_manager', 'cora_workspace_owner', 'owner' ) ) ) : ?>
                <button onclick="openInviteDrawer()" class="bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 font-bold text-[10px] px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer active:scale-95 shadow-sm flex items-center gap-1 shrink-0">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Invite
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Desktop Sub Navigation Tabs -->
    <div class="cora-sub-tabs-container hidden md:flex border-b border-zinc-200 dark:border-zinc-800 items-center gap-1.5 overflow-x-auto pb-px shrink-0 select-none no-scrollbar mb-4">
        <button class="cora-sub-tab active flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-semibold border-b-2 border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 transition-all cursor-pointer whitespace-nowrap animate-none" data-target="tab-active-members">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            Active Members
        </button>
        <button class="cora-sub-tab flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-medium border-b-2 border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer whitespace-nowrap" data-target="tab-pending-invites">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            Pending Invitations
        </button>
        <button class="cora-sub-tab flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-medium border-b-2 border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer whitespace-nowrap" data-target="tab-permissions-matrix">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
            Permissions Matrix
        </button>
        <button class="cora-sub-tab flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-medium border-b-2 border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer whitespace-nowrap" data-target="tab-attendance-logs">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Attendance Logs
        </button>
        <?php if ( cora_is_super_owner() || current_user_can( 'manage_options' ) || in_array( $current_role, array( 'administrator', 'cora_shruti', 'cora_super_admin', 'cora_re_broker_owner', 'cora_studio_owner', 'cora_workspace_owner', 'owner' ) ) ) : ?>
            <button class="cora-sub-tab flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-medium border-b-2 border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer whitespace-nowrap" data-target="tab-custom-roles">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Custom Roles
            </button>
        <?php endif; ?>
        <?php if ( cora_is_super_owner() || current_user_can( 'manage_options' ) || in_array( $current_role, array( 'administrator', 'cora_shruti', 'cora_super_admin', 'cora_re_broker_owner', 'cora_studio_owner', 'cora_workspace_owner', 'owner', 'cora_manager' ) ) ) : ?>
            <button class="cora-sub-tab flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs font-medium border-b-2 border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer whitespace-nowrap" data-target="tab-owner-automations">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path></svg>
                Automated Digests & Alerts
            </button>
        <?php endif; ?>
    </div>

    <!-- Mobile Sub Navigation Tabs -->
    <div class="cora-sub-tabs-container flex md:hidden items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-px mb-4 px-0 bg-white dark:bg-zinc-900 relative select-none">
        <div class="flex items-center gap-1.5">
            <!-- Active Members tab -->
            <button class="cora-sub-tab active flex items-center gap-1.5 px-2.5 pb-2 pt-1 text-[11px] font-semibold border-b-[1.5px] border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 transition-all cursor-pointer whitespace-nowrap focus:outline-none focus:ring-0 outline-none shadow-none" data-target="tab-active-members">
                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                Members
            </button>
            <!-- Pending Invitations tab -->
            <button class="cora-sub-tab flex items-center gap-1.5 px-2.5 pb-2 pt-1 text-[11px] font-medium border-b-[1.5px] border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer whitespace-nowrap focus:outline-none focus:ring-0 outline-none shadow-none" data-target="tab-pending-invites">
                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                Invites
            </button>
        </div>

        <!-- More Button and Floating Dropdown Panel -->
        <div class="relative">
            <button id="mobile-tabs-more-btn" class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] font-medium text-zinc-650 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer focus:outline-none focus:ring-0 outline-none shadow-none border-0 bg-transparent">
                <span>More</span>
                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="transition-transform" id="more-chevron-icon"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>

            <!-- Floating Right-Aligned Dropdown Menu Card -->
            <div id="mobile-tabs-more-dropdown" class="hidden absolute right-0 top-full mt-1.5 z-30 w-48 bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800 rounded-lg shadow-md py-1 animate-in fade-in duration-100">
                <button class="cora-sub-tab flex items-center gap-2 w-full px-3 py-2 text-left text-[11px] font-medium text-zinc-650 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer whitespace-nowrap focus:outline-none focus:ring-0 outline-none shadow-none" data-target="tab-permissions-matrix">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                    Permissions Matrix
                </button>
                <button class="cora-sub-tab flex items-center gap-2 w-full px-3 py-2 text-left text-[11px] font-medium text-zinc-650 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer whitespace-nowrap focus:outline-none focus:ring-0 outline-none shadow-none" data-target="tab-attendance-logs">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Attendance Logs
                </button>
                <?php if ( cora_is_super_owner() || current_user_can( 'manage_options' ) || in_array( $current_role, array( 'administrator', 'cora_shruti', 'cora_super_admin', 'cora_re_broker_owner', 'cora_studio_owner', 'cora_workspace_owner', 'owner' ) ) ) : ?>
                    <button class="cora-sub-tab flex items-center gap-2 w-full px-3 py-2 text-left text-[11px] font-medium text-zinc-650 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer whitespace-nowrap focus:outline-none focus:ring-0 outline-none shadow-none" data-target="tab-custom-roles">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        Custom Roles
                    </button>
                <?php endif; ?>
                <button class="cora-sub-tab flex items-center gap-2 w-full px-3 py-2 text-left text-[11px] font-medium text-zinc-650 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer whitespace-nowrap focus:outline-none focus:ring-0 outline-none shadow-none" data-target="tab-owner-automations">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path></svg>
                    Automated Digests
                </button>
            </div>
        </div>
    </div>

    <!-- TAB 1: ACTIVE MEMBERS -->
    <div id="tab-active-members" class="cora-tab-content space-y-4">
        <!-- Filters Toolbar -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 md:p-4 shadow-sm flex flex-col gap-2.5 md:gap-3">
            <!-- Search bar & Mobile Toggle Row -->
            <div class="flex flex-row items-center gap-2 md:gap-3 w-full justify-between">
                <div class="flex flex-row items-center gap-2 flex-1 min-w-0">
                    <!-- Search bar -->
                    <div class="relative flex-1 max-w-xs md:max-w-sm">
                        <input type="text" id="member-search" oninput="filterActiveMembers()" class="w-full h-9 text-xs pl-8 pr-3 rounded-lg border-0 bg-zinc-100/60 dark:bg-zinc-850/60 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:ring-0 focus:outline-none text-zinc-900 dark:text-zinc-100 transition-colors" placeholder="Search by name or email...">
                        <div class="absolute left-2.5 top-0 bottom-0 flex items-center pointer-events-none text-zinc-400">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                    </div>

                    <!-- Mobile Filter Toggle Button -->
                    <button type="button" id="mobile-filter-toggle" onclick="toggleMobileFilters()" class="md:hidden h-9 w-9 shrink-0 flex items-center justify-center rounded-lg border-0 bg-zinc-100/60 dark:bg-zinc-850/60 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-700/50 focus:outline-none focus:ring-0 transition-colors cursor-pointer" title="Toggle Filters">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    </button>

                    <!-- Desktop Inline Filters -->
                    <div class="hidden md:flex items-center gap-3">
                        <!-- Role Filter -->
                        <select id="filter-role" onchange="filterActiveMembers()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 px-3 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer focus:border-zinc-400 focus:ring-0 w-32 transition-colors">
                            <option value="">All Roles</option>
                            <?php foreach ( $role_labels as $key => $lbl ) : ?>
                                <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($lbl); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Branch Filter -->
                        <select id="filter-branch" onchange="filterActiveMembers()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 px-3 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer focus:border-zinc-400 focus:ring-0 w-32 transition-colors">
                            <option value="">All Branches</option>
                            <?php foreach ( $agency_branches as $b_id => $b ) : ?>
                                <option value="<?php echo esc_attr($b_id); ?>"><?php echo esc_html($b['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Status Filter -->
                        <select id="filter-status" onchange="filterActiveMembers()" class="border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 px-3 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer focus:border-zinc-400 focus:ring-0 w-32 transition-colors">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <!-- Clear Filters Button (Desktop) -->
                        <button type="button" onclick="clearFilters()" class="hidden border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 px-3 text-xs text-zinc-500 hover:text-zinc-950 dark:hover:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-955 transition-colors focus:outline-none focus:ring-0 cursor-pointer" id="btn-clear-filters-desktop">Clear</button>
                    </div>
                </div>

                <!-- Member Count Badge -->
                <div class="flex items-center justify-end shrink-0 select-none">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider" id="member-count-badge"><?php echo count($users); ?> members total</span>
                </div>
            </div>

            <!-- Collapsible Mobile Filters Panel -->
            <div id="member-filter-panel" class="hidden md:hidden border-t border-zinc-200/60 dark:border-zinc-800/60 pt-3">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    <!-- Role Filter -->
                    <select id="filter-role-mobile" onchange="syncFilterAndRun('role')" class="bg-zinc-100/60 dark:bg-zinc-850/60 border-0 text-xs rounded-lg px-2 py-1.5 h-9 w-full cursor-pointer text-zinc-700 dark:text-zinc-300 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:ring-0 focus:outline-none transition-colors">
                        <option value="">All Roles</option>
                        <?php foreach ( $role_labels as $key => $lbl ) : ?>
                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($lbl); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Branch Filter -->
                    <select id="filter-branch-mobile" onchange="syncFilterAndRun('branch')" class="bg-zinc-100/60 dark:bg-zinc-850/60 border-0 text-xs rounded-lg px-2 py-1.5 h-9 w-full cursor-pointer text-zinc-700 dark:text-zinc-300 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:ring-0 focus:outline-none transition-colors">
                        <option value="">All Branches</option>
                        <?php foreach ( $agency_branches as $b_id => $b ) : ?>
                            <option value="<?php echo esc_attr($b_id); ?>"><?php echo esc_html($b['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Status Filter -->
                    <select id="filter-status-mobile" onchange="syncFilterAndRun('status')" class="bg-zinc-100/60 dark:bg-zinc-850/60 border-0 text-xs rounded-lg px-2 py-1.5 h-9 w-full cursor-pointer text-zinc-700 dark:text-zinc-300 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:ring-0 focus:outline-none transition-colors">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <!-- Clear Filters button -->
                    <button type="button" onclick="clearFilters()" class="hidden h-9 px-3 rounded-lg border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-500 hover:text-zinc-950 dark:hover:text-zinc-100 bg-white dark:bg-zinc-950 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors focus:ring-0 focus:outline-none cursor-pointer flex items-center justify-center gap-1" id="btn-clear-filters-mobile">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- ── MOBILE CARDS GRID (hidden on desktop) ── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 md:hidden">
            <?php foreach ( $users as $u ) :
                $u_role = ! empty( $u->roles ) ? $u->roles[0] : 'subscriber';
                $u_role_lbl = isset( $role_labels[$u_role] ) ? $role_labels[$u_role] : $u_role;
                $u_branch_id = get_user_meta( $u->ID, 'cora_branch_id', true );
                $norm_u_branch_key = 'branch_' . cora_normalize_branch_id( $u_branch_id );
                $u_branch_lbl = isset( $agency_branches[$norm_u_branch_key] ) ? $agency_branches[$norm_u_branch_key]['name'] : '—';
                $u_status = get_user_meta( $u->ID, 'cora_user_status', true ) ?: 'active';
                $u_joined = date( 'd M Y', strtotime( $u->user_registered ) );
                $avatar = get_user_meta( $u->ID, 'cora_avatar_url', true );
                $banner = get_user_meta( $u->ID, 'cora_profile_banner_url', true );
                
                $u_phone = get_user_meta( $u->ID, 'cora_phone', true );
                $u_specs = get_user_meta( $u->ID, 'cora_specializations', true ) ?: array();
                $u_split = get_user_meta( $u->ID, 'cora_commission_split', true ) ?: '70/30';
                $u_rate  = get_user_meta( $u->ID, 'cora_hourly_rate', true ) ?: '2500';
                $u_bank  = get_user_meta( $u->ID, 'cora_bank_upi', true ) ?: '';
                $u_bio   = get_user_meta( $u->ID, 'description', true ) ?: '';

                $user_payload = array(
                    'id'         => $u->ID,
                    'name'       => $u->display_name,
                    'email'      => $u->user_email,
                    'phone'      => $u_phone,
                    'role'       => $u_role,
                    'branch'     => $u_branch_id,
                    'status'     => $u_status,
                    'specs'      => (array) $u_specs,
                    'split'      => $u_split,
                    'rate'       => $u_rate,
                    'bank'       => $u_bank,
                    'bio'        => $u_bio,
                    'avatar'     => $avatar ? $avatar : '',
                    'banner'     => $banner ? $banner : '',
                    'ai_token_limit'    => intval( get_user_meta( $u->ID, 'cora_ai_token_limit', true ) ?: 0 ),
                    'mcp_allowed_tools' => get_user_meta( $u->ID, 'cora_mcp_allowed_tools', true ) ?: array(),
                    'stay_logged_in'    => get_user_meta( $u->ID, 'cora_stay_logged_in', true ) ?: 'no'
                );
                
                $name_initials = '';
                $words = explode( ' ', $u->display_name );
                foreach ( $words as $w ) $name_initials .= strtoupper( substr( $w, 0, 1 ) );
                $name_initials = substr( $name_initials, 0, 2 );
                $name_color = '#' . substr( md5( $u->display_name ), 0, 6 );
            ?>
                <div class="active-member-row bg-white dark:bg-zinc-900 border-0 rounded-xl p-2.5 flex items-center gap-2.5 transition-all cursor-pointer active:bg-zinc-100 dark:active:bg-zinc-800 focus:outline-none outline-none shadow-2xs hover:shadow-xs w-full max-w-[480px] mx-auto"
                    data-name="<?php echo esc_attr(strtolower($u->display_name)); ?>"
                    data-email="<?php echo esc_attr(strtolower($u->user_email)); ?>"
                    data-role="<?php echo esc_attr($u_role); ?>"
                    data-branch="<?php echo esc_attr($u_branch_id); ?>"
                    data-status="<?php echo esc_attr($u_status); ?>"
                    data-user="<?php echo esc_attr(wp_json_encode($user_payload)); ?>"
                    onclick="openEditUserDrawer(this)">
                    <!-- Avatar -->
                    <?php if ( ! empty($avatar) ) : ?>
                        <img src="<?php echo esc_url($avatar); ?>" class="w-8 h-8 rounded-full object-cover shrink-0">
                    <?php else : ?>
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs shrink-0 border border-zinc-100 dark:border-zinc-800/80" style="background-color: <?php echo esc_attr($name_color); ?>"><?php echo esc_html($name_initials); ?></div>
                    <?php endif; ?>
                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 justify-between">
                            <span class="font-bold text-xs text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html($u->display_name); ?></span>
                            <?php 
                            $status_classes = $u_status === 'active' 
                                ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-450' 
                                : 'bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-450';
                            $dot_color = $u_status === 'active' ? 'bg-emerald-500' : 'bg-red-500';
                            ?>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[9px] font-bold rounded-md whitespace-nowrap shrink-0 <?php echo $status_classes; ?>">
                                <span class="w-1 h-1 rounded-full <?php echo $dot_color; ?> inline-block"></span>
                                <?php echo esc_html(ucfirst($u_status)); ?>
                            </span>
                        </div>
                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate mt-0.5"><?php echo esc_html($u->user_email); ?></div>
                        <div class="flex flex-wrap items-center gap-1 mt-0.5">
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[9px] font-bold rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 whitespace-nowrap select-none">
                                <?php echo esc_html($u_role_lbl); ?>
                            </span>
                            <?php if ($u_branch_lbl && $u_branch_lbl !== '—') : ?>
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[9px] font-bold rounded bg-zinc-50 dark:bg-zinc-900 text-zinc-550 dark:text-zinc-400 whitespace-nowrap select-none border border-zinc-200 dark:border-zinc-800">
                                <?php echo esc_html($u_branch_lbl); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Chevron -->
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 dark:text-zinc-600 shrink-0"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ── DESKTOP: MEMBERS TABLE (hidden on mobile) ── -->
        <div class="hidden md:block bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-xs text-left" id="active-members-table">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-800/40">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Email Address</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Role</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Branch</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Joined Date</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        <?php foreach ( $users as $u ) :
                            $u_role = ! empty( $u->roles ) ? $u->roles[0] : 'subscriber';
                            $u_role_lbl = isset( $role_labels[$u_role] ) ? $role_labels[$u_role] : $u_role;
                            $u_branch_id = get_user_meta( $u->ID, 'cora_branch_id', true );
                            $norm_u_branch_key = 'branch_' . cora_normalize_branch_id( $u_branch_id );
                            $u_branch_lbl = isset( $agency_branches[$norm_u_branch_key] ) ? $agency_branches[$norm_u_branch_key]['name'] : '—';
                            $u_status = get_user_meta( $u->ID, 'cora_user_status', true ) ?: 'active';
                            $u_joined = date( 'd M Y', strtotime( $u->user_registered ) );
                            $avatar = get_user_meta( $u->ID, 'cora_avatar_url', true );
                            $banner = get_user_meta( $u->ID, 'cora_profile_banner_url', true );
                            
                            $u_phone = get_user_meta( $u->ID, 'cora_phone', true );
                            $u_specs = get_user_meta( $u->ID, 'cora_specializations', true ) ?: array();
                            $u_split = get_user_meta( $u->ID, 'cora_commission_split', true ) ?: '70/30';
                            $u_rate  = get_user_meta( $u->ID, 'cora_hourly_rate', true ) ?: '2500';
                            $u_bank  = get_user_meta( $u->ID, 'cora_bank_upi', true ) ?: '';
                            $u_bio   = get_user_meta( $u->ID, 'description', true ) ?: '';

                            $user_payload = array(
                                'id'         => $u->ID,
                                'name'       => $u->display_name,
                                'email'      => $u->user_email,
                                'phone'      => $u_phone,
                                'role'       => $u_role,
                                'branch'     => $u_branch_id,
                                'status'     => $u_status,
                                'specs'      => (array) $u_specs,
                                'split'      => $u_split,
                                'rate'       => $u_rate,
                                'bank'       => $u_bank,
                                'bio'        => $u_bio,
                                'avatar'     => $avatar ? $avatar : '',
                                'banner'     => $banner ? $banner : '',
                                'ai_token_limit'    => intval( get_user_meta( $u->ID, 'cora_ai_token_limit', true ) ?: 0 ),
                                'mcp_allowed_tools' => get_user_meta( $u->ID, 'cora_mcp_allowed_tools', true ) ?: array(),
                                'stay_logged_in'    => get_user_meta( $u->ID, 'cora_stay_logged_in', true ) ?: 'no'
                            );
                            
                            $name_initials = '';
                            $words = explode( ' ', $u->display_name );
                            foreach ( $words as $w ) $name_initials .= strtoupper( substr( $w, 0, 1 ) );
                            $name_initials = substr( $name_initials, 0, 2 );
                            $name_color = '#' . substr( md5( $u->display_name ), 0, 6 );
                        ?>
                            <tr class="hover:bg-zinc-50/20 active-member-row" data-name="<?php echo esc_attr(strtolower($u->display_name)); ?>" data-email="<?php echo esc_attr(strtolower($u->user_email)); ?>" data-role="<?php echo esc_attr($u_role); ?>" data-branch="<?php echo esc_attr($u_branch_id); ?>" data-status="<?php echo esc_attr($u_status); ?>">
                                <td class="px-5 py-3 flex items-center gap-3">
                                    <?php if ( ! empty( $avatar ) ) : ?>
                                        <img src="<?php echo esc_url($avatar); ?>" class="w-8 h-8 rounded-full object-cover border border-zinc-200">
                                    <?php else : ?>
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs border border-zinc-200" style="background-color: <?php echo esc_attr($name_color); ?>">
                                            <?php echo esc_html( $name_initials ); ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $u->display_name ); ?></span>
                                </td>
                                <td class="px-5 py-3 text-zinc-500 font-medium"><?php echo esc_html( $u->user_email ); ?></td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 whitespace-nowrap select-none">
                                        <?php echo esc_html($u_role_lbl); ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-semibold text-zinc-800 dark:text-zinc-200"><?php echo esc_html($u_branch_lbl); ?></td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded-md select-none <?php echo $u_status === 'active' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-450' : 'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-450'; ?>">
                                        <?php echo esc_html(ucfirst($u_status)); ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-zinc-400 font-medium"><?php echo esc_html($u_joined); ?></td>
                                <td class="px-5 py-3 text-right">
                                    <button data-user="<?php echo esc_attr( wp_json_encode( $user_payload ) ); ?>" onclick="openEditUserDrawer(this)" class="cora-edit-user-btn px-2.5 py-1 border border-zinc-200 dark:border-zinc-800 rounded-lg text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer shadow-sm transition-colors">Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: PENDING INVITATIONS -->
    <div id="tab-pending-invites" class="cora-tab-content space-y-4 hidden">
        <div class="bg-white border border-zinc-200/85 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-xs text-left responsive-table">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-800/40">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Email Address</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Role</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Branch</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Expiry</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Status</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        <?php if ( empty( $pending_invites ) ) : ?>
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-zinc-400 dark:text-zinc-500 font-medium">No pending invitations.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $pending_invites as $tok => $inv ) :
                                $invite_role_lbl = isset( $role_labels[$inv['role']] ) ? $role_labels[$inv['role']] : $inv['role'];
                                $invite_branch_lbl = isset( $agency_branches[$inv['branch_id']] ) ? $agency_branches[$inv['branch_id']]['name'] : '—';
                                $expired = time() > intval( $inv['expires_at'] );
                                $status = $inv['status'];
                                if ( $status === 'pending' && $expired ) {
                                    $status = 'expired';
                                }
                                $expiry_date = date( 'd M Y, H:i', $inv['expires_at'] );
                            ?>
                                <tr class="hover:bg-zinc-50/10 dark:hover:bg-zinc-800/20 text-zinc-700 dark:text-zinc-300">
                                    <td class="px-5 py-3 font-bold text-zinc-900 dark:text-zinc-100" data-label="Name"><?php echo esc_html( $inv['first_name'] . ' ' . $inv['last_name'] ); ?></td>
                                    <td class="px-5 py-3 text-zinc-500 dark:text-zinc-400 font-medium" data-label="Email Address"><?php echo esc_html( $inv['email'] ); ?></td>
                                    <td class="px-5 py-3" data-label="Role">
                                        <span class="inline-flex items-center px-2 py-0.5 text-[9px] font-bold rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 whitespace-nowrap select-none">
                                            <?php echo esc_html($invite_role_lbl); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 font-semibold text-zinc-800 dark:text-zinc-200" data-label="Branch"><?php echo esc_html($invite_branch_lbl); ?></td>
                                    <td class="px-5 py-3 text-zinc-400 dark:text-zinc-500 font-medium" data-label="Expiry"><?php echo esc_html($expiry_date); ?></td>
                                    <td class="px-5 py-3" data-label="Status">
                                        <?php
                                        $status_classes = 'bg-zinc-100 dark:bg-zinc-800 text-zinc-550 dark:text-zinc-400';
                                        $dot_color = 'bg-zinc-400';
                                        if ( $status === 'accepted' ) {
                                            $status_classes = 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-450';
                                            $dot_color = 'bg-emerald-500';
                                        } elseif ( $status === 'pending' ) {
                                            $status_classes = 'bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-450';
                                            $dot_color = 'bg-amber-550';
                                        }
                                        ?>
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[9px] font-bold rounded-md whitespace-nowrap <?php echo $status_classes; ?>">
                                            <span class="w-1 h-1 rounded-full <?php echo $dot_color; ?> inline-block"></span>
                                            <?php echo esc_html(ucfirst($status)); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right" data-label="Actions">
                                        <div class="flex items-center justify-end gap-2">
                                            <?php if ( $status === 'pending' ) : ?>
                                                <button onclick="coraResendVerification('<?php echo esc_attr($inv['email']); ?>')" class="px-2 py-1 border border-zinc-200 dark:border-zinc-800 rounded text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer shadow-sm">Resend</button>
                                                <button onclick="coraCancelInvitation('<?php echo esc_attr($tok); ?>', this)" class="px-2 py-1 border border-zinc-200 dark:border-zinc-800 rounded text-[10px] font-bold text-red-655 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 hover:border-red-200 dark:hover:border-red-800 cursor-pointer transition-all shadow-sm">Cancel</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: PERMISSIONS MATRIX -->
    <div id="tab-permissions-matrix" class="cora-tab-content space-y-4 hidden">
        <div class="cora-card bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-5 shadow-sm space-y-4">
            <!-- Header Title Row -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 w-full pb-1">
                <div class="flex items-start gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 block">Granular Role Permissions Matrix</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 leading-relaxed">Control dashboard visibility and feature access for each workspace role. Super Admin permissions are locked globally.</p>
                    </div>
                </div>
                <div class="shrink-0 self-start sm:self-center">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-750 dark:text-emerald-450 text-[10px] font-bold border border-emerald-500/20 select-none">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live Sync Active
                    </div>
                </div>
            </div>

            <!-- Full-Width Search Input -->
            <div class="relative w-full">
                <input type="text" id="matrix-role-search" placeholder="Search roles..." class="w-full h-10 pl-9 pr-12 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-450 transition-all placeholder-zinc-400 dark:placeholder-zinc-500">
                <div class="absolute left-3 top-0 bottom-0 flex items-center pointer-events-none text-zinc-400">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <div class="absolute right-3 top-0 bottom-0 flex items-center pointer-events-none">
                    <kbd class="px-1.5 py-0.5 text-[9px] font-mono text-zinc-400 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700 select-none">⌘ K</kbd>
                </div>
            </div>

            <!-- Action Controls Toolbar Row -->
            <div class="flex items-center justify-between gap-3 flex-wrap pb-2 w-full pt-1">
                <!-- Left Action Buttons group -->
                <div class="flex items-center gap-2">
                    <!-- Reset Defaults Button -->
                    <button type="button" id="matrix-reset-defaults-btn" onclick="coraResetMatrixDefaults()" class="h-9 px-3 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs whitespace-nowrap">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                        <span>Reset</span>
                    </button>

                    <!-- Grant All Button -->
                    <button type="button" id="matrix-grant-all-btn" onclick="coraGrantAllSelectedRolePermissions()" class="h-9 px-3 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs whitespace-nowrap">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Grant All</span>
                    </button>

                    <!-- Configure AI Trainer Button -->
                    <button type="button" id="matrix-ai-trainer-btn" onclick="openAiTrainerDrawer('gemini')" class="h-9 px-3 text-xs font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs whitespace-nowrap">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0 text-amber-500"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                        <span>AI Trainer</span>
                    </button>
                </div>

                <!-- Right Save Matrix Button -->
                <button type="button" id="matrix-save-btn" onclick="coraSavePermissionsMatrix()" class="h-9 px-4 text-xs font-bold text-white dark:text-zinc-950 bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer active:scale-95 shadow-sm whitespace-nowrap">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    <span>Save Changes</span>
                </button>
            </div>
            </div>
            
            <?php
            $active_industry = cora_get_active_industry();
            if ( $active_industry === 'photography_studio' ) {
                $categories = array(
                    'CORE NAVIGATION' => array(
                        'dashboard' => 'Dashboard',
                        'bookings'  => 'Shoots',
                        'portfolio' => 'Portfolio',
                        'leads'     => 'Client Leads',
                    ),
                    'OPERATIONAL' => array(
                        'team-roles' => 'Team & Roles',
                        'equipment'  => 'Camera Gear',
                    ),
                    'SALES CHANNEL' => array(
                        'canvas'             => 'Canvas',
                        'forms'              => 'Forms',
                        'emails'             => 'Emails',
                        'review_acquisition' => 'Reviews',
                    ),
                    'ADMINISTRATIVE' => array(
                        'financials' => 'Financials',
                        'settings'   => 'Settings',
                    ),
                );
            } else {
                $categories = array(
                    'CORE NAVIGATION' => array(
                        'dashboard'   => 'Dashboard',
                        'bookings'    => 'Showings CRM',
                        'feature-hub' => 'Feature Hub',
                    ),
                    'OPERATIONAL' => array(
                        'team-roles' => 'Team & Roles',
                        'equipment'  => 'Property Listings',
                    ),
                    'SALES CHANNEL' => array(
                        'canvas'             => 'Canvas',
                        'forms'              => 'Forms',
                        'emails'             => 'Emails',
                        'review_acquisition' => 'Reviews',
                    ),
                    'ADMINISTRATIVE' => array(
                        'financials' => 'Financials',
                        'settings'   => 'Settings',
                    ),
                );
            }

            $matrix_columns = array();
            foreach ( $categories as $cat_label => $cat_cols ) {
                foreach ( $cat_cols as $col_key => $col_name ) {
                    $matrix_columns[$col_key] = $col_name;
                }
            }
            ?>

            <!-- Matrix Table Container with Sticky Left Column (Desktop) -->
            <div class="hidden md:block overflow-x-auto rounded-lg border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-xs text-left select-none border-collapse" id="cora-permissions-matrix-table">
                    <thead>
                        <!-- Row 1: Category Grouping Badges -->
                        <tr class="bg-zinc-100/70 dark:bg-zinc-800/50 border-b border-zinc-200/80 dark:border-zinc-800">
                            <th class="px-4 py-2 sticky left-0 z-20 bg-zinc-100/90 dark:bg-zinc-800/90 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider text-left shadow-[1px_0_0_0_rgba(0,0,0,0.06)] dark:shadow-[1px_0_0_0_rgba(255,255,255,0.08)]">
                                CATEGORIES
                            </th>
                            <?php foreach ( $categories as $cat_label => $cat_cols ) : ?>
                                <th colspan="<?php echo count($cat_cols); ?>" class="px-3 py-1.5 text-center border-r last:border-r-0 border-zinc-200/80 dark:border-zinc-700/80 bg-zinc-200/40 dark:bg-zinc-800/80">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-extrabold tracking-wider bg-zinc-200/80 dark:bg-zinc-700/80 text-zinc-800 dark:text-zinc-200 shadow-2xs">
                                        <?php echo esc_html( $cat_label ); ?>
                                    </span>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                        <!-- Row 2: Feature Labels -->
                        <tr class="bg-zinc-50 dark:bg-zinc-850 border-b border-zinc-200 dark:border-zinc-800">
                            <th class="px-4 py-2.5 sticky left-0 z-20 bg-zinc-50 dark:bg-zinc-850 font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider text-[10px] shadow-[1px_0_0_0_rgba(0,0,0,0.06)] dark:shadow-[1px_0_0_0_rgba(255,255,255,0.08)]">
                                Role Title
                            </th>
                            <?php foreach ( $matrix_columns as $col_key => $col_lbl ) : ?>
                                <th class="px-3 py-2.5 font-bold text-zinc-600 dark:text-zinc-300 text-[10px] uppercase tracking-wider text-center border-r last:border-r-0 border-zinc-200/60 dark:border-zinc-800/60">
                                    <?php echo esc_html( $col_lbl ); ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 bg-white dark:bg-zinc-900">
                        <!-- Super Admin / Owner Row (Locked) -->
                        <tr class="group hover:bg-zinc-50/70 dark:hover:bg-zinc-850/50 transition-colors cora-matrix-row border-b border-zinc-100 dark:border-zinc-800/60" data-role="cora_super_admin" data-locked="true">
                            <td class="px-4 py-3 sticky left-0 z-10 bg-white dark:bg-zinc-900 group-hover:bg-zinc-50/70 dark:group-hover:bg-zinc-850/50 transition-colors shadow-[1px_0_0_0_rgba(0,0,0,0.06)] dark:shadow-[1px_0_0_0_rgba(255,255,255,0.08)]">
                                <div class="flex items-center gap-2.5">
                                    <span class="font-bold text-xs text-zinc-900 dark:text-zinc-100 cora-role-title-text">Super Admin</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 shadow-2xs">
                                        <svg class="w-2.5 h-2.5 text-zinc-500 dark:text-zinc-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        Global System Lock
                                    </span>
                                </div>
                            </td>
                            <?php foreach ( $matrix_columns as $col_key => $col_lbl ) : ?>
                                <td class="text-center py-2 px-4 border-r last:border-r-0 border-zinc-100 dark:border-zinc-800/40 text-zinc-400 dark:text-zinc-500 relative">
                                    <div class="flex items-center justify-center" title="Full Access Locked">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                    </div>
                                    <input type="checkbox" checked disabled data-feature="<?php echo esc_attr($col_key); ?>" class="cora-permission-checkbox absolute opacity-[0.01] w-[1px] h-[1px] cursor-not-allowed pointer-events-none" style="left:0; top:0; z-index:100;">
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <!-- Custom & Standard Roles -->
                        <?php 
                        $all_roles = cora_get_all_roles();
                        $cora_permissions = get_option( 'cora_role_permissions', array() );
                        $cora_custom_roles = get_option( 'cora_custom_roles', array() );
                        $cora_permission_levels = get_option( 'cora_role_permission_levels', array() );
                        if ( ! is_array( $cora_permission_levels ) ) { $cora_permission_levels = array(); }
                        if ( ! function_exists( 'cora_get_matrix_cell_badge' ) ) {
                            function cora_get_matrix_cell_badge( $arg1 = '', $arg2 = '', $arg3 = '' ) {
                                $level = 'none';
                                if ( ! empty( $arg3 ) ) {
                                    $level = $arg3;
                                } elseif ( ! empty( $arg1 ) && empty( $arg2 ) && empty( $arg3 ) ) {
                                    $level = $arg1;
                                }
                                $level = in_array( $level, array( 'view', 'edit', 'none' ), true ) ? $level : 'none';

                                $view_icon = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                                $edit_icon = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>';
                                $none_icon = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>';
                                $caret = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0 opacity-70"><polyline points="6 9 12 15 18 9"></polyline></svg>';

                                if ( $level === 'view' ) {
                                    return '<span class="cora-matrix-cell-badge inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-500/20 transition-all cursor-pointer shadow-2xs select-none hover:border-emerald-500/50">' . $view_icon . 'View' . $caret . '</span>';
                                } elseif ( $level === 'edit' ) {
                                    return '<span class="cora-matrix-cell-badge inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap bg-purple-50 text-purple-700 dark:bg-purple-950/30 dark:text-purple-400 border border-purple-500/20 transition-all cursor-pointer shadow-2xs select-none hover:border-purple-500/50">' . $edit_icon . 'Edit' . $caret . '</span>';
                                } else {
                                    return '<span class="cora-matrix-cell-badge inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap bg-zinc-100 text-zinc-650 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 transition-all cursor-pointer shadow-2xs select-none hover:border-zinc-400/50">' . $none_icon . 'No Access' . $caret . '</span>';
                                }
                            }
                        }

                        $active_ind = ! empty( $_COOKIE['cora_workspace_industry'] ) 
                            ? $_COOKIE['cora_workspace_industry'] 
                            : get_option( 'cora_workspace_industry', 'real_estate' );
                        $active_ind_clean = str_replace( '_', '-', strtolower( trim( $active_ind ) ) );
                        $is_studio_ind = $is_studio_mode;

                        $re_only_roles     = array('cora_branch_manager', 'cora_re_agent', 'cora_lead_coordinator');
                        $studio_only_roles = array('cora_studio_manager', 'cora_photographer', 'cora_videographer', 'cora_drone_pilot', 'cora_editor');

                        $target_roles = array();
                        foreach ( $all_roles as $rk => $rl ) {
                            if ( $rk !== 'administrator' && $rk !== 'cora_shruti' && $rk !== 'cora_super_admin' ) {
                                if ( $is_studio_ind && in_array( $rk, $re_only_roles, true ) ) {
                                    continue;
                                }
                                if ( ! $is_studio_ind && in_array( $rk, $studio_only_roles, true ) ) {
                                    continue;
                                }
                                $target_roles[$rk] = $rl;
                            }
                        }
                        
                        foreach ($target_roles as $role_key => $role_name): 
                            $allowed_features = isset($cora_permissions[$role_key]) ? $cora_permissions[$role_key] : array();
                            
                            // Determine Access Level Badge
                            $access_badge_label = 'Contributor';
                            $access_badge_class = 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 font-semibold';
                            
                            if ( isset( $cora_custom_roles[$role_key] ) ) {
                                $cdef = $cora_custom_roles[$role_key];
                                $lvl = isset($cdef['access_level']) ? $cdef['access_level'] : 'contributor';
                                if ($lvl === 'manager') {
                                    $access_badge_label = 'Manager';
                                    $access_badge_class = 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-bold';
                                } elseif ($lvl === 'read_only') {
                                    $access_badge_label = 'Read-Only';
                                    $access_badge_class = 'bg-zinc-100/80 text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-750 font-semibold';
                                } else {
                                    $access_badge_label = 'Custom';
                                }
                            } else {
                                if ( in_array($role_key, array('cora_branch_manager', 'editor')) ) {
                                    $access_badge_label = 'Manager';
                                    $access_badge_class = 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950 font-bold';
                                } elseif ( in_array($role_key, array('cora_viewer', 'subscriber')) ) {
                                    $access_badge_label = 'Read-Only';
                                    $access_badge_class = 'bg-zinc-100/80 text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-750 font-semibold';
                                }
                            }
                        ?>
                        <tr class="group hover:bg-zinc-50/70 dark:hover:bg-zinc-850/50 transition-colors cora-matrix-row cursor-pointer border-b border-zinc-100 dark:border-zinc-800/60" data-role="<?php echo esc_attr($role_key); ?>">
                            <td class="px-4 py-3 sticky left-0 z-10 bg-white dark:bg-zinc-900 group-hover:bg-zinc-50/70 dark:group-hover:bg-zinc-850/50 transition-colors shadow-[1px_0_0_0_rgba(0,0,0,0.06)] dark:shadow-[1px_0_0_0_rgba(255,255,255,0.08)]">
                                <div class="flex items-center justify-between gap-3 pr-2">
                                    <span class="font-semibold text-xs text-zinc-900 dark:text-zinc-100 cora-role-title-text"><?php echo esc_html($role_name); ?></span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] shadow-2xs <?php echo esc_attr($access_badge_class); ?>">
                                        <?php echo esc_html($access_badge_label); ?>
                                    </span>
                                </div>
                            </td>
                            <?php foreach ($matrix_columns as $feature_key => $feature_label): 
                                $level = isset( $cora_permission_levels[$role_key][$feature_key] ) 
                                    ? $cora_permission_levels[$role_key][$feature_key] 
                                    : ( in_array( $feature_key, $allowed_features, true ) ? 'edit' : 'none' );
                                $is_checked = ( $level !== 'none' ) ? 'checked' : '';
                            ?>
                            <td class="text-center py-2 px-4 border-r last:border-r-0 border-zinc-100 dark:border-zinc-800/40 relative" data-role="<?php echo esc_attr($role_key); ?>" data-feature="<?php echo esc_attr($feature_key); ?>" data-level="<?php echo esc_attr($level); ?>">
                                 <?php echo cora_get_matrix_cell_badge($role_key, $feature_key, $level); ?>
                                 <input type="checkbox" <?php echo $is_checked; ?> data-feature="<?php echo esc_attr($feature_key); ?>" class="cora-permission-checkbox absolute opacity-[0.01] w-[1px] h-[1px] cursor-pointer" style="left:0; top:0; z-index:100; pointer-events:auto;">
                             </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Permissions Matrix Stacked Cards (Vertical Stack, No Horizontal Scroll) -->
            <div class="md:hidden space-y-4">
                <div class="grid grid-cols-1 gap-4 w-full">
                    <?php 
                    $role_metadata = array(
                        'cora_branch_manager' => array(
                            'desc' => 'Full access to operational and sales data.',
                            'color' => 'indigo',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
                        ),
                        'cora_studio_manager' => array(
                            'desc' => 'Full access to studio ops, dispatch, and financials.',
                            'color' => 'indigo',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
                        ),
                        'cora_re_agent' => array(
                            'desc' => 'Operational access with client listing management.',
                            'color' => 'indigo',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>'
                        ),
                        'cora_photographer' => array(
                            'desc' => 'Shoot calendars, listings, and equipment check-ins.',
                            'color' => 'indigo',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>'
                        ),
                        'cora_viewer' => array(
                            'desc' => 'Read-only access to essential data.',
                            'color' => 'indigo',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>'
                        ),
                        'editor' => array(
                            'desc' => 'Full editorial and operational access.',
                            'color' => 'indigo',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>'
                        ),
                        'contributor' => array(
                            'desc' => 'Limited collaborative access.',
                            'color' => 'emerald',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>'
                        ),
                        'subscriber' => array(
                            'desc' => 'Default read-only client access.',
                            'color' => 'zinc',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>'
                        )
                    );

                    $category_details = array(
                        'CORE NAVIGATION' => array(
                            'subtitle' => 'Access to primary navigation and dashboard modules',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>'
                        ),
                        'OPERATIONAL' => array(
                            'subtitle' => 'Access to operational tools and workflows',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
                        ),
                        'SALES CHANNEL' => array(
                            'subtitle' => 'Access to sales channel and customer data',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>'
                        ),
                        'ADMINISTRATIVE' => array(
                            'subtitle' => 'Access to administrative and settings',
                            'icon' => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'
                        )
                    );

                    $role_idx = 0;
                    foreach ($target_roles as $role_key => $role_name): 
                        $role_idx++;
                        $is_role_collapsed = ($role_idx > 2); // Collapse all roles after the first two
                        
                        $allowed_features = isset($cora_permissions[$role_key]) ? $cora_permissions[$role_key] : array();
                        
                        // Default metadata
                        $desc = 'Custom configured workspace role.';
                        $color = 'indigo';
                        $icon_html = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>';
                        
                        if ( isset( $role_metadata[$role_key] ) ) {
                            $desc = $role_metadata[$role_key]['desc'];
                            $color = $role_metadata[$role_key]['color'];
                            $icon_html = $role_metadata[$role_key]['icon'];
                        }
                        
                        // Access level badge
                        $access_badge_label = 'Contributor';
                        if ( isset( $cora_custom_roles[$role_key] ) ) {
                            $cdef = $cora_custom_roles[$role_key];
                            $lvl = isset($cdef['access_level']) ? $cdef['access_level'] : 'contributor';
                            if ($lvl === 'manager') {
                                $access_badge_label = 'Manager';
                            } elseif ($lvl === 'read_only') {
                                $access_badge_label = 'Read-Only';
                            }
                        } else {
                            if ( in_array($role_key, array('cora_branch_manager', 'editor')) ) {
                                $access_badge_label = 'Manager';
                            } elseif ( in_array($role_key, array('cora_viewer', 'subscriber')) ) {
                                $access_badge_label = 'Read-Only';
                            }
                        }

                        // Colors for the access level dropdown select
                        $select_theme_classes = '';
                        if ( $access_badge_label === 'Manager' ) {
                            $select_theme_classes = 'text-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/20 dark:text-indigo-400 border-indigo-100 dark:border-indigo-900/40';
                        } elseif ( $access_badge_label === 'Read-Only' ) {
                            $select_theme_classes = 'text-blue-600 bg-blue-50/50 dark:bg-blue-950/20 dark:text-blue-400 border-blue-100 dark:border-blue-900/40';
                        } else {
                            $select_theme_classes = 'text-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/40';
                        }
                    ?>
                        <div class="w-full bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 shadow-2xs flex flex-col justify-between" data-mobile-role="<?php echo esc_attr($role_key); ?>">
                            <div>
                                <!-- Header Row -->
                                <div class="flex items-center justify-between gap-3 pb-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                            <?php echo $icon_html; ?>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-xs text-zinc-900 dark:text-zinc-100 block truncate"><?php echo esc_html($role_name); ?></span>
                                            <span class="text-[10px] text-zinc-400 dark:text-zinc-500 block truncate mt-0.5"><?php echo esc_html($desc); ?></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <!-- Access Level Picker Dropdown -->
                                        <select onchange="coraChangeMobileRoleAccessLevel('<?php echo esc_attr($role_key); ?>', this.value)" class="h-8 px-2 pr-6 rounded-lg border text-xs font-semibold cursor-pointer focus:outline-none transition-all select-none appearance-none <?php echo $select_theme_classes; ?>" style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%234B5563\' stroke-width=\'2.5\'%3E%3Cpolyline points=\'6 9 12 15 18 9\'/%3E%3C/svg%3E'); background-position: right 6px center; background-size: 10px 10px; background-repeat: no-repeat;">
                                            <option value="manager" <?php selected($access_badge_label, 'Manager'); ?>>Manager</option>
                                            <option value="contributor" <?php selected($access_badge_label, 'Contributor'); ?>>Contributor</option>
                                            <option value="read_only" <?php selected($access_badge_label, 'Read-Only'); ?>>Read-Only</option>
                                        </select>
                                        
                                        <!-- Expand/Collapse Chevron -->
                                        <button type="button" onclick="coraToggleRoleCardAccordion('<?php echo esc_attr($role_key); ?>')" class="p-1 text-zinc-400 hover:text-zinc-655 dark:hover:text-zinc-300 transition-colors cursor-pointer focus:outline-none">
                                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" class="transition-transform duration-200 transform <?php echo $is_role_collapsed ? '' : 'rotate-180'; ?>" id="role-arrow-<?php echo $role_key; ?>"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Categories Accordion Area -->
                                <div id="role-categories-<?php echo $role_key; ?>" class="<?php echo $is_role_collapsed ? 'hidden' : ''; ?> space-y-2.5 mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                    <?php 
                                    $cat_idx = 0;
                                    foreach ( $categories as $cat_label => $cat_cols ) : 
                                        $cat_idx++;
                                        $accordion_id = "matrix-acc-{$role_key}-{$cat_idx}";
                                        
                                        // Calculate category status dynamically
                                        $has_edit = false;
                                        $has_view = false;
                                        $has_none = false;

                                        foreach ( $cat_cols as $feature_key => $feature_label ) {
                                            $m_level = isset( $cora_permission_levels[$role_key][$feature_key] ) 
                                                ? $cora_permission_levels[$role_key][$feature_key] 
                                                : ( in_array( $feature_key, $allowed_features, true ) ? 'edit' : 'none' );
                                            if ( $m_level === 'edit' ) {
                                                $has_edit = true;
                                            } elseif ( $m_level === 'view' ) {
                                                $has_view = true;
                                            } else {
                                                $has_none = true;
                                            }
                                        }

                                        $status_icon = '';
                                        if ( $has_edit && ! $has_view && ! $has_none ) {
                                            // All Edit: checkmark circle
                                            $status_icon = '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-500 shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
                                        } elseif ( ! $has_edit && ! $has_view && $has_none ) {
                                            // All None: slash circle
                                            $status_icon = '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>';
                                        } else {
                                            // Mixed/View: eye circle
                                            $status_icon = '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="text-blue-500 shrink-0"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                                        }
                                        
                                        $cat_meta = isset($category_details[$cat_label]) ? $category_details[$cat_label] : array('subtitle' => '', 'icon' => '');
                                    ?>
                                        <div class="border border-zinc-200/60 dark:border-zinc-800 rounded-xl overflow-hidden bg-zinc-50/40 dark:bg-zinc-850/40">
                                            <div class="w-full px-3 py-2.5 flex items-center justify-between transition-colors select-none">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                                                        <?php echo $cat_meta['icon']; ?>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 block truncate"><?php echo esc_html( ucfirst(strtolower($cat_label)) ); ?></span>
                                                        <span class="text-[10px] text-zinc-400 dark:text-zinc-500 block truncate mt-0.5"><?php echo esc_html($cat_meta['subtitle']); ?></span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 shrink-0">
                                                    <!-- Category Status Indicator -->
                                                    <?php echo $status_icon; ?>
                                                    
                                                    <!-- Expand/Collapse Chevron -->
                                                    <button type="button" onclick="coraToggleMatrixAccordion('<?php echo $accordion_id; ?>')" class="p-1 text-zinc-400 hover:text-zinc-650 dark:hover:text-zinc-300 focus:outline-none">
                                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="transition-transform duration-200 transform" id="arrow-<?php echo $accordion_id; ?>"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Feature Rows -->
                                            <div id="content-<?php echo $accordion_id; ?>" class="hidden px-3.5 py-2.5 space-y-2 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                                                <?php foreach ( $cat_cols as $feature_key => $feature_label ) : 
                                                    $m_level = isset( $cora_permission_levels[$role_key][$feature_key] ) 
                                                        ? $cora_permission_levels[$role_key][$feature_key] 
                                                        : ( in_array( $feature_key, $allowed_features, true ) ? 'edit' : 'none' );
                                                ?>
                                                    <div class="flex items-center justify-between gap-3 text-xs text-zinc-700 dark:text-zinc-300 select-none py-1.5 border-b border-zinc-100/50 dark:border-zinc-800/50 last:border-b-0">
                                                         <span class="font-medium"><?php echo esc_html($feature_label); ?></span>
                                                         <div class="mobile-matrix-cell cursor-pointer shrink-0" data-role="<?php echo esc_attr($role_key); ?>" data-feature="<?php echo esc_attr($feature_key); ?>" data-level="<?php echo esc_attr($m_level); ?>">
                                                             <?php echo cora_get_matrix_cell_badge($role_key, $feature_key, $m_level); ?>
                                                         </div>
                                                     </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                             </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 4: CUSTOM ROLES -->
    <div id="tab-custom-roles" class="cora-tab-content space-y-6 hidden">
        <!-- Header Bar with Title, Subtitle, and Primary CTA Button -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-700 dark:text-zinc-300"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    Custom Roles & Workspace Access
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Configure specialized team roles, assign module permission matrices, and set operational limits.</p>
            </div>
            <div>
                <button type="button" onclick="openCreateCustomRoleDrawer()" class="bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 font-bold text-xs px-4 py-2.5 rounded-lg transition-colors cursor-pointer active:scale-95 shadow-sm flex items-center gap-2 whitespace-nowrap">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Create Custom Role
                </button>
            </div>
        </div>

        <!-- Preset Role Quick-Clones / Template Cards Row -->
        <?php
        if ( $is_studio_mode ) {
            $role_templates = array(
                array(
                    'key'   => 'cora_studio_manager',
                    'title' => 'Studio Manager',
                    'badge' => 'Manager Access',
                    'desc'  => 'Shoot schedule oversight, crew dispatch, camera equipment vault, and studio financials.',
                    'tags'  => array( 'Shoots', 'Equipment', 'Financials', 'Crew' ),
                    'svg'   => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
                ),
                array(
                    'key'   => 'cora_photographer',
                    'title' => 'Photographer',
                    'badge' => 'Contributor',
                    'desc'  => 'Shoot calendar access, camera gear logs, media upload, & shift check-ins.',
                    'tags'  => array( 'Shoots', 'Camera Gear', 'Media' ),
                    'svg'   => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>'
                ),
                array(
                    'key'   => 'cora_editor',
                    'title' => 'Post-Production Editor',
                    'badge' => 'Contributor',
                    'desc'  => 'RAW media vault post-processing, retouching pipeline, proofing, & AI suite processing.',
                    'tags'  => array( 'Media Vault', 'AI Suite', 'Proofing' ),
                    'svg'   => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="2" y1="7" x2="7" y2="7"></line><line x1="2" y1="17" x2="7" y2="17"></line><line x1="17" y1="17" x2="22" y2="17"></line><line x1="17" y1="7" x2="22" y2="7"></line></svg>'
                ),
                array(
                    'key'   => 'cora_viewer',
                    'title' => 'Client Proofing Viewer',
                    'badge' => 'Read-Only',
                    'desc'  => 'Read-only gallery access for client photo selection, proofing approval, & invoice views.',
                    'tags'  => array( 'Galleries', 'Proofing', 'Invoices' ),
                    'svg'   => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
                ),
            );
        } else {
            $role_templates = array(
                array(
                    'key'   => 'cora_branch_manager',
                    'title' => 'Branch Manager',
                    'badge' => 'Manager Access',
                    'desc'  => 'Full office oversight, lead dispatch, team management, property listings, and financials.',
                    'tags'  => array( 'Buyer Leads', 'Listings', 'Financials', 'Team' ),
                    'svg'   => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
                ),
                array(
                    'key'   => 'cora_re_agent',
                    'title' => 'Real Estate Agent',
                    'badge' => 'Contributor',
                    'desc'  => 'Client buyer leads CRM, site showings calendar, listing inventory, & client task management.',
                    'tags'  => array( 'Buyer Leads', 'Site Showings', 'Listings' ),
                    'svg'   => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>'
                ),
                array(
                    'key'   => 'cora_re_assistant',
                    'title' => 'Showings Assistant',
                    'badge' => 'Contributor',
                    'desc'  => 'Property site visits, check-ins, client showings scheduling, & task checklists.',
                    'tags'  => array( 'Site Showings', 'Tasks', 'Attendance' ),
                    'svg'   => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
                ),
                array(
                    'key'   => 'cora_viewer',
                    'title' => 'Client / Investor Viewer',
                    'badge' => 'Read-Only',
                    'desc'  => 'Read-only portal access for property listings, contracts, and showing status.',
                    'tags'  => array( 'Listings', 'Contracts', 'Showings' ),
                    'svg'   => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
                ),
            );
        }
        ?>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-extrabold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">ROLE TEMPLATES &amp; QUICK STARTS</h3>
                <span class="text-xs text-zinc-400 font-medium">Click to launch pre-configured role drawer</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <?php foreach ( $role_templates as $tmpl ) : ?>
                    <div onclick="openCreateCustomRoleDrawer('<?php echo esc_attr( $tmpl['key'] ); ?>')" class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 md:p-5 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all cursor-pointer group flex flex-col justify-between min-h-[220px]">
                        <div>
                            <div class="flex items-center justify-between">
                                <div class="w-9 h-9 rounded-lg bg-zinc-100 dark:bg-zinc-800/80 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                                    <?php echo $tmpl['svg']; ?>
                                </div>
                                <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400"><?php echo esc_html( $tmpl['badge'] ); ?></span>
                            </div>
                            <div class="mt-3">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-zinc-950 dark:group-hover:text-white"><?php echo esc_html( $tmpl['title'] ); ?></h4>
                                <p class="text-xs text-zinc-400 dark:text-zinc-400 mt-1 line-clamp-2 leading-relaxed"><?php echo esc_html( $tmpl['desc'] ); ?></p>
                            </div>
                            <div class="flex flex-wrap gap-1.5 pt-3">
                                <?php foreach ( $tmpl['tags'] as $tag ) : ?>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-zinc-100/70 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400"><?php echo esc_html( $tag ); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs font-bold text-zinc-900 dark:text-zinc-100 group-hover:underline">
                            <span>Use Template</span>
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-800 dark:text-zinc-200"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Active Custom Roles Table (Full Width Overview) -->
        <div class="cora-card bg-white dark:bg-zinc-900 border border-zinc-200/85 dark:border-zinc-800 rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Active Custom Roles Overview</h3>
                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">Manage custom roles registered in this workspace environment, update feature matrices, or duplicate configurations.</p>
                </div>
                <?php
                $my_custom_roles = get_option( 'cora_custom_roles', array() );
                ?>
                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                    <?php echo count($my_custom_roles); ?> Custom Roles Registered
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-xs text-left">
                    <thead>
                        <tr class="bg-zinc-50/50 dark:bg-zinc-950/50">
                            <th class="px-4 py-2.5 font-bold text-zinc-550 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Role Name & Identifier</th>
                            <th class="px-4 py-2.5 font-bold text-zinc-550 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Feature Permission Tags</th>
                            <th class="px-4 py-2.5 font-bold text-zinc-550 dark:text-zinc-400 uppercase tracking-wider text-[10px]">Access Level & Quota</th>
                            <th class="px-4 py-2.5 font-bold text-zinc-550 dark:text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-150 dark:divide-zinc-800">
                        <?php if ( empty( $my_custom_roles ) ) : ?>
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-zinc-400">
                                <div class="space-y-1">
                                    <p class="font-medium text-xs">No custom roles defined yet.</p>
                                    <p class="text-[11px] text-zinc-400">Click "+ Create Custom Role" above or choose a preset template to get started.</p>
                                </div>
                            </td>
                        </tr>
                        <?php else : ?>
                            <?php foreach ( $my_custom_roles as $cr ) : 
                                $role_key     = $cr['role_key'];
                                $role_name    = $cr['role_name'];
                                $base_tmpl    = ! empty( $cr['base_template'] ) ? $cr['base_template'] : 'Custom';
                                $access_lbl   = ! empty( $cr['access_level'] ) ? ucfirst( str_replace( '_', ' ', $cr['access_level'] ) ) : 'Contributor';
                                $quota_txt    = isset( $cr['max_quota'] ) && $cr['max_quota'] !== '' && $cr['max_quota'] !== null ? $cr['max_quota'] . '/mo' : 'Unlimited';
                                $permissions  = ! empty( $cr['permissions'] ) && is_array( $cr['permissions'] ) ? $cr['permissions'] : ( isset( $cora_permissions[$role_key] ) ? $cora_permissions[$role_key] : array() );
                                
                                $json_payload = esc_attr( json_encode( array(
                                    'role_key'      => $role_key,
                                    'role_name'     => $role_name,
                                    'base_template' => $base_tmpl,
                                    'access_level'  => $cr['access_level'] ?? 'contributor',
                                    'max_quota'     => $cr['max_quota'] ?? '',
                                    'permissions'   => $permissions
                                ) ) );

                                // Feature tag mapping labels
                                $perm_labels = array(
                                    'crm_leads'         => $is_studio_mode ? 'Client Leads' : 'Buyer Leads',
                                    'crm'               => $is_studio_mode ? 'Client Leads' : 'Buyer Leads',
                                    'showings_bookings' => $is_studio_mode ? 'Shoots' : 'Showings',
                                    'bookings'          => $is_studio_mode ? 'Shoots' : 'Showings',
                                    'financials'        => 'Financials',
                                    'media_vault'       => 'Media',
                                    'media'             => 'Media',
                                    'vault'             => 'Media',
                                    'equipment'         => $is_studio_mode ? 'Camera Gear' : 'Property Listings',
                                    'ai_suite'          => 'AI Suite',
                                    'attendance'        => 'Attendance'
                                );
                            ?>
                                                        <tr class="hover:bg-zinc-50/30 dark:hover:bg-zinc-800/30">
                                <td class="px-4 py-3" data-label="Role Name & Identifier">
                                    <span class="font-semibold text-zinc-800 dark:text-zinc-200 block"><?php echo esc_html( $role_name ); ?></span>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <code class="text-zinc-500 dark:text-zinc-400 font-mono text-[10px] bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded"><?php echo esc_html( $role_key ); ?></code>
                                        <span class="text-[10px] text-zinc-400">Template: <?php echo esc_html( ucfirst( str_replace( 'cora_', '', $base_tmpl ) ) ); ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3" data-label="Feature Permission Tags">
                                    <div class="flex flex-wrap gap-1 max-w-xs md:justify-start justify-end">
                                        <?php if ( empty( $permissions ) ) : ?>
                                            <span class="text-[10px] text-zinc-400 italic">None assigned</span>
                                        <?php else : ?>
                                            <?php foreach ( $permissions as $p_val ) : 
                                                $tag_name = isset( $perm_labels[$p_val] ) ? $perm_labels[$p_val] : ucfirst( str_replace( '_', ' ', $p_val ) );
                                            ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60">
                                                    <?php echo esc_html( $tag_name ); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3" data-label="Access Level & Quota">
                                    <div class="flex items-center gap-1.5 md:justify-start justify-end">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                            <?php echo esc_html( $access_lbl ); ?>
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-50 dark:bg-zinc-950 text-zinc-600 dark:text-zinc-400 border border-zinc-200/50 dark:border-zinc-800">
                                            Quota: <?php echo esc_html( $quota_txt ); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right" data-label="Actions">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" data-custom-role="<?php echo $json_payload; ?>" class="cora-edit-custom-role-btn text-xs text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white font-semibold cursor-pointer transition-all flex items-center gap-1 px-2.5 py-1.5 rounded bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700">
                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            Edit Permissions
                                        </button>
                                        <button type="button" onclick="handleDuplicateCustomRole('<?php echo esc_attr( $role_key ); ?>')" title="Duplicate Role" class="cora-duplicate-custom-role-btn text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white cursor-pointer p-1.5 rounded hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        </button>
                                        <button type="button" onclick="handleDeleteCustomRole('<?php echo esc_attr( $role_key ); ?>', this)" title="Delete Role" class="text-red-655 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 cursor-pointer p-1.5 rounded hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 5: ATTENDANCE LOGS -->
<div id="tab-attendance-logs" class="cora-tab-content space-y-6 hidden">
    <?php
    $is_attendance_admin = in_array( $current_role, array( 'administrator', 'cora_shruti', 'cora_super_admin', 'cora_manager', 'cora_workspace_owner', 'owner', 'cora_re_broker_owner', 'cora_studio_owner' ) );
    
    $agency_id = cora_db_get_agency_id();
    $agency_slug = cora_get_current_user_agency_id();

    // Retrieve tenant-isolated geofencing settings
    $office_address  = get_option( 'cora_office_address_' . $agency_id, '' );
    if ( empty( $office_address ) && ! empty( $agency_slug ) ) {
        $office_address = get_option( 'cora_office_address_' . $agency_slug, '' );
    }

    $office_maps_url = get_option( 'cora_office_maps_url_' . $agency_id, '' );
    if ( empty( $office_maps_url ) && ! empty( $agency_slug ) ) {
        $office_maps_url = get_option( 'cora_office_maps_url_' . $agency_slug, '' );
    }

    $office_radius   = get_option( 'cora_geofence_radius_' . $agency_id, '' );
    if ( empty( $office_radius ) && ! empty( $agency_slug ) ) {
        $office_radius = get_option( 'cora_geofence_radius_' . $agency_slug, '' );
    }

    $office_lat      = get_option( 'cora_office_lat_' . $agency_id, '' );
    if ( empty( $office_lat ) && ! empty( $agency_slug ) ) {
        $office_lat = get_option( 'cora_office_lat_' . $agency_slug, '' );
    }

    $office_lng      = get_option( 'cora_office_lng_' . $agency_id, '' );
    if ( empty( $office_lng ) && ! empty( $agency_slug ) ) {
        $office_lng = get_option( 'cora_office_lng_' . $agency_slug, '' );
    }

    // Try tenant-isolated legacy locations first
    $legacy_loc = get_option( 'cora_attendance_office_location_' . $agency_id );
    if ( ( empty( $legacy_loc ) || ! is_array( $legacy_loc ) ) && ! empty( $agency_slug ) ) {
        $legacy_loc = get_option( 'cora_attendance_office_location_' . $agency_slug );
    }

    if ( ! empty( $legacy_loc ) && is_array( $legacy_loc ) ) {
        if ( empty( $office_address ) ) {
            $office_address = ! empty( $legacy_loc['address'] ) ? $legacy_loc['address'] : ( ! empty( $legacy_loc['office_name'] ) ? $legacy_loc['office_name'] : '' );
        }
        if ( empty( $office_maps_url ) && ! empty( $legacy_loc['maps_url'] ) ) {
            $office_maps_url = $legacy_loc['maps_url'];
        }
        if ( empty( $office_lat ) && ! empty( $legacy_loc['lat'] ) ) {
            $office_lat = $legacy_loc['lat'];
        }
        if ( empty( $office_lng ) && ! empty( $legacy_loc['lng'] ) ) {
            $office_lng = $legacy_loc['lng'];
        }
        if ( empty( $office_radius ) && ! empty( $legacy_loc['radius'] ) ) {
            $office_radius = $legacy_loc['radius'];
        }
    }

    // If still empty, fall back to global options
    if ( empty( $office_address ) && empty( $office_lat ) ) {
        $office_address  = get_option( 'cora_office_address', '' );
        $office_maps_url = get_option( 'cora_office_maps_url', '' );
        $office_radius   = get_option( 'cora_geofence_radius', 500 );
        $office_lat      = get_option( 'cora_office_lat', '' );
        $office_lng      = get_option( 'cora_office_lng', '' );

        // If global is also empty, try global legacy
        if ( empty( $office_address ) && empty( $office_lat ) ) {
            $global_legacy = get_option( 'cora_attendance_office_location', array() );
            if ( ! empty( $global_legacy ) && is_array( $global_legacy ) ) {
                $office_address = ! empty( $global_legacy['address'] ) ? $global_legacy['address'] : ( ! empty( $global_legacy['office_name'] ) ? $global_legacy['office_name'] : '' );
                $office_maps_url = ! empty( $global_legacy['maps_url'] ) ? $global_legacy['maps_url'] : '';
                $office_lat = ! empty( $global_legacy['lat'] ) ? $global_legacy['lat'] : '';
                $office_lng = ! empty( $global_legacy['lng'] ) ? $global_legacy['lng'] : '';
                $office_radius = ! empty( $global_legacy['radius'] ) ? $global_legacy['radius'] : 500;
            }
        }
    }

    if ( empty( $office_radius ) ) {
        $office_radius = 500;
    }

    $office_loc = array(
        'lat'      => $office_lat,
        'lng'      => $office_lng,
        'address'  => $office_address,
        'maps_url' => $office_maps_url,
        'radius'   => $office_radius ? intval( $office_radius ) : 500
    );
    ?>

    <?php if ( $is_attendance_admin ) : ?>
        <!-- ANALYTICS CARDS (Admin only) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 md:gap-4">
            <!-- Card 1: Total Active Today -->
            <div class="bg-white dark:bg-zinc-900 border-0 rounded-xl p-3 md:p-4 shadow-2xs flex items-center justify-between min-h-[90px]">
                <div class="space-y-1">
                    <span class="text-[9px] md:text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">ACTIVE TODAY</span>
                    <h3 class="text-lg md:text-xl font-extrabold text-zinc-900 dark:text-zinc-100" id="stat-active-today">0</h3>
                </div>
                <div class="p-1.5 bg-zinc-50 dark:bg-zinc-950 rounded-lg text-zinc-650 dark:text-zinc-350">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                </div>
            </div>
            
            <!-- Card 2: Late Check-ins -->
            <div class="bg-white dark:bg-zinc-900 border-0 rounded-xl p-3 md:p-4 shadow-2xs flex items-center justify-between min-h-[90px]">
                <div class="space-y-1">
                    <span class="text-[9px] md:text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">LATE CHECK-INS</span>
                    <h3 class="text-lg md:text-xl font-extrabold text-zinc-900 dark:text-zinc-100" id="stat-late-punches">0</h3>
                </div>
                <div class="p-1.5 bg-zinc-50 dark:bg-zinc-950 rounded-lg text-zinc-650 dark:text-zinc-350">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
            </div>

            <!-- Card 3: Interactive Office Geofence & Location Control Card -->
            <?php
            $geofence_status_txt = 'Not Configured';
            if ( ! empty( $office_loc['lat'] ) || ! empty( $office_loc['address'] ) || ! empty( $office_loc['maps_url'] ) ) {
                $r_val = intval( $office_loc['radius'] );
                $geofence_status_txt = ( $r_val >= 1000 ? ( $r_val / 1000 ) . 'km' : $r_val . 'm' ) . ' Enforced';
            }

            $current_address_txt = 'Not Configured';
            if ( ! empty( $office_loc['address'] ) ) {
                $current_address_txt = $office_loc['address'];
            } elseif ( ! empty( $office_loc['maps_url'] ) ) {
                $current_address_txt = $office_loc['maps_url'];
            } elseif ( ! empty( $office_loc['lat'] ) ) {
                $current_address_txt = $office_loc['lat'] . ', ' . $office_loc['lng'];
            }
            ?>
            <div onclick="openGeofenceDrawer()" class="bg-white dark:bg-zinc-900 border-0 rounded-xl p-3 md:p-4 shadow-2xs flex flex-col justify-between min-h-[90px] cursor-pointer hover:bg-zinc-50/50 dark:hover:bg-zinc-850/55 transition-colors group">
                <div class="space-y-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider truncate">GEOFENCING</span>
                        <span id="stat-geofence-status" class="inline-flex items-center px-1.5 py-0.5 text-[8px] md:text-[9px] font-bold rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60 shrink-0">
                            <?php echo esc_html( $geofence_status_txt ); ?>
                        </span>
                    </div>
                    <p id="cora-geofence-current-address" class="text-[10px] md:text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate" title="<?php echo esc_attr( $current_address_txt ); ?>">
                        <?php echo esc_html( $current_address_txt ); ?>
                    </p>
                </div>
                <div class="mt-2 flex items-center justify-between pt-1 border-t border-zinc-100 dark:border-zinc-800">
                    <span class="text-[9px] md:text-[10px] font-bold text-zinc-900 dark:text-zinc-100 group-hover:underline flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        Settings
                    </span>
                    <div class="p-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 group-hover:bg-zinc-200 dark:group-hover:bg-zinc-700 transition-colors">
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </div>
            </div>

            <!-- Card 4: Action / Email Test -->
            <div class="bg-white dark:bg-zinc-900 border-0 rounded-xl p-3 md:p-4 shadow-2xs flex flex-col justify-between min-h-[90px]">
                <div class="space-y-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider truncate">Cron Automations</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 text-[8px] md:text-[9px] font-bold rounded bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 shrink-0">Active</span>
                    </div>
                    <p class="text-[10px] md:text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate mt-1">Daily Reports &amp; Automated Alerts</p>
                </div>
                <div class="mt-2 flex items-center justify-between pt-1 border-t border-zinc-100 dark:border-zinc-800">
                    <span class="text-[9px] md:text-[10px] font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1">
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="9"></line><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="13" y2="17"></line></svg>
                        Test Runner
                    </span>
                    <div class="flex items-center gap-1.5">
                        <button onclick="triggerCronAction('admin_report')" class="text-[8px] md:text-[9px] px-1.5 py-0.5 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded font-bold text-zinc-700 dark:text-zinc-300 cursor-pointer transition-colors bg-white dark:bg-zinc-900 shadow-2xs">Report</button>
                        <button onclick="triggerCronAction('morning_reminder')" class="text-[8px] md:text-[9px] px-1.5 py-0.5 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded font-bold text-zinc-700 dark:text-zinc-300 cursor-pointer transition-colors bg-white dark:bg-zinc-900 shadow-2xs">Alert</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Full-Width Punch Log History Section -->
    <div class="space-y-4 w-full">
        <!-- Enhanced Attendance Filter Toolbar -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 md:p-4 shadow-sm flex flex-col gap-2.5 md:gap-3">
            <!-- Search bar & Mobile Toggle Row -->
            <div class="flex flex-row items-center gap-2 md:gap-3 w-full justify-between">
                <div class="flex flex-row items-center gap-2 flex-1 min-w-0">
                    <!-- Search Input -->
                    <div class="relative flex-1 max-w-full md:max-w-[180px]">
                        <input type="text" id="attendance-log-search" oninput="fetchAttendanceLogs()" class="w-full border-0 bg-zinc-100/60 dark:bg-zinc-850/60 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:outline-none rounded-lg h-9 pl-8 pr-3 text-xs text-zinc-900 dark:text-zinc-100 transition-colors" placeholder="Search employee...">
                        <div class="absolute left-2.5 top-0 bottom-0 flex items-center pointer-events-none text-zinc-400">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                    </div>

                    <!-- Mobile Filter Toggle Button -->
                    <button type="button" id="attendance-filter-toggle" onclick="toggleAttendanceMobileFilters()" class="md:hidden h-9 w-9 shrink-0 flex items-center justify-center rounded-lg border-0 bg-zinc-100/60 dark:bg-zinc-850/60 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-700/50 focus:outline-none transition-colors cursor-pointer" title="Toggle Filters">
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    </button>

                    <!-- Desktop Inline Filters -->
                    <div class="hidden md:flex items-center gap-2.5 flex-1 min-w-0">
                        <!-- Employee Picker Dropdown -->
                        <select id="attendance-filter-user" onchange="fetchAttendanceLogs()" class="h-9 px-2.5 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none transition-colors cursor-pointer shrink-0 max-w-[100px]" style="max-width: 100px; text-overflow: ellipsis;">
                            <option value="">All Team Members</option>
                            <?php foreach ( $users as $u ) : ?>
                                <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Period Direction Dropdown -->
                        <select id="attendance-filter-period" onchange="handlePeriodFilterChange()" class="h-9 px-2.5 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none transition-colors cursor-pointer shrink-0 max-w-[100px]" style="max-width: 100px; text-overflow: ellipsis;">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="custom">Custom Date Range</option>
                        </select>

                        <!-- Custom Date Range Inputs -->
                        <div id="attendance-custom-date-container" class="hidden flex items-center gap-1.5 shrink-0">
                            <input type="date" id="attendance-date-start" onchange="fetchAttendanceLogs()" class="h-9 px-2.5 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none transition-colors">
                            <span class="text-xs font-medium text-zinc-400">to</span>
                            <input type="date" id="attendance-date-end" onchange="fetchAttendanceLogs()" class="h-9 px-2.5 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none transition-colors">
                        </div>

                        <!-- Event Type Dropdown -->
                        <select id="attendance-filter-event" onchange="fetchAttendanceLogs()" class="h-9 px-2.5 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none transition-colors cursor-pointer shrink-0 max-w-[100px]" style="max-width: 100px; text-overflow: ellipsis;">
                            <option value="all">All Event Types</option>
                            <option value="in">Punch In</option>
                            <option value="out">Punch Out</option>
                        </select>
                    </div>
                </div>

                <!-- Desktop CTAs -->
                <div class="hidden md:flex items-center gap-2 justify-end shrink-0">
                    <button type="button" onclick="openAttendanceReportsDrawer()" class="h-9 px-3.5 bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 cursor-pointer shrink-0 shadow-sm">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        Automated Reports & Share
                    </button>
                    <button type="button" onclick="exportAttendanceCSV()" class="h-9 px-3 border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg text-xs font-bold text-zinc-800 dark:text-zinc-200 transition-colors flex items-center gap-1.5 cursor-pointer shrink-0">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Export CSV
                    </button>
                </div>
            </div>

            <!-- Collapsible Mobile Filters & Actions Panel -->
            <div id="attendance-filter-panel" class="hidden md:hidden border-t border-zinc-200/60 dark:border-zinc-800/60 pt-2.5 space-y-2.5">
                <div class="grid grid-cols-2 gap-2">
                    <!-- Employee Picker Dropdown (Mobile) -->
                    <select id="attendance-filter-user-mobile" onchange="syncAttendanceFilterAndRun('user')" class="bg-zinc-100/60 dark:bg-zinc-850/60 border-0 text-xs rounded-lg px-2.5 h-9 w-full cursor-pointer text-zinc-700 dark:text-zinc-300 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:outline-none transition-colors">
                        <option value="">All Team Members</option>
                        <?php foreach ( $users as $u ) : ?>
                            <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Period Direction Dropdown (Mobile) -->
                    <select id="attendance-filter-period-mobile" onchange="syncAttendanceFilterAndRun('period')" class="bg-zinc-100/60 dark:bg-zinc-850/60 border-0 text-xs rounded-lg px-2.5 h-9 w-full cursor-pointer text-zinc-700 dark:text-zinc-300 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:outline-none transition-colors">
                        <option value="all">All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="custom">Custom Date Range</option>
                    </select>
                </div>

                <!-- Custom Date Range Inputs (Mobile) -->
                <div id="attendance-custom-date-container-mobile" class="hidden flex items-center justify-between gap-1.5">
                    <input type="date" id="attendance-date-start-mobile" onchange="syncAttendanceFilterAndRun('date-start')" class="h-9 px-2 bg-zinc-100/60 dark:bg-zinc-850/60 border-0 text-xs rounded-lg text-zinc-900 dark:text-zinc-100 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:outline-none transition-colors flex-1">
                    <span class="text-xs font-medium text-zinc-400">to</span>
                    <input type="date" id="attendance-date-end-mobile" onchange="syncAttendanceFilterAndRun('date-end')" class="h-9 px-2 bg-zinc-100/60 dark:bg-zinc-850/60 border-0 text-xs rounded-lg text-zinc-900 dark:text-zinc-100 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:outline-none transition-colors flex-1">
                </div>

                <!-- Event Type Dropdown (Mobile) -->
                <select id="attendance-filter-event-mobile" onchange="syncAttendanceFilterAndRun('event')" class="bg-zinc-100/60 dark:bg-zinc-850/60 border-0 text-xs rounded-lg px-2.5 h-9 w-full cursor-pointer text-zinc-700 dark:text-zinc-300 focus:bg-zinc-100 dark:focus:bg-zinc-800 focus:outline-none transition-colors">
                    <option value="all">All Event Types</option>
                    <option value="in">Punch In</option>
                    <option value="out">Punch Out</option>
                </select>

                <!-- Mobile CTA Buttons Row -->
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-850">
                    <button type="button" onclick="openAttendanceReportsDrawer()" class="h-9 bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <span class="truncate">Reports & Share</span>
                    </button>
                    <button type="button" onclick="exportAttendanceCSV()" class="h-9 border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg text-xs font-bold text-zinc-800 dark:text-zinc-200 transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span class="truncate">Export CSV</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Table card wrapper -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/85 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-xs text-left responsive-table">
                    <thead class="bg-zinc-50/50 dark:bg-zinc-950">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">User Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Date & Time</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Event Type</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">GPS Coordinates</th>
                            <?php if ( $is_attendance_admin ) : ?>
                                <th class="px-5 py-3 font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[10px]">Geofence Status</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="cora-user-attendance-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800 text-zinc-700 dark:text-zinc-300">
                        <tr>
                            <td colspan="<?php echo $is_attendance_admin ? '5' : '4'; ?>" class="px-5 py-8 text-center text-zinc-400 dark:text-zinc-500 font-medium">Loading punch records...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>

    <!-- TAB 6: AUTOMATED OWNER DIGESTS & ALERTS -->
    <div id="tab-owner-automations" class="cora-tab-content space-y-4 hidden">
        <div class="cora-card bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 md:p-5 shadow-sm space-y-5">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-zinc-200/60 dark:border-zinc-800">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700 dark:text-zinc-300"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3zm-8.27 4a2 2 0 0 1-3.46 0"></path></svg>
                            Autonomous Workspace Owner Automations
                        </h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-500/20">
                            5 Active Workflows
                        </span>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1 leading-relaxed">Automated email reports, KPI digests, and security alerts delivered directly to the Workspace Owner account without manual checking.</p>
                </div>
            </div>

            <!-- Automations Grid / Table -->
            <?php
            $current_agency_id = get_user_meta( get_current_user_id(), 'cora_user_agency_id', true );
            if ( empty( $current_agency_id ) ) $current_agency_id = 1;
            
            $automation_items = array(
                array(
                    'key' => 'morning_brief',
                    'title' => 'Morning Operational Readiness Brief',
                    'schedule' => '08:30 AM Daily',
                    'desc' => 'Active staff roster, today\'s scheduled bookings/showings, and pending admin approvals.',
                    'badge' => 'Kickoff Brief',
                    'badge_cls' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400 border-blue-200 dark:border-blue-800'
                ),
                array(
                    'key' => 'midday_digest',
                    'title' => 'Mid-Day Anomaly & Exception Digest',
                    'schedule' => '14:00 PM Daily',
                    'desc' => 'Late check-ins, GPS geofence location violations, and unassigned lead alerts.',
                    'badge' => 'Guardrail Alert',
                    'badge_cls' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200 dark:border-amber-800'
                ),
                array(
                    'key' => 'evening_kpi',
                    'title' => 'Evening Business Intelligence Digest',
                    'schedule' => '18:00 PM Daily',
                    'desc' => 'Executive summary of daily revenue, billable hours vs scheduled, and team activity audit.',
                    'badge' => 'Executive KPI',
                    'badge_cls' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'
                ),
                array(
                    'key' => 'security_alert',
                    'title' => 'Security & Role Elevation Alert',
                    'schedule' => 'Real-Time Trigger',
                    'desc' => 'Immediate alert on custom role elevation, bulk permissions matrix saves, or login lockout thresholds.',
                    'badge' => 'Real-Time Guard',
                    'badge_cls' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border-rose-200 dark:border-rose-800'
                ),
                array(
                    'key' => 'weekly_payroll',
                    'title' => 'Weekly Payroll & Performance Digest',
                    'schedule' => 'Sunday 21:00 PM',
                    'desc' => '7-day timesheet compilation, regular vs overtime hours, and weekly performance highlights for Monday payroll.',
                    'badge' => 'Payroll Brief',
                    'badge_cls' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400 border-purple-200 dark:border-purple-800'
                ),
                array(
                    'key' => 'seo_drop_alert',
                    'title' => 'SEO & GEO Rank Drop Monitoring Alert',
                    'schedule' => 'Daily 10:00 AM Check',
                    'desc' => 'Instant alert when a critical, high-performing article drops in Google SERP rankings or loses visibility in AI answer engines.',
                    'badge' => 'Rank Watch',
                    'badge_cls' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800'
                ),
            );
            ?>

            <div class="space-y-3">
                <?php foreach ( $automation_items as $item ) : 
                    $is_enabled = cora_is_owner_automation_enabled( $item['key'], $current_agency_id );
                ?>
                    <div class="bg-zinc-50/70 dark:bg-zinc-950/60 border border-zinc-200/70 dark:border-zinc-800 rounded-xl p-3.5 md:p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 transition-colors hover:border-zinc-300 dark:hover:border-zinc-700">
                        <div class="space-y-1 flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-xs text-zinc-900 dark:text-zinc-100"><?php echo esc_html( $item['title'] ); ?></span>
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded border <?php echo esc_attr( $item['badge_cls'] ); ?>"><?php echo esc_html( $item['badge'] ); ?></span>
                                <span class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500">• <?php echo esc_html( $item['schedule'] ); ?></span>
                            </div>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed"><?php echo esc_html( $item['desc'] ); ?></p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 justify-end pt-2 md:pt-0 border-t md:border-t-0 border-zinc-100 dark:border-zinc-850">
                            <button type="button" onclick="coraTestDispatchAutomation('<?php echo esc_attr( $item['key'] ); ?>')" class="px-2.5 py-1.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-750 text-zinc-800 dark:text-zinc-200 rounded-lg text-[10px] font-bold transition-colors flex items-center gap-1 cursor-pointer shadow-2xs">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 2L11 13"></path><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                Test Send Now
                            </button>
                            
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" <?php echo $is_enabled ? 'checked' : ''; ?> onchange="coraToggleAutomation('<?php echo esc_attr( $item['key'] ); ?>', this.checked)" class="sr-only peer">
                                <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:after:border-zinc-600 peer-checked:bg-zinc-950 dark:peer-checked:bg-zinc-100"></div>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- ═══ OFFICE GEOFENCING DRAWER SHEET ═══════════════════════════════════════ -->
<aside id="cora-geofence-drawer" class="collapsed fixed top-0 right-0 z-[10000] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <!-- Header -->
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0">
        <div>
            <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700 dark:text-zinc-300"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                Office Location & Geofencing
            </h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Configure office address, map location link, and enforcement radius boundaries.</p>
        </div>
        <button onclick="closeGeofenceDrawer()" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" aria-label="Close drawer">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Drawer Body -->
    <div class="p-5 overflow-y-auto flex-1 space-y-5">
        <!-- Dual Mode Options -->
        <div>
            <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Location Entry Mode</label>
            <div class="grid grid-cols-2 gap-1.5 p-1 bg-zinc-100 dark:bg-zinc-950 rounded-lg border border-zinc-200/60 dark:border-zinc-800">
                <button type="button" id="geofence-mode-address-btn" onclick="switchGeofenceMode('address')" class="geofence-mode-tab py-1.5 px-3 rounded-md text-xs font-semibold transition-all cursor-pointer text-center bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-sm">
                    Address Search
                </button>
                <button type="button" id="geofence-mode-url-btn" onclick="switchGeofenceMode('url')" class="geofence-mode-tab py-1.5 px-3 rounded-md text-xs font-semibold transition-all cursor-pointer text-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
                    Google Maps Link
                </button>
            </div>
        </div>

        <!-- Address Search Container -->
        <div id="geofence-address-container" class="space-y-1.5">
            <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Office Address</label>
            <div class="relative">
                <input type="text" id="geofence-address-input" oninput="updateMapPreviewFromInput()" placeholder="Enter office street address, landmark, or city..." class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 pl-9 pr-9 text-xs bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none transition-colors">
                <div class="absolute left-3 top-0 bottom-0 flex items-center pointer-events-none text-zinc-400">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <button type="button" onclick="detectCurrentLocationForGeofence(event)" title="Detect current location" class="absolute right-3 top-0 bottom-0 flex items-center text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors cursor-pointer border-none bg-transparent p-0">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line></svg>
                </button>
            </div>
        </div>

        <!-- Google Maps Link Container -->
        <div id="geofence-url-container" class="space-y-1.5 hidden">
            <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Google Maps Share URL</label>
            <div class="relative">
                <input type="url" id="geofence-maps-url-input" oninput="updateMapPreviewFromInput()" placeholder="https://maps.app.goo.gl/... or google.com/maps/@..." class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg h-9 pl-9 pr-3 text-xs bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none transition-colors">
                <div class="absolute left-3 top-0 bottom-0 flex items-center pointer-events-none text-zinc-400">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                </div>
            </div>
            <p class="text-[10px] text-zinc-400 dark:text-zinc-500">Paste any Google Maps link or coordinates URL.</p>
        </div>

        <!-- Radius Selector Pills -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Geofence Radius Boundary</label>
                <span id="geofence-selected-radius-label" class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300">500m</span>
            </div>
            <div class="grid grid-cols-4 gap-2" id="geofence-radius-pills">
                <button type="button" data-radius="250" onclick="selectGeofenceRadius(250)" class="geofence-radius-pill py-2 px-3 rounded-lg border text-xs font-bold transition-all text-center cursor-pointer border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800">250m</button>
                <button type="button" data-radius="500" onclick="selectGeofenceRadius(500)" class="geofence-radius-pill py-2 px-3 rounded-lg border text-xs font-bold transition-all text-center cursor-pointer bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 border-zinc-950 dark:border-zinc-100 shadow-sm">500m</button>
                <button type="button" data-radius="1000" onclick="selectGeofenceRadius(1000)" class="geofence-radius-pill py-2 px-3 rounded-lg border text-xs font-bold transition-all text-center cursor-pointer border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800">1km</button>
                <button type="button" data-radius="2000" onclick="selectGeofenceRadius(2000)" class="geofence-radius-pill py-2 px-3 rounded-lg border text-xs font-bold transition-all text-center cursor-pointer border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800">2km</button>
            </div>
            <input type="hidden" id="geofence-radius-input" value="500">
        </div>

        <!-- Interactive Map Preview Container -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Map Pin & Boundary Preview</label>
                <span class="text-[10px] text-zinc-400 dark:text-zinc-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live Preview
                </span>
            </div>
            <div class="w-full h-52 rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-950 relative">
                <iframe id="geofence-map-frame" class="w-full h-full border-0" loading="lazy" allowfullscreen src="about:blank"></iframe>
            </div>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 flex items-center justify-end gap-2 shrink-0">
        <button type="button" onclick="closeGeofenceDrawer()" class="px-4 py-2 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
            Cancel
        </button>
        <button type="button" id="save-geofence-btn" onclick="handleSaveGeofence(event)" class="px-4 py-2 rounded-lg text-xs font-semibold bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-150 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 shadow-sm transition-all cursor-pointer flex items-center gap-1.5">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save Location & Geofence
        </button>
    </div>
</aside>

<!-- ═══ AUTOMATED ATTENDANCE REPORT & SHARE SIDE DRAWER SHEET ═════════════════ -->
<aside id="cora-attendance-reports-drawer" class="collapsed fixed top-0 right-0 z-[10000] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <!-- Drawer Header -->
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0">
        <div>
            <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700 dark:text-zinc-300"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Automated Attendance Reports & Share
            </h2>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Configure report parameters, export documents, and dispatch automated summary emails.</p>
        </div>
        <button type="button" onclick="closeAttendanceReportsDrawer()" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" aria-label="Close drawer">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Drawer Body -->
    <div class="p-5 overflow-y-auto flex-1 space-y-6">
        <!-- Section: Report Scope Controls -->
        <div class="space-y-4 bg-zinc-50/70 dark:bg-zinc-950/70 p-4 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Report Scope & Target</span>
                <span class="text-[10px] font-bold text-zinc-500 bg-zinc-200/60 dark:bg-zinc-800 px-2 py-0.5 rounded">Configuration</span>
            </div>

            <!-- Time Horizon Control -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Time Horizon</label>
                <select id="attendance-report-horizon" onchange="handleReportHorizonChange()" class="w-full h-9 px-3 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none transition-colors cursor-pointer">
                    <option value="daily">Daily Summary</option>
                    <option value="weekly">Weekly Report</option>
                    <option value="monthly">Monthly Payroll Sheet</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <!-- Custom Date Inputs (Drawer) -->
            <div id="attendance-report-custom-dates" class="hidden grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">Start Date</label>
                    <input type="date" id="attendance-report-start-date" class="w-full h-9 px-2.5 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1">End Date</label>
                    <input type="date" id="attendance-report-end-date" class="w-full h-9 px-2.5 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none">
                </div>
            </div>

            <!-- Employee Target Control -->
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Employee Target</label>
                <select id="attendance-report-target" class="w-full h-9 px-3 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none transition-colors cursor-pointer">
                    <option value="all">Entire Workspace (All Members)</option>
                    <?php foreach ( $users as $u ) : ?>
                        <option value="<?php echo esc_attr( $u->ID ); ?>">Specific Employee: <?php echo esc_html( $u->display_name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Section: Export & Action Blocks -->
        <div class="space-y-3">
            <span class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Export & Sharing Actions</span>

            <!-- Action Block 1: CSV Export -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex items-center justify-between gap-3 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                <div class="space-y-0.5 min-w-0">
                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                        Download CSV Export
                    </h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Export raw timestamp logs and geofence data into a standard CSV file.</p>
                </div>
                <button type="button" onclick="exportAttendanceCSV()" class="h-8 px-3 border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-750 text-zinc-850 dark:text-zinc-200 rounded-lg text-xs font-bold transition-colors cursor-pointer shrink-0">
                    Export
                </button>
            </div>

            <!-- Action Block 2: Printable / PDF Summary -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex items-center justify-between gap-3 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                <div class="space-y-0.5 min-w-0">
                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        Printable / PDF Summary
                    </h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Generate clean formatted print sheet with total hours and verification stats.</p>
                </div>
                <button type="button" onclick="generatePrintableAttendanceReport()" class="h-8 px-3 border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-750 text-zinc-850 dark:text-zinc-200 rounded-lg text-xs font-bold transition-colors cursor-pointer shrink-0">
                    Print PDF
                </button>
            </div>

            <!-- Action Block 3: Send Email Digest -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm space-y-3 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                <div class="space-y-0.5">
                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        Send Email Digest
                    </h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Send automated HTML summary email to team lead or administrator.</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="email" id="attendance-report-email-recipient" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" placeholder="recipient@agency.com" class="flex-1 h-8 px-2.5 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:border-zinc-400 focus:outline-none">
                    <button type="button" onclick="sendAttendanceEmailDigest()" class="h-8 px-3 bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 rounded-lg text-xs font-bold transition-colors cursor-pointer shrink-0">
                        Send Email
                    </button>
                </div>
            </div>

            <!-- Action Block 4: Copy Secure Share Link -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex items-center justify-between gap-3 hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                <div class="space-y-0.5 min-w-0">
                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                        Copy Secure Share Link
                    </h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Generate read-only share token link for external payroll or audit access.</p>
                </div>
                <button type="button" onclick="copyAttendanceShareLink()" class="h-8 px-3 border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-750 text-zinc-850 dark:text-zinc-200 rounded-lg text-xs font-bold transition-colors cursor-pointer shrink-0">
                    Copy Link
                </button>
            </div>
        </div>
    </div>
</aside>

<!-- ═══ INVITE USER DRAWER SHEET ═════════════════════════════════════════════ -->
<aside id="cora-invite-user-drawer" class="collapsed fixed top-0 right-0 z-[10000] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-950/50 shrink-0">
        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Invite Brokerage Member</h3>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer p-1" onclick="closeInviteDrawer()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    
    <form onsubmit="handleSendInvite(event)" class="flex-1 overflow-y-auto p-6 space-y-5">
        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">First Name</label>
            <input type="text" id="invite-first-name" required placeholder="e.g. Vikas" class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
        </div>
        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Last Name</label>
            <input type="text" id="invite-last-name" required placeholder="e.g. Mehta" class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
        </div>
        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Email Address</label>
            <input type="email" id="invite-email" required placeholder="name@agency.com" class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
        </div>
        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Operational Role</label>
            <select id="invite-role" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                <?php
                foreach ( $role_labels as $role_key => $role_label ) {
                    if ( $role_key === 'cora_super_admin' ) {
                        continue;
                    }
                    if ( $role_key === 'cora_branch_manager' ) {
                        $can_invite_bm = ( $current_role === 'administrator' || $current_role === 'cora_manager' || $current_role === 'cora_super_admin' || $current_role === 'cora_workspace_owner' || $current_role === 'owner' || cora_is_super_owner() );
                        if ( ! $can_invite_bm ) {
                            continue;
                        }
                    }
                    if ( $role_key === 'cora_manager' ) {
                        $is_owner = in_array( $current_role, array( 'cora_super_admin', 'cora_workspace_owner', 'owner', 'cora_re_broker_owner', 'cora_studio_owner' ), true );
                        if ( ! cora_is_real_shruti() && $current_role !== 'cora_super_admin' && $current_role !== 'administrator' && ! cora_is_super_owner() && ! $is_owner ) {
                            continue;
                        }
                    }
                    echo '<option value="' . esc_attr( $role_key ) . '">' . esc_html( $role_label ) . '</option>';
                }
                ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Assign Branch</label>
            <select id="invite-branch" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer" <?php echo ! empty( $current_branch ) ? 'disabled' : ''; ?>>
                <?php foreach ( $agency_branches as $b_id => $b ) : ?>
                    <option value="<?php echo esc_attr($b_id); ?>" <?php selected( $current_branch, $b_id ); ?>><?php echo esc_html($b['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="pt-4">
            <button type="submit" id="send-invite-btn" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-950 font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Send Invitation Link</button>
        </div>
    </form>
</aside>

<!-- ═══ EDIT USER DRAWER SHEET ═══════════════════════════════════════════════ -->
<aside id="cora-edit-user-drawer" class="collapsed fixed top-0 right-0 z-[10000] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
        <!-- Mobile pull-down handle -->
        <div class="md:hidden flex justify-center pt-2 pb-0 cursor-pointer" onclick="closeEditUserDrawer()">
            <div class="w-10 h-1 rounded-full bg-zinc-300 dark:bg-zinc-600"></div>
        </div>
        <!-- Header -->
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-950/50">
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100" id="edit-user-title">Edit Team Member</h3>
                <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5" id="edit-user-subtitle">Manage account details, role specializations, and compensation.</p>
            </div>
            <button type="button" id="edit-drawer-close-btn" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer p-2 -mr-1 min-w-[40px] min-h-[40px] flex items-center justify-center" onclick="closeEditUserDrawer()">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Drawer Sub-Tabs -->
        <div class="flex items-center gap-1 border-b border-zinc-200 dark:border-zinc-800 px-5 pt-2 bg-zinc-50/30 dark:bg-zinc-950/30 shrink-0 select-none">
            <button type="button" class="drawer-edit-tab active px-3 py-2 text-xs font-bold border-b-2 border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 transition-colors cursor-pointer" data-drawer-tab="tab-edit-general">General</button>
            <button type="button" class="drawer-edit-tab px-3 py-2 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200 transition-colors cursor-pointer" data-drawer-tab="tab-edit-specializations">Role & Tags</button>
            <button type="button" class="drawer-edit-tab px-3 py-2 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200 transition-colors cursor-pointer" data-drawer-tab="tab-edit-financials">Financials</button>
            <button type="button" class="drawer-edit-tab px-3 py-2 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200 transition-colors cursor-pointer" data-drawer-tab="tab-edit-actions">Actions</button>
            <button type="button" class="drawer-edit-tab px-3 py-2 text-xs font-medium border-b-2 border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200 transition-colors cursor-pointer" data-drawer-tab="tab-edit-ai-security">
                <span class="flex items-center gap-1"><svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a4 4 0 0 1 4 4v1a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3H8a3 3 0 0 1-3-3v-8a3 3 0 0 1 3-3V6a4 4 0 0 1 4-4z"></path><circle cx="12" cy="14" r="2"></circle><path d="M12 16v2"></path></svg> AI & Security</span>
            </button>
        </div>

        <form onsubmit="handleSaveEditUser(event)" class="flex-1 overflow-y-auto flex flex-col justify-between">
            <input type="hidden" id="edit-user-id">

            <div class="p-6 space-y-5">
                <!-- TAB 1: GENERAL PROFILE -->
                <div id="tab-edit-general" class="drawer-tab-content space-y-4">
                    <!-- Profile Image & Banner -->
                    <div class="relative mb-2">
                        <!-- Banner -->
                        <div id="edit-banner-preview" class="w-full h-[100px] rounded-xl bg-zinc-100 dark:bg-zinc-800 relative overflow-hidden cursor-pointer group" onclick="coraSelectBanner()">
                            <img id="edit-banner-img" src="" alt="" class="w-full h-full object-cover hidden">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 dark:bg-zinc-900/90 rounded-lg px-3 py-1.5 flex items-center gap-1.5 shadow-sm">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-300"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                    <span class="text-[10px] font-medium text-zinc-600 dark:text-zinc-300">Change Cover</span>
                                </div>
                            </div>
                        </div>
                        <!-- Avatar -->
                        <div class="absolute -bottom-5 left-4 z-10">
                            <div id="edit-avatar-preview" class="w-16 h-16 rounded-full border-[3px] border-white dark:border-zinc-900 bg-zinc-200 dark:bg-zinc-700 overflow-hidden cursor-pointer group relative shadow-md" onclick="coraSelectAvatar()">
                                <img id="edit-avatar-img" src="" alt="" class="w-full h-full object-cover hidden">
                                <div id="edit-avatar-initials" class="w-full h-full flex items-center justify-center text-lg font-bold text-zinc-500 dark:text-zinc-400"></div>
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="white" stroke-width="2" fill="none" class="opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-md"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4">
                        <!-- Default Avatar Options -->
                        <label class="block text-[10px] font-medium text-zinc-400 dark:text-zinc-500 mb-1.5">Or pick a default avatar</label>
                        <div class="flex items-center gap-2" id="default-avatar-options">
                            <button type="button" class="default-avatar-btn w-9 h-9 rounded-full border-2 border-zinc-200 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-zinc-100 overflow-hidden transition-colors cursor-pointer bg-zinc-800 flex items-center justify-center" onclick="coraPickDefaultAvatar(this)" data-avatar-svg="1">
                                <svg viewBox="0 0 40 40" width="36" height="36"><circle cx="20" cy="15" r="7" fill="#a1a1aa"/><ellipse cx="20" cy="35" rx="13" ry="10" fill="#a1a1aa"/></svg>
                            </button>
                            <button type="button" class="default-avatar-btn w-9 h-9 rounded-full border-2 border-zinc-200 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-zinc-100 overflow-hidden transition-colors cursor-pointer bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center" onclick="coraPickDefaultAvatar(this)" data-avatar-svg="2">
                                <svg viewBox="0 0 40 40" width="36" height="36"><rect x="10" y="8" width="20" height="20" rx="6" fill="#71717a"/><circle cx="16" cy="17" r="2" fill="#fafafa"/><circle cx="24" cy="17" r="2" fill="#fafafa"/><path d="M15 23 Q20 27 25 23" stroke="#fafafa" stroke-width="1.5" fill="none"/></svg>
                            </button>
                            <button type="button" class="default-avatar-btn w-9 h-9 rounded-full border-2 border-zinc-200 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-zinc-100 overflow-hidden transition-colors cursor-pointer bg-zinc-900 flex items-center justify-center" onclick="coraPickDefaultAvatar(this)" data-avatar-svg="3">
                                <svg viewBox="0 0 40 40" width="36" height="36"><polygon points="20,5 35,30 5,30" fill="#52525b"/><circle cx="20" cy="20" r="5" fill="#d4d4d8"/></svg>
                            </button>
                            <button type="button" class="default-avatar-btn w-9 h-9 rounded-full border-2 border-zinc-200 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-zinc-100 overflow-hidden transition-colors cursor-pointer bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center" onclick="coraPickDefaultAvatar(this)" data-avatar-svg="4">
                                <svg viewBox="0 0 40 40" width="36" height="36"><rect x="8" y="8" width="24" height="24" rx="4" fill="#3f3f46"/><line x1="14" y1="18" x2="20" y2="18" stroke="#d4d4d8" stroke-width="2" stroke-linecap="round"/><line x1="14" y1="23" x2="26" y2="23" stroke="#d4d4d8" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                            <button type="button" class="default-avatar-btn w-9 h-9 rounded-full border-2 border-zinc-200 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-zinc-100 overflow-hidden transition-colors cursor-pointer bg-zinc-300 dark:bg-zinc-600 flex items-center justify-center" onclick="coraPickDefaultAvatar(this)" data-avatar-svg="5">
                                <svg viewBox="0 0 40 40" width="36" height="36"><circle cx="20" cy="20" r="14" fill="none" stroke="#52525b" stroke-width="3"/><circle cx="20" cy="20" r="6" fill="#52525b"/></svg>
                            </button>
                            <button type="button" class="default-avatar-btn w-9 h-9 rounded-full border-2 border-zinc-200 dark:border-zinc-700 hover:border-zinc-900 dark:hover:border-zinc-100 overflow-hidden transition-colors cursor-pointer bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center" onclick="coraPickDefaultAvatar(this)" data-avatar-svg="6">
                                <svg viewBox="0 0 40 40" width="36" height="36"><path d="M10 28 Q15 10 20 20 Q25 30 30 12" stroke="#71717a" stroke-width="3" fill="none" stroke-linecap="round"/></svg>
                            </button>
                            <button type="button" class="w-9 h-9 rounded-full border-2 border-dashed border-zinc-300 dark:border-zinc-600 hover:border-zinc-500 dark:hover:border-zinc-400 flex items-center justify-center cursor-pointer transition-colors" onclick="coraRemoveAvatar()" title="Remove avatar">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="edit-avatar-url" value="">
                    <input type="hidden" id="edit-banner-url" value="">

                    <div>
                        <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Display Name</label>
                        <input type="text" id="edit-display-name" required class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Phone Number</label>
                        <input type="tel" id="edit-phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Operational Role</label>
                        <select id="edit-role" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                            <?php
                            $all_roles_map = cora_get_all_roles();
                            if ( ! cora_is_real_shruti() ) {
                                unset( $all_roles_map['administrator'], $all_roles_map['cora_shruti'] );
                            }
                            foreach ( $all_roles_map as $r_key => $r_label ) {
                                echo '<option value="' . esc_attr( $r_key ) . '">' . esc_html( $r_label ) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Assign Branch</label>
                        <select id="edit-branch" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                            <?php foreach ( $agency_branches as $b_id => $b ) : ?>
                                <option value="<?php echo esc_attr($b_id); ?>"><?php echo esc_html($b['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Active / Inactive Status Switch -->
                    <div class="flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800 pt-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200">Account Status</label>
                            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">Deactivating instantly terminates all active sessions.</p>
                        </div>
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="edit-status-toggle" onchange="handleStatusToggleChange(this)" class="sr-only peer">
                            <div class="w-9 h-5 bg-zinc-200 dark:bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-zinc-950 dark:peer-checked:bg-zinc-150"></div>
                        </div>
                    </div>

                    <!-- Leads Reassignment Panel -->
                    <div id="leads-reassignment-panel" class="hidden bg-[#fafaf9] dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-4 space-y-3">
                        <div class="flex items-start gap-2 text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400 shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <div>
                                <h4 class="text-xs font-bold" id="leads-warning-title">Active Leads Pending Reassignment</h4>
                                <p class="text-[10px] text-zinc-550 dark:text-zinc-400 mt-0.5">This agent currently manages <span id="leads-count-warning" class="font-bold">0</span> open leads. Choose a teammate to reassign them to.</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider mb-1">Reassign To</label>
                            <select id="reassign-leads-to" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-2 py-1 text-xs bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 outline-none">
                                <option value="">Leave Unassigned (Mark as Unassigned)</option>
                                <?php foreach ( $users as $u ) : ?>
                                    <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: SPECIALIZATIONS & TAGS -->
                <div id="tab-edit-specializations" class="drawer-tab-content space-y-4 hidden">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Operational Specializations</label>
                        <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mb-3">Tag expertise areas for lead matching and shoot dispatching.</p>
                        
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <?php
                            $spec_options = $active_industry === 'photography_studio'
                                ? array('Portrait & Fashion', 'Commercial Photography', 'Drone Specialist', 'Post-Production Colorist', 'Wedding Cinematography', 'Event Coverage')
                                : array('Luxury Residential', 'Commercial Sales', 'Land Acquisition', 'Rental Management', 'Auction Specialist', 'Investment Advisory');
                            
                            foreach ($spec_options as $spec):
                            ?>
                                <label class="flex items-center gap-2 p-2 border border-zinc-200 dark:border-zinc-800 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-850 cursor-pointer text-zinc-800 dark:text-zinc-200 font-medium">
                                    <input type="checkbox" name="edit-specs[]" value="<?php echo esc_attr($spec); ?>" class="edit-spec-checkbox accent-zinc-950 dark:accent-zinc-150">
                                    <span class="text-[11px]"><?php echo esc_html($spec); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Internal Bio & Operational Notes</label>
                        <textarea id="edit-bio" rows="4" placeholder="Brief background, certifications, or internal brokerage notes..." class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100"></textarea>
                    </div>

                    <!-- AI Talent Matchmaker -->
                    <div class="pt-3 border-t border-zinc-200/60 dark:border-zinc-800/60">
                        <button type="button" onclick="coraAnalyzeProfileSkills()" id="ai-matchmaker-btn" class="w-full py-2.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg text-xs font-bold text-zinc-800 dark:text-zinc-200 transition-colors cursor-pointer flex items-center justify-center gap-2 border border-zinc-200/60 dark:border-zinc-700/60">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l2.09 6.26L20.18 9l-4.91 3.74L16.91 19 12 15.27 7.09 19l1.64-6.26L3.82 9l6.09-.74z"></path></svg>
                            AI Talent Matchmaker
                        </button>
                        <div id="ai-match-results" class="hidden mt-3 p-3 bg-zinc-50/80 dark:bg-zinc-900/50 border border-zinc-200/60 dark:border-zinc-800/60 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">AI Match Score</span>
                                <span id="ai-match-score" class="text-xs font-bold text-zinc-900 dark:text-zinc-100">—</span>
                            </div>
                            <p id="ai-match-summary" class="text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed"></p>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: FINANCIALS & COMPENSATION -->
                <div id="tab-edit-financials" class="drawer-tab-content space-y-4 hidden">
                    <?php if ($active_industry === 'photography_studio') : ?>
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Base Shoot Rate (per assignment)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-zinc-400 font-bold">₹</span>
                                <input type="number" id="edit-hourly-rate" placeholder="2500" class="w-full pl-7 pr-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
                            </div>
                            <p class="text-[10px] text-zinc-400 mt-1">Default payout rate per completed shoot.</p>
                        </div>
                    <?php else : ?>
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Commission Split Ratio (Agent % / Brokerage %)</label>
                            <input type="text" id="edit-commission-split" placeholder="e.g. 75/25 or 80/20" class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
                            <p class="text-[10px] text-zinc-400 mt-1">Contractual split percentage applied on closed deals.</p>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Bank Payout / UPI Details</label>
                        <input type="text" id="edit-bank-upi" placeholder="e.g. name@upi or HDFC0001234 A/C 50100..." class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
                    </div>
                </div>

                <!-- TAB 4: ACTIONS & SHORTCUTS -->
                <div id="tab-edit-actions" class="drawer-tab-content space-y-4 hidden">
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-3 bg-zinc-50/50 dark:bg-zinc-950/50">
                        <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Account Utility Actions</h4>
                        <p class="text-[10px] text-zinc-400">Trigger automated security or credential updates for this crew member.</p>

                        <div class="space-y-2 pt-1">
                            <button type="button" onclick="triggerPasswordResetForUser()" class="w-full py-2 px-3 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-bold rounded-lg text-xs transition-colors flex items-center justify-between cursor-pointer">
                                <span>Send Password Reset Email</span>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: AI & SECURITY -->
                <div id="tab-edit-ai-security" class="drawer-tab-content space-y-4 hidden">
                    <!-- AI Monthly Token Budget -->
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-3 bg-zinc-50/50 dark:bg-zinc-950/50">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">AI Monthly Token Budget</h4>
                            <span id="ai-token-display" class="text-[10px] font-bold text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-full">0 tokens</span>
                        </div>
                        <p class="text-[10px] text-zinc-400">Set the maximum AI tokens this team member can consume per billing cycle.</p>
                        <div class="pt-1">
                            <input type="range" id="edit-ai-token-limit" min="0" max="500000" step="10000" value="0" class="w-full h-1.5 rounded-full appearance-none cursor-pointer bg-zinc-200 dark:bg-zinc-700 accent-zinc-900 dark:accent-zinc-200" oninput="document.getElementById('ai-token-display').textContent = (parseInt(this.value) === 0 ? 'Unlimited' : parseInt(this.value).toLocaleString() + ' tokens')">
                            <div class="flex justify-between text-[9px] text-zinc-400 mt-1">
                                <span>Unlimited</span>
                                <span>250K</span>
                                <span>500K</span>
                            </div>
                        </div>
                    </div>

                    <!-- MCP Agent Tool Permissions -->
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-3 bg-zinc-50/50 dark:bg-zinc-950/50">
                        <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">MCP Agent Tool Permissions</h4>
                        <p class="text-[10px] text-zinc-400">Control which AI-agent tools this team member can invoke.</p>
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <?php
                            $mcp_tools = array(
                                'read_leads'       => 'Read Leads',
                                'write_leads'      => 'Write Leads',
                                'read_properties'  => 'Read Properties',
                                'write_properties' => 'Write Properties',
                                'read_vault'       => 'Read Vault',
                                'send_emails'      => 'Send Emails'
                            );
                            foreach ( $mcp_tools as $tool_key => $tool_label ) :
                            ?>
                                <label class="flex items-center gap-2 px-2.5 py-2 rounded-lg border border-zinc-200/60 dark:border-zinc-800/60 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-850 cursor-pointer transition-colors select-none">
                                    <input type="checkbox" value="<?php echo esc_attr($tool_key); ?>" class="edit-mcp-tool-checkbox accent-zinc-950 dark:accent-zinc-100 rounded w-3.5 h-3.5 cursor-pointer">
                                    <span class="text-[11px] font-medium text-zinc-700 dark:text-zinc-300"><?php echo esc_html($tool_label); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Persistent Login Session -->
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-3 bg-zinc-50/50 dark:bg-zinc-950/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Persistent Login Session</h4>
                                <p class="text-[10px] text-zinc-400 mt-0.5">Keep this user signed in across browser sessions.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="edit-stay-logged-in" class="sr-only peer">
                                <div class="w-9 h-5 bg-zinc-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-zinc-600 peer-checked:bg-zinc-900 dark:peer-checked:bg-zinc-300"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Active Sessions -->
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-3 bg-zinc-50/50 dark:bg-zinc-950/50">
                        <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Active Sessions</h4>
                        <p class="text-[10px] text-zinc-400">Force logout from all other devices and browsers.</p>
                        <button type="button" onclick="coraRevokeUserSessions()" class="w-full py-2 px-3 border border-red-200 dark:border-red-900/40 bg-red-50 dark:bg-red-950/20 hover:bg-red-100 dark:hover:bg-red-950/40 text-red-700 dark:text-red-400 font-bold rounded-lg text-xs transition-colors flex items-center justify-center gap-2 cursor-pointer">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            Revoke All Sessions
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-5 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0">
                <button type="submit" id="save-edit-btn" class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-zinc-200 font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Save Changes</button>
            </div>
        </form>
</aside>

<!-- ═══ EDIT CUSTOM ROLE DRAWER SHEET ════════════════════════════════════════ -->
<aside id="cora-edit-custom-role-drawer" class="collapsed fixed top-0 right-0 z-[10000] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-950/50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700 dark:text-zinc-300"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Edit Custom Role Permissions
            </h3>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">Modify access levels, quota caps, and feature permissions matrix.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer p-1" onclick="closeEditCustomRoleDrawer()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="edit-custom-role-form" onsubmit="handleSaveCustomRolePermissions(event)" class="flex-1 overflow-y-auto p-6 space-y-5">
        <input type="hidden" id="edit-custom-role-key">

        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Role Display Name</label>
            <input type="text" id="edit-custom-role-name" required class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Operational Access Level</label>
            <select id="edit-custom-role-access-level" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                <option value="read_only">Read-Only</option>
                <option value="contributor">Standard Contributor</option>
                <option value="manager">Manager / Admin</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5"><?php echo esc_html( $feature_labels['quota_label'] ); ?></label>
            <input type="number" id="edit-custom-role-max-quota" min="0" placeholder="Unlimited (leave blank)" class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
        </div>

        <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200">Assigned Feature Permissions Matrix</label>
            <div class="space-y-2 border border-zinc-200 dark:border-zinc-800 rounded-lg p-3 bg-zinc-50/50 dark:bg-zinc-950/50">
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="crm_leads" class="edit-custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['crm_leads'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="showings_bookings" class="edit-custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['showings_bookings'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="financials" class="edit-custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['financials'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="media_vault" class="edit-custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['media_vault'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="equipment" class="edit-custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['equipment'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="ai_suite" class="edit-custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['ai_suite'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="attendance" class="edit-custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['attendance'] ); ?></span>
                </label>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800">
            <button type="button" onclick="closeEditCustomRoleDrawer()" class="px-4 py-2 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="submit" id="save-custom-role-btn" class="px-4 py-2 rounded-lg text-xs font-semibold bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 shadow-sm transition-all cursor-pointer flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Save Role & Permissions
            </button>
    </form>
</aside>

<!-- ═══ CREATE CUSTOM ROLE DRAWER SHEET ══════════════════════════════════════ -->
<aside id="cora-create-custom-role-drawer" class="collapsed fixed top-0 right-0 z-[10000] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-950/50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700 dark:text-zinc-300"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Define Custom Role
            </h3>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">Add a tailored role for your brokerage or studio departments.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer p-1" onclick="closeCreateCustomRoleDrawer()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form id="create-custom-role-form" onsubmit="handleCreateCustomRole(event)" class="flex-1 overflow-y-auto p-6 space-y-5">
        <!-- 1. Role Display Name -->
        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Role Display Name</label>
            <input type="text" id="custom-role-name" required placeholder="e.g. Social Media Specialist" class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
        </div>

        <!-- 2. Base Role Template Selector -->
        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Base Role Template</label>
            <select id="custom-role-base-template" onchange="handleApplyBaseTemplate(this.value)" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                <option value="">Custom (Blank Template)</option>
                <?php foreach ( $role_templates as $tmpl ) : ?>
                    <option value="<?php echo esc_attr( $tmpl['key'] ); ?>"><?php echo esc_html( $tmpl['title'] ); ?></option>
                <?php endforeach; ?>
            </select>
            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-1">Clones default permissions and operational access level.</p>
        </div>

        <!-- 3. Operational Access Level -->
        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Operational Access Level</label>
            <select id="custom-role-access-level" class="w-full border border-zinc-200 dark:border-zinc-800 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-950 outline-none cursor-pointer">
                <option value="read_only">Read-Only</option>
                <option value="contributor" selected>Standard Contributor</option>
                <option value="manager">Manager / Admin</option>
            </select>
        </div>

        <!-- 4. Max Shoot/Booking Quota -->
        <div>
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5"><?php echo esc_html( $feature_labels['quota_label'] ); ?></label>
            <input type="number" id="custom-role-max-quota" min="0" placeholder="Unlimited (or e.g. 15)" class="w-full px-3 py-2 text-xs border border-zinc-200 dark:border-zinc-800 rounded-lg focus:border-zinc-400 focus:outline-none bg-white dark:bg-zinc-950 text-zinc-950 dark:text-zinc-100">
        </div>

        <!-- 5. Feature Permissions Matrix checkboxes -->
        <div class="space-y-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
            <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200">Feature Permissions Matrix</label>
            <div class="space-y-2 border border-zinc-200 dark:border-zinc-800 rounded-lg p-3 bg-zinc-50/50 dark:bg-zinc-950/50 max-h-56 overflow-y-auto">
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="crm_leads" class="custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['crm_leads'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="showings_bookings" class="custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded" checked>
                    <span><?php echo esc_html( $feature_labels['showings_bookings'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="financials" class="custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['financials'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="media_vault" class="custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded" checked>
                    <span><?php echo esc_html( $feature_labels['media_vault'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="equipment" class="custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['equipment'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="ai_suite" class="custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded">
                    <span><?php echo esc_html( $feature_labels['ai_suite'] ); ?></span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 dark:text-zinc-200 cursor-pointer hover:text-zinc-950">
                    <input type="checkbox" value="attendance" class="custom-role-perm-cb accent-zinc-950 dark:accent-zinc-100 rounded" checked>
                    <span><?php echo esc_html( $feature_labels['attendance'] ); ?></span>
                </label>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800">
            <button type="button" onclick="closeCreateCustomRoleDrawer()" class="px-4 py-2 rounded-lg text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                Cancel
            </button>
            <button type="submit" id="create-role-submit-btn" class="px-4 py-2 rounded-lg text-xs font-semibold bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 shadow-sm transition-all cursor-pointer flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Create Role
            </button>
        </div>
    </form>
</aside>

<!-- ═══ AI TRAINER & DOUBT ASSISTANT DRAWER SHEET ════════════════════════════════ -->
<aside id="cora-ai-trainer-drawer" class="collapsed fixed top-0 right-0 z-[10000] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out translate-x-full">
    <!-- Header -->
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-950/50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700 dark:text-zinc-300"><path d="M12 2c.13 2.9 2.1 4.87 5 5-2.9.13-4.87 2.1-5 5-.13-2.9-2.1-4.87-5-5 2.9-.13 4.87-2.1 5-5Z" /></svg>
                AI Trainer & Doubts
            </h3>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">Ask questions about permissions or train the assistant.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer p-1" onclick="closeAiTrainerDrawer()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Drawer Tabs -->
    <div class="flex border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/20 dark:bg-zinc-950/20 px-4 shrink-0">
        <button onclick="switchTrainerTab('chat')" id="tab-btn-trainer-chat" class="flex-1 py-3 text-center text-xs font-semibold border-b-2 border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50 transition-all cursor-pointer focus:outline-none">
            Ask Doubts
        </button>
        <?php if ( current_user_can( 'manage_options' ) || cora_is_workspace_owner() ) : ?>
        <button onclick="switchTrainerTab('context')" id="tab-btn-trainer-context" class="flex-1 py-3 text-center text-xs font-medium border-b-2 border-transparent text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-all cursor-pointer focus:outline-none">
            Training Context
        </button>
        <?php endif; ?>
        <button onclick="switchTrainerTab('keys')" id="tab-btn-trainer-keys" class="flex-1 py-3 text-center text-xs font-medium border-b-2 border-transparent text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-all cursor-pointer focus:outline-none">
            Integration Keys
        </button>
    </div>

    <!-- Content Sections -->
    <div class="flex-1 flex flex-col min-h-0 overflow-y-auto">
        <!-- 1. ASK DOUBTS (CHAT) -->
        <div id="trainer-content-chat" class="flex-1 flex flex-col min-h-0">
            <!-- Messages Container -->
            <div id="doubts-chat-messages" class="flex-1 p-5 overflow-y-auto space-y-4 text-xs">
                <!-- Welcome Bubble -->
                <div class="flex gap-2.5 items-start">
                    <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-800 dark:text-zinc-200 border border-zinc-200/50 dark:border-zinc-800 shrink-0">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2c.13 2.9 2.1 4.87 5 5-2.9.13-4.87 2.1-5 5-.13-2.9-2.1-4.87-5-5 2.9-.13 4.87-2.1 5-5Z" /></svg>
                    </div>
                    <div class="flex-1 bg-zinc-50 dark:bg-zinc-850 rounded-xl p-3 border border-zinc-200/50 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 leading-relaxed max-w-[85%]">
                        Hello! I am your Cora AI Workspace Assistant. I can explain workspace roles, help configure permissions, or detail how to save changes. What can I help you with?
                    </div>
                </div>
            </div>

            <!-- Quick click doubts list -->
            <div class="px-5 py-2.5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/40 dark:bg-zinc-950/20 shrink-0">
                <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-2">Quick Doubts</p>
                <div class="flex flex-wrap gap-1.5">
                    <button onclick="sendQuickDoubt('How to save permissions?')" class="px-2.5 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-[11px] text-zinc-650 dark:text-zinc-400 hover:border-zinc-400 dark:hover:border-zinc-600 transition-colors cursor-pointer text-left">
                        How to save permissions?
                    </button>
                    <button onclick="sendQuickDoubt('Difference between View and Edit?')" class="px-2.5 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-[11px] text-zinc-650 dark:text-zinc-400 hover:border-zinc-400 dark:hover:border-zinc-600 transition-colors cursor-pointer text-left">
                        Difference between View and Edit?
                    </button>
                </div>
            </div>

            <!-- Input Box -->
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 shrink-0 bg-white dark:bg-zinc-900">
                <div class="relative flex items-center">
                    <input type="text" id="doubts-chat-input" placeholder="Ask a doubt..." onkeydown="handleChatKeyDown(event)" class="w-full pl-3 pr-10 py-2.5 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-1 focus:ring-zinc-950 dark:focus:ring-zinc-100 transition-all">
                    <button onclick="submitChatDoubt()" class="absolute right-1.5 p-2 rounded-lg bg-zinc-950 dark:bg-zinc-100 text-white dark:text-zinc-950 hover:opacity-90 transition-all cursor-pointer flex items-center justify-center border-none">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2c.13 2.9 2.1 4.87 5 5-2.9.13-4.87 2.1-5 5-.13-2.9-2.1-4.87-5-5 2.9-.13 4.87-2.1 5-5Z" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. TRAINING CONTEXT (ADMIN-ONLY) -->
        <?php if ( current_user_can( 'manage_options' ) || cora_is_workspace_owner() ) : ?>
        <div id="trainer-content-context" class="hidden p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold text-zinc-800 dark:text-zinc-200 mb-1.5">Assistant System Training Prompt</label>
                <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mb-2 leading-relaxed">Customize the background knowledge, role definitions, and guidelines that prime the LLM response algorithm for this workspace.</p>
                <textarea id="trainer-context-prompt" rows="12" class="w-full p-3 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-950 dark:focus:ring-zinc-100 font-mono leading-relaxed" placeholder="Enter custom prompt instructions..."><?php echo esc_textarea( get_option( 'cora_workspace_ai_permissions_training_prompt', '' ) ); ?></textarea>
            </div>
            <div class="flex justify-end pt-2">
                <button type="button" onclick="saveTrainerContext()" class="px-4 py-2 rounded-lg text-xs font-semibold bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 shadow-sm transition-all cursor-pointer flex items-center gap-1.5 border-none">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Save Context
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- 3. INTEGRATION KEYS -->
        <div id="trainer-content-keys" class="hidden p-5 space-y-4">
            <h4 class="text-xs font-bold text-zinc-800 dark:text-zinc-200 uppercase tracking-wider border-b border-zinc-100 dark:border-zinc-800 pb-2">Active API Providers</h4>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 leading-relaxed">Verification indicators showing keys pre-loaded in the parent Cora suite configuration.</p>
            
            <div class="space-y-2.5">
                <?php
                $has_openai = defined('CORA_PLATFORM_OPENAI_API_KEY') && ! empty(CORA_PLATFORM_OPENAI_API_KEY);
                $has_gemini = defined('CORA_PLATFORM_GEMINI_API_KEY') && ! empty(CORA_PLATFORM_GEMINI_API_KEY);
                $has_groq = defined('CORA_PLATFORM_GROQ_API_KEY') && ! empty(CORA_PLATFORM_GROQ_API_KEY);
                $has_anthropic = defined('CORA_PLATFORM_ANTHROPIC_API_KEY') && ! empty(CORA_PLATFORM_ANTHROPIC_API_KEY);
                
                $keys = array(
                    'OpenAI (ChatGPT)'   => $has_openai,
                    'Gemini (Google)'    => $has_gemini,
                    'Groq (Llama)'       => $has_groq,
                    'Anthropic (Claude)' => $has_anthropic
                );
                
                foreach ($keys as $name => $status) :
                ?>
                <div class="flex items-center justify-between p-3 rounded-xl border border-zinc-200/60 dark:border-zinc-850 bg-zinc-50/30 dark:bg-zinc-950/20 text-xs">
                    <span class="font-semibold text-zinc-800 dark:text-zinc-200"><?php echo esc_html($name); ?></span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full <?php echo $status ? 'bg-emerald-500' : 'bg-zinc-300 dark:bg-zinc-700'; ?>"></span>
                        <span class="text-[10px] font-bold <?php echo $status ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-400 dark:text-zinc-500'; ?>">
                            <?php echo $status ? 'Active Key' : 'Not Configured'; ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</aside>

<!-- ═══ WALKTHROUGH TUTORIAL DRAWER SHEET ══════════════════════════════════════ -->
<aside id="cora-permissions-video-drawer" class="collapsed fixed top-0 right-0 z-[10000] h-full w-[440px] max-w-[90vw] bg-white dark:bg-zinc-900 border-l border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col transition-transform duration-300 ease-in-out translate-x-full">
    <!-- Header -->
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-950/50 shrink-0">
        <div>
            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-700 dark:text-zinc-300"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                Tutorial Walkthrough
            </h3>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">Learn how to manage team permissions and custom roles.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer p-1" onclick="closePermissionsVideoDrawer()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Video & Timeline Content -->
    <div class="flex-1 overflow-y-auto p-5 space-y-5">
        <!-- Responsive Iframe Wrapper (16:9) -->
        <div class="relative w-full aspect-video rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800">
            <iframe id="permissions-walkthrough-player" class="absolute inset-0 w-full h-full" src="https://www.youtube.com/embed/dQw4w9WgXcQ?enablejsapi=1" title="Cora Workspace Tutorial" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
        </div>

        <!-- Video Walkthrough Timeline -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold text-zinc-800 dark:text-zinc-200 uppercase tracking-wider">Video Timeline & Topics</h4>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                <!-- Timeline item 1 -->
                <button onclick="seekWalkthroughVideo(0)" class="w-full flex items-center justify-between py-2.5 text-left text-xs text-zinc-650 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors cursor-pointer group bg-transparent border-none">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[10px] font-bold px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">0:00</span>
                        <span>Introduction to Cora Roles</span>
                    </div>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="opacity-0 group-hover:opacity-100 transition-opacity"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                </button>
                <!-- Timeline item 2 -->
                <button onclick="seekWalkthroughVideo(75)" class="w-full flex items-center justify-between py-2.5 text-left text-xs text-zinc-650 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors cursor-pointer group bg-transparent border-none">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[10px] font-bold px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">1:15</span>
                        <span>Core Navigation Access Details</span>
                    </div>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="opacity-0 group-hover:opacity-100 transition-opacity"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                </button>
                <!-- Timeline item 3 -->
                <button onclick="seekWalkthroughVideo(165)" class="w-full flex items-center justify-between py-2.5 text-left text-xs text-zinc-650 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors cursor-pointer group bg-transparent border-none">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[10px] font-bold px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">2:45</span>
                        <span>View vs Edit vs No Access Settings</span>
                    </div>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="opacity-0 group-hover:opacity-100 transition-opacity"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                </button>
                <!-- Timeline item 4 -->
                <button onclick="seekWalkthroughVideo(250)" class="w-full flex items-center justify-between py-2.5 text-left text-xs text-zinc-650 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-zinc-100 transition-colors cursor-pointer group bg-transparent border-none">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-[10px] font-bold px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">4:10</span>
                        <span>Saving Permissions Matrix & Live Sync</span>
                    </div>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="opacity-0 group-hover:opacity-100 transition-opacity"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                </button>
            </div>
        </div>

        <!-- Instructions Text Box -->
        <div class="p-4 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/30 space-y-2 text-xs leading-relaxed">
            <h5 class="font-bold text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-600 dark:text-zinc-400"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                Quick Reference Guides
            </h5>
            <ul class="list-disc pl-4 space-y-1 text-zinc-600 dark:text-zinc-400">
                <li>Choose <strong class="text-zinc-800 dark:text-zinc-200">View</strong> for read-only visibilities on core dashboard features.</li>
                <li>Choose <strong class="text-zinc-800 dark:text-zinc-200">Edit</strong> to permit editing, deleting, and updating listing/shoot details.</li>
                <li>Choose <strong class="text-zinc-800 dark:text-zinc-200">No Access</strong> to hide the screen/module entirely from the user role.</li>
                <li>Super Admin access levels are locked globally and cannot be modified.</li>
            </ul>
        </div>
    </div>
</aside>

<script>


    // Tab switching for User Management section (synchronized across mobile/desktop menus)
    $(document).on('click', '#cora-page-team-roles .cora-sub-tabs-container .cora-sub-tab', function(e) {
        e.preventDefault();
        var targetId = $(this).data('target');
        if (!targetId) return;

        // Sync active states on all matching tab buttons
        $('#cora-page-team-roles .cora-sub-tabs-container .cora-sub-tab').each(function() {
            var $t = $(this);
            if ($t.data('target') === targetId) {
                $t.addClass('active border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 font-semibold')
                  .removeClass('border-transparent text-zinc-550 dark:text-zinc-400 font-medium');
                if ($t.closest('#mobile-tabs-more-dropdown').length) {
                    $t.addClass('bg-zinc-50 dark:bg-zinc-850');
                }
            } else {
                $t.removeClass('active border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 font-semibold bg-zinc-50 dark:bg-zinc-850')
                  .addClass('border-transparent text-zinc-550 dark:text-zinc-400 font-medium');
            }
        });
        
        $('#cora-page-team-roles .cora-tab-content').addClass('hidden');
        $('#' + targetId).removeClass('hidden');

        // Handle More dropdown active styling on mobile
        var isSecondary = ['tab-attendance-logs', 'tab-permissions-matrix', 'tab-custom-roles'].indexOf(targetId) !== -1;
        if (isSecondary) {
            $('#cora-page-team-roles #users-more-tab-btn')
                .addClass('active border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 font-semibold')
                .removeClass('border-transparent text-zinc-400 dark:text-zinc-500 font-medium');
        } else {
            $('#cora-page-team-roles #users-more-tab-btn')
                .removeClass('active border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 font-semibold')
                .addClass('border-transparent text-zinc-400 dark:text-zinc-500 font-medium');
        }

        if (targetId === 'tab-attendance-logs' && typeof fetchAttendanceLogs === 'function') {
            fetchAttendanceLogs();
        }

        // Update URL query string with ?tab=tab-slug
        var tabSlug = targetId.replace(/^tab-/, '');
        if (tabSlug === 'pending-invites') {
            tabSlug = 'pending-invitations';
        }
        var newUrl = new URL(window.location.href);
        newUrl.searchParams.set('tab', tabSlug);
        history.replaceState(null, '', newUrl.toString());

        // Update "More" button text and state on mobile
        var isDropdownTab = ['tab-permissions-matrix', 'tab-attendance-logs', 'tab-custom-roles'].includes(targetId);
        var $moreBtn = $('#cora-page-team-roles #mobile-tabs-more-btn');
        if (isDropdownTab) {
            var tabName = $(this).text().trim();
            $moreBtn.find('span').text(tabName);
            $moreBtn.addClass('active border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 font-semibold')
                    .removeClass('text-zinc-650 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800');
        } else {
            $moreBtn.find('span').text('More');
            $moreBtn.removeClass('active border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 font-semibold')
                    .addClass('text-zinc-650 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800');
        }

        // Close dropdown
        $('#cora-page-team-roles #mobile-tabs-more-dropdown').addClass('hidden');
        $('#cora-page-team-roles #more-chevron-icon').removeClass('rotate-180');
    });

    // Toggle Mobile "More" Tabs Dropdown Menu
    $(document).on('click', '#cora-page-team-roles #mobile-tabs-more-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $dropdown = $('#cora-page-team-roles #mobile-tabs-more-dropdown');
        var $chevron = $('#cora-page-team-roles #more-chevron-icon');
        
        if ($dropdown.hasClass('hidden')) {
            $dropdown.removeClass('hidden');
            $chevron.addClass('rotate-180');
        } else {
            $dropdown.addClass('hidden');
            $chevron.removeClass('rotate-180');
        }
    });

    // Close Mobile "More" dropdown on click outside
    $(document).on('click', function(e) {
        if ($('#cora-page-team-roles').is(':visible')) {
            if (!$(e.target).closest('#mobile-tabs-more-btn, #mobile-tabs-more-dropdown').length) {
                $('#cora-page-team-roles #mobile-tabs-more-dropdown').addClass('hidden');
                $('#cora-page-team-roles #more-chevron-icon').removeClass('rotate-180');
            }
        }
    });

    $(document).ready(function() {
        const params = new URLSearchParams(window.location.search);
        const activeTab = params.get('tab');
        if (activeTab) {
            let $matchingTab = $('#cora-page-team-roles .cora-sub-tabs-container .cora-sub-tab').filter(function() {
                var target = $(this).data('target') || '';
                var slug = target.replace(/^tab-/, '');
                return target === activeTab ||
                       target === 'tab-' + activeTab ||
                       slug === activeTab ||
                       (activeTab === 'pending-invitations' && (slug === 'pending-invites' || target === 'tab-pending-invites')) ||
                       (activeTab === 'pending-invites' && (slug === 'pending-invites' || target === 'tab-pending-invites'));
            });
            if ($matchingTab.length > 0) {
                $matchingTab.first().trigger('click');
            }
        }

        const inviteRole = params.get('invite_role');
        if (inviteRole) {
            setTimeout(function() {
                openInviteDrawer(inviteRole);
            }, 100);
        }
    });

    function filterActiveMembers() {
        var isMobile = window.innerWidth < 768;
        var q = $('#member-search').val() || '';
        q = q.toLowerCase().trim();
        var role = isMobile ? $('#filter-role-mobile').val() : $('#filter-role').val();
        var branch = isMobile ? $('#filter-branch-mobile').val() : $('#filter-branch').val();
        var status = isMobile ? $('#filter-status-mobile').val() : $('#filter-status').val();
        
        // Sync desktop selects to mobile selects
        $('#filter-role-mobile').val(role);
        $('#filter-branch-mobile').val(branch);
        $('#filter-status-mobile').val(status);
        
        // Update the visibility of the "Clear Filters" buttons
        updateClearFiltersVisibility();
        
        var count = 0;
        $('.active-member-row').each(function() {
            var rowName = $(this).data('name') || '';
            var rowEmail = $(this).data('email') || '';
            var rowRole = $(this).data('role') || '';
            var rowBranch = $(this).data('branch') || '';
            var rowStatus = $(this).data('status') || '';
            
            var matchQ = !q || rowName.indexOf(q) !== -1 || rowEmail.indexOf(q) !== -1;
            var matchRole = !role || rowRole === role;
            var matchBranch = !branch || rowBranch === branch;
            var matchStatus = !status || rowStatus === status;
            
            if (matchQ && matchRole && matchBranch && matchStatus) {
                $(this).show();
                count++;
            } else {
                $(this).hide();
            }
        });
        
        $('#member-count-badge').text(count + ' members total');
        $('#mobile-members-count-badge').text(count);
        
        // Show indicator dot on filter toggle button if filters are active
        if (role || branch || status) {
            $('#filter-active-dot').removeClass('hidden');
        } else {
            $('#filter-active-dot').addClass('hidden');
        }
    }

    function toggleUsersMoreMenu() {
        $('#users-more-menu').toggleClass('hidden');
    }
    window.toggleUsersMoreMenu = toggleUsersMoreMenu;

    function closeUsersMoreMenu() {
        $('#users-more-menu').addClass('hidden');
    }
    window.closeUsersMoreMenu = closeUsersMoreMenu;

    // Robust close handler for edit drawer (backup for inline onclick)
    $(document).on('click touchend', '#edit-drawer-close-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (typeof window.closeEditUserDrawer === 'function') {
            window.closeEditUserDrawer();
        } else if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('#cora-edit-user-drawer').addClass('collapsed');
            $('#cora-drawer-backdrop').addClass('hidden');
        }
    });

    // Close dropdown menu when clicking outside
    $(document).on('click', function(e) {
        if ($('#cora-page-team-roles').is(':visible')) {
            if (!$(e.target).closest('#users-more-tab-wrapper').length) {
                closeUsersMoreMenu();
            }
        }
    });

    function syncFilterAndRun(type) {
        var mobileVal = $('#filter-' + type + '-mobile').val();
        $('#filter-' + type).val(mobileVal);
        filterActiveMembers();
    }

    function updateClearFiltersVisibility() {
        var hasFilters = $('#filter-role').val() || $('#filter-branch').val() || $('#filter-status').val();
        if (hasFilters) {
            $('#btn-clear-filters-desktop').removeClass('hidden');
            $('#btn-clear-filters-mobile').removeClass('hidden');
        } else {
            $('#btn-clear-filters-desktop').addClass('hidden');
            $('#btn-clear-filters-mobile').addClass('hidden');
        }
    }

    function toggleMobileFilters() {
        var $panel = $('#member-filter-panel');
        if ($panel.hasClass('hidden')) {
            $panel.removeClass('hidden');
            $('#mobile-filter-toggle').addClass('bg-zinc-50 dark:bg-zinc-800 border-zinc-400 dark:border-zinc-700 text-zinc-950 dark:text-zinc-50');
        } else {
            $panel.addClass('hidden');
            $('#mobile-filter-toggle').removeClass('bg-zinc-50 dark:bg-zinc-800 border-zinc-400 dark:border-zinc-700 text-zinc-950 dark:text-zinc-50');
        }
    }

    function clearFilters() {
        $('#filter-role, #filter-role-mobile').val('');
        $('#filter-branch, #filter-branch-mobile').val('');
        $('#filter-status, #filter-status-mobile').val('');
        filterActiveMembers();
    }

    // Invite user drawer
    function openInviteDrawer(role) {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('aside[id$="-drawer"]').addClass('collapsed hidden');
        }
        var targetRole = role || $('#filter-role').val() || $('#filter-role-mobile').val() || '';
        if (targetRole && $('#invite-role').length) {
            $('#invite-role').val(targetRole);
        } else if ($('#invite-role option:first').length) {
            $('#invite-role').val($('#invite-role option:first').val());
        }
        $('#cora-invite-user-drawer').removeClass('collapsed hidden translate-x-full pointer-events-none').addClass('translate-x-0').css({
            'display': 'flex',
            'pointer-events': 'auto',
            'transform': 'translateX(0)',
            'visibility': 'visible'
        });
        $('#cora-drawer-backdrop').removeClass('hidden').css({
            'display': 'block',
            'pointer-events': 'auto'
        });
    }
    window.openInviteDrawer = openInviteDrawer;

    function closeInviteDrawer() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('#cora-invite-user-drawer').addClass('collapsed hidden translate-x-full').removeClass('translate-x-0');
        }
        $('#cora-invite-user-drawer').removeClass('translate-x-0').addClass('translate-x-full').css({
            'display': 'none',
            'pointer-events': 'none',
            'transform': 'translateX(100%)',
            'visibility': 'hidden'
        });
        $('#cora-drawer-backdrop').addClass('hidden').css({
            'display': 'none',
            'pointer-events': 'none'
        });
        $('#invite-first-name').val('');
        $('#invite-last-name').val('');
        $('#invite-email').val('');
    }
    window.closeInviteDrawer = closeInviteDrawer;

    function handleSendInvite(e) {
        e.preventDefault();
        var fname = $('#invite-first-name').val().trim();
        var lname = $('#invite-last-name').val().trim();
        var email = $('#invite-email').val().trim();
        var role = $('#invite-role').val();
        var branch = $('#invite-branch').val();

        $('#send-invite-btn').prop('disabled', true).text('Sending invitation...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_send_invitation',
            first_name: fname,
            last_name: lname,
            email: email,
            role: role,
            branch_id: branch,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            var msg = (res && res.data && (typeof res.data === 'string' ? res.data : res.data.message)) || 'Failed to send invitation.';
            if (res && res.success) {
                window.coraShowToast('Invitation sent successfully.');
                closeInviteDrawer();
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(msg);
                $('#send-invite-btn').prop('disabled', false).text('Send Invitation Link');
            }
        }, 'json').fail(function(xhr) {
            var err = 'Failed to send invitation.';
            try {
                var parsed = typeof xhr.responseJSON === 'object' ? xhr.responseJSON : JSON.parse(xhr.responseText);
                if (parsed && parsed.data) {
                    err = typeof parsed.data === 'string' ? parsed.data : (parsed.data.message || err);
                }
            } catch(e) {}
            window.coraShowToast(err);
            $('#send-invite-btn').prop('disabled', false).text('Send Invitation Link');
        });
    }

    // Edit user drawer
    // Edit user drawer sub-tabs
    $('.drawer-edit-tab').on('click', function() {
        $('.drawer-edit-tab')
            .removeClass('active border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 font-bold')
            .addClass('border-transparent text-zinc-500 font-medium');
        $(this)
            .addClass('active border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 font-bold')
            .removeClass('border-transparent text-zinc-500 font-medium');
        
        $('.drawer-tab-content').addClass('hidden');
        $('#' + $(this).data('drawer-tab')).removeClass('hidden');
    });



    var currentEditingUser = null;
    var currentEditingStatus = 'active';

    function openEditUserDrawer(userPayload) {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('aside[id$="-drawer"]').addClass('collapsed');
        }

        var user = null;
        if (userPayload && typeof userPayload === 'object') {
            if (userPayload.nodeType || userPayload.jquery || $(userPayload).attr('data-user')) {
                var raw = $(userPayload).attr('data-user');
                try {
                    user = JSON.parse(raw);
                } catch(e) {
                    console.error('Failed to parse user data from element:', e);
                }
            } else {
                user = userPayload;
            }
        } else if (typeof userPayload === 'string') {
            try {
                user = JSON.parse(userPayload);
            } catch(e) {
                console.error('Failed to parse user string:', e);
            }
        }

        if (!user) return;
        currentEditingUser = user;

        $('#edit-user-id').val(user.id || '');
        $('#edit-display-name').val(user.name || '');
        $('#edit-phone').val(user.phone || '');
        $('#edit-role').val(user.role || '');
        $('#edit-branch').val(user.branch || '');
        $('#edit-bio').val(user.bio || '');
        $('#edit-commission-split').val(user.split || '70/30');
        $('#edit-hourly-rate').val(user.rate || '2500');
        $('#edit-bank-upi').val(user.bank || '');

        $('#edit-user-title').text('Edit ' + (user.name || 'Team Member'));

        // Profile Image & Banner
        var avatarUrl = user.avatar || '';
        var bannerUrl = user.banner || '';
        $('#edit-avatar-url').val(avatarUrl);
        $('#edit-banner-url').val(bannerUrl);
        if (avatarUrl) {
            $('#edit-avatar-img').attr('src', avatarUrl).removeClass('hidden');
            $('#edit-avatar-initials').addClass('hidden');
        } else {
            $('#edit-avatar-img').addClass('hidden').attr('src', '');
            $('#edit-avatar-initials').removeClass('hidden').text((user.name || '?').charAt(0).toUpperCase());
        }
        if (bannerUrl) {
            $('#edit-banner-img').attr('src', bannerUrl).removeClass('hidden');
        } else {
            $('#edit-banner-img').addClass('hidden').attr('src', '');
        }
        $('.default-avatar-btn').removeClass('ring-2 ring-zinc-900 dark:ring-zinc-100');

        // Reset and populate specializations checkboxes
        $('.edit-spec-checkbox').prop('checked', false);
        if (Array.isArray(user.specs)) {
            user.specs.forEach(function(s) {
                $('.edit-spec-checkbox[value="' + s + '"]').prop('checked', true);
            });
        }

        currentEditingStatus = user.status || 'active';
        var checked = (currentEditingStatus === 'active');
        $('#edit-status-toggle').prop('checked', checked);
        $('#leads-reassignment-panel').addClass('hidden');

        // Hide self-deactivation option
        if (parseInt(user.id) === <?php echo $current_user_id; ?>) {
            $('#edit-status-toggle').prop('disabled', true);
        } else {
            $('#edit-status-toggle').prop('disabled', false);
        }

        // Reset and populate AI & Security settings
        var tokenLimit = parseInt(user.ai_token_limit) || 0;
        $('#edit-ai-token-limit').val(tokenLimit);
        $('#ai-token-display').text(tokenLimit === 0 ? 'Unlimited' : tokenLimit.toLocaleString() + ' tokens');

        $('.edit-mcp-tool-checkbox').prop('checked', false);
        if (Array.isArray(user.mcp_allowed_tools)) {
            user.mcp_allowed_tools.forEach(function(t) {
                $('.edit-mcp-tool-checkbox[value="' + t + '"]').prop('checked', true);
            });
        }

        $('#edit-stay-logged-in').prop('checked', user.stay_logged_in === 'yes');

        // Reset AI Talent Matchmaker results
        $('#ai-match-results').addClass('hidden');
        $('#ai-match-score').text('—');
        $('#ai-match-summary').text('');

        // Reset to Tab 1
        $('.drawer-edit-tab[data-drawer-tab="tab-edit-general"]').trigger('click');

        $('#cora-edit-user-drawer').removeClass('collapsed hidden translate-x-full pointer-events-none').addClass('translate-x-0').css({
            'display': 'flex',
            'pointer-events': 'auto',
            'transform': 'translateX(0)',
            'visibility': 'visible'
        });
        $('#cora-drawer-backdrop').removeClass('hidden').css({
            'display': 'block',
            'pointer-events': 'auto'
        });
    }
    window.openEditUserDrawer = openEditUserDrawer;
    window.coraOpenEditUserDrawer = openEditUserDrawer;
    window.closeEditUserDrawer = closeEditUserDrawer;

    window.toggleUserStatusAction = function() {
        if (!currentEditingUser || !currentEditingUser.id) return;
        var newStatus = (currentEditingStatus === 'active') ? 'suspended' : 'active';
        $.post(coraREData.ajaxUrl, {
            action: 'cora_update_user_status',
            nonce: coraREData.nonce,
            target_user_id: currentEditingUser.id,
            status: newStatus
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Status updated successfully.', 'success');
                closeEditUserDrawer();
                setTimeout(function() { window.location.reload(); }, 800);
            } else {
                if (window.coraShowToast) window.coraShowToast((res.data && res.data.message) ? res.data.message : 'Failed to update status.', 'error');
            }
        }, 'json').fail(function() {
            if (window.coraShowToast) window.coraShowToast('Network error updating user status.', 'error');
        });
    };

    function closeEditUserDrawer() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('#cora-edit-user-drawer').addClass('collapsed hidden translate-x-full').removeClass('translate-x-0');
        }
        $('#cora-edit-user-drawer').removeClass('translate-x-0').addClass('translate-x-full').css({
            'display': 'none',
            'pointer-events': 'none',
            'transform': 'translateX(100%)',
            'visibility': 'hidden'
        });
        $('#cora-drawer-backdrop').addClass('hidden').css({
            'display': 'none',
            'pointer-events': 'none'
        });
    }

    // Profile Avatar & Banner Helpers
    window.coraSelectAvatar = function() {
        if (typeof wp !== 'undefined' && wp.media) {
            var frame = wp.media({
                title: 'Select Profile Photo',
                button: { text: 'Use as Avatar' },
                multiple: false,
                library: { type: 'image' }
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#edit-avatar-url').val(attachment.url);
                $('#edit-avatar-img').attr('src', attachment.url).removeClass('hidden');
                $('#edit-avatar-initials').addClass('hidden');
                $('.default-avatar-btn').removeClass('ring-2 ring-zinc-900 dark:ring-zinc-100');
            });
            frame.open();
        }
    };

    window.coraSelectBanner = function() {
        if (typeof wp !== 'undefined' && wp.media) {
            var frame = wp.media({
                title: 'Select Cover Banner',
                button: { text: 'Use as Banner' },
                multiple: false,
                library: { type: 'image' }
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#edit-banner-url').val(attachment.url);
                $('#edit-banner-img').attr('src', attachment.url).removeClass('hidden');
            });
            frame.open();
        }
    };

    window.coraPickDefaultAvatar = function(btn) {
        var svgEl = $(btn).find('svg')[0];
        if (!svgEl) return;
        var svgData = new XMLSerializer().serializeToString(svgEl);
        var dataUri = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
        $('#edit-avatar-url').val(dataUri);
        $('#edit-avatar-img').attr('src', dataUri).removeClass('hidden');
        $('#edit-avatar-initials').addClass('hidden');
        $('.default-avatar-btn').removeClass('ring-2 ring-zinc-900 dark:ring-zinc-100');
        $(btn).addClass('ring-2 ring-zinc-900 dark:ring-zinc-100');
    };

    window.coraRemoveAvatar = function() {
        $('#edit-avatar-url').val('');
        $('#edit-avatar-img').addClass('hidden').attr('src', '');
        $('#edit-avatar-initials').removeClass('hidden');
        $('.default-avatar-btn').removeClass('ring-2 ring-zinc-900 dark:ring-zinc-100');
    };

    function handleStatusToggleChange(el) {
        var active = $(el).is(':checked');
        var userId = $('#edit-user-id').val();
        
        if (!active) {
            // Check if user has open leads
            $.post(coraREData.ajaxUrl, {
                action: 'cora_ajax_get_user_leads_count',
                user_id: userId,
                nonce: coraREData.ajaxNonce
            }, function(res) {
                if (res.success && res.data.leads_count > 0) {
                    $('#leads-count-warning').text(res.data.leads_count);
                    // Filter dropdown to hide current editing user
                    $('#reassign-leads-to option').show();
                    $('#reassign-leads-to option[value="' + userId + '"]').hide();
                    $('#leads-reassignment-panel').removeClass('hidden');
                } else {
                    $('#leads-reassignment-panel').addClass('hidden');
                }
            });
        } else {
            $('#leads-reassignment-panel').addClass('hidden');
        }
    }

    function handleSaveEditUser(e) {
        e.preventDefault();
        var userId = $('#edit-user-id').val();
        var name = $('#edit-display-name').val().trim();
        var phone = $('#edit-phone').val().trim();
        var role = $('#edit-role').val();
        if (!role && currentEditingUser && currentEditingUser.role) {
            role = currentEditingUser.role;
        }
        if (!role) {
            role = 'administrator';
        }
        var branch = $('#edit-branch').val();
        var active = $('#edit-status-toggle').is(':checked');
        var status = active ? 'active' : 'inactive';
        var reassignTo = $('#reassign-leads-to').val();
        var bio = $('#edit-bio').val().trim();
        var split = $('#edit-commission-split').length ? $('#edit-commission-split').val().trim() : '';
        var rate = $('#edit-hourly-rate').length ? $('#edit-hourly-rate').val().trim() : '';
        var bank = $('#edit-bank-upi').val().trim();

        var specs = [];
        $('.edit-spec-checkbox:checked').each(function() {
            specs.push($(this).val());
        });

        // AI & Security Settings
        var aiTokenLimit = $('#edit-ai-token-limit').val();
        var mcpAllowedTools = [];
        $('.edit-mcp-tool-checkbox:checked').each(function() {
            mcpAllowedTools.push($(this).val());
        });
        var stayLoggedIn = $('#edit-stay-logged-in').is(':checked') ? 'yes' : 'no';

        $('#save-edit-btn').prop('disabled', true).text('Saving profile...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_user_changes',
            user_id: userId,
            display_name: name,
            phone: phone,
            role: role,
            branch_id: branch,
            status: status,
            reassign_to: reassignTo,
            bio: bio,
            specs: specs,
            commission_split: split,
            hourly_rate: rate,
            bank_upi: bank,
            ai_token_limit: aiTokenLimit,
            mcp_allowed_tools: mcpAllowedTools,
            stay_logged_in: stayLoggedIn,
            avatar_url: $('#edit-avatar-url').val(),
            banner_url: $('#edit-banner-url').val(),
            nonce: coraREData.ajaxNonce
        }, function(res) {
            var msg = (res && res.data && (typeof res.data === 'string' ? res.data : res.data.message)) || 'Failed to update user.';
            if (res && res.success) {
                window.coraShowToast('Team member profile updated successfully.');
                closeEditUserDrawer();
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(msg);
                $('#save-edit-btn').prop('disabled', false).text('Save Changes');
            }
        }, 'json').fail(function(xhr) {
            var err = 'Failed to update user.';
            try {
                var parsed = typeof xhr.responseJSON === 'object' ? xhr.responseJSON : JSON.parse(xhr.responseText);
                if (parsed && parsed.data) {
                    err = typeof parsed.data === 'string' ? parsed.data : (parsed.data.message || err);
                }
            } catch(e) {}
            window.coraShowToast(err);
            $('#save-edit-btn').prop('disabled', false).text('Save Changes');
        });
    }

    window.coraAnalyzeProfileSkills = function() {
        var bio = $('#edit-bio').val().trim();
        if (!bio) {
            window.coraShowToast('Please enter a bio or operational notes first.');
            return;
        }

        var $btn = $('#ai-matchmaker-btn');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('Analyzing bio...');

        $('#ai-match-results').addClass('hidden');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_analyze_profile_skills',
            bio: bio,
            industry: coraREData.industry || 'real_estate',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            $btn.prop('disabled', false).html(originalText);
            if (res.success && res.data) {
                // Populate specializations checkboxes
                $('.edit-spec-checkbox').prop('checked', false);
                if (Array.isArray(res.data.specs)) {
                    res.data.specs.forEach(function(spec) {
                        $('.edit-spec-checkbox[value="' + spec + '"]').prop('checked', true);
                    });
                }
                // Show AI feedback card
                $('#ai-match-score').text((res.data.match_score || 0) + '%');
                $('#ai-match-summary').text(res.data.analysis || 'Skills auto-matched successfully.');
                $('#ai-match-results').removeClass('hidden');
                window.coraShowToast('Skills profile analysis complete!');
            } else {
                window.coraShowToast(res.data && res.data.message ? res.data.message : 'Analysis failed.');
            }
        }, 'json').fail(function() {
            $btn.prop('disabled', false).html(originalText);
            window.coraShowToast('Network error during AI profile analysis.');
        });
    };

    window.coraRevokeUserSessions = function() {
        if (!currentEditingUser || !currentEditingUser.id) {
            window.coraShowToast('Invalid user context.');
            return;
        }

        var performRevoke = function() {
            window.coraShowToast('Revoking active sessions...');
            $.post(coraREData.ajaxUrl, {
                action: 'cora_ajax_logout_other_sessions',
                user_id: currentEditingUser.id,
                nonce: coraREData.ajaxNonce
            }, function(res) {
                if (res.success) {
                    window.coraShowToast(res.data && res.data.message ? res.data.message : 'Successfully revoked all sessions.');
                } else {
                    window.coraShowToast(res.data && res.data.message ? res.data.message : 'Revocation failed.');
                }
            }).fail(function() {
                window.coraShowToast('Network error revoking user sessions.');
            });
        };

        if (window.coraConfirmAction) {
            window.coraConfirmAction(
                'Revoke Active Sessions',
                'Are you sure you want to force logout this user from all devices?',
                performRevoke
            );
        } else {
            performRevoke();
        }
    };

    function triggerPasswordResetForUser() {
        if (!currentEditingUser || !currentEditingUser.email) {
            window.coraShowToast('Invalid user context.');
            return;
        }
        window.coraShowToast('Sending password reset email to ' + currentEditingUser.email + '...');
        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_resend_verification',
            email: currentEditingUser.email,
            nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Password reset instructions dispatched.');
            } else {
                window.coraShowToast(res.data.message || 'Failed to dispatch reset email.');
            }
        });
    }
    window.triggerPasswordResetForUser = triggerPasswordResetForUser;

    var coraPendingCancels = {};
    function coraCancelInvitation(token, btn) {
        if (!coraPendingCancels[token]) {
            coraPendingCancels[token] = true;
            if (btn) $(btn).text('Confirm Cancel').addClass('bg-red-50 text-red-700 border-red-300');
            window.coraShowToast('Click Confirm Cancel to revoke this invitation.', 'info');
            setTimeout(function() {
                coraPendingCancels[token] = false;
                if (btn) $(btn).text('Cancel').removeClass('bg-red-50 text-red-700 border-red-300');
            }, 4000);
            return;
        }

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_cancel_invitation',
            token: token,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Invitation cancelled successfully.');
                setTimeout(function() { window.location.reload(); }, 800);
            }
        });
    }
    window.coraCancelInvitation = coraCancelInvitation;

    function coraResendVerification(email) {
        window.coraShowToast('Resending invitation link...');
        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_resend_verification',
            email: email,
            nonce: '<?php echo wp_create_nonce( "cora_login_nonce" ); ?>'
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Invitation link resent.');
            } else {
                window.coraShowToast(res.data.message);
            }
        });
    }
    window.coraResendVerification = coraResendVerification;

    // ==========================================
    // PERMISSIONS MATRIX TOOLBAR & ACTIONS HANDLERS
    // ==========================================
    window.coraSavePermissionsMatrix = function() {
        var matrix = {};
        $('#cora-permissions-matrix-table tbody tr.cora-matrix-row:not([data-locked="true"])').each(function() {
            var role = $(this).data('role');
            if (!role) return;
            matrix[role] = {};
            $(this).find('td[data-feature]').each(function() {
                var feature = $(this).data('feature');
                var level = $(this).data('level') || 'none';
                matrix[role][feature] = level;
            });
        });

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_permissions_matrix',
            matrix: matrix,
            security: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data && res.data.message ? res.data.message : 'Permissions matrix saved successfully.');
            } else {
                window.coraShowToast((res.data && res.data.message) ? res.data.message : 'Failed to save permissions matrix.');
            }
        }).fail(function() {
            window.coraShowToast('Network error saving permissions matrix.');
        });
    };

    // Cell Selection & Custom Dropdown Badge helpers
    window.coraGetCellBadgeHtml = function(level) {
        var view_icon = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        var edit_icon = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>';
        var none_icon = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>';
        var caret = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0 opacity-70"><polyline points="6 9 12 15 18 9"></polyline></svg>';

        if (level === 'view') {
            return '<span class="cora-matrix-cell-badge inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-500/20 transition-all cursor-pointer shadow-2xs select-none hover:border-emerald-500/50">' + view_icon + 'View' + caret + '</span>';
        } else if (level === 'edit') {
            return '<span class="cora-matrix-cell-badge inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap bg-purple-50 text-purple-700 dark:bg-purple-950/30 dark:text-purple-400 border border-purple-500/20 transition-all cursor-pointer shadow-2xs select-none hover:border-purple-500/50">' + edit_icon + 'Edit' + caret + '</span>';
        } else {
            return '<span class="cora-matrix-cell-badge inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap bg-zinc-100 text-zinc-650 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 transition-all cursor-pointer shadow-2xs select-none hover:border-zinc-400/50">' + none_icon + 'No Access' + caret + '</span>';
        }
    };

    window.updateCellState = function(role, feature, newLevel) {
        var $desktopTd = $('#cora-permissions-matrix-table tbody tr[data-role="' + role + '"]')
            .find('td[data-feature="' + feature + '"]');
        if ($desktopTd.length) {
            $desktopTd.data('level', newLevel);
            $desktopTd.attr('data-level', newLevel);
            
            var badgeHtml = window.coraGetCellBadgeHtml(newLevel);
            var isChecked = (newLevel !== 'none') ? 'checked' : '';
            var cbHtml = '<input type="checkbox" ' + isChecked + ' data-feature="' + feature + '" class="cora-permission-checkbox absolute opacity-[0.01] w-[1px] h-[1px] cursor-pointer" style="left:0; top:0; z-index:100; pointer-events:auto;">';
            
            $desktopTd.html(badgeHtml + cbHtml);
        }

        var $mobileCell = $('[data-mobile-role="' + role + '"]')
            .find('.mobile-matrix-cell[data-feature="' + feature + '"]');
        if ($mobileCell.length) {
            $mobileCell.data('level', newLevel);
            $mobileCell.attr('data-level', newLevel);
            $mobileCell.html(window.coraGetCellBadgeHtml(newLevel));
        }
    };

    $(document).on('click', '.cora-matrix-cell-badge', function(e) {
        e.stopPropagation();
        $('.cora-matrix-popover').remove();

        var $badge = $(this);
        var $cell = $badge.parent();
        var role = $cell.data('role');
        var feature = $cell.data('feature');
        var currentLevel = $cell.data('level') || 'none';

        if (!role || !feature) return;

        var $popover = $('<div class="cora-matrix-popover absolute z-[10005] w-36 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-lg p-1.5 space-y-0.5 select-none text-xs"></div>');

        var options = [
            { level: 'view', label: 'View', class: 'hover:bg-emerald-50 dark:hover:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400', icon: '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>' },
            { level: 'edit', label: 'Edit', class: 'hover:bg-purple-50 dark:hover:bg-purple-950/20 text-purple-600 dark:text-purple-400', icon: '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>' },
            { level: 'none', label: 'No Access', class: 'hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-650 dark:text-zinc-400', icon: '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>' }
        ];

        options.forEach(function(opt) {
            var activeClass = (currentLevel === opt.level) ? 'bg-zinc-100 dark:bg-zinc-800 font-semibold' : '';
            var $item = $('<button type="button" class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-left transition-colors cursor-pointer bg-transparent border-none ' + opt.class + ' ' + activeClass + '">' + opt.icon + '<span>' + opt.label + '</span></button>');
            $item.on('click', function() {
                window.updateCellState(role, feature, opt.level);
                $popover.remove();
                window.coraSavePermissionsMatrix();
            });
            $popover.append($item);
        });

        $('body').append($popover);
        var offset = $badge.offset();
        var popoverWidth = $popover.outerWidth();
        var top = offset.top + $badge.outerHeight() + 4;
        var left = offset.left + ($badge.outerWidth() / 2) - (popoverWidth / 2);

        if (left < 10) left = 10;
        if (left + popoverWidth > $(window).width() - 10) {
            left = $(window).width() - popoverWidth - 10;
        }

        $popover.css({
            top: top + 'px',
            left: left + 'px'
        });
    });

    $(document).on('click', function() {
        $('.cora-matrix-popover').remove();
    });

    window.coraResetMatrixDefaults = function() {
        var defaultPermsMap = {
            'cora_branch_manager': ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'portfolio', 'leads', 'settings', 'canvas', 'forms', 'emails', 'review_acquisition'],
            'cora_photographer': ['bookings', 'equipment', 'portfolio', 'leads'],
            'cora_editor': ['bookings', 'feature-hub', 'portfolio'],
            'cora_viewer': ['dashboard', 'bookings'],
            'editor': ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'settings', 'canvas', 'forms', 'emails', 'review_acquisition'],
            'author': ['dashboard', 'bookings', 'equipment'],
            'contributor': ['dashboard', 'bookings'],
            'subscriber': ['dashboard']
        };

        $('#cora-permissions-matrix-table tbody tr.cora-matrix-row:not([data-locked="true"])').each(function() {
            var role = $(this).data('role');
            if (!role) return;
            var defaultList = defaultPermsMap[role] || ['dashboard', 'bookings'];
            
            $(this).find('td[data-feature]').each(function() {
                var feature = $(this).data('feature');
                var level = (defaultList.indexOf(feature) !== -1) ? 'edit' : 'none';
                window.updateCellState(role, feature, level);
            });
        });

        window.coraSavePermissionsMatrix();
        window.coraShowToast('Permissions matrix reset to default settings.');
    };

    window.coraGrantAllSelectedRolePermissions = function() {
        var selectedRow = $('#cora-permissions-matrix-table tbody tr.selected-matrix-role');
        
        if (!selectedRow.length) {
            selectedRow = $('#cora-permissions-matrix-table tbody tr.cora-matrix-row:visible:not([data-locked="true"])').first();
        }
        
        if (!selectedRow.length || selectedRow.data('locked')) {
            window.coraShowToast('Please click a role row first to select it for Grant All.');
            return;
        }

        var role = selectedRow.data('role');
        var roleName = selectedRow.find('.cora-role-title-text').text().trim();
        
        selectedRow.find('td[data-feature]').each(function() {
            var feature = $(this).data('feature');
            window.updateCellState(role, feature, 'edit');
        });
        
        window.coraSavePermissionsMatrix();
        window.coraShowToast('Granted all permissions for role: ' + roleName);
    };

    $(document).on('click', '#cora-permissions-matrix-table tbody tr.cora-matrix-row', function(e) {
        if ($(e.target).closest('.cora-matrix-cell-badge').length) return;
        if ($(this).data('locked')) return;
        
        $('#cora-permissions-matrix-table tbody tr.cora-matrix-row').removeClass('selected-matrix-role bg-zinc-100/80 dark:bg-zinc-800/80');
        $(this).addClass('selected-matrix-role bg-zinc-100/80 dark:bg-zinc-800/80');
    });

    $(document).on('input', '#matrix-role-search', function() {
        var query = $(this).val().toLowerCase().trim();
        $('#cora-permissions-matrix-table tbody tr.cora-matrix-row').each(function() {
            var roleName = $(this).find('.cora-role-title-text').text().toLowerCase();
            var roleKey = $(this).data('role') ? $(this).data('role').toLowerCase() : '';
            if (!query || roleName.indexOf(query) !== -1 || roleKey.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        // Sync mobile swipe cards filter
        $('[data-mobile-role]').each(function() {
            var roleKey = $(this).data('mobile-role') ? $(this).data('mobile-role').toLowerCase() : '';
            var roleName = $(this).find('span').first().text().toLowerCase();
            if (!query || roleName.indexOf(query) !== -1 || roleKey.indexOf(query) !== -1) {
                $(this).show().addClass('shrink-0');
            } else {
                $(this).hide().removeClass('shrink-0');
            }
        });
    });

    window.coraToggleMatrixAccordion = function(accordionId) {
        var $content = $('#content-' + accordionId);
        var $arrow = $('#arrow-' + accordionId);
        if ($content.hasClass('hidden')) {
            $content.removeClass('hidden');
            $arrow.addClass('rotate-180');
        } else {
            $content.addClass('hidden');
            $arrow.removeClass('rotate-180');
        }
    };

    window.coraToggleRoleCardAccordion = function(roleKey) {
        var $content = $('#role-categories-' + roleKey);
        var $arrow = $('#role-arrow-' + roleKey);
        if ($content.hasClass('hidden')) {
            $content.removeClass('hidden');
            $arrow.addClass('rotate-180');
        } else {
            $content.addClass('hidden');
            $arrow.removeClass('rotate-180');
        }
    };

    window.coraChangeMobileRoleAccessLevel = function(roleKey, val) {
        var $select = $('[data-mobile-role="' + roleKey + '"] select');
        // Remove old theme classes
        $select.removeClass('text-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/20 dark:text-indigo-400 border-indigo-100 dark:border-indigo-900/40 text-blue-600 bg-blue-50/50 dark:bg-blue-950/20 dark:text-blue-400 border-blue-100 dark:border-blue-900/40 text-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/40');
        
        // Add new theme classes based on value
        if (val === 'manager') {
            $select.addClass('text-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/20 dark:text-indigo-400 border-indigo-100 dark:border-indigo-900/40');
        } else if (val === 'read_only') {
            $select.addClass('text-blue-600 bg-blue-50/50 dark:bg-blue-950/20 dark:text-blue-400 border-blue-100 dark:border-blue-900/40');
        } else {
            $select.addClass('text-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/20 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/40');
        }
    };

    $(document).on('change', '.cora-permission-checkbox', function() {
        var role = $(this).closest('tr').data('role');
        var feature = $(this).data('feature');
        if (!role || !feature) return;

        var isChecked = this.checked;
        var currentLevel = $(this).closest('td').data('level') || 'none';
        var targetLevel = isChecked ? 'edit' : 'none';
        
        if (currentLevel !== targetLevel) {
            window.updateCellState(role, feature, targetLevel);
            window.coraSavePermissionsMatrix();
        }
    });

    // Platform Redirection and Clipboard Sync Helpers
    window.coraGetReferQuery = function() {
        // Find which tab is currently active (not hidden)
        var activeTab = $('.cora-tab-content:not(.hidden)').attr('id') || 'tab-active-members';
        
        if (activeTab === 'tab-permissions-matrix') {
            return "How do I configure the granular Permissions Matrix (View, Edit, No Access levels) in the Cora Workspace Platform?";
        } else if (activeTab === 'tab-custom-roles') {
            return "How to create custom team roles and manage capabilities inside Cora Workspace?";
        } else if (activeTab === 'tab-attendance-logs') {
            return "How to configure geofenced office location attendance logs and GPS check-ins in Cora Workspace?";
        } else if (activeTab === 'tab-owner-automations') {
            return "How to setup automated owner digests, security alerts, and SMTP templates in Cora Workspace?";
        } else if (activeTab === 'tab-pending-invites') {
            return "How to invite new users, manage pending invitations, and resend links in Cora Workspace?";
        } else {
            return "How to manage team members, active accounts, and edit profile details in Cora Workspace?";
        }
    };

    window.coraAskExternalPlatform = function(platform) {
        var query = window.coraGetReferQuery();
        
        // Copy to clipboard dynamically
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(query).then(function() {
                // Clipboard copy successful
            }).catch(function() {
                // Ignore fallback
            });
        }
        
        var url = '';
        var platformName = '';
        if (platform === 'openai') {
            platformName = 'ChatGPT';
            url = 'https://chatgpt.com/?q=' + encodeURIComponent(query);
        } else if (platform === 'gemini') {
            platformName = 'Gemini';
            url = 'https://gemini.google.com/app?q=' + encodeURIComponent(query);
        } else if (platform === 'claude') {
            platformName = 'Claude';
            url = 'https://claude.ai/';
        } else if (platform === 'perplexity') {
            platformName = 'Perplexity';
            url = 'https://www.perplexity.ai/?q=' + encodeURIComponent(query);
        }
        
        if (url) {
            window.coraShowToast('Opening ' + platformName + '... Query copied to clipboard!');
            window.open(url, '_blank');
        }
    };

    // Drawer Open/Close & Tab Switching helpers
    window.openAiTrainerDrawer = function(platform) {
        window.currentLlmPlatform = platform || 'gemini';
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
        $('#cora-ai-trainer-drawer').removeClass('collapsed hidden translate-x-full pointer-events-none').addClass('translate-x-0').css({
            'display': 'flex',
            'pointer-events': 'auto',
            'transform': 'translateX(0)',
            'visibility': 'visible'
        });
        $('#cora-drawer-backdrop').removeClass('hidden').css({
            'display': 'block',
            'pointer-events': 'auto'
        });
        window.switchTrainerTab('chat');
    };

    window.closeAiTrainerDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('#cora-ai-trainer-drawer').addClass('collapsed hidden translate-x-full').removeClass('translate-x-0');
        }
        $('#cora-ai-trainer-drawer').removeClass('translate-x-0').addClass('translate-x-full').css({
            'display': 'none',
            'pointer-events': 'none',
            'transform': 'translateX(100%)',
            'visibility': 'hidden'
        });
    };

    window.openPermissionsVideoDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
        $('#cora-permissions-video-drawer').removeClass('collapsed hidden translate-x-full pointer-events-none').addClass('translate-x-0').css({
            'display': 'flex',
            'pointer-events': 'auto',
            'transform': 'translateX(0)',
            'visibility': 'visible'
        });
        $('#cora-drawer-backdrop').removeClass('hidden').css({
            'display': 'block',
            'pointer-events': 'auto'
        });
    };

    window.closePermissionsVideoDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('#cora-permissions-video-drawer').addClass('collapsed hidden translate-x-full').removeClass('translate-x-0');
        }
        $('#cora-permissions-video-drawer').removeClass('translate-x-0').addClass('translate-x-full').css({
            'display': 'none',
            'pointer-events': 'none',
            'transform': 'translateX(100%)',
            'visibility': 'hidden'
        });
    };

    window.switchTrainerTab = function(tab) {
        $('#tab-btn-trainer-chat, #tab-btn-trainer-context, #tab-btn-trainer-keys')
            .removeClass('border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50 font-semibold')
            .addClass('border-transparent text-zinc-400 font-medium');

        $('#tab-btn-trainer-' + tab)
            .addClass('border-zinc-950 dark:border-zinc-50 text-zinc-950 dark:text-zinc-50 font-semibold')
            .removeClass('border-transparent text-zinc-400 font-medium');

        $('#trainer-content-chat, #trainer-content-context, #trainer-content-keys').addClass('hidden');
        $('#trainer-content-' + tab).removeClass('hidden');
    };

    // Chat Doubts Box
    window.submitChatDoubt = function() {
        var $input = $('#doubts-chat-input');
        var query = $input.val().trim();
        if (!query) return;

        $input.val('');
        window.appendChatMessage('user', query);

        var $thinking = window.appendChatMessage('ai', '<span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-zinc-600 dark:bg-zinc-400 animate-bounce"></span><span class="w-1.5 h-1.5 rounded-full bg-zinc-600 dark:bg-zinc-400 animate-bounce" style="animation-delay:0.2s"></span><span class="w-1.5 h-1.5 rounded-full bg-zinc-600 dark:bg-zinc-400 animate-bounce" style="animation-delay:0.4s"></span></span>');

        var platform = window.currentLlmPlatform || 'gemini';

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_ask_llm_doubt',
            doubt: query,
            platform: platform,
            security: coraREData.ajaxNonce
        }, function(res) {
            $thinking.remove();
            if (res.success && res.data && res.data.reply) {
                window.appendChatMessage('ai', res.data.reply);
            } else {
                var msg = (res.data && res.data.message) ? res.data.message : 'Sorry, I encountered an error processing your query.';
                window.appendChatMessage('ai', '<span class="text-rose-500 font-semibold">' + msg + '</span>');
            }
        }).fail(function() {
            $thinking.remove();
            window.appendChatMessage('ai', '<span class="text-rose-500 font-semibold">Network error. Please try again.</span>');
        });
    };

    window.handleChatKeyDown = function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            window.submitChatDoubt();
        }
    };

    window.sendQuickDoubt = function(text) {
        $('#doubts-chat-input').val(text);
        window.submitChatDoubt();
    };

    window.appendChatMessage = function(sender, text) {
        var $chat = $('#doubts-chat-messages');
        var isUser = (sender === 'user');
        var bubbleHtml = '';

        if (isUser) {
            bubbleHtml = '<div class="flex gap-2.5 items-start justify-end">' +
                '<div class="flex-1 bg-zinc-950 dark:bg-zinc-100 rounded-xl p-3 text-white dark:text-zinc-950 leading-relaxed max-w-[85%] text-right">' +
                window.formatMarkdown(text) +
                '</div>' +
                '</div>';
        } else {
            bubbleHtml = '<div class="flex gap-2.5 items-start">' +
                '<div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-800 dark:text-zinc-200 border border-zinc-200/50 dark:border-zinc-800 shrink-0">' +
                '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2c.13 2.9 2.1 4.87 5 5-2.9.13-4.87 2.1-5 5-.13-2.9-2.1-4.87-5-5 2.9-.13 4.87-2.1 5-5Z" /></svg>' +
                '</div>' +
                '<div class="flex-1 bg-zinc-50 dark:bg-zinc-850 rounded-xl p-3 border border-zinc-200/50 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 leading-relaxed max-w-[85%] markdown-body">' +
                window.formatMarkdown(text) +
                '</div>' +
                '</div>';
        }

        var $bubble = $(bubbleHtml);
        $chat.append($bubble);
        $chat.scrollTop($chat[0].scrollHeight);
        return $bubble;
    };

    window.formatMarkdown = function(text) {
        if (!text) return '';
        var escaped = text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
        
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        escaped = escaped.replace(/`(.*?)`/g, '<code class="bg-zinc-105 dark:bg-zinc-800 px-1 py-0.5 rounded text-[11px] font-mono">$1</code>');
        escaped = escaped.replace(/^\s*[-*]\s+(.*)$/gm, '<li>$1</li>');
        escaped = escaped.replace(/(<li>.*<\/li>)/g, '<ul class="list-disc pl-4 space-y-1 my-1">$1</ul>');
        escaped = escaped.replace(/\n/g, '<br>');
        return escaped;
    };

    window.saveTrainerContext = function() {
        var promptVal = $('#trainer-context-prompt').val();
        window.coraShowToast('Saving training context...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_training_prompt',
            prompt: promptVal,
            security: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data && res.data.message ? res.data.message : 'Training context saved successfully.');
            } else {
                window.coraShowToast((res.data && res.data.message) ? res.data.message : 'Failed to save training context.');
            }
        }).fail(function() {
            window.coraShowToast('Network error saving training context.');
        });
    };

    // YouTube Seek walkthrough
    window.seekWalkthroughVideo = function(seconds) {
        var player = document.getElementById('permissions-walkthrough-player');
        if (player) {
            player.contentWindow.postMessage(JSON.stringify({
                "event": "command",
                "func": "seekTo",
                "args": [seconds, true]
            }), "*");
            player.contentWindow.postMessage(JSON.stringify({
                "event": "command",
                "func": "playVideo",
                "args": []
            }), "*");
        }
    };

    // Attendance Logs Mobile Filter Toggle & Sync Handlers
    window.toggleAttendanceMobileFilters = function() {
        $('#attendance-filter-panel').toggleClass('hidden');
        $('#attendance-filter-toggle').toggleClass('bg-zinc-200/60 dark:bg-zinc-700/60');
    };

    window.syncAttendanceFilterAndRun = function(type) {
        if (type === 'user') {
            $('#attendance-filter-user').val($('#attendance-filter-user-mobile').val());
        } else if (type === 'period') {
            var periodVal = $('#attendance-filter-period-mobile').val();
            $('#attendance-filter-period').val(periodVal);
            if (periodVal === 'custom') {
                $('#attendance-custom-date-container-mobile').removeClass('hidden');
                $('#attendance-custom-date-container').removeClass('hidden');
            } else {
                $('#attendance-custom-date-container-mobile').addClass('hidden');
                $('#attendance-custom-date-container').addClass('hidden');
            }
        } else if (type === 'date-start') {
            $('#attendance-date-start').val($('#attendance-date-start-mobile').val());
        } else if (type === 'date-end') {
            $('#attendance-date-end').val($('#attendance-date-end-mobile').val());
        } else if (type === 'event') {
            $('#attendance-filter-event').val($('#attendance-filter-event-mobile').val());
        }
        fetchAttendanceLogs();
    };

    var _origHandlePeriodFilterChange = window.handlePeriodFilterChange;
    window.handlePeriodFilterChange = function() {
        var periodVal = $('#attendance-filter-period').val();
        $('#attendance-filter-period-mobile').val(periodVal);
        if (periodVal === 'custom') {
            $('#attendance-custom-date-container').removeClass('hidden');
            $('#attendance-custom-date-container-mobile').removeClass('hidden');
        } else {
            $('#attendance-custom-date-container').addClass('hidden');
            $('#attendance-custom-date-container-mobile').addClass('hidden');
        }
        if (typeof _origHandlePeriodFilterChange === 'function') {
            _origHandlePeriodFilterChange();
        } else {
            fetchAttendanceLogs();
        }
    };

    // Owner Automation Toggles & Test Dispatch Handlers
    window.coraToggleAutomation = function(key, enable) {
        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_toggle_owner_automation',
            key: key,
            enable: enable ? 1 : 0,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message);
            }
        });
    };

    window.coraTestDispatchAutomation = function(key) {
        window.coraShowToast('Dispatching test report to Workspace Owner email...');
        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_test_dispatch_automation',
            key: key,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message);
            } else {
                window.coraShowToast(res.data.message || 'Dispatch failed.');
            }
        });
    };

    // Custom Roles Handlers
    function handleApplyBaseTemplate(tmplKey) {
        if (!tmplKey) return;
        var permsMap = {
            'cora_branch_manager': { access: 'manager', perms: ['crm_leads', 'showings_bookings', 'financials', 'media_vault', 'attendance'] },
            'cora_re_agent': { access: 'contributor', perms: ['crm_leads', 'showings_bookings', 'media_vault', 'attendance'] },
            'cora_re_assistant': { access: 'contributor', perms: ['showings_bookings', 'attendance'] },
            'cora_studio_manager': { access: 'manager', perms: ['showings_bookings', 'media_vault', 'equipment', 'financials', 'ai_suite', 'attendance'] },
            'cora_photographer': { access: 'contributor', perms: ['showings_bookings', 'media_vault', 'equipment', 'attendance'] },
            'cora_editor': { access: 'contributor', perms: ['media_vault', 'ai_suite'] },
            'cora_viewer': { access: 'read_only', perms: ['showings_bookings', 'media_vault'] }
        };
        var config = permsMap[tmplKey];
        if (config) {
            $('#custom-role-access-level').val(config.access);
            $('.custom-role-perm-cb').each(function() {
                var val = $(this).val();
                $(this).prop('checked', config.perms.indexOf(val) !== -1);
            });
        }
    }

    function handleCreateCustomRole(e) {
        e.preventDefault();
        var name = $('#custom-role-name').val().trim();
        if (!name) return;

        var baseTmpl = $('#custom-role-base-template').val();
        var accessLvl = $('#custom-role-access-level').val();
        var maxQuota = $('#custom-role-max-quota').val();
        var perms = [];
        $('.custom-role-perm-cb:checked').each(function() {
            perms.push($(this).val());
        });

        var btn = $('#create-role-submit-btn');
        btn.prop('disabled', true).text('Creating role...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_add_custom_role',
            role_name: name,
            base_template: baseTmpl,
            access_level: accessLvl,
            max_quota: maxQuota,
            permissions: perms,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message || 'Custom role created successfully.');
                setTimeout(function() { window.location.reload(); }, 800);
            } else {
                window.coraShowToast(res.data.message || 'Failed to create role.');
                btn.prop('disabled', false).text('Create Role');
            }
        }).fail(function() {
            window.coraShowToast('Network error creating custom role.');
            btn.prop('disabled', false).text('Create Role');
        });
    }

    function openCreateCustomRoleDrawer(baseTemplate) {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('aside[id$="-drawer"]').addClass('collapsed');
        }

        if (baseTemplate) {
            $('#custom-role-base-template').val(baseTemplate);
            if (typeof handleApplyBaseTemplate === 'function') {
                handleApplyBaseTemplate(baseTemplate);
            }
        }

        $('#cora-create-custom-role-drawer').removeClass('collapsed hidden translate-x-full pointer-events-none').addClass('translate-x-0').css({
            'display': 'flex',
            'pointer-events': 'auto',
            'transform': 'translateX(0)',
            'visibility': 'visible'
        });
        $('#cora-drawer-backdrop').removeClass('hidden').css({
            'display': 'block',
            'pointer-events': 'auto'
        });
    }

    function closeCreateCustomRoleDrawer() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('#cora-create-custom-role-drawer').addClass('collapsed hidden translate-x-full').removeClass('translate-x-0');
        }
        $('#cora-create-custom-role-drawer').removeClass('translate-x-0').addClass('translate-x-full').css({
            'display': 'none',
            'pointer-events': 'none',
            'transform': 'translateX(100%)',
            'visibility': 'hidden'
        });
        $('#cora-drawer-backdrop').addClass('hidden').css({
            'display': 'none',
            'pointer-events': 'none'
        });
    }

    window.openCreateCustomRoleDrawer = openCreateCustomRoleDrawer;
    window.closeCreateCustomRoleDrawer = closeCreateCustomRoleDrawer;

    function openEditCustomRoleDrawer(roleData) {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('aside[id$="-drawer"]').addClass('collapsed');
        }

        $('#edit-custom-role-key').val(roleData.role_key || '');
        $('#edit-custom-role-name').val(roleData.role_name || '');
        $('#edit-custom-role-access-level').val(roleData.access_level || 'contributor');
        $('#edit-custom-role-max-quota').val(roleData.max_quota !== null && roleData.max_quota !== undefined ? roleData.max_quota : '');

        var perms = roleData.permissions || [];
        $('.edit-custom-role-perm-cb').each(function() {
            var val = $(this).val();
            $(this).prop('checked', perms.indexOf(val) !== -1 || perms.indexOf(val.replace('_', '-')) !== -1);
        });

        $('#cora-edit-custom-role-drawer').removeClass('collapsed hidden translate-x-full pointer-events-none').addClass('translate-x-0').css({
            'display': 'flex',
            'pointer-events': 'auto',
            'transform': 'translateX(0)',
            'visibility': 'visible'
        });
        $('#cora-drawer-backdrop').removeClass('hidden').css({
            'display': 'block',
            'pointer-events': 'auto'
        });
    }
    window.openEditCustomRoleDrawer = openEditCustomRoleDrawer;

    function closeEditCustomRoleDrawer() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('#cora-edit-custom-role-drawer').addClass('collapsed hidden translate-x-full').removeClass('translate-x-0');
        }
        $('#cora-edit-custom-role-drawer').removeClass('translate-x-0').addClass('translate-x-full').css({
            'display': 'none',
            'pointer-events': 'none',
            'transform': 'translateX(100%)',
            'visibility': 'hidden'
        });
        $('#cora-drawer-backdrop').addClass('hidden').css({
            'display': 'none',
            'pointer-events': 'none'
        });
    }
    window.closeEditCustomRoleDrawer = closeEditCustomRoleDrawer;

    $(document).on('click', '.cora-edit-custom-role-btn', function(e) {
        e.preventDefault();
        var raw = $(this).attr('data-custom-role');
        if (!raw) return;
        try {
            var roleData = JSON.parse(raw);
            openEditCustomRoleDrawer(roleData);
        } catch(err) {
            console.error('Error parsing custom role payload:', err);
        }
    });

    function handleSaveCustomRolePermissions(e) {
        e.preventDefault();
        var roleKey = $('#edit-custom-role-key').val();
        var roleName = $('#edit-custom-role-name').val().trim();
        var accessLvl = $('#edit-custom-role-access-level').val();
        var maxQuota = $('#edit-custom-role-max-quota').val();
        var perms = [];
        $('.edit-custom-role-perm-cb:checked').each(function() {
            perms.push($(this).val());
        });

        var btn = $('#save-custom-role-btn');
        btn.prop('disabled', true).css('opacity', '0.7');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_custom_role_permissions',
            role_key: roleKey,
            role_name: roleName,
            access_level: accessLvl,
            max_quota: maxQuota,
            permissions: perms,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            btn.prop('disabled', false).css('opacity', '1');
            if (res.success) {
                window.coraShowToast(res.data.message || 'Permissions updated successfully.');
                closeEditCustomRoleDrawer();
                setTimeout(function() { window.location.reload(); }, 800);
            } else {
                window.coraShowToast(res.data.message || 'Failed to save role permissions.');
            }
        }).fail(function() {
            btn.prop('disabled', false).css('opacity', '1');
            window.coraShowToast('Network error saving custom role.');
        });
    }

    function handleDuplicateCustomRole(roleKey) {
        if (!roleKey) return;
        window.coraShowToast('Duplicating custom role...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_duplicate_custom_role',
            role_key: roleKey,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message || 'Custom role duplicated successfully.');
                setTimeout(function() { window.location.reload(); }, 800);
            } else {
                window.coraShowToast(res.data.message || 'Failed to duplicate role.');
            }
        }).fail(function() {
            window.coraShowToast('Network error duplicating custom role.');
        });
    }
    window.handleDuplicateCustomRole = handleDuplicateCustomRole;

    var coraPendingRoleDeletes = {};
    function handleDeleteCustomRole(roleKey, btn) {
        if (!coraPendingRoleDeletes[roleKey]) {
            coraPendingRoleDeletes[roleKey] = true;
            if (btn) $(btn).text('Confirm Delete').addClass('text-red-700 dark:text-red-400 underline font-extrabold');
            window.coraShowToast('Click Confirm Delete to remove this custom role.', 'info');
            setTimeout(function() {
                coraPendingRoleDeletes[roleKey] = false;
                if (btn) $(btn).text('Delete').removeClass('text-red-700 dark:text-red-400 underline font-extrabold');
            }, 4000);
            return;
        }

        $.post(coraREData.ajaxUrl, {
            action: 'cora_delete_custom_role',
            role_key: roleKey,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message || 'Custom role deleted.');
                setTimeout(function() { window.location.reload(); }, 800);
            } else {
                window.coraShowToast(res.data.message || 'Failed to delete role.');
            }
        }).fail(function() {
            window.coraShowToast('Network error deleting role.');
        });
    }
    window.handleDeleteCustomRole = handleDeleteCustomRole;

    // ==========================================
    // ATTENDANCE LOGS TAB LOGIC
    // ==========================================
    var isAttendanceAdmin = <?php echo $is_attendance_admin ? 'true' : 'false'; ?>;
    var loggedInUserName = <?php echo json_encode( wp_get_current_user()->display_name ); ?>;

    function openAttendanceReportsDrawer() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('aside[id$="-drawer"]').addClass('collapsed hidden');
        }
        $('#cora-attendance-reports-drawer').removeClass('collapsed hidden translate-x-full pointer-events-none').addClass('translate-x-0').css({
            'display': 'flex',
            'pointer-events': 'auto',
            'transform': 'translateX(0)',
            'visibility': 'visible'
        });
        $('#cora-drawer-backdrop').removeClass('hidden').css({
            'display': 'block',
            'pointer-events': 'auto'
        });
    }
    window.openAttendanceReportsDrawer = openAttendanceReportsDrawer;

    function closeAttendanceReportsDrawer() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('#cora-attendance-reports-drawer').addClass('collapsed hidden translate-x-full').removeClass('translate-x-0');
        }
        $('#cora-attendance-reports-drawer').removeClass('translate-x-0').addClass('translate-x-full').css({
            'display': 'none',
            'pointer-events': 'none',
            'transform': 'translateX(100%)',
            'visibility': 'hidden'
        });
        $('#cora-drawer-backdrop').addClass('hidden').css({
            'display': 'none',
            'pointer-events': 'none'
        });
    }
    window.closeAttendanceReportsDrawer = closeAttendanceReportsDrawer;

    function handlePeriodFilterChange() {
        var val = $('#attendance-filter-period').val();
        if (val === 'custom') {
            $('#attendance-custom-date-container').removeClass('hidden');
        } else {
            $('#attendance-custom-date-container').addClass('hidden');
        }
        fetchAttendanceLogs();
    }

    function handleReportHorizonChange() {
        var val = $('#attendance-report-horizon').val();
        if (val === 'custom') {
            $('#attendance-report-custom-dates').removeClass('hidden');
        } else {
            $('#attendance-report-custom-dates').addClass('hidden');
        }
    }

    function fetchAttendanceLogs() {
        var userId    = $('#attendance-filter-user').val() || '';
        var period    = $('#attendance-filter-period').val() || 'all';
        var startDate = $('#attendance-date-start').val() || '';
        var endDate   = $('#attendance-date-end').val() || '';
        var eventType = $('#attendance-filter-event').val() || 'all';

        $.post(coraREData.ajaxUrl, { 
            action: 'cora_get_attendance_logs', 
            nonce: coraREData.ajaxNonce,
            user_id: userId,
            period: period,
            start_date: startDate,
            end_date: endDate,
            event_type: eventType
        }, function(res) {
            if (res.success && res.data.logs) {
                var tbody = $('#cora-user-attendance-table-body');
                tbody.empty();
                
                var displayLogs = res.data.logs;
                if (!isAttendanceAdmin) {
                    displayLogs = res.data.logs.filter(function(log) {
                        return (log.user || '').toLowerCase() === loggedInUserName.toLowerCase();
                    });
                }
                
                var searchQuery = ($('#attendance-log-search').val() || '').toLowerCase().trim();
                if (searchQuery) {
                    displayLogs = displayLogs.filter(function(log) {
                        return (log.user || '').toLowerCase().indexOf(searchQuery) !== -1;
                    });
                }

                if (displayLogs.length === 0) {
                    var colCount = isAttendanceAdmin ? 5 : 4;
                    tbody.append('<tr><td colspan="' + colCount + '" class="px-5 py-8 text-center text-zinc-400 dark:text-zinc-500">No attendance records found for selected filters.</td></tr>');
                } else {
                    displayLogs.slice().reverse().forEach(function(log) {
                        var dateObj = new Date(log.timestamp);
                        var timeStr = dateObj.toLocaleString();
                        var typeLabel = log.type === 'in' 
                            ? '<span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 rounded-md text-[9px] font-bold uppercase select-none">Punch In</span>' 
                            : '<span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-400 rounded-md text-[9px] font-bold uppercase select-none">Punch Out</span>';
                        
                        var locLink = 'Unknown';
                        if (log.lat && log.lng) {
                            var mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' + log.lat + ',' + log.lng;
                            locLink = '<a href="' + mapsUrl + '" target="_blank" class="hover:underline flex items-center gap-1 text-zinc-500 hover:text-zinc-850 dark:hover:text-zinc-100"><svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg> ' + parseFloat(log.lat).toFixed(4) + ', ' + parseFloat(log.lng).toFixed(4) + '</a>';
                        }
                        
                        var geofenceCell = '';
                        if (isAttendanceAdmin) {
                            var geofenceLabel = '—';
                            if (log.geofence === 'verified') {
                                geofenceLabel = '<span class="px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-450 rounded text-[9px] font-bold">Verified (' + (log.distance || '') + ')</span>';
                            } else if (log.geofence === 'disabled') {
                                geofenceLabel = '<span class="px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 rounded text-[9px] font-medium">Standard</span>';
                            }
                            geofenceCell = '<td class="px-5 py-3" data-label="Geofence Status">' + geofenceLabel + '</td>';
                        }
                        
                        tbody.append(`
                            <tr class="hover:bg-zinc-50/30 dark:hover:bg-zinc-850/30 transition-colors cora-attendance-row" data-user="${(log.user || '').toLowerCase()}">
                                <td class="px-5 py-3 font-bold text-zinc-900 dark:text-zinc-100" data-label="User Name">${log.user}</td>
                                <td class="px-5 py-3 text-zinc-500 dark:text-zinc-400 font-medium" data-label="Date & Time">${timeStr}</td>
                                <td class="px-5 py-3" data-label="Event Type">${typeLabel}</td>
                                <td class="px-5 py-3 font-semibold" data-label="GPS Coordinates">${locLink}</td>
                                ${geofenceCell}
                            </tr>
                        `);
                    });
                }
                
                // Compute analytics metrics dynamically for admins/managers
                if (isAttendanceAdmin) {
                    var uniqueUsersToday = {};
                    var latePunchesToday = 0;
                    var todayStart = new Date();
                    todayStart.setHours(0,0,0,0);
                    var todayEnd = new Date();
                    todayEnd.setHours(23,59,59,999);
                    
                    res.data.logs.forEach(function(log) {
                        var logTime = new Date(log.timestamp);
                        if (logTime >= todayStart && logTime <= todayEnd) {
                            uniqueUsersToday[log.user] = true;
                            
                            if (log.type === 'in') {
                                var hours = logTime.getHours();
                                var minutes = logTime.getMinutes();
                                // Checked in after 9:30 AM
                                if (hours > 9 || (hours === 9 && minutes > 30)) {
                                    latePunchesToday++;
                                }
                            }
                        }
                    });
                    
                    $('#stat-active-today').text(Object.keys(uniqueUsersToday).length);
                    $('#stat-late-punches').text(latePunchesToday);
                }
            }
        });
    }

    function logUserPunch(type) {
        var statusDiv = $('#cora-user-punch-status');
        statusDiv.removeClass('hidden text-red-500 dark:text-red-400').addClass('text-zinc-500 dark:text-zinc-400').text('Acquiring browser GPS location...').show();
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var logData = {
                    type: type,
                    timestamp: Date.now(),
                    lat: lat,
                    lng: lng,
                    user: 'Current User'
                };
                
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_save_attendance',
                    nonce: coraREData.ajaxNonce,
                    log: JSON.stringify(logData)
                }, function(res) {
                    if (res.success) {
                        window.coraShowToast("Punch logged successfully");
                        statusDiv.hide();
                        fetchAttendanceLogs();
                    } else {
                        statusDiv.removeClass('text-zinc-500').addClass('text-red-500 dark:text-red-400').text(res.data.message || 'Failed to save punch.');
                    }
                });
            }, function(error) {
                statusDiv.removeClass('text-zinc-500').addClass('text-red-500 dark:text-red-400').text('Location access denied or unavailable.');
            });
        } else {
            statusDiv.removeClass('text-zinc-500').addClass('text-red-500 dark:text-red-400').text('Geolocation is not supported by your browser.');
        }
    }

    function filterAttendanceLogs() {
        fetchAttendanceLogs();
    }

    // ==========================================
    // GEOFENCE DRAWER HANDLERS & PREVIEW
    // ==========================================
    var coraGeofenceMeta = {
        address: <?php echo json_encode( $office_loc['address'] ); ?>,
        maps_url: <?php echo json_encode( $office_loc['maps_url'] ); ?>,
        radius: <?php echo json_encode( intval( $office_loc['radius'] ) ); ?>,
        lat: <?php echo json_encode( $office_loc['lat'] ); ?>,
        lng: <?php echo json_encode( $office_loc['lng'] ); ?>
    };

    var activeGeofenceMode = 'address';

    function switchGeofenceMode(mode) {
        activeGeofenceMode = mode;
        if (mode === 'address') {
            $('#geofence-mode-address-btn').addClass('bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-sm').removeClass('text-zinc-500 dark:text-zinc-400');
            $('#geofence-mode-url-btn').removeClass('bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-sm').addClass('text-zinc-500 dark:text-zinc-400');
            $('#geofence-address-container').removeClass('hidden');
            $('#geofence-url-container').addClass('hidden');
        } else {
            $('#geofence-mode-url-btn').addClass('bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-sm').removeClass('text-zinc-500 dark:text-zinc-400');
            $('#geofence-mode-address-btn').removeClass('bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-sm').addClass('text-zinc-500 dark:text-zinc-400');
            $('#geofence-url-container').removeClass('hidden');
            $('#geofence-address-container').addClass('hidden');
        }
        updateMapPreviewFromInput();
    }

    function selectGeofenceRadius(r) {
        r = parseInt(r, 10) || 500;
        $('#geofence-radius-input').val(r);
        var label = r >= 1000 ? (r / 1000) + 'km' : r + 'm';
        $('#geofence-selected-radius-label').text(label);

        $('.geofence-radius-pill').each(function() {
            var pillRadius = parseInt($(this).data('radius'), 10);
            if (pillRadius === r) {
                $(this).addClass('bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 border-zinc-950 dark:border-zinc-100 shadow-sm')
                       .removeClass('border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800');
            } else {
                $(this).removeClass('bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 border-zinc-950 dark:border-zinc-100 shadow-sm')
                       .addClass('border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800');
            }
        });
    }

    function updateMapPreviewFromInput() {
        var query = '';
        if (activeGeofenceMode === 'address') {
            query = $('#geofence-address-input').val().trim();
        } else {
            query = $('#geofence-maps-url-input').val().trim();
        }

        if (!query) {
            if (coraGeofenceMeta.address) {
                query = coraGeofenceMeta.address;
            } else if (coraGeofenceMeta.maps_url) {
                query = coraGeofenceMeta.maps_url;
            } else if (coraGeofenceMeta.lat && coraGeofenceMeta.lng) {
                query = coraGeofenceMeta.lat + ',' + coraGeofenceMeta.lng;
            } else {
                query = 'New Delhi, India';
            }
        }

        var mapUrl = 'https://maps.google.com/maps?q=' + encodeURIComponent(query) + '&t=&z=15&ie=UTF8&iwloc=&output=embed';
        $('#geofence-map-frame').attr('src', mapUrl);
    }

    window.openGeofenceDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            jQuery('aside[id$="-drawer"]').addClass('collapsed hidden').css({'display':'none'});
        }
        jQuery('#geofence-address-input').val(coraGeofenceMeta.address || '');
        jQuery('#geofence-maps-url-input').val(coraGeofenceMeta.maps_url || '');

        var initialRadius = parseInt(coraGeofenceMeta.radius, 10) || 500;
        selectGeofenceRadius(initialRadius);

        if (coraGeofenceMeta.maps_url && !coraGeofenceMeta.address) {
            switchGeofenceMode('url');
        } else {
            switchGeofenceMode('address');
        }

        jQuery('#cora-geofence-drawer').removeClass('collapsed hidden translate-x-full pointer-events-none').addClass('translate-x-0').css({
            'display': 'flex',
            'pointer-events': 'auto',
            'transform': 'translateX(0)',
            'visibility': 'visible'
        });
        jQuery('#cora-drawer-backdrop').removeClass('hidden').css({
            'display': 'block',
            'pointer-events': 'auto'
        });
     };

     window.closeGeofenceDrawer = function() {
         if (typeof window.coraCloseAllDrawers === 'function') {
             window.coraCloseAllDrawers();
         } else {
             jQuery('#cora-geofence-drawer').addClass('collapsed hidden translate-x-full').removeClass('translate-x-0');
         }
         jQuery('#cora-geofence-drawer').removeClass('translate-x-0').addClass('translate-x-full').css({
             'display': 'none',
             'pointer-events': 'none',
             'transform': 'translateX(100%)',
             'visibility': 'hidden'
         });
         jQuery('#cora-drawer-backdrop').addClass('hidden').css({
             'display': 'none',
             'pointer-events': 'none'
         });
     };

    function handleSaveGeofence(e) {
        if (e && e.preventDefault) e.preventDefault();

        var address = $('#geofence-address-input').val().trim();
        var mapsUrl = $('#geofence-maps-url-input').val().trim();
        var radius  = parseInt($('#geofence-radius-input').val(), 10) || 500;

        if (!address && !mapsUrl) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Please enter an address or Google Maps link.', 'error');
            }
            return;
        }

        var btn = $('#save-geofence-btn');
        btn.prop('disabled', true).css('opacity', '0.7');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_update_geofence',
            address: address,
            maps_url: mapsUrl,
            radius: radius,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            btn.prop('disabled', false).css('opacity', '1');
            if (res.success) {
                coraGeofenceMeta.address = res.data.address;
                coraGeofenceMeta.maps_url = res.data.maps_url;
                coraGeofenceMeta.radius = res.data.radius;
                coraGeofenceMeta.lat = res.data.lat;
                coraGeofenceMeta.lng = res.data.lng;

                var rDisplay = radius >= 1000 ? (radius / 1000) + 'km' : radius + 'm';
                $('#cora-geofence-pill').text(rDisplay + ' Enforced');
                $('#stat-geofence-status').text(rDisplay + ' Enforced');
                $('#cora-geofence-current-address').text(res.data.address || res.data.maps_url || (res.data.lat + ', ' + res.data.lng) || 'Not Configured');

                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(res.data.message || 'Office location & geofence updated successfully.', 'success');
                }
                closeGeofenceDrawer();
            } else {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(res.data.message || 'Failed to update location.', 'error');
                }
            }
        }).fail(function() {
            btn.prop('disabled', false).css('opacity', '1');
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Network error while saving geofence.', 'error');
            }
        });
    }

    // Dynamic crons debugger tool action trigger
    function triggerCronAction(action) {
        window.coraShowToast("Triggering email automation...");
        $.post(coraREData.ajaxUrl, {
            action: 'cora_trigger_attendance_automation',
            cron_action: action,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message);
            } else {
                window.coraShowToast(res.data.message || "Failed to trigger cron.");
            }
        });
    }
    window.triggerCronAction = triggerCronAction;

    // Export Table Records to CSV
    function exportAttendanceCSV() {
        var userId    = $('#attendance-filter-user').val() || '';
        var period    = $('#attendance-filter-period').val() || 'all';
        var startDate = $('#attendance-date-start').val() || '';
        var endDate   = $('#attendance-date-end').val() || '';
        var eventType = $('#attendance-filter-event').val() || 'all';

        $.post(coraREData.ajaxUrl, {
            action: 'cora_get_attendance_logs',
            nonce: coraREData.ajaxNonce,
            user_id: userId,
            period: period,
            start_date: startDate,
            end_date: endDate,
            event_type: eventType
        }, function(res) {
            if (res.success && res.data.logs) {
                var csv = 'User Name,Date & Time,Event Type,Latitude,Longitude,Geofence Status,Enforced Distance\n';
                res.data.logs.forEach(function(log) {
                    var dateObj = new Date(log.timestamp);
                    var dateStr = dateObj.toLocaleString().replace(/,/g, '');
                    var geofence = log.geofence || 'disabled';
                    var dist = log.distance || '—';
                    csv += [
                        '"' + (log.user || '').replace(/"/g, '""') + '"',
                        '"' + dateStr + '"',
                        '"' + (log.type === 'in' ? 'Punch In' : 'Punch Out') + '"',
                        log.lat || '',
                        log.lng || '',
                        '"' + geofence.toUpperCase() + '"',
                        '"' + dist + '"'
                    ].join(',') + '\n';
                });
                
                var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement("a");
                var url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", "cora_attendance_report_" + new Date().toISOString().slice(0, 10) + ".csv");
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast("Attendance CSV exported successfully");
                }
            }
        });
    }
    window.exportAttendanceCSV = exportAttendanceCSV;
    window.exportAttendanceReport = exportAttendanceCSV;

    function generatePrintableAttendanceReport() {
        var horizon    = $('#attendance-report-horizon').val() || 'daily';
        var targetUser = $('#attendance-report-target').val() || 'all';
        var startDate  = $('#attendance-report-start-date').val() || '';
        var endDate    = $('#attendance-report-end-date').val() || '';

        $.post(coraREData.ajaxUrl, {
            action: 'cora_generate_attendance_report',
            nonce: coraREData.ajaxNonce,
            report_type: 'printable',
            horizon: horizon,
            target_user: targetUser,
            start_date: startDate,
            end_date: endDate
        }, function(res) {
            if (res.success && res.data.logs) {
                var printWindow = window.open('', '_blank', 'height=600,width=800');
                var html = '<html><head><title>Cora Attendance Summary Report</title>';
                html += '<style>body{font-family:-apple-system,BlinkMacSystemFont,sans-serif;padding:20px;color:#18181b;} h1{font-size:20px;} table{width:100%;border-collapse:collapse;margin-top:15px;font-size:12px;} th,td{border:1px solid #e4e4e7;padding:8px;text-align:left;} th{background:#f4f4f5;font-weight:bold;}</style>';
                html += '</head><body>';
                html += '<h1>Cora Attendance Report (' + horizon.toUpperCase() + ')</h1>';
                html += '<p>Generated on: ' + new Date().toLocaleString() + '</p>';
                html += '<table><thead><tr><th>User</th><th>Timestamp</th><th>Type</th><th>Geofence</th></tr></thead><tbody>';
                
                res.data.logs.forEach(function(l) {
                    var d = new Date(l.timestamp).toLocaleString();
                    html += '<tr><td>' + (l.user || '') + '</td><td>' + d + '</td><td>' + (l.type === 'in' ? 'Punch In' : 'Punch Out') + '</td><td>' + (l.geofence || 'Standard') + '</td></tr>';
                });

                html += '</tbody></table></body></html>';
                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(function(){ printWindow.print(); }, 250);

                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast("Printable attendance report generated");
                }
            }
        });
    }
    window.generatePrintableAttendanceReport = generatePrintableAttendanceReport;

    function sendAttendanceEmailDigest() {
        var horizon   = $('#attendance-report-horizon').val() || 'daily';
        var targetUser = $('#attendance-report-target').val() || 'all';
        var recipient = $('#attendance-report-email-recipient').val() || '';

        $.post(coraREData.ajaxUrl, {
            action: 'cora_generate_attendance_report',
            nonce: coraREData.ajaxNonce,
            report_type: 'email_digest',
            horizon: horizon,
            target_user: targetUser,
            recipient_email: recipient
        }, function(res) {
            if (res.success) {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(res.data.message || "Email digest sent successfully");
                }
            } else {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(res.data.message || "Failed to send email digest");
                }
            }
        });
    }
    window.sendAttendanceEmailDigest = sendAttendanceEmailDigest;

    function copyAttendanceShareLink() {
        var horizon    = $('#attendance-report-horizon').val() || 'daily';
        var targetUser = $('#attendance-report-target').val() || 'all';

        $.post(coraREData.ajaxUrl, {
            action: 'cora_generate_attendance_report',
            nonce: coraREData.ajaxNonce,
            report_type: 'share_link',
            horizon: horizon,
            target_user: targetUser
        }, function(res) {
            if (res.success && res.data.share_url) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(res.data.share_url).then(function() {
                        if (typeof window.coraShowToast === 'function') {
                            window.coraShowToast("Secure share link copied to clipboard");
                        }
                    });
                } else {
                    if (typeof window.coraShowToast === 'function') {
                        window.coraShowToast("Share link generated: " + res.data.share_url);
                    }
                }
            }
        });
    }
    window.copyAttendanceShareLink = copyAttendanceShareLink;

    function detectCurrentLocationForGeofence(e) {
        if (e && e.preventDefault) e.preventDefault();
        
        if (!navigator.geolocation) {
            if (window.coraShowToast) {
                window.coraShowToast('Geolocation is not supported by your browser.', 'error');
            }
            return;
        }

        if (window.coraShowToast) {
            window.coraShowToast('Requesting device location coordinates...', 'info');
        }

        var options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };

        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            var coordsStr = lat.toFixed(6) + ', ' + lng.toFixed(6);

            // Populate the address input field
            $('#geofence-address-input').val(coordsStr);
            
            // Force preview map to update
            updateMapPreviewFromInput();

            if (window.coraShowToast) {
                window.coraShowToast('Location detected successfully!', 'success');
            }

            // Attempt to reverse geocode using Nominatim API (OpenStreetMap)
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data && data.display_name) {
                        // Check if user has changed the input in the meantime
                        if ($('#geofence-address-input').val() === coordsStr) {
                            $('#geofence-address-input').val(data.display_name);
                        }
                    }
                })
                .catch(function(err) {
                    console.log('Reverse geocoding failed, keeping raw coordinates:', err);
                });

        }, function(error) {
            var errMsg = 'Unable to retrieve location details.';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errMsg = 'Location access denied. Please enable location permissions in your browser settings.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errMsg = 'Location information is unavailable. Please try entering address manually.';
                    break;
                case error.TIMEOUT:
                    errMsg = 'Location request timed out. Please try again.';
                    break;
            }
            if (window.coraShowToast) {
                window.coraShowToast(errMsg, 'error');
            }
        }, options);
    }
    window.detectCurrentLocationForGeofence = detectCurrentLocationForGeofence;

    // Bind geofencing functions to window for inline HTML triggers
    window.selectGeofenceRadius = selectGeofenceRadius;
    window.switchGeofenceMode = switchGeofenceMode;
    window.updateMapPreviewFromInput = updateMapPreviewFromInput;
    window.handleSaveGeofence = handleSaveGeofence;
    window.toggleMobileFilters = toggleMobileFilters;
    window.clearFilters = clearFilters;

    $('#cora-user-punch-in-btn').on('click', function() { logUserPunch('in'); });
    $('#cora-user-punch-out-btn').on('click', function() { logUserPunch('out'); });
</script>
