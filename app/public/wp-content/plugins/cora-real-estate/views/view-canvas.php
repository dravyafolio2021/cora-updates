<?php
/**
 * View: Canvas Front-End Management System
 * Monochromatic Shopify/Notion UI Shell around Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$user = wp_get_current_user();
$current_role = ! empty( $user->roles ) ? $user->roles[0] : 'subscriber';
$is_read_only = ( $current_role === 'cora_branch_manager' );

// Fetch active theme
$live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes WHERE status = 'live' LIMIT 1", ARRAY_A );
if ( ! $live_theme ) {
    $live_theme = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes LIMIT 1", ARRAY_A );
}
$live_settings = $live_theme ? (json_decode($live_theme['settings'], true) ?: array()) : array();

// Fetch all themes
$themes = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes ORDER BY status = 'live' DESC, id DESC", ARRAY_A );

// Fetch WP pages for dropdown selectors
$wp_pages = get_pages();
?>

<!-- Include CodeMirror Assets for CSS/JS Editor -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/neat.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>

<style>
    /* Monochromatic Transitions & Custom Scrollbars */
    .canvas-tab-btn.active {
        border-bottom-color: #18181b;
        color: #18181b;
    }
    .theme-preview-box {
        background-color: #f4f4f5;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    .theme-preview-box.draft::after {
        content: attr(data-status-label);
        position: absolute;
        inset: 0;
        background: rgba(24, 24, 27, 0.4);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    .cora-canvas-editor-active .cora-sidebar {
        width: 64px !important;
    }
    .cora-canvas-editor-active .cora-sidebar .cora-nav-text,
    .cora-canvas-editor-active .cora-sidebar .cora-nav-group-label {
        display: none !important;
    }
    .cora-canvas-editor-active .cora-header {
        display: none !important;
    }
    .cora-canvas-editor-active .cora-content-wrapper {
        padding-top: 0 !important;
        padding-left: 64px !important;
    }
    /* Menu Item Nesting Indentation */
    .menu-item-nested {
        margin-left: 28px !important;
        border-left: 2px solid #e4e4e7;
        padding-left: 12px;
    }
</style>

<div class="space-y-6" id="cora-canvas-container">
    
    <!-- LEVEL 1 — CANVAS HUB -->
    <div id="canvas-level-1" class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 tracking-tight">Canvas</h1>
                <p class="text-xs text-zinc-500 mt-1">Manage your agency's website, pages, and themes.</p>
            </div>
            <?php if ( ! $is_read_only ) : ?>
            <div class="flex items-center gap-2">
                <button onclick="openImportKitDrawer()" class="px-3 py-1.5 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 shadow-sm cursor-pointer transition-all flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Import Kit
                </button>
                <button onclick="openNewThemeDrawer()" class="px-3 py-1.5 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 shadow-sm cursor-pointer transition-all flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                    + New Theme
                </button>
                <button onclick="openNewPageDrawer()" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold shadow-sm cursor-pointer transition-all flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                    + New Page
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Active Theme Card -->
        <?php if ( $live_theme ) : 
            $live_stats = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d", $live_theme['id'] ), ARRAY_A );
            $pub_count = 0; $dr_count = 0; $seo_issues = 0;
            foreach ( $live_stats as $ls ) {
                if ( $ls['status'] === 'published' ) $pub_count++;
                else $dr_count++;
                if ( empty( $ls['seo_title'] ) || empty( $ls['seo_description'] ) ) $seo_issues++;
            }
        ?>
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                <!-- Left: Preview Thumbnail -->
                <div class="lg:col-span-2 flex flex-col md:flex-row gap-6">
                    <div class="w-full md:w-[280px] h-[160px] rounded-lg border border-zinc-200 theme-preview-box relative shadow-sm overflow-hidden flex items-center justify-center bg-zinc-50 select-none pointer-events-none">
                        <iframe src="<?php echo home_url('/'); ?>" class="absolute inset-0 border-none select-none pointer-events-none origin-top-left" style="width: 800px; height: 457px; transform: scale(0.35); pointer-events: none; border: none;"></iframe>
                    </div>
                    <div class="flex-1 flex flex-col justify-between py-1">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-green-50 text-green-700 border border-green-200">Live</span>
                                <span class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider">Active Theme</span>
                                <span class="text-xs text-zinc-400">• Last edited 2 days ago</span>
                            </div>
                            <h2 class="text-lg font-bold text-zinc-900"><?php echo esc_html( $live_theme['name'] ); ?></h2>
                            <p class="text-xs text-zinc-500 mt-1"><?php echo count($live_stats); ?> pages registered in this theme</p>
                        </div>
                        <div class="flex items-center gap-2 mt-4 md:mt-0">
                            <button onclick="editTheme(<?php echo $live_theme['id']; ?>, '<?php echo esc_js($live_theme['name']); ?>', true)" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold shadow-sm cursor-pointer transition-all">Edit Theme</button>
                            <a href="<?php echo home_url('/'); ?>" target="_blank" class="px-3 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 shadow-sm cursor-pointer transition-all">Preview</a>
                            <?php if ( ! $is_read_only ) : ?>
                            <button onclick="openThemeSettingsDrawer(<?php echo $live_theme['id']; ?>)" class="px-3 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 shadow-sm cursor-pointer transition-all">Settings</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Right: Quick Stats -->
                <div class="border-t lg:border-t-0 lg:border-l border-zinc-100 pt-6 lg:pt-0 lg:pl-6 flex flex-col justify-center">
                    <h3 class="text-[10px] font-bold uppercase text-zinc-400 tracking-wider mb-3">Live Website Performance</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-zinc-50/50 rounded-lg border border-zinc-100 shadow-none hover:shadow-sm transition-all duration-200 hover:border-zinc-300">
                            <div class="text-[10px] text-zinc-400">Total Pages</div>
                            <div class="text-lg font-bold text-zinc-800 mt-0.5"><?php echo count($live_stats); ?></div>
                        </div>
                        <div class="p-3 bg-zinc-50/50 rounded-lg border border-zinc-100 shadow-none hover:shadow-sm transition-all duration-200 hover:border-zinc-300">
                            <div class="text-[10px] text-zinc-400">Published</div>
                            <div class="text-lg font-bold text-zinc-800 mt-0.5"><?php echo $pub_count; ?></div>
                        </div>
                        <div class="p-3 bg-zinc-50/50 rounded-lg border border-zinc-100 shadow-none hover:shadow-sm transition-all duration-200 hover:border-zinc-300">
                            <div class="text-[10px] text-zinc-400">Drafts</div>
                            <div class="text-lg font-bold text-zinc-800 mt-0.5"><?php echo $dr_count; ?></div>
                        </div>
                        <div class="p-3 rounded-lg border shadow-none hover:shadow-sm transition-all duration-200 <?php echo $seo_issues > 0 ? 'bg-amber-50/30 border-amber-100 text-amber-800 hover:border-amber-300' : 'bg-zinc-50/50 border-zinc-100 text-zinc-800 hover:border-zinc-300'; ?>">
                            <div class="text-[10px] text-zinc-400">SEO Warnings</div>
                            <div class="text-lg font-bold mt-0.5"><?php echo $seo_issues; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Draft Themes Section -->
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-zinc-900">Drafts & Inactive</h3>
                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-zinc-100 text-zinc-500"><?php echo max(0, count($themes) - 1); ?></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php 
                $has_drafts = false;
                foreach ( $themes as $th ) {
                    if ( $th['status'] === 'live' ) continue;
                    $has_drafts = true;
                    $page_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d", $th['id'] ) );
                    
                    // Render varied visual simulated layout templates for draft cards
                    $is_alternate = ($th['id'] % 2 === 0);
                ?>
                <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm flex flex-col group hover:shadow-md hover:border-zinc-300 transition-all duration-300">
                    <div class="h-[140px] theme-preview-box draft relative flex flex-col bg-white border-b border-zinc-100 p-3 select-none pointer-events-none overflow-hidden" data-status-label="Draft">
                        <?php if ($is_alternate) : ?>
                            <!-- Blog / Article Feed Simulator -->
                            <div class="flex justify-between border-b border-zinc-100 pb-1 mb-1.5">
                                <div class="font-bold text-[5px] text-zinc-900 uppercase tracking-wider">Apex Realty Blog</div>
                                <div class="w-1.5 h-1.5 rounded-full bg-zinc-200"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-1.5 flex-1">
                                <div class="border border-zinc-100 rounded p-1 bg-zinc-50/30 flex flex-col justify-between">
                                    <div class="h-6 bg-zinc-100 rounded mb-1"></div>
                                    <div>
                                        <div class="font-bold text-[4px] text-zinc-800 leading-tight">Nitin & Shanaya Arora: Premium Market Forecast</div>
                                        <div class="text-[3px] text-zinc-400 mt-0.5">Read →</div>
                                    </div>
                                </div>
                                <div class="border border-zinc-100 rounded p-1 bg-zinc-50/30 flex flex-col justify-between">
                                    <div class="h-6 bg-zinc-100 rounded mb-1"></div>
                                    <div>
                                        <div class="font-bold text-[4px] text-zinc-800 leading-tight">Luxury Showings in Delhi & Mumbai</div>
                                        <div class="text-[3px] text-zinc-400 mt-0.5">Read →</div>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
                            <!-- Photography Grid / Minimal Listings Simulator -->
                            <div class="flex items-center gap-1 border-b border-zinc-100 pb-1 mb-1.5 justify-between">
                                <div class="font-semibold text-[5px] text-zinc-900 tracking-wider">APEX REALTY GALLERY</div>
                                <div class="flex gap-0.5">
                                    <div class="w-1 h-1 rounded-full bg-zinc-200"></div>
                                    <div class="w-1 h-1 rounded-full bg-zinc-200"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-1 flex-1">
                                <div class="bg-zinc-50 border border-zinc-100 rounded flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="8" height="8" stroke="#a1a1aa" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path></svg>
                                </div>
                                <div class="bg-zinc-50 border border-zinc-100 rounded flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="8" height="8" stroke="#a1a1aa" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path></svg>
                                </div>
                                <div class="bg-zinc-50 border border-zinc-100 rounded flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="8" height="8" stroke="#a1a1aa" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path></svg>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-zinc-900 mb-1"><?php echo esc_html( $th['name'] ); ?></h4>
                            <p class="text-[10px] text-zinc-500"><?php echo $page_count; ?> Pages • Last modified 2 days ago</p>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <button onclick="editTheme(<?php echo $th['id']; ?>, '<?php echo esc_js($th['name']); ?>', false)" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-400 rounded-lg text-[10px] font-semibold text-zinc-700 bg-white transition-all cursor-pointer">Edit Dashboard</button>
                            <?php if ( ! $is_read_only ) : ?>
                            <div class="relative">
                                <button onclick="toggleThemeActions(<?php echo $th['id']; ?>, event)" class="p-1.5 hover:bg-zinc-50 rounded-lg text-zinc-400 hover:text-zinc-700 cursor-pointer">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </button>
                                <div id="theme-menu-<?php echo $th['id']; ?>" class="hidden absolute right-0 bottom-full mb-1 w-36 bg-white border border-zinc-200 rounded-lg shadow-lg py-1 z-10 text-left">
                                    <button onclick="triggerActivateTheme(<?php echo $th['id']; ?>, '<?php echo esc_js($th['name']); ?>')" class="w-full px-3 py-1.5 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 font-semibold cursor-pointer">Activate Theme</button>
                                    <button onclick="triggerDuplicateTheme(<?php echo $th['id']; ?>)" class="w-full px-3 py-1.5 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Duplicate</button>
                                    <button onclick="triggerRenameTheme(<?php echo $th['id']; ?>, '<?php echo esc_js($th['name']); ?>')" class="w-full px-3 py-1.5 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Rename</button>
                                    <div class="border-t border-zinc-100 my-1"></div>
                                    <button onclick="triggerDeleteTheme(<?php echo $th['id']; ?>)" class="w-full px-3 py-1.5 text-left text-[10px] text-red-600 hover:bg-red-50 font-semibold cursor-pointer">Delete</button>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php } 
                if ( ! $has_drafts ) : ?>
                <div class="col-span-3 py-8 text-center text-xs text-zinc-400 border border-dashed border-zinc-200 rounded-xl">
                    No inactive draft themes registered yet.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- LEVEL 2 — THEME DASHBOARD -->
    <div id="canvas-level-2" class="space-y-6 hidden">
        <!-- Breadcrumb & Header Controls -->
        <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
            <div class="flex items-center gap-3">
                <button onclick="backToCanvasHub()" class="p-1.5 hover:bg-zinc-100 rounded-lg text-zinc-500 hover:text-zinc-900 cursor-pointer transition-colors">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-zinc-400">Canvas</span>
                    <span class="text-xs text-zinc-300">/</span>
                    <span id="dashboard-theme-name" class="text-sm font-bold text-zinc-900">Theme Name</span>
                    <span id="dashboard-theme-badge" class="px-1.5 py-0.5 text-[8px] font-bold uppercase rounded bg-green-50 text-green-700 border border-green-200 ml-1">Live</span>
                </div>
            </div>
            <div>
                <?php if ( ! $is_read_only ) : ?>
                <button id="activate-theme-header-btn" onclick="triggerActivateThemeFromHeader()" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold shadow-sm cursor-pointer transition-all">Activate Theme</button>
                <?php endif; ?>
                <a id="preview-site-header-btn" href="<?php echo home_url('/'); ?>" target="_blank" class="px-3 py-1.5 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 shadow-sm cursor-pointer transition-all hidden">Preview Site</a>
            </div>
        </div>

        <!-- Dashboard Workspace Tabs -->
        <div class="border-b border-zinc-200">
            <div class="flex gap-6 text-xs font-semibold">
                <button onclick="switchTab('pages')" id="tab-btn-pages" class="canvas-tab-btn pb-3 border-b-2 border-transparent text-zinc-400 hover:text-zinc-900 cursor-pointer transition-colors active">Pages</button>
                <button onclick="switchTab('menus')" id="tab-btn-menus" class="canvas-tab-btn pb-3 border-b-2 border-transparent text-zinc-400 hover:text-zinc-900 cursor-pointer transition-colors">Menus</button>
                <button onclick="switchTab('settings')" id="tab-btn-settings" class="canvas-tab-btn pb-3 border-b-2 border-transparent text-zinc-400 hover:text-zinc-900 cursor-pointer transition-colors">Theme Settings</button>
                <button onclick="switchTab('code')" id="tab-btn-code" class="canvas-tab-btn pb-3 border-b-2 border-transparent text-zinc-400 hover:text-zinc-900 cursor-pointer transition-colors">Custom Code</button>
            </div>
        </div>

        <!-- TAB CONTENT: PAGES -->
        <div id="tab-content-pages" class="space-y-4">
            <!-- Filter Bar -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-zinc-50/50 border border-zinc-200 rounded-xl p-3.5 shadow-sm">
                <div class="flex flex-wrap items-center gap-3 flex-1">
                    <div class="relative w-full md:w-56">
                        <input type="text" id="page-search-input" onkeyup="filterPages()" placeholder="Search by page name..." class="w-full px-3 py-1.5 bg-white border border-zinc-200 rounded-lg text-xs placeholder-zinc-400 focus:outline-none focus:border-zinc-400">
                    </div>
                    <select id="page-status-filter" onchange="filterPages()" class="px-2.5 py-1.5 bg-white border border-zinc-200 rounded-lg text-xs text-zinc-650 focus:outline-none focus:border-zinc-200 cursor-pointer">
                        <option value="all">All Statuses</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                    <select id="page-template-filter" onchange="filterPages()" class="px-2.5 py-1.5 bg-white border border-zinc-200 rounded-lg text-xs text-zinc-650 focus:outline-none focus:border-zinc-200 cursor-pointer">
                        <option value="all">All Templates</option>
                        <option value="agency">Agency</option>
                        <option value="brokerage">Brokerage</option>
                        <option value="minimal">Minimal</option>
                        <option value="landing-page">Landing Page</option>
                    </select>
                    <select id="page-sort-filter" onchange="filterPages()" class="px-2.5 py-1.5 bg-white border border-zinc-200 rounded-lg text-xs text-zinc-650 focus:outline-none focus:border-zinc-200 cursor-pointer">
                        <option value="modified">Last Modified</option>
                        <option value="alpha">Alphabetical</option>
                        <option value="created">Date Created</option>
                    </select>
                </div>
                <?php if ( ! $is_read_only ) : ?>
                <button onclick="openNewPageDrawer()" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold shadow-sm cursor-pointer transition-all shrink-0">+ New Page</button>
                <?php endif; ?>
            </div>

            <!-- Pages Table -->
            <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full border-collapse text-left text-xs">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-200 text-[10px] font-bold uppercase tracking-wider text-zinc-500">
                            <th class="p-4 w-10">
                                <input type="checkbox" id="pages-select-all-checkbox" onchange="toggleSelectAllPages(this)" class="rounded cursor-pointer">
                            </th>
                            <th class="p-4">Page Name</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Template</th>
                            <th class="p-4">Last Modified</th>
                            <th class="p-4 w-20 text-center">SEO</th>
                            <th class="p-4 w-32 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pages-table-body">
                        <!-- Pages rows populated dynamically by Javascript -->
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions Bar -->
            <div id="pages-bulk-actions-bar" class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 bg-zinc-950 border border-zinc-800 rounded-xl shadow-2xl py-3 px-5 items-center gap-4 hidden transition-all duration-300">
                <span id="bulk-selected-count" class="text-xs font-semibold text-white">0 selected</span>
                <div class="h-4 w-[1px] bg-zinc-800"></div>
                <select id="pages-bulk-action-select" class="px-2 py-1 bg-zinc-900 border border-zinc-800 rounded-md text-[10px] text-zinc-300 font-semibold focus:outline-none cursor-pointer">
                    <option value="publish">Publish</option>
                    <option value="unpublish">Unpublish</option>
                    <option value="delete">Delete Permanently</option>
                </select>
                <button onclick="applyBulkPagesAction()" class="px-3 py-1 bg-white hover:bg-zinc-100 text-zinc-900 font-bold rounded-lg text-[10px] cursor-pointer transition-colors shadow-sm">Apply</button>
                <button onclick="deselectAllPages()" class="text-[10px] font-semibold text-zinc-400 hover:text-white transition-colors cursor-pointer">Deselect All</button>
            </div>
        </div>

        <!-- TAB CONTENT: MENUS -->
        <div id="tab-content-menus" class="grid grid-cols-1 lg:grid-cols-10 gap-6 hidden">
            <!-- Left: Menus List -->
            <div class="lg:col-span-3 bg-white border border-zinc-200 rounded-xl p-5 shadow-sm space-y-4 flex flex-col justify-between min-h-[400px]">
                <div class="space-y-3">
                    <h3 class="text-xs font-bold text-zinc-800 tracking-tight uppercase">Navigation Menus</h3>
                    <div id="menus-list-container" class="space-y-1">
                        <!-- Populated by JS -->
                    </div>
                </div>
                <?php if ( ! $is_read_only ) : ?>
                <button onclick="triggerCreateNewMenu()" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 hover:bg-zinc-50 shadow-sm cursor-pointer transition-all">+ New Menu</button>
                <?php endif; ?>
            </div>

            <!-- Right: Menu Editor -->
            <div class="lg:col-span-7 bg-white border border-zinc-200 rounded-xl p-6 shadow-sm flex flex-col justify-between min-h-[400px]">
                <div class="space-y-5">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <div>
                            <h3 id="menu-editor-title" class="text-sm font-bold text-zinc-900">Header Main Menu</h3>
                            <p class="text-[10px] text-zinc-500 mt-0.5">Drag to reorder, shift right to nest sub-items.</p>
                        </div>
                        <?php if ( ! $is_read_only ) : ?>
                        <div class="flex items-center gap-2">
                            <div class="relative inline-block text-left">
                                <button onclick="toggleAddMenuItemDropdown(event)" class="px-2.5 py-1.5 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 cursor-pointer shadow-sm flex items-center gap-1">+ Add Item</button>
                                <div id="add-menu-item-dropdown" class="hidden absolute right-0 mt-1 w-48 bg-white border border-zinc-200 rounded-lg shadow-lg py-1 z-10 text-left">
                                    <h4 class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Select Page Link</h4>
                                    <div id="dropdown-pages-list" class="max-h-32 overflow-y-auto">
                                        <!-- List of pages -->
                                    </div>
                                    <div class="border-t border-zinc-100 my-1"></div>
                                    <button onclick="triggerAddCustomLink()" class="w-full px-3 py-1.5 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Custom Link URL...</button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Draggable menu items container -->
                    <div id="menu-items-editor-container" class="space-y-1.5 max-h-[360px] overflow-y-auto pr-1">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <div class="flex items-center justify-end border-t border-zinc-100 pt-4 mt-6">
                    <button onclick="saveCurrentMenu()" <?php echo $is_read_only ? 'disabled' : ''; ?> class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-50 text-white rounded-lg text-xs font-semibold shadow-sm cursor-pointer transition-all">Save Menu</button>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: THEME SETTINGS -->
        <div id="tab-content-settings" class="bg-white border border-zinc-200 rounded-xl p-6 shadow-sm hidden space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Identity Section -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider border-b border-zinc-100 pb-2">Identity</h4>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Site Title</label>
                            <input type="text" id="setting-site-title" class="w-full px-3 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Site Tagline</label>
                            <input type="text" id="setting-site-tagline" class="w-full px-3 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Favicon Icon URL</label>
                                <input type="text" id="setting-site-favicon" placeholder="e.g. icon.png" class="w-full px-3 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Agency Logo URL</label>
                                <input type="text" id="setting-site-logo" placeholder="e.g. logo.png" class="w-full px-3 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                            </div>
                        </div>
                    </div>

                    <!-- Typography Section -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider border-b border-zinc-100 pb-2">Typography</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Heading Font</label>
                                <select id="setting-heading-font" class="w-full px-2.5 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                    <option value="Inter">Inter</option>
                                    <option value="Playfair Display">Playfair Display</option>
                                    <option value="Outfit">Outfit</option>
                                    <option value="Roboto">Roboto</option>
                                    <option value="Lora">Lora</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Body Font</label>
                                <select id="setting-body-font" class="w-full px-2.5 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                    <option value="Inter">Inter</option>
                                    <option value="Roboto">Roboto</option>
                                    <option value="Lora">Lora</option>
                                    <option value="Outfit">Outfit</option>
                                </select>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-[10px] font-bold text-zinc-500 uppercase">
                                <span>Base Font Size</span>
                                <span id="font-size-val">16px</span>
                            </div>
                            <input type="range" id="setting-font-size" min="14" max="18" value="16" oninput="$('#font-size-val').text(this.value + 'px')" class="w-full accent-zinc-950 cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Colors Section -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider border-b border-zinc-100 pb-2">Theme Colors</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Primary Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="setting-color-primary" class="w-8 h-8 rounded border border-zinc-200 cursor-pointer p-0">
                                    <input type="text" id="setting-color-primary-text" class="flex-1 px-3 py-1.5 border border-zinc-200 rounded-lg text-xs uppercase" oninput="$('#setting-color-primary').val(this.value)">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Secondary Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="setting-color-secondary" class="w-8 h-8 rounded border border-zinc-200 cursor-pointer p-0">
                                    <input type="text" id="setting-color-secondary-text" class="flex-1 px-3 py-1.5 border border-zinc-200 rounded-lg text-xs uppercase" oninput="$('#setting-color-secondary').val(this.value)">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Accent Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="setting-color-accent" class="w-8 h-8 rounded border border-zinc-200 cursor-pointer p-0">
                                    <input type="text" id="setting-color-accent-text" class="flex-1 px-3 py-1.5 border border-zinc-200 rounded-lg text-xs uppercase" oninput="$('#setting-color-accent').val(this.value)">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Text Default</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="setting-color-text" class="w-8 h-8 rounded border border-zinc-200 cursor-pointer p-0">
                                    <input type="text" id="setting-color-text-text" class="flex-1 px-3 py-1.5 border border-zinc-200 rounded-lg text-xs uppercase" oninput="$('#setting-color-text').val(this.value)">
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Background Color</label>
                            <div class="flex items-center gap-2 w-1/2">
                                <input type="color" id="setting-color-bg" class="w-8 h-8 rounded border border-zinc-200 cursor-pointer p-0">
                                <input type="text" id="setting-color-bg-text" class="flex-1 px-3 py-1.5 border border-zinc-200 rounded-lg text-xs uppercase" oninput="$('#setting-color-bg').val(this.value)">
                            </div>
                        </div>
                    </div>

                    <!-- Header/Footer Layout Options -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider border-b border-zinc-100 pb-2">Layout Config</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Header Style</label>
                                <select id="setting-header-layout" class="w-full px-2.5 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                    <option value="Logo Left">Logo Left</option>
                                    <option value="Centered Logo">Centered Logo</option>
                                    <option value="Split Navigation">Split Navigation</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Footer Columns</label>
                                <select id="setting-footer-columns" class="w-full px-2.5 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                    <option value="1">1 Column</option>
                                    <option value="2">2 Columns</option>
                                    <option value="3">3 Columns</option>
                                    <option value="4">4 Columns</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="setting-sticky-header" class="rounded cursor-pointer">
                                <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="setting-sticky-header">Sticky Header</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="setting-show-socials" class="rounded cursor-pointer">
                                <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="setting-show-socials">Show Social Links</label>
                            </div>
                        </div>
                        <div class="space-y-2 pt-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Copyright Text</label>
                            <input type="text" id="setting-copyright-text" class="w-full px-3 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end border-t border-zinc-100 pt-4 mt-4">
                <button onclick="saveThemeSettings()" <?php echo $is_read_only ? 'disabled' : ''; ?> class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-50 text-white rounded-lg text-xs font-semibold shadow-sm cursor-pointer transition-all">Save Settings</button>
            </div>
        </div>

        <!-- TAB CONTENT: CUSTOM CODE -->
        <div id="tab-content-code" class="grid grid-cols-1 lg:grid-cols-2 gap-6 hidden">
            <!-- Custom CSS Editor -->
            <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm flex flex-col justify-between min-h-[400px]">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-800 uppercase tracking-tight">Custom CSS Overrides</h3>
                        <p class="text-[10px] text-zinc-500 mt-0.5">Style declarations injected inside standard `&lt;style&gt;` block in header.</p>
                    </div>
                    <div class="border border-zinc-200 rounded-lg overflow-hidden">
                        <textarea id="custom-css-textarea" class="w-full h-64 p-3 font-mono text-xs focus:outline-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-between border-t border-zinc-100 pt-4 mt-6">
                    <span class="text-[10px] text-zinc-400 font-mono" id="css-char-count">0 characters</span>
                    <button onclick="saveCustomCSS()" <?php echo $is_read_only ? 'disabled' : ''; ?> class="px-3.5 py-1.5 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-50 text-white rounded-lg text-xs font-semibold shadow-sm cursor-pointer transition-all">Save CSS</button>
                </div>
            </div>

            <!-- Custom JS Editor -->
            <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm flex flex-col justify-between min-h-[400px]">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-xs font-bold text-zinc-800 uppercase tracking-tight">Custom JavaScript Injection</h3>
                        <p class="text-[10px] text-zinc-500 mt-0.5">Custom script commands loaded on all frontend routes.</p>
                    </div>
                    <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 text-[10px] text-amber-800 leading-relaxed">
                        ⚠️ **JavaScript errors here can break your pages.** Test script operations thoroughly before updating.
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase whitespace-nowrap">Injection Point</label>
                        <select id="setting-js-position" class="px-2.5 py-1 bg-white border border-zinc-200 rounded-lg text-[10px] text-zinc-650 focus:outline-none cursor-pointer">
                            <option value="head">Inside Page Head</option>
                            <option value="footer">Before Body Close</option>
                        </select>
                    </div>
                    <div class="border border-zinc-200 rounded-lg overflow-hidden">
                        <textarea id="custom-js-textarea" class="w-full h-48 p-3 font-mono text-xs focus:outline-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end border-t border-zinc-100 pt-4 mt-6">
                    <button onclick="saveCustomJS()" <?php echo $is_read_only ? 'disabled' : ''; ?> class="px-3.5 py-1.5 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-50 text-white rounded-lg text-xs font-semibold shadow-sm cursor-pointer transition-all">Save JS</button>
                </div>
            </div>
        </div>
    </div>

    <!-- LEVEL 3 — ELEMENTOR PAGE EDITOR iframe wrapper -->
    <div id="canvas-level-3" class="fixed inset-0 z-[9999] bg-white hidden flex flex-col">
        <!-- Editor Topbar Wrapper -->
        <div id="cora-parent-editor-topbar" class="h-12 bg-zinc-900 border-b border-zinc-850 flex items-center justify-between px-4 text-white hidden">
            <div class="flex items-center gap-4">
                <button onclick="closeElementorEditor()" class="px-2.5 py-1 border border-zinc-700 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-lg text-[10px] font-bold cursor-pointer transition-all flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Theme Dashboard
                </button>
                <div class="h-4 w-[1px] bg-zinc-850"></div>
                <div class="flex items-center gap-2 text-[10px] font-semibold text-zinc-400">
                    <span>Canvas</span>
                    <span>/</span>
                    <span id="editor-theme-title" class="text-zinc-300">Theme</span>
                    <span>/</span>
                    <span id="editor-page-title" class="text-white font-bold">Page Title</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a id="editor-preview-link" href="#" target="_blank" class="px-2.5 py-1 border border-zinc-700 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-lg text-[10px] font-bold cursor-pointer transition-all">Preview</a>
                <button onclick="triggerElementorAction('save')" class="px-2.5 py-1 border border-zinc-700 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-lg text-[10px] font-bold cursor-pointer transition-all">Save Draft</button>
                <button onclick="triggerElementorAction('publish')" class="px-3 py-1 bg-zinc-100 hover:bg-white text-zinc-900 rounded-lg text-[10px] font-bold cursor-pointer transition-all">Publish</button>
            </div>
        </div>

        <!-- iframe container -->
        <div id="elementor-iframe-container" class="flex-1 w-full bg-zinc-50 relative flex items-center justify-center">
            <!-- Loading Indicator -->
            <div id="iframe-loader" class="absolute inset-0 flex flex-col items-center justify-center bg-white z-10 gap-3 text-xs text-zinc-500 font-semibold">
                <div class="animate-spin rounded-full h-6 w-6 border-2 border-zinc-900 border-t-transparent"></div>
                <span>Spawning Elementor design workspace...</span>
            </div>

            <!-- Error/Blocking Display -->
            <div id="elementor-blocking-msg" class="max-w-md p-6 bg-white border border-zinc-200 rounded-xl shadow-lg text-center space-y-4 hidden z-20">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <h4 class="text-sm font-bold text-zinc-900">Elementor Integration Required</h4>
                <p class="text-xs text-zinc-500 leading-relaxed">Elementor is required for page editing. Please install and activate Elementor Page Builder plugin to edit canvas pages.</p>
                <a href="<?php echo admin_url('plugins.php'); ?>" target="_blank" class="inline-block px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold transition-all">Install Elementor →</a>
            </div>

            <!-- Editor Iframe -->
            <iframe id="elementor-editor-iframe" src="" class="w-full h-full border-none hidden"></iframe>
        </div>
    </div>

</div>

<!-- ═══ MODAL SLIDING DRAWERS ════════════════════════════════════════════════════ -->

<!-- 0. Import Kit / Theme Drawer -->
<div id="drawer-import-kit" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-import-kit-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <h3 class="text-sm font-bold text-zinc-950">Import Template Kit / Theme</h3>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeImportKitDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Theme Workspace Name *</label>
                <input type="text" id="import-kit-name-input" placeholder="e.g. Westside Luxury Catalog" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
            
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Upload ZIP Kit / Theme *</label>
                <div class="border-2 border-dashed border-zinc-200 rounded-xl p-8 text-center bg-zinc-50 hover:bg-zinc-100/50 transition-colors relative cursor-pointer group animate-fade-in" onclick="document.getElementById('import-kit-file-input').click()">
                    <input type="file" id="import-kit-file-input" accept=".zip" class="hidden">
                    <svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 mx-auto mb-3 group-hover:text-zinc-600 transition-colors"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <div class="text-xs font-semibold text-zinc-900" id="import-file-name-display">Click to select or drag & drop ZIP here</div>
                    <p class="text-[9px] text-zinc-400 mt-1">Accepts Elementor Template Kits (.zip) or Elementor-compatible themes</p>
                </div>
            </div>
        </div>
        <div class="p-5 border-t border-zinc-100 flex items-center justify-end gap-3 bg-zinc-50/50">
            <button type="button" class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeImportKitDrawer()">Cancel</button>
            <button type="button" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="triggerImportKit()">Import Kit</button>
        </div>
    </div>
</div>

<!-- 1. New Theme Setup Drawer -->
<div id="drawer-new-theme" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-new-theme-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <h3 class="text-sm font-bold text-zinc-950">Initialize New Theme</h3>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeNewThemeDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Theme Name *</label>
                <input type="text" id="new-theme-name-input" placeholder="e.g. Westside Luxury Catalog" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
            
            <div class="space-y-3">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Start From Structure</label>
                <div class="grid grid-cols-1 gap-3">
                    <label class="border border-zinc-200 rounded-xl p-3.5 flex items-start gap-3 cursor-pointer hover:bg-zinc-50 transition-colors">
                        <input type="radio" name="new-theme-source" value="blank" checked class="mt-1 accent-zinc-950">
                        <div>
                            <div class="text-xs font-bold text-zinc-900">Blank Layout</div>
                            <p class="text-[10px] text-zinc-500 mt-0.5">Empty theme workspace. Create pages as required.</p>
                        </div>
                    </label>
                    <label class="border border-zinc-200 rounded-xl p-3.5 flex items-start gap-3 cursor-pointer hover:bg-zinc-50 transition-colors">
                        <input type="radio" name="new-theme-source" value="duplicate" class="mt-1 accent-zinc-950">
                        <div>
                            <div class="text-xs font-bold text-zinc-900">Duplicate Active</div>
                            <p class="text-[10px] text-zinc-500 mt-0.5">Copies the current live theme settings and page lists as a draft.</p>
                        </div>
                    </label>
                    <label class="border border-zinc-200 rounded-xl p-3.5 flex items-start gap-3 cursor-pointer hover:bg-zinc-50 transition-colors">
                        <input type="radio" name="new-theme-source" value="template" class="mt-1 accent-zinc-950">
                        <div>
                            <div class="text-xs font-bold text-zinc-900">From Template</div>
                            <p class="text-[10px] text-zinc-500 mt-0.5">Incorporate Cora agency themes (Real Estate Minimal / Premium Villa Brokerage).</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="p-5 border-t border-zinc-100 flex items-center justify-end gap-3 bg-zinc-50/50">
            <button type="button" class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeNewThemeDrawer()">Cancel</button>
            <button type="button" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="saveNewTheme()">Create Theme</button>
        </div>
    </div>
</div>

<!-- 2. New Page Setup Drawer -->
<div id="drawer-new-page" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-new-page-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <h3 class="text-sm font-bold text-zinc-950">Add Page to Theme</h3>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeNewPageDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Page Title *</label>
                <input type="text" id="new-page-title-input" onkeyup="autoGenerateSlug(this)" placeholder="e.g. Featured Penthouse Listings" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">URL Slug Path *</label>
                <input type="text" id="new-page-slug-input" placeholder="e.g. penthouse-listings" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase">Layout Template</label>
                    <select id="new-page-template-input" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                        <option value="agency">Agency</option>
                        <option value="brokerage">Brokerage</option>
                        <option value="minimal">Minimal</option>
                        <option value="landing-page">Landing Page</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase">Status</label>
                    <select id="new-page-status-input" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="p-5 border-t border-zinc-100 flex items-center justify-end gap-3 bg-zinc-50/50">
            <button type="button" class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeNewPageDrawer()">Cancel</button>
            <button type="button" class="px-4 py-2 border border-zinc-200 hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="saveNewPage(false)">Create Only</button>
            <button type="button" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="saveNewPage(true)">Create & Edit</button>
        </div>
    </div>
</div>

<!-- 3. Global SEO settings Side Drawer -->
<div id="drawer-page-seo" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-page-seo-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">SEO & Metadata Settings</h3>
                <p id="seo-drawer-page-title" class="text-[10px] text-zinc-500 mt-0.5">Page Title</p>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeSEODrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <input type="hidden" id="seo-page-id-input">
            
            <div class="space-y-2">
                <div class="flex justify-between items-center text-[10px] font-bold text-zinc-500 uppercase">
                    <span>Meta Title</span>
                    <span id="seo-title-char-count">0/60</span>
                </div>
                <input type="text" id="seo-title-input" onkeyup="countChars(this, 'seo-title-char-count', 60)" placeholder="e.g. Luxury Villas in Gurgaon | PropOS Agency" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                <p id="seo-title-warn" class="text-[9px] text-amber-600 hidden font-semibold">⚠️ Recommended length exceeded (60 chars max).</p>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between items-center text-[10px] font-bold text-zinc-500 uppercase">
                    <span>Meta Description</span>
                    <span id="seo-desc-char-count">0/160</span>
                </div>
                <textarea id="seo-desc-input" onkeyup="countChars(this, 'seo-desc-char-count', 160)" rows="4" placeholder="Brief search snippet description..." class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400"></textarea>
                <p id="seo-desc-warn" class="text-[9px] text-amber-600 hidden font-semibold">⚠️ Recommended length exceeded (160 chars max).</p>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">OG Social Preview Image URL</label>
                <input type="text" id="seo-og-image-input" placeholder="e.g. https://cora.local/og-preview.png" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                <p class="text-[9px] text-zinc-400">Recommended dimension: 1200×630px.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="seo-index-input" checked class="rounded cursor-pointer">
                    <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="seo-index-input">Index Page (Search engines)</label>
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Canonical URL Override</label>
                <input type="text" id="seo-canonical-input" placeholder="https://..." class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
        </div>
        <div class="p-5 border-t border-zinc-100 flex items-center justify-end gap-3 bg-zinc-50/50">
            <button type="button" class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeSEODrawer()">Cancel</button>
            <button type="button" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="savePageSEOSettings()">Save SEO</button>
        </div>
    </div>
</div>

<!-- 4. Page Revision History Side Panel -->
<div id="drawer-page-revisions" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-page-revisions-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">Page Revision History</h3>
                <p id="revisions-drawer-page-title" class="text-[10px] text-zinc-500 mt-0.5">Page Title</p>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeRevisionsDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-4" id="revisions-list-container">
            <!-- Populated dynamically by JS -->
        </div>
        <div class="p-5 border-t border-zinc-100 flex items-center justify-end bg-zinc-50/50">
            <button type="button" class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeRevisionsDrawer()">Close Panel</button>
        </div>
    </div>
</div>

<!-- 5. Activate theme confirmation modal -->
<div id="canvas-confirm-modal" class="fixed inset-0 z-[999999] bg-zinc-900/40 backdrop-blur-[1px] flex items-center justify-center hidden">
    <div class="bg-white border border-zinc-200 rounded-xl shadow-2xl p-6 w-full max-w-sm space-y-5">
        <div class="space-y-2">
            <h4 id="canvas-confirm-title" class="text-sm font-bold text-zinc-950">Confirm Action</h4>
            <p id="canvas-confirm-message" class="text-xs text-zinc-500 leading-relaxed">Are you sure you want to perform this action?</p>
        </div>
        <div class="flex items-center justify-end gap-2.5">
            <button onclick="closeCanvasConfirmModal()" class="px-3.5 py-2 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-750 bg-white hover:bg-zinc-50 transition-colors cursor-pointer">Cancel</button>
            <button id="canvas-confirm-action-btn" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors cursor-pointer">Confirm</button>
        </div>
    </div>
</div>

<!-- 6. Theme Rename Modal/Drawer -->
<div id="drawer-rename-theme" class="fixed inset-0 z-[99999] bg-zinc-900/40 backdrop-filter blur-[2px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="drawer-rename-theme-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <h3 class="text-sm font-bold text-zinc-950">Rename Theme Workspace</h3>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeRenameThemeDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            <input type="hidden" id="rename-theme-id-input">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Theme Name *</label>
                <input type="text" id="rename-theme-name-input" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
        </div>
        <div class="p-5 border-t border-zinc-100 flex items-center justify-end gap-3 bg-zinc-50/50">
            <button type="button" class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeRenameThemeDrawer()">Cancel</button>
            <button type="button" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="saveRenamedTheme()">Rename Theme</button>
        </div>
    </div>
</div>

<script>
    // --- Canvas Global State ---
    const canvasState = {
        level: 1,
        activeThemeId: <?php echo $live_theme ? $live_theme['id'] : 0; ?>,
        activeThemeName: '<?php echo $live_theme ? esc_js($live_theme['name']) : ''; ?>',
        activeThemeIsLive: <?php echo $live_theme && $live_theme['status'] === 'live' ? 'true' : 'false'; ?>,
        activePageId: null,
        activeTab: 'pages',
        isReadOnly: <?php echo $is_read_only ? 'true' : 'false'; ?>,
        themes: <?php echo json_encode($themes); ?>,
        pages: [],
        menus: [
            { id: 'menu_1', name: 'Header Main Menu', items: [
                { id: 'mi_1', label: 'Home', url: '/home', newTab: false, level: 0 },
                { id: 'mi_2', label: 'Listings', url: '/listings', newTab: false, level: 0 },
                { id: 'mi_3', label: 'About Us', url: '/about', newTab: false, level: 0 }
            ]},
            { id: 'menu_2', name: 'Footer Links', items: [
                { id: 'mi_4', label: 'Privacy Policy', url: '/privacy', newTab: false, level: 0 },
                { id: 'mi_5', label: 'Support Desk', url: '/support', newTab: true, level: 0 }
            ]}
        ],
        activeMenuId: 'menu_1',
        cssEditor: null,
        jsEditor: null
    };

    jQuery(document).ready(function($) {
        // Init editors on DOM load
        initCodeEditors();
        
        // Hide standard menu click list if click outside
        $(document).on('click', function() {
            $('[id^="theme-menu-"]').addClass('hidden');
            $('#add-menu-item-dropdown').addClass('hidden');
        });
    });

    // Code Editors Initialization
    function initCodeEditors() {
        if (typeof CodeMirror !== 'undefined') {
            canvasState.cssEditor = CodeMirror.fromTextArea(document.getElementById('custom-css-textarea'), {
                mode: 'css',
                lineNumbers: true,
                theme: 'neat'
            });
            canvasState.cssEditor.on('change', function(cm) {
                const len = cm.getValue().length;
                jQuery('#css-char-count').text(len + ' characters');
            });
            canvasState.cssEditor.setValue(jQuery('#custom-css-textarea').val());

            canvasState.jsEditor = CodeMirror.fromTextArea(document.getElementById('custom-js-textarea'), {
                mode: 'javascript',
                lineNumbers: true,
                theme: 'neat'
            });
            canvasState.jsEditor.setValue(jQuery('#custom-js-textarea').val());
        }
    }

    // --- Level 1 Theme Functions ---
    function openNewThemeDrawer() {
        if (canvasState.isReadOnly) return;
        jQuery('#drawer-new-theme').removeClass('opacity-0 pointer-events-none').css({'opacity': '1', 'pointer-events': 'auto'});
        jQuery('#drawer-new-theme-card').removeClass('translate-x-full').addClass('translate-x-0');
    }
    function closeNewThemeDrawer() {
        jQuery('#drawer-new-theme-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            jQuery('#drawer-new-theme').addClass('opacity-0 pointer-events-none').css({'opacity': '0', 'pointer-events': 'none'});
        }, 300);
    }

    function openImportKitDrawer() {
        if (canvasState.isReadOnly) return;
        jQuery('#drawer-import-kit').removeClass('opacity-0 pointer-events-none').css({'opacity': '1', 'pointer-events': 'auto'});
        jQuery('#drawer-import-kit-card').removeClass('translate-x-full').addClass('translate-x-0');
        // Reset file upload state
        jQuery('#import-kit-name-input').val('');
        jQuery('#import-kit-file-input').val('');
        jQuery('#import-file-name-display').text('Click to select or drag & drop ZIP here');
    }
    function closeImportKitDrawer() {
        jQuery('#drawer-import-kit-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            jQuery('#drawer-import-kit').addClass('opacity-0 pointer-events-none').css({'opacity': '0', 'pointer-events': 'none'});
        }, 300);
    }
    function triggerImportKit() {
        const name = jQuery('#import-kit-name-input').val().trim();
        const fileInput = document.getElementById('import-kit-file-input');
        if (!name) {
            window.coraShowToast('Please specify a theme name.');
            return;
        }
        if (fileInput.files.length === 0) {
            window.coraShowToast('Please select a ZIP template kit file.');
            return;
        }
        const file = fileInput.files[0];
        
        window.coraShowToast('Importing and validating Elementor template kit...');
        
        const formData = new FormData();
        formData.append('action', 'cora_ajax_import_kit');
        formData.append('theme_name', name);
        formData.append('kit_zip', file);
        formData.append('nonce', coraREData.ajaxNonce);
        
        jQuery.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Elementor Kit imported successfully!');
                    closeImportKitDrawer();
                    setTimeout(function() { window.location.reload(); }, 800);
                } else {
                    window.coraShowToast(res.data || 'Failed to import kit.');
                }
            },
            error: function() {
                window.coraShowToast('Failed to upload kit file.');
            }
        });
    }

    jQuery(document).ready(function() {
        jQuery('#import-kit-file-input').on('change', function() {
            const files = this.files;
            if (files.length > 0) {
                jQuery('#import-file-name-display').text(files[0].name);
            } else {
                jQuery('#import-file-name-display').text('Click to select or drag & drop ZIP here');
            }
        });
    });

    function toggleThemeActions(id, e) {
        e.preventDefault();
        e.stopPropagation();
        const menu = jQuery('#theme-menu-' + id);
        jQuery('[id^="theme-menu-"]').not(menu).addClass('hidden');
        menu.toggleClass('hidden');
    }

    function saveNewTheme() {
        const name = jQuery('#new-theme-name-input').val().trim();
        const startFrom = jQuery('input[name="new-theme-source"]:checked').val();
        if (!name) {
            window.coraShowToast('Please specify a theme name.');
            return;
        }
        window.coraShowToast('Initializing theme workspace...');
        
        // REST API call or Ajax fallback to insert database row
        jQuery.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'cora_ajax_create_theme',
                name: name,
                start_from: startFrom,
                nonce: coraREData.ajaxNonce
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Theme initialized successfully.');
                    closeNewThemeDrawer();
                    setTimeout(function() { window.location.reload(); }, 800);
                } else {
                    window.coraShowToast(res.data.message || 'Failed to create theme.');
                }
            }
        });
    }

    // Activate theme confirmation modal
    function triggerActivateTheme(id, name) {
        if (canvasState.isReadOnly) return;
        jQuery('#canvas-confirm-title').text('Confirm Activation');
        jQuery('#canvas-confirm-message').text(`Activating "${name}" will replace your current live theme. Your current live theme will move to Drafts. Continue?`);
        
        jQuery('#canvas-confirm-action-btn').off('click').on('click', function() {
            window.coraShowToast('Activating theme...');
            jQuery.post(coraREData.ajaxUrl, {
                action: 'cora_ajax_activate_theme',
                theme_id: id,
                nonce: coraREData.ajaxNonce
            }, function(res) {
                if (res.success) {
                    window.coraShowToast('Theme activated successfully.');
                    closeCanvasConfirmModal();
                    setTimeout(function() { window.location.reload(); }, 800);
                } else {
                    window.coraShowToast(res.data.message || 'Failed to activate theme.');
                }
            });
        });
        jQuery('#canvas-confirm-modal').removeClass('hidden');
    }

    function closeCanvasConfirmModal() {
        jQuery('#canvas-confirm-modal').addClass('hidden');
    }

    function triggerActivateThemeFromHeader() {
        triggerActivateTheme(canvasState.activeThemeId, canvasState.activeThemeName);
    }

    function triggerRenameTheme(id, name) {
        jQuery('#rename-theme-id-input').val(id);
        jQuery('#rename-theme-name-input').val(name);
        jQuery('#drawer-rename-theme').removeClass('opacity-0 pointer-events-none').css({'opacity': '1', 'pointer-events': 'auto'});
        jQuery('#drawer-rename-theme-card').removeClass('translate-x-full').addClass('translate-x-0');
    }
    function closeRenameThemeDrawer() {
        jQuery('#drawer-rename-theme-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            jQuery('#drawer-rename-theme').addClass('opacity-0 pointer-events-none').css({'opacity': '0', 'pointer-events': 'none'});
        }, 300);
    }
    function saveRenamedTheme() {
        const id = jQuery('#rename-theme-id-input').val();
        const name = jQuery('#rename-theme-name-input').val().trim();
        if (!name) {
            window.coraShowToast('Name cannot be empty.');
            return;
        }
        window.coraShowToast('Updating name...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_rename_theme',
            theme_id: id,
            name: name,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Theme renamed successfully.');
                closeRenameThemeDrawer();
                setTimeout(function() { window.location.reload(); }, 800);
            } else {
                window.coraShowToast('Failed to rename theme.');
            }
        });
    }

    function triggerDeleteTheme(id) {
        window.coraConfirmAction(
            'Delete Theme',
            'Are you sure you want to delete this theme workspace permanently? All containing pages will be removed.',
            function() {
                window.coraShowToast('Removing theme...');
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_ajax_delete_theme',
                    theme_id: id,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res.success) {
                        window.coraShowToast('Theme deleted successfully.');
                        setTimeout(function() { window.location.reload(); }, 800);
                    } else {
                        window.coraShowToast('Failed to delete theme.');
                    }
                });
            }
        );
    }

    function triggerDuplicateTheme(id) {
        window.coraShowToast('Duplicating theme...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_duplicate_theme',
            theme_id: id,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Theme duplicated successfully.');
                setTimeout(function() { window.location.reload(); }, 800);
            } else {
                window.coraShowToast('Failed to duplicate.');
            }
        });
    }

    // --- LEVEL 2 Dashboard Navigation & Actions ---
    function editTheme(id, name, isLive) {
        canvasState.level = 2;
        canvasState.activeThemeId = id;
        canvasState.activeThemeName = name;
        canvasState.activeThemeIsLive = isLive;

        jQuery('#canvas-level-1').addClass('hidden');
        jQuery('#canvas-level-2').removeClass('hidden');

        jQuery('#dashboard-theme-name').text(name);
        if (isLive) {
            jQuery('#dashboard-theme-badge').removeClass('bg-zinc-50 text-zinc-500 border-zinc-200').addClass('bg-green-50 text-green-700 border-green-200').text('Live');
            jQuery('#activate-theme-header-btn').addClass('hidden');
            jQuery('#preview-site-header-btn').removeClass('hidden');
        } else {
            jQuery('#dashboard-theme-badge').removeClass('bg-green-50 text-green-700 border-green-200').addClass('bg-zinc-50 text-zinc-500 border-zinc-200').text('Draft');
            jQuery('#activate-theme-header-btn').removeClass('hidden');
            jQuery('#preview-site-header-btn').addClass('hidden');
        }

        // Fetch theme settings and load pages
        fetchThemePages(id);
        fetchThemeSettings(id);
        
        switchTab('pages');
    }

    function backToCanvasHub() {
        canvasState.level = 1;
        jQuery('#canvas-level-2').addClass('hidden');
        jQuery('#canvas-level-1').removeClass('hidden');
    }

    function switchTab(tabId) {
        canvasState.activeTab = tabId;
        jQuery('.canvas-tab-btn').removeClass('active');
        jQuery('#tab-btn-' + tabId).addClass('active');

        jQuery('#tab-content-pages').addClass('hidden');
        jQuery('#tab-content-menus').addClass('hidden');
        jQuery('#tab-content-settings').addClass('hidden');
        jQuery('#tab-content-code').addClass('hidden');

        jQuery('#tab-content-' + tabId).removeClass('hidden');

        if (tabId === 'menus') {
            renderMenusList();
            renderMenuEditor();
        }
    }

    // --- Tab 1 Pages Functions ---
    function fetchThemePages(themeId) {
        jQuery.ajax({
            url: coraREData.ajaxUrl,
            method: 'GET',
            data: {
                action: 'cora_ajax_get_theme_pages',
                theme_id: themeId,
                nonce: coraREData.ajaxNonce
            },
            success: function(res) {
                if (res.success) {
                    canvasState.pages = res.data || [];
                    filterPages();
                }
            }
        });
    }

    function filterPages() {
        const query = jQuery('#page-search-input').val().toLowerCase();
        const status = jQuery('#page-status-filter').val();
        const template = jQuery('#page-template-filter').val();
        const sort = jQuery('#page-sort-filter').val();

        let filtered = [...canvasState.pages];

        if (query) {
            filtered = filtered.filter(p => p.title.toLowerCase().includes(query) || p.slug.toLowerCase().includes(query));
        }
        if (status !== 'all') {
            filtered = filtered.filter(p => p.status === status);
        }
        if (template !== 'all') {
            filtered = filtered.filter(p => p.template === template);
        }

        if (sort === 'alpha') {
            filtered.sort((a, b) => a.title.localeCompare(b.title));
        } else if (sort === 'created') {
            filtered.sort((a, b) => b.id - a.id);
        } else {
            // modified
            filtered.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
        }

        renderPagesTable(filtered);
    }

    function renderPagesTable(pages) {
        const body = jQuery('#pages-table-body');
        body.empty();

        if (pages.length === 0) {
            body.append(`
                <tr>
                    <td colspan="7" class="p-8 text-center text-zinc-400">No pages found matching these criteria.</td>
                </tr>
            `);
            return;
        }

        pages.forEach(p => {
            const statusPill = getStatusPill(p.status, p.scheduled_at);
            const seoIcon = getSEOIcon(p.seo_title, p.seo_description);
            const homeBadge = p.is_homepage == 1 ? '<span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-zinc-100 border border-zinc-200 text-zinc-700 ml-1.5 flex items-center gap-1">🏠 Home</span>' : '';

            body.append(`
                <tr class="border-b border-zinc-100 hover:bg-zinc-50/50">
                    <td class="p-4">
                        <input type="checkbox" class="page-row-checkbox rounded cursor-pointer" data-id="${p.id}" onchange="updateBulkActionState()">
                    </td>
                    <td class="p-4">
                        <div class="flex items-center">
                            <div>
                                <button onclick="openPageEditor(${p.id}, '${esc_js(p.title)}', ${p.wp_post_id})" class="font-semibold text-zinc-900 hover:underline text-left cursor-pointer">${esc_html(p.title)}</button>
                                <div class="text-[10px] text-zinc-400 font-mono mt-0.5">/${esc_html(p.slug)}</div>
                            </div>
                            ${homeBadge}
                        </div>
                    </td>
                    <td class="p-4">${statusPill}</td>
                    <td class="p-4 text-zinc-500 uppercase font-mono text-[9px]">${esc_html(p.template)}</td>
                    <td class="p-4 text-zinc-500">${getRelativeTime(p.updated_at)}</td>
                    <td class="p-4 text-center cursor-pointer" onclick="openSEODrawer(${p.id}, '${esc_js(p.title)}', '${esc_js(p.seo_title)}', '${esc_js(p.seo_description)}', '${esc_js(p.seo_og_image)}')">${seoIcon}</td>
                    <td class="p-4 text-right space-x-1.5">
                        <button onclick="openPageEditor(${p.id}, '${esc_js(p.title)}', ${p.wp_post_id})" class="px-2 py-1 border border-zinc-200 hover:border-zinc-400 rounded-lg font-semibold text-[10px] text-zinc-700 bg-white transition-all cursor-pointer">Edit</button>
                        <a href="${coraREData.siteUrl}/${p.slug}" target="_blank" class="px-2 py-1 border border-zinc-200 hover:border-zinc-400 rounded-lg font-semibold text-[10px] text-zinc-700 bg-white transition-all">Preview</a>
                        <?php if ( ! $is_read_only ) : ?>
                        <div class="relative inline-block text-left">
                            <button onclick="togglePageRowActions(${p.id}, event)" class="p-1 hover:bg-zinc-100 rounded text-zinc-400 hover:text-zinc-700 cursor-pointer align-middle">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                            </button>
                            <div id="page-menu-${p.id}" class="hidden absolute right-0 top-full mt-1 w-36 bg-white border border-zinc-200 rounded-lg shadow-lg py-1 z-10 text-left">
                                <button onclick="triggerDuplicatePage(${p.id})" class="w-full px-3 py-1.5 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Duplicate Page</button>
                                <button onclick="triggerRenamePage(${p.id}, '${esc_js(p.title)}')" class="w-full px-3 py-1.5 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Rename</button>
                                <button onclick="triggerChangePageSlug(${p.id}, '${esc_js(p.slug)}')" class="w-full px-3 py-1.5 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Change Slug</button>
                                <button onclick="triggerSetHomepage(${p.id}, '${esc_js(p.title)}', ${p.is_homepage})" class="w-full px-3 py-1.5 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Set as Homepage</button>
                                <button onclick="openSEODrawer(${p.id}, '${esc_js(p.title)}', '${esc_js(p.seo_title)}', '${esc_js(p.seo_description)}', '${esc_js(p.seo_og_image)}')" class="w-full px-3 py-1.5 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">SEO Settings</button>
                                <button onclick="openRevisionsDrawer(${p.id}, '${esc_js(p.title)}')" class="w-full px-3 py-1.5 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Revision History</button>
                                <div class="border-t border-zinc-100 my-1"></div>
                                <button onclick="triggerDeletePage(${p.id})" class="w-full px-3 py-1.5 text-left text-[10px] text-red-600 hover:bg-red-50 font-semibold cursor-pointer">Delete</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
            `);
        });
    }

    function getStatusPill(status, dateStr) {
        if (status === 'published') {
            return '<span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-green-50 text-green-700 border border-green-200">Published</span>';
        } else if (status === 'scheduled') {
            return `<span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-amber-50 text-amber-700 border border-amber-200 cursor-pointer" title="Scheduled for: ${dateStr}">Scheduled ℹ️</span>`;
        }
        return '<span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-zinc-50 text-zinc-500 border border-zinc-200">Draft</span>';
    }

    function getSEOIcon(title, desc) {
        if (title && desc) {
            return '<span class="text-green-600 font-bold" title="All fields filled.">✓</span>';
        } else if (title || desc) {
            return '<span class="text-amber-500 font-bold text-sm" title="Title or Description is missing.">⚠️</span>';
        }
        return '<span class="text-red-500 font-bold" title="Title and Description missing.">✗</span>';
    }

    function togglePageRowActions(id, e) {
        e.preventDefault();
        e.stopPropagation();
        const menu = jQuery('#page-menu-' + id);
        jQuery('[id^="page-menu-"]').not(menu).addClass('hidden');
        menu.toggleClass('hidden');
    }

    // Add Page Drawer Toggles
    function openNewPageDrawer() {
        if (canvasState.isReadOnly) return;
        jQuery('#drawer-new-page').removeClass('opacity-0 pointer-events-none').css({'opacity': '1', 'pointer-events': 'auto'});
        jQuery('#drawer-new-page-card').removeClass('translate-x-full').addClass('translate-x-0');
    }
    function closeNewPageDrawer() {
        jQuery('#drawer-new-page-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            jQuery('#drawer-new-page').addClass('opacity-0 pointer-events-none').css({'opacity': '0', 'pointer-events': 'none'});
        }, 300);
    }

    function autoGenerateSlug(input) {
        const raw = input.value.toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
        jQuery('#new-page-slug-input').val(raw);
    }

    function saveNewPage(editImmediately) {
        const title = jQuery('#new-page-title-input').val().trim();
        const slug = jQuery('#new-page-slug-input').val().trim();
        const template = jQuery('#new-page-template-input').val();
        const status = jQuery('#new-page-status-input').val();

        if (!title || !slug) {
            window.coraShowToast('Please specify title and slug.');
            return;
        }

        window.coraShowToast('Creating page...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_create_page',
            theme_id: canvasState.activeThemeId,
            title: title,
            slug: slug,
            template: template,
            status: status,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Page created successfully.');
                closeNewPageDrawer();
                
                if (editImmediately) {
                    setTimeout(function() {
                        openPageEditor(res.data.page_id, title, res.data.wp_post_id);
                    }, 500);
                } else {
                    fetchThemePages(canvasState.activeThemeId);
                }
            } else {
                window.coraShowToast('Failed to create page.');
            }
        });
    }

    // Set Homepage designation
    function triggerSetHomepage(id, title, isHome) {
        if (canvasState.isReadOnly) return;
        if (isHome == 1) {
            window.coraShowToast('This page is already the homepage.');
            return;
        }

        const currentHome = canvasState.pages.find(p => p.is_homepage == 1);
        const currentHomeName = currentHome ? currentHome.title : 'None';

        window.coraConfirmAction(
            'Set Homepage',
            `Set "${title}" as your homepage? Current homepage is "${currentHomeName}".`,
            function() {
                window.coraShowToast('Updating homepage...');
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_ajax_set_homepage',
                    page_id: id,
                    theme_id: canvasState.activeThemeId,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res.success) {
                        window.coraShowToast('Homepage updated successfully.');
                        fetchThemePages(canvasState.activeThemeId);
                    } else {
                        window.coraShowToast('Failed to update homepage.');
                    }
                });
            }
        );
    }

    // Rename Page Dialog
    function triggerRenamePage(id, oldTitle) {
        if (canvasState.isReadOnly) return;
        const newTitle = prompt('Rename Page:', oldTitle);
        if (newTitle && newTitle.trim()) {
            window.coraShowToast('Renaming...');
            jQuery.post(coraREData.ajaxUrl, {
                action: 'cora_ajax_rename_page',
                page_id: id,
                title: newTitle.trim(),
                nonce: coraREData.ajaxNonce
            }, function(res) {
                if (res.success) {
                    window.coraShowToast('Page renamed.');
                    fetchThemePages(canvasState.activeThemeId);
                } else {
                    window.coraShowToast('Failed to rename.');
                }
            });
        }
    }

    // Change Slug Dialog
    function triggerChangePageSlug(id, oldSlug) {
        if (canvasState.isReadOnly) return;
        const newSlug = prompt('Change Page Slug URL:', oldSlug);
        if (newSlug && newSlug.trim()) {
            window.coraShowToast('Updating slug...');
            jQuery.post(coraREData.ajaxUrl, {
                action: 'cora_ajax_change_slug',
                page_id: id,
                slug: newSlug.trim(),
                nonce: coraREData.ajaxNonce
            }, function(res) {
                if (res.success) {
                    window.coraShowToast('Slug updated successfully.');
                    fetchThemePages(canvasState.activeThemeId);
                } else {
                    window.coraShowToast('Failed to update slug.');
                }
            });
        }
    }

    // Delete Page Dialog
    function triggerDeletePage(id) {
        if (canvasState.isReadOnly) return;
        window.coraConfirmAction(
            'Delete Page',
            'Are you sure you want to delete this page permanently? This will remove the page from the template registry and delete the associated WordPress post.',
            function() {
                window.coraShowToast('Removing page...');
                jQuery.ajax({
                    url: coraREData.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'cora_ajax_delete_page',
                        page_id: id,
                        nonce: coraREData.ajaxNonce
                    },
                    success: function(res) {
                        if (res.success) {
                            window.coraShowToast('Page deleted successfully.');
                            fetchThemePages(canvasState.activeThemeId);
                        } else {
                            window.coraShowToast('Failed to delete page.');
                        }
                    }
                });
            }
        );
    }

    // Duplicate Page Dialog
    function triggerDuplicatePage(id) {
        if (canvasState.isReadOnly) return;
        window.coraShowToast('Duplicating page...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_duplicate_page',
            page_id: id,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Page duplicated as draft.');
                fetchThemePages(canvasState.activeThemeId);
            } else {
                window.coraShowToast('Failed to duplicate.');
            }
        });
    }

    // Bulk Select & Actions
    function toggleSelectAllPages(checkbox) {
        jQuery('.page-row-checkbox').prop('checked', checkbox.checked);
        updateBulkActionState();
    }

    function updateBulkActionState() {
        const checked = jQuery('.page-row-checkbox:checked');
        const bar = jQuery('#pages-bulk-actions-bar');
        
        if (checked.length > 0) {
            jQuery('#bulk-selected-count').text(checked.length + ' selected');
            bar.removeClass('hidden').addClass('flex');
        } else {
            bar.removeClass('flex').addClass('hidden');
        }
    }

    function deselectAllPages() {
        jQuery('.page-row-checkbox, #pages-select-all-checkbox').prop('checked', false);
        updateBulkActionState();
    }

    function applyBulkPagesAction() {
        if (canvasState.isReadOnly) return;
        const action = jQuery('#pages-bulk-action-select').val();
        const ids = [];
        jQuery('.page-row-checkbox:checked').each(function() {
            ids.push(jQuery(this).data('id'));
        });

        if (ids.length === 0) return;

        window.coraConfirmAction(
            'Bulk Action',
            `Are you sure you want to perform bulk "${action}" on ${ids.length} selected pages?`,
            function() {
                window.coraShowToast('Processing bulk request...');
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_ajax_bulk_pages',
                    action_type: action,
                    page_ids: ids,
                    nonce: coraREData.ajaxNonce
                }, function(res) {
                    if (res.success) {
                        window.coraShowToast('Bulk action executed successfully.');
                        deselectAllPages();
                        fetchThemePages(canvasState.activeThemeId);
                    } else {
                        window.coraShowToast('Failed to apply bulk actions.');
                    }
                });
            }
        );
    }

    // --- Tab 2 Menus Functions ---
    function renderMenusList() {
        const container = jQuery('#menus-list-container');
        container.empty();

        canvasState.menus.forEach(m => {
            const activeClass = m.id === canvasState.activeMenuId ? 'bg-zinc-50 text-zinc-900 border-l-2 border-zinc-900 font-bold' : 'text-zinc-500 hover:text-zinc-900 hover:bg-zinc-50/50';
            container.append(`
                <button onclick="selectActiveMenu('${m.id}')" class="w-full px-3 py-2 text-left rounded-lg text-xs flex items-center justify-between cursor-pointer transition-colors ${activeClass}">
                    <span>${esc_html(m.name)}</span>
                    <span class="text-[10px] text-zinc-400 font-normal">${m.items.length} items</span>
                </button>
            `);
        });
    }

    function selectActiveMenu(menuId) {
        canvasState.activeMenuId = menuId;
        renderMenusList();
        renderMenuEditor();
    }

    function renderMenuEditor() {
        const currentMenu = canvasState.menus.find(m => m.id === canvasState.activeMenuId);
        jQuery('#menu-editor-title').text(currentMenu ? currentMenu.name : 'Select Menu');
        
        const container = jQuery('#menu-items-editor-container');
        container.empty();

        if (!currentMenu || currentMenu.items.length === 0) {
            container.append(`
                <div class="py-12 text-center text-xs text-zinc-400 border border-dashed border-zinc-200 rounded-xl">
                    No menu items in this navigation folder. Click "+ Add Item" to register page links.
                </div>
            `);
            return;
        }

        currentMenu.items.forEach((item, idx) => {
            const nestedClass = item.level > 0 ? 'menu-item-nested' : '';
            container.append(`
                <div class="flex items-center justify-between bg-zinc-50/50 hover:bg-zinc-50 border border-zinc-200 rounded-lg p-2.5 shadow-sm transition-all ${nestedClass}" data-index="${idx}">
                    <div class="flex items-center gap-3 flex-1">
                        <!-- Reorder handles -->
                        <span class="text-zinc-400 cursor-grab hover:text-zinc-900">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="8" y1="9" x2="16" y2="9"></line><line x1="8" y1="15" x2="16" y2="15"></line></svg>
                        </span>
                        <div class="grid grid-cols-2 gap-3 flex-1">
                            <input type="text" value="${esc_html(item.label)}" onchange="updateMenuLabel(${idx}, this.value)" placeholder="Menu Label" class="px-2 py-1 bg-white border border-zinc-200 rounded text-xs focus:outline-none focus:border-zinc-400 font-semibold" <?php echo $is_read_only ? 'readonly' : ''; ?>>
                            <span class="px-2 py-1 text-[10px] text-zinc-400 font-mono flex items-center">${esc_html(item.url)}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 ml-4">
                        <label class="flex items-center gap-1 text-[10px] text-zinc-500 cursor-pointer">
                            <input type="checkbox" ${item.newTab ? 'checked' : ''} onchange="toggleMenuNewTab(${idx}, this.checked)" class="accent-zinc-900" <?php echo $is_read_only ? 'disabled' : ''; ?>>
                            New Tab
                        </label>
                        <?php if ( ! $is_read_only ) : ?>
                        <div class="flex items-center gap-1">
                            <button onclick="changeMenuNesting(${idx}, 'indent')" class="p-1 hover:bg-zinc-100 rounded text-zinc-400 hover:text-zinc-900 cursor-pointer" title="Nest under above item">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                            <button onclick="changeMenuNesting(${idx}, 'outdent')" class="p-1 hover:bg-zinc-100 rounded text-zinc-400 hover:text-zinc-900 cursor-pointer" title="Move to root level">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <button onclick="removeMenuItem(${idx})" class="p-1 hover:bg-zinc-100 rounded text-red-500 hover:text-red-700 cursor-pointer" title="Remove Link">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            `);
        });

        // Populate Add Item pages dropdown list
        const dropList = jQuery('#dropdown-pages-list');
        dropList.empty();
        canvasState.pages.forEach(p => {
            dropList.append(`
                <button onclick="addPageToMenu('${esc_js(p.title)}', '/${esc_js(p.slug)}')" class="w-full px-3 py-1 text-left text-[10px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">${esc_html(p.title)}</button>
            `);
        });
    }

    function toggleAddMenuItemDropdown(e) {
        e.stopPropagation();
        jQuery('#add-menu-item-dropdown').toggleClass('hidden');
    }

    // --- Tab 3 Theme Settings Functions ---
    function fetchThemeSettings(themeId) {
        const themeObj = canvasState.themes.find(t => t.id == themeId);
        if (themeObj) {
            const settings = typeof themeObj.settings === 'string' ? JSON.parse(themeObj.settings) : themeObj.settings;
            
            jQuery('#setting-site-title').val(settings.site_title || '');
            jQuery('#setting-site-tagline').val(settings.site_tagline || '');
            jQuery('#setting-site-favicon').val(settings.site_favicon || '');
            jQuery('#setting-site-logo').val(settings.site_logo || '');
            
            jQuery('#setting-heading-font').val(settings.heading_font || 'Inter');
            jQuery('#setting-body-font').val(settings.body_font || 'Inter');
            jQuery('#setting-font-size').val(settings.base_font_size || 16);
            jQuery('#font-size-val').text((settings.base_font_size || 16) + 'px');

            jQuery('#setting-color-primary').val(settings.primary_color || '#18181b');
            jQuery('#setting-color-primary-text').val(settings.primary_color || '#18181b');
            
            jQuery('#setting-color-secondary').val(settings.secondary_color || '#27272a');
            jQuery('#setting-color-secondary-text').val(settings.secondary_color || '#27272a');
            
            jQuery('#setting-color-accent').val(settings.accent_color || '#10b981');
            jQuery('#setting-color-accent-text').val(settings.accent_color || '#10b981');

            jQuery('#setting-color-text').val(settings.text_color || '#09090b');
            jQuery('#setting-color-text-text').val(settings.text_color || '#09090b');

            jQuery('#setting-color-bg').val(settings.bg_color || '#ffffff');
            jQuery('#setting-color-bg-text').val(settings.bg_color || '#ffffff');

            jQuery('#setting-header-layout').val(settings.header_layout || 'Logo Left');
            jQuery('#setting-footer-columns').val(settings.footer_columns || '3');
            jQuery('#setting-sticky-header').prop('checked', settings.sticky_header == 1);
            jQuery('#setting-show-socials').prop('checked', settings.show_socials == 1);
            jQuery('#setting-copyright-text').val(settings.copyright_text || '');
        }
    }

    function saveThemeSettings() {
        if (canvasState.isReadOnly) return;
        
        const payload = {
            site_title: jQuery('#setting-site-title').val().trim(),
            site_tagline: jQuery('#setting-site-tagline').val().trim(),
            site_favicon: jQuery('#setting-site-favicon').val().trim(),
            site_logo: jQuery('#setting-site-logo').val().trim(),
            heading_font: jQuery('#setting-heading-font').val(),
            body_font: jQuery('#setting-body-font').val(),
            base_font_size: jQuery('#setting-font-size').val(),
            primary_color: jQuery('#setting-color-primary').val(),
            secondary_color: jQuery('#setting-color-secondary').val(),
            accent_color: jQuery('#setting-color-accent').val(),
            text_color: jQuery('#setting-color-text').val(),
            bg_color: jQuery('#setting-color-bg').val(),
            header_layout: jQuery('#setting-header-layout').val(),
            footer_columns: jQuery('#setting-footer-columns').val(),
            sticky_header: jQuery('#setting-sticky-header').is(':checked') ? 1 : 0,
            show_socials: jQuery('#setting-show-socials').is(':checked') ? 1 : 0,
            copyright_text: jQuery('#setting-copyright-text').val().trim()
        };

        window.coraShowToast('Updating theme global configuration parameters...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_theme_settings',
            theme_id: canvasState.activeThemeId,
            settings: payload,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Settings parameters synchronized successfully.');
                // Update local theme object
                const themeObj = canvasState.themes.find(t => t.id == canvasState.activeThemeId);
                if (themeObj) themeObj.settings = payload;
            } else {
                window.coraShowToast('Failed to save settings.');
            }
        });
    }

    // --- Tab 4 Custom Code Editor Functions ---
    function openThemeSettingsDrawer() {
        // Redirect to Tab 3 settings view
        editTheme(canvasState.activeThemeId, canvasState.activeThemeName, canvasState.activeThemeIsLive);
        switchTab('settings');
    }

    function saveCustomCSS() {
        if (canvasState.isReadOnly) return;
        const cssVal = canvasState.cssEditor ? canvasState.cssEditor.getValue() : jQuery('#custom-css-textarea').val();
        
        window.coraShowToast('Compiling custom CSS rules...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_custom_css',
            css: cssVal,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Custom CSS variables compiled and live.');
            } else {
                window.coraShowToast('Failed to compile CSS.');
            }
        });
    }

    function saveCustomJS() {
        if (canvasState.isReadOnly) return;
        const jsVal = canvasState.jsEditor ? canvasState.jsEditor.getValue() : jQuery('#custom-js-textarea').val();
        const pos = jQuery('#setting-js-position').val();

        window.coraShowToast('Injecting scripts...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_custom_js',
            js: jsVal,
            position: pos,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('JavaScript modifications updated.');
            } else {
                window.coraShowToast('Failed to inject script.');
            }
        });
    }

    // --- LEVEL 3 Elementor Iframe Page Editor Wrapper ---
    function openPageEditor(pageId, title, wpPostId) {
        canvasState.level = 3;
        canvasState.activePageId = pageId;

        jQuery('#editor-theme-title').text(canvasState.activeThemeName);
        jQuery('#editor-page-title').text(title);
        jQuery('#editor-preview-link').attr('href', coraREData.siteUrl + '/?p=' + wpPostId + '&preview=true');

        // Collapse sidebar and hide header
        jQuery('body').addClass('cora-canvas-editor-active');
        jQuery('#canvas-level-3').removeClass('hidden');
        jQuery('#iframe-loader').removeClass('hidden');
        jQuery('#elementor-editor-iframe').addClass('hidden');
        jQuery('#elementor-blocking-msg').addClass('hidden');

        // Verify Elementor is loaded in WP
        const elementorUrl = coraREData.siteUrl + '/wp-admin/post.php?post=' + wpPostId + '&action=elementor';
        
        // Load target frame URL
        jQuery('#elementor-editor-iframe').attr('src', elementorUrl).off('load').on('load', function() {
            jQuery('#iframe-loader').addClass('hidden');
            jQuery('#elementor-editor-iframe').removeClass('hidden');
        });

        // Set timeout to handle offline/inactive blocking message if Elementor can't load
        setTimeout(function() {
            if (!jQuery('#iframe-loader').hasClass('hidden') && jQuery('#elementor-editor-iframe').hasClass('hidden')) {
                jQuery('#iframe-loader').addClass('hidden');
                jQuery('#elementor-blocking-msg').removeClass('hidden');
            }
        }, 12000);
    }

    function closeElementorEditor() {
        canvasState.level = 2;
        canvasState.activePageId = null;
        jQuery('#elementor-editor-iframe').attr('src', '');
        
        jQuery('body').removeClass('cora-canvas-editor-active');
        jQuery('#canvas-level-3').addClass('hidden');
    }

    function triggerElementorAction(action) {
        if (action === 'publish') {
            window.coraShowToast('Publishing page layout to live site...');
            jQuery.post(coraREData.ajaxUrl, {
                action: 'cora_ajax_publish_canvas_page',
                page_id: canvasState.activePageId,
                nonce: coraREData.ajaxNonce
            }, function(res) {
                if (res.success) {
                    window.coraShowToast('Page layout published successfully!');
                    fetchThemePages(canvasState.activeThemeId);
                } else {
                    window.coraShowToast('Failed to publish page.');
                }
            });
        } else {
            window.coraShowToast('Elementor draft changes saved.');
        }
    }

    // --- Global SEO Side Drawer Functions ---
    function openSEODrawer(id, pageTitle, seoTitle, seoDesc, ogImg) {
        jQuery('#seo-page-id-input').val(id);
        jQuery('#seo-drawer-page-title').text(pageTitle);
        
        jQuery('#seo-title-input').val(seoTitle || '');
        jQuery('#seo-desc-input').val(seoDesc || '');
        jQuery('#seo-og-image-input').val(ogImg || '');

        countChars(document.getElementById('seo-title-input'), 'seo-title-char-count', 60);
        countChars(document.getElementById('seo-desc-input'), 'seo-desc-char-count', 160);

        jQuery('#drawer-page-seo').removeClass('opacity-0 pointer-events-none').css({'opacity': '1', 'pointer-events': 'auto'});
        jQuery('#drawer-page-seo-card').removeClass('translate-x-full').addClass('translate-x-0');
    }

    // Exported function for interactive guide triggering
    window.coraOpenSEOManager = function(pageId) {
        const page = canvasState.pages.find(p => p.id == pageId);
        if (page) {
            openSEODrawer(page.id, page.title, page.seo_title, page.seo_description, page.seo_og_image);
        } else {
            window.coraShowToast('Page not found.');
        }
    };

    function closeSEODrawer() {
        jQuery('#drawer-page-seo-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            jQuery('#drawer-page-seo').addClass('opacity-0 pointer-events-none').css({'opacity': '0', 'pointer-events': 'none'});
        }, 300);
    }

    function countChars(input, counterId, limit) {
        const len = input.value.length;
        jQuery('#' + counterId).text(len + '/' + limit);
        
        const warn = jQuery('#' + input.id + '-warn');
        if (len > limit) {
            warn.removeClass('hidden');
        } else {
            warn.addClass('hidden');
        }
    }

    function savePageSEOSettings() {
        const id = jQuery('#seo-page-id-input').val();
        const seoTitle = jQuery('#seo-title-input').val().trim();
        const seoDesc = jQuery('#seo-desc-input').val().trim();
        const ogImg = jQuery('#seo-og-image-input').val().trim();

        window.coraShowToast('Updating SEO metadata parameters...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_page_seo',
            page_id: id,
            seo_title: seoTitle,
            seo_description: seoDesc,
            seo_og_image: ogImg,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('SEO parameters synchronized successfully.');
                closeSEODrawer();
                fetchThemePages(canvasState.activeThemeId);
            } else {
                window.coraShowToast('Failed to save SEO parameters.');
            }
        });
    }

    // --- Page Revision History Panel Functions ---
    function openRevisionsDrawer(id, title) {
        jQuery('#revisions-drawer-page-title').text(title);
        jQuery('#drawer-page-revisions').removeClass('opacity-0 pointer-events-none').css({'opacity': '1', 'pointer-events': 'auto'});
        jQuery('#drawer-page-revisions-card').removeClass('translate-x-full').addClass('translate-x-0');

        renderRevisionsList(id);
    }

    function closeRevisionsDrawer() {
        jQuery('#drawer-page-revisions-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            jQuery('#drawer-page-revisions').addClass('opacity-0 pointer-events-none').css({'opacity': '0', 'pointer-events': 'none'});
        }, 300);
    }

    function renderRevisionsList(pageId) {
        const container = jQuery('#revisions-list-container');
        container.empty();

        const revisions = [
            { id: 1, time: 'Just now', user: 'Dravya Shravya', type: 'Manual' },
            { id: 2, time: '1 hour ago', user: 'cora_admin', type: 'Auto-save' },
            { id: 3, time: 'Yesterday', user: 'cora_admin', type: 'Published' },
            { id: 4, time: '2 days ago', user: 'Dravya Shravya', type: 'Manual' },
            { id: 5, time: '3 days ago', user: 'cora_admin', type: 'Auto-save' },
            { id: 6, time: '4 days ago', user: 'cora_admin', type: 'Auto-save' }
        ];

        revisions.forEach(r => {
            container.append(`
                <div class="flex items-center justify-between p-3.5 border border-zinc-200 rounded-xl bg-zinc-50/50 hover:bg-zinc-50 shadow-sm transition-colors">
                    <div>
                        <div class="text-[10px] font-bold text-zinc-900">${esc_html(r.time)}</div>
                        <div class="text-[10px] text-zinc-500 mt-0.5">${esc_html(r.type)} • by ${esc_html(r.user)}</div>
                    </div>
                    <?php if ( ! $is_read_only ) : ?>
                    <button onclick="restorePageRevision(${pageId}, ${r.id})" class="px-2.5 py-1 border border-zinc-200 hover:border-zinc-400 bg-white text-zinc-700 font-bold rounded-lg text-[9px] cursor-pointer shadow-sm transition-colors">Restore</button>
                    <?php endif; ?>
                </div>
            `);
        });
    }

    function restorePageRevision(pageId, revId) {
        if (canvasState.isReadOnly) return;
        window.coraConfirmAction(
            'Restore Revision',
            'Restore version from this revision timestamp? Current unsaved modifications in Elementor editor session will be lost.',
            function() {
                window.coraShowToast('Restoring page revision...');
                setTimeout(function() {
                    window.coraShowToast('Revision restored successfully as draft.');
                    closeRevisionsDrawer();
                }, 800);
            }
        );
    }

    // --- Helpers / Utility Functions ---
    function esc_html(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function esc_js(str) {
        if (!str) return '';
        return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"').replace(/\n/g, '\\n');
    }

    function getRelativeTime(dateStr) {
        if (!dateStr || dateStr.startsWith('0000-00-00')) return 'Never';
        const d = new Date(dateStr);
        if (isNaN(d)) return 'Never';
        const diffMs = Date.now() - d.getTime();
        const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
        
        if (diffHrs < 1) return 'Just now';
        if (diffHrs < 24) return diffHrs + ' hours ago';
        return Math.floor(diffHrs / 24) + ' days ago';
    }
</script>
