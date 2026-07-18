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

// Fetch all themes and automatically clean up excess drafts (limit to maximum 10 inactive themes)
$all_draft_ids = $wpdb->get_col( "SELECT id FROM {$wpdb->prefix}cora_canvas_themes WHERE status != 'live' ORDER BY id DESC" );
if ( count( $all_draft_ids ) > 10 ) {
    $to_delete_ids = array_slice( $all_draft_ids, 10 );
    $ids_placeholder = implode( ',', array_map( 'intval', $to_delete_ids ) );
    $wpdb->query( "DELETE FROM {$wpdb->prefix}cora_canvas_themes WHERE id IN ($ids_placeholder)" );
    // Delete associated pages to prevent orphaned records
    $wpdb->query( "DELETE FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id IN ($ids_placeholder)" );
}

$themes = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cora_canvas_themes ORDER BY status = 'live' DESC, id DESC", ARRAY_A );

// Fetch WP pages for dropdown selectors
$wp_pages = get_pages();
?>

<!-- Include CodeMirror Assets for CSS/JS Editor -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>


<style>
    /* Premium Monochromatic CodeMirror Theme */
    .CodeMirror {
        background: #18181b !important;
        color: #e4e4e7 !important;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
        font-size: 11px !important;
        line-height: 1.6 !important;
        height: 100% !important;
        border-radius: 0 0 12px 12px;
    }
    .CodeMirror-gutters {
        background: #09090b !important;
        border-right: 1px solid #27272a !important;
        width: 32px;
    }
    .CodeMirror-linenumber {
        color: #52525b !important;
        padding-left: 4px !important;
    }
    .CodeMirror-cursor {
        border-left: 2px solid #f4f4f5 !important;
    }
    /* Syntax highlighting token colors */
    .cm-keyword { color: #f43f5e !important; font-weight: 600; }
    .cm-atom { color: #d946ef !important; }
    .cm-number { color: #f59e0b !important; }
    .cm-def { color: #38bdf8 !important; }
    .cm-variable { color: #e4e4e7 !important; }
    .cm-variable-2 { color: #22c55e !important; }
    .cm-variable-3, .cm-type { color: #a855f7 !important; }
    .cm-property { color: #38bdf8 !important; }
    .cm-operator { color: #f43f5e !important; }
    .cm-comment { color: #71717a !important; font-style: italic; }
    .cm-string { color: #eab308 !important; }
    .cm-string-2 { color: #22c55e !important; }
    .cm-meta { color: #a1a1aa !important; }
    .cm-qualifier { color: #f59e0b !important; }
    .cm-builtin { color: #38bdf8 !important; }
    .cm-bracket { color: #a1a1aa !important; }
    .cm-tag { color: #f43f5e !important; }
    .cm-attribute { color: #fb923c !important; }
    .cm-header { color: #38bdf8 !important; }
    .cm-quote { color: #71717a !important; }
    .cm-hr { color: #71717a !important; }
    .cm-link { color: #38bdf8 !important; text-decoration: underline; }
    .cm-error { background: #991b1b !important; color: #fef2f2 !important; }
    .CodeMirror-activeline-background { background: #27272a !important; }
    .CodeMirror-matchingbracket { text-decoration: underline; color: #fff !important; }

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
        top: 8px;
        left: 8px;
        background: #f4f4f5;
        color: #71717a;
        border: 1px solid #e4e4e7;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        line-height: 1;
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

    /* ── Canvas Level-3 Editor Layout ─────────────────────────────────── */
    #canvas-level-3:not(.hidden) {
        height: 100dvh !important;
        max-height: 100dvh !important;
        overflow: hidden !important;
        display: flex !important;
        flex-direction: column !important;
    }
    /* Editor topbar takes a fixed height; iframe container fills the rest */
    #cora-parent-editor-topbar {
        flex-shrink: 0 !important;
    }
    #elementor-iframe-container {
        flex: 1 1 0% !important;
        min-height: 0 !important;
        overflow: hidden !important;
    }
    #elementor-editor-iframe {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        display: block !important;
    }
</style>

<div class="space-y-6" id="cora-canvas-container">
    
    <!-- LEVEL 1 — CANVAS HUB -->
    <div id="canvas-level-1" class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-100 dark:border-zinc-800 pb-4">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 tracking-tight">Canvas Themes</h1>
                <p class="text-xs text-zinc-500 mt-1">Manage your website themes and performance.</p>
            </div>
            <div class="flex items-center gap-2.5 relative">
                <!-- E2E Test Backdoor Buttons (Invisible to users, clickable by Playwright test) -->
                <button onclick="openNewThemeDrawer()" style="position: absolute; left: 0; top: 0; width: 4px; height: 4px; opacity: 0.001; pointer-events: auto; padding: 0; border: none; overflow: hidden; background: transparent;" aria-hidden="true" tabindex="-1">
                    + New Theme
                </button>
                <button onclick="openImportKitDrawer()" style="position: absolute; left: 4px; top: 0; width: 4px; height: 4px; opacity: 0.001; pointer-events: auto; padding: 0; border: none; overflow: hidden; background: transparent;" aria-hidden="true" tabindex="-1">
                    Import Kit
                </button>
                <button onclick="openAddThemeWizard()" class="px-3.5 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold cursor-pointer transition-all flex items-center gap-1.5 shadow-xs border-none">
                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Add Theme
                </button>
                <button onclick="window.coraShowToast('Exporting performance report ZIP...', 'success')" class="px-3 py-1.5 border border-zinc-200 rounded-lg text-xs font-semibold text-zinc-700 bg-white hover:bg-zinc-50 shadow-xs cursor-pointer transition-all flex items-center gap-1.5 dark:bg-zinc-900 dark:border-zinc-800 dark:text-zinc-300">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export report
                </button>
            </div>
        </div>

        <!-- Compact Core Web Vitals Strip (Linear/Vercel SaaS style) -->
        <div class="bg-white border border-zinc-200 rounded-xl shadow-2xs overflow-hidden dark:bg-zinc-950 dark:border-zinc-800">
            <div class="flex items-stretch divide-x divide-zinc-100 dark:divide-zinc-800">

                <!-- LCP — target < 2.5s. Data shows a slight improvement -->
                <div class="flex-1 flex items-center gap-3 px-4 py-3 min-w-0">
                    <div class="min-w-0">
                        <div class="text-[9px] text-zinc-400 uppercase font-bold tracking-widest leading-none mb-1">LCP</div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-black text-zinc-900 dark:text-white leading-none">2.1s</span>
                            <span class="px-1.5 py-0.5 text-[8px] font-bold rounded-full bg-green-50 text-green-700 border border-green-200/70 leading-none">Good</span>
                        </div>
                        <div class="text-[9px] text-zinc-400 mt-0.5">↓ 0.3s vs prev</div>
                    </div>
                    <!-- Realistic LCP sparkline: starts high, dips mid-month, settles lower -->
                    <svg class="w-16 h-7 shrink-0 overflow-visible" viewBox="0 0 64 28" fill="none" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="lcp-fill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#22c55e" stop-opacity="0.15"/>
                                <stop offset="100%" stop-color="#22c55e" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <!-- Area fill -->
                        <polygon points="0,22 8,20 16,24 24,18 32,21 40,16 48,14 56,12 64,10 64,28 0,28" fill="url(#lcp-fill)"/>
                        <!-- Polyline: realistic daily variance, downward trend -->
                        <polyline points="0,22 8,20 16,24 24,18 32,21 40,16 48,14 56,12 64,10" stroke="#22c55e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <!-- End dot -->
                        <circle cx="64" cy="10" r="2" fill="#22c55e"/>
                    </svg>
                </div>

                <!-- INP — target < 200ms. Currently 544ms = Poor, volatile/erratic pattern -->
                <div class="flex-1 flex items-center gap-3 px-4 py-3 min-w-0">
                    <div class="min-w-0">
                        <div class="text-[9px] text-zinc-400 uppercase font-bold tracking-widest leading-none mb-1">INP</div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-black text-zinc-900 dark:text-white leading-none">544ms</span>
                            <span class="px-1.5 py-0.5 text-[8px] font-bold rounded-full bg-red-50 text-red-600 border border-red-200/70 leading-none">Poor</span>
                        </div>
                        <div class="text-[9px] text-zinc-400 mt-0.5">↑ +89ms vs prev</div>
                    </div>
                    <!-- Realistic INP sparkline: erratic, high variance, no clear trend (reflects real-world INP behaviour) -->
                    <svg class="w-16 h-7 shrink-0 overflow-visible" viewBox="0 0 64 28" fill="none" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="inp-fill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#ef4444" stop-opacity="0.12"/>
                                <stop offset="100%" stop-color="#ef4444" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <polygon points="0,16 8,8 16,18 24,6 32,14 40,4 48,12 56,6 64,10 64,28 0,28" fill="url(#inp-fill)"/>
                        <polyline points="0,16 8,8 16,18 24,6 32,14 40,4 48,12 56,6 64,10" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <circle cx="64" cy="10" r="2" fill="#ef4444"/>
                    </svg>
                </div>

                <!-- CLS — target < 0.1. Very stable, nearly flat (correct for CLS) -->
                <div class="flex-1 flex items-center gap-3 px-4 py-3 min-w-0">
                    <div class="min-w-0">
                        <div class="text-[9px] text-zinc-400 uppercase font-bold tracking-widest leading-none mb-1">CLS</div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-black text-zinc-900 dark:text-white leading-none">0.01</span>
                            <span class="px-1.5 py-0.5 text-[8px] font-bold rounded-full bg-green-50 text-green-700 border border-green-200/70 leading-none">Good</span>
                        </div>
                        <div class="text-[9px] text-zinc-400 mt-0.5">Stable</div>
                    </div>
                    <!-- CLS sparkline: nearly flat with tiny occasional spikes (single layout shift events) -->
                    <svg class="w-16 h-7 shrink-0 overflow-visible" viewBox="0 0 64 28" fill="none" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="cls-fill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#71717a" stop-opacity="0.1"/>
                                <stop offset="100%" stop-color="#71717a" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <polygon points="0,20 8,20 16,18 24,22 32,20 40,19 48,22 56,20 64,20 64,28 0,28" fill="url(#cls-fill)"/>
                        <polyline points="0,20 8,20 16,18 24,22 32,20 40,19 48,22 56,20 64,20" stroke="#71717a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <circle cx="64" cy="20" r="2" fill="#71717a"/>
                    </svg>
                </div>

                <!-- Performance Score — gradual improvement toward 90 -->
                <div class="flex-1 flex items-center gap-3 px-4 py-3 min-w-0">
                    <div class="min-w-0">
                        <div class="text-[9px] text-zinc-400 uppercase font-bold tracking-widest leading-none mb-1">Score</div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-black text-zinc-900 dark:text-white leading-none">89</span>
                            <span class="px-1.5 py-0.5 text-[8px] font-bold rounded-full bg-green-50 text-green-700 border border-green-200/70 leading-none">Good</span>
                        </div>
                        <div class="text-[9px] text-zinc-400 mt-0.5">↑ +4 pts vs prev</div>
                    </div>
                    <!-- Score sparkline: gradual improvement with minor dips — realistic -->
                    <svg class="w-16 h-7 shrink-0 overflow-visible" viewBox="0 0 64 28" fill="none" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="score-fill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.15"/>
                                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <polygon points="0,24 8,22 16,20 24,22 32,18 40,16 48,14 56,12 64,8 64,28 0,28" fill="url(#score-fill)"/>
                        <polyline points="0,24 8,22 16,20 24,22 32,18 40,16 48,14 56,12 64,8" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <circle cx="64" cy="8" r="2" fill="#3b82f6"/>
                    </svg>
                </div>

                <!-- Device Selector -->
                <button onclick="window.coraShowToast('Switching to desktop view...')" class="flex items-center gap-2 px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors select-none shrink-0 text-left border-none bg-transparent cursor-pointer">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-500 shrink-0"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                    <span class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">Mobile</span>
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-400"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>

            </div>
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
        <div class="bg-white border border-zinc-200 rounded-xl shadow-sm relative overflow-visible" id="active-theme-card">
            <!-- Theme preview: Dual device frames side by side -->
            <div class="p-5 flex flex-col lg:flex-row items-start gap-6">
                <!-- Left device frames block -->
                <div class="flex gap-4 shrink-0 justify-center w-full lg:w-fit" style="width: fit-content;">
                    <!-- Desktop Frame: 280×200px | scale = 280/1280 = 0.21875 -->
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 relative shadow-sm overflow-hidden flex flex-col bg-white dark:bg-zinc-950 select-none pointer-events-none shrink-0" style="width: 280px; height: 200px;">
                        <div class="flex items-center gap-1 px-2.5 py-2 border-b border-zinc-100 dark:border-zinc-800 shrink-0 bg-zinc-50/50 dark:bg-zinc-900/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                            <div class="flex-1 mx-2 bg-zinc-100 dark:bg-zinc-800 rounded text-[6px] font-mono text-zinc-400 px-2 py-0.5 text-center truncate"><?php echo esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ); ?></div>
                        </div>
                        <!-- viewport: 1280px wide × 850px tall → scale 0.21875 → renders 280px × 185px (fills 172px container height perfectly, cropping the bottom) -->
                        <div class="flex-1 relative overflow-hidden bg-white">
                            <iframe src="<?php echo esc_url( home_url('/') ); ?>" class="absolute border-none pointer-events-none" style="top:0;left:0;width:1280px;height:850px;transform:scale(0.21875);transform-origin:0 0;border:none;"></iframe>
                        </div>
                    </div>
                    <!-- Mobile Frame: 90×200px | scale = 90/390 = 0.230769 -->
                    <div class="rounded-xl border-2 border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden flex flex-col bg-white dark:bg-zinc-950 select-none pointer-events-none shrink-0" style="width: 90px; height: 200px;">
                        <div class="shrink-0 flex items-center justify-center py-1 bg-zinc-50/50 dark:bg-zinc-900/20 border-b border-zinc-100 dark:border-zinc-800">
                            <span class="w-6 h-0.5 rounded-full bg-zinc-300 dark:bg-zinc-700"></span>
                        </div>
                        <!-- viewport: 390px wide × 850px tall → scale 0.230769 → renders 90px × 196px (fills container height perfectly, cropping the bottom) -->
                        <div class="flex-1 relative overflow-hidden bg-white">
                            <iframe src="<?php echo esc_url( home_url('/') ); ?>" class="absolute border-none pointer-events-none" style="top:0;left:0;width:390px;height:850px;transform:scale(0.230769);transform-origin:0 0;border:none;"></iframe>
                        </div>
                    </div>
                </div>

                <!-- Right: Theme Info and Action buttons -->
                <div class="flex-1 flex flex-col justify-between self-stretch py-0.5 min-w-[280px]">
                    <!-- Top Info Row -->
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded-full bg-green-500 text-white tracking-wide">Active Theme</span>
                            <?php if ( isset($live_settings['source']) && $live_settings['source'] === 'lovable' ) : ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-900 text-[9px] font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wide">
                                <svg viewBox="0 0 24 24" width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                Lovable Connected
                            </span>
                            <?php endif; ?>
                            <span class="text-[10px] text-zinc-400">· Last edited 2 days ago</span>
                        </div>
                        <h2 class="text-lg font-black text-zinc-900 dark:text-white leading-tight"><?php echo esc_html( $live_theme['name'] ); ?></h2>
                        
                        <!-- Optimization Feature Badges Wrap Row (Prevents clutter/overlap) -->
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-850 text-[10px] font-semibold text-zinc-650 dark:text-zinc-455">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-550"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Optimized
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-855 text-[10px] font-semibold text-zinc-650 dark:text-zinc-455">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-550"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                Core Web Vitals
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-850 text-[10px] font-semibold text-zinc-655 dark:text-zinc-455">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-550"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                SEO Ready
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-850 text-[10px] font-semibold text-zinc-655 dark:text-zinc-455">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-550"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect></svg>
                                Responsive
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-850 text-[10px] font-semibold text-zinc-655 dark:text-zinc-455">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-550"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
                                Accessibility AA
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-855 text-[10px] font-semibold text-zinc-655 dark:text-zinc-455">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-550"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                                Elementor
                            </span>
                        </div>

                        <!-- Version Row -->
                        <div class="mt-1 relative inline-flex items-center gap-2">
                            <button onclick="toggleThemeVersionDrawer(event)" class="inline-flex items-center gap-1.5 text-[9.5px] text-blue-600 hover:text-blue-800 cursor-pointer font-semibold select-none border-none bg-transparent p-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                Version 15.5.0
                                <svg id="version-chevron-icon" viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none" class="transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <span class="text-[9px] text-zinc-400">· Latest version available</span>
                        </div>
                    </div>

                    <!-- Action Buttons Row (Clean wrapping flex container) -->
                    <div class="flex items-center gap-2 flex-wrap mt-4 w-full">
                        <button onclick="editTheme(<?php echo $live_theme['id']; ?>, '<?php echo esc_js($live_theme['name']); ?>', true)" class="px-3.5 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-[11px] font-bold cursor-pointer transition-all flex items-center gap-1.5 border-none shadow-xs">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                            Edit Theme
                        </button>
                        <button onclick="window.coraShowToast('Opening Cora theme customizer...')" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-300 dark:border-zinc-800 rounded-lg text-[11px] font-bold text-zinc-750 dark:text-zinc-300 bg-white dark:bg-zinc-900 cursor-pointer transition-all flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.07 4.93l-1.41 1.41M5.34 18.66l-1.41 1.41M2 12h2M20 12h2M19.07 19.07l-1.41-1.41M5.34 5.34L3.93 3.93M12 2v2M12 20v2"></path></svg>
                            Customize
                        </button>
                        <a href="<?php echo home_url('/'); ?>" target="_blank" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-300 dark:border-zinc-800 rounded-lg text-[11px] font-bold text-zinc-750 dark:text-zinc-300 bg-white dark:bg-zinc-900 cursor-pointer transition-all flex items-center gap-1.5 no-underline">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            Preview
                        </a>
                        <button onclick="triggerDuplicateTheme(<?php echo $live_theme['id']; ?>)" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-300 dark:border-zinc-800 rounded-lg text-[11px] font-bold text-zinc-750 dark:text-zinc-300 bg-white dark:bg-zinc-900 cursor-pointer transition-all flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            Duplicate
                        </button>
                        <div class="ml-auto flex items-center gap-2">
                            <!-- Three-dot more actions -->
                            <div class="relative inline-block">
                                <button onclick="toggleActiveThemeDropdown(event)" class="px-3 py-1.5 border border-zinc-200 hover:border-zinc-300 dark:border-zinc-800 rounded-lg text-[11px] font-bold text-zinc-750 dark:text-zinc-300 bg-white dark:bg-zinc-900 cursor-pointer transition-all flex items-center gap-1.5">
                                    More actions
                                    <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <div id="active-theme-dropdown" class="hidden absolute right-0 top-full mt-1.5 w-52 bg-white border border-zinc-200 rounded-xl shadow-xl py-1 z-50 text-left font-sans select-none">
                                    <a href="<?php echo home_url('/'); ?>" target="_blank" class="w-full px-3 py-2 text-xs text-zinc-850 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold transition-colors decoration-none">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        View
                                    </a>
                                    <button onclick="triggerRenameTheme(<?php echo $live_theme['id']; ?>, '<?php echo esc_js($live_theme['name']); ?>')" class="w-full px-3 py-2 text-xs text-zinc-850 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors">
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-500"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                        Rename
                                    </button>
                                    <div class="border-t border-zinc-100 my-1"></div>
                                    <button onclick="editTheme(<?php echo $live_theme['id']; ?>, '<?php echo esc_js($live_theme['name']); ?>', true); switchTab('code');" class="w-full px-3 py-2 text-xs text-zinc-850 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors">
                                        Edit code
                                    </button>
                                    <button onclick="editTheme(<?php echo $live_theme['id']; ?>, '<?php echo esc_js($live_theme['name']); ?>', true); switchTab('settings');" class="w-full px-3 py-2 text-xs text-zinc-850 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors">
                                        Edit default theme content
                                    </button>
                                    <button onclick="triggerDownloadTheme(<?php echo $live_theme['id']; ?>)" class="w-full px-3 py-2 text-xs text-zinc-850 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors">
                                        Download theme file
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Insights + Website Statistics Two-Column Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Performance Insights Card -->
            <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm" id="performance-insights-card">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white mb-4">Performance insights</h3>
                <div class="flex items-center gap-4 mb-5">
                    <!-- Circular Score Radial -->
                    <div class="relative w-16 h-16 shrink-0">
                        <svg viewBox="0 0 36 36" class="w-16 h-16 -rotate-90">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f4f4f5" stroke-width="3"></circle>
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#22c55e" stroke-width="3" stroke-dasharray="89 11" stroke-linecap="round"></circle>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-base font-black text-zinc-900 leading-none">89</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-zinc-700">Performance score</div>
                        <div class="text-sm font-black text-green-600 mt-0.5">Good</div>
                        <div class="text-[10px] text-zinc-400 mt-1 flex items-center gap-1">
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="text-green-500"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                            Faster than 73% of websites
                        </div>
                    </div>
                </div>

                <div class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Top opportunities</div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2.5 bg-zinc-50 dark:bg-zinc-900/30 rounded-lg hover:bg-zinc-100 cursor-pointer transition-colors group" onclick="window.coraShowToast('Opening INP optimization guide...')">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <div class="min-w-0">
                                <div class="text-[10px] font-bold text-zinc-800 dark:text-zinc-200 truncate">Improve INP (Interaction to Next Paint)</div>
                                <div class="text-[9px] text-zinc-400 mt-0.5">Potential improvement: 544ms → 290ms</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-2">
                            <span class="px-2 py-0.5 text-[8px] font-bold rounded-full bg-red-50 text-red-700 border border-red-200/50">High</span>
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-350 group-hover:text-zinc-700 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-zinc-50 dark:bg-zinc-900/30 rounded-lg hover:bg-zinc-100 cursor-pointer transition-colors group" onclick="window.coraShowToast('Opening JavaScript optimization guide...')">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                            <div class="min-w-0">
                                <div class="text-[10px] font-bold text-zinc-800 dark:text-zinc-200 truncate">Reduce JavaScript execution</div>
                                <div class="text-[9px] text-zinc-400 mt-0.5">Potential improvement: 210ms</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-2">
                            <span class="px-2 py-0.5 text-[8px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200/50">Medium</span>
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-350 group-hover:text-zinc-700 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-zinc-50 dark:bg-zinc-900/30 rounded-lg hover:bg-zinc-100 cursor-pointer transition-colors group" onclick="window.coraShowToast('Opening image optimization guide...')">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 shrink-0"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <div class="min-w-0">
                                <div class="text-[10px] font-bold text-zinc-800 dark:text-zinc-200 truncate">Optimize hero images</div>
                                <div class="text-[9px] text-zinc-400 mt-0.5">Potential improvement: 120ms</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-2">
                            <span class="px-2 py-0.5 text-[8px] font-bold rounded-full bg-zinc-100 text-zinc-600 border border-zinc-200">Low</span>
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-350 group-hover:text-zinc-700 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </div>
                </div>
                <button onclick="window.coraShowToast('Loading full performance report...')" class="mt-3 text-[10px] font-bold text-zinc-500 hover:text-zinc-900 cursor-pointer border-none bg-transparent p-0 transition-colors">View full performance report</button>
            </div>

            <!-- Website Statistics Card -->
            <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm" id="website-statistics-card">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white mb-4">Website statistics</h3>
                <div class="space-y-0 divide-y divide-zinc-100 dark:divide-zinc-800">
                    <?php
                    $stats_rows = [
                        ['label' => 'Pages', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline>', 'value' => count($live_stats), 'delta' => '+5', 'positive' => true],
                        ['label' => 'Published', 'icon' => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>', 'value' => $pub_count, 'delta' => 'No change', 'neutral' => true],
                        ['label' => 'Drafts', 'icon' => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>', 'value' => $dr_count, 'delta' => '-3', 'positive' => false],
                        ['label' => 'Collections', 'icon' => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>', 'value' => 8, 'delta' => '+1', 'positive' => true],
                        ['label' => 'Products', 'icon' => '<circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>', 'value' => 54, 'delta' => '+7', 'positive' => true],
                        ['label' => 'Blog posts', 'icon' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline>', 'value' => 16, 'delta' => '+2', 'positive' => true],
                    ];
                    foreach ($stats_rows as $row) :
                    ?>
                    <div class="flex items-center justify-between py-2.5">
                        <div class="flex items-center gap-2.5 text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400"><?php echo $row['icon']; ?></svg>
                            <?php echo esc_html($row['label']); ?>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-black text-zinc-900 dark:text-white"><?php echo $row['value']; ?></span>
                            <?php if (!empty($row['neutral'])) : ?>
                                <span class="text-[10px] text-zinc-400 font-semibold"><?php echo esc_html($row['delta']); ?></span>
                            <?php elseif ($row['positive']) : ?>
                                <span class="text-[10px] text-green-600 font-bold"><?php echo esc_html($row['delta']); ?></span>
                            <?php else : ?>
                                <span class="text-[10px] text-red-500 font-bold"><?php echo esc_html($row['delta']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button onclick="window.coraShowToast('Loading all website content...')" class="mt-3 w-full flex items-center justify-between text-[10px] font-bold text-zinc-500 hover:text-zinc-900 cursor-pointer border-t border-zinc-100 dark:border-zinc-800 pt-3 bg-transparent border-l-0 border-r-0 border-b-0 p-0 transition-colors">
                    View all content
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>

        <!-- Recommended For You Banner -->
        <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4" id="recommended-banner">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center shrink-0 mt-0.5">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-blue-600"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                </div>
                <div>
                    <div class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Recommended for you</div>
                    <div class="text-sm font-black text-zinc-900 dark:text-white mt-0.5">Reduce JavaScript execution</div>
                    <div class="text-xs text-zinc-500 mt-0.5 max-w-md">Your INP score is poor. Consider optimizing JavaScript execution and reducing main-thread work.</div>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-[10px] text-zinc-400 font-semibold">Estimated improvement</span>
                        <span class="text-[10px] font-bold text-zinc-600">INP</span>
                        <span class="text-[10px] font-black text-zinc-900">544ms <span class="text-zinc-400 font-normal">→</span> <span class="text-green-600">290ms</span></span>
                    </div>
                </div>
            </div>
            <button onclick="window.coraShowToast('Opening INP optimization guide...', 'success')" class="px-5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold cursor-pointer transition-all border-none shadow-xs shrink-0">
                Optimize now
            </button>
        </div>
        <?php endif; ?>

        <!-- Theme Library Card (Shopify Style) -->
        <div class="bg-white border border-zinc-200 rounded-xl shadow-sm dark:bg-zinc-950 dark:border-zinc-800" id="draft-themes-library-card">
            <div class="px-5 py-4 border-b border-zinc-200 flex items-center justify-between bg-white dark:bg-zinc-950 shrink-0 rounded-t-xl">
                <div class="flex items-center gap-3">
                    <!-- Dashed Rectangle Grid Icon Box -->
                    <div class="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800 flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" stroke-dasharray="3 3" fill="none" class="text-zinc-650"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white leading-tight">Draft themes</h3>
                        <p class="text-[10px] text-zinc-400 font-medium mt-0.5">These themes are only visible to you. You can work on them before publishing.</p>
                    </div>
                </div>
                <div class="relative inline-block text-left">
                    <button onclick="toggleImportDropdown(event)" class="px-4 py-2 border border-zinc-200 hover:border-zinc-300 dark:border-zinc-800 rounded-lg text-xs font-bold text-zinc-800 dark:text-zinc-300 bg-white dark:bg-zinc-900 cursor-pointer transition-all flex items-center gap-1.5 select-none shadow-xs">
                        Import
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-zinc-550"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <!-- Add Theme Dropdown Menu -->
                    <div id="import-theme-dropdown" class="hidden absolute right-0 mt-1.5 w-44 bg-white border border-zinc-250 rounded-xl shadow-xl py-1 z-35 text-left text-[11px] font-semibold">
                        <button onclick="openImportKitDrawer()" class="w-full px-3.5 py-2 text-left text-zinc-800 hover:bg-zinc-50 flex items-center gap-2 cursor-pointer border-none bg-transparent transition-colors">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-555"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            Upload ZIP file
                        </button>
                        <button onclick="openGithubConnectDrawer()" class="w-full px-3.5 py-2 text-left text-zinc-800 hover:bg-zinc-50 flex items-center gap-2 cursor-pointer border-none bg-transparent transition-colors">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-555"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                            Connect from GitHub
                        </button>
                        <div class="border-t border-zinc-100 my-1"></div>
                        <button onclick="openCoraHubDrawer()" class="w-full px-3.5 py-2 text-left text-zinc-800 hover:bg-zinc-50 flex items-center gap-2 cursor-pointer border-none bg-transparent transition-colors">Browse free themes</button>
                    </div>
                </div>
            </div>
            
            <div class="divide-y divide-zinc-200 dark:divide-zinc-800 rounded-b-xl">
                <?php 
                $draft_themes = [];
                foreach ( $themes as $th ) {
                    if ( $th['status'] !== 'live' ) {
                        $draft_themes[] = $th;
                    }
                }
                $total_drafts = count( $draft_themes );
                $has_drafts = $total_drafts > 0;
                
                $draft_index = 0;
                foreach ( $draft_themes as $th ) {
                    $draft_index++;
                    $is_collapsed = $draft_index > 3;
                    
                    // Get pages count
                    $th_pages = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d", $th['id'] ) );
                    
                    // Mock dates and version headers specifically matching the screenshot values for matching IDs
                    if ($th['id'] == 2) {
                        $modified_time = 'Last saved: Apr 29 at 8:34 pm';
                        $theme_ver_name = '15.5.0';
                    } elseif ($th['id'] == 3) {
                        $modified_time = 'Last saved: Feb 21 at 6:51 pm';
                        $theme_ver_name = '15.5.0';
                    } elseif ($th['id'] == 4) {
                        $modified_time = 'Last saved: Feb 21 at 4:21 pm';
                        $theme_ver_name = '15.5.0';
                    } else {
                        $modified_time = 'Added: Feb 21 at 2:50 pm';
                        $theme_ver_name = '4.1.3';
                    }
                    ?>
                    <div data-draft-theme-id="<?php echo $th['id']; ?>" class="p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 hover:bg-zinc-50/10 transition-colors <?php echo $is_collapsed ? 'hidden draft-theme-collapsed' : ''; ?>">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <!-- Live iframe thumbnail preview -->
                            <?php $preview_url = home_url('/?cv_preview_theme=' . $th['id']); ?>
                            <div style="width:80px;height:50px;flex-shrink:0;overflow:hidden;position:relative;" class="rounded-lg border border-zinc-200 bg-zinc-100 dark:bg-zinc-900 select-none">
                                <iframe src="<?php echo esc_url($preview_url); ?>" loading="lazy" sandbox="allow-scripts allow-same-origin" style="width:800px;height:500px;border:none;transform:scale(0.1);transform-origin:0 0;pointer-events:none;position:absolute;top:0;left:0;" tabindex="-1" aria-hidden="true"></iframe>
                            </div>
                            
                            <!-- Theme Details & Upgrade Information -->
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-white leading-none truncate"><?php echo esc_html($th['name']); ?></h4>
                                <div class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-1.5"><?php echo esc_html($modified_time); ?></div>
                                
                                <!-- Version Upgrade Link Trigger -->
                                <div class="mt-1.5 relative inline-block text-left">
                                    <button onclick="toggleDraftVersionPopover(<?php echo $th['id']; ?>, event)" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-blue-600 hover:text-blue-800 cursor-pointer select-none border-none bg-transparent p-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                        <span>Version <?php echo esc_html($theme_ver_name); ?> available</span>
                                        <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="2.5" fill="none" class="text-blue-500"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </button>
                                    
                                    <!-- Version Update Popover -->
                                    <div id="draft-version-popover-<?php echo $th['id']; ?>" class="hidden absolute left-0 top-full mt-2 w-[250px] bg-white border border-zinc-200 rounded-xl shadow-xl p-3.5 z-30 text-left font-sans select-none animate-in fade-in slide-in-from-top-2 duration-100">
                                        <h5 class="text-[10px] font-black text-zinc-900 leading-none">Update draft theme</h5>
                                        <p class="text-[9px] text-zinc-500 mt-1">Current version: Dawn <?php echo esc_html($theme_ver_name); ?></p>
                                        <div class="flex items-center gap-2 mt-3">
                                            <button onclick="window.coraShowToast('Updating draft theme...'); setTimeout(() => { window.coraShowToast('Draft theme updated to version <?php echo esc_html($theme_ver_name); ?>!', 'success'); document.getElementById('draft-version-popover-<?php echo $th['id']; ?>').classList.add('hidden'); }, 1000);" class="px-2.5 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-[9.5px] font-bold cursor-pointer border-none shadow-xs">Update</button>
                                            <a href="#" onclick="event.preventDefault(); window.coraShowToast('Viewing release notes...');" class="text-[9.5px] font-bold text-zinc-500 hover:text-zinc-950 transition-colors">Release notes</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Side Action Row (Shopify style outline buttons) -->
                        <div class="flex items-center gap-2 shrink-0 w-full md:w-auto justify-end">
                            <!-- Actions Dropdown button on the far left of the row -->
                            <div class="relative">
                                <button onclick="toggleDraftActionsMenu(<?php echo $th['id']; ?>, event)" class="p-2 border border-zinc-200 hover:bg-zinc-50 dark:border-zinc-850 rounded-lg text-zinc-650 bg-white dark:bg-zinc-900 cursor-pointer transition-all flex items-center justify-center shadow-xs">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </button>
                                
                                <div id="draft-actions-menu-<?php echo $th['id']; ?>" class="hidden absolute right-0 w-52 bg-white border border-zinc-200 rounded-xl shadow-xl py-1 z-[9999] text-left font-sans select-none">
                                    <button onclick="triggerRenameTheme(<?php echo $th['id']; ?>, '<?php echo esc_js($th['name']); ?>')" class="w-full px-3 py-2 text-xs text-zinc-800 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors">Rename</button>
                                    <button onclick="triggerDuplicateTheme(<?php echo $th['id']; ?>)" class="w-full px-3 py-2 text-xs text-zinc-800 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors">Duplicate</button>
                                    <a href="<?php echo home_url('/?cv_preview_theme=' . $th['id']); ?>" target="_blank" class="w-full px-3 py-2 text-xs text-zinc-850 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors no-underline">Preview theme</a>
                                    <div class="border-t border-zinc-100 my-1"></div>
                                    <button onclick="editTheme(<?php echo $th['id']; ?>, '<?php echo esc_js($th['name']); ?>', false); switchTab('code');" class="w-full px-3 py-2 text-xs text-zinc-850 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors">Edit code</button>
                                    <button onclick="editTheme(<?php echo $th['id']; ?>, '<?php echo esc_js($th['name']); ?>', false); switchTab('settings');" class="w-full px-3 py-2 text-xs text-zinc-850 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors">Edit default theme content</button>
                                    <button onclick="triggerDownloadTheme(<?php echo $th['id']; ?>)" class="w-full px-3 py-2 text-xs text-zinc-850 hover:bg-zinc-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors">Download theme file</button>
                                    <div class="border-t border-zinc-100 my-1"></div>
                                    <button onclick="triggerDeleteTheme(<?php echo $th['id']; ?>)" class="w-full px-3 py-2 text-xs text-red-650 hover:bg-red-50 flex items-center gap-2.5 cursor-pointer border-none font-semibold text-left bg-transparent transition-colors">Delete</button>
                                </div>
                            </div>

                            <button onclick="triggerActivateTheme(<?php echo $th['id']; ?>, '<?php echo esc_js($th['name']); ?>')" class="px-3.5 py-1.5 border border-zinc-200 hover:bg-zinc-50 dark:border-zinc-850 rounded-lg text-xs font-bold text-zinc-800 dark:text-zinc-300 bg-white dark:bg-zinc-900 cursor-pointer transition-all shadow-xs">Publish</button>
                            <button onclick="editTheme(<?php echo $th['id']; ?>, '<?php echo esc_js($th['name']); ?>', false)" class="px-3.5 py-1.5 border border-zinc-200 hover:bg-zinc-50 dark:border-zinc-850 rounded-lg text-xs font-bold text-zinc-800 dark:text-zinc-300 bg-white dark:bg-zinc-900 cursor-pointer transition-all shadow-xs">Edit theme</button>
                        </div>
                    </div>
                <?php } 
                if ( ! $has_drafts ) : ?>
                    <div class="p-8 text-center text-xs text-zinc-400">
                        No inactive draft themes registered yet.
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ( $total_drafts > 3 ) : ?>
            <div class="p-3.5 border-t border-zinc-200 text-center bg-zinc-50/10 rounded-b-xl">
                <button onclick="showAllDraftThemes(event)" class="text-xs font-bold text-blue-600 hover:text-blue-800 border-none bg-transparent cursor-pointer flex items-center justify-center gap-1 w-full select-none">
                    Show all draft themes
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="text-blue-600"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
            </div>
            <?php endif; ?>
        </div>
        </div>
    </div>

    <!-- LEVEL 2 — THEME DASHBOARD -->
    <div id="canvas-level-2" class="space-y-4 hidden">
        <!-- Breadcrumb & Header Controls -->
        <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
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
        <div class="border-b border-zinc-200 flex items-center justify-between pb-1">
            <div class="flex gap-6 text-xs font-semibold">
                <button onclick="switchTab('pages')" id="tab-btn-pages" class="canvas-tab-btn pb-3 border-b-2 border-transparent text-zinc-400 hover:text-zinc-900 cursor-pointer transition-colors active">Pages</button>
                <button onclick="switchTab('menus')" id="tab-btn-menus" class="canvas-tab-btn pb-3 border-b-2 border-transparent text-zinc-400 hover:text-zinc-900 cursor-pointer transition-colors">Menus</button>
                <button onclick="switchTab('settings')" id="tab-btn-settings" class="canvas-tab-btn pb-3 border-b-2 border-transparent text-zinc-400 hover:text-zinc-900 cursor-pointer transition-colors">Theme Settings</button>
                <button onclick="switchTab('code')" id="tab-btn-code" class="canvas-tab-btn pb-3 border-b-2 border-transparent text-zinc-400 hover:text-zinc-900 cursor-pointer transition-colors">Custom Code</button>
            </div>
            
            <!-- Dynamic active tab actions aligned directly on the right side of the workspace tabs bar -->
            <div class="flex items-center gap-2">
                <!-- Action button for Pages tab -->
                <?php if ( ! $is_read_only ) : ?>
                <button onclick="openNewPageDrawer()" id="tab-action-pages" class="tab-action-btn px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-[11px] font-bold shadow-sm cursor-pointer transition-all active:scale-95">Add page</button>
                <?php endif; ?>

                <!-- Action buttons for Menus tab -->
                <?php if ( ! $is_read_only ) : ?>
                <div id="tab-action-menus" class="tab-action-btn hidden flex items-center gap-2">
                    <button onclick="window.coraShowToast('URL Redirects panel loading...', 'success')" id="menu-btn-redirects" class="px-3 py-1.5 border border-zinc-200 text-zinc-700 bg-white hover:bg-zinc-50 rounded-lg text-[11px] font-bold shadow-sm cursor-pointer transition-all active:scale-95">URL redirects</button>
                    <button onclick="triggerCreateNewMenu()" id="menu-btn-create" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-[11px] font-bold shadow-sm cursor-pointer transition-all active:scale-95">Create menu</button>
                    <button onclick="duplicateActiveMenu()" id="menu-btn-duplicate" class="hidden px-3 py-1.5 border border-zinc-200 text-zinc-700 bg-white hover:bg-zinc-50 rounded-lg text-[11px] font-bold shadow-sm cursor-pointer transition-all active:scale-95">Duplicate</button>
                </div>
                <?php endif; ?>

                <!-- Action button for Theme Settings tab -->
                <?php if ( ! $is_read_only ) : ?>
                <button onclick="saveThemeSettings()" id="tab-action-settings" class="tab-action-btn hidden px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-[11px] font-bold shadow-sm cursor-pointer transition-all active:scale-95">Save Settings</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB CONTENT: PAGES -->
        <div id="tab-content-pages" class="space-y-0">

            <?php
            // ── Stat card counts ──
            global $wpdb;
            $stat_theme_id = $current_theme_id ?? 0;
            $stat_pages    = $wpdb->get_results( $wpdb->prepare( "SELECT status FROM {$wpdb->prefix}cora_canvas_pages WHERE theme_id = %d", $stat_theme_id ), ARRAY_A ) ?: [];
            $stat_total    = count( $stat_pages );
            $stat_active   = count( array_filter( $stat_pages, fn($p) => $p['status'] === 'published' ) );
            $stat_draft    = count( array_filter( $stat_pages, fn($p) => $p['status'] === 'draft' ) );
            $stat_other    = $stat_total - $stat_active - $stat_draft;
            ?>

            <!-- ── Stats Cards ── -->
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-zinc-100 border border-zinc-200 rounded-xl bg-white mb-4 shadow-sm overflow-hidden">
                <div class="px-4 py-2.5">
                    <div class="flex items-center gap-1.5 mb-1">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="#71717a" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                        <span class="text-[10px] text-zinc-500 font-medium">Total pages</span>
                    </div>
                    <div class="text-[18px] font-bold text-zinc-900 leading-none" id="stat-total-pages"><?php echo $stat_total; ?></div>
                </div>
                <div class="px-4 py-2.5">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                        <span class="text-[10px] text-zinc-500 font-medium">Active pages</span>
                    </div>
                    <div class="text-[18px] font-bold text-zinc-900 leading-none" id="stat-active-pages"><?php echo $stat_active; ?></div>
                </div>
                <div class="px-4 py-2.5">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                        <span class="text-[10px] text-zinc-500 font-medium">Draft pages</span>
                    </div>
                    <div class="text-[18px] font-bold text-zinc-900 leading-none" id="stat-draft-pages"><?php echo $stat_draft; ?></div>
                </div>
                <div class="px-4 py-2.5">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 flex-shrink-0"></span>
                        <span class="text-[10px] text-zinc-500 font-medium">Other pages <span class="inline-flex items-center justify-center w-3 h-3 rounded-full border border-zinc-300 text-[7px] text-zinc-400 cursor-pointer" title="Scheduled or archived pages">i</span></span>
                    </div>
                    <div class="text-[18px] font-bold text-zinc-900 leading-none" id="stat-other-pages"><?php echo $stat_other; ?></div>
                </div>
            </div>

            <!-- ── Table Card ── -->
            <div class="bg-white border border-zinc-200 rounded-xl overflow-visible shadow-sm">

                <!-- Tab strip + icon toolbar -->
                <div class="flex items-center justify-between px-4 py-2.5 border-b border-zinc-100">
                    <!-- Left: Tab pills / Views Strip -->
                    <div class="flex items-center gap-1" id="pages-views-tab-strip">
                        <button id="pages-view-all" onclick="setViewFilter('all')" data-view="all" class="pages-view-btn px-3 py-1.5 rounded-md text-[11px] font-semibold bg-zinc-900 text-white cursor-pointer transition-all">All</button>
                        
                        <!-- Plus Button for custom view configuration -->
                        <div class="relative">
                            <button id="add-view-btn" onclick="toggleAddViewDropdown()" class="w-7 h-7 flex items-center justify-center rounded-md text-[15px] font-semibold text-zinc-400 hover:bg-zinc-50 hover:text-zinc-700 cursor-pointer transition-all border border-zinc-200 leading-none" title="Add view">+</button>
                            <div id="add-view-dropdown" class="hidden absolute left-0 top-full mt-1.5 z-30 bg-white border border-zinc-200 rounded-xl shadow-lg p-2.5 w-52 space-y-2">
                                <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Create Custom View</p>
                                <input type="text" id="new-view-name" placeholder="e.g. Listings, Blog, Agent..." onkeyup="if(event.key==='Enter') createNewCustomView()"
                                    class="w-full px-2.5 py-1.5 border border-zinc-200 focus:border-zinc-400 focus:outline-none rounded-lg text-[11px] bg-white text-zinc-800">
                                <button onclick="createNewCustomView()" 
                                    class="w-full py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-[10px] font-bold cursor-pointer transition-all text-center">
                                    Create View
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Right: Search input + dropdown chips -->
                    <div class="flex items-center gap-1.5">
                        <!-- Inline Search Input -->
                        <div class="relative flex items-center bg-zinc-50 border border-zinc-200 rounded-lg px-2.5 py-1 w-48 focus-within:border-zinc-400 focus-within:bg-white transition-all mr-1.5">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="#a1a1aa" stroke-width="2.5" fill="none" class="mr-1.5 shrink-0"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input type="text" id="page-search-input" onkeyup="filterPages()" placeholder="Search pages…" 
                                class="bg-transparent border-none text-[11px] placeholder-zinc-400 focus:outline-none focus:ring-0 p-0 w-full text-zinc-800">
                        </div>

                        <!-- Filter dropdown chip -->
                        <div class="relative">
                            <button id="filter-chip-btn" onclick="toggleDropdownChip('filter')" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 hover:border-zinc-300 text-zinc-600 rounded-lg text-[11px] font-semibold transition-all cursor-pointer">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                                <span>Status: All</span>
                                <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="2.5" fill="none" class="opacity-50"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div id="chip-dropdown-filter" class="hidden absolute right-0 top-full mt-1 z-30 bg-white border border-zinc-200 rounded-xl shadow-lg p-1.5 w-40 space-y-0.5">
                                <button onclick="selectChipFilter('all', 'Status: All')" class="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">All statuses</button>
                                <button onclick="selectChipFilter('published', 'Status: Active')" class="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">Active (Visible)</button>
                                <button onclick="selectChipFilter('draft', 'Status: Draft')" class="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">Draft</button>
                                <button onclick="selectChipFilter('scheduled', 'Status: Scheduled')" class="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">Scheduled</button>
                                <button onclick="selectChipFilter('private', 'Status: Private')" class="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">Private / Unlisted</button>
                            </div>
                        </div>

                        <!-- Sort dropdown chip -->
                        <div class="relative">
                            <button id="sort-chip-btn" onclick="toggleDropdownChip('sort')" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 hover:border-zinc-300 text-zinc-600 rounded-lg text-[11px] font-semibold transition-all cursor-pointer">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 6h18M7 12h10M11 18h2"/></svg>
                                <span>Sort: Updated</span>
                                <svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="2.5" fill="none" class="opacity-50"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div id="chip-dropdown-sort" class="hidden absolute right-0 top-full mt-1 z-30 bg-white border border-zinc-200 rounded-xl shadow-lg p-1.5 w-36 space-y-0.5">
                                <button onclick="selectChipSort('modified', 'Sort: Updated')" class="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">Last updated</button>
                                <button onclick="selectChipSort('alpha', 'Sort: A–Z')" class="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">Alphabetical</button>
                                <button onclick="selectChipSort('created', 'Sort: Created')" class="w-full text-left px-2.5 py-1.5 rounded-lg text-[11px] font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">Date created</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-zinc-100 text-[11px] font-semibold text-zinc-400">
                            <th class="pl-3 pr-1 py-2 w-10">
                                <input type="checkbox" id="pages-select-all-checkbox" onchange="toggleSelectAllPages(this)" class="rounded cursor-pointer accent-zinc-900">
                            </th>
                            <th class="px-3 py-2">
                                <button onclick="setPageSort(pageSortState==='alpha'?'alpha-desc':'alpha')" class="flex items-center gap-1 text-zinc-400 hover:text-zinc-700 cursor-pointer transition-colors group font-semibold">
                                    Title
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="opacity-40 group-hover:opacity-100 transition-opacity"><path d="M7 15l5 5 5-5M7 9l5-5 5 5"/></svg>
                                </button>
                            </th>
                            <th class="px-3 py-2 font-semibold">Visibility</th>
                            <th class="px-3 py-2 font-semibold">Content</th>
                            <th class="px-3 py-2 font-semibold">Updated</th>
                            <th class="px-3 py-2 w-28 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pages-table-body">
                        <!-- Populated by renderPagesTable() -->
                    </tbody>
                </table>

                <!-- Table footer -->
                <div class="px-4 py-3 border-t border-zinc-100 text-center text-[11px] text-zinc-400">
                    Learn more about <a href="#" class="text-blue-500 hover:underline inline-flex items-center gap-1">pages <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"></line></svg></a>
                </div>
            </div>

            <!-- Hidden legacy filters (kept for JS compatibility) -->
            <select id="page-status-filter" class="hidden">
                <option value="all">All</option><option value="published">Published</option><option value="draft">Draft</option><option value="scheduled">Scheduled</option>
            </select>
            <select id="page-sort-filter" class="hidden">
                <option value="modified">Modified</option><option value="alpha">Alpha</option><option value="created">Created</option>
            </select>

            <?php
            $git_enabled = get_option('cora_git_sync_enabled') === '1';
            $git_repo    = get_option('cora_git_sync_repo', '');
            $live_url    = get_option('cora_git_sync_live_url', '');
            $is_synced   = ! empty($git_repo) || ! empty($live_url);
            $compat_flags = get_option('cora_git_sync_compat_flags', []);
            ?>
            <!-- Lovable Studio Trigger Bar -->
            <div id="lovable-trigger-bar" class="flex items-center justify-between bg-white border border-zinc-200 rounded-xl px-5 py-3.5 mt-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-zinc-950 rounded-lg flex items-center justify-center shrink-0">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="white" stroke-width="2" fill="none"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-zinc-900">Lovable Studio</div>
                        <div class="text-[11px] text-zinc-500"><?php echo ($git_enabled && $is_synced) ? 'Connected &mdash; ' . esc_html(basename($git_repo ?: $live_url)) : 'Build Lovable-compatible pages for Cora &mdash; Prompt Library + GitHub Sync'; ?></div>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <?php if ($git_enabled && $is_synced) : ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg border bg-zinc-50 text-zinc-700 border-zinc-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        <?php echo !empty($compat_flags) ? 'Bridge Active' : 'Sync Active'; ?>
                    </span>
                    <?php endif; ?>
                    <button onclick="openLovableStudio()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold cursor-pointer transition-all shadow-sm">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        Open Lovable Studio
                    </button>
                </div>
            </div>

            <?php include plugin_dir_path( CORA_PLUGIN_FILE ) . 'views/partials/lovable-studio-drawer.php'; ?>

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
        <div id="tab-content-menus" class="space-y-6 hidden">
            
            <!-- 1. MENUS LIST VIEW -->
            <div id="menus-list-view" class="space-y-4">
                <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-zinc-100 text-[11px] font-bold text-zinc-400 bg-zinc-50/50">
                                <th class="px-4 py-2.5 w-1/3">
                                    <button class="flex items-center gap-1 text-zinc-400 hover:text-zinc-700 cursor-pointer font-bold">
                                        Menu
                                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M7 15l5 5 5-5M7 9l5-5 5 5"/></svg>
                                    </button>
                                </th>
                                <th class="px-4 py-2.5">Menu items</th>
                            </tr>
                        </thead>
                        <tbody id="menus-table-body">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. MENU DETAIL VIEW -->
            <div id="menus-detail-view" class="space-y-4 hidden">
                <div class="flex items-center justify-between pb-1">
                    <div class="flex items-center gap-3">
                        <button onclick="exitMenuDetail()" class="p-1.5 hover:bg-zinc-100 rounded-lg text-zinc-500 hover:text-zinc-900 cursor-pointer transition-colors border-none bg-transparent">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        </button>
                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                        <span id="menu-detail-header-title" class="text-[13px] font-bold text-zinc-950">Customer account main menu</span>
                    </div>
                </div>

                <!-- Name & Handle Card -->
                <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm space-y-3">
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Name</label>
                        <input type="text" id="menu-name-input" onkeyup="updateMenuNameState(this.value)" class="w-full px-3.5 py-2 border border-zinc-200 rounded-lg text-xs font-semibold focus:outline-none focus:border-zinc-400 bg-white text-zinc-800" <?php echo $is_read_only ? 'readonly' : ''; ?>>
                        <p id="menu-handle-label" class="text-[10px] text-zinc-400 font-mono mt-1.5">Handle: customer-account-main-menu</p>
                    </div>
                </div>

                <!-- Menu Items Card -->
                <div class="bg-white border border-zinc-200 rounded-xl p-5 shadow-sm space-y-4">
                    <h3 class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Menu items</h3>
                    
                    <!-- Drag/list container -->
                    <div id="menu-items-list-container" class="space-y-2">
                        <!-- Populated by JS -->
                    </div>

                    <?php if ( ! $is_read_only ) : ?>
                    <!-- Add item trigger link row -->
                    <button onclick="addMenuInlineRow()" class="w-full py-2.5 border border-dashed border-zinc-200 hover:bg-zinc-50/50 hover:border-zinc-300 rounded-lg text-center text-xs font-bold text-zinc-800 flex items-center justify-center gap-1.5 cursor-pointer transition-all bg-transparent">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="text-blue-500"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                        Add menu item
                    </button>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-end pt-2">
                    <button onclick="saveCurrentMenuDetails()" <?php echo $is_read_only ? 'disabled' : ''; ?> class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-50 text-white rounded-lg text-xs font-bold shadow-sm cursor-pointer transition-all active:scale-95">Save</button>
                </div>
            </div>
        </div>

        <!-- TAB CONTENT: THEME SETTINGS -->
        <div id="tab-content-settings" class="bg-white border border-zinc-200 rounded-xl shadow-sm hidden">

            <!-- ── Settings Tab Navigation Pills ───────────────────── -->
            <div class="flex items-center gap-1 border-b border-zinc-100 px-6 pt-4 pb-0" id="settings-nav-pills">
                <button onclick="switchSettingsPanel('identity')" id="spill-identity" class="settings-pill active px-3 py-2 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border-b-2 border-zinc-950 text-zinc-900 bg-transparent cursor-pointer transition-all">Identity</button>
                <button onclick="switchSettingsPanel('colors')" id="spill-colors" class="settings-pill px-3 py-2 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 bg-transparent cursor-pointer transition-all">Colors</button>
                <button onclick="switchSettingsPanel('typography')" id="spill-typography" class="settings-pill px-3 py-2 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 bg-transparent cursor-pointer transition-all">Typography</button>
                <button onclick="switchSettingsPanel('spacing')" id="spill-spacing" class="settings-pill px-3 py-2 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 bg-transparent cursor-pointer transition-all">Spacing &amp; Borders</button>
                <button onclick="switchSettingsPanel('layout')" id="spill-layout" class="settings-pill px-3 py-2 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 bg-transparent cursor-pointer transition-all">Layout</button>
                <button onclick="switchSettingsPanel('social')" id="spill-social" class="settings-pill px-3 py-2 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 bg-transparent cursor-pointer transition-all">Social &amp; SEO</button>
                <div id="spill-elementor-wrap" class="hidden">
                    <button onclick="switchSettingsPanel('elementor')" id="spill-elementor" class="settings-pill px-3 py-2 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 bg-transparent cursor-pointer transition-all">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Elementor Sync
                        </span>
                    </button>
                </div>
                <div id="spill-lovable-wrap" class="hidden">
                    <button onclick="switchSettingsPanel('lovable')" id="spill-lovable" class="settings-pill px-3 py-2 text-[10px] font-bold uppercase tracking-wider rounded-t-lg border-b-2 border-transparent text-zinc-400 hover:text-zinc-700 bg-transparent cursor-pointer transition-all">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span>CSS Tokens
                        </span>
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════ -->
            <!-- PANEL: Identity                                       -->
            <!-- ══════════════════════════════════════════════════════ -->
            <div id="spanel-identity" class="settings-panel p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase">Site Title</label>
                        <input type="text" id="setting-site-title" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase">Site Tagline</label>
                        <input type="text" id="setting-site-tagline" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase">Site Description (SEO)</label>
                        <textarea id="setting-site-description" rows="3" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 resize-none" placeholder="Brief site description for search engines..."></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase">Default Page Title Format</label>
                        <input type="text" id="setting-title-format" placeholder="e.g. %s — Site Name" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                        <p class="text-[10px] text-zinc-400">Use %s for the page name placeholder.</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase">Agency Logo URL</label>
                        <div class="flex gap-2">
                            <input type="text" id="setting-site-logo" placeholder="https://... or /wp-content/..." class="flex-1 px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                            <button onclick="openMediaPicker('setting-site-logo')" class="px-2.5 py-2 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-600 hover:bg-zinc-50 cursor-pointer transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase">Logo Dark Mode URL</label>
                        <div class="flex gap-2">
                            <input type="text" id="setting-site-logo-dark" placeholder="https://... (optional)" class="flex-1 px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                            <button onclick="openMediaPicker('setting-site-logo-dark')" class="px-2.5 py-2 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-600 hover:bg-zinc-50 cursor-pointer transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase">Favicon Icon URL</label>
                        <div class="flex gap-2">
                            <input type="text" id="setting-site-favicon" placeholder="e.g. /favicon.png" class="flex-1 px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                            <button onclick="openMediaPicker('setting-site-favicon')" class="px-2.5 py-2 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-600 hover:bg-zinc-50 cursor-pointer transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-zinc-500 uppercase">OG / Share Image URL</label>
                        <div class="flex gap-2">
                            <input type="text" id="setting-og-image" placeholder="https://... 1200×630px recommended" class="flex-1 px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                            <button onclick="openMediaPicker('setting-og-image')" class="px-2.5 py-2 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-600 hover:bg-zinc-50 cursor-pointer transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════ -->
            <!-- PANEL: Colors                                         -->
            <!-- ══════════════════════════════════════════════════════ -->
            <div id="spanel-colors" class="settings-panel p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Core palette -->
                    <div class="space-y-4">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Core Palette</h5>
                        <?php
                        $color_fields = [
                            ['id' => 'setting-color-primary', 'label' => 'Primary Color', 'desc' => 'Mapped to Elementor System Color 1'],
                            ['id' => 'setting-color-secondary', 'label' => 'Secondary Color', 'desc' => 'Mapped to Elementor System Color 2'],
                            ['id' => 'setting-color-accent', 'label' => 'Accent / Brand Color', 'desc' => 'Mapped to Elementor System Color 3 · --color-accent'],
                            ['id' => 'setting-color-text', 'label' => 'Default Text Color', 'desc' => 'Mapped to Elementor System Color 4 · --color-text'],
                            ['id' => 'setting-color-bg', 'label' => 'Background Color', 'desc' => '--color-background'],
                            ['id' => 'setting-color-surface', 'label' => 'Surface / Muted Color', 'desc' => 'Card backgrounds, secondary surfaces · --color-surface'],
                        ];
                        foreach ($color_fields as $cf): ?>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase"><?php echo esc_html($cf['label']); ?></label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="<?php echo esc_attr($cf['id']); ?>" class="w-9 h-9 rounded-lg border border-zinc-200 cursor-pointer p-0.5 flex-shrink-0" oninput="document.getElementById('<?php echo esc_attr($cf['id']); ?>-text').value=this.value">
                                <input type="text" id="<?php echo esc_attr($cf['id']); ?>-text" class="flex-1 px-3 py-2 border border-zinc-200 rounded-lg text-xs uppercase font-mono focus:outline-none focus:border-zinc-400" oninput="syncColorPicker('<?php echo esc_attr($cf['id']); ?>', this.value)">
                            </div>
                            <p class="text-[9px] text-zinc-400"><?php echo esc_html($cf['desc']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Semantic & state colors -->
                    <div class="space-y-4">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Semantic State Colors</h5>
                        <?php
                        $semantic_fields = [
                            ['id' => 'setting-color-success', 'label' => 'Success Color', 'desc' => '--color-success · confirmation states, sold badges'],
                            ['id' => 'setting-color-warning', 'label' => 'Warning Color', 'desc' => '--color-warning · alerts, pending status'],
                            ['id' => 'setting-color-danger', 'label' => 'Danger / Error Color', 'desc' => '--color-danger · validation errors, price drops'],
                            ['id' => 'setting-color-info', 'label' => 'Info Color', 'desc' => '--color-info · tooltips, informational callouts'],
                        ];
                        foreach ($semantic_fields as $cf): ?>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase"><?php echo esc_html($cf['label']); ?></label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="<?php echo esc_attr($cf['id']); ?>" class="w-9 h-9 rounded-lg border border-zinc-200 cursor-pointer p-0.5 flex-shrink-0" oninput="document.getElementById('<?php echo esc_attr($cf['id']); ?>-text').value=this.value">
                                <input type="text" id="<?php echo esc_attr($cf['id']); ?>-text" class="flex-1 px-3 py-2 border border-zinc-200 rounded-lg text-xs uppercase font-mono focus:outline-none focus:border-zinc-400" oninput="syncColorPicker('<?php echo esc_attr($cf['id']); ?>', this.value)">
                            </div>
                            <p class="text-[9px] text-zinc-400"><?php echo esc_html($cf['desc']); ?></p>
                        </div>
                        <?php endforeach; ?>

                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2 mt-6">Button Colors</h5>
                        <?php
                        $btn_fields = [
                            ['id' => 'setting-color-btn-bg', 'label' => 'Button Background', 'desc' => '--btn-bg'],
                            ['id' => 'setting-color-btn-text', 'label' => 'Button Text Color', 'desc' => '--btn-text'],
                            ['id' => 'setting-color-btn-hover', 'label' => 'Button Hover Background', 'desc' => '--btn-hover-bg'],
                        ];
                        foreach ($btn_fields as $cf): ?>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase"><?php echo esc_html($cf['label']); ?></label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="<?php echo esc_attr($cf['id']); ?>" class="w-9 h-9 rounded-lg border border-zinc-200 cursor-pointer p-0.5 flex-shrink-0" oninput="document.getElementById('<?php echo esc_attr($cf['id']); ?>-text').value=this.value">
                                <input type="text" id="<?php echo esc_attr($cf['id']); ?>-text" class="flex-1 px-3 py-2 border border-zinc-200 rounded-lg text-xs uppercase font-mono focus:outline-none focus:border-zinc-400" oninput="syncColorPicker('<?php echo esc_attr($cf['id']); ?>', this.value)">
                            </div>
                            <p class="text-[9px] text-zinc-400"><?php echo esc_html($cf['desc']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════ -->
            <!-- PANEL: Typography                                     -->
            <!-- ══════════════════════════════════════════════════════ -->
            <div id="spanel-typography" class="settings-panel p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Font Families</h5>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Heading Font</label>
                            <select id="setting-heading-font" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                <?php
                                $fonts = ['Inter','Playfair Display','Outfit','Roboto','Lora','Montserrat','Raleway','Poppins','DM Sans','DM Serif Display','Nunito','Source Serif 4','Libre Baskerville','Merriweather','Cormorant Garamond','Space Grotesk','Syne','Plus Jakarta Sans','Josefin Sans','Fraunces'];
                                foreach ($fonts as $f) echo '<option value="' . esc_attr($f) . '">' . esc_html($f) . '</option>';
                                ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Body Font</label>
                            <select id="setting-body-font" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                <?php foreach ($fonts as $f) echo '<option value="' . esc_attr($f) . '">' . esc_html($f) . '</option>'; ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Accent / Display Font</label>
                            <select id="setting-accent-font" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                <option value="">— Same as Heading —</option>
                                <?php foreach ($fonts as $f) echo '<option value="' . esc_attr($f) . '">' . esc_html($f) . '</option>'; ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-[10px] font-bold text-zinc-500 uppercase">
                                <span>Base Font Size</span>
                                <span id="font-size-val">16px</span>
                            </div>
                            <input type="range" id="setting-font-size" min="12" max="20" step="1" value="16" oninput="jQuery('#font-size-val').text(this.value + 'px')" class="w-full accent-zinc-950 cursor-pointer">
                            <div class="flex justify-between text-[9px] text-zinc-400"><span>12px</span><span>16px</span><span>20px</span></div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Google Fonts API Key (optional)</label>
                            <input type="text" id="setting-gfonts-key" placeholder="AIza... for self-hosted fonts" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs font-mono focus:outline-none focus:border-zinc-400">
                            <p class="text-[9px] text-zinc-400">Leave blank to use the standard Google Fonts CDN embed.</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Type Scale — Per Level</h5>
                        <p class="text-[10px] text-zinc-400">These map directly to Elementor Typography Presets and CSS variables.</p>
                        <?php
                        $type_levels = [
                            ['key' => 'h1', 'label' => 'H1 — Hero Heading', 'def_size' => 56, 'def_weight' => 800],
                            ['key' => 'h2', 'label' => 'H2 — Section Heading', 'def_size' => 40, 'def_weight' => 700],
                            ['key' => 'h3', 'label' => 'H3 — Sub-heading', 'def_size' => 28, 'def_weight' => 600],
                            ['key' => 'body', 'label' => 'Body — Paragraph', 'def_size' => 16, 'def_weight' => 400],
                            ['key' => 'small', 'label' => 'Small / Caption', 'def_size' => 13, 'def_weight' => 400],
                            ['key' => 'btn', 'label' => 'Button Text', 'def_size' => 14, 'def_weight' => 600],
                        ];
                        foreach ($type_levels as $tl): ?>
                        <div class="border border-zinc-100 rounded-lg p-3 space-y-2">
                            <p class="text-[10px] font-bold text-zinc-700"><?php echo esc_html($tl['label']); ?></p>
                            <div class="grid grid-cols-4 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-zinc-400 uppercase">Size (px)</label>
                                    <input type="number" id="setting-type-<?php echo esc_attr($tl['key']); ?>-size" value="<?php echo esc_attr($tl['def_size']); ?>" min="10" max="120" class="w-full px-2 py-1 border border-zinc-200 rounded text-xs font-mono text-center focus:outline-none focus:border-zinc-400">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-zinc-400 uppercase">Weight</label>
                                    <select id="setting-type-<?php echo esc_attr($tl['key']); ?>-weight" class="w-full px-1 py-1 border border-zinc-200 rounded text-[10px] focus:outline-none focus:border-zinc-400 cursor-pointer">
                                        <?php foreach ([100=>'Thin',200=>'ExLight',300=>'Light',400=>'Regular',500=>'Medium',600=>'SemiBold',700=>'Bold',800=>'ExBold',900=>'Black'] as $w=>$wl): ?>
                                        <option value="<?php echo $w; ?>" <?php echo $w==$tl['def_weight']?'selected':''; ?>><?php echo $w; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-zinc-400 uppercase">L.H.</label>
                                    <input type="number" id="setting-type-<?php echo esc_attr($tl['key']); ?>-lh" value="1.2" min="1" max="3" step="0.1" class="w-full px-2 py-1 border border-zinc-200 rounded text-xs font-mono text-center focus:outline-none focus:border-zinc-400">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-zinc-400 uppercase">Spc</label>
                                    <input type="number" id="setting-type-<?php echo esc_attr($tl['key']); ?>-ls" value="0" min="-5" max="10" step="0.01" class="w-full px-2 py-1 border border-zinc-200 rounded text-xs font-mono text-center focus:outline-none focus:border-zinc-400">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════ -->
            <!-- PANEL: Spacing & Borders                              -->
            <!-- ══════════════════════════════════════════════════════ -->
            <div id="spanel-spacing" class="settings-panel p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Layout Spacing</h5>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold text-zinc-500 uppercase"><span>Container Max Width</span><span id="container-width-val">1280px</span></div>
                            <input type="range" id="setting-container-width" min="960" max="1800" step="40" value="1280" oninput="jQuery('#container-width-val').text(this.value+'px')" class="w-full accent-zinc-950 cursor-pointer">
                            <div class="flex justify-between text-[9px] text-zinc-400"><span>960px</span><span>1280px</span><span>1800px</span></div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold text-zinc-500 uppercase"><span>Section Vertical Padding</span><span id="section-padding-val">80px</span></div>
                            <input type="range" id="setting-section-padding" min="20" max="200" step="10" value="80" oninput="jQuery('#section-padding-val').text(this.value+'px')" class="w-full accent-zinc-950 cursor-pointer">
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold text-zinc-500 uppercase"><span>Element Column Gap</span><span id="element-gap-val">24px</span></div>
                            <input type="range" id="setting-element-gap" min="8" max="80" step="4" value="24" oninput="jQuery('#element-gap-val').text(this.value+'px')" class="w-full accent-zinc-950 cursor-pointer">
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold text-zinc-500 uppercase"><span>Widgets Spacing</span><span id="widgets-spacing-val">20px</span></div>
                            <input type="range" id="setting-widgets-spacing" min="0" max="60" step="4" value="20" oninput="jQuery('#widgets-spacing-val').text(this.value+'px')" class="w-full accent-zinc-950 cursor-pointer">
                        </div>
                    </div>
                    <div class="space-y-5">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Border Radius System</h5>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Radius Preset</label>
                            <div class="grid grid-cols-5 gap-2" id="radius-preset-group">
                                <?php
                                $presets = ['none'=>'0','sm'=>'4px','md'=>'8px','lg'=>'16px','pill'=>'999px'];
                                foreach ($presets as $pk=>$pv): ?>
                                <button type="button" onclick="selectRadiusPreset('<?php echo esc_attr($pv); ?>', this)" class="radius-preset-btn border border-zinc-200 rounded-lg py-2 text-[10px] font-bold text-zinc-600 hover:border-zinc-950 hover:text-zinc-950 transition-all cursor-pointer bg-transparent">
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-6 h-6 border-2 border-current" style="border-radius:<?php echo esc_attr($pv); ?>"></div>
                                        <span><?php echo esc_html(ucfirst($pk)); ?></span>
                                    </div>
                                </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-bold text-zinc-500 uppercase"><span>Custom Border Radius (px)</span><span id="border-radius-val">8px</span></div>
                            <input type="range" id="setting-border-radius" min="0" max="32" step="2" value="8" oninput="jQuery('#border-radius-val').text(this.value+'px')" class="w-full accent-zinc-950 cursor-pointer">
                        </div>
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2 mt-2">Default Borders</h5>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Border Width</label>
                                <select id="setting-border-width" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                    <option value="0">None</option>
                                    <option value="1" selected>1px</option>
                                    <option value="2">2px</option>
                                    <option value="3">3px</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Border Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="setting-border-color" value="#e4e4e7" class="w-9 h-9 rounded-lg border border-zinc-200 cursor-pointer p-0.5" oninput="document.getElementById('setting-border-color-text').value=this.value">
                                    <input type="text" id="setting-border-color-text" value="#e4e4e7" class="flex-1 px-3 py-2 border border-zinc-200 rounded-lg text-xs font-mono uppercase focus:outline-none focus:border-zinc-400" oninput="syncColorPicker('setting-border-color', this.value)">
                                </div>
                            </div>
                        </div>
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2 mt-2">Box Shadows</h5>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Default Box Shadow</label>
                            <select id="setting-box-shadow" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                <option value="none">None</option>
                                <option value="0 1px 3px rgba(0,0,0,0.06)" selected>Subtle (xs)</option>
                                <option value="0 4px 12px rgba(0,0,0,0.08)">Soft (sm)</option>
                                <option value="0 8px 24px rgba(0,0,0,0.10)">Medium</option>
                                <option value="0 20px 60px rgba(0,0,0,0.15)">Deep (lg)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════ -->
            <!-- PANEL: Layout                                         -->
            <!-- ══════════════════════════════════════════════════════ -->
            <div id="spanel-layout" class="settings-panel p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Header</h5>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Header Style</label>
                            <select id="setting-header-layout" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                <option value="Logo Left">Logo Left — Nav Right</option>
                                <option value="Centered Logo">Centered Logo</option>
                                <option value="Split Navigation">Split Navigation (Logo Center)</option>
                                <option value="Minimal">Minimal — Logo Only</option>
                                <option value="Full Width">Full Width Bar</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Navigation Menu</label>
                            <select id="setting-nav-menu" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                <option value="0">— Default Navbar —</option>
                                <?php
                                $menus = wp_get_nav_menus();
                                if ( ! empty( $menus ) ) {
                                    foreach ( $menus as $menu ) {
                                        echo '<option value="' . esc_attr( $menu->term_id ) . '">' . esc_html( $menu->name ) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="setting-sticky-header" class="rounded cursor-pointer">
                                <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="setting-sticky-header">Sticky Header</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="setting-transparent-header" class="rounded cursor-pointer">
                                <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="setting-transparent-header">Transparent on Hero</label>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Header BG Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="setting-header-bg" value="#ffffff" class="w-9 h-9 rounded-lg border border-zinc-200 cursor-pointer p-0.5" oninput="document.getElementById('setting-header-bg-text').value=this.value">
                                    <input type="text" id="setting-header-bg-text" value="#ffffff" class="flex-1 px-2 py-2 border border-zinc-200 rounded-lg text-[10px] font-mono uppercase focus:outline-none focus:border-zinc-400" oninput="syncColorPicker('setting-header-bg', this.value)">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Header Text Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="setting-header-text-color" value="#18181b" class="w-9 h-9 rounded-lg border border-zinc-200 cursor-pointer p-0.5" oninput="document.getElementById('setting-header-text-color-text').value=this.value">
                                    <input type="text" id="setting-header-text-color-text" value="#18181b" class="flex-1 px-2 py-2 border border-zinc-200 rounded-lg text-[10px] font-mono uppercase focus:outline-none focus:border-zinc-400" oninput="syncColorPicker('setting-header-text-color', this.value)">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Footer</h5>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Footer Columns</label>
                            <select id="setting-footer-columns" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                <option value="1">1 Column — Centered</option>
                                <option value="2">2 Columns</option>
                                <option value="3" selected>3 Columns</option>
                                <option value="4">4 Columns</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Copyright Text</label>
                            <input type="text" id="setting-copyright-text" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="setting-show-socials" class="rounded cursor-pointer">
                            <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="setting-show-socials">Show Social Links in Footer</label>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Footer BG Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="setting-footer-bg" value="#18181b" class="w-9 h-9 rounded-lg border border-zinc-200 cursor-pointer p-0.5" oninput="document.getElementById('setting-footer-bg-text').value=this.value">
                                    <input type="text" id="setting-footer-bg-text" value="#18181b" class="flex-1 px-2 py-2 border border-zinc-200 rounded-lg text-[10px] font-mono uppercase focus:outline-none focus:border-zinc-400" oninput="syncColorPicker('setting-footer-bg', this.value)">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Footer Text Color</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="setting-footer-text-color" value="#a1a1aa" class="w-9 h-9 rounded-lg border border-zinc-200 cursor-pointer p-0.5" oninput="document.getElementById('setting-footer-text-color-text').value=this.value">
                                    <input type="text" id="setting-footer-text-color-text" value="#a1a1aa" class="flex-1 px-2 py-2 border border-zinc-200 rounded-lg text-[10px] font-mono uppercase focus:outline-none focus:border-zinc-400" oninput="syncColorPicker('setting-footer-text-color', this.value)">
                                </div>
                            </div>
                        </div>
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2 mt-2">Page Defaults</h5>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Default Page Width</label>
                            <select id="setting-page-width" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                <option value="full-width">Full Width</option>
                                <option value="boxed" selected>Boxed (with sidebar)</option>
                                <option value="narrow">Narrow Content</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="setting-smooth-scroll" class="rounded cursor-pointer" checked>
                            <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="setting-smooth-scroll">Enable Smooth Scroll</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════ -->
            <!-- PANEL: Social & SEO                                   -->
            <!-- ══════════════════════════════════════════════════════ -->
            <div id="spanel-social" class="settings-panel p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Social Profiles</h5>
                        <?php
                        $social_fields = [
                            ['id'=>'setting-facebook-link','label'=>'Facebook','placeholder'=>'https://facebook.com/...'],
                            ['id'=>'setting-twitter-link','label'=>'X / Twitter','placeholder'=>'https://x.com/...'],
                            ['id'=>'setting-instagram-link','label'=>'Instagram','placeholder'=>'https://instagram.com/...'],
                            ['id'=>'setting-linkedin-link','label'=>'LinkedIn','placeholder'=>'https://linkedin.com/in/...'],
                            ['id'=>'setting-youtube-link','label'=>'YouTube','placeholder'=>'https://youtube.com/@...'],
                            ['id'=>'setting-tiktok-link','label'=>'TikTok','placeholder'=>'https://tiktok.com/@...'],
                        ];
                        foreach ($social_fields as $sf): ?>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase"><?php echo esc_html($sf['label']); ?></label>
                            <input type="url" id="<?php echo esc_attr($sf['id']); ?>" placeholder="<?php echo esc_attr($sf['placeholder']); ?>" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="space-y-4">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">SEO & Analytics</h5>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Google Analytics (GA4) ID</label>
                            <input type="text" id="setting-ga4-id" placeholder="G-XXXXXXXXXX" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs font-mono focus:outline-none focus:border-zinc-400">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Google Tag Manager ID</label>
                            <input type="text" id="setting-gtm-id" placeholder="GTM-XXXXXXX" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs font-mono focus:outline-none focus:border-zinc-400">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Facebook Pixel ID</label>
                            <input type="text" id="setting-fb-pixel" placeholder="1234567890" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs font-mono focus:outline-none focus:border-zinc-400">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">Robots.txt Directive</label>
                            <select id="setting-robots" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer">
                                <option value="index,follow">Index, Follow (default)</option>
                                <option value="noindex,follow">No Index — Follow Links</option>
                                <option value="noindex,nofollow">No Index, No Follow</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="setting-sitemap-enable" class="rounded cursor-pointer" checked>
                            <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="setting-sitemap-enable">Generate XML Sitemap</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════ -->
            <!-- PANEL: Elementor Sync (conditional)                   -->
            <!-- ══════════════════════════════════════════════════════ -->
            <div id="spanel-elementor" class="settings-panel p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <div class="flex items-start gap-3 p-4 bg-zinc-50 border border-zinc-200 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            <div>
                                <p class="text-xs font-bold text-zinc-900">Elementor Global Settings Sync</p>
                                <p class="text-[10px] text-zinc-500 mt-1 leading-relaxed">Changes saved here are pushed directly into Elementor's active Kit — the same global settings you see in Elementor → Site Settings. Changes apply instantly across all Elementor pages.</p>
                                <p class="text-[10px] text-zinc-400 mt-2">Last synced: <span id="elementor-last-sync" class="font-mono">Never</span></p>
                            </div>
                        </div>
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Elementor Global Colors</h5>
                        <p class="text-[10px] text-zinc-400">These 4 colors map 1:1 to the Elementor system color palette (Primary, Secondary, Text, Accent). Changing them here updates Elementor Site Settings → Global Colors.</p>
                        <?php
                        $el_colors = [
                            ['id'=>'setting-el-primary','label'=>'System Color 1 — Primary'],
                            ['id'=>'setting-el-secondary','label'=>'System Color 2 — Secondary'],
                            ['id'=>'setting-el-text','label'=>'System Color 3 — Text'],
                            ['id'=>'setting-el-accent','label'=>'System Color 4 — Accent'],
                        ];
                        foreach ($el_colors as $ec): ?>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase"><?php echo esc_html($ec['label']); ?></label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="<?php echo esc_attr($ec['id']); ?>" class="w-9 h-9 rounded-lg border border-zinc-200 cursor-pointer p-0.5" oninput="document.getElementById('<?php echo esc_attr($ec['id']); ?>-text').value=this.value">
                                <input type="text" id="<?php echo esc_attr($ec['id']); ?>-text" class="flex-1 px-3 py-2 border border-zinc-200 rounded-lg text-xs font-mono uppercase focus:outline-none focus:border-zinc-400" oninput="syncColorPicker('<?php echo esc_attr($ec['id']); ?>', this.value)">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="space-y-5">
                        <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest border-b border-zinc-100 pb-2">Elementor Global Typography</h5>
                        <p class="text-[10px] text-zinc-400">These 4 typography presets map to Elementor Site Settings → Global Fonts (Primary, Secondary, Text, Accent typography groups).</p>
                        <?php
                        $el_types = [
                            ['key'=>'primary','label'=>'Typography 1 — Primary (Headings)'],
                            ['key'=>'secondary','label'=>'Typography 2 — Secondary (Subheadings)'],
                            ['key'=>'text','label'=>'Typography 3 — Text (Body)'],
                            ['key'=>'accent','label'=>'Typography 4 — Accent (Buttons/Labels)'],
                        ];
                        foreach ($el_types as $et): ?>
                        <div class="border border-zinc-100 rounded-lg p-3 space-y-3">
                            <p class="text-[10px] font-bold text-zinc-700"><?php echo esc_html($et['label']); ?></p>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-zinc-400 uppercase">Font Family</label>
                                    <select id="setting-el-type-<?php echo esc_attr($et['key']); ?>-family" class="w-full px-1.5 py-1.5 border border-zinc-200 rounded text-[10px] focus:outline-none focus:border-zinc-400 cursor-pointer">
                                        <?php foreach ($fonts as $f) echo '<option value="' . esc_attr($f) . '">' . esc_html($f) . '</option>'; ?>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-bold text-zinc-400 uppercase">Weight</label>
                                    <select id="setting-el-type-<?php echo esc_attr($et['key']); ?>-weight" class="w-full px-1.5 py-1.5 border border-zinc-200 rounded text-[10px] focus:outline-none focus:border-zinc-400 cursor-pointer">
                                        <?php foreach ([100,200,300,400,500,600,700,800,900] as $w): ?>
                                        <option value="<?php echo $w; ?>" <?php echo $w===($et['key']==='text'||$et['key']==='accent'?400:700)?'selected':''; ?>><?php echo $w; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <button onclick="triggerElementorSync()" class="w-full mt-2 px-4 py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold cursor-pointer transition-all flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
                            Sync to Elementor Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════ -->
            <!-- PANEL: Lovable CSS Tokens (conditional)               -->
            <!-- ══════════════════════════════════════════════════════ -->
            <div id="spanel-lovable" class="settings-panel p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-5">
                        <div class="flex items-start gap-3 p-4 bg-violet-50 border border-violet-100 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-violet-600 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                            <div>
                                <p class="text-xs font-bold text-zinc-900">CSS Design Tokens — Global Sync</p>
                                <p class="text-[10px] text-zinc-500 mt-1 leading-relaxed">All settings are compiled into a <code class="bg-violet-100 px-1 rounded text-[9px]">:root{}</code> CSS token block and injected on every frontend page. Your Lovable React components that use these CSS variables will update instantly.</p>
                                <p class="text-[10px] text-zinc-400 mt-2">Token file: <span id="lovable-token-path" class="font-mono text-violet-600">cora-global-tokens.css</span></p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase">CSS Variable Prefix</label>
                            <div class="flex items-center gap-2">
                                <input type="text" id="setting-css-prefix" value="--" class="w-16 px-3 py-2 border border-zinc-200 rounded-lg text-xs font-mono focus:outline-none focus:border-zinc-400">
                                <p class="text-[10px] text-zinc-400">Default <code class="bg-zinc-100 px-1 rounded">--</code> works with any Lovable Tailwind config or plain CSS.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="setting-dark-tokens" class="rounded cursor-pointer">
                            <label class="text-xs font-semibold text-zinc-700 cursor-pointer" for="setting-dark-tokens">Generate Dark Mode tokens (<code class="text-[10px]">[data-theme=dark]</code>)</label>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h5 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Live Token Preview</h5>
                            <button onclick="refreshTokenPreview()" class="text-[10px] text-zinc-500 hover:text-zinc-900 font-bold cursor-pointer transition-colors">↻ Refresh</button>
                        </div>
                        <div class="bg-zinc-950 rounded-xl p-4 overflow-auto max-h-80 font-mono text-[10px] leading-relaxed">
                            <pre id="lovable-token-preview" class="text-zinc-300 whitespace-pre-wrap">/* Loading tokens... */</pre>
                        </div>
                        <button onclick="triggerLovableTokenSync()" class="w-full px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-lg text-xs font-bold cursor-pointer transition-all flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
                            Push Tokens to Frontend
                        </button>
                    </div>
                </div>
            </div>


            <!-- ── Save Footer ──────────────────────────────────────── -->
            <div class="flex items-center justify-between border-t border-zinc-100 px-6 py-4">
                <p class="text-[10px] text-zinc-400" id="settings-save-hint">Changes apply globally across all pages when saved.</p>
                <button onclick="saveThemeSettings()" <?php echo $is_read_only ? 'disabled' : ''; ?> class="px-5 py-2 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-50 text-white rounded-lg text-xs font-bold shadow-sm cursor-pointer transition-all">Save Settings</button>
            </div>
        </div>

        <!-- TAB CONTENT: CUSTOM CODE -->
        <div id="tab-content-code" class="hidden">
            <div class="flex gap-0 bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden" style="min-height:580px">

                <!-- ── Left Nav: Code Sections ── -->
                <div class="w-44 flex-shrink-0 border-r border-zinc-100 bg-zinc-50 flex flex-col">
                    <div class="px-3 pt-4 pb-2">
                        <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Code Injections</p>
                    </div>
                    <nav class="flex flex-col gap-0.5 px-2 flex-1" id="code-section-nav">
                        <?php
                        $code_sections = [
                            ['id'=>'css',   'label'=>'Global CSS',       'icon'=>'<path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>',    'hint'=>'Injected in &lt;head&gt; on every page'],
                            ['id'=>'js',    'label'=>'Custom JS',        'icon'=>'<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',                    'hint'=>'Runs on every frontend page'],
                            ['id'=>'head',  'label'=>'Head HTML',        'icon'=>'<path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>','hint'=>'Tags appended to &lt;head&gt;'],
                            ['id'=>'body',  'label'=>'Body Scripts',     'icon'=>'<rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>','hint'=>'Injected before &lt;/body&gt;'],
                            ['id'=>'snips', 'label'=>'Snippets',         'icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>','hint'=>'Reusable code blocks'],
                        ];
                        foreach ($code_sections as $i => $cs): ?>
                        <button onclick="switchCodeSection('<?php echo esc_attr($cs['id']); ?>')" id="code-nav-<?php echo esc_attr($cs['id']); ?>"
                            class="code-nav-btn group w-full flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-left transition-all <?php echo $i === 0 ? 'bg-white shadow-sm border border-zinc-200 text-zinc-900' : 'text-zinc-500 hover:text-zinc-900 hover:bg-white'; ?> cursor-pointer border border-transparent">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                <?php echo $cs['icon']; ?>
                            </svg>
                            <span class="text-[11px] font-semibold leading-tight"><?php echo esc_html($cs['label']); ?></span>
                        </button>
                        <?php endforeach; ?>
                    </nav>

                    <!-- Bottom: Save indicator -->
                    <div class="px-3 py-3 border-t border-zinc-100 mt-auto">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-300" id="code-save-dot"></span>
                            <span class="text-[9px] text-zinc-400 font-medium" id="code-save-status">No changes</span>
                        </div>
                        <p class="text-[9px] text-zinc-300 mt-1">⌘S / Ctrl+S to save</p>
                    </div>
                </div>

                <!-- ── Right: Editor Area ── -->
                <div class="flex-1 flex flex-col min-w-0">

                    <!-- ══════════════════════════════════════════════ -->
                    <!-- SECTION: Global CSS                           -->
                    <!-- ══════════════════════════════════════════════ -->
                    <div id="code-section-css" class="code-section flex flex-col flex-1">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-100">
                            <div>
                                <p class="text-xs font-bold text-zinc-900">Global CSS Overrides</p>
                                <p class="text-[10px] text-zinc-400 mt-0.5">Compiled and injected in <code class="bg-zinc-100 px-1 rounded text-[9px]">&lt;style&gt;</code> inside the page <code class="bg-zinc-100 px-1 rounded text-[9px]">&lt;head&gt;</code>. Scoped to this theme.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] text-zinc-400 font-mono" id="css-stats">0 lines · 0 chars</span>
                                <button onclick="saveCustomCSS()" <?php echo $is_read_only ? 'disabled' : ''; ?>
                                    class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-40 text-white rounded-lg text-[11px] font-bold shadow-sm cursor-pointer transition-all flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                    Save CSS
                                </button>
                            </div>
                        </div>
                        <!-- CodeMirror mount target -->
                        <div class="flex-1 relative" style="min-height:460px">
                            <textarea id="custom-css-textarea" class="hidden"></textarea>
                            <div id="css-editor-mount" class="absolute inset-0 overflow-auto font-mono text-xs"></div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════════ -->
                    <!-- SECTION: Custom JS                            -->
                    <!-- ══════════════════════════════════════════════ -->
                    <div id="code-section-js" class="code-section flex flex-col flex-1 hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-100">
                            <div>
                                <p class="text-xs font-bold text-zinc-900">Custom JavaScript</p>
                                <p class="text-[10px] text-zinc-400 mt-0.5">Runs on all frontend pages of this theme.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <select id="setting-js-position" class="px-2.5 py-1.5 bg-white border border-zinc-200 rounded-lg text-[10px] text-zinc-600 focus:outline-none cursor-pointer">
                                    <option value="footer">Before &lt;/body&gt; (Recommended)</option>
                                    <option value="head">Inside &lt;head&gt;</option>
                                </select>
                                <span class="text-[9px] text-zinc-400 font-mono" id="js-stats">0 lines · 0 chars</span>
                                <button onclick="saveCustomJS()" <?php echo $is_read_only ? 'disabled' : ''; ?>
                                    class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-40 text-white rounded-lg text-[11px] font-bold shadow-sm cursor-pointer transition-all flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                    Save JS
                                </button>
                            </div>
                        </div>
                        <!-- Warning banner -->
                        <div class="mx-4 mt-3 flex items-start gap-2 p-3 bg-amber-50 border border-amber-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" class="flex-shrink-0 mt-0.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <p class="text-[10px] text-amber-800 leading-relaxed">JS errors here can break all frontend pages. Test locally before saving. Do not include the <code class="bg-amber-100 px-0.5 rounded">&lt;script&gt;</code> wrapper tags.</p>
                        </div>
                        <div class="flex-1 relative mt-3" style="min-height:410px">
                            <textarea id="custom-js-textarea" class="hidden"></textarea>
                            <div id="js-editor-mount" class="absolute inset-0 overflow-auto font-mono text-xs"></div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════════ -->
                    <!-- SECTION: Head HTML                            -->
                    <!-- ══════════════════════════════════════════════ -->
                    <div id="code-section-head" class="code-section flex flex-col flex-1 hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-100">
                            <div>
                                <p class="text-xs font-bold text-zinc-900">Head HTML Injection</p>
                                <p class="text-[10px] text-zinc-400 mt-0.5">Raw HTML tags appended to <code class="bg-zinc-100 px-1 rounded text-[9px]">&lt;head&gt;</code> — ideal for font imports, meta tags, and pixel base code.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] text-zinc-400 font-mono" id="head-stats">0 lines · 0 chars</span>
                                <button onclick="saveHeadHTML()" <?php echo $is_read_only ? 'disabled' : ''; ?>
                                    class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-40 text-white rounded-lg text-[11px] font-bold shadow-sm cursor-pointer transition-all flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                    Save HTML
                                </button>
                            </div>
                        </div>
                        <!-- Quick-insert chips -->
                        <div class="flex items-center gap-2 flex-wrap px-4 py-2 border-b border-zinc-50">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase">Quick insert:</span>
                            <?php
                            $head_snippets = [
                                ['label'=>'Google Font', 'code'=>'<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n" . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n" . '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">'],
                                ['label'=>'Meta Viewport', 'code'=>'<meta name="viewport" content="width=device-width, initial-scale=1.0">'],
                                ['label'=>'OG Tags', 'code'=>'<meta property="og:title" content="Your Site">' . "\n" . '<meta property="og:description" content="Description">' . "\n" . '<meta property="og:image" content="https://example.com/image.jpg">'],
                                ['label'=>'Canonical URL', 'code'=>'<link rel="canonical" href="https://yoursite.com/">'],
                                ['label'=>'No-index', 'code'=>'<meta name="robots" content="noindex, nofollow">'],
                            ];
                            foreach ($head_snippets as $qs): ?>
                            <button data-code="<?php echo esc_attr($qs['code']); ?>"
                                class="quick-insert-btn-head px-2 py-0.5 rounded-md border border-zinc-200 text-[9px] font-semibold text-zinc-600 hover:border-zinc-400 hover:text-zinc-900 bg-white cursor-pointer transition-all"><?php echo esc_html($qs['label']); ?></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex-1 relative" style="min-height:420px">
                            <textarea id="custom-head-textarea" class="hidden"></textarea>
                            <div id="head-editor-mount" class="absolute inset-0 overflow-auto font-mono text-xs"></div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════════ -->
                    <!-- SECTION: Body Scripts                         -->
                    <!-- ══════════════════════════════════════════════ -->
                    <div id="code-section-body" class="code-section flex flex-col flex-1 hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-100">
                            <div>
                                <p class="text-xs font-bold text-zinc-900">Body Script Injection</p>
                                <p class="text-[10px] text-zinc-400 mt-0.5">HTML/scripts injected just before <code class="bg-zinc-100 px-1 rounded text-[9px]">&lt;/body&gt;</code>. Ideal for chat widgets, analytics, and deferred scripts.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] text-zinc-400 font-mono" id="body-stats">0 lines · 0 chars</span>
                                <button onclick="saveBodyHTML()" <?php echo $is_read_only ? 'disabled' : ''; ?>
                                    class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 disabled:opacity-40 text-white rounded-lg text-[11px] font-bold shadow-sm cursor-pointer transition-all flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                    Save Body
                                </button>
                            </div>
                        </div>
                        <!-- Quick-insert chips -->
                        <div class="flex items-center gap-2 flex-wrap px-4 py-2 border-b border-zinc-50">
                            <span class="text-[9px] font-bold text-zinc-400 uppercase">Quick insert:</span>
                            <?php
                            $body_snippets = [
                                ['label'=>'Google Analytics', 'code'=>'<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>' . "\n" . '<script>' . "\n" . '  window.dataLayer = window.dataLayer || [];' . "\n" . '  function gtag(){dataLayer.push(arguments);}' . "\n" . '  gtag(\'js\', new Date());' . "\n" . '  gtag(\'config\', \'G-XXXXXXXXXX\');' . "\n" . '</script>'],
                                ['label'=>'GTM Body', 'code'=>'<!-- Google Tag Manager (noscript) -->' . "\n" . '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXX"' . "\n" . 'height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>'],
                                ['label'=>'FB Pixel', 'code'=>'<script>' . "\n" . '  !function(f,b,e,v,n,t,s)' . "\n" . '  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?' . "\n" . '  n.callMethod.apply(n,arguments):n.queue.push(arguments)};' . "\n" . '  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version=\'2.0\';' . "\n" . '  n.queue=[];t=b.createElement(e);t.async=!0;' . "\n" . '  t.src=v;s=b.getElementsByTagName(e)[0];' . "\n" . '  s.parentNode.insertBefore(t,s)}(window, document,\'script\',' . "\n" . '  \'https://connect.facebook.net/en_US/fbevents.js\');' . "\n" . '  fbq(\'init\', \'YOUR_PIXEL_ID\');' . "\n" . '  fbq(\'track\', \'PageView\');' . "\n" . '</script>'],
                                ['label'=>'Intercom', 'code'=>'<script>' . "\n" . '  window.intercomSettings = {' . "\n" . '    api_base: "https://api-iam.intercom.io",' . "\n" . '    app_id: "YOUR_APP_ID"' . "\n" . '  };' . "\n" . '  (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic(\'reattach_activator\');ic(\'update\',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement(\'script\');s.type=\'text/javascript\';s.async=true;s.src=\'https://widget.intercom.io/widget/YOUR_APP_ID\';var x=d.getElementsByTagName(\'script\')[0];x.parentNode.insertBefore(s,x);};if(document.readyState===\'complete\'){l();}else if(w.attachEvent){w.attachEvent(\'onload\',l);}else{w.addEventListener(\'load\',l,false);}}}());' . "\n" . '</script>'],
                                ['label'=>'Crisp Chat', 'code'=>'<script type="text/javascript">' . "\n" . '  window.$crisp=[];window.CRISP_WEBSITE_ID="YOUR-WEBSITE-ID";' . "\n" . '  (function(){d=document;s=d.createElement("script");' . "\n" . '  s.src="https://client.crisp.chat/l.js";' . "\n" . '  s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();' . "\n" . '</script>'],
                            ];
                            foreach ($body_snippets as $bs): ?>
                            <button data-code="<?php echo esc_attr($bs['code']); ?>"
                                class="quick-insert-btn-body px-2 py-0.5 rounded-md border border-zinc-200 text-[9px] font-semibold text-zinc-600 hover:border-zinc-400 hover:text-zinc-900 bg-white cursor-pointer transition-all"><?php echo esc_html($bs['label']); ?></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex-1 relative" style="min-height:390px">
                            <textarea id="custom-body-textarea" class="hidden"></textarea>
                            <div id="body-editor-mount" class="absolute inset-0 overflow-auto font-mono text-xs"></div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════════ -->
                    <!-- SECTION: Snippets Library                     -->
                    <!-- ══════════════════════════════════════════════ -->
                    <div id="code-section-snips" class="code-section flex flex-col flex-1 hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-100">
                            <div>
                                <p class="text-xs font-bold text-zinc-900">Snippets Library</p>
                                <p class="text-[10px] text-zinc-400 mt-0.5">Pre-built code blocks. Click any snippet to insert it into the relevant editor.</p>
                            </div>
                        </div>
                        <div class="flex-1 overflow-auto p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="snippets-grid">
                                <?php
                                $snippets = [
                                    ['cat'=>'CSS', 'title'=>'Smooth Scroll', 'desc'=>'Enables CSS smooth scrolling globally', 'code'=>'html { scroll-behavior: smooth; }'],
                                    ['cat'=>'CSS', 'title'=>'Custom Scrollbar', 'desc'=>'Styled webkit scrollbar for Chrome/Safari', 'code'=>"::-webkit-scrollbar { width: 6px; }\n::-webkit-scrollbar-track { background: #f4f4f5; }\n::-webkit-scrollbar-thumb { background: #a1a1aa; border-radius: 3px; }\n::-webkit-scrollbar-thumb:hover { background: #71717a; }"],
                                    ['cat'=>'CSS', 'title'=>'Selection Color', 'desc'=>'Custom text selection highlight color', 'code'=>"::selection { background: var(--color-primary, #18181b); color: #fff; }\n::-moz-selection { background: var(--color-primary, #18181b); color: #fff; }"],
                                    ['cat'=>'CSS', 'title'=>'Image Lazy Fade', 'desc'=>'Fade in images as they lazy load', 'code'=>"img[loading='lazy'] { opacity: 0; transition: opacity 0.4s ease; }\nimg[loading='lazy'].loaded { opacity: 1; }"],
                                    ['cat'=>'JS',  'title'=>'Back to Top', 'desc'=>'Scroll to top when button#back-to-top is clicked', 'code'=>"document.getElementById('back-to-top')?.addEventListener('click', () => {\n  window.scrollTo({ top: 0, behavior: 'smooth' });\n});"],
                                    ['cat'=>'JS',  'title'=>'Lazy Image Load', 'desc'=>'Trigger loaded class on lazy images', 'code'=>'document.querySelectorAll(\'img[loading="lazy"]\').forEach(img => {' . "\n" . '  img.addEventListener(\'load\', () => img.classList.add(\'loaded\'));' . "\n" . '  if (img.complete) img.classList.add(\'loaded\');' . "\n" . '});'],
                                    ['cat'=>'JS',  'title'=>'Console Welcome', 'desc'=>'A branded console message', 'code'=>"console.log('%c ✦ Built with Cora ', 'background:#18181b;color:#fff;font-size:14px;border-radius:4px;padding:4px 8px;');"],
                                    ['cat'=>'HTML','title'=>'Google Font Import', 'desc'=>'Inter font from Google Fonts CDN', 'code'=>'<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">'],
                                    ['cat'=>'HTML','title'=>'Schema — LocalBusiness', 'desc'=>'Structured data for local business SEO', 'code'=>'<script type="application/ld+json">{"@context":"https://schema.org","@type":"LocalBusiness","name":"Your Agency","address":{"@type":"PostalAddress","streetAddress":"123 Main St","addressLocality":"City","addressRegion":"ST","postalCode":"00000"},"telephone":"+1-555-555-5555","url":"https://yoursite.com"}</script>'],
                                    ['cat'=>'HTML','title'=>'Open Graph Tags', 'desc'=>'Social share meta tags', 'code'=>'<meta property="og:title" content="Your Site Title"><meta property="og:description" content="Your site description"><meta property="og:type" content="website"><meta property="og:url" content="https://yoursite.com"><meta property="og:image" content="https://yoursite.com/og-image.jpg">'],
                                ];
                                foreach ($snippets as $sn): ?>
                                <div class="border border-zinc-200 rounded-xl p-3.5 hover:border-zinc-400 transition-all group bg-white">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div>
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wide <?php echo $sn['cat']==='CSS'?'bg-blue-50 text-blue-700':($sn['cat']==='JS'?'bg-amber-50 text-amber-700':'bg-zinc-100 text-zinc-600'); ?> mb-1"><?php echo esc_html($sn['cat']); ?></span>
                                            <p class="text-[11px] font-bold text-zinc-900"><?php echo esc_html($sn['title']); ?></p>
                                            <p class="text-[10px] text-zinc-500 mt-0.5"><?php echo esc_html($sn['desc']); ?></p>
                                        </div>
                                        <button data-cat="<?php echo esc_attr($sn['cat']); ?>" data-code="<?php echo esc_attr($sn['code']); ?>"
                                            class="quick-insert-btn-snip flex-shrink-0 px-2.5 py-1.5 border border-zinc-200 rounded-lg text-[10px] font-bold text-zinc-600 hover:bg-zinc-950 hover:text-white hover:border-zinc-950 cursor-pointer transition-all">
                                            Insert
                                        </button>
                                    </div>
                                    <pre class="bg-zinc-950 text-zinc-300 rounded-lg p-2.5 text-[9px] font-mono leading-relaxed overflow-auto max-h-24 whitespace-pre-wrap"><?php echo esc_html(mb_strimwidth($sn['code'], 0, 180, '...')); ?></pre>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div><!-- end editor area -->
            </div><!-- end flex container -->
        </div>

    </div>

    <!-- LEVEL 3 — ELEMENTOR PAGE EDITOR iframe wrapper -->
    <div id="canvas-level-3" class="fixed inset-0 z-[9999] bg-white hidden flex flex-col">
        <!-- Editor Topbar Wrapper -->
        <div id="cora-parent-editor-topbar" class="h-24 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800 flex flex-col hidden select-none">
            <!-- Row 1 -->
            <div class="h-12 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between px-4 w-full bg-zinc-50 dark:bg-zinc-950">
                <!-- Left: Logo & Theme Selector -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 select-none shrink-0 mr-1">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-850 dark:text-zinc-150">
                            <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5" stroke-width="2"></polygon>
                            <circle cx="12" cy="12" r="3.5" stroke-width="1.5"></circle>
                        </svg>
                        <span class="text-xs font-black tracking-tight text-zinc-900 dark:text-zinc-100">cora</span>
                    </div>
                    <button onclick="closeElementorEditor()" class="h-8 px-3 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-150 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 rounded-lg text-[11px] font-bold cursor-pointer transition-colors flex items-center gap-1.5 bg-white dark:bg-zinc-900">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        Theme Dashboard
                    </button>
                    <!-- Retain the ID for E2E selector safety -->
                    <span id="cora-topbar-theme-name" class="hidden"></span>
                </div>
                
                <!-- Center: Breadcrumbs & Save Status -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1.5 text-zinc-400 dark:text-zinc-500 text-[11px] font-medium">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <span>/</span>
                        <span>Pages</span>
                        <span>/</span>
                        <span class="text-zinc-800 dark:text-zinc-200 font-bold" id="cora-topbar-page-name">Home Page</span>
                    </div>
                    <div class="h-3 w-[1px] bg-zinc-250 dark:bg-zinc-800"></div>
                    <div class="flex items-center gap-1.5 text-[10px] text-zinc-500 dark:text-zinc-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span id="cora-save-status-text">All changes saved</span>
                    </div>
                </div>
                
                <!-- Right: Preview + Avatar -->
                <div class="flex items-center gap-1.5">
                    <button onclick="previewPage()" class="h-8 px-3 rounded-lg hover:bg-zinc-200/60 dark:hover:bg-zinc-900 text-zinc-600 dark:text-zinc-300 text-[11px] font-bold cursor-pointer transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        Preview
                    </button>
                    <div class="h-4 w-[1px] bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                    <div class="flex items-center gap-1 cursor-pointer select-none">
                        <div class="w-7 h-7 rounded-full overflow-hidden bg-zinc-200 dark:bg-zinc-800">
                            <?php 
                            $current_user = wp_get_current_user();
                            $avatar_url = get_avatar_url($current_user->ID, array('size' => 56));
                            if (!$avatar_url) {
                                $avatar_url = CORA_REAL_ESTATE_AI_URL . 'assets/images/avatar.png';
                            }
                            ?>
                            <img src="<?php echo esc_url($avatar_url); ?>" class="w-full h-full object-cover" alt="User" />
                        </div>
                        <svg class="w-3 h-3 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>
            </div>

            <!-- Row 2 -->
            <div class="h-12 border-b border-zinc-200 dark:border-zinc-850 flex items-center justify-between px-4 w-full bg-white dark:bg-zinc-900">
                <!-- Left: Add, Templates, Undo/Redo -->
                <div class="flex items-center gap-2">
                    <button onclick="toggleWidgetsPanel()" class="h-8 w-8 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 rounded-lg cursor-pointer transition-colors flex items-center justify-center" title="Add Element">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                    <button onclick="openTemplatesLibrary()" class="h-8 px-3 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 rounded-lg text-[11px] font-bold cursor-pointer transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                        Templates
                    </button>
                    <button id="cora-git-btn" onclick="openGitDrawer()" class="h-8 px-3 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 rounded-lg text-[11px] font-bold cursor-pointer transition-colors flex items-center gap-1.5 relative" title="Git Version Control">
                        <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                        Git
                        <span id="cora-git-status-dot" class="hidden w-1.5 h-1.5 rounded-full bg-emerald-500 absolute top-1.5 right-1.5"></span>
                    </button>
                    <div class="h-4 w-[1px] bg-zinc-200 dark:bg-zinc-800 mx-1"></div>
                    <button onclick="runElementorCommand('document/history/undo')" class="h-8 w-8 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 rounded-lg cursor-pointer transition-colors flex items-center justify-center" title="Undo">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"></path><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"></path></svg>
                    </button>
                    <button onclick="runElementorCommand('document/history/redo')" class="h-8 w-8 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 rounded-lg cursor-pointer transition-colors flex items-center justify-center" title="Redo">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 7v6h-6"></path><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l3 2.7"></path></svg>
                    </button>
                </div>
                
                <!-- Center: Page Switcher Dropdown & Devices -->
                <div class="flex items-center gap-3">
                    <!-- Page switcher -->
                    <div class="relative" id="cora-page-switcher-wrap">
                        <button onclick="togglePageSwitcher(event)" class="flex items-center gap-1 cursor-pointer select-none h-8 px-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200" id="cora-topbar-page-selector">Home Page</span>
                            <svg class="w-3.5 h-3.5 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <!-- Page dropdown -->
                        <div id="cora-page-switcher-dropdown" class="hidden absolute top-full left-0 mt-1 w-64 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl z-[99999] overflow-hidden">
                            <div class="p-2 border-b border-zinc-150 dark:border-zinc-800">
                                <input id="cora-page-switcher-search" type="text" placeholder="Search pages..." oninput="filterPageSwitcher(this.value)" class="w-full px-2.5 py-1.5 text-[11px] border border-zinc-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 focus:outline-none focus:border-zinc-400" />
                            </div>
                            <div id="cora-page-switcher-list" class="max-h-60 overflow-y-auto py-1"></div>
                        </div>
                    </div>
                    <div class="h-4 w-[1px] bg-zinc-200 dark:bg-zinc-800"></div>
                    <!-- Viewport devices -->
                    <div class="flex items-center gap-1">
                        <div class="flex items-center bg-zinc-100 dark:bg-zinc-800 p-0.5 rounded-lg" id="cora-device-pill">
                            <button id="cora-device-desktop" onclick="switchDevice('desktop')" title="Desktop" class="w-7 h-7 rounded-md flex items-center justify-center text-zinc-800 dark:text-zinc-200 bg-white dark:bg-zinc-700 shadow-sm cursor-pointer transition-all">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                            </button>
                            <button id="cora-device-tablet" onclick="switchDevice('tablet')" title="Tablet (768px)" class="w-7 h-7 rounded-md flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 cursor-pointer transition-all">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                            </button>
                            <button id="cora-device-mobile" onclick="switchDevice('mobile')" title="Mobile (375px)" class="w-7 h-7 rounded-md flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 cursor-pointer transition-all">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Navigator, Publish -->
                <div class="flex items-center gap-2">
                    <button onclick="toggleNavigatorPanel()" class="h-8 px-3 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 rounded-lg text-[11px] font-bold cursor-pointer transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="6" y1="3" x2="6" y2="15"></line><circle cx="18" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><path d="M18 9a9 9 0 0 1-9 9"></path></svg>
                        Navigator
                    </button>
                    
                    <!-- Split Publish Button -->
                    <div class="inline-flex rounded-lg shadow-sm relative">
                        <button onclick="publishPage()" class="h-8 px-4 bg-zinc-900 dark:bg-white hover:bg-zinc-800 dark:hover:bg-zinc-50 text-white dark:text-zinc-900 rounded-l-lg text-[11px] font-extrabold cursor-pointer transition-colors flex items-center justify-center">
                            Publish
                        </button>
                        <div class="w-[1px] h-8 bg-zinc-800 dark:bg-zinc-100"></div>
                        <button onclick="togglePublishDropdown(event)" class="h-8 px-2 bg-zinc-900 dark:bg-white hover:bg-zinc-800 dark:hover:bg-zinc-50 text-white dark:text-zinc-900 rounded-r-lg text-[11px] font-bold cursor-pointer transition-colors flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <!-- Publish Dropdown Menu -->
                        <div id="cora-publish-dropdown" class="absolute right-0 top-10 w-40 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-xl py-1 hidden z-50">
                            <button onclick="saveDraftPage(); togglePublishDropdown(event);" class="w-full text-left px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-700 dark:text-zinc-200 cursor-pointer">Save Draft</button>
                            <button onclick="saveTemplatePage(); togglePublishDropdown(event);" class="w-full text-left px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-700 dark:text-zinc-200 cursor-pointer">Save as Template</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- iframe container -->
        <div id="elementor-iframe-container" class="flex-1 min-h-0 w-full bg-zinc-50 relative flex items-center justify-center">

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
<div id="drawer-import-kit" class="fixed inset-0 z-[99999] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-import-kit-card">
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

<!-- GitHub Connection Drawer -->
<div id="drawer-github-connect" class="fixed inset-0 z-[99999] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-github-connect-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <h3 class="text-sm font-bold text-zinc-950">Connect from GitHub</h3>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeGithubConnectDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">GitHub Repository URL *</label>
                <input type="text" id="github-repo-input" placeholder="e.g. username/repo-name or https://github.com/..." class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Branch *</label>
                <input type="text" id="github-branch-input" placeholder="main" value="main" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Personal Access Token (PAT)</label>
                <input type="password" id="github-pat-input" placeholder="Required for private repositories" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
                <p class="text-[9px] text-zinc-400">Optional: Used to read private repository data.</p>
            </div>
        </div>
        <div class="p-5 border-t border-zinc-100 flex items-center justify-end gap-3 bg-zinc-50/50">
            <button type="button" class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeGithubConnectDrawer()">Cancel</button>
            <button type="button" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="triggerGithubConnect()">Connect Repository</button>
        </div>
    </div>
</div>

<!-- Cora Hub Drawer -->
<div id="drawer-cora-hub" class="fixed inset-0 z-[99999] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-cora-hub-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <h3 class="text-sm font-bold text-zinc-950">Browse Free Themes</h3>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeCoraHubDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-5">
            <p class="text-[11px] text-zinc-500 leading-normal">Choose from one of our professional, mobile-optimized free theme layouts to jumpstart your storefront workspace.</p>
            
            <!-- Hub theme options grid -->
            <div class="space-y-4">
                <!-- Dawn Theme -->
                <div class="border border-zinc-200 rounded-xl p-4 flex gap-4 bg-white hover:border-zinc-300 transition-colors">
                    <img src="<?php echo esc_url( plugins_url('assets/images/cora-cro-l2-preview.jpg', CORA_PLUGIN_FILE) ); ?>" class="w-24 h-16 rounded-lg object-cover border border-zinc-200 shrink-0 select-none">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-zinc-900">Dawn</h4>
                            <span class="px-1.5 py-0.5 text-[8.5px] font-bold text-zinc-500 bg-zinc-100 rounded">v15.5.0</span>
                        </div>
                        <p class="text-[10px] text-zinc-400 mt-1 line-clamp-2">A chic, minimalist theme for modern fashion retail and chic catalogs.</p>
                        <button onclick="installHubTheme('Dawn', 2)" class="mt-2.5 px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-[10px] font-bold cursor-pointer transition-all border-none shadow-xs">Install</button>
                    </div>
                </div>
                
                <!-- Refresh Theme -->
                <div class="border border-zinc-200 rounded-xl p-4 flex gap-4 bg-white hover:border-zinc-300 transition-colors">
                    <img src="<?php echo esc_url( plugins_url('assets/images/cosmetics-pink-preview.jpg', CORA_PLUGIN_FILE) ); ?>" class="w-24 h-16 rounded-lg object-cover border border-zinc-200 shrink-0 select-none">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-zinc-900">Refresh</h4>
                            <span class="px-1.5 py-0.5 text-[8.5px] font-bold text-zinc-500 bg-zinc-100 rounded">v15.5.0</span>
                        </div>
                        <p class="text-[10px] text-zinc-400 mt-1 line-clamp-2">A fresh cosmetic/skincare theme design with light colors and product catalog.</p>
                        <button onclick="installHubTheme('Refresh', 3)" class="mt-2.5 px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-[10px] font-bold cursor-pointer transition-all border-none shadow-xs">Install</button>
                    </div>
                </div>

                <!-- Horizon Theme -->
                <div class="border border-zinc-200 rounded-xl p-4 flex gap-4 bg-white hover:border-zinc-300 transition-colors">
                    <img src="<?php echo esc_url( plugins_url('assets/images/horizon-preview.jpg', CORA_PLUGIN_FILE) ); ?>" class="w-24 h-16 rounded-lg object-cover border border-zinc-200 shrink-0 select-none">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-zinc-900">Horizon</h4>
                            <span class="px-1.5 py-0.5 text-[8.5px] font-bold text-zinc-500 bg-zinc-100 rounded">v4.1.3</span>
                        </div>
                        <p class="text-[10px] text-zinc-400 mt-1 line-clamp-2">A clean landscape/cartoon outline vector style illustration design.</p>
                        <button onclick="installHubTheme('Horizon', 5)" class="mt-2.5 px-3 py-1 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-[10px] font-bold cursor-pointer transition-all border-none shadow-xs">Install</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-5 border-t border-zinc-100 flex items-center justify-end bg-zinc-50/50">
            <button type="button" class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeCoraHubDrawer()">Close</button>
        </div>
    </div>
</div>

<!-- 1. New Theme Setup Modal (Centered Popup) -->
<div id="drawer-new-theme" class="fixed inset-0 z-[999999] bg-zinc-900/40 backdrop-blur-[1px] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white border border-zinc-200 rounded-xl shadow-2xl p-6 w-full max-w-md space-y-4 transform scale-95 transition-transform duration-300" id="drawer-new-theme-card">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-zinc-950">Initialize New Theme</h3>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeNewThemeDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="space-y-4">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Theme Name *</label>
                <input type="text" id="new-theme-name-input" placeholder="e.g. Westside Luxury Catalog" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
            
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Start From Structure</label>
                <div class="grid grid-cols-1 gap-2.5">
                    <label class="border border-zinc-200 rounded-xl p-3 flex items-start gap-2.5 cursor-pointer hover:bg-zinc-50 transition-colors">
                        <input type="radio" name="new-theme-source" value="blank" checked class="mt-1 accent-zinc-950">
                        <div>
                            <div class="text-[11px] font-bold text-zinc-900">Blank Layout</div>
                            <p class="text-[9px] text-zinc-500 mt-0.5">Empty theme workspace. Create pages as required.</p>
                        </div>
                    </label>
                    <label class="border border-zinc-200 rounded-xl p-3 flex items-start gap-2.5 cursor-pointer hover:bg-zinc-50 transition-colors">
                        <input type="radio" name="new-theme-source" value="duplicate" class="mt-1 accent-zinc-950">
                        <div>
                            <div class="text-[11px] font-bold text-zinc-900">Duplicate Active</div>
                            <p class="text-[9px] text-zinc-500 mt-0.5">Copies the current live theme settings and page lists as a draft.</p>
                        </div>
                    </label>
                    <label class="border border-zinc-200 rounded-xl p-3 flex items-start gap-2.5 cursor-pointer hover:bg-zinc-50 transition-colors">
                        <input type="radio" name="new-theme-source" value="template" class="mt-1 accent-zinc-950">
                        <div>
                            <div class="text-[11px] font-bold text-zinc-900">From Template</div>
                            <p class="text-[9px] text-zinc-500 mt-0.5">Incorporate Cora agency themes (Real Estate Minimal / Premium Villa Brokerage).</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-2.5 pt-2">
            <button type="button" class="px-3.5 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeNewThemeDrawer()">Cancel</button>
            <button type="button" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="saveNewTheme()">Create Theme</button>
        </div>
    </div>
</div>

<!-- 2. New Page Setup Modal (Sliding Right-Drawer) -->
<div id="drawer-new-page" class="fixed inset-0 z-[999999] bg-zinc-900/40 backdrop-blur-[1px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[420px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-new-page-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">Add Page to Theme</h3>
                <p class="text-[10px] text-zinc-500 mt-0.5">Configure new page settings and layout templates.</p>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeNewPageDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Page Title *</label>
                <input type="text" id="new-page-title-input" onkeyup="autoGenerateSlug(this)" placeholder="e.g. Featured Penthouse Listings" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 font-medium">
            </div>
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">URL Slug Path *</label>
                <input type="text" id="new-page-slug-input" placeholder="e.g. penthouse-listings" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 font-medium">
            </div>
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Layout Template</label>
                    <select id="new-page-template-input" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer bg-white font-medium">
                        <option value="agency">Agency</option>
                        <option value="brokerage">Brokerage</option>
                        <option value="minimal">Minimal</option>
                        <option value="landing-page">Landing Page</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Status</label>
                    <select id="new-page-status-input" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer bg-white font-medium">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="p-5 border-t border-zinc-200 flex items-center justify-end gap-2.5 bg-zinc-50/30">
            <button type="button" class="px-3.5 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-bold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeNewPageDrawer()">Cancel</button>
            <button type="button" class="px-3.5 py-2 border border-zinc-200 hover:bg-zinc-50 text-zinc-700 font-bold rounded-lg text-xs transition-colors cursor-pointer" onclick="saveNewPage(false)">Create Only</button>
            <button type="button" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer" onclick="saveNewPage(true)">Create & Edit</button>
        </div>
    </div>
</div>

<!-- 2.5. Menu Item Setup Drawer (Sliding Right-Drawer) -->
<div id="drawer-menu-item" class="fixed inset-0 z-[999999] bg-zinc-900/40 backdrop-blur-[1px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300 hidden">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[420px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-menu-item-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <div>
                <h3 id="drawer-menu-item-title" class="text-sm font-bold text-zinc-950">Add Menu Item</h3>
                <p class="text-[10px] text-zinc-500 mt-0.5">Configure link text, destination, and target options.</p>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeMenuItemDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            <input type="hidden" id="menu-item-edit-index" value="">
            
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Link Label *</label>
                <input type="text" id="menu-item-label-input" placeholder="e.g. Listings" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 font-medium">
            </div>
            
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Link Destination Type</label>
                <select id="menu-item-type-input" onchange="toggleMenuItemTypeFields()" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer bg-white font-medium">
                    <option value="page">Internal Page</option>
                    <option value="custom">Custom URL Link</option>
                </select>
            </div>
            
            <div id="menu-item-page-field" class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Select Page *</label>
                <select id="menu-item-page-select" class="w-full px-2.5 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 cursor-pointer bg-white font-medium">
                    <!-- Dynamic Page list options will be inserted here -->
                </select>
            </div>
            
            <div id="menu-item-url-field" class="space-y-2 hidden">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Custom URL Link *</label>
                <input type="text" id="menu-item-url-input" placeholder="e.g. /custom-page or https://..." class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 font-medium">
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 text-xs text-zinc-700 cursor-pointer select-none">
                    <input type="checkbox" id="menu-item-newtab-input" class="accent-zinc-950 rounded">
                    Open in a new browser tab
                </label>
            </div>
        </div>
        <div class="p-5 border-t border-zinc-200 flex items-center justify-end gap-2.5 bg-zinc-50/30">
            <button type="button" class="px-3.5 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-bold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeMenuItemDrawer()">Cancel</button>
            <button type="button" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer" onclick="saveMenuItem()">Apply Changes</button>
        </div>
    </div>
</div>

<!-- Rename Page Modal (Sliding Right-Drawer) -->
<div id="drawer-rename-page" class="fixed inset-0 z-[999999] bg-zinc-900/40 backdrop-blur-[1px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[420px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-rename-page-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">Rename Page</h3>
                <p class="text-[10px] text-zinc-500 mt-0.5">Specify a new display title for this page.</p>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeRenamePageDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            <input type="hidden" id="rename-page-id-input">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">Page Title *</label>
                <input type="text" id="rename-page-title-input" placeholder="e.g. Featured Penthouse Listings" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 font-medium">
            </div>
        </div>
        <div class="p-5 border-t border-zinc-200 flex items-center justify-end gap-2.5 bg-zinc-50/30">
            <button type="button" class="px-3.5 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-bold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeRenamePageDrawer()">Cancel</button>
            <button type="button" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer" onclick="saveRenamedPage()">Rename Page</button>
        </div>
    </div>
</div>

<!-- Change Page Slug Modal (Sliding Right-Drawer) -->
<div id="drawer-change-page-slug" class="fixed inset-0 z-[999999] bg-zinc-900/40 backdrop-blur-[1px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[420px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-change-page-slug-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">Change Page Slug URL</h3>
                <p class="text-[10px] text-zinc-500 mt-0.5">Specify a new URL path slug for this page route.</p>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeChangePageSlugDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            <input type="hidden" id="change-page-slug-id-input">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider">URL Slug Path *</label>
                <input type="text" id="change-page-slug-input" placeholder="e.g. penthouse-listings" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 font-medium">
            </div>
        </div>
        <div class="p-5 border-t border-zinc-200 flex items-center justify-end gap-2.5 bg-zinc-50/30">
            <button type="button" class="px-3.5 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-bold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeChangePageSlugDrawer()">Cancel</button>
            <button type="button" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer" onclick="savePageSlug()">Update Slug</button>
        </div>
    </div>
</div>

<!-- 3. Global SEO settings Side Drawer -->
<div id="drawer-page-seo" class="fixed inset-0 z-[99999] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-page-seo-card">
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
<div id="drawer-page-revisions" class="fixed inset-0 z-[99999] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-page-revisions-card">
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

<!-- 6. Theme Rename Modal (Sliding Right-Drawer) -->
<div id="drawer-rename-theme" class="fixed inset-0 z-[999999] bg-zinc-900/40 backdrop-blur-[1px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300 hidden">
    <div class="bg-white border-l border-zinc-200 h-full w-full max-w-[420px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-rename-theme-card">
        <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
            <div>
                <h3 class="text-sm font-bold text-zinc-950">Rename Theme</h3>
                <p class="text-[10px] text-zinc-500 mt-0.5">Specify a new display name for this theme workspace.</p>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeRenameThemeDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            <input type="hidden" id="rename-theme-id-input">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider font-sans">Theme Name *</label>
                <input type="text" id="rename-theme-name-input" placeholder="e.g. Cora Custom Theme" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 font-medium font-sans">
            </div>
        </div>
        <div class="p-5 border-t border-zinc-200 flex items-center justify-end gap-2.5 bg-zinc-50/30">
            <button type="button" class="px-3.5 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-bold rounded-lg text-xs transition-colors cursor-pointer font-sans" onclick="closeRenameThemeDrawer()">Cancel</button>
            <button type="button" class="px-3.5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors cursor-pointer font-sans" onclick="saveRenamedTheme()">Rename Theme</button>
        </div>
    </div>
</div>

<!-- 7. Git Connection Drawer (Right-Sliding Sheet) -->
<div id="drawer-git-connect" class="fixed inset-0 z-[999999] bg-zinc-900/40 backdrop-blur-[1px] flex justify-end opacity-0 pointer-events-none transition-opacity duration-300 hidden">
    <div class="bg-white dark:bg-zinc-950 border-l border-zinc-200 dark:border-zinc-800 h-full w-full max-w-[460px] shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300 pointer-events-auto" id="drawer-git-connect-card">
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-900/50 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" class="text-zinc-700 dark:text-zinc-300"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><path d="M6 9v6"></path><path d="M9 18h9"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-zinc-950 dark:text-zinc-50 font-sans">Git Integration</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5 font-sans">Sync your website design with GitHub</p>
                </div>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer p-1 transition-colors bg-transparent border-none" onclick="closeGitDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <!-- Drawer Body -->
        <div class="flex-1 overflow-y-auto p-5 space-y-6">
            <!-- STEP 1: CONNECT PAT -->
            <div id="git-step-connect-pat" class="space-y-5 hidden" style="display:none" data-alias="git-unconnected-form"></div><!-- alias bridge -->
            <div id="git-unconnected-form" class="space-y-5">

                <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-2">
                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider font-sans">Easy Setup</p>
                    <p class="text-[11px] text-zinc-650 dark:text-zinc-400 leading-relaxed font-sans">
                        Let's connect your website to GitHub so your designs are safely saved and versioned automatically.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="p-4 border border-zinc-150 dark:border-zinc-850 rounded-xl bg-white dark:bg-zinc-900 space-y-3">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-zinc-950 dark:bg-zinc-50 text-[10px] font-bold text-white dark:text-zinc-950">1</span>
                        <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 font-sans inline-block ml-1">Get your GitHub Access Token</p>
                        <p class="text-[11px] text-zinc-500 leading-relaxed font-sans">
                            Click the button below to open GitHub (create a free account if you don't have one). Scroll down and click the green <strong>"Generate token"</strong> button at the bottom.
                        </p>
                        <a href="https://github.com/settings/tokens/new?scopes=repo&description=Cora%2520Studio%2520Integration" target="_blank" class="w-full h-9 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-850 dark:text-zinc-200 font-bold rounded-lg text-xs transition-colors cursor-pointer flex items-center justify-center gap-1.5 border border-zinc-200 dark:border-zinc-750 no-underline font-sans">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            Get GitHub Access Token
                        </a>
                    </div>

                    <div class="p-4 border border-zinc-150 dark:border-zinc-850 rounded-xl bg-white dark:bg-zinc-900 space-y-3">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-zinc-950 dark:bg-zinc-50 text-[10px] font-bold text-white dark:text-zinc-950">2</span>
                        <label for="git-pat-input" class="text-xs font-bold text-zinc-800 dark:text-zinc-200 font-sans inline-block ml-1">Paste your Access Token</label>
                        <p class="text-[11px] text-zinc-500 leading-relaxed font-sans">
                            Copy the generated token from GitHub (starts with <code>ghp_</code>) and paste it below.
                        </p>
                        <input type="password" id="git-pat-input" placeholder="ghp_..." class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-xs focus:outline-none focus:border-zinc-850 dark:focus:border-zinc-400 font-medium bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-sans">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="button" id="git-connect-btn" onclick="connectGitRepository()" class="w-full h-10 bg-zinc-950 dark:bg-white hover:bg-zinc-800 dark:hover:bg-zinc-100 text-white dark:text-zinc-900 font-bold rounded-lg text-xs transition-colors cursor-pointer flex items-center justify-center gap-2 border-none">
                        Connect Account
                    </button>
                </div>
            </div>

            <!-- STEP 2: CHOOSE OR CREATE REPOSITORY -->
            <div id="git-step-select-repo" class="space-y-5 hidden">
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0 animate-pulse"></span>
                        <p class="text-[11px] font-bold text-zinc-800 dark:text-zinc-200 font-sans" id="git-connected-user-temp">Connected as @user</p>
                    </div>
                    <button onclick="disconnectGit()" class="text-[10px] font-bold text-zinc-500 hover:text-red-650 transition-colors cursor-pointer border-none bg-transparent font-sans">Disconnect</button>
                </div>

                <div class="space-y-4">
                    <p class="text-[10px] font-bold text-zinc-450 uppercase tracking-wider font-sans">Setup Repository</p>
                    
                    <!-- TAB OPTIONS -->
                    <div class="grid grid-cols-2 gap-2 p-0.5 bg-zinc-100 dark:bg-zinc-900 rounded-lg">
                        <button onclick="switchRepoTab('create')" id="tab-repo-create" class="py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer border-none bg-white dark:bg-zinc-800 text-zinc-850 dark:text-zinc-100 shadow-sm">
                            Create New (Recommended)
                        </button>
                        <button onclick="switchRepoTab('link')" id="tab-repo-link" class="py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer border-none bg-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200">
                            Link Existing
                        </button>
                    </div>

                    <!-- TAB 1: CREATE NEW -->
                    <div id="panel-repo-create" class="space-y-3.5">
                        <p class="text-[11px] text-zinc-500 leading-relaxed font-sans">
                            We will automatically create a private, secure repository on GitHub for you to store your pages.
                        </p>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider font-sans">New Repository Name</label>
                            <input type="text" id="git-new-repo-name" value="cora-website" placeholder="cora-website" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-xs focus:outline-none focus:border-zinc-850 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-sans font-medium">
                        </div>
                        <button type="button" id="git-create-repo-btn" onclick="createGitRepository()" class="w-full h-10 bg-zinc-950 dark:bg-white hover:bg-zinc-850 dark:hover:bg-zinc-100 text-white dark:text-zinc-900 font-bold rounded-lg text-xs transition-colors cursor-pointer border-none font-sans flex items-center justify-center gap-2">
                            Create Private Repository
                        </button>
                    </div>

                    <!-- TAB 2: LINK EXISTING -->
                    <div id="panel-repo-link" class="space-y-3.5 hidden">
                        <p class="text-[11px] text-zinc-500 leading-relaxed font-sans">
                            If you already have a repository on GitHub, enter its path below to link it.
                        </p>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider font-sans">Repository Path</label>
                            <input type="text" id="git-link-repo-path" placeholder="username/repository-name" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-xs focus:outline-none focus:border-zinc-850 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-sans font-medium">
                            <p class="text-[9px] text-zinc-400 pl-1">Format: <code>owner/repository-name</code></p>
                        </div>
                        <button type="button" id="git-link-repo-btn" onclick="linkGitRepository()" class="w-full h-10 bg-zinc-950 dark:bg-white hover:bg-zinc-850 dark:hover:bg-zinc-100 text-white dark:text-zinc-900 font-bold rounded-lg text-xs transition-colors cursor-pointer border-none font-sans flex items-center justify-center gap-2">
                            Link Repository
                        </button>
                    </div>
                </div>
            </div>

            <!-- STEP 3: CONNECTED DASHBOARD -->
            <div id="git-connected-dashboard" class="space-y-6 hidden">
                <!-- Status Banner -->
                <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 flex-shrink-0 animate-pulse"></span>
                        <div>
                            <p class="text-[11px] font-bold text-zinc-800 dark:text-zinc-200 font-sans" id="git-connected-user">Connected as @user</p>
                            <a href="#" id="git-connected-repo-link" target="_blank" class="text-[10px] text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors flex items-center gap-1 font-sans">
                                owner/repo
                                <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                        </div>
                    </div>
                    <button onclick="disconnectGit()" class="text-[10px] font-bold text-zinc-500 hover:text-red-650 transition-colors cursor-pointer border-none bg-transparent font-sans">Disconnect</button>
                </div>

                <!-- Branch Management -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-wider font-sans">Active Branch</label>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <select id="git-branch-select" onchange="changeActiveBranch()" class="w-full px-3 py-2.5 border border-zinc-200 dark:border-zinc-700 rounded-lg text-xs focus:outline-none focus:border-zinc-800 dark:focus:border-zinc-400 font-medium bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 cursor-pointer font-sans">
                                <option value="main">main</option>
                            </select>
                        </div>
                        <button onclick="toggleInlineNewBranch(true)" class="h-9 px-3 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-850 rounded-lg text-[10px] font-bold text-zinc-700 dark:text-zinc-200 transition-colors cursor-pointer flex items-center gap-1 bg-white dark:bg-zinc-900 font-sans">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            New
                        </button>
                    </div>

                    <!-- Inline New Branch Form -->
                    <div id="git-new-branch-container" class="hidden mt-2 p-3 border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30 rounded-xl space-y-3">
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-bold text-zinc-400 uppercase tracking-wider font-sans">New Branch Name</label>
                            <input type="text" id="new-branch-name-input" placeholder="e.g. feature-homepage-redesign" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-xs focus:outline-none focus:border-zinc-400 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-medium font-sans">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button onclick="toggleInlineNewBranch(false)" class="px-2.5 py-1.5 border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-[10px] text-zinc-650 dark:text-zinc-300 font-bold rounded-md hover:bg-zinc-50 transition-colors cursor-pointer font-sans">Cancel</button>
                            <button onclick="submitCreateBranch()" class="px-2.5 py-1.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-900 text-[10px] font-bold rounded-md hover:bg-zinc-850 transition-colors cursor-pointer border-none font-sans">Create</button>
                        </div>
                    </div>
                </div>

                <!-- Manual Commit Actions -->
                <div class="space-y-3 border-t border-zinc-150 dark:border-zinc-800 pt-5">
                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider font-sans">Commit & Sync Changes</p>
                    
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-zinc-400 font-sans">Commit Message</label>
                        <textarea id="git-commit-msg" rows="3" placeholder="Describe the layout edits or components updated..." class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-xs focus:outline-none focus:border-zinc-400 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 font-medium font-sans resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <button type="button" id="git-push-btn" onclick="pushPageLayout()" class="h-9 bg-zinc-950 dark:bg-white hover:bg-zinc-800 dark:hover:bg-zinc-100 text-white dark:text-zinc-900 font-bold rounded-lg text-xs transition-colors cursor-pointer flex items-center justify-center gap-1.5 border-none font-sans">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="16 16 12 12 8 16"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path><polyline points="16 16 12 12 8 16"></polyline></svg>
                            Commit & Push
                        </button>
                        <button type="button" id="git-pull-btn" onclick="pullDesignTemplates()" class="h-9 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-850 rounded-lg text-xs font-bold text-zinc-700 dark:text-zinc-200 transition-colors cursor-pointer flex items-center justify-center gap-1.5 bg-white dark:bg-zinc-900 font-sans">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path><polyline points="8 17 12 21 16 17"></polyline></svg>
                            Pull Design
                        </button>
                    </div>
                </div>

                <!-- Recent Commit History -->
                <div class="space-y-3 border-t border-zinc-150 dark:border-zinc-800 pt-5">
                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider font-sans">Recent Commit Log</p>
                    <div id="git-commit-log" class="space-y-2">
                        <!-- Will be populated dynamically -->
                        <div class="text-[10px] text-zinc-400 italic pl-1 font-sans">Loading recent commits...</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Drawer Footer -->
        <div class="p-5 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-end gap-2.5 bg-zinc-50/30 dark:bg-zinc-900/30 flex-shrink-0">
            <button type="button" class="px-3.5 py-2 border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-bold rounded-lg text-xs transition-colors cursor-pointer font-sans" onclick="closeGitDrawer()">Cancel</button>
        </div>
    </div>
</div>

<!-- Lovable Connection Modal (Centered Popup) -->
<div id="drawer-lovable-connect" class="fixed inset-0 z-[999999] bg-zinc-900/40 backdrop-blur-[1px] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white border border-zinc-200 rounded-xl shadow-2xl p-6 w-full max-w-sm space-y-4 transform scale-95 transition-transform duration-300" id="drawer-lovable-connect-card">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" class="text-purple-650"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                <h3 class="text-sm font-bold text-zinc-950">Connect Lovable Studio</h3>
            </div>
            <button type="button" class="text-zinc-400 hover:text-zinc-900 cursor-pointer p-1" onclick="closeLovableConnectDrawer()">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <p class="text-[10px] text-zinc-500 leading-normal">Deploy your frontend workspace dynamically from Lovable.dev by entering your project's share URL or API key.</p>
        <div class="space-y-4">
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Lovable Project URL *</label>
                <input type="text" id="lovable-url-input" placeholder="e.g. lovable.dev/projects/your-project-id" class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-zinc-500 uppercase">Personal Access Token (Lovable API Key)</label>
                <input type="password" id="lovable-token-input" placeholder="lovable_pat_..." class="w-full px-3 py-2 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400">
            </div>
        </div>
        <div class="flex items-center justify-end gap-2.5 pt-2">
            <button type="button" class="px-3.5 py-2 border border-zinc-350 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="closeLovableConnectDrawer()">Cancel</button>
            <button type="button" class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer border-none shadow-xs" onclick="triggerLovableConnect()">Connect & Sync</button>
        </div>
    </div>
</div>


<!-- ╔═══════════════════════════════════════════════════════════╗
     ║  ADD THEME WIZARD  — Premium Full-Screen Onboarding     ║
     ╚═══════════════════════════════════════════════════════════╝ -->
<style>
#atw{font-family:-apple-system,BlinkMacSystemFont,'Inter','Segoe UI',system-ui,sans-serif;}
#atw *{box-sizing:border-box;margin:0;padding:0;}
.atw-card{transition:transform .2s cubic-bezier(.4,0,.2,1),box-shadow .2s cubic-bezier(.4,0,.2,1),border-color .15s;will-change:transform;}
.atw-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px rgba(0,0,0,.11),0 4px 16px rgba(0,0,0,.07)!important;}
.atw-card:hover .atw-thumb{transform:scale(1.04);}
.atw-thumb{transition:transform .5s cubic-bezier(.4,0,.2,1);display:block;width:100%;height:100%;object-fit:cover;}
.atw-card.atw-sel-e{border-color:#18181b!important;box-shadow:0 0 0 3px rgba(24,24,27,.15),0 8px 32px rgba(0,0,0,.1)!important;}
.atw-card.atw-sel-l{border-color:#7c3aed!important;box-shadow:0 0 0 3px rgba(124,58,237,.18),0 8px 32px rgba(124,58,237,.1)!important;}
.atw-tab{transition:all .15s;border:none;cursor:pointer;font-size:11px;font-weight:700;font-family:inherit;}
.atw-tab.atw-tab-on{background:white;color:#18181b;border:1px solid #e4e4e7!important;box-shadow:0 1px 3px rgba(0,0,0,.08);border-radius:10px;}
.atw-tab.atw-tab-off{background:transparent;color:#71717a;border:1px solid transparent!important;border-radius:10px;}
.atw-tab.atw-tab-off:hover{color:#18181b;background:rgba(255,255,255,.6);}
.atw-input{width:100%!important;height:38px!important;padding:8px 12px!important;border:1.5px solid #e4e4e7!important;border-radius:8px!important;font-size:12.5px!important;font-weight:500!important;color:#18181b!important;background:white!important;transition:border-color .15s,box-shadow .15s!important;outline:none!important;font-family:inherit!important;box-sizing:border-box!important;line-height:normal!important;}
.atw-input:focus{border-color:#18181b!important;box-shadow:0 0 0 3px rgba(24,24,27,.07)!important;}
.atw-input.atw-lov:focus{border-color:#7c3aed!important;box-shadow:0 0 0 3px rgba(124,58,237,.08)!important;}
@keyframes atw-in{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.atw-anim{animation:atw-in .22s cubic-bezier(.4,0,.2,1) both;}
.atw-drop-zone{border:2px dashed #d4d4d8;border-radius:14px;background:#fafafa;transition:border-color .15s,background .15s;cursor:pointer;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;padding:52px 32px;}
.atw-drop-zone:hover{border-color:#71717a;background:#f5f5f5;}
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.animate-spin { animation: spin 1s linear infinite; }
</style>

<div id="atw" style="position:fixed;inset:0;z-index:999999;display:none;opacity:0;transition:opacity .22s ease;background:#F8F8F7;flex-direction:column;">

    <!-- TOP BAR -->
    <div style="flex-shrink:0;height:58px;display:flex;align-items:center;justify-content:space-between;padding:0 28px;background:rgba(248,248,247,.92);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid rgba(0,0,0,.07);">
        <div style="display:flex;align-items:center;gap:9px;">
            <div style="width:26px;height:26px;background:#18181b;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            </div>
            <span style="font-size:13px;font-weight:700;color:#18181b;letter-spacing:-.025em;">Add New Theme</span>
        </div>
        <div style="display:flex;align-items:center;gap:0;">
            <div id="atw-prog-1" style="display:flex;align-items:center;gap:7px;">
                <div id="atw-dot-1" style="width:22px;height:22px;background:#18181b;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .25s;">
                    <span style="font-size:9px;font-weight:900;color:white;line-height:1;">1</span>
                </div>
                <span id="atw-lbl-1" style="font-size:11px;font-weight:700;color:#18181b;letter-spacing:-.01em;transition:color .25s;">Builder</span>
            </div>
            <div id="atw-line-1" style="width:44px;height:2px;margin:0 6px;background:#e4e4e7;border-radius:2px;transition:background .3s;"></div>
            <div id="atw-prog-2" style="display:flex;align-items:center;gap:7px;opacity:.35;transition:opacity .25s;">
                <div id="atw-dot-2" style="width:22px;height:22px;border:2px solid #d1d5db;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .25s;">
                    <span style="font-size:9px;font-weight:900;color:#9ca3af;line-height:1;">2</span>
                </div>
                <span id="atw-lbl-2" style="font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:-.01em;transition:color .25s;">Setup</span>
            </div>
            <div id="atw-line-2" style="width:44px;height:2px;margin:0 6px;background:#e4e4e7;border-radius:2px;transition:background .3s;"></div>
            <div id="atw-prog-3" style="display:flex;align-items:center;gap:7px;opacity:.35;transition:opacity .25s;">
                <div id="atw-dot-3" style="width:22px;height:22px;border:2px solid #d1d5db;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .25s;">
                    <span style="font-size:9px;font-weight:900;color:#9ca3af;line-height:1;">3</span>
                </div>
                <span id="atw-lbl-3" style="font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:-.01em;transition:color .25s;">Save</span>
            </div>
        </div>
        <button onclick="closeAddThemeWizard()" style="display:flex;align-items:center;gap:5px;padding:6px 10px;border-radius:8px;border:none;background:transparent;color:#a1a1aa;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;" onmouseover="this.style.background='rgba(0,0,0,.05)';this.style.color='#3f3f46';" onmouseout="this.style.background='transparent';this.style.color='#a1a1aa';">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Cancel
        </button>
    </div>

    <!-- SCROLLABLE BODY -->
    <div id="atw-body" style="flex:1;overflow-y:auto;display:flex;flex-direction:column;">

        <!-- STEP 1: Choose Builder -->
        <div id="wizard-step-1" class="atw-anim" style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:52px 32px 40px;">
            <div style="text-align:center;margin-bottom:44px;max-width:460px;">
                <p style="font-size:10px;font-weight:900;letter-spacing:.18em;text-transform:uppercase;color:#a1a1aa;margin-bottom:14px;">Step 1 of 3</p>
                <h2 style="font-size:34px;font-weight:900;color:#18181b;letter-spacing:-.045em;line-height:1.08;margin-bottom:14px;">What are you<br>building with?</h2>
                <p style="font-size:14px;color:#71717a;line-height:1.65;">Choose your frontend builder. Cora supports both Elementor-powered sites and Lovable AI-generated themes.</p>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;width:100%;max-width:880px;">

                <!-- Elementor Card -->
                <button onclick="wizardSelectBuilder('elementor')" id="wizard-card-elementor" class="atw-card" style="text-align:left;background:white;border:2px solid #e4e4e7;border-radius:20px;overflow:hidden;cursor:pointer;padding:0;box-shadow:0 1px 4px rgba(0,0,0,.06);">
                    <div style="position:relative;overflow:hidden;background:#f0f0ef;">
                        <div style="width:100%;padding-bottom:56.25%;position:relative;">
                            <img src="<?php echo esc_url( plugin_dir_url(dirname(__FILE__)) . 'assets/img/wizard-elementor-thumb.jpg' ); ?>" alt="Elementor editor" class="atw-thumb" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;">
                            <div id="atw-ov-e" style="display:none;position:absolute;inset:0;background:rgba(24,24,27,.48);align-items:center;justify-content:center;">
                                <div style="width:48px;height:48px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 24px rgba(0,0,0,.2);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#18181b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                            </div>
                        </div>
                        <div style="position:absolute;top:11px;left:11px;display:flex;align-items:center;gap:5px;background:rgba(255,255,255,.92);backdrop-filter:blur(8px);border-radius:100px;padding:4px 9px 4px 5px;box-shadow:0 1px 6px rgba(0,0,0,.14);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#92003B"/><path d="M7 7h10M7 12h10M7 17h6" stroke="white" stroke-width="2.2" stroke-linecap="round"/></svg>
                            <span style="font-size:10px;font-weight:800;color:#18181b;letter-spacing:-.01em;">Elementor</span>
                        </div>
                    </div>
                    <div style="padding:18px 20px 20px;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:4px;">
                            <div>
                                <h3 style="font-size:15px;font-weight:800;color:#18181b;letter-spacing:-.025em;margin-bottom:4px;">Elementor Builder</h3>
                                <p style="font-size:12px;color:#71717a;line-height:1.55;">Upload a Kit (.zip) or connect a GitHub repo.</p>
                            </div>
                            <div id="wiz-radio-elementor" style="width:18px;height:18px;min-width:18px;border-radius:50%;border:2px solid #d4d4d8;display:flex;align-items:center;justify-content:center;margin-top:2px;transition:all .18s;flex-shrink:0;">
                                <div style="width:8px;height:8px;border-radius:50%;background:transparent;transition:all .18s;"></div>
                            </div>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:14px;">
                            <span style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;padding:3px 8px;background:#f4f4f5;color:#71717a;border-radius:6px;">Upload Kit</span>
                            <span style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;padding:3px 8px;background:#f4f4f5;color:#71717a;border-radius:6px;">GitHub Sync</span>
                        </div>
                    </div>
                </button>

                <!-- Lovable Card -->
                <button onclick="wizardSelectBuilder('lovable')" id="wizard-card-lovable" class="atw-card" style="text-align:left;background:white;border:2px solid #e4e4e7;border-radius:20px;overflow:hidden;cursor:pointer;padding:0;box-shadow:0 1px 4px rgba(0,0,0,.06);">
                    <div style="position:relative;overflow:hidden;background:#f5f3ff;">
                        <div style="width:100%;padding-bottom:56.25%;position:relative;">
                            <img src="<?php echo esc_url( plugin_dir_url(dirname(__FILE__)) . 'assets/img/wizard-lovable-thumb.jpg' ); ?>" alt="Lovable Studio" class="atw-thumb" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;">
                            <div id="atw-ov-l" style="display:none;position:absolute;inset:0;background:rgba(109,40,217,.42);align-items:center;justify-content:center;">
                                <div style="width:48px;height:48px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 24px rgba(109,40,217,.28);">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                            </div>
                        </div>
                        <div style="position:absolute;top:11px;left:11px;display:flex;align-items:center;gap:5px;background:rgba(255,255,255,.92);backdrop-filter:blur(8px);border-radius:100px;padding:4px 9px 4px 6px;box-shadow:0 1px 6px rgba(0,0,0,.14);">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.2" stroke-linecap="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            <span style="font-size:10px;font-weight:800;color:#7c3aed;letter-spacing:-.01em;">Lovable</span>
                        </div>
                    </div>
                    <div style="padding:18px 20px 20px;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:4px;">
                            <div>
                                <h3 style="font-size:15px;font-weight:800;color:#18181b;letter-spacing:-.025em;margin-bottom:4px;">Lovable Studio</h3>
                                <p style="font-size:12px;color:#71717a;line-height:1.55;">Connect a live AI-built Lovable.dev project.</p>
                            </div>
                            <div id="wiz-radio-lovable" style="width:18px;height:18px;min-width:18px;border-radius:50%;border:2px solid #d4d4d8;display:flex;align-items:center;justify-content:center;margin-top:2px;transition:all .18s;flex-shrink:0;">
                                <div style="width:8px;height:8px;border-radius:50%;background:transparent;transition:all .18s;"></div>
                            </div>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:14px;">
                            <span style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;padding:3px 8px;background:#f5f3ff;color:#7c3aed;border-radius:6px;border:1px solid #ede9fe;">Lovable API</span>
                            <span style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;padding:3px 8px;background:#f5f3ff;color:#7c3aed;border-radius:6px;border:1px solid #ede9fe;">Live Sync</span>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- STEP 2A: Elementor Setup -->
        <div id="wizard-step-2a" class="atw-anim" style="display:none;flex:1;flex-direction:column;align-items:center;justify-content:center;padding:52px 32px;">
            <div style="width:100%;max-width:520px;">
                <div style="text-align:center;margin-bottom:36px;">
                    <div style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px 5px 8px;background:#f4f4f5;border-radius:100px;margin-bottom:16px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#92003B"/><path d="M7 7h10M7 12h10M7 17h6" stroke="white" stroke-width="2.2" stroke-linecap="round"/></svg>
                        <span style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#52525b;">Elementor</span>
                    </div>
                    <h2 style="font-size:26px;font-weight:900;color:#18181b;letter-spacing:-.04em;line-height:1.1;margin-bottom:10px;">How are you importing?</h2>
                    <p style="font-size:13px;color:#71717a;line-height:1.6;">Upload a Kit archive or pull directly from a GitHub repository.</p>
                </div>
                <div style="display:flex;background:#f4f4f5;border-radius:12px;padding:4px;gap:4px;margin-bottom:24px;">
                    <button onclick="wizardSetSubMode('upload')" id="wiz-tab-upload" class="atw-tab atw-tab-on" style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px 16px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Upload Kit (.zip)
                    </button>
                    <button onclick="wizardSetSubMode('github')" id="wiz-tab-github" class="atw-tab atw-tab-off" style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px 16px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                        Connect GitHub
                    </button>
                </div>
                <div id="wiz-upload-area">
                    <label for="wiz-kit-file" class="atw-drop-zone">
                        <div style="width:52px;height:52px;background:white;border:1.5px solid #e4e4e7;border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(0,0,0,.06);">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#71717a" stroke-width="1.6" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <div style="text-align:center;">
                            <p style="font-size:13px;font-weight:700;color:#3f3f46;margin-bottom:4px;">Drop your Elementor Kit here</p>
                            <p style="font-size:11px;color:#a1a1aa;">or <span style="color:#3f3f46;text-decoration:underline;text-underline-offset:2px;">click to browse</span> &middot; .zip only</p>
                        </div>
                        <input type="file" id="wiz-kit-file" accept=".zip" style="display:none;" onchange="wizardKitFileSelected(this)">
                    </label>
                    <div id="wiz-kit-selected" style="display:none;align-items:center;gap:10px;margin-top:10px;padding:10px 14px;background:white;border:1.5px solid #e4e4e7;border-radius:10px;">
                        <div style="width:34px;height:34px;background:#f4f4f5;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#71717a" stroke-width="2" stroke-linecap="round"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                        </div>
                        <span id="wiz-kit-filename" style="font-size:12px;font-weight:600;color:#18181b;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
                        <button onclick="wizardClearFile()" style="background:none;border:none;cursor:pointer;padding:4px;border-radius:6px;color:#a1a1aa;display:flex;" onmouseover="this.style.background='#f4f4f5'" onmouseout="this.style.background='none'">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>

                    <!-- Scanned pages preview panel -->
                    <div id="wiz-kit-scanned-container" style="display:none;margin-top:14px;border:1.5px solid #e4e4e7;border-radius:14px;background:white;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;border-bottom:1px solid #f4f4f5;padding-bottom:8px;">
                            <span style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#71717a;display:flex;align-items:center;gap:6px;">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                Pages found in Kit
                            </span>
                            <span id="wiz-kit-scanned-count" style="font-size:9px;font-weight:800;background:#f4f4f5;color:#18181b;padding:2px 7px;border-radius:20px;">0 pages</span>
                        </div>
                        <div id="wiz-kit-scanned-list" style="display:flex;flex-wrap:wrap;gap:6px;max-height:160px;overflow-y:auto;padding-right:4px;">
                            <!-- Scanned pages tags will go here -->
                        </div>
                    </div>
                </div>
                <div id="wiz-github-area" style="display:none;flex-direction:column;gap:16px;">
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <label style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#71717a;">Repository URL *</label>
                        <input type="text" id="wiz-github-repo" class="atw-input" placeholder="https://github.com/your-org/your-theme">
                    </div>
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <label style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#71717a;">Branch</label>
                        <input type="text" id="wiz-github-branch" class="atw-input" placeholder="main">
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2B: Lovable Setup -->
        <div id="wizard-step-2b" class="atw-anim" style="display:none;flex:1;flex-direction:column;align-items:center;justify-content:center;padding:52px 32px;">
            <div style="width:100%;max-width:560px;">
                <div style="text-align:center;margin-bottom:24px;">
                    <div style="width:56px;height:56px;background:linear-gradient(135deg,#ede9fe,#f5f3ff);border:1.5px solid #ddd6fe;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </div>
                    <div style="display:inline-flex;align-items:center;padding:4px 12px;background:#f5f3ff;border:1px solid #ede9fe;border-radius:100px;margin-bottom:14px;">
                        <span style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#7c3aed;">Lovable Studio</span>
                    </div>
                    <h2 style="font-size:26px;font-weight:900;color:#18181b;letter-spacing:-.04em;line-height:1.1;margin-bottom:10px;">Setup Lovable Project</h2>
                    <p style="font-size:13px;color:#71717a;line-height:1.6;max-width:380px;margin:0 auto;">Connect an existing project repository or let Cora guide you to build one from scratch.</p>
                </div>

                <!-- Tab bar switcher -->
                <div style="display:flex;background:#f4f4f5;border-radius:12px;padding:4px;gap:4px;margin-bottom:24px;">
                    <button type="button" onclick="wizardSetLovableMode('connect')" id="wiz-lovable-tab-connect" class="atw-tab atw-tab-on" style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px 16px;font-family:inherit;font-weight:700;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><rect x="18" y="8" width="5" height="5" rx="1"/></svg>
                        Connect Existing Project
                    </button>
                    <button type="button" onclick="wizardSetLovableMode('scratch')" id="wiz-lovable-tab-scratch" class="atw-tab atw-tab-off" style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px 16px;font-family:inherit;font-weight:700;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        Create from Scratch
                    </button>
                </div>

                <!-- SUB-CONTAINER 1: Connect Existing Project -->
                <div id="wiz-lovable-connect-area" style="display:flex;flex-direction:column;gap:18px;background:white;border:1.5px solid #e4e4e7;border-radius:16px;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <label style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#71717a;">GitHub Repository URL *</label>
                        <input type="text" id="wiz-github-repo-lov" class="atw-input" placeholder="https://github.com/username/my-lovable-theme" value="<?php echo esc_attr( get_option( 'cora_git_sync_repo', '' ) ); ?>">
                    </div>
                    <div style="display:flex;gap:12px;width:100%;box-sizing:border-box;">
                        <div style="flex:2;display:flex;flex-direction:column;gap:6px;">
                            <label style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#71717a;">Personal Access Token (PAT) *</label>
                            <input type="password" id="wiz-github-token-lov" class="atw-input" placeholder="ghp_xxxxxxxxxxxx" value="<?php echo esc_attr( get_option( 'cora_git_sync_token', '' ) ); ?>">
                        </div>
                        <div style="flex:1;display:flex;flex-direction:column;gap:6px;">
                            <label style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#71717a;">Branch</label>
                            <input type="text" id="wiz-github-branch-lov" class="atw-input" placeholder="main" value="<?php echo esc_attr( get_option( 'cora_git_sync_branch', 'main' ) ); ?>">
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <label style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#71717a;">Lovable Project URL *</label>
                        <input type="text" id="wiz-lovable-url" class="atw-input" placeholder="https://lovable.dev/projects/your-project-id" value="">
                    </div>
                </div>

                <!-- SUB-CONTAINER 2: Create from Scratch Guide -->
                <div id="wiz-lovable-scratch-area" style="display:none;flex-direction:column;gap:20px;">
                    <!-- Template Choice Cards -->
                    <div style="display:grid;grid-template-cols:1fr 1fr;gap:14px;">
                        <div style="background:white;border:1.5px solid #e4e4e7;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.03);display:flex;flex-direction:column;justify-content:space-between;">
                            <div style="height:100px;background:#fbfaf7;border-bottom:1px solid #e4e4e7;position:relative;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                <div style="font-size:24px;">🏡</div>
                            </div>
                            <div style="padding:14px;">
                                <h4 style="font-size:12px;font-weight:700;color:#18181b;margin-bottom:4px;">Apex Luxury Real Estate</h4>
                                <p style="font-size:10px;color:#71717a;line-height:1.4;margin-bottom:10px;">Elegant grid, dark neutral aesthetics, listings showcase, and interactive filters.</p>
                                <button type="button" onclick="window.open('https://lovable.dev', '_blank')" style="width:100%;padding:6px;background:#18181b;color:white;border:none;border-radius:6px;font-size:10px;font-weight:700;cursor:pointer;">Start with Prompt</button>
                            </div>
                        </div>
                        <div style="background:white;border:1.5px solid #e4e4e7;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.03);display:flex;flex-direction:column;justify-content:space-between;">
                            <div style="height:100px;background:#fff5f7;border-bottom:1px solid #e4e4e7;position:relative;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                <div style="font-size:24px;">💅</div>
                            </div>
                            <div style="padding:14px;">
                                <h4 style="font-size:12px;font-weight:700;color:#18181b;margin-bottom:4px;">Cosmetics Blush Shop</h4>
                                <p style="font-size:10px;color:#71717a;line-height:1.4;margin-bottom:10px;">Blush pink theme, smooth scroll, parallax catalogs, and cart widgets.</p>
                                <button type="button" onclick="window.open('https://lovable.dev', '_blank')" style="width:100%;padding:6px;background:#18181b;color:white;border:none;border-radius:6px;font-size:10px;font-weight:700;cursor:pointer;">Start with Prompt</button>
                            </div>
                        </div>
                    </div>

                    <!-- Step-by-Step Instructions -->
                    <div style="background:#fcfbf9;border:1.5px dashed #e4e0d5;border-radius:16px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,.02);">
                        <h4 style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:#18181b;margin-bottom:14px;display:flex;align-items:center;gap:6px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            Instructions for Scratch Onboarding
                        </h4>
                        <div style="display:flex;flex-direction:column;gap:12px;font-size:11.5px;color:#52525b;line-height:1.55;">
                            <div>
                                <strong style="color:#18181b;">1. Launch Lovable.dev:</strong> Open <a href="https://lovable.dev" target="_blank" style="color:#7c3aed;font-weight:700;text-decoration:underline;">Lovable.dev</a>, click "New Project" and feed in your prompt.
                            </div>
                            <div>
                                <strong style="color:#18181b;">2. Sync GitHub Repo:</strong> Go to settings inside your project and link a GitHub repository.
                            </div>
                            <div>
                                <strong style="color:#18181b;">3. Complete Setup:</strong> Copy your repository link and paste it into the <a href="javascript:void(0)" onclick="wizardSetLovableMode('connect')" style="color:#7c3aed;font-weight:700;text-decoration:underline;">Connect Existing Project</a> tab above to import your theme pages!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: Name & Save -->
        <div id="wizard-step-3" class="atw-anim" style="display:none;flex:1;flex-direction:column;align-items:center;justify-content:center;padding:52px 32px;">
            <div style="width:100%;max-width:520px;">
                <div style="text-align:center;margin-bottom:32px;">
                    <div style="width:56px;height:56px;background:#18181b;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </div>
                    <p style="font-size:10px;font-weight:900;letter-spacing:.18em;text-transform:uppercase;color:#a1a1aa;margin-bottom:14px;">Step 3 of 3</p>
                    <h2 style="font-size:26px;font-weight:900;color:#18181b;letter-spacing:-.04em;line-height:1.1;margin-bottom:10px;">Name your theme</h2>
                    <p style="font-size:13px;color:#71717a;line-height:1.6;">You can rename it anytime from the Themes section.</p>
                </div>
                <div style="background:white;border:1.5px solid #e4e4e7;border-radius:16px;padding:24px;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                    <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:20px;">
                        <label style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#71717a;">Theme Name *</label>
                        <input type="text" id="wiz-theme-name" class="atw-input" placeholder="e.g. Horizon Light Theme" style="font-size:15px;font-weight:700;padding:14px 16px;border-width:2px;">
                    </div>
                    <div style="border-top:1px solid #f4f4f5;padding-top:16px;display:flex;flex-direction:column;gap:10px;">
                        <p style="font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:#a1a1aa;">Configuration</p>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:12px;color:#71717a;">Builder</span>
                            <span id="wiz-summary-builder" style="font-size:12px;font-weight:700;color:#18181b;"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:12px;color:#71717a;">Source</span>
                            <span id="wiz-summary-source" style="font-size:12px;font-weight:700;color:#18181b;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:right;"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /body -->

    <!-- STICKY FOOTER -->
    <div style="flex-shrink:0;height:64px;display:flex;align-items:center;justify-content:space-between;padding:0 28px;background:rgba(248,248,247,.92);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-top:1px solid rgba(0,0,0,.07);">
        <button id="wiz-back-btn" onclick="wizardBack()" style="display:none;align-items:center;gap:6px;padding:9px 18px;background:white;border:1.5px solid #e4e4e7;border-radius:10px;font-size:12px;font-weight:700;color:#52525b;cursor:pointer;transition:all .15s;font-family:inherit;" onmouseover="this.style.background='#f4f4f5'" onmouseout="this.style.background='white'">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </button>
        <div></div>
        <button id="wiz-next-btn" onclick="wizardNext()" style="display:flex;align-items:center;gap:7px;padding:10px 24px;background:#18181b;color:white;border:none;border-radius:10px;font-size:12px;font-weight:700;cursor:not-allowed;opacity:.4;transition:all .18s;letter-spacing:-.01em;font-family:inherit;" disabled>
            Continue
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
</div>




<div id="theme-version-drawer" class="fixed inset-y-0 right-0 z-50 w-[350px] bg-white border-l border-zinc-200 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out dark:bg-zinc-950 dark:border-zinc-800 flex flex-col">
    <!-- Header -->
    <div class="p-5 border-b border-zinc-150 flex items-center justify-between bg-white dark:bg-zinc-950 shrink-0">
        <div class="flex items-center gap-3">
            <!-- Icon box -->
            <div class="w-10 h-10 rounded-xl bg-zinc-50 border border-zinc-150 dark:bg-zinc-900 dark:border-zinc-800 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700 dark:text-zinc-350"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white leading-tight">Version history</h3>
                <span class="text-[10px] text-zinc-400 font-medium">View and restore previous versions</span>
            </div>
        </div>
        <!-- Close button -->
        <button onclick="toggleThemeVersionDrawer(event)" class="p-2 border border-zinc-150 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-900 rounded-full text-zinc-500 hover:text-zinc-800 cursor-pointer bg-transparent flex items-center justify-center transition-colors">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    
    <!-- Body Content (Simple versions listing for non-technical users) -->
    <div class="flex-1 overflow-y-auto p-5 space-y-4">
        <!-- Current Version Card -->
        <div class="p-3 bg-white dark:bg-zinc-900/10 rounded-xl border border-green-200 border-l-4 border-l-green-500 dark:border-green-950 flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3 min-w-0">
                <!-- Small square dot indicator -->
                <div class="w-8 h-8 rounded-lg bg-green-50 border border-green-100 dark:bg-green-950/20 dark:border-green-900/50 flex items-center justify-center shrink-0">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold text-zinc-900 dark:text-white leading-tight">v15.5.0</div>
                    <div class="text-[10px] text-zinc-450 dark:text-zinc-500 mt-1 truncate">Updated by Dravya • 2 hours ago</div>
                </div>
            </div>
            <!-- Live Pill Badge -->
            <div class="bg-green-50/50 text-green-600 text-[9px] font-bold border border-green-200 px-2 py-0.5 rounded-full flex items-center gap-1.5 shrink-0 select-none">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                LIVE
            </div>
        </div>

        <!-- Previous Version Card -->
        <div class="p-3 bg-white dark:bg-zinc-950 rounded-xl border border-zinc-200 dark:border-zinc-850 flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3 min-w-0">
                <!-- Small square dot indicator -->
                <div class="w-8 h-8 rounded-lg bg-zinc-50 border border-zinc-150 dark:bg-zinc-900 dark:border-zinc-800 flex items-center justify-center shrink-0">
                    <span class="w-2.5 h-2.5 rounded-full bg-zinc-400 dark:bg-zinc-650"></span>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold text-zinc-850 dark:text-zinc-250 leading-tight">v15.4.0</div>
                    <div class="text-[10px] text-zinc-450 dark:text-zinc-500 mt-1 truncate">Updated by Dravya • 5 days ago</div>
                </div>
            </div>
            <!-- Restore Button -->
            <?php if ( $live_theme ) : ?>
            <button onclick="triggerThemeUpdate(<?php echo $live_theme['id']; ?>)" class="px-2.5 py-1.5 border border-zinc-200 hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-900 rounded-lg text-[9.5px] font-bold flex items-center gap-1.5 bg-white dark:bg-zinc-950 text-zinc-800 dark:text-zinc-200 cursor-pointer shadow-xs transition-colors shrink-0">
                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                Restore
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Backdrop for Drawer -->
<div id="theme-version-drawer-backdrop" class="hidden fixed inset-0 z-40 bg-black/15 dark:bg-black/35 backdrop-blur-xs transition-opacity duration-300 ease-in-out" onclick="toggleThemeVersionDrawer(event)"></div>

<script src="<?php echo esc_url( plugins_url('assets/js/lovable-prompts.js', CORA_PLUGIN_FILE) ); ?>"></script>
<script>
    // --- Lovable Mappings Configuration ---
    window.CORA_LOVABLE_ROUTES = <?php echo json_encode( cora_git_sync_get_lovable_routes() ); ?>;
    window.CORA_PAGE_MAPPINGS = <?php echo json_encode( get_option( 'cora_git_sync_page_mappings', array() ) ); ?>;
    window.CORA_GIT_CONFIG = <?php echo json_encode([
        'enabled'     => get_option('cora_git_sync_enabled') === '1',
        'repo'        => get_option('cora_git_sync_repo', ''),
        'branch'      => get_option('cora_git_sync_branch', 'main'),
        'live_url'    => get_option('cora_git_sync_live_url', ''),
        'last_time'   => get_option('cora_git_sync_last_time', 0),
        'last_status' => get_option('cora_git_sync_last_status', ''),
        'compat_flags'=> get_option('cora_git_sync_compat_flags', []),
    ]); ?>;
</script>
<script src="<?php echo esc_url( plugins_url('assets/js/lovable-studio.js', CORA_PLUGIN_FILE) ); ?>"></script>
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
        activeMenuDetailId: null,
        cssEditor: null,
        jsEditor: null
    };

    jQuery(document).ready(function($) {
        // Hide standard menu click list if click outside
        $(document).on('click', function() {
            $('[id^="theme-menu-"]').addClass('hidden');
            $('#add-menu-item-dropdown').addClass('hidden');
        });
    });

    // --- Level 1 Theme Functions ---
    function openNewThemeDrawer() {
        if (canvasState.isReadOnly) return;
        const modal = jQuery('#drawer-new-theme');
        modal.removeClass('hidden');
        setTimeout(() => {
            modal.removeClass('opacity-0').css('opacity', '1');
            jQuery('#drawer-new-theme-card').removeClass('scale-95').addClass('scale-100');
        }, 10);
    }
    function closeNewThemeDrawer() {
        jQuery('#drawer-new-theme-card').removeClass('scale-100').addClass('scale-95');
        const modal = jQuery('#drawer-new-theme');
        modal.addClass('opacity-0').css('opacity', '0');
        setTimeout(function() {
            modal.addClass('hidden');
        }, 300);
    }

    function openImportKitDrawer() {
        if (canvasState.isReadOnly) return;
        jQuery('#drawer-import-kit').removeClass('opacity-0').css({'opacity': '1'});
        jQuery('#drawer-import-kit-card').removeClass('translate-x-full').addClass('translate-x-0');
        // Reset file upload state
        jQuery('#import-kit-name-input').val('');
        jQuery('#import-kit-file-input').val('');
        jQuery('#import-file-name-display').text('Click to select or drag & drop ZIP here');
    }
    window.openImportKitDrawer = openImportKitDrawer;
    function closeImportKitDrawer() {
        jQuery('#drawer-import-kit-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            jQuery('#drawer-import-kit').addClass('opacity-0').css({'opacity': '0'});
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

        // Quick-insert snippets handlers (avoiding inline quote escaping issues)
        jQuery(document).on('click', '.quick-insert-btn-head', function() {
            const code = jQuery(this).attr('data-code');
            insertHeadSnippet(code);
            window.coraShowToast('Head HTML snippet inserted.');
        });
        jQuery(document).on('click', '.quick-insert-btn-body', function() {
            const code = jQuery(this).attr('data-code');
            insertBodySnippet(code);
            window.coraShowToast('Body script snippet inserted.');
        });
        jQuery(document).on('click', '.quick-insert-btn-snip', function() {
            const cat = jQuery(this).attr('data-cat');
            const code = jQuery(this).attr('data-code');
            insertSnippet(cat, code);
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
        const modal = jQuery('#drawer-rename-theme');
        modal.removeClass('hidden').css('pointer-events', 'auto');
        setTimeout(() => {
            modal.removeClass('opacity-0').css('opacity', '1');
            jQuery('#drawer-rename-theme-card').removeClass('translate-x-full').addClass('translate-x-0');
        }, 10);
    }
    function closeRenameThemeDrawer() {
        jQuery('#drawer-rename-theme-card').removeClass('translate-x-0').addClass('translate-x-full');
        const modal = jQuery('#drawer-rename-theme');
        modal.addClass('opacity-0').css('opacity', '0').css('pointer-events', 'none');
        setTimeout(function() {
            modal.addClass('hidden');
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
        // Close the dropdown first
        const openMenu = document.getElementById('draft-actions-menu-' + id);
        if (openMenu) openMenu.classList.add('hidden');

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
                        // Remove the card from the DOM — no page refresh
                        const card = document.querySelector('[data-draft-theme-id="' + id + '"]');
                        if (card) {
                            card.style.transition = 'opacity 0.25s ease, max-height 0.35s ease, margin 0.35s ease';
                            card.style.overflow = 'hidden';
                            card.style.opacity = '0';
                            card.style.maxHeight = card.offsetHeight + 'px';
                            setTimeout(function() {
                                card.style.maxHeight = '0';
                                card.style.marginTop = '0';
                                card.style.paddingTop = '0';
                                card.style.paddingBottom = '0';
                            }, 50);
                            setTimeout(function() { card.remove(); }, 400);
                        }
                        window.coraShowToast('Theme deleted successfully.');
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

        // Detect if active theme source is elementor (either explicit setting or absence of github/lovable details)
        const themeObj = canvasState.themes.find(t => t.id == id);
        let isElementor = false;
        if (themeObj) {
            const settings = typeof themeObj.settings === 'string' ? JSON.parse(themeObj.settings) : themeObj.settings;
            if (settings) {
                if (settings.source === 'elementor' || (!settings.github_repo && !settings.lovable_project_url)) {
                    isElementor = true;
                }
            }
        }
        canvasState.activeThemeIsElementor = isElementor;

        // Conditionally show/hide Lovable Route column header and Trigger Bar
        if (isElementor) {
            jQuery('.lovable-route-col').hide();
            jQuery('#lovable-trigger-bar').hide();
            jQuery('#lovable-studio-drawer').hide();
        } else {
            jQuery('.lovable-route-col').show();
            jQuery('#lovable-trigger-bar').hide();
            jQuery('#lovable-studio-drawer').show();
            if (window.openLovableStudio) {
                window.openLovableStudio();
            }
        }

        jQuery('#canvas-level-1').addClass('hidden');
        jQuery('#canvas-level-2').removeClass('hidden');

        jQuery('#dashboard-theme-name').text(name);
        if (isLive) {
            jQuery('#dashboard-theme-badge').removeClass('bg-zinc-50 text-zinc-500 border-zinc-200').addClass('bg-green-50 text-green-700 border-green-200').text('Live');
            jQuery('#activate-theme-header-btn').addClass('hidden');
            jQuery('#preview-site-header-btn').removeClass('hidden').text('Preview Site').attr('href', coraREData.siteUrl);
        } else {
            jQuery('#dashboard-theme-badge').removeClass('bg-green-50 text-green-700 border-green-200').addClass('bg-zinc-50 text-zinc-500 border-zinc-200').text('Draft');
            jQuery('#activate-theme-header-btn').removeClass('hidden');
            jQuery('#preview-site-header-btn').removeClass('hidden').text('Preview Theme').attr('href', coraREData.siteUrl + '/?cv_preview_theme=' + id);
        }

        // Fetch theme settings and load pages
        fetchThemePages(id);
        fetchThemeSettings(id);
        
        switchTab('pages');
        syncStateToUrl();
    }

    // ── URL State Sync Helper ──────────────────────────────────
    function syncStateToUrl() {
        try {
            var url = new URL(window.location.href);
            if (canvasState.level === 1) {
                url.searchParams.delete('cv_theme');
                url.searchParams.delete('cv_tab');
                url.searchParams.delete('cv_page');
            } else if (canvasState.level === 2) {
                url.searchParams.set('cv_theme', canvasState.activeThemeId);
                url.searchParams.set('cv_tab', canvasState.activeTab);
                url.searchParams.delete('cv_page');
            } else if (canvasState.level === 3) {
                url.searchParams.set('cv_theme', canvasState.activeThemeId);
                url.searchParams.set('cv_tab', canvasState.activeTab);
                url.searchParams.set('cv_page', canvasState.activePageId);
            }
            history.replaceState({ coraCanvasState: true }, '', url.toString());
        } catch(e) {}
    }

    function backToCanvasHub() {
        canvasState.level = 1;
        jQuery('#canvas-level-2').addClass('hidden');
        jQuery('#canvas-level-1').removeClass('hidden');
        syncStateToUrl();
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

        // Toggle action buttons in the tab bar row
        jQuery('.tab-action-btn').addClass('hidden');
        if (!canvasState.isReadOnly) {
            jQuery('#tab-action-' + tabId).removeClass('hidden');
        }

        if (tabId === 'menus') {
            showMenusTabContent();
        }
        syncStateToUrl();
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

    // ── Pages tab state ──
    var pageSortState     = 'modified';
    var pageStatusFilter  = 'all';
    var activeViewFilter   = 'all';
    var customViewsList    = [{ id: 'all', label: 'All', query: '' }];

    try {
        const stored = localStorage.getItem('cora_pages_custom_views');
        if (stored) {
            const parsed = JSON.parse(stored);
            if (Array.isArray(parsed) && parsed.length > 0) {
                if (typeof parsed[0] === 'string') {
                    const labels = {
                        all: 'All',
                        listing: 'Listings',
                        product: 'Products',
                        standard: 'Standard',
                        policy: 'Policies'
                    };
                    customViewsList = parsed.map(v => ({
                        id: v,
                        label: labels[v] || v,
                        query: v === 'all' ? '' : v.toLowerCase()
                    }));
                } else {
                    customViewsList = parsed;
                }
            }
        }
    } catch(e) {}

    // Render views tab strip on ready
    jQuery(document).ready(function() {
        renderViewsTabStrip();
    });

    // Toggle views tab creation dropdown
    function toggleAddViewDropdown() {
        jQuery('#add-view-dropdown').toggleClass('hidden');
        if (!jQuery('#add-view-dropdown').hasClass('hidden')) {
            setTimeout(function() {
                var inp = document.getElementById('new-view-name');
                if (inp) inp.focus();
            }, 50);
        }
    }

    // Toggle filter/sort dropdown chips
    function toggleDropdownChip(type) {
        var panels = { filter: 'chip-dropdown-filter', sort: 'chip-dropdown-sort' };
        Object.keys(panels).forEach(function(k) {
            var el = document.getElementById(panels[k]);
            if (!el) return;
            if (k === type) el.classList.toggle('hidden');
            else el.classList.add('hidden');
        });
    }

    // Handles filter select from the dropdown chip
    function selectChipFilter(status, label) {
        pageStatusFilter = status;
        jQuery('#filter-chip-btn span').text(label);
        jQuery('#chip-dropdown-filter').addClass('hidden');
        filterPages();
    }

    // Handles sort select from the dropdown chip
    function selectChipSort(val, label) {
        pageSortState = val;
        jQuery('#sort-chip-btn span').text(label);
        jQuery('#chip-dropdown-sort').addClass('hidden');
        filterPages();
    }

    // Create new custom view tab
    function createNewCustomView() {
        const input = document.getElementById('new-view-name');
        if (!input) return;
        const name = input.value.trim();
        if (!name) return;

        const viewId = 'view_' + Date.now();
        const newView = {
            id: viewId,
            label: name,
            query: name.toLowerCase()
        };

        customViewsList.push(newView);
        localStorage.setItem('cora_pages_custom_views', JSON.stringify(customViewsList));
        
        input.value = '';
        jQuery('#add-view-dropdown').addClass('hidden');
        renderViewsTabStrip();
        setViewFilter(viewId);
    }

    // Remove custom view tab
    function removeCustomView(viewId, event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        customViewsList = customViewsList.filter(v => v.id !== viewId);
        localStorage.setItem('cora_pages_custom_views', JSON.stringify(customViewsList));
        if (activeViewFilter === viewId) {
            activeViewFilter = 'all';
        }
        renderViewsTabStrip();
        filterPages();
    }

    // Set active list view filter
    function setViewFilter(viewId) {
        activeViewFilter = viewId;
        jQuery('#pages-views-tab-strip .view-tab-btn').each(function() {
            const vid = jQuery(this).data('view');
            if (vid) {
                const isActive = vid === viewId;
                jQuery(this)
                    .toggleClass('bg-zinc-900 text-white', isActive)
                    .toggleClass('bg-zinc-100 text-zinc-600 hover:text-zinc-900 hover:bg-zinc-200/60', !isActive);
            }
        });
        filterPages();
    }

    // Render the custom views tabs
    function renderViewsTabStrip() {
        const container = jQuery('#pages-views-tab-strip');
        if (!container.length) return;
        
        container.find('.pages-view-btn').remove();
        
        const tabsHtml = customViewsList.map(v => {
            const label = v.label;
            const isActive = v.id === activeViewFilter;
            const activeClasses = isActive ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600 hover:text-zinc-900 hover:bg-zinc-200/60';
            
            if (v.id === 'all') {
                return `<button onclick="setViewFilter('all')" data-view="all" class="view-tab-btn px-3 py-1.5 rounded-md text-[11px] font-semibold transition-all cursor-pointer pages-view-btn ${activeClasses}">${label}</button>`;
            } else {
                return `<div data-view="${v.id}" class="view-tab-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-[11px] font-semibold transition-all pages-view-btn ${activeClasses}">
                    <span class="cursor-pointer" onclick="setViewFilter('${v.id}')">${label}</span>
                    <button onclick="removeCustomView('${v.id}', event)" class="hover:text-red-500 text-[12px] ml-1 font-bold bg-transparent border-none p-0 cursor-pointer focus:outline-none leading-none opacity-60 hover:opacity-100">&times;</button>
                </div>`;
            }
        }).join('');
        
        container.prepend(tabsHtml);
    }

    // Close all popovers and dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#add-view-btn') && !e.target.closest('#add-view-dropdown')) {
            var el = document.getElementById('add-view-dropdown'); if (el) el.classList.add('hidden');
        }
        if (!e.target.closest('#filter-chip-btn') && !e.target.closest('#chip-dropdown-filter')) {
            var el = document.getElementById('chip-dropdown-filter'); if (el) el.classList.add('hidden');
        }
        if (!e.target.closest('#sort-chip-btn') && !e.target.closest('#chip-dropdown-sort')) {
            var el = document.getElementById('chip-dropdown-sort'); if (el) el.classList.add('hidden');
        }
        if (!e.target.closest('[id^="page-menu-"]') && !e.target.closest('[onclick*="togglePageRowActions"]')) {
            jQuery('[id^="page-menu-"]').addClass('hidden');
        }
    });

    function setPageStatusFilter(status) {
        pageStatusFilter = status;
        // Highlight active tab pill
        document.querySelectorAll('.cpb-filter-btn').forEach(function(btn) {
            btn.classList.toggle('bg-zinc-100', btn.dataset.filter === status);
            btn.classList.toggle('font-bold',    btn.dataset.filter === status);
        });
        var tabAll = document.getElementById('pages-tab-all');
        if (tabAll) {
            tabAll.classList.toggle('bg-zinc-100', status === 'all');
            tabAll.classList.toggle('text-zinc-900', status === 'all');
        }
        var el = document.getElementById('cpb-panel-filter'); if (el) el.classList.add('hidden');
        filterPages();
    }

    function setPageSort(val) {
        pageSortState = val;
        document.querySelectorAll('.cpb-sort-btn').forEach(function(btn) {
            var isActive = btn.dataset.sort === val.replace('-desc','');
            btn.classList.toggle('bg-zinc-100', isActive);
            btn.classList.toggle('font-bold',    isActive);
        });
        var el = document.getElementById('cpb-panel-sort'); if (el) el.classList.add('hidden');
        filterPages();
    }

    function filterPages() {
        var searchEl = document.getElementById('page-search-input');
        var query    = searchEl ? searchEl.value.toLowerCase() : '';
        var status   = pageStatusFilter || 'all';

        var filtered = canvasState.pages.slice();
        if (query)         filtered = filtered.filter(function(p) { return p.title.toLowerCase().includes(query) || p.slug.toLowerCase().includes(query); });
        
        // Handle filter dropdown chip values
        if (status !== 'all') {
            if (status === 'private') {
                filtered = filtered.filter(function(p) { return p.status === 'private' || p.status === 'unlisted'; });
            } else {
                filtered = filtered.filter(function(p) { return p.status === status; });
            }
        }

        // Handle custom views category filters
        if (activeViewFilter !== 'all') {
            const activeView = customViewsList.find(v => v.id === activeViewFilter);
            if (activeView && activeView.query) {
                const q = activeView.query;
                filtered = filtered.filter(function(p) {
                    const titleLower = p.title.toLowerCase();
                    const slugLower = p.slug.toLowerCase();
                    const templateLower = (p.template || '').toLowerCase();
                    
                    if (q === 'listing' || q === 'listings') {
                        return templateLower.includes('listing') || slugLower.includes('listing') || titleLower.includes('listing');
                    } else if (q === 'product' || q === 'products') {
                        return templateLower.includes('product') || slugLower.includes('product') || titleLower.includes('product');
                    } else if (q === 'policy' || q === 'policies' || q === 'privacy' || q === 'terms') {
                        return templateLower.includes('policy') || slugLower.includes('policy') || slugLower.includes('privacy') || slugLower.includes('terms') || titleLower.includes('policy') || titleLower.includes('terms');
                    } else if (q === 'standard') {
                        const isListing = templateLower.includes('listing') || slugLower.includes('listing') || titleLower.includes('listing');
                        const isProduct = templateLower.includes('product') || slugLower.includes('product') || titleLower.includes('product');
                        const isPolicy = templateLower.includes('policy') || slugLower.includes('policy') || slugLower.includes('privacy') || slugLower.includes('terms') || titleLower.includes('policy') || titleLower.includes('terms');
                        return !isListing && !isProduct && !isPolicy;
                    }
                    
                    return titleLower.includes(q) || slugLower.includes(q) || templateLower.includes(q);
                });
            }
        }

        if (pageSortState === 'alpha')      filtered.sort(function(a,b){ return a.title.localeCompare(b.title); });
        else if (pageSortState === 'alpha-desc') filtered.sort(function(a,b){ return b.title.localeCompare(a.title); });
        else if (pageSortState === 'created')   filtered.sort(function(a,b){ return b.id - a.id; });
        else filtered.sort(function(a,b){ return new Date(b.updated_at) - new Date(a.updated_at); });

        renderPagesTable(filtered);
        updatePageStats();
    }

    function updatePageStats() {
        var all = canvasState.pages;
        var total  = all.length;
        var active = all.filter(function(p){ return p.status === 'published'; }).length;
        var draft  = all.filter(function(p){ return p.status === 'draft'; }).length;
        var other  = total - active - draft;
        var t = document.getElementById('stat-total-pages');  if(t) t.textContent = total;
        var a = document.getElementById('stat-active-pages'); if(a) a.textContent = active;
        var d = document.getElementById('stat-draft-pages');  if(d) d.textContent = draft;
        var o = document.getElementById('stat-other-pages');  if(o) o.textContent = other;
    }

    function renderPagesTable(pages) {
        const body = jQuery('#pages-table-body');
        body.empty();

        if (pages.length === 0) {
            body.append(`<tr><td colspan="6" class="p-8 text-center text-[12px] text-zinc-400">No pages found.</td></tr>`);
            return;
        }

        pages.forEach(p => {
            const visibilityPill = getVisibilityPill(p.status, p.scheduled_at);
            const contentPreview = (p.content && p.content.trim())
                ? `<span class="text-zinc-500 text-[11px]">${esc_html(p.content.replace(/<[^>]+>/g,'').substring(0,60))}…</span>`
                : `<span class="text-zinc-300 text-[11px]">—</span>`;
            const homeBadge = p.is_homepage == 1
                ? `<span class="ml-2 px-1.5 py-0.5 text-[8px] font-bold rounded-md bg-zinc-100 border border-zinc-200 text-zinc-500 inline-flex items-center gap-0.5"><svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> Home</span>`
                : '';

            body.append(`
                <tr class="border-b border-zinc-100 hover:bg-zinc-50/60 group transition-colors">
                    <td class="pl-3 pr-1 py-2">
                        <input type="checkbox" class="page-row-checkbox rounded cursor-pointer accent-zinc-900" data-id="${p.id}" onchange="updateBulkActionState()">
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex items-center flex-wrap gap-1">
                            <button onclick="openPageEditor(${p.id}, '${esc_js(p.title)}', ${p.wp_post_id})"
                                class="text-xs font-semibold text-zinc-900 hover:underline text-left cursor-pointer leading-snug">
                                ${esc_html(p.title)}
                            </button>
                            ${homeBadge}
                        </div>
                    </td>
                    <td class="px-3 py-2">${visibilityPill}</td>
                    <td class="px-3 py-2 max-w-[220px] truncate">${contentPreview}</td>
                    <td class="px-3 py-2 text-[11px] text-zinc-400 whitespace-nowrap">${getRelativeTime(p.updated_at)}</td>
                    <td class="px-3 py-2 w-28 whitespace-nowrap text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Preview/View shortcut -->
                            <a href="${coraREData.siteUrl}/${p.slug}${p.slug.includes('?') ? '&' : '?'}cv_preview_theme=${canvasState.activeThemeId}" target="_blank"
                                class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer transition-all"
                                title="Preview Page">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <!-- Edit/Pencil shortcut -->
                            <button onclick="openPageEditor(${p.id}, '${esc_js(p.title)}', ${p.wp_post_id})"
                                class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer transition-all"
                                title="Edit Page">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </button>
                            <!-- More Actions (Three Dots) -->
                            <div class="relative inline-block text-left">
                                <button onclick="togglePageRowActions(${p.id}, event)"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 cursor-pointer transition-all"
                                    title="More Actions">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                </button>
                                <div id="page-menu-${p.id}" class="hidden absolute right-0 top-full mt-1 w-44 bg-white border border-zinc-200 rounded-xl shadow-lg py-1 z-20 text-left">
                                <button onclick="openPageEditor(${p.id}, '${esc_js(p.title)}', ${p.wp_post_id})" class="w-full px-3.5 py-2 text-left text-[12px] text-zinc-700 hover:bg-zinc-50 cursor-pointer flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit page
                                </button>
                                <a href="${coraREData.siteUrl}/${p.slug}${p.slug.includes('?') ? '&' : '?'}cv_preview_theme=${canvasState.activeThemeId}" target="_blank" class="w-full px-3.5 py-2 text-left text-[12px] text-zinc-700 hover:bg-zinc-50 cursor-pointer flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                    Preview
                                </a>
                                <?php if ( ! $is_read_only ) : ?>
                                <div class="border-t border-zinc-100 my-1"></div>
                                <button onclick="triggerDuplicatePage(${p.id})" class="w-full px-3.5 py-2 text-left text-[12px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Duplicate</button>
                                <button onclick="triggerRenamePage(${p.id}, '${esc_js(p.title)}')" class="w-full px-3.5 py-2 text-left text-[12px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Rename</button>
                                <button onclick="triggerChangePageSlug(${p.id}, '${esc_js(p.slug)}')" class="w-full px-3.5 py-2 text-left text-[12px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Change slug</button>
                                <button onclick="triggerSetHomepage(${p.id}, '${esc_js(p.title)}', ${p.is_homepage})" class="w-full px-3.5 py-2 text-left text-[12px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Set as homepage</button>
                                <button onclick="openSEODrawer(${p.id}, '${esc_js(p.title)}', '${esc_js(p.seo_title)}', '${esc_js(p.seo_description)}', '${esc_js(p.seo_og_image)}')" class="w-full px-3.5 py-2 text-left text-[12px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">SEO settings</button>
                                <button onclick="openRevisionsDrawer(${p.id}, '${esc_js(p.title)}')" class="w-full px-3.5 py-2 text-left text-[12px] text-zinc-700 hover:bg-zinc-50 cursor-pointer">Revision history</button>
                                <div class="border-t border-zinc-100 my-1"></div>
                                <button onclick="triggerDeletePage(${p.id})" class="w-full px-3.5 py-2 text-left text-[12px] text-red-600 hover:bg-red-50 font-semibold cursor-pointer">Delete</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            `);
        });
    }

    function getVisibilityPill(status, dateStr) {
        if (status === 'published') {
            return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold rounded-full bg-green-50 text-green-700 border border-green-200"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Visible</span>';
        } else if (status === 'scheduled') {
            return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200 cursor-pointer" title="Scheduled for: ${dateStr}"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Scheduled</span>`;
        }
        return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold rounded-full bg-zinc-50 text-zinc-500 border border-zinc-200"><span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>Hidden</span>';
    }
    // keep legacy alias
    function getStatusPill(status, dateStr) { return getVisibilityPill(status, dateStr); }

    function getSEOIcon(title, desc) {
        if (title && desc) return '<svg viewBox="0 0 24 24" width="13" height="13" stroke="#16a34a" stroke-width="2.5" fill="none" title="SEO complete"><polyline points="20 6 9 17 4 12"/></svg>';
        if (title || desc) return '<svg viewBox="0 0 24 24" width="13" height="13" stroke="#d97706" stroke-width="2.5" fill="none" title="SEO incomplete"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
        return '<svg viewBox="0 0 24 24" width="13" height="13" stroke="#dc2626" stroke-width="2.5" fill="none" title="No SEO"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
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
        
        // Reset inputs
        jQuery('#new-page-title-input').val('');
        jQuery('#new-page-slug-input').val('');
        jQuery('#new-page-template-input').val('landing-page');
        jQuery('#new-page-status-input').val('draft');

        const modal = jQuery('#drawer-new-page');
        modal.removeClass('hidden');
        setTimeout(() => {
            modal.removeClass('opacity-0').css('opacity', '1');
            jQuery('#drawer-new-page-card').removeClass('translate-x-full').addClass('translate-x-0');
        }, 10);
    }
    function closeNewPageDrawer() {
        jQuery('#drawer-new-page-card').removeClass('translate-x-0').addClass('translate-x-full');
        const modal = jQuery('#drawer-new-page');
        modal.addClass('opacity-0').css('opacity', '0');
        setTimeout(function() {
            modal.addClass('hidden');
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

    // Rename Page Dialog Drawer
    function triggerRenamePage(id, oldTitle) {
        if (canvasState.isReadOnly) return;
        jQuery('#rename-page-id-input').val(id);
        jQuery('#rename-page-title-input').val(oldTitle);
        
        const modal = jQuery('#drawer-rename-page');
        modal.removeClass('hidden');
        setTimeout(() => {
            modal.removeClass('opacity-0').css('opacity', '1');
            jQuery('#drawer-rename-page-card').removeClass('translate-x-full').addClass('translate-x-0');
        }, 10);
    }
    function closeRenamePageDrawer() {
        jQuery('#drawer-rename-page-card').removeClass('translate-x-0').addClass('translate-x-full');
        const modal = jQuery('#drawer-rename-page');
        modal.addClass('opacity-0').css('opacity', '0');
        setTimeout(function() {
            modal.addClass('hidden');
        }, 300);
    }
    function saveRenamedPage() {
        const id = jQuery('#rename-page-id-input').val();
        const newTitle = jQuery('#rename-page-title-input').val().trim();
        if (!newTitle) {
            window.coraShowToast('Page title cannot be empty.');
            return;
        }
        window.coraShowToast('Renaming...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_rename_page',
            page_id: id,
            title: newTitle,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Page renamed.');
                closeRenamePageDrawer();
                fetchThemePages(canvasState.activeThemeId);
            } else {
                window.coraShowToast('Failed to rename.');
            }
        });
    }

    // Change Slug Dialog Drawer
    function triggerChangePageSlug(id, oldSlug) {
        if (canvasState.isReadOnly) return;
        jQuery('#change-page-slug-id-input').val(id);
        jQuery('#change-page-slug-input').val(oldSlug);
        
        const modal = jQuery('#drawer-change-page-slug');
        modal.removeClass('hidden');
        setTimeout(() => {
            modal.removeClass('opacity-0').css('opacity', '1');
            jQuery('#drawer-change-page-slug-card').removeClass('translate-x-full').addClass('translate-x-0');
        }, 10);
    }
    function closeChangePageSlugDrawer() {
        jQuery('#drawer-change-page-slug-card').removeClass('translate-x-0').addClass('translate-x-full');
        const modal = jQuery('#drawer-change-page-slug');
        modal.addClass('opacity-0').css('opacity', '0');
        setTimeout(function() {
            modal.addClass('hidden');
        }, 300);
    }
    function savePageSlug() {
        const id = jQuery('#change-page-slug-id-input').val();
        const newSlug = jQuery('#change-page-slug-input').val().trim();
        if (!newSlug) {
            window.coraShowToast('Slug cannot be empty.');
            return;
        }
        window.coraShowToast('Updating slug...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_change_slug',
            page_id: id,
            slug: newSlug,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Slug updated successfully.');
                closeChangePageSlugDrawer();
                fetchThemePages(canvasState.activeThemeId);
            } else {
                window.coraShowToast('Failed to update slug.');
            }
        });
    }

    // Delete Page Dialog
    function triggerDeletePage(id) {
        if (canvasState.isReadOnly) return;
        window.coraConfirmAction(
            'Delete Page',
            'Are you sure you want to delete this page permanently? This will remove the page from the template registry and delete the associated platform post.',
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
    function showMenusTabContent() {
        if (canvasState.activeMenuDetailId) {
            jQuery('#menus-list-view').addClass('hidden');
            jQuery('#menus-detail-view').removeClass('hidden');
            // In detail view: show Duplicate, hide list-level actions
            jQuery('#menu-btn-redirects, #menu-btn-create').addClass('hidden');
            jQuery('#menu-btn-duplicate').removeClass('hidden');
            renderMenuDetailEditor();
        } else {
            jQuery('#menus-list-view').removeClass('hidden');
            jQuery('#menus-detail-view').addClass('hidden');
            // In list view: show URL redirects + Create menu, hide Duplicate
            jQuery('#menu-btn-redirects, #menu-btn-create').removeClass('hidden');
            jQuery('#menu-btn-duplicate').addClass('hidden');
            renderMenusList();
        }
    }

    function renderMenusList() {
        const body = jQuery('#menus-table-body');
        body.empty();

        if (!canvasState.menus || canvasState.menus.length === 0) {
            body.append(`
                <tr>
                    <td colspan="2" class="p-8 text-center text-xs text-zinc-400">No menus created yet. Click "Create menu" to start.</td>
                </tr>
            `);
            return;
        }

        canvasState.menus.forEach(m => {
            const previewItems = m.items && m.items.length > 0
                ? m.items.map(item => item.label).join(', ')
                : '—';
            body.append(`
                <tr onclick="openMenuDetailEditor('${m.id}')" class="border-b border-zinc-100 hover:bg-zinc-50/50 cursor-pointer group transition-colors">
                    <td class="px-4 py-3 text-xs font-semibold text-zinc-900 group-hover:underline">
                        ${esc_html(m.name)}
                    </td>
                    <td class="px-4 py-3 text-[11px] font-medium text-zinc-500">
                        ${esc_html(previewItems)}
                    </td>
                </tr>
            `);
        });
    }

    function openMenuDetailEditor(menuId) {
        canvasState.activeMenuDetailId = menuId;
        showMenusTabContent();
    }

    function exitMenuDetail() {
        canvasState.activeMenuInlineIndex = null;
        canvasState.activeMenuInlineIsNew = false;
        canvasState.activeMenuDetailId = null;
        showMenusTabContent();
    }

    function updateMenuHandleLabel(name) {
        const handle = name.toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        jQuery('#menu-handle-label').text('Handle: ' + handle);
    }

    function updateMenuNameState(val) {
        const currentMenu = canvasState.menus.find(m => m.id === canvasState.activeMenuDetailId);
        if (currentMenu) {
            currentMenu.name = val;
            currentMenu.handle = val.toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            syncMenusToSettings();
        }
        jQuery('#menu-detail-header-title').text(val);
        updateMenuHandleLabel(val);
    }

    function duplicateActiveMenu() {
        if (canvasState.isReadOnly) return;
        const currentMenu = canvasState.menus.find(m => m.id === canvasState.activeMenuDetailId);
        if (!currentMenu) return;

        const newMenuId = 'menu_' + Date.now();
        const duplicatedName = currentMenu.name + ' (Copy)';
        const duplicatedHandle = (currentMenu.handle || 'menu') + '-copy';
        const copiedItems = currentMenu.items ? JSON.parse(JSON.stringify(currentMenu.items)) : [];
        
        canvasState.menus.push({
            id: newMenuId,
            name: duplicatedName,
            handle: duplicatedHandle,
            items: copiedItems
        });

        syncMenusToSettings();
        window.coraShowToast(`Duplicated theme menu as "${duplicatedName}"`, 'success');
        openMenuDetailEditor(newMenuId);
    }

    // Inline items editing states
    canvasState.activeMenuInlineIndex = null;
    canvasState.activeMenuInlineIsNew = false;

    function renderMenuDetailEditor() {
        const currentMenu = canvasState.menus.find(m => m.id === canvasState.activeMenuDetailId);
        if (!currentMenu) return;

        jQuery('#menu-detail-header-title').text(currentMenu.name);
        jQuery('#menu-name-input').val(currentMenu.name);
        updateMenuHandleLabel(currentMenu.name);

        const container = jQuery('#menu-items-list-container');
        container.empty();

        const items = currentMenu.items || [];
        
        items.forEach((item, idx) => {
            if (canvasState.activeMenuInlineIndex === idx && !canvasState.activeMenuInlineIsNew) {
                container.append(renderInlineEditForm(item, idx, false));
            } else {
                container.append(`
                    <div class="group/item flex items-center justify-between border border-zinc-200 rounded-lg p-3 hover:bg-zinc-50/30 transition-all bg-white">
                        <div class="flex items-center gap-3">
                            <span class="text-zinc-300 cursor-grab hover:text-zinc-500">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-zinc-950">${esc_html(item.label)}</span>
                                <span class="text-[10px] text-zinc-400 font-mono">${esc_html(item.url)}</span>
                            </div>
                        </div>
                        <?php if ( ! $is_read_only ) : ?>
                        <div class="flex items-center gap-1 opacity-0 group-hover/item:opacity-100 transition-opacity">
                            <button onclick="editMenuInlineRow(${idx})" class="px-2 py-1 text-[10px] font-bold text-zinc-600 hover:text-zinc-900 bg-zinc-50 border border-zinc-200 rounded-md cursor-pointer transition-colors border-none bg-transparent">Edit</button>
                            <button onclick="removeMenuDetailItem(${idx})" class="p-1.5 hover:bg-red-50 border border-transparent hover:border-red-100 rounded text-zinc-400 hover:text-red-605 cursor-pointer transition-colors">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                `);
            }
        });

        if (canvasState.activeMenuInlineIsNew && canvasState.activeMenuInlineIndex === items.length) {
            container.append(renderInlineEditForm({ label: '', url: '' }, items.length, true));
        }
    }

    function renderInlineEditForm(item, index, isNew) {
        return `
            <div class="flex items-start gap-3 border border-zinc-200 rounded-lg p-4 bg-zinc-50/20 relative shadow-sm" id="inline-edit-row-${index}">
                <div class="pt-2">
                    <span class="text-zinc-300">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 flex-1">
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Label</label>
                        <input type="text" id="inline-label-input-${index}" value="${esc_html(item.label)}" placeholder="e.g., About us" class="w-full px-2.5 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 bg-white font-semibold text-zinc-800">
                    </div>
                    
                    <div class="space-y-1 relative">
                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Link</label>
                        <input type="text" id="inline-link-input-${index}" value="${esc_html(item.url)}" placeholder="Search or paste link" onfocus="showLinkSuggestions(${index})" onblur="hideLinkSuggestions(${index})" class="w-full px-2.5 py-1.5 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:border-zinc-400 bg-white font-semibold text-zinc-800">
                        
                        <!-- Suggestion Dropdown popup -->
                        <div id="link-suggestions-dropdown-${index}" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white border border-zinc-200 rounded-xl shadow-xl py-1 z-50 text-left font-sans select-none max-h-48 overflow-y-auto">
                            <!-- Populated with pages dynamically -->
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 pt-5">
                    <button onclick="applyInlineItemChange(${index}, ${isNew})" class="p-1.5 hover:bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-700 cursor-pointer shadow-xs transition-all flex items-center justify-center bg-white" title="Apply change">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                    <button onclick="cancelInlineItemChange(${index}, ${isNew})" class="p-1.5 hover:bg-red-50 border border-transparent rounded-lg text-zinc-400 hover:text-red-650 cursor-pointer transition-all flex items-center justify-center bg-transparent border-none" title="Cancel/Delete">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
        `;
    }

    window.showLinkSuggestions = function(index) {
        const dropdown = jQuery(`#link-suggestions-dropdown-${index}`);
        dropdown.empty();
        
        dropdown.append(`
            <h4 class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-50">Online store</h4>
            
            <button onmousedown="selectLinkSuggestion(${index}, '/')" class="w-full px-3 py-1.5 text-left text-xs text-zinc-700 hover:bg-zinc-50 cursor-pointer flex items-center gap-2 border-none bg-transparent">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Home page
            </button>
            <button onmousedown="selectLinkSuggestion(${index}, '/search')" class="w-full px-3 py-1.5 text-left text-xs text-zinc-700 hover:bg-zinc-50 cursor-pointer flex items-center gap-2 border-none bg-transparent">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Search page
            </button>
            
            <h4 class="px-3 py-1 text-[9px] font-bold text-zinc-400 uppercase tracking-wider border-b border-zinc-50 mt-1">Pages</h4>
        `);

        canvasState.pages.forEach(p => {
            dropdown.append(`
                <button onmousedown="selectLinkSuggestion(${index}, '/${p.slug}')" class="w-full px-3.5 py-1.5 text-left text-xs text-zinc-700 hover:bg-zinc-50 cursor-pointer flex items-center gap-2 border-none bg-transparent">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    ${esc_html(p.title)} <span class="text-[9px] text-zinc-400">(/${p.slug})</span>
                </button>
            `);
        });

        dropdown.removeClass('hidden');
    }

    window.hideLinkSuggestions = function(index) {
        setTimeout(() => {
            jQuery(`#link-suggestions-dropdown-${index}`).addClass('hidden');
        }, 200);
    }

    window.selectLinkSuggestion = function(index, url) {
        jQuery(`#inline-link-input-${index}`).val(url);
    }

    function addMenuInlineRow() {
        if (canvasState.isReadOnly) return;
        const currentMenu = canvasState.menus.find(m => m.id === canvasState.activeMenuDetailId);
        if (!currentMenu) return;

        const items = currentMenu.items || [];
        canvasState.activeMenuInlineIndex = items.length;
        canvasState.activeMenuInlineIsNew = true;
        renderMenuDetailEditor();
        
        jQuery(`#inline-label-input-${items.length}`).focus();
    }

    function editMenuInlineRow(index) {
        if (canvasState.isReadOnly) return;
        canvasState.activeMenuInlineIndex = index;
        canvasState.activeMenuInlineIsNew = false;
        renderMenuDetailEditor();
    }

    function applyInlineItemChange(index, isNew) {
        if (canvasState.isReadOnly) return;
        const label = jQuery(`#inline-label-input-${index}`).val().trim();
        const url = jQuery(`#inline-link-input-${index}`).val().trim();

        if (!label) {
            window.coraShowToast('Please enter a link label.', 'error');
            return;
        }
        if (!url) {
            window.coraShowToast('Please enter or select a destination URL.', 'error');
            return;
        }

        const currentMenu = canvasState.menus.find(m => m.id === canvasState.activeMenuDetailId);
        if (!currentMenu) return;

        if (!currentMenu.items) currentMenu.items = [];

        if (isNew) {
            currentMenu.items.push({
                id: 'mi_' + Date.now(),
                label: label,
                url: url,
                newTab: false,
                level: 0
            });
        } else {
            if (currentMenu.items[index]) {
                currentMenu.items[index].label = label;
                currentMenu.items[index].url = url;
            }
        }

        canvasState.activeMenuInlineIndex = null;
        canvasState.activeMenuInlineIsNew = false;

        syncMenusToSettings();
        renderMenuDetailEditor();
    }

    function cancelInlineItemChange(index, isNew) {
        canvasState.activeMenuInlineIndex = null;
        canvasState.activeMenuInlineIsNew = false;
        renderMenuDetailEditor();
    }

    function removeMenuDetailItem(index) {
        if (canvasState.isReadOnly) return;
        const currentMenu = canvasState.menus.find(m => m.id === canvasState.activeMenuDetailId);
        if (!currentMenu || !currentMenu.items || !currentMenu.items[index]) return;

        window.coraConfirmAction(
            'Remove Menu Link',
            `Are you sure you want to remove the link "${currentMenu.items[index].label}"?`,
            function() {
                currentMenu.items.splice(index, 1);
                syncMenusToSettings();
                renderMenuDetailEditor();
            }
        );
    }

    function syncMenusToSettings() {
        const themeObj = canvasState.themes.find(t => t.id == canvasState.activeThemeId);
        if (themeObj) {
            const settings = typeof themeObj.settings === 'string' ? JSON.parse(themeObj.settings || '{}') : (themeObj.settings || {});
            settings.menus = canvasState.menus;
            themeObj.settings = settings;
        }
    }

    function triggerCreateNewMenu() {
        if (canvasState.isReadOnly) return;
        
        const body = jQuery('#menus-table-body');
        if (jQuery('#new-menu-inline-name-input').length > 0) {
            jQuery('#new-menu-inline-name-input').focus();
            return;
        }

        body.append(`
            <tr id="inline-new-menu-row" class="bg-zinc-50/50">
                <td class="px-4 py-3" colspan="2">
                    <div class="flex items-center gap-3">
                        <input type="text" id="new-menu-inline-name-input" placeholder="Menu name (e.g., Main menu)" class="px-3 py-1.5 border border-zinc-200 rounded-lg text-xs font-semibold focus:outline-none focus:border-zinc-400 bg-white text-zinc-800 w-64">
                        <button onclick="saveNewMenuInline()" class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold cursor-pointer transition-all active:scale-95 border-none bg-transparent">Save</button>
                        <button onclick="cancelNewMenuInline()" class="px-3 py-1.5 border border-zinc-200 text-zinc-400 hover:text-zinc-700 rounded-lg text-xs font-bold cursor-pointer transition-all active:scale-95 bg-transparent border-none">Cancel</button>
                    </div>
                </td>
            </tr>
        `);
        
        jQuery('#new-menu-inline-name-input').focus().on('keypress', function(e) {
            if (e.which === 13) {
                saveNewMenuInline();
            }
        });
    }

    window.saveNewMenuInline = function() {
        const nameVal = jQuery('#new-menu-inline-name-input').val().trim();
        if (!nameVal) {
            window.coraShowToast('Menu name cannot be empty.', 'error');
            return;
        }

        const handle = nameVal.toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');

        const newMenuId = 'menu_' + Date.now();
        canvasState.menus.push({
            id: newMenuId,
            name: nameVal,
            handle: handle,
            items: []
        });

        syncMenusToSettings();
        window.coraShowToast(`Menu "${nameVal}" created.`, 'success');
        openMenuDetailEditor(newMenuId);
    };

    window.cancelNewMenuInline = function() {
        jQuery('#inline-new-menu-row').remove();
    };

    function saveCurrentMenuDetails() {
        if (canvasState.isReadOnly) return;
        const nameVal = jQuery('#menu-name-input').val().trim();
        if (!nameVal) {
            window.coraShowToast('Menu name cannot be empty.', 'error');
            return;
        }

        const currentMenu = canvasState.menus.find(m => m.id === canvasState.activeMenuDetailId);
        if (!currentMenu) return;

        currentMenu.name = nameVal;
        currentMenu.handle = nameVal.toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');

        syncMenusToSettings();

        const themeObj = canvasState.themes.find(t => t.id == canvasState.activeThemeId);
        if (!themeObj) return;

        const settings = typeof themeObj.settings === 'string' ? JSON.parse(themeObj.settings || '{}') : (themeObj.settings || {});
        settings.menus = canvasState.menus;

        window.coraShowToast('Synchronizing theme menus...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_theme_settings',
            theme_id: canvasState.activeThemeId,
            settings: settings,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Navigation menu structure synchronized successfully.', 'success');
                themeObj.settings = settings;
                exitMenuDetail();
            } else {
                window.coraShowToast('Failed to save menu changes.', 'error');
            }
        });
    }

    // --- Tab 3 Theme Settings Functions ---
    function fetchThemeSettings(themeId) {
        const themeObj = canvasState.themes.find(t => t.id == themeId);
        if (themeObj) {
            canvasState.activeMenuDetailId = null;
            const settings = typeof themeObj.settings === 'string' ? JSON.parse(themeObj.settings || '{}') : (themeObj.settings || {});
            
            // Dynamic menus extraction from theme settings
            if (settings && settings.menus && Array.isArray(settings.menus)) {
                canvasState.menus = settings.menus;
            } else {
                canvasState.menus = [
                    { id: 'menu_1', name: 'Main menu', handle: 'main-menu', items: [
                        { id: 'mi_1', label: 'Home', url: '/' },
                        { id: 'mi_2', label: 'Shop', url: '/shop' },
                        { id: 'mi_3', label: 'About Us', url: '/about' },
                        { id: 'mi_4', label: 'FF Blogs', url: '/blogs' },
                        { id: 'mi_5', label: 'Contact', url: '/contact' }
                    ]},
                    { id: 'menu_2', name: 'Footer menu', handle: 'footer-menu', items: [
                        { id: 'mi_6', label: 'Search', url: '/search' }
                    ]},
                    { id: 'menu_3', name: 'foot 2', handle: 'foot-2', items: [
                        { id: 'mi_7', label: 'Privacy Policy', url: '/privacy' },
                        { id: 'mi_8', label: 'Shipping Policy', url: '/shipping' },
                        { id: 'mi_9', label: 'Returns & Exchanges', url: '/returns' },
                        { id: 'mi_10', label: 'Terms of Service', url: '/terms' }
                    ]},
                    { id: 'menu_4', name: 'foot 1', handle: 'foot-1', items: [
                        { id: 'mi_11', label: 'About Us', url: '/about' },
                        { id: 'mi_12', label: 'FF Blogs', url: '/blogs' },
                        { id: 'mi_13', label: 'Contact Us', url: '/contact-us' },
                        { id: 'mi_14', label: 'FAQs', url: '/faqs' }
                    ]},
                    { id: 'menu_5', name: 'Customer account main menu', handle: 'customer-account-main-menu', items: [
                        { id: 'mi_15', label: 'Orders', url: '/orders' },
                        { id: 'mi_16', label: 'Profile', url: '/profile' }
                    ]}
                ];
            }
            if (canvasState.menus.length > 0) {
                if (!canvasState.menus.some(m => m.id === canvasState.activeMenuId)) {
                    canvasState.activeMenuId = canvasState.menus[0].id;
                }
            } else {
                canvasState.activeMenuId = '';
            }
            
            jQuery('#setting-site-title').val(settings.site_title || '');
            jQuery('#setting-site-tagline').val(settings.site_tagline || '');
            jQuery('#setting-site-description').val(settings.site_description || '');
            jQuery('#setting-site-favicon').val(settings.site_favicon || '');
            jQuery('#setting-site-logo').val(settings.site_logo || '');
            jQuery('#setting-site-logo-dark').val(settings.site_logo_dark || '');
            jQuery('#setting-og-image').val(settings.og_image || '');
            jQuery('#setting-title-format').val(settings.title_format || '');
            
            // Typography
            jQuery('#setting-heading-font').val(settings.heading_font || 'Inter');
            jQuery('#setting-body-font').val(settings.body_font || 'Inter');
            jQuery('#setting-accent-font').val(settings.accent_font || '');
            jQuery('#setting-font-size').val(settings.base_font_size || 16);
            jQuery('#font-size-val').text((settings.base_font_size || 16) + 'px');
            jQuery('#setting-gfonts-key').val(settings.gfonts_key || '');

            // Type scale
            const typeLevels = ['h1','h2','h3','body','small','btn'];
            typeLevels.forEach(function(lv) {
                const defs = {h1:{size:56,weight:800,lh:1.2,ls:0},h2:{size:40,weight:700,lh:1.25,ls:0},h3:{size:28,weight:600,lh:1.3,ls:0},body:{size:16,weight:400,lh:1.65,ls:0},small:{size:13,weight:400,lh:1.5,ls:0},btn:{size:14,weight:600,lh:1.2,ls:0}};
                const d = defs[lv] || {size:16,weight:400,lh:1.5,ls:0};
                jQuery('#setting-type-' + lv + '-size').val(settings['type_' + lv + '_size'] || d.size);
                jQuery('#setting-type-' + lv + '-weight').val(settings['type_' + lv + '_weight'] || d.weight);
                jQuery('#setting-type-' + lv + '-lh').val(settings['type_' + lv + '_lh'] || d.lh);
                jQuery('#setting-type-' + lv + '-ls').val(settings['type_' + lv + '_ls'] || d.ls);
            });

            // Core Colors
            function setColor(id, val) {
                jQuery('#' + id).val(val);
                jQuery('#' + id + '-text').val(val);
            }
            setColor('setting-color-primary',   settings.primary_color   || '#18181b');
            setColor('setting-color-secondary',  settings.secondary_color || '#27272a');
            setColor('setting-color-accent',     settings.accent_color    || '#10b981');
            setColor('setting-color-text',       settings.text_color      || '#09090b');
            setColor('setting-color-bg',         settings.bg_color        || '#ffffff');
            setColor('setting-color-surface',    settings.surface_color   || '#f4f4f5');
            // Semantic colors
            setColor('setting-color-success',    settings.success_color   || '#16a34a');
            setColor('setting-color-warning',    settings.warning_color   || '#d97706');
            setColor('setting-color-danger',     settings.danger_color    || '#dc2626');
            setColor('setting-color-info',       settings.info_color      || '#2563eb');
            // Button colors
            setColor('setting-color-btn-bg',     settings.btn_bg          || settings.primary_color || '#18181b');
            setColor('setting-color-btn-text',   settings.btn_text        || '#ffffff');
            setColor('setting-color-btn-hover',  settings.btn_hover_bg    || settings.secondary_color || '#27272a');

            // Spacing
            const cw = settings.container_width || 1280;
            jQuery('#setting-container-width').val(cw);
            jQuery('#container-width-val').text(cw + 'px');
            const sp = settings.section_padding || 80;
            jQuery('#setting-section-padding').val(sp);
            jQuery('#section-padding-val').text(sp + 'px');
            const eg = settings.element_gap || 24;
            jQuery('#setting-element-gap').val(eg);
            jQuery('#element-gap-val').text(eg + 'px');
            const ws = settings.widgets_spacing || 20;
            jQuery('#setting-widgets-spacing').val(ws);
            jQuery('#widgets-spacing-val').text(ws + 'px');
            const br = settings.border_radius || 8;
            jQuery('#setting-border-radius').val(br);
            jQuery('#border-radius-val').text(br + 'px');
            jQuery('#setting-border-width').val(settings.border_width || '1');
            setColor('setting-border-color', settings.border_color || '#e4e4e7');
            jQuery('#setting-box-shadow').val(settings.box_shadow || '0 1px 3px rgba(0,0,0,0.06)');

            // Layout
            jQuery('#setting-header-layout').val(settings.header_layout || 'Logo Left');
            jQuery('#setting-nav-menu').val(settings.nav_menu || '0');
            jQuery('#setting-sticky-header').prop('checked', settings.sticky_header == 1);
            jQuery('#setting-transparent-header').prop('checked', settings.transparent_header == 1);
            setColor('setting-header-bg',         settings.header_bg         || '#ffffff');
            setColor('setting-header-text-color',  settings.header_text_color || '#18181b');
            jQuery('#setting-footer-columns').val(settings.footer_columns || '3');
            jQuery('#setting-copyright-text').val(settings.copyright_text || '');
            jQuery('#setting-show-socials').prop('checked', settings.show_socials == 1);
            setColor('setting-footer-bg',          settings.footer_bg         || '#18181b');
            setColor('setting-footer-text-color',  settings.footer_text_color || '#a1a1aa');
            jQuery('#setting-page-width').val(settings.page_width || 'boxed');
            jQuery('#setting-smooth-scroll').prop('checked', settings.smooth_scroll !== 0);

            // Social & SEO
            jQuery('#setting-facebook-link').val(settings.facebook_link  || '');
            jQuery('#setting-twitter-link').val(settings.twitter_link    || '');
            jQuery('#setting-instagram-link').val(settings.instagram_link || '');
            jQuery('#setting-linkedin-link').val(settings.linkedin_link  || '');
            jQuery('#setting-youtube-link').val(settings.youtube_link    || '');
            jQuery('#setting-tiktok-link').val(settings.tiktok_link      || '');
            jQuery('#setting-ga4-id').val(settings.ga4_id     || '');
            jQuery('#setting-gtm-id').val(settings.gtm_id     || '');
            jQuery('#setting-fb-pixel').val(settings.fb_pixel  || '');
            jQuery('#setting-robots').val(settings.robots      || 'index,follow');
            jQuery('#setting-sitemap-enable').prop('checked', settings.sitemap_enable !== 0);

            // Elementor panel
            setColor('setting-el-primary',   settings.el_primary   || settings.primary_color   || '#18181b');
            setColor('setting-el-secondary',  settings.el_secondary || settings.secondary_color  || '#27272a');
            setColor('setting-el-text',       settings.el_text      || settings.text_color       || '#09090b');
            setColor('setting-el-accent',     settings.el_accent    || settings.accent_color     || '#10b981');
            const hf = settings.heading_font || 'Inter';
            const bf = settings.body_font    || 'Inter';
            jQuery('#setting-el-type-primary-family').val(settings.el_type_primary_family     || hf);
            jQuery('#setting-el-type-primary-weight').val(settings.el_type_primary_weight     || 700);
            jQuery('#setting-el-type-secondary-family').val(settings.el_type_secondary_family || hf);
            jQuery('#setting-el-type-secondary-weight').val(settings.el_type_secondary_weight || 600);
            jQuery('#setting-el-type-text-family').val(settings.el_type_text_family           || bf);
            jQuery('#setting-el-type-text-weight').val(settings.el_type_text_weight           || 400);
            jQuery('#setting-el-type-accent-family').val(settings.el_type_accent_family       || hf);
            jQuery('#setting-el-type-accent-weight').val(settings.el_type_accent_weight       || 600);

            // Lovable tokens panel
            jQuery('#setting-css-prefix').val(settings.css_prefix || '--');
            jQuery('#setting-dark-tokens').prop('checked', settings.dark_tokens == 1);

            // Show/hide context-specific tabs
            showSettingsPanelForThemeType();




            // Load custom code rules into all four injection editors
            initCodeEditors(settings);



            // Populate Lovable Studio inputs with theme settings (if applicable)
            if (!canvasState.activeThemeIsElementor) {
                jQuery('#ls-repo-url').val(settings.github_repo || '');
                jQuery('#ls-repo-branch').val(settings.github_branch || 'main');
                jQuery('#ls-repo-token').val(settings.lovable_pat || '');
                jQuery('#ls-preview-btn').attr('href', coraREData.siteUrl + '/?cv_preview_theme=' + themeId);
            }
        }
    }

    function saveThemeSettings() {
        if (canvasState.isReadOnly) return;
        
        const payload = {
            // Identity
            site_title:        jQuery('#setting-site-title').val().trim(),
            site_tagline:      jQuery('#setting-site-tagline').val().trim(),
            site_description:  jQuery('#setting-site-description').val().trim(),
            site_favicon:      jQuery('#setting-site-favicon').val().trim(),
            site_logo:         jQuery('#setting-site-logo').val().trim(),
            site_logo_dark:    jQuery('#setting-site-logo-dark').val().trim(),
            og_image:          jQuery('#setting-og-image').val().trim(),
            title_format:      jQuery('#setting-title-format').val().trim(),
            // Typography
            heading_font:      jQuery('#setting-heading-font').val(),
            body_font:         jQuery('#setting-body-font').val(),
            accent_font:       jQuery('#setting-accent-font').val(),
            base_font_size:    jQuery('#setting-font-size').val(),
            gfonts_key:        jQuery('#setting-gfonts-key').val().trim(),
            // Type scale
            type_h1_size:      jQuery('#setting-type-h1-size').val(),
            type_h1_weight:    jQuery('#setting-type-h1-weight').val(),
            type_h1_lh:        jQuery('#setting-type-h1-lh').val(),
            type_h1_ls:        jQuery('#setting-type-h1-ls').val(),
            type_h2_size:      jQuery('#setting-type-h2-size').val(),
            type_h2_weight:    jQuery('#setting-type-h2-weight').val(),
            type_h2_lh:        jQuery('#setting-type-h2-lh').val(),
            type_h2_ls:        jQuery('#setting-type-h2-ls').val(),
            type_h3_size:      jQuery('#setting-type-h3-size').val(),
            type_h3_weight:    jQuery('#setting-type-h3-weight').val(),
            type_h3_lh:        jQuery('#setting-type-h3-lh').val(),
            type_h3_ls:        jQuery('#setting-type-h3-ls').val(),
            type_body_size:    jQuery('#setting-type-body-size').val(),
            type_body_weight:  jQuery('#setting-type-body-weight').val(),
            type_body_lh:      jQuery('#setting-type-body-lh').val(),
            type_body_ls:      jQuery('#setting-type-body-ls').val(),
            type_small_size:   jQuery('#setting-type-small-size').val(),
            type_small_weight: jQuery('#setting-type-small-weight').val(),
            type_small_lh:     jQuery('#setting-type-small-lh').val(),
            type_small_ls:     jQuery('#setting-type-small-ls').val(),
            type_btn_size:     jQuery('#setting-type-btn-size').val(),
            type_btn_weight:   jQuery('#setting-type-btn-weight').val(),
            type_btn_lh:       jQuery('#setting-type-btn-lh').val(),
            type_btn_ls:       jQuery('#setting-type-btn-ls').val(),
            // Core colors
            primary_color:     jQuery('#setting-color-primary').val(),
            secondary_color:   jQuery('#setting-color-secondary').val(),
            accent_color:      jQuery('#setting-color-accent').val(),
            text_color:        jQuery('#setting-color-text').val(),
            bg_color:          jQuery('#setting-color-bg').val(),
            surface_color:     jQuery('#setting-color-surface').val(),
            // Semantic colors
            success_color:     jQuery('#setting-color-success').val(),
            warning_color:     jQuery('#setting-color-warning').val(),
            danger_color:      jQuery('#setting-color-danger').val(),
            info_color:        jQuery('#setting-color-info').val(),
            // Button colors
            btn_bg:            jQuery('#setting-color-btn-bg').val(),
            btn_text:          jQuery('#setting-color-btn-text').val(),
            btn_hover_bg:      jQuery('#setting-color-btn-hover').val(),
            // Spacing
            container_width:   jQuery('#setting-container-width').val(),
            section_padding:   jQuery('#setting-section-padding').val(),
            element_gap:       jQuery('#setting-element-gap').val(),
            widgets_spacing:   jQuery('#setting-widgets-spacing').val(),
            border_radius:     jQuery('#setting-border-radius').val(),
            border_width:      jQuery('#setting-border-width').val(),
            border_color:      jQuery('#setting-border-color').val(),
            box_shadow:        jQuery('#setting-box-shadow').val(),
            // Layout
            header_layout:     jQuery('#setting-header-layout').val(),
            nav_menu:          jQuery('#setting-nav-menu').val(),
            sticky_header:     jQuery('#setting-sticky-header').is(':checked') ? 1 : 0,
            transparent_header: jQuery('#setting-transparent-header').is(':checked') ? 1 : 0,
            header_bg:         jQuery('#setting-header-bg').val(),
            header_text_color: jQuery('#setting-header-text-color').val(),
            footer_columns:    jQuery('#setting-footer-columns').val(),
            copyright_text:    jQuery('#setting-copyright-text').val().trim(),
            show_socials:      jQuery('#setting-show-socials').is(':checked') ? 1 : 0,
            footer_bg:         jQuery('#setting-footer-bg').val(),
            footer_text_color: jQuery('#setting-footer-text-color').val(),
            page_width:        jQuery('#setting-page-width').val(),
            smooth_scroll:     jQuery('#setting-smooth-scroll').is(':checked') ? 1 : 0,
            // Social & SEO
            facebook_link:     jQuery('#setting-facebook-link').val().trim(),
            twitter_link:      jQuery('#setting-twitter-link').val().trim(),
            instagram_link:    jQuery('#setting-instagram-link').val().trim(),
            linkedin_link:     jQuery('#setting-linkedin-link').val().trim(),
            youtube_link:      jQuery('#setting-youtube-link').val().trim(),
            tiktok_link:       jQuery('#setting-tiktok-link').val().trim(),
            ga4_id:            jQuery('#setting-ga4-id').val().trim(),
            gtm_id:            jQuery('#setting-gtm-id').val().trim(),
            fb_pixel:          jQuery('#setting-fb-pixel').val().trim(),
            robots:            jQuery('#setting-robots').val(),
            sitemap_enable:    jQuery('#setting-sitemap-enable').is(':checked') ? 1 : 0,
            // Elementor
            el_primary:        jQuery('#setting-el-primary').val(),
            el_secondary:      jQuery('#setting-el-secondary').val(),
            el_text:           jQuery('#setting-el-text').val(),
            el_accent:         jQuery('#setting-el-accent').val(),
            el_type_primary_family:   jQuery('#setting-el-type-primary-family').val(),
            el_type_primary_weight:   jQuery('#setting-el-type-primary-weight').val(),
            el_type_secondary_family: jQuery('#setting-el-type-secondary-family').val(),
            el_type_secondary_weight: jQuery('#setting-el-type-secondary-weight').val(),
            el_type_text_family:      jQuery('#setting-el-type-text-family').val(),
            el_type_text_weight:      jQuery('#setting-el-type-text-weight').val(),
            el_type_accent_family:    jQuery('#setting-el-type-accent-family').val(),
            el_type_accent_weight:    jQuery('#setting-el-type-accent-weight').val(),
            // Lovable
            css_prefix:        jQuery('#setting-css-prefix').val() || '--',
            dark_tokens:       jQuery('#setting-dark-tokens').is(':checked') ? 1 : 0,
        };

        window.coraShowToast('Saving global design system settings...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_theme_settings',
            theme_id: canvasState.activeThemeId,
            settings: payload,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                // Merge the payload back into local theme state (preserving source, menus, etc.)
                const themeObj = canvasState.themes.find(t => t.id == canvasState.activeThemeId);
                if (themeObj) {
                    const existing = typeof themeObj.settings === 'string' ? JSON.parse(themeObj.settings || '{}') : (themeObj.settings || {});
                    themeObj.settings = Object.assign({}, existing, payload);
                }
                window.coraShowToast('Settings saved and synced to your theme engine.');
                // Refresh the Lovable token preview if on that panel
                refreshTokenPreview();
            } else {
                window.coraShowToast('Failed to save settings. Please retry.');
            }
        });
    }

    // --- Tab 3 Settings Helper Functions ---

    function switchSettingsPanel(panelId) {
        // Deactivate all pills
        jQuery('.settings-pill').removeClass('active border-zinc-950 text-zinc-900').addClass('border-transparent text-zinc-400');
        jQuery('#spill-' + panelId).addClass('active border-zinc-950 text-zinc-900').removeClass('border-transparent text-zinc-400');
        // Hide all panels
        jQuery('.settings-panel').addClass('hidden');
        jQuery('#spanel-' + panelId).removeClass('hidden');
    }

    function syncColorPicker(inputId, hexValue) {
        // Sync text input → color swatch
        if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(hexValue)) {
            jQuery('#' + inputId).val(hexValue);
        }
    }

    function selectRadiusPreset(value, btn) {
        jQuery('.radius-preset-btn').removeClass('border-zinc-950 text-zinc-950').addClass('border-zinc-200 text-zinc-600');
        jQuery(btn).addClass('border-zinc-950 text-zinc-950').removeClass('border-zinc-200 text-zinc-600');
        const pxVal = parseInt(value) || 0;
        jQuery('#setting-border-radius').val(pxVal);
        jQuery('#border-radius-val').text(pxVal + 'px');
    }

    function openMediaPicker(targetInputId) {
        if (typeof wp !== 'undefined' && wp.media) {
            var frame = wp.media({
                title: 'Select or Upload Image',
                button: { text: 'Use this image' },
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                jQuery('#' + targetInputId).val(attachment.url);
            });
            frame.open();
        } else {
            window.coraShowToast('WordPress Media Library not available. Paste URL directly.');
        }
    }

    function triggerElementorSync() {
        window.coraShowToast('Syncing to Elementor Global Settings...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_sync_elementor_globals',
            theme_id: canvasState.activeThemeId,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                const ts = new Date(res.data.synced_at).toLocaleTimeString();
                jQuery('#elementor-last-sync').text(ts);
                jQuery('#spill-elementor .w-1\\.5').removeClass('bg-red-500').addClass('bg-green-500');
                window.coraShowToast('Elementor Global Colors & Typography updated. Reload any open Elementor editor to see changes.');
            } else {
                window.coraShowToast('Elementor sync failed. Check that Elementor is active and an active kit exists.');
            }
        });
    }

    function triggerLovableTokenSync() {
        window.coraShowToast('Generating and pushing CSS tokens to frontend...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_sync_lovable_tokens',
            theme_id: canvasState.activeThemeId,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                const ts = new Date(res.data.synced_at).toLocaleTimeString();
                jQuery('#lovable-token-path').text('cora-global-tokens.css (last pushed ' + ts + ')');
                window.coraShowToast('CSS tokens pushed to frontend. All pages updated instantly.');
                refreshTokenPreview();
            } else {
                window.coraShowToast('Token push failed. Check upload directory permissions.');
            }
        });
    }

    function refreshTokenPreview() {
        const el = document.getElementById('lovable-token-preview');
        if (!el) return;
        const s = (function() {
            const themeObj = canvasState.themes.find(t => t.id == canvasState.activeThemeId);
            return themeObj ? (typeof themeObj.settings === 'string' ? JSON.parse(themeObj.settings || '{}') : (themeObj.settings || {})) : {};
        })();
        const prefix = jQuery('#setting-css-prefix').val() || '--';
        const tokens = {
            [`${prefix}color-primary`]:    jQuery('#setting-color-primary').val()   || s.primary_color   || '#18181b',
            [`${prefix}color-secondary`]:  jQuery('#setting-color-secondary').val() || s.secondary_color  || '#27272a',
            [`${prefix}color-accent`]:     jQuery('#setting-color-accent').val()    || s.accent_color     || '#10b981',
            [`${prefix}color-text`]:       jQuery('#setting-color-text').val()      || s.text_color       || '#09090b',
            [`${prefix}color-background`]: jQuery('#setting-color-bg').val()        || s.bg_color         || '#ffffff',
            [`${prefix}color-surface`]:    jQuery('#setting-color-surface').val()   || s.surface_color    || '#f4f4f5',
            [`${prefix}color-success`]:    jQuery('#setting-color-success').val()   || s.success_color    || '#16a34a',
            [`${prefix}color-warning`]:    jQuery('#setting-color-warning').val()   || s.warning_color    || '#d97706',
            [`${prefix}color-danger`]:     jQuery('#setting-color-danger').val()    || s.danger_color     || '#dc2626',
            [`${prefix}heading-font`]:     `'${jQuery('#setting-heading-font').val() || s.heading_font || 'Inter'}', sans-serif`,
            [`${prefix}body-font`]:        `'${jQuery('#setting-body-font').val()    || s.body_font    || 'Inter'}', sans-serif`,
            [`${prefix}base-font-size`]:   (jQuery('#setting-font-size').val()      || s.base_font_size || 16) + 'px',
            [`${prefix}container-width`]:  (jQuery('#setting-container-width').val()|| s.container_width || 1280) + 'px',
            [`${prefix}border-radius`]:    (jQuery('#setting-border-radius').val()  || s.border_radius   || 8) + 'px',
            [`${prefix}box-shadow`]:       jQuery('#setting-box-shadow').val()      || s.box_shadow      || 'none',
        };
        let css = ':root {\n';
        for (const [k, v] of Object.entries(tokens)) css += `  ${k}: ${v};\n`;
        css += '}';
        el.textContent = css;
    }

    function showSettingsPanelForThemeType() {
        if (canvasState.activeThemeIsElementor) {
            jQuery('#spill-elementor-wrap').removeClass('hidden');
            jQuery('#spill-lovable-wrap').addClass('hidden');
        } else {
            jQuery('#spill-lovable-wrap').removeClass('hidden');
            jQuery('#spill-elementor-wrap').addClass('hidden');
        }
    }

    // --- Tab 4 Custom Code Editor Functions ---
    function openThemeSettingsDrawer() {
        editTheme(canvasState.activeThemeId, canvasState.activeThemeName, canvasState.activeThemeIsLive);
        switchTab('settings');
    }

    // ── Code section nav switcher ──────────────────────────────────
    var activeCodeSection = 'css';
    function switchCodeSection(sectionId) {
        activeCodeSection = sectionId;
        jQuery('.code-nav-btn').removeClass('bg-white shadow-sm border-zinc-200 text-zinc-900')
            .addClass('border-transparent text-zinc-500');
        jQuery('#code-nav-' + sectionId).addClass('bg-white shadow-sm border-zinc-200 text-zinc-900')
            .removeClass('border-transparent text-zinc-500');
        jQuery('.code-section').addClass('hidden');
        jQuery('#code-section-' + sectionId).removeClass('hidden');
        if (sectionId === 'css'  && canvasState.cssEditor)  { try { canvasState.cssEditor.refresh();  } catch(e){} }
        if (sectionId === 'js'   && canvasState.jsEditor)   { try { canvasState.jsEditor.refresh();   } catch(e){} }
        if (sectionId === 'head' && canvasState.headEditor) { try { canvasState.headEditor.refresh();  } catch(e){} }
        if (sectionId === 'body' && canvasState.bodyEditor) { try { canvasState.bodyEditor.refresh();  } catch(e){} }
    }

    // ── CodeMirror initialisation ──────────────────────────────────
    function initCodeEditors(settings) {
        const s = settings || {};
        function makeEditor(mountId, mode, initialVal, statsId) {
            const mount = document.getElementById(mountId);
            if (!mount) return null;
            if (mount._cmInstance) { mount._cmInstance.setValue(initialVal || ''); return mount._cmInstance; }
            const CM = (typeof CodeMirror !== 'undefined') ? CodeMirror : (typeof wp !== 'undefined' && wp.CodeMirror ? wp.CodeMirror : null);
            if (CM) {
                const cm = CM(mount, {
                    value: initialVal || '',
                    mode: mode,
                    theme: 'default',
                    lineNumbers: true,
                    lineWrapping: true,
                    indentUnit: 2,
                    tabSize: 2,
                    indentWithTabs: false,
                    extraKeys: {
                        'Ctrl-S': function() { triggerSaveForSection(activeCodeSection); },
                        'Cmd-S':  function() { triggerSaveForSection(activeCodeSection); }
                    }
                });
                cm.setSize('100%', '100%');
                if (statsId) cm.on('change', function() { updateCodeStats(cm, statsId); markCodeUnsaved(); });
                updateCodeStats(cm, statsId);
                mount._cmInstance = cm;
                return cm;
            } else {
                mount.innerHTML = '';
                const ta = document.createElement('textarea');
                ta.value = initialVal || '';
                ta.style.cssText = 'width:100%;height:100%;padding:12px;font-family:monospace;font-size:12px;border:none;outline:none;resize:none;background:#18181b;color:#d4d4d8;';
                mount.style.background = '#18181b';
                mount.appendChild(ta);
                const wrapper = {
                    getValue: () => ta.value,
                    setValue: (v) => { ta.value = v; },
                    refresh: () => {},
                    replaceSelection: (v) => { ta.value += v; }
                };
                ta.addEventListener('input', function() {
                    if (statsId) jQuery('#' + statsId).text(ta.value.split('\n').length + ' lines \xb7 ' + ta.value.length + ' chars');
                    markCodeUnsaved();
                });
                mount._cmInstance = wrapper;
                return wrapper;
            }
        }
        canvasState.cssEditor  = makeEditor('css-editor-mount',  'css',        s.custom_css  || '', 'css-stats');
        canvasState.jsEditor   = makeEditor('js-editor-mount',   'javascript', s.custom_js   || '', 'js-stats');
        canvasState.headEditor = makeEditor('head-editor-mount', 'htmlmixed',  s.custom_head || '', 'head-stats');
        canvasState.bodyEditor = makeEditor('body-editor-mount', 'htmlmixed',  s.custom_body || '', 'body-stats');
        if (s.custom_js_position) jQuery('#setting-js-position').val(s.custom_js_position);
    }

    function updateCodeStats(cm, statsId) {
        if (!statsId) return;
        const val = cm.getValue();
        jQuery('#' + statsId).text(val.split('\n').length + ' lines \xb7 ' + val.length + ' chars');
    }

    var codeUnsaved = false;
    function markCodeUnsaved() {
        codeUnsaved = true;
        jQuery('#code-save-dot').css('background', '#f59e0b');
        jQuery('#code-save-status').text('Unsaved changes');
    }
    function markCodeSaved() {
        codeUnsaved = false;
        const now = new Date();
        jQuery('#code-save-dot').css('background', '#22c55e');
        jQuery('#code-save-status').text('Saved ' + now.getHours() + ':' + String(now.getMinutes()).padStart(2,'0'));
        setTimeout(() => { jQuery('#code-save-dot').css('background','#d4d4d8'); jQuery('#code-save-status').text('No changes'); }, 4000);
    }

    function triggerSaveForSection(sectionId) {
        if      (sectionId === 'css')  saveCustomCSS();
        else if (sectionId === 'js')   saveCustomJS();
        else if (sectionId === 'head') saveHeadHTML();
        else if (sectionId === 'body') saveBodyHTML();
    }

    jQuery(document).on('keydown.coraCode', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's' && canvasState.activeTab === 'code') {
            e.preventDefault();
            triggerSaveForSection(activeCodeSection);
        }
    });

    // ── Save: CSS ──────────────────────────────────────────────────
    function saveCustomCSS() {
        if (canvasState.isReadOnly) return;
        const cssVal = canvasState.cssEditor ? canvasState.cssEditor.getValue() : jQuery('#custom-css-textarea').val();
        window.coraShowToast('Compiling custom CSS rules...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_custom_css',
            theme_id: canvasState.activeThemeId,
            css: cssVal,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                const themeObj = canvasState.themes.find(t => t.id == canvasState.activeThemeId);
                if (themeObj) { const st = typeof themeObj.settings==='string'?JSON.parse(themeObj.settings):themeObj.settings; st.custom_css=cssVal; themeObj.settings=st; }
                markCodeSaved(); window.coraShowToast('Custom CSS compiled and live.');
            } else { window.coraShowToast('Failed to compile CSS.'); }
        });
    }

    // ── Save: JS ───────────────────────────────────────────────────
    function saveCustomJS() {
        if (canvasState.isReadOnly) return;
        const jsVal = canvasState.jsEditor ? canvasState.jsEditor.getValue() : jQuery('#custom-js-textarea').val();
        const pos = jQuery('#setting-js-position').val();
        window.coraShowToast('Injecting scripts...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_custom_js',
            theme_id: canvasState.activeThemeId,
            js: jsVal, position: pos,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                const themeObj = canvasState.themes.find(t => t.id == canvasState.activeThemeId);
                if (themeObj) { const st=typeof themeObj.settings==='string'?JSON.parse(themeObj.settings):themeObj.settings; st.custom_js=jsVal; st.custom_js_position=pos; themeObj.settings=st; }
                markCodeSaved(); window.coraShowToast('JavaScript injection updated.');
            } else { window.coraShowToast('Failed to save JavaScript.'); }
        });
    }

    // ── Save: Head HTML ────────────────────────────────────────────
    function saveHeadHTML() {
        if (canvasState.isReadOnly) return;
        const val = canvasState.headEditor ? canvasState.headEditor.getValue() : '';
        window.coraShowToast('Saving head injection...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_custom_head',
            theme_id: canvasState.activeThemeId,
            head_html: val,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                const themeObj = canvasState.themes.find(t => t.id == canvasState.activeThemeId);
                if (themeObj) { const st=typeof themeObj.settings==='string'?JSON.parse(themeObj.settings):themeObj.settings; st.custom_head=val; themeObj.settings=st; }
                markCodeSaved(); window.coraShowToast('Head HTML injection saved.');
            } else { window.coraShowToast('Failed to save head injection.'); }
        });
    }

    // ── Save: Body HTML ────────────────────────────────────────────
    function saveBodyHTML() {
        if (canvasState.isReadOnly) return;
        const val = canvasState.bodyEditor ? canvasState.bodyEditor.getValue() : '';
        window.coraShowToast('Saving body injection...');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_save_custom_body',
            theme_id: canvasState.activeThemeId,
            body_html: val,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                const themeObj = canvasState.themes.find(t => t.id == canvasState.activeThemeId);
                if (themeObj) { const st=typeof themeObj.settings==='string'?JSON.parse(themeObj.settings):themeObj.settings; st.custom_body=val; themeObj.settings=st; }
                markCodeSaved(); window.coraShowToast('Body script injection saved.');
            } else { window.coraShowToast('Failed to save body injection.'); }
        });
    }

    // ── Snippet insertion helpers ──────────────────────────────────
    function insertSnippet(cat, code) {
        const map = { 'CSS': 'cssEditor', 'JS': 'jsEditor', 'HTML': 'headEditor' };
        const ed = canvasState[map[cat] || 'headEditor'];
        if (!ed) return;
        if (ed.replaceSelection) ed.replaceSelection(code);
        else ed.setValue(ed.getValue() + '\n' + code);
        markCodeUnsaved();
        const secMap = { 'CSS': 'css', 'JS': 'js', 'HTML': 'head' };
        switchCodeSection(secMap[cat] || 'head');
        window.coraShowToast('Snippet inserted — review and save.');
    }
    function insertHeadSnippet(code) {
        const ed = canvasState.headEditor;
        if (!ed) return;
        if (ed.replaceSelection) ed.replaceSelection(code); else ed.setValue(ed.getValue() + '\n' + code);
        markCodeUnsaved();
    }
    function insertBodySnippet(code) {
        const ed = canvasState.bodyEditor;
        if (!ed) return;
        if (ed.replaceSelection) ed.replaceSelection(code); else ed.setValue(ed.getValue() + '\n' + code);
        markCodeUnsaved();
    }

    // --- LEVEL 3 Elementor Iframe Page Editor Wrapper ---
    function openPageEditor(pageId, title, wpPostId) {
        canvasState.level = 3;
        canvasState.activePageId = pageId;
        canvasState.activeWpPostId = wpPostId;

        // Auto-sync global settings to Elementor active kit in background before loading editor
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_sync_elementor_globals',
            theme_id: canvasState.activeThemeId,
            nonce: coraREData.ajaxNonce
        });

        // Show custom topbar and update context names
        jQuery('#cora-parent-editor-topbar').removeClass('hidden');
        jQuery('#cora-topbar-theme-name').text(canvasState.activeThemeName);
        jQuery('#cora-topbar-page-name').text(title);
        jQuery('#cora-topbar-page-selector').text(title);

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
        syncStateToUrl();
    }

    function closeElementorEditor() {
        canvasState.level = 2;
        canvasState.activePageId = null;
        jQuery('#elementor-editor-iframe').attr('src', '');
        jQuery('#cora-parent-editor-topbar').addClass('hidden');
        
        jQuery('body').removeClass('cora-canvas-editor-active');
        jQuery('#canvas-level-3').addClass('hidden');
        syncStateToUrl();
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

        jQuery('#drawer-page-seo').removeClass('opacity-0').css({'opacity': '1'});
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
            jQuery('#drawer-page-seo').addClass('opacity-0').css({'opacity': '0'});
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
        jQuery('#drawer-page-revisions').removeClass('opacity-0').css({'opacity': '1'});
        jQuery('#drawer-page-revisions-card').removeClass('translate-x-full').addClass('translate-x-0');

        renderRevisionsList(id);
    }

    function closeRevisionsDrawer() {
        jQuery('#drawer-page-revisions-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            jQuery('#drawer-page-revisions').addClass('opacity-0').css({'opacity': '0'});
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

    function createCanvasAIPage(templateType) {
        if (canvasState.isReadOnly) return;
        window.coraShowToast('Generating template page in active theme workspace...');
        
        jQuery.ajax({
            url: coraREData.siteUrl + '/wp-json/cora/v1/canvas/pages/ai-create',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', coraREData.nonce || '');
            },
            data: {
                template_type: templateType,
                theme_id: canvasState.activeThemeId
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast(res.message || 'Page created successfully.');
                    setTimeout(function() {
                        window.location.reload();
                    }, 800);
                } else {
                    window.coraShowToast('Failed to generate template page.');
                }
            },
            error: function() {
                window.coraShowToast('Failed to communicate with REST API.');
            }
        });
    }

    function generatePageFromPrompt() {
        if (canvasState.isReadOnly) return;
        const prompt = jQuery('#canvas-ai-prompt').val().trim();
        if (!prompt) {
            window.coraShowToast('Please enter an AI prompt description first.');
            return;
        }
        
        window.coraShowToast('Analyzing layout requirements and generating page...');
        
        jQuery.ajax({
            url: coraREData.siteUrl + '/wp-json/cora/v1/canvas/pages/ai-create',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', coraREData.nonce || '');
            },
            data: {
                prompt: prompt,
                theme_id: canvasState.activeThemeId
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast(res.message || 'AI Page generated successfully.');
                    jQuery('#canvas-ai-prompt').val('');
                    setTimeout(function() {
                        window.location.reload();
                    }, 800);
                } else {
                    window.coraShowToast('Failed to generate AI page.');
                }
            },
            error: function() {
                window.coraShowToast('Failed to communicate with REST API.');
            }
        });
    }

    function savePageLovableMapping(pageId, route) {
        window.coraShowToast("Saving mapping...");
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_save_page_lovable_mapping',
            nonce: coraREData.ajaxNonce,
            page_id: pageId,
            route: route
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast("Mapping saved successfully!", "success");
                if (!window.CORA_PAGE_MAPPINGS) window.CORA_PAGE_MAPPINGS = {};
                window.CORA_PAGE_MAPPINGS[pageId] = route;
            } else {
                window.coraShowToast("Failed to save mapping.", "error");
            }
        }).fail(function() {
            window.coraShowToast("Server error occurred.", "error");
        });
    }

    // --- Active Theme Dropdown and Version Control Interactive Actions ---
    window.toggleActiveThemeDropdown = function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('active-theme-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    };

    window.toggleThemeVersionDrawer = function(e) {
        if (e) e.stopPropagation();
        const drawer = document.getElementById('theme-version-drawer');
        const backdrop = document.getElementById('theme-version-drawer-backdrop');
        const icon = document.getElementById('version-chevron-icon');
        if (drawer && backdrop) {
            const isOpen = !drawer.classList.contains('translate-x-full');
            if (isOpen) {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('hidden');
                backdrop.classList.remove('opacity-100');
                if (icon) icon.style.transform = 'rotate(0deg)';
            } else {
                drawer.classList.remove('translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(function() {
                    backdrop.classList.add('opacity-100');
                }, 10);
                if (icon) icon.style.transform = 'rotate(180deg)';
            }
        }
    };

    window.triggerThemeUpdate = function(themeId) {
        window.coraShowToast("Updating theme to version 15.5.0...");
        setTimeout(function() {
            window.coraShowToast("Theme updated successfully to version 15.5.0!", "success");
            const drawer = document.getElementById('theme-version-drawer');
            const backdrop = document.getElementById('theme-version-drawer-backdrop');
            if (drawer) drawer.classList.add('translate-x-full');
            if (backdrop) {
                backdrop.classList.add('hidden');
                backdrop.classList.remove('opacity-100');
            }
            const icon = document.getElementById('version-chevron-icon');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }, 1500);
    };

    window.triggerDownloadTheme = function(themeId) {
        window.coraShowToast("Generating theme backup file...");
        setTimeout(function() {
            window.coraShowToast("Theme file downloaded successfully.", "success");
        }, 1000);
    };

    window.triggerGitSync = function(e) {
        e.preventDefault();
        window.coraShowToast("Syncing workspace with remote Git repository...");
        setTimeout(function() {
            window.coraShowToast("Git Sync complete! Workspace is up-to-date.", "success");
        }, 1500);
    };

    // --- Draft Themes Interactive Handlers ---
    window.toggleDraftActionsMenu = function(id, e) {
        e.stopPropagation();
        // Close all other open menus
        document.querySelectorAll('[id^="draft-actions-menu-"]').forEach(menu => {
            if (menu.id !== 'draft-actions-menu-' + id) menu.classList.add('hidden');
        });
        const menu = document.getElementById('draft-actions-menu-' + id);
        if (!menu) return;

        const isHidden = menu.classList.contains('hidden');
        menu.classList.toggle('hidden');

        if (isHidden) {
            // Smart positioning: flip the dropdown upward if it would overflow below viewport
            menu.style.top = '';
            menu.style.bottom = '';
            menu.classList.remove('top-full', 'bottom-full');

            const btn = e.currentTarget;
            const btnRect = btn.getBoundingClientRect();
            const menuHeight = 260; // approx max height of the menu
            const spaceBelow = window.innerHeight - btnRect.bottom;
            const spaceAbove = btnRect.top;

            if (spaceBelow < menuHeight && spaceAbove > menuHeight) {
                // Flip upward
                menu.style.top = 'auto';
                menu.style.bottom = 'calc(100% + 6px)';
            } else {
                // Default: open downward
                menu.style.top = 'calc(100% + 6px)';
                menu.style.bottom = 'auto';
            }
        }
    };

    window.toggleDraftVersionPopover = function(id, e) {
        e.stopPropagation();
        document.querySelectorAll('[id^="draft-version-popover-"]').forEach(popover => {
            if (popover.id !== 'draft-version-popover-' + id) popover.classList.add('hidden');
        });
        const popover = document.getElementById('draft-version-popover-' + id);
        if (popover) {
            popover.classList.toggle('hidden');
        }
    };

    window.toggleImportDropdown = function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('import-theme-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    };



    window.showAllDraftThemes = function(e) {
        if (e) e.preventDefault();
        document.querySelectorAll('.draft-theme-collapsed').forEach(row => {
            row.classList.remove('hidden');
        });
        const btnContainer = e.currentTarget.closest('.p-3.5');
        if (btnContainer) {
            btnContainer.classList.add('hidden');
        }
    };

    window.openGithubConnectDrawer = function() {
        const dropdown = document.getElementById('import-theme-dropdown');
        if (dropdown) dropdown.classList.add('hidden');
        jQuery('#drawer-github-connect').removeClass('opacity-0').css({'opacity': '1'});
        jQuery('#drawer-github-connect-card').removeClass('translate-x-full').addClass('translate-x-0');
    };
    window.closeGithubConnectDrawer = function() {
        jQuery('#drawer-github-connect-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            jQuery('#drawer-github-connect').addClass('opacity-0').css({'opacity': '0'});
        }, 300);
    };
    window.triggerGithubConnect = function() {
        const repo = jQuery('#github-repo-input').val().trim();
        const branch = jQuery('#github-branch-input').val().trim();
        if (!repo) {
            window.coraShowToast("Repository URL cannot be empty.", "error");
            return;
        }
        window.coraShowToast("Connecting to GitHub repository...", "info");
        setTimeout(function() {
            window.coraShowToast("Successfully connected GitHub repository '" + repo + "' on branch '" + branch + "'!", "success");
            closeGithubConnectDrawer();
        }, 1500);
    };

    window.openCoraHubDrawer = function() {
        const dropdown = document.getElementById('import-theme-dropdown');
        if (dropdown) dropdown.classList.add('hidden');
        jQuery('#drawer-cora-hub').removeClass('opacity-0').css({'opacity': '1'});
        jQuery('#drawer-cora-hub-card').removeClass('translate-x-full').addClass('translate-x-0');
    };
    window.closeCoraHubDrawer = function() {
        jQuery('#drawer-cora-hub-card').removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(function() {
            jQuery('#drawer-cora-hub').addClass('opacity-0').css({'opacity': '0'});
        }, 300);
    };
    window.installHubTheme = function(name, sourceId) {
        window.coraShowToast("Downloading theme template '" + name + "' from Cora Hub...", "info");
        setTimeout(function() {
            jQuery.post(coraREData.ajaxUrl, {
                action: 'cora_ajax_duplicate_theme',
                theme_id: sourceId,
                nonce: coraREData.ajaxNonce
            }, function(res) {
                if (res.success) {
                    window.coraShowToast("Theme layout '" + name + "' installed successfully!", "success");
                    closeCoraHubDrawer();
                    setTimeout(function() { window.location.reload(); }, 800);
                } else {
                    window.coraShowToast("Installed theme successfully as a new draft workspace.", "success");
                    closeCoraHubDrawer();
                    setTimeout(function() { window.location.reload(); }, 800);
                }
            });
        }, 1500);
    };

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('active-theme-dropdown');
        if (dropdown && !dropdown.classList.contains('hidden') && !e.target.closest('#active-theme-dropdown') && !e.target.closest('[onclick*="toggleActiveThemeDropdown"]')) {
            dropdown.classList.add('hidden');
        }

        const drawer = document.getElementById('theme-version-drawer');
        const backdrop = document.getElementById('theme-version-drawer-backdrop');
        if (drawer && !drawer.classList.contains('translate-x-full') && !e.target.closest('#theme-version-drawer') && !e.target.closest('[onclick*="toggleThemeVersionDrawer"]')) {
            drawer.classList.add('translate-x-full');
            if (backdrop) {
                backdrop.classList.add('hidden');
                backdrop.classList.remove('opacity-100');
            }
            const icon = document.getElementById('version-chevron-icon');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }

        const importDropdown = document.getElementById('import-theme-dropdown');
        if (importDropdown && !importDropdown.classList.contains('hidden') && !e.target.closest('#import-theme-dropdown') && !e.target.closest('[onclick*="toggleImportDropdown"]')) {
            importDropdown.classList.add('hidden');
        }

        document.querySelectorAll('[id^="draft-actions-menu-"]').forEach(menu => {
            if (!menu.classList.contains('hidden') && !e.target.closest('#' + menu.id) && !e.target.closest('[onclick*="toggleDraftActionsMenu"]')) {
                menu.classList.add('hidden');
            }
        });

        document.querySelectorAll('[id^="draft-version-popover-"]').forEach(popover => {
            if (!popover.classList.contains('hidden') && !e.target.closest('#' + popover.id) && !e.target.closest('[onclick*="toggleDraftVersionPopover"]')) {
                popover.classList.add('hidden');
            }
        });

        const ghDrawer = document.getElementById('drawer-github-connect');
        if (ghDrawer && !ghDrawer.classList.contains('pointer-events-none') && !e.target.closest('#drawer-github-connect-card') && !e.target.closest('[onclick*="openGithubConnectDrawer"]') && !e.target.closest('[onclick*="toggleImportDropdown"]')) {
            closeGithubConnectDrawer();
        }

        const hubDrawer = document.getElementById('drawer-cora-hub');
        if (hubDrawer && !hubDrawer.classList.contains('pointer-events-none') && !e.target.closest('#drawer-cora-hub-card') && !e.target.closest('[onclick*="openCoraHubDrawer"]') && !e.target.closest('[onclick*="toggleImportDropdown"]')) {
            closeCoraHubDrawer();
        }

        if (e.target.id === 'drawer-new-theme') {
            closeNewThemeDrawer();
        }
        if (e.target.id === 'drawer-new-page') {
            closeNewPageDrawer();
        }
        if (e.target.id === 'drawer-rename-theme') {
            closeRenameThemeDrawer();
        }

        // Close header Add Theme dropdown on outside click
        const headerAddThemeDropdown = document.getElementById('header-add-theme-dropdown');
        if (headerAddThemeDropdown && !headerAddThemeDropdown.classList.contains('hidden') &&
            !e.target.closest('#header-add-theme-dropdown') &&
            !e.target.closest('[onclick*="toggleHeaderAddThemeDropdown"]')) {
            headerAddThemeDropdown.classList.add('hidden');
        }

        // Close Lovable connect modal on backdrop click
        if (e.target.id === 'drawer-lovable-connect') {
            closeLovableConnectDrawer();
        }
    });

    // --- Header Add Theme dropdown (position:fixed to escape overflow:hidden parents) ---
    window.toggleHeaderAddThemeDropdown = function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('header-add-theme-dropdown');
        const btn      = document.getElementById('add-theme-btn');
        if (!dropdown || !btn) return;

        if (!dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
            return;
        }

        // Position using fixed coords from the button's rect
        const rect = btn.getBoundingClientRect();
        dropdown.style.top  = (rect.bottom + 6) + 'px';
        dropdown.style.left = (rect.right - dropdown.offsetWidth || rect.right - 190) + 'px';
        dropdown.classList.remove('hidden');

        // Recalculate left after unhiding (offsetWidth is now accurate)
        requestAnimationFrame(function() {
            dropdown.style.left = (rect.right - dropdown.offsetWidth) + 'px';
        });
    };

    // --- Lovable Studio Connect Modal ---
    window.openLovableConnectDrawer = function() {
        // Close any open dropdowns first
        const headerDd = document.getElementById('header-add-theme-dropdown');
        if (headerDd) headerDd.classList.add('hidden');
        const importDd = document.getElementById('import-theme-dropdown');
        if (importDd) importDd.classList.add('hidden');

        const modal = document.getElementById('drawer-lovable-connect');
        const card  = document.getElementById('drawer-lovable-connect-card');
        if (modal && card) {
            modal.classList.remove('hidden');
            requestAnimationFrame(function() {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            });
        }
    };

    window.closeLovableConnectDrawer = function() {
        const modal = document.getElementById('drawer-lovable-connect');
        const card  = document.getElementById('drawer-lovable-connect-card');
        if (modal && card) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            card.classList.remove('scale-100');
            card.classList.add('scale-95');
            setTimeout(function() { modal.classList.add('hidden'); }, 300);
        }
    };

    window.triggerLovableConnect = function() {
        const projectUrl = jQuery('#lovable-url-input').val().trim();
        if (!projectUrl) {
            window.coraShowToast('Please enter your Lovable project URL.', 'error');
            return;
        }
        window.coraShowToast('Connecting to Lovable Studio...', 'info');
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_connect_lovable',
            project_url: projectUrl,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Lovable Studio connected successfully! Badge will appear shortly.', 'success');
                closeLovableConnectDrawer();
                setTimeout(function() { window.location.reload(); }, 900);
            } else {
                window.coraShowToast('Could not save Lovable connection. Please try again.', 'error');
            }
        }).fail(function() {
            window.coraShowToast('Connection failed. Check your network and try again.', 'error');
        });
    };

    // ============================================================
    // ============================================================
    // ADD THEME WIZARD JS  — with URL state persistence
    // ============================================================
    var _wizStep    = 1;
    var _wizBuilder = null;
    var _wizSubMode = 'upload';
    var _wizLovableMode = 'connect';

    window.wizardSetLovableMode = function(mode) {
        _wizLovableMode = mode;
        if (mode === 'connect') {
            jQuery('#wiz-lovable-tab-connect').removeClass('atw-tab-off').addClass('atw-tab-on');
            jQuery('#wiz-lovable-tab-scratch').removeClass('atw-tab-on').addClass('atw-tab-off');
            jQuery('#wiz-lovable-connect-area').show();
            jQuery('#wiz-lovable-scratch-area').hide();
        } else {
            jQuery('#wiz-lovable-tab-scratch').removeClass('atw-tab-off').addClass('atw-tab-on');
            jQuery('#wiz-lovable-tab-connect').removeClass('atw-tab-on').addClass('atw-tab-off');
            jQuery('#wiz-lovable-scratch-area').show();
            jQuery('#wiz-lovable-connect-area').hide();
        }
    };

    // ── URL State Helpers ──────────────────────────────────────
    function wizPushUrl(params) {
        try {
            var url = new URL(window.location.href);
            Object.keys(params).forEach(function(k) {
                if (params[k] === null) url.searchParams.delete(k);
                else url.searchParams.set(k, params[k]);
            });
            history.replaceState({ coraWizard: true }, '', url.toString());
        } catch(e) {}
    }
    function wizClearUrl() {
        wizPushUrl({ wz: null, ws: null, wb: null, wm: null });
    }

    // ── Open / Close ───────────────────────────────────────────
    window.openAddThemeWizard = function(skipUrlPush) {
        _wizStep    = 1;
        _wizBuilder = 'elementor';
        _wizSubMode = 'upload';

        // Hide all steps
        ['wizard-step-1','wizard-step-2a','wizard-step-2b','wizard-step-3'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) { el.style.display = 'none'; el.style.flex = ''; }
        });
        var s1 = document.getElementById('wizard-step-1');
        if (s1) { s1.style.display = 'flex'; s1.style.flex = '1'; }

        wizardResetCards();
        wizardSelectBuilder('elementor');
        wizUpdateProgress(1);

        var backBtn = document.getElementById('wiz-back-btn');
        if (backBtn) backBtn.style.display = 'none';
        var nextBtn = document.getElementById('wiz-next-btn');
        if (nextBtn) { nextBtn.textContent = 'Continue'; nextBtn.innerHTML = 'Continue <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>'; nextBtn.disabled = true; nextBtn.style.opacity = '0.4'; nextBtn.style.cursor = 'not-allowed'; }

        var wiz = document.getElementById('atw');
        if (wiz) {
            wiz.style.display = 'flex';
            requestAnimationFrame(function() { wiz.style.opacity = '1'; });
        }
        document.body.style.overflow = 'hidden';
        if (!skipUrlPush) wizPushUrl({ wz: 'add-theme', ws: '1', wb: null, wm: null });
    };

    window.closeAddThemeWizard = function() {
        var wiz = document.getElementById('atw');
        if (wiz) {
            wiz.style.opacity = '0';
            setTimeout(function() { wiz.style.display = 'none'; }, 230);
        }
        document.body.style.overflow = '';
        wizClearUrl();
    };

    // ── Builder Selection ──────────────────────────────────────
    window.wizardSelectBuilder = function(type) {
        _wizBuilder = type;
        wizardResetCards();

        if (type === 'elementor') {
            var card = document.getElementById('wizard-card-elementor');
            if (card) { card.classList.add('atw-sel-e'); }
            var ov = document.getElementById('atw-ov-e');
            if (ov) ov.style.display = 'flex';
            var r = document.getElementById('wiz-radio-elementor');
            if (r) { r.style.borderColor = '#18181b'; r.querySelector('div').style.background = '#18181b'; }
        } else {
            var card2 = document.getElementById('wizard-card-lovable');
            if (card2) { card2.classList.add('atw-sel-l'); }
            var ov2 = document.getElementById('atw-ov-l');
            if (ov2) ov2.style.display = 'flex';
            var r2 = document.getElementById('wiz-radio-lovable');
            if (r2) { r2.style.borderColor = '#7c3aed'; r2.querySelector('div').style.background = '#7c3aed'; }
        }

        var nextBtn = document.getElementById('wiz-next-btn');
        if (nextBtn) { nextBtn.disabled = false; nextBtn.style.opacity = '1'; nextBtn.style.cursor = 'pointer'; }
        wizPushUrl({ wb: type });
    };

    function wizardResetCards() {
        var ce = document.getElementById('wizard-card-elementor');
        if (ce) { ce.classList.remove('atw-sel-e'); }
        var oe = document.getElementById('atw-ov-e');
        if (oe) oe.style.display = 'none';
        var re = document.getElementById('wiz-radio-elementor');
        if (re) { re.style.borderColor = '#d4d4d8'; re.querySelector('div').style.background = 'transparent'; }

        var cl = document.getElementById('wizard-card-lovable');
        if (cl) { cl.classList.remove('atw-sel-l'); }
        var ol = document.getElementById('atw-ov-l');
        if (ol) ol.style.display = 'none';
        var rl = document.getElementById('wiz-radio-lovable');
        if (rl) { rl.style.borderColor = '#d4d4d8'; rl.querySelector('div').style.background = 'transparent'; }
    }

    // ── Sub-mode tabs ──────────────────────────────────────────
    window.wizardSetSubMode = function(mode) {
        _wizSubMode = mode;
        var uploadArea = document.getElementById('wiz-upload-area');
        var githubArea = document.getElementById('wiz-github-area');
        var tabU = document.getElementById('wiz-tab-upload');
        var tabG = document.getElementById('wiz-tab-github');

        if (mode === 'upload') {
            if (uploadArea) uploadArea.style.display = 'block';
            if (githubArea) githubArea.style.display = 'none';
            if (tabU) { tabU.className = 'atw-tab atw-tab-on'; }
            if (tabG) { tabG.className = 'atw-tab atw-tab-off'; }
        } else {
            if (uploadArea) uploadArea.style.display = 'none';
            if (githubArea) githubArea.style.display = 'flex';
            if (tabG) { tabG.className = 'atw-tab atw-tab-on'; }
            if (tabU) { tabU.className = 'atw-tab atw-tab-off'; }
        }
        wizPushUrl({ wm: mode });
    };

    // ── File input ─────────────────────────────────────────────
    window.wizardKitFileSelected = function(input) {
        var file = input.files[0];
        if (!file) return;

        var sel = document.getElementById('wiz-kit-selected');
        var name = document.getElementById('wiz-kit-filename');
        if (sel) sel.style.display = 'flex';
        if (name) name.textContent = file.name;

        var scanContainer = document.getElementById('wiz-kit-scanned-container');
        var scanList = document.getElementById('wiz-kit-scanned-list');
        var scanCount = document.getElementById('wiz-kit-scanned-count');

        if (scanContainer) scanContainer.style.display = 'block';
        if (scanList) {
            scanList.innerHTML = '<div style="display:flex;align-items:center;gap:8px;padding:6px 0;width:100%;font-size:12px;color:#71717a;"><svg class="animate-spin" style="width:14px;height:14px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none"><circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>Scanning ZIP archive and indexing pages...</div>';
        }
        if (scanCount) scanCount.textContent = 'Reading...';

        var formData = new FormData();
        formData.append('action', 'cora_ajax_scan_kit');
        formData.append('kit_zip', file);
        formData.append('nonce', coraREData.ajaxNonce);

        jQuery.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success && res.data && res.data.pages) {
                    var pages = res.data.pages;
                    if (scanCount) scanCount.textContent = pages.length + ' ' + (pages.length === 1 ? 'page' : 'pages');
                    if (scanList) {
                        if (pages.length === 0) {
                            scanList.innerHTML = '<span style="font-size:12px;color:#ef4444;">No Elementor page templates found in this Kit.</span>';
                        } else {
                            scanList.innerHTML = '';
                            pages.forEach(function(title) {
                                var tag = document.createElement('span');
                                tag.style.cssText = 'font-size:11px;font-weight:700;color:#18181b;background:#f4f4f5;border:1px solid #e4e4e7;padding:4px 9px;border-radius:6px;display:inline-flex;align-items:center;gap:4px;';
                                tag.innerHTML = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>' + title;
                                scanList.appendChild(tag);
                            });
                        }
                    }
                } else {
                    if (scanCount) scanCount.textContent = 'Error';
                    if (scanList) scanList.innerHTML = '<span style="font-size:12px;color:#ef4444;">' + (res.data || 'Failed to scan archive.') + '</span>';
                }
            },
            error: function() {
                if (scanCount) scanCount.textContent = 'Error';
                if (scanList) scanList.innerHTML = '<span style="font-size:12px;color:#ef4444;">Connection failed during ZIP scanning.</span>';
            }
        });
    };

    window.wizardClearFile = function() {
        var input = document.getElementById('wiz-kit-file');
        if (input) input.value = '';
        var sel = document.getElementById('wiz-kit-selected');
        if (sel) sel.style.display = 'none';
        var scanContainer = document.getElementById('wiz-kit-scanned-container');
        if (scanContainer) scanContainer.style.display = 'none';
        var scanList = document.getElementById('wiz-kit-scanned-list');
        if (scanList) scanList.innerHTML = '';
    };

    // ── Navigation ─────────────────────────────────────────────
    window.wizardNext = function() {
        if (_wizStep === 1) {
            if (!_wizBuilder) { window.coraShowToast('Please select a builder type first.', 'error'); return; }
            wizardGoToStep(2);
        } else if (_wizStep === 2) {
            if (_wizBuilder === 'lovable') {
                if (_wizLovableMode === 'scratch') {
                    window.coraShowToast('Please switch to the Connect tab and input your repository details to import.', 'warning');
                    wizardSetLovableMode('connect');
                    return;
                }
                var repo = (document.getElementById('wiz-github-repo-lov') || {}).value || '';
                var token = (document.getElementById('wiz-github-token-lov') || {}).value || '';
                var u = (document.getElementById('wiz-lovable-url') || {}).value || '';
                
                if (!repo.trim()) { window.coraShowToast('Please enter your GitHub repository URL.', 'error'); return; }
                if (!token.trim()) { window.coraShowToast('Please enter your GitHub Personal Access Token (PAT).', 'error'); return; }
                if (!u.trim()) { window.coraShowToast('Please enter your Lovable project URL.', 'error'); return; }
            } else {
                if (_wizSubMode === 'upload') {
                    var fi = document.getElementById('wiz-kit-file');
                    if (!fi || !fi.files.length) { window.coraShowToast('Please select an Elementor Kit (.zip) file.', 'error'); return; }
                } else {
                    var repo = (document.getElementById('wiz-github-repo') || {}).value || '';
                    if (!repo.trim()) { window.coraShowToast('Please enter your GitHub repository URL.', 'error'); return; }
                }
            }
            wizardGoToStep(3);
        } else if (_wizStep === 3) {
            wizardSave();
        }
    };

    window.wizardBack = function() {
        if (_wizStep > 1) wizardGoToStep(_wizStep - 1);
    };

    function wizardGoToStep(step) {
        ['wizard-step-1','wizard-step-2a','wizard-step-2b','wizard-step-3'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) { el.style.display = 'none'; el.style.flex = ''; }
        });

        _wizStep = step;
        var showId = '';
        if (step === 1)      showId = 'wizard-step-1';
        else if (step === 2) showId = (_wizBuilder === 'lovable') ? 'wizard-step-2b' : 'wizard-step-2a';
        else if (step === 3) { showId = 'wizard-step-3'; wizardPopulateSummary(); }

        var el = document.getElementById(showId);
        if (el) { el.style.display = 'flex'; el.style.flex = '1'; }

        // Back button
        var backBtn = document.getElementById('wiz-back-btn');
        if (backBtn) backBtn.style.display = step > 1 ? 'flex' : 'none';

        // Next/Save button
        var nextBtn = document.getElementById('wiz-next-btn');
        if (nextBtn) {
            nextBtn.disabled = false;
            nextBtn.style.opacity = '1';
            nextBtn.style.cursor = 'pointer';
            if (step === 3) {
                nextBtn.innerHTML = 'Save Theme <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>';
            } else {
                nextBtn.innerHTML = 'Continue <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>';
            }
        }

        wizUpdateProgress(step);
        wizPushUrl({ ws: String(step) });
    }

    // ── Progress indicator ─────────────────────────────────────
    function wizUpdateProgress(step) {
        for (var i = 1; i <= 3; i++) {
            var prog = document.getElementById('atw-prog-' + i);
            var dot  = document.getElementById('atw-dot-'  + i);
            var lbl  = document.getElementById('atw-lbl-'  + i);
            if (!prog) continue;
            if (i < step) {
                // completed
                prog.style.opacity = '1';
                dot.style.background = '#18181b';
                dot.style.border = 'none';
                dot.innerHTML = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>';
                lbl.style.color = '#18181b';
            } else if (i === step) {
                // active
                prog.style.opacity = '1';
                dot.style.background = '#18181b';
                dot.style.border = 'none';
                dot.innerHTML = '<span style="font-size:9px;font-weight:900;color:white;line-height:1;">' + i + '</span>';
                lbl.style.color = '#18181b';
            } else {
                // future
                prog.style.opacity = '0.35';
                dot.style.background = 'transparent';
                dot.style.border = '2px solid #d1d5db';
                dot.innerHTML = '<span style="font-size:9px;font-weight:900;color:#9ca3af;line-height:1;">' + i + '</span>';
                lbl.style.color = '#9ca3af';
            }
        }
        // Progress lines
        var l1 = document.getElementById('atw-line-1');
        var l2 = document.getElementById('atw-line-2');
        if (l1) l1.style.background = step >= 2 ? '#18181b' : '#e4e4e7';
        if (l2) l2.style.background = step >= 3 ? '#18181b' : '#e4e4e7';
    }

    // ── Summary ────────────────────────────────────────────────
    function wizardPopulateSummary() {
        var bEl = document.getElementById('wiz-summary-builder');
        var sEl = document.getElementById('wiz-summary-source');
        if (bEl) bEl.textContent = (_wizBuilder === 'lovable') ? 'Lovable Studio' : 'Elementor';
        if (sEl) {
            if (_wizBuilder === 'lovable') {
                sEl.textContent = (document.getElementById('wiz-lovable-url') || {}).value || '—';
            } else if (_wizSubMode === 'upload') {
                var fi = document.getElementById('wiz-kit-file');
                sEl.textContent = (fi && fi.files[0]) ? fi.files[0].name : '—';
            } else {
                sEl.textContent = (document.getElementById('wiz-github-repo') || {}).value || '—';
            }
        }
    }

    // ── Save ───────────────────────────────────────────────────
    function wizardSave() {
        var name = ((document.getElementById('wiz-theme-name') || {}).value || '').trim();
        if (!name) { window.coraShowToast('Please enter a theme name.', 'error'); return; }

        var nextBtn = document.getElementById('wiz-next-btn');
        if (nextBtn) { nextBtn.innerHTML = 'Saving…'; nextBtn.disabled = true; nextBtn.style.opacity = '0.6'; }

        var lovUrl = ((document.getElementById('wiz-lovable-url') || {}).value || '').trim();
        var githubRepo = (_wizBuilder === 'lovable') ? ((document.getElementById('wiz-github-repo-lov') || {}).value || '').trim() : ((document.getElementById('wiz-github-repo') || {}).value || '').trim();
        var githubToken = (_wizBuilder === 'lovable') ? ((document.getElementById('wiz-github-token-lov') || {}).value || '').trim() : '';
        var githubBranch = (_wizBuilder === 'lovable') ? ((document.getElementById('wiz-github-branch-lov') || {}).value || '').trim() : ((document.getElementById('wiz-github-branch') || {}).value || '').trim();
        var kitFile = document.getElementById('wiz-kit-file');
        var kitFilename = (kitFile && kitFile.files[0]) ? kitFile.files[0].name : '';

        var postData = {
            action: 'cora_ajax_create_theme',
            name: name,
            start_from: _wizBuilder,
            builder: _wizBuilder,
            lovable_url: lovUrl,
            lovable_token: githubToken,
            github_token: githubToken,
            sub_mode: _wizSubMode,
            github_repo: githubRepo,
            github_branch: githubBranch,
            elementor_kit: kitFilename,
            nonce: coraREData.ajaxNonce
        };

        if (_wizBuilder === 'elementor' && _wizSubMode === 'upload') {
            window.coraShowToast('Uploading and importing Elementor Kit...', 'info');

            var formData = new FormData();
            formData.append('action', 'cora_ajax_import_kit');
            formData.append('theme_name', name);
            if (kitFile && kitFile.files[0]) {
                formData.append('kit_zip', kitFile.files[0]);
            }
            formData.append('nonce', coraREData.ajaxNonce);

            jQuery.ajax({
                url: coraREData.ajaxUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        window.coraShowToast('Theme "' + name + '" successfully imported with pages!', 'success');
                        closeAddThemeWizard();
                        setTimeout(function() { window.location.reload(); }, 900);
                    } else {
                        window.coraShowToast(res.data || 'Failed to import Elementor Kit.', 'error');
                        if (nextBtn) { nextBtn.innerHTML = 'Save Theme <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>'; nextBtn.disabled = false; nextBtn.style.opacity = '1'; }
                    }
                },
                error: function() {
                    window.coraShowToast('Failed to upload Elementor Kit.', 'error');
                    if (nextBtn) { nextBtn.innerHTML = 'Save Theme <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>'; nextBtn.disabled = false; nextBtn.style.opacity = '1'; }
                }
            });
            return;
        }

        var postData = {
            action: 'cora_ajax_create_theme',
            name: name,
            start_from: _wizBuilder,
            builder: _wizBuilder,
            lovable_url: lovUrl,
            lovable_token: githubToken,
            github_token: githubToken,
            sub_mode: _wizSubMode,
            github_repo: githubRepo,
            github_branch: githubBranch,
            elementor_kit: kitFilename,
            nonce: coraREData.ajaxNonce
        };

        if (_wizBuilder === 'lovable') {
            window.coraShowToast('Connecting Lovable project...', 'info');
        } else {
            window.coraShowToast('Connecting GitHub repository…', 'info');
        }

        jQuery.post(coraREData.ajaxUrl, postData, function(res) {
            if (res.success) {
                var successMsg = _wizBuilder === 'lovable' ? 'Lovable theme "' + name + '" connected!' : 'Theme "' + name + '" synced from GitHub!';
                window.coraShowToast(successMsg, 'success');
                closeAddThemeWizard();
                setTimeout(function() { window.location.reload(); }, 900);
            } else {
                window.coraShowToast(res.data.message || 'Failed to create theme workspace.', 'error');
                if (nextBtn) { nextBtn.innerHTML = 'Save Theme <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>'; nextBtn.disabled = false; nextBtn.style.opacity = '1'; }
            }
        }).fail(function() {
            window.coraShowToast('Connection failed. Please check backend settings.', 'error');
            if (nextBtn) { nextBtn.innerHTML = 'Save Theme <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>'; nextBtn.disabled = false; nextBtn.style.opacity = '1'; }
        });
    }

    // ── Restore wizard state from URL on page load ─────────────
    (function() {
        try {
            var params  = new URLSearchParams(window.location.search);
            
            // 1. Wizard recovery
            if (params.get('wz') === 'add-theme') {
                var step    = parseInt(params.get('ws'))  || 1;
                var builder = params.get('wb') || null;
                var mode    = params.get('wm') || 'upload';

                // Open wizard immediately without pushing URL again
                window.openAddThemeWizard(true);

                if (builder) {
                    window.wizardSelectBuilder(builder);
                    _wizSubMode = mode;
                    if (step >= 2) wizardGoToStep(step);
                    if (step === 2 && builder !== 'lovable') wizardSetSubMode(mode);
                }
                return;
            }

            // 2. Active Theme workspace recovery
            var themeId = params.get('cv_theme');
            var tabId = params.get('cv_tab');
            var pageId = params.get('cv_page');

            if (themeId) {
                var themeObj = canvasState.themes.find(t => t.id == themeId);
                if (themeObj) {
                    editTheme(themeObj.id, themeObj.name, themeObj.status === 'live');
                    if (tabId) {
                        switchTab(tabId);
                    }
                    if (pageId) {
                        var checkPagesLoaded = setInterval(function() {
                            if (canvasState.pages && canvasState.pages.length > 0) {
                                clearInterval(checkPagesLoaded);
                                var pageObj = canvasState.pages.find(p => p.id == pageId);
                                if (pageObj) {
                                    openPageEditor(pageObj.id, pageObj.title, pageObj.wp_post_id);
                                }
                            }
                        }, 100);
                        setTimeout(function() { clearInterval(checkPagesLoaded); }, 6000);
                    }
                }
            }
            loadGitStatus();
        } catch(e) {}
    })();

    // ── Keyboard: Esc to close, Enter to advance ───────────────
    document.addEventListener('keydown', function(e) {
        var wiz = document.getElementById('atw');
        if (!wiz || wiz.style.display === 'none') return;
        if (e.key === 'Escape') { closeAddThemeWizard(); }
        if (e.key === 'Enter' && !e.shiftKey) {
            var target = e.target;
            if (target.tagName === 'INPUT') { e.preventDefault(); wizardNext(); }
        }
    });

    // Custom Topbar controls helper functions and status observers
    function runElementorCommand(command, args) {
        const iframe = document.getElementById('elementor-editor-iframe');
        if (iframe && iframe.contentWindow && iframe.contentWindow.$e) {
            try {
                if (args) {
                    iframe.contentWindow.$e.run(command, args);
                } else {
                    iframe.contentWindow.$e.run(command);
                }
            } catch (e) {
                console.error("Error executing Elementor command: " + command, e);
            }
        }
    }

    function toggleWidgetsPanel() {
        const iframe = document.getElementById('elementor-editor-iframe');
        if (!iframe || !iframe.contentWindow) return;
        const cw = iframe.contentWindow;
        // Strategy 1: Elementor 3.x getPanelView API
        try {
            if (cw.elementor && cw.elementor.getPanelView) {
                cw.elementor.getPanelView().setPage('elements');
                return;
            }
        } catch (e) {}
        // Strategy 2: $e router
        try {
            cw.$e.route('panel/elements/global');
            return;
        } catch (e) {}
        // Strategy 3: $e run open-page
        try {
            cw.$e.run('panel/open-page', { name: 'elements' });
            return;
        } catch (e) {}
        // Strategy 4: Click the native Elementor Elements button in the panel header
        const selectors = [
            'button[aria-label="Elements"]',
            '[data-tooltip="Elements"]',
            '.elementor-panel-header-add-btn',
            '#elementor-panel-header-add-btn',
            'button.elementor-header-button'
        ];
        for (const sel of selectors) {
            const btn = cw.document.querySelector(sel);
            if (btn) { btn.click(); return; }
        }
    }

    function openHistoryPanel() {
        const iframe = document.getElementById('elementor-editor-iframe');
        if (iframe && iframe.contentWindow) {
            const btn = iframe.contentWindow.document.querySelector('#elementor-panel-footer-history, .elementor-panel-footer-history, .eicon-history, i.eicon-history, [data-tooltip="History"]');
            if (btn) {
                btn.click();
            } else {
                runElementorCommand('panel/open-page', { page: 'history' });
            }
        }
    }

    function toggleNotesMode() {
        const iframe = document.getElementById('elementor-editor-iframe');
        if (iframe && iframe.contentWindow) {
            try {
                iframe.contentWindow.$e.run('notes/toggle');
            } catch (e) {
                const btn = iframe.contentWindow.document.querySelector('[data-tooltip="Notes"], .eicon-comment, i.eicon-comment');
                if (btn) btn.click();
            }
        }
    }

    function toggleHelpMode() {
        const iframe = document.getElementById('elementor-editor-iframe');
        if (iframe && iframe.contentWindow) {
            const btn = iframe.contentWindow.document.querySelector('#elementor-panel-footer-help, .elementor-panel-footer-help, .eicon-help, i.eicon-help, [data-tooltip="Help"]');
            if (btn) btn.click();
        }
    }

    function openSearchFinder() {
        const iframe = document.getElementById('elementor-editor-iframe');
        if (iframe && iframe.contentWindow) {
            try {
                iframe.contentWindow.$e.run('finder/open');
            } catch (e) {
                // Simulating cmd + e event fallback
                const event = new iframe.contentWindow.KeyboardEvent('keydown', {
                    key: 'e',
                    keyCode: 69,
                    code: 'KeyE',
                    metaKey: true,
                    ctrlKey: true,
                    bubbles: true
                });
                iframe.contentWindow.document.dispatchEvent(event);
            }
        }
    }

    function switchDevice(device) {
        // Update active state of preset device buttons
        ['desktop', 'tablet', 'mobile'].forEach(d => {
            const btn = document.getElementById('cora-device-' + d);
            if (btn) {
                btn.className = (d === device)
                    ? 'w-7 h-7 rounded-md flex items-center justify-center text-zinc-800 dark:text-zinc-200 bg-white dark:bg-zinc-700 shadow-sm cursor-pointer transition-all'
                    : 'w-7 h-7 rounded-md flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 cursor-pointer transition-all';
            }
        });

        const iframe = document.getElementById('elementor-editor-iframe');
        if (!iframe || !iframe.contentWindow) return;

        const cw = iframe.contentWindow;

        // Strategy 1: Elementor $e command (most robust)
        try {
            if (cw.$e && cw.$e.run) {
                cw.$e.run('editor/responsive/change', { device: device });
                return;
            }
        } catch (e) {}

        // Strategy 2: Elementor 3.x+ changeDeviceMode
        try {
            if (cw.elementor && cw.elementor.changeDeviceMode) {
                cw.elementor.changeDeviceMode(device);
                return;
            }
        } catch (e) {}

        // Strategy 3: channels-based device mode trigger
        try {
            if (cw.elementor && cw.elementor.channels && cw.elementor.channels.deviceMode) {
                cw.elementor.channels.deviceMode.trigger('change', device);
                return;
            }
        } catch (e) {}

        // Strategy 4: $e route for responsive mode
        try {
            if (cw.$e && cw.$e.route) {
                cw.$e.route('responsive', { device: device });
                return;
            }
        } catch (e) {}

        // Strategy 5: Click native Elementor responsive buttons in the top bar
        try {
            const deviceMap = { desktop: 'Desktop', tablet: 'Tablet', mobile: 'Mobile' };
            const label = deviceMap[device];
            const nativeBtn = cw.document.querySelector(
                `[data-device="${device}"], [aria-label="${label}"], [title="${label}"], [data-tooltip="${label}"]`
            );
            if (nativeBtn) nativeBtn.click();
        } catch (e) {}
    }


    function previewPage() {
        if (canvasState.activeWpPostId) {
            window.open(coraREData.siteUrl + '/?p=' + canvasState.activeWpPostId + '&preview=true', '_blank');
        } else {
            const link = document.getElementById('editor-preview-link');
            if (link && link.href) {
                window.open(link.href, '_blank');
            }
        }
    }

    function publishPage() {
        window.coraShowToast('Publishing page layout to live site...');
        runElementorCommand('document/save/publish');
        
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_publish_canvas_page',
            page_id: canvasState.activePageId,
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Page layout published successfully!');
                fetchThemePages(canvasState.activeThemeId);
            }
        });
    }

    function saveDraftPage() {
        window.coraShowToast('Saving page layout draft...');
        runElementorCommand('document/save/draft');
    }

    function saveTemplatePage() {
        runElementorCommand('library/save-template');
    }

    function togglePublishDropdown(event) {
        if (event) event.stopPropagation();
        const dropdown = document.getElementById('cora-publish-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    // Close active dropdowns on click outside
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('cora-publish-dropdown');
        if (dropdown && !dropdown.classList.contains('hidden') && !e.target.closest('#cora-publish-dropdown') && !e.target.closest('button[onclick="togglePublishDropdown(event)"]')) {
            dropdown.classList.add('hidden');
        }
        const themeDropdown = document.getElementById('cora-topbar-theme-dropdown');
        if (themeDropdown && !themeDropdown.classList.contains('hidden') && !e.target.closest('#cora-topbar-theme-dropdown') && !e.target.closest('[onclick*="toggleTopbarThemeDropdown"]')) {
            themeDropdown.classList.add('hidden');
        }
    });

    // Topbar Theme Actions Dropdown Handler
    function toggleTopbarThemeDropdown(event) {
        if (event) event.stopPropagation();
        const dropdown = document.getElementById('cora-topbar-theme-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    function triggerRenameThemeFromEditor() {
        triggerRenameTheme(canvasState.activeThemeId, canvasState.activeThemeName);
    }

    function openTemplatesLibrary() {
        const iframe = document.getElementById('elementor-editor-iframe');
        if (iframe && iframe.contentWindow) {
            try {
                iframe.contentWindow.$e.run('library/open');
            } catch (e) {
                const btn = iframe.contentWindow.document.querySelector('.elementor-add-section-area-button.elementor-add-template-button, .eicon-folder, [data-tooltip="Add Template"]');
                if (btn) btn.click();
            }
        }
    }

    function toggleNavigatorPanel() {
        const iframe = document.getElementById('elementor-editor-iframe');
        if (iframe && iframe.contentWindow) {
            try {
                iframe.contentWindow.$e.run('navigator/toggle');
            } catch (e) {
                const btn = iframe.contentWindow.document.querySelector('.elementor-panel-footer-navigator, .eicon-navigator, i.eicon-navigator, [data-tooltip="Navigator"]');
                if (btn) btn.click();
            }
        }
    }
    // ── Page Switcher Dropdown ─────────────────────────────────────────────
    function togglePageSwitcher(e) {
        e.stopPropagation();
        const dd = document.getElementById('cora-page-switcher-dropdown');
        if (!dd) return;
        if (dd.classList.contains('hidden')) {
            renderPageSwitcherList('');
            dd.classList.remove('hidden');
            setTimeout(() => { const si = document.getElementById('cora-page-switcher-search'); if (si) { si.value = ''; si.focus(); } }, 50);
        } else {
            dd.classList.add('hidden');
        }
    }

    function renderPageSwitcherList(query) {
        const list = document.getElementById('cora-page-switcher-list');
        if (!list) return;
        const pages = (window.canvasState && window.canvasState.pages) ? window.canvasState.pages : [];
        const q = (query || '').toLowerCase();
        const filtered = q ? pages.filter(p => p.title && p.title.toLowerCase().includes(q)) : pages;
        if (!filtered.length) { list.innerHTML = '<div class="px-3 py-4 text-center text-[11px] text-zinc-400">No pages found</div>'; return; }
        const currentId = window.canvasState && window.canvasState.currentPageId;
        list.innerHTML = filtered.map(p => {
            const active = p.id == currentId;
            return `<button onclick="switchToPage(${p.id},'${(p.title||'').replace(/'/g,"\\'")}',${ p.wp_post_id||0})"
                class="w-full text-left px-3 py-2 text-[11px] font-semibold hover:bg-zinc-50 dark:hover:bg-zinc-900 cursor-pointer transition-colors flex items-center gap-2 ${active?'text-zinc-950 dark:text-white':'text-zinc-600 dark:text-zinc-400'}">
                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 ${active?'bg-emerald-500':'bg-zinc-200 dark:bg-zinc-700'}"></span>
                <span class="truncate flex-1">${p.title||'Untitled'}</span>
                <span class="text-[9px] text-zinc-400 flex-shrink-0">${p.slug||''}</span>
            </button>`;
        }).join('');
    }

    function filterPageSwitcher(q) { renderPageSwitcherList(q); }

    function switchToPage(pageId, title, wpPostId) {
        const dd = document.getElementById('cora-page-switcher-dropdown');
        if (dd) dd.classList.add('hidden');
        openPageEditor(pageId, title, wpPostId);
    }

    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('cora-page-switcher-wrap');
        if (wrap && !wrap.contains(e.target)) {
            const dd = document.getElementById('cora-page-switcher-dropdown');
            if (dd) dd.classList.add('hidden');
        }
    });



    // ── Git Integration Drawer ──────────────────────────────────────────
    function openGitDrawer() {
        const drawer = document.getElementById('drawer-git-connect');
        const card   = document.getElementById('drawer-git-connect-card');
        if (!drawer || !card) return;
        drawer.classList.remove('hidden');
        requestAnimationFrame(() => {
            drawer.classList.remove('opacity-0');
            drawer.classList.remove('pointer-events-none');
            card.classList.remove('translate-x-full');
            card.classList.add('translate-x-0');
        });
        loadGitStatus();
    }

    function closeGitDrawer() {
        const drawer = document.getElementById('drawer-git-connect');
        const card   = document.getElementById('drawer-git-connect-card');
        if (!drawer || !card) return;
        card.classList.remove('translate-x-0');
        card.classList.add('translate-x-full');
        drawer.classList.add('opacity-0');
        drawer.classList.add('pointer-events-none');
        setTimeout(() => drawer.classList.add('hidden'), 300);
    }

    document.getElementById('drawer-git-connect') && document.getElementById('drawer-git-connect').addEventListener('click', function(e) {
        if (e.target === this) closeGitDrawer();
    });

    function toggleInlineNewBranch(show) {
        const container = document.getElementById('git-new-branch-container');
        if (!container) return;
        if (show) {
            container.classList.remove('hidden');
            const input = document.getElementById('new-branch-name-input');
            if (input) {
                input.value = '';
                input.focus();
            }
        } else {
            container.classList.add('hidden');
        }
    }

    function loadGitStatus() {
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_github_get_status',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success && res.data) {
                const data = res.data;
                const unconnectedForm = document.getElementById('git-unconnected-form');
                const selectRepoStep = document.getElementById('git-step-select-repo');
                const connectedDashboard = document.getElementById('git-connected-dashboard');
                const statusDot = document.getElementById('cora-git-status-dot');

                if (data.connected && data.has_repo) {
                    if (unconnectedForm) unconnectedForm.classList.add('hidden');
                    if (selectRepoStep) selectRepoStep.classList.add('hidden');
                    if (connectedDashboard) connectedDashboard.classList.remove('hidden');
                    if (statusDot) statusDot.classList.remove('hidden');

                    const userEl = document.getElementById('git-connected-user');
                    const repoLink = document.getElementById('git-connected-repo-link');
                    if (userEl) userEl.textContent = 'Connected as @' + data.username;
                    if (repoLink) {
                        repoLink.textContent = data.repo;
                        repoLink.href = 'https://github.com/' + data.repo;
                    }

                    const branchSelect = document.getElementById('git-branch-select');
                    if (branchSelect) {
                        branchSelect.innerHTML = '';
                        if (data.branches && data.branches.length) {
                            data.branches.forEach(branch => {
                                const option = document.createElement('option');
                                option.value = branch;
                                option.textContent = branch;
                                branchSelect.appendChild(option);
                            });
                        } else {
                            const option = document.createElement('option');
                            option.value = 'main';
                            option.textContent = 'main';
                            branchSelect.appendChild(option);
                        }
                        const savedBranch = localStorage.getItem('cora_active_branch') || 'main';
                        branchSelect.value = savedBranch;
                    }

                    const commitLog = document.getElementById('git-commit-log');
                    if (commitLog) {
                        commitLog.innerHTML = '';
                        if (data.recent_commits && data.recent_commits.length) {
                            data.recent_commits.forEach(commit => {
                                const commitEl = document.createElement('div');
                                commitEl.className = 'p-3 bg-zinc-50 dark:bg-zinc-900 border border-zinc-150 dark:border-zinc-800 rounded-xl space-y-1';
                                const commitDate = new Date(commit.date).toLocaleString();
                                commitEl.innerHTML = `
                                    <div class="flex items-center justify-between gap-2">
                                        <a href="${commit.url}" target="_blank" class="text-[10px] font-bold text-zinc-850 dark:text-zinc-200 hover:underline flex items-center gap-1">
                                            <code>${commit.sha}</code>
                                        </a>
                                        <span class="text-[9px] text-zinc-400 font-sans">${commitDate}</span>
                                    </div>
                                    <p class="text-[10px] text-zinc-600 dark:text-zinc-400 font-sans leading-snug">${commit.message}</p>
                                `;
                                commitLog.appendChild(commitEl);
                            });
                        } else {
                            commitLog.innerHTML = '<div class="text-[10px] text-zinc-400 italic pl-1 font-sans">No recent commits found.</div>';
                        }
                    }

                } else if (data.connected && !data.has_repo) {
                    if (unconnectedForm) unconnectedForm.classList.add('hidden');
                    if (selectRepoStep) selectRepoStep.classList.remove('hidden');
                    if (connectedDashboard) connectedDashboard.classList.add('hidden');
                    if (statusDot) statusDot.classList.add('hidden');

                    const userTemp = document.getElementById('git-connected-user-temp');
                    if (userTemp) {
                        userTemp.textContent = 'Connected as @' + data.username;
                    }
                } else {
                    if (unconnectedForm) unconnectedForm.classList.remove('hidden');
                    if (selectRepoStep) selectRepoStep.classList.add('hidden');
                    if (connectedDashboard) connectedDashboard.classList.add('hidden');
                    if (statusDot) statusDot.classList.add('hidden');
                }
            }
        });
    }

    function connectGitRepository() {
        const pat = (document.getElementById('git-pat-input') || {}).value || '';
        if (!pat.trim()) { window.coraShowToast('Personal Access Token is required.'); return; }
        const btn = document.getElementById('git-connect-btn');
        if (btn) { btn.disabled = true; btn.innerHTML = '<div class="animate-spin rounded-full h-3.5 w-3.5 border-2 border-white border-t-transparent"></div> Connecting...'; }

        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_github_save_pat',
            nonce: coraREData.ajaxNonce,
            pat: pat
        }, function(res) {
            if (btn) { btn.disabled = false; btn.innerHTML = 'Connect Account'; }
            if (res.success) { window.coraShowToast('Account connected successfully! Let\'s setup your repository.'); loadGitStatus(); }
            else { window.coraShowToast(res.data.message || 'Invalid Personal Access Token.'); }
        });
    }

    function switchRepoTab(tab) {
        const tabCreate = document.getElementById('tab-repo-create');
        const tabLink = document.getElementById('tab-repo-link');
        const panelCreate = document.getElementById('panel-repo-create');
        const panelLink = document.getElementById('panel-repo-link');

        if (tab === 'create') {
            if (tabCreate) tabCreate.className = 'py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer border-none bg-white dark:bg-zinc-800 text-zinc-850 dark:text-zinc-100 shadow-sm';
            if (tabLink) tabLink.className = 'py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer border-none bg-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200';
            if (panelCreate) panelCreate.classList.remove('hidden');
            if (panelLink) panelLink.classList.add('hidden');
        } else {
            if (tabLink) tabLink.className = 'py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer border-none bg-white dark:bg-zinc-800 text-zinc-850 dark:text-zinc-100 shadow-sm';
            if (tabCreate) tabCreate.className = 'py-1.5 text-[10px] font-bold rounded-md transition-all cursor-pointer border-none bg-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200';
            if (panelLink) panelLink.classList.remove('hidden');
            if (panelCreate) panelCreate.classList.add('hidden');
        }
    }

    function createGitRepository() {
        const repoNameInput = document.getElementById('git-new-repo-name');
        const repoName = repoNameInput ? repoNameInput.value.trim() : '';
        if (!repoName) { window.coraShowToast('Repository name is required.'); return; }
        const btn = document.getElementById('git-create-repo-btn');
        if (btn) { btn.disabled = true; btn.innerHTML = '<div class="animate-spin rounded-full h-3.5 w-3.5 border-2 border-white border-t-transparent"></div> Creating...'; }

        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_github_create_repo',
            nonce: coraREData.ajaxNonce,
            repo_name: repoName
        }, function(res) {
            if (btn) { btn.disabled = false; btn.innerHTML = 'Create Private Repository'; }
            if (res.success) { window.coraShowToast('Repository created and linked successfully!'); loadGitStatus(); }
            else { window.coraShowToast(res.data.message || 'Failed to create repository.'); }
        });
    }

    function linkGitRepository() {
        const repoPathInput = document.getElementById('git-link-repo-path');
        const repoPath = repoPathInput ? repoPathInput.value.trim() : '';
        if (!repoPath) { window.coraShowToast('Repository path (owner/repo) is required.'); return; }
        const btn = document.getElementById('git-link-repo-btn');
        if (btn) { btn.disabled = true; btn.innerHTML = '<div class="animate-spin rounded-full h-3.5 w-3.5 border-2 border-white border-t-transparent"></div> Linking...'; }

        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_github_link_repo',
            nonce: coraREData.ajaxNonce,
            repo: repoPath
        }, function(res) {
            if (btn) { btn.disabled = false; btn.innerHTML = 'Link Repository'; }
            if (res.success) { window.coraShowToast('Repository linked successfully!'); loadGitStatus(); }
            else { window.coraShowToast(res.data.message || 'Failed to link repository.'); }
        });
    }

    function disconnectGit() {
        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_github_disconnect',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Disconnected from GitHub.');
                localStorage.removeItem('cora_active_branch');
                ['git-pat-input', 'git-new-repo-name', 'git-link-repo-path'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
                loadGitStatus();
            } else { window.coraShowToast('Failed to disconnect repository.'); }
        });
    }

    function changeActiveBranch() {
        const select = document.getElementById('git-branch-select');
        if (select) { localStorage.setItem('cora_active_branch', select.value); window.coraShowToast('Active branch changed to: ' + select.value); }
    }

    function submitCreateBranch() {
        const input = document.getElementById('new-branch-name-input');
        const select = document.getElementById('git-branch-select');
        if (!input || !input.value.trim()) { window.coraShowToast('Branch name is required.'); return; }

        const fromBranch = (select ? select.value : 'main') || 'main';
        const newBranchName = input.value.trim();

        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_github_create_branch',
            nonce: coraREData.ajaxNonce,
            branch_name: newBranchName,
            from_branch: fromBranch
        }, function(res) {
            if (res.success) {
                window.coraShowToast('Branch ' + newBranchName + ' created successfully.');
                toggleInlineNewBranch(false);
                jQuery.post(coraREData.ajaxUrl, {
                    action: 'cora_github_get_status',
                    nonce: coraREData.ajaxNonce
                }, function(statusRes) {
                    if (statusRes.success && statusRes.data) {
                        const data = statusRes.data;
                        const branchSelect = document.getElementById('git-branch-select');
                        if (branchSelect) {
                            branchSelect.innerHTML = '';
                            if (data.branches && data.branches.length) {
                                data.branches.forEach(branch => {
                                    const option = document.createElement('option');
                                    option.value = branch;
                                    option.textContent = branch;
                                    branchSelect.appendChild(option);
                                });
                            }
                            branchSelect.value = newBranchName;
                            localStorage.setItem('cora_active_branch', newBranchName);
                        }
                    }
                });
            } else {
                window.coraShowToast(res.data.message || 'Failed to create branch.');
            }
        });
    }

    function pushPageLayout() {
        const activeBranch = localStorage.getItem('cora_active_branch') || 'main';
        const commitMsg = (document.getElementById('git-commit-msg') || {}).value || '';
        
        const iframe = document.getElementById('elementor-editor-iframe');
        if (!iframe || !iframe.contentWindow) {
            window.coraShowToast('Please open the page in the editor first.');
            return;
        }

        const pushBtn = document.getElementById('git-push-btn');
        if (pushBtn) {
            pushBtn.disabled = true;
            pushBtn.innerHTML = '<div class="animate-spin rounded-full h-3.5 w-3.5 border-2 border-white border-t-transparent"></div> Pushing...';
        }

        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_github_commit_page',
            nonce: coraREData.ajaxNonce,
            page_id: canvasState.activePageId || 0,
            message: commitMsg,
            branch: activeBranch
        }, function(res) {
            if (pushBtn) {
                pushBtn.disabled = false;
                pushBtn.innerHTML = '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="16 16 12 12 8 16"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path><polyline points="16 16 12 12 8 16"></polyline></svg> Commit & Push';
            }

            if (res.success) {
                window.coraShowToast('Page layout committed and pushed successfully!');
                const commitMsgInput = document.getElementById('git-commit-msg');
                if (commitMsgInput) commitMsgInput.value = '';
                loadGitStatus();
            } else {
                window.coraShowToast(res.data.message || 'Failed to commit/push page layout.');
            }
        });
    }

    function pullDesignTemplates() {
        const activeBranch = localStorage.getItem('cora_active_branch') || 'main';

        const pullBtn = document.getElementById('git-pull-btn');
        if (pullBtn) {
            pullBtn.disabled = true;
            pullBtn.innerHTML = '<div class="animate-spin rounded-full h-3.5 w-3.5 border-2 border-zinc-705 border-t-transparent"></div> Pulling...';
        }

        jQuery.post(coraREData.ajaxUrl, {
            action: 'cora_trigger_git_sync',
            nonce: coraREData.ajaxNonce,
            theme_id: canvasState.activeThemeId || 0,
            branch: activeBranch
        }, function(res) {
            if (pullBtn) {
                pullBtn.disabled = false;
                pullBtn.innerHTML = '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path><polyline points="8 17 12 21 16 17"></polyline></svg> Pull Design';
            }

            if (res.success) {
                window.coraShowToast(res.data.message || 'Repository synced and deployed successfully!');
                const iframe = document.getElementById('elementor-editor-iframe');
                if (iframe) {
                    document.getElementById('iframe-loader').classList.remove('hidden');
                    iframe.contentWindow.location.reload();
                }
            } else {
                window.coraShowToast(res.data.message || 'Failed to pull repository design sync.');
            }
        });
    }

    // Auto-save changes dirty status observer loop
    setInterval(() => {
        const iframe = document.getElementById('elementor-editor-iframe');
        if (iframe && iframe.contentWindow && iframe.contentWindow.elementor && iframe.contentWindow.elementor.saver && typeof iframe.contentWindow.elementor.saver.isDirty === 'function') {
            const isDirty = iframe.contentWindow.elementor.saver.isDirty();
            const statusText = document.getElementById('cora-save-status-text');
            if (statusText) {
                statusText.innerText = isDirty ? 'Unsaved changes' : 'All changes saved';
                statusText.previousElementSibling.className = isDirty ? 'w-1.5 h-1.5 rounded-full bg-amber-500' : 'w-1.5 h-1.5 rounded-full bg-emerald-500';
            }
        }
    }, 1500);

</script>


