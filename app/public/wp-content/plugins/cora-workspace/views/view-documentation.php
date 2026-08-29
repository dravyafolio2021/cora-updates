<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Security Check: double-check platform admin access
if ( ! cora_is_super_owner() ) {
    echo '<div class="p-6 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-700 font-bold">Access Denied: Platform Admin only.</div>';
    return;
}

global $wpdb;
$t_pages = $wpdb->prefix . 'cora_docs_pages';
$t_changelog = $wpdb->prefix . 'cora_docs_changelog';
$t_api = $wpdb->prefix . 'cora_docs_api_endpoints';

// Load initial pages from database
$all_pages = $wpdb->get_results( "SELECT * FROM {$t_pages} ORDER BY title ASC", ARRAY_A );
$all_changelogs = $wpdb->get_results( "SELECT * FROM {$t_changelog} ORDER BY created_at DESC", ARRAY_A );
$all_apis = $wpdb->get_results( "SELECT * FROM {$t_api} ORDER BY path ASC", ARRAY_A );

// Get registered modules from registry
$registered_modules = array();
if ( class_exists( 'Cora_Module_Registry' ) ) {
    $modules = Cora_Module_Registry::get_all_modules();
    foreach ( $modules as $key => $mod ) {
        $registered_modules[$key] = array(
            'id' => $mod->get_module_id(),
            'name' => str_replace('Cora ', '', get_class($mod))
        );
    }
}
// Default backup if registry is empty
if ( empty( $registered_modules ) ) {
    $registered_modules['user-management'] = array( 'id' => 'user-management', 'name' => 'User Management & Auth' );
    $registered_modules['email-management'] = array( 'id' => 'email-management', 'name' => 'Email Management' );
}

// Helper to find page in results
function cora_find_doc_page( $pages, $slug ) {
    foreach ( $pages as $p ) {
        if ( $p['slug'] === $slug ) {
            return $p;
        }
    }
    return null;
}

$overview_page = cora_find_doc_page( $all_pages, 'platform-overview' );
$guide_page = cora_find_doc_page( $all_pages, 'workspace-configuration' );
$roadmap_page = cora_find_doc_page( $all_pages, 'roadmap' );
?>

<!-- Scoped CSS for side drawers and transitions -->
<style>
    .cora-docs-drawer {
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .cora-docs-drawer.translate-x-full {
        transform: translateX(100%);
    }
    .cora-docs-drawer.translate-x-0 {
        transform: translateX(0);
    }
    .cora-docs-subtab-btn.active {
        border-color: #18181b;
        color: #18181b;
    }
    .dark .cora-docs-subtab-btn.active {
        border-color: #f4f4f5;
        color: #f4f4f5;
    }
</style>

<!-- Main Container -->
<div class="space-y-6" id="cora-docs-container">
    
    <!-- Header Area -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200/80 pb-5">
        <div class="flex items-center gap-3">
            <span class="text-zinc-900 ">
                <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </span>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 ">Documentation Control Desk</h1>
                <p class="text-xs text-zinc-500 mt-0.5">Manage internal documentation pages, publish to the public developer portal, and configure API registries.</p>
            </div>
        </div>
        
        <!-- Global Search Bar -->
        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" id="cora-docs-global-search" oninput="coraDocsGlobalSearch(this.value)" class="w-64 border border-zinc-200 rounded-lg py-1.5 pl-8 pr-3 text-xs bg-white focus:border-zinc-450 focus:outline-none text-zinc-900 " placeholder="Search documentation content...">
                <span class="absolute left-2.5 top-2.5 text-zinc-400">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
            </div>
            <a href="<?php echo esc_url( home_url( '/docs' ) ); ?>" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 border border-zinc-200 rounded-lg text-xs font-bold text-zinc-700 hover:bg-zinc-50 transition-colors select-none">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                Public Docs Portal
            </a>
        </div>
    </div>

    <!-- Navigation Sub-Tabs -->
    <div class="flex border-b border-zinc-200 gap-6 text-xs font-bold text-zinc-500 pb-0.5 select-none overflow-x-auto">
        <button onclick="coraDocsSwitchTab('overview')" class="cora-docs-subtab-btn active pb-2 border-b-2 border-transparent cursor-pointer focus:outline-none transition-colors" id="tab-btn-overview">Platform Overview</button>
        <button onclick="coraDocsSwitchTab('changelog')" class="cora-docs-subtab-btn pb-2 border-b-2 border-transparent cursor-pointer focus:outline-none transition-colors" id="tab-btn-changelog">Changelog</button>
        <button onclick="coraDocsSwitchTab('modules')" class="cora-docs-subtab-btn pb-2 border-b-2 border-transparent cursor-pointer focus:outline-none transition-colors" id="tab-btn-modules">Modules</button>
        <button onclick="coraDocsSwitchTab('api')" class="cora-docs-subtab-btn pb-2 border-b-2 border-transparent cursor-pointer focus:outline-none transition-colors" id="tab-btn-api">API Reference</button>
        <button onclick="coraDocsSwitchTab('guides')" class="cora-docs-subtab-btn pb-2 border-b-2 border-transparent cursor-pointer focus:outline-none transition-colors" id="tab-btn-guides">Workspace Guides</button>
        <button onclick="coraDocsSwitchTab('release-notes')" class="cora-docs-subtab-btn pb-2 border-b-2 border-transparent cursor-pointer focus:outline-none transition-colors" id="tab-btn-release-notes">Release Notes</button>
        <button onclick="coraDocsSwitchTab('roadmap')" class="cora-docs-subtab-btn pb-2 border-b-2 border-transparent cursor-pointer focus:outline-none transition-colors" id="tab-btn-roadmap">Roadmap</button>
    </div>

    <!-- ───────────────────────────────────────────────────────────────────────
         SUBTAB 1: PLATFORM OVERVIEW
         ─────────────────────────────────────────────────────────────────────── -->
    <div id="cora-docs-tab-overview" class="cora-docs-tab-content space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Editor Column -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-zinc-900 flex items-center gap-1.5">
                            Platform Overview Content
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-zinc-100 text-zinc-650 uppercase"><?php echo esc_html( $overview_page['status'] ?? 'draft' ); ?></span>
                        </h2>
                        
                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <button onclick="coraDocsToggleEdit('overview')" class="text-xs px-2.5 py-1 border border-zinc-200 rounded-lg text-zinc-600 hover:bg-zinc-50 select-none cursor-pointer" id="btn-edit-overview">Edit</button>
                            <button onclick="coraDocsOpenHistory(<?php echo intval( $overview_page['id'] ?? 0 ); ?>)" class="text-xs px-2.5 py-1 border border-zinc-200 rounded-lg text-zinc-600 hover:bg-zinc-50 select-none cursor-pointer">History</button>
                            
                            <?php if ( isset( $overview_page['status'] ) && $overview_page['status'] === 'approved_internal' ) : ?>
                            <button onclick="coraDocsPublishPage(<?php echo intval( $overview_page['id'] ); ?>, 'public_live')" class="text-xs px-2.5 py-1 bg-zinc-950 text-white rounded-lg select-none cursor-pointer">Publish to Public</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Editor Box -->
                    <div id="cora-docs-overview-editor-box" class="hidden space-y-3">
                        <textarea id="cora-docs-overview-textarea" rows="12" class="w-full border border-zinc-200 rounded-lg p-3 text-xs bg-zinc-50 text-zinc-900 font-mono focus:outline-none focus:border-zinc-450"><?php echo esc_textarea( $overview_page['content'] ?? '' ); ?></textarea>
                        <div class="flex items-center gap-2 justify-end">
                            <select id="cora-docs-overview-status" class="border border-zinc-200 rounded-lg px-2 py-1 text-xs text-zinc-700 bg-white focus:outline-none outline-none">
                                <option value="draft" <?php selected( $overview_page['status'] ?? 'draft', 'draft' ); ?>>Draft</option>
                                <option value="approved_internal" <?php selected( $overview_page['status'] ?? 'draft', 'approved_internal' ); ?>>Approved Internal</option>
                                <option value="public_live" <?php selected( $overview_page['status'] ?? 'draft', 'public_live' ); ?>>Public Live</option>
                            </select>
                            <button onclick="coraDocsSavePage('overview', <?php echo intval( $overview_page['id'] ?? 0 ); ?>, 'platform-overview', 'Platform Overview', 'overview')" class="text-xs px-3 py-1.5 bg-zinc-950 text-white rounded-lg font-bold select-none cursor-pointer">Save Page</button>
                        </div>
                    </div>

                    <!-- Preview Box -->
                    <div id="cora-docs-overview-preview-box" class="prose max-w-none text-xs ">
                        <?php echo cora_markdown_to_html( $overview_page['content'] ?? 'No overview page created yet. Click edit to begin.' ); ?>
                    </div>
                </div>
            </div>

            <!-- Meta Information Column -->
            <div class="space-y-4">
                <div class="bg-zinc-50 border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-450 ">Platform Meta Information</h3>
                    
                    <div class="space-y-3 divide-y divide-zinc-200/60 ">
                        <div class="pt-0 flex items-center justify-between text-xs">
                            <span class="text-zinc-500">Platform Version:</span>
                            <span class="font-mono font-bold text-zinc-900 ">v2.2.1</span>
                        </div>
                        <div class="pt-2.5 flex items-center justify-between text-xs">
                            <span class="text-zinc-500">Foundation Stack:</span>
                            <span class="font-bold text-zinc-900 ">WordPress 6.x SaaS</span>
                        </div>
                        <div class="pt-2.5 flex items-center justify-between text-xs">
                            <span class="text-zinc-500">AI Framework:</span>
                            <span class="font-bold text-zinc-900 ">Gemini 3.5 Flash RAG</span>
                        </div>
                        <div class="pt-2.5 flex items-center justify-between text-xs">
                            <span class="text-zinc-500">Last Synced:</span>
                            <span class="text-zinc-650 font-mono text-[11px]"><?php echo date('Y-m-d H:i'); ?></span>
                        </div>
                    </div>

                    <!-- Modules List Table -->
                    <div class="space-y-2.5 pt-4">
                        <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-450">Active Modules List</h4>
                        <div class="overflow-hidden border border-zinc-200/80 rounded-lg bg-white ">
                            <table class="w-full text-[11px] text-left border-collapse">
                                <thead>
                                    <tr class="bg-zinc-50 border-b border-zinc-200 ">
                                        <th class="p-2 text-zinc-500 font-bold">Module ID</th>
                                        <th class="p-2 text-zinc-500 font-bold">Version</th>
                                        <th class="p-2 text-zinc-500 font-bold">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-zinc-150/50 ">
                                        <td class="p-2 font-medium text-zinc-900 ">User Management</td>
                                        <td class="p-2 font-mono text-zinc-500">v1.0.0</td>
                                        <td class="p-2"><span class="px-1.5 py-0.5 rounded-full text-[9px] bg-emerald-50 text-emerald-700 font-bold border border-emerald-100">Live</span></td>
                                    </tr>
                                    <tr class="border-b border-zinc-150/50 ">
                                        <td class="p-2 font-medium text-zinc-900 ">Email outbound</td>
                                        <td class="p-2 font-mono text-zinc-500">v1.0.1</td>
                                        <td class="p-2"><span class="px-1.5 py-0.5 rounded-full text-[9px] bg-emerald-50 text-emerald-700 font-bold border border-emerald-100">Live</span></td>
                                    </tr>
                                    <tr class="border-b border-zinc-150/50 ">
                                        <td class="p-2 font-medium text-zinc-900 ">Document Vault</td>
                                        <td class="p-2 font-mono text-zinc-500">v0.9.8</td>
                                        <td class="p-2"><span class="px-1.5 py-0.5 rounded-full text-[9px] bg-amber-50 text-amber-700 font-bold border border-amber-100">Staged</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Auto-Update Diagnostic Triggers -->
                <div class="bg-zinc-50 border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500">Diagnostic Simulation Panel</h3>
                    <p class="text-[10px] text-zinc-500">Test the platform auto-update triggers. Firing these simulates background actions on the platform and creates draft changelogs or doc updates.</p>
                    <div class="space-y-2 pt-2">
                        <button onclick="coraDocsSimulateTrigger('register_module')" class="w-full text-center px-3 py-2 bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100 rounded-lg text-xs font-bold transition-all cursor-pointer select-none">Simulate Module Registration</button>
                        <button onclick="coraDocsSimulateTrigger('status_changed')" class="w-full text-center px-3 py-2 bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100 rounded-lg text-xs font-bold transition-all cursor-pointer select-none">Simulate Module Status Change</button>
                        <button onclick="coraDocsSimulateTrigger('feature_completed')" class="w-full text-center px-3 py-2 bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100 rounded-lg text-xs font-bold transition-all cursor-pointer select-none">Simulate Feature Completed</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ───────────────────────────────────────────────────────────────────────
         SUBTAB 2: CHANGELOG
         ─────────────────────────────────────────────────────────────────────── -->
    <div id="cora-docs-tab-changelog" class="cora-docs-tab-content space-y-4 hidden">
        
        <!-- Filters & Tools -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-3 items-center flex-1">
                <!-- Search changelog -->
                <div class="relative">
                    <input type="text" id="cora-changelog-search" oninput="coraDocsFilterChangelog()" class="border border-zinc-200 rounded-lg py-1.5 pl-8 pr-3 text-xs bg-white focus:outline-none" placeholder="Filter by changes...">
                    <span class="absolute left-2.5 top-2.5 text-zinc-400">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                </div>
                <!-- Module Filter -->
                <select id="cora-changelog-module-filter" onchange="coraDocsFilterChangelog()" class="border border-zinc-200 rounded-lg px-2 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Threads</option>
                    <option value="platform">Platform Level</option>
                    <?php foreach ($registered_modules as $key => $mod) : ?>
                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($mod['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Status Filter -->
                <select id="cora-changelog-status-filter" onchange="coraDocsFilterChangelog()" class="border border-zinc-200 rounded-lg px-2 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="released">Released</option>
                    <option value="in_progress">In Progress</option>
                    <option value="deprecated">Deprecated</option>
                    <option value="planned">Planned</option>
                </select>
            </div>
            
            <button onclick="coraDocsOpenChangelogDrawer()" class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 text-white rounded-lg text-xs font-bold select-none cursor-pointer">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                New Entry
            </button>
        </div>

        <!-- Changelog Entries Feed -->
        <div class="space-y-4" id="cora-changelog-feed">
            <?php if ( empty( $all_changelogs ) ) : ?>
                <div class="p-8 text-center text-zinc-400 bg-zinc-50 border border-zinc-200 rounded-xl text-xs">No changelog entries submitted yet.</div>
            <?php else : ?>
                <?php foreach ( $all_changelogs as $entry ) : 
                    $category_class = empty($entry['module_key']) ? 'bg-zinc-950 text-white' : 'bg-zinc-100 text-zinc-800';
                    $category_label = empty($entry['module_key']) ? 'Platform Core' : ($registered_modules[$entry['module_key']]['name'] ?? $entry['module_key']);
                    $status_colors = array(
                        'released' => 'bg-emerald-50 text-emerald-800 border-emerald-100',
                        'in_progress' => 'bg-amber-50 text-amber-800 border-amber-100',
                        'deprecated' => 'bg-rose-50 text-rose-800 border-rose-100',
                        'planned' => 'bg-zinc-100 text-zinc-500 border-zinc-200'
                    );
                    $status_badge = $status_colors[$entry['status']] ?? 'bg-zinc-100 text-zinc-500';
                ?>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-3 changelog-entry-card" data-module="<?php echo esc_attr($entry['module_key'] ?: 'platform'); ?>" data-status="<?php echo esc_attr($entry['status']); ?>">
                    <div class="flex items-start justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2.5">
                            <span class="px-2 py-0.5 text-[9.5px] font-bold rounded-full uppercase <?php echo $category_class; ?>"><?php echo esc_html($category_label); ?></span>
                            <span class="text-xs font-mono font-bold text-zinc-400">v<?php echo esc_html($entry['version']); ?></span>
                            <span class="text-[9.5px] px-2 py-0.5 rounded-full font-bold uppercase border <?php echo $status_badge; ?>"><?php echo esc_html($entry['status']); ?></span>
                            <?php if ( ! empty($entry['ticket_id']) ) : ?>
                            <span class="text-xs font-mono text-zinc-450 border-l border-zinc-200 pl-2">Ticket: <?php echo esc_html($entry['ticket_id']); ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="text-[10px] text-zinc-400 font-mono"><?php echo date('M j, Y', strtotime($entry['created_at'])); ?></span>
                    </div>

                    <h3 class="text-xs font-bold text-zinc-950 changelog-card-title"><?php echo esc_html($entry['title']); ?></h3>
                    <p class="text-xs text-zinc-700 leading-relaxed font-sans"><?php echo esc_html($entry['description']); ?></p>
                    
                    <div class="flex items-center justify-between pt-2 border-t border-zinc-100 ">
                        <span class="text-[10.5px] text-zinc-400">Recorded by Admin</span>
                        <button onclick="coraDocsEditChangelog(<?php echo htmlspecialchars(json_encode($entry)); ?>)" class="text-xs text-zinc-500 hover:text-zinc-850 flex items-center gap-1 cursor-pointer">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            Edit
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ───────────────────────────────────────────────────────────────────────
         SUBTAB 3: MODULES
         ─────────────────────────────────────────────────────────────────────── -->
    <div id="cora-docs-tab-modules" class="cora-docs-tab-content space-y-6 hidden">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            
            <!-- Left Modules Sidebar Navigation -->
            <div class="md:col-span-1 space-y-2">
                <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-450 px-2">Registered Modules</h3>
                <nav class="space-y-1">
                    <?php foreach ($registered_modules as $key => $mod) : 
                        $mod_doc = cora_find_doc_page( $all_pages, sanitize_title($key) );
                    ?>
                    <button onclick="coraDocsSwitchModule('<?php echo esc_attr($key); ?>')" class="w-full text-left px-3 py-2 text-xs rounded-lg font-medium transition-colors hover:bg-zinc-100 hover:text-zinc-900 text-zinc-700 flex items-center justify-between cursor-pointer cora-docs-mod-btn" id="mod-btn-<?php echo esc_attr($key); ?>">
                        <span><?php echo esc_html($mod['name']); ?></span>
                        <span class="px-1.5 py-0.5 rounded text-[8px] bg-zinc-200 text-zinc-650 uppercase tracking-wide"><?php echo esc_html($mod_doc['status'] ?? 'Draft'); ?></span>
                    </button>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- Right Module Documentation Editor -->
            <div class="md:col-span-3">
                <?php foreach ($registered_modules as $key => $mod) : 
                    $mod_doc = cora_find_doc_page( $all_pages, sanitize_title($key) );
                    $doc_content = $mod_doc['content'] ?? "# " . esc_html($mod['name']) . "\n\nConfigure module documentation page parameters.";
                ?>
                <div id="cora-docs-mod-content-<?php echo esc_attr($key); ?>" class="cora-docs-module-view-box space-y-4 hidden">
                    <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4 border-b border-zinc-150/40 pb-4">
                            <div>
                                <h2 class="text-sm font-bold text-zinc-900 flex items-center gap-2">
                                    <?php echo esc_html($mod['name']); ?> Page
                                    <span class="text-[9.5px] px-2 py-0.5 rounded-full font-bold bg-zinc-100 text-zinc-600 uppercase"><?php echo esc_html( $mod_doc['status'] ?? 'draft' ); ?></span>
                                </h2>
                                <p class="text-[10px] text-zinc-450 mt-0.5">Assigned key: <code class="font-mono text-zinc-650"><?php echo esc_html($key); ?></code></p>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <button onclick="coraDocsToggleEdit('<?php echo esc_attr($key); ?>')" class="text-xs px-2.5 py-1 border border-zinc-200 rounded-lg text-zinc-600 hover:bg-zinc-50 cursor-pointer" id="btn-edit-<?php echo esc_attr($key); ?>">Edit</button>
                                <button onclick="coraDocsOpenHistory(<?php echo intval( $mod_doc['id'] ?? 0 ); ?>)" class="text-xs px-2.5 py-1 border border-zinc-200 rounded-lg text-zinc-600 hover:bg-zinc-50 cursor-pointer">History</button>
                                
                                <?php if ( isset($mod_doc['status']) && $mod_doc['status'] === 'approved_internal' ) : ?>
                                <button onclick="coraDocsPublishPage(<?php echo intval( $mod_doc['id'] ); ?>, 'public_live')" class="text-xs px-3 py-1 bg-zinc-950 text-white rounded-lg select-none cursor-pointer">Publish to Public</button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Editor Area -->
                        <div id="cora-docs-editor-box-<?php echo esc_attr($key); ?>" class="hidden space-y-3">
                            <textarea id="cora-docs-textarea-<?php echo esc_attr($key); ?>" rows="15" class="w-full border border-zinc-200 rounded-lg p-3 text-xs bg-zinc-50 text-zinc-900 font-mono focus:outline-none"><?php echo esc_textarea( $doc_content ); ?></textarea>
                            <div class="flex items-center gap-2 justify-end">
                                <select id="cora-docs-status-<?php echo esc_attr($key); ?>" class="border border-zinc-200 rounded-lg px-2 py-1 text-xs text-zinc-700 bg-white focus:outline-none">
                                    <option value="draft" <?php selected( $mod_doc['status'] ?? 'draft', 'draft' ); ?>>Draft</option>
                                    <option value="approved_internal" <?php selected( $mod_doc['status'] ?? 'draft', 'approved_internal' ); ?>>Approved Internal</option>
                                    <option value="public_live" <?php selected( $mod_doc['status'] ?? 'draft', 'public_live' ); ?>>Public Live</option>
                                </select>
                                <button onclick="coraDocsSavePage('<?php echo esc_attr($key); ?>', <?php echo intval( $mod_doc['id'] ?? 0 ); ?>, '<?php echo esc_attr(sanitize_title($key)); ?>', '<?php echo esc_attr($mod['name']); ?>', 'modules', '<?php echo esc_attr($key); ?>')" class="text-xs px-3 py-1.5 bg-zinc-950 text-white rounded-lg font-bold select-none cursor-pointer">Save Module Docs</button>
                            </div>
                        </div>

                        <!-- Preview Area -->
                        <div id="cora-docs-preview-box-<?php echo esc_attr($key); ?>" class="prose max-w-none text-xs">
                            <?php echo cora_markdown_to_html( $doc_content ); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ───────────────────────────────────────────────────────────────────────
         SUBTAB 4: API REFERENCE
         ─────────────────────────────────────────────────────────────────────── -->
    <div id="cora-docs-tab-api" class="cora-docs-tab-content space-y-4 hidden">
        
        <!-- Filters Toolbar -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-3 items-center flex-1">
                <!-- Search APIs -->
                <div class="relative">
                    <input type="text" id="cora-api-search" oninput="coraDocsFilterApi()" class="border border-zinc-200 rounded-lg py-1.5 pl-8 pr-3 text-xs bg-white focus:outline-none" placeholder="Filter path or permissions...">
                    <span class="absolute left-2.5 top-2.5 text-zinc-400">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                </div>
                <!-- Method filter -->
                <select id="cora-api-method-filter" onchange="coraDocsFilterApi()" class="border border-zinc-200 rounded-lg px-2 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Methods</option>
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
                </select>
                <!-- MCP Filter -->
                <select id="cora-api-mcp-filter" onchange="coraDocsFilterApi()" class="border border-zinc-200 rounded-lg px-2 py-1.5 text-xs text-zinc-700 bg-white outline-none cursor-pointer">
                    <option value="">All Endpoints</option>
                    <option value="1">MCP Compatible Only</option>
                </select>
            </div>
            
            <button onclick="coraDocsOpenApiDrawer()" class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 text-white rounded-lg text-xs font-bold select-none cursor-pointer">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add API Endpoint
            </button>
        </div>

        <!-- Endpoints List -->
        <div class="space-y-4" id="cora-api-list">
            <?php if ( empty( $all_apis ) ) : ?>
                <div class="p-8 text-center text-zinc-400 bg-zinc-50 border border-zinc-200 rounded-xl text-xs">No API endpoints registered in registry.</div>
            <?php else : ?>
                <?php foreach ( $all_apis as $api ) : 
                    $method_colors = array(
                        'GET' => 'bg-zinc-100 text-zinc-900 border-zinc-200',
                        'POST' => 'bg-zinc-950 text-white border-zinc-950',
                        'PUT' => 'bg-zinc-150 text-zinc-800 border-zinc-300',
                        'DELETE' => 'bg-rose-50 text-rose-800 border-rose-155'
                    );
                    $method_class = $method_colors[$api['method']] ?? 'bg-zinc-100 text-zinc-800';
                ?>
                <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4 api-endpoint-card" data-method="<?php echo esc_attr($api['method']); ?>" data-mcp="<?php echo intval($api['mcp_compatible']); ?>">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 text-[9.5px] font-mono font-bold rounded-md uppercase border <?php echo $method_class; ?>"><?php echo esc_html($api['method']); ?></span>
                            <code class="text-xs font-mono font-bold text-zinc-950 api-card-path"><?php echo esc_html($api['path']); ?></code>
                        </div>
                        <div class="flex items-center gap-2">
                            <?php if ( $api['mcp_compatible'] ) : ?>
                            <span class="px-2 py-0.5 rounded-full text-[9px] bg-zinc-100 border border-zinc-300 text-zinc-700 font-bold uppercase tracking-wider flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> MCP Ready
                            </span>
                            <?php endif; ?>
                            <span class="px-2 py-0.5 rounded-full text-[9px] bg-zinc-50 border border-zinc-200 text-zinc-500 font-bold uppercase">Auth: <?php echo esc_html($api['permission_level']); ?></span>
                        </div>
                    </div>

                    <p class="text-xs text-zinc-650 leading-relaxed font-sans"><?php echo esc_html($api['description']); ?></p>

                    <!-- Toggleable details -->
                    <details class="group border border-zinc-150/60 rounded-lg overflow-hidden bg-zinc-50/50">
                        <summary class="flex justify-between items-center p-3 text-xs font-bold text-zinc-500 cursor-pointer select-none outline-none hover:bg-zinc-50">
                            Show Request/Response Details
                            <span class="transition-transform group-open:rotate-180">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </summary>
                        <div class="p-4 border-t border-zinc-150/60 space-y-3 bg-white text-[11px] font-mono">
                            <div>
                                <span class="text-zinc-450 font-bold text-[9px] uppercase tracking-wider block mb-1">Required Capabilities:</span>
                                <code class="bg-zinc-100 p-1 rounded"><?php echo esc_html($api['required_permissions'] ?: 'None'); ?></code>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-zinc-450 font-bold text-[9px] uppercase tracking-wider block mb-1">Request Payload Schema:</span>
                                    <pre class="bg-zinc-950 text-zinc-100 p-3 rounded-lg overflow-x-auto text-[10.5px] max-h-48"><?php echo esc_html($api['request_schema'] ?: '{}'); ?></pre>
                                </div>
                                <div>
                                    <span class="text-zinc-450 font-bold text-[9px] uppercase tracking-wider block mb-1">Response Payload Schema:</span>
                                    <pre class="bg-zinc-950 text-zinc-100 p-3 rounded-lg overflow-x-auto text-[10.5px] max-h-48"><?php echo esc_html($api['response_schema'] ?: '{}'); ?></pre>
                                </div>
                            </div>
                            <?php if ( ! empty($api['example']) ) : ?>
                            <div>
                                <span class="text-zinc-450 font-bold text-[9px] uppercase tracking-wider block mb-1">API Call Example:</span>
                                <pre class="bg-zinc-900 text-zinc-100 p-3 rounded-lg overflow-x-auto text-[10.5px] max-h-64"><?php echo esc_html($api['example']); ?></pre>
                            </div>
                            <?php endif; ?>
                        </div>
                    </details>

                    <div class="flex items-center justify-end gap-3 pt-2.5 border-t border-zinc-100 ">
                        <button onclick="coraDocsEditApi(<?php echo htmlspecialchars(json_encode($api)); ?>)" class="text-xs text-zinc-500 hover:text-zinc-850 flex items-center gap-1 cursor-pointer">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            Edit Reference
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ───────────────────────────────────────────────────────────────────────
         SUBTAB 5: WORKSPACE GUIDES
         ─────────────────────────────────────────────────────────────────────── -->
    <div id="cora-docs-tab-guides" class="cora-docs-tab-content space-y-6 hidden">
        <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-bold text-zinc-900 flex items-center gap-1.5">
                    Workspace Combination Logic & Guide
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-zinc-100 text-zinc-650 uppercase"><?php echo esc_html( $guide_page['status'] ?? 'draft' ); ?></span>
                </h2>
                
                <div class="flex items-center gap-2">
                    <button onclick="coraDocsToggleEdit('guides')" class="text-xs px-2.5 py-1 border border-zinc-200 rounded-lg text-zinc-600 hover:bg-zinc-50 cursor-pointer" id="btn-edit-guides">Edit Guide</button>
                    <button onclick="coraDocsOpenHistory(<?php echo intval( $guide_page['id'] ?? 0 ); ?>)" class="text-xs px-2.5 py-1 border border-zinc-200 rounded-lg text-zinc-600 hover:bg-zinc-50 cursor-pointer">History</button>
                </div>
            </div>

            <!-- Editor Box -->
            <div id="cora-docs-guides-editor-box" class="hidden space-y-3">
                <textarea id="cora-docs-guides-textarea" rows="15" class="w-full border border-zinc-200 rounded-lg p-3 text-xs bg-zinc-50 text-zinc-900 font-mono focus:outline-none"><?php echo esc_textarea( $guide_page['content'] ?? '' ); ?></textarea>
                <div class="flex items-center gap-2 justify-end">
                    <select id="cora-docs-guides-status" class="border border-zinc-200 rounded-lg px-2 py-1 text-xs text-zinc-700 bg-white focus:outline-none">
                        <option value="draft" <?php selected( $guide_page['status'] ?? 'draft', 'draft' ); ?>>Draft</option>
                        <option value="approved_internal" <?php selected( $guide_page['status'] ?? 'draft', 'approved_internal' ); ?>>Approved Internal</option>
                        <option value="public_live" <?php selected( $guide_page['status'] ?? 'draft', 'public_live' ); ?>>Public Live</option>
                    </select>
                    <button onclick="coraDocsSavePage('guides', <?php echo intval( $guide_page['id'] ?? 0 ); ?>, 'workspace-configuration', 'Workspace Configuration Guide', 'guides')" class="text-xs px-3 py-1.5 bg-zinc-950 text-white rounded-lg font-bold select-none cursor-pointer">Save Guide</button>
                </div>
            </div>

            <!-- Preview Box -->
            <div id="cora-docs-guides-preview-box" class="prose max-w-none text-xs">
                <?php echo cora_markdown_to_html( $guide_page['content'] ?? 'No configuration guide page created yet. Click edit to begin.' ); ?>
            </div>
        </div>
    </div>

    <!-- ───────────────────────────────────────────────────────────────────────
         SUBTAB 6: RELEASE NOTES
         ─────────────────────────────────────────────────────────────────────── -->
    <div id="cora-docs-tab-release-notes" class="cora-docs-tab-content space-y-6 hidden">
        <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-zinc-150 pb-4">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900">Business-Facing Release Notes</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Non-technical explanations of new additions, bug fixes, and performance upgrades written specifically for tenant workspace owners.</p>
                </div>
                
                <button onclick="coraDocsPublishToPublicSite()" class="flex items-center gap-1.5 px-3.5 py-1.5 bg-zinc-950 text-white rounded-lg text-xs font-bold hover:bg-zinc-800 transition-colors select-none cursor-pointer shadow-xs">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><polyline points="16 6 12 2 8 6"></polyline><line x1="12" y1="2" x2="12" y2="15"></line></svg>
                    Publish to Public Site
                </button>
            </div>

            <!-- Release Notes Preview feed -->
            <div class="space-y-6 divide-y divide-zinc-200">
                <?php 
                $count_rel = 0;
                foreach ( $all_changelogs as $entry ) :
                    if ( $entry['status'] !== 'released' ) continue;
                    $count_rel++;
                ?>
                <div class="pt-4 first:pt-0 space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-zinc-900">Release v<?php echo esc_html($entry['version']); ?></span>
                        <span class="text-[10px] text-zinc-400 font-mono"><?php echo date('M j, Y', strtotime($entry['created_at'])); ?></span>
                    </div>
                    <h3 class="text-xs font-bold text-zinc-950"><?php echo esc_html($entry['title']); ?></h3>
                    <p class="text-xs text-zinc-650 font-sans leading-relaxed"><?php echo esc_html($entry['description']); ?></p>
                </div>
                <?php endforeach; ?>
                
                <?php if ( $count_rel === 0 ) : ?>
                    <div class="p-8 text-center text-zinc-450 text-xs">No released changelogs to compile notes from. Make some changelog entries first.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ───────────────────────────────────────────────────────────────────────
         SUBTAB 7: ROADMAP
         ─────────────────────────────────────────────────────────────────────── -->
    <div id="cora-docs-tab-roadmap" class="cora-docs-tab-content space-y-6 hidden">
        <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-bold text-zinc-900 flex items-center gap-1.5">
                    Roadmap & Milestones
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-zinc-100 text-zinc-650 uppercase"><?php echo esc_html( $roadmap_page['status'] ?? 'draft' ); ?></span>
                </h2>
                
                <div class="flex items-center gap-2">
                    <button onclick="coraDocsToggleEdit('roadmap')" class="text-xs px-2.5 py-1 border border-zinc-200 rounded-lg text-zinc-600 hover:bg-zinc-50 cursor-pointer" id="btn-edit-roadmap">Edit Roadmap</button>
                    <button onclick="coraDocsOpenHistory(<?php echo intval( $roadmap_page['id'] ?? 0 ); ?>)" class="text-xs px-2.5 py-1 border border-zinc-200 rounded-lg text-zinc-600 hover:bg-zinc-50 cursor-pointer">History</button>
                </div>
            </div>

            <!-- Editor Box -->
            <div id="cora-docs-roadmap-editor-box" class="hidden space-y-3">
                <textarea id="cora-docs-roadmap-textarea" rows="15" class="w-full border border-zinc-200 rounded-lg p-3 text-xs bg-zinc-50 text-zinc-900 font-mono focus:outline-none"><?php echo esc_textarea( $roadmap_page['content'] ?? '' ); ?></textarea>
                <div class="flex items-center gap-2 justify-end">
                    <select id="cora-docs-roadmap-status" class="border border-zinc-200 rounded-lg px-2 py-1 text-xs text-zinc-700 bg-white focus:outline-none">
                        <option value="draft" <?php selected( $roadmap_page['status'] ?? 'draft', 'draft' ); ?>>Draft</option>
                        <option value="approved_internal" <?php selected( $roadmap_page['status'] ?? 'draft', 'approved_internal' ); ?>>Approved Internal</option>
                        <option value="public_live" <?php selected( $roadmap_page['status'] ?? 'draft', 'public_live' ); ?>>Public Live</option>
                    </select>
                    <button onclick="coraDocsSavePage('roadmap', <?php echo intval( $roadmap_page['id'] ?? 0 ); ?>, 'roadmap', 'Roadmap', 'roadmap')" class="text-xs px-3 py-1.5 bg-zinc-950 text-white rounded-lg font-bold select-none cursor-pointer">Save Roadmap</button>
                </div>
            </div>

            <!-- Preview Box -->
            <div id="cora-docs-roadmap-preview-box" class="prose max-w-none text-xs">
                <?php echo cora_markdown_to_html( $roadmap_page['content'] ?? 'No platform roadmap created yet. Click edit to begin.' ); ?>
            </div>
        </div>
    </div>

</div>

<!-- ───────────────────────────────────────────────────────────────────────
     RIGHT SLIDING DRAWER: CHANGELOG FORM
     ─────────────────────────────────────────────────────────────────────── -->
<div id="cora-changelog-drawer" class="cora-docs-drawer fixed top-0 right-0 h-screen w-96 bg-white shadow-2xl border-l border-zinc-200 z-50 translate-x-full flex flex-col justify-between hidden">
    <div class="p-6 space-y-6 overflow-y-auto flex-1">
        <div class="flex items-center justify-between border-b border-zinc-150 pb-4">
            <h3 class="text-sm font-bold text-zinc-900" id="changelog-drawer-title">New Changelog Entry</h3>
            <button onclick="coraDocsCloseChangelogDrawer()" class="text-zinc-400 hover:text-zinc-650 cursor-pointer">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <form id="cora-changelog-form" class="space-y-4 text-xs">
            <input type="hidden" id="changelog-entry-id" value="0">
            
            <div class="space-y-1">
                <label class="font-bold text-zinc-500">Module Thread Context</label>
                <select id="changelog-form-module" class="w-full border border-zinc-200 rounded-lg p-2 bg-white focus:outline-none">
                    <option value="">Platform Core Level</option>
                    <?php foreach ($registered_modules as $key => $mod) : ?>
                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($mod['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-bold text-zinc-500">Version</label>
                    <input type="text" id="changelog-form-version" class="w-full border border-zinc-200 rounded-lg p-2 focus:outline-none" value="1.0.0">
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-zinc-500">Status</label>
                    <select id="changelog-form-status" class="w-full border border-zinc-200 rounded-lg p-2 bg-white focus:outline-none">
                        <option value="released">Released</option>
                        <option value="in_progress">In Progress</option>
                        <option value="deprecated">Deprecated</option>
                        <option value="planned">Planned</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-zinc-500">Ticket ID (Optional)</label>
                <input type="text" id="changelog-form-ticket" class="w-full border border-zinc-200 rounded-lg p-2 focus:outline-none" placeholder="e.g. CORA-123">
            </div>

            <div class="space-y-1">
                <label class="font-bold text-zinc-500">Brief Title</label>
                <input type="text" id="changelog-form-title" class="w-full border border-zinc-200 rounded-lg p-2 focus:outline-none" placeholder="Change summary header">
            </div>

            <div class="space-y-1">
                <label class="font-bold text-zinc-500">Description</label>
                <textarea id="changelog-form-desc" rows="6" class="w-full border border-zinc-200 rounded-lg p-2 focus:outline-none font-sans" placeholder="Describe what changed in detail..."></textarea>
            </div>
        </form>
    </div>
    
    <div class="p-6 border-t border-zinc-150 bg-zinc-50 flex items-center justify-end gap-3">
        <button onclick="coraDocsCloseChangelogDrawer()" class="px-4 py-2 border border-zinc-200 rounded-lg text-zinc-600 font-bold cursor-pointer hover:bg-zinc-100 select-none">Cancel</button>
        <button onclick="coraDocsSaveChangelogSubmit()" class="px-4 py-2 bg-zinc-950 text-white rounded-lg font-bold cursor-pointer hover:bg-zinc-800 select-none">Save Entry</button>
    </div>
</div>

<!-- ───────────────────────────────────────────────────────────────────────
     RIGHT SLIDING DRAWER: API FORM
     ─────────────────────────────────────────────────────────────────────── -->
<div id="cora-api-drawer" class="cora-docs-drawer fixed top-0 right-0 h-screen w-[28rem] bg-white shadow-2xl border-l border-zinc-200 z-50 translate-x-full flex flex-col justify-between hidden">
    <div class="p-6 space-y-6 overflow-y-auto flex-1">
        <div class="flex items-center justify-between border-b border-zinc-150 pb-4">
            <h3 class="text-sm font-bold text-zinc-900" id="api-drawer-title">Register API Endpoint</h3>
            <button onclick="coraDocsCloseApiDrawer()" class="text-zinc-400 hover:text-zinc-650 cursor-pointer">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <form id="cora-api-form" class="space-y-4 text-xs">
            <input type="hidden" id="api-entry-id" value="0">
            
            <div class="grid grid-cols-3 gap-3">
                <div class="space-y-1">
                    <label class="font-bold text-zinc-500">Method</label>
                    <select id="api-form-method" class="w-full border border-zinc-200 rounded-lg p-2 bg-white focus:outline-none">
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                </div>
                <div class="col-span-2 space-y-1">
                    <label class="font-bold text-zinc-500">Path</label>
                    <input type="text" id="api-form-path" class="w-full border border-zinc-200 rounded-lg p-2 focus:outline-none" placeholder="/api/v1/resource">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-zinc-500">Description</label>
                <textarea id="api-form-description" rows="3" class="w-full border border-zinc-200 rounded-lg p-2 focus:outline-none" placeholder="Endpoint functionality details..."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-bold text-zinc-500">Auth Tier</label>
                    <select id="api-form-auth" class="w-full border border-zinc-200 rounded-lg p-2 bg-white focus:outline-none">
                        <option value="public">Public (Open)</option>
                        <option value="member">Member Role</option>
                        <option value="admin">Admin Role</option>
                        <option value="owner">Workspace Owner</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="font-bold text-zinc-500">Required Permissions</label>
                    <input type="text" id="api-form-perms" class="w-full border border-zinc-200 rounded-lg p-2 focus:outline-none" placeholder="e.g. read_vault">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-zinc-500">Request Schema (JSON)</label>
                <textarea id="api-form-req-schema" rows="4" class="w-full border border-zinc-200 rounded-lg p-2 focus:outline-none font-mono" placeholder="{}"></textarea>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-zinc-500">Response Schema (JSON)</label>
                <textarea id="api-form-res-schema" rows="4" class="w-full border border-zinc-200 rounded-lg p-2 focus:outline-none font-mono" placeholder="{}"></textarea>
            </div>

            <div class="space-y-1">
                <label class="font-bold text-zinc-500">Example Request/Response Block</label>
                <textarea id="api-form-example" rows="4" class="w-full border border-zinc-200 rounded-lg p-2 focus:outline-none font-mono" placeholder="Example payload structure"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-2 select-none">
                <input type="checkbox" id="api-form-mcp" class="rounded border-zinc-300 focus:ring-0">
                <label for="api-form-mcp" class="font-bold text-zinc-650 cursor-pointer">MCP Compatibility Flag Enabled</label>
            </div>
        </form>
    </div>
    
    <div class="p-6 border-t border-zinc-150 bg-zinc-50 flex items-center justify-end gap-3">
        <button onclick="coraDocsCloseApiDrawer()" class="px-4 py-2 border border-zinc-200 rounded-lg text-zinc-600 font-bold cursor-pointer hover:bg-zinc-100 select-none">Cancel</button>
        <button onclick="coraDocsSaveApiSubmit()" class="px-4 py-2 bg-zinc-950 text-white rounded-lg font-bold cursor-pointer hover:bg-zinc-800 select-none">Save Endpoint</button>
    </div>
</div>

<!-- ───────────────────────────────────────────────────────────────────────
     RIGHT SLIDING DRAWER: VERSION HISTORY
     ─────────────────────────────────────────────────────────────────────── -->
<div id="cora-history-drawer" class="cora-docs-drawer fixed top-0 right-0 h-screen w-96 bg-white shadow-2xl border-l border-zinc-200 z-50 translate-x-full flex flex-col justify-between hidden">
    <div class="p-6 space-y-6 overflow-y-auto flex-1">
        <div class="flex items-center justify-between border-b border-zinc-150 pb-4">
            <h3 class="text-sm font-bold text-zinc-900">Page Version History</h3>
            <button onclick="coraDocsCloseHistory()" class="text-zinc-400 hover:text-zinc-650 cursor-pointer">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- History Timeline -->
        <div class="space-y-4" id="cora-history-list-box">
            <!-- Populated via AJAX -->
            <div class="text-center text-zinc-400 py-6 text-xs">Loading history entries...</div>
        </div>
    </div>
    
    <div class="p-6 border-t border-zinc-150 bg-zinc-50 flex items-center justify-end">
        <button onclick="coraDocsCloseHistory()" class="px-4 py-2 border border-zinc-200 rounded-lg text-zinc-600 font-bold cursor-pointer hover:bg-zinc-100 select-none">Close Drawer</button>
    </div>
</div>

<!-- Backdrop overlay for drawers -->
<div id="cora-docs-drawer-backdrop" class="fixed inset-0 bg-black/15 z-40 hidden" onclick="coraDocsCloseAllDrawers()"></div>

<!-- JAVASCRIPT LOGIC -->
<script>
    // Tab switching
    function coraDocsSwitchTab(tabId) {
        document.querySelectorAll('.cora-docs-tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.cora-docs-subtab-btn').forEach(btn => btn.classList.remove('active'));
        
        const targetTab = document.getElementById('cora-docs-tab-' + tabId);
        if (targetTab) targetTab.classList.remove('hidden');
        
        const targetBtn = document.getElementById('tab-btn-' + tabId);
        if (targetBtn) targetBtn.classList.add('active');
        
        // Save state in localStorage
        localStorage.setItem('cora_docs_active_tab', tabId);
    }

    // Toggle inline editors
    function coraDocsToggleEdit(key) {
        const editorBox = document.getElementById('cora-docs-' + key + '-editor-box');
        const previewBox = document.getElementById('cora-docs-' + key + '-preview-box');
        const btn = document.getElementById('btn-edit-' + key);
        
        if (editorBox.classList.contains('hidden')) {
            editorBox.classList.remove('hidden');
            previewBox.classList.add('hidden');
            btn.textContent = 'Preview';
        } else {
            editorBox.classList.add('hidden');
            previewBox.classList.remove('hidden');
            btn.textContent = 'Edit';
            
            // Generate client-side markdown preview when toggling back
            const txt = document.getElementById('cora-docs-' + key + '-textarea').value;
            coraDocsLocalMarkdownPreview(txt, previewBox);
        }
    }

    // Client-side simple markdown parser simulation for instantaneous preview updates
    function coraDocsLocalMarkdownPreview(md, destElement) {
        if (!md) {
            destElement.innerHTML = '<p class="text-zinc-400 italic">No content written.</p>';
            return;
        }
        
        let html = md
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/^\#\#\# (.*?)$/gm, '<h3 class="text-sm font-bold text-zinc-950 mt-6 mb-2 flex items-center gap-1">$1</h3>')
            .replace(/^\#\# (.*?)$/gm, '<h2 class="text-base font-bold text-zinc-950 border-b border-zinc-200/80 pb-1 mt-7 mb-3">$1</h2>')
            .replace(/^\# (.*?)$/gm, '<h1 class="text-xl font-extrabold text-zinc-950 tracking-tight mt-4 mb-4">$1</h1>')
            .replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-zinc-900">$1</strong>')
            .replace(/\*(.*?)\*/g, '<em class="italic text-zinc-800">$1</em>')
            .replace(/\`(.*?)\`/g, '<code class="bg-zinc-100 border border-zinc-200/60 text-zinc-800 px-1.5 py-0.5 rounded font-mono text-[10.5px]">$1</code>')
            .replace(/^\- (.*?)$/gm, '<li class="ml-4 list-disc text-xs text-zinc-700 my-1">$1</li>');

        const lines = html.split('\n');
        const processed = [];
        let inList = false;
        
        lines.forEach(line => {
            const trimmed = line.trim();
            if (!trimmed) {
                if (inList) {
                    processed.push('</ul>');
                    inList = false;
                }
                return;
            }
            
            if (trimmed.indexOf('<li') === 0) {
                if (!inList) {
                    processed.push('<ul class="space-y-1 my-3">');
                    inList = true;
                }
                processed.push(line);
            } else {
                if (inList) {
                    processed.push('</ul>');
                    inList = false;
                }
                if (trimmed.indexOf('<h') === 0 || trimmed.indexOf('<div') === 0 || trimmed.indexOf('<ul') === 0) {
                    processed.push(line);
                } else {
                    processed.push('<p class="text-xs leading-relaxed text-zinc-700 my-2.5">' + line + '</p>');
                }
            }
        });
        if (inList) processed.push('</ul>');
        
        destElement.innerHTML = processed.join('\n');
    }

    // Save Documentation Page AJAX
    function coraDocsSavePage(editorKey, id, slug, title, category, moduleKey = '') {
        const content = document.getElementById('cora-docs-' + editorKey + '-textarea').value;
        const status = document.getElementById('cora-docs-' + editorKey + '-status') ? document.getElementById('cora-docs-' + editorKey + '-status').value : 'draft';
        
        jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'cora_save_doc_page',
                nonce: '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                id: id,
                slug: slug,
                title: title,
                content: content,
                category: category,
                module_key: moduleKey,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    window.coraShowToast('success', response.data.message || 'Page updated successfully.');
                    
                    // Reload page after a delay to pull DB changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    window.coraShowToast('error', response.data.message || 'Error occurred while saving.');
                }
            },
            error: function() {
                window.coraShowToast('error', 'Connection failure: Could not execute save action.');
            }
        });
    }

    // Publish page to public doc site
    function coraDocsPublishPage(pageId, status) {
        jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'cora_publish_doc_page',
                nonce: '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                id: pageId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    window.coraShowToast('success', response.data.message || 'Published to public documents site!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    window.coraShowToast('error', response.data.message || 'Failed to publish.');
                }
            },
            error: function() {
                window.coraShowToast('error', 'Connection failure: Action failed.');
            }
        });
    }

    // Version History Drawer Management
    let coraActivePageHistoryId = 0;
    
    function coraDocsOpenHistory(pageId) {
        if (!pageId) {
            window.coraShowToast('error', 'Save the page first to enable versioning history.');
            return;
        }
        
        coraActivePageHistoryId = pageId;
        
        document.getElementById('cora-docs-drawer-backdrop').classList.remove('hidden');
        const drawer = document.getElementById('cora-history-drawer');
        drawer.classList.remove('hidden');
        setTimeout(() => drawer.classList.add('translate-x-0'), 50);
        
        // Fetch logs via AJAX
        jQuery.ajax({
            url: ajaxurl,
            method: 'GET',
            data: {
                action: 'cora_get_doc_history',
                nonce: '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                page_id: pageId
            },
            success: function(response) {
                if (response.success) {
                    let html = '';
                    if (response.data.length === 0) {
                        html = '<div class="text-center py-8 text-zinc-400 text-xs">No version backups created yet. Content creates backups automatically when edits are saved.</div>';
                    } else {
                        response.data.forEach(v => {
                            html += `
                            <div class="bg-zinc-50 border border-zinc-200 rounded-lg p-3 text-xs flex justify-between items-center">
                                <div>
                                    <span class="font-bold text-zinc-800 block">${v.version_label}</span>
                                    <span class="text-[10px] text-zinc-450 mt-0.5 block">Saved on: ${v.created_at} by ${v.display_name || 'Admin'}</span>
                                </div>
                                <button onclick="coraDocsRevertVersion(${v.id})" class="text-xs px-2 py-1 bg-zinc-950 text-white rounded font-bold hover:bg-zinc-800 select-none cursor-pointer">Revert</button>
                            </div>`;
                        });
                    }
                    document.getElementById('cora-history-list-box').innerHTML = html;
                } else {
                    document.getElementById('cora-history-list-box').innerHTML = '<div class="text-center text-red-500 py-6 text-xs">Failed to load history list.</div>';
                }
            },
            error: function() {
                document.getElementById('cora-history-list-box').innerHTML = '<div class="text-center text-red-500 py-6 text-xs">Connection error.</div>';
            }
        });
    }
    
    function coraDocsCloseHistory() {
        const drawer = document.getElementById('cora-history-drawer');
        drawer.classList.remove('translate-x-0');
        setTimeout(() => {
            drawer.classList.add('hidden');
            document.getElementById('cora-docs-drawer-backdrop').classList.add('hidden');
        }, 300);
    }

    function coraDocsRevertVersion(versionId) {
        const doRevert = function() {
            jQuery.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'cora_revert_doc_page',
                    nonce: '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                    version_id: versionId
                },
                success: function(response) {
                    if (response.success) {
                        window.coraShowToast('success', response.data.message || 'Reverted successfully.');
                        coraDocsCloseHistory();
                        setTimeout(() => {
                            window.location.reload();
                        }, 800);
                    } else {
                        window.coraShowToast('error', response.data.message || 'Failed to revert.');
                    }
                },
                error: function() {
                    window.coraShowToast('error', 'Revert action communication error.');
                }
            });
        };

        if (window.coraConfirmAction) {
            window.coraConfirmAction('Revert Version Backup', 'Are you sure you want to revert to this version backup? The current workspace version will be archived.', doRevert);
        } else {
            doRevert();
        }
    }

    // Changelog Drawer Management
    function coraDocsOpenChangelogDrawer() {
        document.getElementById('changelog-entry-id').value = '0';
        document.getElementById('cora-changelog-form').reset();
        document.getElementById('changelog-drawer-title').textContent = 'New Changelog Entry';
        
        document.getElementById('cora-docs-drawer-backdrop').classList.remove('hidden');
        const drawer = document.getElementById('cora-changelog-drawer');
        drawer.classList.remove('hidden');
        setTimeout(() => drawer.classList.add('translate-x-0'), 50);
    }
    
    function coraDocsCloseChangelogDrawer() {
        const drawer = document.getElementById('cora-changelog-drawer');
        drawer.classList.remove('translate-x-0');
        setTimeout(() => {
            drawer.classList.add('hidden');
            document.getElementById('cora-docs-drawer-backdrop').classList.add('hidden');
        }, 300);
    }

    function coraDocsEditChangelog(data) {
        coraDocsOpenChangelogDrawer();
        document.getElementById('changelog-drawer-title').textContent = 'Edit Changelog Entry';
        document.getElementById('changelog-entry-id').value = data.id;
        document.getElementById('changelog-form-module').value = data.module_key || '';
        document.getElementById('changelog-form-version').value = data.version;
        document.getElementById('changelog-form-status').value = data.status;
        document.getElementById('changelog-form-ticket').value = data.ticket_id || '';
        document.getElementById('changelog-form-title').value = data.title;
        document.getElementById('changelog-form-desc').value = data.description;
    }

    function coraDocsSaveChangelogSubmit() {
        const id = document.getElementById('changelog-entry-id').value;
        const module_key = document.getElementById('changelog-form-module').value;
        const version = document.getElementById('changelog-form-version').value;
        const status = document.getElementById('changelog-form-status').value;
        const ticket_id = document.getElementById('changelog-form-ticket').value;
        const title = document.getElementById('changelog-form-title').value;
        const description = document.getElementById('changelog-form-desc').value;

        if (!title) {
            window.coraShowToast('error', 'Changelog title is required.');
            return;
        }

        jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'cora_save_changelog',
                nonce: '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                id: id,
                module_key: module_key,
                version: version,
                status: status,
                ticket_id: ticket_id,
                title: title,
                description: description
            },
            success: function(response) {
                if (response.success) {
                    window.coraShowToast('success', response.data.message || 'Changelog saved.');
                    coraDocsCloseChangelogDrawer();
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    window.coraShowToast('error', response.data.message || 'Error occurred.');
                }
            },
            error: function() {
                window.coraShowToast('error', 'Server error saving changelog.');
            }
        });
    }

    // API Drawer Management
    function coraDocsOpenApiDrawer() {
        document.getElementById('api-entry-id').value = '0';
        document.getElementById('cora-api-form').reset();
        document.getElementById('api-drawer-title').textContent = 'Register API Endpoint';
        
        document.getElementById('cora-docs-drawer-backdrop').classList.remove('hidden');
        const drawer = document.getElementById('cora-api-drawer');
        drawer.classList.remove('hidden');
        setTimeout(() => drawer.classList.add('translate-x-0'), 50);
    }
    
    function coraDocsCloseApiDrawer() {
        const drawer = document.getElementById('cora-api-drawer');
        drawer.classList.remove('translate-x-0');
        setTimeout(() => {
            drawer.classList.add('hidden');
            document.getElementById('cora-docs-drawer-backdrop').classList.add('hidden');
        }, 300);
    }

    function coraDocsEditApi(data) {
        coraDocsOpenApiDrawer();
        document.getElementById('api-drawer-title').textContent = 'Edit API Endpoint Reference';
        document.getElementById('api-entry-id').value = data.id;
        document.getElementById('api-form-method').value = data.method;
        document.getElementById('api-form-path').value = data.path;
        document.getElementById('api-form-description').value = data.description;
        document.getElementById('api-form-auth').value = data.permission_level;
        document.getElementById('api-form-perms').value = data.required_permissions || '';
        document.getElementById('api-form-req-schema').value = data.request_schema;
        document.getElementById('api-form-res-schema').value = data.response_schema;
        document.getElementById('api-form-example').value = data.example;
        document.getElementById('api-form-mcp').checked = parseInt(data.mcp_compatible) === 1;
    }

    function coraDocsSaveApiSubmit() {
        const id = document.getElementById('api-entry-id').value;
        const method = document.getElementById('api-form-method').value;
        const path = document.getElementById('api-form-path').value;
        const description = document.getElementById('api-form-description').value;
        const permission_level = document.getElementById('api-form-auth').value;
        const required_permissions = document.getElementById('api-form-perms').value;
        const request_schema = document.getElementById('api-form-req-schema').value;
        const response_schema = document.getElementById('api-form-res-schema').value;
        const example = document.getElementById('api-form-example').value;
        const mcp_compatible = document.getElementById('api-form-mcp').checked ? 1 : 0;

        if (!path) {
            window.coraShowToast('error', 'Path route is required.');
            return;
        }

        jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'cora_save_api_endpoint',
                nonce: '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                id: id,
                method: method,
                path: path,
                description: description,
                permission_level: permission_level,
                required_permissions: required_permissions,
                request_schema: request_schema,
                response_schema: response_schema,
                example: example,
                mcp_compatible: mcp_compatible
            },
            success: function(response) {
                if (response.success) {
                    window.coraShowToast('success', response.data.message || 'API Reference updated.');
                    coraDocsCloseApiDrawer();
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    window.coraShowToast('error', response.data.message || 'Error occurred.');
                }
            },
            error: function() {
                window.coraShowToast('error', 'Server connection error.');
            }
        });
    }

    // Publish compilation of release notes
    function coraDocsPublishToPublicSite() {
        window.coraShowToast('success', 'Public release notes pipeline triggered. Compilation live at /docs');
    }

    // Global backdrop closer
    function coraDocsCloseAllDrawers() {
        coraDocsCloseHistory();
        coraDocsCloseChangelogDrawer();
        coraDocsCloseApiDrawer();
    }

    // Left Navigation Modules switches
    function coraDocsSwitchModule(modKey) {
        document.querySelectorAll('.cora-docs-module-view-box').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.cora-docs-mod-btn').forEach(btn => btn.classList.remove('bg-zinc-100', 'text-zinc-900'));
        
        const targetBox = document.getElementById('cora-docs-mod-content-' + modKey);
        if (targetBox) targetBox.classList.remove('hidden');
        
        const targetBtn = document.getElementById('mod-btn-' + modKey);
        if (targetBtn) targetBtn.classList.add('bg-zinc-100', 'text-zinc-900');
        
        localStorage.setItem('cora_docs_active_module', modKey);
    }

    // Global Search Filter
    function coraDocsGlobalSearch(val) {
        const query = val.toLowerCase().trim();
        if (!query) {
            // Restore default displays
            document.querySelectorAll('.changelog-entry-card').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.api-endpoint-card').forEach(el => el.classList.remove('hidden'));
            return;
        }

        // Search changelogs titles/descriptions
        document.querySelectorAll('.changelog-entry-card').forEach(card => {
            const title = card.querySelector('.changelog-card-title').textContent.toLowerCase();
            const desc = card.querySelector('p').textContent.toLowerCase();
            if (title.indexOf(query) !== -1 || desc.indexOf(query) !== -1) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });

        // Search API endpoints paths/descriptions
        document.querySelectorAll('.api-endpoint-card').forEach(card => {
            const path = card.querySelector('.api-card-path').textContent.toLowerCase();
            const desc = card.querySelector('p').textContent.toLowerCase();
            if (path.indexOf(query) !== -1 || desc.indexOf(query) !== -1) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    // Changelog Filters
    function coraDocsFilterChangelog() {
        const search = document.getElementById('cora-changelog-search').value.toLowerCase().trim();
        const moduleVal = document.getElementById('cora-changelog-module-filter').value;
        const statusVal = document.getElementById('cora-changelog-status-filter').value;

        document.querySelectorAll('.changelog-entry-card').forEach(card => {
            const cardModule = card.getAttribute('data-module');
            const cardStatus = card.getAttribute('data-status');
            const title = card.querySelector('.changelog-card-title').textContent.toLowerCase();
            const desc = card.querySelector('p').textContent.toLowerCase();

            let match = true;

            if (moduleVal && cardModule !== moduleVal) match = false;
            if (statusVal && cardStatus !== statusVal) match = false;
            if (search && (title.indexOf(search) === -1 && desc.indexOf(search) === -1)) match = false;

            if (match) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    // API Reference Filters
    function coraDocsFilterApi() {
        const search = document.getElementById('cora-api-search').value.toLowerCase().trim();
        const methodVal = document.getElementById('cora-api-method-filter').value;
        const mcpVal = document.getElementById('cora-api-mcp-filter').value;

        document.querySelectorAll('.api-endpoint-card').forEach(card => {
            const cardMethod = card.getAttribute('data-method');
            const cardMcp = card.getAttribute('data-mcp');
            const path = card.querySelector('.api-card-path').textContent.toLowerCase();
            const desc = card.querySelector('p').textContent.toLowerCase();

            let match = true;

            if (methodVal && cardMethod !== methodVal) match = false;
            if (mcpVal && cardMcp !== mcpVal) match = false;
            if (search && (path.indexOf(search) === -1 && desc.indexOf(search) === -1)) match = false;

            if (match) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    function coraDocsSimulateTrigger(eventKey) {
        jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'cora_simulate_doc_trigger',
                nonce: '<?php echo wp_create_nonce("cora_ajax_nonce"); ?>',
                event: eventKey
            },
            success: function(response) {
                if (response.success) {
                    window.coraShowToast('success', response.data.message || 'Simulation completed.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    window.coraShowToast('error', response.data.message || 'Simulation failed.');
                }
            },
            error: function() {
                window.coraShowToast('error', 'Simulation API communication error.');
            }
        });
    }

    // Initialize View States on document ready
    jQuery(document).ready(function() {
        const activeTab = localStorage.getItem('cora_docs_active_tab') || 'overview';
        coraDocsSwitchTab(activeTab);

        const activeModule = localStorage.getItem('cora_docs_active_module') || 'user-management';
        coraDocsSwitchModule(activeModule);
    });
</script>
