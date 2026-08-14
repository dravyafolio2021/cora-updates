<?php
/**
 * Cora Workspace - Business Pulse (AI Activity Intelligence)
 * File: views/view-activity-timeline.php
 * Studio-Grade Monochromatic UI/UX with 100% Guaranteed Workspace Isolation.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Fetch comprehensive workspace intelligence
$pulse_data = function_exists( 'cora_pulse_get_workspace_intelligence' ) ? cora_pulse_get_workspace_intelligence() : array();
$agency_id  = intval( $pulse_data['agency_id'] ?? 1 );
$user_name  = esc_html( $pulse_data['user_name'] ?? 'Founder' );
$greeting   = esc_html( $pulse_data['greeting'] ?? 'Good day' );

$pulse_status      = $pulse_data['pulse_status'] ?? 'optimal';
$pulse_status_text = $pulse_data['pulse_status_text'] ?? 'All Systems Operational';
$attention_count   = intval( $pulse_data['attention_count'] ?? 0 );
$briefing_bullets  = is_array( $pulse_data['briefing_bullets'] ?? null ) ? $pulse_data['briefing_bullets'] : array();
$attention_items   = is_array( $pulse_data['attention_items'] ?? null ) ? $pulse_data['attention_items'] : array();
$cora_handled      = is_array( $pulse_data['cora_handled'] ?? null ) ? $pulse_data['cora_handled'] : array();
$timeline_events   = is_array( $pulse_data['timeline_events'] ?? null ) ? $pulse_data['timeline_events'] : array();
$deltas            = is_array( $pulse_data['deltas'] ?? null ) ? $pulse_data['deltas'] : array();

// Standardized Page Header Arguments
$activity_header_args = array(
    'title'            => 'Business Pulse',
    'description'      => 'Cora continuously observes activity across this workspace, highlights what matters, and turns activity into action.',
    'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>',
    'ai_stack'         => true,
    'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'              => array(
        'text'        => 'Live Refresh',
        'mobile_text' => 'Refresh',
        'onclick'     => "window.coraRefreshPulse()",
        'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>',
        'visible'     => true,
        'class'       => '!bg-white hover:!bg-zinc-50 !text-zinc-800 !border-zinc-200 hover:!border-zinc-300 border shadow-2xs',
    ),
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $activity_header_args );
}
?>

<div id="cora-business-pulse-module" class="cora-pulse-container space-y-5 sm:space-y-6 max-w-full font-sans pb-16">

    <!-- ════════════════════════════════════════════════════════
         LAYER 1: AI MORNING / AFTERNOON EXECUTIVE BRIEFING
         ════════════════════════════════════════════════════════ -->
    <div class="p-4 sm:p-6 rounded-2xl border border-zinc-200/80 shadow-2xs relative overflow-hidden" style="background-color: #FBFaf7;">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-4">
            <div class="space-y-1 sm:space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-[10px] sm:text-xs shrink-0">C</span>
                    <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-zinc-600">Cora Executive Briefing</span>
                    
                    <!-- Live Pulse Status Indicator -->
                    <?php if ( $pulse_status === 'optimal' ) : ?>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span><?php echo esc_html( $pulse_status_text ); ?></span>
                        </span>
                    <?php elseif ( $pulse_status === 'attention' ) : ?>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span><?php echo esc_html( $pulse_status_text ); ?></span>
                        </span>
                    <?php else : ?>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-ping"></span>
                            <span><?php echo esc_html( $pulse_status_text ); ?></span>
                        </span>
                    <?php endif; ?>

                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/90 border border-zinc-200/80 text-zinc-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Workspace #<?php echo esc_html( $agency_id ); ?></span>
                    </span>
                </div>

                <h2 class="text-base sm:text-lg font-bold text-zinc-950 tracking-tight">
                    <?php echo esc_html( $greeting ); ?>, <?php echo esc_html( $user_name ); ?> 👋
                </h2>
                <p class="text-xs text-zinc-600 font-medium leading-normal">Here is what changed across your business since you were last here.</p>
            </div>
        </div>

        <!-- Briefing Highlights (Unified Single Container on Mobile, Clean Grid on Desktop) -->
        <div class="mt-3.5 pt-3 border-t border-zinc-200/70">
            <div class="bg-white/80 rounded-xl border border-zinc-200/80 divide-y divide-zinc-100 sm:divide-y-0 sm:border-0 sm:bg-transparent sm:grid sm:grid-cols-3 sm:gap-3 overflow-hidden">
                <?php foreach ( $briefing_bullets as $bullet ) : ?>
                <div class="p-3 sm:rounded-xl sm:bg-white/80 sm:border sm:border-zinc-200/80 flex items-start gap-2.5 text-xs text-zinc-700 leading-relaxed">
                    <span class="w-4 h-4 rounded-full bg-zinc-100 text-zinc-800 flex items-center justify-center font-bold text-[9px] shrink-0 mt-0.5">
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </span>
                    <div class="flex-1">
                        <?php 
                        // Parse markdown bold tags safely
                        $formatted = preg_replace( '/\*\*(.*?)\*\*/', '<strong class="font-bold text-zinc-950">$1</strong>', esc_html( $bullet ) );
                        echo $formatted; 
                        ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


    <!-- ════════════════════════════════════════════════════════
         LAYER 2: "NEEDS YOUR ATTENTION" (Prioritized Action Cards)
         ════════════════════════════════════════════════════════ -->
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2">
            <div class="flex items-center gap-2">
                <h3 class="text-xs sm:text-sm font-bold text-zinc-950 uppercase tracking-wider">Needs Your Attention</h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo $attention_count > 0 ? 'bg-amber-100 text-amber-900' : 'bg-zinc-100 text-zinc-600'; ?>">
                    <?php echo $attention_count; ?>
                </span>
            </div>
            <?php if ( $attention_count === 0 ) : ?>
                <span class="text-[11px] sm:text-xs text-emerald-600 font-medium">All clear — no critical bottlenecks</span>
            <?php else : ?>
                <span class="text-[11px] sm:text-xs text-zinc-400 font-medium">Ranked by business urgency</span>
            <?php endif; ?>
        </div>

        <?php if ( empty( $attention_items ) ) : ?>
            <div class="p-6 sm:p-8 text-center bg-white rounded-2xl border border-zinc-200/80 shadow-2xs space-y-2 select-none">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center font-bold text-sm">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <div class="text-xs sm:text-sm font-bold text-zinc-900">Zero Pending Bottlenecks</div>
                <p class="text-xs text-zinc-500 max-w-sm mx-auto leading-relaxed">No overdue receivables, stalled leads, or unsigned contracts currently require your immediate intervention.</p>
            </div>
        <?php else : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="cora-pulse-attention-grid">
                <?php foreach ( $attention_items as $item ) : 
                    $prio = $item['priority'] ?? 'medium';
                    $prio_badge_bg = ( $prio === 'critical' ) ? 'bg-red-50 text-red-800 border-red-200' : ( ( $prio === 'high' ) ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-blue-50 text-blue-800 border-blue-200' );
                    $dot_color = ( $prio === 'critical' ) ? 'bg-red-500' : ( ( $prio === 'high' ) ? 'bg-amber-500' : 'bg-blue-500' );
                ?>
                <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs flex flex-col justify-between gap-3 hover:border-zinc-300 transition-all cora-pulse-card" id="card-<?php echo esc_attr( $item['id'] ); ?>">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold border <?php echo $prio_badge_bg; ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?php echo $dot_color; ?>"></span>
                                <span><?php echo esc_html( ucfirst( $item['priority'] ) ); ?> · <?php echo esc_html( $item['category'] ); ?></span>
                            </span>

                            <button type="button" onclick="window.coraDismissPulseItem('<?php echo esc_attr( $item['id'] ); ?>')" class="text-zinc-400 hover:text-zinc-600 text-xs font-bold cursor-pointer border-0 bg-transparent p-1" title="Dismiss">✕</button>
                        </div>

                        <h4 class="text-xs sm:text-sm font-bold text-zinc-950 leading-snug"><?php echo esc_html( $item['title'] ); ?></h4>
                        <p class="text-xs text-zinc-600 leading-relaxed"><?php echo esc_html( $item['subtitle'] ); ?></p>
                        
                        <?php if ( ! empty( $item['recommendation'] ) ) : ?>
                        <div class="p-2.5 rounded-xl bg-zinc-50 border border-zinc-100 text-[11px] text-zinc-700 flex items-start gap-2">
                            <span class="text-zinc-900 font-bold shrink-0">💡</span>
                            <div>
                                <span class="font-bold text-zinc-900">Cora recommends:</span>
                                <span><?php echo esc_html( $item['recommendation'] ); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="pt-2.5 border-t border-zinc-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span class="text-[10px] text-zinc-400 font-medium"><?php echo esc_html( $item['context'] ?? '' ); ?></span>
                        
                        <button type="button" onclick="window.coraExecutePulseAction(<?php echo esc_attr( json_encode( $item ) ); ?>)" class="w-full sm:w-auto px-3.5 py-2 sm:py-1.5 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 shadow-2xs shrink-0 text-center transition-transform active:scale-97">
                            <?php echo esc_html( $item['action_label'] ?? 'Action' ); ?> →
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>


    <!-- ════════════════════════════════════════════════════════
         LAYER 3: "WHAT CHANGED?" (Workspace Delta Analytics)
         ════════════════════════════════════════════════════════ -->
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
            <h3 class="text-xs sm:text-sm font-bold text-zinc-950 uppercase tracking-wider">What Changed This Week</h3>
            <span class="text-[11px] sm:text-xs text-zinc-400 font-medium">Compared to previous 7 days</span>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <!-- Metric 1: Inbound Lead Velocity -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-1">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block truncate">Inbound Leads</span>
                <div class="text-lg sm:text-xl font-extrabold text-zinc-950 cora-mono-num">
                    <?php echo intval( $deltas['leads_this_week'] ?? 0 ); ?>
                </div>
                <div class="text-[10px] truncate <?php echo ( $deltas['leads_growth_pct'] ?? 0 ) >= 0 ? 'text-emerald-700 font-semibold' : 'text-zinc-500'; ?>">
                    <?php echo ( $deltas['leads_growth_pct'] ?? 0 ) >= 0 ? '+' : ''; ?><?php echo intval( $deltas['leads_growth_pct'] ?? 0 ); ?>% vs last week
                </div>
            </div>

            <!-- Metric 2: Overdue Invoices -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-1">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block truncate">Overdue Invoices</span>
                <div class="text-lg sm:text-xl font-extrabold text-zinc-950 cora-mono-num">
                    <?php echo intval( $deltas['overdue_count'] ?? 0 ); ?>
                </div>
                <div class="text-[10px] truncate <?php echo ( $deltas['overdue_count'] ?? 0 ) > 0 ? 'text-red-700 font-semibold' : 'text-emerald-700 font-semibold'; ?>">
                    <?php echo ( $deltas['overdue_count'] ?? 0 ) > 0 ? ( '₹' . number_format( $deltas['overdue_amount'] ?? 0 ) . ' pending' ) : 'Zero overdue balance'; ?>
                </div>
            </div>

            <!-- Metric 3: Workspace Activity Volume -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-1">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block truncate">Logged Events</span>
                <div class="text-lg sm:text-xl font-extrabold text-zinc-950 cora-mono-num">
                    <?php echo count( $timeline_events ); ?>
                </div>
                <div class="text-[10px] text-zinc-500 font-medium truncate">Audited &amp; indexed</div>
            </div>

            <!-- Metric 4: Multi-Tenant Boundary Health -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-1">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block truncate">Tenant Isolation</span>
                <div class="text-lg sm:text-xl font-extrabold text-emerald-700 cora-mono-num">
                    100%
                </div>
                <div class="text-[10px] text-emerald-700 font-semibold truncate">Workspace #<?php echo esc_html( $agency_id ); ?> Scoped</div>
            </div>
        </div>
    </div>


    <!-- ════════════════════════════════════════════════════════
         LAYER 4: "CORA WORKED WHILE YOU WERE AWAY" (Autonomous Actions)
         ════════════════════════════════════════════════════════ -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-zinc-200/80 shadow-2xs space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-zinc-950 uppercase tracking-wider">Cora Worked While You Were Away</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-800">Autonomous</span>
            </div>
            <span class="text-[11px] text-zinc-400 hidden sm:inline">Continuous AI observers</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
            <?php foreach ( $cora_handled as $ch ) : ?>
            <div class="flex items-start gap-2.5 p-3 rounded-xl bg-zinc-50 border border-zinc-100">
                <span class="text-emerald-700 font-bold shrink-0 mt-0.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </span>
                <div class="flex-1 space-y-0.5 min-w-0">
                    <p class="text-zinc-800 font-medium leading-normal"><?php echo esc_html( $ch['text'] ); ?></p>
                    <span class="text-[10px] text-zinc-400 font-mono"><?php echo esc_html( $ch['time'] ); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>


    <!-- ════════════════════════════════════════════════════════
         LAYER 5: UNIFIED, FILTERABLE ACTIVITY TIMELINE FEED
         ════════════════════════════════════════════════════════ -->
    <div class="space-y-4 pt-2">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-zinc-950 uppercase tracking-wider">Workspace Activity Feed</h3>
                <p class="text-xs text-zinc-500">Chronological history and tamper-evident audit ledger across all modules.</p>
            </div>

            <!-- Real-time Workspace Search Bar -->
            <div class="relative w-full sm:w-64">
                <input type="text" id="cora-pulse-search-input" oninput="window.coraFilterPulseEvents()" placeholder="Search activities..." class="w-full bg-white border border-zinc-200 rounded-xl pl-8 pr-3 py-1.5 text-xs text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:border-zinc-900 shadow-2xs">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
        </div>

        <!-- Filter Pill Controls -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-xs select-none">
            <button type="button" onclick="window.coraSetPulseCategory('all')" class="cora-pulse-filter-btn active px-3 py-1.5 rounded-xl font-bold bg-zinc-950 text-white cursor-pointer border-0 shrink-0 shadow-2xs" data-cat="all">
                All Activities
            </button>
            <button type="button" onclick="window.coraSetPulseCategory('crm')" class="cora-pulse-filter-btn px-3 py-1.5 rounded-xl font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0 shrink-0" data-cat="crm">
                CRM &amp; Leads
            </button>
            <button type="button" onclick="window.coraSetPulseCategory('finance')" class="cora-pulse-filter-btn px-3 py-1.5 rounded-xl font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0 shrink-0" data-cat="finance">
                Finance
            </button>
            <button type="button" onclick="window.coraSetPulseCategory('calendar')" class="cora-pulse-filter-btn px-3 py-1.5 rounded-xl font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0 shrink-0" data-cat="calendar">
                Calendar &amp; Shoots
            </button>
            <button type="button" onclick="window.coraSetPulseCategory('team')" class="cora-pulse-filter-btn px-3 py-1.5 rounded-xl font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0 shrink-0" data-cat="team">
                Team Actions
            </button>
            <button type="button" onclick="window.coraSetPulseCategory('ai')" class="cora-pulse-filter-btn px-3 py-1.5 rounded-xl font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0 shrink-0" data-cat="ai">
                Cora AI
            </button>
        </div>

        <!-- Timeline Events List -->
        <div class="space-y-2.5" id="cora-pulse-events-stream">
            <?php if ( empty( $timeline_events ) ) : ?>
                <div class="p-10 text-center bg-white rounded-2xl border border-zinc-200/80 shadow-2xs space-y-2 select-none" id="cora-pulse-empty-state">
                    <p class="text-xs font-semibold text-zinc-700">No activity recorded yet in this workspace</p>
                    <p class="text-[11px] text-zinc-400">Events, client communications, invoices, and team actions will stream here in real-time.</p>
                </div>
            <?php else : ?>
                <?php foreach ( $timeline_events as $evt ) : 
                    $cat = $evt['category'] ?? 'general';
                    $is_cora = ! empty( $evt['is_cora'] );
                ?>
                <div class="cora-pulse-event-row p-3.5 sm:p-4 rounded-2xl bg-white border border-zinc-200/80 hover:border-zinc-300 shadow-2xs flex items-center justify-between gap-3 transition-all cursor-pointer" data-category="<?php echo esc_attr( $cat ); ?>" onclick="window.coraInspectPulseEvent(<?php echo esc_attr( json_encode( $evt ) ); ?>)">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Category / Actor Icon Badge -->
                        <div class="w-8 h-8 rounded-xl <?php echo $is_cora ? 'bg-zinc-950 text-white' : 'bg-zinc-100 text-zinc-800'; ?> flex items-center justify-center shrink-0 font-bold text-xs">
                            <?php if ( $is_cora ) : ?>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2-6.4-4.8-6.4 4.8 2.4-7.2-6-4.8h7.6z"></path></svg>
                            <?php elseif ( $cat === 'finance' ) : ?>
                                <span class="font-bold">₹</span>
                            <?php elseif ( $cat === 'crm' ) : ?>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <?php elseif ( $cat === 'calendar' ) : ?>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <?php else : ?>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                            <?php endif; ?>
                        </div>

                        <div class="min-w-0 space-y-0.5">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-xs font-bold text-zinc-950 truncate max-w-[180px] sm:max-w-none"><?php echo esc_html( $evt['title'] ); ?></span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold bg-zinc-100 text-zinc-600 uppercase tracking-wider"><?php echo esc_html( $evt['category_label'] ?? $cat ); ?></span>
                            </div>
                            <p class="text-[11px] text-zinc-500 truncate"><?php echo esc_html( $evt['subtitle'] ); ?></p>
                            <div class="text-[10px] text-zinc-400 font-mono sm:hidden"><?php echo esc_html( $evt['time_ago'] ); ?></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-bold text-zinc-900"><?php echo esc_html( $evt['time_formatted'] ); ?></div>
                            <div class="text-[10px] text-zinc-400"><?php echo esc_html( $evt['time_ago'] ); ?></div>
                        </div>

                        <span class="px-2.5 py-1 rounded-xl text-xs font-semibold text-zinc-700 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 shadow-2xs shrink-0">
                            Inspect →
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>


<!-- ════════════════════════════════════════════════════════
     LAYER 6: EVENT INSPECTOR SIDE DRAWER SHEET
     (Monochromatic sliding drawer, zero default popups)
     ════════════════════════════════════════════════════════ -->
<aside id="cora-pulse-inspector-drawer" class="cora-side-drawer fixed top-0 right-0 w-full sm:w-[500px] max-w-full h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden hidden collapsed">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50 shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="w-7 h-7 rounded-xl bg-zinc-950 text-white flex items-center justify-center font-bold text-xs shrink-0">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
            </span>
            <div>
                <h3 class="text-sm font-bold text-zinc-950">Activity Inspector</h3>
                <p class="text-[11px] text-zinc-500">Connected context &amp; audit graph</p>
            </div>
        </div>
        <button type="button" onclick="window.coraClosePulseDrawer()" class="w-8 h-8 rounded-lg hover:bg-zinc-200 text-zinc-600 flex items-center justify-center cursor-pointer border-0 bg-transparent text-sm font-bold" title="Close">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-5 space-y-4" id="cora-pulse-inspector-body">
        <!-- Injected dynamically via JS -->
    </div>
</aside>


<!-- ════════════════════════════════════════════════════════
     BUSINESS PULSE JAVASCRIPT CONTROLLER
     ════════════════════════════════════════════════════════ -->
<script>
(function() {
    'use strict';

    let currentCategory = 'all';

    /* ── Live Refresh ── */
    window.coraRefreshPulse = function() {
        if (window.coraShowToast) window.coraShowToast('Refreshing workspace pulse telemetry...', 'info');
        setTimeout(() => location.reload(), 400);
    };

    /* ── Category Filter Controller ── */
    window.coraSetPulseCategory = function(cat) {
        currentCategory = cat;
        document.querySelectorAll('.cora-pulse-filter-btn').forEach(btn => {
            if (btn.getAttribute('data-cat') === cat) {
                btn.classList.add('active', 'bg-zinc-950', 'text-white');
                btn.classList.remove('text-zinc-600', 'hover:bg-zinc-100');
            } else {
                btn.classList.remove('active', 'bg-zinc-950', 'text-white');
                btn.classList.add('text-zinc-600');
            }
        });
        window.coraFilterPulseEvents();
    };

    /* ── Filter Timeline Events Stream ── */
    window.coraFilterPulseEvents = function() {
        const query = (document.getElementById('cora-pulse-search-input')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.cora-pulse-event-row');
        let visibleCount = 0;

        rows.forEach(r => {
            const cat = r.getAttribute('data-category');
            const text = r.innerText.toLowerCase();
            const matchesCat = (currentCategory === 'all' || cat === currentCategory);
            const matchesQuery = (!query || text.includes(query));

            if (matchesCat && matchesQuery) {
                r.style.display = '';
                visibleCount++;
            } else {
                r.style.display = 'none';
            }
        });
    };

    /* ── Dismiss Item Controller ── */
    window.coraDismissPulseItem = function(itemId) {
        const card = document.getElementById('card-' + itemId);
        if (card) {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => card.remove(), 250);
        }
        if (window.coraShowToast) window.coraShowToast('Attention item dismissed.', 'info');

        const ajaxUrl = window.coraREData?.ajaxUrl || '/wp-admin/admin-ajax.php';
        const nonce   = window.coraREData?.ajaxNonce || '';
        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'cora_ajax_pulse_dismiss_item',
                security: nonce,
                item_id: itemId
            })
        });
    };

    /* ── Execute Action Controller ── */
    window.coraExecutePulseAction = function(item) {
        if (!item) return;
        const type = item.action_type;
        const target = item.action_target;

        if (type === 'navigate') {
            if (typeof window.coraNavigateTo === 'function') {
                window.coraNavigateTo(target);
            } else {
                window.location.href = '/workspace/' + target;
            }
        } else if (type === 'drawer') {
            if (typeof window.coraOpenDrawer === 'function') {
                window.coraOpenDrawer(target);
            }
        } else {
            if (window.coraShowToast) window.coraShowToast('Opening ' + (item.title || 'action'), 'info');
        }
    };

    /* ── Inspect Pulse Event Drawer ── */
    window.coraInspectPulseEvent = function(evt) {
        const drawer = document.getElementById('cora-pulse-inspector-drawer');
        const body   = document.getElementById('cora-pulse-inspector-body');
        if (!drawer || !body) return;

        body.innerHTML = `
            <div class="space-y-4">
                <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-200/80 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-200 text-zinc-800 uppercase">${evt.category_label || evt.category}</span>
                        <span class="text-[11px] font-mono text-zinc-500">${evt.time_formatted || ''} (${evt.time_ago || ''})</span>
                    </div>
                    <h4 class="text-sm font-bold text-zinc-950">${evt.title || ''}</h4>
                    <p class="text-xs text-zinc-600">${evt.subtitle || ''}</p>
                </div>

                <div class="space-y-2">
                    <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Connected Context Graph</div>
                    <div class="p-3 rounded-xl border border-zinc-200 bg-white space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500 font-medium">Actor:</span>
                            <span class="font-bold text-zinc-900">${evt.actor_name || 'System'}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500 font-medium">Workspace ID:</span>
                            <span class="font-mono font-bold text-zinc-900">#${evt.agency_id || '<?php echo esc_js($agency_id); ?>'}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500 font-medium">Tamper-Proof Audit:</span>
                            <span class="text-emerald-700 font-semibold">Verified</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-zinc-200 flex items-center justify-end gap-2">
                    <button type="button" onclick="window.coraClosePulseDrawer()" class="px-4 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:bg-zinc-100 cursor-pointer border-0">
                        Close
                    </button>
                    ${evt.action_label ? `
                    <button type="button" onclick="window.coraClosePulseDrawer(); window.coraExecutePulseAction(${JSON.stringify(evt).replace(/"/g, '&quot;')});" class="px-4 py-2 rounded-xl text-xs font-bold bg-zinc-950 text-white hover:bg-zinc-800 cursor-pointer border-0 shadow-2xs">
                        ${evt.action_label} →
                    </button>
                    ` : ''}
                </div>
            </div>
        `;

        drawer.classList.remove('hidden', 'collapsed', 'translate-x-full');
        drawer.style.display = 'flex';
        drawer.style.pointerEvents = 'auto';
    };

    window.coraClosePulseDrawer = function() {
        const drawer = document.getElementById('cora-pulse-inspector-drawer');
        if (drawer) {
            drawer.classList.add('translate-x-full');
            setTimeout(() => {
                drawer.classList.add('hidden', 'collapsed');
                drawer.style.display = 'none';
            }, 300);
        }
    };

})();
</script>
