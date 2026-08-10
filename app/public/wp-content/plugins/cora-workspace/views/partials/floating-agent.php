<?php
/**
 * Cora Floating AI Agent — Reusable Partial Component
 *
 * Include on any workspace page:
 *   $cora_agent_config = [
 *       'page_context' => 'content_suite',
 *       'ajax_action'  => 'cora_ajax_content_suite_agent',
 *       'placeholder'  => 'Ask anything...',
 *       'pill_text'    => 'Search...',
 *       'quick_actions' => [ ['id'=>'x','label'=>'X','icon'=>'edit'], ... ],
 *       'suggestions'   => [ ['text'=>'Do X','icon'=>'activity'], ... ],
 *   ];
 *   include CORA_WORKSPACE_PATH . 'views/partials/floating-agent.php';
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Prevent double-render on same page
if ( defined( 'CORA_FLOATING_AGENT_RENDERED' ) ) return;
define( 'CORA_FLOATING_AGENT_RENDERED', true );

/* ─── Icon SVG helper ─────────────────────────────────────────────── */
if ( ! function_exists( '_cora_fai' ) ) {
function _cora_fai( $name, $size = 12, $extra_class = '' ) {
    $cls = $extra_class ? ' class="' . esc_attr( $extra_class ) . '"' : '';
    $paths = array(
        'edit'      => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>',
        'file'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>',
        'search'    => '<circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>',
        'sliders'   => '<line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="2" y1="14" x2="6" y2="14"></line><line x1="10" y1="8" x2="14" y2="8"></line><line x1="18" y1="16" x2="22" y2="16"></line>',
        'activity'  => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>',
        'folder'    => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>',
        'file-plus' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line>',
        'user-plus' => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line>',
        'more'      => '<circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle>',
        'zap'       => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>',
        'bar-chart' => '<line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line>',
    );
    $inner = isset( $paths[ $name ] ) ? $paths[ $name ] : '';
    return '<svg viewBox="0 0 24 24" width="' . (int) $size . '" height="' . (int) $size . '" stroke="currentColor" stroke-width="1.8" fill="none"' . $cls . '>' . $inner . '</svg>';
}
}

/* ─── Config defaults ─────────────────────────────────────────────── */
$_agent_defaults = array(
    'page_context' => 'general',
    'ajax_action'  => 'cora_ajax_content_suite_agent',
    'placeholder'  => 'Ask anything or search articles, keywords, opportunities...',
    'pill_text'    => 'Search articles, keywords, opportunities...',
    'show_more'    => true,
    'quick_actions' => array(
        array( 'id' => 'new-article',     'label' => 'New Article',       'icon' => 'edit' ),
        array( 'id' => 'content-brief',   'label' => 'AI Content Brief',  'icon' => 'file' ),
        array( 'id' => 'keyword-research','label' => 'Keyword Research',  'icon' => 'search' ),
        array( 'id' => 'optimizer',       'label' => 'Optimizer',         'icon' => 'sliders' ),
    ),
);

$_cfg = isset( $cora_agent_config ) && is_array( $cora_agent_config )
    ? array_merge( $_agent_defaults, $cora_agent_config )
    : $_agent_defaults;

/* ─── Workspace Quota Data ────────────────────────────────────────── */
$_ws_ctx  = function_exists( 'cora_get_current_workspace_context' ) ? cora_get_current_workspace_context() : array( 'id' => 1, 'name' => 'Workspace', 'plan' => 'starter' );
$_ws_id   = isset( $_ws_ctx['id'] ) ? intval( $_ws_ctx['id'] ) : 1;
$_ws_plan = isset( $_ws_ctx['plan'] ) ? $_ws_ctx['plan'] : 'starter';

// AI usage stats (5h / daily)
$_ai_usage = function_exists( 'cora_workspace_get_ai_usage_stats' ) ? cora_workspace_get_ai_usage_stats() : array( 'five_hour_count' => 0, 'five_hour_limit' => 30, 'daily_count' => 0, 'daily_limit' => 100 );

// RAG token quota
$_rag_quota_fn = function_exists( 'cora_get_agency_quota' );
$_rag_used  = 0;
$_rag_total = $_rag_quota_fn ? cora_get_agency_quota( $_ws_id, 'rag_token_quota' ) : 100000;
if ( $_rag_quota_fn ) {
    global $wpdb;
    $_rag_table = $wpdb->prefix . 'cora_rag_embeddings';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$_rag_table}'" ) === $_rag_table ) {
        $_rag_used = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(token_count),0) FROM {$_rag_table} WHERE agency_id = %d", $_ws_id ) ) );
    }
}
$_rag_pct = $_rag_total > 0 ? round( ( $_rag_used / $_rag_total ) * 100 ) : 0;

// Storage quota
$_stor_total = $_rag_quota_fn ? cora_get_agency_quota( $_ws_id, 'storage_limit_mb' ) : 1024;
$_stor_used  = 0;
$upload_dir  = wp_upload_dir();
$_agency_dir = $upload_dir['basedir'] . '/cora-workspace/' . $_ws_id;
if ( is_dir( $_agency_dir ) ) {
    $_stor_used = intval( shell_exec( 'du -sm ' . escapeshellarg( $_agency_dir ) . ' 2>/dev/null | cut -f1' ) );
}
$_stor_pct = $_stor_total > 0 ? round( ( $_stor_used / $_stor_total ) * 100 ) : 0;

// Team seats
$_seats_total = $_rag_quota_fn ? intval( cora_get_agency_quota( $_ws_id, 'max_users_limit' ) ) : 5;
$_seats_used  = 0;
if ( $_rag_quota_fn ) {
    $_members_table = $wpdb->prefix . 'cora_workspace_members';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$_members_table}'" ) === $_members_table ) {
        $_seats_used = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$_members_table} WHERE agency_id = %d", $_ws_id ) ) );
    }
}
$_seats_pct = $_seats_total > 0 ? round( ( $_seats_used / $_seats_total ) * 100 ) : 0;

// Helper: format large numbers
if ( ! function_exists( '_cora_fmt_k' ) ) {
function _cora_fmt_k( $n ) {
    if ( $n >= 1000000 ) return round( $n / 1000000, 1 ) . 'M';
    if ( $n >= 1000 ) return round( $n / 1000, 1 ) . 'K';
    return number_format( $n );
}
}
?>

<!-- ═══════════════════════════════════════════════════════════════════
     FLOATING AI AGENT — BOTTOM CENTER DOCK
     ═══════════════════════════════════════════════════════════════════ -->
<div id="cora-floating-agent-container" class="cora-floating-agent-wrapper"
     data-ajax-action="<?php echo esc_attr( $_cfg['ajax_action'] ); ?>"
     data-page-context="<?php echo esc_attr( $_cfg['page_context'] ); ?>">

    <!-- COLLAPSED SEARCH PILL BAR -->
    <div id="cora-agent-pill" class="cora-agent-pill" onclick="coraExpandAgent()">
        <div class="cora-agent-pill-inner">
            <span class="cora-agent-search-icon">
                <?php echo _cora_fai( 'search', 16 ); ?>
            </span>
            <span class="cora-agent-placeholder"><?php echo esc_html( $_cfg['pill_text'] ); ?></span>
            <button class="cora-agent-ask-btn" type="button">Ask AI</button>
        </div>
    </div>

    <!-- EXPANDED FLOATING AGENT BOARD -->
    <div id="cora-agent-board" class="cora-agent-board hidden">

        <!-- MODE 1: DASHBOARD (QUICK ACTIONS & SUGGESTIONS) -->
        <div id="cora-agent-dashboard" class="cora-agent-view-mode">
            <div class="cora-agent-cols">
                <!-- LEFT COLUMN: QUICK ACTIONS & RECENT SEARCHES -->
                <div class="cora-agent-col-left">
                    <div class="cora-agent-section-title">Quick Actions</div>
                    <div class="cora-agent-action-buttons">
                        <?php foreach ( $_cfg['quick_actions'] as $act ) : ?>
                        <button onclick="coraAgentTriggerAction('<?php echo esc_attr( $act['id'] ); ?>')" type="button" class="cora-agent-action-btn">
                            <?php echo _cora_fai( $act['icon'] ); ?>
                            <span><?php echo esc_html( $act['label'] ); ?></span>
                        </button>
                        <?php endforeach; ?>
                        <?php if ( $_cfg['show_more'] ) : ?>
                        <button onclick="coraAgentTriggerAction('more')" type="button" class="cora-agent-action-btn-more">
                            <?php echo _cora_fai( 'more' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>

                    <div class="cora-agent-section-header">
                        <span class="cora-agent-section-title">Recent Searches</span>
                        <button onclick="coraClearAgentSearches()" type="button" class="cora-agent-clear-btn">Clear</button>
                    </div>
                    <div id="cora-agent-recent-chips" class="cora-agent-chips"></div>
                </div>

                <!-- RIGHT COLUMN: WORKSPACE USAGE / QUOTA -->
                <div class="cora-agent-col-right">
                    <div class="cora-agent-section-title">Usage &amp; Quota</div>
                    <div class="cora-agent-quota-panel">

                        <!-- AI Requests (Session / Daily) -->
                        <div class="cora-quota-row">
                            <div class="cora-quota-row-header">
                                <span class="cora-quota-icon"><?php echo _cora_fai( 'zap', 11 ); ?></span>
                                <span class="cora-quota-label">AI Requests</span>
                                <span class="cora-quota-value" id="cora-agent-ai-daily"><?php echo intval( $_ai_usage['daily_count'] ); ?> / <?php echo intval( $_ai_usage['daily_limit'] ); ?></span>
                            </div>
                            <div class="cora-quota-bar">
                                <div class="cora-quota-bar-fill" id="cora-agent-ai-bar" style="width: <?php echo $_ai_usage['daily_limit'] > 0 ? round( ( $_ai_usage['daily_count'] / $_ai_usage['daily_limit'] ) * 100 ) : 0; ?>%"></div>
                            </div>
                            <span class="cora-quota-sub">Session: <span id="cora-agent-session-credits">0</span> credits used</span>
                        </div>

                        <!-- RAG Tokens -->
                        <div class="cora-quota-row">
                            <div class="cora-quota-row-header">
                                <span class="cora-quota-icon"><?php echo _cora_fai( 'activity', 11 ); ?></span>
                                <span class="cora-quota-label">RAG Tokens</span>
                                <span class="cora-quota-value"><?php echo _cora_fmt_k( $_rag_used ); ?> / <?php echo _cora_fmt_k( $_rag_total ); ?></span>
                            </div>
                            <div class="cora-quota-bar">
                                <div class="cora-quota-bar-fill <?php echo $_rag_pct > 85 ? 'cora-bar-warn' : ''; ?>" style="width: <?php echo min( $_rag_pct, 100 ); ?>%"></div>
                            </div>
                        </div>

                        <!-- Storage -->
                        <div class="cora-quota-row">
                            <div class="cora-quota-row-header">
                                <span class="cora-quota-icon"><?php echo _cora_fai( 'folder', 11 ); ?></span>
                                <span class="cora-quota-label">Storage</span>
                                <span class="cora-quota-value"><?php echo $_stor_used; ?> MB / <?php echo $_stor_total >= 1024 ? round( $_stor_total / 1024, 1 ) . ' GB' : $_stor_total . ' MB'; ?></span>
                            </div>
                            <div class="cora-quota-bar">
                                <div class="cora-quota-bar-fill <?php echo $_stor_pct > 85 ? 'cora-bar-warn' : ''; ?>" style="width: <?php echo min( $_stor_pct, 100 ); ?>%"></div>
                            </div>
                        </div>

                        <!-- Team Seats -->
                        <div class="cora-quota-row">
                            <div class="cora-quota-row-header">
                                <span class="cora-quota-icon"><?php echo _cora_fai( 'user-plus', 11 ); ?></span>
                                <span class="cora-quota-label">Team Seats</span>
                                <span class="cora-quota-value"><?php echo $_seats_used; ?> / <?php echo $_seats_total; ?></span>
                            </div>
                            <div class="cora-quota-bar">
                                <div class="cora-quota-bar-fill" style="width: <?php echo min( $_seats_pct, 100 ); ?>%"></div>
                            </div>
                        </div>

                    </div>
                    <div class="cora-agent-plan-badge">
                        <span class="cora-plan-dot"></span>
                        <?php echo esc_html( ucfirst( $_ws_plan ) ); ?> Plan
                    </div>
                </div>
            </div>
        </div>

        <!-- MODE 2: CONVERSATION WITH AI AGENT -->
        <div id="cora-agent-conversation" class="cora-agent-view-mode hidden">
            <div class="cora-agent-chat-header">
                <div class="cora-agent-chat-info">
                    <span class="cora-agent-avatar">C</span>
                    <div>
                        <div class="cora-agent-chat-title">Cora Content Assistant</div>
                        <div class="cora-agent-status-row">
                            <span class="cora-agent-status-dot"></span>
                            <span class="cora-agent-status-text">Active Context</span>
                        </div>
                    </div>
                </div>
                <button onclick="coraResetAgentSession()" class="cora-agent-reset-btn" type="button">Reset Session</button>
            </div>
            <div id="cora-agent-chat-messages" class="cora-agent-messages"></div>
        </div>

        <!-- SHARED EXPANDED FOOTER INPUT BAR -->
        <div class="cora-agent-footer">
            <div class="cora-agent-input-container">
                <span class="cora-agent-input-icon">
                    <?php echo _cora_fai( 'search', 16 ); ?>
                </span>
                <input type="text" id="cora-agent-input-field"
                       placeholder="<?php echo esc_attr( $_cfg['placeholder'] ); ?>"
                       onkeydown="coraAgentHandleKeyDown(event)">
                <span class="cora-agent-k-indicator">&#8984; K</span>
                <button onclick="coraSubmitAgentQuery()" type="button" class="cora-agent-sparkle-btn" id="cora-agent-submit-btn" title="Ask AI">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L15 9L22 12L15 15L12 22L9 15L2 12L9 9Z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════════
     FLOATING AI AGENT — STYLES
     ═══════════════════════════════════════════════════════════════════ -->
<style id="cora-floating-agent-styles">
/* ── Gradient animation property ──────────────────────────────────── */
@property --cora-gradient-angle {
    syntax: "<angle>";
    initial-value: 0deg;
    inherits: false;
}

/* ── Wrapper ──────────────────────────────────────────────────────── */
.cora-floating-agent-wrapper {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 99999;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    width: 800px;
    max-width: calc(100vw - 48px);
}
@media (max-width: 1023px) {
    .cora-floating-agent-wrapper {
        display: none !important;
    }
}
.cora-floating-agent-wrapper .hidden {
    display: none !important;
}

/* ── Collapsed Pill ───────────────────────────────────────────────── */
.cora-agent-pill {
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 9999px;
    padding: 6px 6px 6px 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02);
    cursor: text;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
}
.cora-agent-pill:hover {
    border-color: #c084fc;
    box-shadow: 0 4px 16px rgba(168, 85, 247, 0.12), 0 2px 4px rgba(0, 0, 0, 0.02);
}
.dark .cora-agent-pill {
    background: #09090b;
    border-color: #27272a;
}
.dark .cora-agent-pill:hover {
    border-color: #a855f7;
    box-shadow: 0 4px 16px rgba(168, 85, 247, 0.18);
}
.cora-agent-pill-inner {
    display: flex;
    align-items: center;
    width: 100%;
    justify-content: space-between;
}
.cora-agent-search-icon {
    color: #71717a;
    display: flex;
    align-items: center;
    margin-right: 10px;
    flex-shrink: 0;
}
.cora-agent-placeholder {
    color: #a1a1aa;
    font-size: 13px;
    flex-grow: 1;
    user-select: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cora-agent-ask-btn {
    background: #09090b;
    color: #ffffff;
    border: none;
    border-radius: 9999px;
    padding: 6px 16px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s ease;
    flex-shrink: 0;
}
.cora-agent-ask-btn:hover {
    background: #27272a;
}
.dark .cora-agent-ask-btn {
    background: #fafafa;
    color: #09090b;
}
.dark .cora-agent-ask-btn:hover {
    background: #e4e4e7;
}

/* ── Expanded Board — Animated Purple Gradient Border ─────────────── */
.cora-agent-board {
    border: 2px solid transparent;
    border-radius: 16px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    width: 100%;
    background:
        linear-gradient(#ffffff, #ffffff) padding-box,
        conic-gradient(from var(--cora-gradient-angle), #a855f7, #7c3aed, #c084fc, #e9d5ff, #a855f7) border-box;
    box-shadow:
        0 10px 25px -5px rgba(0, 0, 0, 0.08),
        0 8px 10px -6px rgba(0, 0, 0, 0.04),
        0 0 20px -4px rgba(168, 85, 247, 0.15);
    animation: coraAgentFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1), coraGradientSpin 3s linear infinite;
}
.dark .cora-agent-board {
    background:
        linear-gradient(#09090b, #09090b) padding-box,
        conic-gradient(from var(--cora-gradient-angle), #a855f7, #581c87, #7c3aed, #3b0764, #a855f7) border-box;
    box-shadow:
        0 10px 25px -5px rgba(0, 0, 0, 0.3),
        0 0 24px -4px rgba(168, 85, 247, 0.25);
}

@keyframes coraGradientSpin {
    to { --cora-gradient-angle: 360deg; }
}
@keyframes coraAgentFadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Two-Column Layout ────────────────────────────────────────────── */
.cora-agent-cols {
    display: flex;
    gap: 16px;
    border-bottom: 1px solid #f4f4f5;
    padding-bottom: 14px;
    margin-bottom: 12px;
}
.dark .cora-agent-cols {
    border-color: #18181b;
}
.cora-agent-col-left {
    flex: 1.2;
    min-width: 0;
}
.cora-agent-col-right {
    flex: 0.8;
    border-left: 1px solid #f4f4f5;
    padding-left: 16px;
    min-width: 0;
}
.dark .cora-agent-col-right {
    border-color: #18181b;
}

/* ── Section Headers ──────────────────────────────────────────────── */
.cora-agent-section-title {
    font-size: 10px;
    font-weight: 700;
    color: #71717a;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
}
.cora-agent-section-header {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 8px;
    margin-top: 14px;
    margin-bottom: 8px;
}
.cora-agent-clear-btn {
    background: none;
    border: none;
    font-size: 10px;
    color: #a1a1aa;
    cursor: pointer;
    font-weight: 500;
    padding: 0;
}
.cora-agent-clear-btn:hover {
    color: #71717a;
    text-decoration: underline;
}

/* ── Quick Action Buttons ─────────────────────────────────────────── */
.cora-agent-action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.cora-agent-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 600;
    color: #27272a;
    cursor: pointer;
    transition: all 0.15s ease;
}
.cora-agent-action-btn:hover {
    border-color: #a1a1aa;
    background: #f4f4f5;
}
.dark .cora-agent-action-btn {
    background: #09090b;
    border-color: #27272a;
    color: #e4e4e7;
}
.dark .cora-agent-action-btn:hover {
    border-color: #52525b;
    background: #18181b;
}
.cora-agent-action-btn-more {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 8px;
    padding: 6px 8px;
    color: #71717a;
    cursor: pointer;
    transition: all 0.15s ease;
}
.cora-agent-action-btn-more:hover {
    border-color: #a1a1aa;
    background: #f4f4f5;
}
.dark .cora-agent-action-btn-more {
    background: #09090b;
    border-color: #27272a;
    color: #a1a1aa;
}
.dark .cora-agent-action-btn-more:hover {
    border-color: #52525b;
    background: #18181b;
}

/* ── Recent Search Chips ──────────────────────────────────────────── */
.cora-agent-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.cora-agent-chip {
    font-size: 11px;
    background: #f4f4f5;
    color: #52525b;
    padding: 4px 10px;
    border-radius: 9999px;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    border: 1px solid transparent;
}
.cora-agent-chip:hover {
    background: #e4e4e7;
    color: #18181b;
}
.dark .cora-agent-chip {
    background: #18181b;
    color: #a1a1aa;
}
.dark .cora-agent-chip:hover {
    background: #27272a;
    color: #fafafa;
}

/* ── Quota & Usage Panel ─────────────────────────────────────────── */
.cora-agent-quota-panel {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 4px;
}
.cora-quota-row {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.cora-quota-row-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
}
.cora-quota-icon {
    color: #71717a;
    display: inline-flex;
    align-items: center;
    margin-right: 6px;
    flex-shrink: 0;
}
.cora-quota-label {
    font-weight: 600;
    color: #27272a;
    flex-grow: 1;
}
.dark .cora-quota-label {
    color: #e4e4e7;
}
.cora-quota-value {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 700;
    color: #18181b;
}
.dark .cora-quota-value {
    color: #fafafa;
}
.cora-quota-bar {
    width: 100%;
    height: 6px;
    background: #e4e4e7;
    border-radius: 9999px;
    overflow: hidden;
}
.dark .cora-quota-bar {
    background: #27272a;
}
.cora-quota-bar-fill {
    height: 100%;
    background: #09090b;
    border-radius: 9999px;
    transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.dark .cora-quota-bar-fill {
    background: #fafafa;
}
.cora-quota-bar-fill.cora-bar-warn {
    background: #ef4444 !important;
}
.cora-quota-sub {
    font-size: 9px;
    color: #a1a1aa;
    font-weight: 500;
}
.cora-agent-plan-badge {
    margin-top: 16px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f4f4f5;
    border: 1px solid #e4e4e7;
    color: #52525b;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.dark .cora-agent-plan-badge {
    background: #18181b;
    border-color: #27272a;
    color: #a1a1aa;
}
.cora-plan-dot {
    width: 6px;
    height: 6px;
    background: #22c55e;
    border-radius: 50%;
}

/* ── Footer Input Bar ─────────────────────────────────────────────── */
.cora-agent-footer {
    display: flex;
    align-items: center;
    width: 100%;
}
.cora-agent-input-container {
    display: flex;
    align-items: center;
    width: 100%;
    background: #ffffff;
    border: 1px solid #09090b;
    border-radius: 9999px;
    padding: 4px 6px 4px 14px;
    box-shadow: 0 0 0 2px rgba(9, 9, 11, 0.06);
}
.dark .cora-agent-input-container {
    background: #09090b;
    border-color: #fafafa;
    box-shadow: 0 0 0 2px rgba(250, 250, 250, 0.08);
}
.cora-agent-input-icon {
    color: #71717a;
    display: flex;
    align-items: center;
    margin-right: 8px;
    flex-shrink: 0;
}
.cora-agent-input-container input {
    flex-grow: 1;
    background: transparent;
    border: none !important;
    outline: none !important;
    padding: 6px 0;
    font-size: 13px;
    color: #09090b;
    min-width: 0;
}
.dark .cora-agent-input-container input {
    color: #fafafa;
}
.cora-agent-input-container input::placeholder {
    color: #a1a1aa;
}
.cora-agent-k-indicator {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    background: #f4f4f5;
    border: 1px solid #e4e4e7;
    color: #71717a;
    padding: 2px 5px;
    border-radius: 4px;
    margin-right: 8px;
    flex-shrink: 0;
    user-select: none;
}
.dark .cora-agent-k-indicator {
    background: #18181b;
    border-color: #27272a;
    color: #a1a1aa;
}

/* ── Sparkle Submit Button (Purple AI accent) ─────────────────────── */
.cora-agent-sparkle-btn {
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    color: #ffffff;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
    padding: 0;
}
.cora-agent-sparkle-btn:hover {
    background: linear-gradient(135deg, #6d28d9, #9333ea);
    box-shadow: 0 0 10px rgba(168, 85, 247, 0.3);
}
.dark .cora-agent-sparkle-btn {
    background: linear-gradient(135deg, #a855f7, #c084fc);
    color: #09090b;
}
.dark .cora-agent-sparkle-btn:hover {
    background: linear-gradient(135deg, #9333ea, #a855f7);
    box-shadow: 0 0 10px rgba(168, 85, 247, 0.4);
}

/* ── Conversation Chat Header ─────────────────────────────────────── */
.cora-agent-chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f4f4f5;
    padding-bottom: 10px;
    margin-bottom: 12px;
}
.dark .cora-agent-chat-header {
    border-color: #18181b;
}
.cora-agent-chat-info {
    display: flex;
    align-items: center;
    gap: 8px;
}
.cora-agent-avatar {
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    color: #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 11px;
}
.cora-agent-chat-title {
    font-size: 13px;
    font-weight: 700;
    color: #09090b;
}
.dark .cora-agent-chat-title {
    color: #fafafa;
}
.cora-agent-status-row {
    display: flex;
    align-items: center;
    gap: 4px;
}
.cora-agent-status-dot {
    width: 6px;
    height: 6px;
    background: #22c55e;
    border-radius: 50%;
    animation: coraAgentBlink 1.5s infinite ease-in-out;
}
@keyframes coraAgentBlink {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}
.cora-agent-status-text {
    font-size: 9px;
    color: #71717a;
    font-weight: 500;
}
.cora-agent-reset-btn {
    background: none;
    border: 1px solid #e4e4e7;
    font-size: 11px;
    color: #71717a;
    cursor: pointer;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
}
.cora-agent-reset-btn:hover {
    background: #f4f4f5;
    color: #09090b;
}
.dark .cora-agent-reset-btn {
    border-color: #27272a;
}
.dark .cora-agent-reset-btn:hover {
    background: #18181b;
    color: #fafafa;
}

/* ── Chat Messages ────────────────────────────────────────────────── */
.cora-agent-messages {
    display: flex;
    flex-direction: column;
    gap: 12px;
    height: 260px;
    overflow-y: auto;
    padding-right: 4px;
    margin-bottom: 12px;
}
.cora-msg-bubble {
    display: flex;
    flex-direction: column;
    max-width: 90%;
    animation: coraAgentMessageSlide 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes coraAgentMessageSlide {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.cora-msg-bubble.user {
    align-self: flex-end;
}
.cora-msg-bubble.ai {
    align-self: flex-start;
}
.cora-msg-sender {
    font-size: 9px;
    font-weight: 700;
    color: #a1a1aa;
    margin-bottom: 3px;
    text-transform: uppercase;
}
.cora-msg-bubble.user .cora-msg-sender {
    align-self: flex-end;
}
.cora-msg-content {
    font-size: 12.5px;
    line-height: 1.5;
    padding: 10px 12px;
    border-radius: 12px;
}
.cora-msg-bubble.user .cora-msg-content {
    background: #09090b;
    color: #ffffff;
    border-bottom-right-radius: 2px;
}
.dark .cora-msg-bubble.user .cora-msg-content {
    background: #fafafa;
    color: #09090b;
}
.cora-msg-bubble.ai .cora-msg-content {
    background: #f4f4f5;
    color: #18181b;
    border: 1px solid #e4e4e7;
    border-bottom-left-radius: 2px;
}
.dark .cora-msg-bubble.ai .cora-msg-content {
    background: #18181b;
    color: #e4e4e7;
    border-color: #27272a;
}

/* ── Chat Action Buttons ──────────────────────────────────────────── */
.cora-chat-action-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    padding-top: 8px;
}
.dark .cora-chat-action-container {
    border-color: rgba(255, 255, 255, 0.05);
}
.cora-chat-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #09090b;
    color: #09090b;
    border-radius: 6px;
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}
.cora-chat-action-btn:hover {
    background: #09090b;
    color: #ffffff;
}
.dark .cora-chat-action-btn {
    background: #09090b;
    border-color: #fafafa;
    color: #fafafa;
}
.dark .cora-chat-action-btn:hover {
    background: #fafafa;
    color: #09090b;
}

/* ── Loading Checklist ────────────────────────────────────────────── */
.cora-agent-loading-checklist {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.cora-loading-checklist-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    color: #71717a;
}
.cora-loading-spinner-small {
    width: 10px;
    height: 10px;
    border: 1.5px solid #d4d4d8;
    border-top-color: #18181b;
    border-radius: 50%;
    animation: coraSpinnerSmallSpin 0.6s linear infinite;
}
.dark .cora-loading-spinner-small {
    border-top-color: #fafafa;
}
@keyframes coraSpinnerSmallSpin {
    to { transform: rotate(360deg); }
}
</style>


<!-- ═══════════════════════════════════════════════════════════════════
     FLOATING AI AGENT — ENGINE (JavaScript)
     ═══════════════════════════════════════════════════════════════════ -->
<script>
(function() {
    'use strict';

    // Double-init guard
    if (window.__coraFloatingAgentInit) return;
    window.__coraFloatingAgentInit = true;

    // Read config from container data attributes
    var _container = document.getElementById('cora-floating-agent-container');
    var AJAX_ACTION = _container ? _container.getAttribute('data-ajax-action') : 'cora_ajax_content_suite_agent';

    // ─── Action Handler Registry ────────────────────────────────────
    // Pages can register custom action handlers after including the partial:
    //   window.coraAgentRegisterActionHandler('my-action', function() { ... });
    window.__coraAgentActionHandlers = window.__coraAgentActionHandlers || {};

    window.coraAgentRegisterActionHandler = function(actionId, callback) {
        window.__coraAgentActionHandlers[actionId] = callback;
    };

    // ─── Recent Searches (localStorage) ─────────────────────────────
    function coraLoadRecentSearches() {
        try {
            var stored = localStorage.getItem('cora_agent_recent_searches');
            return stored ? JSON.parse(stored) : [
                "skincare content ideas",
                "wedding photography keywords",
                "ai visibility report",
                "low ranking pages"
            ];
        } catch(e) {
            return [];
        }
    }

    function coraSaveRecentSearches(searches) {
        try {
            localStorage.setItem('cora_agent_recent_searches', JSON.stringify(searches));
        } catch(e) {}
    }

    var initialDailyCount = <?php echo intval($_ai_usage['daily_count']); ?>;
    var dailyLimit = <?php echo intval($_ai_usage['daily_limit']); ?>;
    var sessionQueriesCount = 0;

    function coraUpdateCreditsDisplay() {
        var credits = parseInt(localStorage.getItem('cora_ai_credits_used') || '0', 10);
        var sessionCreditsEl = document.getElementById('cora-agent-session-credits');
        if (sessionCreditsEl) {
            sessionCreditsEl.innerText = credits.toLocaleString();
        }
        
        var dailyCount = initialDailyCount + sessionQueriesCount;
        var dailyCountEl = document.getElementById('cora-agent-ai-daily');
        var dailyBarEl = document.getElementById('cora-agent-ai-bar');
        if (dailyCountEl) {
            dailyCountEl.innerText = dailyCount + ' / ' + dailyLimit;
        }
        if (dailyBarEl && dailyLimit > 0) {
            var pct = Math.min(100, Math.round((dailyCount / dailyLimit) * 100));
            dailyBarEl.style.width = pct + '%';
        }
    }

    // Render recent search chips
    window.coraRenderRecentChips = function() {
        var container = document.getElementById('cora-agent-recent-chips');
        if (!container) return;
        var list = coraLoadRecentSearches();
        container.innerHTML = '';
        list.forEach(function(query) {
            var chip = document.createElement('div');
            chip.className = 'cora-agent-chip';
            chip.innerText = query;
            chip.onclick = function() {
                var input = document.getElementById('cora-agent-input-field');
                if (input) {
                    input.value = query;
                    window.coraSubmitAgentQuery();
                }
            };
            container.appendChild(chip);
        });
    };

    window.coraClearAgentSearches = function() {
        coraSaveRecentSearches([]);
        window.coraRenderRecentChips();
        if (window.coraShowToast) window.coraShowToast('Recent searches cleared', 'success');
    };

    // ─── Expand / Collapse ──────────────────────────────────────────
    window.coraExpandAgent = function() {
        var pill = document.getElementById('cora-agent-pill');
        var board = document.getElementById('cora-agent-board');
        if (pill && board) {
            pill.classList.add('hidden');
            board.classList.remove('hidden');
            var input = document.getElementById('cora-agent-input-field');
            if (input) {
                setTimeout(function() { input.focus(); }, 50);
            }
            window.coraRenderRecentChips();
        }
    };

    window.coraCollapseAgent = function() {
        var pill = document.getElementById('cora-agent-pill');
        var board = document.getElementById('cora-agent-board');
        if (pill && board) {
            board.classList.add('hidden');
            pill.classList.remove('hidden');
        }
    };

    // Click outside to collapse
    document.addEventListener('mousedown', function(e) {
        var container = document.getElementById('cora-floating-agent-container');
        if (container && !container.contains(e.target)) {
            var board = document.getElementById('cora-agent-board');
            if (board && !board.classList.contains('hidden')) {
                window.coraCollapseAgent();
            }
        }
    });

    // ─── Keyboard Shortcuts ─────────────────────────────────────────
    // Intercept Command Palette (⌘K / Ctrl+K) to open floating agent
    var originalOpenCommandPalette = window.coraOpenCommandPalette;
    Object.defineProperty(window, 'coraOpenCommandPalette', {
        get: function() {
            return function() {
                var pill = document.getElementById('cora-agent-pill');
                var board = document.getElementById('cora-agent-board');
                if (pill || board) {
                    window.coraExpandAgent();
                    return;
                }
                if (typeof originalOpenCommandPalette === 'function') {
                    originalOpenCommandPalette();
                }
            };
        },
        set: function(val) {
            originalOpenCommandPalette = val;
        },
        configurable: true
    });

    // Escape to collapse
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.coraCollapseAgent();
        }
    });

    // Enter to submit
    window.coraAgentHandleKeyDown = function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            window.coraSubmitAgentQuery();
        }
    };

    // ─── Quick Action Triggers ──────────────────────────────────────
    window.coraAgentTriggerAction = function(actionType) {
        // 1. Check custom registry first
        if (window.__coraAgentActionHandlers[actionType]) {
            window.__coraAgentActionHandlers[actionType]();
            return;
        }

        // 2. Built-in fallback handlers (safe: check typeof before calling)
        if (actionType === 'new-article') {
            if (typeof window.openCreateArticleDrawer === 'function') {
                window.openCreateArticleDrawer();
                window.coraCollapseAgent();
            }
        } else if (actionType === 'content-brief') {
            if (typeof window.switchContentTab === 'function') {
                window.switchContentTab('ct-opportunities');
                window.coraCollapseAgent();
            }
        } else if (actionType === 'keyword-research') {
            if (window.coraShowToast) window.coraShowToast('AI Keyword research focused. Use inputs below.', 'info');
            var input = document.getElementById('cora-agent-input-field');
            if (input) {
                input.value = "Find newborn photography keywords";
                input.focus();
            }
        } else if (actionType === 'optimizer') {
            if (typeof window.switchContentTab === 'function') {
                window.switchContentTab('ct-seo');
                window.coraCollapseAgent();
            }
        } else if (actionType === 'more') {
            if (window.coraShowToast) window.coraShowToast('Opening advanced RAG vector logs...', 'info');
            if (typeof window.switchContentTab === 'function') {
                window.switchContentTab('ct-brain');
                window.coraCollapseAgent();
            }
        }
    };

    // ─── Suggestion Click ───────────────────────────────────────────
    window.coraAgentSuggestion = function(text) {
        var input = document.getElementById('cora-agent-input-field');
        if (input) {
            input.value = text;
            window.coraSubmitAgentQuery();
        }
    };

    // ─── Reset Session ──────────────────────────────────────────────
    window.coraResetAgentSession = function() {
        var dashboard = document.getElementById('cora-agent-dashboard');
        var convo = document.getElementById('cora-agent-conversation');
        var msgs = document.getElementById('cora-agent-chat-messages');
        if (dashboard && convo && msgs) {
            msgs.innerHTML = '';
            convo.classList.add('hidden');
            dashboard.classList.remove('hidden');
            var input = document.getElementById('cora-agent-input-field');
            if (input) {
                input.value = '';
                input.placeholder = "Ask anything or search articles, keywords, opportunities...";
                input.focus();
            }
        }
    };

    // ─── Submit Query ───────────────────────────────────────────────
    window.coraSubmitAgentQuery = function() {
        var input = document.getElementById('cora-agent-input-field');
        if (!input) return;
        var query = input.value.trim();
        if (!query) return;

        input.value = '';
        input.placeholder = "Reply to Cora Assistant...";

        // Save to recent searches
        var searches = coraLoadRecentSearches();
        searches = searches.filter(function(s) { return s.toLowerCase() !== query.toLowerCase(); });
        searches.unshift(query);
        if (searches.length > 4) searches.pop();
        coraSaveRecentSearches(searches);
        window.coraRenderRecentChips();

        // Switch to conversation mode
        var dashboard = document.getElementById('cora-agent-dashboard');
        var convo = document.getElementById('cora-agent-conversation');
        if (dashboard && convo) {
            dashboard.classList.add('hidden');
            convo.classList.remove('hidden');
        }

        // Append user message
        coraAppendAgentMessage('user', query);

        // Append loading indicator
        var loadingId = 'cora-agent-loading-' + Date.now();
        coraAppendAgentLoading(loadingId);

        // AJAX request
        var $ = window.jQuery;
        var ajaxUrl = (typeof window.coraREWPData !== 'undefined' && window.coraREWPData.ajaxUrl)
            ? window.coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php';
        var ajaxNonce = (typeof window.coraREWPData !== 'undefined' && window.coraREWPData.ajaxNonce)
            ? window.coraREWPData.ajaxNonce : '';

        $.post(ajaxUrl, {
            action: AJAX_ACTION,
            nonce: ajaxNonce,
            prompt: query
        }, function(response) {
            var loadingEl = document.getElementById(loadingId);
            if (loadingEl) loadingEl.remove();

            if (response.success && response.data && response.data.reply) {
                // Increment credits
                var credits = parseInt(localStorage.getItem('cora_ai_credits_used') || '0', 10);
                credits += 10;
                localStorage.setItem('cora_ai_credits_used', credits.toString());

                sessionQueriesCount++;
                coraUpdateCreditsDisplay();

                var sidebarCreditsVal = document.getElementById('cora-sidebar-credits-val');
                if (sidebarCreditsVal) {
                    sidebarCreditsVal.innerText = credits.toLocaleString();
                }

                if (window.coraShowToast) {
                    window.coraShowToast('RAG ground query complete. Credits: -10.', 'info');
                }

                coraAppendAgentMessage('ai', response.data.reply);
            } else {
                var errMsg = (response.data && response.data.message) ? response.data.message : (response.data || 'Encountered connection trouble.');
                coraAppendAgentMessage('ai', '<span style="color:#ef4444; font-weight:600;">Error: ' + errMsg + '</span>');
            }
        }).fail(function() {
            var loadingEl = document.getElementById(loadingId);
            if (loadingEl) loadingEl.remove();
            coraAppendAgentMessage('ai', '<span style="color:#ef4444; font-weight:600;">Network error. Please verify your connection.</span>');
        });
    };

    // ─── Chat Bubble Renderer ───────────────────────────────────────
    function coraAppendAgentMessage(sender, text) {
        var msgs = document.getElementById('cora-agent-chat-messages');
        if (!msgs) return;

        var isUser = (sender === 'user');
        var bubble = document.createElement('div');
        bubble.className = 'cora-msg-bubble ' + sender;

        var label = document.createElement('span');
        label.className = 'cora-msg-sender';
        label.innerText = isUser ? 'You' : 'Cora Assistant';
        bubble.appendChild(label);

        var content = document.createElement('div');
        content.className = 'cora-msg-content';

        if (isUser) {
            content.innerText = text;
            bubble.appendChild(content);
            msgs.appendChild(bubble);
            msgs.scrollTop = msgs.scrollHeight;
        } else {
            // Parse AI response: extract [ACTION: TYPE|args] tags
            var replyText = text;
            var actions = [];

            var actionRegex = /\[ACTION:\s*([A-Z_]+)(?:\|([^\]]+))?\]/gi;
            var match;
            while ((match = actionRegex.exec(text)) !== null) {
                var actionType = match[1].toLowerCase();
                var rawArgs = match[2] || '';
                var args = {};

                var argRegex = /(\w+)\s*=\s*(?:"([^"]*)"|'([^']*)'|(\S+))/g;
                var argMatch;
                while ((argMatch = argRegex.exec(rawArgs)) !== null) {
                    var k = argMatch[1];
                    var v = argMatch[2] || argMatch[3] || argMatch[4];
                    args[k] = v;
                }
                actions.push({ type: actionType, args: args });
            }

            replyText = replyText.replace(actionRegex, '').trim();

            // Streaming typewriter effect
            bubble.appendChild(content);
            msgs.appendChild(bubble);

            var i = 0;
            content.innerHTML = '';

            function typeNextChar() {
                if (i < replyText.length) {
                    var char = replyText.charAt(i);
                    if (char === '\n') {
                        content.innerHTML += '<br>';
                    } else {
                        content.innerHTML += char;
                    }
                    i++;
                    msgs.scrollTop = msgs.scrollHeight;
                    setTimeout(typeNextChar, 10);
                } else {
                    // Typing done — render action buttons if any
                    if (actions.length > 0) {
                        var actionContainer = document.createElement('div');
                        actionContainer.className = 'cora-chat-action-container';

                        actions.forEach(function(act) {
                            if (act.type === 'create_draft') {
                                var title = act.args.title || 'Untitled Draft';
                                var btn = document.createElement('button');
                                btn.className = 'cora-chat-action-btn';
                                btn.innerHTML = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg> Create WordPress Draft: "' + title + '"';
                                btn.onclick = function() {
                                    window.coraAgentExecAction('create_draft', title);
                                };
                                actionContainer.appendChild(btn);
                            } else if (act.type === 'switch_tab') {
                                var tab = act.args.tab || 'ct-overview';
                                var btn2 = document.createElement('button');
                                btn2.className = 'cora-chat-action-btn';
                                btn2.innerText = 'Switch Tab: ' + tab.replace('ct-', '').toUpperCase();
                                btn2.onclick = function() {
                                    window.coraAgentExecAction('switch_tab', tab);
                                };
                                actionContainer.appendChild(btn2);
                            } else if (act.type === 'optimize_article') {
                                var postId = act.args.post_id || '0';
                                var btn3 = document.createElement('button');
                                btn3.className = 'cora-chat-action-btn';
                                btn3.innerText = 'Audit Article ID: ' + postId;
                                btn3.onclick = function() {
                                    window.coraAgentExecAction('optimize_article', postId);
                                };
                                actionContainer.appendChild(btn3);
                            } else if (act.type === 'show_toast') {
                                var toastText = act.args.text || '';
                                if (toastText && window.coraShowToast) {
                                    window.coraShowToast(toastText, 'success');
                                }
                            }
                        });
                        content.appendChild(actionContainer);
                        msgs.scrollTop = msgs.scrollHeight;
                    }
                }
            }

            typeNextChar();
        }
    }

    // ─── Execute Action from Chat Button ────────────────────────────
    window.coraAgentExecAction = function(actionType, arg) {
        if (actionType === 'create_draft') {
            var $ = window.jQuery;
            var ajaxUrl = (typeof window.coraREWPData !== 'undefined' && window.coraREWPData.ajaxUrl)
                ? window.coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php';
            var nonce = (typeof window.coraREWPData !== 'undefined' && window.coraREWPData.ajaxNonce)
                ? window.coraREWPData.ajaxNonce : '';

            if (window.coraShowToast) window.coraShowToast('Creating new WordPress draft: "' + arg + '"...', 'info');

            $.post(ajaxUrl, {
                action: 'cora_save_article',
                nonce: nonce,
                post_id: 0,
                title: arg,
                content: '<p>This article outline was generated and ingested into WordPress by the Cora Content Suite AI Agent.</p>',
                status: 'draft'
            }, function(response) {
                if (response && response.success) {
                    if (window.coraShowToast) window.coraShowToast('WordPress draft saved successfully!', 'success');
                    setTimeout(function() { window.location.reload(); }, 1000);
                } else {
                    if (window.coraShowToast) window.coraShowToast('Failed to create draft', 'error');
                }
            });
        } else if (actionType === 'switch_tab') {
            if (typeof window.switchContentTab === 'function') {
                window.switchContentTab(arg);
                window.coraCollapseAgent();
            }
        } else if (actionType === 'optimize_article') {
            if (typeof window.switchContentTab === 'function') {
                window.switchContentTab('ct-seo');
            }
            if (typeof window.openSEODetailDrawer === 'function') {
                window.openSEODetailDrawer(arg, 'SEO Audit & Ingestion');
            }
            window.coraCollapseAgent();
        }
    };

    // ─── Loading Progress Checklist ─────────────────────────────────
    function coraAppendAgentLoading(loadingId) {
        var msgs = document.getElementById('cora-agent-chat-messages');
        if (!msgs) return;

        var bubble = document.createElement('div');
        bubble.className = 'cora-msg-bubble ai';
        bubble.id = loadingId;

        var label = document.createElement('span');
        label.className = 'cora-msg-sender';
        label.innerText = 'Cora Assistant';
        bubble.appendChild(label);

        var content = document.createElement('div');
        content.className = 'cora-msg-content';

        var checklist = document.createElement('div');
        checklist.className = 'cora-agent-loading-checklist';

        var steps = [
            "Reading library records...",
            "Checking local Search Console opportunities...",
            "Syncing RAG Business Brain context..."
        ];

        steps.forEach(function(stepText, idx) {
            var item = document.createElement('div');
            item.className = 'cora-loading-checklist-item';
            item.id = loadingId + '-step-' + idx;
            item.innerHTML = '<span class="cora-loading-spinner-small"></span> <span>' + stepText + '</span>';
            checklist.appendChild(item);
        });

        content.appendChild(checklist);
        bubble.appendChild(content);
        msgs.appendChild(bubble);
        msgs.scrollTop = msgs.scrollHeight;

        setTimeout(function() {
            var step0 = document.getElementById(loadingId + '-step-0');
            if (step0) step0.innerHTML = '<span style="color:#22c55e;font-weight:700;">&#10003;</span> <span>Library records analyzed.</span>';
        }, 800);

        setTimeout(function() {
            var step1 = document.getElementById(loadingId + '-step-1');
            if (step1) step1.innerHTML = '<span style="color:#22c55e;font-weight:700;">&#10003;</span> <span>Opportunities backlog synced.</span>';
        }, 1600);

        setTimeout(function() {
            var step2 = document.getElementById(loadingId + '-step-2');
            if (step2) step2.innerHTML = '<span style="color:#22c55e;font-weight:700;">&#10003;</span> <span>Grounding system ready. Asking LLM...</span>';
        }, 2400);
    }

    // Initialize quota display on load
    coraUpdateCreditsDisplay();

})();
</script>
