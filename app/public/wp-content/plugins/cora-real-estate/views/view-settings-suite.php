<?php
/**
 * Cora Real Estate CRM - Module 6: System Settings Complete Suite
 * Studio-Grade Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$active_tab = isset( $_GET['settings_tab'] ) ? sanitize_text_field( $_GET['settings_tab'] ) : 'general';
$pages      = get_pages();
$categories = get_categories();
$roles      = wp_roles()->get_names();
?>

<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">System Settings Complete Suite</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Global network parameters, reading/writing defaults, discussion moderation rules, and SEO permalinks.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button class="cora-btn-primary px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-md text-xs transition-colors cursor-pointer flex items-center gap-2 shadow-sm" onclick="coraSaveSystemSettingsSuite()">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save All Settings
        </button>
    </div>
</div>

<!-- Settings Sidebar Grid Layout -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mt-6">
    <!-- Left Column: Navigation Sidebar -->
    <div class="lg:col-span-1 space-y-1.5">
        <?php
        $tabs = array(
            'general'    => array(
                'label' => 'General Settings',
                'desc'  => 'Workspace details & identity',
                'icon'  => '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'
            ),
            'pwd-policy' => array(
                'label' => 'Password Policy',
                'desc'  => 'Enforce security parameters',
                'icon'  => '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>'
            ),
            'branches'   => array(
                'label' => 'Branch Management',
                'desc'  => 'Brokerage physical offices',
                'icon'  => '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>'
            ),
            'brand'      => array(
                'label' => 'Branding & API Keys',
                'desc'  => 'Favicon, logos, integrations',
                'icon'  => '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>'
            ),
            'reading'    => array(
                'label' => 'Reading & SEO',
                'desc'  => 'Homepage and search engines',
                'icon'  => '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>'
            ),
            'writing'    => array(
                'label' => 'Writing Defaults',
                'desc'  => 'Category & format variables',
                'icon'  => '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>'
            ),
            'discussion' => array(
                'label' => 'Discussion Suite',
                'desc'  => 'Moderation & blacklists',
                'icon'  => '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>'
            ),
            'permalinks' => array(
                'label' => 'SEO Permalinks',
                'desc'  => 'SEO URL structures',
                'icon'  => '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>'
            ),
            'privacy'    => array(
                'label' => 'Privacy Policy',
                'desc'  => 'Compliance terms page',
                'icon'  => '<svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
            )
        );
        foreach ( $tabs as $tab_key => $tab ) :
            $is_active = ( $active_tab === $tab_key );
        ?>
        <a href="?page=cora-workspace&sub=settings-suite&settings_tab=<?php echo esc_attr( $tab_key ); ?>" class="flex items-center gap-3.5 px-4 py-3 rounded-xl border transition-all text-left group <?php echo $is_active ? 'bg-zinc-950 text-white border-zinc-950 shadow-md font-bold' : 'bg-white border-zinc-200/80 text-zinc-700 hover:bg-zinc-50/50 hover:border-zinc-300'; ?>">
            <div class="<?php echo $is_active ? 'text-white' : 'text-zinc-400 group-hover:text-zinc-800'; ?> transition-colors shrink-0">
                <?php echo $tab['icon']; ?>
            </div>
            <div class="min-w-0">
                <div class="text-[11px] font-bold leading-tight <?php echo $is_active ? 'text-white' : 'text-zinc-900'; ?>"><?php echo esc_html( $tab['label'] ); ?></div>
                <div class="text-[9px] mt-0.5 truncate <?php echo $is_active ? 'text-zinc-300' : 'text-zinc-400'; ?>"><?php echo esc_html( $tab['desc'] ); ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Right Column: Settings Form Card -->
    <div class="lg:col-span-3 bg-white border border-zinc-200/80 rounded-xl p-6 shadow-sm flex flex-col justify-between">
        <form id="cora-settings-suite-form" onsubmit="event.preventDefault(); coraSaveSystemSettingsSuite();">
            <input type="hidden" name="active_tab" value="<?php echo esc_attr( $active_tab ); ?>">

        <?php if ( $active_tab === 'general' ) : ?>
        <!-- TAB 1: GENERAL SETTINGS -->
        <div class="space-y-6 max-w-2xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">General Site Configuration</h3>
                <p class="text-xs text-zinc-500">Core identity and default user registration parameters.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Site Title</label>
                    <input type="text" name="blogname" value="<?php echo esc_attr( get_option('blogname') ); ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Tagline / Subtitle</label>
                    <input type="text" name="blogdescription" value="<?php echo esc_attr( get_option('blogdescription') ); ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Administration Email Address</label>
                    <input type="email" name="admin_email" value="<?php echo esc_attr( get_option('admin_email') ); ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">New User Default Role</label>
                    <select name="default_role" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                        <?php foreach ( $roles as $role_key => $role_name ) : ?>
                            <option value="<?php echo esc_attr( $role_key ); ?>" <?php selected( get_option('default_role'), $role_key ); ?>><?php echo esc_html( $role_name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="pt-2">
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                    <input type="checkbox" name="users_can_register" value="1" <?php checked( get_option('users_can_register'), 1 ); ?> class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                    <span>Membership: Anyone can register for an account</span>
                </label>
            </div>

            <!-- Workspace Details Section -->
            <div class="border-t border-zinc-150 pt-5 space-y-4">
                <div class="border-b border-zinc-100 pb-3">
                    <h3 class="text-sm font-bold text-zinc-900">General Workspace Settings</h3>
                    <p class="text-xs text-zinc-500">Corporate identity, localized workspace address, and billing tax descriptors.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">Workspace Name</label>
                        <input type="text" name="cora_workspace_name" value="<?php echo esc_attr( get_option('cora_workspace_name', 'Cora Studio') ); ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900" placeholder="e.g. Mumbai Main Office">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">Tax Registration Details</label>
                        <input type="text" name="cora_workspace_tax_details" value="<?php echo esc_attr( get_option('cora_workspace_tax_details', 'GSTIN: 27AAAAA1111A1Z1') ); ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900" placeholder="e.g. VAT / GSTIN / PAN details">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-zinc-800 mb-1">Workspace Address</label>
                        <input type="text" name="cora_workspace_address" value="<?php echo esc_attr( get_option('cora_workspace_address', '101, BKC Road, Bandra East, Mumbai') ); ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900" placeholder="Full physical office location">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-zinc-800 mb-1">Activity Log Auto-Archive Threshold</label>
                        <select name="cora_activity_logs_retention" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                            <?php $retention = get_option('cora_activity_logs_retention', 0); ?>
                            <option value="0" <?php selected( $retention, 0 ); ?>>Never (Keep all logs)</option>
                            <option value="30" <?php selected( $retention, 30 ); ?>>30 Days</option>
                            <option value="90" <?php selected( $retention, 90 ); ?>>90 Days</option>
                            <option value="180" <?php selected( $retention, 180 ); ?>>180 Days</option>
                            <option value="365" <?php selected( $retention, 365 ); ?>>1 Year</option>
                        </select>
                        <p class="text-[10px] text-zinc-400 mt-1">Prune system activity log events older than the selection to optimize database performance.</p>
                    </div>
                </div>
                <div class="pt-2 border-t border-zinc-100 mt-4">
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                        <input type="checkbox" name="cora_workspace_allow_tours" value="1" <?php checked( get_option('cora_workspace_allow_tours', 1), 1 ); ?> class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                        <span>Enable Workspace Interactive Tour guides for first-time logins</span>
                    </label>
                </div>
            </div>

            <!-- Database Clean Up Section -->
            <div class="border-t border-zinc-150 pt-5 space-y-4">
                <div class="border-b border-zinc-100 pb-3">
                    <h3 class="text-sm font-bold text-red-600">Database Optimization</h3>
                    <p class="text-xs text-zinc-500">Clean up legacy key-value storage once you have verified custom database tables are fully working.</p>
                </div>
                <div class="p-4 border border-red-200 bg-red-50/50 rounded-lg space-y-3">
                    <p class="text-xs text-zinc-700">
                        Purging legacy data removes the redundant data tables from <code>wp_options</code>. 
                        <strong>Note:</strong> Make sure you have verified the data migration counts are correct before purging.
                    </p>
                    <button type="button" id="cora-purge-legacy-options" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        Purge Old wp_options Cache
                    </button>
                </div>
            </div>
        </div>

        <?php elseif ( $active_tab === 'pwd-policy' ) : ?>
        <!-- TAB: PASSWORD POLICY SETTINGS -->
        <div class="space-y-6 max-w-2xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Workspace Password Policy</h3>
                <p class="text-xs text-zinc-500">Enforce minimum complexity guidelines for passwords across logins, setups, and resets.</p>
            </div>
            
            <div class="space-y-4">
                <div class="w-48">
                    <label class="block text-xs font-bold text-zinc-800 mb-1.5">Minimum Password Length</label>
                    <input type="number" min="6" max="32" name="cora_pwd_policy_min_len" value="<?php echo esc_attr( get_option('cora_pwd_policy_min_len', 8) ); ?>" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>

                <div class="pt-2 space-y-3">
                    <label class="flex items-center gap-2.5 text-xs text-zinc-850 font-semibold cursor-pointer">
                        <input type="checkbox" name="cora_pwd_policy_numbers" value="1" <?php checked( get_option('cora_pwd_policy_numbers', 0), 1 ); ?> class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                        <span>Require at least one number (0-9)</span>
                    </label>

                    <label class="flex items-center gap-2.5 text-xs text-zinc-850 font-semibold cursor-pointer">
                        <input type="checkbox" name="cora_pwd_policy_uppercase" value="1" <?php checked( get_option('cora_pwd_policy_uppercase', 0), 1 ); ?> class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                        <span>Require at least one uppercase letter (A-Z)</span>
                    </label>

                    <label class="flex items-center gap-2.5 text-xs text-zinc-850 font-semibold cursor-pointer">
                        <input type="checkbox" name="cora_pwd_policy_special" value="1" <?php checked( get_option('cora_pwd_policy_special', 0), 1 ); ?> class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                        <span>Require at least one special character (e.g. !, @, #, $, %, etc.)</span>
                    </label>
                </div>
            </div>
        </div>

        <?php elseif ( $active_tab === 'branches' ) : 
            $agency_id = cora_get_current_user_agency_id();
            $branches  = cora_db_get_branches();
            $filtered_branches = $branches;

            // Count active agents per branch
            $all_wp_users = get_users();
            $branch_agent_counts = array();
            foreach ( $all_wp_users as $u ) {
                $u_branch = get_user_meta( $u->ID, 'cora_branch_id', true );
                if ( ! empty( $u_branch ) ) {
                    if ( ! isset( $branch_agent_counts[$u_branch] ) ) {
                        $branch_agent_counts[$u_branch] = 0;
                    }
                    $branch_agent_counts[$u_branch]++;
                }
            }

            // Find all potential branch managers in this agency
            $manager_query_args = array(
                'role__in' => array( 'cora_branch_manager', 'cora_manager', 'administrator' )
            );
            if ( $agency_id !== 'super' ) {
                $manager_query_args['meta_query'] = array(
                    array(
                        'key'     => 'cora_agency_id',
                        'value'   => $agency_id,
                        'compare' => '='
                    )
                );
            }
            $potential_managers = get_users( $manager_query_args );

            // Find currently assigned managers for 1:1 check
            $assigned_managers = array();
            foreach ( $filtered_branches as $b_id => $b ) {
                if ( ! empty( $b['manager_id'] ) ) {
                    $assigned_managers[ intval( $b['manager_id'] ) ] = $b_id;
                }
            }
        ?>
        <!-- TAB: BRANCH MANAGEMENT -->
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900">Brokerage Branches</h3>
                    <p class="text-xs text-zinc-500">Manage multiple physical offices, assign localized managers, and monitor regional agent counts.</p>
                </div>
                <button type="button" onclick="openCreateBranchDrawer()" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    New Branch
                </button>
            </div>

            <div class="bg-white border border-zinc-200/85 rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-zinc-200 text-xs text-left">
                    <thead class="bg-zinc-50/50">
                        <tr>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Branch Name</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Location / Address</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Branch Manager</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px]">Active Crew</th>
                            <th class="px-5 py-3 font-bold text-zinc-400 uppercase tracking-wider text-[10px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        <?php if ( empty( $filtered_branches ) ) : ?>
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-zinc-400 font-medium">No branches configured.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $filtered_branches as $b_id => $b ) :
                                $mgr = ! empty( $b['manager_id'] ) ? get_userdata( $b['manager_id'] ) : null;
                                $mgr_name = $mgr ? $mgr->display_name : 'Unassigned';
                                $crew_count = $branch_agent_counts[$b_id] ?? 0;
                            ?>
                                <tr class="hover:bg-zinc-50/10">
                                    <td class="px-5 py-3.5 font-bold text-zinc-900"><?php echo esc_html( $b['name'] ); ?></td>
                                    <td class="px-5 py-3.5 text-zinc-500 font-semibold"><?php echo esc_html( $b['city'] . ' / ' . $b['address'] ); ?></td>
                                    <td class="px-5 py-3.5 font-semibold text-zinc-700">
                                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-800 text-[9px] font-bold">
                                            <?php echo esc_html( $mgr_name ); ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 font-bold text-zinc-900"><?php echo esc_html( $crew_count ); ?> Agents</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick="openEditBranchDrawer('<?php echo esc_attr($b_id); ?>', '<?php echo esc_attr($b['name']); ?>', '<?php echo esc_attr($b['city']); ?>', '<?php echo esc_attr($b['address']); ?>', '<?php echo esc_attr($b['manager_id'] ?? ''); ?>')" class="px-2.5 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 cursor-pointer shadow-sm transition-colors">Edit</button>
                                            <button type="button" onclick="deleteBranch('<?php echo esc_attr($b_id); ?>', <?php echo $crew_count; ?>)" class="px-2.5 py-1 border border-zinc-200 rounded-lg text-[10px] font-bold text-red-600 bg-white hover:bg-red-50 hover:border-red-200 cursor-pointer shadow-sm transition-colors">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ═══ CREATE BRANCH DRAWER SHEET ══════════════════════════════════════════ -->
        <div id="drawer-create-branch" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-create-branch-card">
                <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
                    <h3 class="text-sm font-bold text-zinc-900">Configure New Branch</h3>
                    <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeCreateBranchDrawer()">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Branch Office Name</label>
                        <input type="text" id="new-branch-name" required placeholder="e.g. Westside HQ" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">City</label>
                        <input type="text" id="new-branch-city" required placeholder="e.g. Mumbai" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Office Address</label>
                        <input type="text" id="new-branch-address" required placeholder="e.g. 402, Bandra Kurla Complex" class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Assign Branch Manager</label>
                        <select id="new-branch-manager" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                            <option value="">— Unassigned —</option>
                            <?php foreach ( $potential_managers as $pm ) :
                                $already_assigned = isset($assigned_managers[$pm->ID]);
                                $label_suffix = $already_assigned ? ' (Already managing another branch)' : '';
                            ?>
                                <option value="<?php echo esc_attr( $pm->ID ); ?>" <?php if ($already_assigned) echo 'disabled style="color:#a1a1aa;"'; ?>><?php echo esc_html( $pm->display_name . $label_suffix ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="pt-4">
                        <button type="button" onclick="handleCreateBranch(event)" id="create-branch-btn" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Initialize Branch</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ EDIT BRANCH DRAWER SHEET ════════════════════════════════════════════ -->
        <div id="drawer-edit-branch" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-edit-branch-card">
                <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
                    <h3 class="text-sm font-bold text-zinc-900">Modify Branch Details</h3>
                    <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeEditBranchDrawer()">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 space-y-5">
                    <input type="hidden" id="edit-branch-id">
                    
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Branch Office Name</label>
                        <input type="text" id="edit-branch-name" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">City</label>
                        <input type="text" id="edit-branch-city" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Office Address</label>
                        <input type="text" id="edit-branch-address" required class="w-full px-3 py-2 text-xs border border-zinc-200 rounded-lg focus:border-zinc-400 focus:outline-none bg-white text-zinc-955">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1.5">Assign Branch Manager</label>
                        <select id="edit-branch-manager" class="w-full border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                            <option value="">— Unassigned —</option>
                            <?php foreach ( $potential_managers as $pm ) :
                                $already_assigned = isset($assigned_managers[$pm->ID]);
                                $current_assigned_to_this = ($already_assigned && $assigned_managers[$pm->ID] === 'this_placeholder'); // Will be updated dynamically in JS
                            ?>
                                <option value="<?php echo esc_attr( $pm->ID ); ?>" data-assigned-to="<?php echo esc_attr( $assigned_managers[$pm->ID] ?? '' ); ?>"><?php echo esc_html( $pm->display_name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="pt-4">
                        <button type="button" onclick="handleEditBranch(event)" id="edit-branch-btn" class="w-full py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer shadow-sm">Save Shifts</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openCreateBranchDrawer() {
                $('#drawer-create-branch').removeClass('opacity-0 pointer-events-none');
                $('#drawer-create-branch').css({'opacity': '1', 'pointer-events': 'auto'});
                $('#drawer-create-branch-card').removeClass('translate-x-full').addClass('translate-x-0');
            }
            function closeCreateBranchDrawer() {
                $('#drawer-create-branch-card').removeClass('translate-x-0').addClass('translate-x-full');
                setTimeout(function() {
                    $('#drawer-create-branch').addClass('opacity-0 pointer-events-none');
                    $('#drawer-create-branch').css({'opacity': '0', 'pointer-events': 'none'});
                }, 300);
                $('#new-branch-name').val('');
                $('#new-branch-city').val('');
                $('#new-branch-address').val('');
                $('#new-branch-manager').val('');
            }

            function openEditBranchDrawer(id, name, city, address, managerId) {
                $('#edit-branch-id').val(id);
                $('#edit-branch-name').val(name);
                $('#edit-branch-city').val(city);
                $('#edit-branch-address').val(address);
                
                // Set manager dropdown options and disable managers assigned to OTHER branches
                $('#edit-branch-manager option').each(function() {
                    var assignedBranch = $(this).data('assigned-to') || '';
                    if (assignedBranch !== '' && assignedBranch !== id) {
                        $(this).prop('disabled', true).text($(this).text().split(' (Already')[0] + ' (Already managing another branch)').css('color', '#a1a1aa');
                    } else {
                        $(this).prop('disabled', false).text($(this).text().split(' (Already')[0]).css('color', '');
                    }
                });

                $('#edit-branch-manager').val(managerId);

                $('#drawer-edit-branch').removeClass('opacity-0 pointer-events-none');
                $('#drawer-edit-branch').css({'opacity': '1', 'pointer-events': 'auto'});
                $('#drawer-edit-branch-card').removeClass('translate-x-full').addClass('translate-x-0');
            }

            function closeEditBranchDrawer() {
                $('#drawer-edit-branch-card').removeClass('translate-x-0').addClass('translate-x-full');
                setTimeout(function() {
                    $('#drawer-edit-branch').addClass('opacity-0 pointer-events-none');
                    $('#drawer-edit-branch').css({'opacity': '0', 'pointer-events': 'none'});
                }, 300);
            }

            function handleCreateBranch(e) {
                if (e && e.preventDefault) e.preventDefault();
                var name = $('#new-branch-name').val().trim();
                var city = $('#new-branch-city').val().trim();
                var address = $('#new-branch-address').val().trim();
                var manager = $('#new-branch-manager').val();

                if (!name || !city || !address) {
                    window.coraShowToast('Please fill all required fields.');
                    return;
                }

                $('#create-branch-btn').prop('disabled', true).text('Initializing branch...');

                $.post(coraREData.ajaxUrl, {
                    action: 'cora_ajax_save_branch',
                    branch_name: name,
                    city: city,
                    address: address,
                    manager_id: manager,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res.success) {
                        window.coraShowToast('Branch saved successfully.');
                        closeCreateBranchDrawer();
                        setTimeout(function() { window.location.reload(); }, 1000);
                    } else {
                        window.coraShowToast(res.data.message || 'Failed to initialize branch.');
                        $('#create-branch-btn').prop('disabled', false).text('Initialize Branch');
                    }
                });
            }

            function handleEditBranch(e) {
                if (e && e.preventDefault) e.preventDefault();
                var id = $('#edit-branch-id').val();
                var name = $('#edit-branch-name').val().trim();
                var city = $('#edit-branch-city').val().trim();
                var address = $('#edit-branch-address').val().trim();
                var manager = $('#edit-branch-manager').val();

                if (!name || !city || !address) {
                    window.coraShowToast('Please fill all required fields.');
                    return;
                }

                $('#edit-branch-btn').prop('disabled', true).text('Saving shifts...');

                $.post(coraREData.ajaxUrl, {
                    action: 'cora_ajax_save_branch',
                    branch_id: id,
                    branch_name: name,
                    city: city,
                    address: address,
                    manager_id: manager,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res.success) {
                        window.coraShowToast('Branch saved successfully.');
                        closeEditBranchDrawer();
                        setTimeout(function() { window.location.reload(); }, 1000);
                    } else {
                        window.coraShowToast(res.data.message || 'Failed to save branch.');
                        $('#edit-branch-btn').prop('disabled', false).text('Save Shifts');
                    }
                });
            }

            function deleteBranch(id, crewCount) {
                if (crewCount > 0) {
                    window.coraShowToast('You cannot delete a branch with active team members. Reassign all members first.');
                    return;
                }

                window.coraConfirmAction(
                    'Confirm Deletion',
                    'Are you sure you want to delete this branch?',
                    function() {
                        window.coraShowToast('Deleting branch...');
                        $.post(coraREData.ajaxUrl, {
                            action: 'cora_ajax_delete_branch',
                            branch_id: id,
                            nonce: coraREData.ajaxNonce
                        }, function(res) {
                            if (res.success) {
                                window.coraShowToast('Branch deleted successfully.');
                                setTimeout(function() { window.location.reload(); }, 800);
                            } else {
                                window.coraShowToast(res.data.message || 'Failed to delete branch.');
                            }
                        });
                    }
                );
            }
        </script>

        <?php elseif ( $active_tab === 'brand' ) : ?>
        <!-- TAB 7: BRANDING & API KEYS -->
        <div class="space-y-6 max-w-2xl animate-in fade-in duration-200">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Brand Identity & API Integrations</h3>
                <p class="text-xs text-zinc-500">Configure your agency's logo, custom favicon, currency layout, and third-party developer API credentials.</p>
            </div>
            
            <div class="space-y-5">
                <!-- Agency Logo Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-zinc-200 rounded-xl bg-zinc-50/50">
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Agency Logo URL</label>
                            <div class="flex gap-2">
                                <input type="url" id="cora-brand-logo-url-suite" name="cora_brand_logo_url" value="<?php echo esc_url( get_option('cora_brand_logo_url', '') ); ?>" placeholder="https://..." class="flex-1 bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                                <button type="button" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraOpenMediaSelector('cora-brand-logo-url-suite')">Browse</button>
                            </div>
                        </div>
                        <p class="text-[11px] text-zinc-400">Upload your real estate group's official logo. This will be used on all shared portfolios, custom client portals, and invoice headers.</p>
                    </div>
                    <div class="flex items-center justify-center border border-zinc-200 rounded-lg bg-white p-3 h-28">
                        <?php $logo_url = get_option('cora_brand_logo_url', ''); ?>
                        <div id="cora-suite-logo-preview" class="w-full h-full flex items-center justify-center overflow-hidden">
                            <?php if ( ! empty( $logo_url ) ) : ?>
                                <img src="<?php echo esc_url( $logo_url ); ?>" class="max-h-full max-w-full object-contain" alt="Logo Preview">
                            <?php else : ?>
                                <span class="text-[10px] text-zinc-400 uppercase font-semibold">No Logo Set</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Custom Favicon Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-zinc-200 rounded-xl bg-zinc-50/50">
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Custom Favicon URL (32x32 / 64x64 PNG)</label>
                            <div class="flex gap-2">
                                <input type="url" id="cora-brand-favicon-url-suite" name="cora_brand_favicon_url" value="<?php echo esc_url( get_option('cora_brand_favicon_url', '') ); ?>" placeholder="https://..." class="flex-1 bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                                <button type="button" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraOpenMediaSelector('cora-brand-favicon-url-suite')">Browse</button>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="px-2.5 py-1.5 border border-zinc-200 hover:border-zinc-450 bg-white text-zinc-700 font-semibold text-[10px] rounded transition-colors cursor-pointer" onclick="coraSetDefaultPremiumFavicon()">
                                Set to Premium Monogram Icon
                            </button>
                            <button type="button" class="px-2.5 py-1.5 border border-zinc-200 hover:border-zinc-450 bg-white text-zinc-700 font-semibold text-[10px] rounded transition-colors cursor-pointer" onclick="document.getElementById('cora-brand-favicon-url-suite').value='';">
                                Clear Favicon
                            </button>
                        </div>
                        <script>
                            function coraSetDefaultPremiumFavicon() {
                                const url = "<?php echo esc_url( CORA_REAL_ESTATE_AI_URL . 'assets/images/cora-favicon.png' ); ?>";
                                document.getElementById('cora-brand-favicon-url-suite').value = url;
                                const img = document.querySelector('#cora-suite-favicon-preview img');
                                if (img) {
                                    img.src = url;
                                } else {
                                    document.getElementById('cora-suite-favicon-preview').innerHTML = `<img src="${url}" class="w-10 h-10 object-contain" alt="Favicon Preview">`;
                                }
                                window.coraShowToast("Premium Monogram Icon selected as Favicon.");
                            }
                        </script>
                        <p class="text-[11px] text-zinc-400">Configure your website browser tab favicon. You can use your own or select the unique custom-designed Cora Real Estate monogram favicon.</p>
                    </div>
                    <div class="flex flex-col items-center justify-center border border-zinc-200 rounded-lg bg-white p-3 h-28 space-y-1.5">
                        <span class="text-[9px] text-zinc-400 uppercase font-bold tracking-wider">Tab Favicon</span>
                        <?php 
                        $favicon_url = get_option('cora_brand_favicon_url', ''); 
                        if ( empty( $favicon_url ) ) {
                            $favicon_url = CORA_REAL_ESTATE_AI_URL . 'assets/images/cora-favicon.png';
                        }
                        ?>
                        <div id="cora-suite-favicon-preview" class="w-12 h-12 flex items-center justify-center border border-zinc-100 rounded-md bg-zinc-50">
                            <img src="<?php echo esc_url( $favicon_url ); ?>" class="w-8 h-8 object-contain" alt="Favicon Preview">
                        </div>
                    </div>
                </div>

                <!-- Google Maps API and WhatsApp integration -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">Google Maps API Key</label>
                        <input type="text" name="cora_gbp_maps_api_key" value="<?php echo esc_attr( get_option('cora_gbp_maps_api_key', '') ); ?>" placeholder="AIzaSy..." class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                        <p class="text-[10px] text-zinc-400 mt-1">Required for geolocating listing properties and rendering location matrix details on property maps.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">System Currency Layout</label>
                        <select name="cora_currency_format" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                            <?php $curr_format = get_option('cora_currency_format', 'INR_LAKHS'); ?>
                            <option value="INR_LAKHS" <?php selected( $curr_format, 'INR_LAKHS' ); ?>>Indian Rupees (Lakhs/Crores - e.g. ₹1.80 L / ₹4.50 Cr)</option>
                            <option value="INR_STANDARD" <?php selected( $curr_format, 'INR_STANDARD' ); ?>>Indian Rupees Standard (Comma separated - e.g. ₹1,80,000)</option>
                            <option value="USD" <?php selected( $curr_format, 'USD' ); ?>>US Dollars (Standard - e.g. $180,000)</option>
                        </select>
                        <p class="text-[10px] text-zinc-400 mt-1">Determines how prices, transactions, invoices, and payouts are formatted in the Ledger.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-zinc-100 pt-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">WhatsApp Cloud API Token</label>
                        <input type="password" name="cora_whatsapp_api_token" value="<?php echo esc_attr( get_option('cora_whatsapp_api_token', '') ); ?>" placeholder="EAAW..." class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">WhatsApp Business Phone ID</label>
                        <input type="text" name="cora_whatsapp_phone_number" value="<?php echo esc_attr( get_option('cora_whatsapp_phone_number', '') ); ?>" placeholder="e.g. 1093847291039" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                    </div>
                    <p class="sm:col-span-2 text-[10px] text-zinc-400">Configure WhatsApp credentials to activate automated transaction notifications, client shortlisting alerts, and showing follow-ups.</p>
                </div>
            </div>
        </div>

        <?php elseif ( $active_tab === 'reading' ) : ?>
        <!-- TAB 2: READING & SEO SETTINGS -->
        <div class="space-y-6 max-w-2xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Reading & Search Engine Indexing</h3>
                <p class="text-xs text-zinc-500">Control homepage display mode and search engine crawler visibility.</p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-2">Homepage Displays</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-xs text-zinc-800 font-medium cursor-pointer">
                            <input type="radio" name="show_on_front" value="posts" <?php checked( get_option('show_on_front'), 'posts' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <span>Your latest blog posts feed</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs text-zinc-800 font-medium cursor-pointer">
                            <input type="radio" name="show_on_front" value="page" <?php checked( get_option('show_on_front'), 'page' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                            <span>A static landing page</span>
                        </label>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">Static Homepage</label>
                        <select name="page_on_front" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                            <option value="0">— Select Page —</option>
                            <?php foreach ( $pages as $p ) : ?>
                                <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('page_on_front'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-800 mb-1">Posts Page (Blog Archive)</label>
                        <select name="page_for_posts" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                            <option value="0">— Select Page —</option>
                            <?php foreach ( $pages as $p ) : ?>
                                <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('page_for_posts'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="pt-4 border-t border-zinc-100">
                    <label class="flex items-start gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                        <input type="checkbox" name="blog_public" value="0" <?php checked( get_option('blog_public'), 0 ); ?> class="rounded border-zinc-300 text-red-600 focus:ring-red-600 mt-0.5">
                        <div>
                            <span class="text-red-700 font-bold">Discourage search engines from indexing this site</span>
                            <p class="text-[11px] text-zinc-500 font-normal">Modifies robots.txt and meta tags. Note: It is up to search engines to honor this request.</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <?php elseif ( $active_tab === 'writing' ) : ?>
        <!-- TAB 3: WRITING DEFAULTS -->
        <div class="space-y-6 max-w-xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Writing & Content Defaults</h3>
                <p class="text-xs text-zinc-500">Configure default taxonomy labeling and publishing format presets.</p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Default Post Category</label>
                    <select name="default_category" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                        <?php foreach ( $categories as $cat ) : ?>
                            <option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( get_option('default_category'), $cat->term_id ); ?>><?php echo esc_html( $cat->name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Default Post Format</label>
                    <select name="default_post_format" class="w-full bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                        <option value="0" <?php selected( get_option('default_post_format'), '0' ); ?>>Standard</option>
                        <option value="gallery" <?php selected( get_option('default_post_format'), 'gallery' ); ?>>Gallery</option>
                        <option value="video" <?php selected( get_option('default_post_format'), 'video' ); ?>>Video</option>
                        <option value="quote" <?php selected( get_option('default_post_format'), 'quote' ); ?>>Quote</option>
                    </select>
                </div>
            </div>
        </div>

        <?php elseif ( $active_tab === 'discussion' ) : ?>
        <!-- TAB 4: DISCUSSION & MODERATION -->
        <div class="space-y-6 max-w-2xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Discussion & Comment Moderation Rules</h3>
                <p class="text-xs text-zinc-500">Automate spam filtering, link limits, and moderation blacklists.</p>
            </div>
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                        <input type="checkbox" name="default_pingback_flag" value="1" <?php checked( get_option('default_pingback_flag'), 1 ); ?> class="rounded border-zinc-300 text-zinc-900">
                        <span>Allow link notifications from other blogs (pingbacks and trackbacks)</span>
                    </label>
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                        <input type="checkbox" name="default_comment_status" value="open" <?php checked( get_option('default_comment_status'), 'open' ); ?> class="rounded border-zinc-300 text-zinc-900">
                        <span>Allow people to submit comments on new articles</span>
                    </label>
                    <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                        <input type="checkbox" name="comment_moderation" value="1" <?php checked( get_option('comment_moderation'), 1 ); ?> class="rounded border-zinc-300 text-zinc-900">
                        <span>Comment must be manually approved before publishing</span>
                    </label>
                </div>
                <div class="pt-2">
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Comment Moderation Queue Keywords</label>
                    <textarea name="moderation_keys" rows="3" placeholder="One word, IP address, or URL per line..." class="w-full bg-white border border-zinc-300 rounded-lg p-2.5 text-xs text-zinc-900 font-mono focus:outline-none"><?php echo esc_textarea( get_option('moderation_keys') ); ?></textarea>
                    <p class="text-[11px] text-zinc-400 mt-1">When a comment contains any of these words in its content, name, URL, email, or IP address, it will be held in the moderation queue.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-red-800 mb-1">Disallowed Comment Keys (Automatic Trash/Spam)</label>
                    <textarea name="disallowed_keys" rows="3" placeholder="One word, IP address, or URL per line..." class="w-full bg-white border border-red-300 rounded-lg p-2.5 text-xs text-zinc-900 font-mono focus:outline-none"><?php echo esc_textarea( get_option('disallowed_keys') ); ?></textarea>
                </div>
            </div>
        </div>

        <?php elseif ( $active_tab === 'permalinks' ) : ?>
        <!-- TAB 5: SEO PERMALINKS -->
        <div class="space-y-6 max-w-xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">SEO URL Permalinks Structure</h3>
                <p class="text-xs text-zinc-500">Choose clean, human-readable URL routing schemas for better search engine rankings.</p>
            </div>
            <div class="space-y-3">
                <?php $current_permalink = get_option('permalink_structure'); ?>
                <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border border-zinc-200 rounded-lg bg-zinc-50 hover:bg-zinc-100 cursor-pointer transition-colors gap-2">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="permalink_structure" value="" <?php checked( $current_permalink, '' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                        <span class="text-xs font-bold text-zinc-900">Plain</span>
                    </div>
                    <code class="text-[11px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/?p=123') ); ?></code>
                </label>

                <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border border-zinc-200 rounded-lg bg-zinc-50 hover:bg-zinc-100 cursor-pointer transition-colors gap-2">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="permalink_structure" value="/%year%/%monthnum%/%day%/%postname%/" <?php checked( $current_permalink, '/%year%/%monthnum%/%day%/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                        <span class="text-xs font-bold text-zinc-900">Day and name</span>
                    </div>
                    <code class="text-[11px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/2026/07/08/sample-post/') ); ?></code>
                </label>

                <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border border-zinc-200 rounded-lg bg-zinc-50 hover:bg-zinc-100 cursor-pointer transition-colors gap-2">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="permalink_structure" value="/%year%/%monthnum%/%postname%/" <?php checked( $current_permalink, '/%year%/%monthnum%/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                        <span class="text-xs font-bold text-zinc-900">Month and name</span>
                    </div>
                    <code class="text-[11px] text-zinc-500 font-mono truncate break-all"><?php echo esc_url( home_url('/2026/07/sample-post/') ); ?></code>
                </label>

                <label class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border border-zinc-900 rounded-lg bg-zinc-900/5 hover:bg-zinc-900/10 cursor-pointer transition-colors gap-2">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="permalink_structure" value="/%postname%/" <?php checked( $current_permalink, '/%postname%/' ); ?> class="text-zinc-900 focus:ring-zinc-900">
                        <div>
                            <span class="text-xs font-bold text-zinc-900">Post name (Recommended SEO)</span>
                        </div>
                    </div>
                    <code class="text-[11px] text-zinc-900 font-bold font-mono truncate break-all"><?php echo esc_url( home_url('/sample-post/') ); ?></code>
                </label>
            </div>
        </div>

        <?php elseif ( $active_tab === 'privacy' ) : ?>
        <!-- TAB 6: PRIVACY POLICY -->
        <div class="space-y-6 max-w-xl">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900">Privacy Policy Page Assignment</h3>
                <p class="text-xs text-zinc-500">Designate an official privacy policy page for legal compliance and user transparency.</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-800 mb-1">Change your Privacy Policy page</label>
                <div class="flex gap-2">
                    <select name="wp_page_for_privacy_policy" class="flex-1 bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                        <option value="0">— Select Page —</option>
                        <?php foreach ( $pages as $p ) : ?>
                            <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( get_option('wp_page_for_privacy_policy'), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="?page=cora-workspace&sub=pages" class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-lg transition-colors flex items-center gap-1">Create New Page</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="pt-6 mt-6 border-t border-zinc-200 flex items-center justify-end">
            <button type="submit" class="px-6 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors shadow-sm cursor-pointer flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2 2H5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Save All Settings
            </button>
        </div>
    </form>
</div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#cora-purge-legacy-options').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('Purging options...');
        
        $.post(coraREData.ajaxUrl, {
            action: 'cora_purge_options_data',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data || 'Old options cache database tables purged successfully!');
                setTimeout(function() { window.location.reload(); }, 1200);
            } else {
                window.coraShowToast(res.data || 'Failed to purge database options.');
                $btn.prop('disabled', false).text('Purge Old wp_options Cache');
            }
        }).fail(function() {
            window.coraShowToast('A system error occurred during purge.');
            $btn.prop('disabled', false).text('Purge Old wp_options Cache');
        });
    });
});
</script>
